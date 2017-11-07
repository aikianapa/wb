wb_include("/engine/js/php.js");
if ($("link[rel$=less]").length) wb_include("/engine/js/less.min.js");

$(document).ready(function () {
    wb_delegates();
});

function wb_delegates() {
    wb_ajax();
    wb_pagination();
    wb_formsave();
    wb_base_fix();
    wb_plugins();
    wb_multiinput();
    wb_tree();
}

function wb_include(url) {
    if (!$(document).find("script[src='" + url + "']").length) {
        document.write('<script src="' + url + '" type="text/javascript" ></script>\n');
    }
}

function wb_get_cdata(text) {
    text = text.replace("<![CDATA[", "").replace("]]>", "");
    return text;
}

function wb_tree() {
    $.get("/ajax/getform/common/tree_rowmenu/", function (data) {
        $(document).data("wb-tree-rowmenu", data);
    });
    $.get("/ajax/getform/common/tree_row/", function (data) {
        $(document).data("wb-tree-row", data);
    });
    $.get("/ajax/getform/common/tree_ol/", function (data) {
        $(document).data("wb-tree-ol", data);
    });
    $.get("/ajax/getform/common/tree_edit/", function (data) {
        $(document).data("wb-tree-edit", data);
    });
    $.get("/ajax/getform/common/tree_dict/", function (data) {
        $(document).data("wb-tree-dict", data);
    });

    setTimeout(function () {
        $(document).find(".wb-tree-item.dd-collapsed").each(function () {
            $(this).find("button[data-action=collapse]").hide();
            $(this).find("button[data-action=expand]").show();
        });
    }, 10);

    $(document).off("wb-tree-init");
    $(document).on("wb-tree-init", function (e, tree) {
        var data = wb_tree_serialize(tree);
        data = JSON.stringify(data);
        var name = $(tree).attr("name");
        $(tree).children("input[name=" + name + "]").val(data);
    });

    $(document).undelegate(".wb-tree .wb-tree-item button[data-action]", "click");
    $(document).delegate(".wb-tree .wb-tree-item button[data-action]", "click", function () {
        var tree = $(this).parents(".wb-tree");
        var name = $(tree).attr("name");
        setTimeout(function () {
            var data = JSON.stringify(wb_tree_serialize(tree));
            $(tree).children("input[name='" + name + "']").val(data);
        }, 200);
    });


    $(document).undelegate(".wb-tree .wb-tree-item", "contextmenu");
    $(document).delegate(".wb-tree .wb-tree-item", "contextmenu", function (e) {
        $(e.target).parent(".wb-tree-item").append("<div class='wb-tree-menu'>" + $(document).data("wb-tree-rowmenu") + "</div>");
        var offset = $(e.target).offset();
        var relativeX = (e.pageX - offset.left - 25);
        var relativeY = (e.pageY - offset.top);
        $(e.target).parent(".wb-tree-item").find(".wb-tree-menu").css("margin-left", relativeX + "px").css("margin-top", relativeY + "px");
        $(e.target).parent(".wb-tree-item").find(".wb-tree-menu [data-toggle=dropdown]").trigger("click");
        e.preventDefault();
    });
    $(document).undelegate(".wb-tree .wb-tree-item", "mouseleave");
    $(document).delegate(".wb-tree .wb-tree-item", "mouseleave", function (e) {
        $(this).find(".wb-tree-menu").remove();
        e.preventDefault();
    });

    $(document).undelegate(".wb-tree-item .dd-content", "focusout");
    $(document).delegate(".wb-tree-item .dd-content", "focusout", function (e) {
        $(this).parent(".wb-tree-item").attr("data-name", $(this).text());
        var tree = $(this).parents(".wb-tree");
        var name = $(tree).attr("name");
        var data = JSON.stringify(wb_tree_serialize(tree));
        $(tree).children("input[name='" + name + "']").val(data);
    });

    $(document).undelegate(".wb-tree-item .dd3-btn", "click");
    $(document).delegate(".wb-tree-item .dd3-btn", "click", function (e) {
        $(this).parent(".wb-tree-item").children(".dd-content").trigger("dblclick");
    });

    $('.wb-tree').on('change', function (e) {
        var that = e.target;
        var name = $(that).attr("name");
        setTimeout(function () {
            var data = wb_tree_serialize(that);
            data = JSON.stringify(data);
            $(that).children("input[name=" + name + "]").val(data);
        }, 200);
    });

    $(document).undelegate(".wb-tree-item > .dd-content", "dblclick");
    $(document).delegate(".wb-tree-item > .dd-content", "dblclick", function (e) {
        var cont = this;
        var tree = $(this).parents("[data-wb-role=tree]");
        var that = $(e.target).parent(".wb-tree-item");

        var item = $(this).parents(".wb-tree-item").attr("data-id");
        var form = $(tree).parents("[data-wb-form]").attr("data-wb-form");
        var text = $(this).val();
        var name = $(tree).attr("name");

        var edid = "#tree_" + form + "_" + name;
        if ($(document).find(edid).length) {
            $(document).find(edid).remove();
        }
        var edit = $($(document).data("wb-tree-edit"));
        var path = wb_tree_data_path(that);
        var data = wb_tree_data_get(that, path);
        var orig = data;

        var dict = $(tree).children("[data-name=dict]").val();
        if (dict == undefined || dict == "" || trim(dict) == " ") {
            var dict = [];
        } else {
            var dict = $.parseJSON(dict);
        }

        var dataval = data["data"];
        var tpl = wb_tree_data_fields(dict);
        data["_form"] = data["data"]["_form"] = form;
        data["_id"] = data["data"]["_id"] = $(tree).parents("form[data-wb-item]").attr("data-wb-item");


        var tpl = $(wb_setdata(tpl, data["data"], true));
        tpl.find(".wb-uploader").attr("data-wb-path", "/uploads/" + form + "/" + $(tree).parents("form[data-wb-item]").attr("data-wb-item"));
        data["fields"] = dict;
        data["name"] = name;
        data["data-name"] = text;
        data["form"] = form;
        data["data-id"] = item;
        $(".content-w .tree-edit.modal").remove();
        edit = $(wb_setdata(edit, data, true));
        edit.find(".modal").attr("id", "tree_" + form + "_" + name);
        $(".content-w").append(edit);
        $(".content-w").find(".modal #treeData form").html(tpl);
        $(edid).after("<div class='modal-backdrop show fade'></div>");
        $(edid).css("z-index", 10000);
        $(edid).next(".modal-backdrop").css("z-index", 9999);
        $(edid).data("path", path);
        $(edid).modal();
        $(edid).undelegate("#treeDict *", "change");
        $(edid).delegate("#treeDict *", "change", function (e) {
            if ($(e.currentTarget).is("input,select,textarea")) {
                var fields = $(edid).find("#treeDict [data-wb-role=multiinput][name=fields]");
                var tpl = wb_tree_dict_change(fields, tree);
                tpl = $(wb_setdata(tpl, dataval, true));
                $(this).parents("#treeDict").prev("#treeData").children("form").html(tpl);
                wb_plugins();
            }
        });

        $(edid).off("multiinput");
        $(edid).on("multiinput", function (e, multi, trigger) {
            if ($(multi).attr("name") == "fields") {
                var fields = multi;
                var tpl = wb_tree_dict_change(fields, tree);
                tpl = $(wb_setdata(tpl, dataval, true));
                $(edid).find("#treeData").children("form").html(tpl);
                wb_plugins();
            }
        });

        $(edid).find('.modal-footer button').off("click");
        $(edid).find('.modal-footer button').on("click", function (e) {
            if ($(this).hasClass("tree-close")) {
                var data = $(edid).find(".modal-body > form").serializeArray();
                $(data).each(function (i, d) {
                    $(that).attr(d.name, d.value);
                    if (d.name == "data-name") {
                        $(that).children(".dd-content").val(d.value);
                    }
                    if (d.name == "data-id") {
                            console.log(i,d);
                        $(that).children(".dd3-btn").children("span").html(d.value);
                    }
                });
                var cdata = JSON.stringify($(edid).find("#treeData > form").serializeArray());
                wb_tree_data_set(that, path, cdata);
                $(tree).find("input[name='" + name + "']").val(JSON.stringify(wb_tree_serialize($(tree).children(".dd-list"))));
            }
            $(edid).modal("hide");
            $(edid).next(".modal-backdrop").remove();
            setTimeout(function () {
                $(edid).remove();
            }, 500)
        });
        wb_plugins();
    });


    $(document).undelegate(".wb-tree-menu .dropdown-item", "click");
    $(document).delegate(".wb-tree-menu .dropdown-item", "click", function (e) {
        var tree = $(this).parents("[data-wb-role=tree]");
        var name = $(tree).attr("name");
        var tpl = $($(tree).attr("data-tpl")).html();
        var row = $(document).data("wb-tree-row");
        var form = $(this).parents("[data-wb-form]").attr("data-wb-form");
        row = wb_setdata(row, {
            "name": "",
            "form": form,
            "id": wb_newid()
        }, true);
        var name = $(tree).attr("name");
        if ($(this).attr("href") == "#after") {
            $(this).parents(".wb-tree-menu").parent(".wb-tree-item").after(row);
        }
        if ($(this).attr("href") == "#before") {
            $(this).parents(".wb-tree-menu").parent(".wb-tree-item").before(row);
        }
        if ($(this).attr("href") == "#remove") {
            $(this).parents(".wb-tree-menu").parent(".wb-tree-item").remove();
        }
        if (!$(tree).find(".wb-tree-item").length) {
            $(tree).children(".dd-list").append(row);
        }
        $(this).parents(".wb-tree-menu").remove();
        setTimeout(function () {
            var data = JSON.stringify(wb_tree_serialize($(tree).children(".dd-list")));
            $(tree).find("input[name='" + name + "']").val(data);
        }, 200);
    });
}

