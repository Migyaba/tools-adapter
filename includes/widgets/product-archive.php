<?php
namespace ToolsAdapter\Widgets;

use Elementor\Controls_Manager;
use ToolsAdapter\Base_Widget;
use ToolsAdapter\Product_Card;
use ToolsAdapter\Products_Query;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Widget: Product Archive — grille filtrable (prix + catégories AJAX).
 */
class Product_Archive extends Base_Widget {

	use Products_Widget_Controls;

	public function get_name() {
		return 'tools-adapter-product-archive';
	}

	public function get_title() {
		return esc_html__( 'Archive Produits', 'tools-adapter' );
	}

	public function get_icon() {
		return 'eicon-archive-posts';
	}

	public function get_categories() {
		return [ 'tools-adapter', 'woocommerce-elements' ];
	}

	public function get_keywords() {
		return [ 'woocommerce', 'archive', 'shop', 'filter', 'products', 'grid' ];
	}

	public function get_style_depends() {
		return [ 'tools-adapter-products', 'tools-adapter-product-archive', 'tools-adapter-modal', 'tools-adapter-quick-view' ];
	}

	public function get_script_depends() {
		return [ 'tools-adapter-archive', 'tools-adapter-modal', 'tools-adapter-quick-view' ];
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_archive_query',
			[
				'label' => esc_html__( 'Archive', 'tools-adapter' ),
			]
		);

