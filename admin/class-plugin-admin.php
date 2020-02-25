<?php
if ( ! defined( 'ABSPATH' ) ) {
	die;
} // Cannot access pages directly.

/**
 * @package    Mage_Plugin
 * @subpackage Mage_Plugin/admin
 * @author     MagePeople team <magepeopleteam@gmail.com>
 */
class WHBM_Plugin_Admin {

	private $plugin_name;

	private $version;

	public function __construct() {
		$this->load_admin_dependencies();
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_filter( 'bulk_actions-edit-post', array( $this, 'register_my_bulk_actions' ) );
		add_action( 'woocommerce_checkout_order_processed', array( $this, 'hotel_attendee_data_create' ) );
		add_action( 'woocommerce_order_status_changed', array( $this, 'hotel_inventory_management' ), 10, 4 );
		add_filter( 'manage_hotel_booking_info_posts_columns', array( $this, 'wchbmpro_add_pdf_dl_column' ) );
		add_action( 'wp_trash_post', array( $this, 'whbm_booking_info_trash' ), 90 );
		add_action( 'untrash_post', array( $this, 'whbm_booking_info_untrash' ), 90 );
		add_action( 'save_post', array( $this, 'whbm_wc_link_product_on_save', 99, 1 ) );
		add_action( 'parse_query', array( $this, 'whbm_product_tags_sorting_query' ) );
	}
	public function enqueue_styles() {
		wp_enqueue_style( 'whbm-jquery-ui-style', WHBM_PLUGIN_URL . 'admin/css/jquery-ui.css', array() );
		wp_enqueue_style( 'pickplugins-options-framework', WHBM_PLUGIN_URL . 'admin/assets/css/pickplugins-options-framework.css' );
		wp_enqueue_style( 'jquery-ui', WHBM_PLUGIN_URL . 'admin/assets/css/jquery-ui.css' );
		wp_enqueue_style( 'select2.min', WHBM_PLUGIN_URL . 'admin/assets/css/select2.min.css' );
		wp_enqueue_style( 'codemirror', WHBM_PLUGIN_URL . 'admin/assets/css/codemirror.css' );
		wp_enqueue_style( 'fontawesome', WHBM_PLUGIN_URL . 'admin/assets/css/fontawesome.min.css' );
		wp_enqueue_style( 'mage-admin-css', WHBM_PLUGIN_URL . 'admin/css/mage-plugin-admin.css', array(), time(), 'all' );
	}

