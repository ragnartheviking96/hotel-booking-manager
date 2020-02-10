<?php
if ( ! defined( 'ABSPATH' ) ) {
	die;
} // Cannot access pages directly.
class Hotel_Shortcode_Hello {
	/**
	 * Hotel_Shortcode_Hello constructor.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'shortcode_initialize' ) );
	}

	public function shortcode_initialize() {
		add_shortcode( 'hotel-list', array( $this, 'whbm_hotel_list' ) );
		add_shortcode( 'whbm-hotel-search', array( $this, 'whbm_hotel_search_shortcode' ) );
		add_shortcode( 'whbm_single_hotel', array( $this, 'whbm_single_hotel' ) );
		add_shortcode( 'whbm-hotel-search-form', array( $this, 'whbm_hotel_search_form' ) );
		add_action( 'wp_ajax_ajax_whbm_hotel_list', array( $this, 'ajax_whbm_hotel_list' ) );
		add_action( 'wp_ajax_nopriv_ajax_whbm_hotel_list', array( $this, 'ajax_whbm_hotel_list' ) );
		//add_shortcode('ajax_whbm_hotel_list', array( $this, 'ajax_whbm_hotel_list'));
	}

	public function ajax_whbm_hotel_list() {

		$hotel_facilities = $_GET['hotel_facilities'];
		$hotel_type       = $_GET['hotel_type'];

		if ( ! isset( $hotel_facilities ) && ! isset( $hotel_type ) ) {
			$args = array(
				'post_type'      => 'mage_hotel',
				'post_status'    => 'publish',
				'posts_per_page' => - 1,
			);
		} else {

			$args = array(
				'post_type'      => 'mage_hotel',
				'post_status'    => 'publish',
				'posts_per_page' => - 1,
				'tax_query'      => array(
					'relation' => 'OR',
					array(
						'taxonomy' => 'mage_hotel_type',
						'field'    => 'slug',
						'terms'    => $hotel_type,
					),
					array(
						'taxonomy' => 'mage_hotel_cat',
						'field'    => 'slug',
						'terms'    => $hotel_facilities,
					),
				),
			);
		}

		$posts     = 'No posts found.';
		$the_query = new WP_Query( $args );
//		echo $the_query->post_count;
		if ( $the_query->have_posts() ) :
			ob_start();
			while ( $the_query->have_posts() ) {
				$the_query->the_post();
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
									//write_log($global_price);
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
									echo wc_price( min( $price_array ), $curr_args );
									?></h3>
								<?php
								?>
                                <span class="room-qty">1 night, 2 adults</span>
                                <a href="<?php the_permalink() ?>" class="btn btn-default
                                                            details_btn">View Details</a>
                            </div>
                        </div>
                    </div>
                </div>
			<?php }
			$posts = ob_get_clean();
		endif;
		//echo $hotel_facilities;
		$return = array(
			'posts' => $posts
		);

		wp_send_json( $return );
	}

	/**
	 * @param $atts
	 * @param null $content
	 *
	 * @return string
	 * This method creates shortcode all functionality
	 */
	public function whbm_hotel_list( $atts, $content = null ) {
		$atts = shortcode_atts( array(
			'view'     => 'list',
			'location' => '',
			'type'     => ''
		), $atts, 'hotel-list' );
		ob_start();
		$destination_search = isset( $_GET['dest_name'] ) ? $_GET['dest_name'] : '';
		$check_in_out       = isset( $_GET['daterange'] ) ? $_GET['daterange'] : '';
		$args               = array(
			'post_type' => 'mage_hotel',
		);
		$loop               = new WP_Query( $args );

		?>
        <section class="whbmt_packege_wrapper_area">
            <div class="container">
                <div class="row">
                    <!-- Start Left-Side-Bar -->
                    <div class="col-12 col-md-3">
                        <div class="whbmt_wrapper_off_left">

                            <form id="whbmt_off_left_form1" action="<?php echo get_site_url() . '/hotel-search-list/'
							?>">
                                <h4>Search</h4>
                                <div class="whbmt_form_off_left form_item_select_off_left">
                                    <div class="whbmt_custom_select_off_left">
                                        <input type="text" name="dest_name" class="dest-name" id="dest-name"
                                               value="<?php echo $destination_search ?>"
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
                                            <div class="checkbox"><label><input type="checkbox" class="room-price-range" value="200 to 500"> $200 to $500</label></div>
                                            <div class="checkbox"><label><input type="checkbox" class="room-price-range" value="500 to 800">$500 to $800</label></div>
                                            <div class="checkbox"><label><input type="checkbox" class="room-price-range" value="800 to 1000">$800 to $1000</label></div>
                                            <div class="checkbox"><label><input type="checkbox" class="room-price-range" value="1000 to 10000">Above $1000</label></div>
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
                            </form>
                        </div>
                    </div>

                    <div class="col-12 col-md-9">
                        <div class="package_listing">
                            <h4>United States: 49 propertice found</h4>
                            <ul class="nav nav-tabs" id="myTab" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="home-tab" data-toggle="tab" href="#tab1" role="tab">Top
                                        Hotel First</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="home-tab" data-toggle="tab" href="#tab2" role="tab">Low
                                        Price First</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="home-tab" data-toggle="tab" href="#tab3" role="tab">Top Star
                                        Rating</a>
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
															//write_log($global_price);
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
															echo wc_price( min( $price_array ), $curr_args );
															?></h3>
														<?php
														?>
                                                        <span class="room-qty">1 night, 2 adults</span>
                                                        <a href="<?php the_permalink() ?>" class="btn btn-default
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

                        <div class="pagination_nav">
                            <a href="#" class="pagination_btn"><i class="fa fa-angle-left"></i></a>
                            <a href="#" class="active">1</a>
                            <a href="#">2</a>
                            <a href="#">2</a>
                            <a href="#">3</a>
                            <a href="#">4</a>
                            <a href="#">7</a>
                            <a href="#">9</a>
                            <a href="#">...</a>
                            <a href="#" class="pagination_btn"><i class="fa fa-angle-right"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
		<?php
		return ob_get_clean();
	}// end of mage_shortcode_hello

