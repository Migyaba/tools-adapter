<?php
namespace ToolsAdapter;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Shared pagination renderer for Product Archive.
 *
 * Used both for the initial PHP render and the AJAX response so every
 * pagination type stays perfectly in sync between first paint and
 * subsequent filtering/sorting/page changes.
 *
 * Supported types:
 * - numbers    Classic numbered pagination with ellipsis + optional prev/next arrows.
 * - prev_next  Simple previous/next buttons with a "X / Y" status.
 * - load_more  A single "Load more" button that appends the next page.
 * - infinite   Auto-loads the next page when a sentinel scrolls into view.
 */
final class Pagination {

	const TYPES = [ 'numbers', 'prev_next', 'load_more', 'infinite' ];

	/**
	 * Default pagination settings.
	 *
	 * @return array
	 */
	public static function default_settings() {
		return [
			'type'            => 'numbers',
			'show_prev_next'  => 'yes',
			'end_size'        => 1,
			'mid_size'        => 2,
			'prev_text'       => '‹',
			'next_text'       => '›',
			'load_more_text'  => __( 'Charger plus', 'tools-adapter' ),
			'loading_text'    => __( 'Chargement…', 'tools-adapter' ),
			'show_progress'   => 'yes',
			/* translators: 1: number of products shown, 2: total number of products. */
			'progress_format' => __( '%1$d sur %2$d produits affichés', 'tools-adapter' ),
			'infinite_offset' => 300,
		];
	}

	/**
	 * Normalize pagination settings coming from Elementor controls or an
	 * AJAX POST payload (both pass loosely-typed / string values).
	 *
	 * @param array $raw Raw settings.
	 * @return array
	 */
	public static function parse_settings( array $raw ) {
		$defaults = self::default_settings();
		$type     = in_array( $raw['type'] ?? '', self::TYPES, true ) ? $raw['type'] : $defaults['type'];

		return [
			'type'            => $type,
			'show_prev_next'  => ( 'yes' === ( $raw['show_prev_next'] ?? $defaults['show_prev_next'] ) ) ? 'yes' : '',
			'end_size'        => max( 1, min( 5, intval( $raw['end_size'] ?? $defaults['end_size'] ) ) ),
			'mid_size'        => max( 0, min( 5, intval( $raw['mid_size'] ?? $defaults['mid_size'] ) ) ),
			'prev_text'       => self::sanitize_label( $raw['prev_text'] ?? $defaults['prev_text'] ),
			'next_text'       => self::sanitize_label( $raw['next_text'] ?? $defaults['next_text'] ),
			'load_more_text'  => self::sanitize_label( $raw['load_more_text'] ?? $defaults['load_more_text'] ),
			'loading_text'    => self::sanitize_label( $raw['loading_text'] ?? $defaults['loading_text'] ),
			'show_progress'   => ( 'yes' === ( $raw['show_progress'] ?? $defaults['show_progress'] ) ) ? 'yes' : '',
			'progress_format' => self::sanitize_label( $raw['progress_format'] ?? $defaults['progress_format'] ),
			'infinite_offset' => max( 0, min( 2000, intval( $raw['infinite_offset'] ?? $defaults['infinite_offset'] ) ) ),
		];
	}

	/**
	 * @param mixed $value Raw label value.
	 * @return string
	 */
	private static function sanitize_label( $value ) {
		return is_string( $value ) ? $value : '';
	}

	/**
	 * Render the pagination markup for the given state + settings.
	 *
	 * @param array $args {
	 *     @type int   $current   Current page (1-based).
	 *     @type int   $max_pages Total number of pages.
	 *     @type int   $found     Total number of items found.
	 *     @type int   $per_page  Items per page.
	 *     @type array $settings  Parsed settings, see parse_settings().
	 * }
	 * @return string HTML.
	 */
	public static function render( array $args ) {
		$current   = max( 1, (int) ( $args['current'] ?? 1 ) );
		$max_pages = max( 0, (int) ( $args['max_pages'] ?? 0 ) );
		$found     = max( 0, (int) ( $args['found'] ?? 0 ) );
		$per_page  = max( 1, (int) ( $args['per_page'] ?? 12 ) );
		$settings  = self::parse_settings( $args['settings'] ?? [] );

		switch ( $settings['type'] ) {
			case 'prev_next':
				return self::render_prev_next( $current, $max_pages, $settings );
			case 'load_more':
				return self::render_load_more( $current, $max_pages, $found, $per_page, $settings );
			case 'infinite':
				return self::render_infinite( $current, $max_pages, $found, $per_page, $settings );
			case 'numbers':
			default:
				return self::render_numbers( $current, $max_pages, $settings );
		}
	}

