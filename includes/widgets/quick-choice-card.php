<?php
namespace ToolsAdapter\Widgets;

use Elementor\Controls_Manager;
use Elementor\Icons_Manager;
use ToolsAdapter\Repeater;
use ToolsAdapter\Base_Widget;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Widget: Carte d'orientation — carte vitrée « De quoi avez-vous besoin ? »
 * avec liste de choix (icône, titre, sous-titre, flèche) et pied téléphone.
 */
class Quick_Choice_Card extends Base_Widget {

	public function get_name() {
		return 'tools-adapter-quick-choice';
	}

	public function get_title() {
		return esc_html__( 'Carte d\'orientation', 'tools-adapter' );
	}

	public function get_icon() {
		return 'eicon-bullet-list';
	}

	public function get_categories() {
		return [ 'tools-adapter' ];
	}

	public function get_keywords() {
		return [ 'choix', 'besoin', 'orientation', 'accès rapide', 'carte', 'vitrée', 'glass', 'téléphone', 'urgence' ];
	}

	public function get_style_depends() {
		return [ 'tools-adapter-quick-choice' ];
	}

	protected function register_controls() {
		// ── Contenu : en-tête ────────────────────────────────────────────
		$this->start_controls_section( 'section_header', [ 'label' => esc_html__( 'En-tête', 'tools-adapter' ) ] );

		$this->add_control( 'title', [ 'label' => esc_html__( 'Titre', 'tools-adapter' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'De quoi avez-vous besoin ?', 'tools-adapter' ), 'label_block' => true ] );
		$this->add_control( 'title_tag', [ 'label' => esc_html__( 'Balise du titre', 'tools-adapter' ), 'type' => Controls_Manager::SELECT, 'default' => 'h3', 'options' => [ 'h2' => 'H2', 'h3' => 'H3', 'h4' => 'H4', 'div' => 'div' ] ] );
		$this->add_control( 'description', [ 'label' => esc_html__( 'Sous-titre', 'tools-adapter' ), 'type' => Controls_Manager::TEXTAREA, 'rows' => 2, 'default' => esc_html__( 'Choisissez votre besoin, on s\'occupe du reste.', 'tools-adapter' ) ] );

		$this->end_controls_section();

		// ── Contenu : choix ──────────────────────────────────────────────
		$this->start_controls_section( 'section_items', [ 'label' => esc_html__( 'Choix', 'tools-adapter' ) ] );

		$repeater = new Repeater();
		$repeater->start_controls_tabs( 'item_edit_tabs' );
		$repeater->start_controls_tab( 'item_edit_content', [ 'label' => esc_html__( 'Contenu', 'tools-adapter' ) ] );
		$repeater->add_control( 'icon', [ 'label' => esc_html__( 'Icône', 'tools-adapter' ), 'type' => Controls_Manager::ICONS, 'default' => [ 'value' => 'fas fa-check', 'library' => 'fa-solid' ] ] );
		$repeater->add_control( 'title', [ 'label' => esc_html__( 'Titre', 'tools-adapter' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'Prestation', 'tools-adapter' ), 'label_block' => true ] );
		$repeater->add_control( 'subtitle', [ 'label' => esc_html__( 'Sous-titre', 'tools-adapter' ), 'type' => Controls_Manager::TEXT, 'label_block' => true ] );
		$repeater->add_control( 'badge', [ 'label' => esc_html__( 'Badge (optionnel)', 'tools-adapter' ), 'type' => Controls_Manager::TEXT, 'description' => esc_html__( 'Ex. « Urgence 24h » — affiché avant la flèche.', 'tools-adapter' ) ] );
		$repeater->add_control( 'link', [ 'label' => esc_html__( 'Lien (ancre ou URL)', 'tools-adapter' ), 'type' => Controls_Manager::URL, 'default' => [ 'url' => '#' ] ] );
		$repeater->end_controls_tab();
		$repeater->start_controls_tab( 'item_edit_style', [ 'label' => esc_html__( 'Style', 'tools-adapter' ) ] );
		$repeater->add_control( 'item_style_note', [ 'type' => Controls_Manager::RAW_HTML, 'raw' => esc_html__( 'Laissez vide pour utiliser le style global (onglet Style du widget).', 'tools-adapter' ), 'content_classes' => 'elementor-descriptor' ] );
		$repeater->add_group_control( Group_Control_Background::get_type(), [ 'name' => 'item_icon_bg', 'label' => esc_html__( 'Fond de la pastille', 'tools-adapter' ), 'types' => [ 'classic', 'gradient' ], 'exclude' => [ 'image' ], 'selector' => '{{WRAPPER}} {{CURRENT_ITEM}} .ta-qc__icon' ] );
		$repeater->add_control( 'item_icon_color', [ 'label' => esc_html__( 'Couleur de l\'icône', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} {{CURRENT_ITEM}} .ta-qc__icon' => 'color: {{VALUE}};', '{{WRAPPER}} {{CURRENT_ITEM}} .ta-qc__icon svg' => 'fill: {{VALUE}};' ] ] );
		$repeater->add_control( 'item_accent', [ 'label' => esc_html__( 'Couleur d\'accent (flèche, badge, bordure au survol)', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} {{CURRENT_ITEM}} .ta-qc__arrow, {{WRAPPER}} {{CURRENT_ITEM}} .ta-qc__badge' => 'color: {{VALUE}};', '{{WRAPPER}} {{CURRENT_ITEM}} .ta-qc__badge' => 'border-color: {{VALUE}};', '{{WRAPPER}} {{CURRENT_ITEM}}.ta-qc__item:hover, {{WRAPPER}} {{CURRENT_ITEM}}.ta-qc__item:focus-visible' => 'border-color: {{VALUE}};' ] ] );
		$repeater->add_control( 'item_bg', [ 'label' => esc_html__( 'Fond du choix', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} {{CURRENT_ITEM}}.ta-qc__item' => 'background-color: {{VALUE}};' ] ] );
		$repeater->end_controls_tab();
		$repeater->end_controls_tabs();

		$this->add_control(
			'items',
			[
				'label'       => esc_html__( 'Choix', 'tools-adapter' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ title }}}',
				'default'     => [
					[
						'icon'     => [ 'value' => 'fas fa-tint', 'library' => 'fa-solid' ],
						'title'    => esc_html__( 'Vidange & débouchage', 'tools-adapter' ),
						'subtitle' => esc_html__( 'Fosses, bacs à graisse, hydrocurage', 'tools-adapter' ),
						'link'     => [ 'url' => '#assainissement' ],
					],
					[
						'icon'     => [ 'value' => 'fas fa-hard-hat', 'library' => 'fa-solid' ],
						'title'    => esc_html__( 'Terrassement', 'tools-adapter' ),
						'subtitle' => esc_html__( 'Excavation, cours, enrochement', 'tools-adapter' ),
						'link'     => [ 'url' => '#terrassement' ],
					],
					[
						'icon'     => [ 'value' => 'fas fa-tree', 'library' => 'fa-solid' ],
						'title'    => esc_html__( 'Bois de chauffage', 'tools-adapter' ),
						'subtitle' => esc_html__( 'Hêtre & mélange, livré chez vous', 'tools-adapter' ),
						'link'     => [ 'url' => '#bois' ],
					],
				],
			]
		);

		$this->add_control( 'show_arrow', [ 'label' => esc_html__( 'Icône à droite', 'tools-adapter' ), 'type' => Controls_Manager::SWITCHER, 'return_value' => 'yes', 'default' => 'yes' ] );
		$this->add_control( 'arrow_icon', [ 'label' => esc_html__( 'Icône à droite', 'tools-adapter' ), 'type' => Controls_Manager::ICONS, 'description' => esc_html__( 'Laissez vide pour la flèche par défaut.', 'tools-adapter' ), 'condition' => [ 'show_arrow' => 'yes' ] ] );

		$this->end_controls_section();

		// ── Contenu : pied téléphone ─────────────────────────────────────
		$this->start_controls_section( 'section_footer', [ 'label' => esc_html__( 'Pied de carte (téléphone)', 'tools-adapter' ) ] );

		$this->add_control( 'show_footer', [ 'label' => esc_html__( 'Afficher', 'tools-adapter' ), 'type' => Controls_Manager::SWITCHER, 'return_value' => 'yes', 'default' => 'yes' ] );
		$this->add_control( 'footer_icon', [ 'label' => esc_html__( 'Icône', 'tools-adapter' ), 'type' => Controls_Manager::ICONS, 'default' => [ 'value' => 'fas fa-headset', 'library' => 'fa-solid' ], 'condition' => [ 'show_footer' => 'yes' ] ] );
		$this->add_control( 'footer_label', [ 'label' => esc_html__( 'Libellé', 'tools-adapter' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'Une urgence ? Appelez-nous', 'tools-adapter' ), 'condition' => [ 'show_footer' => 'yes' ] ] );
		$this->add_control( 'phone_number', [ 'label' => esc_html__( 'Numéro affiché', 'tools-adapter' ), 'type' => Controls_Manager::TEXT, 'default' => '03 88 63 70 71', 'condition' => [ 'show_footer' => 'yes' ] ] );
		$this->add_control( 'phone_url', [ 'label' => esc_html__( 'Lien (optionnel)', 'tools-adapter' ), 'type' => Controls_Manager::TEXT, 'placeholder' => 'tel:0388637071', 'description' => esc_html__( 'Laissez vide pour générer automatiquement « tel: » à partir du numéro.', 'tools-adapter' ), 'condition' => [ 'show_footer' => 'yes' ] ] );

		$this->end_controls_section();

		// ── Style : carte ────────────────────────────────────────────────
		$this->start_controls_section( 'section_style_card', [ 'label' => esc_html__( 'Carte', 'tools-adapter' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_responsive_control( 'card_max_width', [ 'label' => esc_html__( 'Largeur max.', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'size_units' => [ 'px', '%' ], 'range' => [ 'px' => [ 'min' => 280, 'max' => 900 ] ], 'default' => [ 'size' => 430, 'unit' => 'px' ], 'selectors' => [ '{{WRAPPER}} .ta-qc' => 'max-width: {{SIZE}}{{UNIT}};' ] ] );
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'           => 'card_background',
				'types'          => [ 'classic', 'gradient' ],
				'selector'       => '{{WRAPPER}} .ta-qc',
				'fields_options' => [
					'background' => [ 'default' => 'classic' ],
					'color'      => [ 'default' => 'rgba(255,255,255,0.12)' ],
				],
			]
		);
		$this->add_control( 'card_blur', [ 'label' => esc_html__( 'Flou d\'arrière-plan (verre)', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'range' => [ 'px' => [ 'min' => 0, 'max' => 40 ] ], 'default' => [ 'size' => 16, 'unit' => 'px' ], 'selectors' => [ '{{WRAPPER}} .ta-qc' => '-webkit-backdrop-filter: blur({{SIZE}}px); backdrop-filter: blur({{SIZE}}px);' ] ] );
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'           => 'card_border',
				'selector'       => '{{WRAPPER}} .ta-qc',
				'fields_options' => [
					'border' => [ 'default' => 'solid' ],
					'width'  => [ 'default' => [ 'top' => 1, 'right' => 1, 'bottom' => 1, 'left' => 1, 'unit' => 'px', 'isLinked' => true ] ],
					'color'  => [ 'default' => 'rgba(255,255,255,0.28)' ],
				],
			]
		);
		$this->add_responsive_control( 'card_radius', [ 'label' => esc_html__( 'Arrondi', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'range' => [ 'px' => [ 'min' => 0, 'max' => 48 ] ], 'default' => [ 'size' => 24, 'unit' => 'px' ], 'selectors' => [ '{{WRAPPER}} .ta-qc' => 'border-radius: {{SIZE}}{{UNIT}};' ] ] );
		$this->add_responsive_control( 'card_padding', [ 'label' => esc_html__( 'Espacement interne', 'tools-adapter' ), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => [ 'px', 'em' ], 'default' => [ 'top' => '32', 'right' => '32', 'bottom' => '32', 'left' => '32', 'unit' => 'px', 'isLinked' => true ], 'selectors' => [ '{{WRAPPER}} .ta-qc' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ] ] );
		$this->add_group_control( Group_Control_Box_Shadow::get_type(), [ 'name' => 'card_shadow', 'selector' => '{{WRAPPER}} .ta-qc' ] );

		$this->add_control( 'heading_header_style', [ 'label' => esc_html__( 'En-tête', 'tools-adapter' ), 'type' => Controls_Manager::HEADING, 'separator' => 'before' ] );
		$this->add_responsive_control(
			'header_align',
			[
				'label'     => esc_html__( 'Alignement', 'tools-adapter' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'left'   => [ 'title' => esc_html__( 'Gauche', 'tools-adapter' ), 'icon' => 'eicon-text-align-left' ],
					'center' => [ 'title' => esc_html__( 'Centre', 'tools-adapter' ), 'icon' => 'eicon-text-align-center' ],
					'right'  => [ 'title' => esc_html__( 'Droite', 'tools-adapter' ), 'icon' => 'eicon-text-align-right' ],
				],
				'selectors' => [ '{{WRAPPER}} .ta-qc__title, {{WRAPPER}} .ta-qc__desc' => 'text-align: {{VALUE}};' ],
			]
		);
		$this->add_control( 'title_color', [ 'label' => esc_html__( 'Couleur du titre', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#ffffff', 'selectors' => [ '{{WRAPPER}} .ta-qc__title' => 'color: {{VALUE}};' ] ] );
		$this->add_group_control( Group_Control_Typography::get_type(), [ 'name' => 'title_typography', 'selector' => '{{WRAPPER}} .ta-qc__title' ] );
		$this->add_responsive_control( 'title_spacing', [ 'label' => esc_html__( 'Espace sous le titre', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'range' => [ 'px' => [ 'min' => 0, 'max' => 60 ] ], 'selectors' => [ '{{WRAPPER}} .ta-qc__title' => 'margin-bottom: {{SIZE}}{{UNIT}};' ] ] );
		$this->add_control( 'desc_color', [ 'label' => esc_html__( 'Couleur du sous-titre', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => 'rgba(255,255,255,0.8)', 'selectors' => [ '{{WRAPPER}} .ta-qc__desc' => 'color: {{VALUE}};' ] ] );
		$this->add_group_control( Group_Control_Typography::get_type(), [ 'name' => 'desc_typography', 'selector' => '{{WRAPPER}} .ta-qc__desc' ] );
		$this->add_responsive_control( 'desc_spacing', [ 'label' => esc_html__( 'Espace avant la liste', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'range' => [ 'px' => [ 'min' => 0, 'max' => 60 ] ], 'selectors' => [ '{{WRAPPER}} .ta-qc__desc' => 'margin-bottom: {{SIZE}}{{UNIT}};' ] ] );

		$this->end_controls_section();

		// ── Style : choix ────────────────────────────────────────────────
		$this->start_controls_section( 'section_style_items', [ 'label' => esc_html__( 'Choix', 'tools-adapter' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_responsive_control( 'items_gap', [ 'label' => esc_html__( 'Espacement entre choix', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'range' => [ 'px' => [ 'min' => 0, 'max' => 40 ] ], 'default' => [ 'size' => 12, 'unit' => 'px' ], 'selectors' => [ '{{WRAPPER}} .ta-qc__items' => 'gap: {{SIZE}}{{UNIT}};' ] ] );
		$this->add_responsive_control( 'item_padding', [ 'label' => esc_html__( 'Espacement interne', 'tools-adapter' ), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => [ 'px' ], 'default' => [ 'top' => '16', 'right' => '18', 'bottom' => '16', 'left' => '16', 'unit' => 'px', 'isLinked' => false ], 'selectors' => [ '{{WRAPPER}} .ta-qc__item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ] ] );
		$this->add_control( 'item_border_width', [ 'label' => esc_html__( 'Épaisseur de bordure', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'range' => [ 'px' => [ 'min' => 0, 'max' => 6 ] ], 'selectors' => [ '{{WRAPPER}} .ta-qc__item' => 'border-width: {{SIZE}}{{UNIT}};' ] ] );
		$this->add_control( 'item_inner_gap', [ 'label' => esc_html__( 'Espace icône / texte', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'range' => [ 'px' => [ 'min' => 0, 'max' => 40 ] ], 'selectors' => [ '{{WRAPPER}} .ta-qc__item' => 'gap: {{SIZE}}{{UNIT}};' ] ] );
		$this->add_control( 'item_radius', [ 'label' => esc_html__( 'Arrondi', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'range' => [ 'px' => [ 'min' => 0, 'max' => 30 ] ], 'default' => [ 'size' => 14, 'unit' => 'px' ], 'selectors' => [ '{{WRAPPER}} .ta-qc__item' => 'border-radius: {{SIZE}}{{UNIT}};' ] ] );

		$this->start_controls_tabs( 'item_tabs' );
		$this->start_controls_tab( 'item_tab_normal', [ 'label' => esc_html__( 'Normal', 'tools-adapter' ) ] );
		$this->add_control( 'item_bg', [ 'label' => esc_html__( 'Fond', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => 'rgba(255,255,255,0.08)', 'selectors' => [ '{{WRAPPER}} .ta-qc__item' => 'background-color: {{VALUE}};' ] ] );
		$this->add_control( 'item_border', [ 'label' => esc_html__( 'Bordure', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => 'rgba(255,255,255,0.18)', 'selectors' => [ '{{WRAPPER}} .ta-qc__item' => 'border-color: {{VALUE}};' ] ] );
		$this->end_controls_tab();
		$this->start_controls_tab( 'item_tab_hover', [ 'label' => esc_html__( 'Survol', 'tools-adapter' ) ] );
		$this->add_control( 'item_bg_hover', [ 'label' => esc_html__( 'Fond', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => 'rgba(255,255,255,0.16)', 'selectors' => [ '{{WRAPPER}} .ta-qc__item:hover, {{WRAPPER}} .ta-qc__item:focus-visible' => 'background-color: {{VALUE}};' ] ] );
		$this->add_control( 'item_shift_hover', [ 'label' => esc_html__( 'Décalage au survol (px)', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'range' => [ 'px' => [ 'min' => 0, 'max' => 16 ] ], 'default' => [ 'size' => 3, 'unit' => 'px' ], 'selectors' => [ '{{WRAPPER}} .ta-qc__item:hover, {{WRAPPER}} .ta-qc__item:focus-visible' => 'transform: translateX({{SIZE}}px);' ] ] );
		$this->add_control( 'item_title_color_hover', [ 'label' => esc_html__( 'Couleur du titre', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .ta-qc__item:hover .ta-qc__item-title' => 'color: {{VALUE}};' ] ] );
		$this->add_control( 'item_border_hover', [ 'label' => esc_html__( 'Bordure', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#FFC107', 'selectors' => [ '{{WRAPPER}} .ta-qc__item:hover, {{WRAPPER}} .ta-qc__item:focus-visible' => 'border-color: {{VALUE}};' ] ] );
		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_control( 'heading_icon_style', [ 'label' => esc_html__( 'Icône', 'tools-adapter' ), 'type' => Controls_Manager::HEADING, 'separator' => 'before' ] );
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'           => 'icon_bg',
				'types'          => [ 'classic', 'gradient' ],
				'exclude'        => [ 'image' ],
				'selector'       => '{{WRAPPER}} .ta-qc__icon',
				'fields_options' => [
					'background'     => [ 'default' => 'gradient' ],
					'color'          => [ 'default' => '#FFC107' ],
					'color_b'        => [ 'default' => '#FF9800' ],
					'gradient_angle' => [ 'default' => [ 'unit' => 'deg', 'size' => 160 ] ],
				],
			]
		);
		$this->add_control( 'icon_color', [ 'label' => esc_html__( 'Couleur de l\'icône', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#1a1a1a', 'selectors' => [ '{{WRAPPER}} .ta-qc__icon' => 'color: {{VALUE}};', '{{WRAPPER}} .ta-qc__icon svg' => 'fill: {{VALUE}};' ] ] );
		$this->add_control( 'icon_box_size', [ 'label' => esc_html__( 'Taille de la pastille', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'range' => [ 'px' => [ 'min' => 28, 'max' => 80 ] ], 'default' => [ 'size' => 46, 'unit' => 'px' ], 'selectors' => [ '{{WRAPPER}} .ta-qc__icon' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};' ] ] );
		$this->add_control( 'icon_size', [ 'label' => esc_html__( 'Taille de l\'icône', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'range' => [ 'px' => [ 'min' => 10, 'max' => 40 ] ], 'default' => [ 'size' => 18, 'unit' => 'px' ], 'selectors' => [ '{{WRAPPER}} .ta-qc__icon' => 'font-size: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .ta-qc__icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};' ] ] );
		$this->add_control( 'icon_radius', [ 'label' => esc_html__( 'Arrondi de la pastille', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'size_units' => [ 'px', '%' ], 'range' => [ 'px' => [ 'min' => 0, 'max' => 40 ] ], 'default' => [ 'size' => 12, 'unit' => 'px' ], 'selectors' => [ '{{WRAPPER}} .ta-qc__icon' => 'border-radius: {{SIZE}}{{UNIT}};' ] ] );

		$this->add_control( 'heading_item_text_style', [ 'label' => esc_html__( 'Textes', 'tools-adapter' ), 'type' => Controls_Manager::HEADING, 'separator' => 'before' ] );
		$this->add_control( 'item_title_color', [ 'label' => esc_html__( 'Couleur du titre', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#ffffff', 'selectors' => [ '{{WRAPPER}} .ta-qc__item-title' => 'color: {{VALUE}};' ] ] );
		$this->add_group_control( Group_Control_Typography::get_type(), [ 'name' => 'item_title_typography', 'selector' => '{{WRAPPER}} .ta-qc__item-title' ] );
		$this->add_control( 'item_sub_color', [ 'label' => esc_html__( 'Couleur du sous-titre', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => 'rgba(255,255,255,0.72)', 'selectors' => [ '{{WRAPPER}} .ta-qc__item-sub' => 'color: {{VALUE}};' ] ] );
		$this->add_group_control( Group_Control_Typography::get_type(), [ 'name' => 'item_sub_typography', 'selector' => '{{WRAPPER}} .ta-qc__item-sub' ] );

		$this->add_control( 'heading_badge_style', [ 'label' => esc_html__( 'Badge', 'tools-adapter' ), 'type' => Controls_Manager::HEADING, 'separator' => 'before' ] );
		$this->add_control( 'badge_color', [ 'label' => esc_html__( 'Texte & bordure', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#FFC107', 'selectors' => [ '{{WRAPPER}} .ta-qc__badge' => 'color: {{VALUE}}; border-color: {{VALUE}};' ] ] );
		$this->add_control( 'badge_bg', [ 'label' => esc_html__( 'Fond', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .ta-qc__badge' => 'background-color: {{VALUE}};' ] ] );
		$this->add_group_control( Group_Control_Typography::get_type(), [ 'name' => 'badge_typography', 'selector' => '{{WRAPPER}} .ta-qc__badge' ] );
		$this->add_responsive_control( 'badge_padding', [ 'label' => esc_html__( 'Espacement interne', 'tools-adapter' ), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => [ 'px', 'em' ], 'selectors' => [ '{{WRAPPER}} .ta-qc__badge' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ] ] );
		$this->add_control( 'badge_radius', [ 'label' => esc_html__( 'Arrondi', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'size_units' => [ 'px', '%' ], 'range' => [ 'px' => [ 'min' => 0, 'max' => 40 ] ], 'selectors' => [ '{{WRAPPER}} .ta-qc__badge' => 'border-radius: {{SIZE}}{{UNIT}};' ] ] );

		$this->add_control( 'heading_arrow_style', [ 'label' => esc_html__( 'Icône à droite', 'tools-adapter' ), 'type' => Controls_Manager::HEADING, 'separator' => 'before' ] );
		$this->add_control( 'arrow_color', [ 'label' => esc_html__( 'Couleur', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#FFC107', 'selectors' => [ '{{WRAPPER}} .ta-qc__arrow' => 'color: {{VALUE}};' ] ] );
		$this->add_control( 'arrow_size', [ 'label' => esc_html__( 'Taille', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'range' => [ 'px' => [ 'min' => 8, 'max' => 40 ] ], 'selectors' => [ '{{WRAPPER}} .ta-qc__arrow' => 'font-size: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .ta-qc__arrow svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};' ] ] );
		$this->add_control( 'arrow_shift', [ 'label' => esc_html__( 'Décalage au survol (px)', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'range' => [ 'px' => [ 'min' => 0, 'max' => 16 ] ], 'default' => [ 'size' => 3, 'unit' => 'px' ], 'selectors' => [ '{{WRAPPER}} .ta-qc__item:hover .ta-qc__arrow' => 'transform: translateX({{SIZE}}px);' ] ] );

		$this->end_controls_section();

		// ── Style : pied ─────────────────────────────────────────────────
		$this->start_controls_section( 'section_style_footer', [ 'label' => esc_html__( 'Pied de carte', 'tools-adapter' ), 'tab' => Controls_Manager::TAB_STYLE, 'condition' => [ 'show_footer' => 'yes' ] ] );

		$this->add_responsive_control( 'footer_spacing', [ 'label' => esc_html__( 'Espace avant le pied', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'range' => [ 'px' => [ 'min' => 0, 'max' => 60 ] ], 'selectors' => [ '{{WRAPPER}} .ta-qc__footer' => 'margin-top: {{SIZE}}{{UNIT}};' ] ] );
		$this->add_responsive_control( 'footer_padding', [ 'label' => esc_html__( 'Espace sous le séparateur', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'range' => [ 'px' => [ 'min' => 0, 'max' => 60 ] ], 'selectors' => [ '{{WRAPPER}} .ta-qc__footer' => 'padding-top: {{SIZE}}{{UNIT}};' ] ] );
		$this->add_control( 'separator_width', [ 'label' => esc_html__( 'Épaisseur du séparateur', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'range' => [ 'px' => [ 'min' => 0, 'max' => 6 ] ], 'selectors' => [ '{{WRAPPER}} .ta-qc__footer' => 'border-top-width: {{SIZE}}{{UNIT}};' ] ] );
		$this->add_control( 'separator_color', [ 'label' => esc_html__( 'Couleur du séparateur', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => 'rgba(255,255,255,0.22)', 'selectors' => [ '{{WRAPPER}} .ta-qc__footer' => 'border-top-color: {{VALUE}};' ] ] );
		$this->add_control( 'footer_icon_color', [ 'label' => esc_html__( 'Couleur de l\'icône', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#FFC107', 'selectors' => [ '{{WRAPPER}} .ta-qc__footer-icon' => 'color: {{VALUE}};', '{{WRAPPER}} .ta-qc__footer-icon svg' => 'fill: {{VALUE}};' ] ] );
		$this->add_control( 'footer_icon_size', [ 'label' => esc_html__( 'Taille de l\'icône', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'range' => [ 'px' => [ 'min' => 10, 'max' => 60 ] ], 'selectors' => [ '{{WRAPPER}} .ta-qc__footer-icon' => 'font-size: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .ta-qc__footer-icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};' ] ] );
		$this->add_control( 'footer_label_color', [ 'label' => esc_html__( 'Couleur du libellé', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => 'rgba(255,255,255,0.75)', 'selectors' => [ '{{WRAPPER}} .ta-qc__footer-label' => 'color: {{VALUE}};' ] ] );
		$this->add_group_control( Group_Control_Typography::get_type(), [ 'name' => 'footer_label_typography', 'selector' => '{{WRAPPER}} .ta-qc__footer-label' ] );
		$this->add_control( 'phone_color', [ 'label' => esc_html__( 'Couleur du numéro', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#ffffff', 'selectors' => [ '{{WRAPPER}} .ta-qc__phone' => 'color: {{VALUE}};' ] ] );
		$this->add_control( 'phone_color_hover', [ 'label' => esc_html__( 'Couleur du numéro (survol)', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#FFC107', 'selectors' => [ '{{WRAPPER}} .ta-qc__phone:hover' => 'color: {{VALUE}};' ] ] );
		$this->add_group_control( Group_Control_Typography::get_type(), [ 'name' => 'phone_typography', 'selector' => '{{WRAPPER}} .ta-qc__phone' ] );

		$this->end_controls_section();
	}

	protected function render() {
		$settings  = $this->get_settings_for_display();
		$title_tag = in_array( $settings['title_tag'] ?? 'h3', [ 'h2', 'h3', 'h4', 'div' ], true ) ? $settings['title_tag'] : 'h3';
		$items     = is_array( $settings['items'] ?? null ) ? $settings['items'] : [];
		$arrow     = 'yes' === ( $settings['show_arrow'] ?? '' );
		$phone     = trim( (string) ( $settings['phone_number'] ?? '' ) );
		$phone_url = ! empty( $settings['phone_url'] ) ? $settings['phone_url'] : 'tel:' . preg_replace( '/[^0-9+]/', '', $phone );
		?>
		<div class="ta-qc">
			<?php if ( ! empty( $settings['title'] ) ) : ?>
				<<?php echo esc_attr( $title_tag ); ?> class="ta-qc__title"><?php echo esc_html( \tools_adapter_translate( $settings['title'] ) ); ?></<?php echo esc_attr( $title_tag ); ?>>
			<?php endif; ?>
			<?php if ( ! empty( $settings['description'] ) ) : ?>
				<p class="ta-qc__desc"><?php echo esc_html( \tools_adapter_translate( $settings['description'] ) ); ?></p>
			<?php endif; ?>

			<?php if ( $items ) : ?>
				<ul class="ta-qc__items">
					<?php
					foreach ( $items as $item ) :
						$url    = ! empty( $item['link']['url'] ) ? $item['link']['url'] : '#';
						$target = ! empty( $item['link']['is_external'] ) ? ' target="_blank"' : '';
						$rel    = ! empty( $item['link']['nofollow'] ) ? ' rel="nofollow noopener"' : ( $target ? ' rel="noopener"' : '' );
						?>
						<li>
							<a class="ta-qc__item elementor-repeater-item-<?php echo esc_attr( $item['_id'] ?? '' ); ?>" href="<?php echo esc_url( $url ); ?>"<?php echo $target . $rel; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
								<?php if ( ! empty( $item['icon']['value'] ) ) : ?>
									<span class="ta-qc__icon" aria-hidden="true"><?php Icons_Manager::render_icon( $item['icon'], [ 'aria-hidden' => 'true' ] ); ?></span>
								<?php endif; ?>
								<span class="ta-qc__text">
									<span class="ta-qc__item-title"><?php echo esc_html( \tools_adapter_translate( $item['title'] ?? '' ) ); ?></span>
									<?php if ( ! empty( $item['subtitle'] ) ) : ?>
										<span class="ta-qc__item-sub"><?php echo esc_html( \tools_adapter_translate( $item['subtitle'] ) ); ?></span>
									<?php endif; ?>
								</span>
								<?php if ( ! empty( $item['badge'] ) ) : ?>
									<span class="ta-qc__badge"><?php echo esc_html( \tools_adapter_translate( $item['badge'] ) ); ?></span>
								<?php endif; ?>
								<?php if ( $arrow ) : ?>
									<span class="ta-qc__arrow" aria-hidden="true">
										<?php if ( ! empty( $settings['arrow_icon']['value'] ) ) : ?>
											<?php Icons_Manager::render_icon( $settings['arrow_icon'], [ 'aria-hidden' => 'true' ] ); ?>
										<?php else : ?>
											<svg viewBox="0 0 24 24" width="20" height="20" focusable="false"><path d="M5 12h14M13 6l6 6-6 6" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg>
										<?php endif; ?>
									</span>
								<?php endif; ?>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>

			<?php if ( 'yes' === ( $settings['show_footer'] ?? '' ) && '' !== $phone ) : ?>
				<div class="ta-qc__footer">
					<?php if ( ! empty( $settings['footer_icon']['value'] ) ) : ?>
						<span class="ta-qc__footer-icon" aria-hidden="true"><?php Icons_Manager::render_icon( $settings['footer_icon'], [ 'aria-hidden' => 'true' ] ); ?></span>
					<?php endif; ?>
					<div class="ta-qc__footer-text">
						<?php if ( ! empty( $settings['footer_label'] ) ) : ?>
							<span class="ta-qc__footer-label"><?php echo esc_html( \tools_adapter_translate( $settings['footer_label'] ) ); ?></span>
						<?php endif; ?>
						<a class="ta-qc__phone" href="<?php echo esc_url( $phone_url, [ 'tel', 'http', 'https', 'mailto' ] ); ?>"><?php echo esc_html( $phone ); ?></a>
					</div>
				</div>
			<?php endif; ?>
		</div>
		<?php
	}
}
