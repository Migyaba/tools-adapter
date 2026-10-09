<?php
namespace ToolsAdapter\Widgets;

use Elementor\Controls_Manager;
use ToolsAdapter\Base_Widget;
use ToolsAdapter\Repeater;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Image_Size;
use Elementor\Icons_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Widget: Boîtes d'image — Cartes de services et prestations avec badge, liste et CTA.
 * Supporte un affichage en grille responsive ou en carrousel défilant avec navigation.
 */
class Image_Box extends Base_Widget {

	public function get_name() {
		return 'tools-adapter-image-box';
	}

	public function get_title() {
		return esc_html__( 'Boîtes d\'image (Grille & Carrousel)', 'tools-adapter' );
	}

	public function get_icon() {
		return 'eicon-image-box';
	}

	public function get_categories() {
		return [ 'tools-adapter' ];
	}

	public function get_keywords() {
		return [ 'image box', 'carte', 'service', 'prestation', 'carrousel', 'grille', 'badge', 'features' ];
	}

	public function get_style_depends() {
		return [ 'tools-adapter-image-box' ];
	}

	public function get_script_depends() {
		return [ 'tools-adapter-carousel' ];
	}

	protected function register_controls() {

		// ==========================================
		// SECTION CONTENU : CARTES (REPEATER)
		// ==========================================
		$this->start_controls_section(
			'section_cards',
			[ 'label' => esc_html__( 'Cartes de services', 'tools-adapter' ) ]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'image',
			[
				'label'   => esc_html__( 'Image de la carte', 'tools-adapter' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => [
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				],
			]
		);

		$repeater->add_control(
			'badge_text',
			[
				'label'       => esc_html__( 'Texte du badge (optionnel)', 'tools-adapter' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'RGE QUALIBAT', 'tools-adapter' ),
				'placeholder' => esc_html__( 'ex: RGE QUALIBAT, EXTÉRIEUR...', 'tools-adapter' ),
			]
		);

		$repeater->add_control(
			'badge_icon',
			[
				'label'   => esc_html__( 'Icône du badge', 'tools-adapter' ),
				'type'    => Controls_Manager::ICONS,
				'default' => [
					'value'   => 'fas fa-leaf',
					'library' => 'fa-solid',
				],
			]
		);

		$repeater->add_control(
			'custom_badge_color',
			[
				'label'     => esc_html__( 'Couleur personnalisée du badge', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} .ta-image-box__badge' => 'background-color: {{VALUE}};',
				],
			]
		);

		$repeater->add_control(
			'custom_badge_text_color',
			[
				'label'     => esc_html__( 'Couleur du texte du badge', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} .ta-image-box__badge' => 'color: {{VALUE}};',
				],
			]
		);

