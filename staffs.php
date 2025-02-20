<?php 
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

// get all records
global $wpdb;
$staff_table = $wpdb->base_prefix . "sm_staffs";
$staff_category_table = $wpdb->base_prefix . "sm_staff_category";

//get all staffs
$all_active_staffs = $wpdb->get_results($wpdb->prepare("SELECT * FROM `$staff_table` WHERE `status` LIKE %d", 1)); //worked
$all_inactive_staffs = $wpdb->get_results($wpdb->prepare("SELECT * FROM `$staff_table` WHERE `status` LIKE %d", 2)); //worked
?>
<nav class="navbar navbar-dark bg-dark main-dashboard-cip other-pages">
	<a class="navbar-brand" href="<?php echo esc_url( admin_url( 'admin.php?page=clock-in-portal' ) ); ?>"><i class="fas fa-home"></i></a>
  	<a class="navbar-brand" href="#"><?php esc_html_e('Administrar Empleados', CIP_FREE_TXTDM ); ?></a>
  	<div class="form-inline my-2 my-lg-0">
  		<button class="btn btn-outline-success my-2 my-sm-0" data-toggle="modal" data-target="#add-existing-staff-modal"><i class="fas fa-user" aria-hidden="true"></i>&nbsp;<?php esc_html_e('Add Staff', CIP_FREE_TXTDM );?></button>&nbsp;&nbsp;
      	<a class="navbar-brand" href="<?php echo esc_url( admin_url( 'admin.php?page=cip-settings' ) ); ?>"><i class="fas fa-cog"></i></a>
    </div>
</nav>

<div class="other-page-content">
	<!-- Nav tabs -->
	<ul class="nav nav-tabs" role="tablist">
		<li class="nav-item" role="presentation" class="active sm-labels"><a class="nav-link active" href="#active-staff" aria-controls="home" role="tab" data-toggle="tab"><?php esc_html_e('Personal Activo', CIP_FREE_TXTDM );?></a></li>
		<li class="nav-item" role="presentation" class="sm-labels"><a class="nav-link" href="#in-active-staff" aria-controls="in-active-staff" role="tab" data-toggle="tab"><?php esc_html_e('Personal Inactivo', CIP_FREE_TXTDM );?></a></li>
	</ul>
	<!-- Tabs -->
	<div class="tab-content">
		<!--active staffs-->
		<div role="tabpanel" class="tab-pane active" id="active-staff">
			<table class="table table-hover table-striped">
				<thead>
					<tr class="info main_tb_head">
						<th><?php esc_html_e('#', CIP_FREE_TXTDM );?></th>
						<th><?php esc_html_e('Nombre', CIP_FREE_TXTDM );?></th>
						<th><?php esc_html_e('Cargo', CIP_FREE_TXTDM );?></th>
						<th><?php esc_html_e('Acción', CIP_FREE_TXTDM );?></th>
					</tr>
				</thead>
				<?php if ( ! empty ( $all_active_staffs ) ) { ?>
				<tbody>
					<?php					
						$no = 1;
						$color = "#FFFFFF";
						foreach($all_active_staffs as $staff) {
							$staff_id = $staff->staff_id;
							$staff_cat_id = $staff->cat_id;
							
							// get staff details
							$userinfo = get_userdata($staff_id); //worked if staff_id exist						
							if ( ! empty ( $userinfo ) ) {
							if ( $userinfo->ID ) { // Condition worked when staff_id exist otherwise data-table show as null data
								$role  = $userinfo->roles[0];
								$fname = $userinfo->first_name;
								$lname = $userinfo->last_name;
								$email = $userinfo->user_email;
								
								// get designation name
								if($staff_cat_id) {
									$designation_details = $wpdb->get_row($wpdb->prepare("SELECT * FROM `$staff_category_table` WHERE `id` = %d", $staff_cat_id));
									$designation = $designation_details->name;
									
									$color = $designation_details->color;
								}
							}
					?>
					<tr style="opacity: 0.9;">
						<td><?php esc_html_e( $no); ?>.</td> <?php // If staff_id not existor 0 then data-table show as null data ?>
						<td><?php esc_html_e( ucwords($fname." ".$lname)." ($email)"); ?></td>
						<td><?php esc_html_e( ucwords($designation)); ?></td>
						<td>
							<a href="#" class="btn btn-info" title="View" data-toggle="modal" data-target="#view-staff-modal" onclick="return DoAction('view', '<?php esc_html_e($staff_id); ?>');"><i class="fas fa-eye" aria-hidden="true"></i></a>
							<a href="#" class="btn btn-success" title="Update" data-toggle="modal" data-target="#update-staff-modal" onclick="return DoAction('update', '<?php esc_html_e($staff_id); ?>');"><i class="fas fa-edit" aria-hidden="true"></i></a>
							<a href="#" class="btn btn-danger" title="Delete" data-toggle="modal" data-target="#delete-staff-modal" onclick="return DoAction('delete', '<?php esc_html_e($staff_id); ?>');"><i class="fas fa-times" aria-hidden="true"></i></a>
						</td>
					</tr>
					<?php
						$no++;
						} } // end foreach		
					?>
				</tbody>
				<?php } else {	echo "<tbody><tr><td colspan='6'>No Staff Found.</td></tr></tbody>"; }	?>				
