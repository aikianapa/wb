<h6 class="element-header">
   Файловый менеджер
   <button class="btn btn-sm btn-success pull-right" data-wb-ajax="" data-wb-append="body">
     <i class="fa fa-plus"></i>
   </button>
</h6>
<div class="col-12"  id="filemanager">
        <div class="content-wrapper">
          <div class="content-left">
            <button class="btn btn-default btn-block mg-b-20">Загрузить файл</button>

            <label class="content-left-label">Действия</label>
            <ul class="nav mg-t-1-force">
              <li class="nav-item">
                <a href="" class="nav-link">
                  <i class="icon ion-ios-folder-outline"></i>
                  <span>Новая директория</span>
                </a>
              </li><!-- nav-item -->
              <li class="nav-item">
                <a href="" class="nav-link">
                  <i class="icon ion-ios-folder-outline"></i>
                  <span>Новый файл</span>
                </a>
              </li><!-- nav-item -->
              <li class="nav-item">
                <a href="" class="nav-link">
                  <i class="icon ion-ios-folder-outline"></i>
                  <span>Переименовать</span>
                </a>
              </li><!-- nav-item -->
              <li class="nav-item">
                <a href="" class="nav-link">
                  <i class="icon ion-ios-folder-outline"></i>
                  <span>Удалить</span>
                </a>
              </li><!-- nav-item -->
              <li class="nav-item">
                <a href="" class="nav-link">
                  <i class="icon ion-ios-folder-outline"></i>
                  <span>Редактировать</span>
                </a>
              </li><!-- nav-item -->
              <li class="nav-item">
                <a href="" class="nav-link">
                  <i class="icon ion-ios-folder-outline"></i>
                  <span>Архивировать</span>
                </a>
              </li><!-- nav-item -->
              <li class="nav-item">
                <a href="" class="nav-link">
                  <i class="icon ion-ios-folder-outline"></i>
                  <span>Разахивировать</span>
                </a>
              </li><!-- nav-item -->
              <li class="nav-item">
                <a href="" class="nav-link">
                  <i class="icon ion-ios-folder-outline"></i>
                  <span>Скачать</span>
                </a>
              </li><!-- nav-item -->
              <li class="nav-item">
                <a href="" class="nav-link">
                  <i class="icon ion-ios-folder-outline"></i>
                  <span>Копировать</span>
                </a>
              </li><!-- nav-item -->
              <li class="nav-item">
                <a href="" class="nav-link">
                  <i class="icon ion-ios-folder-outline"></i>
                  <span>Вырезать</span>
                </a>
              </li><!-- nav-item -->
              <li class="nav-item">
                <a href="" class="nav-link">
                  <i class="icon ion-ios-folder-outline"></i>
                  <span>Вставить</span>
                </a>
              </li><!-- nav-item -->
            </ul>
          </div><!-- content-left -->
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
            </div><!-- content-body-header -->

              <!--ul id="list" class="row" data-wb-role="foreach" data-wb-from="result" data-wb-tpl="false" data-wb-size="false" data-wb-hide="wb">
                <li class="col-12 {{type}}{{link}} {{ext}}" data-name="{{name}}">{{name}}</li>
              </ul-->

            <table id="list" class="table table-striped mg-b-0" data-wb-role="foreach" data-wb-from="result" data-wb-tpl="false" data-wb-size="false" data-wb-hide="wb">
              <tr class="col-12 {{type}}{{link}} {{ext}}" data-name="{{name}}">
                <td class="valign-middle">
                  <label class="ckbox mg-b-0">
                    <input type="checkbox"><span></span>
                  </label>
                </td>
                <td class="col">
                  <i class="fa {{type}} {{ext}} tx-22 tx-primary lh-0 valign-middle"></i>
                  <span class="pd-l-5">{{name}}</span>
                </td>
                <td class="hidden-xs-down">{{ext}}</td>
                <td class="dropdown">
                  <a href="#" data-toggle="dropdown" class="btn pd-y-3 tx-gray-500 hover-info"><i class="icon ion-more"></i></a>
                  <div class="dropdown-menu dropdown-menu-right pd-10">
                    <nav class="nav nav-style-1 flex-column">
                      <a href="" class="nav-link">Info</a>
                      <a href="" class="nav-link">Download</a>
                      <a href="" class="nav-link">Rename</a>
                      <a href="" class="nav-link">Move</a>
                      <a href="" class="nav-link">Copy</a>
                      <a href="" class="nav-link">Delete</a>
                    </nav>
                  </div><!-- dropdown-menu -->
                </td>
              </tr>
            </table>



          </div><!-- content-body -->
        </div><!-- content-wrapper -->



