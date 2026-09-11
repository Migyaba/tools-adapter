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
 * Widget: Témoignages — grille ou carrousel d'avis clients.
 */
class Testimonials extends Widget_Base {

	public function get_name() {
		return 'tools-adapter-testimonials';
	}

	public function get_title() {
		return esc_html__( 'Témoignages', 'tools-adapter' );
	}

	public function get_icon() {
		return 'eicon-testimonial';
	}

	public function get_categories() {
		return [ 'tools-adapter' ];
	}

	public function get_keywords() {
		return [ 'témoignages', 'avis', 'clients', 'testimonial', 'review' ];
	}

	public function get_style_depends() {
		return [ 'tools-adapter-testimonials' ];
	}

	public function get_script_depends() {
		return [ 'tools-adapter-carousel' ];
	}

	protected function register_controls() {
		$this->start_controls_section( 'section_content', [ 'label' => esc_html__( 'Témoignages', 'tools-adapter' ) ] );

		$repeater = new Repeater();
		$repeater->add_control( 'photo', [ 'label' => esc_html__( 'Photo', 'tools-adapter' ), 'type' => Controls_Manager::MEDIA, 'default' => [ 'url' => \Elementor\Utils::get_placeholder_image_src() ] ] );
		$repeater->add_control( 'name', [ 'label' => esc_html__( 'Nom', 'tools-adapter' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'Prénom Nom', 'tools-adapter' ) ] );
		$repeater->add_control( 'role', [ 'label' => esc_html__( 'Fonction / Société', 'tools-adapter' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'Client', 'tools-adapter' ) ] );
		$repeater->add_control( 'quote', [ 'label' => esc_html__( 'Témoignage', 'tools-adapter' ), 'type' => Controls_Manager::TEXTAREA, 'rows' => 4, 'default' => esc_html__( 'Un service impeccable, je recommande vivement !', 'tools-adapter' ) ] );
		$repeater->add_control( 'rating', [ 'label' => esc_html__( 'Note (0-5)', 'tools-adapter' ), 'type' => Controls_Manager::NUMBER, 'min' => 0, 'max' => 5, 'step' => 0.5, 'default' => 5 ] );

		$this->add_control(
			'testimonials',
			[
				'label'       => esc_html__( 'Éléments', 'tools-adapter' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => [ [], [], [] ],
				'title_field' => '{{{ name }}}',
			]
		);

		$this->add_control(
			'layout',
			[
				'label'        => esc_html__( 'Disposition', 'tools-adapter' ),
				'type'         => Controls_Manager::SELECT,
				'default'      => 'grid',
				'options'      => [
					'grid'     => esc_html__( 'Grille', 'tools-adapter' ),
					'carousel' => esc_html__( 'Carrousel', 'tools-adapter' ),
				],
				'prefix_class' => 'ta-testimonials--',
			]
		);

		$this->add_responsive_control(
			'columns',
			[
				'label'          => esc_html__( 'Colonnes', 'tools-adapter' ),
				'type'           => Controls_Manager::NUMBER,
				'min'            => 1,
				'max'            => 4,
				'default'        => 3,
				'tablet_default' => 2,
				'mobile_default' => 1,
				'condition'      => [ 'layout' => 'grid' ],
				'selectors'      => [ '{{WRAPPER}} .ta-testimonials__grid' => '--ta-cols: {{VALUE}};' ],
			]
		);

		$this->add_control( 'show_arrows', [ 'label' => esc_html__( 'Flèches', 'tools-adapter' ), 'type' => Controls_Manager::SWITCHER, 'return_value' => 'yes', 'default' => 'yes', 'condition' => [ 'layout' => 'carousel' ] ] );
		$this->add_control( 'show_dots', [ 'label' => esc_html__( 'Puces', 'tools-adapter' ), 'type' => Controls_Manager::SWITCHER, 'return_value' => 'yes', 'default' => 'yes', 'condition' => [ 'layout' => 'carousel' ] ] );
		$this->add_control( 'autoplay', [ 'label' => esc_html__( 'Lecture automatique', 'tools-adapter' ), 'type' => Controls_Manager::SWITCHER, 'return_value' => 'yes', 'default' => 'yes', 'condition' => [ 'layout' => 'carousel' ] ] );
		$this->add_control( 'autoplay_speed', [ 'label' => esc_html__( 'Délai (ms)', 'tools-adapter' ), 'type' => Controls_Manager::NUMBER, 'min' => 1000, 'max' => 15000, 'step' => 500, 'default' => 5000, 'condition' => [ 'layout' => 'carousel', 'autoplay' => 'yes' ] ] );

		$this->add_control( 'show_quote_icon', [ 'label' => esc_html__( 'Icône guillemet', 'tools-adapter' ), 'type' => Controls_Manager::SWITCHER, 'return_value' => 'yes', 'default' => 'yes', 'separator' => 'before' ] );
		$this->add_control( 'show_rating', [ 'label' => esc_html__( 'Note (étoiles)', 'tools-adapter' ), 'type' => Controls_Manager::SWITCHER, 'return_value' => 'yes', 'default' => 'yes' ] );

		$this->end_controls_section();

		$this->start_controls_section( 'section_style_card', [ 'label' => esc_html__( 'Carte', 'tools-adapter' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_control( 'card_bg', [ 'label' => esc_html__( 'Fond', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#ffffff', 'selectors' => [ '{{WRAPPER}} .ta-testimonial' => 'background-color: {{VALUE}};' ] ] );
		$this->add_responsive_control( 'card_padding', [ 'label' => esc_html__( 'Espacement interne', 'tools-adapter' ), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => [ 'px', 'em' ], 'default' => [ 'top' => '32', 'right' => '28', 'bottom' => '32', 'left' => '28', 'unit' => 'px' ], 'selectors' => [ '{{WRAPPER}} .ta-testimonial' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ] ] );
		$this->add_control( 'card_radius', [ 'label' => esc_html__( 'Arrondi', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'range' => [ 'px' => [ 'min' => 0, 'max' => 60 ] ], 'default' => [ 'size' => 12, 'unit' => 'px' ], 'selectors' => [ '{{WRAPPER}} .ta-testimonial' => 'border-radius: {{SIZE}}{{UNIT}};' ] ] );
		$this->add_group_control( Group_Control_Border::get_type(), [ 'name' => 'card_border', 'selector' => '{{WRAPPER}} .ta-testimonial' ] );
		$this->add_group_control( Group_Control_Box_Shadow::get_type(), [ 'name' => 'card_shadow', 'selector' => '{{WRAPPER}} .ta-testimonial' ] );
		$this->add_responsive_control( 'items_gap', [ 'label' => esc_html__( 'Espacement entre cartes', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'range' => [ 'px' => [ 'min' => 0, 'max' => 60 ] ], 'default' => [ 'size' => 24, 'unit' => 'px' ], 'separator' => 'before', 'selectors' => [ '{{WRAPPER}} .ta-testimonials' => '--ta-gap: {{SIZE}}{{UNIT}};' ] ] );

		$this->end_controls_section();

		$this->start_controls_section( 'section_style_content', [ 'label' => esc_html__( 'Contenu', 'tools-adapter' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_control( 'avatar_size', [ 'label' => esc_html__( 'Taille photo', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'range' => [ 'px' => [ 'min' => 30, 'max' => 120 ] ], 'default' => [ 'size' => 56, 'unit' => 'px' ], 'selectors' => [ '{{WRAPPER}} .ta-testimonial__avatar' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};' ] ] );
		$this->add_control( 'quote_color', [ 'label' => esc_html__( 'Couleur du texte', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#333333', 'selectors' => [ '{{WRAPPER}} .ta-testimonial__quote' => 'color: {{VALUE}};' ] ] );
		$this->add_group_control( Group_Control_Typography::get_type(), [ 'name' => 'quote_typography', 'selector' => '{{WRAPPER}} .ta-testimonial__quote' ] );
		$this->add_control( 'name_color', [ 'label' => esc_html__( 'Couleur du nom', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#1c1c1c', 'separator' => 'before', 'selectors' => [ '{{WRAPPER}} .ta-testimonial__name' => 'color: {{VALUE}};' ] ] );
		$this->add_control( 'role_color', [ 'label' => esc_html__( 'Couleur de la fonction', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#888888', 'selectors' => [ '{{WRAPPER}} .ta-testimonial__role' => 'color: {{VALUE}};' ] ] );
		$this->add_control( 'rating_color', [ 'label' => esc_html__( 'Couleur des étoiles', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#C9A84C', 'separator' => 'before', 'selectors' => [ '{{WRAPPER}} .ta-testimonial__rating' => 'color: {{VALUE}};' ] ] );
		$this->add_control( 'quote_icon_color', [ 'label' => esc_html__( 'Couleur du guillemet', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#eeeeee', 'selectors' => [ '{{WRAPPER}} .ta-testimonial__quote-icon' => 'color: {{VALUE}};' ] ] );

		$this->end_controls_section();

		$this->start_controls_section( 'section_style_nav', [ 'label' => esc_html__( 'Navigation carrousel', 'tools-adapter' ), 'tab' => Controls_Manager::TAB_STYLE, 'condition' => [ 'layout' => 'carousel' ] ] );
		$this->add_control( 'arrow_color', [ 'label' => esc_html__( 'Couleur flèches', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#1c1c1c', 'selectors' => [ '{{WRAPPER}} .ta-carousel__arrow' => 'color: {{VALUE}};' ] ] );
		$this->add_control( 'dot_color', [ 'label' => esc_html__( 'Couleur puces', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#dddddd', 'selectors' => [ '{{WRAPPER}} .ta-carousel__dot' => 'background-color: {{VALUE}};' ] ] );
		$this->add_control( 'dot_color_active', [ 'label' => esc_html__( 'Couleur puce active', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#C9A84C', 'selectors' => [ '{{WRAPPER}} .ta-carousel__dot.is-active' => 'background-color: {{VALUE}};' ] ] );
		$this->end_controls_section();
	}

	/**
	 * @param float $rating Rating value.
	 */
	private function render_stars( $rating ) {
		$rating = max( 0, min( 5, (float) $rating ) );
		echo '<span class="ta-testimonial__rating" aria-hidden="true">';
		for ( $i = 1; $i <= 5; $i++ ) {
			echo $i <= round( $rating ) ? '★' : '☆'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		echo '</span>';
	}

	protected function render() {
		$settings     = $this->get_settings_for_display();
		$testimonials = $settings['testimonials'] ?? [];
		if ( empty( $testimonials ) ) {
			return;
		}

		$is_carousel = 'carousel' === ( $settings['layout'] ?? 'grid' );
		?>
		<div
			class="ta-testimonials"
			<?php if ( $is_carousel ) : ?>
				data-ta-carousel
				data-autoplay="<?php echo 'yes' === ( $settings['autoplay'] ?? '' ) ? '1' : '0'; ?>"
				data-autoplay-speed="<?php echo esc_attr( (string) intval( $settings['autoplay_speed'] ?? 5000 ) ); ?>"
			<?php endif; ?>
		>
			<div class="ta-testimonials__<?php echo $is_carousel ? 'track' : 'grid'; ?>" <?php echo $is_carousel ? 'data-carousel-track' : ''; ?>>
				<?php foreach ( $testimonials as $item ) : ?>
					<div class="ta-testimonial<?php echo $is_carousel ? ' ta-testimonial--slide' : ''; ?>" <?php echo $is_carousel ? 'data-carousel-slide' : ''; ?>>
						<?php if ( 'yes' === ( $settings['show_quote_icon'] ?? '' ) ) : ?>
							<span class="ta-testimonial__quote-icon" aria-hidden="true">&ldquo;</span>
						<?php endif; ?>
						<?php if ( 'yes' === ( $settings['show_rating'] ?? '' ) ) : ?>
							<?php $this->render_stars( $item['rating'] ?? 5 ); ?>
						<?php endif; ?>
						<?php if ( ! empty( $item['quote'] ) ) : ?>
							<p class="ta-testimonial__quote"><?php echo esc_html( \tools_adapter_translate( $item['quote'] ) ); ?></p>
						<?php endif; ?>
						<div class="ta-testimonial__author">
							<?php if ( ! empty( $item['photo']['url'] ) ) : ?>
								<img class="ta-testimonial__avatar" src="<?php echo esc_url( $item['photo']['url'] ); ?>" alt="<?php echo esc_attr( $item['name'] ?? '' ); ?>" loading="lazy" />
							<?php endif; ?>
							<div class="ta-testimonial__meta">
								<?php if ( ! empty( $item['name'] ) ) : ?><p class="ta-testimonial__name"><?php echo esc_html( \tools_adapter_translate( $item['name'] ) ); ?></p><?php endif; ?>
								<?php if ( ! empty( $item['role'] ) ) : ?><p class="ta-testimonial__role"><?php echo esc_html( \tools_adapter_translate( $item['role'] ) ); ?></p><?php endif; ?>
							</div>
						</div>
					</div>
				<?php endforeach; ?>
			</div>

			<?php if ( $is_carousel ) : ?>
				<?php if ( 'yes' === ( $settings['show_arrows'] ?? '' ) ) : ?>
					<button type="button" class="ta-carousel__arrow ta-carousel__arrow--prev" data-carousel-prev aria-label="<?php echo esc_attr__( 'Précédent', 'tools-adapter' ); ?>">&#8249;</button>
					<button type="button" class="ta-carousel__arrow ta-carousel__arrow--next" data-carousel-next aria-label="<?php echo esc_attr__( 'Suivant', 'tools-adapter' ); ?>">&#8250;</button>
				<?php endif; ?>
				<?php if ( 'yes' === ( $settings['show_dots'] ?? '' ) ) : ?>
					<div class="ta-carousel__dots" data-carousel-dots></div>
				<?php endif; ?>
			<?php endif; ?>
		</div>
		<?php
	}
}
