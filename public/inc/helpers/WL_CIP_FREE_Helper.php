<?php
defined( 'ABSPATH' ) or die();

class WL_CIP_FREE_Helper {

	/* Get Users data from datatables */
	public static function get_user_data( $username, $user_id ) {
		global $wpdb;
		$staff_table = $wpdb->base_prefix . "sm_staffs";
		$all_staffs  = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM `$staff_table` WHERE `staff_id` = %d", $user_id ) );
		if ( ! empty ( $all_staffs ) ) {
			return true;
		} else {
			return false;
		}
	}

	/* insert user login data */
	public static function insert_user_login_data( $user_id ) {
		global $wpdb;
		$staff_attendance_table = $wpdb->base_prefix . "sm_attendance";
		$cip_settings  = get_option('cip_settings');
		date_default_timezone_set($cip_settings['cip_timezone']);
		$date   	   = date("Y-m-d");
		$time   	   = date("H:i:s");
		$ip            = sanitize_text_field($_SERVER['REMOTE_ADDR']);
		$user_location = user_locationn_free($ip);

		$query = $wpdb->prepare( "INSERT INTO `$staff_attendance_table` (`id`, `staff_id`, `office_in`, `date`, `ip`, `user_location`) VALUES (NULL, %d, %s, %s, %s, %s );", $user_id, $time, $date, $ip, $user_location );
		if( $in = $wpdb->query( $query ) ) {
			$message = "Your office working session was started at ".date( 'H:i:s', strtotime( $time ) );
		} else {
			$message = "Unable to start working session.";
		}
		return $message;
	}

	/* Insert user lunch in data */
	public static function insert_user_lunch_in_data( $user_id ) {
		global $wpdb;
		$staff_attendance_table = $wpdb->base_prefix . "sm_attendance";
		$cip_settings  = get_option('cip_settings');
		date_default_timezone_set($cip_settings['cip_timezone']);
		$time   	   = date("H:i:s");
		$date   	   = date("Y-m-d");

		$query = $wpdb->prepare("UPDATE `$staff_attendance_table` SET `lunch_in` = %s WHERE `staff_id` = %d AND `date` = %s", $time, $user_id, $date);
		if ( $in = $wpdb->query( $query ) ) {
			$message = 'Your lunch session was started at '.date( 'H:i:s', strtotime( $time ) );
		} else {
			$message = 'Unable to start lunch session.';
		}
		return $message;
	}

	/* Insert user lunch Out data */
	public static function insert_user_lunch_out_data( $user_id ) {
		global $wpdb;
		$staff_attendance_table = $wpdb->base_prefix . "sm_attendance";
		$cip_settings  = get_option('cip_settings');
		date_default_timezone_set($cip_settings['cip_timezone']);
		$time   	   = date("H:i:s");
		$date   	   = date("Y-m-d");

		$query = $wpdb->prepare("UPDATE `$staff_attendance_table` SET `lunch_out` = %s WHERE `staff_id` = %d AND `date` = %s", $time, $user_id, $date);
		if ( $in = $wpdb->query( $query ) ) {
			$message = 'Your lunch session was end at '.date( 'H:i:s', strtotime( $time ) );
		} else {
			$message = 'Unable to end your lunch session.';
		}
		return $message;
	}

	/* Insert user Break in data */
	public static function insert_user_break_in_data( $user_id ) {
		global $wpdb;
		$staff_attendance_table = $wpdb->base_prefix . "sm_attendance";
		$cip_settings  = get_option('cip_settings');
		date_default_timezone_set($cip_settings['cip_timezone']);
		$time   	   = date("H:i:s");
		$date   	   = date("Y-m-d");

		$query = $wpdb->prepare("UPDATE `$staff_attendance_table` SET `break_in` = %s WHERE `staff_id` = %d AND `date` = %s", $time, $user_id, $date);
		if ( $in = $wpdb->query( $query ) ) {
			$message = 'Your Break session was started at '.date( 'H:i:s', strtotime( $time ) );
		} else {
			$message = 'Unable to start Break session.';
		}
		return $message;
	}

