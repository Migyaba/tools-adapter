<?php
namespace ToolsAdapter\Widgets;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Icons_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Widget: Boîte d'icône — carte moderne avec badge d'icône flottant et fond décoratif décalé.
 */
class Icon_Box extends Widget_Base {

	public function get_name() {
		return 'tools-adapter-icon-box';
	}

	public function get_title() {
		return esc_html__( 'Boîte d\'icône', 'tools-adapter' );
	}

	public function get_icon() {
		return 'eicon-icon-box';
	}

	public function get_categories() {
		return [ 'tools-adapter' ];
	}

	public function get_keywords() {
		return [ 'icon box', 'boite icone', 'carte', 'service', 'feature', 'avantage', 'bureau' ];
	}

	public function get_style_depends() {
		return [ 'tools-adapter-icon-box' ];
	}

	protected function register_controls() {
		// ==========================================
		// SECTION CONTENU : BADGE & ICÔNE
		// ==========================================
		$this->start_controls_section(
			'section_badge',
			[ 'label' => esc_html__( 'Icône & Badge', 'tools-adapter' ) ]
		);

		$this->add_control(
			'icon',
			[
				'label'   => esc_html__( 'Icône', 'tools-adapter' ),
				'type'    => Controls_Manager::ICONS,
				'default' => [
					'value'   => 'fas fa-laptop-code',
					'library' => 'fa-solid',
				],
			]
		);

		$this->add_control(
			'badge_position',
			[
				'label'   => esc_html__( 'Position du badge', 'tools-adapter' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'top-right',
				'options' => [
					'top-right'    => esc_html__( 'En haut à droite (Flottant)', 'tools-adapter' ),
					'top-left'     => esc_html__( 'En haut à gauche (Flottant)', 'tools-adapter' ),
					'top-center'   => esc_html__( 'En haut au centre (Flottant)', 'tools-adapter' ),
					'bottom-right' => esc_html__( 'En bas à droite (Flottant)', 'tools-adapter' ),
					'bottom-left'  => esc_html__( 'En bas à gauche (Flottant)', 'tools-adapter' ),
					'inline'       => esc_html__( 'Dans la carte (Classique)', 'tools-adapter' ),
				],
			]
		);

		$this->end_controls_section();

		// ==========================================
		// SECTION CONTENU : TEXTES
		// ==========================================
		$this->start_controls_section(
			'section_content_text',
			[ 'label' => esc_html__( 'Contenu textuel', 'tools-adapter' ) ]
		);

		$this->add_control(
			'subtitle',
			[
				'label'       => esc_html__( 'Sur-titre (optionnel)', 'tools-adapter' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'ex: Service, 01, etc.', 'tools-adapter' ),
			]
		);

		$this->add_control(
			'title',
			[
				'label'       => esc_html__( 'Titre', 'tools-adapter' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Bureau d\'étude intégré', 'tools-adapter' ),
				'placeholder' => esc_html__( 'Entrez un titre...', 'tools-adapter' ),
			]
		);

		$this->add_control(
			'title_tag',
			[
				'label'   => esc_html__( 'Balise HTML du titre', 'tools-adapter' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'h3',
				'options' => [
					'h1'   => 'H1',
					'h2'   => 'H2',
					'h3'   => 'H3',
					'h4'   => 'H4',
					'h5'   => 'H5',
					'h6'   => 'H6',
					'div'  => 'div',
					'span' => 'span',
					'p'    => 'p',
				],
			]
		);

		$this->add_control(
			'description',
			[
				'label'   => esc_html__( 'Description', 'tools-adapter' ),
				'type'    => Controls_Manager::TEXTAREA,
				'rows'    => 4,
				'default' => esc_html__( 'Plans, perspectives 3D et simulations avant tout démarrage de chantier.', 'tools-adapter' ),
			]
		);

		$this->end_controls_section();

		// ==========================================
		// SECTION CONTENU : LIEN & ACTION
		// ==========================================
		$this->start_controls_section(
			'section_content_link',
			[ 'label' => esc_html__( 'Lien / Action', 'tools-adapter' ) ]
		);

		$this->add_control(
			'link',
			[
				'label'       => esc_html__( 'Lien', 'tools-adapter' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => 'https://exemple.com',
			]
		);

		$this->add_control(
			'link_type',
			[
				'label'     => esc_html__( 'Type d\'interaction', 'tools-adapter' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'none',
				'options'   => [
					'none'   => esc_html__( 'Aucun lien actif', 'tools-adapter' ),
					'box'    => esc_html__( 'Toute la boîte cliquable', 'tools-adapter' ),
					'button' => esc_html__( 'Lien / Bouton en bas', 'tools-adapter' ),
					'both'   => esc_html__( 'Toute la boîte + Bouton en bas', 'tools-adapter' ),
				],
				'condition' => [ 'link[url]!' => '' ],
			]
		);

		$this->add_control(
			'action_text',
			[
				'label'     => esc_html__( 'Texte du bouton', 'tools-adapter' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'En savoir plus', 'tools-adapter' ),
				'condition' => [
					'link[url]!' => '',
					'link_type'  => [ 'button', 'both' ],
				],
			]
		);

		$this->add_control(
			'action_icon',
			[
				'label'     => esc_html__( 'Icône du bouton', 'tools-adapter' ),
				'type'      => Controls_Manager::ICONS,
				'default'   => [
					'value'   => 'fas fa-arrow-right',
					'library' => 'fa-solid',
				],
				'condition' => [
					'link[url]!' => '',
					'link_type'  => [ 'button', 'both' ],
				],
			]
		);

		$this->end_controls_section();

		// ==========================================
		// SECTION STYLE : FOND DÉCORATIF DÉCALÉ
		// ==========================================
		$this->start_controls_section(
			'section_style_backdrop',
			[
				'label' => esc_html__( 'Fond décoratif décalé', 'tools-adapter' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'enable_backdrop',
			[
				'label'        => esc_html__( 'Activer le fond décalé', 'tools-adapter' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'backdrop_bg',
			[
				'label'     => esc_html__( 'Couleur du fond', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#eaf5db',
				'selectors' => [ '{{WRAPPER}} .ta-icon-box' => '--ta-ib-backdrop-bg: {{VALUE}};' ],
				'condition' => [ 'enable_backdrop' => 'yes' ],
			]
		);

		$this->add_control(
			'backdrop_radius',
			[
				'label'      => esc_html__( 'Arrondi du fond', 'tools-adapter' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'      => [ 'px' => [ 'min' => 0, 'max' => 60 ] ],
				'default'    => [ 'size' => 24, 'unit' => 'px' ],
				'selectors'  => [ '{{WRAPPER}} .ta-icon-box' => '--ta-ib-backdrop-radius: {{SIZE}}{{UNIT}};' ],
				'condition'  => [ 'enable_backdrop' => 'yes' ],
			]
		);

		$this->add_responsive_control(
			'backdrop_offset_x',
			[
				'label'      => esc_html__( 'Décalage horizontal (X)', 'tools-adapter' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 'px' => [ 'min' => 0, 'max' => 40 ] ],
				'default'    => [ 'size' => 12, 'unit' => 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .ta-icon-box' => '--ta-ib-wrap-pl: {{SIZE}}{{UNIT}}; --ta-ib-offset-x: {{SIZE}}{{UNIT}};',
				],
				'condition'  => [ 'enable_backdrop' => 'yes' ],
			]
		);

		$this->add_responsive_control(
			'backdrop_offset_y',
			[
				'label'      => esc_html__( 'Décalage vertical (Y)', 'tools-adapter' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 'px' => [ 'min' => 0, 'max' => 40 ] ],
				'default'    => [ 'size' => 14, 'unit' => 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .ta-icon-box' => '--ta-ib-wrap-pt: {{SIZE}}{{UNIT}}; --ta-ib-offset-y: {{SIZE}}{{UNIT}};',
				],
				'condition'  => [ 'enable_backdrop' => 'yes' ],
			]
		);

		$this->end_controls_section();

		// ==========================================
		// SECTION STYLE : CARTE PRINCIPALE
		// ==========================================
		$this->start_controls_section(
			'section_style_card',
			[
				'label' => esc_html__( 'Carte principale', 'tools-adapter' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'card_bg',
			[
				'label'     => esc_html__( 'Couleur de fond', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [ '{{WRAPPER}} .ta-icon-box' => '--ta-ib-card-bg: {{VALUE}};' ],
			]
		);

		$this->add_responsive_control(
			'card_padding',
			[
				'label'      => esc_html__( 'Marge interne (Padding)', 'tools-adapter' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'default'    => [
					'top'      => '32',
					'right'    => '28',
					'bottom'   => '28',
					'left'     => '28',
					'unit'     => 'px',
					'isLinked' => false,
				],
				'selectors'  => [
					'{{WRAPPER}} .ta-icon-box' => '--ta-ib-card-padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'card_radius',
			[
				'label'      => esc_html__( 'Arrondi des coins', 'tools-adapter' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'      => [ 'px' => [ 'min' => 0, 'max' => 60 ] ],
				'default'    => [ 'size' => 20, 'unit' => 'px' ],
				'selectors'  => [ '{{WRAPPER}} .ta-icon-box' => '--ta-ib-card-radius: {{SIZE}}{{UNIT}};' ],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'card_shadow',
				'label'    => esc_html__( 'Ombre de la carte', 'tools-adapter' ),
				'selector' => '{{WRAPPER}} .ta-icon-box__card',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'card_border',
				'label'    => esc_html__( 'Bordure', 'tools-adapter' ),
				'selector' => '{{WRAPPER}} .ta-icon-box__card',
			]
		);

		$this->add_control(
			'card_hover_animation',
			[
				'label'   => esc_html__( 'Animation au survol', 'tools-adapter' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'lift',
				'options' => [
					'lift' => esc_html__( 'Lévitation vers le haut', 'tools-adapter' ),
					'none' => esc_html__( 'Aucune', 'tools-adapter' ),
				],
			]
		);

		$this->end_controls_section();

		// ==========================================
		// SECTION STYLE : BADGE D'ICÔNE
		// ==========================================
		$this->start_controls_section(
			'section_style_badge',
			[
				'label' => esc_html__( 'Badge d\'icône', 'tools-adapter' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'badge_size',
			[
				'label'      => esc_html__( 'Taille du badge', 'tools-adapter' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 'px' => [ 'min' => 40, 'max' => 120 ] ],
				'default'    => [ 'size' => 64, 'unit' => 'px' ],
				'selectors'  => [ '{{WRAPPER}} .ta-icon-box' => '--ta-ib-badge-size: {{SIZE}}{{UNIT}};' ],
			]
		);

		$this->add_responsive_control(
			'icon_size',
			[
				'label'      => esc_html__( 'Taille de l\'icône', 'tools-adapter' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 'px' => [ 'min' => 16, 'max' => 60 ] ],
				'default'    => [ 'size' => 28, 'unit' => 'px' ],
				'selectors'  => [ '{{WRAPPER}} .ta-icon-box' => '--ta-ib-icon-size: {{SIZE}}{{UNIT}};' ],
			]
		);

		$this->add_control(
			'badge_bg',
			[
				'label'     => esc_html__( 'Couleur de fond du badge', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#7bc81d',
				'selectors' => [ '{{WRAPPER}} .ta-icon-box' => '--ta-ib-badge-bg: {{VALUE}};' ],
			]
		);

		$this->add_control(
			'badge_color',
			[
				'label'     => esc_html__( 'Couleur de l\'icône', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [ '{{WRAPPER}} .ta-icon-box' => '--ta-ib-badge-color: {{VALUE}};' ],
			]
		);

		$this->add_control(
			'badge_radius',
			[
				'label'      => esc_html__( 'Arrondi du badge', 'tools-adapter' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'      => [ 'px' => [ 'min' => 0, 'max' => 60 ] ],
				'default'    => [ 'size' => 16, 'unit' => 'px' ],
				'selectors'  => [ '{{WRAPPER}} .ta-icon-box' => '--ta-ib-badge-radius: {{SIZE}}{{UNIT}};' ],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'badge_shadow',
				'label'    => esc_html__( 'Ombre du badge', 'tools-adapter' ),
				'selector' => '{{WRAPPER}} .ta-icon-box__badge',
			]
		);

		$this->add_control(
			'badge_hover_effect',
			[
				'label'   => esc_html__( 'Animation au survol du badge', 'tools-adapter' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'zoom',
				'options' => [
					'zoom' => esc_html__( 'Agrandissement (Zoom)', 'tools-adapter' ),
					'tilt' => esc_html__( 'Légère inclinaison', 'tools-adapter' ),
					'none' => esc_html__( 'Aucune', 'tools-adapter' ),
				],
			]
		);

		$this->end_controls_section();

		// ==========================================
		// SECTION STYLE : TYPOGRAPHIE & COULEURS
		// ==========================================
		$this->start_controls_section(
			'section_style_typography',
			[
				'label' => esc_html__( 'Typographie & Couleurs', 'tools-adapter' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		// Titre
		$this->add_control(
			'heading_title',
			[
				'label'     => esc_html__( 'Titre', 'tools-adapter' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'title_color',
			[
				'label'     => esc_html__( 'Couleur', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#0b2545',
				'selectors' => [ '{{WRAPPER}} .ta-icon-box' => '--ta-ib-title-color: {{VALUE}};' ],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .ta-icon-box__title',
			]
		);

		// Description
		$this->add_control(
			'heading_desc',
			[
				'label'     => esc_html__( 'Description', 'tools-adapter' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'desc_color',
			[
				'label'     => esc_html__( 'Couleur', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#556987',
				'selectors' => [ '{{WRAPPER}} .ta-icon-box' => '--ta-ib-desc-color: {{VALUE}};' ],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'desc_typography',
				'selector' => '{{WRAPPER}} .ta-icon-box__description',
			]
		);

		// Sur-titre
		$this->add_control(
			'heading_subtitle',
			[
				'label'     => esc_html__( 'Sur-titre', 'tools-adapter' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'subtitle_color',
			[
				'label'     => esc_html__( 'Couleur', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#7bc81d',
				'selectors' => [ '{{WRAPPER}} .ta-icon-box' => '--ta-ib-subtitle-color: {{VALUE}};' ],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'subtitle_typography',
				'selector' => '{{WRAPPER}} .ta-icon-box__subtitle',
			]
		);

		// Bouton / Action
		$this->add_control(
			'heading_action',
			[
				'label'     => esc_html__( 'Bouton / Action', 'tools-adapter' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'action_color',
			[
				'label'     => esc_html__( 'Couleur', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#0b2545',
				'selectors' => [ '{{WRAPPER}} .ta-icon-box' => '--ta-ib-action-color: {{VALUE}};' ],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'action_typography',
				'selector' => '{{WRAPPER}} .ta-icon-box__action',
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		$has_backdrop = ( $settings['enable_backdrop'] ?? 'yes' ) === 'yes';
		$badge_pos    = $settings['badge_position'] ?? 'top-right';
		$hover_lift   = ( $settings['card_hover_animation'] ?? 'lift' ) === 'lift';
		$badge_hover  = $settings['badge_hover_effect'] ?? 'zoom';

		$wrapper_classes = [
			'ta-icon-box',
			'ta-icon-box--badge-' . sanitize_html_class( $badge_pos ),
		];

		if ( $has_backdrop ) {
			$wrapper_classes[] = 'ta-icon-box--has-backdrop';
		}
		if ( $hover_lift ) {
			$wrapper_classes[] = 'ta-icon-box--hover-lift';
		}
		if ( 'none' !== $badge_hover ) {
			$wrapper_classes[] = 'ta-icon-box--hover-' . sanitize_html_class( $badge_hover );
		}

		$url       = ! empty( $settings['link']['url'] ) ? $settings['link']['url'] : '';
		$target    = ! empty( $settings['link']['is_external'] ) ? ' target="_blank"' : '';
		$nofollow  = ! empty( $settings['link']['nofollow'] ) ? ' rel="nofollow"' : '';
		$link_type = $settings['link_type'] ?? 'none';

		$is_box_clickable    = $url && in_array( $link_type, [ 'box', 'both' ], true );
		$is_button_displayed = $url && in_array( $link_type, [ 'button', 'both' ], true );

		$title_tag = in_array( $settings['title_tag'], [ 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'div', 'span', 'p' ], true ) ? $settings['title_tag'] : 'h3';
		?>
		<div class="<?php echo esc_attr( implode( ' ', $wrapper_classes ) ); ?>">
			<div class="ta-icon-box__card">
				<?php if ( $is_box_clickable ) : ?>
					<a class="ta-icon-box__overlay-link" href="<?php echo esc_url( $url ); ?>"<?php echo $target . $nofollow; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
						<?php echo esc_html( $settings['title'] ?? '' ); ?>
					</a>
				<?php endif; ?>

				<?php if ( ! empty( $settings['icon']['value'] ) ) : ?>
					<div class="ta-icon-box__badge" aria-hidden="true">
						<?php Icons_Manager::render_icon( $settings['icon'], [ 'aria-hidden' => 'true' ] ); ?>
					</div>
				<?php endif; ?>

				<div class="ta-icon-box__content">
					<?php if ( ! empty( $settings['subtitle'] ) ) : ?>
						<span class="ta-icon-box__subtitle">
							<?php echo esc_html( \tools_adapter_translate( $settings['subtitle'] ) ); ?>
						</span>
					<?php endif; ?>

					<?php if ( ! empty( $settings['title'] ) ) : ?>
						<<?php echo esc_html( $title_tag ); ?> class="ta-icon-box__title">
							<?php if ( $url && 'box' !== $link_type && ! $is_button_displayed ) : ?>
								<a href="<?php echo esc_url( $url ); ?>"<?php echo $target . $nofollow; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
									<?php echo esc_html( \tools_adapter_translate( $settings['title'] ) ); ?>
								</a>
							<?php else : ?>
								<?php echo esc_html( \tools_adapter_translate( $settings['title'] ) ); ?>
							<?php endif; ?>
						</<?php echo esc_html( $title_tag ); ?>>
					<?php endif; ?>

					<?php if ( ! empty( $settings['description'] ) ) : ?>
						<div class="ta-icon-box__description">
							<?php echo wp_kses_post( wpautop( \tools_adapter_translate( $settings['description'] ) ) ); ?>
						</div>
					<?php endif; ?>

					<?php if ( $is_button_displayed && ! empty( $settings['action_text'] ) ) : ?>
						<a class="ta-icon-box__action" href="<?php echo esc_url( $url ); ?>"<?php echo $target . $nofollow; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
							<span><?php echo esc_html( \tools_adapter_translate( $settings['action_text'] ) ); ?></span>
							<?php if ( ! empty( $settings['action_icon']['value'] ) ) : ?>
								<span class="ta-icon-box__action-icon">
									<?php Icons_Manager::render_icon( $settings['action_icon'], [ 'aria-hidden' => 'true' ] ); ?>
								</span>
							<?php endif; ?>
						</a>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<?php
	}
}
