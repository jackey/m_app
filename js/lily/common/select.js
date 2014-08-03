define([
    // libs
    'jQuery',
    'Handlebars',
    'common/map',
    'common/api',
    'lib/text!templates/storelocator.html',
    'lib/text!templates/starshop.html'
], function($, Handlebars, map, api, storelocatorTpl, starshopTpl) {
    var dBody = $('body');

    var homeSelect = function (dHome) {
        var oAddress = map.getAddress(),
            dMap = $('#map'),
            dStores = $('.page_storelocator .stores'),
            dCountry = $('#country'),
            dProvince = $('#province'),
            dCity = $('#city'),
            dDistrict = $('#district'),
            dCountrySelect = dCountry.find('select'),
            dCountryText = dCountry.find('.store_sl_txt'),
            dProvinceSelect = dProvince.find('select'),
            dCitySelect = dCity.find('select'),
            dDistrictSelect = dDistrict.find('select'),
            dProvinceText = dProvince.find('.store_sl_txt'),
            dCityText = dCity.find('.store_sl_txt'),
            dDistrictText = dDistrict.find('.store_sl_txt'),
            dBtn = $('.page_storelocator .searchbtn'),
            sProvince,
            sCity,
            sDistrict,
            updateText = function (findNear) {
                sProvince = dProvinceSelect.find('option:selected').text();
                sCity = dCitySelect.find('option:selected').text();
                sDistrict = dDistrictSelect.find('option:selected').text();

                dProvinceText.html(sProvince);
                dCityText.html(sCity);
                dDistrictText.html(sDistrict);

                dProvinceText.data('val',sProvince);
                dCityText.data('val',sCity);
                dDistrictText.data('val',sDistrict);

                var data = {country:'CN', city:dCityText.data('val'), star:0};
                api.getStorelocator({
                    data:data,
                    success:function(aData){
                        if(findNear && aData.min_distance_shop) {
                            map.updateMarkers(aData.shopes, false);
                            map.centerZoom(aData.min_distance_shop.lat, aData.min_distance_shop.lng, 20);
                        }
                        else {
                            map.updateMarkers(aData.shopes, true);
                        }
                    }
                });
            };


        oHandler = dHome.ChinaCitySelect({
            //'prov' : dProvinceSelect,
            'city' : dCitySelect,
            'dist' : dDistrictSelect,
            'url' : 'admin/index.php/api/shop/location',
            'success' : function(aData){
                setTimeout(function(){
                    dCityText.html(aData.user_city);
                    dCityText.data('val', aData.user_city);
                    dCitySelect.val(aData.user_city);
                    updateText(true);
                    $(".store_sl").selectOrDie({
                        size: 5
                    });
                },2000);
            }
        });

        dProvinceSelect.change(function () {
            updateText(false);
        });

        dCitySelect.change(function () {
            updateText(false);
        });

        dDistrictSelect.change(function () {
            updateText(false);
        });
    };

    var starshopSelect = function (dStarshop) {
        var oAddress = map.getAddress(),
            dContainer = $(".page_starshop"),
            dBtn = $('.page_starshop .searchbtn'),
            dStores = $('.page_starshop .stores'),
            dCountry = $('#country', dContainer),
            dProvince = $('#province', dContainer),
            dCity = $('#city', dContainer),
            dCountrySelect = dCountry.find('select'),
            dProvinceSelect = dProvince.find('select'),
            dCitySelect = dCity.find('select'),
            dProvinceText = dProvince.find('.store_sl_txt'),
            dCityText = dCity.find('.store_sl_txt'),
            sProvince,
            sCity,
            bMap,
            updateText = function () {
                setTimeout(function () {
                    //sProvince = dProvinceSelect.find('option:selected').text();
                    sCity = dCitySelect.find('option:selected').text();

                    //dProvinceText.html(sProvince);
                    dCityText.html(sCity);

                }, 100);
            };

        dCitySelect.change(function () {
            updateText();
            var hash = dCitySelect.val();
            var top = $('#'+hash).position().top - 100;
            $('html, body').animate({scrollTop : top});
        });


        // when click the search button, should the tpl
        dBtn.bind('click', function () {
            api.getStarshop({
                path : 'xxx',
                method : 'get',
                data : {
                    province : sProvince,
                    city : sCity
                },
                success : function (aData) {
                    var nSize = aData.length;

                    // need do some trik for display order
                    for (var i = 0; i < nSize; i++) {
                        if (i % 2) {
                            aData[i].contentClass = 'right';
                            aData[i].imageClass = 'left';
                        } else {
                            aData[i].contentClass = 'left';
                            aData[i].imageClass = 'right';
                        }

                        if (i == (nSize - 1)) {
                            // update page store tpl
                            var str = Handlebars.compile(starshopTpl)({
                                data : aData
                            });

                            dStores.html(str);
                        }
                    }
                }
            })
        });


        // view map feature
        dStores.delegate('.store_view', 'click', function () {
            var mapWrap = $(this).parents('.limit').find('.starshop_map');
            if( !mapWrap.find('.map').length ) {
                var tmpid = 'map-' + ( + new Date() );
                $('<div class="map"></div>')
                    .appendTo( mapWrap )
                    .attr('id' , tmpid)
                    .css({width:mapWrap.width(),height:mapWrap.height() , 'position': 'absolute' , top: 0});
                var data = [{
                    title: $(this).parent().find('h2').eq(0).html(),
                    address: $(this).parent().find('p').eq(1).html(),
                    phone: $(this).parent().find('p').eq(2).html(),
                    lat: $(this).data('lat'),
                    lng: $(this).data('lng')
                }];
                var bMap = new BMap.Map( tmpid );
                
                bMap.addControl(new BMap.NavigationControl());
                var point = new BMap.Point(data[0].lat,data[0].lng);
                var marker = new BMap.Marker(point, {title:data[0].title});
                bMap.centerAndZoom(point, 15);
                bMap.addOverlay(marker);
                if( $(document.body).hasClass('index') ){
                    var styleJson = [
                         {
                                   "featureType": "all",
                                   "elementType": "geometry",
                                   "stylers": {
                                             "hue": "#fff1f4",
                                             "saturation": 50
                                   }
                         },
                         {
                                   "featureType": "water",
                                   "elementType": "all",
                                   "stylers": {
                                             "color": "#ffffff"
                                   }
                         }
                     ]
                    bMap.setMapStyle({styleJson:styleJson});
                } else {
                    bMap.setMapStyle({style: 'light'});
                }
                

                $(this).html(_e('view_shop'))
            } else {
                mapWrap.find('.map').toggle();
                if( mapWrap.find('.map').is(':visible') ){
                    $(this).html(_e('view_shop'));
                } else {
                    $(this).html(_e('view_map'));
                }
            }
        });

        $(".store_sl").selectOrDie({
        });
    };

    var storelocatorSelect = function (dStorelocator) {
        var oAddress = map.getAddress(),
            dContainer = $(".page_storelocator"),
            dMap = $('#map'),
            dStores = $('.page_storelocator .stores'),
            dCountry = $('#country',dContainer),
            dProvince = $('#province', dContainer),
            dCity = $('#city', dContainer),
            dDistrict = $('#district', dContainer),
            dCountrySelect = dCountry.find('select'),
            dCountryText = dCountry.find('.store_sl_txt'),
            dProvinceSelect = dProvince.find('select'),
            dCitySelect = dCity.find('select'),
            dDistrictSelect = dDistrict.find('select'),
            dProvinceText = dProvince.find('.store_sl_txt'),
            dCityText = dCity.find('.sod_label'),
            dDistrictText = dDistrict.find('.store_sl_txt'),
            dBtn = $('.page_storelocator .searchbtn'),
            sProvince,
            sCity,
            sDistrict,
            updateText = function () {
                setTimeout(function () {
                    sProvince = dProvinceSelect.find('option:selected').text();
                    sCity = dCitySelect.find('option:selected').text();
                    sDistrict = dDistrictSelect.find('option:selected').text();

                    dProvinceText.html(sProvince);
                    dCityText.html(sCity);
                    dDistrictText.html(sDistrict);

                    dProvinceText.data('val',sProvince);
                    dCityText.data('val',sCity);
                    dDistrictText.data('val',sDistrict);
                }, 100);
            },
            searchShop = function (findNear) {
                var data = {country:'CN', city:dCityText.data('val'), distinct:dDistrictText.data('val'), star:0};
                api.getStorelocator({
                    data:data,
                    success:function(aData){
                        var str = Handlebars.compile(storelocatorTpl)({
                            title : aData.shopes[0].city,
                            data : aData.shopes,
                            _eView: _e("view_map")
                        });

                        dStores.html(str);
                        if(findNear && aData.min_distance_shop) {
                            map.updateMarkers(aData.shopes, false);
                            map.centerZoom(aData.min_distance_shop.lat, aData.min_distance_shop.lng, 20);
                        }
                        else {
                            map.updateMarkers(aData.shopes, true);
                        }
                    }
                });
            };

        oHandler = dStorelocator.ChinaCitySelect({
            //'prov' : dProvinceSelect,
            'city' : dCitySelect,
            'dist' : dDistrictSelect,
            'url' : 'admin/index.php/api/shop/location',
            'success' : function(aData){
                setTimeout(function(){

                    $(".store_sl").selectOrDie({
                        size: 5
                    });
                    dCityText.html(aData.user_city);
                    dCityText.data('val',aData.user_city);
                    searchShop(true);
                },2000);
            }
        });

        dProvinceSelect.change(function () {
            updateText();
        });

        dCitySelect.change(function () {
            updateText();
             setTimeout(function(){
                searchShop(true);
            } , 200);
        });

        dDistrictSelect.change(function () {
            updateText();
             setTimeout(function(){
                searchShop(true);
            } , 200);
        });

        // when click the search button, should update the map center and markers
        dBtn.bind('click', function () {
            searchShop(false);
        });

        // view map feature
        dStores.delegate('.store_additem .store_view', 'click', function () {
            var data = [{
                title: $(this).parent().find('p').eq(0).html(),
                address: $(this).parent().find('p').eq(1).html(),
                phone: $(this).parent().find('p').eq(2).html(),
                lat: $(this).data('lat'),
                lng: $(this).data('lng')
            }];
            map.updateMarkers(data,true);
            map.zoomMap(18);
            var height = $('#map').position().top;
            $('html,body').animate({scrollTop:height});
        });
    };

    // enable the select
    var init = function () {
        var dHome = $('.index #home-selectbox'),
            dStarshop = $('.starstore #store-selectbox'),
            dStorelocator = $('.storelocator #store-selectbox');


        // home page, the city select is display none, we need it
        if (dHome.length) {
            homeSelect(dHome);
        }

        // starshop page
        if (dStarshop.length) {
            starshopSelect(dStarshop);
        }

        // storelocator page
        if (dStorelocator.length) {
            storelocatorSelect(dStorelocator);
        }



    };

    return {
        init : init
    }
})
