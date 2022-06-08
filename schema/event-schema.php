<?php 
/**
 * Schema generated for single events page
 */

use Spatie\SchemaOrg\Schema;

class eventSchema
{            
    public static function event_title() {
        return get_the_title();
    }

    public static function event_start_date_time() {  
        global $EM_Event;
        
        $start_time = $EM_Event->event_start_time;
        $start_date = $EM_Event->event_start_date;

        return $start_date . 'T' . $start_time;
    }

    public static function event_end_date_time() {
        global $EM_Event;
        
        $end_time = $EM_Event->event_end_time;
        $end_date = $EM_Event->event_end_date;

        return $end_date . 'T' . $end_time;
    }

    public static function event_location_name() {
        global $EM_Event;
        $global_location = get_field('event_global_location');

        if(isset($EM_Event->location->location_name)) {
           return $EM_Event->location->location_name;
        } else {
            return Site::companyName($global_location);
        }
    }

    public static function event_street_address() {
        global $EM_Event;
        $global_location = get_field('event_global_location');

        if(isset($EM_Event->location->location_address)) {
            return $EM_Event->location->location_address;
         } else {
            return Site::street($global_location);
        }
    }

    public static function event_city() {
        global $EM_Event;
        $global_location = get_field('event_global_location');

        if(isset($EM_Event->location->location_town)) {
            return $EM_Event->location->location_town;
        } else {
            return Site::city($global_location);
        }
    }

    public static function event_zip_code() {
        global $EM_Event;
        $global_location = get_field('event_global_location');

        if(isset($EM_Event->location->location_postcode)) {
            return $EM_Event->location->location_postcode;
        } else {
            return Site::zip($global_location); 
        }
    }

    public static function event_state() {
        global $EM_Event;
        $global_location = get_field('event_global_location');

        if(isset($EM_Event->location->location_state)) {
            return strtoupper(substr($EM_Event->location->location_state, 0, 2));
        } else {
            return Site::state($global_location);
        }
    }

    public static function event_country() {    
        return 'US';
    }

    public static function event_image() {    
        if(get_the_post_thumbnail_url()) {
            return get_the_post_thumbnail_url();        
        } else {
            return get_field('banner_background_image')['url'];
        }
    }

    public static function event_description() {   
        global $EM_Event;
        
        if(isset($EM_Event->post_content)) {            
            return $EM_Event->post_content;
        } else {
            return false;
        }
    }

    public static function event_price() {    
        if (get_field('ticket_starts_at_price')) {           
            $schema_price = preg_replace('/[^0-9]/', '', get_field('ticket_starts_at_price'));                     
            return $schema_price;
        } else {
            return '0';
        }                 
    }

    public static function event_performer() {
        if(get_field('event_performers')) {
            $name = [];
            foreach(get_field('event_performers') as $single) {
                $name[] = $single['perfromer_name'];
            }
            return $name;
        } else {
            return false;
        }
    }

    public static function url() {    
        $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
        return $actual_link;
    }

    public static function event_status() {
        return "EventScheduled";
    }

    public static function event_attendance_mode() {
        return "OfflineEventAttendanceMode";
    }

    public static function organizer() {
        return "Organization";
    }

    public static function organizer_url() {
        return get_site_url();
    }  
    
    public static function event_ticket_link() {    
        if (get_field('tickets')) {
            $ticket_links = [];
            foreach(get_field('tickets') as $single_link) {               
                if(!empty($single_link['ticket_link']['url'])) {
                    $ticket_links[] = $single_link['ticket_link']['url'];
                }                
            }
            return $ticket_links;
        } else {
            return false;
        }
    }
    
    public function event_schema() {
        echo '<!-- SINGLE EVENT SCHEMA -->';

        echo Schema::event()
            ->name(eventSchema::event_title())
            ->startDate(eventSchema::event_start_date_time())
            ->location(
                Schema::place()
                ->name(eventSchema::event_location_name())
                ->address(
                    Schema::postalAddress()
                    ->streetAddress(eventSchema::event_street_address())
                    ->addressLocality(eventSchema::event_city())
                    ->postalCode(eventSchema::event_zip_code())
                    ->addressRegion(eventSchema::event_state())
                    ->addressCountry(eventSchema::event_country())
                )
            )
            ->organizer(
                Schema::Organization()
                ->name(eventSchema::organizer())
                ->url(eventSchema::organizer_url())
            )
            ->if(eventSchema::event_street_address(), function($schema){
                $schema->eventAttendanceMode(eventSchema::event_attendance_mode());
            })
            ->eventStatus(eventSchema::event_status())
            ->offers(
                Schema::offer()
                ->url(eventSchema::url())                
                ->if(eventSchema::event_price(), function($schema) {
                    $schema->price(eventSchema::event_price());
                })
                ->priceCurrency('USD')
                ->availability('https://schema.org/InStock')
                ->if(eventSchema::event_end_date_time(), function ($schema) {
                    $schema->validFrom(eventSchema::event_end_date_time());
                })
            )
            ->image(eventSchema::event_image())
            ->description(eventSchema::event_description())
            ->if(eventSchema::event_end_date_time(), function ($schema) {
                $schema->endDate(eventSchema::event_end_date_time());
            })
            ->if(eventSchema::event_ticket_link(), function ($schema) {
                $schema->offers(
                    Schema::offer()
                    ->url(eventSchema::event_ticket_link()) 
                    ->if(eventSchema::event_price(), function($schema) {
                        $schema->price(eventSchema::event_price());
                    })                   
                    ->priceCurrency('USD')
                    ->availability('https://schema.org/InStock')
                    ->if(eventSchema::event_end_date_time(), function ($schema) {
                        $schema->validFrom(eventSchema::event_end_date_time());
                    })
                );
            })
            ->if(eventSchema::event_performer(), function ($schema) {
                $schema->performer(
                    array_map(function ($name) {
                        return Schema::performingGroup()->name($name);
                    }, eventSchema::event_performer())
                );
            });

        echo '<!-- //SINGLE EVENT SCHEMA -->';
    } 
}

if(class_exists('eventSchema')) {
    $schema = new eventSchema();
}