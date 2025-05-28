<?php
/*
Plugin Name: JOLT™ LoadSense
Plugin URI: https://github.com/johnoltmans
Description: A true preloader that waits until the entire page has fully loaded (images, scripts, fonts) before showing the content.
Version: 1.2
Author: John Oltmans
Author URI: https://www.johnoltmans.nl/
*/

// Add Settings link next to Deactivate in Plugins overview
add_filter('plugin_action_links_' . plugin_basename(__FILE__), function($links) {
    $settings_link = '<a href="' . admin_url('options-general.php?page=jolt-loadsense') . '">Settings</a>';
    // Place Settings after Deactivate
    $links[] = $settings_link;
    return $links;
});

// Register settings and menu
add_action('admin_menu', function() {
    add_options_page('JOLT LoadSense Settings', 'JOLT LoadSense', 'manage_options', 'jolt-loadsense', 'jolt_loadsense_settings_page');
});

add_action('admin_init', function() {
    register_setting('jolt_loadsense_settings', 'jolt_loadsense_mode');
    register_setting('jolt_loadsense_settings', 'jolt_loadsense_animation');
});

function jolt_loadsense_settings_page() {
    $mode = get_option('jolt_loadsense_mode', 'real');
    $animation = get_option('jolt_loadsense_animation', 'spinner');
    ?>
    <div class="wrap">
        <h1>JOLT LoadSense Settings</h1>
        <form method="post" action="options.php">
            <?php settings_fields('jolt_loadsense_settings'); ?>
            <?php do_settings_sections('jolt_loadsense_settings'); ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Preloader Mode</th>
                    <td>
                        <select name="jolt_loadsense_mode">
                            <option value="real" <?php selected($mode, 'real'); ?>>Real (wait for full page load)</option>
                            <option value="fake" <?php selected($mode, 'fake'); ?>>Fake (always shows for 3 seconds)</option>
                        </select>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Animation Style</th>
                    <td>
                        <select name="jolt_loadsense_animation" id="jolt-animation-select">
                            <option value="spinner" <?php selected($animation, 'spinner'); ?>>Spinner</option>
                            <option value="dots" <?php selected($animation, 'dots'); ?>>Dots</option>
                            <option value="bounce" <?php selected($animation, 'bounce'); ?>>Bounce</option>
                        </select>

                        <div id="jolt-preview-container" style="margin-top: 1em;">
                            <div class="jolt-preview jolt-spinner" data-animation="spinner" style="display: none; margin-right: 20px;"></div>

                            <div class="jolt-preview jolt-dots" data-animation="dots" style="display: none; margin-right: 20px;">
                                <span></span><span></span><span></span>
                            </div>

                            <div class="jolt-preview jolt-bounce" data-animation="bounce" style="display: none;"></div>
                        </div>

                        <style>
                            /* Kleine previews styling */
                            .jolt-preview {
                                width: 30px;
                                height: 30px;
                                vertical-align: middle;
                            }
                            /* Spinner preview (scaled down) */
                            .jolt-spinner {
                                border-width: 3px;
                                animation: spin 1s linear infinite;
                                border-style: solid;
                                border-color: #aaa #3498db #aaa #aaa;
                                border-radius: 50%;
                            }
                            /* Dots preview */
                            .jolt-dots span {
                                width: 6px;
                                height: 6px;
                                margin: 0 3px;
                                background-color: #3498db;
                                border-radius: 50%;
                                display: inline-block;
                                animation: bounce 1.4s infinite ease-in-out both;
                            }
                            .jolt-dots span:nth-child(1) {
                                animation-delay: -0.32s;
                            }
                            .jolt-dots span:nth-child(2) {
                                animation-delay: -0.16s;
                            }
                            /* Bounce preview */
                            .jolt-bounce {
                                width: 30px;
                                height: 30px;
                                background-color: #3498db;
                                border-radius: 50%;
                                animation: bounceUpDown 1s infinite ease-in-out;
                            }
                            /* Animations */
                            @keyframes spin {
                                to { transform: rotate(360deg); }
                            }
                            @keyframes bounce {
                                0%, 80%, 100% { transform: scale(0); }
                                40% { transform: scale(1); }
                            }
                            @keyframes bounceUpDown {
                                0%, 100% { transform: translateY(0); }
                                50% { transform: translateY(-10px); }
                            }
                        </style>

                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                const select = document.getElementById('jolt-animation-select');
                                const previews = document.querySelectorAll('#jolt-preview-container .jolt-preview');

                                function showPreview() {
                                    const val = select.value;
                                    previews.forEach(preview => {
                                        if (preview.getAttribute('data-animation') === val) {
                                            preview.style.display = 'inline-block';
                                        } else {
                                            preview.style.display = 'none';
                                        }
                                    });
                                }

                                select.addEventListener('change', showPreview);

                                // Show correct preview on page load
                                showPreview();
                            });
                        </script>
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
        <div style="margin-top: 2em; background: #fffbe5; border: 1px solid #ffcc00; padding: 1em;">
            <strong>Note:</strong> In fake mode the preloader is shown for 3 seconds for testing purposes. In real mode, it will disappear only after all content (including images, fonts, and scripts) is fully loaded.
        </div>
    </div>
    <?php
}


// Enqueue CSS and JS, pass options to JS
add_action('wp_enqueue_scripts', function() {
    wp_enqueue_style('jolt-loadsense-style', plugin_dir_url(__FILE__) . 'style.css');
    wp_enqueue_script('jolt-loadsense-script', plugin_dir_url(__FILE__) . 'loadsense.js', [], false, true);

    wp_localize_script('jolt-loadsense-script', 'JOLTLoadSenseData', [
        'mode' => get_option('jolt_loadsense_mode', 'real'),
        'animation' => get_option('jolt_loadsense_animation', 'spinner'),
    ]);
});

// Output preloader HTML in body
add_action('wp_body_open', function() {
    $animation = get_option('jolt_loadsense_animation', 'spinner');
    ?>
    <div id="jolt-preloader">
        <?php if ($animation === 'spinner') : ?>
            <div class="jolt-spinner"></div>
        <?php elseif ($animation === 'dots') : ?>
            <div class="jolt-dots">
                <span></span><span></span><span></span>
            </div>
        <?php elseif ($animation === 'bounce') : ?>
            <div class="jolt-bounce"></div>
        <?php endif; ?>
    </div>
    <?php
});