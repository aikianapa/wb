var wbapp = new Object();
$(function() {
	if ($(document).data("wb_include")==undefined) {
		// загрузка напрямую
		wb_init();
	} else {
		// загрузка из wbengine
	    var loading = setInterval(function() {
		if ($(document).data("wb_include").length >= 3) {
		    wb_init();
		    clearInterval(loading);
		}
	    },10);
	}
});



function wb_init() {
    if ($("link[rel$=less],style[rel$=less]").length) wb_include("/engine/js/less.min.js");
    var wb_getsysmsg = function() {
        if ($("#setup.wbengine").length) {
            var url = "/engine/ajax.php?getsysmsg"
        } else {
            var url = "/ajax/getsysmsg/"
        }
        var sysmsg=null;
        wbapp.getWait(url, {}, function(data) {
            sysmsg = $.parseJSON(base64_decode(data));
        });
        return sysmsg;
    }

    var wb_getlocale = function(type,name) {
        var url = "/ajax/getlocale/";
        var msg = null;
        //if (type == "url") url = name;
        wbapp.postWait(url, {"type":type,"name":name}, function(data) {
            msg = $.parseJSON(base64_decode(data));
        });
        return msg;
    }


	$.fn.wbChangeWatcher = function(mode) {
		if (mode==undefined) mode="start";
		if (mode=="start" && $(this).data('DOMSubtreeModified')==undefined) {
			$(this).bind( 'DOMSubtreeModified',function(e){
				$(this).trigger("change");
				$(this).data("_changed",true);
			});
		} else if (mode=="stop") {
			$(this).data('DOMSubtreeModified',undefined);
			$(this).data("_changed",false);
			$(this).unbind( 'DOMSubtreeModified');
		}
	}





    wbapp.ajaxWait = function(options) {
        return wb_ajaxWait([options]);
    }
    wbapp.getWait = function(url, data, func) {
		var res;
        wb_ajaxWait([ {
async: false,
type: 'GET',
data: data,
url: url,
success: function(data) {
                if (func !== undefined) {
                    res = func(data);
                } else {
					res = data;
				}
            }
        }]);
        return res;
    }
    wbapp.postWait = function(url, data, func) {
        var res;
        wb_ajaxWait([ {
async: false,
type: 'POST',
data: data,
url: url,
success: function(data) {
                if (func !== undefined) {
                    res = func(data);
                } else {
					res = data;
		}
            }
        }]);
        return res;
    }
    wbapp.scriptWait = function(url, data, func) {
        new Promise(function (resolve, reject) {
            var s;
            s = document.createElement('script');
            s.src = url;
            s.onload = resolve;
            s.onerror = reject;
            document.head.appendChild(s);
		if (func !== undefined) {
		    return func(data);
		}
        });
    }

    wbapp.settings = wb_settings();
    wbapp.sysmsg = wb_getsysmsg();
    wbapp.getlocale = function(type,form) {
        return wb_getlocale(type,form);
    }
    wbapp.getTree = function(tree, branch, parent, childrens) {
        return wb_gettree(tree, branch, parent, childrens);
    }
    wbapp.getTreeDict = function(tree) {
        return wb_gettreedict(tree);
    }
    wbapp.merchantModal = function(mode) {
        return wb_merchant_modal(mode);
    }
    wbapp.getSnippet = function(snippet) {
        return wb_getsnippet(snippet);
    }
    wbapp.newId = function(separator, prefix) {
        return wb_newid(separator, prefix);
    }
    wbapp.modal = function(id,selector) {
	return wb_modal(id,selector);
    }
    wbapp.func = function(func,params,fn=undefined) {
		res = wb_func(func,params,fn);
		if (is_object) {
			return res;
		} else {
			return base64_decode(res);
		}
    }
    wbapp.user = wb_user();
	wbapp.getForm = function(form,mode,fn=undefined) {
		return wb_func("wbGetForm",[form,mode],fn);
	}

	wbapp.baloon = function(text,type='info',delay=3000) {
		if (!wb_plugins_loaded()) {
			console.log("Error: wb_plugins not loaded!");
			return;
		}
		$.bootstrapGrowl(text, {
			ele: 'body',
			type: type,
			offset: {
			from: 'top',
					amount: 20
			},
			align: 'right',
			width: "auto",
			delay: delay,
			allow_dismiss: true,
				stackup_spacing: 10
		});
	}


    $("body").removeClass("cursor-wait");
    $(document).trigger("wbapp");
}

$(document).on("wbapp",function(){
	wb_alive();
	wb_delegates();
});


function wb_delegates() {
    wb_pagination();
    wb_formsave();
    wb_base_fix();
    wb_plugins();
    wb_tree();
    wb_ajax();
    $(document).trigger("wb-delegates");
}

function wb_settings() {
    if ($("#setup.wbengine").length) {
        return;
    }
    if ($(document).data("settings") !== undefined) {
        return $(document).data("settings");
    } else {
        var settings = null;
        wbapp.getWait("/ajax/settings/", {}, function(data) {
            settings = $.parseJSON(base64_decode(data));
            $(document).data("settings", settings);
        });
        return settings;
    }
}

function wb_user() {
	var user;
	if ($(document).data("wbapp_user")!==undefined) {
		return $(document).data("wbapp_user");
	} else {
		wbapp.getWait("/ajax/getuser/", {}, function(data) {
			user = $.parseJSON(base64_decode(data));
			if (user!==false) $(document).data("wbapp_user",user);
		});
		return user;
	}
}

function wb_gettree(tree, branch, parent, childrens) {
    if (branch == undefined) {
        var branch = "";
    }
    if (parent == undefined) {
        var parent = true;
    }
    if (childrens == undefined) {
        var childrens = true;
    }
    if (tree == undefined) return;
    wbapp.postWait("/ajax/gettree/", {
        tree,
        branch,
        parent,
        childrens
    }, function(data) {
        tree = $.parseJSON(base64_decode(data));
    });
    return tree;
}

function wb_gettreedict(tree) {
    if (tree == undefined) return;
    wbapp.postWait("/ajax/gettreedict/", {tree}, function(data) {
        dict = $.parseJSON(base64_decode(data));
    });
    return dict;
}


function wb_ajaxWait(ajaxObjs, fn) {
    if (!ajaxObjs) {
        return;
    }
    if (fn == undefined) {
        var fn = function() {
            return data;
        }
    }
    var data = [];
    var ajaxCount = ajaxObjs.length;
    for (var i = 0; i < ajaxObjs.length; i++) { //append logic to invoke callback function once all the ajax calls are completed, in success handler.
        $.ajax(ajaxObjs[i]).done(function(res) {
		ajaxCount--;
		if (ajaxObjs.length > 1) {data.push(res);} else {data = res;}
        }).fail(function(){
		ajaxCount--;
		if (ajaxObjs.length > 1) {data.push(false);} else {data = false;}
	}); //make ajax call
    };
    while (ajaxCount>0) {
	// wait all done
    }
    fn();
}

function wb_func(func,params,fn=undefined) {
	if (params == undefined) params=[];
	var obj = {};
	for (var i = 0; i < params.length; ++i) obj[i] = params[i];
	var res;
    wbapp.postWait("/ajax/callfunc/" + func, obj, function(data) {
		if (is_object(data)) {res=data;} else {res = base64_decode(data);}
		if (fn == undefined) {
			return res;
		} else {
			return fn(res);
		}
	});
    return res;
}

function wb_getsnippet(snippet) {
	var res;
        wbapp.postWait("/ajax/getform/snippets/" + snippet,{}, function(data) {res=data;});
        return res;
}

function wb_merchant_modal(mode) {
    var merchant = wbapp.settings.merchant;
    if (mode == undefined) {
        mode = "show";
    }
    if (mode == "show") {
        $.ajax({
async: false,
type: "POST",
url: "/module/" + merchant,
success: function(data) {
                if ($(document).find("#" + merchant + "Modal").length) {
                    $(document).find("#" + merchant + "Modal").replaceWith($(data).find("#" + merchant + "Modal"));
                } else {
                    $(document).find("body").append($(data).find("#" + merchant + "Modal"));
                }
            }
        });
    }
    return $("#" + merchant + "Modal").modal(mode);
}

function wb_alive() {
    if ($("body").attr("data-wb-alive") == "true") {
        var list = [];
        $("[data-wb-cache]").each(function(i) {
            list.push($(this).attr("data-wb-cache"));
        });
        var post = {data:"wb_get_user_role"};
        if (!$(".modal#wb_session_die").length) post.modal = true;
        setInterval(function() {
		if ($(document).data("user_role")!==undefined) post.user_role = $(document).data("user_role");
		if (!$(".modal#wb_session_die").length) {post.modal = true;} else {post.modal = false;}
		$.ajax({
url: "/ajax/alive"
,type: "POST"
,async: true
,data: post
,cache: list}).done(function(ret){
                ret = $.parseJSON(base64_decode(ret));
                if ($(document).data("user_role")==undefined && ret.user_role!==undefined) $(document).data("user_role",ret.user_role);
                if (ret == "false") ret["mode"] = "wb_session_die";
                if (!$(".modal#wb_session_die").length && ret.modal !== undefined) $("body").append(ret.modal);
                if (ret["mode"] !== undefined && ret["mode"] == "wb_session_die") {
			$(".modal#wb_session_die").modal("show");
			console.log("session_die");
                }
	    });
        }, 60000);
    }
}

function wb_get_cdata(cdata) {
    return cdata.replace("<![CDATA[", "").replace("]]>", "");
}

function wb_json_replacer(key, value) {
    if (is_object(value)) return value;
    if (is_string(value)) {
        value=str_replace("'",'\u0027',value);
        value=str_replace('"','\u0022',value);
        value=str_replace('&','\u0026',value);
        value=str_replace('<','\u003C',value);
        value=str_replace('>','\u003E',value);
    }
    return value;
}

function wb_json_encode(obj) {
    return JSON.stringify(obj,wb_json_replacer);
}

function wb_json_decode(str) {
    return JSON.safeParse(str);
}

function wb_modal(id,selector) {
	if (id == undefined) {var id = "commonModal";}
	if (selector == undefined) {var selector = "body";}
	if (substr(id,0,1)=="#") {id=substr(id,1);}
	if ($(document).data("modal")==undefined) {
		wbapp.getWait("/ajax/getform/common/modal/", {}, function(data) {
			$(document).data("modal", data);
		});
	}
	if ($(selector).find("#"+id).length) return $("#"+id);
	$(selector).append($(document).data("modal"));
	$(selector).find(".modal:last").attr("id",id);
	return $("#"+id);
}