<div id="filemanagerModalSrc" class="modal fade hidden" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
      <button class="btn btn-sm btn-default btn-edit-close" data-toggle="modal" data-target="#filemanagerModalSrc"><i class="fa fa-close"></i></button>
    <div class="modal-content">
      <div data-wb-role="include" src="source" id="filemanagerSrc">
      </div>
    </div>
  </div>
</div>
<div class="nav-active-primary mg-t-5" data-wb-append="#filemanagerSrc .source-toolbar">
    <ul class="nav nav-tabs" role="tablist" id="filemanagerTabs">
        <li class="nav-item"><a class="nav-link" href="" data-toggle="tab" aria-expanded="false"> <i class="fa fa-close"></a></i></li>
    </ul>
</div>


<style>
  #filemanager #filemanagerModalSrc .btn-edit-close {position:absolute; right:0; z-index:1;}
  #filemanager #list .fa.back:before {content: "\f077";}
  #filemanager #list .fa.dir:before {content: "\f114"; color: #ffc107;}
  #filemanager #list .fa.dir1:before {content: "\f114"; color: #ffc107;}
  #filemanager #list .fa.file:before {content: "\f016";}
  #filemanager #list .fa.file1:before {content: "\f016";}
  #filemanager #list .fa.php:before {content: "\f1c9";}
  #filemanager #list .fa.css:before {content: "\f1c9";}
  #filemanager #list .fa.scss:before {content: "\f1c9";}
  #filemanager #list .fa.less:before {content: "\f1c9";}
  #filemanager #list .fa.htm:before {content: "\f1c9";}
  #filemanager #list .fa.html:before {content: "\f1c9";}
  #filemanager #list .fa.js:before {content: "\f1c9";}
  #filemanager #list .fa.json:before {content: "\f1c9";}
  #filemanager #list .fa.ini:before {content: "\f1c9";}
  #filemanager #list .fa.txt:before {content: "\f0f6";}
  #filemanager #list .fa.ico:before {content: "\f1c5";}
  #filemanager #list .fa.svg:before {content: "\f1c5";}
  #filemanager #list .fa.png:before {content: "\f1c5";}
  #filemanager #list .fa.gif:before {content: "\f1c5";}
  #filemanager #list .fa.jpg:before {content: "\f1c5";}
  #filemanager #list .fa.jpeg:before {content: "\f1c5";}
  #filemanager #list .fa.tiff:before {content: "\f1c5";}
  #filemanager #list .fa.bmp:before {content: "\f1c5";}
  #filemanager #list .fa.zip:before {content: "\f1c6";}
  #filemanager #list .fa.tar:before {content: "\f1c6";}
  #filemanager #list .fa.gzip:before {content: "\f1c6";}
  #filemanager #list .fa.arj:before {content: "\f1c6";}
  #filemanager #list .fa.rar:before {content: "\f1c6";}
  #filemanager #list .fa.z:before {content: "\f1c6";}
</style>

