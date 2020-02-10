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

        $args = array(
            'taxonomy'       => 'mage_hotel_cat',
            'options' 	        => $page_1_options,
        );
        $TaxonomyEdit = new TaxonomyEdit( $args );
    }
}
new MageTaxMeta();