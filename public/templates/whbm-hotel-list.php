<?php
/**
 * Template Name: Hotel list
 *
 */
get_header();

$destination_search = isset( $_GET['dest_name'] ) ? $_GET['dest_name'] : array();
$check_in_out       = isset( $_GET['daterange'] ) ? $_GET['daterange'] : '';
$paged              = get_query_var( "paged" ) ? get_query_var( "paged" ) : 1;
$args               = array(
	'post_type'      => 'mage_hotel',
	'paged'          => $paged,
	'posts_per_page' => 2
);
$loop               = new WP_Query( $args );
?>
	<section class="whbmt_packege_wrapper_area">
		<div class="container">
			<div class="row">
				<!-- Start Left-Side-Bar -->
				<div class="col-12 col-md-3">
					<div class="whbmt_wrapper_off_left">

						<form id="whbmt_off_left_form1"
						      action="<?php echo get_site_url() . '/whbm-hotel-search-form/' ?>">
							<h4>Search</h4>
							<div class="whbmt_form_off_left form_item_select_off_left">
								<div class="whbmt_custom_select_off_left">
									<!-- <input type="text" name="dest_name" class="dest-name" id="dest-name"
                                               value="<?php /*echo $destination_search */ ?>"
                                               placeholder="<?php /*esc_html_e( 'Type Your Destinations', 'whbm' ) */ ?>"
                                               autocomplete="off">-->
									<select class="dest-name destination_search" id="dest-name" name="dest_name">
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
									       id="daterange" value="<?php echo $check_in_out ?>" autocomplete="off">
								</div>
							</div>
							<div class="row">
								<div class="col-md-6 pad_right">
									<div class="whbmt_custom_select_off_left">
										<select>
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
										<select>
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
										<select>
											<option value="0">No Childern</option>
											<option value="0">Childern</option>
										</select>
									</div>
								</div>
								<div class="col-md-6">
									<div class="whbmt_custom_select_off_left">
										<select>
											<option value="0">2 adult</option>
											<option value="0">3 adult</option>
											<option value="0">4 adult</option>
											<option value="0">5 adult</option>
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
						<form action="" method="get">
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
											                                    value="<?php echo $value->slug ?>"><?php
													echo
													$value->name; ?></label>
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
											<div class="checkbox"><label><input class="mage_hotel_cat" type="checkbox" name="hotel_facilities[]"
											                                    value="<?php echo $facilities_value->slug ?>"><?php echo $facilities_value->name; ?>
												</label>
											</div>
										<?php }
										?>
									</div>
								</div>
							</div>
						</form>
					</div>
				</div>

				<div class="col-12 col-md-9">
					<div class="package_listing">
						<h4 class="mb-3"><?php echo $loop->post_count ?> properties found</h4>
						<ul class="nav nav-tabs" id="myTab" role="tablist">
							<li class="nav-item">
								<a class="nav-link active" id="home-tab" data-toggle="tab" href="#tab1" role="tab">Top Hotel First</a>
							</li>
							<li class="nav-item">
								<a class="nav-link" id="home-tab" data-toggle="tab" href="#tab2" role="tab">Low Price First</a>
							</li>
							<li class="nav-item">
								<a class="nav-link" id="home-tab" data-toggle="tab" href="#tab3" role="tab">Top Star Rating</a>
							</li>
							<li class="nav-item">
								<a class="nav-link" id="home-tab" data-toggle="tab" href="#tab4" role="tab">Review Score</a>
							</li>
							<li class="nav-item">
								<a class="nav-link" id="home-tab" data-toggle="tab" href="#tab5" role="tab">Distance From City</a>
							</li>
							<li class="nav-item">
								<a class="nav-link" id="home-tab" data-toggle="tab" href="#tab6" role="tab">Top Reviewer</a>
							</li>
						</ul>

						<div class="tab-content" id="myTabContent">
							<div class="tab-pane fade show active" id="tab1" role="tabpanel">
								<div class="newly_added_ajax">

								</div>
								<?php
								while ( $loop->have_posts() ) {
									$loop->the_post();
									$hotel_id = get_the_ID();
									?>
									<div class="whbmt_single_package">
										<div class="row">
											<div class="col-md-4 col-sm-6">
												<div class="package_content">
													<a href="<?php the_permalink(); ?>"><?php the_post_thumbnail( 'full' ); ?></a>
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
														<h4><a href="<?php the_permalink(); ?>"><?php
																the_title() ?></a></h4>
														<span class="package_date">2 km from centre</span>
														<p><?php the_excerpt() ?></p>
														<ul class="whbmt_package_shedule">
															<li>1 double bed</li>
															<li class="activecolor">1 sofa bed</li>
														</ul>

														<div class="row border_top">
															<div class="col-md-7">
																<div class="whbmt_package_benefits">
																	<h4><i class="fa
                                                                            fa-map-marker"></i><?php echo get_post_meta
																		( get_the_ID(), 'address', true ) ?></h4>
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
														if ( $hotel_id == $selected_hotel ) {
															$price_array[] = $global_price;
														}
													}
													?>
													<h3>
														<?php
														$curr_args = array(
															'ex_tax_label' => false,
															'currency'     => ''
														);
														$min_price_starts = get_post_meta($hotel_id, 'min_price_starts', true);
														echo wc_price($min_price_starts, $curr_args);
														?></h3>
													<?php
													?>
													<span class="room-qty">1 night, 2 adults</span>
													<a href="<?php echo get_the_permalink( $hotel_id ) ?>" class="btn btn-default
                                                            details_btn">View Details</a>
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
														typesetting industry. Lorem Ipsum has been the industry's
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
																<h4><i class="fa fa-comments-o "></i>1275 Reviews
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
														typesetting industry. Lorem Ipsum has been the industry's
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
																<h4><i class="fa fa-comments-o "></i>1275 Reviews
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
												<img src="<?php echo get_template_directory_uri(); ?>./assets/img/package3.jpg">
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
														typesetting industry. Lorem Ipsum has been the industry's
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
																<h4><i class="fa fa-comments-o "></i>1275 Reviews
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
												<img src="assets/img/package4.jpg">
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
														typesetting industry. Lorem Ipsum has been the industry's
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
																<h4><i class="fa fa-comments-o "></i>1275 Reviews
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
														typesetting industry. Lorem Ipsum has been the industry's
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
																<h4><i class="fa fa-comments-o "></i>1275 Reviews
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
														typesetting industry. Lorem Ipsum has been the industry's
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
																<h4><i class="fa fa-comments-o "></i>1275 Reviews
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
														typesetting industry. Lorem Ipsum has been the industry's
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
																<h4><i class="fa fa-comments-o "></i>1275 Reviews
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
														typesetting industry. Lorem Ipsum has been the industry's
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
																<h4><i class="fa fa-comments-o "></i>1275 Reviews
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
														typesetting industry. Lorem Ipsum has been the industry's
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
																<h4><i class="fa fa-comments-o "></i>1275 Reviews
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
														typesetting industry. Lorem Ipsum has been the industry's
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
																<h4><i class="fa fa-comments-o "></i>1275 Reviews
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
												<img src="assets/img/package3.jpg">
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
														typesetting industry. Lorem Ipsum has been the industry's
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
																<h4><i class="fa fa-comments-o "></i>1275 Reviews
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
												<img src="assets/img/package4.jpg">
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
														typesetting industry. Lorem Ipsum has been the industry's
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
																<h4><i class="fa fa-comments-o "></i>1275 Reviews
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
														typesetting industry. Lorem Ipsum has been the industry's
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
																<h4><i class="fa fa-comments-o "></i>1275 Reviews
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
														typesetting industry. Lorem Ipsum has been the industry's
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
																<h4><i class="fa fa-comments-o "></i>1275 Reviews
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

	                    <?php echo paginate_links( $pargs ); ?>
                    </div>
				</div>
			</div>
		</div>
	</section>
<?php
get_footer();