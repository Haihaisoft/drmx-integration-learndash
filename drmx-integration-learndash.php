<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.haihaisoft.com
 * @since             1.0.0
 * @package           Drmx_Integration_Learndash
 *
 * @wordpress-plugin
 * Plugin Name:       DRM-X 4.0 Integration With LearnDash
 * Plugin URI:        https://www.drm-x.com
 * Description:       DRM-X 4.0 integrates with LearnDash, verifies student course permissions when obtain licenses, and automatically obtain licenses. And it supports embedding encrypted videos into Xvast Player using shortcodes, For example [xvast-player] Video URL [/xvast-player].
 * Version:           1.0.0
 * Author:            Haihaisoft
 * Author URI:        https://www.haihaisoft.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       drmx-integration-learndash
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'DRMX_INTEGRATION_LEARNDASH_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-drmx-integration-learndash-activator.php
 */
function activate_drmx_integration_learndash() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-drmx-integration-learndash-activator.php';
	Drmx_Integration_Learndash_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-drmx-integration-learndash-deactivator.php
 */
function deactivate_drmx_integration_learndash() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-drmx-integration-learndash-deactivator.php';
	Drmx_Integration_Learndash_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_drmx_integration_learndash' );
register_deactivation_hook( __FILE__, 'deactivate_drmx_integration_learndash' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-drmx-integration-learndash.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_drmx_integration_learndash() {

	$plugin = new Drmx_Integration_Learndash();
	$plugin->run();

}
run_drmx_integration_learndash();

function embed_xvast_player($atts = [], $content){
	$embed_html = '<div id="Xvast_Video_URL" style="display: none;">'.$content.'</div>
	<script type="text/javascript" src="https://www.xvast.com/dist/js/embedPlayer.js"></script>
	<script type="text/javascript" src="https://www.xvast.com/dist/js/video.js"></script>
	<script type="text/javascript" src="https://www.xvast.com/dist/wordpress/XvastVideoJSPlayer.js"></script>';
	
	return $embed_html;
}
add_shortcode('xvast-player','embed_xvast_player');

add_action('admin_menu', 'register_drmx_integration_learndash_menu');

function register_drmx_integration_learndash_menu(){
	add_menu_page('DRM-X 4.0 integration with LearnDash','DRM-X 4.0 with LearnDash','administrator','DRMX-integration-LearnDash-Settings','drmx_integration_learndash_settings');
}

function drmx_integration_learndash_settings(){
	include 'drmx-integration-learndash-settings.php';
}