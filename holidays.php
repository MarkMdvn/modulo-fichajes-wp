<?php 
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

$date_format = get_option('date_format');
$time_format = get_option('time_format');
// get all records
global $wpdb;
$holidays = get_option("cip_official_holidays");

if ( isset( $_REQUEST['add-holiday-button'] ) ) {

	$saved_holidays = get_option( "cip_official_holidays" );
	$name           = sanitize_text_field( $_POST['name'] );
	$status         = sanitize_text_field( $_POST['status'] );
	$start_date     = sanitize_text_field( $_POST['start_date'] );
	$end_date       = sanitize_text_field( $_POST['end_date'] );
	$date1          = date_create( $start_date );
	$date2          = date_create( $end_date );
	$diff           = date_diff( $date1,$date2 );
	$leaves         = $diff->format("%a");	
	$leaves         = $leaves+1;	
	
	$new_holiday = array ( 
		'name'       => $name,
		'start_date' => $start_date,
		'end_date'   => $end_date,
		'leaves'     => $leaves,
		'status'     => $status,
	);

	if ( empty ( $saved_holidays ) ) {
		$saved_holidays = array();
	}

	array_push( $saved_holidays, $new_holiday );
	if ( update_option("cip_official_holidays" , $saved_holidays ) ) {
		echo "<div class='alert alert-success'>
			  <strong>Éxito!</strong>
			</div>";
	} else {
		echo "<div class='alert alert-danger'>
			  <strong>Failed!</strong> 
			</div>";
	}

}
?>
<nav class="navbar navbar-dark bg-dark main-dashboard-cip other-pages">
	<a class="navbar-brand" href="<?php echo esc_url( admin_url( 'admin.php?page=clock-in-portal' ) ); ?>"><i class="fas fa-home"></i></a>
  	<a class="navbar-brand" href="#"><?php esc_html_e('Administración de Vacaciones', CIP_FREE_TXTDM ); ?></a>
  	<div class="form-inline my-2 my-lg-0">
  		<button class="btn btn-outline-success my-2 my-sm-0" data-toggle="modal" data-target="#add-holiday-modal"><i class="fas fa-plus" aria-hidden="true"></i>&nbsp;<?php esc_html_e(' Añadir Vacaciones', CIP_FREE_TXTDM );?></button>&nbsp;&nbsp;
      	<a class="navbar-brand" href="<?php echo esc_url( admin_url( 'admin.php?page=cip-settings' ) ); ?>"><i class="fas fa-cog"></i></a>
    </div>
</nav>

