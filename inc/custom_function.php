<?php 
//All custom functions 
//Function to sum working hours //


//------------function for all datea---------//
function range_date_free($first, $last) {
  $arr = array();
  $now = strtotime($first);
  $last = strtotime($last);

  while( $now <= $last ) {
    array_push( $arr, date( 'Y-m-d', $now ) );
    $now = strtotime( '+1 day', $now );
  }
  return $arr;
}

//get user location
function user_locationn_free($user_ip)
{
	$details = unserialize(file_get_contents('http://www.geoplugin.net/php.gp?ip='.$user_ip));
	return $details['geoplugin_city'].", ".$details['geoplugin_region'].", ".$details['geoplugin_countryName'];
}

//get holiday dates
function cip_holiday_days_free()
{
	$holidays_settings = get_option("cip_official_holidays");
	$main_arr_holi = '';
	if ( $holidays_settings = get_option( "cip_official_holidays" ) ) {	
		//Next 12 month	
		$startdateee = new \DateTime(date("Y")."-01-01");                                                                                                                                                                        
		$startdateee = $startdateee->format("Y-m-d");
		$enddateee = new \DateTime(date("Y")."-12-31");                                                                                                                                                                        
		$enddateee = $enddateee->format("Y-m-d");	
		$k = strtotime($startdateee);
		$l = strtotime($enddateee);
		$all_datesss = array();
		for($k; $k <= $l; $k = strtotime(date("Y-m-d", strtotime("+1 day", $k))) ) {
			array_push( $all_datesss, date( "Y-m-d", $k ) ); 
		}
		$holliday_arrrr = array();
		$holliday_arrrr1 = array();
		$holliday_arrrr2 = array();
		foreach($all_datesss as $row_dateee) {		
			//print_r($holidays_settings['name']);
			if( ! empty ($holidays_settings)) {						
				foreach($holidays_settings as $key => $holidayyy) {	
					$start_date = $holidayyy['start_date'];
					$end_date = $holidayyy['end_date'];
					$date_format = get_option('date_format');
					if(strtotime($start_date))
					$start_date = date($date_format , strtotime($holidayyy['start_date']));
					if(strtotime($end_date))
					$end_date = date($date_format , strtotime($holidayyy['end_date']));
						if($holidayyy['start_date'] == $row_dateee){
							 if ($end_date == $start_date){ 
								    array_push($holliday_arrrr,date('Y-m-d',strtotime($holidays_settings['start_date'])));
								}else{
									array_push($holliday_arrrr1,date('Y-m-d',strtotime($holidayyy['start_date'])));
									for ($i=0; $i < $holidayyy['leaves'] ; $i++) { 
										$start_date1 = date('Y-m-d', strtotime($start_date . ' +'.$i.' day'));
									}
									array_push($holliday_arrrr2,$start_date1);
								} 
							} 
						} 
					}
				}
			$main_arr_holi = array_merge($holliday_arrrr,$holliday_arrrr1,$holliday_arrrr2);
		}
	return $main_arr_holi;
}

