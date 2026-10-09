<?php
namespace ToolsAdapter\Widgets;

use Elementor\Controls_Manager;
use Elementor\Icons_Manager;
use ToolsAdapter\Repeater;
use Elementor\Utils;
use ToolsAdapter\Base_Widget;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Widget: Cartes services Bento — cartes image plein fond avec dégradé,
 * badge, titre, description, pastilles et lien, largeur libre sur 12 colonnes.
 */
class Service_Cards extends Base_Widget {

	/**
	 * Allowed column spans (12-column grid).
	 */
	const SPANS = [ '3', '4', '5', '6', '7', '8', '9', '12' ];

	public function get_name() {
		return 'tools-adapter-service-cards';
	}

	public function get_title() {
		return esc_html__( 'Cartes services Bento', 'tools-adapter' );
	}

	public function get_icon() {
		return 'eicon-gallery-grid';
	}

	public function get_categories() {
		return [ 'tools-adapter' ];
	}

	public function get_keywords() {
		return [ 'services', 'cartes', 'bento', 'pôle', 'prestations', 'image', 'badge' ];
	}

	public function get_style_depends() {
		return [ 'tools-adapter-service-cards' ];
	}

	/**
	 * Span options for the width selects.
	 *
	 * @param string $auto_label Label of the empty "automatic" option, or '' for none.
	 * @return array<string,string>
	 */
	private function span_options( $auto_label = '' ) {
		$options = $auto_label ? [ '' => $auto_label ] : [];
		return $options + [
			'3'  => '3 — ' . esc_html__( 'un quart', 'tools-adapter' ),
			'4'  => '4 — ' . esc_html__( 'un tiers', 'tools-adapter' ),
			'5'  => '5',
			'6'  => '6 — ' . esc_html__( 'moitié', 'tools-adapter' ),
			'7'  => '7',
			'8'  => '8 — ' . esc_html__( 'deux tiers', 'tools-adapter' ),
			'9'  => '9 — ' . esc_html__( 'trois quarts', 'tools-adapter' ),
			'12' => '12 — ' . esc_html__( 'pleine largeur', 'tools-adapter' ),
		];
	}

