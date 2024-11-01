<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

    function fetch_klaviyo_lists() {
        global $klaviyo_api;
        check_ajax_referer('klcf_nonce', 'nonce');

        $api_key = sanitize_text_field($_POST['api_key']);
        $post_id = intval($_POST['post_id']);

        if (empty($api_key) || empty($post_id)) {
            wp_send_json_error('Please enter a Klaviyo Private API Key');
        }
        
        $lists = $klaviyo_api->get_lists($api_key);

        //If klaviyo api returned error then send the error detail
        if (isset($lists['error'])) {
            delete_post_meta($post_id, '_KLCF_key');
            delete_post_meta($post_id, '_KLCF_lists');
            wp_send_json_error($lists['error']);
        }

        if (!empty($lists)) {
            //delete_post_meta($post_id, '_KLCF_key');
            update_post_meta($post_id, '_KLCF_key', $api_key);
            update_post_meta($post_id, '_KLCF_form_send_status', 1);
            update_post_meta($post_id, '_KLCF_lists', $lists);
            wp_send_json_success($lists);
        } else {
            wp_send_json_error('No List Found in Klaviyo account. Please create list in Klaviyo.');
        }
    }
    add_action('wp_ajax_fetch_klaviyo_lists', 'fetch_klaviyo_lists');

    //We update form send status
    function change_integration_status() {
        check_ajax_referer('klcf_nonce', 'nonce');
        //klcf_write_log($_POST);

        $post_id = intval($_POST['post_id']);
        $integration_status = intval($_POST['integration_status']);

        $api_key = 	get_post_meta($post_id, "_KLCF_key", true); //Klaviyo API Key

        if(!empty($api_key)){
            //delete_post_meta( $post_id, "_KLCF_key");
            //delete_post_meta( $post_id, "_KLCF_form_send_status");
            update_post_meta($post_id, '_KLCF_form_send_status',  $integration_status);
            wp_send_json_success();
        }else{
            wp_send_json_error('Please Enter API Key');
        }
    }
    add_action('wp_ajax_change_integration_status', 'change_integration_status');


    //We prepare the request for creating the Klaviyo profile
    function prepareKlaviyoCreateProfile($formSettings, $klaviyo_data_model, $submission_data, $klaviyo_mapping){
        global $klaviyo_api;
        $klaviyo_data = $klaviyo_data_model;

        // Map standard fields
        foreach ($klaviyo_mapping as $klaviyo_key => $form_field) {
            $klaviyo_field = str_replace('profile_', '', $klaviyo_key);
                if (!empty($submission_data[$form_field])) {
                    // Handling nested location fields
                    if (strpos($klaviyo_field, 'location_') === 0) {
                        $location_field = str_replace('location_', '', $klaviyo_field);
                        $klaviyo_data['attributes']['location'][$location_field] = $submission_data[$form_field];
                    } else {
                        $klaviyo_data['attributes'][$klaviyo_field] = $submission_data[$form_field];
                    }
                }
        }

        $klaviyo_data['attributes'] = array_filter_recursive($klaviyo_data['attributes']);
        $payload = wp_json_encode(["data" => $klaviyo_data]);

        $created_profile = $klaviyo_api->klaviyo_create_profile($formSettings, $payload);
        //if($created_profile)
        return $created_profile;
    }


    //We prepare the request for creating the Klaviyo profile
    function prepareKlaviyoUpdateProfile($formSettings, $klaviyo_data_model, $submission_data, $klaviyo_mapping, $profile_ID){
        global $klaviyo_api;
        $klaviyo_data = $klaviyo_data_model;

        $klaviyo_data['id'] = $profile_ID;

        // Map standard fields
        foreach ($klaviyo_mapping as $klaviyo_key => $form_field) {
            $klaviyo_field = str_replace('profile_', '', $klaviyo_key);
                if (!empty($submission_data[$form_field])) {
                    // Handling nested location fields
                    if (strpos($klaviyo_field, 'location_') === 0) {
                        $location_field = str_replace('location_', '', $klaviyo_field);
                        $klaviyo_data['attributes']['location'][$location_field] = $submission_data[$form_field];
                    } else {
                        $klaviyo_data['attributes'][$klaviyo_field] = $submission_data[$form_field];
                    }
                }
        }

        $klaviyo_data['attributes'] = array_filter_recursive($klaviyo_data['attributes']);
        $payload = wp_json_encode(["data" => $klaviyo_data]);

        $created_profile = $klaviyo_api->klaviyo_update_profile($formSettings, $payload);
        //if($created_profile)
        return $created_profile;
    }


    //We prepare the subscription for adding to the Klaviyo list
    function prepareEmailSubscription($formSettings, $klaviyo_subscribe_email_model, $profile_response) {
        global $klaviyo_api;

        // Extract profile ID and email from the response
        $profile_id = $profile_response['data']['id'];
        $email = $profile_response['data']['attributes']['email'];

        // Initialize the subscription data array with the provided model
        $subscription_data = $klaviyo_subscribe_email_model;

        // Set the profile ID and email in the subscription model
        $subscription_data['data']['attributes']['profiles']['data'][0]['id'] = $profile_id;
        $subscription_data['data']['attributes']['profiles']['data'][0]['attributes']['email'] = $email;
        
        // Set the custom source and list ID
        if(!empty($formSettings['formCustomSource'])){
            $subscription_data['data']['attributes']['custom_source'] = $formSettings['formCustomSource'];
        }
        $subscription_data['data']['relationships']['list']['data']['id'] = $formSettings['selectedList'];

        // Prepare the payload for the API request
        $payload = wp_json_encode($subscription_data);

        $email_subscribed = $klaviyo_api->klaviyo_subscribe_email($formSettings, $payload );

        return $email_subscribed;
    }


    // Remove empty fields (recursively)
    function array_filter_recursive($input) {
        foreach ($input as &$value) {
            if (is_array($value)) {
                $value = array_filter_recursive($value);
            }
        }
        return array_filter($input);
    }

    if ( ! function_exists('klcf_write_log')) {
        function klcf_write_log ( $log )  {
            if ( is_array( $log ) || is_object( $log ) ) {
                error_log( print_r( $log, true ) );
            } else {
                error_log( $log );
            }
        }
    }