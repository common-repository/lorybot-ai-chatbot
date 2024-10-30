<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Handle the display of the chatbot interface on the front-end
 */

function lorybot_display() {
    $options = get_option('lorybot_options');

    // Debugging output
    error_log('chat_display: ' . $options['chat_display']);

    if (isset($options['chat_enabled']) && $options['chat_enabled'] === 'on') {
        if (isset($options['chat_display']) && !empty($options['chat_display'])) {
            echo wp_kses_post($options['chat_display']); // Render HTML safely
        } else {
            // Get the absolute path to the chat-html.php file
            $chat_html_path = plugin_dir_path(__FILE__) . 'settings/chat-html.php';

            // Include the file using the absolute path
            include($chat_html_path);
        }
    }
}

add_action('wp_footer', 'lorybot_display');

function lorybot_custom_kses_allowed_html($tags) {
    $tags['input'] = array(
        'type' => true,
        'id' => true,
        'name' => true,
        'value' => true,
        'class' => true,
        'style' => true
    );

    return $tags;
}

add_filter('wp_kses_allowed_html', 'lorybot_custom_kses_allowed_html');