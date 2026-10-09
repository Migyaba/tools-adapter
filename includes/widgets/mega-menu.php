<?php
namespace ToolsAdapter\Widgets;

use Elementor\Controls_Manager;
use ToolsAdapter\Base_Widget;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Widget: Mega menu — menu de navigation horizontal avec panneaux mega-menu
 * pour les éléments ayant des sous-menus, option "collant" au défilement.
 */
class Mega_Menu extends Base_Widget {

	public function get_name() {
		return 'tools-adapter-mega-menu';
	}

	public function get_title() {
		return esc_html__( 'Mega Menu', 'tools-adapter' );
	}

	public function get_icon() {
		return 'eicon-nav-menu';
	}

	public function get_categories() {
		return [ 'tools-adapter' ];
	}

	public function get_keywords() {
		return [ 'menu', 'navigation', 'mega menu', 'sticky', 'collant' ];
	}

	public function get_style_depends() {
		return [ 'tools-adapter-mega-menu' ];
	}

	public function get_script_depends() {
		return [ 'tools-adapter-mega-menu' ];
	}

	/**
	 * @return array<int|string,string>
	 */
	private function get_menu_options() {
		if ( ! function_exists( 'wp_get_nav_menus' ) ) {
			return [];
		}
		$menus   = wp_get_nav_menus();
		$options = [];
		foreach ( $menus as $menu ) {
			$options[ $menu->term_id ] = $menu->name;
		}
		return $options;
	}

