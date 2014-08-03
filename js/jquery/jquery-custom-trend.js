jQuery.noConflict();
jQuery(document).ready(function($){

	
	/*trend pic move*/
	/*moveleft offset*/
	$('.trend1 > img').css('margin-left',-100);
	//$('.trend2 .grid6 .grid6 > img').css('margin-left',-100);
	$('.trend3  img.move').css('margin-left',-100);
	$('.trend5  img.move').css('margin-left',-100);
	
	$(window).scroll(function() {
		var diff = $(window).scrollTop(),
		trend1_elementOffset = $('.trend1').offset().top,
		trend2_elementOffset = $('.trend2').offset().top,
		trend3_elementOffset = $('.trend3').offset().top,
		trend4_elementOffset = $('.trend4').offset().top,
		trend5_elementOffset = $('.trend5').offset().top,
		trend7_elementOffset = $('.trend3:eq(1)').offset().top,
		trend1_distance = 0.1*(trend1_elementOffset - diff),
		trend2_distance = 0.05*(trend2_elementOffset - diff),
		trend3_distance = 0.05*(trend3_elementOffset - diff),
		trend4_distance = 0.05*(trend4_elementOffset - diff),
		trend5_distance = 0.05*(trend5_elementOffset - diff);
		trend7_distance = 0.05*(trend7_elementOffset - diff);
			
		/*move left*/
		if(trend1_distance < 0 && trend1_distance > -100){
			//console.log(trend1_distance);
			$('.trend1 > img').css('margin-left',-100 - trend1_distance );
		}
			/*move right*/
		if(trend2_distance < 50 && trend2_distance > -100){
			//console.log(trend1_distance);
			$('.trend2   img.move').css('margin-left',-50+ trend2_distance );
		}
		
			/*move left*/
		if(trend3_distance < 50 && trend3_distance > -100){
			//console.log(trend1_distance);
			$('.trend3  img.move').css('margin-left', 50 -100 - trend3_distance );
		}
		
			/*move left*/
		if(trend7_distance < 50 && trend7_distance > -100){
			//console.log(trend1_distance);
			$('.trend3:eq(1)  img.move').css('margin-left', 50 -100 - trend7_distance );
		}
		
			/*move right*/
		if(trend4_distance < 50 && trend4_distance > -100){
			//console.log(trend1_distance);
			$('.trend4  img.move').css('margin-left',-50+ trend4_distance );
		}
			/*move left*/
		if(trend5_distance < 50 && trend5_distance > -100){
			//console.log(trend1_distance);
			$('.trend5  img.move').css('margin-left', 50 -100 - trend5_distance );
		}
	});	
	/*trend pic move end*/
	
});