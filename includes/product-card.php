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

		$style = $settings['card_style'] ?? 'classic';
		if ( in_array( $style, [ 'modern', 'minimal', 'overlay', 'elevated' ], true ) ) {
			self::render_modern( $product, $settings, $style );
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
		// Heart button: per-widget toggle + global "Liste de souhaits" feature.
		$show_wishlist = ( $settings['show_wishlist'] ?? 'yes' ) === 'yes' && Admin_Settings::is_feature_enabled( 'wishlist' ) && class_exists( __NAMESPACE__ . '\\Wishlist' );
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
					<div class="ta-product-card__visual">
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
					<?php if ( $show_wishlist ) : ?>
						<?php echo Wishlist::button( $product->get_id(), 'ta-product-card__wishlist' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in Wishlist::button(). ?>
					<?php endif; ?>
					</div>
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
	 * "Moderne" card: hover image, icon actions on the image, add-to-cart
	 * revealed on hover (always visible on touch screens), category and
	 * percentage badge. No card frame.
	 *
	 * Variants (CSS class ta-product-card--style-{style}):
	 * - modern: add-to-cart revealed over the image;
	 * - minimal: title and price on one line, round add-to-cart icon;
	 * - overlay: details laid over the image, add-to-cart icon;
	 * - elevated: framed card, centered details, button always visible.
	 *
	 * @param \WC_Product $product  Product.
	 * @param array       $settings Settings.
	 * @param string      $style    Variant.
	 */
	private static function render_modern( $product, array $settings, $style = 'modern' ) {
		$show_image      = ( $settings['show_image'] ?? 'yes' ) === 'yes';
		$show_title      = ( $settings['show_title'] ?? 'yes' ) === 'yes';
		$show_price      = ( $settings['show_price'] ?? 'yes' ) === 'yes';
		$show_rating     = ( $settings['show_rating'] ?? '' ) === 'yes';
		$show_badge      = ( $settings['show_sale_badge'] ?? 'yes' ) === 'yes';
		$show_button     = ( $settings['show_add_to_cart'] ?? 'yes' ) === 'yes';
		$show_category   = ( $settings['show_category'] ?? 'yes' ) === 'yes';
		$show_quick_view = ( $settings['show_quick_view'] ?? '' ) === 'yes' && Admin_Settings::is_feature_enabled( 'quick_view' );
		$show_wishlist   = ( $settings['show_wishlist'] ?? 'yes' ) === 'yes' && Admin_Settings::is_feature_enabled( 'wishlist' ) && class_exists( __NAMESPACE__ . '\\Wishlist' );
		$image_size      = $settings['image_size'] ?? 'woocommerce_thumbnail';
		$title_tag       = self::sanitize_tag( $settings['title_html_tag'] ?? 'h3' );
		$permalink       = get_permalink( $product->get_id() );

		$hover_id = 0;
		if ( ( $settings['hover_image'] ?? 'yes' ) === 'yes' ) {
			$gallery  = $product->get_gallery_image_ids();
			$hover_id = $gallery ? (int) $gallery[0] : 0;
		}

		$badges = [];
		if ( $show_badge ) {
			if ( ! $product->is_in_stock() ) {
				$badges['out'] = __( 'Épuisé', 'tools-adapter' );
			} elseif ( $product->is_on_sale() ) {
				$percent        = self::sale_percentage( $product );
				$badges['sale'] = $percent ? '-' . $percent . '%' : \tools_adapter_translate( $settings['sale_badge_text'] ?? __( 'Promo', 'tools-adapter' ) );
			}
		}

		$icon_cart = in_array( $style, [ 'minimal', 'overlay' ], true );
		$classes   = [ 'ta-product-card', 'ta-product-card--modern', 'ta-product-card--style-' . $style, 'product', 'type-product', 'post-' . $product->get_id() ];
		if ( $hover_id ) {
			$classes[] = 'has-hover-image';
		}
		?>
		<article <?php wc_product_class( implode( ' ', $classes ), $product ); ?>>
			<div class="ta-product-card__inner">
				<?php if ( $show_image ) : ?>
					<div class="ta-product-card__visual">
						<a class="ta-product-card__media" href="<?php echo esc_url( $permalink ); ?>" tabindex="-1" aria-hidden="true">
							<?php
							echo $product->get_image( $image_size, [ 'class' => 'ta-product-card__image' ] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							if ( $hover_id ) {
								echo wp_get_attachment_image( $hover_id, $image_size, false, [ 'class' => 'ta-product-card__image ta-product-card__image--hover', 'alt' => '', 'loading' => 'lazy' ] );
							}
							?>
						</a>

						<?php if ( $badges ) : ?>
							<div class="ta-product-card__badges">
								<?php foreach ( $badges as $type => $label ) : ?>
									<span class="ta-product-card__badge ta-product-card__badge--<?php echo esc_attr( $type ); ?>"><?php echo esc_html( $label ); ?></span>
								<?php endforeach; ?>
							</div>
						<?php endif; ?>

						<?php if ( $show_wishlist || $show_quick_view || ( $icon_cart && $show_button ) ) : ?>
							<div class="ta-product-card__icons">
								<?php
								if ( $show_wishlist ) {
									echo Wishlist::button( $product->get_id() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in Wishlist::button().
								}
								if ( $show_quick_view ) :
									?>
									<button type="button" class="ta-product-card__icon" data-quick-view="<?php echo esc_attr( (string) $product->get_id() ); ?>" aria-label="<?php echo esc_attr__( 'Vue rapide', 'tools-adapter' ); ?>">
										<svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true" focusable="false"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg>
									</button>
									<?php
								endif;
								if ( $icon_cart && $show_button ) {
									self::render_add_to_cart_icon( $product );
								}
								?>
							</div>
						<?php endif; ?>

						<?php if ( $show_button && 'modern' === $style ) : ?>
							<div class="ta-product-card__reveal">
								<?php self::render_add_to_cart( $product, $settings ); ?>
							</div>
						<?php endif; ?>
					</div>
				<?php endif; ?>

				<div class="ta-product-card__body">
					<?php
					if ( $show_category ) {
						$terms = get_the_terms( $product->get_id(), 'product_cat' );
						if ( $terms && ! is_wp_error( $terms ) ) {
							$term = reset( $terms );
							printf( '<a class="ta-product-card__category" href="%1$s">%2$s</a>', esc_url( get_term_link( $term ) ), esc_html( $term->name ) );
						}
					}
					?>

					<?php if ( 'minimal' === $style ) : ?>
						<div class="ta-product-card__row">
					<?php endif; ?>

					<?php if ( $show_title ) : ?>
						<<?php echo esc_attr( $title_tag ); ?> class="ta-product-card__title">
							<a href="<?php echo esc_url( $permalink ); ?>"><?php echo esc_html( $product->get_name() ); ?></a>
						</<?php echo esc_attr( $title_tag ); ?>>
					<?php endif; ?>

					<?php if ( 'minimal' !== $style && $show_rating && wc_review_ratings_enabled() && $product->get_average_rating() ) : ?>
						<div class="ta-product-card__rating">
							<?php echo wc_get_rating_html( $product->get_average_rating() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</div>
					<?php endif; ?>

					<?php if ( $show_price ) : ?>
						<div class="ta-product-card__price">
							<?php echo $product->get_price_html(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</div>
					<?php endif; ?>

					<?php if ( 'minimal' === $style ) : ?>
						</div>
					<?php endif; ?>

					<?php if ( 'elevated' === $style && $show_button ) : ?>
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
	 * Round add-to-cart icon (AJAX for simple products, link otherwise).
	 *
	 * @param \WC_Product $product Product.
	 */
	private static function render_add_to_cart_icon( $product ) {
		$bag = '<svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true" focusable="false"><path d="M5 8h14l-1 13H6L5 8Z"/><path d="M9 8V6a3 3 0 0 1 6 0v2"/></svg>';

		if ( $product->is_purchasable() && $product->is_in_stock() && $product->is_type( 'simple' ) ) {
			printf(
				'<a href="%1$s" data-quantity="1" class="ta-product-card__icon ta-product-card__quick-add add_to_cart_button ajax_add_to_cart product_type_simple" data-product_id="%2$d" data-product_sku="%3$s" aria-label="%4$s" title="%4$s" rel="nofollow">%5$s</a>',
				esc_url( $product->add_to_cart_url() ),
				absint( $product->get_id() ),
				esc_attr( $product->get_sku() ),
				esc_attr( $product->add_to_cart_description() ),
				$bag // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG.
			);
			return;
		}

		printf(
			'<a href="%1$s" class="ta-product-card__icon ta-product-card__quick-add" aria-label="%2$s" title="%2$s">%3$s</a>',
			esc_url( $product->get_permalink() ),
			esc_attr__( 'Choisir les options', 'tools-adapter' ),
			$bag // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG.
		);
	}

	/**
	 * Highest discount percentage of a product on sale.
	 *
	 * @param \WC_Product $product Product.
	 * @return int
	 */
	private static function sale_percentage( $product ) {
		$pairs = [];
		if ( $product->is_type( 'variable' ) ) {
			$prices = $product->get_variation_prices();
			foreach ( $prices['regular_price'] as $id => $regular ) {
				$pairs[] = [ (float) $regular, (float) $prices['sale_price'][ $id ] ];
			}
		} else {
			$pairs[] = [ (float) $product->get_regular_price(), (float) $product->get_sale_price() ];
		}

		$max = 0;
		foreach ( $pairs as $pair ) {
			if ( $pair[0] > 0 && $pair[1] < $pair[0] ) {
				$max = max( $max, (int) round( ( $pair[0] - $pair[1] ) / $pair[0] * 100 ) );
			}
		}

		return $max;
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
