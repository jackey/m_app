jQuery.noConflict();
jQuery(document).ready(function($){

    if($(window).width() <= 640) {
        $('body').addClass('mobile');
    }
    $('.header .menu').click(function(){

        $('.mbmenu').toggleClass('open');
        $('body').toggleClass('open');
    });

    $('.mbmenu .item').click(function(){
        $('.mbmenu').find('ol').css({height:0});
        $(this).find('ol').css({height:'auto'});
    });

    $('.club_welcome_m').click(function(){
        $('.club_navlist_m').toggleClass('open');
    });

    //$(".express_addition select,.pager select").selectOrDie();

// $('.open-share').click(function(){
	// $(this).parent().toggleClass('active')
// });
$('.ckepop').hover(function(){
	$(this).addClass('active');
},
function(){
	$(this).removeClass('active');
}


);

//var nice = $("html").niceScroll(
//{
////mousescrollstep:150,scrollspeed:150
//
//}
//);
//var nice3 = $("#search-city-result").niceScroll(
//{mousescrollstep:50,touchbehavior:true,scrollspeed:150}
//);
// var nice4 = $(".media-center .col-main").niceScroll(
// {mousescrollstep:50,touchbehavior:true,scrollspeed:150}
// );
//var nice5 = $(".club-rule-wrapper").niceScroll(
//{mousescrollstep:50,touchbehavior:true,scrollspeed:150}
//);
// var nice2 = $(".cms-page-view .col1-layout .col-main").niceScroll(
// {mousescrollstep:150,touchbehavior:true,scrollspeed:150}


// );

	
	
		if($( "body" ).hasClass( "media-center" )){
			var colmain_height= $(".media-center .col-main").height();
			
			$('.media-center .col-left').css('height',colmain_height)
		
			}
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
$('#forgot-password-home').click(function(){


	$('#slides-1-home').hide();
	$('#slides-2-home').show();
	return false;
	
});
$('#back-login').click(function(){
	
	
	$('#slides-2').hide();
	$('#slides-1').show();

	return false;
	
});
$('#back-login-home').click(function(){
	
	

	$('#slides-2-home').hide();
	$('#slides-1-home').show();
	return false;
	
});
$('.child-nav li:last-child').addClass('last');
$('.nav-parent').live({
     mouseenter:function(){
     $(this).children('.child-nav').stop(true, true).show();
     } ,
     mouseleave:function(){
     $(this).children('.child-nav').stop(true, true).hide();
     }
});
$('.nav-parent').hover(
function(){

	$(this).children('.child-nav').stop(true, true).show();

	},
function(){
	$(this).children('.child-nav').stop(true, true).hide();
	}
)

$('#attribute_1 li').click(function(){
	$('#attribute_1 li').removeClass('active');
	$(this).addClass('active');
	//console.log('aihei')
})

$('#attribute_2 li').click(function(){
	$('#attribute_2 li').removeClass('active');
	$(this).addClass('active');
	//console.log('aihei')
})


});
