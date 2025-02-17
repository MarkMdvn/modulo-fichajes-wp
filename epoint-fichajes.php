<?php
/*
Plugin Name: Módulo de fichajes para epoint
Plugin URI:  https://epoint.es
Description: Rastrea la asistencia de todos los empleados registrados mediante un sistema de marcación de entrada y salida, gestión sencilla de salarios, y administración de turnos para empleados (usuarios).
Version:     2.2
Author:      vibhorp
Author URI:  https://www.infigosoftware.in
Plugin URI:  https://wordpress.org/plugins/clock-in-portal/
Domain Path: /languages
Text Domain: CIP_FREE_TXTDM
*/

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

define('CIP_FREE_TXTDM', 'CIP_FREE');
define('CIP_PLUGIN_NAME', __('Fichajes epoint', CIP_FREE_TXTDM));
define("CIP_PLG_URL", plugin_dir_url(__FILE__));
if (!defined('WL_CIP_FREE_PLUGIN_DIR_PATH')) {
    define('WL_CIP_FREE_PLUGIN_DIR_PATH', plugin_dir_path(__FILE__));
}

//Include files
include(plugin_dir_path(__FILE__) . 'inc/custom_function.php');
include(plugin_dir_path(__FILE__) . 'public/public.php');
require_once WL_CIP_FREE_PLUGIN_DIR_PATH . 'admin-setup-wizard.php';

add_action('plugins_loaded', 'CIP_Language_Translater_Free');
function CIP_Language_Translater_Free() {
    load_plugin_textdomain(CIP_FREE_TXTDM, false, dirname(plugin_basename(__FILE__)) . '/languages');
}

// on activation
register_activation_hook(__FILE__, 'cip_run_install_free');
function cip_run_install_free() {
    require("install-script.php");
}

register_activation_hook(__FILE__, 'cip_intsert_data_free');
function cip_intsert_data_free() {
    global $wpdb;
    $id = "1";
    $name  = "default";
    $color  = "#fff";
    $status  = "1";
    $staff_category_table = $wpdb->base_prefix . "sm_staff_category";
    // first check if data exists with select query

    $result = $wpdb->get_results("SELECT * FROM $staff_category_table");
    if ($wpdb->num_rows > 0) {
    }
    // if not exist in the database then insert it
    else {
        $rows_affected = $wpdb->insert($staff_category_table, array('id' => 1, 'name' => $name, 'color' => $color, 'status' => $status));
    }
}

register_activation_hook(__FILE__, 'cip_setup_wizard_activation_hook_free');
function cip_setup_wizard_activation_hook_free() {
    add_option('wl_cip_setup_wizard', true);
}

add_action('admin_init', 'cip_setup_wizard_redirect_free');
function cip_setup_wizard_redirect_free() {
    if (get_option('wl_cip_setup_wizard', false)) {
        delete_option('wl_cip_setup_wizard');
        if (!isset($_GET['activate-multi'])) {
            wp_redirect("index.php?page=cip-setup-wizard-free");
        }
    }
}

