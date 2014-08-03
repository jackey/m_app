jQuery.noConflict();
jQuery(document).ready(function($){

// $(document).bind(
  // 'touchmove',
  // function(e) {
    // e.preventDefault();
  // }
// );
//$(document).bind("dragstart", function() { return false; });
	
	
$("input").blur();
//$('.cms-index-index #navi li:first').click();
/* click #link to scroll */
$('.cms-index-index #navi a').click(function(){
    $('html, body').animate({
        scrollTop: $( $.attr(this, 'href') ).offset().top
    }, 500);
    return false;
});


$('h1.logo a').click(function(){
    $('html, body').animate({
        scrollTop: $( $.attr(this, 'href') ).offset().top
    }, 500);
    return false;
});

if($('.iosSlider').length>0){

	function slideChange(args) {
				
				$('.selectors .item').removeClass('selected');
				$('.selectors .item:eq(' + (args.currentSlideNumber - 1) + ')').addClass('selected');
				
				var current_slider_num = args.currentSlideNumber - 1;
				var c_url = $('.slider .item:eq(' + current_slider_num + ')').attr('dataurl');
				var pic_url = $('.slider .item:eq(' + current_slider_num + ')').find('img').attr('src');
				
				var setShare = "setShare('形象大片', "  + "'" + c_url + "', "+ "'" + c_url+ pic_url + "', " + "'" + pic_url + "', "+"'刚刚在Lily商务时装官网分享了一张图片，一起来看看吧')";
				
				$('.share-slider').attr('onmouseover', setShare);
				
				
			}
	$('.iosSlider').iosSlider({
					snapToChildren: true,
					desktopClickDrag: false,
					keyboardControls: true,
					navNextSelector: $('.next'),
					navPrevSelector: $('.prev'),
					navSlideSelector: $('.selectors .item'),
					autoSlide: true,
					onSlideChange: slideChange
				});
				}

// $('#vision .link2').click(function(){
	// console.log('yes');
	// $('#loading').show();
	// setTimeout(function() {
      // $('body .wrapper').addClass('move-left');
// }, 10);
	
// });

	$('body').addClass('fixed');		

	$('.copy').hover(function(){
		$(this).addClass('active');
	},function(){
		$(this).removeClass('active');
	})
	

	
	
	
	

	
	
	$(window).scroll(function() {
	
	var diff = $(window).scrollTop(),
    brand_elementOffset = $('#brand').offset().top,
    vision_elementOffset = $('#vision').offset().top,
    new_elementOffset = $('#new').offset().top,
    club_elementOffset = $('#club').offset().top,
    brand_distance = 0.05*(brand_elementOffset - diff),
    vision_distance = 0.15*(vision_elementOffset - diff),
    new_distance = 0.05*(new_elementOffset - diff),
    club_distance = 0.05*(club_elementOffset - diff);
	//console.log(distance);
	//console.log(vision_distance);
		 
		if(diff >= 592){
			$('body').removeClass('fixed');
		}else{
			$('body').addClass('fixed');
		}



		//if($('#brand').hasClass('active')){
			//console.log('brand is active');
			//console.log(distance);
			//$('#movetxt').
			
		//**#brand	
			if(brand_distance < 100 && brand_distance > 0){
			
				//$('#movetxt').css('margin-left','120' - brand_distance );
			$('#brand').css('background-position', 0+brand_distance + '%');
			
			}
		//}
		
		//**#vision
			if(vision_distance <  100 && vision_distance > -100){
			
				$('#vision .zoom img').css('width',1060 + vision_distance );
				
				
			}
			if(vision_distance < 0 && vision_distance > -100){
			
				$('#vision .move-right-img img').css('margin-left',vision_distance );
				
			}
		
		//**#new
			if(new_distance < 100 && new_distance > -50){
			
				$('#new .move-left-img img').css('margin-left', vision_distance );
				$('#new .move-right-img img').css('right',200 + vision_distance );
				
			}
		//**#club
			if(club_distance < 100 && club_distance > -100){
			
				$('#club .zoom img').css('width',1330 + vision_distance );
				
				
			}
		
		
	});	
	

	$('.window').addClass('inactive');
//scroll down 	
	// $('#brand').waypoint(function(down) {
		
		// $(this).addClass('active');
		// $(this).removeClass('inactive');
	// });
//scroll up	
	// $('#brand').waypoint(function(down) {
	
		// $(this).addClass('inactive');
		// $(this).removeClass('active');
	// }, { offset: 'bottom-in-view' });

});