<?php
namespace ToolsAdapter\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Widget: Price Filter
 *
 * Filtre WooCommerce par plage de tarifs (AJAX sans rechargement).
 */
class Price_Filter extends Widget_Base {

	public function get_name() {
		return 'tools-adapter-price-filter';
	}

	public function get_title() {
		return esc_html__( 'Filtre par Tarifs', 'tools-adapter' );
	}

	public function get_icon() {
		return 'eicon-price-table';
	}

	public function get_categories() {
		return [ 'tools-adapter', 'woocommerce-elements' ];
	}

	public function get_keywords() {
		return [ 'woocommerce', 'price', 'filter', 'tarif', 'slider', 'shop', 'ajax' ];
	}

	public function get_style_depends() {
		return [ 'tools-adapter-price-filter' ];
	}

	public function get_script_depends() {
		return [ 'tools-adapter-price-filter', 'tools-adapter-archive' ];
	}

	protected function register_controls() {
		/* ═══════════════ CONTENT ═══════════════ */
		$this->start_controls_section(
			'section_content',
			[
				'label' => esc_html__( 'Contenu', 'tools-adapter' ),
			]
		);

		$this->add_control(
			'title',
			[
				'label'       => esc_html__( 'Titre', 'tools-adapter' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Filtrer par prix', 'tools-adapter' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'show_title',
			[
				'label'        => esc_html__( 'Afficher le titre', 'tools-adapter' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'heading_html_tag',
			[
				'label'     => esc_html__( 'Balise HTML titre', 'tools-adapter' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'h3',
				'options'   => [
					'h2'   => 'H2',
					'h3'   => 'H3',
					'h4'   => 'H4',
					'h5'   => 'H5',
					'h6'   => 'H6',
					'div'  => 'div',
					'p'    => 'p',
					'span' => 'span',
				],
				'condition' => [
					'show_title' => 'yes',
				],
			]
		);

		$this->add_control(
			'price_label',
			[
				'label'   => esc_html__( 'Libellé prix', 'tools-adapter' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Prix :', 'tools-adapter' ),
			]
		);

		$this->add_control(
			'min_price',
			[
				'label'       => esc_html__( 'Prix minimum (catalogue)', 'tools-adapter' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 0,
				'min'         => 0,
				'description' => esc_html__( 'Laisser 0 pour utiliser automatiquement le prix le plus bas WooCommerce.', 'tools-adapter' ),
			]
		);

		$this->add_control(
			'max_price',
			[
				'label'       => esc_html__( 'Prix maximum (catalogue)', 'tools-adapter' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 0,
				'min'         => 0,
				'description' => esc_html__( 'Laisser 0 pour utiliser automatiquement le prix le plus haut WooCommerce.', 'tools-adapter' ),
			]
		);

		$this->add_control(
			'step',
			[
				'label'   => esc_html__( 'Pas', 'tools-adapter' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 1,
				'min'     => 0.01,
				'step'    => 0.01,
			]
		);

		$this->add_control(
			'show_inputs',
			[
				'label'        => esc_html__( 'Champs numériques', 'tools-adapter' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'min_label',
			[
				'label'   => esc_html__( 'Libellé Min', 'tools-adapter' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Min', 'tools-adapter' ),
				'condition' => [
					'show_inputs' => 'yes',
				],
			]
		);

		$this->add_control(
			'max_label',
			[
				'label'   => esc_html__( 'Libellé Max', 'tools-adapter' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Max', 'tools-adapter' ),
				'condition' => [
					'show_inputs' => 'yes',
				],
			]
		);

		$this->add_control(
			'show_filter_button',
			[
				'label'        => esc_html__( 'Bouton Filtrer', 'tools-adapter' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Afficher', 'tools-adapter' ),
				'label_off'    => esc_html__( 'Masquer', 'tools-adapter' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'description'  => esc_html__( 'Masqué automatiquement si le filtrage au curseur est activé.', 'tools-adapter' ),
				'condition'    => [
					'ajax_on_change!' => 'yes',
				],
			]
		);

		$this->add_control(
			'button_text',
			[
				'label'     => esc_html__( 'Texte du bouton Filtrer', 'tools-adapter' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'Filtrer', 'tools-adapter' ),
				'condition' => [
					'show_filter_button' => 'yes',
					'ajax_on_change!'    => 'yes',
				],
			]
		);

		$this->add_control(
			'show_reset',
			[
				'label'        => esc_html__( 'Bouton Réinitialiser', 'tools-adapter' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Afficher', 'tools-adapter' ),
				'label_off'    => esc_html__( 'Masquer', 'tools-adapter' ),
				'return_value' => 'yes',
				'default'      => '',
			]
		);

		$this->add_control(
			'reset_text',
			[
				'label'     => esc_html__( 'Texte Réinitialiser', 'tools-adapter' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'Réinitialiser', 'tools-adapter' ),
				'condition' => [
					'show_reset' => 'yes',
				],
			]
		);

		$this->end_controls_section();

		/* ═══════════════ AJAX ═══════════════ */
		$this->start_controls_section(
			'section_ajax',
			[
				'label' => esc_html__( 'AJAX', 'tools-adapter' ),
			]
		);

		$this->add_control(
			'enable_ajax',
			[
				'label'        => esc_html__( 'Filtrage AJAX', 'tools-adapter' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'description'  => esc_html__( 'Met à jour la grille produits sans recharger la page.', 'tools-adapter' ),
			]
		);

		$this->add_control(
			'ajax_on_change',
			[
				'label'        => esc_html__( 'Filtrer au déplacement du curseur', 'tools-adapter' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
				'description'  => esc_html__( 'Filtre automatiquement. Le bouton Filtrer est alors masqué.', 'tools-adapter' ),
				'condition'    => [
					'enable_ajax' => 'yes',
				],
			]
		);

		$this->add_control(
			'ajax_debounce',
			[
				'label'     => esc_html__( 'Délai (ms)', 'tools-adapter' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 400,
				'min'       => 100,
				'step'      => 50,
				'condition' => [
					'enable_ajax'    => 'yes',
					'ajax_on_change' => 'yes',
				],
			]
		);

		$this->add_control(
			'products_selector',
			[
				'label'       => esc_html__( 'Sélecteur grille produits', 'tools-adapter' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '.ta-products-archive__grid, ul.products',
				'label_block' => true,
				'description' => esc_html__( 'Avec Archive Produits, laissez la valeur par défaut. Sinon : ul.products', 'tools-adapter' ),
				'condition'   => [
					'enable_ajax' => 'yes',
				],
			]
		);

		$this->add_control(
			'result_count_selector',
			[
				'label'       => esc_html__( 'Sélecteur compteur résultats', 'tools-adapter' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '.woocommerce-result-count',
				'label_block' => true,
				'condition'   => [
					'enable_ajax' => 'yes',
				],
			]
		);

		$this->add_control(
			'update_url',
			[
				'label'        => esc_html__( 'Mettre à jour l’URL', 'tools-adapter' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => [
					'enable_ajax' => 'yes',
				],
			]
		);

		$this->add_control(
			'scroll_to_products',
			[
				'label'        => esc_html__( 'Scroll vers les produits', 'tools-adapter' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
				'condition'    => [
					'enable_ajax' => 'yes',
				],
			]
		);

		$this->add_control(
			'target_url',
			[
				'label'       => esc_html__( 'URL de destination (sans AJAX)', 'tools-adapter' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => esc_html__( 'Laisser vide = page boutique', 'tools-adapter' ),
				'condition'   => [
					'enable_ajax!' => 'yes',
				],
			]
		);

		$this->end_controls_section();

		/* ═══════════════ STYLE: BOX ═══════════════ */
		$this->start_controls_section(
			'section_style_box',
			[
				'label' => esc_html__( 'Conteneur', 'tools-adapter' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'box_background',
				'types'    => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .ta-price-filter',
			]
		);

		$this->add_responsive_control(
			'box_padding',
			[
				'label'      => esc_html__( 'Padding', 'tools-adapter' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'default'    => [
					'top'      => '4',
					'right'    => '0',
					'bottom'   => '4',
					'left'     => '0',
					'unit'     => 'px',
					'isLinked' => false,
				],
				'selectors'  => [
					'{{WRAPPER}} .ta-price-filter' => '--ta-box-pad-top: {{TOP}}{{UNIT}}; --ta-box-pad-right: {{RIGHT}}{{UNIT}}; --ta-box-pad-bottom: {{BOTTOM}}{{UNIT}}; --ta-box-pad-left: {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'box_gap',
			[
				'label'      => esc_html__( 'Espacement interne', 'tools-adapter' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 60,
					],
				],
				'default'    => [
					'size' => 14,
					'unit' => 'px',
				],
				'selectors'  => [
					'{{WRAPPER}} .ta-price-filter' => '--ta-box-gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'box_border',
				'selector' => '{{WRAPPER}} .ta-price-filter',
			]
		);

		$this->add_responsive_control(
			'box_radius',
			[
				'label'      => esc_html__( 'Border radius', 'tools-adapter' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .ta-price-filter' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'box_shadow',
				'selector' => '{{WRAPPER}} .ta-price-filter',
			]
		);

		$this->end_controls_section();

		/* ═══════════════ STYLE: TITLE ═══════════════ */
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
					'{{WRAPPER}} .ta-price-filter__title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .ta-price-filter__title',
			]
		);

		$this->add_responsive_control(
			'title_align',
			[
				'label'     => esc_html__( 'Alignement', 'tools-adapter' ),
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
				'selectors' => [
					'{{WRAPPER}} .ta-price-filter__title' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'title_spacing',
			[
				'label'      => esc_html__( 'Marge bas', 'tools-adapter' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 60,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .ta-price-filter__title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		/* ═══════════════ STYLE: SLIDER ═══════════════ */
		$this->start_controls_section(
			'section_style_slider',
			[
				'label' => esc_html__( 'Curseur / Slider', 'tools-adapter' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'accent_color',
			[
				'label'     => esc_html__( 'Couleur plage active', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#C9A84C',
				'selectors' => [
					'{{WRAPPER}} .ta-price-filter' => '--ta-accent: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'track_color',
			[
				'label'     => esc_html__( 'Couleur piste', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#dddddd',
				'selectors' => [
					'{{WRAPPER}} .ta-price-filter' => '--ta-track: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'track_height',
			[
				'label'      => esc_html__( 'Épaisseur de la ligne', 'tools-adapter' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 1,
						'max' => 6,
					],
				],
				'default'    => [
					'size' => 2,
				],
				'selectors'  => [
					'{{WRAPPER}} .ta-price-filter' => '--ta-track-height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'show_ticks',
			[
				'label'        => esc_html__( 'Graduations', 'tools-adapter' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'ticks_count',
			[
				'label'     => esc_html__( 'Nombre de graduations', 'tools-adapter' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 5,
				'min'       => 2,
				'max'       => 11,
				'condition' => [
					'show_ticks' => 'yes',
				],
			]
		);

		$this->add_control(
			'tick_color',
			[
				'label'     => esc_html__( 'Couleur graduations', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#bbbbbb',
				'selectors' => [
					'{{WRAPPER}} .ta-price-filter' => '--ta-tick: {{VALUE}};',
				],
				'condition' => [
					'show_ticks' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'thumb_size',
			[
				'label'      => esc_html__( 'Taille poignée', 'tools-adapter' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 12,
						'max' => 36,
					],
				],
				'default'    => [
					'size' => 20,
				],
				'selectors'  => [
					'{{WRAPPER}} .ta-price-filter' => '--ta-thumb-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'thumb_color',
			[
				'label'     => esc_html__( 'Couleur poignée', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ta-price-filter' => '--ta-thumb: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'thumb_border_color',
			[
				'label'     => esc_html__( 'Bordure poignée', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .ta-price-filter' => '--ta-thumb-border: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		/* ═══════════════ STYLE: VALUES ═══════════════ */
		$this->start_controls_section(
			'section_style_values',
			[
				'label' => esc_html__( 'Libellé prix', 'tools-adapter' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'values_color',
			[
				'label'     => esc_html__( 'Couleur texte', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#555555',
				'selectors' => [
					'{{WRAPPER}} .ta-price-filter__values' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'values_strong_color',
			[
				'label'     => esc_html__( 'Couleur montants', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#222222',
				'selectors' => [
					'{{WRAPPER}} .ta-price-filter__values strong' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'values_typography',
				'selector' => '{{WRAPPER}} .ta-price-filter__values',
			]
		);

		$this->add_responsive_control(
			'values_align',
			[
				'label'     => esc_html__( 'Alignement', 'tools-adapter' ),
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
				'selectors' => [
					'{{WRAPPER}} .ta-price-filter__values' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		/* ═══════════════ STYLE: INPUTS ═══════════════ */
		$this->start_controls_section(
			'section_style_inputs',
			[
				'label'     => esc_html__( 'Champs numériques', 'tools-adapter' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_inputs' => 'yes',
				],
			]
		);

		$this->add_control(
			'input_label_color',
			[
				'label'     => esc_html__( 'Couleur libellés', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ta-price-filter__field' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'input_text_color',
			[
				'label'     => esc_html__( 'Couleur texte', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ta-price-filter__input' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'input_bg',
			[
				'label'     => esc_html__( 'Fond', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ta-price-filter__input' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'input_typography',
				'selector' => '{{WRAPPER}} .ta-price-filter__input',
			]
		);

		$this->add_responsive_control(
			'input_padding',
			[
				'label'      => esc_html__( 'Padding', 'tools-adapter' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'default'    => [
					'top'      => '10',
					'right'    => '12',
					'bottom'   => '10',
					'left'     => '12',
					'unit'     => 'px',
					'isLinked' => false,
				],
				'selectors'  => [
					'{{WRAPPER}} .ta-price-filter' => '--ta-input-pad-top: {{TOP}}{{UNIT}}; --ta-input-pad-right: {{RIGHT}}{{UNIT}}; --ta-input-pad-bottom: {{BOTTOM}}{{UNIT}}; --ta-input-pad-left: {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'input_border',
				'selector' => '{{WRAPPER}} .ta-price-filter__input',
			]
		);

		$this->add_responsive_control(
			'input_radius',
			[
				'label'      => esc_html__( 'Border radius', 'tools-adapter' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .ta-price-filter__input' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'input_focus_color',
			[
				'label'     => esc_html__( 'Bordure focus', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ta-price-filter__input:focus' => 'border-color: {{VALUE}}; box-shadow: 0 0 0 3px color-mix(in srgb, {{VALUE}} 25%, transparent);',
				],
			]
		);

		$this->end_controls_section();

		/* ═══════════════ STYLE: BUTTONS ═══════════════ */
		$this->start_controls_section(
			'section_style_buttons',
			[
				'label' => esc_html__( 'Boutons', 'tools-adapter' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'button_typography',
				'selector' => '{{WRAPPER}} .ta-price-filter__submit, {{WRAPPER}} .ta-price-filter__reset',
			]
		);

		$this->add_responsive_control(
			'button_padding',
			[
				'label'      => esc_html__( 'Padding', 'tools-adapter' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'default'    => [
					'top'      => '12',
					'right'    => '22',
					'bottom'   => '12',
					'left'     => '22',
					'unit'     => 'px',
					'isLinked' => false,
				],
				'selectors'  => [
					'{{WRAPPER}} .ta-price-filter' => '--ta-btn-pad-top: {{TOP}}{{UNIT}}; --ta-btn-pad-right: {{RIGHT}}{{UNIT}}; --ta-btn-pad-bottom: {{BOTTOM}}{{UNIT}}; --ta-btn-pad-left: {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'button_radius',
			[
				'label'      => esc_html__( 'Border radius', 'tools-adapter' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'default'    => [
					'size' => 6,
					'unit' => 'px',
				],
				'selectors'  => [
					'{{WRAPPER}} .ta-price-filter__submit, {{WRAPPER}} .ta-price-filter__reset' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'buttons_gap',
			[
				'label'      => esc_html__( 'Espacement boutons', 'tools-adapter' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 40,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .ta-price-filter__actions' => 'gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'heading_submit',
			[
				'label'     => esc_html__( 'Bouton Filtrer', 'tools-adapter' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'show_filter_button' => 'yes',
					'ajax_on_change!'    => 'yes',
				],
			]
		);

		$this->start_controls_tabs(
			'submit_tabs',
			[
				'condition' => [
					'show_filter_button' => 'yes',
					'ajax_on_change!'    => 'yes',
				],
			]
		);

		$this->start_controls_tab(
			'submit_normal',
			[
				'label' => esc_html__( 'Normal', 'tools-adapter' ),
			]
		);

		$this->add_control(
			'button_bg',
			[
				'label'     => esc_html__( 'Fond', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#C9A84C',
				'selectors' => [
					'{{WRAPPER}} .ta-price-filter__submit' => 'background-color: {{VALUE}}; border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_color',
			[
				'label'     => esc_html__( 'Texte', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .ta-price-filter__submit' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'submit_shadow',
				'selector' => '{{WRAPPER}} .ta-price-filter__submit',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'submit_hover',
			[
				'label' => esc_html__( 'Survol', 'tools-adapter' ),
			]
		);

		$this->add_control(
			'button_bg_hover',
			[
				'label'     => esc_html__( 'Fond', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ta-price-filter__submit:hover' => 'background-color: {{VALUE}}; border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_color_hover',
			[
				'label'     => esc_html__( 'Texte', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ta-price-filter__submit:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'submit_shadow_hover',
				'selector' => '{{WRAPPER}} .ta-price-filter__submit:hover',
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_control(
			'heading_reset',
			[
				'label'     => esc_html__( 'Bouton Réinitialiser', 'tools-adapter' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'show_reset' => 'yes',
				],
			]
		);

		$this->start_controls_tabs(
			'reset_tabs',
			[
				'condition' => [
					'show_reset' => 'yes',
				],
			]
		);

		$this->start_controls_tab(
			'reset_normal',
			[
				'label' => esc_html__( 'Normal', 'tools-adapter' ),
			]
		);

		$this->add_control(
			'reset_bg',
			[
				'label'     => esc_html__( 'Fond', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ta-price-filter__reset' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'reset_color',
			[
				'label'     => esc_html__( 'Texte', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ta-price-filter__reset' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'reset_border_color',
			[
				'label'     => esc_html__( 'Bordure', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ta-price-filter__reset' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'reset_hover',
			[
				'label' => esc_html__( 'Survol', 'tools-adapter' ),
			]
		);

		$this->add_control(
			'reset_bg_hover',
			[
				'label'     => esc_html__( 'Fond', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ta-price-filter__reset:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'reset_color_hover',
			[
				'label'     => esc_html__( 'Texte', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ta-price-filter__reset:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'reset_border_color_hover',
			[
				'label'     => esc_html__( 'Bordure', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ta-price-filter__reset:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();

		/* ═══════════════ STYLE: LOADING ═══════════════ */
		$this->start_controls_section(
			'section_style_loading',
			[
				'label'     => esc_html__( 'État chargement', 'tools-adapter' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'enable_ajax' => 'yes',
				],
			]
		);

		$this->add_control(
			'loading_overlay',
			[
				'label'     => esc_html__( 'Overlay grille', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(255,255,255,0.65)',
				'selectors' => [
					'{{WRAPPER}}' => '--ta-loading-overlay: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'loading_spinner',
			[
				'label'     => esc_html__( 'Couleur spinner', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#C9A84C',
				'selectors' => [
					'{{WRAPPER}}' => '--ta-loading-spinner: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Detect min/max product prices from WooCommerce catalog.
	 *
	 * @return array{min: float, max: float}
	 */
	private function detect_catalog_prices() {
		global $wpdb;

		$detected_min = 0.0;
		$detected_max = 0.0;

		// Prefer WooCommerce product meta lookup table (accurate + fast).
		$lookup_table = $wpdb->prefix . 'wc_product_meta_lookup';
		$table_exists = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $lookup_table ) );

		if ( $table_exists === $lookup_table ) {
			$prices = $wpdb->get_row(
				"SELECT MIN(min_price) AS min_price, MAX(max_price) AS max_price
				 FROM {$lookup_table}
				 WHERE min_price IS NOT NULL
				   AND max_price IS NOT NULL
				   AND min_price >= 0"
			);

			if ( $prices ) {
				$detected_min = floatval( $prices->min_price );
				$detected_max = floatval( $prices->max_price );
			}
		}

		// Fallback: post meta _price on published products.
		if ( $detected_max <= 0 ) {
			$prices = $wpdb->get_row(
				"SELECT MIN( CAST( pm.meta_value AS DECIMAL(20,6) ) ) AS min_price,
				        MAX( CAST( pm.meta_value AS DECIMAL(20,6) ) ) AS max_price
				 FROM {$wpdb->postmeta} pm
				 INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
				 WHERE pm.meta_key = '_price'
				   AND pm.meta_value != ''
				   AND pm.meta_value REGEXP '^[0-9]+(\\.[0-9]+)?'
				   AND p.post_type IN ('product', 'product_variation')
				   AND p.post_status = 'publish'"
			);

			if ( $prices && null !== $prices->min_price ) {
				$detected_min = floatval( $prices->min_price );
				$detected_max = floatval( $prices->max_price );
			}
		}

		if ( $detected_max <= $detected_min ) {
			$detected_max = $detected_min > 0 ? $detected_min * 2 : 100;
		}

		return [
			'min' => max( 0, $detected_min ),
			'max' => max( 0, $detected_max ),
		];
	}

	/**
	 * Resolve min/max price from settings or WooCommerce catalog.
	 *
	 * @param array $settings Widget settings.
	 * @return array{min: float, max: float}
	 */
	private function resolve_price_bounds( array $settings ) {
		$manual_min = floatval( $settings['min_price'] );
		$manual_max = floatval( $settings['max_price'] );
		$detected   = $this->detect_catalog_prices();

		// 0 = auto (WooCommerce catalog). Manual values override when > 0.
		$min = $manual_min > 0 ? $manual_min : $detected['min'];
		$max = $manual_max > 0 ? $manual_max : $detected['max'];

		if ( $max < $min ) {
			$max = $min;
		}

		return [
			'min' => $min,
			'max' => $max,
		];
	}

	/**
	 * Shop / target form action URL.
	 *
	 * @param array $settings Widget settings.
	 * @return string
	 */
	private function get_form_action( array $settings ) {
		if ( ! empty( $settings['target_url']['url'] ) ) {
			return $settings['target_url']['url'];
		}

		if ( function_exists( 'wc_get_page_permalink' ) ) {
			$shop = wc_get_page_permalink( 'shop' );
			if ( $shop ) {
				return $shop;
			}
		}

		return home_url( '/' );
	}

	/**
	 * Current taxonomy context for AJAX (category archive).
	 *
	 * @return array{taxonomy: string, term_id: int}
	 */
	private function get_taxonomy_context() {
		if ( is_tax() || is_product_category() || is_product_tag() ) {
			$term = get_queried_object();
			if ( $term && ! empty( $term->taxonomy ) && ! empty( $term->term_id ) ) {
				return [
					'taxonomy' => $term->taxonomy,
					'term_id'  => (int) $term->term_id,
				];
			}
		}

		return [
			'taxonomy' => '',
			'term_id'  => 0,
		];
	}

	protected function render() {
		if ( ! class_exists( 'WooCommerce' ) ) {
			if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
				echo '<p>' . esc_html__( 'WooCommerce est requis pour ce widget.', 'tools-adapter' ) . '</p>';
			}
			return;
		}

		$settings    = $this->get_settings_for_display();
		$bounds      = $this->resolve_price_bounds( $settings );
		$step        = max( 0.01, floatval( $settings['step'] ) );
		$action      = $this->get_form_action( $settings );
		$enable_ajax = ( 'yes' === $settings['enable_ajax'] );
		$tax_context = $this->get_taxonomy_context();

		$current_min = isset( $_GET['min_price'] ) ? floatval( wp_unslash( $_GET['min_price'] ) ) : $bounds['min']; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$current_max = isset( $_GET['max_price'] ) ? floatval( wp_unslash( $_GET['max_price'] ) ) : $bounds['max']; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		$current_min = max( $bounds['min'], min( $current_min, $bounds['max'] ) );
		$current_max = max( $bounds['min'], min( $current_max, $bounds['max'] ) );

		if ( $current_min > $current_max ) {
			$current_min = $bounds['min'];
			$current_max = $bounds['max'];
		}

		$currency = function_exists( 'get_woocommerce_currency_symbol' )
			? get_woocommerce_currency_symbol()
			: '€';

		$heading_tag = isset( $settings['heading_html_tag'] ) ? $settings['heading_html_tag'] : 'h3';
		$allowed     = [ 'h2', 'h3', 'h4', 'h5', 'h6', 'div', 'p', 'span' ];
		if ( ! in_array( $heading_tag, $allowed, true ) ) {
			$heading_tag = 'h3';
		}

		$uid = 'ta-price-' . $this->get_id();

		$ajax_on_change = ( 'yes' === $settings['enable_ajax'] && 'yes' === $settings['ajax_on_change'] );
		// Hide Filtrer when live filter is on; otherwise respect the switcher.
		$show_filter_btn = $ajax_on_change ? false : ( 'yes' === ( $settings['show_filter_button'] ?? 'yes' ) );
		$show_reset_btn  = ( 'yes' === $settings['show_reset'] );
		$show_actions    = $show_filter_btn || $show_reset_btn;

		$form_classes = [ 'ta-price-filter' ];
		if ( $enable_ajax ) {
			$form_classes[] = 'ta-price-filter--ajax';
		}
		if ( $ajax_on_change ) {
			$form_classes[] = 'ta-price-filter--live';
		}

		$show_ticks  = ( 'yes' === ( $settings['show_ticks'] ?? 'yes' ) );
		$ticks_count = max( 2, min( 11, intval( $settings['ticks_count'] ?? 5 ) ) );

		$orderby = isset( $_GET['orderby'] ) ? sanitize_text_field( wp_unslash( $_GET['orderby'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		?>
		<form
			class="<?php echo esc_attr( implode( ' ', $form_classes ) ); ?>"
			method="get"
			action="<?php echo esc_url( $action ); ?>"
			data-min="<?php echo esc_attr( $bounds['min'] ); ?>"
			data-max="<?php echo esc_attr( $bounds['max'] ); ?>"
			data-step="<?php echo esc_attr( $step ); ?>"
			data-ajax="<?php echo $enable_ajax ? '1' : '0'; ?>"
			data-ajax-on-change="<?php echo $ajax_on_change ? '1' : '0'; ?>"
			data-debounce="<?php echo esc_attr( isset( $settings['ajax_debounce'] ) ? $settings['ajax_debounce'] : 400 ); ?>"
			data-products-selector="<?php echo esc_attr( $settings['products_selector'] ?: 'ul.products' ); ?>"
			data-result-count-selector="<?php echo esc_attr( $settings['result_count_selector'] ?: '.woocommerce-result-count' ); ?>"
			data-update-url="<?php echo ( 'yes' === $settings['update_url'] ) ? '1' : '0'; ?>"
			data-scroll="<?php echo ( 'yes' === $settings['scroll_to_products'] ) ? '1' : '0'; ?>"
			data-taxonomy="<?php echo esc_attr( $tax_context['taxonomy'] ); ?>"
			data-term-id="<?php echo esc_attr( (string) $tax_context['term_id'] ); ?>"
			data-orderby="<?php echo esc_attr( $orderby ); ?>"
			data-currency="<?php echo esc_attr( $currency ); ?>"
		>
			<?php if ( 'yes' === $settings['show_title'] && ! empty( $settings['title'] ) ) : ?>
				<<?php echo esc_attr( $heading_tag ); ?> class="ta-price-filter__title">
					<?php echo esc_html( \tools_adapter_translate( $settings['title'] ) ); ?>
				</<?php echo esc_attr( $heading_tag ); ?>>
			<?php endif; ?>

			<div class="ta-price-filter__slider" id="<?php echo esc_attr( $uid ); ?>">
				<div class="ta-price-filter__track" aria-hidden="true">
					<div class="ta-price-filter__range" data-range></div>
				</div>
				<?php if ( $show_ticks ) : ?>
					<div class="ta-price-filter__ticks" aria-hidden="true">
						<?php for ( $i = 0; $i < $ticks_count; $i++ ) : ?>
							<span class="ta-price-filter__tick<?php echo ( 0 === $i || $i === $ticks_count - 1 ) ? ' is-edge' : ''; ?>"></span>
						<?php endfor; ?>
					</div>
				<?php endif; ?>
				<input
					type="range"
					class="ta-price-filter__thumb ta-price-filter__thumb--min"
					min="<?php echo esc_attr( $bounds['min'] ); ?>"
					max="<?php echo esc_attr( $bounds['max'] ); ?>"
					step="<?php echo esc_attr( $step ); ?>"
					value="<?php echo esc_attr( $current_min ); ?>"
					aria-label="<?php echo esc_attr__( 'Prix minimum', 'tools-adapter' ); ?>"
					data-thumb="min"
				>
				<input
					type="range"
					class="ta-price-filter__thumb ta-price-filter__thumb--max"
					min="<?php echo esc_attr( $bounds['min'] ); ?>"
					max="<?php echo esc_attr( $bounds['max'] ); ?>"
					step="<?php echo esc_attr( $step ); ?>"
					value="<?php echo esc_attr( $current_max ); ?>"
					aria-label="<?php echo esc_attr__( 'Prix maximum', 'tools-adapter' ); ?>"
					data-thumb="max"
				>
			</div>

			<div class="ta-price-filter__values">
				<span class="ta-price-filter__label">
					<?php echo esc_html( \tools_adapter_translate( $settings['price_label'] ?: __( 'Prix :', 'tools-adapter' ) ) ); ?>
					<strong data-display-min><?php echo esc_html( $this->format_price( $current_min, $currency ) ); ?></strong>
					–
					<strong data-display-max><?php echo esc_html( $this->format_price( $current_max, $currency ) ); ?></strong>
				</span>
			</div>

			<?php if ( 'yes' === $settings['show_inputs'] ) : ?>
				<div class="ta-price-filter__inputs">
					<label class="ta-price-filter__field">
						<span><?php echo esc_html( \tools_adapter_translate( $settings['min_label'] ?: __( 'Min', 'tools-adapter' ) ) ); ?></span>
						<span class="ta-price-filter__currency"><?php echo esc_html( $currency ); ?></span>
						<input
							type="number"
							class="ta-price-filter__input"
							name="min_price"
							min="<?php echo esc_attr( $bounds['min'] ); ?>"
							max="<?php echo esc_attr( $bounds['max'] ); ?>"
							step="<?php echo esc_attr( $step ); ?>"
							value="<?php echo esc_attr( $current_min ); ?>"
							data-input="min"
						>
					</label>
					<label class="ta-price-filter__field">
						<span><?php echo esc_html( \tools_adapter_translate( $settings['max_label'] ?: __( 'Max', 'tools-adapter' ) ) ); ?></span>
						<span class="ta-price-filter__currency"><?php echo esc_html( $currency ); ?></span>
						<input
							type="number"
							class="ta-price-filter__input"
							name="max_price"
							min="<?php echo esc_attr( $bounds['min'] ); ?>"
							max="<?php echo esc_attr( $bounds['max'] ); ?>"
							step="<?php echo esc_attr( $step ); ?>"
							value="<?php echo esc_attr( $current_max ); ?>"
							data-input="max"
						>
					</label>
				</div>
			<?php else : ?>
				<input type="hidden" name="min_price" value="<?php echo esc_attr( $current_min ); ?>" data-input="min">
				<input type="hidden" name="max_price" value="<?php echo esc_attr( $current_max ); ?>" data-input="max">
			<?php endif; ?>

			<?php
			foreach ( $_GET as $key => $value ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				if ( in_array( $key, [ 'min_price', 'max_price', 'paged' ], true ) ) {
					continue;
				}
				if ( is_array( $value ) ) {
					continue;
				}
				printf(
					'<input type="hidden" name="%1$s" value="%2$s">',
					esc_attr( $key ),
					esc_attr( wp_unslash( $value ) )
				);
			}
			?>

			<?php if ( $show_actions ) : ?>
				<div class="ta-price-filter__actions">
					<?php if ( $show_filter_btn ) : ?>
						<button type="submit" class="ta-price-filter__submit">
							<?php echo esc_html( \tools_adapter_translate( $settings['button_text'] ) ); ?>
						</button>
					<?php endif; ?>
					<?php if ( $show_reset_btn ) : ?>
						<button
							type="button"
							class="ta-price-filter__reset"
							data-reset
							data-href="<?php echo esc_url( $action ); ?>"
						>
							<?php echo esc_html( \tools_adapter_translate( $settings['reset_text'] ) ); ?>
						</button>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<div class="ta-price-filter__status" data-status hidden aria-live="polite"></div>
		</form>
		<?php
	}

	/**
	 * Simple price label for display.
	 *
	 * @param float  $amount   Amount.
	 * @param string $currency Currency symbol.
	 * @return string
	 */
	private function format_price( $amount, $currency ) {
		$decimals = function_exists( 'wc_get_price_decimals' ) ? wc_get_price_decimals() : 2;
		return $currency . number_format_i18n( $amount, $decimals );
	}
}
