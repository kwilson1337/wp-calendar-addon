<?php 
/**
 * BcEventList
 * Handles logic for looping over events
 * and determining whether of not take over events
 * are present within loop.
 * 
 * Also handles logic of BC Event List Block settings
 */
class BcEventList extends EM_Events { 

    // Used for class change for list/grid display    
    public static function listOrGrid() {
        $event_list = [
            'display'       => get_field('list_or_grid'),               
        ];

        if($event_list['display'] == 'list') {
            return;
        } else {
            return '-grid';
        }
    }

    // Determines posts per row
    public static function postsPerRow() {
        $post_per_row = get_field('posts_per_row', 'option');

        if($post_per_row == 'three') {
            return '-three';
        } else {
            return '';
        }
    }

    // Determines if Title overlay trigger is switched
    public static function titleOverlay() {
        $title_overlay = get_field('title_over_image', 'option');
        
        if($title_overlay) {
            return '-title-overlay';
        }  
    }

    // Sort events by day, and gather IDs of take over events
    public static function eventSort($event) {
        // Group events by date
        $events_dates = [];
        foreach($event as $EM_Event){
            $events_dates[strtotime($EM_Event->start_date)][] = $EM_Event;
        } 

        $event_ids = [];
        foreach($events_dates as $key => $BC_Event) {
            // Map over objects to look for take over attribute				
            $events_available = array_map(function($e) {							
                if(isset($e->event_attributes['is_this_event_a_take_over'])) {
                    if($e->event_attributes['is_this_event_a_take_over'] == 1) {
                        return $e;
                    }
                }
            }, $BC_Event);
                                                
            // Check if attribute is true else display all events                 
            if ( !empty(array_filter($events_available))) {	                   
                // Grabs events with Take over attribute                            													
                foreach($events_available as $key => $single_event) {																					
                    if(!empty($single_event)) {	    
                        $event_ids[] = $single_event->post_id;                                                                                                       		                                                   
                    }
                }                                        
                
            } else {		
                // Grabs the rest of events		                   
                foreach($BC_Event as $key => $single_event) {                                                        
                    $event_ids[] = $single_event->post_id;                                                      
                }                    	                       
            }                                     
        }
        return $event_ids;
    }

    // Used for events in loop.
    public static function bcEventLoopSchema($schema) {
        foreach(EM_Events::get($schema) as $single_event_schema) {
            include( plugin_dir_path( __DIR__ ) . 'schema/event-loop-schema.php');
        }
    }

    // All Event Logic
    public static function bcEventLoop($args = null) { 
                
        // Location ID for Ajax
        $location_id = '';
        if(isset($_POST['location_id'])) {
            $location_id = $_POST['location_id'];
            $args['location'] = $location_id;
        }

        // Used for Archive pages + Ajax
        // Creates event scope
        $event_scope_ajax = '';
        if(isset($_POST['url_base'])) {
            $event_scope_ajax = basename($_POST['url_base']);
            
            if(strtotime($event_scope_ajax)) {               
                $args['scope'] = $event_scope_ajax;
            } else {
                $args['scope'] = 'future';
            }
        }

        // Grab Category IDs for Ajax Query
        $event_categories_id = '';
        if(isset($_POST['category_id'])) {
            $event_categories_id = $_POST['category_id'];
            $args['category'] = $event_categories_id;
        }
                             
        // Global Settings
        $bc_event_settings = [
            'pagination'    => get_field('turn_on_pagination', 'option'),
            'limit'         => get_field('post_limit', 'option'),    
            'cat'           => get_field('event_category_id'),                   
        ];

        $bc_event_pagination = $bc_event_settings['pagination'] ? 1 : 0;
        
        // Event Default Args
        $event_default_args = [            
            'ajax'          => '1',                                 
            'long_events'   => 1,
            'page_queryvar' => 'pno',                                            
        ];
               
        //Gather all args     
        $combined_args = '';
        if(is_array($args)) {
            $combined_args = array_merge($args, $event_default_args);
        } else {
            $combined_args = $event_default_args;
        }   
                                                           
        // Query Events
        $BC_Events = EM_Events::get($combined_args); 
        $events_count = EM_Events::$num_rows_found;  
                                             
        if($events_count > 0) {                                               
            // Show events & Formats
            include( plugin_dir_path( __DIR__ ) . 'blocks/event-list-layout.php');
            $event_list = implode(',', self::eventSort($BC_Events));                           
            echo do_shortcode('[events_list 
                                post_id="'. $event_list .'" 
                                limit="'. $bc_event_settings['limit'] .'" 
                                pagination="'. $bc_event_pagination .'"   
                                category="'. $bc_event_settings['cat'] .'"                              
                                ajax="1"]
                                '.$format.'
                                [/events_list]
                                '                            
                            );

            // Schema for loop
            // Get current events being called
            $schema_args = [
                'post_id'   => $event_list,
                'category'  => $bc_event_settings['cat'],                              
            ];                
            self::bcEventLoopSchema($schema_args);

        }  else {
            echo '<div class="bc-plugin-event-list__single -empty"><h4>No Events</h4></div>';
        }                                     
    }    
}
?>