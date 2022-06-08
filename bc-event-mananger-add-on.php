<?php
/**
 * Plugin Name:       BC Events Manager Add On
 * Plugin URI:        http://brandcoders.com/
 * Description:       Seamlessly displays your custom events, the way you want them to display.
 * Version:           1.0
 * Author:            BrandCoders
 * Text Domain:       bc-events
 */

defined('ABSPATH') or die('Naaaah');

class BC_Event_Manager 
{
   
    // Actives Plugin
    public function plugin_activate() {
        add_action('init', [$this, 'create_admin_archive_page']); //Create archive page options
        add_action('init', [$this, 'create_single_event_page']); //Creates single-events.php
        add_action('init', [ $this, 'register_shortcodes' ]); //Register the ShortCodes
        add_action('init', [$this, 'create_acf_blocks']); //Creates ACF Blocks
        add_action('init', [ $this, 'stylesAndJS' ]); //Load in styles and scripts
        add_action('init', [ $this, 'create_bc_events' ]); // creates event loop class        
    }

    // Creates ACF option page / include ACF options
    public function create_admin_archive_page() 
    {                  
        // Add new options page for BC EM Add On
        if (function_exists('acf_add_options_page')) {
            acf_add_options_page(array(
                'page_title' 	=> 'BC EM Options',
                'menu_title'	=> 'BC EM Options',
                'menu_slug' 	=> 'bc-em-options',
                'parent_slug'    => 'edit.php?post_type=event',
                'position' 	    => '5',
            ));            
        }  
        
        include_once(__DIR__ . '/acf-settings/acf-settings.php');        
        if (class_exists('BC_Events_Manager_Add_On_ACF_Settings')) {
            $bc_event_settings = new BC_Events_Manager_Add_On_ACF_Settings();
        }
    }
    
    // Creates custom templates
    public function create_single_event_page() {
        add_filter( 'template_include', 'bc_create_templates' );
        function bc_create_templates($template) {
            $post_types = array('event');
            $location_type = array('location');

             // Loads custom archive-event.php
            if (is_post_type_archive($post_types) && ! file_exists(get_stylesheet_directory() . '/archive-event.php'))
                $template = dirname(__FILE__) . '/archive-event.php';

            // Loads custom single-event.php
            if (is_singular($post_types) && ! file_exists(get_stylesheet_directory() . '/single-event.php'))
                $template = dirname(__FILE__) . '/single-event.php';

            // Will finish template in V2
            // Loads custom single-location.php            
            // if (is_singular($location_type) && ! file_exists(get_stylesheet_directory() . '/single-location.php'))
            //     $template = dirname(__FILE__) . '/single-location.php';  

            return $template;            
        }               

        // Checks if plugins folder exists
        // If not creates it and moves calendar-full.php into folder structure. 
        $directory = '/plugins/events-manager/templates/';        
        if (!file_exists(get_template_directory() .  $directory)) {
            mkdir(get_stylesheet_directory() . $directory, 0777, true);
            @rename(dirname(__FILE__) . '/calendar-full.php', get_template_directory() .  $directory . 'calendar-full.php');
        } 
    }
    
    // Registers Shortcodes
    public function register_shortcodes() {    
        include_once(__DIR__ . '/shortcodes/shortcodes.php');           
    }
    
    // Creates ACF block
    public function create_acf_blocks() {
        if (function_exists('acf_register_block_type')) {

            //*********** BC Event List
            acf_register_block_type(array(
                'name'			    => 'bc-event-list',
                'title'			    => __('BC Event List', 'Brandcoders'),
                'render_template'   => dirname(__FILE__) . '/blocks/bc-event-list.php',
                'category'		    => 'bc-blocks',
                'icon'			    => 'admin-page',
                'mode'			    => 'auto',
                'keywords'		    => array( 'event', 'list', 'bc'),
            ));
        }
    }

