		var module_editarea = function(id) {
		$(document).data("wb_source_change",false);
		var taid="#"+id;
		var $edit=$(taid);
		if (!$edit.length) return;
		var height = $edit.attr("height");
		var value = $edit.html();
		var fldname = $edit.attr("name");
		var theme = "chrome";
		var form = $edit.parents("form").attr("data-wb-form");
		var item = $edit.parents("form").attr("data-wb-item");
		var fsize = $(document).data("modEditAreaFsize");
		var file=$("#ed_"+id).attr("data-wb-file");
		if (fsize == undefined || fsize == "") {
			fsize = 12;
			$(document).data("modEditAreaFsize",fsize);
		}
		var locale = wbapp.getlocale("file","/engine/modules/editarea/editarea_ui.php");

		var editor = ace.edit(id);
		$(editor.container).css("height", height).css("margin", 0);

		editor.setTheme("ace/theme/"+theme);
		editor.setOptions({
			enableBasicAutocompletion: true,
			enableSnippets: true
		});
		editor.getSession().setUseWrapMode(true);
		editor.getSession().setUseSoftTabs(true);
		editor.getSession().getUndoManager().markClean()
		editor.setDisplayIndentGuides(true);
		editor.setHighlightActiveLine(false);
		editor.setAutoScrollEditorIntoView(true);
		editor.resize(true);
		editor.getSession().setMode("ace/mode/html");
		editor.setFontSize(fsize);
		if (file!==undefined && file!=="") {
			wbapp.postWait("/module/filemanager/getfile/", {
			    file: file
			}, function(data) {
				value = data;
			});
		} else {
		    if (value=="" && $("[name='" + fldname + "']").length) {
		      value = $("[name='" + fldname + "']").val();
		      $("#"+id+" .mod_editarea_toolbar button.btnSave").remove();
		    }
		    value = html_entity_decode(value);
		    file="";
		}
		editor.getSession().setValue(value);

		editor.gotoLine(0, 0);

		editor.getSession().on("change", function() {
			update();
		});

		$(editor.container).on("mouseleave",function(){
			update();
			var value = editor.getSession().getValue();
			$(document).data("wb_source_change",true);
			$(document).trigger("wb_source_change",{
				form: form,
				field: fldname,
				value: value
			});
		});

		$(document).on("wb_editor_change", function(e, data) {
			if ($(document).data("wb_editor_change")==true) {
				if (data.field == fldname && data.item == item && data.form == form) {
					editor.getSession().setValue(data.value);
				}
				$(document).data("wb_editor_change",false);
				return false;
			}
		});



		function update() {
			var value = editor.getSession().getValue();
			$("#inp"+id).val(value);
		}

		function toolbar() {
			$toolbar = $("#ed_"+id+" .mod_editarea_toolbar");
			$("#ed_"+id+" .mod_editarea_toolbar button").on("click",function(){

    if ($(this).hasClass("btnCopy")) {
      $(document).data("modEditAreaClipboard", editor.getCopyText());
    }
    if ($(this).hasClass("btnPaste")) {
      editor.insert($(document).data("modEditAreaClipboard"));
    }
    if ($(this).hasClass("btnUndo")) {
      editor.execCommand("undo");
    }
    if ($(this).hasClass("btnRedo")) {
      editor.execCommand("redo");
    }
    if ($(this).hasClass("btnFind")) {
      editor.execCommand("find");
    }
    if ($(this).hasClass("btnReplace")) {
      editor.execCommand("replace");
    }
    if ($(this).hasClass("btnLight")) {
      editor.setTheme("ace/theme/chrome");
      setcookie("sourceEditorTheme", "ace/theme/chrome");
    }
    if ($(this).hasClass("btnDark")) {
      editor.setTheme("ace/theme/monokai");
      setcookie("sourceEditorTheme", "ace/theme/monokai");
    }
    if ($(this).hasClass("btnClose")) {
      editor.setValue("");
      $(document).data("sourceFile", null);
      $("#sourceEditorToolbar .btnSave").removeClass("btn-danger");
    }
    if ($(this).hasClass("btnFontDn")) {
	if (fsize > 8) {
		fsize = fsize * 1 - 1;
	}
        editor.setFontSize(fsize);
        $(document).data("modEditAreaFsize",fsize);
    }
    if ($(this).hasClass("btnFontUp")) {
	if (fsize < 20) {
		fsize = fsize * 1 + 1;
	}
	editor.setFontSize(fsize);
	$(document).data("modEditAreaFsize",fsize);
    }
    if ($(this).hasClass("btnFullScr")) {
      var div = $(this).parents($toolbar).parent();
      var modal = $(this).parents(".modal");
      modal.toggleClass("modal-fullscreen");
      if (modal.hasClass("modal-fullscreen")) {
        div.find("pre.ace_editor").height($(window).height() - $(modal).find(".modal-content > .nav-active-primary",0).height() - $toolbar.height() - 15);
      } else {
        div.find("pre.ace_editor").height(400);
      }
      editor.resize();
      //document.getElementById(id).requestFullScreen();
      window.dispatchEvent(new Event('resize'));
    }
    if ($(this).hasClass("btnSave")) {
            $.post("/module/filemanager/putfile/", {
                file: file,
                text: editor.getValue()
            }, function(data) {
		    data=$.parseJSON(data);
                if ($.bootstrapGrowl) {
			if (data.result==true) {
				var type = "success";
			} else {
				var type = "danger";
			}
			var text = locale[data.error];
                    $.bootstrapGrowl(text, {
                        ele: 'body',
                        type: type,
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
                } else {
			alert(text);
		}
            });
    }


				return false;
			});
		}
		toolbar();
	}

$(document).trigger('module_editarea_loaded');
