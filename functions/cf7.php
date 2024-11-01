<?php
/**
 * Setup of the integration form processes
 *  * @since 1.0
 * KLCF_admin_after_additional_settings - form settings
 * KLCF_save_contact_form - Save contact form
 */
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

	//Get Models
	include(KLCF_KLAVIYO_CF7_DIR. "admin/models/models.php");
	
	add_action('wpcf7_save_contact_form', 'KLCF_save_contact_form',10,3);
	add_action('wpcf7_init', 'cf7_gdpr_shortcodes');
	add_action("wpcf7_mail_sent", "klcf_submission_activities");

	function KLCF_admin_after_additional_settings( $cf7 ) {

		$post_id = isset($_GET['post']) ? sanitize_text_field($_GET['post']) : '';

		if ( empty( $post_id ) ) {
			$active_error  = "<h3>Please Save Contact Form</h3>";
			$active_error .= "<p>Please save your contact form at least once before you can access the Klaviyo configuration section. Klaviyo Contact Form 7 Integration is not able to get form fields if you have never saved the form.</p>";
			echo wp_kses_post( $active_error ); // Escaping the output
			return;
		}

		$api_key = 	get_post_meta($post_id, "_KLCF_key", true); //Klaviyo API Key
		$all_lists = get_post_meta($post_id, "_KLCF_lists", true); //Array of lists
		$selected_list = get_post_meta($post_id, "_KLCF_selected_list", true); //The list the user selected
		$klcf_form_send_status = get_post_meta($post_id, "_KLCF_form_send_status", true); //Klaviyo API Key
		$klcf_form_status = $api_key ? true : false; //here we get form status based on whether an api key is saved

		//Get regular field mappings
		$regularFieldMapping = get_post_meta($post_id, "_KLCF_regular_fields_mappings", true);

		//GDPR feature
		$gdpr_settings_status = get_post_meta($post_id, "_KLCF_gdpr_status", true); // Get GDPR setting status
		$gdpr_fields_message = get_post_meta($post_id, "_KLCF_gdpr_message", true); // Get GDPR message.
		$gdpr_fields_message = $gdpr_fields_message ? $gdpr_fields_message : "I agree to receive updates about products or services via email.";

		//Here we get the form tags in an array so we can add them to select list
		$manager = WPCF7_FormTagsManager::get_instance();
		$form_tags = $manager->get_scanned_tags();
		$form_tags_list = getFormTagNames($form_tags, "all");
		$form_tags_list_phone = getFormTagNames($form_tags, "tel");
		$form_tags_list_emails = getFormTagNames($form_tags, "emails");

		$klcf_form_custom_source = get_post_meta($post_id, "_KLCF_form_custom_source", true);
		$klcf_form_logging_status = get_post_meta($post_id, "_KLCF_form_logging_status", true);
		$klcf_form_tel_mask_status = get_post_meta($post_id, "_KLCF_form_input_mask_status", true);


		$klaviyo_regular_fields_model = klaviyo_regular_fields_model();
		$klaviyo_create_profile_model = klaviyo_create_profile_model();

		//Here we get the template for our field mapping section
		include(KLCF_KLAVIYO_CF7_DIR.'admin/templates/integration-form.php');
	}

	function KLCF_save_contact_form( $cf7 ) {
		
		//Nonce check is done by contact form 7

		$post_id = isset($_POST['KLCF_post']) ? sanitize_text_field($_POST['KLCF_post']) : null;
		$integration_status = isset($_POST['klcf_enable_integration']) ? intval($_POST['klcf_enable_integration']) : 0;
		$api_key = isset($_POST['KLCF_key']) ? sanitize_text_field($_POST['KLCF_key']) : '';

		//Here we save the main klaviyo list
		if(isset($_POST['KLCF_listing'])){
			update_post_meta($post_id, "_KLCF_selected_list", sanitize_text_field($_POST['KLCF_listing']));
		}

		//Here we get the mappings for the regular fields
		if(isset($_POST['klcf_klaviyo_param']) && isset($_POST['klcf_form_field'])){
			$regularFieldMapping = mapFieldsArray($_POST['klcf_klaviyo_param'], $_POST['klcf_form_field']);
			update_post_meta($post_id, "_KLCF_regular_fields_mappings", $regularFieldMapping);
		}

		//Enable GDPR Feature
		$klcf_enable_gdpr = isset($_POST['klcf_enable_gdpr']) ? sanitize_text_field($_POST['klcf_enable_gdpr']) : '';
		if(!empty($klcf_enable_gdpr)){
			update_post_meta($post_id, "_KLCF_gdpr_status", $klcf_enable_gdpr);
			update_post_meta($post_id, "_KLCF_gdpr_message", sanitize_textarea_field($_POST['klcf_gdpr_message']));
		}else{
			update_post_meta($post_id, "_KLCF_gdpr_status", 0);
		}

		//Save additional settings fields
		if(isset($_POST['klcf_custom_source'])){
			update_post_meta($post_id, "_KLCF_form_custom_source", sanitize_text_field($_POST['klcf_custom_source']));
		}

		$klcf_enable_logging = isset($_POST['klcf_form_logging_status']) ? sanitize_text_field($_POST['klcf_form_logging_status']) : '';
		if(!empty($klcf_enable_logging)){
			update_post_meta($post_id, "_KLCF_form_logging_status", $klcf_enable_logging);
		}else{
			update_post_meta($post_id, "_KLCF_form_logging_status", 0);
		}

		$klcf_enable_input_mask = isset($_POST['klcf_form_tel_mask_status']) ? sanitize_text_field($_POST['klcf_form_tel_mask_status']) : '';
		if(!empty($klcf_enable_input_mask)){
			update_post_meta($post_id, "_KLCF_form_input_mask_status", $klcf_enable_input_mask);
		}else{
			update_post_meta($post_id, "_KLCF_form_input_mask_status", 0);
		}

		//Here we add the GDPR code to the form on both front and back end
		if(!empty($klcf_enable_gdpr) && !empty($api_key) && $integration_status){
			$form = add_GDPR_field_to_form($cf7->get_properties(), 1);
			$cf7->set_properties(['form' => $form]);
		}else {
			$form = add_GDPR_field_to_form($cf7->get_properties(), 0);
			$cf7->set_properties(['form' => $form]);
		}

		return $cf7;
	}

	function add_GDPR_field_to_form($form,$enable)
	{
		if(!empty($form)){
			$form = $form['form'];
			if(!strpos($form,'[klaviyo_gdpr KLCF_gdpr]') !== false && $enable){
				$form = str_replace("[submit", "[klaviyo_gdpr KLCF_gdpr]\n[submit", $form);

			}else{
				if(!$enable){
					//$form = str_replace("[klaviyo_gdpr KLCF_gdpr]", "", $form);
					$form = preg_replace("/\s*\[klaviyo_gdpr KLCF_gdpr\]\s*/", "\n", $form);

				}
			}
		}
		return $form;
	}


	function cf7_gdpr_shortcodes(){
	    wpcf7_add_form_tag( 'klaviyo_gdpr', 'gdpr_handler' ,true);
	}

	/*function KLCF_validation_filter($result,$tag){
		if(empty($_POST['KLCF_gdpr'])){
			$tag = new WPCF7_FormTag($tag);
			//print_rr($tag);
			$result->invalidate($tag, "GDPR Is Mendatory.");

		}
		return $result;
	}*/

	function gdpr_handler(){
		$manager = WPCF7_ContactForm::get_current();
		$id = $manager->id();
		$gdpr_message = get_post_meta($id, "_KLCF_gdpr_message", true);

	    return "<input type='checkbox' id='KLCF_gdpr' name='KLCF_gdpr' value='1'>
		<label for='KLCF_gdpr'> ".$gdpr_message."</label>";
	}



	function klcf_submission_activities( $contact_form ){

		// to get form id
		$form_id = $contact_form->id();
		$form_title = $contact_form->title();

		$api_key = 	get_post_meta($form_id, "_KLCF_key", true); //Klaviyo API Key
		$klcf_form_send_status = get_post_meta($form_id, "_KLCF_form_send_status", true); //Klaviyo API Key
		$klcf_form_status = $api_key ? true : false; //here we get form status based on whether an api key is saved
		$gdpr_settings_status = get_post_meta($form_id, "_KLCF_gdpr_status", true);

		//First we check if form is enabled
		if($klcf_form_status && $klcf_form_send_status){
			// to get submission data
			$submission = WPCF7_Submission::get_instance(); 
			$posted_data = $submission->get_posted_data();

			//If gdpr is enabled but the user does not tick the privacy box
			if($gdpr_settings_status && empty($posted_data['KLCF_gdpr'])){
				return;
			}
			
			$selected_list = get_post_meta($form_id, "_KLCF_selected_list", true);
			$regularFieldMapping = get_post_meta($form_id, "_KLCF_regular_fields_mappings", true);

			$klaviyo_regular_fields_model = klaviyo_regular_fields_model();
			$klaviyo_create_profile_model = klaviyo_create_profile_model();
			$klaviyo_subscribe_email_model = klaviyo_subscribe_email_model();

			//Additional Settings
			$klcf_form_custom_source = get_post_meta($form_id, "_KLCF_form_custom_source", true);
			$klcf_form_logging_status = get_post_meta($form_id, "_KLCF_form_logging_status", true);
			

			$formSettings = [
				"formID" => $form_id,
				"formTitle" => $form_title,
				"apiKey" => $api_key,
				"selectedList" => $selected_list,
				"formCustomSource" => $klcf_form_custom_source ? $klcf_form_custom_source : "",
				"formLoggingStatus" => $klcf_form_logging_status ? true : false,
			];

			if(!empty($regularFieldMapping)){
				//Here we attempt to create profile
				$profile = prepareKlaviyoCreateProfile($formSettings, $klaviyo_create_profile_model, $posted_data, $regularFieldMapping);
				
				//Here we now check the previous result and prepare to subscribe the user
				if(empty($profile['error'])){
					//If email is available then subscribe email
					if(!empty($profile['response']['data']['attributes']['email'])){
						$subscription = prepareEmailSubscription($formSettings, $klaviyo_subscribe_email_model, $profile['response']);
					}
				}else{
					if($profile['response']['errors'][0]['code'] === "duplicate_profile"){
						$profile_ID = $profile['response']['errors'][0]['meta']['duplicate_profile_id'];
						$profile = prepareKlaviyoUpdateProfile($formSettings, $klaviyo_create_profile_model, $posted_data, $regularFieldMapping, $profile_ID);

						//Here we now check the previous result and prepare to subscribe the user
						if(!empty($profile['response']['data']['attributes'])){
							//If email is available then subscribe email
							if(!empty($profile['response']['data']['attributes']['email'])){
								$subscription = prepareEmailSubscription($formSettings, $klaviyo_subscribe_email_model, $profile['response']);
							}			
						}
					}
				}
			}
		}
	}


	

	function getFormTagNames($formTags, $type) {
		// Initialize arrays to store the names
		$emailNames = [];
		$phoneNames = [];
		$otherNames = [];

		// Loop through each WPCF7_FormTag object in the array
		foreach ($formTags as $tag) {
			// Check if the type is 'submit'
			if ($tag->type === 'submit') {
				continue;
			}

			// Prepare the key-value pair
			$name = $tag->name;

			// Add the name to the appropriate array based on the type
			if ($tag->basetype === 'email') {
				$emailNames[$name] = $name;
			}else if ($tag->basetype === 'tel') {
				$phoneNames[$name] = $name;
			}
			else {
				$otherNames[$name] = $name;
			}
		}
		if($type === "emails"){
			return $emailNames;
		}else if($type === "tel"){
			return $phoneNames;
		}else{
			// Merge the email names with other names, ensuring emails come first
			return array_merge($emailNames, $phoneNames, $otherNames);
		}
	}

	function mapFieldsArray($klcf_klaviyo_param, $klcf_form_field){
		// Initialize the combined array
		$combined_array = [];
		// Loop through the first array and map its values to the values of the second array using their indexes
		foreach ($klcf_klaviyo_param as $index => $klaviyo_value) {
			if (isset($klcf_form_field[$index])) {
				$combined_array[sanitize_text_field($klaviyo_value)] = sanitize_text_field($klcf_form_field[$index]);
			}
		}
		// Output the combined array for verification
		return $combined_array;
	}

	//We get the form id to get the post meta and determine if we need to include input mask
	add_filter('do_shortcode_tag', 'enqueue_input_mask_script', 10, 3);
	function enqueue_input_mask_script($output, $tag, $attr) {
		// Check if the shortcode is for Contact Form 7
		if ('contact-form-7' != $tag) {
			return $output;
		}

		// Check if the output contains the form ID
		if (preg_match('/name="_wpcf7" value="(\d+)"/', $output, $matches)) {
			$form_id = $matches[1];
			//klcf_write_log($form_id);

			// Retrieve the form post meta
			$form_input_mask_status = get_post_meta($form_id, '_KLCF_form_input_mask_status', true);

			// Check if the post meta exists and enqueue the script if it does
			if ($form_input_mask_status) {
				wp_enqueue_script('klcf-input-mask-script');
			}
		}
		// Return the original output
		return $output;
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