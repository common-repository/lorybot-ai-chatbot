<?php

function lorybot_sanitize_options($input) {
    $output = [];

    $fields = [
        'url' => 'esc_url_raw',
        'chat_enabled' => null,
        'custom_id' => null,
        'prompt' => null,
        'embedding' => null,
        'chat_display' => null,
        'main_color' => function($value) {
            return preg_match('/^#[a-f0-9]{6}$/i', $value) ? sanitize_text_field($value) : null;
        },
        'title_color' => function($value) {
            return preg_match('/^#[a-f0-9]{6}$/i', $value) ? sanitize_text_field($value) : null;
        },
        'background_color' => function($value) {
            return preg_match('/^#[a-f0-9]{6}$/i', $value) ? sanitize_text_field($value) : null;
        },
    ];

    foreach ($fields as $field => $sanitizer) {
        if (isset($input[$field])) {
            $output[$field] = $sanitizer ? $sanitizer($input[$field]) : $input[$field];
        }
    }

    return $output;
}
