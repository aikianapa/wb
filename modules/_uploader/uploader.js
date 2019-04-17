jQuery.fn.fileUploader = function() {
		var id=$(this).attr("id");
		var uid="upl-"+id;
		var $store=$('#'+ id + ' > input[name]');
		var path=$(this).attr("data-path");

		var callback=$(this).attr("data-callback");
		var max=$.attr("data-max"); if (max==undefined) {max=10000;}
		var ext=$store.attr("data-ext");
		if ($('#'+id+' .console').attr("id")==undefined) $('#'+id+' .console').attr("id",id+"-console");
		if ($('#'+id+' .upl-list').attr("id")==undefined) $('#'+id+' .upl-list').attr("id",id+"-list");
		if ($('#'+id+' .upl-find').attr("id")==undefined) $('#'+id+' .upl-find').attr("id",id+"-find");
		if ($('#'+id+' .upl-upload').attr("id")==undefined) $('#'+id+' .upl-upload').attr("id",id+"-upload");
		if ($('#'+id+' .upl-drop').attr("id")==undefined) $('#'+id+' .upl-drop').attr("id","#"+id+"-drop");
		var $listfiles = $("#"+$('#'+id+' .upl-list').attr("id"));
		var pickfiles = $('#'+id+' .upl-find').attr("id");
		var droparea = $('#'+id+' .upl-drop').attr("id");
		var $btn_upload = $("#"+id+' .upl-upload');
		var $gallery = $('#'+id+' .gallery')
		var $uploader = $(this);

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

		var uploader = new plupload.Uploader({
			runtimes : 'html5,html4',
			browse_button : pickfiles, // you can pass in id...
			drop_element: droparea,
			container: document.getElementById(uid), // ... or DOM Element itself
			url : '/libs/uploader/upload.php?path='+$(this).attr("data-path"),
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
				$listfiles.html("");
				$btn_upload.click(function() {
					uploader.start();
					return false;
				});
				$("#"+id).data("images",$store.val());
				$("#"+id+" ul.gallery").sortable({update: function() { $uploader.trigger("files_sorted"); }});
				//$("#"+id).wbImagesEvents();
				//$("#"+id).wbUploaderResizer();
			},
			FilesAdded: function(up, files) {
				plupload.each(files, function(file) {
					$gallery.prepend('<div class="list-group-item" id="' + file.id + '"><span class="fa fa-upload"></span>&nbsp;<b>' + file.name + '</b>  <span class="badge">' + plupload.formatSize(file.size) + '</span></div>');
				});
				uploader.start();
			},
			FileUploaded: function(up, file, res) {
				var res=$.parseJSON(res.response);
				$gallery.find('#'+file.id).remove();
				$uploader.trigger("file_uploaded",res);
			},
			UploadProgress: function(up, file) {
				document.getElementById(file.id).getElementsByTagName('b')[0].innerHTML = '<span>' + file.percent + "%</span>";
			},
			Error: function(up, err) {
				document.getElementById(id+"-console").innerHTML += "\nError #" + err.code + ": " + err.message;
			}
		}
		});

		uploader.bind('Init', function(up, params) {
		if (uploader.features.dragdrop) {
		  $('debug').innerHTML = "";

		  var target = $("drop-target");

		  target.ondragover = function(event) {
		    event.dataTransfer.dropEffect = "copy";
		  };

		  target.ondragenter = function() {
		    this.className = "dragover";
		  };

		  target.ondragleave = function() {
		    this.className = "";
		  };

		  target.ondrop = function() {
		    this.className = "";
		  };
		}
		});


		uploader.init();
		uploader_events();
		gallery_init();



		function gallery_init() {
			$gallery.data("tpl",$gallery.find(".img-block").clone());
			$gallery.find(".img-block").remove();
			$uploader.trigger("gallery_show");
		};

		function get_store() {
			var res=$store.val();
			if (res>"") {
				var res=JSON.parse($store.val());
			} else {
				res=[];
			}
			if (res==null) {res=[];}
			return res;
		};

		function set_store(arr) {
			$store.val(JSON.stringify(arr));
		};


		function uploader_events() {

			$uploader.on("gallery_show",function(e){
				var images=get_store();
				$(images).each(function(i,img){
					var $tpl=$($gallery.data("tpl")).clone();
					$tpl.data("img",img);
					$tpl.find("img").attr("src",img.img);
					$tpl.find("a").attr("href",img.img);
					$gallery.append($tpl);
				});
			});


			$uploader.on("file_uploaded",function(e,res){
				var $tpl=$($gallery.data("tpl")).clone();
				$tpl.find("img").attr("src",res.id);
				$tpl.find("a").attr("href",res.img);
				$gallery.append($tpl);
				var images=get_store();
				var img = {
					img: res.id
					,title: ""
					,alt: ""
					,visible: 1
				}
				$tpl.data("img",img);
				images.push(img);
				set_store(images);
				if (callback!==undefined) {
					eval(callback);
				}
			});

			$uploader.on("files_sorted",function(e,res){
				var images=[];
				$gallery.find(".img-block").each(function(){
					images.push($(this).data("img"));
				});
				set_store(images);
				if (callback!==undefined) {
					eval(callback);
				}
			});
		}
}