<div class="other-page-content">
	<!-- Nav tabs -->
	<ul class="nav nav-tabs" role="tablist">
		<li class="nav-item" role="presentation" class="active sm-labels nav-item"><a class="nav-link active" href="#official-holiday" aria-controls="official-holiday" role="tab" data-toggle="tab"><?php esc_html_e('Vacaciones', CIP_FREE_TXTDM );?></a></li>
	</ul>

	<!-- Tabs -->
	<div class="tab-content">
		<!--official-holiday-->
		<div role="tabpanel" class="tab-pane active" id="official-holiday">
			
			<table class="table table-hover table-striped">
				<thead>
					<tr class="info main_tb_head">
						<th><?php esc_html_e('#', CIP_FREE_TXTDM );?></th>
						<th><?php esc_html_e('Nombre', CIP_FREE_TXTDM );?></th>
						<th><?php esc_html_e('Fecha', CIP_FREE_TXTDM );?></th>
						<th><?php esc_html_e('Día/s', CIP_FREE_TXTDM );?></th>
						<th><?php esc_html_e('Estado', CIP_FREE_TXTDM );?></th>
						<th><?php esc_html_e('Fecha', CIP_FREE_TXTDM );?></th>
					</tr>
				</thead>
				<?php if($holidays = get_option("cip_official_holidays")) { ?>
				<tbody>
					<?php	
				//Next 12 month	
				$startdate   = new \DateTime(date("Y")."-01-01");                                                                                         
				$startdate   = $startdate->format("Y-m-d");
				$plusOneYear = date("Y")+1;
				$enddate     = new \DateTime($plusOneYear."-12-31");                                                                                                                                                                         
				$enddate     = $enddate->format("Y-m-d");	
				$i           = strtotime($startdate);
				$j           = strtotime($enddate);
				$all_dates = array();
				for($i; $i <= $j; $i = strtotime(date("Y-m-d", strtotime("+1 day", $i))) ) {
					array_push( $all_dates, date("Y-m-d", $i) );
				}
				$n = 1;
				foreach( $all_dates as $row_date ) {		
					if ( ! empty ( $holidays ) ) {						
						foreach( $holidays as $key => $holiday ) {
							$status = $holiday['status'];
							if($status == 1) $status = "Enable";
							if($status == 2) $status = "Disable";
							$start_date = $holiday['start_date'];
							$end_date = $holiday['end_date'];
							if(strtotime($start_date))
							$start_date = date($date_format , strtotime($holiday['start_date']));
							if(strtotime($end_date))
							$end_date = date($date_format , strtotime($holiday['end_date']));
								if($holiday['start_date'] == $row_date){
									?>
									<tr>
										<td><?php esc_html_e( $n.".");?></td>
										<td><?php esc_html_e( $holiday['name']); ?></td>
										<?php if ($end_date == $start_date){ ?>
										<td><?php if($end_date != "") { esc_html_e( $end_date); } ?></td>
										<?php }else{ ?>
										<td><?php esc_html_e( $start_date); if($end_date != "") { ?> - <?php esc_html_e( $end_date); } ?></td>
										<?php } ?>
										<td>For <?php esc_html_e( $holiday['leaves']); ?> Day(s)</td>
										<td><?php esc_html_e( $status); ?></td>
										<td>
											<a href="#" class="btn btn-info" title="View" data-toggle="modal" data-target="#view-holiday-modal" onclick="return DoAction('view', '<?php echo esc_attr( $key); ?>');"><i class="fas fa-eye" aria-hidden="true"></i></a>
											<a href="#" class="btn btn-info" title="Update" data-toggle="modal" data-target="#update-holiday-modal" onclick="return DoAction('update', '<?php echo esc_attr( $key); ?>');"><i class="fas fa-edit" aria-hidden="true"></i></a>
											<a href="#" class="btn btn-info" title="Delete" data-toggle="modal" data-target="#delete-holiday-modal" onclick="return DoAction('delete', '<?php echo esc_attr( $key); ?>');"><i class="fas fa-times" aria-hidden="true"></i></a>
										</td>
									</tr>
							<?php  $n++;} 
						} } } ?>
				</tbody>
				<?php } else {
						echo "<tbody><tr><td colspan='6'>No existen empleados.</td></tr></tbody>";
					} ?>		
<!--				<thead>-->
<!--					<tr class="info main_tb_head">-->
<!--						<th>#</th>-->
<!--						<th>--><?php //esc_html_e('Nombre', CIP_FREE_TXTDM );?><!--</th>-->
<!--						<th>--><?php //esc_html_e('Fecha', CIP_FREE_TXTDM );?><!--</th>-->
<!--						<th>--><?php //esc_html_e('Día/s', CIP_FREE_TXTDM );?><!--</th>-->
<!--						<th>--><?php //esc_html_e('Estado', CIP_FREE_TXTDM );?><!--</th>-->
<!--						<th>--><?php //esc_html_e('Fecha', CIP_FREE_TXTDM );?><!--</th>-->
<!--					</tr>-->
<!--				</thead>-->
			</table>
		</div>
		<!--official-holiday end--->
		
		<!--upcoming-holiday--->
		<div role="tabpanel" class="tab-pane" id="upcoming-holiday">
			
		</div>
		<!--upcoming-holiday end--->
		
	</div>
</div>