<!--				<thead>-->
<!--					<tr class="info main_tb_head">-->
<!--						<th>#</th>-->
<!--						<th>--><?php //esc_html_e('Name', CIP_FREE_TXTDM );?><!--</th>-->
<!--						<th>--><?php //esc_html_e('Designation', CIP_FREE_TXTDM );?><!--</th>-->
<!--						<th>--><?php //esc_html_e('Action', CIP_FREE_TXTDM );?><!--</th>-->
<!--					</tr>-->
<!--				</thead>-->
			</table>
		</div>
		<!--active staffs end--->
		
		<!--inactive staffs-->
		<div role="tabpanel" class="tab-pane" id="in-active-staff">
			<table class="table table-hover table-striped">
				<thead>
					<tr class="info main_tb_head">
						<th>#</th>
						<th><?php esc_html_e('Name', CIP_FREE_TXTDM );?></th>
						<th><?php esc_html_e('Designation', CIP_FREE_TXTDM );?></th>
						<th><?php esc_html_e('Role', CIP_FREE_TXTDM );?></th>
						<th><?php esc_html_e('Action', CIP_FREE_TXTDM );?></th>
					</tr>
				</thead>
				<?php if ( ! empty ( $all_inactive_staffs ) ) { ?>
				<tbody>
					<?php					
						$no = 1;
						foreach($all_inactive_staffs as $staff) {
							$staff_id = $staff->staff_id;
							$staff_cat_id = $staff->cat_id;
							// get staff details
							$userinfo = get_userdata($staff_id);
							if($userinfo->ID){
								$role = $userinfo->roles[0];
								$fname = $userinfo->first_name;
								$lname = $userinfo->last_name;
								$email = $userinfo->user_email;
								
								// get designation name
								if($staff_cat_id) {
									$designation_details = $wpdb->get_row($wpdb->prepare("SELECT * FROM `$staff_category_table` WHERE `id` = %d", $staff_cat_id));
									$designation = $designation_details->name;
								}
							}
					?>
					<tr>
						<td><?php esc_html_e($no); ?>.</td>
						<td><?php esc_html_e(ucwords($fname." ".$lname)." ($email)"); ?></td>
						<td><?php esc_html_e(ucwords($designation)); ?></td>
						<td><?php esc_html_e(ucwords($role)); ?></td>
						<td>
							<a href="#" class="btn btn-info" title="View" data-toggle="modal" data-target="#view-staff-modal" onclick="return DoAction('view', '<?php echo esc_attr($staff_id); ?>');"><i class="fas fa-eye" aria-hidden="true"></i></a>
							<a href="#" class="btn btn-success" title="Update" data-toggle="modal" data-target="#update-staff-modal" onclick="return DoAction('update', '<?php echo esc_attr($staff_id); ?>');"><i class="fas fa-edit" aria-hidden="true"></i></a>
							<a href="#" class="btn btn-danger" title="Delete" data-toggle="modal" data-target="#delete-staff-modal" onclick="return DoAction('delete', '<?php echo esc_attr($staff_id); ?>');"><i class="fas fa-times" aria-hidden="true"></i></a>
						</td>
					</tr>
					<?php
						$no++;
						}// end foreach					
					?>
				</tbody>
				<?php } else {	echo "<tbody><tr><td colspan='6'>No Staff Found.</td></tr></tbody>"; }	?>
				<thead>
					<tr class="info main_tb_head">
						<th>#</th>
						<th><?php esc_html_e('Name', CIP_FREE_TXTDM );?></th>
						<th><?php esc_html_e('Designation', CIP_FREE_TXTDM );?></th>
						<th><?php esc_html_e('Role', CIP_FREE_TXTDM );?></th>
						<th><?php esc_html_e('Action', CIP_FREE_TXTDM );?></th>
					</tr>
				</thead>
			</table>
		</div>
		<!--in active staffs end--->		
	</div>
