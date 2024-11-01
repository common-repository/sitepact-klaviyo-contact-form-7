jQuery(document).ready(function ($) {
  $("#klcf_enable_integration").change(function () {
    var postId = $("#post_ID").val(); //Assumes you're in a post edit screen
    var integrationStatus;
    if (this.checked) {
      integrationStatus = 1;
    } else {
      integrationStatus = 0;
    }

    $.ajax({
      url: main_klcf_script_ajax_object.ajax_url,
      type: "POST",
      data: {
        action: "change_integration_status",
        post_id: postId,
        integration_status: integrationStatus,
        nonce: main_klcf_script_ajax_object.nonce,
      },
      success: function (response) {
        if (integrationStatus) {
          $("#status_append")
            .removeClass("inactive")
            .addClass("active")
            .text("Active");
        } else {
          $("#status_append")
            .removeClass("active")
            .addClass("inactive")
            .text("Inactive");
        }
      },
      error: function (response) {
        //console.log(response);
        showIntegrationFormError(response.data);
      },
    });
  });

  $("#fetchKlaviyoLists, #refreshKlaviyoLists").on("click", function (e) {
    e.preventDefault();
    if (this.id === "refreshKlaviyoLists") {
      $(".klcf-refresh-spinner").css("visibility", "visible");
    }

    hideIntegrationFormError();

    var apiKey = $('input[name="KLCF_key"]').val();
    var postId = $("#post_ID").val(); //Assumes you're in a post edit screen

    // Show the spinner
    $(".klcf-connect-spinner").css("visibility", "visible");

    $.ajax({
      url: main_klcf_script_ajax_object.ajax_url,
      type: "POST",
      data: {
        action: "fetch_klaviyo_lists",
        api_key: apiKey,
        post_id: postId,
        nonce: main_klcf_script_ajax_object.nonce,
      },
      success: function (response) {
        // Hide the spinner
        $(".klcf-connect-spinner, .klcf-refresh-spinner").css(
          "visibility",
          "hidden"
        );
        if (response.success) {
          $(
            ".klcf-integration-section, #enable-integration-column, .feature-section"
          ).removeClass("klcf-hidden"); //show audience and integration checkbox sections
          $("#status_append")
            .removeClass("inactive")
            .addClass("active")
            .text("Active"); //update status indicator styling and text

          $("#klcf_enable_integration").prop("checked", true);

          //console.log(response.data);

          // Clear existing options
          $(
            "#select-klaviyo-list, #klcf_sms_lists, #klcf_unsubscribe_lists"
          ).empty();
          /**Here we append the new lists to the select */
          $.each(response.data, function (key, value) {
            // Create a new option element
            const $option = $("<option></option>")
              .attr("value", key)
              .text(value);
            // Append the option to the select element
            $(
              "#select-klaviyo-list, #klcf_sms_lists, #klcf_unsubscribe_lists"
            ).append($option);
          });

          //alert("Lists fetched successfully!");
          // You can update the DOM or do further processing here
        } else {
          $(".klcf-integration-section").addClass("klcf-hidden"); //hide audience section
          $("#status_append")
            .removeClass("active")
            .addClass("inactive")
            .text("Inactive"); //update status indicator styling and text when the correct API key become a wrong one or some related error
          showIntegrationFormError(response.data);
        }
      },
      error: function () {
        // Hide the spinner
        $(".klcf-connect-spinner, .klcf-refresh-spinner").css(
          "visibility",
          "hidden"
        );
        showIntegrationFormError("An error occurred while fetching the lists.");
      },
    });
  });

  function showIntegrationFormError(message) {
    $(".klcf-config-form-error")
      .find("p")
      .text(message)
      .end()
      .removeClass("klcf-hidden");
  }

  function hideIntegrationFormError() {
    if ($(".klcf-config-form-error").is(":visible")) {
      $(".klcf-config-form-error").addClass("klcf-hidden");
    }
  }

  /***
   * Add/Remove new row in add regular fields
   * */
  $("#addRegularField").on("click", function (e) {
    e.preventDefault();

    // Clone the last data row
    let newRow = $("#map-fields-section .map-fields-data-row:last").clone();

    //console.log(newRow);
    // Determine the current highest row index from the last row in the table
    let lastSelectName = $(
      "#map-fields-section .map-fields-data-row:last select:first"
    ).attr("name");

    let rowIndex = parseInt(lastSelectName.match(/\d+/)) + 1;

    // Update the name and id attributes of the select elements
    newRow.find("select").each(function () {
      let name = $(this).attr("name");
      let id = $(this).attr("id");

      // Extract base name and increment the index
      let newName = name.replace(/\[\d+\]/, "[" + rowIndex + "]");
      let newId = id.replace(/\d+/, rowIndex);

      // Set new name and id
      $(this).attr("name", newName);
      $(this).attr("id", newId);
    });

    // Append the new row before the last row
    newRow.insertBefore($("#map-fields-table tr:last"));
    newRow.removeClass("klcf-hidden");
  });

  // Remove row
  $("#map-fields-table").on("click", ".remove-row", function (e) {
    e.preventDefault();
    $(this).closest(".map-fields-data-row").remove();
  });


  /**
   * Hide Show Enable GDPR Section
   */
  $("#klcf_enable_gdpr").change(function () {
    if (this.checked) {
      $("#klcf_gdpr_settings_table tr").removeClass("klcf-hidden");
    } else {
      $("#klcf_gdpr_settings_table tr:not(:first)").addClass("klcf-hidden");
    }
  });
});