function wb_tree() {
    if ($(document).data("wb-tree-rowmenu") == undefined && $(".wb-tree").length) {
        wbapp.getWait("/ajax/getform/common/tree_rowmenu/", {}, function(data) {
            $(document).data("wb-tree-rowmenu", data);
        });
        wbapp.getWait("/ajax/getform/common/tree_row/", {}, function(data) {
            $(document).data("wb-tree-row", data);
        });
        wbapp.getWait("/ajax/getform/common/tree_ol/", {}, function(data) {
            $(document).data("wb-tree-ol", data);
        });
        wbapp.getWait("/ajax/getform/common/tree_edit/", {}, function(data) {
            $(document).data("wb-tree-edit", data);
        });
        wbapp.getWait("/ajax/getform/common/tree_dict/", {}, function(data) {
            $(document).data("wb-tree-dict", data);
        });
    }
    setTimeout(function() {
        $(document).find(".wb-tree-item.dd-collapsed").each(function() {
            $(this).find("button[data-action=collapse]").hide();
            $(this).find("button[data-action=expand]").show();
        });
    }, 10);
    $(document).off("wb-tree-init");
    $(document).on("wb-tree-init", function(e, tree) {
        $(tree).treeEvents();
        $(tree).treeStore();

    });

    $(document).off("tree_before_formsave");
    $(document).on("tree_before_formsave", function(event, formname, itemname) {
        var form = $(event.delegateTarget).find("form")[0];
        $(form).find("[data-wb-role=tree]").each(function(i, tree) {
            $(this).treeStore();
        });
    });

	$.fn.wbSource = function(options={}) {
		var $source = $(this);
		var $editor;
		var $toolbar;
		if ($source.is(".wb-plugin")) return;
		if ($source.attr("id")==undefined || $source.attr("id")=="") {$source.attr("id",wb_newid());}
		var id = $source.attr("id");
		$source.hide().addClass("wb-plugin");
		wbapp.postWait("/module/filemanager/getfile/",{file:"/modules/editarea/editarea_ui.php"},function(data){
			$editor = $("<div>"+data+"</div>");
			$editor = $editor.find(".mod_editarea");
		});
		var theme = "chrome";
		var height = $source.height();
		var mode = "php";
		var value = $source.text();
		var fsize = $source.data("modEditAreaFsize");
		if (fsize == undefined || fsize == "") {
			fsize = 12;
			$(document).data(id,fsize);
		}
		$editor.attr("id","ed_"+id);
		$editor.find("textarea").attr("id","source_"+id);
		$source.after($editor);
		var editor = ace.edit($editor.find("textarea").attr("id"));
		if (height==0) height=300;
		if ($source.attr("data-height")!==undefined) {height = $editor.attr("data-height");}
		if ($source.attr("data-theme")!==undefined) {theme = $editor.attr("data-theme");}
		if ($source.attr("data-mode")!==undefined) {mode = $editor.attr("data-mode");}
		if (options.height !==undefined) {height=options.height}
		if (options.theme !==undefined) {height=options.theme}
		if (options.mode !==undefined) {mode=options.mode}
		$(editor.container).css("height", height).css("margin", 0);
		editor.setTheme("ace/theme/"+theme);
		editor.setOptions({
			enableBasicAutocompletion: true,
			enableSnippets: true
		});
		editor.getSession().setUseWrapMode(true);
		editor.getSession().setUseSoftTabs(true);
		editor.getSession().getUndoManager().markClean()
		editor.setDisplayIndentGuides(true);
		editor.setHighlightActiveLine(false);
		editor.setAutoScrollEditorIntoView(true);
		editor.getSession().setMode("ace/mode/"+mode);
		editor.getSession().setValue(value);
		editor.resize(true);
		editor.gotoLine(0, 0);

		editor.getSession().on("change", function() {
			update();
		});

		function update() {
			var value = editor.getSession().getValue();
			$source.text(value);
			$source.val(value);
		}

		function toolbar() {
			$toolbar = $("#ed_"+id+" .mod_editarea_toolbar");
			$("#ed_"+id+" .mod_editarea_toolbar button").on("click",function(){

			if ($(this).hasClass("btnCopy")) {
			$(document).data("modEditAreaClipboard", editor.getCopyText());
			}
			if ($(this).hasClass("btnPaste")) {
			editor.insert($(document).data("modEditAreaClipboard"));
			}
			if ($(this).hasClass("btnUndo")) {
			editor.execCommand("undo");
			}
			if ($(this).hasClass("btnRedo")) {
			editor.execCommand("redo");
			}
			if ($(this).hasClass("btnFind")) {
			editor.execCommand("find");
			}
			if ($(this).hasClass("btnReplace")) {
			editor.execCommand("replace");
			}
			if ($(this).hasClass("btnLight")) {
			editor.setTheme("ace/theme/chrome");
			setcookie("sourceEditorTheme", "ace/theme/chrome");
			}
			if ($(this).hasClass("btnDark")) {
			editor.setTheme("ace/theme/monokai");
			setcookie("sourceEditorTheme", "ace/theme/monokai");
			}
			if ($(this).hasClass("btnClose")) {
			editor.setValue("");
			$(document).data("sourceFile", null);
			$("#sourceEditorToolbar .btnSave").removeClass("btn-danger");
			}
			if ($(this).hasClass("btnFontDn")) {
			if (fsize > 8) {
			fsize = fsize * 1 - 1;
			}
			editor.setFontSize(fsize);
			$source.data("modEditAreaFsize",fsize);
			}
			if ($(this).hasClass("btnFontUp")) {
			if (fsize < 20) {
			fsize = fsize * 1 + 1;
			}
			editor.setFontSize(fsize);
			$source.data("modEditAreaFsize",fsize);
			}
			if ($(this).hasClass("btnFullScr")) {
			var div = $(this).parents(toolbar).parent();
			div.parents(".modal").toggleClass("fullscr");
			if (div.parents(".modal").hasClass("fullscr")) {
			var offset = div.find("pre.ace_editor").offset();
			div.find("pre.ace_editor").height($(window).height() - offset.top - 15);
			} else {
			div.find("pre.ace_editor").height(400);
			}
			editor.resize();
			document.getElementById(id).requestFullScreen();
			window.dispatchEvent(new Event('resize'));
			}
			if ($(this).hasClass("btnSave")) {
			$.post("/module/filemanager/putfile/", {
			file: file,
			text: editor.getValue()
			}, function(data) {
			if ($.bootstrapGrowl) {
			$.bootstrapGrowl(locale.saved, {
			ele: 'body',
			type: 'success',
			offset: {
			from: 'top',
			amount: 20
			},
			align: 'right',
			width: "auto",
			delay: 4000,
			allow_dismiss: true,
			stackup_spacing: 10
			});
			}
			});
			}


			return false;
			});
		}
		toolbar();
		return editor;
	}


    $.fn.treeStore = function() {
        var name = $(this).attr("name");
        var store = $(this).find("input[name='" + name + "']");
        $(store).val(wb_json_encode($(this).treeBranchStore()));
        $(".modal:visible").wbFixModal();
    };

    $.fn.treeBranchStore = function() {
        var store = [];
        $(this).children("ol").each(function() {
            $(this).children("li").each(function() {
                if ($(this).hasClass("dd-collapsed")) {
                    $(this).attr("data-open", false);
                } else {
                    $(this).attr("data-open", true);
                }
                var branch = {
id:
                    $(this).attr("data-id"),
name:
                    $(this).attr("data-name"),
data:
                    $(this).treeBranchData(),
children:
                    $(this).treeBranchStore(),
open:
                    $(this).attr("data-open")
                }
                if (branch.children.length == 0) {
                    branch.children = false;
                }
                if (branch.open == "true") {
                    branch.open = true;
                } else {
                    branch.open = false;
                }
                store.push(branch);
            });

        });
        return store;
    }

    $.fn.treeBranchData = function() {
        return wb_json_decode($(this).children("input[data]").attr("data"));
    }

    $.fn.treeBranchChange = function(edid) {
        // edid - id редактора
        if ($(edid).find("form:first").length) {
            var dict = $(this).treeDict();
            var that = $(this).find(".wb-tree-item.wb-tree-current");
            var data = $(edid).find("form:first").serializeArray();
            $(data).each(function(i, d) {
                $(that).attr(d.name, d.value);
                if (d.name == "data-name") {
                    $(that).children(".dd-content").val(d.value);
                    $(that).children("input").val(d.value);
                }
                if (d.name == "data-id") {
                    $(that).children(".dd3-btn").children("span").html(d.value);
                }
            });
            var data = wb_tree_json_prep(wb_json_encode($(edid).find(".treeData > form").serializeArray()), dict);
            $(that).children("input").attr("data", wb_json_encode(data));
        }
    };

    $.fn.treeDict = function() {
        return wb_json_decode($(this).children("[data-name=dict]").val());
    }

    $.fn.treeEvents = function() {
        var tree = this;
        var row = $(document).data("wb-tree-row");
        var form = $(this).parents("[data-wb-form]").attr("data-wb-form");
	row = wb_setdata(row, {
	"name": "",
	"form": form,
	"id": wb_newid()
	}, true);
        // collapse
        $(this).undelegate(".wb-tree-item button[data-action]", "click");
        $(this).delegate(".wb-tree-item button[data-action]", "click", function() {
            //setTimeout(function () {$(tree).treeStore();}, 200);
            $(tree).treeStore();
        });

        // contextmenu
        $(this).undelegate(".wb-tree-item", "contextmenu");
        $(this).delegate(".wb-tree-item", "contextmenu", function(e) {
            $(".wb-tree-menu").remove();
            $(e.target).parent(".wb-tree-item").append($(document).data("wb-tree-rowmenu"));
            var w = e;
            var relativeX = (w.clientX - 10);
            var relativeY = (w.clientY - 10);
            if ($(e.target).parents(".modal-content").children(".modal-header").length) {
                relativeY -= $(e.target).parents(".modal-content").children(".modal-header").height();
            }
            if ($(e.target).parents("#adminTree.tab-pane").length) {
                relativeY -= ($(".kt-pagetitle").offset().top + $(".kt-pagetitle").height());
            }
            $(".wb-tree-item").find(".wb-tree-menu").css("left", relativeX + "px").css("top", relativeY + "px");
            $(".wb-tree-item").find(".wb-tree-menu [data-toggle=dropdown]").trigger("click");
            e.preventDefault();
        });


        $(this).undelegate(".wb-tree-add", "click");
        $(this).delegate(".wb-tree-add", "click", function(e) {
            $(this).parent(".wb-tree-item").after(row);
            $(tree).treeStore();
        });

        $(this).undelegate(".wb-tree-del", "click");
        $(this).delegate(".wb-tree-del", "click", function(e) {
            $(this).parent(".wb-tree-item").remove();
            if (!$(tree).find(".wb-tree-item").length) {
                $(tree).children(".dd-list").append(row);
            }
            $(tree).treeStore();
        });

        // contextmenu Click
        $(this).undelegate(".wb-tree-menu .dropdown-item", "click");
        $(this).delegate(".wb-tree-menu .dropdown-item", "click", function(e) {
            var name = $(tree).attr("name");
            if ($(this).attr("href") == "#edit") {
                $(this).parents(".wb-tree-menu").parent(".wb-tree-item").children(".dd3-btn").trigger("click");
            }
            if ($(this).attr("href") == "#after") {
                $(this).parents(".wb-tree-menu").parent(".wb-tree-item").after(row);
            }
            if ($(this).attr("href") == "#before") {
                $(this).parents(".wb-tree-menu").parent(".wb-tree-item").before(row);
            }
            if ($(this).attr("href") == "#clone") {
                var copy = $(this).parents(".wb-tree-menu").parent(".wb-tree-item").clone();
                var newid = wb_newid();
                copy.attr("data-id", newid);
                copy.attr("title", newid);
                copy.find(".dd3-btn > span").html(newid);
                $(this).parents(".wb-tree-menu").parent(".wb-tree-item").after(copy);
            }
            if ($(this).attr("href") == "#remove") {
                $(this).parents(".wb-tree-menu").parent(".wb-tree-item").remove();
            }
            if (!$(tree).find(".wb-tree-item").length) {
                $(tree).children(".dd-list").append(row);
            }
            $(document).find(".wb-tree-menu.show").remove();
            $(tree).treeStore();
        });

        // item button click
        $(this).undelegate(".wb-tree-item .dd3-btn", "click");
        $(this).delegate(".wb-tree-item .dd3-btn", "click", function(e) {
            $(this).parent(".wb-tree-item").children(".dd-content").treeContentEdit();
        });

        // item line click
        $(this).undelegate(".wb-tree-item > .dd-content", "dblclick");
        $(this).delegate(".wb-tree-item > .dd-content", "dblclick", function(e) {
            $(this).parent(".wb-tree-item").children(".dd-content").treeContentEdit();
        });

        // tree change
        $(this).on('change', function(e) {
            $(tree).treeStore();
        });
    }

    $.fn.treeEditModal = function(data) {
        var res = false;
        wbapp.postWait("/ajax/treeedit/",data,function(data) {
            res = data;
        });
        return res;
    }

    $.fn.treeContentEdit = function() {
        if ($(this).is("[data-wb-role=tree]")) {
            var tree = this;
        } else {
            var tree = $(this).parents("[data-wb-role=tree]");
        }
        var dict = $(tree).treeDict();
        var that = $(this).parent(".wb-tree-item");
        var item = $(that).attr("data-id");
        var form = $(tree).parents("[data-wb-form]").attr("data-wb-form");
        var formitem = $(tree).parents("[data-wb-form]").attr("data-wb-item");
        $(tree).find(".wb-tree-item").removeClass("wb-tree-current");
        $(that).addClass("wb-tree-current");
        if (form == undefined) {
            form = "tree";
        }
        var text = $(this).val();
        var name = $(tree).attr("name");
        var edid = "#tree_" + form + "_" + name;
        if ($(edid).length) {
            $(edid).remove();
        }

        var data = {};
        data["data"] = $($(this).parents(".wb-tree-item")).treeBranchData();
        data["fields"] = $(tree).treeDict();
        data["form"] = data["_form"] = data["data"]["_form"] = form;
        data["item"] = data["_item"] = data["data"]["_item"] = formitem;
        data["name"] = name;
        data["_id"] = data["data"]["_id"] = formitem;
        data["data-id"] = item;
        data["data-name"] = text;

        var tpl = $(tree).treeEditModal(data);
        $(tpl).find(".wb-uploader").attr("data-wb-path", "/uploads/" + form + "/" + formitem);
        if ($("#tree_edit .tree-edit").length && $("#tree_edit .tree-edit").is(":visible")) {
            edid = "#tree_edit .tree-edit";
            $(edid + " > div").html($(tpl).find(".modal-body").html());
            $("#tree_edit .modal-header").html($(tpl).find(".modal-header").html());
        } else {
            $("body").append(tpl);
            $(edid).modal();
            $(document).click(function(e) {
                if ($(e.target).parents(".dd-btn").length ) {
                    $(tree).treeBranchChange(edid);
                    $(edid).modal("hide");
                }
            });

        }
        $(edid).data("tree", tree);
        $(edid).data("that", that);
        $(edid).data("dict", dict);
        $(edid).data("data", data);
        $(edid).treeContentEditEvents();
	if (is_callable("wb_multiinput")) {wb_multiinput();}
        wb_plugins();
    };

    $.fn.treeContentEditEvents = function() {
        var tree = $(this).data("tree");
        var edid = this;

        $(edid).find("form").off("change");
        $(edid).find("form").on("change", function() {
            $(tree).treeBranchChange(edid);
            $(tree).treeStore();
        });

        $(edid).find('.modal-footer button').off("click");
        $(edid).find('.modal-footer button').on("click", function(e) {
            $(tree).treeStore();
            $(edid).modal("hide");
            setTimeout(function() {
                $(edid).remove();
            }, 500)
        });

        $(edid).off("multiinput");
        $(edid).on("multiinput", function(e, multi, trigger) {
            if ($(multi).attr("name") == "fields") {
                console.log("multiinput");
                var tree = $(edid).data("tree");
                $(edid).treeDictChange();
            }
        });

        $(edid).undelegate(".wb-tree-dict-prop-btn", "click");
        $(edid).delegate(".wb-tree-dict-prop-btn", "click", function(e) {
		var modal = wbapp.modal("#treeEditDictProp");
		var type = $(this).parents(".wb-multiinput").find("[data-wb-field=type]").val();
		var name = $(this).parents(".wb-multiinput").find("input[data-wb-field=name]").val();
		var field = $(this).parents(".wb-multiinput").find("input[data-wb-field=prop]");
		$("#treeEditDictProp .modal-body").html($(edid).find("script.wb-prop-fields").html());
		$("#treeEditDictProp").find("[data-type-allow],[data-type-disallow]").each(function(){
		if ( $(this).attr("data-type-allow") !== undefined ) {
			var allow = explode(" ",trim(str_replace(";"," ",str_replace(","," ",$(this).attr("data-type-allow")))));
			if (!in_array(type,allow)) {$(this).remove();}
		} else if ( $(this).attr("data-type-disallow") !== undefined ) {
			var disallow = explode(" ",trim(str_replace(";"," ",str_replace(","," ",$(this).attr("data-type-disallow")))));
			if (in_array(type,disallow)) {$(this).remove();}
		}
		});
		var data = {};
		if ($(field).attr("value")>"") {data=$.parseJSON($(field).attr("value"));}
		wb_setdata($("#treeEditDictProp form"), data );
		wb_delegates();
		$("#treeEditDictProp").find(".modal-title").html(name+"&nbsp;");
		$("#treeEditDictProp").data("field",field);
		$("#treeEditDictProp").modal('show');
        });

        $(document).undelegate("#treeEditDictProp",'hide.bs.modal');
        $(document).delegate("#treeEditDictProp",'hide.bs.modal', function (e) {
		var field = $("#treeEditDictProp").data("field");
		var form = $("#treeEditDictProp").find("form").serializeJSON();
		$(field).attr("value",json_encode(form));
		$(edid).treeDictChange(e);
	});

        $(edid).undelegate(".wb-tree-dict-lang-btn", "click");
        $(edid).delegate(".wb-tree-dict-lang-btn", "click", function(e) {
		var modal = wbapp.modal("#treeEditDictProp");
		var name = $(this).parents(".wb-multiinput").find("input[data-wb-field=name]").val();
		var field = $(this).parents(".wb-multiinput").find("input[data-wb-field=lang]");
		$("#treeEditDictProp .modal-body").html($(edid).find("script.wb-prop-lang").html());
		var data = {};
		if ($(field).attr("value")>"") {data=$.parseJSON($(field).attr("value"));}
		wb_setdata($("#treeEditDictProp form"), data );
		wb_delegates();
		$("#treeEditDictProp").find(".modal-title").html(name+"&nbsp;");
		$("#treeEditDictProp").data("field",field);
		$("#treeEditDictProp").modal('show');
        });

        $(document).undelegate("#treeEditDictProp",'hide.bs.modal');
        $(document).delegate("#treeEditDictProp",'hide.bs.modal', function (e) {
		var field = $("#treeEditDictProp").data("field");
		var form = $("#treeEditDictProp").find("form").serializeJSON();
		$(field).attr("value",json_encode(form));
		$(edid).treeDictChange(e);
	});

        $(edid).undelegate(".treeDict *", "change");
        $(edid).delegate(".treeDict *", "change", function(e) {
            if ($(e.currentTarget).is("input,select,textarea")) {
                $(edid).treeDictChange($(this));
            }
        });

    }

    $.fn.treeDictChange = function(event) {
        if (event == undefined) return;
        console.log("treeDictChange");
        var dict = [];
        var tree = $(this).data("tree");

        var form = $(tree).parents("[data-wb-form]").attr("data-wb-form");
        var formitem = $(tree).parents("[data-wb-form]").attr("data-wb-item");

        $(this).find(".treeDict .wb-multiinput").each(function(i) {
            var fld = {};
            $(this).find(":input").each(function() {
                if ($(this).is("input")) {
                    fld[$(this).attr("data-wb-field")] = $(this).val();
                } else if ($(this).is("textarea")) {
                    fld[$(this).attr("data-wb-field")] = $(this).html();
                } else if ($(this).is("select")) {
                    fld[$(this).attr("data-wb-field")] = $(this).find("option:selected").attr("value");
                }
            });
            dict.push(fld);
        });

        $(this).data("dict", dict);
        $(tree).children("[data-name=dict]").val(wb_json_encode(dict));
        $(this).data("data")["data"] = wb_json_decode($(this).data("that").children("input").attr("data"));
        $(this).data("data")["fields"] = $(tree).treeDict();
        $(this).data("data")["_form"] = form;
        $(this).data("data")["_item"] = formitem;
        var tpl = $(tree).treeEditModal($(this).data("data"));
        if (event !== undefined ) $(event).treeDictUpdateProp(tpl);
        $(tpl).find("#treeDict_tree_tree").remove();
        $(this).find(".treeData").children("form").html($(tpl).find(".treeData form").html());
        $(this).treeContentEditEvents();
        wb_plugins();
        wb_multiinput();
    }

    $.fn.treeDictUpdateProp = function(tpl) {
        if (!$(this).is("select[data-wb-field=type]")) return;
        var field=$(this).parents(".wb-multiinput").find("[data-wb-field=name]").val();
        var prop = $(tpl).find("[data-wb-field=name][value='"+field+"']").parents(".wb-multiinput").find(".wb-prop-fields").html();
        $(this).parents(".wb-multiinput").find(".wb-prop-fields").html(prop);
    }

    function wb_tree_json_prep(data, dict) {
        var data = wb_json_decode(data);
        var values = {};
        $(data).each(function(j, d) {
            var fldname = d["name"];
            var fldval = d["value"];
            $(dict).each(function(z, di) {
                if (di["name"] == fldname) {
                    fldval = wb_iconv(fldval, di["type"]);
                    values[fldname] = fldval;
                }
                if (strpos(fldname, "[")) {
                    var pos = strpos(fldname, "[");
                    var sub = substr(fldname, pos);
                    var fldn = "values['" + substr(fldname, 0, pos) + "']";

                    var myRe = /\[(.*?)\]/g;
                    var cur = 1;
                    var myArray = [];
                    eval("if (" + fldn + "==undefined) {" + fldn + "={};};");
                    while ((myArray = myRe.exec(sub)) != null) {
                        eval("fldn+=\"['" + myArray[1] + "']\";");
                        if (cur < myArray.length) {
                            eval("if (" + fldn + "==undefined) {" + fldn + "={};};");
                        } else {
                            eval(fldn + "=fldval;");
                        }
                        cur++;
                    }
                }
            });
        });
        return values;
    }

}

