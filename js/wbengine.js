"use strict";
var $ = jQuery.noConflict();
$.fn.outerHTML = function (s) {
    return s ? this.before(s).remove() : jQuery("<p>").append(this.eq(0).clone()).html();
};

function wb_include(url,defer,async) {
    if ($(document).data("wb_include")==undefined) {
        $(document).data("wb_include",[]);
    }
    var loaded=$(document).data("wb_include");
    var res=false;
    $(loaded).each(function(i){
        if (loaded[i]==url) {
		res=true;
	}
    });
    if (res==false) {
                if (url.substr(-3) == ".js" || url.indexOf("js?")>0) {
                        new Promise(function (resolve, reject) {
                                var s;
                                s = document.createElement('script');
                                s.src = url;
                                s.onload = resolve;
                                s.onerror = reject;
                                document.head.appendChild(s);
                        });
                } else {
                        if (url.substr(-4) == ".css" || url.indexOf(".css?")>0) {
                                    var inc = document.createElement('link');
                                    inc.type = "text/css";
                                    inc.rel = "stylesheet"
                                    inc.href = url;
                                } else if (url.substr(-5) == ".less" ) {
                                    var inc = document.createElement('link');
                                    inc.type = "text/css";
                                    inc.rel = "less"
                                    inc.href = url;
                                }
                                document.getElementsByTagName('body')[0].appendChild(inc);
                        }
            loaded.push(url);
            setTimeout(function(){
		$(document).trigger("wb_include",{url:url});
	    },500);

    }
    return;
}

wb_include("/engine/js/php.js");
wb_include("/engine/js/jquery.redirect.js");
wb_include("/engine/js/functions.js");
