<?php
namespace ToolsAdapter\HeaderFooter;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles registration and admin management for the "ta_header_footer" custom post type.
 */
class Post_Type {

	const POST_TYPE = 'ta_header_footer';

	public function __construct() {
		add_action( 'init', [ $this, 'register_post_type' ] );
		add_action( 'admin_menu', [ $this, 'register_admin_submenus' ], 20 );
		add_action( 'add_meta_boxes', [ $this, 'register_meta_box' ] );
		add_action( 'save_post_' . self::POST_TYPE, [ $this, 'save_meta_box' ] );
		add_filter( 'manage_' . self::POST_TYPE . '_posts_columns', [ $this, 'filter_admin_columns' ] );
		add_action( 'manage_' . self::POST_TYPE . '_posts_custom_column', [ $this, 'render_admin_columns' ], 10, 2 );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_assets' ] );
		add_filter( 'post_row_actions', [ $this, 'filter_row_actions' ], 10, 2 );

		// Ensure Elementor enables editor on ta_header_footer.
		add_action( 'elementor/init', [ $this, 'add_elementor_support' ] );
	}

	/**
	 * Register the CPT for Header & Footer templates.
	 */
	public function register_post_type() {
		$labels = [
			'name'               => _x( 'Modèles Header & Footer', 'post type general name', 'tools-adapter' ),
			'singular_name'      => _x( 'Modèle Header/Footer', 'post type singular name', 'tools-adapter' ),
			'menu_name'          => _x( 'Header & Footer', 'admin menu', 'tools-adapter' ),
			'name_admin_bar'     => _x( 'Modèle Header/Footer', 'add new on admin bar', 'tools-adapter' ),
			'add_new'            => __( 'Ajouter un modèle', 'tools-adapter' ),
			'add_new_item'       => __( 'Ajouter un modèle Header/Footer', 'tools-adapter' ),
			'new_item'           => __( 'Nouveau modèle', 'tools-adapter' ),
			'edit_item'          => __( 'Modifier le modèle', 'tools-adapter' ),
			'view_item'          => __( 'Voir le modèle', 'tools-adapter' ),
			'all_items'          => __( 'Tous les modèles', 'tools-adapter' ),
			'search_items'       => __( 'Rechercher des modèles', 'tools-adapter' ),
			'not_found'          => __( 'Aucun modèle trouvé.', 'tools-adapter' ),
			'not_found_in_trash' => __( 'Aucun modèle dans la corbeille.', 'tools-adapter' ),
		];

		$args = [
			'labels'              => $labels,
			'public'              => true,
			'has_archive'         => false,
			'show_ui'             => true,
			'show_in_menu'        => false, // We attach it under Tools Adapter manually.
			'show_in_nav_menus'   => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => true, // Required for Elementor preview iframe.
			'capability_type'     => 'post',
			'hierarchical'        => false,
			'supports'            => [ 'title', 'elementor' ],
			'rewrite'             => false,
		];

		register_post_type( self::POST_TYPE, $args );
	}

	/**
	 * Attach sub-menus under "Tools Adapter".
	 */
	public function register_admin_submenus() {
		add_submenu_page(
			'tools-adapter-settings',
			__( 'Header & Footer Builder', 'tools-adapter' ),
			__( 'Header & Footer', 'tools-adapter' ),
			'manage_options',
			'edit.php?post_type=' . self::POST_TYPE
		);

		add_submenu_page(
			'tools-adapter-settings',
			__( 'Ajouter un modèle', 'tools-adapter' ),
			__( 'Ajouter un modèle', 'tools-adapter' ),
			'manage_options',
			'post-new.php?post_type=' . self::POST_TYPE
		);
	}

	/**
	 * Tell Elementor that ta_header_footer supports the Elementor page builder.
	 */
	public function add_elementor_support() {
		add_post_type_support( self::POST_TYPE, 'elementor' );

		// Auto-enable CPT in Elementor's option if not yet present.
		$cpt_support = get_option( 'elementor_cpt_support', [ 'page', 'post' ] );
		if ( is_array( $cpt_support ) && ! in_array( self::POST_TYPE, $cpt_support, true ) ) {
			$cpt_support[] = self::POST_TYPE;
			update_option( 'elementor_cpt_support', $cpt_support );
		}
	}

