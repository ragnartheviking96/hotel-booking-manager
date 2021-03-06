<?php
add_filter( 'woocommerce_account_menu_items', 'whbm_create_hotel_link', 40 );
function whbm_create_hotel_link( $menu_links ) {

	$menu_links = array_slice( $menu_links, 0, 5, true )
	              + array( 'hotel-vendor' => 'My Hotels' )
	              + array_slice( $menu_links, 5, null, true );
	$user       = wp_get_current_user();

	return $menu_links;
}

add_action( 'init', 'hotel_add_endpoint' );
function hotel_add_endpoint() {
	// WP_Rewrite is my Achilles' heel, so please do not ask me for detailed explanation
	add_rewrite_endpoint( 'hotel-vendor', EP_PAGES );
}

add_action( 'woocommerce_account_hotel-vendor_endpoint', 'whbm_create_hotel_content' );
function whbm_create_hotel_content() {
	$user = wp_get_current_user();
	$args = array(
		'post_type'      => array( 'mage_hotel' ),
		'posts_per_page' => - 1,
		'author'         => $user->ID
	);
	$loop = new WP_Query( $args ); ?>
    <div class="button-section"><a href="<?php echo get_site_url() ?>/create-hotel/" class="page-title-action">Add New
            Hotel</a></div>
    <div class="event-data">
        <table class="user-created-event">
            <tr>
                <th width="30%">Hotel Name</th>
                <th width="15%">Action / Edit</th>
                <th width="20%">Property Type</th>
                <th width="20%">Hotel Quality Type</th>
                <th width="15%">Total Order</th>

            </tr>
			<?php while ( $loop->have_posts() ) {
				$loop->the_post(); ?>
                <tr>
                    <td><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></td>
                    <td>
                        <a href="<?php echo get_site_url() . '/create-hotel/?post_id=' . get_the_ID() ?>"><?php esc_html_e( 'Edit', 'whbm' ); ?></a>
                        <a href="<?php /*wp_trash_post(get_the_ID(), true);*/ ?>"><?php esc_html_e( '/ Delete', 'whbm' ); ?></a>
                    </td>
                    <td><?php
						$mage_property_type = get_the_terms( get_the_ID(), 'mage_property_type' );
						if ( is_array( $mage_property_type ) || is_object( $mage_property_type ) ) {
							foreach ( $mage_property_type as $key => $value ) {
								echo '<span>' . $value->name . '</span>';
							}
						}
						?></td>
                    <td>
						<?php
						$mage_hotel_type = get_the_terms( get_the_ID(), 'mage_hotel_type' );
						if ( is_array( $mage_hotel_type ) || is_object( $mage_hotel_type ) ) {
							foreach ( $mage_hotel_type as $key => $value ) {
								echo '<span>' . $value->name . '</span>';
							}
						}
						?>
                    </td>
                    <td></td>

                </tr>

			<?php } ?>
        </table>
    </div>
	<?php
}


add_shortcode( 'add-hotel-vendor', 'add_hotel' );

