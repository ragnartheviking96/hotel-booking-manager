<?php
/*
* @Author 		pickplugins
* Copyright: 	pickplugins.com
*/

if ( ! defined('ABSPATH')) exit;  // if direct access 


class MageTaxMeta{
    public function __construct(){
        $this->tax_meta();
    }
    public function tax_meta(){

        $page_1_options = array(
            array(
                'id'		=> 'icon_field',
                'title'		=> __('Icon Of Features Services','whbm'),
                'details'	=> __('Description of icon field','whbm'),
                'default'	=> 'fas fa-bomb',
                'type'		=> 'icon',
                'args'		=> 'FONTAWESOME_ARRAY',
            ),
        );
	    $page_2_options = array(
		    array(
			    'id'          => 'property_type_image',
			    'title'       => __( 'Property Type Image', 'text-domain' ),
			    'details'     => __( 'Property Type Image', 'text-domain' ),
			    'placeholder' => 'https://i.imgur.com/GD3zKtz.png',
			    'type'        => 'media',
		    ),
	    );
	    $ptype_args = array(
		    'taxonomy'       => 'mage_property_type',
		    'options' 	        => $page_2_options,
	    );

        $args = array(
            'taxonomy'       => 'mage_hotel_cat',
            'options' 	        => $page_1_options,
        );
        $TaxonomyEdit = new TaxonomyEdit( $ptype_args );
        $TaxonomyEdit = new TaxonomyEdit( $args );
    }
}
new MageTaxMeta();