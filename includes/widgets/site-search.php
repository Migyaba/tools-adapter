<?php
namespace ToolsAdapter\Widgets;

use Elementor\Controls_Manager;
use ToolsAdapter\Base_Widget;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Widget: Recherche du Site — barre de recherche intégrée ou bouton d'ouverture de modale plein écran.
 */
class Site_Search extends Base_Widget {

	public function get_name() {
		return 'tools-adapter-site-search';
	}

	public function get_title() {
		return esc_html__( 'Recherche du Site', 'tools-adapter' );
	}

	public function get_icon() {
		return 'eicon-search';
	}

	public function get_categories() {
		return [ 'tools-adapter' ];
	}

	public function get_keywords() {
		return [ 'search', 'recherche', 'loupe', 'header', 'barre', 'modal', 'popup' ];
	}

	public function get_style_depends() {
		return [ 'tools-adapter-site-search' ];
	}

	public function get_script_depends() {
		return [ 'tools-adapter-site-search' ];
	}

	protected function register_controls() {
		// ==========================================
		// SECTION CONTENU : RECHERCHE
		// ==========================================
		$this->start_controls_section(
			'section_search',
			[ 'label' => esc_html__( 'Champ de Recherche', 'tools-adapter' ) ]
		);

		$this->add_control(
			'layout',
			[
				'label'   => esc_html__( 'Disposition', 'tools-adapter' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'inline',
				'options' => [
					'inline' => esc_html__( 'Barre intégrée (Inline)', 'tools-adapter' ),
					'popup'  => esc_html__( 'Icône loupe avec Modale (Pop-up)', 'tools-adapter' ),
				],
			]
		);

		$this->add_control(
			'placeholder',
			[
				'label'   => esc_html__( 'Texte d\'invitation (Placeholder)', 'tools-adapter' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Rechercher…', 'tools-adapter' ),
			]
		);

		$this->add_control(
			'search_target',
			[
				'label'   => esc_html__( 'Cible de la recherche', 'tools-adapter' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'any',
				'options' => [
					'any'     => esc_html__( 'Tout le site (Articles, pages, etc.)', 'tools-adapter' ),
					'product' => esc_html__( 'Produits WooCommerce uniquement', 'tools-adapter' ),
				],
			]
		);

		$this->add_responsive_control(
			'align',
			[
				'label'     => esc_html__( 'Alignement', 'tools-adapter' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'left'   => [ 'title' => esc_html__( 'Gauche', 'tools-adapter' ), 'icon' => 'eicon-text-align-left' ],
					'center' => [ 'title' => esc_html__( 'Centre', 'tools-adapter' ), 'icon' => 'eicon-text-align-center' ],
					'right'  => [ 'title' => esc_html__( 'Droite', 'tools-adapter' ), 'icon' => 'eicon-text-align-right' ],
				],
				'selectors' => [
					'{{WRAPPER}} .ta-site-search-wrap' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		// ==========================================
		// SECTION STYLE : BARRE / BOUTON
		// ==========================================
		$this->start_controls_section(
			'section_style_search',
			[
				'label' => esc_html__( 'Style', 'tools-adapter' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'input_bg',
			[
				'label'     => esc_html__( 'Arrière-plan', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ta-site-search-form'    => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .ta-site-search-trigger' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_bg',
			[
				'label'     => esc_html__( 'Couleur du bouton loupe', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ta-site-search-btn'     => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .ta-search-modal-btn'    => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_icon_color',
			[
				'label'     => esc_html__( 'Couleur de l\'icône loupe', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ta-site-search-btn'     => 'color: {{VALUE}};',
					'{{WRAPPER}} .ta-site-search-trigger' => 'color: {{VALUE}};',
					'{{WRAPPER}} .ta-search-modal-btn'    => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'border_radius',
			[
				'label'      => esc_html__( 'Rayon des coins', 'tools-adapter' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .ta-site-search-form'    => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .ta-site-search-trigger' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$layout   = $settings['layout'];
		?>
		<div class="ta-site-search-wrap">

			<?php if ( 'inline' === $layout ) : ?>
				<form role="search" method="get" class="ta-site-search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
					<input type="search"
					       class="ta-site-search-input"
					       placeholder="<?php echo esc_attr( $settings['placeholder'] ); ?>"
					       value="<?php echo get_search_query(); ?>"
					       name="s"
					       required>
					<?php if ( 'product' === $settings['search_target'] ) : ?>
						<input type="hidden" name="post_type" value="product">
					<?php endif; ?>
					<button type="submit" class="ta-site-search-btn" aria-label="<?php esc_attr_e( 'Lancer la recherche', 'tools-adapter' ); ?>">
						<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
					</button>
				</form>

			<?php else : ?>
				<!-- Pop-up trigger -->
				<button type="button" class="ta-site-search-trigger" aria-label="<?php esc_attr_e( 'Ouvrir la recherche', 'tools-adapter' ); ?>">
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
				</button>

				<!-- Pop-up modal -->
				<div class="ta-site-search-modal" role="dialog" aria-modal="true">
					<div class="ta-search-modal-content">
						<button type="button" class="ta-search-modal-close" aria-label="<?php esc_attr_e( 'Fermer', 'tools-adapter' ); ?>">✕</button>
						<form role="search" method="get" class="ta-search-modal-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
							<input type="search"
							       class="ta-search-modal-input"
							       placeholder="<?php echo esc_attr( $settings['placeholder'] ); ?>"
							       value="<?php echo get_search_query(); ?>"
							       name="s"
							       required>
							<?php if ( 'product' === $settings['search_target'] ) : ?>
								<input type="hidden" name="post_type" value="product">
							<?php endif; ?>
							<button type="submit" class="ta-search-modal-btn" aria-label="<?php esc_attr_e( 'Lancer la recherche', 'tools-adapter' ); ?>">
								<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
							</button>
						</form>
					</div>
				</div>
			<?php endif; ?>

		</div>
		<?php
	}
}
