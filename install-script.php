<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
$saved_holiday = array();
add_option("cip_official_holidays");
global $wpdb;

/*--------Staff atendance table--------*/
$staff_attendance_table = $wpdb->base_prefix . "sm_attendance";

$coll3 = $wpdb->get_var("SHOW TABLES LIKE '$staff_attendance_table'"); 
if ( !$coll3 ){
$staff_attendance_table_query = $wpdb->query("CREATE TABLE IF NOT EXISTS `$staff_attendance_table` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `staff_id` int(11) NOT NULL,
  `office_in` time NOT NULL,
  `office_out` time NOT NULL,
  `lunch_in` time NOT NULL,
  `lunch_out` time NOT NULL,
  `break_in` time NOT NULL,
  `break_out` time NOT NULL,
  `date` date NOT NULL,
  `today_total_hours` time NOT NULL,
  `ip` varchar(255) NOT NULL,
  `timestamp` timestamp NOT NULL,
  `note` text NOT NULL,
  `extra` longtext NOT NULL,
  `report` longtext NOT NULL,
  `user_location` varchar(255) NOT NULL,
 PRIMARY KEY (`id`)
)$charset_collate;");
}
$chcek_user_location = cip_table_column_exists($staff_attendance_table, 'user_location');
   if($chcek_user_location == false){
       $staff_table_query_user_location = "ALTER TABLE $staff_attendance_table ADD user_location VARCHAR(255) NOT NULL";
       $wpdb->query($staff_table_query_user_location);
   }
$chcek_break_in = cip_table_column_exists($staff_attendance_table, 'break_in');
   if($chcek_break_in == false){
       $staff_table_query_break_in = "ALTER TABLE $staff_attendance_table ADD break_in time NOT NULL";
       $wpdb->query($staff_table_query_break_in);
   }
$chcek_break_out = cip_table_column_exists($staff_attendance_table, 'break_out');
   if($chcek_break_out == false){
       $staff_table_query_break_out = "ALTER TABLE $staff_attendance_table ADD break_out time NOT NULL";
       $wpdb->query($staff_table_query_break_out);
   }

/*--------Staff table--------*/
$staff_table = $wpdb->base_prefix . "sm_staffs";
$staff_table_query = "CREATE TABLE IF NOT EXISTS `$staff_table` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `staff_id` int(11) NOT NULL,
  `cat_id` int(11) NOT NULL,
  `status` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 ;";
$wpdb->query($staff_table_query);

/*--------Staff Category table--------*/
$staff_category_table = $wpdb->base_prefix . "sm_staff_category";
$staff_category_table_query = "CREATE TABLE IF NOT EXISTS `$staff_category_table` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `color` varchar(10) NOT NULL,
  `status` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 ;";
$wpdb->query($staff_category_table_query); ?>