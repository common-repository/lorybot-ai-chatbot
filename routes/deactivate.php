<?php

namespace Lorybot\Deactivate;

/**
 * Function to be executed when the plugin is deactivated.
 */
function lorybot_deactivate() {
    $lorybot_server_url = get_option('lorybot_server_url');
    $options = get_option('lorybot_options');
    $custom_id = isset($options['custom_id']) ? $options['custom_id'] : '';

    // Prepare data for the HTTP POST request
    $json = [
        'domain'    => lorybot_get_main_domain(),
        'custom_id' => $custom_id,
    ];

    // HTTP request arguments
    $args = [
        'method'    => 'POST',
        'headers'   => [
            'Content-Type'      => 'application/json',
            'LORYBOT-API-KEY'   => $custom_id, // Include the API key in the request header
        ],
        'body'      => wp_json_encode($json),
        'sslverify' => false,
        'timeout'   => 60,
    ];

    // Remove the lorybot_options as the plugin is being deactivated
    delete_option('lorybot_options');

    // Send POST request and handle the response
    $response = wp_remote_post($lorybot_server_url . "deactivate", $args);
    lorybot_handleDeactivationResponse($response);
}

/**
 * Handles the response from the HTTP request.
 *
 * @param WP_Error|array $response The response or WP_Error on failure.
 */
function lorybot_handleDeactivationResponse($response) {
    if (is_wp_error($response)) {
        // Safely escape and output the error message
        echo "Something went wrong: " . esc_html($response->get_error_message());
    } else {
        echo 'POST request sent successfully!';
    }
}

?>