	protected function register_controls() {
		$this->start_controls_section( 'section_content', [ 'label' => esc_html__( 'Contenu', 'tools-adapter' ) ] );

		$menu_options = $this->get_menu_options();

		$this->add_control(
			'menu_id',
			[
				'label'       => esc_html__( 'Menu WordPress', 'tools-adapter' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => $menu_options,
				'default'     => ! empty( $menu_options ) ? array_key_first( $menu_options ) : '',
				'description' => empty( $menu_options ) ? esc_html__( 'Créez un menu dans Apparence → Menus.', 'tools-adapter' ) : '',
			]
		);

		$this->add_control(
			'show_logo',
			[
				'label'        => esc_html__( 'Afficher un logo', 'tools-adapter' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
				'separator'    => 'before',
			]
		);

		$this->add_control(
			'logo',
			[
				'label'     => esc_html__( 'Logo', 'tools-adapter' ),
				'type'      => Controls_Manager::MEDIA,
				'condition' => [ 'show_logo' => 'yes' ],
			]
		);

		$this->add_control(
			'logo_url',
			[
				'label'     => esc_html__( 'Lien du logo', 'tools-adapter' ),
				'type'      => Controls_Manager::URL,
				'default'   => [ 'url' => home_url( '/' ) ],
				'condition' => [ 'show_logo' => 'yes' ],
			]
		);

		$this->add_control(
			'alignment',
			[
				'label'        => esc_html__( 'Alignement des liens', 'tools-adapter' ),
				'type'         => Controls_Manager::CHOOSE,
				'options'      => [
					'flex-start' => [ 'title' => esc_html__( 'Gauche', 'tools-adapter' ), 'icon' => 'eicon-text-align-left' ],
					'center'     => [ 'title' => esc_html__( 'Centre', 'tools-adapter' ), 'icon' => 'eicon-text-align-center' ],
					'flex-end'   => [ 'title' => esc_html__( 'Droite', 'tools-adapter' ), 'icon' => 'eicon-text-align-right' ],
				],
				'default'      => 'flex-end',
				'separator'    => 'before',
				'selectors'    => [ '{{WRAPPER}} .ta-mega-menu__list' => 'justify-content: {{VALUE}};' ],
			]
		);

		$this->add_control(
			'sticky',
			[
				'label'        => esc_html__( 'Collant au défilement', 'tools-adapter' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'separator'    => 'before',
			]
		);

		$this->add_control(
			'mobile_breakpoint',
			[
				'label'       => esc_html__( 'Repli mobile sous (px)', 'tools-adapter' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 992,
				'min'         => 320,
				'max'         => 1400,
				'description' => esc_html__( 'En dessous de cette largeur d\'écran, le menu devient un menu déroulant mobile.', 'tools-adapter' ),
			]
		);

		$this->add_control(
			'mobile_toggle_label',
			[
				'label'   => esc_html__( 'Label bouton mobile', 'tools-adapter' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Menu', 'tools-adapter' ),
			]
		);

		$this->end_controls_section();

		$this->start_controls_section( 'section_style_bar', [ 'label' => esc_html__( 'Barre', 'tools-adapter' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_control( 'bar_bg', [ 'label' => esc_html__( 'Fond', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#ffffff', 'selectors' => [ '{{WRAPPER}} .ta-mega-menu' => 'background-color: {{VALUE}};' ] ] );
		$this->add_responsive_control( 'bar_padding', [ 'label' => esc_html__( 'Espacement interne', 'tools-adapter' ), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => [ 'px' ], 'default' => [ 'top' => '16', 'right' => '24', 'bottom' => '16', 'left' => '24', 'unit' => 'px' ], 'selectors' => [ '{{WRAPPER}} .ta-mega-menu__inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ] ] );
		$this->add_group_control( Group_Control_Box_Shadow::get_type(), [ 'name' => 'bar_shadow', 'selector' => '{{WRAPPER}} .ta-mega-menu' ] );
		$this->add_control( 'sticky_bg', [ 'label' => esc_html__( 'Fond (collant)', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#ffffff', 'separator' => 'before', 'selectors' => [ '{{WRAPPER}} .ta-mega-menu.is-stuck' => 'background-color: {{VALUE}};' ] ] );

		$this->end_controls_section();

		$this->start_controls_section( 'section_style_links', [ 'label' => esc_html__( 'Liens', 'tools-adapter' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_control( 'link_color', [ 'label' => esc_html__( 'Couleur', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#1c1c1c', 'selectors' => [ '{{WRAPPER}} .ta-mega-menu__link' => 'color: {{VALUE}};' ] ] );
		$this->add_control( 'link_color_hover', [ 'label' => esc_html__( 'Couleur (survol / actif)', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#C9A84C', 'selectors' => [ '{{WRAPPER}} .ta-mega-menu__link:hover, {{WRAPPER}} .ta-mega-menu__item.current-menu-item > .ta-mega-menu__link' => 'color: {{VALUE}};' ] ] );
		$this->add_group_control( Group_Control_Typography::get_type(), [ 'name' => 'link_typography', 'selector' => '{{WRAPPER}} .ta-mega-menu__link' ] );
		$this->add_responsive_control( 'links_gap', [ 'label' => esc_html__( 'Espacement entre liens', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'range' => [ 'px' => [ 'min' => 0, 'max' => 60 ] ], 'default' => [ 'size' => 28, 'unit' => 'px' ], 'separator' => 'before', 'selectors' => [ '{{WRAPPER}} .ta-mega-menu__list' => '--ta-mega-gap: {{SIZE}}{{UNIT}};' ] ] );

		$this->end_controls_section();

		$this->start_controls_section( 'section_style_panel', [ 'label' => esc_html__( 'Panneau mega menu', 'tools-adapter' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_control( 'panel_bg', [ 'label' => esc_html__( 'Fond', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#ffffff', 'selectors' => [ '{{WRAPPER}} .ta-mega-menu__panel' => 'background-color: {{VALUE}};' ] ] );
		$this->add_control( 'panel_link_color', [ 'label' => esc_html__( 'Couleur des liens', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#555555', 'selectors' => [ '{{WRAPPER}} .ta-mega-menu__panel-link' => 'color: {{VALUE}};' ] ] );
		$this->add_control( 'panel_link_color_hover', [ 'label' => esc_html__( 'Couleur des liens (survol)', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#C9A84C', 'selectors' => [ '{{WRAPPER}} .ta-mega-menu__panel-link:hover' => 'color: {{VALUE}};' ] ] );
		$this->add_group_control( Group_Control_Box_Shadow::get_type(), [ 'name' => 'panel_shadow', 'selector' => '{{WRAPPER}} .ta-mega-menu__panel' ] );
		$this->add_control( 'panel_radius', [ 'label' => esc_html__( 'Arrondi', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'range' => [ 'px' => [ 'min' => 0, 'max' => 30 ] ], 'selectors' => [ '{{WRAPPER}} .ta-mega-menu__panel' => 'border-radius: {{SIZE}}{{UNIT}};' ] ] );

		$this->end_controls_section();
	}

	/**
	 * Build a parent => children map from a flat menu items list (2 levels only).
	 *
	 * @param \WP_Post[] $items Menu items.
	 * @return array<int,array>
	 */
	private function build_tree( $items ) {
		$by_parent = [];
		foreach ( $items as $item ) {
			$parent = (int) $item->menu_item_parent;
			if ( ! isset( $by_parent[ $parent ] ) ) {
				$by_parent[ $parent ] = [];
			}
			$by_parent[ $parent ][] = $item;
		}
		return $by_parent;
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$menu_id  = $settings['menu_id'] ?? '';

		if ( empty( $menu_id ) || ! function_exists( 'wp_get_nav_menu_items' ) ) {
			if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
				echo '<p>' . esc_html__( 'Sélectionnez un menu WordPress dans les réglages du widget.', 'tools-adapter' ) . '</p>';
			}
			return;
		}

		$items = wp_get_nav_menu_items( $menu_id );
		if ( empty( $items ) ) {
			return;
		}

		$tree      = $this->build_tree( $items );
		$top_level = $tree[0] ?? [];
		$sticky    = 'yes' === ( $settings['sticky'] ?? '' );
		$breakpoint = absint( $settings['mobile_breakpoint'] ?? 992 );
		$logo_url  = ( $settings['logo']['url'] ?? '' );
		?>
		<nav
			class="ta-mega-menu"
			data-ta-mega-menu
			data-sticky="<?php echo $sticky ? '1' : '0'; ?>"
			data-breakpoint="<?php echo esc_attr( (string) $breakpoint ); ?>"
			style="--ta-mega-breakpoint: <?php echo esc_attr( (string) $breakpoint ); ?>px;"
			aria-label="<?php echo esc_attr__( 'Navigation principale', 'tools-adapter' ); ?>"
		>
			<div class="ta-mega-menu__inner">
				<?php if ( 'yes' === ( $settings['show_logo'] ?? '' ) && ! empty( $logo_url ) ) : ?>
					<a class="ta-mega-menu__logo" href="<?php echo esc_url( $settings['logo_url']['url'] ?? home_url( '/' ) ); ?>">
						<img src="<?php echo esc_url( $logo_url ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" />
					</a>
				<?php endif; ?>

				<button type="button" class="ta-mega-menu__toggle" data-mega-toggle aria-expanded="false" aria-controls="<?php echo esc_attr( 'ta-mega-list-' . $this->get_id() ); ?>">
					<span class="ta-mega-menu__toggle-bar"></span>
					<span class="ta-mega-menu__toggle-bar"></span>
					<span class="ta-mega-menu__toggle-bar"></span>
					<span class="screen-reader-text"><?php echo esc_html( \tools_adapter_translate( $settings['mobile_toggle_label'] ?? 'Menu' ) ); ?></span>
				</button>

				<ul class="ta-mega-menu__list" id="<?php echo esc_attr( 'ta-mega-list-' . $this->get_id() ); ?>" data-mega-list>
					<?php foreach ( $top_level as $item ) :
						$children  = $tree[ $item->ID ] ?? [];
						$has_panel = ! empty( $children );
						?>
						<li class="ta-mega-menu__item<?php echo $has_panel ? ' has-panel' : ''; ?><?php echo in_array( 'current-menu-item', $item->classes, true ) ? ' current-menu-item' : ''; ?>">
							<a class="ta-mega-menu__link" href="<?php echo esc_url( $item->url ); ?>"<?php echo $item->target ? ' target="' . esc_attr( $item->target ) . '"' : ''; ?>>
								<?php echo esc_html( $item->title ); ?>
								<?php if ( $has_panel ) : ?><i class="fas fa-chevron-down ta-mega-menu__caret" aria-hidden="true"></i><?php endif; ?>
							</a>
							<?php if ( $has_panel ) : ?>
								<div class="ta-mega-menu__panel" data-mega-panel>
									<div class="ta-mega-menu__panel-columns">
										<?php foreach ( $children as $child ) : ?>
											<a class="ta-mega-menu__panel-link" href="<?php echo esc_url( $child->url ); ?>"<?php echo $child->target ? ' target="' . esc_attr( $child->target ) . '"' : ''; ?>>
												<?php echo esc_html( $child->title ); ?>
											</a>
										<?php endforeach; ?>
									</div>
								</div>
							<?php endif; ?>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
		</nav>
		<?php
	}
}
