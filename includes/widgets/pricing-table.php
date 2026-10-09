<?php
namespace ToolsAdapter\Widgets;

use Elementor\Controls_Manager;
use ToolsAdapter\Base_Widget;
use ToolsAdapter\Repeater;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Widget: Tableau de tarifs (Grille de tarification 3 offres).
 * Permet d'afficher 1 à 3 offres modulables avec masquage individuel et personnalisation complète.
 */
class Pricing_Table extends Base_Widget {

	public function get_name() {
		return 'tools-adapter-pricing-table';
	}

	public function get_title() {
		return esc_html__( 'Grille de tarifs (3 offres)', 'tools-adapter' );
	}

	public function get_icon() {
		return 'eicon-price-table';
	}

	public function get_categories() {
		return [ 'tools-adapter' ];
	}

	public function get_keywords() {
		return [ 'tarifs', 'prix', 'plan', 'pricing', 'offre', 'forfait', 'table', 'devis' ];
	}

	public function get_style_depends() {
		return [ 'tools-adapter-pricing-table' ];
	}

	protected function register_controls() {

		// ==========================================
		// SECTION CONTENU : DISPOSITION GLOBALE
		// ==========================================
		$this->start_controls_section(
			'section_layout',
			[ 'label' => esc_html__( 'Disposition & Grille', 'tools-adapter' ) ]
		);

		$this->add_responsive_control(
			'columns_count',
			[
				'label'     => esc_html__( 'Colonnes max (Bureau)', 'tools-adapter' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => '3',
				'options'   => [
					'1' => '1 colonne',
					'2' => '2 colonnes',
					'3' => '3 colonnes',
				],
				'selectors' => [
					'{{WRAPPER}} .ta-pricing-grid' => 'grid-template-columns: repeat({{VALUE}}, 1fr);',
				],
			]
		);

		$this->add_responsive_control(
			'grid_gap',
			[
				'label'      => esc_html__( 'Espacement entre cartes (px)', 'tools-adapter' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 'px' => [ 'min' => 10, 'max' => 60 ] ],
				'default'    => [ 'size' => 30, 'unit' => 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .ta-pricing-grid' => 'gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'grid_max_width',
			[
				'label'      => esc_html__( 'Largeur maximale de la grille (px)', 'tools-adapter' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'      => [ 'px' => [ 'min' => 400, 'max' => 1400 ] ],
				'default'    => [ 'size' => 1040, 'unit' => 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .ta-pricing-wrapper' => 'max-width: {{SIZE}}{{UNIT}}; margin-left: auto; margin-right: auto;',
				],
			]
		);

		$this->end_controls_section();

		// ==========================================
		// SECTIONS CONTENU : OFFRES 1, 2 ET 3
		// ==========================================
		$this->register_offer_controls(
			1,
			esc_html__( 'Offre 1 : Balcons & Petits Jardins', 'tools-adapter' ),
			'yes',
			'',
			'',
			esc_html__( 'Balcons & Petits Jardins', 'tools-adapter' ),
			esc_html__( 'Pour surfaces jusqu\'à 30 m²', 'tools-adapter' ),
			'amount',
			'465',
			'€',
			'TTC',
			'Sur Mesure',
			[
				[ 'text' => esc_html__( 'Relevé et analyse contextuelle du site', 'tools-adapter' ), 'included' => 'yes', 'is_bold' => '' ],
				[ 'text' => esc_html__( 'Plan technique coté à l\'échelle', 'tools-adapter' ), 'included' => 'yes', 'is_bold' => '' ],
				[ 'text' => esc_html__( 'Plan de masse et d’aménagement végétal', 'tools-adapter' ), 'included' => 'yes', 'is_bold' => '' ],
				[ 'text' => esc_html__( 'Palette végétale personnalisée', 'tools-adapter' ), 'included' => 'yes', 'is_bold' => '' ],
				[ 'text' => esc_html__( 'Sélection de contenants (pots, jardinières)', 'tools-adapter' ), 'included' => 'yes', 'is_bold' => '' ],
				[ 'text' => esc_html__( 'Devis estimatif précis pour réalisation', 'tools-adapter' ), 'included' => 'yes', 'is_bold' => '' ],
				[ 'text' => esc_html__( 'Modélisation et perspectives 3D hyperréalistes', 'tools-adapter' ), 'included' => '', 'is_bold' => '' ],
				[ 'text' => esc_html__( 'Étude complexe d\'éclairage et d\'arrosage multi-zones', 'tools-adapter' ), 'included' => '', 'is_bold' => '' ],
			],
			esc_html__( 'Choisir ce forfait', 'tools-adapter' ),
			'default'
		);

		$this->register_offer_controls(
			2,
			esc_html__( 'Offre 2 : Terrasses & Grands Jardins (Recommandée)', 'tools-adapter' ),
			'yes',
			'yes',
			esc_html__( 'LE PLUS DEMANDÉ', 'tools-adapter' ),
			esc_html__( 'Terrasses & Grands Jardins', 'tools-adapter' ),
			esc_html__( 'Dossier complet haute précision', 'tools-adapter' ),
			'amount',
			'885',
			'€',
			'TTC',
			'Sur Mesure',
			[
				[ 'text' => esc_html__( 'Relevé exhaustif sur site par notre paysagiste', 'tools-adapter' ), 'included' => 'yes', 'is_bold' => '' ],
				[ 'text' => esc_html__( 'Plan technique complet coté géométrique', 'tools-adapter' ), 'included' => 'yes', 'is_bold' => '' ],
				[ 'text' => esc_html__( 'Plan de masse artistique & circulations', 'tools-adapter' ), 'included' => 'yes', 'is_bold' => '' ],
				[ 'text' => esc_html__( 'Plan d’arrosage automatique raisonné', 'tools-adapter' ), 'included' => 'yes', 'is_bold' => '' ],
				[ 'text' => esc_html__( 'Plan d’éclairage nocturne calibré', 'tools-adapter' ), 'included' => 'yes', 'is_bold' => '' ],
				[ 'text' => esc_html__( 'Palettes de végétaux poétiques & pérennes', 'tools-adapter' ), 'included' => 'yes', 'is_bold' => '' ],
				[ 'text' => esc_html__( 'Palettes de matériaux & mobiliers recommandés', 'tools-adapter' ), 'included' => 'yes', 'is_bold' => '' ],
				[ 'text' => esc_html__( 'Perspectives et projections 3D réalistes', 'tools-adapter' ), 'included' => 'yes', 'is_bold' => 'yes' ],
				[ 'text' => esc_html__( 'Budget prévisionnel clé-en-main ajusté', 'tools-adapter' ), 'included' => 'yes', 'is_bold' => '' ],
			],
			esc_html__( 'Commander mon étude complète', 'tools-adapter' ),
			'primary'
		);

		$this->register_offer_controls(
			3,
			esc_html__( 'Offre 3 : Domaines & Espaces Pro', 'tools-adapter' ),
			'yes',
			'',
			'',
			esc_html__( 'Domaines & Espaces Pro', 'tools-adapter' ),
			esc_html__( 'Hôtels, toits d\'envergure, copropriétés', 'tools-adapter' ),
			'text',
			'',
			'€',
			'TTC',
			esc_html__( 'Sur Mesure', 'tools-adapter' ),
			[
				[ 'text' => esc_html__( 'Relevé topographique et contraintes ERP', 'tools-adapter' ), 'included' => 'yes', 'is_bold' => '' ],
				[ 'text' => esc_html__( 'Étude de charge toiture avec bureau de contrôle', 'tools-adapter' ), 'included' => 'yes', 'is_bold' => '' ],
				[ 'text' => esc_html__( 'Ensemble complet des 8 livrables techniques', 'tools-adapter' ), 'included' => 'yes', 'is_bold' => '' ],
				[ 'text' => esc_html__( 'Plusieurs déclinaisons et scénarios d\'aménagements', 'tools-adapter' ), 'included' => 'yes', 'is_bold' => '' ],
				[ 'text' => esc_html__( 'Dossier de présentation d\'assemblée générale / investisseurs', 'tools-adapter' ), 'included' => 'yes', 'is_bold' => '' ],
				[ 'text' => esc_html__( 'Accompagnement jusqu\'à la réception de chantier', 'tools-adapter' ), 'included' => 'yes', 'is_bold' => '' ],
			],
			esc_html__( 'Consulter notre bureau d\'étude', 'tools-adapter' ),
			'default'
		);

		// ==========================================
		// SECTION CONTENU : ENCART ENGAGEMENT / GARANTIE
		// ==========================================
		$this->start_controls_section(
			'section_guarantee',
			[ 'label' => esc_html__( 'Encart Réassurance / Engagement', 'tools-adapter' ) ]
		);

		$this->add_control(
			'show_guarantee',
			[
				'label'        => esc_html__( 'Afficher l\'encart sous la grille', 'tools-adapter' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'guarantee_title',
			[
				'label'       => esc_html__( 'Titre / Préfixe en gras', 'tools-adapter' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Engagement Fiorellino :', 'tools-adapter' ),
				'condition'   => [ 'show_guarantee' => 'yes' ],
				'label_block' => true,
			]
		);

		$this->add_control(
			'guarantee_text',
			[
				'label'       => esc_html__( 'Texte explicatif', 'tools-adapter' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 3,
				'default'     => esc_html__( 'Le coût de votre dossier de conception peut être déduit du montant des travaux de réalisation lorsque ceux-ci sont confiés à nos équipes de paysagistes qualifiés.', 'tools-adapter' ),
				'condition'   => [ 'show_guarantee' => 'yes' ],
				'label_block' => true,
			]
		);

		$this->end_controls_section();

		// =========================================================================
		// ONGLETS DE STYLE ELEMENTOR
		// =========================================================================

		// --- STYLE : CARTES TARIFS ---
		$this->start_controls_section(
			'section_style_cards',
			[ 'label' => esc_html__( 'Cartes de tarifs', 'tools-adapter' ), 'tab' => Controls_Manager::TAB_STYLE ]
		);

		$this->add_control(
			'card_bg',
			[
				'label'     => esc_html__( 'Couleur de fond', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [ '{{WRAPPER}} .ta-pricing-card' => 'background-color: {{VALUE}};' ],
			]
		);

		$this->add_responsive_control(
			'card_padding',
			[
				'label'      => esc_html__( 'Espacement interne (Padding)', 'tools-adapter' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'default'    => [ 'top' => '40', 'right' => '30', 'bottom' => '40', 'left' => '30', 'unit' => 'px' ],
				'selectors'  => [ '{{WRAPPER}} .ta-pricing-card' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
			]
		);

		$this->add_control(
			'card_radius',
			[
				'label'      => esc_html__( 'Rayon des coins (Arrondi)', 'tools-adapter' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => [ 'px' => [ 'min' => 0, 'max' => 60 ] ],
				'default'    => [ 'size' => 24, 'unit' => 'px' ],
				'selectors'  => [ '{{WRAPPER}} .ta-pricing-card' => 'border-radius: {{SIZE}}{{UNIT}};' ],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'card_border',
				'selector' => '{{WRAPPER}} .ta-pricing-card:not(.is-featured)',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'card_shadow',
				'selector' => '{{WRAPPER}} .ta-pricing-card',
			]
		);

		$this->add_control(
			'heading_featured_card',
			[
				'label'     => esc_html__( 'Offre mise en avant (Recommandée)', 'tools-adapter' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'card_featured_border_color',
			[
				'label'     => esc_html__( 'Couleur bordure mise en avant', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#7bc81d',
				'selectors' => [ '{{WRAPPER}} .ta-pricing-card.is-featured' => 'border-color: {{VALUE}};' ],
			]
		);

		$this->add_control(
			'card_featured_border_width',
			[
				'label'      => esc_html__( 'Épaisseur bordure mise en avant (px)', 'tools-adapter' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => [ 'px' => [ 'min' => 1, 'max' => 10 ] ],
				'default'    => [ 'size' => 2, 'unit' => 'px' ],
				'selectors'  => [ '{{WRAPPER}} .ta-pricing-card.is-featured' => 'border-width: {{SIZE}}{{UNIT}}; border-style: solid;' ],
			]
		);

		$this->add_control(
			'card_featured_scale',
			[
				'label'        => esc_html__( 'Effet zoom / surélévation de la carte', 'tools-adapter' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->end_controls_section();

		// --- STYLE : BADGE FLOTTANT ---
		$this->start_controls_section(
			'section_style_badge',
			[ 'label' => esc_html__( 'Badge / Ruban populaire', 'tools-adapter' ), 'tab' => Controls_Manager::TAB_STYLE ]
		);

		$this->add_control(
			'badge_bg',
			[
				'label'     => esc_html__( 'Couleur de fond du badge', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#7bc81d',
				'selectors' => [ '{{WRAPPER}} .ta-pricing-badge' => 'background-color: {{VALUE}};' ],
			]
		);

		$this->add_control(
			'badge_color',
			[
				'label'     => esc_html__( 'Couleur du texte', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [ '{{WRAPPER}} .ta-pricing-badge' => 'color: {{VALUE}};' ],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'badge_typography',
				'selector' => '{{WRAPPER}} .ta-pricing-badge',
			]
		);

		$this->end_controls_section();

		// --- STYLE : TITRES & EN-TÊTE ---
		$this->start_controls_section(
			'section_style_header',
			[ 'label' => esc_html__( 'En-tête & Titres', 'tools-adapter' ), 'tab' => Controls_Manager::TAB_STYLE ]
		);

		$this->add_control(
			'title_color',
			[
				'label'     => esc_html__( 'Couleur du nom de l\'offre', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#152718',
				'selectors' => [ '{{WRAPPER}} .ta-pricing-title' => 'color: {{VALUE}};' ],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .ta-pricing-title',
			]
		);

		$this->add_control(
			'subtitle_color',
			[
				'label'     => esc_html__( 'Couleur du sous-titre', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#64748b',
				'separator' => 'before',
				'selectors' => [ '{{WRAPPER}} .ta-pricing-subtitle' => 'color: {{VALUE}};' ],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'subtitle_typography',
				'selector' => '{{WRAPPER}} .ta-pricing-subtitle',
			]
		);

		$this->add_control(
			'header_divider_color',
			[
				'label'     => esc_html__( 'Ligne de séparation', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#f1f5f9',
				'separator' => 'before',
				'selectors' => [ '{{WRAPPER}} .ta-pricing-header' => 'border-bottom-color: {{VALUE}};' ],
			]
		);

		$this->end_controls_section();

		// --- STYLE : PRIX ---
		$this->start_controls_section(
			'section_style_price',
			[ 'label' => esc_html__( 'Prix & Tarification', 'tools-adapter' ), 'tab' => Controls_Manager::TAB_STYLE ]
		);

		$this->add_control(
			'price_color',
			[
				'label'     => esc_html__( 'Couleur du montant / texte libre', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#152718',
				'selectors' => [
					'{{WRAPPER}} .ta-pricing-amount .ta-pricing-val' => 'color: {{VALUE}};',
					'{{WRAPPER}} .ta-pricing-amount .ta-pricing-cur' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'price_typography',
				'selector' => '{{WRAPPER}} .ta-pricing-amount .ta-pricing-val',
			]
		);

		$this->add_control(
			'period_color',
			[
				'label'     => esc_html__( 'Couleur de la mention (ex: TTC)', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#64748b',
				'separator' => 'before',
				'selectors' => [ '{{WRAPPER}} .ta-pricing-amount .ta-pricing-period' => 'color: {{VALUE}};' ],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'period_typography',
				'selector' => '{{WRAPPER}} .ta-pricing-amount .ta-pricing-period',
			]
		);

		$this->end_controls_section();

		// --- STYLE : FONCTIONNALITÉS ---
		$this->start_controls_section(
			'section_style_features',
			[ 'label' => esc_html__( 'Liste des fonctionnalités', 'tools-adapter' ), 'tab' => Controls_Manager::TAB_STYLE ]
		);

		$this->add_control(
			'feature_text_color',
			[
				'label'     => esc_html__( 'Couleur du texte', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#334155',
				'selectors' => [ '{{WRAPPER}} .ta-pricing-feature-item' => 'color: {{VALUE}};' ],
			]
		);

		$this->add_control(
			'feature_bold_color',
			[
				'label'     => esc_html__( 'Couleur texte mis en valeur (Gras)', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#152718',
				'selectors' => [ '{{WRAPPER}} .ta-pricing-feature-text.is-bold' => 'color: {{VALUE}};' ],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'features_typography',
				'selector' => '{{WRAPPER}} .ta-pricing-feature-item',
			]
		);

		$this->add_control(
			'feature_icon_yes_color',
			[
				'label'     => esc_html__( 'Couleur icône incluse (✓)', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#7bc81d',
				'separator' => 'before',
				'selectors' => [ '{{WRAPPER}} .ta-pricing-feature-item:not(.is-disabled) .ta-pricing-feature-icon' => 'color: {{VALUE}};' ],
			]
		);

		$this->add_control(
			'feature_icon_no_color',
			[
				'label'     => esc_html__( 'Couleur icône exclue (✕)', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#cbd5e1',
				'selectors' => [ '{{WRAPPER}} .ta-pricing-feature-item.is-disabled .ta-pricing-feature-icon' => 'color: {{VALUE}};' ],
			]
		);

		$this->add_control(
			'feature_disabled_text_color',
			[
				'label'     => esc_html__( 'Couleur texte exclu / désactivé', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#94a3b8',
				'selectors' => [ '{{WRAPPER}} .ta-pricing-feature-item.is-disabled' => 'color: {{VALUE}};' ],
			]
		);

		$this->add_responsive_control(
			'features_gap',
			[
				'label'      => esc_html__( 'Espacement vertical entre lignes', 'tools-adapter' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 'px' => [ 'min' => 6, 'max' => 30 ] ],
				'default'    => [ 'size' => 14, 'unit' => 'px' ],
				'selectors'  => [ '{{WRAPPER}} .ta-pricing-features' => 'gap: {{SIZE}}{{UNIT}};' ],
			]
		);

		$this->end_controls_section();

		// --- STYLE : BOUTONS CTA ---
		$this->start_controls_section(
			'section_style_buttons',
			[ 'label' => esc_html__( 'Boutons d\'action (CTA)', 'tools-adapter' ), 'tab' => Controls_Manager::TAB_STYLE ]
		);

		$this->add_responsive_control(
			'button_radius',
			[
				'label'      => esc_html__( 'Arrondi des boutons', 'tools-adapter' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => [ 'px' => [ 'min' => 0, 'max' => 50 ] ],
				'default'    => [ 'size' => 50, 'unit' => 'px' ],
				'selectors'  => [ '{{WRAPPER}} .ta-pricing-btn' => 'border-radius: {{SIZE}}{{UNIT}};' ],
			]
		);

		$this->add_responsive_control(
			'button_padding',
			[
				'label'      => esc_html__( 'Espacement interne du bouton', 'tools-adapter' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'default'    => [ 'top' => '14', 'right' => '24', 'bottom' => '14', 'left' => '24', 'unit' => 'px' ],
				'selectors'  => [ '{{WRAPPER}} .ta-pricing-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'button_typography',
				'selector' => '{{WRAPPER}} .ta-pricing-btn',
			]
		);

		$this->add_control(
			'heading_style_btn_primary',
			[
				'label'     => esc_html__( 'Bouton primaire (Vert / Mis en avant)', 'tools-adapter' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'btn_primary_bg',
			[
				'label'     => esc_html__( 'Couleur fond', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#7bc81d',
				'selectors' => [ '{{WRAPPER}} .ta-pricing-btn--primary' => 'background-color: {{VALUE}};' ],
			]
		);

		$this->add_control(
			'btn_primary_color',
			[
				'label'     => esc_html__( 'Couleur texte', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [ '{{WRAPPER}} .ta-pricing-btn--primary' => 'color: {{VALUE}};' ],
			]
		);

		$this->add_control(
			'btn_primary_hover_bg',
			[
				'label'     => esc_html__( 'Couleur fond (Survol)', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#68ab18',
				'selectors' => [ '{{WRAPPER}} .ta-pricing-btn--primary:hover' => 'background-color: {{VALUE}};' ],
			]
		);

		$this->add_control(
			'heading_style_btn_default',
			[
				'label'     => esc_html__( 'Bouton standard (Sombre)', 'tools-adapter' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'btn_default_bg',
			[
				'label'     => esc_html__( 'Couleur fond', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#152718',
				'selectors' => [ '{{WRAPPER}} .ta-pricing-btn--default' => 'background-color: {{VALUE}};' ],
			]
		);

		$this->add_control(
			'btn_default_color',
			[
				'label'     => esc_html__( 'Couleur texte', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [ '{{WRAPPER}} .ta-pricing-btn--default' => 'color: {{VALUE}};' ],
			]
		);

		$this->add_control(
			'btn_default_hover_bg',
			[
				'label'     => esc_html__( 'Couleur fond (Survol)', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#203c25',
				'selectors' => [ '{{WRAPPER}} .ta-pricing-btn--default:hover' => 'background-color: {{VALUE}};' ],
			]
		);

		$this->end_controls_section();

		// --- STYLE : ENCART ENGAGEMENT / GARANTIE ---
		$this->start_controls_section(
			'section_style_guarantee',
			[ 'label' => esc_html__( 'Encart Engagement / Réassurance', 'tools-adapter' ), 'tab' => Controls_Manager::TAB_STYLE ]
		);

		$this->add_control(
			'guarantee_box_bg',
			[
				'label'     => esc_html__( 'Fond de l\'encart', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(123, 200, 29, 0.08)',
				'selectors' => [ '{{WRAPPER}} .ta-pricing-guarantee' => 'background-color: {{VALUE}};' ],
			]
		);

		$this->add_control(
			'guarantee_box_border',
			[
				'label'     => esc_html__( 'Bordure pointillés', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(123, 200, 29, 0.4)',
				'selectors' => [ '{{WRAPPER}} .ta-pricing-guarantee' => 'border-color: {{VALUE}};' ],
			]
		);

		$this->add_control(
			'guarantee_text_color',
			[
				'label'     => esc_html__( 'Couleur du texte', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#152718',
				'selectors' => [ '{{WRAPPER}} .ta-pricing-guarantee' => 'color: {{VALUE}};' ],
			]
		);

		$this->add_control(
			'guarantee_icon_color',
			[
				'label'     => esc_html__( 'Couleur de l\'icône', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#7bc81d',
				'selectors' => [ '{{WRAPPER}} .ta-pricing-guarantee__icon' => 'color: {{VALUE}};' ],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Enregistre les contrôles pour une offre (colonne).
	 */
	protected function register_offer_controls(
		$index,
		$section_label,
		$default_visible,
		$default_featured,
		$default_badge,
		$default_title,
		$default_subtitle,
		$default_price_type,
		$default_price,
		$default_currency,
		$default_period,
		$default_custom_price,
		$default_features,
		$default_btn_text,
		$default_btn_style
	) {
		$prefix = "plan_{$index}_";

		$this->start_controls_section(
			"section_plan_{$index}",
			[ 'label' => $section_label ]
		);

		$this->add_control(
			"{$prefix}visible",
			[
				'label'        => esc_html__( 'Afficher cette offre', 'tools-adapter' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => $default_visible,
				'description'  => esc_html__( 'Désactivez pour masquer cette colonne sur votre site tout en conservant vos réglages.', 'tools-adapter' ),
			]
		);

		$this->add_control(
			"{$prefix}featured",
			[
				'label'        => esc_html__( 'Mettre en avant cette offre', 'tools-adapter' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => $default_featured,
				'condition'    => [ "{$prefix}visible" => 'yes' ],
			]
		);

		$this->add_control(
			"{$prefix}badge_text",
			[
				'label'       => esc_html__( 'Texte du badge supérieur', 'tools-adapter' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => $default_badge,
				'placeholder' => esc_html__( 'LE PLUS DEMANDÉ', 'tools-adapter' ),
				'condition'   => [
					"{$prefix}visible"  => 'yes',
					"{$prefix}featured" => 'yes',
				],
			]
		);

		$this->add_control(
			"{$prefix}title",
			[
				'label'       => esc_html__( 'Nom du forfait', 'tools-adapter' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => $default_title,
				'separator'   => 'before',
				'condition'   => [ "{$prefix}visible" => 'yes' ],
				'label_block' => true,
			]
		);

		$this->add_control(
			"{$prefix}title_tag",
			[
				'label'     => esc_html__( 'Balise HTML du titre', 'tools-adapter' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'h3',
				'options'   => [
					'h2'   => 'H2',
					'h3'   => 'H3',
					'h4'   => 'H4',
					'div'  => 'div',
					'span' => 'span',
				],
				'condition' => [ "{$prefix}visible" => 'yes' ],
			]
		);

		$this->add_control(
			"{$prefix}subtitle",
			[
				'label'       => esc_html__( 'Sous-titre descriptif', 'tools-adapter' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => $default_subtitle,
				'condition'   => [ "{$prefix}visible" => 'yes' ],
				'label_block' => true,
			]
		);

		$this->add_control(
			"{$prefix}price_type",
			[
				'label'       => esc_html__( 'Format du tarif', 'tools-adapter' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => $default_price_type,
				'options'     => [
					'amount' => esc_html__( 'Prix chiffré (ex: 465 € TTC)', 'tools-adapter' ),
					'text'   => esc_html__( 'Texte libre (ex: Sur Mesure)', 'tools-adapter' ),
				],
				'separator'   => 'before',
				'condition'   => [ "{$prefix}visible" => 'yes' ],
			]
		);

		$this->add_control(
			"{$prefix}price",
			[
				'label'     => esc_html__( 'Montant', 'tools-adapter' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => $default_price,
				'condition' => [
					"{$prefix}visible"    => 'yes',
					"{$prefix}price_type" => 'amount',
				],
			]
		);

		$this->add_control(
			"{$prefix}currency",
			[
				'label'     => esc_html__( 'Symbole de devise', 'tools-adapter' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => $default_currency,
				'condition' => [
					"{$prefix}visible"    => 'yes',
					"{$prefix}price_type" => 'amount',
				],
			]
		);

		$this->add_control(
			"{$prefix}period",
			[
				'label'     => esc_html__( 'Mention / Période (ex: TTC, /mois)', 'tools-adapter' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => $default_period,
				'condition' => [
					"{$prefix}visible"    => 'yes',
					"{$prefix}price_type" => 'amount',
				],
			]
		);

		$this->add_control(
			"{$prefix}custom_price",
			[
				'label'       => esc_html__( 'Texte libre du tarif', 'tools-adapter' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => $default_custom_price,
				'condition'   => [
					"{$prefix}visible"    => 'yes',
					"{$prefix}price_type" => 'text',
				],
				'label_block' => true,
			]
		);

		// Repeater des fonctionnalités
		$repeater = new Repeater();

		$repeater->add_control(
			'text',
			[
				'label'       => esc_html__( 'Intitulé de la prestation', 'tools-adapter' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Prestation incluse', 'tools-adapter' ),
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'included',
			[
				'label'        => esc_html__( 'Prestation incluse', 'tools-adapter' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'description'  => esc_html__( 'Si non incluse, affiche une croix et grise le texte.', 'tools-adapter' ),
			]
		);

		$repeater->add_control(
			'is_bold',
			[
				'label'        => esc_html__( 'Mettre en valeur (Gras)', 'tools-adapter' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
			]
		);

		$this->add_control(
			"{$prefix}features",
			[
				'label'       => esc_html__( 'Liste des prestations incluses / exclues', 'tools-adapter' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => $default_features,
				'title_field' => '{{{ text }}}',
				'separator'   => 'before',
				'condition'   => [ "{$prefix}visible" => 'yes' ],
			]
		);

		$this->add_control(
			"{$prefix}button_text",
			[
				'label'       => esc_html__( 'Texte du bouton CTA', 'tools-adapter' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => $default_btn_text,
				'separator'   => 'before',
				'condition'   => [ "{$prefix}visible" => 'yes' ],
				'label_block' => true,
			]
		);

		$this->add_control(
			"{$prefix}button_link",
			[
				'label'       => esc_html__( 'Lien de destination', 'tools-adapter' ),
				'type'        => Controls_Manager::URL,
				'default'     => [ 'url' => '#', 'is_external' => false, 'nofollow' => false ],
				'condition'   => [ "{$prefix}visible" => 'yes' ],
				'label_block' => true,
			]
		);

		$this->add_control(
			"{$prefix}button_style",
			[
				'label'     => esc_html__( 'Style du bouton', 'tools-adapter' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => $default_btn_style,
				'options'   => [
					'default' => esc_html__( 'Bouton Sombre (#152718)', 'tools-adapter' ),
					'primary' => esc_html__( 'Bouton Vert (#7bc81d)', 'tools-adapter' ),
				],
				'condition' => [ "{$prefix}visible" => 'yes' ],
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		// Récupérer les offres actives
		$active_plans = [];
		for ( $i = 1; $i <= 3; $i++ ) {
			if ( 'yes' === ( $settings["plan_{$i}_visible"] ?? 'yes' ) ) {
				$active_plans[] = $i;
			}
		}

		if ( empty( $active_plans ) ) {
			if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
				echo '<div class="ta-pricing-empty-notice" style="text-align:center; padding: 30px; background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 8px;">';
				echo esc_html__( 'Toutes les offres de cette grille sont actuellement masquées. Activez au moins une offre dans le panneau latéral pour afficher la grille.', 'tools-adapter' );
				echo '</div>';
			}
			return;
		}

		$count = count( $active_plans );
		$grid_class = "ta-pricing-grid ta-pricing-grid--count-{$count}";
		$scale_featured = 'yes' === ( $settings['card_featured_scale'] ?? 'yes' );
		?>
		<div class="ta-pricing-wrapper">
			<div class="<?php echo esc_attr( $grid_class ); ?>">
				<?php foreach ( $active_plans as $i ) :
					$prefix       = "plan_{$i}_";
					$featured     = 'yes' === ( $settings["{$prefix}featured"] ?? '' );
					$badge_text   = $settings["{$prefix}badge_text"] ?? '';
					$title        = $settings["{$prefix}title"] ?? '';
					$title_tag    = ! empty( $settings["{$prefix}title_tag"] ) ? $settings["{$prefix}title_tag"] : 'h3';
					$allowed_tags = [ 'h2', 'h3', 'h4', 'div', 'span' ];
					if ( ! in_array( $title_tag, $allowed_tags, true ) ) {
						$title_tag = 'h3';
					}
					$subtitle     = $settings["{$prefix}subtitle"] ?? '';
					$price_type   = $settings["{$prefix}price_type"] ?? 'amount';
					$price        = $settings["{$prefix}price"] ?? '';
					$currency     = $settings["{$prefix}currency"] ?? '€';
					$period       = $settings["{$prefix}period"] ?? 'TTC';
					$custom_price = $settings["{$prefix}custom_price"] ?? '';
					$features     = $settings["{$prefix}features"] ?? [];
					$btn_text     = $settings["{$prefix}button_text"] ?? '';
					$btn_link     = $settings["{$prefix}button_link"] ?? [];
					$btn_url      = ! empty( $btn_link['url'] ) ? $btn_link['url'] : '#';
					$btn_style    = $settings["{$prefix}button_style"] ?? 'default';

					$card_classes = [ 'ta-pricing-card' ];
					if ( $featured ) {
						$card_classes[] = 'is-featured';
						if ( $scale_featured ) {
							$card_classes[] = 'has-scale';
						}
					}
					?>
					<div class="<?php echo esc_attr( implode( ' ', $card_classes ) ); ?>">
						<?php if ( $featured && ! empty( $badge_text ) ) : ?>
							<span class="ta-pricing-badge"><?php echo esc_html( \tools_adapter_translate( $badge_text ) ); ?></span>
						<?php endif; ?>

						<div class="ta-pricing-header">
							<?php if ( ! empty( $title ) ) : ?>
								<<?php echo esc_attr( $title_tag ); ?> class="ta-pricing-title">
									<?php echo esc_html( \tools_adapter_translate( $title ) ); ?>
								</<?php echo esc_attr( $title_tag ); ?>>
							<?php endif; ?>

							<?php if ( ! empty( $subtitle ) ) : ?>
								<p class="ta-pricing-subtitle"><?php echo esc_html( \tools_adapter_translate( $subtitle ) ); ?></p>
							<?php endif; ?>

							<div class="ta-pricing-amount-box">
								<?php if ( 'amount' === $price_type ) : ?>
									<div class="ta-pricing-amount">
										<?php if ( ! empty( $price ) ) : ?>
											<span class="ta-pricing-val"><?php echo esc_html( $price ); ?></span>
										<?php endif; ?>
										<?php if ( ! empty( $currency ) ) : ?>
											<span class="ta-pricing-cur"><?php echo esc_html( $currency ); ?></span>
										<?php endif; ?>
										<?php if ( ! empty( $period ) ) : ?>
											<span class="ta-pricing-period"><?php echo esc_html( \tools_adapter_translate( $period ) ); ?></span>
										<?php endif; ?>
									</div>
								<?php else : ?>
									<div class="ta-pricing-amount ta-pricing-amount--custom">
										<span class="ta-pricing-val"><?php echo esc_html( \tools_adapter_translate( $custom_price ) ); ?></span>
									</div>
								<?php endif; ?>
							</div>
						</div>

						<?php if ( ! empty( $features ) && is_array( $features ) ) : ?>
							<ul class="ta-pricing-features">
								<?php foreach ( $features as $feat ) :
									$is_included = 'yes' === ( $feat['included'] ?? 'yes' );
									$is_bold     = 'yes' === ( $feat['is_bold'] ?? '' );
									$text        = \tools_adapter_translate( $feat['text'] ?? '' );

									$item_classes = [ 'ta-pricing-feature-item' ];
									if ( ! $is_included ) {
										$item_classes[] = 'is-disabled';
									}
									?>
									<li class="<?php echo esc_attr( implode( ' ', $item_classes ) ); ?>">
										<span class="ta-pricing-feature-icon" aria-hidden="true">
											<?php echo $is_included ? '✓' : '✕'; ?>
										</span>
										<span class="ta-pricing-feature-text<?php echo $is_bold ? ' is-bold' : ''; ?>">
											<?php echo wp_kses_post( $text ); ?>
										</span>
									</li>
								<?php endforeach; ?>
							</ul>
						<?php endif; ?>

						<?php if ( ! empty( $btn_text ) ) : ?>
							<div class="ta-pricing-footer">
								<a class="ta-pricing-btn ta-pricing-btn--<?php echo esc_attr( $btn_style ); ?>"
								   href="<?php echo esc_url( $btn_url ); ?>"
								   <?php echo ! empty( $btn_link['is_external'] ) ? ' target="_blank"' : ''; ?>
								   <?php echo ! empty( $btn_link['nofollow'] ) ? ' rel="nofollow"' : ''; ?>>
									<?php echo esc_html( \tools_adapter_translate( $btn_text ) ); ?>
								</a>
							</div>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
			</div>

			<?php if ( 'yes' === ( $settings['show_guarantee'] ?? 'yes' ) && ( ! empty( $settings['guarantee_title'] ) || ! empty( $settings['guarantee_text'] ) ) ) : ?>
				<div class="ta-pricing-guarantee">
					<span class="ta-pricing-guarantee__icon" aria-hidden="true">
						<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
							<path d="M14 9a2 2 0 0 1-2 2H6l-4 4V4c0-1.1.9-2 2-2h8a2 2 0 0 1 2 2v5Z"/>
							<path d="M18 9h2a2 2 0 0 1 2 2v11l-4-4h-6a2 2 0 0 1-2-2v-1"/>
						</svg>
					</span>
					<div class="ta-pricing-guarantee__content">
						<?php if ( ! empty( $settings['guarantee_title'] ) ) : ?>
							<strong><?php echo esc_html( \tools_adapter_translate( $settings['guarantee_title'] ) ); ?></strong>
						<?php endif; ?>
						<span><?php echo esc_html( \tools_adapter_translate( $settings['guarantee_text'] ) ); ?></span>
					</div>
				</div>
			<?php endif; ?>
		</div>
		<?php
	}
}
