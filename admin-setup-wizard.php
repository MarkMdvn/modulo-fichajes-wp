<?php
/**
 * Setup Wizard Class
 *
 * Takes new users through some basic steps to setup their store.
 *
 * @package clock-in-portal
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * CIP_Admin_Setup_Wizard_free class.
 */
class CIP_Admin_Setup_Wizard_free {

    /**
	 * Current step
	 *
	 * @var string
	 */
	private $step = '';

	/**
	 * Steps for the setup wizard
	 *
	 * @var array
	 */
    private $steps = array();

    /**
	 * Hook in tabs.
	 */
	public function __construct() {
        add_action( 'admin_menu', array( $this, 'admin_menus' ) );
        add_action( 'admin_init', array( $this, 'setup_wizard' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'cip_setup_setup_footer', array( $this, 'add_footer_scripts' ) );
    }

    /**
	 * Add admin menus/screens.
	 */
	public function admin_menus() {
		add_dashboard_page( '', '', 'manage_options', 'cip-setup-wizard-free', '' );
	}

	/**
	 * Add footer scripts to OBW via woocommerce_setup_footer
	 */
	public function add_footer_scripts() {
		wp_print_scripts();
		// WC_Site_Tracking::add_tracking_function();
	}

    /**
	 * Register/enqueue scripts and styles for the Setup Wizard.
	 *
	 * Hooked onto 'admin_enqueue_scripts'.
	 */
	public function enqueue_scripts() {

		if(strpos($_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'], 'cip-setup-wizard-free') == true) {
		wp_enqueue_style( 'bootstrap', CIP_PLG_URL . '/css/bootstrap.min.css' );
		wp_enqueue_style( 'timepicker', CIP_PLG_URL . '/new-assets/css/bootstrap-timepicker.css' );
		wp_enqueue_style( 'font-awesome', CIP_PLG_URL . '/css/all.css' );
		wp_enqueue_style( 'weblizar-setup-css', CIP_PLG_URL . '/css/admin-setup.css');

		/* Add the color picker css file */
		wp_enqueue_style( 'wp-color-picker' );

		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'wp-color-picker' );
		wp_enqueue_script( 'popper', CIP_PLG_URL . '/js/popper.min.js', array( 'jquery' ), true, true );
		wp_enqueue_script( 'bootstrap-js', CIP_PLG_URL . '/js/bootstrap.min.js', array( 'jquery' ), true, true );
		wp_enqueue_script( 'timepicker-js', CIP_PLG_URL . '/new-assets/js/bootstrap-timepicker.js', array( 'jquery' ), true, true );
        wp_enqueue_script( 'weblizar-setup-js', CIP_PLG_URL . '/js/admin-setup.js', array( 'jquery' ), true, true );
    }
}

    /**
	 * Show the setup wizard.
	 */
	public function setup_wizard() {
		if ( empty( $_GET['page'] ) || 'cip-setup-wizard-free' !== $_GET['page'] ) {
			return;
		}
		$default_steps = array(
			'designation'     => array(
				'name'    => __( 'Create Designation', CIP_FREE_TXTDM ),
				'view'    => array( $this, 'cip_setup_desig' ),
				'handler' => array( $this, 'cip_setup_desig_save' ),
			),
			'settings'    => array(
				'name'    => __( 'Configure Settings', CIP_FREE_TXTDM ),
				'view'    => array( $this, 'cip_setup_settings' ),
				'handler' => array( $this, 'cip_setup_settings_save' ),
			),
			'next_steps'  => array(
				'name'    => __( 'Ready!', CIP_FREE_TXTDM ),
				'view'    => array( $this, 'cip_setup_ready' ),
				'handler' => '',
			),
		);

		$this->steps = apply_filters( 'cip_setup_wizard_steps', $default_steps );
		$this->step  = isset( $_GET['step'] ) ? sanitize_key( $_GET['step'] ) : current( array_keys( $this->steps ) );

		// @codingStandardsIgnoreStart
		if ( ! empty( $_POST['save_step'] ) && isset( $this->steps[ $this->step ]['handler'] ) ) {
			call_user_func( $this->steps[ $this->step ]['handler'], $this );
		}
		// @codingStandardsIgnoreEnd

		ob_start();
		$this->setup_wizard_header();
		$this->setup_wizard_steps();
		$this->setup_wizard_content();
		$this->setup_wizard_footer();
		exit;
	}

	/** Next step function **/
	public function get_next_step_link( $step = '' ) {
		if ( ! $step ) {
			$step = $this->step;
		}

		$keys = array_keys( $this->steps );
		if ( end( $keys ) === $step ) {
			return admin_url();
		}

		$step_index = array_search( $step, $keys, true );
		if ( false === $step_index ) {
			return '';
		}

		return add_query_arg( 'step', $keys[ $step_index + 1 ], remove_query_arg( 'activate_error' ) );
	}

    /**
	 * Setup Wizard Header.
	 */
	public function setup_wizard_header() {
		set_current_screen();
		?>
		<!DOCTYPE html>
		<html <?php language_attributes(); ?>>
		<head>
			<meta name="viewport" content="width=device-width" />
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
			<title><?php esc_html_e( 'Clock in pro &rsaquo; Setup Wizard', CIP_FREE_TXTDM ); ?></title>
			<?php do_action( 'admin_enqueue_scripts' ); ?>
			<?php wp_print_scripts( 'cip-setup-wizard-free' ); ?>
			<?php do_action( 'admin_print_styles' ); ?>
			<?php do_action( 'admin_head' ); ?>
		</head>
		<body class="cip-setup-wizard wp-core-ui wl_custom">
			<div class="logo">
				<img style="width: 55%;height: auto;margin-bottom: 2%;" src="<?php echo CIP_PLG_URL; ?>new-assets/images/logo.png" alt="logo">
			</div>
		<?php
    }

    /**
	 * Output the steps.
	 */
	public function setup_wizard_steps() {
		$output_steps = $this->steps;
		?>
		<ol class="cip-setup-steps">
			<?php
			foreach ( $output_steps as $step_key => $step ) {
				$is_completed = array_search( $this->step, array_keys( $this->steps ), true ) > array_search( $step_key, array_keys( $this->steps ), true );

				if ( $step_key === $this->step ) {
					?>
					<li class="active"><?php esc_html_e( $step['name'] ); ?></li>
					<?php
				} elseif ( $is_completed ) {
					?>
					<li class="done">
						<a href="<?php echo esc_url( add_query_arg( 'step', $step_key, remove_query_arg( 'activate_error' ) ) ); ?>"><?php esc_html_e( $step['name'] ); ?></a>
					</li>
					<?php
				} else {
					?>
					<li><?php esc_html_e( $step['name'] ); ?></li>
					<?php
				}
			}
			?>
		</ol>
		<?php
	}

    /**
	 * Setup Wizard Footer.
	 */
	public function setup_wizard_footer() { ?>
		<a class="cip-setup-footer-links" href="<?php echo esc_url( admin_url() ); ?>"><?php esc_html_e( 'Not right now', CIP_FREE_TXTDM ); ?></a>

			<?php do_action( 'cip_setup_setup_footer' ); ?>
			</body>
		</html>
		<?php
	}

	/**
	 * Output the content for the current step.
	 */
	public function setup_wizard_content() {
		echo '<div class="cip-setup-content">';
		if ( ! empty( $this->steps[ $this->step ]['view'] ) ) {
			call_user_func( $this->steps[ $this->step ]['view'], $this );
		}
		echo '</div>';
	}

	/** Designation step **/
	public function cip_setup_desig() {
		?>
		<form method="post" class="designation-step">
			<p class="store-setup"><?php esc_html_e( 'The following wizard will help you configure your Clock in Portal and get you started quickly.', CIP_FREE_TXTDM ); ?></p>

			<div class="form-body">
				<label for="desig_name" class="location-prompt"><?php esc_html_e( 'Designation Name', CIP_FREE_TXTDM ); ?></label>
				<input type="text" id="desig_name" class="location-input" name="desig_name" required value="" />
			</div>
			<div class="form-body">
				<label for="desig_color" class="location-prompt"><?php esc_html_e( 'Designation color', CIP_FREE_TXTDM ); ?></label>
				<input type="text" id="desig_color" class="location-input color-field" name="desig_color" value="" />
			</div>
			<p class="cip-setup-actions step">
				<button type="submit" class="button-primary button button-large button-next" value="<?php echo esc_attr( "Next", CIP_FREE_TXTDM ); ?>" name="save_step"><?php esc_html_e( "Next", CIP_FREE_TXTDM ); ?></button>
			</p>
			<?php wp_nonce_field( 'cip-setup-wizard-free' ); ?>
		</form>
		<?php
	}

	public function cip_setup_desig_save() {
		check_admin_referer( 'cip-setup-wizard-free' );

		$desig_name  = isset( $_POST['desig_name'] ) ? sanitize_text_field( $_POST['desig_name'] ) : '';
		$desig_color = isset( $_POST['desig_color'] ) ? sanitize_hex_color( $_POST['desig_color'] ) : '';

		global $wpdb;
		$table  = $wpdb->prefix.'sm_staff_category';
		$data   = array(
					'id'     => NULL,
					'name'   => $desig_name,
					'color'  => $desig_color,
					'status' => 1,
				);
		$format = array( NULL, '%s', '%s', '%d' );
		$wpdb->insert( $table,$data,$format );

		wp_safe_redirect( esc_url_raw( $this->get_next_step_link() ) );
		exit;
	}

	/** Settings step **/
	public function cip_setup_settings() {
		$cip_settings = get_option('cip_settings');

		//Timezone array
		$timezones = DateTimeZone::listAbbreviations();
		$tzlist    = DateTimeZone::listIdentifiers();
		$cities1   = array();
		$cities2   = array();
		$cities3   = array();

		foreach( $timezones as $key => $zones ) {
			foreach( $zones as $id => $zone )
			{
				array_push($cities1,$zone["timezone_id"]);
			}
		}

		foreach(timezone_abbreviations_list() as $abbr => $timezone) {
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
		$ALL_timezone = array_merge($cities1,$cities2,$cities3);
		$result_timezone = array_unique($ALL_timezone);
		sort($result_timezone);

        if(isset($cip_settings['have_woo'])) $have_woo = $cip_settings['have_woo']; else $have_woo = "no";
        if(isset($cip_settings['shortcode_enable'])) $shortcode_enable = $cip_settings['shortcode_enable']; else $shortcode_enable = "no";

		?>
		<form method="post" class="settings-step">
			<p class="store-setup"><?php esc_html_e( 'The following wizard will help you configure your Clock in Portal and get you started quickly.', CIP_FREE_TXTDM ); ?></p>

			<div class="form-body">

				<div class="form-group">
					<label for="desig_name" class="location-prompt"><?php esc_html_e( 'Select TimeZone', CIP_FREE_TXTDM ); ?></label>
					<select class="cip_timezone" name="cip_timezone" id="cip_timezone" required>
						<?php $staff_timezone = isset($cip_settings['cip_timezone']) ? $cip_settings['cip_timezone'] : null;
							foreach( $result_timezone as $timezone ) { ?>
								<option value="<?php echo esc_attr( $timezone); ?>" <?php if($staff_timezone == $timezone) echo esc_attr("selected=selected"); ?>><?php esc_html_e( $timezone); ?></option> <?php
							}
						?>
					</select>
				</div>

				<div class="form-group">
					<label for="have_woo" class="location-prompt"><?php esc_html_e( 'Select yes if you have "Woocommerce" installed', CIP_FREE_TXTDM ); ?></label>
					<br>
					<input type="radio" name="have_woo" value="yes" <?php if( $have_woo =='yes'){ echo esc_attr('checked'); } ?>><?php esc_html_e ( ' Yes', CIP_FREE_TXTDM ); ?> &nbsp;&nbsp;
 					<input type="radio" name="have_woo" value="no" <?php if( $have_woo =='no'){ echo esc_attr('checked'); } ?>> <?php esc_html_e ( 'No', CIP_FREE_TXTDM ); ?>
				</div>

				<div class="form-group">
					<label for="shortcode_enable" class="location-prompt"><?php esc_html_e( 'Select "YES" if you are using shortcode', CIP_FREE_TXTDM ); ?></label>
					<br>
					<input type="radio" name="shortcode_enable" value="yes" <?php if($shortcode_enable=='yes'){ echo esc_attr('checked'); } ?>> <?php esc_html_e ( 'Yes', CIP_FREE_TXTDM ); ?> &nbsp;&nbsp;
 					<input type="radio" name="shortcode_enable" value="no" <?php if($shortcode_enable=='no'){ echo esc_attr('checked'); } ?>> <?php esc_html_e ( 'No', CIP_FREE_TXTDM ); ?>
				</div>

			</div>
			<p class="cip-setup-actions step">
				<button type="submit" class="button-primary button button-large button-next" value="<?php echo esc_attr( "Next", CIP_FREE_TXTDM ); ?>" name="save_step"><?php esc_html_e( "Next", CIP_FREE_TXTDM ); ?></button>
			</p>
			<?php wp_nonce_field( 'cip-setup-wizard-free' ); ?>
		</form>
		<?php
	}

	public function cip_setup_settings_save() {
		check_admin_referer( 'cip-setup-wizard-free' );
		update_option( 'cip_settings', $_POST );
		wp_safe_redirect( esc_url_raw( $this->get_next_step_link() ) );
		exit;

	}

	/** Final step **/
	public function cip_setup_ready() {
		?>
		<div class="final-setup text-center">
			<h3 class="main-heading text-center">You're ready to start!</h3>
			<h4 class="sub-heading text-center">All configurations are done..!! Now you just need to add your staff into system</h4>
			<a href="<?php echo admin_url( '/admin.php?page=cip-staffs/' ); ?>" class="btn btn-success final-step_btn"> Add staff</a>
		</div>
		<?php
	}
}

new CIP_Admin_Setup_Wizard_free();