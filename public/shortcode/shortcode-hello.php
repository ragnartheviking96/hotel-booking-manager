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
		add_shortcode( 'whbm-single-hotel', array( $this, 'whbm_single_hotel' ) );
		add_shortcode( 'whbm-hotel-search-form', array( $this, 'whbm_hotel_search_form' ) );
		add_action( 'wp_ajax_ajax_whbm_hotel_list', array( $this, 'ajax_whbm_hotel_list' ) );
		add_action( 'wp_ajax_nopriv_ajax_whbm_hotel_list', array( $this, 'ajax_whbm_hotel_list' ) );
		//add_shortcode('ajax_whbm_hotel_list', array( $this, 'ajax_whbm_hotel_list'));
	}

	public function ajax_whbm_hotel_list() {

		$hotel_facilities = $_GET['hotel_facilities'];
		$hotel_type       = $_GET['hotel_type'];
		$paged            = get_query_var( "paged" ) ? get_query_var( "paged" ) : 1;

		if ( ! isset( $hotel_facilities ) && ! isset( $hotel_type ) ) {
			$args = array(
				'post_type'      => 'mage_hotel',
				'post_status'    => 'publish',
				'posts_per_page' => 2,
				'paged'          => $paged
			);
		} else {

			$args = array(
				'post_type'      => 'mage_hotel',
				'post_status'    => 'publish',
				'posts_per_page' => 2,
				'paged'          => $paged,
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
									if ( $hotel_id == $selected_hotel ) {
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
                                <span class="room-qty">1 night, 2 adults</span>
                                <a href="<?php echo get_the_permalink( $hotel_id ) ?>" class="btn btn-default
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
		global $wchbm, $magemain;;
		$atts = shortcode_atts( array(
			'view'     => 'list',
			'location' => '',
			'type'     => ''
		), $atts, 'hotel-list' );
		ob_start();
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
                    <!-- Start Left-Side-Bar -->
                    <div class="d-flex">
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
                        <div class="off_right">
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
											                    echo wc_price( min( $price_array ), $curr_args );
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
                                <a href="#" class="pagination_btn"><i class="fa fa-angle-left"></i></a>
			                    <?php echo paginate_links( $pargs ); ?>
                                <a href="#" class="pagination_btn"><i class="fa fa-angle-right"></i></a>
                            </div>
                        </div>
                    </div>
        </section>
		<?php
		return ob_get_clean();
	}// end of mage_shortcode_hello

	/**
	 * @return string
	 */
	public function whbm_hotel_search_form( $atts ) {
		global $wchbm, $magemain;;
		$defaults = array();
		$params   = shortcode_atts( $defaults, $atts );
		ob_start();
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
								<?php echo paginate_links( $pargs ); ?>
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
            </section>
			<?php
		}
		$content = ob_get_clean();

		return $content;
		//return ob_get_clean();
	}// end of whbm_hotel_search_form method

	public function whbm_single_hotel( $atts ) {
		$date_format = get_option( 'date_format' );
		global $magemain, $post;
		$atts = shortcode_atts( array(
			'id' => ''
		), $atts, 'whbm_single_hotel' );
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
											foreach ( $image_meta as $key => $value ) {
												$value = maybe_unserialize( $value );
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
											?>

                                        </div>
                                        <!-- Thumbnail Large Image End -->

                                        <!-- Thumbnail Image End -->
                                        <div class="product-thumbnail mt-15">
                                            <div class="thumb_menu bor_0 owl-carousel nav tabs-area owl-loaded"
                                                 role="tablist">


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
                                                            <div class="owl-item"
                                                                 style="width: 65.909px; margin-right: 10px;"><a
                                                                        class="show" data-toggle="tab"
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
                                    <h4><?php the_title(); ?></h4>
                                    <ul class="listing_details_location">
                                        <li><?php ?>2 km from centre</li>
                                        <li>
                                            <i class="fa fa-map-marker"></i> <?php echo get_post_meta( get_the_ID(), 'address',
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

                                        <div class="col-md-4">
                                            <div class="single_listing_sub_content">
                                                <label for="daterange">Check in-out date</label>
                                                <input type="text" name="daterange" class="form-control whbmt_datepicker
                                        custom_whbm" id="daterange"
                                                       placeholder="<?php esc_html_e( 'Checkin & Checkout Date' ); ?>"
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

                                        <div class="col-md-4">
                                            <button type="submit" class=" btn btn-default main_btn change_btn">Change
                                                Search
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
													     $magemain->get_booked_room_count( $check_in, $check_out, $post_id, get_the_ID() );
													?>
                                                    <tr class="whbmt_single_room_preview">
                                                        <td class="whbmt_room_title">
                                                            <div class="row">
                                                                <div class="room_type_name_sec">
                                                                    <h5 class="room-heading"><?php the_title() ?></h5>
                                                                    <input type="hidden"
                                                                           value="<?php echo get_the_ID() ?>"
                                                                           name="room_id[]">
                                                                    <input type="hidden"
                                                                           value="<?php echo the_title() ?>"
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
                                                                                            data-fancybox="img"
                                                                                            href="<?php echo
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
                                                                       id="room_cap"
                                                                       value="<?php echo $room_capacity; ?>">
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
                                                            <span class="active_color"><?php echo wc_price( $magemain->get_room_price(get_the_ID()), $curr_args );; ?></span>
                                                            <input type="hidden" name="hotel_room_price[]"
                                                                   value="<?php echo $magemain->get_room_price(get_the_ID()); ?>">
                                                            <p class="room-price" hidden><?php echo $magemain->get_room_price(get_the_ID());; ?></p>
                                                            <h6>Per night</h6>
                                                        </td>
                                                        <td class="whbmt_select_room_quantity">
															<?php
															if ( $f != 0 ) {
																?>
                                                                <p>
                                                                    <input class="room-quantity-number" type="number"
                                                                           min="0"
                                                                           max="<?php echo get_post_meta( get_the_ID(), 'room_quantity', true ) - $magemain->get_booked_room_count
																		       ( $check_in, $check_out, $post_id, get_the_ID() ); ?>"
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
                                                            <td rowspan="<?php echo $data_count; ?>">
                                                                <input type="hidden" name="daterange" class="daterange"
                                                                       id="" placeholder="Type Your Date"
                                                                       autocomplete="off" required
                                                                       value="<?php echo $check_in_out; ?>">
                                                                <input type="hidden" name="final_price" id="final_price"
                                                                       value="0">
                                                                <input type="hidden" name="total_day_stay"
                                                                       id="total_day_stay"
                                                                       value="<?php echo $total_stay ?>">
                                                                <div class="whbmt_room_order_add_to_cart">
                                                                    <h5 class="mb-0"><span id="total-room"></span> room
                                                                        for</h5>
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
                                                    <div class="panel-heading" role="tab"
                                                         id="heading<?php echo $row_count; ?>">
                                                        <h4 class="panel-title">
                                                            <a role="button" data-toggle="collapse"
                                                               data-parent="#accordion"
                                                               href="#collapse<?php echo $row_count; ?>"
                                                               aria-expanded="false"
                                                               aria-controls="collapse<?php echo $row_count; ?>">
																<?php echo $value['text_field'] ?>
                                                            </a>
                                                        </h4>
                                                    </div>

                                                    <div id="collapse<?php echo $row_count; ?>"
                                                         class="panel-collapse collapse"
                                                         role="tabpanel"
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
								<?php } ?>
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