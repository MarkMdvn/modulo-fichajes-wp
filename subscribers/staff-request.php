<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit; ?>
<h1><?php esc_html_e('Leave Request', CIP_FREE_TXTDM );?></h1>
<?php 
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

$date_format = get_option('date_format');
$time_format = get_option('time_format');

// get all records
global $wpdb;
$staff_request = get_option("cip_staff_request");
?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.min.js" integrity="sha384-VHvPCCyXqtD5DqJeNxl2dtTyhF78xXNXdkwX1CZeRusQfRKp+tA7hAShOK/B/fQ2" crossorigin="anonymous"></script>
<h1>
	<button type="button" class="btn btn-success" data-toggle="modal" data-target="#add-staff_request-modal"><i class="fas fa-plus" aria-hidden="true"></i> <?php esc_html_e('Add New Request', CIP_FREE_TXTDM );?></button>
</h1>

	<!-- Nav tabs -->
	<ul class="nav nav-tabs" role="tablist">
		<li class="nav-item" role="presentation" class="active sm-labels"><a class="nav-link active" href="#official-staff_request" aria-controls="official-staff_request" role="tab" data-toggle="tab"><?php esc_html_e('Staff Request Zone', CIP_FREE_TXTDM );?></a></li>
	</ul>

	<!-- Tabs -->
	<div class="tab-content">
		<!--official-staff_request--->
		<div role="tabpanel" class="tab-pane active" id="official-staff_request">
			
			<table class="table table-hover">
				<thead>
					<tr class="info">
						<th>#</th>
						<th><?php esc_html_e('Title', CIP_FREE_TXTDM );?></th>
						<th><?php esc_html_e('Short Description about Request', CIP_FREE_TXTDM );?></th>
						<th><?php esc_html_e('Date', CIP_FREE_TXTDM );?></th>
						<th><?php esc_html_e('Request For', CIP_FREE_TXTDM );?></th>
						<th><?php esc_html_e('Status', CIP_FREE_TXTDM );?></th>
						<th><?php esc_html_e('Action', CIP_FREE_TXTDM );?></th>						
					</tr>
				</thead>
				<?php if($staff_request = get_option("cip_staff_request")) { ?>
				<tbody>
					<?php
						if ( ! empty ( $staff_request ) ) {
							$n = 1;
							
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
									$current_user = wp_get_current_user();
									$fname = $current_user->user_firstname;
									$lname = $current_user->user_lastname;						
									$user_name = $fname.' '.$lname;									
								
									if($staff_request['name'] == $user_name){
					?>
					<tr>
						<td><?php  esc_html_e( $n.".");?></td>
						<td><?php  esc_html_e( $staff_request['event']);?></td>
						<td><?php  esc_html_e( $staff_request['event_disc']); ?></td>
						<?php if ($end_date == $start_date){ ?>
						<td><?php if($end_date != "") {  esc_html_e( $end_date); } ?></td>
						<?php }else{ ?>
						<td><?php  esc_html_e( $start_date); if($end_date != "") { ?> - <?php  esc_html_e( $end_date); } ?></td>
						<?php } ?>	
						<td><?php esc_html_e('For '.$staff_request['leaves'].' Day(s)', CIP_FREE_TXTDM );?></td>
						<td><?php  esc_html_e( $status); ?></td>
						<td>
							<a href="#" class="btn btn-info" title="View" data-toggle="modal" data-target="#view-staff_request-modal" onclick="return DoAction('view', '<?php echo esc_attr($key); ?>');"><i class="fas fa-eye" aria-hidden="true"></i></a>
							<?php 
					$saved_staff_requests = get_option("cip_staff_request");
					$status = $staff_request['status'];					
					if ($status =='1'){ ?>
							<a href="#" class="btn btn-info" title="Update" data-toggle="modal" data-target="#update-staff_request-modal" onclick="return DoAction('update', '<?php echo esc_attr($key); ?>');"><i class="fas fa-edit" aria-hidden="true"></i></a>
							<a href="#" class="btn btn-info" title="Delete" data-toggle="modal" data-target="#delete-staff_request-modal" onclick="return DoAction('delete', '<?php echo esc_attr($key); ?>');"><i class="fas fa-times" aria-hidden="true"></i></a>
						<?php } ?>
						</td>
					</tr>
					<?php $n++; } } } ?>
				</tbody>
				<?php }else{ ?>
				<tbody>	
					<tr>
						<td><?php esc_html_e('No data Found', CIP_FREE_TXTDM );?></td>
					</tr>
				</tbody>
				<?php } ?>
				<thead>
					<tr class="info">
						<th>#</th>
						<th><?php esc_html_e('Title', CIP_FREE_TXTDM );?></th>
						<th><?php esc_html_e('Short Description about Request', CIP_FREE_TXTDM );?></th>
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
<!-- Add staff_request Modal-->
<div class="modal fade" id="add-staff_request-modal" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<form id="add-staff_request-form" name="add-staff_request-form">
				<div class="modal-header">
					<h4 class="modal-title" id="myModalLabel"><?php esc_html_e('Add New Request', CIP_FREE_TXTDM );?></h4>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					
				</div>
				<div class="modal-body">
					<div class="form-group">
						<label for="staff_request"><?php esc_html_e('Title', CIP_FREE_TXTDM );?></label>
						<input type="input" class="form-control" id="event" name="event" placeholder="Request Title">
						<div class="required" style="display:none; color:red"><?php esc_html_e('Required A text for Title.', CIP_FREE_TXTDM );?></div>
						<?php wp_nonce_field( 'add_staff_request_nonce_action', 'add_staff_request_nonce_name' ); ?>
					</div>
					<div class="form-group">
						<label for="staff_request"><?php esc_html_e('Short Description', CIP_FREE_TXTDM );?></label>
						<textarea class="form-control" id="event_disc" name="event_disc" rows="5" placeholder="Type Short Description about Request"></textarea>						
						<div class="required" style="display:none; color:red"><?php esc_html_e('Required A text for Short Description.', CIP_FREE_TXTDM );?></div>
					</div>
					<div class="form-group">
						<label for="Date"><?php esc_html_e('Start Date', CIP_FREE_TXTDM );?></label>
						<input type="input" class="form-control sdate" id="start_date" name="start_date" placeholder="Start Date (Use Format YYYY-MM-DD)">
						<div class="required" style="display:none; color:red"><?php esc_html_e('Select Start date', CIP_FREE_TXTDM );?></div>
					</div>					      
					<div class="form-group">
						<label for="Date"><?php esc_html_e('End Date', CIP_FREE_TXTDM );?></label>
						<input type="input" class="form-control edate" id="end_date" name="end_date" placeholder="End Date (Use Format YYYY-MM-DD)">
						<div class="required" style="display:none; color:red"><?php esc_html_e('Select End date', CIP_FREE_TXTDM );?></div>
					</div>									
				</div>
				<div class="modal-footer">
					<button type="button" id="add-staff_request-button" name="add-staff_request-button" class="btn btn-success" onclick="return DoAction('add', '');"><?php esc_html_e('Add Request', CIP_FREE_TXTDM );?></button>
					<button type="button" id="add-staff_request-close" name="add-staff_request-close" class="btn btn-danger" data-dismiss="modal"><?php esc_html_e('Close', CIP_FREE_TXTDM );?></button>
					<div id="add-loading-icon" style="display:none;">
						<?php esc_html_e('Processing...', CIP_FREE_TXTDM );?><i class="fas fa-spinner fa-3x" aria-hidden="true"></i>
					</div>
				</div>
			</form>
		</div>
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
					<h4 class="modal-title" id="myModalLabel"><?php esc_html_e('Update Staff Request Details', CIP_FREE_TXTDM );?></h4>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					
				</div>
				<div class="modal-body" id="update-modal-body">
					<div id="update-loading-icon" style="display:none;">
					<?php esc_html_e('Processing...', CIP_FREE_TXTDM );?><i class="fas fa-spinner fa-3x" aria-hidden="true"></i>
					</div>
				</div>
				<div class="modal-footer">
					<?php 
					$saved_staff_requests = get_option("cip_staff_request");
					if($saved_staff_requests){
						$status =  $staff_request['status'];
					}else{
						$status =  0;
					}
					//$status    = (array_key_exists("status",$staff_request)) ? $staff_request['status'] : '0';
					/*$status = $staff_request['status'];*/						
					if ($status =='1'){ ?>
					<button type="button" id="update-staff_request-button" name="update-staff_request-button" class="btn btn-success" onclick="return DoAction('update-now', '');"><?php esc_html_e('Update Request', CIP_FREE_TXTDM );?></button>
					<?php } ?>
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
	
	//add
	if(action == "add") {
		var event = jQuery("#event").val();		
		if(event == "") {
			jQuery("#event").next(".required").css('display','block').fadeOut(4000);
			return false;
		}
		var event_disc = jQuery("#event_disc").val();		
		if(event_disc == "") {
			jQuery("#event_disc").next(".required").css('display','block').fadeOut(4000);
			return false;
		}
		var start_date = jQuery("#start_date").val();		
		if(start_date == "") {
			jQuery("#start_date").next(".required").css('display','block').fadeOut(4000);
			return false;
		}
		var end_date = jQuery("#end_date").val();		
		if(end_date == "") {
			jQuery("#end_date").next(".required").css('display','block').fadeOut(4000);
			return false;
		}
		
		jQuery("#add-staff_request-button").hide();
		jQuery("#add-staff_request-close").hide();
		jQuery("#add-loading-icon").show();
		var data_values = jQuery("#add-staff_request-form").serialize() + "&action=" + action;
		//post data
		jQuery.ajax({
			type: "post",
			url: location.href,
			data: data_values,
			contentType: "application/x-www-form-urlencoded",
			success: function(responseData, textStatus, jqXHR) {
				var result = jQuery(responseData).find('div#add-staff_request-result');
				location.reload();
			},
			error: function(jqXHR, textStatus, errorThrown) {
			}
		});
	}	
	
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

	$action = sanitize_text_field($_POST['action']);
	
	//add
	if($action == "add") {
		if ( ! wp_verify_nonce( $_POST['add_staff_request_nonce_name'], 'add_staff_request_nonce_action' ) ) {
			print 'Sorry, your add staff_request nonce did not verify.';
			exit;
		} else {
		   // nonce verified - process form data	
			$saved_staff_requests = get_option("cip_staff_request");
			$current_user = wp_get_current_user();
			$fname = $current_user->user_firstname;
			$lname = $current_user->user_lastname;
			$name = $fname.' '.$lname;
			$event = sanitize_text_field($_POST['event']);
			$event_disc = sanitize_text_field($_POST['event_disc']);
			$start_date = sanitize_text_field($_POST['start_date']);
			$end_date = sanitize_text_field($_POST['end_date']);
			$date1=date_create($start_date);
			$date2=date_create($end_date);
			$diff=date_diff($date1,$date2);
			$leaves = $diff->format("%a");
			$leaves = $leaves+1;
			$status = '1';
			$new_staff_request = array ( 
				'name' => $name,
				'event' => $event,
				'event_disc' => $event_disc,
				'start_date' => $start_date,
				'end_date' => $end_date,
				'leaves' => $leaves,
				'status' => $status,
			);
			$saved_staff_requests[] = $new_staff_request;
			if(update_option("cip_staff_request" , $saved_staff_requests)) {
				?><div id="add-staff_request-result"><?php esc_html_e('Request added successfully.', CIP_FREE_TXTDM );?></div><?php
			}
		}
	} // end add
	
	// view
	if($action == "view") {
		$key = sanitize_text_field($_POST['id']);
		$saved_staff_requests = get_option("cip_staff_request");
		$staff_request = $saved_staff_requests[$key];
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
				<td><label for="Title"><?php esc_html_e('Title', CIP_FREE_TXTDM );?></label></td>
				<td><?php esc_html_e(ucwords($event)); ?></td>
			</tr>
			<tr>
				<td><label for="Short Description"><?php esc_html_e('Request Short Description', CIP_FREE_TXTDM );?></label></td>
				<td><?php esc_html_e(ucwords($event_disc)); ?></td>
			</tr>
			<tr>
				<td><label for="Start Date"><?php esc_html_e('Start Date', CIP_FREE_TXTDM );?></label></td>
				<td><?php esc_html_e(($start_date)); ?></td>					
			</tr>
			<tr>
				<td><label for="End Date"><?php esc_html_e('End Date', CIP_FREE_TXTDM );?></label></td>
				<td><?php esc_html_e(($end_date)); ?></td>					
			</tr>
			<tr>
				<td><label for="Leaves"><?php esc_html_e('Leaves (In Days)', CIP_FREE_TXTDM );?></label></td>
				<td><?php esc_html_e(($leaves)); ?></td>					
			</tr>
			<tr>
				<td><label for="Status"><?php esc_html_e('Status', CIP_FREE_TXTDM );?></label></td>
				<td><?php if($status == 1) esc_html_e( "Pending"); if($status == 2) esc_html_e( "Approved"); if($status == 3) esc_html_e( "Cancelled"); ?></td>					
			</tr>
			<tr>
				<td><label for="Reason Message"><?php esc_html_e('Reason Message', CIP_FREE_TXTDM );?></label></td>
				<td><?php esc_html_e(($reason_message)); ?></td>					
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
		?>
		<div id="update-result">
			<table class="table table-striped">
			<tr>
				<td><label for="Title"><?php esc_html_e('Title', CIP_FREE_TXTDM );?></label></td>
				<td>
					<input type="hidden" class="form-control" id="id" name="id" placeholder="ID" value="<?php echo esc_attr($key); ?>">
					<input type="hidden" class="form-control" id="name" name="name" placeholder="ID" value="<?php echo esc_attr($name); ?>">
					<input type="input" class="form-control" id="event" name="event" placeholder="Type a title" value="<?php echo esc_attr($event); ?>">
					<?php wp_nonce_field( 'update_staff_request_nonce_action', 'update_staff_request_nonce_name' ); ?>
				</td>
			</tr>
			<tr>
				<td><label for="Role"><?php esc_html_e('Staff Request Description', CIP_FREE_TXTDM );?></label></td>
				<td>
					<textarea class="form-control" id="event_disc" name="event_disc" rows="5" placeholder="Type Short Description about Request"><?php esc_html_e( $event_disc); ?></textarea>
				</td>
			</tr>	
			<tr>
				<td><label for="Role"><?php esc_html_e('Start Date', CIP_FREE_TXTDM );?></label></td>
				<td><input type="input" class="form-control sdate" id="start_dates" name="start_date" placeholder="Start Date (Use Format YYYY-MM-DD)" value="<?php echo esc_attr($start_date); ?>"></td>
				<?php 
				wp_register_script( 'clock-in-subs-staff-request-script1', false );
				wp_enqueue_script( 'clock-in-subs-staff-request-script1' );
				$js = " ";
				ob_start(); ?>
					jQuery('#start_dates').datetimepicker({viewMode: 'years', format: 'YYYY-MM-DD'});				 
				<?php
				$js .= ob_get_clean();
				wp_add_inline_script( 'clock-in-subs-staff-request-script1', $js ); ?>
			</tr>
			<tr>
				<td><label for="Role"><?php esc_html_e('End Date', CIP_FREE_TXTDM );?></label></td>
				<td><input type="input" class="form-control edate" id="end_dates" name="end_date" placeholder="End Date (Use Format YYYY-MM-DD)" value="<?php echo esc_attr($end_date); ?>"></td>
				<?php 
				wp_register_script( 'clock-in-subs-staff-request-script2', false );
				wp_enqueue_script( 'clock-in-subs-staff-request-script2' );
				$js = " ";
				ob_start(); ?>
					jQuery('#end_dates').datetimepicker({viewMode: 'years', format: 'YYYY-MM-DD'});					 
				<?php
				$js .= ob_get_clean();
				wp_add_inline_script( 'clock-in-subs-staff-request-script2', $js ); ?>
			</tr>
			<tr>
				<td><label for="Role"><?php esc_html_e('Status', CIP_FREE_TXTDM );?></label></td>
				<td><label for="Role"><?php if($status == 1) {esc_html_e( "Pending");}elseif($status == 2) {esc_html_e( "Approved");}elseif($status == 3) {esc_html_e( "Cancelled");} ?></label>
				<input type="hidden" class="form-control" id="status" name="status" placeholder="Leave Days" value="<?php echo esc_attr($status); ?>">
				</td>					
			</tr>
		</table>
		</div>
		<?php
	} // end update
	
	if($action == "update-now") {
		if ( ! wp_verify_nonce( $_POST['update_staff_request_nonce_name'], 'update_staff_request_nonce_action' ) ) {
			print 'Sorry, your update request nonce did not verify.';
			exit;
		} else {
			// nonce verified - process form data
			$key = sanitize_text_field($_POST['id']);	
			$name = sanitize_text_field($_POST['name']);
			$event = sanitize_text_field($_POST['event']);			
			$event_disc = sanitize_text_field($_POST['event_disc']);
			$start_date = sanitize_text_field($_POST['start_date']);
			$end_date = sanitize_text_field($_POST['end_date']);
			$date1=date_create($start_date);
			$date2=date_create($end_date);
			$diff=date_diff($date1,$date2);
			$leaves = $diff->format("%a");
			$leaves = $leaves+1;
			$status = sanitize_text_field($_POST['status']);
			$saved_staff_requests = get_option("cip_staff_request");
			$saved_staff_requests[$key] = array ( 
				'name' => $name,
				'event' => $event,
				'event_disc' => $event_disc,
				'start_date' => $start_date,
				'end_date' => $end_date,
				'leaves' => $leaves,
				'status' => $status,
			);

			update_option("cip_staff_request", $saved_staff_requests);
		}
	}
	
	// update
	if($action == "delete") {
		$key = sanitize_text_field($_POST['id']);
		$staff_request = get_option("cip_staff_request");
		unset($staff_request[$key]);
		?><div id="delete-staff_request-result">staff_request deleted successfully.</div><?php
		$staff_request = update_option("cip_staff_request", $staff_request);
	} // end delete
}
?>
<?php 
wp_register_script( 'clock-in-subs-staff-request-script3', false );
wp_enqueue_script( 'clock-in-subs-staff-request-script3' );
$js = " ";
ob_start(); ?>
	jQuery(function () {
		jQuery('#start_date').datetimepicker({viewMode: 'years', format: 'YYYY-MM-DD'});
		jQuery('#end_date').datetimepicker({viewMode: 'years', format: 'YYYY-MM-DD'});
	});
<?php
$js .= ob_get_clean();
wp_add_inline_script( 'clock-in-subs-staff-request-script3', $js ); ?>			