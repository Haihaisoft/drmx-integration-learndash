<?php
define('WP_USE_THEMES', false);
require_once dirname(__DIR__, 3) . '/wp-load.php';

/*When the user opens your protected content, DRM-X will provide the value.*/
$drmx_params = [
    'profileid'      => isset($_REQUEST['profileid']) ? sanitize_text_field($_REQUEST['profileid']) : '',
    'clientinfo'     => isset($_REQUEST['clientinfo']) ? sanitize_text_field($_REQUEST['clientinfo']) : '',
    'platform'       => isset($_REQUEST['platform']) ? sanitize_text_field($_REQUEST['platform']) : '',
    'contenttype'    => isset($_REQUEST['contenttype']) ? sanitize_text_field($_REQUEST['contenttype']) : '',
    'yourproductid'  => isset($_REQUEST['yourproductid']) ? sanitize_text_field($_REQUEST['yourproductid']) : "0",
    'rightsid'       => isset($_REQUEST['rightsid']) ? sanitize_text_field($_REQUEST['rightsid']) : '',
    'version'        => isset($_REQUEST['version']) ? sanitize_text_field($_REQUEST['version']) : '',
    'return_url'     => isset($_REQUEST['return_url']) ? $_REQUEST['return_url'] : '',
    'mac'            => isset($_REQUEST['mac']) ? sanitize_text_field($_REQUEST['mac']) : '',
];

if (!session_id()) {
   session_start();
}

$cache_key = 'drmx_temp_params_' . session_id();
wp_cache_delete($cache_key, 'options');
update_option($cache_key, $drmx_params, false);

if (is_user_logged_in()) {
    wp_redirect(home_url('/wp-content/plugins/drmx-integration-learndash/drmx_login.php'));
    exit;
} else {
   wp_redirect(wp_login_url(home_url('/wp-content/plugins/drmx-integration-learndash/drmx_login.php')));
   exit;
}

?>
