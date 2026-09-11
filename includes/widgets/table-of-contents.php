<?php
namespace ToolsAdapter\Widgets;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Widget: Table des matières — sommaire auto-généré à partir des titres de la page.
 */
class Table_Of_Contents extends Widget_Base {

	public function get_name() {
		return 'tools-adapter-toc';
	}

	public function get_title() {
		return esc_html__( 'Table des matières', 'tools-adapter' );
	}

	public function get_icon() {
		return 'eicon-table-of-contents';
	}

	public function get_categories() {
		return [ 'tools-adapter' ];
	}

	public function get_keywords() {
		return [ 'sommaire', 'table des matières', 'toc', 'navigation' ];
	}

	public function get_style_depends() {
		return [ 'tools-adapter-toc' ];
	}

	public function get_script_depends() {
		return [ 'tools-adapter-toc' ];
	}

	protected function register_controls() {
		$this->start_controls_section( 'section_content', [ 'label' => esc_html__( 'Contenu', 'tools-adapter' ) ] );

		$this->add_control( 'title', [ 'label' => esc_html__( 'Titre', 'tools-adapter' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'Sommaire', 'tools-adapter' ) ] );

		$this->add_control(
			'heading_levels',
			[
				'label'    => esc_html__( 'Niveaux de titres', 'tools-adapter' ),
				'type'     => Controls_Manager::SELECT2,
				'multiple' => true,
				'options'  => [ 'h2' => 'H2', 'h3' => 'H3', 'h4' => 'H4' ],
				'default'  => [ 'h2', 'h3' ],
			]
		);

		$this->add_control(
			'container_selector',
			[
				'label'       => esc_html__( 'Sélecteur du contenu', 'tools-adapter' ),
				'description' => esc_html__( 'Classe/ID CSS de la zone à analyser. Laisser vide pour analyser toute la page.', 'tools-adapter' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'placeholder' => '.elementor, article, #content...',
			]
		);

		$this->add_control( 'collapsible', [ 'label' => esc_html__( 'Repliable', 'tools-adapter' ), 'type' => Controls_Manager::SWITCHER, 'return_value' => 'yes', 'default' => '' ] );
		$this->add_control( 'numbering', [ 'label' => esc_html__( 'Numérotation', 'tools-adapter' ), 'type' => Controls_Manager::SWITCHER, 'return_value' => 'yes', 'default' => 'yes' ] );
		$this->add_control( 'smooth_scroll', [ 'label' => esc_html__( 'Défilement doux', 'tools-adapter' ), 'type' => Controls_Manager::SWITCHER, 'return_value' => 'yes', 'default' => 'yes' ] );
		$this->add_control( 'scroll_spy', [ 'label' => esc_html__( 'Surligner la section active', 'tools-adapter' ), 'type' => Controls_Manager::SWITCHER, 'return_value' => 'yes', 'default' => 'yes' ] );

		$this->end_controls_section();

		$this->start_controls_section( 'section_style', [ 'label' => esc_html__( 'Style', 'tools-adapter' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_control( 'bg_color', [ 'label' => esc_html__( 'Fond', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#f7f7f7', 'selectors' => [ '{{WRAPPER}} .ta-toc' => 'background-color: {{VALUE}};' ] ] );
		$this->add_responsive_control( 'toc_padding', [ 'label' => esc_html__( 'Espacement interne', 'tools-adapter' ), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => [ 'px' ], 'default' => [ 'top' => '24', 'right' => '24', 'bottom' => '24', 'left' => '24', 'unit' => 'px' ], 'selectors' => [ '{{WRAPPER}} .ta-toc' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ] ] );
		$this->add_control( 'toc_radius', [ 'label' => esc_html__( 'Arrondi', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'range' => [ 'px' => [ 'min' => 0, 'max' => 40 ] ], 'default' => [ 'size' => 8, 'unit' => 'px' ], 'selectors' => [ '{{WRAPPER}} .ta-toc' => 'border-radius: {{SIZE}}{{UNIT}};' ] ] );

		$this->add_control( 'title_color', [ 'label' => esc_html__( 'Couleur titre', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#1c1c1c', 'separator' => 'before', 'selectors' => [ '{{WRAPPER}} .ta-toc__title' => 'color: {{VALUE}};' ] ] );
		$this->add_group_control( Group_Control_Typography::get_type(), [ 'name' => 'title_typography', 'selector' => '{{WRAPPER}} .ta-toc__title' ] );

		$this->add_control( 'link_color', [ 'label' => esc_html__( 'Couleur des liens', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#444444', 'separator' => 'before', 'selectors' => [ '{{WRAPPER}} .ta-toc__list a' => 'color: {{VALUE}};' ] ] );
		$this->add_control( 'link_color_active', [ 'label' => esc_html__( 'Couleur du lien actif', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#C9A84C', 'selectors' => [ '{{WRAPPER}} .ta-toc__list a.is-active' => 'color: {{VALUE}};' ] ] );

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$levels   = is_array( $settings['heading_levels'] ?? null ) && ! empty( $settings['heading_levels'] ) ? $settings['heading_levels'] : [ 'h2', 'h3' ];
		?>
		<div
			class="ta-toc"
			data-ta-toc
			data-levels="<?php echo esc_attr( implode( ',', $levels ) ); ?>"
			data-container="<?php echo esc_attr( $settings['container_selector'] ?? '' ); ?>"
			data-numbering="<?php echo 'yes' === ( $settings['numbering'] ?? '' ) ? '1' : '0'; ?>"
			data-smooth="<?php echo 'yes' === ( $settings['smooth_scroll'] ?? '' ) ? '1' : '0'; ?>"
			data-spy="<?php echo 'yes' === ( $settings['scroll_spy'] ?? '' ) ? '1' : '0'; ?>"
			data-collapsible="<?php echo 'yes' === ( $settings['collapsible'] ?? '' ) ? '1' : '0'; ?>"
		>
			<?php if ( ! empty( $settings['title'] ) ) : ?>
				<?php if ( 'yes' === ( $settings['collapsible'] ?? '' ) ) : ?>
					<button type="button" class="ta-toc__title" data-toc-toggle aria-expanded="true"><?php echo esc_html( \tools_adapter_translate( $settings['title'] ) ); ?></button>
				<?php else : ?>
					<p class="ta-toc__title"><?php echo esc_html( \tools_adapter_translate( $settings['title'] ) ); ?></p>
				<?php endif; ?>
			<?php endif; ?>
			<nav class="ta-toc__list" data-toc-list aria-label="<?php echo esc_attr__( 'Sommaire', 'tools-adapter' ); ?>">
				<p class="ta-toc__empty" data-toc-empty><?php echo esc_html__( 'Le sommaire s\'affichera ici sur la page publiée.', 'tools-adapter' ); ?></p>
			</nav>
		</div>
		<?php
	}
}
