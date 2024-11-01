<?php 
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
?>

    <div style="float: right;"><a href="<?php echo esc_url('https://sitepact.com/contact-form-7-klaviyo-integration/'); ?>">Need Help?</a></div>
    <div class='klcf_container row'>
        <input type="hidden" name="KLCF_post" value="<?php echo intval($post_id); ?>">
        <div class="klcf-panel-header">
            <div style="align-items: center; display: flex;" class="klcf-col-50">
                <h2>Integration Status: <span id="status_append" class="<?php echo $klcf_form_status && $klcf_form_send_status ? 'active' : 'inactive'; ?>"><?php echo $klcf_form_status && $klcf_form_send_status ? 'Active' : 'Inactive'; ?></span></h2>
            </div>
            <div id="enable-integration-column" class="klcf-col-50 <?php echo !$api_key ? 'klcf-hidden' : ''; ?>" style="text-align:right;">
                <p>
                    <span class="enable-integration-wrap button">
                        <input type="checkbox" id="klcf_enable_integration" name="klcf_enable_integration" value="1" <?php checked($klcf_form_send_status); ?>> 
                        <span id="enable-integration-text"><?php echo $klcf_form_send_status ? "Disable Integration" : "Enable Integration" ?></span>
                    </span>
                </p>
            </div>
        </div>
        <div class="klcf-config-form-error klcf-hidden">
            <p>API key invalid or incorrect or invalid</p>
        </div>
        <div id="klcf-container">
        <form method="post" action="options.php">
            <table class="form-table" role="presentation">
                <tbody>
                    <tr>
                        <th scope="row">
                            <label>Klaviyo Private Key</label>
                        </th>
                        <td>
							<input type="text" class="large-text" name="KLCF_key" value="<?php echo esc_attr( $api_key ); ?>">
                        </td>
                        <td>
                            <a id="fetchKlaviyoLists" href="#" class="button-primary">Connect & Fetch Klaviyo Lists</a>
                            <span class="spinner klcf-connect-spinner"></span>
                        </td>
                    </tr>
                    <!--Select List Section-->
                     <tr id="select-klcf-list-section" class="klcf-integration-section <?php echo !$klcf_form_status ? 'klcf-hidden' : ''; ?>">
                        <th scope="row">
                            <label>Select Audience List</label>
                        </th>
                        <td>
                            <select name="KLCF_listing" id="select-klaviyo-list">
                                <?php if ($all_lists && is_array($all_lists)) : ?>
                                    <?php foreach ($all_lists as $listid => $listname) : ?>
                                        <option value="<?php echo esc_attr($listid); ?>" <?php selected($listid, $selected_list); ?>>
                                            <?php echo esc_html($listname); ?>
                                        </option>
                                    <?php endforeach; ?>
                            <?php else : ?>
								<option value=""><?php esc_html_e( 'No lists available', 'klaviyo-cf7' ); ?></option>
                            <?php endif; ?>
                            </select>
                        </td>
                        <td>
                            <a id="refreshKlaviyoLists" href="#" class="button">Refresh Klaviyo Lists</a>
                            <span class="spinner klcf-refresh-spinner"></span>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div id="map-fields-section" class="feature-section <?php echo !$klcf_form_status ? 'klcf-hidden' : ''; ?>">
                <table  class="form-table" id="map-fields-table" role="presentation">
                    <tbody>
                        <tr>
                            <th class="map-fields-title">Map Fields</th>
                        </tr>


                        <?php
                        if (!empty($regularFieldMapping)) {
                            $data_row_index = 1;
                            foreach ($regularFieldMapping as $klaviyo_key => $form_key) : ?>
                                <tr class="map-fields-data-row">
                                    <td>
                                        
										<select name="klcf_form_field[<?php echo esc_attr( $data_row_index ); ?>]" id="klcf_form_field[<?php echo esc_attr( $data_row_index ); ?>]">
                                            <?php foreach ($form_tags_list as $key => $name) : ?>
                                                <option value="<?php echo esc_attr($key); ?>" <?php selected($key, $form_key); ?>>
                                                    <?php echo esc_html($name); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td class="map-pointer">
                                        <span class="dashicons dashicons-arrow-right-alt"></span>
                                    </td>
                                    <td>
										<select name="klcf_klaviyo_param[<?php echo esc_attr($data_row_index); ?>]" id="klcf_klaviyo_param[<?php echo esc_attr($data_row_index); ?>]">
                                            <?php foreach ($klaviyo_regular_fields_model as $field) : ?>
                                                    <option value="<?php echo esc_attr($field['key']); ?>" <?php selected($field['key'], $klaviyo_key); ?>>
                                                        <?php echo esc_html($field['name']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                        </select>
                                    </td>
                                    
                                    <td> 
                                        <a href="#" class="remove-row button">Remove</a>
                                    </td>
                                </tr>
                                
                            <?php $data_row_index++;
                            endforeach;
                        } else { ?>
                            <tr class="map-fields-data-row">
                                    <td>
                                        <select name="klcf_form_field[1]" id="klcf_form_field[1]">
                                            <?php foreach ($form_tags_list as $key => $name) : ?>
                                                <option value="<?php echo esc_attr($key); ?>" <?php selected($key, reset($form_tags_list_emails)); ?>>
                                                    <?php echo esc_html($name); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td class="map-pointer">
                                        <span class="dashicons dashicons-arrow-right-alt"></span>
                                    </td>
                                    <td>
                                        
                                        <select name="klcf_klaviyo_param[1]" id="klcf_klaviyo_param[1]">
                                            <?php foreach ($klaviyo_regular_fields_model as $field) : ?>
                                                    <option value="<?php echo esc_attr($field['key']); ?>" <?php selected($field['key'], 'profile_email'); ?>>
                                                        <?php echo esc_html($field['name']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                        </select>
                                    </td>
                                    
                                    <td> 
                                        <a href="#" class="remove-row button">Remove</a>
                                    </td>
                                </tr>
                        <?php } ?>



                        <tr>
                            <td style="text-align:right" colspan="4">
                                <a id="addRegularField" href="#" class="button-primary">Add Field</a>
                            </td>
                            
                        </tr>
                    </tbody>
                </table>
            </div>


            <!--Enable GDPR-->
            <div id="gdpr-fields-section" class="feature-section <?php echo !$klcf_form_status ? 'klcf-hidden' : ''; ?>">
                <table  class="form-table" id="klcf_gdpr_settings_table" role="presentation">
                    <tbody>
                        <tr>
                            <th colspan="1" class="map-fields-title">GDPR Settings</th>
                            <th colspan="1" class="klcf-enable-feature" style="text-align:right;">
                                <span class="enable-feature-wrap"><input type="checkbox" id="klcf_enable_gdpr" name="klcf_enable_gdpr" value="1" <?php checked($gdpr_settings_status ); ?>> Enable GDPR</span>
                            </th>
                        </tr>
                        <tr class="map-fields-data-row <?php echo $gdpr_settings_status  ? "" : "klcf-hidden" ?>">
                            <td colspan="2">
                                <label for="klcf_gdpr_message">Enter GDPR message</label>
								<textarea style="width:100%;" class="klcf_form_textarea" name="klcf_gdpr_message" id="klcf_gdpr_message"><?php echo esc_textarea(trim($gdpr_fields_message)) ? esc_textarea(trim($gdpr_fields_message)) : ""; ?></textarea>
                            </td>
                        </tr>
                        <tr class="<?php echo $gdpr_settings_status ? "" : "klcf-hidden" ?>">
                            <td colspan="3">
                                <div>
                                    <ul style="list-style:circle; list-style-position: inside;">
                                        <li><strong>[klaviyo_gdpr KLCF_gdpr]</strong> will be added automatically above the submit button else you can add the tag manually in the form.</li>
                                        <li><span style="font-weight:900; color: red;">Important</span>: Form will not be submitted to Klaviyo if this feature is enabled AND the user does not tick the box when they submit the form.</li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>


            <!-- Custom fields section -->
            <div id="custom-map-fields-section" class="feature-section <?php echo !$klcf_form_status ? 'klcf-hidden' : ''; ?>">
                <table  class="form-table" id="custom-map-fields-table" role="presentation">
                    <tbody>
                        <tr>
                            <th colspan="2" class="map-fields-title">Map Custom Fields</th>
                            <th colspan="2" class="klcf-enable-feature" style="text-align:right;">
                                <span class="enable-feature-wrap"><input type="checkbox" id="klcf_enable_custom_fields" name="klcf_enable_custom_fields" value="1" checked > Enable Custom Fields</span>
                            </th>
                        </tr>
                            <tr class="map-custom-fields-data-row">
                                <td>
                                    <select name="klcf_custom_form_field[1]" id="klcf_custom_form_field[1]">
                                        <option value="business-email" selected="selected">business-email</option>
                                        <option value="personal-email">personal-email</option>
                                    </select>
                                </td>
                                <td class="map-pointer">
                                    <span class="dashicons dashicons-arrow-right-alt"></span>
                                </td>
                                <td>
                                    <input placeholder="custom_field" type="text" class="large-text" name="klcf_custom_klaviyo_param[1]" id="klcf_custom_klaviyo_param[1]" value="custom_phone_number" onkeypress="return event.charCode != 32">
                                </td>
                                
                                <td> 
                                    <a href="#" class="remove-row button">Remove</a>
                                </td>
                            </tr>
                        <tr>
                            <td style="text-align:right" colspan="4">
                                <a id="addCustomField" href="#" class="button-primary">Add Custom Field</a>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <a class="klcf-link-to-pro" href="<?php echo esc_url('https://sitepact.com/contact-form-7-klaviyo-integration'); ?>" target="_blank" title="Klaviyio Contact Form 7 Pro Options"><span class="pro-feature-title"><?php echo esc_html('PRO Feature'); ?></span><span class="pro-feature-btn-link button-primary"><?php echo esc_html('Learn More...'); ?></span></a>
            </div>

            <!-- SMS Subscription section -->
            <div id="custom-map-sms-field-section" class="feature-section">
                <table  class="form-table" id="custom-sms-fields-table" role="presentation">
                    <tbody>
                        <tr>
                            <th colspan="2" class="map-fields-title">Subscribe to SMS</th>
                            <th colspan="2" class="klcf-enable-feature" style="text-align:right;">
                                <span class="enable-feature-wrap"><input type="checkbox" id="klcf_enable_sms_subscription" name="klcf_enable_sms_subscription" value="1" checked> Enable SMS Subscription</span>
                            </th>
                        </tr>
                        <tr class="map-fields-data-row">
                            <td>
                                <label for="klcf_sms_form_field">Select Phone Field</label>
                                <select name="klcf_sms_form_field" id="klcf_sms_form_field">
                                    <option value="business-email" selected="selected">business-email</option>
                                    <option value="personal-email">personal-email</option>
                                </select>
                            </td>
                            <td class="map-pointer">
                                <span class="dashicons dashicons-arrow-right-alt"></span>
                            </td>
                            <td>
                                <label for="klcf_sms_lists">Select Klaviyo list</label>
                                <select name="klcf_sms_lists" id="klcf_sms_lists">
                                    <option value="S8x32k">SMS Subscribers</option>
                                    <option value="RYzAr8">RefreshList</option>
                                </select>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <a class="klcf-link-to-pro" href="<?php echo esc_url('https://sitepact.com/contact-form-7-klaviyo-integration'); ?>" target="_blank" title="Klaviyio Contact Form 7 Pro Options"><span class="pro-feature-title"><?php echo esc_html('PRO Feature'); ?></span><span class="pro-feature-btn-link button-primary"><?php echo esc_html('Learn More...'); ?></span></a>
            </div>


            <!-- Unsubscribe Settings -->
            <div id="unsubscribe-fields-section" class="feature-section">
                <table  class="form-table" id="unsubscribe-fields-table" role="presentation">
                    <tbody>
                        <tr>
                            <th colspan="2" class="map-fields-title">Unsubscribe Settings</th>
                            <th colspan="2" class="klcf-enable-feature" style="text-align:right;">
                                <span class="enable-feature-wrap"><input type="checkbox" id="klcf_enable_unsubscription" name="klcf_enable_unsubscription" value="1" checked> Enable Klaviyo Unsubscribe</span>
                            </th>
                        </tr>
                        <tr class="map-fields-data-row">
                            <td>
                                <label for="klcf_unsubscribe_field">Select Email Field</label>
                                <select name="klcf_unsubscribe_field" id="klcf_unsubscribe_field">
                                    <option value="business-email" selected="selected">business-email</option>
                                    <option value="personal-email">personal-email</option>
                                </select>
                            </td>
                            <td class="map-pointer">
                                <span class="dashicons dashicons-arrow-right-alt"></span>
                            </td>
                            <td>
                                <label for="klcf_unsubscribe_lists">Select Klaviyo list</label>
                                <select name="klcf_unsubscribe_lists" id="klcf_unsubscribe_lists">
                                    <option value="RYzAr8">Blog Subscribers</option>
                                    <option value="S8x32k">SMS Subscribers</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3">
                                <div>
                                    <ul style="list-style:circle; list-style-position: inside; margin: 0px;">
                                        <li>Select the email field and the list you want to unsubscribe this email from.</li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <a class="klcf-link-to-pro" href="<?php echo esc_url('https://sitepact.com/contact-form-7-klaviyo-integration'); ?>" target="_blank" title="Klaviyio Contact Form 7 Pro Options"><span class="pro-feature-title"><?php echo esc_html('PRO Feature'); ?></span><span class="pro-feature-btn-link button-primary"><?php echo esc_html('Learn More...'); ?></span></a>
            </div>

            <!--Additional Form Settings-->
            <div id="additional settings-fields-section" class="feature-section <?php echo !$klcf_form_status ? 'klcf-hidden' : ''; ?>">
                <table  class="form-table" id="klcf_additional_settings_table" role="presentation">
                    <tbody>
                        <tr>
                            <th colspan="1" class="map-fields-title">Additional Form Settings</th>
                            <th></th>
                        </tr>
                        <tr class="map-fields-data-row">
                            <td>
                                <label for="klcf_custom_source">Subscription Source (custom_source)</label>
                            </td>
                            <td>
								<input placeholder="Marketing" type="text" class="large-text" name="klcf_custom_source" id="klcf_custom_source" value="<?php echo esc_attr($klcf_form_custom_source); ?>">
                            </td>
                        </tr>
                        <tr class="map-fields-data-row">
                            <td>
                                <label for="klcf_form_tel_mask_status">Intl. Phone Formatting</label>
                            </td>
                            <td style="text-align:right;">
                                <span class="enable-feature-wrap"><input type="checkbox" id="klcf_form_tel_mask_status" name="klcf_form_tel_mask_status" value="1" <?php checked($klcf_form_tel_mask_status ); ?>> Enable Intl. Phone Formatting</span>
                            </td>
                        </tr>
                        <tr class="map-fields-data-row">
                            <td>
                                <label for="klcf_form_logging_status">Log Requests (For Debugging)</label>
                            </td>
                            <td style="text-align:right;">
                                <span class="enable-feature-wrap"><input type="checkbox" id="klcf_form_logging_status" name="klcf_form_logging_status" value="1" <?php checked($klcf_form_logging_status ); ?>> Enable Logging</span>
                            </td>
                        </tr>
                        
                        <tr class="map-fields-data-row">
                            <td colspan="2">
                                <div>
                                    <ul style="list-style:circle; list-style-position: inside;">
                                        <li>Subscription Source is a custom method detail or source to store on the consent records.</li>
                                        <li><span style="font-weight:900; color: red;">Important</span>: Enable Intl Phone formatting to ensure phone number formatting is entered correctly by users</li>
                                        <li><span style="font-weight:900; color: red;">Important</span>: Logging should only be enabled for debug purposes. It fills up the database quickly.</li>

                                    </ul>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </form>
        </div>
    </div> <!-- Close klcf_container div -->