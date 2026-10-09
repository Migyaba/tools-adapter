<?php
namespace ToolsAdapter;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Liste de souhaits (wishlist).
 *
 * The list lives in the browser (localStorage) so it works for guests; for
 * logged-in customers it is mirrored in user meta and merged on every page
 * load, so it follows them across devices.
 */
final class Wishlist {

	const ACTION_SYNC   = 'tools_adapter_wishlist_sync';
	const ACTION_RENDER = 'tools_adapter_wishlist_render';
	const NONCE         = 'tools_adapter_wishlist';
	const META_KEY      = '_ta_wishlist';
	const MAX_ITEMS     = 100;

	public function __construct() {
		add_action( 'wp_ajax_' . self::ACTION_SYNC, [ $this, 'handle_sync' ] );
		add_action( 'wp_ajax_' . self::ACTION_RENDER, [ $this, 'handle_render' ] );
		add_action( 'wp_ajax_nopriv_' . self::ACTION_RENDER, [ $this, 'handle_render' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue' ], 20 );

		add_shortcode( 'ta_wishlist', [ $this, 'shortcode_list' ] );
		add_shortcode( 'ta_wishlist_count', [ $this, 'shortcode_count' ] );
	}

	/**
	 * Wishlist page URL (set by the theme or via the filter).
	 *
	 * @return string
	 */
	public static function get_page_url() {
		$page_id = (int) get_option( 'tools_adapter_wishlist_page', 0 );
		$url     = $page_id ? get_permalink( $page_id ) : '';

		return (string) apply_filters( 'tools_adapter_wishlist_page_url', $url ? $url : '' );
	}

	/**
	 * Keep only IDs of visible, published products.
	 *
	 * @param mixed $raw Array or comma-separated string.
	 * @return int[]
	 */
	public static function sanitize_ids( $raw ) {
		if ( is_string( $raw ) ) {
			$raw = explode( ',', $raw );
		}
		if ( ! is_array( $raw ) ) {
			return [];
		}

		$ids = array_values( array_unique( array_filter( array_map( 'absint', $raw ) ) ) );
		$ids = array_slice( $ids, 0, self::MAX_ITEMS );

		return array_values(
			array_filter(
				$ids,
				static function ( $id ) {
					return 'product' === get_post_type( $id ) && 'publish' === get_post_status( $id );
				}
			)
		);
	}

	/**
	 * Stored wishlist of a user.
	 *
	 * @param int $user_id User ID (current user by default).
	 * @return int[]
	 */
	public static function get_user_items( $user_id = 0 ) {
		$user_id = $user_id ? $user_id : get_current_user_id();
		if ( ! $user_id ) {
			return [];
		}

		$items = get_user_meta( $user_id, self::META_KEY, true );

		return is_array( $items ) ? array_map( 'absint', $items ) : [];
	}

	public function enqueue() {
		if ( ! class_exists( 'WooCommerce' ) ) {
			return;
		}

		wp_enqueue_style( 'tools-adapter-wishlist' );

		// The list ([ta_wishlist] / widget) renders Tools Adapter product cards.
		if ( self::get_page_url() && is_page( (int) get_option( 'tools_adapter_wishlist_page', 0 ) ) ) {
			wp_enqueue_style( 'tools-adapter-products' );
			wp_enqueue_style( 'tools-adapter-quick-view' );
			wp_enqueue_script( 'tools-adapter-quick-view' );
		}
		wp_enqueue_script( 'tools-adapter-wishlist' );
		wp_localize_script(
			'tools-adapter-wishlist',
			'ToolsAdapterWishlist',
			[
				'ajaxUrl'      => admin_url( 'admin-ajax.php' ),
				'actionSync'   => self::ACTION_SYNC,
				'actionRender' => self::ACTION_RENDER,
				'nonce'        => wp_create_nonce( self::NONCE ),
				'loggedIn'     => is_user_logged_in(),
				'items'        => self::get_user_items(),
				'max'          => self::MAX_ITEMS,
				'pageUrl'      => self::get_page_url(),
				'i18n'         => [
					'add'    => __( 'Ajouter à la liste de souhaits', 'tools-adapter' ),
					'remove' => __( 'Retirer de la liste de souhaits', 'tools-adapter' ),
					'added'  => __( 'Ajouté à votre liste de souhaits', 'tools-adapter' ),
					'view'   => __( 'Voir la liste', 'tools-adapter' ),
				],
			]
		);
	}

	/**
	 * Save the list of the logged-in customer (the browser sends the merged list).
	 */
	public function handle_sync() {
		check_ajax_referer( self::NONCE, 'nonce' );

		if ( ! is_user_logged_in() ) {
			wp_send_json_error( [ 'message' => __( 'Connexion requise.', 'tools-adapter' ) ], 403 );
		}

		$items = self::sanitize_ids( isset( $_POST['items'] ) ? sanitize_text_field( wp_unslash( $_POST['items'] ) ) : '' );
		update_user_meta( get_current_user_id(), self::META_KEY, $items );

		wp_send_json_success( [ 'items' => $items ] );
	}

	/**
	 * Render the wishlist products for the IDs sent by the browser.
	 */
	public function handle_render() {
		check_ajax_referer( self::NONCE, 'nonce' );

		$items   = self::sanitize_ids( isset( $_POST['items'] ) ? sanitize_text_field( wp_unslash( $_POST['items'] ) ) : '' );
		$columns = isset( $_POST['columns'] ) ? max( 1, min( 6, absint( $_POST['columns'] ) ) ) : 4;

		ob_start();
		self::render_items( $items, $columns );

		wp_send_json_success( [ 'html' => ob_get_clean(), 'items' => $items ] );
	}

	/**
	 * Products grid (or empty state).
	 *
	 * @param int[] $items   Product IDs.
	 * @param int   $columns Desktop columns.
	 */
	public static function render_items( array $items, $columns = 4 ) {
		if ( ! $items || ! class_exists( 'WooCommerce' ) ) {
			$shop = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' );
			?>
			<div class="ta-wishlist__empty">
				<span class="ta-wishlist__empty-icon"><?php echo self::heart_svg(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?></span>
				<p class="ta-wishlist__empty-title"><?php esc_html_e( 'Votre liste de souhaits est vide', 'tools-adapter' ); ?></p>
				<p><?php esc_html_e( 'Cliquez sur le cœur d\'un produit pour le retrouver ici plus tard.', 'tools-adapter' ); ?></p>
				<a class="button ta-wishlist__shop" href="<?php echo esc_url( $shop ); ?>"><?php esc_html_e( 'Découvrir la boutique', 'tools-adapter' ); ?></a>
			</div>
			<?php
			return;
		}

		$settings = [
			'show_quick_view' => 'yes',
			'show_wishlist'   => 'yes',
			'image_size'      => 'woocommerce_thumbnail',
		];
		?>
		<div class="ta-products ta-products-grid ta-wishlist__grid" style="--ta-cols: <?php echo absint( $columns ); ?>;">
			<?php
			foreach ( $items as $id ) {
				$product = wc_get_product( $id );
				if ( $product && $product->is_visible() ) {
					Product_Card::render( $product, $settings );
				}
			}
			?>
		</div>
		<?php
	}

	/**
	 * Heart toggle button.
	 *
	 * @param int    $product_id Product ID.
	 * @param string $class      Extra classes.
	 * @return string
	 */
	public static function button( $product_id, $class = '' ) {
		return sprintf(
			'<button type="button" class="ta-wishlist-btn %1$s" data-ta-wishlist="%2$d" aria-pressed="false" aria-label="%3$s" title="%3$s">%4$s</button>',
			esc_attr( $class ),
			absint( $product_id ),
			esc_attr__( 'Ajouter à la liste de souhaits', 'tools-adapter' ),
			self::heart_svg()
		);
	}

	/**
	 * Counter badge (hidden while empty).
	 *
	 * @param string $class Extra classes.
	 * @return string
	 */
	public static function count( $class = '' ) {
		return '<span class="ta-wishlist-count ' . esc_attr( $class ) . '" data-ta-wishlist-count data-count="0">0</span>';
	}

	/**
	 * Heart icon.
	 *
	 * @return string
	 */
	public static function heart_svg() {
		return '<svg class="ta-wishlist-heart" viewBox="0 0 24 24" width="18" height="18" aria-hidden="true" focusable="false"><path d="M12 20s-7-4.4-7-10a4 4 0 0 1 7-2.6A4 4 0 0 1 19 10c0 5.6-7 10-7 10Z"/></svg>';
	}

	/**
	 * [ta_wishlist columns="4"] — the list, filled by JavaScript.
	 *
	 * @param array $atts Attributes.
	 * @return string
	 */
	public function shortcode_list( $atts ) {
		$atts = shortcode_atts( [ 'columns' => 4 ], $atts, 'ta_wishlist' );

		return self::list_markup( absint( $atts['columns'] ) );
	}

	/**
	 * Container filled by wishlist.js.
	 *
	 * @param int $columns Desktop columns.
	 * @return string
	 */
	public static function list_markup( $columns = 4 ) {
		return sprintf(
			'<div class="ta-wishlist" data-ta-wishlist-list data-columns="%1$d"><div class="ta-wishlist__loading" aria-live="polite">%2$s</div></div>',
			max( 1, min( 6, $columns ) ),
			esc_html__( 'Chargement de votre liste…', 'tools-adapter' )
		);
	}

	/**
	 * [ta_wishlist_count] — link with counter.
	 *
	 * @return string
	 */
	public function shortcode_count() {
		$url = self::get_page_url();

		return sprintf(
			'<a class="ta-wishlist-link" href="%1$s" aria-label="%2$s">%3$s%4$s</a>',
			esc_url( $url ? $url : '#' ),
			esc_attr__( 'Liste de souhaits', 'tools-adapter' ),
			self::heart_svg(),
			self::count()
		);
	}
}
