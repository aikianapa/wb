<h6 class="element-header">
    Файловый менеджер
    <button class="btn btn-sm btn-success pull-right" data-wb-ajax="" data-wb-append="body">
     <i class="fa fa-plus"></i>
   </button>
</h6>
<div class="col-12" id="filemanager">
    <div class="content-wrapper">
        <div class="content-left">
            <button class="btn btn-default btn-block mg-b-20">Загрузить файл</button>
            <label class="content-left-label">Действия</label>
            <ul class="nav mg-t-1-force">
                <li class="nav-item">
                    <a href="#refresh" class="nav-link">
                  <i class="fa fa-refresh"></i>
                  <span>Обновить список</span>
                </a>
                </li>
                <!-- nav-item -->
                <li class="nav-item">
                    <a href="#newdir" class="nav-link">
                  <i class="fa fa-folder-o"></i>
                  <span>Новая директория</span>
                </a>
                </li>
                <!-- nav-item -->
                <li class="nav-item">
                    <a href="#newfile" class="nav-link">
                  <i class="fa fa-file-o"></i>
                  <span>Новый файл</span>
                </a>
                </li>
                <!-- nav-item -->
                <li class="nav-item hidden allow-single allow-dir allow-file allow-dir1 allow-file1">
                    <a href="#rename" class="nav-link">
                  <i class="fa fa-i-cursor"></i>
                  <span>Переименовать</span>
                </a>
                </li>
                <!-- nav-item -->
                <li class="nav-item hidden allow-all">
                    <a href="#remove" class="nav-link">
                  <i class="fa fa-trash-o"></i>
                  <span>Удалить</span>
                </a>
                </li>
                <!-- nav-item -->
                <li class="nav-item hidden allow-single allow-file allow-file1">
                    <a href="#edit" class="nav-link">
                  <i class="fa fa-edit"></i>
                  <span>Редактировать</span>
                </a>
                </li>
                <!-- nav-item -->
                <li class="nav-item hidden allow-all" data-no-ext="zip">
                    <a href="#zip" class="nav-link">
                  <i class="fa fa-file-archive-o"></i>
                  <span>Архивировать</span>
                </a>
                </li>
                <!-- nav-item -->
                <li class="nav-item hidden allow-single allow-file" data-ext="zip">
                    <a href="#unzip" class="nav-link">
                  <i class="fa fa-file-archive-o"></i>
                  <span>Разахивировать</span>
                </a>
                </li>
                <!-- nav-item -->
                <li class="nav-item hidden allow-single allow-file">
                    <a href="#dnload" class="nav-link">
                  <i class="fa fa-download"></i>
                  <span>Скачать</span>
                </a>
                </li>
                <!-- nav-item -->
                <li class="nav-item hidden allow-all">
                    <a href="#copy" class="nav-link">
                  <i class="fa fa-copy"></i>
                  <span>Копировать</span>
                </a>
                </li>
                <!-- nav-item -->
                <li class="nav-item hidden allow-all">
                    <a href="#cut" class="nav-link">
                  <i class="fa fa-cut"></i>
                  <span>Вырезать</span>
                </a>
                </li>
                <!-- nav-item -->
                <li class="nav-item hidden allow-buffer">
                    <a href="#paste" class="nav-link">
                  <i class="fa fa-paste"></i>
                  <span>Вставить</span>
                </a>
                </li>
                <!-- nav-item -->
            </ul>
        </div>
        <!-- content-left -->
        <div class="content-body" id="panel">
            <div class="content-body-header">
                <div class="d-flex">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb" data-wb-role="foreach" data-wb-from="path" data-wb-tpl="false" data-wb-size="false" data-wb-hide="wb">
                            <li class="breadcrumb-item" data-name="{{0}}"><a href="#">{{0}}</a></li>
                            <!--li class="breadcrumb-item active" aria-current="page">Data</li-->
                        </ol>
                        <li class="breadcrumb-item" data-wb-prepend="nav .breadcrumb"><a href="#" data-name=""><i class="fa fa-home"></i></a></li>
                    </nav>
                </div>
            </div>
            <!-- content-body-header -->

            <!--ul id="list" class="row" data-wb-role="foreach" data-wb-from="result" data-wb-tpl="false" data-wb-size="false" data-wb-hide="wb">
                <li class="col-12 {{type}}{{link}} {{ext}}" data-name="{{name}}">{{name}}</li>
              </ul-->

            <table id="list" class="table table-striped mg-b-0" data-wb-role="foreach" data-wb-from="result" data-wb-tpl="false" data-wb-size="false" data-wb-hide="wb">
                <tr class="col-12 {{type}}{{link}} {{ext}}" data-name="{{name}}" data-ext="{{ext}}">
                    <td class="valign-middle">
                        <label class="ckbox mg-b-0">
                    <input type="checkbox"><span></span>
                  </label>
                    </td>
                    <td class="col name">
                        <i class="fa {{type}} {{ext}} tx-22 tx-primary lh-0 valign-middle"></i>
                        <span class="pd-l-5">{{name}}</span>
                    </td>
                    <td class="hidden-xs-down">{{ext}}</td>
                    <td class="dropdown">
                        <a href="#" data-toggle="dropdown" class="btn pd-y-3 tx-gray-500 hover-info" data-wb-where='type!="back"'><i class="icon ion-more"></i></a>
                        <div class="dropdown-menu dropdown-menu-right pd-10" data-wb-where='type!="back"'>
                            <nav class="nav nav-style-1 flex-column">
                                <a href="#edit" class="nav-link" data-wb-where='type="file"'><i class="fa fa-edit"></i> Редактировать</a>
                                <a href="#rendir" class="nav-link" data-wb-where='type="dir"'><i class="fa fa-i-cursor"></i> Переименовать</a>
                                <a href="#renfile" class="nav-link" data-wb-where='type="file"'><i class="fa fa-i-cursor"></i> Переименовать</a>
                                <a href="#renlink" class="nav-link" data-wb-where='type="dir1" OR type="file1"'><i class="fa fa-i-cursor"></i> Переименовать</a>
                                <a href="#clone" class="nav-link" data-wb-where='type="file"'><i class="fa fa-copy"></i> Дублировать</a>
                                <a href="{{href}}" download="{{name}}" class="nav-link" data-wb-where='type="file"'><i class="fa fa-download"></i> Скачать</a>
                                <a href="#rmfile" class="nav-link" data-wb-where='type="file"'><i class="fa fa-remove"></i> Удалить</a>
                                <a href="#rmdir" class="nav-link" data-wb-where='type="dir"'><i class="fa fa-remove"></i> Удалить</a>
                                <a href="#rmlink" class="nav-link" data-wb-where='type="dir1" OR type="file1"'><i class="fa fa-trash-o"></i> Удалить</a>
                            </nav>
                        </div>
                        <!-- dropdown-menu -->
                    </td>
                </tr>
            </table>



        </div>
        <!-- content-body -->
    </div>
    <!-- content-wrapper -->



    <div id="filemanagerModalSrc" class="modal fade hidden" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <button class="btn btn-sm btn-default btn-edit-close" data-toggle="modal" data-target="#filemanagerModalSrc"><i class="fa fa-close"></i></button>
            <div class="modal-content">
                <div data-wb-role="include" src="source" id="filemanagerSrc">
                </div>
            </div>
        </div>
    </div>

    <div id="filemanagerModalDialog" class="modal fade hidden" tabindex="-1" role="dialog" aria-hidden="true">
        <meta name="newdir" title="Новая директория" content="Создать новую директорию с именем:<br> {{newname}}">
        <meta name="newfile" title="Новый файл" content="Создать новый файл с именем:<br> {{newname}}">
        <meta name="rmdir" title="Удаление директории" content="Удалить директорию <b>{{_POST[name]}}</b> со всем её содержимым? {{dirname}}" invisible="dirname">
        <meta name="rmfile" title="Удаление файла" content="Удалить файл <b>{{_POST[name]}}</b>? {{filename}}" invisible="filename">
        <meta name="rendir" title="Переименование директории" content="Переименовать директорию <b>{{_POST[name]}}</b> в: {{dirname}} {{oldname}}" visible="dirname" invisible="oldname">
        <meta name="renfile" title="Переименование файла" content="Переименовать файл <b>{{_POST[name]}}</b> в: {{filename}} {{oldname}}" visible="filename" invisible="oldname">
        <meta name="paste" title="Вставка" content="Некоторые объекты уже существуют в этой директории.<br> Выполнить перезапись существующих объектов?">
        <input type="text" class="form-control" name="newname">
        <input type="hidden" class="form-control" name="dirname" value="{{_POST[name]}}">
        <input type="hidden" class="form-control" name="filename" value="{{_POST[name]}}">
        <input type="hidden" class="form-control" name="oldname" value="{{_POST[name]}}">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
                </div>
                <div class="modal-body">
                    <form></form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Отмена</button>
                    <button type="button" class="btn btn-primary">Выполнить</button>
                </div>
            </div>
        </div>
    </div>


    <div class="nav-active-primary mg-t-5" data-wb-append="#filemanagerSrc .source-toolbar">
        <ul class="nav nav-tabs" role="tablist" id="filemanagerTabs">
            <li class="nav-item"><a class="nav-link" href="" data-toggle="tab" aria-expanded="false"> <i class="fa fa-close"></a></i>
            </li>
        </ul>
    </div>

