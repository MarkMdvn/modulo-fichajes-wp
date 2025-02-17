<?php
$date_format  = get_option('date_format');
$time_format  = get_option('time_format');
$cip_settings = get_option('cip_settings');

//print_r($cip_settings);
if(isset($cip_settings['staff_show_holidays'])) $staff_show_holidays = $cip_settings['staff_show_holidays']; else $staff_show_holidays = "yes";
if(isset($cip_settings['shortcode_enable'])) $shortcode_enable = $cip_settings['shortcode_enable']; else $shortcode_enable = "no";
if(isset($cip_settings['stafff_report_submission'])) $stafff_report_submission = $cip_settings['stafff_report_submission']; else $stafff_report_submission = "yes";
if(empty($cip_settings['have_woo'])) $cip_settings['have_woo'] = "no";
if(isset($cip_settings['clock_in_btn_text'])) $clock_in_btn_text = $cip_settings['clock_in_btn_text']; else $clock_in_btn_text = "Office In";
if(isset($cip_settings['clock_out_btn_text'])) $clock_out_btn_text = $cip_settings['clock_out_btn_text']; else $clock_out_btn_text = "Office Out";
if(isset($cip_settings['clock_in_alert_text'])) $clock_in_alert_text = $cip_settings['clock_in_alert_text']; else $clock_in_alert_text = "Are you sure want to start your office working session now?";
if(isset($cip_settings['clock_out_alert_text'])) $clock_out_alert_text = $cip_settings['clock_out_alert_text']; else $clock_out_alert_text = "You are going to office out at";
if(isset($cip_settings['clock_out_alert_text2'])) $clock_out_alert_text2 = $cip_settings['clock_out_alert_text2']; else $clock_out_alert_text2 = "Are you sure and want to office out now?";

if(isset($cip_settings['lunch_in_btn_text'])) $lunch_in_btn_text = $cip_settings['lunch_in_btn_text']; else $lunch_in_btn_text = "Lunch In";
if(isset($cip_settings['lunch_out_btn_text'])) $lunch_out_btn_text = $cip_settings['lunch_out_btn_text']; else $lunch_out_btn_text = "Lunch Out";
if(isset($cip_settings['lunch_in_alert_text'])) $lunch_in_alert_text = $cip_settings['lunch_in_alert_text']; else $lunch_in_alert_text = "Are you sure want to start your lunch session now?";
if(isset($cip_settings['lunch_out_alert_text'])) $lunch_out_alert_text = $cip_settings['lunch_out_alert_text']; else $lunch_out_alert_text = "You are going to lunch out at";
if(isset($cip_settings['lunch_out_alert_text2'])) $lunch_out_alert_text2 = $cip_settings['lunch_out_alert_text2']; else $lunch_out_alert_text2 = "Are you sure and want to lunch out now?";

//Timezone array
/*$timezones = DateTimeZone::listAbbreviations(DateTimeZone::ALL); */
$timezones = DateTimeZone::listAbbreviations(); 
$tzlist    = DateTimeZone::listIdentifiers(DateTimeZone::ALL);
$cities1   = array();
$cities2   = array();
$cities3   = array();
foreach( $timezones as $key => $zones )
{
    foreach( $zones as $id => $zone )
    {  
        array_push($cities1,$zone["timezone_id"]); 
    }
} 
foreach(timezone_abbreviations_list() as $abbr => $timezone){
    foreach($timezone as $val){
        if(isset($val['timezone_id'])){ 
            array_push($cities2,$val['timezone_id']);
        }
    }
} 
foreach($tzlist as  $timezone){
    if(isset($timezone)){
        array_push($cities3,$timezone);
    }
} 
$ALL_timezone    = array_merge($cities1,$cities2,$cities3);
$result_timezone = array_unique($ALL_timezone); 
sort($result_timezone);
?>

<?php 
wp_register_style( 'clock-in-settings-style', false );
wp_enqueue_style( 'clock-in-settings-style' );
$css = " ";
ob_start(); ?>
	input[text], input[button], textarea {
		width: 100% !important;
	}
