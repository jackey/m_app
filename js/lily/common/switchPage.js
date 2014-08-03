define([
    // libs
    'jQuery',
    'Handlebars',
    'History',
    // apps
    'common/loading',
    'common/helper',
    'common/select',
    'lib/text!templates/links.html',
    'lib/text!templates/weixin.html'
], function($, Handlebars, History , loading, helper, select, linksTpl, weixinTpl) {
    var sPath = location.pathname,
        sCur = sPath.replace(sPath.match(/^.*\php\//), ''),
        isNext,
        isAnimate = false,
        dBody = $('body'),
        dWrap = $('#wrap'),
        dLoading = $('.loading'),
        dTape = $('.showy'),
        sLinks = Handlebars.compile(linksTpl)(),
        sWeixin = Handlebars.compile(weixinTpl)();


    // loading animation start
    var updateBodyClass = function () {
        var str = sCur;
        var pos_cn = str.indexOf('_cn');
        if(pos_cn > 0) {
            str = str.substring(0, pos_cn);
        }

        var pos_param = str.indexOf('?');
        if(pos_param > 0) {
            str = str.substring(0, pos_param);
        }

        // update page class
        if (!helper.isPC()) {
            str = str + ' ' + 'mobile';

            if (dBody.hasClass('open')) {
                str = str + ' ' + 'open';
            }
        }

        dBody.attr('class', str);
    }

    var updateBgColor = function () {
        var bgColor;
        switch(sCur) {
            case 'index':
                bgColor = '255, 241, 244';
                break;
            case 'news':
                bgColor = '221, 235, 243';
                break;
            case 'campaign':
                bgColor = '241, 255, 252';
                break;
            case 'lookbook':
                bgColor = '250, 250, 236';
                break;
            case 'streetshot':
                bgColor = '250, 250, 236';
                break;
            case 'starshop':
                bgColor = '221, 235, 243';
                break;
            case 'storelocator':
                bgColor = '221, 235, 243';
                break;
            case 'job':
                bgColor = '222, 213, 202';
                break;
            case 'contact':
                bgColor = '221, 235, 243';
                break;
            case 'privacy':
                bgColor = '243, 222, 221';
                break;
            default:
                bgColor = '255, 241, 244';
        }
        dBody.attr('data-0', 'background-color:rgb(255,255,255);');
        dBody.attr('data-500', 'background-color:rgb('+bgColor+');');
    }


    var getQueryString = function(name) {
        var reg = new RegExp("(.*?)" + name + "=([^&]*)(&|$)", "i");
        var r = window.location.href.match(reg);
        if (r != null) return unescape(r[2]); return null;
    }


    var localHash = function () {
        var hashtag = getQueryString('hash');
        if($('#'+hashtag).length > 0) {
            var top = $('#'+hashtag).offset().top - 100;
            setTimeout(function(){
                $('html,body').animate({scrollTop:top});
            },10);
        }
    }
    var pageSwitchAnimate = function ( html ) {

        // if using mobile or ugly ie, stop the animation
        if (!helper.canAnimate()) {
            setContent( html );
            return updateBodyClass();
        }

        var nTime = 600,
            nWidth  = $(window).width(),
            nHeight  = $(window).height(),
            compelete = function () {
                isAnimate = false;

                // reset wrap style
                dWrap.removeAttr('style');

                // update page class
                updateBodyClass();

                // update background animation
                //updateBgColor();

                // update html
                setContent( html );

                // local hash
                   setTimeout(function(){
                       localHash();
                   },2000);

                //select.init();

            };

        // prevent duplicate animate
        isAnimate = true;

        // remove style for next loading animate
        dLoading.fadeOut().removeAttr('style').fadeIn();
        // hide tapes
        dTape.fadeOut();

        // for page switch animation need
        dWrap.css('position', 'relative')

        if (isNext) {
            dWrap.animate({
                'left' : '-' + nWidth / 2 + 'px',
                'opacity' : 0
            }, nTime, function () {
                compelete()
            })
        } else {
            dWrap.animate({
                'left' : nWidth / 2 + 'px',
                'opacity' : 0
            }, nTime, function () {
                compelete()
            })
        }
    }

    // for whole page hash link click catch
    var linkCatch = function () {
        var dLinks = dWrap.find('a[href^="#"]'),
            dWeixin = dWrap.find('.ft_share3');

        dLinks.bind('click', function () {
            var dTarget = $(this);

            sCur = dTarget.attr('href').replace('#', '');

            // as its hard to judge next or pre, default it would be next
            isNext = true;

            // page update
            updatePage()
        })

        dWeixin.bind('click', function () {
            helper.overlay(sWeixin, function () {
                setTimeout(function() {
                    var dOverlay = $('.fancybox-mobile').length ?  $('.fancybox-mobile') : $.fancybox.wrap.parent();

                    // for custom style
                    dOverlay.attr('id', 'weixin');
                }, 0)
            });
        })
    }

    // when animate end html the wrap
    var setContent = function (str) {
        var timer,
            setHtml = function () {
                dWrap.html(str);
                // update catch targets
                linkCatch()
                // loading
                loading.start();
            };

        if (isAnimate) {
            timer = setInterval(function () {
                if (!isAnimate) {
                    // update content
                    setHtml();
                    // clear interval
                    clearInterval(timer);
                }
            }, 300)
        } else {
            setHtml();
        }
    }

    var updatePage = function () {
        $.ajax({
            url : sCur ,
            method:'get',
            beforeSend: function () {
            },
            success: function(str) {
                var dHtml = $('<div>' + str + '</div>');
                var activedNav = dHtml.find('#nav li.item').index(dHtml.find('#nav li.active'));
                $('#nav li.item').removeClass('active').eq(activedNav).addClass('active');
                // update title
                document.title = dHtml.find('title').html();
                // setContent(dHtml.find('#wrap').html());
                pageSwitchAnimate( dHtml.find('#wrap').html() );
            }
        })
    }

    // for enable links catch and modal etc.
    var init = function () {
        var inAnimate = false,
            dMenu = $('.header .menu'),
            dNav = $('#nav'),
            dMbmenu = $('.mbmenu'),
            autoClick = false,
            showLinksModal = function () {
                var href = location.href;
                var match = href.match( /\/(\w+)(\?[.*])?$/ );
                var page = match ? match[1] : 'index';
                

                setTimeout(function(){
                   localHash();
                },1000);
                // Bind to StateChange Event
                if( !$.browser.msie || $.browser.version >= 9 ){
                    History.replaceState( { url: page } , document.title , href  );
                    History.Adapter.bind(window,'statechange',function(){ // Note: We are using statechange instead of popstate

                        console.log( location.href );

                        var State = History.getState(); // Note: We are using History.getState() instead of event.state

                        if( autoClick ){
                            isNext = State.data.isNext;
                        } else {
                            isNext = !State.data.isNext;
                            sCur = State.data.url;
                        }

                        autoClick = false;
                        // remove
                        $.fancybox.close(true);
                        // page update
                        updatePage();
                    });
                }

                $('.footer').delegate('a:not([href^=http]):not([href^=javascript])', 'click', function(e) {
                    var $a = $('#nav').find('.item a[href="' + $(this).attr('href') + '"]').trigger('click');
                    if( $a.length )
                        return false;
                });
                // for special method
                $('#nav').delegate('.item a', 'click', function(e) {
                    var dTarget = $(this),
                        dCur = dMbmenu.find('a.on'),
                        sTitle = dTarget.attr('title'),
                        nCur = dCur.attr('index') || 0,
                        nTarget = dTarget.attr('index') || 0;


                    // click self
//                    if (dTarget.hasClass('on')) {
//                        return e.preventDefault();
//                    }

                    // click others clickable link
                    if (sTitle) {
                        dCur.removeClass('on');
                        dTarget.addClass('on');
                        sCur = dTarget.attr('href').replace(/[./]+(\w+)/g, '$1');
                        // update animation judge params
                        isNext = nTarget > nCur;
                        autoClick = true;
                        if( $.browser.msie && $.browser.version < 9 ){
                            return true;
                        } else {
                            History.pushState({
                                isNext: isNext,
                                url: sCur
                            },  document.title , "./" + sCur);
                            return false;
                        }
                    }
                })

                // active the related link
                dMbmenu.find('a[href$="' + sCur + '"]').addClass('on');
            },
            sliderMenu = function () {
                var dCurList = dMbmenu.find('.item ol.active'),
                    dCurLink = dMbmenu.find('a[href$="' + sCur + '"]');

                // shrik or expand the list
                dMbmenu.delegate('.item', 'click', function () {
                    var dList = $(this).find('ol');

                    if (inAnimate) return;

                    inAnimate = true;

                    if (dCurList.length) {
                        dCurList.animate({
                            'height': '0px'
                        }, 600, function () {
                            dCurList.removeClass('active');
                            dList.removeAttr('style');
                        })
                    }

                    dList.animate({
                        'height': dList.children().length * 30 + 'px'
                    }, 600, function () {
                        dList.addClass('active');
                        dList.removeAttr('style');

                        inAnimate = false;
                    })

                    dCurList = dList;
                });

                // links
                dMbmenu.delegate('.item a', 'click', function () {
                    var dLink = $(this),
                        sHref = dLink.attr('href');

                    // if click self
                    if (dLink.hasClass('active')) {
                        return;
                    }

                    if (dCurLink.length) {
                        dCurLink.removeClass('active');
                    }

                    dLink.addClass('active');

                    dCurLink = dLink;

                    // update judge info
                    if (sHref.indexOf('#') >= 0) {
                        sCur = dLink.attr('href').replace('#', '');

                        // page update
                        updatePage()
                    }
                })

                dBody.toggleClass('open');
                dMbmenu.toggleClass('open');
            };

        // mobile menu need
        if (!helper.isPC()) {
            setTimeout(function(){
                localHash();
            },1000);
            dBody.addClass('mobile');
        }
        else
        {
            showLinksModal();
        }



        // show
        dMenu.bind('click', function () {
            if (helper.isPC()) {
                // pc
                //showLinksModal();
            } else {
                // mobile
                sliderMenu();
            }
        })

        dNav.find('.item').hover(function(){
            $(this).find('ol').stop(true,true).delay(200).fadeIn();
        }, function(){
            $(this).find('ol').stop(true,true).fadeOut();
        });


        // update catch targets
        linkCatch();

    }

    return {
        init : init
    }
})