	/*
	 * Hotel Information
	 */
	public function mage_hotel_information( $hotel ) {
		$date_format = get_option( 'date_format' );
		$html        = '<div class="col-12 col-md-3">';
		$html        .= '<div class="whbmt_wrapper_off_left" >';
		$html        .= '<div class="whbm-hotel-search-form-sec">';
		$html        .= '<form action="' . get_site_url() . '/hotel-search-list/" method="get">';
		$html        .= $this->whbm_hotel_search_form();
		$html        .= '</form>';
		$html        .= '</div>';

		$args          = array(
			'post_type' => 'mage_hotel',
		);
		$posts_details = get_posts( $args );
		$html          .= '<div class="hotel-list">';

		foreach ( $posts_details as $key => $rows ) {
			$hotel_type_arr = get_the_terms( $rows->ID, 'mage_hotel_type' );
			$hotel_type     = '';
			if ( is_array( $hotel_type_arr ) ) {
				foreach ( $hotel_type_arr as $type_arr_key => $type_arr_value ) {
					$hotel_type = $type_arr_value->name;
				}
			}

			$hotel_address = get_post_meta( $rows->ID, 'address' );
			$hotel_city    = get_post_meta( $rows->ID, 'city' );
			$hotel_state   = get_post_meta( $rows->ID, 'state' );
			$hotel_country = get_post_meta( $rows->ID, 'country' );
			$address       = array_merge( $hotel_address, $hotel_city, $hotel_state, $hotel_country );
			if ( ( ( $hotel_city[0] == $hotel['location'] ) || ( $hotel['location'] == '' ) ) && ( $hotel_type == $hotel['type'] || ( $hotel['type'] == '' ) ) ) {
				$image_meta = get_post_meta( $rows->ID, 'hotel_gallery' );
				$image      = '';
				$image_url  = '';
				$html       .= '<div class="single-hotel"><div class="hotel-wrapper">';
				$html       .= '<div class="room-image-gallery hotel-gallery">';
				foreach ( $image_meta as $image_meta_key => $value ) {
					$value = maybe_unserialize( $value );
					if ( is_array( $value ) || is_object( $value ) ) {
						foreach ( $value as $image_key => $attachment_id ) {
							$image     = wp_get_attachment_image( $attachment_id, array( 10, 10 ) );
							$image_url = wp_get_attachment_image_url( $attachment_id, array( 100, 500 ) );
						}
					}
				}
				$html              .= '<a href="' . $image_url . '">' . $image . '</a>';
				$html              .= '</div>';
				$html              .= '<div class="hotel-info">';
				$html              .= '<div class="hotel-address"><ul class="info-list"><li><a href="' . get_permalink( $rows->ID ) . '"><h4 id="hotel-title">' .
				                      $rows->post_title . '</h4></a></li>';
				$html              .= '<li><span class="hotel-q-type">' . $hotel_type . '</span></li>';
				$html              .= '<li><span class="dashicons dashicons-location"></span>' . $hotel_city[0] . '</li></ul></div>';
				$html              .= '<div class="hotel-facilities-list"><ul class="">';
				$features_tax_data = get_terms( array(
					'taxonomy' => 'mage_hotel_cat'
				) );
				foreach ( $features_tax_data as $tax_key => $value ) {
					$icat_desc = get_term_meta( $value->term_id, 'icon_field', true );
					$html      .= '<li>' . $value->name . '<i class="feature-icon ' . $icat_desc . '"></i></li>';
				}
				$html .= '</ul></div>';
				$html .= '</div></div></div>';

			}
		}
		$html .= '</div>';

		return $html;
	}// end of mage_hotel_information method