		$this->add_control(
			'posts_per_page',
			[
				'label'   => esc_html__( 'Produits par page', 'tools-adapter' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 12,
				'min'     => 1,
				'max'     => 48,
			]
		);

		$this->add_control(
			'orderby',
			[
				'label'   => esc_html__( 'Trier par', 'tools-adapter' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'menu_order',
				'options' => [
					'menu_order' => esc_html__( 'Ordre menu', 'tools-adapter' ),
					'date'       => esc_html__( 'Plus récents', 'tools-adapter' ),
					'popularity' => esc_html__( 'Popularité', 'tools-adapter' ),
					'rating'     => esc_html__( 'Note', 'tools-adapter' ),
					'price'      => esc_html__( 'Prix croissant', 'tools-adapter' ),
					'price-desc' => esc_html__( 'Prix décroissant', 'tools-adapter' ),
					'title'      => esc_html__( 'Titre', 'tools-adapter' ),
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

		$this->add_control(
			'show_result_count',
			[
				'label'        => esc_html__( 'Compteur de résultats', 'tools-adapter' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'show_pagination',
			[
				'label'        => esc_html__( 'Pagination', 'tools-adapter' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'show_orderby',
			[
				'label'        => esc_html__( 'Sélecteur de tri', 'tools-adapter' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'update_url',
			[
				'label'        => esc_html__( 'Mettre à jour l’URL', 'tools-adapter' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'archive_note',
			[
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => '<p style="margin:0;line-height:1.45;">' . esc_html__( 'Placez sur la même page les widgets « Catégories Produits » (mode Filtre AJAX) et « Filtre par Tarifs ». Ils piloteront cette archive sans rechargement.', 'tools-adapter' ) . '</p>',
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_pagination',
			[
				'label'     => esc_html__( 'Pagination', 'tools-adapter' ),
				'condition' => [ 'show_pagination' => 'yes' ],
			]
		);

		$this->add_control(
			'pagination_type',
			[
				'label'   => esc_html__( 'Type de pagination', 'tools-adapter' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'numbers',
				'options' => [
					'numbers'   => esc_html__( 'Numérotée (classique)', 'tools-adapter' ),
					'prev_next' => esc_html__( 'Précédent / Suivant', 'tools-adapter' ),
					'load_more' => esc_html__( 'Bouton « Charger plus »', 'tools-adapter' ),
					'infinite'  => esc_html__( 'Défilement infini', 'tools-adapter' ),
				],
			]
		);

		$this->add_control(
			'show_prev_next',
			[
				'label'        => esc_html__( 'Flèches précédent/suivant', 'tools-adapter' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => [ 'pagination_type' => 'numbers' ],
			]
		);

		$this->add_control(
			'pagination_end_size',
			[
				'label'       => esc_html__( 'Pages en bord', 'tools-adapter' ),
				'description' => esc_html__( 'Nombre de pages toujours visibles au début et à la fin.', 'tools-adapter' ),
				'type'        => Controls_Manager::NUMBER,
				'min'         => 1,
				'max'         => 5,
				'default'     => 1,
				'condition'   => [ 'pagination_type' => 'numbers' ],
			]
		);

		$this->add_control(
			'pagination_mid_size',
			[
				'label'       => esc_html__( 'Pages autour de la page active', 'tools-adapter' ),
				'description' => esc_html__( 'Nombre de pages visibles avant/après la page en cours.', 'tools-adapter' ),
				'type'        => Controls_Manager::NUMBER,
				'min'         => 0,
				'max'         => 5,
				'default'     => 2,
				'condition'   => [ 'pagination_type' => 'numbers' ],
			]
		);

		$this->add_control(
			'prev_text',
			[
				'label'      => esc_html__( 'Texte « Précédent »', 'tools-adapter' ),
				'type'       => Controls_Manager::TEXT,
				'default'    => '‹',
				'conditions' => [
					'relation' => 'or',
					'terms'    => [
						[
							'relation' => 'and',
							'terms'    => [
								[ 'name' => 'pagination_type', 'operator' => '==', 'value' => 'numbers' ],
								[ 'name' => 'show_prev_next', 'operator' => '==', 'value' => 'yes' ],
							],
						],
						[ 'name' => 'pagination_type', 'operator' => '==', 'value' => 'prev_next' ],
					],
				],
			]
		);

		$this->add_control(
			'next_text',
			[
				'label'      => esc_html__( 'Texte « Suivant »', 'tools-adapter' ),
				'type'       => Controls_Manager::TEXT,
				'default'    => '›',
				'conditions' => [
					'relation' => 'or',
					'terms'    => [
						[
							'relation' => 'and',
							'terms'    => [
								[ 'name' => 'pagination_type', 'operator' => '==', 'value' => 'numbers' ],
								[ 'name' => 'show_prev_next', 'operator' => '==', 'value' => 'yes' ],
							],
						],
						[ 'name' => 'pagination_type', 'operator' => '==', 'value' => 'prev_next' ],
					],
				],
			]
		);

		$this->add_control(
			'load_more_text',
			[
				'label'     => esc_html__( 'Texte du bouton', 'tools-adapter' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'Charger plus', 'tools-adapter' ),
				'condition' => [ 'pagination_type' => 'load_more' ],
			]
		);

		$this->add_control(
			'loading_text',
			[
				'label'     => esc_html__( 'Texte « Chargement… »', 'tools-adapter' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'Chargement…', 'tools-adapter' ),
				'condition' => [ 'pagination_type' => [ 'load_more', 'infinite' ] ],
			]
		);

		$this->add_control(
			'infinite_offset',
			[
				'label'       => esc_html__( 'Déclenchement anticipé (px)', 'tools-adapter' ),
				'description' => esc_html__( 'Distance avant le bas de la grille à partir de laquelle la page suivante se charge automatiquement.', 'tools-adapter' ),
				'type'        => Controls_Manager::NUMBER,
				'min'         => 0,
				'max'         => 2000,
				'step'        => 50,
				'default'     => 300,
				'condition'   => [ 'pagination_type' => 'infinite' ],
			]
		);

		$this->add_control(
			'show_progress_text',
			[
				'label'        => esc_html__( 'Texte de progression', 'tools-adapter' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => [ 'pagination_type' => [ 'load_more', 'infinite' ] ],
			]
		);

		$this->add_control(
			'progress_text_format',
			[
				'label'       => esc_html__( 'Format du texte', 'tools-adapter' ),
				'description' => esc_html__( 'Utilisez %1$d pour le nombre affiché et %2$d pour le total.', 'tools-adapter' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( '%1$d sur %2$d produits affichés', 'tools-adapter' ),
				'condition'   => [
					'pagination_type'     => [ 'load_more', 'infinite' ],
					'show_progress_text'  => 'yes',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_layout',
			[
				'label' => esc_html__( 'Disposition', 'tools-adapter' ),
			]
		);

		$this->add_responsive_control(
			'columns',
			[
				'label'          => esc_html__( 'Colonnes', 'tools-adapter' ),
				'type'           => Controls_Manager::NUMBER,
				'min'            => 1,
				'max'            => 6,
				'default'        => 3,
				'tablet_default' => 2,
				'mobile_default' => 1,
				'selectors'      => [
					'{{WRAPPER}} .ta-products-archive__grid' => '--ta-cols: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'columns_gap',
			[
				'label'      => esc_html__( 'Espacement', 'tools-adapter' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 60,
					],
				],
				'default'    => [
					'size' => 20,
					'unit' => 'px',
				],
				'selectors'  => [
					'{{WRAPPER}} .ta-products-archive__grid' => '--ta-grid-gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		// Card display (reuse shared controls but skip source query section).
		$this->start_controls_section(
			'section_display',
			[
				'label' => esc_html__( 'Affichage carte', 'tools-adapter' ),
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
			\Elementor\Group_Control_Image_Size::get_type(),
			[
				'name'      => 'thumbnail',
				'default'   => 'woocommerce_thumbnail',
				'condition' => [ 'show_image' => 'yes' ],
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
					'h2' => 'H2', 'h3' => 'H3', 'h4' => 'H4', 'h5' => 'H5', 'h6' => 'H6', 'div' => 'div',
				],
				'condition' => [ 'show_title' => 'yes' ],
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
				'condition' => [ 'show_sale_badge' => 'yes' ],
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
				'condition' => [ 'show_add_to_cart' => 'yes' ],
			]
		);

		$this->add_control(
			'view_product_text',
			[
				'label'     => esc_html__( 'Texte « Voir »', 'tools-adapter' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'Voir le produit', 'tools-adapter' ),
				'condition' => [ 'show_add_to_cart' => 'yes' ],
			]
		);

		$this->end_controls_section();

		$this->register_products_style_controls();

		$this->start_controls_section(
			'section_style_toolbar',
			[
				'label' => esc_html__( 'Barre de résultats', 'tools-adapter' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'toolbar_color',
			[
				'label'     => esc_html__( 'Couleur texte', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ta-archive__toolbar' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_pagination',
			[
				'label'     => esc_html__( 'Pagination', 'tools-adapter' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [ 'show_pagination' => 'yes' ],
			]
		);

		$this->add_responsive_control(
			'page_gap',
			[
				'label'      => esc_html__( 'Espacement', 'tools-adapter' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 'px' => [ 'min' => 0, 'max' => 40 ] ],
				'default'    => [ 'size' => 8, 'unit' => 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .ta-archive__pagination, {{WRAPPER}} .ta-archive__pagination--prev-next' => 'gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'page_heading_numbers',
			[
				'label'     => esc_html__( 'Boutons de page', 'tools-adapter' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'page_color',
			[
				'label'     => esc_html__( 'Couleur texte', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ta-archive__page' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'page_bg',
			[
				'label'     => esc_html__( 'Couleur fond', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ta-archive__page' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'page_active_bg',
			[
				'label'     => esc_html__( 'Couleur page active', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#C9A84C',
				'selectors' => [
					'{{WRAPPER}} .ta-archive__page.is-active' => 'background-color: {{VALUE}}; border-color: {{VALUE}}; color: #fff;',
				],
			]
		);

		$this->add_control(
			'page_border_radius',
			[
				'label'      => esc_html__( 'Arrondi', 'tools-adapter' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'      => [ 'px' => [ 'min' => 0, 'max' => 50 ] ],
				'selectors'  => [
					'{{WRAPPER}} .ta-archive__page' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'page_disabled_opacity',
			[
				'label'     => esc_html__( 'Opacité désactivé', 'tools-adapter' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [ 'px' => [ 'min' => 0, 'max' => 1, 'step' => 0.05 ] ],
				'default'   => [ 'size' => 0.4 ],
				'selectors' => [
					'{{WRAPPER}} .ta-archive__page.is-disabled, {{WRAPPER}} .ta-archive__page:disabled' => 'opacity: {{SIZE}}; cursor: not-allowed;',
				],
			]
		);

		$this->add_control(
			'dots_color',
			[
				'label'     => esc_html__( 'Couleur des points de suspension', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => [ 'pagination_type' => 'numbers' ],
				'selectors' => [
					'{{WRAPPER}} .ta-archive__page-dots' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'page_status_color',
			[
				'label'     => esc_html__( 'Couleur du texte « X / Y »', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => [ 'pagination_type' => 'prev_next' ],
				'selectors' => [
					'{{WRAPPER}} .ta-archive__page-status' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'page_heading_load_more',
			[
				'label'     => esc_html__( 'Bouton « Charger plus »', 'tools-adapter' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [ 'pagination_type' => 'load_more' ],
			]
		);

		$this->add_control(
			'load_more_color',
			[
				'label'     => esc_html__( 'Couleur texte', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => [ 'pagination_type' => 'load_more' ],
				'selectors' => [
					'{{WRAPPER}} .ta-archive__load-more-btn' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'load_more_bg',
			[
				'label'     => esc_html__( 'Couleur fond', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#C9A84C',
				'condition' => [ 'pagination_type' => 'load_more' ],
				'selectors' => [
					'{{WRAPPER}} .ta-archive__load-more-btn' => 'background-color: {{VALUE}}; border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'load_more_color_hover',
			[
				'label'     => esc_html__( 'Couleur texte (survol)', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => [ 'pagination_type' => 'load_more' ],
				'selectors' => [
					'{{WRAPPER}} .ta-archive__load-more-btn:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'load_more_bg_hover',
			[
				'label'     => esc_html__( 'Couleur fond (survol)', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => [ 'pagination_type' => 'load_more' ],
				'selectors' => [
					'{{WRAPPER}} .ta-archive__load-more-btn:hover' => 'background-color: {{VALUE}}; border-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'load_more_padding',
			[
				'label'      => esc_html__( 'Espacement interne', 'tools-adapter' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'condition'  => [ 'pagination_type' => 'load_more' ],
				'selectors'  => [
					'{{WRAPPER}} .ta-archive__load-more-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'load_more_radius',
			[
				'label'      => esc_html__( 'Arrondi', 'tools-adapter' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'      => [ 'px' => [ 'min' => 0, 'max' => 50 ] ],
				'condition'  => [ 'pagination_type' => 'load_more' ],
				'selectors'  => [
					'{{WRAPPER}} .ta-archive__load-more-btn' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'page_heading_progress',
			[
				'label'     => esc_html__( 'Texte de progression / chargement', 'tools-adapter' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [ 'pagination_type' => [ 'load_more', 'infinite' ] ],
			]
		);

		$this->add_control(
			'progress_text_color',
			[
				'label'     => esc_html__( 'Couleur', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => [ 'pagination_type' => [ 'load_more', 'infinite' ] ],
				'selectors' => [
					'{{WRAPPER}} .ta-archive__load-more-progress' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'infinite_loader_color',
			[
				'label'     => esc_html__( 'Couleur du texte de chargement', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => [ 'pagination_type' => 'infinite' ],
				'selectors' => [
					'{{WRAPPER}} .ta-archive__infinite-loader' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Initial query for first paint (respects URL params).
	 *
	 * @param array $settings Settings.
	 * @return \WP_Query
	 */
	private function build_initial_query( array $settings ) {
		$page     = max( 1, get_query_var( 'paged' ) ? (int) get_query_var( 'paged' ) : ( isset( $_GET['paged'] ) ? (int) $_GET['paged'] : 1 ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$per_page = max( 1, intval( $settings['posts_per_page'] ) );
		$orderby  = $settings['orderby'] ?? 'menu_order';

		if ( isset( $_GET['orderby'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$orderby = sanitize_text_field( wp_unslash( $_GET['orderby'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		}

		$min_price = isset( $_GET['min_price'] ) ? floatval( wp_unslash( $_GET['min_price'] ) ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$max_price = isset( $_GET['max_price'] ) ? floatval( wp_unslash( $_GET['max_price'] ) ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$cat_ids   = [];
		if ( ! empty( $_GET['product_cat'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$cat_ids = array_filter( array_map( 'absint', (array) wp_unslash( $_GET['product_cat'] ) ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		}

		$args = [
			'post_type'           => 'product',
			'post_status'         => 'publish',
			'ignore_sticky_posts' => true,
			'posts_per_page'      => $per_page,
			'paged'               => $page,
			'meta_query'          => [], // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
			'tax_query'           => [ 'relation' => 'AND' ], // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
		];

		if ( 'yes' === ( $settings['hide_out_of_stock'] ?? '' ) ) {
			$args['meta_query'][] = [
				'key'     => '_stock_status',
				'value'   => 'instock',
				'compare' => '=',
			];
		}

		if ( $min_price > 0 || $max_price > 0 ) {
			$price_meta = [
				'key'     => '_price',
				'type'    => 'DECIMAL(10,2)',
				'compare' => 'BETWEEN',
				'value'   => [
					$min_price > 0 ? $min_price : 0,
					$max_price > 0 ? $max_price : PHP_INT_MAX,
				],
			];
			if ( $min_price > 0 && $max_price <= 0 ) {
				$price_meta['compare'] = '>=';
				$price_meta['value']   = $min_price;
			} elseif ( $max_price > 0 && $min_price <= 0 ) {
				$price_meta['compare'] = '<=';
				$price_meta['value']   = $max_price;
			}
			$args['meta_query'][] = $price_meta; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
		}

		if ( $cat_ids ) {
			$args['tax_query'][] = [ // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
				'taxonomy' => 'product_cat',
				'field'    => 'term_id',
				'terms'    => $cat_ids,
			];
		} elseif ( is_product_category() || is_product_tag() || is_tax() ) {
			$term = get_queried_object();
			if ( $term && ! empty( $term->term_id ) && ! empty( $term->taxonomy ) ) {
				$args['tax_query'][] = [ // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
					'taxonomy' => $term->taxonomy,
					'field'    => 'term_id',
					'terms'    => [ (int) $term->term_id ],
				];
			}
		}

		switch ( $orderby ) {
			case 'price':
				$args['meta_key'] = '_price'; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
				$args['orderby']  = 'meta_value_num';
				$args['order']    = 'ASC';
				break;
			case 'price-desc':
				$args['meta_key'] = '_price'; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
				$args['orderby']  = 'meta_value_num';
				$args['order']    = 'DESC';
				break;
			case 'popularity':
				$args['meta_key'] = 'total_sales'; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
				$args['orderby']  = 'meta_value_num';
				$args['order']    = 'DESC';
				break;
			case 'rating':
				$args['meta_key'] = '_wc_average_rating'; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
				$args['orderby']  = 'meta_value_num';
				$args['order']    = 'DESC';
				break;
			case 'date':
				$args['orderby'] = 'date';
				$args['order']   = 'DESC';
				break;
			case 'title':
				$args['orderby'] = 'title';
				$args['order']   = 'ASC';
				break;
			default:
				$args['orderby'] = 'menu_order title';
				$args['order']   = 'ASC';
				break;
		}

		return new \WP_Query( $args );
	}

	/**
	 * Card settings payload for AJAX re-render.
	 *
	 * @param array $settings Widget settings.
	 * @return array
	 */
	private function card_settings_payload( array $settings ) {
		$card = $this->get_card_settings( $settings );
		return [
			'show_image'        => $card['show_image'] ?? 'yes',
			'show_title'        => $card['show_title'] ?? 'yes',
			'show_price'        => $card['show_price'] ?? 'yes',
			'show_rating'       => $card['show_rating'] ?? '',
			'show_sale_badge'   => $card['show_sale_badge'] ?? 'yes',
			'show_add_to_cart'  => $card['show_add_to_cart'] ?? 'yes',
			'show_excerpt'      => $card['show_excerpt'] ?? '',
			'image_size'        => $card['image_size'] ?? 'woocommerce_thumbnail',
			'title_html_tag'    => $card['title_html_tag'] ?? 'h3',
			'sale_badge_text'   => \tools_adapter_translate( $card['sale_badge_text'] ?? __( 'Promo', 'tools-adapter' ) ),
			'add_to_cart_text'  => \tools_adapter_translate( $card['add_to_cart_text'] ?? __( 'Ajouter au panier', 'tools-adapter' ) ),
			'view_product_text' => \tools_adapter_translate( $card['view_product_text'] ?? __( 'Voir le produit', 'tools-adapter' ) ),
		];
	}

	/**
	 * Pagination settings payload, shared between the initial render and the
	 * AJAX response (posted back as JSON on every filter/sort/page change).
	 *
	 * @param array $settings Widget settings.
	 * @return array
	 */
	private function pagination_settings_payload( array $settings ) {
		return \ToolsAdapter\Pagination::parse_settings(
			[
				'type'            => $settings['pagination_type'] ?? 'numbers',
				'show_prev_next'  => $settings['show_prev_next'] ?? 'yes',
				'end_size'        => $settings['pagination_end_size'] ?? 1,
				'mid_size'        => $settings['pagination_mid_size'] ?? 2,
				'prev_text'       => \tools_adapter_translate( $settings['prev_text'] ?? '‹' ),
				'next_text'       => \tools_adapter_translate( $settings['next_text'] ?? '›' ),
				'load_more_text'  => \tools_adapter_translate( $settings['load_more_text'] ?? __( 'Charger plus', 'tools-adapter' ) ),
				'loading_text'    => \tools_adapter_translate( $settings['loading_text'] ?? __( 'Chargement…', 'tools-adapter' ) ),
				'show_progress'   => $settings['show_progress_text'] ?? 'yes',
				'progress_format' => \tools_adapter_translate( $settings['progress_text_format'] ?? __( '%1$d sur %2$d produits affichés', 'tools-adapter' ) ),
				'infinite_offset' => $settings['infinite_offset'] ?? 300,
			]
		);
	}

	protected function render() {
		if ( ! class_exists( 'WooCommerce' ) ) {
			if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
				echo '<p>' . esc_html__( 'WooCommerce est requis pour ce widget.', 'tools-adapter' ) . '</p>';
			}
			return;
		}

		$settings = $this->get_settings_for_display();
		$query    = $this->build_initial_query( $settings );
		$card     = $this->card_settings_payload( $settings );

		$current_page = max( 1, (int) $query->get( 'paged' ) );
		if ( $current_page < 1 ) {
			$current_page = 1;
		}

		$min_price = isset( $_GET['min_price'] ) ? floatval( wp_unslash( $_GET['min_price'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$max_price = isset( $_GET['max_price'] ) ? floatval( wp_unslash( $_GET['max_price'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$cat_ids   = [];
		if ( ! empty( $_GET['product_cat'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$cat_ids = array_values( array_filter( array_map( 'absint', (array) wp_unslash( $_GET['product_cat'] ) ) ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		}

		$orderby = isset( $_GET['orderby'] ) ? sanitize_text_field( wp_unslash( $_GET['orderby'] ) ) : ( $settings['orderby'] ?? 'menu_order' ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		$per_page            = max( 1, intval( $settings['posts_per_page'] ) );
		$pagination_settings = $this->pagination_settings_payload( $settings );

		$config = [
			'perPage'            => $per_page,
			'orderby'            => $orderby,
			'page'               => $current_page,
			'minPrice'           => $min_price !== '' ? $min_price : null,
			'maxPrice'           => $max_price !== '' ? $max_price : null,
			'categoryIds'        => $cat_ids,
			'updateUrl'          => ( 'yes' === ( $settings['update_url'] ?? '' ) ),
			'cardSettings'       => $card,
			'renderMode'         => 'archive',
			'paginationSettings' => $pagination_settings,
		];
		?>
		<div
			class="ta-products-archive"
			data-ta-archive="1"
			data-config="<?php echo esc_attr( wp_json_encode( $config ) ); ?>"
		>
			<div class="ta-archive__toolbar">
				<?php if ( 'yes' === ( $settings['show_result_count'] ?? '' ) ) : ?>
					<p class="ta-archive__count" data-result-count>
						<?php
						$count = (int) $query->found_posts;
						printf(
							/* translators: %d: products found */
							esc_html( _n( '%d produit trouvé', '%d produits trouvés', $count, 'tools-adapter' ) ),
							$count
						);
						?>
					</p>
				<?php endif; ?>

				<?php if ( 'yes' === ( $settings['show_orderby'] ?? '' ) ) : ?>
					<label class="ta-archive__orderby">
						<span class="screen-reader-text"><?php echo esc_html__( 'Trier par', 'tools-adapter' ); ?></span>
						<select data-orderby>
							<option value="menu_order" <?php selected( $orderby, 'menu_order' ); ?>><?php echo esc_html__( 'Par défaut', 'tools-adapter' ); ?></option>
							<option value="popularity" <?php selected( $orderby, 'popularity' ); ?>><?php echo esc_html__( 'Popularité', 'tools-adapter' ); ?></option>
							<option value="rating" <?php selected( $orderby, 'rating' ); ?>><?php echo esc_html__( 'Note', 'tools-adapter' ); ?></option>
							<option value="date" <?php selected( $orderby, 'date' ); ?>><?php echo esc_html__( 'Plus récents', 'tools-adapter' ); ?></option>
							<option value="price" <?php selected( $orderby, 'price' ); ?>><?php echo esc_html__( 'Prix croissant', 'tools-adapter' ); ?></option>
							<option value="price-desc" <?php selected( $orderby, 'price-desc' ); ?>><?php echo esc_html__( 'Prix décroissant', 'tools-adapter' ); ?></option>
							<option value="title" <?php selected( $orderby, 'title' ); ?>><?php echo esc_html__( 'Titre', 'tools-adapter' ); ?></option>
						</select>
					</label>
				<?php endif; ?>
			</div>

			<div class="ta-products ta-products-grid ta-products-archive__grid" data-archive-grid>
				<?php
				if ( $query->have_posts() ) {
					while ( $query->have_posts() ) {
						$query->the_post();
						$product = wc_get_product( get_the_ID() );
						if ( $product ) {
							Product_Card::render( $product, $this->get_card_settings( $settings ) );
						}
					}
					wp_reset_postdata();
				} else {
					echo '<p class="ta-products-empty">' . esc_html__( 'Aucun produit trouvé.', 'tools-adapter' ) . '</p>';
				}
				?>
			</div>

			<?php if ( 'yes' === ( $settings['show_pagination'] ?? '' ) ) : ?>
				<div class="ta-archive__pagination-wrap" data-pagination>
					<?php
					echo \ToolsAdapter\Pagination::render(
						[
							'current'   => $current_page,
							'max_pages' => (int) $query->max_num_pages,
							'found'     => (int) $query->found_posts,
							'per_page'  => $per_page,
							'settings'  => $pagination_settings,
						]
					);
					?>
				</div>
			<?php endif; ?>
		</div>
		<?php
	}
}
