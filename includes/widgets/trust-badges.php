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
 * Widget: Bloc réassurance — icônes + textes (livraison, paiement sécurisé, retours...).
 */
class Trust_Badges extends Widget_Base {

	public function get_name() {
		return 'tools-adapter-trust-badges';
	}

	public function get_title() {
		return esc_html__( 'Bloc réassurance', 'tools-adapter' );
	}

	public function get_icon() {
		return 'eicon-badge';
	}

	public function get_categories() {
		return [ 'tools-adapter', 'woocommerce-elements' ];
	}

	public function get_keywords() {
		return [ 'réassurance', 'trust', 'livraison', 'paiement', 'garantie', 'retour' ];
	}

	public function get_style_depends() {
		return [ 'tools-adapter-trust-badges' ];
	}

	protected function register_controls() {
		$this->start_controls_section( 'section_content', [ 'label' => esc_html__( 'Éléments', 'tools-adapter' ) ] );

		$repeater = new Repeater();
		$repeater->add_control( 'icon', [ 'label' => esc_html__( 'Icône', 'tools-adapter' ), 'type' => Controls_Manager::ICONS, 'default' => [ 'value' => 'fas fa-truck', 'library' => 'fa-solid' ] ] );
		$repeater->add_control( 'title', [ 'label' => esc_html__( 'Titre', 'tools-adapter' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'Livraison rapide', 'tools-adapter' ) ] );
		$repeater->add_control( 'description', [ 'label' => esc_html__( 'Description', 'tools-adapter' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'Sous 48h partout en France', 'tools-adapter' ) ] );

		$this->add_control(
			'badges',
			[
				'label'       => esc_html__( 'Badges', 'tools-adapter' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => [
					[ 'icon' => [ 'value' => 'fas fa-truck', 'library' => 'fa-solid' ], 'title' => esc_html__( 'Livraison rapide', 'tools-adapter' ), 'description' => esc_html__( 'Sous 48h partout en France', 'tools-adapter' ) ],
					[ 'icon' => [ 'value' => 'fas fa-lock', 'library' => 'fa-solid' ], 'title' => esc_html__( 'Paiement sécurisé', 'tools-adapter' ), 'description' => esc_html__( 'Transactions cryptées SSL', 'tools-adapter' ) ],
					[ 'icon' => [ 'value' => 'fas fa-rotate-left', 'library' => 'fa-solid' ], 'title' => esc_html__( 'Retours gratuits', 'tools-adapter' ), 'description' => esc_html__( '30 jours pour changer d\'avis', 'tools-adapter' ) ],
					[ 'icon' => [ 'value' => 'fas fa-headset', 'library' => 'fa-solid' ], 'title' => esc_html__( 'Support client', 'tools-adapter' ), 'description' => esc_html__( 'Une équipe à votre écoute', 'tools-adapter' ) ],
				],
				'title_field' => '{{{ title }}}',
			]
		);

		$this->add_responsive_control(
			'columns',
			[
				'label'          => esc_html__( 'Colonnes', 'tools-adapter' ),
				'type'           => Controls_Manager::NUMBER,
				'min'            => 1,
				'max'            => 6,
				'default'        => 4,
				'tablet_default' => 2,
				'mobile_default' => 1,
				'selectors'      => [ '{{WRAPPER}} .ta-trust' => '--ta-trust-cols: {{VALUE}};' ],
			]
		);

		$this->add_control(
			'layout',
			[
				'label'        => esc_html__( 'Disposition', 'tools-adapter' ),
				'type'         => Controls_Manager::CHOOSE,
				'options'      => [
					'row'    => [ 'title' => esc_html__( 'Icône à gauche', 'tools-adapter' ), 'icon' => 'eicon-navigation-horizontal' ],
					'column' => [ 'title' => esc_html__( 'Icône au-dessus', 'tools-adapter' ), 'icon' => 'eicon-navigation-vertical' ],
				],
				'default'      => 'row',
				'prefix_class' => 'ta-trust-layout--',
			]
		);

		$this->add_control(
			'show_dividers',
			[
				'label'        => esc_html__( 'Séparateurs verticaux', 'tools-adapter' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section( 'section_style', [ 'label' => esc_html__( 'Style', 'tools-adapter' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_responsive_control( 'items_gap', [ 'label' => esc_html__( 'Espacement', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'range' => [ 'px' => [ 'min' => 0, 'max' => 80 ] ], 'default' => [ 'size' => 24, 'unit' => 'px' ], 'selectors' => [ '{{WRAPPER}} .ta-trust' => '--ta-trust-gap: {{SIZE}}{{UNIT}};' ] ] );

		$this->add_control( 'icon_heading', [ 'label' => esc_html__( 'Icône', 'tools-adapter' ), 'type' => Controls_Manager::HEADING, 'separator' => 'before' ] );
		$this->add_control( 'icon_color', [ 'label' => esc_html__( 'Couleur', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#C9A84C', 'selectors' => [ '{{WRAPPER}} .ta-trust__icon' => 'color: {{VALUE}};' ] ] );
		$this->add_control( 'icon_bg', [ 'label' => esc_html__( 'Fond', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .ta-trust__icon' => 'background-color: {{VALUE}};' ] ] );
		$this->add_control( 'icon_size', [ 'label' => esc_html__( 'Taille', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'range' => [ 'px' => [ 'min' => 10, 'max' => 60 ] ], 'default' => [ 'size' => 26, 'unit' => 'px' ], 'selectors' => [ '{{WRAPPER}} .ta-trust__icon' => 'font-size: {{SIZE}}{{UNIT}};' ] ] );
		$this->add_control( 'icon_box_size', [ 'label' => esc_html__( 'Taille du cercle', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'range' => [ 'px' => [ 'min' => 20, 'max' => 120 ] ], 'default' => [ 'size' => 56, 'unit' => 'px' ], 'selectors' => [ '{{WRAPPER}} .ta-trust__icon' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};' ] ] );

		$this->add_control( 'title_heading', [ 'label' => esc_html__( 'Titre', 'tools-adapter' ), 'type' => Controls_Manager::HEADING, 'separator' => 'before' ] );
		$this->add_control( 'title_color', [ 'label' => esc_html__( 'Couleur', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#1c1c1c', 'selectors' => [ '{{WRAPPER}} .ta-trust__title' => 'color: {{VALUE}};' ] ] );
		$this->add_group_control( Group_Control_Typography::get_type(), [ 'name' => 'title_typography', 'selector' => '{{WRAPPER}} .ta-trust__title' ] );

		$this->add_control( 'description_heading', [ 'label' => esc_html__( 'Description', 'tools-adapter' ), 'type' => Controls_Manager::HEADING, 'separator' => 'before' ] );
		$this->add_control( 'description_color', [ 'label' => esc_html__( 'Couleur', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#777777', 'selectors' => [ '{{WRAPPER}} .ta-trust__description' => 'color: {{VALUE}};' ] ] );
		$this->add_group_control( Group_Control_Typography::get_type(), [ 'name' => 'description_typography', 'selector' => '{{WRAPPER}} .ta-trust__description' ] );

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$badges   = $settings['badges'] ?? [];
		if ( empty( $badges ) ) {
			return;
		}
		?>
		<div class="ta-trust<?php echo 'yes' === ( $settings['show_dividers'] ?? '' ) ? ' ta-trust--dividers' : ''; ?>">
			<?php foreach ( $badges as $badge ) : ?>
				<div class="ta-trust__item">
					<?php if ( ! empty( $badge['icon']['value'] ) ) : ?>
						<span class="ta-trust__icon"><?php \Elementor\Icons_Manager::render_icon( $badge['icon'], [ 'aria-hidden' => 'true' ] ); ?></span>
					<?php endif; ?>
					<div class="ta-trust__text">
						<?php if ( ! empty( $badge['title'] ) ) : ?>
							<p class="ta-trust__title"><?php echo esc_html( \tools_adapter_translate( $badge['title'] ) ); ?></p>
						<?php endif; ?>
						<?php if ( ! empty( $badge['description'] ) ) : ?>
							<p class="ta-trust__description"><?php echo esc_html( \tools_adapter_translate( $badge['description'] ) ); ?></p>
						<?php endif; ?>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
		<?php
	}
}
