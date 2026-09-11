<?php
namespace ToolsAdapter\Widgets;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use Elementor\Repeater;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Widget: Bandeau cookies / RGPD — bannière de consentement avec catégories
 * personnalisables, mémorisée via cookie navigateur.
 */
class Cookie_Banner extends Widget_Base {

	public function get_name() {
		return 'tools-adapter-cookie-banner';
	}

	public function get_title() {
		return esc_html__( 'Bandeau cookies (RGPD)', 'tools-adapter' );
	}

	public function get_icon() {
		return 'eicon-cookies';
	}

	public function get_categories() {
		return [ 'tools-adapter' ];
	}

	public function get_keywords() {
		return [ 'cookies', 'rgpd', 'gdpr', 'consentement', 'confidentialité' ];
	}

	public function get_style_depends() {
		return [ 'tools-adapter-cookie-banner' ];
	}

	public function get_script_depends() {
		return [ 'tools-adapter-cookie-banner' ];
	}

	protected function register_controls() {
		$this->start_controls_section( 'section_content', [ 'label' => esc_html__( 'Message', 'tools-adapter' ) ] );

		$this->add_control(
			'message',
			[
				'label'   => esc_html__( 'Texte', 'tools-adapter' ),
				'type'    => Controls_Manager::TEXTAREA,
				'rows'    => 4,
				'default' => esc_html__( 'Nous utilisons des cookies pour améliorer votre expérience, mesurer notre audience et vous proposer des offres personnalisées. Vous pouvez accepter, refuser ou personnaliser vos choix à tout moment.', 'tools-adapter' ),
			]
		);

		$this->add_control( 'show_link', [ 'label' => esc_html__( 'Lien politique de confidentialité', 'tools-adapter' ), 'type' => Controls_Manager::SWITCHER, 'return_value' => 'yes', 'default' => 'yes' ] );
		$this->add_control( 'link_text', [ 'label' => esc_html__( 'Texte du lien', 'tools-adapter' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'En savoir plus', 'tools-adapter' ), 'condition' => [ 'show_link' => 'yes' ] ] );
		$this->add_control( 'link_url', [ 'label' => esc_html__( 'URL', 'tools-adapter' ), 'type' => Controls_Manager::URL, 'condition' => [ 'show_link' => 'yes' ] ] );

		$this->add_control( 'accept_text', [ 'label' => esc_html__( 'Texte bouton « Accepter »', 'tools-adapter' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'Tout accepter', 'tools-adapter' ), 'separator' => 'before' ] );
		$this->add_control( 'show_decline', [ 'label' => esc_html__( 'Bouton « Refuser »', 'tools-adapter' ), 'type' => Controls_Manager::SWITCHER, 'return_value' => 'yes', 'default' => 'yes' ] );
		$this->add_control( 'decline_text', [ 'label' => esc_html__( 'Texte bouton « Refuser »', 'tools-adapter' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'Tout refuser', 'tools-adapter' ), 'condition' => [ 'show_decline' => 'yes' ] ] );
		$this->add_control( 'show_customize', [ 'label' => esc_html__( 'Bouton « Personnaliser »', 'tools-adapter' ), 'type' => Controls_Manager::SWITCHER, 'return_value' => 'yes', 'default' => 'yes' ] );
		$this->add_control( 'customize_text', [ 'label' => esc_html__( 'Texte bouton « Personnaliser »', 'tools-adapter' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'Personnaliser', 'tools-adapter' ), 'condition' => [ 'show_customize' => 'yes' ] ] );
		$this->add_control( 'save_prefs_text', [ 'label' => esc_html__( 'Texte bouton « Enregistrer »', 'tools-adapter' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'Enregistrer mes préférences', 'tools-adapter' ), 'condition' => [ 'show_customize' => 'yes' ] ] );

		$this->end_controls_section();

		$this->start_controls_section( 'section_categories', [ 'label' => esc_html__( 'Catégories de cookies', 'tools-adapter' ) ] );

		$repeater = new Repeater();
		$repeater->add_control( 'name', [ 'label' => esc_html__( 'Nom', 'tools-adapter' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'Catégorie', 'tools-adapter' ) ] );
		$repeater->add_control( 'key', [ 'label' => esc_html__( 'Identifiant technique', 'tools-adapter' ), 'type' => Controls_Manager::TEXT, 'default' => 'category', 'description' => esc_html__( 'Sans espaces ni accents, ex : analytics, marketing.', 'tools-adapter' ) ] );
		$repeater->add_control( 'description', [ 'label' => esc_html__( 'Description', 'tools-adapter' ), 'type' => Controls_Manager::TEXTAREA, 'rows' => 2, 'default' => '' ] );
		$repeater->add_control( 'locked', [ 'label' => esc_html__( 'Toujours actif (non désactivable)', 'tools-adapter' ), 'type' => Controls_Manager::SWITCHER, 'return_value' => 'yes', 'default' => '' ] );

		$this->add_control(
			'categories',
			[
				'label'       => esc_html__( 'Catégories', 'tools-adapter' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => [
					[ 'name' => esc_html__( 'Nécessaires', 'tools-adapter' ), 'key' => 'necessary', 'description' => esc_html__( 'Indispensables au fonctionnement du site, ne peuvent pas être désactivés.', 'tools-adapter' ), 'locked' => 'yes' ],
					[ 'name' => esc_html__( 'Statistiques', 'tools-adapter' ), 'key' => 'analytics', 'description' => esc_html__( 'Nous aident à comprendre comment le site est utilisé.', 'tools-adapter' ) ],
					[ 'name' => esc_html__( 'Marketing', 'tools-adapter' ), 'key' => 'marketing', 'description' => esc_html__( 'Utilisés pour vous proposer des publicités pertinentes.', 'tools-adapter' ) ],
				],
				'title_field' => '{{{ name }}}',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section( 'section_settings', [ 'label' => esc_html__( 'Réglages', 'tools-adapter' ) ] );

		$this->add_control( 'cookie_name', [ 'label' => esc_html__( 'Nom du cookie', 'tools-adapter' ), 'type' => Controls_Manager::TEXT, 'default' => 'ta_cookie_consent' ] );
		$this->add_control( 'cookie_expiry', [ 'label' => esc_html__( 'Durée de validité (jours)', 'tools-adapter' ), 'type' => Controls_Manager::NUMBER, 'default' => 180, 'min' => 1, 'max' => 730 ] );

		$this->add_control(
			'position',
			[
				'label'   => esc_html__( 'Position', 'tools-adapter' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'bottom',
				'options' => [
					'bottom' => esc_html__( 'Bas de page', 'tools-adapter' ),
					'top'    => esc_html__( 'Haut de page', 'tools-adapter' ),
				],
			]
		);

		$this->add_control(
			'layout',
			[
				'label'        => esc_html__( 'Mise en page', 'tools-adapter' ),
				'type'         => Controls_Manager::SELECT,
				'default'      => 'bar',
				'options'      => [
					'bar' => esc_html__( 'Barre pleine largeur', 'tools-adapter' ),
					'box' => esc_html__( 'Boîte flottante (coin)', 'tools-adapter' ),
				],
				'prefix_class' => 'ta-cookie-layout--',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section( 'section_style', [ 'label' => esc_html__( 'Style', 'tools-adapter' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_control( 'bg_color', [ 'label' => esc_html__( 'Fond', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#1c1c1c', 'selectors' => [ '{{WRAPPER}} .ta-cookie-banner' => 'background-color: {{VALUE}};' ] ] );
		$this->add_control( 'text_color', [ 'label' => esc_html__( 'Couleur du texte', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#f5f5f5', 'selectors' => [ '{{WRAPPER}} .ta-cookie-banner__text, {{WRAPPER}} .ta-cookie-banner__category-name' => 'color: {{VALUE}};' ] ] );
		$this->add_group_control( Group_Control_Typography::get_type(), [ 'name' => 'text_typography', 'selector' => '{{WRAPPER}} .ta-cookie-banner__text' ] );
		$this->add_control( 'link_color', [ 'label' => esc_html__( 'Couleur du lien', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#C9A84C', 'selectors' => [ '{{WRAPPER}} .ta-cookie-banner__link' => 'color: {{VALUE}};' ] ] );

		$this->add_control( 'accept_heading', [ 'label' => esc_html__( 'Bouton Accepter', 'tools-adapter' ), 'type' => Controls_Manager::HEADING, 'separator' => 'before' ] );
		$this->add_control( 'accept_bg', [ 'label' => esc_html__( 'Fond', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#C9A84C', 'selectors' => [ '{{WRAPPER}} .ta-cookie-banner__btn--accept' => 'background-color: {{VALUE}};' ] ] );
		$this->add_control( 'accept_color', [ 'label' => esc_html__( 'Texte', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#1c1c1c', 'selectors' => [ '{{WRAPPER}} .ta-cookie-banner__btn--accept' => 'color: {{VALUE}};' ] ] );

		$this->add_control( 'secondary_heading', [ 'label' => esc_html__( 'Boutons secondaires', 'tools-adapter' ), 'type' => Controls_Manager::HEADING, 'separator' => 'before' ] );
		$this->add_control( 'secondary_color', [ 'label' => esc_html__( 'Texte', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#f5f5f5', 'selectors' => [ '{{WRAPPER}} .ta-cookie-banner__btn--secondary' => 'color: {{VALUE}}; border-color: {{VALUE}};' ] ] );

		$this->add_control( 'buttons_radius', [ 'label' => esc_html__( 'Arrondi des boutons', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'range' => [ 'px' => [ 'min' => 0, 'max' => 40 ] ], 'default' => [ 'size' => 6, 'unit' => 'px' ], 'separator' => 'before', 'selectors' => [ '{{WRAPPER}} .ta-cookie-banner__btn' => 'border-radius: {{SIZE}}{{UNIT}};' ] ] );
		$this->add_group_control( Group_Control_Box_Shadow::get_type(), [ 'name' => 'banner_shadow', 'selector' => '{{WRAPPER}} .ta-cookie-banner' ] );

		$this->end_controls_section();
	}

	protected function render() {
		$settings   = $this->get_settings_for_display();
		$categories = $settings['categories'] ?? [];
		$position   = $settings['position'] ?? 'bottom';
		$cookie_name   = sanitize_key( $settings['cookie_name'] ?? 'ta_cookie_consent' );
		$cookie_expiry = absint( $settings['cookie_expiry'] ?? 180 );
		$link_url      = $settings['link_url']['url'] ?? '';
		?>
		<div
			class="ta-cookie-banner ta-cookie-banner--<?php echo esc_attr( $position ); ?>"
			data-ta-cookie-banner
			data-cookie-name="<?php echo esc_attr( $cookie_name ); ?>"
			data-cookie-expiry="<?php echo esc_attr( (string) $cookie_expiry ); ?>"
			hidden
		>
			<div class="ta-cookie-banner__inner">
				<div class="ta-cookie-banner__main">
					<p class="ta-cookie-banner__text">
						<?php echo esc_html( \tools_adapter_translate( $settings['message'] ?? '' ) ); ?>
						<?php if ( 'yes' === ( $settings['show_link'] ?? '' ) && ! empty( $link_url ) ) : ?>
							<a class="ta-cookie-banner__link" href="<?php echo esc_url( $link_url ); ?>" target="_blank" rel="noopener"><?php echo esc_html( \tools_adapter_translate( $settings['link_text'] ?? '' ) ); ?></a>
						<?php endif; ?>
					</p>

					<?php if ( ! empty( $categories ) ) : ?>
						<div class="ta-cookie-banner__categories" data-cookie-categories hidden>
							<?php foreach ( $categories as $index => $cat ) :
								$key    = sanitize_key( $cat['key'] ?? ( 'cat_' . $index ) );
								$locked = 'yes' === ( $cat['locked'] ?? '' );
								?>
								<label class="ta-cookie-banner__category">
									<input type="checkbox" data-cookie-category="<?php echo esc_attr( $key ); ?>" <?php echo ( $locked ) ? 'checked disabled' : 'checked'; ?> />
									<span>
										<span class="ta-cookie-banner__category-name"><?php echo esc_html( \tools_adapter_translate( $cat['name'] ?? '' ) ); ?></span>
										<?php if ( ! empty( $cat['description'] ) ) : ?>
											<span class="ta-cookie-banner__category-desc"><?php echo esc_html( \tools_adapter_translate( $cat['description'] ) ); ?></span>
										<?php endif; ?>
									</span>
								</label>
							<?php endforeach; ?>
							<?php if ( ! empty( $settings['save_prefs_text'] ) ) : ?>
								<button type="button" class="ta-cookie-banner__btn ta-cookie-banner__btn--accept ta-cookie-banner__save" data-cookie-save><?php echo esc_html( \tools_adapter_translate( $settings['save_prefs_text'] ) ); ?></button>
							<?php endif; ?>
						</div>
					<?php endif; ?>
				</div>

				<div class="ta-cookie-banner__actions">
					<?php if ( 'yes' === ( $settings['show_customize'] ?? '' ) && ! empty( $categories ) ) : ?>
						<button type="button" class="ta-cookie-banner__btn ta-cookie-banner__btn--secondary" data-cookie-customize><?php echo esc_html( \tools_adapter_translate( $settings['customize_text'] ?? '' ) ); ?></button>
					<?php endif; ?>
					<?php if ( 'yes' === ( $settings['show_decline'] ?? '' ) ) : ?>
						<button type="button" class="ta-cookie-banner__btn ta-cookie-banner__btn--secondary" data-cookie-decline><?php echo esc_html( \tools_adapter_translate( $settings['decline_text'] ?? '' ) ); ?></button>
					<?php endif; ?>
					<button type="button" class="ta-cookie-banner__btn ta-cookie-banner__btn--accept" data-cookie-accept><?php echo esc_html( \tools_adapter_translate( $settings['accept_text'] ?? '' ) ); ?></button>
				</div>
			</div>
		</div>
		<?php
	}
}
