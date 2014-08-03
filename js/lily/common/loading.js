define([
    // libs
    'jQuery',
    'skrollr',
    'imagesLoaded',
    'Handlebars',
    'scrollpanel',
    'lib/jquery/jquery.mousewheel',
    // apps
    'common/api',
    'common/helper',
    'common/map',
    'common/select',
    'lib/text!templates/news.html'
], function($, skrollr, imagesLoaded, Handlebars, panel , mousewheel , api, helper, map, select, newsTpl) {
    var oRoll,
        oSkrollr = null,
        dWrap = $('#wrap'),
        dLeft = $('.loading.left'),
        dBottom = $('.loading.bottom'),
        dRight = $('.loading.right'),
        dTop = $('.loading.top'),
        dTape = $('.showy');

    

    // new overlay init
    var newsInit = function () {
        var dEvent = $('.event_list');

        if (dEvent.length) {
            dEvent.delegate('a.event_open', 'click', function() {
                var news_id = $(this).parents('.event_item').data('nid');
                api.getNews({
                    data : { news_id : news_id },
                    success : function (oData) {
                        var sNews = Handlebars.compile(newsTpl)(oData);

                        helper.overlay(sNews, function(){

                        } , function(){
                            var dOverlay = $.fancybox.wrap.parent();
                            // for custom style
                            dOverlay.attr('id', 'news');
                            dOverlay.find('.newswrap-inner')
                                .height( $(window).height() * 0.9 * 0.8)
                                .css('overflow' , 'visible');
                                //.jScrollPane({autoReinitialise:true})
                                
                                // .find('img')
                                // .css('opacity' , 0);

                            setTimeout(function(){
                                dOverlay.find('.newswrap-inner')
                                    .jScrollPane({autoReinitialise:true})
                                    .css('overflow' , 'visible');
                            } , 100);

                            // setTimeout(function(){
                            //     dOverlay.find('.newswrap-inner img')
                            //         .animate({
                            //             opacity: 1
                            //         } , 200);
                            // } , 200);

                            dOverlay.find('.fancybox-skin').click(function( e ){
                                    var tar = e.target || e.srcElement;
                                    if( $(tar).hasClass('fancybox-skin') )
                                        $.fancybox.close();
                                });
                        } )
                    }
                })
            })
        }
    }

    // the loading animation start
    var start = function() {
        // enable selects
        select.init();

        // enable news page overlay
        newsInit();


        // if using mobile or ugly ie, stop the animation
        if (!helper.canAnimate()) {
            // show the page content
            dWrap.fadeIn();

            return;
        }

        var nTime = 300,
            nLoad = 0,
            nTotal = dWrap.find('img').length,
            imgLoad = imagesLoaded(dWrap);

        var getQueryString = function(name) {
            var reg = new RegExp("(.*?)" + name + "=([^&]*)(&|$)", "i");
            var r = window.location.hash.match(reg);
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

        var _finisherTimer = setInterval(function(){
            if( !_finished['top'] ) return;

            clearInterval( _finisherTimer );
            // dTop.dequeue();


            // try to init the map
            map.init();

            dWrap.fadeIn();

            // show tapes
            dTape.fadeIn();

//                    setTimeout(function(){
//                        localHash();
//                    },1000);

            if (oSkrollr) {
                oSkrollr.refresh();
            } else {
                // let elements skroll
                oSkrollr = skrollr.init({
                    edgeStrategy: 'set',
                    forceHeight: false
                })
            }
        } , 1000 / 20 );

//         imgLoad.on('always', function(instance) {
//             dTop.queue(function() {
//                 var _timer = setInterval(function(){
//                     if( !_finished['top'] ) return

//                     clearInterval( _timer );
//                     dTop.dequeue();


//                     // try to init the map
//                     map.init();

//                     dWrap.fadeIn();

//                     // show tapes
//                     dTape.fadeIn();

//     //                    setTimeout(function(){
//     //                        localHash();
//     //                    },1000);

//                     if (oSkrollr) {
//                         oSkrollr.refresh();
//                     } else {
//                         // let elements skroll
//                         oSkrollr = skrollr.init({
//                             edgeStrategy: 'set',
//                             forceHeight: false
//                         })
//                     }
//                 } , 1000 / 20 );
//     //             setTimeout(function(){
//     //                 dTop.dequeue();


//     //                 // try to init the map
//     //                 map.init();

//     //                 dWrap.fadeIn();

//     //                 // show tapes
//     //                 dTape.fadeIn();

//     // //                    setTimeout(function(){
//     // //                        localHash();
//     // //                    },1000);

//     //                 if (oSkrollr) {
//     //                     oSkrollr.refresh();
//     //                 } else {
//     //                     // let elements skroll
//     //                     oSkrollr = skrollr.init({
//     //                         edgeStrategy: 'set',
//     //                         forceHeight: false
//     //                     })
//     //                 }
//     //             } , 1.5 * nTime);
                

// //                 var dHeight = $(window).height() - 90*2;
// //                 dRight.animate({
// //                     height: dHeight
// //                 } , 200);
// //                 dBottom
// //                     .delay(200)
// //                     .animate({
// //                         'width':'90%'
// //                     } , 200);

// //                 dLeft.delay(400)
// //                     .animate({
// //                         'height':dHeight
// //                     } , 200);

// //                 dTop.delay(600).animate({
// //                     'width': '90%'
// //                 }, nTime, function() {
// //                     // show the page content
// //                     dWrap.fadeIn();

// //                     // show tapes
// //                     dTape.fadeIn();

// // //                    setTimeout(function(){
// // //                        localHash();
// // //                    },1000);

// //                     if (oSkrollr) {
// //                         oSkrollr.refresh();
// //                     } else {
// //                         // let elements skroll
// //                         oSkrollr = skrollr.init({
// //                             edgeStrategy: 'set',
// //                             forceHeight: false
// //                         })
// //                     }
// //                 })
//             })
//         })

        $(window).resize(function(){
            var dHeight = $(window).height() - 90*2;
            dRight.css('height', dHeight);
            dLeft.css('height', dHeight);
        });
        var _animate = {};
        var _finished = {};
        var _dHeight = $(window).height() - 90*2;
        var _dWidth = $(window).width();
        var _timer = setInterval(function(){

            if( !nTotal ){
                clearInterval( _timer );
                dRight.animate({
                    height: _dHeight
                } , 200);
                dBottom
                    .delay(200)
                    .animate({
                        'width':'90%'
                    } , 200);

                dLeft.delay(400)
                    .animate({
                        'height':_dHeight
                    } , 200);


                dTop.delay(600)
                    .animate({
                        'width': '90%'
                    }, nTime , '' , function() {
                        _finished['top'] = 1;
                    });

                return;
            }


            var nVal = parseInt((nLoad / nTotal) * 100);

            if (0 <= nVal && nVal <= 25) {
                dRight.show().stop( true , true )
                    .animate({
                        'height': _dHeight * nVal / 25
                    } , nTime );
                return;
            }
            if( _finished['right'] === undefined  ){
                _finished['right'] = 0;
                dRight.show().animate({
                    height: _dHeight
                } , nTime / 2 , '' , function(){
                    _finished['right'] = 1;
                });
                return;
            }

            if (25 < nVal && nVal <= 50 ) {
                dBottom.show().stop( true , true )
                    .animate({
                        'width': (nVal - 25) / 25 * _dWidth
                    } , nTime ); 
                return;
            }
            if( _finished['bottom'] === undefined  ){
                _finished['bottom'] = 0;
                dBottom.show().animate({
                    width: '90%'
                } , nTime / 2 , '' , function(){
                    _finished['bottom'] = 1;
                });
                return;
            }

            if (50 < nVal && nVal <= 75) {

                dLeft.show().stop(true , true)
                    .animate({
                        'height': (nVal - 50)/ 25 * _dHeight
                    } , nTime);

                return;
            }
            if( _finished['left'] === undefined  ){
                _finished['left'] = 0;
                dLeft.show().animate({
                    height: _dHeight
                } , nTime / 2 , '' , function(){
                    _finished['left'] = 1;
                });
                return;
            }

            if ( 75 < nVal && nVal < 100 ) {
                dTop.show().stop(true , true)
                    .animate({
                        'width': (nVal - 75) / 25 * _dWidth
                    } , nTime  );
                return;
            }
            if( _finished['top'] === undefined  ){
                _finished['top'] = 0;
                dTop.show().animate({
                    width: '90%'
                } , nTime / 2 , '' , function(){
                    _finished['top'] = 1;
                });
                clearInterval( _timer );
                return;
            }
        } , nTime / 2  );
    
        imgLoad.on('progress', function(instance, image) {
            nLoad += 1;
            
            // var dHeight = $(window).height() - 90*2;
            // var nVal = parseInt((nLoad / nTotal) * 100);

            // if( nVal > 25 && !_animate['top'] ){
            //     _animate['top'] = 1;
            //     dTop.animate({
            //         'width': '90%'
            //     } , nTime);
            // }
            // if( nVal > 50 && !_animate['right'] ){
            //     _animate['right'] = 1;
            //     dRight.animate({
            //         'height': dHeight
            //     } , nTime );
            // }
            // if( nVal > 75 && !_animate['bottom'] ){
            //     _animate['bottom'] = 1;
            //     dBottom.animate({
            //         'width': '90%'
            //     } , nTime );
            // }
            // if( nVal > 99 && !_animate['left'] ){
            //     _animate['left'] = 1;
            //     dLeft.animate({
            //         'height': dHeight
            //     } , nTime );
            // }
            // if (0 < nVal && nVal < 25) {
            //     dRight.stop( true , true )
            //         .animate({
            //             'height': dHeight * nVal / 25
            //         } , nTime );
            //     return;
            // }
            // if (25 < nVal && nVal < 50 ) {
            //     dRight.css({
            //         'height': dHeight
            //     });

            //     dBottom.css({
            //         'width': parseInt((nVal / 50) * 100) + '%'
            //     }); 

            //     return;
            // }

            // if (50 < nVal && nVal < 75) {
            //     dBottom.css({
            //         'width': '90%'
            //     })

            //     dLeft.css({
            //         'height': parseInt((nVal / 75) * 100) + '%'
            //     })

            //     return;
            // }

            // if (75 < nVal && nVal < 100) {
            //     dLeft.css({
            //         'height': '75%'
            //     })

            //     dTop.animate({
            //         'width': parseInt((nVal / 100) * 100) + '%'
            //     }, nTime)

            //     return;
            // }
        });
    }

    return {
        start: start
    }
})
