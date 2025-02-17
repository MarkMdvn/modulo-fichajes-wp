<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

wp_enqueue_script('jquery');
wp_enqueue_script('jquery-ui-core');
wp_enqueue_script('jquery-ui-datepicker');
wp_enqueue_style('jquery-ui-css', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');

global $wpdb;
$staff_attendance_table = $wpdb->base_prefix . "sm_attendance";
$staff_table = $wpdb->base_prefix . "sm_staffs";
$staff_category_table = $wpdb->base_prefix . "sm_staff_category";
$date_format = get_option('date_format');
$time_format = get_option('time_format');
$current_date = date("Y-m-d");
$filter_name = "1";
$region = $city = $country = '';
if(isset($_POST['filter_name'])) {
	$filter_name = $_POST['filter_name'];
}
$attend_filter_name = "1";
if(isset($_POST['attend_filter_name'])) {
	$attend_filter_name = $_POST['attend_filter_name'];
}
$userid = -2;
if(isset($_POST['staff_id'])) {
	$userid = $_POST['staff_id'];
}
$all_dates = array();

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
?>
<?php 
wp_register_style( 'clock-in-report-style', false );
wp_enqueue_style( 'clock-in-report-style' );
$css = " ";
ob_start(); ?>
	table {
		background-color: #FFFFFF !important;
	}
<?php
$css .= ob_get_clean();
wp_add_inline_style( 'clock-in-report-style', $css ); ?>

<nav class="navbar navbar-dark bg-dark main-dashboard-cip other-pages">
	<a class="navbar-brand" href="<?php echo esc_url( admin_url( 'admin.php?page=clock-in-portal' ) ); ?>"><i class="fas fa-home"></i></a>
  	<a class="navbar-brand" href="#"><?php esc_html_e('Mange Reports', CIP_FREE_TXTDM ); ?></a>
  	<div class="form-inline my-2 my-lg-0">
      	<a class="navbar-brand" href="<?php echo esc_url( admin_url( 'admin.php?page=cip-settings' ) ); ?>"><i class="fas fa-cog"></i></a>
    </div>
</nav>
<div class="wl_im">
<!-- filter table-->
<br>
<form id="get-report" name="get-report" method="POST">
<table class="table">
	<tr>
		<td class="sm-labels">
			<!-- staffs -->
			<?php esc_html_e('Staff(s)', CIP_FREE_TXTDM );?>
			<select id="staff_id" name="staff_id">
				<optgroup label="Select Staff">
				<?php
				if (! empty ( $all_staffs = $wpdb->get_results( $wpdb->prepare("SELECT * FROM `$staff_table` WHERE `status` = %d", 1) ) ) ) {
					foreach( $all_staffs as $staff_data ) {
						$staff_id = $staff_data->staff_id;
						$user_info = get_userdata( $staff_id );
						if(!empty($user_info)){						
						$email = $user_info->user_email;
						$fname = $user_info->first_name;
						$lname = $user_info->last_name;
						$fullname = ucwords($fname." ".$lname);
						?>
						<option value="<?php echo esc_attr($staff_id); ?>" <?php if($userid == $staff_id) echo esc_attr( "selected=selected"); ?>><?php esc_html_e($fullname); ?> (<?php esc_html_e($email); ?>)</option>
						<?php
					} }
				} else { ?>
				<option readonly><?php esc_html_e('No Staff Available', CIP_FREE_TXTDM );?></option>
				<?php 
				} ?>
				</optgroup>
			</select>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<?php esc_html_e('Filter', CIP_FREE_TXTDM );?>
			<!-- filter -->
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
			<select id="filter_name" name="filter_name">
				<optgroup label="Select Any Filter ( individual Months )">
					<option value="1" <?php if($filter_name == "1") echo esc_attr("selected=selected"); ?>><?php esc_html_e( $current_mnth, CIP_FREE_TXTDM );?></option>
					<option value="2" <?php if($filter_name == "2") echo esc_attr("selected=selected"); ?>><?php esc_html_e( $previous_mnth, CIP_FREE_TXTDM );?></option>
					<option value="3" <?php if($filter_name == "3") echo esc_attr("selected=selected"); ?>><?php esc_html_e( $previous_mnth_1, CIP_FREE_TXTDM );?></option>
					<option value="4" <?php if($filter_name == "4") echo esc_attr("selected=selected"); ?>><?php esc_html_e( $previous_mnth_2, CIP_FREE_TXTDM );?></option>
					<option value="5" <?php if($filter_name == "5") echo esc_attr("selected=selected"); ?>><?php esc_html_e( $previous_mnth_3, CIP_FREE_TXTDM );?></option>
					<option value="6" <?php if($filter_name == "6") echo esc_attr("selected=selected"); ?>><?php esc_html_e( $previous_mnth_4, CIP_FREE_TXTDM );?></option>
					<option value="7" <?php if($filter_name == "7") echo esc_attr("selected=selected"); ?>><?php esc_html_e( $previous_mnth_5, CIP_FREE_TXTDM );?></option>
					<option value="8" <?php if($filter_name == "8") echo esc_attr("selected=selected"); ?>><?php esc_html_e( $previous_mnth_6, CIP_FREE_TXTDM );?></option>
					<option value="9" <?php if($filter_name == "9") echo esc_attr("selected=selected"); ?>><?php esc_html_e( $previous_mnth_7, CIP_FREE_TXTDM );?></option>
					<option value="10" <?php if($filter_name == "10") echo esc_attr("selected=selected"); ?>><?php esc_html_e( $previous_mnth_8, CIP_FREE_TXTDM );?></option>
					<option value="11" <?php if($filter_name == "11") echo esc_attr("selected=selected"); ?>><?php esc_html_e( $previous_mnth_9, CIP_FREE_TXTDM );?></option>
					<option value="12" <?php if($filter_name == "12") echo esc_attr("selected=selected"); ?>><?php esc_html_e( $previous_mnth_10, CIP_FREE_TXTDM );?></option>
					<option value="13" <?php if($filter_name == "13") echo esc_attr("selected=selected"); ?>><?php esc_html_e( $previous_mnth_11, CIP_FREE_TXTDM );?></option>
				</optgroup>
				<optgroup label="Select Any Filter ( Combine Months )">
					<option value="14" <?php if($filter_name == "14") echo esc_attr("selected=selected"); ?>><?php esc_html_e('Previous Three Month', CIP_FREE_TXTDM );?></option>
					<option value="15" <?php if($filter_name == "15") echo esc_attr("selected=selected"); ?>><?php esc_html_e('Previous Six Month', CIP_FREE_TXTDM );?></option>
					<option value="16" <?php if($filter_name == "16") echo esc_attr("selected=selected"); ?>><?php esc_html_e('Previous Nine Month', CIP_FREE_TXTDM );?></option>
					<option value="17" <?php if($filter_name == "17") echo esc_attr("selected=selected"); ?>><?php esc_html_e('Previous One Year', CIP_FREE_TXTDM );?></option>
				</optgroup>
			</select>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<select id="attend_filter_name" name="attend_filter_name">
				<optgroup label="Select Any Filter">
				<option value="1" <?php if($attend_filter_name == "1") echo esc_attr("selected=selected"); ?>><?php esc_html_e('All Days', CIP_FREE_TXTDM );?></option>
				<option value="2" <?php if($attend_filter_name == "2") echo esc_attr("selected=selected"); ?>><?php esc_html_e('Only Attend days', CIP_FREE_TXTDM );?></option>
				<option value="3" <?php if($attend_filter_name == "3") echo esc_attr("selected=selected"); ?>><?php esc_html_e('Only Absent Days', CIP_FREE_TXTDM );?></option>
				</optgroup>
			</select>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<button type="submit" class="btn btn-info get_report_btn"><?php esc_html_e('Get Report', CIP_FREE_TXTDM );?></button>
		</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>		
	</tr>
</table>
</form>

	<?php
	if(isset($_POST['filter_name']) && isset($_POST['staff_id'])) {
		$filter_name = sanitize_text_field($_POST['filter_name']);
		$attend_filter_name = sanitize_text_field($_POST['attend_filter_name']);
		$userid = sanitize_text_field($_POST['staff_id']);
		
		// check id user exist
		if($user_info = get_userdata( $userid )) {
			//get user info
			$user_info = get_userdata( $userid );
			$email = $user_info->useresc_html_email;
			$fname = $user_info->first_name;
			$lname = $user_info->last_name;
			$fullname = ucwords($fname." ".$lname);
		}

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

		if( $filter_name == 1 ) $filter_selected =$current_date;
		elseif( $filter_name == 2 ) $filter_selected ="Past $previous_mnth Month";
		elseif( $filter_name == 3 ) $filter_selected ="Past $previous_mnth_1 Month";
		elseif( $filter_name == 4 ) $filter_selected ="Past $previous_mnth_2 Month";
		elseif( $filter_name == 5 ) $filter_selected ="Past $previous_mnth_3 Month";
		elseif( $filter_name == 6 ) $filter_selected ="Past $previous_mnth_4 Month";
		elseif( $filter_name == 7 ) $filter_selected ="Past $previous_mnth_5 Month";
		elseif( $filter_name == 8 ) $filter_selected ="Past $previous_mnth_6 Month";
		elseif( $filter_name == 9 ) $filter_selected ="Past $previous_mnth_7 Month";
		elseif( $filter_name == 10 ) $filter_selected ="Past $previous_mnth_8 Month";
		elseif( $filter_name == 11 ) $filter_selected ="Past $previous_mnth_9 Month";
		elseif( $filter_name == 12 ) $filter_selected ="Past $previous_mnth_10 Month";
		elseif( $filter_name == 13 ) $filter_selected ="Past $previous_mnth_11 Month";
		elseif( $filter_name == 14 ) $filter_selected ="Past Three Month";
		elseif( $filter_name == 15 ) $filter_selected ="Past Six Month";
		elseif( $filter_name == 16 ) $filter_selected ="Past Nine Month";
		elseif( $filter_name == 17 ) $filter_selected ="Past One Year";

		elseif($attend_filter_name == 1) $attend_filter_selected ="All days";
		elseif($attend_filter_name == 2) $attend_filter_selected ="Only Attend days";
		elseif($attend_filter_name == 3) $attend_filter_selected ="Only absent days";
		echo "<h2>$filter_selected Report Generated For: $fullname</h2>";
	}
	?>

<!-- record table-->
<table class="table table-striped" id="report_table" class="display" style="width:100%">
	<thead>
		<tr class="info main_tb_head">
			<th>#</th>
			<th><?php esc_html_e('Name', CIP_FREE_TXTDM );?></th>
			<th><?php esc_html_e('Date', CIP_FREE_TXTDM );?></th>
			<th><?php esc_html_e('Office In', CIP_FREE_TXTDM );?></th>
			<th><?php esc_html_e('Office Out', CIP_FREE_TXTDM );?></th>
			<th><?php esc_html_e('Lunch In', CIP_FREE_TXTDM );?></th>
			<th><?php esc_html_e('Lunch Out', CIP_FREE_TXTDM );?></th>
			<th><?php esc_html_e('Break Time', CIP_FREE_TXTDM );?></th>
			<th><?php esc_html_e('Work Hour', CIP_FREE_TXTDM );?></th>
			<th><?php esc_html_e('IP', CIP_FREE_TXTDM );?></th>
			<th><?php esc_html_e('Location', CIP_FREE_TXTDM );?></th>
			<th><?php esc_html_e('Action', CIP_FREE_TXTDM );?></th>
		</tr>
	<thead>
		<?php if(isset($_POST['filter_name']) && isset($_POST['staff_id'])) { ?>
	<tbody>
	<?php
	
		$filter_name = sanitize_text_field($_POST['filter_name']);
		$userid = sanitize_text_field($_POST['staff_id']);
		$attend_filter_name = sanitize_text_field($_POST['attend_filter_name']);
		// check id user exist
		if($user_info = get_userdata( $userid )) {
			//get user info
			$user_info = get_userdata( $userid );
			$email = $user_info->useresc_html_email;
			$fname = $user_info->first_name;
			$lname = $user_info->last_name;
			$fullname = ucwords($fname." ".$lname);
			//set file parameter
			$upload_dir_all = wp_upload_dir();
			$upload_dir_path = $upload_dir_all['basedir'];
			$upload_dir_url = $upload_dir_all['baseurl'];

			/* New code for Month filter */
			$previous_mnth_1   = date( "F-Y", strtotime( "-2 month" ) );
			$previous_mnth_2   = date( "F-Y", strtotime( "-3 month" ) );
			$previous_mnth_3   = date( "F-Y", strtotime( "-4 month" ) );
			$previous_mnth_4   = date( "F-Y", strtotime( "-5 month" ) );
			$previous_mnth_5   = date( "F-Y", strtotime( "-6 month" ) );
			$previous_mnth_6   = date( "F-Y", strtotime( "-7 month" ) );
			$previous_mnth_7   = date( "F-Y", strtotime( "-8 month" ) );
			$previous_mnth_8   = date( "F-Y", strtotime( "-9 month" ) );
			$previous_mnth_9   = date( "F-Y", strtotime( "-10 month" ) );
			$previous_mnth_10  = date( "F-Y", strtotime( "-11 month" ) );
			$previous_mnth_11  = date( "F-Y", strtotime( "-12 month" ) );


			if ( $attend_filter_name == "1" ) {

				if( $filter_name == "1" ) $file_name = $fname."-".$lname."-this-month-report.csv";
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

			} elseif( $attend_filter_name == "2" ) {

				if($filter_name == "1") $file_name = $fname."-".$lname."-this-month-Attend-report.csv";
				elseif($filter_name == "2") $file_name = $fname."-".$lname."-previous-month-Attend-report.csv";

				elseif( $filter_name == "3" ) $file_name = $fname."-".$lname."-".$previous_mnth_1."Attend-report.csv";
				elseif( $filter_name == "4" ) $file_name = $fname."-".$lname."-".$previous_mnth_2."Attend-report.csv";
				elseif( $filter_name == "5" ) $file_name = $fname."-".$lname."-".$previous_mnth_3."Attend-report.csv";
				elseif( $filter_name == "6" ) $file_name = $fname."-".$lname."-".$previous_mnth_4."Attend-report.csv";
				elseif( $filter_name == "7" ) $file_name = $fname."-".$lname."-".$previous_mnth_5."Attend-report.csv";
				elseif( $filter_name == "8" ) $file_name = $fname."-".$lname."-".$previous_mnth_6."Attend-report.csv";
				elseif( $filter_name == "9" ) $file_name = $fname."-".$lname."-".$previous_mnth_7."Attend-report.csv";
				elseif( $filter_name == "10" ) $file_name = $fname."-".$lname."-".$previous_mnth_8."Attend-report.csv";
				elseif( $filter_name == "11" ) $file_name = $fname."-".$lname."-".$previous_mnth_9."Attend-report.csv";
				elseif( $filter_name == "12" ) $file_name = $fname."-".$lname."-".$previous_mnth_10."Attend-report.csv";
				elseif( $filter_name == "13" ) $file_name = $fname."-".$lname."-".$previous_mnth_11."Attend-report.csv";

				elseif($filter_name == "3") $file_name = $fname."-".$lname."-previous-three-month-Attend-report.csv";
				elseif($filter_name == "6") $file_name = $fname."-".$lname."-previous-six-month-Attend-report.csv";
				elseif($filter_name == "9") $file_name = $fname."-".$lname."-previous-nine-month-Attend-report.csv";
				elseif($filter_name == "12") $file_name = $fname."-".$lname."-previous-one-year-Attend-report.csv";
				elseif($filter_name == "all") $file_name = $fname."-".$lname."-all-time-Attend-report.csv";		

			} elseif( $attend_filter_name == "3" ) {

				if($filter_name == "1") $file_name = $fname."-".$lname."-this-month-leave-report.csv";
				elseif($filter_name == "2") $file_name = $fname."-".$lname."-previous-month-leave-report.csv";

				elseif( $filter_name == "3" ) $file_name = $fname."-".$lname."-".$previous_mnth_1."leave-report.csv";
				elseif( $filter_name == "4" ) $file_name = $fname."-".$lname."-".$previous_mnth_2."leave-report.csv";
				elseif( $filter_name == "5" ) $file_name = $fname."-".$lname."-".$previous_mnth_3."leave-report.csv";
				elseif( $filter_name == "6" ) $file_name = $fname."-".$lname."-".$previous_mnth_4."leave-report.csv";
				elseif( $filter_name == "7" ) $file_name = $fname."-".$lname."-".$previous_mnth_5."leave-report.csv";
				elseif( $filter_name == "8" ) $file_name = $fname."-".$lname."-".$previous_mnth_6."leave-report.csv";
				elseif( $filter_name == "9" ) $file_name = $fname."-".$lname."-".$previous_mnth_7."leave-report.csv";
				elseif( $filter_name == "10" ) $file_name = $fname."-".$lname."-".$previous_mnth_8."leave-report.csv";
				elseif( $filter_name == "11" ) $file_name = $fname."-".$lname."-".$previous_mnth_9."leave-report.csv";
				elseif( $filter_name == "12" ) $file_name = $fname."-".$lname."-".$previous_mnth_10."leave-report.csv";
				elseif( $filter_name == "13" ) $file_name = $fname."-".$lname."-".$previous_mnth_11."leave-report.csv";

				elseif($filter_name == "3") $file_name = $fname."-".$lname."-previous-three-month-leave-report.csv";
				elseif($filter_name == "6") $file_name = $fname."-".$lname."-previous-six-month-leave-report.csv";
				elseif($filter_name == "9") $file_name = $fname."-".$lname."-previous-nine-month-leave-report.csv";
				elseif($filter_name == "12") $file_name = $fname."-".$lname."-previous-one-year-leave-report.csv";
				elseif($filter_name == "all") $file_name = $fname."-".$lname."-all-time-leave-report.csv";	

			}
			//create a file
			$report_file = fopen($upload_dir_path."/".$file_name, "w") or die("Unable to create report file!");
			$headertext = "No., Date, Name, Office In, Office Out, Lunch In, lunch Out, Break Time, Working Hours, IP, Location \n";
			fwrite($report_file, $headertext);
			$fullname = ucwords($fname." ".$lname);

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
			
			$no = 1;
			$flag = 0;
			$work_array1 = array();
			$work_array2 = array();
			$work_array3 = array();

			foreach( $all_days_report as $row_date ) {
				$row = $wpdb->get_row( $wpdb->prepare("SELECT * FROM `$staff_attendance_table` WHERE `staff_id` = %d AND `date` LIKE %s", $userid, $row_date) );

			if ( $attend_filter_name == "1" ) {	
				if( ! empty( $row ) ) {
					$flag = 1;
					$id = $row->id;
					$date = $row->date;
					$office_in = $row->office_in;
					$office_out = $row->office_out;
					$datep = $row->date;
					if($office_out!='00:00:00')
						{
							$dteStart = new DateTime($row->office_in); 
							$dteEnd   = new DateTime($row->office_out); 
							$dteDiff  = $dteStart->diff($dteEnd); 
							$work_hour = $dteDiff->format("%H:%I:%S");
							array_push($work_array1,$work_hour);
						} else {
							$work_hour = $row->today_total_hours;
							array_push($work_array2,$work_hour);
						}
					
					$lunch_in = $row->lunch_in;
					$lunch_out = $row->lunch_out;
					$lunch_out = $row->lunch_out;
					$user_ip = $row->ip;

					if( isset( $row->break_in ) && ! empty ( $row->break_in ) ) {
						$dteStart = new DateTime($row->break_in); 
						$dteEnd   = new DateTime($row->break_out); 
						$dteDiff  = $dteStart->diff($dteEnd); 
						$break_hour = $dteDiff->format("%H:%I:%S");
					} else {
						$break_hour = '00:00:00';
					}

					// if any time == 00:00:00 
					if($office_in == "00:00:00") $office_in = "None"; else $office_in = date($time_format, strtotime($office_in));
					if($office_out == "00:00:00") $office_out = "None"; else $office_out = date($time_format, strtotime($office_out));
					if($lunch_in == "00:00:00") $lunch_in = "None"; else $lunch_in = date($time_format, strtotime($lunch_in));
					if($lunch_out == "00:00:00") $lunch_out = "None"; else $lunch_out = date($time_format, strtotime($lunch_out));
					if(date("l", strtotime($date)) == "Sunday") {
						$date = date($date_format, strtotime($date));
						$office_in = "Sunday";
						$office_out = "Sunday";
						$lunch_in = "Sunday";
						$lunch_out = "Sunday";
						$today_total_hours = "Sunday";
						$user_ip = "Sunday";
						$work_hour = "Sunday";
					} else {
						$date = date($date_format, strtotime($date));
					}					
				?>
			<tr class="<?php if($office_in == "Sunday") esc_html_e( "success sunday_tb"); ?>">
				<td><?php esc_html_e( $no ); ?>.</td>
				<td><?php esc_html_e( $fullname ); ?></td>
				<td><?php esc_html_e( date( $date_format, strtotime( $row->date ) ) ); ?></td>
				<td><?php esc_html_e( $office_in ); ?></td>
				<td><?php esc_html_e( $office_out ); ?></td>
				<td><?php esc_html_e( $lunch_in ); ?></td>
				<td><?php esc_html_e( $lunch_out ); ?></td>
				<td>
					<?php esc_html_e($break_hour ); ?>
				</td>
				<td><?php if($office_in != "00:00:00") { echo esc_attr( $work_hour ); } else { echo esc_attr( 'None'); } ?></td>
				<td><?php if($office_in != "") { echo esc_attr( $user_ip ); } else { echo esc_attr( "None"); } ?></td>
				<td><?php if($office_in != "00:00:00") { echo esc_attr( $row->user_location ); } else { echo esc_attr( "None"); } ?></td>
				<td><?php if($office_in != "00:00:00") { ?>
					<button id="view-report" name="view-report" class="btn btn-info" data-toggle="modal" data-target="#ViewModal" onclick="return ViewReport('<?php echo esc_attr($id); ?>', '<?php echo esc_attr($userid); ?>', '<?php echo esc_attr($row->date); ?>');">View Report</button>
					<button id="edit-record" name="edit-record" class="btn btn-primary" data-toggle="modal" data-target="#EditModal" onclick="return EditRecord('<?php echo esc_attr($id); ?>', '<?php echo esc_attr($userid); ?>', '<?php echo esc_attr($row->date); ?>');">Edit</button>
					<?php } else { echo esc_attr( 'No report'); } ?>
				</td>
			</tr>
			<?php
					//add records
					$date = date("d M y", strtotime($date));
					$txt = "$no, $date, $fullname, $office_in, $office_out, $lunch_in, $lunch_out, $break_hour, $work_hour , $user_ip, $region,$city,$country \n";
					fwrite($report_file, $txt);
				} else {
					//check if Sunday else no record found
					if(date("l", strtotime($row_date)) == "Sunday") {
						$date = date($date_format, strtotime($row_date));
						$office_in = "Sunday";
						$office_out = "Sunday";
						$lunch_in = "Sunday";
						$lunch_out = "Sunday";
						$work_hour = "Sunday";
						$user_ip = "Sunday";
						$break_hour = "Sunday";
					} else {
						$date = date($date_format, strtotime($row_date));
						$office_in = "Sorry";
						$office_out = "No";
						$lunch_in = "Record";
						$lunch_out = "Found";
						$work_hour = '';
						$user_ip = "";
						$break_hour = '';
					}
			?>
			<tr class="<?php if($office_in == "Sunday") esc_html_e( "success sunday_tb"); ?>">
				<td><?php esc_html_e( $no ); ?>.</td>
				<td><?php esc_html_e( $fullname ); ?></td>
				<td><?php esc_html_e( $date ); ?></td>
				<td><?php esc_html_e( $office_in ); ?></td>
				<td><?php esc_html_e( $office_out ); ?></td>
				<td><?php esc_html_e( $lunch_in ); ?></td>
				<td><?php esc_html_e( $lunch_out ); ?></td>
				<td>
					<?php esc_html_e( $break_hour ); ?>
				</td>
				<td><?php if($office_in != "00:00:00") { echo esc_attr( $work_hour ); } else { echo esc_attr( 'None'); } ?></td>
				<td><?php if($office_in != "") { echo esc_attr( $user_ip ); } else { echo esc_attr( "None"); } ?></td>
				<td><?php if(isset($row->user_location)){ echo esc_attr( $row->user_location ); } ?></td>
				<td>&nbsp;</td>
			</tr>
			<?php
					//add no record found
					$date = date( "d M y", strtotime( $date ) );
					$txt  = "$no, $date, $fullname, $office_in, $office_out, $lunch_in, $lunch_out, $break_hour, $work_hour, $user_ip, $region,$city,$country \n";
					fwrite($report_file, $txt);
					
				}
				$no++;
			} elseif ( $attend_filter_name == "2" ) {
				if ( ( date( "l", strtotime( $row_date ) ) != "Sunday" ) && ( ! empty ( $row ) ) ) { 
					//check if Sunday else no record found					
					$flag = 1;
					$id = $row->id;
					$date = $row->date;
					$office_in = $row->office_in;
					$office_out = $row->office_out;
					$lunch_in = $row->lunch_in;
					$lunch_out = $row->lunch_out;
					$lunch_out = $row->lunch_out;

					$user_ip = $row->ip;
					$query = @unserialize(file_get_contents('http://ip-api.com/php/'.$user_ip));
					if($query && $query['status'] == 'success') {
					  $region = $query['regionName'].""; 
				      $city = $query['city'].""; 
				      $country = $query['country'];
					}

					if($office_out!='00:00:00')
						{
							$dteStart = new DateTime($row->office_in); 
							$dteEnd   = new DateTime($row->office_out); 
							$dteDiff  = $dteStart->diff($dteEnd); 
							$work_hour = $dteDiff->format("%H:%I:%S");
							array_push($work_array1,$work_hour);
						} else
						{
							$work_hour = $row->today_total_hours;
							array_push($work_array2,$work_hour);
						}

					if( isset( $row->break_in ) && ! empty ( $row->break_in ) ) {
						$dteStart = new DateTime($row->break_in); 
						$dteEnd   = new DateTime($row->break_out); 
						$dteDiff  = $dteStart->diff($dteEnd); 
						$break_hour = $dteDiff->format("%H:%I:%S");
					} else {
						$break_hour = '00:00:00';
					}

					// if any time == 00:00:00 
					if($office_in == "00:00:00") $office_in = "None"; else $office_in = date($time_format, strtotime($office_in));
					if($office_out == "00:00:00") $office_out = "None"; else $office_out = date($time_format, strtotime($office_out));
					if($lunch_in == "00:00:00") $lunch_in = "None"; else $lunch_in = date($time_format, strtotime($lunch_in));
					if($lunch_out == "00:00:00") $lunch_out = "None"; else $lunch_out = date($time_format, strtotime($lunch_out));
					if(date("l", strtotime($date)) == "Sunday") {
						$date = date($date_format, strtotime($date));
						$office_in = "Sunday";
						$office_out = "Sunday";
						$lunch_in = "Sunday";
						$lunch_out = "Sunday";
						$today_total_hours = "Sunday";
						$user_ip = "Sunday";
						$work_hour = "Sunday";	
					} else {
						$date = date($date_format, strtotime($date));
					}
					//}
				
			?>
			<tr class="<?php if($office_in == "Sunday") esc_html_e( "success sunday_tb"); ?>">
				<td><?php esc_html_e( $no ); ?>.</td>
				<td><?php esc_html_e( $fullname ); ?></td>
				<td><?php esc_html_e( $date ); ?></td>
				<td><?php esc_html_e( $office_in ); ?></td>
				<td><?php esc_html_e( $office_out ); ?></td>
				<td><?php esc_html_e( $lunch_in ); ?></td>
				<td><?php esc_html_e( $lunch_out ); ?></td>
				<td>
					<?php esc_html_e( $break_hour ); ?>
				</td>
				<td><?php if($office_in != "00:00:00") { echo esc_attr( $work_hour ); } else { echo esc_attr( 'None'); } ?></td>
				<td><?php if($office_in != "") { echo esc_attr( $user_ip ); } else { echo esc_attr( "None"); } ?></td>
				<td><?php if($office_in != "00:00:00") { echo esc_attr( $row->user_location ); } else { echo esc_attr( "None"); } ?></td>
				<td><?php if($office_in != "00:00:00") { ?>
					<button id="view-report" name="view-report" class="btn btn-info" data-toggle="modal" data-target="#ViewModal" onclick="return ViewReport('<?php echo esc_attr($id); ?>', '<?php echo esc_attr($userid); ?>', '<?php echo esc_attr($row->date); ?>');">View Report</button>
					<button id="edit-record" name="edit-record" class="btn btn-primary" data-toggle="modal" data-target="#EditModal" onclick="return EditRecord('<?php echo esc_attr($id); ?>', '<?php echo esc_attr($userid); ?>', '<?php echo esc_attr($row->date); ?>');">Edit</button>
					<?php } else { echo esc_attr( 'No report'); } ?>
				</td>
			</tr>
			<?php
					//add no record found
					$date = date("d M y", strtotime( $date ) );
					$txt = "$no, $date, $fullname, $office_in, $office_out, $lunch_in, $lunch_out, $break_hour, $work_hour, $user_ip, $region,$city,$country \n";
					fwrite($report_file, $txt);
					$no++;
				}
				
			} elseif ( $attend_filter_name == "3" ) {
				if ( ( date( "l", strtotime( $row_date ) ) != "Sunday") && ( empty ( $row ) ) ) { 
					//check if Sunday else no record found					
						$flag =1;
						$date = date( $date_format, strtotime( $row_date ) );
						$office_in = "Sorry";
						$office_out = "No";
						$lunch_in = "Record";
						$lunch_out = "Found";
						$user_ip = "";
						$work_hour = "";
					//}
				
			?>
			<tr class="<?php if($office_in == "Sunday") esc_html_e( "success sunday_tb"); ?>">
				<td><?php esc_html_e( $no ); ?>.</td>
				<td><?php esc_html_e( $fullname ); ?></td>
				<td><?php esc_html_e( $date ); ?></td>
				<td><?php esc_html_e( $office_in ); ?></td>
				<td><?php esc_html_e( $office_out ); ?></td>
				<td><?php esc_html_e( $lunch_in ); ?></td>
				<td><?php esc_html_e( $lunch_out ); ?></td>
				<td><?php esc_html_e( $break_hour = '00:00:00' ); ?></td>
				<td><?php if( $office_in != "00:00:00") { esc_html_e( $work_hour ); } else { esc_html_e( 'None'); } ?></td>
				<td><?php if( $office_in != "") { esc_html_e( $user_ip ); } else { esc_html_e( "None"); } ?></td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
			<?php
					//add no record found
					$date = date("d M y", strtotime($date));
					$txt = "$no, $date, $fullname, $office_in, $office_out, $lunch_in, $lunch_out, $break_hour, $work_hour, $user_ip, $region,$city,$country \n";
					fwrite($report_file, $txt);
					$no++;
				}				
			}

			}//end of foreach			
		
		} ?>
	</tbody>
	<?php } ?>		
	<tfoot>
		<tr class="info main_tb_head">
			<th>#</th>
			<th><?php esc_html_e('Name', CIP_FREE_TXTDM );?></th>
			<th><?php esc_html_e('Date', CIP_FREE_TXTDM );?></th>
			<th><?php esc_html_e('Office In', CIP_FREE_TXTDM );?></th>
			<th><?php esc_html_e('Office Out', CIP_FREE_TXTDM );?></th>
			<th><?php esc_html_e('Lunch In', CIP_FREE_TXTDM );?></th>
			<th><?php esc_html_e('Lunch Out', CIP_FREE_TXTDM );?></th>
			<th><?php esc_html_e('Break Time', CIP_FREE_TXTDM );?></th>
			<th><?php esc_html_e('Work Hour', CIP_FREE_TXTDM );?></th>
			<th><?php esc_html_e('IP', CIP_FREE_TXTDM );?></th>
			<th><?php esc_html_e('Location', CIP_FREE_TXTDM );?></th>
			<th><?php esc_html_e('Action', CIP_FREE_TXTDM );?></th>
		</tr>
	<tfoot>
</table>
<?php
if ( isset( $flag ) && ! empty ( $flag ) ) { 
	$work_hourup = array_merge($work_array1,$work_array2,$work_array3);
	echo "<p class='total_working_hour'>Total Working Time:- ".AddPlayTime( $work_hourup )."</p>";
}
?>
<?php 
wp_register_script( 'clock-in-reports-script', false );
wp_enqueue_script( 'clock-in-reports-script' );
$js = " ";
ob_start(); ?>
function ViewReport(id, staff_id, date){
	console.log(id + staff_id);
	jQuery("#view-report-result").remove();
	jQuery.ajax({
		type: "post",
		url: location.href,
		data: "&id=" + id + "&staff_id=" + staff_id + "&date=" + date + "&action=view",
		contentType: "application/x-www-form-urlencoded",
		success: function(responseData, textStatus, jqXHR) {
			var result = jQuery(responseData).find('div#view-report-result');
			jQuery(".view-modal-body").html(result);
		},
		error: function(jqXHR, textStatus, errorThrown) {
		}
	});
}
function EditRecord(id, staff_id, date){
	console.log(id + staff_id);
	jQuery("#edit-report-result").remove();
	jQuery.ajax({
		type: "post",
		url: location.href,
		data: "&id=" + id + "&staff_id=" + staff_id + "&date=" + date + "&action=edit",
		contentType: "application/x-www-form-urlencoded",
		success: function(responseData, textStatus, jqXHR) {
			var result = jQuery(responseData).find('div#edit-report-result');
			jQuery(".edit-modal-body").html(result);
		},
		error: function(jqXHR, textStatus, errorThrown) {
		}
	});
}

function UpdateRecoed(id, staff_id, date){
	jQuery("#update-report-result").hide();
	jQuery.ajax({
		type: "post",
		url: location.href,
		data:  jQuery("#update-record-form").serialize() + "&action=update",
		contentType: "application/x-www-form-urlencoded",
		success: function(responseData, textStatus, jqXHR) {
			var result = jQuery(responseData).find('div#update-report-result');
			jQuery("#update-record-form").after(result);
		},
		error: function(jqXHR, textStatus, errorThrown) {
		}
	});
}
// modal js 
jQuery('#ViewModal').on('shown.bs.modal', function () {
  jQuery('#myInput').focus()
});
jQuery('#EditModal').on('shown.bs.modal', function () {
  jQuery('#myInput').focus()
});

<?php
$js .= ob_get_clean();
wp_add_inline_script( 'clock-in-reports-script', $js ); ?>

<!-- View Modal -->
<div class="modal fade" id="ViewModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog modal-lg modal-notify modal-info" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title text-white" id="myModalLabel"><?php esc_html_e('View Record', CIP_FREE_TXTDM );?></h4>
				<button type="button" class="close text-white" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			</div>
			<div class="modal-body view-modal-body">
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal"><?php esc_html_e('Close', CIP_FREE_TXTDM );?></button>
			</div>
		</div>
	</div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="EditModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog modal-lg modal-notify modal-info" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title text-white" id="myModalLabel"><?php esc_html_e('Edit Record', CIP_FREE_TXTDM );?></h4>
				<button type="button" class="close text-white" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			</div>
			<div class="modal-body edit-modal-body">
			</div>
			<div class="modal-footer">
				<button type="button" id="update-record" name="update-record" class="btn btn-default" onclick="return UpdateRecoed();"><?php esc_html_e('Update', CIP_FREE_TXTDM );?></button>
				<button type="button" class="btn btn-default" data-dismiss="modal"><?php esc_html_e('Close', CIP_FREE_TXTDM );?></button>
			</div>
		</div>
	</div>
</div>
</div>
<?php
// view fetch report
if(isset($_POST['id']) && isset($_POST['staff_id']) && isset($_POST['date']) && isset($_POST['action'])) {
	$id = sanitize_text_field($_POST['id']);
	$staff_id = sanitize_text_field($_POST['staff_id']);
	$date = sanitize_text_field($_POST['date']);
	$action = sanitize_text_field($_POST['action']);
	
	$user_info = get_userdata( $staff_id );
	$email = $user_info->user_email;
	$fname = $user_info->first_name;
	$lname = $user_info->last_name;
	$fullname = ucwords($fname." ".$lname);
	
	if($action == "view") {	
		$view_query = $wpdb->prepare("SELECT * FROM `$staff_attendance_table` WHERE `id` = %d AND `staff_id` = %d AND `date` = %s", $id, $staff_id, $date);
		if($report_data = $wpdb->get_row($view_query)) {
			$id = $report_data->id;
			$date = $report_data->date;
			$office_in = $report_data->office_in;
			$office_out = $report_data->office_out;
			$lunch_in = $report_data->lunch_in;
			$lunch_out = $report_data->lunch_out;
			$user_ip = $report_data->ip;
			$extra = @unserialize($report_data->extra);
			$sever_name = $_SERVER['SERVER_NAME'];
			$sever_ip_address = $_SERVER['SERVER_ADDR'];
			$sever_remote_ip_address = $_SERVER['REMOTE_ADDR'];
			$sever_bwoser_system_details = $_SERVER['HTTP_USER_AGENT'];
			$server_software = $_SERVER['SERVER_SOFTWARE'];
			$server_signature = $_SERVER['SERVER_SIGNATURE'];
			$report = $report_data->report;

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
			<div id="view-report-result">
				<table class="table table-bordered">
					<tr>
						<th><?php esc_html_e('Name', CIP_FREE_TXTDM );?></th>
						<td><?php esc_html_e( ucwords($fname." ".$lname)); ?></td>
					<tr>
					<tr>
						<th><?php esc_html_e('Date', CIP_FREE_TXTDM );?></th>
						<td><?php esc_html_e( $date ); ?></td>
					<tr>
					<tr>
						<th><?php esc_html_e('Office In', CIP_FREE_TXTDM );?></th>
						<td><?php esc_html_e( $office_in ); ?></td>
					<tr>
					<tr>
						<th><?php esc_html_e('Office Out', CIP_FREE_TXTDM );?></th>
						<td><?php esc_html_e( $office_out ); ?></td>
					<tr>
					<tr>
						<th><?php esc_html_e('Lunch In', CIP_FREE_TXTDM );?></th>
						<td><?php esc_html_e( $lunch_in ); ?></td>
					<tr>
					<tr>
						<th><?php esc_html_e('Lunch Out', CIP_FREE_TXTDM );?></th>
						<td><?php esc_html_e( $lunch_out ); ?></td>
					<tr>
					<tr>
						<th><?php esc_html_e('Break Time', CIP_FREE_TXTDM );?></th>
						<td><?php esc_html_e( $break_hour ); ?></td>
					<tr>
					<tr>
						<th><?php esc_html_e('Working Hours', CIP_FREE_TXTDM );?></th>
						<td><?php if($office_in != "00:00:00") { esc_html_e( $work_hour );} else { esc_html_e( "None"); } ?></td>
					<tr>
					<tr>
						<th><?php esc_html_e('IP Address', CIP_FREE_TXTDM );?></th>
						<td><?php if($office_in != "") { esc_html_e( $user_ip ); } else { esc_html_e( "None"); } ?></td>
					<tr>
					<tr>
						<th><?php esc_html_e('Location', CIP_FREE_TXTDM );?></th>
						<td><?php if($office_in != "None") { esc_html_e( $report_data->user_location ); } else { esc_html_e( "None"); } ?></td>
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
		} // end if view query
	}// end if view action check
	
	if($action == "edit") {
		$edit_query = $wpdb->prepare("SELECT * FROM `$staff_attendance_table` WHERE `id` = %d AND `staff_id` = %d AND `date` = %s", $id, $staff_id, $date);
		if($report_data = $wpdb->get_row($edit_query)) {
			$id = $report_data->id;
			$date = $report_data->date;
			$office_in = $report_data->office_in;
			$office_out = $report_data->office_out;
			$lunch_in = $report_data->lunch_in;
			$lunch_out = $report_data->lunch_out;
			$report = $report_data->report;
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
			?>
			<div id="edit-report-result">
				<form id="update-record-form" name="update-record-form">
					<table class="table table-bordered">
						<tr>
							<th><?php esc_html_e('Name', CIP_FREE_TXTDM ); ?></th>
							<td>
								<?php esc_html_e( ucwords($fname." ".$lname)); ?>
								<input type="hidden" id="id" name="id" value="<?php echo esc_attr($id); ?>">
								<input type="hidden" id="staff_id" name="staff_id" value="<?php echo esc_attr($staff_id); ?>">
								<input type="hidden" id="date" name="date" value="<?php echo esc_attr($date); ?>">
							</td>
						<tr>
						<tr>
							<th><?php esc_html_e('Date', CIP_FREE_TXTDM );?></th>
							<td><?php echo date("l, ".$date_format, strtotime($date)); ?></td>
						<tr>
						<tr>
							<th><?php esc_html_e('Office In', CIP_FREE_TXTDM );?></th>
							<td><input type="text" class="datetimepicker2" id="office_in" name="office_in" value="<?php echo esc_attr($office_in); ?>"></td>
						<tr>
						<tr>
							<th><?php esc_html_e('Office Out', CIP_FREE_TXTDM );?></th>
							<td><input type="text" class="datetimepicker3" id="office_out" name="office_out" value="<?php echo esc_attr($office_out); ?>"></td>
						<tr>
						<tr>
							<th><?php esc_html_e('Lunch In', CIP_FREE_TXTDM );?></th>
							<td><input type="text" class="datetimepicker4" id="lunch_in" name="lunch_in" value="<?php echo esc_attr($lunch_in); ?>"></td>
						<tr>
						<tr>
							<th><?php esc_html_e('Lunch Out', CIP_FREE_TXTDM );?></th>
							<td><input type="text" class="datetimepicker5" id="lunch_out" name="lunch_out" value="<?php echo esc_attr($lunch_out); ?>"></td>
						<tr>
						<tr>
							<th><?php esc_html_e('Working Hours', CIP_FREE_TXTDM );?></th>
							<td><input type="text" class="datetimepicker5" id="today_total_hours" name="today_total_hours" value="<?php if($office_in != "00:00:00") { echo esc_attr($work_hour);} else { echo esc_attr( "None"); } ?>"></td>
						<tr>
						<tr>
							<th><?php esc_html_e('IP Address', CIP_FREE_TXTDM );?></th>
							<td><input type="text"  id="user_ip" name="user_ip" value="<?php echo esc_attr($report_data->ip); ?>"></td>
						<tr>
						<tr>
							<th><?php esc_html_e('Location', CIP_FREE_TXTDM );?></th>
							<td><input type="text"  id="location" name="location" value="<?php if($office_in != "None") {  echo esc_attr($report_data->user_location); } else { echo esc_attr( "None"); } ?>"></td>
						<tr>
						<tr>
							<th colspan="2" class="text-center"><?php esc_html_e('Work Report', CIP_FREE_TXTDM );?></th>
						<tr>
						<tr>
							<td colspan="2"><?php echo "<pre>".$report."</pre>"; ?></td>
						<tr>
					</table>
				</form>
				<?php 
				wp_register_script( 'clock-in-reports-script1', false );
				wp_enqueue_script( 'clock-in-reports-script1' );
				$js = " ";
				ob_start(); ?>

				jQuery( document ).ready(function() {
					jQuery('.datetimepicker1').datetimepicker({
						format: 'YYYY:MM:DD',
					});
					jQuery('.datetimepicker2').datetimepicker({
						format: 'hh:mm A',
					});
					jQuery('.datetimepicker3').datetimepicker({
						format: 'hh:mm A',
					});
					jQuery('.datetimepicker4').datetimepicker({
						format: 'hh:mm A',
					});
					jQuery('.datetimepicker5').datetimepicker({
						format: 'hh:mm A',	
					});
				});
				
				<?php
				$js .= ob_get_clean();
				wp_add_inline_script( 'clock-in-reports-script1', $js ); ?>
			</div>
			<?php
		} // end if edit query
	}// end if edit action check
	
	if($action == "update") {
		$office_in = date("H:i:s", strtotime($_POST['office_in']));
		$office_out = date("H:i:s", strtotime($_POST['office_out']));
		$lunch_in = date("H:i:s", strtotime($_POST['lunch_in']));
		$lunch_out = date("H:i:s", strtotime($_POST['lunch_out']));		
		$user_ip = $_POST['user_ip'];	
		$update_query = $wpdb->prepare("UPDATE `$staff_attendance_table` SET `office_in` = %s, `office_out` = %s, `lunch_in` = %s, `lunch_out` = %s, `ip` = %s, `timestamp` = NOW() WHERE `id` = %d AND `staff_id` = %d AND `date` = %s", $office_in, $office_out, $lunch_in, $lunch_out, $user_ip, $id, $staff_id, $date);
		if($report_data = $wpdb->get_row($update_query)) {}
			?><div id='update-report-result' class='alert alert-success'>Success: report has been updated.</div><?php
	}
}// if fetch report
?>