	/* Insert user Break Out data */
	public static function insert_user_break_out_data( $user_id ) {
		global $wpdb;
		$staff_attendance_table = $wpdb->base_prefix . "sm_attendance";
		$cip_settings  = get_option('cip_settings');
		date_default_timezone_set($cip_settings['cip_timezone']);
		$time   	   = date("H:i:s");
		$date   	   = date("Y-m-d");

		$query = $wpdb->prepare("UPDATE `$staff_attendance_table` SET `break_out` = %s WHERE `staff_id` = %d AND `date` = %s", $time, $user_id, $date);
		if ( $in = $wpdb->query( $query ) ) {
			$message = 'Your Break session was end at '.date( 'H:i:s', strtotime( $time ) );
		} else {
			$message = 'Unable to end your Break session.';
		}
		return $message;
	}

	/* Insert user Office Out data */
	public static function insert_user_office_out_data( $user_id ) {
		global $wpdb;
		$staff_attendance_table = $wpdb->base_prefix . "sm_attendance";
		$cip_settings  = get_option('cip_settings');
		date_default_timezone_set($cip_settings['cip_timezone']);
		$time   	   = date("H:i:s");
		$date   	   = date("Y-m-d");

		$query = $wpdb->prepare("UPDATE `$staff_attendance_table` SET `office_out` = %s WHERE `staff_id` = %d AND `date` = %s", $time, $user_id, $date);
		if ( $in = $wpdb->query( $query ) ) {
			$message = 'Your Office session was end at '.date( 'H:i:s', strtotime( $time ) );
		} else {
			$message = 'Unable to end your Office session.';
		}
		return $message;
	}

	/* Get Users data from datatables */
	public static function user_validation( $username, $type ) {
		global $wpdb;
		$user_table = $wpdb->base_prefix . "users";
		if(count($all_staffs = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM `$user_table` WHERE `user_login` = %s", $username  ) ) ) ) {
			foreach($all_staffs as $staff_data) {
				$user_id = $staff_data->ID; 
			}
		}
		$staff_table = $wpdb->base_prefix . "sm_staffs";
		if(count($all_staffs = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM `$staff_table` WHERE `staff_id` = %d", $user_id ) ))) {
			if( ! empty ( $all_staffs ) ) {
				$staff_attendance_table = $wpdb->base_prefix . "sm_attendance";
				$current_date = date("Y-m-d");

				$reports = $wpdb->get_row("SELECT * FROM $staff_attendance_table WHERE `staff_id` = '$user_id' AND `date` LIKE '$current_date'");

				if( $type == 'office-in' ){
					if( empty ( $reports->office_in ) ) {
						$message = self::insert_user_login_data( $user_id );
					} else {
						$message = 'User already Logged In';
					}
				} elseif ( $type == 'lunch-in' ) {
					if( ! empty ( $reports->lunch_in ) &&  $reports->lunch_in == '00:00:00' ) {
						$message = self::insert_user_lunch_in_data( $user_id );
					} else {
						$message = 'User already lunch In';
					}
				} elseif ( $type == 'lunch-out' ) {
					if( ! empty ( $reports->lunch_in ) &&  $reports->lunch_in == '00:00:00' ) {
						$message = 'You have to lunch in first';
					} elseif ( ! empty ( $reports->lunch_out ) &&  $reports->lunch_out == '00:00:00' ) {
						$message = self::insert_user_lunch_out_data( $user_id );
					} else {
						$message = 'User already lunch Out';
					}
				} elseif ( $type == 'office-out' ) {
					if( ! empty ( $reports->office_out ) &&  $reports->office_out == '00:00:00' ) {
						$message = self::insert_user_office_out_data( $user_id );
					} else {
						$message = 'User already Office Out';
					}
				} elseif ( $type == 'break-in' ) {
					if( ! empty ( $reports->break_in ) &&  $reports->break_in == '00:00:00' ) {
						$message = self::insert_user_break_in_data( $user_id );
					} else {
						$message = 'User already Break In';
					}
				} elseif ( $type == 'break-out' ) {
					if( ! empty ( $reports->break_in ) &&  $reports->break_in == '00:00:00' ) {
						$message = 'You have to Break In first';
					} elseif ( ! empty ( $reports->break_out ) &&  $reports->break_out == '00:00:00' ) {
						$message = self::insert_user_break_out_data( $user_id );
					} else {
						$message = 'User already Break Out';
					}
				}
			}
		} else {
			$message = _e( 'Sorry! Your account is not activated. Please contact to your higher authority regarding your Inactive account.', CIP_FREE_TXTDM );
		}
		return $message;
	}

	public static function result_echo( $value ) {
		esc_html_e($value);
	}
}

?>