function wb_ajax_loader() {
    if (is_callable("ajax_loader")) {
        ajax_loader();
    } else {
        $("body").addClass("cursor-wait");
        if ($(".kt-loading").length) $(".kt-loading").css({"opacity":"1","z-index":"999999","background-color":"rgba(255, 255, 255, 0.5)"});
    }
}

function wb_ajax_loader_done() {
    if (is_callable("ajax_loader_done")) {
        ajax_loader_done();
    } else {
        if ($(".kt-loading").length) $(".kt-loading").css({"opacity":"0","z-index":"-1"});
		$("body").removeClass("cursor-wait");
    }
}

JSON.safeParse = function(input, def) {
    // Convert null to empty object
    if (!input) {
        return def || {};
    } else if (Object.prototype.toString.call(input) === '[object Object]') {
        return input;
    }
    try {
        return JSON.parse(input);
    } catch (e) {
        return def || {};
    }
};



function wb_newid(separator, prefix) {
    if (separator == undefined) {
        separator = "";
    }
    var mt = explode(" ", microtime());
    var md = substr(str_repeat("0", 2) + dechex(ceil(mt[0] * 10000)), -4);
    var id = dechex(time() + rand(100, 999));
    if (prefix !== undefined && prefix > "") {
        id = prefix + separator + id + md;
    } else {
        id = id + separator + md;
    }
    return id;

}