	public function enqueue_scripts() {
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'jquery-ui-core' );
		wp_enqueue_script( 'jquery-ui-datepicker' );
		wp_enqueue_script( 'jquery-ui-sortable' );
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'wp-color-picker' );
		wp_enqueue_script( 'jquery-ui-js', WHBM_PRO_PLUGIN_URL . 'admin/js/jquery-ui.js', array( 'jquery' ), $this->version );
		wp_enqueue_script( 'magepeople-options-framework', plugins_url( 'assets/js/pickplugins-options-framework.js', __FILE__ ), array( 'jquery' ) );
		wp_localize_script( 'PickpluginsOptionsFramework', 'PickpluginsOptionsFramework_ajax', array( 'PickpluginsOptionsFramework_ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
		wp_enqueue_script( 'select2.min', plugins_url( 'assets/js/select2.min.js', __FILE__ ), array( 'jquery' ) );
		wp_enqueue_script( 'codemirror', WHBM_PLUGIN_URL . 'admin/assets/js/codemirror.min.js', array( 'jquery' ), null, false );
		wp_enqueue_script( 'form-field-dependency', plugins_url( 'assets/js/form-field-dependency.js', __FILE__ ), array( 'jquery' ), null, false );
		wp_enqueue_script( 'mage-plugin-js', WHBM_PLUGIN_URL . 'admin/js/mage-plugin-admin.js', array(
			'jquery',
			'jquery-ui-core',
			'jquery-ui-datepicker'
		), time(), true );
	}

	private function load_admin_dependencies() {
		require_once WHBM_PLUGIN_DIR . 'admin/class/class-create-cpt.php';
		require_once WHBM_PLUGIN_DIR . 'admin/class/class-create-tax.php';
		require_once WHBM_PLUGIN_DIR . 'admin/class/class-whbm-meta-box.php';
		require_once WHBM_PLUGIN_DIR . 'admin/class/class-tax-meta.php';
		require_once WHBM_PLUGIN_DIR . 'admin/class/class-setting-page.php';
		require_once WHBM_PLUGIN_DIR . 'admin/class/class-menu-page.php';
	}

	public function register_my_bulk_actions( $bulk_actions ) {
		$bulk_actions['email_to_eric'] = __( 'Email to Eric', 'email_to_eric' );

		return $bulk_actions;
	}

	// Get Order Itemdata value by Item id
	public function whbmpro_get_order_meta( $item_id, $key ) {
		global $wpdb;
		$table_name = $wpdb->prefix . "woocommerce_order_itemmeta";
		$sql        = 'SELECT meta_value FROM ' . $table_name . ' WHERE order_item_id =' . $item_id . ' AND meta_key="' . $key . '"';
		$results = $wpdb->get_results( $sql ) or die( mysql_error() );
		foreach ( $results as $result ) {
			$value = $result->meta_value;
		}

		return $value;
	}

	public function hotel_attendee_data_create( $order_id ) {
		if ( ! $order_id ) {
			return;
		}

		// Getting an instance of the order object
		$order      = wc_get_order( $order_id );
		$order_meta = get_post_meta( $order_id );
		# Iterating through each order items (WC_Order_Item_Product objects in WC 3+)
		foreach ( $order->get_items() as $item_id => $item_values ) {
			$hotel_id = $this->whbmpro_get_order_meta( $item_id, '_hotel_id' );


			if ( get_post_type( $hotel_id ) == 'mage_hotel' ) {

				$first_name      = isset( $order_meta['_billing_first_name'][0] ) ? $order_meta['_billing_first_name'][0] : array();
				$last_name       = isset( $order_meta['_billing_last_name'][0] ) ? $order_meta['_billing_last_name'][0] : array();
				$company_name    = isset( $order_meta['_billing_company'][0] ) ? $order_meta['_billing_company'][0] : array();
				$address_1       = isset( $order_meta['_billing_address_1'][0] ) ? $order_meta['_billing_address_1'][0] : array();
				$address_2       = isset( $order_meta['_billing_address_2'][0] ) ? $order_meta['_billing_address_2'][0] : array();
				$city            = isset( $order_meta['_billing_city'][0] ) ? $order_meta['_billing_city'][0] : array();
				$state           = isset( $order_meta['_billing_state'][0] ) ? $order_meta['_billing_state'][0] : array();
				$postcode        = isset( $order_meta['_billing_postcode'][0] ) ? $order_meta['_billing_postcode'][0] : array();
				$country         = isset( $order_meta['_billing_country'][0] ) ? $order_meta['_billing_country'][0] : array();
				$email           = isset( $order_meta['_billing_email'][0] ) ? $order_meta['_billing_email'][0] : array();
				$phone           = isset( $order_meta['_billing_phone'][0] ) ? $order_meta['_billing_phone'][0] : array();
				$billing_intotal = isset( $order_meta['_billing_address_index'][0] ) ? $order_meta['_billing_address_index'][0] : array();
				$payment_method  = isset( $order_meta['_payment_method_title'][0] ) ? $order_meta['_payment_method_title'][0] : array();
				$user_id         = isset( $order_meta['_customer_user'][0] ) ? $order_meta['_customer_user'][0] : array();
				$address         = $address_1 . ' ' . $address_2;


				$order_total           = $this->whbmpro_get_order_meta( $item_id, '_hotel_order_total' );
				$hotel_attendee_single = maybe_unserialize( $this->whbmpro_get_order_meta( $item_id, '_hotel_attendee_single_info' ) );
				//$hotel_type_un = ;


				if ( is_array( $hotel_attendee_single ) && sizeof( $hotel_attendee_single ) > 0 ) {
					$room_info    = $hotel_attendee_single[0]['room_info'];
					$guest_info   = $hotel_attendee_single[0]['guest_'];
					$order_status = $order->get_status();
					$reg_form_arr = maybe_unserialize( get_post_meta( $hotel_id, 'hotel_attendee_reg_form', true ) );


					$uname    = '#' . get_the_title( $hotel_id );
					$new_post = array(
						'post_title'    => $uname,
						'post_content'  => '',
						'post_category' => array(),
						'tags_input'    => array(),
						'post_status'   => 'publish',
						'post_type'     => 'hotel_booking_info'
					);

					$pid = wp_insert_post( $new_post );
					update_post_meta( $pid, 'whhbm_hotel_id', $hotel_id );
					update_post_meta( $pid, 'whhbm_hotel_type', $hotel_attendee_single[0]['hotel_type'] );
					update_post_meta( $pid, 'whhbm_order_id', $order_id );
					update_post_meta( $pid, 'whhbm_checkin_date', $hotel_attendee_single[0]['checkin'] );
					update_post_meta( $pid, 'whhbm_checkout_date', $hotel_attendee_single[0]['checkout'] );

					foreach ( $room_info as $room ) {
						$room_id = $room['hotel_room_id'];
						update_post_meta( $pid, 'whhbm_hotel_room_id_' . $room_id, $room['hotel_room_id'] );
						update_post_meta( $pid, 'whhbm_hotel_room_name_' . $room_id, $room['hotel_room_name'] );
						update_post_meta( $pid, 'whhbm_hotel_room_price_' . $room_id, $room['hotel_room_price'] );
						update_post_meta( $pid, 'whhbm_hotel_room_net_price_' . $room_id, $room['hotel_room_net_price'] );
						update_post_meta( $pid, 'whhbm_hotel_room_qty_' . $room_id, $room['hotel_room_qty'] );
						//update_post_meta( $pid, 'whhbm_hotel_type', $hotel_id );
					}

					if ( is_array( $reg_form_arr ) && sizeof( $reg_form_arr ) > 0 ) {
						foreach ( $reg_form_arr as $reg_form ) {
							update_post_meta( $pid, $reg_form['field_id'], $guest_info[ $reg_form['field_id'] ] );
						}

					}
					update_post_meta( $pid, 'whhbm_attendee_first_name', $first_name );
					update_post_meta( $pid, 'whhbm_attendee_last_name', $last_name );
					update_post_meta( $pid, 'whhbm_attendee_company', $company_name );
					update_post_meta( $pid, 'whhbm_attendee_address', $address );
					update_post_meta( $pid, 'whhbm_attendee_city', $city );
					update_post_meta( $pid, 'whhbm_attendee_state', $state );
					update_post_meta( $pid, 'whhbm_attendee_postcode', $postcode );
					update_post_meta( $pid, 'whhbm_attendee_country', $country );
					update_post_meta( $pid, 'whhbm_attendee_email', $email );
					update_post_meta( $pid, 'whhbm_attendee_phone', $phone );


					update_post_meta( $pid, 'whhbm_attendee_payment', $payment_method );
					update_post_meta( $pid, 'whhbm_user_id', $user_id );
					update_post_meta( $pid, 'whhbm_order_total', $order_total );
					update_post_meta( $pid, 'whhbm_order_status', $order_status );
				}
			} // Ticket Post Type Check end
		} //Order Item data Loop
	}

	public function hotel_inventory_management( $order_id, $from_status, $to_status, $order ) {
		$order = wc_get_order( $order_id );
		foreach ( $order->get_items() as $item_id => $item_values ) {
			$item_id  = $item_id;
			$hotel_id = $this->whbmpro_get_order_meta( $item_id, '_hotel_id' );
			if ( get_post_type( $hotel_id ) == 'mage_hotel' ) {

				if ( $order->has_status( 'processing' ) ) {
					//$this->change_attandee_order_status($order_id,'publish','trash','sold');
					$this->change_hotel_booking_status( $order_id, 'publish', 'publish', 'processing' );
				} // end of procesing

				if ( $order->has_status( 'pending' ) ) {
					$this->change_hotel_booking_status( $order_id, 'publish', 'publish', 'pending' );
				}
				if ( $order->has_status( 'on-hold' ) ) {
					$this->change_hotel_booking_status( $order_id, 'publish', 'publish', 'on-hold' );
				}
				if ( $order->has_status( 'completed' ) ) {
					$this->change_hotel_booking_status( $order_id, 'publish', 'publish', 'completed' );
				}
				if ( $order->has_status( 'cancelled' ) ) {
					$this->change_hotel_booking_status( $order_id, 'publish', 'publish', 'cancelled' );
				} // end of cancelled/refunded/faild
				if ( $order->has_status( 'refunded' ) ) {
					$this->change_hotel_booking_status( $order_id, 'publish', 'publish', 'refunded' );
				}
				if ( $order->has_status( 'failed' ) ) {
					$this->change_hotel_booking_status( $order_id, 'publish', 'publish', 'failed' );
				}
			} // End of Post Type Check
		} // End order item foreach
	} // End Function

	function whbm_booking_info_trash( $post_id ) {
		$post_type   = get_post_type( $post_id );
		$post_status = get_post_status( $post_id );
		if ( $post_type == 'shop_order' ) {
			$this->change_hotel_booking_status( $post_id, 'trash', 'publish', 'trash' );
		}
	}

	function whbm_booking_info_untrash( $post_id ) {
		$post_type   = get_post_type( $post_id );
		$post_status = get_post_status( $post_id );
		if ( $post_type == 'shop_order' ) {
			$this->change_hotel_booking_status( $post_id, 'publish', 'trash', 'processing' );
		}
	}


	public function change_hotel_booking_status( $order_id, $set_status, $post_status, $booking_status ) {

		$args = array(
			'post_type'      => array( 'hotel_booking_info' ),
			'posts_per_page' => - 1,
			'post_status'    => $post_status,
			'meta_query'     => array(
				array(
					'key'     => 'whhbm_order_id',
					'value'   => $order_id,
					'compare' => '='
				)
			)
		);

		$loop = new WP_Query( $args );
		foreach ( $loop->posts as $ticket ) {
			$post_id      = $ticket->ID;
			$current_post = get_post( $post_id, 'ARRAY_A' );
			update_post_meta( $post_id, 'whhbm_order_status', $booking_status );
			$current_post['post_status'] = $set_status;
			wp_update_post( $current_post );
		}
	}

	function wchbmpro_add_pdf_dl_column( $columns ) {
		unset( $columns['title'] );
		unset( $columns['date'] );

		$columns['title']                   = __( 'Order Title', 'whbm' );
		$columns['wchbmpro_hotel_type']     = __( 'Hotel Type', 'whbm' );
		$columns['wchbmpro_hotel_order_id'] = __( 'Order ID', 'whbm' );
		$columns['wchbmpro_payment_status'] = __( 'Payment Status', 'whbm' );
		$columns['wchbmpro_attendee_info']  = __( 'Attendee Name', 'whbm' );
		//$columns['wchbmpro_hotel_qr']       = __( 'QR', 'whbm' );
		$columns['wchbmpro_dl_pdf'] = __( 'Download', 'whbm' );

		return $columns;
	}

	function whbm_count_hidden_wc_product( $event_id ) {
		$args = array(
			'post_type'      => 'product',
			'posts_per_page' => - 1,
			'meta_query'     => array(
				array(
					'key'     => 'link_whbm_hotel',
					'value'   => $event_id,
					'compare' => '='
				)
			)
		);
		$loop = new WP_Query( $args );
		print_r( $loop->posts );

		return $loop->post_count;
	}


	function whbm_wc_link_product_on_save( $post_id ) {

		if ( get_post_type( $post_id ) == 'mage_hotel' ) {

			if ( ! isset( $_POST['whbm_event_reg_btn_nonce'] ) ||
			     ! wp_verify_nonce( $_POST['whbm_event_reg_btn_nonce'], 'whbm_event_reg_btn_nonce' ) ) {
				return;
			}

			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				return;
			}

			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return;
			}
			$hotel_name = get_the_title( $post_id );

			if ( $this->whbm_count_hidden_wc_product( $post_id ) == 0 || empty( get_post_meta( $post_id, 'link_wc_product',
					true ) ) ) {
				$this->whbm_create_hidden_event_product( $post_id, $hotel_name );
			}

			$product_id = get_post_meta( $post_id, 'link_wc_product', true ) ? get_post_meta( $post_id, 'link_wc_product', true ) : $post_id;
			set_post_thumbnail( $product_id, get_post_thumbnail_id( $post_id ) );
			wp_publish_post( $product_id );


			$_tax_status = isset( $_POST['_tax_status'] ) ? strip_tags( $_POST['_tax_status'] ) : 'none';
			$_tax_class  = isset( $_POST['_tax_class'] ) ? strip_tags( $_POST['_tax_class'] ) : '';

			$update__tax_status = update_post_meta( $product_id, '_tax_status', $_tax_status );
			$update__tax_class  = update_post_meta( $product_id, '_tax_class', $_tax_class );
			$update__tax_class  = update_post_meta( $product_id, '_stock_status', 'instock' );
			$update__tax_class  = update_post_meta( $product_id, '_manage_stock', 'no' );
			$update__tax_class  = update_post_meta( $product_id, '_virtual', 'yes' );
			$update__tax_class  = update_post_meta( $product_id, '_sold_individually', 'yes' );


			// Update post
			$my_post = array(
				'ID'         => $product_id,
				'post_title' => $hotel_name, // new title
			);

			// unhook this function so it doesn't loop infinitely
			remove_action( 'save_post', 'whbm_wc_link_product_on_save' );
			// update the post, which calls save_post again
			wp_update_post( $my_post );
			// re-hook this function
			add_action( 'save_post', 'whbm_wc_link_product_on_save' );
			// Update the post into the database


		}

	}

	function whbm_product_tags_sorting_query( $query ) {
		global $pagenow;
		$taxonomy = 'product_visibility';
		$q_vars = &$query->query_vars;
		if ( $pagenow == 'edit.php' && isset( $q_vars['post_type'] ) && $q_vars['post_type'] == 'product' ) {
			$tax_query = array(
				[
					'taxonomy' => 'product_visibility',
					'field'    => 'slug',
					'terms'    => 'exclude-from-catalog',
					'operator' => 'NOT IN',
				]
			);
			$query->set( 'tax_query', $tax_query );
		}

	}
	function whbm_create_hidden_event_product($post_id,$title){
		$new_post = array(
			'post_title'    =>   $title,
			'post_content'  =>   '',
			'post_category' =>   array(),
			'tags_input'    =>   array(),
			'post_status'   =>   'publish',
			'post_type'     =>   'product'
		);


		$pid                = wp_insert_post($new_post);

		update_post_meta( $post_id, 'link_wc_product', $pid );
		update_post_meta( $pid, 'link_whbm_hotel', $post_id );
		update_post_meta( $pid, '_price', 0.01 );

		update_post_meta( $pid, '_sold_individually', 'yes' );
		update_post_meta( $pid, '_virtual', 'yes' );
		$terms = array( 'exclude-from-catalog', 'exclude-from-search' );
		wp_set_object_terms( $pid, $terms, 'product_visibility' );
		update_post_meta( $post_id, 'check_if_run_once', true );

	}


}

