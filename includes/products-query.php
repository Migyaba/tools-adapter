<?php
namespace ToolsAdapter;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Shared WooCommerce product query builder for Grid & Carousel widgets.
 */
final class Products_Query {

	/**
	 * Source options for Elementor SELECT control.
	 *
	 * @return array<string, string>
	 */
	public static function get_source_options() {
		return [
			'recent'      => esc_html__( 'Produits récents', 'tools-adapter' ),
			'featured'    => esc_html__( 'Mis en avant', 'tools-adapter' ),
			'sale'        => esc_html__( 'En promotion', 'tools-adapter' ),
			'best_selling'=> esc_html__( 'Best-sellers', 'tools-adapter' ),
			'top_rated'   => esc_html__( 'Mieux notés', 'tools-adapter' ),
			'category'    => esc_html__( 'Par catégorie', 'tools-adapter' ),
			'manual'      => esc_html__( 'Sélection manuelle (IDs)', 'tools-adapter' ),
		];
	}

	/**
	 * Build WP_Query args from widget settings.
	 *
	 * @param array $settings Elementor settings.
	 * @return array
	 */
	public static function build_args( array $settings ) {
		$limit  = max( 1, intval( $settings['posts_per_page'] ?? 8 ) );
		$source = $settings['source'] ?? 'recent';
		$order  = ( $settings['order'] ?? 'DESC' ) === 'ASC' ? 'ASC' : 'DESC';

		$args = [
			'post_type'           => 'product',
			'post_status'         => 'publish',
			'ignore_sticky_posts' => true,
			'posts_per_page'      => $limit,
			'order'               => $order,
			'meta_query'          => [], // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
			'tax_query'           => [ // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
				'relation' => 'AND',
			],
		];

		// Hide out of stock if requested.
		if ( 'yes' === ( $settings['hide_out_of_stock'] ?? '' ) ) {
			$args['meta_query'][] = [
				'key'     => '_stock_status',
				'value'   => 'instock',
				'compare' => '=',
			];
		}

		switch ( $source ) {
			case 'featured':
				$args['tax_query'][] = [
					'taxonomy' => 'product_visibility',
					'field'    => 'name',
					'terms'    => [ 'featured' ],
					'operator' => 'IN',
				];
				$args['orderby'] = 'date';
				break;

			case 'sale':
				$sale_ids = function_exists( 'wc_get_product_ids_on_sale' ) ? wc_get_product_ids_on_sale() : [];
				$sale_ids = array_filter( array_map( 'absint', $sale_ids ) );
				$args['post__in'] = $sale_ids ? $sale_ids : [ 0 ];
				$args['orderby']  = 'post__in' === ( $settings['orderby'] ?? '' ) ? 'post__in' : 'date';
				break;

			case 'best_selling':
				$args['meta_key'] = 'total_sales'; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
				$args['orderby']  = 'meta_value_num';
				$args['order']    = 'DESC';
				break;

			case 'top_rated':
				$args['meta_key'] = '_wc_average_rating'; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
				$args['orderby']  = 'meta_value_num';
				$args['order']    = 'DESC';
				break;

			case 'category':
				$cat_ids = self::parse_ids( $settings['category_ids'] ?? '' );
				if ( $cat_ids ) {
					$args['tax_query'][] = [
						'taxonomy' => 'product_cat',
						'field'    => 'term_id',
						'terms'    => $cat_ids,
						'operator' => 'IN',
					];
				}
				$args = self::apply_orderby( $args, $settings['orderby'] ?? 'date', $order );
				break;

			case 'manual':
				$ids = self::parse_ids( $settings['product_ids'] ?? '' );
				$args['post__in'] = $ids ? $ids : [ 0 ];
				$args['orderby']  = 'post__in';
				break;

			case 'recent':
			default:
				$args = self::apply_orderby( $args, $settings['orderby'] ?? 'date', $order );
				break;
		}

		// Optional extra category filter for non-category sources.
		if ( 'category' !== $source && ! empty( $settings['filter_category_ids'] ) ) {
			$extra = self::parse_ids( $settings['filter_category_ids'] );
			if ( $extra ) {
				$args['tax_query'][] = [
					'taxonomy' => 'product_cat',
					'field'    => 'term_id',
					'terms'    => $extra,
					'operator' => 'IN',
				];
			}
		}

		// Exclude IDs.
		if ( ! empty( $settings['exclude_ids'] ) ) {
			$exclude = self::parse_ids( $settings['exclude_ids'] );
			if ( $exclude ) {
				$args['post__not_in'] = $exclude; // phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_post__not_in
			}
		}

		return apply_filters( 'tools_adapter_products_query_args', $args, $settings );
	}

