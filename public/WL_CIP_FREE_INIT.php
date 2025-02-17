<?php
defined( 'ABSPATH' ) or die();
require_once( WL_CIP_FREE_PLUGIN_DIR_PATH . 'public/inc/helpers/WL_CIP_FREE_Helper.php' );

class WL_CIP_FREE_INIT {
	/* action call for front login form */
	public static function wl_cip_front_ajax_call() {
		check_ajax_referer( 'cip_ajax_nonce', 'nounce' );

		$error_arr = array();
		if ( isset( $_POST['username'] ) && ! empty( $_POST['username'] ) ) {
		}	
		else {
			wp_send_json( 'Please enter Username', CIP_FREE_TXTDM );
		}
		if ( isset( $_POST['user_id'] ) && ! empty( $_POST['user_id'] ) ) {
		}	
		else {
			wp_send_json( 'Please Enter Password', CIP_FREE_TXTDM );
		}

		if ( isset( $_POST['username'] ) && ! empty( $_POST['username'] ) && isset( $_POST['user_id'] ) && ! empty( $_POST['user_id'] ) )
		{
			$type     = sanitize_text_field( $_POST['type'] );
			$username = sanitize_text_field( $_POST['username'] );
			$user_id  = sanitize_text_field( $_POST['user_id'] );

			$valid_user = WL_CIP_FREE_Helper::get_user_data( $username, $user_id );
			 
			if ( $type == 'office-in' ) {			
				if ( ! empty ( $valid_user ) ) {
					$valid_user = WL_CIP_FREE_Helper::user_validation( $username, $type );
					wp_send_json( $valid_user );
				} else {
					wp_send_json( 'Login details are incorrect, Try again.');
				}
			} elseif ( $type == 'office-out' ) {
				if ( ! empty ( $valid_user ) ) {
					$valid_user = WL_CIP_FREE_Helper::user_validation( $username, $type );
					wp_send_json( $valid_user );
				} else {
					wp_send_json( 'Login details are incorrect, Try again.');
				}
			} elseif ( $type == 'lunch-in' ) {
				if ( ! empty ( $valid_user ) ) {
					$valid_user = WL_CIP_FREE_Helper::user_validation( $username, $type );
					wp_send_json( $valid_user );
				} else {
					wp_send_json( 'Login details are incorrect, Try again.');
				}
			} elseif ( $type == 'lunch-out' ) {
				if ( ! empty ( $valid_user ) ) {
					$valid_user = WL_CIP_FREE_Helper::user_validation( $username, $type );
					wp_send_json( $valid_user );
				} else {
					wp_send_json( 'Login details are incorrect, Try again.');
				}
			} elseif ( $type == 'break-in' ) {
				if ( ! empty ( $valid_user ) ) {
					$valid_user = WL_CIP_FREE_Helper::user_validation( $username, $type );
					wp_send_json( $valid_user );
				} else {
					wp_send_json( 'Login details are incorrect, Try again.');
				}
			} elseif ( $type == 'break-out' ) {
				if ( ! empty ( $valid_user ) ) {
					$valid_user = WL_CIP_FREE_Helper::user_validation( $username, $type );
					wp_send_json( $valid_user );
				} else {
					wp_send_json( 'Login details are incorrect, Try again.');
				}
			}
		}
		wp_die();
	}
}
?>