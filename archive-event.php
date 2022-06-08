<?php 
/**
 * Used to display All Events
 */

get_header(); 

// Get end date from URL
$current_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
$filtered_url = basename($current_url);

// New scope for loop
$args = [];
if(strtotime($filtered_url)) {
    $args['scope'] = $filtered_url;
} else {
    $args['scope'] = 'future';
}

$archive_event_banner = [
    'inc_banner'    => get_field('include_em_archive_banner', 'option'),        
    'img'           => get_field('em_archive_background_image', 'option'),
    'video'         => get_field('em_archive_background_video', 'option'),
    'title'         => get_field('em_archive_banner_title', 'option'),
    'sub_title'     => get_field('em_archive_banner_sub_title', 'option'),
    'btn'           => get_field('em_archive_banner_button', 'option'),    
    'overlay'       => get_field('em_archive_banner_overlay', 'option'),    
    'desc'          => get_field('em_archive_banner_description', 'option'),    
];
?>

<?php if($archive_event_banner['inc_banner']): ?>
<section 
    class="bc-banner bg-cover bc-plugin-single-event
    <?php echo $banner_layout; ?>
    <?php echo $archive_event_banner['video'] ? "-overflow" : ""; ?>" 
    style="background-image:url(<?php echo $archive_event_banner['img']['url'] ?: get_the_post_thumbnail_url(); ?>)"
>    
    <?php if ($archive_event_banner['video']): ?> <!-- Video -->        
        <video class="bc-banner__video" autoplay muted loop playsinline>
            <source src="<?php echo $archive_event_banner['video']; ?>" type="video/mp4">
        </video>    
    <?php endif; ?> <!-- //Video -->
    
    <div class="container"><!-- Container -->
        <div class="bc-banner__content">
            <div class="bc-banner__content-container">
                <div class="bc-banner__title">
                    <h1 class="h1"><?php echo $archive_event_banner['title'] ?: 'Events'; ?></h1>                
                    <?php if ($archive_event_banner['sub_title']): ?>
                        <h2 class="h2"><?php echo $archive_event_banner['sub_title']; ?></h2>
                    <?php endif; ?>
                    <?php if ($archive_event_banner['desc']): ?>
                        <span class="-desc"><?php echo $archive_event_banner['desc'] ?></span>
                    <?php endif; ?>
                </div>
                
                <?php if ($archive_event_banner['btn']): ?>
                    <div class="bc-banner__button">
                        <?php if($archive_event_banner['btn']): ?>
                            <a href="<?php echo $archive_event_banner['btn']['url']; ?>" class="bc-button -button1"><?php echo $archive_event_banner['btn']['title']; ?></a>
                        <?php endif; ?>                       
                    </div>
                <?php endif; ?>
            </div> <!-- //__content-container -->
            
        </div> <!-- //__content -->
    </div><!-- //Container -->

    <?php if ($archive_event_banner['overlay']): ?>
        <div class="bc-banner__overlay"></div><!-- //overlay -->
    <?php endif; ?>
</section> 
<?php endif; ?> <!-- //Banner -->

<section id="eventList" class="bc-plugin-event-list -archive-page">
    <div class="container">   
        <div class="bc-plugin-event-list__options -between">
            <div class="options-right">
                <select name="bcLocations" id="bcLocations" class="form-control">
                    <option value="">All Locations</option>  

                    <?php foreach(EM_Locations::get() as $BC_locations) : ?>
                        <option value="<?php echo $BC_locations->location_id; ?>"><?php echo $BC_locations->location_name; ?></option>
                    <?php endforeach; ?>
                </select>  
            </div>              

            <div class="options-left">
                <button id="bcList" class="bc-layout-button"><i class="fas fa-list"></i></button>
                <button id="bcGrid" class="bc-layout-button"><i class="fas fa-th-large"></i></button>
            </div>
        </div>

        <div class="bc-plugin-event-list__output <?php echo BcEventList::listOrGrid(); ?> <?php echo BcEventList::postsPerRow(); ?>">           
            <?php echo BcEventList::bcEventLoop($args); ?>                   
        </div>                    
    </div>    
</section>

<?php get_footer(); ?>