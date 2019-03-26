var $=jQuery.noConflict();
function wbModFormBuilder() {
	var $fb = $("#modFormBuilder")
	var $fbm = $("#modFormBuilderModal");
	var lang = wbapp.getlocale("file","/engine/modules/formbuilder/formbuilder_ui.php");
	var $editor = $("#modFormBuilderPanel textarea").wbSource({height:500});
	var formname;
	var filename;
	var formtype;
	$("#modFormBuilder").disableSelection();
	$('#modFormBuilderMenu, #modFormBuilderPanel').perfectScrollbar({
		useBothWheelAxes: false,
		suppressScrollX: true,
		wheelPropogation: true
	});

	//$("body").addClass("hide-left");

	$(document).undelegate("#modFormBuilderCreator","click");
	$(document).delegate("#modFormBuilderCreator","click",function(){
		$fbm.find(".modal-title").html(lang.createform);
		$fbm.find(".modal-body").html($fb.find("#_modCreateForm").html());
		$fbm.modal("show");
	});


	$fbm.delegate("button.btn-success","click",function(){
		var formname=strtolower($fbm.find("input[name=formname]").val());
		if (formname=="" || $("#modFormBuilderSelectForm").find("option[value='"+formname+"']").length) {
			alert("Error!");
			return;
		} else {
			wbapp.postWait("/module/formbuilder/create/"+formname,{},function(data){
				data=$.parseJSON(data);
				if (data==true) {
					$fbm.modal("hide");
					$("#modFormBuilderSelectForm").append("<option value='"+formname+"'>"+formname+"</option>");
				}
			});
		}
	});

	$('[href="#modFormBuilderView"]').click(function(){
		fromEditorToView();
	});

	$(document).undelegate("#modFormBuilderSelectForm","change");
	$(document).delegate("#modFormBuilderSelectForm","change",function(){
		$("#modFormBuilderView").html("");
		$editor.getSession().setValue("");
		var formname=strtolower($(this).val());
		wbapp.postWait("/module/formbuilder/getmodelist/"+formname,{},function(data){
			data=$.parseJSON(data);
			var tpl=$("#_modFormBuilderFilesList").html();
			$("#modFormBuilderFilesList ul li").remove();
			$.each(data,function(i,item){
				var file=str_replace("{{file}}",item.filename,tpl);;
				$("#modFormBuilderFilesList ul").append($(file));
				$("[href='#modFormBuilderSnippets']").addClass("disabled");
				var $last=$("#modFormBuilderFilesList ul li:last-child");
				$last.attr("data-path",item.filepath);

				var name=$last.text();
				if (name.indexOf("_") < 0) {
					$last.find("i.fa").attr("class","fa fa-code");
					$last.attr("data-type","prog");
				} else if (name.indexOf(".ini") > 0) {
					$last.find("i.fa").attr("class","fa fa-language");
					$last.attr("data-type","locale");
				} else {
					$last.attr("data-type","form");
				}
			});

		});
	});

	$("#modFormBuilderSave .btn").on("click",function(){
		if ($(this).is(".btn-success")) {
			formViewSave();
			$($("#modFormBuilderSave").data("click")).trigger("click");
			$("#modFormBuilderSave").data("click",false);
		} else if ($(this).is(".btn-danger")) {
			$("#modFormBuilderView").data('_changed',false);
			$("#modFormBuilderView").data('_editor',false);
			$($("#modFormBuilderSave").data("click")).trigger("click");
		}
		$("#modFormBuilderSave").modal("hide");
	});

	$("#modFormBuilderSaveBtn").on("click",function(){
		$("#modFormBuilderSave .btn").trigger("click");
	});

	$("#modFormBuilderCode .mod_editarea_toolbar .btnSave").off("click");
	$("#modFormBuilderCode .mod_editarea_toolbar .btnSave").on("click",function(){
		$("#modFormBuilderSave .btn").trigger("click");
	});

	$fb.delegate("#modFormBuilderFilesList li[data-type] a","click",function(){
		fromEditorToView();
		if ($("#modFormBuilderView").data('_changed') == true) {
			$("#modFormBuilderSave").data("click",this);
			$("#modFormBuilderSave").modal("show");
			return;
		}
		$("#modFormBuilderFilesList ul a.nav-link").removeClass("active");
		$(this).addClass("active");
		formtype = $(this).parent("li").attr("data-type");
		formname = $("#modFormBuilderSelectForm").val();
		formfile = $(this).find("span").text();

		$("#modFormBuilderView").wbChangeWatcher("stop");
		wbapp.postWait("/module/formbuilder/getform/"+formname,{formFile:formfile},function(data){
			var form = $.parseJSON(base64_decode(data));
			$("#modFormBuilderView").html("");
			if (formtype == "form") {
				//form=str_replace("{{_form}}","__"+formname+"__",form);
				//form=str_replace("{{_mode}}","__mode__",form);
				//form=str_replace("{{_item}}","__item__",form);
				form = "<div>"+form+"</div>";
				$("#modFormBuilderView").html($(form).html());
				$editor.getSession().setValue($("#modFormBuilderView").html());
				$("[href='#modFormBuilderView']").removeClass("disabled");
				$("[href='#modFormBuilderSnippets']").removeClass("disabled");
				$("[href='#modFormBuilderView']").trigger("click");
				initsort();
			} else {
				$editor.getSession().setValue(form);
				$("[href='#modFormBuilderView']").addClass("disabled");
				$("[href='#modFormBuilderSnippets']").addClass("disabled");
				$("[href='#modFormBuilderCode']").trigger("click");
			}
			$editor.getSession().off("change");
			$editor.getSession().on("change", function() {
				$("#modFormBuilderView").data("_editor",true);
				$("#modFormBuilderSaveBtn").addClass("btn-success");
			});

			setTimeout(function(){
				$("#modFormBuilderView").data("_editor",false);
				$("#modFormBuilderView").wbChangeWatcher("start");
				$("#modFormBuilderSaveBtn").removeClass("btn-success");
				$("#modFormBuilderView").off('change');
				$("#modFormBuilderView").on('change',function(){
					$("#modFormBuilderSaveBtn").addClass("btn-success");
				});
			},200);


			function initsort() {
				$("#modFormBuilderView").sortable({
					connectToSortable: '#modFormBuilderView',
					items: 'div.row, div[class*="col-"], .form-group, table, a:not(.with-sub), ul, li',
					cursor: 'move',
					placeholder: 'modFormBuilderPlaceholder',
					stop: function(event, ui) {
						fromViewToEditor();
					},
					helper: "clone"
				});
				$("#modFormBuilder").sortable({
					connectWith: '#modFormBuilderView',
					containment: '#modFormBuilderView',
					placeholder: 'modFormBuilderPlaceholder',
					items: 'a.wb-mod-snippet',
					cursor: 'move',
					remove: function (e, ui) {
						copyHelper = ui.item.clone().insertAfter(ui.item);
						$(this).sortable('cancel');
						return ui.item.clone();
					},
					stop: function(event, ui) {
						$("#modFormBuilderView .wb-mod-snippet").replaceWith($("#modFormBuilderView .wb-mod-snippet").html());
						$editor.getSession().setValue($("#modFormBuilderView").html());
					},
					helper: 'clone'
				});
			}
		});
	});

	function fromEditorToView() {
		if ($("#modFormBuilderView").data("_editor") == true) {
			$("#modFormBuilderView").html($editor.getSession().getValue());
			$("#modFormBuilderView").data("_editor",false);
		}
	};

	function fromViewToEditor() {
		$editor.getSession().setValue($("#modFormBuilderView").html());
	};

	function formViewSave() {
		var code=$editor.getSession().getValue();
		if (formtype=="form") {
			//code=str_replace("__"+formname+"__","{{_form}}",code);
			//code=str_replace("__mode__","{{_mode}}",code);
			//code=str_replace("__item__","{{_item}}",code);
		}
		wbapp.postWait("/module/filemanager/putfile/",{file:"/forms/"+formname+"/"+formfile,text:code},function(){
			$("#modFormBuilderView").data('_changed',false);
			$("#modFormBuilderView").data('_editor',false);
			$("#modFormBuilderSaveBtn").removeClass("btn-success");
		});
	};
}
wbModFormBuilder();
