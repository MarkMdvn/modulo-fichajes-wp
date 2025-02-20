<?php 
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

// get all records
global $wpdb;
$staff_category_table = $wpdb->prefix . "sm_staff_category";
?>
<nav class="navbar navbar-dark bg-dark main-dashboard-cip other-pages">
	<a class="navbar-brand" href="<?php echo esc_url( admin_url( 'admin.php?page=clock-in-portal' ) ); ?>"><i class="fas fa-home"></i></a>
  	<a class="navbar-brand" href="#"><?php esc_html_e('Administración de Grupos', CIP_FREE_TXTDM ); ?></a>
  	<div class="form-inline my-2 my-lg-0">
  		<button class="btn btn-outline-success my-2 my-sm-0" data-toggle="modal" data-target="#add-designation-modal"><i class="fas fa-thumbtack" aria-hidden="true"></i>&nbsp;<?php esc_html_e(' Añadir un Grupo', CIP_FREE_TXTDM );?></button>&nbsp;&nbsp;
      	<a class="navbar-brand" href="<?php echo esc_url( admin_url( 'admin.php?page=cip-settings' ) ); ?>"><i class="fas fa-cog"></i></a>
    </div>
</nav>

<div class="other-page-content">
	<table class="table table-hover table-striped">
		<thead>
			<tr class="info main_tb_head">
				<th>#</th>
				<th><?php esc_html_e('Grupo', CIP_FREE_TXTDM );?></th>
				<th><?php esc_html_e('Color', CIP_FREE_TXTDM );?></th>
				<th><?php esc_html_e('Estado', CIP_FREE_TXTDM );?></th>
				<th><?php esc_html_e('Acción', CIP_FREE_TXTDM );?></th>
			<tr>
		<thead>
		<tbody>
			<?php
				//get all staffs designation
				if(count($all_staff_categories = $wpdb->get_results("SELECT * FROM `$staff_category_table`"))){
					$no = 1;
					foreach($all_staff_categories as $staff_category) {
						$id = ucwords($staff_category->id);
						$name = ucwords($staff_category->name);
						$color = strtoupper($staff_category->color);
						if($staff_category->status) $status = "Activo"; else $status = "Inactivo";
			?>
			<tr>
				<td><?php esc_html_e( $no); ?>.</td>
				<td><?php esc_html_e( $name); ?></td>
				<td><span style="background-color: <?php esc_html_e( $color); ?>; opacity: 0.7; padding: 4px;"><?php esc_html_e( $color); ?></span></td>
				<td><?php esc_html_e( $status); ?></td>
				<td>
					<a href="#" class="btn btn-info" title="View" data-toggle="modal" data-target="#view-designation-modal" onclick="return DoAction('view', '<?php echo esc_attr( $id); ?>');"><i class="fas fa-eye" aria-hidden="true"></i></a>
					<a href="#" class="btn btn-success" title="Update" data-toggle="modal" data-target="#update-designation-modal" onclick="return DoAction('update', '<?php echo esc_attr( $id); ?>');"><i class="fas fa-edit" aria-hidden="true"></i></a>
					<?php if($no > 1) { ?>
					<a href="#" class="btn btn-danger" title="Delete" onclick="return DoAction('delete', '<?php echo esc_attr( $id); ?>');"><i class="fas fa-times" aria-hidden="true"></i></a>
					<?php } ?>
				</td>
			</tr>
			<?php
					$no++;
					}// end foreach
				} else {
					echo "<tr><td colspan=5 class='text-center'>No hay nada por aquí...</td></tr>";
				}
			?>
		</tbody>
<!--		<thead>-->
<!--			<tr class="info main_tb_head">-->
<!--				<th>#</th>-->
<!--				<th>--><?php //esc_html_e('Designation', CIP_FREE_TXTDM );?><!--</th>-->
<!--				<th>--><?php //esc_html_e('Color', CIP_FREE_TXTDM );?><!--</th>-->
<!--				<th>--><?php //esc_html_e('Status', CIP_FREE_TXTDM );?><!--</th>-->
<!--				<th>--><?php //esc_html_e('Action', CIP_FREE_TXTDM );?><!--</th>-->
<!--			<tr>-->
<!--		<thead>-->
	</table>	
