<?php
namespace ToolsAdapter\Widgets;

use Elementor\Controls_Manager;
use ToolsAdapter\Base_Widget;
use ToolsAdapter\Repeater;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Widget: Logos partenaires — grille statique ou défilement (marquee).
 */
class Logos_Grid extends Base_Widget {

	public function get_name() {
		return 'tools-adapter-logos';
	}

	public function get_title() {
		return esc_html__( 'Logos partenaires', 'tools-adapter' );
	}

	public function get_icon() {
		return 'eicon-image-before-after';
	}

	public function get_categories() {
		return [ 'tools-adapter' ];
	}

	public function get_keywords() {
		return [ 'logos', 'partenaires', 'marques', 'clients', 'marquee' ];
	}

	public function get_style_depends() {
		return [ 'tools-adapter-logos' ];
	}

	protected function register_controls() {
		$this->start_controls_section( 'section_content', [ 'label' => esc_html__( 'Logos', 'tools-adapter' ) ] );

		$repeater = new Repeater();
		$repeater->add_control( 'image', [ 'label' => esc_html__( 'Logo', 'tools-adapter' ), 'type' => Controls_Manager::MEDIA, 'default' => [ 'url' => \Elementor\Utils::get_placeholder_image_src() ] ] );
		$repeater->add_control( 'name', [ 'label' => esc_html__( 'Nom (alt)', 'tools-adapter' ), 'type' => Controls_Manager::TEXT, 'default' => '' ] );
		$repeater->add_control( 'link', [ 'label' => esc_html__( 'Lien', 'tools-adapter' ), 'type' => Controls_Manager::URL ] );

		$this->add_control(
			'logos',
			[
				'label'       => esc_html__( 'Logos', 'tools-adapter' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => [ [], [], [], [] ],
				'title_field' => '{{{ name }}}',
			]
		);

		$this->add_control(
			'display_mode',
			[
				'label'        => esc_html__( 'Mode d\'affichage', 'tools-adapter' ),
				'type'         => Controls_Manager::SELECT,
				'default'      => 'grid',
				'options'      => [
					'grid'    => esc_html__( 'Grille statique', 'tools-adapter' ),
					'marquee' => esc_html__( 'Défilement continu', 'tools-adapter' ),
				],
				'prefix_class' => 'ta-logos--',
			]
		);

		$this->add_responsive_control(
			'columns',
			[
				'label'     => esc_html__( 'Colonnes', 'tools-adapter' ),
				'type'      => Controls_Manager::NUMBER,
				'min'       => 2,
				'max'       => 8,
				'default'   => 5,
				'tablet_default' => 3,
				'mobile_default' => 2,
				'condition' => [ 'display_mode' => 'grid' ],
				'selectors' => [ '{{WRAPPER}} .ta-logos__grid' => '--ta-logos-cols: {{VALUE}};' ],
			]
		);

		$this->add_control(
			'marquee_speed',
			[
				'label'     => esc_html__( 'Vitesse de défilement (secondes)', 'tools-adapter' ),
				'type'      => Controls_Manager::NUMBER,
				'min'       => 5,
				'max'       => 120,
				'default'   => 30,
				'condition' => [ 'display_mode' => 'marquee' ],
			]
		);

		$this->add_control(
			'pause_on_hover',
			[
				'label'        => esc_html__( 'Pause au survol', 'tools-adapter' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => [ 'display_mode' => 'marquee' ],
			]
		);

		$this->add_control(
			'grayscale',
			[
				'label'        => esc_html__( 'Niveaux de gris', 'tools-adapter' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section( 'section_style', [ 'label' => esc_html__( 'Style', 'tools-adapter' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_responsive_control( 'logo_height', [ 'label' => esc_html__( 'Hauteur des logos', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'range' => [ 'px' => [ 'min' => 20, 'max' => 150 ] ], 'default' => [ 'size' => 48, 'unit' => 'px' ], 'selectors' => [ '{{WRAPPER}} .ta-logos__img' => 'height: {{SIZE}}{{UNIT}}; width: auto;' ] ] );
		$this->add_responsive_control( 'items_gap', [ 'label' => esc_html__( 'Espacement', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'range' => [ 'px' => [ 'min' => 0, 'max' => 100 ] ], 'default' => [ 'size' => 40, 'unit' => 'px' ], 'selectors' => [ '{{WRAPPER}} .ta-logos' => '--ta-logos-gap: {{SIZE}}{{UNIT}};' ] ] );
		$this->add_control( 'grayscale_opacity', [ 'label' => esc_html__( 'Opacité (au repos)', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'range' => [ 'px' => [ 'min' => 0, 'max' => 1, 'step' => 0.05 ] ], 'default' => [ 'size' => 0.6 ], 'condition' => [ 'grayscale' => 'yes' ], 'selectors' => [ '{{WRAPPER}} .ta-logos__link img' => 'opacity: {{SIZE}};' ] ] );

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$logos    = $settings['logos'] ?? [];
		if ( empty( $logos ) ) {
			return;
		}

		$mode  = ( 'marquee' === ( $settings['display_mode'] ?? 'grid' ) ) ? 'marquee' : 'grid';
		$speed = max( 5, intval( $settings['marquee_speed'] ?? 30 ) );

		$render_item = function ( $logo ) {
			$image_url = $logo['image']['url'] ?? '';
			if ( empty( $image_url ) ) {
				return;
			}
			$alt  = ! empty( $logo['name'] ) ? $logo['name'] : '';
			$link = $logo['link']['url'] ?? '';
			?>
			<span class="ta-logos__item">
				<?php if ( $link ) : ?>
					<a class="ta-logos__link" href="<?php echo esc_url( $link ); ?>"<?php echo ! empty( $logo['link']['is_external'] ) ? ' target="_blank"' : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
						<img class="ta-logos__img" src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $alt ); ?>" loading="lazy" />
					</a>
				<?php else : ?>
					<span class="ta-logos__link">
						<img class="ta-logos__img" src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $alt ); ?>" loading="lazy" />
					</span>
				<?php endif; ?>
			</span>
			<?php
		};
		?>
		<div class="ta-logos<?php echo 'yes' !== ( $settings['grayscale'] ?? 'yes' ) ? ' ta-logos--no-grayscale' : ''; ?>" data-mode="<?php echo esc_attr( $mode ); ?>">
			<?php if ( 'marquee' === $mode ) : ?>
				<div
					class="ta-logos__track"
					style="animation-duration: <?php echo esc_attr( (string) $speed ); ?>s;"
					<?php echo 'yes' === ( $settings['pause_on_hover'] ?? '' ) ? 'data-pause-hover="1"' : ''; ?>
				>
					<?php
					// Render the list twice for a seamless infinite loop.
					for ( $i = 0; $i < 2; $i++ ) {
						foreach ( $logos as $logo ) {
							$render_item( $logo );
						}
					}
					?>
				</div>
			<?php else : ?>
				<div class="ta-logos__grid">
					<?php foreach ( $logos as $logo ) { $render_item( $logo ); } ?>
				</div>
			<?php endif; ?>
		</div>
		<?php
	}
}
