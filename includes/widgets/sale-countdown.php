<?php
namespace ToolsAdapter\Widgets;

use Elementor\Controls_Manager;
use ToolsAdapter\Base_Widget;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Widget: Compte à rebours promo — date fixe ou fin de promo du produit courant.
 */
class Sale_Countdown extends Base_Widget {

	public function get_name() {
		return 'tools-adapter-sale-countdown';
	}

	public function get_title() {
		return esc_html__( 'Compte à rebours promo', 'tools-adapter' );
	}

	public function get_icon() {
		return 'eicon-countdown';
	}

	public function get_categories() {
		return [ 'tools-adapter', 'woocommerce-elements' ];
	}

	public function get_keywords() {
		return [ 'compte à rebours', 'countdown', 'promo', 'urgence' ];
	}

	public function get_style_depends() {
		return [ 'tools-adapter-sale-countdown' ];
	}

	public function get_script_depends() {
		return [ 'tools-adapter-countdown' ];
	}

	protected function register_controls() {
		$this->start_controls_section( 'section_content', [ 'label' => esc_html__( 'Réglages', 'tools-adapter' ) ] );

		$this->add_control(
			'source',
			[
				'label'   => esc_html__( 'Source de la date', 'tools-adapter' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'fixed',
				'options' => [
					'fixed'          => esc_html__( 'Date fixe', 'tools-adapter' ),
					'product_sale'   => esc_html__( 'Fin de promo du produit courant', 'tools-adapter' ),
				],
			]
		);

		$this->add_control(
			'end_date',
			[
				'label'     => esc_html__( 'Date et heure de fin', 'tools-adapter' ),
				'type'      => Controls_Manager::DATE_TIME,
				'default'   => gmdate( 'Y-m-d H:i', strtotime( '+7 days' ) ),
				'condition' => [ 'source' => 'fixed' ],
			]
		);

		$this->add_control( 'label_days', [ 'label' => esc_html__( 'Libellé jours', 'tools-adapter' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'Jours', 'tools-adapter' ), 'separator' => 'before' ] );
		$this->add_control( 'label_hours', [ 'label' => esc_html__( 'Libellé heures', 'tools-adapter' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'Heures', 'tools-adapter' ) ] );
		$this->add_control( 'label_minutes', [ 'label' => esc_html__( 'Libellé minutes', 'tools-adapter' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'Min', 'tools-adapter' ) ] );
		$this->add_control( 'label_seconds', [ 'label' => esc_html__( 'Libellé secondes', 'tools-adapter' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'Sec', 'tools-adapter' ) ] );

		$this->add_control( 'expired_text', [ 'label' => esc_html__( 'Texte à expiration', 'tools-adapter' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'Offre expirée', 'tools-adapter' ), 'separator' => 'before' ] );
		$this->add_control( 'hide_on_expire', [ 'label' => esc_html__( 'Masquer le widget à expiration', 'tools-adapter' ), 'type' => Controls_Manager::SWITCHER, 'return_value' => 'yes', 'default' => '' ] );

		$this->end_controls_section();

		$this->start_controls_section( 'section_style', [ 'label' => esc_html__( 'Style', 'tools-adapter' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_responsive_control( 'unit_gap', [ 'label' => esc_html__( 'Espacement', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'range' => [ 'px' => [ 'min' => 0, 'max' => 40 ] ], 'default' => [ 'size' => 12, 'unit' => 'px' ], 'selectors' => [ '{{WRAPPER}} .ta-countdown' => '--ta-countdown-gap: {{SIZE}}{{UNIT}};' ] ] );
		$this->add_control( 'unit_bg', [ 'label' => esc_html__( 'Fond des blocs', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#1c1c1c', 'selectors' => [ '{{WRAPPER}} .ta-countdown__unit' => 'background-color: {{VALUE}};' ] ] );
		$this->add_control( 'unit_radius', [ 'label' => esc_html__( 'Arrondi des blocs', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'range' => [ 'px' => [ 'min' => 0, 'max' => 30 ] ], 'default' => [ 'size' => 8, 'unit' => 'px' ], 'selectors' => [ '{{WRAPPER}} .ta-countdown__unit' => 'border-radius: {{SIZE}}{{UNIT}};' ] ] );
		$this->add_control( 'value_color', [ 'label' => esc_html__( 'Couleur du chiffre', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#ffffff', 'separator' => 'before', 'selectors' => [ '{{WRAPPER}} .ta-countdown__value' => 'color: {{VALUE}};' ] ] );
		$this->add_group_control( Group_Control_Typography::get_type(), [ 'name' => 'value_typography', 'selector' => '{{WRAPPER}} .ta-countdown__value' ] );
		$this->add_control( 'label_color', [ 'label' => esc_html__( 'Couleur du libellé', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#C9A84C', 'separator' => 'before', 'selectors' => [ '{{WRAPPER}} .ta-countdown__label' => 'color: {{VALUE}};' ] ] );

		$this->end_controls_section();
	}

	protected function render() {
		$settings   = $this->get_settings_for_display();
		$target_ts  = 0;

		if ( 'product_sale' === ( $settings['source'] ?? 'fixed' ) ) {
			if ( class_exists( 'WooCommerce' ) && function_exists( 'is_product' ) && is_product() ) {
				$product = wc_get_product( get_the_ID() );
				if ( $product && $product->get_date_on_sale_to() ) {
					$target_ts = $product->get_date_on_sale_to()->getTimestamp() * 1000;
				}
			}
		} else {
			$end_date = $settings['end_date'] ?? '';
			if ( $end_date ) {
				$timestamp = strtotime( $end_date );
				if ( $timestamp ) {
					$target_ts = $timestamp * 1000;
				}
			}
		}

		if ( ! $target_ts ) {
			if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
				echo '<p>' . esc_html__( 'Aucune date de fin valide (produit sans promo programmée, ou date fixe manquante).', 'tools-adapter' ) . '</p>';
			}
			return;
		}
		?>
		<div
			class="ta-countdown"
			data-ta-countdown
			data-target="<?php echo esc_attr( (string) $target_ts ); ?>"
			data-hide-on-expire="<?php echo 'yes' === ( $settings['hide_on_expire'] ?? '' ) ? '1' : '0'; ?>"
			data-expired-text="<?php echo esc_attr( \tools_adapter_translate( $settings['expired_text'] ?? '' ) ); ?>"
		>
			<div class="ta-countdown__unit">
				<span class="ta-countdown__value" data-countdown-days>00</span>
				<span class="ta-countdown__label"><?php echo esc_html( \tools_adapter_translate( $settings['label_days'] ?? '' ) ); ?></span>
			</div>
			<div class="ta-countdown__unit">
				<span class="ta-countdown__value" data-countdown-hours>00</span>
				<span class="ta-countdown__label"><?php echo esc_html( \tools_adapter_translate( $settings['label_hours'] ?? '' ) ); ?></span>
			</div>
			<div class="ta-countdown__unit">
				<span class="ta-countdown__value" data-countdown-minutes>00</span>
				<span class="ta-countdown__label"><?php echo esc_html( \tools_adapter_translate( $settings['label_minutes'] ?? '' ) ); ?></span>
			</div>
			<div class="ta-countdown__unit">
				<span class="ta-countdown__value" data-countdown-seconds>00</span>
				<span class="ta-countdown__label"><?php echo esc_html( \tools_adapter_translate( $settings['label_seconds'] ?? '' ) ); ?></span>
			</div>
		</div>
		<?php
	}
}
