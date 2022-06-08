/**
 * Gallery
 */
jQuery(document).ready(function() {
  jQuery('.gallery-images').slick({
      infinite: false,
      slidesToShow: 4,
      slidesToScroll: 1,
      prevArrow: $('.arrows.-prev'),
      nextArrow: $('.arrows.-next'),
      responsive: [
          {
            breakpoint: 991,
            settings: {
              slidesToShow: 3,                
            }
          },
          {
            breakpoint: 768,
            settings: {
              slidesToShow: 2,                
            }
          },            
      ]
    });
});