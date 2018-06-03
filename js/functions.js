"use strict";
var wbapp= new Object();
wbapp.settings = wb_settings();
if ($("[data-wb-role=cart]").length) {wbapp.cart = wb_getcart();}

function wb_delegates() {
    wb_ajax();
    wb_pagination();
    wb_formsave();
    wb_base_fix();
    wb_plugins();
    wb_multiinput();
    wb_tree();
    wb_cart();
}

function wb_settings() {
    var settings = null;
    var defer = $.ajax({
        async: false
        , type: 'GET'
        , url: "/ajax/settings/"
        , success: function (data) {
            settings = $.parseJSON(base64_decode(data));
            return data;
        }
    });
    return settings;
    
}

function wb_getcart() {
    var cart = null;
    var defer = $.ajax({
        async: false
        , type: 'GET'
        , url: "/ajax/cart/getdata"
        , success: function (data) {
            cart = $.parseJSON(data);
            return data;
        }
    });
    return cart;   
}

function wb_merchant_modal() {        
        var merchant = wbapp.settings.merchant;
        $.ajax({
            async: false
            , type: "POST"
            , url: "/module/"+merchant
            , success: function(data) {
                if ( $(document).find("#"+merchant+"Modal").length) {
                    $(document).find("#"+merchant+"Modal").replaceWith($(data).find("#"+merchant+"Modal"));
                } else {
                    $(document).find("body").append($(data).find("#"+merchant+"Modal"));
                }
            }
        });
        $("#"+merchant+"Modal").modal("show");
}

function wb_cart() {
    $(document).unbind("cart-recalc");
    $(document).unbind("cart-clear");
    $(document).unbind("cart-item-recalc");
    $(document).unbind("cart-item-plus");
    $(document).unbind("cart-item-minus");
    $(document).unbind("cart-item-remove");
    $(document).unbind("cart-total-recalc");
    $("[data-wb-role=cart]").find("input,select,textarea").unbind("change");
    $("[data-wb-role=cart] .cart-item").find("*").unbind("click");
    $("[data-wb-role=cart] .cart-clear").unbind("click");
    $("[data-wb-role=cart] .add-to-art").unbind("click");
    $(document).undelegate("form[data-wb-role=cart] .add-to-cart", "click");
    $(document).delegate("form[data-wb-role=cart] .add-to-cart", "click", function () {
        $(this).trigger("cart-add-click");
        var that = $(this);
        var form = $(this).parents("form[data-wb-role=cart]").serialize();
        var ajax = $(this).parents("form[data-wb-role=cart]").attr("data-wb-ajax");
        if (ajax == undefined || ajax == "") {
            var ajax = "/ajax/cart/";
        }
        if ($(this).hasClass("add-to-cart")) {
            ajax += "add-to-cart";
        }
        $.get(ajax, form, function (data) {
            that.trigger("add-to-cart-done", [getcookie("order_id")]);
            if ($("[data-wb-role=cart][data-wb-tpl]").length) {
                wb_setdata("[data-wb-role=cart][data-wb-tpl]", {});
            }
            $(document).trigger("cart-total-recalc");
            $(document).trigger("cart-add-done",data);
        });
        return false;
    });
    
    $(document).undelegate(".cart-clear", "click");
    $(document).delegate(".cart-clear", "click", function () {
        $(this).trigger("cart-clear", [this]);
        return false;
    });
    $(document).off("cart-recalc");
    $(document).on("cart-recalc", function (event, flag) {
        $("[data-wb-role=cart] .cart-item").each(function () {
            $(this).trigger("cart-item-recalc", [this, flag]);
        });
        $(document).trigger("cart-total-recalc");
    });
    $(document).off("cart-clear");
    $(document).on("cart-clear", function (event) {
        var ajax = "/ajax/cart/cart-clear";
        $.get(ajax, function (data) {
            $("[data-wb-role=cart] .cart-item").remove();
            $(document).trigger("cart-total-recalc");
            $(document).trigger("cart-after-clear", [event]);
        });
    });
    $(document).off("cart-item-recalc");
    $(document).on("cart-item-recalc", function (event, item, flag) {
        var index = 1;
        var total=0;
        var idx = $(item).attr("idx");
        var arr=wb_cart_item(item);
        if (arr.price!==undefined && arr.count!==undefined) {total=arr.price*arr.count;}

        if ($("[data-wb-role=cart] .cart-item[idx=" + idx + "] .cart-item-total").is(":input")) {
            $("[data-wb-role=cart] .cart-item[idx=" + idx + "] .cart-item-total").val(total);
        }
        else {
            $("[data-wb-role=cart] .cart-item[idx=" + idx + "] .cart-item-total").html(total);
        }
        $(document).on("cart-item-recalc-done");
    });
    $(document).off("cart-item-remove");
    $(document).on("cart-item-remove", function (event, item, flag) {
        var idx = $(item).attr("idx");
        var ajax = $(this).parents("[data-wb-role=cart]:not(form):first").attr("data-wb-ajax");
        $("[data-wb-role=cart] .cart-item[idx=" + idx + "]").remove();
        $("[data-wb-role=cart]").each(function () {
            $(this).find(".cart-item").each(function (i) {
                $(this).attr("idx", i);
            });
        });
        $(document).trigger("cart-total-recalc");
        $(document).trigger("cart-item-remove-done");
    });
    $(document).off("cart-item-plus");
    $(document).on("cart-item-plus", function (event, item, flag) {
        var idx = $(item).attr("idx");
        var quants = $(this).parents("[data-wb-role=cart] .cart-item[idx=" + idx + "] .cart-item-count");
        var max = 1000;
        quants.each(function () {
            if ($(this).is("input") || $(this).is("select")) {
                if ($(this).val() < max) {
                    $(this).val($(this).val() * 1 + 1);
                }
            }
            else {
                if ($(this).text() * 1 < max) {
                    $(this).html($(this).text() * 1 + 1);
                }
            }
        });
        $(document).trigger("cart-item-recalc", item);
        $(document).trigger("cart-total-recalc");
    });
    $(document).off("cart-item-minus");
    $(document).on("cart-item-minus", function (event, item, flag) {
        var idx = $(item).attr("idx");
        var quants = $(this).parents("[data-wb-role=cart] .cart-item[idx=" + idx + "] .cart-item-count");
        var ajax = $(this).parents("[data-wb-role=cart]").attr("data-wb-ajax");
        var min = 1;
        quants.each(function () {
            if ($(this).is("input") || $(this).is("select")) {
                if ($(this).val() > min) {
                    $(this).val($(this).val() * 1 - 1);
                }
            }
            else {
                if ($(this).text() * 1 > min) {
                    $(this).html($(this).text() * 1 - 1);
                }
            }
        });
        $(document).trigger("cart-item-recalc", item);
        $(document).trigger("cart-total-recalc");
    });
    $(document).off("cart-total-recalc");
    $(document).on("cart-total-recalc", function (event, item, flag) {
        $("[data-wb-role=cart]:not(form)").each(function(){
                var total = 0;
                var lines = 0;
                var count = 0;
                $(this).find(".cart-item").each(function () {
                $(document).trigger("cart-item-recalc", $(this));
                if ($(this).find(".cart-item-total").is(":input")) {
                    total = total + $(this).find(".cart-item-total").val() * 1;
                }
                else {
                    total = total + $(this).find(".cart-item-total").text() * 1;
                }
                if ($(this).find(".cart-item-count").is(":input")) {
                    count = count + $(this).find(".cart-item-count").val() * 1;
                }
                else {
                    count = count + $(this).find(".cart-item-count").text() * 1;
                }
                lines++;
            });
            $(this).find(".cart-count").text(count);
            $(this).find(".cart-total").text(total);
            $(this).find(".cart-lines").text(lines);
            if ($(this).attr("data-wb-writable")=="true") {$(document).trigger("cart-update");}
        });
        $(document).trigger("cart-recalc-done");
    });
    $(document).off("cart-update");
    $(document).on("cart-update", function (event) {
        console.log("cart-update");
        var cart=$(document).find("[data-wb-role=cart][data-wb-writable=true]:first");
        var ajax = $(cart).attr("data-wb-ajax");
        var form={};
        $(cart).find(".cart-item").each(function(i){
            form[i]=wb_cart_item(this);
             
        });
        if (ajax == undefined || ajax == "") {
            var ajax = "/ajax/cart/cart-update";
        }
        var diff = $.post(ajax, form);
        $(document).trigger("cart-update-done");
    });
    
    $("[data-wb-role=cart]").find("input,select,textarea").off("change");
    $("[data-wb-role=cart]").find("input,select,textarea").on("change", function () {
        var item = $(this).parents(".cart-item");
        $(document).trigger("cart-item-recalc", item);
        $(document).trigger("cart-total-recalc");
    });
    $(document).undelegate("[data-wb-role=cart] .cart-item *", "click");
    $(document).delegate("[data-wb-role=cart] .cart-item *", "click", function () {
        var item = $(this).parents(".cart-item");
        if ($(this).hasClass("cart-item-remove")) {
            $(document).trigger("cart-item-remove", item);
        }
        if ($(this).hasClass("cart-item-plus")) {
            $(document).trigger("cart-item-plus", item);
        }
        if ($(this).hasClass("cart-item-minus")) {
            $(document).trigger("cart-item-minus", item);
        }
    });
    $(document).trigger("cart-recalc", ["noajax"]);
};

