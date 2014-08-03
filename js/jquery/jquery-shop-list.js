jQuery.noConflict();
jQuery(document).ready(function($){
	//console.log('every thing ok');
		function sliderTest(args) {
				try {
					console.log(args);
				} catch(err) {
				}
			}
			
			function slideComplete(args) {
			
				$('.next, .prev').removeClass('unselectable');
				
			    if(args.currentSlideNumber == 1) {
			
			        $('.prev').addClass('unselectable');
			
			    } else if(args.currentSliderOffset == args.data.sliderMax) {
			
			        $('.next').addClass('unselectable');
			
			    }
			
			}
				$('.iosSlider').iosSlider({
					snapToChildren: true,
					desktopClickDrag: true,
					keyboardControls: true,
					//onSliderLoaded: sliderTest,
					//onSlideStart: sliderTest,
					onSlideComplete: slideComplete,
					navNextSelector: $('.next'),
				    navPrevSelector: $('.prev'),
				});
				
	$('.iosSlider .slider .item').hover(
		function(){$(this).addClass('hover')},
		function(){$('.iosSlider .slider .item').removeClass('hover')}
	)			
	$( ".iosSlider .slider .item:nth-child(4)" ).addClass('fourth');			
	//$( ".iosSlider .slider .item:visible" ).addClass('visible');		
		
});
