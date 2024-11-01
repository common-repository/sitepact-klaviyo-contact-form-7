<?php
/**
 * Admin setup for the plugin
 *
 * @since 1.0
 * @function    KLCF_add_menu_links()        Add admin menu pages
 * @function    KLCF_register_settings      Register Settings
 * @function    KLCF_validater_and_sanitizer() Validate And Sanitize User Input Before Its Saved To Database
 * @function    KLCF_get_settings()          Get settings from database
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Add admin menu pages
 *
 * @since 1.0
 * @refer https://developer.wordpress.org/plugins/administration-menus/
 */

require_once( KLCF_KLAVIYO_CF7_DIR . "functions/admin-form-handler.php" );
require_once( KLCF_KLAVIYO_CF7_DIR . "functions/cf7.php" );

add_action( 'admin_menu', 'klcf_admin_menu' );
add_action( 'admin_enqueue_scripts', 'klcf_settings_add_assets' );
add_filter( 'wpcf7_editor_panels', 'KLCF_editor_panels' );

// Add plugin menu link to contact form 7 sub menu
function klcf_admin_menu() {
    add_submenu_page( 
        'wpcf7', 
        esc_html__( 'Klaviyo', 'klaviyo-contact-form-7' ), 
        esc_html__( 'Klaviyo Logs', 'klaviyo-contact-form-7' ), 
        'manage_options', 
        'wpcf7-klaviyo', 
        'klcf_routing' 
    );
}

// Load assets for the screens above
function klcf_settings_add_assets( $hook ) {
    if ( strpos( $hook, 'wpcf7' ) !== false ) {
        wp_enqueue_style( 'main-klcf-styles', KLCF_KLAVIYO_CF7_ASSETS . '/css/custom_bootstrap.css', array(), time(), false );
    }
}

function klcf_routing() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }
	
	// Verify nonce for the tab parameter
    if ( isset( $_GET['klcf_nonce'] ) && ! wp_verify_nonce( $_GET['klcf_nonce'], 'klcf_tab_action' ) ) {
		wp_die( esc_html__( 'Nonce verification failed', 'klaviyo-contact-form-7' ) );
    }
	
    // Get the active tab from the $_GET param
    $default_tab = null;
    $tab = isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : $default_tab;
	$nonce = wp_create_nonce( 'klcf_tab_action' );
    ?>
    <div class="wrap">
        <div id="icon-tools" class="icon32"></div>
        <h2><?php esc_html_e( 'Klaviyo Contact Form 7', 'klaviyo-contact-form-7' ); ?></h2>
        <nav class="nav-tab-wrapper">
			<a href="?page=wpcf7-klaviyo&tab=klaviyo-logs&klcf_nonce=<?php echo esc_attr( $nonce ); ?>" class="nav-tab <?php if ( $tab === 'klaviyo-logs' ) echo 'nav-tab-active'; ?>"><?php esc_html_e( 'Klaviyo Logs', 'klaviyo-contact-form-7' ); ?></a>

        </nav>

        <div class="tab-content">
            <?php include_once( KLCF_KLAVIYO_CF7_DIR . 'admin/templates/manage-logs.php' ); ?>
        </div>
    </div>
    <?php
}

// Hook into contact form 7 form
function KLCF_editor_panels( $panels ) {
    $new_page = array(
        'KLCF' => array(
            'title' => __( 'Klaviyo Integration', 'Klaviyo-Contact-Form-7' ),
            'callback' => 'KLCF_admin_after_additional_settings'
        )
    );

    wp_enqueue_script( 'main-klcf-script', KLCF_KLAVIYO_CF7_ASSETS . '/js/klcf-init.js', array(), '1.1', false );

    wp_localize_script( 'main-klcf-script', 'main_klcf_script_ajax_object', array(
        'ajax_url' => admin_url( 'admin-ajax.php' ),
        'nonce'    => wp_create_nonce( 'klcf_nonce' )
    ));

    $panels = array_merge( $panels, $new_page );

    return $panels;
}
?>