function wb_cart_item(item) {
        var arr={};
        var fld = new Array("id","form","count", "price");
        var add = $(this).parents("[data-wb-role=cart]").attr("data-wb-update");
        // можно передать список полей, участвующих в пересчёте
        if (add !== undefined && add !== "") {
            fld=fld+explode(",", add);
        }
        for (var i in fld) {
            var fldname = (fld[i]).trim();
            var field = $(item).find(".cart-item-" + fldname);
            if (field.is(":input")) {
                var value = field.val();
            }
            else {
                var value = field.text();
            }
            if (is_numeric(value*1)) {value=value*1;}
            if (fldname=="id") fldname="item";
            arr[fldname]=value;
        };
        $(item).find("[name]:input").each(function(){
            arr[$(this).attr("name")]=$(this).val();
        });
    return arr;
}

function wb_alive() {
    if ($("body").attr("data-wb-alive") == "true") {
        var post = "wb_get_user_role";
        setInterval(function () {
            $.post("/ajax/alive", {
                data: post
            }, function (ret) {
                ret = json_decode(ret, true);
                if (ret == false) {
                    post = "wb_session_die";
                }
                if (ret["mode"] !== undefined && ret["mode"] == "wb_session_die") {
                    $(".modal#wb_session_die").modal("show");
                    console.log("session_die");
                }
                if (ret["mode"] !== undefined && ret["mode"] == "wb_set_user_role" && ret["user_role"] !== undefined && ret["user_role"] > "") {
                    if (!$(".modal#wb_session_die").length) {
                        $("body").append(ret.msg);
                    }
                    $(document).data("user_role", ret["user_role"]);
                    post = $(document).data("user_role");
                }
                //if (ret==false && document.location.pathname=="/admin") {document.location.href="/login";}
            });
        }, 60000);
    }
}

function wb_get_cdata(text) {
    text = text.replace("<![CDATA[", "").replace("]]>", "");
    return text;
}

