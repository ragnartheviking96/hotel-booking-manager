<?php
if ( ! defined( 'ABSPATH' ) ) { die; } // Cannot access pages directly.

class WHBM_Tax{
	public function __construct(){
		add_action("init",array($this,"whbm_tax_init"),10);
	}
	public function whbm_tax_init(){
		$labels = array(
			'singular_name'              => _x( 'Hotel Feature','whbm' ),
			'name'                       => _x( 'Hotel Features','whbm' ),
			'add_new_item' => esc_html__('Add New Features', 'whbm'),
			'new_item_name' => esc_html__('Add New Features', 'whbm')
		);

		$args = array(
			'hierarchical'          => true,
			"public" 				=> true,
			'labels'                => $labels,
			'show_ui'               => true,
			'show_admin_column'     => true,
			'update_count_callback' => '_update_post_term_count',
			'query_var'             => true,
			'rewrite'               => array( 'slug' => 'hotel-cat' )
		);
		register_taxonomy('mage_hotel_cat', 'mage_hotel', $args);
		$labels = array(
			'singular_name'              => _x( 'Hotel Type','whbm' ),
			'name'                       => _x( 'Hotel Type','whbm' ),
			'add_new_item' => esc_html__('Add New Type', 'whbm'),
			'new_item_name' => esc_html__('Add New Type', 'whbm')
		);

		$args = array(
			'hierarchical'          => true,
			"public" 				=> true,
			'labels'                => $labels,
			'show_ui'               => true,
			'show_admin_column'     => true,
			'update_count_callback' => '_update_post_term_count',
			'query_var'             => true,
			'rewrite'               => array( 'slug' => 'hotel-type' )
		);
		register_taxonomy('mage_hotel_type', 'mage_hotel', $args);
	}
}
new WHBM_Tax();