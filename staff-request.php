<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

wp_enqueue_script('jquery');
wp_enqueue_script('jquery-ui-core');
wp_enqueue_script('jquery-ui-datepicker');
wp_enqueue_style('jquery-ui-css', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');

global $wpdb;
$staff_attendance_table = $wpdb->prefix . "sm_attendance";
$staff_table            = $wpdb->prefix . "sm_staffs";
$staff_category_table   = $wpdb->prefix . "sm_staff_category";
$date_format            = get_option('date_format');
$time_format            = get_option('time_format');
$current_date           = date("Y-m-d");
$filter_name            = "1";
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
?>
<nav class="navbar navbar-dark bg-dark main-dashboard-cip other-pages">
	<a class="navbar-brand" href="<?php echo esc_url( admin_url( 'admin.php?page=clock-in-portal' ) ); ?>"><i class="fas fa-home"></i></a>
  	<a class="navbar-brand" href="#"><?php esc_html_e('Leave Request Management', CIP_FREE_TXTDM ); ?></a>
  	<div class="form-inline my-2 my-lg-0">
      	<a class="navbar-brand" href="<?php echo esc_url( admin_url( 'admin.php?page=cip-settings' ) ); ?>"><i class="fas fa-cog"></i></a>
    </div>
</nav>
<!-- filter table-->
<br>
<form id="get-report" name="get-report" method="POST">
<table class="table ">
	<tr>
		<td class="sm-labels">
			<?php esc_html_e('Filter', CIP_FREE_TXTDM );?>
			<!-- filter -->
			<select id="filter_name" name="filter_name">
				<optgroup label="Select Any Filter">
				<option value="1" <?php if($filter_name == "1") echo esc_attr( "selected=selected"); ?>><?php esc_html_e('This Month', CIP_FREE_TXTDM );?></option>
				<option value="2" <?php if($filter_name == "2") echo esc_attr( "selected=selected"); ?>><?php esc_html_e('Previous Month', CIP_FREE_TXTDM );?></option>
				<option value="3" <?php if($filter_name == "3") echo esc_attr( "selected=selected"); ?>><?php esc_html_e('Next Three Month', CIP_FREE_TXTDM );?></option>
				<option value="6" <?php if($filter_name == "6") echo esc_attr( "selected=selected"); ?>><?php esc_html_e('Next Six Month', CIP_FREE_TXTDM );?></option>
				<option value="9" <?php if($filter_name == "9") echo esc_attr( "selected=selected"); ?>><?php esc_html_e('Next Nine Month', CIP_FREE_TXTDM );?></option>
				<option value="12" <?php if($filter_name == "12") echo esc_attr( "selected=selected"); ?>><?php esc_html_e('Next One Year', CIP_FREE_TXTDM );?></option>
				</optgroup>
			</select>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<select id="attend_filter_name" name="attend_filter_name">
				<optgroup label="Select Any Filter">
				<option value="0" <?php if($attend_filter_name == "0") echo esc_attr( "selected=selected"); ?>><?php esc_html_e('All', CIP_FREE_TXTDM );?></option>
				<option value="1" <?php if($attend_filter_name == "1") echo esc_attr( "selected=selected"); ?>><?php esc_html_e('Pending', CIP_FREE_TXTDM );?></option>
				<option value="2" <?php if($attend_filter_name == "2") echo esc_attr( "selected=selected"); ?>><?php esc_html_e('Approved', CIP_FREE_TXTDM );?></option>
				<option value="3" <?php if($attend_filter_name == "3") echo esc_attr( "selected=selected"); ?>><?php esc_html_e('Cancelled', CIP_FREE_TXTDM );?></option>
				</optgroup>
			</select>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<button type="submit" class="btn btn-success"><?php esc_html_e('Filtered', CIP_FREE_TXTDM );?></button>
		</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
	</tr>
</table>
</form>

	<?php
	if(isset($_POST['filter_name'])) {
		$filter_name = sanitize_text_field($_POST['filter_name']);
		$attend_filter_name = sanitize_text_field($_POST['attend_filter_name']);
		if($filter_name == 1) $filter_selected ="This Month";
		if($filter_name == 2) $filter_selected ="Past Month";
		if($filter_name == 3) $filter_selected ="Past Three Month";
		if($filter_name == 6) $filter_selected ="Past Six Month";
		if($filter_name == 9) $filter_selected ="Past Nine Month";
		if($filter_name == 12) $filter_selected ="Past One Year";
		if($attend_filter_name == 1) $attend_filter_selected ="Pending";
		if($attend_filter_name == 2) $attend_filter_selected ="Approved";
		if($attend_filter_name == 3) $attend_filter_selected ="Cancelled";
	}

	$date_format = get_option('date_format');
	$time_format = get_option('time_format');
	// get all records
	global $wpdb;
	$staff_request = get_option("cip_staff_request");

?>
<div>
	<!-- Nav tabs -->
	<ul class="nav nav-tabs" role="tablist">
		<li role="presentation" class="active sm-labels"><a href="#official-staff_request" aria-controls="official-staff_request" role="tab" data-toggle="tab"><?php esc_html_e('Staff Request Zone', CIP_FREE_TXTDM );?></a></li>
	</ul>

	<!-- Tabs -->
	<div class="tab-content">
		<!--official-staff_request--->
		<div role="tabpanel" class="tab-pane active" id="official-staff_request">
			<table class="table table-hover table-striped">
				<thead>
					<tr class="info main_tb_head">
						<th>#</th>
						<th><?php esc_html_e('Name', CIP_FREE_TXTDM );?></th>
						<th><?php esc_html_e('Title', CIP_FREE_TXTDM );?></th>
						<th><?php esc_html_e('Short Description about Leave', CIP_FREE_TXTDM );?></th>
						<th><?php esc_html_e('Date', CIP_FREE_TXTDM );?></th>
						<th><?php esc_html_e('Request For', CIP_FREE_TXTDM );?></th>
						<th><?php esc_html_e('Status', CIP_FREE_TXTDM );?></th>
						<th><?php esc_html_e('Action', CIP_FREE_TXTDM );?></th>
					</tr>
				</thead>
	<?php if(isset($_POST['filter_name']) && isset($_POST['attend_filter_name'])) { ?>
	<tbody>
	<?php

		$filter_name 		= sanitize_text_field($_POST['filter_name']);
		$attend_filter_name = sanitize_text_field($_POST['attend_filter_name']);

			if ( $filter_name == '1' )
			{

				$first = date( "Y-m-01" );
				$last  = date( "Y-m-t", strtotime( $first ) );
				$all_days_report = range_date_free( $first, $last );

			} elseif ( $filter_name == '2' ) {

				$first = date( "Y-m-01", strtotime( "-1 month" ) );
				$last  = date( "Y-m-t", strtotime( $first ) );
				$all_days_report = range_date_free( $first, $last );

			} elseif ( $filter_name == '3' ) {

				$first = date( "Y-m-01", strtotime( "+1 month" ) );
				$last  = date( "Y-m-t", strtotime( $first ) );
				$last  = date( "Y-m-d", strtotime( "+2 month", strtotime( $last ) ) );
				$all_days_report = range_date_free( $first, $last );

			} elseif( $filter_name == "6" ) {

				$first = date( "Y-m-01", strtotime( "+1 month" ) );
				$last  = date( "Y-m-t", strtotime( $first ) );
				$last  = date( "Y-m-d", strtotime( "+5 month", strtotime( $last ) ) );
				$all_days_report = range_date_free( $first, $last );

			} elseif( $filter_name == "9" ) {

				$first = date( "Y-m-01", strtotime( "+1 month" ) );
				$last  = date( "Y-m-t", strtotime( $first ) );
				$last  = date( "Y-m-d", strtotime( "+8 month", strtotime( $last ) ) );
				$all_days_report = range_date_free( $first, $last );

			} elseif($filter_name == "12") {

				$first = date( "Y-m-01", strtotime( "+1 month" ) );
				$last  = date( "Y-m-t", strtotime( $first ) );
				$last  = date( "Y-m-d", strtotime( "+11 month", strtotime( $last ) ) );
				$all_days_report = range_date_free( $first, $last );

			}
			$n = 1;
			$flag = 0;

			foreach( $all_days_report as $row_date ) {
				if( $staff_request = get_option( "cip_staff_request" ) ) {

						if( ! empty ( $staff_request ) ) {
							//$n = 1;
							foreach($staff_request as $key => $staff_request) {
								$status = $staff_request['status'];
								if($status == 1) $status = "Pending";
								if($status == 2) $status = "Approved";
								if($status == 3) $status = "Cancelled";

								$start_date = $staff_request['start_date'];
								$end_date = $staff_request['end_date'];

								if(strtotime($start_date))
									$start_date = date($date_format , strtotime($staff_request['start_date']));
								if(strtotime($end_date))
									$end_date = date($date_format , strtotime($staff_request['end_date']));

			if( $row_date == $staff_request['start_date'] ) {

			if( $attend_filter_name == '0' ) { ?>
				<tr>
						<td><?php esc_html_e( $n.".");?></td>
						<td><?php esc_html_e( $staff_request['name']); ?></td>
						<td><?php esc_html_e( $staff_request['event']); ?></td>
						<td><?php esc_html_e( $staff_request['event_disc']); ?></td>
						<?php if ($end_date == $start_date){ ?>
						<td><?php if($end_date != "") { esc_html_e( $end_date); } ?></td>
						<?php }else{ ?>
						<td><?php esc_html_e( $start_date); if($end_date != "") { ?> - <?php esc_html_e( $end_date); } ?></td>
						<?php } ?>
						<td><?php esc_html_e('For '.$staff_request['leaves'].' Day(s)', CIP_FREE_TXTDM );?></td>
						<td><?php esc_html_e( $status); ?></td>
						<td>
							<a href="#" class="btn btn-info" title="View" data-toggle="modal" data-target="#view-staff_request-modal" onclick="return DoAction('view', '<?php esc_html_e( $key); ?>');"><i class="fas fa-eye" aria-hidden="true"></i></a>
							<a href="#" class="btn btn-info" title="Update" data-toggle="modal" data-target="#update-staff_request-modal" onclick="return DoAction('update', '<?php esc_html_e( $key); ?>');"><i class="fas fa-edit" aria-hidden="true"></i></a>
							<a href="#" class="btn btn-info" title="Delete" data-toggle="modal" data-target="#delete-staff_request-modal" onclick="return DoAction('delete', '<?php esc_html_e( $key); ?>');"><i class="fas fa-times" aria-hidden="true"></i></a>
						</td>
					</tr>
			<?php $n++; } elseif ( $attend_filter_name == $staff_request['status'] ) {	?>
					<tr>
						<td><?php esc_html_e( $n.".");?></td>
						<td><?php esc_html_e( $staff_request['name']); ?></td>
						<td><?php esc_html_e( $staff_request['event']); ?></td>
						<td><?php esc_html_e( $staff_request['event_disc']); ?></td>
						<?php if ($end_date == $start_date){ ?>
						<td><?php if($end_date != "") { esc_html_e( $end_date); } ?></td>
						<?php }else{ ?>
						<td><?php esc_html_e( $start_date); if($end_date != "") { ?> - <?php esc_html_e( $end_date); } ?></td>
						<?php } ?>
						<td><?php esc_html_e('For '.$staff_request['leaves'].' Day(s)', CIP_FREE_TXTDM );?></td>
						<td><?php esc_html_e( $status); ?></td>
						<td>
							<a href="#" class="btn btn-info" title="View" data-toggle="modal" data-target="#view-staff_request-modal" onclick="return DoAction('view', '<?php echo esc_attr( $key); ?>');"><i class="fas fa-eye" aria-hidden="true"></i></a>
							<a href="#" class="btn btn-info" title="Update" data-toggle="modal" data-target="#update-staff_request-modal" onclick="return DoAction('update', '<?php echo esc_attr( $key); ?>');"><i class="fas fa-edit" aria-hidden="true"></i></a>
							<a href="#" class="btn btn-info" title="Delete" data-toggle="modal" data-target="#delete-staff_request-modal" onclick="return DoAction('delete', '<?php echo esc_attr( $key); ?>');"><i class="fas fa-times" aria-hidden="true"></i></a>
						</td>
					</tr>
					<?php  $n++;} }

							} ?>
				</tbody>
				<?php }else{ ?>
				<tbody>
					<tr>
						<td><?php esc_html_e('No data Found', CIP_FREE_TXTDM );?></td>
					</tr>
				</tbody>
				<?php }  } } } ?>
				<thead>
					<tr class="info main_tb_head">
						<th>#</th>
						<th><?php esc_html_e('Name', CIP_FREE_TXTDM );?></th>
						<th><?php esc_html_e('Title', CIP_FREE_TXTDM );?></th>
						<th><?php esc_html_e('Short Description about Leave', CIP_FREE_TXTDM );?></th>
						<th><?php esc_html_e('Date', CIP_FREE_TXTDM );?></th>
						<th><?php esc_html_e('Request For', CIP_FREE_TXTDM );?></th>
						<th><?php esc_html_e('Status', CIP_FREE_TXTDM );?></th>
						<th><?php esc_html_e('Action', CIP_FREE_TXTDM );?></th>
					</tr>
				</thead>
			</table>
		</div>
		<!--official-staff_request end--->

		<!--upcoming-staff_request--->
		<div role="tabpanel" class="tab-pane" id="upcoming-staff_request">

		</div>
		<!--upcoming-staff_request end--->

	</div>
</div>

<!-- View staff_request Modal-->
<div class="modal fade" id="view-staff_request-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<form id="view-staff_request-form" name="view-staff_request-form">
				<div class="modal-header">
					<h4 class="modal-title" id="myModalLabel"><?php esc_html_e('Staff Request Details', CIP_FREE_TXTDM );?></h4>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>

				</div>
				<div class="modal-body" id="view-modal-body">
					<div id="view-loading-icon" style="display:none;">
					<?php esc_html_e('Processing...', CIP_FREE_TXTDM );?><i class="fas fa-spinner fa-3x" aria-hidden="true"></i>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-danger" data-dismiss="modal"><?php esc_html_e('Close', CIP_FREE_TXTDM );?></button>
				</div>
			</form>
		</div>
	</div>
</div>

<!-- Update staff_request Modal-->
<div class="modal fade" id="update-staff_request-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<form id="update-staff_request-form" name="update-staff_request-form">
				<div class="modal-header">
					<h4 class="modal-title" id="myModalLabel"><?php esc_html_e('Update Staff Request Details', CIP_FREE_TXTDM ); ?></h4>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>

				</div>
				<div class="modal-body" id="update-modal-body">
					<div id="update-loading-icon" style="display:none;">
					<?php esc_html_e('Processing...', CIP_FREE_TXTDM );?><i class="fas fa-spinner fa-3x" aria-hidden="true"></i>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" id="update-staff_request-button" name="update-staff_request-button" class="btn btn-success" onclick="return DoAction('update-now', '');"><?php esc_html_e('Update Request', CIP_FREE_TXTDM );?></button>
					<button type="button" id="update-staff_request-close" name="update-staff_request-close" class="btn btn-danger" data-dismiss="modal"><?php esc_html_e('Close', CIP_FREE_TXTDM );?></button>
					<div id="update-now-loading-icon" style="display:none;">
						<?php esc_html_e('Processing...', CIP_FREE_TXTDM );?><i class="fas fa-spinner fa-3x" aria-hidden="true"></i>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>

<?php 
wp_register_script( 'clock-in-staff-request-script', false );
wp_enqueue_script( 'clock-in-staff-request-script' );
$js = " ";
ob_start(); ?>
//tabs
jQuery('#myTabs a').click(function (e) {
  e.preventDefault()
  jQuery(this).tab('show');
});

// add staff_request modal call
jQuery('#add-staff_request-modal').on('shown.bs.modal', function (){
	
	jQuery("#add-staff_request-button").prop('enable', true);
	jQuery("#name").val("");
	jQuery("#start_date").val("");
	jQuery("#end_date").val("");
	jQuery("#leaves").val("");
});

// action handler - action: view/add/update/delete
function DoAction(action, id){
	var data_values = "";
	//view
	if(action == "view") {
		jQuery("div#view-staff_request-result").remove();
		jQuery("#view-loading-icon").show();
		var data_values = "action=" + action + "&id=" + id;
		//post data
		jQuery.ajax({
			type: "post",
			url: location.href,
			data: data_values,
			contentType: "application/x-www-form-urlencoded",
			success: function(responseData, textStatus, jqXHR) {
				var result = jQuery(responseData).find('div#view-staff_request-result');
				jQuery("#view-loading-icon").hide();
				jQuery("#view-modal-body").after(result);
			},
			error: function(jqXHR, textStatus, errorThrown) {
			}
		});
	}

	//update
	if(action == "update") {
		jQuery("div#update-result").remove();
		jQuery("#update-loading-icon").show();
		var data_values = "action=" + action + "&id=" + id;
		//post data
		jQuery.ajax({
			type: "post",
			url: location.href,
			data: data_values,
			contentType: "application/x-www-form-urlencoded",
			success: function(responseData, textStatus, jqXHR) {
				var result = jQuery(responseData).find('div#update-result');
				jQuery("#update-loading-icon").hide();
				jQuery("#update-modal-body").after(result);
			},
			error: function(jqXHR, textStatus, errorThrown) {
			}
		});
	}

	//update-now
	if(action == "update-now") {
		jQuery("#update-staff_request-button").hide();
		jQuery("#update-staff_request-close").hide();
		jQuery("#update-now-loading-icon").show();
		var data_values = jQuery("#update-staff_request-form").serialize() + "&action=" + action;
		//post data
		jQuery.ajax({
			type: "post",
			url: location.href,
			data: data_values,
			contentType: "application/x-www-form-urlencoded",
			success: function(responseData, textStatus, jqXHR) {
				var result = jQuery(responseData).find('div#update-staff_request-result');
				jQuery("#update-now-loading-icon").hide();
				location.reload();
			},
			error: function(jqXHR, textStatus, errorThrown) {
			}
		});
	}

	//delete
	if(action == "delete") {
		if (confirm("Are you sure want to delete this request?") == true) {
			var data_values = "&action=" + action + "&id=" + id;
			//post data
			jQuery.ajax({
				type: "post",
				url: location.href,
				data: data_values,
				contentType: "application/x-www-form-urlencoded",
				success: function(responseData, textStatus, jqXHR) {
					var result = jQuery(responseData).find('div#delete-staff_request-result');
					location.reload();
				},
				error: function(jqXHR, textStatus, errorThrown) {
				}
			});
		}
	}
}

<?php
$js .= ob_get_clean();
wp_add_inline_script( 'clock-in-staff-request-script', $js ); ?>
<?php
//Action Executor
if(isset($_POST['action'])){
	//print_r($_POST);
	$action = sanitize_text_field($_POST['action']);

	// view
	if($action == "view") {
		$key = sanitize_text_field($_POST['id']);
		$saved_staff_requests = get_option("cip_staff_request");
		$staff_request = $saved_staff_requests[$key];
		$name = $staff_request['name'];
		$event = $staff_request['event'];
		$event_disc = $staff_request['event_disc'];
		$start_date = $staff_request['start_date'];
		$end_date = $staff_request['end_date'];
		$leaves = $staff_request['leaves'];
		$status = $staff_request['status'];
		$reason_message = $staff_request['reason_message'];
		?>
		<div id="view-staff_request-result">
		<table class="table table-striped">
			<tr>
				<td><label for="Role"><?php esc_html_e('Name', CIP_FREE_TXTDM ); ?></label></td>
				<td><?php esc_html_e( ucwords($name)); ?></td>
			</tr>
			<tr>
				<td><label for="Role"><?php esc_html_e('Title', CIP_FREE_TXTDM ); ?></label></td>
				<td><?php esc_html_e( ucwords($event)); ?></td>
			</tr>
			<tr>
				<td><label for="Role"><?php esc_html_e('Short Description about Leave', CIP_FREE_TXTDM ); ?></label></td>
				<td><?php esc_html_e( ucwords($event_disc)); ?></td>
			</tr>
			<tr>
				<td><label for="Role"><?php esc_html_e('Start Date', CIP_FREE_TXTDM );?></label></td>
				<td><?php esc_html_e( ($start_date)); ?></td>
			</tr>
			<tr>
				<td><label for="Role"><?php esc_html_e('End Date', CIP_FREE_TXTDM );?></label></td>
				<td><?php esc_html_e( ($end_date)); ?></td>
			</tr>
			<tr>
				<td><label for="Role"><?php esc_html_e('Leaves (In Days)', CIP_FREE_TXTDM );?></label></td>
				<td><?php esc_html_e( ($leaves)); ?></td>
			</tr>
			<tr>
				<td><label for="Role"><?php esc_html_e('Status', CIP_FREE_TXTDM );?></label></td>
				<td><?php if($status == 1) esc_html_e( "Pending"); if($status == 2) esc_html_e( "Approved"); if($status == 3) esc_html_e( "Cancelled"); ?></td>
			</tr>
		
			<tr>
				<td><label for="Role"><?php esc_html_e('Reason Message', CIP_FREE_TXTDM ); ?></label></td>
				<td><?php esc_html_e( ucwords($reason_message)); ?></td>
			</tr>
		</table>
		</div>
		<?php
	} // end view


	// update
	if($action == "update") {
		$key = sanitize_text_field($_POST['id']);
		$saved_staff_requests = get_option("cip_staff_request");
		$staff_request = $saved_staff_requests[$key];
		$name = $staff_request['name'];
		$event = $staff_request['event'];
		$event_disc = $staff_request['event_disc'];
		$start_date = $staff_request['start_date'];
		$end_date = $staff_request['end_date'];
		$leaves = $staff_request['leaves'];
		$status = $staff_request['status'];
		$reason_message = $staff_request['reason_message'];
		?>
		<div id="update-result">
			<table class="table table-striped">
			<tr>
				<td><label for="Role">Name</label></td>
				<td>
					<input type="hidden" class="form-control" id="id" name="id" placeholder="ID" value="<?php echo esc_attr($key); ?>">
					<input type="input" class="form-control" id="name" name="name" placeholder="Name" value="<?php echo esc_attr($name); ?>" readonly>
					<?php wp_nonce_field( 'update_staff_request_nonce_action', 'update_staff_request_nonce_name' ); ?>
				</td>
			</tr>
			<tr>
				<td><label for="Role"><?php esc_html_e('Title', CIP_FREE_TXTDM ); ?></label></td>
				<td>
					<input type="input" class="form-control" id="event" name="event" placeholder="Short Description" value="<?php echo esc_attr($event); ?>" readonly>
				</td>
			</tr>
			<tr>
				<td><label for="Role"><?php esc_html_e('Short Description about Leave', CIP_FREE_TXTDM ); ?></label></td>
				<td><textarea class="form-control" id="event_disc" name="event_disc" rows="5" placeholder="Type Short Description about Request" readonly><?php echo esc_attr($event_disc); ?></textarea>
				</td>
			</tr>
			<tr>
				<td><label for="Role"><?php esc_html_e('Start Date', CIP_FREE_TXTDM ); ?></label></td>
				<td><input type="input" class="form-control sdate" id="start_date" name="start_date" placeholder="Start Date (Use Format YYYY-MM-DD)" value="<?php echo esc_attr( $start_date); ?>" readonly></td>
			</tr>
			<tr>
				<td><label for="Role"><?php esc_html_e('End Date', CIP_FREE_TXTDM ); ?></label></td>
				<td><input type="input" class="form-control edate" id="end_date" name="end_date" placeholder="End Date (Use Format YYYY-MM-DD)" value="<?php echo esc_attr( $end_date); ?>" readonly></td>
			</tr>
			<tr>
				<td><label for="Role"><?php esc_html_e('Leaves (In Days)', CIP_FREE_TXTDM ); ?></label></td>
				<td><input type="input" class="form-control" id="leaves" name="leaves" placeholder="Leave Days" value="<?php echo esc_attr( $leaves); ?>" readonly></td>
			</tr>
			<tr>
				<td><label for="Role"><?php esc_html_e('Status', CIP_FREE_TXTDM ); ?></label></td>
				<td>
					<select id="status" name="status" class="form-control">
						<optgroup label="Select Any Status">
							<option value="1" <?php if($status == 1) echo esc_attr( "selected=selected"); ?>>Pending</option>
							<option value="2" <?php if($status == 2) echo esc_attr( "selected=selected"); ?>>Approved</option>
							<option value="3" <?php if($status == 3) echo esc_attr( "selected=selected"); ?>>Cancelled</option>
						</optgroup>
					</select>
				</td>
			</tr>
			<tr>
				<td><label for="Role"><?php esc_html_e('Reason Message', CIP_FREE_TXTDM ); ?></label></td>
				<td>
					<input type="input" class="form-control" id="reason_message" name="reason_message" placeholder="Reason Message" value="<?php echo esc_attr($reason_message); ?>">
				</td>
			</tr>
		</table>
		</div>
		<?php
	} // end update

	if($action == "update-now") {
		if ( ! wp_verify_nonce( $_POST['update_staff_request_nonce_name'], 'update_staff_request_nonce_action' ) ) {
			print 'Sorry, your update staff_request nonce did not verify.';
			exit;
		} else {
			// nonce verified - process form data
			$key = sanitize_text_field($_POST['id']);
			$name = sanitize_text_field($_POST['name']);
			$event = sanitize_text_field($_POST['event']);
			$event_disc = sanitize_text_field($_POST['event_disc']);
			$start_date = sanitize_text_field($_POST['start_date']);
			$end_date = sanitize_text_field($_POST['end_date']);
			$leaves = sanitize_text_field($_POST['leaves']);
			$status = sanitize_text_field($_POST['status']);
			$reason_message = sanitize_text_field($_POST['reason_message']);
			$saved_staff_requests = get_option("cip_staff_request");
			$saved_staff_requests[$key] = array (
				'name' => $name,
				'event' => $event,
				'event_disc' => $event_disc,
				'start_date' => $start_date,
				'end_date' => $end_date,
				'leaves' => $leaves,
				'status' => $status,
				'reason_message' => $reason_message
			);
			
			update_option("cip_staff_request", $saved_staff_requests);
		}
	}

	// update
	if($action == "delete") {
		$key = sanitize_text_field($_POST['id']);
		$staff_request = get_option("cip_staff_request");
		unset($staff_request[$key]);
		?><div id="delete-staff_request-result">Request deleted successfully.</div><?php
		$staff_request = update_option("cip_staff_request", $staff_request);
	} // end delete
}
?>
