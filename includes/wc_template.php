<?php


function hotel_set_go_to_vendor_dashboard_btn() {

	/*if ( ! dokan_is_user_seller( get_current_user_id() ) ) {
		return;
	}*/

	echo '<p><a href="%s" class="dokan-btn dokan-btn-theme vendor-dashboard" >'.esc_html__('Go Hotel Vendor Dashboard', 'whbm').'</a></p>';
}

add_action( 'woocommerce_account_dashboard', 'hotel_set_go_to_vendor_dashboard_btn' );