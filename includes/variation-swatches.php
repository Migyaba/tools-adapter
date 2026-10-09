<?php
namespace ToolsAdapter;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Remplace les listes déroulantes de variations WooCommerce par des
 * swatches visuels (pastilles de couleur ou pilules de texte), tout en
 * conservant le <select> natif (masqué) pour rester 100% compatible avec
 * le script de variations de WooCommerce.
 */
final class Variation_Swatches {

	/**
	 * Table de correspondance nom de couleur → code hexadécimal (FR + EN).
	 *
	 * @var array<string, string>
	 */
	private static $color_map = [
		'noir'      => '#000000',
		'black'     => '#000000',
		'blanc'     => '#ffffff',
		'white'     => '#ffffff',
		'gris'      => '#8c8c8c',
		'grey'      => '#8c8c8c',
		'gray'      => '#8c8c8c',
		'rouge'     => '#d62828',
		'red'       => '#d62828',
		'bleu'      => '#1d4e89',
		'blue'      => '#1d4e89',
		'marine'    => '#0b2545',
		'navy'      => '#0b2545',
		'vert'      => '#2e8b57',
		'green'     => '#2e8b57',
		'jaune'     => '#f2c14e',
		'yellow'    => '#f2c14e',
		'orange'    => '#e8772e',
		'rose'      => '#e8a0bf',
		'pink'      => '#e8a0bf',
		'violet'    => '#7d5ba6',
		'purple'    => '#7d5ba6',
		'marron'    => '#6b4226',
		'brown'     => '#6b4226',
		'beige'     => '#e8dcc8',
		'kaki'      => '#7c7a52',
		'khaki'     => '#7c7a52',
		'or'        => '#c9a84c',
		'gold'      => '#c9a84c',
		'argent'    => '#c0c0c0',
		'silver'    => '#c0c0c0',
		'multicolore' => 'linear-gradient(135deg,#d62828,#f2c14e,#2e8b57,#1d4e89)',
	];

	public function __construct() {
		add_filter( 'woocommerce_dropdown_variation_attribute_options_html', [ $this, 'render_swatches' ], 20, 2 );
	}

	/**
	 * Intercept the default <select> markup and append swatch buttons after it.
	 *
	 * @param string $html Original select markup.
	 * @param array  $args Args passed to wc_dropdown_variation_attribute_options().
	 * @return string
	 */
	public function render_swatches( $html, $args ) {
		if ( empty( $args['options'] ) || empty( $args['attribute'] ) ) {
			return $html;
		}

		$attribute = $args['attribute'];
		$is_color  = (bool) preg_match( '/(color|colour|couleur)/i', $attribute );
		$selected  = $args['selected'] ?? '';
		// WooCommerce passes an empty `id` by default and then falls back to the sanitized attribute.
		$select_id = ! empty( $args['id'] ) ? $args['id'] : sanitize_title( $attribute );

		$product   = $args['product'] ?? null;
		$is_taxonomy = 0 === strpos( $attribute, 'attribute_pa_' ) || ( $product && taxonomy_exists( wc_attribute_taxonomy_name( str_replace( 'attribute_', '', $attribute ) ) ) );
		$taxonomy    = $is_taxonomy ? wc_attribute_taxonomy_name( str_replace( 'attribute_', '', $attribute ) ) : '';

		ob_start();
		?>
		<div class="ta-swatches" data-ta-swatches data-select="<?php echo esc_attr( $select_id ); ?>" data-color="<?php echo $is_color ? '1' : '0'; ?>">
			<?php foreach ( $args['options'] as $option ) : ?>
				<?php
				$value = $option;
				$label = $option;

				if ( $taxonomy ) {
					$term = get_term_by( 'slug', $option, $taxonomy );
					if ( $term ) {
						$label = $term->name;
					}
				} else {
					$label = apply_filters( 'woocommerce_variation_option_name', $option, null, $attribute, $product );
				}

				$is_selected = ( $selected === $value );
				$hex         = $is_color ? $this->guess_color( $label ) : '';
				?>
				<button
					type="button"
					class="ta-swatch <?php echo $is_color ? 'ta-swatch--color' : 'ta-swatch--text'; ?><?php echo $is_selected ? ' is-active' : ''; ?>"
					data-value="<?php echo esc_attr( $value ); ?>"
					aria-pressed="<?php echo $is_selected ? 'true' : 'false'; ?>"
					title="<?php echo esc_attr( $label ); ?>"
					<?php if ( $hex ) : ?>style="background: <?php echo esc_attr( $hex ); ?>;"<?php endif; ?>
				>
					<?php if ( ! $is_color ) : ?>
						<span><?php echo esc_html( $label ); ?></span>
					<?php else : ?>
						<span class="screen-reader-text"><?php echo esc_html( $label ); ?></span>
					<?php endif; ?>
				</button>
			<?php endforeach; ?>
		</div>
		<?php
		$swatches_html = ob_get_clean();

		// Keep the native <select> (visually hidden via CSS) so WooCommerce's
		// variation-matching script keeps working exactly as before.
		return '<span class="ta-swatches-select-wrap">' . $html . '</span>' . $swatches_html;
	}

	/**
	 * Best-effort color name → CSS color/gradient lookup.
	 *
	 * @param string $label Term / option label.
	 * @return string
	 */
	private function guess_color( $label ) {
		$key = strtolower( trim( wp_strip_all_tags( $label ) ) );
		if ( isset( self::$color_map[ $key ] ) ) {
			return self::$color_map[ $key ];
		}
		foreach ( self::$color_map as $name => $hex ) {
			if ( false !== strpos( $key, $name ) ) {
				return $hex;
			}
		}
		// Last resort: let the browser try to interpret it as a CSS color keyword.
		return preg_match( '/^[a-z]+$/', $key ) ? $key : '';
	}
}
