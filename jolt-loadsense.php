<?php
/*
Plugin Name: JOLT™ LoadSense
Plugin URI: https://github.com/johnoltmans/JOLT-LoadSense
Description: A true preloader that waits until the entire page has fully loaded (images, scripts, fonts) before showing the content.
Version: 1.0
Author: John Oltmans
Author URI: https://www.johnoltmans.nl/
*/

if (!defined('ABSPATH')) exit; // Exit if accessed directly

// Voeg preload markup toe aan de body
add_action('wp_body_open', function() {
    echo '<div id="jolt-preloader"><div class="spinner"></div></div>';
});

// Voeg CSS en JS toe
add_action('wp_enqueue_scripts', function() {
    wp_enqueue_style('jolt-loadsense-style', plugin_dir_url(__FILE__) . 'style.css');
    wp_enqueue_script('jolt-loadsense-script', plugin_dir_url(__FILE__) . 'loadsense.js', [], false, true);
});
