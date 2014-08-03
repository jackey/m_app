jQuery.noConflict();
jQuery(document).ready(function($){

$('.open-share').click(function(){
	$(this).parent().toggleClass('active')
});

var nice = $("html").niceScroll(
{
//mousescrollstep:150,scrollspeed:150

}
);
var nice3 = $("#search-city-result").niceScroll(
{mousescrollstep:50,touchbehavior:true,scrollspeed:150}
);
var nice4 = $(".media-center .col-main").niceScroll(
{mousescrollstep:50,touchbehavior:true,scrollspeed:150}
);
var nice5 = $(".club-rule-wrapper").niceScroll(
{mousescrollstep:50,touchbehavior:true,scrollspeed:150}
);
var nice2 = $(".cms-page-view .col1-layout .col-main").niceScroll(
{mousescrollstep:150,touchbehavior:true,scrollspeed:150}


);

	
	function hideMenu(){
		$('.quick-access').hide();
	}
	function showMenu(){
		$('.quick-access').show();
	}
	$('.header-container').hover(function(){
		showMenu();
	},function(){
		hideMenu();
	})	

	$(window).scroll(function() {	
		var diff = $(window).scrollTop()
		
			if(diff >= 40){
		$('.quick-access').removeClass('show');
	}else{
		$('.quick-access').addClass('show');
	}
	});	
		$('#navi li a').click(function(){
			//console.log('click');
				var window_Width = $(window).width(); 
				if( window_Width <= 1279 )
					{
						$('#navi').slideUp();
					}
		});
	
	$(window).resize(function() {   
		var window_Width = $(window).width(); 
		if( window_Width > 1279 )
		{
		$('.media-center-nav').show();
		$('#navi').show();
		}else{
		

		$('#navi').hide();
		$('.media-center-nav').hide();
		}
	//update stuff 
	
	});


	
	$('#en-store').click(function(){
		//console.log('en store')
		$('#en-store-wrapper').show()
	})
	
	$('#meida-center-link').click(function(){
		//console.log('en store')
		$('#en-store-wrapper').show()
	})
	$('#en-store-wrapper').click(function(){
		//console.log('en store')
		$(this).hide()
	})
		
	$('#navi-switch').click(function(){
		$('#navi').slideToggle()
	
	})	
		
	$('#media-center-title').click(function(){
		$('.media-center-nav').slideToggle()
	
	})	
		
	
	
	// $('.main-container').click(function(){
	// var window_width = 	$(window).width();
	// if( window_width < 960)	{
		// $('#navi').slideUp()
	// }
	// })	
	$('#close-nav').click(function(){

		$('#navi').slideUp()

});
	
	
$('#weixin').click(function(){
$('#weixin-qr').show()
});
	
$('#weixin-qr').click(function(){
$(this).hide()
});
$('#forgot-password').click(function(){
	
	
	$('#slides-1').hide();
	$('#slides-2').show();
	return false;
	
});
$('#back-login').click(function(){
	
	
	$('#slides-2').hide();
	$('#slides-1').show();
	return false;
	
});

});