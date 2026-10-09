<?php
namespace ToolsAdapter\Widgets;

use Elementor\Controls_Manager;
use ToolsAdapter\Base_Widget;
use ToolsAdapter\Repeater;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Widget: Guide des tailles — bouton ouvrant une modale avec un tableau de tailles.
 */
class Size_Guide extends Base_Widget {

	public function get_name() {
		return 'tools-adapter-size-guide';
	}

	public function get_title() {
		return esc_html__( 'Guide des tailles', 'tools-adapter' );
	}

	public function get_icon() {
		return 'eicon-table-of-contents';
	}

	public function get_categories() {
		return [ 'tools-adapter', 'woocommerce-elements' ];
	}

	public function get_keywords() {
		return [ 'tailles', 'size guide', 'mensurations', 'tableau' ];
	}

	public function get_style_depends() {
		return [ 'tools-adapter-size-guide', 'tools-adapter-modal' ];
	}

	public function get_script_depends() {
		return [ 'tools-adapter-modal' ];
	}

	protected function register_controls() {
		$this->start_controls_section( 'section_content', [ 'label' => esc_html__( 'Bouton', 'tools-adapter' ) ] );

		$this->add_control( 'button_text', [ 'label' => esc_html__( 'Texte du bouton', 'tools-adapter' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'Guide des tailles', 'tools-adapter' ) ] );
		$this->add_control( 'button_icon', [ 'label' => esc_html__( 'Icône', 'tools-adapter' ), 'type' => Controls_Manager::ICONS, 'default' => [ 'value' => 'fas fa-ruler', 'library' => 'fa-solid' ] ] );
		$this->add_control( 'modal_title', [ 'label' => esc_html__( 'Titre de la fenêtre', 'tools-adapter' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'Guide des tailles', 'tools-adapter' ) ] );

		$this->end_controls_section();

		$this->start_controls_section( 'section_table', [ 'label' => esc_html__( 'Tableau des tailles', 'tools-adapter' ) ] );

		$this->add_control(
			'unit',
			[
				'label'   => esc_html__( 'Unité', 'tools-adapter' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'cm',
				'options' => [ 'cm' => 'cm', 'in' => 'in (pouces)' ],
			]
		);

		$repeater = new Repeater();
		$repeater->add_control( 'size', [ 'label' => esc_html__( 'Taille', 'tools-adapter' ), 'type' => Controls_Manager::TEXT, 'default' => 'M' ] );
		$repeater->add_control( 'chest', [ 'label' => esc_html__( 'Poitrine', 'tools-adapter' ), 'type' => Controls_Manager::TEXT, 'default' => '' ] );
		$repeater->add_control( 'waist', [ 'label' => esc_html__( 'Taille (tour)', 'tools-adapter' ), 'type' => Controls_Manager::TEXT, 'default' => '' ] );
		$repeater->add_control( 'hips', [ 'label' => esc_html__( 'Hanches', 'tools-adapter' ), 'type' => Controls_Manager::TEXT, 'default' => '' ] );
		$repeater->add_control( 'length', [ 'label' => esc_html__( 'Longueur', 'tools-adapter' ), 'type' => Controls_Manager::TEXT, 'default' => '' ] );

		$this->add_control(
			'rows',
			[
				'label'       => esc_html__( 'Lignes', 'tools-adapter' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => [
					[ 'size' => 'S', 'chest' => '86-91', 'waist' => '71-76', 'hips' => '86-91', 'length' => '68' ],
					[ 'size' => 'M', 'chest' => '92-97', 'waist' => '77-82', 'hips' => '92-97', 'length' => '70' ],
					[ 'size' => 'L', 'chest' => '98-103', 'waist' => '83-88', 'hips' => '98-103', 'length' => '72' ],
					[ 'size' => 'XL', 'chest' => '104-109', 'waist' => '89-94', 'hips' => '104-109', 'length' => '74' ],
				],
				'title_field' => '{{{ size }}}',
			]
		);

		$this->add_control( 'notes', [ 'label' => esc_html__( 'Notes complémentaires', 'tools-adapter' ), 'type' => Controls_Manager::WYSIWYG, 'default' => '' ] );

		$this->end_controls_section();

		$this->start_controls_section( 'section_style_button', [ 'label' => esc_html__( 'Bouton', 'tools-adapter' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_control( 'btn_color', [ 'label' => esc_html__( 'Texte', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#1c1c1c', 'selectors' => [ '{{WRAPPER}} .ta-size-guide__btn' => 'color: {{VALUE}};' ] ] );
		$this->add_control( 'btn_underline', [ 'label' => esc_html__( 'Souligné', 'tools-adapter' ), 'type' => Controls_Manager::SWITCHER, 'return_value' => 'yes', 'default' => 'yes', 'selectors_dictionary' => [ 'yes' => 'underline' ], 'selectors' => [ '{{WRAPPER}} .ta-size-guide__btn' => 'text-decoration: {{VALUE}};' ] ] );

		$this->end_controls_section();

		$this->start_controls_section( 'section_style_table', [ 'label' => esc_html__( 'Tableau', 'tools-adapter' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_control( 'header_bg', [ 'label' => esc_html__( 'Fond en-tête', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#1c1c1c', 'selectors' => [ '{{WRAPPER}} .ta-size-guide__table th' => 'background-color: {{VALUE}};' ] ] );
		$this->add_control( 'header_color', [ 'label' => esc_html__( 'Texte en-tête', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#ffffff', 'selectors' => [ '{{WRAPPER}} .ta-size-guide__table th' => 'color: {{VALUE}};' ] ] );
		$this->add_control( 'row_bg_alt', [ 'label' => esc_html__( 'Fond ligne alternée', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#f7f7f7', 'selectors' => [ '{{WRAPPER}} .ta-size-guide__table tr:nth-child(even)' => 'background-color: {{VALUE}};' ] ] );
		$this->add_control( 'cell_color', [ 'label' => esc_html__( 'Texte cellules', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#333333', 'selectors' => [ '{{WRAPPER}} .ta-size-guide__table td' => 'color: {{VALUE}};' ] ] );

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$rows     = $settings['rows'] ?? [];
		$unit     = $settings['unit'] ?? 'cm';
		$modal_id = 'ta-size-guide-' . $this->get_id();
		?>
		<button type="button" class="ta-size-guide__btn" data-ta-modal-open="<?php echo esc_attr( $modal_id ); ?>">
			<?php if ( ! empty( $settings['button_icon']['value'] ) ) : ?>
				<?php \Elementor\Icons_Manager::render_icon( $settings['button_icon'], [ 'aria-hidden' => 'true' ] ); ?>
			<?php endif; ?>
			<span><?php echo esc_html( \tools_adapter_translate( $settings['button_text'] ?? '' ) ); ?></span>
		</button>

		<div class="ta-modal" data-ta-modal="<?php echo esc_attr( $modal_id ); ?>" aria-hidden="true">
			<div class="ta-modal__backdrop"></div>
			<div class="ta-modal__dialog" role="dialog" aria-modal="true">
				<button type="button" class="ta-modal__close" data-ta-modal-close aria-label="<?php echo esc_attr__( 'Fermer', 'tools-adapter' ); ?>">&times;</button>
				<p class="ta-modal__title"><?php echo esc_html( \tools_adapter_translate( $settings['modal_title'] ?? '' ) ); ?></p>
				<div class="ta-modal__body">
					<?php if ( ! empty( $rows ) ) : ?>
						<table class="ta-size-guide__table">
							<thead>
								<tr>
									<th><?php echo esc_html__( 'Taille', 'tools-adapter' ); ?></th>
									<th><?php echo esc_html__( 'Poitrine', 'tools-adapter' ); ?> (<?php echo esc_html( $unit ); ?>)</th>
									<th><?php echo esc_html__( 'Tour de taille', 'tools-adapter' ); ?> (<?php echo esc_html( $unit ); ?>)</th>
									<th><?php echo esc_html__( 'Hanches', 'tools-adapter' ); ?> (<?php echo esc_html( $unit ); ?>)</th>
									<th><?php echo esc_html__( 'Longueur', 'tools-adapter' ); ?> (<?php echo esc_html( $unit ); ?>)</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ( $rows as $row ) : ?>
									<tr>
										<td><strong><?php echo esc_html( $row['size'] ?? '' ); ?></strong></td>
										<td><?php echo esc_html( $row['chest'] ?? '' ); ?></td>
										<td><?php echo esc_html( $row['waist'] ?? '' ); ?></td>
										<td><?php echo esc_html( $row['hips'] ?? '' ); ?></td>
										<td><?php echo esc_html( $row['length'] ?? '' ); ?></td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					<?php endif; ?>

					<?php if ( ! empty( $settings['notes'] ) ) : ?>
						<div class="ta-size-guide__notes"><?php echo wp_kses_post( $settings['notes'] ); ?></div>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<?php
	}
}
