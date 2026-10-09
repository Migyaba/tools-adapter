<?php
namespace ToolsAdapter\Widgets;

use Elementor\Controls_Manager;
use ToolsAdapter\Base_Widget;
use ToolsAdapter\Admin_Settings;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Widget: Liste de souhaits — affiche les produits mis en favoris par le visiteur.
 */
class Wishlist_List extends Base_Widget {

	public function get_name() {
		return 'tools-adapter-wishlist';
	}

	public function get_title() {
		return esc_html__( 'Liste de souhaits', 'tools-adapter' );
	}

	public function get_icon() {
		return 'eicon-heart-o';
	}

	public function get_categories() {
		return [ 'tools-adapter', 'woocommerce-elements' ];
	}

	public function get_keywords() {
		return [ 'wishlist', 'favoris', 'souhaits', 'coeur', 'liste' ];
	}

	public function get_style_depends() {
		return [ 'tools-adapter-products', 'tools-adapter-wishlist' ];
	}

	public function get_script_depends() {
		return [ 'tools-adapter-wishlist' ];
	}

	protected function register_controls() {
		$this->start_controls_section( 'section_content', [ 'label' => esc_html__( 'Liste de souhaits', 'tools-adapter' ) ] );

		$this->add_control(
			'columns',
			[
				'label'   => esc_html__( 'Colonnes (ordinateur)', 'tools-adapter' ),
				'type'    => Controls_Manager::NUMBER,
				'min'     => 1,
				'max'     => 6,
				'default' => 4,
			]
		);

		$this->add_control(
			'notice',
			[
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => esc_html__( 'La liste est propre à chaque visiteur : elle s\'affiche sur le site, pas dans l\'éditeur. La fonctionnalité « Liste de souhaits » doit être active dans les réglages Tools Adapter.', 'tools-adapter' ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		if ( ! Admin_Settings::is_feature_enabled( 'wishlist' ) || ! class_exists( '\ToolsAdapter\Wishlist' ) ) {
			if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
				echo '<p>' . esc_html__( 'Activez la fonctionnalité « Liste de souhaits » dans les réglages Tools Adapter.', 'tools-adapter' ) . '</p>';
			}
			return;
		}

		echo \ToolsAdapter\Wishlist::list_markup( absint( $this->get_settings_for_display( 'columns' ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in list_markup().
	}
}
