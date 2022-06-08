<?php
/**
 * Used to display single Event pages
 */

get_header(); 

//Schema
include_once('schema/event-schema.php');
    echo $schema -> event_schema();   
    
$bc_event_global_options = [
    'banner_layout' => get_field('banner_layout', 'option'),

    'img'       => get_field('single_event_banner_image', 'option'),
    'vid'       => get_field('single_event_banner_video', 'option'),
    'overlay'   => get_field('single_event_banner_overlay', 'option'),
    'title'     => get_field('single_event_banner_title', 'option'),
    'sub_title' => get_field('single_event_banner_sub_title', 'option'),
];

$banner_layout = '';
if($bc_event_global_options['banner_layout'] == 'left') {
    $banner_layout = '-align-left';
} elseif($bc_event_global_options['banner_layout'] == 'right') {
    $banner_layout = '-align-right';
} else {
    $banner_layout = '';
}

// Banner options
$event_banner = [
    'inc_banner'    => get_field('include_banner'),        
    'img'           => get_field('banner_background_image'),
    'video'         => get_field('banner_background_video'),
    'title'         => get_field('banner_title'),
    'sub_title'     => get_field('banner_sub_title'),
    'btn'           => get_field('banner_button'),
    'btn_two'       => get_field('banner_button_two'),
    'overlay'       => get_field('include_overlay'),    
    'desc'          => get_field('banner_description'),
    'align_desc'    => get_field('banner_aligned_content'),
];

// Event information
$event_info = [
    'c_button_one'  => get_field('event_custom_button_one'),
    'c_button_two'  => get_field('event_custom_button_two'),
    'ticket_start'  => get_field('ticket_starts_at_price'),
    'tickets'       => get_field('tickets'),
];

// Date formating
$bc_event_date = '';
if($EM_Event->event_start_date == $EM_Event->event_end_date) {                    
    $bc_event_date = date("M j Y", strtotime($EM_Event->event_start_date));
} else {
    $start_date = date("M j Y", strtotime($EM_Event->event_start_date));
    $end_date = date("M j Y", strtotime($EM_Event->event_end_date));
    $bc_event_date = $start_date . ' - ' . $end_date;
}     

// Location formating
$bc_location = '';
if(!empty($EM_Event->location)) {      
    $bc_location = $EM_Event->location->location_address . ' ' . $EM_Event->location->location_town . ' ' . $EM_Event->location->location_state . ' ' . $EM_Event->location->location_postcode;    
}

//Banner BG Image options
$bc_single_event_bg_img = '';
if($event_banner['img']) {
    $bc_single_event_bg_img = $event_banner['img']['url'];
} elseif ($bc_event_global_options['img']) {
    $bc_single_event_bg_img = $bc_event_global_options['img']['url'];
} else {
    $bc_single_event_bg_img = get_the_post_thumbnail_url();
}

//Banner Video options
$bc_single_event_bg_vid = '';
if($event_banner['video']) {
    $bc_single_event_bg_vid = $event_banner['video'];
} elseif($bc_event_global_options['vid']) {
    $bc_single_event_bg_vid =  $bc_event_global_options['vid'];
} 

// Banner Title Options
$bc_single_event_title = get_the_title();
if($event_banner['title']) {
    $bc_single_event_title = $event_banner['title'];
} elseif($bc_event_global_options['title']) {
    $bc_single_event_title = $bc_event_global_options['title'];
}

// Banner Sub Title Options
$bc_single_event_sub_title = '';
if($event_banner['sub_title']) {
    $bc_single_event_sub_title = $event_banner['sub_title'];
} elseif($bc_event_global_options['sub_title']) {
    $bc_single_event_sub_title = $bc_event_global_options['sub_title'];
}
?>

<?php if($event_banner['inc_banner']): ?>
<section 
    class="bc-banner bg-cover bc-plugin-single-event
    <?php echo $banner_layout; ?>
    <?php echo $event_banner['video'] || $bc_event_global_options['vid'] ? "-overflow" : ""; ?>" 
    style="background-image:url(<?php echo $bc_single_event_bg_img; ?>)"
