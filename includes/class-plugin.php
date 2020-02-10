<?php
if ( ! defined( 'ABSPATH' ) ) { die; } // Cannot access pages directly.
/**
 * @since      1.0.0
 * @package    whbm_Plugin
 * @subpackage whbm_Plugin/includes
 * @author     whbmPeople team <whbmpeopleteam@gmail.com>
 */

class WHBM_Plugin {


	protected $loader;

	protected $plugin_name;

	protected $version;

	public function __construct() {
		$this->load_dependencies();
	}

	private function load_dependencies() {
		require_once WHBM_PLUGIN_DIR . 'lib/classes/class-wc-product-data.php';
		require_once WHBM_PLUGIN_DIR . 'lib/classes/class-form-fields-generator.php';
		require_once WHBM_PLUGIN_DIR . 'lib/classes/class-form-fields-wrapper.php';
		require_once WHBM_PLUGIN_DIR . 'lib/classes/class-meta-box.php';
		require_once WHBM_PLUGIN_DIR . 'lib/classes/class-taxonomy-edit.php';
		require_once WHBM_PLUGIN_DIR . 'lib/classes/class-theme-page.php';
		require_once WHBM_PLUGIN_DIR . 'lib/classes/class-menu-page.php';
		require_once WHBM_PLUGIN_DIR . 'includes/class-plugin-loader.php';
		require_once WHBM_PLUGIN_DIR . 'includes/class-functions.php';
		require_once WHBM_PLUGIN_DIR . 'includes/class-add-cart-data.php';
		require_once WHBM_PLUGIN_DIR . 'admin/class-plugin-admin.php';
		require_once WHBM_PLUGIN_DIR . 'public/class-plugin-public.php';
		$this->loader = new WHBM_Plugin_Loader();
	}



	public function run() {
		$this->loader->run();
	}

	public function get_plugin_name() {
		return $this->plugin_name;
	}

	public function get_loader() {
		return $this->loader;
	}

	public function get_version() {
		return $this->version;
	}

}
