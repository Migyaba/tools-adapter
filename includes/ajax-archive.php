<?php
namespace ToolsAdapter;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Unified AJAX archive filtering (price + categories + pagination).
 */
final class Ajax_Archive {

	const ACTION = 'tools_adapter_filter_archive';
	const NONCE  = 'tools_adapter_archive';

	public function __construct() {
		add_action( 'wp_ajax_' . self::ACTION, [ $this, 'handle' ] );
		add_action( 'wp_ajax_nopriv_' . self::ACTION, [ $this, 'handle' ] );

		// Back-compat for price-filter JS still posting the old action.
		add_action( 'wp_ajax_tools_adapter_filter_products', [ $this, 'handle_legacy_price' ] );
		add_action( 'wp_ajax_nopriv_tools_adapter_filter_products', [ $this, 'handle_legacy_price' ] );
	}

	/**
	 * Legacy price-filter endpoint → same handler.
	 */
	public function handle_legacy_price() {
		check_ajax_referer( 'tools_adapter_price_filter', 'nonce' );
		$this->respond();
	}

	/**
	 * Main archive endpoint.
	 */
	public function handle() {
		check_ajax_referer( self::NONCE, 'nonce' );
		$this->respond();
	}

	/**
	 * Build query + HTML response.
	 */
	private function respond() {
		if ( ! class_exists( 'WooCommerce' ) ) {
			wp_send_json_error( [ 'message' => __( 'WooCommerce est requis.', 'tools-adapter' ) ], 400 );
		}

		$min_price    = isset( $_POST['min_price'] ) ? floatval( wp_unslash( $_POST['min_price'] ) ) : 0;
		$max_price    = isset( $_POST['max_price'] ) ? floatval( wp_unslash( $_POST['max_price'] ) ) : 0;
		$page         = isset( $_POST['page'] ) ? max( 1, intval( wp_unslash( $_POST['page'] ) ) ) : 1;
		$per_page     = isset( $_POST['per_page'] ) ? intval( wp_unslash( $_POST['per_page'] ) ) : 0;
		$orderby      = isset( $_POST['orderby'] ) ? sanitize_text_field( wp_unslash( $_POST['orderby'] ) ) : 'menu_order';
		$render_mode  = isset( $_POST['render_mode'] ) ? sanitize_key( wp_unslash( $_POST['render_mode'] ) ) : 'woocommerce';
		$category_ids = $this->parse_category_ids();
		$card_settings = $this->parse_card_settings();

		// Single taxonomy context (legacy price filter / archive page).
		$taxonomy = isset( $_POST['taxonomy'] ) ? sanitize_key( wp_unslash( $_POST['taxonomy'] ) ) : '';
		$term_id  = isset( $_POST['term_id'] ) ? intval( wp_unslash( $_POST['term_id'] ) ) : 0;

		if ( $per_page <= 0 ) {
			$per_page = (int) apply_filters(
				'loop_shop_per_page',
				function_exists( 'wc_get_default_products_per_row' )
					? wc_get_default_products_per_row() * wc_get_default_product_rows_per_page()
					: 12
			);
		}

		$query_args = [
			'post_type'           => 'product',
			'post_status'         => 'publish',
			'ignore_sticky_posts' => true,
			'posts_per_page'      => $per_page,
			'paged'               => $page,
			'meta_query'          => [], // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
			'tax_query'           => [ 'relation' => 'AND' ], // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
		];

		if ( function_exists( 'WC' ) && WC()->query ) {
			$query_args['meta_query'] = WC()->query->get_meta_query(); // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
			$query_args['tax_query']  = WC()->query->get_tax_query(); // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
		}

		// Price filter.
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

			$query_args['meta_query'][] = $price_meta; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
		}

		// Category IDs from AJAX category filter.
		if ( ! empty( $category_ids ) ) {
			$query_args['tax_query'][] = [ // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
				'taxonomy' => 'product_cat',
				'field'    => 'term_id',
				'terms'    => $category_ids,
				'operator' => 'IN',
			];
		} elseif ( $taxonomy && $term_id > 0 && taxonomy_exists( $taxonomy ) ) {
			$query_args['tax_query'][] = [ // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
				'taxonomy' => $taxonomy,
				'field'    => 'term_id',
				'terms'    => [ $term_id ],
			];
		}

