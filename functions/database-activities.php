<?php
/**
 * Basic database table creation function for plugin
 *
 * @since 1.0
 * @function	migrate_db()		This function allows us to rename the db tables to support the rebuind
 * @function	KLCF_DB_Create()	This handles the DB table creation
 */

    // Exit if accessed directly
    if ( ! defined( 'ABSPATH' ) ) exit;
 	require_once(ABSPATH.'wp-admin/includes/upgrade.php');


    function migrate_db(){
        global $wpdb; // WordPress database access object

        // Define old and new table names
        $table_name_mappings = array(
            $wpdb->prefix.'_klcf_new_logs' => $wpdb->prefix.KLCF_KLAVIYO_CF7_TABLE,
            $wpdb->prefix.'klcf_meta_table' => $wpdb->prefix.KLCF_KLAVIYO_CF7_META_TABLE
        );
        
        /* Iterate through table mappings*/
        foreach ($table_name_mappings as $old_table_name => $new_table_name) {	
			// Check if old table exists before attempting to rename
			$query = $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $old_table_name ) );
			if ( $wpdb->get_var( $query ) === $old_table_name ) {
				//$wpdb->query( $wpdb->prepare( "RENAME TABLE %s TO %s", $old_table_name, $new_table_name ) );
				$wpdb->query( $wpdb->prepare( 'RENAME TABLE %i TO %i', $old_table_name, $new_table_name ) );
			}
			
			// Check if new table already exists	
			$query = $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $new_table_name ) );
			if ( $wpdb->get_var( $query ) === $new_table_name ) { //prepared above
				continue; // Skip renaming if new table already exists
			}
        }
    }





    function KLCF_maybe_create_table( $table_name, $create_sql ) {
        global $wpdb; // WordPress database access object

        // Checking if the database supports collation, and getting the collate value if it does
        $collate = $wpdb->has_cap( 'collation' ) ? $wpdb->get_charset_collate() : '';
        
        // Checking if the table already exists in the database
        $table_found = $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $table_name ) );
        if ( $wpdb->get_var( $table_found ) === $table_name ) { //Prepared statement used above $table_found
		    return true; // If the table exists, return true
	    }
        
        // Adding collate to the SQL query
        $create_sql_collection = $create_sql." ".$collate;
        
        // Attempting to create the table in the database
		//$prepared_sql = $wpdb->prepare($create_sql); // Use prepared statement
		//$check = $wpdb->query($prepared_sql);
		dbDelta($create_sql_collection);

        // Returning true if the table exists after creation, false otherwise
        $table_created = $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $table_name ) );
        if ( $wpdb->get_var( $table_created ) === $table_created ) { //Prepared statement used above $table_created
		    return true; // If the table exists, return true
	    }else{
            return false;
        }
    }


    function KLCF_DB_Create(){
        global $wpdb; // WordPress database access object

		$tbl_name =  $wpdb->prefix.KLCF_KLAVIYO_CF7_TABLE;
		$table= "
                CREATE TABLE $tbl_name (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `post_id` int(11) NOT NULL,
                    `request` text DEFAULT NULL,
                    `response` text DEFAULT NULL,
                    `response_message` text DEFAULT NULL,
                    `time` timestamp NOT NULL DEFAULT current_timestamp(),
                    `response_code` int(11) NOT NULL,
                    PRIMARY KEY (`id`)
                );
                ";
		KLCF_maybe_create_table($tbl_name, $table);

		$tbl_name   = $wpdb->prefix.KLCF_KLAVIYO_CF7_META_TABLE;
		$table= "
				CREATE TABLE $tbl_name (
					`id` int(11) NOT NULL AUTO_INCREMENT,
					`meta_key` varchar(255) NOT NULL,
					`meta_value` text NOT NULL,
					`created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE current_timestamp(),
					PRIMARY KEY (`id`),
					UNIQUE KEY `meta_key` (`meta_key`) USING HASH
				);
				";
		KLCF_maybe_create_table($tbl_name, $table);
	}

    function KLCF_add_log($log_data) {
        global $wpdb;
        KLCF_DB_Create();

        // Ensure all values are strings and escape them for SQL
        $data = [
            "post_id" => is_scalar($log_data['post_id']) ? (string)$log_data['post_id'] : wp_json_encode($log_data['post_id']),
            "request" => is_scalar($log_data['request']) ? (string)$log_data['request'] : wp_json_encode($log_data['request']),
            "response" => is_scalar($log_data['response']) ? (string)$log_data['response'] : wp_json_encode($log_data['response']),
            "response_code" => is_scalar($log_data['response_code']) ? (string)$log_data['response_code'] : wp_json_encode($log_data['response_code']),
            "response_message" => is_scalar($log_data['response_message']) ? (string)$log_data['response_message'] : wp_json_encode($log_data['response_message']),
        ];
		
		$table_name = $wpdb->prefix . KLCF_KLAVIYO_CF7_TABLE;
		$data = array(
			'post_id' =>  $data['post_id'],
			'request' => $data['request'],
			'response' => $data['response'],
			'response_code' => $data['response_code'],
			'response_message' => $data['response_message'],
		);
		$wpdb->insert($table_name, $data);
    }