function wb_base_fix() {
    if ($("base").length) {
        var base = $("base").attr("href");
        $(document).undelegate("a", "click");
        $(document).delegate("a", "click", function(e) {
            if (!$(this).is("[data-toggle]")) {
                var hash = $(this).attr("href");
                var role = $(this).attr("role");
                if (hash !== undefined && role == undefined && hash.substr(0, 1) == "#") {
                    var loc = explode("#", window.location.href);
                    var loc = loc[0];
                    loc.replace(base, "");
                    document.location = loc + hash;
                    e.preventDefault();
                }
            }
        });
    }
}

function wb_plugins() {
    $(document).ready(function() {
        if (wb_plugins_loaded()) {
            autosize($('textarea[rows=auto]'));
            if ($('[data-toggle="tooltip"]').length) {
                $('[data-toggle="tooltip"]').tooltip();
            }
            if ($('[data-toggle="popover"]').length) {
                $('[data-toggle="popover"]').each(function() {
                    if ($(this).attr("data-html") !== undefined) {
                        var content = $($(this).attr("data-html")).html();
                        $(this).popover({
				html: true,
				content: content
                        });
                    } else {
                        $(this).popover();
                    }
                });
                $('[data-toggle="popover"]').popover();
            }
        }
        if ($("script[src*=fancybox]").length && $("a[href$='.jpeg'],a[href$='.jpg'],a[href$='.png'],a[href$='.gif']").length) {
            $("a[href$='.jpeg'],a[href$='.jpg'],a[href$='.png'],a[href$='.gif']").each(function() {
                if ($(this).attr("data-fancybox") == undefined) {
                    if ($(this).parents("[data-wb-tpl]").length) {
                        if ($(this).parents("[data-wb-tpl]").attr("data-wb-tpl") !== "false") {
                            $(this).attr("data-fancybox", $(this).parents("[data-wb-tpl]").attr("data-wb-tpl"));
                        }
                    } else {
                        $(this).attr("data-fancybox", "gallery");
                    }
                }
                if ($(this).parents(".modal").length) {
                    $(this).attr("data-fancybox", $(this).parents(".modal").attr("id") + "-" + $(this).attr("data-fancybox"));

                }
            });
        }
        if ($("script[src*=datetimepicker]").length) {
            $("[type=datepicker]:not(.wb-plugin)").each(function() {
                if ($(this).attr("data-date-format") == undefined) {
                    $(this).attr("data-date-format", "dd.mm.yyyy"); // Plugin Format
                }
                if ($(this).val() > "") {
                    $(this).val(wb_oconv_object(this));
                }
                $(this).addClass("wb-plugin");
                var lang = wbapp.settings.js_locale;
                if ($(this).attr("data-wb-lang") !== undefined) {
                    lang = $(this).attr("data-wb-lang");
                }
                $(this).datetimepicker({
language: lang,
autoclose: true,
todayBtn: true,
                    minView: 2
                }).on('changeDate', function(ev) {
                    $(this).attr("value", wb_iconv_object(this));
                });
            });
            $("[type=datetimepicker]:not(.wb-plugin)").each(function() {
                if ($(this).attr("data-date-format") == undefined) {
                    $(this).attr("data-date-format", "dd.mm.yyyy hh:ii"); // Plugin Format
                }
                if ($(this).val() > "") {
                    $(this).val(wb_oconv_object(this));
                }
                $(this).addClass("wb-plugin");
                var lang = wbapp.settings.js_locale;
                if ($(this).attr("data-wb-lang") !== undefined) {
                    lang = $(this).attr("data-wb-lang");
                }
                $(this).datetimepicker({
language: lang,
autoclose: true,
todayBtn: true
                }).on('changeDate', function(ev) {
                    $(this).attr("value", wb_iconv_object(this));
                });
            });
            $("[type=timepicker]:not(.wb-plugin)").each(function() {
                if ($(this).attr("data-date-format") == undefined) {
                    $(this).attr("data-date-format", "hh:ii"); // Plugin Format
                } else {
                    $(this).attr("data-date-format", "hh:ii");
                }
                if ($(this).val() > "") {
                    $(this).val(wb_oconv_object(this));
                }
                $(this).addClass("wb-plugin");
                var lang = wbapp.settings.js_locale;
                if ($(this).attr("data-wb-lang") !== undefined) {
                    lang = $(this).attr("data-wb-lang");
                }
                $(this).datetimepicker({
language: lang,
                    startView: 1,
autoclose: true,
todayBtn: true
                }).on('changeDate', function(ev) {
                    $(this).attr("value", wb_iconv_object(this));
                });
            });
        }
        if ($(".dd[data-wb-role=tree]").length) {
            $(".dd[data-wb-role=tree]").each(function(e) {
                $(this).nestable({
                    maxDepth: 100
                });
                $(".dd-item").unbind("contextmenu");
                $(this).trigger("wb-tree-init", this);
            });
        }
        if (wb_plugins_loaded() && $('.select2:not(.wb-plugin)').length) {
            $('.select2:not(.wb-plugin)').each(function() {
                var that = this;
                if ($(this).is("[data-wb-ajax]")) {
                    var url = $(this).attr("data-wb-ajax");
                    var tpl = $(this).html();
                    if ($(this).attr("data-wb-tpl")!==undefined) tpl = $("#" + $(this).attr("data-wb-tpl")).html();
                    var where = $(this).attr("data-wb-where");
                    var val = $(this).attr("value");
                    var plh = $(this).attr("placeholder");
                    var min = $(this).attr("min");
                    if (min == undefined) min = 1;
                    if (plh == undefined) plh = wbapp.sysmsg.search;
                    $(this).select2({
					language: wbapp.settings.js_locale,
					placeholder: plh,
					minimumInputLength: min,
					ajax: {
							url: url,
							async: true,
							method: "post",
							dataType: 'json',
							data: function(term, page) {
									return {
										value:	term.term,
										page:	page,
										where:	where,
										tpl:	tpl
									};
								},
							processResults:
								function(data) {
									$(that).data("wb-ajax-data", data);
									$(that).trigger("wb_ajax_done", [that, url, data]);
									$(that).data("item", data);
									return {
							results:
										data
									};
								},
					},
					});
                    wbapp.postWait(url, {id:val,tpl:tpl},function(data) {
                        var option = new Option(data.text, data.id, true, true);
                        $(that).append(option).trigger('change');
                        $(document).data("item", data.item);
                        $(that).trigger({
								type: 'select2:select',
								params: {
								data: data
                            }
                        });
                    });

                    $(that).off("change");
                    $(that).on("change", function() {
                        if ($(that).val() > "") {
                            $($(that).data("item")).each(function(i, item) {
                                if (item.id == $(that).val()) {
                                    $(that).data("item", item.item);
                                    return;
                                }
                            });
                        }
                    });
                } else {
                    $(this).select2();
                }
            });
            $('.select2').addClass("wb-plugin");
        }


        if (wb_plugins_loaded() && $('.input-tags').length) {
            $('.input-tags').each(function() {
                if ($(this).attr("placeholder") !== undefined) {
                    var ph = $(this).attr("placeholder");
                } else {
                    var ph = 'new';
                }
                if ($(this).attr("height") !== undefined) {
                    var h = $(this).attr("height");
                } else {
                    var h = 'auto';
                }
                if ($(this).attr("width") !== undefined) {
                    var w = $(this).attr("width");
                } else {
                    var w = 'auto';
                }
                $(this).tagsInput({
width: w,
height: h,
'defaultText': ph
                });
            });
        }
        if (wb_plugins_loaded() && $('.rating').length) {
            $('.rating:not(.wb-plugin)').each(function() {
                $(this).addClass("wb-plugin");
                $(this).rating({
filled: 'fa fa-star',
empty: 'fa fa-star-o'
                });
            });
        }
        if (wb_plugins_loaded()) {
            if ($("input[type=phone]").length) {
                $("input[type=phone]").mask("+9 (999) 999-99-99");
            }
            if ($("input[type=tel]").length) {
                $("input[type=tel]").mask("+9 (999) 999-99-99");
            }
            if ($("input[data-wb-mask]").length) {
                $("input[data-wb-mask]").each(function() {
                    $(this).attr("type", "text");
                    $(this).mask($(this).attr("data-wb-mask"));
                });
            }
        }
        if (is_callable("wb_plugin_editor")) wb_plugin_editor();
        if (is_callable("wbCommonUploader")) wbCommonUploader();

        $(document).undelegate('.modal','hidden.bs.modal');
        $(document).delegate('.modal','hidden.bs.modal', function (e) {
            var zi=1000;
            $(".modal:visible").each(function() {
                zi += 10;
            });
            $(".modal-backdrop").css("z-index",zi-10);
		$("tester").remove();
        });

        $(document).undelegate('.modal','shown.bs.modal');
        $(document).delegate('.modal','shown.bs.modal', function (e) {
            var zi=1000;
            $(".modal:visible").each(function() {
                zi += 10;
            });
            $(this).css("z-index",zi+10);
            $(".modal-backdrop").css("z-index",zi);
            if ($(this).is("[data-backdrop=false]")) {
                $(".modal-backdrop").css("z-index",zi-10);
            }
		$("tester").remove();
        });

	// modal fixer
	$('.modal').on('shown.bs.modal', function () {
		$(this).wbFixModal();
	})
	if (wb_plugins_loaded()) {
		$(".content-box, .modal-body").perfectScrollbar();
		$("#treeEditForm").find(".tree-view,.tree-edit > div").perfectScrollbar();
	}
	if ($("textarea.source:not(.wb-plugin)").length){
		wbapp.scriptWait("/engine/js/ace/ace.js");
		wbapp.scriptWait("/engine/js/ace/theme-chrome.js");
		wbapp.scriptWait("/engine/js/ace/mode-php.js");
		wbapp.scriptWait("/engine/modules/editarea/editarea.js");
		$("textarea.source:not(.wb-plugin)").wbSource();
	};



	$(window).on('resize',function(){
		$(".modal:visible").wbFixModal();
	});
    });
}

