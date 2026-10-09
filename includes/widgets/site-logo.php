<?php
namespace ToolsAdapter\Widgets;

use Elementor\Controls_Manager;
use ToolsAdapter\Base_Widget;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Widget: Logo du Site — affiche le logo personnalisé ou une image avec lien retour et titre/slogan.
 */
class Site_Logo extends Base_Widget {

	public function get_name() {
		return 'tools-adapter-site-logo';
	}

	public function get_title() {
		return esc_html__( 'Logo du Site', 'tools-adapter' );
	}

	public function get_icon() {
		return 'eicon-site-logo';
	}

	public function get_categories() {
		return [ 'tools-adapter' ];
	}

	public function get_keywords() {
		return [ 'logo', 'site logo', 'header', 'branding', 'identite', 'titre', 'marque' ];
	}

	public function get_style_depends() {
		return [ 'tools-adapter-site-logo' ];
	}

	protected function register_controls() {
		// ==========================================
		// SECTION CONTENU : LOGO
		// ==========================================
		$this->start_controls_section(
			'section_logo',
			[ 'label' => esc_html__( 'Logo du Site', 'tools-adapter' ) ]
		);

		$this->add_control(
			'logo_source',
			[
				'label'   => esc_html__( 'Source du logo', 'tools-adapter' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'custom_logo',
				'options' => [
					'custom_logo'  => esc_html__( 'Logo natif du site (Personnaliser)', 'tools-adapter' ),
					'custom_image' => esc_html__( 'Image personnalisée', 'tools-adapter' ),
					'none'         => esc_html__( 'Texte uniquement (Titre du site)', 'tools-adapter' ),
				],
			]
		);

		$this->add_control(
			'custom_image',
			[
				'label'     => esc_html__( 'Choisir une image', 'tools-adapter' ),
				'type'      => Controls_Manager::MEDIA,
				'default'   => [ 'url' => Utils::get_placeholder_image_src() ],
				'condition' => [ 'logo_source' => 'custom_image' ],
			]
		);

		$this->add_control(
			'show_title',
			[
				'label'        => esc_html__( 'Afficher le titre du site', 'tools-adapter' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Oui', 'tools-adapter' ),
				'label_off'    => esc_html__( 'Non', 'tools-adapter' ),
				'return_value' => 'yes',
				'default'      => 'no',
			]
		);

		$this->add_control(
			'show_tagline',
			[
				'label'        => esc_html__( 'Afficher le slogan (tagline)', 'tools-adapter' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Oui', 'tools-adapter' ),
				'label_off'    => esc_html__( 'Non', 'tools-adapter' ),
				'return_value' => 'yes',
				'default'      => 'no',
			]
		);

		$this->add_control(
			'link_to',
			[
				'label'   => esc_html__( 'Lien', 'tools-adapter' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'home',
				'options' => [
					'home'   => esc_html__( 'Page d\'accueil (Recommandé)', 'tools-adapter' ),
					'custom' => esc_html__( 'URL personnalisée', 'tools-adapter' ),
					'none'   => esc_html__( 'Aucun lien', 'tools-adapter' ),
				],
			]
		);

		$this->add_control(
			'custom_link',
			[
				'label'         => esc_html__( 'URL du lien', 'tools-adapter' ),
				'type'          => Controls_Manager::URL,
				'placeholder'   => 'https://example.com',
				'show_external' => true,
				'condition'     => [ 'link_to' => 'custom' ],
			]
		);

		$this->add_responsive_control(
			'align',
			[
				'label'     => esc_html__( 'Alignement', 'tools-adapter' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'left'   => [ 'title' => esc_html__( 'Gauche', 'tools-adapter' ), 'icon' => 'eicon-text-align-left' ],
					'center' => [ 'title' => esc_html__( 'Centre', 'tools-adapter' ), 'icon' => 'eicon-text-align-center' ],
					'right'  => [ 'title' => esc_html__( 'Droite', 'tools-adapter' ), 'icon' => 'eicon-text-align-right' ],
				],
				'selectors' => [
					'{{WRAPPER}} .ta-site-logo-container' => 'text-align: {{VALUE}};',
					'{{WRAPPER}} .ta-site-logo-wrap'      => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		// ==========================================
		// SECTION STYLE : IMAGE
		// ==========================================
		$this->start_controls_section(
			'section_style_image',
			[
				'label'     => esc_html__( 'Image du Logo', 'tools-adapter' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [ 'logo_source!' => 'none' ],
			]
		);

		$this->add_responsive_control(
			'logo_width',
			[
				'label'      => esc_html__( 'Largeur', 'tools-adapter' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'vw' ],
				'range'      => [
					'px' => [ 'min' => 20, 'max' => 600, 'step' => 2 ],
					'%'  => [ 'min' => 10, 'max' => 100 ],
				],
				'selectors'  => [
					'{{WRAPPER}} .ta-site-logo-img' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'logo_max_height',
			[
				'label'      => esc_html__( 'Hauteur maximale', 'tools-adapter' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'vh' ],
				'range'      => [
					'px' => [ 'min' => 20, 'max' => 300, 'step' => 2 ],
				],
				'selectors'  => [
					'{{WRAPPER}} .ta-site-logo-img' => 'max-height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'logo_border_radius',
			[
				'label'      => esc_html__( 'Rayon des coins', 'tools-adapter' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .ta-site-logo-img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'logo_hover_opacity',
			[
				'label'     => esc_html__( 'Opacité au survol', 'tools-adapter' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [ 'min' => 0.2, 'max' => 1, 'step' => 0.05 ],
				],
				'selectors' => [
					'{{WRAPPER}} .ta-site-logo-link:hover .ta-site-logo-img' => 'opacity: {{SIZE}};',
				],
			]
		);

		$this->end_controls_section();

		// ==========================================
		// SECTION STYLE : TEXTE
		// ==========================================
		$this->start_controls_section(
			'section_style_text',
			[
				'label' => esc_html__( 'Titre & Slogan', 'tools-adapter' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'title_typography',
				'label'     => esc_html__( 'Typographie Titre', 'tools-adapter' ),
				'selector'  => '{{WRAPPER}} .ta-site-logo-title',
				'condition' => [ 'show_title' => 'yes' ],
			]
		);

		$this->add_control(
			'title_color',
			[
				'label'     => esc_html__( 'Couleur du Titre', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ta-site-logo-title' => 'color: {{VALUE}};',
				],
				'condition' => [ 'show_title' => 'yes' ],
			]
		);

		$this->add_control(
			'title_color_hover',
			[
				'label'     => esc_html__( 'Couleur Titre au survol', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ta-site-logo-link:hover .ta-site-logo-title' => 'color: {{VALUE}};',
				],
				'condition' => [ 'show_title' => 'yes' ],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'tagline_typography',
				'label'     => esc_html__( 'Typographie Slogan', 'tools-adapter' ),
				'selector'  => '{{WRAPPER}} .ta-site-logo-tagline',
				'condition' => [ 'show_tagline' => 'yes' ],
			]
		);

		$this->add_control(
			'tagline_color',
			[
				'label'     => esc_html__( 'Couleur du Slogan', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ta-site-logo-tagline' => 'color: {{VALUE}};',
				],
				'condition' => [ 'show_tagline' => 'yes' ],
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		$img_url = '';
		$img_alt = get_bloginfo( 'name' );

		if ( 'custom_logo' === $settings['logo_source'] ) {
			$custom_logo_id = get_theme_mod( 'custom_logo' );
			if ( $custom_logo_id ) {
				$img_src = wp_get_attachment_image_src( $custom_logo_id, 'full' );
				if ( $img_src ) {
					$img_url = $img_src[0];
					$alt     = get_post_meta( $custom_logo_id, '_wp_attachment_image_alt', true );
					if ( ! empty( $alt ) ) {
						$img_alt = $alt;
					}
				}
			}
		} elseif ( 'custom_image' === $settings['logo_source'] && ! empty( $settings['custom_image']['url'] ) ) {
			$img_url = $settings['custom_image']['url'];
			if ( ! empty( $settings['custom_image']['alt'] ) ) {
				$img_alt = $settings['custom_image']['alt'];
			}
		}

		$link_url = '';
		$is_external = false;
		$nofollow = false;

		if ( 'home' === $settings['link_to'] ) {
			$link_url = home_url( '/' );
		} elseif ( 'custom' === $settings['link_to'] && ! empty( $settings['custom_link']['url'] ) ) {
			$link_url    = $settings['custom_link']['url'];
			$is_external = ! empty( $settings['custom_link']['is_external'] );
			$nofollow    = ! empty( $settings['custom_link']['nofollow'] );
		}

		$show_title   = 'yes' === $settings['show_title'] || ( 'none' === $settings['logo_source'] );
		$show_tagline = 'yes' === $settings['show_tagline'];
		$site_title   = get_bloginfo( 'name' );
		$site_tagline = get_bloginfo( 'description' );
		?>
		<div class="ta-site-logo-container">
			<div class="ta-site-logo-wrap">
				<?php if ( $link_url ) : ?>
					<a href="<?php echo esc_url( $link_url ); ?>"
					   class="ta-site-logo-link"
					   <?php if ( $is_external ) : ?>target="_blank" rel="noopener noreferrer"<?php endif; ?>
					   <?php if ( $nofollow && ! $is_external ) : ?>rel="nofollow"<?php endif; ?>>
				<?php else : ?>
					<div class="ta-site-logo-link">
				<?php endif; ?>

					<?php if ( $img_url ) : ?>
						<img src="<?php echo esc_url( $img_url ); ?>"
						     alt="<?php echo esc_attr( $img_alt ); ?>"
						     class="ta-site-logo-img">
					<?php endif; ?>

					<?php if ( $show_title || $show_tagline ) : ?>
						<div class="ta-site-logo-text-wrap">
							<?php if ( $show_title ) : ?>
								<span class="ta-site-logo-title"><?php echo esc_html( $site_title ); ?></span>
							<?php endif; ?>
							<?php if ( $show_tagline && $site_tagline ) : ?>
								<span class="ta-site-logo-tagline"><?php echo esc_html( $site_tagline ); ?></span>
							<?php endif; ?>
						</div>
					<?php endif; ?>

				<?php if ( $link_url ) : ?>
					</a>
				<?php else : ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}
}
