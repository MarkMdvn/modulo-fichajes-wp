<h1><?php esc_html_e('Official Holidays', CIP_FREE_TXTDM );?></h1>
<?php 
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

// get all records
global $wpdb;
$date_format = get_option('date_format');
$time_format = get_option('time_format');
$holidays = get_option("cip_official_holidays");
?>
<div>
	<!--official-holiday-->
	<div role="tabpanel" class="tab-pane active" id="official-holiday">
		
		<table class="table table-hover">
			<thead>
				<tr class="info">
					<th>#</th>
					<th><?php esc_html_e('Holiday Name', CIP_FREE_TXTDM );?></th>
					<th><?php esc_html_e('Date', CIP_FREE_TXTDM );?></th>
					<th><?php esc_html_e('Day(s)', CIP_FREE_TXTDM );?></th>
				</tr>
			</thead>
			<?php 
			if( 'yes' == get_clockin_settings( 'cip_settings' ) ) {
				if($holidays = get_option("cip_official_holidays")) { ?>
					<tbody>
						<?php	
						//Next 12 month	
						$startdate = new \DateTime(date("Y")."-01-01");                                                                                                                                                                        
						$startdate = $startdate->format("Y-m-d");
						$plusOneYear = date("Y")+1;
						$enddate = new \DateTime($plusOneYear."-12-31");                                                                                                                                                                       
						$enddate = $enddate->format("Y-m-d");	
						$i = strtotime($startdate);
						$j = strtotime($enddate);
						$all_dates = array();
						for($i; $i <= $j; $i = strtotime(date("Y-m-d", strtotime("+1 day", $i))) ) {
							array_push( $all_dates, date("Y-m-d", $i) );
						}
						$n = 1;
						foreach( $all_dates as $row_date ) {	
						if ( ! empty ( $holidays ) ) {
							
							if ( ! empty( $holidays ) ) {						
							foreach( $holidays as $key => $holiday ) {
							$status = $holiday['status'];
							if ( $status == 1 ) {
								$start_date = $holiday['start_date'];
								$end_date   = $holiday['end_date'];
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
							<td><?php esc_html_e('For '.$holiday['leaves'].' Day(s)', CIP_FREE_TXTDM );?></td>
						</tr>
						<?php $n++;} } } } } } ?>
					</tbody>
					<?php } else { ?>
						<tbody><tr><td colspan='6'><?php esc_html_e('No Holiday Found.', CIP_FREE_TXTDM );?></td></tr></tbody>
					<?php } 
				
			} else {
				?>
					<tbody><tr><td colspan='6' class="text-center"><?php esc_html_e('Not allowed.', CIP_FREE_TXTDM );?></td></tr></tbody>
				<?php
			}
			?>			
			<thead>
				<tr class="info">
					<th>#</th>
					<th><?php esc_html_e('Holiday Name', CIP_FREE_TXTDM );?></th>
					<th><?php esc_html_e('Date', CIP_FREE_TXTDM );?></th>
					<th><?php esc_html_e('Day(s)', CIP_FREE_TXTDM );?></th>
				</tr>
			</thead>
		</table>
	</div>
</div>
<?php
function get_clockin_settings( $option_name ) {
    $fetch_settings            = get_option( $option_name );
    // return $fetch_settings['staff_show_holidays'];
    if( isset( $fetch_settings  ) && !empty( $fetch_settings  ) ) {
    	if( isset( $fetch_settings['staff_show_holidays'] ) && !empty( $fetch_settings['staff_show_holidays'] ) ) {
    		$result = $fetch_settings['staff_show_holidays'];
    	} else {
    		$result = 'yes';
    	}		
	} else{
		$result = 'yes';
	}
    return $result;
}