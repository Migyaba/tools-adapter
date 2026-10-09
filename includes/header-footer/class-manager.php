<?php
namespace ToolsAdapter\HeaderFooter;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once __DIR__ . '/class-post-type.php';
require_once __DIR__ . '/class-render.php';

/**
 * Main coordinator for Header & Footer Builder module.
 */
class Manager {

	/**
	 * @var Manager|null
	 */
	private static $instance = null;

	/**
	 * @var Post_Type
	 */
	public $post_type;

	/**
	 * @var Render
	 */
	public $render;

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		$this->post_type = new Post_Type();
		$this->render    = new Render();
	}
}
