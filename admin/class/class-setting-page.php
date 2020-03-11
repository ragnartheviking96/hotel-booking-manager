<?php
/*
* @Author 		pickplugins
* Copyright: 	pickplugins.com
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}  // if direct access


class WHBMSettingPage {
	public function __construct() {
		$this->settings_page();
	}
	
	public function whbm_set_page_settings( $gen_settings, $translation_settings, $pdf_setttings ) {
		
		$default = array(
			'panelGroup-10' => $gen_settings,
			'panelGroup-11' => $translation_settings,
			'panelGroup-12' => $pdf_setttings,
		);
		
		return apply_filters( 'whbm_settings_array', $default );
	}
	
	public function settings_page() {
		
		
		$gen_settings = array(
			'page_nav'      => __( '<i class="fas fa fa-cog"></i> General Settings', 'whbm' ),
			'priority'      => 10,
			'page_settings' => array(
				
				'section_4' => array(
					'description' => __( 'This is section details', 'whbm' ),
					'options'     => array(),
				),
			),
		);
		
		
		$translation_settings = array(
			'page_nav'      => __( '<i class="fas fa fa-cog"></i> Translation Settings', 'whbm' ),
			'priority'      => 10,
			'page_settings' => array(
				
				'section_4' => array(
					'description' => __( 'This is section details', 'whbm' ),
					'options'     => array(
						array(
							'id'      => 'whbm_buy_ticket_text',
							'title'   => __( 'BUY TICKET', 'whbm' ),
							'details' => __( 'Enter the text which you want to display as To Search form page.',
								'whbm' ),
							'type'    => 'text',
							'default' => 'BUY TICKET',
						),
						array(
							'id'      => 'whbm_from_text',
							'title'   => __( 'From', 'whbm' ),
							'details' => __( 'Enter the text which you want to display as To Search form page.',
								'whbm' ),
							'type'    => 'text',
							'default' => 'From:',
						),
						array(
							'id'      => 'whbm_to_text',
							'title'   => __( 'To:', 'whbm' ),
							'details' => __( 'Enter the text which you want to display as To Search form page.',
								'whbm' ),
							'type'    => 'text',
							'default' => 'To:',
						),
						
						array(
							'id'      => 'whbm_date_of_journey_text',
							'title'   => __( 'Date of Journey:', 'whbm' ),
							'details' => __( 'Enter the text which you want to display as Date of Journey Search form page.',
								'whbm' ),
							'type'    => 'text',
							'default' => 'Date of Journey:',
						),
						
						array(
							'id'      => 'whbm_return_date_text',
							'title'   => __( 'Return Date:', 'whbm' ),
							'details' => __( 'Enter the text which you want to display as Date of Journey Search form page.',
								'whbm' ),
							'type'    => 'text',
							'default' => 'Return Date:',
						),
						
						array(
							'id'      => 'whbm_one_way_text',
							'title'   => __( 'One Way', 'whbm' ),
							'details' => __( 'Enter the text which you want to display as One Way Search form page.',
								'whbm' ),
							'type'    => 'text',
							'default' => 'One Way',
						),
						
						array(
							'id'      => 'whbm_return_text',
							'title'   => __( 'Return', 'whbm' ),
							'details' => __( 'Enter the text which you want to display as Return Search form page.',
								'whbm' ),
							'type'    => 'text',
							'default' => 'Return',
						),
					),
				),
			),
		);
		
		$pdf_setttings = apply_filters( 'pdf_email_settings', array() );
		
		
		$args         = array(
			'add_in_menu'     => true,
			'menu_type'       => 'sub',
			'menu_name'       => __( 'Settings', 'whbm' ),
			'menu_title'      => __( 'Settings', 'whbm' ),
			'page_title'      => __( 'Settings', 'whbm' ),
			'menu_page_title' => __( 'Settings', 'whbm' ),
			'capability'      => "manage_options",
			'cpt_menu'        => "edit.php?post_type=mage_hotel",
			'menu_slug'       => "whbm-hotel-manager-settings",
			'option_name'     => "whbm_hotel_settings",
			'menu_icon'       => "dashicons-iwhbm-filter",
			'item_name'       => "Hotel Booking Manager Settings",
			'item_version'    => "1.0.0",
			'panels'          => $this->whbm_set_page_settings( $gen_settings, $translation_settings, $pdf_setttings ),
		);
		$AddThemePage = new AddThemePage( $args );
	}
}

new WHBMSettingPage();