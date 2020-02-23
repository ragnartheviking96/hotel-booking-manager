<?php

class WcHBMCartCalculation {

	public function __construct() {
		$this->add_hooks();
	}

	public function add_hooks() {
		add_filter( 'woocommerce_add_cart_item_data', array( $this, 'prepare_cart_data' ), 10, 3 );
		add_action( 'woocommerce_before_calculate_totals', array( $this, 'update_cart_price' ) );
		add_filter( 'woocommerce_get_item_data', array( $this, 'show_data_in_cart_table' ), 10, 2 );
		add_action( 'woocommerce_checkout_create_order_line_item', array( $this, 'add_data_into_order_item' ), 10, 4 );
	}

	function get_tour_room_price( $room_id, $room_name, $room_qty ) {
		$room_details = maybe_unserialize( get_post_meta( $room_id, 'global_price', true ) );
		$room_fare    = 0;
		foreach ( $room_details as $key => $val ) {
			if ( $val['room_type'] === $room_name ) {
				$room_fare = $val['room_fare'];
			}
		}

		return ( $room_fare * $room_qty );
	}

	/**
	 * @param $cart_item_data
	 * @param $product_id
	 * @param $variation_id
	 *
	 * @return mixed
	 */
	public function prepare_cart_data( $cart_item_data, $product_id, $variation_id ) {
		global $wchbm;
		$product_id = get_post_meta( $product_id, 'link_whbm_hotel', true ) ? get_post_meta( $product_id, 'link_whbm_hotel', true ) : $product_id;
		if ( get_post_type( $product_id ) == 'mage_hotel' ) {
			$date_format = get_option( 'date_format' );

			$hotel_id = $product_id;

			$hotel_name       = get_the_title( $hotel_id );
			$room_id          = $_POST['room_id'];
			$room_name        = $_POST['room_name'];
			$room_qty         = $_POST['room_qty'];
			$total_day_stay   = $_POST['total_day_stay'];
			$hotel_room_cap   = isset( $_POST['room_cap'] ) ? $_POST['room_cap'] : array();
			$final_price      = $_POST['final_price'];
			$hotel_room_price = $_POST['hotel_room_price'];
			$hotel_type       = $_POST['hotel_q_type'];
			$check_in_out     = isset( $_POST['daterange'] ) ? $_POST['daterange'] : date_i18n( $date_format );
			//write_log($check_in_out);
			$adult_qty            = isset( $_POST['adult_qty'] ) ? strip_tags( $_POST['adult_qty'] ) : '';
			$child_qty            = isset( $_POST['child_qty'] ) ? strip_tags( $_POST['child_qty'] ) : '';
			$check_in_out_explode = explode( 'to', $check_in_out );
			//print_r($check_in_out_explode);
			//$check_in_out_explode = isset( $check_in_out_explode[1] ) ? $check_in_out_explode[1] : null;
			$check_in       = date_i18n( $date_format, strtotime( $check_in_out_explode[0] ) );
			$check_out      = date_i18n( $date_format, strtotime( $check_in_out_explode[1] ) );
			$reg_form_arr   = unserialize( get_post_meta( $product_id, 'hotel_attendee_reg_form', true ) );
			$hotel_reg_user = isset( $_POST['hotel_reg_field'] ) ? $_POST['hotel_reg_field'] : array();


			$total_room = count( $room_id );

			for ( $i = 0; $i < $total_room; $i ++ ) {
				$hotel_room_id        = $room_id[ $i ];
				$hotel_room_name      = $room_name[ $i ];
				$hotel_room_qty       = $room_qty[ $i ];
				$hotel_room_prices    = $hotel_room_price[ $i ];
				$hotel_q_type         = $hotel_type[ $i ];
				$total_hotel_day_stay = $total_day_stay;
				if ( $hotel_room_qty > 0 ) {
					$room[ $i ]['hotel_room_id']   = stripslashes( strip_tags( $hotel_room_id ) );
					$room[ $i ]['hotel_room_name'] = stripslashes( strip_tags( $hotel_room_name ) );
					$room[ $i ]['hotel_room_qty']  = stripslashes( strip_tags( $hotel_room_qty ) );
					//$room[ $i ]['hotel_quality_type']       = stripslashes( strip_tags( $hotel_q_type ) );
					$room[ $i ]['hotel_room_price']     = stripslashes( strip_tags( $hotel_room_prices ) );
					$room[ $i ]['hotel_room_net_price'] = stripslashes( strip_tags( $hotel_room_prices *
					                                                                $hotel_room_qty * $total_hotel_day_stay ) );
				}
			}
			for ( $i = 0; $i < count( $hotel_reg_user ); $i ++ ) {
				$user_single[ $i ]['hotel_id']    = stripslashes( strip_tags( $product_id ) );
				$user_single[ $i ]['hotel_type']  = $hotel_type;
				$user_single[ $i ]['room_info']   = $room;
				$user_single[ $i ]['checkin']     = $check_in;
				$user_single[ $i ]['checkout']    = $check_out;
				$user_single[ $i ]['total_stays'] = $total_day_stay;

				if ( is_array( $reg_form_arr ) || is_object( $reg_form_arr ) ) {
					foreach ( $reg_form_arr as $reg_form ) {
						$user_single[ $i ]['guest_'][ $reg_form['field_id'] ] = stripslashes( strip_tags( $_POST[ $reg_form['field_id'] ][ $i ] ) );
					}
				}
			}

			$cart_item_data['hotel_name'] = $hotel_name;
			//$cart_item_data['hotel_type']            = $hotel_type;
			$cart_item_data['hotel_room_info']       = $room;
			$cart_item_data['hotel_price']           = $final_price;
			$cart_item_data['line_total']            = $final_price;
			$cart_item_data['line_subtotal']         = $final_price;
			$cart_item_data['hotel_id']              = $product_id;
			$cart_item_data['hotel_attendee_single'] = $user_single;

			/*echo '<pre>';
			print_r($cart_item_data);
			die();*/

			return $cart_item_data;
		}
		$cart_item_data['hotel_id'] = $product_id;

		return $cart_item_data;
	}

