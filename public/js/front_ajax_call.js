/* Get Username & Password from frontend login form */
jQuery(document).ready(function () {

    jQuery('#loginform #user_login').attr('placeholder', 'Username');
    jQuery('#loginform #user_pass').attr('placeholder', 'Password');

    /* Values of Sub-Continents */
    jQuery(document).on('click', '.cip_front_btn', function (e) {
        e.preventDefault();

        var username = jQuery('#cip_user').val();
        var user_id  = jQuery('#cip_id').val();
        var type     = jQuery(this).data('value');
        var nounce   = ajax_cip.cip_nonce;
        var ds = "username=" + username + "&user_id=" + user_id + "&nounce=" + nounce + "&type=" + type + "&action=wl_cip_front_call";
        jQuery.ajax({
            url: ajax_cip.ajax_url,
            type: 'POST',
            data: ds,
            success: function (response) {
                if (response) {
                    alert(response);
                    console.log(response);
                    location.reload();
                }
            }
        });
    });
});