$.fn.wbFixModal = function () {
   	$(this).find(".modal-dialog").css( 'height' ,'').css("margin","");
	$(this).find(".modal-content").css( 'height' ,'');
	$(this).find(".modal-body").css( 'height' ,'');

    var mh = $(this).height();
    var wh = $(window).height();
    var mhd = $(this).find(".modal-header").height();
    var mft = $(this).find(".modal-footer").height();
    var mbd = $(this).find(".modal-body").height();
    if ( (mhd + mft + mbd) > wh ) {
	$(this).find(".modal-dialog").height(wh).css({"overflow":"hidden","margin":"0"});
	$(this).find(".modal-content").height(wh).css("overflow","hidden");;
	$(this).find(".modal-body").height( wh - (mhd + mft) ).css("overflow","auto");
	//$(this).height(wh).css("overflow","hidden");
    }
};

function wb_plugins_loaded() {
    return $("script[src='/engine/js/plugins/plugins.js']").length;
}

function wb_plugin_editor() {
    $(document).data("wb_editor_change",false);
    if ($("textarea.editor:not(.wb-plugin)").length) {
        $("textarea.editor:not(.wb-plugin)").each(function() {
            $(this).addClass("wb-plugin");
            var fldname = $(this).attr("name");
            if ($(this).attr("id") == undefined || $(this).attr("id") == "") {
                $(this).attr("id", wb_newid());
            }
            var editor = $(this).ckeditor();
            CKEDITOR.config.extraPlugins = 'youtube';
            CKEDITOR.config.skin = 'office2013';
            CKEDITOR.config.allowedContent = true;
            CKEDITOR.config.forceEnterMode = true;
            CKEDITOR.config.language = wbapp.settings.js_locale;;
            CKEDITOR.plugins.registered['save'] = {
init:
                function(editor) {
                    var command = editor.addCommand('save', {
modes: {
                            wysiwyg: 1,
                            source: 1
                        },
exec: function(editor) {
                            var fo = editor.element.$.form;
                            editor.updateElement();
                            wb_formsave($(fo));
                        }
                    });
                    editor.ui.addButton('Save', {
label: wbapp.sysmsg.save,
command: 'save'
                    });
                }
            }
        });
        for (var i in CKEDITOR.instances) {
            // это работает
            CKEDITOR.instances[i].on('change', function(e) {
                if ($(document).data("wb_source_change")!==true) {
                    CKEDITOR.instances[i].updateElement();
                    var instance = CKEDITOR.instances[i].name;
                    var form = $(document).find("textarea#" + instance).parents("form[data-wb-form]").attr("data-wb-form");
                    var item = $(document).find("textarea#" + instance).parents("form[data-wb-item]").attr("data-wb-item");
                    var fldname = $(document).find("textarea#" + instance).attr("name");
                    var value = CKEDITOR.instances[i].getData();
                    $(document).find("textarea#" + instance).trigger("change");

                    $(document).data("wb_editor_change",true);
                    $(document).trigger("wb_editor_change", {
"form": form,
"item": item,
"field": fldname,
"value": value
                    });
                }
            });
        }
    }
}

$(document).on("wb_source_change", function(e, data) {
    if ($(document).data("wb_source_change")==true) {
        if (CKEDITOR.instances[$("form[data-wb-form='"+data.form+"'] [name='" + data.field + "']").attr("id")] !== undefined) {
            CKEDITOR.instances[$("form[data-wb-form='"+data.form+"'] [name='" + data.field + "']").attr("id")].setData(data.value);
        }
        $(document).data("wb_source_change",false);
        return false;
    }
});

/*
$(document).on("", function(e, data) {
  $(document).data("sourceChange", true);
  var form = data.form;
  var field = data.field;
  var value = data.value;
  if (CKEDITOR.instances[$("[name='" + field + "']").attr("id")] !== undefined) {
    CKEDITOR.instances[$("[name='" + field + "']").attr("id")].setData(value);
  }
  if ($("[name='" + field + "']:not('.ace_editor')").is("textarea")) {
    $("[name='" + field + "']:not('.ace_editor')").html(value);
  } else {
    $("[name='" + field + "']:not('.ace_editor')").val(value);
  }
  $("[name='" + field + "']:not('.ace_editor')").trigger("change");
  setTimeout(function() {
    $(document).data("sourceChange", false);
  }, 250);
  e.preventDefault();
  return false;
});
*/

function wb_formsave() {
    // <button data-formsave="#formId" data-src="/path/ajax.php"></button>
    // data-formsave	-	JQ идентификатор сохраняемой формы
    // data-form		-	переопределяет имя формы, по-умолчанию берётся аттрибут name тэга form
    // data-src			-	путь к кастомному ajax обработчику (необязательно)
    $(document).undelegate(".modal-dialog:visible input, .modal-dialog:visible select, .modal-dialog:visible textarea", "change");
    $(document).delegate(".modal-dialog:visible input, .modal-dialog:visible select, .modal-dialog:visible textarea", "change", function() {
        $(".modal-dialog:visible").find("[data-formsave] span.glyphicon").removeClass("glyphicon-ok").addClass("glyphicon-save");
    });
    $(document).undelegate("[data-wb-formsave]:not([data-wb-role=include])", "click");
    $(document).delegate("[data-wb-formsave]:not([data-wb-role=include])", "click", function() {
        var formObj = $($(this).attr("data-wb-formsave"));
        if ($(this).attr("data-wb-add") == "false") {
            $(formObj).attr("data-wb-add", "false");
        }
        $(this).find("span.fa").addClass("loader");
        var save = wb_formsave_obj(formObj);
        $(this).find("span.fa").removeClass("loader fa-save").addClass("fa-check");
        if (save) {
            return save;
        } else {
            return {
                error: 1
            };
        }
        return false;
    });

    $(document).undelegate("form[data-wb-form] [name=id]", "keyup input click");
    $(document).delegate("form[data-wb-form] [name=id]", "keyup input click", function() {
        $(this).val(wb_prepareId($(this).val()))

    });

    $(document).undelegate("form[data-wb-form] [name=id]", "change");
    $(document).delegate("form[data-wb-form] [name=id]", "change", function() {
        var value = $(this).val();
        var form = $(this).parents("form[data-wb-form]");
        var name = $(form).attr("data-wb-form");
        var path = "/uploads/" + name + "/" + value;
        if (!$(form).parents("script").length) {
            if ($(form).attr("data-wb-item") !== undefined) {
                $(form).attr("data-wb-item", value);
            }
            if ($(form).attr("data-wb-item") !== undefined) {
                $(form).attr("data-wb-item", value);
            }
            $(form).find(".wb-uploader").each(function() {
                $(this).attr("data-wb-path", path);
                $(this).attr("data-wb-item", value);
                $(this).wbUploaderInit();
            });
        }
    });
}


function wb_prepareId(id) {
    var tr = 'a b v g d e ["zh","j"] z i y k l m n o p r s t u f h c ch sh ["shh","shch"] ~ y ~ e yu ya ~ ["jo","e"]'.split(' ');
    var ww = '';
    id = id.toLowerCase();
    var i = 0;
    for (i = 0; i < id.length; ++i) {
        var cc = id.charCodeAt(i);
        var ch = (cc >= 1072 ? tr[cc - 1072] : id[i]);
        if (ch.length < 3) ww += ch;
        else ww += eval(ch)[0];
    }
    return ww
           .replace(/[^a-z\d\-\s_\s]/gi, '') // удаляем весь мусор, который нам нахрен не сдался
           .replace(/[\s\-]+/ig, '-') // Удаляем всё дубяжи и пробелы на "-"
           //.replace(/^[^a-z\d]+/i, '') // Удаляем всё лишнее в начала
           //.replace(/[^a-z\d]+$/i, '') // Удаляем всё лишнее с конца*/
}

function wb_iconv_object(obj) {
    var type = $(obj).attr("type");
    var value = $(obj).val();
    return wb_iconv(value, type);
}

function wb_iconv(value, type) {
        if (type == "date" || type == "datepicker") {
            var mask = "Y-m-d";
            value = date(mask, strtotime(value));
        }
        if (type == "datetime" || type == "datetimepicker") {
            var mask = "Y-m-d H:i";
            value = date(mask, strtotime(value));
        }
    return value;
}

function wb_oconv_object(obj) {
    var type = $(obj).attr("type");
    var value = $(obj).val();
    return wb_oconv(value, type, obj);
}

function wb_oconv(value, type, obj) {
    var mask = "";
    if (substr(type, 0, 4) == "date") {
        if (value == "") {
            value = date(new Date().toLocaleString());
        }
        if ($(obj).attr("data-date-format") !== undefined) {
            mask = $(obj).attr("data-date-format");
            mask = str_replace("yyyy", "Y", mask);
            mask = str_replace("hh", "H", mask);
            mask = str_replace("ii", "i", mask);
            mask = str_replace("ss", "s", mask);
            mask = str_replace("mm", "m", mask);
            mask = str_replace("dd", "d", mask);
            return date(mask, strtotime(value));
        } else {
            if (type == "date" || type == "datepicker") {
                mask = "Y-m-d";
            }
            if (type == "datetime" || type == "datetimepicker") {
                mask = "Y-m-d H:i";
            }
            return date(mask, strtotime(value));
        }
    }
    return value;
}

