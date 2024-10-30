<?php
/**
 * Plugin Name: LoryBot | Advanced AI Chatbot
 * Plugin URI: https://www.lorybot.com
 * Description: Build better customer relationships and boost sales with LoryBot's AI chatbot. Using your unique business data, LoryBot provides personalized, helpful responses, creating meaningful conversations that keep visitors engaged. Optimize the return from your website traffic with tailored, individualized interactions powered by leading-edge AI technology.
 * Version: 1.3.5
 * Author: Danilo Vaccalluzzo
 * Author URI: https://www.linkedin.com/in/danilovaccalluzzo/
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: LoryBot
 * Domain Path: /languages
 * Requires at least: 4.9
 * Requires PHP: 5.3
 */

defined('ABSPATH') or exit;

$plugin_dir = plugin_dir_path(__FILE__);

// Set server URL
lorybot_initialize_server_url();

// Include necessary files
require_once $plugin_dir . 'includes/utils.php';
require_once $plugin_dir . 'includes/lorybot_update.php';
require_once $plugin_dir . 'includes/functions-settings.php';
require_once $plugin_dir . 'includes/functions-enqueue-scripts.php';
require_once $plugin_dir . 'includes/functions-chat-display.php';
require_once $plugin_dir . 'routes/activate.php';
require_once $plugin_dir . 'routes/deactivate.php';



register_activation_hook(__FILE__, 'Lorybot\Activate\lorybot_activate');
register_deactivation_hook(__FILE__, 'Lorybot\Deactivate\lorybot_deactivate');

function lorybot_initialize_server_url() {
    // Set the server URL as an option
    update_option('lorybot_server_url', 'https://lorybot-q4yzxnb64q-uc.a.run.app/');
}
