<?php
namespace ToolsAdapter\Widgets;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use ToolsAdapter\Product_Card;
use ToolsAdapter\Products_Query;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Widget: Product Grid
 */
class Product_Grid extends Widget_Base {

	use Products_Widget_Controls;

	public function get_name() {
		return 'tools-adapter-product-grid';
	}

	public function get_title() {
		return esc_html__( 'Grille Produits', 'tools-adapter' );
	}

	public function get_icon() {
		return 'eicon-products';
	}

	public function get_categories() {
		return [ 'tools-adapter', 'woocommerce-elements' ];
	}

	public function get_keywords() {
		return [ 'woocommerce', 'products', 'grid', 'shop', 'sale', 'bestseller' ];
	}

	public function get_style_depends() {
		return [ 'tools-adapter-products', 'tools-adapter-modal', 'tools-adapter-quick-view' ];
	}

	public function get_script_depends() {
		return [ 'tools-adapter-modal', 'tools-adapter-quick-view' ];
	}

	protected function register_controls() {
		$this->register_products_content_controls();

		$this->start_controls_section(
			'section_layout',
			[
				'label' => esc_html__( 'Disposition grille', 'tools-adapter' ),
			]
		);

		$this->add_responsive_control(
			'columns',
			[
				'label'          => esc_html__( 'Colonnes', 'tools-adapter' ),
				'type'           => Controls_Manager::NUMBER,
				'min'            => 1,
				'max'            => 6,
				'default'        => 4,
				'tablet_default' => 2,
				'mobile_default' => 1,
				'selectors'      => [
					'{{WRAPPER}} .ta-products-grid' => '--ta-cols: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'columns_gap',
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
				'selectors'  => [
					'{{WRAPPER}} .ta-products-grid' => '--ta-grid-gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->register_products_style_controls();
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
		?>
		<div class="ta-products ta-products-grid">
			<?php
			while ( $query->have_posts() ) {
				$query->the_post();
				$product = wc_get_product( get_the_ID() );
				if ( ! $product ) {
					continue;
				}
				Product_Card::render( $product, $settings );
			}
			wp_reset_postdata();
			?>
		</div>
		<?php
	}
}
