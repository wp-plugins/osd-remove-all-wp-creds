<?php
/*
Plugin Name: OSD Remove All Wordpress Branding
Plugin URI: http://outsidesource.com
Description: A plugin that removes all mention of Wordpress on the front and backend of your website
Version: 2.0
Author: OSD Web Development Team
Author URI: http://outsidesource.com
License: GPL2v2
*/

defined('ABSPATH') or die("No script kiddies please!");

include_once('includes/OSDRemoveAllWPCreds.php');
new OSDRemoveAllWPCreds();

if (is_admin()) {
	include_once('includes/global_settings.php');

	// Add settings page link to plugins page
	function osd_remove_all_wp_creds_link_generate($links) { 
		$settings_link = '<a href="admin.php?page=osd-remove-all-wp-creds-options">Settings</a>'; 
		array_unshift($links, $settings_link); 
		return $links; 
	}
	add_filter("plugin_action_links_".plugin_basename(__FILE__), 'osd_remove_all_wp_creds_link_generate');
}

// Activation functions
function osd_remove_all_wp_creds_activate() {
    include_once('includes/installation_actions.php');
}
register_activation_hook(__FILE__, 'osd_remove_all_wp_creds_activate');