<?php
namespace ToolsAdapter\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Widget: Product Categories
 *
 * Affiche dynamiquement les catégories WooCommerce sous forme de boutons.
 */
class Product_Categories extends Widget_Base {

	public function get_name() {
		return 'tools-adapter-product-categories';
	}

	public function get_title() {
		return esc_html__( 'Catégories Produits', 'tools-adapter' );
	}

	public function get_icon() {
		return 'eicon-product-categories';
	}

	public function get_categories() {
		return [ 'tools-adapter', 'woocommerce-elements' ];
	}

	public function get_keywords() {
		return [ 'woocommerce', 'category', 'categories', 'product', 'shop' ];
	}

	public function get_style_depends() {
		return [ 'tools-adapter-product-categories' ];
	}

	protected function register_controls() {
		/* ═══════════════ CONTENT ═══════════════ */
		$this->start_controls_section(
			'section_query',
			[
				'label' => esc_html__( 'Contenu', 'tools-adapter' ),
			]
		);

		$this->add_control(
			'filter_mode',
			[
				'label'       => esc_html__( 'Mode', 'tools-adapter' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'links',
				'options'     => [
					'links'  => esc_html__( 'Liens (pages catégorie)', 'tools-adapter' ),
					'filter' => esc_html__( 'Filtre AJAX (Archive Produits)', 'tools-adapter' ),
				],
				'description' => esc_html__( 'En mode Filtre AJAX, utilisez le widget Archive Produits sur la même page.', 'tools-adapter' ),
			]
		);

		$this->add_control(
			'multi_select',
			[
				'label'        => esc_html__( 'Sélection multiple', 'tools-adapter' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
				'condition'    => [
					'filter_mode' => 'filter',
				],
			]
		);

		$this->add_control(
			'show_all_button',
			[
				'label'        => esc_html__( 'Bouton « Tous »', 'tools-adapter' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => [
					'filter_mode' => 'filter',
				],
			]
		);

		$this->add_control(
			'all_button_text',
			[
				'label'     => esc_html__( 'Texte « Tous »', 'tools-adapter' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'Tous', 'tools-adapter' ),
				'condition' => [
					'filter_mode'     => 'filter',
					'show_all_button' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'columns',
			[
				'label'          => esc_html__( 'Colonnes', 'tools-adapter' ),
				'type'           => Controls_Manager::NUMBER,
				'min'            => 1,
				'max'            => 8,
				'default'        => 4,
				'tablet_default' => 3,
				'mobile_default' => 2,
				'selectors'      => [
					'{{WRAPPER}} .vv-product-categories' => '--vv-columns: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'hide_empty',
			[
				'label'        => esc_html__( 'Masquer les vides', 'tools-adapter' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Oui', 'tools-adapter' ),
				'label_off'    => esc_html__( 'Non', 'tools-adapter' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'show_count',
			[
				'label'        => esc_html__( 'Afficher le compteur', 'tools-adapter' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Oui', 'tools-adapter' ),
				'label_off'    => esc_html__( 'Non', 'tools-adapter' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'parent',
			[
				'label'       => esc_html__( 'Catégorie parente', 'tools-adapter' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 0,
				'description' => esc_html__( '0 = catégories de premier niveau.', 'tools-adapter' ),
			]
		);

		$this->add_control(
			'exclude',
			[
				'label'       => esc_html__( 'Exclure (IDs)', 'tools-adapter' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => '15, 22',
				'description' => esc_html__( 'IDs de catégories à exclure, séparés par des virgules.', 'tools-adapter' ),
			]
		);

		$this->add_control(
			'limit',
			[
				'label'       => esc_html__( 'Limite', 'tools-adapter' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => -1,
				'min'         => -1,
				'description' => esc_html__( '-1 pour toutes les catégories.', 'tools-adapter' ),
			]
		);

		$this->add_control(
			'orderby',
			[
				'label'   => esc_html__( 'Trier par', 'tools-adapter' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'name',
				'options' => [
					'name'       => esc_html__( 'Nom', 'tools-adapter' ),
					'slug'       => esc_html__( 'Slug', 'tools-adapter' ),
					'count'      => esc_html__( 'Nombre de produits', 'tools-adapter' ),
					'term_order' => esc_html__( 'Ordre menu', 'tools-adapter' ),
					'id'         => esc_html__( 'ID', 'tools-adapter' ),
				],
			]
		);

		$this->add_control(
			'order',
			[
				'label'   => esc_html__( 'Ordre', 'tools-adapter' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'ASC',
				'options' => [
					'ASC'  => esc_html__( 'Croissant', 'tools-adapter' ),
					'DESC' => esc_html__( 'Décroissant', 'tools-adapter' ),
				],
			]
		);

		$this->add_control(
			'open_in_new_tab',
			[
				'label'        => esc_html__( 'Ouvrir dans un nouvel onglet', 'tools-adapter' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
				'condition'    => [
					'filter_mode' => 'links',
				],
			]
		);

		$this->end_controls_section();

		/* ═══════════════ STYLE: GRID ═══════════════ */
		$this->start_controls_section(
			'section_style_grid',
			[
				'label' => esc_html__( 'Grille', 'tools-adapter' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'gap',
			[
				'label'      => esc_html__( 'Espacement', 'tools-adapter' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 80,
					],
				],
				'default'    => [
					'size' => 16,
					'unit' => 'px',
				],
				'selectors'  => [
					'{{WRAPPER}} .vv-product-categories' => '--vv-gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'grid_align',
			[
				'label'     => esc_html__( 'Alignement horizontal', 'tools-adapter' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'start'  => [
						'title' => esc_html__( 'Début', 'tools-adapter' ),
						'icon'  => 'eicon-h-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Centre', 'tools-adapter' ),
						'icon'  => 'eicon-h-align-center',
					],
					'end'    => [
						'title' => esc_html__( 'Fin', 'tools-adapter' ),
						'icon'  => 'eicon-h-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .vv-product-categories' => 'justify-items: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'grid_max_width',
			[
				'label'      => esc_html__( 'Largeur max grille', 'tools-adapter' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'      => [
					'px' => [
						'min' => 200,
						'max' => 1600,
					],
					'%'  => [
						'min' => 20,
						'max' => 100,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .vv-product-categories' => 'max-width: {{SIZE}}{{UNIT}}; margin-left: auto; margin-right: auto;',
				],
			]
		);

		$this->end_controls_section();

		/* ═══════════════ STYLE: ITEM ═══════════════ */
		$this->start_controls_section(
			'section_style_item',
			[
				'label' => esc_html__( 'Bouton catégorie', 'tools-adapter' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'item_min_height',
			[
				'label'      => esc_html__( 'Hauteur min', 'tools-adapter' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'default'    => [
					'size' => 60,
					'unit' => 'px',
				],
				'selectors'  => [
					'{{WRAPPER}} .vv-product-categories' => '--vv-item-min-height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'item_width',
			[
				'label'      => esc_html__( 'Largeur bouton', 'tools-adapter' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 600,
					],
					'%'  => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .vv-product-category' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'item_padding',
			[
				'label'      => esc_html__( 'Padding', 'tools-adapter' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'default'    => [
					'top'      => '15',
					'right'    => '20',
					'bottom'   => '15',
					'left'     => '20',
					'unit'     => 'px',
					'isLinked' => false,
				],
				'selectors'  => [
					'{{WRAPPER}} .vv-product-categories' => '--vv-item-pad-top: {{TOP}}{{UNIT}}; --vv-item-pad-right: {{RIGHT}}{{UNIT}}; --vv-item-pad-bottom: {{BOTTOM}}{{UNIT}}; --vv-item-pad-left: {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'item_content_align',
			[
				'label'     => esc_html__( 'Alignement contenu', 'tools-adapter' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'flex-start' => [
						'title' => esc_html__( 'Gauche', 'tools-adapter' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center'     => [
						'title' => esc_html__( 'Centre', 'tools-adapter' ),
						'icon'  => 'eicon-text-align-center',
					],
					'flex-end'   => [
						'title' => esc_html__( 'Droite', 'tools-adapter' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'default'   => 'center',
				'selectors' => [
					'{{WRAPPER}} .vv-product-category' => 'justify-content: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'item_gap',
			[
				'label'      => esc_html__( 'Espace nom / compteur', 'tools-adapter' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 30,
					],
				],
				'default'    => [
					'size' => 5,
					'unit' => 'px',
				],
				'selectors'  => [
					'{{WRAPPER}} .vv-product-categories' => '--vv-item-gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'item_border_radius',
			[
				'label'      => esc_html__( 'Border radius', 'tools-adapter' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'default'    => [
					'top'      => '6',
					'right'    => '6',
					'bottom'   => '6',
					'left'     => '6',
					'unit'     => 'px',
					'isLinked' => true,
				],
				'selectors'  => [
					'{{WRAPPER}} .vv-product-categories' => '--vv-item-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'item_border',
				'selector' => '{{WRAPPER}} .vv-product-category',
			]
		);

		$this->add_control(
			'transition_duration',
			[
				'label'      => esc_html__( 'Durée transition', 'tools-adapter' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 's' ],
				'range'      => [
					's' => [
						'min'  => 0,
						'max'  => 1,
						'step' => 0.05,
					],
				],
				'default'    => [
					'size' => 0.3,
					'unit' => 's',
				],
				'selectors'  => [
					'{{WRAPPER}} .vv-product-category' => 'transition-duration: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs( 'item_colors' );

		$this->start_controls_tab(
			'item_colors_normal',
			[
				'label' => esc_html__( 'Normal', 'tools-adapter' ),
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'           => 'item_bg',
				'types'          => [ 'classic', 'gradient' ],
				'selector'       => '{{WRAPPER}} .vv-product-category',
				'exclude'        => [ 'image' ],
				'fields_options' => [
					'background' => [
						'default' => 'classic',
					],
					'color'      => [
						'default'   => '#ffffff',
						'selectors' => [
							'{{WRAPPER}} .vv-product-categories' => '--vv-item-bg: {{VALUE}};',
						],
					],
				],
			]
		);

		$this->add_control(
			'name_color',
			[
				'label'     => esc_html__( 'Couleur nom', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#222222',
				'selectors' => [
					'{{WRAPPER}} .vv-product-categories' => '--vv-name-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'count_color',
			[
				'label'     => esc_html__( 'Couleur compteur', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#777777',
				'selectors' => [
					'{{WRAPPER}} .vv-product-categories' => '--vv-count-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'item_shadow',
				'selector' => '{{WRAPPER}} .vv-product-category',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'item_colors_hover',
			[
				'label' => esc_html__( 'Survol', 'tools-adapter' ),
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'           => 'item_bg_hover',
				'types'          => [ 'classic', 'gradient' ],
				'selector'       => '{{WRAPPER}} .vv-product-category:hover',
				'exclude'        => [ 'image' ],
				'fields_options' => [
					'background' => [
						'default' => 'classic',
					],
					'color'      => [
						'default'   => '#C9A84C',
						'selectors' => [
							'{{WRAPPER}} .vv-product-categories' => '--vv-hover-bg: {{VALUE}};',
						],
					],
				],
			]
		);

		$this->add_control(
			'item_border_color_hover',
			[
				'label'     => esc_html__( 'Couleur bordure', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#C9A84C',
				'selectors' => [
					'{{WRAPPER}} .vv-product-categories' => '--vv-hover-border: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'name_color_hover',
			[
				'label'     => esc_html__( 'Couleur nom', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .vv-product-categories' => '--vv-hover-name: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'count_color_hover',
			[
				'label'     => esc_html__( 'Couleur compteur', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .vv-product-categories' => '--vv-hover-count: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'item_hover_lift',
			[
				'label'     => esc_html__( 'Décalage vertical', 'tools-adapter' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 20,
					],
				],
				'default'   => [
					'size' => 3,
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .vv-product-categories' => '--vv-hover-lift: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'item_shadow_hover',
				'selector' => '{{WRAPPER}} .vv-product-category:hover',
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();

		/* ═══════════════ STYLE: TYPOGRAPHY ═══════════════ */
		$this->start_controls_section(
			'section_style_typography',
			[
				'label' => esc_html__( 'Typographie', 'tools-adapter' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'name_typography',
				'label'    => esc_html__( 'Nom', 'tools-adapter' ),
				'selector' => '{{WRAPPER}} .vv-category-name',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'count_typography',
				'label'    => esc_html__( 'Compteur', 'tools-adapter' ),
				'selector' => '{{WRAPPER}} .vv-category-count',
			]
		);

		$this->add_control(
			'count_prefix',
			[
				'label'       => esc_html__( 'Préfixe compteur', 'tools-adapter' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '(',
				'placeholder' => '(',
			]
		);

		$this->add_control(
			'count_suffix',
			[
				'label'       => esc_html__( 'Suffixe compteur', 'tools-adapter' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => ')',
				'placeholder' => ')',
			]
		);

		$this->end_controls_section();
	}

	public function get_script_depends() {
		return [ 'tools-adapter-archive' ];
	}

	protected function render() {
		if ( ! class_exists( 'WooCommerce' ) ) {
			if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
				echo '<p>' . esc_html__( 'WooCommerce est requis pour ce widget.', 'tools-adapter' ) . '</p>';
			}
			return;
		}

		$settings = $this->get_settings_for_display();
		$mode     = $settings['filter_mode'] ?? 'links';

		$args = [
			'taxonomy'   => 'product_cat',
			'hide_empty' => ( 'yes' === $settings['hide_empty'] ),
			'orderby'    => $settings['orderby'],
			'order'      => $settings['order'],
		];

		if ( '' !== $settings['parent'] && 'all' !== $settings['parent'] ) {
			$args['parent'] = intval( $settings['parent'] );
		}

		if ( intval( $settings['limit'] ) > 0 ) {
			$args['number'] = intval( $settings['limit'] );
		}

		if ( ! empty( $settings['exclude'] ) ) {
			$exclude = array_filter( array_map( 'intval', explode( ',', $settings['exclude'] ) ) );
			if ( $exclude ) {
				$args['exclude'] = $exclude;
			}
		}

		$categories = get_terms( $args );

		if ( empty( $categories ) || is_wp_error( $categories ) ) {
			echo '<p class="vv-no-categories">' . esc_html__( 'Aucune catégorie disponible.', 'tools-adapter' ) . '</p>';
			return;
		}

		$prefix = isset( $settings['count_prefix'] ) ? $settings['count_prefix'] : '(';
		$suffix = isset( $settings['count_suffix'] ) ? $settings['count_suffix'] : ')';

		$active_ids = [];
		if ( ! empty( $_GET['product_cat'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$active_ids = array_values( array_filter( array_map( 'absint', (array) wp_unslash( $_GET['product_cat'] ) ) ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		}

		$wrapper_class = 'vv-product-categories';
		if ( 'filter' === $mode ) {
			$wrapper_class .= ' vv-product-categories--filter';
		}
		?>
		<div
			class="<?php echo esc_attr( $wrapper_class ); ?>"
			<?php if ( 'filter' === $mode ) : ?>
				data-ta-category-filter="1"
				data-multi="<?php echo ( 'yes' === ( $settings['multi_select'] ?? '' ) ) ? '1' : '0'; ?>"
			<?php endif; ?>
		>
			<?php if ( 'filter' === $mode && 'yes' === ( $settings['show_all_button'] ?? '' ) ) : ?>
				<button
					type="button"
					class="vv-product-category vv-product-category--all<?php echo empty( $active_ids ) ? ' is-active' : ''; ?>"
					data-category-id="0"
					data-category-filter
				>
					<span class="vv-category-name"><?php echo esc_html( \tools_adapter_translate( $settings['all_button_text'] ?: __( 'Tous', 'tools-adapter' ) ) ); ?></span>
				</button>
			<?php endif; ?>

			<?php foreach ( $categories as $category ) : ?>
				<?php
				$is_active = in_array( (int) $category->term_id, $active_ids, true );
				if ( 'filter' === $mode ) :
					?>
					<button
						type="button"
						class="vv-product-category<?php echo $is_active ? ' is-active' : ''; ?>"
						data-category-id="<?php echo esc_attr( (string) $category->term_id ); ?>"
						data-category-filter
					>
						<span class="vv-category-name"><?php echo esc_html( $category->name ); ?></span>
						<?php if ( 'yes' === $settings['show_count'] ) : ?>
							<span class="vv-category-count"><?php echo esc_html( $prefix . $category->count . $suffix ); ?></span>
						<?php endif; ?>
					</button>
				<?php else :
					$category_link = get_term_link( $category );
					if ( is_wp_error( $category_link ) ) {
						continue;
					}
					$target = ( 'yes' === $settings['open_in_new_tab'] ) ? '_blank' : '_self';
					$rel    = ( 'yes' === $settings['open_in_new_tab'] ) ? 'noopener noreferrer' : '';
					?>
					<a
						href="<?php echo esc_url( $category_link ); ?>"
						class="vv-product-category"
						target="<?php echo esc_attr( $target ); ?>"
						<?php echo $rel ? 'rel="' . esc_attr( $rel ) . '"' : ''; ?>
					>
						<span class="vv-category-name"><?php echo esc_html( $category->name ); ?></span>
						<?php if ( 'yes' === $settings['show_count'] ) : ?>
							<span class="vv-category-count"><?php echo esc_html( $prefix . $category->count . $suffix ); ?></span>
						<?php endif; ?>
					</a>
				<?php endif; ?>
			<?php endforeach; ?>
		</div>
		<?php
	}
}
