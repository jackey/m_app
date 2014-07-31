jQuery.noConflict();
jQuery(document).ready(function($){
	$('.level1').click(function(){
		$(this).toggleClass('active');
		$(this).next('ul').slideToggle();
	})
});