<?php
$css .= ob_get_clean();
wp_add_inline_style( 'clock-in-settings-style', $css ); ?>	

<nav class="navbar navbar-dark bg-dark main-dashboard-cip other-pages">
	<a class="navbar-brand" href="<?php echo esc_url( admin_url( 'admin.php?page=clock-in-portal' ) ); ?>"><i class="fas fa-home"></i></a>
  	<a class="navbar-brand" href="#"><?php esc_html_e('General Settings', CIP_FREE_TXTDM ); ?></a>
  	<div class="form-inline my-2 my-lg-0">
      	<a class="navbar-brand" href="<?php echo esc_url( admin_url( 'admin.php?page=cip-settings' ) ); ?>"><i class="fas fa-cog"></i></a>
    </div>
</nav>
<div class="alert">
	<!-- Tab Nav -->
	<ul class="nav nav-tabs" role="tablist">
		<li class="nav-item" role="presentation" ><a  class="nav-link active" href="#general" aria-controls="general" role="tab" data-toggle="tab"><strong><?php esc_html_e('General Settings', CIP_FREE_TXTDM );?></strong></a></li>
		<li class="nav-item" role="presentation"><a class="nav-link" href="#staff" aria-controls="staff" role="tab" data-toggle="tab"><strong><?php esc_html_e('Staff', CIP_FREE_TXTDM );?></strong></a></li>
		<li class="nav-item" role="presentation"><a class="nav-link" href="#message" aria-controls="message" role="tab" data-toggle="tab"><strong><?php esc_html_e('Message & Text', CIP_FREE_TXTDM );?></strong></a></li>
	</ul>

	<form id="cip-save-setting" name="cip-save-setting" method="post">
		<!-- Tab Panes -->
		<div class="tab-content">
			<div role="tabpanel" class="tab-pane active" id="general">
				<h3><?php esc_html_e('General Settings', CIP_FREE_TXTDM );?></h3>
				<div class="table-responsive col-md-6 col-sm-12">
					<table class="table">
					<tbody>
						 <tr>
							<td><?php esc_html_e('TimeZone', CIP_FREE_TXTDM ); ?></td>
							<td>
								<select class="form-control cip_timezone" name="cip_timezone" id="cip_timezone">
								<?php 
								$staff_timezone = array_key_exists('cip_timezone', $cip_settings) ?  $cip_settings['cip_timezone'] : "";
								/*$staff_timezone = $cip_settings['cip_timezone'];*/
								$wordpress_timezone = wp_timezone_string();
								foreach($result_timezone as $timezone){
					                ?><option value="<?php echo esc_attr($timezone); ?>" <?php if($wordpress_timezone == $timezone) echo esc_attr( "selected=selected"); ?>><?php echo esc_attr($timezone); ?></option> <?php 
					                } 
						        ?>
								</select>
							</td>
						</tr> 
						<tr>
							<td><?php esc_html_e('Date Format', CIP_FREE_TXTDM );?></td>
							<td><?php echo esc_attr($date_format); ?> (<?php echo esc_attr(date($date_format)); ?>) | <a href="options-general.php" target="_blank"><?php esc_html_e('change format', CIP_FREE_TXTDM );?></a></td>
						</tr>``
						<tr>
							<td><?php esc_html_e('Time Format', CIP_FREE_TXTDM );?></td>
							<td><?php echo esc_attr($time_format); ?> (<?php echo esc_attr(date($time_format)); ?>) | <a href="options-general.php" target="_blank"><?php esc_html_e('change format', CIP_FREE_TXTDM );?></a></td>
						</tr>
						<tr>
							<td class="setting_label"><?php esc_html_e('Select yes if you have "Woocommerce" installed', CIP_FREE_TXTDM );?></td>
							<td>
								<input type="radio" name="have_woo" value="yes" <?php if($cip_settings['have_woo']=='yes'){ echo esc_attr( 'checked'); } ?>> <?php esc_html_e('Yes', CIP_FREE_TXTDM );?> &nbsp;&nbsp;
 								<input type="radio" name="have_woo" value="no" <?php if($cip_settings['have_woo']=='no'){ echo esc_attr( 'checked'); } ?>> <?php esc_html_e('No', CIP_FREE_TXTDM );?>
							</td>
						</tr>
						<tr>
							<td class="setting_label"><?php esc_html_e('Select "YES" if you are using shortcode', CIP_FREE_TXTDM );?></td>
							<td>
								<input type="radio" name="shortcode_enable" value="yes" <?php if(isset($shortcode_enable) && $shortcode_enable=='yes'){ echo esc_attr( 'checked'); } ?> > <?php esc_html_e('Yes', CIP_FREE_TXTDM );?> &nbsp;&nbsp;
 								<input type="radio" name="shortcode_enable" value="no" <?php if( isset($shortcode_enable) && $shortcode_enable=='no'){ echo esc_attr( 'checked'); } ?> > <?php esc_html_e('No', CIP_FREE_TXTDM );?>
							</td>
						</tr>
					</tbody>
					</table>
				</div>
			</div>

			<div role="tabpanel" class="tab-pane" id="staff">
				<h3><?php esc_html_e('Staff Dashboard Settings', CIP_FREE_TXTDM );?></h3>
				<div class="table-responsive col-md-6 col-sm-12">
				<table class="table">
					<tbody>
						<tr>
							<td><?php esc_html_e('Show Holidays', CIP_FREE_TXTDM );?></td>
							<td>
							<input type="radio" id="staff_show_holidays" name="staff_show_holidays" <?php if($staff_show_holidays == "yes") echo esc_attr( "checked=checked"); ?> value="yes"> <?php esc_html_e('Yes', CIP_FREE_TXTDM );?> &nbsp;&nbsp;
							<input type="radio" id="staff_show_holidays" name="staff_show_holidays" <?php if($staff_show_holidays == "no") echo esc_attr( "checked=checked"); ?> value="no"> <?php esc_html_e('No', CIP_FREE_TXTDM );?>
							</td>
						</tr>
						<tr>
							<td><?php esc_html_e('Enable Report Submission', CIP_FREE_TXTDM );?></td>
							<td>
							<input type="radio" id="stafff_report_submission" name="stafff_report_submission" <?php if($stafff_report_submission == "yes") echo esc_attr( "checked=checked"); ?> value="yes"> <?php esc_html_e('Yes', CIP_FREE_TXTDM );?> &nbsp;&nbsp;
							<input type="radio" id="stafff_report_submission" name="stafff_report_submission" <?php if($stafff_report_submission == "no") echo esc_attr( "checked=checked"); ?> value="no"> <?php esc_html_e('No', CIP_FREE_TXTDM );?>
							</td>
						</tr>
					</tbody>
					</table>
				</div>
			</div>
		
			<div role="tabpanel" class="tab-pane" id="message">
				<h3><?php esc_html_e('Message & Text Settings', CIP_FREE_TXTDM );?></h3>
				<div class="table-responsive col-md-6 col-sm-12">
				<table class="table">
					<tbody>
						<tr>
							<td><?php esc_html_e('Clock In Button Text', CIP_FREE_TXTDM );?></td>
							<td><input type="text" id="clock_in_btn_text" name="clock_in_btn_text" value="<?php echo esc_attr( $clock_in_btn_text); ?>"></td>
						</tr>
						<tr>
							<td><?php esc_html_e('Clock Out Button Text', CIP_FREE_TXTDM );?></td>
							<td><input type="text" id="clock_out_btn_text" name="clock_out_btn_text" value="<?php echo esc_attr($clock_out_btn_text); ?>"></td>
						</tr>
						
						<tr>
							<td><?php esc_html_e('Lunch In Button Text', CIP_FREE_TXTDM );?></td>
							<td><input type="text" id="lunch_in_btn_text" name="lunch_in_btn_text" value="<?php echo esc_attr($lunch_in_btn_text); ?>"></td>
						</tr>
						<tr>
							<td><?php esc_html_e('Lunch Out Button Text', CIP_FREE_TXTDM );?></td>
							<td><input type="text" id="lunch_out_btn_text" name="lunch_out_btn_text" value="<?php echo esc_attr($lunch_out_btn_text); ?>"></td>
						</tr>
						<tr>
							<td><?php esc_html_e('Clock In Alert Message', CIP_FREE_TXTDM );?></td>
							<td><textarea id="clock_in_alert_text" name="clock_in_alert_text"><?php echo esc_attr($clock_in_alert_text); ?></textarea></td>
						</tr>
						<tr>
							<td><?php esc_html_e('Clock Out Alert Message 1', CIP_FREE_TXTDM );?></td>
							<td><textarea id="clock_out_alert_text" name="clock_out_alert_text"><?php echo esc_attr($clock_out_alert_text); ?></textarea></td>
						</tr>
						<tr>
							<td><?php esc_html_e('Clock Out Alert Message 2', CIP_FREE_TXTDM );?></td>
							<td><textarea id="clock_out_alert_text2" name="clock_out_alert_text2"><?php echo esc_attr($clock_out_alert_text2); ?></textarea></td>
						</tr>
						<tr>
							<td><?php esc_html_e('Lunch In Alert Message', CIP_FREE_TXTDM );?></td>
							<td><textarea id="lunch_in_alert_text" name="lunch_in_alert_text"><?php echo esc_attr($lunch_in_alert_text); ?></textarea></td>
						</tr>
						<tr>
							<td><?php esc_html_e('Lunch Out Alert Message 1', CIP_FREE_TXTDM );?></td>
							<td><textarea id="lunch_out_alert_text" name="lunch_out_alert_text"><?php echo esc_attr($lunch_out_alert_text); ?></textarea></td>
						</tr>
						<tr>
							<td><?php esc_html_e('Lunch Out Alert Message 2', CIP_FREE_TXTDM );?></td>
							<td><textarea id="lunch_out_alert_text2" name="lunch_out_alert_text2"><?php echo esc_attr($lunch_out_alert_text2); ?></textarea></td>
						</tr>						
					</tbody>
					</table>
				</div>
			</div>
		</div>
		
		<div class="table-responsive col-md-12 col-sm-12">
			<tr>
				<td>&nbsp;</td>
				<td>
					<input type="hidden" id="save-setting" name="save-setting" value="cip-save-setting">
					<input type="button" id="save" name="save" class="btn btn-success btn-lg" onclick="return SaveSetting();" value="Save Settings" style="text-align:right;">
					<div id="setting-save-loading-icon" style="display:none; text-align:center;">
						<?php esc_html_e('Processing...', CIP_FREE_TXTDM );?><i class="fas fa-spinner fa-3x" aria-hidden="true"></i>
					</div>
				</td>
			</tr>
		</div>
	</form>
</div>
<?php 
wp_register_script( 'clock-in-settings-script', false );
wp_enqueue_script( 'clock-in-settings-script' );
$js = " ";
ob_start(); ?>
function SaveSetting(){
	jQuery("#setting-save-loading-icon").show();
	var data_values = jQuery("#cip-save-setting").serialize();
	//post data
	jQuery.ajax({
		type: "post",
		url: location.href,
		data: data_values,
		contentType: "application/x-www-form-urlencoded",
		success: function(responseData, textStatus, jqXHR) {
			var result = jQuery(responseData).find('div#action-result');
			jQuery("#setting-save-loading-icon").hide();
		},
		error: function(jqXHR, textStatus, errorThrown) {
		}
	});
}
jQuery('#myTabs a').click(function (e) {
  e.preventDefault();
  jQuery(this).tab('show');
});

<?php
$js .= ob_get_clean();
wp_add_inline_script( 'clock-in-settings-script', $js ); ?>

<?php
// save settings
if(isset($_POST['save-setting'])) {
	update_option('cip_settings', $_POST);
}
?>