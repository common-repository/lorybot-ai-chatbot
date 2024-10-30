<?php
/**
 * This script contains callback functions for the LoryBot plugin's settings.
 * Each function corresponds to a specific setting, handling its display and behavior in the settings page.
 */

/**
 * Callback for displaying the section description.
 */
function lorybot_section_callback() {
    echo '<p class="setting-p">You can tailor the behavior of the LoryBot AI engine and the style of the chatbot to suit your preferences here.</p>';
}


/**
 * Retrieves a specific LoryBot option with an optional default value.
 *
 * @param string $option_name The name of the option to retrieve.
 * @param mixed $default The default value to return if the option is not set.
 * @return mixed The value of the option or the default value.
 */
function lorybot_get_option($option_name, $default = '') {
    $options = get_option('lorybot_options');
    return $options[$option_name] ?? $default;
}


/**
 * Callback for displaying the chat enabled checkbox.
 */
function lorybot_enable_callback() {
    $chat_enabled = lorybot_get_option('chat_enabled');
    ?>
    <label class="custom-checkbox">
        <input type="checkbox" name="lorybot_options[chat_enabled]" id="lorybot_enabled_field" <?php checked($chat_enabled, 'on'); ?>>
        <span class="checkmark"></span>
    </label>
    <span class="help-tip-text">Enable this option to display the chatbot on your website.</span>
    <?php
}

/**
 * Callback for displaying the custom ID text field.
 */
function lorybot_custom_id_callback() {
    $custom_id = lorybot_get_option('custom_id');
    ?>
    <div class="lorybot-field-wrap">
        <input value="<?php echo esc_attr($custom_id); ?>" type="text" name="lorybot_options[custom_id]" id="lorybot_custom_id_field" class="lorybot-input">
        <span class="help-tip-text">Please handle this API key with care. It is essential for the proper functioning of the application.</span>
    </div>
    <?php
}



/**
 * Callback for displaying the prompt textarea.
 */
function lorybot_prompt_callback() {
    $prompt = lorybot_get_option('prompt');
    ?>
    <div class="lorybot-field-wrap">
        <textarea id="lorybot_prompt_field" name="lorybot_options[prompt]" class="lorybot-textarea"><?php echo esc_textarea($prompt); ?></textarea>
        <span class="help-tip-text">
            This section allows you to customize the behavior and responses of the LoryBot AI. You have the opportunity to shape the AI's tone, style of conversation, and the specific guidelines it follows when interacting with users.
            <br /><br />
            <strong>Considerations for Customization:</strong>
            <ul>
                <li><strong>Tone and Style:</strong> Decide on the tone (professional, friendly, casual, etc.) and conversation style (formal, conversational, etc.) that best fits your brand and audience. This sets the overall character of your AI's interactions.</li>
                <li><strong>Specific Guidelines:</strong> Define rules or guidelines for the AI. This can include the use of specific phrases, keywords, or the type of information it should prioritize in responses.</li>
                <li><strong>Resource Links:</strong> Incorporate links to useful resources such as FAQs, product pages, or support articles within the AI's responses. This can provide users with direct access to more detailed information.</li>
                <li><strong>Example Prompts:</strong> Create prompts that exemplify how you want the AI to interact. For example:<br />
                    <code>
                        'You are an AI assistant specialized in customer support for tech products. <br />
                        Your responses should be friendly, clear, and informative. When users ask questions, provide concise and helpful answers. <br />
                        Include a link to our FAQ page for more detailed information. <br />
                        If asked about product features, highlight our product's unique aspects positively and engagingly.'
                    </code>
                </li>
            </ul>
            The way you configure these prompts directly influences how effectively the AI can serve and engage with your users. A well-configured AI can significantly enhance user experience and satisfaction.
        </span>
    </div>
    <?php
}

/**
 * Callback for displaying the Information Source textarea.
 */
