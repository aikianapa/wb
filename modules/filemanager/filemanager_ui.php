<h6 class="element-header">
    Файловый менеджер
    <button class="btn btn-sm btn-success pull-right" data-wb-ajax="" data-wb-append="body">
     <i class="fa fa-plus"></i>
   </button>
</h6>
<div class="col-12" id="filemanager">
    <div class="content-wrapper">
        <div class="content-left">

        <div id="filemanagerUploader">
            <div class="uploader">
                <button id="pickfiles" class="btn btn-default btn-block mg-b-20">Загрузить файлы</button>
            </div>
        </div>
            
            
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
                <li class="nav-item hidden allow-single allow-file allow-file1" data-no-ext="zip tar arj rar gzip jpg jpeg png gif tif tiff">
                    <a href="#edit" class="nav-link">
                  <i class="fa fa-edit"></i>
                  <span>Редактировать</span>
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
                <!--li class="nav-item hidden allow-single allow-file">
                    <a href="#dnload" class="nav-link">
                  <i class="fa fa-download"></i>
                  <span>Скачать</span>
                </a>
                </li-->
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
                    <td class="hidden-xs">
                        <nobr>{{perms}}</nobr>
                    </td>
                    <td class="hidden-xs">
                        <nobr>{{size}}</nobr>
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
        <meta name="remove" title="Множественное удаление" content="<span class='text-danger'>Выполнить удаление</span> выбранных объектов? {{filename}}">
        <meta name="rendir" title="Переименование директории" content="Переименовать директорию <b>{{_POST[name]}}</b> в: {{dirname}} {{oldname}}" visible="dirname" invisible="oldname">
        <meta name="renfile" title="Переименование файла" content="Переименовать файл <b>{{_POST[name]}}</b> в: {{filename}} {{oldname}}" visible="filename" invisible="oldname">
        <meta name="paste" title="Вставка" content="Некоторые объекты уже существуют в этой директории.<br> Выполнить перезапись существующих объектов?">
        <meta name="zip" title="Архивация" content="Сжать выбранные объекты в архив? {{filename}}" visible="filename">
        <meta name="unzip" title="Распаковка архива" content="Извлечь файлы и папки из архива?<br>Существующие объекты будут перезаписаны.">
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
    
    #filemanager .content-left {
        position:fixed;
        top:inherit;
        left:inherit;
        height: calc( 100vh - 200px );
        overflow-y:auto;
    }

</style>

<script src="/engine/modules/filemanager/filemanager.js?{{_ENV[new_id]}}"></script>
