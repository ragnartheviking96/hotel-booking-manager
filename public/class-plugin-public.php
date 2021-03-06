<?php
if ( ! defined( 'ABSPATH' ) ) {
	die;
} // Cannot access pages directly.

/**
 * @package    Mage_Plugin
 * @subpackage Mage_Plugin/public
 * @author     MagePeople team <magepeopleteam@gmail.com>
 */
class Mage_Plugin_Public {

	private $plugin_name;

	private $version;

	public function __construct() {
		$this->load_public_dependencies();
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_filter( 'single_template', array( $this, 'whbm_register_custom_single_template' ) );
		add_filter( 'template_include', array( $this, 'whbm_register_custom_tax_template' ) );
		add_filter( 'page_template',  array( $this, 'search_page_template' ));
		add_filter( 'page_template',  array( $this, 'hotel_page_template' ));
	}

	private function load_public_dependencies() {
		require_once WHBM_PLUGIN_DIR . 'public/shortcode/shortcode-hello.php';
	}

	public function enqueue_styles() {
		wp_enqueue_style( 'magnific', plugin_dir_url( __FILE__ ) . 'css/magnific.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'bootstrap', plugin_dir_url( __FILE__ ) . 'css/bootstrap.min.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'fotorama', plugin_dir_url( __FILE__ ) . 'css/fotorama.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'jquery-ui', plugin_dir_url( __FILE__ ) . 'css/jquery-ui.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'select2-min', plugin_dir_url( __FILE__ ) . 'css/select2.min.css' );
		wp_enqueue_style( 'daterangepicker', plugin_dir_url( __FILE__ ) . 'css/daterangepicker.min.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'font-awesome', plugin_dir_url( __FILE__ ) . 'css/font-awesome.min.css', array(), $this->version , 'all');
		wp_enqueue_style( 'carousel', plugin_dir_url( __FILE__ ) . 'css/owl.carousel.min.css', array(), $this->version , 'all');
		wp_enqueue_style( 'animate', plugin_dir_url( __FILE__ ) . 'css/animate.min.css', array(), $this->version , 'all');
		wp_enqueue_style( 'fancybox', plugin_dir_url( __FILE__ ) . 'css/jquery.fancybox.css', array(), $this->version , 'all');
		wp_enqueue_style( 'jquery-ui', plugin_dir_url( __FILE__ ) . 'css/jquery-ui.min.css', array(), $this->version , 'all');
		wp_enqueue_style( 'slicknav', plugin_dir_url( __FILE__ ) . 'css/slicknav.min.css', array(), $this->version , 'all');
		wp_enqueue_style( 'responsive', plugin_dir_url( __FILE__ ) . 'css/responsive.css', array(), $this->version , 'all');
		wp_enqueue_style( 'vendor-dashboard', plugin_dir_url( __FILE__ ) . 'css/vendor-dashboard.css', array(),
			$this->version,	'all' );
		wp_enqueue_style( 'whbm-public-css', plugin_dir_url( __FILE__ ) . 'css/style.css', array(), $this->version, 'all' );
	}


	public function enqueue_scripts() {
		wp_enqueue_script( 'fontawesome', plugin_dir_url( __FILE__ ) . 'js/fontawesome.js',
			array(), '5.5', true );
		wp_enqueue_script( 'magnific-js', plugin_dir_url( __FILE__ ) . 'js/jquery.magnific-popup.js', array( 'jquery' ), $this->version, true );
		wp_enqueue_script( 'fotorama-js', plugin_dir_url( __FILE__ ) . 'js/fotorama.js', array(
			'jquery',
			'whbm-public-js'
		), $this->version, true );
		wp_enqueue_script( 'select2-min',plugin_dir_url( __FILE__ ) . 'js/select2.min.js', array( 'jquery' ) );
		wp_enqueue_script( 'jquery-ui-js', plugin_dir_url( __FILE__ ) . 'js/jquery-ui.js', array( 'jquery' ), $this->version, true );
		wp_enqueue_script( 'bootstrap-js', plugin_dir_url( __FILE__ ) . 'js/bootstrap.min.js', array( 'jquery' ), $this->version, true );

//		wp_enqueue_script( 'sticky-js', plugin_dir_url( __FILE__ ) . 'js/jquery.sticky.js', array( 'jquery' ), $this->version, true );
		/*wp_enqueue_script( 'moment', plugin_dir_url( __FILE__ ) . 'js/moment.min.js', array( 'jquery' ),
			$this->version, true );*/

		wp_enqueue_script( 'popper', plugin_dir_url( __FILE__ ) . 'js/popper.min.js', array ( 'jquery' ), 1.1, true);
		wp_enqueue_script( 'bootstrap-js', plugin_dir_url( __FILE__ ) . 'js/bootstrap.min.js', array ( 'jquery'
		));
		wp_enqueue_script( 'carousel', plugin_dir_url( __FILE__ ) . 'js/owl.carousel.min.js', array ( 'jquery'
		), 1.1, true);
		wp_enqueue_script( 'fancybox', plugin_dir_url( __FILE__ ) . 'js/jquery.fancybox.min.js', array ( 'jquery' ), 1.1, true);
		wp_enqueue_script( 'jquery-ui', plugin_dir_url( __FILE__ ) . 'js/jquery-ui.min.js', array ( 'jquery' ),
			1.1, true);
		wp_enqueue_script( 'jquery-nav', plugin_dir_url( __FILE__ ) . 'js/jquery.nav.js', array ( 'jquery' ), 1.1, true);
		wp_enqueue_script( 'aos-js', plugin_dir_url( __FILE__ ) . 'js/aos.js', array ( 'jquery' ), 1.1, true);
		wp_enqueue_script( 'slicknav', plugin_dir_url( __FILE__ ) . 'js/jquery.slicknav.min.js', array ( 'jquery' ), 1.1, true);
		wp_enqueue_script( 'custom', plugin_dir_url( __FILE__ ) . 'js/custom.js', array ( 'jquery' ), 1.1, true);

		wp_enqueue_script( 'daterangepicker-js', plugin_dir_url( __FILE__ ) . 'js/jquery.daterangepicker.js', array( 'jquery', 'moment' ), $this->version, true );
		$results_array = $this->whbm_get_location_meta();
		//$autocomplete_array
		wp_enqueue_script( 'whbm-public-js', plugin_dir_url( __FILE__ ) . 'js/mage-plugin-public.js', array( 'jquery' ), $this->version, true );
		wp_enqueue_script( 'dashboard', plugin_dir_url( __FILE__ ) . 'js/dashboard.js', array( 'jquery' ), $this->version, true );
		//write_log($results_array);
		wp_localize_script( 'whbm-public-js', 'whbm_autocomplete', $results_array );
		wp_localize_script('whbm-public-js', 'whbm_ajax_object', (array) admin_url( 'admin-ajax.php' ) );
	}



	public function search_page_template($page_template){
		if ( is_page( 'whbm-hotel-search-form' ) ) {
			$page_template = WHBM_PLUGIN_DIR . 'public/templates/whbm-search-form.php';
		}
		return $page_template;
	}

	public function hotel_page_template( $page_template )
	{
		if ( is_page( 'whbm-hotel-list' ) ) {
			$page_template = WHBM_PLUGIN_DIR . 'public/templates/whbm-hotel-list.php';
		}
		return $page_template;
	}

	/**
	 * @param $template
	 *
	 * @return string
	 */
	public function whbm_register_custom_single_template( $template ) {
		global $post;
		if ( $post->post_type == "mage_hotel" ) {
			$template_name = 'single-hotel.php';
			$template_path = 'hotel-templates/';
			$default_path  = WHBM_PLUGIN_DIR . 'public/templates/';
			$template      = locate_template( array( $template_path . $template_name ) );
			if ( ! $template ) :
				$template = $default_path . $template_name;
			endif;

			return $template;
		}

		return $template;
	}


	/**
	 * @param $template
	 *
	 * @return string
	 */
	public function whbm_register_custom_tax_template( $template ) {
		if ( is_tax( 'mage_property_type' ) ) {
			$template = WHBM_PLUGIN_DIR . 'public/templates/taxonomy-property-type.php';
		}

		return $template;
	}

	/**
	 * @return array
	 */
	public function whbm_get_location_meta() {
		$posts = get_posts( array(
				'post_type'      => 'mage_hotel',
				'post_status'    => 'publish',
				'posts_per_page' => - 1,
				'fields'         => 'ids'
			)
		);
		$city = array();
		foreach ( $posts as $post ) {
			$city[] = get_post_meta( $post, 'city', true );
		}
		return array_unique($city);

	}
	public function wchbmtm_get_hotel_ids($term_id,$qty){
		$args = array (
			'post_type'         => array( 'mage_hotel' ),
			'posts_per_page'    => $qty,
			'meta_query' => array(
				array(
					'key'       => 'wotm_ticket_status',
					'value'     => 'available',
					'compare'   => '='
				)
			),
			'tax_query'       => array(
				array(
					'taxonomy'  => 'mage_ticket_cat',
					'field'     => 'term_id',
					'terms'     => $term_id
				)
			)
		);
		$loop = new WP_Query($args);
		$tid = array();
		foreach ($loop->posts as $ticket) {
			$tid[] = $ticket->ID;
		}
		return join($tid,',');
	}

}
global $wchbm;
$wchbm = new Mage_Plugin_Public();