>    
    <?php if ($event_banner['video'] || $bc_event_global_options['vid']): ?> <!-- Video -->        
        <video class="bc-banner__video" autoplay muted loop playsinline>
            <source src="<?php echo $bc_single_event_bg_vid; ?>" type="video/mp4">
        </video>    
    <?php endif; ?> <!-- //Video -->
    
    <div class="container"><!-- Container -->
        <div class="bc-banner__content">
            <div class="bc-banner__content-container">
                <div class="bc-banner__title">
                    <h1 class="h1"><?php echo $bc_single_event_title; ?></h1>                
                    <?php if ($event_banner['sub_title'] || $bc_event_global_options['sub_title']): ?>
                        <h2 class="h2"><?php echo $bc_single_event_sub_title; ?></h2>
                    <?php endif; ?>
                    <?php if ($event_banner['desc']): ?>
                        <span class="-desc"><?php echo $event_banner['desc'] ?></span>
                    <?php endif; ?>
                </div>

                <div class="bc-banner__date">
                    <p><i class="fas fa-calendar"></i> <?php echo $bc_event_date; ?></p>
                </div>

                <?php if(!empty($EM_Event->location)): ?>
                    <div class="bc-banner__location">
                        <a target="_blank" rel="noopener noreferrer" href="<?php echo do_shortcode('[event id="'. $EM_Event->post_id .'"]#_LATT{Address_Link}[/event]'); ?>">
                            <i class="fas fa-map-marker-alt"></i> <?php echo $bc_location; ?>
                        </a>
                    </div>
                <?php endif; ?>

                <?php if($event_info['ticket_start']): ?>
                    <div class="bc-banner__ticket-start">
                        <p><i class="fas fa-ticket-alt"></i> Starting at <?php echo $event_info['ticket_start']; ?></p>
                    </div>
                <?php endif; ?>

                <?php if ($event_banner['btn'] || $event_banner['btn_two']): ?>
                    <div class="bc-banner__button">
                        <?php if($event_banner['btn']): ?>
                            <a href="<?php echo $event_banner['btn']['url']; ?>" class="bc-button -button1"><?php echo $event_banner['btn']['title']; ?></a>
                        <?php endif; ?>

                        <?php if($event_banner['btn_two']): ?>
                            <a href="<?php echo $event_banner['btn_two']['url']; ?>" class="bc-button -button1"><?php echo $event_banner['btn_two']['title']; ?></a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div> <!-- //__content-container -->

            <?php if($bc_event_global_options['banner_layout'] == 'left' 
                    || $bc_event_global_options['banner_layout'] == 'right' 
                    && $event_banner['align_desc']): 
            ?>
                    <div class="bc-banner__aligned-content">
                        <?php echo $event_banner['align_desc']; ?>
                    </div>
            <?php endif; ?> <!-- // __aligned-content -->

        </div> <!-- //__content -->
    </div><!-- //Container -->

    <?php if ($event_banner['overlay'] || $bc_event_global_options['overlay']): ?>
        <div class="bc-banner__overlay"></div><!-- //overlay -->
    <?php endif; ?>
</section> 
<?php endif; ?> <!-- //Banner -->

