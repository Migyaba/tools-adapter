<?php
namespace ToolsAdapter;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * AJAX product filtering by price range.
 */
final class Ajax_Price_Filter {

	const ACTION = 'tools_adapter_filter_products';

	public function __construct() {
		add_action( 'wp_ajax_' . self::ACTION, [ $this, 'handle' ] );
		add_action( 'wp_ajax_nopriv_' . self::ACTION, [ $this, 'handle' ] );
	}

	/**
	 * Handle AJAX request — returns product loop HTML.
	 */
	public function handle() {
		check_ajax_referer( 'tools_adapter_price_filter', 'nonce' );

		if ( ! class_exists( 'WooCommerce' ) ) {
			wp_send_json_error(
				[ 'message' => __( 'WooCommerce est requis.', 'tools-adapter' ) ],
				400
			);
		}

		$min_price = isset( $_POST['min_price'] ) ? floatval( wp_unslash( $_POST['min_price'] ) ) : 0;
		$max_price = isset( $_POST['max_price'] ) ? floatval( wp_unslash( $_POST['max_price'] ) ) : 0;
		$page      = isset( $_POST['page'] ) ? max( 1, intval( wp_unslash( $_POST['page'] ) ) ) : 1;
		$per_page  = isset( $_POST['per_page'] ) ? intval( wp_unslash( $_POST['per_page'] ) ) : 0;
		$orderby   = isset( $_POST['orderby'] ) ? sanitize_text_field( wp_unslash( $_POST['orderby'] ) ) : '';
		$taxonomy  = isset( $_POST['taxonomy'] ) ? sanitize_key( wp_unslash( $_POST['taxonomy'] ) ) : '';
		$term_id   = isset( $_POST['term_id'] ) ? intval( wp_unslash( $_POST['term_id'] ) ) : 0;

		if ( $per_page <= 0 ) {
			$per_page = (int) apply_filters( 'loop_shop_per_page', wc_get_default_products_per_row() * wc_get_default_product_rows_per_page() );
		}

		$query_args = [
			'post_type'           => 'product',
			'post_status'         => 'publish',
			'ignore_sticky_posts' => true,
			'posts_per_page'      => $per_page,
			'paged'               => $page,
			'meta_query'          => WC()->query->get_meta_query(), // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
			'tax_query'           => WC()->query->get_tax_query(), // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
		];

		// Price meta query (WooCommerce style).
		$price_query = [
			'relation' => 'AND',
		];

		if ( $min_price > 0 || $max_price > 0 ) {
			$price_meta = [
				'key'     => '_price',
				'type'    => 'DECIMAL(10,2)',
				'compare' => 'BETWEEN',
				'value'   => [
					$min_price > 0 ? $min_price : 0,
					$max_price > 0 ? $max_price : PHP_INT_MAX,
				],
			];

			if ( $min_price > 0 && $max_price <= 0 ) {
				$price_meta['compare'] = '>=';
				$price_meta['value']   = $min_price;
			} elseif ( $max_price > 0 && $min_price <= 0 ) {
				$price_meta['compare'] = '<=';
				$price_meta['value']   = $max_price;
			}

			$price_query[] = $price_meta;
		}

		if ( count( $price_query ) > 1 ) {
			$query_args['meta_query'][] = $price_query; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
		}

		// Optional current category / taxonomy context.
		if ( $taxonomy && $term_id > 0 && taxonomy_exists( $taxonomy ) ) {
			$query_args['tax_query'][] = [ // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
				'taxonomy' => $taxonomy,
				'field'    => 'term_id',
				'terms'    => [ $term_id ],
			];
		}

		// Ordering.
		if ( $orderby ) {
			$query_args = $this->apply_ordering( $query_args, $orderby );
		}

		$query_args = apply_filters( 'tools_adapter_price_filter_query_args', $query_args, $_POST );

		$products = new \WP_Query( $query_args );

		ob_start();

		if ( $products->have_posts() ) {
			woocommerce_product_loop_start();

			while ( $products->have_posts() ) {
				$products->the_post();
				wc_get_template_part( 'content', 'product' );
			}

			woocommerce_product_loop_end();
		} else {
			echo '<p class="woocommerce-info ta-price-filter__empty">' . esc_html__( 'Aucun produit ne correspond à cette plage de prix.', 'tools-adapter' ) . '</p>';
		}

		$html = ob_get_clean();
		wp_reset_postdata();

		$result_count = '';
		if ( function_exists( 'woocommerce_result_count' ) ) {
			ob_start();
			// Temporarily mimic loop globals for result count template.
			$GLOBALS['woocommerce_loop']['total']    = (int) $products->found_posts;
			$GLOBALS['woocommerce_loop']['per_page'] = $per_page;
			$GLOBALS['woocommerce_loop']['current_page'] = $page;
			woocommerce_result_count();
			$result_count = ob_get_clean();
		}

		wp_send_json_success(
			[
				'html'         => $html,
				'found'        => (int) $products->found_posts,
				'max_pages'    => (int) $products->max_num_pages,
				'page'         => $page,
				'result_count' => $result_count,
				'min_price'    => $min_price,
				'max_price'    => $max_price,
			]
		);
	}

	/**
	 * Apply WooCommerce-like ordering.
	 *
	 * @param array  $args    Query args.
	 * @param string $orderby Orderby key.
	 * @return array
	 */
	private function apply_ordering( array $args, $orderby ) {
		switch ( $orderby ) {
			case 'price':
				$args['orderby']  = 'meta_value_num';
				$args['meta_key'] = '_price'; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
				$args['order']    = 'ASC';
				break;
			case 'price-desc':
				$args['orderby']  = 'meta_value_num';
				$args['meta_key'] = '_price'; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
				$args['order']    = 'DESC';
				break;
			case 'popularity':
				$args['orderby']  = 'meta_value_num';
				$args['meta_key'] = 'total_sales'; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
				$args['order']    = 'DESC';
				break;
			case 'rating':
				$args['orderby']  = 'meta_value_num';
				$args['meta_key'] = '_wc_average_rating'; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
				$args['order']    = 'DESC';
				break;
			case 'date':
				$args['orderby'] = 'date';
				$args['order']   = 'DESC';
				break;
			case 'title':
				$args['orderby'] = 'title';
				$args['order']   = 'ASC';
				break;
			default:
				$args['orderby'] = 'menu_order title';
				$args['order']   = 'ASC';
				break;
		}

		return $args;
	}
}