	/**
	 * @param $cart_object
	 */
	public function update_cart_price( $cart_object ) {
		foreach ( $cart_object->cart_contents as $key => $value ) {
			$hotel_id = $value['hotel_id'];
			if ( get_post_type( $hotel_id ) == 'mage_hotel' ) {
				$cp = $value['hotel_price'];
				$value['data']->set_price( $cp );
				$value['data']->set_regular_price( $cp );
				$value['data']->set_sale_price( $cp );
				$value['data']->set_sold_individually( 'yes' );
				$new_price = $value['data']->get_price();
			}
		}
	}

	/**
	 * @param $item_data
	 * @param $cart_item
	 *
	 * @return mixed
	 */
	public function show_data_in_cart_table( $item_data, $cart_item ) {
		$hotel_id = isset( $cart_item['hotel_id'] ) ? $cart_item['hotel_id'] : array();
		if ( get_post_type( $hotel_id ) == 'mage_hotel' ) {
			$date_format           = get_option( 'date_format' );
			$reg_form_arr          = unserialize( get_post_meta( $hotel_id, 'hotel_attendee_reg_form', true ) );
			$hotel_room_info       = $cart_item['hotel_room_info'];
			$hotel_attendee_single = $cart_item['hotel_attendee_single'];
			/*echo "<pre>";
			print_r($hotel_attendee_single);
			die();*/
			echo '<ul>';
			echo '<li><strong>Hotel Name:</strong> ' . $cart_item['hotel_name'] . '</li>';
			//write_log( $cart_item['hotel_type'] );

			if ( is_array( $hotel_room_info ) ) {
				foreach ( $hotel_room_info as $room ) {
					echo '<ul>';
					echo '<li><strong> Room Name:</strong> ' . $room['hotel_room_name'] . '</li>';
					echo '<li><strong> Room Qty:</strong> ' . $room['hotel_room_qty'] . '</li>';
					echo '<li><strong> Room Price:</strong> ' . wc_price( $room['hotel_room_price'] ) . ' X ' . $room['hotel_room_qty'] . ' = ' .
					     wc_price( $room['hotel_room_price'] * $room['hotel_room_qty'] ) . '</li>';
					echo '</ul>';
				}
			}
			echo '<li> <b>Checkin Date:</b> ' . date_i18n( 'D, d M Y', strtotime( $hotel_attendee_single[0]['checkin'] ) ) . '</li>';
			echo '<li> <b>Checkout Date:</b> ' . date_i18n( 'D, d M Y', strtotime( $hotel_attendee_single[0]['checkout'] ) ) . '</li>';
			echo '</ul>';
			echo '<ul>';

			$net_price          = 0;
			$day_wise_net_price = 0;
			foreach ( $hotel_room_info as $room ) {
				$hotel_room_qty     = $room['hotel_room_qty'];
				$hotel_room_price   = $room['hotel_room_price'];
				$net_price          = $hotel_room_qty * $hotel_room_price;
				$day_wise_net_price += $net_price;
			}

			echo '<li> <b>Total Price With Days you are staying:</b> ' . $hotel_attendee_single[0]['total_stays'] . 'X' . wc_price( $day_wise_net_price ) . ' = ' . wc_price( $day_wise_net_price * $hotel_attendee_single[0]['total_stays'] ) . '</li>';
			echo '</ul>';
			if ( is_array( $reg_form_arr ) && sizeof( $reg_form_arr ) > 0 ) {
				$guest_info = $hotel_attendee_single[0]['guest_'];
				echo '<h5>Guest Info:</h5>';
				foreach ( $reg_form_arr as $reg_form ) {
					echo '<li><b>' . $reg_form['field_label'] . ':</b> ' . $guest_info[ $reg_form['field_id'] ];
				}
			}
		}

		return $item_data;
	}

	/**
	 * @param $item
	 * @param $cart_item_key
	 * @param $values
	 * @param $order
	 */
	public function add_data_into_order_item( $item, $cart_item_key, $values, $order ) {
		$hotel_id = $values['hotel_id'];

		if ( get_post_type( $hotel_id ) == 'mage_hotel' ) {
			$date_format = get_option( 'date_format' );

			$hotel_room_info       = $values['hotel_room_info'];
			$hotel_attendee_single = $values['hotel_attendee_single'];
			//$hotel_attendee_single = $values['hotel_attendee_single'];

			$item->add_meta_data( 'Hotel Name', $values['hotel_name'] );
			//$item->add_meta_data( 'Hotel Quality', $values['hotel_type'] );


			foreach ( $hotel_room_info as $room ) {
				$item->add_meta_data( 'Room Name', $room['hotel_room_name'] );
				$item->add_meta_data( 'Room Qty', $room['hotel_room_qty'] );
				$item->add_meta_data( 'Room Price', wc_price( $room['hotel_room_price'] ) . ' X ' . $room['hotel_room_qty'] . ' = ' . wc_price( $room['hotel_room_price'] * $room['hotel_room_qty'] ) );
			}

			$item->add_meta_data( '_room_info', $hotel_room_info );
			// $item->add_meta_data('_no_of_ticket',count($ticket_data));
			$item->add_meta_data( '_hotel_id', $hotel_id );
			$item->add_meta_data( '_hotel_attendee_single_info', $hotel_attendee_single );
			$item->add_meta_data( '_hotel_order_total', $values['hotel_price'] );
		}
	}
}

new WcHBMCartCalculation();