function add_hotel( $atts ) {
	$atts = shortcode_atts( array(), $atts, 'add-hotel-vendor' );
	ob_start();
	$post_id = isset( $_GET['post_id'] ) ? $_GET['post_id'] : '';
	if ( isset( $post_id ) && ! empty( $post_id ) ) {
		$args = array(
			'post_type' => 'mage_hotel',
			'p'         => $post_id,
		);
		$loop = new WP_Query( $args );
		//echo $loop->post_count;
		if ( $loop->post_count != 0 ) {
			while ( $loop->have_posts() ) {
				$loop->the_post();
				if ( 'POST' == $_SERVER['REQUEST_METHOD'] && ! empty( $_POST['action'] ) && $_POST['action'] == "new_post" && (
						isset( $_GET['post_id'] ) && ! empty( $_GET['post_id'] ) ) ) {
// Do some minor form validation to make sure there is content
					$post_to_edit = get_post( (int) $_GET['post_id'] );

					// Add the content of the form to $post_to_edit array
					$post_to_edit->post_title   = $_POST['post-title'];
					$post_to_edit->post_content = $_POST['content'];
					$hotel_quality_terms = isset( $_POST['mage_hotel_type'] ) ? $_POST['mage_hotel_type'] : '';
					//save the edited post and return its ID

					$pid = wp_update_post( $post_to_edit );
					//$ex_array = array('1 Star', '3 Star');
					wp_set_object_terms($pid, $hotel_quality_terms, 'mage_hotel_type');

					if ( is_wp_error( $pid ) ) {
						echo $pid->get_error_message();
					} else {
						echo '<div id="message" class="updated notice notice-success is-dismissible"><p>' . esc_html_e( 'Post Updated Successfully. ', 'whbm' ) . '<a href="">View post</a></p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>';
					}
				}
				?>
                <div class="create-product-vendor-section">
                    <div class="vendor-button-section">
                        <button><a href="<?php echo get_site_url() ?>/my-account/hotel-vendor/"><p>Back To Your Vendor
                                    Profile</p></a></button>
                        <button><a href="<?php echo get_site_url() ?>/create-hotel/"><p>Create New Hotel</p></a>
                        </button>
                        <button><a href="<?php the_permalink(); ?>" target=_blank>
                                <p><?php esc_html_e( 'View Hotel', 'whbm' ); ?></p></a></button>
                    </div>
                    <div class="hotel-form-section">
                        <form action="" method="post">

                            <ul class="form-style-1">
                                <li><label><?php esc_html_e( 'Property Title', 'whbm' ); ?><span
                                                class="required">*</span></label><input type="text" name="post-title"
                                                                                        class="post-title"
                                                                                        id="posttitle"
                                                                                        value="<?php the_title() ?>">
                                </li>
                                <li>
                                    <label><?php esc_html_e( 'Property Description', 'whbm' ); ?><span
                                                class="required">*</span></label>
                                    <textarea name="content" placeholder="Enter Hotel Details..."
                                              value="<?php echo get_post_field( 'post_content', get_the_ID() );
									          ?>w"><?php echo get_post_field( 'post_content', get_the_ID() );
										?></textarea>
                                </li>
                                <li><label><?php esc_html_e( 'Street', 'whbm' ); ?></label><input type="text"
                                                                                                  name="address"
                                                                                                  class="address"
                                                                                                  id="address"
                                                                                                  value="<?php
								                                                                  $address = get_post_meta( get_the_ID(), 'address', true );
								                                                                  echo $address;
								                                                                  ?>">
                                </li>
                                <li><label><?php esc_html_e( 'City', 'whbm' ); ?></label><input type="text" name="city"
                                                                                                class="city" id="city"
                                                                                                value="<?php
								                                                                $city = get_post_meta( get_the_ID(), 'city', true );
								                                                                echo $city;
								                                                                ?>">
                                </li>
                                <li><label><?php esc_html_e( 'State', 'whbm' ); ?></label><input type="text"
                                                                                                 name="state"
                                                                                                 class="state"
                                                                                                 id="state"
                                                                                                 value="<?php
								                                                                 $state = get_post_meta( get_the_ID(), 'state', true );
								                                                                 echo $state;
								                                                                 ?>">
                                </li>

                                <li><label><?php esc_html_e( 'Country', 'whbm' ); ?></label><input type="text"
                                                                                                   name="country"
                                                                                                   class="country"
                                                                                                   id="country"
                                                                                                   value="<?php
								                                                                   $country = get_post_meta( get_the_ID(), 'country', true );
								                                                                   echo $country;
								                                                                   ?>">
                                </li>

                                <li>
                                    <label for="hotel_rules"><?php esc_html_e( 'Rules and don\'t or do,s', 'whbm' ); ?>
                                        <span
                                                class="required">*</span></label>
                                    <textarea name="hotel_rules" placeholder="Enter Hotel Rules..."
                                              class="hotel_rules" id="hotel_rules"
                                              value="<?php echo get_post_meta( get_the_ID(), 'hotel_rules', true ); ?>"><?php echo get_post_meta( get_the_ID(), 'hotel_rules', true ); ?></textarea>
                                </li>

                                <li>
                                    <label for="price_starts_from"><?php esc_html_e( 'Minimum Price Starts from', 'whbm'
										); ?><span
                                                class="required">*</span></label>
                                    <input type="number" name="min_price_starts" class="price_starts_from"
                                           id="price_starts_from"
                                           placeholder="Write down your Price"
                                           value="<?php echo get_post_meta( get_the_ID(), 'min_price_starts', true ); ?>"/>
                                </li>

                                <li>
                                    <div class="ex_feat">
                                        <label for=""><?php esc_html_e( 'Add Extra Features', 'whbm' ); ?></label><span
                                                class="time_input">
                                            <span class="extra_feat_fields">
                                            <?php

                                            $ex_features = maybe_unserialize( get_post_meta( get_the_ID(), 'extra_features',
	                                            true ) );

                                            $last_count = isset( $ex_features['ex_feature_last_count'] ) ? $ex_features['ex_feature_last_count'] : 0;

                                            if ( ! is_array( $ex_features ) ) {
	                                            $ex_features = array();
                                            }


                                            foreach ( $ex_features as $key => $time ) {
	                                            if ( "ex_feature_last_count" != $key ) {
		                                            ?>
                                                    <p>
                                                        <label for="extra_features_text">Feature Name</label>
                        <input type="text" id="extra_features_text" name="extra_features[<?php echo $key;
                        ?>][text_field]"
                               value="<?php echo $time['text_field']; ?>"
                               class=" time_field"> -

                        <label for="extra_features_number">Price</label>
                        <input type="number" id="extra_features_number" name="extra_features[<?php echo $key;
                        ?>][number_field]" value="<?php
                        echo $time['number_field']; ?>" class=" time_field">


                        <a href="" class="button ex_feat_remove">
                            <span class="dashicons dashicons-trash"
                                  style="margin-top: 3px;color: red;"></span>Remove</a>
                    </p>
	                                            <?php }
                                            } ?>
                                                <a href="#" class="button ex_feat_add"><span
                                                            class="dashicons dashicons-plus-alt"
                                                            style="margin-top:3px;color: #00bb00;"></span>Add</a>
                </span>

                                    </div>
                                    <input type="hidden" name="extra_features[ex_feature_last_count]"
                                           class="ex_feature_last_count" value="<?php echo $last_count; ?>">
                                </li>

                                <li>
                                    <div class="hotel_faq">
                                        <label for=""><?php esc_html_e( 'Add FAQ', 'whbm' ); ?></label>
                                        <span class="hotel_faq_input">
                                    <span class="faq_fields">
                                        <?php
                                        $ex_features = maybe_unserialize( get_post_meta( get_the_ID(), 'hotel_faq',
	                                        true ) );

                                        $last_count = isset( $ex_features['faq_last_count'] ) ? $ex_features['faq_last_count'] : 0;

                                        if ( ! is_array( $ex_features ) ) {
	                                        $ex_features = array();
                                        }


                                        foreach ( $ex_features as $key => $time ) {
	                                        if ( "faq_last_count" != $key ) {
		                                        ?>
                                                <p>
                                                        <label for="hotel_faq_text">FAQ Title</label>
                        <input type="text" id="hotel_faq_text"
                               name="hotel_faq[<?php echo $key; ?>][text_field]"
                               value="<?php echo $time['text_field']; ?>"
                               class="faq_text_field">
                        <label for="hotel_faq_number">Possible Answer</label>
                                                        <input type="text" id="hotel_faq_number"
                                                               name="hotel_faq[<?php echo $key; ?>][number_field]"
                                                               value="<?php echo $time['number_field']; ?>"
                                                               class="faq_number_field">

                        <a href="" class="button faq_remove">
                            <span class="dashicons dashicons-trash"
                                  style="margin-top: 3px;color: red;"></span>Remove</a>
                    </p>
	                                        <?php }
                                        } ?>
                                        <a href="#" class="button faq_add"><span class="dashicons dashicons-plus-alt"
                                                                                 style="margin-top:3px;color: #00bb00;"></span>Add</a>
                </span>

                                    </div>
                                    <input type="hidden" name="hotel_faq[faq_last_count]"
                                           class="faq_last_count" value="<?php echo $last_count; ?>">
                                </li>

                                <li>
                            <h4>Hotel Quality Rating</h4>
                            <?php
                            $mage_hotel_type = get_terms([
                                    'taxonomy' => 'mage_hotel_type',
                                    'hide_empty' => false
                                    ]);
                            $saved_terms = get_the_terms( get_the_ID(), 'mage_hotel_type', false );

                                if ( is_array( $mage_hotel_type ) || is_object( $mage_hotel_type ) ) {
                                    $i = 1;
                                foreach ($mage_hotel_type as $key=>$value){?>
                                    <label for="hotel_terms_<?php echo $i?>"><input type="checkbox" name="mage_hotel_type[]"
                                    id="hotel_terms_<?php echo $i?>" value="<?php echo $value->name?>"><?php echo
                                    $value->name?></label>
                            <?php
                                $i++;
                                }
                                }
                            ?>

                        </li>

                                <li>
                                    <input type="submit" name="hotel-vendor"
                                           value="<?php esc_html_e( 'Update', 'whbm' ); ?>"
                                           class="hotel-vendor">
                                    <input type="hidden" name="action" value="new_post"/>
									<?php wp_nonce_field( 'new-post' ); ?>
                                </li>

                            </ul>
                        </form>

                    </div>
                </div>
				<?php

			}
		} else {
			echo '<p>' . esc_html__( 'sorry no post\'s found', 'whbm' ) . '</p>';
		}

	} else {
		?>
        <div class="create-product-vendor-section">
            <div class="vendor-button-section">
                <button><a href="<?php echo get_site_url() ?>/my-account/hotel-vendor/"><p>Back To Your Vendor
                            Profile</p></a></button>
                <button><a href="<?php echo get_site_url() ?>/create-hotel/"><p>Create New Hotel</p></a></button>
            </div>
            <div class="hotel-form-section">
                <form action="" method="post">

                    <ul class="form-style-1">
                        <li><label><?php esc_html_e( 'Property Title', 'whbm' ); ?><span
                                        class="required">*</span></label><input type="text"
                                                                                name="post-title" class="post-title"
                                                                                id="posttitle" value="">
                        </li>
                        <li>
                            <label><?php esc_html_e( 'Property Description', 'whbm' ); ?><span
                                        class="required">*</span></label>
                            <textarea name="content" placeholder="Enter Hotel Details..." value=""></textarea>
                        </li>
                        <li><label><?php esc_html_e( 'Street', 'whbm' ); ?></label><input type="text" name="address"
                                                                                          class="address" id="address"
                                                                                          value="">
                        </li>
                        <li><label><?php esc_html_e( 'City', 'whbm' ); ?></label><input type="text" name="city"
                                                                                        class="city"
                                                                                        id="city" value="">
                        </li>
                        <li><label><?php esc_html_e( 'State', 'whbm' ); ?></label><input type="text" name="state"
                                                                                         class="state" id="state"
                                                                                         value="">
                        </li>
                        <li><label><?php esc_html_e( 'Country', 'whbm' ); ?></label><input type="text" name="country"
                                                                                           class="country" id="country"
                                                                                           value="">
                        </li>

                        <li>
                            <label><?php esc_html_e( 'Rules and don\'t or do,s', 'whbm' ); ?><span
                                        class="required">*</span></label>
                            <textarea name="hotel_rules" placeholder="Enter Hotel Rules" value=""></textarea>
                        </li>

                        <li>
                            <div class="ex_feat">
                                <label for=""><?php esc_html_e( 'Add Extra Features', 'whbm' ); ?></label><span
                                        class="time_input">
                                            <span class="extra_feat_fields">
                                            <?php

                                            $ex_features = maybe_unserialize( get_post_meta( get_the_ID(), 'extra_features',
	                                            true ) );
                                            $last_count = isset( $ex_features['ex_feature_last_count'] ) ?
                                                $ex_features['ex_feature_last_count'] : 0;

                                            if ( ! is_array( $ex_features ) ) {
	                                            $ex_features = array();
                                            }
                                            ?>
                                                <a href="#" class="button ex_feat_add"><span
                                                            class="dashicons dashicons-plus-alt"
                                                            style="margin-top:3px;color: #00bb00;"></span>Add</a>
                </span>

                            </div>
                            <input type="hidden" name="extra_features[ex_feature_last_count]"
                                   class="ex_feature_last_count" value="<?php echo $last_count; ?>">
                        </li>

                        <li>
                            <div class="hotel_faq">
                                <label for=""><?php esc_html_e( 'Add FAQ', 'whbm' ); ?></label>
                                <span class="hotel_faq_input">
                                    <span class="faq_fields">
                                        <?php
                                        $ex_features = maybe_unserialize( get_post_meta( get_the_ID(), 'hotel_faq',
	                                        true ) );

                                        $last_count = isset( $ex_features['faq_last_count'] ) ? $ex_features['faq_last_count'] : 0;

                                        if ( ! is_array( $ex_features ) ) {
	                                        $ex_features = array();
                                        }
                                        ?>
                                        <a href="#" class="button faq_add"><span class="dashicons dashicons-plus-alt"
                                                                                 style="margin-top:3px;color: #00bb00;"></span>Add</a>
                </span>

                            </div>
                            <input type="hidden" name="hotel_faq[faq_last_count]"
                                   class="faq_last_count" value="<?php echo $last_count; ?>">
                        </li>

                        <li>
                            <h4>Hotel Quality Rating</h4>
                            <?php
                            $mage_hotel_type = get_terms([
                                    'taxonomy' => 'mage_hotel_type',
                                    'hide_empty' => false
                                    ]);
                                if ( is_array( $mage_hotel_type ) || is_object( $mage_hotel_type ) ) {
                                    $i = 1;
                                foreach ($mage_hotel_type as $key=>$value){?>
                                    <label for="hotel_terms_<?php echo $i?>"><input type="checkbox" name="tax_input[mage_hotel_type][]"
                                    id="hotel_terms_<?php echo $i?>" value="<?php echo $value->term_id?>"><?php echo
                                    $value->name?></label>
                            <?php
                                $i++;
                                }
                                }
                            ?>

                        </li>

                        <li>
                            <input type="submit" name="hotel-vendor"
                                   value="<?php esc_html_e( 'Create New Property', 'whbm' ); ?>"
                                   class="hotel-vendor">
                            <input type="hidden" name="action" value="new_post"/>
							<?php wp_nonce_field( 'new-post' ); ?>
                        </li>

                    </ul>
                </form>

            </div>
        </div>
	<?php }
	if ( 'POST' == $_SERVER['REQUEST_METHOD'] && ! empty( $_POST['action'] ) && $_POST['action'] == "new_post" &&
	     ( ! isset( $_GET['post_id'] ) && empty( $_GET['post_id'] ) ) ) {

		// Do some minor form validation to make sure there is content
		if ( isset ( $_POST['post-title'] ) ) {
			$title = $_POST['post-title'];
		} else {
			echo 'Please enter a  title';
		}
		if ( isset ( $_POST['content'] ) ) {
			$description = $_POST['content'];
		} else {
			echo 'Please enter the content';
		}
		$address = isset( $_POST['address'] ) ? $_POST['address'] : '';
		$city    = isset( $_POST['city'] ) ? $_POST['city'] : '';
		$state   = isset( $_POST['state'] ) ? $_POST['state'] : '';
		$country = isset( $_POST['country'] ) ? $_POST['country'] : '';
		$extra_features = isset( $_POST['extra_features'] ) ? $_POST['extra_features'] : '';
		$hotel_faq = isset( $_POST['hotel_faq'] ) ? $_POST['hotel_faq'] : '';
		$hotel_faq = isset( $_POST['hotel_faq'] ) ? $_POST['hotel_faq'] : '';
		$hotel_quality_terms = isset( $_POST['tax_input'] ) ? $_POST['tax_input'] : '';
		// Add the content of the form to $post as an array
		$new_post = array(
			'post_title'   => $title,
			'post_content' => $description,
			'post_status'  => 'pending',           // Choose: publish, preview, future, draft, etc.
			'post_type'    => 'mage_hotel'  //'post',page' or use a custom post type if you want to
		);
       /* echo '<pre>';
		print_r($hotel_quality_terms);
		die();*/
		//save the new post
		$pid = wp_insert_post( $new_post );
		add_post_meta( $pid, 'address', $address, true );
		add_post_meta( $pid, 'city', $city, true );
		add_post_meta( $pid, 'state', $state, true );
		add_post_meta( $pid, 'country', $country, true );
		add_post_meta( $pid, 'extra_features', $extra_features );
		add_post_meta( $pid, 'hotel_faq', $hotel_faq );
		wp_set_post_terms($pid, $hotel_quality_terms, 'mage_hotel_type');
		if ( is_wp_error( $pid ) ) {
			echo $pid->get_error_message();
		} else {
			echo '<div id="message" class="updated notice notice-success is-dismissible"><p>Post published. <a href="' . $pid . '">View post</a></p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>';
		}
	}
	return ob_get_clean();
}