	/**
	 * @return string
	 */
	public function whbm_hotel_search_form( $atts ) {
		$defaults = array();
		$params   = shortcode_atts( $defaults, $atts );
		ob_start();
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
			$destination_search   = isset( $_GET['dest_name'] ) ? $_GET['dest_name'] : '';
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

			//write_log($loop->post_count);
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
                                      action="<?php echo get_site_url() . '/hotel-search-list/'
								      ?>">
                                    <h4>Search</h4>
                                    <div class="whbmt_form_off_left form_item_select_off_left">
                                        <div class="whbmt_custom_select_off_left">
                                            <input type="text" name="dest_name" class="dest-name" id="dest-name"
                                                   value="<?php echo $destination_search ?>"
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
                                            <div class="checkbox"><label><input type="checkbox" class="room-price-range" value="200 to 500"> $200 to $500</label></div>
                                            <div class="checkbox"><label><input type="checkbox" class="room-price-range" value="500 to 800">$500 to $800</label></div>
                                            <div class="checkbox"><label><input type="checkbox" class="room-price-range" value="800 to 1000">$800 to $1000</label></div>
                                            <div class="checkbox"><label><input type="checkbox" class="room-price-range" value="1000 to 10000">Above $1000</label></div>
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
                                                                                    value="<?php echo $value->slug ?>"><?php echo $value->name; ?></label>
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
                                <h4>United States: 49 propertice found</h4>
                                <ul class="nav nav-tabs" id="myTab" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" id="home-tab" data-toggle="tab" href="#tab1"
                                           role="tab">Top
                                            Hotel First</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="home-tab" data-toggle="tab" href="#tab2" role="tab">Low
                                            Price First</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="home-tab" data-toggle="tab" href="#tab3" role="tab">Top
                                            Star
                                            Rating</a>
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
                                                            <a href="<?php echo get_the_permalink( $_hotels ); ?>"><?php echo get_the_post_thumbnail( $_hotels );; ?></a>
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
                                                                <h4><a href="<?php echo get_the_permalink( $_hotels );
																	?>"><?php echo get_the_title( $_hotels ); ?></a>
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
                                                                            <h4><i class="fa
                                                                            fa-map-marker"></i><?php echo get_post_meta
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
																//write_log($global_price);
																if ( $_hotels == $selected_hotel ) {
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
																echo wc_price( min( $price_array ), $curr_args );
																?></h3>
															<?php
															?>
                                                            <span class="room-qty">1 night, 2 adults</span>
                                                            <a href="<?php the_permalink() ?>" class="btn btn-default
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

                            <div class="pagination_nav">
                                <a href="#" class="pagination_btn"><i class="fa fa-angle-left"></i></a>
                                <a href="#" class="active">1</a>
                                <a href="#">2</a>
                                <a href="#">2</a>
                                <a href="#">3</a>
                                <a href="#">4</a>
                                <a href="#">7</a>
                                <a href="#">9</a>
                                <a href="#">...</a>
                                <a href="#" class="pagination_btn"><i class="fa fa-angle-right"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
		<?php } else {
			?>
            <div id="whbmt_form_area whbm_search_form">
                <div class="row justify-content-center">
                    <div class="col-lg-10 col-md-12 col-sm-12 col-xs-12">
                        <div class="whbmt_form_area">
                            <div class="whbmt_banner_title text-center whbm_search_form_heading">
                                <h1 class="banner_heading ">Find the best deals from all the major
                                    hotel in popular
                                    city</h1>
                            </div>
                            <form id="whbmt_wanderlust_form1" class="search-form-shortcode" action="" method="get">
                                <div class="whbmt_form__item form__item_select">
                                    <div class="whbmt_custom_select form_destination">
                                        <input type="text" name="dest_name" class="dest-name" id="dest-name"
                                               value="<?php echo $destination_search ?>"
                                               placeholder="<?php esc_html_e( 'Type Your Destinations', 'whbm' ) ?>"
                                               autocomplete="off">
                                    </div>
                                </div>
                                <div class="whbmt_form__item">
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
			<?php
		}
		$content = ob_get_clean();

		return $content;

		//return ob_get_clean();
	}// end of whbm_hotel_search_form method

