<?php
get_header();
global $post, $magemain;;
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

$date_format        = get_option( 'date_format' );
$check_in_out       = isset( $_GET['daterange'] ) ? $_GET['daterange'] : '';
$destination_search = isset( $_GET['dest_name'] ) ? $_GET['dest_name'] : '';

?>
    <section class="whbmt_packege_wrapper_area">
		<?php do_action( 'woocommerce_before_single_product' ); ?>
        <div class="container">
            <div class="row">
                <!-- Start Left-Side-Bar -->
                <div class="col-12 col-md-3">
                    <div class="whbmt_wrapper_off_left">

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
									if ( is_array( $image_meta ) || is_object( $image_meta ) ) {
										foreach ( $image_meta as $key => $value ) {
											$value = maybe_unserialize( $value );
											if ( is_array( $value ) || is_object( $value ) ) {
												foreach ( $value as $image_key => $attachment_id ) {
													$image          = wp_get_attachment_image( $attachment_id, 'full' );
													$room_image_url = wp_get_attachment_image_url( $attachment_id, 'full' );
													?>
                                                    <div id="thumb<?php echo $thumb ?>" class="tab-pane fade show">
                                                        <a data-fancybox="img"
                                                           href="<?php echo $room_image_url ?>"><?php
															echo $image ?></a>
                                                    </div>
													<?php
													$thumb ++;
												}
											}
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
												if ( is_array( $image_meta ) || is_object( $image_meta ) ) {
													foreach ( $image_meta as $key => $value ) {
														$image          = wp_get_attachment_image( $value, 'full' );
														$room_image_url = wp_get_attachment_image_url( $value, 'full' );
														?>
                                                        <div class="owl-item"
                                                             style="width: 65.909px; margin-right: 10px;"><a
                                                                    class="show" data-toggle="tab"
                                                                    href="#thumb<?php echo $i; ?>"><?php echo $image; ?></a>
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
                        <div class="whbmt_package_listing_content">
                            <h4><?php the_title(); ?></h4>
                            <ul class="listing_details_location">
                                <li><?php ?>2 km from centre</li>
                                <li><i class="fa fa-map-marker"></i> <?php echo get_post_meta( get_the_ID(), 'address',
										true ); ?></li>
                                <li><i class="fa fa-comments-o"></i> 1275 Reviews</li>
                            </ul>
                            <p><?php echo get_post_field( 'post_content', get_the_ID() ); ?></p>
                        </div>
						<?php
						$check_in_out         = isset( $_GET['daterange'] ) ? $_GET['daterange'] : 'Please Select Date';
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
                                <div class="col-12">
                                    <label for="daterange">Check in-out date</label>
                                </div>
                                <div class="col-6">
                                    <input type="text" name="daterange" class="form-control whbmt_datepicker
                                        custom_whbm" id="daterange"
                                           placeholder="<?php esc_html_e( 'Checkin & Checkout Date' ); ?>"
                                           value="<?php echo $check_in_out ?>"
                                           autocomplete="off">
                                </div>
                                <div class="col-3 whbmt-total-guest">
                                    <span>Guests</span>
                                    <h5>2 adults</h5>
                                </div>
                                <div class="col-3">
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
										foreach ( $hotel_type as $arr_key => $term_value ) { ?>
                                            <input type="hidden" class="hotel_q_type" id="hotel_q_type"
                                                   name="hotel_q_type" value="<?php echo $term_value->name ?>">
										<?php }
									}
									?>
                                </div>
                                <table class="table table-bordered">
                                    <thead class="room_heading" id="">
                                    <tr>
                                        <th width="25%">Room Type</th>
                                        <th width="15%">Person</th>
                                        <th width="15%">Price</th>
                                        <th width="25%">Select Rooms</th>
                                        <th width="30%"></th>
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
									$id            = 1;
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
                                                            <a href="" data-toggle="modal"
                                                               data-target="#myModal<?php echo $id ?>"><?php
																the_title() ?></h5></a>
                                                            <input type="hidden" value="<?php echo get_the_ID() ?>"
                                                                   name="room_id[]">
                                                            <input type="hidden" value="<?php echo the_title() ?>"
                                                                   name="room_name[]">
                                                        </div>
                                                        <div class="col-md-9">
                                                            <div class="modal fade" id="myModal<?php echo $id ?>">
                                                                <div class="modal-dialog modal-dialog-centered">
                                                                    <div class="modal-content">
                                                                        <div class="modal-body">
                                                                            <div class="">
                                                                                <h3><?php the_title() ?></h3>
                                                                            </div>
                                                                            <div class="row">
                                                                                <div class="room-image-gallery col-md-6">
																					<?php
																					$image_meta = get_post_meta( get_the_ID(), 'room_gallery' );
																					$i          = 1;
																					foreach ( $image_meta as $key => $value ) {
																						$value = maybe_unserialize( $value );
																						foreach ( $value as $image_key => $attachment_id ) {
																							$image          =
																								wp_get_attachment_image(
																									$attachment_id,
																									array( 200, 300 ) );
																							$room_image_url =
																								wp_get_attachment_image_url( $attachment_id, array(
																									300,
																									300
																								) );
																							echo $image; ?>
																							<?php $i ++;
																						}
																					}
																					?>
                                                                                </div>
                                                                                <div class="col-md-6">
                                                                                    <div class=" room-facilities-modal">
                                                                                        <h4>Room Facilities</h4>
																						<?php
																						$room_facilities = get_post_meta( get_the_ID(), 'room_facilities', true );
																						if ( is_array( $room_facilities ) || is_object( $room_facilities ) ) {
																						foreach ( maybe_unserialize( $room_facilities ) as $key => $value ) {
																							?>
                                                                                            <p><?php echo $value['feature_name'] ?></p>
																						<?php }}
																						?>
                                                                                    </div>
                                                                                    <div class="room-capacity-modal">
                                                                                        <h4>Room Capacities</h4>
                                                                                        <ul>
																							<?php
																							$room_capacity = get_post_meta(
																								get_the_ID(), 'room_capacity', true
																							);
																							$child         = get_post_meta( get_the_ID(), 'child_accepts', true );
																							for ( $i = 1; $i <= $room_capacity; $i ++ ) { ?>
                                                                                                <li>
                                                                                                    <i class="fas fa-user"></i>
                                                                                                </li>
																							<?php } ?>
                                                                                            +
																							<?php for (
																								$j = 1; $j <= $child;
																								$j ++
																							) { ?>
                                                                                                <li><i class="fas
                                                                                            fa-child"></i></li>
																							<?php }
																							$curr_args = array(
																								'ex_tax_label' => false,
																								'currency'     => ''
																							);
																							?>
                                                                                        </ul>
                                                                                    </div>
                                                                                    <div class="room-price-modal">
                                                                                        <p>Hurry! Limited left only
																							<?php echo wc_price( $magemain->get_room_price( get_the_ID() ), $curr_args ) ?></p>
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                            <div class="modal-footer">
                                                                                <button type="button"
                                                                                        class="btn btn-danger"
                                                                                        data-dismiss="modal">
                                                                                    Close
                                                                                </button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <!-- Thumbnail Large Image End -->

                                                            <!-- Thumbnail Image End -->
                                                            <div class="product-thumbnail mt-15">
                                                                <div class="whbmt_room_thumb_menu nav tabs-area"
                                                                     role="tablist">

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
                                                    <span class="active_color"><?php echo wc_price( $magemain->get_room_price( get_the_ID() ), $curr_args );; ?></span>
                                                    <input type="hidden" name="hotel_room_price[]"
                                                           value="<?php echo $magemain->get_room_price( get_the_ID() ); ?>">
                                                    <p class="room-price"
                                                       hidden><?php echo $magemain->get_room_price( get_the_ID() ); ?></p>
                                                    <h6>Per night</h6>
                                                </td>
                                                <td class="whbmt_select_room_quantity">
													<?php
													if ( $f != 0 ) {
														?>
                                                        <p>
                                                            <input class="room-quantity-number" type="number" min="0"
                                                                   max="<?php echo get_post_meta( get_the_ID(), 'room_quantity', true ) - get_booked_room_count( $check_in, $check_out, $post_id, get_the_ID() ); ?>"
                                                                   value="" name="room_qty[]">
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
                                                    <td class="whbmt-order-summary" rowspan="<?php echo $data_count;
													?>">
                                                        <input type="hidden" name="daterange" class="daterange"
                                                               id="" placeholder="Type Your Date"
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
                                                    btn btn-default main_btn mt-2" value="<?php
															echo get_post_meta( $post_id, 'link_wc_product', true ); ?>"><?php
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
										$id ++;
									}
									?>

                                    </tbody>
                                </table>
                            </form>
                        </div>
						<?php

						?>

                        <div class="whbmt_travelling_package">
                            <h2><?php echo get_the_title($post_id) ?> Facilities</h2>
                            <div class="row">
                                <div class="col-md-6">
                                    <ul>
                                        <?php
                                            $hotel_facilities = get_terms('mage_hotel_cat', true );
                                            foreach ( $hotel_facilities as $facilities_value ) {?>
                                                <li><?php echo $facilities_value->name ?></li>

                                        <?php    }
                                        ?>
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
                                        <?php
                                        $extra_features = maybe_unserialize(get_post_meta($post_id, 'extra_features', true));
                                        write_log($extra_features);
                                        foreach ($extra_features as $single_feature){?>
                                            <li><?php echo $single_feature['text_field']?></li>
                                    <?php  }
                                        ?>

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
						<?php
						$faq_un    = maybe_unserialize( get_post_meta( $post_id, 'hotel_faq', true ) );
						$row_count = 1;
						if ( is_array( $faq_un ) || is_object( $faq_un ) ) {
							?>
                            <div class="whbmt_faq_tab_wrapper">
                                <h4>Some of FAQ about hotel</h4>
                                <div class="panel-group whbmt_accor_padding_top" id="accordion" role="tablist"
                                     aria-multiselectable="true">
									<?php
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
						<?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php
get_footer();