    // Register styles/scripts
    public function stylesAndJS() {    
        wp_register_style('bc-event-add-on-style', plugins_url('dist/style.min.css', __FILE__));
        wp_enqueue_style('bc-event-add-on-style');
        wp_register_script('bc-event-add-on-js', plugins_url('dist/main.min.js', __FILE__),  array('jquery'), null, true);        
        wp_enqueue_script('bc-event-add-on-js');

        // ajax handler
        wp_register_script('bc-location-ajax', plugins_url('assets/js/bc-location-ajax.js', __FILE__),  array('jquery'), null, true); 
        wp_enqueue_script('bc-location-ajax');

        //Localize Ajax script
        wp_localize_script('bc-location-ajax', 'locationAjax',
            array(
                'locationAjaxUrl'  => admin_url( 'admin-ajax.php')               
            )
        );
    }

    // Includes BcEventList Class
    public static function create_bc_events() {
        include( plugin_dir_path( __FILE__ ) . 'classes/bc-event-list-loop.php');      
    }

    // Ajax callback
    public static function location_filter() {                                                  
        echo BcEventList::bcEventLoop();             
        wp_die();
    }
}

// Checks to make sure Events Manager plugin is active
if (!function_exists('is_plugin_active')) { 
    include_once(ABSPATH.'wp-admin/includes/plugin.php');
    if(!is_plugin_active('events-manager/events-manager.php')) {
        return;
    } else {
        $bc_events = new BC_Event_Manager;
        $bc_events->plugin_activate();
    }
}

// Ajax actions
add_action('wp_ajax_location_filter', ['BC_Event_Manager', 'location_filter']);
add_action('wp_ajax_nopriv_location_filter', ['BC_Event_Manager', 'location_filter']);

// Deactives the plugin
register_deactivation_hook(__FILE__, function () {
    // Moves calendar-full.php back to plugin directory       
    @rename(get_stylesheet_directory() . '/plugins/events-manager/templates/calendar-full.php', dirname(__FILE__) . '/calendar-full.php'); 
    
    // Removes folder structure in theme
    $directory = get_template_directory() . '/plugins';
    function deleteFilesThenSelf($folder) {
        foreach(new DirectoryIterator($folder) as $f) {
            if($f->isDot()) continue; // skip . and ..
            if ($f->isFile()) {
                unlink($f->getPathname());
            } else if($f->isDir()) {
                deleteFilesThenSelf($f->getPathname());
            }
        }
        rmdir($folder);
    }
    deleteFilesThenSelf($directory);
});


/**
 * ACF Activation
 * TIPS FOR TOMORROW
 * TURN ON DEBUG
 * LOOK AT NAME SPACE OF EVENT-LIST
 * MIGHT NEED TO CREATE A NEW BLOCK ENTIRELY.
 * FUCK GF
 */

// Options page & fields
// if ( function_exists('acf_add_options_page') || function_exists('acf_register_block_type')) {
//     add_action('acf/init', 'activate_acf_option_page_fields');
// }
// function activate_acf_option_page_fields() {
//     // Add new options page for BC EM Add On
//     if (function_exists('acf_add_options_page')) {
//         acf_add_options_page(array(
//             'page_title' 	=> 'BC EM Options',
//             'menu_title'	=> 'BC EM Options',
//             'menu_slug' 	=> 'bc-em-options',
//             'parent_slug'    => 'edit.php?post_type=event',
//             'position' 	    => '5',
//         ));            
//     }  
    
//     include_once(__DIR__ . '/acf-settings/acf-settings.php');        
//     if (class_exists('BC_Events_Manager_Add_On_ACF_Settings')) {
//         $settings = new BC_Events_Manager_Add_On_ACF_Settings();
//     }

//     // ACF Block
//     if (function_exists('acf_register_block_type')) {
//         //*********** BC Event List
//         acf_register_block_type(array(
//             'name'			    => 'bc-event-list',
//             'title'			    => __('BC Event List', 'Brandcoders'),
//             'render_template'   => dirname(__FILE__) . '/blocks/bc-event-list.php',
//             'category'		    => 'bc-blocks',
//             'icon'			    => 'admin-page',
//             'mode'			    => 'auto',
//             'keywords'		    => array( 'event', 'list', 'bc'),
//         ));
//     }
// }      