<?php
namespace ToolsAdapter;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main plugin class — registers category, widgets and assets.
 */
final class Plugin {

	/**
	 * @var Plugin|null
	 */
	private static $instance = null;

	/**
	 * Singleton.
	 *
	 * @return Plugin
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	private function __construct() {
		require_once TOOLS_ADAPTER_PATH . 'includes/pagination.php';
		require_once TOOLS_ADAPTER_PATH . 'includes/ajax-archive.php';
		require_once TOOLS_ADAPTER_PATH . 'includes/ajax-cart.php';
		require_once TOOLS_ADAPTER_PATH . 'includes/ajax-quick-view.php';
		require_once TOOLS_ADAPTER_PATH . 'includes/ajax-recently-viewed.php';
		require_once TOOLS_ADAPTER_PATH . 'includes/ajax-contact.php';
		require_once TOOLS_ADAPTER_PATH . 'includes/variation-swatches.php';
		require_once TOOLS_ADAPTER_PATH . 'includes/products-query.php';
		require_once TOOLS_ADAPTER_PATH . 'includes/product-card.php';
		new Ajax_Archive();
		new Ajax_Cart();
		new Ajax_Quick_View();
		new Ajax_Recently_Viewed();
		new Ajax_Contact();

		// "Sélecteur de variations visuel" is a global feature (not an
		// Elementor widget) — only patch WooCommerce's variation dropdowns
		// when it is enabled in the Tools Adapter settings page.
		if ( Admin_Settings::is_feature_enabled( 'variation_swatches' ) ) {
			new Variation_Swatches();
		}

		// "Liste de souhaits" — global feature (heart buttons, counter, list page).
		if ( Admin_Settings::is_feature_enabled( 'wishlist' ) ) {
			require_once TOOLS_ADAPTER_PATH . 'includes/wishlist.php';
			new Wishlist();
		}

		// "Header & Footer Builder" is a global theme-building feature.
		if ( Admin_Settings::is_feature_enabled( 'header_footer_builder' ) ) {
			require_once TOOLS_ADAPTER_PATH . 'includes/header-footer/class-manager.php';
			HeaderFooter\Manager::instance();
		}

		add_action( 'wp_footer', [ $this, 'print_current_product_id' ] );

		// Balises dynamiques propres au plugin (fonctionnent sans Elementor Pro).
		if ( Admin_Settings::is_feature_enabled( 'dynamic_tags' ) ) {
			add_action( 'elementor/dynamic_tags/register', [ $this, 'register_dynamic_tags' ] );
		}

		add_action( 'elementor/elements/categories_registered', [ $this, 'register_category' ] );
		add_action( 'elementor/widgets/register', [ $this, 'register_widgets' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'register_assets' ] );
		add_action( 'elementor/editor/after_enqueue_styles', [ $this, 'register_assets' ] );
		add_action( 'elementor/preview/enqueue_styles', [ $this, 'register_assets' ] );
	}

	/**
	 * Custom Elementor panel category.
	 *
	 * @param \Elementor\Elements_Manager $elements_manager Elements manager.
	 */
	public function register_category( $elements_manager ) {
		$elements_manager->add_category(
			'tools-adapter',
			[
				'title' => esc_html__( 'Tools Adapter', 'tools-adapter' ),
				'icon'  => 'fa fa-plug',
			]
		);
	}

