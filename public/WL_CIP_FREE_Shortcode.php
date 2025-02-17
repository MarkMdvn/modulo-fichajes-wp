<?php
defined( 'ABSPATH' ) or die();

class WL_CIP_FREE_Shortcode {

	/* Location */
	public static function create_login_portal( $attr ) {	
		ob_start();
        require_once( 'inc/controllers/WL_CIP_FREE_Portal_Shotcode.php' );
        return ob_get_clean();
	}

	public static function shortcode_enqueue_assets() {
		global $post;
		if ( is_a( $post, 'WP_Post' ) ) {
			if ( has_shortcode( $post->post_content, 'WL_CIP_PORTAL' ) ) {
				//js
				wp_enqueue_script( 'jquery');
				wp_enqueue_script( 'cip-bootstrap-js', CIP_PLG_URL.'/js/bootstrap.js', array('jquery'), '', true);
				wp_enqueue_script( 'cip-flipclock-js', CIP_PLG_URL.'/js/flipclock.js', array('jquery'), '', false);
				wp_enqueue_script( 'cip-flipclock_new', CIP_PLG_URL.'public/js/flipclock_new.js', array('jquery'), '', true);

				/* Enqueue select region js */
				wp_enqueue_script( 'cip_front_ajax_call', CIP_PLG_URL . 'public/js/front_ajax_call.js', array( 'jquery' ), '', true);
				wp_localize_script( 'cip_front_ajax_call', 'ajax_cip', array(
					'ajax_url'     => admin_url( 'admin-ajax.php' ),
					'cip_nonce' => wp_create_nonce( 'cip_ajax_nonce' )
				) );
				
				//css
				wp_enqueue_style( 'cip-flip-clock-css', CIP_PLG_URL.'/css/flipclock.css');
				wp_enqueue_style( 'cip-dashboard-css', CIP_PLG_URL.'/css/cip-dashboard.css');
				wp_enqueue_style( 'cip-bootstrap-css', CIP_PLG_URL.'/css/bootstrap.css');	
				wp_enqueue_style( 'cip-style-css', CIP_PLG_URL.'/css/style.css');
				wp_enqueue_style( 'cip-front-css', CIP_PLG_URL.'/public/css/portal_front.css');
			}
		}
	}

}

?>