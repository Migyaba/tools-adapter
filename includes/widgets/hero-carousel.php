<?php
/**
 * Tools Adapter — Widget Hero Carrousel (Glassmorphism & Staggered Animations)
 *
 * @package ToolsAdapter
 */

namespace ToolsAdapter\Widgets;

use ToolsAdapter\Base_Widget;
use Elementor\Controls_Manager;
use ToolsAdapter\Repeater;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Hero_Carousel extends Base_Widget {

	public function get_name() {
		return 'tools-adapter-hero-carousel';
	}

	public function get_title() {
		return esc_html__( 'Hero Carrousel', 'tools-adapter' );
	}

	public function get_icon() {
		return 'eicon-slider-album';
	}

	public function get_categories() {
		return [ 'tools-adapter' ];
	}

	public function get_style_depends() {
		return [ 'tools-adapter-hero-carousel' ];
	}

	public function get_script_depends() {
		return [ 'tools-adapter-carousel' ];
	}

	protected function register_controls() {

		// =========================================================================
		// CONTENU : DIAPOSITIVES
		// =========================================================================
		$this->start_controls_section(
			'section_slides',
			[
				'label' => esc_html__( 'Diapositives', 'tools-adapter' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'background_image',
			[
				'label'   => esc_html__( 'Image d\'arrière-plan', 'tools-adapter' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => [
					'url' => 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=1920&q=80',
				],
			]
		);

		$repeater->add_control(
			'subtitle',
			[
				'label'       => esc_html__( 'Sur-titre (Sous-titre)', 'tools-adapter' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'HomeOptimize Master Kit', 'tools-adapter' ),
				'placeholder' => esc_html__( 'Ex: HomeOptimize Master Kit', 'tools-adapter' ),
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'title',
			[
				'label'       => esc_html__( 'Titre principal', 'tools-adapter' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'SmartLife Complete Ensemble', 'tools-adapter' ),
				'placeholder' => esc_html__( 'Ex: SmartLife Complete Ensemble', 'tools-adapter' ),
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'title_tag',
			[
				'label'   => esc_html__( 'Balise HTML du titre', 'tools-adapter' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'h1'   => 'H1',
					'h2'   => 'H2',
					'h3'   => 'H3',
					'h4'   => 'H4',
					'span' => 'span',
					'div'  => 'div',
				],
				'default' => 'h1',
			]
		);

		$repeater->add_control(
			'description',
			[
				'label'       => esc_html__( 'Description', 'tools-adapter' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 4,
				'default'     => esc_html__( 'These names can be used for collections of smart home devices that are designed to work together to optimize and automate different aspects of a home.', 'tools-adapter' ),
				'placeholder' => esc_html__( 'Saisissez votre texte de description…', 'tools-adapter' ),
			]
		);

		$repeater->add_control(
			'show_button',
			[
				'label'        => esc_html__( 'Afficher le bouton principal', 'tools-adapter' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			]
		);

		$repeater->add_control(
			'button_text',
			[
				'label'       => esc_html__( 'Texte du bouton principal', 'tools-adapter' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Discovery now', 'tools-adapter' ),
				'condition'   => [ 'show_button' => 'yes' ],
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'button_link',
			[
				'label'       => esc_html__( 'Lien du bouton principal', 'tools-adapter' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => 'https://...',
				'default'     => [ 'url' => '#' ],
				'condition'   => [ 'show_button' => 'yes' ],
			]
		);

		$repeater->add_control(
			'show_secondary_button',
			[
				'label'        => esc_html__( 'Afficher un bouton secondaire', 'tools-adapter' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'no',
				'return_value' => 'yes',
			]
		);

		$repeater->add_control(
			'sec_button_text',
			[
				'label'       => esc_html__( 'Texte bouton secondaire', 'tools-adapter' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'En savoir plus', 'tools-adapter' ),
				'condition'   => [ 'show_secondary_button' => 'yes' ],
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'sec_button_link',
			[
				'label'       => esc_html__( 'Lien bouton secondaire', 'tools-adapter' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => 'https://...',
				'default'     => [ 'url' => '#' ],
				'condition'   => [ 'show_secondary_button' => 'yes' ],
			]
		);

		$repeater->add_control(
			'card_align_override',
			[
				'label'   => esc_html__( 'Alignement de la carte (cette slide)', 'tools-adapter' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					''       => esc_html__( 'Hériter du style général', 'tools-adapter' ),
					'left'   => esc_html__( 'Aligné à gauche', 'tools-adapter' ),
					'center' => esc_html__( 'Centré', 'tools-adapter' ),
					'right'  => esc_html__( 'Aligné à droite', 'tools-adapter' ),
				],
				'default' => '',
			]
		);

		$repeater->add_control(
			'slide_overlay_color',
			[
				'label'       => esc_html__( 'Couleur d\'overlay spécifique', 'tools-adapter' ),
				'type'        => Controls_Manager::COLOR,
				'description' => esc_html__( 'Laissez vide pour utiliser l\'overlay global.', 'tools-adapter' ),
			]
		);

		$this->add_control(
			'slides',
			[
				'label'       => esc_html__( 'Liste des diapositives', 'tools-adapter' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ title }}}',
				'default'     => [
					[
						'subtitle'         => esc_html__( 'HomeOptimize Master Kit', 'tools-adapter' ),
						'title'            => esc_html__( 'SmartLife Complete Ensemble', 'tools-adapter' ),
						'title_tag'        => 'h1',
						'description'      => esc_html__( 'These names can be used for collections of smart home devices that are designed to work together to optimize and automate different aspects of a home.', 'tools-adapter' ),
						'show_button'      => 'yes',
						'button_text'      => esc_html__( 'Discovery now', 'tools-adapter' ),
						'background_image' => [
							'url' => 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=1920&q=80',
						],
					],
					[
						'subtitle'         => esc_html__( 'Collection Harmonie & Confort', 'tools-adapter' ),
						'title'            => esc_html__( 'Intérieurs Contemporains & Épurés', 'tools-adapter' ),
						'title_tag'        => 'h2',
						'description'      => esc_html__( 'Sublimez vos espaces avec des matériaux nobles, un design intemporel et une atmosphère chaleureuse conçue pour votre bien-être.', 'tools-adapter' ),
						'show_button'      => 'yes',
						'button_text'      => esc_html__( 'Explorer la collection', 'tools-adapter' ),
						'background_image' => [
							'url' => 'https://images.unsplash.com/photo-1618221195710-dd6b41faaea6?auto=format&fit=crop&w=1920&q=80',
						],
					],
				],
			]
		);

		$this->end_controls_section();

		// =========================================================================
		// CONTENU : PARAMÈTRES DU CARROUSEL
		// =========================================================================
		$this->start_controls_section(
			'section_carousel_settings',
			[
				'label' => esc_html__( 'Paramètres du Carrousel', 'tools-adapter' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'autoplay',
			[
				'label'        => esc_html__( 'Défilement automatique (Autoplay)', 'tools-adapter' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			]
		);

		$this->add_control(
			'autoplay_speed',
			[
				'label'     => esc_html__( 'Vitesse d\'autoplay (ms)', 'tools-adapter' ),
				'type'      => Controls_Manager::NUMBER,
				'min'       => 1500,
				'max'       => 15000,
				'step'      => 500,
				'default'   => 5000,
				'condition' => [ 'autoplay' => 'yes' ],
			]
		);

		$this->add_control(
			'transition_speed',
			[
				'label'   => esc_html__( 'Vitesse de transition (ms)', 'tools-adapter' ),
				'type'    => Controls_Manager::NUMBER,
				'min'     => 200,
				'max'     => 2000,
				'step'    => 50,
				'default' => 500,
			]
		);

		$this->add_control(
			'show_arrows',
			[
				'label'        => esc_html__( 'Afficher les flèches latérales', 'tools-adapter' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			]
		);

		$this->add_control(
			'show_dots',
			[
				'label'        => esc_html__( 'Afficher les puces de pagination', 'tools-adapter' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			]
		);

		$this->add_control(
			'animation_style',
			[
				'label'   => esc_html__( 'Style d\'animation d\'apparition', 'tools-adapter' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'fade_up'  => esc_html__( 'Fondu vers le haut (Fade In Up)', 'tools-adapter' ),
					'fade_in'  => esc_html__( 'Fondu simple (Fade In)', 'tools-adapter' ),
					'scale_up' => esc_html__( 'Zoom progressif (Scale Up)', 'tools-adapter' ),
				],
				'default' => 'fade_up',
			]
		);

		$this->end_controls_section();

		// =========================================================================
		// STYLE : CONTENEUR DU HERO
		// =========================================================================
		$this->start_controls_section(
			'section_style_container',
			[
				'label' => esc_html__( 'Conteneur Hero', 'tools-adapter' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'hero_height',
			[
				'label'      => esc_html__( 'Hauteur minimale', 'tools-adapter' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'vh' ],
				'range'      => [
					'px' => [ 'min' => 320, 'max' => 1000, 'step' => 10 ],
					'vh' => [ 'min' => 30, 'max' => 100, 'step' => 1 ],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 560,
				],
				'selectors'  => [
					'{{WRAPPER}} .ta-hero-carousel' => '--ta-hero-min-height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'hero_border_radius',
			[
				'label'      => esc_html__( 'Rayon des coins (Border Radius)', 'tools-adapter' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'default'    => [
					'top'      => 24,
					'right'    => 24,
					'bottom'   => 24,
					'left'     => 24,
					'unit'     => 'px',
					'isLinked' => true,
				],
				'selectors'  => [
					'{{WRAPPER}} .ta-hero-carousel' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'hero_overlay_color',
			[
				'label'     => esc_html__( 'Couleur du voile (Overlay)', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(0, 0, 0, 0.15)',
				'selectors' => [
					'{{WRAPPER}} .ta-hero-slide__overlay' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'hero_box_shadow',
				'selector' => '{{WRAPPER}} .ta-hero-carousel',
			]
		);

		$this->add_responsive_control(
			'hero_margin',
			[
				'label'      => esc_html__( 'Marge externe', 'tools-adapter' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .ta-hero-carousel' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		// =========================================================================
		// STYLE : CARTE EN VERRE (GLASSMORPHISM)
		// =========================================================================
		$this->start_controls_section(
			'section_style_glass_card',
			[
				'label' => esc_html__( 'Carte en Verre (Glassmorphism)', 'tools-adapter' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'card_h_align',
			[
				'label'     => esc_html__( 'Position horizontale de la carte', 'tools-adapter' ),
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
					'{{WRAPPER}} .ta-hero-slide' => '--ta-hero-card-h-align: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'card_v_align',
			[
				'label'     => esc_html__( 'Position verticale de la carte', 'tools-adapter' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'flex-start' => [
						'title' => esc_html__( 'Haut', 'tools-adapter' ),
						'icon'  => 'eicon-v-align-top',
					],
					'center'     => [
						'title' => esc_html__( 'Milieu', 'tools-adapter' ),
						'icon'  => 'eicon-v-align-middle',
					],
					'flex-end'   => [
						'title' => esc_html__( 'Bas', 'tools-adapter' ),
						'icon'  => 'eicon-v-align-bottom',
					],
				],
				'default'   => 'center',
				'selectors' => [
					'{{WRAPPER}} .ta-hero-slide' => '--ta-hero-card-v-align: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'card_text_align',
			[
				'label'     => esc_html__( 'Alignement du texte interne', 'tools-adapter' ),
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
				'default'   => 'center',
				'selectors' => [
					'{{WRAPPER}} .ta-hero-glass-card' => 'text-align: {{VALUE}};',
					'{{WRAPPER}} .ta-hero-actions'    => 'justify-content: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'card_max_width',
			[
				'label'      => esc_html__( 'Largeur maximale de la carte', 'tools-adapter' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'vw' ],
				'range'      => [
					'px' => [ 'min' => 280, 'max' => 900, 'step' => 10 ],
					'%'  => [ 'min' => 30, 'max' => 100 ],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 640,
				],
				'selectors'  => [
					'{{WRAPPER}} .ta-hero-glass-card' => 'max-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'card_bg_color',
			[
				'label'     => esc_html__( 'Couleur de fond givré', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(255, 255, 255, 0.58)',
				'selectors' => [
					'{{WRAPPER}} .ta-hero-glass-card' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'card_blur',
			[
				'label'     => esc_html__( 'Intensité du flou d\'arrière-plan (px)', 'tools-adapter' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [ 'min' => 0, 'max' => 50, 'step' => 1 ],
				],
				'default'   => [
					'unit' => 'px',
					'size' => 16,
				],
				'selectors' => [
					'{{WRAPPER}} .ta-hero-glass-card' => '--ta-card-blur: {{SIZE}}px;',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'      => 'card_border',
				'selector'  => '{{WRAPPER}} .ta-hero-glass-card',
				'separator' => 'before',
				'fields_options' => [
					'border' => [
						'default' => 'solid',
					],
					'width'  => [
						'default' => [
							'top'    => '1',
							'right'  => '1',
							'bottom' => '1',
							'left'   => '1',
							'isLinked' => true,
						],
					],
					'color'  => [
						'default' => 'rgba(255, 255, 255, 0.55)',
					],
				],
			]
		);

		$this->add_responsive_control(
			'card_border_radius',
			[
				'label'      => esc_html__( 'Arrondi des coins de la carte', 'tools-adapter' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'default'    => [
					'top'      => 32,
					'right'    => 32,
					'bottom'   => 32,
					'left'     => 32,
					'unit'     => 'px',
					'isLinked' => true,
				],
				'selectors'  => [
					'{{WRAPPER}} .ta-hero-glass-card' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'card_padding',
			[
				'label'      => esc_html__( 'Marge interne (Padding)', 'tools-adapter' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'default'    => [
					'top'      => 48,
					'right'    => 44,
					'bottom'   => 48,
					'left'     => 44,
					'unit'     => 'px',
					'isLinked' => false,
				],
				'selectors'  => [
					'{{WRAPPER}} .ta-hero-glass-card' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'card_box_shadow',
				'selector' => '{{WRAPPER}} .ta-hero-glass-card',
			]
		);

		$this->end_controls_section();

		// =========================================================================
		// STYLE : TYPOGRAPHIE & COULEURS DU CONTENU
		// =========================================================================
		$this->start_controls_section(
			'section_style_content',
			[
				'label' => esc_html__( 'Typographie & Textes', 'tools-adapter' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		// --- SOUS-TITRE / SUR-TITRE ---
		$this->add_control(
			'heading_subtitle',
			[
				'label'     => esc_html__( 'Sur-titre (Sous-titre)', 'tools-adapter' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'subtitle_color',
			[
				'label'     => esc_html__( 'Couleur du texte', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#334155',
				'selectors' => [
					'{{WRAPPER}} .ta-hero-subtitle' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'subtitle_typography',
				'selector' => '{{WRAPPER}} .ta-hero-subtitle',
			]
		);

		$this->add_responsive_control(
			'subtitle_spacing',
			[
				'label'      => esc_html__( 'Espacement bas', 'tools-adapter' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'range'      => [
					'px' => [ 'min' => 0, 'max' => 50 ],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 12,
				],
				'selectors'  => [
					'{{WRAPPER}} .ta-hero-subtitle' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'subtitle_as_badge',
			[
				'label'        => esc_html__( 'Afficher sous forme de badge capsule', 'tools-adapter' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'no',
				'return_value' => 'yes',
			]
		);

		$this->add_control(
			'badge_bg_color',
			[
				'label'     => esc_html__( 'Couleur de fond du badge', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(255, 255, 255, 0.65)',
				'condition' => [ 'subtitle_as_badge' => 'yes' ],
				'selectors' => [
					'{{WRAPPER}} .ta-hero-subtitle--badge' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'badge_border_color',
			[
				'label'     => esc_html__( 'Bordure du badge', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(255, 255, 255, 0.8)',
				'condition' => [ 'subtitle_as_badge' => 'yes' ],
				'selectors' => [
					'{{WRAPPER}} .ta-hero-subtitle--badge' => 'border-color: {{VALUE}};',
				],
			]
		);

		// --- TITRE PRINCIPAL ---
		$this->add_control(
			'heading_title',
			[
				'label'     => esc_html__( 'Titre Principal', 'tools-adapter' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'title_color',
			[
				'label'     => esc_html__( 'Couleur du titre', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#0f172a',
				'selectors' => [
					'{{WRAPPER}} .ta-hero-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .ta-hero-title',
			]
		);

		$this->add_responsive_control(
			'title_spacing',
			[
				'label'      => esc_html__( 'Espacement bas', 'tools-adapter' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'range'      => [
					'px' => [ 'min' => 0, 'max' => 60 ],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 16,
				],
				'selectors'  => [
					'{{WRAPPER}} .ta-hero-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name'     => 'title_text_shadow',
				'selector' => '{{WRAPPER}} .ta-hero-title',
			]
		);

		// --- DESCRIPTION ---
		$this->add_control(
			'heading_desc',
			[
				'label'     => esc_html__( 'Description', 'tools-adapter' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'desc_color',
			[
				'label'     => esc_html__( 'Couleur de la description', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#475569',
				'selectors' => [
					'{{WRAPPER}} .ta-hero-desc' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'desc_typography',
				'selector' => '{{WRAPPER}} .ta-hero-desc',
			]
		);

		$this->add_responsive_control(
			'desc_spacing',
			[
				'label'      => esc_html__( 'Espacement bas', 'tools-adapter' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'range'      => [
					'px' => [ 'min' => 0, 'max' => 60 ],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 28,
				],
				'selectors'  => [
					'{{WRAPPER}} .ta-hero-desc' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'desc_max_width',
			[
				'label'      => esc_html__( 'Largeur maximale description', 'tools-adapter' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'      => [
					'px' => [ 'min' => 250, 'max' => 800 ],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 540,
				],
				'selectors'  => [
					'{{WRAPPER}} .ta-hero-desc' => 'max-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		// =========================================================================
		// STYLE : BOUTONS D'ACTION (CTA)
		// =========================================================================
		$this->start_controls_section(
			'section_style_buttons',
			[
				'label' => esc_html__( 'Boutons d\'Action (CTA)', 'tools-adapter' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'heading_btn_primary',
			[
				'label' => esc_html__( 'Bouton Principal', 'tools-adapter' ),
				'type'  => Controls_Manager::HEADING,
			]
		);

		$this->start_controls_tabs( 'tabs_btn_primary' );

		// Onglet Normal
		$this->start_controls_tab(
			'tab_btn_normal',
			[
				'label' => esc_html__( 'Normal', 'tools-adapter' ),
			]
		);

		$this->add_control(
			'btn_color',
			[
				'label'     => esc_html__( 'Couleur du texte', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .ta-hero-btn--primary' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'btn_bg_color',
			[
				'label'     => esc_html__( 'Couleur de fond', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#f59e0b',
				'selectors' => [
					'{{WRAPPER}} .ta-hero-btn--primary' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'btn_box_shadow',
				'selector' => '{{WRAPPER}} .ta-hero-btn--primary',
			]
		);

		$this->end_controls_tab();

		// Onglet Hover
		$this->start_controls_tab(
			'tab_btn_hover',
			[
				'label' => esc_html__( 'Survol', 'tools-adapter' ),
			]
		);

		$this->add_control(
			'btn_hover_color',
			[
				'label'     => esc_html__( 'Couleur du texte au survol', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .ta-hero-btn--primary:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'btn_hover_bg_color',
			[
				'label'     => esc_html__( 'Couleur de fond au survol', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#d97706',
				'selectors' => [
					'{{WRAPPER}} .ta-hero-btn--primary:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'btn_hover_box_shadow',
				'selector' => '{{WRAPPER}} .ta-hero-btn--primary:hover',
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'btn_typography',
				'selector'  => '{{WRAPPER}} .ta-hero-btn',
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'btn_border_radius',
			[
				'label'      => esc_html__( 'Arrondi des boutons', 'tools-adapter' ),
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
					'{{WRAPPER}} .ta-hero-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'btn_padding',
			[
				'label'      => esc_html__( 'Marge interne du bouton (Padding)', 'tools-adapter' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'default'    => [
					'top'      => 14,
					'right'    => 34,
					'bottom'   => 14,
					'left'     => 34,
					'unit'     => 'px',
					'isLinked' => false,
				],
				'selectors'  => [
					'{{WRAPPER}} .ta-hero-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		// Bouton Secondaire
		$this->add_control(
			'heading_btn_secondary',
			[
				'label'     => esc_html__( 'Bouton Secondaire', 'tools-adapter' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'sec_btn_color',
			[
				'label'     => esc_html__( 'Couleur texte secondaire', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#1e293b',
				'selectors' => [
					'{{WRAPPER}} .ta-hero-btn--secondary' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'sec_btn_bg_color',
			[
				'label'     => esc_html__( 'Couleur fond secondaire', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(255, 255, 255, 0.75)',
				'selectors' => [
					'{{WRAPPER}} .ta-hero-btn--secondary' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'sec_btn_border_color',
			[
				'label'     => esc_html__( 'Bordure bouton secondaire', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(0, 0, 0, 0.1)',
				'selectors' => [
					'{{WRAPPER}} .ta-hero-btn--secondary' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		// =========================================================================
		// STYLE : NAVIGATION (FLÈCHES ET PUCES)
		// =========================================================================
		$this->start_controls_section(
			'section_style_navigation',
			[
				'label' => esc_html__( 'Navigation (Flèches & Puces)', 'tools-adapter' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'heading_nav_arrows',
			[
				'label' => esc_html__( 'Flèches de navigation', 'tools-adapter' ),
				'type'  => Controls_Manager::HEADING,
			]
		);

		$this->add_responsive_control(
			'arrow_size',
			[
				'label'      => esc_html__( 'Diamètre du cercle (px)', 'tools-adapter' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => [
					'px' => [ 'min' => 32, 'max' => 80 ],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 50,
				],
				'selectors'  => [
					'{{WRAPPER}} .ta-hero-carousel .ta-carousel__arrow' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'arrow_offset',
			[
				'label'      => esc_html__( 'Distance du bord latéral', 'tools-adapter' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => [
					'px' => [ 'min' => 5, 'max' => 80 ],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 28,
				],
				'selectors'  => [
					'{{WRAPPER}} .ta-hero-carousel .ta-carousel__arrow--prev' => 'left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ta-hero-carousel .ta-carousel__arrow--next' => 'right: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'arrow_bg_color',
			[
				'label'     => esc_html__( 'Couleur de fond de la flèche', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .ta-hero-carousel .ta-carousel__arrow' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'arrow_icon_color',
			[
				'label'     => esc_html__( 'Couleur du symbole flèche', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#0f172a',
				'selectors' => [
					'{{WRAPPER}} .ta-hero-carousel .ta-carousel__arrow' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'arrow_hover_bg_color',
			[
				'label'     => esc_html__( 'Fond flèche au survol', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .ta-hero-carousel .ta-carousel__arrow:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'arrow_hover_icon_color',
			[
				'label'     => esc_html__( 'Symbole flèche au survol', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#f59e0b',
				'selectors' => [
					'{{WRAPPER}} .ta-hero-carousel .ta-carousel__arrow:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'arrow_box_shadow',
				'selector' => '{{WRAPPER}} .ta-hero-carousel .ta-carousel__arrow',
			]
		);

		// Puces
		$this->add_control(
			'heading_nav_dots',
			[
				'label'     => esc_html__( 'Puces de pagination', 'tools-adapter' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'dots_bottom_offset',
			[
				'label'      => esc_html__( 'Distance du bas', 'tools-adapter' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => [
					'px' => [ 'min' => 8, 'max' => 60 ],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 22,
				],
				'selectors'  => [
					'{{WRAPPER}} .ta-hero-carousel .ta-carousel__dots' => 'bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'dot_color',
			[
				'label'     => esc_html__( 'Couleur des puces inactives', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(255, 255, 255, 0.5)',
				'selectors' => [
					'{{WRAPPER}} .ta-hero-carousel .ta-carousel__dot' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'dot_active_color',
			[
				'label'     => esc_html__( 'Couleur de la puce active', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .ta-hero-carousel .ta-carousel__dot.is-active' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'dot_size',
			[
				'label'      => esc_html__( 'Taille des puces', 'tools-adapter' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => [
					'px' => [ 'min' => 6, 'max' => 20 ],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 10,
				],
				'selectors'  => [
					'{{WRAPPER}} .ta-hero-carousel .ta-carousel__dot' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'dot_active_width',
			[
				'label'      => esc_html__( 'Largeur puce active (effet pilule)', 'tools-adapter' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => [
					'px' => [ 'min' => 10, 'max' => 50 ],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 28,
				],
				'selectors'  => [
					'{{WRAPPER}} .ta-hero-carousel .ta-carousel__dot.is-active' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$slides   = $settings['slides'] ?? [];

		if ( empty( $slides ) ) {
			return;
		}

		$is_autoplay       = 'yes' === ( $settings['autoplay'] ?? '' );
		$autoplay_speed    = ! empty( $settings['autoplay_speed'] ) ? absint( $settings['autoplay_speed'] ) : 5000;
		$transition_speed  = ! empty( $settings['transition_speed'] ) ? absint( $settings['transition_speed'] ) : 500;
		$show_arrows       = 'yes' === ( $settings['show_arrows'] ?? '' );
		$show_dots         = 'yes' === ( $settings['show_dots'] ?? '' );
		$subtitle_as_badge = 'yes' === ( $settings['subtitle_as_badge'] ?? '' );
		$animation_style   = $settings['animation_style'] ?? 'fade_up';

		$wrapper_classes = [
			'ta-hero-carousel',
			'ta-hero-carousel--anim-' . esc_attr( $animation_style ),
		];
		?>
		<div
			class="<?php echo esc_attr( implode( ' ', $wrapper_classes ) ); ?>"
			data-ta-carousel
			data-slides-show="1"
			data-slides-show-tablet="1"
			data-slides-show-mobile="1"
			data-gap="0"
			data-autoplay="<?php echo $is_autoplay ? '1' : '0'; ?>"
			data-autoplay-speed="<?php echo esc_attr( (string) $autoplay_speed ); ?>"
			data-transition-speed="<?php echo esc_attr( (string) $transition_speed ); ?>"
		>
			<div class="ta-hero-carousel__track" data-carousel-track>
				<?php foreach ( $slides as $index => $item ) : ?>
					<?php
					$bg_url = ! empty( $item['background_image']['url'] ) ? $item['background_image']['url'] : '';
					$subtitle = ! empty( $item['subtitle'] ) ? \tools_adapter_translate( $item['subtitle'] ) : '';
					$title = ! empty( $item['title'] ) ? \tools_adapter_translate( $item['title'] ) : '';
					$title_tag = ! empty( $item['title_tag'] ) ? esc_attr( $item['title_tag'] ) : 'h1';
					$description = ! empty( $item['description'] ) ? \tools_adapter_translate( $item['description'] ) : '';
					$show_btn = 'yes' === ( $item['show_button'] ?? 'yes' ) && ! empty( $item['button_text'] );
					$show_sec_btn = 'yes' === ( $item['show_secondary_button'] ?? 'no' ) && ! empty( $item['sec_button_text'] );

					$slide_classes = [ 'ta-hero-slide' ];
					if ( 0 === $index ) {
						$slide_classes[] = 'is-active';
					}

					$slide_style = '';
					if ( $bg_url ) {
						$slide_style .= 'background-image: url(' . esc_url( $bg_url ) . ');';
					}

					$custom_align = $item['card_align_override'] ?? '';
					if ( 'left' === $custom_align ) {
						$slide_style .= ' --ta-hero-card-h-align: flex-start;';
					} elseif ( 'center' === $custom_align ) {
						$slide_style .= ' --ta-hero-card-h-align: center;';
					} elseif ( 'right' === $custom_align ) {
						$slide_style .= ' --ta-hero-card-h-align: flex-end;';
					}

					$overlay_style = '';
					if ( ! empty( $item['slide_overlay_color'] ) ) {
						$overlay_style = 'background-color: ' . esc_attr( $item['slide_overlay_color'] ) . ';';
					}
					?>
					<div
						class="<?php echo esc_attr( implode( ' ', $slide_classes ) ); ?>"
						style="<?php echo esc_attr( $slide_style ); ?>"
						data-carousel-slide
					>
						<div class="ta-hero-slide__overlay" style="<?php echo esc_attr( $overlay_style ); ?>"></div>

						<div class="ta-hero-slide__container">
							<div class="ta-hero-glass-card">
								<?php if ( $subtitle ) : ?>
									<span class="ta-hero-subtitle<?php echo $subtitle_as_badge ? ' ta-hero-subtitle--badge' : ''; ?>">
										<?php echo esc_html( $subtitle ); ?>
									</span>
								<?php endif; ?>

								<?php if ( $title ) : ?>
									<<?php echo esc_attr( $title_tag ); ?> class="ta-hero-title">
										<?php echo esc_html( $title ); ?>
									</<?php echo esc_attr( $title_tag ); ?>>
								<?php endif; ?>

								<?php if ( $description ) : ?>
									<p class="ta-hero-desc">
										<?php echo nl2br( esc_html( $description ) ); ?>
									</p>
								<?php endif; ?>

								<?php if ( $show_btn || $show_sec_btn ) : ?>
									<div class="ta-hero-actions">
										<?php if ( $show_btn ) : ?>
											<?php
											$btn_url = ! empty( $item['button_link']['url'] ) ? $item['button_link']['url'] : '#';
											$btn_is_external = ! empty( $item['button_link']['is_external'] );
											$btn_nofollow = ! empty( $item['button_link']['nofollow'] );
											?>
											<a
												href="<?php echo esc_url( $btn_url ); ?>"
												class="ta-hero-btn ta-hero-btn--primary"
												<?php echo $btn_is_external ? 'target="_blank"' : ''; ?>
												<?php echo $btn_nofollow ? 'rel="nofollow noopener"' : ''; ?>
											>
												<span><?php echo esc_html( \tools_adapter_translate( $item['button_text'] ) ); ?></span>
											</a>
										<?php endif; ?>

										<?php if ( $show_sec_btn ) : ?>
											<?php
											$sec_btn_url = ! empty( $item['sec_button_link']['url'] ) ? $item['sec_button_link']['url'] : '#';
											$sec_btn_is_external = ! empty( $item['sec_button_link']['is_external'] );
											$sec_btn_nofollow = ! empty( $item['sec_button_link']['nofollow'] );
											?>
											<a
												href="<?php echo esc_url( $sec_btn_url ); ?>"
												class="ta-hero-btn ta-hero-btn--secondary"
												<?php echo $sec_btn_is_external ? 'target="_blank"' : ''; ?>
												<?php echo $sec_btn_nofollow ? 'rel="nofollow noopener"' : ''; ?>
											>
												<span><?php echo esc_html( \tools_adapter_translate( $item['sec_button_text'] ) ); ?></span>
											</a>
										<?php endif; ?>
									</div>
								<?php endif; ?>
							</div>
						</div>
					</div>
				<?php endforeach; ?>
			</div>

			<?php if ( $show_arrows && count( $slides ) > 1 ) : ?>
				<button type="button" class="ta-carousel__arrow ta-carousel__arrow--prev" data-carousel-prev aria-label="<?php echo esc_attr__( 'Précédent', 'tools-adapter' ); ?>">&#8249;</button>
				<button type="button" class="ta-carousel__arrow ta-carousel__arrow--next" data-carousel-next aria-label="<?php echo esc_attr__( 'Suivant', 'tools-adapter' ); ?>">&#8250;</button>
			<?php endif; ?>

			<?php if ( $show_dots && count( $slides ) > 1 ) : ?>
				<div class="ta-carousel__dots" data-carousel-dots></div>
			<?php endif; ?>
		</div>
		<?php
	}
}
