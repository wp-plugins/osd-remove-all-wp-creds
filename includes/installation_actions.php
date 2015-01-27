<?php
// Prevent direct access to file
defined('ABSPATH') or die("No script kiddies please!");

if (get_option('osd_remove_all_wp_creds_options') === false) {
    $default_options = array(
        'favicon' => '',
        'login-image' => ''
    );

    add_option('osd_remove_all_wp_creds_options', $default_options, '', 'no');
}