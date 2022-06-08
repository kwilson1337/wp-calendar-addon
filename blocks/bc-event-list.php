<?php 
/**
 * BC Event Block
 * Logic : class/bc-event-list-loop.php
 */

$event_block = [
    'inc_form'  => get_field('show_location_form'),
    'cat_id'    => get_field('event_category_id'),
    'class'     => get_field('event_list_class'),
];
?>
<section id="eventList" class="bc-plugin-event-list <?php echo $event_block['class'] ?: null; ?>">
    <div class="container">   
        <div class="bc-plugin-event-list__options <?php echo $event_block['inc_form'] ? "-between" : null; ?>">
            <?php if($event_block['inc_form']): ?>
                <div class="options-right">
                    <select name="bcLocations" id="bcLocations" class="form-control">
                        <option value="">All Locations</option>  

                        <?php foreach(EM_Locations::get() as $BC_locations) : ?>
                            <option value="<?php echo $BC_locations->location_id; ?>"><?php echo $BC_locations->location_name; ?></option>
                        <?php endforeach; ?>
                    </select>  
                </div>    
            <?php endif; ?>         

            <div class="options-left">
                <button id="bcList" class="bc-layout-button"><i class="fas fa-list"></i></button>
                <button id="bcGrid" class="bc-layout-button"><i class="fas fa-th-large"></i></button>
            </div>
        </div>

        <div data-locationCategory="<?php echo $event_block['cat_id']; ?>" class="bc-plugin-event-list__output <?php echo BcEventList::listOrGrid(); ?> <?php echo BcEventList::postsPerRow(); ?>">           
            <?php echo BcEventList::bcEventLoop(); ?>                   
        </div>               
    </div>    
</section>