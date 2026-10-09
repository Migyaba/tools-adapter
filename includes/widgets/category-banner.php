<?php
namespace ToolsAdapter\Widgets;

use Elementor\Controls_Manager;
use ToolsAdapter\Base_Widget;
use ToolsAdapter\Products_Query;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Widget: Bannière catégorie — hero pour une catégorie WooCommerce.
 */
class Category_Banner extends Base_Widget {

	public function get_name() {
		return 'tools-adapter-category-banner';
	}

	public function get_title() {
		return esc_html__( 'Bannière catégorie', 'tools-adapter' );
	}

	public function get_icon() {
		return 'eicon-product-categories';
	}

	public function get_categories() {
		return [ 'tools-adapter', 'woocommerce-elements' ];
	}

	public function get_keywords() {
		return [ 'catégorie', 'bannière', 'hero', 'woocommerce', 'archive' ];
	}

	public function get_style_depends() {
		return [ 'tools-adapter-category-banner' ];
	}

	protected function register_controls() {
		$this->start_controls_section( 'section_content', [ 'label' => esc_html__( 'Contenu', 'tools-adapter' ) ] );

		$this->add_control(
			'source',
			[
				'label'   => esc_html__( 'Catégorie', 'tools-adapter' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'current',
				'options' => [
					'current' => esc_html__( 'Détecter automatiquement (page archive)', 'tools-adapter' ),
					'manual'  => esc_html__( 'Choisir une catégorie', 'tools-adapter' ),
				],
			]
		);

		$this->add_control(
			'category_id',
			[
				'label'     => esc_html__( 'Catégorie', 'tools-adapter' ),
				'type'      => Controls_Manager::SELECT2,
				'options'   => class_exists( 'WooCommerce' ) ? Products_Query::get_category_options() : [],
				'condition' => [ 'source' => 'manual' ],
			]
		);

		$this->add_control(
			'show_image',
			[
				'label'        => esc_html__( 'Image de fond', 'tools-adapter' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'image_fallback',
			[
				'label'        => esc_html__( 'Repli sur image produit', 'tools-adapter' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'description'  => esc_html__( 'Si la catégorie n\'a pas d\'image, utiliser celle d\'un de ses produits.', 'tools-adapter' ),
				'condition'    => [ 'show_image' => 'yes' ],
			]
		);

		$this->add_control(
			'show_description',
			[
				'label'        => esc_html__( 'Description de la catégorie', 'tools-adapter' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'show_count',
			[
				'label'        => esc_html__( 'Nombre de produits', 'tools-adapter' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'count_format',
			[
				'label'       => esc_html__( 'Format du compteur', 'tools-adapter' ),
				'description' => esc_html__( 'Utilisez %d pour le nombre.', 'tools-adapter' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( '%d produits', 'tools-adapter' ),
				'condition'   => [ 'show_count' => 'yes' ],
			]
		);

		$this->add_control(
			'show_button',
			[
				'label'        => esc_html__( 'Bouton', 'tools-adapter' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'button_text',
			[
				'label'     => esc_html__( 'Texte du bouton', 'tools-adapter' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'Voir la collection', 'tools-adapter' ),
				'condition' => [ 'show_button' => 'yes' ],
			]
		);

		$this->add_responsive_control(
			'min_height',
			[
				'label'      => esc_html__( 'Hauteur minimale', 'tools-adapter' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'vh' ],
				'range'      => [ 'px' => [ 'min' => 150, 'max' => 800 ], 'vh' => [ 'min' => 10, 'max' => 100 ] ],
				'default'    => [ 'size' => 320, 'unit' => 'px' ],
				'selectors'  => [ '{{WRAPPER}} .ta-cat-banner' => 'min-height: {{SIZE}}{{UNIT}};' ],
			]
		);

		$this->add_control(
			'content_align',
			[
				'label'        => esc_html__( 'Alignement', 'tools-adapter' ),
				'type'         => Controls_Manager::CHOOSE,
				'options'      => [
					'left'   => [ 'title' => esc_html__( 'Gauche', 'tools-adapter' ), 'icon' => 'eicon-text-align-left' ],
					'center' => [ 'title' => esc_html__( 'Centre', 'tools-adapter' ), 'icon' => 'eicon-text-align-center' ],
					'right'  => [ 'title' => esc_html__( 'Droite', 'tools-adapter' ), 'icon' => 'eicon-text-align-right' ],
				],
				'default'      => 'center',
				'prefix_class' => 'ta-cat-banner-align-',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section( 'section_style', [ 'label' => esc_html__( 'Style', 'tools-adapter' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_control( 'overlay_color', [ 'label' => esc_html__( 'Superposition', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => 'rgba(0,0,0,0.4)', 'selectors' => [ '{{WRAPPER}} .ta-cat-banner__overlay' => 'background-color: {{VALUE}};' ] ] );

		$this->add_control( 'title_heading', [ 'label' => esc_html__( 'Titre', 'tools-adapter' ), 'type' => Controls_Manager::HEADING, 'separator' => 'before' ] );
		$this->add_control( 'title_color', [ 'label' => esc_html__( 'Couleur', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#ffffff', 'selectors' => [ '{{WRAPPER}} .ta-cat-banner__title' => 'color: {{VALUE}};' ] ] );
		$this->add_group_control( Group_Control_Typography::get_type(), [ 'name' => 'title_typography', 'selector' => '{{WRAPPER}} .ta-cat-banner__title', 'fields_options' => [ 'font_size' => [ 'default' => [ 'size' => 36, 'unit' => 'px' ] ], 'font_weight' => [ 'default' => '700' ] ] ] );

		$this->add_control( 'description_heading', [ 'label' => esc_html__( 'Description', 'tools-adapter' ), 'type' => Controls_Manager::HEADING, 'separator' => 'before' ] );
		$this->add_control( 'description_color', [ 'label' => esc_html__( 'Couleur', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#eeeeee', 'selectors' => [ '{{WRAPPER}} .ta-cat-banner__description' => 'color: {{VALUE}};' ] ] );

		$this->add_control( 'count_heading', [ 'label' => esc_html__( 'Compteur', 'tools-adapter' ), 'type' => Controls_Manager::HEADING, 'separator' => 'before' ] );
		$this->add_control( 'count_color', [ 'label' => esc_html__( 'Couleur', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#C9A84C', 'selectors' => [ '{{WRAPPER}} .ta-cat-banner__count' => 'color: {{VALUE}};' ] ] );

		$this->add_control( 'button_heading', [ 'label' => esc_html__( 'Bouton', 'tools-adapter' ), 'type' => Controls_Manager::HEADING, 'separator' => 'before' ] );
		$this->add_control( 'button_color', [ 'label' => esc_html__( 'Texte', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#1c1c1c', 'selectors' => [ '{{WRAPPER}} .ta-cat-banner__btn' => 'color: {{VALUE}};' ] ] );
		$this->add_control( 'button_bg', [ 'label' => esc_html__( 'Fond', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#ffffff', 'selectors' => [ '{{WRAPPER}} .ta-cat-banner__btn' => 'background-color: {{VALUE}};' ] ] );
		$this->add_control( 'button_radius', [ 'label' => esc_html__( 'Arrondi', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'range' => [ 'px' => [ 'min' => 0, 'max' => 50 ] ], 'default' => [ 'size' => 6, 'unit' => 'px' ], 'selectors' => [ '{{WRAPPER}} .ta-cat-banner__btn' => 'border-radius: {{SIZE}}{{UNIT}};' ] ] );

		$this->add_group_control( Group_Control_Border::get_type(), [ 'name' => 'banner_border', 'selector' => '{{WRAPPER}} .ta-cat-banner', 'separator' => 'before' ] );
		$this->add_control( 'banner_radius', [ 'label' => esc_html__( 'Arrondi du bloc', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'range' => [ 'px' => [ 'min' => 0, 'max' => 60 ] ], 'selectors' => [ '{{WRAPPER}} .ta-cat-banner' => 'border-radius: {{SIZE}}{{UNIT}}; overflow: hidden;' ] ] );
		$this->add_group_control( Group_Control_Box_Shadow::get_type(), [ 'name' => 'banner_shadow', 'selector' => '{{WRAPPER}} .ta-cat-banner' ] );

		$this->end_controls_section();
	}

	protected function render() {
		if ( ! class_exists( 'WooCommerce' ) ) {
			if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
				echo '<p>' . esc_html__( 'WooCommerce est requis pour ce widget.', 'tools-adapter' ) . '</p>';
			}
			return;
		}

		$settings = $this->get_settings_for_display();
		$term     = null;

		if ( 'manual' === ( $settings['source'] ?? 'current' ) && ! empty( $settings['category_id'] ) ) {
			$term = get_term( (int) $settings['category_id'], 'product_cat' );
		} elseif ( is_product_category() ) {
			$term = get_queried_object();
		}

		if ( ! $term || is_wp_error( $term ) ) {
			if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
				echo '<p>' . esc_html__( 'Sélectionnez une catégorie ou placez ce widget sur une page d\'archive de catégorie.', 'tools-adapter' ) . '</p>';
			}
			return;
		}

		$image_id  = 0;
		if ( 'yes' === ( $settings['show_image'] ?? '' ) ) {
			$image_id = Products_Query::get_category_image_id( $term->term_id, 'yes' === ( $settings['image_fallback'] ?? 'yes' ) );
		}
		$image_url = $image_id ? wp_get_attachment_image_url( $image_id, 'full' ) : '';
		$link      = get_term_link( $term );
		$link      = is_wp_error( $link ) ? '#' : $link;
		?>
		<div class="ta-cat-banner"<?php echo $image_url ? ' style="background-image:url(' . esc_url( $image_url ) . ');"' : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
			<?php if ( $image_url ) : ?><span class="ta-cat-banner__overlay" aria-hidden="true"></span><?php endif; ?>
			<div class="ta-cat-banner__content">
				<p class="ta-cat-banner__title"><?php echo esc_html( $term->name ); ?></p>
				<?php if ( 'yes' === ( $settings['show_description'] ?? '' ) && ! empty( $term->description ) ) : ?>
					<div class="ta-cat-banner__description"><?php echo wp_kses_post( wpautop( $term->description ) ); ?></div>
				<?php endif; ?>
				<?php if ( 'yes' === ( $settings['show_count'] ?? '' ) ) : ?>
					<p class="ta-cat-banner__count"><?php echo esc_html( sprintf( $settings['count_format'] ?? '%d produits', (int) $term->count ) ); ?></p>
				<?php endif; ?>
				<?php if ( 'yes' === ( $settings['show_button'] ?? '' ) && ! empty( $settings['button_text'] ) ) : ?>
					<a class="ta-cat-banner__btn" href="<?php echo esc_url( $link ); ?>"><?php echo esc_html( \tools_adapter_translate( $settings['button_text'] ) ); ?></a>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}
}