	/**
	 * Classic numbered pagination with ellipsis (mirrors paginate_links()).
	 *
	 * @param int   $current   Current page.
	 * @param int   $max_pages Total pages.
	 * @param array $settings  Parsed settings.
	 * @return string
	 */
	private static function render_numbers( $current, $max_pages, array $settings ) {
		if ( $max_pages <= 1 ) {
			return '';
		}

		$end_size = $settings['end_size'];
		$mid_size = $settings['mid_size'];

		ob_start();
		echo '<nav class="ta-archive__pagination ta-archive__pagination--numbers" aria-label="' . esc_attr__( 'Pagination produits', 'tools-adapter' ) . '">';

		if ( 'yes' === $settings['show_prev_next'] ) {
			$prev_page  = max( 1, $current - 1 );
			$is_disabled = ( 1 === $current );
			printf(
				'<button type="button" class="ta-archive__page ta-archive__page--prev%1$s" data-page="%2$d"%3$s aria-label="%4$s">%5$s</button>',
				$is_disabled ? ' is-disabled' : '',
				$prev_page,
				$is_disabled ? ' disabled' : '',
				esc_attr__( 'Page précédente', 'tools-adapter' ),
				esc_html( $settings['prev_text'] )
			);
		}

		$dots_open = false;
		for ( $i = 1; $i <= $max_pages; $i++ ) {
			$show = ( $i <= $end_size )
				|| ( $i > $max_pages - $end_size )
				|| ( $i >= $current - $mid_size && $i <= $current + $mid_size );

			if ( ! $show ) {
				if ( ! $dots_open ) {
					echo '<span class="ta-archive__page-dots" aria-hidden="true">&hellip;</span>';
					$dots_open = true;
				}
				continue;
			}

			$dots_open = false;
			printf(
				'<button type="button" class="ta-archive__page%1$s" data-page="%2$d"%3$s>%2$d</button>',
				$i === $current ? ' is-active' : '',
				$i,
				$i === $current ? ' aria-current="page"' : ''
			);
		}

		if ( 'yes' === $settings['show_prev_next'] ) {
			$next_page  = min( $max_pages, $current + 1 );
			$is_disabled = ( $current === $max_pages );
			printf(
				'<button type="button" class="ta-archive__page ta-archive__page--next%1$s" data-page="%2$d"%3$s aria-label="%4$s">%5$s</button>',
				$is_disabled ? ' is-disabled' : '',
				$next_page,
				$is_disabled ? ' disabled' : '',
				esc_attr__( 'Page suivante', 'tools-adapter' ),
				esc_html( $settings['next_text'] )
			);
		}

		echo '</nav>';
		return ob_get_clean();
	}

	/**
	 * @param int   $current   Current page.
	 * @param int   $max_pages Total pages.
	 * @param array $settings  Parsed settings.
	 * @return string
	 */
	private static function render_prev_next( $current, $max_pages, array $settings ) {
		if ( $max_pages <= 1 ) {
			return '';
		}

		$prev_page = max( 1, $current - 1 );
		$next_page = min( $max_pages, $current + 1 );

		ob_start();
		?>
		<nav class="ta-archive__pagination ta-archive__pagination--prev-next" aria-label="<?php echo esc_attr__( 'Pagination produits', 'tools-adapter' ); ?>">
			<button
				type="button"
				class="ta-archive__page ta-archive__page--prev<?php echo 1 === $current ? ' is-disabled' : ''; ?>"
				data-page="<?php echo esc_attr( (string) $prev_page ); ?>"
				<?php echo 1 === $current ? 'disabled' : ''; ?>
			><?php echo esc_html( $settings['prev_text'] ); ?></button>
			<span class="ta-archive__page-status"><?php echo esc_html( sprintf( '%1$d / %2$d', $current, $max_pages ) ); ?></span>
			<button
				type="button"
				class="ta-archive__page ta-archive__page--next<?php echo $current === $max_pages ? ' is-disabled' : ''; ?>"
				data-page="<?php echo esc_attr( (string) $next_page ); ?>"
				<?php echo $current === $max_pages ? 'disabled' : ''; ?>
			><?php echo esc_html( $settings['next_text'] ); ?></button>
		</nav>
		<?php
		return ob_get_clean();
	}

	/**
	 * @param int   $current   Current page.
	 * @param int   $max_pages Total pages.
	 * @param int   $found     Total items found.
	 * @param int   $per_page  Items per page.
	 * @param array $settings  Parsed settings.
	 * @return string
	 */
	private static function render_load_more( $current, $max_pages, $found, $per_page, array $settings ) {
		$has_more = ( $current < $max_pages );
		$shown    = min( $found, $current * $per_page );

		ob_start();
		printf( '<div class="ta-archive__load-more" data-load-more-wrap data-has-more="%s">', $has_more ? '1' : '0' );
		if ( $has_more ) {
			printf(
				'<button type="button" class="ta-archive__load-more-btn" data-load-more data-page="%1$d" data-loading-text="%2$s">%3$s</button>',
				(int) ( $current + 1 ),
				esc_attr( $settings['loading_text'] ),
				esc_html( $settings['load_more_text'] )
			);
		}
		if ( 'yes' === $settings['show_progress'] && $found > 0 ) {
			printf(
				'<p class="ta-archive__load-more-progress">%s</p>',
				esc_html( sprintf( $settings['progress_format'], $shown, $found ) )
			);
		}
		echo '</div>';
		return ob_get_clean();
	}

	/**
	 * @param int   $current   Current page.
	 * @param int   $max_pages Total pages.
	 * @param int   $found     Total items found.
	 * @param int   $per_page  Items per page.
	 * @param array $settings  Parsed settings.
	 * @return string
	 */
	private static function render_infinite( $current, $max_pages, $found, $per_page, array $settings ) {
		$has_more = ( $current < $max_pages );
		$shown    = min( $found, $current * $per_page );

		ob_start();
		?>
		<div
			class="ta-archive__infinite"
			data-infinite="1"
			data-has-more="<?php echo $has_more ? '1' : '0'; ?>"
			data-next-page="<?php echo esc_attr( (string) ( $current + 1 ) ); ?>"
			data-offset="<?php echo esc_attr( (string) $settings['infinite_offset'] ); ?>"
		>
			<?php if ( $has_more ) : ?>
				<div class="ta-archive__infinite-sentinel" data-sentinel aria-hidden="true"></div>
				<div class="ta-archive__infinite-loader" data-loader hidden><?php echo esc_html( $settings['loading_text'] ); ?></div>
			<?php elseif ( 'yes' === $settings['show_progress'] && $found > 0 ) : ?>
				<p class="ta-archive__load-more-progress"><?php echo esc_html( sprintf( $settings['progress_format'], $shown, $found ) ); ?></p>
			<?php endif; ?>
		</div>
		<?php
		return ob_get_clean();
	}
}
