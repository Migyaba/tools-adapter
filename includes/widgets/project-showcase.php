<?php
namespace ToolsAdapter\Widgets;

use Elementor\Controls_Manager;
use ToolsAdapter\Base_Widget;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Widget: Galerie Réalisation (Miniatures & Plein Écran) — visionneuse de projet interactive
 * avec grand visualiseur, bandeau de miniatures défilant, compteur, boutons de contrôle et modale diaporama plein écran.
 */
class Project_Showcase extends Base_Widget {

	public function get_name() {
		return 'tools-adapter-project-showcase';
	}

	public function get_title() {
		return esc_html__( 'Galerie Réalisation (Miniatures & Plein Écran)', 'tools-adapter' );
	}

	public function get_icon() {
		return 'eicon-gallery-grid';
	}

	public function get_categories() {
		return [ 'tools-adapter' ];
	}

	public function get_keywords() {
		return [ 'galerie', 'photos', 'miniatures', 'diaporama', 'plein écran', 'lightbox', 'projet', 'terrasse', 'balcon', 'portfolio' ];
	}

	public function get_style_depends() {
		return [ 'tools-adapter-project-showcase' ];
	}

	public function get_script_depends() {
		return [ 'tools-adapter-project-showcase' ];
	}

	/**
	 * Default fallback photos (Fiorellino Terrasse & Balcon project).
	 *
	 * @return array<int,array{url:string,alt:string}>
	 */
	private function get_default_photos() {
		return [
			[ 'url' => 'https://www.fiorellino.fr/images/realisations/terrasse-55-photo.jpg', 'alt' => 'Terrasse paysagère d\'exception — Vue d\'ensemble' ],
			[ 'url' => 'https://www.fiorellino.fr/images/realisations/terrasse-55-photo-1.jpg', 'alt' => 'Terrasse paysagère — Pots italiens et claustras' ],
			[ 'url' => 'https://www.fiorellino.fr/images/realisations/terrasse-55-photo-2.jpg', 'alt' => 'Terrasse paysagère — Mobilier design et banquettes' ],
			[ 'url' => 'https://www.fiorellino.fr/images/realisations/terrasse-55-photo-3.jpg', 'alt' => 'Terrasse paysagère — Perspectives et stores bannes' ],
			[ 'url' => 'https://www.fiorellino.fr/images/realisations/terrasse-55-photo-4.jpg', 'alt' => 'Terrasse paysagère — Claustras blancs sur mesure' ],
			[ 'url' => 'https://www.fiorellino.fr/images/realisations/terrasse-55-photo-5.jpg', 'alt' => 'Terrasse paysagère — Jasmins et graminées' ],
			[ 'url' => 'https://www.fiorellino.fr/images/realisations/terrasse-55-photo-6.jpg', 'alt' => 'Terrasse paysagère — Plancher en teck naturel' ],
			[ 'url' => 'https://www.fiorellino.fr/images/realisations/terrasse-55-photo-7.jpg', 'alt' => 'Terrasse paysagère — Topiaires en pots' ],
		];
	}