<section <?php echo body_class($class = 'bc-p-' . get_post_field('post_name', get_post()) . ' bc-plugin-single-event') ?>>
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="bc-plugin-single-event__top">
                    <div class="event-title">
                        <?php 
                        if($event_banner['inc_banner']): ?>
                            <h2><?php the_title(); ?></h2>
                        <?php 
                        else : ?>
                            <h1><?php the_title(); ?></h1>
                        <?php 
                        endif; 
                        ?> 
                    </div>   
                    
                    <?php if($event_info['c_button_one']): ?>
                        <div class="custom-button-one">
                            <a href="<?php echo $event_info['c_button_one']['url']; ?>" class="bc-button -button1"><?php echo $event_info['c_button_one']['title']; ?></a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div> <!-- //__top -->

        <div class="row">
            <div class="col-md-8">
                <div class="bc-plugin-single-event__left">
                    <?php 
                    // A little hacky but it works
                    echo do_shortcode('[event id="'. $EM_Event->post_id .'"]#_EVENTNOTES[/event]')
                    ?>
                </div>
            </div>

            <div class="col-md-4">
                <div class="bc-plugin-single-event__right">
                    <?php if($event_info['tickets']): ?>
                        <div class="bc-plugin-single-event__tickets">
                            <h4 class="section-title">Tickets</h4>

                            <?php foreach($event_info['tickets'] as $single): ?>
                                <div class="single-ticket">
                                    <div class="name-price">
                                        <?php if($single['ticket_name']): ?>
                                            <p class="name h5"><?php echo $single['ticket_name']; ?></p>
                                        <?php endif; ?>

                                        <?php if($single['ticket_price']): ?>
                                            <p class="price"><?php echo $single['ticket_price']; ?></p>
                                        <?php endif; ?>
                                    </div> <!-- // name-price -->

                                    <?php if($single['ticket_description']): ?>
                                        <div class="ticket-desc">
                                            <p><?php echo $single['ticket_description']; ?></p>
                                        </div>
                                    <?php endif; ?> <!-- //ticket-desc -->

                                    <?php if($single['ticket_link']): ?>
                                        <div class="ticket-button">
                                            <a href="<?php echo $single['ticket_link']['url']; ?>" class="bc-button -button1">
                                                <?php echo $single['ticket_link']['title']; ?>
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?> <!-- //__tickets -->

                    <div class="bc-plugin-single-event__event-details">
                        <h4 class="section-title">Event Details</h4>

                        <div class="single-detail -date">
                            <p>
                                <i class="fas fa-calendar"></i> 
                                <span>
                                    <?php echo $bc_event_date; ?> <br><?php echo date("g:i A", strtotime($EM_Event->event_start_time)); ?>
                                </span>
                            </p>
                        </div> <!-- // single-detail -date -->

                        <?php if(!empty($EM_Event->location)): ?>
                            <div class="single-detail -location">
                                <p>
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span>
                                        <strong><?php echo $EM_Event->location->location_name; ?></strong> <br />                                        
                                        <?php echo $bc_location; ?> <br />
                                        <a target="_blank" rel="noopener noreferrer" href="<?php echo do_shortcode('[event id="'. $EM_Event->post_id .'"]#_LATT{Address_Link}[/event]'); ?>">View Map</a>
                                    </span>
                                </p>                                
                            </div>
                        <?php endif; ?> <!-- // single-detail -location -->

                        <?php if($event_info['ticket_start']): ?>
                            <div class="single-detail -ticket-price">
                                <p><i class="fas fa-ticket-alt"></i> <span>Starting at <?php echo $event_info['ticket_start']; ?></span></p>
                            </div>
                        <?php endif; ?> 

                        <div class="event-share">
                            <p class="h4">Share this event</p>
                            <?php echo do_shortcode('[share_links]'); ?>
                        </div> <!-- // event-share -->

                        <?php if($event_info['c_button_two']): ?>
                            <div class="custom-button-two">
                                <a href="<?php echo $event_info['c_button_two']['url']; ?>" class="bc-button -button1">
                                    <?php echo $event_info['c_button_two']['title']; ?>
                                </a>
                            </div>
                        <?php endif; ?>
                    </div> <!-- //__event-details-->
                </div>
            </div>
        </div> <!-- // event info row 0-->        
    </div> <!-- //container -->

    <?php 
    $event_schedule = [
        'title' => get_field('event_schedule_title'),
        'desc'  => get_field('event_schedule_description'),
        'sched' => get_field('event_schedule'),
    ];

    if($event_schedule['sched']):
    ?>
    <div class="bc-plugin-single-event__schedule">
        <div class="container">
            <?php if($event_schedule['title'] || $event_schedule['desc']): ?>
                <div class="schedule-title">
                    <?php if($event_schedule['title']): ?>
                        <h2><?php echo $event_schedule['title']; ?></h2>
                    <?php endif; ?>

                    <?php 
                    if($event_schedule['desc']):
                        echo $event_schedule['desc'];
                    endif;
                    ?>
                </div>
            <?php endif; ?>
            
            <nav>
                <div class="nav nav-tabs single-event-tabs nav-collapse" id="bcScheduleNav" role="tablist">
                    <?php 
                    $i = 0;
                    foreach($event_schedule['sched'] as $single): ?>                    
                        <a class="nav-item nav-link single-tab <?php echo $i == 0 ? "active" : false; ?>" id="nav-profile-tab" data-toggle="tab" href="#event-<?php echo $i; ?>" role="tab" aria-controls="<?php echo $single['event_schedule_label']; ?>" aria-selected="false">
                            <?php echo $single['event_schedule_label']; ?>
                        </a>                    
                    <?php 
                    $i++;
                    endforeach; ?>
                </div>
            </nav>
            
            <div class="tab-content event-list" id="nav-tabContent">                
                <?php 
                $i = 0;
                foreach($event_schedule['sched'] as $single): ?>
                    <div class="tab-pane fade <?php echo $i == 0 ? "show active" : false; ?>" id="event-<?php echo $i; ?>" role="tabpanel" aria-labelledby="<?php echo $single['event_schedule_label']; ?>-tab">
                        <?php 
                        if($single['events']):
                            foreach($single['events'] as $single_event): ?>
                                <div class="single-event">
                                    <div class="single-event-image">
                                        <?php if($single_event['event_image']): ?>
                                            <img src="<?php echo $single_event['event_image']['url']; ?>" alt="<?php echo $single_event['event_image']['alt']; ?>" class="img-fluid">
                                        <?php else: ?>
                                            <img src="<?php echo plugin_dir_url( __FILE__ ) . 'images/generic-event-image.png'; ?>" alt="Event Image" class="img-fluid">
                                        <?php endif; ?>
                                    </div>

                                    <div class="single-event-content">
                                        <?php if($single_event['event_time']): ?>
                                            <p class="single-event-meta -time"><?php echo $single_event['event_time']; ?></p>
                                        <?php endif; ?>

                                        <?php if($single_event['event_title']): ?>
                                            <p class="single-event-meta -title"><?php echo $single_event['event_title']; ?></p>
                                        <?php endif; ?>

                                        <?php if($single_event['event_description']): ?>
                                            <div class="single-event-meta -desd">
                                                <?php echo $single_event['event_description']; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                        <?php                         
                            endforeach; 
                        endif;
                        ?>
                    </div>                
                <?php
                $i++;
                endforeach; ?>
            </div>
        </div>     
    </div>
    <?php
    endif;
    ?>
    <?php 
    $event_gallery = [
        'title'     => get_field('event_gallery_title'),
        'desc'      => get_field('event_gallery_description'),
        'gallery'   => get_field('event_gallery_images'),
    ];

    if($event_gallery['gallery']):
    ?>
    <div class="bc-plugin-single-event__gallery">
        <div class="container">
            <?php if($event_gallery['title'] || $event_gallery['desc']): ?>
                <div class="gallery-title">
                    <?php if($event_gallery['title']): ?>
                        <h2><?php echo $event_gallery['title']; ?></h2>
                    <?php endif; ?>

                    <?php 
                    if($event_gallery['desc']):
                        echo $event_gallery['desc'];
                    endif;
                    ?>
                </div>                
            <?php endif; ?>

            <?php if($event_gallery['gallery']): ?>
                <div class="gallery-images">
                    <?php foreach($event_gallery['gallery'] as $single): ?>
                        <div class="single-image">
                            <a href="<?php echo $single['url']; ?>" data-lightbox="Event Gallery">
                                <img src="<?php echo $single['url']; ?>" alt="<?php echo $single['alt']; ?>" class="img-fluid">
                            </a>
                        </div>
                    <?php endforeach; ?>                        
                </div>

                <div class="gallery-arrows">
                    <div class="arrows -prev">
                        <i class="fas fa-long-arrow-left"></i>
                    </div>

                    <div class="arrows -next">
                        <i class="fas fa-long-arrow-right"></i>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div> <!-- //__gallery -->
    <?php 
    endif; 
    ?>
</section><!-- //Event body -->

<?php get_footer(); ?>