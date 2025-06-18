<?php
/*
Plugin Name: JOLT™ LoadSense
Plugin URI: https://github.com/johnoltmans/JOLT-LoadSense
Description: A true preloader that waits until the entire page has fully loaded (images, scripts, fonts) before showing the content.
Version: 1.4
Author: John Oltmans
Author URI: https://www.johnoltmans.nl/
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

// Add Settings link next to Deactivate in Plugins overview
add_filter('plugin_action_links_' . plugin_basename(__FILE__), function($links) {
    $settings_link = '<a href="' . admin_url('options-general.php?page=jolt-loadsense') . '">Settings</a>';
    // Place Settings after Deactivate
    $links[] = $settings_link;
    return $links;
});

// Voeg hoofdmenu toe aan admin menu
add_action('admin_menu', 'jolt_loadsense_add_admin_menu');
function jolt_loadsense_add_admin_menu() {
    add_menu_page(
        'JOLT LoadSense',                  // Pagina titel
        'JOLT LoadSense',                  // Menu titel in de zijbalk
        'manage_options',                  // Vereiste rechten
        'jolt-loadsense',                  // Slug
        'jolt_loadsense_settings_page',    // Callback functie
        'dashicons-update',                // Icoontje, bijvoorbeeld 'dashicons-update' of 'dashicons-admin-generic'
        61                                 // Positie in menu
    );
}


add_action('admin_enqueue_scripts', function($hook) {
    if ($hook !== 'settings_page_jolt-loadsense') return;
    wp_enqueue_media();
    wp_enqueue_script('jquery');
});

add_action('admin_init', function() {
    register_setting('jolt_loadsense_settings', 'jolt_loadsense_mode');
    register_setting('jolt_loadsense_settings', 'jolt_loadsense_animation');
    register_setting('jolt_loadsense_settings', 'jolt_loadsense_custom_image');
    register_setting('jolt_loadsense_settings', 'jolt_loadsense_custom_html');
});


function jolt_loadsense_settings_page() {
    $mode = get_option('jolt_loadsense_mode', 'real');
    $animation = get_option('jolt_loadsense_animation', 'spinner');
    $custom_image = get_option('jolt_loadsense_custom_image', '');
    $custom_html = get_option('jolt_loadsense_custom_html', '');
    ?>
    <div class="wrap">
        <h1>JOLT LoadSense Settings</h1> 		 
        <div style="margin-top: 2em; background: #fffbe5; border: 1px solid #ffcc00; padding: 1em;">
            <strong>Fake Mode</strong>: This is a fake preloader to test your preloader with and is shown for 3 seconds.<br> <strong>Real Mode</strong>: It will disappear only after all content (including images, fonts, and scripts) is fully loaded.
            <br><br>
            <strong>Warning!</strong> Be sure to turn Real Mode on when you are done testing!
        </div>
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
                            <option value="pulse" <?php selected($animation, 'pulse'); ?>>Pulse</option>
                            <option value="flip" <?php selected($animation, 'flip'); ?>>Flip</option>
                            <option value="custom_image" <?php selected($animation, 'custom_image'); ?>>Custom Image</option>
                            <option value="custom_html" <?php selected($animation, 'custom_html'); ?>>Custom HTML</option>
                        </select>

                        <div id="jolt-preview-container" style="margin-top: 1em;">
                            <div class="jolt-preview jolt-spinner" data-animation="spinner" style="display: none; margin-right: 20px;"></div>
                            <div class="jolt-preview jolt-dots" data-animation="dots" style="display: none; margin-right: 20px;">
                                <span></span><span></span><span></span>
                            </div>
                            <div class="jolt-preview jolt-bounce" data-animation="bounce" style="display: none;"></div>
                            <div class="jolt-preview jolt-pulse" data-animation="pulse" style="display: none; margin-right: 20px;"></div>
                            <div class="jolt-preview jolt-flip" data-animation="flip" style="display: none; margin-right: 20px;"></div>

                            <div class="jolt-preview jolt-custom-image" data-animation="custom_image" style="display: none; margin-top: 10px;">
                                <?php if ($custom_image): ?>
                                    <img src="<?php echo esc_url($custom_image); ?>" alt="Custom Loader" style="max-width: 100px; max-height: 100px;">
                                <?php else: ?>
                                    <em>No image uploaded yet.</em>
                                <?php endif; ?>
                            </div>

                            <div class="jolt-preview jolt-custom-html" data-animation="custom_html" style="display: none; margin-top: 10px; border: 1px solid #ccc; padding: 5px; background: #fff;">
                                <?php echo $custom_html ? $custom_html : '<em>No custom HTML provided.</em>'; ?>
                            </div>
                        </div>

                        <!-- Styles for new animations -->
                        <style>
                            .jolt-preview {
                                width: 30px;
                                height: 30px;
                                vertical-align: middle;
                            }
                            .jolt-spinner {
                                border-width: 3px;
                                animation: spin 1s linear infinite;
                                border-style: solid;
                                border-color: #aaa #3498db #aaa #aaa;
                                border-radius: 50%;
                            }
                            .jolt-dots span {
                                width: 6px;
                                height: 6px;
                                margin: 0 3px;
                                background-color: #3498db;
                                border-radius: 50%;
                                display: inline-block;
                                animation: bounce 1.4s infinite ease-in-out both;
                            }
                            .jolt-dots span:nth-child(1) { animation-delay: -0.32s; }
                            .jolt-dots span:nth-child(2) { animation-delay: -0.16s; }
                            .jolt-bounce {
                                width: 30px;
                                height: 30px;
                                background-color: #3498db;
                                border-radius: 50%;
                                animation: bounceUpDown 1s infinite ease-in-out;
                            }
                            .jolt-pulse {
                                width: 30px;
                                height: 30px;
                                background-color: #3498db;
                                border-radius: 50%;
                                animation: pulse 1.5s infinite;
                            }
                            .jolt-flip {
                                width: 30px;
                                height: 30px;
                                background-color: #3498db;
                                border-radius: 4px;
                                animation: flip 1s infinite;
                            }

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
                            @keyframes pulse {
                                0% { transform: scale(1); opacity: 1; }
                                50% { transform: scale(1.2); opacity: 0.7; }
                                100% { transform: scale(1); opacity: 1; }
                            }
                            @keyframes flip {
                                0%, 100% { transform: rotateY(0deg); }
                                50% { transform: rotateY(180deg); }
                            }
                        </style>

                        <!-- Script to show preview -->
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
                                showPreview();
                            });
                        </script>
                    </td>
                </tr>

                <!-- Custom image upload -->
                <tr valign="top">
                    <th scope="row">Upload Custom Loader Image (.gif/.svg/.webp)</th>
                    <td>
                        <input type="text" name="jolt_loadsense_custom_image" id="jolt_custom_image" value="<?php echo esc_attr($custom_image); ?>" style="width: 60%;" />
                        <button type="button" class="button" id="jolt_custom_image_button">Upload / Select Image</button>
                        <p class="description">Upload an image or select one from the media library.</p>
                    </td>
                </tr>

                <!-- Custom HTML -->
                <tr valign="top">
                    <th scope="row">Custom Loader HTML</th>
                    <td>
                        <textarea name="jolt_loadsense_custom_html" rows="5" style="width: 100%;"><?php echo esc_textarea($custom_html); ?></textarea>
                        <p class="description">Enter your own custom HTML for the loader.</p>
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
        
    </div>

    <script>
    jQuery(document).ready(function($){
        $('#jolt_custom_image_button').on('click', function(e) {
            e.preventDefault();

            var frame = wp.media({
                title: 'Select or Upload Custom Loader Image',
                button: { text: 'Use this image' },
                multiple: false
            });

            frame.on('select', function() {
                var attachment = frame.state().get('selection').first().toJSON();
                $('#jolt_custom_image').val(attachment.url);
                $('#jolt_custom_image').trigger('change');
                            });

            frame.open();
        });
    });
    </script>
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
    $custom_image = get_option('jolt_loadsense_custom_image', '');
    $custom_html = get_option('jolt_loadsense_custom_html', '');

    ?>
    <div id="jolt-preloader">
        <?php 
        if ($animation === 'spinner') : ?>
            <div class="jolt-spinner"></div>
        <?php elseif ($animation === 'dots') : ?>
            <div class="jolt-dots">
                <span></span><span></span><span></span>
            </div>
        <?php elseif ($animation === 'bounce') : ?>
            <div class="jolt-bounce"></div>
        <?php elseif ($animation === 'pulse') : ?>
            <div class="jolt-pulse"></div>
        <?php elseif ($animation === 'flip') : ?>
            <div class="jolt-flip"></div>
        <?php elseif ($animation === 'custom_image' && $custom_image) : ?>
            <img src="<?php echo esc_url($custom_image); ?>" alt="Custom Loader" style="max-width: 100px; max-height: 100px;">
        <?php elseif ($animation === 'custom_html' && $custom_html) : ?>
            <?php echo $custom_html; ?>
        <?php else : ?>
            <div class="jolt-spinner"></div> <!-- fallback -->
        <?php endif; ?>
    </div>
    <?php
});
