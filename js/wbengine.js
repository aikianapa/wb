"use strict";
var $ = jQuery.noConflict();
$.fn.outerHTML = function (s) {
    return s ? this.before(s).remove() : jQuery("<p>").append(this.eq(0).clone()).html();
};

function wb_include(url,defer,async) {
    if (defer==undefined) {defer=false;}
    if (async==undefined) {async=false;}
    var js_script = document.createElement('script');
    js_script.type = "text/javascript";
    js_script.src = url;
    js_script.async = async;
    js_script.defer = defer;
    document.getElementsByTagName('head')[0].appendChild(js_script);
    return;
}

var defer = wb_include("/engine/js/php.js");
var defer = wb_include("/engine/js/jquery.redirect.js");
var defer = wb_include("/engine/js/functions.js",true);