<?php get_header();
$paged = get_query_var( "paged" ) ? get_query_var( "paged" ) : 1;
/*$date_format        = get_option( 'date_format' );*/
$destination_search = isset( $_GET['dest_name'] ) ? $_GET['dest_name'] : '';
$check_in_out       = isset( $_GET['daterange'] ) ? $_GET['daterange'] : date( 'Y/m/d' );
$adult_qty          = isset( $_GET['adult_qty'] ) ? strip_tags( $_GET['adult_qty'] ) : '';
$child_qty          = isset( $_GET['child_qty'] ) ? strip_tags( $_GET['child_qty'] ) : '';

if ( ! empty( $_GET['dest_name'] ) ) {
	/*$args = array(
	'post_type'     => 'mage_hotel',
	'post_per_page' => - 1,
	'meta_query'    => array(
	'relation' => 'AND',
	array(
	'key'   => 'hotel_location',
	'value' => $destination_search
	)
	),
	);
	$loop = new WP_Query( $args );*/
	$destination_search   = isset( $_GET['dest_name'] ) ? $_GET['dest_name'] : array();
	$check_in_out         = isset( $_GET['daterange'] ) ? $_GET['daterange'] : date( 'Y/m/d' );
	$adult_qty            = isset( $_GET['adult_qty'] ) ? strip_tags( $_GET['adult_qty'] ) : '';
	$child_qty            = isset( $_GET['child_qty'] ) ? strip_tags( $_GET['child_qty'] ) : '';
	$check_in_out_explode = explode( '-', $check_in_out );
	$check_in_out_explode = isset( $check_in_out_explode[1] ) ? $check_in_out_explode[1] : null;
	$check_in             = date( 'Y-m-d', strtotime( $check_in_out_explode[0] ) );
	$check_out            = date( 'Y-m-d', strtotime( $check_in_out_explode[1] ) );

	$args = array(
		'post_type'     => 'mage-room-pricing',
		'post_per_page' => - 1,
		'paged'         => $paged,
		'meta_query'    => array(
			'relation' => 'OR',
			array(
				'key'     => 'hotel_location',
				'value'   => $destination_search,
				'compare' => '='
			),
			array(
				'key'     => 'room_capacity',
				'value'   => $adult_qty,
				'compare' => '='
			),
			array(
				'key'     => 'child_accepts',
				'value'   => $child_qty,
				'compare' => '='
			)
		),
	);
	$loop = new WP_Query( $args );

	$hotel_id = array();
	foreach ( $loop->posts as $room ) {
		$hotel_id[] = get_post_meta( $room->ID, 'hotel_list', true );
	}
	?>
    <section class="whbmt_packege_wrapper_area">
        <div class="container">
            <div class="row">
                <!-- Start Left-Side-Bar -->
                <div class="col-12 col-md-3">
                    <div class="whbmt_wrapper_off_left">

                        <form id="whbmt_off_left_form1"
                              action="<?php echo get_site_url() . '/whbm-hotel-search-form/'
						      ?>">
                            <h4>Search</h4>
                            <div class="whbmt_form_off_left form_item_select_off_left">
                                <div class="whbmt_custom_select_off_left">
                                    <!--<input type="text" name="dest_name" class="dest-name" id="dest-name"
                                                   value="<?php /*echo $destination_search */ ?>"
                                                   placeholder="<?php /*esc_html_e( 'Type Your Destinations', 'whbm' ) */ ?>"
                                                   autocomplete="off">-->

                                    <select class="dest-name destination_search" id="dest-name"
                                            name="dest_name">
                                        <option value="" selected>Search</option>
										<?php
										$wchbm->whbm_get_location_meta();
										foreach ( $wchbm->whbm_get_location_meta() as $value ) {
											?>
                                            <option value="<?php echo $value ?>"><?php echo $value ?></option>
										<?php }
										?>
                                    </select>
                                </div>
                            </div>
                            <div class="whbmt_form__item_off_left">
                                <div class="whbmt_form__item_datepicker_off_left">
                                    <input type="text" name="daterange" class="whbmt_datepicker"
                                           placeholder="<?php esc_html_e( 'Checkin - Checkout', 'whbm' ) ?>"
                                           id="daterange" value="<?php echo $check_in_out ?>"
                                           autocomplete="off">
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
                                    <div class="checkbox"><label><input type="checkbox" class="room-price-range"
                                                                        value="200 to 500"> $200 to $500</label>
                                    </div>
                                    <div class="checkbox"><label><input type="checkbox" class="room-price-range"
                                                                        value="500 to 800">$500 to $800</label>
                                    </div>
                                    <div class="checkbox"><label><input type="checkbox" class="room-price-range"
                                                                        value="800 to 1000">$800 to
                                            $1000</label></div>
                                    <div class="checkbox"><label><input type="checkbox" class="room-price-range"
                                                                        value="1000 to 10000">Above
                                            $1000</label></div>
                                </div>
                            </div>
                            <div class="whbmt_catagory border_top">
                                <h4>Hotel Star Rating</h4>
                                <div class="package_catagory_content">
									<?php
									$terms = get_terms( [
										'taxonomy'   => 'mage_hotel_type',
										'hide_empty' => false,
									] );
									foreach ( $terms as $value ) {
										?>
                                        <div class="checkbox"><label><input type="checkbox"
                                                                            class="mage_hotel_type"
                                                                            name="hotel_type[]"
                                                                            value="<?php echo $value->slug ?>"><?php echo $value->name; ?>
                                            </label>
                                        </div>
									<?php }
									?>
                                </div>
                            </div>
                            <div class="whbmt_catagory border_top">
                                <h4>Room facilities</h4>
                                <div class="package_catagory_content">
									<?php
									$hotel_facilities = get_terms( 'mage_hotel_cat', true );
									foreach ( $hotel_facilities as $facilities_value ) {
										?>
                                        <div class="checkbox"><label><input class="mage_hotel_cat"
                                                                            type="checkbox"
                                                                            name="hotel_facilities[]"
                                                                            value="<?php echo $facilities_value->slug ?>"><?php echo $facilities_value->name; ?>
                                            </label>
                                        </div>
									<?php }
									?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-9">
                    <div class="package_listing">
                        <h4><?php echo $loop->post_count; ?><?php esc_html_e( ' propertice found', 'whbm' ); ?></h4>
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="home-tab" data-toggle="tab" href="#tab1"
                                   role="tab">Top Hotel First</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="home-tab" data-toggle="tab" href="#tab2" role="tab">Low
                                    Price First</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="home-tab" data-toggle="tab" href="#tab3" role="tab">Top
                                    Star Rating</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="home-tab" data-toggle="tab" href="#tab4" role="tab">Review
                                    Score</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="home-tab" data-toggle="tab" href="#tab5" role="tab">Distance
                                    From City</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="home-tab" data-toggle="tab" href="#tab6" role="tab">Top
                                    Reviewer</a>
                            </li>
                        </ul>

                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade show active" id="tab1" role="tabpanel">
                                <div class="newly_added_ajax">

                                </div>
								<?php
								$hotels = array_unique( $hotel_id );
								foreach ( $hotels as $_hotels ) {
									?>
                                    <div class="whbmt_single_package">
                                        <div class="row">
                                            <div class="col-md-4 col-sm-6">
                                                <div class="package_content">
                                                    <a href="<?php echo get_the_permalink( $_hotels ) ?>?dest_name=<?php echo $destination_search ?>&daterange=<?php echo $check_in_out ?>"><?php echo get_the_post_thumbnail( $_hotels );; ?></a>
                                                    <div class="single_package_content">
                                                        <h5>Top Rated</h5>
                                                        <span>5.0</span>
                                                    </div>
                                                </div>
                                                <!-- Thumbnail Large Image End -->
                                            </div>
                                            <div class="col-md-6">
                                                <div class="whbmt_single_package_details">
                                                    <div class="whbmt_package_content_right">
                                                        <h4>
                                                            <a href="<?php echo get_the_permalink( $_hotels ); ?>?dest_name=<?php echo $destination_search ?>&daterange=<?php echo $check_in_out ?>"><?php echo get_the_title( $_hotels ); ?></a>
                                                        </h4>
                                                        <span class="package_date">2 km from centre</span>
                                                        <p><?php echo get_the_excerpt( $_hotels ) ?></p>
                                                        <ul class="whbmt_package_shedule">
                                                            <li>1 double bed</li>
                                                            <li class="activecolor">1 sofa bed</li>
                                                        </ul>

                                                        <div class="row border_top">
                                                            <div class="col-md-7">
                                                                <div class="whbmt_package_benefits">
                                                                    <h4><i class="fa fa-map-marker"></i><?php
																		echo get_post_meta
																		( $_hotels, 'address', true ) ?></h4>
                                                                </div>
                                                            </div>

                                                            <div class="col-md-5">
                                                                <div class="whbmt_package_benefits">
                                                                    <h4><i class="fa fa-comments-o "></i>1275
                                                                        Reviews</h4>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-2">
                                                <div class="whbmt_single_package_right">
													<?php
													$args        = array(
														'post_type' => 'mage-room-pricing'
													);
													$room_list   = new WP_Query( $args );
													$price_array = array();
													while ( $room_list->have_posts() ) {
														$room_list->the_post();
														$global_price      = get_post_meta( get_the_ID(), 'global_price', true );
														$seasonal_price_un = get_post_meta( get_the_ID(), 'seasonal_price' );
														$dateWise_price_un = get_post_meta( get_the_ID(), 'dateWise_price' );
														$selected_hotel    = get_post_meta( get_the_ID(), 'hotel_list', true );
														if ( $_hotels == $selected_hotel ) {
															$price_array[] = $global_price;
														}
													}
													wp_reset_postdata();
													?>
                                                    <h3>
														<?php
														$curr_args = array(
															'ex_tax_label' => false,
															'currency'     => ''
														);
														echo wc_price( min( $price_array ), $curr_args );
														?></h3>
													<?php
													?>
                                                    <span class="room-qty">1 night, 2 adults<?php echo $check_in_out ?></span>
                                                    <a href="<?php echo get_the_permalink( $_hotels ) ?>?dest_name=<?php echo $destination_search ?>&daterange=<?php echo $check_in_out ?>"
                                                       class="btn btn-default details_btn">View Details</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
								<?php }
								?>

                            </div>
                            <!-- End Tab-pane -->

                            <div class="tab-pane fade" id="tab2" role="tabpanel">
                                <div class="whbmt_single_package">
                                    <div class="row">
                                        <div class="col-md-4 col-sm-6">
                                            <div class="package_content">
                                                <img src="<?php echo get_template_directory_uri(); ?>./assets/img/package1.jpg">
                                                <div class="single_package_content">
                                                    <h5>Top Rated</h5>
                                                    <span>5.0</span>
                                                </div>
                                            </div>
                                            <!-- Thumbnail Large Image End -->
                                        </div>
                                        <div class="col-md-6">
                                            <div class="whbmt_single_package_details">
                                                <div class="whbmt_package_content_right">
                                                    <h4>Le Pavillon Luxury Hotel in Downtown</h4>
                                                    <span class="package_date">2 km from centre</span>
                                                    <p>Lorem Ipsum is simply dummy text of the printing and
                                                        typesetting industry. Lorem Ipsum has been the
                                                        industry's
                                                        standard dummy
                                                        text ever since the 1500s</p>
                                                    <ul class="whbmt_package_shedule">
                                                        <li>1 double bed</li>
                                                        <li class="activecolor">1 sofa bed</li>
                                                    </ul>

                                                    <div class="row border_top">
                                                        <div class="col-md-7">
                                                            <div class="whbmt_package_benefits">
                                                                <h4><i class="fa fa-map-marker"></i>1235 Park
                                                                    Street, SV Point.USA</h4>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-5">
                                                            <div class="whbmt_package_benefits">
                                                                <h4><i class="fa fa-comments-o "></i>1275
                                                                    Reviews
                                                                </h4>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-2">
                                            <div class="whbmt_single_package_right">
                                                <h3>USD 120.00</h3>
                                                <span>1 night, 2 adults</span>
                                                <a href="#" class="btn btn-default details_btn">View Details</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="whbmt_single_package">
                                    <div class="row">
                                        <div class="col-md-4 col-sm-6">
                                            <div class="package_content">
                                                <img src="<?php echo get_template_directory_uri(); ?>./assets/img/package2.jpg">
                                                <div class="single_package_content">
                                                    <h5>Top Rated</h5>
                                                    <span>5.0</span>
                                                </div>
                                            </div>
                                            <!-- Thumbnail Large Image End -->
                                        </div>
                                        <div class="col-md-6">
                                            <div class="whbmt_single_package_details">
                                                <div class="whbmt_package_content_right">
                                                    <h4>Le Pavillon Luxury Hotel in Downtown</h4>
                                                    <span class="package_date">2 km from centre</span>
                                                    <p>Lorem Ipsum is simply dummy text of the printing and
                                                        typesetting industry. Lorem Ipsum has been the
                                                        industry's
                                                        standard dummy
                                                        text ever since the 1500s</p>
                                                    <ul class="whbmt_package_shedule">
                                                        <li>1 double bed</li>
                                                        <li class="activecolor">1 sofa bed</li>
                                                    </ul>

                                                    <div class="row border_top">
                                                        <div class="col-md-7">
                                                            <div class="whbmt_package_benefits">
                                                                <h4><i class="fa fa-map-marker"></i>1235 Park
                                                                    Street, SV Point.USA</h4>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-5">
                                                            <div class="whbmt_package_benefits">
                                                                <h4><i class="fa fa-comments-o "></i>1275
                                                                    Reviews
                                                                </h4>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-2">
                                            <div class="whbmt_single_package_right">
                                                <h3>USD 120.00</h3>
                                                <span>1 night, 2 adults</span>
                                                <a href="#" class="btn btn-default details_btn">View Details</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <!-- End Tab-pane -->

                            <div class="tab-pane fade" id="tab3" role="tabpanel">
                                <div class="whbmt_single_package">
                                    <div class="row">
                                        <div class="col-md-4 col-sm-6">
                                            <div class="package_content">
                                                <img src="assets/img/package1.jpg">
                                                <div class="single_package_content">
                                                    <h5>Top Rated</h5>
                                                    <span>5.0</span>
                                                </div>
                                            </div>
                                            <!-- Thumbnail Large Image End -->
                                        </div>
                                        <div class="col-md-6">
                                            <div class="whbmt_single_package_details">
                                                <div class="whbmt_package_content_right">
                                                    <h4>Le Pavillon Luxury Hotel in Downtown</h4>
                                                    <span class="package_date">2 km from centre</span>
                                                    <p>Lorem Ipsum is simply dummy text of the printing and
                                                        typesetting industry. Lorem Ipsum has been the
                                                        industry's
                                                        standard dummy
                                                        text ever since the 1500s</p>
                                                    <ul class="whbmt_package_shedule">
                                                        <li>1 double bed</li>
                                                        <li class="activecolor">1 sofa bed</li>
                                                    </ul>

                                                    <div class="row border_top">
                                                        <div class="col-md-7">
                                                            <div class="whbmt_package_benefits">
                                                                <h4><i class="fa fa-map-marker"></i>1235 Park
                                                                    Street, SV Point.USA</h4>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-5">
                                                            <div class="whbmt_package_benefits">
                                                                <h4><i class="fa fa-comments-o "></i>1275
                                                                    Reviews
                                                                </h4>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-2">
                                            <div class="whbmt_single_package_right">
                                                <h3>USD 120.00</h3>
                                                <span>1 night, 2 adults</span>
                                                <a href="#" class="btn btn-default details_btn">View Details</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="whbmt_single_package">
                                    <div class="row">
                                        <div class="col-md-4 col-sm-6">
                                            <div class="package_content">
                                                <img src="assets/img/package2.jpg">
                                                <div class="single_package_content">
                                                    <h5>Top Rated</h5>
                                                    <span>5.0</span>
                                                </div>
                                            </div>
                                            <!-- Thumbnail Large Image End -->
                                        </div>
                                        <div class="col-md-6">
                                            <div class="whbmt_single_package_details">
                                                <div class="whbmt_package_content_right">
                                                    <h4>Le Pavillon Luxury Hotel in Downtown</h4>
                                                    <span class="package_date">2 km from centre</span>
                                                    <p>Lorem Ipsum is simply dummy text of the printing and
                                                        typesetting industry. Lorem Ipsum has been the
                                                        industry's
                                                        standard dummy
                                                        text ever since the 1500s</p>
                                                    <ul class="whbmt_package_shedule">
                                                        <li>1 double bed</li>
                                                        <li class="activecolor">1 sofa bed</li>
                                                    </ul>

                                                    <div class="row border_top">
                                                        <div class="col-md-7">
                                                            <div class="whbmt_package_benefits">
                                                                <h4><i class="fa fa-map-marker"></i>1235 Park
                                                                    Street, SV Point.USA</h4>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-5">
                                                            <div class="whbmt_package_benefits">
                                                                <h4><i class="fa fa-comments-o "></i>1275
                                                                    Reviews
                                                                </h4>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-2">
                                            <div class="whbmt_single_package_right">
                                                <h3>USD 120.00</h3>
                                                <span>1 night, 2 adults</span>
                                                <a href="#" class="btn btn-default details_btn">View Details</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <!-- End Tab-pane -->

                            <div class="tab-pane fade" id="tab4" role="tabpanel">
                                <div class="whbmt_single_package">
                                    <div class="row">
                                        <div class="col-md-4 col-sm-6">
                                            <div class="package_content">
                                                <img src="assets/img/package1.jpg">
                                                <div class="single_package_content">
                                                    <h5>Top Rated</h5>
                                                    <span>5.0</span>
                                                </div>
                                            </div>
                                            <!-- Thumbnail Large Image End -->
                                        </div>
                                        <div class="col-md-6">
                                            <div class="whbmt_single_package_details">
                                                <div class="whbmt_package_content_right">
                                                    <h4>Le Pavillon Luxury Hotel in Downtown</h4>
                                                    <span class="package_date">2 km from centre</span>
                                                    <p>Lorem Ipsum is simply dummy text of the printing and
                                                        typesetting industry. Lorem Ipsum has been the
                                                        industry's
                                                        standard dummy
                                                        text ever since the 1500s</p>
                                                    <ul class="whbmt_package_shedule">
                                                        <li>1 double bed</li>
                                                        <li class="activecolor">1 sofa bed</li>
                                                    </ul>

                                                    <div class="row border_top">
                                                        <div class="col-md-7">
                                                            <div class="whbmt_package_benefits">
                                                                <h4><i class="fa fa-map-marker"></i>1235 Park
                                                                    Street, SV Point.USA</h4>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-5">
                                                            <div class="whbmt_package_benefits">
                                                                <h4><i class="fa fa-comments-o "></i>1275
                                                                    Reviews
                                                                </h4>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-2">
                                            <div class="whbmt_single_package_right">
                                                <h3>USD 120.00</h3>
                                                <span>1 night, 2 adults</span>
                                                <a href="#" class="btn btn-default details_btn">View Details</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="whbmt_single_package">
                                    <div class="row">
                                        <div class="col-md-4 col-sm-6">
                                            <div class="package_content">
                                                <img src="assets/img/package2.jpg">
                                                <div class="single_package_content">
                                                    <h5>Top Rated</h5>
                                                    <span>5.0</span>
                                                </div>
                                            </div>
                                            <!-- Thumbnail Large Image End -->
                                        </div>
                                        <div class="col-md-6">
                                            <div class="whbmt_single_package_details">
                                                <div class="whbmt_package_content_right">
                                                    <h4>Le Pavillon Luxury Hotel in Downtown</h4>
                                                    <span class="package_date">2 km from centre</span>
                                                    <p>Lorem Ipsum is simply dummy text of the printing and
                                                        typesetting industry. Lorem Ipsum has been the
                                                        industry's
                                                        standard dummy
                                                        text ever since the 1500s</p>
                                                    <ul class="whbmt_package_shedule">
                                                        <li>1 double bed</li>
                                                        <li class="activecolor">1 sofa bed</li>
                                                    </ul>

                                                    <div class="row border_top">
                                                        <div class="col-md-7">
                                                            <div class="whbmt_package_benefits">
                                                                <h4><i class="fa fa-map-marker"></i>1235 Park
                                                                    Street, SV Point.USA</h4>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-5">
                                                            <div class="whbmt_package_benefits">
                                                                <h4><i class="fa fa-comments-o "></i>1275
                                                                    Reviews
                                                                </h4>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-2">
                                            <div class="whbmt_single_package_right">
                                                <h3>USD 120.00</h3>
                                                <span>1 night, 2 adults</span>
                                                <a href="#" class="btn btn-default details_btn">View Details</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <!-- End Tab-pane -->

                            <div class="tab-pane fade" id="tab5" role="tabpanel">
                                <div class="whbmt_single_package">
                                    <div class="row">
                                        <div class="col-md-4 col-sm-6">
                                            <div class="package_content">
                                                <img src="assets/img/package1.jpg">
                                                <div class="single_package_content">
                                                    <h5>Top Rated</h5>
                                                    <span>5.0</span>
                                                </div>
                                            </div>
                                            <!-- Thumbnail Large Image End -->
                                        </div>
                                        <div class="col-md-6">
                                            <div class="whbmt_single_package_details">
                                                <div class="whbmt_package_content_right">
                                                    <h4>Le Pavillon Luxury Hotel in Downtown</h4>
                                                    <span class="package_date">2 km from centre</span>
                                                    <p>Lorem Ipsum is simply dummy text of the printing and
                                                        typesetting industry. Lorem Ipsum has been the
                                                        industry's
                                                        standard dummy
                                                        text ever since the 1500s</p>
                                                    <ul class="whbmt_package_shedule">
                                                        <li>1 double bed</li>
                                                        <li class="activecolor">1 sofa bed</li>
                                                    </ul>

                                                    <div class="row border_top">
                                                        <div class="col-md-7">
                                                            <div class="whbmt_package_benefits">
                                                                <h4><i class="fa fa-map-marker"></i>1235 Park
                                                                    Street, SV Point.USA</h4>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-5">
                                                            <div class="whbmt_package_benefits">
                                                                <h4><i class="fa fa-comments-o "></i>1275
                                                                    Reviews
                                                                </h4>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-2">
                                            <div class="whbmt_single_package_right">
                                                <h3>USD 120.00</h3>
                                                <span>1 night, 2 adults</span>
                                                <a href="#" class="btn btn-default details_btn">View Details</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="whbmt_single_package">
                                    <div class="row">
                                        <div class="col-md-4 col-sm-6">
                                            <div class="package_content">
                                                <img src="assets/img/package2.jpg">
                                                <div class="single_package_content">
                                                    <h5>Top Rated</h5>
                                                    <span>5.0</span>
                                                </div>
                                            </div>
                                            <!-- Thumbnail Large Image End -->
                                        </div>
                                        <div class="col-md-6">
                                            <div class="whbmt_single_package_details">
                                                <div class="whbmt_package_content_right">
                                                    <h4>Le Pavillon Luxury Hotel in Downtown</h4>
                                                    <span class="package_date">2 km from centre</span>
                                                    <p>Lorem Ipsum is simply dummy text of the printing and
                                                        typesetting industry. Lorem Ipsum has been the
                                                        industry's
                                                        standard dummy
                                                        text ever since the 1500s</p>
                                                    <ul class="whbmt_package_shedule">
                                                        <li>1 double bed</li>
                                                        <li class="activecolor">1 sofa bed</li>
                                                    </ul>

                                                    <div class="row border_top">
                                                        <div class="col-md-7">
                                                            <div class="whbmt_package_benefits">
                                                                <h4><i class="fa fa-map-marker"></i>1235 Park
                                                                    Street, SV Point.USA</h4>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-5">
                                                            <div class="whbmt_package_benefits">
                                                                <h4><i class="fa fa-comments-o "></i>1275
                                                                    Reviews
                                                                </h4>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-2">
                                            <div class="whbmt_single_package_right">
                                                <h3>USD 120.00</h3>
                                                <span>1 night, 2 adults</span>
                                                <a href="#" class="btn btn-default details_btn">View Details</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <!-- End Tab-pane -->

                            <div class="tab-pane fade" id="tab6" role="tabpanel">
                                <div class="whbmt_single_package">
                                    <div class="row">
                                        <div class="col-md-4 col-sm-6">
                                            <div class="package_content">
                                                <img src="assets/img/package1.jpg">
                                                <div class="single_package_content">
                                                    <h5>Top Rated</h5>
                                                    <span>5.0</span>
                                                </div>
                                            </div>
                                            <!-- Thumbnail Large Image End -->
                                        </div>
                                        <div class="col-md-6">
                                            <div class="whbmt_single_package_details">
                                                <div class="whbmt_package_content_right">
                                                    <h4>Le Pavillon Luxury Hotel in Downtown</h4>
                                                    <span class="package_date">2 km from centre</span>
                                                    <p>Lorem Ipsum is simply dummy text of the printing and
                                                        typesetting industry. Lorem Ipsum has been the
                                                        industry's
                                                        standard dummy
                                                        text ever since the 1500s</p>
                                                    <ul class="whbmt_package_shedule">
                                                        <li>1 double bed</li>
                                                        <li class="activecolor">1 sofa bed</li>
                                                    </ul>

                                                    <div class="row border_top">
                                                        <div class="col-md-7">
                                                            <div class="whbmt_package_benefits">
                                                                <h4><i class="fa fa-map-marker"></i>1235 Park
                                                                    Street, SV Point.USA</h4>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-5">
                                                            <div class="whbmt_package_benefits">
                                                                <h4><i class="fa fa-comments-o "></i>1275
                                                                    Reviews
                                                                </h4>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-2">
                                            <div class="whbmt_single_package_right">
                                                <h3>USD 120.00</h3>
                                                <span>1 night, 2 adults</span>
                                                <a href="#" class="btn btn-default details_btn">View Details</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="whbmt_single_package">
                                    <div class="row">
                                        <div class="col-md-4 col-sm-6">
                                            <div class="package_content">
                                                <img src="assets/img/package2.jpg">
                                                <div class="single_package_content">
                                                    <h5>Top Rated</h5>
                                                    <span>5.0</span>
                                                </div>
                                            </div>
                                            <!-- Thumbnail Large Image End -->
                                        </div>
                                        <div class="col-md-6">
                                            <div class="whbmt_single_package_details">
                                                <div class="whbmt_package_content_right">
                                                    <h4>Le Pavillon Luxury Hotel in Downtown</h4>
                                                    <span class="package_date">2 km from centre</span>
                                                    <p>Lorem Ipsum is simply dummy text of the printing and
                                                        typesetting industry. Lorem Ipsum has been the
                                                        industry's
                                                        standard dummy
                                                        text ever since the 1500s</p>
                                                    <ul class="whbmt_package_shedule">
                                                        <li>1 double bed</li>
                                                        <li class="activecolor">1 sofa bed</li>
                                                    </ul>

                                                    <div class="row border_top">
                                                        <div class="col-md-7">
                                                            <div class="whbmt_package_benefits">
                                                                <h4><i class="fa fa-map-marker"></i>1235 Park
                                                                    Street, SV Point.USA</h4>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-5">
                                                            <div class="whbmt_package_benefits">
                                                                <h4><i class="fa fa-comments-o "></i>1275
                                                                    Reviews
                                                                </h4>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-2">
                                            <div class="whbmt_single_package_right">
                                                <h3>USD 120.00</h3>
                                                <span>1 night, 2 adults</span>
                                                <a href="#" class="btn btn-default details_btn">View Details</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- End Tab-pane -->
                        </div>
                        <!-- End Tab-content -->
                    </div>
					<?php
					wp_reset_postdata();
					$pargs = array(
						"current" => $paged,
						"total"   => $loop->max_num_pages
					);
					?>
                    <div class="pagination_nav">
                        <a href="#" class="pagination_btn"><i class="fa fa-angle-left"></i></a>
						<?php echo paginate_links( $pargs ); ?>
                        <a href="#" class="pagination_btn"><i class="fa fa-angle-right"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php } else {
	?>
    <section>
        <div id="whbm_search_form" class="container">
            <div class="row">
                <div class="col-12">
                    <div class="whbmt_form_area">
                        <div class="whbmt_banner_title text-center whbm_search_form_heading">
                            <h1 class="banner_heading ">Find the best deals from all the major hotel in popular
                                city</h1>
                        </div>
                        <form id="whbmt_wanderlust_form1" class="search-form-shortcode" action="" method="get">
                            <div class="whbmt_form__item form__item_select">
                                <div class="whbmt_custom_select form_destination">
                                    <select class="dest-name destination_search" id="dest-name"
                                            name="dest_name">
                                        <option value="" selected>Search</option>
										<?php
										$wchbm->whbm_get_location_meta();
										foreach ( $wchbm->whbm_get_location_meta() as $value ) {
											?>
                                            <option value="<?php echo $value ?>"><?php echo $value ?></option>
										<?php }
										?>
                                    </select>
                                </div>
                            </div>
                            <div class="whbmt_form__item checkin-out">
                                <div class="whbmt_form__item_datepicker form__item_select">
                                    <input type="text" name="daterange" class="whbmt_datepicker"
                                           placeholder="<?php esc_html_e( 'Checkin - Checkout', 'whbm' ) ?>"
                                           id="daterange" value="" autocomplete="off">
                                </div>
                            </div>
                            <div class="whbmt_form__item">
                                <div class="whbmt_custom_select">
                                    <select name="adult_qty">
                                        <option value="0">2 adult</option>
                                        <option value="1">3 adult</option>
                                        <option value="2">4 adult</option>
                                        <option value="3">5 adult</option>
                                    </select>
                                </div>
                            </div>
                            <div class="whbmt_form__item">
                                <div class="whbmt_custom_select">
                                    <select name="child_qty">
                                        <option value="0">No Childern</option>
                                        <option value="1">2</option>
                                        <option value="2">3</option>
                                        <option value="3">4</option>
                                    </select>
                                </div>
                            </div>
                            <div class="whbmt_form__item">
                                <div class="whbmt_custom_select">
                                    <select>
                                        <option value="0">1 Room</option>
                                        <option value="Barishal">2 Room</option>
                                        <option value="Barishal">4 Room</option>
                                        <option value="Barishal">5 Room</option>
                                        <option value="Barishal">7 Room</option>
                                    </select>
                                </div>
                            </div>
                            <div class="whbmt_form__item form__item_submit">
                                <input type="submit" value="Search">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
	<?php
}
get_footer();