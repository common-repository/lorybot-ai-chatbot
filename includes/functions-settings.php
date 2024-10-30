<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 

include_once 'settings/callbacks.php';
include_once 'settings/sanitaze.php';

/**
 * Registers the settings page for LoryBot.
 */
function lorybot_register_settings_page() {
    add_options_page('LoryBot Settings', 'LoryBot Settings', 'manage_options', 'lorybot-settings', 'lorybot_settings_page_content');
}
add_action('admin_menu', 'lorybot_register_settings_page');

/**
 * Initializes LoryBot settings by registering them and adding settings sections and fields.
 */
function lorybot_settings_init() {
    register_setting('lorybot_settings', 'lorybot_options', 'lorybot_sanitize_options');
    add_settings_section('lorybot_main_section', 'Main Settings', 'lorybot_section_callback', 'lorybot-settings');
    
    // Register fields
    include_once 'settings/fields.php';
}
add_action('admin_init', 'lorybot_settings_init');


/**
 * Outputs the content of the settings page.
 */
function lorybot_settings_page_content() {
    // Correctly retrieve the plugin directory URL and concatenate it with the relative path to the logo
    $logo_url = plugin_dir_url(__FILE__) . '../assets/images/lorybot-logo.png';
    ?>
    <div class="wrap lorybot-settings-wrap">
        <!-- Embed the logo using PHP echo for the URL -->
        <img src="<?php echo esc_url($logo_url); ?>" alt="LoryBot Logo" class="lorybot-logo">

        <h1>LoryBot Settings</h1>
        <!-- Settings form -->
        <form action="options.php" method="post">
            <?php
            // Output nonce, action, and option_page fields for a settings page
            settings_fields('lorybot_settings');
            // Output settings sections and their fields
            do_settings_sections('lorybot-settings');
            // Output save settings button
            submit_button('Save Settings');
            ?>
        </form>
    </div>
    <?php
}


/**
 * Handles the after-update logic for LoryBot settings.
 *
 * @param array $updated_values The new values updated in the settings.
 * @return bool True if the update was successful, false otherwise.
 */
function lorybot_function_after_update($updated_values) {    
    $options = get_option('lorybot_options');
    $custom_id = $options['custom_id'] ?? '';

    if (empty($custom_id)) {
        delete_option('lorybot_options');
        return false;
    }

    $json = lorybot_build_update_json($updated_values, $custom_id);
    return lorybot_send_update_request($json);
}

/**
 * Builds the JSON payload for the update request.
 *
 * @param array $values The new values to be updated.
 * @param string $custom_id The custom ID.
 * @return array The JSON payload.
 */
function lorybot_build_update_json($values, $custom_id) {
    return [
        'embedding' => $values['embedding'] ?? '',
        'prompt' => $values['prompt'] ?? '',
        'custom_id' => $custom_id,
    ];
}

/**
 * Sends an update request to the server.
 *
 * @param array $json The JSON payload for the update.
 * @return bool True if the request was successful, false otherwise.
 */
function lorybot_send_update_request($json) {
    $lorybot_server_url = get_option('lorybot_server_url');
    $response = wp_remote_post($lorybot_server_url . "settings", [
        'method'    => 'POST',
        'headers'   => [
            'Content-Type' => 'application/json',
            'LORYBOT-API-KEY' => $json['custom_id'],
        ],
        'body'      => wp_json_encode($json),
        'sslverify' => false,
        'timeout'   => 60
    ]);

    if (is_wp_error($response)) {
        return false;
    }

    $response_code = wp_remote_retrieve_response_code($response);
    $response_body = wp_remote_retrieve_body($response);

    return $response_code == 200;
}

/**
 * Callback function for the 'updated_option' action.
 * It triggers after an option update and handles custom logic for LoryBot settings.
 *
 * @param string $option_name Name of the updated option.
 * @param mixed $old_value The old option value.
 * @param mixed $value The new option value.
 */
function lorybot_option_updated($option_name, $old_value, $value) {

    if ($option_name === 'lorybot_options') {
        $update_success = lorybot_function_after_update($value);

        if (!$update_success) {
            add_settings_error(
                'lorybot_options',
                'lorybot_update_failed',
                'There was an error updating the settings.',
                'error'
            );
            update_option('lorybot_options', $old_value);
        }
    }
}
add_action('updated_option', 'lorybot_option_updated', 10, 3);

?>
