<?php
namespace ToolsAdapter\Widgets;

use Elementor\Controls_Manager;
use ToolsAdapter\Base_Widget;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Widget: Menu de Navigation — menu WordPress responsive avec sous-menus animés et volet mobile tactile.
 */
class Nav_Menu extends Base_Widget {

	public function get_name() {
		return 'tools-adapter-nav-menu';
	}

	public function get_title() {
		return esc_html__( 'Menu de Navigation', 'tools-adapter' );
	}

	public function get_icon() {
		return 'eicon-nav-menu';
	}

	public function get_categories() {
		return [ 'tools-adapter' ];
	}

	public function get_keywords() {
		return [ 'menu', 'nav', 'navigation', 'header', 'barre', 'burger', 'mobile' ];
	}

	public function get_style_depends() {
		return [ 'tools-adapter-nav-menu' ];
	}

	public function get_script_depends() {
		return [ 'tools-adapter-nav-menu' ];
	}

	private function get_available_menus() {
		$menus = wp_get_nav_menus();
		$options = [];
		foreach ( $menus as $menu ) {
			$options[ $menu->slug ] = $menu->name;
		}
		return $options;
	}

	protected function register_controls() {
		// ==========================================
		// SECTION CONTENU : MENU
		// ==========================================
		$this->start_controls_section(
			'section_menu',
			[ 'label' => esc_html__( 'Menu de Navigation', 'tools-adapter' ) ]
		);

		$menus = $this->get_available_menus();
		if ( ! empty( $menus ) ) {
			$this->add_control(
				'menu',
				[
					'label'   => esc_html__( 'Choisir un menu', 'tools-adapter' ),
					'type'    => Controls_Manager::SELECT,
					'options' => $menus,
					'default' => array_keys( $menus )[0],
				]
			);
		} else {
			$this->add_control(
				'menu_empty',
				[
					'type'            => Controls_Manager::RAW_HTML,
					'raw'             => sprintf(
						/* translators: %s: URL to WP menus */
						__( 'Aucun menu trouvé. Créez-en un dans <a href="%s" target="_blank">Apparence > Menus</a>.', 'tools-adapter' ),
						admin_url( 'nav-menus.php' )
					),
					'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
				]
			);
		}

		$this->add_control(
			'layout',
			[
				'label'   => esc_html__( 'Disposition', 'tools-adapter' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'horizontal',
				'options' => [
					'horizontal' => esc_html__( 'Horizontal', 'tools-adapter' ),
					'vertical'   => esc_html__( 'Vertical', 'tools-adapter' ),
				],
			]
		);

		$this->add_responsive_control(
			'align',
			[
				'label'     => esc_html__( 'Alignement', 'tools-adapter' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'flex-start' => [ 'title' => esc_html__( 'Gauche', 'tools-adapter' ), 'icon' => 'eicon-text-align-left' ],
					'center'     => [ 'title' => esc_html__( 'Centre', 'tools-adapter' ), 'icon' => 'eicon-text-align-center' ],
					'flex-end'   => [ 'title' => esc_html__( 'Droite', 'tools-adapter' ), 'icon' => 'eicon-text-align-right' ],
					'space-between' => [ 'title' => esc_html__( 'Justifié', 'tools-adapter' ), 'icon' => 'eicon-text-align-justify' ],
				],
				'selectors' => [
					'{{WRAPPER}} .ta-nav-menu-desktop' => 'justify-content: {{VALUE}};',
					'{{WRAPPER}} .ta-nav-mobile-toggle-wrap' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'hover_effect',
			[
				'label'   => esc_html__( 'Animation au survol', 'tools-adapter' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'underline',
				'options' => [
					'none'      => esc_html__( 'Aucune', 'tools-adapter' ),
					'underline' => esc_html__( 'Soulignement fluide (Underline)', 'tools-adapter' ),
				],
			]
		);

		$this->add_control(
			'submenu_indicator',
			[
				'label'        => esc_html__( 'Flèche pour sous-menus', 'tools-adapter' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Oui', 'tools-adapter' ),
				'label_off'    => esc_html__( 'Non', 'tools-adapter' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'breakpoint',
			[
				'label'   => esc_html__( 'Basculement Mobile (Burger)', 'tools-adapter' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'tablet',
				'options' => [
					'tablet' => esc_html__( 'Tablette & Mobile (< 1024px)', 'tools-adapter' ),
					'mobile' => esc_html__( 'Mobile uniquement (< 768px)', 'tools-adapter' ),
					'none'   => esc_html__( 'Toujours afficher la version bureau', 'tools-adapter' ),
				],
			]
		);

		$this->add_control(
			'mobile_title',
			[
				'label'     => esc_html__( 'Titre du volet mobile', 'tools-adapter' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'Menu', 'tools-adapter' ),
				'condition' => [ 'breakpoint!' => 'none' ],
			]
		);

		$this->end_controls_section();

		// ==========================================
		// SECTION STYLE : LIENS PRINCIPAUX (BUREAU)
		// ==========================================
		$this->start_controls_section(
			'section_style_main_menu',
			[
				'label' => esc_html__( 'Liens Principaux (Bureau)', 'tools-adapter' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'menu_typography',
				'selector' => '{{WRAPPER}} .ta-nav-menu-desktop .ta-nav-link',
			]
		);

		$this->start_controls_tabs( 'tabs_menu_colors' );

		// Normal
		$this->start_controls_tab(
			'tab_menu_normal',
			[ 'label' => esc_html__( 'Normal', 'tools-adapter' ) ]
		);
		$this->add_control(
			'menu_text_color',
			[
				'label'     => esc_html__( 'Couleur du texte', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#1e293b',
				'selectors' => [ '{{WRAPPER}} .ta-nav-menu-desktop .ta-nav-link' => 'color: {{VALUE}};' ],
			]
		);
		$this->end_controls_tab();

		// Survol
		$this->start_controls_tab(
			'tab_menu_hover',
			[ 'label' => esc_html__( 'Survol', 'tools-adapter' ) ]
		);
		$this->add_control(
			'menu_text_color_hover',
			[
				'label'     => esc_html__( 'Couleur au survol', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#0f172a',
				'selectors' => [
					'{{WRAPPER}} .ta-nav-menu-desktop .ta-nav-link:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}} .ta-nav-menu-desktop .ta-nav-item:hover > .ta-nav-link' => 'color: {{VALUE}};',
				],
			]
		);
		$this->end_controls_tab();

		// Actif
		$this->start_controls_tab(
			'tab_menu_active',
			[ 'label' => esc_html__( 'Actif', 'tools-adapter' ) ]
		);
		$this->add_control(
			'menu_text_color_active',
			[
				'label'     => esc_html__( 'Couleur élément actif', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#16a34a',
				'selectors' => [
					'{{WRAPPER}} .ta-nav-menu-desktop .ta-nav-item.current-menu-item > .ta-nav-link' => 'color: {{VALUE}};',
					'{{WRAPPER}} .ta-nav-menu-desktop .ta-nav-item.current_page_item > .ta-nav-link' => 'color: {{VALUE}};',
				],
			]
		);
		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'item_padding',
			[
				'label'      => esc_html__( 'Espacement des liens (Padding)', 'tools-adapter' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .ta-nav-menu-desktop .ta-nav-link' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator'  => 'before',
			]
		);

		$this->end_controls_section();

		// ==========================================
		// SECTION STYLE : SOUS-MENUS (DROPDOWNS)
		// ==========================================
		$this->start_controls_section(
			'section_style_dropdown',
			[
				'label' => esc_html__( 'Sous-menus (Déroulants)', 'tools-adapter' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'dropdown_bg',
			[
				'label'     => esc_html__( 'Couleur d\'arrière-plan', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [ '{{WRAPPER}} .ta-nav-submenu' => 'background-color: {{VALUE}};' ],
			]
		);

		$this->add_control(
			'dropdown_text_color',
			[
				'label'     => esc_html__( 'Couleur du texte', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#475569',
				'selectors' => [ '{{WRAPPER}} .ta-nav-submenu .ta-nav-link' => 'color: {{VALUE}};' ],
			]
		);

		$this->add_control(
			'dropdown_hover_bg',
			[
				'label'     => esc_html__( 'Fond au survol', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#f8fafc',
				'selectors' => [ '{{WRAPPER}} .ta-nav-submenu .ta-nav-link:hover' => 'background-color: {{VALUE}};' ],
			]
		);

		$this->add_control(
			'dropdown_hover_color',
			[
				'label'     => esc_html__( 'Texte au survol', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#0f172a',
				'selectors' => [ '{{WRAPPER}} .ta-nav-submenu .ta-nav-link:hover' => 'color: {{VALUE}};' ],
			]
		);

		$this->add_responsive_control(
			'dropdown_border_radius',
			[
				'label'      => esc_html__( 'Rayon des coins', 'tools-adapter' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [ '{{WRAPPER}} .ta-nav-submenu' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'dropdown_shadow',
				'selector' => '{{WRAPPER}} .ta-nav-submenu',
			]
		);

		$this->end_controls_section();

		// ==========================================
		// SECTION STYLE : MENU BURGER & MOBILE
		// ==========================================
		$this->start_controls_section(
			'section_style_mobile',
			[
				'label'     => esc_html__( 'Menu Burger & Volet Mobile', 'tools-adapter' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [ 'breakpoint!' => 'none' ],
			]
		);

		$this->add_control(
			'burger_color',
			[
				'label'     => esc_html__( 'Couleur de l\'icône Burger', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#0f172a',
				'selectors' => [ '{{WRAPPER}} .ta-nav-mobile-toggle' => 'color: {{VALUE}};' ],
			]
		);

		$this->add_control(
			'drawer_bg',
			[
				'label'     => esc_html__( 'Arrière-plan du volet mobile', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [ '{{WRAPPER}} .ta-nav-drawer' => 'background-color: {{VALUE}};' ],
			]
		);

		$this->add_control(
			'mobile_link_color',
			[
				'label'     => esc_html__( 'Couleur des liens mobiles', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#1e293b',
				'selectors' => [ '{{WRAPPER}} .ta-mobile-nav-link' => 'color: {{VALUE}};' ],
			]
		);

		$this->end_controls_section();
	}

	private function render_menu_tree( $menu_slug ) {
		$menu = wp_get_nav_menu_object( $menu_slug );
		if ( ! $menu ) {
			return;
		}

		$items = wp_get_nav_menu_items( $menu->term_id );
		if ( empty( $items ) ) {
			return;
		}

		// Build parent-children tree
		$tree = [];
		foreach ( $items as $item ) {
			$parent_id = (int) $item->menu_item_parent;
			if ( ! isset( $tree[ $parent_id ] ) ) {
				$tree[ $parent_id ] = [];
			}
			$tree[ $parent_id ][] = $item;
		}

		return $tree;
	}

	private function render_desktop_items( $tree, $parent_id = 0, $is_submenu = false ) {
		if ( empty( $tree[ $parent_id ] ) ) {
			return;
		}

		$tag = $is_submenu ? 'ul class="ta-nav-submenu"' : 'ul class="ta-nav-menu-desktop"';
		echo '<' . $tag . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		$has_indicator = 'yes' === $this->get_settings_for_display( 'submenu_indicator' );

		foreach ( $tree[ $parent_id ] as $item ) {
			$has_children = ! empty( $tree[ $item->ID ] );
			$classes = [ 'ta-nav-item' ];
			if ( $has_children ) {
				$classes[] = 'ta-has-children';
			}
			if ( in_array( 'current-menu-item', (array) $item->classes, true ) ) {
				$classes[] = 'current-menu-item';
			}

			echo '<li class="' . esc_attr( implode( ' ', $classes ) ) . '">';
			echo '<a href="' . esc_url( $item->url ) . '" class="ta-nav-link">';
			echo esc_html( $item->title );
			if ( $has_children && $has_indicator ) {
				echo '<span class="ta-submenu-arrow"></span>';
			}
			echo '</a>';

			if ( $has_children ) {
				$this->render_desktop_items( $tree, $item->ID, true );
			}

			echo '</li>';
		}

		echo '</ul>';
	}

	private function render_mobile_items( $tree, $parent_id = 0, $is_submenu = false ) {
		if ( empty( $tree[ $parent_id ] ) ) {
			return;
		}

		$list_class = $is_submenu ? 'ta-mobile-submenu' : 'ta-mobile-nav-list';
		echo '<ul class="' . esc_attr( $list_class ) . '">';

		foreach ( $tree[ $parent_id ] as $item ) {
			$has_children = ! empty( $tree[ $item->ID ] );
			echo '<li class="ta-mobile-nav-item">';
			echo '<div class="ta-mobile-nav-link-wrap">';
			echo '<a href="' . esc_url( $item->url ) . '" class="ta-mobile-nav-link">' . esc_html( $item->title ) . '</a>';
			if ( $has_children ) {
				echo '<button type="button" class="ta-mobile-submenu-toggle" aria-label="Toggle submenu">▼</button>';
			}
			echo '</div>';

			if ( $has_children ) {
				$this->render_mobile_items( $tree, $item->ID, true );
			}

			echo '</li>';
		}

		echo '</ul>';
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		if ( empty( $settings['menu'] ) ) {
			return;
		}

		$tree = $this->render_menu_tree( $settings['menu'] );
		if ( empty( $tree ) ) {
			return;
		}

		$wrapper_classes = [ 'ta-nav-menu-wrapper' ];
		if ( 'underline' === $settings['hover_effect'] ) {
			$wrapper_classes[] = 'ta-nav-hover-underline';
		}
		if ( 'vertical' === $settings['layout'] ) {
			$wrapper_classes[] = 'ta-layout-vertical';
		}
		if ( 'tablet' === $settings['breakpoint'] ) {
			$wrapper_classes[] = 'ta-breakpoint-tablet';
		} elseif ( 'mobile' === $settings['breakpoint'] ) {
			$wrapper_classes[] = 'ta-breakpoint-mobile';
		}
		?>
		<div class="<?php echo esc_attr( implode( ' ', $wrapper_classes ) ); ?>">

			<!-- Version Bureau -->
			<?php $this->render_desktop_items( $tree ); ?>

			<!-- Version Mobile (Toggle & Volet) -->
			<?php if ( 'none' !== $settings['breakpoint'] ) : ?>
				<div class="ta-nav-mobile-toggle-wrap">
					<button type="button" class="ta-nav-mobile-toggle" aria-label="<?php esc_attr_e( 'Ouvrir le menu', 'tools-adapter' ); ?>">
						<span class="ta-hamburger-box">
							<span class="ta-hamburger-inner"></span>
						</span>
					</button>
				</div>

				<div class="ta-nav-drawer-overlay"></div>
				<div class="ta-nav-drawer" role="dialog" aria-modal="true">
					<div class="ta-nav-drawer-header">
						<span class="ta-nav-drawer-title"><?php echo esc_html( $settings['mobile_title'] ); ?></span>
						<button type="button" class="ta-nav-drawer-close" aria-label="<?php esc_attr_e( 'Fermer le menu', 'tools-adapter' ); ?>">✕</button>
					</div>
					<?php $this->render_mobile_items( $tree ); ?>
				</div>
			<?php endif; ?>

		</div>
		<?php
	}
}