	/**
	 * Enqueue admin scripts & styles on our CPT edit screen.
	 *
	 * @param string $hook Page hook.
	 */
	public function enqueue_admin_assets( $hook ) {
		global $post_type;

		if ( self::POST_TYPE !== $post_type ) {
			return;
		}

		wp_enqueue_style(
			'tools-adapter-admin-hf',
			TOOLS_ADAPTER_URL . 'assets/css/admin-header-footer.css',
			[],
			TOOLS_ADAPTER_VERSION
		);

		wp_enqueue_script(
			'tools-adapter-admin-hf',
			TOOLS_ADAPTER_URL . 'assets/js/admin-header-footer.js',
			[ 'jquery' ],
			TOOLS_ADAPTER_VERSION,
			true
		);

		wp_localize_script(
			'tools-adapter-admin-hf',
			'ToolsAdapterHF',
			[
				'copiedText' => __( 'Copié !', 'tools-adapter' ),
			]
		);
	}

	/**
	 * Register meta box for template settings.
	 */
	public function register_meta_box() {
		add_meta_box(
			'ta_header_footer_settings',
			__( '⚙️ Paramètres du modèle — Tools Adapter', 'tools-adapter' ),
			[ $this, 'render_meta_box' ],
			self::POST_TYPE,
			'normal',
			'high'
		);
	}

