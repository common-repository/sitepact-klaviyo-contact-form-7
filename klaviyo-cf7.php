<?php
/**
* Plugin Name: Sitepact's Contact Form 7 Extension For Klaviyo
* Requires Plugins: contact-form-7
* Description: Integrate Contact Form 7 with Klaviyo. Automatically add form submissions to predetermined lists and fields. You can also trigger Klaviyo metric flows/events.
* Version: 3.0.1
* License: GPL v3
* Requires at least: 6.2
* Tested up to: 6.5.5
* Requires PHP: 7.2
* Author URI: https://sitepact.com
* Plugin URI: https://sitepact.com/contact-form-7-klaviyo-integration/
* Author: Sitepact
* Text Domain: klaviyo-cf7
* License URI: https://www.gnu.org/licenses/gpl-3.0.html
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;



/**
 * Define constants
 *
 * @since 1.0
 */
if ( ! defined( 'KLCF_VERSION_NUM' ) ) 		define( 'KLCF_VERSION_NUM'		, '3.0.1' ); // Plugin version constant
if ( ! defined( 'KLCF_KLAVIYO_CF7_FILE' ) )		define( 'KLCF_KLAVIYO_CF7_FILE'		, plugin_basename( __FILE__ ) ); // Name of the plugin folder eg - 'klaviyo-cf7'
if ( ! defined( 'KLCF_KLAVIYO_CF7' ) )		define( 'KLCF_KLAVIYO_CF7'		, trim( dirname( plugin_basename( __FILE__ ) ), '/' ) ); // Name of the plugin folder eg - 'klaviyo-cf7'
if ( ! defined( 'KLCF_KLAVIYO_CF7_DIR' ) )	define( 'KLCF_KLAVIYO_CF7_DIR'	, plugin_dir_path( __FILE__ ) ); // Plugin directory absolute path with the trailing slash. 
if ( ! defined( 'KLCF_KLAVIYO_CF7_URL' ) )	define( 'KLCF_KLAVIYO_CF7_URL'	, plugin_dir_url( __FILE__ ) ); // URL to the plugin folder with the trailing slash.
if ( ! defined( 'KLCF_KLAVIYO_CF7_ASSETS' ) )	define( 'KLCF_KLAVIYO_CF7_ASSETS'	, plugin_dir_url( __FILE__) . 'includes/assets'); //Asset Folder
if ( ! defined( 'KLCF_KLAVIYO_CF7_TABLE' ) )	define( 'KLCF_KLAVIYO_CF7_TABLE'	, "klcf_logs" ); //Database table name for logs
if ( ! defined( 'KLCF_KLAVIYO_CF7_META_TABLE' ) )	define( 'KLCF_KLAVIYO_CF7_META_TABLE'	, "klcf_plugin_meta" ); // Database table name for plugin meta meta
if ( ! defined( 'KLCF_KLAVIYO_CF7_OPTIN_INSTRUCTIONS' ) )	define( 'KLCF_KLAVIYO_CF7_OPTIN_INSTRUCTIONS'	, "https://help.klaviyo.com/hc/en-us/articles/115005251108-Guide-to-The-Double-Opt-In-Process" ); // Opt in Instructions
if ( ! defined( 'KLCF_API_BASE_URL' ) )	define( 'KLCF_API_BASE_URL'	, "https://a.klaviyo.com/api" ); // Database table name for logs meta


/**
 * Database upgrade todo
 *
 * @since 1.0
 */
function KLCF_upgrader() {
	
	// Get the current version of the plugin stored in the database.
	$current_ver = get_option( 'abl_KLCF_version', '0.0' );
	
	// Return if we are already on updated version. 
	if ( version_compare( $current_ver, KLCF_VERSION_NUM, '==' ) ) {
		return;
	}
	
	// This part will only be executed once when a user upgrades from an older version to a newer version.
	
	// Finally add the current version to the database. Upgrade todo complete. 
	update_option( 'abl_KLCF_version', KLCF_VERSION_NUM );
}
add_action( 'admin_init', 'KLCF_upgrader' );

// Load everything
require_once( KLCF_KLAVIYO_CF7_DIR . 'loader.php' );

// Register activation hook (this has to be in the main plugin file or refer bit.ly/2qMbn2O)
register_activation_hook( __FILE__, 'KLCF_activate_plugin' );
