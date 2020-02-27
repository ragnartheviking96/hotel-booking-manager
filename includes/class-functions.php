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
	public function get_room_price($room_id){
		$global_price      = get_post_meta( $room_id, 'global_price' );
		$seasonal_price_un = get_post_meta( $room_id, 'seasonal_price' );
		$dateWise_price_un = get_post_meta( $room_id, 'dateWise_price' );
		$sunday_price      = get_post_meta( $room_id, 'sunday' );
		$monday_price      = get_post_meta( $room_id, 'monday' );
		$tuesday_price     = get_post_meta( $room_id, 'tuesday' );
		$wednesday_price   = get_post_meta( $room_id, 'wednesday' );
		$thursday_price    = get_post_meta( $room_id, 'thursday' );
		$friday_price      = get_post_meta( $room_id, 'friday' );
		$saturday_price    = get_post_meta( $room_id, 'saturday' );

		$sunday_has_price    = current( $sunday_price );
		$monday_has_price    = current( $monday_price );
		$tuesday_has_price   = current( $tuesday_price );
		$wednesday_has_price = current( $wednesday_price );
		$thursday_has_price  = current( $thursday_price );
		$friday_has_price    = current( $friday_price );
		$saturday_has_price  = current( $saturday_price );
		$by_week             = array_merge( $sunday_price, $monday_price, $tuesday_price, $wednesday_price, $thursday_price, $friday_price, $saturday_price );


		$dateWise_price_serialized = array();

		foreach ( $global_price as $price_arr_key => $price_value ) {
			$ar                 = $dateWise_price_un;
			$cur                = current( $ar );
			$has_seasonal_price = current( $seasonal_price_un );
			if ( is_array( $dateWise_price_un ) && ! empty( $cur ) ) {
				foreach ( $dateWise_price_un as $key => $dateWise_value ) {
					$dateWise_price_serialized = maybe_unserialize( $dateWise_value );
					if ( is_array( $dateWise_price_serialized ) ) {
						foreach ( $dateWise_price_serialized as $single_arr_key => $single_arr_value ) {
							$day_name = date( 'l', strtotime( $single_arr_value['date_field'] ) );
							$today    = date( 'l' );
							if ( isset( $single_arr_value['date_field'] ) && $day_name == $today ) {
								$price_value = $single_arr_value['number_field'];
							}
						}

					}
				}
			} elseif ( is_array( $seasonal_price_un ) && ! empty( $has_seasonal_price ) ) {
				foreach ( $seasonal_price_un as $seasonal_price_key => $seasonal_price_value ) {
					$seasonal_price_serialized = maybe_unserialize( $seasonal_price_value );
					if ( is_array( $seasonal_price_serialized ) ) {
						foreach ( $seasonal_price_serialized as $date_arr_key => $date_arr_value ) {
							$date_range = seasonal_date_range( $date_arr_value['start_date'],
								$date_arr_value['end_date'] );
							$start_date = $date_arr_value['start_date'];
							$end_date   = $date_arr_value['end_date'];
							$today      = date( 'Y-m-d' );
							if ( ! empty( $start_date ) && ! empty( $end_date ) &&
							     $start_date <= $today &&
							     $end_date >= $today ) {
								$price_value = $date_arr_value['number_field'];
							}
						}
					}
				}
			} elseif ( is_array( $sunday_price ) || is_array( $monday_price ) || is_array( $tuesday_price )
			           || is_array( $wednesday_price ) || is_array( $thursday_price ) || is_array
			           ( $friday_price ) || is_array( $saturday_price ) ) {
				$today = date( 'l' );
				if ( ! empty( $sunday_has_price ) && $today == "Sunday" ) {
					$price_value = $sunday_has_price;
				} elseif ( ! empty( $monday_has_price && $today == "Monday" ) ) {
					$price_value = $monday_has_price;
				} elseif ( ! empty( $tuesday_has_price && $today == "Tuesday" ) ) {
					$price_value = $tuesday_has_price;
				} elseif ( ! empty( $wednesday_has_price ) && $today == "Wednesday" ) {
					$price_value = $wednesday_has_price;
				} elseif ( ! empty( $thursday_has_price ) && $today == "Thursday" ) {
					$price_value = $thursday_has_price;
				} elseif ( ! empty( $friday_has_price ) && $today == "Friday" ) {
					$price_value = $friday_has_price;
				} elseif ( ! empty( $saturday_has_price ) && $today == "Saturday" ) {
					$price_value = $saturday_has_price;
				}

			}

		}
		return $price_value;
	}



}
global $magemain;
$magemain = new Mage_Plugin_Functions();