jQuery.noConflict();
jQuery(document).ready(function($){
	
	if (window.location.hash) {

		var fragment = window.location.hash;

		var sfragment = fragment.substr(6); 

	}
									
				$('.doubleSlider-1').iosSlider({
					scrollbar: true,
					snapToChildren: true,
					desktopClickDrag: true,
					
					startAtSlide:sfragment,
					
					infiniteSlider: true,
					navPrevSelector: $('.doubleSliderPrevButton'),
					navNextSelector: $('.doubleSliderNextButton'),
					scrollbarHeight: '2',
					scrollbarBorderRadius: '0',
					scrollbarOpacity: '0.5',
					onSliderLoaded: doubleSlider2Load,
					onSlideChange: doubleSlider2Load,
					//startAtSlide: 2,
					stageCSS: {
						overflow: 'visible'
					}
					
					
					
				});
				
				$('.doubleSlider-2 .button').each(function(i) {
				
					$(this).bind('click', function() {

						$('.doubleSlider-1').iosSlider('goToSlide', i+1);
						
					});
				
				});
				
				$('.doubleSlider-2').iosSlider({
					desktopClickDrag: true,
					snapToChildren: true,
					snapSlideCenter: true,
					infiniteSlider: true
				});
				
				function doubleSlider2Load(args) {
					
					//currentSlide = args.currentSlideNumber;
					$('.doubleSlider-2').iosSlider('goToSlide', args.currentSlideNumber);
					
					/* update indicator */
					$('.doubleSlider-2 .button').removeClass('selected');
					$('.doubleSlider-2 .button:eq(' + (args.currentSlideNumber-1) + ')').addClass('selected');
					
				}
				
				

				
});
