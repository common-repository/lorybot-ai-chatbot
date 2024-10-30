<?php

namespace Lorybot\Activate;

/**
 * Function to be executed when the plugin is activated.
 */
function lorybot_activate() {
    global $is_lorybot_activating;

    // Set a global flag to indicate activation is in progress
    $is_lorybot_activating = true;

    // Clear existing options before setting new ones
    delete_option('lorybot_options');

    $customID = lorybot_generate_uuid();
    // Store custom_id in lorybot_options array
    update_option('lorybot_options', ['custom_id' => $customID]);

    // Prepare data for the HTTP POST request
    $json = [
        'domain'    => lorybot_get_main_domain(),
        'custom_id' => $customID,
    ];

    // HTTP request arguments
    $args = [
        'method'    => 'POST',
        'headers'   => ['Content-Type' => 'application/json'],
        'body'      => wp_json_encode($json),
        'sslverify' => false,
        'timeout'   => 60,
    ];

    // Send POST request and handle the response
    $response = wp_remote_post(get_option('lorybot_server_url') . "activate", $args);
    lorybot_handleActivationResponse($response);

    // Ensure 'lorybot_do_activation_redirect' option is set
    add_option('lorybot_do_activation_redirect', true);

    // Reset the global flag after activation is complete
    $is_lorybot_activating = false;
}

/**
 * Handles the response from the HTTP request.
 *
 * @param WP_Error|array $response The response or WP_Error on failure.
 */
function lorybot_handleActivationResponse($response) {
    if (is_wp_error($response)) {
        // Log error message
        $error_message = $response->get_error_message();
    } else {
        // Convert the response to a readable string and log it
        $response_string = print_r($response, true);
    }
}

?>
