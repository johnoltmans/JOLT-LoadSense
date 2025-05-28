<?php
/*
Plugin Name: JOLT™ LoadSense
Plugin URI: https://github.com/johnoltmans
Description: A true preloader that waits until the entire page has fully loaded (images, scripts, fonts) before showing the content.
Version: 1.1
Author: John Oltmans
Author URI: https://www.johnoltmans.nl/
*/

// Admin menu toevoegen
add_action('admin_menu', function() {
    add_options_page(
        'JOLT LoadSense Settings',
        'JOLT LoadSense',
        'manage_options',
        'jolt-loadsense',
        'jolt_loadsense_settings_page'
    );
});

// Setting registreren
add_action('admin_init', function() {
    register_setting('jolt_loadsense_settings', 'jolt_loadsense_mode');
});

// Instellingenpagina tonen
function jolt_loadsense_settings_page() {
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
                            <option value="real" <?php selected(get_option('jolt_loadsense_mode'), 'real'); ?>>Real (wait for full page load)</option>
                            <option value="fake" <?php selected(get_option('jolt_loadsense_mode'), 'fake'); ?>>Fake (always shows for 3 seconds)</option>
                        </select>
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

// Scripts en styles laden
add_action('wp_enqueue_scripts', function() {
    wp_enqueue_style('jolt-loadsense-style', plugin_dir_url(__FILE__) . 'style.css');
    wp_enqueue_script('jolt-loadsense-script', plugin_dir_url(__FILE__) . 'loadsense.js', [], false, true);

    // PHP-setting doorgeven aan JS
    wp_localize_script('jolt-loadsense-script', 'JOLTLoadSenseData', [
        'mode' => get_option('jolt_loadsense_mode', 'real'),
    ]);
});

// Preloader HTML in de <body> injecteren
add_action('wp_body_open', function() {
    ?>
    <div id="jolt-preloader">
        <div class="jolt-spinner"></div>
    </div>
    <?php
});

add_filter('plugin_action_links_' . plugin_basename(__FILE__), function($links) {
    $settings_link = '<a href="' . admin_url('options-general.php?page=jolt-loadsense') . '">Settings</a>';
    // Deactivate-link zit meestal al in $links, die staat standaard aan het begin
    // Wij plaatsen Settings dus achteraan, zodat het wordt: Deactivate | Settings
    $links[] = $settings_link;
    return $links;
});
