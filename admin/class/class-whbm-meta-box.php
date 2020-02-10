<?php
/*
* @Author 		magePeople
* Copyright: 	mage-people.com
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}  // if direct access


class MageMetaBox {

	public function __construct() {
		$this->meta_boxs();
		//$this->get_hotel_list();
		add_action( 'save_post', array( $this, 'update_price_of_ticket' ) );
		add_action( 'save_post', array( $this, 'update_room_location' ), 90 );
	}


	function update_all_room_location_of_hotel( $hotel_id, $location ) {
		$args   = array(
			'post_type'      => 'mage-room-pricing',
			'posts_per_page' => - 1,
			'meta_query'     => array(
				array(
					'key'     => 'hotel_list',
					'value'   => $hotel_id,
					'compare' => '='
				)
			)
		);
		$result = new WP_Query( $args );
		foreach ( $result->posts as $room ) {
			update_post_meta( $room->ID, 'hotel_location', $location );
		}
	}


	public function update_price_of_ticket( $post_id ) {
		global $post;
		if ( $post ) {
			if ( $post->post_type != 'mage_hotel' ) {
				return;
			}
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				return;
			}
			$location = $_POST['city'];
			$this->update_all_room_location_of_hotel( $post_id, $location );
			update_post_meta( $post_id, '_price', 0 );
		}
	}

	public function update_room_location( $post_id ) {
		$hotel_id       = get_post_meta( $post_id, 'hotel_list', true );
		$hotel_location = get_post_meta( $hotel_id, 'city', true );
		global $post;
		if ( $post ) {
			if ( $post->post_type != 'mage-room-pricing' ) {
				return;
			}
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				return;
			}
			update_post_meta( $post_id, 'hotel_location', $hotel_location );
		}
	}


	public function meta_boxs() {
		$page_1_options = array(
			'page_nav' => __( '<i class="far fa-dot-circle"></i> Nav Title 1', 'whbm' ),
			'priority' => 10,
			'sections' => array(
				'section_0' => array(
					'title'       => __( 'Hotel Information', 'whbm' ),
					'description' => '',
					'options'     => array(
						array(
							'id'          => 'hotel_google_map',
							'title'       => __( 'Hotel Google Map', 'whbm' ),
							'details'     => __( 'Description of google map for hotel', 'whbm' ),
							'placeholder' => __( 'Text value', 'whbm' ),
							'preview'     => true,
							'type'        => 'google_map',
							'value'       => array(
								'lat'    => '25.75',
								'lng'    => '89.25',
								'zoom'   => '5',
								'title'  => 'Map Title',
								'apikey' => '',
							),
							'default'     => array(
								'lat'    => '25.75',
								'lng'    => '89.25',
								'zoom'   => '5',
								'title'  => 'Map Title',
								'apikey' => '',
							),
							'args'        => array(
								'lat'    => __( 'Latitude', 'whbm' ),
								'lng'    => __( 'Longitude', 'whbm' ),
								'zoom'   => __( 'Zoom', 'whbm' ),
								'title'  => __( 'Title', 'whbm' ),
								'apikey' => __( 'API key', 'whbm' ),
							),
						),
						array(
							'id'          => 'address',
							//'field_name'		    => 'some_id_text_field_1',
							'title'       => __( 'Address', 'whbm' ),
							'details'     => '',
							'type'        => 'textarea',
							'default'     => '',
							'placeholder' => __( 'Address Details', 'whbm' ),
						),
						array(
							'id'          => 'city',
							//'field_name'		    => 'some_id_text_field_1',
							'title'       => __( 'City', 'whbm' ),
							'details'     => '',
							'type'        => 'text',
							'default'     => '',
							'placeholder' => __( 'City Name', 'whbm' ),
						),
						array(
							'id'          => 'state',
							//'field_name'		    => 'some_id_text_field_1',
							'title'       => __( 'State', 'whbm' ),
							'details'     => '',
							'type'        => 'text',
							'default'     => '',
							'placeholder' => __( 'State Name', 'whbm' ),
						),
						array(
							'id'          => 'country',
							//'field_name'		    => 'some_id_text_field_1',
							'title'       => __( 'Country', 'whbm' ),
							'details'     => '',
							'type'        => 'text',
							'default'     => '',
							'placeholder' => __( 'Country Name', 'whbm' ),
						),
						array(
							'id'      => 'hotel_gallery',
							'title'   => __( 'Hotel Images', 'whbm' ),
							'details' => __( 'Images of hotel', 'whbm' ),
							'type'    => 'media_multi',
						),
						array(
							'id'          => 'hotel_rules',
							//'field_name'	=> 'textarea_field',
							'title'       => __( 'Hotel Rules', 'whbm' ),
							'details'     => __( 'Hotel Rules And Regulation', 'whbm' ),
							'value'       => __( 'Textarea value', 'whbm' ),
							'default'     => __( 'Default Text Value', 'whbm' ),
							'type'        => 'textarea',
							'placeholder' => __( 'Textarea placeholder', 'whbm' ),
						),
						array(
							'id'          => 'extra_features',
							'title'       => __( 'Extra Features', 'whbm' ),
							'details'     => __( 'Extra features like Morning breakfast + tour guide + whole day car service + security service', 'whbm' ),
							'collapsible' => true,
							'type'        => 'repeatable',
							'title_field' => 'text_field',
							'fields'      => array(
								array(
									'type'    => 'text',
									'default' => 'Hello 3',
									'item_id' => 'text_field',
									'name'    => 'Feature Name'
								),
								array(
									'type'    => 'number',
									'default' => '123456',
									'item_id' => 'number_field',
									'name'    => 'Price'
								)
							),
						),
						array(
							'id'          => 'hotel_faq',
							'title'       => __( 'Frequently Asked Questions', 'whbm' ),
							'details'     => __( 'Different question a user could ever asked', 'whbm' ),
							'collapsible' => true,
							'type'        => 'repeatable',
							'title_field' => 'text_field',
							'fields'      => array(
								array(
									'type'    => 'text',
									'default' => 'Type The Possible Question A User Cas Ask',
									'item_id' => 'text_field',
									'name'    => 'FAQ Title'
								),
								array(
									'type'    => 'textarea',
									'default' => 'Type your answer here',
									'item_id' => 'number_field',
									'name'    => 'Possible Answer'
								)
							),
						)
					)
				),

			),
		);


		$args = array(
			'meta_box_id'    => 'post_meta_box_1',
			'meta_box_title' => __( 'Hotel Information', 'whbm' ),
			//'callback'       => '_meta_box_callback',
			'screen'         => array( 'mage_hotel' ),
			'context'        => 'normal', // 'normal', 'side', and 'advanced'
			'priority'       => 'high', // 'high', 'low'
			'callback_args'  => array(),
			'nav_position'   => 'none', // right, top, left, none
			'item_name'      => "PickPlugins",
			'item_version'   => "1.0.2",
			'panels'         => array(
				'panelGroup-1' => $page_1_options,

			),
		);

		$AddMenuPage = new AddMetaBox( $args );
	}
}

new MageMetaBox();

class HotelPricingMetaBox {

	public function __construct() {
		$this->meta_boxs();
	}

	public function get_hotel_list() {
		$args          = array(
			'post_type'      => 'mage_hotel',
			'posts_per_page' => - 1,
		);
		$posts_details = get_posts( $args );
		$hotel         = array( '0' => 'Select a hotel' );
		foreach ( $posts_details as $rows ) {
			$hotel_id           = $rows->ID;
			$hotel_name         = $rows->post_title;
			$hotel[ $hotel_id ] = $hotel_name;
		}

		return $hotel;
	}

	public function meta_boxs() {
		$page_1_options = array(
			'page_nav' => __( '<i class="far fa-dot-circle"></i> Nav Title 1', 'whbm' ),
			'priority' => 10,
			'sections' => array(
				'section_0' => array(
					'title'       => __( 'Hotel Room And Pricing Details', 'whbm' ),
					'description' => '',
					'options'     => array(
						array(
							'id'          => 'room_quantity',
							'title'       => __( 'Room Quantity', 'whbm' ),
							'details'     => __( 'Room Quantity / Total Number Of Room', 'whbm' ),
							'type'        => 'text',
							'default'     => '10',
							'placeholder' => __( 'Text value', 'whbm' ),
						),
						array(
							'id'          => 'room_capacity',
							'title'       => __( 'Room Capacity', 'whbm' ),
							'details'     => __( 'Room Capacity / Total Number Of People Can live in one individual room',
								'whbm' ),
							'type'        => 'text',
							'default'     => '13',
							'placeholder' => __( 'Person Number', 'whbm' ),
						),
						array(
							'id'          => 'child_accepts',
							'title'       => __( 'Child Capacity', 'whbm' ),
							'details'     => __( 'Total number of child accepts with adults', 'whbm' ),
							'type'        => 'text',
							'default'     => '23',
							'placeholder' => __( 'Child QTY', 'whbm' ),
						),

						array(
							'id'          => 'global_price',
							'title'       => __( 'Room Global Price', 'whbm' ),
							'details'     => __( 'Room Price For All Time', 'whbm' ),
							'type'        => 'text',
							'default'     => '34',
							'placeholder' => __( 'Text value', 'whbm' ),
						),
						array(
							'id'          => 'seasonal_price',
							'title'       => __( 'Seasonal Price', 'whbm' ),
							'details'     => __( 'Seasonal Price list', 'whbm' ),
							'collapsible' => true,
							'type'        => 'repeatable',
							'title_field' => 'start_date',
							'fields'      => array(
								array(
									'type'    => 'date',
									'default' => '34',
									'item_id' => 'start_date',
									'name'    => 'Start Date'
								),
								array(
									'type'    => 'date',
									'default' => '242',
									'item_id' => 'end_date',
									'name'    => 'End Date'
								),
								array(
									'type'    => 'number',
									'default' => '123456',
									'item_id' => 'number_field',
									'name'    => 'Seasonal Price'
								),

							),
						),
						array(
							'id'          => 'dateWise_price',
							'title'       => __( 'DateWise Price', 'whbm' ),
							'details'     => __( 'Specific date wise price list', 'whbm' ),
							'collapsible' => true,
							'type'        => 'repeatable',
							'title_field' => 'date_field',
							'fields'      => array(
								array(
									'type'    => 'date',
									'default' => '',
									'item_id' => 'date_field',
									'name'    => 'Date'
								),
								array(
									'type'    => 'number',
									'default' => '',
									'item_id' => 'number_field',
									'name'    => 'Price'
								),

							),
						)
					)
				),
				'section_1' => array(
					'title'       => __( 'Weekly Price list', 'whbm' ),
					'description' => '',
					'options'     => array(
						array(
							'id'          => 'sunday',
							'title'       => __( 'Sunday', 'whbm' ),
							'details'     => '',
							'type'        => 'text',
							'default'     => '',
							'placeholder' => __( 'Sunday Price', 'whbm' ),
						),
						array(
							'id'          => 'monday',
							'title'       => __( 'Monday', 'whbm' ),
							'details'     => '',
							'type'        => 'text',
							'default'     => '',
							'placeholder' => __( 'Monday Price', 'whbm' ),
						),
						array(
							'id'          => 'tuesday',
							'title'       => __( 'Tuesday', 'whbm' ),
							'details'     => '',
							'type'        => 'text',
							'default'     => '',
							'placeholder' => __( 'Tuesday Price', 'whbm' ),
						),
						array(
							'id'          => 'wednesday',
							'title'       => __( 'Wednesday', 'whbm' ),
							'details'     => '',
							'type'        => 'text',
							'default'     => '',
							'placeholder' => __( 'Wednesday Price', 'whbm' ),
						),
						array(
							'id'          => 'thursday',
							'title'       => __( 'Thursday', 'whbm' ),
							'details'     => '',
							'type'        => 'text',
							'default'     => '',
							'placeholder' => __( 'Thursday Price', 'whbm' ),
						),
						array(
							'id'          => 'friday',
							'title'       => __( 'Friday', 'whbm' ),
							'details'     => '',
							'type'        => 'text',
							'default'     => '',
							'placeholder' => __( 'Friday Price', 'whbm' ),
						),
						array(
							'id'          => 'saturday',
							'title'       => __( 'Saturday', 'whbm' ),
							'details'     => '',
							'type'        => 'text',
							'default'     => '',
							'placeholder' => __( 'Saturday Price', 'whbm' ),
						)
					),
				),
				'section_2' => array(
					'title'       => __( 'Hotel List', 'whbm' ),
					'description' => '',
					'options'     => array(
						array(
							'id'      => 'hotel_list',
							//'field_name'		    => 'text_multi_field',
							'title'   => __( 'Hotel List', 'whbm' ),
							'details' => '',
							'default' => 'Choose......',
							'value'   => 'option_2',
							'type'    => 'select',
							'args'    => $this->get_hotel_list(),
						)
					),
				),
				'section_3' => array(
					'title'       => __( 'Image Gallery Of Hotel Room', 'whbm' ),
					'description' => '',
					'options'     => array(
						array(
							'id'      => 'room_gallery',
							'title'   => __( 'Hotel Room Image Gallery', 'whbm' ),
							'details' => __( 'Images of hotel', 'whbm' ),
							'type'    => 'media_multi',
						)
					),
				)
			),

		);

		$args = array(
			'meta_box_id'    => 'post_meta_box_2',
			'meta_box_title' => __( 'Hotel Room And Pricing Details', 'whbm' ),
			//'callback'       => '_meta_box_callback',
			'screen'         => array( 'mage-room-pricing' ),
			'context'        => 'normal', // 'normal', 'side', and 'advanced'
			'priority'       => 'high', // 'high', 'low'
			'callback_args'  => array(),
			'nav_position'   => 'none', // right, top, left, none
			'item_name'      => "PickPlugins",
			'item_version'   => "1.0.2",
			'panels'         => array(
				'panelGroup-1' => $page_1_options,
			),
		);

		$AddMenuPage = new AddMetaBox( $args );
		//apply_filters();
	}
}

new HotelPricingMetaBox();