function wb_formsave_obj(formObj) {
    if (wb_check_required(formObj)) {
        var name = form = formObj.attr("data-wb-form");
        var item = formObj.attr("data-wb-item");
        var oldi = formObj.attr("data-wb-item-old");
        var item_id;
        $(document).trigger("wb_before_formsave", [name, item, form, true]);
        console.log("call: wb_before_formsave");
        $(document).trigger(name + "_before_formsave", [name, item, form, true]);
        console.log("call: " + name + "_before_formsave");
        var ptpl = formObj.attr("data-wb-parent");
        var padd = formObj.attr("data-wb-add");
        if (ptpl == undefined) {
            ptpl = $(document).find("[data-wb-add=true][data-wb-form="+form+"][data-wb-role=foreach]").attr("data-wb-tpl");
        }
        if (ptpl == undefined) {
            ptpl = $(document).find("[data-wb-add=true][data-wb-tpl]").attr("data-wb-tpl");
        }


        // обработка switch и checkbox
        var ui_switch = "";
        formObj.find("input[type=checkbox]:not(.bs-switch)").each(function() {
            var swname = $(this).attr("name");
            if ($(this).prop("checked") == true) {
                ui_switch += "&" + swname + "=on";
            } else {
                ui_switch += "&" + swname + "=";
            }
        });
        if (formObj.find("input[name=id]").length && formObj.find("input[name=id]").val() > "") {
            item_id = formObj.find("input[name=id]").val();
        } else {
            item_id = formObj.attr("data-wb-item");
            if (item_id == "_new") {
                item_id = wb_newid();
                formObj.find("input[name=id]").val(item_id)
            }
        }
        var ic_date = "";
        formObj.find("[name][type^=date]").each(function() {
            var dtname = $(this).attr("name");
            var type = $(this).attr("type");
            var val = wb_iconv_object(this);
            ic_date += "&" + dtname + "=" + val;
        });
        // прячем данные корзины перед сериализацией - нужно для orders_edit.php
        var cart = formObj.find("[data-wb-role=cart]");
        if (cart.length) {
            cart.find("input,select,textarea").each(function() {
                if ($(this).attr("disabled") != undefined) {
                    $(this).addClass("tmpDisabled");
                } else {
                    $(this).prop("disabled");
                }
            });
            var form = formObj.serialize();
            cart.find("input,select,textarea").each(function() {
                if (!$(this).hasClass("tmpDisabled")) {
                    $(this).removeAttr("disabled");
                }
            });
        } else {
            var form = formObj.serialize();
        }
        form += ui_switch + ic_date;
        if ($(this).attr("data-wb-form") !== undefined) {
            name = $(this).attr("data-wb-form");
        }
        if ($(this).attr("data-wb-src") !== undefined) {
            var src = $(this).attr("data-wb-src");
        } else {
            var src = "/ajax/save/" + name + "/" + item;
        }
        if (oldi !== undefined) {
            src += "&copy=" + oldi;
        }
        if ($(this).parents("#engine__setup").length) {
            var setup = true;
        } else {
            setup = false;
        }
        if (name !== undefined) {
            var data = {
mode: "save"
                ,
form:
                name
            };
            $.ajax({
type: 'POST',
url: src,
data: form,
success: function(data) {
                    var data=$.parseJSON(data);
                    var growl_text = wbapp.sysmsg.saved;
                    var growl_color = 'success';
                    if (data.error !== undefined && data.error == 1) {
                        if (data.text !== undefined && data.text > "") {
                            var growl_text = data.text;
                            var growl_color = 'danger';
                        }
                    }
                    if ($.bootstrapGrowl) {
                        $.bootstrapGrowl(growl_text, {
ele: 'body',
type: growl_color,
offset: {
from: 'top',
                                amount: 20
                            },
align: 'right',
width: "auto",
                            delay: 4000,
allow_dismiss: true,
                            stackup_spacing: 10
                        });
                    }
                    if (ptpl !== undefined && padd !== "false") {
                        var tpl = $(document).find("#" + ptpl).html();
                        var list = $(document).find("[data-wb-tpl=" + ptpl + "]");
                        var post = {
tpl:
                            tpl,
_form:
                            name,
_id:
                            item_id
                        };
                        var ret = false;
                        if (list.attr("data-wb-add") + "" !== "false") {
							list.find(".wb-empty").addClass("d-none");
                            ret = wb_setdata(tpl, post, true);
                            if (list.find("[idx=" + item_id + "]").length) {
                                list.find("[idx=" + item_id + "]").after(ret);
                                list.find("[idx=" + item_id + "]:first").remove();
                            } else {
                                list.prepend(ret);
                            }
                            list.find("[item=" + item_id + "]").each(function() {
                                if ($(this).attr("idx") == undefined) {
                                    $(this).attr("idx", $(this).attr("item"));
                                }
                            });
                        }
                    }
                    if (setup == true) {
                        document.location.href = "/login";
                    }
                    $(document).trigger(name + "_after_formsave", [name, item, form, true]);
                    console.log("call: " + name + "_after_formsave");
                    $(document).trigger("wb_after_formsave", [name, item, form, true]);
                    console.log("call: wb_after_formsave");
                    $(formObj).trigger("wb_form_saved");
                    return data;
                },
error: function(data) {
                    if (is_callable(name + "_after_formsave")) {
                        $(document).trigger(name + "_after_formsave", [name, item, form, false]);
                        console.log("call: " + name + "_after_formsave");
                    }
                    $(document).trigger("wb_after_formsave", [name, item, form, false]);
                    console.log("call: wb_after_formsave");
                    if ($.bootstrapGrowl) {
                        $.bootstrapGrowl(wbapp.sysmsg.save_failed+"!", {
ele: 'body',
type: 'danger',
offset: {
from: 'top',
                                amount: 20
                            },
align: 'right',
width: "auto",
                            delay: 4000,
allow_dismiss: true,
                            stackup_spacing: 10
                        });
                    }
                    return {
                        error: 1
                    };
                }
            });
        }
    } else {
        if ($.bootstrapGrowl) {
            $.bootstrapGrowl(wbapp.sysmsg.save_failed+"!", {
ele: 'body',
type: 'danger',
offset: {
from: 'top',
                    amount: 20
                },
align: 'right',
width: "auto",
                delay: 4000,
allow_dismiss: true,
                stackup_spacing: 10
            });
        }
	$(document).trigger("wb_form_invalid",formObj);
	return false;
    }
}

function wb_check_email(email) {
    if (email.match(/^([a-z0-9_-]+\.)*[a-z0-9_-]+@[a-z0-9_-]+(\.[a-z0-9_-]+)*\.[a-z]{2,6}$/i)) {
        return true;
    } else {
        return false;
    }
}

function wb_check_required(form) {
    var res = true;
    var idx = 0;
    $(form).find("[required],[type=password],[minlength]").each(function(i) {
        idx++;
		var label = $(this).attr("data-label");
		if (label == undefined || label == "") label = $(this).prev("label").text();
		if (label == undefined || label == "") label = $(this).next("label").text();
		if ((label == undefined || label == "") && $(this).attr("id") !== undefined) label = $(this).parents("form").find("label[for="+$(this).attr("id")+"]").text();
		if (label == undefined || label == "") label = $(this).parents(".form-group").find("label").text();
		if (label == undefined || label == "") label = $(this).attr("placeholder");
		if (label == undefined || label == "") label = $(this).attr("name");

        $(this).data("idx", idx);
        if ($(this).is(":not([disabled],[type=checkbox]):visible")) {
            if ($(this).val() == "") {
                res = false;
                console.log("trigger: wb_required_false ["+$(this).attr("name")+"]");
                $(document).trigger("wb_required_false", [this]);
            } else {
                if ($(this).attr("type") == "email" && !wb_check_email($(this).val())) {
                    res = false;
                    $(this).data("error", wbapp.sysmsg.email_correct);
                    console.log("trigger: wb_required_false ["+$(this).attr("name")+"]");
                    $(document).trigger("wb_required_false", [this]);
                } else {
					console.log("trigger: wb_required_true");
                    $(document).trigger("wb_required_true", [this]);
                }
            }
        }
        if ($(this).is("[type=checkbox]") && $(this).is(":not(:checked)")) {
            res = false;
            console.log("trigger: wb_required_false ["+$(this).attr("name")+"]");
            $(document).trigger("wb_required_false", [this]);
        }
        if ($(this).is("[type=password]")) {
            var pcheck = $(this).attr("name") + "_check";
            if ($("input[type=password][name='" + pcheck + "']").length) {
                if ($(this).val() !== $("input[type=password][name=" + pcheck + "]").val()) {
                    res = false;
                    $(this).data("error", wbapp.sysmsg.pass_match);
                    console.log("trigger: wb_required_false ["+$(this).attr("name")+"]");
                    $(document).trigger("wb_required_false", [this]);
                }
            }
        }
        if ($(this).is("[min]:visible")) {
			var min = $(this).attr("min") * 1;
			var minstr = $(this).val() * 1;
			if (minstr < min) {
                res = false;
                $(this).data("error", ucfirst(label) + " " + wbapp.sysmsg.min_val+": " + min);
                console.log("trigger: wb_required_false ["+$(this).attr("name")+"]");
                $(document).trigger("wb_required_false", [this]);
			}
		}

        if ($(this).is("[max]:visible")) {
			var max = $(this).attr("max") * 1;
			var maxstr = $(this).val() * 1;
			if (maxstr > max) {
                res = false;
                $(this).data("error", ucfirst(label) + " " + wbapp.sysmsg.max_val+": " + max);
                console.log("trigger: wb_required_false ["+$(this).attr("name")+"]");
                $(document).trigger("wb_required_false", [this]);
			}
		}

        if ($(this).is("[minlength]:visible")) {
            var minlen = $(this).attr("minlength") * 1;
            var lenstr = strlen($(this).val());
            if (lenstr < minlen) {
                res = false;
                $(this).data("error", ucfirst(label) + " " + wbapp.sysmsg.min_length+": " + minlen);
                console.log("trigger: wb_required_false ["+$(this).attr("name")+"]");
                $(document).trigger("wb_required_false", [this]);
            }
        }

        if ($(this).is("button")) {
			if (
				($(this).attr("value") !== undefined && $(this).val() == "")
				||
				($(this).attr("value")  == undefined && $(this).html() == "")
			) {
				res = false;
			}
		}
    });
    if (res == true) {
	console.log("trigger: wb_required_success");
        $(document).trigger("wb_required_success", [form]);
    }
    if (res == false) {
	console.log("trigger: wb_required_danger");
        $(document).trigger("wb_required_danger", [form]);
    }
    return res;
}

