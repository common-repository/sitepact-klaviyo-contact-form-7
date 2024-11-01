<?php
/**
 * Admin setup for the plugin
 *
 * @since 1.0
 * @function	KLCF_add_menu_links()		Add admin menu pages
 * @function	KLCF_register_settings	Register Settings
 * @function	KLCF_validater_and_sanitizer()	Validate And Sanitize User Input Before Its Saved To Database
 * @function	KLCF_get_settings()		Get settings from database
 */

// Exit if accessed directly
if ( ! defined('ABSPATH') ) exit; 

/**
 * Add admin menu pages - specifically logs
 *
 * @since 1.0
 */

    // Include necessary WordPress files
    if (!class_exists('WP_List_Table')) {
        require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
    }

    $log_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
	$nonce = isset($_GET['nonce']) ? sanitize_text_field($_GET['nonce']) : '';

    // Fetch the log post details from the database using the ID
    if ( $log_id > 0 && wp_verify_nonce( $nonce, 'view_log_action' ) ) {
        global $wpdb;
        $table_name = $wpdb->prefix . KLCF_KLAVIYO_CF7_TABLE; // Replace 'your_table_name' with your actual table name
		$log_post = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM %i WHERE id = %d", $table_name, $log_id ), ARRAY_A );

        // Display the log post details
        if (!empty($log_post)) {
            echo '<div class="tab-content"><h2>View Klaviyo Request Details</h2>';
            echo '<table class="form-table" role="presentation">';
            echo '<tbody>';

        // Loop through each log post attribute and generate table rows
        foreach ($log_post as $key => $value) {
            // Skip 'id' and 'time' columns
            if ($key === 'id' || $key === 'time') {
                continue;
            }

            echo '<tr>';
			echo '<th scope="row"><label for="' . esc_attr($key) . '">' . esc_html(ucfirst(str_replace('_', ' ', $key))) . '</label></th>';
            echo '<td>';

            // Check if the value is an array (e.g., response, request)
            if (is_array($value)) {
                // If it's an array, display each item in a list
                echo '<ul>';
                foreach ($value as $item) {
                    echo '<li>' . esc_html($item) . '</li>';
                }
                echo '</ul>';
            } else {
                // If it's not an array, display the value directly
                echo esc_html($value);
            }

            echo '</td>';
            echo '</tr>';
        }

        echo '</tbody>';
        echo '</table>';
        echo '<p><a href="?page=wpcf7-klaviyo&tab=klaviyo-logs" class="button">Back to Logs</a></p>';
        echo '</div>';
        } else {
			echo '<div class="wrap"><h2>Log Post Not Found</h2><p>The log post with the ID ' . esc_html($log_id) . ' was not found.</p></div>';
        }
    }else{

            // Define custom list table class
    class Custom_List_Table extends WP_List_Table {

        // Define table columns
        function get_columns() {
            return array(
                    'id'               => 'ID',
                    'post_id'          => 'Form Name',
                    'response_code'    => 'Response Code',
                    'time'             => 'Date Time',
                    'response'         => 'Response',
                    'request'          => 'Request',
                    'response_message' => 'Response Message',
                    'action'           => 'Action',
                );
        }

        // Retrieve data from database
        function prepare_items() {
            global $wpdb;

            $per_page = 10;
            $current_page = $this->get_pagenum();
            $offset = ($current_page - 1) * $per_page;

            $table_name = $wpdb->prefix . KLCF_KLAVIYO_CF7_TABLE; // Replace 'your_table_name' with your actual table name
			
			$total_items = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(id) FROM %i", $table_name ) );


            $this->set_pagination_args(array(
                'total_items' => $total_items,
                'per_page' => $per_page
            ));

            $this->process_bulk_action();

            $this->_column_headers = array($this->get_columns(), array(), array());

            $this->items = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM %i ORDER BY id DESC LIMIT %d OFFSET %d", $table_name, $per_page, $offset ), ARRAY_A );

        }
        
        // Display each column of the table
        function column_default($item, $column_name) {
            switch ($column_name) {
                case 'id':
					$nonce = wp_create_nonce( 'view_log_action' );
					return '<a href="?page=wpcf7-klaviyo&tab=klaviyo-logs&action=view&id=' . $item['id'] . '&nonce=' . $nonce . '">' . $item[$column_name] . '</a>';
                case 'post_id':
                    return get_the_title($item[$column_name]) ? get_the_title($item[$column_name]) : $item[$column_name];
                case 'response':
                    return substr(stripslashes($item[$column_name]), 0, 50)."..."; // Truncate response to one line
                case 'request':
                    return substr($item[$column_name], 0, 60)."..."; // Truncate response to one line
                case 'action':
					$nonce = wp_create_nonce( 'view_log_action' );
                    return '<a class="button" href="?page=wpcf7-klaviyo&tab=klaviyo-logs&action=view&id=' . $item['id'] . '&nonce=' . $nonce . '">View</a>';
                case 'response_message':
                    return substr(stripslashes($item[$column_name]), 0, 50)."..."; // Truncate response to one line
                default:
                    return $item[$column_name];
            }
        }

        // Add Clear Table button
        function get_bulk_actions() {
            $actions = array(
                'clear_table' => 'Clear Logs'
            );
            return $actions;
        }

        // Handle Clear Table action
        function process_bulk_action() {
            if ('clear_table' === $this->current_action()) {
                // Verify nonce
                if (!isset($_POST['_wpnonce']) || !wp_verify_nonce($_POST['_wpnonce'], 'bulk-' . $this->_args['plural'])) {
                    wp_die('Invalid nonce.');
                }
                // Check user capabilities
                if (!current_user_can('manage_options')) {
                    wp_die('You do not have sufficient permissions to perform this action.');
                }
                global $wpdb;
                $table_name = $wpdb->prefix . KLCF_KLAVIYO_CF7_TABLE; // Replace 'your_table_name' with your actual table name
				$wpdb->query( $wpdb->prepare( "TRUNCATE TABLE %i", $table_name ) );
            }
        }
    }

    // Render custom list table page
    function render_custom_list_table() {
        $list_table = new Custom_List_Table();
        $list_table->prepare_items();
        echo '<div class="tab-content"><h2>Klaviyo Integration Log</h2><p>View submission logs for Klaviyo</p>';
        echo '<form id="klaviyo-logs-table" method="POST">';
        $list_table->display();
        echo '</form>';
        echo '</div>';
    }

    // Call the function to render the custom list table
    render_custom_list_table();

}