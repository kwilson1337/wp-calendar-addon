<?php 
/**
 * bc_events short code
 * feel free to modify shortcode
 * or create new ones in this file
 */
add_shortcode('bc_events', function($args) {
    // Settings
    $limit = isset($args['limit']) ? $args['limit'] : null;    
    $event_id = isset($args['id']) ? $args['id'] : null;
    $location = isset($args['location']) ? $args['location'] : null;
    $pagination = isset($args['pagination']) ? $args['pagination'] : 0;
    $category = isset($args['category']) ? $args['category'] : null;
    $title = isset($args['title']) ? $args['title'] : null;
    $grid = isset($args['grid']) ? $args['grid'] : null;
    $container = isset($args['container']) && $args['container'] == "1" ? 'class="container"' : null;         
    $class = isset($args['class']) ? $args['class'] : null;
    $scope = isset($args['scope']) ? $args['scope'] : '';
    $inc_img = ''; 
    if(isset($args['inc_img']) && $args['inc_img'] == '1') {
        $inc_img = '
        <div class="feat-image">
            <img class="img-fluid" src="#_EVENTIMAGEURL" alt="#_EVENTNAME"> 
        </div>
        ';
    }
  
    // Get current events being called
    $short_code_args = [        
        'category'  => $category,   
        'location'  => $location,               
    ];   
                    
    // Get all Event IDs including take over events
    $gather_all_event_ids = [];       
    if(!isset($event_id)) {        
        $gather_all_event_ids = implode(', ',BcEventList::eventSort(EM_Events::get($short_code_args)));
    } else {
        $gather_all_event_ids = $event_id;
    }    

    ob_start();               
    ?>
    <div class="bc-plugin-event-shortcode <?php echo $class; ?>">           
        <div <?php echo $container; ?>>
            <?php if($title): ?>
                <div class="bc-plugin-event-shortcode__title">
                    <h2><?php echo $title; ?></h2>
                </div>
            <?php endif; ?>

            <div class="bc-plugin-event-shortcode__grid -<?php echo $grid; ?>">                               
                <?php
                echo do_shortcode('[events_list
                                        ajax="1"
                                        pagination="'. $pagination .'" 
                                        limit="'. $limit .'"
                                        category="'. $category .'" 
                                        post_id="'. $gather_all_event_ids .'"   
                                        location="'. $location .'"
                                        scope="'. $scope .'"     
                                    ]
                                    <div class="bc-plugin-event-shortcode__single">
                                        {has_image}
                                            '. $inc_img .'
                                        {/has_image}

                                        <div class="event-info -title">
                                            <p>#_EVENTNAME</p>
                                        </div>

                                        <div class="event-info -date-time">
                                            <p class="date">#M #j #@_{ \-\ M j}</p>
                                            <p class="time">#_EVENTTIMES</p>
                                        </div>

                                        <div class="event-info -desc">
                                            #_EVENTEXCERPT{15,...}
                                        </div>

                                        <div class="event-info -button-container">
                                            <a href="#_EVENTURL" class="bc-button -button1">Event Info</a>
                                        </div>
                                    </div>
                                    [/events_list]
                                    '                        
                                );
                ?>                
            </div>
        </div>
    </div>
    <?php        
    // Shortcode Schema
    // Create array with new set of IDs
    $schema_args = [
        'post_id'   => $gather_all_event_ids,
    ];
    // Merge both arrays together to make complete argument list
    $gather_schema_args = array_merge($schema_args, $short_code_args);       
    echo BcEventList::bcEventLoopSchema($gather_schema_args);
    
    return ob_get_clean();
});
?>