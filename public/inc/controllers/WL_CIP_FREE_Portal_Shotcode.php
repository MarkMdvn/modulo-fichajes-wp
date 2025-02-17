<?php
defined( 'ABSPATH' ) or die();

$cip_settings = get_option('cip_settings');

if(isset($cip_settings['clock_in_btn_text'])) $clock_in_btn_text = $cip_settings['clock_in_btn_text']; else $clock_in_btn_text = "Office In";
if(isset($cip_settings['clock_out_btn_text'])) $clock_out_btn_text = $cip_settings['clock_out_btn_text']; else $clock_out_btn_text = "Office Out";
if(isset($cip_settings['lunch_in_btn_text'])) $lunch_in_btn_text = $cip_settings['lunch_in_btn_text']; else $lunch_in_btn_text = "Lunch In";
if(isset($cip_settings['lunch_out_btn_text'])) $lunch_out_btn_text = $cip_settings['lunch_out_btn_text']; else $lunch_out_btn_text = "Lunch Out";

if($cip_settings['cip_timezone'] == null){
echo "<h1>Please Select time zone in settings</h1>";
}else {date_default_timezone_set( $cip_settings['cip_timezone'] );
}

$current_time   = date( "H:i:s" );
if ( $current_time < '12:00:00' ) {
	$greetings = esc_html__('Good Morning', CIP_FREE_TXTDM );
}
if ( $current_time > '12:00:00' && $current_time < '17:00:00') {
	$greetings = esc_html__('Good Afternoon ', CIP_FREE_TXTDM );
}
if ( $current_time > '17:00:00' && $current_time < '21:00:00') {
	$greetings = esc_html__('Good Evening ', CIP_FREE_TXTDM );
}
if ( $current_time > '21:00:00' && $current_time < '04:00:00') {
	$greetings = esc_html__('Good Night ', CIP_FREE_TXTDM );
}

