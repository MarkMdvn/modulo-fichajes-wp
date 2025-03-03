<style>
    #wpbody {
        padding-top: 0px !important;
    }
    .custom-nav-bar {
        background: #fff !important;
        padding: 10px 20px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1) !important;
        margin: 10px auto !important;
    }
    .custom-nav-bar .nav-left img {
        width: 100px !important;
    }
    .custom-nav-bar .nav-right {
        display: flex !important;
        align-items: center !important;
    }
    .custom-nav-bar .nav-right a {
        color: #333 !important;
        text-decoration: none !important;
        margin-left: 15px !important;
        font-weight: 500 !important;
        transition: color 0.3s ease !important;
        font-size: 1.5rem !important;
    }
    .custom-nav-bar .nav-right a:hover {
        color: #000 !important;
    }
    /* Mobile adjustments to maintain a single row layout */
    @media (max-width: 600px) {
        .custom-nav-bar {
            flex-direction: row !important;
            justify-content: space-between !important;
        }
        .custom-nav-bar .nav-right a {
            margin-left: 10px !important;
        }
    }
</style>

<div class="custom-nav-bar">
    <div class="nav-left">
        <!-- Replace with your logo URL -->
        <img href="https://control.carniceriademadrid.es/wp-admin/admin.php?page=subscribers-staff-attendance" src="https://www.carniceriademadrid.es/wp-content/uploads/2021/03/logo-carniceria-de-madrid.png" alt="Logo">
    </div>
    <div class="nav-right">
        <a href="https://control.carniceriademadrid.es/wp-admin/admin.php?page=subscribers-staff-attendance">
            <i class="fas fa-tachometer-alt"></i>
        </a>
        <a href="https://control.carniceriademadrid.es/wp-admin/admin.php?page=subscribers-staff-reports">
            <i class="fas fa-history"></i>
        </a>
        <a href="https://control.carniceriademadrid.es/wp-admin/admin.php?page=subscribers-staff-holidays">
            <i class="fas fa-umbrella-beach"></i>
        </a>
        <!-- Account Icon -->
        <a href="https://control.carniceriademadrid.es/account/">
            <i class="fas fa-user"></i>
        </a>
    </div>
</div>

<h1><?php esc_html_e('Vacaciones', CIP_FREE_TXTDM );?></h1>
<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

global $wpdb;
$date_format = get_option('date_format');
$time_format = get_option('time_format');
$holidays = get_option("cip_official_holidays");

// Obtener el ID del usuario actual
$current_user_id = get_current_user_id();
?>
<div>
    <!--official-holiday-->
    <div role="tabpanel" class="tab-pane active" id="official-holiday">

        <table class="table table-hover">
            <thead>
            <tr class="info">
                <th>#</th>
                <th><?php esc_html_e('Nombre', CIP_FREE_TXTDM );?></th>
                <th><?php esc_html_e('Fecha', CIP_FREE_TXTDM );?></th>
                <th><?php esc_html_e('Día/s', CIP_FREE_TXTDM );?></th>
            </tr>
            </thead>
            <?php
            if( 'yes' == get_clockin_settings( 'cip_settings' ) ) {
                if( $holidays = get_option("cip_official_holidays") ) { ?>
                    <tbody>
                    <?php
                    //Next 12 month
                    $startdate   = new \DateTime(date("Y")."-01-01");
                    $startdate   = $startdate->format("Y-m-d");
                    $plusOneYear = date("Y")+1;
                    $enddate     = new \DateTime($plusOneYear."-12-31");
                    $enddate     = $enddate->format("Y-m-d");
                    $i = strtotime($startdate);
                    $j = strtotime($enddate);
                    $all_dates = array();
                    for($i; $i <= $j; $i = strtotime(date("Y-m-d", strtotime("+1 day", $i))) ) {
                        array_push( $all_dates, date("Y-m-d", $i) );
                    }
                    $n = 1;
                    foreach( $all_dates as $row_date ) {
                        if ( ! empty ( $holidays ) ) {
                            foreach( $holidays as $key => $holiday ) {
                                // Filtrar vacación: si existe el campo 'user_ids' y el usuario actual no está asignado, se omite.
                                if ( isset( $holiday['user_ids'] ) && ! in_array( $current_user_id, $holiday['user_ids'] ) ) {
                                    continue;
                                }

                                $status = $holiday['status'];
                                if ( $status == 1 ) {
                                    $start_date = $holiday['start_date'];
                                    $end_date   = $holiday['end_date'];
                                    if(strtotime($start_date))
                                        $start_date = date($date_format, strtotime($holiday['start_date']));
                                    if(strtotime($end_date))
                                        $end_date = date($date_format, strtotime($holiday['end_date']));
                                    if($holiday['start_date'] == $row_date){
                                        ?>
                                        <tr>
                                            <td><?php esc_html_e( $n.".");?></td>
                                            <td><?php esc_html_e( $holiday['name']); ?></td>
                                            <?php if ($end_date == $start_date){ ?>
                                                <td><?php if($end_date != "") { esc_html_e( $end_date); } ?></td>
                                            <?php } else { ?>
                                                <td><?php esc_html_e( $start_date); if($end_date != "") { ?> - <?php esc_html_e( $end_date); } ?></td>
                                            <?php } ?>
                                            <td><?php esc_html_e('For '.$holiday['leaves'].' Day(s)', CIP_FREE_TXTDM );?></td>
                                        </tr>
                                        <?php
                                        $n++;
                                    }
                                }
                            }
                        }
                    } ?>
                    </tbody>
                <?php } else { ?>
                    <tbody>
                    <tr>
                        <td colspan='6'><?php esc_html_e('No hay nada por aquí...', CIP_FREE_TXTDM );?></td>
                    </tr>
                    </tbody>
                <?php }

            } else { ?>
                <tbody>
                <tr>
                    <td colspan='6' class="text-center"><?php esc_html_e('Not allowed.', CIP_FREE_TXTDM );?></td>
                </tr>
                </tbody>
            <?php } ?>
        </table>
    </div>
</div>
<?php
// Función de configuración, se mantiene igual.
function get_clockin_settings( $option_name ) {
    $fetch_settings = get_option( $option_name );
    if( isset( $fetch_settings ) && !empty( $fetch_settings ) ) {
        if( isset( $fetch_settings['staff_show_holidays'] ) && !empty( $fetch_settings['staff_show_holidays'] ) ) {
            $result = $fetch_settings['staff_show_holidays'];
        } else {
            $result = 'yes';
        }
    } else {
        $result = 'yes';
    }
    return $result;
}
?>
