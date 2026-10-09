<?php
namespace ToolsAdapter\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use ToolsAdapter\Base_Widget;
use ToolsAdapter\Repeater;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Widget: Mosaïque promo — tuiles image (bento, 2, 3 ou 4 colonnes) avec sur-titre, titre, texte et lien.
 */
class Promo_Grid extends Base_Widget {

	public function get_name() {
		return 'tools-adapter-promo-grid';
	}

	public function get_title() {
		return esc_html__( 'Mosaïque promo (Bento)', 'tools-adapter' );
	}

	public function get_icon() {
		return 'eicon-gallery-masonry';
	}

	public function get_categories() {
		return [ 'tools-adapter' ];
	}

	public function get_keywords() {
		return [ 'bento', 'mosaïque', 'promo', 'bannière', 'collection', 'tuiles', 'grid' ];
	}

	public function get_style_depends() {
		return [ 'tools-adapter-promo-grid' ];
	}

	protected function register_controls() {
		$this->start_controls_section( 'section_tiles', [ 'label' => esc_html__( 'Tuiles', 'tools-adapter' ) ] );

		$this->add_control(
			'layout',
			[
				'label'   => esc_html__( 'Disposition', 'tools-adapter' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'bento',
				'options' => [
					'bento'       => esc_html__( 'Bento — grande tuile à gauche', 'tools-adapter' ),
					'bento-right' => esc_html__( 'Bento — grande tuile à droite', 'tools-adapter' ),
					'cols-2'      => esc_html__( '2 colonnes', 'tools-adapter' ),
					'cols-3'      => esc_html__( '3 colonnes', 'tools-adapter' ),
					'cols-4'      => esc_html__( '4 colonnes', 'tools-adapter' ),
				],
			]
		);

		$repeater = new Repeater();
		$repeater->add_control( 'image', [ 'label' => esc_html__( 'Image', 'tools-adapter' ), 'type' => Controls_Manager::MEDIA ] );
		$repeater->add_control( 'eyebrow', [ 'label' => esc_html__( 'Sur-titre', 'tools-adapter' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'Nouvelle collection', 'tools-adapter' ) ] );
		$repeater->add_control( 'title', [ 'label' => esc_html__( 'Titre', 'tools-adapter' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'Titre de la tuile', 'tools-adapter' ) ] );
		$repeater->add_control( 'description', [ 'label' => esc_html__( 'Texte', 'tools-adapter' ), 'type' => Controls_Manager::TEXTAREA, 'rows' => 2 ] );
		$repeater->add_control( 'button_text', [ 'label' => esc_html__( 'Texte du lien', 'tools-adapter' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'Découvrir', 'tools-adapter' ) ] );
		$repeater->add_control( 'link', [ 'label' => esc_html__( 'Lien', 'tools-adapter' ), 'type' => Controls_Manager::URL ] );
		$repeater->add_control(
			'position',
			[
				'label'   => esc_html__( 'Position du texte', 'tools-adapter' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'bottom-left',
				'options' => [
					'bottom-left'   => esc_html__( 'Bas gauche', 'tools-adapter' ),
					'bottom-center' => esc_html__( 'Bas centré', 'tools-adapter' ),
					'center'        => esc_html__( 'Centré', 'tools-adapter' ),
					'top-left'      => esc_html__( 'Haut gauche', 'tools-adapter' ),
				],
			]
		);

		$this->add_control(
			'tiles',
			[
				'label'       => esc_html__( 'Tuiles', 'tools-adapter' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => [
					[ 'title' => esc_html__( 'Nouvelle collection', 'tools-adapter' ), 'eyebrow' => esc_html__( 'Automne', 'tools-adapter' ) ],
					[ 'title' => esc_html__( 'Essentiels', 'tools-adapter' ), 'eyebrow' => esc_html__( 'Basiques', 'tools-adapter' ) ],
					[ 'title' => esc_html__( 'Accessoires', 'tools-adapter' ), 'eyebrow' => esc_html__( 'Détails', 'tools-adapter' ) ],
				],
				'title_field' => '{{{ title }}}',
			]
		);

		$this->add_control( 'title_tag', [ 'label' => esc_html__( 'Balise du titre', 'tools-adapter' ), 'type' => Controls_Manager::SELECT, 'default' => 'h3', 'options' => [ 'h2' => 'H2', 'h3' => 'H3', 'h4' => 'H4', 'p' => 'p' ] ] );
		$this->add_control( 'hover_zoom', [ 'label' => esc_html__( 'Zoom au survol', 'tools-adapter' ), 'type' => Controls_Manager::SWITCHER, 'return_value' => 'yes', 'default' => 'yes' ] );

		$this->end_controls_section();

		$this->start_controls_section( 'section_style', [ 'label' => esc_html__( 'Style', 'tools-adapter' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_responsive_control( 'height', [ 'label' => esc_html__( 'Hauteur (bento)', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'size_units' => [ 'px', 'vh' ], 'range' => [ 'px' => [ 'min' => 240, 'max' => 1000 ] ], 'default' => [ 'size' => 620, 'unit' => 'px' ], 'selectors' => [ '{{WRAPPER}} .ta-promo-grid' => '--ta-pg-height: {{SIZE}}{{UNIT}};' ] ] );
		$this->add_responsive_control( 'tile_height', [ 'label' => esc_html__( 'Hauteur des tuiles (colonnes / mobile)', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'size_units' => [ 'px', 'vh' ], 'range' => [ 'px' => [ 'min' => 160, 'max' => 900 ] ], 'default' => [ 'size' => 460, 'unit' => 'px' ], 'mobile_default' => [ 'size' => 340, 'unit' => 'px' ], 'selectors' => [ '{{WRAPPER}} .ta-promo-grid' => '--ta-pg-tile-h: {{SIZE}}{{UNIT}};' ] ] );
		$this->add_responsive_control( 'gap', [ 'label' => esc_html__( 'Espacement', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'range' => [ 'px' => [ 'min' => 0, 'max' => 60 ] ], 'default' => [ 'size' => 16, 'unit' => 'px' ], 'selectors' => [ '{{WRAPPER}} .ta-promo-grid' => '--ta-pg-gap: {{SIZE}}{{UNIT}};' ] ] );
		$this->add_control( 'radius', [ 'label' => esc_html__( 'Arrondi', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'range' => [ 'px' => [ 'min' => 0, 'max' => 40 ] ], 'default' => [ 'size' => 0, 'unit' => 'px' ], 'selectors' => [ '{{WRAPPER}} .ta-promo-grid' => '--ta-pg-radius: {{SIZE}}{{UNIT}};' ] ] );
		$this->add_control( 'overlay', [ 'label' => esc_html__( 'Voile (dégradé)', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => 'rgba(0,0,0,0.55)', 'selectors' => [ '{{WRAPPER}} .ta-promo-grid' => '--ta-pg-overlay: {{VALUE}};' ] ] );
		$this->add_control( 'text_color', [ 'label' => esc_html__( 'Couleur du texte', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#ffffff', 'selectors' => [ '{{WRAPPER}} .ta-promo-tile' => 'color: {{VALUE}};' ] ] );
		$this->add_group_control( Group_Control_Typography::get_type(), [ 'name' => 'title_typography', 'selector' => '{{WRAPPER}} .ta-promo-tile__title' ] );

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$tiles    = $settings['tiles'] ?? [];
		if ( ! $tiles ) {
			return;
		}

		$layout = in_array( $settings['layout'] ?? '', [ 'bento', 'bento-right', 'cols-2', 'cols-3', 'cols-4' ], true ) ? $settings['layout'] : 'bento';
		$tag    = in_array( $settings['title_tag'] ?? '', [ 'h2', 'h3', 'h4', 'p' ], true ) ? $settings['title_tag'] : 'h3';
		$class  = 'ta-promo-grid ta-promo-grid--' . $layout . ( 'yes' === ( $settings['hover_zoom'] ?? '' ) ? ' ta-promo-grid--zoom' : '' );
		?>
		<div class="<?php echo esc_attr( $class ); ?>">
			<?php
			foreach ( $tiles as $index => $tile ) :
				$url      = $tile['link']['url'] ?? '';
				$position = in_array( $tile['position'] ?? '', [ 'bottom-left', 'bottom-center', 'center', 'top-left' ], true ) ? $tile['position'] : 'bottom-left';
				$link_key = 'tile_link_' . $index;
				$this->add_render_attribute( $link_key, 'class', [ 'ta-promo-tile', 'ta-promo-tile--' . $position ] );
				if ( $url ) {
					$this->add_link_attributes( $link_key, $tile['link'] );
				}
				$element = $url ? 'a' : 'div';
				?>
				<<?php echo esc_attr( $element ); ?> <?php $this->print_render_attribute_string( $link_key ); ?>>
					<?php
					if ( ! empty( $tile['image']['id'] ) ) {
						echo wp_get_attachment_image( (int) $tile['image']['id'], 'full', false, [ 'class' => 'ta-promo-tile__img', 'loading' => 0 === $index ? 'eager' : 'lazy', 'sizes' => '(max-width: 767px) 100vw, 50vw' ] );
					} elseif ( ! empty( $tile['image']['url'] ) ) {
						printf( '<img class="ta-promo-tile__img" src="%s" alt="" loading="lazy">', esc_url( $tile['image']['url'] ) );
					}
					?>
					<span class="ta-promo-tile__overlay" aria-hidden="true"></span>
					<span class="ta-promo-tile__content">
						<?php if ( ! empty( $tile['eyebrow'] ) ) : ?>
							<span class="ta-promo-tile__eyebrow"><?php echo esc_html( $tile['eyebrow'] ); ?></span>
						<?php endif; ?>
						<?php if ( ! empty( $tile['title'] ) ) : ?>
							<<?php echo esc_attr( $tag ); ?> class="ta-promo-tile__title"><?php echo esc_html( $tile['title'] ); ?></<?php echo esc_attr( $tag ); ?>>
						<?php endif; ?>
						<?php if ( ! empty( $tile['description'] ) ) : ?>
							<span class="ta-promo-tile__text"><?php echo esc_html( $tile['description'] ); ?></span>
						<?php endif; ?>
						<?php if ( $url && ! empty( $tile['button_text'] ) ) : ?>
							<span class="ta-promo-tile__cta"><?php echo esc_html( $tile['button_text'] ); ?></span>
						<?php endif; ?>
					</span>
				</<?php echo esc_attr( $element ); ?>>
			<?php endforeach; ?>
		</div>
		<?php
	}
}
