<?php 
/**
 * Schema generated for events in the loop
 */
use Spatie\SchemaOrg\Schema;

// Format dates/times correctly
$event_date = '';
if($single_event_schema->event_start_date === $single_event_schema->event_end_date) {
    $event_date = date("M j", strtotime($single_event_schema->event_start_date)) . ", " . date("g:i A", strtotime($single_event_schema->event_start_time));                            
} else {
    $event_date = date("M j", strtotime($single_event_schema->event_start_date)) . " - " . date("M j", strtotime($single_event_schema->event_end_date));
}  

if($single_event_schema->location_id !== 0) {
    $location_args = [
        'location'  => $single_event_schema->location_id,                        
    ];
}
   
// Global location for schema
$global_location = get_field('event_global_location');

// Get location built into plugin
$schema_location = '';
if($single_event_schema->location_id !== 0) {
    $schema_location = EM_Locations::get($location_args);
} else {
    $schema_location = false;
}

// Performer Logic
$performer_name = [];
if(get_field('event_performers', $single_event_schema->post_id)) {
    foreach(get_field('event_performers', $single_event_schema->post_id) as $single_performer) {
        $performer_name[] = $single_performer['perfromer_name'];
    }       
}        
    
// Schema
echo Schema::event()
    ->name($single_event_schema->event_name)
    ->startDate($single_event_schema->event_start_date)
    ->endDate($single_event_schema->event_end_date . 'T' .$single_event_schema->event_end_time)
    ->location(
        Schema::place()
        ->name(
            (isset($schema_location[0]->location_name) ? $schema_location[0]->location_name : Site::companyName($global_location))
        )
        ->address(
            Schema::postalAddress()
            ->streetAddress(isset($schema_location[0]->location_address) ? $schema_location[0]->location_address : Site::street($global_location))
            ->addressLocality(isset($schema_location[0]->location_town) ? $schema_location[0]->location_town : Site::city($global_location))
            ->postalCode(isset($schema_location[0]->location_postcode) ? $schema_location[0]->location_postcode : Site::zip($global_location))
            ->addressRegion(isset($schema_location[0]->location_state) ? $schema_location[0]->location_state : Site::state($global_location))
            ->addressCountry('US')
        )
    )  
    ->organizer(
        Schema::Organization()
        ->name("Organization")
        ->url(get_site_url())
    )
    ->eventAttendanceMode(isset($schema_location[0]->location_address) || Site::street($global_location) ? 'OfflineEventAttendanceMode' : 'OnlineEventAttendanceMode')        
    ->eventStatus('EventScheduled')
        ->offers(
            Schema::offer()
            ->url(get_site_url())                
            ->price(isset($single_event_schema->event_attributes['ticket_starts_at_price']) ? preg_replace('/[^0-9]/', '', $single_event_schema->event_attributes['ticket_starts_at_price']) : 'Free')                
            ->priceCurrency('USD')
            ->availability('https://schema.org/InStock')
            ->validFrom($single_event_schema->event_end_date . 'T' .$single_event_schema->event_end_time)
        )
    ->image((get_the_post_thumbnail_url($single_event_schema->post_id)) ? get_the_post_thumbnail_url($single_event_schema->post_id) : get_field('banner_background_image', $single_event_schema->post_id)['url'])
    ->description($single_event_schema->post_content)
    ->performer(!empty($performer_name)
        ?
        array_map(function ($name) {
            return Schema::performingGroup()->name($name);
        }, $performer_name)
        :
        null
    );