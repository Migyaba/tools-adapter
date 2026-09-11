<?php
namespace ToolsAdapter\Widgets;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;
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
class Hero_Banner extends Widget_Base {

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

		// Background.
		$this->start_controls_section( 'section_background', [ 'label' => esc_html__( 'Fond', 'tools-adapter' ) ] );

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'hero_bg',
				'types'    => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .ta-hero',
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
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$tag      = in_array( $settings['title_tag'], [ 'h1', 'h2', 'h3', 'div' ], true ) ? $settings['title_tag'] : 'h1';
		?>
		<div class="ta-hero">
			<span class="ta-hero__overlay" aria-hidden="true"></span>
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
		</div>
		<?php
	}
}