</div>

<!-- Add Existing User As Staff Modal-->
<div class="modal fade" id="add-existing-staff-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="tab-content">
				<form id="add-existing-staff-form" name="add-existing-staff-form" class="tab-pane fade in active add-existing-staff-form">
					<div class="modal-header">
						<h4 class="modal-title" id="myModalLabel"><?php esc_html_e('Add User As Staff', CIP_FREE_TXTDM );?></h4>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					</div>				
					<div class="modal-body">
						<div class="form-group">						
							<label for="Role"><?php esc_html_e('Select User', CIP_FREE_TXTDM );?></label>						
							<select id="role" name="role" class="form-control">
								<option value=""><?php esc_html_e('Select User', CIP_FREE_TXTDM );?></option>
							<?php 
								$blogusers = get_users( array( 'search' => '' ) );							
								foreach ( $blogusers as $user ) { ?>
								<option value="<?php echo esc_attr($user->ID); ?>"><?php echo esc_attr($user->display_name); ?> </option>							
							<?php } ?>
							</select>
							<div class="required" style="display:none; color:red"><?php esc_html_e('Required field.', CIP_FREE_TXTDM );?></div>
							<?php wp_nonce_field( 'add_existing_staff_nonce_action', 'add_existing_staff_nonce_name' ); ?>
						</div> 
						<?php 
						//$blogusers = get_users( array( 'search' => '' ) );
						foreach ( $blogusers as $user ) { 
						$user_info = get_userdata($user->ID);
						?>    
						<div class="get_userdata-option" id="<?php echo esc_attr($user->ID); ?>">
							<div class="form-group">
								<label for="username"><?php esc_html_e('Username', CIP_FREE_TXTDM );?></label>
								<input type="hidden" class="form-control" id="id" name="id" value="<?php echo esc_attr( $user->ID); ?>">
								<input type="input" class="form-control" id="username" name="username" value="<?php echo esc_attr($user_info->user_login); ?>" readonly>
							<div class="required" style="display:none; color:red"><?php esc_html_e('Required field.', CIP_FREE_TXTDM );?></div>
							</div>
							<div class="form-group">
								<label for="fname">First Name</label>
								<input type="input" class="form-control" id="fname" name="fname" value="<?php echo esc_attr($user_info->first_name); ?>" readonly>
						<div class="required" style="display:none; color:red"><?php esc_html_e('Required field.', CIP_FREE_TXTDM );?></div>
							</div>
							<div class="form-group">
								<label for="lname">Last Name</label>
								<input type="input" class="form-control" id="lname" name="lname" value="<?php echo esc_attr($user_info->last_name); ?>" readonly>
							</div>
							<div class="form-group">
								<label for="Email">Email</label>
								<input type="input" class="form-control" id="email" name="email" value="<?php echo esc_attr($user->user_email); ?>" readonly>
						<div class="required" style="display:none; color:red"><?php esc_html_e('Required field.', CIP_FREE_TXTDM );?></div>
							</div>
						</div>	
							<?php } ?>
							<div class="form-group">
								<label for="Designation"><?php esc_html_e('Designation', CIP_FREE_TXTDM );?></label>
								<select id="designation" name="designation" class="form-control">
									<option value="0">Select Any Designation</option>
									<?php 
									if ( ! empty ( $all_staff_categories = $wpdb->get_results( "SELECT * FROM `$staff_category_table`" ) ) ) {
										foreach( $all_staff_categories as $staff_category ) {
									?>
									<option value="<?php echo esc_attr($staff_category->id); ?>"><?php esc_html_e( ucwords($staff_category->name)); ?></option>
									<?php
										}// end foreach
									}
									?>							
								</select>
							<div class="required" style="display:none; color:red"><?php esc_html_e('Required field.', CIP_FREE_TXTDM );?></div>
							</div>
							<div class="form-group">
								<label for="Status"><?php esc_html_e('Status', CIP_FREE_TXTDM );?></label>
								<select id="status" name="status" class="form-control">
									<optgroup label="Select Any Status">
										<option value="1"><?php esc_html_e('Active', CIP_FREE_TXTDM );?></option>
										<option value="2"><?php esc_html_e('Inactive', CIP_FREE_TXTDM );?></option>
									</optgroup>
								</select>
							</div>						
					</div>
					<div class="modal-footer">
						<button type="button" id="add-existing-staff-button" name="add-existing-staff-button" class="btn btn-primary" onclick="return DoAction('add-existing', '');"><?php esc_html_e('Add Staff', CIP_FREE_TXTDM );?></button>
						<button type="button" id="add-existing-staff-close" name="add-existing-staff-close" class="btn btn-success" data-dismiss="modal"><?php esc_html_e('Cerrar', CIP_FREE_TXTDM );?></button>
						<button type="button" style="float:left" id="add-New-staff" name="add-New-staff" class="btn btn-primary" data-dismiss="modal" onclick="window.location.href='<?php echo admin_url( 'user-new.php');?>'"><?php esc_html_e('Registered New Staff', CIP_FREE_TXTDM );?></button>
						<div id="add-existing-loading-icon" name="add-existing-loading-icon" style="display:none;">
							<?php esc_html_e('Processing...', CIP_FREE_TXTDM );?><i class="fas fa-spinner fa-3x" aria-hidden="true"></i>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<!-- View Staff Modal-->