function wb_tree() {
    if ($(document).data("wb-tree-rowmenu") == undefined && $(".wb-tree").length) {
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
    }
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
        var w = e;
        var relativeX = (w.clientX - 10);
        var relativeY = (w.clientY - 10);
        $(".wb-tree-item").find(".wb-tree-menu").css("left", relativeX + "px").css("top", relativeY + "px");
        $(".wb-tree-item").find(".wb-tree-menu [data-toggle=dropdown]").trigger("click");
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
        $(tree).find(".wb-tree-item").removeClass("wb-tree-current");
        $(that).addClass("wb-tree-current");
        if (form == undefined) {
            form = "tree";
        }
        var formitem = $(tree).parents("[data-wb-form]").attr("data-wb-item");
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
        $(that).data("path",path);
        if (dict == undefined || dict == "" || trim(dict) == " ") {
            var dict = [];
        }
        else {
            var dict = $.parseJSON(dict);
        }
        if (trim(data["data"]) == "") {
            data["data"] = {};
        }
        data["_form"] = data["data"]["_form"] = form;
        data["_item"] = data["data"]["_item"] = formitem;
        data["_id"] = data["data"]["_id"] = formitem;
        var dataval = data["data"];

        var tpl = wb_tree_data_fields(dict, dataval);
        //var tpl = $(wb_setdata(tpl, dataval, true));
        $(tpl).find(".wb-uploader").attr("data-wb-path", "/uploads/" + form + "/" + formitem);
        data["fields"] = dict;
        data["name"] = name;
        data["data-name"] = text;
        data["form"] = form;
        data["data-id"] = item;
        //$(".content-box .tree-edit.modal").remove();
        $("tester").remove();
        edit = $(wb_setdata(edit, data, true));
        edit.find(".modal").attr("id", "tree_" + form + "_" + name);
        edit.find(".modal .wb-uploader").attr("data-wb-path", "/uploads/" + form + "/" + formitem);
        if ($("#tree_edit .tree-edit").length && $("#tree_edit .tree-edit").is(":visible")) {
            edid="#tree_edit .tree-edit";
            $("#tree_edit .tree-edit > div").html($(edit).find(".modal-body").html());
            $("#tree_edit .tree-edit > div #treeData form").html(tpl);
        } else {
            $(".content-box").append(edit);
            $(".content-box").find(".modal #treeData form").html(tpl);
            //$(edid).after("<div class='modal-backdrop show fade'></div>");
            var zi = 1050;
            if ($(".modal:visible").length) {
                $(".modal:visible").each(function () {
                    if (zi < $(this).css("z-index")) {
                        zi = $(this).css("z-index");
                    }
                });
                zi += 2;
            }
            $(edid).css("z-index", zi);
            //$(edid).next(".modal-backdrop").css("z-index", zi - 1);
            $(edid).modal();
            
            $(document).click(function(e){
               if (!$(e.target).parents(".tree-edit").length 
                   && !$(e.target).parents(".wb-tree").length 
                   && !$(e.target).parents(".dropdown-item").length
                   && !$(e.target).is(".dropdown-item")
                   && !$(e.target).parents(".cke_reset_all").length
                   && !$(e.target).parents(".cke_screen_reader_only").length
                   && !$(e.target).is(".cke_dialog_background_cover")
                  )  {
                    tree_branch_change();
                    $(edid).modal("hide");
               }
            });
            
        }
        $(edid).data("path", path);
        $(edid).data("tree", tree);
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
                var tree=$(edid).data("tree");
                var tpl = wb_tree_dict_change(fields, tree);
                tpl = $(wb_setdata(tpl, dataval, true));
                $(edid).find("#treeData").children("form").html(tpl);
                wb_multiinput();
                wb_plugins();
            }
        });
        

        $(edid).find('.modal-footer button').off("click");
        $(edid).find('.modal-footer button').on("click", function (e) {
            if ($(this).hasClass("tree-close")) {
                tree_branch_change();
            }
            $(edid).modal("hide");
            $(edid).next(".modal-backdrop").remove();
            setTimeout(function () {
                $(edid).remove();
            }, 500)
        });
        
        
        function tree_branch_change() {
            if ($(edid).find("form:first").length) {
                var data = $(edid).find("form:first").serializeArray();
                $(data).each(function (i, d) {
                    $(that).attr(d.name, d.value);
                    if (d.name == "data-name") {
                        $(that).children(".dd-content").val(d.value);
                    }
                    if (d.name == "data-id") {
                        $(that).children(".dd3-btn").children("span").html(d.value);
                    }
                });
                var cdata = JSON.stringify($(edid).find("#treeData > form").serializeArray());
                wb_tree_data_set(that, $(that).data("path"), cdata);
                $(tree).find("input[name='" + name + "']").val(JSON.stringify(wb_tree_serialize($(tree).children(".dd-list"))));
            }
        };

        $(edid).undelegate("form *","change");
        $(edid).delegate("form *","change",function(){
            tree_branch_change();
        });
        
        
        $(document).on("wb_before_formsave",function(){
            if ($(edid).length) {
                that=$(tree).find(".wb-tree-current");
                tree_branch_change();
            }
        });
        
        wb_multiinput();
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
            "name": ""
            , "form": form
            , "id": wb_newid()
        }, true);
        var name = $(tree).attr("name");
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
    var trid = json_decode($(tree).children("[name]").val(), true);
    var trid = trid[0]["id"];
    $(tree).children("[data-name=dict]").val(JSON.stringify(dict));
    var res = wb_tree_data_fields(dict, {
        _form: "tree"
        , _item: trid
    });
    return res;
}

function wb_tree_data_fields(dictdata, datadata) {
    var res = false;
    var dict = {};
    if (datadata == undefined) {
        var data = [];
    }
    else {
        var data = datadata;
    }
    $(dictdata).each(function (i) {
        dict[i] = dictdata[i];
    });
    $.ajax({
        async: false
        , type: 'POST'
        , url: "/ajax/buildfields/"
        , data: {
            dict: dict
            , data: data
        }
        , success: function (data) {
            res = data;
        }
        , error: function (data) {
            res = "Ошибка!";
        }
    });
    return res;
}

function wb_ajax_loader() {
    if (is_callable("ajax_loader")) {
        ajax_loader();
    }
    else {
        $("body").addClass("cursor-wait");
    }
}

function wb_ajax_loader_done() {
    if (is_callable("ajax_loader_done")) {
        ajax_loader_done();
    }
    else {
        $("body").removeClass("cursor-wait");
    }
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
    }
    else {
        data["data"] = [];
    }
    if (path == undefined) {
        var path = wb_tree_data_path(that);
    }
    $(path).each(function (i, j) {
        if (i == 0) {
            data = data[j];
        }
        else {
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
    }
    else if (Object.prototype.toString.call(input) === '[object Object]') {
        return input;
    }
    try {
        return JSON.parse(input);
    }
    catch (e) {
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
    }
    else {
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
            if (strpos(fldname,"[")) {
                var pos=strpos(fldname,"[");
                var sub=substr(fldname,pos);
                var fldn="values['"+substr(fldname,0,pos)+"']";
                
                var myRe = /\[(.*?)\]/g;
                var cur=1;
                var myArray=[];
                eval("if ("+fldn+"==undefined) {"+fldn+"={};};");
                while ((myArray = myRe.exec(sub)) != null) {
                    eval("fldn+=\"['"+myArray[1]+"']\";");
                    if (cur<myArray.length) {
                         eval("if ("+fldn+"==undefined) {"+fldn+"={};};");
                    } else {
                        eval(fldn+"=fldval;");
                    }
                    cur++;
                }
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
        }
        else {
            p += "['children'][" + j + "]";
        }
    });
    eval("data" + p + "['data']=values;");
    $(that).children(".data").html("<![CDATA[" + JSON.stringify(values) + "]]>");
    data = JSON.stringify(data);
    $(tree).children("input[name=" + name + "]").val(data);
    $(tree).find("textarea.source:not(.wb-done)").each(function () {
        wb_call_source($(this).attr("id"));
    });
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
    }
    else {
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
        }
        else {
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
        }
        else {
            var child = false;
            var open = false;
        }
        if (count(child) == 0) {
            child = false;
            open = false;
        }
        tree_data.push({
            id: id
            , name: name
            , open: open
            , data: flds
            , children: child
        });
    });
    return tree_data;
}

function wb_newid() {
    var newid = "";
    $.ajax({
        async: false
        , type: 'GET'
        , url: "/ajax/newid/"
        , success: function (data) {
            newid = JSON.parse(data);
        }
        , error: function (data) {
            newid = $(document).uniqueId();
        }
    });
    return newid;
}