	/**
	 * Render settings meta box.
	 *
	 * @param \WP_Post $post Current post.
	 */
	public function render_meta_box( $post ) {
		wp_nonce_field( 'ta_hf_meta_box', 'ta_hf_meta_box_nonce' );

		$template_type = get_post_meta( $post->ID, '_ta_hf_template_type', true ) ?: 'header';
		$display_on    = get_post_meta( $post->ID, '_ta_hf_display_on', true ) ?: 'entire_site';
		$specific_ids  = get_post_meta( $post->ID, '_ta_hf_specific_ids', true ) ?: '';
		$exclude_on    = get_post_meta( $post->ID, '_ta_hf_exclude_on', true ) ?: 'none';
		$exclude_ids   = get_post_meta( $post->ID, '_ta_hf_exclude_ids', true ) ?: '';
		$user_roles    = get_post_meta( $post->ID, '_ta_hf_user_roles', true ) ?: 'all';
		$hide_theme_hf = get_post_meta( $post->ID, '_ta_hf_hide_theme_hf', true );
		if ( '' === $hide_theme_hf ) {
			$hide_theme_hf = 'yes'; // Default to hiding theme header/footer.
		}
		$status = get_post_meta( $post->ID, '_ta_hf_status', true ) ?: 'active';
		?>
		<div class="ta-hf-metabox">
			<div class="ta-hf-grid">

				<!-- Type de modèle -->
				<div class="ta-hf-field">
					<label for="ta_hf_template_type" class="ta-hf-label">
						<strong><?php esc_html_e( 'Type de modèle', 'tools-adapter' ); ?></strong>
						<span class="ta-hf-desc"><?php esc_html_e( 'Choisissez le rôle de ce modèle sur votre site.', 'tools-adapter' ); ?></span>
					</label>
					<div class="ta-hf-control">
						<select name="ta_hf_template_type" id="ta_hf_template_type" class="widefat">
							<option value="header" <?php selected( $template_type, 'header' ); ?>><?php esc_html_e( 'En-tête (Header)', 'tools-adapter' ); ?></option>
							<option value="footer" <?php selected( $template_type, 'footer' ); ?>><?php esc_html_e( 'Pied de page (Footer)', 'tools-adapter' ); ?></option>
							<option value="before_footer" <?php selected( $template_type, 'before_footer' ); ?>><?php esc_html_e( 'Avant-pied de page (Before Footer)', 'tools-adapter' ); ?></option>
							<option value="custom" <?php selected( $template_type, 'custom' ); ?>><?php esc_html_e( 'Bloc personnalisé (Shortcode / Hook)', 'tools-adapter' ); ?></option>
						</select>
					</div>
				</div>

				<!-- Statut -->
				<div class="ta-hf-field">
					<label for="ta_hf_status" class="ta-hf-label">
						<strong><?php esc_html_e( 'Statut du modèle', 'tools-adapter' ); ?></strong>
						<span class="ta-hf-desc"><?php esc_html_e( 'Activez ou suspendez l\'application de ce modèle.', 'tools-adapter' ); ?></span>
					</label>
					<div class="ta-hf-control">
						<select name="ta_hf_status" id="ta_hf_status" class="widefat">
							<option value="active" <?php selected( $status, 'active' ); ?>><?php esc_html_e( '✅ Actif (affiché selon conditions)', 'tools-adapter' ); ?></option>
							<option value="inactive" <?php selected( $status, 'inactive' ); ?>><?php esc_html_e( '⏸️ Inactif (désactivé)', 'tools-adapter' ); ?></option>
						</select>
					</div>
				</div>

				<!-- Condition d'affichage -->
				<div class="ta-hf-field">
					<label for="ta_hf_display_on" class="ta-hf-label">
						<strong><?php esc_html_e( 'Afficher sur', 'tools-adapter' ); ?></strong>
						<span class="ta-hf-desc"><?php esc_html_e( 'Définissez où ce modèle doit s\'afficher.', 'tools-adapter' ); ?></span>
					</label>
					<div class="ta-hf-control">
						<select name="ta_hf_display_on" id="ta_hf_display_on" class="widefat">
							<option value="entire_site" <?php selected( $display_on, 'entire_site' ); ?>><?php esc_html_e( '🌐 Tout le site', 'tools-adapter' ); ?></option>
							<option value="home" <?php selected( $display_on, 'home' ); ?>><?php esc_html_e( '🏠 Page d\'accueil uniquement', 'tools-adapter' ); ?></option>
							<option value="all_pages" <?php selected( $display_on, 'all_pages' ); ?>><?php esc_html_e( '📄 Toutes les pages', 'tools-adapter' ); ?></option>
							<option value="all_posts" <?php selected( $display_on, 'all_posts' ); ?>><?php esc_html_e( '✍️ Tous les articles de blog', 'tools-adapter' ); ?></option>
							<option value="archives" <?php selected( $display_on, 'archives' ); ?>><?php esc_html_e( '📚 Toutes les archives (catégories, tags, dates)', 'tools-adapter' ); ?></option>
							<?php if ( class_exists( 'WooCommerce' ) ) : ?>
								<option value="woocommerce" <?php selected( $display_on, 'woocommerce' ); ?>><?php esc_html_e( '🛍️ Boutique & Produits WooCommerce', 'tools-adapter' ); ?></option>
							<?php endif; ?>
							<option value="404" <?php selected( $display_on, '404' ); ?>><?php esc_html_e( '🚫 Page 404 (Erreur)', 'tools-adapter' ); ?></option>
							<option value="specific" <?php selected( $display_on, 'specific' ); ?>><?php esc_html_e( '🎯 Pages / Articles spécifiques (par ID)', 'tools-adapter' ); ?></option>
						</select>
					</div>
				</div>

				<!-- IDs spécifiques d'affichage -->
				<div class="ta-hf-field ta-hf-conditional" id="ta_hf_field_specific_ids">
					<label for="ta_hf_specific_ids" class="ta-hf-label">
						<strong><?php esc_html_e( 'IDs des pages/articles ciblés', 'tools-adapter' ); ?></strong>
						<span class="ta-hf-desc"><?php esc_html_e( 'Séparez plusieurs identifiants par une virgule (ex: 12, 45, 108).', 'tools-adapter' ); ?></span>
					</label>
					<div class="ta-hf-control">
						<input type="text" name="ta_hf_specific_ids" id="ta_hf_specific_ids" value="<?php echo esc_attr( $specific_ids ); ?>" class="widefat" placeholder="ex: 42, 89">
					</div>
				</div>

				<!-- Condition d'exclusion -->
				<div class="ta-hf-field">
					<label for="ta_hf_exclude_on" class="ta-hf-label">
						<strong><?php esc_html_e( 'Ne pas afficher sur (Exclusion)', 'tools-adapter' ); ?></strong>
						<span class="ta-hf-desc"><?php esc_html_e( 'Règle optionnelle pour exclure certaines pages.', 'tools-adapter' ); ?></span>
					</label>
					<div class="ta-hf-control">
						<select name="ta_hf_exclude_on" id="ta_hf_exclude_on" class="widefat">
							<option value="none" <?php selected( $exclude_on, 'none' ); ?>><?php esc_html_e( '— Aucune exclusion —', 'tools-adapter' ); ?></option>
							<option value="home" <?php selected( $exclude_on, 'home' ); ?>><?php esc_html_e( '🏠 Page d\'accueil', 'tools-adapter' ); ?></option>
							<option value="404" <?php selected( $exclude_on, '404' ); ?>><?php esc_html_e( '🚫 Page 404', 'tools-adapter' ); ?></option>
							<?php if ( class_exists( 'WooCommerce' ) ) : ?>
								<option value="woocommerce" <?php selected( $exclude_on, 'woocommerce' ); ?>><?php esc_html_e( '🛍️ Pages WooCommerce', 'tools-adapter' ); ?></option>
							<?php endif; ?>
							<option value="specific" <?php selected( $exclude_on, 'specific' ); ?>><?php esc_html_e( '🎯 Pages / Articles spécifiques (par ID)', 'tools-adapter' ); ?></option>
						</select>
					</div>
				</div>

				<!-- IDs spécifiques d'exclusion -->
				<div class="ta-hf-field ta-hf-conditional" id="ta_hf_field_exclude_ids">
					<label for="ta_hf_exclude_ids" class="ta-hf-label">
						<strong><?php esc_html_e( 'IDs des pages/articles exclus', 'tools-adapter' ); ?></strong>
						<span class="ta-hf-desc"><?php esc_html_e( 'Séparez plusieurs identifiants par une virgule (ex: 15, 99).', 'tools-adapter' ); ?></span>
					</label>
					<div class="ta-hf-control">
						<input type="text" name="ta_hf_exclude_ids" id="ta_hf_exclude_ids" value="<?php echo esc_attr( $exclude_ids ); ?>" class="widefat" placeholder="ex: 15, 99">
					</div>
				</div>

				<!-- Rôles utilisateurs -->
				<div class="ta-hf-field">
					<label for="ta_hf_user_roles" class="ta-hf-label">
						<strong><?php esc_html_e( 'Afficher selon les utilisateurs', 'tools-adapter' ); ?></strong>
						<span class="ta-hf-desc"><?php esc_html_e( 'Permet de créer un en-tête dédié aux membres connectés par exemple.', 'tools-adapter' ); ?></span>
					</label>
					<div class="ta-hf-control">
						<select name="ta_hf_user_roles" id="ta_hf_user_roles" class="widefat">
							<option value="all" <?php selected( $user_roles, 'all' ); ?>><?php esc_html_e( 'Tous les visiteurs', 'tools-adapter' ); ?></option>
							<option value="logged_in" <?php selected( $user_roles, 'logged_in' ); ?>><?php esc_html_e( '👤 Utilisateurs connectés uniquement', 'tools-adapter' ); ?></option>
							<option value="logged_out" <?php selected( $user_roles, 'logged_out' ); ?>><?php esc_html_e( '👥 Visiteurs déconnectés uniquement', 'tools-adapter' ); ?></option>
						</select>
					</div>
				</div>

				<!-- Masquer Header/Footer natif du thème -->
				<div class="ta-hf-field">
					<label class="ta-hf-label">
						<strong><?php esc_html_e( 'Remplacement du thème', 'tools-adapter' ); ?></strong>
						<span class="ta-hf-desc"><?php esc_html_e( 'Masque automatiquement le header/footer natif de votre thème pour éviter les doublons.', 'tools-adapter' ); ?></span>
					</label>
					<div class="ta-hf-control">
						<label class="ta-hf-checkbox">
							<input type="checkbox" name="ta_hf_hide_theme_hf" value="yes" <?php checked( $hide_theme_hf, 'yes' ); ?>>
							<span><?php esc_html_e( 'Masquer l\'élément natif d\'origine du thème', 'tools-adapter' ); ?></span>
						</label>
					</div>
				</div>

				<!-- Shortcode -->
				<div class="ta-hf-field ta-hf-field-full">
					<label class="ta-hf-label">
						<strong><?php esc_html_e( 'Shortcode du modèle', 'tools-adapter' ); ?></strong>
						<span class="ta-hf-desc"><?php esc_html_e( 'Vous pouvez insérer ce modèle n\'importe où dans une page, article ou fichier PHP.', 'tools-adapter' ); ?></span>
					</label>
					<div class="ta-hf-control">
						<div class="ta-hf-shortcode-wrap">
							<input type="text" readonly value="<?php echo esc_attr( '[tools_adapter_template id="' . $post->ID . '"]' ); ?>" class="ta-hf-shortcode-input" onclick="this.select();">
							<button type="button" class="button ta-hf-copy-btn" data-clipboard="<?php echo esc_attr( '[tools_adapter_template id="' . $post->ID . '"]' ); ?>">
								<?php esc_html_e( 'Copier', 'tools-adapter' ); ?>
							</button>
						</div>
					</div>
				</div>

			</div>
		</div>
		<?php
	}

