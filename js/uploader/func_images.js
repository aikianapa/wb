$(document).ready(function(){
	$(document).on("wb_ajax_done",function(event,target,ajax){
		wbCommonUploader();
	});

});

function wbCommonUploader() {
	$(document).find(".wb-uploader:not(wb-done)").each(function(){
        $(this).wbUploaderInit();
	});
}

jQuery.fn.wbUploaderInit = function() {
		var id=$(this).attr("id");
		var uid="upl-"+$(this).attr("id");
		var store=$('#'+ id + ' > input[name]');
		var path=$(this).attr("data-wb-path");
		var max=store.attr("data-wb-max"); if (max==undefined) {max=30;}
		var ext=store.attr("data-wb-ext");
		if (ext>"") {
			var ext=explode(" ",ext);
			var types=[];
			for(i=0; i<ext.length; i++) {types.push({title : ext[i], extensions : ext[i]});}
		} else {
			var types= [
					{title : "Image files", extensions : "jpg,gif,png"},
					{title : "Zip files", extensions : "zip"},
					{title : "Pdf files", extensions : "pdf"}
				]
		}
		$(this).find(".uploader").attr("id",uid);
		$(this).addClass("wb-done");

		var uploader = new plupload.Uploader({
		runtimes : 'html5,html4',
		browse_button : 'pickfiles', // you can pass in id...
		container: document.getElementById(uid), // ... or DOM Element itself
		url : '/engine/js/uploader/upload.php?path='+path,
		dragdrop: true,
		chunk_size : '1mb',
		unique_names : true,
		//resize : {width : 320, height : 240, quality : 90},
		filters : {
			max_file_size : max+'mb',
			mime_types: types
		},
		init: {
			PostInit: function() {
				$('#'+id+' #filelist').html("");
				$('#'+id+' #uploadfiles').click(function() {
					uploader.start();
					return false;
				});
				$("#"+id).data("images",store.val());
				$("#"+id+" ul.gallery").sortable({	update: function() { wbImagesToField(id); }	});
				$("#"+id).wbImagesEvents();
                $("#"+id).wbUploaderResizer();
			},
			FilesAdded: function(up, files) {
				plupload.each(files, function(file) {
					$('#'+id+' #filelist').append('<div class="list-group-item" id="' + file.id + '"><span class="glyphicon glyphicon-upload"></span>&nbsp;<b>' + file.name + '</b>  <span class="badge">' + plupload.formatSize(file.size) + '</span></div>');
				});
				uploader.start();
			},
			FileUploaded: function(up, file, res) {
				var res=$.parseJSON(res.response);
				$('#'+id+' #filelist #'+file.id).remove();
				var name=res.id.toLowerCase();
				wbImagesAddToList(id,name);
				$(".wbImagesAll ul.gallery li:last").trigger("click");
                $("#"+id).wbUploaderResizer();
                $("#"+id).children("input").trigger("change");
			},
			UploadProgress: function(up, file) {
				document.getElementById(file.id).getElementsByTagName('b')[0].innerHTML = '<span>' + file.percent + "%</span>";
			},
			Error: function(up, err) {
				document.getElementById('console').innerHTML += "\nError #" + err.code + ": " + err.message;
			}
		}
		});
		uploader.init(); 
}


jQuery.fn.wbUploaderResizer = function() {
   if ($(this).hasClass("single")) {
        var img=$(this).find(".gallery > li:first-child > img");
        if ($(img).length) {
            var height=$(img).attr("height");
            var width=$(img).attr("width");
            $(img).css("height",height).css("width",width);
            $(this).find(".moxie-shim").css({"height":height,"width":width,"background":"transparent"});
            $(this).find(".gallery").css({"height":"auto","width":"auto"});
        } else {
            $(this).find(".moxie-shim").css({"height":"120px","width":"160px"});
            $(this).find(".gallery").css({"height":"120px","width":"160px"});

        }
   }
}

