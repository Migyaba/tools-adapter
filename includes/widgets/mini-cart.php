<?php
namespace ToolsAdapter\Widgets;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use ToolsAdapter\Ajax_Cart;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Widget: Mini-panier — icône panier avec compteur + dropdown AJAX.
 */
class Mini_Cart extends Widget_Base {

	public function get_name() {
		return 'tools-adapter-mini-cart';
	}

	public function get_title() {
		return esc_html__( 'Mini-panier', 'tools-adapter' );
	}

	public function get_icon() {
		return 'eicon-cart';
	}

	public function get_categories() {
		return [ 'tools-adapter', 'woocommerce-elements' ];
	}

	public function get_keywords() {
		return [ 'panier', 'cart', 'mini-panier', 'woocommerce' ];
	}

	public function get_style_depends() {
		return [ 'tools-adapter-mini-cart' ];
	}

	public function get_script_depends() {
		return [ 'tools-adapter-mini-cart' ];
	}

	protected function register_controls() {
		$this->start_controls_section( 'section_content', [ 'label' => esc_html__( 'Contenu', 'tools-adapter' ) ] );

		$this->add_control( 'icon', [ 'label' => esc_html__( 'Icône', 'tools-adapter' ), 'type' => Controls_Manager::ICONS, 'default' => [ 'value' => 'fas fa-shopping-bag', 'library' => 'fa-solid' ] ] );
		$this->add_control( 'show_subtotal', [ 'label' => esc_html__( 'Afficher le sous-total', 'tools-adapter' ), 'type' => Controls_Manager::SWITCHER, 'return_value' => 'yes', 'default' => 'yes' ] );
		$this->add_control( 'checkout_text', [ 'label' => esc_html__( 'Texte bouton commande', 'tools-adapter' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'Commander', 'tools-adapter' ) ] );
		$this->add_control( 'cart_text', [ 'label' => esc_html__( 'Texte lien panier', 'tools-adapter' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'Voir le panier', 'tools-adapter' ) ] );
		$this->add_control(
			'dropdown_position',
			[
				'label'        => esc_html__( 'Alignement du dropdown', 'tools-adapter' ),
				'type'         => Controls_Manager::SELECT,
				'default'      => 'right',
				'options'      => [ 'left' => esc_html__( 'Gauche', 'tools-adapter' ), 'right' => esc_html__( 'Droite', 'tools-adapter' ) ],
				'prefix_class' => 'ta-minicart-pos--',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section( 'section_style_icon', [ 'label' => esc_html__( 'Icône & compteur', 'tools-adapter' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_control( 'icon_color', [ 'label' => esc_html__( 'Couleur icône', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#1c1c1c', 'selectors' => [ '{{WRAPPER}} .ta-minicart__icon' => 'color: {{VALUE}};' ] ] );
		$this->add_control( 'icon_size', [ 'label' => esc_html__( 'Taille icône', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'range' => [ 'px' => [ 'min' => 14, 'max' => 48 ] ], 'default' => [ 'size' => 22, 'unit' => 'px' ], 'selectors' => [ '{{WRAPPER}} .ta-minicart__icon' => 'font-size: {{SIZE}}{{UNIT}};' ] ] );
		$this->add_control( 'badge_color', [ 'label' => esc_html__( 'Couleur texte badge', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#ffffff', 'separator' => 'before', 'selectors' => [ '{{WRAPPER}} .ta-minicart__badge' => 'color: {{VALUE}};' ] ] );
		$this->add_control( 'badge_bg', [ 'label' => esc_html__( 'Couleur fond badge', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#C9A84C', 'selectors' => [ '{{WRAPPER}} .ta-minicart__badge' => 'background-color: {{VALUE}};' ] ] );

		$this->end_controls_section();

		$this->start_controls_section( 'section_style_dropdown', [ 'label' => esc_html__( 'Dropdown', 'tools-adapter' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_control( 'dropdown_bg', [ 'label' => esc_html__( 'Fond', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#ffffff', 'selectors' => [ '{{WRAPPER}} .ta-minicart__dropdown' => 'background-color: {{VALUE}};' ] ] );
		$this->add_control( 'dropdown_width', [ 'label' => esc_html__( 'Largeur', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'range' => [ 'px' => [ 'min' => 260, 'max' => 480 ] ], 'default' => [ 'size' => 340, 'unit' => 'px' ], 'selectors' => [ '{{WRAPPER}} .ta-minicart__dropdown' => 'width: {{SIZE}}{{UNIT}};' ] ] );
		$this->add_group_control( Group_Control_Box_Shadow::get_type(), [ 'name' => 'dropdown_shadow', 'selector' => '{{WRAPPER}} .ta-minicart__dropdown' ] );
		$this->add_control( 'name_color', [ 'label' => esc_html__( 'Couleur nom produit', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#1c1c1c', 'separator' => 'before', 'selectors' => [ '{{WRAPPER}} .ta-minicart__name' => 'color: {{VALUE}};' ] ] );
		$this->add_control( 'price_color', [ 'label' => esc_html__( 'Couleur prix', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#666666', 'selectors' => [ '{{WRAPPER}} .ta-minicart__qty-price' => 'color: {{VALUE}};' ] ] );
		$this->add_control( 'checkout_bg', [ 'label' => esc_html__( 'Fond bouton commande', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#1c1c1c', 'separator' => 'before', 'selectors' => [ '{{WRAPPER}} .ta-minicart__checkout' => 'background-color: {{VALUE}};' ] ] );
		$this->add_control( 'checkout_color', [ 'label' => esc_html__( 'Texte bouton commande', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#ffffff', 'selectors' => [ '{{WRAPPER}} .ta-minicart__checkout' => 'color: {{VALUE}};' ] ] );

		$this->end_controls_section();
	}

	protected function render() {
		if ( ! class_exists( 'WooCommerce' ) ) {
			echo '<p>' . esc_html__( 'WooCommerce est requis pour ce widget.', 'tools-adapter' ) . '</p>';
			return;
		}

		$settings = $this->get_settings_for_display();
		$count    = ( WC()->cart ) ? WC()->cart->get_cart_contents_count() : 0;
		$subtotal = ( WC()->cart ) ? WC()->cart->get_cart_subtotal() : '';
		?>
		<div class="ta-minicart" data-ta-minicart data-nonce="<?php echo esc_attr( wp_create_nonce( Ajax_Cart::NONCE ) ); ?>">
			<button type="button" class="ta-minicart__toggle" data-minicart-toggle aria-expanded="false" aria-label="<?php echo esc_attr__( 'Ouvrir le panier', 'tools-adapter' ); ?>">
				<span class="ta-minicart__icon" aria-hidden="true">
					<?php if ( ! empty( $settings['icon']['value'] ) ) : ?>
						<?php \Elementor\Icons_Manager::render_icon( $settings['icon'], [ 'aria-hidden' => 'true' ] ); ?>
					<?php endif; ?>
				</span>
				<span class="ta-minicart__badge" data-minicart-count><?php echo esc_html( $count ); ?></span>
			</button>

			<div class="ta-minicart__dropdown" data-minicart-dropdown>
				<div class="ta-minicart__items" data-minicart-items>
					<?php Ajax_Cart::render_items(); ?>
				</div>
				<?php if ( 'yes' === ( $settings['show_subtotal'] ?? '' ) ) : ?>
					<div class="ta-minicart__subtotal">
						<span><?php echo esc_html__( 'Sous-total', 'tools-adapter' ); ?></span>
						<strong data-minicart-subtotal><?php echo wp_kses_post( $subtotal ); ?></strong>
					</div>
				<?php endif; ?>
				<div class="ta-minicart__actions">
					<a class="ta-minicart__view-cart" href="<?php echo esc_url( wc_get_cart_url() ); ?>"><?php echo esc_html( \tools_adapter_translate( $settings['cart_text'] ?? '' ) ); ?></a>
					<a class="ta-minicart__checkout" href="<?php echo esc_url( wc_get_checkout_url() ); ?>"><?php echo esc_html( \tools_adapter_translate( $settings['checkout_text'] ?? '' ) ); ?></a>
				</div>
			</div>
		</div>
		<?php
	}
}
