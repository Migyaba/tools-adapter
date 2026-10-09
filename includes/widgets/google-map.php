<?php
namespace ToolsAdapter\Widgets;

use Elementor\Controls_Manager;
use ToolsAdapter\Base_Widget;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Widget: Carte Google Maps stylisée — intégration par adresse (sans clé
 * API) ou via un code d'intégration personnalisé, avec carte d'infos flottante.
 */
class Google_Map extends Base_Widget {

	public function get_name() {
		return 'tools-adapter-google-map';
	}

	public function get_title() {
		return esc_html__( 'Carte Google Maps', 'tools-adapter' );
	}

	public function get_icon() {
		return 'eicon-google-maps';
	}

	public function get_categories() {
		return [ 'tools-adapter' ];
	}

	public function get_keywords() {
		return [ 'carte', 'map', 'google maps', 'adresse', 'localisation' ];
	}

	public function get_style_depends() {
		return [ 'tools-adapter-google-map' ];
	}

	protected function register_controls() {
		$this->start_controls_section( 'section_content', [ 'label' => esc_html__( 'Carte', 'tools-adapter' ) ] );

		$this->add_control(
			'source',
			[
				'label'   => esc_html__( 'Source', 'tools-adapter' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'address',
				'options' => [
					'address' => esc_html__( 'Adresse (sans clé API)', 'tools-adapter' ),
					'embed'   => esc_html__( 'Code d\'intégration personnalisé', 'tools-adapter' ),
				],
			]
		);

		$this->add_control(
			'address',
			[
				'label'     => esc_html__( 'Adresse', 'tools-adapter' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( '1 Rue de la Paix, 75002 Paris, France', 'tools-adapter' ),
				'condition' => [ 'source' => 'address' ],
			]
		);

		$this->add_control(
			'zoom',
			[
				'label'     => esc_html__( 'Zoom', 'tools-adapter' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 15,
				'min'       => 1,
				'max'       => 20,
				'condition' => [ 'source' => 'address' ],
			]
		);

		$this->add_control(
			'embed_url',
			[
				'label'       => esc_html__( 'URL d\'intégration', 'tools-adapter' ),
				'description' => esc_html__( 'Depuis Google Maps → Partager → Intégrer une carte → copiez l\'URL du champ src="…".', 'tools-adapter' ),
				'type'        => Controls_Manager::TEXT,
				'condition'   => [ 'source' => 'embed' ],
			]
		);

		$this->add_control( 'grayscale', [ 'label' => esc_html__( 'Effet noir & blanc', 'tools-adapter' ), 'type' => Controls_Manager::SWITCHER, 'return_value' => 'yes', 'default' => '', 'separator' => 'before' ] );

		$this->add_responsive_control(
			'map_height',
			[
				'label'      => esc_html__( 'Hauteur', 'tools-adapter' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'vh' ],
				'range'      => [ 'px' => [ 'min' => 200, 'max' => 900 ] ],
				'default'    => [ 'size' => 420, 'unit' => 'px' ],
				'selectors'  => [ '{{WRAPPER}} .ta-google-map' => 'height: {{SIZE}}{{UNIT}};' ],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section( 'section_card', [ 'label' => esc_html__( 'Carte d\'informations', 'tools-adapter' ) ] );

		$this->add_control( 'show_card', [ 'label' => esc_html__( 'Afficher', 'tools-adapter' ), 'type' => Controls_Manager::SWITCHER, 'return_value' => 'yes', 'default' => 'yes' ] );
		$this->add_control( 'card_title', [ 'label' => esc_html__( 'Titre', 'tools-adapter' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'Notre boutique', 'tools-adapter' ), 'condition' => [ 'show_card' => 'yes' ] ] );
		$this->add_control( 'card_address', [ 'label' => esc_html__( 'Adresse affichée', 'tools-adapter' ), 'type' => Controls_Manager::TEXTAREA, 'rows' => 2, 'default' => esc_html__( '1 Rue de la Paix, 75002 Paris', 'tools-adapter' ), 'condition' => [ 'show_card' => 'yes' ] ] );
		$this->add_control( 'card_hours', [ 'label' => esc_html__( 'Horaires', 'tools-adapter' ), 'type' => Controls_Manager::TEXTAREA, 'rows' => 3, 'default' => esc_html__( "Lun–Ven : 9h–19h\nSam : 10h–18h", 'tools-adapter' ), 'condition' => [ 'show_card' => 'yes' ] ] );
		$this->add_control( 'card_button_text', [ 'label' => esc_html__( 'Texte du bouton', 'tools-adapter' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'Itinéraire', 'tools-adapter' ), 'condition' => [ 'show_card' => 'yes' ] ] );
		$this->add_control( 'card_button_url', [ 'label' => esc_html__( 'URL du bouton', 'tools-adapter' ), 'type' => Controls_Manager::URL, 'condition' => [ 'show_card' => 'yes' ] ] );

		$this->add_control(
			'card_position',
			[
				'label'        => esc_html__( 'Position', 'tools-adapter' ),
				'type'         => Controls_Manager::CHOOSE,
				'options'      => [
					'bottom-left'  => [ 'title' => esc_html__( 'Bas gauche', 'tools-adapter' ), 'icon' => 'eicon-h-align-left' ],
					'bottom-right' => [ 'title' => esc_html__( 'Bas droite', 'tools-adapter' ), 'icon' => 'eicon-h-align-right' ],
					'top-left'     => [ 'title' => esc_html__( 'Haut gauche', 'tools-adapter' ), 'icon' => 'eicon-v-align-top' ],
					'top-right'    => [ 'title' => esc_html__( 'Haut droite', 'tools-adapter' ), 'icon' => 'eicon-v-align-top' ],
				],
				'default'      => 'bottom-left',
				'prefix_class' => 'ta-map-card-pos--',
				'condition'    => [ 'show_card' => 'yes' ],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section( 'section_style', [ 'label' => esc_html__( 'Style', 'tools-adapter' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_control( 'map_radius', [ 'label' => esc_html__( 'Arrondi de la carte', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'range' => [ 'px' => [ 'min' => 0, 'max' => 60 ] ], 'selectors' => [ '{{WRAPPER}} .ta-google-map' => 'border-radius: {{SIZE}}{{UNIT}}; overflow: hidden;' ] ] );
		$this->add_group_control( Group_Control_Box_Shadow::get_type(), [ 'name' => 'map_shadow', 'selector' => '{{WRAPPER}} .ta-google-map' ] );

		$this->add_control( 'card_heading', [ 'label' => esc_html__( 'Carte d\'informations', 'tools-adapter' ), 'type' => Controls_Manager::HEADING, 'separator' => 'before' ] );
		$this->add_control( 'card_bg', [ 'label' => esc_html__( 'Fond', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#ffffff', 'selectors' => [ '{{WRAPPER}} .ta-google-map__card' => 'background-color: {{VALUE}};' ] ] );
		$this->add_control( 'card_title_color', [ 'label' => esc_html__( 'Couleur du titre', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#1c1c1c', 'selectors' => [ '{{WRAPPER}} .ta-google-map__card-title' => 'color: {{VALUE}};' ] ] );
		$this->add_group_control( Group_Control_Typography::get_type(), [ 'name' => 'card_title_typography', 'selector' => '{{WRAPPER}} .ta-google-map__card-title' ] );
		$this->add_control( 'card_text_color', [ 'label' => esc_html__( 'Couleur du texte', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#666666', 'selectors' => [ '{{WRAPPER}} .ta-google-map__card-text' => 'color: {{VALUE}};' ] ] );
		$this->add_control( 'card_button_bg', [ 'label' => esc_html__( 'Fond du bouton', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#C9A84C', 'separator' => 'before', 'selectors' => [ '{{WRAPPER}} .ta-google-map__card-btn' => 'background-color: {{VALUE}};' ] ] );
		$this->add_control( 'card_button_color', [ 'label' => esc_html__( 'Texte du bouton', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#1c1c1c', 'selectors' => [ '{{WRAPPER}} .ta-google-map__card-btn' => 'color: {{VALUE}};' ] ] );
		$this->add_control( 'card_radius', [ 'label' => esc_html__( 'Arrondi', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'range' => [ 'px' => [ 'min' => 0, 'max' => 40 ] ], 'separator' => 'before', 'selectors' => [ '{{WRAPPER}} .ta-google-map__card' => 'border-radius: {{SIZE}}{{UNIT}};' ] ] );
		$this->add_group_control( Group_Control_Box_Shadow::get_type(), [ 'name' => 'card_shadow', 'selector' => '{{WRAPPER}} .ta-google-map__card' ] );

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$source   = $settings['source'] ?? 'address';

		if ( 'embed' === $source ) {
			$src = trim( $settings['embed_url'] ?? '' );
		} else {
			$address = $settings['address'] ?? '';
			if ( '' === $address ) {
				if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
					echo '<p>' . esc_html__( 'Renseignez une adresse pour afficher la carte.', 'tools-adapter' ) . '</p>';
				}
				return;
			}
			$zoom = absint( $settings['zoom'] ?? 15 );
			$src  = 'https://maps.google.com/maps?q=' . rawurlencode( $address ) . '&z=' . $zoom . '&output=embed';
		}

		if ( '' === $src ) {
			if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
				echo '<p>' . esc_html__( 'Renseignez une URL d\'intégration valide.', 'tools-adapter' ) . '</p>';
			}
			return;
		}

		$show_card   = 'yes' === ( $settings['show_card'] ?? '' );
		$button_url  = $settings['card_button_url']['url'] ?? '';
		?>
		<div class="ta-google-map-wrap">
			<div class="ta-google-map<?php echo 'yes' === ( $settings['grayscale'] ?? '' ) ? ' ta-google-map--grayscale' : ''; ?>">
				<iframe
					src="<?php echo esc_url( $src ); ?>"
					width="100%"
					height="100%"
					style="border:0;"
					loading="lazy"
					referrerpolicy="no-referrer-when-downgrade"
					title="<?php echo esc_attr( $settings['card_title'] ?? esc_html__( 'Carte', 'tools-adapter' ) ); ?>"
				></iframe>

				<?php if ( $show_card ) : ?>
					<div class="ta-google-map__card">
						<?php if ( ! empty( $settings['card_title'] ) ) : ?><p class="ta-google-map__card-title"><?php echo esc_html( \tools_adapter_translate( $settings['card_title'] ) ); ?></p><?php endif; ?>
						<?php if ( ! empty( $settings['card_address'] ) ) : ?><p class="ta-google-map__card-text"><?php echo esc_html( \tools_adapter_translate( $settings['card_address'] ) ); ?></p><?php endif; ?>
						<?php if ( ! empty( $settings['card_hours'] ) ) : ?><p class="ta-google-map__card-text ta-google-map__card-hours"><?php echo wp_kses_post( nl2br( esc_html( \tools_adapter_translate( $settings['card_hours'] ) ) ) ); ?></p><?php endif; ?>
						<?php if ( ! empty( $settings['card_button_text'] ) && ! empty( $button_url ) ) : ?>
							<a class="ta-google-map__card-btn" href="<?php echo esc_url( $button_url ); ?>" target="_blank" rel="noopener"><?php echo esc_html( \tools_adapter_translate( $settings['card_button_text'] ) ); ?></a>
						<?php endif; ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}
}
