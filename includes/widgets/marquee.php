<?php
namespace ToolsAdapter\Widgets;

use Elementor\Controls_Manager;
use ToolsAdapter\Base_Widget;
use ToolsAdapter\Repeater;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Icons_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Widget: Ruban / Bandeau défilant continu (Marquee).
 * Affiche une ligne infinie de mots-clés, services, certifications ou slogans avec défilement fluide.
 */
class Marquee extends Base_Widget {

	public function get_name() {
		return 'tools-adapter-marquee';
	}

	public function get_title() {
		return esc_html__( 'Ruban Défilant (Marquee)', 'tools-adapter' );
	}

	public function get_icon() {
		return 'eicon-animation-text';
	}

	public function get_categories() {
		return [ 'tools-adapter' ];
	}

	public function get_keywords() {
		return [ 'marquee', 'ruban', 'bandeau', 'ticker', 'défilement', 'texte', 'mots-clés', 'services' ];
	}

	public function get_style_depends() {
		return [ 'tools-adapter-marquee' ];
	}

	protected function register_controls() {

		// ==========================================
		// SECTION CONTENU : ÉLÉMENTS DU RUBAN
		// ==========================================
		$this->start_controls_section(
			'section_content',
			[ 'label' => esc_html__( 'Éléments du ruban', 'tools-adapter' ) ]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'text',
			[
				'label'       => esc_html__( 'Texte', 'tools-adapter' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Prestation ou certification', 'tools-adapter' ),
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'icon',
			[
				'label' => esc_html__( 'Icône (optionnelle)', 'tools-adapter' ),
				'type'  => Controls_Manager::ICONS,
			]
		);

		$repeater->add_control(
			'link',
			[
				'label'       => esc_html__( 'Lien (optionnel)', 'tools-adapter' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => 'https://...',
			]
		);

		$repeater->add_control(
			'highlight',
			[
				'label'        => esc_html__( 'Mettre en valeur (Accent)', 'tools-adapter' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
			]
		);

		$this->add_control(
			'items',
			[
				'label'       => esc_html__( 'Mots-clés / Services', 'tools-adapter' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => [
					[ 'text' => esc_html__( 'Vidange de fosses septiques', 'tools-adapter' ) ],
					[ 'text' => esc_html__( 'Hydrocurage haute pression', 'tools-adapter' ) ],
					[ 'text' => esc_html__( 'Inspection caméra & diagnostic', 'tools-adapter' ) ],
					[ 'text' => esc_html__( 'Terrassement & Nivellement', 'tools-adapter' ) ],
					[ 'text' => esc_html__( 'Enrochement & VRD', 'tools-adapter' ) ],
					[ 'text' => esc_html__( 'Bois de chauffage prêt à l\'emploi', 'tools-adapter' ) ],
					[ 'text' => esc_html__( 'Intervention d\'urgence 6j/7', 'tools-adapter' ), 'highlight' => 'yes' ],
				],
				'title_field' => '{{{ text }}}',
			]
		);

		$this->end_controls_section();

		// ==========================================
		// SECTION CONFIGURATION : DÉFILEMENT
		// ==========================================
		$this->start_controls_section(
			'section_settings',
			[ 'label' => esc_html__( 'Paramètres de défilement', 'tools-adapter' ) ]
		);

		$this->add_control(
			'speed',
			[
				'label'       => esc_html__( 'Durée d\'un cycle (secondes)', 'tools-adapter' ),
				'type'        => Controls_Manager::NUMBER,
				'min'         => 5,
				'max'         => 120,
				'step'        => 1,
				'default'     => 28,
				'description' => esc_html__( 'Plus la valeur est basse, plus le défilement est rapide.', 'tools-adapter' ),
				'selectors'   => [
					'{{WRAPPER}} .ta-marquee__track' => 'animation-duration: {{VALUE}}s;',
				],
			]
		);

		$this->add_control(
			'direction',
			[
				'label'        => esc_html__( 'Direction', 'tools-adapter' ),
				'type'         => Controls_Manager::SELECT,
				'default'      => 'normal',
				'options'      => [
					'normal'  => esc_html__( 'Vers la gauche (Classique)', 'tools-adapter' ),
					'reverse' => esc_html__( 'Vers la droite', 'tools-adapter' ),
				],
				'selectors'    => [
					'{{WRAPPER}} .ta-marquee__track' => 'animation-direction: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'pause_on_hover',
			[
				'label'        => esc_html__( 'Pause au survol de la souris', 'tools-adapter' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'prefix_class' => 'ta-marquee--pause-',
			]
		);

		$this->add_control(
			'fade_edges',
			[
				'label'        => esc_html__( 'Dégradé de masquage sur les bords (Fade)', 'tools-adapter' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'prefix_class' => 'ta-marquee--fade-',
			]
		);

		$this->add_control(
			'separator_type',
			[
				'label'   => esc_html__( 'Séparateur entre éléments', 'tools-adapter' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'dot',
				'options' => [
					'none'   => esc_html__( 'Aucun (simple espace)', 'tools-adapter' ),
					'dot'    => esc_html__( 'Puce ronde (•)', 'tools-adapter' ),
					'dash'   => esc_html__( 'Tiret (—)', 'tools-adapter' ),
					'slash'  => esc_html__( 'Barre oblique (/)', 'tools-adapter' ),
					'star'   => esc_html__( 'Étoile (★)', 'tools-adapter' ),
					'diamond'=> esc_html__( 'Losange (◆)', 'tools-adapter' ),
				],
			]
		);

		$this->end_controls_section();

		// ==========================================
		// STYLE : DISPOSITION & TYPOGRAPHIE
		// ==========================================
		$this->start_controls_section(
			'section_style_general',
			[ 'label' => esc_html__( 'Style & Typographie', 'tools-adapter' ), 'tab' => Controls_Manager::TAB_STYLE ]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'item_typography',
				'selector' => '{{WRAPPER}} .ta-marquee__item',
			]
		);

		$this->add_control(
			'text_color',
			[
				'label'     => esc_html__( 'Couleur du texte', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#e2e8f0',
				'selectors' => [
					'{{WRAPPER}} .ta-marquee__item' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'hover_color',
			[
				'label'     => esc_html__( 'Couleur du texte au survol', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .ta-marquee__item:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'highlight_color',
			[
				'label'     => esc_html__( 'Couleur des éléments mis en valeur', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#DDA853',
				'selectors' => [
					'{{WRAPPER}} .ta-marquee__item--highlight' => 'color: {{VALUE}}; font-weight: 700;',
				],
			]
		);

		$this->add_control(
			'icon_color',
			[
				'label'     => esc_html__( 'Couleur des icônes', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#DDA853',
				'selectors' => [
					'{{WRAPPER}} .ta-marquee__icon' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'icon_size',
			[
				'label'      => esc_html__( 'Taille des icônes (px)', 'tools-adapter' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => [ 'px' => [ 'min' => 8, 'max' => 48 ] ],
				'default'    => [ 'size' => 16, 'unit' => 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .ta-marquee__icon' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ta-marquee__icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'separator_color',
			[
				'label'     => esc_html__( 'Couleur du séparateur', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(221, 168, 83, 0.45)',
				'selectors' => [
					'{{WRAPPER}} .ta-marquee__sep' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'item_gap',
			[
				'label'      => esc_html__( 'Espacement entre éléments (px)', 'tools-adapter' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => [ 'px' => [ 'min' => 10, 'max' => 80 ] ],
				'default'    => [ 'size' => 28, 'unit' => 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .ta-marquee__item' => 'margin-right: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'fade_color',
			[
				'label'       => esc_html__( 'Couleur des dégradés latéraux (Fade)', 'tools-adapter' ),
				'type'        => Controls_Manager::COLOR,
				'default'     => '#0B192C',
				'description' => esc_html__( 'Doit correspondre à la couleur de fond de votre section pour un fondu parfait.', 'tools-adapter' ),
				'selectors'   => [
					'{{WRAPPER}} .ta-marquee' => '--ta-marquee-fade: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'bg_color',
			[
				'label'     => esc_html__( 'Couleur de fond', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#0B192C',
				'selectors' => [
					'{{WRAPPER}} .ta-marquee' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'marquee_border',
				'selector' => '{{WRAPPER}} .ta-marquee',
			]
		);

		$this->add_responsive_control(
			'marquee_radius',
			[
				'label'      => esc_html__( 'Arrondi des angles (px)', 'tools-adapter' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => [ 'px' => [ 'min' => 0, 'max' => 60 ] ],
				'selectors'  => [
					'{{WRAPPER}} .ta-marquee' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'marquee_shadow',
				'selector' => '{{WRAPPER}} .ta-marquee',
			]
		);

		$this->add_responsive_control(
			'padding_block',
			[
				'label'      => esc_html__( 'Espacement vertical (padding)', 'tools-adapter' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => [ 'px' => [ 'min' => 4, 'max' => 60 ] ],
				'default'    => [ 'size' => 14, 'unit' => 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .ta-marquee' => 'padding-top: {{SIZE}}{{UNIT}}; padding-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$items    = ! empty( $settings['items'] ) && is_array( $settings['items'] ) ? $settings['items'] : [];

		if ( empty( $items ) ) {
			return;
		}

		$sep_char = '';
		switch ( $settings['separator_type'] ?? 'dot' ) {
			case 'dot':
				$sep_char = '•';
				break;
			case 'dash':
				$sep_char = '—';
				break;
			case 'slash':
				$sep_char = '/';
				break;
			case 'star':
				$sep_char = '★';
				break;
			case 'diamond':
				$sep_char = '◆';
				break;
			case 'none':
			default:
				$sep_char = '';
				break;
		}

		// Helper to render one cycle of items.
		$render_items_cycle = function() use ( $items, $sep_char ) {
			foreach ( $items as $item ) {
				$text      = isset( $item['text'] ) ? \tools_adapter_translate( $item['text'] ) : '';
				$highlight = ! empty( $item['highlight'] ) && 'yes' === $item['highlight'];
				$item_cls  = 'ta-marquee__item' . ( $highlight ? ' ta-marquee__item--highlight' : '' );
				$url       = ! empty( $item['link']['url'] ) ? $item['link']['url'] : '';
				$target    = ! empty( $item['link']['is_external'] ) ? ' target="_blank"' : '';
				$rel       = ! empty( $item['link']['nofollow'] ) ? ' rel="nofollow"' : '';

				if ( $url ) {
					echo '<a href="' . esc_url( $url ) . '" class="' . esc_attr( $item_cls ) . '"' . $target . $rel . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				} else {
					echo '<span class="' . esc_attr( $item_cls ) . '">';
				}

				if ( ! empty( $item['icon']['value'] ) ) {
					echo '<span class="ta-marquee__icon" aria-hidden="true">';
					Icons_Manager::render_icon( $item['icon'], [ 'aria-hidden' => 'true' ] );
					echo '</span> ';
				}

				echo esc_html( $text );

				if ( '' !== $sep_char ) {
					echo ' <span class="ta-marquee__sep" aria-hidden="true">' . esc_html( $sep_char ) . '</span>';
				}

				if ( $url ) {
					echo '</a>';
				} else {
					echo '</span>';
				}
			}
		};
		?>
		<div class="ta-marquee" role="marquee" aria-label="<?php esc_attr_e( 'Ruban d\'informations défilant', 'tools-adapter' ); ?>">
			<div class="ta-marquee__inner">
				<div class="ta-marquee__track" aria-hidden="false">
					<?php $render_items_cycle(); ?>
				</div>
				<!-- Duplicate track for seamless infinite looping -->
				<div class="ta-marquee__track" aria-hidden="true">
					<?php $render_items_cycle(); ?>
				</div>
			</div>
		</div>
		<?php
	}
}