// on deactivation
register_deactivation_hook(__FILE__, 'cip_run_uninstall');
function cip_run_uninstall() {
    // do nothing
}
add_action('admin_menu', 'cip_plugin_menu_free');
function cip_plugin_menu_free() {
    if ($staff_request = get_option("cip_staff_request")) {
        $i = 0;
        foreach ($staff_request as $key => $staff_request) {
            $status = $staff_request['status'];
            if ($status == 1) {
                $i = $i + 1;
            }
            if ($i > 0) {
                $LeaveRequest = 'Leave Request <span class="update-plugins count-1"><span class="plugin-count">' . $i . '</span></span>';
            } else {
                $LeaveRequest = 'Leave Request';
            }
        }
    } else {
        $LeaveRequest = 'Leave Request';
    }

    $cip_menu1 = add_menu_page(CIP_PLUGIN_NAME, __(CIP_PLUGIN_NAME, CIP_FREE_TXTDM), 'administrator', 'clock-in-portal', 'cip_dashboard_free', 'dashicons-clock', 65);
    $cip_menu2 = add_submenu_page('clock-in-portal', 'Reports', __('Reports', CIP_FREE_TXTDM), 'administrator', 'cip-reports', 'cip_reports_free');
    $cip_menu3 = add_submenu_page('clock-in-portal', 'Staffs', __('Staff', CIP_FREE_TXTDM), 'administrator', 'cip-staffs', 'cip_staffs_free');
    $cip_menu4 = add_submenu_page('clock-in-portal', 'Designations', __('Designations', CIP_FREE_TXTDM), 'administrator', 'cip-designations', 'cip_designations_free');
    $cip_menu5 = add_submenu_page('clock-in-portal', 'Holidays', __('Holidays', CIP_FREE_TXTDM), 'administrator', 'cip-holidays', 'cip_holidays_free');
    $cip_menu6 = add_submenu_page('clock-in-portal', $LeaveRequest, __($LeaveRequest, CIP_FREE_TXTDM), 'administrator', 'cip-staff-request', 'cip_staff_request_free');
    $cip_menu7 = add_submenu_page('clock-in-portal', 'Settings', __('Settings', CIP_FREE_TXTDM), 'administrator', 'cip-settings', 'cip_settings_free');
    $cip_menu8 = add_submenu_page('clock-in-portal', 'How to Work', __('How to Work', CIP_FREE_TXTDM), 'administrator', 'cip-help', 'cip_help_free');
	


    add_action('admin_print_styles-' . $cip_menu1, 'cip_css_js_free');
    add_action('admin_print_styles-' . $cip_menu2, 'cip_css_js_free');
    add_action('admin_print_styles-' . $cip_menu2, 'cip_css_js_free');
    add_action('admin_print_styles-' . $cip_menu3, 'cip_css_js_free');
    add_action('admin_print_styles-' . $cip_menu4, 'cip_css_js_free');
    add_action('admin_print_styles-' . $cip_menu5, 'cip_css_js_free');
    add_action('admin_print_styles-' . $cip_menu6, 'cip_css_js_free');
    add_action('admin_print_styles-' . $cip_menu7, 'cip_css_js_free');
    add_action('admin_print_styles-' . $cip_menu8, 'cip_css_js_free');
	

    global $wpdb;
    $staff_table = $wpdb->base_prefix . "sm_staffs";
    // get user details & id
    $current_user = wp_get_current_user();
    $userid = $current_user->ID;

    if ($userdata = $wpdb->get_row($wpdb->prepare("SELECT * FROM `$staff_table` WHERE status = 1 AND `staff_id` = %d", $userid))) {
        $allowed_roles = array('editor', 'subscriber', 'author', 'contributor', 'shop_manager');
        foreach ($allowed_roles as $userroles) {
            if ($userroles == $current_user->roles[0]) {
                $cip_menu10 = add_submenu_page('clock-in-portal', 'Dashboard', __('Dashboard', CIP_FREE_TXTDM), $userroles, 'subscribers-staff-attendance', 'cip_subscribers_staff_attendance_free');
                $cip_menu11 = add_submenu_page('clock-in-portal', 'Reports', __('Reports', CIP_FREE_TXTDM), $userroles, 'subscribers-staff-reports', 'cip_subscribers_staff_reports_free');
                $cip_menu12 = add_submenu_page('clock-in-portal', 'Holidays', __('Holidays', CIP_FREE_TXTDM), $userroles, 'subscribers-staff-holidays', 'cip_subscribers_staff_holiodays_free');
                $cip_menu13 = add_submenu_page('clock-in-portal', 'Leave Request', __('Leave Request', CIP_FREE_TXTDM), $userroles, 'subscribers-staff-request', 'cip_subscribers_staff_request_free');
				
                add_action('admin_print_styles-' . $cip_menu10, 'cip_css_js_free');
                add_action('admin_print_styles-' . $cip_menu11, 'cip_css_js_free');
                add_action('admin_print_styles-' . $cip_menu12, 'cip_css_js_free');
                add_action('admin_print_styles-' . $cip_menu13, 'cip_css_js_free');
			
            }
        }
    }
}

