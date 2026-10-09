<?php
namespace ToolsAdapter\Widgets;

use Elementor\Controls_Manager;
use ToolsAdapter\Base_Widget;
use ToolsAdapter\Repeater;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Icons_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Widget: Timeline / Frise des Étapes du Projet.
 * Permet d'afficher une frise verticale de phases/étapes avec cartes modernes,
 * badges bi-lignes (01 / PHASE), ligne de connexion dégradée et personnalisation totale.
 */
class Timeline extends Base_Widget {

	public function get_name() {
		return 'tools-adapter-timeline';
	}

	public function get_title() {
		return esc_html__( 'Frise des Étapes (Timeline)', 'tools-adapter' );
	}

	public function get_icon() {
		return 'eicon-time-line';
	}

	public function get_categories() {
		return [ 'tools-adapter' ];
	}

	public function get_keywords() {
		return [ 'timeline', 'frise', 'chronologie', 'historique', 'étapes', 'phases', 'processus', 'projet' ];
	}

	public function get_style_depends() {
		return [ 'tools-adapter-timeline' ];
	}

	protected function register_controls() {

		// ==========================================
		// SECTION CONTENU : ÉTAPES / PHASES
		// ==========================================
		$this->start_controls_section(
			'section_content',
			[ 'label' => esc_html__( 'Étapes & Déroulement', 'tools-adapter' ) ]
		);

		$this->add_control(
			'layout_style',
			[
				'label'        => esc_html__( 'Style de présentation', 'tools-adapter' ),
				'type'         => Controls_Manager::SELECT,
				'default'      => 'cards',
				'options'      => [
					'cards'   => esc_html__( 'Frise de projet avec cartes latérales (Recommandé)', 'tools-adapter' ),
					'classic' => esc_html__( 'Frise chronologique classique (Simple)', 'tools-adapter' ),
				],
				'prefix_class' => 'ta-timeline-layout--',
			]
		);

		$this->add_control(
			'badge_format',
			[
				'label'     => esc_html__( 'Format du badge', 'tools-adapter' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'number_label',
				'options'   => [
					'number_label' => esc_html__( 'Numéro + Sous-label (ex: 01 / PHASE)', 'tools-adapter' ),
					'number_only'  => esc_html__( 'Numéro seul (01, 02...)', 'tools-adapter' ),
					'icon_only'    => esc_html__( 'Icône seule au centre', 'tools-adapter' ),
					'custom'       => esc_html__( 'Texte libre / Date', 'tools-adapter' ),
				],
			]
		);

		$this->add_control(
			'default_badge_label',
			[
				'label'       => esc_html__( 'Sous-label global du badge', 'tools-adapter' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'PHASE', 'tools-adapter' ),
				'placeholder' => 'PHASE, ÉTAPE, AN...',
				'condition'   => [ 'badge_format' => 'number_label' ],
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'is_visible',
			[
				'label'        => esc_html__( 'Afficher cette étape', 'tools-adapter' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'description'  => esc_html__( 'Désactivez pour masquer cette étape sans la supprimer.', 'tools-adapter' ),
			]
		);

		$repeater->add_control(
			'title',
			[
				'label'       => esc_html__( 'Titre de l\'étape', 'tools-adapter' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Intitulé de l\'étape', 'tools-adapter' ),
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'card_icon',
			[
				'label'       => esc_html__( 'Icône du titre', 'tools-adapter' ),
				'type'        => Controls_Manager::ICONS,
				'default'     => [
					'value'   => 'fas fa-check',
					'library' => 'fa-solid',
				],
			]
		);

		$repeater->add_control(
			'description',
			[
				'label'       => esc_html__( 'Description détaillée', 'tools-adapter' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 3,
				'default'     => esc_html__( 'Description des actions et livrables de cette phase.', 'tools-adapter' ),
			]
		);

		$repeater->add_control(
			'badge_number_override',
			[
				'label'       => esc_html__( 'Numéro / Texte personnalisé du badge', 'tools-adapter' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Auto (01, 02...)', 'tools-adapter' ),
				'description' => esc_html__( 'Laissez vide pour utiliser la numérotation automatique.', 'tools-adapter' ),
			]
		);

		$repeater->add_control(
			'badge_sublabel_override',
			[
				'label'       => esc_html__( 'Sous-label personnalisé du badge', 'tools-adapter' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Hériter (ex: PHASE)', 'tools-adapter' ),
			]
		);

		$repeater->add_control(
			'custom_badge_colors_heading',
			[
				'label'     => esc_html__( 'Couleurs du badge (spécifique)', 'tools-adapter' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$repeater->add_control(
			'badge_bg_color',
			[
				'label'       => esc_html__( 'Couleur fond du badge', 'tools-adapter' ),
				'type'        => Controls_Manager::COLOR,
				'description' => esc_html__( 'Laissez vide pour hériter de la couleur globale.', 'tools-adapter' ),
				'selectors'   => [
					'{{WRAPPER}} {{CURRENT_ITEM}} .ta-timeline-badge' => 'background-color: {{VALUE}} !important; background: {{VALUE}} !important;',
				],
			]
		);

		$repeater->add_control(
			'badge_text_color',
			[
				'label'       => esc_html__( 'Couleur texte / chiffre du badge', 'tools-adapter' ),
				'type'        => Controls_Manager::COLOR,
				'description' => esc_html__( 'Laissez vide pour hériter de la couleur globale.', 'tools-adapter' ),
				'selectors'   => [
					'{{WRAPPER}} {{CURRENT_ITEM}} .ta-timeline-badge' => 'color: {{VALUE}} !important;',
					'{{WRAPPER}} {{CURRENT_ITEM}} .ta-timeline-badge__num' => 'color: {{VALUE}} !important;',
					'{{WRAPPER}} {{CURRENT_ITEM}} .ta-timeline-badge__lbl' => 'color: {{VALUE}} !important;',
					'{{WRAPPER}} {{CURRENT_ITEM}} .ta-timeline-badge i' => 'color: {{VALUE}} !important; fill: {{VALUE}} !important;',
					'{{WRAPPER}} {{CURRENT_ITEM}} .ta-timeline-badge svg' => 'fill: {{VALUE}} !important;',
				],
			]
		);

		$repeater->add_control(
			'badge_border_color',
			[
				'label'       => esc_html__( 'Couleur de bordure du badge', 'tools-adapter' ),
				'type'        => Controls_Manager::COLOR,
				'selectors'   => [
					'{{WRAPPER}} {{CURRENT_ITEM}} .ta-timeline-badge' => 'border-color: {{VALUE}} !important;',
				],
			]
		);

		$repeater->add_control(
			'link',
			[
				'label'       => esc_html__( 'Lien (optionnel)', 'tools-adapter' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => 'https://exemple.com',
			]
		);

		$this->add_control(
			'items',
			[
				'label'       => esc_html__( 'Liste des étapes', 'tools-adapter' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => [
					[
						'title'       => esc_html__( 'Vous nous contactez', 'tools-adapter' ),
						'card_icon'   => [ 'value' => 'fas fa-phone', 'library' => 'fa-solid' ],
						'description' => esc_html__( 'Afin de convenir d’un premier rendez-vous, vous pouvez nous rendre visite à nos bureaux de Boulogne-Billancourt ou à notre agence de Passy à Paris, nous joindre par téléphone au 01 42 12 04 74, ou directement depuis la page contact de notre site.', 'tools-adapter' ),
					],
					[
						'title'       => esc_html__( 'Fiorellino se déplace chez vous', 'tools-adapter' ),
						'card_icon'   => [ 'value' => 'fas fa-map-marker-alt', 'library' => 'fa-solid' ],
						'description' => esc_html__( 'Nous nous rendons sur place, afin de dialoguer avec vous de votre projet. Nous prenons mesures précises et photos de votre jardin ou terrasse « en l’état » afin que notre bureau d’étude puisse développer votre futur dossier de conception.', 'tools-adapter' ),
					],
					[
						'title'       => esc_html__( 'Fiorellino conçoit votre projet', 'tools-adapter' ),
						'card_icon'   => [ 'value' => 'fas fa-pencil-ruler', 'library' => 'fa-solid' ],
						'description' => esc_html__( 'Le bureau d’étude de Fiorellino imagine votre nouveau jardin ou votre nouvelle terrasse sur-mesure. Fruit d’une créativité et d’une expertise sans égale, nos architectes paysagistes développent un projet judicieux, parfaitement ajusté à votre budget.', 'tools-adapter' ),
					],
					[
						'title'       => esc_html__( 'Vous validez la commande de votre projet', 'tools-adapter' ),
						'card_icon'   => [ 'value' => 'fas fa-file-signature', 'library' => 'fa-solid' ],
						'description' => esc_html__( 'Après avoir intégré les dernières modifications en fonction de vos réflexions et de vos remarques, vous validez la conception, les choix de matériaux et végétaux, le devis détaillé et le planning précis du chantier.', 'tools-adapter' ),
					],
					[
						'title'       => esc_html__( 'Fiorellino met en œuvre votre projet', 'tools-adapter' ),
						'card_icon'   => [ 'value' => 'fas fa-hammer', 'library' => 'fa-solid' ],
						'description' => esc_html__( 'En relation constante avec notre bureau d’étude, nos chefs de chantier et nos jardiniers qualifiés réalisent votre jardin ou terrasse, conformément au cahier des charges et aux plans techniques préalablement définis.', 'tools-adapter' ),
					],
					[
						'title'       => esc_html__( 'Vous validez la finalisation de votre projet', 'tools-adapter' ),
						'card_icon'   => [ 'value' => 'fas fa-glass-cheers', 'library' => 'fa-solid' ],
						'description' => esc_html__( 'Le chantier est terminé. Votre jardin ou votre terrasse n’a plus qu’à s’épanouir ! Nous effectuons ensemble la réception des travaux paysagers, vérifions chaque finition, et vous validez la fin du chantier en réglant le solde restant dû.', 'tools-adapter' ),
					],
					[
						'title'       => esc_html__( 'Fiorellino vous propose un contrat d’entretien sur-mesure', 'tools-adapter' ),
						'card_icon'   => [ 'value' => 'fas fa-shield-alt', 'library' => 'fa-solid' ],
						'description' => esc_html__( 'L’entreprise paysagiste Fiorellino vous conseille la mise en place d’un contrat d’entretien personnalisé pour assurer le suivi, les tailles saisonnières, la santé phytosanitaire et l’épanouissement pérenne de votre jardin ou de votre terrasse.', 'tools-adapter' ),
					],
				],
				'title_field' => '{{{ title }}}',
			]
		);

		$this->end_controls_section();

		// ==========================================
		// SECTION STYLE : DISPOSITION GLOBALE
		// ==========================================
		$this->start_controls_section(
			'section_style_general',
			[ 'label' => esc_html__( 'Disposition & Espacements', 'tools-adapter' ), 'tab' => Controls_Manager::TAB_STYLE ]
		);

		$this->add_responsive_control(
			'max_width',
			[
				'label'      => esc_html__( 'Largeur maximale (px)', 'tools-adapter' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'      => [ 'px' => [ 'min' => 400, 'max' => 1400 ] ],
				'default'    => [ 'size' => 960, 'unit' => 'px' ],
				'selectors'  => [ '{{WRAPPER}} .ta-timeline-wrap' => 'max-width: {{SIZE}}{{UNIT}};' ],
			]
		);

		$this->add_responsive_control(
			'row_gap',
			[
				'label'      => esc_html__( 'Espacement entre étapes (px)', 'tools-adapter' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 'px' => [ 'min' => 15, 'max' => 80 ] ],
				'default'    => [ 'size' => 35, 'unit' => 'px' ],
				'selectors'  => [ '{{WRAPPER}} .ta-timeline-step-row' => 'margin-bottom: {{SIZE}}{{UNIT}};' ],
			]
		);

		$this->add_responsive_control(
			'col_gap',
			[
				'label'      => esc_html__( 'Écart entre le badge et la carte (px)', 'tools-adapter' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 'px' => [ 'min' => 10, 'max' => 60 ] ],
				'default'    => [ 'size' => 30, 'unit' => 'px' ],
				'selectors'  => [ '{{WRAPPER}} .ta-timeline-step-row' => 'gap: {{SIZE}}{{UNIT}};' ],
			]
		);

		$this->end_controls_section();

		// ==========================================
		// SECTION STYLE : BADGE CIRCULAIRE
		// ==========================================
		$this->start_controls_section(
			'section_style_badge',
			[ 'label' => esc_html__( 'Badge de Phase / Étape', 'tools-adapter' ), 'tab' => Controls_Manager::TAB_STYLE ]
		);

		$this->add_responsive_control(
			'badge_size',
			[
				'label'      => esc_html__( 'Diamètre du badge (px)', 'tools-adapter' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => [ 'px' => [ 'min' => 40, 'max' => 120 ] ],
				'default'    => [ 'size' => 72, 'unit' => 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .ta-timeline-badge' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; min-width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ta-timeline-wrap::before' => 'left: calc({{SIZE}}{{UNIT}} / 2);',
				],
			]
		);

		$this->add_control(
			'badge_bg',
			[
				'label'     => esc_html__( 'Couleur de fond du badge', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#152718',
				'selectors' => [ '{{WRAPPER}} .ta-timeline-badge' => 'background-color: {{VALUE}};' ],
			]
		);

		$this->add_control(
			'badge_border_color',
			[
				'label'     => esc_html__( 'Couleur de bordure du badge', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [ '{{WRAPPER}} .ta-timeline-badge' => 'border-color: {{VALUE}};' ],
			]
		);

		$this->add_control(
			'badge_border_width',
			[
				'label'      => esc_html__( 'Épaisseur de bordure du badge (px)', 'tools-adapter' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => [ 'px' => [ 'min' => 0, 'max' => 10 ] ],
				'default'    => [ 'size' => 4, 'unit' => 'px' ],
				'selectors'  => [ '{{WRAPPER}} .ta-timeline-badge' => 'border-width: {{SIZE}}{{UNIT}}; border-style: solid;' ],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'badge_shadow',
				'selector' => '{{WRAPPER}} .ta-timeline-badge',
			]
		);

		$this->add_control(
			'heading_badge_num',
			[
				'label'     => esc_html__( 'Numéro principal (ex: 01)', 'tools-adapter' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'badge_num_color',
			[
				'label'     => esc_html__( 'Couleur du numéro', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#7bc81d',
				'selectors' => [ '{{WRAPPER}} .ta-timeline-badge__num' => 'color: {{VALUE}};' ],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'badge_num_typography',
				'selector' => '{{WRAPPER}} .ta-timeline-badge__num',
			]
		);

		$this->add_control(
			'heading_badge_lbl',
			[
				'label'     => esc_html__( 'Sous-label (ex: PHASE)', 'tools-adapter' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'badge_lbl_color',
			[
				'label'     => esc_html__( 'Couleur du sous-label', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#cbd5e1',
				'selectors' => [ '{{WRAPPER}} .ta-timeline-badge__lbl' => 'color: {{VALUE}};' ],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'badge_lbl_typography',
				'selector' => '{{WRAPPER}} .ta-timeline-badge__lbl',
			]
		);

		$this->end_controls_section();

		// ==========================================
		// SECTION STYLE : LIGNE DE CONNEXION VERTICALE
		// ==========================================
		$this->start_controls_section(
			'section_style_line',
			[ 'label' => esc_html__( 'Ligne verticale de connexion', 'tools-adapter' ), 'tab' => Controls_Manager::TAB_STYLE ]
		);

		$this->add_control(
			'show_line',
			[
				'label'        => esc_html__( 'Afficher la ligne', 'tools-adapter' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'line_color_type',
			[
				'label'     => esc_html__( 'Type de couleur', 'tools-adapter' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'gradient',
				'options'   => [
					'gradient' => esc_html__( 'Dégradé (Vert $\rightarrow$ Sombre)', 'tools-adapter' ),
					'solid'    => esc_html__( 'Couleur unie', 'tools-adapter' ),
				],
				'condition' => [ 'show_line' => 'yes' ],
			]
		);

		$this->add_control(
			'line_color_top',
			[
				'label'     => esc_html__( 'Couleur haut (Dégradé)', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#7bc81d',
				'condition' => [
					'show_line'       => 'yes',
					'line_color_type' => 'gradient',
				],
				'selectors' => [
					'{{WRAPPER}} .ta-timeline-wrap::before' => 'background: linear-gradient(to bottom, {{VALUE}}, {{line_color_bottom.VALUE}});',
				],
			]
		);

		$this->add_control(
			'line_color_bottom',
			[
				'label'     => esc_html__( 'Couleur bas (Dégradé)', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#152718',
				'condition' => [
					'show_line'       => 'yes',
					'line_color_type' => 'gradient',
				],
				'selectors' => [
					'{{WRAPPER}} .ta-timeline-wrap::before' => 'background: linear-gradient(to bottom, {{line_color_top.VALUE}}, {{VALUE}});',
				],
			]
		);

		$this->add_control(
			'line_solid_color',
			[
				'label'     => esc_html__( 'Couleur unie', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#7bc81d',
				'condition' => [
					'show_line'       => 'yes',
					'line_color_type' => 'solid',
				],
				'selectors' => [
					'{{WRAPPER}} .ta-timeline-wrap::before' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'line_thickness',
			[
				'label'      => esc_html__( 'Épaisseur de la ligne (px)', 'tools-adapter' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => [ 'px' => [ 'min' => 1, 'max' => 10 ] ],
				'default'    => [ 'size' => 3, 'unit' => 'px' ],
				'condition'  => [ 'show_line' => 'yes' ],
				'selectors'  => [
					'{{WRAPPER}} .ta-timeline-wrap::before' => 'width: {{SIZE}}{{UNIT}}; margin-left: calc(-{{SIZE}}{{UNIT}} / 2);',
				],
			]
		);

		$this->end_controls_section();

		// ==========================================
		// SECTION STYLE : CARTES DE CONTENU
		// ==========================================
		$this->start_controls_section(
			'section_style_cards',
			[ 'label' => esc_html__( 'Cartes de contenu', 'tools-adapter' ), 'tab' => Controls_Manager::TAB_STYLE ]
		);

		$this->add_control(
			'card_bg',
			[
				'label'     => esc_html__( 'Couleur de fond', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [ '{{WRAPPER}} .ta-timeline-card' => 'background-color: {{VALUE}};' ],
			]
		);

		$this->add_responsive_control(
			'card_padding',
			[
				'label'      => esc_html__( 'Padding interne', 'tools-adapter' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'default'    => [ 'top' => '28', 'right' => '30', 'bottom' => '28', 'left' => '30', 'unit' => 'px' ],
				'selectors'  => [ '{{WRAPPER}} .ta-timeline-card' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
			]
		);

		$this->add_control(
			'card_radius',
			[
				'label'      => esc_html__( 'Rayon des coins (Arrondi)', 'tools-adapter' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => [ 'px' => [ 'min' => 0, 'max' => 50 ] ],
				'default'    => [ 'size' => 14, 'unit' => 'px' ],
				'selectors'  => [ '{{WRAPPER}} .ta-timeline-card' => 'border-radius: {{SIZE}}{{UNIT}};' ],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'card_border',
				'selector' => '{{WRAPPER}} .ta-timeline-card',
			]
		);

		$this->add_control(
			'card_hover_border_color',
			[
				'label'     => esc_html__( 'Couleur de bordure au survol', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#7bc81d',
				'selectors' => [ '{{WRAPPER}} .ta-timeline-card:hover' => 'border-color: {{VALUE}};' ],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'card_shadow',
				'selector' => '{{WRAPPER}} .ta-timeline-card',
			]
		);

		$this->add_control(
			'card_hover_translate',
			[
				'label'        => esc_html__( 'Animation décalage au survol', 'tools-adapter' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->end_controls_section();

		// ==========================================
		// SECTION STYLE : TITRES & ICÔNES DU TITRE
		// ==========================================
		$this->start_controls_section(
			'section_style_title',
			[ 'label' => esc_html__( 'Titre & Icône de l\'étape', 'tools-adapter' ), 'tab' => Controls_Manager::TAB_STYLE ]
		);

		$this->add_control(
			'title_color',
			[
				'label'     => esc_html__( 'Couleur du titre', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#152718',
				'selectors' => [ '{{WRAPPER}} .ta-timeline-card__title' => 'color: {{VALUE}};' ],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .ta-timeline-card__title',
			]
		);

		$this->add_control(
			'heading_title_icon',
			[
				'label'     => esc_html__( 'Icône intégrée au titre', 'tools-adapter' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'icon_color',
			[
				'label'     => esc_html__( 'Couleur de l\'icône', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#7bc81d',
				'selectors' => [ '{{WRAPPER}} .ta-timeline-card__icon' => 'color: {{VALUE}}; fill: {{VALUE}};' ],
			]
		);

		$this->add_responsive_control(
			'icon_size',
			[
				'label'      => esc_html__( 'Taille de l\'icône (px)', 'tools-adapter' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => [ 'px' => [ 'min' => 12, 'max' => 40 ] ],
				'default'    => [ 'size' => 18, 'unit' => 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .ta-timeline-card__icon' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ta-timeline-card__icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'icon_spacing',
			[
				'label'      => esc_html__( 'Écart entre l\'icône et le texte (px)', 'tools-adapter' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => [ 'px' => [ 'min' => 4, 'max' => 30 ] ],
				'default'    => [ 'size' => 10, 'unit' => 'px' ],
				'selectors'  => [ '{{WRAPPER}} .ta-timeline-card__title' => 'gap: {{SIZE}}{{UNIT}};' ],
			]
		);

		$this->end_controls_section();

		// ==========================================
		// SECTION STYLE : DESCRIPTION
		// ==========================================
		$this->start_controls_section(
			'section_style_desc',
			[ 'label' => esc_html__( 'Texte descriptif', 'tools-adapter' ), 'tab' => Controls_Manager::TAB_STYLE ]
		);

		$this->add_control(
			'desc_color',
			[
				'label'     => esc_html__( 'Couleur du texte', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#64748b',
				'selectors' => [ '{{WRAPPER}} .ta-timeline-card__desc' => 'color: {{VALUE}};' ],
			]
		);

		$this->add_control(
			'desc_bold_color',
			[
				'label'     => esc_html__( 'Couleur du texte en gras', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#152718',
				'selectors' => [ '{{WRAPPER}} .ta-timeline-card__desc strong' => 'color: {{VALUE}};' ],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'desc_typography',
				'selector' => '{{WRAPPER}} .ta-timeline-card__desc',
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$items    = $settings['items'] ?? [];

		if ( empty( $items ) ) {
			return;
		}

		$badge_format        = $settings['badge_format'] ?? 'number_label';
		$default_badge_label = $settings['default_badge_label'] ?? 'PHASE';
		$show_line           = 'yes' === ( $settings['show_line'] ?? 'yes' );
		$has_hover_effect    = 'yes' === ( $settings['card_hover_translate'] ?? 'yes' );

		$wrap_classes = [ 'ta-timeline-wrap' ];
		if ( ! $show_line ) {
			$wrap_classes[] = 'ta-timeline--no-line';
		}
		if ( $has_hover_effect ) {
			$wrap_classes[] = 'ta-timeline--has-hover';
		}
		?>
		<div class="<?php echo esc_attr( implode( ' ', $wrap_classes ) ); ?>">
			<?php
			$visible_index = 1;
			foreach ( $items as $index => $item ) :
				if ( 'no' === ( $item['is_visible'] ?? 'yes' ) ) {
					continue;
				}

				$title       = $item['title'] ?? '';
				$desc        = $item['description'] ?? '';
				$card_icon   = $item['card_icon'] ?? [];
				$link        = $item['link'] ?? [];
				$has_link    = ! empty( $link['url'] );

				// Numérotation du badge
				$badge_num = ! empty( $item['badge_number_override'] )
					? $item['badge_number_override']
					: sprintf( '%02d', $visible_index );

				// Sous-label du badge
				$badge_lbl = ! empty( $item['badge_sublabel_override'] )
					? $item['badge_sublabel_override']
					: $default_badge_label;

				$visible_index++;

				$badge_inline_styles = [];
				if ( ! empty( $item['badge_bg_color'] ) ) {
					$badge_inline_styles[] = 'background: ' . esc_attr( $item['badge_bg_color'] ) . ' !important;';
				}
				if ( ! empty( $item['badge_text_color'] ) ) {
					$badge_inline_styles[] = 'color: ' . esc_attr( $item['badge_text_color'] ) . ' !important;';
				}
				if ( ! empty( $item['badge_border_color'] ) ) {
					$badge_inline_styles[] = 'border-color: ' . esc_attr( $item['badge_border_color'] ) . ' !important;';
				}
				$badge_style_attr = ! empty( $badge_inline_styles ) ? ' style="' . implode( ' ', $badge_inline_styles ) . '"' : '';

				$row_classes = [ 'ta-timeline-step-row' ];
				if ( ! empty( $item['_id'] ) ) {
					$row_classes[] = 'elementor-repeater-item-' . esc_attr( $item['_id'] );
				}
				?>
				<div class="<?php echo esc_attr( implode( ' ', $row_classes ) ); ?>">
					<!-- Badge circulaire -->
					<div class="ta-timeline-badge" aria-hidden="true"<?php echo $badge_style_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
						<?php if ( 'number_label' === $badge_format ) : ?>
							<span class="ta-timeline-badge__num"><?php echo esc_html( $badge_num ); ?></span>
							<span class="ta-timeline-badge__lbl"><?php echo esc_html( \tools_adapter_translate( $badge_lbl ) ); ?></span>
						<?php elseif ( 'number_only' === $badge_format ) : ?>
							<span class="ta-timeline-badge__num"><?php echo esc_html( $badge_num ); ?></span>
						<?php elseif ( 'icon_only' === $badge_format && ! empty( $card_icon['value'] ) ) : ?>
							<span class="ta-timeline-badge__icon">
								<?php Icons_Manager::render_icon( $card_icon, [ 'aria-hidden' => 'true' ] ); ?>
							</span>
						<?php else : ?>
							<span class="ta-timeline-badge__custom"><?php echo esc_html( $badge_num ); ?></span>
						<?php endif; ?>
					</div>

					<!-- Carte de contenu -->
					<div class="ta-timeline-card">
						<?php if ( ! empty( $title ) ) : ?>
							<h4 class="ta-timeline-card__title">
								<?php if ( ! empty( $card_icon['value'] ) ) : ?>
									<span class="ta-timeline-card__icon" aria-hidden="true">
										<?php Icons_Manager::render_icon( $card_icon, [ 'aria-hidden' => 'true' ] ); ?>
									</span>
								<?php endif; ?>
								<span><?php echo esc_html( \tools_adapter_translate( $title ) ); ?></span>
							</h4>
						<?php endif; ?>

						<?php if ( ! empty( $desc ) ) : ?>
							<div class="ta-timeline-card__desc">
								<p><?php echo wp_kses_post( \tools_adapter_translate( $desc ) ); ?></p>
							</div>
						<?php endif; ?>

						<?php if ( $has_link ) : ?>
							<a class="ta-timeline-card__link"
							   href="<?php echo esc_url( $link['url'] ); ?>"
							   <?php echo ! empty( $link['is_external'] ) ? ' target="_blank"' : ''; ?>
							   <?php echo ! empty( $link['nofollow'] ) ? ' rel="nofollow"' : ''; ?>>
								<span class="screen-reader-text"><?php echo esc_html( $title ); ?></span>
							</a>
						<?php endif; ?>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
		<?php
	}
}
