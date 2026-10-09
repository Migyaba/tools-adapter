<?php
namespace ToolsAdapter\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use ToolsAdapter\Products_Query;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Shared Elementor controls for Product Grid & Carousel.
 */
trait Products_Widget_Controls {

	/**
	 * Content: query + display toggles.
	 */
	protected function register_products_content_controls() {
		$this->start_controls_section(
			'section_query',
			[
				'label' => esc_html__( 'Requête produits', 'tools-adapter' ),
			]
		);

		$this->add_control(
			'source',
			[
				'label'   => esc_html__( 'Source', 'tools-adapter' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'recent',
				'options' => Products_Query::get_source_options(),
			]
		);

		$this->add_control(
			'category_ids',
			[
				'label'       => esc_html__( 'IDs catégories', 'tools-adapter' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => '12, 34',
				'label_block' => true,
				'description' => esc_html__( 'IDs séparés par des virgules.', 'tools-adapter' ),
				'condition'   => [
					'source' => 'category',
				],
			]
		);

		$this->add_control(
			'product_ids',
			[
				'label'       => esc_html__( 'IDs produits', 'tools-adapter' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => '101, 102, 103',
				'label_block' => true,
				'condition'   => [
					'source' => 'manual',
				],
			]
		);

		$this->add_control(
			'filter_category_ids',
			[
				'label'       => esc_html__( 'Filtrer aussi par catégorie (optionnel)', 'tools-adapter' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => '12, 34',
				'label_block' => true,
				'condition'   => [
					'source!' => [ 'category', 'manual' ],
				],
			]
		);

		$this->add_control(
			'exclude_ids',
			[
				'label'       => esc_html__( 'Exclure IDs', 'tools-adapter' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => '55, 66',
				'label_block' => true,
			]
		);

		$this->add_control(
			'posts_per_page',
			[
				'label'   => esc_html__( 'Nombre de produits', 'tools-adapter' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 8,
				'min'     => 1,
				'max'     => 48,
			]
		);

		$this->add_control(
			'orderby',
			[
				'label'     => esc_html__( 'Trier par', 'tools-adapter' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'date',
				'options'   => [
					'date'       => esc_html__( 'Date', 'tools-adapter' ),
					'title'      => esc_html__( 'Titre', 'tools-adapter' ),
					'price'      => esc_html__( 'Prix', 'tools-adapter' ),
					'popularity' => esc_html__( 'Popularité', 'tools-adapter' ),
					'rating'     => esc_html__( 'Note', 'tools-adapter' ),
					'menu_order' => esc_html__( 'Ordre menu', 'tools-adapter' ),
					'rand'       => esc_html__( 'Aléatoire', 'tools-adapter' ),
				],
				'condition' => [
					'source' => [ 'recent', 'category', 'featured' ],
				],
			]
		);

		$this->add_control(
			'order',
			[
				'label'     => esc_html__( 'Ordre', 'tools-adapter' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'DESC',
				'options'   => [
					'DESC' => esc_html__( 'Décroissant', 'tools-adapter' ),
					'ASC'  => esc_html__( 'Croissant', 'tools-adapter' ),
				],
				'condition' => [
					'source!' => [ 'best_selling', 'top_rated', 'manual' ],
				],
			]
		);

		$this->add_control(
			'hide_out_of_stock',
			[
				'label'        => esc_html__( 'Masquer rupture de stock', 'tools-adapter' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_display',
			[
				'label' => esc_html__( 'Affichage carte', 'tools-adapter' ),
			]
		);

		$this->add_control(
			'card_style',
			[
				'label'       => esc_html__( 'Style de carte', 'tools-adapter' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'classic',
				'options'     => [
					'classic'  => esc_html__( 'Classique (bouton visible)', 'tools-adapter' ),
					'modern'   => esc_html__( 'Moderne (panier révélé au survol)', 'tools-adapter' ),
					'minimal'  => esc_html__( 'Minimal (nom et prix sur une ligne, icône panier)', 'tools-adapter' ),
					'overlay'  => esc_html__( 'Superposé (infos sur l\'image)', 'tools-adapter' ),
					'elevated' => esc_html__( 'Élevé (carte encadrée, bouton visible)', 'tools-adapter' ),
				],
				'description' => esc_html__( 'Moderne : 2e image au survol, cœur et vue rapide sur l\'image, bouton panier révélé au survol, catégorie et badge en pourcentage.', 'tools-adapter' ),
			]
		);

		$this->add_control(
			'hover_image',
			[
				'label'        => esc_html__( '2e image au survol', 'tools-adapter' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => [ 'card_style!' => 'classic' ],
			]
		);

		$this->add_control(
			'show_category',
			[
				'label'        => esc_html__( 'Afficher la catégorie', 'tools-adapter' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => [ 'card_style!' => 'classic' ],
			]
		);

		$this->add_control(
			'show_image',
			[
				'label'        => esc_html__( 'Image', 'tools-adapter' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'      => 'thumbnail',
				'default'   => 'woocommerce_thumbnail',
				'condition' => [
					'show_image' => 'yes',
				],
			]
		);

		$this->add_control(
			'show_title',
			[
				'label'        => esc_html__( 'Titre', 'tools-adapter' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'title_html_tag',
			[
				'label'     => esc_html__( 'Balise titre', 'tools-adapter' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'h3',
				'options'   => [
					'h2'  => 'H2',
					'h3'  => 'H3',
					'h4'  => 'H4',
					'h5'  => 'H5',
					'h6'  => 'H6',
					'div' => 'div',
				],
				'condition' => [
					'show_title' => 'yes',
				],
			]
		);

		$this->add_control(
			'show_price',
			[
				'label'        => esc_html__( 'Prix', 'tools-adapter' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'show_rating',
			[
				'label'        => esc_html__( 'Note', 'tools-adapter' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
			]
		);

		$this->add_control(
			'show_excerpt',
			[
				'label'        => esc_html__( 'Extrait', 'tools-adapter' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
			]
		);

		$this->add_control(
			'show_sale_badge',
			[
				'label'        => esc_html__( 'Badge promo', 'tools-adapter' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'sale_badge_text',
			[
				'label'     => esc_html__( 'Texte badge', 'tools-adapter' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'Promo', 'tools-adapter' ),
				'condition' => [
					'show_sale_badge' => 'yes',
				],
			]
		);

		$this->add_control(
			'show_add_to_cart',
			[
				'label'        => esc_html__( 'Bouton panier / voir', 'tools-adapter' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'add_to_cart_text',
			[
				'label'     => esc_html__( 'Texte « Ajouter »', 'tools-adapter' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'Ajouter au panier', 'tools-adapter' ),
				'condition' => [
					'show_add_to_cart' => 'yes',
				],
			]
		);

		$this->add_control(
			'view_product_text',
			[
				'label'     => esc_html__( 'Texte « Voir »', 'tools-adapter' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'Voir le produit', 'tools-adapter' ),
				'condition' => [
					'show_add_to_cart' => 'yes',
				],
			]
		);

		$this->add_control(
			'show_quick_view',
			[
				'label'        => esc_html__( 'Bouton vue rapide', 'tools-adapter' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
				'separator'    => 'before',
			]
		);

		$this->add_control(
			'show_wishlist',
			[
				'label'        => esc_html__( 'Bouton liste de souhaits', 'tools-adapter' ),
				'description'  => esc_html__( 'Nécessite la fonctionnalité « Liste de souhaits » dans les réglages Tools Adapter.', 'tools-adapter' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Style controls for product cards.
	 */
	protected function register_products_style_controls() {
		$this->start_controls_section(
			'section_style_card',
			[
				'label' => esc_html__( 'Carte produit', 'tools-adapter' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'card_background',
				'types'    => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .ta-product-card__inner',
			]
		);

		$this->add_responsive_control(
			'card_padding',
			[
				'label'      => esc_html__( 'Padding contenu', 'tools-adapter' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'default'    => [
					'top'      => '16',
					'right'    => '16',
					'bottom'   => '16',
					'left'     => '16',
					'unit'     => 'px',
					'isLinked' => true,
				],
				'selectors'  => [
					'{{WRAPPER}} .ta-product-card' => '--ta-body-pad-top: {{TOP}}{{UNIT}}; --ta-body-pad-right: {{RIGHT}}{{UNIT}}; --ta-body-pad-bottom: {{BOTTOM}}{{UNIT}}; --ta-body-pad-left: {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'card_border',
				'selector' => '{{WRAPPER}} .ta-product-card__inner',
			]
		);

		$this->add_responsive_control(
			'card_radius',
			[
				'label'      => esc_html__( 'Border radius', 'tools-adapter' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'default'    => [
					'top'      => '8',
					'right'    => '8',
					'bottom'   => '8',
					'left'     => '8',
					'unit'     => 'px',
					'isLinked' => true,
				],
				'selectors'  => [
					'{{WRAPPER}} .ta-product-card' => '--ta-card-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'card_shadow',
				'selector' => '{{WRAPPER}} .ta-product-card__inner',
			]
		);

		$this->add_control(
			'card_align',
			[
				'label'     => esc_html__( 'Alignement texte', 'tools-adapter' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'left'   => [
						'title' => esc_html__( 'Gauche', 'tools-adapter' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Centre', 'tools-adapter' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [
						'title' => esc_html__( 'Droite', 'tools-adapter' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'default'   => 'left',
				'selectors' => [
					'{{WRAPPER}} .ta-product-card__body' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_image',
			[
				'label'     => esc_html__( 'Image', 'tools-adapter' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_image' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'image_height',
			[
				'label'      => esc_html__( 'Hauteur', 'tools-adapter' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 100,
						'max' => 500,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .ta-product-card__media' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'image_object_fit',
			[
				'label'     => esc_html__( 'Object fit', 'tools-adapter' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'cover',
				'options'   => [
					'cover'   => 'Cover',
					'contain' => 'Contain',
					'fill'    => 'Fill',
				],
				'selectors' => [
					'{{WRAPPER}} .ta-product-card__image' => 'object-fit: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'image_hover_zoom',
			[
				'label'     => esc_html__( 'Zoom au survol', 'tools-adapter' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min'  => 1,
						'max'  => 1.3,
						'step' => 0.01,
					],
				],
				'default'   => [
					'size' => 1.05,
				],
				'selectors' => [
					'{{WRAPPER}} .ta-product-card' => '--ta-img-zoom: {{SIZE}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_title',
			[
				'label'     => esc_html__( 'Titre', 'tools-adapter' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_title' => 'yes',
				],
			]
		);

		$this->add_control(
			'title_color',
			[
				'label'     => esc_html__( 'Couleur', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#222222',
				'selectors' => [
					'{{WRAPPER}} .ta-product-card' => '--ta-title-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'title_color_hover',
			[
				'label'     => esc_html__( 'Couleur survol', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#C9A84C',
				'selectors' => [
					'{{WRAPPER}} .ta-product-card' => '--ta-title-hover: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .ta-product-card__title',
			]
		);

		$this->add_responsive_control(
			'title_spacing',
			[
				'label'      => esc_html__( 'Espacement bas', 'tools-adapter' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .ta-product-card__title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_price',
			[
				'label'     => esc_html__( 'Prix', 'tools-adapter' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_price' => 'yes',
				],
			]
		);

		$this->add_control(
			'price_color',
			[
				'label'     => esc_html__( 'Couleur', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#222222',
				'selectors' => [
					'{{WRAPPER}} .ta-product-card' => '--ta-price-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'price_typography',
				'selector' => '{{WRAPPER}} .ta-product-card__price',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_badge',
			[
				'label'     => esc_html__( 'Badge promo', 'tools-adapter' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_sale_badge' => 'yes',
				],
			]
		);

		$this->add_control(
			'badge_bg',
			[
				'label'     => esc_html__( 'Fond', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#C9A84C',
				'selectors' => [
					'{{WRAPPER}} .ta-product-card' => '--ta-badge-bg: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'badge_color',
			[
				'label'     => esc_html__( 'Texte', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .ta-product-card' => '--ta-badge-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'badge_typography',
				'selector' => '{{WRAPPER}} .ta-product-card__badge',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_button',
			[
				'label'     => esc_html__( 'Bouton', 'tools-adapter' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_add_to_cart' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'btn_typography',
				'selector' => '{{WRAPPER}} .ta-product-card__btn',
				'exclude'  => [ 'color' ],
			]
		);

		$this->add_responsive_control(
			'btn_padding',
			[
				'label'      => esc_html__( 'Padding', 'tools-adapter' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'default'    => [
					'top'      => '10',
					'right'    => '16',
					'bottom'   => '10',
					'left'     => '16',
					'unit'     => 'px',
					'isLinked' => false,
				],
				'selectors'  => [
					'{{WRAPPER}} .ta-product-card' => '--ta-btn-pad-top: {{TOP}}{{UNIT}}; --ta-btn-pad-right: {{RIGHT}}{{UNIT}}; --ta-btn-pad-bottom: {{BOTTOM}}{{UNIT}}; --ta-btn-pad-left: {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'btn_radius',
			[
				'label'      => esc_html__( 'Border radius', 'tools-adapter' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 40,
					],
				],
				'default'    => [
					'size' => 6,
					'unit' => 'px',
				],
				'selectors'  => [
					'{{WRAPPER}} .ta-product-card' => '--ta-btn-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs( 'btn_tabs' );

		$this->start_controls_tab(
			'btn_normal',
			[
				'label' => esc_html__( 'Normal', 'tools-adapter' ),
			]
		);

		$this->add_control(
			'btn_bg',
			[
				'label'     => esc_html__( 'Fond', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#C9A84C',
				'selectors' => [
					'{{WRAPPER}} .ta-product-card' => '--ta-btn-bg: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'btn_color',
			[
				'label'     => esc_html__( 'Texte', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .ta-product-card' => '--ta-btn-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'btn_hover',
			[
				'label' => esc_html__( 'Survol', 'tools-adapter' ),
			]
		);

		$this->add_control(
			'btn_bg_hover',
			[
				'label'     => esc_html__( 'Fond', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ta-product-card' => '--ta-btn-bg-hover: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'btn_color_hover',
			[
				'label'     => esc_html__( 'Texte', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ta-product-card' => '--ta-btn-color-hover: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Normalize card settings before render.
	 *
	 * @param array $settings Raw settings.
	 * @return array
	 */
	protected function get_card_settings( array $settings ) {
		$image_size = 'woocommerce_thumbnail';
		if ( ! empty( $settings['thumbnail_size'] ) ) {
			if ( 'custom' === $settings['thumbnail_size'] ) {
				$image_size = [
					absint( $settings['thumbnail_custom_dimension']['width'] ?? 300 ),
					absint( $settings['thumbnail_custom_dimension']['height'] ?? 300 ),
				];
			} else {
				$image_size = $settings['thumbnail_size'];
			}
		}

		$settings['image_size'] = $image_size;
		return $settings;
	}
}
