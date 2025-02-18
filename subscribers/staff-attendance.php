<?php
// Exit if accessed directly
if (!defined('ABSPATH')) exit;

$cip_settings = get_option('cip_settings');

global $wpdb;
$staff_attendance_table = $wpdb->base_prefix . "sm_attendance";
$staff_table = $wpdb->base_prefix . "sm_staffs";
$staff_category_table = $wpdb->base_prefix . "sm_staff_category";
$date_format = "d F y";
$time_format = "g:i A";
$current_date = date("Y-m-d");

// get user details & id
$current_user = wp_get_current_user();
$username = $current_user->user_login;
$email = $current_user->user_email;
$fname = $current_user->user_firstname;
$lname = $current_user->user_lastname;
$userid = $current_user->ID;

/*--------Current Month dates array--------*/
$startdatee = date("Y-m-01");
$enddatee = date("Y-m-d");
$i = strtotime($startdatee);
$j = strtotime($enddatee);
$all_dates_attend = array();
for ($i; $i <= $j; $i = strtotime(date("Y-m-d", strtotime("+1 day", $i)))) {
    array_push($all_dates_attend, date("Y-m-d", $i));
}
/*--------end--------*/

$count_query = "select count(*) from $staff_attendance_table";
$num = $wpdb->get_var($count_query);
$prev_date = null;
$row = $wpdb->get_results("SELECT * FROM $staff_attendance_table");
$count = 1;
foreach ($row as $row) {
    if ($count == $num) {
        $prev_date = $row->date;
        $sepparator = '-';
        $parts = explode($sepparator, $row->date);
        $dayForDate = date("l", mktime(0, 0, 0, $parts[1], $parts[2], $parts[0]));
        $dayForDate;
    }
    $count++;
}

/*total absent*/
$holiday_arr = cip_holiday_days_free();
$count_ab = 0;
$total_day_absent = array();
foreach ($all_dates_attend as $row_date) {
    $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM `$staff_attendance_table` WHERE `staff_id` = %d AND `date` LIKE %s", $userid, $row_date));
    $holiday_arr = cip_holiday_days_free();
    if (empty ($holiday_arr)) {
        $holiday_arr = array();
    }
    if (((date("l", strtotime($row_date)) != "Sunday") && (empty ($row))) || (in_array($row_date, $holiday_arr) && (empty ($row)))) {
        //check if Sunday else no record found
        $total_absent = $count_ab++;
        array_push($total_day_absent, $row_date);
    }
}

if (isset($total_day_absent)) {
    $total_day_absent2 = $total_day_absent;
    if (is_array($total_day_absent2)) {
        $total_day_absent2 = $total_day_absent;
    } else {
        $total_day_absent2 = array();
    }
}
if (isset($total_absent)) {
    $total_absent2 = $total_absent;
} else {
    $total_absent2 = '';
}

foreach ($all_dates_attend as $row_date) {
    if ($holidays = get_option("cip_official_holidays")) {
        foreach ($holidays as $key => $holiday) {
            if ((($row_date == $holiday['start_date']))) {
                //check if Sunday else no record found
                $date = $holidays['start_date'];
                $end_date = $holidays['end_date'];
                $dif = 0;
                while (strtotime($date) <= strtotime($end_date)) {
                    if (date("l", strtotime($date)) != "Sunday") {
                        $total_day_absent3 = array_diff($total_day_absent2, array($date));
                    }
                    $date = date("Y-m-d", strtotime("+1 day", strtotime($date)));
                    if (date("l", strtotime($date)) != "Sunday") {
                        $dif = $dif + 1;
                    }
                }
                if (isset($total_absent2)) {
                    $total_absent3 = $total_absent2 - $dif;
                }
            }
        }
    }
}
if (isset($total_day_absent3)) {
    $total_day_absent4 = $total_day_absent3;
} else {
    $total_day_absent4 = $total_day_absent2;
}
/* declare array */
if (!is_array($total_day_absent4)) {
    $total_day_absent4 = array();
}

if (isset($total_absent3)) {
    $total_absent3 = $total_absent3;
} else {
    $total_absent3 = $total_absent2;
}
if ($holidays = get_option("cip_official_holidays")) {
    foreach ($holidays as $key => $holiday) {
        $date = $holiday['start_date'];
        $end_date = $holiday['end_date'];
        $dif2 = 0;
        while (strtotime($date) <= strtotime($end_date)) {
            $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM `$staff_attendance_table` WHERE `staff_id` = %d AND `date` LIKE %s", $userid, $date));
            if (isset($row->date)) {
                $total_day_absent5 = array_diff($total_day_absent4, array($date));
            }
            $date = date("Y-m-d", strtotime("+1 day", strtotime($date)));
            if (isset($row->date)) {
                $dif2 = $dif2 + 1;
            }
        }
        if (isset($total_absent3)) {
            $final_total_absent = $total_absent3 + $dif2;
        }
    }
}
if (isset($total_day_absent5)) {
    $total_absent_days = $total_day_absent5;
} else {
    $total_absent_days = $total_day_absent4;
}

if (isset($final_total_absent)) {
    $final_total_absent = $final_total_absent;
} else {
    $final_total_absent = $total_absent3;
}
/*end of total absent*/

