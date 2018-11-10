"use strict";
var $ = jQuery.noConflict();
$.fn.outerHTML = function (s) {
    return s ? this.before(s).remove() : jQuery("<p>").append(this.eq(0).clone()).html();
};

function wb_include(url,defer,async) {
    if (defer==undefined) {defer=false;}
    if (async==undefined) {async=false;}
    if ($(document).data("wb_include")==undefined) {
        $(document).data("wb_include",[]);
    }
    var loaded=$(document).data("wb_include");
    var res=false;
    $(loaded).each(function(i){
        if (loaded[i]==url) {res=true;}
    });
    if (res==false) {
                if (url.substr(-3) == ".js" ) {
                        new Promise(function (resolve, reject) {
                                var s;
                                s = document.createElement('script');
                                s.src = url;
                                s.onload = resolve;
                                s.onerror = reject;
                                document.head.appendChild(s);
                        });
                } else {
                        if (url.substr(-4) == ".css" ) {
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
                                    inc.async = async;
                                    inc.defer = defer;
                                    document.getElementsByTagName('body')[0].appendChild(inc);
                        }
            loaded.push(url);
    }
    return;
}

var defer = wb_include("/engine/js/php.js");
var defer = wb_include("/engine/js/jquery.redirect.js");
var defer = wb_include("/engine/js/functions.js");
