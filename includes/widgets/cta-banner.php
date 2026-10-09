<?php
namespace ToolsAdapter\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use ToolsAdapter\Base_Widget;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Widget: Bannière CTA — image partagée, image plein fond, centrée ou carte d'accent.
 */
class Cta_Banner extends Base_Widget {

	public function get_name() {
		return 'tools-adapter-cta-banner';
	}

	public function get_title() {
		return esc_html__( 'Bannière CTA (styles)', 'tools-adapter' );
	}

	public function get_icon() {
		return 'eicon-call-to-action';
	}

	public function get_categories() {
		return [ 'tools-adapter' ];
	}

	public function get_keywords() {
		return [ 'cta', 'bannière', 'appel', 'action', 'promo', 'newsletter', 'image' ];
	}

	public function get_style_depends() {
		return [ 'tools-adapter-cta-banner' ];
	}

	protected function register_controls() {
		$this->start_controls_section( 'section_content', [ 'label' => esc_html__( 'Contenu', 'tools-adapter' ) ] );

		$this->add_control(
			'style',
			[
				'label'   => esc_html__( 'Style', 'tools-adapter' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'split',
				'options' => [
					'split'    => esc_html__( 'Image partagée (moitié image, moitié texte)', 'tools-adapter' ),
					'overlay'  => esc_html__( 'Image plein fond', 'tools-adapter' ),
					'centered' => esc_html__( 'Centré (sans image)', 'tools-adapter' ),
					'accent'   => esc_html__( 'Carte d\'accent (image à droite)', 'tools-adapter' ),
				],
			]
		);

		$this->add_control( 'image', [ 'label' => esc_html__( 'Image', 'tools-adapter' ), 'type' => Controls_Manager::MEDIA, 'condition' => [ 'style!' => 'centered' ] ] );
		$this->add_control( 'reverse', [ 'label' => esc_html__( 'Image à droite', 'tools-adapter' ), 'type' => Controls_Manager::SWITCHER, 'return_value' => 'yes', 'default' => '', 'condition' => [ 'style' => 'split' ] ] );
		$this->add_control( 'eyebrow', [ 'label' => esc_html__( 'Sur-titre', 'tools-adapter' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'Offre de bienvenue', 'tools-adapter' ) ] );
		$this->add_control( 'title', [ 'label' => esc_html__( 'Titre', 'tools-adapter' ), 'type' => Controls_Manager::TEXTAREA, 'rows' => 2, 'default' => esc_html__( '-10 % sur votre première commande', 'tools-adapter' ) ] );
		$this->add_control( 'text', [ 'label' => esc_html__( 'Texte', 'tools-adapter' ), 'type' => Controls_Manager::TEXTAREA, 'rows' => 3, 'default' => esc_html__( 'Créez votre compte et recevez votre code par e-mail.', 'tools-adapter' ) ] );
		$this->add_control( 'title_tag', [ 'label' => esc_html__( 'Balise du titre', 'tools-adapter' ), 'type' => Controls_Manager::SELECT, 'default' => 'h2', 'options' => [ 'h2' => 'H2', 'h3' => 'H3', 'p' => 'p' ] ] );
		$this->add_control( 'btn_text', [ 'label' => esc_html__( 'Bouton principal', 'tools-adapter' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'J\'en profite', 'tools-adapter' ), 'separator' => 'before' ] );
		$this->add_control( 'btn_link', [ 'label' => esc_html__( 'Lien du bouton principal', 'tools-adapter' ), 'type' => Controls_Manager::URL, 'default' => [ 'url' => '#' ] ] );
		$this->add_control( 'btn2_text', [ 'label' => esc_html__( 'Bouton secondaire', 'tools-adapter' ), 'type' => Controls_Manager::TEXT ] );
		$this->add_control( 'btn2_link', [ 'label' => esc_html__( 'Lien du bouton secondaire', 'tools-adapter' ), 'type' => Controls_Manager::URL ] );
		$this->add_control(
			'align',
			[
				'label'   => esc_html__( 'Alignement du texte', 'tools-adapter' ),
				'type'    => Controls_Manager::CHOOSE,
				'default' => 'left',
				'options' => [
					'left'   => [ 'title' => esc_html__( 'Gauche', 'tools-adapter' ), 'icon' => 'eicon-text-align-left' ],
					'center' => [ 'title' => esc_html__( 'Centré', 'tools-adapter' ), 'icon' => 'eicon-text-align-center' ],
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section( 'section_style', [ 'label' => esc_html__( 'Style', 'tools-adapter' ), 'tab' => Controls_Manager::TAB_STYLE ] );
		$this->add_responsive_control( 'min_height', [ 'label' => esc_html__( 'Hauteur minimale', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'size_units' => [ 'px', 'vh' ], 'range' => [ 'px' => [ 'min' => 160, 'max' => 900 ] ], 'default' => [ 'size' => 480, 'unit' => 'px' ], 'selectors' => [ '{{WRAPPER}} .ta-ctab' => '--ta-ctab-min-h: {{SIZE}}{{UNIT}};' ] ] );
		$this->add_control( 'bg', [ 'label' => esc_html__( 'Fond', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#f3eee8', 'selectors' => [ '{{WRAPPER}} .ta-ctab' => '--ta-ctab-bg: {{VALUE}};' ] ] );
		$this->add_control( 'color', [ 'label' => esc_html__( 'Texte', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#1c1c1c', 'selectors' => [ '{{WRAPPER}} .ta-ctab' => '--ta-ctab-color: {{VALUE}};' ] ] );
		$this->add_control( 'btn_bg', [ 'label' => esc_html__( 'Bouton — fond', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#1c1c1c', 'selectors' => [ '{{WRAPPER}} .ta-ctab' => '--ta-ctab-btn-bg: {{VALUE}};' ] ] );
		$this->add_control( 'btn_color', [ 'label' => esc_html__( 'Bouton — texte', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#ffffff', 'selectors' => [ '{{WRAPPER}} .ta-ctab' => '--ta-ctab-btn-color: {{VALUE}};' ] ] );
		$this->add_control( 'overlay', [ 'label' => esc_html__( 'Voile (image plein fond)', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => 'rgba(0,0,0,0.45)', 'selectors' => [ '{{WRAPPER}} .ta-ctab' => '--ta-ctab-overlay: {{VALUE}};' ], 'condition' => [ 'style' => 'overlay' ] ] );
		$this->add_control( 'radius', [ 'label' => esc_html__( 'Arrondi', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'range' => [ 'px' => [ 'min' => 0, 'max' => 48 ] ], 'default' => [ 'size' => 0, 'unit' => 'px' ], 'selectors' => [ '{{WRAPPER}} .ta-ctab' => '--ta-ctab-radius: {{SIZE}}{{UNIT}};' ] ] );
		$this->add_control( 'btn_radius', [ 'label' => esc_html__( 'Arrondi des boutons', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'range' => [ 'px' => [ 'min' => 0, 'max' => 40 ] ], 'default' => [ 'size' => 0, 'unit' => 'px' ], 'selectors' => [ '{{WRAPPER}} .ta-ctab' => '--ta-ctab-btn-radius: {{SIZE}}{{UNIT}};' ] ] );
		$this->add_group_control( Group_Control_Typography::get_type(), [ 'name' => 'title_typography', 'selector' => '{{WRAPPER}} .ta-ctab__title' ] );
		$this->end_controls_section();
	}

	protected function render() {
		$s     = $this->get_settings_for_display();
		$style = in_array( $s['style'] ?? '', [ 'split', 'overlay', 'centered', 'accent' ], true ) ? $s['style'] : 'split';
		$tag   = in_array( $s['title_tag'] ?? '', [ 'h2', 'h3', 'p' ], true ) ? $s['title_tag'] : 'h2';
		$align = 'center' === ( $s['align'] ?? '' ) || 'centered' === $style ? 'center' : 'left';

		$classes = [ 'ta-ctab', 'ta-ctab--' . $style, 'ta-ctab--align-' . $align ];
		if ( 'split' === $style && 'yes' === ( $s['reverse'] ?? '' ) ) {
			$classes[] = 'ta-ctab--reverse';
		}

		$image = '';
		if ( 'centered' !== $style ) {
			if ( ! empty( $s['image']['id'] ) ) {
				$image = wp_get_attachment_image( (int) $s['image']['id'], 'full', false, [ 'class' => 'ta-ctab__img', 'loading' => 'lazy', 'sizes' => '(max-width: 767px) 100vw, 60vw' ] );
			} elseif ( ! empty( $s['image']['url'] ) ) {
				$image = sprintf( '<img class="ta-ctab__img" src="%s" alt="" loading="lazy">', esc_url( $s['image']['url'] ) );
			}
		}

		foreach ( [ 'btn_link' => 'btn', 'btn2_link' => 'btn2' ] as $key => $attr ) {
			if ( ! empty( $s[ $key ]['url'] ) ) {
				$this->add_link_attributes( $attr, $s[ $key ] );
			}
		}
		$this->add_render_attribute( 'btn', 'class', 'ta-ctab__btn ta-ctab__btn--primary' );
		$this->add_render_attribute( 'btn2', 'class', 'ta-ctab__btn ta-ctab__btn--secondary' );
		?>
		<div class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
			<?php if ( $image ) : ?>
				<div class="ta-ctab__media"><?php echo $image; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core markup / escaped URL. ?></div>
			<?php endif; ?>
			<div class="ta-ctab__content">
				<?php if ( ! empty( $s['eyebrow'] ) ) : ?>
					<span class="ta-ctab__eyebrow"><?php echo esc_html( $s['eyebrow'] ); ?></span>
				<?php endif; ?>
				<?php if ( ! empty( $s['title'] ) ) : ?>
					<<?php echo esc_attr( $tag ); ?> class="ta-ctab__title"><?php echo esc_html( $s['title'] ); ?></<?php echo esc_attr( $tag ); ?>>
				<?php endif; ?>
				<?php if ( ! empty( $s['text'] ) ) : ?>
					<p class="ta-ctab__text"><?php echo esc_html( $s['text'] ); ?></p>
				<?php endif; ?>
				<?php if ( ! empty( $s['btn_text'] ) || ! empty( $s['btn2_text'] ) ) : ?>
					<div class="ta-ctab__buttons">
						<?php if ( ! empty( $s['btn_text'] ) ) : ?>
							<a <?php $this->print_render_attribute_string( 'btn' ); ?>><?php echo esc_html( $s['btn_text'] ); ?></a>
						<?php endif; ?>
						<?php if ( ! empty( $s['btn2_text'] ) ) : ?>
							<a <?php $this->print_render_attribute_string( 'btn2' ); ?>><?php echo esc_html( $s['btn2_text'] ); ?></a>
						<?php endif; ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}
}
