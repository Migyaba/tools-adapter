<?php
/**
 * Tools Adapter — Widget Grille d'articles de blog (Mode classique & Mode onglets)
 *
 * @package ToolsAdapter
 */

namespace ToolsAdapter\Widgets;

use ToolsAdapter\Base_Widget;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Blog_Grid extends Base_Widget {

	public function get_name() {
		return 'tools-adapter-blog-grid';
	}

	public function get_title() {
		return esc_html__( 'Grille de blog', 'tools-adapter' );
	}

	public function get_icon() {
		return 'eicon-post-list';
	}

	public function get_categories() {
		return [ 'tools-adapter' ];
	}

	public function get_keywords() {
		return [ 'blog', 'articles', 'grille', 'actualités', 'onglets', 'catégories' ];
	}

	public function get_style_depends() {
		return [ 'tools-adapter-blog-grid' ];
	}

	public function get_script_depends() {
		return [ 'tools-adapter-blog-grid' ];
	}

	/**
	 * @return array<int,string>
	 */
	private function get_category_options() {
		$terms   = get_categories( [ 'hide_empty' => false ] );
		$options = [];
		foreach ( $terms as $term ) {
			$options[ $term->term_id ] = $term->name;
		}
		return $options;
	}

	protected function register_controls() {

		// =========================================================================
		// CONTENU : CONFIGURATION GÉNÉRALE & DISPOSITION
		// =========================================================================
		$this->start_controls_section(
			'section_layout',
			[
				'label' => esc_html__( 'Disposition & Mode', 'tools-adapter' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'display_mode',
			[
				'label'   => esc_html__( 'Mode d\'affichage', 'tools-adapter' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'tabs',
				'options' => [
					'classic' => esc_html__( 'Mode classique (Grille simple)', 'tools-adapter' ),
					'tabs'    => esc_html__( 'Mode onglets (Filtrage par catégorie)', 'tools-adapter' ),
				],
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
					'{{WRAPPER}} .ta-blog-grid' => '--ta-blog-cols: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		// =========================================================================
		// CONTENU : ONGLETS PAR CATÉGORIE (si mode tabs)
		// =========================================================================
		$this->start_controls_section(
			'section_tabs_settings',
			[
				'label'     => esc_html__( 'Onglets de Catégorie', 'tools-adapter' ),
				'tab'       => Controls_Manager::TAB_CONTENT,
				'condition' => [
					'display_mode' => 'tabs',
				],
			]
		);

		$this->add_control(
			'show_all_tab',
			[
				'label'        => esc_html__( 'Onglet « Tous »', 'tools-adapter' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			]
		);

		$this->add_control(
			'all_tab_text',
			[
				'label'       => esc_html__( 'Texte de l\'onglet « Tous »', 'tools-adapter' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Tous les articles', 'tools-adapter' ),
				'condition'   => [ 'show_all_tab' => 'yes' ],
			]
		);

		$this->add_control(
			'show_tab_count',
			[
				'label'        => esc_html__( 'Afficher le compteur d\'articles', 'tools-adapter' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			]
		);

		$this->add_control(
			'show_tab_icons',
			[
				'label'        => esc_html__( 'Afficher les icônes thématiques', 'tools-adapter' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			]
		);

		$this->end_controls_section();

		// =========================================================================
		// CONTENU : REQUÊTE DES ARTICLES
		// =========================================================================
		$this->start_controls_section(
			'section_query',
			[
				'label' => esc_html__( 'Requête des articles', 'tools-adapter' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'categories',
			[
				'label'       => esc_html__( 'Filtrer par catégories', 'tools-adapter' ),
				'type'        => Controls_Manager::SELECT2,
				'multiple'    => true,
				'options'     => $this->get_category_options(),
				'description' => esc_html__( 'Laissez vide pour inclure toutes les catégories avec articles.', 'tools-adapter' ),
			]
		);

		$this->add_control(
			'posts_count',
			[
				'label'   => esc_html__( 'Nombre d\'articles au total', 'tools-adapter' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 9,
				'min'     => 1,
				'max'     => 48,
			]
		);

		$this->add_control(
			'orderby',
			[
				'label'   => esc_html__( 'Trier par', 'tools-adapter' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'date',
				'options' => [
					'date'          => esc_html__( 'Date', 'tools-adapter' ),
					'title'         => esc_html__( 'Titre', 'tools-adapter' ),
					'rand'          => esc_html__( 'Aléatoire', 'tools-adapter' ),
					'comment_count' => esc_html__( 'Nombre de commentaires', 'tools-adapter' ),
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
				'condition' => [ 'orderby!' => 'rand' ],
			]
		);

		$this->add_control(
			'enable_pagination',
			[
				'label'        => esc_html__( 'Pagination native', 'tools-adapter' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
				'condition'    => [ 'display_mode' => 'classic' ],
			]
		);

		$this->end_controls_section();

		// =========================================================================
		// CONTENU : ÉLÉMENTS DE LA CARTE
		// =========================================================================
		$this->start_controls_section(
			'section_card_elements',
			[
				'label' => esc_html__( 'Éléments de la carte', 'tools-adapter' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'show_image',
			[
				'label'        => esc_html__( 'Image mise en avant', 'tools-adapter' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'show_category_badge',
			[
				'label'        => esc_html__( 'Badge catégorie flottant sur l\'image', 'tools-adapter' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => [ 'show_image' => 'yes' ],
			]
		);

		$this->add_control(
			'title_tag',
			[
				'label'   => esc_html__( 'Balise HTML du titre', 'tools-adapter' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'h2'   => 'H2',
					'h3'   => 'H3',
					'h4'   => 'H4',
					'div'  => 'div',
					'span' => 'span',
				],
				'default' => 'h3',
			]
		);

		$this->add_control(
			'show_excerpt',
			[
				'label'        => esc_html__( 'Extrait de texte', 'tools-adapter' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'excerpt_length',
			[
				'label'     => esc_html__( 'Nombre de mots de l\'extrait', 'tools-adapter' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 18,
				'min'       => 5,
				'max'       => 80,
				'condition' => [ 'show_excerpt' => 'yes' ],
			]
		);

		$this->add_control(
			'footer_left_meta',
			[
				'label'   => esc_html__( 'Information gauche (Pied de carte)', 'tools-adapter' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'tag',
				'options' => [
					'tag'      => esc_html__( 'Première étiquette (Tag)', 'tools-adapter' ),
					'date'     => esc_html__( 'Date de publication', 'tools-adapter' ),
					'author'   => esc_html__( 'Auteur', 'tools-adapter' ),
					'category' => esc_html__( 'Catégorie secondaire', 'tools-adapter' ),
					'none'     => esc_html__( 'Aucune (Masquer)', 'tools-adapter' ),
				],
			]
		);

		$this->add_control(
			'show_read_more',
			[
				'label'        => esc_html__( 'Lien d\'action (Lire le conseil →)', 'tools-adapter' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'read_more_text',
			[
				'label'       => esc_html__( 'Texte du lien d\'action', 'tools-adapter' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Lire le conseil', 'tools-adapter' ),
				'condition'   => [ 'show_read_more' => 'yes' ],
			]
		);

		$this->end_controls_section();

		// =========================================================================
		// STYLE : ONGLETS DE FILTRAGE
		// =========================================================================
		$this->start_controls_section(
			'section_style_tabs',
			[
				'label'     => esc_html__( 'Onglets de Filtrage', 'tools-adapter' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [ 'display_mode' => 'tabs' ],
			]
		);

		$this->add_responsive_control(
			'tabs_align',
			[
				'label'     => esc_html__( 'Alignement des onglets', 'tools-adapter' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'flex-start' => [
						'title' => esc_html__( 'Gauche', 'tools-adapter' ),
						'icon'  => 'eicon-h-align-left',
					],
					'center'     => [
						'title' => esc_html__( 'Centre', 'tools-adapter' ),
						'icon'  => 'eicon-h-align-center',
					],
					'flex-end'   => [
						'title' => esc_html__( 'Droite', 'tools-adapter' ),
						'icon'  => 'eicon-h-align-right',
					],
				],
				'default'   => 'center',
				'selectors' => [
					'{{WRAPPER}} .ta-blog-tabs' => '--ta-tabs-align: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'tabs_gap',
			[
				'label'      => esc_html__( 'Espacement entre onglets', 'tools-adapter' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [ 'min' => 4, 'max' => 30 ],
				],
				'default'    => [
					'size' => 12,
					'unit' => 'px',
				],
				'selectors'  => [
					'{{WRAPPER}} .ta-blog-tabs' => '--ta-tabs-gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'tabs_margin_bottom',
			[
				'label'      => esc_html__( 'Marge sous les onglets', 'tools-adapter' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [ 'min' => 10, 'max' => 70 ],
				],
				'default'    => [
					'size' => 36,
					'unit' => 'px',
				],
				'selectors'  => [
					'{{WRAPPER}} .ta-blog-tabs' => '--ta-tabs-margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'tab_typography',
				'selector' => '{{WRAPPER}} .ta-blog-tab',
			]
		);

		$this->add_responsive_control(
			'tab_padding',
			[
				'label'      => esc_html__( 'Marge interne (Padding)', 'tools-adapter' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'default'    => [
					'top'      => 10,
					'right'    => 20,
					'bottom'   => 10,
					'left'     => 20,
					'unit'     => 'px',
					'isLinked' => false,
				],
				'selectors'  => [
					'{{WRAPPER}} .ta-blog-tab' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'tab_radius',
			[
				'label'      => esc_html__( 'Arrondi des onglets', 'tools-adapter' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'default'    => [
					'top'      => 999,
					'right'    => 999,
					'bottom'   => 999,
					'left'     => 999,
					'unit'     => 'px',
					'isLinked' => true,
				],
				'selectors'  => [
					'{{WRAPPER}} .ta-blog-tab' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs( 'tabs_style_states' );

		// État Normal
		$this->start_controls_tab(
			'tab_style_normal',
			[
				'label' => esc_html__( 'Inactif', 'tools-adapter' ),
			]
		);

		$this->add_control(
			'tab_bg',
			[
				'label'     => esc_html__( 'Couleur de fond', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .ta-blog-tab' => '--ta-tab-bg: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'tab_color',
			[
				'label'     => esc_html__( 'Couleur du texte', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#334155',
				'selectors' => [
					'{{WRAPPER}} .ta-blog-tab' => '--ta-tab-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'tab_border_color',
			[
				'label'     => esc_html__( 'Couleur de bordure', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#e2e8f0',
				'selectors' => [
					'{{WRAPPER}} .ta-blog-tab' => '--ta-tab-border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		// État Survol
		$this->start_controls_tab(
			'tab_style_hover',
			[
				'label' => esc_html__( 'Survol', 'tools-adapter' ),
			]
		);

		$this->add_control(
			'tab_hover_bg',
			[
				'label'     => esc_html__( 'Couleur de fond au survol', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#f8fafc',
				'selectors' => [
					'{{WRAPPER}} .ta-blog-tab' => '--ta-tab-hover-bg: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'tab_hover_color',
			[
				'label'     => esc_html__( 'Couleur du texte au survol', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#0f172a',
				'selectors' => [
					'{{WRAPPER}} .ta-blog-tab' => '--ta-tab-hover-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'tab_hover_border_color',
			[
				'label'     => esc_html__( 'Bordure au survol', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#cbd5e1',
				'selectors' => [
					'{{WRAPPER}} .ta-blog-tab' => '--ta-tab-hover-border: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		// État Actif
		$this->start_controls_tab(
			'tab_style_active',
			[
				'label' => esc_html__( 'Actif', 'tools-adapter' ),
			]
		);

		$this->add_control(
			'tab_active_bg',
			[
				'label'     => esc_html__( 'Couleur de fond actif', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#143d2b',
				'selectors' => [
					'{{WRAPPER}} .ta-blog-tab' => '--ta-tab-active-bg: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'tab_active_color',
			[
				'label'     => esc_html__( 'Couleur du texte actif', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .ta-blog-tab' => '--ta-tab-active-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'tab_active_border_color',
			[
				'label'     => esc_html__( 'Bordure actif', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#143d2b',
				'selectors' => [
					'{{WRAPPER}} .ta-blog-tab' => '--ta-tab-active-border: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();

		// =========================================================================
		// STYLE : CARTE ARTICLE
		// =========================================================================
		$this->start_controls_section(
			'section_style_card',
			[
				'label' => esc_html__( 'Carte d\'article', 'tools-adapter' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'card_bg',
			[
				'label'     => esc_html__( 'Fond de la carte', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .ta-blog-card' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'      => 'card_border',
				'selector'  => '{{WRAPPER}} .ta-blog-card',
				'fields_options' => [
					'border' => [ 'default' => 'solid' ],
					'width'  => [ 'default' => [ 'top' => '1', 'right' => '1', 'bottom' => '1', 'left' => '1', 'isLinked' => true ] ],
					'color'  => [ 'default' => '#eef2f6' ],
				],
			]
		);

		$this->add_responsive_control(
			'card_radius',
			[
				'label'      => esc_html__( 'Arrondi des coins (Border Radius)', 'tools-adapter' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'default'    => [
					'top'      => 20,
					'right'    => 20,
					'bottom'   => 20,
					'left'     => 20,
					'unit'     => 'px',
					'isLinked' => true,
				],
				'selectors'  => [
					'{{WRAPPER}} .ta-blog-card' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'card_shadow',
				'selector' => '{{WRAPPER}} .ta-blog-card',
			]
		);

		$this->add_responsive_control(
			'items_gap',
			[
				'label'      => esc_html__( 'Espacement entre cartes (Gap)', 'tools-adapter' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => [
					'px' => [ 'min' => 10, 'max' => 60 ],
				],
				'default'    => [
					'size' => 28,
					'unit' => 'px',
				],
				'separator'  => 'before',
				'selectors'  => [
					'{{WRAPPER}} .ta-blog-grid' => '--ta-blog-gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'image_ratio',
			[
				'label'      => esc_html__( 'Proportions de l\'image (%)', 'tools-adapter' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => [
					'px' => [ 'min' => 40, 'max' => 100 ],
				],
				'default'    => [
					'size' => 62,
				],
				'selectors'  => [
					'{{WRAPPER}} .ta-blog-card__media' => 'padding-top: {{SIZE}}%;',
				],
			]
		);

		$this->add_responsive_control(
			'card_body_pad',
			[
				'label'      => esc_html__( 'Marge interne du contenu', 'tools-adapter' ),
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
					'{{WRAPPER}} .ta-blog-card__body' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		// =========================================================================
		// STYLE : BADGE CATÉGORIE FLOTTANT
		// =========================================================================
		$this->start_controls_section(
			'section_style_badge',
			[
				'label' => esc_html__( 'Badge Catégorie Flottant', 'tools-adapter' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'badge_bg',
			[
				'label'     => esc_html__( 'Fond du badge', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#1a2e22',
				'selectors' => [
					'{{WRAPPER}} .ta-blog-card__badge' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'badge_color',
			[
				'label'     => esc_html__( 'Texte du badge', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .ta-blog-card__badge' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'badge_typography',
				'selector' => '{{WRAPPER}} .ta-blog-card__badge',
			]
		);

		$this->add_responsive_control(
			'badge_radius',
			[
				'label'      => esc_html__( 'Arrondi du badge', 'tools-adapter' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'default'    => [
					'top'      => 6,
					'right'    => 6,
					'bottom'   => 6,
					'left'     => 6,
					'unit'     => 'px',
					'isLinked' => true,
				],
				'selectors'  => [
					'{{WRAPPER}} .ta-blog-card__badge' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		// =========================================================================
		// STYLE : TITRE & EXTRAIT
		// =========================================================================
		$this->start_controls_section(
			'section_style_content',
			[
				'label' => esc_html__( 'Titre & Extrait', 'tools-adapter' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'heading_title_style',
			[
				'label' => esc_html__( 'Titre de l\'article', 'tools-adapter' ),
				'type'  => Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			'title_color',
			[
				'label'     => esc_html__( 'Couleur du titre', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#0f172a',
				'selectors' => [
					'{{WRAPPER}} .ta-blog-card__title a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'title_color_hover',
			[
				'label'     => esc_html__( 'Couleur au survol', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#2e7d32',
				'selectors' => [
					'{{WRAPPER}} .ta-blog-card__title a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .ta-blog-card__title',
			]
		);

		$this->add_control(
			'heading_excerpt_style',
			[
				'label'     => esc_html__( 'Extrait de texte', 'tools-adapter' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'excerpt_color',
			[
				'label'     => esc_html__( 'Couleur de l\'extrait', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#64748b',
				'selectors' => [
					'{{WRAPPER}} .ta-blog-card__excerpt' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'excerpt_typography',
				'selector' => '{{WRAPPER}} .ta-blog-card__excerpt',
			]
		);

		$this->end_controls_section();

		// =========================================================================
		// STYLE : PIED DE CARTE (MÉTA GAUCHE & LIRE LA SUITE)
		// =========================================================================
		$this->start_controls_section(
			'section_style_footer',
			[
				'label' => esc_html__( 'Pied de carte (Footer)', 'tools-adapter' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'footer_border_color',
			[
				'label'     => esc_html__( 'Ligne de séparation', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#f1f5f9',
				'selectors' => [
					'{{WRAPPER}} .ta-blog-card__footer' => 'border-top-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'heading_submeta_style',
			[
				'label'     => esc_html__( 'Information gauche (Tag / Date)', 'tools-adapter' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'submeta_color',
			[
				'label'     => esc_html__( 'Couleur du texte gauche', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#64748b',
				'selectors' => [
					'{{WRAPPER}} .ta-blog-card__submeta' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'submeta_typography',
				'selector' => '{{WRAPPER}} .ta-blog-card__submeta',
			]
		);

		$this->add_control(
			'heading_read_more_style',
			[
				'label'     => esc_html__( 'Lien d\'action droit (Lire le conseil →)', 'tools-adapter' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'read_more_color',
			[
				'label'     => esc_html__( 'Couleur du lien', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#2e7d32',
				'selectors' => [
					'{{WRAPPER}} .ta-blog-card__more' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'read_more_hover_color',
			[
				'label'     => esc_html__( 'Couleur au survol', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#1b5e20',
				'selectors' => [
					'{{WRAPPER}} .ta-blog-card__more:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'read_more_typography',
				'selector' => '{{WRAPPER}} .ta-blog-card__more',
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Helper SVG pour les icônes thématiques des onglets.
	 *
	 * @param string $slug
	 * @param string $name
	 * @return string
	 */
	private function get_category_icon_svg( $slug, $name ) {
		$text = strtolower( $slug . ' ' . $name );

		if ( 'all' === $slug ) {
			// Grille 4 carrés (Tous les articles)
			return '<svg viewBox="0 0 24 24"><path d="M4 4h7v7H4V4zm9 0h7v7h-7V4zM4 13h7v7H4v-7zm9 0h7v7h-7v-7z"/></svg>';
		}

		if ( strpos( $text, 'végét' ) !== false || strpos( $text, 'soin' ) !== false || strpos( $text, 'plante' ) !== false || strpos( $text, 'jardin' ) !== false || strpos( $text, 'arbre' ) !== false ) {
			// Plante / Pousse
			return '<svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12c0 3.84 2.16 7.18 5.34 8.87-.07-.46-.11-.93-.11-1.42 0-3.53 1.83-6.64 4.62-8.43C12.56 8.35 14.15 6 17 6c.35 0 .68.04 1 .11C17.74 3.73 15.08 2 12 2zm7.74 6.78C18.89 8.28 17.98 8 17 8c-3.31 0-6 2.69-6 6 0 1.25.39 2.41 1.05 3.37.58-.23 1.22-.37 1.89-.37 2.76 0 5 2.24 5 5 0 .28-.03.56-.07.83 2.82-1.46 4.79-4.32 4.79-7.63 0-2.67-1.28-5.06-3.26-6.42z"/></svg>';
		}

		if ( strpos( $text, 'aménag' ) !== false || strpos( $text, 'matér' ) !== false || strpos( $text, 'travaux' ) !== false || strpos( $text, 'outil' ) !== false || strpos( $text, 'techni' ) !== false ) {
			// Outil / Marteau
			return '<svg viewBox="0 0 24 24"><path d="m22.7 19-9.1-9.1c.9-2.3.4-5-1.5-6.9-2-2-5-2.4-7.4-1.3L9 6 6 9 1.6 4.7C.4 7.1.9 10.1 2.9 12.1c1.9 1.9 4.6 2.4 6.9 1.5l9.1 9.1c.4.4 1 .4 1.4 0l2.3-2.3c.5-.4.5-1.1.1-1.4z"/></svg>';
		}

		if ( strpos( $text, 'mobilier' ) !== false || strpos( $text, 'déco' ) !== false || strpos( $text, 'meuble' ) !== false || strpos( $text, 'salon' ) !== false || strpos( $text, 'chaise' ) !== false ) {
			// Fauteuil / Décoration
			return '<svg viewBox="0 0 24 24"><path d="M20 10V7a3 3 0 0 0-3-3H7a3 3 0 0 0-3 3v3a4 4 0 0 0-2 3.5V19a1 1 0 0 0 1 1h2a1 1 0 0 0 1-1v-1h12v1a1 1 0 0 0 1 1h2a1 1 0 0 0 1-1v-5.5a4 4 0 0 0-2-3.5zM6 7a1 1 0 0 1 1-1h10a1 1 0 0 1 1 1v3H6V7z"/></svg>';
		}

		if ( strpos( $text, 'réalisation' ) !== false || strpos( $text, 'chantier' ) !== false || strpos( $text, 'projet' ) !== false ) {
			// Mallette / Réalisation
			return '<svg viewBox="0 0 24 24"><path d="M20 6h-4V4a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2zm-10-2h4v2h-4V4zm10 16H4V8h16v12z"/></svg>';
		}

		// Étiquette standard
		return '<svg viewBox="0 0 24 24"><path d="m21.41 11.58-9-9C12.05 2.22 11.55 2 11 2H4c-1.1 0-2 .9-2 2v7c0 .55.22 1.05.59 1.42l9 9c.36.36.86.58 1.41.58.55 0 1.05-.22 1.41-.59l7-7c.37-.36.59-.86.59-1.41 0-.55-.23-1.06-.59-1.42zM5.5 7C4.67 7 4 6.33 4 5.5S4.67 4 5.5 4 7 4.67 7 5.5 6.33 7 5.5 7z"/></svg>';
	}

	protected function render() {
		$settings   = $this->get_settings_for_display();
		$is_editor  = \Elementor\Plugin::$instance->editor->is_edit_mode();
		$paged      = $is_editor ? 1 : max( 1, (int) get_query_var( 'paged' ), (int) get_query_var( 'page' ) );
		$mode       = $settings['display_mode'] ?? 'tabs';

		$query_args = [
			'post_type'           => 'post',
			'post_status'         => 'publish',
			'posts_per_page'      => absint( $settings['posts_count'] ?? 9 ),
			'orderby'             => $settings['orderby'] ?? 'date',
			'order'               => $settings['order'] ?? 'DESC',
			'paged'               => $paged,
			'ignore_sticky_posts' => true,
		];

		if ( ! empty( $settings['categories'] ) ) {
			$query_args['category__in'] = array_map( 'absint', (array) $settings['categories'] );
		}

		$query = new \WP_Query( $query_args );

		if ( ! $query->have_posts() ) {
			if ( $is_editor ) {
				echo '<p>' . esc_html__( 'Aucun article trouvé pour les critères sélectionnés.', 'tools-adapter' ) . '</p>';
			}
			return;
		}

		$show_image          = 'yes' === ( $settings['show_image'] ?? 'yes' );
		$show_category_badge = 'yes' === ( $settings['show_category_badge'] ?? 'yes' );
		$title_tag           = ! empty( $settings['title_tag'] ) ? esc_attr( $settings['title_tag'] ) : 'h3';
		$show_excerpt        = 'yes' === ( $settings['show_excerpt'] ?? 'yes' );
		$excerpt_length      = absint( $settings['excerpt_length'] ?? 18 );
		$footer_left_meta    = $settings['footer_left_meta'] ?? 'tag';
		$show_read_more      = 'yes' === ( $settings['show_read_more'] ?? 'yes' );
		$read_more_text      = ! empty( $settings['read_more_text'] ) ? \tools_adapter_translate( $settings['read_more_text'] ) : esc_html__( 'Lire le conseil', 'tools-adapter' );

		// Préparation des catégories pour les onglets
		$all_categories = [];
		if ( 'tabs' === $mode ) {
			if ( ! empty( $settings['categories'] ) ) {
				$all_categories = get_categories( [
					'include'    => array_map( 'absint', (array) $settings['categories'] ),
					'hide_empty' => true,
				] );
			} else {
				$all_categories = get_categories( [
					'hide_empty' => true,
				] );
			}
		}

		$show_tab_icons = 'yes' === ( $settings['show_tab_icons'] ?? 'yes' );
		$show_tab_count = 'yes' === ( $settings['show_tab_count'] ?? 'yes' );
		?>
		<div class="ta-blog-archive" data-ta-blog-grid>
			<?php if ( 'tabs' === $mode && ! empty( $all_categories ) ) : ?>
				<div class="ta-blog-tabs" role="tablist">
					<?php if ( 'yes' === ( $settings['show_all_tab'] ?? 'yes' ) ) : ?>
						<button
							type="button"
							class="ta-blog-tab is-active"
							data-blog-filter="all"
							role="tab"
							aria-selected="true"
						>
							<?php if ( $show_tab_icons ) : ?>
								<span class="ta-blog-tab__icon" aria-hidden="true">
									<?php echo $this->get_category_icon_svg( 'all', '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								</span>
							<?php endif; ?>
							<span><?php echo esc_html( \tools_adapter_translate( $settings['all_tab_text'] ?: __( 'Tous les articles', 'tools-adapter' ) ) ); ?></span>
							<?php if ( $show_tab_count ) : ?>
								<span class="ta-blog-tab__count">(<?php echo esc_html( (string) $query->found_posts ); ?>)</span>
							<?php endif; ?>
						</button>
					<?php endif; ?>

					<?php foreach ( $all_categories as $cat_index => $cat ) : ?>
						<?php
						$is_first_active = ( 'no' === ( $settings['show_all_tab'] ?? 'yes' ) && 0 === $cat_index );
						?>
						<button
							type="button"
							class="ta-blog-tab<?php echo $is_first_active ? ' is-active' : ''; ?>"
							data-blog-filter="cat-<?php echo esc_attr( (string) $cat->term_id ); ?>"
							role="tab"
							aria-selected="<?php echo $is_first_active ? 'true' : 'false'; ?>"
						>
							<?php if ( $show_tab_icons ) : ?>
								<span class="ta-blog-tab__icon" aria-hidden="true">
									<?php echo $this->get_category_icon_svg( $cat->slug, $cat->name ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								</span>
							<?php endif; ?>
							<span><?php echo esc_html( $cat->name ); ?></span>
							<?php if ( $show_tab_count ) : ?>
								<span class="ta-blog-tab__count">(<?php echo esc_html( (string) $cat->count ); ?>)</span>
							<?php endif; ?>
						</button>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>

			<div class="ta-blog-grid">
				<?php
				while ( $query->have_posts() ) :
					$query->the_post();
					$post_id    = get_the_ID();
					$categories = get_the_category();
					$cat_classes = [];
					if ( ! empty( $categories ) ) {
						foreach ( $categories as $c ) {
							$cat_classes[] = 'cat-' . $c->term_id;
						}
					}
					$data_cats = implode( ' ', $cat_classes );

					// Détermination du badge principal
					$primary_category = ! empty( $categories ) ? $categories[0] : null;

					// Détermination du meta gauche de bas de carte
					$left_meta_label = '';
					if ( 'tag' === $footer_left_meta ) {
						$tags = get_the_tags();
						if ( ! empty( $tags ) ) {
							$left_meta_label = $tags[0]->name;
						} elseif ( count( $categories ) > 1 ) {
							$left_meta_label = $categories[1]->name;
						}
					} elseif ( 'category' === $footer_left_meta ) {
						if ( count( $categories ) > 1 ) {
							$left_meta_label = $categories[1]->name;
						} elseif ( $primary_category ) {
							$left_meta_label = $primary_category->name;
						}
					} elseif ( 'date' === $footer_left_meta ) {
						$left_meta_label = get_the_date();
					} elseif ( 'author' === $footer_left_meta ) {
						$left_meta_label = sprintf( __( 'Par %s', 'tools-adapter' ), get_the_author() );
					}
					?>
					<article
						class="ta-blog-card"
						data-blog-card
						data-categories="<?php echo esc_attr( $data_cats ); ?>"
					>
						<?php if ( $show_image && has_post_thumbnail() ) : ?>
							<a class="ta-blog-card__media-link" href="<?php the_permalink(); ?>" aria-label="<?php echo esc_attr( get_the_title() ); ?>">
								<div class="ta-blog-card__media">
									<div
										class="ta-blog-card__image"
										style="background-image: url('<?php echo esc_url( get_the_post_thumbnail_url( $post_id, 'large' ) ); ?>');"
									></div>
								</div>
								<?php if ( $show_category_badge && $primary_category ) : ?>
									<span class="ta-blog-card__badge">
										<?php echo esc_html( $primary_category->name ); ?>
									</span>
								<?php endif; ?>
							</a>
						<?php endif; ?>

						<div class="ta-blog-card__body">
							<div class="ta-blog-card__main">
								<<?php echo esc_attr( $title_tag ); ?> class="ta-blog-card__title">
									<a href="<?php the_permalink(); ?>"><?php echo esc_html( get_the_title() ); ?></a>
								</<?php echo esc_attr( $title_tag ); ?>>

								<?php if ( $show_excerpt ) : ?>
									<p class="ta-blog-card__excerpt">
										<?php echo esc_html( wp_trim_words( wp_strip_all_tags( get_the_excerpt() ), $excerpt_length ) ); ?>
									</p>
								<?php endif; ?>
							</div>

							<?php if ( ( 'none' !== $footer_left_meta && $left_meta_label ) || $show_read_more ) : ?>
								<div class="ta-blog-card__footer">
									<?php if ( 'none' !== $footer_left_meta && $left_meta_label ) : ?>
										<span class="ta-blog-card__submeta"><?php echo esc_html( $left_meta_label ); ?></span>
									<?php else : ?>
										<span></span>
									<?php endif; ?>

									<?php if ( $show_read_more && $read_more_text ) : ?>
										<a class="ta-blog-card__more" href="<?php the_permalink(); ?>">
											<span><?php echo esc_html( $read_more_text ); ?></span>
											<svg viewBox="0 0 24 24" width="14" height="14" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
												<line x1="5" y1="12" x2="19" y2="12"></line>
												<polyline points="12 5 19 12 12 19"></polyline>
											</svg>
										</a>
									<?php endif; ?>
								</div>
							<?php endif; ?>
						</div>
					</article>
				<?php endwhile; ?>

				<div class="ta-blog-grid__empty" data-blog-empty style="display: none;">
					<?php esc_html_e( 'Aucun article trouvé dans cette catégorie.', 'tools-adapter' ); ?>
				</div>
			</div>

			<?php
			if ( 'classic' === $mode && 'yes' === ( $settings['enable_pagination'] ?? '' ) && ! $is_editor && $query->max_num_pages > 1 ) {
				echo '<div class="ta-blog-grid__pagination">';
				echo wp_kses_post(
					paginate_links(
						[
							'total'     => $query->max_num_pages,
							'current'   => $paged,
							'prev_text' => esc_html__( '‹ Précédent', 'tools-adapter' ),
							'next_text' => esc_html__( 'Suivant ›', 'tools-adapter' ),
						]
					)
				);
				echo '</div>';
			}
			?>
		</div>
		<?php
		wp_reset_postdata();
	}
}