function wb_tree_dict_change(fields, tree) {
    var dict = [];
    $(fields).find(".wb-multiinput").each(function (i) {
        var fld = {};
        $(this).find("input,select,textarea").each(function () {
            if ($(this).is("input")) {
                fld[$(this).attr("data-wb-field")] = $(this).val();
            }
            if ($(this).is("textarea")) {
                fld[$(this).attr("data-wb-field")] = $(this).html();
            }
            if ($(this).is("select")) {
                fld[$(this).attr("data-wb-field")] = $(this).find("option:selected").attr("value");
            }
        });
        dict.push(fld);
    });
    $(tree).children("[data-name=dict]").val(JSON.stringify(dict));
    var res = wb_tree_data_fields(dict);
    return res;
}

function wb_tree_data_fields(data) {
    var res = false;
    var dict = {};
    $(data).each(function (i) {
        dict[i] = data[i];
    });
    $.ajax({
        async: false,
        type: 'POST',
        url: "/ajax/buildfields/",
        data: dict,
        success: function (data) {
            res = data;
        },
        error: function (data) {
            res = "Ошибка!";
        }
    });
    return res;
}

function wb_tree_data_get(that, path) {
    var tree = $(that).parents("[data-wb-role=tree]");
    var name = $(tree).attr("name");
    var data = $(tree).children("input[name=" + name + "]").val();
    if (data == "" || data == undefined) {
        data = "[]";
    }
    data = JSON.safeParse(data);
    data["data"] = wb_get_cdata($(that).children(".data").html());
    if (trim(data["data"]) > " ") {
        data["data"] = JSON.parse(data["data"]);
    } else {
        data["data"] = [];
    }
    if (path == undefined) {
        var path = wb_tree_data_path(that);
    }
    $(path).each(function (i, j) {
        if (i == 0) {
            data = data[j];
        } else {
            data = data["children"][j];
        }
    });
    if (data == undefined) {
        data = "[]";
    }
    return data;
}

