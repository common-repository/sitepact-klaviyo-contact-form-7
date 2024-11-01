<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class Klaviyo_API {
    /*
     * Get Klaviyo subscriber lists
     */
    public function get_lists($api_token) {


        if (!$api_token) {
            return array();
        }

        $lists = array();
        $next_page = KLCF_API_BASE_URL."/lists/";

        while ($next_page) {
            $args = array(
                'headers' => array(
                    'Accept' => 'application/json',
                    'Authorization' => 'Klaviyo-API-Key ' . $api_token,
                    'revision' => '2024-02-15'
                )
            );

            $response = wp_remote_get($next_page, $args);

            if (is_wp_error($response)) {
                return array("error" => "There was a server error. Please check your configuration or proceed with troubleshooting.");
            }

            $body = wp_remote_retrieve_body($response);
            $data = json_decode($body, true);

            //if there is an error
            if(isset($data['errors'])){
               return array("error" => $data['errors'][0]['detail']);
            }

            // Append the lists from this page to the $lists array
            foreach ($data['data'] as $list) {
                $lists[sanitize_text_field($list['id'])] = sanitize_text_field($list['attributes']['name']);
            }

            // Check if there's a next page
            $next_page = isset($data['links']['next']) ? $data['links']['next'] : null;
        }
        $lists["no-selection"] = "No Selection (Disable Integration)";
        return $lists;
    }


    //Klaviyo Create Profile
    public function klaviyo_create_profile($formSettings, $payload){
        // Prepare the request headers
        $headers = [
            'Authorization' => 'Klaviyo-API-Key ' . $formSettings['apiKey'],
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'revision' => '2024-05-15'
        ];

        // Use wp_remote_post to send the request
        $response = wp_remote_post(KLCF_API_BASE_URL.'/profiles/', [
            'method'    => 'POST',
            'body'      => $payload,
            'headers'   => $headers
        ]);

        // Check the response
        if (is_wp_error($response)) {
            if(!empty($formSettings['formLoggingStatus'])){
                $this->debug_log_responses($formSettings['formID'], $payload, $response, '500', 'A website error has ocurred.');
            }
            
            return [
                'error' => true,
                'response' => "A website error has ocurred."
            ];
        } else {
            $status_code = wp_remote_retrieve_response_code($response);
            $response_body = wp_remote_retrieve_body($response);

            if ($status_code == 201) {
                //log response if logging enabled
                if(!empty($formSettings['formLoggingStatus'])){
                    $this->debug_log_responses($formSettings['formID'], $payload, $response, $status_code, $response_body);
                }
                 return [
                    'error' => false,
                    'response' => json_decode($response_body, true)
                ];
            } else {
                //log response if logging enabled
                if(!empty($formSettings['formLoggingStatus'])){
                    $this->debug_log_responses($formSettings['formID'], $payload, $response, $status_code, $response_body);
                }
                return [
                    'error' => true,
                    'response' => json_decode($response_body, true)
                ];
            }
        }
    }



    //Klaviyo Update Profile
    public function klaviyo_update_profile($formSettings, $payload){
        // Prepare the request headers
        $headers = [
            'Authorization' => 'Klaviyo-API-Key ' . $formSettings['apiKey'],
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'revision' => '2024-05-15'
        ];

        // Use wp_remote_post to send the request
        $response = wp_remote_post(KLCF_API_BASE_URL.'/profile-import/', [
            'method'    => 'POST',
            'body'      => $payload,
            'headers'   => $headers
        ]);

        // Check the response
        if (is_wp_error($response)) {
            if(!empty($formSettings['formLoggingStatus'])){
                $this->debug_log_responses($formSettings['formID'], $payload, $response, '500', 'A website error has ocurred.');
            }
            
            return [
                'error' => true,
                'response' => "A website error has ocurred."
            ];
        } else {
            $status_code = wp_remote_retrieve_response_code($response);
            $response_body = wp_remote_retrieve_body($response);

            if ($status_code == 201) {
                //log response if logging enabled
                if(!empty($formSettings['formLoggingStatus'])){
                    $this->debug_log_responses($formSettings['formID'], $payload, $response, $status_code, $response_body);
                }
                 return [
                    'error' => false,
                    'response' => json_decode($response_body, true)
                ];
            } else {
                //log response if logging enabled
                if(!empty($formSettings['formLoggingStatus'])){
                    $this->debug_log_responses($formSettings['formID'], $payload, $response, $status_code, $response_body);
                }
                return [
                    'error' => true,
                    'response' => json_decode($response_body, true)
                ];
            }
        }
    }


    //Klaviyo Subscribe Profile to List
    public function klaviyo_subscribe_email($formSettings, $payload){

        // Prepare the request headers
        $headers = [
            'Authorization' => 'Klaviyo-API-Key ' . $formSettings['apiKey'],
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'revision' => '2024-05-15'
        ];

        // Use wp_remote_post to send the subscription request
        $response = wp_remote_post(KLCF_API_BASE_URL.'/profile-subscription-bulk-create-jobs/', [
            'method'    => 'POST',
            'body'      => $payload,
            'headers'   => $headers
        ]);

        // Check the response
        if (is_wp_error($response)) {
            if(!empty($formSettings['formLoggingStatus'])){
                $this->debug_log_responses($formSettings['formID'], $payload, $response, '500', $response->get_error_message());
            }
            return [
                'error' => true,
                'response' => $response->get_error_message()
            ];

        } else {
            $status_code = wp_remote_retrieve_response_code($response);
            $response_body = wp_remote_retrieve_body($response);

            if ($status_code == 202) {
                if(!empty($formSettings['formLoggingStatus'])){
                    $this->debug_log_responses($formSettings['formID'], $payload, $response, $status_code, $response_body);
                }
                return [
                    'error' => false,
                    'response' => json_decode($response_body, true)
                ];
            } else {
                 //log response if logging enabled
                if(!empty($formSettings['formLoggingStatus'])){
                    $this->debug_log_responses($formSettings['formID'], $payload, $response, $status_code, $response_body);
                }
                return [
                    'error' => true,
                    'status' => $status_code,
                    'response' => $response_body
                ];
            }
        }
    }


    //Klaviyo Subscribe Profile to List
    public function klaviyo_subscribe_phone($formSettings, $payload){

        // Prepare the request headers
        $headers = [
            'Authorization' => 'Klaviyo-API-Key ' . $formSettings['apiKey'],
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'revision' => '2024-05-15'
        ];

        // Use wp_remote_post to send the subscription request
        $response = wp_remote_post(KLCF_API_BASE_URL.'/profile-subscription-bulk-create-jobs/', [
            'method'    => 'POST',
            'body'      => $payload,
            'headers'   => $headers
        ]);

        // Check the response
        if (is_wp_error($response)) {
            if(!empty($formSettings['formLoggingStatus'])){
                $this->debug_log_responses($formSettings['formID'], $payload, $response, '500', $response->get_error_message());
            }
            return [
                'error' => true,
                'response' => $response->get_error_message()
            ];

        } else {
            $status_code = wp_remote_retrieve_response_code($response);
            $response_body = wp_remote_retrieve_body($response);

            if ($status_code == 202) {
                if(!empty($formSettings['formLoggingStatus'])){
                    $this->debug_log_responses($formSettings['formID'], $payload, $response, $status_code, $response_body);
                }
                return [
                    'error' => false,
                    'response' => json_decode($response_body, true)
                ];
            } else {
                 //log response if logging enabled
                if(!empty($formSettings['formLoggingStatus'])){
                    $this->debug_log_responses($formSettings['formID'], $payload, $response, $status_code, $response_body);
                }
                return [
                    'error' => true,
                    'status' => $status_code,
                    'response' => $response_body
                ];
            }
        }
    }

    //Klaviyo Subscribe Profile to List
    public function klaviyo_unsubscribe_email($formSettings, $payload){
        // Prepare the request headers
        $headers = [
            'Authorization' => 'Klaviyo-API-Key ' . $formSettings['apiKey'],
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'revision' => '2024-05-15'
        ];

        // Use wp_remote_post to send the subscription request
        $response = wp_remote_post(KLCF_API_BASE_URL.'/profile-subscription-bulk-delete-jobs/', [
            'method'    => 'POST',
            'body'      => $payload,
            'headers'   => $headers
        ]);

        // Check the response
        if (is_wp_error($response)) {
            if(!empty($formSettings['formLoggingStatus'])){
                $this->debug_log_responses($formSettings['formID'], $payload, $response, '500', $response->get_error_message());
            }
            return [
                'error' => true,
                'response' => $response->get_error_message()
            ];

        } else {
            $status_code = wp_remote_retrieve_response_code($response);
            $response_body = wp_remote_retrieve_body($response);

            if ($status_code == 202) {
                if(!empty($formSettings['formLoggingStatus'])){
                    $this->debug_log_responses($formSettings['formID'], $payload, $response, $status_code, $response_body);
                }
                return [
                    'error' => false,
                    'response' => json_decode($response_body, true)
                ];
            } else {
                 //log response if logging enabled
                if(!empty($formSettings['formLoggingStatus'])){
                    $this->debug_log_responses($formSettings['formID'], $payload, $response, $status_code, $response_body);
                }
                return [
                    'error' => true,
                    'status' => $status_code,
                    'response' => $response_body
                ];
            }
        }
    }

    public function debug_log_responses($formid, $request, $response, $response_code, $response_message){
        $log_data = [
            'post_id' => $formid,
            'request' => $request,
            'response' => $response,
            'response_code' => $response_code,
            'response_message' => $response_message,
        ];
        KLCF_add_log($log_data);
    }
}