jQuery.fn.wbImagesEvents = function() {
        var id=$(this).attr("id");
		$("#"+id+" .wbImagesAll").off("mouseenter","ul li");
		$("#"+id+" .wbImagesAll").on ("mouseenter","ul li",function(){
			$(this).find(".dropdown-menu").remove();
			$(this).find("a.delete").after($("#"+id+" .wbImagesAll").find(".wbImagesDropdownTpl").html());
		});

		$("#"+id+" .wbImagesAll").off("mouseleave","ul li");
		$("#"+id+" .wbImagesAll").on ("mouseleave","ul li",function(){
			if ($(this).find(".dropdown-menu:visible").length) {
				//$(this).find("a.delete").trigger("click");
				$(this).find(".dropdown-menu").remove();
			}
		});

		$("#"+id+" .wbImagesAll").off("click","ul:first li a.delete-confirm");
		$("#"+id+" .wbImagesAll").on ("click","ul:first li a.delete-confirm",function(){
			var name=$(this).parents("li.thumbnail").attr("data-name");
			var path=$('#'+id).attr("data-wb-path");
			var that=$(this);
			$.get("/ajax/remove/"+path+"/"+name,function(data){
				$(that).parents("li").tooltip().remove();
				if (JSON.parse(data)==true) {
					that.parents("li.thumbnail").remove();
				} else {
					if (confirm("Ошибка удаления! Убрать превью?")) {
					  that.parents("li.thumbnail").remove();
					}
				}
				wbImagesToField(id);
			});
			return false;
		});

		$("#"+id+" .wbImagesAll").delegate("ul li input, ul li textarea","click",function(){
			return false;
		});
		$("#"+id+" .wbImagesAll").delegate("ul li input, ul li textarea","keyup",function(){
			return false;
		});

		$("#"+id+" .wbImagesAll").delegate(".imagesAttr .close","click",function(){
			$(this).parents("#"+id+" .wbImagesAll").prepend($(this).parents(".imagesAttr"));
			return false;
		});

		$("#"+id+" .wbImagesAll").off("click","ul li a.info");
		$("#"+id+" .wbImagesAll").on("click","ul li a.info",function(){
			if ($(this).parents("li").next("li").is(".imagesAttr")) {
				$("#"+id+" .wbImagesAll .imagesAttr .close").trigger("click");
			} else {
				$(this).parent("li").after($(this).parents("#"+id+" .wbImagesAll").find(".imagesAttr"));
				var imginfo=$(this).parents("#"+id+" .wbImagesAll").find(".imagesAttr");
				var imgnum=$(this).parent("li").index();
				var imgname=$(this).parent("li").attr("data-name");
				var imgpath=$('#'+id).attr("data-wb-path")+"/"+imgname;

				$("#"+id+" .wbImagesAll").data("imgnum",imgnum);
				imginfo.find("textarea,input").val("");
				imginfo.find(".attr-link").val(imgpath);
				imginfo.find(".attr-alt").val($(this).parent("li").attr("alt"));
				imginfo.find(".attr-title").val($(this).parent("li").attr("title"));
				//var formname=$(this).parents("#"+id+" .wbImagesAll").parents("form[role=form]").attr("name");
				//var itemname=$(this).parents("#"+id+" .wbImagesAll").parents("form[role=form]").attr("item");
			}
			return false;
		});

		$("#"+id+" .wbImagesAll").delegate(".imagesAttr .attr-alt, .imagesAttr .attr-title","focusout",function(){
			var imgnum=$("#"+id+" .wbImagesAll").data("imgnum");
			if ($(this).is(".attr-title")) {
				$("#"+id+" .wbImagesAll ul.gallery li.thumbnail:eq("+imgnum+")").attr("title",$(this).val());
			}
			if ($(this).is(".attr-alt")) {
				$("#"+id+" .wbImagesAll ul.gallery li.thumbnail:eq("+imgnum+")").attr("alt",$(this).val());
			}
			wbImagesToField(id);
		});


		$("#"+id+" .wbImagesAll").off("click","ul.gallery li.thumbnail");
		$("#"+id+" .wbImagesAll").on ("click","ul.gallery li.thumbnail",function(){
			if ($(this).hasClass("selected")) {$(this).removeClass("selected");} else {$(this).addClass("selected");}
			wbImagesToField(id);
		});

}


