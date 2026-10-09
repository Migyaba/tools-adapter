<?php
namespace ToolsAdapter\Widgets;

use Elementor\Controls_Manager;
use Elementor\Utils;
use ToolsAdapter\Base_Widget;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Css_Filter;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Widget: Images superposées — grande image, médaillon secondaire qui la
 * chevauche et pastille chiffrée (ex. « 27 ans d'excellence »).
 */
class Image_Stack extends Base_Widget {

	public function get_name() {
		return 'tools-adapter-image-stack';
	}

	public function get_title() {
		return esc_html__( 'Images superposées', 'tools-adapter' );
	}

	public function get_icon() {
		return 'eicon-image-before-after';
	}

	public function get_categories() {
		return [ 'tools-adapter' ];
	}

	public function get_keywords() {
		return [ 'image', 'superposée', 'composition', 'à propos', 'badge', 'années', 'expérience', 'médaillon' ];
	}

	public function get_style_depends() {
		return [ 'tools-adapter-image-stack' ];
	}

	public function get_script_depends() {
		return [ 'tools-adapter-stats' ];
	}

	/**
	 * Aspect-ratio options shared by both images.
	 *
	 * @return array<string,string>
	 */
	private function ratio_options() {
		return [
			''     => esc_html__( 'Original', 'tools-adapter' ),
			'4/5'  => '4:5 — ' . esc_html__( 'portrait', 'tools-adapter' ),
			'3/4'  => '3:4',
			'2/3'  => '2:3',
			'1/1'  => '1:1 — ' . esc_html__( 'carré', 'tools-adapter' ),
			'4/3'  => '4:3',
			'3/2'  => '3:2',
			'16/9' => '16:9 — ' . esc_html__( 'paysage', 'tools-adapter' ),
		];
	}

	/**
	 * Corner options for the secondary image and the badge.
	 *
	 * @return array<string,string>
	 */
	private function corner_options() {
		return [
			'top-left'     => esc_html__( 'Haut gauche', 'tools-adapter' ),
			'top-right'    => esc_html__( 'Haut droite', 'tools-adapter' ),
			'bottom-left'  => esc_html__( 'Bas gauche', 'tools-adapter' ),
			'bottom-right' => esc_html__( 'Bas droite', 'tools-adapter' ),
		];
	}

	protected function register_controls() {
		// ── Contenu : images ─────────────────────────────────────────────
		$this->start_controls_section( 'section_images', [ 'label' => esc_html__( 'Images', 'tools-adapter' ) ] );

		$this->add_control( 'main_image', [ 'label' => esc_html__( 'Image principale', 'tools-adapter' ), 'type' => Controls_Manager::MEDIA, 'default' => [ 'url' => Utils::get_placeholder_image_src() ] ] );
		$this->add_control( 'main_link', [ 'label' => esc_html__( 'Lien de l\'image principale', 'tools-adapter' ), 'type' => Controls_Manager::URL ] );

		$this->add_control( 'show_secondary', [ 'label' => esc_html__( 'Image secondaire', 'tools-adapter' ), 'type' => Controls_Manager::SWITCHER, 'return_value' => 'yes', 'default' => 'yes', 'separator' => 'before' ] );
		$this->add_control( 'secondary_image', [ 'label' => esc_html__( 'Image secondaire', 'tools-adapter' ), 'type' => Controls_Manager::MEDIA, 'default' => [ 'url' => Utils::get_placeholder_image_src() ], 'condition' => [ 'show_secondary' => 'yes' ] ] );
		$this->add_control( 'secondary_link', [ 'label' => esc_html__( 'Lien de l\'image secondaire', 'tools-adapter' ), 'type' => Controls_Manager::URL, 'condition' => [ 'show_secondary' => 'yes' ] ] );
		$this->add_control( 'secondary_position', [ 'label' => esc_html__( 'Position', 'tools-adapter' ), 'type' => Controls_Manager::SELECT, 'default' => 'bottom-right', 'options' => $this->corner_options(), 'condition' => [ 'show_secondary' => 'yes' ] ] );

		$this->add_control(
			'image_size',
			[
				'label'     => esc_html__( 'Taille des fichiers image', 'tools-adapter' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'large',
				'separator' => 'before',
				'options'   => [
					'medium'       => esc_html__( 'Moyenne', 'tools-adapter' ),
					'medium_large' => esc_html__( 'Moyenne-grande', 'tools-adapter' ),
					'large'        => esc_html__( 'Grande', 'tools-adapter' ),
					'full'         => esc_html__( 'Originale', 'tools-adapter' ),
				],
			]
		);

		$this->end_controls_section();

		// ── Contenu : pastille ───────────────────────────────────────────
		$this->start_controls_section( 'section_badge', [ 'label' => esc_html__( 'Pastille', 'tools-adapter' ) ] );

		$this->add_control( 'show_badge', [ 'label' => esc_html__( 'Afficher la pastille', 'tools-adapter' ), 'type' => Controls_Manager::SWITCHER, 'return_value' => 'yes', 'default' => 'yes' ] );
		$this->add_control( 'badge_prefix', [ 'label' => esc_html__( 'Avant le nombre', 'tools-adapter' ), 'type' => Controls_Manager::TEXT, 'placeholder' => '+', 'condition' => [ 'show_badge' => 'yes' ] ] );
		$this->add_control( 'badge_number', [ 'label' => esc_html__( 'Nombre', 'tools-adapter' ), 'type' => Controls_Manager::TEXT, 'default' => '27', 'condition' => [ 'show_badge' => 'yes' ] ] );
		$this->add_control( 'badge_suffix', [ 'label' => esc_html__( 'Après le nombre', 'tools-adapter' ), 'type' => Controls_Manager::TEXT, 'placeholder' => '+', 'condition' => [ 'show_badge' => 'yes' ] ] );
		$this->add_control( 'badge_text', [ 'label' => esc_html__( 'Texte', 'tools-adapter' ), 'type' => Controls_Manager::TEXTAREA, 'rows' => 2, 'default' => "ans\nd'excellence", 'description' => esc_html__( 'Un retour à la ligne = une nouvelle ligne.', 'tools-adapter' ), 'condition' => [ 'show_badge' => 'yes' ] ] );
		$this->add_control( 'badge_position', [ 'label' => esc_html__( 'Position sur l\'image principale', 'tools-adapter' ), 'type' => Controls_Manager::SELECT, 'default' => 'top-right', 'options' => $this->corner_options(), 'condition' => [ 'show_badge' => 'yes' ] ] );
		$this->add_control( 'count_up', [ 'label' => esc_html__( 'Animer le nombre', 'tools-adapter' ), 'type' => Controls_Manager::SWITCHER, 'return_value' => 'yes', 'default' => 'yes', 'description' => esc_html__( 'Compte de 0 jusqu\'au nombre quand la pastille apparaît (nombre entier ou décimal uniquement).', 'tools-adapter' ), 'condition' => [ 'show_badge' => 'yes' ] ] );
		$this->add_control( 'count_duration', [ 'label' => esc_html__( 'Durée (ms)', 'tools-adapter' ), 'type' => Controls_Manager::NUMBER, 'min' => 200, 'max' => 10000, 'step' => 100, 'default' => 2000, 'condition' => [ 'show_badge' => 'yes', 'count_up' => 'yes' ] ] );
		$this->add_control(
			'badge_animation',
			[
				'label'        => esc_html__( 'Animation continue', 'tools-adapter' ),
				'type'         => Controls_Manager::SELECT,
				'default'      => 'none',
				'options'      => [
					'none'  => esc_html__( 'Aucune', 'tools-adapter' ),
					'float' => esc_html__( 'Flottement', 'tools-adapter' ),
					'pulse' => esc_html__( 'Pulsation du halo', 'tools-adapter' ),
					'spin'  => esc_html__( 'Anneau tournant', 'tools-adapter' ),
				],
				'prefix_class' => 'ta-is--badge-anim-',
				'condition'    => [ 'show_badge' => 'yes' ],
			]
		);

		$this->end_controls_section();

		// ── Style : composition ──────────────────────────────────────────
		$this->start_controls_section( 'section_style_layout', [ 'label' => esc_html__( 'Composition', 'tools-adapter' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_responsive_control( 'max_width', [ 'label' => esc_html__( 'Largeur max. du bloc', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'size_units' => [ 'px', '%' ], 'range' => [ 'px' => [ 'min' => 200, 'max' => 1200 ], '%' => [ 'min' => 20, 'max' => 100 ] ], 'selectors' => [ '{{WRAPPER}} .ta-is' => 'max-width: {{SIZE}}{{UNIT}};' ] ] );
		$this->add_responsive_control(
			'block_align',
			[
				'label'                => esc_html__( 'Alignement du bloc', 'tools-adapter' ),
				'type'                 => Controls_Manager::CHOOSE,
				'options'              => [
					'left'   => [ 'title' => esc_html__( 'Gauche', 'tools-adapter' ), 'icon' => 'eicon-h-align-left' ],
					'center' => [ 'title' => esc_html__( 'Centre', 'tools-adapter' ), 'icon' => 'eicon-h-align-center' ],
					'right'  => [ 'title' => esc_html__( 'Droite', 'tools-adapter' ), 'icon' => 'eicon-h-align-right' ],
				],
				'selectors_dictionary' => [
					'left'   => 'margin-left: 0; margin-right: auto;',
					'center' => 'margin-left: auto; margin-right: auto;',
					'right'  => 'margin-left: auto; margin-right: 0;',
				],
				'selectors'            => [ '{{WRAPPER}} .ta-is' => '{{VALUE}}' ],
			]
		);
		$this->add_responsive_control( 'main_width', [ 'label' => esc_html__( 'Largeur de l\'image principale (%)', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'range' => [ '%' => [ 'min' => 40, 'max' => 100 ] ], 'size_units' => [ '%' ], 'default' => [ 'size' => 77, 'unit' => '%' ], 'selectors' => [ '{{WRAPPER}} .ta-is__main' => 'width: {{SIZE}}%;' ] ] );
		$this->add_responsive_control( 'sec_width', [ 'label' => esc_html__( 'Largeur de l\'image secondaire (%)', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'range' => [ '%' => [ 'min' => 15, 'max' => 90 ] ], 'size_units' => [ '%' ], 'default' => [ 'size' => 50, 'unit' => '%' ], 'selectors' => [ '{{WRAPPER}} .ta-is__secondary' => 'width: {{SIZE}}%;' ], 'condition' => [ 'show_secondary' => 'yes' ] ] );
		$this->add_responsive_control( 'sec_overflow', [ 'label' => esc_html__( 'Débordement vertical de l\'image secondaire', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'size_units' => [ 'px', '%' ], 'range' => [ 'px' => [ 'min' => -100, 'max' => 300 ], '%' => [ 'min' => -20, 'max' => 50 ] ], 'default' => [ 'size' => 48, 'unit' => 'px' ], 'description' => esc_html__( 'De combien l\'image secondaire dépasse sous (ou au-dessus de) l\'image principale.', 'tools-adapter' ), 'selectors' => [ '{{WRAPPER}} .ta-is' => '--ta-is-overflow: {{SIZE}}{{UNIT}};' ], 'condition' => [ 'show_secondary' => 'yes' ] ] );
		$this->add_responsive_control( 'sec_offset_x', [ 'label' => esc_html__( 'Décalage horizontal de l\'image secondaire', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'size_units' => [ 'px', '%' ], 'range' => [ 'px' => [ 'min' => -150, 'max' => 150 ], '%' => [ 'min' => -30, 'max' => 30 ] ], 'description' => esc_html__( 'Positif = vers l\'extérieur du bloc.', 'tools-adapter' ), 'selectors' => [ '{{WRAPPER}} .ta-is' => '--ta-is-sec-x: {{SIZE}}{{UNIT}};' ], 'condition' => [ 'show_secondary' => 'yes' ] ] );

		$this->add_control( 'mobile_heading', [ 'label' => esc_html__( 'Mobile', 'tools-adapter' ), 'type' => Controls_Manager::HEADING, 'separator' => 'before' ] );
		$this->add_control( 'hide_secondary_mobile', [ 'label' => esc_html__( 'Masquer l\'image secondaire sur mobile', 'tools-adapter' ), 'type' => Controls_Manager::SWITCHER, 'return_value' => 'yes', 'default' => '', 'prefix_class' => 'ta-is--hide-sec-mobile-' ] );

		$this->end_controls_section();

		// ── Style : image principale ─────────────────────────────────────
		$this->start_controls_section( 'section_style_main', [ 'label' => esc_html__( 'Image principale', 'tools-adapter' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_responsive_control( 'main_ratio', [ 'label' => esc_html__( 'Format', 'tools-adapter' ), 'type' => Controls_Manager::SELECT, 'default' => '4/5', 'options' => $this->ratio_options(), 'selectors' => [ '{{WRAPPER}} .ta-is__main .ta-is__img' => 'aspect-ratio: {{VALUE}};' ] ] );
		$this->add_responsive_control( 'main_position', [ 'label' => esc_html__( 'Cadrage', 'tools-adapter' ), 'type' => Controls_Manager::SELECT, 'default' => '', 'options' => [ '' => esc_html__( 'Centre', 'tools-adapter' ), 'center top' => esc_html__( 'Haut', 'tools-adapter' ), 'center bottom' => esc_html__( 'Bas', 'tools-adapter' ), 'left center' => esc_html__( 'Gauche', 'tools-adapter' ), 'right center' => esc_html__( 'Droite', 'tools-adapter' ) ], 'selectors' => [ '{{WRAPPER}} .ta-is__main .ta-is__img' => 'object-position: {{VALUE}};' ] ] );
		$this->add_responsive_control( 'main_radius', [ 'label' => esc_html__( 'Arrondi', 'tools-adapter' ), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => [ 'px', '%' ], 'default' => [ 'top' => 28, 'right' => 28, 'bottom' => 28, 'left' => 28, 'unit' => 'px', 'isLinked' => true ], 'selectors' => [ '{{WRAPPER}} .ta-is__main .ta-is__frame' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ] ] );
		$this->add_group_control( Group_Control_Border::get_type(), [ 'name' => 'main_border', 'selector' => '{{WRAPPER}} .ta-is__main .ta-is__frame' ] );
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'           => 'main_shadow',
				'selector'       => '{{WRAPPER}} .ta-is__main .ta-is__frame',
				'fields_options' => [
					'box_shadow_type' => [ 'default' => 'yes' ],
					'box_shadow'      => [ 'default' => [ 'horizontal' => 0, 'vertical' => 20, 'blur' => 45, 'spread' => -12, 'color' => 'rgba(15,30,55,0.30)' ] ],
				],
			]
		);
		$this->add_group_control( Group_Control_Css_Filter::get_type(), [ 'name' => 'main_filters', 'selector' => '{{WRAPPER}} .ta-is__main .ta-is__img' ] );
		$this->add_control( 'main_hover_zoom', [ 'label' => esc_html__( 'Zoom au survol', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'range' => [ 'px' => [ 'min' => 1, 'max' => 1.3, 'step' => 0.01 ] ], 'description' => esc_html__( '1 = aucun zoom.', 'tools-adapter' ), 'selectors' => [ '{{WRAPPER}} .ta-is__main:hover .ta-is__img' => 'transform: scale({{SIZE}});' ] ] );

		$this->end_controls_section();

		// ── Style : image secondaire ─────────────────────────────────────
		$this->start_controls_section( 'section_style_secondary', [ 'label' => esc_html__( 'Image secondaire', 'tools-adapter' ), 'tab' => Controls_Manager::TAB_STYLE, 'condition' => [ 'show_secondary' => 'yes' ] ] );

		$this->add_responsive_control( 'sec_ratio', [ 'label' => esc_html__( 'Format', 'tools-adapter' ), 'type' => Controls_Manager::SELECT, 'default' => '4/3', 'options' => $this->ratio_options(), 'selectors' => [ '{{WRAPPER}} .ta-is__secondary .ta-is__img' => 'aspect-ratio: {{VALUE}};' ] ] );
		$this->add_responsive_control( 'sec_position_img', [ 'label' => esc_html__( 'Cadrage', 'tools-adapter' ), 'type' => Controls_Manager::SELECT, 'default' => '', 'options' => [ '' => esc_html__( 'Centre', 'tools-adapter' ), 'center top' => esc_html__( 'Haut', 'tools-adapter' ), 'center bottom' => esc_html__( 'Bas', 'tools-adapter' ), 'left center' => esc_html__( 'Gauche', 'tools-adapter' ), 'right center' => esc_html__( 'Droite', 'tools-adapter' ) ], 'selectors' => [ '{{WRAPPER}} .ta-is__secondary .ta-is__img' => 'object-position: {{VALUE}};' ] ] );
		$this->add_responsive_control( 'sec_radius', [ 'label' => esc_html__( 'Arrondi', 'tools-adapter' ), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => [ 'px', '%' ], 'default' => [ 'top' => 24, 'right' => 24, 'bottom' => 24, 'left' => 24, 'unit' => 'px', 'isLinked' => true ], 'selectors' => [ '{{WRAPPER}} .ta-is__secondary .ta-is__frame' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ] ] );
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'           => 'sec_border',
				'selector'       => '{{WRAPPER}} .ta-is__secondary .ta-is__frame',
				'fields_options' => [
					'border' => [ 'default' => 'solid' ],
					'width'  => [ 'default' => [ 'top' => 8, 'right' => 8, 'bottom' => 8, 'left' => 8, 'unit' => 'px', 'isLinked' => true ] ],
					'color'  => [ 'default' => '#ffffff' ],
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'           => 'sec_shadow',
				'selector'       => '{{WRAPPER}} .ta-is__secondary .ta-is__frame',
				'fields_options' => [
					'box_shadow_type' => [ 'default' => 'yes' ],
					'box_shadow'      => [ 'default' => [ 'horizontal' => 0, 'vertical' => 18, 'blur' => 40, 'spread' => -10, 'color' => 'rgba(15,30,55,0.35)' ] ],
				],
			]
		);
		$this->add_group_control( Group_Control_Css_Filter::get_type(), [ 'name' => 'sec_filters', 'selector' => '{{WRAPPER}} .ta-is__secondary .ta-is__img' ] );
		$this->add_control( 'sec_hover_zoom', [ 'label' => esc_html__( 'Zoom au survol', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'range' => [ 'px' => [ 'min' => 1, 'max' => 1.3, 'step' => 0.01 ] ], 'description' => esc_html__( '1 = aucun zoom.', 'tools-adapter' ), 'selectors' => [ '{{WRAPPER}} .ta-is__secondary:hover .ta-is__img' => 'transform: scale({{SIZE}});' ] ] );

		$this->end_controls_section();

		// ── Style : pastille ─────────────────────────────────────────────
		$this->start_controls_section( 'section_style_badge', [ 'label' => esc_html__( 'Pastille', 'tools-adapter' ), 'tab' => Controls_Manager::TAB_STYLE, 'condition' => [ 'show_badge' => 'yes' ] ] );

		$this->add_responsive_control( 'badge_size', [ 'label' => esc_html__( 'Diamètre', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'size_units' => [ 'px' ], 'range' => [ 'px' => [ 'min' => 60, 'max' => 260 ] ], 'default' => [ 'size' => 138, 'unit' => 'px' ], 'mobile_default' => [ 'size' => 104, 'unit' => 'px' ], 'selectors' => [ '{{WRAPPER}} .ta-is' => '--ta-is-badge-size: {{SIZE}}{{UNIT}};' ] ] );
		$this->add_responsive_control( 'badge_offset_x', [ 'label' => esc_html__( 'Décalage horizontal', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'size_units' => [ 'px', '%' ], 'range' => [ 'px' => [ 'min' => -200, 'max' => 200 ], '%' => [ 'min' => -50, 'max' => 50 ] ], 'description' => esc_html__( '0 = centrée sur le bord de l\'image. Positif = vers l\'extérieur.', 'tools-adapter' ), 'selectors' => [ '{{WRAPPER}} .ta-is' => '--ta-is-badge-x: {{SIZE}}{{UNIT}};' ] ] );
		$this->add_responsive_control( 'badge_offset_y', [ 'label' => esc_html__( 'Distance depuis le haut / bas', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'size_units' => [ 'px', '%' ], 'range' => [ 'px' => [ 'min' => -150, 'max' => 400 ], '%' => [ 'min' => -20, 'max' => 80 ] ], 'default' => [ 'size' => 4, 'unit' => '%' ], 'selectors' => [ '{{WRAPPER}} .ta-is' => '--ta-is-badge-y: {{SIZE}}{{UNIT}};' ] ] );
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'           => 'badge_bg',
				'types'          => [ 'classic', 'gradient' ],
				'exclude'        => [ 'image' ],
				'selector'       => '{{WRAPPER}} .ta-is__badge',
				'fields_options' => [
					'background'     => [ 'default' => 'gradient' ],
					'color'          => [ 'default' => '#FFC107' ],
					'color_b'        => [ 'default' => '#FF9800' ],
					'gradient_angle' => [ 'default' => [ 'unit' => 'deg', 'size' => 145 ] ],
				],
			]
		);
		$this->add_group_control( Group_Control_Border::get_type(), [ 'name' => 'badge_border', 'selector' => '{{WRAPPER}} .ta-is__badge' ] );
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'           => 'badge_shadow',
				'label'          => esc_html__( 'Halo', 'tools-adapter' ),
				'selector'       => '{{WRAPPER}} .ta-is__badge',
				'fields_options' => [
					'box_shadow_type' => [ 'default' => 'yes' ],
					'box_shadow'      => [ 'default' => [ 'horizontal' => 0, 'vertical' => 10, 'blur' => 40, 'spread' => 0, 'color' => 'rgba(255,152,0,0.55)' ] ],
				],
			]
		);
		$this->add_control( 'badge_ring_color', [ 'label' => esc_html__( 'Couleur de l\'anneau tournant', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => 'rgba(255,255,255,0.65)', 'selectors' => [ '{{WRAPPER}} .ta-is__badge::after' => 'border-color: {{VALUE}} transparent;' ], 'condition' => [ 'badge_animation' => 'spin' ] ] );

		$this->add_control( 'badge_number_heading', [ 'label' => esc_html__( 'Nombre', 'tools-adapter' ), 'type' => Controls_Manager::HEADING, 'separator' => 'before' ] );
		$this->add_control( 'badge_number_color', [ 'label' => esc_html__( 'Couleur', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#111827', 'selectors' => [ '{{WRAPPER}} .ta-is__badge-number' => 'color: {{VALUE}};' ] ] );
		$this->add_group_control( Group_Control_Typography::get_type(), [ 'name' => 'badge_number_typography', 'selector' => '{{WRAPPER}} .ta-is__badge-number' ] );

		$this->add_control( 'badge_text_heading', [ 'label' => esc_html__( 'Texte', 'tools-adapter' ), 'type' => Controls_Manager::HEADING, 'separator' => 'before' ] );
		$this->add_control( 'badge_text_color', [ 'label' => esc_html__( 'Couleur', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#111827', 'selectors' => [ '{{WRAPPER}} .ta-is__badge-text' => 'color: {{VALUE}};' ] ] );
		$this->add_group_control( Group_Control_Typography::get_type(), [ 'name' => 'badge_text_typography', 'selector' => '{{WRAPPER}} .ta-is__badge-text' ] );
		$this->add_control( 'badge_text_spacing', [ 'label' => esc_html__( 'Espace nombre / texte', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'range' => [ 'px' => [ 'min' => -10, 'max' => 30 ] ], 'selectors' => [ '{{WRAPPER}} .ta-is__badge-text' => 'margin-top: {{SIZE}}{{UNIT}};' ] ] );

		$this->end_controls_section();
	}

	/**
	 * Render one image (attachment or URL) inside a framed, optionally linked wrapper.
	 *
	 * @param array  $image Media control value.
	 * @param array  $link  URL control value.
	 * @param string $size  Image size.
	 * @param string $class Extra wrapper class.
	 * @param string $inner Extra HTML rendered inside the wrapper (badge).
	 */
	private function render_image( $image, $link, $size, $class, $inner = '' ) {
		$url = $link['url'] ?? '';
		$tag = $url ? 'a' : 'div';
		$attrs = '';
		if ( $url ) {
			$attrs .= ' href="' . esc_url( $url ) . '"';
			if ( ! empty( $link['is_external'] ) ) {
				$attrs .= ' target="_blank"';
			}
			$rel = [];
			if ( ! empty( $link['nofollow'] ) ) {
				$rel[] = 'nofollow';
			}
			if ( ! empty( $link['is_external'] ) ) {
				$rel[] = 'noopener';
			}
			if ( $rel ) {
				$attrs .= ' rel="' . esc_attr( implode( ' ', $rel ) ) . '"';
			}
		}

		echo '<' . $tag . ' class="ta-is__item ' . esc_attr( $class ) . '"' . $attrs . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '<span class="ta-is__frame">';
		if ( ! empty( $image['id'] ) ) {
			echo wp_get_attachment_image( (int) $image['id'], $size, false, [ 'class' => 'ta-is__img', 'loading' => 'lazy' ] );
		} elseif ( ! empty( $image['url'] ) ) {
			echo '<img class="ta-is__img" src="' . esc_url( $image['url'] ) . '" alt="" loading="lazy">';
		}
		echo '</span>';
		echo $inner; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built and escaped in render().
		echo '</' . $tag . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$corners  = array_keys( $this->corner_options() );
		$size     = in_array( $settings['image_size'] ?? 'large', [ 'medium', 'medium_large', 'large', 'full' ], true ) ? $settings['image_size'] : 'large';

		$show_sec = 'yes' === ( $settings['show_secondary'] ?? '' ) && ( ! empty( $settings['secondary_image']['url'] ) || ! empty( $settings['secondary_image']['id'] ) );
		$sec_pos  = in_array( $settings['secondary_position'] ?? '', $corners, true ) ? $settings['secondary_position'] : 'bottom-right';
		$badge_pos = in_array( $settings['badge_position'] ?? '', $corners, true ) ? $settings['badge_position'] : 'top-right';

		$classes = [ 'ta-is' ];
		if ( $show_sec ) {
			$classes[] = 'ta-is--sec-' . $sec_pos;
		}

		// Pastille (rendue dans le cadre de l'image principale).
		$badge = '';
		if ( 'yes' === ( $settings['show_badge'] ?? '' ) ) {
			$number   = trim( (string) ( $settings['badge_number'] ?? '' ) );
			$numeric  = '' !== $number && is_numeric( str_replace( ',', '.', $number ) );
			$animate  = $numeric && 'yes' === ( $settings['count_up'] ?? '' );
			$target   = $numeric ? (float) str_replace( ',', '.', $number ) : 0;
			$decimals = $numeric && false !== strpbrk( $number, '.,' ) ? strlen( substr( strrchr( str_replace( ',', '.', $number ), '.' ), 1 ) ) : 0;
			$lines    = array_filter( array_map( 'trim', explode( "\n", (string) ( $settings['badge_text'] ?? '' ) ) ), 'strlen' );

			$badge .= '<span class="ta-is__badge ta-is__badge--' . esc_attr( $badge_pos ) . '"' . ( $animate ? ' data-ta-stats data-duration="' . esc_attr( (string) absint( $settings['count_duration'] ?? 2000 ) ) . '"' : '' ) . '>';
			$badge .= '<span class="ta-is__badge-inner">';
			if ( '' !== $number || ! empty( $settings['badge_prefix'] ) || ! empty( $settings['badge_suffix'] ) ) {
				$badge .= '<span class="ta-is__badge-number">';
				$badge .= esc_html( $settings['badge_prefix'] ?? '' );
				$badge .= $animate
					? '<span data-count-to="' . esc_attr( (string) $target ) . '" data-decimals="' . esc_attr( (string) $decimals ) . '">' . esc_html( $number ) . '</span>'
					: esc_html( $number );
				$badge .= esc_html( $settings['badge_suffix'] ?? '' );
				$badge .= '</span>';
			}
			if ( $lines ) {
				$badge .= '<span class="ta-is__badge-text">' . implode( '<br>', array_map( 'esc_html', array_map( '\tools_adapter_translate', $lines ) ) ) . '</span>';
			}
			$badge .= '</span></span>';
		}
		?>
		<div class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
			<?php
			$this->render_image( $settings['main_image'] ?? [], $settings['main_link'] ?? [], $size, 'ta-is__main', $badge );
			if ( $show_sec ) {
				$this->render_image( $settings['secondary_image'], $settings['secondary_link'] ?? [], $size, 'ta-is__secondary' );
			}
			?>
		</div>
		<?php
	}
}
