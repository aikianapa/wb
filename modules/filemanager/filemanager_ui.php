<h6 class="element-header">
   Файловый менеджер
   <button class="btn btn-sm btn-success pull-right" data-wb-ajax="" data-wb-append="body">
     <i class="fa fa-plus"></i>
   </button>
</h6>
<div class="col-12" id="filemanager">
<div id="panel">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb" data-wb-role="foreach" data-wb-from="path" data-wb-tpl="false" data-wb-size="false" data-wb-hide="wb">
      <li class="breadcrumb-item" data-name="{{0}}"><a href="#">{{0}}</a></li>
      <!--li class="breadcrumb-item active" aria-current="page">Data</li-->
    </ol>
    <li class="breadcrumb-item" data-wb-prepend="nav .breadcrumb"><a href="#" data-name=""><i class="fa fa-home"></i></a></li>
  </nav>

    <ul id="list" class="row" data-wb-role="foreach" data-wb-from="result" data-wb-tpl="false" data-wb-size="false" data-wb-hide="wb">
      <li class="col-12 {{type}}{{link}} {{ext}}" data-name="{{name}}">{{name}}</li>
    </ul>
</div>
</div>

<style>
  #filemanager #list li {
        display:block;
  }
  #filemanager #list li:before {
    display:inline-block;
    width:32px;
    height:32px;
    font-family: FontAwesome;
  }

  #filemanager #list li.back:before {content: "\f077";}
  #filemanager #list li.dir:before {content: "\f114";}
  #filemanager #list li.dir1:before {content: "\f114";}
  #filemanager #list li.file:before {content: "\f016";}
  #filemanager #list li.file1:before {content: "\f016";}
  #filemanager #list li.php:before {content: "\f1c9";}
  #filemanager #list li.css:before {content: "\f1c9";}
  #filemanager #list li.js:before {content: "\f1c9";}
  #filemanager #list li.json:before {content: "\f1c9";}
  #filemanager #list li.ini:before {content: "\f1c9";}
  #filemanager #list li.txt:before {content: "\f0f6";}
  #filemanager #list li.png:before {content: "\f1c5";}
  #filemanager #list li.gif:before {content: "\f1c5";}
  #filemanager #list li.jpg:before {content: "\f1c5";}
  #filemanager #list li.jpeg:before {content: "\f1c5";}
  #filemanager #list li.tiff:before {content: "\f1c5";}
  #filemanager #list li.bmp:before {content: "\f1c5";}
  #filemanager #list li.zip:before {content: "\f1c6";}
  #filemanager #list li.tar:before {content: "\f1c6";}
  #filemanager #list li.gzip:before {content: "\f1c6";}
  #filemanager #list li.arj:before {content: "\f1c6";}
  #filemanager #list li.rar:before {content: "\f1c6";}
  #filemanager #list li.z:before {content: "\f1c6";}
</style>

<script>
// Tree plugin, for more examples you can check out http://www.easyjstree.com/
$(document).ready(function(){
	filemanagerGetDir('');
  $("#filemanager").delegate("#list li","dblclick",function(){
    var path=$("#filemanager #list").data("path");
    if ($(this).is(".dir,.dir1")) {
      filemanagerGetDir(path+"/"+$(this).attr("data-name"));
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
      $("#filemanager *").noSelect();
  		d.resolve();
      wb_ajax_loader_done();
  	});
  	return d;
  }

</script>
