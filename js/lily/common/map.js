define([
    // libs
    'jQuery',
    'common/api'
], function($, api) {
    var oPos = {
            latitude : 31.230416,
            longitude : 121.473701
        },
        oAddress = {
            province : _e('上海市'),
            city : _e('上海市'),
            district : _e('徐汇区')
        },
        oGeocoder = null,
        oMap = null,
        aMarkers = [];

    // update map center
    var updateMapCenter = function (oLatLng) {
        if (dMap) {
            var latlng;

            if (oLatLng) {
                latlng = new google.maps.LatLng(oLatLng.latitude, oLatLng.longitude);
            } else {
                latlng = new google.maps.LatLng(oPos.latitude, oPos.longitude);
            }

            oMap.setCenter(latlng);
        }
    };

    // using address to get latlng info
    var getLatLng = function (address, callback) {
        if (oGeocoder) {
            oGeocoder.geocode( { 'address': address }, function(results, status) {
                if (status == google.maps.GeocoderStatus.OK) {
                    var oLatlng = results[0].geometry.location;

                    callback({
                        latitude : oLatlng.k,
                        longitude : oLatlng.A
                    });
                }
            })
        }
    }

    // using address to get latlng info, just use once when gps success
    var updateAddress = function () {
        if (oGeocoder) {
            var latLng = new google.maps.LatLng(oPos.latitude, oPos.longitude);

            oGeocoder.geocode({ 'latLng' : latLng }, function(results, status) {
                if (status == google.maps.GeocoderStatus.OK) {
                    var sProvince = results[0].address_components[5].short_name,
                        sCity = results[0].address_components[4].short_name,
                        sDistrict = results[0].address_components[3].short_name;

                    if (sProvince) {
                        oAddress.province = sProvince;
                    }

                    if (sProvince) {
                        oAddress.city = sCity;
                    }

                    if (sProvince) {
                        oAddress.district = sDistrict;
                    }
                }
            })
        }
    }

    // clear old markers
    var clearMarkers = function () {
        for (var i = 0; i < aMarkers.length; i++) {
            aMarkers[i].setMap(null);
            aMarkers = [];
        }
    }

    // clear old markers
    var zoomMap = function (level) {
        oMap.setZoom(level);
    }

    // update markers
    var updateMarkers = function (data, isCenter) {
        oMap.clearOverlays();
        $.each(data,function(i,obj){
            var point = new BMap.Point(obj.lat,obj.lng);
            var marker = new BMap.Marker(point, {title:obj.title});
            oMap.addOverlay(marker);
            var sContent =
                "<h4 style='margin:0 0 5px 0;padding:0.2em 0'>"+obj.title+"</h4>" +
                "<p>"+_e("Add")+":"+obj.address+"</p>" +
                "<p>"+_e("Tel")+":"+obj.phone+"</p>";
            var infoWindow = new BMap.InfoWindow(sContent);  // 创建信息窗口对象
            marker.addEventListener("click", function(){
                this.openInfoWindow(infoWindow);
            });
        });
        if(isCenter) {
            var point = new BMap.Point(data[0].lat,data[0].lng);
            oMap.centerAndZoom(point, 14);
        }
    }

    // center and zoom map
    var centerZoom = function(lat,lng,zoom) {
        var point = new BMap.Point(lat,lng);
        oMap.centerAndZoom(point, zoom);
    }

    // init map
    var init = function() {
        dMap = $('#map');
        if(dMap.length > 0)  {
            setTimeout(function(){
                oMap = new BMap.Map("map");
                oMap.addControl(new BMap.NavigationControl());
                var point = new BMap.Point(121.478988,31.227919);
                oMap.centerAndZoom(point, 15);
                oMap.setMapStyle({style: 'light'});
//                if( $(document.body).hasClass('index') ){
//                    var styleJson = [
//                         {
//                                   "featureType": "all",
//                                   "elementType": "geometry",
//                                   "stylers": {
//                                             "hue": "#fff1f4",
//                                             "saturation": 50
//                                   }
//                         },
//                         {
//                                   "featureType": "water",
//                                   "elementType": "all",
//                                   "stylers": {
//                                             "color": "#ffffff"
//                                   }
//                         }
//                     ]
//                    oMap.setMapStyle({styleJson:styleJson});
//                } else {
//                    oMap.setMapStyle({style: 'light'});
//                }

            }, 1000);
        }
    }

    // get gps position, just fired when open the page
    var getPosition = function () {
        // Try HTML5 geolocation, default is shanghai
        if (window.navigator && window.navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(data) {
                oPos = {
                    latitude : data.coords.latitude,
                    longitude : data.coords.longitude
                }

                updateAddress();
            })
        }
    }

    var getAddress = function () {
        return oAddress;
    }

    return {
        init : init,
        getLatLng : getLatLng,
        getPosition : getPosition,
        getAddress : getAddress,
        updateMarkers : updateMarkers,
        updateMapCenter : updateMapCenter,
        zoomMap : zoomMap,
        centerZoom: centerZoom
    }
})
