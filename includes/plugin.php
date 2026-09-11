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
		require_once TOOLS_ADAPTER_PATH . 'includes/variation-swatches.php';
		require_once TOOLS_ADAPTER_PATH . 'includes/products-query.php';
		require_once TOOLS_ADAPTER_PATH . 'includes/product-card.php';
		new Ajax_Archive();
		new Ajax_Cart();
		new Ajax_Quick_View();
		new Ajax_Recently_Viewed();
		new Variation_Swatches();

		add_action( 'wp_footer', [ $this, 'print_current_product_id' ] );

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
		if ( ! function_exists( 'is_product' ) || ! is_product() ) {
			return;
		}
		printf( '<script>window.ToolsAdapterCurrentProductId = %d;</script>', (int) get_the_ID() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Register Elementor widgets.
	 *
	 * @param \Elementor\Widgets_Manager $widgets_manager Widgets manager.
	 */
	public function register_widgets( $widgets_manager ) {
		require_once TOOLS_ADAPTER_PATH . 'includes/widgets/products-widget-controls.php';
		require_once TOOLS_ADAPTER_PATH . 'includes/widgets/product-categories.php';
		require_once TOOLS_ADAPTER_PATH . 'includes/widgets/price-filter.php';
		require_once TOOLS_ADAPTER_PATH . 'includes/widgets/product-grid.php';
		require_once TOOLS_ADAPTER_PATH . 'includes/widgets/product-carousel.php';
		require_once TOOLS_ADAPTER_PATH . 'includes/widgets/product-archive.php';
		require_once TOOLS_ADAPTER_PATH . 'includes/widgets/hero-banner.php';
		require_once TOOLS_ADAPTER_PATH . 'includes/widgets/cta-band.php';
		require_once TOOLS_ADAPTER_PATH . 'includes/widgets/stats-counters.php';
		require_once TOOLS_ADAPTER_PATH . 'includes/widgets/logos-grid.php';
		require_once TOOLS_ADAPTER_PATH . 'includes/widgets/faq-accordion.php';
		require_once TOOLS_ADAPTER_PATH . 'includes/widgets/trust-badges.php';
		require_once TOOLS_ADAPTER_PATH . 'includes/widgets/category-banner.php';
		require_once TOOLS_ADAPTER_PATH . 'includes/widgets/testimonials.php';
		require_once TOOLS_ADAPTER_PATH . 'includes/widgets/team-members.php';
		require_once TOOLS_ADAPTER_PATH . 'includes/widgets/pricing-table.php';
		require_once TOOLS_ADAPTER_PATH . 'includes/widgets/timeline.php';
		require_once TOOLS_ADAPTER_PATH . 'includes/widgets/before-after.php';
		require_once TOOLS_ADAPTER_PATH . 'includes/widgets/table-of-contents.php';
		require_once TOOLS_ADAPTER_PATH . 'includes/widgets/reading-progress-bar.php';
		require_once TOOLS_ADAPTER_PATH . 'includes/widgets/mini-cart.php';
		require_once TOOLS_ADAPTER_PATH . 'includes/widgets/sticky-add-to-cart.php';
		require_once TOOLS_ADAPTER_PATH . 'includes/widgets/size-guide.php';
		require_once TOOLS_ADAPTER_PATH . 'includes/widgets/attribute-filter.php';
		require_once TOOLS_ADAPTER_PATH . 'includes/widgets/brands-grid.php';
		require_once TOOLS_ADAPTER_PATH . 'includes/widgets/recently-viewed.php';
		require_once TOOLS_ADAPTER_PATH . 'includes/widgets/sale-countdown.php';
		require_once TOOLS_ADAPTER_PATH . 'includes/widgets/stock-urgency.php';

		$widgets_manager->register( new Widgets\Product_Categories() );
		$widgets_manager->register( new Widgets\Price_Filter() );
		$widgets_manager->register( new Widgets\Product_Grid() );
		$widgets_manager->register( new Widgets\Product_Carousel() );
		$widgets_manager->register( new Widgets\Product_Archive() );
		$widgets_manager->register( new Widgets\Hero_Banner() );
		$widgets_manager->register( new Widgets\Cta_Band() );
		$widgets_manager->register( new Widgets\Stats_Counters() );
		$widgets_manager->register( new Widgets\Logos_Grid() );
		$widgets_manager->register( new Widgets\Faq_Accordion() );
		$widgets_manager->register( new Widgets\Trust_Badges() );
		$widgets_manager->register( new Widgets\Category_Banner() );
		$widgets_manager->register( new Widgets\Testimonials() );
		$widgets_manager->register( new Widgets\Team_Members() );
		$widgets_manager->register( new Widgets\Pricing_Table() );
		$widgets_manager->register( new Widgets\Timeline() );
		$widgets_manager->register( new Widgets\Before_After() );
		$widgets_manager->register( new Widgets\Table_Of_Contents() );
		$widgets_manager->register( new Widgets\Reading_Progress_Bar() );
		$widgets_manager->register( new Widgets\Mini_Cart() );
		$widgets_manager->register( new Widgets\Sticky_Add_To_Cart() );
		$widgets_manager->register( new Widgets\Size_Guide() );
		$widgets_manager->register( new Widgets\Attribute_Filter() );
		$widgets_manager->register( new Widgets\Brands_Grid() );
		$widgets_manager->register( new Widgets\Recently_Viewed() );
		$widgets_manager->register( new Widgets\Sale_Countdown() );
		$widgets_manager->register( new Widgets\Stock_Urgency() );
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
		wp_register_style( 'tools-adapter-cta-band', TOOLS_ADAPTER_URL . 'assets/css/cta-band.css', [], TOOLS_ADAPTER_VERSION );
		wp_register_style( 'tools-adapter-stats', TOOLS_ADAPTER_URL . 'assets/css/stats-counters.css', [], TOOLS_ADAPTER_VERSION );
		wp_register_style( 'tools-adapter-logos', TOOLS_ADAPTER_URL . 'assets/css/logos-grid.css', [], TOOLS_ADAPTER_VERSION );
		wp_register_style( 'tools-adapter-faq', TOOLS_ADAPTER_URL . 'assets/css/faq-accordion.css', [], TOOLS_ADAPTER_VERSION );
		wp_register_style( 'tools-adapter-trust-badges', TOOLS_ADAPTER_URL . 'assets/css/trust-badges.css', [], TOOLS_ADAPTER_VERSION );
		wp_register_style( 'tools-adapter-category-banner', TOOLS_ADAPTER_URL . 'assets/css/category-banner.css', [], TOOLS_ADAPTER_VERSION );

		wp_register_script( 'tools-adapter-stats', TOOLS_ADAPTER_URL . 'assets/js/stats-counters.js', [], TOOLS_ADAPTER_VERSION, true );
		wp_register_script( 'tools-adapter-faq', TOOLS_ADAPTER_URL . 'assets/js/faq-accordion.js', [], TOOLS_ADAPTER_VERSION, true );

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

		if ( function_exists( 'is_product' ) && is_product() ) {
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
		// data to display later, on any other page of the site.
		if ( class_exists( 'WooCommerce' ) ) {
			wp_enqueue_script( 'tools-adapter-recently-viewed' );
		}
	}
}
