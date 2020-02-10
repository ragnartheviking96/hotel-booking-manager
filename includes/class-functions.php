<?php
if ( ! defined( 'ABSPATH' ) ) { die; } // Cannot access pages directly.
/**
 * @since      1.0.0
 * @package    Mage_Plugin
 * @subpackage Mage_Plugin/includes
 * @author     MagePeople team <magepeopleteam@gmail.com>
 */

class Mage_Plugin_Functions {

	protected $loader;

	protected $plugin_name;

	protected $version;

	public function __construct() {
        $this->add_hooks();
        add_filter('mage_wc_products', array($this, 'add_cpt_to_wc_product'), 10, 1);
	}

	private function add_hooks() {
        add_action( 'plugins_loaded',array($this, 'load_plugin_textdomain' ));
	}

	public function load_plugin_textdomain() {
		load_plugin_textdomain(
			'mage-plugin',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);
	}

    // Adding Custom Post to WC Prodct Data Filter.
    public function add_cpt_to_wc_product($data){
        $mage_cpt = array('mage_hotel');
        return array_merge($data,$mage_cpt);
    }
	public function get_booked_room_count( $checkin_date, $checkout_date, $hotel_id, $room_id ) {

		$args = array(
			'post_type'     => 'hotel_booking_info',
			'post_per_page' => - 1,
			'meta_query'    => array(
				'relation' => 'AND',
				array(
					array(
						'key'     => 'whhbm_hotel_id',
						'value'   => $hotel_id,
						'compare' => '='
					)
				),
				array(
					'relation' => 'OR',
					array(
						'key'     => 'whhbm_order_status',
						'value'   => 'processing',
						'compare' => '='
					),
					array(
						'key'     => 'whhbm_order_status',
						'value'   => 'completed',
						'compare' => '='
					)
				),
				array(
					'relation' => 'OR',
					array(
						'key'     => 'whhbm_checkin_date',
						'value'   => array( $checkin_date, $checkout_date ),
						'compare' => 'BETWEEN',
						'type'    => 'DATE'
					),
					array(
						'key'     => 'whhbm_checkout_date',
						'value'   => array( $checkin_date, $checkout_date ),
						'compare' => 'BETWEEN',
						'type'    => 'DATE'
					)
				)

			)
		);
		$loop = new WP_Query( $args );
		$qty = 0;
		foreach ( $loop->posts as $key => $value ) {
			$room     = 'whhbm_hotel_room_qty_' . $room_id;
			$room_qty = get_post_meta( $value->ID, $room, true );
			$qty      = $qty + (int) $room_qty;
		}
		//write_log($qty);
		return $qty;
	}
}
global $magemain;
$magemain = new Mage_Plugin_Functions();