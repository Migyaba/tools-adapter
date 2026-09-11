<?php
namespace ToolsAdapter\Widgets;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Widget: Barre de stock/urgence — pour pages produit WooCommerce.
 */
class Stock_Urgency extends Widget_Base {

	public function get_name() {
		return 'tools-adapter-stock-urgency';
	}

	public function get_title() {
		return esc_html__( 'Barre de stock / urgence', 'tools-adapter' );
	}

	public function get_icon() {
		return 'eicon-skill-bar';
	}

	public function get_categories() {
		return [ 'tools-adapter', 'woocommerce-elements' ];
	}

	public function get_keywords() {
		return [ 'stock', 'urgence', 'rupture', 'quantité restante' ];
	}

	public function get_style_depends() {
		return [ 'tools-adapter-stock-urgency' ];
	}

	protected function register_controls() {
		$this->start_controls_section( 'section_content', [ 'label' => esc_html__( 'Réglages', 'tools-adapter' ) ] );

		$this->add_control(
			'threshold',
			[
				'label'       => esc_html__( 'Seuil d\'affichage', 'tools-adapter' ),
				'description' => esc_html__( 'La barre ne s\'affiche que si le stock est inférieur ou égal à ce nombre.', 'tools-adapter' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 10,
				'min'         => 1,
			]
		);

		$this->add_control(
			'reference_stock',
			[
				'label'       => esc_html__( 'Stock de référence (100%)', 'tools-adapter' ),
				'description' => esc_html__( 'Utilisé uniquement pour calculer le remplissage de la barre de progression.', 'tools-adapter' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 20,
				'min'         => 1,
			]
		);

		$this->add_control(
			'text_template',
			[
				'label'       => esc_html__( 'Texte (utiliser {stock})', 'tools-adapter' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Il ne reste plus que {stock} en stock — dépêchez-vous !', 'tools-adapter' ),
			]
		);

		$this->add_control( 'show_progress_bar', [ 'label' => esc_html__( 'Afficher la barre de progression', 'tools-adapter' ), 'type' => Controls_Manager::SWITCHER, 'return_value' => 'yes', 'default' => 'yes' ] );
		$this->add_control( 'show_icon', [ 'label' => esc_html__( 'Afficher l\'icône', 'tools-adapter' ), 'type' => Controls_Manager::SWITCHER, 'return_value' => 'yes', 'default' => 'yes' ] );
		$this->add_control( 'icon', [ 'label' => esc_html__( 'Icône', 'tools-adapter' ), 'type' => Controls_Manager::ICONS, 'default' => [ 'value' => 'fas fa-fire', 'library' => 'fa-solid' ], 'condition' => [ 'show_icon' => 'yes' ] ] );

		$this->end_controls_section();

		$this->start_controls_section( 'section_style', [ 'label' => esc_html__( 'Style', 'tools-adapter' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_control( 'text_color', [ 'label' => esc_html__( 'Couleur du texte', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#c0392b', 'selectors' => [ '{{WRAPPER}} .ta-stock-urgency__text' => 'color: {{VALUE}};' ] ] );
		$this->add_group_control( Group_Control_Typography::get_type(), [ 'name' => 'text_typography', 'selector' => '{{WRAPPER}} .ta-stock-urgency__text' ] );
		$this->add_control( 'icon_color', [ 'label' => esc_html__( 'Couleur icône', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#c0392b', 'selectors' => [ '{{WRAPPER}} .ta-stock-urgency__icon' => 'color: {{VALUE}};' ] ] );
		$this->add_control( 'track_color', [ 'label' => esc_html__( 'Fond de la barre', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#f3dede', 'separator' => 'before', 'selectors' => [ '{{WRAPPER}} .ta-stock-urgency__track' => 'background-color: {{VALUE}};' ] ] );
		$this->add_control( 'bar_color', [ 'label' => esc_html__( 'Couleur de progression', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#c0392b', 'selectors' => [ '{{WRAPPER}} .ta-stock-urgency__bar' => 'background-color: {{VALUE}};' ] ] );

		$this->end_controls_section();
	}

	protected function render() {
		if ( ! class_exists( 'WooCommerce' ) || ! function_exists( 'is_product' ) || ! is_product() ) {
			if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
				echo '<p>' . esc_html__( 'Ce widget ne s\'affiche que sur les pages produit WooCommerce avec gestion de stock activée.', 'tools-adapter' ) . '</p>';
			}
			return;
		}

		global $product;
		if ( ! $product instanceof \WC_Product ) {
			$product = wc_get_product( get_the_ID() );
		}
		if ( ! $product || ! $product->managing_stock() ) {
			return;
		}

		$settings  = $this->get_settings_for_display();
		$stock     = $product->get_stock_quantity();
		$threshold = absint( $settings['threshold'] ?? 10 );

		if ( null === $stock || $stock > $threshold || $stock <= 0 ) {
			return;
		}

		$reference = max( 1, absint( $settings['reference_stock'] ?? 20 ) );
		$percent   = min( 100, max( 4, round( ( $stock / $reference ) * 100 ) ) );
		$text      = str_replace( '{stock}', '<strong>' . esc_html( $stock ) . '</strong>', esc_html( \tools_adapter_translate( $settings['text_template'] ?? '' ) ) );
		?>
		<div class="ta-stock-urgency">
			<p class="ta-stock-urgency__text">
				<?php if ( 'yes' === ( $settings['show_icon'] ?? '' ) && ! empty( $settings['icon']['value'] ) ) : ?>
					<span class="ta-stock-urgency__icon" aria-hidden="true"><?php \Elementor\Icons_Manager::render_icon( $settings['icon'], [ 'aria-hidden' => 'true' ] ); ?></span>
				<?php endif; ?>
				<span><?php echo wp_kses_post( $text ); ?></span>
			</p>
			<?php if ( 'yes' === ( $settings['show_progress_bar'] ?? '' ) ) : ?>
				<div class="ta-stock-urgency__track">
					<div class="ta-stock-urgency__bar" style="width: <?php echo esc_attr( (string) $percent ); ?>%;"></div>
				</div>
			<?php endif; ?>
		</div>
		<?php
	}
}
