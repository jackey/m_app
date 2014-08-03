jQuery.noConflict();
jQuery(document).ready(function($){
	//$('.wrapper').addClass('is-right');
	
	// setTimeout(function() {
      // $('.wrapper').addClass('set-center');
	// }, 500);
	// setTimeout(function() {
      // $('.wrapper').css('transform','none');
	// }, 1000);
	
	$('.street-style3 img.moves-left').css('margin-left',-100);
	$('.street-style4 img.moves-left').css('margin-left',-100);
	$('.street-style6 img.moves-left').css('margin-left',-100);
	
	$(window).scroll(function() {
		var diff = $(window).scrollTop(),
		street_style3_elementOffset_1 = $('.street-style3:eq(0)').offset().top,
			street_style3_distance_1 = 0.1*(street_style3_elementOffset_1 - diff),
		street_style3_elementOffset_2 = $('.street-style3:eq(1)').offset().top,
			street_style3_distance_2 = 0.05*(street_style3_elementOffset_2 - diff),
		street_style3_elementOffset_3 = $('.street-style3:eq(2)').offset().top,
			street_style3_distance_3 = 0.05*(street_style3_elementOffset_3 - diff),
		street_style4_elementOffset_1 = $('.street-style4:eq(0)').offset().top,
			street_style4_distance_1 = 0.05*(street_style4_elementOffset_1 - diff),
		street_style6_elementOffset_1 = $('.street-style6:eq(0)').offset().top,
			street_style6_distance_1 = 0.05*(street_style6_elementOffset_1 - diff),
		street_style5_elementOffset_1 = $('.street-style5:eq(0)').offset().top,
			street_style5_distance_1 = 0.1*(street_style5_elementOffset_1 - diff);
		/*move left*/
		if(street_style3_distance_1 < 0 && street_style3_distance_1 > -100){
			//console.log(trend1_distance);
			//console.log($('.street-style3 img.move'));
			//$('.street-style3 img.moves-left').css('margin-left',-100 - street_style3_distance_1);
			$('.street-style3:eq(0)').children('img.moves-left').css('margin-left',-100 - street_style3_distance_1);
			//$('.street-style3 img.moves-left').css('margin-left',-100 - street_style3_distance_1);
		}
		
		if(street_style3_distance_2 < 50 && street_style3_distance_2 > -100){
			//console.log(trend1_distance);
			//console.log($('.street-style3 img.move'));
			//$('.street-style3 img.moves-left').css('margin-left',-100 - street_style3_distance_2);
			$('.street-style3:eq(1)').children('img.moves-left').css('margin-left',-50 - street_style3_distance_2);
		}
		
		if(street_style3_distance_3 < 50 && street_style3_distance_3 > -100){
			//console.log(trend1_distance);
			//console.log($('.street-style3 img.move'));
			//$('.street-style3 img.moves-left').css('margin-left',-100 - street_style3_distance_3);
			$('.street-style3:eq(2)').children('img.moves-left').css('margin-left',-50 - street_style3_distance_3);
		}
		
		if(street_style5_distance_1 < 50 && street_style5_distance_1 > -100){
			//console.log(street_style5_distance_1);
			//console.log($('.street-style3 img.move'));
			//$('.street-style3 img.moves-left').css('margin-left',-100 - street_style5_distance_1);
			$('.street-style5 img.zoom').css('width',1060 + street_style5_distance_1);
		}
		
		/*move right*/
		
		if(street_style4_distance_1 < 50 && street_style4_distance_1 > -100){
			//console.log(trend1_distance);
			$('.street-style4 img.moves-right').css('margin-left',-50+ street_style4_distance_1 );
			$('.street-style4 img.moves-left').css('margin-left',-50 - 2*street_style4_distance_1);
		}
		if(street_style6_distance_1 < 50 && street_style6_distance_1 > -100){
			//console.log(trend1_distance);
			$('.street-style6 img.moves-right').css('margin-left',-50+ street_style6_distance_1 );
			$('.street-style6 img.moves-left').css('margin-left',-50 - street_style6_distance_1);
		}
	});	
	
});