require.config({
    paths: {
        // lib
        jQuery : 'lib/jquery/jquery',
        Handlebars : 'lib/handlebars/handlebars',
        imagesLoaded : 'lib/imagesLoaded/imagesLoaded.min',
        skrollr : 'lib/skrollr/skrollr.min',
        History: 'lib/history.require',
        templates:  '../js/templates',
        scrollpanel: 'lib/jquery/jquery.jscrollpane',
    },
    priority: [
        'jQuery',
        'Handlebars',
        'imagesLoaded',
        'skrollr'
    ]
})

require([
    'require',
    'jQuery',
    'Handlebars',
    'imagesLoaded',
    'skrollr',
    'common/api',
    'common/properties-' + ( window.lang || 'en' )
], function(require, $, Handlebars, imagesLoaded, skrollr , api, lang) {

    
    if( $.browser.mozilla ){
        $('html').addClass('firefox');
    }
    
    // fix lang
    window._e = function( text ){
        return lang[ text ] || text;
    };

    $('a[data-lang]').click(function(){
        api.setCookie('lang' , $(this).data('lang') , 60 * 60 * 24 * 30);
    });

    $('document').ready(function () {
        require(['common/app'], function(app) {
            app();
        });
    });

    var isIphone = navigator.userAgent.toLowerCase().indexOf('iphone') > 0,
        isIpad = navigator.userAgent.toLowerCase().indexOf('ipad') > 0,
        isAndroid = navigator.userAgent.indexOf('Android') >= 0,
        isMobile = isIphone || isIpad || isAndroid;
    if( isMobile ){
        $(window).load(function(){
            setTimeout(window.scrollTo(0,1) , 0);
        });
    }
    
    window.startTrack($);
});

// google map need those functions under global
var googleReady = function () {
    require(['common/map'], function(map) {
        //map.init();
    })
}

//var loadMapScript = function() {
//    var script = document.createElement('script');
//
//    script.type = 'text/javascript';
//
//    script.src = 'https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&language=zh-CN&callback=googleReady';
//
//    document.body.appendChild(script);
//}
//
//window.onload = loadMapScript;