	public function whbm_hotel_search_shortcode( $atts ) {
		global $post;
		$date_format = get_option( 'date_format' );
		$atts        = shortcode_atts( array(), $atts, 'whbm-hotel-search' );
		ob_start();
		$today     = date_i18n( $date_format );
		$next_date = strtotime( "+1 day", strtotime( $today ) );
		//write_log($next_date);
		$next_date = date_i18n( $date_format, $next_date );

		$destination_search = isset( $_GET['dest_name'] ) ? $_GET['dest_name'] : '';
		$check_in_out       = isset( $_GET['daterange'] ) ? $_GET['daterange'] : '';
		$args               = array(
			'post_type'     => 'mage-room-pricing',
			'post_per_page' => - 1,
			'meta_query'    => array(
				'relation' => 'AND',
				array(
					'key'     => 'hotel_location',
					'value'   => $destination_search,
					'compare' => '='
				),
				/*array(
					'key'     => 'room_capacity',
					'value'   => $adult_qty,
					'compare' => '='
				),
				array(
					'key'     => 'child_accepts',
					'value'   => $child_qty,
					'compare' => '='
				)*/
			),
		);

		?>

        <section class="whbmt_packege_wrapper_area">
            <div class="container">
                <div class="row">
                    <!-- Start Left-Side-Bar -->
                    <div class="col-12 col-md-3">
                        <div class="whbmt_wrapper_off_left">

                            <form id="whbmt_off_left_form1" action="<?php echo get_site_url() . '/hotel-search-list/'
							?>">
                                <h4>Search</h4>
                                <div class="whbmt_form_off_left form_item_select_off_left">
                                    <div class="whbmt_custom_select_off_left">
                                        <input type="text" name="dest_name" class="dest-name" id="dest-name"
                                               value="<?php echo $destination_search ?>"
                                               placeholder="<?php esc_html_e( 'Type Your Destinations', 'whbm' ) ?>"
                                               autocomplete="off">
                                    </div>
                                </div>
                                <div class="whbmt_form__item_off_left">
                                    <div class="whbmt_form__item_datepicker_off_left">
                                        <input type="text" name="checkin_display" class="whbmt_datepicker"
                                               placeholder="<?php esc_html_e( 'Checkin - Checkout', 'whbm' ) ?>"
                                               id="daterange" value="" autocomplete="off">
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
                            <div class="whbmt_package_catagory">
                                <div class="whbmt_catagory border_top">
                                    <h4>Tour Package Budget</h4>
                                    <div class="package_catagory_content">
                                        <div class="checkbox"><label><input type="checkbox" checked> $200 to
                                                $500</label></div>
                                        <div class="checkbox"><label><input type="checkbox"> $500 to $800</label></div>
                                        <div class="checkbox"><label><input type="checkbox"> $800 to $1000</label></div>
                                        <div class="checkbox"><label><input type="checkbox"> Above $1000</label></div>
                                    </div>
                                </div>
                                <div class="whbmt_catagory border_top">
                                    <h4>Hotel Star Rating</h4>
                                    <div class="package_catagory_content">
                                        <div class="checkbox"><label><input type="checkbox" checked> 3 Star</label>
                                        </div>
                                        <div class="checkbox"><label><input type="checkbox"> 4 Star</label></div>
                                        <div class="checkbox"><label><input type="checkbox"> 5 Star</label></div>
                                    </div>
                                </div>
                                <div class="whbmt_catagory border_top">
                                    <h4>Room facilities</h4>
                                    <div class="package_catagory_content">
                                        <div class="checkbox"><label><input type="checkbox" checked> Kitchen/kitchenette</label>
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
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-md-9">
                        <div class="package_listing">
                            <h4>United States: 49 propertice found</h4>
                            <ul class="nav nav-tabs" id="myTab" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="home-tab" data-toggle="tab" href="#tab1" role="tab">Top
                                        Hotel First</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="home-tab" data-toggle="tab" href="#tab2" role="tab">Low
                                        Price First</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="home-tab" data-toggle="tab" href="#tab3" role="tab">Top Star
                                        Rating</a>
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
															//write_log($global_price);
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
															echo wc_price( min( $price_array ), $curr_args );
															?></h3>
														<?php
														?>
                                                        <span class="room-qty">1 night, 2 adults</span>
                                                        <a href="<?php the_permalink() ?>" class="btn btn-default
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
                                </div>
                                <!-- End Tab-pane -->

                                <div class="tab-pane fade" id="tab6" role="tabpanel">
                                    <div class="whbmt_single_package">
                                        <div class="row">
                                            <div class="col-md-4 col-sm-6">
                                                <div class="package_content">
                                                    <img src="assets/img/package7.jpg">
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

                        <div class="pagination_nav">
                            <a href="#" class="pagination_btn"><i class="fa fa-angle-left"></i></a>
                            <a href="#" class="active">1</a>
                            <a href="#">2</a>
                            <a href="#">2</a>
                            <a href="#">3</a>
                            <a href="#">4</a>
                            <a href="#">7</a>
                            <a href="#">9</a>
                            <a href="#">...</a>
                            <a href="#" class="pagination_btn"><i class="fa fa-angle-right"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </section>


		<?php
		if ( $_SERVER['REQUEST_METHOD'] == 'GET' ) {
			$destination_search   = isset( $_GET['dest_name'] ) ? $_GET['dest_name'] : '';
			$check_in_out         = isset( $_GET['daterange'] ) ? $_GET['daterange'] : date_i18n( $date_format );
			$adult_qty            = isset( $_GET['adult_qty'] ) ? strip_tags( $_GET['adult_qty'] ) : '';
			$child_qty            = isset( $_GET['child_qty'] ) ? strip_tags( $_GET['child_qty'] ) : '';
			$check_in_out_explode = explode( 'to', $check_in_out );
			$check_in_out_explode = isset( $check_in_out_explode[1] ) ? $check_in_out_explode[1] : null;
			$check_in             = date_i18n( $date_format, strtotime( $check_in_out_explode[0] ) );
			$check_out            = date_i18n( $date_format, strtotime( $check_in_out_explode[1] ) );
			//print_r($destination_search);
			$booking_args = array(
				'post_type'     => 'hotel_booking_info',
				'post_per_page' => - 1,
				'meta_query'    => array(
					'key'   => 'whhbm_checkin_date',
					'value' => $check_in
				)
			);

			$booking_attendee = new WP_Query( $booking_args );
			//write_log($booking_attendee);

			//write_log( $destination_search );
			$args = array(
				'post_type'     => 'mage-room-pricing',
				'post_per_page' => - 1,
				'meta_query'    => array(
					'relation' => 'AND',
					array(
						'key'   => 'hotel_location',
						'value' => $destination_search
					)
				),
			);
			$loop = new WP_Query( $args );
			//write_log($loop);
			$hotel_id = array();
			foreach ( $loop->posts as $room ) {
				$hotel_id[] = get_post_meta( $room->ID, 'hotel_list', true );
			}

			$hotels = array_unique( $hotel_id );
			foreach ( $hotels as $_hotels ) {
				?>

                <div class="hotel-list">
                    <div class="single-hotel">
                        <div class="hotel-wrapper">
                            <div class="room-image-gallery hotel-gallery">
                                <a href="<?php echo get_the_post_thumbnail_url( $_hotels ); ?>"><?php echo get_the_post_thumbnail
									( $_hotels ); ?></a>
                            </div>
                            <div class="hotel-info">
                                <div class="hotel-address">
                                    <ul class="info-list">
                                        <li>
                                            <a href="<?php echo get_the_permalink( $_hotels ) . "?daterange=" . str_replace( ' ', '', $check_in_out ); ?>">
                                                <h4
                                                        id="hotel-title"><?php echo get_the_title
													( $_hotels );
													?></h4></a></li>
										<?php
										$hotel_type = get_the_terms( $_hotels, 'mage_hotel_type' );
										if ( is_array( $hotel_type ) || is_object( $hotel_type ) ) {
											foreach ( $hotel_type as $value ) {
												?>
                                                <li><span class="hotel-q-type"><?php echo $value->name ?></span></li>
											<?php }
										} ?>
										<?php
										$hotel_city = get_post_meta( $_hotels, 'city', true ); ?>
                                        <li><span class="dashicons dashicons-location"></span><?php echo $hotel_city
											?></li>
                                    </ul>
                                </div>
                                <div class="hotel-facilities-list">
                                    <ul class="">
										<?php
										$features_tax_data = get_the_terms( $_hotels, 'mage_hotel_cat' );
										//_log($features_tax_data);
										if ( is_array( $features_tax_data ) || is_object( $features_tax_data ) ) {
											foreach ( $features_tax_data as $tax_key => $value ) {
												$icat_desc = get_term_meta( $value->term_id, 'icon_field', true );
												?>
                                                <li><?php echo $value->name ?><i
                                                            class="feature-icon <?php echo $icat_desc ?>"
                                                            aria-hidden="true"></i>
                                                </li>
											<?php }
										} ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
				<?php
			}
		}
		$output = ob_get_clean();

		return $output;
	}

