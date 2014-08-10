define([
    // libs
    'jQuery',
    'skrollr',
    // apps
    'common/switchPage',
    'common/loading',
    'common/helper',
    'common/map',
    'common/api'
], function($, skrollr, switchPage, loading, helper, map , api ) {

    var initialize = function() {
        // get current location
        //map.getPosition();

        // start loading
        loading.start();

        // if ie8
        if( $.browser.msie && $.browser.version <= 8 ){
            $('.loading.top').css({
                top: 70,
                width: '90%'
            });
            $('.loading.bottom').css({
                bottom: 70,
                width: '90%'
            });
            $('.loading.left,.loading.right')
                .css({
                    height: 'auto',
                    top: 70,
                    bottom: 70
                });
        }

		var sUserAgent = navigator.userAgent.toLowerCase();
		var bIsIpad = sUserAgent.match(/ipad/i) == "ipad";
		if(bIsIpad) {
			$('meta[name="viewport"]').attr('content','width=1024, minimum-scale=0.5, maximum-scale=1, target-densityDpi=290,user-scalable = no,minimal-ui');
			$('html').addClass('ipad');
		}

		map.init();

        // start catch album and videos , weibo events
        helper.init();

        // update the link to hash mode
        switchPage.init();
    };

    return initialize;
})
