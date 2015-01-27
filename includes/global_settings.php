<?php
// Prevent direct access to file
defined('ABSPATH') or die("No script kiddies please!");

//SETTINGS PAGE
$settingsPage = new OSDRemoveAllBrandingSettings();

class OSDRemoveAllBrandingSettings {
    private $options;

    public function __construct() {
        add_action('admin_menu', array($this, 'add_menu_item'));
        add_action('admin_init', array($this, 'page_init'));
    }

    //add options page to wp
    public function add_menu_item() {
        add_options_page(
            'OSD Remove All WordPress Branding', 
            'OSD Remove WP Branding', 
            'manage_options', 
            'osd-remove-all-wp-creds-options', 
            array($this, 'create_admin_page')
        ); 
    }

    //create options page
    public function create_admin_page() {
        // Set class property
        $this->options = get_option('osd_remove_all_wp_creds_options');
        if ($this->options === false) {
            $this->options = array(
                'favicon' => '',
                'login-image' => ''
            );
        }

        //add styling to the page
        $this->addStyle();

        ?>
        <div class="wrap">
            <h2>OSD Remove All WordPress Branding</h2>   
            <form method="post" action="options.php">
            <?php
                // This prints out all hidden setting fields
                settings_fields('osd-remove-all-wp-creds-options');   
                do_settings_sections('osd-remove-all-wp-creds-options');
                submit_button(); 
            ?>
            </form>
        </div>
        <?php

        //add js to the page
        wp_enqueue_media(); //include the js library for media
        $this->addJS();
    }

    //register / add options 
    public function page_init() {        
        register_setting(
            'osd-remove-all-wp-creds-options', // Option group
            'osd_remove_all_wp_creds_options', // Option name
            array($this, 'sanitize') // Sanitize
        );

        add_settings_section(
            'main_settings', // ID
            'Global Remove All WordPress Branding Settings', // Title
            array($this, 'print_section_info'), // Callback
            'osd-remove-all-wp-creds-options' // Page
        );  

        add_settings_field(
            'favicon', // ID
            'WordPress Admin Screens Favicon', // Title 
            array($this, 'favicon_callback'), // Callback
            'osd-remove-all-wp-creds-options', // Page
            'main_settings' // Section           
        ); 

        add_settings_field(
            'login-image', // ID
            'WordPress Login Screen Logo', // Title 
            array($this, 'login_image_callback'), // Callback
            'osd-remove-all-wp-creds-options', // Page
            'main_settings' // Section           
        );          
    }

    //sanitize  
    public function sanitize($input) {
        return $input;
    }

    //section text
    public function print_section_info() {
        // echo 'some instructional info';
    }

    /**** output to admin settings screen ****/
    public function favicon_callback() {
        $url = $this->get_media_by_id($this->options['favicon']);
        $img = ($url != '') ? "<img src='{$url}' />" : '';
        echo "
        <div class='osd-image favicon'>
            <div class='thumbnail'>{$img}</div>
            <input class='img-id' type='hidden' name='osd_remove_all_wp_creds_options[favicon]' value='{$this->options['favicon']}' />
            <div class='img-url'>{$url}</div>
            <div class='submit button-primary select'>Select Image</div>
            <div class='submit button-secondary remove'>Remove</div>
        </div>";
    }

    public function login_image_callback() {
        $url = $this->get_media_by_id($this->options['login-image']);
        $img = ($url != '') ? "<img src='{$url}' />" : '';
        echo "
        <div class='osd-image logo'>
            <div class='thumbnail'>{$img}</div>
            <input class='img-id' type='hidden' name='osd_remove_all_wp_creds_options[login-image]' value='{$this->options['login-image']}' />
            <div class='img-url'>{$url}</div>
            <div class='submit button-primary select'>Select Image</div>
            <div class='submit button-secondary remove'>Remove</div>
        </div>";
    }
    /**** end output to admin settings screen ****/

    private function get_media_by_id($id) {
        if ($id == null) {
            return "";
        }
        $image = get_post_meta($id, '_wp_attached_file', true);
        $image = get_bloginfo('url')."/wp-content/uploads/".$image;
        return $image;
    }

    private function addJS() {
        ?>
        <script type='text/javascript'>
            document.onready = function() {
                jQuery('.submit.select').click(function() {
                    var osd_image = jQuery(this).parent();
                    wp.media.editor.send.attachment = function(props, attachment) {
                        var imgURL = attachment.url;
                        var attachmentID = attachment.id;
                        jQuery(osd_image).find('.thumbnail').html("<img src='"+imgURL+"' />");
                        jQuery(osd_image).find('.img-url').html(imgURL);
                        jQuery(osd_image).find('.img-id').val(attachmentID);
                    }
                    wp.media.editor.open(document);
                    return false;
                });

                jQuery('.submit.remove').click(function() {
                    jQuery(this).siblings('.img-url').html('');
                    jQuery(this).siblings('.img-id').val('');
                    jQuery(this).siblings('.thumbnail').html('');
                });
            }
        </script>
        <?php
    }

    private function addStyle() {
        ?>
        <style type="text/css">
            .osd-image .thumbnail img {
                max-height: 150px;
                max-width: 300px;
                padding-bottom: 10px;
            }
            .osd-image .img-url {
                padding-bottom: 10px;
            }
        </style>    
        <?php
    }
}