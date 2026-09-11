<?php
namespace ToolsAdapter;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * AJAX endpoint powering the "Vue rapide produit" (Quick View) modal.
 */
final class Ajax_Quick_View {

	const ACTION = 'tools_adapter_quick_view';
	const NONCE  = 'tools_adapter_quick_view';

	public function __construct() {
		add_action( 'wp_ajax_' . self::ACTION, [ $this, 'handle' ] );
		add_action( 'wp_ajax_nopriv_' . self::ACTION, [ $this, 'handle' ] );
	}

	public function handle() {
		check_ajax_referer( self::NONCE, 'nonce' );

		if ( ! class_exists( 'WooCommerce' ) ) {
			wp_send_json_error( [ 'message' => __( 'WooCommerce est requis.', 'tools-adapter' ) ], 400 );
		}

		$product_id = isset( $_POST['product_id'] ) ? absint( wp_unslash( $_POST['product_id'] ) ) : 0;
		$product    = $product_id ? wc_get_product( $product_id ) : null;

		if ( ! $product ) {
			wp_send_json_error( [ 'message' => __( 'Produit introuvable.', 'tools-adapter' ) ], 404 );
		}

		ob_start();
		self::render( $product );
		$html = ob_get_clean();

		wp_send_json_success( [ 'html' => $html, 'title' => $product->get_name() ] );
	}

	/**
	 * Render the quick view content for a given product (image, price, short
	 * description and the real WooCommerce add-to-cart form).
	 *
	 * @param \WC_Product $product Product.
	 */
	public static function render( $product ) {
		global $post;
		$saved_post    = $post;
		$saved_product = isset( $GLOBALS['product'] ) ? $GLOBALS['product'] : null;

		$post                    = get_post( $product->get_id() ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
		$GLOBALS['product']      = $product;
		setup_postdata( $post );
		?>
		<div class="ta-quick-view">
			<div class="ta-quick-view__gallery">
				<?php echo wp_kses_post( $product->get_image( 'woocommerce_single' ) ); ?>
			</div>
			<div class="ta-quick-view__summary">
				<p class="ta-quick-view__price"><?php echo wp_kses_post( $product->get_price_html() ); ?></p>

				<?php if ( wc_review_ratings_enabled() ) : ?>
					<div class="ta-quick-view__rating"><?php echo wc_get_rating_html( $product->get_average_rating() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
				<?php endif; ?>

				<div class="ta-quick-view__excerpt">
					<?php echo wp_kses_post( wpautop( $product->get_short_description() ) ); ?>
				</div>

				<div class="ta-quick-view__cart-form">
					<?php woocommerce_template_single_add_to_cart(); ?>
				</div>

				<p class="ta-quick-view__link">
					<a href="<?php echo esc_url( get_permalink( $product->get_id() ) ); ?>"><?php echo esc_html__( 'Voir tous les détails →', 'tools-adapter' ); ?></a>
				</p>
			</div>
		</div>
		<?php
		$post                = $saved_post; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
		$GLOBALS['product']  = $saved_product;
		if ( $saved_post ) {
			setup_postdata( $saved_post );
		}
	}
}