?>
<div class="cip_front_portal">

	<!-- Clock -->
	<div class="cip_clock" id="cip_clock">
		<h1 class="title text-center"><?php esc_html_e('Clock In Portal ', CIP_FREE_TXTDM ); ?></h1>
		<h3 class="title text-center"><?php esc_html_e( $greetings); ?></h3>
		<?php if ( is_user_logged_in() ) { ?>
			<div class="col-md-12 clock-flap">
				<div class="clock">
					<div class="digit tenhour">
						<span class="base"></span>
						<div class="flap over front"></div>
						<div class="flap over back"></div>
						<div class="flap under"></div>
					</div>

					<div class="digit hour">
						<span class="base"></span>
						<div class="flap over front"></div>
						<div class="flap over back"></div>
						<div class="flap under"></div>
					</div>
					
					<div class="digit tenmin">
						<span class="base"></span>
						<div class="flap over front"></div>
						<div class="flap over back"></div>
						<div class="flap under"></div>
					</div>

					<div class="digit min">
						<span class="base"></span>
						<div class="flap over front"></div>
						<div class="flap over back"></div>
						<div class="flap under"></div>
					</div>
					
					<div class="digit tensec">
						<span class="base"></span>
						<div class="flap over front"></div>
						<div class="flap over back"></div>
						<div class="flap under"></div>
					</div>

					<div class="digit sec">
						<span class="base"></span>
						<div class="flap over front"></div>
						<div class="flap over back"></div>
						<div class="flap under"></div>
					</div>
					<div class="digit ampm">
						<span class="base"></span>
						<div class="flap over front"></div>
						<div class="flap over back"></div>
						<div class="flap under"></div>
					</div>
					<div class="digit ampmm">
						<span class="base"></span>
						<div class="flap over front"></div>
						<div class="flap over back"></div>
						<div class="flap under"></div>
					</div>
				</div>
			</div>
		<?php } ?>
		<div class="col-md-12 portal_form">
			<?php if ( is_user_logged_in() ) { 
				global $wpdb;
				$staff_attendance_table = $wpdb->base_prefix . "sm_attendance";
			    $staff = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM `$staff_attendance_table` WHERE `staff_id` = %d AND `date` = %s", get_current_user_id(), date('Y-m-d') ) );
			?>
				<div class="staff_details">
					<?php if ( ! empty ( $staff ) && $staff->office_in != '00:00:00' ) { ?>
						<p>Your Login In time <span class="staff_detail_p"><?php echo date( 'h:i: a', strtotime( $staff->office_in ) ); ?></span></p>
					<?php } else { ?>
						<p class="text-center">Click the below button to strat your session.!</p>
					<?php } if ( ! empty ( $staff ) && $staff->office_out != '00:00:00' ) { ?>
				    	<p>Your Login Out time <span class="staff_detail_p"><?php echo date( 'h:i: a', strtotime( $staff->office_out ) ); ?></span></p>
				    <?php } if ( ! empty ( $staff ) && $staff->lunch_in != '00:00:00' ) { ?>
				    	<p>Your Lunch In time <span class="staff_detail_p"><?php echo date( 'h:i: a', strtotime( $staff->lunch_in ) ); ?></span></p>
				    <?php } if ( ! empty ( $staff ) && $staff->lunch_out != '00:00:00' ) { ?>
				    	<p>Your Lunch Out time <span class="staff_detail_p"><?php echo date( 'h:i: a', strtotime( $staff->lunch_out ) ); ?></span></p>
				    <?php } ?>
				</div>
				<form id="cip_free_form">
					<div class="form-group row">
						<?php   
						$user       = get_userdata( get_current_user_id() );
						$user_login = $user->user_login; 
						?>
		                <input type="hidden" class="form-control" id="cip_user" name="cip_user" placeholder="Username" value="<?php echo esc_attr($user_login); ?>">
		                <input type="hidden" class="form-control" id="cip_id" name="cip_id" placeholder="user_id" value="<?php echo esc_attr(get_current_user_id()); ?>">
		            </div>

		    <?php if ( empty ( $staff ) ) { ?>

		            <p class="text-center"><button type="button" id="office-in-btn" data-value="office-in" name="office-in-btn" class="btn peach-gradient btn-mg cip_front_btn" ><?php esc_html_e( $clock_in_btn_text, CIP_FREE_TXTDM);?></button></p>

		    <?php } if ( ! empty ( $staff ) && ! empty ( $staff->office_in ) && $staff->office_out == '00:00:00' ) { ?>
					<button type="button" id="office-out-btn" data-value="office-out" name="office-out-btn" class="btn purple-gradient btn-mg cip_front_btn"><?php esc_html_e( $clock_out_btn_text, CIP_FREE_TXTDM);?></button>
			<?php } if ( ! empty ( $staff ) && $staff->lunch_in == '00:00:00' && $staff->office_out == '00:00:00' ) { ?>
					<button type="button" id="lunch-in-btn" data-value="lunch-in" name="lunch-in-btn" class="btn peach-gradient btn-mg cip_front_btn"><?php esc_html_e( $lunch_in_btn_text, CIP_FREE_TXTDM);?></button>
			<?php } if ( ! empty ( $staff ) && ! empty ( $staff->lunch_in ) && $staff->lunch_out == '00:00:00' && $staff->office_out == '00:00:00' ) { ?>
					<button type="button" id="lunch-out-btn" data-value="lunch-out" name="lunch-out-btn" class="btn purple-gradient btn-mg cip_front_btn"><?php esc_html_e( $lunch_out_btn_text, CIP_FREE_TXTDM);?></button>
			<?php } if ( ! empty ( $staff ) && $staff->break_in == '00:00:00' && $staff->office_out == '00:00:00' ) { ?>
					<button type="button" id="break-in-btn" data-value="break-in" name="break-in-btn" class="btn peach-gradient btn-mg cip_front_btn"><?php esc_html_e("Break In",CIP_FREE_TXTDM);?></button>
			<?php } if ( ! empty ( $staff ) && ! empty ( $staff->break_in ) &&  $staff->break_out == '00:00:00' && $staff->office_out == '00:00:00' ) { ?>
					<button type="button" id="break-out-btn" data-value="break-out" name="break-out-btn" class="btn purple-gradient btn-mg cip_front_btn"><?php esc_html_e("Break Out",CIP_FREE_TXTDM);?></button>

				</form>
			<?php } } else { 

				$link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 
                "https" : "http") . "://" . $_SERVER['HTTP_HOST'] .  
                $_SERVER['REQUEST_URI']; 

				$args = array(
                        'echo'           => true,
                        'redirect'       => $link, 
                        'form_id'        => 'loginform',
                        'label_username' => '',
                        'label_password' => '',
                        'label_remember' => __( 'Remember Me' ),
                        'label_log_in'   => __( 'Login' ),
                        'id_username'    => 'user_login',
                        'id_password'    => 'user_pass',
                        'id_remember'    => 'rememberme',
                        'id_submit'      => 'wp-submit',
                        'remember'       => true,
                        'value_username' => NULL,
                        'value_remember' => true
                    ); 
                    
                    // Calling the login form.
                    wp_login_form( $args );
				 } ?>
		</div>
	</div>
	<!-- End -->
</div>

