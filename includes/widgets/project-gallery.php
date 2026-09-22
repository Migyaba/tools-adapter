<?php
namespace ToolsAdapter\Widgets;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use Elementor\Repeater;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Widget: Galerie Projets Mosaïque — grille Bento 6 cadres avec diaporama en fondu (FADE) par projet.
 */
class Project_Gallery extends Widget_Base {

	public function get_name() {
		return 'tools-adapter-project-gallery';
	}

	public function get_title() {
		return esc_html__( 'Galerie Projets Mosaïque', 'tools-adapter' );
	}

	public function get_icon() {
		return 'eicon-gallery-grid';
	}

	public function get_categories() {
		return [ 'tools-adapter' ];
	}

	public function get_keywords() {
		return [ 'galerie', 'projets', 'mosaïque', 'bento', 'fade', 'crossfade', 'portfolio', 'réalisations' ];
	}

	public function get_style_depends() {
		return [ 'tools-adapter-project-gallery' ];
	}

	public function get_script_depends() {
		return [ 'tools-adapter-project-gallery' ];
	}

	protected function register_controls() {
		// ==========================================
		// SECTION CONTENU : PROJETS
		// ==========================================
		$this->start_controls_section(
			'section_projects',
			[ 'label' => esc_html__( 'Projets (Cadres)', 'tools-adapter' ) ]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'title',
			[
				'label'       => esc_html__( 'Titre du projet', 'tools-adapter' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Projet d\'aménagement', 'tools-adapter' ),
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'subtitle',
			[
				'label'       => esc_html__( 'Sur-titre / Catégorie (optionnel)', 'tools-adapter' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'ex: Terrasse, Rénovation, etc.', 'tools-adapter' ),
			]
		);

		$repeater->add_control(
			'gallery',
			[
				'label'       => esc_html__( 'Images du projet (Diaporama)', 'tools-adapter' ),
				'type'        => Controls_Manager::GALLERY,
				'default'     => [],
				'description' => esc_html__( 'Sélectionnez plusieurs photos pour activer le défilement en fondu enchaîné.', 'tools-adapter' ),
			]
		);

		$repeater->add_control(
			'link',
			[
				'label'       => esc_html__( 'Lien vers le projet', 'tools-adapter' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => 'https://exemple.com/projet',
			]
		);

		$this->add_control(
			'projects',
			[
				'label'       => esc_html__( 'Liste des projets', 'tools-adapter' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => [
					[
						'title'    => esc_html__( 'Réception Foch — Paris 16e', 'tools-adapter' ),
						'subtitle' => esc_html__( 'Terrasse & Spa', 'tools-adapter' ),
					],
					[
						'title'    => esc_html__( 'Jardin Ranelagh — Paris 16e', 'tools-adapter' ),
						'subtitle' => esc_html__( 'Aménagement extérieur', 'tools-adapter' ),
					],
					[
						'title'    => esc_html__( 'Île de la Jatte — Neuilly', 'tools-adapter' ),
						'subtitle' => esc_html__( 'Végétalisation & Bac', 'tools-adapter' ),
					],
					[
						'title'    => esc_html__( 'Saussaye — Neuilly', 'tools-adapter' ),
						'subtitle' => esc_html__( 'Façade & Cour', 'tools-adapter' ),
					],
					[
						'title'    => esc_html__( 'Hôtel particulier — Paris 16e', 'tools-adapter' ),
						'subtitle' => esc_html__( 'Toit-terrasse panoramique', 'tools-adapter' ),
					],
					[
						'title'    => esc_html__( 'Balcon filant — Paris 7e', 'tools-adapter' ),
						'subtitle' => esc_html__( 'Espace dinatoire arboré', 'tools-adapter' ),
					],
				],
				'title_field' => '{{{ title }}}',
			]
		);

		$this->end_controls_section();

		// ==========================================
		// SECTION CONTENU : RÉGLAGES DU DIAPORAMA
		// ==========================================
		$this->start_controls_section(
			'section_slider_settings',
			[ 'label' => esc_html__( 'Animation & Défilement', 'tools-adapter' ) ]
		);

		$this->add_control(
			'interval',
			[
				'label'       => esc_html__( 'Intervalle d\'affichage (ms)', 'tools-adapter' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 4000,
				'min'         => 1500,
				'max'         => 12000,
				'step'        => 250,
				'description' => esc_html__( 'Temps d\'affichage de chaque image avant la transition en fondu (ex: 4000 = 4 secondes).', 'tools-adapter' ),
			]
		);

		$this->add_control(
			'fade_speed',
			[
				'label'       => esc_html__( 'Durée du fondu (ms)', 'tools-adapter' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 1000,
				'min'         => 300,
				'max'         => 3000,
				'step'        => 100,
				'selectors'   => [ '{{WRAPPER}} .ta-project-gallery' => '--ta-pg-fade-speed: {{VALUE}}ms;' ],
				'description' => esc_html__( 'Durée de la transition progressive d\'opacité.', 'tools-adapter' ),
			]
		);

		$this->add_control(
			'pause_on_hover',
			[
				'label'        => esc_html__( 'Pause au survol', 'tools-adapter' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'true',
				'default'      => 'true',
			]
		);

		$this->add_control(
			'show_count_badge',
			[
				'label'        => esc_html__( 'Badge compteur de photos', 'tools-adapter' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'no',
			]
		);

		$this->end_controls_section();

		// ==========================================
		// SECTION STYLE : MOSAÏQUE & GRILLE
		// ==========================================
		$this->start_controls_section(
			'section_style_layout',
			[
				'label' => esc_html__( 'Mosaïque & Cadres', 'tools-adapter' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'gallery_height',
			[
				'label'      => esc_html__( 'Hauteur de la mosaïque', 'tools-adapter' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'vh' ],
				'range'      => [
					'px' => [ 'min' => 400, 'max' => 1200 ],
					'vh' => [ 'min' => 40, 'max' => 100 ],
				],
				'default'    => [ 'size' => 680, 'unit' => 'px' ],
				'selectors'  => [ '{{WRAPPER}} .ta-project-gallery' => '--ta-pg-height: {{SIZE}}{{UNIT}};' ],
			]
		);

		$this->add_responsive_control(
			'grid_gap',
			[
				'label'      => esc_html__( 'Espacement des cadres (Gap)', 'tools-adapter' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 'px' => [ 'min' => 0, 'max' => 40 ] ],
				'default'    => [ 'size' => 16, 'unit' => 'px' ],
				'selectors'  => [ '{{WRAPPER}} .ta-project-gallery' => '--ta-pg-gap: {{SIZE}}{{UNIT}};' ],
			]
		);

		$this->add_control(
			'card_radius',
			[
				'label'      => esc_html__( 'Arrondi des coins (Border Radius)', 'tools-adapter' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'      => [ 'px' => [ 'min' => 0, 'max' => 40 ] ],
				'default'    => [ 'size' => 16, 'unit' => 'px' ],
				'selectors'  => [ '{{WRAPPER}} .ta-project-gallery' => '--ta-pg-radius: {{SIZE}}{{UNIT}};' ],
			]
		);

		$this->add_control(
			'hover_zoom',
			[
				'label'        => esc_html__( 'Zoom léger sur l\'image au survol', 'tools-adapter' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'card_shadow',
				'label'    => esc_html__( 'Ombre des cadres', 'tools-adapter' ),
				'selector' => '{{WRAPPER}} .ta-project-card',
			]
		);

		$this->end_controls_section();

		// ==========================================
		// SECTION STYLE : SUPERPOSITION & DÉGRADÉ
		// ==========================================
		$this->start_controls_section(
			'section_style_overlay',
			[
				'label' => esc_html__( 'Dégradé d\'assombrissement', 'tools-adapter' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'scrim_opacity',
			[
				'label'       => esc_html__( 'Intensité du dégradé inférieur', 'tools-adapter' ),
				'type'        => Controls_Manager::SLIDER,
				'range'       => [ 'px' => [ 'min' => 0, 'max' => 1, 'step' => 0.05 ] ],
				'default'     => [ 'size' => 0.75 ],
				'selectors'   => [ '{{WRAPPER}} .ta-project-gallery' => '--ta-pg-scrim-opacity: {{SIZE}};' ],
				'description' => esc_html__( 'Garantit une lisibilité parfaite des titres blancs sur toute photo.', 'tools-adapter' ),
			]
		);

		$this->end_controls_section();

		// ==========================================
		// SECTION STYLE : TYPOGRAPHIE & TEXTES
		// ==========================================
		$this->start_controls_section(
			'section_style_typography',
			[
				'label' => esc_html__( 'Typographie & Textes', 'tools-adapter' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'title_color',
			[
				'label'     => esc_html__( 'Couleur du titre', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [ '{{WRAPPER}} .ta-project-card__title' => 'color: {{VALUE}};' ],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .ta-project-card__title',
			]
		);

		$this->add_control(
			'subtitle_color',
			[
				'label'     => esc_html__( 'Couleur du sur-titre', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(255, 255, 255, 0.85)',
				'selectors' => [ '{{WRAPPER}} .ta-project-card__subtitle' => 'color: {{VALUE}};' ],
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'subtitle_typography',
				'selector' => '{{WRAPPER}} .ta-project-card__subtitle',
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$projects = ! empty( $settings['projects'] ) ? $settings['projects'] : [];

		if ( empty( $projects ) ) {
			return;
		}

		$interval   = ! empty( $settings['interval'] ) ? absint( $settings['interval'] ) : 4000;
		$pause      = ( $settings['pause_on_hover'] ?? 'true' ) === 'true' ? 'true' : 'false';
		$hover_zoom = ( $settings['hover_zoom'] ?? 'yes' ) === 'yes';
		$show_count = ( $settings['show_count_badge'] ?? 'no' ) === 'yes';

		$wrapper_classes = [ 'ta-project-gallery' ];
		if ( $hover_zoom ) {
			$wrapper_classes[] = 'ta-project-gallery--hover-zoom';
		}

		$placeholder = Utils::get_placeholder_image_src();
		?>
		<div
			class="<?php echo esc_attr( implode( ' ', $wrapper_classes ) ); ?>"
			data-interval="<?php echo esc_attr( (string) $interval ); ?>"
			data-pause-hover="<?php echo esc_attr( $pause ); ?>"
		>
			<div class="ta-project-gallery__grid">
				<?php foreach ( $projects as $index => $project ) : ?>
					<?php
					$title    = $project['title'] ?? '';
					$subtitle = $project['subtitle'] ?? '';
					$gallery  = $project['gallery'] ?? [];
					$link_url = ! empty( $project['link']['url'] ) ? $project['link']['url'] : '';
					$target   = ! empty( $project['link']['is_external'] ) ? ' target="_blank"' : '';
					$nofollow = ! empty( $project['link']['nofollow'] ) ? ' rel="nofollow"' : '';

					// Liste des URLs d'images pour ce projet
					$images = [];
					if ( ! empty( $gallery ) && is_array( $gallery ) ) {
						foreach ( $gallery as $img ) {
							if ( ! empty( $img['url'] ) ) {
								$images[] = $img['url'];
							}
						}
					}

					// Si aucune image n'a encore été sélectionnée dans Elementor, affiche un placeholder
					if ( empty( $images ) ) {
						$images[] = $placeholder;
					}

					$image_count = count( $images );
					?>
					<article class="ta-project-card" data-card-index="<?php echo esc_attr( (string) $index ); ?>">
						<?php if ( $link_url ) : ?>
							<a class="ta-project-card__link" href="<?php echo esc_url( $link_url ); ?>"<?php echo $target . $nofollow; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
								<?php echo esc_html( $title ); ?>
							</a>
						<?php endif; ?>

						<div class="ta-project-card__slides">
							<?php foreach ( $images as $img_idx => $img_url ) : ?>
								<img
									src="<?php echo esc_url( $img_url ); ?>"
									alt="<?php echo esc_attr( $title ); ?>"
									class="ta-project-card__slide<?php echo 0 === $img_idx ? ' is-active' : ''; ?>"
									loading="lazy"
								/>
							<?php endforeach; ?>
						</div>

						<div class="ta-project-card__overlay"></div>

						<?php if ( $show_count && $image_count > 1 ) : ?>
							<div class="ta-project-card__count">
								<i class="fas fa-images" aria-hidden="true"></i>
								<span><?php echo esc_html( (string) $image_count ); ?></span>
							</div>
						<?php endif; ?>

						<div class="ta-project-card__content">
							<?php if ( $subtitle ) : ?>
								<span class="ta-project-card__subtitle">
									<?php echo esc_html( \tools_adapter_translate( $subtitle ) ); ?>
								</span>
							<?php endif; ?>

							<?php if ( $title ) : ?>
								<h3 class="ta-project-card__title">
									<?php echo esc_html( \tools_adapter_translate( $title ) ); ?>
								</h3>
							<?php endif; ?>
						</div>
					</article>
				<?php endforeach; ?>
			</div>
		</div>
		<?php
	}
}
