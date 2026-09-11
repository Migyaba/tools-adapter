<?php
namespace ToolsAdapter\Widgets;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Widget: Produits récemment consultés — historique client-side (localStorage) + rendu AJAX.
 */
class Recently_Viewed extends Widget_Base {

	public function get_name() {
		return 'tools-adapter-recently-viewed';
	}

	public function get_title() {
		return esc_html__( 'Produits récemment consultés', 'tools-adapter' );
	}

	public function get_icon() {
		return 'eicon-history';
	}

	public function get_categories() {
		return [ 'tools-adapter', 'woocommerce-elements' ];
	}

	public function get_keywords() {
		return [ 'récemment consulté', 'recently viewed', 'historique' ];
	}

	public function get_style_depends() {
		return [ 'tools-adapter-products', 'tools-adapter-recently-viewed' ];
	}

	public function get_script_depends() {
		return [ 'tools-adapter-recently-viewed' ];
	}

	protected function register_controls() {
		$this->start_controls_section( 'section_content', [ 'label' => esc_html__( 'Réglages', 'tools-adapter' ) ] );

		$this->add_control( 'title', [ 'label' => esc_html__( 'Titre', 'tools-adapter' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'Récemment consultés', 'tools-adapter' ) ] );
		$this->add_control( 'limit', [ 'label' => esc_html__( 'Nombre de produits', 'tools-adapter' ), 'type' => Controls_Manager::NUMBER, 'default' => 4, 'min' => 1, 'max' => 12 ] );
		$this->add_control( 'exclude_current', [ 'label' => esc_html__( 'Exclure le produit courant', 'tools-adapter' ), 'type' => Controls_Manager::SWITCHER, 'return_value' => 'yes', 'default' => 'yes' ] );
		$this->add_control( 'hide_if_empty', [ 'label' => esc_html__( 'Masquer si aucun historique', 'tools-adapter' ), 'type' => Controls_Manager::SWITCHER, 'return_value' => 'yes', 'default' => 'yes' ] );
		$this->add_control( 'empty_text', [ 'label' => esc_html__( 'Message si vide', 'tools-adapter' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'Vous n\'avez pas encore consulté de produit.', 'tools-adapter' ), 'condition' => [ 'hide_if_empty' => '' ] ] );

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
				'selectors'      => [ '{{WRAPPER}} .ta-recently-viewed__grid' => '--ta-cols: {{VALUE}};' ],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section( 'section_style', [ 'label' => esc_html__( 'Style', 'tools-adapter' ), 'tab' => Controls_Manager::TAB_STYLE ] );
		$this->add_control( 'title_color', [ 'label' => esc_html__( 'Couleur titre', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#1c1c1c', 'selectors' => [ '{{WRAPPER}} .ta-recently-viewed__title' => 'color: {{VALUE}};' ] ] );
		$this->end_controls_section();
	}

	protected function render() {
		if ( ! class_exists( 'WooCommerce' ) ) {
			echo '<p>' . esc_html__( 'WooCommerce est requis pour ce widget.', 'tools-adapter' ) . '</p>';
			return;
		}

		$settings       = $this->get_settings_for_display();
		$current_id     = function_exists( 'is_product' ) && is_product() ? get_the_ID() : 0;
		$card_settings  = [
			'show_image'       => 'yes',
			'show_title'       => 'yes',
			'show_price'       => 'yes',
			'show_add_to_cart' => 'yes',
		];
		?>
		<div
			class="ta-recently-viewed"
			data-ta-recently-viewed
			data-limit="<?php echo esc_attr( (string) absint( $settings['limit'] ?? 4 ) ); ?>"
			data-exclude="<?php echo esc_attr( 'yes' === ( $settings['exclude_current'] ?? '' ) ? (string) $current_id : '0' ); ?>"
			data-hide-empty="<?php echo esc_attr( 'yes' === ( $settings['hide_if_empty'] ?? '' ) ? '1' : '0' ); ?>"
			data-card-settings="<?php echo esc_attr( wp_json_encode( $card_settings ) ); ?>"
			hidden
		>
			<?php if ( ! empty( $settings['title'] ) ) : ?>
				<p class="ta-recently-viewed__title"><?php echo esc_html( \tools_adapter_translate( $settings['title'] ) ); ?></p>
			<?php endif; ?>
			<div class="ta-products ta-recently-viewed__grid" data-recently-viewed-grid></div>
			<p class="ta-recently-viewed__empty" data-recently-viewed-empty hidden><?php echo esc_html( \tools_adapter_translate( $settings['empty_text'] ?? '' ) ); ?></p>
		</div>
		<?php
	}
}
