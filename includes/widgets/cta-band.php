<?php
namespace ToolsAdapter\Widgets;

use Elementor\Controls_Manager;
use ToolsAdapter\Base_Widget;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Widget: Bande CTA — bloc pleine largeur titre + description + bouton.
 */
class Cta_Band extends Base_Widget {

	public function get_name() {
		return 'tools-adapter-cta-band';
	}

	public function get_title() {
		return esc_html__( 'Bande CTA', 'tools-adapter' );
	}

	public function get_icon() {
		return 'eicon-call-to-action';
	}

	public function get_categories() {
		return [ 'tools-adapter' ];
	}

	public function get_keywords() {
		return [ 'cta', 'call to action', 'bande', 'appel à action' ];
	}

	public function get_style_depends() {
		return [ 'tools-adapter-cta-band' ];
	}

	protected function register_controls() {
		$this->start_controls_section( 'section_content', [ 'label' => esc_html__( 'Contenu', 'tools-adapter' ) ] );

		$this->add_control(
			'title',
			[
				'label'   => esc_html__( 'Titre', 'tools-adapter' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Prêt à passer à l\'action ?', 'tools-adapter' ),
			]
		);

		$this->add_control(
			'description',
			[
				'label'   => esc_html__( 'Description', 'tools-adapter' ),
				'type'    => Controls_Manager::TEXTAREA,
				'rows'    => 2,
				'default' => esc_html__( 'Profitez de notre offre dès aujourd\'hui.', 'tools-adapter' ),
			]
		);

		$this->add_control(
			'btn_text',
			[
				'label'   => esc_html__( 'Texte du bouton', 'tools-adapter' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Commencer', 'tools-adapter' ),
			]
		);

		$this->add_control(
			'btn_link',
			[
				'label'   => esc_html__( 'Lien du bouton', 'tools-adapter' ),
				'type'    => Controls_Manager::URL,
				'default' => [ 'url' => '#' ],
			]
		);

		$this->add_control(
			'btn_icon',
			[
				'label' => esc_html__( 'Icône du bouton', 'tools-adapter' ),
				'type'  => Controls_Manager::ICONS,
			]
		);

		$this->add_control(
			'layout',
			[
				'label'        => esc_html__( 'Disposition', 'tools-adapter' ),
				'type'         => Controls_Manager::SELECT,
				'default'      => 'horizontal',
				'options'      => [
					'horizontal' => esc_html__( 'Horizontale (texte + bouton côte à côte)', 'tools-adapter' ),
					'stacked'    => esc_html__( 'Empilée (centrée)', 'tools-adapter' ),
				],
				'prefix_class' => 'ta-cta--',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section( 'section_style_box', [ 'label' => esc_html__( 'Boîte', 'tools-adapter' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'           => 'cta_bg',
				'types'          => [ 'classic', 'gradient' ],
				'selector'       => '{{WRAPPER}} .ta-cta',
				'fields_options' => [ 'color' => [ 'default' => '#C9A84C' ] ],
			]
		);

		$this->add_responsive_control(
			'cta_padding',
			[
				'label'      => esc_html__( 'Espacement interne', 'tools-adapter' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'default'    => [ 'top' => '40', 'right' => '48', 'bottom' => '40', 'left' => '48', 'unit' => 'px' ],
				'selectors'  => [ '{{WRAPPER}} .ta-cta' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
			]
		);

		$this->add_control(
			'cta_radius',
			[
				'label'      => esc_html__( 'Arrondi', 'tools-adapter' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 'px' => [ 'min' => 0, 'max' => 60 ] ],
				'default'    => [ 'size' => 12, 'unit' => 'px' ],
				'selectors'  => [ '{{WRAPPER}} .ta-cta' => 'border-radius: {{SIZE}}{{UNIT}};' ],
			]
		);

		$this->add_group_control( Group_Control_Border::get_type(), [ 'name' => 'cta_border', 'selector' => '{{WRAPPER}} .ta-cta' ] );
		$this->add_group_control( Group_Control_Box_Shadow::get_type(), [ 'name' => 'cta_shadow', 'selector' => '{{WRAPPER}} .ta-cta' ] );

		$this->end_controls_section();

		$this->start_controls_section( 'section_style_text', [ 'label' => esc_html__( 'Texte', 'tools-adapter' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_control( 'title_color', [ 'label' => esc_html__( 'Couleur titre', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#1c1c1c', 'selectors' => [ '{{WRAPPER}} .ta-cta__title' => 'color: {{VALUE}};' ] ] );
		$this->add_group_control( Group_Control_Typography::get_type(), [ 'name' => 'title_typography', 'selector' => '{{WRAPPER}} .ta-cta__title' ] );
		$this->add_control( 'description_color', [ 'label' => esc_html__( 'Couleur description', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#333333', 'separator' => 'before', 'selectors' => [ '{{WRAPPER}} .ta-cta__description' => 'color: {{VALUE}};' ] ] );
		$this->add_group_control( Group_Control_Typography::get_type(), [ 'name' => 'description_typography', 'selector' => '{{WRAPPER}} .ta-cta__description' ] );

		$this->end_controls_section();

		$this->start_controls_section( 'section_style_button', [ 'label' => esc_html__( 'Bouton', 'tools-adapter' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_control( 'btn_color', [ 'label' => esc_html__( 'Texte', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#ffffff', 'selectors' => [ '{{WRAPPER}} .ta-cta__btn' => 'color: {{VALUE}};' ] ] );
		$this->add_control( 'btn_bg', [ 'label' => esc_html__( 'Fond', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#1c1c1c', 'selectors' => [ '{{WRAPPER}} .ta-cta__btn' => 'background-color: {{VALUE}};' ] ] );
		$this->add_control( 'btn_color_hover', [ 'label' => esc_html__( 'Texte (survol)', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .ta-cta__btn:hover' => 'color: {{VALUE}};' ] ] );
		$this->add_control( 'btn_bg_hover', [ 'label' => esc_html__( 'Fond (survol)', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .ta-cta__btn:hover' => 'background-color: {{VALUE}};' ] ] );
		$this->add_responsive_control( 'btn_padding', [ 'label' => esc_html__( 'Espacement interne', 'tools-adapter' ), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => [ 'px', 'em' ], 'default' => [ 'top' => '14', 'right' => '30', 'bottom' => '14', 'left' => '30', 'unit' => 'px' ], 'separator' => 'before', 'selectors' => [ '{{WRAPPER}} .ta-cta__btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ] ] );
		$this->add_control( 'btn_radius', [ 'label' => esc_html__( 'Arrondi', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'size_units' => [ 'px', '%' ], 'range' => [ 'px' => [ 'min' => 0, 'max' => 60 ] ], 'default' => [ 'size' => 6, 'unit' => 'px' ], 'selectors' => [ '{{WRAPPER}} .ta-cta__btn' => 'border-radius: {{SIZE}}{{UNIT}};' ] ] );

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$url      = $settings['btn_link']['url'] ?? '#';
		$target   = ! empty( $settings['btn_link']['is_external'] ) ? ' target="_blank"' : '';
		$nofollow = ! empty( $settings['btn_link']['nofollow'] ) ? ' rel="nofollow"' : '';
		?>
		<div class="ta-cta">
			<div class="ta-cta__text">
				<?php if ( ! empty( $settings['title'] ) ) : ?>
					<p class="ta-cta__title"><?php echo esc_html( \tools_adapter_translate( $settings['title'] ) ); ?></p>
				<?php endif; ?>
				<?php if ( ! empty( $settings['description'] ) ) : ?>
					<p class="ta-cta__description"><?php echo esc_html( \tools_adapter_translate( $settings['description'] ) ); ?></p>
				<?php endif; ?>
			</div>
			<?php if ( ! empty( $settings['btn_text'] ) ) : ?>
				<a class="ta-cta__btn" href="<?php echo esc_url( $url ); ?>"<?php echo $target . $nofollow; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
					<?php if ( ! empty( $settings['btn_icon']['value'] ) ) : ?>
						<?php \Elementor\Icons_Manager::render_icon( $settings['btn_icon'], [ 'aria-hidden' => 'true' ] ); ?>
					<?php endif; ?>
					<span><?php echo esc_html( \tools_adapter_translate( $settings['btn_text'] ) ); ?></span>
				</a>
			<?php endif; ?>
		</div>
		<?php
	}
}
