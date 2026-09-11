<?php
namespace ToolsAdapter\Widgets;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use Elementor\Repeater;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Widget: FAQ Accordéon.
 */
class Faq_Accordion extends Widget_Base {

	public function get_name() {
		return 'tools-adapter-faq';
	}

	public function get_title() {
		return esc_html__( 'FAQ Accordéon', 'tools-adapter' );
	}

	public function get_icon() {
		return 'eicon-accordion';
	}

	public function get_categories() {
		return [ 'tools-adapter' ];
	}

	public function get_keywords() {
		return [ 'faq', 'accordéon', 'questions', 'réponses' ];
	}

	public function get_style_depends() {
		return [ 'tools-adapter-faq' ];
	}

	public function get_script_depends() {
		return [ 'tools-adapter-faq' ];
	}

	protected function register_controls() {
		$this->start_controls_section( 'section_content', [ 'label' => esc_html__( 'Questions', 'tools-adapter' ) ] );

		$repeater = new Repeater();
		$repeater->add_control( 'question', [ 'label' => esc_html__( 'Question', 'tools-adapter' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'Votre question ici ?', 'tools-adapter' ) ] );
		$repeater->add_control( 'answer', [ 'label' => esc_html__( 'Réponse', 'tools-adapter' ), 'type' => Controls_Manager::TEXTAREA, 'rows' => 4, 'default' => esc_html__( 'Votre réponse ici.', 'tools-adapter' ) ] );
		$repeater->add_control( 'open_default', [ 'label' => esc_html__( 'Ouvert par défaut', 'tools-adapter' ), 'type' => Controls_Manager::SWITCHER, 'return_value' => 'yes', 'default' => '' ] );

		$this->add_control(
			'faqs',
			[
				'label'       => esc_html__( 'Éléments', 'tools-adapter' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => [
					[ 'question' => esc_html__( 'Quels sont les délais de livraison ?', 'tools-adapter' ), 'answer' => esc_html__( 'Les commandes sont expédiées sous 24 à 48h ouvrées.', 'tools-adapter' ), 'open_default' => 'yes' ],
					[ 'question' => esc_html__( 'Puis-je retourner un article ?', 'tools-adapter' ), 'answer' => esc_html__( 'Oui, vous disposez de 30 jours pour changer d\'avis.', 'tools-adapter' ) ],
				],
				'title_field' => '{{{ question }}}',
			]
		);

		$this->add_control(
			'allow_multiple',
			[
				'label'        => esc_html__( 'Autoriser plusieurs ouverts', 'tools-adapter' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
			]
		);

		$this->add_control(
			'toggle_icon',
			[
				'label'   => esc_html__( 'Icône (fermé)', 'tools-adapter' ),
				'type'    => Controls_Manager::ICONS,
				'default' => [ 'value' => 'fas fa-plus', 'library' => 'fa-solid' ],
			]
		);

		$this->add_control(
			'toggle_icon_active',
			[
				'label'   => esc_html__( 'Icône (ouvert)', 'tools-adapter' ),
				'type'    => Controls_Manager::ICONS,
				'default' => [ 'value' => 'fas fa-minus', 'library' => 'fa-solid' ],
			]
		);

		$this->add_control(
			'enable_schema',
			[
				'label'        => esc_html__( 'Balisage SEO (schema.org FAQPage)', 'tools-adapter' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'description'  => esc_html__( 'Ajoute des données structurées pour améliorer l\'affichage dans Google.', 'tools-adapter' ),
			]
		);

		$this->end_controls_section();

		$this->start_controls_section( 'section_style', [ 'label' => esc_html__( 'Style', 'tools-adapter' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_responsive_control( 'items_gap', [ 'label' => esc_html__( 'Espacement entre questions', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'range' => [ 'px' => [ 'min' => 0, 'max' => 40 ] ], 'default' => [ 'size' => 12, 'unit' => 'px' ], 'selectors' => [ '{{WRAPPER}} .ta-faq' => '--ta-faq-gap: {{SIZE}}{{UNIT}};' ] ] );

		$this->add_control( 'question_heading', [ 'label' => esc_html__( 'Question', 'tools-adapter' ), 'type' => Controls_Manager::HEADING, 'separator' => 'before' ] );
		$this->add_control( 'question_color', [ 'label' => esc_html__( 'Couleur texte', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#1c1c1c', 'selectors' => [ '{{WRAPPER}} .ta-faq__question' => 'color: {{VALUE}};' ] ] );
		$this->add_control( 'question_color_active', [ 'label' => esc_html__( 'Couleur texte (ouvert)', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#C9A84C', 'selectors' => [ '{{WRAPPER}} .ta-faq__item.is-open .ta-faq__question' => 'color: {{VALUE}};' ] ] );
		$this->add_control( 'question_bg', [ 'label' => esc_html__( 'Fond', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#f7f7f7', 'selectors' => [ '{{WRAPPER}} .ta-faq__question' => 'background-color: {{VALUE}};' ] ] );
		$this->add_group_control( Group_Control_Typography::get_type(), [ 'name' => 'question_typography', 'selector' => '{{WRAPPER}} .ta-faq__question' ] );

		$this->add_control( 'answer_heading', [ 'label' => esc_html__( 'Réponse', 'tools-adapter' ), 'type' => Controls_Manager::HEADING, 'separator' => 'before' ] );
		$this->add_control( 'answer_color', [ 'label' => esc_html__( 'Couleur texte', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#555555', 'selectors' => [ '{{WRAPPER}} .ta-faq__answer' => 'color: {{VALUE}};' ] ] );
		$this->add_control( 'answer_bg', [ 'label' => esc_html__( 'Fond', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#ffffff', 'selectors' => [ '{{WRAPPER}} .ta-faq__answer' => 'background-color: {{VALUE}};' ] ] );
		$this->add_group_control( Group_Control_Typography::get_type(), [ 'name' => 'answer_typography', 'selector' => '{{WRAPPER}} .ta-faq__answer' ] );

		$this->add_control( 'icon_heading', [ 'label' => esc_html__( 'Icône', 'tools-adapter' ), 'type' => Controls_Manager::HEADING, 'separator' => 'before' ] );
		$this->add_control( 'icon_color', [ 'label' => esc_html__( 'Couleur', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#C9A84C', 'selectors' => [ '{{WRAPPER}} .ta-faq__icon' => 'color: {{VALUE}};' ] ] );

		$this->add_control( 'border_heading', [ 'label' => esc_html__( 'Bordure', 'tools-adapter' ), 'type' => Controls_Manager::HEADING, 'separator' => 'before' ] );
		$this->add_group_control( Group_Control_Border::get_type(), [ 'name' => 'item_border', 'selector' => '{{WRAPPER}} .ta-faq__item' ] );
		$this->add_control( 'item_radius', [ 'label' => esc_html__( 'Arrondi', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'range' => [ 'px' => [ 'min' => 0, 'max' => 40 ] ], 'selectors' => [ '{{WRAPPER}} .ta-faq__item' => 'border-radius: {{SIZE}}{{UNIT}}; overflow: hidden;' ] ] );

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$faqs     = $settings['faqs'] ?? [];
		if ( empty( $faqs ) ) {
			return;
		}

		if ( 'yes' === ( $settings['enable_schema'] ?? '' ) ) {
			$schema_items = [];
			foreach ( $faqs as $faq ) {
				if ( empty( $faq['question'] ) || empty( $faq['answer'] ) ) {
					continue;
				}
				$schema_items[] = [
					'@type'          => 'Question',
					'name'           => wp_strip_all_tags( $faq['question'] ),
					'acceptedAnswer' => [
						'@type' => 'Answer',
						'text'  => wp_strip_all_tags( $faq['answer'] ),
					],
				];
			}
			if ( $schema_items ) {
				echo '<script type="application/ld+json">' . wp_json_encode(
					[
						'@context'   => 'https://schema.org',
						'@type'      => 'FAQPage',
						'mainEntity' => $schema_items,
					]
				) . '</script>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
		}
		?>
		<div class="ta-faq" data-ta-faq data-multiple="<?php echo 'yes' === ( $settings['allow_multiple'] ?? '' ) ? '1' : '0'; ?>">
			<?php foreach ( $faqs as $index => $faq ) :
				$is_open   = 'yes' === ( $faq['open_default'] ?? '' );
				$unique_id = $this->get_id() . '-' . $index;
				?>
				<div class="ta-faq__item<?php echo $is_open ? ' is-open' : ''; ?>" data-faq-item>
					<button
						type="button"
						class="ta-faq__question"
						data-faq-toggle
						id="<?php echo esc_attr( 'ta-faq-toggle-' . $unique_id ); ?>"
						aria-expanded="<?php echo $is_open ? 'true' : 'false'; ?>"
						aria-controls="<?php echo esc_attr( 'ta-faq-body-' . $unique_id ); ?>"
					>
						<span><?php echo esc_html( \tools_adapter_translate( $faq['question'] ?? '' ) ); ?></span>
						<span class="ta-faq__icon">
							<span class="ta-faq__icon-closed"><?php \Elementor\Icons_Manager::render_icon( $settings['toggle_icon'], [ 'aria-hidden' => 'true' ] ); ?></span>
							<span class="ta-faq__icon-open"><?php \Elementor\Icons_Manager::render_icon( $settings['toggle_icon_active'], [ 'aria-hidden' => 'true' ] ); ?></span>
						</span>
					</button>
					<div
						class="ta-faq__answer"
						data-faq-body
						id="<?php echo esc_attr( 'ta-faq-body-' . $unique_id ); ?>"
						role="region"
						aria-labelledby="<?php echo esc_attr( 'ta-faq-toggle-' . $unique_id ); ?>"
						<?php echo $is_open ? '' : 'style="max-height:0;"'; ?>
					>
						<div class="ta-faq__answer-inner">
							<?php echo wp_kses_post( wpautop( \tools_adapter_translate( $faq['answer'] ?? '' ) ) ); ?>
						</div>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
		<?php
	}
}
