<?php
get_header();
global $post;
$post_id      = $post->ID;// example post id
$post_content = get_post( $post_id );
$content      = $post_content->post_content;
//get value of Hotel Room And Pricing from custom post

function seasonal_date_range( $first, $last, $step = '+1 day', $output_format = 'Y/m/d' ) {

	$dates   = array();
	$current = strtotime( $first );
	$last    = strtotime( $last );

	while ( $current <= $last ) {

		$dates[] = date_i18n( $output_format, $current );
		$current = strtotime( $step, $current );
	}

	return $dates;
}

//select * from table where id =1 AND (t = 1 OR t =2);

function get_booked_room_count( $checkin_date, $checkout_date, $hotel_id, $room_id ) {

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

	return $qty;
}

$date_format = get_option( 'date_format' );
$check_in_out = isset( $_GET['daterange'] ) ? $_GET['daterange'] : '';

?>

    <section class="whbmt_packege_wrapper_area">
        <div class="container">
            <div class="row">
                <!-- Start Left-Side-Bar -->
                <div class="col-12 col-md-3">
                    <div class="whbmt_wrapper_off_left">

                        <form id="whbmt_off_left_form1" action="" method="get">
							<?php //print_r($check_out); ?>
                            <h4>Search</h4>
                            <div class="whbmt_form_off_left form_item_select_off_left">
                                <div class="whbmt_custom_select_off_left">
                                    <input type="text" name="dest_name" class="dest-name" id="dest-name"
                                           value=""
                                           placeholder="<?php esc_html_e( 'Type Your Destinations', 'whbm' ) ?>"
                                           autocomplete="off">
                                </div>
                            </div>
                            <div class="whbmt_form__item_off_left">
                                <div class="whbmt_form__item_datepicker_off_left">
                                    <input type="text" name="daterange" class="whbmt_datepicker"
                                           placeholder="<?php esc_html_e( 'Checkin - Checkout', 'whbm' ) ?>"
                                           id="daterange" value="" autocomplete="off">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 pad_right">
                                    <div class="whbmt_custom_select_off_left">
                                        <select name="">
                                            <option value="0">Days</option>
                                            <option value="Barishal">0</option>
                                            <option value="Barishal">1</option>
                                            <option value="Barishal">2</option>
                                            <option value="Barishal">3</option>
                                            <option value="Barishal">4</option>
                                            <option value="Barishal">5</option>
                                            <option value="Barishal">6</option>
                                            <option value="Barishal">7</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="whbmt_custom_select_off_left">
                                        <select name="">
                                            <option value="0">1 Room</option>
                                            <option value="Barishal">2 Room</option>
                                            <option value="Barishal">4 Room</option>
                                            <option value="Barishal">5 Room</option>
                                            <option value="Barishal">7 Room</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6 pad_right">
                                    <div class="whbmt_custom_select_off_left">
                                        <select name="child_qty">
                                            <option selected="selected">No Childern</option>
                                            <option value="1">1 Children</option>
                                            <option value="2">2 Childern</option>
                                            <option value="3">3 Childern</option>
                                            <option value="4">4 Childern</option>
                                            <option value="5">5 Childern</option>
                                            <option value="6">6 Childern</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="whbmt_custom_select_off_left">
                                        <select name="adult_qty" class="adult_qty" id="adult_qty">
                                            <option selected="selected">2 adult</option>
                                            <option value="0">2 adult</option>
                                            <option value="1">3 adult</option>
                                            <option value="2">4 adult</option>
                                            <option value="4">5 adult</option>
                                            <option value="5">6 adult</option>
                                            <option value="5">7 adult</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="whbmt_form__item_off_left form__item_submit_off_left">
                                        <input type="submit" value="Search Now">
                                    </div>
                                </div>
                            </div>

                        </form>
                        <div class="whbmt_catagory_area_title">
                            <h4>Select All Filter</h4>
                        </div>
                        <div class="whbmt_package_catagory">
                            <div class="whbmt_catagory border_top">
                                <h4>Tour Package Budget</h4>
                                <div class="package_catagory_content">
                                    <div class="checkbox"><label><input type="checkbox" checked> $200 to $500</label></div>
                                    <div class="checkbox"><label><input type="checkbox"> $500 to $800</label></div>
                                    <div class="checkbox"><label><input type="checkbox"> $800 to $1000</label></div>
                                    <div class="checkbox"><label><input type="checkbox"> Above $1000</label></div>
                                </div>
                            </div>
                            <div class="whbmt_catagory border_top">
                                <h4>Duration ( in Days )</h4>
                                <div class="package_catagory_content">
                                    <div class="checkbox"><label><input type="checkbox" checked> 1 to 3 Days</label>
                                    </div>
                                    <div class="checkbox"><label><input type="checkbox"> 4 to 6 Days</label></div>
                                    <div class="checkbox"><label><input type="checkbox"> 7 to 9 Days</label></div>
                                    <div class="checkbox"><label><input type="checkbox"> 10 to 12 Days</label></div>
                                    <div class="checkbox"><label><input type="checkbox"> 13 or more Days</label>
                                    </div>
                                </div>
                            </div>
                            <div class="whbmt_catagory border_top">
                                <h4>Hotel Star Rating</h4>
                                <div class="package_catagory_content">
                                    <div class="checkbox"><label><input type="checkbox" > 3 Star</label></div>
                                    <div class="checkbox"><label><input type="checkbox"> 4 Star</label></div>
                                    <div class="checkbox"><label><input type="checkbox"> 5 Star</label></div>
                                </div>
                            </div>
                            <div class="whbmt_catagory border_top">
                                <h4>Room facilities</h4>
                                <div class="package_catagory_content">
                                    <div class="checkbox"><label><input type="checkbox" checked>
                                            Kitchen/kitchenette</label>
                                    </div>
                                    <div class="checkbox"><label><input type="checkbox"> Private bathroom</label>
                                    </div>
                                    <div class="checkbox"><label><input type="checkbox"> Air conditioning</label>
                                    </div>
                                    <div class="checkbox"><label><input type="checkbox"> Balcony</label></div>
                                    <div class="checkbox"><label><input type="checkbox"> Flat-screen TV</label>
                                    </div>
                                </div>
                            </div>
                            <div class="whbmt_catagory border_top">
                                <h4>Other Benifits</h4>
                                <div class="package_catagory_content">
                                    <div class="checkbox"><label><input type="checkbox" checked> Free WIFI</label>
                                    </div>
                                    <div class="checkbox"><label><input type="checkbox"> Welcome Drinks</label>
                                    </div>
                                    <div class="checkbox"><label><input type="checkbox"> Big Space</label></div>
                                    <div class="checkbox"><label><input type="checkbox"> Breakfast</label></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="whbmt_map_area ">
                        <iframe id="gmap_canvas"
                                src="https://maps.google.com/maps?q=Coxesbazar&amp;t=&amp;z=10&amp;ie=UTF8&amp;iwloc=&amp;output=embed"
                                frameborder="0" scrolling="no" marginheight="0" marginwidth="0"
                                style="width: 100%;min-height: 300px;"></iframe>
                    </div>
                </div>

                <div class="col-12 col-md-9">
                    <div class="package_listing">
                        <div class="row">
                            <div class="col-md-12 col-sm-12">
                                <div class="tab-content">
									<?php
									$image_meta = get_post_meta( $post_id, 'hotel_gallery' );
									$thumb      = 1;
									foreach ( $image_meta as $key => $value ) {
										$value = maybe_unserialize( $value );
										foreach ( $value as $image_key => $attachment_id ) {
											$image          = wp_get_attachment_image( $attachment_id, 'full' );
											$room_image_url = wp_get_attachment_image_url( $attachment_id, 'full' );
											?>
                                            <div id="thumb<?php echo $thumb ?>" class="tab-pane fade show">
                                                <a data-fancybox="img" href="<?php echo $room_image_url ?>"><?php
													echo $image ?></a>
                                            </div>
											<?php
											$thumb ++;
										}

									}
									?>

                                </div>
                                <!-- Thumbnail Large Image End -->

                                <!-- Thumbnail Image End -->
                                <div class="product-thumbnail mt-15">
                                    <div class="thumb_menu bor_0 owl-carousel nav tabs-area owl-loaded" role="tablist">


                                        <div class="owl-stage-outer">
                                            <div class="owl-stage"
                                                 style="transform: translate3d(0px, 0px, 0px); transition: all 0s ease 0s; width: 835px;">
												<?php
												$image_meta = maybe_unserialize( get_post_meta( $post_id, 'hotel_gallery', true ) );
												$i          = 1;
												foreach ( $image_meta as $key => $value ) {
													$image          = wp_get_attachment_image( $value, 'full' );
													$room_image_url = wp_get_attachment_image_url( $value, 'full' );
													?>
                                                    <div class="owl-item active"
                                                         style="width: 65.909px; margin-right: 10px;"><a
                                                                class="show onclick_btn" data-toggle="tab"
                                                                href="#thumb<?php echo $i; ?>"><?php echo $image; ?></a>
                                                    </div>
													<?php
													$i ++;
												}
												?>
                                            </div>
                                        </div>
                                        <div class="owl-nav disabled">
                                            <div class="owl-prev">prev</div>
                                            <div class="owl-next">next</div>
                                        </div>
                                        <div class="owl-dots disabled"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="whbmt_package_listing_content">
                            <h4><?php the_title() ?></h4>
                            <ul class="listing_details_location">
                                <li>2 km from centre</li>
                                <li><i class="fa fa-map-marker"></i> <?php echo get_post_meta( get_the_ID(), 'address',
										true ); ?></li>
                                <li><i class="fa fa-comments-o"></i> 1275 Reviews</li>
                            </ul>
                            <p><?php echo get_post_field( 'post_content', get_the_ID() ); ?></p>
                        </div>
						<?php
						$check_in_out         = isset( $_GET['daterange'] ) ? $_GET['daterange'] : date_i18n( $date_format );
						$check_in_out_explode = explode( 'to', $check_in_out );
						//$check_in_out_explode = isset( $check_in_out_explode[1] ) ? $check_in_out_explode[1] : null;
						$check_in = date_i18n( $date_format, strtotime( $check_in_out_explode[0] ) );
						if ( ! isset( $check_in_out_explode[1] ) ) {
							$check_in_out_explode[1] = date_i18n( $date_format );
						}

						$check_out = date_i18n( $date_format, strtotime( $check_in_out_explode[1] ) );
						$now       = strtotime( $check_in ); // or your date as well
						$your_date = strtotime( $check_out );
						$datediff  = $your_date - $now;

						$total_stay = round( $datediff / ( 60 * 60 * 24 ) );
						?>
                        <form action="" method="get">
                            <div class="row" id="listing_sub_content">

                                <div class="col-md-4">
                                    <div class="single_listing_sub_content">
                                        <span>Check in-out date</span>
                                        <input type="text" name="daterange" class="form-control whbmt_datepicker"
                                               placeholder="Checkin - Checkout" id="single_daterange"
                                               value="<?php echo $check_in_out ?>"
                                               autocomplete="off">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="single_listing_sub_content">
                                        <span>Guests</span>
                                        <h5>2 adults</h5>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="single_listing_sub_content">
                                        <span class="active_color">USD 190.00</span>
                                        <h6>2 night</h6>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <button type="submit" class=" btn btn-default main_btn change_btn">Change Search
                                    </button>
                                </div>
                            </div>
                        </form>

                        <div class="whbmt_room_order_summury_box">
							<?php
							//print_r($total_stay);
							?>
                            <form action="" method="post">
                                <div>
									<?php
									$hotel_type = get_the_terms( get_the_ID(), 'mage_hotel_type', true );
									if ( $hotel_type ) {
										foreach ( $hotel_type as $arr_key => $term_value ) {?>
                                            <input type="hidden" class="hotel_q_type" id="hotel_q_type" name="hotel_q_type" value="<?php echo $term_value->name ?>">
										<?php }
									}
									?>
                                </div>
                                <table class="table table-bordered">
                                    <thead class="room_heading" id="">
                                    <tr>
                                        <th>Room Type</th>
                                        <th>Person</th>
                                        <th>Price</th>
                                        <th>Select Rooms</th>
                                        <th></th>
                                    </tr>
                                    </thead>
                                    <tbody>
									<?php
									$args          = array(
										'post_type' => 'mage-room-pricing',
										'meta_key'  => 'hotel_list'
									);
									$hotel_room    = new WP_Query( $args );
									$data_count    = $hotel_room->post_count;
									$data_count_in = 1;
									while ( $hotel_room->have_posts() ) {
										$hotel_room->the_post();
										$hotel_list = get_post_meta( get_the_ID(), 'hotel_list', true );
										if ( $hotel_list == $post_id ) {
											$f = get_post_meta( get_the_ID(), 'room_quantity', true ) -
                                                 get_booked_room_count( $check_in, $check_out, $post_id, get_the_ID() );
											?>
                                            <tr class="whbmt_single_room_preview">
                                                <td class="whbmt_room_title">
                                                    <div class="row">
                                                        <div class="room_type_name_sec">
                                                            <h5 class="room-heading"><?php the_title() ?></h5>
                                                            <input type="hidden" value="<?php echo get_the_ID() ?>"
                                                                   name="room_id[]">
                                                            <input type="hidden" value="<?php echo the_title() ?>"
                                                                   name="room_name[]">
                                                        </div>
                                                        <div class="col-md-7">
                                                            <div class="tab-content">
																<?php
																$image_meta = get_post_meta( get_the_ID(), 'room_gallery' );
																$i          = 1;
																foreach ( $image_meta as $key => $value ) {
																	$value = maybe_unserialize( $value );
																	foreach ( $value as $image_key => $attachment_id ) {
																		$image          = wp_get_attachment_image( $attachment_id, 'full' );
																		$room_image_url = wp_get_attachment_image_url( $attachment_id, 'full' );
																		?>
                                                                        <div id="room<?php echo $i; ?>"
                                                                             class="tab-pane fade show"><a
                                                                                    data-fancybox="img" href="<?php echo
																			$room_image_url ?>"><?php echo $image ?></a>
                                                                        </div>
																		<?php
																		$i ++;
																	}
																}
																?>

                                                            </div>
                                                            <!-- Thumbnail Large Image End -->

                                                            <!-- Thumbnail Image End -->
                                                            <div class="product-thumbnail mt-15">
                                                                <div class="whbmt_room_thumb_menu owl-carousel nav tabs-area owl-loaded"
                                                                     role="tablist">

                                                                    <div class="owl-stage-outer">
                                                                        <div class="owl-stage"
                                                                             style="transform: translate3d(0px, 0px, 0px); transition: all 0s ease 0s; width: 177px;">
																			<?php
																			$image_meta = get_post_meta( get_the_ID(), 'room_gallery' );
																			$i          = 1;
																			foreach ( $image_meta as $key => $value ) {
																				$value = maybe_unserialize( $value );
																				foreach ( $value as $image_key => $attachment_id ) {
																					$image          = wp_get_attachment_image( $attachment_id, 'full' );
																					$room_image_url = wp_get_attachment_image_url( $attachment_id, 'full' );
																					?>
                                                                                    <div class="owl-item active"
                                                                                         style="width: 78.192px; margin-right: 10px;">
                                                                                        <a data-toggle="tab"
                                                                                           href="#room<?php echo
																						   $i ?>"><?php echo
																							$image;
																							?></a>
                                                                                    </div>
																					<?php
																					$i ++;
																				}
																			}
																			?>
                                                                        </div>
                                                                    </div>
                                                                    <div class="owl-nav disabled">
                                                                        <div class="owl-prev">prev</div>
                                                                        <div class="owl-next">next</div>
                                                                    </div>
                                                                    <div class="owl-dots disabled"></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="whbmt_room_person">
                                                    <ul>
														<?php
														$room_capacity = get_post_meta( get_the_ID(), 'room_capacity', true ); ?>
                                                        <input type="hidden" name="room_cap[]" class="room_cap"
                                                               id="room_cap" value="<?php echo $room_capacity; ?>">
														<?php for ( $i = 1; $i <= $room_capacity; $i ++ ) { ?>
                                                            <li>
                                                                <img src="<?php echo plugin_dir_url( __FILE__ ) ?>../css/images/icon.png">
                                                            </li>
														<?php }

														$global_price      = get_post_meta( get_the_ID(), 'global_price' );
														$seasonal_price_un = get_post_meta( get_the_ID(), 'seasonal_price' );
														$dateWise_price_un = get_post_meta( get_the_ID(), 'dateWise_price' );
														$sunday_price      = get_post_meta( get_the_ID(), 'sunday' );
														$monday_price      = get_post_meta( get_the_ID(), 'monday' );
														$tuesday_price     = get_post_meta( get_the_ID(), 'tuesday' );
														$wednesday_price   = get_post_meta( get_the_ID(), 'wednesday' );
														$thursday_price    = get_post_meta( get_the_ID(), 'thursday' );
														$friday_price      = get_post_meta( get_the_ID(), 'friday' );
														$saturday_price    = get_post_meta( get_the_ID(), 'saturday' );

														$sunday_has_price    = current( $sunday_price );
														$monday_has_price    = current( $monday_price );
														$tuesday_has_price   = current( $tuesday_price );
														$wednesday_has_price = current( $wednesday_price );
														$thursday_has_price  = current( $thursday_price );
														$friday_has_price    = current( $friday_price );
														$saturday_has_price  = current( $saturday_price );
														$by_week = array_merge( $sunday_price, $monday_price,
															$tuesday_price,
															$wednesday_price, $thursday_price, $friday_price, $saturday_price );


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
																				$price_value =
																					$date_arr_value['number_field'];
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

														?>
                                                    </ul>
                                                </td>
                                                <td class="whbmt_price_room">
													<?php
													$curr_args = array(
														'ex_tax_label' => false,
														'currency'     => ''
													);
													?>
                                                    <span class="active_color"><?php echo wc_price( $price_value, $curr_args );; ?></span>
                                                    <input type="hidden" name="hotel_room_price[]"
                                                           value="<?php echo $price_value; ?>">
                                                    <p class="room-price" hidden><?php echo $price_value; ?></p>
                                                    <h6>2 night</h6>
                                                </td>
                                                <td class="whbmt_select_room_quantity">
	                                                <?php
	                                                if ( $f != 0 ) {
		                                                ?>
                                                        <p>
                                                            <input class="room-quantity-number" type="number" min="0" max="<?php echo get_post_meta( get_the_ID(), 'room_quantity', true ) - get_booked_room_count( $check_in, $check_out, $post_id, get_the_ID() ); ?>" value="" name="room_qty[]">
                                                        </p>
	                                                <?php } else { ?>
                                                        <p>
			                                                <?php echo '<span style="color: red">' . esc_html__( 'Sorry no room is available with this date', 'whbm' ) . '</span>' ?>
                                                            <input class="room-quantity-number"
                                                                   type="number" min="0" max=""
                                                                   value="" name="room_qty[]"
                                                                   disabled="disabled">
                                                        </p>
	                                                <?php }
	                                                ?>
                                                </td>
												<?php if ( $data_count_in == 1 ) { ?>
                                                    <td rowspan="<?php echo $data_count; ?>">
                                                        <input type="hidden" name="daterange" class="daterange"
                                                               id="daterange" placeholder="Type Your Date"
                                                               autocomplete="off" required
                                                               value="<?php echo $check_in_out; ?>">
                                                        <input type="hidden" name="final_price" id="final_price"
                                                               value="0">
                                                        <input type="hidden" name="total_day_stay" id="total_day_stay"
                                                               value="<?php echo $total_stay ?>">
                                                        <div class="whbmt_room_order_add_to_cart">
                                                            <h5 class="mb-0"><span id="total-room"></span> room for</h5>
                                                            <h2 class="mb-0"><span id="total_price"></span></h2>
                                                            <span>Including tax & vat</span>
                                                            <button type="submit" name="add-to-cart" class="submit-to-cart
                                                    btn btn-default main_btn mt-2" value="<?php echo $post_id; ?>"><?php
																esc_html_e( 'Book Now', 'whbm' ); ?></button>
                                                            <ul class="mt-2">
                                                                <li>Free Cancelation</li>
                                                                <li>Best Rate</li>
                                                            </ul>
	                                                        <?php
	                                                        do_action( 'hotel_user_registration_form', $post_id );
	                                                        ?>
                                                        </div>
                                                    </td>
													<?php $data_count_in ++;
												} ?>
                                            </tr>
										<?php }
									}
									?>

                                    </tbody>
                                </table>
                            </form>
                        </div>
						<?php

						?>

                        <div class="whbmt_travelling_package">
                            <h2>Le Pavillon Luxury Hotel Facilities</h2>
                            <div class="row">
                                <div class="col-md-6">
                                    <ul>
                                        <li>Relish candle light dinner on beach</li>
                                        <li>Enjoy a splendid dolphin tour</li>
                                        <li>Cover Gitgit Waterfall Twin Lake View</li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <ul>
                                        <li>Visit Batubalan, Elephant Cave, Kintam</li>
                                        <li>Enjoy water sports</li>
                                        <li>Cover Gitgit Waterfalls, Twin Lake View</li>
                                    </ul>
                                </div>
                            </div>

                            <h2 class="travelling_title">Extra Charge Applicable Services</h2>
                            <div class="row">
                                <div class="col-md-6">
                                    <ul>
                                        <li>Relish candle light dinner on beach</li>
                                        <li>Enjoy a splendid dolphin tour</li>
                                        <li>Cover Gitgit Waterfall Twin Lake View</li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <ul>
                                        <li>Visit Batubalan, Elephant Cave, Kintam</li>
                                        <li>Enjoy water sports</li>
                                        <li>Cover Gitgit Waterfalls, Twin Lake View</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="whbmt_faq_tab_wrapper">
                            <h4>Some of FAQ about hotel</h4>
                            <div class="panel-group whbmt_accor_padding_top" id="accordion" role="tablist"
                                 aria-multiselectable="true">
								<?php
								$faq_un    = maybe_unserialize( get_post_meta( $post_id, 'hotel_faq', true ) );
								$row_count = 1;
								foreach ( $faq_un as $key => $value ) {
									?>
                                    <div class="panel panel-default">
                                        <div class="panel-heading" role="tab" id="heading<?php echo $row_count; ?>">
                                            <h4 class="panel-title">
                                                <a role="button" data-toggle="collapse" data-parent="#accordion"
                                                   href="#collapse<?php echo $row_count; ?>" aria-expanded="false"
                                                   aria-controls="collapse<?php echo $row_count; ?>">
													<?php echo $value['text_field'] ?>
                                                </a>
                                            </h4>
                                        </div>

                                        <div id="collapse<?php echo $row_count; ?>" class="panel-collapse collapse"
                                             role="tabpanel" aria-labelledby="heading<?php echo $row_count; ?>">
                                            <div class="panel-body">
                                                <p>
													<?php echo $value['number_field'] ?>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
									<?php $row_count ++;
								} ?>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php
get_footer();