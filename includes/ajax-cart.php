<?php
namespace ToolsAdapter;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * AJAX endpoints for the Mini-panier widget: fetch cart fragment, remove item.
 */
final class Ajax_Cart {

	const ACTION_GET    = 'tools_adapter_cart_get';
	const ACTION_REMOVE = 'tools_adapter_cart_remove';
	const NONCE         = 'tools_adapter_cart';

	public function __construct() {
		add_action( 'wp_ajax_' . self::ACTION_GET, [ $this, 'handle_get' ] );
		add_action( 'wp_ajax_nopriv_' . self::ACTION_GET, [ $this, 'handle_get' ] );
		add_action( 'wp_ajax_' . self::ACTION_REMOVE, [ $this, 'handle_remove' ] );
		add_action( 'wp_ajax_nopriv_' . self::ACTION_REMOVE, [ $this, 'handle_remove' ] );
	}

	public function handle_get() {
		check_ajax_referer( self::NONCE, 'nonce' );
		$this->respond();
	}

	public function handle_remove() {
		check_ajax_referer( self::NONCE, 'nonce' );

		if ( ! class_exists( 'WooCommerce' ) || ! WC()->cart ) {
			wp_send_json_error( [ 'message' => __( 'WooCommerce est requis.', 'tools-adapter' ) ], 400 );
		}

		$cart_item_key = isset( $_POST['cart_item_key'] ) ? sanitize_text_field( wp_unslash( $_POST['cart_item_key'] ) ) : '';
		if ( $cart_item_key ) {
			WC()->cart->remove_cart_item( $cart_item_key );
		}

		$this->respond();
	}

	/**
	 * Build & send the cart fragment (items html, count, subtotal).
	 */
	private function respond() {
		if ( ! class_exists( 'WooCommerce' ) || ! WC()->cart ) {
			wp_send_json_error( [ 'message' => __( 'WooCommerce est requis.', 'tools-adapter' ) ], 400 );
		}

		WC()->cart->calculate_totals();

		ob_start();
		$this->render_items();
		$items_html = ob_get_clean();

		wp_send_json_success(
			[
				'itemsHtml' => $items_html,
				'count'     => WC()->cart->get_cart_contents_count(),
				'subtotal'  => wp_kses_post( WC()->cart->get_cart_subtotal() ),
				'cartUrl'   => wc_get_cart_url(),
				'checkoutUrl' => wc_get_checkout_url(),
				'isEmpty'   => WC()->cart->is_empty(),
			]
		);
	}

	/**
	 * Render the list of cart items (shared between first paint and AJAX).
	 */
	public static function render_items() {
		if ( ! class_exists( 'WooCommerce' ) || ! WC()->cart ) {
			return;
		}

		$cart = WC()->cart->get_cart();

		if ( empty( $cart ) ) {
			echo '<p class="ta-minicart__empty">' . esc_html__( 'Votre panier est vide.', 'tools-adapter' ) . '</p>';
			return;
		}

		foreach ( $cart as $cart_item_key => $cart_item ) {
			$product = $cart_item['data'];
			if ( ! $product ) {
				continue;
			}
			$thumbnail = $product->get_image( 'thumbnail' );
			$permalink = $product->get_permalink( $cart_item );
			?>
			<div class="ta-minicart__item" data-cart-item-key="<?php echo esc_attr( $cart_item_key ); ?>">
				<a class="ta-minicart__thumb" href="<?php echo esc_url( $permalink ); ?>">
					<?php echo wp_kses_post( $thumbnail ); ?>
				</a>
				<div class="ta-minicart__details">
					<a class="ta-minicart__name" href="<?php echo esc_url( $permalink ); ?>"><?php echo wp_kses_post( $product->get_name() ); ?></a>
					<span class="ta-minicart__qty-price">
						<?php echo esc_html( $cart_item['quantity'] ); ?> &times; <?php echo wp_kses_post( wc_price( $product->get_price() ) ); ?>
					</span>
				</div>
				<button type="button" class="ta-minicart__remove" data-cart-remove="<?php echo esc_attr( $cart_item_key ); ?>" aria-label="<?php echo esc_attr__( 'Retirer cet article', 'tools-adapter' ); ?>">&times;</button>
			</div>
			<?php
		}
	}
}