<script>
// Tree plugin, for more examples you can check out http://www.easyjstree.com/
$(document).ready(function(){
  var editor;
	filemanagerGetDir('');
  $("#filemanagerTabs").data("tab",$("#filemanagerTabs").html());
  $("#filemanagerTabs").html("");
  $("#filemanager").delegate("#list tr","dblclick",function(){
    var path=$("#filemanager #list").data("path");
    if ($(this).is(".dir,.dir1")) {
      filemanagerGetDir(path+"/"+$(this).attr("data-name"));
    }
    if ($(this).is(".file")) {
      filemanagerCallEditor(path+"/"+$(this).attr("data-name"));
    }
    if ($(this).is(".back")) {
      path=explode("/",path);
      path.splice(path.length-1, 1);
      path=implode("/",path);
      filemanagerGetDir(path);
    }
  });
  $("#filemanager").delegate(".breadcrumb a","click",function(e){
    var idx=$(this).parents("li").attr("idx");
    var path="";
    var breadcrumb=$(this).parents(".breadcrumb");
    if (idx!==undefined) {
      for (i=1;i<=idx;i++) {
          path+="/"+$(breadcrumb).find("li:eq("+i+")").attr("data-name");
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
  	$.get("/module/filemanager/getdir/?dir="+urlencode(dir),function(data){
      $("#filemanager #panel").replaceWith(data);
      $("#filemanager #list").data("path",dir);
      $("#filemanager #panel").noSelect();
  		d.resolve();
      wb_ajax_loader_done();
  	});
  	return d;
  }

  function filemanagerCallEditor(file) {
    wb_ajax_loader();
    var res=false;
    $("#filemanagerTabs").find(".nav-item,.nav-link").removeClass("active");
    $("#filemanagerTabs .nav-item").each(function(){
        if ($(this).data("file")==file) {
            $(this).addClass("active");
            $(this).find(".nav-link").addClass("active");
            res=true;
        }
    });

    if (res==false) {
      var fname=explode("/",file);
      var fname=fname[fname.length-1];
      var tab=$($("#filemanagerTabs").data("tab"));
      $(tab).find(".nav-link").prepend(fname).addClass("active");
      $(tab).data("file",file);
      $("#filemanagerTabs").append($(tab));
    }
    filemanagerEditFile(file);

    $("#filemanagerTabs").undelegate(".fa-close","click");
    $("#filemanagerTabs").delegate(".fa-close","click",function(){
        $(this).parents(".nav-item").remove();
        if (!$("#filemanagerTabs .nav-item").length) {
          $("#filemanagerModalSrc").modal("hide");
        } else {
          $("#filemanagerTabs").find(".nav-item,.nav-link").removeClass("active");
          $("#filemanagerTabs").find(".nav-item:eq(0) .nav-link").trigger("click")
          filemanagerStateLoad($("#filemanagerTabs .nav-item:eq(0)"));
        }
    });

    $("#filemanagerTabs").undelegate(".nav-item:not(.active)","click");
    $("#filemanagerTabs").delegate(".nav-item:not(.active)","click",function(){
        var tab=$("#filemanagerTabs .nav-item.active");
        filemanagerStateSave(tab);
        filemanagerStateLoad(this);
    });

    $("#filemanagerSrc").undelegate(".btnSave","click");
    $("#filemanagerSrc").delegate(".btnSave","click",function(){
      filemanagerSave();
    });

    $("#filemanagerSrc").undelegate(".btnFullScr","click");
    $("#filemanagerSrc").delegate(".btnFullScr","click",function(){

      editor.resize();
    });

    $("#filemanagerModalSrc").undelegate(".ace_editor","mouseleave");
    $("#filemanagerModalSrc").delegate(".ace_editor","mouseleave",function(){
        var tab=$("#filemanagerTabs .nav-link.active").parents(".nav-item");
        filemanagerStateSave(tab);
    });

  }

function filemanagerEditFile(file) {
  $.post("/module/filemanager/getfile/",{file:file},function(data){
    editor=wb_call_source($("#filemanagerSrc .ace_editor").attr("id"));
    editor.setValue(data);
    editor.gotoLine(0,0);
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
    if (!$("#filemanagerModalSrc:visible").length)  {$("#filemanagerModalSrc").modal("show");}
    wb_ajax_loader_done();
  });
}

function filemanagerSave() {
  var tab=$("#filemanagerTabs .nav-link.active").parents(".nav-item");
  if ($(tab).length) {
      $.post("/module/filemanager/putfile/",{file:$(tab).data("file"),text:editor.getValue()},function(data){
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
  	$(tab).data("editor",editor.getValue());
  	$(tab).data("editorUndo",editor.getSession().getUndoManager());
    $(tab).data("editorPos",editor.getCursorPosition());
  }
}

function filemanagerStateLoad(tab) {
    if ($(tab).length && $(tab).data("editor")!==undefined) {
    	editor.setValue($(tab).data("editor"));
    	editor.getSession().setUndoManager($(tab).data("editorUndo"));
      var pos=$(tab).data("editorPos");
      editor.gotoLine(pos["row"]+1,pos["column"]);
    }
}
</script>