// Get holiday name
function cip_holiday_name_free($date)
{
	$holidays_settings = get_option("cip_official_holidays");
	if($holidays_settings = get_option("cip_official_holidays")) {
		//Next 12 month	
		$startdateee = new \DateTime(date("Y")."-01-01");                                                                                                                                                                        
		$startdateee = $startdateee->format("Y-m-d");
		$enddateee = new \DateTime(date("Y")."-12-31");                                                                                                                                                                        
		$enddateee = $enddateee->format("Y-m-d");	
		$k = strtotime($startdateee);
		$l = strtotime($enddateee);
		$all_datesss = array();
		for($k; $k <= $l; $k = strtotime(date("Y-m-d", strtotime("+1 day", $k))) ) {
			array_push( $all_datesss, date("Y-m-d", $k) );
		}
		$holliday_arrrr = array();
		$holliday_arrrr1 = array();
		$holliday_arrrr2 = array();
		foreach($all_datesss as $row_dateee) {		
			if( ! empty ($holidays_settings)) {						
				foreach($holidays_settings as $key => $holidayyy) {
					$start_date = $holidayyy['start_date'];
					$end_date = $holidayyy['end_date'];
					$holidy_name = $holidayyy['name'];
					if(strtotime($start_date))
					$start_date = date($date_format , strtotime($holidayyy['start_date']));
					$that_day = date('Y-m-d',strtotime($holidayyy['start_date']));
					if(strtotime($end_date))
					$end_date = date($date_format , strtotime($holidayyy['end_date']));
						if($holidayyy['start_date'] == $row_dateee){
							 if ($end_date == $start_date){ 
								    array_push($holliday_arrrr, ["$holidy_name" => "$that_day"] );
								}else{
									array_push($holliday_arrrr1, ["$holidy_name" => "$that_day"] ); 
									for ($i=0; $i < $holidayyy['leaves'] ; $i++) { 
										$start_date1 = date('Y-m-d', strtotime($start_date . ' +'.$i.' day'));
									}
									array_push($holliday_arrrr2, ["$holidy_name" => "$start_date1"] ); 
								} 
							} 
						} 
					}
				}
			$main_arr_holi = array_merge($holliday_arrrr,$holliday_arrrr1,$holliday_arrrr2);
		}
		foreach ($main_arr_holi as $value) {
        	foreach($value as $key => $values){
            	if($values == $date){
                	return $key;
            	}
        	}
    	}
}

//Getting staff total abset days and hours
function staff_total_absent_days_free($userid)
{
	global $wpdb;
	$staff_attendance_table = $wpdb->base_prefix . "sm_attendance";
	$staff_table = $wpdb->base_prefix . "sm_staffs";
	$holiday_arr = cip_holiday_days_free();
	$first = date('Y-m-01');
	$last = date('Y-m-t');
	$all_dates_attend = range_date_free($first, $last);
	$working_hour_arr = array();

	$current_dat = date('Y-m-d');
	$days = 0;
	foreach($all_dates_attend as $row_date){
	$row = $wpdb->get_row( $wpdb->prepare("SELECT * FROM `$staff_attendance_table` WHERE `staff_id` = %d AND `date` LIKE %s", $userid, $row_date) );
	$holiday_arr = cip_holiday_days_free();
	if ( empty ( $holiday_arr ) ) {
		$holiday_arr = array();
	}
		if( ( (date("l", strtotime($row_date)) != "Sunday") && ( empty ($row)) ) || ( in_array($row_date, $holiday_arr) && ( empty ($row)) ) ) { 
			//check if Sunday else no record found
			if(!in_array($row_date, $holiday_arr)){		
				// get name of that day
				if ($row_date < $current_dat){
					$days++;
				}		
			}										
		}
	}	
	return $days;
}

function cip_table_column_exists( $table_name, $column_name ) {
   global $wpdb;
   $column = $wpdb->get_results( $wpdb->prepare(
       "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = %s AND TABLE_NAME = %s AND COLUMN_NAME = %s ",
       DB_NAME, $table_name, $column_name
   ) );
   if ( ! empty( $column ) ) {
       return true;
   }
   return false;
}

//Getting staff total abset days and hours
function cip_staff_total_absent_days_free($userid, $all_dates_attend)
{
	global $wpdb;
	$staff_attendance_table = $wpdb->base_prefix . "sm_attendance";
	foreach($all_dates_attend as $row_date){
	$row = $wpdb->get_row( $wpdb->prepare("SELECT * FROM `$staff_attendance_table` WHERE `staff_id` = %d AND `date` LIKE %s", $userid, $row_date) );
	$holiday_arr = cip_holiday_days_free();
	$current_dat = date('Y-m-d');
	$absent_days_date = array();
		if( ( (date("l", strtotime($row_date)) != "Sunday") && ( empty ($row)) ) || ( in_array($row_date, $holiday_arr) && ( empty ($row)) ) ) { 
			//check if Sunday else no record found
			if(!in_array($row_date, $holiday_arr)){		
				// get name of that day
				if ($row_date < $current_dat){
					array_push($absent_days_date, $row_date);
				}		
			}										
		}
	}
	return $absent_days_date;
}

?>
