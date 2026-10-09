<?php
namespace ToolsAdapter\Widgets;

use Elementor\Controls_Manager;
use ToolsAdapter\Base_Widget;
use ToolsAdapter\Repeater;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Widget: Lookbook (Shop the look) — image avec points interactifs reliés à des produits.
 */
class Lookbook extends Base_Widget {

	public function get_name() {
		return 'tools-adapter-lookbook';
	}

	public function get_title() {
		return esc_html__( 'Lookbook (Shop the look)', 'tools-adapter' );
	}

	public function get_icon() {
		return 'eicon-image-hotspot';
	}

	public function get_categories() {
		return [ 'tools-adapter', 'woocommerce-elements' ];
	}

	public function get_keywords() {
		return [ 'lookbook', 'hotspot', 'shop the look', 'points', 'produits', 'image' ];
	}

	public function get_style_depends() {
		return [ 'tools-adapter-lookbook' ];
	}

	public function get_script_depends() {
		return [ 'tools-adapter-lookbook' ];
	}

	protected function register_controls() {
		$this->start_controls_section( 'section_image', [ 'label' => esc_html__( 'Image', 'tools-adapter' ) ] );

		$this->add_control( 'image', [ 'label' => esc_html__( 'Image', 'tools-adapter' ), 'type' => Controls_Manager::MEDIA ] );
		$this->add_control(
			'ratio',
			[
				'label'     => esc_html__( 'Format', 'tools-adapter' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => '4/5',
				'options'   => [
					'4/5'  => '4:5',
					'3/4'  => '3:4',
					'1/1'  => '1:1',
					'4/3'  => '4:3',
					'16/9' => '16:9',
				],
				'selectors' => [ '{{WRAPPER}} .ta-lookbook__media' => 'aspect-ratio: {{VALUE}};' ],
			]
		);

		$repeater = new Repeater();
		$repeater->add_control( 'product_id', [ 'label' => esc_html__( 'ID du produit', 'tools-adapter' ), 'type' => Controls_Manager::NUMBER, 'min' => 1 ] );
		$repeater->add_control( 'x', [ 'label' => esc_html__( 'Position horizontale (%)', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'size_units' => [ '%' ], 'range' => [ '%' => [ 'min' => 0, 'max' => 100 ] ], 'default' => [ 'size' => 50, 'unit' => '%' ] ] );
		$repeater->add_control( 'y', [ 'label' => esc_html__( 'Position verticale (%)', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'size_units' => [ '%' ], 'range' => [ '%' => [ 'min' => 0, 'max' => 100 ] ], 'default' => [ 'size' => 50, 'unit' => '%' ] ] );

		$this->add_control(
			'hotspots',
			[
				'label'       => esc_html__( 'Points produits', 'tools-adapter' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => [],
				'title_field' => 'Produit #{{{ product_id }}}',
			]
		);

		$this->add_control( 'pulse', [ 'label' => esc_html__( 'Animation des points', 'tools-adapter' ), 'type' => Controls_Manager::SWITCHER, 'return_value' => 'yes', 'default' => 'yes' ] );
		$this->add_control( 'link_text', [ 'label' => esc_html__( 'Texte du lien', 'tools-adapter' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'Voir le produit', 'tools-adapter' ) ] );

		$this->end_controls_section();

		$this->start_controls_section( 'section_style', [ 'label' => esc_html__( 'Style', 'tools-adapter' ), 'tab' => Controls_Manager::TAB_STYLE ] );
		$this->add_control( 'dot_color', [ 'label' => esc_html__( 'Couleur des points', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#ffffff', 'selectors' => [ '{{WRAPPER}} .ta-lookbook' => '--ta-lb-dot: {{VALUE}};' ] ] );
		$this->add_control( 'card_bg', [ 'label' => esc_html__( 'Fond de la vignette', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#ffffff', 'selectors' => [ '{{WRAPPER}} .ta-lookbook' => '--ta-lb-card-bg: {{VALUE}};' ] ] );
		$this->add_control( 'card_color', [ 'label' => esc_html__( 'Texte de la vignette', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#1c1c1c', 'selectors' => [ '{{WRAPPER}} .ta-lookbook' => '--ta-lb-card-color: {{VALUE}};' ] ] );
		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$image    = $settings['image'] ?? [];
		$uid      = 'ta-lb-' . $this->get_id();
		?>
		<div class="ta-lookbook<?php echo 'yes' === ( $settings['pulse'] ?? '' ) ? ' ta-lookbook--pulse' : ''; ?>" data-ta-lookbook>
			<div class="ta-lookbook__media">
				<?php
				if ( ! empty( $image['id'] ) ) {
					echo wp_get_attachment_image( (int) $image['id'], 'full', false, [ 'class' => 'ta-lookbook__img', 'loading' => 'lazy', 'sizes' => '(max-width: 767px) 100vw, 50vw' ] );
				} elseif ( ! empty( $image['url'] ) ) {
					printf( '<img class="ta-lookbook__img" src="%s" alt="" loading="lazy">', esc_url( $image['url'] ) );
				}

				foreach ( (array) ( $settings['hotspots'] ?? [] ) as $index => $spot ) :
					$product = function_exists( 'wc_get_product' ) && ! empty( $spot['product_id'] ) ? wc_get_product( (int) $spot['product_id'] ) : null;
					if ( ! $product || ! $product->is_visible() ) {
						continue;
					}
					$x       = isset( $spot['x']['size'] ) ? max( 0, min( 100, (float) $spot['x']['size'] ) ) : 50;
					$y       = isset( $spot['y']['size'] ) ? max( 0, min( 100, (float) $spot['y']['size'] ) ) : 50;
					$card_id = $uid . '-' . $index;
					$classes = 'ta-lookbook__spot' . ( $x > 55 ? ' ta-lookbook__spot--left' : '' ) . ( $y > 60 ? ' ta-lookbook__spot--up' : '' );
					?>
					<div class="<?php echo esc_attr( $classes ); ?>" style="left: <?php echo esc_attr( $x ); ?>%; top: <?php echo esc_attr( $y ); ?>%;">
						<button type="button" class="ta-lookbook__dot" aria-expanded="false" aria-controls="<?php echo esc_attr( $card_id ); ?>">
							<span class="screen-reader-text">
								<?php
								/* translators: %s: product name. */
								echo esc_html( sprintf( __( 'Voir %s', 'tools-adapter' ), $product->get_name() ) );
								?>
							</span>
						</button>
						<div class="ta-lookbook__card" id="<?php echo esc_attr( $card_id ); ?>">
							<a class="ta-lookbook__thumb" href="<?php echo esc_url( $product->get_permalink() ); ?>" tabindex="-1" aria-hidden="true">
								<?php echo $product->get_image( 'woocommerce_gallery_thumbnail' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core markup. ?>
							</a>
							<div class="ta-lookbook__info">
								<a class="ta-lookbook__name" href="<?php echo esc_url( $product->get_permalink() ); ?>"><?php echo esc_html( $product->get_name() ); ?></a>
								<span class="ta-lookbook__price"><?php echo wp_kses_post( $product->get_price_html() ); ?></span>
								<a class="ta-lookbook__link" href="<?php echo esc_url( $product->get_permalink() ); ?>"><?php echo esc_html( $settings['link_text'] ?? '' ); ?></a>
							</div>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
		<?php
	}
}