	/**
	 * Run query.
	 *
	 * @param array $settings Widget settings.
	 * @return \WP_Query
	 */
	public static function query( array $settings ) {
		return new \WP_Query( self::build_args( $settings ) );
	}

	/**
	 * Apply orderby helpers.
	 *
	 * @param array  $args    Query args.
	 * @param string $orderby Orderby key.
	 * @param string $order   ASC|DESC.
	 * @return array
	 */
	private static function apply_orderby( array $args, $orderby, $order ) {
		switch ( $orderby ) {
			case 'price':
				$args['meta_key'] = '_price'; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
				$args['orderby']  = 'meta_value_num';
				$args['order']    = $order;
				break;
			case 'popularity':
				$args['meta_key'] = 'total_sales'; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
				$args['orderby']  = 'meta_value_num';
				$args['order']    = 'DESC';
				break;
			case 'rating':
				$args['meta_key'] = '_wc_average_rating'; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
				$args['orderby']  = 'meta_value_num';
				$args['order']    = 'DESC';
				break;
			case 'title':
				$args['orderby'] = 'title';
				$args['order']   = $order;
				break;
			case 'rand':
				$args['orderby'] = 'rand';
				break;
			case 'menu_order':
				$args['orderby'] = 'menu_order title';
				$args['order']   = $order;
				break;
			case 'date':
			default:
				$args['orderby'] = 'date';
				$args['order']   = $order;
				break;
		}

		return $args;
	}

	/**
	 * Parse comma-separated IDs.
	 *
	 * @param string $raw Raw string.
	 * @return int[]
	 */
	public static function parse_ids( $raw ) {
		if ( empty( $raw ) ) {
			return [];
		}

		return array_values(
			array_filter(
				array_map( 'absint', preg_split( '/[\s,]+/', (string) $raw ) )
			)
		);
	}

	/**
	 * Resolve a category's display image: its own WooCommerce thumbnail,
	 * or (optionally) a fallback picked from one of its products.
	 *
	 * @param int    $term_id  Category term ID.
	 * @param bool   $fallback Whether to fall back to a product image.
	 * @param string $order    'recent' or 'random' — fallback product order.
	 * @return int Attachment ID, or 0 if none found.
	 */
	public static function get_category_image_id( $term_id, $fallback = true, $order = 'recent' ) {
		$term_id = absint( $term_id );
		if ( ! $term_id ) {
			return 0;
		}

		$thumbnail_id = absint( get_term_meta( $term_id, 'thumbnail_id', true ) );
		if ( $thumbnail_id || ! $fallback ) {
			return $thumbnail_id;
		}

		$product_ids = get_posts(
			[
				'post_type'           => 'product',
				'post_status'         => 'publish',
				'posts_per_page'      => 1,
				'fields'              => 'ids',
				'ignore_sticky_posts'  => true,
				'no_found_rows'       => true,
				'orderby'             => ( 'random' === $order ) ? 'rand' : 'date',
				'order'               => 'DESC',
				'tax_query'           => [ // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
					[
						'taxonomy' => 'product_cat',
						'field'    => 'term_id',
						'terms'    => [ $term_id ],
					],
				],
				'meta_query'          => [ // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
					[
						'key'     => '_thumbnail_id',
						'compare' => 'EXISTS',
					],
				],
			]
		);

		if ( empty( $product_ids ) ) {
			return 0;
		}

		return absint( get_post_thumbnail_id( $product_ids[0] ) );
	}

	/**
	 * Product category options for SELECT2-like text help (ID list).
	 *
	 * @return array<int, string>
	 */
	public static function get_category_options() {
		if ( ! taxonomy_exists( 'product_cat' ) ) {
			return [];
		}

		$terms = get_terms(
			[
				'taxonomy'   => 'product_cat',
				'hide_empty' => false,
			]
		);

		if ( empty( $terms ) || is_wp_error( $terms ) ) {
			return [];
		}

		$options = [];
		foreach ( $terms as $term ) {
			$options[ $term->term_id ] = $term->name;
		}

		return $options;
	}

	/**
	 * List of registered global WooCommerce product attributes (taxonomy slug => label).
	 * Used by the "Filtre par attributs" widget to let the admin pick which
	 * attribute (color, size…) a given filter instance should expose.
	 *
	 * @return array<string, string>
	 */
	public static function get_attribute_taxonomy_options() {
		if ( ! function_exists( 'wc_get_attribute_taxonomies' ) ) {
			return [];
		}

		$options = [];
		foreach ( (array) wc_get_attribute_taxonomies() as $attribute ) {
			$taxonomy = wc_attribute_taxonomy_name( $attribute->attribute_name );
			if ( taxonomy_exists( $taxonomy ) ) {
				$options[ $taxonomy ] = $attribute->attribute_label;
			}
		}

		return $options;
	}
}