	protected function register_controls() {
		$this->start_controls_section( 'section_cards', [ 'label' => esc_html__( 'Cartes', 'tools-adapter' ) ] );

		$repeater = new Repeater();

		$repeater->start_controls_tabs( 'card_tabs' );

		// Onglet Contenu.
		$repeater->start_controls_tab( 'card_tab_content', [ 'label' => esc_html__( 'Contenu', 'tools-adapter' ) ] );
		$repeater->add_control( 'image', [ 'label' => esc_html__( 'Image de fond', 'tools-adapter' ), 'type' => Controls_Manager::MEDIA, 'default' => [ 'url' => Utils::get_placeholder_image_src() ] ] );
		$repeater->add_control( 'badge', [ 'label' => esc_html__( 'Badge', 'tools-adapter' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'Pôle', 'tools-adapter' ) ] );
		$repeater->add_control( 'title', [ 'label' => esc_html__( 'Titre', 'tools-adapter' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'Titre du service', 'tools-adapter' ), 'label_block' => true ] );
		$repeater->add_control( 'description', [ 'label' => esc_html__( 'Description', 'tools-adapter' ), 'type' => Controls_Manager::TEXTAREA, 'rows' => 3 ] );
		$repeater->add_control( 'tags', [ 'label' => esc_html__( 'Pastilles (une par ligne)', 'tools-adapter' ), 'type' => Controls_Manager::TEXTAREA, 'rows' => 4 ] );
		$repeater->add_control( 'link_text', [ 'label' => esc_html__( 'Texte du lien', 'tools-adapter' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'En savoir plus', 'tools-adapter' ), 'separator' => 'before' ] );
		$repeater->add_control( 'link', [ 'label' => esc_html__( 'Lien', 'tools-adapter' ), 'type' => Controls_Manager::URL, 'default' => [ 'url' => '#' ] ] );
		$repeater->end_controls_tab();

		// Onglet Disposition.
		$repeater->start_controls_tab( 'card_tab_layout', [ 'label' => esc_html__( 'Largeur', 'tools-adapter' ) ] );
		$repeater->add_control( 'span', [ 'label' => esc_html__( 'Ordinateur (sur 12 colonnes)', 'tools-adapter' ), 'type' => Controls_Manager::SELECT, 'default' => '6', 'options' => $this->span_options(), 'description' => esc_html__( 'Les cartes d\'une même ligne doivent totaliser 12 (ex. 7 + 5).', 'tools-adapter' ) ] );
		$repeater->add_control( 'span_tablet', [ 'label' => esc_html__( 'Tablette', 'tools-adapter' ), 'type' => Controls_Manager::SELECT, 'default' => '', 'options' => $this->span_options( esc_html__( 'Auto (moitié, ou pleine si 12)', 'tools-adapter' ) ) ] );
		$repeater->add_control( 'span_mobile', [ 'label' => esc_html__( 'Mobile', 'tools-adapter' ), 'type' => Controls_Manager::SELECT, 'default' => '', 'options' => $this->span_options( esc_html__( 'Auto (pleine largeur)', 'tools-adapter' ) ) ] );
		$repeater->add_control(
			'image_position',
			[
				'label'     => esc_html__( 'Cadrage de l\'image', 'tools-adapter' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => '',
				'separator' => 'before',
				'options'   => [
					''              => esc_html__( 'Comme le réglage global', 'tools-adapter' ),
					'center center' => esc_html__( 'Centre', 'tools-adapter' ),
					'center top'    => esc_html__( 'Haut', 'tools-adapter' ),
					'center bottom' => esc_html__( 'Bas', 'tools-adapter' ),
					'left center'   => esc_html__( 'Gauche', 'tools-adapter' ),
					'right center'  => esc_html__( 'Droite', 'tools-adapter' ),
					'left top'      => esc_html__( 'Haut gauche', 'tools-adapter' ),
					'right top'     => esc_html__( 'Haut droite', 'tools-adapter' ),
					'left bottom'   => esc_html__( 'Bas gauche', 'tools-adapter' ),
					'right bottom'  => esc_html__( 'Bas droite', 'tools-adapter' ),
				],
				'selectors' => [ '{{WRAPPER}} {{CURRENT_ITEM}} .ta-sc__img' => 'object-position: {{VALUE}};' ],
			]
		);
		$repeater->end_controls_tab();

		// Onglet Style (surcharge propre à cette carte).
		$repeater->start_controls_tab( 'card_tab_style', [ 'label' => esc_html__( 'Style', 'tools-adapter' ) ] );
		$repeater->add_control( 'card_style_note', [ 'type' => Controls_Manager::RAW_HTML, 'raw' => esc_html__( 'Laissez vide pour utiliser le style global (onglet Style du widget).', 'tools-adapter' ), 'content_classes' => 'elementor-descriptor' ] );
		$repeater->add_group_control( Group_Control_Background::get_type(), [ 'name' => 'item_overlay', 'label' => esc_html__( 'Voile', 'tools-adapter' ), 'types' => [ 'classic', 'gradient' ], 'exclude' => [ 'image' ], 'selector' => '{{WRAPPER}} {{CURRENT_ITEM}} .ta-sc__overlay' ] );
		$repeater->add_control( 'item_badge_bg', [ 'label' => esc_html__( 'Fond du badge', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} {{CURRENT_ITEM}} .ta-sc__badge' => 'background-color: {{VALUE}};' ] ] );
		$repeater->add_control( 'item_badge_color', [ 'label' => esc_html__( 'Texte du badge', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} {{CURRENT_ITEM}} .ta-sc__badge' => 'color: {{VALUE}};' ] ] );
		$repeater->add_control( 'item_title_color', [ 'label' => esc_html__( 'Couleur du titre', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} {{CURRENT_ITEM}} .ta-sc__title' => 'color: {{VALUE}};' ] ] );
		$repeater->add_control( 'item_link_color', [ 'label' => esc_html__( 'Couleur du lien', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} {{CURRENT_ITEM}} .ta-sc__link' => 'color: {{VALUE}};' ] ] );
		$repeater->add_control( 'item_bg_color', [ 'label' => esc_html__( 'Fond (sans image)', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} {{CURRENT_ITEM}}' => 'background-color: {{VALUE}};' ] ] );
		$repeater->end_controls_tab();

		$repeater->end_controls_tabs();

		$this->add_control(
			'cards',
			[
				'label'       => esc_html__( 'Cartes', 'tools-adapter' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ title }}}',
				'default'     => [
					[
						'badge'       => esc_html__( 'Pôle Assainissement', 'tools-adapter' ),
						'title'       => esc_html__( 'Vidange & Hydrocurage', 'tools-adapter' ),
						'description' => esc_html__( 'Vidange certifiée de fosses septiques, dégraisseurs, puisards et séparateurs d\'hydrocarbures. Débouchage haute pression et inspection caméra.', 'tools-adapter' ),
						'tags'        => "Fosses toutes eaux\nUrgence hydrocureur\nCaméra d'inspection\nDégazage cuves fioul\nMise aux normes",
						'link_text'   => esc_html__( 'Découvrir l\'assainissement', 'tools-adapter' ),
						'span'        => '7',
					],
					[
						'badge'       => esc_html__( 'Pôle Travaux Publics', 'tools-adapter' ),
						'title'       => esc_html__( 'Terrassement', 'tools-adapter' ),
						'description' => esc_html__( 'Excavation, démolition, aménagement de cours, accès et terrasses, enrochement.', 'tools-adapter' ),
						'tags'        => "Fondations\nRemblai\nGravier & terre\nBennes TP",
						'link_text'   => esc_html__( 'Voir nos travaux', 'tools-adapter' ),
						'span'        => '5',
					],
					[
						'badge'       => esc_html__( 'Pôle Énergie Bois', 'tools-adapter' ),
						'title'       => esc_html__( 'Bois de chauffage', 'tools-adapter' ),
						'description' => esc_html__( 'Vente et livraison toute l\'année de hêtre et de mélange, scié en 1 m, 50, 33 ou 25 cm — sec, fendu, prêt à brûler.', 'tools-adapter' ),
						'tags'        => "100 % hêtre ou mélange\nVrac ou palette filet\nÀ partir de 105 €/stère",
						'link_text'   => esc_html__( 'Commander du bois', 'tools-adapter' ),
						'span'        => '12',
					],
				],
			]
		);

		$this->add_control(
			'title_tag',
			[
				'label'     => esc_html__( 'Balise du titre', 'tools-adapter' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'h3',
				'separator' => 'before',
				'options'   => [ 'h2' => 'H2', 'h3' => 'H3', 'h4' => 'H4', 'div' => 'div' ],
			]
		);

		$this->add_control( 'show_arrow', [ 'label' => esc_html__( 'Icône après le lien', 'tools-adapter' ), 'type' => Controls_Manager::SWITCHER, 'return_value' => 'yes', 'default' => 'yes' ] );
		$this->add_control( 'link_icon', [ 'label' => esc_html__( 'Icône du lien', 'tools-adapter' ), 'type' => Controls_Manager::ICONS, 'description' => esc_html__( 'Laissez vide pour la flèche par défaut.', 'tools-adapter' ), 'condition' => [ 'show_arrow' => 'yes' ] ] );
		$this->add_control( 'whole_card_link', [ 'label' => esc_html__( 'Toute la carte cliquable', 'tools-adapter' ), 'type' => Controls_Manager::SWITCHER, 'return_value' => 'yes', 'default' => '' ] );

		$this->end_controls_section();

		// ── Style : grille & cartes ──────────────────────────────────────
		$this->start_controls_section( 'section_style_card', [ 'label' => esc_html__( 'Cartes', 'tools-adapter' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_responsive_control(
			'image_fit',
			[
				'label'     => esc_html__( 'Ajustement des images de fond', 'tools-adapter' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'cover',
				'options'   => [
					'cover'   => esc_html__( 'Couvrir toute la carte (recommandé)', 'tools-adapter' ),
					'contain' => esc_html__( 'Image entière (bandes possibles)', 'tools-adapter' ),
					'fill'    => esc_html__( 'Étirer', 'tools-adapter' ),
				],
				'selectors' => [ '{{WRAPPER}} .ta-sc' => '--ta-sc-fit: {{VALUE}};' ],
			]
		);

		$this->add_responsive_control(
			'image_position_global',
			[
				'label'       => esc_html__( 'Position des images de fond', 'tools-adapter' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'center center',
				'options'     => [
					'center center' => esc_html__( 'Centre', 'tools-adapter' ),
					'center top'    => esc_html__( 'Haut', 'tools-adapter' ),
					'center bottom' => esc_html__( 'Bas', 'tools-adapter' ),
					'left center'   => esc_html__( 'Gauche', 'tools-adapter' ),
					'right center'  => esc_html__( 'Droite', 'tools-adapter' ),
				],
				'description' => esc_html__( 'Peut être modifiée carte par carte (onglet « Largeur » de chaque carte).', 'tools-adapter' ),
				'selectors'   => [ '{{WRAPPER}} .ta-sc' => '--ta-sc-pos: {{VALUE}};' ],
			]
		);

		$this->add_responsive_control(
			'gap',
			[
				'label'      => esc_html__( 'Espacement entre cartes', 'tools-adapter' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 'px' => [ 'min' => 0, 'max' => 80 ] ],
				'default'    => [ 'size' => 22, 'unit' => 'px' ],
				'selectors'  => [ '{{WRAPPER}} .ta-sc' => 'gap: {{SIZE}}{{UNIT}};' ],
			]
		);

		$this->add_responsive_control(
			'min_height',
			[
				'label'      => esc_html__( 'Hauteur minimale', 'tools-adapter' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'vh' ],
				'range'      => [ 'px' => [ 'min' => 200, 'max' => 900 ], 'vh' => [ 'min' => 20, 'max' => 100 ] ],
				'default'    => [ 'size' => 440, 'unit' => 'px' ],
				'selectors'  => [ '{{WRAPPER}} .ta-sc__card' => 'min-height: {{SIZE}}{{UNIT}};' ],
			]
		);

		$this->add_responsive_control(
			'padding',
			[
				'label'      => esc_html__( 'Espacement interne', 'tools-adapter' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'default'    => [ 'top' => '84', 'right' => '32', 'bottom' => '40', 'left' => '32', 'unit' => 'px', 'isLinked' => false ],
				'selectors'  => [ '{{WRAPPER}} .ta-sc__body' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
			]
		);

		$this->add_responsive_control(
			'content_valign',
			[
				'label'     => esc_html__( 'Alignement vertical du contenu', 'tools-adapter' ),
				'type'      => Controls_Manager::CHOOSE,
				'default'   => 'flex-end',
				'options'   => [
					'flex-start' => [ 'title' => esc_html__( 'Haut', 'tools-adapter' ), 'icon' => 'eicon-v-align-top' ],
					'center'     => [ 'title' => esc_html__( 'Milieu', 'tools-adapter' ), 'icon' => 'eicon-v-align-middle' ],
					'flex-end'   => [ 'title' => esc_html__( 'Bas', 'tools-adapter' ), 'icon' => 'eicon-v-align-bottom' ],
				],
				'selectors' => [ '{{WRAPPER}} .ta-sc__body' => 'justify-content: {{VALUE}};' ],
			]
		);

		$this->add_responsive_control(
			'content_halign',
			[
				'label'                => esc_html__( 'Alignement horizontal du contenu', 'tools-adapter' ),
				'type'                 => Controls_Manager::CHOOSE,
				'default'              => 'left',
				'options'              => [
					'left'   => [ 'title' => esc_html__( 'Gauche', 'tools-adapter' ), 'icon' => 'eicon-text-align-left' ],
					'center' => [ 'title' => esc_html__( 'Centre', 'tools-adapter' ), 'icon' => 'eicon-text-align-center' ],
					'right'  => [ 'title' => esc_html__( 'Droite', 'tools-adapter' ), 'icon' => 'eicon-text-align-right' ],
				],
				'selectors_dictionary' => [
					'left'   => '--ta-sc-align: flex-start; text-align: left;',
					'center' => '--ta-sc-align: center; text-align: center;',
					'right'  => '--ta-sc-align: flex-end; text-align: right;',
				],
				'selectors'            => [ '{{WRAPPER}} .ta-sc__body' => '{{VALUE}}' ],
			]
		);

		$this->add_responsive_control(
			'content_max_width',
			[
				'label'      => esc_html__( 'Largeur max. du texte', 'tools-adapter' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'      => [ 'px' => [ 'min' => 200, 'max' => 1000 ], '%' => [ 'min' => 20, 'max' => 100 ] ],
				'default'    => [ 'size' => 580, 'unit' => 'px' ],
				'selectors'  => [ '{{WRAPPER}} .ta-sc__body > *' => 'max-width: {{SIZE}}{{UNIT}};' ],
			]
		);

		$this->add_responsive_control(
			'radius',
			[
				'label'      => esc_html__( 'Arrondi', 'tools-adapter' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 'px' => [ 'min' => 0, 'max' => 60 ] ],
				'default'    => [ 'size' => 24, 'unit' => 'px' ],
				'selectors'  => [ '{{WRAPPER}} .ta-sc__card' => 'border-radius: {{SIZE}}{{UNIT}};' ],
			]
		);

		$this->add_group_control( Group_Control_Border::get_type(), [ 'name' => 'card_border', 'selector' => '{{WRAPPER}} .ta-sc__card' ] );

		$this->add_control( 'overlay_heading', [ 'label' => esc_html__( 'Voile sur l\'image', 'tools-adapter' ), 'type' => Controls_Manager::HEADING, 'separator' => 'before' ] );

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'           => 'overlay',
				'types'          => [ 'classic', 'gradient' ],
				'exclude'        => [ 'image' ],
				'selector'       => '{{WRAPPER}} .ta-sc__overlay',
				'fields_options' => [
					'background'     => [ 'default' => 'gradient' ],
					'color'          => [ 'default' => 'rgba(12,28,52,0.25)' ],
					'color_b'        => [ 'default' => 'rgba(12,28,52,0.88)' ],
					'gradient_angle' => [ 'default' => [ 'unit' => 'deg', 'size' => 180 ] ],
				],
			]
		);

		// Normal / survol.
		$this->start_controls_tabs( 'card_state_tabs', [ 'separator' => 'before' ] );

		$this->start_controls_tab( 'card_state_normal', [ 'label' => esc_html__( 'Normal', 'tools-adapter' ) ] );
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'           => 'card_shadow',
				'selector'       => '{{WRAPPER}} .ta-sc__card',
				'fields_options' => [
					'box_shadow_type' => [ 'default' => 'yes' ],
					'box_shadow'      => [ 'default' => [ 'horizontal' => 0, 'vertical' => 14, 'blur' => 34, 'spread' => -10, 'color' => 'rgba(15,30,55,0.35)' ] ],
				],
			]
		);
		$this->add_control( 'overlay_opacity', [ 'label' => esc_html__( 'Opacité du voile', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'range' => [ 'px' => [ 'min' => 0, 'max' => 1, 'step' => 0.05 ] ], 'selectors' => [ '{{WRAPPER}} .ta-sc__overlay' => 'opacity: {{SIZE}};' ] ] );
		$this->end_controls_tab();

		$this->start_controls_tab( 'card_state_hover', [ 'label' => esc_html__( 'Survol', 'tools-adapter' ) ] );
		$this->add_group_control( Group_Control_Box_Shadow::get_type(), [ 'name' => 'card_shadow_hover', 'selector' => '{{WRAPPER}} .ta-sc__card:hover' ] );
		$this->add_control( 'overlay_opacity_hover', [ 'label' => esc_html__( 'Opacité du voile', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'range' => [ 'px' => [ 'min' => 0, 'max' => 1, 'step' => 0.05 ] ], 'selectors' => [ '{{WRAPPER}} .ta-sc__card:hover .ta-sc__overlay' => 'opacity: {{SIZE}};' ] ] );
		$this->add_control( 'hover_zoom', [ 'label' => esc_html__( 'Zoom de l\'image', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'range' => [ 'px' => [ 'min' => 1, 'max' => 1.3, 'step' => 0.01 ] ], 'default' => [ 'size' => 1.06, 'unit' => 'px' ], 'description' => esc_html__( '1 = aucun zoom.', 'tools-adapter' ), 'selectors' => [ '{{WRAPPER}} .ta-sc__card:hover .ta-sc__img' => 'transform: scale({{SIZE}});' ] ] );
		$this->add_control( 'hover_lift', [ 'label' => esc_html__( 'Élévation (px)', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'range' => [ 'px' => [ 'min' => 0, 'max' => 20 ] ], 'selectors' => [ '{{WRAPPER}} .ta-sc__card:hover' => 'transform: translateY(-{{SIZE}}px);' ] ] );
		$this->add_control( 'card_border_hover', [ 'label' => esc_html__( 'Couleur de bordure', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .ta-sc__card:hover' => 'border-color: {{VALUE}};' ] ] );
		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		// ── Style : badge ────────────────────────────────────────────────
		$this->start_controls_section( 'section_style_badge', [ 'label' => esc_html__( 'Badge', 'tools-adapter' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_control( 'badge_color', [ 'label' => esc_html__( 'Texte', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#1a1a1a', 'selectors' => [ '{{WRAPPER}} .ta-sc__badge' => 'color: {{VALUE}};' ] ] );
		$this->add_control( 'badge_bg', [ 'label' => esc_html__( 'Fond', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#FFC107', 'selectors' => [ '{{WRAPPER}} .ta-sc__badge' => 'background-color: {{VALUE}};' ] ] );
		$this->add_group_control( Group_Control_Typography::get_type(), [ 'name' => 'badge_typography', 'selector' => '{{WRAPPER}} .ta-sc__badge' ] );
		$this->add_group_control( Group_Control_Border::get_type(), [ 'name' => 'badge_border', 'selector' => '{{WRAPPER}} .ta-sc__badge' ] );
		$this->add_responsive_control( 'badge_padding', [ 'label' => esc_html__( 'Espacement interne', 'tools-adapter' ), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => [ 'px', 'em' ], 'selectors' => [ '{{WRAPPER}} .ta-sc__badge' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ] ] );
		$this->add_responsive_control( 'badge_radius', [ 'label' => esc_html__( 'Arrondi', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'size_units' => [ 'px', '%' ], 'range' => [ 'px' => [ 'min' => 0, 'max' => 60 ] ], 'selectors' => [ '{{WRAPPER}} .ta-sc__badge' => 'border-radius: {{SIZE}}{{UNIT}};' ] ] );
		$this->add_responsive_control( 'badge_offset', [ 'label' => esc_html__( 'Position depuis le haut', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'size_units' => [ 'px' ], 'range' => [ 'px' => [ 'min' => 0, 'max' => 120 ] ], 'default' => [ 'size' => 24, 'unit' => 'px' ], 'selectors' => [ '{{WRAPPER}} .ta-sc__badge' => 'top: {{SIZE}}{{UNIT}};' ] ] );
		$this->add_responsive_control( 'badge_offset_x', [ 'label' => esc_html__( 'Position depuis le bord', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'size_units' => [ 'px' ], 'range' => [ 'px' => [ 'min' => 0, 'max' => 120 ] ], 'default' => [ 'size' => 24, 'unit' => 'px' ], 'selectors' => [ '{{WRAPPER}} .ta-sc__badge' => '--ta-sc-badge-x: {{SIZE}}{{UNIT}};' ] ] );
		$this->add_control(
			'badge_side',
			[
				'label'                => esc_html__( 'Côté', 'tools-adapter' ),
				'type'                 => Controls_Manager::CHOOSE,
				'default'              => 'left',
				'options'              => [
					'left'  => [ 'title' => esc_html__( 'Gauche', 'tools-adapter' ), 'icon' => 'eicon-h-align-left' ],
					'right' => [ 'title' => esc_html__( 'Droite', 'tools-adapter' ), 'icon' => 'eicon-h-align-right' ],
				],
				'selectors_dictionary' => [
					'left'  => 'left: var(--ta-sc-badge-x, 24px); right: auto;',
					'right' => 'right: var(--ta-sc-badge-x, 24px); left: auto;',
				],
				'selectors'            => [ '{{WRAPPER}} .ta-sc__badge' => '{{VALUE}}' ],
			]
		);

		$this->end_controls_section();

		// ── Style : textes ───────────────────────────────────────────────
		$this->start_controls_section( 'section_style_text', [ 'label' => esc_html__( 'Titre & description', 'tools-adapter' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_control( 'title_color', [ 'label' => esc_html__( 'Couleur du titre', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#ffffff', 'selectors' => [ '{{WRAPPER}} .ta-sc__title' => 'color: {{VALUE}};' ] ] );
		$this->add_group_control( Group_Control_Typography::get_type(), [ 'name' => 'title_typography', 'selector' => '{{WRAPPER}} .ta-sc__title' ] );
		$this->add_responsive_control( 'title_spacing', [ 'label' => esc_html__( 'Espace sous le titre', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'range' => [ 'px' => [ 'min' => 0, 'max' => 60 ] ], 'selectors' => [ '{{WRAPPER}} .ta-sc__title' => 'margin-bottom: {{SIZE}}{{UNIT}};' ] ] );
		$this->add_control( 'description_color', [ 'label' => esc_html__( 'Couleur de la description', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => 'rgba(255,255,255,0.82)', 'separator' => 'before', 'selectors' => [ '{{WRAPPER}} .ta-sc__description' => 'color: {{VALUE}};' ] ] );
		$this->add_group_control( Group_Control_Typography::get_type(), [ 'name' => 'description_typography', 'selector' => '{{WRAPPER}} .ta-sc__description' ] );
		$this->add_responsive_control( 'description_spacing', [ 'label' => esc_html__( 'Espace sous la description', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'range' => [ 'px' => [ 'min' => 0, 'max' => 60 ] ], 'selectors' => [ '{{WRAPPER}} .ta-sc__description' => 'margin-bottom: {{SIZE}}{{UNIT}};' ] ] );

		$this->end_controls_section();

		// ── Style : pastilles ────────────────────────────────────────────
		$this->start_controls_section( 'section_style_tags', [ 'label' => esc_html__( 'Pastilles', 'tools-adapter' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_control( 'tag_color', [ 'label' => esc_html__( 'Texte', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#ffffff', 'selectors' => [ '{{WRAPPER}} .ta-sc__tag' => 'color: {{VALUE}};' ] ] );
		$this->add_control( 'tag_bg', [ 'label' => esc_html__( 'Fond', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => 'rgba(255,255,255,0.12)', 'selectors' => [ '{{WRAPPER}} .ta-sc__tag' => 'background-color: {{VALUE}};' ] ] );
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'           => 'tag_border',
				'selector'       => '{{WRAPPER}} .ta-sc__tag',
				'fields_options' => [
					'border' => [ 'default' => 'solid' ],
					'width'  => [ 'default' => [ 'top' => 1, 'right' => 1, 'bottom' => 1, 'left' => 1, 'unit' => 'px', 'isLinked' => true ] ],
					'color'  => [ 'default' => 'rgba(255,255,255,0.35)' ],
				],
			]
		);
		$this->add_group_control( Group_Control_Typography::get_type(), [ 'name' => 'tag_typography', 'selector' => '{{WRAPPER}} .ta-sc__tag' ] );
		$this->add_responsive_control( 'tag_padding', [ 'label' => esc_html__( 'Espacement interne', 'tools-adapter' ), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => [ 'px', 'em' ], 'selectors' => [ '{{WRAPPER}} .ta-sc__tag' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ] ] );
		$this->add_responsive_control( 'tag_radius', [ 'label' => esc_html__( 'Arrondi', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'size_units' => [ 'px', '%' ], 'range' => [ 'px' => [ 'min' => 0, 'max' => 60 ] ], 'selectors' => [ '{{WRAPPER}} .ta-sc__tag' => 'border-radius: {{SIZE}}{{UNIT}};' ] ] );
		$this->add_responsive_control( 'tag_gap', [ 'label' => esc_html__( 'Espacement entre pastilles', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'range' => [ 'px' => [ 'min' => 0, 'max' => 30 ] ], 'selectors' => [ '{{WRAPPER}} .ta-sc__tags' => 'gap: {{SIZE}}{{UNIT}};' ] ] );
		$this->add_responsive_control( 'tags_spacing', [ 'label' => esc_html__( 'Espace sous les pastilles', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'range' => [ 'px' => [ 'min' => 0, 'max' => 60 ] ], 'selectors' => [ '{{WRAPPER}} .ta-sc__tags' => 'margin-bottom: {{SIZE}}{{UNIT}};' ] ] );

		$this->end_controls_section();

		// ── Style : lien ─────────────────────────────────────────────────
		$this->start_controls_section( 'section_style_link', [ 'label' => esc_html__( 'Lien', 'tools-adapter' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_control( 'link_color', [ 'label' => esc_html__( 'Couleur', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#FFC107', 'selectors' => [ '{{WRAPPER}} .ta-sc__link' => 'color: {{VALUE}};' ] ] );
		$this->add_control( 'link_color_hover', [ 'label' => esc_html__( 'Couleur (survol)', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#ffffff', 'selectors' => [ '{{WRAPPER}} .ta-sc__link:hover, {{WRAPPER}} .ta-sc__card--linked:hover .ta-sc__link' => 'color: {{VALUE}};' ] ] );
		$this->add_group_control( Group_Control_Typography::get_type(), [ 'name' => 'link_typography', 'selector' => '{{WRAPPER}} .ta-sc__link' ] );
		$this->add_control( 'link_icon_size', [ 'label' => esc_html__( 'Taille de l\'icône', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'range' => [ 'px' => [ 'min' => 8, 'max' => 40 ] ], 'separator' => 'before', 'selectors' => [ '{{WRAPPER}} .ta-sc__arrow' => 'font-size: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .ta-sc__arrow svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};' ] ] );
		$this->add_control( 'link_icon_gap', [ 'label' => esc_html__( 'Espace texte / icône', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'range' => [ 'px' => [ 'min' => 0, 'max' => 30 ] ], 'selectors' => [ '{{WRAPPER}} .ta-sc__link' => 'gap: {{SIZE}}{{UNIT}};' ] ] );
		$this->add_control( 'link_icon_shift', [ 'label' => esc_html__( 'Décalage de l\'icône au survol', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'range' => [ 'px' => [ 'min' => 0, 'max' => 16 ] ], 'default' => [ 'size' => 4, 'unit' => 'px' ], 'selectors' => [ '{{WRAPPER}} .ta-sc__link:hover .ta-sc__arrow, {{WRAPPER}} .ta-sc__card--linked:hover .ta-sc__arrow' => 'transform: translateX({{SIZE}}px);' ] ] );

		$this->end_controls_section();
	}

	/**
	 * Render the link icon: chosen icon, or the default arrow SVG.
	 *
	 * @param array $settings Widget settings.
	 */
	private function render_link_icon( array $settings ) {
		echo '<span class="ta-sc__arrow" aria-hidden="true">';
		if ( ! empty( $settings['link_icon']['value'] ) ) {
			Icons_Manager::render_icon( $settings['link_icon'], [ 'aria-hidden' => 'true' ] );
		} else {
			echo '<svg viewBox="0 0 24 24" width="18" height="18" focusable="false"><path d="M5 12h14M13 6l6 6-6 6" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
		}
		echo '</span>';
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$cards    = $settings['cards'] ?? [];

		if ( empty( $cards ) ) {
			return;
		}

		$title_tag  = in_array( $settings['title_tag'] ?? 'h3', [ 'h2', 'h3', 'h4', 'div' ], true ) ? $settings['title_tag'] : 'h3';
		$show_arrow = 'yes' === ( $settings['show_arrow'] ?? '' );
		$whole_card = 'yes' === ( $settings['whole_card_link'] ?? '' );
		?>
		<div class="ta-sc">
			<?php
			foreach ( $cards as $card ) :
				$span      = in_array( (string) ( $card['span'] ?? '6' ), self::SPANS, true ) ? (string) $card['span'] : '6';
				$span_t    = in_array( (string) ( $card['span_tablet'] ?? '' ), self::SPANS, true ) ? (string) $card['span_tablet'] : '';
				$span_m    = in_array( (string) ( $card['span_mobile'] ?? '' ), self::SPANS, true ) ? (string) $card['span_mobile'] : '';
				$url       = $card['link']['url'] ?? '';
				$target    = ! empty( $card['link']['is_external'] ) ? ' target="_blank"' : '';
				$rel       = ! empty( $card['link']['nofollow'] ) ? ' rel="nofollow noopener"' : ( $target ? ' rel="noopener"' : '' );
				$tags      = array_filter( array_map( 'trim', explode( "\n", (string) ( $card['tags'] ?? '' ) ) ) );
				$link_text = \tools_adapter_translate( $card['link_text'] ?? '' );
				$linked    = $whole_card && $url;

				$classes = [ 'ta-sc__card', 'ta-sc__card--span-' . $span, 'elementor-repeater-item-' . ( $card['_id'] ?? '' ) ];
				if ( $span_t ) {
					$classes[] = 'ta-sc__card--t-' . $span_t;
				}
				if ( $span_m ) {
					$classes[] = 'ta-sc__card--m-' . $span_m;
				}
				if ( $linked ) {
					$classes[] = 'ta-sc__card--linked';
				}
				?>
				<article class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
					<div class="ta-sc__media">
						<?php
						if ( ! empty( $card['image']['id'] ) ) {
							echo wp_get_attachment_image( (int) $card['image']['id'], 'large', false, [ 'class' => 'ta-sc__img', 'alt' => '', 'loading' => 'lazy' ] );
						} elseif ( ! empty( $card['image']['url'] ) ) {
							echo '<img class="ta-sc__img" src="' . esc_url( $card['image']['url'] ) . '" alt="" loading="lazy">';
						}
						?>
					</div>
					<div class="ta-sc__overlay" aria-hidden="true"></div>

					<?php if ( ! empty( $card['badge'] ) ) : ?>
						<span class="ta-sc__badge"><?php echo esc_html( \tools_adapter_translate( $card['badge'] ) ); ?></span>
					<?php endif; ?>

					<div class="ta-sc__body">
						<?php if ( ! empty( $card['title'] ) ) : ?>
							<<?php echo esc_attr( $title_tag ); ?> class="ta-sc__title"><?php echo esc_html( \tools_adapter_translate( $card['title'] ) ); ?></<?php echo esc_attr( $title_tag ); ?>>
						<?php endif; ?>

						<?php if ( ! empty( $card['description'] ) ) : ?>
							<p class="ta-sc__description"><?php echo esc_html( \tools_adapter_translate( $card['description'] ) ); ?></p>
						<?php endif; ?>

						<?php if ( $tags ) : ?>
							<ul class="ta-sc__tags">
								<?php foreach ( $tags as $tag ) : ?>
									<li class="ta-sc__tag"><?php echo esc_html( \tools_adapter_translate( $tag ) ); ?></li>
								<?php endforeach; ?>
							</ul>
						<?php endif; ?>

						<?php if ( $link_text && $url ) : ?>
							<a class="ta-sc__link" href="<?php echo esc_url( $url ); ?>"<?php echo $target . $rel; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
								<span><?php echo esc_html( $link_text ); ?></span>
								<?php
								if ( $show_arrow ) {
									$this->render_link_icon( $settings );
								}
								?>
							</a>
						<?php endif; ?>
					</div>
				</article>
			<?php endforeach; ?>
		</div>
		<?php
	}
}
