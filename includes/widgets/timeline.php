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
 * Widget: Timeline — frise chronologique verticale.
 */
class Timeline extends Widget_Base {

	public function get_name() {
		return 'tools-adapter-timeline';
	}

	public function get_title() {
		return esc_html__( 'Timeline', 'tools-adapter' );
	}

	public function get_icon() {
		return 'eicon-time-line';
	}

	public function get_categories() {
		return [ 'tools-adapter' ];
	}

	public function get_keywords() {
		return [ 'timeline', 'frise', 'chronologie', 'historique', 'étapes' ];
	}

	public function get_style_depends() {
		return [ 'tools-adapter-timeline' ];
	}

	protected function register_controls() {
		$this->start_controls_section( 'section_content', [ 'label' => esc_html__( 'Étapes', 'tools-adapter' ) ] );

		$repeater = new Repeater();
		$repeater->add_control( 'date', [ 'label' => esc_html__( 'Date / Label', 'tools-adapter' ), 'type' => Controls_Manager::TEXT, 'default' => '2024' ] );
		$repeater->add_control( 'title', [ 'label' => esc_html__( 'Titre', 'tools-adapter' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'Titre de l\'étape', 'tools-adapter' ) ] );
		$repeater->add_control( 'description', [ 'label' => esc_html__( 'Description', 'tools-adapter' ), 'type' => Controls_Manager::TEXTAREA, 'rows' => 3, 'default' => esc_html__( 'Description de cette étape.', 'tools-adapter' ) ] );
		$repeater->add_control( 'icon', [ 'label' => esc_html__( 'Icône', 'tools-adapter' ), 'type' => Controls_Manager::ICONS, 'default' => [ 'value' => 'fas fa-flag', 'library' => 'fa-solid' ] ] );

		$this->add_control(
			'items',
			[
				'label'       => esc_html__( 'Étapes', 'tools-adapter' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => [
					[ 'date' => '2022', 'title' => esc_html__( 'Création de l\'entreprise', 'tools-adapter' ), 'description' => esc_html__( 'Les débuts de l\'aventure.', 'tools-adapter' ) ],
					[ 'date' => '2023', 'title' => esc_html__( 'Ouverture de la boutique en ligne', 'tools-adapter' ), 'description' => esc_html__( 'Lancement de notre e-shop.', 'tools-adapter' ) ],
					[ 'date' => '2024', 'title' => esc_html__( '+500 clients satisfaits', 'tools-adapter' ), 'description' => esc_html__( 'Merci pour votre confiance !', 'tools-adapter' ) ],
				],
				'title_field' => '{{{ date }}} — {{{ title }}}',
			]
		);

		$this->add_control(
			'layout',
			[
				'label'        => esc_html__( 'Disposition', 'tools-adapter' ),
				'type'         => Controls_Manager::SELECT,
				'default'      => 'alternate',
				'options'      => [
					'alternate' => esc_html__( 'Alternée (gauche/droite)', 'tools-adapter' ),
					'single'    => esc_html__( 'Colonne unique', 'tools-adapter' ),
				],
				'prefix_class' => 'ta-timeline--',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section( 'section_style', [ 'label' => esc_html__( 'Style', 'tools-adapter' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_control( 'line_color', [ 'label' => esc_html__( 'Couleur de la ligne', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#e5e5e5', 'selectors' => [ '{{WRAPPER}} .ta-timeline__line' => 'background-color: {{VALUE}};' ] ] );

		$this->add_control( 'icon_heading', [ 'label' => esc_html__( 'Icône', 'tools-adapter' ), 'type' => Controls_Manager::HEADING, 'separator' => 'before' ] );
		$this->add_control( 'icon_color', [ 'label' => esc_html__( 'Couleur', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#ffffff', 'selectors' => [ '{{WRAPPER}} .ta-timeline__icon' => 'color: {{VALUE}};' ] ] );
		$this->add_control( 'icon_bg', [ 'label' => esc_html__( 'Fond', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#C9A84C', 'selectors' => [ '{{WRAPPER}} .ta-timeline__icon' => 'background-color: {{VALUE}};' ] ] );
		$this->add_control( 'icon_size', [ 'label' => esc_html__( 'Taille du cercle', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'range' => [ 'px' => [ 'min' => 24, 'max' => 80 ] ], 'default' => [ 'size' => 44, 'unit' => 'px' ], 'selectors' => [ '{{WRAPPER}} .ta-timeline__icon' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};' ] ] );

		$this->add_control( 'date_heading', [ 'label' => esc_html__( 'Date', 'tools-adapter' ), 'type' => Controls_Manager::HEADING, 'separator' => 'before' ] );
		$this->add_control( 'date_color', [ 'label' => esc_html__( 'Couleur', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#C9A84C', 'selectors' => [ '{{WRAPPER}} .ta-timeline__date' => 'color: {{VALUE}};' ] ] );

		$this->add_control( 'title_heading', [ 'label' => esc_html__( 'Titre', 'tools-adapter' ), 'type' => Controls_Manager::HEADING, 'separator' => 'before' ] );
		$this->add_control( 'title_color', [ 'label' => esc_html__( 'Couleur', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#1c1c1c', 'selectors' => [ '{{WRAPPER}} .ta-timeline__title' => 'color: {{VALUE}};' ] ] );
		$this->add_group_control( Group_Control_Typography::get_type(), [ 'name' => 'title_typography', 'selector' => '{{WRAPPER}} .ta-timeline__title' ] );

		$this->add_control( 'description_heading', [ 'label' => esc_html__( 'Description', 'tools-adapter' ), 'type' => Controls_Manager::HEADING, 'separator' => 'before' ] );
		$this->add_control( 'description_color', [ 'label' => esc_html__( 'Couleur', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#666666', 'selectors' => [ '{{WRAPPER}} .ta-timeline__description' => 'color: {{VALUE}};' ] ] );

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$items    = $settings['items'] ?? [];
		if ( empty( $items ) ) {
			return;
		}
		?>
		<div class="ta-timeline">
			<span class="ta-timeline__line" aria-hidden="true"></span>
			<?php foreach ( $items as $index => $item ) : ?>
				<div class="ta-timeline__item ta-timeline__item--<?php echo 0 === $index % 2 ? 'left' : 'right'; ?>">
					<span class="ta-timeline__icon" aria-hidden="true">
						<?php if ( ! empty( $item['icon']['value'] ) ) : ?>
							<?php \Elementor\Icons_Manager::render_icon( $item['icon'], [ 'aria-hidden' => 'true' ] ); ?>
						<?php endif; ?>
					</span>
					<div class="ta-timeline__content">
						<?php if ( ! empty( $item['date'] ) ) : ?><p class="ta-timeline__date"><?php echo esc_html( \tools_adapter_translate( $item['date'] ) ); ?></p><?php endif; ?>
						<?php if ( ! empty( $item['title'] ) ) : ?><p class="ta-timeline__title"><?php echo esc_html( \tools_adapter_translate( $item['title'] ) ); ?></p><?php endif; ?>
						<?php if ( ! empty( $item['description'] ) ) : ?><p class="ta-timeline__description"><?php echo esc_html( \tools_adapter_translate( $item['description'] ) ); ?></p><?php endif; ?>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
		<?php
	}
}