// load js css in admin dashboard
function cip_css_js_free() {
    //js

    if ((strpos($_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], 'clock-in-portal') == true) or (site_url() . '/index.php?page=cip-setup-wizard-free') == true) {
        wp_enqueue_script('jquery');
        wp_register_script('cip-bootstrap-js', CIP_PLG_URL . '/js/bootstrap.min.js', array('jquery'), true, true);
        wp_enqueue_script('cip-bootstrap-js');
        wp_register_script('cip-flipclock-js', CIP_PLG_URL . '/js/flipclock.js', array('jquery'), true, false);
        wp_enqueue_script('cip-flipclock-js');
        wp_register_script('cip-jquery-date-format-js', CIP_PLG_URL . '/js/jquery-date-format.js', array('jquery'), true, true);
        wp_enqueue_script('cip-jquery-date-format-js');
        wp_enqueue_script('cip-moment-js', CIP_PLG_URL . '/js/moment.js', array('jquery'), true, true);
        wp_register_script('datepicker-js', CIP_PLG_URL . '/js/bootstrap-datetimepicker.min.js', array('jquery'), true, true);
        wp_enqueue_script('datepicker-js');
        wp_register_script('jquery-dataTables-min-js', CIP_PLG_URL . '/js/jquery.dataTables.min.js', array('jquery'), true, true);
        wp_enqueue_script('jquery-dataTables-min-js');
        wp_register_script('dataTables-buttons-min-js', CIP_PLG_URL . '/js/dataTables.buttons.min.js', array('jquery'), true, true);
        wp_enqueue_script('dataTables-buttons-min-js');
        wp_register_script('jszip-min-js', CIP_PLG_URL . '/js/jszip.min.js', array('jquery'), true, true);
        wp_enqueue_script('jszip-min-js');
        wp_register_script('pdfmake-min-js', CIP_PLG_URL . '/js/pdfmake.min.js', array('jquery'), true, true);
        wp_enqueue_script('pdfmake-min-js');
        wp_register_script('vfs_fonts-js', CIP_PLG_URL . '/js/vfs_fonts.js', array('jquery'), true, true);
        wp_enqueue_script('vfs_fonts-js');
        wp_enqueue_script('buttons-html5-min-js', CIP_PLG_URL . '/js/buttons.html5.min.js', array('jquery'), true, true);
        wp_register_script('buttons-print-min-js', CIP_PLG_URL . '/js/buttons.print.min.js', array('jquery'), true, true);
        wp_enqueue_script('buttons-print-min-js');
        wp_register_script('buttons-colVis-min-js', CIP_PLG_URL . '/js/buttons.colVis.min.js', array('jquery'), true, true);
        wp_enqueue_script('buttons-colVis-min-js');

        //css
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_style('cip-flip-clock-css', CIP_PLG_URL . '/css/flipclock.css');
        wp_enqueue_style('cip-dashboard-css', CIP_PLG_URL . '/css/cip-dashboard.css');
        wp_enqueue_style('cip-bootstrap-css', CIP_PLG_URL . '/css/bootstrap.min.css');
        wp_enqueue_style('cip-datetimepicker-css', CIP_PLG_URL . '/css/bootstrap-datetimepicker.min.css');
        wp_enqueue_style('cip-font-awesome-css', CIP_PLG_URL . '/css/all.min.css');
        wp_enqueue_style('cip-style-css', CIP_PLG_URL . '/css/style.css');
        wp_enqueue_style('cip-jquery-dataTables-min-css', CIP_PLG_URL . '/css/jquery.dataTables.min.css');
        wp_enqueue_style('cip-buttons-dataTables-min-css', CIP_PLG_URL . '/css/buttons.dataTables.min.css');
    }
}