function wb_multiinput() {
    if ($("[data-wb-role=multiinput]").length && $(document).data("wb-multiinput-menu") == undefined) {
        $.get("/ajax/getform/common/multiinput_menu/", function (data) {
            $(document).data("wb-multiinput-menu", data);
        });
        $.get("/ajax/getform/common/multiinput_row/", function (data) {
            $(document).data("wb-multiinput-row", data);
        });
    }
    if ($("[data-wb-role=multiinput]").length) {
        $("[data-wb-role=multiinput]").sortable();
    }
    $(document).undelegate(".wb-multiinput", "mouseenter");
    $(document).delegate(".wb-multiinput", "mouseenter", function () {
        $(document).data("wb-multiinput", this);
    });
    $(document).undelegate(".wb-multiinput", "mouseleave");
    $(document).delegate(".wb-multiinput", "mouseleave", function () {
        //    $("body").find(".wb-multiinput-menu").remove();
    });
    $(document).undelegate(".wb-multiinput", "contextmenu");
    $(document).delegate(".wb-multiinput", "contextmenu", function (e) {
        $("body").find(".wb-multiinput-menu").remove();
        $("body").append("<div class='wb-multiinput-menu'>" + $(document).data("wb-multiinput-menu") + "</div>");
        var relativeX = (e.clientX - 10);
        var relativeY = (e.clientY - 10);
        $("body").find(".wb-multiinput-menu").css("left", relativeX + "px").css("top", relativeY + "px");
        $("body").find(".wb-multiinput-menu [data-toggle=dropdown]").trigger("click");
        return false;
    });
    $(document).undelegate(".wb-multiinput-menu .dropdown-item", "click");
    $(document).delegate(".wb-multiinput-menu .dropdown-item", "click", function (e) {
        var line = $(document).data("wb-multiinput");
        var multi = $(line).parents("[data-wb-role=multiinput]");
        var tpl = $($(multi).attr("data-wb-tpl")).html();
        var row = $(document).data("wb-multiinput-row");
        var name = $(multi).attr("name");
        row = str_replace("{{template}}", tpl, row);
        row = wb_setdata(row, {
            "form": "procucts"
            , "id": "_new"
        }, true);
        if ($(this).attr("href") == "#after") {
            $(line).after(row);
        }
        if ($(this).attr("href") == "#before") {
            $(line).before(row);
        }
        if ($(this).attr("href") == "#remove") {
            console.log("Trigger: before_remove");
            $(multi).trigger("before_remove", line);
            $(line).remove();
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
            }
            else {
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
        if (wb_plugins_loaded()) {
            autosize($('textarea[rows=auto]'));
            if ($('[data-toggle="tooltip"]').length) {
                $('[data-toggle="tooltip"]').tooltip();
            }
            if ($('[data-toggle="popover"]').length) {
                $('[data-toggle="popover"]').each(function(){
                    if ($(this).attr("data-html")!==undefined) {
                        var content=$($(this).attr("data-html")).html();
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
            $("a[href$='.jpeg'],a[href$='.jpg'],a[href$='.png'],a[href$='.gif']").each(function(){
                if ($(this).attr("data-fancybox")==undefined) {
                    if ($(this).parents("[data-wb-tpl]").length) {
                        if ($(this).parents("[data-wb-tpl]").attr("data-wb-tpl")!=="false") {
                            $(this).attr("data-fancybox",$(this).parents("[data-wb-tpl]").attr("data-wb-tpl"));
                        } 
                    } else {
                        $(this).attr("data-fancybox","gallery");
                    }
                }
                if ($(this).parents(".modal").length) {
                    $(this).attr("data-fancybox",$(this).parents(".modal").attr("id")+"-"+$(this).attr("data-fancybox"));
                    
                }
            });
        }
        if ($("script[src*=datetimepicker]").length) {
            $("[type=datepicker]:not(.wb-plugin)").each(function () {
                if ($(this).attr("data-date-format") == undefined) {
                    $(this).attr("data-date-format", "dd.mm.yyyy"); // Plugin Format
                }
                else {
                    $(this).attr("data-date-format", "dd.mm.yyyy");
                }
                if ($(this).val() > "") {
                    $(this).val(wb_oconv_object(this));
                }
                $(this).addClass("wb-plugin");
                var lang = "ru";
                if ($(this).attr("data-wb-lang") !== undefined) {
                    lang = $(this).attr("data-wb-lang");
                }
                $(this).datetimepicker({
                    language: lang
                    , autoclose: true
                    , todayBtn: true
                    , minView: 2
                }).on('changeDate', function (ev) {
                    $(this).attr("value", wb_iconv_object(this));
                });
            });
            $("[type=datetimepicker]:not(.wb-plugin)").each(function () {
                if ($(this).attr("data-date-format") == undefined) {
                    $(this).attr("data-date-format", "dd.mm.yyyy hh:ii"); // Plugin Format
                }
                else {
                    $(this).attr("data-date-format", "dd.mm.yyyy hh:ii");
                }
                if ($(this).val() > "") {
                    $(this).val(wb_oconv_object(this));
                }
                $(this).addClass("wb-plugin");
                var lang = "ru";
                if ($(this).attr("data-wb-lang") !== undefined) {
                    lang = $(this).attr("data-wb-lang");
                }
                $(this).datetimepicker({
                    language: lang
                    , autoclose: true
                    , todayBtn: true
                }).on('changeDate', function (ev) {
                    $(this).attr("value", wb_iconv_object(this));
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
        if (wb_plugins_loaded() && $('.select2:not(.wb-plugin)').length) {
            $('.select2:not(.wb-plugin').each(function() {
                var that = this;
                if ($(this).is("[data-wb-ajax]")) {
                    var url = $(this).attr("data-wb-ajax");
                    var tpl = $("#" + $(this).attr("data-wb-tpl")).html();
                    var where = $(this).attr("data-wb-where");
                    var val = $(this).attr("value");
                    var plh = $(this).attr("placeholder");
                    var min = $(this).attr("min"); if (min==undefined) {min=1;}
                    if (plh == undefined) {
                        plh = "Поиск...";
                    }
                    $(this).select2({
                        language: "ru"
                        , placeholder: plh
                        , minimumInputLength: min
                        , ajax: {
                            url: url
                            , async: true
                            , method: "post"
                            , dataType: 'json'
                            , data: function (term, page) {
                                return {
                                    value: term.term
                                    , page: page
                                    , where: where
                                    , tpl: tpl
                                };
                            }
                            , processResults: function (data) {
                                $(that).data("wb-ajax-data", data);
                                $(that).trigger("wb_ajax_done", [that, url, data]);
                                $(that).data("item", data);
                                return {
                                    results: data
                                };
                            }
                        , }
                    , });
                    $.ajax({
                        url: url
                        , async: true
                        , method: "post"
                        , data: {
                            id: val
                            , tpl: tpl
                        }
                        , success: function (data) {
                            var option = new Option(data.text, data.id, true, true);
                            $(that).append(option).trigger('change');
                            $(document).data("item", data.item);
                            $(that).trigger({
                                type: 'select2:select'
                                , params: {
                                    data: data
                                }
                            });
                        }
                    });
                    $(that).off("change");
                    $(that).on("change", function () {
                        if ($(that).val() > "") {
                            $($(that).data("item")).each(function (i, item) {
                                if (item.id == $(that).val()) {
                                    $(that).data("item", item.item);
                                    return;
                                }
                            });
                        }
                    });
                }
                else {
                    $(this).select2();
                }
            });
            $('.select2').addClass("wb-plugin");
        }
        if (wb_plugins_loaded() && $('.input-tags').length) {
            $('.input-tags').each(function () {
                if ($(this).attr("placeholder") !== undefined) {
                    var ph = $(this).attr("placeholder");
                }
                else {
                    var ph = 'новый';
                }
                if ($(this).attr("height") !== undefined) {
                    var h = $(this).attr("height");
                }
                else {
                    var h = 'auto';
                }
                if ($(this).attr("width") !== undefined) {
                    var w = $(this).attr("width");
                }
                else {
                    var w = 'auto';
                }
                $(this).tagsInput({
                    width: w
                    , height: h
                    , 'defaultText': ph
                });
            });
        }
        if (wb_plugins_loaded() && $('.rating').length) {
            $('.rating:not(.wb-plugin)').each(function () {
                $(this).addClass("wb-plugin");
                $(this).rating({
                    filled: 'fa fa-star'
                    , empty: 'fa fa-star-o'
                });
            });
        }
        if (wb_plugins_loaded()) {
            if ($("input[type=phone]").length) {
                $("input[type=phone]").mask("+7 (999) 999-99-99");
            }
            if ($("input[type=tel]").length) {
                $("input[type=tel]").mask("+7 (999) 999-99-99");
            }
            if ($("input[data-wb-mask]").length) {
                $("input[data-wb-mask]").each(function () {
                    $(this).attr("type", "text");
                    $(this).mask($(this).attr("data-wb-mask"));
                });
            }
        }
        if (is_callable("wb_plugin_editor")) wb_plugin_editor();
        if (is_callable("wbCommonUploader")) wbCommonUploader();
    });
}

function wb_plugins_loaded() {
    return $("script[src='/engine/js/plugins/plugins.js']").length;
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
            //CKEDITOR.config.skin = 'bootstrapck';
            CKEDITOR.config.allowedContent = true;
            CKEDITOR.config.forceEnterMode = true;
            CKEDITOR.plugins.registered['save'] = {
                init: function (editor) {
                    var command = editor.addCommand('save', {
                        modes: {
                            wysiwyg: 1
                            , source: 1
                        }
                        , exec: function (editor) {
                            var fo = editor.element.$.form;
                            editor.updateElement();
                            wb_formsave($(fo));
                        }
                    });
                    editor.ui.addButton('Save', {
                        label: 'Сохранить'
                        , command: 'save'
                    });
                }
            }
        });
        for (var i in CKEDITOR.instances) {
            // это работает
            CKEDITOR.instances[i].on('change', function (e) {
                CKEDITOR.instances[i].updateElement();
                var instance = CKEDITOR.instances[i].name;
                var fldname = $("textarea#" + instance).attr("name");
                var value = CKEDITOR.instances[i].getData();
                if (fldname > "" && $("textarea#" + instance).parents("form").find("[name='" + fldname + "']").length) {
                    $("textarea#" + instance).parents("form").find("[name='" + fldname + "']:not(.ace_editor,.editor)").val(value);
                }
                else {
                    $("textarea#" + instance).val(value);
                }
                $("textarea#" + instance).html(value);
                $("textarea#" + instance).trigger("change");
                $(document).trigger("editorChange", {
                     "value": value
                    ,"field": fldname
                });
            });
        }
    }
}
$(document).on("sourceChange", function (e, data) {
    $(document).data("sourceChange", true);
    var form = data.form;
    var field = data.field;
    var value = data.value;
    if (CKEDITOR.instances[$("[name='" + field + "']").attr("id")] !== undefined) {
        CKEDITOR.instances[$("[name='" + field + "']").attr("id")].setData(value);
    }
    if ($("[name='" + field + "']:not('.ace_editor')").is("textarea")) {$("[name='" + field + "']:not('.ace_editor')").html(value);} else {$("[name='" + field + "']:not('.ace_editor')").val(value);}
    $("[name='" + field + "']:not('.ace_editor')").trigger("change");
    setTimeout(function () {
        $(document).data("sourceChange", false);
    }, 250);
    e.preventDefault();
    return false;
});

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
        //$(this).find("span.glyphicon").removeClass("loader glyphicon-save").addClass("glyphicon-ok");
        if (save) {
            return save;
        }
        else {
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
        if (type == "date" || type == "datepicker") {
            var mask = "Y-m-d";
        }
        if (type == "datetime" || type == "datetimepicker") {
            var mask = "Y-m-d H:i";
        }
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
        }
        else {
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
        var name = formObj.attr("data-wb-form");
        var item = formObj.attr("data-wb-item");
        var oldi = formObj.attr("data-wb-item-old");
        $(document).trigger("wb_before_formsave", [name, item, form, true]);
        console.log("call: wb_before_formsave");
        $(document).trigger(name + "_before_formsave", [name, item, form, true]);
        console.log("call: " + name + "_before_formsave");
        var ptpl = formObj.attr("parent-template");
        var padd = formObj.attr("data-wb-add");
        // обработка switch и checkbox
        var ui_switch = "";
        formObj.find("input[type=checkbox]:not(.bs-switch)").each(function () {
            var swname = $(this).attr("name");
            if ($(this).prop("checked") == true) {
                ui_switch += "&" + swname + "=on";
            }
            else {
                ui_switch += "&" + swname + "=";
            }
        });
        if (formObj.find("input[name=id]").length && formObj.find("input[name=id]").val() > "") {
            var item_id = formObj.find("input[name=id]").val();
        }
        else {
            var item_id = formObj.attr("data-wb-item");
            if (item_id == "_new") {
                item_id = wb_newid();
                formObj.find("input[name=id]").val(item_id)
            }
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
                }
                else {
                    $(this).prop("disabled");
                }
            });
            var form = formObj.serialize();
            cart.find("input,select,textarea").each(function () {
                if (!$(this).hasClass("tmpDisabled")) {
                    $(this).removeAttr("disabled");
                }
            });
        }
        else {
            var form = formObj.serialize();
        }
        form += ui_switch + ic_date;
        if ($(this).attr("data-wb-form") !== undefined) {
            name = $(this).attr("data-wb-form");
        }
        if ($(this).attr("data-wb-src") !== undefined) {
            var src = $(this).attr("data-wb-src");
        }
        else {
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
        }
        else {
            setup = false;
        }
        if (name !== undefined) {
            var data = {
                mode: "save"
                , form: name
            };
            $.ajax({
                type: 'POST'
                , url: src
                , data: form
                , success: function (data) {
                    if ($.bootstrapGrowl) {
                        $.bootstrapGrowl("Сохранено!", {
                            ele: 'body'
                            , type: 'success'
                            , offset: {
                                from: 'top'
                                , amount: 20
                            }
                            , align: 'right'
                            , width: "auto"
                            , delay: 4000
                            , allow_dismiss: true
                            , stackup_spacing: 10
                        });
                    }
                    if (ptpl !== undefined && padd !== "false") {
                        var tpl = $(document).find("#" + ptpl).html();
                        var list = $(document).find("[data-wb-tpl=" + ptpl + "]");
                        var post = {
                            tpl: tpl
                        };
                        var ret = false;
                        if (list.attr("data-wb-add") + "" !== "false") {
                            $.post("/ajax/setdata/" + name + "/" + item_id, post, function (ret) {
                                if (list.find("[item=" + item_id + "]").length) {
                                    list.find("[item=" + item_id + "]").after(ret);
                                    list.find("[item=" + item_id + "]:first").remove();
                                }
                                else {
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
                    $(document).trigger(name + "_after_formsave", [name, item, form, true]);
                    console.log("call: " + name + "_after_formsave");
                    $(document).trigger("wb_after_formsave", [name, item, form, true]);
                    console.log("call: wb_after_formsave");
                    return data;
                }
                , error: function (data) {
                    if (is_callable(name + "_after_formsave")) {
                        $(document).trigger(name + "_after_formsave", [name, item, form, false]);
                        console.log("call: " + name + "_after_formsave");
                    }
                    $(document).trigger("wb_after_formsave", [name, item, form, false]);
                    console.log("call: wb_after_formsave");
                    if ($.bootstrapGrowl) {
                        $.bootstrapGrowl("Ошибка сохранения!", {
                            ele: 'body'
                            , type: 'danger'
                            , offset: {
                                from: 'top'
                                , amount: 20
                            }
                            , align: 'right'
                            , width: "auto"
                            , delay: 4000
                            , allow_dismiss: true
                            , stackup_spacing: 10
                        });
                    }
                    return {
                        error: 1
                    };
                }
            });
        }
    }
    else {
        $.bootstrapGrowl("Ошибка сохранения!", {
            ele: 'body'
            , type: 'danger'
            , offset: {
                from: 'top'
                , amount: 20
            }
            , align: 'right'
            , width: "auto"
            , delay: 4000
            , allow_dismiss: true
            , stackup_spacing: 10
        });
    }
}

function wb_check_email(email) {
    if (email.match(/^([a-z0-9_-]+\.)*[a-z0-9_-]+@[a-z0-9_-]+(\.[a-z0-9_-]+)*\.[a-z]{2,6}$/i)) {
        return true;
    }
    else {
        return false;
    }
}

function wb_check_required(form) {
    var res = true;
    var idx = 0;
    $(form).find("input[required],select[required],textarea[required],[type=password],[minlength]").each(function (i) {
        idx++;
        $(this).data("idx", idx);
        if ($(this).is(":not([disabled],[type=checkbox]):visible")) {
            if ($(this).val() == "") {
                res = false;
                $(document).trigger("wb_required_false", [this]);
            }
            else {
                if ($(this).attr("type") == "email" && !wb_check_email($(this).val())) {
                    res = false;
                    $(this).data("error", "Введите корректный email");
                    $(document).trigger("wb_required_false", [this]);
                }
                else {
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
        if ($(this).is("[minlength]")) {
            var minlen = $(this).attr("minlength") * 1
            var lenstr = strlen($(this).val());
            if (lenstr < minlen) {
                res = false;
                $(this).data("error", "Минимальная длинна поля " + minlen + " символов");
                $(document).trigger("wb_required_false", [this]);
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
	
	
	var wb_ajax_process = function(that) {
        wb_ajax_loader();
        var link = that;
        var src = $(that).attr("data-wb-ajax");
        var call = $(that).attr("data-wb-ajax-done");
        var flag = true;
        if (src > "") {
            var ajax = {};
            if ($(that).attr("data-wb-tpl") !== undefined) {
                ajax.tpl = $($(that).attr("data-wb-tpl")).html();
            }
            if ($(that).is("button,:input") ) {
                if ($(that).parents("form").length) {
                    var form = $(that).parents("form");
                    flag = wb_check_required(form);
                    ajax = $(form).serialize();
                }
                if ($(that).attr("data-wb-json") !== undefined && $(that).attr("data-wb-json")>"") {
                    ajax = $.parseJSON($(that).attr("data-wb-json"));
                }
            }
            if (flag == true) {
                $(that).attr("disabled", true);
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
                    $("<div>" + data + "</div>").find(".modal[id]:not(.hidden)").each(function (i) {
                        if (i == 0) {
                            $("#" + $(this).attr("id")).modal();
                        }
                    });
                    if (call !== undefined && is_callable(call)) {
                        (eval(call))(link, src, data);
                    }
                    $(document).trigger("wb_ajax_done", [link, src, data]);
                    wb_plugins();
                    wb_delegates();
                    wb_ajax_loader_done();
                    $(that).removeAttr("disabled");
                });
            }
        } else {
            if ($(that).attr("data-wb-href") > "") {
                document.location.href = $(that).attr("data-wb-href");
            }
        }
	}
	
	
    $(document).undelegate("[data-wb-ajax]", "click");
    $(document).delegate("[data-wb-ajax]", "click", function () {
		if ($(this).is(":not(:input)") || $(this).is("button")) {
			wb_ajax_process(this);
		}
    });

    $(document).undelegate("[data-wb-ajax]:input", "change");
    $(document).delegate("[data-wb-ajax]:input", "change", function () {
		wb_ajax_process(this);
    });
    
    $("[data-wb-ajax]").each(function () {
        $(this).attr("data-wb-href", $(this).attr("href"));
        $(this).removeAttr("href");
        if ($(this).attr("data-wb-autoload")==true) {
			$(this).trigger("click");
			$(this).removeAttr("data-wb-autoload");	
		}
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
    if (wb_plugins_loaded()) {
        $.bootstrapGrowl(text, {
            ele: 'body'
            , type: 'warning'
            , offset: {
                from: 'top'
                , amount: 20
            }
            , align: 'right'
            , width: "auto"
            , delay: delay
            , allow_dismiss: true
            , stackup_spacing: 10
        });
    }
});

function wb_set_inputs(selector, data) {
    if ($(selector).length) {
        var html = $(selector).outerHTML();
    }
    else {
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
        }
        else {
            if ($(selector).is("script")) {
                var html = $(selector).html();
            }
            else {
                if ($(selector).length == 1) {
                    var html = $(selector).outerHTML();
                }
                else {
                    var html = selector;
                }
            }
        }
    }
    else {
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
        tpl: html
        , data: data
    };
    var url = "/ajax/setdata/" + form + "/" + item;
    var res = null;
    $.when($.ajax({
        type: 'POST'
        , async: false
        , data: param
        , url: url
    })).done(function (data) {
        if (ret == undefined || ret == false) {
            $(selector).after(data).remove();
        }
        else {
            res = data;
        }
    });
    return res;
}

function wb_pagination(pid) {
    if (pid == undefined) {
        var slr = ".pagination";
    }
    else {
        var slr = ".pagination[id='" + pid + "']";
    }
    $.each($(document).find(slr), function (idx) {
        var that = this;
        var id = $(this).attr("id");
        var tplid = substr(id, 5);
        $(this).data("route",$(this).attr("data-wb-route")).removeAttr("data-wb-route");
        if ($(this).is(":not([data-idx])")) {
            $(this).attr("data-idx", idx);
        }
        if ($("[data-wb-tpl='" + tplid + "']").data("variables") == undefined) {
            $.get("/ajax/pagination_vars/" + id, function (data) {
                $("[data-wb-tpl='" + tplid + "']").data("variables", data);
            });
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
        $(document).undelegate(".pagination[id=" + id + "] > a", "click");
        $(document).delegate(".pagination[id=" + id + "] > a", "click", function (event) {
            if ($(this).hasClass("next")) {
                $(this).parents(".pagination").find("li.active").next("li").find("a").trigger("click");
            }
            if ($(this).hasClass("prev")) {
                $(this).parents(".pagination").find("li.active").prev("li").find("a").trigger("click");
            }
        });
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
                //			}
                if (substr(page, 0, 4) == "page") {
                    // js пагинация
                    $("[data-page^=" + id + "]").hide();
                    $("[data-page=" + page + "]").show();
                }
                else {
                    var cache = $source.attr("data-wb-cache");
                    var size = $source.attr("data-wb-size");
                    var sort = $source.attr("data-wb-sort");
                    var idx = $source.attr("data-wb-idx");
                    var arr = explode("-", page);
                    var tpl = $("#" + tid).html();
                    var foreach = $('<div>').append($("[data-wb-tpl=" + tid + "]").clone());
                    $(foreach).find("[data-wb-tpl=" + tid + "]").html("");
                    $(foreach).find("[data-wb-tpl=" + tid + "]").attr("data-wb-sort", sort);
                    $(foreach).find("[data-wb-tpl=" + tid + "]").removeAttr("data-wb-desc");
                    var loader = $(foreach).find("[data-wb-tpl=" + tid + "]").attr("data-wb-loader");
                    var offset = $(foreach).find("[data-wb-tpl=" + tid + "]").attr("data-wb-offset");
                    if (offset == undefined) {}
                    var foreach = $(foreach).html();
                    var param = {
                        tpl: tpl
                        , tplid: tid
                        , idx: idx
                        , page: arr[2]
                        , size: size
                        , cache: cache
                        , vars: $("[data-wb-tpl=" + tid + "]").data("variables")
                        , foreach: foreach
                        , route: $source.data("route")
                    };
                    var url = "/ajax/pagination/";
                    if ($("#" + id).data("find") !== undefined) {
                        var find = $("#" + id).data("find");
                    }
                    else {
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
                        async: true
                        , type: 'POST'
                        , data: param
                        , url: url
                        , success: function (data) {
                            var data = JSON.parse(data);
                            $("[data-wb-tpl=" + tid + "]").html(data.data);
                            if (data.pages > "1") {
                                $(".pagination[id=ajax-" + pid + "]").show();
                                var pid = $(data.pagr).attr("id");
                                $(document).undelegate(".pagination[id=" + pid + "] li a", "click");
                                $("#" + pid).after(data.pagr);
                                $("#" + pid + ":first").remove();
                            }
                            else {
                                $(".pagination[id=ajax-" + tid + "]").hide();
                            }
                            window.location.hash = "page-" + idx + "-" + arr[2];
                            wb_delegates();
                            console.log("active_pagination(): trigger:after-pagination-done");
                            $(document).trigger("after-pagination-done", [id, page, arr[2]]);
                            $("body").removeClass("cursor-wait");
                            if (loader == "" || loader == undefined) {}
                            else {
                                var funcCall = loader + "(false);";
                                eval(funcCall);
                            }
                        }
                        , error: function (data) {
                            $("body").removeClass("cursor-wait");
                            if (loader == "" || loader == undefined) {}
                            else {
                                var funcCall = loader + "(false);";
                                eval(funcCall);
                            }
                            (document).trigger("after-pagination-error", [id, page, arr[2]]);
                        }
                    });
                }
                $(this).parents("ul").find("li").removeClass("active");
                $(this).parent("li").addClass("active");
                if (offset !== undefined) {
                    var scrollTop = $("[data-wb-tpl=" + tid + "]").offset().top - offset;
                    if (scrollTop < 0) {
                        scrollTop = 0;
                    }
                    $('body,html').animate({
                        scrollTop: scrollTop
                    }, 1000);
                }
                //$(document).trigger("after_pagination_click",[id,page,arr[2]]);
            }
            event.preventDefault();
            return false;
        });
    });
}

function wb_call_source(id) {
    if (substr(id, 0, 1) == "#") {
        var eid = id;
        id = substr(id, 1);
    }
    else {
        var eid = "#" + id;
    }
    if (!$(eid).parents(".formDesignerEditor").length) {
        $(document).data("sourceFile", null);
        var form = $(eid).parents("form");
        var theme = getcookie("sourceEditorTheme");
        var fsize = getcookie("sourceEditorFsize") * 1;
        var source = "&nbsp;";
        var fldname = $(eid).attr("name");
        if ($("[name='" + fldname + "']").length) {
            source = $("[name='" + fldname + "']").val();
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
        var srced = ace.edit(id);
        srced.setTheme("ace/theme/chrome");
        srced.setOptions({
            enableBasicAutocompletion: true
            , enableSnippets: true
        });
        srced.getSession().setUseWrapMode(true);
        srced.getSession().setUseSoftTabs(true);
        srced.setDisplayIndentGuides(true);
        srced.setHighlightActiveLine(false);
        srced.setAutoScrollEditorIntoView(true);
        srced.commands.addCommand({
            name: 'save'
            , bindKey: {
                win: 'Ctrl-S'
                , mac: 'Command-S'
            }
            , exec: function () {
                if (form !== undefined) {
                    console.log(form);
                    wb_formsave(form);
                }
            }
            , readOnly: false
        });
        srced.gotoLine(0, 0);
        srced.resize(true);
        if ($("#cke_text .cke_contents").length) {
            var ace_height = $("#cke_text .cke_contents").height();
        }
        else {
            var ace_height = 400;
        }
        $(".ace_editor").css("height", ace_height);
        srced.setTheme(theme);
        srced.setFontSize(fsize);
        srced.setValue(source);
        srced.gotoLine(0, 0);
        srced.getSession().setMode("ace/mode/php");
        $(form).data(eid, srced);
        wb_call_source_events(srced, eid, fldname);
        return srced;
    }
}

function wb_call_source_events(srced, eid, fldname) {
    var tmp = explode("-", eid);
    var toolbar = tmp[0] + "-toolbar-" + tmp[1] + " ";
    $(toolbar).data("editor", false);
    $(toolbar).next(".ace_editor").attr("name", fldname).attr("id", substr(eid, 1));
    srced.getSession().on('change', function (e) {
        if ($(toolbar).data("editor") == undefined) {
            $(toolbar).data("editor", false);
        }
        if ($(toolbar).data("editor") == false && $(document).data("editorChange") !== true) {
            $(toolbar).data("editor", true);
            setTimeout(function () {
                $(document).trigger("sourceChange", {
                    "value": srced.getSession().getValue()
                    , "field": fldname
                    , "form": $(toolbar).parents("form")
                });
                $(toolbar).data("editor", false);
            }, 500);
        }
    });
    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        if ($(e.target.hash).find(eid).length) {
            var form = $(eid).parents("form");
            var val = srced.getSession().getValue();
            srced.getSession().setValue(val);
        }
    });
    $(document).on("editorChange", function (e, data) {
        if ($(document).data("sourceChange") !== true) {
            $(document).data("editorChange", true);
            var aeid = "#" + $(".ace_editor[name=" + data.field + "]").attr("id");
            if (eid==aeid) {
                var form = $(eid).parents("form");
                srced.getSession().setValue(data.value);
                setTimeout(function () {
                    $(document).data("editorChange", false);
                }, 100);
            }
        }
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
        //if ($(this).hasClass("btnCopy")) 		{$(document).data("sourceFile",srced.getCopyText());}
        //if ($(this).hasClass("btnPaste")) 		{srced.insert($(document).data("sourceFile"));}
        if ($(this).hasClass("btnCopy")) {
            $(document).data("sourceClipboard", srced.getCopyText());
        }
        if ($(this).hasClass("btnPaste")) {
            srced.insert($(document).data("sourceClipboard"));
        }
        if ($(this).hasClass("btnUndo")) {
            srced.execCommand("undo");
        }
        if ($(this).hasClass("btnRedo")) {
            srced.execCommand("redo");
        }
        if ($(this).hasClass("btnFind")) {
            srced.execCommand("find");
        }
        if ($(this).hasClass("btnReplace")) {
            srced.execCommand("replace");
        }
        if ($(this).hasClass("btnLight")) {
            srced.setTheme("ace/theme/chrome");
            setcookie("sourceEditorTheme", "ace/theme/chrome");
        }
        if ($(this).hasClass("btnDark")) {
            srced.setTheme("ace/theme/monokai");
            setcookie("sourceEditorTheme", "ace/theme/monokai");
        }
        if ($(this).hasClass("btnClose")) {
            srced.setValue("");
            $(document).data("sourceFile", null);
            $("#sourceEditorToolbar .btnSave").removeClass("btn-danger");
        }
        if ($(this).hasClass("btnFontDn")) {
            if (fsize > 8) {
                fsize = fsize * 1 - 1;
            }
            srced.setFontSize(fsize);
            setcookie("sourceEditorFsize", fsize);
        }
        if ($(this).hasClass("btnFontUp")) {
            if (fsize < 20) {
                fsize = fsize * 1 + 1;
            }
            srced.setFontSize(fsize);
            setcookie("sourceEditorFsize", fsize);
        }
        if ($(this).hasClass("btnFullScr")) {
            var div = $(this).parents(toolbar).parent();
            div.parents(".modal").toggleClass("fullscr");
            if (div.parents(".modal").hasClass("fullscr")) {
                var offset = div.find("pre.ace_editor").offset();
                div.find("pre.ace_editor").height($(window).height() - offset.top - 15);
            }
            else {
                div.find("pre.ace_editor").height(400);
            }
            srced.resize();
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
    var name = ''
        , obj = {}
        , method = '';
    if (typeof v === 'string') {
        obj = window;
        method = v;
        name = v;
    }
    else if (v instanceof Array && v.length === 2 && typeof v[0] === 'object' && typeof v[1] === 'string') {
        obj = v[0];
        method = v[1];
        name = (obj.constructor && obj.constructor.name) + '::' + method;
    }
    else {
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
    }
    else {
        return null;
    }
}

    $(document).ready(function() {
        "use strict";
        if ($("link[rel$=less],style[rel$=less]").length) wb_include("/engine/js/less.min.js");
        wb_alive();
        wb_delegates();
        $("body").removeClass("cursor-wait");
    });
function base64_decode( data ) {	// Decodes data encoded with MIME base64
	// 
	// +   original by: Tyler Akins (http://rumkin.com)


	var b64 = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";
	var o1, o2, o3, h1, h2, h3, h4, bits, i=0, enc='';

	do {  // unpack four hexets into three octets using index points in b64
		h1 = b64.indexOf(data.charAt(i++));
		h2 = b64.indexOf(data.charAt(i++));
		h3 = b64.indexOf(data.charAt(i++));
		h4 = b64.indexOf(data.charAt(i++));

		bits = h1<<18 | h2<<12 | h3<<6 | h4;

		o1 = bits>>16 & 0xff;
		o2 = bits>>8 & 0xff;
		o3 = bits & 0xff;

		if (h3 == 64)	  enc += String.fromCharCode(o1);
		else if (h4 == 64) enc += String.fromCharCode(o1, o2);
		else			   enc += String.fromCharCode(o1, o2, o3);
	} while (i < data.length);

	return enc;
}