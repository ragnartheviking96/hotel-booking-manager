<?php
if ( ! defined( 'ABSPATH' ) ) {
	die;
} // Cannot access pages directly.
class Mage_Cpt {

	public function __construct() {
		add_action( 'init', array( $this, 'register_cpt' ) );
		add_action( 'init', array( $this, 'hotel_room_pricing_cpt' ) );
	}


	public function register_cpt() {
		$labels = array(
			'name'          => esc_html__( 'Hotel', 'whbm' ),
			'singular_name' => esc_html__( 'Hotel', 'whbm' ),
			'add_new'       => esc_html__( 'Add New Hotel', 'whbm' ),
			'add_new_item'  => esc_html__( 'Add New Hotel', 'whbm' ),
			'edit_item'     => esc_html__( 'Edit Hotel', 'whbm' ),
			'new_item'      => esc_html__( 'Add New', 'whbm' )
		);
		$args   = array(
			'public'        => true,
			'labels'        => $labels,
			'menu_icon'     => 'dashicons-admin-home',
			'show_in_rest'  => true,
			'show_ui '      => true,
			'show_in_menu ' => true,
			'supports'      => array( 'title', 'editor', 'thumbnail', 'tag' )
		);
		register_post_type( 'mage_hotel', $args );

	}

	public function hotel_room_pricing_cpt() {
		$labels = array(
			'name'          => esc_html__( 'Hotel Room And Prcing', 'whbm' ),
			'singular_name' => esc_html__( 'Room And Pricing', 'whbm' ),
			'add_new'       => esc_html__( 'Add New Room And Pricing', 'whbm' ),
			'add_new_item'  => esc_html__( 'Add New Room And Pricing', 'whbm' ),
			'edit_item'     => esc_html__( 'Edit Room And Pricing', 'whbm' ),
			'new_item'      => esc_html__( 'Add New', 'whbm' )
		);
		$args   = array(
			'public'       => true,
			'labels'       => $labels,
			'menu_icon'    => 'dashicons-layout',
			'show_in_rest' => true,
			'supports'     => array( 'title', 'editor', 'thumbnail', 'tag' ),
			'show_in_menu' => 'edit.php?post_type=mage_hotel',
		);
		register_post_type( 'mage-room-pricing', $args );
	}

}

new Mage_Cpt();