<?php
namespace ToolsAdapter\HeaderFooter;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Frontend rendering and theme integration engine for Header & Footer templates.
 */
class Render {

	/**
	 * Cached resolved template IDs for the current request.
	 *
	 * @var array<string,int|null>
	 */
	private $resolved_templates = [
		'header'        => null,
		'footer'        => null,
		'before_footer' => null,
	];

	/**
	 * Flags to ensure templates are rendered at most once per request.
	 *
	 * @var array<string,bool>
	 */
	private $rendered = [
		'header'        => false,
		'footer'        => false,
		'before_footer' => false,
	];

	public function __construct() {
		add_action( 'wp', [ $this, 'resolve_active_templates' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_template_assets' ] );
		add_action( 'wp_head', [ $this, 'print_theme_override_css' ] );

		// Register Elementor Locations (Hello Elementor & theme location API).
		add_action( 'elementor/theme/register_locations', [ $this, 'register_elementor_locations' ] );
		add_action( 'elementor/theme/header', [ $this, 'render_elementor_header_location' ] );
		add_action( 'elementor/theme/footer', [ $this, 'render_elementor_footer_location' ] );

		// Theme-specific hooks (Astra, GeneratePress, OceanWP, Storefront).
		add_action( 'wp', [ $this, 'setup_theme_hooks' ], 20 );

		// Universal Fallback hooks.
		add_action( 'wp_body_open', [ $this, 'render_header_fallback' ], 1 );
		add_action( 'wp_footer', [ $this, 'render_footer_fallback' ], 1 );

		// Shortcodes.
		add_shortcode( 'tools_adapter_template', [ $this, 'render_shortcode' ] );
		add_shortcode( 'ta_header_footer', [ $this, 'render_shortcode' ] );
	}

	/**
	 * Resolve which templates match the current request.
	 */
	public function resolve_active_templates() {
		if ( is_admin() ) {
			return;
		}

		// Don't override if currently editing with Elementor.
		if ( class_exists( '\Elementor\Plugin' ) && \Elementor\Plugin::$instance->preview->is_preview_mode() ) {
			$preview_id = (int) get_the_ID();
			$post_type  = get_post_type( $preview_id );
			if ( Post_Type::POST_TYPE === $post_type ) {
				// We are editing a Header/Footer template itself: do not loop-inject.
				return;
			}
		}

		$templates = get_posts( [
			'post_type'      => Post_Type::POST_TYPE,
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'fields'         => 'ids',
		] );

		if ( empty( $templates ) ) {
			return;
		}

		foreach ( [ 'header', 'footer', 'before_footer' ] as $type ) {
			foreach ( $templates as $post_id ) {
				$template_type = get_post_meta( $post_id, '_ta_hf_template_type', true ) ?: 'header';
				if ( $template_type !== $type ) {
					continue;
				}

				if ( $this->should_render( $post_id ) ) {
					$this->resolved_templates[ $type ] = $post_id;
					break; // Found the highest priority matching template for this type.
				}
			}
		}
	}

	/**
	 * Check whether a given template ID meets display, exclusion and user role rules.
	 *
	 * @param int $post_id Template ID.
	 * @return bool
	 */
	public function should_render( $post_id ) {
		$status = get_post_meta( $post_id, '_ta_hf_status', true ) ?: 'active';
		if ( 'inactive' === $status ) {
			return false;
		}

		// 1. User role condition.
		$user_role = get_post_meta( $post_id, '_ta_hf_user_roles', true ) ?: 'all';
		if ( 'logged_in' === $user_role && ! is_user_logged_in() ) {
			return false;
		}
		if ( 'logged_out' === $user_role && is_user_logged_in() ) {
			return false;
		}

		$current_id = (int) get_queried_object_id();

		// 2. Exclusion condition.
		$exclude_on = get_post_meta( $post_id, '_ta_hf_exclude_on', true ) ?: 'none';
		if ( 'none' !== $exclude_on ) {
			if ( 'home' === $exclude_on && ( is_front_page() || is_home() ) ) {
				return false;
			}
			if ( '404' === $exclude_on && is_404() ) {
				return false;
			}
			if ( 'woocommerce' === $exclude_on && $this->is_woocommerce_page() ) {
				return false;
			}
			if ( 'specific' === $exclude_on ) {
				$exclude_ids = array_map( 'intval', array_map( 'trim', explode( ',', get_post_meta( $post_id, '_ta_hf_exclude_ids', true ) ) ) );
				if ( in_array( $current_id, $exclude_ids, true ) ) {
					return false;
				}
			}
		}

		// 3. Inclusion condition.
		$display_on = get_post_meta( $post_id, '_ta_hf_display_on', true ) ?: 'entire_site';
		switch ( $display_on ) {
			case 'entire_site':
				return true;

			case 'home':
				return is_front_page() || is_home();

			case 'all_pages':
				return is_page();

			case 'all_posts':
				return is_single() && 'post' === get_post_type();

			case 'archives':
				return is_archive() || is_category() || is_tag() || is_date() || is_author();

			case 'woocommerce':
				return $this->is_woocommerce_page();

			case '404':
				return is_404();

			case 'specific':
				$specific_ids = array_map( 'intval', array_map( 'trim', explode( ',', get_post_meta( $post_id, '_ta_hf_specific_ids', true ) ) ) );
				return in_array( $current_id, $specific_ids, true );

			default:
				return true;
		}
	}

	/**
	 * Check if the current page belongs to WooCommerce.
	 *
	 * @return bool
	 */
	private function is_woocommerce_page() {
		if ( ! class_exists( 'WooCommerce' ) ) {
			return false;
		}
		if ( function_exists( 'is_woocommerce' ) && is_woocommerce() ) {
			return true;
		}
		if ( function_exists( 'is_cart' ) && is_cart() ) {
			return true;
		}
		if ( function_exists( 'is_checkout' ) && is_checkout() ) {
			return true;
		}
		if ( function_exists( 'is_account_page' ) && is_account_page() ) {
			return true;
		}
		return false;
	}

	/**
	 * Enqueue compiled Elementor CSS files of the active templates in <head> to prevent FOUC.
	 */
	public function enqueue_template_assets() {
		foreach ( [ 'header', 'footer', 'before_footer' ] as $type ) {
			$template_id = $this->resolved_templates[ $type ];
			if ( $template_id && class_exists( '\Elementor\Core\Files\CSS\Post' ) ) {
				$css_file = new \Elementor\Core\Files\CSS\Post( $template_id );
				$css_file->enqueue();
			}
		}
	}

	/**
	 * Print CSS to hide native theme header/footer if requested by template options.
	 */
	public function print_theme_override_css() {
		$css = [];

		$header_id = $this->resolved_templates['header'];
		if ( $header_id ) {
			$hide_theme_header = get_post_meta( $header_id, '_ta_hf_hide_theme_hf', true );
			if ( 'no' !== $hide_theme_header ) {
				$css[] = '
				/* Tools Adapter: Hide native theme header */
				header#masthead,
				header.site-header,
				.site-header:not(.ta-site-header),
				#site-header:not(#ta-site-header),
				#header:not(#ta-site-header),
				.header-main,
				.main-header-bar,
				.ast-theme-transparent-header #masthead {
					display: none !important;
				}';
			}
		}

		$footer_id = $this->resolved_templates['footer'];
		if ( $footer_id ) {
			$hide_theme_footer = get_post_meta( $footer_id, '_ta_hf_hide_theme_hf', true );
			if ( 'no' !== $hide_theme_footer ) {
				$css[] = '
				/* Tools Adapter: Hide native theme footer */
				footer#colophon,
				footer.site-footer,
				.site-footer:not(.ta-site-footer),
				#site-footer:not(#ta-site-footer),
				#footer:not(#ta-site-footer),
				.main-footer,
				.ast-footer-overlay {
					display: none !important;
				}';
			}
		}

		if ( ! empty( $css ) ) {
			echo '<style id="ta-hf-theme-override-inline-css">' . implode( "\n", $css ) . '</style>' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}

	/**
	 * Register Elementor theme locations.
	 *
	 * @param \ElementorPro\Modules\ThemeBuilder\Classes\Locations_Manager $location_manager Locations manager.
	 */
	public function register_elementor_locations( $location_manager ) {
		$location_manager->register_location( 'header', [ 'hook' => 'ta_header', 'remove_hooks' => [] ] );
		$location_manager->register_location( 'footer', [ 'hook' => 'ta_footer', 'remove_hooks' => [] ] );
	}

	/**
	 * Render header via Elementor Location API (Hello Elementor, etc.).
	 */
	public function render_elementor_header_location() {
		$this->render_header();
	}

	/**
	 * Render footer via Elementor Location API.
	 */
	public function render_elementor_footer_location() {
		$this->render_before_footer();
		$this->render_footer();
	}

	/**
	 * Setup hooks for popular themes (Astra, GeneratePress, OceanWP, Storefront).
	 */
	public function setup_theme_hooks() {
		if ( empty( $this->resolved_templates['header'] ) && empty( $this->resolved_templates['footer'] ) ) {
			return;
		}

		// 1. Astra.
		if ( function_exists( 'astra_header' ) ) {
			if ( ! empty( $this->resolved_templates['header'] ) ) {
				remove_action( 'astra_header', 'astra_header_markup' );
				add_action( 'astra_header', [ $this, 'render_header' ], 10 );
			}
			if ( ! empty( $this->resolved_templates['footer'] ) ) {
				remove_action( 'astra_footer', 'astra_footer_markup' );
				add_action( 'astra_footer', [ $this, 'render_before_footer' ], 9 );
				add_action( 'astra_footer', [ $this, 'render_footer' ], 10 );
			}
		}

		// 2. GeneratePress.
		if ( function_exists( 'generate_construct_header' ) ) {
			if ( ! empty( $this->resolved_templates['header'] ) ) {
				remove_action( 'generate_header', 'generate_construct_header' );
				add_action( 'generate_header', [ $this, 'render_header' ], 10 );
			}
			if ( ! empty( $this->resolved_templates['footer'] ) ) {
				remove_action( 'generate_footer', 'generate_construct_footer' );
				add_action( 'generate_footer', [ $this, 'render_before_footer' ], 9 );
				add_action( 'generate_footer', [ $this, 'render_footer' ], 10 );
			}
		}

		// 3. OceanWP.
		if ( class_exists( 'OCEANWP_Theme_Class' ) ) {
			if ( ! empty( $this->resolved_templates['header'] ) ) {
				remove_action( 'ocean_header', 'oceanwp_header_template' );
				add_action( 'ocean_header', [ $this, 'render_header' ], 10 );
			}
			if ( ! empty( $this->resolved_templates['footer'] ) ) {
				remove_action( 'ocean_footer', 'oceanwp_footer_template' );
				add_action( 'ocean_footer', [ $this, 'render_before_footer' ], 9 );
				add_action( 'ocean_footer', [ $this, 'render_footer' ], 10 );
			}
		}

		// 4. Storefront.
		if ( function_exists( 'storefront_header_container' ) ) {
			if ( ! empty( $this->resolved_templates['header'] ) ) {
				remove_action( 'storefront_header', 'storefront_header_container', 0 );
				remove_action( 'storefront_header', 'storefront_header_container_close', 41 );
				remove_action( 'storefront_header', 'storefront_primary_navigation_wrapper', 42 );
				remove_action( 'storefront_header', 'storefront_primary_navigation_wrapper_close', 68 );
				add_action( 'storefront_header', [ $this, 'render_header' ], 10 );
			}
			if ( ! empty( $this->resolved_templates['footer'] ) ) {
				remove_action( 'storefront_footer', 'storefront_footer_widgets', 10 );
				remove_action( 'storefront_footer', 'storefront_credit', 20 );
				add_action( 'storefront_footer', [ $this, 'render_before_footer' ], 9 );
				add_action( 'storefront_footer', [ $this, 'render_footer' ], 10 );
			}
		}

		// 5. Good Theme.
		if ( defined( 'GOOD_THEME_VERSION' ) ) {
			if ( ! empty( $this->resolved_templates['header'] ) ) {
				remove_action( 'good_theme_header', 'good_theme_render_header' );
				add_action( 'good_theme_header', [ $this, 'render_header' ], 10 );
			}
			if ( ! empty( $this->resolved_templates['footer'] ) ) {
				remove_action( 'good_theme_footer', 'good_theme_render_footer' );
				add_action( 'good_theme_footer', [ $this, 'render_before_footer' ], 9 );
				add_action( 'good_theme_footer', [ $this, 'render_footer' ], 10 );
			}
		}
	}

	/**
	 * Universal Header Fallback (fires on wp_body_open).
	 */
	public function render_header_fallback() {
		// Good Theme renders the header inside #page through `good_theme_header`.
		if ( defined( 'GOOD_THEME_VERSION' ) && ! empty( $this->resolved_templates['header'] ) ) {
			return;
		}


		$this->render_header();
	}

	/**
	 * Universal Footer Fallback (fires on wp_footer at priority 1).
	 */
	public function render_footer_fallback() {
		$this->render_before_footer();
		$this->render_footer();
	}

	/**
	 * Output the custom header markup.
	 */
	public function render_header() {
		if ( $this->rendered['header'] ) {
			return;
		}

		$template_id = $this->resolved_templates['header'];
		if ( ! $template_id ) {
			return;
		}

		$this->rendered['header'] = true;

		echo '<header id="ta-site-header" class="ta-site-header" role="banner" style="position:relative;z-index:9999;">';
		$this->render_elementor_content( $template_id );
		echo '</header>';
	}

	/**
	 * Output the custom before-footer markup.
	 */
	public function render_before_footer() {
		if ( $this->rendered['before_footer'] ) {
			return;
		}

		$template_id = $this->resolved_templates['before_footer'];
		if ( ! $template_id ) {
			return;
		}

		$this->rendered['before_footer'] = true;

		echo '<div id="ta-before-footer" class="ta-before-footer">';
		$this->render_elementor_content( $template_id );
		echo '</div>';
	}

	/**
	 * Output the custom footer markup.
	 */
	public function render_footer() {
		if ( $this->rendered['footer'] ) {
			return;
		}

		$template_id = $this->resolved_templates['footer'];
		if ( ! $template_id ) {
			return;
		}

		$this->rendered['footer'] = true;

		echo '<footer id="ta-site-footer" class="ta-site-footer" role="contentinfo" style="position:relative;z-index:999;">';
		$this->render_elementor_content( $template_id );
		echo '</footer>';
	}

	/**
	 * Render Elementor content with fallback.
	 *
	 * @param int $template_id Template ID.
	 */
	public function render_elementor_content( $template_id ) {
		if ( ! class_exists( '\Elementor\Plugin' ) ) {
			$post = get_post( $template_id );
			if ( $post ) {
				echo do_shortcode( wp_kses_post( $post->post_content ) );
			}
			return;
		}

		echo \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $template_id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Render shortcode [tools_adapter_template id="123"].
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string
	 */
	public function render_shortcode( $atts ) {
		$atts = shortcode_atts( [ 'id' => 0 ], $atts, 'tools_adapter_template' );
		$id   = (int) $atts['id'];

		if ( ! $id ) {
			return '';
		}

		ob_start();
		$this->render_elementor_content( $id );
		return ob_get_clean();
	}
}
