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
		require_once TOOLS_ADAPTER_PATH . 'includes/ajax-archive.php';
		require_once TOOLS_ADAPTER_PATH . 'includes/products-query.php';
		require_once TOOLS_ADAPTER_PATH . 'includes/product-card.php';
		new Ajax_Archive();

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

		$widgets_manager->register( new Widgets\Product_Categories() );
		$widgets_manager->register( new Widgets\Price_Filter() );
		$widgets_manager->register( new Widgets\Product_Grid() );
		$widgets_manager->register( new Widgets\Product_Carousel() );
		$widgets_manager->register( new Widgets\Product_Archive() );
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
	}
}
