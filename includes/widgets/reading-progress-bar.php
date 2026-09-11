<?php
namespace ToolsAdapter\Widgets;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Widget: Barre de progression de lecture — barre fixe indiquant l'avancement de lecture.
 */
class Reading_Progress_Bar extends Widget_Base {

	public function get_name() {
		return 'tools-adapter-reading-progress';
	}

	public function get_title() {
		return esc_html__( 'Barre de progression', 'tools-adapter' );
	}

	public function get_icon() {
		return 'eicon-skill-bar';
	}

	public function get_categories() {
		return [ 'tools-adapter' ];
	}

	public function get_keywords() {
		return [ 'progression', 'lecture', 'scroll', 'reading progress' ];
	}

	public function get_style_depends() {
		return [ 'tools-adapter-reading-progress' ];
	}

	public function get_script_depends() {
		return [ 'tools-adapter-reading-progress' ];
	}

	protected function register_controls() {
		$this->start_controls_section( 'section_content', [ 'label' => esc_html__( 'Réglages', 'tools-adapter' ) ] );

		$this->add_control(
			'position',
			[
				'label'   => esc_html__( 'Position', 'tools-adapter' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'top',
				'options' => [
					'top'    => esc_html__( 'Haut de la page', 'tools-adapter' ),
					'bottom' => esc_html__( 'Bas de la page', 'tools-adapter' ),
				],
			]
		);

		$this->add_control(
			'container_selector',
			[
				'label'       => esc_html__( 'Zone suivie', 'tools-adapter' ),
				'description' => esc_html__( 'Sélecteur CSS de la zone à mesurer (ex: article). Vide = toute la page.', 'tools-adapter' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'placeholder' => 'article, .elementor...',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section( 'section_style', [ 'label' => esc_html__( 'Style', 'tools-adapter' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_control( 'bar_height', [ 'label' => esc_html__( 'Épaisseur', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'range' => [ 'px' => [ 'min' => 2, 'max' => 20 ] ], 'default' => [ 'size' => 4, 'unit' => 'px' ], 'selectors' => [ '{{WRAPPER}} .ta-reading-progress' => 'height: {{SIZE}}{{UNIT}};' ] ] );
		$this->add_control( 'track_color', [ 'label' => esc_html__( 'Couleur du fond', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => 'rgba(0,0,0,0.08)', 'selectors' => [ '{{WRAPPER}} .ta-reading-progress' => 'background-color: {{VALUE}};' ] ] );
		$this->add_control( 'bar_color', [ 'label' => esc_html__( 'Couleur de la barre', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#C9A84C', 'selectors' => [ '{{WRAPPER}} .ta-reading-progress__bar' => 'background-color: {{VALUE}};' ] ] );
		$this->add_control( 'z_index', [ 'label' => esc_html__( 'Z-index', 'tools-adapter' ), 'type' => Controls_Manager::NUMBER, 'default' => 9999, 'selectors' => [ '{{WRAPPER}} .ta-reading-progress' => 'z-index: {{VALUE}};' ] ] );

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		?>
		<div
			class="ta-reading-progress ta-reading-progress--<?php echo esc_attr( $settings['position'] ?? 'top' ); ?>"
			data-ta-reading-progress
			data-container="<?php echo esc_attr( $settings['container_selector'] ?? '' ); ?>"
			role="progressbar"
			aria-valuemin="0"
			aria-valuemax="100"
			aria-valuenow="0"
		>
			<span class="ta-reading-progress__bar" data-progress-bar></span>
		</div>
		<?php
	}
}
