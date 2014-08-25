jQuery.noConflict();
jQuery(document).ready(function($){

	
$("input").blur();

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
				var title = $('.slider .item:eq(' + current_slider_num + ')').find('img').attr('alt');
				var setShare = "setShare(" + "'" + title +"', "  + "'" + c_url + "', "+ "'" + pic_url + "', "+"'刚刚在Lily商务时装官网分享了一张图片，一起来看看吧')";
				// var setShare = "setShare('形象大片', "  + "'" + c_url + "', "+ "'" + c_url+ pic_url + "', " + "'" + pic_url + "', "+"'刚刚在Lily商务时装官网分享了一张图片，一起来看看吧')";
				
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
					onSlideChange: slideChange,
					infiniteSlider:true
				});
				}

	$('body').addClass('fixed');		

	$('.copy').hover(function(){
		$(this).addClass('active');
	},function(){
		$(this).removeClass('active');
	})
	

	
	
	
	var didScroll = false,
	brandbox= $('#brand'),
	visionMR= $('#vision .move-right-img img'),
	newML= $('#new .move-left-img img'),
	clubZoom = $('#club .zoom img');
	
	/*pic move*/
		$(window).scroll(function() {
			didScroll = true;
		});
		setInterval(function() {
			if ( didScroll ) {
				didScroll = false;
				var diff = $(window).scrollTop(),
				brand_elementOffset = $('#brand').offset().top,
				vision_elementOffset = $('#vision').offset().top,
				new_elementOffset = $('#new').offset().top,
				club_elementOffset = $('#club').offset().top,
				brand_distance = 0.05*(brand_elementOffset - diff),
				vision_distance = 0.15*(vision_elementOffset - diff),
				new_distance = 0.05*(new_elementOffset - diff),
				club_distance = 0.05*(club_elementOffset - diff);
		if(diff >= 592){
			$('body').removeClass('fixed');
		}else{
			$('body').addClass('fixed');
		}
		//**#brand	
			if(brand_distance < 100 && brand_distance > 0){
				//$('#movetxt').css('margin-left','120' - brand_distance );
			brandbox.css('background-position', 0+brand_distance + '%');
			}
			if(vision_distance < 0 && vision_distance > -100){
				visionMR.css('margin-left',vision_distance );
			}
		//**#new
			if(new_distance < 100 && new_distance > -100){
				newML.css('margin-left',-100 + new_distance );
				//$('#new .move-right-img img').css('right',200 + new_distance );
			}
		//**#club
			if(club_distance < 100 && club_distance > -100){
				clubZoom.css('width',1130 + vision_distance*0.8 );
			}
		               }
          }, 50);
		/*pic move end*/
});