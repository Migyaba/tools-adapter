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
 * Widget: Équipe — grille de membres avec photo, poste, bio et réseaux sociaux.
 */
class Team_Members extends Base_Widget {

	public function get_name() {
		return 'tools-adapter-team';
	}

	public function get_title() {
		return esc_html__( 'Équipe', 'tools-adapter' );
	}

	public function get_icon() {
		return 'eicon-person';
	}

	public function get_categories() {
		return [ 'tools-adapter' ];
	}

	public function get_keywords() {
		return [ 'équipe', 'team', 'membres', 'staff' ];
	}

	public function get_style_depends() {
		return [ 'tools-adapter-team' ];
	}

	protected function register_controls() {
		$this->start_controls_section( 'section_content', [ 'label' => esc_html__( 'Membres', 'tools-adapter' ) ] );

		$repeater = new Repeater();
		$repeater->add_control( 'photo', [ 'label' => esc_html__( 'Photo', 'tools-adapter' ), 'type' => Controls_Manager::MEDIA, 'default' => [ 'url' => \Elementor\Utils::get_placeholder_image_src() ] ] );
		$repeater->add_control( 'name', [ 'label' => esc_html__( 'Nom', 'tools-adapter' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'Prénom Nom', 'tools-adapter' ) ] );
		$repeater->add_control( 'role', [ 'label' => esc_html__( 'Poste', 'tools-adapter' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'Fondateur', 'tools-adapter' ) ] );
		$repeater->add_control( 'bio', [ 'label' => esc_html__( 'Bio', 'tools-adapter' ), 'type' => Controls_Manager::TEXTAREA, 'rows' => 3, 'default' => '' ] );
		$repeater->add_control( 'link_facebook', [ 'label' => esc_html__( 'Facebook', 'tools-adapter' ), 'type' => Controls_Manager::URL ] );
		$repeater->add_control( 'link_x', [ 'label' => esc_html__( 'X / Twitter', 'tools-adapter' ), 'type' => Controls_Manager::URL ] );
		$repeater->add_control( 'link_instagram', [ 'label' => esc_html__( 'Instagram', 'tools-adapter' ), 'type' => Controls_Manager::URL ] );
		$repeater->add_control( 'link_linkedin', [ 'label' => esc_html__( 'LinkedIn', 'tools-adapter' ), 'type' => Controls_Manager::URL ] );

		$this->add_control(
			'members',
			[
				'label'       => esc_html__( 'Membres', 'tools-adapter' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => [ [], [], [] ],
				'title_field' => '{{{ name }}}',
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
				'selectors'      => [ '{{WRAPPER}} .ta-team' => '--ta-team-cols: {{VALUE}};' ],
			]
		);

		$this->add_control(
			'photo_shape',
			[
				'label'        => esc_html__( 'Forme de la photo', 'tools-adapter' ),
				'type'         => Controls_Manager::SELECT,
				'default'      => 'circle',
				'options'      => [ 'circle' => esc_html__( 'Cercle', 'tools-adapter' ), 'square' => esc_html__( 'Carré (arrondi)', 'tools-adapter' ) ],
				'prefix_class' => 'ta-team-photo--',
			]
		);

		$this->add_control( 'text_align', [ 'label' => esc_html__( 'Alignement', 'tools-adapter' ), 'type' => Controls_Manager::CHOOSE, 'options' => [ 'left' => [ 'title' => esc_html__( 'Gauche', 'tools-adapter' ), 'icon' => 'eicon-text-align-left' ], 'center' => [ 'title' => esc_html__( 'Centre', 'tools-adapter' ), 'icon' => 'eicon-text-align-center' ] ], 'default' => 'center', 'prefix_class' => 'ta-team-align--' ] );

		$this->end_controls_section();

		$this->start_controls_section( 'section_style_card', [ 'label' => esc_html__( 'Carte', 'tools-adapter' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_control( 'card_bg', [ 'label' => esc_html__( 'Fond', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#ffffff', 'selectors' => [ '{{WRAPPER}} .ta-team__member' => 'background-color: {{VALUE}};' ] ] );
		$this->add_responsive_control( 'card_padding', [ 'label' => esc_html__( 'Espacement interne', 'tools-adapter' ), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => [ 'px' ], 'default' => [ 'top' => '28', 'right' => '24', 'bottom' => '28', 'left' => '24', 'unit' => 'px' ], 'selectors' => [ '{{WRAPPER}} .ta-team__member' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ] ] );
		$this->add_control( 'card_radius', [ 'label' => esc_html__( 'Arrondi', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'range' => [ 'px' => [ 'min' => 0, 'max' => 60 ] ], 'selectors' => [ '{{WRAPPER}} .ta-team__member' => 'border-radius: {{SIZE}}{{UNIT}};' ] ] );
		$this->add_group_control( Group_Control_Border::get_type(), [ 'name' => 'card_border', 'selector' => '{{WRAPPER}} .ta-team__member' ] );
		$this->add_group_control( Group_Control_Box_Shadow::get_type(), [ 'name' => 'card_shadow', 'selector' => '{{WRAPPER}} .ta-team__member' ] );
		$this->add_responsive_control( 'items_gap', [ 'label' => esc_html__( 'Espacement', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'range' => [ 'px' => [ 'min' => 0, 'max' => 60 ] ], 'default' => [ 'size' => 24, 'unit' => 'px' ], 'separator' => 'before', 'selectors' => [ '{{WRAPPER}} .ta-team' => '--ta-team-gap: {{SIZE}}{{UNIT}};' ] ] );

		$this->end_controls_section();

		$this->start_controls_section( 'section_style_content', [ 'label' => esc_html__( 'Contenu', 'tools-adapter' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_control( 'photo_size', [ 'label' => esc_html__( 'Taille photo', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'range' => [ 'px' => [ 'min' => 60, 'max' => 220 ] ], 'default' => [ 'size' => 120, 'unit' => 'px' ], 'selectors' => [ '{{WRAPPER}} .ta-team__photo' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};' ] ] );
		$this->add_control( 'name_color', [ 'label' => esc_html__( 'Couleur du nom', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#1c1c1c', 'selectors' => [ '{{WRAPPER}} .ta-team__name' => 'color: {{VALUE}};' ] ] );
		$this->add_group_control( Group_Control_Typography::get_type(), [ 'name' => 'name_typography', 'selector' => '{{WRAPPER}} .ta-team__name' ] );
		$this->add_control( 'role_color', [ 'label' => esc_html__( 'Couleur du poste', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#C9A84C', 'separator' => 'before', 'selectors' => [ '{{WRAPPER}} .ta-team__role' => 'color: {{VALUE}};' ] ] );
		$this->add_control( 'bio_color', [ 'label' => esc_html__( 'Couleur de la bio', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#777777', 'separator' => 'before', 'selectors' => [ '{{WRAPPER}} .ta-team__bio' => 'color: {{VALUE}};' ] ] );
		$this->add_control( 'social_color', [ 'label' => esc_html__( 'Couleur icônes sociales', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#666666', 'separator' => 'before', 'selectors' => [ '{{WRAPPER}} .ta-team__social a' => 'color: {{VALUE}};' ] ] );
		$this->add_control( 'social_color_hover', [ 'label' => esc_html__( 'Couleur icônes (survol)', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#C9A84C', 'selectors' => [ '{{WRAPPER}} .ta-team__social a:hover' => 'color: {{VALUE}};' ] ] );

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$members  = $settings['members'] ?? [];
		if ( empty( $members ) ) {
			return;
		}

		$socials = [
			'link_facebook'  => [ 'fab fa-facebook-f', esc_html__( 'Facebook', 'tools-adapter' ) ],
			'link_x'         => [ 'fab fa-x-twitter', esc_html__( 'X / Twitter', 'tools-adapter' ) ],
			'link_instagram' => [ 'fab fa-instagram', esc_html__( 'Instagram', 'tools-adapter' ) ],
			'link_linkedin'  => [ 'fab fa-linkedin-in', esc_html__( 'LinkedIn', 'tools-adapter' ) ],
		];
		?>
		<div class="ta-team">
			<?php foreach ( $members as $member ) : ?>
				<div class="ta-team__member">
					<?php if ( ! empty( $member['photo']['url'] ) ) : ?>
						<img class="ta-team__photo" src="<?php echo esc_url( $member['photo']['url'] ); ?>" alt="<?php echo esc_attr( $member['name'] ?? '' ); ?>" loading="lazy" />
					<?php endif; ?>
					<?php if ( ! empty( $member['name'] ) ) : ?><p class="ta-team__name"><?php echo esc_html( \tools_adapter_translate( $member['name'] ) ); ?></p><?php endif; ?>
					<?php if ( ! empty( $member['role'] ) ) : ?><p class="ta-team__role"><?php echo esc_html( \tools_adapter_translate( $member['role'] ) ); ?></p><?php endif; ?>
					<?php if ( ! empty( $member['bio'] ) ) : ?><p class="ta-team__bio"><?php echo esc_html( \tools_adapter_translate( $member['bio'] ) ); ?></p><?php endif; ?>
					<?php
					$has_social = false;
					foreach ( $socials as $key => $data ) {
						if ( ! empty( $member[ $key ]['url'] ) ) {
							$has_social = true;
							break;
						}
					}
					?>
					<?php if ( $has_social ) : ?>
						<div class="ta-team__social">
							<?php foreach ( $socials as $key => $data ) : ?>
								<?php if ( ! empty( $member[ $key ]['url'] ) ) : ?>
									<a href="<?php echo esc_url( $member[ $key ]['url'] ); ?>" target="_blank" rel="noopener nofollow" aria-label="<?php echo esc_attr( $data[1] ); ?>">
										<i class="<?php echo esc_attr( $data[0] ); ?>" aria-hidden="true"></i>
									</a>
								<?php endif; ?>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>
		<?php
	}
}
