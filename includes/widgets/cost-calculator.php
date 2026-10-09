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
 * Widget: Simulateur de tarifs & devis interactif (ex: Bois de chauffage, matériaux au volume).
 * Permet un calcul de commande en temps réel avec essence/variante, longueur/taille, curseur de volume et total TTC instantané.
 *
 * Les couleurs par défaut viennent du thème (sombre / clair) défini dans
 * cost-calculator.css : les contrôles de couleur n'ont pas de valeur par
 * défaut, ils ne font que surcharger ce thème.
 */
class Cost_Calculator extends Base_Widget {

	/**
	 * Legacy grid (mode « Grille ») : longueur => [ clé de réglage, libellé, prix par défaut hêtre, mélange ].
	 */
	const LEGACY_LENGTHS = [
		'25'  => [ '25 cm', 97, 92 ],
		'33'  => [ '33 cm', 93, 88 ],
		'50'  => [ '50 cm', 88, 83 ],
		'100' => [ '1 m', 77, 73 ],
	];

	public function get_name() {
		return 'tools-adapter-cost-calculator';
	}

	public function get_title() {
		return esc_html__( 'Simulateur de Devis / Bois', 'tools-adapter' );
	}

	public function get_icon() {
		return 'eicon-calculator';
	}

	public function get_categories() {
		return [ 'tools-adapter' ];
	}

	public function get_keywords() {
		return [ 'simulateur', 'calculateur', 'bois', 'chauffage', 'stère', 'devis', 'prix', 'volume', 'calculator' ];
	}

	public function get_style_depends() {
		return [ 'tools-adapter-cost-calculator' ];
	}

	public function get_script_depends() {
		return [ 'tools-adapter-cost-calculator' ];
	}

	/**
	 * Shorthand for a DIMENSIONS padding control.
	 *
	 * @param string $id       Control id.
	 * @param string $label    Label.
	 * @param string $selector CSS selector (without {{WRAPPER}}).
	 */
	private function add_padding_control( $id, $label, $selector ) {
		$this->add_responsive_control( $id, [ 'label' => $label, 'type' => Controls_Manager::DIMENSIONS, 'size_units' => [ 'px', 'em' ], 'selectors' => [ '{{WRAPPER}} ' . $selector => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ] ] );
	}

