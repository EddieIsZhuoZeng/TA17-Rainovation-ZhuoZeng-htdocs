<?php
/**
 * Created by PhpStorm.
 * User: Your Inspiration
 * Date: 18/03/2015
 * Time: 14:44
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly


return array(

    'settings' => array(

    	'tab_manager_table_section_start' => array(
    		'type' => 'title',
		    'value' => ''
	    ),
	    'tab_manager_list_table' => array(
	        'type' => 'yith-field',
		    'yith-type' => 'list-table',
		    'post_type' => 'ywtm_tab',
	        'list_table_class'     => 'YITH_Tab_Manager_Table',
	        'list_table_class_dir' =>YWTM_INC . 'admin-tables/class.ywctm-tab-manager-table.php',
	        'title'                => __( 'Tab Manager', 'yith-woocommerce-tab-manager' ),
	        'add_new_button'       => __( 'Add New Tab', 'yith-woocommerce-tab-manager' ),
	        'id'                   => 'ywctm_list_table',

	    ),
	    'tab_manager_table_section_end' => array(
		    'type' => 'sectionend',

	    ),

    )
);