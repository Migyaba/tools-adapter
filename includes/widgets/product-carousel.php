<?php
namespace ToolsAdapter\Widgets;

use Elementor\Controls_Manager;
use ToolsAdapter\Base_Widget;
use ToolsAdapter\Product_Card;
use ToolsAdapter\Products_Query;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Widget: Product Carousel
 */
class Product_Carousel extends Base_Widget {

	use Products_Widget_Controls;

	public function get_name() {
		return 'tools-adapter-product-carousel';
	}

	public function get_title() {
		return esc_html__( 'Carrousel Produits', 'tools-adapter' );
	}

	public function get_icon() {
		return 'eicon-slides';
	}

	public function get_categories() {
		return [ 'tools-adapter', 'woocommerce-elements' ];
	}

	public function get_keywords() {
		return [ 'woocommerce', 'products', 'carousel', 'slider', 'shop' ];
	}

	public function get_style_depends() {
		return [ 'tools-adapter-products', 'tools-adapter-product-carousel', 'tools-adapter-modal', 'tools-adapter-quick-view' ];
	}

	public function get_script_depends() {
		return [ 'tools-adapter-product-carousel', 'tools-adapter-modal', 'tools-adapter-quick-view' ];
	}

	protected function register_controls() {
		$this->register_products_content_controls();

		$this->start_controls_section(
			'section_carousel',
			[
				'label' => esc_html__( 'Carrousel', 'tools-adapter' ),
			]
		);

		$this->add_responsive_control(
			'slides_to_show',
			[
				'label'          => esc_html__( 'Slides visibles', 'tools-adapter' ),
				'type'           => Controls_Manager::NUMBER,
				'min'            => 1,
				'max'            => 6,
				'default'        => 4,
				'tablet_default' => 2,
				'mobile_default' => 1,
			]
		);

		$this->add_responsive_control(
			'slides_gap',
			[
				'label'      => esc_html__( 'Espacement', 'tools-adapter' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 60,
					],
				],
				'default'    => [
					'size' => 20,
				],
			]
		);

