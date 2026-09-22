<?php
namespace ToolsAdapter;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Page de réglages admin — permet d'activer/désactiver individuellement
 * chaque widget Elementor et chaque fonctionnalité globale du plugin.
 */
final class Admin_Settings {

	const OPTION_KEY = 'tools_adapter_settings';
	const PAGE_SLUG   = 'tools-adapter-settings';
	const GROUP       = 'tools_adapter_settings_group';

	/**
	 * @var array<string,array>|null
	 */
	private static $widgets_map = null;

	public function __construct() {
		add_action( 'admin_menu', [ $this, 'register_menu' ] );
		add_action( 'admin_init', [ $this, 'register_setting' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_assets' ] );
		add_filter( 'plugin_action_links_' . plugin_basename( TOOLS_ADAPTER_FILE ), [ $this, 'add_settings_link' ] );
	}

	/**
	 * Add a "Réglages" shortcut on the plugin list page.
	 *
	 * @param string[] $links Existing action links.
	 * @return string[]
	 */
	public function add_settings_link( $links ) {
		$url = admin_url( 'admin.php?page=' . self::PAGE_SLUG );
		array_unshift( $links, '<a href="' . esc_url( $url ) . '">' . esc_html__( 'Réglages', 'tools-adapter' ) . '</a>' );
		return $links;
	}

	/**
	 * Register the top-level admin menu page.
	 */
	public function register_menu() {
		add_menu_page(
			esc_html__( 'Tools Adapter', 'tools-adapter' ),
			esc_html__( 'Tools Adapter', 'tools-adapter' ),
			'manage_options',
			self::PAGE_SLUG,
			[ $this, 'render_page' ],
			$this->get_menu_icon(),
			58.6
		);
	}

	/**
	 * Small inline SVG (plug icon) used as the admin menu icon.
	 *
	 * @return string
	 */
	private function get_menu_icon() {
		$svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#a7aaad" stroke-width="1.6"><path d="M9 3v5M15 3v5M6 8h12l-1 4a5 5 0 0 1-5 4h0a5 5 0 0 1-5-4L6 8Z"/><path d="M12 16v3M9.5 21h5"/></svg>';
		return 'data:image/svg+xml;base64,' . base64_encode( $svg ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.serialize_comment_base64
	}

	public function enqueue_assets( $hook ) {
		if ( 'toplevel_page_' . self::PAGE_SLUG !== $hook ) {
			return;
		}
		wp_enqueue_style( 'tools-adapter-admin-settings', TOOLS_ADAPTER_URL . 'assets/css/admin-settings.css', [], TOOLS_ADAPTER_VERSION );
		wp_enqueue_script( 'tools-adapter-admin-settings', TOOLS_ADAPTER_URL . 'assets/js/admin-settings.js', [], TOOLS_ADAPTER_VERSION, true );
	}

	/**
	 * Register the option with the Settings API (nonce, capability check,
	 * sanitization and persistence are all handled by WordPress core).
	 */
	public function register_setting() {
		register_setting(
			self::GROUP,
			self::OPTION_KEY,
			[
				'type'              => 'array',
				'sanitize_callback' => [ __CLASS__, 'sanitize' ],
				'default'           => self::get_defaults(),
			]
		);
	}

	/**
	 * Sanitize the posted settings against the known widgets/features map
	 * (unknown keys are dropped, missing keys default to disabled).
	 *
	 * @param mixed $input Raw posted value.
	 * @return array
	 */
	public static function sanitize( $input ) {
		$defaults = self::get_defaults();
		$input    = is_array( $input ) ? $input : [];

		$clean = [ 'widgets' => [], 'features' => [] ];

		$posted_widgets = isset( $input['widgets'] ) && is_array( $input['widgets'] ) ? $input['widgets'] : [];
		foreach ( $defaults['widgets'] as $slug => $_default ) {
			$clean['widgets'][ $slug ] = ! empty( $posted_widgets[ $slug ] ) ? 'yes' : 'no';
		}

		$posted_features = isset( $input['features'] ) && is_array( $input['features'] ) ? $input['features'] : [];
		foreach ( $defaults['features'] as $key => $_default ) {
			$clean['features'][ $key ] = ! empty( $posted_features[ $key ] ) ? 'yes' : 'no';
		}

		add_settings_error(
			self::OPTION_KEY,
			'tools_adapter_settings_updated',
			esc_html__( 'Réglages enregistrés avec succès.', 'tools-adapter' ),
			'updated'
		);

		return $clean;
	}

	/**
	 * Default settings: every widget & feature enabled (fully backward
	 * compatible with installs predating this settings page).
	 *
	 * @return array{widgets:array<string,string>,features:array<string,string>}
	 */
	public static function get_defaults() {
		$widgets = [];
		foreach ( self::get_widgets_map() as $group ) {
			foreach ( $group['items'] as $slug => $item ) {
				$widgets[ $slug ] = 'yes';
			}
		}

		return [
			'widgets'  => $widgets,
			'features' => [
				'quick_view'         => 'yes',
				'variation_swatches' => 'yes',
			],
		];
	}

	/**
	 * Merge the stored option on top of the defaults, so any widget added
	 * in a future plugin update is automatically enabled for existing sites.
	 *
	 * @return array{widgets:array<string,string>,features:array<string,string>}
	 */
	public static function get_settings() {
		$saved    = get_option( self::OPTION_KEY, [] );
		$saved    = is_array( $saved ) ? $saved : [];
		$settings = self::get_defaults();

		if ( isset( $saved['widgets'] ) && is_array( $saved['widgets'] ) ) {
			foreach ( $saved['widgets'] as $slug => $value ) {
				if ( isset( $settings['widgets'][ $slug ] ) ) {
					$settings['widgets'][ $slug ] = ( 'yes' === $value ) ? 'yes' : 'no';
				}
			}
		}

		if ( isset( $saved['features'] ) && is_array( $saved['features'] ) ) {
			foreach ( $saved['features'] as $key => $value ) {
				if ( isset( $settings['features'][ $key ] ) ) {
					$settings['features'][ $key ] = ( 'yes' === $value ) ? 'yes' : 'no';
				}
			}
		}

		return $settings;
	}

	/**
	 * Whether a given Elementor widget (by its get_name() slug) is enabled.
	 *
	 * @param string $slug Widget slug, e.g. "tools-adapter-hero-banner".
	 * @return bool
	 */
	public static function is_widget_enabled( $slug ) {
		$settings = self::get_settings();
		return ! isset( $settings['widgets'][ $slug ] ) || 'yes' === $settings['widgets'][ $slug ];
	}

	/**
	 * Whether a global (non per-widget) feature is enabled.
	 *
	 * @param string $key Feature key, e.g. "quick_view".
	 * @return bool
	 */
	public static function is_feature_enabled( $key ) {
		$settings = self::get_settings();
		return ! isset( $settings['features'][ $key ] ) || 'yes' === $settings['features'][ $key ];
	}

	/**
	 * Full catalogue of widgets grouped by phase, used both to build the
	 * settings page and to compute the defaults.
	 *
	 * @return array<string,array{label:string,items:array<string,array{label:string,description:string,requires_woo:bool}>}>
	 */
	public static function get_widgets_map() {
		if ( null !== self::$widgets_map ) {
			return self::$widgets_map;
		}

		self::$widgets_map = [
			'layout'    => [
				'label' => esc_html__( 'Mise en page générale', 'tools-adapter' ),
				'items' => [
					'tools-adapter-hero-banner'   => [ 'label' => esc_html__( 'Hero / Bannière', 'tools-adapter' ), 'description' => esc_html__( 'Section d\'accroche plein écran : titre, description, boutons, fond image/couleur/dégradé.', 'tools-adapter' ), 'requires_woo' => false ],
					'tools-adapter-cta-band'      => [ 'label' => esc_html__( 'Bande CTA', 'tools-adapter' ), 'description' => esc_html__( 'Bloc pleine largeur mettant en avant un appel à l\'action.', 'tools-adapter' ), 'requires_woo' => false ],
					'tools-adapter-stats'         => [ 'label' => esc_html__( 'Compteurs / Statistiques', 'tools-adapter' ), 'description' => esc_html__( 'Chiffres clés animés au défilement.', 'tools-adapter' ), 'requires_woo' => false ],
					'tools-adapter-logos'         => [ 'label' => esc_html__( 'Logos partenaires', 'tools-adapter' ), 'description' => esc_html__( 'Grille statique ou défilement continu des logos clients/partenaires.', 'tools-adapter' ), 'requires_woo' => false ],
					'tools-adapter-faq'           => [ 'label' => esc_html__( 'FAQ Accordéon', 'tools-adapter' ), 'description' => esc_html__( 'Questions/réponses repliables avec données structurées SEO (FAQPage).', 'tools-adapter' ), 'requires_woo' => false ],
					'tools-adapter-trust-badges'  => [ 'label' => esc_html__( 'Bloc réassurance', 'tools-adapter' ), 'description' => esc_html__( 'Icônes de réassurance : livraison, paiement sécurisé, retours…', 'tools-adapter' ), 'requires_woo' => false ],
					'tools-adapter-category-banner' => [ 'label' => esc_html__( 'Bannière catégorie', 'tools-adapter' ), 'description' => esc_html__( 'Hero pour une page de catégorie WooCommerce (image, description, compteur, CTA).', 'tools-adapter' ), 'requires_woo' => true ],
					'tools-adapter-icon-box'        => [ 'label' => esc_html__( 'Boîte d\'icône', 'tools-adapter' ), 'description' => esc_html__( 'Carte avec badge d\'icône flottant et fond décoratif décalé.', 'tools-adapter' ), 'requires_woo' => false ],
				],
			],
			'content'   => [
				'label' => esc_html__( 'Contenu & preuve sociale', 'tools-adapter' ),
				'items' => [
					'tools-adapter-project-gallery' => [ 'label' => esc_html__( 'Galerie Projets Mosaïque', 'tools-adapter' ), 'description' => esc_html__( 'Grille Bento 6 cadres avec diaporama en fondu (FADE) par projet.', 'tools-adapter' ), 'requires_woo' => false ],
					'tools-adapter-process-steps'   => [ 'label' => esc_html__( 'Étapes / Processus', 'tools-adapter' ), 'description' => esc_html__( 'Déroulement étape par étape avec ligne de connexion et badge Icône ou Numéro.', 'tools-adapter' ), 'requires_woo' => false ],
					'tools-adapter-testimonials'    => [ 'label' => esc_html__( 'Témoignages', 'tools-adapter' ), 'description' => esc_html__( 'Avis clients en grille ou carrousel (photo, note, citation).', 'tools-adapter' ), 'requires_woo' => false ],
					'tools-adapter-team'            => [ 'label' => esc_html__( 'Équipe', 'tools-adapter' ), 'description' => esc_html__( 'Grille de membres avec photo, poste, bio et réseaux sociaux.', 'tools-adapter' ), 'requires_woo' => false ],
					'tools-adapter-pricing-table'   => [ 'label' => esc_html__( 'Tableau de tarifs', 'tools-adapter' ), 'description' => esc_html__( 'Carte de plan : prix, fonctionnalités, bouton, ruban « populaire ».', 'tools-adapter' ), 'requires_woo' => false ],
					'tools-adapter-timeline'        => [ 'label' => esc_html__( 'Timeline', 'tools-adapter' ), 'description' => esc_html__( 'Frise chronologique verticale, alternée ou en colonne unique.', 'tools-adapter' ), 'requires_woo' => false ],
					'tools-adapter-before-after'    => [ 'label' => esc_html__( 'Avant / Après', 'tools-adapter' ), 'description' => esc_html__( 'Slider comparatif de deux images.', 'tools-adapter' ), 'requires_woo' => false ],
					'tools-adapter-toc'             => [ 'label' => esc_html__( 'Table des matières', 'tools-adapter' ), 'description' => esc_html__( 'Sommaire auto-généré à partir des titres de la page.', 'tools-adapter' ), 'requires_woo' => false ],
					'tools-adapter-reading-progress' => [ 'label' => esc_html__( 'Barre de progression', 'tools-adapter' ), 'description' => esc_html__( 'Barre fixe indiquant l\'avancement de lecture de la page.', 'tools-adapter' ), 'requires_woo' => false ],
				],
			],
			'shop'      => [
				'label' => esc_html__( 'Catalogue & boutique', 'tools-adapter' ),
				'items' => [
					'tools-adapter-product-categories' => [ 'label' => esc_html__( 'Catégories Produits', 'tools-adapter' ), 'description' => esc_html__( 'Navigation par catégories WooCommerce (carte ou cercle).', 'tools-adapter' ), 'requires_woo' => true ],
					'tools-adapter-price-filter'        => [ 'label' => esc_html__( 'Filtre Prix', 'tools-adapter' ), 'description' => esc_html__( 'Slider de plage de prix avec filtrage AJAX sans rechargement.', 'tools-adapter' ), 'requires_woo' => true ],
					'tools-adapter-product-grid'        => [ 'label' => esc_html__( 'Grille Produits', 'tools-adapter' ), 'description' => esc_html__( 'Grille responsive de produits WooCommerce.', 'tools-adapter' ), 'requires_woo' => true ],
					'tools-adapter-product-carousel'    => [ 'label' => esc_html__( 'Carrousel Produits', 'tools-adapter' ), 'description' => esc_html__( 'Carrousel de produits avec contrôles Elementor.', 'tools-adapter' ), 'requires_woo' => true ],
					'tools-adapter-product-archive'     => [ 'label' => esc_html__( 'Archive Produits', 'tools-adapter' ), 'description' => esc_html__( 'Liste filtrable avec pagination AJAX et types de pagination personnalisables.', 'tools-adapter' ), 'requires_woo' => true ],
					'tools-adapter-mini-cart'            => [ 'label' => esc_html__( 'Mini-panier', 'tools-adapter' ), 'description' => esc_html__( 'Icône panier + dropdown AJAX (articles, sous-total, retrait).', 'tools-adapter' ), 'requires_woo' => true ],
					'tools-adapter-sticky-atc'           => [ 'label' => esc_html__( 'Barre panier collante', 'tools-adapter' ), 'description' => esc_html__( 'Barre fixe en bas de page produit pour l\'ajout au panier.', 'tools-adapter' ), 'requires_woo' => true ],
					'tools-adapter-size-guide'           => [ 'label' => esc_html__( 'Guide des tailles', 'tools-adapter' ), 'description' => esc_html__( 'Bouton ouvrant un tableau de correspondance des tailles.', 'tools-adapter' ), 'requires_woo' => true ],
				],
			],
			'discovery' => [
				'label' => esc_html__( 'Découverte & filtrage', 'tools-adapter' ),
				'items' => [
					'tools-adapter-attribute-filter' => [ 'label' => esc_html__( 'Filtre par attributs', 'tools-adapter' ), 'description' => esc_html__( 'Pastilles/pilules pour filtrer par attribut WooCommerce (couleur, taille…).', 'tools-adapter' ), 'requires_woo' => true ],
					'tools-adapter-brands'            => [ 'label' => esc_html__( 'Marques', 'tools-adapter' ), 'description' => esc_html__( 'Grille de logos de marques.', 'tools-adapter' ), 'requires_woo' => true ],
					'tools-adapter-recently-viewed'   => [ 'label' => esc_html__( 'Produits récemment consultés', 'tools-adapter' ), 'description' => esc_html__( 'Historique client-side (localStorage) rendu en cartes produit.', 'tools-adapter' ), 'requires_woo' => true ],
					'tools-adapter-sale-countdown'    => [ 'label' => esc_html__( 'Compte à rebours promo', 'tools-adapter' ), 'description' => esc_html__( 'Minuteur configurable (date fixe ou fin de promo produit).', 'tools-adapter' ), 'requires_woo' => true ],
					'tools-adapter-stock-urgency'     => [ 'label' => esc_html__( 'Barre de stock / urgence', 'tools-adapter' ), 'description' => esc_html__( 'Message + barre de progression selon le stock restant.', 'tools-adapter' ), 'requires_woo' => true ],
				],
			],
			'sitewide'  => [
				'label' => esc_html__( 'Site & navigation', 'tools-adapter' ),
				'items' => [
					'tools-adapter-mega-menu'     => [ 'label' => esc_html__( 'Mega Menu', 'tools-adapter' ), 'description' => esc_html__( 'Menu de navigation horizontal avec panneaux mega-menu, option collante.', 'tools-adapter' ), 'requires_woo' => false ],
					'tools-adapter-cookie-banner' => [ 'label' => esc_html__( 'Bandeau cookies (RGPD)', 'tools-adapter' ), 'description' => esc_html__( 'Bannière de consentement avec catégories de cookies personnalisables.', 'tools-adapter' ), 'requires_woo' => false ],
					'tools-adapter-social-proof'  => [ 'label' => esc_html__( 'Popup preuve sociale', 'tools-adapter' ), 'description' => esc_html__( 'Notifications flottantes — messages personnalisés ou ventes récentes.', 'tools-adapter' ), 'requires_woo' => false ],
					'tools-adapter-contact-form'  => [ 'label' => esc_html__( 'Formulaire de contact', 'tools-adapter' ), 'description' => esc_html__( 'Formulaire stylisé avec envoi AJAX par e-mail et anti-spam.', 'tools-adapter' ), 'requires_woo' => false ],
					'tools-adapter-google-map'    => [ 'label' => esc_html__( 'Carte Google Maps', 'tools-adapter' ), 'description' => esc_html__( 'Intégration par adresse ou code d\'intégration, carte d\'infos flottante.', 'tools-adapter' ), 'requires_woo' => false ],
					'tools-adapter-blog-grid'     => [ 'label' => esc_html__( 'Grille de blog', 'tools-adapter' ), 'description' => esc_html__( 'Grille personnalisable d\'articles WordPress.', 'tools-adapter' ), 'requires_woo' => false ],
				],
			],
		];

		return self::$widgets_map;
	}

	/**
	 * Global (non per-widget) toggleable features.
	 *
	 * @return array<string,array{label:string,description:string,requires_woo:bool}>
	 */
	public static function get_features_map() {
		return [
			'quick_view'         => [
				'label'        => esc_html__( 'Vue rapide produit', 'tools-adapter' ),
				'description'  => esc_html__( 'Bouton « œil » sur les cartes produit (Grille, Carrousel, Archive) ouvrant une fiche AJAX en modale. Se désactive aussi via le réglage du widget concerné.', 'tools-adapter' ),
				'requires_woo' => true,
			],
			'variation_swatches' => [
				'label'        => esc_html__( 'Sélecteur de variations visuel', 'tools-adapter' ),
				'description'  => esc_html__( 'Remplace les listes déroulantes WooCommerce par des pastilles de couleur / pilules de texte sur les pages produit variable.', 'tools-adapter' ),
				'requires_woo' => true,
			],
		];
	}

	/**
	 * Count enabled/total widgets, overall and per group.
	 *
	 * @return array
	 */
	private static function get_counts( array $settings ) {
		$total   = 0;
		$enabled = 0;
		$groups  = [];

		foreach ( self::get_widgets_map() as $group_key => $group ) {
			$group_total   = count( $group['items'] );
			$group_enabled = 0;
			foreach ( $group['items'] as $slug => $item ) {
				if ( 'yes' === ( $settings['widgets'][ $slug ] ?? 'yes' ) ) {
					++$group_enabled;
				}
			}
			$groups[ $group_key ] = [ 'enabled' => $group_enabled, 'total' => $group_total ];
			$total   += $group_total;
			$enabled += $group_enabled;
		}

		return [ 'enabled' => $enabled, 'total' => $total, 'groups' => $groups ];
	}

	/**
	 * Render the full settings page.
	 */
	public function render_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$settings = self::get_settings();
		$counts   = self::get_counts( $settings );
		$has_woo  = class_exists( 'WooCommerce' );
		?>
		<div class="wrap ta-settings">
			<?php settings_errors( self::OPTION_KEY ); ?>
			<form method="post" action="options.php" class="ta-settings__form" data-ta-settings-form>
				<?php settings_fields( self::GROUP ); ?>

				<div class="ta-settings__header">
					<div class="ta-settings__header-main">
						<span class="ta-settings__logo" aria-hidden="true">
							<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M9 3v5M15 3v5M6 8h12l-1 4a5 5 0 0 1-5 4h0a5 5 0 0 1-5-4L6 8Z"/><path d="M12 16v3M9.5 21h5"/></svg>
						</span>
						<div>
							<h1><?php echo esc_html__( 'Tools Adapter', 'tools-adapter' ); ?> <span class="ta-settings__version">v<?php echo esc_html( TOOLS_ADAPTER_VERSION ); ?></span></h1>
							<p class="ta-settings__tagline"><?php echo esc_html__( 'Activez uniquement les widgets Elementor dont vous avez besoin. Les widgets désactivés disparaissent du panneau Elementor et n\'ajoutent plus aucun style ni script sur le site.', 'tools-adapter' ); ?></p>
						</div>
					</div>
					<div class="ta-settings__header-actions">
						<span class="ta-settings__counter" data-ta-global-counter>
							<strong><?php echo esc_html( (string) $counts['enabled'] ); ?></strong> / <?php echo esc_html( (string) $counts['total'] ); ?> <?php echo esc_html__( 'widgets activés', 'tools-adapter' ); ?>
						</span>
						<button type="submit" class="button button-primary button-hero" data-ta-save>
							<?php echo esc_html__( 'Enregistrer les modifications', 'tools-adapter' ); ?>
						</button>
					</div>
				</div>

				<?php if ( ! $has_woo ) : ?>
					<div class="notice notice-warning inline ta-settings__notice">
						<p><?php echo esc_html__( 'WooCommerce n\'est pas actif : les widgets marqués « WooCommerce » resteront visibles ici mais ne fonctionneront pas tant que WooCommerce n\'est pas installé et activé.', 'tools-adapter' ); ?></p>
					</div>
				<?php endif; ?>

				<div class="ta-settings__toolbar">
					<div class="ta-settings__search">
						<span class="dashicons dashicons-search" aria-hidden="true"></span>
						<input type="search" placeholder="<?php echo esc_attr__( 'Rechercher un widget…', 'tools-adapter' ); ?>" data-ta-search />
					</div>
					<div class="ta-settings__bulk-actions">
						<button type="button" class="button" data-ta-toggle-all="on"><?php echo esc_html__( 'Tout activer', 'tools-adapter' ); ?></button>
						<button type="button" class="button" data-ta-toggle-all="off"><?php echo esc_html__( 'Tout désactiver', 'tools-adapter' ); ?></button>
					</div>
				</div>

				<div class="ta-settings__layout">
					<nav class="ta-settings__nav" aria-label="<?php echo esc_attr__( 'Sections', 'tools-adapter' ); ?>">
						<?php foreach ( self::get_widgets_map() as $group_key => $group ) : ?>
							<a href="#ta-group-<?php echo esc_attr( $group_key ); ?>" class="ta-settings__nav-link">
								<span><?php echo esc_html( $group['label'] ); ?></span>
								<span class="ta-settings__nav-count" data-ta-group-counter="<?php echo esc_attr( $group_key ); ?>">
									<?php echo esc_html( $counts['groups'][ $group_key ]['enabled'] . '/' . $counts['groups'][ $group_key ]['total'] ); ?>
								</span>
							</a>
						<?php endforeach; ?>
						<a href="#ta-group-features" class="ta-settings__nav-link">
							<span><?php echo esc_html__( 'Fonctionnalités globales', 'tools-adapter' ); ?></span>
						</a>
					</nav>

					<div class="ta-settings__content">
						<?php foreach ( self::get_widgets_map() as $group_key => $group ) : ?>
							<section class="ta-settings__card" id="ta-group-<?php echo esc_attr( $group_key ); ?>" data-ta-group>
								<header class="ta-settings__card-header">
									<h2><?php echo esc_html( $group['label'] ); ?></h2>
									<div class="ta-settings__card-header-actions">
										<span class="ta-settings__nav-count" data-ta-group-counter="<?php echo esc_attr( $group_key ); ?>">
											<?php echo esc_html( $counts['groups'][ $group_key ]['enabled'] . '/' . $counts['groups'][ $group_key ]['total'] ); ?>
										</span>
										<button type="button" class="button-link" data-ta-toggle-group="on"><?php echo esc_html__( 'Tout activer', 'tools-adapter' ); ?></button>
										<span aria-hidden="true">·</span>
										<button type="button" class="button-link" data-ta-toggle-group="off"><?php echo esc_html__( 'Tout désactiver', 'tools-adapter' ); ?></button>
									</div>
								</header>
								<div class="ta-settings__items">
									<?php foreach ( $group['items'] as $slug => $item ) :
										$checked = 'yes' === ( $settings['widgets'][ $slug ] ?? 'yes' );
										?>
										<label class="ta-settings__item" data-ta-item data-ta-search-text="<?php echo esc_attr( strtolower( $item['label'] . ' ' . $item['description'] ) ); ?>">
											<span class="ta-settings__switch">
												<input
													type="checkbox"
													name="<?php echo esc_attr( self::OPTION_KEY ); ?>[widgets][<?php echo esc_attr( $slug ); ?>]"
													value="1"
													<?php checked( $checked ); ?>
													data-ta-checkbox
												/>
												<span class="ta-settings__switch-track" aria-hidden="true"></span>
											</span>
											<span class="ta-settings__item-body">
												<span class="ta-settings__item-title">
													<?php echo esc_html( $item['label'] ); ?>
													<?php if ( $item['requires_woo'] ) : ?>
														<span class="ta-settings__badge">WooCommerce</span>
													<?php endif; ?>
												</span>
												<span class="ta-settings__item-desc"><?php echo esc_html( $item['description'] ); ?></span>
											</span>
										</label>
									<?php endforeach; ?>
								</div>
							</section>
						<?php endforeach; ?>

						<section class="ta-settings__card" id="ta-group-features" data-ta-group>
							<header class="ta-settings__card-header">
								<h2><?php echo esc_html__( 'Fonctionnalités globales', 'tools-adapter' ); ?></h2>
							</header>
							<p class="ta-settings__card-intro"><?php echo esc_html__( 'Ces fonctionnalités ne sont pas des widgets à glisser-déposer : elles s\'appliquent automatiquement sur les pages concernées.', 'tools-adapter' ); ?></p>
							<div class="ta-settings__items">
								<?php foreach ( self::get_features_map() as $key => $item ) :
									$checked = 'yes' === ( $settings['features'][ $key ] ?? 'yes' );
									?>
									<label class="ta-settings__item" data-ta-item data-ta-search-text="<?php echo esc_attr( strtolower( $item['label'] . ' ' . $item['description'] ) ); ?>">
										<span class="ta-settings__switch">
											<input
												type="checkbox"
												name="<?php echo esc_attr( self::OPTION_KEY ); ?>[features][<?php echo esc_attr( $key ); ?>]"
												value="1"
												<?php checked( $checked ); ?>
												data-ta-checkbox
											/>
											<span class="ta-settings__switch-track" aria-hidden="true"></span>
										</span>
										<span class="ta-settings__item-body">
											<span class="ta-settings__item-title">
												<?php echo esc_html( $item['label'] ); ?>
												<?php if ( $item['requires_woo'] ) : ?>
													<span class="ta-settings__badge">WooCommerce</span>
												<?php endif; ?>
											</span>
											<span class="ta-settings__item-desc"><?php echo esc_html( $item['description'] ); ?></span>
										</span>
									</label>
								<?php endforeach; ?>
							</div>
						</section>
					</div>
				</div>

				<div class="ta-settings__footer">
					<button type="submit" class="button button-primary button-hero" data-ta-save>
						<?php echo esc_html__( 'Enregistrer les modifications', 'tools-adapter' ); ?>
					</button>
				</div>
			</form>
		</div>
		<?php
	}
}
