<?php
namespace ToolsAdapter\Widgets;

use Elementor\Controls_Manager;
use ToolsAdapter\Base_Widget;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Widget: Droits d'auteur / Copyright — texte avec actualisation dynamique de l'année et du titre du site.
 */
class Site_Copyright extends Base_Widget {

	public function get_name() {
		return 'tools-adapter-site-copyright';
	}

	public function get_title() {
		return esc_html__( 'Droits d\'auteur / Copyright', 'tools-adapter' );
	}

	public function get_icon() {
		return 'eicon-heading';
	}

	public function get_categories() {
		return [ 'tools-adapter' ];
	}

	public function get_keywords() {
		return [ 'copyright', 'annee', 'year', 'footer', 'pied de page', 'droits', 'mentions' ];
	}

	public function get_style_depends() {
		return [ 'tools-adapter-site-copyright' ];
	}

	protected function register_controls() {
		// ==========================================
		// SECTION CONTENU : COPYRIGHT
		// ==========================================
		$this->start_controls_section(
			'section_copyright',
			[ 'label' => esc_html__( 'Texte du Copyright', 'tools-adapter' ) ]
		);

		$this->add_control(
			'copyright_text',
			[
				'label'       => esc_html__( 'Format du texte', 'tools-adapter' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 3,
				'default'     => '© {year} {site_title}. ' . esc_html__( 'Tous droits réservés.', 'tools-adapter' ),
				'description' => esc_html__( 'Balises dynamiques disponibles : {year} pour l\'année actuelle, {site_title} pour le nom du site.', 'tools-adapter' ),
			]
		);

		$this->add_control(
			'link_site_title',
			[
				'label'        => esc_html__( 'Ajouter un lien sur le titre du site', 'tools-adapter' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Oui', 'tools-adapter' ),
				'label_off'    => esc_html__( 'Non', 'tools-adapter' ),
				'return_value' => 'yes',
				'default'      => 'no',
			]
		);

		$this->add_control(
			'site_title_link_url',
			[
				'label'         => esc_html__( 'URL du lien', 'tools-adapter' ),
				'type'          => Controls_Manager::URL,
				'default'       => [ 'url' => home_url( '/' ) ],
				'show_external' => true,
				'condition'     => [ 'link_site_title' => 'yes' ],
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
					'{{WRAPPER}} .ta-site-copyright-wrap' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		// ==========================================
		// SECTION STYLE : TYPOGRAPHIE & COULEURS
		// ==========================================
		$this->start_controls_section(
			'section_style_copyright',
			[
				'label' => esc_html__( 'Typographie & Couleurs', 'tools-adapter' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'copyright_typography',
				'selector' => '{{WRAPPER}} .ta-site-copyright-text',
			]
		);

		$this->add_control(
			'copyright_color',
			[
				'label'     => esc_html__( 'Couleur du texte', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#64748b',
				'selectors' => [
					'{{WRAPPER}} .ta-site-copyright-text' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'link_color',
			[
				'label'     => esc_html__( 'Couleur des liens', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ta-site-copyright-text a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'link_hover_color',
			[
				'label'     => esc_html__( 'Couleur des liens au survol', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ta-site-copyright-text a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		$raw_text = $settings['copyright_text'];
		$year     = date_i18n( 'Y' );
		$site_title = get_bloginfo( 'name' );

		if ( 'yes' === $settings['link_site_title'] && ! empty( $settings['site_title_link_url']['url'] ) ) {
			$url = $settings['site_title_link_url']['url'];
			$ext = ! empty( $settings['site_title_link_url']['is_external'] ) ? ' target="_blank" rel="noopener noreferrer"' : '';
			$site_title = sprintf( '<a href="%s"%s>%s</a>', esc_url( $url ), $ext, esc_html( $site_title ) );
		} else {
			$site_title = esc_html( $site_title );
		}

		$formatted = str_replace(
			[ '{year}', '{current_year}', '{site_title}' ],
			[ $year, $year, $site_title ],
			$raw_text
		);
		?>
		<div class="ta-site-copyright-wrap">
			<p class="ta-site-copyright-text"><?php echo wp_kses_post( $formatted ); ?></p>
		</div>
		<?php
	}
}