JSON.safeParse = function (input, def) {
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

function wb_tree_data_set(that, path, values) {
    var tree = $(that).parents(".wb-tree");
    var name = $(tree).attr("name");
    var data = JSON.parse($(tree).children("input[name=" + name + "]").val());
    var dict = $(tree).children("[data-name=dict]").val();
    if (dict > "") {
        var dict = JSON.parse($(tree).children("[data-name=dict]").val());
    } else {
        var dict = [];
    }
    var fields = JSON.parse(values);
    var values = {};
    $(fields).each(function (j, d) {
        var fldname = d["name"];
        var fldval = d["value"];
        $(dict).each(function (z, di) {
            if (di["name"] == fldname) {
                fldval = wb_iconv(fldval, di["type"]);
                values[fldname] = fldval;
            }
        });
    });
    if (path == undefined) {
        var path = wb_tree_data_path(that);
    }
    var p = "";
    $(path).each(function (i, j) {
        if (i == 0) {
            p = "[" + j + "]";
        } else {
            p += "['children'][" + j + "]";
        }
    });
    eval("data" + p + "['data']=values;");
    $(that).children(".data").html("<![CDATA[" + JSON.stringify(values) + "]]>");
    data = JSON.stringify(data);
    $(tree).children("input[name=" + name + "]").val(data);
    return data;
}

function wb_tree_data_path(that, path) {
    if (path == undefined) {
        var path = [];
    }
    path.unshift($(that).index());
    if ($(that).parents(".dd-list").index() !== 0) {
        path = wb_tree_data_path($(that).parents(".dd-list").parents(".wb-tree-item"), path);
    }
    return path;
}


function wb_tree_serialize(that, branch) {
    var flag = false;
    if ($(that).is(".wb-tree")) {
        var tree = that;
    } else {
        var tree = $(that).parents(".wb-tree");
    }
    if (branch == undefined) {
        branch = $(tree).children(".dd-list");
        flag = true;
    }

    var tree_data = [];
    $(branch).children(".wb-tree-item").each(function () {
        var name = $(this).attr("data-name");
        var open = $(this).attr("data-open");
        var id = $(this).attr("data-id");
        if (id == undefined || id == "") {
            id = wb_newid();
        }
        if ($(this).hasClass("dd-collapsed")) {
            open = false;
        } else {
            open = true;
        }
        $(this).attr("data-open", open);
        var flds = wb_get_cdata($(this).children(".data").html());
        if (trim(flds) > " ") {
            flds = JSON.parse(flds);
        }
        var path = wb_tree_data_path(this);
        if ($(this).children(".dd-list").length) {
            var child = wb_tree_serialize(tree, $(this).children(".dd-list"));
        } else {
            var child = false;
            var open = false;
        }
        tree_data.push({
            id: id,
            name: name,
            open: open,
            data: flds,
            children: child
        });
    });
    return tree_data;
}



function wb_newid() {
    var newid = "";
    $.ajax({
        async: false,
        type: 'GET',
        url: "/ajax/newid/",
        success: function (data) {
            newid = JSON.parse(data);
        },
        error: function (data) {
            newid = $(document).uniqueId();
        }
    });
    return newid;
}

function wb_multiinput() {
    $.get("/ajax/getform/common/multiinput_menu/", function (data) {
        $(document).data("wb-multiinput-menu", data);
    });
    $.get("/ajax/getform/common/multiinput_row/", function (data) {
        $(document).data("wb-multiinput-row", data);
    });
    $(document).undelegate(".wb-multiinput", "mouseenter");
    $(document).delegate(".wb-multiinput", "mouseenter", function () {
        $(this).append("<div class='wb-multiinput-menu'>" + $(document).data("wb-multiinput-menu") + "</div>");
    });
    $(document).undelegate(".wb-multiinput", "mouseleave");
    $(document).delegate(".wb-multiinput", "mouseleave", function () {
        $(this).find(".wb-multiinput-menu").remove();
    });
    $(document).undelegate(".wb-multiinput", "contextmenu");
    $(document).delegate(".wb-multiinput", "contextmenu", function (e) {
        var offset = $(this).offset();
        var relativeX = (e.pageX - offset.left - 25);
        var relativeY = (e.pageY - offset.top);
        $(this).find(".wb-multiinput-menu").css("margin-left", relativeX + "px").css("margin-top", relativeY + "px");
        $(this).find(".wb-multiinput-menu [data-toggle=dropdown]").trigger("click");
        return false;
    });
    $(document).undelegate(".wb-multiinput-menu .dropdown-item", "click");
    $(document).delegate(".wb-multiinput-menu .dropdown-item", "click", function (e) {
        var multi = $(this).parents("[data-wb-role=multiinput]");
        var tpl = $($(multi).attr("data-tpl")).html();
        var row = $(document).data("wb-multiinput-row");
        var name = $(multi).attr("name");

        row = str_replace("{{template}}", tpl, row);
        row = wb_setdata(row, {
            "form": "procucts",
            "id": "_new"
        }, true);
        if ($(this).attr("href") == "#after") {
            $(this).parents(".wb-multiinput").after(row);
        }
        if ($(this).attr("href") == "#before") {
            $(this).parents(".wb-multiinput").before(row);
        }
        if ($(this).attr("href") == "#remove") {
            $(this).parents(".wb-multiinput").remove();
        }
        if (!$(multi).find(".wb-multiinput").length) {
            $(multi).append(row);
        }
        wb_multiinput_sort(multi);
        $(multi).trigger("multiinput", multi, this);
        wb_plugins();
        e.preventDefault();
    });
}

function wb_multiinput_sort(mi) {
    var name = $(mi).attr("name");
    $(mi).find(".wb-multiinput").each(function (i) {
        $(this).find("input,select,textarea").each(function () {
            if ($(this).attr("data-wb-field") > "") {
                var field = $(this).attr("data-wb-field");
            } else {
                var field = $(this).attr("name");
            }
            if (field !== undefined && field > "") {
                $(this).attr("name", name + "[" + i + "][" + field + "]");
                $(this).attr("data-wb-field", field);
            }
        });
    });
}

function wb_base_fix() {
    if ($("base").length) {
        var base = $("base").attr("href");
        $(document).undelegate("a", "click");
        $(document).delegate("a", "click", function (e) {
            if (!$(this).is("[data-toggle]")) {
                var hash = $(this).attr("href");
                var role = $(this).attr("role");
                if (hash !== undefined && role == undefined && substr(hash, 0, 1) == "#") {
                    var loc = explode("#", window.location.href);
                    var loc = str_replace(base, "", loc[0]);
                    document.location = loc + hash;
                    e.preventDefault();
                }
            }
        });
    }
}

function wb_plugins() {
    $(document).ready(function () {
        if ($("[data-wb-src=datepicker]").length) {
            $("[type=datepicker]:not(.wb-plugin)").each(function () {
                if ($(this).val() > "") {
                    $(this).attr("data-date-format", "d.m.Y"); // PHP format
                    $(this).val(wb_oconv_object(this));
                }
                if ($(this).attr("data-date-format") == undefined) {
                    $(this).attr("data-date-format", "dd.mm.yyyy"); // Plugin Format
                }
                $(this).addClass("wb-plugin");
                var lang = "ru";
                if ($(this).attr("data-wb-lang") !== undefined) {
                    lang = $(this).attr("data-wb-lang");
                }
                $(this).datetimepicker({
                    language: lang,
                    autoclose: true,
                    todayBtn: true,
                    minView: 2
                }).on('changeDate', function (ev) {
                    $(this).attr("value",wb_iconv_object(this));
                });
            });

            $("[type=datetimepicker]:not(.wb-plugin)").each(function () {
                if ($(this).val() > "") {
                    $(this).attr("data-date-format", "d.m.Y H:i"); // PHP format
                    $(this).val(wb_oconv_object(this));
                }
                if ($(this).attr("data-date-format") == undefined) {
                    $(this).attr("data-date-format", "dd.mm.yyyy hh:ii"); // Plugin Format
                }
                $(this).addClass("wb-plugin");
                var lang = "ru";
                if ($(this).attr("data-wb-lang") !== undefined) {
                    lang = $(this).attr("data-wb-lang");
                }
                $(this).datetimepicker({
                    language: lang,
                    autoclose: true,
                    todayBtn: true
                }).on('changeDate', function (ev) {
                    $(this).attr("value",wb_iconv_object(this));
                });
            });
        }
        if ($(".dd[data-wb-role=tree]").length) {
            $(".dd[data-wb-role=tree]").each(function (e) {
                $(this).nestable({
                    maxDepth: 100
                });
                $(".dd-item").unbind("contextmenu");
                $(this).trigger("wb-tree-init", this);
            });
        }
        if ($('.select2:not(.wb-plugin)').length) {
            $('.select2:not(.wb-plugin').each(function () {
                var that = this;
                if ($(this).is("[data-wb-ajax]")) {
                    var url = $(this).attr("data-wb-ajax");
                    var tpl = $(this).attr("data-wb-tpl");
                    var where = $(this).attr("data-wb-where");
                    var val = $(this).attr("value");
                    var plh = $(this).attr("placeholder");
                    if (plh == undefined) {
                        plh = "Поиск...";
                    }
                    $(this).select2({
                        language: "ru",
                        placeholder: plh,
                        minimumInputLength: 2,
                        ajax: {
                            url: url,
                            method: "post",
                            dataType: 'json',
                            delay: 200,
                            cache: true,
                            data: function (term, page) {
                                return {
                                    value: term.term,
                                    page: page,
                                    where: where,
                                    tpl: $("#" + tpl).html()
                                };
                            },
                            processResults: function (data) {
                                $(that).data("wb-ajax-data", data);
                                $(that).trigger("wb_ajax_done", [that, url, data]);
                                $(that).data("item",data);
                                return {
                                    results: data
                                };
                            },
                        },
                    });
                    $.ajax({
                        url: url,
                        method: "post",
                        dataType: 'json',
                        data: {
                            id: val,
                            tpl: $("#" + tpl).html()
                        }
                    }).then(function (data) {
                        var option = new Option(data.text, data.id, true, true);
                        $(that).append(option).trigger('change');
                        $(document).data("item",data.item);
                        $(that).trigger({
                            type: 'select2:select',
                            params: {
                                data: data
                            }
                        });
                    });
                    $(that).off("change");
                    $(that).on("change",function(){
                        if ($(that).val()>"") {
                            $($(that).data("item")).each(function(i,item){
                                if (item.id==$(that).val()) {$(that).data("item",item.item);return;}
                            });
                        }
                    });
                } else {
                    $(this).select2();
                }

            });

            $('.select2').addClass("wb-plugin");
        }

        if ($('.input-tags').length) {
            $('.input-tags').each(function () {
                if ($(this).attr("placeholder") !== undefined) {
                    var ph = $(this).attr("placeholder");
                } else {
                    var ph = 'новый';
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

        if (is_callable("wb_plugin_editor")) wb_plugin_editor();
        if (is_callable("wbCommonUploader")) wbCommonUploader();
    });
}

function wb_plugin_editor() {
    if ($("textarea.editor:not(.wb-plugin)").length) {
        $("textarea.editor:not(.wb-plugin)").each(function () {
            $(this).addClass("wb-plugin");
            var fldname = $(this).attr("name");
            if ($(this).attr("id") == undefined || $(this).attr("id") == "") {
                $(this).attr("id", wb_newid());
            }

            var editor = $(this).ckeditor();
            CKEDITOR.config.extraPlugins = 'youtube';
            CKEDITOR.config.toolbarGroups = [
                {
                    name: 'document',
                    groups: ['document', 'doctools']
                },
			//    { name: 'editing',     groups: [ 'find', 'selection', 'spellchecker' ] },
                {
                    name: 'mode'
                },
                {
                    name: 'clipboard',
                    groups: ['clipboard', 'undo']
                },
                {
                    name: 'links'
                },
                {
                    name: 'insert'
                },
                {
                    name: 'others'
                },
				'/',
                {
                    name: 'basicstyles',
                    groups: ['basicstyles', 'cleanup']
                },
                {
                    name: 'paragraph',
                    groups: ['list', 'indent', 'blocks', 'align']
                },
                {
                    name: 'colors'
                },
                {
                    name: 'tools'
                }
			];
            CKEDITOR.config.skin = 'bootstrapck';
            CKEDITOR.config.allowedContent = true;
            CKEDITOR.config.forceEnterMode = true;
            CKEDITOR.plugins.registered['save'] = {
                init: function (editor) {
                    var command = editor.addCommand('save', {
                        modes: {
                            wysiwyg: 1,
                            source: 1
                        },
                        exec: function (editor) {
                            var fo = editor.element.$.form;
                            editor.updateElement();
                            wb_formsave($(fo));
                        }
                    });
                    editor.ui.addButton('Save', {
                        label: 'Сохранить',
                        command: 'save'
                    });
                }
            }
        });

        for (var i in CKEDITOR.instances) {
            // это работает
            CKEDITOR.instances[i].on('change', function () {
                CKEDITOR.instances[i].updateElement();
            });
        }

        /*
        			CKEDITOR.on('instanceReady', function(){
        			   $.each( CKEDITOR.instances, function(instance) {
        				CKEDITOR.instances[instance].on("change", function(e) {
        							var fldname=$("textarea#"+instance).attr("name");
        							if (fldname>"" && $("textarea#"+instance).parents("form").find("[name="+fldname+"]").length) {
        								$("textarea#"+instance).parents("form").find("[name="+fldname+"]:not(.ace_editor)").html(CKEDITOR.instances[instance].getData());
        							} else {
        								$("textarea#"+instance).html(CKEDITOR.instances[instance].getData());	
        							}
        							$(document).trigger("editorChange",{
        								"value" : CKEDITOR.instances[instance].getData(),
        								"field"	: fldname
        							});
        						$("textarea#"+instance).trigger("change");
        				});
        			   });			   
        			});
        */

        $(document).on("sourceChange", function (e, data) {
            var form = data.form;
            var field = data.field;
            var value = data.value;

            $(this).html(data.value);
            if (CKEDITOR.instances[$("[name=" + field + "]").attr("id")] !== undefined) {
                CKEDITOR.instances[$("[name=" + field + "]").attr("id")].setData(value);

            }
            e.preventDefault();
            return false;



        });


    }
}


function wb_formsave() {
    // <button data-formsave="#formId" data-src="/path/ajax.php"></button>
    // data-formsave	-	JQ идентификатор сохраняемой формы
    // data-form		-	переопределяет имя формы, по-умолчанию берётся аттрибут name тэга form
    // data-src			-	путь к кастомному ajax обработчику (необязательно)

    $(document).undelegate(".modal-dialog:visible input, .modal-dialog:visible select, .modal-dialog:visible textarea", "change");
    $(document).delegate(".modal-dialog:visible input, .modal-dialog:visible select, .modal-dialog:visible textarea", "change", function () {
        $(".modal-dialog:visible").find("[data-formsave] span.glyphicon").removeClass("glyphicon-ok").addClass("glyphicon-save");
    });


    $(document).undelegate("[data-wb-formsave]:not([data-wb-role=include])", "click");
    $(document).delegate("[data-wb-formsave]:not([data-wb-role=include])", "click", function () {
        var formObj = $($(this).attr("data-wb-formsave"));
        if ($(this).attr("data-wb-add") == "false") {
            $(formObj).attr("data-wb-add", "false");
        }
        $(this).find("span.glyphicon").addClass("loader");
        var save = wb_formsave_obj(formObj);
        $(this).find("span.glyphicon").removeClass("loader glyphicon-save").addClass("glyphicon-ok");
        if (save) {
            return save;
        } else {
            return {
                error: 1
            };
        }
        return false;
    });
}

function wb_iconv_object(obj) {
    var type = $(obj).attr("type");
    var value = $(obj).val();
    return wb_iconv(value, type);
}

function wb_iconv(value, type) {
    if (substr(type, 0, 4) == "date") {
        if (type == "datepicker") {
            mask = "Y-m-d";
        }
        if (type == "date") {
            mask = "Y-m-d";
        }
        if (type == "datetimepicker") {
            mask = "Y-m-d H:i";
        }
        if (type == "datetime") {
            mask = "Y-m-d H:i";
        }
        return date(mask, strtotime(value));
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
            return date(mask, strtotime(value));
        } else {
            if (type == "datepicker") {
                mask = "YYYY-MM-DD";
            }
            if (type == "date") {
                mask = "YYYY-MM-DD";
            }
            if (type == "datetimepicker") {
                mask = "YYYY-MM-DD H:m";
            }
            if (type == "datetime") {
                mask = "YYYY-MM-DD H:m";
            }
            return date(mask, strtotime(value));
        }

    }
    return value;
}

function wb_formsave_obj(formObj) {
    if (wb_check_required(formObj)) {
        var name = formObj.attr("data-wb-form");
        var item = formObj.attr("data-wb-item");
        var oldi = formObj.attr("data-wb-item-old");
        $(document).trigger(name + "_before_formsave", [name, item, form, true]);

        var ptpl = formObj.attr("parent-template");
        var padd = formObj.attr("data-wb-add");
        // обработка switch из appUI (и checkbox вообще кроме bs-switch)
        var ui_switch = "";
        formObj.find("input[type=checkbox]:not(.bs-switch)").each(function () {
            var swname = $(this).attr("name");
            if ($(this).prop("checked") == true) {
                ui_switch += "&" + swname + "=on";
            } else {
                ui_switch += "&" + swname + "=";
            }
        });

        // обработка bootstrap switch
        var bs_switch = "";
        formObj.find(".bs-switch").each(function () {
            var bsname = $(this).attr("name");
            if (bsname != undefined && bsname > "") {
                if ($(this).bootstrapSwitch("state") == true) {
                    bs_switch += "&" + bsname + "=on";
                } else {
                    bs_switch += "&" + bsname + "=";
                }
            }
        });
        if (formObj.find("input[name=id]").length) {
            var item_id = formObj.find("input[name=id]").val();
        } else {
            item_id = formObj.attr("data-wb-item");
        }
        var ic_date = "";
        formObj.find("[name][type^=date]").each(function () {
            var dtname = $(this).attr("name");
            var type = $(this).attr("type");
            var val = wb_iconv_object(this);
            ic_date += "&" + dtname + "=" + val;
        });


        // прячем данные корзины перед сериализацией - нужно для orders_edit.php
        var cart = formObj.find("[data-wb-role=cart]");
        if (cart.length) {
            cart.find("input,select,textarea").each(function () {
                if ($(this).attr("disabled") != undefined) {
                    $(this).addClass("tmpDisabled");
                } else {
                    $(this).prop("disabled");
                }
            });
            var form = formObj.serialize();
            cart.find("input,select,textarea").each(function () {
                if (!$(this).hasClass("tmpDisabled")) {
                    $(this).removeAttr("disabled");
                }
            });

        } else {
            var form = formObj.serialize();
        }
        form += ui_switch + bs_switch + ic_date;

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

        if (ptpl == undefined) {
            var ptpl = $(document).find("[data-wb-add=true][data-wb-tpl]").attr("data-wb-tpl");
        }
        if ($(this).parents("#engine__setup").length) {
            var setup = true;
        } else {
            setup = false;
        }
        if (name !== undefined) {
            var data = {
                mode: "save",
                form: name
            };
            $.ajax({
                type: 'POST',
                url: src,
                data: form,
                success: function (data) {
                    if ($.bootstrapGrowl) {
                        $.bootstrapGrowl("Сохранено!", {
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

                    if (ptpl !== undefined && padd !== "false") {
                        var tpl = $(document).find("#" + ptpl).html();
                        var list = $(document).find("[data-wb-tpl=" + ptpl + "]");
                        var post = {
                            tpl: tpl
                        };

                        var ret = false;
                        if (list.attr("data-add") + "" !== "false") {
                            $.post("/ajax/setdata/" + name + "/" + item_id, post, function (ret) {
                                if (list.find("[item=" + item_id + "]").length) {
                                    list.find("[item=" + item_id + "]").after(ret);
                                    list.find("[item=" + item_id + "]:first").remove();
                                } else {
                                    list.prepend(ret);
                                }
                                list.find("[item=" + item + "]").each(function () {
                                    if ($(this).attr("idx") == undefined) {
                                        $(this).attr("idx", $(this).attr("item"));
                                    }
                                });
                            });
                        }

                    }
                    if (setup == true) {
                        document.location.href = "/login.htm";
                    }
                    if (is_callable(name + "_after_formsave")) {
                        $(document).trigger(name + "_after_formsave", [name, item, form, true]);
                    }
                    return data;
                },
                error: function (data) {
                    if (is_callable(name + "_after_formsave")) {
                        $(document).trigger(name + "_after_formsave", [name, item, form, false]);
                    }
                    if ($.bootstrapGrowl) {
                        $.bootstrapGrowl("Ошибка сохранения!", {
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
        $.bootstrapGrowl("Ошибка сохранения!", {
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
    $(form).find("input[required],select[required],textarea[required],[type=password]").each(function (i) {
        if ($(this).is(":not([disabled],[type=checkbox]):visible")) {
            if ($(this).val() == "") {
                res = false;
                idx++;
                $(this).data("idx", idx);
                $(document).trigger("wb_required_false", [this]);
            } else {
                if ($(this).attr("type") == "email" && !wb_check_email($(this).val())) {
                    res = false;
                    idx++;
                    $(this).data("idx", idx);
                    $(this).data("error", "Введите корректный email");
                    $(document).trigger("wb_required_false", [this]);
                } else {
                    $(document).trigger("wb_required_true", [this]);
                }
            }
        }
        if ($(this).is("[type=password]")) {
            var pcheck = $(this).attr("name") + "_check";
            if ($("input[type=password][name=" + pcheck + "]").length) {
                if ($(this).val() !== $("input[type=password][name=" + pcheck + "]").val()) {
                    res = false;
                    $(this).data("error", "Пароли должны совпадать");
                    $(document).trigger("wb_required_false", [this]);
                }
            }
        }
    });
    if (res == true) {
        $(document).trigger("wb_required_success", [form]);
    }
    if (res == false) {
        $(document).trigger("wb_required_danger", [form]);
    }
    return res;
}



function wb_ajax() {
    $(document).undelegate("[data-wb-ajax]", "click");
    $(document).delegate("[data-wb-ajax]", "click", function () {
        var link = this;
        var src = $(this).attr("data-wb-ajax");
        var ajax = {};
        if ($(link).attr("data-wb-tpl") !== undefined) {
            ajax.tpl = $($(link).attr("data-wb-tpl")).html();
        }
        $.post(src, ajax, function (data) {
            var html = $("<div>" + data + "</div>");
            var mid = "";
            $(html).find("[id]").each(function (i) {
                if (i == 0) {
                    mid = $(this).attr("id");
                }
                $("#" + $(this).attr("id")).remove();
            });
            $("script.sc-" + mid).remove();
            $(html).find("script").addClass("sc-" + mid);
            $("style.st-" + mid).remove();
            $(html).find("style").addClass("st-" + mid);
            data = $(html).html();
            if ($(link).attr("data-wb-remove") !== undefined) {
                $($(link).attr("data-wb-remove")).remove();
            }
            if ($(link).attr("data-wb-after") !== undefined) {
                $($(link).attr("data-wb-after")).after(data);
            }
            if ($(link).attr("data-wb-before") !== undefined) {
                $($(link).attr("data-wb-before")).before(data);
            }
            if ($(link).attr("data-wb-html") !== undefined) {
                $($(link).attr("data-wb-html")).html(data);
            }
            if ($(link).attr("data-wb-replace") !== undefined) {
                $($(link).attr("data-wb-replace")).replaceWith(data);
            }
            if ($(link).attr("data-wb-append") !== undefined) {
                $($(link).attr("data-wb-append")).append(data);
            }
            if ($(link).attr("data-wb-prepend") !== undefined) {
                $($(link).attr("data-wb-prepend")).prepend(data);
            }
            $("<div>" + data + "</div>").find(".modal[id]").each(function (i) {
                if (i == 0) {
                    $("#" + $(this).attr("id")).modal();
                }
            });
            $(document).trigger("wb_ajax_done", [link, src, data]);
            wb_plugins();
            wb_delegates();
        });
    });
    $("[data-wb-ajax][data-wb-autoload=true]").each(function () {
        $(this).trigger("click");
        $(this).removeAttr("data-wb-autoload")
    });
}

$(document).unbind("wb_required_false");
$(document).on("wb_required_false", function (event, that, text) {
    var delay = (4000 + $(that).data("idx") * 250) * 1;
    var text = $(that).data("error");
    if (!text > "") {
        text = "Заполните поле: " + $(that).attr("name");
        if ($(that).parents(".form-group").find("label").text() > "") {
            text = "Заполните поле: " + $(that).parents(".form-group").find("label").text();
        }
        if ($(that).attr("placeholder") > "") {
            text = "Заполните поле: " + $(that).attr("placeholder");
        }
    }

    $.bootstrapGrowl(text, {
        ele: 'body',
        type: 'warning',
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

});

function wb_set_inputs(selector, data) {
    if ($(selector).length) {
        var html = $(selector).outerHTML();
    } else {
        var html = selector;
    }
    html = $(html);
    $(data).each(function () {
        $(html).find("input,select,textarea").each(function () {
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
    var param = {
        tpl: html,
        data: data
    };
    var url = "/ajax/setdata/" + form + "/" + item;
    var res = null;
    $.when($.ajax({
        type: 'POST',
        async: false,
        data: param,
        url: url
    })).done(function (data) {
        if (ret == undefined || ret == false) {
            $(selector).after(data).remove();
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
        var slr = ".pagination[id=" + pid + "]";
    }
    $.each($(document).find(slr), function (idx) {
        var id = $(this).attr("id");
        if ($(this).is(":not([data-idx])")) {
            $(this).attr("data-idx", idx);
        }
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
        $(document).undelegate(".pagination[id=" + id + "] li a, thead[data=" + id + "] th[data-sort]", "click");
        $(document).delegate(".pagination[id=" + id + "] li a, thead[data=" + id + "] th[data-sort]", "click", function (event) {
            if (!$(this).is("a") || !$(this).parent().hasClass("active")) { // отсекает дубль вызова ajax, но не работает trigger в поиске
                console.log("active_pagination(): Click");
                var that = $(this);
                /*
                			if ($(this).is("th[data-sort]")) {
                				var $source=$(this).parents("thead");
                				var page=$source.attr("data")+"-"+$(".pagination[id="+id+"] .active").attr("data-page");
                				var sort=$(this).attr("data-sort");
                				var desc=$(this).attr("data-desc");
                				var sort=explode(" ",trim(sort));
                				$(sort).each(function(i){
                					var s=explode(":",sort[i]);
                					if (s[1]==undefined) {
                						if (desc==undefined) {s[1]="a";}
                						if (desc!==undefined && desc=="false") {s[1]="a"; desc="false";}
                						if (desc!==undefined && desc=="true") {s[1]="d"; desc="true";}
                					}
                					if (s[1]=="a") {s[1]="d";} else {s[1]="a";}
                					$(that).attr("data-sort",implode(":",s));
                				});
                				var sort=$(this).attr("data-sort");

                				$(this).parents("thead").find("th[data-sort]").each(function(){
                					$(this).find(".aiki-sort").remove();
                					$(this).data("desc","");
                					$(this).removeAttr("data-desc");
                				});
                				if (desc=="true") {
                					$(this).prepend("<i class='aiki-sort fa fa-long-arrow-up pull-left'></i>");
                					$(this).data("desc","false");
                				} else {
                					$(this).prepend("<i class='aiki-sort fa fa-long-arrow-down pull-left'></i>");
                					$(this).data("desc","true");
                				}
                			} else {
                */
                var $source = $(this).parents(".pagination");
                var page = $(this).attr("data");
                var sort = null;
                var desc = null;
                //			}
                if (substr(page, 0, 4) == "page") {
                    // js пагинация
                    $("[data-page^=" + id + "]").hide();
                    $("[data-page=" + page + "]").show();
                } else {
                    var cache = $source.attr("data-cache");
                    var size = $source.attr("data-size");
                    var idx = $source.attr("data-idx");
                    var arr = explode("-", page);
                    var tpl = $("#" + arr[1]).html();
                    var foreach = $('<div>').append($("[data-wb-tpl=" + arr[1] + "]").clone());
                    $(foreach).find("[data-wb-tpl=" + arr[1] + "]").html("");
                    $(foreach).find("[data-wb-tpl=" + arr[1] + "]").attr("data-sort", sort);
                    $(foreach).find("[data-wb-tpl=" + arr[1] + "]").removeAttr("data-desc");
                    var loader = $(foreach).find("[data-wb-tpl=" + arr[1] + "]").attr("data-loader");
                    var offset = $(foreach).find("[data-wb-tpl=" + arr[1] + "]").attr("data-offset");
                    if (offset == undefined) {
                        offset = 130;
                    }
                    var foreach = $(foreach).html();
                    var param = {
                        tpl: tpl,
                        tplid: arr[1],
                        idx: idx,
                        page: arr[2],
                        size: size,
                        cache: cache,
                        foreach: foreach
                    };
                    var url = "/ajax/pagination/";
                    if ($("#" + id).data("find") !== undefined) {
                        var find = $("#" + id).data("find");
                    } else {
                        var find = $source.attr("data-find");
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
                        success: function (data) {
                            var data = JSON.parse(data);
                            $("[data-wb-tpl=" + arr[1] + "]").html(data.data);
                            if (data.pages > "1") {
                                $(".pagination[id=ajax-" + pid + "]").show();
                                var pid = $(data.pagr).attr("id");
                                $(document).undelegate(".pagination[id=" + pid + "] li a", "click");
                                $("#" + pid).after(data.pagr);
                                $("#" + pid + ":first").remove();
                            } else {
                                $(".pagination[id=ajax-" + arr[1] + "]").hide();
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
                        error: function (data) {
                            $("body").removeClass("cursor-wait");
                            if (loader == "" || loader == undefined) {} else {
                                var funcCall = loader + "(false);";
                                eval(funcCall);
                            }
                            (document).trigger("after-pagination-error", [id, page, arr[2]]);

                        }
                    });
                }
                $(this).parents("ul").find("li").removeClass("active");
                $(this).parent("li").addClass("active");

                var scrollTop = $("[data-wb-tpl=" + arr[1] + "]").offset().top - offset;
                if (scrollTop < 0) {
                    scrollTop = 0;
                }
                $('body,html').animate({
                    scrollTop: scrollTop
                }, 1000);

                //$(document).trigger("after_pagination_click",[id,page,arr[2]]);
            }
            event.preventDefault();
            return false;
        });
    });
}

function wb_call_source(id) {
    var eid = "#" + id;
    if (!$(eid).parents(".formDesignerEditor").length) {
        $(document).data("sourceFile", null);
        var form = $(eid).parents("form");
        var theme = getcookie("sourceEditorTheme");
        var fsize = getcookie("sourceEditorFsize") * 1;
        var source = "&nbsp;";
        var fldname = $(eid).attr("name");
        if ($("[name=" + fldname + "]").length) {
            source = $("[name=" + fldname + "]").val();
        }

        if (theme == undefined || theme == "") {
            var theme = "ace/theme/chrome";
            setcookie("sourceEditorTheme", theme);
        }
        if (fsize == undefined || fsize == "") {
            var fsize = 12;
            setcookie("sourceEditorFsize", fsize);
        }
        if ($(document).data("sourceClipboard") == undefined) {
            $(document).data("sourceClipboard", "");
        }
        $(form).data(eid, ace.edit(id));
        $(form).data(eid).setTheme("ace/theme/chrome");
        $(form).data(eid).setOptions({
            enableBasicAutocompletion: true,
            enableSnippets: true
        });
        $(form).data(eid).getSession().setUseWrapMode(true);
        $(form).data(eid).getSession().setUseSoftTabs(true);
        $(form).data(eid).setDisplayIndentGuides(true);
        $(form).data(eid).setHighlightActiveLine(false);
        $(form).data(eid).setAutoScrollEditorIntoView(true);
        $(form).data(eid).commands.addCommand({
            name: 'save',
            bindKey: {
                win: 'Ctrl-S',
                mac: 'Command-S'
            },
            exec: function () {
                console.log(form);
                //wb_formsave(form);
            },
            readOnly: false
        });
        $(form).data(eid).gotoLine(0, 0);
        $(form).data(eid).resize(true);
        if ($("#cke_text .cke_contents").length) {
            var ace_height = $("#cke_text .cke_contents").height();
        } else {
            var ace_height = 400;
        }
        $(".ace_editor").css("height", ace_height);
        $(form).data(eid).setTheme(theme);
        $(form).data(eid).setFontSize(fsize);
        $(form).data(eid).setValue(source);
        $(form).data(eid).gotoLine(0, 0);
        $(form).data(eid).getSession().setMode("ace/mode/php");
        wb_call_source_events(eid, fldname);
        return $(form).data(eid);
    }
}


function wb_call_source_events(eid, fldname) {

    var tmp = explode("-", eid);
    var toolbar = tmp[0] + "-toolbar-" + tmp[1] + " ";
    $(toolbar).data("editor", false);
    $(toolbar).next(".ace_editor").attr("name", fldname).attr("id", substr(eid, 1));
    var form = $(eid).parents("form");
    $(form).data(eid).getSession().on('change', function (e) {
        if ($(toolbar).data("editor") == undefined) {
            $(toolbar).data("editor", false);
        }
        if ($(toolbar).data("editor") == false && $(document).data("editor") !== true) {
            $(toolbar).data("editor", true);
            setTimeout(function () {
                $(document).trigger("sourceChange", {
                    "value": $(form).data(eid).getSession().getValue(),
                    "field": fldname,
                    "form": $(toolbar).parents("form")
                });
                $(toolbar).data("editor", false);
            }, 500);
        }
    });

    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        if ($(e.target.hash).find(eid).length) {
            var form = $(eid).parents("form");
            var val = $(form).data(eid).getSession().getValue();
            $(form).data(eid).getSession().setValue(val);
        }
    });


    $(document).on("editorChange", function (e, data) {
        $(document).data("editor", true);
        var eid = "#" + $(".ace_editor[name=" + data.field + "]").attr("id");
        var form = $(eid).parents("form");
        $(form).data(eid).getSession().setValue(data.value);
        setTimeout(function () {
            $(document).data("editor", false);
        }, 30);
    });




    $(document).undelegate(toolbar + " button", "click");
    $(document).delegate(toolbar + " button", "click", function (e) {
        var theme = getcookie("sourceEditorTheme");
        var fsize = getcookie("sourceEditorFsize");
        if (theme == undefined || theme == "") {
            var theme = "ace/theme/chrome";
            setcookie("sourceEditorTheme", theme);
        }
        if (fsize == undefined || fsize == "") {
            var fsize = 12;
            setcookie("sourceEditorFsize", fsize);
        }

        //if ($(this).hasClass("btnCopy")) 		{$(document).data("sourceFile",$(form).data(eid).getCopyText());}
        //if ($(this).hasClass("btnPaste")) 		{$(form).data(eid).insert($(document).data("sourceFile"));}
        if ($(this).hasClass("btnCopy")) {
            $(document).data("sourceClipboard", $(form).data(eid).getCopyText());
        }
        if ($(this).hasClass("btnPaste")) {
            $(form).data(eid).insert($(document).data("sourceClipboard"));
        }
        if ($(this).hasClass("btnUndo")) {
            $(form).data(eid).execCommand("undo");
        }
        if ($(this).hasClass("btnRedo")) {
            $(form).data(eid).execCommand("redo");
        }
        if ($(this).hasClass("btnFind")) {
            $(form).data(eid).execCommand("find");
        }
        if ($(this).hasClass("btnReplace")) {
            $(form).data(eid).execCommand("replace");
        }
        if ($(this).hasClass("btnLight")) {
            $(form).data(eid).setTheme("ace/theme/chrome");
            setcookie("sourceEditorTheme", "ace/theme/chrome");
        }
        if ($(this).hasClass("btnDark")) {
            $(form).data(eid).setTheme("ace/theme/monokai");
            setcookie("sourceEditorTheme", "ace/theme/monokai");
        }
        if ($(this).hasClass("btnClose")) {
            $(form).data(eid).setValue("");
            $(document).data("sourceFile", null);
            $("#sourceEditorToolbar .btnSave").removeClass("btn-danger");
        }
        if ($(this).hasClass("btnFontDn")) {
            if (fsize > 8) {
                fsize = fsize * 1 - 1;
            }
            $(form).data(eid).setFontSize(fsize);
            setcookie("sourceEditorFsize", fsize);
        }
        if ($(this).hasClass("btnFontUp")) {
            if (fsize < 20) {
                fsize = fsize * 1 + 1;
            }
            $(form).data(eid).setFontSize(fsize);
            setcookie("sourceEditorFsize", fsize);
        }
        if ($(this).hasClass("btnFullScr")) {
            var div = $(this).parents(toolbar).parent();
            var offset = div.offset();
            if (!div.hasClass("fullscr")) {
                div.parents(".modal").addClass("fullscr");
                div.addClass("fullscr");
                $(this).parents(".modal").css("overflow-y", "hidden");
                $("pre.ace_editor").css("height", $(window).height() - $(toolbar).height() - $(toolbar).next(".nav").height() - 15);
            } else {
                div.removeAttr("style");
                $("pre.ace_editor").css("height", "500px");
                div.removeClass("fullscr");
                div.parents(".modal").removeClass("fullscr");
                $(this).parents(".modal").css("overflow-y", "auto");
            }
            window.dispatchEvent(new Event('resize'));
        }
        if ($(this).hasClass("btnSave")) {
            var fo = $(this).parents(".modal").find("[data-wb-formsave]").trigger("click");

            //wb_formsave(fo);
        }
        e.preventDefault();
        return false;
    });
}



function is_callable(v, syntax_only, callable_name) {
    // Returns true if var is callable.    
    //   
    // version: 902.821  
    // discuss at: http://phpjs.org/functions/is_callable  
    // +   original by: Brett Zamir  
    // %        note 1: The variable callable_name cannot work as a string variable passed by reference as in PHP (since JavaScript does not support passing strings by reference), but instead will take the name of a global variable and set that instead  
    // %        note 2: When used on an object, depends on a constructor property being kept on the object prototype  
    // *     example 1: is_callable('is_callable');  
    // *     returns 1: true  
    // *     example 2: is_callable('bogusFunction', true);  
    // *     returns 2:true // gives true because does not do strict checking  
    // *     example 3: function SomeClass () {}  
    // *     example 3: SomeClass.prototype.someMethod = function(){};  
    // *     example 3: var testObj = new SomeClass();  
    // *     example 3: is_callable([testObj, 'someMethod'], true, 'myVar');  
    // *     example 3: alert(myVar); // 'SomeClass::someMethod'  
    var name = '',
        obj = {},
        method = '';
    if (typeof v === 'string') {
        obj = window;
        method = v;
        name = v;
    } else if (v instanceof Array && v.length === 2 && typeof v[0] === 'object' && typeof v[1] === 'string') {
        obj = v[0];
        method = v[1];
        name = (obj.constructor && obj.constructor.name) + '::' + method;
    } else {
        return false;
    }
    if (syntax_only || typeof obj[method] === 'function') {
        if (callable_name) {
            window[callable_name] = name;
        }
        return true;
    }
    return false;
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

jQuery.fn.outerHTML = function (s) {
    return s ?
        this.before(s).remove() :
        jQuery("<p>").append(this.eq(0).clone()).html();
};
