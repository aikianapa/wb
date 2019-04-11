function wb_multiinput() {
    if ($("[data-wb-role=multiinput]").length && $(document).data("wb-multiinput-menu") == undefined) {
        wbapp.getWait("/ajax/getform/common/multiinput_menu/", {}, function(data) {
            $(document).data("wb-multiinput-menu", data);
        });

        wbapp.getWait("/ajax/getform/common/multiinput_row/", {}, function(data) {
            $(document).data("wb-multiinput-row", data);
        });
    }

    $.fn.wbMultiInputEvents = function() {
        var $multi = $(this);
	var name = $multi.attr("name");
	if ($multi.data("wb-multiinput-row") == undefined)  {
		var row = $(document).data("wb-multiinput-row");
		if ($multi.data("wb-multiinput-tpl") == undefined)  {
			var tpl = $($multi.attr("data-wb-tpl")).html();
			$multi.data("wb-multiinput-tpl",tpl)
			$multi.removeAttr("data-wb-tpl");
		} else {
			var tpl = $multi.data("wb-multiinput-tpl");
		}
		row = str_replace("{{template}}", htmlspecialchars_decode(tpl), row);

		$multi.data("wb-multiinput-row",row);
	}
	var row = $multi.data("wb-multiinput-row");
        $multi.undelegate(".wb-multiinput", "mouseenter");
        $multi.delegate(".wb-multiinput", "mouseenter", function() {
            $(document).data("wb-multiinput", this);
        });
        $multi.undelegate(".wb-multiinput", "mouseleave");
        $multi.delegate(".wb-multiinput", "mouseleave", function() {
            $(document).data("wb-multiinput", undefined);
        });

        $multi.undelegate(".wb-multiinput", "contextmenu");
        $multi.delegate(".wb-multiinput", "contextmenu", function(e) {
		var line = $(document).data("wb-multiinput");
		$(document).data("wb-multiinput-multi",$multi);
		$("body").find(".wb-multiinput-menu").remove();
		$("body").append("<div class='wb-multiinput-menu'>" + $(document).data("wb-multiinput-menu") + "</div>");
		var relativeX = (e.clientX - 10);
		var relativeY = (e.clientY - 10);
		$("body").find(".wb-multiinput-menu").css("left", relativeX + "px").css("top", relativeY + "px");
		$("body").find(".wb-multiinput-menu [data-toggle=dropdown]").trigger("click");
		e.preventDefault();
		return false;
        });

        $multi.undelegate(".wb-multiinput .wb-multiinput-del", "click");
        $multi.delegate(".wb-multiinput .wb-multiinput-del", "click", function(e) {
            var line = $(document).data("wb-multiinput");
            console.log("Trigger: before_remove");
            $multi.trigger("before_remove", line);
            $(line).remove();
            if (!$multi.find(".wb-multiinput").length) {
                $multi.append(row);
            }
            $multi.wbMultiInputSort();
            return false;
        });

        $multi.undelegate(".wb-multiinput .wb-multiinput-add", "click");
        $multi.delegate(".wb-multiinput .wb-multiinput-add", "click", function(e) {
            var line = $(document).data("wb-multiinput");
            $(line).after(row);
            $multi.wbMultiInputSort();
            wb_plugins();
            return false;
        });

        $(document).undelegate(".wb-multiinput-menu .dropdown-item", "click");
        $(document).delegate(".wb-multiinput-menu .dropdown-item", "click", function(e) {
		var $multi = $(document).data("wb-multiinput-multi");
		var line = $(document).data("wb-multiinput");
		var row = $multi.data("wb-multiinput-row");
            if ($(this).attr("href") == "#after") {
                $(line).after(row);
            }
            if ($(this).attr("href") == "#before") {
                $(line).before(row);
            }
            if ($(this).attr("href") == "#remove") {
                console.log("Trigger: before_remove");
                $multi.trigger("before_remove", line);
                $(line).remove();
            }
            if (!$multi.find(".wb-multiinput").length) {
                $multi.append(row);
            }
            $multi.wbMultiInputSort();
            $multi.trigger("multiinput", $multi, this);
            wb_plugins();
            e.preventDefault();
        });
    }

    $.fn.wbMultiInputSort = function() {
        var name = $(this).attr("name");
        var last = null;
        $(this).children(".wb-multiinput").each(function(i) {
            $(this).find("input,select,textarea").each(function() {
                if ($(this).attr("data-wb-field") > "") {
                    var field = $(this).attr("data-wb-field");
                } else {
                    var field = $(this).attr("name");
                }
                if (field !== undefined && field > "") {
                    $(this).attr("name", name + "[" + i + "][" + field + "]");
                    $(this).attr("data-wb-field", field);
                }
                last = this;
            });
        });
        if (last !== null) {
            $(last).trigger("change");
        }
    }

        $("[data-wb-role=multiinput][data-wb-tpl]").each(function(){
		if ($(this).data("wb-multiinput-row") == undefined) {
			$(this).sortable({
				update: function(e) {
					$(e.target).wbMultiInputSort();
				}
			});
			$(this).wbMultiInputEvents();
		}

	});
}

$(document).on("wbapp",function(){wb_multiinput();});
$(document).on("wb-delegates",function(){wb_multiinput();});
