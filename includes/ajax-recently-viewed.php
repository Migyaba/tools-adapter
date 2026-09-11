<?php
namespace ToolsAdapter;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * AJAX endpoint powering the "Produits récemment consultés" widget.
 * The list of viewed product IDs is tracked client-side (localStorage) by
 * recently-viewed.js and sent here to be rendered as real product cards.
 */
final class Ajax_Recently_Viewed {

	const ACTION = 'tools_adapter_recently_viewed';
	const NONCE  = 'tools_adapter_recently_viewed';

	public function __construct() {
		add_action( 'wp_ajax_' . self::ACTION, [ $this, 'handle' ] );
		add_action( 'wp_ajax_nopriv_' . self::ACTION, [ $this, 'handle' ] );
	}

	public function handle() {
		check_ajax_referer( self::NONCE, 'nonce' );

		if ( ! class_exists( 'WooCommerce' ) ) {
			wp_send_json_error( [ 'message' => __( 'WooCommerce est requis.', 'tools-adapter' ) ], 400 );
		}

		$ids_raw = isset( $_POST['ids'] ) ? wp_unslash( $_POST['ids'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
		$ids     = is_string( $ids_raw ) ? json_decode( $ids_raw, true ) : [];
		$ids     = is_array( $ids ) ? array_values( array_filter( array_map( 'absint', $ids ) ) ) : [];

		$exclude = isset( $_POST['exclude'] ) ? absint( wp_unslash( $_POST['exclude'] ) ) : 0;
		$limit   = isset( $_POST['limit'] ) ? absint( wp_unslash( $_POST['limit'] ) ) : 8;

		if ( $exclude ) {
			$ids = array_values( array_diff( $ids, [ $exclude ] ) );
		}

		$ids = array_slice( $ids, 0, $limit );

		if ( empty( $ids ) ) {
			wp_send_json_success( [ 'html' => '', 'count' => 0 ] );
		}

		$card_settings = [];
		$raw_settings  = isset( $_POST['card_settings'] ) ? wp_unslash( $_POST['card_settings'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
		if ( is_string( $raw_settings ) && '' !== $raw_settings ) {
			$decoded = json_decode( $raw_settings, true );
			if ( is_array( $decoded ) ) {
				$card_settings = $decoded;
			}
		}

		$query = new \WP_Query(
			[
				'post_type'      => 'product',
				'post_status'    => 'publish',
				'post__in'       => $ids,
				'orderby'        => 'post__in',
				'posts_per_page' => count( $ids ),
			]
		);

		ob_start();
		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				$product = wc_get_product( get_the_ID() );
				if ( $product ) {
					Product_Card::render( $product, $card_settings );
				}
			}
		}
		wp_reset_postdata();
		$html = ob_get_clean();

		wp_send_json_success( [ 'html' => $html, 'count' => $query->found_posts ] );
	}
}