</div>


<!-- Add Staff Designation Modal-->
<div class="modal fade" id="add-designation-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<form id="add-designation-form" name="add-designation-form">
				<div class="modal-header">
					<h4 class="modal-title" id="myModalLabel"><?php esc_html_e('Crear un grupo para los empleados', CIP_FREE_TXTDM );?></h4>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					
				</div>
				<div class="modal-body">
					<div class="form-group">
						<label for="Password"><?php esc_html_e('Nombre del Grupo', CIP_FREE_TXTDM );?></label>
						<input type="input" class="form-control" id="name" name="name" >
						<?php wp_nonce_field( 'add_designation_nonce_action', 'add_designation_nonce_name' ); ?>
					</div>
					<div class="form-group">
						<label for="Color"><?php esc_html_e('Color del Grupo', CIP_FREE_TXTDM );?></label>
						<input type="color" class="form-control" id="color" name="color" value="#EE2724">
					</div>
					<div class="form-group">
						<label for="Status"><?php esc_html_e('Estado', CIP_FREE_TXTDM );?></label>
						<select id="status" name="status" class="form-control">
							<optgroup label="Select Any Status">
								<option value="1"><?php esc_html_e('Activo', CIP_FREE_TXTDM );?></option>
								<option value="0"><?php esc_html_e('Inactivo', CIP_FREE_TXTDM );?></option>
							</optgroup>
						</select>
					</div>					
				</div>
				<div class="modal-footer">
					<div id="add-loading-icon" style="display:none;">
						<?php esc_html_e('Processing...', CIP_FREE_TXTDM );?><i class="fas fa-spinner fa-3x" aria-hidden="true"></i>
					</div>
					<button id="add-designation" name="add-designation" type="button" class="btn btn-success" onclick="return DoAction('add', '');"><?php esc_html_e('Crear', CIP_FREE_TXTDM );?></button>
					<button id="add-close" name="add-close" type="button" class="btn btn-danger" data-dismiss="modal"><?php esc_html_e('Cerrar', CIP_FREE_TXTDM );?></button>
				</div>
			</form>
		</div>
	</div>
</div>

<!-- View Staff Designation Modal -->
<div class="modal fade" id="view-designation-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="myModalLabel"><?php esc_html_e('View Staff Designation', CIP_FREE_TXTDM );?></h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				
			</div>
			<div class="modal-body" id="view-modal-body">
				<div id="view-loading-icon" style="display:none;">
					<?php esc_html_e('Processing...', CIP_FREE_TXTDM );?><i class="fas fa-spinner fa-3x" aria-hidden="true"></i>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal"><?php esc_html_e('Close', CIP_FREE_TXTDM );?></button>
			</div>
		</div>
	</div>
</div>

<!-- Update Staff Designation Modal -->
<div class="modal fade" id="update-designation-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<form id="update-designation-form" name="update-designation-form">
				<div class="modal-header">
					<h4 class="modal-title" id="myModalLabel"><?php esc_html_e('Editar Grupo', CIP_FREE_TXTDM );?></h4>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				</div>
				<div class="modal-body" id="update-modal-body">
				</div>
				<div class="modal-footer">
					<div id="update-loading-icon" style="display:none;">
						<?php esc_html_e('Processing...', CIP_FREE_TXTDM );?><i class="fas fa-spinner fa-3x" aria-hidden="true"></i>
					</div>
					<button id="update-designation" name="update-designation" type="button" class="btn btn-primary" onclick="return DoAction('update-now', '');"><?php esc_html_e('Actualizar', CIP_FREE_TXTDM );?></button>
					<button id="update-close" name="update-close" type="button" class="btn btn-default" data-dismiss="modal"><?php esc_html_e('Cerrar', CIP_FREE_TXTDM );?></button>
				</div>
			</form>
		</div>
	</div>