function my_login_redirect_free($url, $request, $user) {
    if ($user && is_object($user) && is_a($user, 'WP_User')) {
        if ($user->has_cap('administrator')) {
            $url = admin_url();
        } else {
            $url = admin_url('/admin.php?page=subscribers-staff-attendance/');
        }
    }
    return $url;
}
add_filter('login_redirect', 'my_login_redirect_free', 10, 3);



/* ADMIN PAGES */

//dashboard
function cip_dashboard_free() {
    require('dashboard.php');
}
//report
function cip_help_free() {
    require('help.php');
}

//report
function cip_reports_free() {
    require('reports.php');
}

//staffs
function cip_staffs_free() {
    require('staffs.php');
}



//designations
function cip_designations_free() {
    require('designations.php');
}


//holidays
function cip_holidays_free() {
    require('holidays.php');
}

//features
function cip_features() {
    require('pro_features.php');
}

//Staff Request
function cip_staff_request_free() {
    require('staff-request.php');
}

//settings
function cip_settings_free() {
    require('settings/settings.php');
}

//*** Subscriber Pages ***//

//subscribers-staff-attendance
function cip_subscribers_staff_attendance_free() {
    require('subscribers/staff-attendance.php');
}

//subscribers-staff-reports
function cip_subscribers_staff_reports_free() {
    require('subscribers/staff-reports.php');
}
//subscribers-staff-holidays
function cip_subscribers_staff_request_free() {
    require('subscribers/staff-request.php');
}

//subscribers-staff-holidays
function cip_subscribers_staff_holiodays_free() {
    require('subscribers/staff-holidays.php');
}

// our products

$cip_settings = get_option('cip_settings');
if (isset($cip_settings['have_woo'])) {
    if ($cip_settings['have_woo'] == 'yes') {
        /* To Bypass woocommerce function, it allows clients and staff to see admin dashboard */
        add_filter('woocommerce_disable_admin_bar', '_wc_disable_admin_bar', 10, 1);

        function _wc_disable_admin_bar($prevent_admin_access) {
            return false;
        }

        add_filter('woocommerce_prevent_admin_access', '_wc_prevent_admin_access', 10, 1);

        function _wc_prevent_admin_access($prevent_admin_access) {
            return false;
        }
        /*To bypass woocommerce function*/
    }
}

add_action('wp_dashboard_setup', 'cip_dashboard_widgets');

function cip_dashboard_widgets() {
    global $wp_meta_boxes;

    $user = new WP_User(get_current_user_id());
    $role = $user->roles[0];

    if ($role == 'administrator') {
        wp_add_dashboard_widget('custom_help_widget', 'Fichajes epoint', 'cip_dashboard_help');
    }
}

function cip_dashboard_help() {
    echo '<h3 class="users_status_main"><b>Users Status</b></h3>';
    global $wpdb;
    $staff_attendance_table = $wpdb->base_prefix . "sm_attendance";
    $staff_table            = $wpdb->base_prefix . "sm_staffs";
    $date_format            = get_option('date_format');
    $time_format            = get_option('time_format');
    $current_date           = date("Y-m-d");
    $stattus                = 0;

    $all_staffs = $wpdb->get_results($wpdb->prepare("SELECT * FROM `$staff_table` WHERE `status` = %d", 1));

    if (!empty($all_staffs)) {
        $no = 1;
        foreach ($all_staffs as $staff_data) {
            $staff_id  = $staff_data->staff_id;
            $user_info = get_userdata($staff_id);
            if (!empty($user_info)) {
                $email    = $user_info->user_email;
                $fname    = $user_info->first_name;
                $lname    = $user_info->last_name;
                $fullname = ucwords($fname . " " . $lname);
                $office_status = 0;
                $staff = $wpdb->get_row($wpdb->prepare("SELECT * FROM `$staff_attendance_table` WHERE `staff_id` = %d AND `date` = %s", $staff_id, $current_date));
                if (!empty($staff)) {
                    if ($staff->office_in != '00:00:00' && $staff->office_out == '00:00:00') {
                        $status = 'Log In';
                        $class = 'green';
                    } elseif ($staff->office_in != '00:00:00' && $staff->office_out != '00:00:00') {
                        $status = 'Log Out';
                        $class = 'red';
                    }
                    echo '<p class="logged_in_users">' . $no . '. ' . $fullname . ' <span class="cip_user_status ' . $class . '">' . $status . '</span></p>';

                    $stattus = 1;
                    $no++;
                }
            }
        }

        if ($stattus != 1) {
            echo '<p class="cip_no_users">No one logged in yet.!</p>';
        }
    } ?>
	<?php
}