global $whbm_plugin_admin;
$whbm_plugin_admin = new WHBM_Plugin_Admin();

add_action( 'wp_insert_post', 'whbm_on_post_publish', 10, 3 );
function whbm_on_post_publish( $post_id, $post, $update ) {
	if ( $post->post_type == 'mage_hotel' && $post->post_status == 'publish' && empty( get_post_meta( $post_id, 'check_if_run_once' ) ) ) {
		// print_r($post);

		// ADD THE FORM INPUT TO $new_post ARRAY
		$new_post = array(
			'post_title'    => $post->post_title,
			'post_content'  => '',
			'post_category' => array(),  // Usable for custom taxonomies too
			'tags_input'    => array(),
			'post_status'   => 'publish', // Choose: publish, preview, future, draft, etc.
			'post_type'     => 'product'  //'post',page' or use a custom post type if you want to
		);
		//SAVE THE POST
		$pid = wp_insert_post( $new_post );
		update_post_meta( $post_id, 'link_wc_product', $pid );
		update_post_meta( $pid, 'link_whbm_hotel', $post_id );
		update_post_meta( $pid, '_price', 0.01 );
		update_post_meta( $pid, '_sold_individually', 'yes' );
		update_post_meta( $pid, '_virtual', 'yes' );
		$terms = array( 'exclude-from-catalog', 'exclude-from-search' );
		wp_set_object_terms( $pid, $terms, 'product_visibility' );
		update_post_meta( $post_id, 'check_if_run_once', true );
		//die();
	}
}