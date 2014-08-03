
//Require JS will call me
window.startTrack = function ($) {
  var lang = window.lang != "en" ? "ZH_PC": "EN_PC";
  
  function cleanTitle(title) {
    // " " => "-"
    if (title == "") {
      return title;
    }
    title = title.replace(/\s/ig, "-");
    
    return title;
  }
  
  function callGa(data) {
    ga('send', 'event', 'lily_official', data, data);
  }
  
  function debug_console(obj) {
    if (typeof console != "undefined") {
      console.log(obj);
    }
  }
  
  // Header nav menu
  $("#nav h2").click(function () {
    var title = $(this).attr("data-title");
    if (title) {
      callGa(lang + "_" + title);
      debug_console(lang + "_" + title);
    }
  });
  
  $("#nav a").click(function () {
    var title = $(this).attr("data-title");
    if (title) {
      callGa(lang + "_" + title);
      debug_console(lang + "_" + title);
    }
  });
  
  // Footer menu
  $("body .ft_com > .ft_list a").click(function () {
    //var title = cleanTitle($(this).html());
    var title = $(this).attr("data-title");
    if (title) {
      ga('send', 'event', 'lily_official', lang + "_" + title, lang + "_" + title);
      debug_console(lang + "_" + title);
    }
  });
  
  // Home lookbook / campagin
  var pre = "HOME";
  $(".home_lookbook .home_lb_item a").click(function () {
    var title = cleanTitle($(this).html());
    ga('send', 'event', 'lily_official', lang + "_" + pre + "-" + title, lang + "_" + pre + "-" + title);
    debug_console(lang + "_" + pre + "-" + title);
  });
  $(".home_cpbox").click(function () {
    var title = "Campaign";
    ga('send', 'event', 'lily_official', lang + "_" + pre + "-" + title, lang + "_" + pre + "-" + title);
    debug_console(lang + "_" + pre + "-" + title);
  });
  
  // Event / 新闻
  $(".event_open").click(function () {
    var title = cleanTitle($(this).attr("data-title"));
    ga('send', 'event', 'lily_official', lang + "_" + title, lang + "_" + title);
  });
  
  // Campagin 
  $(".page_campaign .cp_tit").click(function () {
    var title = "Campaign-Campaign";
    ga('send', 'event', 'lily_official', lang + "_" + title, lang + "_" + title);
    debug_console(lang + "_" + title);
  });
  $(".page_campaign .cp_season").click(function () {
    var title = $(this).html();
    title = title.replace(/\s/ig, "");
    ga('send', 'event', 'lily_official', lang + "_" + title, lang + "_" + title);
    debug_console(lang + "_" + title);
  });
  
  //social / weibo/wechat 链接
  $(".ft_share1").click(function () {
    var title = "Follow_Sina";
    ga('send', 'event', 'lily_official', lang + "_" + title, lang + "_" + title);
  });
  $(".ft_share3").click(function () {
    var title = "Follow_Wechat";
    ga('send', 'event', 'lily_official', lang + "_" + title, lang + "_" + title);
  });
};