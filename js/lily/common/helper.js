define([
    // libs
    'jQuery',
    'Handlebars',
    'common/map',
    'common/api',
    'lib/video-js',
    'lib/text!templates/album.html',
    'lib/text!templates/video.html',
    'lib/text!templates/weibo.html'
], function($, Handlebars, map, api, videojs , albumTpl, videoTpl, weiboTpl) {
    var dBody = $('body'),
        isUglyIe = $.browser.msie && $.browser.version <= 8,
        isIphone = navigator.userAgent.toLowerCase().indexOf('iphone') > 0,
        isIpad = navigator.userAgent.toLowerCase().indexOf('ipad') > 0,
        isAndroid = navigator.userAgent.indexOf('Android') >= 0,
        isMobile = isIphone || isIpad || isAndroid,
        sWeibo = (function () {
            var str = '';

            api.getWeibo({
                success : function (oData) {
                    str = Handlebars.compile(weiboTpl)(oData);
                }
            })

            return str;
        })();


    var isPC = function () {
        if (isMobile) {
            return false;
        } else {
            return true;
        }
    }

    var canAnimate = function () {
        if (isUglyIe || isMobile) {
            return false;
        } else {
            return true;
        }
    }

    var loadedAllImages = function( $imgs , fn ){
        var index = 0;
        $imgs.each(function(){
            $('<img/>').load(function(){
                index ++;
                if( index == $imgs.length )
                    fn();
            })
            .attr( 'src' , this.getAttribute('src') );
        });
    }

    // window event for esc keydown
    $(window).keydown(function( ev ){
        switch( ev.which ){
            case 27:
                $.fancybox.close( true );
                break;
            case 39:
                $('.jcarousel-control-next').trigger('click');
                break;
            case 37:
                $('.jcarousel-control-prev').trigger('click');
                break;
        }
    });

    var overlay = function (sHthml, func , onBeforeShow) {
        //sHthml = '<div style="color:red;height:100px;width:100px;">asdasd</div>';
        if (canAnimate() ) {
            $.fancybox({
                openSpeed : 1000,
                closeSpeed : 1000,
                content: sHthml,
                closeClick: false,
                cyclic: true,
                helpers: {
                    overlay: {
                        width: '100%',
                        height: '100%',
                        closeClick: false
                    }
                },
                beforeShow: function () {
                    onBeforeShow && onBeforeShow();
                    // custom close function
                    dBody.delegate('.close', 'click', function() {
                        $.fancybox.close(true);
                    })
                    // after all image loaded, run the func
                    loadedAllImages( this.wrap.find('img') , func );
                }
            })
        } else {
            $.fancybox({
                openSpeed : 0,
                closeSpeed : 0,
                content: sHthml,
                cyclic: true,
                helpers: {
                    overlay: {
                        width: '100%',
                        height: '100%'
                    }
                },
                beforeShow: function () {
                    // custom close function
                    dBody.delegate('.close', 'click', function() {
                        $.fancybox.close(true);
                    })

                    func();
                }
            })
        }
    }

    var enableList = function() {
        var imageData = null;
        // album list
        dBody.delegate('.album', 'click', function() {
            var imgIndex =  $(this).data('index');//$('.album').index( this ); //$(this).attr('src').match(/(\d+)\.\w+$/)[1];
            //imgIndex = imgIndex - 1;
            var bFunc = function(aData) {
                    var jcarousel = $('.fancybox-inner .jcarousel'),
                        dPre = $('.jcarousel-control-prev'),
                        dNext = $('.jcarousel-control-next'),
                        dDesc = $('.actions .desc'),
                        nLength = $('.jcarousel .content li').length,
                        nIndex = 0,
                        updateDesc = function () {
                            if (nIndex <= 0 ) {
                                nIndex = nLength;
                            }

                            if (nIndex > nLength){
                                nIndex = 1;
                            }

                            dDesc.html(aData[nIndex - 1].title);
                        };
                       // ,
                       // updateSize = function () {
                       //     jcarousel.jcarousel('items').css('height', '100%');
                       // };

                    jcarousel.on('jcarousel:create', function() {
                        // but i don't know why
                        jcarousel.jcarousel('items')
                            .find('img')
                            .css('width', '0');
                        setTimeout(function(){
                            jcarousel.jcarousel('items')
                                .find('img')
                                .css('width', 'auto');
							tImgs = jcarousel.find('img');
							tImgs.each(function(index, obj){
								var width = $(obj).width();
								$(obj).parent().css('width',width);
							})
							jcarousel.jcarousel('scroll',  imgIndex , false );

                        } , 0 );
                    }).jcarousel({
                        wrap: 'circular',
                        center: true
                    });

                    dPre.jcarouselControl({
                        target: '-=1'
                    });

                    dNext.jcarouselControl({
                        target: '+=1'
                    });

                    // // for dynamic title
                    dPre.bind('click', function () {
                        nIndex -= 1;
                        updateDesc()
                    })

                    dNext.bind('click', function () {
                        nIndex += 1;
                        updateDesc()
                    })


                    // if is mobile , init hammer event
                    jcarousel.hammer()
                        .on("dragleft dragright swipeleft swiperight", function(ev) {
                            switch( ev.type ){
                                case 'dragleft':
                                case 'swipeleft':
                                    dNext.trigger('click');
                                    break;
                                case 'dragright':
                                case 'swiperight':
                                    dPre.trigger('click');
                                    break;
                            }
                        });

                };

            var albumId = $(this).data('album');
            
            if(albumId == undefined) albumId = 1;
            
            if( imageData && false) {
                var sAlbum = Handlebars.compile(albumTpl)({ data : imageData });

                overlay(sAlbum, function () {
                    bFunc(imageData);
                });
            } else {
                api.getAlbumList({
                    data : { id : albumId },
                    success : function (aData) {
                        imageData = aData;
                        var sAlbum = Handlebars.compile(albumTpl)({ data : aData });

                        overlay(sAlbum, function () {
                            bFunc(aData);
                        });
                    }
                });
            }
        });
    
    function renderVideo ( $wrap , movie , poster , config , cb ){
        var id = 'video-js-' + ( $.guid++ );
        $wrap.append( '<div class="video-wrap-inner" style="position:absolute;width:100%;height:100%;"><video id="' + id + '" style="width: 100%;height: 100%;" class="video-js vjs-default-skin"\
            preload="auto"\
              poster="' + poster + '">\
             <source src="' + movie + '.mp4" type="video/mp4" />\
             <source src="' + movie + '.webm" type="video/webm" />\
             <source src="' + movie + '.ogv" type="video/ogg" />\
        </video></div>');

        config = $.extend( { "controls": false, "autoplay": false, "preload": "auto","loop": true, "children": {"loadingSpinner": false} } , config || {} );
        videojs.options.flash.swf = "./js/lib/video-js/video-js.swf";
        var myVideo = videojs( id , config , function(){
            var v = this;
            $wrap.find('.video-wrap').fadeIn();
            $wrap.data('video-object' , v);
            cb && cb.call( v );
        } );
    }

        // video list
        dBody.delegate('.video', 'click', function() {
            var bFunc = function(aData) {
                var imgIndex = $('.video').index( this );
                var jcarousel = $('.fancybox-inner .jcarousel'),
                    dPre = $('.jcarousel-control-prev'),
                    dNext = $('.jcarousel-control-next'),
                    dDesc = $('.actions .desc'),
                    nLength = $('.jcarousel .content li').length,
                    nIndex = imgIndex,
                    updateDesc = function () {

                        if (nIndex <= 0 ) {
                            nIndex = nLength;
                        }

                        if (nIndex >= nLength){
                            nIndex = 1;
                        }

                        // dDesc.html(aData[nIndex - 1].title);
                    }
                    // ,
                    // updateSize = function () {
                    //     var dWidth = $(window).width();
                    //     jcarousel.jcarousel('items').css('width', dWidth + 'px');
                    // };

                // jcarousel init
                jcarousel.on('jcarousel:create', function() {
                    //updateSize();
                    // jcarousel.jcarousel('items')
                    //     .find('img')
                    //     .css('width', '0');
                    // setTimeout(function(){
                    //     jcarousel.jcarousel('items')
                    //         .find('img')
                    //         .css('width', 'auto');
                    jcarousel.jcarousel('items').css('width' , $(window).width());
                    jcarousel.jcarousel('scroll',  imgIndex , false );
                    $(window).resize(function(){
                        jcarousel.jcarousel('items').css('width' , $(window).width());
                    });
                    //} , 0 );
                })
                .on('jcarousel:animate', function(event, carousel) {
                    if( $.browser.msie && $.browser.version <= 8 ){
                        var $target = $(this).jcarousel('target');
                        var $wrap = $target.find('.video-wrap');
                        $wrap.data('video-object') && $wrap.data('video-object').dispose();
                        $wrap.find('.video-player-btn').remove();
                        // reinit
                        renderVideo( $wrap , $wrap.data('video').replace(/\.\w+$/ , '') , $wrap.data('poster') , {} , function(){
                            var videoObject = this;
                            $wrap.find('.vjs-poster').show();
                            var $btn = $('<div class="video-player-btn"></div>').appendTo( $wrap )
                                .click(function(){
                                    videoObject.play();
                                });
                            videos.push( videoObject );
                            videoObject.on('play' , function(){
                                $btn.hide();
                            });

                            videoObject.on('pause' , function(){
                                $btn.show();
                            });
                        } );
                    }
                }).jcarousel({
                    wrap: 'circular',
                    center: true
                });

                dPre.jcarouselControl({
                    target: '-=1'
                });

                dNext.jcarouselControl({
                    target: '+=1'
                });

                // video stuff
                var dVideo = jcarousel.find('video'),
                    stopPlay = function() {
                        $.each( videos , function( i , v ){
                            v.pause();
                        });
                    }


                var videos = [];
                window.videos = videos;
                jcarousel.find('li').each(function(){
                    var $wrap = $(this).find('.video-wrap');
                    renderVideo( $wrap , $wrap.data('video').replace(/\.\w+$/ , '') , $wrap.data('poster') , {} , function(){
                        var videoObject = this;
                        videos.push( videoObject );
                        $wrap.find('.vjs-poster').show();
                        var $btn = $('<div class="video-player-btn"></div>').appendTo( $wrap )
                            .click(function(){
                                videoObject.play();
                            });
                        videoObject.on('play' , function(){
                            $btn.hide();
                        });

                        videoObject.on('pause' , function(){
                            $btn.show();
                        });
                    } );
                });
                // var myVideo = videojs( dVideo , config , function(){
                //     // setTimeout(function(){
                //     //     $wrap.find('.video-wrap').fadeIn();
                //     //     if( config.autoplay )
                //     //         myVideo.play();
                //     // } , 20);
                //     //$wrap.data('video-object' , v);
                // } );
                // dVideo.mediaelementplayer()

                // for dynamic title
                dPre.bind('click', function () {
                    nIndex -= 1;
                    stopPlay();
                    updateDesc();
                })

                dNext.bind('click', function () {
                    nIndex += 1;
                    stopPlay();
                    updateDesc();
                })

                // auto resize
                // $('window').on('resize', function () {
                //     updateSize();
                // })
            };

            api.getVideoList({
                data : { id : '1231313' },
                success : function (aData) {
                    var sVideo = Handlebars.compile(videoTpl)({ data : aData });

                    overlay(sVideo, function () {
                        bFunc(aData);
                    });
                }
            })
        })
    }

    var weibo = function() {
        var dShowyitem = $('.showy .showyitem');

        dBody.delegate('.showy .showyitem', 'mouseenter', function() {
            var dWeibo,
                dTarget = $(this),
                nTop = parseInt(this.style.bottom);

            // clear old weibos
            dShowyitem.html();

            // set weibo content
            dTarget.html(sWeibo);

            dWeibo = dTarget.find('.weibo');

            // fadein
            dWeibo.fadeIn();

            // show weibo on bottom
            if (nTop > 50) {
                dWeibo.addClass('weibo_bottom');
            } else {
                // on top
                dWeibo.addClass('weibo_top');
            }

            // empty weibo content
            $(window).on('load resize scroll', function() {
                dWeibo.fadeOut();
            })

            dTarget.bind('mouseleave', function() {
                dWeibo.fadeOut();
            })
        })
    }

    var campaignEvent = function() {
        dBody.delegate('.go_play_ground', 'click', function() {
            var top = $('#play_ground').position().top - 140;
            $('html, body').animate({scrollTop : top});
        });
    };

    var mapEvent = function() {
        dBody.delegate('.starshop .store_view', 'click', function() {
            $(this).parent().next().html('<a href="http://api.map.baidu.com/geocoder?address=上海虹桥机场&output=html" target="_blank"> <img src="http://api.map.baidu.com/staticimage? width=400&height=300&zoom=11¢er=上海虹桥机场" />');
        });
    }

    var globalEvent = function(){
        dBody.delegate('.gohash', 'click', function() {
            var hash = $(this).data('hash');
            var top = $('#'+hash).position().top - 140;
            $('html, body').animate({scrollTop : top});
        });

        dBody.delegate('.header .search', 'click', function() {
            $('.popup_overlay').fadeIn();
            $('.search_popup').fadeIn();
        });

        dBody.delegate('.popup_close,.popup_overlay', 'click', function() {
            $('.popup_overlay').fadeOut();
            $('.popup').fadeOut();
        });

        dBody.delegate('.search_popup .search_input', 'keyup', function(e) {
            if(e.keyCode == 13) {
                $('.search_popup .search_btn').trigger('click');
            }
        });

        dBody.delegate('.search_popup .search_btn', 'click', function() {
            var keyword = $('.search_popup .search_input').val();
            window.location.href = "search.php?keyword=" + keyword;
        });
    }

    var init = function() {
        // for album/photo list modals
        enableList();
        // for weibo mouse event
        //weibo();
        // campaign event
        campaignEvent();

        // for map
        mapEvent();

        globalEvent();
    }

    return {
        init: init,
        overlay : overlay,
        canAnimate : canAnimate,
        isPC : isPC
    }
})