	/**
	 * Expose the current product ID to the front-end (used by the "Produits
	 * récemment consultés" tracker and other WooCommerce-aware scripts).
	 */
	public function print_current_product_id() {
		if ( ! Admin_Settings::is_widget_enabled( 'tools-adapter-recently-viewed' ) ) {
			return;
		}
		if ( ! function_exists( 'is_product' ) || ! is_product() ) {
			return;
		}
		printf( '<script>window.ToolsAdapterCurrentProductId = %d;</script>', (int) get_the_ID() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Register the Tools Adapter dynamic tags group and tags.
	 *
	 * @param \Elementor\Core\DynamicTags\Manager $manager Dynamic tags manager.
	 */
	public function register_dynamic_tags( $manager ) {
		require_once TOOLS_ADAPTER_PATH . 'includes/dynamic-tags.php';
		\ToolsAdapter\DynamicTags\register( $manager );
	}

	/**
	 * Register Elementor widgets.
	 *
	 * @param \Elementor\Widgets_Manager $widgets_manager Widgets manager.
	 */
	public function register_widgets( $widgets_manager ) {
		// Classes de base (balises dynamiques sur les champs lien / texte / image).
		require_once TOOLS_ADAPTER_PATH . 'includes/dynamic-support.php';
		require_once TOOLS_ADAPTER_PATH . 'includes/widgets/products-widget-controls.php';

		// slug (as returned by the widget's get_name()) => [ file, class name ].
		// The slug is what the settings page ("Réglages Tools Adapter") uses
		// to enable/disable each widget individually — a widget whose slug
		// is disabled there is simply never registered with Elementor.
		$widgets = [
			'tools-adapter-product-categories' => [ 'product-categories.php', 'Product_Categories' ],
			'tools-adapter-price-filter'        => [ 'price-filter.php', 'Price_Filter' ],
			'tools-adapter-product-grid'        => [ 'product-grid.php', 'Product_Grid' ],
			'tools-adapter-product-carousel'    => [ 'product-carousel.php', 'Product_Carousel' ],
			'tools-adapter-product-archive'     => [ 'product-archive.php', 'Product_Archive' ],
			'tools-adapter-hero-banner'          => [ 'hero-banner.php', 'Hero_Banner' ],
			'tools-adapter-hero-carousel'        => [ 'hero-carousel.php', 'Hero_Carousel' ],
			'tools-adapter-cta-band'             => [ 'cta-band.php', 'Cta_Band' ],
			'tools-adapter-marquee'              => [ 'marquee.php', 'Marquee' ],
			'tools-adapter-cost-calculator'      => [ 'cost-calculator.php', 'Cost_Calculator' ],
			'tools-adapter-stats'                => [ 'stats-counters.php', 'Stats_Counters' ],
			'tools-adapter-logos'                => [ 'logos-grid.php', 'Logos_Grid' ],
			'tools-adapter-faq'                  => [ 'faq-accordion.php', 'Faq_Accordion' ],
			'tools-adapter-trust-badges'         => [ 'trust-badges.php', 'Trust_Badges' ],
			'tools-adapter-category-banner'      => [ 'category-banner.php', 'Category_Banner' ],
			'tools-adapter-testimonials'         => [ 'testimonials.php', 'Testimonials' ],
			'tools-adapter-team'                 => [ 'team-members.php', 'Team_Members' ],
			'tools-adapter-pricing-table'        => [ 'pricing-table.php', 'Pricing_Table' ],
			'tools-adapter-timeline'             => [ 'timeline.php', 'Timeline' ],
			'tools-adapter-before-after'         => [ 'before-after.php', 'Before_After' ],
			'tools-adapter-toc'                  => [ 'table-of-contents.php', 'Table_Of_Contents' ],
			'tools-adapter-reading-progress'    => [ 'reading-progress-bar.php', 'Reading_Progress_Bar' ],
			'tools-adapter-mini-cart'            => [ 'mini-cart.php', 'Mini_Cart' ],
			'tools-adapter-sticky-atc'           => [ 'sticky-add-to-cart.php', 'Sticky_Add_To_Cart' ],
			'tools-adapter-size-guide'           => [ 'size-guide.php', 'Size_Guide' ],
			'tools-adapter-attribute-filter'     => [ 'attribute-filter.php', 'Attribute_Filter' ],
			'tools-adapter-brands'               => [ 'brands-grid.php', 'Brands_Grid' ],
			'tools-adapter-recently-viewed'      => [ 'recently-viewed.php', 'Recently_Viewed' ],
			'tools-adapter-sale-countdown'       => [ 'sale-countdown.php', 'Sale_Countdown' ],
			'tools-adapter-stock-urgency'        => [ 'stock-urgency.php', 'Stock_Urgency' ],
			'tools-adapter-mega-menu'            => [ 'mega-menu.php', 'Mega_Menu' ],
			'tools-adapter-cookie-banner'        => [ 'cookie-banner.php', 'Cookie_Banner' ],
			'tools-adapter-social-proof'         => [ 'social-proof.php', 'Social_Proof' ],
			'tools-adapter-contact-form'         => [ 'contact-form.php', 'Contact_Form' ],
			'tools-adapter-google-map'           => [ 'google-map.php', 'Google_Map' ],
			'tools-adapter-blog-grid'            => [ 'blog-grid.php', 'Blog_Grid' ],
			'tools-adapter-icon-box'             => [ 'icon-box.php', 'Icon_Box' ],
			'tools-adapter-image-box'            => [ 'image-box.php', 'Image_Box' ],
			'tools-adapter-service-cards'        => [ 'service-cards.php', 'Service_Cards' ],
			'tools-adapter-quick-choice'         => [ 'quick-choice-card.php', 'Quick_Choice_Card' ],
			'tools-adapter-image-stack'          => [ 'image-stack.php', 'Image_Stack' ],
			'tools-adapter-price-card'           => [ 'price-card.php', 'Price_Card' ],
			'tools-adapter-project-gallery'      => [ 'project-gallery.php', 'Project_Gallery' ],
			'tools-adapter-project-showcase'     => [ 'project-showcase.php', 'Project_Showcase' ],
			'tools-adapter-process-steps'        => [ 'process-steps.php', 'Process_Steps' ],
			'tools-adapter-site-logo'            => [ 'site-logo.php', 'Site_Logo' ],
			'tools-adapter-nav-menu'            => [ 'nav-menu.php', 'Nav_Menu' ],
			'tools-adapter-site-search'          => [ 'site-search.php', 'Site_Search' ],
			'tools-adapter-site-copyright'       => [ 'site-copyright.php', 'Site_Copyright' ],
			'tools-adapter-interactive-map'      => [ 'interactive-map.php', 'Interactive_Map' ],
			'tools-adapter-wishlist'             => [ 'wishlist.php', 'Wishlist_List' ],
			'tools-adapter-promo-grid'           => [ 'promo-grid.php', 'Promo_Grid' ],
			'tools-adapter-lookbook'             => [ 'lookbook.php', 'Lookbook' ],
			'tools-adapter-category-showcase'    => [ 'category-showcase.php', 'Category_Showcase' ],
			'tools-adapter-cta-banner'           => [ 'cta-banner.php', 'Cta_Banner' ],
		];

		foreach ( $widgets as $slug => $data ) {
			list( $file, $class ) = $data;

			if ( ! Admin_Settings::is_widget_enabled( $slug ) ) {
				continue;
			}

			require_once TOOLS_ADAPTER_PATH . 'includes/widgets/' . $file;
			$class_name = __NAMESPACE__ . '\\Widgets\\' . $class;
			$widgets_manager->register( new $class_name() );
		}
	}

	/**
	 * Register styles & scripts (enqueued by widgets via get_*_depends).
	 */
	public function register_assets() {
		wp_register_style(
			'tools-adapter-product-categories',
			TOOLS_ADAPTER_URL . 'assets/css/product-categories.css',
			[ 'tools-adapter-product-archive' ],
			TOOLS_ADAPTER_VERSION
		);

		wp_register_style(
			'tools-adapter-price-filter',
			TOOLS_ADAPTER_URL . 'assets/css/price-filter.css',
			[],
			TOOLS_ADAPTER_VERSION
		);

		wp_register_style(
			'tools-adapter-products',
			TOOLS_ADAPTER_URL . 'assets/css/products.css',
			[],
			TOOLS_ADAPTER_VERSION
		);

		wp_register_style(
			'tools-adapter-product-carousel',
			TOOLS_ADAPTER_URL . 'assets/css/product-carousel.css',
			[ 'tools-adapter-products' ],
			TOOLS_ADAPTER_VERSION
		);

		wp_register_style(
			'tools-adapter-product-archive',
			TOOLS_ADAPTER_URL . 'assets/css/product-archive.css',
			[ 'tools-adapter-products' ],
			TOOLS_ADAPTER_VERSION
		);

		wp_register_script(
			'tools-adapter-archive',
			TOOLS_ADAPTER_URL . 'assets/js/archive.js',
			[ 'jquery', 'elementor-frontend' ],
			TOOLS_ADAPTER_VERSION,
			true
		);

		wp_localize_script(
			'tools-adapter-archive',
			'ToolsAdapterArchive',
			[
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'action'  => Ajax_Archive::ACTION,
				'nonce'   => wp_create_nonce( Ajax_Archive::NONCE ),
				'i18n'    => [
					'loading' => __( 'Filtrage en cours…', 'tools-adapter' ),
					'error'   => __( 'Impossible de filtrer les produits. Réessayez.', 'tools-adapter' ),
					'empty'   => __( 'Aucun produit ne correspond à vos filtres.', 'tools-adapter' ),
				],
			]
		);

		wp_register_script(
			'tools-adapter-price-filter',
			TOOLS_ADAPTER_URL . 'assets/js/price-filter.js',
			[ 'jquery', 'elementor-frontend', 'tools-adapter-archive' ],
			TOOLS_ADAPTER_VERSION,
			true
		);

		wp_localize_script(
			'tools-adapter-price-filter',
			'ToolsAdapterPriceFilter',
			[
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'action'  => 'tools_adapter_filter_products',
				'nonce'   => wp_create_nonce( 'tools_adapter_price_filter' ),
				'i18n'    => [
					'loading' => __( 'Filtrage en cours…', 'tools-adapter' ),
					'error'   => __( 'Impossible de filtrer les produits. Réessayez.', 'tools-adapter' ),
					'empty'   => __( 'Aucun produit ne correspond à cette plage de prix.', 'tools-adapter' ),
				],
			]
		);

		wp_register_script(
			'tools-adapter-product-carousel',
			TOOLS_ADAPTER_URL . 'assets/js/product-carousel.js',
			[ 'elementor-frontend' ],
			TOOLS_ADAPTER_VERSION,
			true
		);

		// Phase 1 — widgets génériques (page building).
		wp_register_style( 'tools-adapter-hero-banner', TOOLS_ADAPTER_URL . 'assets/css/hero-banner.css', [], TOOLS_ADAPTER_VERSION );
		wp_register_style( 'tools-adapter-hero-carousel', TOOLS_ADAPTER_URL . 'assets/css/hero-carousel.css', [], TOOLS_ADAPTER_VERSION );
		wp_register_style( 'tools-adapter-cta-band', TOOLS_ADAPTER_URL . 'assets/css/cta-band.css', [], TOOLS_ADAPTER_VERSION );
		wp_register_style( 'tools-adapter-marquee', TOOLS_ADAPTER_URL . 'assets/css/marquee.css', [], TOOLS_ADAPTER_VERSION );
		wp_register_style( 'tools-adapter-cost-calculator', TOOLS_ADAPTER_URL . 'assets/css/cost-calculator.css', [], TOOLS_ADAPTER_VERSION );
		wp_register_style( 'tools-adapter-stats', TOOLS_ADAPTER_URL . 'assets/css/stats-counters.css', [], TOOLS_ADAPTER_VERSION );
		wp_register_style( 'tools-adapter-logos', TOOLS_ADAPTER_URL . 'assets/css/logos-grid.css', [], TOOLS_ADAPTER_VERSION );
		wp_register_style( 'tools-adapter-faq', TOOLS_ADAPTER_URL . 'assets/css/faq-accordion.css', [], TOOLS_ADAPTER_VERSION );
		wp_register_style( 'tools-adapter-trust-badges', TOOLS_ADAPTER_URL . 'assets/css/trust-badges.css', [], TOOLS_ADAPTER_VERSION );
		wp_register_style( 'tools-adapter-category-banner', TOOLS_ADAPTER_URL . 'assets/css/category-banner.css', [], TOOLS_ADAPTER_VERSION );
		wp_register_style( 'tools-adapter-icon-box', TOOLS_ADAPTER_URL . 'assets/css/icon-box.css', [], TOOLS_ADAPTER_VERSION );
		wp_register_style( 'tools-adapter-image-box', TOOLS_ADAPTER_URL . 'assets/css/image-box.css', [], TOOLS_ADAPTER_VERSION );
		wp_register_style( 'tools-adapter-service-cards', TOOLS_ADAPTER_URL . 'assets/css/service-cards.css', [], TOOLS_ADAPTER_VERSION );
		wp_register_style( 'tools-adapter-price-card', TOOLS_ADAPTER_URL . 'assets/css/price-card.css', [], TOOLS_ADAPTER_VERSION );
		wp_register_style( 'tools-adapter-image-stack', TOOLS_ADAPTER_URL . 'assets/css/image-stack.css', [], TOOLS_ADAPTER_VERSION );
		wp_register_style( 'tools-adapter-quick-choice', TOOLS_ADAPTER_URL . 'assets/css/quick-choice-card.css', [], TOOLS_ADAPTER_VERSION );
		wp_register_style( 'tools-adapter-project-gallery', TOOLS_ADAPTER_URL . 'assets/css/project-gallery.css', [], TOOLS_ADAPTER_VERSION );
		wp_register_style( 'tools-adapter-process-steps', TOOLS_ADAPTER_URL . 'assets/css/process-steps.css', [], TOOLS_ADAPTER_VERSION );

		wp_register_script( 'tools-adapter-stats', TOOLS_ADAPTER_URL . 'assets/js/stats-counters.js', [], TOOLS_ADAPTER_VERSION, true );
		wp_register_script( 'tools-adapter-cost-calculator', TOOLS_ADAPTER_URL . 'assets/js/cost-calculator.js', [], TOOLS_ADAPTER_VERSION, true );
		wp_register_script( 'tools-adapter-faq', TOOLS_ADAPTER_URL . 'assets/js/faq-accordion.js', [], TOOLS_ADAPTER_VERSION, true );
		wp_register_script( 'tools-adapter-project-gallery', TOOLS_ADAPTER_URL . 'assets/js/project-gallery.js', [ 'jquery' ], TOOLS_ADAPTER_VERSION, true );

		// Phase 2 — contenu & preuve sociale.
		wp_register_style( 'tools-adapter-testimonials', TOOLS_ADAPTER_URL . 'assets/css/testimonials.css', [], TOOLS_ADAPTER_VERSION );
		wp_register_style( 'tools-adapter-team', TOOLS_ADAPTER_URL . 'assets/css/team.css', [], TOOLS_ADAPTER_VERSION );
		wp_register_style( 'tools-adapter-pricing-table', TOOLS_ADAPTER_URL . 'assets/css/pricing-table.css', [], TOOLS_ADAPTER_VERSION );
		wp_register_style( 'tools-adapter-timeline', TOOLS_ADAPTER_URL . 'assets/css/timeline.css', [], TOOLS_ADAPTER_VERSION );
		wp_register_style( 'tools-adapter-before-after', TOOLS_ADAPTER_URL . 'assets/css/before-after.css', [], TOOLS_ADAPTER_VERSION );
		wp_register_style( 'tools-adapter-toc', TOOLS_ADAPTER_URL . 'assets/css/table-of-contents.css', [], TOOLS_ADAPTER_VERSION );
		wp_register_style( 'tools-adapter-reading-progress', TOOLS_ADAPTER_URL . 'assets/css/reading-progress.css', [], TOOLS_ADAPTER_VERSION );

		wp_register_script( 'tools-adapter-carousel', TOOLS_ADAPTER_URL . 'assets/js/simple-carousel.js', [], TOOLS_ADAPTER_VERSION, true );
		wp_register_script( 'tools-adapter-before-after', TOOLS_ADAPTER_URL . 'assets/js/before-after.js', [], TOOLS_ADAPTER_VERSION, true );
		wp_register_script( 'tools-adapter-toc', TOOLS_ADAPTER_URL . 'assets/js/table-of-contents.js', [], TOOLS_ADAPTER_VERSION, true );
		wp_register_script( 'tools-adapter-reading-progress', TOOLS_ADAPTER_URL . 'assets/js/reading-progress.js', [], TOOLS_ADAPTER_VERSION, true );

		// Phase 3 — conversion boutique.
		wp_register_style( 'tools-adapter-modal', TOOLS_ADAPTER_URL . 'assets/css/modal.css', [], TOOLS_ADAPTER_VERSION );
		wp_register_style( 'tools-adapter-mini-cart', TOOLS_ADAPTER_URL . 'assets/css/mini-cart.css', [], TOOLS_ADAPTER_VERSION );
		wp_register_style( 'tools-adapter-sticky-atc', TOOLS_ADAPTER_URL . 'assets/css/sticky-add-to-cart.css', [], TOOLS_ADAPTER_VERSION );
		wp_register_style( 'tools-adapter-quick-view', TOOLS_ADAPTER_URL . 'assets/css/quick-view.css', [ 'tools-adapter-modal' ], TOOLS_ADAPTER_VERSION );
		wp_register_style( 'tools-adapter-size-guide', TOOLS_ADAPTER_URL . 'assets/css/size-guide.css', [ 'tools-adapter-modal' ], TOOLS_ADAPTER_VERSION );
		wp_register_style( 'tools-adapter-variation-swatches', TOOLS_ADAPTER_URL . 'assets/css/variation-swatches.css', [], TOOLS_ADAPTER_VERSION );

		wp_register_script( 'tools-adapter-modal', TOOLS_ADAPTER_URL . 'assets/js/modal.js', [], TOOLS_ADAPTER_VERSION, true );
		wp_register_script( 'tools-adapter-mini-cart', TOOLS_ADAPTER_URL . 'assets/js/mini-cart.js', [], TOOLS_ADAPTER_VERSION, true );
		wp_register_script( 'tools-adapter-sticky-atc', TOOLS_ADAPTER_URL . 'assets/js/sticky-add-to-cart.js', [], TOOLS_ADAPTER_VERSION, true );
		wp_register_script( 'tools-adapter-quick-view', TOOLS_ADAPTER_URL . 'assets/js/quick-view.js', [ 'tools-adapter-modal' ], TOOLS_ADAPTER_VERSION, true );
		wp_register_script( 'tools-adapter-variation-swatches', TOOLS_ADAPTER_URL . 'assets/js/variation-swatches.js', [], TOOLS_ADAPTER_VERSION, true );

		// Liste de souhaits, mosaïque promo, lookbook.
		wp_register_style( 'tools-adapter-wishlist', TOOLS_ADAPTER_URL . 'assets/css/wishlist.css', [], TOOLS_ADAPTER_VERSION );
		wp_register_script( 'tools-adapter-wishlist', TOOLS_ADAPTER_URL . 'assets/js/wishlist.js', [], TOOLS_ADAPTER_VERSION, true );
		wp_register_style( 'tools-adapter-promo-grid', TOOLS_ADAPTER_URL . 'assets/css/promo-grid.css', [], TOOLS_ADAPTER_VERSION );
		wp_register_style( 'tools-adapter-lookbook', TOOLS_ADAPTER_URL . 'assets/css/lookbook.css', [], TOOLS_ADAPTER_VERSION );
		wp_register_script( 'tools-adapter-lookbook', TOOLS_ADAPTER_URL . 'assets/js/lookbook.js', [], TOOLS_ADAPTER_VERSION, true );
		wp_register_style( 'tools-adapter-category-showcase', TOOLS_ADAPTER_URL . 'assets/css/category-showcase.css', [], TOOLS_ADAPTER_VERSION );
		wp_register_script( 'tools-adapter-category-showcase', TOOLS_ADAPTER_URL . 'assets/js/category-showcase.js', [], TOOLS_ADAPTER_VERSION, true );
		wp_register_style( 'tools-adapter-cta-banner', TOOLS_ADAPTER_URL . 'assets/css/cta-banner.css', [], TOOLS_ADAPTER_VERSION );

		wp_localize_script(
			'tools-adapter-mini-cart',
			'ToolsAdapterCart',
			[
				'ajaxUrl'     => admin_url( 'admin-ajax.php' ),
				'actionGet'   => Ajax_Cart::ACTION_GET,
				'actionRemove' => Ajax_Cart::ACTION_REMOVE,
			]
		);

		wp_localize_script(
			'tools-adapter-quick-view',
			'ToolsAdapterQuickView',
			[
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'action'  => Ajax_Quick_View::ACTION,
				'nonce'   => wp_create_nonce( Ajax_Quick_View::NONCE ),
				'i18n'    => [
					'loading' => __( 'Chargement…', 'tools-adapter' ),
					'error'   => __( 'Impossible de charger ce produit.', 'tools-adapter' ),
				],
			]
		);

		if ( function_exists( 'is_product' ) && is_product() && Admin_Settings::is_feature_enabled( 'variation_swatches' ) ) {
			wp_enqueue_style( 'tools-adapter-variation-swatches' );
			wp_enqueue_script( 'tools-adapter-variation-swatches' );
		}

		// Phase 4 — découverte & filtrage.
		wp_register_style( 'tools-adapter-attribute-filter', TOOLS_ADAPTER_URL . 'assets/css/attribute-filter.css', [], TOOLS_ADAPTER_VERSION );
		wp_register_style( 'tools-adapter-brands', TOOLS_ADAPTER_URL . 'assets/css/brands.css', [], TOOLS_ADAPTER_VERSION );
		wp_register_style( 'tools-adapter-recently-viewed', TOOLS_ADAPTER_URL . 'assets/css/recently-viewed.css', [ 'tools-adapter-products' ], TOOLS_ADAPTER_VERSION );
		wp_register_style( 'tools-adapter-sale-countdown', TOOLS_ADAPTER_URL . 'assets/css/sale-countdown.css', [], TOOLS_ADAPTER_VERSION );
		wp_register_style( 'tools-adapter-stock-urgency', TOOLS_ADAPTER_URL . 'assets/css/stock-urgency.css', [], TOOLS_ADAPTER_VERSION );

		wp_register_script( 'tools-adapter-recently-viewed', TOOLS_ADAPTER_URL . 'assets/js/recently-viewed.js', [], TOOLS_ADAPTER_VERSION, true );
		wp_register_script( 'tools-adapter-countdown', TOOLS_ADAPTER_URL . 'assets/js/countdown.js', [], TOOLS_ADAPTER_VERSION, true );

		wp_localize_script(
			'tools-adapter-recently-viewed',
			'ToolsAdapterRecentlyViewed',
			[
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'action'  => Ajax_Recently_Viewed::ACTION,
				'nonce'   => wp_create_nonce( Ajax_Recently_Viewed::NONCE ),
			]
		);

		// Always enqueue the tracker (not just when the widget is present):
		// visits must be recorded on every product page so the widget has
		// data to display later, on any other page of the site. Skipped
		// entirely when the widget is disabled in the settings page.
		if ( class_exists( 'WooCommerce' ) && Admin_Settings::is_widget_enabled( 'tools-adapter-recently-viewed' ) ) {
			wp_enqueue_script( 'tools-adapter-recently-viewed' );
		}

		// Phase 5 — site-wide & navigation.
		wp_register_style( 'tools-adapter-mega-menu', TOOLS_ADAPTER_URL . 'assets/css/mega-menu.css', [], TOOLS_ADAPTER_VERSION );
		wp_register_style( 'tools-adapter-cookie-banner', TOOLS_ADAPTER_URL . 'assets/css/cookie-banner.css', [], TOOLS_ADAPTER_VERSION );
		wp_register_style( 'tools-adapter-social-proof', TOOLS_ADAPTER_URL . 'assets/css/social-proof.css', [], TOOLS_ADAPTER_VERSION );
		wp_register_style( 'tools-adapter-contact-form', TOOLS_ADAPTER_URL . 'assets/css/contact-form.css', [], TOOLS_ADAPTER_VERSION );
		wp_register_style( 'tools-adapter-google-map', TOOLS_ADAPTER_URL . 'assets/css/google-map.css', [], TOOLS_ADAPTER_VERSION );
		wp_register_style( 'tools-adapter-blog-grid', TOOLS_ADAPTER_URL . 'assets/css/blog-grid.css', [], TOOLS_ADAPTER_VERSION );

		// Carte Interactive (Leaflet & CartoDB).
		wp_register_style( 'leaflet-css', 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css', [], '1.9.4' );
		wp_register_script( 'leaflet-js', 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js', [], '1.9.4', true );
		wp_register_style( 'tools-adapter-interactive-map', TOOLS_ADAPTER_URL . 'assets/css/interactive-map.css', [ 'leaflet-css' ], TOOLS_ADAPTER_VERSION );
		wp_register_script( 'tools-adapter-interactive-map', TOOLS_ADAPTER_URL . 'assets/js/interactive-map.js', [ 'jquery', 'leaflet-js', 'elementor-frontend' ], TOOLS_ADAPTER_VERSION, true );

		wp_register_script( 'tools-adapter-mega-menu', TOOLS_ADAPTER_URL . 'assets/js/mega-menu.js', [], TOOLS_ADAPTER_VERSION, true );
		wp_register_script( 'tools-adapter-cookie-banner', TOOLS_ADAPTER_URL . 'assets/js/cookie-banner.js', [], TOOLS_ADAPTER_VERSION, true );
		wp_register_script( 'tools-adapter-social-proof', TOOLS_ADAPTER_URL . 'assets/js/social-proof.js', [], TOOLS_ADAPTER_VERSION, true );
		wp_register_script( 'tools-adapter-contact-form', TOOLS_ADAPTER_URL . 'assets/js/contact-form.js', [], TOOLS_ADAPTER_VERSION, true );
		wp_register_script( 'tools-adapter-blog-grid', TOOLS_ADAPTER_URL . 'assets/js/blog-grid.js', [], TOOLS_ADAPTER_VERSION, true );

		// Header & Footer builder widgets assets.
		wp_register_style( 'tools-adapter-site-logo', TOOLS_ADAPTER_URL . 'assets/css/site-logo.css', [], TOOLS_ADAPTER_VERSION );
		wp_register_style( 'tools-adapter-nav-menu', TOOLS_ADAPTER_URL . 'assets/css/nav-menu.css', [], TOOLS_ADAPTER_VERSION );
		wp_register_style( 'tools-adapter-site-search', TOOLS_ADAPTER_URL . 'assets/css/site-search.css', [], TOOLS_ADAPTER_VERSION );
		wp_register_style( 'tools-adapter-site-copyright', TOOLS_ADAPTER_URL . 'assets/css/site-copyright.css', [], TOOLS_ADAPTER_VERSION );

		wp_register_script( 'tools-adapter-nav-menu', TOOLS_ADAPTER_URL . 'assets/js/nav-menu.js', [ 'jquery' ], TOOLS_ADAPTER_VERSION, true );
		wp_register_script( 'tools-adapter-site-search', TOOLS_ADAPTER_URL . 'assets/js/site-search.js', [ 'jquery' ], TOOLS_ADAPTER_VERSION, true );

		// Project showcase gallery assets.
		wp_register_style( 'tools-adapter-project-showcase', TOOLS_ADAPTER_URL . 'assets/css/project-showcase.css', [], TOOLS_ADAPTER_VERSION );
		wp_register_script( 'tools-adapter-project-showcase', TOOLS_ADAPTER_URL . 'assets/js/project-showcase.js', [ 'jquery', 'elementor-frontend' ], TOOLS_ADAPTER_VERSION, true );

		wp_localize_script(
			'tools-adapter-contact-form',
			'ToolsAdapterContactForm',
			[
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'action'  => Ajax_Contact::ACTION,
				'nonce'   => wp_create_nonce( Ajax_Contact::NONCE ),
			]
		);
	}
}
