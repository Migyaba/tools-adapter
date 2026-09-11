<?php
namespace ToolsAdapter\Widgets;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Widget: Grille d'articles de blog — grille personnalisable d'articles WordPress.
 */
class Blog_Grid extends Widget_Base {

	public function get_name() {
		return 'tools-adapter-blog-grid';
	}

	public function get_title() {
		return esc_html__( 'Grille de blog', 'tools-adapter' );
	}

	public function get_icon() {
		return 'eicon-post-list';
	}

	public function get_categories() {
		return [ 'tools-adapter' ];
	}

	public function get_keywords() {
		return [ 'blog', 'articles', 'grille', 'actualités' ];
	}

	public function get_style_depends() {
		return [ 'tools-adapter-blog-grid' ];
	}

	/**
	 * @return array<int,string>
	 */
	private function get_category_options() {
		$terms   = get_categories( [ 'hide_empty' => false ] );
		$options = [];
		foreach ( $terms as $term ) {
			$options[ $term->term_id ] = $term->name;
		}
		return $options;
	}

	protected function register_controls() {
		$this->start_controls_section( 'section_content', [ 'label' => esc_html__( 'Contenu', 'tools-adapter' ) ] );

		$this->add_control(
			'categories',
			[
				'label'   => esc_html__( 'Catégories', 'tools-adapter' ),
				'type'    => Controls_Manager::SELECT2,
				'multiple' => true,
				'options' => $this->get_category_options(),
				'description' => esc_html__( 'Laissez vide pour afficher toutes les catégories.', 'tools-adapter' ),
			]
		);

		$this->add_control( 'posts_count', [ 'label' => esc_html__( 'Nombre d\'articles', 'tools-adapter' ), 'type' => Controls_Manager::NUMBER, 'default' => 6, 'min' => 1, 'max' => 48 ] );

		$this->add_control(
			'orderby',
			[
				'label'   => esc_html__( 'Trier par', 'tools-adapter' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'date',
				'options' => [
					'date'  => esc_html__( 'Date', 'tools-adapter' ),
					'title' => esc_html__( 'Titre', 'tools-adapter' ),
					'rand'  => esc_html__( 'Aléatoire', 'tools-adapter' ),
				],
			]
		);

		$this->add_control(
			'order',
			[
				'label'     => esc_html__( 'Ordre', 'tools-adapter' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'DESC',
				'options'   => [
					'DESC' => esc_html__( 'Décroissant', 'tools-adapter' ),
					'ASC'  => esc_html__( 'Croissant', 'tools-adapter' ),
				],
				'condition' => [ 'orderby!' => 'rand' ],
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
				'selectors'      => [ '{{WRAPPER}} .ta-blog-grid' => '--ta-blog-cols: {{VALUE}};' ],
				'separator'      => 'before',
			]
		);

		$this->add_control( 'show_image', [ 'label' => esc_html__( 'Image mise en avant', 'tools-adapter' ), 'type' => Controls_Manager::SWITCHER, 'return_value' => 'yes', 'default' => 'yes', 'separator' => 'before' ] );
		$this->add_control( 'show_category', [ 'label' => esc_html__( 'Badge catégorie', 'tools-adapter' ), 'type' => Controls_Manager::SWITCHER, 'return_value' => 'yes', 'default' => 'yes' ] );
		$this->add_control( 'show_date', [ 'label' => esc_html__( 'Date', 'tools-adapter' ), 'type' => Controls_Manager::SWITCHER, 'return_value' => 'yes', 'default' => 'yes' ] );
		$this->add_control( 'show_author', [ 'label' => esc_html__( 'Auteur', 'tools-adapter' ), 'type' => Controls_Manager::SWITCHER, 'return_value' => 'yes', 'default' => '' ] );
		$this->add_control( 'show_excerpt', [ 'label' => esc_html__( 'Extrait', 'tools-adapter' ), 'type' => Controls_Manager::SWITCHER, 'return_value' => 'yes', 'default' => 'yes' ] );
		$this->add_control( 'excerpt_length', [ 'label' => esc_html__( 'Longueur de l\'extrait (mots)', 'tools-adapter' ), 'type' => Controls_Manager::NUMBER, 'default' => 20, 'min' => 5, 'max' => 100, 'condition' => [ 'show_excerpt' => 'yes' ] ] );
		$this->add_control( 'show_read_more', [ 'label' => esc_html__( 'Lien « Lire la suite »', 'tools-adapter' ), 'type' => Controls_Manager::SWITCHER, 'return_value' => 'yes', 'default' => 'yes' ] );
		$this->add_control( 'read_more_text', [ 'label' => esc_html__( 'Texte du lien', 'tools-adapter' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'Lire la suite', 'tools-adapter' ), 'condition' => [ 'show_read_more' => 'yes' ] ] );
		$this->add_control( 'enable_pagination', [ 'label' => esc_html__( 'Pagination', 'tools-adapter' ), 'type' => Controls_Manager::SWITCHER, 'return_value' => 'yes', 'default' => '', 'separator' => 'before', 'description' => esc_html__( 'Utilise la pagination native de WordPress (fonctionne uniquement en dehors de l\'éditeur Elementor).', 'tools-adapter' ) ] );

		$this->end_controls_section();

		$this->start_controls_section( 'section_style_card', [ 'label' => esc_html__( 'Carte', 'tools-adapter' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_control( 'card_bg', [ 'label' => esc_html__( 'Fond', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#ffffff', 'selectors' => [ '{{WRAPPER}} .ta-blog-card' => 'background-color: {{VALUE}};' ] ] );
		$this->add_group_control( Group_Control_Border::get_type(), [ 'name' => 'card_border', 'selector' => '{{WRAPPER}} .ta-blog-card' ] );
		$this->add_control( 'card_radius', [ 'label' => esc_html__( 'Arrondi', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'range' => [ 'px' => [ 'min' => 0, 'max' => 40 ] ], 'default' => [ 'size' => 10, 'unit' => 'px' ], 'selectors' => [ '{{WRAPPER}} .ta-blog-card' => 'border-radius: {{SIZE}}{{UNIT}}; overflow: hidden;' ] ] );
		$this->add_group_control( Group_Control_Box_Shadow::get_type(), [ 'name' => 'card_shadow', 'selector' => '{{WRAPPER}} .ta-blog-card' ] );
		$this->add_responsive_control( 'items_gap', [ 'label' => esc_html__( 'Espacement', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'range' => [ 'px' => [ 'min' => 0, 'max' => 60 ] ], 'default' => [ 'size' => 28, 'unit' => 'px' ], 'separator' => 'before', 'selectors' => [ '{{WRAPPER}} .ta-blog-grid' => '--ta-blog-gap: {{SIZE}}{{UNIT}};' ] ] );
		$this->add_responsive_control( 'image_ratio', [ 'label' => esc_html__( 'Ratio de l\'image', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'range' => [ 'px' => [ 'min' => 40, 'max' => 100 ] ], 'default' => [ 'size' => 60 ], 'selectors' => [ '{{WRAPPER}} .ta-blog-card__image' => 'padding-top: {{SIZE}}%;' ] ] );

		$this->end_controls_section();

		$this->start_controls_section( 'section_style_content', [ 'label' => esc_html__( 'Contenu', 'tools-adapter' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_control( 'category_bg', [ 'label' => esc_html__( 'Fond badge catégorie', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#C9A84C', 'selectors' => [ '{{WRAPPER}} .ta-blog-card__category' => 'background-color: {{VALUE}};' ] ] );
		$this->add_control( 'category_color', [ 'label' => esc_html__( 'Texte badge catégorie', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#1c1c1c', 'selectors' => [ '{{WRAPPER}} .ta-blog-card__category' => 'color: {{VALUE}};' ] ] );
		$this->add_control( 'title_color', [ 'label' => esc_html__( 'Couleur du titre', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#1c1c1c', 'separator' => 'before', 'selectors' => [ '{{WRAPPER}} .ta-blog-card__title a' => 'color: {{VALUE}};' ] ] );
		$this->add_control( 'title_color_hover', [ 'label' => esc_html__( 'Couleur du titre (survol)', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#C9A84C', 'selectors' => [ '{{WRAPPER}} .ta-blog-card__title a:hover' => 'color: {{VALUE}};' ] ] );
		$this->add_group_control( Group_Control_Typography::get_type(), [ 'name' => 'title_typography', 'selector' => '{{WRAPPER}} .ta-blog-card__title' ] );
		$this->add_control( 'meta_color', [ 'label' => esc_html__( 'Couleur des méta-infos', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#999999', 'separator' => 'before', 'selectors' => [ '{{WRAPPER}} .ta-blog-card__meta' => 'color: {{VALUE}};' ] ] );
		$this->add_control( 'excerpt_color', [ 'label' => esc_html__( 'Couleur de l\'extrait', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#666666', 'separator' => 'before', 'selectors' => [ '{{WRAPPER}} .ta-blog-card__excerpt' => 'color: {{VALUE}};' ] ] );
		$this->add_control( 'read_more_color', [ 'label' => esc_html__( 'Couleur « Lire la suite »', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#C9A84C', 'separator' => 'before', 'selectors' => [ '{{WRAPPER}} .ta-blog-card__more' => 'color: {{VALUE}};' ] ] );

		$this->end_controls_section();
	}

	protected function render() {
		$settings   = $this->get_settings_for_display();
		$is_editor  = \Elementor\Plugin::$instance->editor->is_edit_mode();
		$paged      = $is_editor ? 1 : max( 1, (int) get_query_var( 'paged' ), (int) get_query_var( 'page' ) );

		$query_args = [
			'post_type'      => 'post',
			'post_status'    => 'publish',
			'posts_per_page' => absint( $settings['posts_count'] ?? 6 ),
			'orderby'        => $settings['orderby'] ?? 'date',
			'order'          => $settings['order'] ?? 'DESC',
			'paged'          => $paged,
			'ignore_sticky_posts' => true,
		];

		if ( ! empty( $settings['categories'] ) ) {
			$query_args['category__in'] = array_map( 'absint', (array) $settings['categories'] );
		}

		$query = new \WP_Query( $query_args );

		if ( ! $query->have_posts() ) {
			if ( $is_editor ) {
				echo '<p>' . esc_html__( 'Aucun article trouvé.', 'tools-adapter' ) . '</p>';
			}
			return;
		}

		$show_image      = 'yes' === ( $settings['show_image'] ?? '' );
		$show_category   = 'yes' === ( $settings['show_category'] ?? '' );
		$show_date       = 'yes' === ( $settings['show_date'] ?? '' );
		$show_author     = 'yes' === ( $settings['show_author'] ?? '' );
		$show_excerpt    = 'yes' === ( $settings['show_excerpt'] ?? '' );
		$excerpt_length  = absint( $settings['excerpt_length'] ?? 20 );
		$show_read_more  = 'yes' === ( $settings['show_read_more'] ?? '' );
		$read_more_text  = \tools_adapter_translate( $settings['read_more_text'] ?? '' );
		?>
		<div class="ta-blog-grid">
			<?php
			while ( $query->have_posts() ) :
				$query->the_post();
				$categories = get_the_category();
				?>
				<article class="ta-blog-card">
					<a class="ta-blog-card__link" href="<?php the_permalink(); ?>">
						<?php if ( $show_image && has_post_thumbnail() ) : ?>
							<div class="ta-blog-card__image" style="background-image:url('<?php echo esc_url( get_the_post_thumbnail_url( get_the_ID(), 'large' ) ); ?>');"></div>
						<?php endif; ?>
					</a>
					<div class="ta-blog-card__body">
						<?php if ( $show_category && ! empty( $categories ) ) : ?>
							<a class="ta-blog-card__category" href="<?php echo esc_url( get_category_link( $categories[0]->term_id ) ); ?>"><?php echo esc_html( $categories[0]->name ); ?></a>
						<?php endif; ?>
						<h3 class="ta-blog-card__title"><a href="<?php the_permalink(); ?>"><?php echo esc_html( get_the_title() ); ?></a></h3>
						<?php if ( $show_date || $show_author ) : ?>
							<p class="ta-blog-card__meta">
								<?php if ( $show_date ) : ?><span><?php echo esc_html( get_the_date() ); ?></span><?php endif; ?>
								<?php if ( $show_date && $show_author ) : ?><span aria-hidden="true"> · </span><?php endif; ?>
								<?php if ( $show_author ) : ?><span><?php echo esc_html( get_the_author() ); ?></span><?php endif; ?>
							</p>
						<?php endif; ?>
						<?php if ( $show_excerpt ) : ?>
							<p class="ta-blog-card__excerpt"><?php echo esc_html( wp_trim_words( wp_strip_all_tags( get_the_excerpt() ), $excerpt_length ) ); ?></p>
						<?php endif; ?>
						<?php if ( $show_read_more && $read_more_text ) : ?>
							<a class="ta-blog-card__more" href="<?php the_permalink(); ?>">
								<?php echo esc_html( $read_more_text ); ?>
								<i class="fas fa-arrow-right" aria-hidden="true"></i>
							</a>
						<?php endif; ?>
					</div>
				</article>
			<?php endwhile; ?>
		</div>
		<?php
		if ( 'yes' === ( $settings['enable_pagination'] ?? '' ) && ! $is_editor && $query->max_num_pages > 1 ) {
			echo '<div class="ta-blog-grid__pagination">';
			echo wp_kses_post(
				paginate_links(
					[
						'total'     => $query->max_num_pages,
						'current'   => $paged,
						'prev_text' => esc_html__( '‹ Précédent', 'tools-adapter' ),
						'next_text' => esc_html__( 'Suivant ›', 'tools-adapter' ),
					]
				)
			);
			echo '</div>';
		}

		wp_reset_postdata();
	}
}
