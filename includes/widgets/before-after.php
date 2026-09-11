<?php
namespace ToolsAdapter\Widgets;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Widget: Avant/Après — slider comparatif de deux images.
 */
class Before_After extends Widget_Base {

	public function get_name() {
		return 'tools-adapter-before-after';
	}

	public function get_title() {
		return esc_html__( 'Avant / Après', 'tools-adapter' );
	}

	public function get_icon() {
		return 'eicon-slider-3d';
	}

	public function get_categories() {
		return [ 'tools-adapter' ];
	}

	public function get_keywords() {
		return [ 'avant', 'après', 'comparaison', 'before', 'after', 'slider' ];
	}

	public function get_style_depends() {
		return [ 'tools-adapter-before-after' ];
	}

	public function get_script_depends() {
		return [ 'tools-adapter-before-after' ];
	}

	protected function register_controls() {
		$this->start_controls_section( 'section_content', [ 'label' => esc_html__( 'Images', 'tools-adapter' ) ] );

		$this->add_control( 'before_image', [ 'label' => esc_html__( 'Image « Avant »', 'tools-adapter' ), 'type' => Controls_Manager::MEDIA, 'default' => [ 'url' => \Elementor\Utils::get_placeholder_image_src() ] ] );
		$this->add_control( 'before_label', [ 'label' => esc_html__( 'Libellé « Avant »', 'tools-adapter' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'Avant', 'tools-adapter' ) ] );
		$this->add_control( 'after_image', [ 'label' => esc_html__( 'Image « Après »', 'tools-adapter' ), 'type' => Controls_Manager::MEDIA, 'default' => [ 'url' => \Elementor\Utils::get_placeholder_image_src() ], 'separator' => 'before' ] );
		$this->add_control( 'after_label', [ 'label' => esc_html__( 'Libellé « Après »', 'tools-adapter' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'Après', 'tools-adapter' ) ] );

		$this->add_control(
			'orientation',
			[
				'label'        => esc_html__( 'Orientation', 'tools-adapter' ),
				'type'         => Controls_Manager::SELECT,
				'default'      => 'horizontal',
				'options'      => [
					'horizontal' => esc_html__( 'Horizontale', 'tools-adapter' ),
					'vertical'   => esc_html__( 'Verticale', 'tools-adapter' ),
				],
				'prefix_class' => 'ta-ba--',
				'separator'    => 'before',
			]
		);

		$this->add_control(
			'initial_position',
			[
				'label'   => esc_html__( 'Position initiale (%)', 'tools-adapter' ),
				'type'    => Controls_Manager::SLIDER,
				'range'   => [ 'px' => [ 'min' => 0, 'max' => 100 ] ],
				'default' => [ 'size' => 50 ],
			]
		);

		$this->add_control( 'show_labels', [ 'label' => esc_html__( 'Afficher les libellés', 'tools-adapter' ), 'type' => Controls_Manager::SWITCHER, 'return_value' => 'yes', 'default' => 'yes' ] );

		$this->add_responsive_control(
			'height',
			[
				'label'      => esc_html__( 'Hauteur', 'tools-adapter' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'vh' ],
				'range'      => [ 'px' => [ 'min' => 150, 'max' => 800 ] ],
				'default'    => [ 'size' => 420, 'unit' => 'px' ],
				'selectors'  => [ '{{WRAPPER}} .ta-ba' => 'height: {{SIZE}}{{UNIT}};' ],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section( 'section_style', [ 'label' => esc_html__( 'Style', 'tools-adapter' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_control( 'handle_color', [ 'label' => esc_html__( 'Couleur du curseur', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#ffffff', 'selectors' => [ '{{WRAPPER}} .ta-ba__handle' => 'background-color: {{VALUE}}; color: {{VALUE}};' ] ] );
		$this->add_control( 'handle_size', [ 'label' => esc_html__( 'Taille du curseur', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'range' => [ 'px' => [ 'min' => 20, 'max' => 80 ] ], 'default' => [ 'size' => 40, 'unit' => 'px' ], 'selectors' => [ '{{WRAPPER}} .ta-ba__handle-btn' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};' ] ] );
		$this->add_control( 'label_color', [ 'label' => esc_html__( 'Couleur du texte des libellés', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#ffffff', 'separator' => 'before', 'selectors' => [ '{{WRAPPER}} .ta-ba__label' => 'color: {{VALUE}};' ] ] );
		$this->add_control( 'label_bg', [ 'label' => esc_html__( 'Fond des libellés', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => 'rgba(0,0,0,0.55)', 'selectors' => [ '{{WRAPPER}} .ta-ba__label' => 'background-color: {{VALUE}};' ] ] );
		$this->add_control( 'ba_radius', [ 'label' => esc_html__( 'Arrondi', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'range' => [ 'px' => [ 'min' => 0, 'max' => 60 ] ], 'separator' => 'before', 'selectors' => [ '{{WRAPPER}} .ta-ba' => 'border-radius: {{SIZE}}{{UNIT}}; overflow: hidden;' ] ] );
		$this->add_group_control( Group_Control_Border::get_type(), [ 'name' => 'ba_border', 'selector' => '{{WRAPPER}} .ta-ba' ] );
		$this->add_group_control( Group_Control_Box_Shadow::get_type(), [ 'name' => 'ba_shadow', 'selector' => '{{WRAPPER}} .ta-ba' ] );

		$this->end_controls_section();
	}

	protected function render() {
		$settings   = $this->get_settings_for_display();
		$before_url = $settings['before_image']['url'] ?? '';
		$after_url  = $settings['after_image']['url'] ?? '';
		if ( empty( $before_url ) || empty( $after_url ) ) {
			return;
		}
		$position = isset( $settings['initial_position']['size'] ) ? (float) $settings['initial_position']['size'] : 50;
		?>
		<div class="ta-ba" data-ta-before-after data-position="<?php echo esc_attr( (string) $position ); ?>">
			<div class="ta-ba__image ta-ba__image--before">
				<img src="<?php echo esc_url( $before_url ); ?>" alt="<?php echo esc_attr( $settings['before_label'] ?? '' ); ?>" loading="lazy" />
				<?php if ( 'yes' === ( $settings['show_labels'] ?? '' ) && ! empty( $settings['before_label'] ) ) : ?>
					<span class="ta-ba__label ta-ba__label--before"><?php echo esc_html( \tools_adapter_translate( $settings['before_label'] ) ); ?></span>
				<?php endif; ?>
			</div>
			<div class="ta-ba__image ta-ba__image--after" data-ba-clip>
				<img src="<?php echo esc_url( $after_url ); ?>" alt="<?php echo esc_attr( $settings['after_label'] ?? '' ); ?>" loading="lazy" />
				<?php if ( 'yes' === ( $settings['show_labels'] ?? '' ) && ! empty( $settings['after_label'] ) ) : ?>
					<span class="ta-ba__label ta-ba__label--after"><?php echo esc_html( \tools_adapter_translate( $settings['after_label'] ) ); ?></span>
				<?php endif; ?>
			</div>
			<div class="ta-ba__handle" data-ba-handle>
				<span class="ta-ba__handle-btn" aria-hidden="true">
					<span class="ta-ba__handle-arrow ta-ba__handle-arrow--left">&#8249;</span>
					<span class="ta-ba__handle-arrow ta-ba__handle-arrow--right">&#8250;</span>
				</span>
			</div>
		</div>
		<?php
	}
}
