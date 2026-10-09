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
 * Widget: Carte de tarifs — titre, description, badge et lignes
 * « libellé … prix / unité » (ex. tarifs du bois au stère).
 *
 * Les couleurs par défaut viennent du style prédéfini (clair / sombre) défini
 * dans price-card.css : les contrôles de couleur n'ont pas de valeur par
 * défaut, ils ne font que surcharger ce style.
 */
class Price_Card extends Base_Widget {

	public function get_name() {
		return 'tools-adapter-price-card';
	}

	public function get_title() {
		return esc_html__( 'Carte de tarifs', 'tools-adapter' );
	}

	public function get_icon() {
		return 'eicon-price-list';
	}

	public function get_categories() {
		return [ 'tools-adapter' ];
	}

	public function get_keywords() {
		return [ 'tarifs', 'prix', 'liste', 'stère', 'bois', 'carte', 'price', 'list' ];
	}

	public function get_style_depends() {
		return [ 'tools-adapter-price-card' ];
	}

	protected function register_controls() {
		// ── Contenu : en-tête ────────────────────────────────────────────
		$this->start_controls_section( 'section_header', [ 'label' => esc_html__( 'En-tête', 'tools-adapter' ) ] );

		$this->add_control( 'title', [ 'label' => esc_html__( 'Titre', 'tools-adapter' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'Hêtre', 'tools-adapter' ), 'label_block' => true ] );
		$this->add_control( 'title_tag', [ 'label' => esc_html__( 'Balise du titre', 'tools-adapter' ), 'type' => Controls_Manager::SELECT, 'default' => 'h3', 'options' => [ 'h2' => 'H2', 'h3' => 'H3', 'h4' => 'H4', 'div' => 'div' ] ] );
		$this->add_control( 'description', [ 'label' => esc_html__( 'Description', 'tools-adapter' ), 'type' => Controls_Manager::TEXTAREA, 'rows' => 2, 'default' => esc_html__( 'Pouvoir calorifique supérieur, flamme régulière.', 'tools-adapter' ) ] );
		$this->add_control( 'badge', [ 'label' => esc_html__( 'Badge', 'tools-adapter' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'Haute densité', 'tools-adapter' ), 'separator' => 'before' ] );
		$this->add_control(
			'badge_placement',
			[
				'label'   => esc_html__( 'Emplacement du badge', 'tools-adapter' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'corner',
				'options' => [
					'corner' => esc_html__( 'Coin haut droit', 'tools-adapter' ),
					'above'  => esc_html__( 'Au-dessus du titre', 'tools-adapter' ),
					'inline' => esc_html__( 'À côté du titre', 'tools-adapter' ),
				],
			]
		);

		$this->end_controls_section();

		// ── Contenu : tarifs ─────────────────────────────────────────────
		$this->start_controls_section( 'section_rows', [ 'label' => esc_html__( 'Tarifs', 'tools-adapter' ) ] );

		$repeater = new Repeater();
		$repeater->add_control( 'label', [ 'label' => esc_html__( 'Libellé', 'tools-adapter' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'Prestation', 'tools-adapter' ), 'label_block' => true ] );
		$repeater->add_control( 'price', [ 'label' => esc_html__( 'Prix', 'tools-adapter' ), 'type' => Controls_Manager::TEXT, 'default' => '100', 'description' => esc_html__( 'Nombre ou texte libre (ex. « Sur devis »).', 'tools-adapter' ) ] );
		$repeater->add_control( 'unit', [ 'label' => esc_html__( 'Unité (si différente)', 'tools-adapter' ), 'type' => Controls_Manager::TEXT, 'placeholder' => esc_html__( 'Unité globale', 'tools-adapter' ) ] );
		$repeater->add_control( 'old_price', [ 'label' => esc_html__( 'Ancien prix (barré)', 'tools-adapter' ), 'type' => Controls_Manager::TEXT ] );
		$repeater->add_control( 'note', [ 'label' => esc_html__( 'Précision sous le libellé', 'tools-adapter' ), 'type' => Controls_Manager::TEXT ] );
		$repeater->add_control( 'highlight', [ 'label' => esc_html__( 'Mettre en avant', 'tools-adapter' ), 'type' => Controls_Manager::SWITCHER, 'return_value' => 'yes' ] );
		$repeater->add_control( 'row_label_color', [ 'label' => esc_html__( 'Couleur du libellé', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'separator' => 'before', 'selectors' => [ '{{WRAPPER}} {{CURRENT_ITEM}} .ta-pc__label' => 'color: {{VALUE}};' ] ] );
		$repeater->add_control( 'row_price_color', [ 'label' => esc_html__( 'Couleur du prix', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} {{CURRENT_ITEM}} .ta-pc__amount' => 'color: {{VALUE}};' ] ] );

		$this->add_control(
			'rows',
			[
				'label'       => esc_html__( 'Lignes', 'tools-adapter' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ label }}} — {{{ price }}}',
				'default'     => [
					[ 'label' => esc_html__( 'Sciage 1 mètre', 'tools-adapter' ), 'price' => '115' ],
					[ 'label' => esc_html__( 'Sciage 33 ou 50 cm', 'tools-adapter' ), 'price' => '130' ],
					[ 'label' => esc_html__( 'Sciage 25 cm', 'tools-adapter' ), 'price' => '135' ],
				],
			]
		);

		$this->add_control( 'currency', [ 'label' => esc_html__( 'Devise', 'tools-adapter' ), 'type' => Controls_Manager::TEXT, 'default' => '€', 'separator' => 'before' ] );
		$this->add_control(
			'currency_position',
			[
				'label'   => esc_html__( 'Position de la devise', 'tools-adapter' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'after',
				'options' => [
					'after'  => esc_html__( 'Après (115 €)', 'tools-adapter' ),
					'before' => esc_html__( 'Avant ($115)', 'tools-adapter' ),
				],
			]
		);
		$this->add_control( 'unit', [ 'label' => esc_html__( 'Unité globale', 'tools-adapter' ), 'type' => Controls_Manager::TEXT, 'default' => '/stère' ] );

		$this->end_controls_section();

		// ── Contenu : pied ───────────────────────────────────────────────
		$this->start_controls_section( 'section_footer', [ 'label' => esc_html__( 'Pied de carte', 'tools-adapter' ) ] );

		$this->add_control( 'footer_text', [ 'label' => esc_html__( 'Mention', 'tools-adapter' ), 'type' => Controls_Manager::TEXTAREA, 'rows' => 2, 'placeholder' => esc_html__( 'Ex. Tarifs TTC, livraison en sus.', 'tools-adapter' ) ] );
		$this->add_control( 'button_text', [ 'label' => esc_html__( 'Texte du bouton', 'tools-adapter' ), 'type' => Controls_Manager::TEXT, 'placeholder' => esc_html__( 'Commander', 'tools-adapter' ) ] );
		$this->add_control( 'button_link', [ 'label' => esc_html__( 'Lien du bouton', 'tools-adapter' ), 'type' => Controls_Manager::URL, 'default' => [ 'url' => '#' ] ] );
		$this->add_control( 'button_icon', [ 'label' => esc_html__( 'Icône du bouton', 'tools-adapter' ), 'type' => Controls_Manager::ICONS ] );

		$this->end_controls_section();

		// ── Style : carte ────────────────────────────────────────────────
		$this->start_controls_section( 'section_style_card', [ 'label' => esc_html__( 'Carte', 'tools-adapter' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_control(
			'theme',
			[
				'label'        => esc_html__( 'Style prédéfini', 'tools-adapter' ),
				'type'         => Controls_Manager::SELECT,
				'default'      => 'dark',
				'options'      => [
					'dark'  => esc_html__( 'Sombre', 'tools-adapter' ),
					'light' => esc_html__( 'Clair', 'tools-adapter' ),
				],
				'prefix_class' => 'ta-pc--theme-',
				'description'  => esc_html__( 'Base de couleurs ; les réglages ci-dessous la remplacent.', 'tools-adapter' ),
			]
		);
		$this->add_group_control( Group_Control_Background::get_type(), [ 'name' => 'card_background', 'types' => [ 'classic', 'gradient' ], 'selector' => '{{WRAPPER}} .ta-pc' ] );
		$this->add_group_control( Group_Control_Border::get_type(), [ 'name' => 'card_border', 'selector' => '{{WRAPPER}} .ta-pc' ] );
		$this->add_responsive_control( 'card_radius', [ 'label' => esc_html__( 'Arrondi', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'range' => [ 'px' => [ 'min' => 0, 'max' => 60 ] ], 'selectors' => [ '{{WRAPPER}} .ta-pc' => 'border-radius: {{SIZE}}{{UNIT}};' ] ] );
		$this->add_responsive_control( 'card_padding', [ 'label' => esc_html__( 'Espacement interne', 'tools-adapter' ), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => [ 'px', 'em' ], 'selectors' => [ '{{WRAPPER}} .ta-pc' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ] ] );
		$this->add_control( 'equal_height', [ 'label' => esc_html__( 'Occuper toute la hauteur de la colonne', 'tools-adapter' ), 'type' => Controls_Manager::SWITCHER, 'return_value' => 'yes', 'prefix_class' => 'ta-pc--full-height-' ] );

		$this->start_controls_tabs( 'card_state_tabs' );
		$this->start_controls_tab( 'card_state_normal', [ 'label' => esc_html__( 'Normal', 'tools-adapter' ) ] );
		$this->add_group_control( Group_Control_Box_Shadow::get_type(), [ 'name' => 'card_shadow', 'selector' => '{{WRAPPER}} .ta-pc' ] );
		$this->end_controls_tab();
		$this->start_controls_tab( 'card_state_hover', [ 'label' => esc_html__( 'Survol', 'tools-adapter' ) ] );
		$this->add_group_control( Group_Control_Box_Shadow::get_type(), [ 'name' => 'card_shadow_hover', 'selector' => '{{WRAPPER}} .ta-pc:hover' ] );
		$this->add_control( 'card_lift', [ 'label' => esc_html__( 'Élévation (px)', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'range' => [ 'px' => [ 'min' => 0, 'max' => 20 ] ], 'selectors' => [ '{{WRAPPER}} .ta-pc:hover' => 'transform: translateY(-{{SIZE}}px);' ] ] );
		$this->add_control( 'card_border_hover', [ 'label' => esc_html__( 'Couleur de bordure', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .ta-pc:hover' => 'border-color: {{VALUE}};' ] ] );
		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();

		// ── Style : badge ────────────────────────────────────────────────
		$this->start_controls_section( 'section_style_badge', [ 'label' => esc_html__( 'Badge', 'tools-adapter' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_control( 'badge_color', [ 'label' => esc_html__( 'Texte', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .ta-pc__badge' => 'color: {{VALUE}};' ] ] );
		$this->add_control( 'badge_bg', [ 'label' => esc_html__( 'Fond', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .ta-pc__badge' => 'background-color: {{VALUE}};' ] ] );
		$this->add_group_control( Group_Control_Typography::get_type(), [ 'name' => 'badge_typography', 'selector' => '{{WRAPPER}} .ta-pc__badge' ] );
		$this->add_group_control( Group_Control_Border::get_type(), [ 'name' => 'badge_border', 'selector' => '{{WRAPPER}} .ta-pc__badge' ] );
		$this->add_responsive_control( 'badge_padding', [ 'label' => esc_html__( 'Espacement interne', 'tools-adapter' ), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => [ 'px', 'em' ], 'selectors' => [ '{{WRAPPER}} .ta-pc__badge' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ] ] );
		$this->add_control( 'badge_radius', [ 'label' => esc_html__( 'Arrondi', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'size_units' => [ 'px', '%' ], 'range' => [ 'px' => [ 'min' => 0, 'max' => 40 ] ], 'selectors' => [ '{{WRAPPER}} .ta-pc__badge' => 'border-radius: {{SIZE}}{{UNIT}};' ] ] );
		$this->add_responsive_control( 'badge_offset', [ 'label' => esc_html__( 'Distance au coin', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'range' => [ 'px' => [ 'min' => -30, 'max' => 60 ] ], 'selectors' => [ '{{WRAPPER}} .ta-pc' => '--ta-pc-badge-offset: {{SIZE}}{{UNIT}};' ], 'condition' => [ 'badge_placement' => 'corner' ] ] );

		$this->end_controls_section();

		// ── Style : en-tête ──────────────────────────────────────────────
		$this->start_controls_section( 'section_style_header', [ 'label' => esc_html__( 'Titre & description', 'tools-adapter' ), 'tab' => Controls_Manager::TAB_STYLE ] );

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
				'selectors' => [ '{{WRAPPER}} .ta-pc__header' => 'text-align: {{VALUE}};' ],
			]
		);
		$this->add_control( 'title_color', [ 'label' => esc_html__( 'Couleur du titre', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .ta-pc__title' => 'color: {{VALUE}};' ] ] );
		$this->add_group_control( Group_Control_Typography::get_type(), [ 'name' => 'title_typography', 'selector' => '{{WRAPPER}} .ta-pc__title' ] );
		$this->add_responsive_control( 'title_spacing', [ 'label' => esc_html__( 'Espace sous le titre', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'range' => [ 'px' => [ 'min' => 0, 'max' => 40 ] ], 'selectors' => [ '{{WRAPPER}} .ta-pc__title' => 'margin-bottom: {{SIZE}}{{UNIT}};' ] ] );
		$this->add_control( 'desc_color', [ 'label' => esc_html__( 'Couleur de la description', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'separator' => 'before', 'selectors' => [ '{{WRAPPER}} .ta-pc__desc' => 'color: {{VALUE}};' ] ] );
		$this->add_group_control( Group_Control_Typography::get_type(), [ 'name' => 'desc_typography', 'selector' => '{{WRAPPER}} .ta-pc__desc' ] );
		$this->add_responsive_control( 'header_spacing', [ 'label' => esc_html__( 'Espace avant les tarifs', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'range' => [ 'px' => [ 'min' => 0, 'max' => 60 ] ], 'selectors' => [ '{{WRAPPER}} .ta-pc__header' => 'margin-bottom: {{SIZE}}{{UNIT}};' ] ] );

		$this->end_controls_section();

		// ── Style : lignes ───────────────────────────────────────────────
		$this->start_controls_section( 'section_style_rows', [ 'label' => esc_html__( 'Lignes de tarifs', 'tools-adapter' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_responsive_control( 'row_padding', [ 'label' => esc_html__( 'Hauteur des lignes (espacement vertical)', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'range' => [ 'px' => [ 'min' => 0, 'max' => 40 ] ], 'selectors' => [ '{{WRAPPER}} .ta-pc__row' => 'padding-top: {{SIZE}}{{UNIT}}; padding-bottom: {{SIZE}}{{UNIT}};' ] ] );
		$this->add_control(
			'separator_style',
			[
				'label'     => esc_html__( 'Séparateurs', 'tools-adapter' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'dashed',
				'options'   => [
					'dashed' => esc_html__( 'Tirets', 'tools-adapter' ),
					'dotted' => esc_html__( 'Pointillés', 'tools-adapter' ),
					'solid'  => esc_html__( 'Trait plein', 'tools-adapter' ),
					'none'   => esc_html__( 'Aucun', 'tools-adapter' ),
				],
				'selectors' => [ '{{WRAPPER}} .ta-pc' => '--ta-pc-sep-style: {{VALUE}};' ],
			]
		);
		$this->add_control( 'separator_color', [ 'label' => esc_html__( 'Couleur des séparateurs', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .ta-pc' => '--ta-pc-sep: {{VALUE}};' ] ] );
		$this->add_control( 'separator_width', [ 'label' => esc_html__( 'Épaisseur des séparateurs', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'range' => [ 'px' => [ 'min' => 0, 'max' => 5 ] ], 'selectors' => [ '{{WRAPPER}} .ta-pc' => '--ta-pc-sep-width: {{SIZE}}{{UNIT}};' ] ] );
		$this->add_control( 'last_separator', [ 'label' => esc_html__( 'Séparateur sous la dernière ligne', 'tools-adapter' ), 'type' => Controls_Manager::SWITCHER, 'return_value' => 'yes', 'prefix_class' => 'ta-pc--last-sep-' ] );

		$this->add_control( 'row_label_heading', [ 'label' => esc_html__( 'Libellés', 'tools-adapter' ), 'type' => Controls_Manager::HEADING, 'separator' => 'before' ] );
		$this->add_control( 'label_color', [ 'label' => esc_html__( 'Couleur', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .ta-pc__label' => 'color: {{VALUE}};' ] ] );
		$this->add_group_control( Group_Control_Typography::get_type(), [ 'name' => 'label_typography', 'selector' => '{{WRAPPER}} .ta-pc__label' ] );
		$this->add_control( 'note_color', [ 'label' => esc_html__( 'Couleur des précisions', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .ta-pc__note' => 'color: {{VALUE}};' ] ] );
		$this->add_group_control( Group_Control_Typography::get_type(), [ 'name' => 'note_typography', 'selector' => '{{WRAPPER}} .ta-pc__note' ] );

		$this->add_control( 'row_price_heading', [ 'label' => esc_html__( 'Prix', 'tools-adapter' ), 'type' => Controls_Manager::HEADING, 'separator' => 'before' ] );
		$this->add_control( 'price_color', [ 'label' => esc_html__( 'Couleur', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .ta-pc__amount' => 'color: {{VALUE}};' ] ] );
		$this->add_group_control( Group_Control_Typography::get_type(), [ 'name' => 'price_typography', 'selector' => '{{WRAPPER}} .ta-pc__amount' ] );
		$this->add_control( 'unit_color', [ 'label' => esc_html__( 'Couleur de l\'unité', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .ta-pc__unit' => 'color: {{VALUE}};' ] ] );
		$this->add_group_control( Group_Control_Typography::get_type(), [ 'name' => 'unit_typography', 'selector' => '{{WRAPPER}} .ta-pc__unit' ] );
		$this->add_control( 'old_price_color', [ 'label' => esc_html__( 'Couleur de l\'ancien prix', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .ta-pc__old' => 'color: {{VALUE}};' ] ] );

		$this->add_control( 'row_highlight_heading', [ 'label' => esc_html__( 'Ligne mise en avant', 'tools-adapter' ), 'type' => Controls_Manager::HEADING, 'separator' => 'before' ] );
		$this->add_control( 'highlight_bg', [ 'label' => esc_html__( 'Fond', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .ta-pc__row--highlight' => 'background-color: {{VALUE}};' ] ] );
		$this->add_control( 'highlight_price_color', [ 'label' => esc_html__( 'Couleur du prix', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .ta-pc__row--highlight .ta-pc__amount' => 'color: {{VALUE}};' ] ] );

		$this->end_controls_section();

		// ── Style : pied ─────────────────────────────────────────────────
		$this->start_controls_section( 'section_style_footer', [ 'label' => esc_html__( 'Pied de carte', 'tools-adapter' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_control( 'footer_color', [ 'label' => esc_html__( 'Couleur de la mention', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .ta-pc__footer-text' => 'color: {{VALUE}};' ] ] );
		$this->add_group_control( Group_Control_Typography::get_type(), [ 'name' => 'footer_typography', 'selector' => '{{WRAPPER}} .ta-pc__footer-text' ] );
		$this->add_control( 'button_heading', [ 'label' => esc_html__( 'Bouton', 'tools-adapter' ), 'type' => Controls_Manager::HEADING, 'separator' => 'before' ] );
		$this->add_group_control( Group_Control_Typography::get_type(), [ 'name' => 'button_typography', 'selector' => '{{WRAPPER}} .ta-pc__btn' ] );
		$this->start_controls_tabs( 'button_tabs' );
		$this->start_controls_tab( 'button_normal', [ 'label' => esc_html__( 'Normal', 'tools-adapter' ) ] );
		$this->add_control( 'button_color', [ 'label' => esc_html__( 'Texte', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .ta-pc__btn' => 'color: {{VALUE}};', '{{WRAPPER}} .ta-pc__btn svg' => 'fill: {{VALUE}};' ] ] );
		$this->add_group_control( Group_Control_Background::get_type(), [ 'name' => 'button_bg', 'types' => [ 'classic', 'gradient' ], 'exclude' => [ 'image' ], 'selector' => '{{WRAPPER}} .ta-pc__btn' ] );
		$this->end_controls_tab();
		$this->start_controls_tab( 'button_hover', [ 'label' => esc_html__( 'Survol', 'tools-adapter' ) ] );
		$this->add_control( 'button_color_hover', [ 'label' => esc_html__( 'Texte', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .ta-pc__btn:hover' => 'color: {{VALUE}};', '{{WRAPPER}} .ta-pc__btn:hover svg' => 'fill: {{VALUE}};' ] ] );
		$this->add_group_control( Group_Control_Background::get_type(), [ 'name' => 'button_bg_hover', 'types' => [ 'classic', 'gradient' ], 'exclude' => [ 'image' ], 'selector' => '{{WRAPPER}} .ta-pc__btn:hover' ] );
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_group_control( Group_Control_Border::get_type(), [ 'name' => 'button_border', 'selector' => '{{WRAPPER}} .ta-pc__btn', 'separator' => 'before' ] );
		$this->add_responsive_control( 'button_padding', [ 'label' => esc_html__( 'Espacement interne', 'tools-adapter' ), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => [ 'px', 'em' ], 'selectors' => [ '{{WRAPPER}} .ta-pc__btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ] ] );
		$this->add_control( 'button_radius', [ 'label' => esc_html__( 'Arrondi', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'size_units' => [ 'px', '%' ], 'range' => [ 'px' => [ 'min' => 0, 'max' => 60 ] ], 'selectors' => [ '{{WRAPPER}} .ta-pc__btn' => 'border-radius: {{SIZE}}{{UNIT}};' ] ] );
		$this->add_control( 'button_full', [ 'label' => esc_html__( 'Pleine largeur', 'tools-adapter' ), 'type' => Controls_Manager::SWITCHER, 'return_value' => 'yes', 'default' => 'yes', 'prefix_class' => 'ta-pc--btn-full-' ] );

		$this->end_controls_section();
	}

	/**
	 * Format a price with its currency (free text such as "Sur devis" is kept as is).
	 *
	 * @param string $price    Raw price.
	 * @param string $currency Currency symbol.
	 * @param string $position "before" or "after".
	 * @return string Escaped HTML.
	 */
	private function format_price( $price, $currency, $position ) {
		$price = trim( (string) $price );
		if ( '' === $price || '' === $currency || ! preg_match( '/^[0-9\s.,]+$/u', $price ) ) {
			return esc_html( $price );
		}
		return 'before' === $position
			? esc_html( $currency . $price )
			: esc_html( $price ) . '&nbsp;' . esc_html( $currency );
	}

	protected function render() {
		$settings  = $this->get_settings_for_display();
		$title_tag = in_array( $settings['title_tag'] ?? 'h3', [ 'h2', 'h3', 'h4', 'div' ], true ) ? $settings['title_tag'] : 'h3';
		$placement = in_array( $settings['badge_placement'] ?? 'corner', [ 'corner', 'above', 'inline' ], true ) ? $settings['badge_placement'] : 'corner';
		$currency  = (string) ( $settings['currency'] ?? '€' );
		$cur_pos   = 'before' === ( $settings['currency_position'] ?? 'after' ) ? 'before' : 'after';
		$unit      = \tools_adapter_translate( $settings['unit'] ?? '' );
		$rows      = is_array( $settings['rows'] ?? null ) ? $settings['rows'] : [];
		$badge     = ! empty( $settings['badge'] ) ? '<span class="ta-pc__badge ta-pc__badge--' . esc_attr( $placement ) . '">' . esc_html( \tools_adapter_translate( $settings['badge'] ) ) . '</span>' : '';
		$btn_url   = $settings['button_link']['url'] ?? '';
		?>
		<div class="ta-pc<?php echo $badge && 'corner' === $placement ? ' ta-pc--has-corner-badge' : ''; ?>">
			<?php
			if ( 'corner' === $placement ) {
				echo $badge; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
			?>
			<div class="ta-pc__header">
				<?php
				if ( 'above' === $placement ) {
					echo $badge; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				}
				?>
				<?php if ( ! empty( $settings['title'] ) || ( 'inline' === $placement && $badge ) ) : ?>
					<<?php echo esc_attr( $title_tag ); ?> class="ta-pc__title">
						<?php echo esc_html( \tools_adapter_translate( $settings['title'] ?? '' ) ); ?>
						<?php
						if ( 'inline' === $placement ) {
							echo $badge; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						}
						?>
					</<?php echo esc_attr( $title_tag ); ?>>
				<?php endif; ?>
				<?php if ( ! empty( $settings['description'] ) ) : ?>
					<p class="ta-pc__desc"><?php echo esc_html( \tools_adapter_translate( $settings['description'] ) ); ?></p>
				<?php endif; ?>
			</div>

			<?php if ( $rows ) : ?>
				<ul class="ta-pc__rows">
					<?php
					foreach ( $rows as $row ) :
						$row_unit = '' !== trim( (string) ( $row['unit'] ?? '' ) ) ? \tools_adapter_translate( $row['unit'] ) : $unit;
						$classes  = 'ta-pc__row elementor-repeater-item-' . ( $row['_id'] ?? '' ) . ( 'yes' === ( $row['highlight'] ?? '' ) ? ' ta-pc__row--highlight' : '' );
						?>
						<li class="<?php echo esc_attr( $classes ); ?>">
							<span class="ta-pc__label-wrap">
								<span class="ta-pc__label"><?php echo esc_html( \tools_adapter_translate( $row['label'] ?? '' ) ); ?></span>
								<?php if ( ! empty( $row['note'] ) ) : ?>
									<span class="ta-pc__note"><?php echo esc_html( \tools_adapter_translate( $row['note'] ) ); ?></span>
								<?php endif; ?>
							</span>
							<span class="ta-pc__price">
								<?php if ( ! empty( $row['old_price'] ) ) : ?>
									<del class="ta-pc__old"><?php echo $this->format_price( $row['old_price'], $currency, $cur_pos ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></del>
								<?php endif; ?>
								<strong class="ta-pc__amount"><?php echo $this->format_price( \tools_adapter_translate( $row['price'] ?? '' ), $currency, $cur_pos ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></strong>
								<?php if ( '' !== $row_unit ) : ?>
									<span class="ta-pc__unit"><?php echo esc_html( $row_unit ); ?></span>
								<?php endif; ?>
							</span>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>

			<?php if ( ! empty( $settings['footer_text'] ) || ( ! empty( $settings['button_text'] ) && $btn_url ) ) : ?>
				<div class="ta-pc__footer">
					<?php if ( ! empty( $settings['footer_text'] ) ) : ?>
						<p class="ta-pc__footer-text"><?php echo esc_html( \tools_adapter_translate( $settings['footer_text'] ) ); ?></p>
					<?php endif; ?>
					<?php
					if ( ! empty( $settings['button_text'] ) && $btn_url ) :
						$target = ! empty( $settings['button_link']['is_external'] ) ? ' target="_blank" rel="noopener"' : '';
						?>
						<a class="ta-pc__btn" href="<?php echo esc_url( $btn_url ); ?>"<?php echo $target; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
							<?php if ( ! empty( $settings['button_icon']['value'] ) ) : ?>
								<?php Icons_Manager::render_icon( $settings['button_icon'], [ 'aria-hidden' => 'true' ] ); ?>
							<?php endif; ?>
							<span><?php echo esc_html( \tools_adapter_translate( $settings['button_text'] ) ); ?></span>
						</a>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>
		<?php
	}
}
