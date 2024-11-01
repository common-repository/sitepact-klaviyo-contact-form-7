<?php 
/**
 * Basic setup functions for the plugin
 *
 * @since 1.0
 * @function	KLCF_activate_plugin()		Plugin activatation todo list
 * @function	KLCF_load_plugin_textdomain()	Load plugin text domain
 * @function	KLCF_settings_link()			Print direct link to plugin settings in plugins list in admin
 * @function	KLCF_plugin_row_meta()		Add donate and other links to plugins list
 */


// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


// Require the core stuff
require(KLCF_KLAVIYO_CF7_DIR."functions/database-activities.php");
require(KLCF_KLAVIYO_CF7_DIR."functions/klaviyo-api.php");

//Instantiate Klaviyo API Class
global $klaviyo_api;
$klaviyo_api = new Klaviyo_API();

add_filter('wp_enqueue_scripts', 'register_mask_script');
function register_mask_script(){
  wp_register_script( 'klcf-input-mask-script', KLCF_KLAVIYO_CF7_ASSETS. '/js/klcf-input-mask.js', array(), "1.0" , false );
}

/**
 * Plugin activation todo list
 *
 * This function runs when user activates the plugin. Used in register_activation_hook in the main plugin file. 
 * @since 1.0
 */
function KLCF_activate_plugin() {

	if (is_plugin_active('KLCF_Klaviyo-Contact-Form-7/KLCF_Klaviyo-Contact-Form-7.php')) {
        // Deactivate the free version plugin
        deactivate_plugins('KLCF_Klaviyo-Contact-Form-7/KLCF_Klaviyo-Contact-Form-7.php');
    }

	//migrate db - rename tables and stuff
	migrate_db();

	//Here we create the db tables
	KLCF_DB_Create();
}

/**
 * Load plugin text domain
 *
 * @since 1.0
 */
function KLCF_load_plugin_textdomain() {
    load_plugin_textdomain( 'klaviyo-cf7', false, '/klaviyo-cf7/languages/' );
}
add_action( 'plugins_loaded', 'KLCF_load_plugin_textdomain' );


/**
 * Print direct link to plugin settings in plugins list in admin
 *
 * @since 1.0
 */
function KLCF_settings_link( $links ) {
	return array_merge(
		array(
			'settings' => '<a href="' . esc_url( admin_url( 'admin.php?page=wpcf7-klaviyo' ) ) . '">' . __( 'Settings', 'klaviyo-cf7' ) . '</a>'
		),
		$links
	);
}
add_filter( 'plugin_action_links_' . KLCF_KLAVIYO_CF7 . '/klaviyo-cf7.php', 'KLCF_settings_link' );

/**
 * Add donate and other links to plugins list
 *
 * @since 1.0
 */
function KLCF_plugin_row_meta( $links, $file ) {
	if ( strpos( $file, 'klaviyo-cf7.php' ) !== false ) {
		$new_links = array(
            'Get-Support' 	=> '<a href="' . esc_url('http://sitecare.sitepact.com') . '" target="_blank">Get Support</a>',
            'Donate' 	=> '<a href="' . esc_url('https://paypal.me/ryonwhyte') . '" target="_blank">Donate</a>',
			);
		$links = array_merge( $links, $new_links );
	}
	return $links;
}
add_filter( 'plugin_row_meta', 'KLCF_plugin_row_meta', 10, 2 );


// Display admin notice
function klcf1_admin_notice() {
    if ( klcf_get_notice_status("klcf_v3_update_notice_dismissed") ) {
        return;
    }
    $dismiss_url = add_query_arg( array(
        'klcf_v3_update_notice_dismissed' => '1',
        'klcf_nonce' => wp_create_nonce( 'klcf_dismiss_notice' )
    ) );
    ?>
    <div class="notice notice-warning is-dismissible">
        <p><?php esc_html_e( 'Important! Contact Form 7 Extension For Klaviyo version 3 needs to be re-configured. Please go to form settings, reactivate and reconfigure to ensure form details are being sent to Klaviyo.', 'klaviyo-cf7' ); ?></p>
        <a href="<?php echo esc_url( get_admin_url( null, 'admin.php?page=wpcf7-klaviyo' ) ); ?>"><?php esc_html_e( 'Settings', 'klaviyo-cf7' ); ?></a>
        <p><a class="klcf-hide-notice button" href="<?php echo esc_url( $dismiss_url ); ?>"><?php esc_html_e( 'Already Done', 'klaviyo-cf7' ); ?></a></p>
    </div>
    <?php
}
add_action( 'admin_notices', 'klcf1_admin_notice' );

// Get notice status
function klcf_get_notice_status($dismiss_status_meta) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'klcf_plugin_meta';
    $status = $wpdb->get_var( $wpdb->prepare( "SELECT meta_value FROM %i WHERE meta_key = %s", $table_name, $dismiss_status_meta ) );
    return $status;
}

// Check and handle the dismiss notice query parameter
function klcf_check_dismiss_notice() {
	//Nonce Verified below
    if ( isset( $_GET['klcf_v3_update_notice_dismissed'] ) && '1' == sanitize_text_field( $_GET['klcf_v3_update_notice_dismissed'] ) && current_user_can( 'manage_options' ) ) {
        $nonce = isset( $_GET['klcf_nonce'] ) ? $_GET['klcf_nonce'] : '';
        if ( ! wp_verify_nonce( $nonce, 'klcf_dismiss_notice' ) ) {
			wp_die( esc_html__( 'Security check failed.', 'klaviyo-cf7' ) );
        } else {
            klcf_save_notice_status( 'klcf_v3_update_notice_dismissed', '1' );
            wp_safe_redirect( remove_query_arg( 'klcf_v3_update_notice_dismissed' ) );
            exit;
        }
    }
}
add_action( 'admin_init', 'klcf_check_dismiss_notice' );

// Save notice status
function klcf_save_notice_status($meta_key, $status) {
    global $wpdb;
	$meta_key = sanitize_key( $meta_key );
	$status = sanitize_text_field( $status );
    $table_name = $wpdb->prefix . 'klcf_plugin_meta';
    $exists = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM %i WHERE meta_key = %s", $table_name, $meta_key ) );

    if ( $exists ) {
        $wpdb->update(
            $table_name,
            array( 'meta_value' => $status ),
            array( 'meta_key' => $meta_key ),
            array( '%s' ),
            array( '%s' )
        );
    } else {
        $wpdb->insert(
            $table_name,
            array(
                'meta_key' => $meta_key,
                'meta_value' => $status
            ),
            array(
                '%s',
                '%s'
            )
        );
    }
}


