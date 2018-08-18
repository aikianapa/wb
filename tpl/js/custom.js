$(document).ready(function(){
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
});