	/**
	 * Shorthand for a radius SLIDER control.
	 *
	 * @param string $id       Control id.
	 * @param string $selector CSS selector (without {{WRAPPER}}).
	 * @param int    $max      Max value.
	 */
	private function add_radius_control( $id, $selector, $max = 40 ) {
		$this->add_responsive_control( $id, [ 'label' => esc_html__( 'Arrondi', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'size_units' => [ 'px', '%' ], 'range' => [ 'px' => [ 'min' => 0, 'max' => $max ] ], 'selectors' => [ '{{WRAPPER}} ' . $selector => 'border-radius: {{SIZE}}{{UNIT}};' ] ] );
	}

	protected function register_controls() {

		// ==========================================
		// CONTENU : EN-TÊTE
		// ==========================================
		$this->start_controls_section( 'section_header', [ 'label' => esc_html__( 'En-tête & Textes', 'tools-adapter' ) ] );

		$this->add_control( 'badge', [ 'label' => esc_html__( 'Badge sur-titre', 'tools-adapter' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'Calculateur interactif', 'tools-adapter' ) ] );
		$this->add_control( 'title', [ 'label' => esc_html__( 'Titre du simulateur', 'tools-adapter' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'Estimez votre commande de bois', 'tools-adapter' ), 'label_block' => true ] );
		$this->add_control( 'title_tag', [ 'label' => esc_html__( 'Balise du titre', 'tools-adapter' ), 'type' => Controls_Manager::SELECT, 'default' => 'h3', 'options' => [ 'h2' => 'H2', 'h3' => 'H3', 'h4' => 'H4', 'div' => 'div' ] ] );
		$this->add_control( 'title_icon', [ 'label' => esc_html__( 'Icône du titre', 'tools-adapter' ), 'type' => Controls_Manager::ICONS ] );
		$this->add_control( 'description', [ 'label' => esc_html__( 'Description', 'tools-adapter' ), 'type' => Controls_Manager::TEXTAREA, 'rows' => 2, 'default' => esc_html__( 'Choisissez votre essence, la longueur des bûches et la quantité souhaitée pour obtenir votre estimation instantanée.', 'tools-adapter' ) ] );

		$this->add_control( 'step_labels_heading', [ 'label' => esc_html__( 'Intitulés des étapes', 'tools-adapter' ), 'type' => Controls_Manager::HEADING, 'separator' => 'before' ] );
		$this->add_control( 'label_essence', [ 'label' => esc_html__( 'Essence', 'tools-adapter' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( '1. Essence de bois :', 'tools-adapter' ), 'label_block' => true ] );
		$this->add_control( 'label_length', [ 'label' => esc_html__( 'Longueur', 'tools-adapter' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( '2. Longueur des bûches :', 'tools-adapter' ), 'label_block' => true ] );
		$this->add_control( 'label_volume', [ 'label' => esc_html__( 'Quantité', 'tools-adapter' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( '3. Quantité souhaitée :', 'tools-adapter' ), 'label_block' => true ] );

		$this->end_controls_section();

		// ==========================================
		// CONTENU : TARIFS
		// ==========================================
		$this->start_controls_section( 'section_pricing', [ 'label' => esc_html__( 'Grille des tarifs au stère', 'tools-adapter' ) ] );

		$this->add_control(
			'pricing_mode',
			[
				'label'       => esc_html__( 'Mode de saisie', 'tools-adapter' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'grid',
				'options'     => [
					'grid' => esc_html__( 'Grille fixe (2 essences × 4 longueurs)', 'tools-adapter' ),
					'list' => esc_html__( 'Liste libre (essences et longueurs au choix)', 'tools-adapter' ),
				],
				'description' => esc_html__( 'En liste libre, chaque ligne est une combinaison essence + longueur + prix ; les choix proposés sont construits automatiquement dans l\'ordre des lignes.', 'tools-adapter' ),
			]
		);

		// Mode « Grille » (historique, conservé pour les pages existantes).
		$this->add_control( 'heading_wood_hetre', [ 'label' => esc_html__( 'Essence 1', 'tools-adapter' ), 'type' => Controls_Manager::HEADING, 'separator' => 'before', 'condition' => [ 'pricing_mode' => 'grid' ] ] );
		$this->add_control( 'label_hetre', [ 'label' => esc_html__( 'Libellé', 'tools-adapter' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'Hêtre (100% dur)', 'tools-adapter' ), 'condition' => [ 'pricing_mode' => 'grid' ] ] );
		foreach ( self::LEGACY_LENGTHS as $key => $data ) {
			/* translators: %s: log length (e.g. "33 cm"). */
			$this->add_control( 'price_hetre_' . $key, [ 'label' => sprintf( esc_html__( 'Prix %s', 'tools-adapter' ), $data[0] ), 'type' => Controls_Manager::NUMBER, 'default' => $data[1], 'step' => 0.01, 'condition' => [ 'pricing_mode' => 'grid' ] ] );
		}
		$this->add_control( 'heading_wood_melange', [ 'label' => esc_html__( 'Essence 2', 'tools-adapter' ), 'type' => Controls_Manager::HEADING, 'separator' => 'before', 'condition' => [ 'pricing_mode' => 'grid' ] ] );
		$this->add_control( 'label_melange', [ 'label' => esc_html__( 'Libellé', 'tools-adapter' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'Mélange dur (Chêne, Charme...)', 'tools-adapter' ), 'condition' => [ 'pricing_mode' => 'grid' ] ] );
		foreach ( self::LEGACY_LENGTHS as $key => $data ) {
			/* translators: %s: log length (e.g. "33 cm"). */
			$this->add_control( 'price_melange_' . $key, [ 'label' => sprintf( esc_html__( 'Prix %s', 'tools-adapter' ), $data[0] ), 'type' => Controls_Manager::NUMBER, 'default' => $data[2], 'step' => 0.01, 'condition' => [ 'pricing_mode' => 'grid' ] ] );
		}

		// Mode « Liste libre ».
		$rates = new Repeater();
		$rates->add_control( 'essence', [ 'label' => esc_html__( 'Essence / variante', 'tools-adapter' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'Hêtre', 'tools-adapter' ) ] );
		$rates->add_control( 'length', [ 'label' => esc_html__( 'Longueur / taille', 'tools-adapter' ), 'type' => Controls_Manager::TEXT, 'default' => '33 cm' ] );
		$rates->add_control( 'price', [ 'label' => esc_html__( 'Prix unitaire', 'tools-adapter' ), 'type' => Controls_Manager::NUMBER, 'default' => 100, 'step' => 0.01 ] );
		$this->add_control(
			'rates',
			[
				'label'       => esc_html__( 'Tarifs', 'tools-adapter' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $rates->get_controls(),
				'title_field' => '{{{ essence }}} — {{{ length }}} : {{{ price }}}',
				'separator'   => 'before',
				'condition'   => [ 'pricing_mode' => 'list' ],
				'default'     => [
					[ 'essence' => esc_html__( 'Hêtre', 'tools-adapter' ), 'length' => '25 cm', 'price' => 135 ],
					[ 'essence' => esc_html__( 'Hêtre', 'tools-adapter' ), 'length' => '33 cm', 'price' => 130 ],
					[ 'essence' => esc_html__( 'Hêtre', 'tools-adapter' ), 'length' => '50 cm', 'price' => 130 ],
					[ 'essence' => esc_html__( 'Hêtre', 'tools-adapter' ), 'length' => '1 m', 'price' => 115 ],
					[ 'essence' => esc_html__( 'Mélange', 'tools-adapter' ), 'length' => '25 cm', 'price' => 120 ],
					[ 'essence' => esc_html__( 'Mélange', 'tools-adapter' ), 'length' => '33 cm', 'price' => 115 ],
					[ 'essence' => esc_html__( 'Mélange', 'tools-adapter' ), 'length' => '50 cm', 'price' => 115 ],
					[ 'essence' => esc_html__( 'Mélange', 'tools-adapter' ), 'length' => '1 m', 'price' => 105 ],
				],
			]
		);

		$this->add_control( 'default_essence', [ 'label' => esc_html__( 'Essence sélectionnée au départ', 'tools-adapter' ), 'type' => Controls_Manager::TEXT, 'placeholder' => esc_html__( 'La première', 'tools-adapter' ), 'description' => esc_html__( 'Libellé exact ; vide = la première.', 'tools-adapter' ), 'separator' => 'before' ] );
		$this->add_control( 'default_length', [ 'label' => esc_html__( 'Longueur sélectionnée au départ', 'tools-adapter' ), 'type' => Controls_Manager::TEXT, 'default' => '33 cm', 'description' => esc_html__( 'Libellé exact ; si absent, la première disponible.', 'tools-adapter' ) ] );

		$this->add_control( 'currency', [ 'label' => esc_html__( 'Devise', 'tools-adapter' ), 'type' => Controls_Manager::TEXT, 'default' => '€', 'separator' => 'before' ] );
		$this->add_control( 'currency_position', [ 'label' => esc_html__( 'Position de la devise', 'tools-adapter' ), 'type' => Controls_Manager::SELECT, 'default' => 'after', 'options' => [ 'after' => esc_html__( 'Après (130 €)', 'tools-adapter' ), 'before' => esc_html__( 'Avant ($130)', 'tools-adapter' ) ] ] );
		$this->add_control( 'tax_label', [ 'label' => esc_html__( 'Mention de taxe', 'tools-adapter' ), 'type' => Controls_Manager::TEXT, 'default' => 'TTC' ] );

		$this->end_controls_section();

		// ==========================================
		// CONTENU : AFFICHAGE DES CHOIX
		// ==========================================
		$this->start_controls_section( 'section_choices', [ 'label' => esc_html__( 'Affichage des choix', 'tools-adapter' ) ] );

		$display_options = [
			'buttons' => esc_html__( 'Boutons', 'tools-adapter' ),
			'select'  => esc_html__( 'Liste déroulante', 'tools-adapter' ),
		];
		$this->add_control( 'essence_display', [ 'label' => esc_html__( 'Essences', 'tools-adapter' ), 'type' => Controls_Manager::SELECT, 'default' => 'buttons', 'options' => $display_options ] );
		$this->add_responsive_control( 'essence_columns', [ 'label' => esc_html__( 'Boutons par ligne (essences)', 'tools-adapter' ), 'type' => Controls_Manager::NUMBER, 'min' => 1, 'max' => 6, 'selectors' => [ '{{WRAPPER}} .ta-calculator__pills' => 'grid-template-columns: repeat({{VALUE}}, minmax(0, 1fr));' ], 'condition' => [ 'essence_display' => 'buttons' ] ] );
		$this->add_control( 'length_display', [ 'label' => esc_html__( 'Longueurs', 'tools-adapter' ), 'type' => Controls_Manager::SELECT, 'default' => 'buttons', 'options' => $display_options, 'separator' => 'before' ] );
		$this->add_responsive_control( 'length_columns', [ 'label' => esc_html__( 'Boutons par ligne (longueurs)', 'tools-adapter' ), 'type' => Controls_Manager::NUMBER, 'min' => 1, 'max' => 8, 'selectors' => [ '{{WRAPPER}} .ta-calculator__radios' => 'grid-template-columns: repeat({{VALUE}}, minmax(0, 1fr));' ], 'condition' => [ 'length_display' => 'buttons' ] ] );

		$this->end_controls_section();

		// ==========================================
		// CONTENU : QUANTITÉ, TOTAL & ACTION
		// ==========================================
		$this->start_controls_section( 'section_slider_settings', [ 'label' => esc_html__( 'Quantité & Action', 'tools-adapter' ) ] );

		$this->add_control( 'volume_min', [ 'label' => esc_html__( 'Volume minimum', 'tools-adapter' ), 'type' => Controls_Manager::NUMBER, 'default' => 1 ] );
		$this->add_control( 'volume_max', [ 'label' => esc_html__( 'Volume maximum', 'tools-adapter' ), 'type' => Controls_Manager::NUMBER, 'default' => 30 ] );
		$this->add_control( 'volume_step', [ 'label' => esc_html__( 'Pas', 'tools-adapter' ), 'type' => Controls_Manager::NUMBER, 'default' => 1, 'min' => 0.1, 'step' => 0.1, 'description' => esc_html__( 'Ex. 0,5 pour proposer des demi-stères.', 'tools-adapter' ) ] );
		$this->add_control( 'volume_default', [ 'label' => esc_html__( 'Volume par défaut', 'tools-adapter' ), 'type' => Controls_Manager::NUMBER, 'default' => 6 ] );
		$this->add_control( 'volume_unit', [ 'label' => esc_html__( 'Unité de mesure', 'tools-adapter' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'stères', 'tools-adapter' ) ] );
		$this->add_control( 'volume_display', [ 'label' => esc_html__( 'Affichage de la quantité', 'tools-adapter' ), 'type' => Controls_Manager::SELECT, 'default' => 'text', 'options' => [ 'text' => esc_html__( 'Texte', 'tools-adapter' ), 'pill' => esc_html__( 'Pastille', 'tools-adapter' ) ] ] );
		$this->add_control( 'volume_position', [ 'label' => esc_html__( 'Position de la quantité', 'tools-adapter' ), 'type' => Controls_Manager::SELECT, 'default' => 'header', 'options' => [ 'header' => esc_html__( 'À droite de l\'intitulé', 'tools-adapter' ), 'slider' => esc_html__( 'À droite du curseur', 'tools-adapter' ) ] ] );
		$this->add_control( 'show_range_hints', [ 'label' => esc_html__( 'Afficher le minimum et le maximum', 'tools-adapter' ), 'type' => Controls_Manager::SWITCHER, 'return_value' => 'yes', 'default' => 'yes' ] );

		$this->add_control(
			'summary_layout',
			[
				'label'     => esc_html__( 'Disposition du total', 'tools-adapter' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'split',
				'separator' => 'before',
				'options'   => [
					'split'    => esc_html__( 'Prix unitaire + total côte à côte', 'tools-adapter' ),
					'centered' => esc_html__( 'Grand total centré + détail', 'tools-adapter' ),
				],
			]
		);
		$this->add_control( 'unit_price_label', [ 'label' => esc_html__( 'Libellé du prix unitaire', 'tools-adapter' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'Prix unitaire au stère :', 'tools-adapter' ), 'condition' => [ 'summary_layout' => 'split' ] ] );
		$this->add_control( 'total_label', [ 'label' => esc_html__( 'Libellé du total', 'tools-adapter' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'Total estimé :', 'tools-adapter' ), 'condition' => [ 'summary_layout' => 'split' ] ] );
		$this->add_control( 'centered_total_label', [ 'label' => esc_html__( 'Libellé du total', 'tools-adapter' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'Total estimé TTC', 'tools-adapter' ), 'condition' => [ 'summary_layout' => 'centered' ] ] );
		$this->add_control( 'detail_text', [ 'label' => esc_html__( 'Ligne de détail', 'tools-adapter' ), 'type' => Controls_Manager::TEXT, 'default' => '{prix} € / stère × {volume}', 'label_block' => true, 'description' => esc_html__( 'Variables : {prix} {volume} {unite} {total} {essence} {longueur}.', 'tools-adapter' ), 'condition' => [ 'summary_layout' => 'centered' ] ] );

		$this->add_control( 'notice_text', [ 'label' => esc_html__( 'Mention légale / info sous total', 'tools-adapter' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( '* Tarifs indicatifs TTC au stère. Livraison en sus selon commune.', 'tools-adapter' ), 'separator' => 'before', 'label_block' => true ] );
		$this->add_control( 'cta_text', [ 'label' => esc_html__( 'Texte du bouton CTA', 'tools-adapter' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'Commander ces stères', 'tools-adapter' ) ] );
		$this->add_control( 'cta_link', [ 'label' => esc_html__( 'Lien du bouton (ex: #devis ou /contact)', 'tools-adapter' ), 'type' => Controls_Manager::URL, 'default' => [ 'url' => '#devis' ] ] );
		$this->add_control( 'cta_icon', [ 'label' => esc_html__( 'Icône du bouton', 'tools-adapter' ), 'type' => Controls_Manager::ICONS, 'description' => esc_html__( 'Vide = flèche par défaut.', 'tools-adapter' ) ] );
		$this->add_control( 'cta_icon_position', [ 'label' => esc_html__( 'Position de l\'icône', 'tools-adapter' ), 'type' => Controls_Manager::SELECT, 'default' => 'after', 'options' => [ 'before' => esc_html__( 'Avant le texte', 'tools-adapter' ), 'after' => esc_html__( 'Après le texte', 'tools-adapter' ), 'none' => esc_html__( 'Aucune icône', 'tools-adapter' ) ] ] );
		$this->add_control( 'prefill_form', [ 'label' => esc_html__( 'Pré-remplir le formulaire de contact', 'tools-adapter' ), 'type' => Controls_Manager::SWITCHER, 'return_value' => 'yes', 'default' => 'yes', 'description' => esc_html__( 'Si le lien est une ancre (#devis) vers un formulaire, le message est pré-rempli avec la commande.', 'tools-adapter' ) ] );
		$this->add_control( 'prefill_message', [ 'label' => esc_html__( 'Message pré-rempli', 'tools-adapter' ), 'type' => Controls_Manager::TEXTAREA, 'rows' => 4, 'default' => esc_html__( 'Bonjour, je souhaite commander {volume} {unite} de bois de chauffage ({essence}, longueur {longueur}) pour un total estimé à {total} € TTC. Merci de me contacter pour convenir de la date de livraison.', 'tools-adapter' ), 'description' => esc_html__( 'Variables : {prix} {volume} {unite} {total} {essence} {longueur}.', 'tools-adapter' ), 'condition' => [ 'prefill_form' => 'yes' ] ] );
		$this->add_control( 'prefill_service', [ 'label' => esc_html__( 'Prestation à sélectionner dans le formulaire', 'tools-adapter' ), 'type' => Controls_Manager::TEXT, 'default' => 'bois', 'description' => esc_html__( 'Mot recherché dans la liste « Prestation » du formulaire (vide = ne rien sélectionner).', 'tools-adapter' ), 'condition' => [ 'prefill_form' => 'yes' ] ] );

		$this->end_controls_section();

		// ==========================================
		// STYLE 1 : CARTE GLOBALE
		// ==========================================
		$this->start_controls_section( 'section_style_box', [ 'label' => esc_html__( 'Boîte & Conteneur', 'tools-adapter' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_control(
			'theme',
			[
				'label'        => esc_html__( 'Thème', 'tools-adapter' ),
				'type'         => Controls_Manager::SELECT,
				'default'      => 'dark',
				'options'      => [
					'dark'  => esc_html__( 'Sombre', 'tools-adapter' ),
					'light' => esc_html__( 'Clair', 'tools-adapter' ),
				],
				'prefix_class' => 'ta-calc--theme-',
				'description'  => esc_html__( 'Base de couleurs ; tous les réglages ci-dessous la remplacent.', 'tools-adapter' ),
			]
		);
		$this->add_control( 'card_bg', [ 'label' => esc_html__( 'Couleur de fond', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .ta-calculator' => 'background: {{VALUE}};' ] ] );
		$this->add_control( 'accent_color', [ 'label' => esc_html__( 'Couleur d\'accent', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .ta-calculator' => '--ta-calc-accent: {{VALUE}};' ] ] );
		$this->add_responsive_control( 'card_padding', [ 'label' => esc_html__( 'Espacement interne (padding)', 'tools-adapter' ), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => [ 'px', 'em' ], 'selectors' => [ '{{WRAPPER}} .ta-calculator' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ] ] );
		$this->add_group_control( Group_Control_Border::get_type(), [ 'name' => 'card_border', 'selector' => '{{WRAPPER}} .ta-calculator' ] );
		$this->add_responsive_control( 'card_radius', [ 'label' => esc_html__( 'Arrondi des angles (px)', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'range' => [ 'px' => [ 'min' => 0, 'max' => 40 ] ], 'selectors' => [ '{{WRAPPER}} .ta-calculator' => 'border-radius: {{SIZE}}{{UNIT}};' ] ] );
		$this->add_group_control( Group_Control_Box_Shadow::get_type(), [ 'name' => 'card_shadow', 'selector' => '{{WRAPPER}} .ta-calculator' ] );
		$this->add_responsive_control( 'group_spacing', [ 'label' => esc_html__( 'Espace entre les étapes', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'range' => [ 'px' => [ 'min' => 0, 'max' => 60 ] ], 'separator' => 'before', 'selectors' => [ '{{WRAPPER}} .ta-calculator__group' => 'margin-bottom: {{SIZE}}{{UNIT}};' ] ] );

		$this->end_controls_section();

		// ==========================================
		// STYLE 2 : EN-TÊTE & TEXTES
		// ==========================================
		$this->start_controls_section( 'section_style_header', [ 'label' => esc_html__( 'En-tête & Textes', 'tools-adapter' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_responsive_control( 'header_spacing', [ 'label' => esc_html__( 'Espace sous l\'en-tête', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'range' => [ 'px' => [ 'min' => 0, 'max' => 60 ] ], 'selectors' => [ '{{WRAPPER}} .ta-calculator__header' => 'margin-bottom: {{SIZE}}{{UNIT}};' ] ] );

		$this->add_control( 'heading_badge_style', [ 'label' => esc_html__( 'Badge sur-titre', 'tools-adapter' ), 'type' => Controls_Manager::HEADING, 'separator' => 'before' ] );
		$this->add_control( 'badge_color', [ 'label' => esc_html__( 'Couleur texte', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .ta-calculator__badge' => 'color: {{VALUE}};' ] ] );
		$this->add_control( 'badge_bg', [ 'label' => esc_html__( 'Fond', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .ta-calculator__badge' => 'background-color: {{VALUE}};' ] ] );
		$this->add_control( 'badge_border', [ 'label' => esc_html__( 'Bordure', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .ta-calculator__badge' => 'border-color: {{VALUE}};' ] ] );
		$this->add_group_control( Group_Control_Typography::get_type(), [ 'name' => 'badge_typography', 'selector' => '{{WRAPPER}} .ta-calculator__badge' ] );
		$this->add_radius_control( 'badge_radius', '.ta-calculator__badge' );

		$this->add_control( 'heading_title_style', [ 'label' => esc_html__( 'Titre', 'tools-adapter' ), 'type' => Controls_Manager::HEADING, 'separator' => 'before' ] );
		$this->add_control( 'title_color', [ 'label' => esc_html__( 'Couleur', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .ta-calculator__title' => 'color: {{VALUE}};' ] ] );
		$this->add_group_control( Group_Control_Typography::get_type(), [ 'name' => 'title_typography', 'selector' => '{{WRAPPER}} .ta-calculator__title' ] );
		$this->add_control( 'title_icon_color', [ 'label' => esc_html__( 'Couleur de l\'icône', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .ta-calculator__title-icon' => 'color: {{VALUE}};', '{{WRAPPER}} .ta-calculator__title-icon svg' => 'fill: {{VALUE}};' ] ] );
		$this->add_control( 'title_icon_size', [ 'label' => esc_html__( 'Taille de l\'icône', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'range' => [ 'px' => [ 'min' => 10, 'max' => 60 ] ], 'selectors' => [ '{{WRAPPER}} .ta-calculator__title-icon' => 'font-size: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .ta-calculator__title-icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};' ] ] );
		$this->add_control( 'title_icon_gap', [ 'label' => esc_html__( 'Espace icône / titre', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'range' => [ 'px' => [ 'min' => 0, 'max' => 30 ] ], 'selectors' => [ '{{WRAPPER}} .ta-calculator__title' => 'gap: {{SIZE}}{{UNIT}};' ] ] );

		$this->add_control( 'heading_desc_style', [ 'label' => esc_html__( 'Description', 'tools-adapter' ), 'type' => Controls_Manager::HEADING, 'separator' => 'before' ] );
		$this->add_control( 'desc_color', [ 'label' => esc_html__( 'Couleur', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .ta-calculator__desc' => 'color: {{VALUE}};' ] ] );
		$this->add_group_control( Group_Control_Typography::get_type(), [ 'name' => 'desc_typography', 'selector' => '{{WRAPPER}} .ta-calculator__desc' ] );

		$this->add_control( 'heading_labels_style', [ 'label' => esc_html__( 'Intitulés des étapes', 'tools-adapter' ), 'type' => Controls_Manager::HEADING, 'separator' => 'before' ] );
		$this->add_control( 'label_color', [ 'label' => esc_html__( 'Couleur des libellés', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .ta-calculator__label' => 'color: {{VALUE}};' ] ] );
		$this->add_group_control( Group_Control_Typography::get_type(), [ 'name' => 'label_typography', 'selector' => '{{WRAPPER}} .ta-calculator__label' ] );
		$this->add_responsive_control( 'label_spacing', [ 'label' => esc_html__( 'Espace sous l\'intitulé', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'range' => [ 'px' => [ 'min' => 0, 'max' => 30 ] ], 'selectors' => [ '{{WRAPPER}} .ta-calculator__label, {{WRAPPER}} .ta-calculator__vol-header' => 'margin-bottom: {{SIZE}}{{UNIT}};' ] ] );

		$this->end_controls_section();

		// ==========================================
		// STYLE 3 : BOUTONS DE SÉLECTION
		// ==========================================
		$this->start_controls_section( 'section_style_pills', [ 'label' => esc_html__( 'Boutons de sélection (Essences & Tailles)', 'tools-adapter' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$choice = '{{WRAPPER}} .ta-calculator__pill span, {{WRAPPER}} .ta-calculator__radio span';
		$this->add_group_control( Group_Control_Typography::get_type(), [ 'name' => 'pills_typography', 'selector' => $choice ] );
		$this->add_responsive_control( 'pill_padding', [ 'label' => esc_html__( 'Espacement interne', 'tools-adapter' ), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => [ 'px', 'em' ], 'selectors' => [ '{{WRAPPER}} .ta-calculator__pill span, {{WRAPPER}} .ta-calculator__radio span' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ] ] );
		$this->add_responsive_control( 'pill_gap', [ 'label' => esc_html__( 'Espace entre boutons', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'range' => [ 'px' => [ 'min' => 0, 'max' => 30 ] ], 'selectors' => [ '{{WRAPPER}} .ta-calculator__pills, {{WRAPPER}} .ta-calculator__radios' => 'gap: {{SIZE}}{{UNIT}};' ] ] );
		$this->add_responsive_control( 'pill_radius', [ 'label' => esc_html__( 'Arrondi des boutons (px)', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'range' => [ 'px' => [ 'min' => 0, 'max' => 30 ] ], 'selectors' => [ $choice => 'border-radius: {{SIZE}}{{UNIT}};' ] ] );
		$this->add_control( 'pill_border_width', [ 'label' => esc_html__( 'Épaisseur de bordure', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'range' => [ 'px' => [ 'min' => 0, 'max' => 4 ] ], 'selectors' => [ $choice => 'border-width: {{SIZE}}{{UNIT}};' ] ] );

		$this->start_controls_tabs( 'pill_state_tabs' );
		$this->start_controls_tab( 'pill_state_normal', [ 'label' => esc_html__( 'Normal', 'tools-adapter' ) ] );
		$this->add_control( 'pill_bg', [ 'label' => esc_html__( 'Fond inactif', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'selectors' => [ $choice => 'background-color: {{VALUE}};' ] ] );
		$this->add_control( 'pill_text_color', [ 'label' => esc_html__( 'Texte inactif', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'selectors' => [ $choice => 'color: {{VALUE}};' ] ] );
		$this->add_control( 'pill_border_color', [ 'label' => esc_html__( 'Bordure inactive', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'selectors' => [ $choice => 'border-color: {{VALUE}};' ] ] );
		$this->end_controls_tab();
		$this->start_controls_tab( 'pill_state_hover', [ 'label' => esc_html__( 'Survol', 'tools-adapter' ) ] );
		$hover = '{{WRAPPER}} .ta-calculator__pill:hover input:not(:checked) + span, {{WRAPPER}} .ta-calculator__radio:hover input:not(:checked):not(:disabled) + span';
		$this->add_control( 'pill_bg_hover', [ 'label' => esc_html__( 'Fond', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'selectors' => [ $hover => 'background-color: {{VALUE}};' ] ] );
		$this->add_control( 'pill_text_hover', [ 'label' => esc_html__( 'Texte', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'selectors' => [ $hover => 'color: {{VALUE}};' ] ] );
		$this->add_control( 'pill_border_hover', [ 'label' => esc_html__( 'Bordure', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'selectors' => [ $hover => 'border-color: {{VALUE}};' ] ] );
		$this->end_controls_tab();
		$this->start_controls_tab( 'pill_state_active', [ 'label' => esc_html__( 'Actif', 'tools-adapter' ) ] );
		$active = '{{WRAPPER}} .ta-calculator__pill input:checked + span, {{WRAPPER}} .ta-calculator__radio input:checked + span';
		$this->add_control( 'pill_active_bg', [ 'label' => esc_html__( 'Fond actif', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'selectors' => [ $active => 'background-color: {{VALUE}};' ] ] );
		$this->add_control( 'pill_active_text_color', [ 'label' => esc_html__( 'Texte actif', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'selectors' => [ $active => 'color: {{VALUE}};' ] ] );
		$this->add_control( 'pill_active_border_color', [ 'label' => esc_html__( 'Bordure active', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'selectors' => [ $active => 'border-color: {{VALUE}};' ] ] );
		$this->add_group_control( Group_Control_Box_Shadow::get_type(), [ 'name' => 'pill_active_shadow', 'selector' => '{{WRAPPER}} .ta-calculator__pill input:checked + span, {{WRAPPER}} .ta-calculator__radio input:checked + span' ] );
		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_control( 'pill_disabled_opacity', [ 'label' => esc_html__( 'Opacité d\'une longueur indisponible', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'range' => [ 'px' => [ 'min' => 0, 'max' => 1, 'step' => 0.05 ] ], 'separator' => 'before', 'selectors' => [ '{{WRAPPER}} .ta-calculator__radio input:disabled + span' => 'opacity: {{SIZE}};' ] ] );

		$this->end_controls_section();

		// ==========================================
		// STYLE 4 : LISTES DÉROULANTES
		// ==========================================
		$this->start_controls_section( 'section_style_select', [ 'label' => esc_html__( 'Listes déroulantes', 'tools-adapter' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_group_control( Group_Control_Typography::get_type(), [ 'name' => 'select_typography', 'selector' => '{{WRAPPER}} .ta-calculator__select' ] );
		$this->add_control( 'select_bg', [ 'label' => esc_html__( 'Fond', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .ta-calculator__select' => 'background-color: {{VALUE}};' ] ] );
		$this->add_control( 'select_color', [ 'label' => esc_html__( 'Texte', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .ta-calculator__select' => 'color: {{VALUE}};' ] ] );
		$this->add_control( 'select_border', [ 'label' => esc_html__( 'Bordure', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .ta-calculator__select' => 'border-color: {{VALUE}};' ] ] );
		$this->add_control( 'select_border_focus', [ 'label' => esc_html__( 'Bordure (focus)', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .ta-calculator__select:focus' => 'border-color: {{VALUE}};' ] ] );
		$this->add_control( 'select_arrow', [ 'label' => esc_html__( 'Couleur de la flèche', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .ta-calculator__select-wrap' => '--ta-calc-select-arrow: {{VALUE}};' ] ] );
		$this->add_padding_control( 'select_padding', esc_html__( 'Espacement interne', 'tools-adapter' ), '.ta-calculator__select' );
		$this->add_radius_control( 'select_radius', '.ta-calculator__select', 30 );

		$this->end_controls_section();

		// ==========================================
		// STYLE 5 : CURSEUR DE QUANTITÉ
		// ==========================================
		$this->start_controls_section( 'section_style_range', [ 'label' => esc_html__( 'Curseur de quantité', 'tools-adapter' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_control( 'range_track', [ 'label' => esc_html__( 'Couleur de la piste', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .ta-calculator' => '--ta-calc-track: {{VALUE}};' ] ] );
		$this->add_control( 'range_fill', [ 'label' => esc_html__( 'Couleur de la partie remplie', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .ta-calculator' => '--ta-calc-fill-color: {{VALUE}};' ] ] );
		$this->add_control( 'range_thumb', [ 'label' => esc_html__( 'Couleur de la poignée', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .ta-calculator' => '--ta-calc-thumb: {{VALUE}};' ] ] );
		$this->add_control( 'range_thumb_border', [ 'label' => esc_html__( 'Contour de la poignée', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .ta-calculator' => '--ta-calc-thumb-border: {{VALUE}};' ] ] );
		$this->add_control( 'range_height', [ 'label' => esc_html__( 'Épaisseur de la piste', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'range' => [ 'px' => [ 'min' => 2, 'max' => 20 ] ], 'selectors' => [ '{{WRAPPER}} .ta-calculator' => '--ta-calc-track-h: {{SIZE}}{{UNIT}};' ] ] );
		$this->add_control( 'range_thumb_size', [ 'label' => esc_html__( 'Taille de la poignée', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'range' => [ 'px' => [ 'min' => 10, 'max' => 40 ] ], 'selectors' => [ '{{WRAPPER}} .ta-calculator' => '--ta-calc-thumb-size: {{SIZE}}{{UNIT}};' ] ] );

		$this->add_control( 'heading_volume_style', [ 'label' => esc_html__( 'Quantité affichée', 'tools-adapter' ), 'type' => Controls_Manager::HEADING, 'separator' => 'before' ] );
		$this->add_control( 'volume_color', [ 'label' => esc_html__( 'Couleur', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .ta-calculator__vol-val' => 'color: {{VALUE}};' ] ] );
		$this->add_control( 'volume_bg', [ 'label' => esc_html__( 'Fond (pastille)', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .ta-calculator__vol-val--pill' => 'background-color: {{VALUE}};' ], 'condition' => [ 'volume_display' => 'pill' ] ] );
		$this->add_group_control( Group_Control_Typography::get_type(), [ 'name' => 'volume_typography', 'selector' => '{{WRAPPER}} .ta-calculator__vol-val, {{WRAPPER}} .ta-calculator__vol-val strong' ] );
		$this->add_padding_control( 'volume_padding', esc_html__( 'Espacement interne (pastille)', 'tools-adapter' ), '.ta-calculator__vol-val--pill' );

		$this->add_control( 'heading_hints_style', [ 'label' => esc_html__( 'Minimum / maximum', 'tools-adapter' ), 'type' => Controls_Manager::HEADING, 'separator' => 'before', 'condition' => [ 'show_range_hints' => 'yes' ] ] );
		$this->add_control( 'hints_color', [ 'label' => esc_html__( 'Couleur', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .ta-calculator__range-hints' => 'color: {{VALUE}};' ], 'condition' => [ 'show_range_hints' => 'yes' ] ] );
		$this->add_group_control( Group_Control_Typography::get_type(), [ 'name' => 'hints_typography', 'selector' => '{{WRAPPER}} .ta-calculator__range-hints', 'condition' => [ 'show_range_hints' => 'yes' ] ] );

		$this->end_controls_section();

		// ==========================================
		// STYLE 6 : RÉCAPITULATIF & TOTAL
		// ==========================================
		$this->start_controls_section( 'section_style_summary', [ 'label' => esc_html__( 'Encart Résumé & Prix Total', 'tools-adapter' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_control( 'summary_bg', [ 'label' => esc_html__( 'Fond de l\'encart', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .ta-calculator__summary' => 'background: {{VALUE}};' ] ] );
		$this->add_group_control( Group_Control_Background::get_type(), [ 'name' => 'summary_background', 'label' => esc_html__( 'Fond dégradé', 'tools-adapter' ), 'types' => [ 'gradient' ], 'selector' => '{{WRAPPER}} .ta-calculator__summary' ] );
		$this->add_control( 'summary_border', [ 'label' => esc_html__( 'Bordure', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .ta-calculator__summary' => 'border-color: {{VALUE}};' ] ] );
		$this->add_padding_control( 'summary_padding', esc_html__( 'Espacement interne', 'tools-adapter' ), '.ta-calculator__summary' );
		$this->add_radius_control( 'summary_radius', '.ta-calculator__summary' );
		$this->add_group_control( Group_Control_Box_Shadow::get_type(), [ 'name' => 'summary_shadow', 'selector' => '{{WRAPPER}} .ta-calculator__summary' ] );

		$this->add_control( 'summary_label_color', [ 'label' => esc_html__( 'Couleur des libellés', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'separator' => 'before', 'selectors' => [ '{{WRAPPER}} .ta-calculator__summary-label' => 'color: {{VALUE}};' ] ] );
		$this->add_group_control( Group_Control_Typography::get_type(), [ 'name' => 'summary_label_typography', 'selector' => '{{WRAPPER}} .ta-calculator__summary-label' ] );
		$this->add_control( 'summary_total_color', [ 'label' => esc_html__( 'Couleur du Total', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'separator' => 'before', 'selectors' => [ '{{WRAPPER}} .ta-calculator__total' => 'color: {{VALUE}};' ] ] );
		$this->add_group_control( Group_Control_Typography::get_type(), [ 'name' => 'summary_total_typography', 'selector' => '{{WRAPPER}} .ta-calculator__total strong' ] );
		$this->add_group_control( Group_Control_Typography::get_type(), [ 'name' => 'summary_currency_typography', 'label' => esc_html__( 'Typographie devise / taxe', 'tools-adapter' ), 'selector' => '{{WRAPPER}} .ta-calculator__total' ] );
		$this->add_control( 'summary_unit_price_color', [ 'label' => esc_html__( 'Couleur du Prix unitaire', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'separator' => 'before', 'selectors' => [ '{{WRAPPER}} .ta-calculator__unit-price' => 'color: {{VALUE}};' ], 'condition' => [ 'summary_layout' => 'split' ] ] );
		$this->add_group_control( Group_Control_Typography::get_type(), [ 'name' => 'summary_unit_price_typography', 'selector' => '{{WRAPPER}} .ta-calculator__unit-price', 'condition' => [ 'summary_layout' => 'split' ] ] );
		$this->add_control( 'summary_detail_color', [ 'label' => esc_html__( 'Couleur de la ligne de détail', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'separator' => 'before', 'selectors' => [ '{{WRAPPER}} .ta-calculator__detail' => 'color: {{VALUE}};' ], 'condition' => [ 'summary_layout' => 'centered' ] ] );
		$this->add_group_control( Group_Control_Typography::get_type(), [ 'name' => 'summary_detail_typography', 'selector' => '{{WRAPPER}} .ta-calculator__detail', 'condition' => [ 'summary_layout' => 'centered' ] ] );

		$this->add_control( 'heading_notice_style', [ 'label' => esc_html__( 'Mention sous le total', 'tools-adapter' ), 'type' => Controls_Manager::HEADING, 'separator' => 'before' ] );
		$this->add_control( 'notice_color', [ 'label' => esc_html__( 'Couleur', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .ta-calculator__notice' => 'color: {{VALUE}};' ] ] );
		$this->add_group_control( Group_Control_Typography::get_type(), [ 'name' => 'notice_typography', 'selector' => '{{WRAPPER}} .ta-calculator__notice' ] );

		$this->end_controls_section();

		// ==========================================
		// STYLE 7 : BOUTON D'ACTION (CTA)
		// ==========================================
		$this->start_controls_section( 'section_style_cta', [ 'label' => esc_html__( 'Bouton d\'action (CTA)', 'tools-adapter' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_group_control( Group_Control_Typography::get_type(), [ 'name' => 'cta_typography', 'selector' => '{{WRAPPER}} .ta-calculator__btn' ] );
		$this->add_control( 'cta_full_width', [ 'label' => esc_html__( 'Pleine largeur', 'tools-adapter' ), 'type' => Controls_Manager::SWITCHER, 'return_value' => 'yes', 'default' => 'yes', 'prefix_class' => 'ta-calc--cta-full-' ] );

		$this->start_controls_tabs( 'cta_state_tabs' );
		$this->start_controls_tab( 'cta_state_normal', [ 'label' => esc_html__( 'Normal', 'tools-adapter' ) ] );
		$this->add_control( 'cta_bg', [ 'label' => esc_html__( 'Fond', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .ta-calculator__btn' => 'background: {{VALUE}};' ] ] );
		$this->add_group_control( Group_Control_Background::get_type(), [ 'name' => 'cta_background', 'label' => esc_html__( 'Fond dégradé', 'tools-adapter' ), 'types' => [ 'gradient' ], 'selector' => '{{WRAPPER}} .ta-calculator__btn' ] );
		$this->add_control( 'cta_text_color', [ 'label' => esc_html__( 'Couleur du texte', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .ta-calculator__btn' => 'color: {{VALUE}};', '{{WRAPPER}} .ta-calculator__btn svg' => 'fill: {{VALUE}};' ] ] );
		$this->add_group_control( Group_Control_Box_Shadow::get_type(), [ 'name' => 'cta_shadow', 'selector' => '{{WRAPPER}} .ta-calculator__btn' ] );
		$this->end_controls_tab();
		$this->start_controls_tab( 'cta_state_hover', [ 'label' => esc_html__( 'Survol', 'tools-adapter' ) ] );
		$this->add_control( 'cta_bg_hover', [ 'label' => esc_html__( 'Fond (survol)', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .ta-calculator__btn:hover' => 'background: {{VALUE}};' ] ] );
		$this->add_control( 'cta_text_color_hover', [ 'label' => esc_html__( 'Couleur du texte (survol)', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .ta-calculator__btn:hover' => 'color: {{VALUE}};', '{{WRAPPER}} .ta-calculator__btn:hover svg' => 'fill: {{VALUE}};' ] ] );
		$this->add_group_control( Group_Control_Box_Shadow::get_type(), [ 'name' => 'cta_shadow_hover', 'selector' => '{{WRAPPER}} .ta-calculator__btn:hover' ] );
		$this->add_control( 'cta_lift', [ 'label' => esc_html__( 'Élévation (px)', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'range' => [ 'px' => [ 'min' => 0, 'max' => 10 ] ], 'selectors' => [ '{{WRAPPER}} .ta-calculator__btn:hover' => 'transform: translateY(-{{SIZE}}px);' ] ] );
		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_group_control( Group_Control_Border::get_type(), [ 'name' => 'cta_border', 'selector' => '{{WRAPPER}} .ta-calculator__btn', 'separator' => 'before' ] );
		$this->add_responsive_control( 'cta_padding', [ 'label' => esc_html__( 'Espacement interne (padding)', 'tools-adapter' ), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => [ 'px', 'em' ], 'selectors' => [ '{{WRAPPER}} .ta-calculator__btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ] ] );
		$this->add_responsive_control( 'cta_radius', [ 'label' => esc_html__( 'Arrondi du bouton (px)', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'range' => [ 'px' => [ 'min' => 0, 'max' => 50 ] ], 'selectors' => [ '{{WRAPPER}} .ta-calculator__btn' => 'border-radius: {{SIZE}}{{UNIT}};' ] ] );
		$this->add_control( 'cta_icon_size', [ 'label' => esc_html__( 'Taille de l\'icône', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'range' => [ 'px' => [ 'min' => 8, 'max' => 40 ] ], 'selectors' => [ '{{WRAPPER}} .ta-calculator__btn-icon' => 'font-size: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .ta-calculator__btn-icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};' ] ] );
		$this->add_control( 'cta_icon_gap', [ 'label' => esc_html__( 'Espace icône / texte', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'range' => [ 'px' => [ 'min' => 0, 'max' => 30 ] ], 'selectors' => [ '{{WRAPPER}} .ta-calculator__btn' => 'gap: {{SIZE}}{{UNIT}};' ] ] );

		$this->end_controls_section();
	}

	/**
	 * Build the pricing data (labels + price matrix) from either mode.
	 *
	 * @param array $settings Widget settings.
	 * @return array{essences:string[],lengths:string[],prices:array<int,array<int,float|null>>}
	 */
	private function get_pricing( array $settings ) {
		$essences = [];
		$lengths  = [];
		$matrix   = [];

		if ( 'list' === ( $settings['pricing_mode'] ?? 'grid' ) ) {
			foreach ( (array) ( $settings['rates'] ?? [] ) as $rate ) {
				$essence = trim( (string) ( $rate['essence'] ?? '' ) );
				$length  = trim( (string) ( $rate['length'] ?? '' ) );
				$price   = str_replace( [ ' ', ',' ], [ '', '.' ], (string) ( $rate['price'] ?? '' ) );
				if ( '' === $essence || '' === $length || ! is_numeric( $price ) ) {
					continue;
				}
				$essence = \tools_adapter_translate( $essence );
				$length  = \tools_adapter_translate( $length );
				$e       = array_search( $essence, $essences, true );
				if ( false === $e ) {
					$essences[] = $essence;
					$e          = count( $essences ) - 1;
				}
				$l = array_search( $length, $lengths, true );
				if ( false === $l ) {
					$lengths[] = $length;
					$l         = count( $lengths ) - 1;
				}
				$matrix[ $e ][ $l ] = (float) $price;
			}
		} else {
			$essences = [
				\tools_adapter_translate( $settings['label_hetre'] ?? 'Hêtre' ),
				\tools_adapter_translate( $settings['label_melange'] ?? 'Mélange dur' ),
			];
			foreach ( array_keys( self::LEGACY_LENGTHS ) as $l => $key ) {
				$lengths[]       = self::LEGACY_LENGTHS[ $key ][0];
				$matrix[0][ $l ] = (float) ( $settings[ 'price_hetre_' . $key ] ?? self::LEGACY_LENGTHS[ $key ][1] );
				$matrix[1][ $l ] = (float) ( $settings[ 'price_melange_' . $key ] ?? self::LEGACY_LENGTHS[ $key ][2] );
			}
		}

		// Dense matrix: null = combinaison indisponible.
		$prices = [];
		foreach ( array_keys( $essences ) as $e ) {
			foreach ( array_keys( $lengths ) as $l ) {
				$prices[ $e ][ $l ] = $matrix[ $e ][ $l ] ?? null;
			}
		}

		return [ 'essences' => $essences, 'lengths' => $lengths, 'prices' => $prices ];
	}

	/**
	 * Format a number the French way (thousands with a space, up to 2 decimals).
	 *
	 * @param float $number Number.
	 * @return string
	 */
	private static function format_number( $number ) {
		$decimals = abs( $number - round( $number ) ) < 0.005 ? 0 : 2;
		return number_format( (float) $number, $decimals, ',', ' ' );
	}

	/**
	 * Wrap an amount with the currency (amount in a <strong> updated by JS).
	 *
	 * @param string $amount_html <strong> element.
	 * @param string $currency    Currency.
	 * @param string $position    "before" or "after".
	 * @param string $suffix      Text after (e.g. " TTC").
	 * @return string
	 */
	private static function money_html( $amount_html, $currency, $position, $suffix = '' ) {
		$currency = esc_html( $currency );
		$html     = 'before' === $position ? $currency . $amount_html : $amount_html . ( '' !== $currency ? ' ' . $currency : '' );
		return $html . ( '' !== $suffix ? ' ' . esc_html( $suffix ) : '' );
	}

	/**
	 * Replace {placeholders} in a template.
	 *
	 * @param string $template Template.
	 * @param array  $vars     Variables.
	 * @return string
	 */
	private static function fill_template( $template, array $vars ) {
		foreach ( $vars as $key => $value ) {
			$template = str_replace( '{' . $key . '}', (string) $value, $template );
		}
		return $template;
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$calc_id  = 'ta-calc-' . $this->get_id();
		$pricing  = $this->get_pricing( $settings );

		if ( empty( $pricing['essences'] ) || empty( $pricing['lengths'] ) ) {
			if ( isset( \Elementor\Plugin::$instance->editor ) && \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
				echo '<p class="ta-calculator__empty">' . esc_html__( 'Ajoutez au moins un tarif (essence + longueur + prix).', 'tools-adapter' ) . '</p>';
			}
			return;
		}

		// Volume.
		$step    = (float) ( $settings['volume_step'] ?? 1 );
		$step    = $step > 0 ? $step : 1;
		$min_vol = (float) ( $settings['volume_min'] ?? 1 );
		$max_vol = max( $min_vol, (float) ( $settings['volume_max'] ?? 30 ) );
		$volume  = min( $max_vol, max( $min_vol, (float) ( $settings['volume_default'] ?? 6 ) ) );
		$unit    = \tools_adapter_translate( $settings['volume_unit'] ?? 'stères' );

		// Sélection initiale : essence demandée (ou 1re), puis longueur demandée si disponible (sinon 1re disponible).
		$essence_index = array_search( trim( (string) ( $settings['default_essence'] ?? '' ) ), $pricing['essences'], true );
		$essence_index = false === $essence_index ? 0 : $essence_index;
		$length_index  = array_search( trim( (string) ( $settings['default_length'] ?? '' ) ), $pricing['lengths'], true );
		if ( false === $length_index || null === $pricing['prices'][ $essence_index ][ $length_index ] ) {
			$length_index = 0;
			foreach ( $pricing['prices'][ $essence_index ] as $l => $price ) {
				if ( null !== $price ) {
					$length_index = $l;
					break;
				}
			}
		}
		$unit_price = (float) ( $pricing['prices'][ $essence_index ][ $length_index ] ?? 0 );
		$total      = $unit_price * $volume;

		$currency   = (string) ( $settings['currency'] ?? '€' );
		$cur_pos    = 'before' === ( $settings['currency_position'] ?? 'after' ) ? 'before' : 'after';
		$tax        = \tools_adapter_translate( $settings['tax_label'] ?? '' );
		$layout     = 'centered' === ( $settings['summary_layout'] ?? 'split' ) ? 'centered' : 'split';
		$title_tag  = in_array( $settings['title_tag'] ?? 'h3', [ 'h2', 'h3', 'h4', 'div' ], true ) ? $settings['title_tag'] : 'h3';
		$vol_pill   = 'pill' === ( $settings['volume_display'] ?? 'text' );
		$vol_inline = 'slider' === ( $settings['volume_position'] ?? 'header' );
		$detail_tpl = \tools_adapter_translate( $settings['detail_text'] ?? '' );

		$vars = [
			'prix'     => self::format_number( $unit_price ),
			'volume'   => self::format_number( $volume ),
			'unite'    => $unit,
			'total'    => self::format_number( $total ),
			'essence'  => $pricing['essences'][ $essence_index ],
			'longueur' => $pricing['lengths'][ $length_index ],
		];

		$config = [
			'essences' => $pricing['essences'],
			'lengths'  => $pricing['lengths'],
			'prices'   => $pricing['prices'],
			'unit'     => $unit,
			'detail'   => $detail_tpl,
			'prefill'  => 'yes' === ( $settings['prefill_form'] ?? '' ) ? \tools_adapter_translate( $settings['prefill_message'] ?? '' ) : '',
			'service'  => 'yes' === ( $settings['prefill_form'] ?? '' ) ? (string) ( $settings['prefill_service'] ?? '' ) : '',
		];

		$cta_url      = ! empty( $settings['cta_link']['url'] ) ? $settings['cta_link']['url'] : '#devis';
		$cta_target   = ! empty( $settings['cta_link']['is_external'] ) ? ' target="_blank" rel="noopener"' : '';
		$cta_icon_pos = in_array( $settings['cta_icon_position'] ?? 'after', [ 'before', 'after', 'none' ], true ) ? $settings['cta_icon_position'] : 'after';

		$volume_html = '<span class="ta-calculator__vol-val' . ( $vol_pill ? ' ta-calculator__vol-val--pill' : '' ) . '" aria-hidden="true"><strong data-calc-vol-display>' . esc_html( self::format_number( $volume ) ) . '</strong> ' . esc_html( $unit ) . '</span>';
		?>
		<div class="ta-calculator ta-calculator--summary-<?php echo esc_attr( $layout ); ?>" id="<?php echo esc_attr( $calc_id ); ?>" data-ta-calculator data-config="<?php echo esc_attr( wp_json_encode( $config ) ); ?>">
			<?php if ( ! empty( $settings['badge'] ) || ! empty( $settings['title'] ) || ! empty( $settings['description'] ) ) : ?>
				<div class="ta-calculator__header">
					<?php if ( ! empty( $settings['badge'] ) ) : ?>
						<span class="ta-calculator__badge"><?php echo esc_html( \tools_adapter_translate( $settings['badge'] ) ); ?></span>
					<?php endif; ?>
					<?php if ( ! empty( $settings['title'] ) ) : ?>
						<<?php echo esc_attr( $title_tag ); ?> class="ta-calculator__title">
							<?php if ( ! empty( $settings['title_icon']['value'] ) ) : ?>
								<span class="ta-calculator__title-icon" aria-hidden="true"><?php Icons_Manager::render_icon( $settings['title_icon'], [ 'aria-hidden' => 'true' ] ); ?></span>
							<?php endif; ?>
							<span><?php echo esc_html( \tools_adapter_translate( $settings['title'] ) ); ?></span>
						</<?php echo esc_attr( $title_tag ); ?>>
					<?php endif; ?>
					<?php if ( ! empty( $settings['description'] ) ) : ?>
						<p class="ta-calculator__desc"><?php echo esc_html( \tools_adapter_translate( $settings['description'] ) ); ?></p>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<?php // Étape 1 : essence. ?>
			<div class="ta-calculator__group">
				<?php if ( 'select' === ( $settings['essence_display'] ?? 'buttons' ) ) : ?>
					<label class="ta-calculator__label" for="<?php echo esc_attr( $calc_id ); ?>-essence"><?php echo esc_html( \tools_adapter_translate( $settings['label_essence'] ?? '' ) ); ?></label>
					<div class="ta-calculator__select-wrap">
						<select class="ta-calculator__select" id="<?php echo esc_attr( $calc_id ); ?>-essence" data-calc-essence>
							<?php foreach ( $pricing['essences'] as $e => $label ) : ?>
								<option value="<?php echo esc_attr( (string) $e ); ?>"<?php selected( $e, $essence_index ); ?>><?php echo esc_html( $label ); ?></option>
							<?php endforeach; ?>
						</select>
					</div>
				<?php else : ?>
					<span class="ta-calculator__label" id="<?php echo esc_attr( $calc_id ); ?>-essence-label"><?php echo esc_html( \tools_adapter_translate( $settings['label_essence'] ?? '' ) ); ?></span>
					<div class="ta-calculator__pills" role="radiogroup" aria-labelledby="<?php echo esc_attr( $calc_id ); ?>-essence-label">
						<?php foreach ( $pricing['essences'] as $e => $label ) : ?>
							<label class="ta-calculator__pill">
								<input type="radio" name="essence_<?php echo esc_attr( $calc_id ); ?>" value="<?php echo esc_attr( (string) $e ); ?>"<?php checked( $e, $essence_index ); ?> data-calc-essence>
								<span><?php echo esc_html( $label ); ?></span>
							</label>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>

			<?php // Étape 2 : longueur. ?>
			<div class="ta-calculator__group">
				<?php if ( 'select' === ( $settings['length_display'] ?? 'buttons' ) ) : ?>
					<label class="ta-calculator__label" for="<?php echo esc_attr( $calc_id ); ?>-length"><?php echo esc_html( \tools_adapter_translate( $settings['label_length'] ?? '' ) ); ?></label>
					<div class="ta-calculator__select-wrap">
						<select class="ta-calculator__select" id="<?php echo esc_attr( $calc_id ); ?>-length" data-calc-length>
							<?php foreach ( $pricing['lengths'] as $l => $label ) : ?>
								<option value="<?php echo esc_attr( (string) $l ); ?>"<?php selected( $l, $length_index ); ?><?php disabled( null === $pricing['prices'][ $essence_index ][ $l ] ); ?>><?php echo esc_html( $label ); ?></option>
							<?php endforeach; ?>
						</select>
					</div>
				<?php else : ?>
					<span class="ta-calculator__label" id="<?php echo esc_attr( $calc_id ); ?>-length-label"><?php echo esc_html( \tools_adapter_translate( $settings['label_length'] ?? '' ) ); ?></span>
					<div class="ta-calculator__radios" role="radiogroup" aria-labelledby="<?php echo esc_attr( $calc_id ); ?>-length-label">
						<?php foreach ( $pricing['lengths'] as $l => $label ) : ?>
							<label class="ta-calculator__radio">
								<input type="radio" name="longueur_<?php echo esc_attr( $calc_id ); ?>" value="<?php echo esc_attr( (string) $l ); ?>"<?php checked( $l, $length_index ); ?><?php disabled( null === $pricing['prices'][ $essence_index ][ $l ] ); ?> data-calc-length>
								<span><?php echo esc_html( $label ); ?></span>
							</label>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>

			<?php // Étape 3 : volume. ?>
			<div class="ta-calculator__group">
				<div class="ta-calculator__vol-header">
					<label class="ta-calculator__label" for="<?php echo esc_attr( $calc_id ); ?>-range"><?php echo esc_html( \tools_adapter_translate( $settings['label_volume'] ?? '' ) ); ?></label>
					<?php
					if ( ! $vol_inline ) {
						echo $volume_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above.
					}
					?>
				</div>
				<div class="ta-calculator__range-row">
					<input type="range" class="ta-calculator__range" id="<?php echo esc_attr( $calc_id ); ?>-range" min="<?php echo esc_attr( (string) $min_vol ); ?>" max="<?php echo esc_attr( (string) $max_vol ); ?>" step="<?php echo esc_attr( (string) $step ); ?>" value="<?php echo esc_attr( (string) $volume ); ?>" aria-valuetext="<?php echo esc_attr( self::format_number( $volume ) . ' ' . $unit ); ?>" data-calc-range>
					<?php
					if ( $vol_inline ) {
						echo $volume_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above.
					}
					?>
				</div>
				<?php if ( 'yes' === ( $settings['show_range_hints'] ?? '' ) ) : ?>
					<div class="ta-calculator__range-hints" aria-hidden="true">
						<span><?php echo esc_html( self::format_number( $min_vol ) . ' ' . $unit ); ?></span>
						<span><?php echo esc_html( self::format_number( $max_vol ) . ' ' . $unit ); ?></span>
					</div>
				<?php endif; ?>
			</div>

			<?php // Résumé & total. ?>
			<div class="ta-calculator__summary" aria-live="polite">
				<?php if ( 'centered' === $layout ) : ?>
					<?php if ( ! empty( $settings['centered_total_label'] ) ) : ?>
						<span class="ta-calculator__summary-label"><?php echo esc_html( \tools_adapter_translate( $settings['centered_total_label'] ) ); ?></span>
					<?php endif; ?>
					<span class="ta-calculator__total"><?php echo self::money_html( '<strong data-calc-total-display>' . esc_html( $vars['total'] ) . '</strong>', $currency, $cur_pos ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
					<?php if ( '' !== $detail_tpl ) : ?>
						<span class="ta-calculator__detail" data-calc-detail><?php echo esc_html( self::fill_template( $detail_tpl, $vars ) ); ?></span>
					<?php endif; ?>
				<?php else : ?>
					<div class="ta-calculator__summary-left">
						<span class="ta-calculator__summary-label"><?php echo esc_html( \tools_adapter_translate( $settings['unit_price_label'] ?? '' ) ); ?></span>
						<span class="ta-calculator__unit-price"><?php echo self::money_html( '<strong data-calc-unit-display>' . esc_html( $vars['prix'] ) . '</strong>', $currency, $cur_pos, $tax ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
					</div>
					<div class="ta-calculator__summary-right">
						<span class="ta-calculator__summary-label"><?php echo esc_html( \tools_adapter_translate( $settings['total_label'] ?? '' ) ); ?></span>
						<span class="ta-calculator__total"><?php echo self::money_html( '<strong data-calc-total-display>' . esc_html( $vars['total'] ) . '</strong>', $currency, $cur_pos, $tax ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
					</div>
				<?php endif; ?>
			</div>

			<?php if ( ! empty( $settings['notice_text'] ) ) : ?>
				<p class="ta-calculator__notice"><?php echo esc_html( \tools_adapter_translate( $settings['notice_text'] ) ); ?></p>
			<?php endif; ?>

			<?php if ( ! empty( $settings['cta_text'] ) ) : ?>
				<?php
				ob_start();
				if ( 'none' !== $cta_icon_pos ) {
					echo '<span class="ta-calculator__btn-icon" aria-hidden="true">';
					if ( ! empty( $settings['cta_icon']['value'] ) ) {
						Icons_Manager::render_icon( $settings['cta_icon'], [ 'aria-hidden' => 'true' ] );
					} else {
						echo '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" focusable="false"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>';
					}
					echo '</span>';
				}
				$cta_icon_html = ob_get_clean();
				?>
				<div class="ta-calculator__actions">
					<a href="<?php echo esc_url( $cta_url ); ?>" class="ta-calculator__btn" data-calc-cta<?php echo $cta_target; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
						<?php
						if ( 'before' === $cta_icon_pos ) {
							echo $cta_icon_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						}
						?>
						<span><?php echo esc_html( \tools_adapter_translate( $settings['cta_text'] ) ); ?></span>
						<?php
						if ( 'after' === $cta_icon_pos ) {
							echo $cta_icon_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						}
						?>
					</a>
				</div>
			<?php endif; ?>
		</div>
		<?php
	}
}
