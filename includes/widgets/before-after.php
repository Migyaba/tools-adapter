<?php
namespace ToolsAdapter\Widgets;

use Elementor\Controls_Manager;
use Elementor\Icons_Manager;
use Elementor\Utils;
use ToolsAdapter\Base_Widget;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Widget: Avant/Après — slider comparatif de deux images.
 *
 * L'image « Avant » est au-dessus et découpée (à gauche / en haut), l'image
 * « Après » est dessous en entier. La position est une variable CSS
 * (--ta-ba-pos) posée dès le rendu PHP puis pilotée par before-after.js.
 */
class Before_After extends Base_Widget {

	public function get_name() {
		return 'tools-adapter-before-after';
	}

	public function get_title() {
		return esc_html__( 'Avant / Après', 'tools-adapter' );
	}

	public function get_icon() {
		return 'eicon-slider-3d';
	}

	public function get_categories() {
		return [ 'tools-adapter' ];
	}

	public function get_keywords() {
		return [ 'avant', 'après', 'comparaison', 'before', 'after', 'slider' ];
	}

	public function get_style_depends() {
		return [ 'tools-adapter-before-after' ];
	}

	public function get_script_depends() {
		return [ 'tools-adapter-before-after' ];
	}

	protected function register_controls() {
		// ── Contenu : images ─────────────────────────────────────────────
		$this->start_controls_section( 'section_content', [ 'label' => esc_html__( 'Images', 'tools-adapter' ) ] );

		$this->add_control( 'before_image', [ 'label' => esc_html__( 'Image « Avant »', 'tools-adapter' ), 'type' => Controls_Manager::MEDIA, 'default' => [ 'url' => Utils::get_placeholder_image_src() ] ] );
		$this->add_control( 'before_label', [ 'label' => esc_html__( 'Libellé « Avant »', 'tools-adapter' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'Avant', 'tools-adapter' ) ] );
		$this->add_control( 'after_image', [ 'label' => esc_html__( 'Image « Après »', 'tools-adapter' ), 'type' => Controls_Manager::MEDIA, 'default' => [ 'url' => Utils::get_placeholder_image_src() ], 'separator' => 'before' ] );
		$this->add_control( 'after_label', [ 'label' => esc_html__( 'Libellé « Après »', 'tools-adapter' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'Après', 'tools-adapter' ) ] );

		$this->add_control(
			'image_size',
			[
				'label'     => esc_html__( 'Taille des fichiers image', 'tools-adapter' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'large',
				'separator' => 'before',
				'options'   => [
					'medium_large' => esc_html__( 'Moyenne-grande', 'tools-adapter' ),
					'large'        => esc_html__( 'Grande', 'tools-adapter' ),
					'full'         => esc_html__( 'Originale', 'tools-adapter' ),
				],
			]
		);

		$this->add_responsive_control(
			'image_fit',
			[
				'label'     => esc_html__( 'Ajustement des images', 'tools-adapter' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'cover',
				'options'   => [
					'cover'      => esc_html__( 'Couvrir tout le cadre (recommandé)', 'tools-adapter' ),
					'contain'    => esc_html__( 'Image entière (bandes possibles)', 'tools-adapter' ),
					'fill'       => esc_html__( 'Étirer', 'tools-adapter' ),
					'none'       => esc_html__( 'Taille d\'origine', 'tools-adapter' ),
					'scale-down' => esc_html__( 'Réduire si trop grande', 'tools-adapter' ),
				],
				'selectors' => [ '{{WRAPPER}} .ta-ba' => '--ta-ba-fit: {{VALUE}};' ],
			]
		);

		$this->add_responsive_control(
			'image_position',
			[
				'label'     => esc_html__( 'Position des images', 'tools-adapter' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'center center',
				'options'   => [
					'center center' => esc_html__( 'Centre', 'tools-adapter' ),
					'center top'    => esc_html__( 'Haut', 'tools-adapter' ),
					'center bottom' => esc_html__( 'Bas', 'tools-adapter' ),
					'left center'   => esc_html__( 'Gauche', 'tools-adapter' ),
					'right center'  => esc_html__( 'Droite', 'tools-adapter' ),
					'left top'      => esc_html__( 'Haut gauche', 'tools-adapter' ),
					'right top'     => esc_html__( 'Haut droite', 'tools-adapter' ),
					'left bottom'   => esc_html__( 'Bas gauche', 'tools-adapter' ),
					'right bottom'  => esc_html__( 'Bas droite', 'tools-adapter' ),
				],
				'selectors' => [ '{{WRAPPER}} .ta-ba' => '--ta-ba-pos: {{VALUE}};' ],
			]
		);

		$this->end_controls_section();

		// ── Contenu : comportement ───────────────────────────────────────
		$this->start_controls_section( 'section_behavior', [ 'label' => esc_html__( 'Comparateur', 'tools-adapter' ) ] );

		$this->add_control(
			'orientation',
			[
				'label'   => esc_html__( 'Orientation', 'tools-adapter' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'horizontal',
				'options' => [
					'horizontal' => esc_html__( 'Horizontale (avant à gauche)', 'tools-adapter' ),
					'vertical'   => esc_html__( 'Verticale (avant en haut)', 'tools-adapter' ),
				],
			]
		);

		$this->add_control(
			'interaction',
			[
				'label'       => esc_html__( 'Déplacement du curseur', 'tools-adapter' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'drag',
				'options'     => [
					'drag'  => esc_html__( 'Glisser ou cliquer', 'tools-adapter' ),
					'hover' => esc_html__( 'Suit la souris (ordinateur)', 'tools-adapter' ),
				],
				'description' => esc_html__( 'Sur écran tactile, le glisser reste toujours actif.', 'tools-adapter' ),
			]
		);

		$this->add_control(
			'initial_position',
			[
				'label'   => esc_html__( 'Position initiale (%)', 'tools-adapter' ),
				'type'    => Controls_Manager::SLIDER,
				'range'   => [ 'px' => [ 'min' => 0, 'max' => 100 ] ],
				'default' => [ 'size' => 50 ],
			]
		);

		$this->add_control(
			'ratio',
			[
				'label'     => esc_html__( 'Format', 'tools-adapter' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => '',
				'separator' => 'before',
				'options'   => [
					''     => esc_html__( 'Hauteur personnalisée', 'tools-adapter' ),
					'21/9' => '21:9',
					'16/9' => '16:9',
					'3/2'  => '3:2',
					'4/3'  => '4:3',
					'1/1'  => '1:1',
					'4/5'  => '4:5',
				],
			]
		);

		$this->add_responsive_control(
			'height',
			[
				'label'      => esc_html__( 'Hauteur', 'tools-adapter' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'vh' ],
				'range'      => [ 'px' => [ 'min' => 150, 'max' => 900 ] ],
				'default'    => [ 'size' => 420, 'unit' => 'px' ],
				'selectors'  => [ '{{WRAPPER}} .ta-ba' => 'height: {{SIZE}}{{UNIT}};' ],
				'condition'  => [ 'ratio' => '' ],
			]
		);

		$this->add_responsive_control(
			'max_width',
			[
				'label'      => esc_html__( 'Largeur max.', 'tools-adapter' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'      => [ 'px' => [ 'min' => 200, 'max' => 1600 ] ],
				'selectors'  => [ '{{WRAPPER}} .ta-ba-wrap' => 'max-width: {{SIZE}}{{UNIT}}; margin-left: auto; margin-right: auto;' ],
			]
		);

		$this->add_control( 'show_labels', [ 'label' => esc_html__( 'Afficher les libellés', 'tools-adapter' ), 'type' => Controls_Manager::SWITCHER, 'return_value' => 'yes', 'default' => 'yes', 'separator' => 'before' ] );
		$this->add_control(
			'labels_visibility',
			[
				'label'     => esc_html__( 'Visibilité des libellés', 'tools-adapter' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'always',
				'options'   => [
					'always' => esc_html__( 'Toujours', 'tools-adapter' ),
					'hover'  => esc_html__( 'Au survol uniquement', 'tools-adapter' ),
				],
				'condition' => [ 'show_labels' => 'yes' ],
			]
		);

		$this->add_control( 'handle_icon', [ 'label' => esc_html__( 'Icône du curseur', 'tools-adapter' ), 'type' => Controls_Manager::ICONS, 'separator' => 'before', 'description' => esc_html__( 'Laissez vide pour la double flèche par défaut.', 'tools-adapter' ) ] );

		$this->add_control( 'show_hint', [ 'label' => esc_html__( 'Légende sous l\'image', 'tools-adapter' ), 'type' => Controls_Manager::SWITCHER, 'return_value' => 'yes', 'default' => '', 'separator' => 'before' ] );
		$this->add_control( 'hint_text', [ 'label' => esc_html__( 'Texte', 'tools-adapter' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'Glissez pour comparer', 'tools-adapter' ), 'condition' => [ 'show_hint' => 'yes' ] ] );
		$this->add_control( 'hint_icon', [ 'label' => esc_html__( 'Icône', 'tools-adapter' ), 'type' => Controls_Manager::ICONS, 'default' => [ 'value' => 'fas fa-hand-pointer', 'library' => 'fa-solid' ], 'condition' => [ 'show_hint' => 'yes' ] ] );

		$this->end_controls_section();

		// ── Style : cadre ────────────────────────────────────────────────
		$this->start_controls_section( 'section_style', [ 'label' => esc_html__( 'Cadre', 'tools-adapter' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_responsive_control( 'ba_radius', [ 'label' => esc_html__( 'Arrondi', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'range' => [ 'px' => [ 'min' => 0, 'max' => 60 ] ], 'selectors' => [ '{{WRAPPER}} .ta-ba' => 'border-radius: {{SIZE}}{{UNIT}};' ] ] );
		$this->add_group_control( Group_Control_Border::get_type(), [ 'name' => 'ba_border', 'selector' => '{{WRAPPER}} .ta-ba' ] );
		$this->add_group_control( Group_Control_Box_Shadow::get_type(), [ 'name' => 'ba_shadow', 'selector' => '{{WRAPPER}} .ta-ba' ] );

		$this->end_controls_section();

		// ── Style : curseur ──────────────────────────────────────────────
		$this->start_controls_section( 'section_style_handle', [ 'label' => esc_html__( 'Curseur', 'tools-adapter' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_control( 'handle_color', [ 'label' => esc_html__( 'Couleur de la ligne et du bouton', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#ffffff', 'selectors' => [ '{{WRAPPER}} .ta-ba__line, {{WRAPPER}} .ta-ba__handle-btn' => 'background-color: {{VALUE}};' ] ] );
		$this->add_control( 'line_width', [ 'label' => esc_html__( 'Épaisseur de la ligne', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'range' => [ 'px' => [ 'min' => 0, 'max' => 12 ] ], 'selectors' => [ '{{WRAPPER}} .ta-ba' => '--ta-ba-line: {{SIZE}}{{UNIT}};' ] ] );
		$this->add_control( 'handle_size', [ 'label' => esc_html__( 'Taille du bouton', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'range' => [ 'px' => [ 'min' => 20, 'max' => 100 ] ], 'default' => [ 'size' => 56, 'unit' => 'px' ], 'selectors' => [ '{{WRAPPER}} .ta-ba__handle-btn' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};' ] ] );
		$this->add_control( 'handle_btn_bg', [ 'label' => esc_html__( 'Fond du bouton (si différent)', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .ta-ba__handle-btn' => 'background-color: {{VALUE}};' ] ] );
		$this->add_control( 'handle_icon_color', [ 'label' => esc_html__( 'Couleur de l\'icône', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#111827', 'selectors' => [ '{{WRAPPER}} .ta-ba__handle-btn' => 'color: {{VALUE}};', '{{WRAPPER}} .ta-ba__handle-btn svg' => 'fill: {{VALUE}};' ] ] );
		$this->add_control( 'handle_icon_size', [ 'label' => esc_html__( 'Taille de l\'icône', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'range' => [ 'px' => [ 'min' => 8, 'max' => 48 ] ], 'selectors' => [ '{{WRAPPER}} .ta-ba__handle-btn' => 'font-size: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .ta-ba__handle-btn svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};' ] ] );
		$this->add_group_control( Group_Control_Border::get_type(), [ 'name' => 'handle_border', 'selector' => '{{WRAPPER}} .ta-ba__handle-btn' ] );
		$this->add_group_control( Group_Control_Box_Shadow::get_type(), [ 'name' => 'handle_shadow', 'selector' => '{{WRAPPER}} .ta-ba__handle-btn' ] );

		$this->end_controls_section();

		// ── Style : libellés ─────────────────────────────────────────────
		$this->start_controls_section( 'section_style_labels', [ 'label' => esc_html__( 'Libellés', 'tools-adapter' ), 'tab' => Controls_Manager::TAB_STYLE, 'condition' => [ 'show_labels' => 'yes' ] ] );

		$this->add_group_control( Group_Control_Typography::get_type(), [ 'name' => 'label_typography', 'selector' => '{{WRAPPER}} .ta-ba__label' ] );
		$this->add_responsive_control( 'label_padding', [ 'label' => esc_html__( 'Espacement interne', 'tools-adapter' ), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => [ 'px', 'em' ], 'selectors' => [ '{{WRAPPER}} .ta-ba__label' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ] ] );
		$this->add_responsive_control( 'label_radius', [ 'label' => esc_html__( 'Arrondi', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'size_units' => [ 'px', '%' ], 'range' => [ 'px' => [ 'min' => 0, 'max' => 60 ] ], 'selectors' => [ '{{WRAPPER}} .ta-ba__label' => 'border-radius: {{SIZE}}{{UNIT}};' ] ] );
		$this->add_responsive_control( 'label_offset', [ 'label' => esc_html__( 'Distance aux bords', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'range' => [ 'px' => [ 'min' => 0, 'max' => 80 ] ], 'selectors' => [ '{{WRAPPER}} .ta-ba' => '--ta-ba-label-offset: {{SIZE}}{{UNIT}};' ] ] );
		$this->add_control(
			'label_vposition',
			[
				'label'        => esc_html__( 'Position (mode horizontal)', 'tools-adapter' ),
				'type'         => Controls_Manager::CHOOSE,
				'default'      => 'top',
				'options'      => [
					'top'    => [ 'title' => esc_html__( 'Haut', 'tools-adapter' ), 'icon' => 'eicon-v-align-top' ],
					'middle' => [ 'title' => esc_html__( 'Milieu', 'tools-adapter' ), 'icon' => 'eicon-v-align-middle' ],
					'bottom' => [ 'title' => esc_html__( 'Bas', 'tools-adapter' ), 'icon' => 'eicon-v-align-bottom' ],
				],
				'prefix_class' => 'ta-ba--labels-',
			]
		);

		$this->add_control( 'before_label_heading', [ 'label' => esc_html__( 'Libellé « Avant »', 'tools-adapter' ), 'type' => Controls_Manager::HEADING, 'separator' => 'before' ] );
		$this->add_control( 'label_color', [ 'label' => esc_html__( 'Texte', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#ffffff', 'selectors' => [ '{{WRAPPER}} .ta-ba__label--before' => 'color: {{VALUE}};' ] ] );
		$this->add_control( 'label_bg', [ 'label' => esc_html__( 'Fond', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => 'rgba(6,24,44,0.75)', 'selectors' => [ '{{WRAPPER}} .ta-ba__label--before' => 'background-color: {{VALUE}};' ] ] );

		$this->add_control( 'after_label_heading', [ 'label' => esc_html__( 'Libellé « Après »', 'tools-adapter' ), 'type' => Controls_Manager::HEADING, 'separator' => 'before' ] );
		$this->add_control( 'after_label_color', [ 'label' => esc_html__( 'Texte', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#111827', 'selectors' => [ '{{WRAPPER}} .ta-ba__label--after' => 'color: {{VALUE}};' ] ] );
		$this->add_control( 'after_label_bg', [ 'label' => esc_html__( 'Fond', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#FFC107', 'selectors' => [ '{{WRAPPER}} .ta-ba__label--after' => 'background-color: {{VALUE}};' ] ] );

		$this->end_controls_section();

		// ── Style : légende ──────────────────────────────────────────────
		$this->start_controls_section( 'section_style_hint', [ 'label' => esc_html__( 'Légende', 'tools-adapter' ), 'tab' => Controls_Manager::TAB_STYLE, 'condition' => [ 'show_hint' => 'yes' ] ] );

		$this->add_control( 'hint_color', [ 'label' => esc_html__( 'Couleur', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#64748b', 'selectors' => [ '{{WRAPPER}} .ta-ba__hint' => 'color: {{VALUE}};', '{{WRAPPER}} .ta-ba__hint svg' => 'fill: {{VALUE}};' ] ] );
		$this->add_group_control( Group_Control_Typography::get_type(), [ 'name' => 'hint_typography', 'selector' => '{{WRAPPER}} .ta-ba__hint' ] );
		$this->add_responsive_control(
			'hint_align',
			[
				'label'     => esc_html__( 'Alignement', 'tools-adapter' ),
				'type'      => Controls_Manager::CHOOSE,
				'default'   => 'center',
				'options'   => [
					'flex-start' => [ 'title' => esc_html__( 'Gauche', 'tools-adapter' ), 'icon' => 'eicon-text-align-left' ],
					'center'     => [ 'title' => esc_html__( 'Centre', 'tools-adapter' ), 'icon' => 'eicon-text-align-center' ],
					'flex-end'   => [ 'title' => esc_html__( 'Droite', 'tools-adapter' ), 'icon' => 'eicon-text-align-right' ],
				],
				'selectors' => [ '{{WRAPPER}} .ta-ba__hint' => 'justify-content: {{VALUE}};' ],
			]
		);
		$this->add_responsive_control( 'hint_spacing', [ 'label' => esc_html__( 'Espace au-dessus', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'range' => [ 'px' => [ 'min' => 0, 'max' => 60 ] ], 'selectors' => [ '{{WRAPPER}} .ta-ba__hint' => 'margin-top: {{SIZE}}{{UNIT}};' ] ] );

		$this->end_controls_section();
	}

	/**
	 * Render one comparison image (attachment with srcset, or plain URL).
	 *
	 * @param array  $image Media control value.
	 * @param string $size  Image size.
	 * @param string $alt   Fallback alternative text.
	 */
	private function render_image( $image, $size, $alt ) {
		if ( ! empty( $image['id'] ) ) {
			$attrs = [ 'class' => 'ta-ba__img', 'loading' => 'lazy', 'draggable' => 'false' ];
			if ( '' === trim( (string) get_post_meta( (int) $image['id'], '_wp_attachment_image_alt', true ) ) ) {
				$attrs['alt'] = $alt;
			}
			echo wp_get_attachment_image( (int) $image['id'], $size, false, $attrs );
			return;
		}
		echo '<img class="ta-ba__img" src="' . esc_url( $image['url'] ?? '' ) . '" alt="' . esc_attr( $alt ) . '" loading="lazy" draggable="false">';
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$before   = $settings['before_image'] ?? [];
		$after    = $settings['after_image'] ?? [];
		if ( ( empty( $before['url'] ) && empty( $before['id'] ) ) || ( empty( $after['url'] ) && empty( $after['id'] ) ) ) {
			return;
		}

		$position    = isset( $settings['initial_position']['size'] ) && '' !== $settings['initial_position']['size'] ? (float) $settings['initial_position']['size'] : 50;
		$position    = max( 0, min( 100, $position ) );
		$vertical    = 'vertical' === ( $settings['orientation'] ?? '' );
		$size        = in_array( $settings['image_size'] ?? 'large', [ 'medium_large', 'large', 'full' ], true ) ? $settings['image_size'] : 'large';
		$ratio       = in_array( $settings['ratio'] ?? '', [ '21/9', '16/9', '3/2', '4/3', '1/1', '4/5' ], true ) ? $settings['ratio'] : '';
		$label_b     = \tools_adapter_translate( $settings['before_label'] ?? '' );
		$label_a     = \tools_adapter_translate( $settings['after_label'] ?? '' );
		$show_labels = 'yes' === ( $settings['show_labels'] ?? '' );

		$classes = [ 'ta-ba', $vertical ? 'ta-ba--vertical' : 'ta-ba--horizontal' ];
		if ( 'hover' === ( $settings['interaction'] ?? '' ) ) {
			$classes[] = 'ta-ba--follow';
		}
		if ( $show_labels && 'hover' === ( $settings['labels_visibility'] ?? '' ) ) {
			$classes[] = 'ta-ba--labels-on-hover';
		}

		$style = '--ta-ba-pos: ' . $position . '%;';
		if ( $ratio ) {
			$style .= ' aspect-ratio: ' . $ratio . '; height: auto;';
		}

		$aria_label = $label_b && $label_a
			/* translators: 1: "before" label, 2: "after" label. */
			? sprintf( __( 'Comparer %1$s et %2$s', 'tools-adapter' ), $label_b, $label_a )
			: __( 'Comparer avant et après', 'tools-adapter' );
		?>
		<div class="ta-ba-wrap">
			<div class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>" style="<?php echo esc_attr( $style ); ?>" data-ta-before-after data-position="<?php echo esc_attr( (string) $position ); ?>">
				<div class="ta-ba__image ta-ba__image--after">
					<?php $this->render_image( $after, $size, $label_a ); ?>
				</div>
				<div class="ta-ba__image ta-ba__image--before">
					<?php $this->render_image( $before, $size, $label_b ); ?>
				</div>

				<?php if ( $show_labels && $label_b ) : ?>
					<span class="ta-ba__label ta-ba__label--before" aria-hidden="true"><?php echo esc_html( $label_b ); ?></span>
				<?php endif; ?>
				<?php if ( $show_labels && $label_a ) : ?>
					<span class="ta-ba__label ta-ba__label--after" aria-hidden="true"><?php echo esc_html( $label_a ); ?></span>
				<?php endif; ?>

				<div class="ta-ba__handle">
					<span class="ta-ba__line" aria-hidden="true"></span>
					<span
						class="ta-ba__handle-btn"
						data-ba-handle
						role="slider"
						tabindex="0"
						aria-label="<?php echo esc_attr( $aria_label ); ?>"
						aria-orientation="<?php echo $vertical ? 'vertical' : 'horizontal'; ?>"
						aria-valuemin="0"
						aria-valuemax="100"
						aria-valuenow="<?php echo esc_attr( (string) round( $position ) ); ?>"
						aria-valuetext="<?php echo esc_attr( round( $position ) . ' %' ); ?>"
					>
						<?php if ( ! empty( $settings['handle_icon']['value'] ) ) : ?>
							<?php Icons_Manager::render_icon( $settings['handle_icon'], [ 'aria-hidden' => 'true' ] ); ?>
						<?php else : ?>
							<svg class="ta-ba__arrows" viewBox="0 0 24 24" width="20" height="20" aria-hidden="true" focusable="false"><path d="M8 7l-5 5 5 5M16 7l5 5-5 5M3 12h18" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg>
						<?php endif; ?>
					</span>
				</div>
			</div>

			<?php if ( 'yes' === ( $settings['show_hint'] ?? '' ) && ! empty( $settings['hint_text'] ) ) : ?>
				<p class="ta-ba__hint">
					<?php if ( ! empty( $settings['hint_icon']['value'] ) ) : ?>
						<?php Icons_Manager::render_icon( $settings['hint_icon'], [ 'aria-hidden' => 'true' ] ); ?>
					<?php endif; ?>
					<span><?php echo esc_html( \tools_adapter_translate( $settings['hint_text'] ) ); ?></span>
				</p>
			<?php endif; ?>
		</div>
		<?php
	}
}
