<?php
namespace ToolsAdapter\Widgets;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use Elementor\Repeater;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Widget: Compteurs / Statistiques animées.
 */
class Stats_Counters extends Widget_Base {

	public function get_name() {
		return 'tools-adapter-stats';
	}

	public function get_title() {
		return esc_html__( 'Compteurs / Statistiques', 'tools-adapter' );
	}

	public function get_icon() {
		return 'eicon-counter';
	}

	public function get_categories() {
		return [ 'tools-adapter' ];
	}

	public function get_keywords() {
		return [ 'stats', 'compteur', 'chiffres', 'statistiques', 'counter' ];
	}

	public function get_style_depends() {
		return [ 'tools-adapter-stats' ];
	}

	public function get_script_depends() {
		return [ 'tools-adapter-stats' ];
	}

	protected function register_controls() {
		$this->start_controls_section( 'section_content', [ 'label' => esc_html__( 'Statistiques', 'tools-adapter' ) ] );

		$repeater = new Repeater();

		$repeater->add_control( 'number', [ 'label' => esc_html__( 'Nombre', 'tools-adapter' ), 'type' => Controls_Manager::NUMBER, 'default' => 100 ] );
		$repeater->add_control( 'decimals', [ 'label' => esc_html__( 'Décimales', 'tools-adapter' ), 'type' => Controls_Manager::NUMBER, 'min' => 0, 'max' => 2, 'default' => 0 ] );
		$repeater->add_control( 'prefix', [ 'label' => esc_html__( 'Préfixe', 'tools-adapter' ), 'type' => Controls_Manager::TEXT, 'default' => '' ] );
		$repeater->add_control( 'suffix', [ 'label' => esc_html__( 'Suffixe', 'tools-adapter' ), 'type' => Controls_Manager::TEXT, 'default' => '+' ] );
		$repeater->add_control( 'label', [ 'label' => esc_html__( 'Libellé', 'tools-adapter' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'Clients satisfaits', 'tools-adapter' ) ] );
		$repeater->add_control( 'icon', [ 'label' => esc_html__( 'Icône', 'tools-adapter' ), 'type' => Controls_Manager::ICONS ] );

		$this->add_control(
			'stats',
			[
				'label'       => esc_html__( 'Statistiques', 'tools-adapter' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => [
					[ 'number' => 500, 'suffix' => '+', 'label' => esc_html__( 'Clients satisfaits', 'tools-adapter' ) ],
					[ 'number' => 98, 'suffix' => '%', 'label' => esc_html__( 'Taux de satisfaction', 'tools-adapter' ) ],
					[ 'number' => 10, 'suffix' => ' ans', 'label' => esc_html__( "D'expérience", 'tools-adapter' ) ],
				],
				'title_field' => '{{{ number }}}{{{ suffix }}} — {{{ label }}}',
			]
		);

		$this->add_responsive_control(
			'columns',
			[
				'label'     => esc_html__( 'Colonnes', 'tools-adapter' ),
				'type'      => Controls_Manager::NUMBER,
				'min'       => 1,
				'max'       => 6,
				'default'   => 3,
				'tablet_default' => 2,
				'mobile_default' => 1,
				'selectors' => [ '{{WRAPPER}} .ta-stats' => '--ta-stats-cols: {{VALUE}};' ],
			]
		);

		$this->add_control(
			'duration',
			[
				'label'       => esc_html__( 'Durée de l\'animation (ms)', 'tools-adapter' ),
				'type'        => Controls_Manager::NUMBER,
				'min'         => 200,
				'max'         => 5000,
				'step'        => 100,
				'default'     => 2000,
			]
		);

		$this->end_controls_section();

		$this->start_controls_section( 'section_style', [ 'label' => esc_html__( 'Style', 'tools-adapter' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_responsive_control( 'items_gap', [ 'label' => esc_html__( 'Espacement', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'range' => [ 'px' => [ 'min' => 0, 'max' => 80 ] ], 'default' => [ 'size' => 32, 'unit' => 'px' ], 'selectors' => [ '{{WRAPPER}} .ta-stats' => '--ta-stats-gap: {{SIZE}}{{UNIT}};' ] ] );
		$this->add_control( 'text_align', [ 'label' => esc_html__( 'Alignement', 'tools-adapter' ), 'type' => Controls_Manager::CHOOSE, 'options' => [ 'left' => [ 'title' => esc_html__( 'Gauche', 'tools-adapter' ), 'icon' => 'eicon-text-align-left' ], 'center' => [ 'title' => esc_html__( 'Centre', 'tools-adapter' ), 'icon' => 'eicon-text-align-center' ], 'right' => [ 'title' => esc_html__( 'Droite', 'tools-adapter' ), 'icon' => 'eicon-text-align-right' ] ], 'default' => 'center', 'selectors' => [ '{{WRAPPER}} .ta-stats__item' => 'text-align: {{VALUE}};' ] ] );

		$this->add_control( 'icon_heading', [ 'label' => esc_html__( 'Icône', 'tools-adapter' ), 'type' => Controls_Manager::HEADING, 'separator' => 'before' ] );
		$this->add_control( 'icon_color', [ 'label' => esc_html__( 'Couleur', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#C9A84C', 'selectors' => [ '{{WRAPPER}} .ta-stats__icon' => 'color: {{VALUE}};' ] ] );
		$this->add_control( 'icon_size', [ 'label' => esc_html__( 'Taille', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'range' => [ 'px' => [ 'min' => 10, 'max' => 80 ] ], 'default' => [ 'size' => 32, 'unit' => 'px' ], 'selectors' => [ '{{WRAPPER}} .ta-stats__icon' => 'font-size: {{SIZE}}{{UNIT}};' ] ] );

		$this->add_control( 'number_heading', [ 'label' => esc_html__( 'Nombre', 'tools-adapter' ), 'type' => Controls_Manager::HEADING, 'separator' => 'before' ] );
		$this->add_control( 'number_color', [ 'label' => esc_html__( 'Couleur', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#1c1c1c', 'selectors' => [ '{{WRAPPER}} .ta-stats__number' => 'color: {{VALUE}};' ] ] );
		$this->add_group_control( Group_Control_Typography::get_type(), [ 'name' => 'number_typography', 'selector' => '{{WRAPPER}} .ta-stats__number', 'fields_options' => [ 'font_size' => [ 'default' => [ 'size' => 42, 'unit' => 'px' ] ], 'font_weight' => [ 'default' => '700' ] ] ] );

		$this->add_control( 'label_heading', [ 'label' => esc_html__( 'Libellé', 'tools-adapter' ), 'type' => Controls_Manager::HEADING, 'separator' => 'before' ] );
		$this->add_control( 'label_color', [ 'label' => esc_html__( 'Couleur', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#666666', 'selectors' => [ '{{WRAPPER}} .ta-stats__label' => 'color: {{VALUE}};' ] ] );
		$this->add_group_control( Group_Control_Typography::get_type(), [ 'name' => 'label_typography', 'selector' => '{{WRAPPER}} .ta-stats__label' ] );

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$stats    = $settings['stats'] ?? [];
		if ( empty( $stats ) ) {
			return;
		}
		?>
		<div class="ta-stats" data-ta-stats data-duration="<?php echo esc_attr( (string) intval( $settings['duration'] ?? 2000 ) ); ?>">
			<?php foreach ( $stats as $stat ) : ?>
				<div class="ta-stats__item">
					<?php if ( ! empty( $stat['icon']['value'] ) ) : ?>
						<span class="ta-stats__icon"><?php \Elementor\Icons_Manager::render_icon( $stat['icon'], [ 'aria-hidden' => 'true' ] ); ?></span>
					<?php endif; ?>
					<div class="ta-stats__number">
						<?php if ( ! empty( $stat['prefix'] ) ) : ?><span class="ta-stats__affix"><?php echo esc_html( $stat['prefix'] ); ?></span><?php endif; ?>
						<span
							data-count-to="<?php echo esc_attr( (string) floatval( $stat['number'] ?? 0 ) ); ?>"
							data-decimals="<?php echo esc_attr( (string) intval( $stat['decimals'] ?? 0 ) ); ?>"
						>0</span>
						<?php if ( ! empty( $stat['suffix'] ) ) : ?><span class="ta-stats__affix"><?php echo esc_html( $stat['suffix'] ); ?></span><?php endif; ?>
					</div>
					<?php if ( ! empty( $stat['label'] ) ) : ?>
						<p class="ta-stats__label"><?php echo esc_html( \tools_adapter_translate( $stat['label'] ) ); ?></p>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>
		<?php
	}
}
