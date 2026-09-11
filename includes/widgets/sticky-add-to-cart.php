<?php
namespace ToolsAdapter\Widgets;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Widget: Barre "Ajouter au panier" collante — pour pages produit simple WooCommerce.
 */
class Sticky_Add_To_Cart extends Widget_Base {

	public function get_name() {
		return 'tools-adapter-sticky-atc';
	}

	public function get_title() {
		return esc_html__( 'Barre panier collante', 'tools-adapter' );
	}

	public function get_icon() {
		return 'eicon-single-product';
	}

	public function get_categories() {
		return [ 'tools-adapter', 'woocommerce-elements' ];
	}

	public function get_keywords() {
		return [ 'sticky', 'panier', 'ajouter au panier', 'collant' ];
	}

	public function get_style_depends() {
		return [ 'tools-adapter-sticky-atc' ];
	}

	public function get_script_depends() {
		return [ 'tools-adapter-sticky-atc' ];
	}

	protected function register_controls() {
		$this->start_controls_section( 'section_content', [ 'label' => esc_html__( 'Réglages', 'tools-adapter' ) ] );

		$this->add_control(
			'trigger_selector',
			[
				'label'       => esc_html__( 'Sélecteur du formulaire produit', 'tools-adapter' ),
				'description' => esc_html__( 'La barre apparaît quand cet élément sort du viewport en défilant.', 'tools-adapter' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => 'form.cart',
			]
		);

		$this->add_control( 'show_image', [ 'label' => esc_html__( 'Afficher l\'image', 'tools-adapter' ), 'type' => Controls_Manager::SWITCHER, 'return_value' => 'yes', 'default' => 'yes' ] );
		$this->add_control( 'show_price', [ 'label' => esc_html__( 'Afficher le prix', 'tools-adapter' ), 'type' => Controls_Manager::SWITCHER, 'return_value' => 'yes', 'default' => 'yes' ] );
		$this->add_control( 'show_quantity', [ 'label' => esc_html__( 'Afficher le sélecteur de quantité', 'tools-adapter' ), 'type' => Controls_Manager::SWITCHER, 'return_value' => 'yes', 'default' => 'yes' ] );
		$this->add_control( 'button_text', [ 'label' => esc_html__( 'Texte du bouton', 'tools-adapter' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'Ajouter au panier', 'tools-adapter' ) ] );
		$this->add_control( 'variable_notice', [ 'label' => esc_html__( 'Texte (produit variable)', 'tools-adapter' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'Choisir les options', 'tools-adapter' ) ] );

		$this->end_controls_section();

		$this->start_controls_section( 'section_style', [ 'label' => esc_html__( 'Style', 'tools-adapter' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_control( 'bar_bg', [ 'label' => esc_html__( 'Fond', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#ffffff', 'selectors' => [ '{{WRAPPER}} .ta-sticky-atc' => 'background-color: {{VALUE}};' ] ] );
		$this->add_group_control( Group_Control_Box_Shadow::get_type(), [ 'name' => 'bar_shadow', 'selector' => '{{WRAPPER}} .ta-sticky-atc' ] );
		$this->add_control( 'name_color', [ 'label' => esc_html__( 'Couleur du nom', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#1c1c1c', 'separator' => 'before', 'selectors' => [ '{{WRAPPER}} .ta-sticky-atc__name' => 'color: {{VALUE}};' ] ] );
		$this->add_control( 'price_color', [ 'label' => esc_html__( 'Couleur du prix', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#C9A84C', 'selectors' => [ '{{WRAPPER}} .ta-sticky-atc__price' => 'color: {{VALUE}};' ] ] );
		$this->add_control( 'button_bg', [ 'label' => esc_html__( 'Fond bouton', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#1c1c1c', 'separator' => 'before', 'selectors' => [ '{{WRAPPER}} .ta-sticky-atc__btn' => 'background-color: {{VALUE}};' ] ] );
		$this->add_control( 'button_color', [ 'label' => esc_html__( 'Texte bouton', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#ffffff', 'selectors' => [ '{{WRAPPER}} .ta-sticky-atc__btn' => 'color: {{VALUE}};' ] ] );

		$this->end_controls_section();
	}

	protected function render() {
		if ( ! function_exists( 'is_product' ) || ! is_product() ) {
			echo '<p class="ta-sticky-atc__editor-notice">' . esc_html__( 'Ce widget ne s\'affiche que sur les pages produit WooCommerce.', 'tools-adapter' ) . '</p>';
			return;
		}

		global $product;
		if ( ! $product instanceof \WC_Product ) {
			$product = wc_get_product( get_the_ID() );
		}
		if ( ! $product ) {
			return;
		}

		$settings   = $this->get_settings_for_display();
		$is_simple  = $product->is_type( 'simple' );
		$max_qty    = $product->get_max_purchase_quantity();
		?>
		<div
			class="ta-sticky-atc"
			data-ta-sticky-atc
			data-trigger="<?php echo esc_attr( $settings['trigger_selector'] ?? 'form.cart' ); ?>"
			data-simple="<?php echo $is_simple ? '1' : '0'; ?>"
		>
			<div class="ta-sticky-atc__inner">
				<?php if ( 'yes' === ( $settings['show_image'] ?? '' ) ) : ?>
					<div class="ta-sticky-atc__thumb"><?php echo wp_kses_post( $product->get_image( 'thumbnail' ) ); ?></div>
				<?php endif; ?>

				<div class="ta-sticky-atc__info">
					<p class="ta-sticky-atc__name"><?php echo wp_kses_post( $product->get_name() ); ?></p>
					<?php if ( 'yes' === ( $settings['show_price'] ?? '' ) ) : ?>
						<span class="ta-sticky-atc__price"><?php echo wp_kses_post( $product->get_price_html() ); ?></span>
					<?php endif; ?>
				</div>

				<?php if ( $is_simple ) : ?>
					<?php if ( 'yes' === ( $settings['show_quantity'] ?? '' ) ) : ?>
						<div class="ta-sticky-atc__qty">
							<button type="button" class="ta-sticky-atc__qty-btn" data-qty-decrease aria-label="<?php echo esc_attr__( 'Diminuer', 'tools-adapter' ); ?>">&minus;</button>
							<input type="number" class="ta-sticky-atc__qty-input" data-qty-input value="1" min="1" <?php echo $max_qty > 0 ? 'max="' . esc_attr( $max_qty ) . '"' : ''; ?> />
							<button type="button" class="ta-sticky-atc__qty-btn" data-qty-increase aria-label="<?php echo esc_attr__( 'Augmenter', 'tools-adapter' ); ?>">&#43;</button>
						</div>
					<?php endif; ?>
					<button type="button" class="ta-sticky-atc__btn" data-sticky-add><?php echo esc_html( \tools_adapter_translate( $settings['button_text'] ?? '' ) ); ?></button>
				<?php else : ?>
					<button type="button" class="ta-sticky-atc__btn" data-sticky-scroll><?php echo esc_html( \tools_adapter_translate( $settings['variable_notice'] ?? '' ) ); ?></button>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}
}