<div class="modal fade" id="view-staff-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<form id="view-staff-form" name="view-staff-form">
				<div class="modal-header">
					<h4 class="modal-title" id="myModalLabel"><?php esc_html_e('Información del empleado', CIP_FREE_TXTDM );?></h4>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					
				</div>
				<div class="modal-body" id="view-modal-body">
					<div id="view-loading-icon" >
					<?php esc_html_e('Processing...', CIP_FREE_TXTDM );?><i class="fas fa-spinner fa-3x" aria-hidden="true"></i>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-danger" data-dismiss="modal"><?php esc_html_e('Cerrar', CIP_FREE_TXTDM );?></button>
				</div>
			</form>
		</div>
	</div>
</div>

<!-- Update Holiday Modal-->
<div class="modal fade" id="update-staff-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<form id="update-staff-form" name="update-staff-form">
				<div class="modal-header">
					<h4 class="modal-title" id="myModalLabel"><?php esc_html_e('Actualizar Información', CIP_FREE_TXTDM );?></h4>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					
				</div>
				<div class="modal-body" id="update-modal-body">
					<div id="update-loading-icon" style="display:none;">
					<?php esc_html_e('Processing...', CIP_FREE_TXTDM );?><i class="fas fa-spinner fa-3x" aria-hidden="true"></i>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" id="update-staff-button" name="update-staff-button" class="btn btn-success" onclick="return DoAction('update-now', '');">Actualizar</button>
					<button type="button" id="update-staff-close" name="update-staff-close" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
					<div id="update-now-loading-icon" style="display:none;">
						<?php esc_html_e('Processing...', CIP_FREE_TXTDM );?><i class="fas fa-spinner fa-3x" aria-hidden="true"></i>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
<?php 
wp_register_style( 'clock-in-staffs-style', false );
wp_enqueue_style( 'clock-in-staffs-style' );
$css = " ";
ob_start(); ?>
	.error {
		color: red;
	}
<?php
$css .= ob_get_clean();
wp_add_inline_style( 'clock-in-staffs-style', $css ); ?>

<?php 
wp_register_script( 'clock-in-staffs-script', false );
wp_enqueue_script( 'clock-in-staffs-script' );
$js = " ";
ob_start(); ?>