		$query_args = $this->apply_ordering( $query_args, $orderby );
		$query_args = apply_filters( 'tools_adapter_archive_query_args', $query_args, $_POST );

		$products = new \WP_Query( $query_args );

		ob_start();
		if ( 'archive' === $render_mode ) {
			$this->render_archive_cards( $products, $card_settings );
		} else {
			$this->render_woocommerce_loop( $products );
		}
		$html = ob_get_clean();

		$pagination = '';
		if ( 'archive' === $render_mode && (int) $products->max_num_pages > 1 ) {
			$pagination = $this->render_pagination_html( (int) $products->max_num_pages, $page );
		}

		$result_count = sprintf(
			/* translators: %d: number of products found */
			_n( '%d produit trouvé', '%d produits trouvés', (int) $products->found_posts, 'tools-adapter' ),
			(int) $products->found_posts
		);

		wp_reset_postdata();

		wp_send_json_success(
			[
				'html'          => $html,
				'pagination'    => $pagination,
				'found'         => (int) $products->found_posts,
				'max_pages'     => (int) $products->max_num_pages,
				'page'          => $page,
				'result_count'  => $result_count,
				'min_price'     => $min_price,
				'max_price'     => $max_price,
				'category_ids'  => $category_ids,
				'render_mode'   => $render_mode,
			]
		);
	}

	/**
	 * @return int[]
	 */
	private function parse_category_ids() {
		$raw = isset( $_POST['category_ids'] ) ? wp_unslash( $_POST['category_ids'] ) : []; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput

		if ( is_string( $raw ) ) {
			$decoded = json_decode( $raw, true );
			if ( is_array( $decoded ) ) {
				$raw = $decoded;
			} else {
				$raw = preg_split( '/[\s,]+/', $raw );
			}
		}

		if ( ! is_array( $raw ) ) {
			return [];
		}

		return array_values( array_filter( array_map( 'absint', $raw ) ) );
	}

	/**
	 * @return array
	 */
	private function parse_card_settings() {
		$raw = isset( $_POST['card_settings'] ) ? wp_unslash( $_POST['card_settings'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
		if ( is_string( $raw ) && $raw !== '' ) {
			$decoded = json_decode( $raw, true );
			if ( is_array( $decoded ) ) {
				return $decoded;
			}
		}
		return [
			'show_image'       => 'yes',
			'show_title'       => 'yes',
			'show_price'       => 'yes',
			'show_rating'      => '',
			'show_sale_badge'  => 'yes',
			'show_add_to_cart' => 'yes',
			'show_excerpt'     => '',
			'image_size'       => 'woocommerce_thumbnail',
			'title_html_tag'   => 'h3',
			'sale_badge_text'  => __( 'Promo', 'tools-adapter' ),
			'add_to_cart_text' => __( 'Ajouter au panier', 'tools-adapter' ),
			'view_product_text'=> __( 'Voir le produit', 'tools-adapter' ),
		];
	}

	/**
	 * @param \WP_Query $products Products query.
	 * @param array     $settings Card settings.
	 */
	private function render_archive_cards( $products, array $settings ) {
		if ( ! $products->have_posts() ) {
			echo '<p class="ta-products-empty">' . esc_html__( 'Aucun produit ne correspond à vos filtres.', 'tools-adapter' ) . '</p>';
			return;
		}

		while ( $products->have_posts() ) {
			$products->the_post();
			$product = wc_get_product( get_the_ID() );
			if ( $product ) {
				Product_Card::render( $product, $settings );
			}
		}
	}

	/**
	 * @param \WP_Query $products Products query.
	 */
	private function render_woocommerce_loop( $products ) {
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
	}

	/**
	 * @param int $max_pages Max pages.
	 * @param int $current   Current page.
	 * @return string
	 */
	private function render_pagination_html( $max_pages, $current ) {
		ob_start();
		echo '<nav class="ta-archive__pagination" aria-label="' . esc_attr__( 'Pagination produits', 'tools-adapter' ) . '">';
		for ( $i = 1; $i <= $max_pages; $i++ ) {
			printf(
				'<button type="button" class="ta-archive__page%1$s" data-page="%2$d">%2$d</button>',
				$i === $current ? ' is-active' : '',
				$i
			);
		}
		echo '</nav>';
		return ob_get_clean();
	}

	/**
	 * @param array  $args    Query args.
	 * @param string $orderby Orderby.
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
