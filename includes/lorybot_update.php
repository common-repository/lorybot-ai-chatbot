<?php
defined('ABSPATH') or exit;

function lorybot_update_settings() {
    $current_version = get_option('lorybot_version', '1.2'); // Default to version 1.0 if not set
    $new_version = '1.3.2'; // The new version of your plugin

    // if current version is empty
    if (empty($current_version)) {
            update_option('lorybot_version', '1.2');
            return;
    }

    if (version_compare($current_version, $new_version, '<')) {

        error_log('Updating LoryBot from ' . $current_version . ' to ' . $new_version);

        // Assume you've added new settings or need to update old settings
        $options = get_option('lorybot_options');
        $file_path = plugin_dir_path(__FILE__) . 'settings/chat-html.php';
        if (file_exists($file_path)) {
            $file_contents = file_get_contents($file_path);
            $options['chat_display'] = $file_contents; // Store the content of the file
        } else {
            error_log('File not found: ' . $file_path);
        }

        update_option('lorybot_options', $options);
        update_option('lorybot_version', $new_version); // Update the stored version to the new version

    }
}

add_action('upgrader_process_complete', 'lorybot_update_settings', 10, 2);