<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 

/**
 * This script handles the enqueueing of styles and scripts for the LoryBot plugin.
 * It includes the main styles and scripts for the plugin's front-end.
 */

/**
 * Enqueues styles and scripts for the LoryBot plugin's front-end.
 */
function lorybot_enqueue_frontend_scripts() {
    
    // Enqueue Google Material Symbols fonts
    wp_enqueue_style('material-symbols-outlined', 'https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0');
    wp_enqueue_style('material-symbols-rounded', 'https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@48,400,1,0');

    // Enqueue the chatbot's main JavaScript, making sure jQuery is loaded as a dependency
    wp_enqueue_script('lorybot-script', plugin_dir_url(__FILE__) . '../assets/js/script.js', ['jquery'], '1.3.5', true);

    // Enqueue the chatbot's main stylesheet
    wp_enqueue_style('lorybot-style', plugin_dir_url(__FILE__) . '../assets/css/style.css', [], '1.3.5');

    // Localize the script with data from PHP
    lorybot_localize_frontend_script();
}

/**
 * Localizes the front-end script with PHP variables.
 */
function lorybot_localize_frontend_script() {
    $lorybot_options = get_option('lorybot_options');

    // Provide default values if settings are not set
    $main_color = isset($lorybot_options['main_color']) ? $lorybot_options['main_color'] : '#0e456c';
    $background_color = isset($lorybot_options['background_color']) ? $lorybot_options['background_color'] : '#ffffff';
    $title_color = isset($lorybot_options['title_color']) ? $lorybot_options['title_color'] : '#ffffff';

    wp_localize_script('lorybot-script', 'chatbot_vars', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'plugin_url' => plugin_dir_url(dirname(__FILE__)),
        'prompt' => $lorybot_options['prompt'] ?? '',
        'client_id' => lorybot_get_main_domain(),
        'main_color' => $main_color,
        'background_color' => $background_color,
        'title_color' => $title_color,
        'chat_display' => $lorybot_options['chat_display'] ?? '',
        'custom_id' => $lorybot_options['custom_id'] ?? '',
        'server_url' => get_option('lorybot_server_url'),
    ]);
}

/**
 * Enqueues styles for the LoryBot plugin's admin settings page.
 *
 * @param string $hook_suffix The current admin page's hook suffix.
 */
function lorybot_enqueue_admin_styles($hook_suffix) {
    if ($hook_suffix === 'settings_page_lorybot-settings') {
        // Ensure the path is correct for your admin-style.css file
        wp_enqueue_style('lorybot-admin-style', plugin_dir_url(__FILE__) . '../assets/css/admin-style.css');
    }
}
add_action('admin_enqueue_scripts', 'lorybot_enqueue_admin_styles');


// Hook into WordPress to enqueue scripts and styles for the front-end
add_action('wp_enqueue_scripts', 'lorybot_enqueue_frontend_scripts');
?>
