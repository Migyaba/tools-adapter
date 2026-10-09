<?php
namespace ToolsAdapter\Widgets;

use Elementor\Controls_Manager;
use ToolsAdapter\Base_Widget;
use ToolsAdapter\Repeater;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Widget: Témoignages — grille ou carrousel d'avis clients multi-cartes ultra-personnalisable.
 */
class Testimonials extends Base_Widget {

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
		return [ 'témoignages', 'avis', 'clients', 'testimonial', 'review', 'carrousel', 'etoiles' ];
	}

	public function get_style_depends() {
		return [ 'tools-adapter-testimonials' ];
	}

	public function get_script_depends() {
		return [ 'tools-adapter-carousel' ];
	}

	protected function register_controls() {
		// ==========================================
		// SECTION CONTENU : TÉMOIGNAGES
		// ==========================================
		$this->start_controls_section( 'section_content', [ 'label' => esc_html__( 'Avis Clients', 'tools-adapter' ) ] );

		$repeater = new Repeater();
		$repeater->add_control(
			'photo',
			[
				'label'   => esc_html__( 'Photo / Avatar', 'tools-adapter' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => [ 'url' => \Elementor\Utils::get_placeholder_image_src() ],
			]
		);
		$repeater->add_control(
			'name',
			[
				'label'   => esc_html__( 'Nom du client', 'tools-adapter' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Sophie M.', 'tools-adapter' ),
			]
		);
		$repeater->add_control(
			'role',
			[
				'label'   => esc_html__( 'Localisation / Statut', 'tools-adapter' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Client particulier, Paris 16e', 'tools-adapter' ),
			]
		);
		$repeater->add_control(
			'quote',
			[
				'label'   => esc_html__( 'Témoignage', 'tools-adapter' ),
				'type'    => Controls_Manager::TEXTAREA,
				'rows'    => 4,
				'default' => esc_html__( 'Une équipe à l\'écoute, un rendu qui dépasse nos attentes. Notre terrasse est devenue une vraie pièce à vivre.', 'tools-adapter' ),
			]
		);
		$repeater->add_control(
			'rating',
			[
				'label'   => esc_html__( 'Note (0-5)', 'tools-adapter' ),
				'type'    => Controls_Manager::NUMBER,
				'min'     => 0,
				'max'     => 5,
				'step'    => 0.5,
				'default' => 5,
			]
		);

		$this->add_control(
			'testimonials',
			[
				'label'       => esc_html__( 'Liste des avis', 'tools-adapter' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => [
					[
						'name'   => esc_html__( 'Sophie M.', 'tools-adapter' ),
						'role'   => esc_html__( 'Client particulier, Paris 16e', 'tools-adapter' ),
						'quote'  => esc_html__( 'Une équipe à l\'écoute, un rendu qui dépasse nos attentes. Notre terrasse est devenue une vraie pièce à vivre.', 'tools-adapter' ),
						'rating' => 5,
					],
					[
						'name'   => esc_html__( 'Marc D.', 'tools-adapter' ),
						'role'   => esc_html__( 'Client particulier, Levallois-Perret', 'tools-adapter' ),
						'quote'  => esc_html__( 'Un accompagnement complet, du plan à l\'entretien saisonnier. Exactement ce que nous cherchions.', 'tools-adapter' ),
						'rating' => 5,
					],
					[
						'name'   => esc_html__( 'Isabelle R.', 'tools-adapter' ),
						'role'   => esc_html__( 'Client particulier, Levallois-Perret', 'tools-adapter' ),
						'quote'  => esc_html__( 'Des délais tenus, un chantier propre et une équipe très professionnelle du début à la fin.', 'tools-adapter' ),
						'rating' => 5,
					],
					[
						'name'   => esc_html__( 'Julien T.', 'tools-adapter' ),
						'role'   => esc_html__( 'Architecte d\'intérieur, Paris 7e', 'tools-adapter' ),
						'quote'  => esc_html__( 'Collaboration fluide, respect des matériaux nobles et finitions irréprochables sur l\'ensemble du chantier.', 'tools-adapter' ),
						'rating' => 5,
					],
				],
				'title_field' => '{{{ name }}}',
			]
		);

		$this->end_controls_section();

		// ==========================================
		// SECTION CONTENU : DISPOSITION & FORMAT
		// ==========================================
		$this->start_controls_section( 'section_layout', [ 'label' => esc_html__( 'Disposition & Carrousel', 'tools-adapter' ) ] );

		$this->add_control(
			'layout',
			[
				'label'        => esc_html__( 'Mode d\'affichage', 'tools-adapter' ),
				'type'         => Controls_Manager::SELECT,
				'default'      => 'carousel',
				'options'      => [
					'carousel' => esc_html__( 'Carrousel défilant', 'tools-adapter' ),
					'grid'     => esc_html__( 'Grille statique', 'tools-adapter' ),
				],
				'prefix_class' => 'ta-testimonials--',
			]
		);

		// Nombre de colonnes / slides visibles (responsive)
		$this->add_responsive_control(
			'slides_to_show',
			[
				'label'          => esc_html__( 'Avis visibles en simultané', 'tools-adapter' ),
				'type'           => Controls_Manager::NUMBER,
				'min'            => 1,
				'max'            => 6,
				'default'        => 3,
				'tablet_default' => 2,
				'mobile_default' => 1,
				'condition'      => [ 'layout' => 'carousel' ],
			]
		);

		$this->add_responsive_control(
			'columns',
			[
				'label'          => esc_html__( 'Colonnes', 'tools-adapter' ),
				'type'           => Controls_Manager::NUMBER,
				'min'            => 1,
				'max'            => 6,
				'default'        => 3,
				'tablet_default' => 2,
				'mobile_default' => 1,
				'condition'      => [ 'layout' => 'grid' ],
				'selectors'      => [ '{{WRAPPER}} .ta-testimonials__grid' => '--ta-cols: {{VALUE}};' ],
			]
		);

		$this->add_responsive_control(
			'items_gap',
			[
				'label'      => esc_html__( 'Espacement entre cartes', 'tools-adapter' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => [ 'px' => [ 'min' => 0, 'max' => 60 ] ],
				'default'    => [ 'size' => 24, 'unit' => 'px' ],
				'selectors'  => [ '{{WRAPPER}} .ta-testimonials' => '--ta-gap: {{SIZE}}{{UNIT}};' ],
			]
		);

		// Ordre des éléments dans la carte
		$this->add_control(
			'card_structure',
			[
				'label'   => esc_html__( 'Structure de la carte', 'tools-adapter' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'stars_top',
				'options' => [
					'stars_top'  => esc_html__( 'Étoiles en haut, citation au milieu, auteur en bas', 'tools-adapter' ),
					'quote_top'  => esc_html__( 'Citation en haut, étoiles au milieu, auteur en bas', 'tools-adapter' ),
				],
			]
		);

		$this->add_control(
			'quote_italic',
			[
				'label'        => esc_html__( 'Citation en italique', 'tools-adapter' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'quote_marks',
			[
				'label'   => esc_html__( 'Guillemets de citation', 'tools-adapter' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'french',
				'options' => [
					'french'  => '« … » (Français)',
					'english' => '“ … ” (Anglais)',
					'none'    => esc_html__( 'Aucun guillemet', 'tools-adapter' ),
				],
			]
		);

		$this->add_control(
			'show_rating',
			[
				'label'        => esc_html__( 'Afficher la note (étoiles)', 'tools-adapter' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'show_quote_icon',
			[
				'label'        => esc_html__( 'Gros guillemet décoratif en fond', 'tools-adapter' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'no',
			]
		);

		$this->add_control(
			'avatar_shape',
			[
				'label'   => esc_html__( 'Forme de la photo auteur', 'tools-adapter' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'circle',
				'options' => [
					'circle'  => esc_html__( 'Cercle (rond)', 'tools-adapter' ),
					'rounded' => esc_html__( 'Carré arrondi', 'tools-adapter' ),
					'square'  => esc_html__( 'Carré droit', 'tools-adapter' ),
				],
			]
		);

		$this->end_controls_section();

		// ==========================================
		// SECTION CONTENU : PARAMÈTRES CARROUSEL
		// ==========================================
		$this->start_controls_section(
			'section_carousel_settings',
			[
				'label'     => esc_html__( 'Contrôles du Carrousel', 'tools-adapter' ),
				'condition' => [ 'layout' => 'carousel' ],
			]
		);

		$this->add_control(
			'show_dots',
			[
				'label'        => esc_html__( 'Puces de pagination', 'tools-adapter' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'dot_style',
			[
				'label'     => esc_html__( 'Style des puces', 'tools-adapter' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'ring',
				'options'   => [
					'ring'   => esc_html__( 'Anneau cerclé (comme la capture)', 'tools-adapter' ),
					'bullet' => esc_html__( 'Rond plein standard', 'tools-adapter' ),
					'pill'   => esc_html__( 'Pilule allongée', 'tools-adapter' ),
				],
				'condition' => [ 'show_dots' => 'yes' ],
			]
		);

		$this->add_control(
			'show_arrows',
			[
				'label'        => esc_html__( 'Flèches Précédent / Suivant', 'tools-adapter' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'no',
			]
		);

		$this->add_control(
			'autoplay',
			[
				'label'        => esc_html__( 'Lecture automatique', 'tools-adapter' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'autoplay_speed',
			[
				'label'     => esc_html__( 'Délai d\'affichage (ms)', 'tools-adapter' ),
				'type'      => Controls_Manager::NUMBER,
				'min'       => 1500,
				'max'       => 15000,
				'step'      => 500,
				'default'   => 5000,
				'condition' => [ 'autoplay' => 'yes' ],
			]
		);

		$this->end_controls_section();

		// ==========================================
		// SECTION STYLE : CARTE
		// ==========================================
		$this->start_controls_section( 'section_style_card', [ 'label' => esc_html__( 'Cartes', 'tools-adapter' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_control(
			'card_bg',
			[
				'label'     => esc_html__( 'Couleur de fond', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [ '{{WRAPPER}} .ta-testimonial' => '--ta-card-bg: {{VALUE}};' ],
			]
		);

		$this->add_responsive_control(
			'card_padding',
			[
				'label'      => esc_html__( 'Espacement interne (Padding)', 'tools-adapter' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'default'    => [ 'top' => '32', 'right' => '28', 'bottom' => '28', 'left' => '28', 'unit' => 'px' ],
				'selectors'  => [ '{{WRAPPER}} .ta-testimonial' => '--ta-card-padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
			]
		);

		$this->add_control(
			'card_radius',
			[
				'label'      => esc_html__( 'Arrondi des coins', 'tools-adapter' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => [ 'px' => [ 'min' => 0, 'max' => 50 ] ],
				'default'    => [ 'size' => 14, 'unit' => 'px' ],
				'selectors'  => [ '{{WRAPPER}} .ta-testimonial' => '--ta-card-radius: {{SIZE}}{{UNIT}};' ],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'card_shadow',
				'label'    => esc_html__( 'Ombre de la carte', 'tools-adapter' ),
				'selector' => '{{WRAPPER}} .ta-testimonial',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'card_border',
				'label'    => esc_html__( 'Bordure', 'tools-adapter' ),
				'selector' => '{{WRAPPER}} .ta-testimonial',
			]
		);

		$this->add_control(
			'hover_lift',
			[
				'label'        => esc_html__( 'Lévitation douce au survol', 'tools-adapter' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->end_controls_section();

		// ==========================================
		// SECTION STYLE : ÉTOILES
		// ==========================================
		$this->start_controls_section(
			'section_style_rating',
			[
				'label'     => esc_html__( 'Étoiles d\'évaluation', 'tools-adapter' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [ 'show_rating' => 'yes' ],
			]
		);

		$this->add_control(
			'rating_color',
			[
				'label'     => esc_html__( 'Couleur des étoiles', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#f59e0b',
				'selectors' => [ '{{WRAPPER}} .ta-testimonial' => '--ta-rating-color: {{VALUE}};' ],
			]
		);

		$this->add_responsive_control(
			'rating_size',
			[
				'label'      => esc_html__( 'Taille des étoiles', 'tools-adapter' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => [ 'px' => [ 'min' => 12, 'max' => 36 ] ],
				'default'    => [ 'size' => 18, 'unit' => 'px' ],
				'selectors'  => [ '{{WRAPPER}} .ta-testimonial' => '--ta-rating-size: {{SIZE}}{{UNIT}};' ],
			]
		);

		$this->add_responsive_control(
			'rating_spacing',
			[
				'label'      => esc_html__( 'Espacement entre étoiles', 'tools-adapter' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => [ 'px' => [ 'min' => 0, 'max' => 10 ] ],
				'default'    => [ 'size' => 3, 'unit' => 'px' ],
				'selectors'  => [ '{{WRAPPER}} .ta-testimonial' => '--ta-rating-spacing: {{SIZE}}{{UNIT}};' ],
			]
		);

		$this->end_controls_section();

		// ==========================================
		// SECTION STYLE : CITATION & TEXTES
		// ==========================================
		$this->start_controls_section( 'section_style_quote', [ 'label' => esc_html__( 'Citation', 'tools-adapter' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_control(
			'quote_color',
			[
				'label'     => esc_html__( 'Couleur de la citation', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#334155',
				'selectors' => [ '{{WRAPPER}} .ta-testimonial' => '--ta-quote-color: {{VALUE}};' ],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'quote_typography',
				'selector' => '{{WRAPPER}} .ta-testimonial__quote',
			]
		);

		$this->end_controls_section();

		// ==========================================
		// SECTION STYLE : AUTEUR
		// ==========================================
		$this->start_controls_section( 'section_style_author', [ 'label' => esc_html__( 'Auteur (Nom & Statut)', 'tools-adapter' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_responsive_control(
			'avatar_size',
			[
				'label'      => esc_html__( 'Taille de la photo', 'tools-adapter' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => [ 'px' => [ 'min' => 30, 'max' => 100 ] ],
				'default'    => [ 'size' => 48, 'unit' => 'px' ],
				'selectors'  => [ '{{WRAPPER}} .ta-testimonial' => '--ta-avatar-size: {{SIZE}}{{UNIT}};' ],
			]
		);

		$this->add_control(
			'name_color',
			[
				'label'     => esc_html__( 'Couleur du nom', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#0b2545',
				'selectors' => [ '{{WRAPPER}} .ta-testimonial' => '--ta-name-color: {{VALUE}};' ],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'name_typography',
				'selector' => '{{WRAPPER}} .ta-testimonial__name',
			]
		);

		$this->add_control(
			'role_color',
			[
				'label'     => esc_html__( 'Couleur du statut / localisation', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#64748b',
				'separator' => 'before',
				'selectors' => [ '{{WRAPPER}} .ta-testimonial' => '--ta-role-color: {{VALUE}};' ],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'role_typography',
				'selector' => '{{WRAPPER}} .ta-testimonial__role',
			]
		);

		$this->end_controls_section();

		// ==========================================
		// SECTION STYLE : NAVIGATION (CARROUSEL)
		// ==========================================
		$this->start_controls_section(
			'section_style_nav',
			[
				'label'     => esc_html__( 'Navigation Carrousel', 'tools-adapter' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [ 'layout' => 'carousel' ],
			]
		);

		$this->add_control(
			'dot_color',
			[
				'label'     => esc_html__( 'Couleur des puces inactives', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#7bc81d',
				'selectors' => [ '{{WRAPPER}} .ta-carousel__dots' => '--ta-dot-color: {{VALUE}};' ],
				'condition' => [ 'show_dots' => 'yes' ],
			]
		);

		$this->add_control(
			'dot_color_active',
			[
				'label'     => esc_html__( 'Couleur de la puce active', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#7bc81d',
				'selectors' => [ '{{WRAPPER}} .ta-carousel__dots' => '--ta-dot-active: {{VALUE}};' ],
				'condition' => [ 'show_dots' => 'yes' ],
			]
		);

		$this->add_control(
			'arrow_color',
			[
				'label'     => esc_html__( 'Couleur flèches', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#0b2545',
				'selectors' => [ '{{WRAPPER}} .ta-carousel__arrow' => '--ta-arrow-color: {{VALUE}};' ],
				'condition' => [ 'show_arrows' => 'yes' ],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * @param float $rating Rating value.
	 */
	private function render_stars( $rating ) {
		$rating = max( 0, min( 5, (float) $rating ) );
		echo '<div class="ta-testimonial__rating" aria-label="' . esc_attr( sprintf( __( 'Note : %s sur 5', 'tools-adapter' ), $rating ) ) . '">';
		for ( $i = 1; $i <= 5; $i++ ) {
			echo $i <= round( $rating ) ? '★' : '☆'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		echo '</div>';
	}

	protected function render() {
		$settings     = $this->get_settings_for_display();
		$testimonials = $settings['testimonials'] ?? [];
		if ( empty( $testimonials ) ) {
			return;
		}

		$is_carousel = 'carousel' === ( $settings['layout'] ?? 'carousel' );
		$is_hover_lift = ( $settings['hover_lift'] ?? 'yes' ) === 'yes';

		$wrapper_classes = [ 'ta-testimonials' ];
		if ( $is_carousel ) {
			$wrapper_classes[] = 'ta-testimonials--carousel';
		}
		if ( $is_hover_lift ) {
			$wrapper_classes[] = 'ta-testimonials--hover-lift';
		}

		$show_stars_top = ( $settings['card_structure'] ?? 'stars_top' ) === 'stars_top';
		$quote_italic   = ( $settings['quote_italic'] ?? 'yes' ) === 'yes';
		$quote_marks    = $settings['quote_marks'] ?? 'french';
		$avatar_shape   = $settings['avatar_shape'] ?? 'circle';
		$dot_style      = $settings['dot_style'] ?? 'ring';

		// Attributs multi-slides pour le JS
		$slides_desktop = ! empty( $settings['slides_to_show'] ) ? absint( $settings['slides_to_show'] ) : 3;
		$slides_tablet  = ! empty( $settings['slides_to_show_tablet'] ) ? absint( $settings['slides_to_show_tablet'] ) : 2;
		$slides_mobile  = ! empty( $settings['slides_to_show_mobile'] ) ? absint( $settings['slides_to_show_mobile'] ) : 1;
		$gap            = ! empty( $settings['items_gap']['size'] ) ? absint( $settings['items_gap']['size'] ) : 24;
		?>
		<div
			class="<?php echo esc_attr( implode( ' ', $wrapper_classes ) ); ?>"
			<?php if ( $is_carousel ) : ?>
				data-ta-carousel
				data-slides-show="<?php echo esc_attr( (string) $slides_desktop ); ?>"
				data-slides-show-tablet="<?php echo esc_attr( (string) $slides_tablet ); ?>"
				data-slides-show-mobile="<?php echo esc_attr( (string) $slides_mobile ); ?>"
				data-gap="<?php echo esc_attr( (string) $gap ); ?>"
				data-autoplay="<?php echo 'yes' === ( $settings['autoplay'] ?? '' ) ? '1' : '0'; ?>"
				data-autoplay-speed="<?php echo esc_attr( (string) intval( $settings['autoplay_speed'] ?? 5000 ) ); ?>"
			<?php endif; ?>
		>
			<div class="ta-testimonials__<?php echo $is_carousel ? 'track' : 'grid'; ?>" <?php echo $is_carousel ? 'data-carousel-track' : ''; ?>>
				<?php foreach ( $testimonials as $item ) : ?>
					<?php
					$quote_text = $item['quote'] ?? '';
					if ( $quote_text ) {
						$quote_text = \tools_adapter_translate( $quote_text );
						if ( 'french' === $quote_marks ) {
							$quote_text = '« ' . trim( $quote_text, "«» \t\n\r\0\x0B" ) . ' »';
						} elseif ( 'english' === $quote_marks ) {
							$quote_text = '“' . trim( $quote_text, "“”\" \t\n\r\0\x0B" ) . '”';
						}
					}
					?>
					<div class="ta-testimonial<?php echo $is_carousel ? ' ta-testimonial--slide' : ''; ?>" <?php echo $is_carousel ? 'data-carousel-slide' : ''; ?>>
						<?php if ( 'yes' === ( $settings['show_quote_icon'] ?? '' ) ) : ?>
							<span class="ta-testimonial__quote-icon" aria-hidden="true">&ldquo;</span>
						<?php endif; ?>

						<?php if ( $show_stars_top && 'yes' === ( $settings['show_rating'] ?? '' ) ) : ?>
							<?php $this->render_stars( $item['rating'] ?? 5 ); ?>
						<?php endif; ?>

						<?php if ( $quote_text ) : ?>
							<p class="ta-testimonial__quote<?php echo $quote_italic ? ' ta-testimonial__quote--italic' : ''; ?>">
								<?php echo esc_html( $quote_text ); ?>
							</p>
						<?php endif; ?>

						<?php if ( ! $show_stars_top && 'yes' === ( $settings['show_rating'] ?? '' ) ) : ?>
							<?php $this->render_stars( $item['rating'] ?? 5 ); ?>
						<?php endif; ?>

						<div class="ta-testimonial__author">
							<?php if ( ! empty( $item['photo']['url'] ) ) : ?>
								<img
									class="ta-testimonial__avatar ta-testimonial__avatar--<?php echo esc_attr( $avatar_shape ); ?>"
									src="<?php echo esc_url( $item['photo']['url'] ); ?>"
									alt="<?php echo esc_attr( $item['name'] ?? '' ); ?>"
									loading="lazy"
								/>
							<?php endif; ?>
							<div class="ta-testimonial__meta">
								<?php if ( ! empty( $item['name'] ) ) : ?>
									<p class="ta-testimonial__name"><?php echo esc_html( \tools_adapter_translate( $item['name'] ) ); ?></p>
								<?php endif; ?>
								<?php if ( ! empty( $item['role'] ) ) : ?>
									<p class="ta-testimonial__role"><?php echo esc_html( \tools_adapter_translate( $item['role'] ) ); ?></p>
								<?php endif; ?>
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
					<div class="ta-carousel__dots ta-carousel__dots--<?php echo esc_attr( $dot_style ); ?>" data-carousel-dots></div>
				<?php endif; ?>
			<?php endif; ?>
		</div>
		<?php
	}
}
