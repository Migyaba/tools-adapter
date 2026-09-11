<?php
namespace ToolsAdapter;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Shared product card markup for Grid & Carousel.
 */
final class Product_Card {

	/**
	 * Render one product card.
	 *
	 * @param \WC_Product $product  Product object.
	 * @param array       $settings Display settings.
	 */
	public static function render( $product, array $settings = [] ) {
		if ( ! $product || ! is_a( $product, 'WC_Product' ) ) {
			return;
		}

		$show_image   = ( $settings['show_image'] ?? 'yes' ) === 'yes';
		$show_title   = ( $settings['show_title'] ?? 'yes' ) === 'yes';
		$show_price   = ( $settings['show_price'] ?? 'yes' ) === 'yes';
		$show_rating  = ( $settings['show_rating'] ?? '' ) === 'yes';
		$show_badge   = ( $settings['show_sale_badge'] ?? 'yes' ) === 'yes';
		$show_button  = ( $settings['show_add_to_cart'] ?? 'yes' ) === 'yes';
		$show_excerpt = ( $settings['show_excerpt'] ?? '' ) === 'yes';
		// The per-card toggle only takes effect if the "Vue rapide produit"
		// feature is also enabled in the Tools Adapter settings page.
		$show_quick_view = ( $settings['show_quick_view'] ?? '' ) === 'yes' && Admin_Settings::is_feature_enabled( 'quick_view' );
		$image_size   = $settings['image_size'] ?? 'woocommerce_thumbnail';
		$title_tag    = self::sanitize_tag( $settings['title_html_tag'] ?? 'h3' );

		$permalink = get_permalink( $product->get_id() );
		$classes   = [
			'ta-product-card',
			'product',
			'type-product',
			'post-' . $product->get_id(),
		];

		if ( $product->is_on_sale() ) {
			$classes[] = 'sale';
		}
		if ( ! $product->is_in_stock() ) {
			$classes[] = 'outofstock';
		}
		?>
		<article <?php wc_product_class( implode( ' ', $classes ), $product ); ?>>
			<div class="ta-product-card__inner">
				<?php if ( $show_image ) : ?>
					<a class="ta-product-card__media" href="<?php echo esc_url( $permalink ); ?>">
						<?php if ( $show_badge && $product->is_on_sale() ) : ?>
							<span class="ta-product-card__badge">
								<?php echo esc_html( \tools_adapter_translate( $settings['sale_badge_text'] ?? __( 'Promo', 'tools-adapter' ) ) ); ?>
							</span>
						<?php endif; ?>
						<?php echo $product->get_image( $image_size, [ 'class' => 'ta-product-card__image' ] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</a>
					<?php if ( $show_quick_view ) : ?>
						<button
							type="button"
							class="ta-product-card__quick-view"
							data-quick-view="<?php echo esc_attr( (string) $product->get_id() ); ?>"
							aria-label="<?php echo esc_attr__( 'Vue rapide', 'tools-adapter' ); ?>"
						>
							<i class="fas fa-eye" aria-hidden="true"></i>
						</button>
					<?php endif; ?>
				<?php endif; ?>

				<div class="ta-product-card__body">
					<?php if ( $show_title ) : ?>
						<<?php echo esc_attr( $title_tag ); ?> class="ta-product-card__title">
							<a href="<?php echo esc_url( $permalink ); ?>">
								<?php echo esc_html( $product->get_name() ); ?>
							</a>
						</<?php echo esc_attr( $title_tag ); ?>>
					<?php endif; ?>

					<?php if ( $show_rating && wc_review_ratings_enabled() ) : ?>
						<div class="ta-product-card__rating">
							<?php echo wc_get_rating_html( $product->get_average_rating() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</div>
					<?php endif; ?>

					<?php if ( $show_price ) : ?>
						<div class="ta-product-card__price">
							<?php echo $product->get_price_html(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</div>
					<?php endif; ?>

					<?php if ( $show_excerpt ) : ?>
						<div class="ta-product-card__excerpt">
							<?php echo wp_kses_post( wp_trim_words( $product->get_short_description() ?: get_the_excerpt( $product->get_id() ), 18 ) ); ?>
						</div>
					<?php endif; ?>

					<?php if ( $show_button ) : ?>
						<div class="ta-product-card__actions">
							<?php self::render_add_to_cart( $product, $settings ); ?>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</article>
		<?php
	}

	/**
	 * Add to cart / view product button.
	 *
	 * @param \WC_Product $product  Product.
	 * @param array       $settings Settings.
	 */
	private static function render_add_to_cart( $product, array $settings ) {
		if ( $product->is_purchasable() && $product->is_in_stock() && $product->is_type( 'simple' ) ) {
			$url   = $product->add_to_cart_url();
			$text  = ! empty( $settings['add_to_cart_text'] )
				? \tools_adapter_translate( $settings['add_to_cart_text'] )
				: $product->add_to_cart_text();
			$class = 'ta-product-card__btn add_to_cart_button ajax_add_to_cart product_type_simple';
			printf(
				'<a href="%1$s" data-quantity="1" class="%2$s" data-product_id="%3$d" data-product_sku="%4$s" aria-label="%5$s" rel="nofollow">%6$s</a>',
				esc_url( $url ),
				esc_attr( $class ),
				esc_attr( (string) $product->get_id() ),
				esc_attr( $product->get_sku() ),
				esc_attr( $product->add_to_cart_description() ),
				esc_html( $text )
			);
			return;
		}

		printf(
			'<a href="%1$s" class="ta-product-card__btn button">%2$s</a>',
			esc_url( get_permalink( $product->get_id() ) ),
			esc_html( \tools_adapter_translate( $settings['view_product_text'] ?: __( 'Voir le produit', 'tools-adapter' ) ) )
		);
	}

	/**
	 * Sanitize heading tag.
	 *
	 * @param string $tag Tag.
	 * @return string
	 */
	private static function sanitize_tag( $tag ) {
		$allowed = [ 'h2', 'h3', 'h4', 'h5', 'h6', 'div', 'p', 'span' ];
		return in_array( $tag, $allowed, true ) ? $tag : 'h3';
	}
}
