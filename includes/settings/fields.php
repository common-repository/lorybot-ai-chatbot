<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 

$fields = [
    ['lorybot_enabled_field', 'Activate Chatbot', 'lorybot_enable_callback'],
    ['lorybot_custom_id_field', 'LoryBot API Key', 'lorybot_custom_id_callback'],
    ['lorybot_prompt_field', 'Prompt', 'lorybot_prompt_callback'],
    ['lorybot_embedding_field', 'Information Source', 'lorybot_embedding_callback'],
    ['lorybot_chat_display_field', 'Chat Display', 'lorybot_chat_display_callback'],
    ['lorybot_main_color_field', 'Main Color', 'lorybot_main_color_callback'],
    ['lorybot_title_color_field', 'Title Color', 'lorybot_title_color_callback'],
    ['lorybot_background_color_field', 'Background Color', 'lorybot_background_color_callback']
];

foreach ($fields as $field) {
    add_settings_field(
        $field[0],  // Field ID
        $field[1],  // Title
        $field[2],  // Callback
        'lorybot-settings',      // Page
        'lorybot_main_section'   // Section
    );
}
