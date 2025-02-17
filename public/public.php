<?php
defined( 'ABSPATH' ) or die();

require_once( 'WL_CIP_FREE_Shortcode.php' );
require_once( 'WL_CIP_FREE_INIT.php' );

/* Enqueue Assets for shortcodes */
add_action( 'wp_enqueue_scripts', array( 'WL_CIP_FREE_Shortcode', 'shortcode_enqueue_assets' ) );

/* Shortcode for login portal */
add_shortcode( 'WL_CIP_PORTAL', array( 'WL_CIP_FREE_Shortcode', 'create_login_portal' ) );

/* action call for Countries in wl_agm_region_ajax.js */
add_action( 'wp_ajax_nopriv_wl_cip_front_call', array( 'WL_CIP_FREE_INIT', 'wl_cip_front_ajax_call' ) );
add_action( 'wp_ajax_wl_cip_front_call', array( 'WL_CIP_FREE_INIT', 'wl_cip_front_ajax_call' ) );

?>