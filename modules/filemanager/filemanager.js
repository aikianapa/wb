    var editor;
    var $=jQuery.noConflict();
    filemanagerGetDir('');
    filemanagerSideMenu();
    filemanagerListEvents();
    filemanagerBreadcrumbs();
    filemanagerDialog();
    filemanagerBuffer();
    $("#filemanagerTabs").data("tab", $("#filemanagerTabs").html());
    $("#filemanagerTabs").html("");

    
    function filemanagerListEvents() {
        
    $('#filemanager').undelegate('#filemanagerModalDialog','shown.bs.modal');    
    $('#filemanager').delegate('#filemanagerModalDialog','shown.bs.modal', function () {
      $('#filemanagerModalDialog input:visible:first').focus();
    });
        
        
    $("#filemanager").undelegate("#filemanagerModalDialog","keydown");
    $("#filemanager").delegate("#filemanagerModalDialog","keydown",function(e){
        if (e.keyCode == 13) {
            $("#filemanagerModalDialog .btn-primary").trigger("click");
            return false;
        }
    });
        

    $("#filemanager").off("checkbox");
    $("#filemanager").on("checkbox", function() {
            var menu = $("#filemanager .content-left .nav");
            var count = $("#filemanager #list").find("tr:not(.back) [type=checkbox]:checked").length;

            if (count > 1) {
                $(menu).find(".nav-item.allow-all").show();
            }
            if (count == 0) {
                $("#filemanager").data("buffer", undefined);
                $("#filemanager").data("bufferpath", undefined);
                $("#filemanager .allow-buffer").hide();
            }
    });
        
    $("#filemanager").delegate("#list tr", "dblclick", function() {
            var path = $("#filemanager #list").data("path");
            if ($(this).is(".dir,.dir1")) {
                filemanagerGetDir(path + "/" + $(this).attr("data-name"));
            }
            if ($(this).is(".file")) {
                filemanagerCallEditor(path + "/" + $(this).attr("data-name"));
            }
            if ($(this).is(".back")) {
                path = explode("/", path);
                path.splice(path.length - 1, 1);
                path = implode("/", path);
                filemanagerGetDir(path);
            }
        });


        $("#filemanager").undelegate("#list tr.dir td.name", "click");
        $("#filemanager").delegate("#list tr.dir td.name", "click", function() {
            if (!$("#filemanager #list .dropdown-menu.show").length) {
                $(this).parents("tr").trigger("dblclick");
            }
        });
        $("#filemanager").undelegate("#list tr.dir1 td.name", "click");
        $("#filemanager").delegate("#list tr.dir1 td.name", "click", function() {
            if (!$("#filemanager #list .dropdown-menu.show").length) {
                $(this).parents("tr").trigger("dblclick");
            }
        });

        $("#filemanager").undelegate("#list a.nav-link, a.nav-link[href='#newdir'], a.nav-link[href='#newfile']", "click");
        $("#filemanager").delegate("#list a.nav-link, a.nav-link[href='#newdir'], a.nav-link[href='#newfile']", "click", function() {
            $("#filemanager #filemanagerModalDialog").remove();
            var href = $(this).attr("href");
            var post = {
                "path": $("#filemanager #list").data("path")
            };
            if ($(this).parents("tr[data-name]").length) {
                var parent = $(this).parents("tr[data-name]");
                post["name"] = $(parent).attr("data-name");
                if ($(parent).hasClass("dir")) {
                    post["type"] = "dir";
                }
                if ($(parent).hasClass("dir1")) {
                    post["type"] = "dir-link";
                }
                if ($(parent).hasClass("file")) {
                    post["type"] = "file";
                }
                if ($(parent).hasClass("file1")) {
                    post["type"] = "file-link";
                }
            }
            $("#filemanager").data("post", post);
            $("#filemanager").data("line", $(this).parents("tr"));
            if (substr(href, 0, 1) == "#" && !in_array(href, ["#edit"])) {
                $.post("/module/filemanager/dialog/" + substr(href, 1), post, function(data) {
                    $("#filemanager").append(data);
                    $("#filemanager #filemanagerModalDialog").modal("show");
                });
            }
            if (href == "#edit") {
                $(this).parents("tr").find("td.name").trigger("dblclick");
            }
        });
        $("#filemanager").undelegate("#list tr.back td.name", "click");
        $("#filemanager").delegate("#list tr.back td.name", "click", function() {
            $(this).parents("tr").trigger("dblclick");
        });

        $("#filemanager").undelegate("#list tr", "contextmenu");
        $("#filemanager").delegate("#list tr", "contextmenu", function() {
            $(this).find("td.dropdown > a").trigger("click");
            return false;
        });

    }

    function filemanagerBuffer() {
        $("#filemanager").undelegate("#list tr [type=checkbox]", "change");
        $("#filemanager").delegate("#list tr [type=checkbox]", "change", function() {
            var menu = $("#filemanager .content-left .nav");
            var count = $("#filemanager #list").find("tr:not(.back) [type=checkbox]:checked").length;
            var type;
            $(menu).find(".nav-item.hidden").hide();
            if (count == 1) {
                var check = $(this).parents("#list").find("[type=checkbox]:checked");
                var ext = $(check).parents("tr").attr("data-ext");
                if ($(check).parents("tr.dir").length) {
                    type = "dir";
                } else if ($(check).parents("tr.file").length) {
                    type = "file";
                } else if ($(check).parents("tr.dir1").length) {
                    type = "dir1";
                } else if ($(check).parents("tr.file1").length) {
                    type = "file1";
                }
                $(menu).find(".nav-item.allow-single.allow-" + type).show();
                $(menu).find(".nav-item.allow-all").show();
                if (ext !== undefined) {
                    $(menu).find(".nav-item[data-ext]").each(function() {
                        if (!in_array(ext, explode(",", $(this).attr("data-ext")))) {
                            $(this).hide();
                        }
                    });
                    $(menu).find(".nav-item[data-no-ext]").each(function() {
                        if (in_array(ext, explode(",", $(this).attr("data-no-ext")))) {
                            $(this).hide();
                        }
                    });
                }
            }
            $("#filemanager").trigger("checkbox");
            return false;
        });   
    }

    function filemanagerDialogMulti(href) {
            $("#filemanager #filemanagerModalDialog").remove();
            var check = $("#filemanager #list").find("tr:not(.back) [type=checkbox]:checked");
            var post = {
                "path": $("#filemanager #list").data("path"),
                "multi": true
            };
            $.post("/module/filemanager/dialog/" + substr(href, 1), post, function(data) {
                $("#filemanager").append(data);
                $("#filemanager #filemanagerModalDialog").modal("show");
            });       
    }


    function filemanagerSideMenu() {

        $("#filemanager").undelegate(".content-left .nav a.nav-link", "click");
        $("#filemanager").delegate(".content-left .nav a.nav-link", "click", function() {
            var check = $("#filemanager #list").find("tr:not(.back) [type=checkbox]:checked");
            var count = $(check).length;
            var href = $(this).attr("href");
            var type;
            if (count == 1) {
                if ($(check).parents("tr.dir").length) {
                    type = "dir";
                } else if ($(check).parents("tr.file").length) {
                    type = "file";
                } else if ($(check).parents("tr.dir1").length) {
                    type = "dir1";
                } else if ($(check).parents("tr.file1").length) {
                    type = "file1";
                }
            }

            switch (href) {
                case '#rename':
                    if (count == 1) {
                        $(check).parents("tr").find("a[href='#ren" + type + "']").trigger("click");
                    }
                    break
                case '#remove':
                    if (type !== "dir" && type !== "file") {
                        type = "link";
                    }
                    if (count == 1) {
                        $(check).parents("tr").find("a[href='#rm" + type + "']").trigger("click");
                    } else {
                         var list={};
                         $("#filemanager #list").find("tr:not(.back) [type=checkbox]:checked").parents("tr").each(function(i){
                            var item = {name: $(this).attr("data-name")};
                            list[i]=item; 
                         });
                        var post={
                            path : $("#filemanager #list").data("path"),
                            list : json_encode(list)
                        };
                         $("#filemanager").data("post",post);
                        filemanagerDialogMulti(href);
                    }
                    break
                case '#edit':
                    if (count == 1) {
                        $(check).parents("tr").find("a[href='#edit']").trigger("click");
                    }
                    break
                case '#dnload':
                    // не работает!!!!
                    if (count == 1) {
                        var dnl=$(check).parents("tr").find("a[download]");
                        if (dnl.length) {
                            console.log($(check).parents("tr").find("a[download]").attr("download"));
                            $(dnl).trigger("click");
                        }
                    }
                    break
                case '#copy':
                    $("#filemanager").data("buffer", $("#filemanager #list").find("tr:not(.back) [type=checkbox]:checked").parents("tr"));
                    $("#filemanager").data("bufferpath", $("#filemanager #list").data("path"));
                    $("#filemanager").data("buffertype", "copy");
                    $("#filemanager .allow-buffer").show();
                    break
                    
                case '#cut':
                    $("#filemanager").data("buffer", $("#filemanager #list").find("tr:not(.back) [type=checkbox]:checked").parents("tr"));
                    $("#filemanager").data("bufferpath", $("#filemanager #list").data("path"));
                    $("#filemanager").data("buffertype", "cut");
                    $("#filemanager .allow-buffer").show();
                    break
                    
                case '#paste':
                    if ($("#filemanager").data("bufferpath") !== $("#filemanager #list").data("path")) {
                        filemanagerPaste();
                    }
                    break
                case "#refresh":
                    filemanager_reload_list();
                    break
            }
            return false;
        });
    }

    function filemanagerDialog() {
        $("#filemanager").undelegate("#filemanagerModalDialog .btn-primary", "click");
        $("#filemanager").delegate("#filemanagerModalDialog .btn-primary", "click", function() {
            var action = $(this).attr("data-action");
            var post, data;
            if (action == "paste" || action == "remove") {
                post = $("#filemanager").data("post");
            } else {
                post = $("#filemanager #filemanagerModalDialog .modal-body form").serialize();
                data = $("#filemanager").data("post");
                post += "&type=" + data["type"] + "&path=" + data["path"];
            }
            $("#filemanager #filemanagerModalDialog").modal("hide");
            $.post("/module/filemanager/action/" + action, post, function(data) {
                data = json_decode(data);
                var line = $("#filemanager").data("line");
                $("#filemanager #filemanagerModalDialog").modal("hide");
                if (line !== undefined && data.action == "change_name") {
                    $(line).find("td.name > span").html(data.name);
                    $(line).attr("data-name", data.name);
                    if (data.ext !== undefined) {
                        $(line).find("td.name + td").html(data.ext);
                    }
                }
                $("#filemanager").data("post", undefined);
                $("#filemanager").data("line", undefined);
                if (data.action == "reload_list") {
                    filemanager_reload_list();
                }


            });
        });
    }


    function filemanagerBreadcrumbs() {

        $("#filemanager").delegate(".breadcrumb a", "click", function(e) {
            var idx = $(this).parents("li").attr("idx");
            var path = "";
            var i;
            var breadcrumb = $(this).parents(".breadcrumb");
            if (idx !== undefined) {
                for (i = 1; i <= idx; i++) {
                    path += "/" + $(breadcrumb).find("li:eq(" + i + ")").attr("data-name");
                }
            }
            filemanagerGetDir(path);
            e.preventDefault();
            return false;
        });
    }

    function filemanagerGetDir(dir) {
        wb_ajax_loader();
        var d = $.Deferred();
        var res;
        $.get("/module/filemanager/getdir/?dir=" + urlencode(dir), function(data) {
            $("#filemanager #panel").replaceWith(data);
            $("#filemanager #list").data("path", dir);
            $("#filemanager #panel").noSelect();
            d.resolve();
            wb_ajax_loader_done();
        });
        return d;
    }

    function filemanagerCallEditor(file) {
        wb_ajax_loader();
        var res = false;
        var fname, tab;
        $("#filemanagerTabs").find(".nav-item,.nav-link").removeClass("active");
        $("#filemanagerTabs .nav-item").each(function() {
            if ($(this).data("file") == file) {
                $(this).addClass("active");
                $(this).find(".nav-link").addClass("active");
                res = true;
            }
        });

        if (res == false) {
            fname = explode("/", file);
            fname = fname[fname.length - 1];
            tab = $($("#filemanagerTabs").data("tab"));
            $(tab).find(".nav-link").prepend(fname).addClass("active");
            $(tab).data("file", file);
            $("#filemanagerTabs").append($(tab));
        }
        filemanagerEditFile(file);

        $("#filemanagerTabs").undelegate(".fa-close", "click");
        $("#filemanagerTabs").delegate(".fa-close", "click", function() {
            $(this).parents(".nav-item").remove();
            if (!$("#filemanagerTabs .nav-item").length) {
                $("#filemanagerModalSrc").modal("hide");
            } else {
                $("#filemanagerTabs").find(".nav-item,.nav-link").removeClass("active");
                $("#filemanagerTabs").find(".nav-item:eq(0) .nav-link").trigger("click")
                filemanagerStateLoad($("#filemanagerTabs .nav-item:eq(0)"));
            }
        });

        $("#filemanagerTabs").undelegate(".nav-item:not(.active)", "click");
        $("#filemanagerTabs").delegate(".nav-item:not(.active)", "click", function() {
            var tab = $("#filemanagerTabs .nav-item.active");
            filemanagerStateSave(tab);
            filemanagerStateLoad(this);
        });

        $("#filemanagerSrc").undelegate(".btnSave", "click");
        $("#filemanagerSrc").delegate(".btnSave", "click", function() {
            filemanagerSave();
        });

        $("#filemanagerSrc").undelegate(".btnFullScr", "click");
        $("#filemanagerSrc").delegate(".btnFullScr", "click", function() {
            editor.resize();
        });

        $("#filemanagerModalSrc").undelegate(".ace_editor", "mouseleave");
        $("#filemanagerModalSrc").delegate(".ace_editor", "mouseleave", function() {
            var tab = $("#filemanagerTabs .nav-link.active").parents(".nav-item");
            filemanagerStateSave(tab);
        });

    }

    function filemanagerPaste() {
        var spath = $("#filemanager").data("bufferpath");
        var dpath = $("#filemanager #list").data("path");
        var method = $("#filemanager").data("buffertype");
        var list = [];
        $($("#filemanager").data("buffer")).each(function() {
            var type = "file";
            if ($(this).hasClass("dir")) {
                type = "dir";
            }
            if ($(this).hasClass("dir1")) {
                type = "dir";
            }
            if ($(this).hasClass("file")) {
                type = "file";
            }
            if ($(this).hasClass("file1")) {
                type = "file";
            }
            var item = {
                name: $(this).attr("data-name"),
                path: spath,
                type: type
            };
            list.push(item);
        });
        $.post("/module/filemanager/dialog/paste", {
            list: list,
            method: method,
            path: dpath
        }, function(data) {
            data = json_decode(data);
            console.log(data);
            $("#filemanager").data("post", data.post);
            if (data.res == "dialog") {
                $("#filemanager #filemanagerModalDialog").remove();
                $("#filemanager").append(data.action);
                $("#filemanager #filemanagerModalDialog").modal("show");
            }
            if (data.action == "reload_list") {
                filemanager_reload_list();
            }
        });
    }

    function filemanager_reload_list() {
        $("#filemanager").find(".breadcrumb .breadcrumb-item:last > a").trigger("click");
    }


    function filemanagerEditFile(file) {
        $.post("/module/filemanager/getfile/", {
            file: file
        }, function(data) {
            editor = wb_call_source($("#filemanagerSrc .ace_editor").attr("id"));
            editor.setValue(data);
            editor.gotoLine(0, 0);
            editor.commands.addCommand({
                name: 'save',
                bindKey: {
                    win: 'Ctrl-S',
                    mac: 'Command-S'
                },
                exec: function() {
                    filemanagerSave();
                },
                readOnly: false
            });
            filemanagerStateSave($("#filemanagerTabs .nav-item.active"));
            $("#filemanagerModalSrc").removeClass("fullscr");
            if (!$("#filemanagerModalSrc:visible").length) {
                $("#filemanagerModalSrc").modal("show");
            }
            wb_ajax_loader_done();
        });
    }

    function filemanagerSave() {
        var tab = $("#filemanagerTabs .nav-link.active").parents(".nav-item");
        if ($(tab).length) {
            $.post("/module/filemanager/putfile/", {
                file: $(tab).data("file"),
                text: editor.getValue()
            }, function(data) {
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
            });
        }
    }

    function filemanagerStateSave(tab) {
        if ($(tab).length) {
            $(tab).data("editor", editor.getValue());
            $(tab).data("editorUndo", editor.getSession().getUndoManager());
            $(tab).data("editorPos", editor.getCursorPosition());
        }
    }

    function filemanagerStateLoad(tab) {
        if ($(tab).length && $(tab).data("editor") !== undefined) {
            editor.setValue($(tab).data("editor"));
            editor.getSession().setUndoManager($(tab).data("editorUndo"));
            var pos = $(tab).data("editorPos");
            editor.gotoLine(pos["row"] + 1, pos["column"]);
        }
    }