</div>

<style>
    #filemanager #filemanagerModalSrc .btn-edit-close {
        position: absolute;
        right: 0;
        z-index: 1;
    }

    #filemanager #list .fa.back:before {
        content: "\f077";
    }

    #filemanager #list .fa.dir:before {
        content: "\f114";
        color: #ffc107;
    }

    #filemanager #list .fa.dir1:before {
        content: "\f114";
        color: #ffc107;
    }

    #filemanager #list .fa.file:before {
        content: "\f016";
    }

    #filemanager #list .fa.file1:before {
        content: "\f016";
    }

    #filemanager #list .fa.php:before {
        content: "\f1c9";
    }

    #filemanager #list .fa.css:before {
        content: "\f1c9";
    }

    #filemanager #list .fa.scss:before {
        content: "\f1c9";
    }

    #filemanager #list .fa.less:before {
        content: "\f1c9";
    }

    #filemanager #list .fa.htm:before {
        content: "\f1c9";
    }

    #filemanager #list .fa.html:before {
        content: "\f1c9";
    }

    #filemanager #list .fa.js:before {
        content: "\f1c9";
    }

    #filemanager #list .fa.json:before {
        content: "\f1c9";
    }

    #filemanager #list .fa.ini:before {
        content: "\f1c9";
    }

    #filemanager #list .fa.txt:before {
        content: "\f0f6";
    }

    #filemanager #list .fa.ico:before {
        content: "\f1c5";
    }

    #filemanager #list .fa.svg:before {
        content: "\f1c5";
    }

    #filemanager #list .fa.png:before {
        content: "\f1c5";
    }

    #filemanager #list .fa.gif:before {
        content: "\f1c5";
    }

    #filemanager #list .fa.jpg:before {
        content: "\f1c5";
    }

    #filemanager #list .fa.jpeg:before {
        content: "\f1c5";
    }

    #filemanager #list .fa.tiff:before {
        content: "\f1c5";
    }

    #filemanager #list .fa.bmp:before {
        content: "\f1c5";
    }

    #filemanager #list .fa.zip:before {
        content: "\f1c6";
    }

    #filemanager #list .fa.tar:before {
        content: "\f1c6";
    }

    #filemanager #list .fa.gzip:before {
        content: "\f1c6";
    }

    #filemanager #list .fa.arj:before {
        content: "\f1c6";
    }

    #filemanager #list .fa.rar:before {
        content: "\f1c6";
    }

    #filemanager #list .fa.z:before {
        content: "\f1c6";
    }