<!-- Add Holiday Modal-->
<div class="modal fade" id="add-holiday-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<form id="add-holiday-form" name="add-holiday-form" method="post" action="">
				<div class="modal-header">
					<h4 class="modal-title" id="myModalLabel"><?php esc_html_e('Crear Vacaciones', CIP_FREE_TXTDM );?></h4>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					
				</div>
				<div class="modal-body">
					<div class="form-group">
						<label for="name"><?php esc_html_e('Nombre', CIP_FREE_TXTDM );?></label>
						<input type="input" class="form-control" id="name" name="name" placeholder="Type Holiday Name" required>
						<?php wp_nonce_field( 'add_holiday_nonce_action', 'add_holiday_nonce_name' ); ?>
					</div>
					<div class="form-group">
						<label for="start_date"><?php esc_html_e('Comienzo', CIP_FREE_TXTDM );?></label>
						<input type="input" class="form-control sdate" id="start_date" name="start_date" placeholder="(Formato YYYY-MM-DD)" required>
					</div>
					
					<div class="form-group">
						<label for="end_date"><?php esc_html_e('Finalización', CIP_FREE_TXTDM );?></label>
						<input type="input" class="form-control edate" id="end_date" name="end_date" placeholder="(Formato YYYY-MM-DD)" required>
					</div>
					<div class="form-group">
						<label for="status"><?php esc_html_e('Estado', CIP_FREE_TXTDM );?></label>
						<select id="status" name="status" class="form-control">
							<optgroup label="Select Any Status">
								<option value="1"><?php esc_html_e('Activo', CIP_FREE_TXTDM );?></option>
								<option value="2"><?php esc_html_e('Inactivo', CIP_FREE_TXTDM );?></option>
							</optgroup>
						</select>
					</div>					
				</div>
				<div class="modal-footer">
					<input type="submit" id="add-holiday-button" name="add-holiday-button" class="btn btn-success" value="Añadir">
					<button type="button" id="add-holiday-close" name="add-holiday-close" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
					<div id="add-loading-icon" style="display:none;">
						<?php esc_html_e('Processing...', CIP_FREE_TXTDM );?><i class="fas fa-spinner fa-3x" aria-hidden="true"></i>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>

<!-- View Holiday Modal-->
<div class="modal fade" id="view-holiday-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<form id="view-holiday-form" name="view-holiday-form">
				<div class="modal-header">
					<h4 class="modal-title" id="myModalLabel"><?php esc_html_e('Holiday Details', CIP_FREE_TXTDM );?></h4>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					
				</div>
				<div class="modal-body" id="view-modal-body">
					<div id="view-loading-icon" style="display:none;">
					<?php esc_html_e('Processing...', CIP_FREE_TXTDM );?><i class="fas fa-spinner fa-3x" aria-hidden="true"></i>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
				</div>
			</form>
		</div>
	</div>
</div>

<!-- Update Holiday Modal-->
<div class="modal fade" id="update-holiday-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<form id="update-holiday-form" name="update-holiday-form">
				<div class="modal-header">
					<h4 class="modal-title" id="myModalLabel"><?php esc_html_e('Update Holiday Details', CIP_FREE_TXTDM );?></h4>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					
				</div>
				<div class="modal-body" id="update-modal-body">
					<div id="update-loading-icon" style="display:none;">
						<?php esc_html_e('Processing...', CIP_FREE_TXTDM );?><i class="fas fa-spinner fa-3x" aria-hidden="true"></i>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" id="update-holiday-button" name="update-holiday-button" class="btn btn-success" onclick="return DoAction('update-now', '');"><?php esc_html_e('Update Holiday', CIP_FREE_TXTDM );?></button>
					<button type="button" id="update-holiday-close" name="update-holiday-close" class="btn btn-danger" data-dismiss="modal"><?php esc_html_e('Close', CIP_FREE_TXTDM );?></button>
					<div id="update-now-loading-icon" style="display:none;">
							<?php esc_html_e('Processing...', CIP_FREE_TXTDM );?><i class="fas fa-spinner fa-3x" aria-hidden="true"></i>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
<?php 
wp_register_script( 'clock-in-holidays-script', false );
wp_enqueue_script( 'clock-in-holidays-script' );
$js = " ";
ob_start(); ?>

//tabs
jQuery('#myTabs a').click(function (e) {
  e.preventDefault()
  jQuery(this).tab('show');
});