		$repeater->add_control(
			'title',
			[
				'label'       => esc_html__( 'Titre', 'tools-adapter' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Isolation Thermique Extérieure (ITE)', 'tools-adapter' ),
				'placeholder' => esc_html__( 'Titre du service...', 'tools-adapter' ),
			]
		);

		$repeater->add_control(
			'description',
			[
				'label'       => esc_html__( 'Description', 'tools-adapter' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 4,
				'default'     => esc_html__( 'Améliorez considérablement votre confort thermique été comme hiver, supprimez les ponts thermiques et allégez vos factures de chauffage grâce à notre certification RGE.', 'tools-adapter' ),
				'placeholder' => esc_html__( 'Description du service...', 'tools-adapter' ),
			]
		);

		$repeater->add_control(
			'features',
			[
				'label'       => esc_html__( 'Liste de caractéristiques (1 ligne = 1 puce)', 'tools-adapter' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 4,
				'default'     => "Polystyrène expansé (blanc & gris)\nLaine de roche & Fibre de bois\nÉligible aux aides MaPrimeRénov' & CEE",
				'placeholder' => esc_html__( "Ligne 1\nLigne 2\nLigne 3", 'tools-adapter' ),
				'description' => esc_html__( 'Chaque saut de ligne créera un point avec une coche.', 'tools-adapter' ),
			]
		);

		$repeater->add_control(
			'link_text',
			[
				'label'       => esc_html__( 'Texte du lien / bouton', 'tools-adapter' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Découvrir l\'ITE', 'tools-adapter' ),
				'placeholder' => esc_html__( 'ex: En savoir plus', 'tools-adapter' ),
			]
		);

		$repeater->add_control(
			'link',
			[
				'label'       => esc_html__( 'Lien / URL', 'tools-adapter' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => 'https://...',
				'default'     => [
					'url'         => '#',
					'is_external' => false,
					'nofollow'    => false,
				],
			]
		);

		$this->add_control(
			'cards',
			[
				'label'       => esc_html__( 'Liste des cartes', 'tools-adapter' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => [
					[
						'title'             => esc_html__( 'Isolation Thermique Extérieure (ITE)', 'tools-adapter' ),
						'badge_text'        => esc_html__( 'RGE QUALIBAT', 'tools-adapter' ),
						'badge_icon'        => [ 'value' => 'fas fa-leaf', 'library' => 'fa-solid' ],
						'custom_badge_color'=> '#15803d',
						'description'       => esc_html__( 'Améliorez considérablement votre confort thermique été comme hiver, supprimez les ponts thermiques et allégez vos factures de chauffage grâce à notre certification RGE.', 'tools-adapter' ),
						'features'          => "Polystyrène expansé (blanc & gris)\nLaine de roche & Fibre de bois\nÉligible aux aides MaPrimeRénov' & CEE",
						'link_text'         => esc_html__( 'Découvrir l\'ITE', 'tools-adapter' ),
					],
					[
						'title'             => esc_html__( 'Ravalement de Façades & Crépissage', 'tools-adapter' ),
						'badge_text'        => esc_html__( 'EXTÉRIEUR', 'tools-adapter' ),
						'badge_icon'        => [ 'value' => '', 'library' => '' ],
						'custom_badge_color'=> '#0b2545',
						'description'       => esc_html__( 'Restauration complète de façades neuves ou anciennes alsaciennes. Traitement des fissures, hydrofugation, enduits talochés, grattés et peintures minérales longue durée.', 'tools-adapter' ),
						'features'          => "Traitement curatif fissures & démoussage\nCrépis projetés, talochés & siloxanés\nRénovation colombages & boiseries",
						'link_text'         => esc_html__( 'En savoir plus sur les façades', 'tools-adapter' ),
					],
					[
						'title'             => esc_html__( 'Peinture Intérieure & Décoration', 'tools-adapter' ),
						'badge_text'        => esc_html__( 'INTÉRIEUR', 'tools-adapter' ),
						'badge_icon'        => [ 'value' => '', 'library' => '' ],
						'custom_badge_color'=> '#0b2545',
						'description'       => esc_html__( 'Mise en peinture soignée des murs, plafonds et boiseries. Création d\'ambiances uniques avec finitions haut de gamme (velours, mat profond, stucs, béton ciré type Mortex).', 'tools-adapter' ),
						'features'          => "Peintures dépolluantes & sans odeur\nRénovation après dégâts des eaux\nRevêtements muraux & pose de parquets",
						'link_text'         => esc_html__( 'Explorer la peinture intérieure', 'tools-adapter' ),
					],
				],
				'title_field' => '{{{ title }}}',
			]
		);

		$this->end_controls_section();

		// ==========================================
		// SECTION CONTENU : DISPOSITION & MODE
		// ==========================================
		$this->start_controls_section(
			'section_layout',
			[ 'label' => esc_html__( 'Disposition & Affichage', 'tools-adapter' ) ]
		);

		$this->add_control(
			'layout',
			[
				'label'        => esc_html__( 'Mode d\'affichage', 'tools-adapter' ),
				'type'         => Controls_Manager::SELECT,
				'default'      => 'grid',
				'options'      => [
					'grid'     => esc_html__( 'Grille statique', 'tools-adapter' ),
					'carousel' => esc_html__( 'Carrousel défilant', 'tools-adapter' ),
				],
				'prefix_class' => 'ta-image-box-layout--',
			]
		);

		// Colonnes pour Grille
		$this->add_responsive_control(
			'columns',
			[
				'label'          => esc_html__( 'Nombre de colonnes', 'tools-adapter' ),
				'type'           => Controls_Manager::NUMBER,
				'min'            => 1,
				'max'            => 6,
				'default'        => 3,
				'tablet_default' => 2,
				'mobile_default' => 1,
				'condition'      => [ 'layout' => 'grid' ],
				'selectors'      => [
					'{{WRAPPER}} .ta-image-box__grid' => 'grid-template-columns: repeat({{VALUE}}, 1fr);',
				],
			]
		);

		// Slides à afficher pour Carrousel
		$this->add_responsive_control(
			'slides_to_show',
			[
				'label'          => esc_html__( 'Cartes visibles simultanément', 'tools-adapter' ),
				'type'           => Controls_Manager::NUMBER,
				'min'            => 1,
				'max'            => 6,
				'default'        => 3,
				'tablet_default' => 2,
				'mobile_default' => 1,
				'condition'      => [ 'layout' => 'carousel' ],
			]
		);

		$this->add_responsive_control(
			'gap',
			[
				'label'      => esc_html__( 'Espacement entre les cartes', 'tools-adapter' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem' ],
				'range'      => [
					'px' => [ 'min' => 0, 'max' => 60 ],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 30,
				],
				'selectors'  => [
					'{{WRAPPER}} .ta-image-box__grid' => 'gap: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} [data-ta-carousel]'  => '--ta-carousel-gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'title_tag',
			[
				'label'   => esc_html__( 'Balise HTML du titre', 'tools-adapter' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'h3',
				'options' => [
					'h1'   => 'H1',
					'h2'   => 'H2',
					'h3'   => 'H3',
					'h4'   => 'H4',
					'h5'   => 'H5',
					'h6'   => 'H6',
					'p'    => 'p',
					'div'  => 'div',
					'span' => 'span',
				],
			]
		);

		$this->add_control(
			'content_alignment',
			[
				'label'     => esc_html__( 'Alignement du contenu', 'tools-adapter' ),
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
				'default'   => 'left',
				'selectors' => [
					'{{WRAPPER}} .ta-image-box__content'     => 'text-align: {{VALUE}};',
					'{{WRAPPER}} .ta-image-box__features li' => 'justify-content: {{VALUE}};',
					'{{WRAPPER}} .ta-image-box__footer'      => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'feature_icon',
			[
				'label'   => esc_html__( 'Icône des puces', 'tools-adapter' ),
				'type'    => Controls_Manager::ICONS,
				'default' => [
					'value'   => 'fas fa-check',
					'library' => 'fa-solid',
				],
			]
		);

		$this->add_control(
			'link_icon',
			[
				'label'   => esc_html__( 'Icône du lien', 'tools-adapter' ),
				'type'    => Controls_Manager::ICONS,
				'default' => [
					'value'   => 'fas fa-arrow-right',
					'library' => 'fa-solid',
				],
			]
		);

		$this->end_controls_section();

		// ==========================================
		// SECTION CONTENU : OPTIONS CARROUSEL
		// ==========================================
		$this->start_controls_section(
			'section_carousel_options',
			[
				'label'     => esc_html__( 'Options du carrousel', 'tools-adapter' ),
				'condition' => [ 'layout' => 'carousel' ],
			]
		);

		$this->add_control(
			'autoplay',
			[
				'label'        => esc_html__( 'Défilement automatique', 'tools-adapter' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'no',
			]
		);

		$this->add_control(
			'autoplay_speed',
			[
				'label'     => esc_html__( 'Vitesse d\'autoplay (ms)', 'tools-adapter' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 5000,
				'min'       => 1000,
				'max'       => 15000,
				'step'      => 500,
				'condition' => [ 'autoplay' => 'yes' ],
			]
		);

		$this->add_control(
			'transition_speed',
			[
				'label'   => esc_html__( 'Vitesse de transition (ms)', 'tools-adapter' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 450,
				'min'     => 100,
				'max'     => 2000,
				'step'    => 50,
			]
		);

		$this->add_control(
			'show_arrows',
			[
				'label'        => esc_html__( 'Flèches de navigation', 'tools-adapter' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'show_dots',
			[
				'label'        => esc_html__( 'Points de pagination', 'tools-adapter' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->end_controls_section();

		// ==========================================
		// STYLE : CARTE GLOBALE
		// ==========================================
		$this->start_controls_section(
			'section_style_card',
			[
				'label' => esc_html__( 'Carte globale', 'tools-adapter' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs( 'tabs_card_style' );

		$this->start_controls_tab(
			'tab_card_normal',
			[ 'label' => esc_html__( 'Normal', 'tools-adapter' ) ]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'card_background',
				'label'    => esc_html__( 'Arrière-plan', 'tools-adapter' ),
				'types'    => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .ta-image-box__card',
				'fields_options' => [
					'background' => [ 'default' => 'classic' ],
					'color'      => [ 'default' => '#ffffff' ],
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'card_border',
				'selector' => '{{WRAPPER}} .ta-image-box__card',
				'fields_options' => [
					'border' => [ 'default' => 'solid' ],
					'width'  => [ 'default' => [ 'top' => 1, 'right' => 1, 'bottom' => 1, 'left' => 1, 'unit' => 'px' ] ],
					'color'  => [ 'default' => '#e2e8f0' ],
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'card_box_shadow',
				'selector' => '{{WRAPPER}} .ta-image-box__card',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_card_hover',
			[ 'label' => esc_html__( 'Survol (Hover)', 'tools-adapter' ) ]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'card_background_hover',
				'types'    => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .ta-image-box__card:hover',
			]
		);

		$this->add_control(
			'card_border_color_hover',
			[
				'label'     => esc_html__( 'Couleur de bordure au survol', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(0, 119, 182, 0.4)',
				'selectors' => [
					'{{WRAPPER}} .ta-image-box__card:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'card_box_shadow_hover',
				'selector' => '{{WRAPPER}} .ta-image-box__card:hover',
				'fields_options' => [
					'box_shadow' => [
						'default' => [
							'horizontal' => 0,
							'vertical'   => 16,
							'blur'       => 40,
							'spread'     => 0,
							'color'      => 'rgba(11, 37, 69, 0.12)',
						],
					],
				],
			]
		);

		$this->add_control(
			'card_translate_hover',
			[
				'label'      => esc_html__( 'Translation verticale au survol', 'tools-adapter' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => [
					'px' => [ 'min' => -30, 'max' => 0 ],
				],
				'default'    => [ 'unit' => 'px', 'size' => -8 ],
				'selectors'  => [
					'{{WRAPPER}} .ta-image-box__card:hover' => 'transform: translateY({{SIZE}}{{UNIT}});',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'card_border_radius',
			[
				'label'      => esc_html__( 'Rayon des coins (Border Radius)', 'tools-adapter' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'default'    => [
					'top'      => 16,
					'right'    => 16,
					'bottom'   => 16,
					'left'     => 16,
					'unit'     => 'px',
					'isLinked' => true,
				],
				'selectors'  => [
					'{{WRAPPER}} .ta-image-box__card' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator'  => 'before',
			]
		);

		$this->add_responsive_control(
			'card_content_padding',
			[
				'label'      => esc_html__( 'Marge interne du contenu (Padding)', 'tools-adapter' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'default'    => [
					'top'      => 28,
					'right'    => 28,
					'bottom'   => 28,
					'left'     => 28,
					'unit'     => 'px',
					'isLinked' => true,
				],
				'selectors'  => [
					'{{WRAPPER}} .ta-image-box__content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		// ==========================================
		// STYLE : IMAGE
		// ==========================================
		$this->start_controls_section(
			'section_style_image',
			[
				'label' => esc_html__( 'Image de couverture', 'tools-adapter' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'image_height',
			[
				'label'      => esc_html__( 'Hauteur de l\'image', 'tools-adapter' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'vh' ],
				'range'      => [
					'px' => [ 'min' => 120, 'max' => 600 ],
				],
				'default'    => [ 'unit' => 'px', 'size' => 240 ],
				'selectors'  => [
					'{{WRAPPER}} .ta-image-box__media' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'image_zoom_hover',
			[
				'label'        => esc_html__( 'Effet de zoom au survol', 'tools-adapter' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->end_controls_section();

		// ==========================================
		// STYLE : BADGE FLOTTANT
		// ==========================================
		$this->start_controls_section(
			'section_style_badge',
			[
				'label' => esc_html__( 'Badge sur l\'image', 'tools-adapter' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'badge_default_bg',
			[
				'label'     => esc_html__( 'Couleur de fond par défaut', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(11, 37, 69, 0.9)',
				'selectors' => [
					'{{WRAPPER}} .ta-image-box__badge' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'badge_default_text_color',
			[
				'label'     => esc_html__( 'Couleur du texte par défaut', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .ta-image-box__badge' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'badge_typography',
				'selector' => '{{WRAPPER}} .ta-image-box__badge',
			]
		);

		$this->add_responsive_control(
			'badge_padding',
			[
				'label'      => esc_html__( 'Padding du badge', 'tools-adapter' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'default'    => [
					'top'      => 4,
					'right'    => 12,
					'bottom'   => 4,
					'left'     => 12,
					'unit'     => 'px',
					'isLinked' => false,
				],
				'selectors'  => [
					'{{WRAPPER}} .ta-image-box__badge' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'badge_border_radius',
			[
				'label'      => esc_html__( 'Rayon des coins (Border Radius)', 'tools-adapter' ),
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
					'{{WRAPPER}} .ta-image-box__badge' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		// ==========================================
		// STYLE : TITRE
		// ==========================================
		$this->start_controls_section(
			'section_style_title',
			[
				'label' => esc_html__( 'Titre', 'tools-adapter' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'title_font_size',
			[
				'label'      => esc_html__( 'Taille du titre', 'tools-adapter' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem' ],
				'range'      => [
					'px'  => [ 'min' => 12, 'max' => 64 ],
					'rem' => [ 'min' => 0.8, 'max' => 4, 'step' => 0.1 ],
					'em'  => [ 'min' => 0.8, 'max' => 4, 'step' => 0.1 ],
				],
				'selectors'  => [
					'{{WRAPPER}} .ta-image-box__title'   => 'font-size: {{SIZE}}{{UNIT}}; --ta-title-font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ta-image-box__title a' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'title_color',
			[
				'label'     => esc_html__( 'Couleur du titre', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#0b2545',
				'selectors' => [
					'{{WRAPPER}} .ta-image-box__title' => 'color: {{VALUE}};',
					'{{WRAPPER}} .ta-image-box__title a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'title_color_hover',
			[
				'label'     => esc_html__( 'Couleur au survol', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#0077b6',
				'selectors' => [
					'{{WRAPPER}} .ta-image-box__card:hover .ta-image-box__title' => 'color: {{VALUE}};',
					'{{WRAPPER}} .ta-image-box__title a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .ta-image-box__title, {{WRAPPER}} .ta-image-box__title a',
			]
		);

		$this->add_responsive_control(
			'title_spacing',
			[
				'label'      => esc_html__( 'Marge inférieure', 'tools-adapter' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'default'    => [ 'unit' => 'px', 'size' => 12 ],
				'selectors'  => [
					'{{WRAPPER}} .ta-image-box__title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		// ==========================================
		// STYLE : DESCRIPTION
		// ==========================================
		$this->start_controls_section(
			'section_style_description',
			[
				'label' => esc_html__( 'Description', 'tools-adapter' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'description_font_size',
			[
				'label'      => esc_html__( 'Taille du texte', 'tools-adapter' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem' ],
				'range'      => [
					'px'  => [ 'min' => 10, 'max' => 36 ],
					'rem' => [ 'min' => 0.7, 'max' => 2.5, 'step' => 0.1 ],
					'em'  => [ 'min' => 0.7, 'max' => 2.5, 'step' => 0.1 ],
				],
				'selectors'  => [
					'{{WRAPPER}} .ta-image-box__desc'   => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ta-image-box__desc p' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'description_color',
			[
				'label'     => esc_html__( 'Couleur du texte', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#64748b',
				'selectors' => [
					'{{WRAPPER}} .ta-image-box__desc'   => 'color: {{VALUE}};',
					'{{WRAPPER}} .ta-image-box__desc p' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'description_typography',
				'selector' => '{{WRAPPER}} .ta-image-box__desc, {{WRAPPER}} .ta-image-box__desc p',
			]
		);

		$this->add_responsive_control(
			'description_spacing',
			[
				'label'      => esc_html__( 'Marge inférieure', 'tools-adapter' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'default'    => [ 'unit' => 'px', 'size' => 20 ],
				'selectors'  => [
					'{{WRAPPER}} .ta-image-box__desc' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		// ==========================================
		// STYLE : LISTE DE CARACTÉRISTIQUES (PUCES)
		// ==========================================
		$this->start_controls_section(
			'section_style_features',
			[
				'label' => esc_html__( 'Liste de caractéristiques (Puces)', 'tools-adapter' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'features_text_color',
			[
				'label'     => esc_html__( 'Couleur du texte', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#1e293b',
				'selectors' => [
					'{{WRAPPER}} .ta-image-box__features li' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'features_typography',
				'selector' => '{{WRAPPER}} .ta-image-box__features li',
			]
		);

		$this->add_control(
			'features_icon_color',
			[
				'label'     => esc_html__( 'Couleur de l\'icône (coche)', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#0077b6',
				'selectors' => [
					'{{WRAPPER}} .ta-image-box__features .ta-feature-icon' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'features_icon_size',
			[
				'label'      => esc_html__( 'Taille de l\'icône', 'tools-adapter' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => [ 'px' => [ 'min' => 10, 'max' => 32 ] ],
				'default'    => [ 'unit' => 'px', 'size' => 14 ],
				'selectors'  => [
					'{{WRAPPER}} .ta-image-box__features .ta-feature-icon' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'features_item_spacing',
			[
				'label'      => esc_html__( 'Espacement entre les points', 'tools-adapter' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => [ 'px' => [ 'min' => 4, 'max' => 30 ] ],
				'default'    => [ 'unit' => 'px', 'size' => 8 ],
				'selectors'  => [
					'{{WRAPPER}} .ta-image-box__features li:not(:last-child)' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'features_border_top',
			[
				'label'        => esc_html__( 'Séparateur supérieur', 'tools-adapter' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->end_controls_section();

		// ==========================================
		// STYLE : BOUTON / LIEN D'ACTION
		// ==========================================
		$this->start_controls_section(
			'section_style_button',
			[
				'label' => esc_html__( 'Bouton / Lien d\'action', 'tools-adapter' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'link_color',
			[
				'label'     => esc_html__( 'Couleur du lien', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#0077b6',
				'selectors' => [
					'{{WRAPPER}} .ta-image-box__action' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'link_color_hover',
			[
				'label'     => esc_html__( 'Couleur au survol', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#0b2545',
				'selectors' => [
					'{{WRAPPER}} .ta-image-box__action:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}} .ta-image-box__card:hover .ta-image-box__action' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'link_typography',
				'selector' => '{{WRAPPER}} .ta-image-box__action',
			]
		);

		$this->add_responsive_control(
			'link_spacing_top',
			[
				'label'      => esc_html__( 'Marge supérieure du lien', 'tools-adapter' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => [ 'px' => [ 'min' => 0, 'max' => 50 ] ],
				'default'    => [ 'unit' => 'px', 'size' => 20 ],
				'selectors'  => [
					'{{WRAPPER}} .ta-image-box__footer' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		// ==========================================
		// STYLE : NAVIGATION DU CARROUSEL
		// ==========================================
		$this->start_controls_section(
			'section_style_carousel_nav',
			[
				'label'     => esc_html__( 'Flèches & Points du Carrousel', 'tools-adapter' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [ 'layout' => 'carousel' ],
			]
		);

		$this->add_control(
			'nav_arrows_heading',
			[
				'label'     => esc_html__( 'Flèches Précédent / Suivant', 'tools-adapter' ),
				'type'      => Controls_Manager::HEADING,
				'condition' => [ 'show_arrows' => 'yes' ],
			]
		);

		$this->add_control(
			'arrow_color',
			[
				'label'     => esc_html__( 'Couleur de l\'icône flèche', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#0b2545',
				'selectors' => [
					'{{WRAPPER}} .ta-carousel-arrow' => 'color: {{VALUE}};',
				],
				'condition' => [ 'show_arrows' => 'yes' ],
			]
		);

		$this->add_control(
			'arrow_bg',
			[
				'label'     => esc_html__( 'Couleur de fond de la flèche', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .ta-carousel-arrow' => 'background-color: {{VALUE}};',
				],
				'condition' => [ 'show_arrows' => 'yes' ],
			]
		);

		$this->add_control(
			'arrow_bg_hover',
			[
				'label'     => esc_html__( 'Couleur de fond au survol', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#0077b6',
				'selectors' => [
					'{{WRAPPER}} .ta-carousel-arrow:hover' => 'background-color: {{VALUE}}; color: #ffffff;',
				],
				'condition' => [ 'show_arrows' => 'yes' ],
			]
		);

		$this->add_control(
			'nav_dots_heading',
			[
				'label'     => esc_html__( 'Points de pagination (Dots)', 'tools-adapter' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [ 'show_dots' => 'yes' ],
			]
		);

		$this->add_control(
			'dot_color',
			[
				'label'     => esc_html__( 'Couleur du point inactif', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#cbd5e1',
				'selectors' => [
					'{{WRAPPER}} .ta-carousel__dot, {{WRAPPER}} .ta-carousel-dot' => 'background-color: {{VALUE}};',
				],
				'condition' => [ 'show_dots' => 'yes' ],
			]
		);

		$this->add_control(
			'dot_color_active',
			[
				'label'     => esc_html__( 'Couleur du point actif', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#0077b6',
				'selectors' => [
					'{{WRAPPER}} .ta-carousel__dot.is-active, {{WRAPPER}} .ta-carousel-dot.is-active' => 'background-color: {{VALUE}};',
				],
				'condition' => [ 'show_dots' => 'yes' ],
			]
		);

		$this->end_controls_section();

	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		if ( empty( $settings['cards'] ) ) {
			return;
		}

		$is_carousel  = ( 'carousel' === $settings['layout'] );
		$allowed_tags = [ 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p', 'div', 'span' ];
		$title_tag    = ( ! empty( $settings['title_tag'] ) && in_array( $settings['title_tag'], $allowed_tags, true ) ) ? $settings['title_tag'] : 'h3';

		// Carrousel configuration data attributes
		$carousel_attrs = '';
		if ( $is_carousel ) {
			$slides_desktop = ! empty( $settings['slides_to_show'] ) ? (int) $settings['slides_to_show'] : 3;
			$slides_tablet  = ! empty( $settings['slides_to_show_tablet'] ) ? (int) $settings['slides_to_show_tablet'] : 2;
			$slides_mobile  = ! empty( $settings['slides_to_show_mobile'] ) ? (int) $settings['slides_to_show_mobile'] : 1;
			$gap            = ! empty( $settings['gap']['size'] ) ? (int) $settings['gap']['size'] : 30;
			$autoplay       = ( 'yes' === $settings['autoplay'] ) ? '1' : '0';
			$autoplay_speed = ! empty( $settings['autoplay_speed'] ) ? (int) $settings['autoplay_speed'] : 5000;
			$trans_speed    = ! empty( $settings['transition_speed'] ) ? (int) $settings['transition_speed'] : 450;

			$carousel_attrs = sprintf(
				'data-ta-carousel data-slides-show="%d" data-slides-show-tablet="%d" data-slides-show-mobile="%d" data-gap="%d" data-autoplay="%s" data-autoplay-speed="%d" data-transition-speed="%d"',
				$slides_desktop,
				$slides_tablet,
				$slides_mobile,
				$gap,
				$autoplay,
				$autoplay_speed,
				$trans_speed
			);
		}
		?>
		<div class="ta-image-box-wrapper <?php echo $is_carousel ? 'ta-image-box--carousel-mode' : 'ta-image-box--grid-mode'; ?>" <?php echo $carousel_attrs; ?>>
			
			<?php if ( $is_carousel && 'yes' === $settings['show_arrows'] ) : ?>
				<div class="ta-carousel-nav-header">
					<button type="button" class="ta-carousel-arrow ta-carousel-prev" data-carousel-prev aria-label="<?php esc_attr_e( 'Précédent', 'tools-adapter' ); ?>">
						<i class="fas fa-chevron-left" aria-hidden="true"></i>
					</button>
					<button type="button" class="ta-carousel-arrow ta-carousel-next" data-carousel-next aria-label="<?php esc_attr_e( 'Suivant', 'tools-adapter' ); ?>">
						<i class="fas fa-chevron-right" aria-hidden="true"></i>
					</button>
				</div>
			<?php endif; ?>

			<div class="<?php echo $is_carousel ? 'ta-carousel-viewport' : 'ta-image-box__grid'; ?>" <?php echo $is_carousel ? 'data-carousel-viewport' : ''; ?>>
				<div class="<?php echo $is_carousel ? 'ta-carousel-track' : 'ta-image-box__grid-inner'; ?>" <?php echo $is_carousel ? 'data-carousel-track' : ''; ?>>
					
					<?php foreach ( $settings['cards'] as $index => $card ) :
						$has_image = ! empty( $card['image']['url'] );
						$has_badge = ! empty( $card['badge_text'] );
						$has_link  = ! empty( $card['link']['url'] );

						$card_class = 'ta-image-box__card elementor-repeater-item-' . esc_attr( $card['_id'] );
						if ( $is_carousel ) {
							$card_class .= ' ta-carousel-slide';
						}

						$card_title = ! empty( $card['title'] ) ? \tools_adapter_translate( $card['title'] ) : '';
						$card_badge = ! empty( $card['badge_text'] ) ? \tools_adapter_translate( $card['badge_text'] ) : '';
						$card_desc  = ! empty( $card['description'] ) ? \tools_adapter_translate( $card['description'] ) : '';
						$card_btn   = ! empty( $card['link_text'] ) ? \tools_adapter_translate( $card['link_text'] ) : '';

						$link_target = ! empty( $card['link']['is_external'] ) ? ' target="_blank"' : '';
						$rel_parts   = [];
						if ( ! empty( $card['link']['is_external'] ) ) {
							$rel_parts[] = 'noopener';
							$rel_parts[] = 'noreferrer';
						}
						if ( ! empty( $card['link']['nofollow'] ) ) {
							$rel_parts[] = 'nofollow';
						}
						$link_rel = ! empty( $rel_parts ) ? ' rel="' . esc_attr( implode( ' ', $rel_parts ) ) . '"' : '';
					?>
						<article class="<?php echo esc_attr( $card_class ); ?>" <?php echo $is_carousel ? 'data-carousel-slide' : ''; ?>>
							
							<?php if ( $has_image ) : ?>
								<div class="ta-image-box__media">
									<img src="<?php echo esc_url( $card['image']['url'] ); ?>" alt="<?php echo esc_attr( $card_title ); ?>" class="ta-image-box__img" loading="lazy">
									
									<?php if ( $has_badge ) : ?>
										<div class="ta-image-box__badge">
											<?php if ( ! empty( $card['badge_icon']['value'] ) ) : ?>
												<span class="ta-badge-icon">
													<?php Icons_Manager::render_icon( $card['badge_icon'], [ 'aria-hidden' => 'true' ] ); ?>
												</span>
											<?php endif; ?>
											<span><?php echo esc_html( $card_badge ); ?></span>
										</div>
									<?php endif; ?>
								</div>
							<?php endif; ?>

							<div class="ta-image-box__content">
								<?php if ( ! empty( $card_title ) ) : ?>
									<<?php echo esc_attr( $title_tag ); ?> class="ta-image-box__title">
										<?php if ( $has_link ) : ?>
											<a href="<?php echo esc_url( $card['link']['url'] ); ?>"<?php echo $link_target . $link_rel; ?>>
												<?php echo esc_html( $card_title ); ?>
											</a>
										<?php else : ?>
											<?php echo esc_html( $card_title ); ?>
										<?php endif; ?>
									</<?php echo esc_attr( $title_tag ); ?>>
								<?php endif; ?>

								<?php if ( ! empty( $card_desc ) ) : ?>
									<div class="ta-image-box__desc">
										<?php echo wp_kses_post( $card_desc ); ?>
									</div>
								<?php endif; ?>

								<?php
								if ( ! empty( $card['features'] ) ) :
									$features_raw   = \tools_adapter_translate( $card['features'] );
									$features_lines = array_filter( array_map( 'trim', explode( "\n", str_replace( "\r", '', $features_raw ) ) ) );
									if ( ! empty( $features_lines ) ) :
										$border_class = ( 'yes' === $settings['features_border_top'] ) ? 'has-border-top' : '';
								?>
									<ul class="ta-image-box__features <?php echo esc_attr( $border_class ); ?>">
										<?php foreach ( $features_lines as $feature ) : ?>
											<li>
												<span class="ta-feature-icon">
													<?php Icons_Manager::render_icon( $settings['feature_icon'], [ 'aria-hidden' => 'true' ] ); ?>
												</span>
												<span class="ta-feature-text"><?php echo esc_html( $feature ); ?></span>
											</li>
										<?php endforeach; ?>
									</ul>
								<?php
									endif;
								endif;
								?>

								<?php if ( $has_link && ! empty( $card_btn ) ) : ?>
									<div class="ta-image-box__footer">
										<a href="<?php echo esc_url( $card['link']['url'] ); ?>" class="ta-image-box__action"<?php echo $link_target . $link_rel; ?>>
											<span><?php echo esc_html( $card_btn ); ?></span>
											<?php if ( ! empty( $settings['link_icon']['value'] ) ) : ?>
												<span class="ta-link-icon">
													<?php Icons_Manager::render_icon( $settings['link_icon'], [ 'aria-hidden' => 'true' ] ); ?>
												</span>
											<?php endif; ?>
										</a>
									</div>
								<?php endif; ?>
							</div>

						</article>
					<?php endforeach; ?>

				</div>
			</div>

			<?php if ( $is_carousel && 'yes' === $settings['show_dots'] ) : ?>
				<div class="ta-carousel__dots ta-carousel-dots" data-carousel-dots></div>
			<?php endif; ?>

		</div>
		<?php
	}
}
