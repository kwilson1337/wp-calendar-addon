<?php 
/**
 * Layout used for BC Event List Block
 */

$format =
'
<div class="bc-plugin-event-list__single '. BcEventList::titleOverlay() .'">
    <div class="bc-plugin-event-list__left">
        <div class="image-container">
            {has_image}
                <img class="img-fluid" src="#_EVENTIMAGEURL' .'" alt="#_EVENTNAME">
            {/has_image}
            {no_image}
                <img class="img-fluid" src="'. plugin_dir_url( __DIR__ ) . '/images/ticket-image.jpg' .'" alt="#_EVENTNAME">
            {/no_image}

            <div class="title-overlay">
                <h4 class="event-title">#_EVENTNAME</h4>
            </div>
        </div>   
        
        <div class="event-info">
            <h4 class="event-title">#_EVENTNAME</h4>

            <div class="event-meta-data">  
                {not_long}      
                    <p class="event-date"><i class="fas fa-calendar"></i> #_{M} #_{d}, #_12HSTARTTIME - #_12HENDTIME</p>
                {/not_long}

                {is_long}
                <p class="event-date"><i class="fas fa-calendar"></i> #M #j #@_{ \-\ M j}</p>
                {/is_long}

                {has_location}
                    <a target="_blank" href="#_LATT{Address_Link}">
                    <i class="fas fa-map-marker-alt"></i> 
                    #_LOCATIONADDRESS #_LOCATIONTOWN #_LOCATIONSTATE #_LOCATIONPOSTCODE
                    </a>
                {/has_location}
            </div>
        </div>
    </div>

    <div class="bc-plugin-event-list__event-link">
        <a href="#_EVENTURL" class="bc-button -button1">Event Info</a>
    </div>    
</div>
';
?>