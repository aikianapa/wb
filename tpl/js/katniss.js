/*!
 * Katniss v2.0.0 (https://themepixels.me/starlight)
 * Copyright 2017-2018 ThemePixels
 * Licensed under ThemeForest License
 */

 'use strict';

 $(document).ready(function() {

   // displaying time and date in left sidebar
   var interval = setInterval(function() {
     var momentNow = moment();
     $('#ktDate').html(momentNow.format('MMMM DD, YYYY hh:mm:ss') + ' '
       + momentNow.format('dddd')
       .substring(0,3).toUpperCase());
   }, 100);

   $('.kt-sideleft').perfectScrollbar({
     useBothWheelAxes: false,
     suppressScrollX: true,
     wheelPropogation: true
   });

   // hiding all sub nav in left sidebar by default.
   $('.nav-sub').slideUp();

   // showing sub navigation to nav with sub nav.
   $('.with-sub.active + .nav-sub').slideDown();

   // showing sub menu while hiding others
   $('.with-sub').on('click', function(e) {
     e.preventDefault();

     var nextElem = $(this).next();
     if(!nextElem.is(':visible')) {
       $('.nav-sub').slideUp();
     }
     nextElem.slideToggle();
   });

   // showing and hiding left sidebar
   $('#naviconMenu').on('click', function(e) {
     e.preventDefault();
     $('body').toggleClass('hide-left');
   });

   // pushing to/back left sidebar
   $('#naviconMenuMobile').on('click', function(e) {
     e.preventDefault();
     $('body').toggleClass('show-left');
   });

   // highlight syntax highlighter
   $('pre code').each(function(i, block) {
     hljs.highlightBlock(block);
   });

    $(".kt-loading").css("opacity","0").css("z-index","-1");
    $(document).on("wb_ajax_done",function(a,b,c,d){
        var ajax=$(b).attr("data-wb-ajax");
        var menu=".nav.kt-sideleft-menu";
        $(menu).find(".nav-link.active").removeClass("active");
        $(menu).find("[data-wb-ajax='"+ajax+"']").parents(".nav-sub").hide();
        $(menu).find("[data-wb-ajax='"+ajax+"']").addClass("active");
        $(menu).find("[data-wb-ajax='"+ajax+"']").parents(".nav-item").find("a.with-sub").addClass("active");
        $(menu).find("[data-wb-ajax='"+ajax+"']").parents(".nav-sub").removeAttr("style");
        if ($(".content-box .element-header").length) {
            $(".kt-pagetitle").html("<h5 class='w-100'>"+$(".content-box .element-header").html()+"</h5>");
            $(".content-box .element-header").remove();
        }
    });

	$(document).delegate(".nav-tabs .nav-link.disabled","click",function(){
		return false;
	});

    $(document).delegate(".nav-tabs .nav-link:not(.disabled)","click",function(){
	    var $parent = $(this).parent(".nav-tabs");
	    $parent.find(".nav-link").removeClass("active");
	    $(this).addClass("active");
	    var $tab=$($(this).attr("href"));
	    $tab.parent(".tab-content").find(".tab-pane").removeClass("active");
	    $tab.addClass("active");
    });

});
