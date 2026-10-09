<?php
namespace ToolsAdapter\Widgets;

use Elementor\Controls_Manager;
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
 * Widget: Hero / Bannière — section d'accroche pleine largeur.
 */
class Hero_Banner extends Base_Widget {

	public function get_name() {
		return 'tools-adapter-hero-banner';
	}

	public function get_title() {
		return esc_html__( 'Hero / Bannière', 'tools-adapter' );
	}

	public function get_icon() {
		return 'eicon-slider-push';
	}

	public function get_categories() {
		return [ 'tools-adapter' ];
	}

	public function get_keywords() {
		return [ 'hero', 'banner', 'bannière', 'header', 'landing', 'accroche' ];
	}

	public function get_style_depends() {
		return [ 'tools-adapter-hero-banner' ];
	}

	protected function register_controls() {
		$this->start_controls_section( 'section_content', [ 'label' => esc_html__( 'Contenu', 'tools-adapter' ) ] );

		$this->add_control(
			'subtitle',
			[
				'label'   => esc_html__( 'Sur-titre', 'tools-adapter' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '',
			]
		);

		$this->add_control(
			'title',
			[
				'label'   => esc_html__( 'Titre', 'tools-adapter' ),
				'type'    => Controls_Manager::TEXTAREA,
				'rows'    => 3,
				'default' => esc_html__( 'Un titre percutant pour votre page', 'tools-adapter' ),
			]
		);

		$this->add_control(
			'title_tag',
			[
				'label'   => esc_html__( 'Balise titre', 'tools-adapter' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'h1',
				'options' => [
					'h1'  => 'H1',
					'h2'  => 'H2',
					'h3'  => 'H3',
					'div' => 'div',
				],
			]
		);

		$this->add_control(
			'description',
			[
				'label'   => esc_html__( 'Description', 'tools-adapter' ),
				'type'    => Controls_Manager::TEXTAREA,
				'rows'    => 4,
				'default' => esc_html__( 'Décrivez ici votre offre ou votre message principal en une ou deux phrases.', 'tools-adapter' ),
			]
		);

		$this->add_control(
			'content_align',
			[
				'label'        => esc_html__( 'Alignement du contenu', 'tools-adapter' ),
				'type'         => Controls_Manager::CHOOSE,
				'options'      => [
					'left'   => [ 'title' => esc_html__( 'Gauche', 'tools-adapter' ), 'icon' => 'eicon-text-align-left' ],
					'center' => [ 'title' => esc_html__( 'Centre', 'tools-adapter' ), 'icon' => 'eicon-text-align-center' ],
					'right'  => [ 'title' => esc_html__( 'Droite', 'tools-adapter' ), 'icon' => 'eicon-text-align-right' ],
				],
				'default'      => 'center',
				'prefix_class' => 'ta-hero-align-',
			]
		);

		$this->add_responsive_control(
			'min_height',
			[
				'label'      => esc_html__( 'Hauteur minimale', 'tools-adapter' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'vh' ],
				'range'      => [
					'px' => [ 'min' => 200, 'max' => 1000 ],
					'vh' => [ 'min' => 20, 'max' => 100 ],
				],
				'default'    => [ 'size' => 480, 'unit' => 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .ta-hero' => 'min-height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'content_max_width',
			[
				'label'      => esc_html__( 'Largeur max. du contenu', 'tools-adapter' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'      => [
					'px' => [ 'min' => 200, 'max' => 1400 ],
					'%'  => [ 'min' => 10, 'max' => 100 ],
				],
				'default'    => [ 'size' => 720, 'unit' => 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .ta-hero__content' => 'max-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		// Buttons.
		$this->start_controls_section( 'section_buttons', [ 'label' => esc_html__( 'Boutons', 'tools-adapter' ) ] );

		$this->add_control(
			'show_primary_btn',
			[
				'label'        => esc_html__( 'Bouton principal', 'tools-adapter' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'primary_btn_text',
			[
				'label'     => esc_html__( 'Texte', 'tools-adapter' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'Découvrir', 'tools-adapter' ),
				'condition' => [ 'show_primary_btn' => 'yes' ],
			]
		);

		$this->add_control(
			'primary_btn_link',
			[
				'label'       => esc_html__( 'Lien', 'tools-adapter' ),
				'type'        => Controls_Manager::URL,
				'default'     => [ 'url' => '#' ],
				'condition'   => [ 'show_primary_btn' => 'yes' ],
			]
		);

		$this->add_control(
			'show_secondary_btn',
			[
				'label'        => esc_html__( 'Bouton secondaire', 'tools-adapter' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
				'separator'    => 'before',
			]
		);

		$this->add_control(
			'secondary_btn_text',
			[
				'label'     => esc_html__( 'Texte', 'tools-adapter' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'En savoir plus', 'tools-adapter' ),
				'condition' => [ 'show_secondary_btn' => 'yes' ],
			]
		);

		$this->add_control(
			'secondary_btn_link',
			[
				'label'     => esc_html__( 'Lien', 'tools-adapter' ),
				'type'      => Controls_Manager::URL,
				'default'   => [ 'url' => '#' ],
				'condition' => [ 'show_secondary_btn' => 'yes' ],
			]
		);

		$this->end_controls_section();

		// Badges de réassurance inline
		$this->start_controls_section( 'section_badges', [ 'label' => esc_html__( 'Badges de réassurance (Sous-titre)', 'tools-adapter' ) ] );

		$this->add_control(
			'show_badges',
			[
				'label'        => esc_html__( 'Afficher les badges', 'tools-adapter' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
			]
		);

		$repeater_badges = new Repeater();
		$repeater_badges->add_control( 'icon', [ 'label' => esc_html__( 'Icône', 'tools-adapter' ), 'type' => Controls_Manager::ICONS ] );
		$repeater_badges->add_control( 'text', [ 'label' => esc_html__( 'Texte du badge', 'tools-adapter' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'Garantie / Engagement', 'tools-adapter' ) ] );

		$this->add_control(
			'badges',
			[
				'label'       => esc_html__( 'Liste des badges', 'tools-adapter' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater_badges->get_controls(),
				'default'     => [
					[ 'text' => esc_html__( 'Intervention d\'urgence 6j/7', 'tools-adapter' ) ],
					[ 'text' => esc_html__( 'Agrément ANC Préfecture', 'tools-adapter' ) ],
					[ 'text' => esc_html__( 'Entreprise familiale depuis 1999', 'tools-adapter' ) ],
				],
				'title_field' => '{{{ text }}}',
				'condition'   => [ 'show_badges' => 'yes' ],
			]
		);

		$this->end_controls_section();

		// Carte vitrée d'accès rapide (Split Layout)
		$this->start_controls_section( 'section_split_card', [ 'label' => esc_html__( 'Carte vitrée d\'accès rapide (Split Hero)', 'tools-adapter' ) ] );

		$this->add_control(
			'enable_split_card',
			[
				'label'        => esc_html__( 'Activer la carte d\'accès rapide', 'tools-adapter' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
				'description'  => esc_html__( 'Crée une disposition 2 colonnes avec une carte interactive à droite.', 'tools-adapter' ),
			]
		);

		$this->add_control(
			'card_title',
			[
				'label'     => esc_html__( 'Titre de la carte', 'tools-adapter' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'Besoin d\'une intervention rapide ?', 'tools-adapter' ),
				'condition' => [ 'enable_split_card' => 'yes' ],
			]
		);

		$this->add_control(
			'card_desc',
			[
				'label'     => esc_html__( 'Sous-texte de la carte', 'tools-adapter' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'Sélectionnez votre besoin pour un traitement prioritaire :', 'tools-adapter' ),
				'condition' => [ 'enable_split_card' => 'yes' ],
			]
		);

		$repeater_card_actions = new Repeater();
		$repeater_card_actions->add_control( 'icon', [ 'label' => esc_html__( 'Icône (optionnelle)', 'tools-adapter' ), 'type' => Controls_Manager::ICONS ] );
		$repeater_card_actions->add_control( 'title', [ 'label' => esc_html__( 'Titre de l\'action', 'tools-adapter' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'Vidange / Débouchage', 'tools-adapter' ) ] );
		$repeater_card_actions->add_control( 'subtitle', [ 'label' => esc_html__( 'Sous-titre (optionnel)', 'tools-adapter' ), 'type' => Controls_Manager::TEXT, 'placeholder' => esc_html__( 'Fosses, bacs à graisse, hydrocurage', 'tools-adapter' ) ] );
		$repeater_card_actions->add_control( 'badge', [ 'label' => esc_html__( 'Badge (ex: Urgence)', 'tools-adapter' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'Urgence 24-48h', 'tools-adapter' ) ] );
		$repeater_card_actions->add_control( 'link', [ 'label' => esc_html__( 'Lien (ancre ou URL)', 'tools-adapter' ), 'type' => Controls_Manager::URL, 'default' => [ 'url' => '#devis' ] ] );

		$this->add_control(
			'card_actions',
			[
				'label'       => esc_html__( 'Boutons de choix rapide', 'tools-adapter' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater_card_actions->get_controls(),
				'default'     => [
					[ 'title' => esc_html__( 'Vidange / Débouchage', 'tools-adapter' ), 'badge' => esc_html__( 'Urgence 24-48h', 'tools-adapter' ), 'link' => [ 'url' => '#devis' ] ],
					[ 'title' => esc_html__( 'Terrassement / Travaux', 'tools-adapter' ), 'badge' => esc_html__( 'Visite sur site & devis', 'tools-adapter' ), 'link' => [ 'url' => '#devis' ] ],
					[ 'title' => esc_html__( 'Bois de chauffage', 'tools-adapter' ), 'badge' => esc_html__( 'Tarifs direct exploitant', 'tools-adapter' ), 'link' => [ 'url' => '#tarifs-bois' ] ],
				],
				'title_field' => '{{{ title }}} — {{{ badge }}}',
				'condition'   => [ 'enable_split_card' => 'yes' ],
			]
		);

		$this->add_control(
			'card_show_arrow',
			[
				'label'        => esc_html__( 'Flèche à droite des choix', 'tools-adapter' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
				'condition'    => [ 'enable_split_card' => 'yes' ],
			]
		);

		$this->add_control(
			'card_phone_icon',
			[
				'label'       => esc_html__( 'Icône téléphone footer', 'tools-adapter' ),
				'type'        => Controls_Manager::ICONS,
				'description' => esc_html__( 'Avec une icône, le libellé passe au-dessus du numéro.', 'tools-adapter' ),
				'separator'   => 'before',
				'condition'   => [ 'enable_split_card' => 'yes' ],
			]
		);

		$this->add_control(
			'card_phone_label',
			[
				'label'     => esc_html__( 'Label téléphone footer', 'tools-adapter' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'Ligne directe atelier :', 'tools-adapter' ),
				'condition' => [ 'enable_split_card' => 'yes' ],
			]
		);

		$this->add_control(
			'card_phone_number',
			[
				'label'     => esc_html__( 'Numéro de téléphone', 'tools-adapter' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => '03 88 63 70 71',
				'condition' => [ 'enable_split_card' => 'yes' ],
			]
		);

		$this->add_control(
			'card_phone_url',
			[
				'label'     => esc_html__( 'Lien téléphone (tel:...)', 'tools-adapter' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => 'tel:0388637071',
				'condition' => [ 'enable_split_card' => 'yes' ],
			]
		);

		$this->end_controls_section();

		// Background.
		$this->start_controls_section( 'section_background', [ 'label' => esc_html__( 'Fond', 'tools-adapter' ) ] );

		$this->add_control(
			'kenburns_effect',
			[
				'label'        => esc_html__( 'Effet de zoom continu (Ken Burns)', 'tools-adapter' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
				'prefix_class' => 'ta-hero-effect--',
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'hero_bg',
				'types'    => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .ta-hero, {{WRAPPER}} .ta-hero__bg-layer',
				'fields_options' => [
					'background' => [ 'default' => 'classic' ],
					'color'      => [ 'default' => '#1c1c1c' ],
				],
			]
		);

		$this->add_control(
			'overlay_color',
			[
				'label'      => esc_html__( 'Couleur de superposition', 'tools-adapter' ),
				'type'       => Controls_Manager::COLOR,
				'default'    => 'rgba(0,0,0,0.35)',
				'selectors'  => [
					'{{WRAPPER}} .ta-hero__overlay' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		// Style: text.
		$this->start_controls_section(
			'section_style_text',
			[ 'label' => esc_html__( 'Texte', 'tools-adapter' ), 'tab' => Controls_Manager::TAB_STYLE ]
		);

		$this->add_control(
			'subtitle_color',
			[
				'label'     => esc_html__( 'Couleur sur-titre', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#C9A84C',
				'selectors' => [ '{{WRAPPER}} .ta-hero__subtitle' => 'color: {{VALUE}};' ],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[ 'name' => 'subtitle_typography', 'selector' => '{{WRAPPER}} .ta-hero__subtitle' ]
		);

		$this->add_control(
			'title_color',
			[
				'label'     => esc_html__( 'Couleur titre', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'separator' => 'before',
				'selectors' => [ '{{WRAPPER}} .ta-hero__title' => 'color: {{VALUE}};' ],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[ 'name' => 'title_typography', 'selector' => '{{WRAPPER}} .ta-hero__title' ]
		);

		$this->add_control(
			'description_color',
			[
				'label'     => esc_html__( 'Couleur description', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#eeeeee',
				'separator' => 'before',
				'selectors' => [ '{{WRAPPER}} .ta-hero__description' => 'color: {{VALUE}};' ],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[ 'name' => 'description_typography', 'selector' => '{{WRAPPER}} .ta-hero__description' ]
		);

		$this->end_controls_section();

		// Style: buttons.
		$this->start_controls_section(
			'section_style_buttons',
			[ 'label' => esc_html__( 'Boutons', 'tools-adapter' ), 'tab' => Controls_Manager::TAB_STYLE ]
		);

		$this->add_control( 'heading_primary_btn', [ 'label' => esc_html__( 'Bouton principal', 'tools-adapter' ), 'type' => Controls_Manager::HEADING ] );
		$this->add_control( 'primary_btn_color', [ 'label' => esc_html__( 'Texte', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#1c1c1c', 'selectors' => [ '{{WRAPPER}} .ta-hero__btn--primary' => 'color: {{VALUE}};' ] ] );
		$this->add_control( 'primary_btn_bg', [ 'label' => esc_html__( 'Fond', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#C9A84C', 'selectors' => [ '{{WRAPPER}} .ta-hero__btn--primary' => 'background-color: {{VALUE}}; border-color: {{VALUE}};' ] ] );
		$this->add_control( 'primary_btn_color_hover', [ 'label' => esc_html__( 'Texte (survol)', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .ta-hero__btn--primary:hover' => 'color: {{VALUE}};' ] ] );
		$this->add_control( 'primary_btn_bg_hover', [ 'label' => esc_html__( 'Fond (survol)', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .ta-hero__btn--primary:hover' => 'background-color: {{VALUE}}; border-color: {{VALUE}};' ] ] );

		$this->add_control( 'heading_secondary_btn', [ 'label' => esc_html__( 'Bouton secondaire', 'tools-adapter' ), 'type' => Controls_Manager::HEADING, 'separator' => 'before' ] );
		$this->add_control( 'secondary_btn_color', [ 'label' => esc_html__( 'Texte', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#ffffff', 'selectors' => [ '{{WRAPPER}} .ta-hero__btn--secondary' => 'color: {{VALUE}};' ] ] );
		$this->add_control( 'secondary_btn_border', [ 'label' => esc_html__( 'Bordure', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#ffffff', 'selectors' => [ '{{WRAPPER}} .ta-hero__btn--secondary' => 'border-color: {{VALUE}};' ] ] );
		$this->add_control( 'secondary_btn_bg_hover', [ 'label' => esc_html__( 'Fond (survol)', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#ffffff', 'selectors' => [ '{{WRAPPER}} .ta-hero__btn--secondary:hover' => 'background-color: {{VALUE}};' ] ] );
		$this->add_control( 'secondary_btn_color_hover', [ 'label' => esc_html__( 'Texte (survol)', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#1c1c1c', 'selectors' => [ '{{WRAPPER}} .ta-hero__btn--secondary:hover' => 'color: {{VALUE}};' ] ] );

		$this->add_responsive_control(
			'btn_padding',
			[
				'label'      => esc_html__( 'Espacement interne', 'tools-adapter' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'default'    => [ 'top' => '14', 'right' => '32', 'bottom' => '14', 'left' => '32', 'unit' => 'px' ],
				'separator'  => 'before',
				'selectors'  => [ '{{WRAPPER}} .ta-hero__btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
			]
		);

		$this->add_control(
			'btn_radius',
			[
				'label'      => esc_html__( 'Arrondi', 'tools-adapter' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'      => [ 'px' => [ 'min' => 0, 'max' => 60 ] ],
				'default'    => [ 'size' => 6, 'unit' => 'px' ],
				'selectors'  => [ '{{WRAPPER}} .ta-hero__btn' => 'border-radius: {{SIZE}}{{UNIT}};' ],
			]
		);

		$this->end_controls_section();

		// Style: box.
		$this->start_controls_section(
			'section_style_box',
			[ 'label' => esc_html__( 'Boîte', 'tools-adapter' ), 'tab' => Controls_Manager::TAB_STYLE ]
		);

		$this->add_responsive_control(
			'hero_padding',
			[
				'label'      => esc_html__( 'Espacement interne', 'tools-adapter' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'default'    => [ 'top' => '80', 'right' => '40', 'bottom' => '80', 'left' => '40', 'unit' => 'px' ],
				'selectors'  => [ '{{WRAPPER}} .ta-hero' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[ 'name' => 'hero_border', 'selector' => '{{WRAPPER}} .ta-hero' ]
		);

		$this->add_control(
			'hero_radius',
			[
				'label'      => esc_html__( 'Arrondi', 'tools-adapter' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 'px' => [ 'min' => 0, 'max' => 80 ] ],
				'selectors'  => [ '{{WRAPPER}} .ta-hero' => 'border-radius: {{SIZE}}{{UNIT}}; overflow: hidden;' ],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[ 'name' => 'hero_shadow', 'selector' => '{{WRAPPER}} .ta-hero' ]
		);

		$this->end_controls_section();

		// Style: Badges de réassurance inline
		$this->start_controls_section(
			'section_style_badges',
			[
				'label'     => esc_html__( 'Badges de réassurance', 'tools-adapter' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [ 'show_badges' => 'yes' ],
			]
		);

		$this->add_group_control( Group_Control_Typography::get_type(), [ 'name' => 'badge_typography', 'selector' => '{{WRAPPER}} .ta-hero__badge-item' ] );
		$this->add_control( 'badge_text_color', [ 'label' => esc_html__( 'Couleur du texte', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#f1f5f9', 'selectors' => [ '{{WRAPPER}} .ta-hero__badge-item' => 'color: {{VALUE}};' ] ] );
		$this->add_control( 'badge_icon_color', [ 'label' => esc_html__( 'Couleur de l\'icône', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#DDA853', 'selectors' => [ '{{WRAPPER}} .ta-hero__badge-icon' => 'color: {{VALUE}};' ] ] );
		$this->add_control( 'badge_bg', [ 'label' => esc_html__( 'Fond', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => 'rgba(255, 255, 255, 0.08)', 'selectors' => [ '{{WRAPPER}} .ta-hero__badge-item' => 'background-color: {{VALUE}};' ] ] );
		$this->add_control( 'badge_border', [ 'label' => esc_html__( 'Bordure', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => 'rgba(255, 255, 255, 0.14)', 'selectors' => [ '{{WRAPPER}} .ta-hero__badge-item' => 'border-color: {{VALUE}};' ] ] );
		$this->add_responsive_control( 'badge_padding', [ 'label' => esc_html__( 'Padding', 'tools-adapter' ), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => [ 'px' ], 'default' => [ 'top' => '6', 'right' => '14', 'bottom' => '6', 'left' => '14', 'unit' => 'px' ], 'selectors' => [ '{{WRAPPER}} .ta-hero__badge-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ] ] );
		$this->add_responsive_control( 'badge_radius', [ 'label' => esc_html__( 'Arrondi', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'range' => [ 'px' => [ 'min' => 0, 'max' => 50 ] ], 'default' => [ 'size' => 99, 'unit' => 'px' ], 'selectors' => [ '{{WRAPPER}} .ta-hero__badge-item' => 'border-radius: {{SIZE}}{{UNIT}};' ] ] );

		$this->end_controls_section();

		// Style: Carte vitrée d'accès rapide (Split Hero)
		$this->start_controls_section(
			'section_style_split_card',
			[
				'label'     => esc_html__( 'Carte vitrée d\'accès rapide', 'tools-adapter' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [ 'enable_split_card' => 'yes' ],
			]
		);

		$this->add_control( 'card_box_bg', [ 'label' => esc_html__( 'Fond de la carte', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => 'rgba(11, 25, 44, 0.75)', 'selectors' => [ '{{WRAPPER}} .ta-hero__card' => 'background-color: {{VALUE}};' ] ] );
		$this->add_group_control( Group_Control_Border::get_type(), [ 'name' => 'card_box_border', 'selector' => '{{WRAPPER}} .ta-hero__card' ] );
		$this->add_responsive_control( 'card_box_radius', [ 'label' => esc_html__( 'Arrondi (px)', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'range' => [ 'px' => [ 'min' => 0, 'max' => 40 ] ], 'default' => [ 'size' => 16, 'unit' => 'px' ], 'selectors' => [ '{{WRAPPER}} .ta-hero__card' => 'border-radius: {{SIZE}}{{UNIT}};' ] ] );
		$this->add_responsive_control( 'card_box_padding', [ 'label' => esc_html__( 'Padding', 'tools-adapter' ), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => [ 'px' ], 'default' => [ 'top' => '30', 'right' => '30', 'bottom' => '30', 'left' => '30', 'unit' => 'px' ], 'selectors' => [ '{{WRAPPER}} .ta-hero__card' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ] ] );
		$this->add_group_control( Group_Control_Box_Shadow::get_type(), [ 'name' => 'card_box_shadow', 'selector' => '{{WRAPPER}} .ta-hero__card' ] );

		$this->add_control( 'heading_card_texts', [ 'label' => esc_html__( 'Titres de la carte', 'tools-adapter' ), 'type' => Controls_Manager::HEADING, 'separator' => 'before' ] );
		$this->add_control( 'card_title_color', [ 'label' => esc_html__( 'Couleur titre', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#ffffff', 'selectors' => [ '{{WRAPPER}} .ta-hero__card-title' => 'color: {{VALUE}};' ] ] );
		$this->add_group_control( Group_Control_Typography::get_type(), [ 'name' => 'card_title_typography', 'selector' => '{{WRAPPER}} .ta-hero__card-title' ] );
		$this->add_control( 'card_desc_color', [ 'label' => esc_html__( 'Couleur description', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#cbd5e1', 'selectors' => [ '{{WRAPPER}} .ta-hero__card-desc' => 'color: {{VALUE}};' ] ] );
		$this->add_group_control( Group_Control_Typography::get_type(), [ 'name' => 'card_desc_typography', 'selector' => '{{WRAPPER}} .ta-hero__card-desc' ] );

		$this->add_control( 'heading_card_btn_style', [ 'label' => esc_html__( 'Boutons de choix rapide', 'tools-adapter' ), 'type' => Controls_Manager::HEADING, 'separator' => 'before' ] );
		$this->add_group_control( Group_Control_Typography::get_type(), [ 'name' => 'card_btn_typography', 'selector' => '{{WRAPPER}} .ta-hero__card-btn' ] );
		$this->add_control( 'card_btn_bg', [ 'label' => esc_html__( 'Fond inactif', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => 'rgba(255, 255, 255, 0.06)', 'selectors' => [ '{{WRAPPER}} .ta-hero__card-btn' => 'background-color: {{VALUE}};' ] ] );
		$this->add_control( 'card_btn_color', [ 'label' => esc_html__( 'Texte inactif', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#f8fafc', 'selectors' => [ '{{WRAPPER}} .ta-hero__card-btn' => 'color: {{VALUE}};' ] ] );
		$this->add_control( 'card_btn_border', [ 'label' => esc_html__( 'Bordure inactive', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => 'rgba(255, 255, 255, 0.12)', 'selectors' => [ '{{WRAPPER}} .ta-hero__card-btn' => 'border-color: {{VALUE}};' ] ] );

		$this->add_control( 'card_btn_bg_hover', [ 'label' => esc_html__( 'Fond (survol)', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => 'rgba(30, 62, 98, 0.7)', 'selectors' => [ '{{WRAPPER}} .ta-hero__card-btn:hover' => 'background-color: {{VALUE}};' ] ] );
		$this->add_control( 'card_btn_color_hover', [ 'label' => esc_html__( 'Texte (survol)', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#ffffff', 'selectors' => [ '{{WRAPPER}} .ta-hero__card-btn:hover' => 'color: {{VALUE}};' ] ] );
		$this->add_control( 'card_btn_border_hover', [ 'label' => esc_html__( 'Bordure (survol)', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#DDA853', 'selectors' => [ '{{WRAPPER}} .ta-hero__card-btn:hover' => 'border-color: {{VALUE}};' ] ] );

		$this->add_control( 'heading_card_icon_style', [ 'label' => esc_html__( 'Icônes, sous-titres & flèches des choix', 'tools-adapter' ), 'type' => Controls_Manager::HEADING, 'separator' => 'before' ] );
		$this->add_group_control( Group_Control_Background::get_type(), [ 'name' => 'card_btn_icon_bg', 'types' => [ 'classic', 'gradient' ], 'exclude' => [ 'image' ], 'selector' => '{{WRAPPER}} .ta-hero__card-btn-icon' ] );
		$this->add_control( 'card_btn_icon_color', [ 'label' => esc_html__( 'Couleur de l\'icône', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .ta-hero__card-btn-icon' => 'color: {{VALUE}};', '{{WRAPPER}} .ta-hero__card-btn-icon svg' => 'fill: {{VALUE}};' ] ] );
		$this->add_control( 'card_btn_icon_size', [ 'label' => esc_html__( 'Taille de la pastille', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'range' => [ 'px' => [ 'min' => 28, 'max' => 80 ] ], 'selectors' => [ '{{WRAPPER}} .ta-hero__card-btn-icon' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};' ] ] );
		$this->add_control( 'card_btn_icon_font', [ 'label' => esc_html__( 'Taille de l\'icône', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'range' => [ 'px' => [ 'min' => 10, 'max' => 40 ] ], 'selectors' => [ '{{WRAPPER}} .ta-hero__card-btn-icon' => 'font-size: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .ta-hero__card-btn-icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};' ] ] );
		$this->add_control( 'card_btn_icon_radius', [ 'label' => esc_html__( 'Arrondi de la pastille', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'size_units' => [ 'px', '%' ], 'range' => [ 'px' => [ 'min' => 0, 'max' => 40 ] ], 'selectors' => [ '{{WRAPPER}} .ta-hero__card-btn-icon' => 'border-radius: {{SIZE}}{{UNIT}};' ] ] );
		$this->add_control( 'card_btn_sub_color', [ 'label' => esc_html__( 'Couleur du sous-titre', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .ta-hero__card-btn-sub' => 'color: {{VALUE}};' ] ] );
		$this->add_group_control( Group_Control_Typography::get_type(), [ 'name' => 'card_btn_sub_typography', 'selector' => '{{WRAPPER}} .ta-hero__card-btn-sub' ] );
		$this->add_control( 'card_btn_arrow_color', [ 'label' => esc_html__( 'Couleur de la flèche', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .ta-hero__card-btn-arrow' => 'color: {{VALUE}};' ] ] );

		$this->add_control( 'heading_card_badge_style', [ 'label' => esc_html__( 'Badges des choix', 'tools-adapter' ), 'type' => Controls_Manager::HEADING, 'separator' => 'before' ] );
		$this->add_control( 'card_badge_color', [ 'label' => esc_html__( 'Texte', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .ta-hero__card-btn-badge' => 'color: {{VALUE}};' ] ] );
		$this->add_control( 'card_badge_bg', [ 'label' => esc_html__( 'Fond', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .ta-hero__card-btn-badge' => 'background-color: {{VALUE}};' ] ] );
		$this->add_control( 'card_badge_border', [ 'label' => esc_html__( 'Bordure', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .ta-hero__card-btn-badge' => 'border-color: {{VALUE}};' ] ] );
		$this->add_group_control( Group_Control_Typography::get_type(), [ 'name' => 'card_badge_typography', 'selector' => '{{WRAPPER}} .ta-hero__card-btn-badge' ] );
		$this->add_control( 'card_badge_radius', [ 'label' => esc_html__( 'Arrondi', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'size_units' => [ 'px', '%' ], 'range' => [ 'px' => [ 'min' => 0, 'max' => 40 ] ], 'selectors' => [ '{{WRAPPER}} .ta-hero__card-btn-badge' => 'border-radius: {{SIZE}}{{UNIT}};' ] ] );

		$this->add_control( 'heading_card_phone_style', [ 'label' => esc_html__( 'Footer Téléphone', 'tools-adapter' ), 'type' => Controls_Manager::HEADING, 'separator' => 'before' ] );
		$this->add_control( 'card_phone_color', [ 'label' => esc_html__( 'Couleur du numéro', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#DDA853', 'selectors' => [ '{{WRAPPER}} .ta-hero__card-phone-val' => 'color: {{VALUE}};' ] ] );
		$this->add_control( 'card_phone_color_hover', [ 'label' => esc_html__( 'Couleur du numéro (survol)', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#f1c40f', 'selectors' => [ '{{WRAPPER}} .ta-hero__card-phone-val:hover' => 'color: {{VALUE}};' ] ] );
		$this->add_group_control( Group_Control_Typography::get_type(), [ 'name' => 'card_phone_typography', 'selector' => '{{WRAPPER}} .ta-hero__card-phone-val' ] );
		$this->add_control( 'card_phone_label_color', [ 'label' => esc_html__( 'Couleur du libellé', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .ta-hero__card-phone-label' => 'color: {{VALUE}};' ] ] );
		$this->add_control( 'card_phone_icon_color', [ 'label' => esc_html__( 'Couleur de l\'icône téléphone', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .ta-hero__card-phone-icon' => 'color: {{VALUE}};', '{{WRAPPER}} .ta-hero__card-phone-icon svg' => 'fill: {{VALUE}};' ] ] );

		$this->end_controls_section();
	}

	protected function render() {
		$settings   = $this->get_settings_for_display();
		$tag        = in_array( $settings['title_tag'], [ 'h1', 'h2', 'h3', 'div' ], true ) ? $settings['title_tag'] : 'h1';
		$split_mode = 'yes' === ( $settings['enable_split_card'] ?? '' );
		$kenburns   = 'yes' === ( $settings['kenburns_effect'] ?? '' );
		$badges     = ( 'yes' === ( $settings['show_badges'] ?? '' ) && ! empty( $settings['badges'] ) && is_array( $settings['badges'] ) ) ? $settings['badges'] : [];
		$card_arrow = 'yes' === ( $settings['card_show_arrow'] ?? '' );
		$card_acts  = ( $split_mode && ! empty( $settings['card_actions'] ) && is_array( $settings['card_actions'] ) ) ? $settings['card_actions'] : [];
		?>
		<div class="ta-hero<?php echo $split_mode ? ' ta-hero--split' : ''; ?><?php echo $kenburns ? ' ta-hero--kenburns' : ''; ?>">
			<?php if ( $kenburns ) : ?>
				<div class="ta-hero__bg-layer" aria-hidden="true"></div>
			<?php endif; ?>
			<span class="ta-hero__overlay" aria-hidden="true"></span>

			<div class="ta-hero__container">
				<div class="ta-hero__content">
					<?php if ( ! empty( $settings['subtitle'] ) ) : ?>
						<p class="ta-hero__subtitle"><?php echo esc_html( \tools_adapter_translate( $settings['subtitle'] ) ); ?></p>
					<?php endif; ?>

					<?php if ( ! empty( $settings['title'] ) ) : ?>
						<<?php echo esc_attr( $tag ); ?> class="ta-hero__title"><?php echo wp_kses_post( nl2br( esc_html( \tools_adapter_translate( $settings['title'] ) ) ) ); ?></<?php echo esc_attr( $tag ); ?>>
					<?php endif; ?>

					<?php if ( ! empty( $settings['description'] ) ) : ?>
						<p class="ta-hero__description"><?php echo wp_kses_post( nl2br( esc_html( \tools_adapter_translate( $settings['description'] ) ) ) ); ?></p>
					<?php endif; ?>

					<?php if ( ! empty( $badges ) ) : ?>
						<ul class="ta-hero__badges">
							<?php foreach ( $badges as $b ) :
								$b_text = isset( $b['text'] ) ? \tools_adapter_translate( $b['text'] ) : '';
								if ( '' === $b_text ) continue;
								?>
								<li class="ta-hero__badge-item">
									<?php if ( ! empty( $b['icon']['value'] ) ) : ?>
										<span class="ta-hero__badge-icon" aria-hidden="true">
											<?php \Elementor\Icons_Manager::render_icon( $b['icon'], [ 'aria-hidden' => 'true' ] ); ?>
										</span>
									<?php else : ?>
										<span class="ta-hero__badge-icon" aria-hidden="true">✓</span>
									<?php endif; ?>
									<span><?php echo esc_html( $b_text ); ?></span>
								</li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>

					<?php if ( 'yes' === ( $settings['show_primary_btn'] ?? '' ) || 'yes' === ( $settings['show_secondary_btn'] ?? '' ) ) : ?>
						<div class="ta-hero__actions">
							<?php if ( 'yes' === ( $settings['show_primary_btn'] ?? '' ) && ! empty( $settings['primary_btn_text'] ) ) :
								$url    = $settings['primary_btn_link']['url'] ?? '#';
								$target = ! empty( $settings['primary_btn_link']['is_external'] ) ? ' target="_blank"' : '';
								$nofollow = ! empty( $settings['primary_btn_link']['nofollow'] ) ? ' rel="nofollow"' : '';
								?>
								<a class="ta-hero__btn ta-hero__btn--primary" href="<?php echo esc_url( $url ); ?>"<?php echo $target . $nofollow; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>><?php echo esc_html( \tools_adapter_translate( $settings['primary_btn_text'] ) ); ?></a>
							<?php endif; ?>
							<?php if ( 'yes' === ( $settings['show_secondary_btn'] ?? '' ) && ! empty( $settings['secondary_btn_text'] ) ) :
								$url    = $settings['secondary_btn_link']['url'] ?? '#';
								$target = ! empty( $settings['secondary_btn_link']['is_external'] ) ? ' target="_blank"' : '';
								$nofollow = ! empty( $settings['secondary_btn_link']['nofollow'] ) ? ' rel="nofollow"' : '';
								?>
								<a class="ta-hero__btn ta-hero__btn--secondary" href="<?php echo esc_url( $url ); ?>"<?php echo $target . $nofollow; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>><?php echo esc_html( \tools_adapter_translate( $settings['secondary_btn_text'] ) ); ?></a>
							<?php endif; ?>
						</div>
					<?php endif; ?>
				</div>

				<?php if ( $split_mode ) : ?>
					<div class="ta-hero__card">
						<?php if ( ! empty( $settings['card_title'] ) ) : ?>
							<h3 class="ta-hero__card-title"><?php echo esc_html( \tools_adapter_translate( $settings['card_title'] ) ); ?></h3>
						<?php endif; ?>
						<?php if ( ! empty( $settings['card_desc'] ) ) : ?>
							<p class="ta-hero__card-desc"><?php echo esc_html( \tools_adapter_translate( $settings['card_desc'] ) ); ?></p>
						<?php endif; ?>

						<?php if ( ! empty( $card_acts ) ) : ?>
							<div class="ta-hero__card-btns">
								<?php foreach ( $card_acts as $act ) :
									$act_title = isset( $act['title'] ) ? \tools_adapter_translate( $act['title'] ) : '';
									$act_badge = isset( $act['badge'] ) ? \tools_adapter_translate( $act['badge'] ) : '';
									$act_url   = ! empty( $act['link']['url'] ) ? $act['link']['url'] : '#devis';
									$act_tgt   = ! empty( $act['link']['is_external'] ) ? ' target="_blank"' : '';
									$act_rel   = ! empty( $act['link']['nofollow'] ) ? ' rel="nofollow"' : '';
									$act_sub   = ! empty( $act['subtitle'] ) ? \tools_adapter_translate( $act['subtitle'] ) : '';
									// Icon or subtitle → two-line layout with the badge/arrow pushed right.
									$act_rich  = $act_sub || ! empty( $act['icon']['value'] ) || $card_arrow;
									?>
									<a href="<?php echo esc_url( $act_url ); ?>" class="ta-hero__card-btn<?php echo $act_rich ? ' ta-hero__card-btn--rich' : ''; ?>"<?php echo $act_tgt . $act_rel; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
										<?php if ( ! empty( $act['icon']['value'] ) ) : ?>
											<span class="ta-hero__card-btn-icon" aria-hidden="true"><?php \Elementor\Icons_Manager::render_icon( $act['icon'], [ 'aria-hidden' => 'true' ] ); ?></span>
										<?php endif; ?>
										<?php if ( $act_rich ) : ?>
											<span class="ta-hero__card-btn-main">
												<span class="ta-hero__card-btn-text"><?php echo esc_html( $act_title ); ?></span>
												<?php if ( $act_sub ) : ?>
													<span class="ta-hero__card-btn-sub"><?php echo esc_html( $act_sub ); ?></span>
												<?php endif; ?>
											</span>
										<?php else : ?>
											<span class="ta-hero__card-btn-text"><?php echo esc_html( $act_title ); ?></span>
										<?php endif; ?>
										<?php if ( $act_badge ) : ?>
											<span class="ta-hero__card-btn-badge"><?php echo esc_html( $act_badge ); ?></span>
										<?php endif; ?>
										<?php if ( $card_arrow ) : ?>
											<svg class="ta-hero__card-btn-arrow" viewBox="0 0 24 24" width="20" height="20" aria-hidden="true" focusable="false"><path d="M5 12h14M13 6l6 6-6 6" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg>
										<?php endif; ?>
									</a>
								<?php endforeach; ?>
							</div>
						<?php endif; ?>

						<?php if ( ! empty( $settings['card_phone_number'] ) ) : ?>
							<?php
							$phone_icon = ! empty( $settings['card_phone_icon']['value'] );
							$phone_url  = ! empty( $settings['card_phone_url'] ) ? $settings['card_phone_url'] : 'tel:' . preg_replace( '/[^0-9+]/', '', $settings['card_phone_number'] );
							?>
							<div class="ta-hero__card-footer<?php echo $phone_icon ? ' ta-hero__card-footer--icon' : ''; ?>">
								<?php if ( $phone_icon ) : ?>
									<span class="ta-hero__card-phone-icon" aria-hidden="true"><?php \Elementor\Icons_Manager::render_icon( $settings['card_phone_icon'], [ 'aria-hidden' => 'true' ] ); ?></span>
									<span class="ta-hero__card-phone-text">
								<?php endif; ?>
								<?php if ( ! empty( $settings['card_phone_label'] ) ) : ?>
									<span class="ta-hero__card-phone-label"><?php echo esc_html( \tools_adapter_translate( $settings['card_phone_label'] ) ); ?></span>
								<?php endif; ?>
								<a href="<?php echo esc_url( $phone_url, [ 'tel', 'http', 'https', 'mailto' ] ); ?>" class="ta-hero__card-phone-val">
									<?php echo esc_html( $settings['card_phone_number'] ); ?>
								</a>
								<?php if ( $phone_icon ) : ?>
									</span>
								<?php endif; ?>
							</div>
						<?php endif; ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}
}