	/**
	 * Save meta box values safely.
	 *
	 * @param int $post_id Post ID.
	 */
	public function save_meta_box( $post_id ) {
		if ( ! isset( $_POST['ta_hf_meta_box_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['ta_hf_meta_box_nonce'] ), 'ta_hf_meta_box' ) ) {
			return;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		$fields = [
			'ta_hf_template_type' => 'sanitize_key',
			'ta_hf_display_on'    => 'sanitize_key',
			'ta_hf_specific_ids'  => 'sanitize_text_field',
			'ta_hf_exclude_on'    => 'sanitize_key',
			'ta_hf_exclude_ids'   => 'sanitize_text_field',
			'ta_hf_user_roles'    => 'sanitize_key',
			'ta_hf_status'        => 'sanitize_key',
		];

		foreach ( $fields as $field => $sanitizer ) {
			if ( isset( $_POST[ $field ] ) ) {
				$val = call_user_func( $sanitizer, $_POST[ $field ] );
				update_post_meta( $post_id, '_' . $field, $val );
			}
		}

		$hide_theme = isset( $_POST['ta_hf_hide_theme_hf'] ) && 'yes' === $_POST['ta_hf_hide_theme_hf'] ? 'yes' : 'no';
		update_post_meta( $post_id, '_ta_hf_hide_theme_hf', $hide_theme );
	}

	/**
	 * Custom columns for admin list table.
	 *
	 * @param array $columns Existing columns.
	 * @return array
	 */
	public function filter_admin_columns( $columns ) {
		$new_columns = [];
		foreach ( $columns as $key => $title ) {
			$new_columns[ $key ] = $title;
			if ( 'title' === $key ) {
				$new_columns['ta_hf_type']      = __( 'Type', 'tools-adapter' );
				$new_columns['ta_hf_display']   = __( 'Conditions d\'affichage', 'tools-adapter' );
				$new_columns['ta_hf_shortcode'] = __( 'Shortcode', 'tools-adapter' );
				$new_columns['ta_hf_status']    = __( 'Statut', 'tools-adapter' );
			}
		}
		return $new_columns;
	}

	/**
	 * Render custom column content.
	 *
	 * @param string $column Column name.
	 * @param int    $post_id Post ID.
	 */
	public function render_admin_columns( $column, $post_id ) {
		switch ( $column ) {
			case 'ta_hf_type':
				$type = get_post_meta( $post_id, '_ta_hf_template_type', true ) ?: 'header';
				$badges = [
					'header'        => [ 'label' => __( 'En-tête (Header)', 'tools-adapter' ), 'class' => 'ta-badge-header' ],
					'footer'        => [ 'label' => __( 'Pied de page (Footer)', 'tools-adapter' ), 'class' => 'ta-badge-footer' ],
					'before_footer' => [ 'label' => __( 'Avant-footer', 'tools-adapter' ), 'class' => 'ta-badge-before-footer' ],
					'custom'        => [ 'label' => __( 'Bloc personnalisé', 'tools-adapter' ), 'class' => 'ta-badge-custom' ],
				];
				$badge = isset( $badges[ $type ] ) ? $badges[ $type ] : [ 'label' => $type, 'class' => 'ta-badge-default' ];
				printf( '<span class="ta-hf-badge %s">%s</span>', esc_attr( $badge['class'] ), esc_html( $badge['label'] ) );
				break;

			case 'ta_hf_display':
				$display = get_post_meta( $post_id, '_ta_hf_display_on', true ) ?: 'entire_site';
				$labels  = [
					'entire_site' => __( '🌐 Tout le site', 'tools-adapter' ),
					'home'        => __( '🏠 Page d\'accueil', 'tools-adapter' ),
					'all_pages'   => __( '📄 Toutes les pages', 'tools-adapter' ),
					'all_posts'   => __( '✍️ Tous les articles', 'tools-adapter' ),
					'archives'    => __( '📚 Toutes les archives', 'tools-adapter' ),
					'woocommerce' => __( '🛍️ WooCommerce', 'tools-adapter' ),
					'404'         => __( '🚫 Page 404', 'tools-adapter' ),
					'specific'    => __( '🎯 Spécifique', 'tools-adapter' ),
				];
				$text = isset( $labels[ $display ] ) ? $labels[ $display ] : $display;
				if ( 'specific' === $display ) {
					$ids = get_post_meta( $post_id, '_ta_hf_specific_ids', true );
					if ( $ids ) {
						$text .= ' (' . esc_html( $ids ) . ')';
					}
				}
				$exclude = get_post_meta( $post_id, '_ta_hf_exclude_on', true );
				if ( $exclude && 'none' !== $exclude ) {
					$text .= ' <br><small style="color:#ef4444;">Exclut : ' . esc_html( $exclude ) . '</small>';
				}
				echo wp_kses_post( $text );
				break;

			case 'ta_hf_shortcode':
				$shortcode = '[tools_adapter_template id="' . $post_id . '"]';
				printf(
					'<div class="ta-hf-shortcode-cell"><input type="text" readonly value="%s" class="ta-hf-shortcode-min" onclick="this.select();"><button type="button" class="button button-small ta-hf-copy-btn" data-clipboard="%s" title="%s">📋</button></div>',
					esc_attr( $shortcode ),
					esc_attr( $shortcode ),
					esc_attr__( 'Copier le shortcode', 'tools-adapter' )
				);
				break;

			case 'ta_hf_status':
				$status = get_post_meta( $post_id, '_ta_hf_status', true ) ?: 'active';
				if ( 'active' === $status ) {
					echo '<span class="ta-hf-status-pill ta-status-active">● ' . esc_html__( 'Actif', 'tools-adapter' ) . '</span>';
				} else {
					echo '<span class="ta-hf-status-pill ta-status-inactive">○ ' . esc_html__( 'Inactif', 'tools-adapter' ) . '</span>';
				}
				break;
		}
	}

	/**
	 * Add "Modifier avec Elementor" action in post row actions.
	 *
	 * @param array    $actions Actions.
	 * @param \WP_Post $post Post object.
	 * @return array
	 */
	public function filter_row_actions( $actions, $post ) {
		if ( self::POST_TYPE !== $post->post_type ) {
			return $actions;
		}

		if ( class_exists( '\Elementor\Plugin' ) ) {
			$elementor_url = \Elementor\Plugin::$instance->documents->get( $post->ID )
				? \Elementor\Plugin::$instance->documents->get( $post->ID )->get_edit_url()
				: admin_url( 'post.php?post=' . $post->ID . '&action=elementor' );

			$actions['edit_with_elementor'] = sprintf(
				'<a href="%s" style="color:#9333ea;font-weight:600;">%s</a>',
				esc_url( $elementor_url ),
				esc_html__( 'Modifier avec Elementor', 'tools-adapter' )
			);
		}

		return $actions;
	}
}