// add holiday modal call
jQuery('#add-holiday-modal').on('shown.bs.modal', function (){ 	
	jQuery("#add-holiday-button").prop('enable', true);
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
		jQuery("div#view-holiday-result").remove();
		jQuery("#view-loading-icon").show();
		var data_values = "action=" + action + "&id=" + id;
		//post data
		jQuery.ajax({
			type: "post",
			url: location.href,
			data: data_values,
			contentType: "application/x-www-form-urlencoded",
			success: function(responseData, textStatus, jqXHR) {
				var result = jQuery(responseData).find('div#view-holiday-result');
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
		var holiday_name = jQuery("#holiday_name").val();
		var start_dates  = jQuery("#start_dates").val();
		var end_dates 	 = jQuery("#end_dates").val();
		if( holiday_name == "" ) {
			jQuery("#holiday_name").next(".required").css('display','block').fadeOut(5000);
			jQuery("#holiday_name").focus();
			return false;
		}
		if( start_dates == "" ) {
			jQuery("#start_dates").next(".required").css('display','block').fadeOut(5000);
			jQuery("#start_dates").focus();
			return false;
		}
		if( end_dates == "" ) {
			jQuery("#end_dates").next(".required").css('display','block').fadeOut(5000);
			jQuery("#end_dates").focus();
			return false;
		}
		jQuery("#update-holiday-button").hide();
		jQuery("#update-holiday-close").hide();
		jQuery("#update-now-loading-icon").show();
		var data_values = jQuery("#update-holiday-form").serialize() + "&action=" + action;
		//post data
		jQuery.ajax({
			type: "post",
			url: location.href,
			data: data_values,
			contentType: "application/x-www-form-urlencoded",
			success: function(responseData, textStatus, jqXHR) {
				var result = jQuery(responseData).find('div#update-holiday-result');
				jQuery("#update-now-loading-icon").hide();
				location.reload();
			},
			error: function(jqXHR, textStatus, errorThrown) {
			}
		});
	}
	
	//delete
	if(action == "delete") {
		if (confirm("Are you sure want to delete this holiday?") == true) {
			var data_values = "&action=" + action + "&id=" + id;
			//post data
			jQuery.ajax({
				type: "post",
				url: location.href,
				data: data_values,
				contentType: "application/x-www-form-urlencoded",
				success: function(responseData, textStatus, jqXHR) {
					var result = jQuery(responseData).find('div#delete-holiday-result');
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
wp_add_inline_script( 'clock-in-holidays-script', $js ); ?>
<?php
//Action Executor
if ( isset ( $_POST['action'] ) ) {	
	$action = sanitize_text_field( $_POST['action'] );	
	// view
	if ( $action == "view" ) {
		$key            = sanitize_text_field( $_POST['id'] );
		$saved_holidays = get_option( "cip_official_holidays" );
		$holiday        = $saved_holidays[$key];
		$name           = $holiday['name'];
		$start_date     = $holiday['start_date'];
		$end_date       = $holiday['end_date'];
		$leaves         = $holiday['leaves'];
		$status         = $holiday['status'];
	?>
		<div id="view-holiday-result">
		<table class="table table-striped">
			<tr>
				<td><label for="Role"><?php esc_html_e('Holiday Name', CIP_FREE_TXTDM );?></label></td>
				<td><?php esc_html_e( ucwords($name)); ?></td>
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
		</table>
		</div>
		<?php
	} // end view
	
	
	// update
	if ( $action == "update" ) {
		$key            = sanitize_text_field( $_POST['id'] );
		$saved_holidays = get_option( "cip_official_holidays" );
		$holiday        = $saved_holidays[$key];
		$name           = $holiday['name'];
		$start_date     = $holiday['start_date'];
		$end_date       = $holiday['end_date'];		
		$status         = $holiday['status'];
	?>
		<div id="update-result">
			<table class="table table-striped">
			<tr>
				<td><label for="holiday_name"><?php esc_html_e('Holiday Name', CIP_FREE_TXTDM );?></label></td>
				<td>
					<input type="hidden" class="form-control" id="id" name="id" placeholder="ID" value="<?php echo esc_attr($key); ?>">
					<input type="input" class="form-control" id="holiday_name" name="name" placeholder="Type Holiday Name" value="<?php echo esc_attr($name); ?>">
					<div class="required" style="display:none; color:red"><?php esc_html_e('Required field.', CIP_FREE_TXTDM );?></div>
					<?php wp_nonce_field( 'update_holiday_nonce_action', 'update_holiday_nonce_name' ); ?>
				</td>
			</tr>
			<tr>
				<td><label for="start_dates"><?php esc_html_e('Start Date', CIP_FREE_TXTDM );?></label></td>
				<td>
					<input type="input" class="form-control sdate" id="start_dates" name="start_date" placeholder="Start Date (Use Format YYYY-MM-DD)" value="<?php echo esc_attr ($start_date); ?>">
					<div class="required" style="display:none; color:red"><?php esc_html_e('Required field.', CIP_FREE_TXTDM );?></div>
				</td>
				<?php 
				wp_register_script( 'clock-in-holidays-script1', false );
				wp_enqueue_script( 'clock-in-holidays-script1' );
				$js = " ";
				ob_start(); ?>
					jQuery('#start_dates').datetimepicker({viewMode: 'years', format: 'YYYY-MM-DD'}); 	
				<?php
				$js .= ob_get_clean();
				wp_add_inline_script( 'clock-in-holidays-script1', $js ); ?>
		</tr>
			<tr>
				<td><label for="end_dates"><?php esc_html_e('End Date', CIP_FREE_TXTDM );?></label></td>
				<td>
					<input type="input" class="form-control edate" id="end_dates" name="end_date" placeholder="End Date (Use Format YYYY-MM-DD)" value="<?php echo esc_attr($end_date); ?>">
					<div class="required" style="display:none; color:red"><?php esc_html_e('Required field.', CIP_FREE_TXTDM );?></div>
				</td>
				<?php 
				wp_register_script( 'clock-in-holidays-script2', false );
				wp_enqueue_script( 'clock-in-holidays-script2' );
				$js = " ";
				ob_start(); ?>
					jQuery('#end_dates').datetimepicker({viewMode: 'years', format: 'YYYY-MM-DD'}); 
				<?php
				$js .= ob_get_clean();
				wp_add_inline_script( 'clock-in-holidays-script2', $js ); ?>
			</tr>
			<tr>
				<td><label for="status"><?php esc_html_e('Status', CIP_FREE_TXTDM );?></label></td>
				<td>
					<select id="status" name="status" class="form-control">
						<optgroup label="Select Any Status">
							<option value="1" <?php if($status == 1) echo esc_attr( "selected=selected"); ?>><?php esc_html_e('Enable', CIP_FREE_TXTDM );?></option>
							<option value="2" <?php if($status == 2) echo esc_attr( "selected=selected"); ?>><?php esc_html_e('Disable', CIP_FREE_TXTDM );?></option>
						</optgroup>
					</select>
				</td>					
			</tr>
		</table>
		</div>
		<?php
	} // end update
	
	if($action == "update-now") {
		if ( ! wp_verify_nonce( $_POST['update_holiday_nonce_name'], 'update_holiday_nonce_action' ) ) {
			print 'Sorry, your update holiday nonce did not verify.';
			exit;
		} else {
			// nonce verified - process form data
			$key            = sanitize_text_field( $_POST['id'] );	
			$name           = sanitize_text_field( $_POST['name'] );
			$start_date     = sanitize_text_field( $_POST['start_date'] );
			$end_date       = sanitize_text_field( $_POST['end_date'] );
			$date1          = date_create( $start_date );
			$date2          = date_create( $end_date );
			$diff           = date_diff( $date1,$date2 );
			$leaves         = $diff->format("%a");
			$leaves         = $leaves+1;
			$status         = sanitize_text_field( $_POST['status']  );
			$saved_holidays = get_option( "cip_official_holidays" );
			$saved_holidays[$key] = array ( 
				'name'       => $name,
				'start_date' => $start_date,
				'end_date'   => $end_date,
				'leaves'     => $leaves,
				'status'     => $status,
			);
			update_option( "cip_official_holidays", $saved_holidays );
		}
	}
	
	// update
	if ( $action == "delete" ) {
		$key      = sanitize_text_field( $_POST['id'] );
		$holidays = get_option( "cip_official_holidays" );
		unset( $holidays[$key] );
		?><div id="delete-holiday-result">Holiday deleted successfully.</div><?php
		$holidays = update_option( "cip_official_holidays", $holidays );
	} // end delete
}
?>
<?php 
wp_register_script( 'clock-in-holidays-script3', false );
wp_enqueue_script( 'clock-in-holidays-script3' );
$js = " ";
ob_start(); ?>
	jQuery(document).ready(function () {
		jQuery('#start_date').datetimepicker({viewMode: 'years', format: 'YYYY-MM-DD'});
		jQuery('#end_date').datetimepicker({viewMode: 'years', format: 'YYYY-MM-DD'});
		jQuery('#update-result .sdate').datetimepicker({viewMode: 'years', format: 'YYYY-MM-DD'});
		jQuery('#update-result .edate').datetimepicker({viewMode: 'years', format: 'YYYY-MM-DD'});
	});
<?php
$js .= ob_get_clean();
wp_add_inline_script( 'clock-in-holidays-script3', $js ); ?>
			