function wb_ajax() {
    var that = this;
    var wb_ajax_process = function(that) {
		if ($(that).is(".select2")) return;
        wb_ajax_loader();
        var ptpl = false;
        var link = that;
        var is_mail = false;
        var src = $(that).attr("data-wb-ajax");
        var start = $(that).attr("data-wb-ajax-start");
        var done = $(that).attr("data-wb-ajax-done");
        $(that).removeAttr("data-wb-ajax-done");
        if ($(that).parents("[data-wb-add=true][data-wb-tpl]").length) {
            ptpl = $(that).parents("[data-wb-add=true][data-wb-tpl]").attr("data-wb-tpl");
        }
        if ($(that).attr("data-wb-parent")!==undefined) {
            console.log(that);
            ptpl = $(document).find($(that).attr("data-wb-parent")).attr("data-wb-tpl");
        }

        var flag = true;
        if ($(that).parents("form").length) form = $(that).parents("form");
        if ($(that).is("form").length) form = $(that);

        var formdata = {};
        formdata = $(form).serializeArray();
        if (formdata["_message"] == undefined && formdata["_tpl"] == undefined && formdata["_form"] == undefined && $(that).attr("data-automail") !== "false") {
            formdata["_message"] = $(form).wbMailForm();
        }


        if (src > "") {
            var ajax = {};
            if ($(that).attr("data-wb-tpl") !== undefined) {
                ajax.tpl = $($(that).attr("data-wb-tpl")).html();
            }
            if ($(that).is("button,:input,[data-wb-ajax='/ajax/mail/']")) {
                if ($(that).parents("form").length) {
                    var form = $(that).parents("form");
                    flag = wb_check_required(form);
                    ajax = $(form).serializeArray();
					if ($(that).attr("data-automail") !== "false" && $(that).attr("data-wb-ajax")=="/ajax/mail/") {
						is_mail = true;
						ajax.push({name:"_message",value:$(form).wbMailForm()});
					}

                }
                if ($(that).attr("data-wb-json") !== undefined && $(that).attr("data-wb-json") > "") {
                    ajax = $.parseJSON($(that).attr("data-wb-json"));
                }
            }
            if (flag == true) {
                $(that).prop("disabled", true);
                $.post(src, ajax, function(data) {
					$(that).prop("disabled", false);
                    var html = $("<div>" + data + "</div>");
                    var mid = "";
                    $(html).find("[id]").each(function(i) {
                        if (i == 0) {
                            mid = $(this).attr("id");
                        }
                        $("#" + $(this).attr("id")).remove();
                    });
                    if (ptpl!==false) {
                        $(html).find("form[data-wb-form][data-wb-item]:first").attr("data-wb-parent",ptpl);
                    }
                    $("script.sc-" + mid).remove();
                    $(html).find("script").addClass("sc-" + mid);
                    $("style.st-" + mid).remove();
                    $(html).find("style").addClass("st-" + mid);
                    data = $(html).html();
                    var actions=["remove","after","before","html","replace","append","prepend","value","data"];
                    $(actions).each(function(i,a) {
                        if ($(link).attr("data-wb-"+a) !== undefined) {
                            eval('$($(link).attr("data-wb-"+a)).'+a+'(data);');
                            $($(link).attr("data-wb-"+a)).data("wb_ajax",src);
                        }
                    });

                    $("<div>" + data + "</div>").find(".modal[id]:not(.hidden)").each(function(i) {
                        if (i == 0) {
                            $("#" + $(this).attr("id")).modal();
                        }
                    });
                    if (done !== undefined) (eval(done))(link, src, data);

                    var navlink = $(document).data("data-wb-ajax");
			setTimeout(function(){
				$(navlink).each(function(i,link){
					$("[data-wb-ajax='"+link+"']").addClass("active");
				});
			},5);
                    console.log("trigger: wb_ajax_done");
					$(link).trigger("wb_ajax_done", [link, src, data]);
                    $(document).trigger("wb_ajax_done", [link, src, data]);
                    if (is_mail) {
						console.log("trigger: wb_mail_done");
						$(that).prop("disabled",false);
						$(link).trigger("wb_mail_done", [link, src, data]);
						$(document).trigger("wb_mail_done", [link, src, data]);
					}
                    wb_delegates();
                    wb_ajax_loader_done();
                    $(that).prop("disabled", false);
                    if (form!==undefined) $(form).trigger('reset');
                }).fail(function(data) {
					console.log("trigger: wb_ajax_fail");
					$(link).trigger("wb_ajax_fail", [link, src, data]);
					$(document).trigger("wb_ajax_fail", [link, src, data]);
					wb_ajax_loader_done();
					$(that).prop("disabled",false);
				});
            } else {
		        $(that).prop("disabled",false);
		        wb_ajax_loader_done();
	    }
        } else {
		$(that).prop("disabled",false);
		wb_ajax_loader_done();
            if ($(that).attr("data-wb-href") > "") {
                document.location.href = $(that).attr("data-wb-href");
            }
        }
    }


    $(document).undelegate("[data-wb-ajax]:not(.select2)", "click");
    $(document).delegate("[data-wb-ajax]:not(.select2)", "click", function(e) {
	    $(this).parents("ul").find(".active[data-wb-ajax]").removeClass("active");
	    $(this).addClass("active");
	    var act=[];
	    $(".nav").find(".active[data-wb-ajax]").each(function(){
		act.push($(this).attr("data-wb-ajax"));
	    });
	    $(document).data("data-wb-ajax",act);
	    //$(this).parents(".nav").find("a.active").removeClass("active");

	    /*
        var that = e.target;
        $(".active").each(function() {
            if ($(that).parents(".nav-sub").length) {
               // $(this).parents(".nav-sub").find(".nav-link.active").removeClass("active");
            }
            if ( 	( $(this).parents(".nav").attr("id")+$(this).parents(".nav").attr("class") )
                    !==
                        ( $(that).parents(".nav").attr("id")+$(this).parents(".nav").attr("class") )
               ) {
                $(".active").attr("data-tmp-active",true);
            }
        });
	*/
        if ($(this).is(":not(:input)") || $(this).is("button")) {
            wb_ajax_process(this);
        }
    });

    $(document).undelegate("[data-wb-ajax]:input:not(.select2)", "change");
    $(document).delegate("[data-wb-ajax]:input:not(.select2)", "change", function() {
        wb_ajax_process(this);
    });

    $("[data-wb-ajax]").each(function() {
        $(this).attr("data-wb-href", $(this).attr("href"));
        $(this).removeAttr("href");
        if ($(this).attr("data-wb-autoload") == "true") {
            $(this).trigger("click");
            $(this).removeAttr("data-wb-autoload");
        }
    });
}

$.fn.wbGetInputLabel = function() {
	var label = "";
	if (label == undefined || label == "") label = $(this).prev("label").text();
	if (label == undefined || label == "") label = $(this).next("label").text();
	if ((label == undefined || label == "") && $(this).attr("id") !== undefined) label = $(this).parents("form").find("label[for="+$(this).attr("id")+"]").text();
	if (label == undefined || label == "") label = $(this).parents(".form-group").find("label").text();
	if (label == undefined || label == "") label = $(this).attr("placeholder");
	if (label == undefined || label == "") label = $(this).attr("name");
	if (label == undefined || label == "") label = $(this).attr("data-label");
	return label;
}

$.fn.wbMailForm = function() {
    // создание автописьма из формы
    var tpl = "";
    $(this).find(":input").each(function() {
        if (!$(this).is("[type=button]") && !$(this).is("[data-mail=false]") && !in_array($(this).attr("name"),["_subject","_mailto"])) {
            var label = $(this).attr("data-label");
            if (label == undefined) label = $(this).wbGetInputLabel();
            var value = "";
            if ($(this).is("textarea")) {
                value = $(this).val();
            } else {
                value = $(this).val();
            }
            label = "<b>" + trim(strip_tags(label)) + "</b>: ";
            value = trim(strip_tags(value));
            if (value > " ") {
                tpl += label + value + "<br>\n\r";
            }
        }
    });
    return tpl;
}

$(document).bind("wb_required_false", function(event, that, text) {
	var label = $(that).wbGetInputLabel();
    var delay = (4000 + $(that).data("idx") * 250) * 1;
    var text = $(that).data("error");
    if (!text > "") {
        text = wbapp.sysmsg.fill_field+": " + label;
    }
    if (wb_plugins_loaded()) {
        $.bootstrapGrowl(text, {
ele: 'body',
type: 'danger',
offset: {
from: 'top',
                amount: 20
            },
align: 'right',
width: "auto",
delay: delay,
allow_dismiss: true,
            stackup_spacing: 10
        });
    } else {
	    $(document).trigger("required_false",that,text);
	    console.log("Trigger: require_false");
    }
});

function wb_set_inputs(selector, data) {
    if ($(selector).length) {
        var html = $(selector).outerHTML();
    } else {
        var html = selector;
    }
    html = $(html);
    $(data).each(function() {
        $(html).find("input,select,textarea").each(function() {
            if ($(this).is("[name]")) {
                var name = $(this).attr("name");
                if ($(this).is("input")) {
                    $(this).attr("value", data[name]);
                }
                if ($(this).is("textarea")) {
                    $(this).html(data[name]);
                }
                if ($(this).is("select")) {
                    $(this).find("option[value='" + data[name] + "']").attr("selected", true);
                    $(this).attr("value", data[name]);
                }
            }
        });
    });
    return $(html).outerHTML();
}

function wb_setdata(selector, data, ret) {
    if (selector == undefined) {
        var selector = "body";
    }
    if (data == undefined) {
        var data = {};
    }
    if ($(selector).length) {
        var tpl_id = $(selector).attr("data-wb-tpl");
        if (tpl_id !== undefined) {
            var html = urldecode($("#" + tpl_id).html());
        } else {
            if ($(selector).is("script")) {
                var html = $(selector).html();
            } else {
                if ($(selector).length == 1) {
                    var html = $(selector).outerHTML();
                } else {
                    var html = selector;
                }
            }
        }
    } else {
        var html = selector;
    }
    var form = "undefined";
    var item = "undefined";
    if (data.form !== undefined) {
        form = data.form;
    }
    if (data.id !== undefined) {
        item = data.id;
    }
    if (data._form !== undefined) {
        form = data._form;
    }
    if (data._id !== undefined) {
        item = data._id;
    }
    if (data._item !== undefined) {
        item = data._item;
    }

    if (is_object(html)) {var tpl=$(html).outerHTML();} else {var tpl=html;}

    var url = "/ajax/setdata/" + form + "/" + item;
    var res = null;
    var param = {tpl: tpl, data: data };
    param = base64_encode(JSON.stringify(param));
    wbapp.postWait(url, {
data: param
    }, function(data) {
        if (ret == undefined || ret == false) {
            $(selector).after(data).remove();
            res = true;
        } else {
            res = data;
        }
    });
    return res;
}