function wbImagesList(id) {
	var path=$('#'+id).attr("data-wb-path");
	$.get("/ajax/listfiles/"+path,function(data){
		var store=$('#'+ id + ' > input[name]');
		var gallery=[];//JSON.parse(data);
		var images=store.val();
		var ext=store.attr("data-wb-ext");
		if (ext!==undefined) {
			ext=trim(str_replace(","," ",ext));
			var exts=explode(" ",ext);
		} else {
			var exts=new Array("jpg","png","gif","pdf");
		}
		$("#"+id).data("images",images);
		if (images!=="") {
			images=JSON.parse(images);
			$(images).each(function(i){
				var ext=images[i]["img"].split('.');
				var ext=ext[ext.length-1].toLowerCase();
				if (in_array(images[i]["img"],gallery)) {gallery.splice(array_search(images[i]["img"],gallery),1);}
				if (in_array(ext,exts)) {wbImagesAddToList(id,images[i]["img"],true);}
			});
		}
		$(gallery).each(function(i){
			var ext=gallery[i].split('.');
			var ext=ext[ext.length-1].toLowerCase();
			if (in_array(ext,exts)) {wbImagesAddToList(id,gallery[i],false);}
		});
		wbImagesSort(id);
		$("#"+id+" ul.gallery").sortable({	update: function() { wbImagesToField(id); }	});
	});
}

function wbImagesSort(id) {
	var store=$('#'+ id + ' > input[name]');
	var images=store.val();
	if (images=="") {var images=[];} else {var images=JSON.parse(images);}
	$("#"+id+" ul.gallery").after("<ul class='tmp' style='display:none;'></ul>");
	$(images).each(function(i,img){
		var name=img["img"];
		that=$("#"+id+" ul.gallery > li[data-name='"+name+"']");
		that.attr("alt",img["alt"]);
		that.attr("title",img["title"]);
		if (that.length) {
			if (img.visible==1 || img.visible==undefined) {that.addClass("selected");}
			$("#"+id+" ul.tmp").append(that);
		}
	});
	$("#"+id+" ul.gallery > li").each(function(){
		$(this).addClass("selected");
		$("#"+id+" ul.tmp").append($(this));
	});
	$("#"+id+" ul.gallery").html($("#"+id+" ul.tmp").html());
	$("#"+id+" ul.tmp").remove();
}

function wbImagesToField(id) {
	var images = new Array();
	var store=$('#'+ id + ' > input[name]');
	$("#"+id+" ul li.thumbnail").each(function(i){
		if ($(this).hasClass("selected")) {var sel=1;} else {var sel=0;}
    if ($("#"+id).hasClass("single")) {var sel=1};
		if ($(this).attr("data-name")>"") {
			var img = {
				img: $(this).attr("data-name"),
				title: $(this).attr("title"),
				alt: $(this).attr("alt"),
				visible: sel
			}
			images.push(img);
		}
	});
	store.val(JSON.stringify(images));
	$("#"+id).wbImagesEvents();
    $(store).trigger("change");
}

function wbImagesAddToList(id,name,vis) {
	var single=false; if ($('#'+ id).hasClass("single")) {var single=true;}
	var store=$('#'+ id + ' > input[name]');
	var path="/engine/phpThumb/phpThumb.php?w=250&src="+$("div.imageloader").attr("path");
	var title=""; var alt=""; var visible=1;
	var images=$("#"+id).data("images");

	if (images=="" || single==true) {var images=[];} else {var images=JSON.parse(images);}
	$(images).each(function(i,img){
		if (img["img"]==name) {title=img["title"]; alt=img["alt"]; visible=img["visible"];}
	});
	var form=$('#'+id).attr("data-wb-form");
	var item=$('#'+id).attr("data-wb-item");
	var path=$('#'+id).attr("data-wb-path");
	var tplid=$("#"+id+" ul.gallery").attr("data-wb-tpl");
	var thumbnail=wb_setdata("#"+tplid,{form:form,id:item,"%path":path,img:name},true);
	if (single==true) {$("#"+id+" ul.gallery").find("li").remove();}
	if (!$("#"+id+" ul.gallery li[data-name='"+name+"']").length) {$("#"+id+" ul.gallery").append(thumbnail);}
	if (single==true) {wbImagesToField(id);}
	$("#"+id).wbImagesEvents();
}