		$this->add_control(
			'autoplay',
			[
				'label'        => esc_html__( 'Lecture auto', 'tools-adapter' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'autoplay_speed',
			[
				'label'     => esc_html__( 'Vitesse autoplay (ms)', 'tools-adapter' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 4000,
				'min'       => 1000,
				'step'      => 100,
				'condition' => [
					'autoplay' => 'yes',
				],
			]
		);

		$this->add_control(
			'loop',
			[
				'label'        => esc_html__( 'Boucle', 'tools-adapter' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'show_arrows',
			[
				'label'        => esc_html__( 'Flèches', 'tools-adapter' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'show_dots',
			[
				'label'        => esc_html__( 'Points', 'tools-adapter' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'pause_on_hover',
			[
				'label'        => esc_html__( 'Pause au survol', 'tools-adapter' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => [
					'autoplay' => 'yes',
				],
			]
		);

		$this->end_controls_section();

		$this->register_products_style_controls();

		$this->start_controls_section(
			'section_style_nav',
			[
				'label' => esc_html__( 'Navigation', 'tools-adapter' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'arrow_color',
			[
				'label'     => esc_html__( 'Couleur flèches', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#222222',
				'selectors' => [
					'{{WRAPPER}} .ta-carousel__arrow' => 'color: {{VALUE}};',
				],
				'condition' => [
					'show_arrows' => 'yes',
				],
			]
		);

		$this->add_control(
			'arrow_bg',
			[
				'label'     => esc_html__( 'Fond flèches', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .ta-carousel__arrow' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'show_arrows' => 'yes',
				],
			]
		);

		$this->add_control(
			'dot_color',
			[
				'label'     => esc_html__( 'Couleur points', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#cccccc',
				'selectors' => [
					'{{WRAPPER}} .ta-carousel__dot' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'show_dots' => 'yes',
				],
			]
		);

		$this->add_control(
			'dot_active_color',
			[
				'label'     => esc_html__( 'Point actif', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#C9A84C',
				'selectors' => [
					'{{WRAPPER}} .ta-carousel__dot.is-active' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'show_dots' => 'yes',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Resolve responsive number setting.
	 *
	 * @param array  $settings Settings.
	 * @param string $key      Control key.
	 * @param mixed  $default  Default.
	 * @return array{desktop: mixed, tablet: mixed, mobile: mixed}
	 */
	private function get_responsive_value( array $settings, $key, $default ) {
		return [
			'desktop' => $settings[ $key ] ?? $default,
			'tablet'  => $settings[ $key . '_tablet' ] ?? ( $settings[ $key ] ?? $default ),
			'mobile'  => $settings[ $key . '_mobile' ] ?? ( $settings[ $key . '_tablet' ] ?? ( $settings[ $key ] ?? $default ) ),
		];
	}

	protected function render() {
		if ( ! class_exists( 'WooCommerce' ) ) {
			if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
				echo '<p>' . esc_html__( 'WooCommerce est requis pour ce widget.', 'tools-adapter' ) . '</p>';
			}
			return;
		}

		$settings = $this->get_card_settings( $this->get_settings_for_display() );
		$query    = Products_Query::query( $settings );

		if ( ! $query->have_posts() ) {
			echo '<p class="ta-products-empty">' . esc_html__( 'Aucun produit trouvé.', 'tools-adapter' ) . '</p>';
			return;
		}

		$slides = $this->get_responsive_value( $settings, 'slides_to_show', 4 );
		$gap    = [
			'desktop' => isset( $settings['slides_gap']['size'] ) ? $settings['slides_gap']['size'] : 20,
			'tablet'  => isset( $settings['slides_gap_tablet']['size'] ) ? $settings['slides_gap_tablet']['size'] : ( isset( $settings['slides_gap']['size'] ) ? $settings['slides_gap']['size'] : 20 ),
			'mobile'  => isset( $settings['slides_gap_mobile']['size'] ) ? $settings['slides_gap_mobile']['size'] : ( isset( $settings['slides_gap_tablet']['size'] ) ? $settings['slides_gap_tablet']['size'] : ( isset( $settings['slides_gap']['size'] ) ? $settings['slides_gap']['size'] : 16 ) ),
		];

		$config = [
			'slidesToShow' => [
				'desktop' => max( 1, intval( $slides['desktop'] ) ),
				'tablet'  => max( 1, intval( $slides['tablet'] ) ),
				'mobile'  => max( 1, intval( $slides['mobile'] ) ),
			],
			'gap'          => [
				'desktop' => floatval( $gap['desktop'] ),
				'tablet'  => floatval( $gap['tablet'] ),
				'mobile'  => floatval( $gap['mobile'] ),
			],
			'autoplay'     => ( 'yes' === ( $settings['autoplay'] ?? '' ) ),
			'autoplaySpeed'=> max( 1000, intval( $settings['autoplay_speed'] ?? 4000 ) ),
			'loop'         => ( 'yes' === ( $settings['loop'] ?? '' ) ),
			'pauseOnHover' => ( 'yes' === ( $settings['pause_on_hover'] ?? '' ) ),
		];

		$uid = 'ta-carousel-' . $this->get_id();
		?>
		<div
			class="ta-carousel"
			id="<?php echo esc_attr( $uid ); ?>"
			data-config="<?php echo esc_attr( wp_json_encode( $config ) ); ?>"
		>
			<div class="ta-carousel__viewport">
				<div class="ta-carousel__track">
					<?php
					while ( $query->have_posts() ) {
						$query->the_post();
						$product = wc_get_product( get_the_ID() );
						if ( ! $product ) {
							continue;
						}
						echo '<div class="ta-carousel__slide">';
						Product_Card::render( $product, $settings );
						echo '</div>';
					}
					wp_reset_postdata();
					?>
				</div>
			</div>

			<?php if ( 'yes' === ( $settings['show_arrows'] ?? '' ) ) : ?>
				<button type="button" class="ta-carousel__arrow ta-carousel__arrow--prev" aria-label="<?php echo esc_attr__( 'Précédent', 'tools-adapter' ); ?>">
					<span aria-hidden="true">‹</span>
				</button>
				<button type="button" class="ta-carousel__arrow ta-carousel__arrow--next" aria-label="<?php echo esc_attr__( 'Suivant', 'tools-adapter' ); ?>">
					<span aria-hidden="true">›</span>
				</button>
			<?php endif; ?>

			<?php if ( 'yes' === ( $settings['show_dots'] ?? '' ) ) : ?>
				<div class="ta-carousel__dots" data-dots></div>
			<?php endif; ?>
		</div>
		<?php
	}
}
