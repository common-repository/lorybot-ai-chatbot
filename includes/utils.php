<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 

// Start output buffering
ob_start();


/**
 * Validates a domain name.
 *
 * @param string $domain The domain name to validate.
 * @return bool True if valid, false otherwise.
 */
function lorybot_isValidDomain($domain) {
    return (filter_var($domain, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME) !== false);
}

/**
 * Retrieves the main domain from the HTTP_HOST server variable.
 *
 * @return string|null The main domain or null if HTTP_HOST is not set or invalid.
 */
function lorybot_get_main_domain() {
    if (isset($_SERVER['HTTP_HOST'])) {
        $host = sanitize_text_field(wp_unslash($_SERVER['HTTP_HOST']));

        if (!lorybot_isValidDomain($host)) {
            return null;
        }

        $hostParts = explode('.', $host);
        return count($hostParts) > 1 ? implode('.', array_slice($hostParts, -2)) : $host;
    } else {
        return null;
    }
}

/**
 * Redirects to the LoryBot settings page upon plugin activation.
 */
function lorybot_redirect() {
    if (get_option('lorybot_do_activation_redirect', false)) {
        delete_option('lorybot_do_activation_redirect');
        wp_redirect(admin_url('options-general.php?page=lorybot-settings'));
        exit;
    }
}
add_action('admin_init', 'lorybot_redirect');

/**
 * Generates a universally unique identifier (UUID).
 *
 * @return string The generated UUID.
 */
function lorybot_generate_uuid() {
    return sprintf(
        '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        wp_rand(0, 0xffff), wp_rand(0, 0xffff),
        wp_rand(0, 0xffff),
        wp_rand(0, 0x0fff) | 0x4000, // Set the version to 4 (randomly generated UUID)
        wp_rand(0, 0x3fff) | 0x8000, // Set the variant to DCE 1.1, ISO/IEC 11578:1996
        wp_rand(0, 0xffff), wp_rand(0, 0xffff), wp_rand(0, 0xffff)
    );
}

/**
 * Enqueues the WordPress color picker on the LoryBot settings page.
 *
 * @param string $hook_suffix The current admin page's hook suffix.
 */
function lorybot_enqueue_color_picker($hook_suffix) {
    if ($hook_suffix === 'settings_page_lorybot-settings') {
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('wp-color-picker');
    }
}
add_action('admin_enqueue_scripts', 'lorybot_enqueue_color_picker');

/**
 * Ends output buffering and flushes the output buffer.
 */
function lorybot_end_output_buffering() {
    if (ob_get_length()) {
        ob_end_flush();
    }
}
add_action('shutdown', 'lorybot_end_output_buffering');
