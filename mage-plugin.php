<?php
/**
 * Plugin Name:       WooCommerce Hotel Booking Manager
 * Plugin URI:        mage-people.com
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.9
 * Author:            magePeople team
 * Author URI:        mage-people.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       whbm
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-whbm-plugin-activator.php
 */
function whbm_activate_whbm_plugin() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-plugin-activator.php';
	// whbm_Plugin_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-whbm-plugin-deactivator.php
 */
function whbm_deactivate_whbm_plugin() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-plugin-deactivator.php';
	// whbm_Plugin_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'whbm_activate_whbm_plugin' );
register_deactivation_hook( __FILE__, 'whbm_deactivate_whbm_plugin' );
// Cretae pages on plugin activation
function whbm_page_create() {

	if ( ! whbm_get_page_by_slug( 'whbm-hotel-search-form' ) ) {
		$hotel_search_page = array(
			'post_type'    => 'page',
			'post_name'    => 'whbm-hotel-search-form',
			'post_title'   => 'Hotel Search',
			'post_content' => '[whbm-hotel-search-form]',
			'post_status'  => 'publish',
		);

		wp_insert_post( $hotel_search_page );
	}
	if ( ! whbm_get_page_by_slug( 'whbm-hotel-list' ) ) {
		$hotel_search_page = array(
			'post_type'    => 'page',
			'post_name'    => 'whbm-hotel-list',
			'post_title'   => 'Hotel List',
			'post_content' => '[whbm-hotel-list]',
			'post_status'  => 'publish',
		);

		wp_insert_post( $hotel_search_page );
	}

	/*if ( ! whbm_get_page_by_slug( 'view-ticket' ) ) {
		$view_ticket_page = array(
			'post_type'    => 'page',
			'post_name'    => 'view-ticket',
			'post_title'   => 'View Ticket',
			'post_content' => '[wbtm-view-ticket]',
			'post_status'  => 'publish',
		);

		wp_insert_post( $view_ticket_page );
	}*/
}

// Function to get page slug
function whbm_get_page_by_slug( $slug ) {
	if ( $pages = get_pages() ) {
		foreach ( $pages as $page ) {
			if ( $slug === $page->post_name ) {
				return $page;
			}
		}
	}

	return false;
}
//register_activation_hook( __FILE__, 'activate_hotel_basic' );
register_activation_hook( __FILE__, 'whbm_page_create' );
//register_activation_hook( __FILE__, 'create_default_terms_rating' );

class whbm_Base {

	public function __construct() {
		$this->define_constants();
		$this->load_main_class();
		$this->run_whbm_plugin();
	}

	public function define_constants() {
		define( 'WHBM_PLUGIN_URL', WP_PLUGIN_URL . '/' . plugin_basename( dirname( __FILE__ ) ) . '/' );
		define( 'WHBM_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
		define( 'WHBM_PLUGIN_FILE', plugin_basename( __FILE__ ) );
		define( 'WHBM_PLUGIN_VERSION', '1.0.0' );
		define( 'WHBM_TEXTDOMAIN', 'whbm' );
	}

	public function load_main_class() {
		require WHBM_PLUGIN_DIR . 'includes/class-plugin.php';
	}

	public function run_whbm_plugin() {
		$plugin = new WHBM_Plugin();
		$plugin->run();

		//new WHBMSettingPage();
	}
}
	function activate_hotel_basic() {
		/**
		 * Check if WooCommerce active
		 * */
		if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
			// Deactivate the plugin
			deactivate_plugins( __FILE__ );
			$error_message = sprintf( 'This plugin requires <a target="_blank" href="%s">%s</a>', 'https://wordpress.org/plugins/woocommerce/', esc_html__( 'Woocommerce Hotel Booking Manager', 'whbm' )
			);
			die( $error_message );
		}
	} // end of activate
	if( !function_exists('get_plugin_data') ){
		require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	}
	/*function WHBM_load_plugin() {
		//$plugin_data = array();

		$plugin_data = get_plugin_data( __FILE__ );

		$plugin_version = $plugin_data['Version'];
		if ( class_exists( 'WooCommerce' ) ) {

		}
	}*/

	//add_action( 'plugins_loaded', 'WHBM_load_plugin' );
	if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
		new whbm_Base();
		//new WHBMSettingPage();
		//echo 'WooCommerce is active.';
	}
//settings_page();