	protected function register_controls() {
		// ==========================================
		// SECTION CONTENU : GALERIE DE PHOTOS
		// ==========================================
		$this->start_controls_section(
			'section_gallery',
			[ 'label' => esc_html__( 'Photos de la Réalisation', 'tools-adapter' ) ]
		);

		$this->add_control(
			'gallery',
			[
				'label'       => esc_html__( 'Sélectionner les photos', 'tools-adapter' ),
				'type'        => Controls_Manager::GALLERY,
				'default'     => [],
				'description' => esc_html__( 'Choisissez plusieurs photographies dans votre médiathèque WordPress (des images de démonstration s\'affichent par défaut si vide).', 'tools-adapter' ),
			]
		);

		$this->end_controls_section();

		// ==========================================
		// SECTION CONTENU : VISUALISEUR PRINCIPAL
		// ==========================================
		$this->start_controls_section(
			'section_main_viewer',
			[ 'label' => esc_html__( 'Visualiseur Principal', 'tools-adapter' ) ]
		);

		$this->add_control(
			'zoom_effect',
			[
				'label'        => esc_html__( 'Effet de zoom au survol', 'tools-adapter' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Oui', 'tools-adapter' ),
				'label_off'    => esc_html__( 'Non', 'tools-adapter' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'show_counter',
			[
				'label'        => esc_html__( 'Afficher le badge compteur', 'tools-adapter' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Oui', 'tools-adapter' ),
				'label_off'    => esc_html__( 'Non', 'tools-adapter' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'counter_format',
			[
				'label'       => esc_html__( 'Format du compteur', 'tools-adapter' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => 'Photo {current} / {total}',
				'description' => esc_html__( 'Balises dynamiques : {current} pour l\'index en cours, {total} pour le nombre total de photos.', 'tools-adapter' ),
				'condition'   => [ 'show_counter' => 'yes' ],
			]
		);

		$this->add_control(
			'show_arrows',
			[
				'label'        => esc_html__( 'Afficher les flèches (Précédent / Suivant)', 'tools-adapter' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Oui', 'tools-adapter' ),
				'label_off'    => esc_html__( 'Non', 'tools-adapter' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'show_fullscreen_btn',
			[
				'label'        => esc_html__( 'Afficher le bouton Plein Écran dans l\'image', 'tools-adapter' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Oui', 'tools-adapter' ),
				'label_off'    => esc_html__( 'Non', 'tools-adapter' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->end_controls_section();

		// ==========================================
		// SECTION CONTENU : MINIATURES
		// ==========================================
		$this->start_controls_section(
			'section_thumbnails',
			[ 'label' => esc_html__( 'Bandeau de Miniatures', 'tools-adapter' ) ]
		);

		$this->add_control(
			'show_thumbs',
			[
				'label'        => esc_html__( 'Afficher les miniatures', 'tools-adapter' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Oui', 'tools-adapter' ),
				'label_off'    => esc_html__( 'Non', 'tools-adapter' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'show_progress_bar',
			[
				'label'        => esc_html__( 'Afficher la barre de progression', 'tools-adapter' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Oui', 'tools-adapter' ),
				'label_off'    => esc_html__( 'Non', 'tools-adapter' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => [ 'show_thumbs' => 'yes' ],
			]
		);

		$this->end_controls_section();

		// ==========================================
		// SECTION CONTENU : BARRE INFÉRIEURE
		// ==========================================
		$this->start_controls_section(
			'section_toolbar',
			[ 'label' => esc_html__( 'Barre d\'Informations Inférieure', 'tools-adapter' ) ]
		);

		$this->add_control(
			'show_footer_toolbar',
			[
				'label'        => esc_html__( 'Afficher la barre inférieure', 'tools-adapter' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Oui', 'tools-adapter' ),
				'label_off'    => esc_html__( 'Non', 'tools-adapter' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'show_footer_count',
			[
				'label'        => esc_html__( 'Afficher le texte d\'infos (gauche)', 'tools-adapter' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Oui', 'tools-adapter' ),
				'label_off'    => esc_html__( 'Non', 'tools-adapter' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => [ 'show_footer_toolbar' => 'yes' ],
			]
		);

		$this->add_control(
			'footer_count_text',
			[
				'label'       => esc_html__( 'Format du texte d\'infos', 'tools-adapter' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '{count} photographies haute définition',
				'description' => esc_html__( 'Balise {count} remplacée automatiquement par le nombre de photos.', 'tools-adapter' ),
				'condition'   => [ 'show_footer_toolbar' => 'yes', 'show_footer_count' => 'yes' ],
			]
		);

		$this->add_control(
			'show_diaporama_btn',
			[
				'label'        => esc_html__( 'Afficher le bouton Diaporama (droite)', 'tools-adapter' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Oui', 'tools-adapter' ),
				'label_off'    => esc_html__( 'Non', 'tools-adapter' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => [ 'show_footer_toolbar' => 'yes' ],
			]
		);

		$this->add_control(
			'diaporama_btn_text',
			[
				'label'     => esc_html__( 'Texte du bouton Diaporama', 'tools-adapter' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'Diaporama Plein Écran', 'tools-adapter' ),
				'condition' => [ 'show_footer_toolbar' => 'yes', 'show_diaporama_btn' => 'yes' ],
			]
		);

		$this->end_controls_section();

		// ==========================================
		// SECTION CONTENU : DIAPORAMA & AUTOPLAY
		// ==========================================
		$this->start_controls_section(
			'section_autoplay',
			[ 'label' => esc_html__( 'Options de Lecture (Autoplay)', 'tools-adapter' ) ]
		);

		$this->add_control(
			'autoplay',
			[
				'label'        => esc_html__( 'Défilement automatique (Autoplay)', 'tools-adapter' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Oui', 'tools-adapter' ),
				'label_off'    => esc_html__( 'Non', 'tools-adapter' ),
				'return_value' => 'yes',
				'default'      => 'no',
			]
		);

		$this->add_control(
			'autoplay_speed',
			[
				'label'     => esc_html__( 'Vitesse de défilement (ms)', 'tools-adapter' ),
				'type'      => Controls_Manager::NUMBER,
				'min'       => 1500,
				'max'       => 12000,
				'step'      => 500,
				'default'   => 4500,
				'condition' => [ 'autoplay' => 'yes' ],
			]
		);

		$this->add_control(
			'pause_on_hover',
			[
				'label'        => esc_html__( 'Pause au survol', 'tools-adapter' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Oui', 'tools-adapter' ),
				'label_off'    => esc_html__( 'Non', 'tools-adapter' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => [ 'autoplay' => 'yes' ],
			]
		);

		$this->end_controls_section();

		// ==========================================
		// SECTION STYLE : VISUALISEUR PRINCIPAL
		// ==========================================
		$this->start_controls_section(
			'section_style_viewer',
			[
				'label' => esc_html__( 'Visualiseur Principal', 'tools-adapter' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'viewer_sizing_mode',
			[
				'label'       => esc_html__( 'Mode de dimensionnement', 'tools-adapter' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'fixed',
				'options'     => [
					'fixed' => esc_html__( 'Hauteur fixe personnalisée (px / vh)', 'tools-adapter' ),
					'ratio' => esc_html__( 'Ratio d\'aspect (16:9, 4:3, 3:2, etc.)', 'tools-adapter' ),
					'auto'  => esc_html__( 'Hauteur automatique (S\'adapte à l\'image)', 'tools-adapter' ),
				],
				'description' => esc_html__( 'Le mode automatique ou ratio permet à l\'image de s\'adapter sans créer de bandes noires en haut ou en bas.', 'tools-adapter' ),
			]
		);

		$this->add_responsive_control(
			'viewer_aspect_ratio',
			[
				'label'     => esc_html__( 'Ratio d\'aspect', 'tools-adapter' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => '16/9',
				'options'   => [
					'16/9' => '16:9 (Panoramique paysage)',
					'4/3'  => '4:3 (Photo classique)',
					'3/2'  => '3:2 (Format Reflex 3:2)',
					'1/1'  => '1:1 (Carré)',
					'21/9' => '21:9 (Cinéma Ultra-large)',
					'9/16' => '9:16 (Portrait / Mobile)',
				],
				'condition' => [ 'viewer_sizing_mode' => 'ratio' ],
				'selectors' => [
					'{{WRAPPER}} .ta-ps-main-viewer' => 'aspect-ratio: {{VALUE}} !important; height: auto !important;',
				],
			]
		);

		$this->add_responsive_control(
			'viewer_height',
			[
				'label'      => esc_html__( 'Hauteur du cadre', 'tools-adapter' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'vh', '%' ],
				'range'      => [
					'px' => [ 'min' => 150, 'max' => 1000, 'step' => 10 ],
					'vh' => [ 'min' => 20, 'max' => 100 ],
				],
				'condition'  => [ 'viewer_sizing_mode' => 'fixed' ],
				'selectors'  => [
					'{{WRAPPER}} .ta-ps-main-viewer' => 'height: {{SIZE}}{{UNIT}} !important;',
				],
			]
		);

		$this->add_responsive_control(
			'viewer_max_height',
			[
				'label'      => esc_html__( 'Hauteur maximale', 'tools-adapter' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'vh' ],
				'range'      => [
					'px' => [ 'min' => 200, 'max' => 1200, 'step' => 10 ],
				],
				'condition'  => [ 'viewer_sizing_mode' => 'auto' ],
				'selectors'  => [
					'{{WRAPPER}} .ta-ps-main-viewer' => 'max-height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ta-ps-main-img'    => 'max-height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'image_fit',
			[
				'label'       => esc_html__( 'Ajustement de l\'image (Object Fit)', 'tools-adapter' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'cover',
				'options'     => [
					'cover'      => esc_html__( 'Couverture (Cover — Remplit tout le cadre)', 'tools-adapter' ),
					'contain'    => esc_html__( 'Contenir (Contain — Image entière sans rognage)', 'tools-adapter' ),
					'auto'       => esc_html__( 'Taille naturelle (Auto)', 'tools-adapter' ),
					'fill'       => esc_html__( 'Étirer (Fill — Remplir)', 'tools-adapter' ),
					'scale-down' => esc_html__( 'Réduire si nécessaire (Scale Down)', 'tools-adapter' ),
				],
				'description' => esc_html__( '« Couverture » garantit que l\'image remplit 100% du cadre. « Contenir » affiche toute la photo.', 'tools-adapter' ),
				'selectors'   => [
					'{{WRAPPER}} .ta-ps-main-viewer .ta-ps-main-img' => 'object-fit: {{VALUE}} !important;',
				],
			]
		);

		$this->add_control(
			'image_position',
			[
				'label'     => esc_html__( 'Position de l\'image (Object Position)', 'tools-adapter' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'center center',
				'options'   => [
					'center center' => esc_html__( 'Centre', 'tools-adapter' ),
					'top center'    => esc_html__( 'Haut Centré', 'tools-adapter' ),
					'bottom center' => esc_html__( 'Bas Centré', 'tools-adapter' ),
					'center left'   => esc_html__( 'Gauche', 'tools-adapter' ),
					'center right'  => esc_html__( 'Droite', 'tools-adapter' ),
					'top left'      => esc_html__( 'Haut Gauche', 'tools-adapter' ),
					'top right'     => esc_html__( 'Haut Droite', 'tools-adapter' ),
					'bottom left'   => esc_html__( 'Bas Gauche', 'tools-adapter' ),
					'bottom right'  => esc_html__( 'Bas Droite', 'tools-adapter' ),
				],
				'selectors' => [
					'{{WRAPPER}} .ta-ps-main-viewer .ta-ps-main-img' => 'object-position: {{VALUE}} !important;',
				],
			]
		);

		$this->add_control(
			'viewer_bg_color',
			[
				'label'     => esc_html__( 'Couleur d\'arrière-plan du cadre', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#0d1a10',
				'selectors' => [
					'{{WRAPPER}} .ta-ps-main-viewer' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'overlay_type',
			[
				'label'     => esc_html__( 'Voile de contraste inférieur', 'tools-adapter' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'gradient',
				'options'   => [
					'gradient' => esc_html__( 'Dégradé sombre doux (Recommandé)', 'tools-adapter' ),
					'none'     => esc_html__( 'Aucun voile (Transparent)', 'tools-adapter' ),
					'custom'   => esc_html__( 'Couleur personnalisée', 'tools-adapter' ),
				],
			]
		);

		$this->add_control(
			'overlay_custom_color',
			[
				'label'     => esc_html__( 'Couleur du voile personnalisé', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => [ 'overlay_type' => 'custom' ],
				'selectors' => [
					'{{WRAPPER}} .ta-ps-viewer-overlay' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'viewer_border_radius',
			[
				'label'      => esc_html__( 'Rayon des coins', 'tools-adapter' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .ta-ps-main-viewer' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator'  => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'viewer_shadow',
				'selector' => '{{WRAPPER}} .ta-ps-main-viewer',
			]
		);

		$this->end_controls_section();

		// ==========================================
		// SECTION STYLE : BADGE & CONTRÔLES INCRUSTÉS
		// ==========================================
		$this->start_controls_section(
			'section_style_controls',
			[
				'label' => esc_html__( 'Badge & Boutons Incrustés', 'tools-adapter' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'accent_color',
			[
				'label'     => esc_html__( 'Couleur d\'Accentuation', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#7bc81d',
				'selectors' => [
					'{{WRAPPER}} .ta-project-showcase' => '--ta-ps-accent: {{VALUE}};',
				],
				'description' => esc_html__( 'Appliquée aux : survol boutons, bordure miniature active, icône pied de page, survol bouton diaporama.', 'tools-adapter' ),
			]
		);

		$this->add_control(
			'accent_text_color',
			[
				'label'     => esc_html__( 'Texte sur couleur d\'accentuation', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#0d1a10',
				'selectors' => [
					'{{WRAPPER}} .ta-project-showcase' => '--ta-ps-accent-text: {{VALUE}};',
				],
				'description' => esc_html__( 'Couleur du texte/icône sur fond accentuation (au survol des boutons).', 'tools-adapter' ),
			]
		);

		$this->add_control(
			'counter_badge_heading',
			[
				'label'     => esc_html__( 'Badge Compteur', 'tools-adapter' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'counter_typography',
				'selector' => '{{WRAPPER}} .ta-ps-counter-badge',
			]
		);

		$this->add_control(
			'counter_bg',
			[
				'label'     => esc_html__( 'Arrière-plan du compteur', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(13, 26, 16, 0.85)',
				'selectors' => [
					'{{WRAPPER}} .ta-ps-counter-badge' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'counter_text_color',
			[
				'label'     => esc_html__( 'Couleur du texte compteur', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .ta-ps-counter-badge' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'action_buttons_heading',
			[
				'label'     => esc_html__( 'Boutons Flèches & Plein Écran', 'tools-adapter' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'btn_size',
			[
				'label'      => esc_html__( 'Diamètre des boutons', 'tools-adapter' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [ 'min' => 28, 'max' => 60 ],
				],
				'selectors'  => [
					'{{WRAPPER}} .ta-ps-btn-action' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'btn_bg',
			[
				'label'     => esc_html__( 'Arrière-plan bouton (normal)', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(13, 26, 16, 0.85)',
				'selectors' => [
					'{{WRAPPER}} .ta-ps-btn-action' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'btn_color',
			[
				'label'     => esc_html__( 'Couleur icône (normal)', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .ta-ps-btn-action' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		// ==========================================
		// SECTION STYLE : MINIATURES
		// ==========================================
		$this->start_controls_section(
			'section_style_thumbnails',
			[
				'label'     => esc_html__( 'Bandeau de Miniatures', 'tools-adapter' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [ 'show_thumbs' => 'yes' ],
			]
		);

		$this->add_responsive_control(
			'thumb_width',
			[
				'label'      => esc_html__( 'Largeur des miniatures', 'tools-adapter' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [ 'min' => 40, 'max' => 180 ],
				],
				'selectors'  => [
					'{{WRAPPER}} .ta-ps-thumb-item' => 'flex: 0 0 {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'thumb_height',
			[
				'label'      => esc_html__( 'Hauteur des miniatures', 'tools-adapter' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [ 'min' => 30, 'max' => 140 ],
				],
				'selectors'  => [
					'{{WRAPPER}} .ta-ps-thumb-item' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'thumb_border_radius',
			[
				'label'      => esc_html__( 'Rayon des coins', 'tools-adapter' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .ta-ps-thumb-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'thumb_inactive_opacity',
			[
				'label'     => esc_html__( 'Opacité miniature inactive', 'tools-adapter' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [ 'min' => 0.2, 'max' => 1, 'step' => 0.05 ],
				],
				'default'   => [ 'size' => 0.6 ],
				'selectors' => [
					'{{WRAPPER}} .ta-ps-thumb-item' => 'opacity: {{SIZE}};',
				],
			]
		);

		$this->end_controls_section();

		// ==========================================
		// SECTION STYLE : BARRE INFÉRIEURE
		// ==========================================
		$this->start_controls_section(
			'section_style_toolbar',
			[
				'label'     => esc_html__( 'Barre d\'Informations Inférieure', 'tools-adapter' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [ 'show_footer_toolbar' => 'yes' ],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'footer_typography',
				'selector' => '{{WRAPPER}} .ta-ps-footer-toolbar',
			]
		);

		$this->add_control(
			'footer_text_color',
			[
				'label'     => esc_html__( 'Couleur du texte d\'infos', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#64748b',
				'selectors' => [
					'{{WRAPPER}} .ta-project-showcase' => '--ta-ps-footer-text: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'diaporama_btn_color',
			[
				'label'     => esc_html__( 'Couleur du bouton Diaporama', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#0d1a10',
				'selectors' => [
					'{{WRAPPER}} .ta-project-showcase' => '--ta-ps-diaporama-btn-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		// ==========================================
		// SECTION STYLE : BARRE DE PROGRESSION
		// ==========================================
		$this->start_controls_section(
			'section_style_progress',
			[
				'label'     => esc_html__( 'Barre de Progression', 'tools-adapter' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [ 'show_progress_bar' => 'yes', 'show_thumbs' => 'yes' ],
			]
		);

		$this->add_control(
			'progress_fill_color',
			[
				'label'     => esc_html__( 'Couleur de la barre (remplissage)', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#7bc81d',
				'selectors' => [
					'{{WRAPPER}} .ta-project-showcase' => '--ta-ps-progress-fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'progress_bg_color',
			[
				'label'     => esc_html__( 'Couleur de fond de la barre', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#e2e8f0',
				'selectors' => [
					'{{WRAPPER}} .ta-project-showcase' => '--ta-ps-progress-bg: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'progress_height',
			[
				'label'      => esc_html__( 'Épaisseur de la barre', 'tools-adapter' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 'px' => [ 'min' => 2, 'max' => 12 ] ],
				'default'    => [ 'size' => 4, 'unit' => 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .ta-project-showcase' => '--ta-ps-progress-height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		// ==========================================
		// SECTION STYLE : MINIATURES (AVANCÉ)
		// ==========================================
		$this->start_controls_section(
			'section_style_thumbs_advanced',
			[
				'label'     => esc_html__( 'Miniatures — Couleurs & Fond', 'tools-adapter' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [ 'show_thumbs' => 'yes' ],
			]
		);

		$this->add_control(
			'thumbs_wrapper_bg',
			[
				'label'     => esc_html__( 'Fond du bandeau de miniatures', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .ta-project-showcase' => '--ta-ps-thumbs-bg: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'thumb_inactive_bg',
			[
				'label'     => esc_html__( 'Fond de la miniature inactive', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#e2e8f0',
				'selectors' => [
					'{{WRAPPER}} .ta-project-showcase' => '--ta-ps-thumb-inactive-bg: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'scrollbar_thumb_color',
			[
				'label'     => esc_html__( 'Couleur de la scrollbar (poignée)', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#7bc81d',
				'selectors' => [
					'{{WRAPPER}} .ta-project-showcase' => '--ta-ps-scrollbar-thumb: {{VALUE}};',
				],
				'separator' => 'before',
			]
		);

		$this->add_control(
			'scrollbar_track_color',
			[
				'label'     => esc_html__( 'Couleur du fond de la scrollbar', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#f1f5f0',
				'selectors' => [
					'{{WRAPPER}} .ta-project-showcase' => '--ta-ps-scrollbar-track: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		// ==========================================
		// SECTION STYLE : LIGHTBOX — FOND & COMPTEUR
		// ==========================================
		$this->start_controls_section(
			'section_style_lightbox_bg',
			[
				'label' => esc_html__( 'Lightbox — Fond & Compteur', 'tools-adapter' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'lb_bg_color',
			[
				'label'     => esc_html__( 'Couleur de fond de la lightbox', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(13, 26, 16, 0.96)',
				'selectors' => [
					'{{WRAPPER}} .ta-ps-lightbox-modal' => '--ta-ps-lb-bg: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'lb_counter_bg',
			[
				'label'     => esc_html__( 'Fond du badge compteur', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(255, 255, 255, 0.1)',
				'selectors' => [
					'{{WRAPPER}} .ta-ps-lightbox-modal' => '--ta-ps-lb-counter-bg: {{VALUE}};',
				],
				'separator' => 'before',
			]
		);

		$this->add_control(
			'lb_counter_color',
			[
				'label'     => esc_html__( 'Couleur texte du compteur', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(255, 255, 255, 0.85)',
				'selectors' => [
					'{{WRAPPER}} .ta-ps-lightbox-modal' => '--ta-ps-lb-counter-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'lb_footer_color',
			[
				'label'     => esc_html__( 'Couleur du texte pied de page (lightbox)', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(255, 255, 255, 0.7)',
				'selectors' => [
					'{{WRAPPER}} .ta-ps-lightbox-modal' => '--ta-ps-lb-footer-color: {{VALUE}};',
				],
				'separator' => 'before',
			]
		);

		$this->end_controls_section();

		// ==========================================
		// SECTION STYLE : LIGHTBOX — BOUTONS
		// ==========================================
		$this->start_controls_section(
			'section_style_lightbox_btns',
			[
				'label' => esc_html__( 'Lightbox — Boutons Navigation', 'tools-adapter' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'lb_close_bg',
			[
				'label'     => esc_html__( 'Fond bouton Fermer (normal)', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(255, 255, 255, 0.1)',
				'selectors' => [
					'{{WRAPPER}} .ta-ps-lightbox-modal' => '--ta-ps-lb-close-bg: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'lb_close_color',
			[
				'label'     => esc_html__( 'Icône bouton Fermer (normal)', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .ta-ps-lightbox-modal' => '--ta-ps-lb-close-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'lb_close_bg_hover',
			[
				'label'     => esc_html__( 'Fond bouton Fermer (survol)', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#7bc81d',
				'selectors' => [
					'{{WRAPPER}} .ta-ps-lightbox-modal' => '--ta-ps-lb-close-bg-hover: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'lb_close_color_hover',
			[
				'label'     => esc_html__( 'Icône bouton Fermer (survol)', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#0d1a10',
				'selectors' => [
					'{{WRAPPER}} .ta-ps-lightbox-modal' => '--ta-ps-lb-close-color-hover: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'lb_nav_bg',
			[
				'label'     => esc_html__( 'Fond boutons Précédent / Suivant (normal)', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(255, 255, 255, 0.12)',
				'selectors' => [
					'{{WRAPPER}} .ta-ps-lightbox-modal' => '--ta-ps-lb-nav-bg: {{VALUE}};',
				],
				'separator' => 'before',
			]
		);

		$this->add_control(
			'lb_nav_color',
			[
				'label'     => esc_html__( 'Icônes boutons Précédent / Suivant (normal)', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .ta-ps-lightbox-modal' => '--ta-ps-lb-nav-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'lb_nav_bg_hover',
			[
				'label'     => esc_html__( 'Fond boutons Précédent / Suivant (survol)', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#7bc81d',
				'selectors' => [
					'{{WRAPPER}} .ta-ps-lightbox-modal' => '--ta-ps-lb-nav-bg-hover: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'lb_nav_color_hover',
			[
				'label'     => esc_html__( 'Icônes boutons Précédent / Suivant (survol)', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#0d1a10',
				'selectors' => [
					'{{WRAPPER}} .ta-ps-lightbox-modal' => '--ta-ps-lb-nav-color-hover: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		// Prepare photos list
		$photos = [];
		if ( ! empty( $settings['gallery'] ) && is_array( $settings['gallery'] ) ) {
			foreach ( $settings['gallery'] as $img ) {
				if ( ! empty( $img['url'] ) ) {
					$photos[] = [
						'url' => $img['url'],
						'alt' => ! empty( $img['id'] ) ? get_post_meta( $img['id'], '_wp_attachment_image_alt', true ) : '',
					];
				}
			}
		}

		if ( empty( $photos ) ) {
			$photos = $this->get_default_photos();
		}

		$total_photos = count( $photos );
		$first_photo  = $photos[0];

		$counter_pattern = ! empty( $settings['counter_format'] ) ? $settings['counter_format'] : 'Photo {current} / {total}';
		$initial_counter = str_replace( [ '{current}', '{total}' ], [ '1', $total_photos ], $counter_pattern );

		$footer_count_text = ! empty( $settings['footer_count_text'] )
			? str_replace( '{count}', $total_photos, $settings['footer_count_text'] )
			: sprintf( esc_html__( '%d photographies haute définition', 'tools-adapter' ), $total_photos );

		$zoom_class  = 'yes' === $settings['zoom_effect'] ? 'ta-ps-zoom-enabled' : '';
		$sizing_mode = ! empty( $settings['viewer_sizing_mode'] ) ? $settings['viewer_sizing_mode'] : 'fixed';

		$viewer_classes = [ 'ta-ps-main-viewer' ];
		if ( $zoom_class ) {
			$viewer_classes[] = $zoom_class;
		}
		if ( 'auto' === $sizing_mode ) {
			$viewer_classes[] = 'ta-ps-mode-auto';
		} elseif ( 'ratio' === $sizing_mode ) {
			$viewer_classes[] = 'ta-ps-mode-ratio';
		}

		$show_overlay = 'none' !== ( ! empty( $settings['overlay_type'] ) ? $settings['overlay_type'] : 'gradient' );
		?>
		<div class="ta-project-showcase-container">
			<div class="ta-project-showcase"
			     data-autoplay="<?php echo esc_attr( $settings['autoplay'] ); ?>"
			     data-autoplay-speed="<?php echo esc_attr( $settings['autoplay_speed'] ); ?>"
			     data-pause-hover="<?php echo esc_attr( $settings['pause_on_hover'] ); ?>"
			     data-counter-pattern="<?php echo esc_attr( $counter_pattern ); ?>">

				<!-- 1. Main Stage / Large Viewer -->
				<div class="<?php echo esc_attr( implode( ' ', $viewer_classes ) ); ?>" title="<?php esc_attr_e( 'Cliquer pour agrandir en plein écran', 'tools-adapter' ); ?>">
					<div class="ta-ps-main-image-wrap">
						<img src="<?php echo esc_url( $first_photo['url'] ); ?>"
						     alt="<?php echo esc_attr( $first_photo['alt'] ); ?>"
						     class="ta-ps-main-img">
						<?php if ( $show_overlay ) : ?>
							<div class="ta-ps-viewer-overlay"></div>
						<?php endif; ?>

						<div class="ta-ps-viewer-controls">
							<?php if ( 'yes' === $settings['show_counter'] ) : ?>
								<div class="ta-ps-counter-badge"><?php echo esc_html( $initial_counter ); ?></div>
							<?php else : ?>
								<div></div>
							<?php endif; ?>

							<div class="ta-ps-action-buttons">
								<?php if ( 'yes' === $settings['show_arrows'] ) : ?>
									<button type="button" class="ta-ps-btn-action ta-ps-btn-prev" aria-label="<?php esc_attr_e( 'Photo précédente', 'tools-adapter' ); ?>">
										<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"><polyline points="15 18 9 12 15 6"></polyline></svg>
									</button>
									<button type="button" class="ta-ps-btn-action ta-ps-btn-next" aria-label="<?php esc_attr_e( 'Photo suivante', 'tools-adapter' ); ?>">
										<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"><polyline points="9 18 15 12 9 6"></polyline></svg>
									</button>
								<?php endif; ?>

								<?php if ( 'yes' === $settings['show_fullscreen_btn'] ) : ?>
									<button type="button" class="ta-ps-btn-action ta-ps-btn-fullscreen" aria-label="<?php esc_attr_e( 'Plein écran', 'tools-adapter' ); ?>">
										<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"><polyline points="15 3 21 3 21 9"></polyline><polyline points="9 21 3 21 3 15"></polyline><line x1="21" y1="3" x2="14" y2="10"></line><line x1="3" y1="21" x2="10" y2="14"></line></svg>
									</button>
								<?php endif; ?>
							</div>
						</div>
					</div>
				</div>

				<!-- 2. Thumbnails Strip -->
				<?php if ( 'yes' === $settings['show_thumbs'] ) : ?>
					<div class="ta-ps-thumbs-wrapper">
						<div class="ta-ps-thumbs-strip">
							<?php foreach ( $photos as $index => $photo ) : ?>
								<div class="ta-ps-thumb-item <?php echo 0 === $index ? 'is-active' : ''; ?>"
								     data-index="<?php echo esc_attr( $index ); ?>"
								     data-src="<?php echo esc_url( $photo['url'] ); ?>">
									<img src="<?php echo esc_url( $photo['url'] ); ?>"
									     alt="<?php echo esc_attr( $photo['alt'] ); ?>"
									     class="ta-ps-thumb-img"
									     loading="lazy">
								</div>
							<?php endforeach; ?>
						</div>

						<?php if ( 'yes' === $settings['show_progress_bar'] ) : ?>
							<div class="ta-ps-progress-bar-wrap">
								<div class="ta-ps-progress-bar-fill" style="width: <?php echo esc_attr( ( 1 / $total_photos ) * 100 ); ?>%;"></div>
							</div>
						<?php endif; ?>
					</div>
				<?php endif; ?>

				<!-- 3. Bottom Toolbar / Hint -->
				<?php if ( 'yes' === $settings['show_footer_toolbar'] ) : ?>
					<div class="ta-ps-footer-toolbar">
						<?php if ( 'yes' === $settings['show_footer_count'] ) : ?>
							<div class="ta-ps-footer-left">
								<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path><circle cx="12" cy="13" r="4"></circle></svg>
								<span><?php echo esc_html( $footer_count_text ); ?></span>
							</div>
						<?php else : ?>
							<div></div>
						<?php endif; ?>

						<?php if ( 'yes' === $settings['show_diaporama_btn'] ) : ?>
							<div class="ta-ps-footer-right" role="button" tabindex="0">
								<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
								<span><?php echo esc_html( $settings['diaporama_btn_text'] ); ?></span>
							</div>
						<?php endif; ?>
					</div>
				<?php endif; ?>

				<!-- 4. Lightbox Fullscreen Modal -->
				<div class="ta-ps-lightbox-modal" role="dialog" aria-modal="true">
					<div class="ta-ps-lb-header">
						<span class="ta-ps-lb-counter"><?php echo esc_html( $initial_counter ); ?></span>
						<button type="button" class="ta-ps-lb-close-btn" aria-label="<?php esc_attr_e( 'Fermer', 'tools-adapter' ); ?>">✕</button>
					</div>

					<div class="ta-ps-lb-body">
						<button type="button" class="ta-ps-lb-nav-btn ta-ps-lb-prev" aria-label="<?php esc_attr_e( 'Précédent', 'tools-adapter' ); ?>">
							<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"><polyline points="15 18 9 12 15 6"></polyline></svg>
						</button>

						<div class="ta-ps-lb-image-container">
							<img src="<?php echo esc_url( $first_photo['url'] ); ?>"
							     alt="<?php echo esc_attr( $first_photo['alt'] ); ?>"
							     class="ta-ps-lb-img">
						</div>

						<button type="button" class="ta-ps-lb-nav-btn ta-ps-lb-next" aria-label="<?php esc_attr_e( 'Suivant', 'tools-adapter' ); ?>">
							<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"><polyline points="9 18 15 12 9 6"></polyline></svg>
						</button>
					</div>

					<div class="ta-ps-lb-footer">
						<span><?php esc_html_e( 'Utilisez les flèches du clavier ← → ou glissez pour naviguer, Échap pour fermer.', 'tools-adapter' ); ?></span>
					</div>
				</div>

			</div>
		</div>
		<?php
	}
}
