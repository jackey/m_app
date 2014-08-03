define([
    // libs
    'jQuery'
], function($) {
    var requset = function (oConfig) {
        $.ajax({
            url : '' + oConfig.path,
            method : oConfig.method,
            data : oConfig.data ? oConfig.data : {},
            success: function(data) {
                oConfig.success(data);
            },
            error: function (err) {
                oConfig.failure(err);
            }
        })
    };

    // data format: { latitude : xxxx, longitude : xxxxx }
    var getStorelocator = function (oConfig) {
        requset({
             path : 'admin/index.php/api/shop/search',
             method : 'get',
             data : oConfig.data,
             success : function (data) {
                 oConfig.success(data.data);
             },
             failure : function (err) {
                 oConfig.failure(err);
             }
        })
    };

    // data format: { province : sProvince, city : sCity }, may need country name
    var getStarshop = function (oConfig) {
        // requset({
        //     path : 'xxx',
        //     method : 'get',
        //     data : oConfig.data,
        //     success : function (data) {
        //         oConfig.success(data);
        //     },
        //     failure : function (err) {
        //         oConfig.failure(err);
        //     }
        // })

        var fakeData = [
            {
                city : _e('南京'),
                title : _e('上海南京路店'),
                name : _e('南京路店'),
                address : _e('xxxxxxxxxxxxxxxxx'),
                phone : '187 567 987',
                image : 'images/wlmq_bj_star.jpg',
                geo : {
                    latitude : 31.440416,
                    longitude : 121.433701
                }
            },
            {
                city : _e('南京'),
                title : _e('SHANGHAI'),
                name : _e('南京路店'),
                address : _e('xxxxxxxx'),
                phone : '187 567 987',
                image : 'images/sh_njl_star.jpg',
                geo : {
                    latitude : 31.460416,
                    longitude : 121.473701
                }
            }
        ];

        oConfig.success(fakeData);
    }

    // data format: { id : xxxx }, the series id
    var getAlbumList = function (oConfig) {
        // requset({
        //     path : 'xxx',
        //     method : 'get',
        //     data : oConfig.data,
        //     success : function (data) {
        //         oConfig.success(data);
        //     },
        //     failure : function (err) {
        //         oConfig.failure(err);
        //     }
        // })

        var lookbook1 = [
            {
                url : 'pic/lookbook/garden/850_850/1.jpg',
                title : _e('Garden')
            },
            {
                url : 'pic/lookbook/garden/850_850/2.jpg',
                title : _e('Garden')
            },
            {
                url : 'pic/lookbook/garden/850_850/3.jpg',
                title : _e('Garden')
            },
            {
                url : 'pic/lookbook/garden/850_850/4.jpg',
                title : _e('Garden')
            },
            {
                url : 'pic/lookbook/garden/850_850/5.jpg',
                title : _e('Garden')
            },
            {
                url : 'pic/lookbook/garden/850_850/6.jpg',
                title : _e('Garden')
            },
            {
                url : 'pic/lookbook/garden/850_850/7.jpg',
                title : _e('Garden')
            },
            {
                url : 'pic/lookbook/garden/850_850/8.jpg',
                title : _e('Garden')
            },
            {
                url : 'pic/lookbook/garden/850_850/16.jpg',
                title : _e('Garden')
            },
            {
                url : 'pic/lookbook/garden/850_850/18.jpg',
                title : _e('Garden')
            },
            {
                url : 'pic/lookbook/garden/850_850/19.jpg',
                title : _e('Garden')
            },
            {
                url : 'pic/lookbook/garden/850_850/20.jpg',
                title : _e('Garden')
            },
            {
                url : 'pic/lookbook/garden/850_850/21.jpg',
                title : _e('Garden')
            },
            {
                url : 'pic/lookbook/garden/850_850/22.jpg',
                title : _e('Garden')
            },
            {
                url : 'pic/lookbook/garden/850_850/23.jpg',
                title : _e('Garden')
            },
            {
                url : 'pic/lookbook/garden/850_850/35.jpg',
                title : _e('Garden')
            },
            {
                url : 'pic/lookbook/garden/850_850/36.jpg',
                title : _e('Garden')
            },
            {
                url : 'pic/lookbook/garden/850_850/37.jpg',
                title : _e('Garden')
            },
            {
                url : 'pic/lookbook/garden/850_850/38.jpg',
                title : _e('Garden')
            },
            {
                url : 'pic/lookbook/garden/850_850/39.jpg',
                title : _e('Garden')
            },
            {
                url : 'pic/lookbook/garden/850_850/41.jpg',
                title : _e('Garden')
            },
            {
                url : 'pic/lookbook/garden/850_850/42.jpg',
                title : _e('Garden')
            }
        ]

        var lookbook2 = [
            {
                url : 'pic/lookbook/modernart/850_850/28.jpg',
                title : _e('Modernart')
            },
            {
                url : 'pic/lookbook/modernart/850_850/29.jpg',
                title : _e('Modernart')
            },
            {
                url : 'pic/lookbook/modernart/850_850/30.jpg',
                title : _e('Modernart')
            },
            {
                url : 'pic/lookbook/modernart/850_850/31.jpg',
                title : _e('Modernart')
            },
            {
                url : 'pic/lookbook/modernart/850_850/32.jpg',
                title : _e('Modernart')
            },
            {
                url : 'pic/lookbook/modernart/850_850/33.jpg',
                title : _e('Modernart')
            },
            {
                url : 'pic/lookbook/modernart/850_850/34.jpg',
                title : _e('Modernart')
            },
            {
                url : 'pic/lookbook/modernart/850_850/40.jpg',
                title : _e('Modernart')
            }
        ]

        var lookbook3 = [
            {
                url : 'pic/lookbook/ocean/850_850/9.jpg',
                title : _e('Ocean')
            },
            {
                url : 'pic/lookbook/ocean/850_850/10.jpg',
                title : _e('Ocean')
            },
            {
                url : 'pic/lookbook/ocean/850_850/11.jpg',
                title : _e('Ocean')
            },
            {
                url : 'pic/lookbook/ocean/850_850/12.jpg',
                title : _e('Ocean')
            },
            {
                url : 'pic/lookbook/ocean/850_850/13.jpg',
                title : _e('Ocean')
            },
            {
                url : 'pic/lookbook/ocean/850_850/14.jpg',
                title : _e('Ocean')
            },
            {
                url : 'pic/lookbook/ocean/850_850/15.jpg',
                title : _e('Ocean')
            },
            {
                url : 'pic/lookbook/ocean/850_850/24.jpg',
                title : _e('Ocean')
            },
            {
                url : 'pic/lookbook/ocean/850_850/25.jpg',
                title : _e('Ocean')
            },
            {
                url : 'pic/lookbook/ocean/850_850/26.jpg',
                title : _e('Ocean')
            },
            {
                url : 'pic/lookbook/ocean/850_850/27.jpg',
                title : _e('Ocean')
            }
        ]

        var streetshot = [
            {
                url : 'pic/streetshot/850_850/1.jpg',
                title : _e('Streetshot')
            },
            {
                url : 'pic/streetshot/850_850/2.jpg',
                title : _e('Streetshot')
            },
            {
                url : 'pic/streetshot/850_850/3.jpg',
                title : _e('Streetshot')
            },
            {
                url : 'pic/streetshot/850_850/4.jpg',
                title : _e('Streetshot')
            },
            {
                url : 'pic/streetshot/850_850/5.jpg',
                title : _e('Streetshot')
            },
            {
                url : 'pic/streetshot/850_850/6.jpg',
                title : _e('Streetshot')
            },
            {
                url : 'pic/streetshot/850_850/7.jpg',
                title : _e('Streetshot')
            },
            {
                url : 'pic/streetshot/850_850/8.jpg',
                title : _e('Streetshot')
            },
            {
                url : 'pic/streetshot/850_850/9.jpg',
                title : _e('Streetshot')
            },
            {
                url : 'pic/streetshot/850_850/10.jpg',
                title : _e('Streetshot')
            },
            {
                url : 'pic/streetshot/850_850/11.jpg',
                title : _e('Streetshot')
            },
            {
                url : 'pic/streetshot/850_850/12.jpg',
                title : _e('Streetshot')
            },
            {
                url : 'pic/streetshot/850_850/13.jpg',
                title : _e('Streetshot')
            },
            {
                url : 'pic/streetshot/850_850/6.jpg',
                title : _e('Streetshot')
            },
            {
                url : 'pic/streetshot/850_850/7.jpg',
                title : _e('Streetshot')
            },
            {
                url : 'pic/streetshot/850_850/8.jpg',
                title : _e('Streetshot')
            },
            {
                url : 'pic/streetshot/850_850/9.jpg',
                title : _e('Streetshot')
            },
            {
                url : 'pic/streetshot/850_850/10.jpg',
                title : _e('Streetshot')
            },
            {
                url : 'pic/streetshot/850_850/11.jpg',
                title : _e('Streetshot')
            },
            {
                url : 'pic/streetshot/850_850/12.jpg',
                title : _e('Streetshot')
            },
            {
                url : 'pic/streetshot/850_850/13.jpg',
                title : _e('Streetshot')
            }
        ]

        var campaign = [
            {
                url : 'pic/campaign/1.jpg',
                title : _e('Campaign')
            },
            {
                url : 'pic/campaign/2.jpg',
                title : _e('Campaign')
            },
            {
                url : 'pic/campaign/3.jpg',
                title : _e('Campaign')
            },
            {
                url : 'pic/campaign/4.jpg',
                title : _e('Campaign')
            },
            {
                url : 'pic/campaign/5.jpg',
                title : _e('Campaign')
            },
            {
                url : 'pic/campaign/6.jpg',
                title : _e('Campaign')
            },
            {
                url : 'pic/campaign/7.jpg',
                title : _e('Campaign')
            },
            {
                url : 'pic/campaign/8.jpg',
                title : _e('Campaign')
            },
            {
                url : 'pic/campaign/9.jpg',
                title : _e('Campaign')
            },
            {
                url : 'pic/campaign/10.jpg',
                title : _e('Campaign')
            },
            {
                url : 'pic/campaign/11.jpg',
                title : _e('Campaign')
            }
        ]

        if(oConfig.data.id==5) {
            //oConfig.success(streetshot);
          requset({
              path : 'admin/api/streehot/ingroup',
              method : 'get',
              success : function (data) {
                if (data["status"] == 0 ) {
                  oConfig.success(data["data"]);
                }
              },
              failure : function (err) {
                  oConfig.failure(err);
              }
          });
        }
        if(oConfig.data.id==1) {
          requset({
              path : 'admin/api/lookbook/ingroup',
              method : 'get',
              success : function (data) {
                if (data["status"] == 0 ) {
                  oConfig.success(data["data"]);
                }
              },
              failure : function (err) {
                  oConfig.failure(err);
              }
          });
        }
        if(oConfig.data.id==4) {
            oConfig.success(campaign);
        }

    }

    // data format: { id : xxxx }, the series id
    var getVideoList = function (oConfig) {
        // requset({
        //     path : 'xxx',
        //     method : 'get',
        //     data : oConfig.data,
        //     success : function (data) {
        //         oConfig.success(data);
        //     },
        //     failure : function (err) {
        //         oConfig.failure(err);
        //     }
        // })

        var fakeData = [
            {
                mp4 : 'media/demo.mp4',
                webm : 'media/demo.webm',
                ogv : 'media/demo.ogv',
                poster : 'media/demo.jpg',
                title : _e('Making of fall')
            }
        ]

        oConfig.success(fakeData);
    }

    var getWeibo = function (oConfig) {
        // requset({
        //     path : 'xxx',
        //     method : 'get',
        //     success : function (data) {
        //         oConfig.success(data);
        //     },
        //     failure : function (err) {
        //         oConfig.failure(err);
        //     }
        // })

        var fakeData = {
            date : _e('Monday 23 may'),
            content : _e('年轻OL的商务着装，可能太严肃，可能太时髦，或者像Lily这样正合适 作为年轻OL商务时装的开创者')
        }

        oConfig.success(fakeData);
    }

    // data format: { id : xxxx }, the news id
    var getNews = function (oConfig) {
        requset({
             path : 'admin/index.php/api/news/index',
             method : 'get',
             data : oConfig.data,
             success : function (data) {
                 var news_data = data.data;
                 var date = news_data.cdate.split(' ');
                 news_data.image = news_data["thumbnail"];
                 news_data.cdate = date[0];
                 oConfig.success(news_data);
             },
             failure : function (err) {
                 oConfig.failure(err);
             }
        })

//        var fakeData = {
//            image : 'images/event_img.jpg',
//            title : '中国零售业可持续发展创新模式高峰论坛',
//            date : '2013年5月30日',
//            content : '"中国零售业可持续发展创新模式高峰论坛" 是由上海丝绸集团旗品牌发展有限公司(Lily品牌)主办，中国商业地产协会、第一财经频道/第一地产、搜狐网财经频道、零点研究咨询集团（上海）协办的一场零售业高峰论坛。论坛以“店商vs电商: 商机再造，谁主未来？" 为主题，汇聚了来自全国的近400名企业领袖、行业专家、电商精英及各界媒体，针对当下中国零售业面临的全新商业格局进行探讨辨析，为实体零售业的未来发展出谋献策。'
//        }
//
//        oConfig.success(fakeData);
    }

    var setCookie = function(name, value, expire, path, domain, s){
        if ( document.cookie === undefined ){
            return false;
        }

        if (expire < 0){
            value = '';
        }

        var dt = new Date();
        dt.setTime(dt.getTime() + 1000 * expire);

        document.cookie = name + "=" + encodeURIComponent(value) +
            ((expire) ? "; expires=" + dt.toGMTString() : "") +
            ((s) ? "; secure" : "");

        return true;
    };

    var removeCookie = function(name, path, domain){
        return setCookie(name, '', -1, path, domain);
    };

    return {
        getStorelocator : getStorelocator,
        getStarshop : getStarshop,
        getAlbumList : getAlbumList,
        getVideoList : getVideoList,
        getWeibo : getWeibo,
        getNews : getNews,
        setCookie : setCookie,
        removeCookie : removeCookie
    }
})
