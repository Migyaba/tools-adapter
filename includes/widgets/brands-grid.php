<?php
namespace ToolsAdapter\Widgets;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use ToolsAdapter\Products_Query;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Widget: Marques — grille de logos de marques (taxonomie native "product_brand"
 * si disponible, sinon n'importe quel attribut WooCommerce utilisé comme marque).
 */
class Brands_Grid extends Widget_Base {

	public function get_name() {
		return 'tools-adapter-brands';
	}

	public function get_title() {
		return esc_html__( 'Marques', 'tools-adapter' );
	}

	public function get_icon() {
		return 'eicon-product-tag';
	}

	public function get_categories() {
		return [ 'tools-adapter', 'woocommerce-elements' ];
	}

	public function get_keywords() {
		return [ 'marques', 'brands', 'fabricants', 'logos' ];
	}

	public function get_style_depends() {
		return [ 'tools-adapter-brands' ];
	}

	/**
	 * @return array<string, string>
	 */
	private function get_taxonomy_options() {
		$options = [];
		if ( taxonomy_exists( 'product_brand' ) ) {
			$options['product_brand'] = esc_html__( 'Marques (natif WooCommerce)', 'tools-adapter' );
		}
		return $options + Products_Query::get_attribute_taxonomy_options();
	}

	protected function register_controls() {
		$this->start_controls_section( 'section_content', [ 'label' => esc_html__( 'Réglages', 'tools-adapter' ) ] );

		$this->add_control(
			'taxonomy',
			[
				'label'   => esc_html__( 'Taxonomie', 'tools-adapter' ),
				'type'    => Controls_Manager::SELECT,
				'options' => $this->get_taxonomy_options(),
				'default' => taxonomy_exists( 'product_brand' ) ? 'product_brand' : '',
			]
		);

		$this->add_responsive_control(
			'columns',
			[
				'label'          => esc_html__( 'Colonnes', 'tools-adapter' ),
				'type'           => Controls_Manager::NUMBER,
				'min'            => 2,
				'max'            => 8,
				'default'        => 5,
				'tablet_default' => 3,
				'mobile_default' => 2,
				'selectors'      => [ '{{WRAPPER}} .ta-brands' => '--ta-brands-cols: {{VALUE}};' ],
			]
		);

		$this->add_control( 'hide_empty', [ 'label' => esc_html__( 'Masquer les marques vides', 'tools-adapter' ), 'type' => Controls_Manager::SWITCHER, 'return_value' => 'yes', 'default' => 'yes' ] );
		$this->add_control( 'limit', [ 'label' => esc_html__( 'Nombre max.', 'tools-adapter' ), 'type' => Controls_Manager::NUMBER, 'default' => 12, 'min' => 1 ] );
		$this->add_control( 'grayscale', [ 'label' => esc_html__( 'Niveaux de gris au repos', 'tools-adapter' ), 'type' => Controls_Manager::SWITCHER, 'return_value' => 'yes', 'default' => 'yes' ] );
		$this->add_control( 'show_name_fallback', [ 'label' => esc_html__( 'Afficher le nom si pas de logo', 'tools-adapter' ), 'type' => Controls_Manager::SWITCHER, 'return_value' => 'yes', 'default' => 'yes' ] );

		$this->end_controls_section();

		$this->start_controls_section( 'section_style', [ 'label' => esc_html__( 'Style', 'tools-adapter' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_control( 'card_bg', [ 'label' => esc_html__( 'Fond', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#ffffff', 'selectors' => [ '{{WRAPPER}} .ta-brands__item' => 'background-color: {{VALUE}};' ] ] );
		$this->add_control( 'card_radius', [ 'label' => esc_html__( 'Arrondi', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'range' => [ 'px' => [ 'min' => 0, 'max' => 40 ] ], 'default' => [ 'size' => 8, 'unit' => 'px' ], 'selectors' => [ '{{WRAPPER}} .ta-brands__item' => 'border-radius: {{SIZE}}{{UNIT}};' ] ] );
		$this->add_group_control( Group_Control_Border::get_type(), [ 'name' => 'card_border', 'selector' => '{{WRAPPER}} .ta-brands__item' ] );
		$this->add_group_control( Group_Control_Box_Shadow::get_type(), [ 'name' => 'card_shadow', 'selector' => '{{WRAPPER}} .ta-brands__item' ] );
		$this->add_responsive_control( 'items_gap', [ 'label' => esc_html__( 'Espacement', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'range' => [ 'px' => [ 'min' => 0, 'max' => 50 ] ], 'default' => [ 'size' => 16, 'unit' => 'px' ], 'separator' => 'before', 'selectors' => [ '{{WRAPPER}} .ta-brands' => '--ta-brands-gap: {{SIZE}}{{UNIT}};' ] ] );
		$this->add_responsive_control( 'logo_height', [ 'label' => esc_html__( 'Hauteur du logo', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'range' => [ 'px' => [ 'min' => 20, 'max' => 160 ] ], 'default' => [ 'size' => 60, 'unit' => 'px' ], 'selectors' => [ '{{WRAPPER}} .ta-brands__logo' => 'max-height: {{SIZE}}{{UNIT}};' ] ] );
		$this->add_control( 'name_color', [ 'label' => esc_html__( 'Couleur du nom', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#1c1c1c', 'separator' => 'before', 'selectors' => [ '{{WRAPPER}} .ta-brands__name' => 'color: {{VALUE}};' ] ] );

		$this->end_controls_section();
	}

	protected function render() {
		if ( ! class_exists( 'WooCommerce' ) ) {
			echo '<p>' . esc_html__( 'WooCommerce est requis pour ce widget.', 'tools-adapter' ) . '</p>';
			return;
		}

		$settings = $this->get_settings_for_display();
		$taxonomy = $settings['taxonomy'] ?? '';

		if ( ! $taxonomy || ! taxonomy_exists( $taxonomy ) ) {
			if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
				echo '<p>' . esc_html__( 'Sélectionnez une taxonomie de marques dans les réglages du widget.', 'tools-adapter' ) . '</p>';
			}
			return;
		}

		$terms = get_terms(
			[
				'taxonomy'   => $taxonomy,
				'hide_empty' => 'yes' === ( $settings['hide_empty'] ?? '' ),
				'number'     => absint( $settings['limit'] ?? 12 ),
			]
		);

		if ( empty( $terms ) || is_wp_error( $terms ) ) {
			return;
		}

		$grayscale_class = 'yes' === ( $settings['grayscale'] ?? '' ) ? '' : ' ta-brands--no-grayscale';
		?>
		<div class="ta-brands<?php echo esc_attr( $grayscale_class ); ?>">
			<?php foreach ( $terms as $term ) : ?>
				<?php
				$thumbnail_id = get_term_meta( $term->term_id, 'thumbnail_id', true );
				$image_url    = $thumbnail_id ? wp_get_attachment_image_url( $thumbnail_id, 'medium' ) : '';
				?>
				<a class="ta-brands__item" href="<?php echo esc_url( get_term_link( $term ) ); ?>">
					<?php if ( $image_url ) : ?>
						<img class="ta-brands__logo" src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $term->name ); ?>" loading="lazy" />
					<?php elseif ( 'yes' === ( $settings['show_name_fallback'] ?? '' ) ) : ?>
						<span class="ta-brands__name"><?php echo esc_html( $term->name ); ?></span>
					<?php endif; ?>
				</a>
			<?php endforeach; ?>
		</div>
		<?php
	}
}
