<?php
defined('ABSPATH') or die("No script kiddies please!");

class OSDRemoveAllWPCreds {
	private $user_settings = array(
        'favicon' => '',
        'login-image' => ''
    );

	function __construct() { 
		$user_settings = get_option('osd_remove_all_wp_creds_options');
		$this->user_settings = ($user_settings === false) ? $this->user_settings : $user_settings;

		global $pagenow;
		if (is_admin() || $pagenow == 'wp-login.php' || $pagenow == 'wp-register.php') {
			add_filter('admin_title', array($this, 'osd_remove_wp_from_admin_title'));
			add_action('login_head', array($this, 'osd_add_favicon'));
			add_action('admin_head', array($this, 'osd_add_favicon'));
			add_action('after_setup_theme', array($this, 'osd_remove_default_tagline'));
			add_action('widgets_init', array($this, 'osd_remove_default_widgets'), 11);
			add_action('wp_dashboard_setup', array($this, 'osd_remove_dashboard_widgets'));
			add_action('load-index.php', array($this, 'osd_hide_welcome_panel'));
			add_filter('update_footer', array($this, 'osd_replace_footer_version'), 999);
			add_filter('admin_footer_text', array($this, 'osd_replace_footer_admin'));
			add_action('login_enqueue_scripts', array($this, 'osd_replace_login_logo'));
			add_filter('login_headerurl', array($this, 'osd_replace_login_logo_url'));
			add_filter('login_headertitle', array($this, 'osd_replace_login_logo_title'));
		} 

		add_action('admin_bar_menu', array($this, 'osd_remove_wp_from_theme'), 999);
		add_filter('the_generator', array($this, 'osd_remove_generator'));
    }

	//filter to remove wp from title in admin section
	function osd_remove_wp_from_admin_title() {
		return get_bloginfo('name') . " > Administration";
	}
	
	//add favicon to login and admin pages
	function osd_add_favicon() {
		if ($this->user_settings['favicon'] != '') {
			$url = $this->get_media_by_id($this->user_settings['favicon']);
		} else {
			$url = get_stylesheet_directory_uri()."/images/favicon.png";
			$file_header = get_headers($url);
			$url = (strpos($file_header[0], '404') === false) ? $url : '';
		}

		echo "<link rel='shortcut icon' href='{$url}' /> \n";
	}

	//function for removing the default tagline
	function osd_remove_default_tagline() {
		if(get_bloginfo('description') == 'Just another WordPress site') {
			update_option('blogdescription', '');	
		}
	}

	//function called when wp head is loaded
	function osd_remove_wp_from_theme($wp_admin_bar) {
		$wp_admin_bar->remove_node('wp-logo');
	}

	//function to unregister the meta widget
	 function osd_remove_default_widgets() {
		 unregister_widget('WP_Widget_Meta');
	}
	 
	//Completely remove various dashboard widgets 
	function osd_remove_dashboard_widgets() {
		remove_meta_box('dashboard_recent_drafts', 'dashboard', 'side');      //Recent Drafts
		remove_meta_box('dashboard_primary', 'dashboard', 'side');      //WordPress.com Blog
		remove_meta_box('dashboard_secondary', 'dashboard', 'side');      //Other WordPress News
		remove_meta_box('dashboard_incoming_links', 'dashboard', 'normal');    //Incoming Links
		remove_meta_box('wpdm_dashboard_widget', 'dashboard', 'normal');    //download manager plugin (if used)
	}

	//remove wp welcome panel 
	function osd_hide_welcome_panel() {
		remove_action('welcome_panel', 'wp_welcome_panel');
	}

	//admin footer changes
	function osd_replace_footer_admin() {  
		echo '<span id="footer-thankyou"></span>';  
	}  

	function osd_replace_footer_version() {
		return ' ';
	}

	//login screen changes
	function osd_replace_login_logo() {
		if ($this->user_settings['login-image'] != '') {
			$url = $this->get_media_by_id($this->user_settings['login-image']);
		} else {
			$url = get_stylesheet_directory_uri()."/images/site-login-logo.png";
			$file_header = get_headers($url);
			$url = (strpos($file_header[0], '404') === false) ? $url : '';
		}

		echo "
			<style type='text/css'>
				body.login div#login h1 a {
					background-image: url({$url});
					background-size: contain;
					height: 152px;
					width: 100%;
				}
			</style>";
	}

	function osd_replace_login_logo_url() {
		return get_bloginfo('url');
	}

	function osd_replace_login_logo_title() {
		return get_bloginfo('name');
	}

	// Remove the generator meta tag
	function osd_remove_generator() {
		return "";
	}

	// Grab any media by id reliably 
	private function get_media_by_id($id) {
        if ($id == null) {
            return "";
        }
        $image = get_post_meta($id, '_wp_attached_file', true);
        $image = get_bloginfo('url')."/wp-content/uploads/".$image;
        return $image;
    }
}