//check logged-in user is Shift Monitor existing User (by ID)
if ($userdata = $wpdb->get_row($wpdb->prepare("SELECT * FROM `$staff_table` WHERE `staff_id` = %d", $userid))) {
    // check user is active user
    $status = $userdata->status;
    if ($status == 1) {
        // get staff designation name
        if ($designation_id = $userdata->cat_id) {
            $designation_details = $wpdb->get_row($wpdb->prepare("SELECT * FROM `$staff_category_table` WHERE `id` = %d", $designation_id));
            $designation = $designation_details->name;
        }

        //check already office in and out
        $off_in_disable = "";
        $off_out_disable = "";
        $off_in_message = "";
        $off_out_message = "";
        $off_in_out = $wpdb->get_row($wpdb->prepare("SELECT * FROM `$staff_attendance_table` WHERE `staff_id` = %d AND `date` = %s", $userid, $current_date));
        if (!empty ($off_in_out)) {
            if ($off_in_out->office_in) $off_in_disable = "disabled";
            if ($off_in_out->office_out != "00:00:00") $off_out_disable = "disabled";
            if ($off_in_disable == "disabled") $off_in_message = "Your office working session was started at <strong>" . date($time_format, strtotime($off_in_out->office_in)) . "</strong>";
            if ($off_out_disable == "disabled") $off_out_message = "Your today's office session was completed at <strong>" . date($time_format, strtotime($off_in_out->office_out)) . "</strong>";
            if ($off_in_out->report != "") $report = $off_in_out->report; else $report = "";
        }

        //check already lunch in and out
        $lunch_in_disable = "";
        $lunch_out_disable = "";
        $lunch_in_message = "";
        $lunch_out_message = "";
        $lunch_in_out = $wpdb->get_row($wpdb->prepare("SELECT * FROM `$staff_attendance_table` WHERE `staff_id` = %d AND `date` = %s", $userid, $current_date));
        if (!empty ($lunch_in_out)) {
            $lunch_in_out->lunch_in;
            if ($lunch_in_out->lunch_in != "00:00:00") $lunch_in_disable = "disabled";
            if ($lunch_in_out->lunch_out != "00:00:00") $lunch_out_disable = "disabled";
            if ($lunch_in_disable == "disabled") $lunch_in_message = "Your lunch session was started at <strong>" . date($time_format, strtotime($lunch_in_out->lunch_in)) . "</strong>";
            if ($lunch_out_disable == "disabled") $lunch_out_message = "Your lunch session was completed at <strong>" . date($time_format, strtotime($lunch_in_out->lunch_out)) . "</strong>";
        }

        //check already Break in and out
        $break_in_disable = "";
        $break_out_disable = "";
        $break_in_message = "";
        $break_out_message = "";
        $break_in_out = $wpdb->get_row($wpdb->prepare("SELECT * FROM `$staff_attendance_table` WHERE `staff_id` = %d AND `date` = %s", $userid, $current_date));
        if (!empty ($break_in_out)) {
            $break_in_out->break_in;
            if ($break_in_out->break_in != "00:00:00") $break_in_disable = "disabled";
            if ($break_in_out->break_out != "00:00:00") $break_out_disable = "disabled";
            if ($break_in_disable == "disabled") $break_in_message = "Your break session was started at <strong>" . date($time_format, strtotime($break_in_out->break_in)) . "</strong>";
            if ($break_out_disable == "disabled") $break_out_message = "Your break session was completed at <strong>" . date($time_format, strtotime($break_in_out->break_out)) . "</strong>";
        }

        ?>
        <div class="clocking">

            <style>
                #wpbody {
                    padding-top: 0px !important;
                }
                .custom-nav-bar {
                    background: #fff !important;
                    padding: 10px 20px !important;
                    display: flex !important;
                    align-items: center !important;
                    justify-content: space-between !important;
                    box-shadow: 0 2px 4px rgba(0,0,0,0.1) !important;
                    margin: 10px auto !important;
                }
                .custom-nav-bar .nav-left img {
                    width: 100px !important;
                }
                .custom-nav-bar .nav-right {
                    display: flex !important;
                    align-items: center !important;
                }
                .custom-nav-bar .nav-right a {
                    color: #333 !important;
                    text-decoration: none !important;
                    margin-left: 15px !important;
                    font-weight: 500 !important;
                    transition: color 0.3s ease !important;
                    font-size: 1.5rem !important;
                }
                .custom-nav-bar .nav-right a:hover {
                    color: #000 !important;
                }
                /* Mobile adjustments to maintain a single row layout */
                @media (max-width: 600px) {
                    .custom-nav-bar {
                        flex-direction: row !important;
                        justify-content: space-between !important;
                    }
                    .custom-nav-bar .nav-right a {
                        margin-left: 10px !important;
                    }
                }
            </style>

            <div class="custom-nav-bar">
                <div class="nav-left">
                    <!-- Replace with your logo URL -->
                    <img src="https://www.carniceriademadrid.es/wp-content/uploads/2021/03/logo-carniceria-de-madrid.png" alt="Logo">
                </div>
                <div class="nav-right">
                    <a href="https://control.carniceriademadrid.es/wp-admin/admin.php?page=subscribers-staff-attendance">
                        <i class="fas fa-tachometer-alt"></i>
                    </a>
                    <a href="https://control.carniceriademadrid.es/wp-admin/admin.php?page=subscribers-staff-reports">
                        <i class="fas fa-history"></i>
                    </a>
                    <a href="https://control.carniceriademadrid.es/wp-admin/admin.php?page=subscribers-staff-holidays">
                        <i class="fas fa-umbrella-beach"></i>
                    </a>
                    <a href="https://control.carniceriademadrid.es/wp-admin/admin.php?page=subscribers-staff-request">
                        <i class="fas fa-sticky-note"></i>
                    </a>
                </div>
            </div>

            <div>
                <h3 class=""
                    style="background-color: #000; color: #fff; text-align: center; padding: 15px; border-radius: 8px; font-family: Arial, sans-serif; box-shadow: 0 2px 5px rgba(0,0,0,0.3); margin: 20px auto; max-width: 80%;">
                    <?php esc_html_e('Buenos días', CIP_FREE_TXTDM); ?>
                    <br>
                    <?php echo esc_html( ucwords($fname . " " . $lname) ); ?>
                </h3>


                <hr>
                <h2 style="text-align:center;background-color: white;padding: 10px;">
                    <em>
                        <?php
                        setlocale(LC_TIME, 'es_ES.UTF-8');
                        echo strftime("%A, %d de %B de %Y");
                        ?>
                    </em>
                </h2>

            </div>

            <!-- Clock -->
            <div id="cip_clock"
                 style="background: #1e1e1e; padding: 30px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.2); margin: 20px auto; max-width: 600px;">
                <div class="row">

                    <!-- RELOJ  -->
                    <style>
                        /* Outer container for the entire clock */
                        .outer-clock-container {
                            margin: 40px auto !important;
                            max-width: 600px !important;
                            background: linear-gradient(135deg, #111, #000) !important;
                            border-radius: 12px !important;
                            padding: 20px !important;
                            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.5) !important;
                        }

                        /* Grid container for the clock digits */
                        .clock-main-container {
                            display: grid !important;
                            grid-template-columns: repeat(4, 1fr) !important;
                            grid-gap: 10px !important;
                            justify-items: center !important;
                            align-items: center !important;
                        }

                        /* Base digit style - all digits will be uniform */
                        .digit {
                            width: 15vw !important;
                            height: 20vw !important;
                            max-width: 60px !important;
                            max-height: 80px !important;
                            background: #222 !important;
                            border-radius: 5px !important;
                            text-align: center !important;
                            color: #a4a4a4 !important;
                            font-size: 3rem !important;
                            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.5) !important;
                            display: flex !important;
                            align-items: center !important;
                            justify-content: center !important;
                        }

                        /* Ensure the digit text is vertically centered */
                        .digit .base {
                            display: block !important;
                            width: 100%;
                            height: 100%;
                            line-height: 80px !important; /* When at max size */
                        }
                    </style>

                    <div class="outer-clock-container">
                        <div class="clock-main-container">
                            <!-- Order: tens of hours, hours, tens of minutes, minutes, tens of seconds, seconds, AM/PM, extra -->
                            <div class="digit tenhour">
                                <span class="base digit"></span>
                            </div>
                            <div class="digit hour">
                                <span class="base digit"></span>
                            </div>
                            <div class="digit tenmin">
                                <span class="base digit"></span>
                            </div>
                            <div class="digit min">
                                <span class="base digit"></span>
                            </div>
                            <div class="digit tensec">
                                <span class="base digit"></span>
                            </div>
                            <div class="digit sec">
                                <span class="base digit"></span>
                            </div>
                            <div class="digit ampm">
                                <span class="base digit"></span>
                            </div>
                            <div class="digit ampmm">
                                <span class="base digit"></span>
                            </div>
                        </div>
                    </div>


                    <!-- fin de RELOJ  -->

                    <div class="col-md-6 text-center text-md-right" style="margin-top: 2%;">
                        <button <?php echo esc_attr($off_in_disable); ?> type="button" id="office-in-btn"
                                                                         name="office-in-btn"
                                                                         class="btn my-2"
                                                                         style="background-color: #d32f2f; color: #fff; border: none; padding: 12px 20px; border-radius: 5px; width: 100%; max-width: 300px; display: inline-block; margin: 8px auto;"
                                                                         onclick="return OfficeClockInOut('office-in', '<?php echo esc_attr($userid); ?>');">
                            <i class="fas fa-sign-in" aria-hidden="true" style="margin-right: 8px;"></i>
                            <?php esc_html_e(isset($cip_settings['clock_in_btn_text']) ? $cip_settings['clock_in_btn_text'] : "Entrada", CIP_FREE_TXTDM); ?>
                        </button>

                        <button <?php if ($off_in_disable != "disabled") {
                            echo esc_attr('disabled');
                        }
                        echo esc_attr($off_out_disable); ?>
                                type="button" id="office-out-btn" name="office-out-btn"
                                class="btn my-2"
                                style="background-color: #000; color: #fff; border: none; padding: 12px 20px; border-radius: 5px; width: 100%; max-width: 300px; display: inline-block; margin: 8px auto;"
                                onclick="return OfficeClockInOut('office-out', '<?php echo esc_attr($userid); ?>');">
                            <i class="fas fa-sign-out" aria-hidden="true" style="margin-right: 8px;"></i>
                            <?php esc_html_e(isset($cip_settings['clock_out_btn_text']) ? $cip_settings['clock_out_btn_text'] : "Salida", CIP_FREE_TXTDM); ?>
                        </button>

                        <button <?php echo esc_attr($lunch_in_disable); ?> type="button" id="lunch-in-btn"
                                                                           name="lunch-in-btn"
                                                                           class="btn my-2"
                                                                           style="background-color: #d32f2f; color: #fff; border: none; padding: 12px 20px; border-radius: 5px; width: 100%; max-width: 300px; display: inline-block; margin: 8px auto;"
                                                                           onclick="return LunchClockInOut('lunch-in', '<?php echo esc_attr($userid); ?>');">
                            <i class="fas fa-coffee" aria-hidden="true" style="margin-right: 8px;"></i>
                            <?php esc_html_e(isset($cip_settings['lunch_in_btn_text']) ? $cip_settings['lunch_in_btn_text'] : "Comenzar Merienda", CIP_FREE_TXTDM); ?>
                        </button>

                        <button <?php if ($lunch_in_disable != "disabled") {
                            esc_html_e('disabled');
                        }
                        echo esc_attr($lunch_out_disable); ?>
                                type="button" id="lunch-out-btn" name="lunch-out-btn"
                                class="btn my-2"
                                style="background-color: #000; color: #fff; border: none; padding: 12px 20px; border-radius: 5px; width: 100%; max-width: 300px; display: inline-block; margin: 8px auto;"
                                onclick="return LunchClockInOut('lunch-out', '<?php echo esc_attr($userid); ?>');">
                            <i class="fas fa-sign-out" aria-hidden="true" style="margin-right: 8px;"></i>
                            <?php esc_html_e(isset($cip_settings['lunch_out_btn_text']) ? $cip_settings['lunch_out_btn_text'] : "Finalizar Merienda", CIP_FREE_TXTDM); ?>
                        </button>
                    </div>


                </div>
            </div>
            <!-- End -->

            <div id="break-clock-div" class="test-left col-md-12 mb-3"
                 style="margin-top: 2%; background-color: #fff; border: 2px solid #000; padding: 15px; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                <h1 style="color: black; text-align: center; margin-bottom: 15px;">Descanso</h1>
                <br>
                <?php if (@$break_out_message) { ?>
                    <div id="lunch-out-result"
                         style="background-color: #ffe6e6; border: 1px solid #d32f2f; color: #000; padding: 10px; border-radius: 5px; text-align: center;">
                        <?php echo wp_kses_post($break_out_message, CIP_FREE_TXTDM); ?>
                    </div>
                <?php }
                if ($break_in_message) { ?>
                    <div id="break-out-result"
                         style="background-color: #ffe6e6; border: 1px solid #d32f2f; color: #000; padding: 10px; border-radius: 5px; text-align: center;">
                        <?php echo wp_kses_post($break_in_message, CIP_FREE_TXTDM); ?>
                    </div>
                <?php } ?>
                <?php if ($break_out_message == "") { ?>
                    <button <?php echo esc_attr($break_in_disable); ?> type="button" id="break-in-btn"
                                                                       name="break-in-btn"
                                                                       class="btn btn-sm "
                                                                       style="background-color: #d32f2f; color: #fff; border: none; padding: 10px; border-radius: 5px; width: 100%; margin-top: 10px;"
                                                                       onclick="return breakClockInOut('break-in', '<?php echo esc_attr($userid); ?>');">
                        <i class="fas fa-sign-in" aria-hidden="true"></i> Comenzar
                    </button>
                    <?php if ($break_in_disable == "disabled") { ?>
                        <button <?php echo esc_attr($break_out_disable); ?> type="button" id="break-out-btn"
                                                                            name="break-out-btn"
                                                                            class="btn btn-sm"
                                                                            style="background-color: #000; color: #fff; border: none; padding: 10px; border-radius: 5px; width: 100%; margin-top: 10px;"
                                                                            onclick="return breakClockInOut('break-out', '<?php echo esc_attr($userid); ?>');">
                            <i class="fas fa-sign-out" aria-hidden="true"></i> Finalizar
                        </button>
                    <?php } ?>
                <?php } ?>
            </div>


            <div id="office-clock-div" class="text-left col-md-12" style="margin-top: 1%;">
                <?php if ($off_out_message) { ?>
                    <div id="office-out-result"
                         class="alert alert-info"><?php echo wp_kses_post($off_out_message, CIP_FREE_TXTDM); ?></div>
                <?php }
                if ($off_in_message) { ?>
                    <div id="office-in-result"
                         class="alert alert-info"><?php echo wp_kses_post($off_in_message, CIP_FREE_TXTDM); ?></div>
                <?php } ?>
                <br>
            </div>

            <div id="lunch-clock-div" class="text-left col-md-12" style="margin-top: 1%;">
                <?php if ($lunch_out_message) { ?>
                    <div id='lunch-out-result'
                         class='alert alert-info'><?php echo wp_kses_post($lunch_out_message, CIP_FREE_TXTDM); ?></div>
                <?php }
                if ($lunch_in_message) { ?>
                    <div id='lunch-in-result'
                         class='alert alert-info'><?php echo wp_kses_post($lunch_in_message, CIP_FREE_TXTDM); ?></div>
                <?php } ?>
            </div>
        </div>

        <div class="col-md-12">
            <br>
            <hr>
        </div>
        <div id="task" class="col-md-12">

            <?php if ($off_in_disable == "disabled") { ?>

            <div class="row">
                <div id="submit_report" class="col-md-6"
                     style="margin: 2% auto; background-color: #fff; border: 2px solid #000; padding: 20px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.2);">
                    <h3 style="color: #d32f2f; text-align: center; margin-bottom: 20px; font-family: 'Arial', sans-serif;">
                        Informe</h3>
                    <form id="report-form" name="report-form">
                        <p style="text-align: center;">
                            <textarea id="report" name="report"
                                      style="width: 90%; display: block; margin: 0 auto; border: 1px solid #000; border-radius: 5px; padding: 10px; resize: vertical;"
                                      rows="10"><?php esc_html_e($report); ?></textarea>
                        </p>
                        <p style="text-align: center;">
                            <input type="button" id="submit-report" name="submit-report" class="btn btn-lg"
                                   style="background-color: #000; color: #fff; border: none; padding: 12px; border-radius: 5px; width: 90%; margin-top: 15px;"
                                   onclick="return SendReport('<?php echo esc_attr($userid); ?>', '<?php echo esc_attr(date("Y-m-d")); ?>');"
                                   value="Enviar">
                        </p>
                    </form>
                </div>
            </div>


            <div id="upcoming-event" class="col-md-6">
                <div id="office-clock-div" class="text-left col-md-12">
                    <style>
                        .custom-tabs {
                            list-style: none !important;
                            margin: 0 !important;
                            padding: 0 !important;
                            display: flex !important;
                            border-bottom: 2px solid #ddd !important;
                        }
                        .custom-tabs li {
                            margin-right: 20px !important;
                        }
                        .custom-tabs li a {
                            text-decoration: none !important;
                            color: #333 !important;
                            padding: 10px 15px !important;
                            display: inline-block !important;
                            transition: color 0.3s ease, border-bottom 0.3s ease !important;
                        }
                        .custom-tabs li a:hover,
                        .custom-tabs li a.active {
                            color: #000 !important;
                            border-bottom: 2px solid #000 !important;
                        }
                    </style>

                    <ul class="custom-tabs" role="tablist">
                        <li role="presentation">
                            <a class="active" href="#daily-report" aria-controls="home" role="tab" data-toggle="tab">
                                <?php esc_html_e('Historial', CIP_FREE_TXTDM); ?>
                            </a>
                        </li>
                        <li role="presentation">
                            <a href="#upcoming-holiday" aria-controls="in-active-staff" role="tab" data-toggle="tab">
                                <?php esc_html_e('Vacaciones', CIP_FREE_TXTDM); ?>
                            </a>
                        </li>
                        <li role="presentation">
                            <a href="#today-event" aria-controls="in-active-today-event" role="tab" data-toggle="tab">
                                <?php esc_html_e('Eventos', CIP_FREE_TXTDM); ?>
                            </a>
                        </li>
                    </ul>

                    <div class="table-responsive">
                        <div class="tab-content">
                            <?php
                            $date_format = get_option('date_format');
                            $time_format = get_option('time_format');
                            $current_user = wp_get_current_user();
                            $userid = $current_user->ID;
                            $no = 1;
                            $no2 = 1;
                            //this month report
                            $total_attend = 0;
                            $total_absent = 0;
                            $total_day_absent = '';
                            $startdate = date("Y-m-01");
                            $enddate = date("Y-m-d");
                            $i = strtotime($startdate);
                            $j = strtotime($enddate);
                            $all_dates = array();
                            for ($i; $i <= $j; $i = strtotime(date("Y-m-d", strtotime("+1 day", $i)))) {
                                array_push($all_dates, date("Y-m-d", $i));
                            } ?>
                            <div role="tabpanel" class="tab-pane active pt-2" id="daily-report">
                                <div class="row" style="margin: 0px;">
                                    <div id="office-clock-div" class="text-left col-6 text-center">
                                        <h3>Asistencias</h3>
                                        <?php
                                        foreach ($all_dates as $row_date) {
                                            $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM `$staff_attendance_table` WHERE `staff_id` = %d AND `date` LIKE %s", $userid, $row_date));
                                            if ((date("l", strtotime($row_date)) == "Sunday") || (!empty ($row))) {
                                                //check if Sunday else no record found
                                                $total_attend = $total_attend + 1;
                                            }
                                        }
                                        if (isset($total_attend)) {
                                            $total_attend2 = $total_attend;
                                        }
                                        foreach ($all_dates as $row_date) {
                                            if ($holidays = get_option("cip_official_holidays")) {
                                                foreach ($holidays as $key => $holiday) {
                                                    if ((($row_date == $holiday['start_date']))) {
                                                        //check if Sunday else no record found
                                                        $date = $holiday['start_date'];
                                                        $end_date = $holiday['end_date'];
                                                        $dif = 0;
                                                        while (strtotime($date) <= strtotime($end_date)) {
                                                            $date = date("Y-m-d", strtotime("+1 day", strtotime($date)));
                                                            if (date("l", strtotime($date)) != "Sunday") {
                                                                $dif = $dif + 1;
                                                            }
                                                        }
                                                        if (isset($total_attend2)) {
                                                            $total_attend3 = $total_attend2 + $dif;
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                        if (isset($total_attend3)) {
                                            $total_attend4 = $total_attend3;
                                        } else {
                                            $total_attend4 = $total_attend2;
                                        }
                                        if ($holidays = get_option("cip_official_holidays")) {
                                            foreach ($holidays as $key => $holiday) {
                                                $date = $holiday['start_date'];
                                                $end_date = $holiday['end_date'];
                                                $dif2 = 0;
                                                while (strtotime($date) <= strtotime($end_date)) {
                                                    $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM `$staff_attendance_table` WHERE `staff_id` = %d AND `date` LIKE %s", $userid, $date));
                                                    $date = date("Y-m-d", strtotime("+1 day", strtotime($date)));
                                                    if (isset($row->date)) {
                                                        $dif2 = $dif2 + 1;
                                                    }
                                                }
                                                if (isset($total_attend4)) {
                                                    $final_total_attend = $total_attend4 - $dif2;
                                                }
                                            }
                                        }

                                        if (isset($final_total_attend)) {
                                            $final_total_attend = $final_total_attend;
                                        } else {
                                            $final_total_attend = $total_attend4;
                                        }

                                        if (isset($final_total_attend)) {
                                            echo '<p class="report-stat blue">' . $final_total_attend . '</p>';
                                        }
                                        ?>
                                    </div>
                                    <div id="office-clock-div" class="text-left col-6 text-center">
                                        <h3><?php esc_html_e('Ausencias', CIP_FREE_TXTDM); ?></h3>
                                        <?php ?>
                                        <p class="report-stat red" data-toggle="tooltip" data-placement="top"
                                           title="<?php foreach ($total_absent_days as $absent_in_days) {
                                               if (strtotime($absent_in_days))
                                                   $absent_in_days = date($date_format, strtotime($absent_in_days));
                                               print_r($absent_in_days . ',  ');
                                           } ?>">
                                            <?php echo staff_total_absent_days_free($userid); ?></p>
                                    </div>
                                </div>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="upcoming-holiday">
                                <table class="table table-hover table-striped">
                                    <thead>
                                    <tr class="info main_tb_head">
                                        <th>#</th>
                                        <th><?php esc_html_e('Event/Holiday Name', CIP_FREE_TXTDM); ?></th>
                                        <th><?php esc_html_e('Date', CIP_FREE_TXTDM); ?></th>
                                        <th><?php esc_html_e('Day(s)', CIP_FREE_TXTDM); ?></th>
                                    </tr>
                                    </thead>
                                    <?php if ($holidays = get_option("cip_official_holidays")) { ?>
                                        <tbody>
                                        <?php
                                        //Next 12 month
                                        $startdate = new \DateTime(date("Y") . "-01-01");
                                        $startdate = $startdate->format("Y-m-d");
                                        $plusOneYear = date("Y") + 1;
                                        $enddate = new \DateTime($plusOneYear . "-12-31");
                                        $enddate = $enddate->format("Y-m-d");
                                        $i = strtotime($startdate);
                                        $j = strtotime($enddate);
                                        $all_dates = array();
                                        for ($i; $i <= $j; $i = strtotime(date("Y-m-d", strtotime("+1 day", $i)))) {
                                            array_push($all_dates, date("Y-m-d", $i));
                                        }
                                        $n = 1;
                                        foreach ($all_dates as $row_date) {
                                            if (!empty ($holidays)) {

                                                if (!empty($holidays)) {
                                                    foreach ($holidays as $key => $holiday) {
                                                        $status = $holiday['status'];
                                                        if ($status == 1) {
                                                            $start_date = $holiday['start_date'];
                                                            $end_date = $holiday['end_date'];
                                                            if (strtotime($start_date))
                                                                $start_date = date($date_format, strtotime($holiday['start_date']));
                                                            if (strtotime($end_date))
                                                                $end_date = date($date_format, strtotime($holiday['end_date']));
                                                            if ($holiday['start_date'] == $row_date) {
                                                                ?>
                                                                <tr>
                                                                    <td><?php esc_html_e($n . "."); ?></td>
                                                                    <td><?php esc_html_e($holiday['name']); ?></td>
                                                                    <?php if ($end_date == $start_date) { ?>
                                                                        <td><?php if ($end_date != "") {
                                                                                esc_html_e($end_date);
                                                                            } ?></td>
                                                                    <?php } else { ?>
                                                                        <td><?php esc_html_e($start_date);
                                                                            if ($end_date != "") { ?> - <?php esc_html_e($end_date);
                                                                            } ?></td>
                                                                    <?php } ?>
                                                                    <td><?php esc_html_e('For ' . $holiday['leaves'] . ' Day(s)', CIP_FREE_TXTDM); ?></td>
                                                                </tr>
                                                                <?php $n++;
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        } ?>
                                        </tbody>
                                    <?php } else { ?>
                                        <tbody>
                                        <tr>
                                            <td colspan='6'><?php esc_html_e('No Holiday Found.', CIP_FREE_TXTDM); ?></td>
                                        </tr>
                                        </tbody>
                                    <?php } ?>
                                    <thead>
                                    <tr class="info main_tb_head">
                                        <th>#</th>
                                        <th><?php esc_html_e('Event/Holiday Name', CIP_FREE_TXTDM); ?></th>
                                        <th><?php esc_html_e('Date', CIP_FREE_TXTDM); ?></th>
                                        <th><?php esc_html_e('Leave', CIP_FREE_TXTDM); ?></th>
                                    </tr>
                                    </thead>
                                </table>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="today-event">
                                <table class="table table-hover table-striped">
                                    <thead>
                                    <tr class="info main_tb_head">
                                        <th>#</th>
                                        <th><?php esc_html_e('Event', CIP_FREE_TXTDM); ?></th>
                                        <th><?php esc_html_e('description', CIP_FREE_TXTDM); ?></th>
                                        <th><?php esc_html_e('Date', CIP_FREE_TXTDM); ?></th>
                                        <th><?php esc_html_e('Day(s)', CIP_FREE_TXTDM); ?></th>
                                    </tr>
                                    </thead>
                                    <?php if ($staff_event_requests = get_option("cip_staff_event_request")) { ?>
                                        <tbody>
                                        <h3 class="info">Today Event</h3>
                                        <?php if (!empty ($staff_event_requests)) {
                                            $n = 1;
                                            foreach ($staff_event_requests as $staff_event_request) {
                                                $status = $staff_event_request['status'];
                                                if ($status == 1) $status = "Pending";
                                                if ($status == 2) $status = "Approved";
                                                if ($status == 3) $status = "Cancelled";
                                                if ($status == 'Approved') {
                                                    $current_user = wp_get_current_user();
                                                    $fname = $current_user->user_firstname;
                                                    $lname = $current_user->user_lastname;
                                                    $user_name = $fname . ' ' . $lname;
                                                    $date = $staff_event_request['start_date'];
                                                    $end_date = $staff_event_request['end_date'];
                                                    $date2 = array();
                                                    while (strtotime($date) <= strtotime($end_date)) {
                                                        array_push($date2, $date);
                                                        $date = date("Y-m-d", strtotime("+1 day", strtotime($date)));
                                                    }

                                                    $current_date = date("Y-m-d");
                                                    if ($current_date == $staff_event_request['start_date']) {
                                                        $start_date = $staff_event_request['start_date'];
                                                        $end_date = $staff_event_request['end_date'];
                                                        if (strtotime($start_date))
                                                            $start_date = date($date_format, strtotime($staff_event_request['start_date']));
                                                        if (strtotime($end_date))
                                                            $end_date = date($date_format, strtotime($staff_event_request['end_date']));
                                                        ?>
                                                        <tr title="<?php esc_html_e('From - ' . $staff_event_request['user_name']) ?>">
                                                            <td><?php esc_html_e($n . "."); ?></td>
                                                            <td><?php esc_html_e($staff_event_request['name']); ?></td>
                                                            <td><?php esc_html_e($staff_event_request['event_disc']); ?></td>
                                                            <?php if ($end_date == $start_date) { ?>
                                                                <td><?php if ($end_date != "") {
                                                                        esc_html_e($end_date);
                                                                    } ?></td>
                                                            <?php } else { ?>
                                                                <td><?php esc_html_e($start_date);
                                                                    if ($end_date != "") { ?> - <?php esc_html_e($end_date);
                                                                    } ?></td>
                                                            <?php } ?>
                                                            <td><?php esc_html_e('For ' . $staff_event_request['leaves'] . ' Day(s)', CIP_FREE_TXTDM); ?></td>
                                                        </tr>
                                                        <?php $n++;
                                                    }
                                                }

                                            }
                                        } ?>
                                        </tbody>
                                    <?php } else { ?>
                                        <tbody>
                                        <tr>
                                            <td colspan='6'><?php esc_html_e('No Record Found.', CIP_FREE_TXTDM); ?></td>
                                        </tr>
                                        </tbody>
                                    <?php } ?>
                                    <thead>
                                    <tr class="info main_tb_head">
                                        <th>#</th>
                                        <th><?php esc_html_e('Event', CIP_FREE_TXTDM); ?></th>
                                        <th><?php esc_html_e('description', CIP_FREE_TXTDM); ?></th>
                                        <th><?php esc_html_e('Date', CIP_FREE_TXTDM); ?></th>
                                        <th><?php esc_html_e('Day(s)', CIP_FREE_TXTDM); ?></th>
                                    </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <?php } ?>
            </div>
        </div>
        <?php
        wp_register_script('clock-in-staff-attendence-script', false);
        wp_enqueue_script('clock-in-staff-attendence-script');
        $js = " ";
        ob_start(); ?>


        /* New clock Js */
        <?php if (!empty ($cip_settings['cip_timezone'])) {
            date_default_timezone_set($cip_settings['cip_timezone']);
            $time_zone = $cip_settings['cip_timezone'];
        } else {
            $time_zone = 'Asia/Kolkata';
        }
        ?>
        function flipTo(digit, n){
        var current = digit.attr('data-num');
        digit.attr('data-num', n);
        digit.find('.front').attr('data-content', current);
        digit.find('.back, .under').attr('data-content', n);
        digit.find('.flap').css('display', 'block');
        setTimeout(function(){
        digit.find('.base').text(n);
        digit.find('.flap').css('display', 'none');
        }, 350);
        }

        function jumpTo(digit, n){
        digit.attr('data-num', n);
        digit.find('.base').text(n);
        }

        function updateGroup(group, n, flip){
        var digit1 = jQuery('.ten'+group);
        var digit2 = jQuery('.'+group);
        n = String(n);
        if(n.length == 1) n = '0'+n;
        var num1 = n.substr(0, 1);
        var num2 = n.substr(1, 1);
        if(digit1.attr('data-num') != num1){
        if(flip) flipTo(digit1, num1);
        else jumpTo(digit1, num1);
        }
        if(digit2.attr('data-num') != num2){
        if(flip) flipTo(digit2, num2);
        else jumpTo(digit2, num2);
        }
        }

        function setTime(flip){
        var currentUtcTime = new Date(); // This is in UTC
        var currentDateTimeCentralTimeZone = new Date(currentUtcTime.toLocaleString('en-US', { timeZone: '<?php echo esc_attr($time_zone); ?>' }));

        var hours = currentDateTimeCentralTimeZone.getHours();
        var minutes = currentDateTimeCentralTimeZone.getMinutes();
        var sec = currentDateTimeCentralTimeZone.getSeconds();
        var ampm = hours >= 12 ? 'P' : 'A';
        var ampmm = hours >= 12 ? 'M' : 'M';
        hours = hours % 12;
        hours = hours ? hours : 12; // the hour '0' should be '12'
        minutes = minutes < 10 ? '0'+minutes : minutes;

        updateGroup('hour', hours, flip);
        updateGroup('min', minutes, flip);
        updateGroup('sec', sec, flip);
        updateGroup('ampm', ampm, flip);
        updateGroup('ampmm', ampmm, flip);
        }

        jQuery(document).ready(function(){
        setTime(false);
        setInterval(function(){
        setTime(true);
        }, 1000);
        });

        /*** Office Clock ***/
        var OfficeClock = jQuery('.office-clock').FlipClock({
        countdown: false,
        autoStart: true,
        clockFace: 'TwelveHourClock',
        // onStart
        onStart: function(type, userid) {
        var today_date = new Date();
        <?php
        $cip_settings = get_option('cip_settings');
        if (empty($cip_settings['cip_timezone'])) {
            $timeZoneID = 'Asia/kolkata';
        } else {
            $timeZoneID = $cip_settings['cip_timezone'];
        }
        date_default_timezone_set($timeZoneID);
        $timestamp = time();
        $date_time = date("d-m-Y (D) H:i:s", $timestamp);
        $today = getdate(); ?>
        var date = "<?php echo $date = date("d-m-Y"); ?>";
        var time = "<?php echo $date = date("H:i:s"); ?>";
        var data_values = "type=" + type + "&userid=" + userid + "&date=" + date + "&time=" + time;
        if (confirm("<?php echo esc_html(isset($cip_settings['clock_in_alert_text']) ? $cip_settings['clock_in_alert_text'] : "Are you sure want to start your office working session now?") ?>") == true) {
        jQuery("#office-in-btn").prop('disabled', true);
        jQuery.ajax({
        type: "post",
        url: location.href,
        data: data_values,
        contentType: "application/x-www-form-urlencoded",
        success: function(responseData, textStatus, jqXHR) {
        var result = jQuery(responseData).find('div#office-in-result');
        jQuery(".office-clock").after(result);
        location.reload();
        },
        error: function(jqXHR, textStatus, errorThrown) {
        }
        });
        }
        },

        // onStop
        onStop: function(type, userid) {
        var today_date = new Date();
        var date = today_date.getFullYear() + "-" + (today_date.getMonth() + 1) + "-" + today_date.getDate();
        var time = today_date.getHours() + ":" + today_date.getMinutes() + ":" + today_date.getSeconds();
        var data_values = "type=" + type + "&userid=" + userid + "&date=" + date + "&time=" + time;
        var new_time = jQuery.format.date(today_date, "h:m a");
        if (confirm("<?php echo esc_html(isset($cip_settings['clock_out_alert_text']) ? $cip_settings['clock_out_alert_text'] : "You are going to office out at") ?> '"+ new_time +"'") == true) {
        if (confirm("<?php echo esc_html(isset($cip_settings['clock_out_alert_text2']) ? $cip_settings['clock_out_alert_text2'] : "Are you sure and want to office out now?") ?>") == true) {
        jQuery("#office-out-btn").prop('disabled', true);
        jQuery.ajax({
        type: "post",
        url: location.href,
        data: data_values,
        contentType: "application/x-www-form-urlencoded",
        success: function(responseData, textStatus, jqXHR) {
        var result = jQuery(responseData).find('div#office-out-result');
        jQuery(".office-clock").after(result);
        location.reload();
        },
        error: function(jqXHR, textStatus, errorThrown) {
        }
        });
        } else location.reload(); // confirm 2
        } else location.reload(); // confirm 1
        },
        });
        // Office Clock
        function OfficeClockInOut(type, userid) {
        console.log(type + userid);
        // in - start clock
        if(type == "office-in") {
        OfficeClock.start();
        OfficeClock.onStart(type, userid);
        }
        // out - stop clock
        if(type == "office-out") {
        OfficeClock.stop();
        OfficeClock.onStop(type, userid);
        }
        }

        /*** Lunch Clock ***/
        var LunchClock = jQuery('.lunch-clock').FlipClock({
        countdown: false,
        autoStart: true,
        clockFace: 'TwelveHourClock',
        // onStart Lunch
        onStart: function(type, userid) {
        var today_date = new Date();
        var date = today_date.getFullYear() + "-" + (today_date.getMonth() + 1) + "-" + today_date.getDate();
        var time = today_date.getHours() + ":" + today_date.getMinutes() + ":" + today_date.getSeconds();
        var data_values = "type=" + type + "&userid=" + userid + "&date=" + date + "&time=" + time;
        if (confirm("<?php echo esc_html(isset($cip_settings['lunch_in_alert_text']) ? $cip_settings['lunch_in_alert_text'] : "Are you sure want to start your lunch session now?") ?>") == true) {
        jQuery("#lunch-in-btn").prop('disabled', true);
        jQuery.ajax({
        type: "post",
        url: location.href,
        data: data_values,
        contentType: "application/x-www-form-urlencoded",
        success: function(responseData, textStatus, jqXHR) {
        var result = jQuery(responseData).find('div#lunch-in-result');
        jQuery(".lunch-clock").after(result);
        location.reload();
        },
        error: function(jqXHR, textStatus, errorThrown) {
        }
        });
        }
        },
        // onStop Lunch
        onStop: function(type, userid) {
        var today_date = new Date();
        var date = today_date.getFullYear() + "-" + (today_date.getMonth() + 1) + "-" + today_date.getDate();
        var time = today_date.getHours() + ":" + today_date.getMinutes() + ":" + today_date.getSeconds();
        var data_values = "type=" + type + "&userid=" + userid + "&date=" + date + "&time=" + time;
        var new_time = jQuery.format.date(today_date, "h:m a");
        if (confirm("<?php echo esc_html(isset($cip_settings['lunch_out_alert_text']) ? $cip_settings['lunch_out_alert_text'] : "You are going to lunch out at") ?> '"+ new_time +"'") == true) {
        if (confirm("<?php echo esc_html(isset($cip_settings['lunch_out_alert_text2']) ? $cip_settings['lunch_out_alert_text2'] : "Are you sure and want to lunch out now?") ?>") == true) {
        jQuery("#lunch-out-btn").prop('disabled', true);
        jQuery.ajax({
        type: "post",
        url: location.href,
        data: data_values,
        contentType: "application/x-www-form-urlencoded",
        success: function(responseData, textStatus, jqXHR) {
        var result = jQuery(responseData).find('div#lunch-out-result');
        jQuery(".lunch-clock").after(result);
        location.reload();
        },
        error: function(jqXHR, textStatus, errorThrown) {
        }
        });
        } else location.reload(); // confirm 2
        } else location.reload(); // confirm 1
        },
        });
        // Lunch Clock
        function LunchClockInOut(type, userid) {
        console.log(type + userid);
        // in - start clock
        if(type == "lunch-in") {
        LunchClock.start();
        LunchClock.onStart(type, userid);
        }

        // out - stop clock
        if(type == "lunch-out") {
        LunchClock.stop();
        LunchClock.onStop(type, userid);
        }
        }

        // Break Clock
        function breakClockInOut(type, userid) {
        console.log(type + userid);
        // in - start clock
        if(type == "break-in") {
        //Break
        var date = new Date();
        var breakclock = jQuery('.break-clock').FlipClock(date,{
        countdown: true,
        clockFace: 'DailyCounter',
        });
        // onStart
        function break_in(type, userid) {
        var today_date = new Date();
        <?php $cip_settings = get_option('cip_settings');
        if (empty($cip_settings['cip_timezone'])) {
            $timeZoneID = 'Asia/kolkata';
        } else {
            $timeZoneID = $cip_settings['cip_timezone'];
        }
        date_default_timezone_set($timeZoneID);
        $timestamp = time();
        $date_time = date("d-m-Y (D) H:i:s", $timestamp);
        $today = getdate(); ?>
        var date = "<?php echo $date = date("d-m-Y"); ?>";
        var time = "<?php echo $date = date("H:i:s"); ?>";
        var data_values = "type=" + type + "&userid=" + userid + "&date=" + date + "&time=" + time;
        if (confirm("Are you sure want to start your Break session now?") == true) {
        jQuery("#break-in-btn").prop('disabled', true);
        jQuery.ajax({
        type: "post",
        url: location.href,
        data: data_values,
        contentType: "application/x-www-form-urlencoded",
        success: function(responseData, textStatus, jqXHR) {
        var result = jQuery(responseData).find('div#break-in-result');
        jQuery(".break-clock").after(result);
        location.reload(true);
        },
        error: function(jqXHR, textStatus, errorThrown) {
        }
        });
        }
        }

        break_in(type, userid);
        }
        // out - stop clock
        if(type == "break-out") {
        function break_out(type, userid) {
        var today_date = new Date();
        var date = today_date.getFullYear() + "-" + (today_date.getMonth() + 1) + "-" + today_date.getDate();
        var time = today_date.getHours() + ":" + today_date.getMinutes() + ":" + today_date.getSeconds();
        var data_values = "type=" + type + "&userid=" + userid + "&date=" + date + "&time=" + time;
        var new_time = jQuery.format.date(today_date, "h:m a");
        if (confirm("You are going to end your break at '"+ new_time +"'") == true) {
        if (confirm("Are you sure and want to end your break now?") == true) {
        jQuery("#break-out-btn").prop('disabled', true);
        jQuery.ajax({
        type: "post",
        url: location.href,
        data: data_values,
        contentType: "application/x-www-form-urlencoded",
        success: function(responseData, textStatus, jqXHR) {
        var result = jQuery(responseData).find('div#break-out-result');
        jQuery(".break-clock").after(result);
        location.reload();
        },
        error: function(jqXHR, textStatus, errorThrown) {
        }
        });
        } else location.reload(); // confirm 2
        } else location.reload(); // confirm 1
        }
        break_out(type, userid);
        }
        }


        function SendReport(id, date) {
        jQuery("#error").hide();
        jQuery("#report-result").hide();
        var report = jQuery("#report").val();
        if(report == "") {
        jQuery("#report").after("<p id='error'><strong>Required:</strong> type your full report here</p>");
        return false;
        }

        jQuery.ajax({
        type: "post",
        url: location.href,
        data: jQuery("#report-form").serialize() + "&staff_id=" + id + "&date=" + date,
        contentType: "application/x-www-form-urlencoded",
        success: function(responseData, textStatus, jqXHR) {
        var result = jQuery(responseData).find('div#report-result');
        jQuery("#report").after(result);
        },
        error: function(jqXHR, textStatus, errorThrown) {
        }
        });
        }
        <?php
        $js .= ob_get_clean();
        wp_add_inline_script('clock-in-staff-attendence-script', $js);
        wp_register_style('clock-in-staff-attendence-style', false);
        wp_enqueue_style('clock-in-staff-attendence-style');
        $css = " ";
        ob_start(); ?>
        .office-clock {
        padding: 10px;
        }
        .lunch-clock {
        padding: 10px;
        }
        .btn-lg,
        .btn-group-lg > .btn {
        padding: 10px 16px !important;
        font-size: 26px !important;
        line-height: 1.3333333;
        border-radius: 6px;
        }
        .alert {
        font-size: 16px !important;
        }
        <?php
        $css .= ob_get_clean();
        wp_add_inline_style('clock-in-staff-attendence-style', $css); ?>

        <?php
    } elseif ($status == 2 || $Status == 3) {
        echo "<p class='alert alert-danger'>" . __('Sorry! Your account is not activated. Please contact to your higher authority regarding your Inactive account.', CIP_FREE_TXTDM) . "</p>";
    }
} else {
    echo "<p class='alert alert-info'>" . __('Sorry! this page is only available for Registered Staffs', CIP_FREE_TXTDM) . "";
}

// save clocking records
if (isset($_POST['type']) && isset($_POST['userid'])) {
    $type = sanitize_text_field($_POST['type']);
    $userid = sanitize_text_field($_POST['userid']);
    $date = sanitize_text_field(date("Y-m-d", strtotime($_POST['date'])));
    $time = sanitize_text_field(date("H:i:s", strtotime($_POST['time'])));
    $ip = sanitize_text_field($_SERVER['REMOTE_ADDR']);
    $user_location = user_locationn_free($ip);

    $extra = array(
        'SERVER_SOFTWARE' => $_SERVER['SERVER_SOFTWARE'],
        'SERVER_SIGNATURE' => $_SERVER['SERVER_SIGNATURE'],
        'SERVER_NAME' => $_SERVER['SERVER_NAME'],
        'SERVER_ADDR' => $_SERVER['SERVER_ADDR'],
        'REMOTE_ADDR' => $_SERVER['REMOTE_ADDR'],
        'HTTP_USER_AGENT' => $_SERVER['HTTP_USER_AGENT'],
    );
    $extra = sanitize_text_field(serialize($extra));

    // office
    if ($type == "office-in") {

        if ($userdatep = $wpdb->get_row($wpdb->prepare("SELECT * FROM `$staff_attendance_table` WHERE `staff_id` = %d AND `date` = %s", $userid, $prev_date))) {
            $office_outp = $userdatep->office_out;
            $office_inp = $userdatep->office_in;
            $datep = $prev_date;
            $sepparator = '-';
            $parts = explode($sepparator, $datep);
            $dayForDate = date("l", mktime(0, 0, 0, $parts[1], $parts[2], $parts[0]));

            if ($office_outp == "00:00:00" && $office_inp != "00:00:00") {
                $strStart = $office_inp;
                if ($dayForDate == "Saturday") {
                    $strEnd = '15:00:00';
                } else {
                    $strEnd = '19:00:00';
                }
                $dteStart = new DateTime($strStart);
                $dteEnd = new DateTime($strEnd);
                $dteDiff = $dteStart->diff($dteEnd);
                $work_hour = $dteDiff->format("%H:%I:%S");
                $timee = "00:00:00";

                $query = $wpdb->prepare("UPDATE `$staff_attendance_table` SET `today_total_hours` = %s WHERE `staff_id` = %d AND `date` = %s", $work_hour, $userid, $prev_date);

                if ($out = $wpdb->query($query)) {
                    $query = $wpdb->prepare("INSERT INTO `$staff_attendance_table` (`id`, `staff_id`, `office_in`, `office_out`, `lunch_in`, `lunch_out`, `date`, `today_total_hours`, `ip`, `timestamp`, `note`, `extra`, `user_location`) VALUES (NULL, %d, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s );", $userid, $time, '', '', '', $date, '', $ip, date("Y-m-d H:i:s"), '', $extra, $user_location);
                    if ($in = $wpdb->query($query)) {
                        echo "<div id='$type-result' class='alert alert-info'>Your office working session was started at <strong>" . date($time_format, strtotime($_POST['time'])) . "</strong></div>";
                    } else {
                        echo "<div id='$type-result' class='alert alert-danger'>Error: unable to start working session.</div>";
                    }
                }
            } else {
                $query = $wpdb->prepare("INSERT INTO `$staff_attendance_table` (`id`, `staff_id`, `office_in`, `office_out`, `lunch_in`, `lunch_out`, `date`, `today_total_hours`, `ip`, `timestamp`, `note`, `extra`, `user_location`) VALUES (NULL, %d, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s );", $userid, $time, '', '', '', $date, '', $ip, date("Y-m-d H:i:s"), '', $extra, $user_location);
                if ($in = $wpdb->query($query)) {
                    echo "<div id='$type-result' class='alert alert-info'>Your office working session was started at <strong>" . date($time_format, strtotime($_POST['time'])) . "</strong></div>";
                } else {
                    echo "<div id='$type-result' class='alert alert-danger'>Error: unable to start working session.</div>";
                }
            }
        } else {
            $query = $wpdb->prepare("INSERT INTO `$staff_attendance_table` (`id`, `staff_id`, `office_in`, `office_out`, `lunch_in`, `lunch_out`, `date`, `today_total_hours`, `ip`, `timestamp`, `note`, `extra`, `user_location`) VALUES (NULL, %d, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s );", $userid, $time, '', '', '', $date, '', $ip, date("Y-m-d H:i:s"), '', $extra, $user_location);
            if ($in = $wpdb->query($query)) {
                echo "<div id='$type-result' class='alert alert-info'>Your office working session was started at <strong>" . date($time_format, strtotime($_POST['time'])) . "</strong></div>";
            } else {
                echo "<div id='$type-result' class='alert alert-danger'>Error: unable to start working session.</div>";
            }
        }

    }

    if ($type == "office-out") {
        $today_total_hours = "00:00:00";
        // total hours calculation
        if ($userdate = $wpdb->get_row($wpdb->prepare("SELECT * FROM `$staff_attendance_table` WHERE `staff_id` = %d AND `date` = %s", $userid, $date))) {
            $office_in = $userdate->office_in;
            $office_out = $time;
            $strStart = $office_in;
            $strEnd = $office_out;
            $dteStart = new DateTime($strStart);
            $dteEnd = new DateTime($strEnd);
            $dteDiff = $dteStart->diff($dteEnd);
            $today_total_hours = $dteDiff->format("%H:%I:%S");

        }
        $query = $wpdb->prepare("UPDATE `$staff_attendance_table` SET `office_out` = %s, `today_total_hours` = %s WHERE `staff_id` = %d AND `date` = %s", $time, $today_total_hours, $userid, $date);
        if ($out = $wpdb->query($query)) {
            echo "<div id='$type-result' class='alert alert-info'>
					<p>Your today's office session was completed at <strong>" . date($time_format, strtotime($_POST['time'])) . "</strong></p>
					<p>Your today's Total Working Hours is <strong>" . $today_total_hours . "</strong> hours</p>
				  </div>";
        } else {
            echo "<div id='$type-result' class='alert alert-danger'>Error: unable to complete end session.</div>";
        }
    }

    //lunch
    if ($type == "lunch-in") {
        $query = $wpdb->prepare("UPDATE `$staff_attendance_table` SET `lunch_in` = %s WHERE `staff_id` = %d AND `date` = %s", $time, $userid, $date);
        if ($in = $wpdb->query($query)) {
            echo "<div id='$type-result' class='alert alert-info'>Your lunch session was started at <strong>" . date($time_format, strtotime($_POST['time'])) . "</strong></div>";
        } else {
            echo "<div id='$type-result' class='alert alert-danger'>Error: unable to start lunch session.</div>";
        }
    }

    if ($type == "lunch-out") {
        $query = $wpdb->prepare("UPDATE `$staff_attendance_table` SET `lunch_out` = %s WHERE `staff_id` = %d AND `date` = %s", $time, $userid, $date);
        if ($in = $wpdb->query($query)) {
            echo "<div id='$type-result' class='alert alert-info'>Your lunch session was completed at <strong>" . date($time_format, strtotime($_POST['time'])) . "</strong></div>";
        } else {
            echo "<div id='$type-result' class='alert alert-danger'>Error: unable to end lunch session.</div>";
        }
    }

    //Break
    if ($type == "break-in") {
        $query = $wpdb->prepare("UPDATE `$staff_attendance_table` SET `break_in` = %s WHERE `staff_id` = %d AND `date` = %s", $time, $userid, $date);
        if ($in = $wpdb->query($query)) {

        } else {
            echo "<div id='$type-result' class='alert alert-danger'>Error: unable to start Break session.</div>";
        }
    }

    if ($type == "break-out") {
        $query = $wpdb->prepare("UPDATE `$staff_attendance_table` SET `break_out` = %s WHERE `staff_id` = %d AND `date` = %s", $time, $userid, $date);
        if ($in = $wpdb->query($query)) {
            echo "<div id='$type-result' class='alert alert-info'>Your Break session was completed at <strong>" . date($time_format, strtotime($_POST['time'])) . "</strong></div>";
        } else {
            echo "<div id='$type-result' class='alert alert-danger'>Error: unable to end Break session.</div>";
        }
    }

}

//submit report
if (isset($_POST['staff_id'])) {
    $staff_id = sanitize_text_field($_POST['staff_id']);
    $report = $_POST['report'];
    $date = sanitize_text_field($_POST['date']);
    $query = $wpdb->prepare("UPDATE `$staff_attendance_table` SET `report` = %s WHERE `staff_id` = %d AND `date` = %s", $report, $userid, $date);
    if ($in = $wpdb->query($query)) {
    }
    ?>
    <div id='report-result' class='alert alert-info'>
        <strong><?php esc_html_e('Success:', CIP_FREE_TXTDM); ?></strong> <?php esc_html_e('Report submitted successfully.', CIP_FREE_TXTDM); ?>
    </div>
    <?php
}
?>