</div>

<?php 
wp_register_script( 'clock-in-designations-script', false );
wp_enqueue_script( 'clock-in-designations-script' );
$js = " ";
ob_start(); ?>

//tabs
jQuery('#myTabs a').click(function (e) {
  e.preventDefault()
  jQuery(this).tab('show');
});

// add staff modal call
jQuery('#staff-modal').on('shown.bs.modal', function (){ });

// add designation modal call
jQuery('#add-designation-modal').on('shown.bs.modal', function (){
	jQuery("#add-loading-icon").hide();
});

// action handler for: designation, action: view/add/update/delete
function DoAction(action, id){
	console.log(action);
	var data_values = "";	
	
	//add
	if(action == "add") {
		var name = jQuery("#name").val();
		if(name == "") {
			jQuery("#name").after("Required field.");
			return false;
		}
	
		jQuery("#add-designation").hide();
		jQuery("#add-close").hide();
		jQuery("#add-loading-icon").show();
		var data_values = jQuery("#add-designation-form").serialize() + "&action=" + action + "&id=" + id;
		//post data
		jQuery.ajax({
			type: "post",
			url: location.href,
			data: data_values,
			contentType: "application/x-www-form-urlencoded",
			success: function(responseData, textStatus, jqXHR) {
				var result = jQuery(responseData).find('div#action-result');
				jQuery("#add-loading-icon").hide();
				location.reload();
			},
			error: function(jqXHR, textStatus, errorThrown) {
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
	
	//update - fetch data
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
	
	//update now
	if(action == "update-now") {
		var name = jQuery("#update-designation-form #name").val();
		if(name == "") {
			jQuery("#update-designation-form #name").after("Required field.");
			return false;
		}
		jQuery("#update-designation").hide();
		jQuery("#update-close").hide();
		jQuery("#update-loading-icon").show();
		var data_values = jQuery("#update-designation-form").serialize() + "&action=" + action;
		//post data
		jQuery.ajax({
			type: "post",
			url: location.href,
			data: data_values,
			contentType: "application/x-www-form-urlencoded",
			success: function(responseData, textStatus, jqXHR) {
				var result = jQuery(responseData).find('div#upate-now-action-result');
				jQuery("#update-loading-icon").hide();
				location.reload();
			},
			error: function(jqXHR, textStatus, errorThrown) {
			}
		});
	}
	
	if(action == "delete") {
		var data_values = "action=" + action + "&id=" + id;
		if (confirm("Are you sure and want to delete this designation now?") == true) {
			//post data
			jQuery.ajax({
				type: "post",
				url: location.href,
				data: data_values,
				contentType: "application/x-www-form-urlencoded",
				success: function(responseData, textStatus, jqXHR) {
					var result = jQuery(responseData).find('div#delete-action-result');
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
wp_add_inline_script( 'clock-in-designations-script', $js ); ?>	

<?php
//Action Executor
if((isset($_POST['action']))) {
	global $wpdb;
	$action = sanitize_text_field($_POST['action']);

	//add
	if($action == "add") {
		if ( ! wp_verify_nonce( $_POST['add_designation_nonce_name'], 'add_designation_nonce_action' ) ) {
			print 'Sorry, your add designation nonce did not verify.';
			exit;
		} else {
			// nonce verified - process form data	
			$name = sanitize_text_field($_POST['name']);
			$color = sanitize_hex_color($_POST['color']);
			$status = sanitize_text_field($_POST['status']);
			if($wpdb->query($wpdb->prepare("INSERT INTO `$staff_category_table` (`id`, `name`, `color`, `status`) VALUES (NULL, %s, %s, %d)", $name, $color, $status))){
				echo "<div id='action-result'>Success: Designation added.</div>";
			} else {
				echo "<div id='action-result'>Error: Unable to add designation.</div>";
			}
		}
	}

	//view
	if($action == "view") {
		$id = sanitize_text_field($_POST['id']);
		if($designation = $wpdb->get_row($wpdb->prepare("SELECT * FROM `$staff_category_table` WHERE `id` = %d", $id))){
			$name = $designation->name;
			$color = $designation->color;
			$status = $designation->status;
			if($status) $status = "Activo"; else $status = "Inactivo";
			echo "<div id='view-action-result'>
			<table class='table'>
				<tr>
					<td><label>Grupo</label></td>
					<td>$name</td>
				</tr>
				<tr>
					<td><label>Color</label></td>
					<td><span style='background-color: $color; opacity: 0.7; padding: 4px;'>$color</span></td>
				</tr>
				<tr>
					<td><label>Estado</label></td>
					<td>$status</td>
				</tr>
			</table>
			</div>";
		} else {
			echo "<div id='action-result'>Error: Unable to fetch data.</div>";
		}
	}
	
	//update
	if($action == "update") {
		$id = sanitize_text_field($_POST['id']);
		if($designation = $wpdb->get_row($wpdb->prepare("SELECT * FROM `$staff_category_table` WHERE `id` = %d", $id))){
			$name = $designation->name;
			$color = $designation->color;
			$status = $designation->status;
			if($status) $status = "Available"; else $status = "Unavailable";
			echo "<div id='update-action-result'>
			<table class='table'>
				<tr>
					<td><label>Designation</label></td>
					<td>
						<input type='hidden' class='form-control' id='id' name='id' value='$id'>
						<input type='input' class='form-control' id='name' name='name' placeholder='Type Designation' value='$name'>
						".wp_nonce_field( 'update_designation_nonce_action', 'update_designation_nonce_name' )."
					</td>
				</tr>
				<tr>
					<td><label>Color</label></td>
					<td><input type='color' class='form-control' id='color' name='color' value='$color'></td>
				</tr>
				<tr>
					<td><label>Staus</label></td>
					<td>
						<select id='status' name='status' class='form-control'>
							<optgroup label='Select Any Status'>
								<option value='1'>Available</option>
								<option value='0'>Unavailable</option>
							</optgroup>
						</select>
					</td>
				</tr>
			</table>
			</div>";
		} else {
			echo "<div id='action-result'>Error: Unable to fetch data.</div>";
		}
	}
	
	//update-now
	if($action == "update-now") {
		if ( ! wp_verify_nonce( $_POST['update_designation_nonce_name'], 'update_designation_nonce_action' ) ) {
			print 'Sorry, your update designation nonce did not verify.';
			exit;
		} else {
			// nonce verified - process form data
			$id = sanitize_text_field($_POST['id']);
			$name = sanitize_text_field($_POST['name']);
			$color = sanitize_hex_color($_POST['color']);
			$status = sanitize_text_field($_POST['status']);
			if($wpdb->query($wpdb->prepare("UPDATE `$staff_category_table` SET `name` = %s, `color` = %s, `status` = %s WHERE `id` = %d", $name, $color, $status, $id))) {
			?><div id='update-now-action-result'><?php esc_html_e('Success: Designation updated.', CIP_FREE_TXTDM );?></div>
				<?php } else { ?><div id='action-result'><?php esc_html_e('Error: Unable to update designation.', CIP_FREE_TXTDM );?> </div>
		<?php }
		}
	}
	
	//delete
	if($action == "delete") {
		$id = sanitize_text_field($_POST['id']);
		if($wpdb->query($wpdb->prepare("DELETE FROM `$staff_category_table` WHERE `id` = %s", $id))) {
			?>
			<div id='action-result'><?php esc_html_e('Success: Designation deleted.', CIP_FREE_TXTDM );?></div>
		<?php } else { ?>
			<div id='action-result'><?php esc_html_e('Error: Unable to delete designation.', CIP_FREE_TXTDM );?></div>
		<?php }
	}
}
?>