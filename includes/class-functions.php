<?php
if ( ! defined( 'ABSPATH' ) ) {
	die;
} // Cannot access pages directly.

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
		add_filter( 'mage_wc_products', array( $this, 'add_cpt_to_wc_product' ), 10, 1 );

	}

	private function add_hooks() {
		add_action( 'plugins_loaded', array( $this, 'load_plugin_textdomain' ) );
	}

	public function load_plugin_textdomain() {
		load_plugin_textdomain(
			'mage-plugin',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);
	}

	// Adding Custom Post to WC Prodct Data Filter.
	public function add_cpt_to_wc_product( $data ) {
		$mage_cpt = array( 'mage_hotel' );

		return array_merge( $data, $mage_cpt );
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
		$qty  = 0;
		foreach ( $loop->posts as $key => $value ) {
			$room     = 'whhbm_hotel_room_qty_' . $room_id;
			$room_qty = get_post_meta( $value->ID, $room, true );
			$qty      = $qty + (int) $room_qty;
		}

		//write_log($qty);
		return $qty;
	}

	public function get_room_price( $room_id ) {
		$global_price      = get_post_meta( $room_id, 'global_price', true );
		$seasonal_price_un = maybe_unserialize( get_post_meta( $room_id, 'seasonal_price', true ) );
		//write_log($seasonal_price_un);
		$dateWise_price_un = maybe_unserialize( get_post_meta( $room_id, 'dateWise_price', true ) );
		$sunday_price      = get_post_meta( $room_id, 'sunday', true );
		$monday_price      = get_post_meta( $room_id, 'monday', true );
		$tuesday_price     = get_post_meta( $room_id, 'tuesday', true );
		$wednesday_price   = get_post_meta( $room_id, 'wednesday', true );
		$thursday_price    = get_post_meta( $room_id, 'thursday', true );
		$friday_price      = get_post_meta( $room_id, 'friday', true );
		$saturday_price    = get_post_meta( $room_id, 'saturday', true );
		$today             = date( 'l' );


		if ( $this->datewise_day_name( $dateWise_price_un ) == $today ) {
			$price_value = $this->datewise_price( $dateWise_price_un );
		} elseif ( $this->seasonal_price_date( $seasonal_price_un ) ) {
			$price_value = $this->seasonal_price( $seasonal_price_un );
		} elseif ( isset( $sunday_price ) || isset( $monday_price ) || isset( $tuesday_price ) || isset( $wednesday_price ) || isset( $thursday_price ) || isset( $friday_price ) || isset( $saturday_price ) ) {
			$price_value = $this->day_wise( $sunday_price, $monday_price, $tuesday_price, $wednesday_price,
				$thursday_price, $friday_price, $saturday_price, $room_id );
		} else {
			$price_value = get_post_meta( $room_id, 'global_price', true );
		}

		return $price_value;
	}

	/**
	 * @param $sunday_price
	 * @param $monday_price
	 * @param $tuesday_price
	 * @param $wednesday_price
	 * @param $thursday_price
	 * @param $friday_price
	 * @param $saturday_price
	 * function for day wise(sunday, monday,...........) price calculation
	 * @param $room_id
	 *
	 * @return mixed
	 */
	function day_wise(
		$sunday_price, $monday_price, $tuesday_price, $wednesday_price, $thursday_price,
		$friday_price, $saturday_price, $room_id
	) {
		$today = date( 'l' );
		if ( ! empty( $sunday_price ) && $today == "Sunday" ) {
			$price_value = $sunday_price;
		} elseif ( ! empty( $monday_price && $today == "Monday" ) ) {
			$price_value = $monday_price;
		} elseif ( ! empty( $tuesday_price && $today == "Tuesday" ) ) {
			$price_value = $tuesday_price;
		} elseif ( ! empty( $wednesday_price ) && $today == "Wednesday" ) {
			$price_value = $wednesday_price;
		} elseif ( ! empty( $thursday_price ) && $today == "Thursday" ) {
			$price_value = $thursday_price;
		} elseif ( ! empty( $friday_price ) && $today == "Friday" ) {
			$price_value = $friday_price;
		} elseif ( ! empty( $saturday_price ) && $today == "Saturday" ) {
			$price_value = $saturday_price;
		} else {
			$price_value = get_post_meta( $room_id, 'global_price', true );
		}

		return $price_value;

	}

	/**
	 * @param $seasonal_price_un
	 * this method returns the price by seasonal date
	 *
	 * @return mixed
	 */
	function seasonal_price( $seasonal_price_un ) {
		foreach ( $seasonal_price_un as $date_arr_key => $date_arr_value ) {
			$date_range = seasonal_date_range( $date_arr_value['start_date'],
				$date_arr_value['end_date'] );
			$start_date = $date_arr_value['start_date'];
			$end_date   = $date_arr_value['end_date'];
			$today      = date( 'Y-m-d' );
			//write_log($start_date);
			if ( ! empty( $start_date ) && ! empty( $end_date ) && $start_date <= $today && $end_date >= $today ) {
				return $date_arr_value['number_field'];
			}
		}
	}

	/**
	 * @param $seasonal_price_un
	 *  this method return the day name of seasonal price set by user
	 *
	 * @return bool
	 */
	function seasonal_price_date( $seasonal_price_un ) {
		if ( is_array( $seasonal_price_un ) || is_object( $seasonal_price_un ) ) {
			foreach ( $seasonal_price_un as $date_arr_key => $date_arr_value ) {
				$date_range = seasonal_date_range( $date_arr_value['start_date'],
					$date_arr_value['end_date'] );
				$start_date = $date_arr_value['start_date'];
				$end_date   = $date_arr_value['end_date'];
				$today      = date( 'Y-m-d' );
				//write_log($start_date);
				if ( ! empty( $start_date ) && ! empty( $end_date ) && $start_date <= $today && $end_date >= $today ) {
					return true;
				} else {
					return false;
				}
			}
		}
	}//end of seasonal_price_date method

	/**
	 * @param $dateWise_price_un
	 *  this method returns the price of particular date set by user
	 *
	 * @return mixed
	 */
	function datewise_price( $dateWise_price_un ) {
		if ( is_array( $dateWise_price_un ) && ! empty( $dateWise_price_un ) ) {
			foreach ( $dateWise_price_un as $single_arr_key => $single_arr_value ) {
				$day_name = date( 'l', strtotime( $single_arr_value['date_field'] ) );
				$today    = date( 'l' );

				return $single_arr_value['number_field'];
			}
		}
	}//end of datewise_price method

	/**
	 * @param $dateWise_price_un
	 *  this method return the day name of datewise price
	 *
	 * @return false|string
	 */
	function datewise_day_name( $dateWise_price_un ) {
		if ( is_array( $dateWise_price_un ) && ! empty( $dateWise_price_un ) ) {
			foreach ( $dateWise_price_un as $single_arr_key => $single_arr_value ) {
				$day_name = date( 'l', strtotime( $single_arr_value['date_field'] ) );
				$today    = date( 'l' );

				return $day_name;
			}
		}
	}


}

global $magemain;
$magemain = new Mage_Plugin_Functions();