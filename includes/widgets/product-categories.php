<?php
namespace ToolsAdapter\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use ToolsAdapter\Base_Widget;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Widget: Product Categories
 *
 * Affiche dynamiquement les catégories WooCommerce sous forme de boutons.
 */
class Product_Categories extends Base_Widget {

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
			'display_style',
			[
				'label'   => esc_html__( 'Style d’affichage', 'tools-adapter' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'buttons',
				'options' => [
					'buttons'     => esc_html__( 'Boutons / grille', 'tools-adapter' ),
					'image_cards' => esc_html__( 'Cartes avec image', 'tools-adapter' ),
				],
			]
		);

		$this->add_control(
			'show_category_image',
			[
				'label'        => esc_html__( 'Image catégorie', 'tools-adapter' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => [
					'display_style' => 'image_cards',
				],
			]
		);

		$this->add_control(
			'category_image_size',
			[
				'label'     => esc_html__( 'Taille image', 'tools-adapter' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'woocommerce_thumbnail',
				'options'   => [
					'thumbnail'             => esc_html__( 'Miniature', 'tools-adapter' ),
					'woocommerce_thumbnail' => esc_html__( 'WooCommerce miniature', 'tools-adapter' ),
					'medium'                => esc_html__( 'Moyenne', 'tools-adapter' ),
					'large'                 => esc_html__( 'Grande', 'tools-adapter' ),
					'full'                  => esc_html__( 'Originale', 'tools-adapter' ),
				],
				'condition' => [
					'display_style'       => 'image_cards',
					'show_category_image' => 'yes',
				],
			]
		);

		$this->add_control(
			'image_layout',
			[
				'label'     => esc_html__( 'Disposition image', 'tools-adapter' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'card',
				'options'   => [
					'card'   => esc_html__( 'Carte (image pleine largeur)', 'tools-adapter' ),
					'circle' => esc_html__( 'Cercle (avatar centré)', 'tools-adapter' ),
					'modern' => esc_html__( 'Moderne avec flèche ↗ (Style Maquette)', 'tools-adapter' ),
				],
				'condition' => [
					'display_style'       => 'image_cards',
					'show_category_image' => 'yes',
				],
			]
		);

		$this->add_control(
			'show_action_button',
			[
				'label'        => esc_html__( 'Afficher le bouton flèche ↗', 'tools-adapter' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => [
					'display_style'       => 'image_cards',
					'show_category_image' => 'yes',
					'image_layout'        => 'modern',
				],
			]
		);

		$this->add_control(
			'image_fallback',
			[
				'label'        => esc_html__( 'Image de repli (produit)', 'tools-adapter' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'description'  => esc_html__( 'Si la catégorie n’a pas d’image définie, utiliser automatiquement l’image d’un de ses produits.', 'tools-adapter' ),
				'condition'    => [
					'display_style'       => 'image_cards',
					'show_category_image' => 'yes',
				],
			]
		);

		$this->add_control(
			'image_fallback_order',
			[
				'label'     => esc_html__( 'Produit choisi', 'tools-adapter' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'recent',
				'options'   => [
					'recent' => esc_html__( 'Le plus récent', 'tools-adapter' ),
					'random' => esc_html__( 'Aléatoire', 'tools-adapter' ),
				],
				'condition' => [
					'display_style'       => 'image_cards',
					'show_category_image' => 'yes',
					'image_fallback'      => 'yes',
				],
			]
		);

		$this->add_control(
			'show_category_name',
			[
				'label'        => esc_html__( 'Afficher le nom', 'tools-adapter' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Oui', 'tools-adapter' ),
				'label_off'    => esc_html__( 'Non', 'tools-adapter' ),
				'return_value' => 'yes',
				'default'      => 'yes',
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
			'image_height',
			[
				'label'      => esc_html__( 'Hauteur image', 'tools-adapter' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 80,
						'max' => 360,
					],
				],
				'default'    => [
					'size' => 150,
					'unit' => 'px',
				],
				'selectors'  => [
					'{{WRAPPER}} .vv-product-categories' => '--vv-image-height: {{SIZE}}{{UNIT}};',
				],
				'condition'  => [
					'display_style'       => 'image_cards',
					'show_category_image' => 'yes',
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
					'{{WRAPPER}} .vv-product-category:not(.vv-product-category--image-card)' => 'justify-content: {{VALUE}};',
					'{{WRAPPER}} .vv-product-category__content' => 'justify-content: {{VALUE}};',
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

		/* ═══════════════ STYLE: CIRCLE LAYOUT ═══════════════ */
		$this->start_controls_section(
			'section_style_circle',
			[
				'label'     => esc_html__( 'Cartes image — Cercle', 'tools-adapter' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'display_style' => 'image_cards',
					'image_layout'  => 'circle',
				],
			]
		);

		$this->add_responsive_control(
			'circle_size',
			[
				'label'      => esc_html__( 'Taille du cercle', 'tools-adapter' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 60,
						'max' => 300,
					],
				],
				'default'    => [
					'size' => 110,
					'unit' => 'px',
				],
				'selectors'  => [
					'{{WRAPPER}} .vv-product-categories' => '--vv-circle-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'circle_bg',
			[
				'label'     => esc_html__( 'Fond du cercle', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#f6f6f6',
				'selectors' => [
					'{{WRAPPER}} .vv-product-categories' => '--vv-circle-bg: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'circle_gap',
			[
				'label'      => esc_html__( 'Espace image / texte', 'tools-adapter' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 40,
					],
				],
				'default'    => [
					'size' => 14,
					'unit' => 'px',
				],
				'selectors'  => [
					'{{WRAPPER}} .vv-product-categories' => '--vv-circle-gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'circle_border',
				'selector' => '{{WRAPPER}} .vv-product-category--circle-layout .vv-product-category__media',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'circle_shadow',
				'selector' => '{{WRAPPER}} .vv-product-category--circle-layout .vv-product-category__media',
			]
		);

		$this->end_controls_section();

		/* ═══════════════ STYLE: MODERN CARD LAYOUT ═══════════════ */
		$this->start_controls_section(
			'section_style_modern',
			[
				'label'     => esc_html__( 'Cartes Modernes (avec bouton ↗)', 'tools-adapter' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'display_style' => 'image_cards',
					'image_layout'  => 'modern',
				],
			]
		);

		$this->add_responsive_control(
			'modern_card_min_height',
			[
				'label'      => esc_html__( 'Hauteur minimale de la carte', 'tools-adapter' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'vh' ],
				'range'      => [
					'px' => [ 'min' => 140, 'max' => 500, 'step' => 5 ],
				],
				'default'    => [
					'size' => 260,
					'unit' => 'px',
				],
				'tablet_default' => [
					'size' => 230,
					'unit' => 'px',
				],
				'mobile_default' => [
					'size' => 200,
					'unit' => 'px',
				],
				'selectors'  => [
					'{{WRAPPER}} .vv-product-categories' => '--vv-modern-height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'modern_card_radius',
			[
				'label'      => esc_html__( 'Arrondi des coins (Border Radius)', 'tools-adapter' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'default'    => [
					'top'      => 24,
					'right'    => 24,
					'bottom'   => 24,
					'left'     => 24,
					'unit'     => 'px',
					'isLinked' => true,
				],
				'selectors'  => [
					'{{WRAPPER}} .vv-product-categories' => '--vv-modern-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'modern_card_padding',
			[
				'label'      => esc_html__( 'Marge interne (Padding)', 'tools-adapter' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'default'    => [
					'top'      => 24,
					'right'    => 24,
					'bottom'   => 24,
					'left'     => 24,
					'unit'     => 'px',
					'isLinked' => true,
				],
				'selectors'  => [
					'{{WRAPPER}} .vv-product-categories' => '--vv-modern-pad: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'modern_img_fit',
			[
				'label'     => esc_html__( 'Cadrage de l\'image', 'tools-adapter' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => [
					'cover'   => esc_html__( 'Couvrir (Plein cadre)', 'tools-adapter' ),
					'contain' => esc_html__( 'Contenir (Proportions entières)', 'tools-adapter' ),
				],
				'default'   => 'cover',
				'selectors' => [
					'{{WRAPPER}} .vv-product-categories' => '--vv-modern-img-fit: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'modern_card_bg',
			[
				'label'     => esc_html__( 'Couleur de fond de la carte', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#f8fafc',
				'selectors' => [
					'{{WRAPPER}} .vv-product-categories' => '--vv-modern-bg: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'modern_card_overlay',
			[
				'label'       => esc_html__( 'Voile / Dégradé de lisibilité (Overlay)', 'tools-adapter' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => 'linear-gradient(180deg, rgba(255, 255, 255, 0.75) 0%, rgba(255, 255, 255, 0.2) 45%, rgba(255, 255, 255, 0) 100%)',
				'description' => esc_html__( 'Couleur unie (rgba) ou dégradé CSS.', 'tools-adapter' ),
				'selectors'   => [
					'{{WRAPPER}} .vv-product-categories' => '--vv-modern-overlay: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'modern_card_border',
				'selector' => '{{WRAPPER}} .vv-product-category--modern-layout',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'modern_card_shadow',
				'selector' => '{{WRAPPER}} .vv-product-category--modern-layout',
			]
		);

		$this->add_control(
			'modern_card_hover_lift',
			[
				'label'     => esc_html__( 'Soulèvement au survol (px)', 'tools-adapter' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [ 'min' => 0, 'max' => 20 ],
				],
				'default'   => [
					'size' => 5,
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .vv-product-categories' => '--vv-modern-lift: {{SIZE}}px;',
				],
			]
		);

		$this->add_control(
			'modern_card_hover_zoom',
			[
				'label'     => esc_html__( 'Zoom image au survol', 'tools-adapter' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [ 'min' => 1.0, 'max' => 1.3, 'step' => 0.01 ],
				],
				'default'   => [
					'size' => 1.06,
				],
				'selectors' => [
					'{{WRAPPER}} .vv-product-categories' => '--vv-modern-zoom: {{SIZE}};',
				],
			]
		);

		// --- BOUTON ACTION ↗ ---
		$this->add_control(
			'heading_modern_btn',
			[
				'label'     => esc_html__( 'Bouton Flèche ↗', 'tools-adapter' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'show_action_button' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'modern_btn_size',
			[
				'label'      => esc_html__( 'Diamètre du bouton', 'tools-adapter' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => [
					'px' => [ 'min' => 28, 'max' => 70 ],
				],
				'default'    => [
					'size' => 44,
					'unit' => 'px',
				],
				'condition'  => [
					'show_action_button' => 'yes',
				],
				'selectors'  => [
					'{{WRAPPER}} .vv-product-categories' => '--vv-modern-btn-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'modern_btn_icon_size',
			[
				'label'      => esc_html__( 'Taille de l\'icône', 'tools-adapter' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => [
					'px' => [ 'min' => 10, 'max' => 36 ],
				],
				'default'    => [
					'size' => 18,
					'unit' => 'px',
				],
				'condition'  => [
					'show_action_button' => 'yes',
				],
				'selectors'  => [
					'{{WRAPPER}} .vv-product-categories' => '--vv-modern-btn-icon-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'modern_btn_offset_bottom',
			[
				'label'      => esc_html__( 'Distance du bas', 'tools-adapter' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => [
					'px' => [ 'min' => 0, 'max' => 60 ],
				],
				'default'    => [
					'size' => 20,
					'unit' => 'px',
				],
				'condition'  => [
					'show_action_button' => 'yes',
				],
				'selectors'  => [
					'{{WRAPPER}} .vv-product-categories' => '--vv-modern-btn-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'modern_btn_offset_right',
			[
				'label'      => esc_html__( 'Distance de la droite', 'tools-adapter' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => [
					'px' => [ 'min' => 0, 'max' => 60 ],
				],
				'default'    => [
					'size' => 20,
					'unit' => 'px',
				],
				'condition'  => [
					'show_action_button' => 'yes',
				],
				'selectors'  => [
					'{{WRAPPER}} .vv-product-categories' => '--vv-modern-btn-right: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs(
			'tabs_modern_btn',
			[
				'condition' => [
					'show_action_button' => 'yes',
				],
			]
		);

		$this->start_controls_tab(
			'tab_modern_btn_normal',
			[
				'label' => esc_html__( 'Normal', 'tools-adapter' ),
			]
		);

		$this->add_control(
			'modern_btn_bg',
			[
				'label'     => esc_html__( 'Fond du bouton', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#f59e0b',
				'selectors' => [
					'{{WRAPPER}} .vv-product-categories' => '--vv-modern-btn-bg: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'modern_btn_color',
			[
				'label'     => esc_html__( 'Couleur de l\'icône', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .vv-product-categories' => '--vv-modern-btn-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'modern_btn_shadow',
				'selector' => '{{WRAPPER}} .vv-product-category__action',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_modern_btn_hover',
			[
				'label' => esc_html__( 'Survol', 'tools-adapter' ),
			]
		);

		$this->add_control(
			'modern_btn_hover_bg',
			[
				'label'     => esc_html__( 'Fond au survol', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#d97706',
				'selectors' => [
					'{{WRAPPER}} .vv-product-categories' => '--vv-modern-btn-bg-hover: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'modern_btn_hover_color',
			[
				'label'     => esc_html__( 'Couleur icône au survol', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .vv-product-categories' => '--vv-modern-btn-color-hover: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'modern_btn_hover_shadow',
				'selector' => '{{WRAPPER}} .vv-product-category--modern-layout:hover .vv-product-category__action',
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		// --- TEXTES DANS LA CARTE MODERNE ---
		$this->add_control(
			'heading_modern_text',
			[
				'label'     => esc_html__( 'Textes (Nom & Compteur)', 'tools-adapter' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'modern_text_align',
			[
				'label'     => esc_html__( 'Alignement du texte', 'tools-adapter' ),
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
				'default'   => 'flex-start',
				'selectors' => [
					'{{WRAPPER}} .vv-product-category--modern-layout .vv-product-category__content' => 'align-items: {{VALUE}}; text-align: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'modern_name_color',
			[
				'label'     => esc_html__( 'Couleur du nom', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#0f172a',
				'selectors' => [
					'{{WRAPPER}} .vv-product-category--modern-layout .vv-category-name' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'modern_name_hover_color',
			[
				'label'     => esc_html__( 'Couleur du nom au survol', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#0f172a',
				'selectors' => [
					'{{WRAPPER}} .vv-product-category--modern-layout:hover .vv-category-name' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'modern_count_color',
			[
				'label'     => esc_html__( 'Couleur du compteur', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#64748b',
				'selectors' => [
					'{{WRAPPER}} .vv-product-category--modern-layout .vv-category-count' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'modern_count_hover_color',
			[
				'label'     => esc_html__( 'Couleur du compteur au survol', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#64748b',
				'selectors' => [
					'{{WRAPPER}} .vv-product-category--modern-layout:hover .vv-category-count' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'modern_count_spacing',
			[
				'label'      => esc_html__( 'Espacement sous le titre', 'tools-adapter' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [ 'min' => 0, 'max' => 30 ],
				],
				'default'    => [
					'size' => 4,
					'unit' => 'px',
				],
				'selectors'  => [
					'{{WRAPPER}} .vv-product-categories' => '--vv-modern-count-spacing: {{SIZE}}{{UNIT}};',
				],
			]
		);

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

	/**
	 * Find a fallback thumbnail from one of the category's products,
	 * used when the category itself has no WooCommerce image set.
	 *
	 * @param int    $term_id Category term ID.
	 * @param string $order   'recent' or 'random'.
	 * @return int Attachment ID, or 0 if none found.
	 */
	private function get_fallback_product_thumbnail_id( $term_id, $order = 'recent' ) {
		$term_id = absint( $term_id );
		if ( ! $term_id ) {
			return 0;
		}

		$product_ids = get_posts(
			[
				'post_type'           => 'product',
				'post_status'         => 'publish',
				'posts_per_page'      => 1,
				'fields'              => 'ids',
				'ignore_sticky_posts' => true,
				'no_found_rows'       => true,
				'orderby'             => ( 'random' === $order ) ? 'rand' : 'date',
				'order'               => 'DESC',
				'tax_query'           => [ // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
					[
						'taxonomy' => 'product_cat',
						'field'    => 'term_id',
						'terms'    => [ $term_id ],
					],
				],
				'meta_query'          => [ // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
					[
						'key'     => '_thumbnail_id',
						'compare' => 'EXISTS',
					],
				],
			]
		);

		if ( empty( $product_ids ) ) {
			return 0;
		}

		return absint( get_post_thumbnail_id( $product_ids[0] ) );
	}

	/**
	 * Render the optional category image used by the image-card display.
	 * Falls back to a product image from the category when no category
	 * image is set and the fallback option is enabled.
	 *
	 * @param \WP_Term|null $category Category term, or null for the "All" button.
	 * @param array         $settings Widget settings.
	 */
	private function render_category_visual( $category, array $settings ) {
		if ( 'image_cards' !== ( $settings['display_style'] ?? 'buttons' ) || 'yes' !== ( $settings['show_category_image'] ?? 'yes' ) ) {
			return;
		}

		$image_size    = ! empty( $settings['category_image_size'] ) ? $settings['category_image_size'] : 'woocommerce_thumbnail';
		$term_id       = ( $category && ! empty( $category->term_id ) ) ? (int) $category->term_id : 0;
		$thumbnail_id  = $term_id ? absint( get_term_meta( $term_id, 'thumbnail_id', true ) ) : 0;
		$used_fallback = false;

		if ( ! $thumbnail_id && $term_id && 'yes' === ( $settings['image_fallback'] ?? 'yes' ) ) {
			$thumbnail_id  = $this->get_fallback_product_thumbnail_id( $term_id, $settings['image_fallback_order'] ?? 'recent' );
			$used_fallback = (bool) $thumbnail_id;
		}

		$media_class = 'vv-product-category__media';
		if ( $used_fallback ) {
			$media_class .= ' vv-product-category__media--fallback';
		}
		?>
		<span class="<?php echo esc_attr( $media_class ); ?>" aria-hidden="true">
			<?php
			if ( $thumbnail_id ) {
				echo wp_get_attachment_image(
					$thumbnail_id,
					$image_size,
					false,
					[
						'class' => 'vv-product-category__image',
					]
				); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			} else {
				echo '<span class="vv-product-category__placeholder"></span>';
			}
			?>
			<?php if ( 'modern' === ( $settings['image_layout'] ?? '' ) ) : ?>
				<span class="vv-product-category__overlay"></span>
			<?php endif; ?>
		</span>
		<?php
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

		$settings     = $this->get_settings_for_display();
		$mode         = $settings['filter_mode'] ?? 'links';
		$display      = $settings['display_style'] ?? 'buttons';
		$image_layout = $settings['image_layout'] ?? 'card';
		$show_name    = 'yes' === ( $settings['show_category_name'] ?? 'yes' );

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

		// Formater par défaut le compteur en "X items" pour la carte moderne
		if ( 'image_cards' === $display && 'modern' === $image_layout && '(' === $prefix && ')' === $suffix ) {
			$prefix = '';
			$suffix = ' ' . esc_html__( 'items', 'tools-adapter' );
		}

		$active_ids = [];
		if ( ! empty( $_GET['product_cat'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$active_ids = array_values( array_filter( array_map( 'absint', (array) wp_unslash( $_GET['product_cat'] ) ) ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		}

		$wrapper_class = 'vv-product-categories';
		if ( 'filter' === $mode ) {
			$wrapper_class .= ' vv-product-categories--filter';
		}
		if ( 'image_cards' === $display ) {
			$wrapper_class .= ' vv-product-categories--image-cards';
			if ( 'circle' === $image_layout ) {
				$wrapper_class .= ' vv-product-categories--circle-layout';
			} elseif ( 'modern' === $image_layout ) {
				$wrapper_class .= ' vv-product-categories--modern-layout';
			}
		}

		$show_btn = 'image_cards' === $display && 'modern' === $image_layout && 'yes' === ( $settings['show_action_button'] ?? 'yes' );
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
					class="vv-product-category vv-product-category--all<?php echo 'image_cards' === $display ? ' vv-product-category--image-card' : ''; ?><?php echo ( 'image_cards' === $display && 'circle' === $image_layout ) ? ' vv-product-category--circle-layout' : ''; ?><?php echo ( 'image_cards' === $display && 'modern' === $image_layout ) ? ' vv-product-category--modern-card' : ''; ?><?php echo empty( $active_ids ) ? ' is-active' : ''; ?>"
					data-category-id="0"
					data-category-filter
				>
					<?php $this->render_category_visual( null, $settings ); ?>
					<span class="vv-product-category__content">
						<?php if ( $show_name ) : ?>
							<span class="vv-category-name"><?php echo esc_html( \tools_adapter_translate( $settings['all_button_text'] ?: __( 'Tous', 'tools-adapter' ) ) ); ?></span>
						<?php endif; ?>
					</span>
					<?php if ( $show_btn ) : ?>
						<span class="vv-product-category__action" aria-hidden="true">
							<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
								<line x1="7" y1="17" x2="17" y2="7"></line>
								<polyline points="7 7 17 7 17 17"></polyline>
							</svg>
						</span>
					<?php endif; ?>
				</button>
			<?php endif; ?>

			<?php foreach ( $categories as $category ) : ?>
				<?php
				$is_active = in_array( (int) $category->term_id, $active_ids, true );
				$item_class = 'vv-product-category';
				if ( 'image_cards' === $display ) {
					$item_class .= ' vv-product-category--image-card';
					if ( 'circle' === $image_layout ) {
						$item_class .= ' vv-product-category--circle-layout';
					} elseif ( 'modern' === $image_layout ) {
						$item_class .= ' vv-product-category--modern-card';
					}
				}
				if ( $is_active ) {
					$item_class .= ' is-active';
				}
				if ( 'filter' === $mode ) :
					?>
					<button
						type="button"
						class="<?php echo esc_attr( $item_class ); ?>"
						data-category-id="<?php echo esc_attr( (string) $category->term_id ); ?>"
						data-category-filter
					>
						<?php $this->render_category_visual( $category, $settings ); ?>
						<span class="vv-product-category__content">
							<?php if ( $show_name ) : ?>
								<span class="vv-category-name"><?php echo esc_html( $category->name ); ?></span>
							<?php endif; ?>
							<?php if ( 'yes' === $settings['show_count'] ) : ?>
								<span class="vv-category-count"><?php echo esc_html( $prefix . $category->count . $suffix ); ?></span>
							<?php endif; ?>
						</span>
						<?php if ( $show_btn ) : ?>
							<span class="vv-product-category__action" aria-hidden="true">
								<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
									<line x1="7" y1="17" x2="17" y2="7"></line>
									<polyline points="7 7 17 7 17 17"></polyline>
								</svg>
							</span>
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
						class="<?php echo esc_attr( $item_class ); ?>"
						target="<?php echo esc_attr( $target ); ?>"
						<?php echo $rel ? 'rel="' . esc_attr( $rel ) . '"' : ''; ?>
					>
						<?php $this->render_category_visual( $category, $settings ); ?>
						<span class="vv-product-category__content">
							<?php if ( $show_name ) : ?>
								<span class="vv-category-name"><?php echo esc_html( $category->name ); ?></span>
							<?php endif; ?>
							<?php if ( 'yes' === $settings['show_count'] ) : ?>
								<span class="vv-category-count"><?php echo esc_html( $prefix . $category->count . $suffix ); ?></span>
							<?php endif; ?>
						</span>
						<?php if ( $show_btn ) : ?>
							<span class="vv-product-category__action" aria-hidden="true">
								<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
									<line x1="7" y1="17" x2="17" y2="7"></line>
									<polyline points="7 7 17 7 17 17"></polyline>
								</svg>
							</span>
						<?php endif; ?>
					</a>
				<?php endif; ?>
			<?php endforeach; ?>
		</div>
		<?php
	}
}
