"use strict";
jQuery.fn.outerHTML = function (s) {
    return s ? this.before(s).remove() : jQuery("<p>").append(this.eq(0).clone()).html();
};
var $ = jQuery.noConflict();

function wb_include(url) {
    if (!$(document).find("script[src='" + url + "']").length) {
        var s = document.createElement('script');
        s.src = url;
        s.type = "text/javascript";
        document.body.appendChild(s);
    }
}

wb_include("/engine/js/php.js");
wb_include("/engine/js/jquery.redirect.js");
wb_include("/engine/js/functions.js");