<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

//Function to sum working hours //
function AddPlayTime($times) {
    $minutes = 0; //declare minutes either it gives Notice: Undefined variable
    // loop throught all the times
    foreach ($times as $time) {
        list($hour, $minute) = explode(':', $time);
        $minutes += $hour * 60;
        $minutes += $minute;
    }

    $hours = floor($minutes / 60);
    $minutes -= $hours * 60;

    // returns the time already formatted
    return sprintf('%02d Hours and %02d Min', $hours, $minutes);
}

global $wpdb;
$staff_attendance_table = $wpdb->base_prefix . "sm_attendance";
$staff_table = $wpdb->base_prefix . "sm_staffs";
$staff_category_table = $wpdb->base_prefix . "sm_staff_category";

$date_format = get_option('date_format');
$time_format = get_option('time_format');

$current_date = date("Y-m-d");
// get user details & id
$current_user = wp_get_current_user();
$username = $current_user->user_login;
$email = $current_user->user_email;
$fname = $current_user->user_firstname;
$lname = $current_user->user_lastname;
$fullname = ucwords($fname." ".$lname);
$userid = $current_user->ID;
$region = $city = $country = '';

//check logged-in user is Shift Monitor existing User (by ID)
if($userdata = $wpdb->get_row($wpdb->prepare("SELECT * FROM `$staff_table` WHERE `staff_id` = %d", $userid))) {
	// check user is active user
	$status = $userdata->status;
	if($status == 1) {

		$filter_name = "1";
		$upload_dir_all = wp_upload_dir();
		$upload_dir_path = $upload_dir_all['basedir'];
		$upload_dir_url = $upload_dir_all['baseurl'];
		$all_dates = array();

		if(isset($_POST['filter_name'])) {
			$filter_name = $_POST['filter_name'];
		}

		//this month report
		if ( $filter_name == '1' )
			{
				$first = date( "Y-m-01" );
				$last  = date( "Y-m-t", strtotime( $first ) );
				$all_days_report = range_date_free( $first, $last );

			} elseif ( $filter_name == '2' ) {

				$first = date( "Y-m-01", strtotime( "-1 month" ) );
				$last  = date( "Y-m-t", strtotime( $first ) );
				$all_days_report = range_date_free( $first, $last );

			}  elseif ( $filter_name == '3' ) {

				$first = date( "Y-m-01", strtotime( "-2 month" ) );
				$last  = date( "Y-m-t", strtotime( $first ) );
				$all_days_report = range_date_free( $first, $last );

			}  elseif ( $filter_name == '4' ) {

				$first = date( "Y-m-01", strtotime( "-3 month" ) );
				$last  = date( "Y-m-t", strtotime( $first ) );
				$all_days_report = range_date_free( $first, $last );

			}  elseif ( $filter_name == '5' ) {

				$first = date( "Y-m-01", strtotime( "-4 month" ) );
				$last  = date( "Y-m-t", strtotime( $first ) );
				$all_days_report = range_date_free( $first, $last );

			}  elseif ( $filter_name == '6' ) {

				$first = date( "Y-m-01", strtotime( "-5 month" ) );
				$last  = date( "Y-m-t", strtotime( $first ) );
				$all_days_report = range_date_free( $first, $last );

			}  elseif ( $filter_name == '7' ) {

				$first = date( "Y-m-01", strtotime( "-6 month" ) );
				$last  = date( "Y-m-t", strtotime( $first ) );
				$all_days_report = range_date_free( $first, $last );

			}  elseif ( $filter_name == '8' ) {

				$first = date( "Y-m-01", strtotime( "-7 month" ) );
				$last  = date( "Y-m-t", strtotime( $first ) );
				$all_days_report = range_date_free( $first, $last );

			}  elseif ( $filter_name == '9' ) {

				$first = date( "Y-m-01", strtotime( "-8 month" ) );
				$last  = date( "Y-m-t", strtotime( $first ) );
				$all_days_report = range_date_free( $first, $last );

			}  elseif ( $filter_name == '10' ) {

				$first = date( "Y-m-01", strtotime( "-9 month" ) );
				$last  = date( "Y-m-t", strtotime( $first ) );
				$all_days_report = range_date_free( $first, $last );

			}  elseif ( $filter_name == '11' ) {

				$first = date( "Y-m-01", strtotime( "-10 month" ) );
				$last  = date( "Y-m-t", strtotime( $first ) );
				$all_days_report = range_date_free( $first, $last );

			}  elseif ( $filter_name == '12' ) {

				$first = date( "Y-m-01", strtotime( "-11 month" ) );
				$last  = date( "Y-m-t", strtotime( $first ) );
				$all_days_report = range_date_free( $first, $last );

			}  elseif ( $filter_name == '13' ) {

				$first = date( "Y-m-01", strtotime( "-12 month" ) );
				$last  = date( "Y-m-t", strtotime( $first ) );
				$all_days_report = range_date_free( $first, $last );

			} elseif ( $filter_name == '14' ) {

				$first = date( "Y-m-01", strtotime( "-3 month" ) );
				$last  = date( "Y-m-t", strtotime( $first ) );
				$last  = date( "Y-m-d", strtotime( "+2 month", strtotime( $last ) ) );
				$all_days_report = range_date_free( $first, $last );

			} elseif( $filter_name == "15" ) {

				$first = date( "Y-m-01", strtotime( "-6 month" ) );
				$last  = date( "Y-m-t", strtotime( $first ) );
				$last  = date( "Y-m-d", strtotime( "+5 month", strtotime( $last ) ) );
				$all_days_report = range_date_free( $first, $last );

			} elseif( $filter_name == "16" ) {

				$first = date( "Y-m-01", strtotime( "-9 month" ) );
				$last  = date( "Y-m-t", strtotime( $first ) );
				$last  = date( "Y-m-d", strtotime( "+8 month", strtotime( $last ) ) );
				$all_days_report = range_date_free( $first, $last );

			} elseif($filter_name == "17") {

				$first = date( "Y-m-01", strtotime( "-12 month" ) );
				$last  = date( "Y-m-t", strtotime( $first ) );
				$last  = date( "Y-m-d", strtotime( "+11 month", strtotime( $last ) ) );
				$all_days_report = range_date_free( $first, $last );

			}
		?>

		<?php
		wp_register_style( 'clock-in-self-report-style', false );
		wp_enqueue_style( 'clock-in-self-report-style' );
		$css = " ";
		ob_start(); ?>
			table {
				background-color: #FFFFFF !important;
			}
		<?php
		$css .= ob_get_clean();
		wp_add_inline_style( 'clock-in-self-report-style', $css ); ?>

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
                <img href="https://control.carniceriademadrid.es/wp-admin/admin.php?page=subscribers-staff-attendance" src="https://www.carniceriademadrid.es/wp-content/uploads/2021/03/logo-carniceria-de-madrid.png" alt="Logo">
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
                <!-- Account Icon -->
                <a href="https://control.carniceriademadrid.es/account/">
                    <i class="fas fa-user"></i>
                </a>
            </div>
        </div>
		<h1><?php esc_html_e('Historial', CIP_FREE_TXTDM );?></h1>
		<!-- filter table-->
		<?php
			/* New code for Month filter */
			$current_mnth      = date("F Y");
			$previous_mnth     = date( "F Y", strtotime( "-1 month" ) );
			$previous_mnth_1   = date( "F Y", strtotime( "-2 month" ) );
			$previous_mnth_2   = date( "F Y", strtotime( "-3 month" ) );
			$previous_mnth_3   = date( "F Y", strtotime( "-4 month" ) );
			$previous_mnth_4   = date( "F Y", strtotime( "-5 month" ) );
			$previous_mnth_5   = date( "F Y", strtotime( "-6 month" ) );
			$previous_mnth_6   = date( "F Y", strtotime( "-7 month" ) );
			$previous_mnth_7   = date( "F Y", strtotime( "-8 month" ) );
			$previous_mnth_8   = date( "F Y", strtotime( "-9 month" ) );
			$previous_mnth_9   = date( "F Y", strtotime( "-10 month" ) );
			$previous_mnth_10  = date( "F Y", strtotime( "-11 month" ) );
			$previous_mnth_11  = date( "F Y", strtotime( "-12 month" ) );
		 ?>
		<br>
		<form id="get-report" name="get-report" method="POST">
		<table class="table">
			<tr>
				<td class="sm-labels">
					<?php esc_html_e('Ver mes: ', CIP_FREE_TXTDM );?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<select id="filter_name" name="filter_name">
						<optgroup label="Selecciona cualquier mes">
							<option value="1" <?php if($filter_name == "1") esc_html_e( "selected=selected" ); ?>><?php esc_html_e( $current_mnth, CIP_FREE_TXTDM );?></option>
							<option value="2" <?php if($filter_name == "2") esc_html_e( "selected=selected" ); ?>><?php esc_html_e( $previous_mnth, CIP_FREE_TXTDM );?></option>
							<option value="3" <?php if($filter_name == "3") esc_html_e( "selected=selected" ); ?>><?php esc_html_e( $previous_mnth_1, CIP_FREE_TXTDM );?></option>
							<option value="4" <?php if($filter_name == "4") esc_html_e( "selected=selected" ); ?>><?php esc_html_e( $previous_mnth_2, CIP_FREE_TXTDM );?></option>
							<option value="5" <?php if($filter_name == "5") esc_html_e( "selected=selected" ); ?>><?php esc_html_e( $previous_mnth_3, CIP_FREE_TXTDM );?></option>
							<option value="6" <?php if($filter_name == "6") esc_html_e( "selected=selected" ); ?>><?php esc_html_e( $previous_mnth_4, CIP_FREE_TXTDM );?></option>
							<option value="7" <?php if($filter_name == "7") esc_html_e( "selected=selected" ); ?>><?php esc_html_e( $previous_mnth_5, CIP_FREE_TXTDM );?></option>
							<option value="8" <?php if($filter_name == "8") esc_html_e( "selected=selected" ); ?>><?php esc_html_e( $previous_mnth_6, CIP_FREE_TXTDM );?></option>
							<option value="9" <?php if($filter_name == "9") esc_html_e( "selected=selected" ); ?>><?php esc_html_e( $previous_mnth_7, CIP_FREE_TXTDM );?></option>
							<option value="10" <?php if($filter_name == "10") esc_html_e( "selected=selected" ); ?>><?php esc_html_e( $previous_mnth_8, CIP_FREE_TXTDM );?></option>
							<option value="11" <?php if($filter_name == "11") esc_html_e( "selected=selected" ); ?>><?php esc_html_e( $previous_mnth_9, CIP_FREE_TXTDM );?></option>
							<option value="12" <?php if($filter_name == "12") esc_html_e( "selected=selected" ); ?>><?php esc_html_e( $previous_mnth_10, CIP_FREE_TXTDM );?></option>
							<option value="13" <?php if($filter_name == "13") esc_html_e( "selected=selected" ); ?>><?php esc_html_e( $previous_mnth_11, CIP_FREE_TXTDM );?></option>
						</optgroup>
						<optgroup label="En grupo">
							<option value="14" <?php if($filter_name == "14") esc_html_e( "selected=selected" ); ?>><?php esc_html_e('Tres últimos meses', CIP_FREE_TXTDM );?></option>
							<option value="15" <?php if($filter_name == "15") esc_html_e( "selected=selected" ); ?>><?php esc_html_e('Seis últimos meses', CIP_FREE_TXTDM );?></option>
							<option value="16" <?php if($filter_name == "16") esc_html_e( "selected=selected" ); ?>><?php esc_html_e('Nueve últimos meses', CIP_FREE_TXTDM );?></option>
							<option value="17" <?php if($filter_name == "17") esc_html_e( "selected=selected" ); ?>><?php esc_html_e('Último año', CIP_FREE_TXTDM );?></option>
						</optgroup>
					</select>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<button type="submit" class="btn btn-info mt-2"><?php esc_html_e('Obtener Informe', CIP_FREE_TXTDM );?></button>
				</td>
				<td>

				</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
			<tr class="custom-filter" style="display: none;">
				<td class="sm-labels"><?php esc_html_e('Fecha de inicio', CIP_FREE_TXTDM );?></td>
				<td><input type="date" id="start_date" name="start_date"></td>
				<td class="sm-labels"><?php esc_html_e('Fecha de finalización', CIP_FREE_TXTDM );?></td>
				<td><input type="date" id="end_date" name="end_date"></td>
			</tr>
		</table>
		</form>
		<br>

		<!-- record table-->
		<table class="table table-stripped">
			<thead>
            <tr class="info main_tb_head">
                <th>#</th>
                <th><?php esc_html_e('Nombre', CIP_FREE_TXTDM );?></th>
                <th><?php esc_html_e('Fecha', CIP_FREE_TXTDM );?></th>
                <th><?php esc_html_e('Entrada', CIP_FREE_TXTDM );?></th>
                <th><?php esc_html_e('Salida', CIP_FREE_TXTDM );?></th>
<!--                <th>--><?php //esc_html_e('Inicio Almuerzo', CIP_FREE_TXTDM );?><!--</th>-->
<!--                <th>--><?php //esc_html_e('Fin Almuerzo', CIP_FREE_TXTDM );?><!--</th>-->
                <th><?php esc_html_e('Tiempo de Descanso', CIP_FREE_TXTDM );?></th>
                <th><?php esc_html_e('Horas Trabajadas', CIP_FREE_TXTDM );?></th>
                <th><?php esc_html_e('IP', CIP_FREE_TXTDM );?></th>
                <th><?php esc_html_e('Ubicación', CIP_FREE_TXTDM );?></th>
                <th><?php esc_html_e('Informes', CIP_FREE_TXTDM );?></th>
            </tr>

            <thead>
			<tbody>
			<?php
				/* New code for Month filter */
				$previous_mnth_1   = date( "F-Y", strtotime( "-2 meses" ) );
				$previous_mnth_2   = date( "F-Y", strtotime( "-3 meses" ) );
				$previous_mnth_3   = date( "F-Y", strtotime( "-4 meses" ) );
				$previous_mnth_4   = date( "F-Y", strtotime( "-5 month" ) );
				$previous_mnth_5   = date( "F-Y", strtotime( "-6 month" ) );
				$previous_mnth_6   = date( "F-Y", strtotime( "-7 month" ) );
				$previous_mnth_7   = date( "F-Y", strtotime( "-8 month" ) );
				$previous_mnth_8   = date( "F-Y", strtotime( "-9 month" ) );
				$previous_mnth_9   = date( "F-Y", strtotime( "-10 month" ) );
				$previous_mnth_10  = date( "F-Y", strtotime( "-11 month" ) );
				$previous_mnth_11  = date( "F-Y", strtotime( "-12 month" ) );

				//create a file
				if( $filter_name == "1" ) $file_name = $fname."-".$lname."-informe-del-mes.csv";
				elseif( $filter_name == "2" ) $file_name = $fname."-".$lname."-previous-month-report.csv";

				elseif( $filter_name == "3" ) $file_name = $fname."-".$lname."-".$previous_mnth_1."-report.csv";
				elseif( $filter_name == "4" ) $file_name = $fname."-".$lname."-".$previous_mnth_2."-report.csv";
				elseif( $filter_name == "5" ) $file_name = $fname."-".$lname."-".$previous_mnth_3."-report.csv";
				elseif( $filter_name == "6" ) $file_name = $fname."-".$lname."-".$previous_mnth_4."-report.csv";
				elseif( $filter_name == "7" ) $file_name = $fname."-".$lname."-".$previous_mnth_5."-report.csv";
				elseif( $filter_name == "8" ) $file_name = $fname."-".$lname."-".$previous_mnth_6."-report.csv";
				elseif( $filter_name == "9" ) $file_name = $fname."-".$lname."-".$previous_mnth_7."-report.csv";
				elseif( $filter_name == "10" ) $file_name = $fname."-".$lname."-".$previous_mnth_8."-report.csv";
				elseif( $filter_name == "11" ) $file_name = $fname."-".$lname."-".$previous_mnth_9."-report.csv";
				elseif( $filter_name == "12" ) $file_name = $fname."-".$lname."-".$previous_mnth_10."-report.csv";
				elseif( $filter_name == "13" ) $file_name = $fname."-".$lname."-".$previous_mnth_11."-report.csv";

				elseif($filter_name == "14") $file_name = $fname."-".$lname."-previous-three-month-report.csv";
				elseif($filter_name == "15") $file_name = $fname."-".$lname."-previous-six-month-report.csv";
				elseif($filter_name == "16") $file_name = $fname."-".$lname."-previous-nine-month-report.csv";
				elseif($filter_name == "17") $file_name = $fname."-".$lname."-previous-one-year-report.csv";
				elseif($filter_name == "all") $file_name = $fname."-".$lname."-all-time-report.csv";

				$report_file = fopen($upload_dir_path."/".$file_name, "w") or die("Unable to create report file!");
				$headertext = "ID, Fecha, Nombre, Entrada, Salida, Inicio Almuerzo, Fin Almuerzo, Tiempo de descanso, Horas trabajadas, IP, Ubicación \n";
				fwrite($report_file, $headertext);
				$fullname = ucwords($fname." ".$lname);

				$no = 1;
				$flag = 0;
				$work_array1 = array();
				$work_array2 = array();
				$work_array3 = array();
				foreach($all_days_report as $row_date) {
					 $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM `$staff_attendance_table` WHERE `staff_id` = %d AND `date` LIKE %s", $userid, $row_date));

					if ( ! empty ( $row ) ) {
						$flag = 1;
						$id = $row->id;
						$date = $row->date;
						$office_in = $row->office_in;
						$office_out = $row->office_out;
//						$lunch_in = $row->lunch_in;
//						$lunch_out = $row->lunch_out;
						$extra = @unserialize($row->extra);
						$sever_name = $_SERVER['SERVER_NAME'];
						$sever_ip_address = $_SERVER['SERVER_ADDR'];
						$sever_remote_ip_address = $_SERVER['REMOTE_ADDR'];
						$sever_bwoser_system_details = $_SERVER['HTTP_USER_AGENT'];
						$server_software = $_SERVER['SERVER_SOFTWARE'];
						$server_signature = $_SERVER['SERVER_SIGNATURE'];
						$report = $row->report;
						//Location fetch
						$user_ip = $row->ip;

						$datep = $row->date;
						if( $office_out!='00:00:00' ) {
							$dteStart = new DateTime($row->office_in);
							$dteEnd   = new DateTime($row->office_out);
							$dteDiff  = $dteStart->diff($dteEnd);
							$work_hour = $dteDiff->format("%H:%I:%S");
							array_push($work_array1,$work_hour);
						} else {
							$work_hour = $row->today_total_hours;
							array_push($work_array2,$work_hour);
						}

						// if any time == 00:00:00
						if($office_in == "00:00:00") $office_in = "None"; else $office_in = date($time_format, strtotime($office_in));
						if($office_out == "00:00:00") $office_out = "None"; else $office_out = date($time_format, strtotime($office_out));
						if($lunch_in == "00:00:00") $lunch_in = "None"; else $lunch_in = date($time_format, strtotime($lunch_in));
						if($lunch_out == "00:00:00") $lunch_out = "None"; else $lunch_out = date($time_format, strtotime($lunch_out));

						if( isset( $row->break_in ) && ! empty ( $row->break_in ) ) {
							$dteStart = new DateTime($row->break_in);
							$dteEnd   = new DateTime($row->break_out);
							$dteDiff  = $dteStart->diff($dteEnd);
							$break_hour = $dteDiff->format("%H:%I:%S");
						} else {
							$break_hour = '00:00:00';
						}

						if(date("l", strtotime($date)) == "Sunday") {
							$date = date($date_format, strtotime($date));
							$office_in = "";
							$office_out = "";
							$lunch_in = "";
							$lunch_out = "";
							$sever_ip_address = "";
							$report = "";
						} else {
							$date = date($date_format, strtotime($date));
						}
					?>
				<tr class="<?php if($office_in == "Sunday") esc_html_e( "success sunday_tb"); ?>">
					<td><?php esc_html_e( $no); ?>.</td>
					<td><?php esc_html_e( $fullname); ?></td>
					<td><?php esc_html_e( $date); ?></td>
					<td><?php esc_html_e( $office_in); ?></td>
					<td><?php esc_html_e( $office_out); ?></td>
<!--					<td>--><?php //esc_html_e( $lunch_in); ?><!--</td>-->
<!--					<td>--><?php //esc_html_e( $lunch_out); ?><!--</td>-->
					<td>
					<?php esc_html_e( $break_hour); ?>
					</td>
					<td><?php esc_html_e( $work_hour); ?></td>
					<td><?php esc_html_e( $user_ip); ?></td>
					<td><?php if(isset($row->user_location)){ esc_html_e( $row->user_location); } ?></td>
					<td><?php if($office_in != "None") { ?>
						<button id="view-report" name="view-report" class="btn btn-default" data-toggle="modal" data-backdrop="false" data-target="#myModal" onclick="return ViewReport('<?php echo esc_attr($id); ?>', '<?php echo esc_attr($userid); ?>', '<?php echo esc_attr($date); ?>');"><?php esc_html_e('Ver Informe', CIP_FREE_TXTDM );?></button></td>
						<?php } else { esc_html_e( 'No report'); } ?>
				</tr>
				<?php
						//add records
						$date = date("d M y", strtotime($date));
						$txt = "$no, $date, $fullname, $office_in, $office_out, $lunch_in, $lunch_out, $break_hour, work_hour, $user_ip, $region,$city,$country \n";
						fwrite($report_file, $txt);
					} else {
						//check if Sunday else no record found
						if(date("l", strtotime($row_date)) == "Sunday") {
							$date = date($date_format, strtotime($row_date));
							$office_in = "-";
							$office_out = "-";
							$lunch_in = "-";
							$lunch_out = "-";
							$user_ip = "-";
							$work_hour = "-";
							$report = "-";
							$break_hour = '-';
						} else {
							$date = date($date_format, strtotime($row_date));
							$office_in = "-";
							$office_out = "-";
							$lunch_in = "-";
							$lunch_out = "-";
							$user_ip = "-";
							$report = "-";
							$work_hour = "-";
							$break_hour = "-";
						}
				?>
				<tr class="<?php if($office_in == "Sunday") esc_html_e( "success sunday_tb"); ?>">
					<td><?php esc_html_e( $no); ?>.</td>
					<td><?php esc_html_e( $fullname); ?></td>
					<td><?php esc_html_e( $date); ?></td>
					<td><?php esc_html_e( $office_in); ?></td>
					<td><?php esc_html_e( $office_out); ?></td>
<!--					<td>--><?php //esc_html_e( $lunch_in); ?><!--</td>-->
<!--					<td>--><?php //esc_html_e( $lunch_out); ?><!--</td>-->
					<td>
					<?php esc_html_e( $break_hour); ?>
					</td>
					<td><?php esc_html_e( $work_hour); ?></td>
					<td><?php esc_html_e( $user_ip); ?></td>
					<td><?php if(isset($row->user_location)) { esc_html_e( $row->user_location); } else { esc_html_e( "None"); } ?></td>
					<td><?php esc_html_e( $report); ?></td>
				</tr>
				<?php
						//add no record found
						$date = date("d M y", strtotime($date));
						$txt = "$no, $date, $fullname, $office_in, $office_out, $lunch_in, $lunch_out, $break_hour,  $work_hour, $user_ip, \n";
						fwrite($report_file, $txt);
					}
					$no++;
				}//end of foreach
				if($flag) {
				$work_hour = array();
				$work_hourup = array_merge($work_array1,$work_array2,$work_array3);
				?>
				<tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td><?php echo AddPlayTime($work_hourup); ?></td><td></td><td></td><td></td></tr>
				 <tr><td colspan=8><a href="<?php echo esc_url( $upload_dir_url."/".$file_name ) ?>" class='btn btn-success '><i class='fas fa-download' aria-hidden='true'></i><?php esc_html_e(' Descargar Informe', CIP_FREE_TXTDM );?></a></td><td></td><td></td><td></td></tr>
				<?php }	?>
			</tbody>

		</table>
		<?php
	} elseif($status == 2 || $Status == 3) { ?>
		<p class='alert alert-danger'><?php esc_html_e('Sorry! Your account is not activated. Please contact to your higher authority regarding your Inactive account.', CIP_FREE_TXTDM );?></p>
<?php	}
} else { ?>
	<?php esc_html_e('Lo sentimos, no tienes suficientes permisos para acceder a esta página', CIP_FREE_TXTDM );?>
<?php }
// fetch report
if(isset($_POST['id']) && isset($_POST['staff_id']) && isset($_POST['date'])) {
	$id = $_POST['id'];
	$staff_id = $_POST['staff_id'];
	$date = date("Y-m-d", strtotime($_POST['date']));

	$view_query = $wpdb->prepare("SELECT * FROM `$staff_attendance_table` WHERE `id` = %d AND `staff_id` = %d AND `date` = %s", $id, $staff_id, $date);
	if($report_data = $wpdb->get_row($view_query)) {
		$id = $report_data->id;
		$date = $report_data->date;
		$office_in = $report_data->office_in;
		$office_out = $report_data->office_out;
		$lunch_in = $report_data->lunch_in;
		$lunch_out = $report_data->lunch_out;
		$report = $report_data->report;
		$user_ip = $report_data->ip;

		if($office_out!='00:00:00')
			{
				$dteStart = new DateTime($report_data->office_in);
				$dteEnd   = new DateTime($report_data->office_out);
				$dteDiff  = $dteStart->diff($dteEnd);
				$work_hour = $dteDiff->format("%H:%I:%S");
			} else
			{
				$work_hour = $report_data->today_total_hours;
			}
		if($office_in != "00:00:00") $office_in = date($time_format, strtotime($office_in));
		if($office_out != "00:00:00") $office_out = date($time_format, strtotime($office_out));
		if($lunch_in != "00:00:00") $lunch_in = date($time_format, strtotime($lunch_in));
		if($lunch_out != "00:00:00") $lunch_out = date($time_format, strtotime($lunch_out));

		$dteStart = new DateTime($report_data->break_in);
		$dteEnd   = new DateTime($report_data->break_out);
		$dteDiff  = $dteStart->diff($dteEnd);
		$break_hour = $dteDiff->format("%H:%I:%S");
		?>
		<div id="view-report-result" >
			<table class="table table-bordered">
				<tr>
					<th><?php esc_html_e('Nombre', CIP_FREE_TXTDM );?></th>
					<td><?php  esc_html_e( ucwords($fname." ".$lname)); ?></td>
				<tr>
				<tr>
					<th><?php esc_html_e('Fecha', CIP_FREE_TXTDM );?></th>
					<td><?php  esc_html_e( $date); ?></td>
				<tr>
				<tr>
					<th><?php esc_html_e('Entrada', CIP_FREE_TXTDM );?></th>
					<td><?php  esc_html_e( $office_in); ?></td>
				<tr>
				<tr>
					<th><?php esc_html_e('Salida', CIP_FREE_TXTDM );?></th>
					<td><?php  esc_html_e( $office_out); ?></td>
				<tr>
				<tr>
					<th><?php esc_html_e('Inicio Almuerzo', CIP_FREE_TXTDM );?></th>
					<td><?php  esc_html_e( $lunch_in); ?></td>
				<tr>
				<tr>
					<th><?php esc_html_e('Fin Almuerzo', CIP_FREE_TXTDM );?></th>
					<td><?php  esc_html_e( $lunch_out); ?></td>
				<tr>
				<tr>
					<th><?php esc_html_e('Tiempo de descanso', CIP_FREE_TXTDM );?></th>
					<td><?php  esc_html_e( $break_hour); ?></td>
				<tr>
				<tr>
					<th><?php esc_html_e('Horas trabajadas', CIP_FREE_TXTDM );?></th>
					<td><?php  esc_html_e( $work_hour); ?></td>
				<tr>
				<tr>
					<th><?php esc_html_e('IP', CIP_FREE_TXTDM );?></th>
					<td><?php  esc_html_e( $user_ip); ?></td>
				<tr>
				<tr>
					<th><?php esc_html_e('Ubicación', CIP_FREE_TXTDM );?></th>
					<td><?php if($office_in != "None") { echo esc_attr( $report_data->user_location); } else { esc_html_e( "None"); } ?></td>
				<tr>
				<tr>
					<th colspan="2" class="text-center"><?php esc_html_e('Work Report', CIP_FREE_TXTDM );?></th>
				<tr>
				<tr>
					<td colspan="2"><?php echo "<pre>".$report."</pre>"; ?></td>
				<tr>
			</table>
		</div>
		<?php
	}
}// if fetch report
?>
<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel"><?php esc_html_e('Ver detalles', CIP_FREE_TXTDM );?></h4>
			</div>
			<div class="modal-body">
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-danger" data-dismiss="modal"><?php esc_html_e('Close', CIP_FREE_TXTDM );?></button>
			</div>
		</div>
	</div>
</div>
<?php
wp_register_script( 'clock-in-subs-staff-reports-script', false );
wp_enqueue_script( 'clock-in-subs-staff-reports-script' );
$js = " ";
ob_start(); ?>
function ViewReport(id, staff_id, date){
	console.log(id + staff_id);
	jQuery("#view-report-result").remove();
	jQuery.ajax({
		type: "post",
		url: location.href,
		data: "&id=" + id + "&staff_id=" + staff_id + "&date=" + date,
		contentType: "application/x-www-form-urlencoded",
		success: function(responseData, textStatus, jqXHR) {
			var result = jQuery(responseData).find('div#view-report-result');
			jQuery(".modal-body").html(result);
		},
		error: function(jqXHR, textStatus, errorThrown) {
		}
	});
}

// modal js
jQuery('#myModal').on('shown.bs.modal', function () {
  jQuery('#myInput').focus()
});

<?php
$js .= ob_get_clean();
wp_add_inline_script( 'clock-in-subs-staff-reports-script', $js ); ?>