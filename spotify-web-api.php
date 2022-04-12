<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://porto.storymadeid.my.id
 * @since             1.0.0
 * @package           Spotify_Web_Api
 *
 * @wordpress-plugin
 * Plugin Name:       Spotify Web Api
 * Plugin URI:        https://github.com/Spectrevuln-sketch
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Gerry
 * Author URI:        https://porto.storymadeid.my.id
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       spotify-web-api
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
define( 'SPOTIFY_WEB_API_VERSION', '1.0.0' );
define('SPOTIFY_DIR', plugin_dir_path(__FILE__));
define('SPOTIFY_URL', plugin_dir_url(__FILE__));
define('SPOTIFY_DIR_2',  plugin_dir_path( dirname( __FILE__, 2 ) ));

// define( 'CLIENT_ID', '117fb17d61d34dd1b218e27dd84286c4' );
// define( 'CLIENT_SECRET', '205515859e4f4db5a70663e0f96f5902' );


/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-spotify-web-api-activator.php
 */
function activate_spotify_web_api() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-spotify-web-api-activator.php';
	Spotify_Web_Api_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-spotify-web-api-deactivator.php
 */
function deactivate_spotify_web_api() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-spotify-web-api-deactivator.php';
	Spotify_Web_Api_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_spotify_web_api' );
register_deactivation_hook( __FILE__, 'deactivate_spotify_web_api' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-spotify-web-api.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_spotify_web_api() {

	$plugin = new Spotify_Web_Api();
	$plugin->run();

}
run_spotify_web_api();