function lorybot_embedding_callback() {
    $embedding = lorybot_get_option('embedding');
    ?>
    <div class="lorybot-field-wrap">
    <textarea id="embedding_editor_id" name="lorybot_options[embedding]" class="lorybot-textarea"><?php echo esc_textarea($embedding); ?></textarea>
    <span class="help-tip-text">
        This section is for entering the foundational information and knowledge base that the AI will use to understand and respond to user inquiries. It's essential for shaping how the AI interacts with users and the quality of its responses.
        <br /><br />
        <strong>Key Points to Consider:</strong>
        <ul>
            <li><strong>Content Relevance:</strong> Include detailed and relevant information about your services, products, or specific topics that are crucial for your business. The AI will use this to provide accurate and context-specific responses.</li>
            <li><strong>Data Accuracy:</strong> Ensure that the information provided is accurate and up-to-date. Accurate data is vital for the AI to deliver reliable and trustworthy responses.</li>
            <li><strong>Structure and Clarity:</strong> Organize the information logically and clearly. Well-structured content helps the AI to process and utilize it effectively.</li>
            <li><strong>Examples and Scenarios:</strong> Consider including examples or common scenarios that your users might encounter. This helps the AI to better understand user needs and respond appropriately.</li>
            <li><strong>Continuous Updates:</strong> Regularly update this knowledge base to reflect any changes in your services or products. An up-to-date AI is more effective in assisting users.</li>
        </ul>
        The more comprehensive and detailed the information provided, the more effectively the AI can assist users. Think of this as teaching the AI about your business so it can serve your users better.
    </span>
    </div>
    <?php
}



/**
 * Callback for displaying the chat display textarea.
 * It uses WP_Filesystem methods to securely load the default HTML from a local file if the setting is not set.
 */
function lorybot_chat_display_callback() {
    require_once(ABSPATH . 'wp-admin/includes/file.php'); // Include the file.php for WP_Filesystem

    WP_Filesystem(); // Initialize the WP filesystem, no more using 'file-put-contents' directly

    global $wp_filesystem; // Global filesystem variable

    $chat_html_path = plugin_dir_path(__FILE__) . 'chat-html.php';

    if ($wp_filesystem->exists($chat_html_path)) {
        $chat_display = $wp_filesystem->get_contents($chat_html_path); // Use WP_Filesystem method
    } else {
        $chat_display = 'Error loading default chat HTML. Please check the file path and permissions.';
    }

    // Use lorybot_get_option() to possibly override the file content with a stored option
    $chat_display = lorybot_get_option('chat_display', $chat_display);

    ?>
    <div class="lorybot-field-wrap">
        <textarea id="lorybot_chat_display_field" name="lorybot_options[chat_display]" class="lorybot-textarea chat-display"><?php echo esc_textarea($chat_display); ?></textarea>
        <span class="help-tip-text">The html of the chatbot. You can customize the messages to be displayed.</span>
    </div>
    <?php
}



/**
 * Adds the color picker script once per page load.
 */
function lorybot_color_picker_script() {
    static $script_added = false;
    if (!$script_added) {
        ?>
        <script type="text/javascript">
            jQuery(document).ready(function($) {
                $('.lorybot-color-picker').wpColorPicker();
            });
        </script>
        <?php
        $script_added = true;
    }
}

/**
 * Callback for displaying a color picker.
 *
 * @param string $option_name The name of the color option to display.
 * @param string $default_color The default color value to use if the option is not set.
 */
function lorybot_color_picker_callback($option_name, $default_color) {
    $color_value = lorybot_get_option($option_name);
    if (empty($color_value)) {
        $color_value = $default_color;
    }
    ?>
    <input class="lorybot-color-picker" type="text" name="lorybot_options[<?php echo esc_attr($option_name); ?>]" value="<?php echo esc_attr($color_value); ?>">
    <?php
    lorybot_color_picker_script();
}

/**
 * Callbacks for color settings.
 */
function lorybot_main_color_callback() {
    lorybot_color_picker_callback('main_color', '#0e456c');
}

function lorybot_background_color_callback() {
    lorybot_color_picker_callback('background_color', '#ffffff');
}

function lorybot_title_color_callback() {
    lorybot_color_picker_callback('title_color', '#ffffff');
}

?>
