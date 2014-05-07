<?php
	/*
	Plugin Name: OSD Remove All Wordpress Branding
	Plugin URI: http://outsidesource.com
	Description: A plugin that removes all mention of Wordpress on the front and backend of your website
	Version: 1.0
	Author: OSD Web Development Team
	Author URI: http://outsidesource.com
	License: GPL2v2
	*/
	
	//filter to remove wp from title in admin section
	function osd_remove_wp_from_admin_title() {
		return get_bloginfo('name') . " > Administration";
	}
	add_filter('admin_title', 'osd_remove_wp_from_admin_title');
	
	//add favicon to login and admin pages
	function osd_add_favicon() {
		echo '<link rel="shortcut icon" href="',get_template_directory_uri(),'/images/favicon.png" />',"\n";
	}
	add_action('login_head', 'osd_add_favicon');
	add_action('admin_head', 'osd_add_favicon');
	
	//function for removing the default tagline
	function osd_remove_default_tagline() {
		if(get_bloginfo('description') == 'Just another WordPress site') {
			update_option('blogdescription', '');	
		}
	}
	add_action('after_setup_theme', 'osd_remove_default_tagline');
	
	//function called when wp head is loaded
	function osd_remove_wp_from_theme($wp_admin_bar) {
		$wp_admin_bar->remove_node('wp-logo');
	}
	add_action('admin_bar_menu', 'osd_remove_wp_from_theme', 999);
	
	//function to unregister the meta widget
	 function osd_remove_default_widgets() {
		 unregister_widget('WP_Widget_Meta');
	}
	add_action('widgets_init', 'osd_remove_default_widgets', 11);
	 
	//Completely remove various dashboard widgets 
	function osd_remove_dashboard_widgets() {
		remove_meta_box('dashboard_recent_drafts', 'dashboard', 'side');      //Recent Drafts
		remove_meta_box('dashboard_primary', 'dashboard', 'side');      //WordPress.com Blog
		remove_meta_box('dashboard_secondary', 'dashboard', 'side');      //Other WordPress News
		remove_meta_box('dashboard_incoming_links', 'dashboard', 'normal');    //Incoming Links
		remove_meta_box('wpdm_dashboard_widget', 'dashboard', 'normal');    //download manager plugin (if used)
	}
	add_action('wp_dashboard_setup', 'osd_remove_dashboard_widgets');
	
	//remove wp welcome panel 
	function osd_hide_welcome_panel() {
		remove_action('welcome_panel', 'wp_welcome_panel');
	}
	add_action('load-index.php', 'osd_hide_welcome_panel');
	
	//admin footer changes
	function osd_replace_footer_admin() {  
		echo '<span id="footer-thankyou"></span>';  
	}  
	add_filter('admin_footer_text', 'osd_replace_footer_admin');
	
	function osd_replace_footer_version() {
		return ' ';
	}
	add_filter('update_footer', 'osd_replace_footer_version', '1234');
	
	//login screen changes
	function osd_replace_login_logo() {
		echo("<style type='text/css'>");
			echo("body.login div#login h1 a {");
				echo("background-image: url(".get_stylesheet_directory_uri()."/images/site-login-logo.png);");
				echo("background-size: contain;");
				echo("height: 152px;");
				echo("width: 100%;");
			echo("}");
		echo("</style>");
	}
	add_action('login_enqueue_scripts', 'osd_replace_login_logo');
	
	function osd_replace_login_logo_url() {
		return get_bloginfo('url');
	}
	add_filter('login_headerurl', 'osd_replace_login_logo_url');
	
	function osd_replace_login_logo_title() {
		return get_bloginfo('name');
	}
	add_filter('login_headertitle', 'osd_replace_login_logo_title');
?>