function wb_pagination(pid) {
    if (pid == undefined) {
        var slr = ".pagination";
    } else {
        var slr = ".pagination[id='" + pid + "']";
    }
    $.each($(document).find(slr), function(idx) {
        var that = this;
        var id = $(this).attr("id");
        if (id==undefined) return;
        var tplid = id.substr(5);
        $(this).data("route", $(this).attr("data-wb-route")).removeAttr("data-wb-route");
        if ($(this).is(":not([data-wb-idx])")) {
            $(this).attr("data-wb-idx", idx);
        }
        /*
        if ($("[data-wb-tpl='" + tplid + "']").data("variables") == undefined) {
            $.get("/ajax/pagination_vars/" + id, function (data) {
                $("[data-wb-tpl='" + tplid + "']").data("variables", data);
            });
        }
        */
        /*
        		$("thead[data='"+id+"']").attr("data-idx",idx);
        		$("thead[data='"+id+"'] th[data-sort]").each(function(){
        			var desc=$(this).data("desc");
        			if (desc==undefined || desc=="") {$(this).prepend("<i class='aiki-sort fa fa-arrows-v pull-left'></i>");}
        			if (desc==undefined || desc=="" || desc=="false") {$(this).attr("data-desc","false");} else {$(this).attr("data-desc","true");}
        			$(this).data("desc",$(this).attr("data-desc"));
        		});
        */
        $("[data-page^=" + id + "]").hide().removeClass("hidden");
        $("[data-page=" + id + "-1]").show();
        $(document).undelegate(".pagination[id=" + id + "] > a", "click");
        $(document).delegate(".pagination[id=" + id + "] > a", "click", function(event) {
            if ($(this).hasClass("next")) {
                $(this).parents(".pagination").find("li.active").next("li").find("a").trigger("click");
            }
            if ($(this).hasClass("prev")) {
                $(this).parents(".pagination").find("li.active").prev("li").find("a").trigger("click");
            }
        });

        $(document).undelegate("thead th[data-wb-sort]", "click");
        $(document).delegate("thead th[data-wb-sort]", "click", function(event) {
            var that=this;
            var thead=$(this).parents("thead");
            var tbody=$(this).parents("table").children("tbody");
            var id = $(thead).attr("data-wb");
            var page=$(".pagination[id="+id+"] .active").attr("data-page");
            var desc=$(this).data("desc");
            //====//
            var oldsort=$(tbody).attr("data-wb-sort");
            var s=explode(":",oldsort);
            if (s[1]==undefined || s[1]=="a") {
                s[1]="a";
            }
            else {
                s[1]="d";
            }
            oldsort = implode(":",s);
            $(tbody).attr("data-wb-sort",oldsort);
            //====//
            var newsort=$(this).attr("data-wb-sort");
            var s=explode(":",newsort);
            if (s[1]==undefined) {
                s[1]="a";
            }
            if (s[1]=="a" || s[1]=="asc") {
                s[1]="d";
                desc="true";
            }
            else {
                s[1]="a";
                desc="false";
            }
            newsort = implode(":",s);
            $(that).attr("data-wb-sort",newsort);
            //====//
            $(thead).find(".wb-desc,.wb-asc").removeClass("wb-desc wb-asc");
            if (desc=="true") {
                $(this).addClass("wb-desc");
                $(this).data("desc","false");
            } else {
                $(this).addClass("wb-asc");
                $(this).data("desc","true");
            }
            $(tbody).attr("data-wb-sort",newsort);
            var page=$(".pagination[id="+id+"] .active").attr("data-page");
            $(".pagination[id="+id+"] .active").removeClass("active");
            if (page>0) {
                $(".pagination[id="+id+"] [data-page='"+page+"'] a").trigger("click");
            } else {
                $(".pagination[id="+id+"] [data-page]:eq(1) a").trigger("click");
            }

        });



        $(document).undelegate(".pagination[id=" + id + "] li a", "click");
        $(document).delegate(".pagination[id=" + id + "] li a", "click", function(event) {
            if (!$(this).is("a") || !$(this).parent().hasClass("active")) { // отсекает дубль вызова ajax, но не работает trigger в поиске
                console.log("active_pagination(): Click");
                var that = $(this);

                var $source = $(this).parents(".pagination");
                var tid = explode("-", $(this).parents(".pagination").attr("id"));
                var tid = tid[1];
                if ($(this).parents(".page-item").attr("data-page") == "next") {
                    var cur = $(this).parents(".pagination").find(".page-item.active");
                    $(cur).next(".page-item").find("a[data-wb-ajaxpage]").trigger("click");
                    return false;
                }
                if ($(this).parents(".page-item").attr("data-page") == "prev") {
                    var cur = $(this).parents(".pagination").find(".page-item.active");
                    $(cur).prev(".page-item").find("a[data-wb-ajaxpage]").trigger("click");
                    return false;
                }
                var page = explode("/", $(this).attr("data-wb-ajaxpage"));
                var c = count(page);
                var page = "ajax-" + page[c - 3] + "-" + page[c - 2];
                var sort = null;
                var desc = null;
                if (substr(page, 0, 4) == "page") {
                    // js пагинация
                    $("[data-page^=" + id + "]").hide();
                    $("[data-page=" + page + "]").show();
                } else {
                    var cache = $source.attr("data-wb-cache");
                    var size = $source.attr("data-wb-size");
                    var pages = $source.attr("data-wb-pages");
                    var sort = $source.attr("data-wb-sort");
                    var idx = $source.attr("data-wb-idx");
                    var arr = explode("-", page);
                    var tpl = $("#" + tid).html();
                    var foreach = $('<div>').append($("[data-wb-tpl=" + tid + "]").clone());
                    $(foreach).find("[data-wb-tpl=" + tid + "]").html("");
                    $(foreach).find("[data-wb-tpl=" + tid + "]").attr("data-wb-sort", sort);
                    $(foreach).find("[data-wb-tpl=" + tid + "]").removeAttr("data-wb-desc");
                    if (pages == undefined) {
                        pages
                    }
                    var loader = $(foreach).find("[data-wb-tpl=" + tid + "]").attr("data-wb-loader");
                    var offset = $(foreach).find("[data-wb-tpl=" + tid + "]").attr("data-wb-offset");
                    if (pages == undefined) {
                        pages = $("[data-wb-tpl=" + tid + "]").attr("data-wb-pages");
                    }
                    var foreach = $(foreach).html();
                    var param = {
tpl:
                        tpl,
tplid:
                        tid,
idx:
                        idx,
uri:
                        $(this).attr("data-wb-ajaxpage"),
page:
                        arr[2],
pages:
                        pages,
size:
                        size,
cache:
                        cache
                        //, vars: $("[data-wb-tpl=" + tid + "]").data("variables")
                        ,
foreach:
                        foreach,
route:
                        $source.data("route")
                        };
                    var url = "/ajax/pagination/";
                    if ($("#" + id).data("find") !== undefined) {
                        var find = $("#" + id).data("find");
                    } else {
                        var find = $source.attr("data-wb-find");
                    }
                    if (find > "") {
                        find = urldecode(find);
                    }
                    param.find = find;
                    param.sort = sort;
                    /*
                    				if (loader=="" || loader==undefined ) {
                    					$("[data-wb-tpl="+arr[1]+"]").html(ajax_loader());
                    				} else {
                    					var funcCall = loader + "(true);";
                    					eval ( funcCall );
                    				}
                    */
                    $("body").addClass("cursor-wait");
                    $.ajax({
async: true,
type: 'POST',
data: param,
url: url,
cache: false,
success: function(data) {
                            var data = wb_json_decode(data);
                            $("[data-wb-tpl=" + tid + "]").html(data.data);
                            if (data.pages > "1") {
                                $(".pagination[id=ajax-" + pid + "]").show();
                                var pid = $(data.pagr).attr("id");
                                $(document).undelegate(".pagination[id=" + pid + "] li a", "click");
                                $("#" + pid).after(data.pagr);
                                $("#" + pid + ":first").remove();
                            } else {
                                $(".pagination[id=ajax-" + tid + "]").hide();
                            }
                            window.location.hash = "page-" + idx + "-" + arr[2];
                            wb_delegates();
                            console.log("active_pagination(): trigger:after-pagination-done");
                            $(document).trigger("after-pagination-done", [id, page, arr[2]]);
                            $("body").removeClass("cursor-wait");
                            if (loader == "" || loader == undefined) {} else {
                                var funcCall = loader + "(false);";
                                eval(funcCall);
                            }
                        },
error: function(data) {
                            $("body").removeClass("cursor-wait");
                            if (loader == "" || loader == undefined) {} else {
                                var funcCall = loader + "(false);";
                                eval(funcCall);
                            }
                            $(document).trigger("after-pagination-error", [id, page, arr[2]]);
                        }
                    });
                }
                $(this).parents("ul").find("li").removeClass("active");
                $(this).parent("li").addClass("active");

				$("[data-wb-tpl=" + tid + "]").parents().scrollTop(0);
                //$(document).trigger("after_pagination_click",[id,page,arr[2]]);
            }

        });
    });
}

function setcookie(name, value, exp_y, exp_m, exp_d, path, domain, secure) {
    var cookie_string = name + "=" + escape(value);
    if (exp_y) {
        var expires = new Date(exp_y, exp_m, exp_d);
        cookie_string += "; expires=" + expires.toGMTString();
    }
    if (path) cookie_string += "; path=" + escape(path);
    if (domain) cookie_string += "; domain=" + escape(domain);
    if (secure) cookie_string += "; secure";
    document.cookie = cookie_string;
}

function delete_cookie(cookie_name) {
    var cookie_date = new Date(); // current date & time
    cookie_date.setTime(cookie_date.getTime() - 1);
    document.cookie = cookie_name += "=; expires=" + cookie_date.toGMTString();
}

function getcookie(cookie_name) {
    var results = document.cookie.match('(^|;) ?' + cookie_name + '=([^;]*)(;|$)');
    if (results) {
        return (unescape(results[2]));
    } else {
        return null;
    }
}

function base64_decode(n){for(var r,e,o,t,f,i,a="ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",c=0,d="";r=(i=a.indexOf(n.charAt(c++))<<18|a.indexOf(n.charAt(c++))<<12|(t=a.indexOf(n.charAt(c++)))<<6|(f=a.indexOf(n.charAt(c++))))>>16&255,e=i>>8&255,o=255&i,d+=64==t?String.fromCharCode(r):64==f?String.fromCharCode(r,e):String.fromCharCode(r,e,o),c<n.length;);return d}function base64_encode(n){var r,e,o,t,f,i=[0,0,0],a=0,c=[];function d(n){c.push(n)}function h(){function n(n){return"ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/"[n]}var r=n(i[0]>>2),e=n((3&i[0])<<4|i[1]>>4),o=n((15&i[1])<<2|i[2]>>6),t=n(63&i[2]);d(1===a?r+e+"==":2===a?r+e+o+"=":r+e+o+t)}function u(n){i[a++]=n,3==a&&(h(),i[0]=0,i[1]=0,i[2]=0,a=0)}for(r=0;r<n.length;r++)55296<=(e=n.charCodeAt(r))&&e<=56319&&(o=e,56320<=(t=n.charCodeAt(r+1))&&t<=57343&&(f=void 0,f=65536,f+=t-56320,e=f+=o-55296<<10,r++)),e<128?u(e):e<2048?(u(e>>6|192),u(e>>0&63|128)):e<65536?(u(e>>12|224),u(e>>6&63|128),u(e>>0&63|128)):e<1114112&&(u(e>>18|240),u(e>>12&63|128),u(e>>6&63|128),u(e>>0&63|128));return 0<a&&h(),c.join("")}
function is_object(val) {return val instanceof Object;}
function is_callable(t,n,o){var e="",r={},i="";if("string"==typeof t)r=window,e=i=t;else{if(!(t instanceof Array&&2===t.length&&"object"==typeof t[0]&&"string"==typeof t[1]))return!1;r=t[0],i=t[1],e=(r.constructor&&r.constructor.name)+"::"+i}return!(!n&&"function"!=typeof r[i])&&(o&&(window[o]=e),!0)}
