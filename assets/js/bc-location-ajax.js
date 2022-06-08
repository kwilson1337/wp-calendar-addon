jQuery(document).ready(function() {
    const eventsList =  document.getElementById('eventList');

    if(eventsList) {
        // Layout buttons
        const listView = document.querySelector('#bcList');
        const gridView = document.querySelector('#bcGrid');
        const eventOutPut = document.querySelector('.bc-plugin-event-list__output');

        // Toggle between list/grid views
        listView.addEventListener('click', function() {
            eventOutPut.classList.remove('-grid');        
        });
        gridView.addEventListener('click', function() {
            eventOutPut.classList.remove('-list');
            eventOutPut.classList.add('-grid');
        });
        
        // Grab Category IDs
        const categoryId = document.querySelector('.bc-plugin-event-list__output').dataset.locationcategory;              

        // Start of ajax
        const locationSelect = jQuery('#bcLocations');
        locationSelect.on('change', function() {
            const locationID = locationSelect.val();
        
            // handles ajax
            jQuery.ajax({
                url: locationAjax.locationAjaxUrl,            
                data: {
                    action: 'location_filter', 
                    location_id: locationID,   
                    category_id: categoryId,
                    url_base: window.location.href,                               
                },            
                type: 'post',
                beforeSend : function() {
                
                },
                success: function(result){                   
                    jQuery('.bc-plugin-event-list__output').html(result);     
                    jQuery('.bc-plugin-event-list__single').addClass('-is-loaded');                                                                                                                                                                                      
                },
                complete: function() {   
                    
                },
                error: function(result) {
                    console.log(result);
                }
            })
        });
    }
});