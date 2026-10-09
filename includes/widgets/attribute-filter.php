<?php
namespace ToolsAdapter\Widgets;

use Elementor\Controls_Manager;
use ToolsAdapter\Base_Widget;
use Elementor\Group_Control_Typography;
use ToolsAdapter\Products_Query;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Widget: Filtre par attributs — expose un attribut WooCommerce global
 * (couleur, taille…) en pastilles/pilules cliquables, synchronisées avec
 * l'Archive/Grille Produits via l'AJAX partagé (Ajax_Archive).
 */
class Attribute_Filter extends Base_Widget {

	public function get_name() {
		return 'tools-adapter-attribute-filter';
	}

	public function get_title() {
		return esc_html__( 'Filtre par attributs', 'tools-adapter' );
	}

	public function get_icon() {
		return 'eicon-filter';
	}

	public function get_categories() {
		return [ 'tools-adapter', 'woocommerce-elements' ];
	}

	public function get_keywords() {
		return [ 'filtre', 'attribut', 'couleur', 'taille', 'filter', 'attribute' ];
	}

	public function get_style_depends() {
		return [ 'tools-adapter-attribute-filter' ];
	}

	public function get_script_depends() {
		return [ 'tools-adapter-archive' ];
	}

	protected function register_controls() {
		$this->start_controls_section( 'section_content', [ 'label' => esc_html__( 'Réglages', 'tools-adapter' ) ] );

		$this->add_control(
			'attribute',
			[
				'label'   => esc_html__( 'Attribut', 'tools-adapter' ),
				'type'    => Controls_Manager::SELECT,
				'options' => Products_Query::get_attribute_taxonomy_options(),
				'default' => '',
			]
		);

		$this->add_control( 'title', [ 'label' => esc_html__( 'Titre', 'tools-adapter' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'Filtrer', 'tools-adapter' ) ] );

		$this->add_control(
			'display_style',
			[
				'label'        => esc_html__( 'Affichage', 'tools-adapter' ),
				'type'         => Controls_Manager::SELECT,
				'default'      => 'auto',
				'options'      => [
					'auto'  => esc_html__( 'Automatique (couleur si détectée)', 'tools-adapter' ),
					'pills' => esc_html__( 'Pilules de texte', 'tools-adapter' ),
					'swatch' => esc_html__( 'Pastilles de couleur', 'tools-adapter' ),
				],
				'prefix_class' => 'ta-attr-filter-style--',
			]
		);

		$this->add_control( 'show_count', [ 'label' => esc_html__( 'Afficher le nombre de produits', 'tools-adapter' ), 'type' => Controls_Manager::SWITCHER, 'return_value' => 'yes', 'default' => 'yes' ] );
		$this->add_control( 'hide_empty', [ 'label' => esc_html__( 'Masquer les termes vides', 'tools-adapter' ), 'type' => Controls_Manager::SWITCHER, 'return_value' => 'yes', 'default' => 'yes' ] );

		$this->end_controls_section();

		$this->start_controls_section( 'section_style', [ 'label' => esc_html__( 'Style', 'tools-adapter' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_control( 'title_color', [ 'label' => esc_html__( 'Couleur titre', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#1c1c1c', 'selectors' => [ '{{WRAPPER}} .ta-attr-filter__title' => 'color: {{VALUE}};' ] ] );
		$this->add_group_control( Group_Control_Typography::get_type(), [ 'name' => 'title_typography', 'selector' => '{{WRAPPER}} .ta-attr-filter__title' ] );
		$this->add_control( 'term_color', [ 'label' => esc_html__( 'Texte terme', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#444444', 'separator' => 'before', 'selectors' => [ '{{WRAPPER}} .ta-attr-filter__term' => 'color: {{VALUE}};' ] ] );
		$this->add_control( 'term_bg', [ 'label' => esc_html__( 'Fond terme', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#f2f2f2', 'selectors' => [ '{{WRAPPER}} .ta-attr-filter__term' => 'background-color: {{VALUE}};' ] ] );
		$this->add_control( 'term_active_color', [ 'label' => esc_html__( 'Texte terme actif', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#ffffff', 'selectors' => [ '{{WRAPPER}} .ta-attr-filter__term.is-active' => 'color: {{VALUE}};' ] ] );
		$this->add_control( 'term_active_bg', [ 'label' => esc_html__( 'Fond terme actif', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#1c1c1c', 'selectors' => [ '{{WRAPPER}} .ta-attr-filter__term.is-active' => 'background-color: {{VALUE}};' ] ] );

		$this->end_controls_section();
	}

	protected function render() {
		if ( ! class_exists( 'WooCommerce' ) ) {
			echo '<p>' . esc_html__( 'WooCommerce est requis pour ce widget.', 'tools-adapter' ) . '</p>';
			return;
		}

		$settings  = $this->get_settings_for_display();
		$taxonomy  = $settings['attribute'] ?? '';

		if ( ! $taxonomy || ! taxonomy_exists( $taxonomy ) ) {
			if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
				echo '<p>' . esc_html__( 'Sélectionnez un attribut WooCommerce dans les réglages du widget.', 'tools-adapter' ) . '</p>';
			}
			return;
		}

		$terms = get_terms(
			[
				'taxonomy'   => $taxonomy,
				'hide_empty' => 'yes' === ( $settings['hide_empty'] ?? '' ),
			]
		);

		if ( empty( $terms ) || is_wp_error( $terms ) ) {
			return;
		}

		$is_color_taxonomy = (bool) preg_match( '/(color|colour|couleur)/i', $taxonomy );
		$display_style     = $settings['display_style'] ?? 'auto';
		$as_swatch          = 'swatch' === $display_style || ( 'auto' === $display_style && $is_color_taxonomy );
		?>
		<div class="ta-attr-filter" data-ta-attribute-filter data-taxonomy="<?php echo esc_attr( $taxonomy ); ?>">
			<?php if ( ! empty( $settings['title'] ) ) : ?>
				<p class="ta-attr-filter__title"><?php echo esc_html( \tools_adapter_translate( $settings['title'] ) ); ?></p>
			<?php endif; ?>
			<div class="ta-attr-filter__terms">
				<?php foreach ( $terms as $term ) : ?>
					<button
						type="button"
						class="ta-attr-filter__term<?php echo $as_swatch ? ' ta-attr-filter__term--swatch' : ' ta-attr-filter__term--pill'; ?>"
						data-attribute-term="<?php echo esc_attr( $term->slug ); ?>"
						title="<?php echo esc_attr( $term->name ); ?>"
						<?php if ( $as_swatch ) : ?>style="background: <?php echo esc_attr( $this->guess_color( $term->name ) ); ?>;"<?php endif; ?>
					>
						<?php if ( $as_swatch ) : ?>
							<span class="screen-reader-text"><?php echo esc_html( $term->name ); ?></span>
						<?php else : ?>
							<span><?php echo esc_html( $term->name ); ?></span>
							<?php if ( 'yes' === ( $settings['show_count'] ?? '' ) ) : ?>
								<span class="ta-attr-filter__count">(<?php echo esc_html( (string) $term->count ); ?>)</span>
							<?php endif; ?>
						<?php endif; ?>
					</button>
				<?php endforeach; ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Best-effort color name → CSS color lookup (same palette used by the
	 * visual variation swatches feature, kept lightweight here on purpose).
	 *
	 * @param string $label Term label.
	 * @return string
	 */
	private function guess_color( $label ) {
		$map = [
			'noir' => '#000000', 'black' => '#000000', 'blanc' => '#ffffff', 'white' => '#ffffff',
			'gris' => '#8c8c8c', 'grey' => '#8c8c8c', 'gray' => '#8c8c8c', 'rouge' => '#d62828', 'red' => '#d62828',
			'bleu' => '#1d4e89', 'blue' => '#1d4e89', 'vert' => '#2e8b57', 'green' => '#2e8b57',
			'jaune' => '#f2c14e', 'yellow' => '#f2c14e', 'orange' => '#e8772e', 'rose' => '#e8a0bf', 'pink' => '#e8a0bf',
			'violet' => '#7d5ba6', 'purple' => '#7d5ba6', 'marron' => '#6b4226', 'brown' => '#6b4226', 'beige' => '#e8dcc8',
		];
		$key = strtolower( trim( wp_strip_all_tags( $label ) ) );
		if ( isset( $map[ $key ] ) ) {
			return $map[ $key ];
		}
		foreach ( $map as $name => $hex ) {
			if ( false !== strpos( $key, $name ) ) {
				return $hex;
			}
		}
		return preg_match( '/^[a-z]+$/', $key ) ? $key : '#cccccc';
	}
}
