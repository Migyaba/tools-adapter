<?php
namespace ToolsAdapter\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use ToolsAdapter\Base_Widget;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Widget: Vitrine catégories — tuiles superposées, liste typographique avec image
 * au survol, carrousel défilant ou grille éditoriale.
 */
class Category_Showcase extends Base_Widget {

	public function get_name() {
		return 'tools-adapter-category-showcase';
	}

	public function get_title() {
		return esc_html__( 'Vitrine catégories', 'tools-adapter' );
	}

	public function get_icon() {
		return 'eicon-gallery-grid';
	}

	public function get_categories() {
		return [ 'tools-adapter', 'woocommerce-elements' ];
	}

	public function get_keywords() {
		return [ 'catégories', 'categories', 'collections', 'vitrine', 'liste', 'carrousel', 'tuiles' ];
	}

	public function get_style_depends() {
		return [ 'tools-adapter-category-showcase' ];
	}

	public function get_script_depends() {
		return [ 'tools-adapter-category-showcase' ];
	}

	protected function register_controls() {
		$this->start_controls_section( 'section_content', [ 'label' => esc_html__( 'Catégories', 'tools-adapter' ) ] );

		$this->add_control(
			'layout',
			[
				'label'   => esc_html__( 'Disposition', 'tools-adapter' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'overlay',
				'options' => [
					'overlay'   => esc_html__( 'Tuiles — texte sur l\'image', 'tools-adapter' ),
					'editorial' => esc_html__( 'Grille éditoriale (une grande tuile)', 'tools-adapter' ),
					'list'      => esc_html__( 'Liste typographique (image au survol)', 'tools-adapter' ),
					'carousel'  => esc_html__( 'Carrousel défilant', 'tools-adapter' ),
				],
			]
		);

		$this->add_control( 'parent', [ 'label' => esc_html__( 'Catégorie parente (ID, 0 = principales)', 'tools-adapter' ), 'type' => Controls_Manager::NUMBER, 'default' => 0, 'min' => 0 ] );
		$this->add_control( 'include', [ 'label' => esc_html__( 'IDs à afficher (optionnel, dans cet ordre)', 'tools-adapter' ), 'type' => Controls_Manager::TEXT, 'placeholder' => '12, 15, 8' ] );
		$this->add_control( 'exclude', [ 'label' => esc_html__( 'IDs à exclure', 'tools-adapter' ), 'type' => Controls_Manager::TEXT ] );
		$this->add_control( 'limit', [ 'label' => esc_html__( 'Nombre maximum', 'tools-adapter' ), 'type' => Controls_Manager::NUMBER, 'default' => 6, 'min' => 1, 'max' => 24 ] );
		$this->add_control(
			'orderby',
			[
				'label'   => esc_html__( 'Trier par', 'tools-adapter' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'count',
				'options' => [
					'count'      => esc_html__( 'Nombre de produits', 'tools-adapter' ),
					'name'       => esc_html__( 'Nom', 'tools-adapter' ),
					'menu_order' => esc_html__( 'Ordre WooCommerce', 'tools-adapter' ),
				],
			]
		);
		$this->add_control( 'hide_empty', [ 'label' => esc_html__( 'Masquer les catégories vides', 'tools-adapter' ), 'type' => Controls_Manager::SWITCHER, 'return_value' => 'yes', 'default' => 'yes' ] );

		$this->add_responsive_control(
			'columns',
			[
				'label'          => esc_html__( 'Colonnes / éléments visibles', 'tools-adapter' ),
				'type'           => Controls_Manager::NUMBER,
				'min'            => 1,
				'max'            => 6,
				'default'        => 3,
				'tablet_default' => 2,
				'mobile_default' => 1,
				'selectors'      => [ '{{WRAPPER}} .ta-catshow' => '--ta-cs-cols: {{VALUE}};' ],
				'condition'      => [ 'layout' => [ 'overlay', 'carousel' ] ],
			]
		);

		$this->add_control(
			'ratio',
			[
				'label'     => esc_html__( 'Format des images', 'tools-adapter' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => '4/5',
				'options'   => [
					'4/5' => '4:5',
					'3/4' => '3:4',
					'1/1' => '1:1',
					'4/3' => '4:3',
				],
				'selectors' => [ '{{WRAPPER}} .ta-catshow' => '--ta-cs-ratio: {{VALUE}};' ],
				'condition' => [ 'layout' => [ 'overlay', 'carousel' ] ],
			]
		);

		$this->add_control( 'show_count', [ 'label' => esc_html__( 'Afficher le nombre de produits', 'tools-adapter' ), 'type' => Controls_Manager::SWITCHER, 'return_value' => 'yes', 'default' => 'yes' ] );
		/* translators: %d: number of products. */
		$this->add_control( 'count_format', [ 'label' => esc_html__( 'Format du compteur', 'tools-adapter' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( '%d produits', 'tools-adapter' ), 'condition' => [ 'show_count' => 'yes' ] ] );
		$this->add_control( 'link_text', [ 'label' => esc_html__( 'Texte du lien', 'tools-adapter' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'Découvrir', 'tools-adapter' ), 'condition' => [ 'layout' => [ 'overlay', 'editorial' ] ] ] );
		$this->add_control( 'title_tag', [ 'label' => esc_html__( 'Balise du nom', 'tools-adapter' ), 'type' => Controls_Manager::SELECT, 'default' => 'h3', 'options' => [ 'h2' => 'H2', 'h3' => 'H3', 'h4' => 'H4', 'span' => 'span' ] ] );

		$this->end_controls_section();

		$this->start_controls_section( 'section_style', [ 'label' => esc_html__( 'Style', 'tools-adapter' ), 'tab' => Controls_Manager::TAB_STYLE ] );
		$this->add_responsive_control( 'gap', [ 'label' => esc_html__( 'Espacement', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'range' => [ 'px' => [ 'min' => 0, 'max' => 60 ] ], 'default' => [ 'size' => 16, 'unit' => 'px' ], 'selectors' => [ '{{WRAPPER}} .ta-catshow' => '--ta-cs-gap: {{SIZE}}{{UNIT}};' ] ] );
		$this->add_responsive_control( 'tile_height', [ 'label' => esc_html__( 'Hauteur des lignes (grille éditoriale)', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'range' => [ 'px' => [ 'min' => 160, 'max' => 600 ] ], 'default' => [ 'size' => 300, 'unit' => 'px' ], 'selectors' => [ '{{WRAPPER}} .ta-catshow' => '--ta-cs-row: {{SIZE}}{{UNIT}};' ], 'condition' => [ 'layout' => 'editorial' ] ] );
		$this->add_control( 'radius', [ 'label' => esc_html__( 'Arrondi', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'range' => [ 'px' => [ 'min' => 0, 'max' => 40 ] ], 'default' => [ 'size' => 0, 'unit' => 'px' ], 'selectors' => [ '{{WRAPPER}} .ta-catshow' => '--ta-cs-radius: {{SIZE}}{{UNIT}};' ] ] );
		$this->add_control( 'overlay', [ 'label' => esc_html__( 'Voile', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => 'rgba(0,0,0,0.45)', 'selectors' => [ '{{WRAPPER}} .ta-catshow' => '--ta-cs-overlay: {{VALUE}};' ] ] );
		$this->add_control( 'text_color', [ 'label' => esc_html__( 'Couleur du texte', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .ta-catshow' => '--ta-cs-color: {{VALUE}};' ] ] );
		$this->add_control( 'muted_color', [ 'label' => esc_html__( 'Couleur secondaire (compteur, lignes)', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .ta-catshow' => '--ta-cs-muted: {{VALUE}};' ] ] );
		$this->add_group_control( Group_Control_Typography::get_type(), [ 'name' => 'title_typography', 'selector' => '{{WRAPPER}} .ta-catshow__name' ] );
		$this->end_controls_section();
	}

	/**
	 * Category terms to display.
	 *
	 * @param array $settings Settings.
	 * @return \WP_Term[]
	 */
	private function get_terms( array $settings ) {
		if ( ! taxonomy_exists( 'product_cat' ) ) {
			return [];
		}

		$ids  = static function ( $value ) {
			return array_filter( array_map( 'absint', explode( ',', (string) $value ) ) );
		};
		$args = [
			'taxonomy'   => 'product_cat',
			'hide_empty' => 'yes' === ( $settings['hide_empty'] ?? 'yes' ),
			'number'     => max( 1, absint( $settings['limit'] ?? 6 ) ),
			'exclude'    => array_merge( $ids( $settings['exclude'] ?? '' ), [ (int) get_option( 'default_product_cat' ) ] ),
		];

		$include = $ids( $settings['include'] ?? '' );
		if ( $include ) {
			$args['include'] = $include;
			$args['orderby'] = 'include';
		} else {
			$args['parent']  = absint( $settings['parent'] ?? 0 );
			$args['orderby'] = in_array( $settings['orderby'] ?? '', [ 'count', 'name', 'menu_order' ], true ) ? $settings['orderby'] : 'count';
			$args['order']   = 'count' === $args['orderby'] ? 'DESC' : 'ASC';
		}

		$terms = get_terms( $args );

		return is_wp_error( $terms ) ? [] : $terms;
	}

	/**
	 * Category image: its thumbnail, or the image of one of its products.
	 *
	 * @param \WP_Term $term Term.
	 * @param string   $size Image size.
	 * @param string   $css_class Image class.
	 * @return string
	 */
	private function term_image( $term, $size, $css_class ) {
		$id = (int) get_term_meta( $term->term_id, 'thumbnail_id', true );

		if ( ! $id ) {
			$products = get_posts(
				[
					'post_type'      => 'product',
					'posts_per_page' => 1,
					'fields'         => 'ids',
					'tax_query'      => [ [ 'taxonomy' => 'product_cat', 'terms' => $term->term_id ] ], // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
				]
			);
			$id       = $products ? (int) get_post_thumbnail_id( $products[0] ) : 0;
		}

		return $id ? wp_get_attachment_image( $id, $size, false, [ 'class' => $css_class, 'loading' => 'lazy', 'alt' => '' ] ) : '';
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$terms    = $this->get_terms( $settings );

		if ( ! $terms ) {
			if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
				echo '<p>' . esc_html__( 'Aucune catégorie à afficher.', 'tools-adapter' ) . '</p>';
			}
			return;
		}

		$layout = in_array( $settings['layout'] ?? '', [ 'overlay', 'editorial', 'list', 'carousel' ], true ) ? $settings['layout'] : 'overlay';
		$tag    = in_array( $settings['title_tag'] ?? '', [ 'h2', 'h3', 'h4', 'span' ], true ) ? $settings['title_tag'] : 'h3';
		$count  = static function ( $term ) use ( $settings ) {
			if ( 'yes' !== ( $settings['show_count'] ?? 'yes' ) ) {
				return '';
			}
			$format = ! empty( $settings['count_format'] ) ? $settings['count_format'] : '%d';
			return '<span class="ta-catshow__count">' . esc_html( sprintf( $format, (int) $term->count ) ) . '</span>';
		};
		?>
		<div class="ta-catshow ta-catshow--<?php echo esc_attr( $layout ); ?>"<?php echo 'carousel' === $layout ? ' data-ta-catshow-carousel' : ''; ?>>
			<?php if ( 'list' === $layout ) : ?>
				<ol class="ta-catshow__list">
					<?php foreach ( $terms as $index => $term ) : ?>
						<li class="ta-catshow__row">
							<a class="ta-catshow__link" href="<?php echo esc_url( get_term_link( $term ) ); ?>">
								<span class="ta-catshow__index"><?php echo esc_html( str_pad( (string) ( $index + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
								<<?php echo esc_attr( $tag ); ?> class="ta-catshow__name"><?php echo esc_html( $term->name ); ?></<?php echo esc_attr( $tag ); ?>>
								<?php echo $count( $term ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above. ?>
								<span class="ta-catshow__arrow" aria-hidden="true">&rarr;</span>
								<span class="ta-catshow__preview" aria-hidden="true"><?php echo $this->term_image( $term, 'woocommerce_thumbnail', 'ta-catshow__img' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
							</a>
						</li>
					<?php endforeach; ?>
				</ol>
			<?php else : ?>
				<?php if ( 'carousel' === $layout ) : ?>
					<div class="ta-catshow__nav">
						<button type="button" class="ta-catshow__arrow-btn" data-dir="-1" aria-label="<?php esc_attr_e( 'Précédent', 'tools-adapter' ); ?>">&larr;</button>
						<button type="button" class="ta-catshow__arrow-btn" data-dir="1" aria-label="<?php esc_attr_e( 'Suivant', 'tools-adapter' ); ?>">&rarr;</button>
					</div>
				<?php endif; ?>
				<div class="ta-catshow__track">
					<?php foreach ( $terms as $term ) : ?>
						<a class="ta-catshow__item" href="<?php echo esc_url( get_term_link( $term ) ); ?>">
							<span class="ta-catshow__media">
								<?php echo $this->term_image( $term, 'carousel' === $layout ? 'woocommerce_single' : 'large', 'ta-catshow__img' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								<?php if ( 'carousel' !== $layout ) : ?>
									<span class="ta-catshow__shade" aria-hidden="true"></span>
								<?php endif; ?>
							</span>
							<span class="ta-catshow__content">
								<<?php echo esc_attr( $tag ); ?> class="ta-catshow__name"><?php echo esc_html( $term->name ); ?></<?php echo esc_attr( $tag ); ?>>
								<?php echo $count( $term ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above. ?>
								<?php if ( 'carousel' !== $layout && ! empty( $settings['link_text'] ) ) : ?>
									<span class="ta-catshow__cta"><?php echo esc_html( $settings['link_text'] ); ?></span>
								<?php endif; ?>
							</span>
						</a>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>
		<?php
	}
}