	public function whbm_single_hotel( $atts ) {
		$date_format = get_option( 'date_format' );
		global $magemain, $post;
		$atts = shortcode_atts( array(
			'id' => ''
		), $atts, 'whbm_single_hotel' );
		///write_log($post);
		ob_start();
		$post_id = $atts['id'];
		$loop    = new WP_Query(
			array(
				'post_type' => 'mage_hotel',
				'p'         => $post_id
			)
		);
//get value of Hotel Room And Pricing from custom post
		$args       = array(
			'post_type' => 'mage-room-pricing',
			'meta_key'  => 'hotel_list'
		);
		$hotel_post = get_posts( $args );
		while ( $loop->have_posts() ) {
			$loop->the_post();
			?>
            <section class="whbmt_packege_wrapper_area">
                <div class="container">
                    <div class="row">
                        <!-- Start Left-Side-Bar -->
                        <div class="col-12 col-md-3">
                            <div class="whbmt_wrapper_off_left">

                                <form id="whbmt_off_left_form1"
                                      action="<?php echo get_site_url() . '/hotel-search-list/'
								      ?>">
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
                                            <div class="checkbox"><label><input type="checkbox" checked> $200 to
                                                    $500</label></div>
                                            <div class="checkbox"><label><input type="checkbox"> $500 to $800</label>
                                            </div>
                                            <div class="checkbox"><label><input type="checkbox"> $800 to $1000</label>
                                            </div>
                                            <div class="checkbox"><label><input type="checkbox"> Above $1000</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="whbmt_catagory border_top">
                                        <h4>Hotel Star Rating</h4>
                                        <div class="package_catagory_content">
                                            <div class="checkbox"><label><input type="checkbox" checked> 3 Star</label>
                                            </div>
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
                                            <div class="checkbox"><label><input type="checkbox"> Private
                                                    bathroom</label>
                                            </div>
                                            <div class="checkbox"><label><input type="checkbox"> Air
                                                    conditioning</label>
                                            </div>
                                            <div class="checkbox"><label><input type="checkbox"> Balcony</label></div>
                                            <div class="checkbox"><label><input type="checkbox"> Flat-screen TV</label>
                                            </div>
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
											foreach ( $image_meta as $key => $value ) {
												$value = maybe_unserialize( $value );
												if ( is_array( $value ) || is_object( $value ) ) {
													foreach ( $value as $image_key => $attachment_id ) {
														$image     = wp_get_attachment_image( $attachment_id, 'full' );
														$image_url = wp_get_attachment_image_url( $attachment_id, 'full' );
														?>
                                                        <div id="thumb2" class="tab-pane fade show">
                                                            <a data-fancybox="img"
                                                               href="<?php echo $image_url ?>"><?php echo
																$image ?></a>
                                                        </div>
													<?php }
												}
											}
											//write_log(maybe_unserialize($image_meta));
											?>


                                        </div>
                                        <!-- Thumbnail Large Image End -->

                                        <!-- Thumbnail Image End -->
                                        <div class="product-thumbnail mt-15">
                                            <div class="thumb_menu bor_0 owl-carousel nav tabs-area" role="tablist">

												<?php
												foreach ( $image_meta as $key => $value ) {
													$value = maybe_unserialize( $value );
													if ( is_array( $value ) || is_object( $value ) ) {
														foreach ( $value as $image_key => $attachment_id ) {
															$image     = wp_get_attachment_image( $attachment_id, 'full' );
															$image_url = wp_get_attachment_image_url( $attachment_id, 'full' );
															?>
                                                            <a class="active" data-toggle="tab"
                                                               href="#thumb1"><?php echo
																$image ?></a>
														<?php }
													}
												}

												?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="whbmt_package_listing_content">
                                    <h4><?php the_title() ?></h4>
                                    <ul class="listing_details_location">
                                        <li>2 km from centre</li>
                                        <li>
                                            <i class="fa fa-map-marker"></i> <?php echo get_post_meta( get_the_ID(), 'address',
												true ); ?></li>
                                        <li><i class="fa fa-comments-o"></i> 1275 Reviews</li>
                                    </ul>
                                    <p><?php echo get_post_field( 'post_content', get_the_ID() ); ?></p>
                                </div>
                                <div class="row" id="listing_sub_content">
                                    <div class="col-md-2">
                                        <div class="single_listing_sub_content">
                                            <span>Check-in date</span>
                                            <h5>Sat 1 Jan 2020</h5>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="single_listing_sub_content">
                                            <span>Check-out date</span>
                                            <h5>Sat 3 Jan 2020</h5>
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
                                        <button class=" btn btn-default main_btn change_btn">Change Search</button>
                                    </div>
                                </div>

                                <div class="whbmt_room_order_summury_box">
                                    <table class="table table-bordered">
                                        <thead class="room_heading">
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
										$args       = array(
											'post_type' => 'mage-room-pricing',
											'meta_key'  => 'hotel_list'
										);
										$hotel_room = new WP_Query( $args );
										while ( $hotel_room->have_posts() ) {
											$hotel_room->the_post();
											$hotel_list = get_post_meta( get_the_ID(), 'hotel_list', true );
											if ( $hotel_list == $post_id ) {

												?>
                                                <tr class="whbmt_single_room_preview">
                                                    <td class="whbmt_room_title">
                                                        <div class="row">
                                                            <div class="col-md-7">
                                                                <div class="tab-content">
																	<?php
																	$room_img = get_post_meta( get_the_ID(), 'room_gallery' );
																	foreach ( $room_img as $un_key => $un_value ) {
																		$unserialize_image = maybe_unserialize( $un_value );
																		if ( is_array( $unserialize_image ) ) {
																			foreach ( $unserialize_image as $arr_key => $img_value ) {
																				$gallery_arr    = $img_value;
																				$room_image     = wp_get_attachment_image( $gallery_arr, array(
																					'50',
																					'50'
																				) );
																				$room_image_url = wp_get_attachment_image_url( $gallery_arr, array(
																					'450',
																					'450'
																				) );
																				?>
                                                                                <div id="room2" class="tab-pane fade">
                                                                                    <a data-fancybox="img"
                                                                                       href="<?php echo
																					   $room_image_url; ?>"><?php echo $room_image; ?></a>
                                                                                </div>
																			<?php }
																		}
																	}

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
																	$by_week             = array_merge( $sunday_price, $monday_price,
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
                                                                </div>
                                                                <div class="whbmt-room-title">
                                                                    <h5><?php the_title() ?></h5>
                                                                </div>
                                                                <!-- Thumbnail Large Image End -->

                                                                <!-- Thumbnail Image End -->
                                                                <div class="product-thumbnail mt-15">
                                                                    <div class="whbmt_room_thumb_menu owl-carousel nav tabs-area"
                                                                         role="tablist">
                                                                        <a class="active" data-toggle="tab"
                                                                           href="#room1"><img
                                                                                    src="assets/img/room/1.jpg"
                                                                                    alt="product-thumbnail"></a>
                                                                        <a data-toggle="tab" href="#room2"><img
                                                                                    src="assets/img/room/2.jpg"
                                                                                    alt="product-thumbnail"></a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="whbmt_room_person">
                                                        <ul>
															<?php
															$room_capacity = get_post_meta( get_the_ID(), 'room_capacity', true );
															for ( $i = 1; $i <= $room_capacity; $i ++ ) { ?>
                                                                <li>
                                                                    <img src="<?php echo plugin_dir_url( __FILE__ ) ?>../css/images/icon.png">
                                                                </li>
															<?php }
															?>
                                                        </ul>
                                                    </td>
                                                    <td class="whbmt_price_room">
                                                        <span class="active_color"><?php echo $price_value; ?></span>
                                                        <h6>2 night</h6>
                                                    </td>
                                                    <td class="whbmt_select_room_quantity">
                                                        <input type="number" value="1">
                                                    </td>
                                                    <td class="whbmt_room_cart_value">
                                                        <h5 class="mb-0">8 room for</h5>
                                                        <h2 class="mb-0">USD 525.00</h2>
                                                        <span>Including tax & vat</span>
                                                        <button class=" btn btn-default main_btn mt-2">Change Search
                                                        </button>
                                                        <ul class="mt-2">
                                                            <li>Free Cancelation</li>
                                                            <li>Best Rate</li>
                                                        </ul>
                                                    </td>
                                                </tr>
											<?php }
										}
										?>

                                        </tbody>
                                    </table>
                                </div>
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
                                                <div class="panel-heading" role="tab"
                                                     id="heading<?php echo $row_count; ?>">
                                                    <h4 class="panel-title">
                                                        <a role="button" data-toggle="collapse" data-parent="#accordion"
                                                           href="#collapse<?php echo $row_count; ?>"
                                                           aria-expanded="false"
                                                           aria-controls="collapse<?php echo $row_count; ?>">
															<?php echo $value['text_field'] ?>
                                                        </a>
                                                    </h4>
                                                </div>

                                                <div id="collapse<?php echo $row_count; ?>"
                                                     class="panel-collapse collapse" role="tabpanel"
                                                     aria-labelledby="heading<?php echo $row_count; ?>">
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
		<?php }
		$output = ob_get_clean();

		return $output;
	}

}

new Hotel_Shortcode_Hello();