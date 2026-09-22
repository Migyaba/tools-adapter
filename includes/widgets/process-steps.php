<?php
namespace ToolsAdapter\Widgets;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use Elementor\Repeater;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Icons_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Widget: Étapes / Processus — présentation étape par étape avec ligne de connexion et badge Icône ou Numéro.
 */
class Process_Steps extends Widget_Base {

	public function get_name() {
		return 'tools-adapter-process-steps';
	}

	public function get_title() {
		return esc_html__( 'Étapes / Processus', 'tools-adapter' );
	}

	public function get_icon() {
		return 'eicon-flow';
	}

	public function get_categories() {
		return [ 'tools-adapter' ];
	}

	public function get_keywords() {
		return [ 'processus', 'étapes', 'steps', 'timeline', 'méthode', 'flow', 'déroulement' ];
	}

	public function get_style_depends() {
		return [ 'tools-adapter-process-steps' ];
	}

	protected function register_controls() {
		// ==========================================
		// SECTION CONTENU : ÉTAPES
		// ==========================================
		$this->start_controls_section(
			'section_steps',
			[ 'label' => esc_html__( 'Étapes du processus', 'tools-adapter' ) ]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'title',
			[
				'label'       => esc_html__( 'Titre de l\'étape', 'tools-adapter' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Titre de l\'étape', 'tools-adapter' ),
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'description',
			[
				'label'       => esc_html__( 'Description', 'tools-adapter' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 3,
				'default'     => esc_html__( 'Description détaillée de cette phase du projet.', 'tools-adapter' ),
			]
		);

		$repeater->add_control(
			'badge_override',
			[
				'label'   => esc_html__( 'Type de badge pour cette étape', 'tools-adapter' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'inherit',
				'options' => [
					'inherit'      => esc_html__( 'Par défaut (paramètre global)', 'tools-adapter' ),
					'icon'         => esc_html__( 'Icône', 'tools-adapter' ),
					'number_auto'  => esc_html__( 'Numéro automatique', 'tools-adapter' ),
					'custom_text'  => esc_html__( 'Texte / Numéro personnalisé', 'tools-adapter' ),
				],
			]
		);

		$repeater->add_control(
			'icon',
			[
				'label'       => esc_html__( 'Icône', 'tools-adapter' ),
				'type'        => Controls_Manager::ICONS,
				'default'     => [
					'value'   => 'fas fa-check',
					'library' => 'fa-solid',
				],
				'condition'   => [
					'badge_override' => [ 'inherit', 'icon' ],
				],
			]
		);

		$repeater->add_control(
			'custom_badge_text',
			[
				'label'       => esc_html__( 'Texte / Numéro personnalisé', 'tools-adapter' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => '01 ou Étape 1',
				'condition'   => [
					'badge_override' => 'custom_text',
				],
			]
		);

		$repeater->add_control(
			'link',
			[
				'label'       => esc_html__( 'Lien (optionnel)', 'tools-adapter' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => 'https://exemple.com',
			]
		);

		$this->add_control(
			'steps',
			[
				'label'       => esc_html__( 'Liste des étapes', 'tools-adapter' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => [
					[
						'title'       => esc_html__( 'Prise de contact', 'tools-adapter' ),
						'description' => esc_html__( 'Vous nous exposez votre projet et vos envies, gratuitement et sans engagement.', 'tools-adapter' ),
						'icon'        => [ 'value' => 'fas fa-phone-volume', 'library' => 'fa-solid' ],
					],
					[
						'title'       => esc_html__( 'Étude sur-mesure', 'tools-adapter' ),
						'description' => esc_html__( 'Notre bureau d\'étude conçoit des plans et perspectives adaptés à votre espace.', 'tools-adapter' ),
						'icon'        => [ 'value' => 'fas fa-pencil-ruler', 'library' => 'fa-solid' ],
					],
					[
						'title'       => esc_html__( 'Réalisation', 'tools-adapter' ),
						'description' => esc_html__( 'Nos équipes réalisent les travaux avec un suivi de chantier à chaque étape.', 'tools-adapter' ),
						'icon'        => [ 'value' => 'fas fa-tools', 'library' => 'fa-solid' ],
					],
					[
						'title'       => esc_html__( 'Entretien', 'tools-adapter' ),
						'description' => esc_html__( 'Un contrat personnalisé pour préserver la beauté de votre espace toute l\'année.', 'tools-adapter' ),
						'icon'        => [ 'value' => 'fas fa-seedling', 'library' => 'fa-solid' ],
					],
				],
				'title_field' => '{{{ title }}}',
			]
		);

		$this->end_controls_section();

		// ==========================================
		// SECTION CONTENU : CONFIGURATION DU BADGE
		// ==========================================
		$this->start_controls_section(
			'section_badge_config',
			[ 'label' => esc_html__( 'Type de badge (Icône / Numéro)', 'tools-adapter' ) ]
		);

		$this->add_control(
			'badge_type',
			[
				'label'   => esc_html__( 'Affichage du badge', 'tools-adapter' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'icon',
				'options' => [
					'icon'          => esc_html__( 'Icône', 'tools-adapter' ),
					'number_auto_2' => esc_html__( 'Numéro à deux chiffres (01, 02, 03...)', 'tools-adapter' ),
					'number_auto_1' => esc_html__( 'Numéro simple (1, 2, 3...)', 'tools-adapter' ),
				],
			]
		);

		$this->add_control(
			'badge_shape',
			[
				'label'   => esc_html__( 'Forme du badge', 'tools-adapter' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'rounded',
				'options' => [
					'rounded' => esc_html__( 'Carré arrondi (comme l\'image)', 'tools-adapter' ),
					'circle'  => esc_html__( 'Cercle', 'tools-adapter' ),
					'square'  => esc_html__( 'Carré', 'tools-adapter' ),
				],
			]
		);

		$this->end_controls_section();

		// ==========================================
		// SECTION CONTENU : LIGNE DE CONNEXION
		// ==========================================
		$this->start_controls_section(
			'section_connector_config',
			[ 'label' => esc_html__( 'Ligne de liaison', 'tools-adapter' ) ]
		);

		$this->add_control(
			'show_connector',
			[
				'label'        => esc_html__( 'Afficher la ligne de liaison', 'tools-adapter' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'connector_style',
			[
				'label'     => esc_html__( 'Style de la ligne', 'tools-adapter' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'solid',
				'options'   => [
					'solid'  => esc_html__( 'Ligne continue', 'tools-adapter' ),
					'dashed' => esc_html__( 'Tirets (Dashed)', 'tools-adapter' ),
					'dotted' => esc_html__( 'Pointillés (Dotted)', 'tools-adapter' ),
				],
				'selectors' => [ '{{WRAPPER}} .ta-process-steps' => '--ta-ps-line-style: {{VALUE}};' ],
				'condition' => [ 'show_connector' => 'yes' ],
			]
		);

		$this->end_controls_section();

		// ==========================================
		// SECTION STYLE : DISPOSITION & COLONNES
		// ==========================================
		$this->start_controls_section(
			'section_style_layout',
			[
				'label' => esc_html__( 'Disposition & Colonnes', 'tools-adapter' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'columns',
			[
				'label'          => esc_html__( 'Colonnes', 'tools-adapter' ),
				'type'           => Controls_Manager::NUMBER,
				'min'            => 1,
				'max'            => 6,
				'default'        => 4,
				'tablet_default' => 2,
				'mobile_default' => 1,
				'selectors'      => [
					'{{WRAPPER}} .ta-process-steps' => '--ta-ps-cols: {{VALUE}};',
					'{{WRAPPER}} .ta-process-steps__items' => '--ta-ps-cols-tablet: {{tablet}};',
				],
			]
		);

		$this->add_responsive_control(
			'grid_gap',
			[
				'label'      => esc_html__( 'Espacement entre étapes', 'tools-adapter' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 'px' => [ 'min' => 10, 'max' => 60 ] ],
				'default'    => [ 'size' => 32, 'unit' => 'px' ],
				'selectors'  => [ '{{WRAPPER}} .ta-process-steps' => '--ta-ps-gap: {{SIZE}}{{UNIT}};' ],
			]
		);

		$this->add_control(
			'alignment',
			[
				'label'   => esc_html__( 'Alignement du texte', 'tools-adapter' ),
				'type'    => Controls_Manager::CHOOSE,
				'options' => [
					'left'   => [ 'title' => esc_html__( 'Gauche', 'tools-adapter' ), 'icon' => 'eicon-text-align-left' ],
					'center' => [ 'title' => esc_html__( 'Centré', 'tools-adapter' ), 'icon' => 'eicon-text-align-center' ],
				],
				'default' => 'left',
			]
		);

		$this->end_controls_section();

		// ==========================================
		// SECTION STYLE : BADGES
		// ==========================================
		$this->start_controls_section(
			'section_style_badges',
			[
				'label' => esc_html__( 'Badges (Icône / Numéro)', 'tools-adapter' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'badge_size',
			[
				'label'      => esc_html__( 'Taille du badge', 'tools-adapter' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 'px' => [ 'min' => 40, 'max' => 100 ] ],
				'default'    => [ 'size' => 60, 'unit' => 'px' ],
				'selectors'  => [ '{{WRAPPER}} .ta-process-steps' => '--ta-ps-badge-size: {{SIZE}}{{UNIT}};' ],
			]
		);

		$this->add_responsive_control(
			'icon_size',
			[
				'label'      => esc_html__( 'Taille de l\'icône', 'tools-adapter' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 'px' => [ 'min' => 16, 'max' => 50 ] ],
				'default'    => [ 'size' => 26, 'unit' => 'px' ],
				'selectors'  => [ '{{WRAPPER}} .ta-process-steps' => '--ta-ps-icon-size: {{SIZE}}{{UNIT}};' ],
			]
		);

		$this->add_responsive_control(
			'num_size',
			[
				'label'      => esc_html__( 'Taille du numéro', 'tools-adapter' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 'px' => [ 'min' => 14, 'max' => 40 ] ],
				'default'    => [ 'size' => 20, 'unit' => 'px' ],
				'selectors'  => [ '{{WRAPPER}} .ta-process-steps' => '--ta-ps-num-size: {{SIZE}}{{UNIT}};' ],
			]
		);

		$this->add_control(
			'badge_bg',
			[
				'label'     => esc_html__( 'Couleur de fond du badge', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#7bc81d',
				'selectors' => [ '{{WRAPPER}} .ta-process-steps' => '--ta-ps-badge-bg: {{VALUE}};' ],
			]
		);

		$this->add_control(
			'badge_color',
			[
				'label'     => esc_html__( 'Couleur de l\'icône / chiffre', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [ '{{WRAPPER}} .ta-process-steps' => '--ta-ps-badge-color: {{VALUE}};' ],
			]
		);

		$this->add_control(
			'badge_radius',
			[
				'label'      => esc_html__( 'Arrondi du badge', 'tools-adapter' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'      => [ 'px' => [ 'min' => 0, 'max' => 50 ] ],
				'default'    => [ 'size' => 16, 'unit' => 'px' ],
				'selectors'  => [ '{{WRAPPER}} .ta-process-steps' => '--ta-ps-badge-radius: {{SIZE}}{{UNIT}};' ],
				'condition'  => [ 'badge_shape' => 'rounded' ],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'badge_shadow',
				'label'    => esc_html__( 'Ombre du badge', 'tools-adapter' ),
				'selector' => '{{WRAPPER}} .ta-process-step__badge',
			]
		);

		$this->add_control(
			'hover_zoom',
			[
				'label'        => esc_html__( 'Zoom doux au survol', 'tools-adapter' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->end_controls_section();

		// ==========================================
		// SECTION STYLE : LIGNE DE CONNEXION
		// ==========================================
		$this->start_controls_section(
			'section_style_connector',
			[
				'label'     => esc_html__( 'Ligne de liaison', 'tools-adapter' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [ 'show_connector' => 'yes' ],
			]
		);

		$this->add_control(
			'connector_color',
			[
				'label'     => esc_html__( 'Couleur de la ligne', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#d1d5db',
				'selectors' => [ '{{WRAPPER}} .ta-process-steps' => '--ta-ps-line-color: {{VALUE}};' ],
			]
		);

		$this->add_control(
			'connector_thickness',
			[
				'label'      => esc_html__( 'Épaisseur de la ligne', 'tools-adapter' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 'px' => [ 'min' => 1, 'max' => 6 ] ],
				'default'    => [ 'size' => 2, 'unit' => 'px' ],
				'selectors'  => [ '{{WRAPPER}} .ta-process-steps' => '--ta-ps-line-thickness: {{SIZE}}{{UNIT}};' ],
			]
		);

		$this->end_controls_section();

		// ==========================================
		// SECTION STYLE : TYPOGRAPHIE & TEXTES
		// ==========================================
		$this->start_controls_section(
			'section_style_typography',
			[
				'label' => esc_html__( 'Typographie & Textes', 'tools-adapter' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'title_color',
			[
				'label'     => esc_html__( 'Couleur du titre', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#0b2545',
				'selectors' => [ '{{WRAPPER}} .ta-process-step__title' => 'color: {{VALUE}};' ],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .ta-process-step__title',
			]
		);

		$this->add_control(
			'desc_color',
			[
				'label'     => esc_html__( 'Couleur de la description', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#556987',
				'selectors' => [ '{{WRAPPER}} .ta-process-step__description' => 'color: {{VALUE}};' ],
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'desc_typography',
				'selector' => '{{WRAPPER}} .ta-process-step__description',
			]
		);

		$this->add_responsive_control(
			'header_gap',
			[
				'label'      => esc_html__( 'Espacement sous le badge', 'tools-adapter' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 'px' => [ 'min' => 8, 'max' => 40 ] ],
				'default'    => [ 'size' => 20, 'unit' => 'px' ],
				'selectors'  => [ '{{WRAPPER}} .ta-process-steps' => '--ta-ps-header-gap: {{SIZE}}{{UNIT}};' ],
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$steps    = ! empty( $settings['steps'] ) ? $settings['steps'] : [];

		if ( empty( $steps ) ) {
			return;
		}

		$badge_global   = $settings['badge_type'] ?? 'icon';
		$badge_shape    = $settings['badge_shape'] ?? 'rounded';
		$show_line      = ( $settings['show_connector'] ?? 'yes' ) === 'yes';
		$align          = $settings['alignment'] ?? 'left';
		$hover_zoom     = ( $settings['hover_zoom'] ?? 'yes' ) === 'yes';

		$wrapper_classes = [ 'ta-process-steps' ];
		if ( 'center' === $align ) {
			$wrapper_classes[] = 'ta-process-steps--align-center';
		}
		if ( $hover_zoom ) {
			$wrapper_classes[] = 'ta-process-steps--hover-zoom';
		}

		$cols_tablet = ! empty( $settings['columns_tablet'] ) ? absint( $settings['columns_tablet'] ) : 2;
		?>
		<div class="<?php echo esc_attr( implode( ' ', $wrapper_classes ) ); ?>">
			<div class="ta-process-steps__items" data-cols-tablet="<?php echo esc_attr( (string) $cols_tablet ); ?>">
				<?php foreach ( $steps as $index => $step ) : ?>
					<?php
					$step_num = $index + 1;
					$override = $step['badge_override'] ?? 'inherit';
					$resolved_type = ( 'inherit' === $override ) ? $badge_global : $override;

					$title    = $step['title'] ?? '';
					$desc     = $step['description'] ?? '';
					$link_url = ! empty( $step['link']['url'] ) ? $step['link']['url'] : '';
					$target   = ! empty( $step['link']['is_external'] ) ? ' target="_blank"' : '';
					$nofollow = ! empty( $step['link']['nofollow'] ) ? ' rel="nofollow"' : '';

					// Forme du badge (style inline radius si cercle ou carré)
					$badge_style = '';
					if ( 'circle' === $badge_shape ) {
						$badge_style = 'style="border-radius: 50%;"';
					} elseif ( 'square' === $badge_shape ) {
						$badge_style = 'style="border-radius: 0;"';
					}
					?>
					<div class="ta-process-step">
						<div class="ta-process-step__header">
							<div class="ta-process-step__badge" <?php echo $badge_style; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
								<?php if ( 'icon' === $resolved_type ) : ?>
									<?php if ( ! empty( $step['icon']['value'] ) ) : ?>
										<?php Icons_Manager::render_icon( $step['icon'], [ 'aria-hidden' => 'true' ] ); ?>
									<?php else : ?>
										<i class="fas fa-check" aria-hidden="true"></i>
									<?php endif; ?>
								<?php elseif ( 'number_auto_2' === $resolved_type ) : ?>
									<span class="ta-process-step__number"><?php echo esc_html( sprintf( '%02d', $step_num ) ); ?></span>
								<?php elseif ( 'number_auto_1' === $resolved_type || 'number_auto' === $resolved_type ) : ?>
									<span class="ta-process-step__number"><?php echo esc_html( (string) $step_num ); ?></span>
								<?php elseif ( 'custom_text' === $resolved_type ) : ?>
									<span class="ta-process-step__number"><?php echo esc_html( ! empty( $step['custom_badge_text'] ) ? $step['custom_badge_text'] : sprintf( '%02d', $step_num ) ); ?></span>
								<?php endif; ?>
							</div>

							<?php if ( $show_line ) : ?>
								<div class="ta-process-step__line" aria-hidden="true"></div>
							<?php endif; ?>
						</div>

						<div class="ta-process-step__content">
							<?php if ( $title ) : ?>
								<h3 class="ta-process-step__title">
									<?php if ( $link_url ) : ?>
										<a href="<?php echo esc_url( $link_url ); ?>"<?php echo $target . $nofollow; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
											<?php echo esc_html( \tools_adapter_translate( $title ) ); ?>
										</a>
									<?php else : ?>
										<?php echo esc_html( \tools_adapter_translate( $title ) ); ?>
									<?php endif; ?>
								</h3>
							<?php endif; ?>

							<?php if ( $desc ) : ?>
								<div class="ta-process-step__description">
									<?php echo wp_kses_post( wpautop( \tools_adapter_translate( $desc ) ) ); ?>
								</div>
							<?php endif; ?>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
		<?php
	}
}
