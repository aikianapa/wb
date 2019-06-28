"use strict";
var $ = jQuery.noConflict();

function wb_include(url,defer,async) {
    if ($(document).data("wb_include")==undefined) $(document).data("wb_include",[]);
    if ($(document).data("wb_include_loading")==undefined) $(document).data("wb_include_loading",[]);
    var loaded=$(document).data("wb_include");
    var loading=$(document).data("wb_include_loading");
    var res=false;
    if (loading.includes(url)) res=true;
    if (res==false) {
		loading.push(url);
		$(document).data("wb_include_loading",loading);
                if (url.substr(-3) == ".js" || url.indexOf("js?")>0) {
			$.getScript(url,function(data, textStatus, jqxhr){
				loaded.push(url);
				$(document).data("wb_include",loaded);
				$(document).trigger("wb_include",{url:url});
				console.log(url);
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
                                loaded.push(url);
                                $(document).data("wb_include",loaded);
                                $(document).trigger("wb_include",{url:url});
                        }
    }
    return;
}
wb_include("/engine/js/vue.min.js");
wb_include("/engine/js/php.js");
wb_include("/engine/js/jquery.redirect.js");
wb_include("/engine/js/functions.js");
