<?php
namespace ToolsAdapter\Widgets;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use Elementor\Repeater;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Widget: Tableau de tarifs — une carte de plan (à dupliquer côte à côte).
 */
class Pricing_Table extends Widget_Base {

	public function get_name() {
		return 'tools-adapter-pricing-table';
	}

	public function get_title() {
		return esc_html__( 'Tableau de tarifs', 'tools-adapter' );
	}

	public function get_icon() {
		return 'eicon-price-table';
	}

	public function get_categories() {
		return [ 'tools-adapter' ];
	}

	public function get_keywords() {
		return [ 'tarifs', 'prix', 'plan', 'pricing', 'offre' ];
	}

	public function get_style_depends() {
		return [ 'tools-adapter-pricing-table' ];
	}

	protected function register_controls() {
		$this->start_controls_section( 'section_content', [ 'label' => esc_html__( 'Plan', 'tools-adapter' ) ] );

		$this->add_control( 'title', [ 'label' => esc_html__( 'Nom du plan', 'tools-adapter' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'Standard', 'tools-adapter' ) ] );
		$this->add_control( 'subtitle', [ 'label' => esc_html__( 'Sous-titre', 'tools-adapter' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'Idéal pour démarrer', 'tools-adapter' ) ] );
		$this->add_control( 'currency', [ 'label' => esc_html__( 'Devise', 'tools-adapter' ), 'type' => Controls_Manager::TEXT, 'default' => '€' ] );
		$this->add_control( 'price', [ 'label' => esc_html__( 'Prix', 'tools-adapter' ), 'type' => Controls_Manager::TEXT, 'default' => '29' ] );
		$this->add_control( 'period', [ 'label' => esc_html__( 'Période', 'tools-adapter' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( '/mois', 'tools-adapter' ) ] );

		$repeater = new Repeater();
		$repeater->add_control( 'text', [ 'label' => esc_html__( 'Texte', 'tools-adapter' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'Fonctionnalité incluse', 'tools-adapter' ) ] );
		$repeater->add_control( 'included', [ 'label' => esc_html__( 'Incluse', 'tools-adapter' ), 'type' => Controls_Manager::SWITCHER, 'return_value' => 'yes', 'default' => 'yes' ] );

		$this->add_control(
			'features',
			[
				'label'       => esc_html__( 'Fonctionnalités', 'tools-adapter' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => [
					[ 'text' => esc_html__( 'Support par email', 'tools-adapter' ) ],
					[ 'text' => esc_html__( '10 Go de stockage', 'tools-adapter' ) ],
					[ 'text' => esc_html__( 'Accès API', 'tools-adapter' ), 'included' => '' ],
				],
				'title_field' => '{{{ text }}}',
			]
		);

		$this->add_control( 'button_text', [ 'label' => esc_html__( 'Texte du bouton', 'tools-adapter' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'Choisir ce plan', 'tools-adapter' ), 'separator' => 'before' ] );
		$this->add_control( 'button_link', [ 'label' => esc_html__( 'Lien du bouton', 'tools-adapter' ), 'type' => Controls_Manager::URL, 'default' => [ 'url' => '#' ] ] );

		$this->add_control( 'featured', [ 'label' => esc_html__( 'Mettre en avant', 'tools-adapter' ), 'type' => Controls_Manager::SWITCHER, 'return_value' => 'yes', 'default' => '', 'separator' => 'before' ] );
		$this->add_control( 'ribbon_text', [ 'label' => esc_html__( 'Texte du ruban', 'tools-adapter' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'Populaire', 'tools-adapter' ), 'condition' => [ 'featured' => 'yes' ] ] );

		$this->end_controls_section();

		$this->start_controls_section( 'section_style_card', [ 'label' => esc_html__( 'Carte', 'tools-adapter' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_control( 'card_bg', [ 'label' => esc_html__( 'Fond', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#ffffff', 'selectors' => [ '{{WRAPPER}} .ta-pricing' => 'background-color: {{VALUE}};' ] ] );
		$this->add_control( 'card_bg_featured', [ 'label' => esc_html__( 'Fond (mis en avant)', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#1c1c1c', 'condition' => [ 'featured' => 'yes' ], 'selectors' => [ '{{WRAPPER}} .ta-pricing.is-featured' => 'background-color: {{VALUE}};' ] ] );
		$this->add_responsive_control( 'card_padding', [ 'label' => esc_html__( 'Espacement interne', 'tools-adapter' ), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => [ 'px' ], 'default' => [ 'top' => '40', 'right' => '32', 'bottom' => '40', 'left' => '32', 'unit' => 'px' ], 'selectors' => [ '{{WRAPPER}} .ta-pricing' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ] ] );
		$this->add_control( 'card_radius', [ 'label' => esc_html__( 'Arrondi', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'range' => [ 'px' => [ 'min' => 0, 'max' => 60 ] ], 'default' => [ 'size' => 16, 'unit' => 'px' ], 'selectors' => [ '{{WRAPPER}} .ta-pricing' => 'border-radius: {{SIZE}}{{UNIT}};' ] ] );
		$this->add_group_control( Group_Control_Border::get_type(), [ 'name' => 'card_border', 'selector' => '{{WRAPPER}} .ta-pricing' ] );
		$this->add_group_control( Group_Control_Box_Shadow::get_type(), [ 'name' => 'card_shadow', 'selector' => '{{WRAPPER}} .ta-pricing' ] );

		$this->end_controls_section();

		$this->start_controls_section( 'section_style_text', [ 'label' => esc_html__( 'Texte', 'tools-adapter' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_control( 'title_color', [ 'label' => esc_html__( 'Couleur titre', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#1c1c1c', 'selectors' => [ '{{WRAPPER}} .ta-pricing__title' => 'color: {{VALUE}};' ] ] );
		$this->add_control( 'subtitle_color', [ 'label' => esc_html__( 'Couleur sous-titre', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#888888', 'selectors' => [ '{{WRAPPER}} .ta-pricing__subtitle' => 'color: {{VALUE}};' ] ] );
		$this->add_control( 'price_color', [ 'label' => esc_html__( 'Couleur prix', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#C9A84C', 'separator' => 'before', 'selectors' => [ '{{WRAPPER}} .ta-pricing__price' => 'color: {{VALUE}};' ] ] );
		$this->add_control( 'period_color', [ 'label' => esc_html__( 'Couleur période', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#888888', 'selectors' => [ '{{WRAPPER}} .ta-pricing__period' => 'color: {{VALUE}};' ] ] );
		$this->add_control( 'feature_color', [ 'label' => esc_html__( 'Couleur des fonctionnalités', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#444444', 'separator' => 'before', 'selectors' => [ '{{WRAPPER}} .ta-pricing__feature' => 'color: {{VALUE}};' ] ] );
		$this->add_control( 'feature_included_color', [ 'label' => esc_html__( 'Couleur icône incluse', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#3aa76d', 'selectors' => [ '{{WRAPPER}} .ta-pricing__feature-icon--yes' => 'color: {{VALUE}};' ] ] );
		$this->add_control( 'feature_excluded_color', [ 'label' => esc_html__( 'Couleur icône exclue', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#cccccc', 'selectors' => [ '{{WRAPPER}} .ta-pricing__feature-icon--no' => 'color: {{VALUE}};' ] ] );

		$this->end_controls_section();

		$this->start_controls_section( 'section_style_button', [ 'label' => esc_html__( 'Bouton', 'tools-adapter' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_control( 'button_color', [ 'label' => esc_html__( 'Texte', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#ffffff', 'selectors' => [ '{{WRAPPER}} .ta-pricing__btn' => 'color: {{VALUE}};' ] ] );
		$this->add_control( 'button_bg', [ 'label' => esc_html__( 'Fond', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#1c1c1c', 'selectors' => [ '{{WRAPPER}} .ta-pricing__btn' => 'background-color: {{VALUE}};' ] ] );
		$this->add_control( 'button_radius', [ 'label' => esc_html__( 'Arrondi', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'range' => [ 'px' => [ 'min' => 0, 'max' => 50 ] ], 'default' => [ 'size' => 6, 'unit' => 'px' ], 'selectors' => [ '{{WRAPPER}} .ta-pricing__btn' => 'border-radius: {{SIZE}}{{UNIT}};' ] ] );

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$featured = 'yes' === ( $settings['featured'] ?? '' );
		$url      = $settings['button_link']['url'] ?? '#';
		?>
		<div class="ta-pricing<?php echo $featured ? ' is-featured' : ''; ?>">
			<?php if ( $featured && ! empty( $settings['ribbon_text'] ) ) : ?>
				<span class="ta-pricing__ribbon"><?php echo esc_html( \tools_adapter_translate( $settings['ribbon_text'] ) ); ?></span>
			<?php endif; ?>

			<?php if ( ! empty( $settings['title'] ) ) : ?><p class="ta-pricing__title"><?php echo esc_html( \tools_adapter_translate( $settings['title'] ) ); ?></p><?php endif; ?>
			<?php if ( ! empty( $settings['subtitle'] ) ) : ?><p class="ta-pricing__subtitle"><?php echo esc_html( \tools_adapter_translate( $settings['subtitle'] ) ); ?></p><?php endif; ?>

			<div class="ta-pricing__price-row">
				<?php if ( ! empty( $settings['currency'] ) ) : ?><span class="ta-pricing__currency"><?php echo esc_html( $settings['currency'] ); ?></span><?php endif; ?>
				<span class="ta-pricing__price"><?php echo esc_html( $settings['price'] ?? '' ); ?></span>
				<?php if ( ! empty( $settings['period'] ) ) : ?><span class="ta-pricing__period"><?php echo esc_html( \tools_adapter_translate( $settings['period'] ) ); ?></span><?php endif; ?>
			</div>

			<?php if ( ! empty( $settings['features'] ) ) : ?>
				<ul class="ta-pricing__features">
					<?php foreach ( $settings['features'] as $feature ) :
						$included = 'yes' === ( $feature['included'] ?? 'yes' );
						?>
						<li class="ta-pricing__feature<?php echo $included ? '' : ' ta-pricing__feature--excluded'; ?>">
							<span class="ta-pricing__feature-icon <?php echo $included ? 'ta-pricing__feature-icon--yes' : 'ta-pricing__feature-icon--no'; ?>" aria-hidden="true"><?php echo $included ? '✓' : '✕'; ?></span>
							<span><?php echo esc_html( \tools_adapter_translate( $feature['text'] ?? '' ) ); ?></span>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>

			<?php if ( ! empty( $settings['button_text'] ) ) : ?>
				<a class="ta-pricing__btn" href="<?php echo esc_url( $url ); ?>"<?php echo ! empty( $settings['button_link']['is_external'] ) ? ' target="_blank"' : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
					<?php echo esc_html( \tools_adapter_translate( $settings['button_text'] ) ); ?>
				</a>
			<?php endif; ?>
		</div>
		<?php
	}
}