</style>

<script>
    // Tree plugin, for more examples you can check out http://www.easyjstree.com/
    $(document).ready(function() {
        var editor;
        filemanagerGetDir('');
        $("#filemanagerTabs").data("tab", $("#filemanagerTabs").html());
        $("#filemanagerTabs").html("");
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

        $("#filemanager").undelegate(".content-left .nav a.nav-link", "click");
        $("#filemanager").delegate(".content-left .nav a.nav-link", "click", function() {
            var check = $("#filemanager #list").find("tr:not(.back) [type=checkbox]:checked");
            var count = $(check).length;
            var href = $(this).attr("href");
            if (count == 1) {
                if ($(check).parents("tr.dir").length) {
                    var type = "dir";
                }
                if ($(check).parents("tr.file").length) {
                    var type = "file";
                }
                if ($(check).parents("tr.dir1").length) {
                    var type = "dir1";
                }
                if ($(check).parents("tr.file1").length) {
                    var type = "file1";
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
                        $(check).parents("tr").find("a[download]").trigger("click");
                    }
                    break
                case '#copy':
                    $("#filemanager").data("buffer", $("#filemanager #list").find("tr:not(.back) [type=checkbox]:checked").parents("tr"));
                    $("#filemanager").data("bufferpath", $("#filemanager #list").data("path"));
                    $("#filemanager").data("buffertype", "copy");
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

        $("#filemanager").undelegate("#filemanagerModalDialog .btn-primary", "click");
        $("#filemanager").delegate("#filemanagerModalDialog .btn-primary", "click", function() {
            var action = $(this).attr("data-action");
            if (action == "paste") {
                var post = $("#filemanager").data("post");
            } else {
                var post = $("#filemanager #filemanagerModalDialog .modal-body form").serialize();
                var data = $("#filemanager").data("post");
                post += "&type=" + data["type"] + "&path=" + data["path"];
            }
            $("#filemanager #filemanagerModalDialog").modal("hide");
            $.post("/module/filemanager/action/" + action, post, function(data) {
                var data = json_decode(data);
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


        $("#filemanager").undelegate("#list tr.back td.name", "click");
        $("#filemanager").delegate("#list tr.back td.name", "click", function() {
            $(this).parents("tr").trigger("dblclick");
        });

        $("#filemanager").undelegate("#list tr", "contextmenu");
        $("#filemanager").delegate("#list tr", "contextmenu", function() {
            $(this).find("td.dropdown > a").trigger("click");
            return false;
        });

        $("#filemanager").undelegate("#list tr [type=checkbox]", "change");
        $("#filemanager").delegate("#list tr [type=checkbox]", "change", function() {
            var menu = $("#filemanager .content-left .nav");
            var count = $(this).parents("#list").find("tr:not(.back) [type=checkbox]:checked").length;
            $(menu).find(".nav-item.hidden").hide();
            if (count == 1) {
                var check = $(this).parents("#list").find("[type=checkbox]:checked");
                var ext = $(check).parents("tr").attr("data-ext");
                if ($(check).parents("tr.dir").length) {
                    var type = "dir";
                }
                if ($(check).parents("tr.file").length) {
                    var type = "file";
                }
                if ($(check).parents("tr.dir1").length) {
                    var type = "dir1";
                }
                if ($(check).parents("tr.file1").length) {
                    var type = "file1";
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
            if (count > 1) {
                $(menu).find(".nav-item.allow-all").show();
            }
            if (count == 0) {
                $("#filemanager").data("buffer", undefined);
                $("#filemanager").data("bufferpath", undefined);
                $("#filemanager .allow-buffer").hide();
            }
            return false;
        });


        $("#filemanager").delegate(".breadcrumb a", "click", function(e) {
            var idx = $(this).parents("li").attr("idx");
            var path = "";
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
    });

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
        $("#filemanagerTabs").find(".nav-item,.nav-link").removeClass("active");
        $("#filemanagerTabs .nav-item").each(function() {
            if ($(this).data("file") == file) {
                $(this).addClass("active");
                $(this).find(".nav-link").addClass("active");
                res = true;
            }
        });

        if (res == false) {
            var fname = explode("/", file);
            var fname = fname[fname.length - 1];
            var tab = $($("#filemanagerTabs").data("tab"));
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
            var data = json_decode(data);
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

</script>