function cip_total_staff_count() {
    global $wpdb;
    $staff_attendance_table = $wpdb->base_prefix . "sm_attendance";
    $staff_table            = $wpdb->base_prefix . "sm_staffs";
    $date_format            = get_option('date_format');
    $time_format            = get_option('time_format');
    $current_date           = date("Y-m-d");
    $stattus                = 0;

    $all_staffs = $wpdb->get_results($wpdb->prepare("SELECT * FROM `$staff_table` WHERE `status` = %d", 1));
    $count = 0;
    if (!empty($all_staffs)) {
        $no    = 1;

        foreach ($all_staffs as $staff_data) {
            $staff_id  = $staff_data->staff_id;
            $user_info = get_userdata($staff_id);
            if (!empty($user_info)) {
                $email    = $user_info->user_email;
                $fname    = $user_info->first_name;
                $lname    = $user_info->last_name;
                $fullname = ucwords($fname . " " . $lname);
                $office_status = 0;
                $staff = $wpdb->get_row($wpdb->prepare("SELECT * FROM `$staff_attendance_table` WHERE `staff_id` = %d AND `date` = %s", $staff_id, $current_date));
                if (!empty($staff)) {
                    $count++;
                    $stattus = 1;
                }
            }
        }
    }
    if ($count == 0) {
        $count = count($all_staffs);
    }

    return $count;
}


//Redirect from wp-admin
$cip_settings = get_option('cip_settings');
if (isset($cip_settings['shortcode_enable'])) {
    $shortcode_enable = $cip_settings['shortcode_enable'];
} else {
    $shortcode_enable = "no";
}
if ($shortcode_enable == 'yes') {
    add_action('admin_init', 'cip_free_admin_redirect');
}

function cip_free_admin_redirect() {
    if (!defined('DOING_AJAX')) {
        if (current_user_can('subscriber')) {
            $refer = wp_get_referer();
            if (!$refer || strpos($refer, 'wp-admin')) {
                wp_safe_redirect(home_url());
            } else {
                wp_safe_redirect($refer);
            }
        }
    }
}

function cip_incoming_holidays_count() {
    global $wpdb;
    $holidays    = get_option("cip_official_holidays");
    $startdate   = new \DateTime(date("Y-m") . "-01");
    $startdate   = $startdate->format("Y-m-d");
    $enddate     = new \DateTime(date("Y-m-t"));
    $enddate     = $enddate->format("Y-m-d");
    $i           = strtotime($startdate);
    $j           = strtotime($enddate);
    $all_dates   = array();
    $count       = 0;

    for ($i; $i <= $j; $i = strtotime(date("Y-m-d", strtotime("+1 day", $i)))) {
        array_push($all_dates, date("Y-m-d", $i));
    }
    $n = 1;
    foreach ($all_dates as $row_date) {
        if (!empty($holidays)) {
            foreach ($holidays as $key => $holiday) {
                $count++;
            }
        }
    }

    return $count;
}

function cip_incoming_leave_count() {
    global $wpdb;
    $first = date("Y-m-01");
    $last  = date("Y-m-t", strtotime($first));
    $all_days_report = range_date_free($first, $last);
    $count       = 0;

    foreach ($all_days_report as $row_date) {
        $staff_request = get_option("cip_staff_request");
        if (!empty($staff_request)) {
            foreach ($staff_request as $key => $staff_request) {
                if ($row_date == $staff_request['start_date']) {
                    $status = $staff_request['status'];
                    if ($status == 1) {
                        $count++;
                    }
                }
            }
        }
    }
    return $count;
}