//tabs
jQuery('#myTabs a').click(function (e) {
  e.preventDefault()
  jQuery(this).tab('show');
});

// add staff modal call
jQuery('#add-new-staff-modal').on('shown.bs.modal', function (){ });
jQuery('#add-existing-staff-modal').on('shown.bs.modal', function (){ });

// action handler - action: view/add/update/delete
function DoAction(action, id){
	var data_values = "";
	jQuery(".error").hide();
	//add-existing
	if(action == "add-existing") {
		
		var role = jQuery('#role').val();
		if( role == "" ) {
			jQuery("#role").next(".required").css('display','block').fadeOut(5000);
			jQuery("#role").focus();
			return false;	
		}
		
		var username = jQuery("#username").val();		
		if(username == "") {
			jQuery("#username").next(".required").css('display','block').focus().fadeOut(4000);
			return false;
		}
			
		var email = jQuery("#email").val();
		if(email == "") {
			jQuery("#email").next(".required").css('display','block').focus().fadeOut(4000);
			return false;
		}
		
		var designation = jQuery(".add-existing-staff-form #designation").val();		
		if(designation == "0") {
			jQuery(".add-existing-staff-form #designation").next(".required").css('display','block').fadeOut(5000);
			jQuery("#add-existing-staff-form").focus();
			jQuery("#designation").focus();
			return false;
		}
		
		jQuery("#add-existing-loading-icon").show();
		jQuery("#add-existing-staff-button").hide();
		jQuery("#add-existing-staff-close").hide();
		var data_values = jQuery(".add-existing-staff-form#add-existing-staff-form").serialize() + "&action=" + action;
		//post data
		jQuery.ajax({
			type: "post",
			url: location.href,
			data: data_values,
			contentType: "application/x-www-form-urlencoded",
			success: function(responseData, textStatus, jqXHR) {
				var result = jQuery(responseData).find('div#action-result');
				jQuery("#add-existing-loading-icon").hide();
				location.reload();
			},
			error: function(jqXHR, textStatus, errorThrown) {
				//console.log(errorThrown);
			}
		});
	}		
	
	//view
	if(action == "view") {
		jQuery("div#view-action-result").remove();
		jQuery("#view-loading-icon").show();
		var data_values = "action=" + action + "&id=" + id;
		//post data
		jQuery.ajax({
			type: "post",
			url: location.href,
			data: data_values,
			contentType: "application/x-www-form-urlencoded",
			success: function(responseData, textStatus, jqXHR) {
				var result = jQuery(responseData).find('div#view-action-result');
				jQuery("#view-loading-icon").hide();
				jQuery("#view-modal-body").after(result);
			},
			error: function(jqXHR, textStatus, errorThrown) {
			}
		});
	}
	
	//update
	if(action == "update") {
		jQuery("div#update-action-result").remove();
		jQuery("#update-loading-icon").show();
		var data_values = "action=" + action + "&id=" + id;
		//post data
		jQuery.ajax({
			type: "post",
			url: location.href,
			data: data_values,
			contentType: "application/x-www-form-urlencoded",
			success: function(responseData, textStatus, jqXHR) {
				var result = jQuery(responseData).find('div#update-action-result');
				jQuery("#update-loading-icon").hide();
				jQuery("#update-modal-body").after(result);
			},
			error: function(jqXHR, textStatus, errorThrown) {
			}
		});
	}
	
	//update-now
	if(action == "update-now") {
		
		var username = jQuery("#update-staff-form #username").val();
		if(username == "") {
			jQuery("#update-staff-form #username").after("Required field.");
			return false;
		}
		
		var password = jQuery("#update-staff-form #password").val();
		if(password == "") {
			jQuery("#update-staff-form #password").after("Required field.");
			return false;
		}
		
		var fname = jQuery("#update-staff-form #fname").val();
		if(fname == "") {
			jQuery("#update-staff-form #fname").after("Required field.");
			return false;
		}
		
		var lname = jQuery("#update-staff-form #lname").val();
		if(lname == "") {
			jQuery("#update-staff-form #lname").after("Required field.");
			return false;
		}
		
		var email = jQuery("#update-staff-form #email").val();
		if(email == "") {
			jQuery("#update-staff-form #email").after("Required field.");
			return false;
		}
		
		jQuery("#update-staff-button").hide();
		jQuery("#update-staff-close").hide();
		jQuery("#update-now-loading-icon").show();
		var data_values = jQuery("#update-staff-form").serialize() + "&action=" + action;
		//post data
		jQuery.ajax({
			type: "post",
			url: location.href,
			data: data_values,
			contentType: "application/x-www-form-urlencoded",
			success: function(responseData, textStatus, jqXHR) {
				var result = jQuery(responseData).find('div#action-result');
				location.reload();
			},
			error: function(jqXHR, textStatus, errorThrown) {
			}
		});
	}
	
	//delete
	if(action == "delete") {
		if (confirm("¿Seguro que quieres borra al empleado?") == true) {
			var data_values = "&action=" + action + "&id=" + id;
			//post data
			jQuery.ajax({
				type: "post",
				url: location.href,
				data: data_values,
				contentType: "application/x-www-form-urlencoded",
				success: function(responseData, textStatus, jqXHR) {
					var result = jQuery(responseData).find('div#action-result');
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
wp_add_inline_script( 'clock-in-staffs-script', $js ); ?>
<?php
//Action Executor
if(isset($_POST['action'])){
	//print_r($_POST);
	$action = sanitize_text_field($_POST['action']);
	
	
	
	//add Existing users
	if($action == "add-existing") {
		if ( ! wp_verify_nonce( $_POST['add_existing_staff_nonce_name'], 'add_existing_staff_nonce_action' ) ) {
			print 'Sorry, your add staff nonce did not verify.';
			exit;
		} else {
		   // nonce verified - process form data			
			$staff_id = sanitize_text_field($_POST['role']);
			$get_id = sanitize_text_field($_POST['id']);
			$username = sanitize_text_field($_POST['username']);			
			$fname = sanitize_text_field($_POST['fname']);
			$lname = sanitize_text_field($_POST['lname']);
			$email = sanitize_email($_POST['email']);
			$designation = sanitize_text_field($_POST['designation']);
			$status = sanitize_text_field($_POST['status']);			
			
			$staff_check =$wpdb->get_results( "SELECT * FROM $staff_table WHERE staff_id = '$staff_id'" );
			if($staff_check)  {
			   echo "<div id='add-action-result'>Error: Unable to staff registered( $username or $email already exist ).</div>";
			}else{			
				$wpdb->query($wpdb->prepare("INSERT INTO `$staff_table` (`id`, `staff_id`, `cat_id`, `status`) VALUES (NULL, %d, %d, %d)", $staff_id, $designation, $status)); 
			}			
		}
	} // end add
	// view
	if($action == "view") {
		$userid = sanitize_text_field($_POST['id']);
		$userinfo = get_userdata($userid);
		if($userinfo->ID){
			$role = $userinfo->roles[0];
			$username = $userinfo->user_login;
			$fname = $userinfo->first_name;
			$lname = $userinfo->last_name;
			$email = $userinfo->user_email;
			
			// get user status
			$extra_data = $wpdb->get_row($wpdb->prepare("SELECT * FROM `$staff_table` WHERE `staff_id` = %d", $userid));
			$status = $extra_data->status;
			
			// get designation name
			if($designation_id = $extra_data->cat_id) {
				$designation_details = $wpdb->get_row($wpdb->prepare("SELECT * FROM `$staff_category_table` WHERE `id` = %d", $designation_id));
				$designation = $designation_details->name;
			}
			?>
			<div id="view-action-result">
				<table class="table table-striped">
					<tr>
						<td><label for="Role"><?php esc_html_e('Role', CIP_FREE_TXTDM );?></label></td>
						<td><?php esc_html_e( ucwords($role)); ?></td>					
					</tr>
					<tr>
						<td><label for="Role"><?php esc_html_e('Nombre de usuario', CIP_FREE_TXTDM );?></label></td>
						<td><?php esc_html_e( $username); ?></td>					
					</tr>
					<tr>
						<td><label for="Role"><?php esc_html_e('Nombre', CIP_FREE_TXTDM );?></label></td>
						<td><?php esc_html_e( ucwords($fname)); ?></td>					
					</tr>
					<tr>
						<td><label for="Role"><?php esc_html_e('Apellidos', CIP_FREE_TXTDM );?></label></td>
						<td><?php esc_html_e( ucwords($lname)); ?></td>					
					</tr>
					<tr>
						<td><label for="Role"><?php esc_html_e('Email', CIP_FREE_TXTDM );?></label></td>
						<td><?php esc_html_e( $email); ?></td>					
					</tr>
					<tr>
						<td><label for="Designation"><?php esc_html_e('Cargo', CIP_FREE_TXTDM );?></label></td>
						<td><?php esc_html_e( ucwords($designation)); ?></td>					
					</tr>
					<tr>
						<td><label for="Role"><?php esc_html_e('Status', CIP_FREE_TXTDM );?></label></td>
						<td><?php if($status == 1) esc_html_e( "ACTIVE"); ?><?php if($status == 2) esc_html_e( "INACTIVE"); ?></td>					
					</tr>
				</table>
			</div>
			<?php			
		} else {
			echo "<div id='view-action-result'>Ha sucedido un error</div>";
		}
	} // end view
	
	
	// update
	if($action == "update") {
		$userid = sanitize_text_field($_POST['id']);
		$userinfo = get_userdata($userid);
		if($userinfo->ID){
			$role = $userinfo->roles[0];
			$username = $userinfo->user_login;
			$fname = $userinfo->first_name;
			$lname = $userinfo->last_name;
			$email = $userinfo->user_email;
			
			// get user status
			$extra_data = $wpdb->get_row($wpdb->prepare("SELECT * FROM `$staff_table` WHERE `staff_id` = %d", $userid));
			$status = $extra_data->status;
			$designation_id = $extra_data->cat_id;
			?>
			<div id="update-action-result">
				<table class="table">
					<tr>
						<td><label for="Role"><?php esc_html_e('Role', CIP_FREE_TXTDM );?></label></td>
						<td>
							<?php esc_html_e( ucwords($role)); ?>
							<?php wp_nonce_field( 'update_staff_nonce_action', 'update_staff_nonce_name' ); ?>
						</td>	
					</tr>
					<tr>
						<td><label for="Username"><?php esc_html_e('Username', CIP_FREE_TXTDM );?></label></td>
						<td>
							<input type="hidden" class="form-control" id="id" name="id" value="<?php echo esc_attr($userid); ?>" readonly>
							<input type="input" class="form-control" id="username" name="username" placeholder="Type Username" value="<?php echo esc_attr($username); ?>" readonly>
						
						</td>
					</tr>
					<tr>
						<td><label for="First Name"><?php esc_html_e('Nombre', CIP_FREE_TXTDM );?></label></td>
						<td><input type="input" class="form-control" id="fname" name="fname" placeholder="Type First Name" value="<?php echo esc_attr($fname); ?>" readonly></td>
					</tr>
					<tr>
						<td><label for="Last Name"><?php esc_html_e('Apellidos', CIP_FREE_TXTDM );?></label></td>
						<td><input type="input" class="form-control" id="lname" name="lname" placeholder="Type Last Name" value="<?php echo esc_attr($lname); ?>" readonly></td>
					</tr>
					<tr>
						<td><label for="Email"><?php esc_html_e('Email', CIP_FREE_TXTDM );?></label></td>
						<td><input type="input" class="form-control" id="email" name="email" placeholder="Type Email" value="<?php echo esc_attr($email); ?>" readonly></td>
					</tr>
					<tr>
						<td><label for="Designation"><?php esc_html_e('Cargo', CIP_FREE_TXTDM );?></label></td>
						<td>
							<select id="designation" name="designation" class="form-control">
								<optgroup label="Select Any Designation">
								<?php 
								if( ! empty ( $all_staff_categories = $wpdb->get_results( "SELECT * FROM `$staff_category_table`" ) ) ) {
									foreach( $all_staff_categories as $staff_category ) {
								?>
								<option value="<?php echo esc_attr($staff_category->id); ?>" <?php if($designation_id == $staff_category->id) echo esc_attr( "selected=selected"); ?>><?php esc_html_e( ucwords($staff_category->name) ); ?></option>
								<?php
									}// end foreach
								}
								?>
								</optgroup>
							</select>
						</td>
					</tr>
					<tr>
						<td><label for="Status"><?php esc_html_e('Status', CIP_FREE_TXTDM );?></label></td>
						<td>
							<select id="status" name="status" class="form-control">
								<optgroup label="Select Any Status">
								<option value="1" <?php if($status == 1) echo esc_attr( "selected=selected"); ?>><?php esc_html_e('Active', CIP_FREE_TXTDM );?></option>
								<option value="2" <?php if($status == 2) echo esc_attr( "selected=selected"); ?>><?php esc_html_e('Inactive', CIP_FREE_TXTDM );?></option>
								</optgroup>
							</select>
						</td>
					</tr>					
				</table>
			</div>
			<?php			
		} else {
			echo "<div id='update-action-result'>Error: Unable to fetch data.</div>";
		}
	} // end view
	
	// update
	if($action == "update-now") {
		if ( ! wp_verify_nonce( $_POST['update_staff_nonce_name'], 'update_staff_nonce_action' ) ) {
			print 'Sorry, your update staff nonce did not verify.';
			exit;
		} else {
			$userid = sanitize_text_field($_POST['id']);
			$role = sanitize_text_field($_POST['role']);
			$username = sanitize_text_field($_POST['username']);
			$fname = sanitize_text_field($_POST['fname']);
			$lname = sanitize_text_field($_POST['lname']);
			$email = sanitize_email($_POST['email']);
			$designation = sanitize_text_field($_POST['designation']);
			$status = sanitize_text_field($_POST['status']);
		
			//first check user registration available or not
			$userid = username_exists( $username );
			if ($userid) {
				
				// add new user - https://codex.wordpress.org/Function_Reference/wp_insert_user
				$userupdatedata = array( 
					'ID' => $userid,				// user id
					'user_login' => $username,		// username
					'first_name' => $fname,			// first name
					'last_name' => $lname,			// last name
					'user_email' => $email,			// email				
				);

				//update user details
				if($userid = wp_insert_user($userupdatedata)) {
					// also update user extra data into sm_staff table
					echo "UPDATE `$staff_table` SET `cat_id` = '$designation', `status` = '$status' WHERE `staff_id` = '$userid';";
					if($userid && $wpdb->query($wpdb->prepare("UPDATE `$staff_table` SET `cat_id` = %d, `status` = %d WHERE `staff_id` = %d", $designation, $status, $userid))) {
						echo "<div id='update-now-action-result'>Success: Staff updated.</div>";
					}
				}
			} else {
				echo "<div id='update-now-action-result'>Error: Unable to update staff.</div>";
			}
		}
	}//end update
	
	// update
	if($action == "delete") {
		$userid = sanitize_text_field($_POST['id']);
		// $reassign: Reassign posts and links to new User ID
		$reassign = 1;
		
		// delete user extra data entry from sm_staff table
		if($wpdb->query($wpdb->prepare("DELETE FROM `$staff_table` WHERE `staff_id` = %d", $userid))) {
			echo "<div id='delete-action-result'>Success: Staff deleted.</div>";
		} else {
			echo "<div id='delete-action-result'>Error: Unable to delete staff.</div>";
		}		
	}
}
?>
<?php 
wp_register_script( 'clock-in-staffs-script1', false );
wp_enqueue_script( 'clock-in-staffs-script1' );
$js = " ";
ob_start(); ?>
	// form select option js	
jQuery(document).ready(function(){		
	var value_select;
	jQuery('.get_userdata-option').first().addClass('active');
	jQuery('#role').on('change',function(){
		value_select = jQuery(this).val();		
		jQuery('.get_userdata-option').removeClass('active');
		jQuery('#'+value_select).addClass('active');		
	});	
});	

<?php
$js .= ob_get_clean();
wp_add_inline_script( 'clock-in-staffs-script1', $js ); ?>

<?php 
wp_register_style( 'clock-in-staffs-style1', false );
wp_enqueue_style( 'clock-in-staffs-style1' );
$css = " ";
ob_start(); ?>
	.get_userdata-option{
		display:none;
	}
	.get_userdata-option.active{
		display:block;
	}
<?php
$css .= ob_get_clean();
wp_add_inline_style( 'clock-in-staffs-style1', $css ); ?>						