<div class="card card-default clearfix mod_editarea">
	<div class="mod_editarea_toolbar card-header p-0">
		<div class="btn-group">
			<button class="btn btn-sm btn-default btnCopy"><i class="fa fa-files-o"></i></button>
			<button class="btn btn-sm btn-default btnPaste"><i class="fa fa-clipboard"></i></button>
		</div>
		<div class="btn-group">
			<button class="btn btn-sm btn-default btnUndo"><i class="fa fa-reply"></i></button>
			<button class="btn btn-sm btn-default btnRedo"><i class="fa fa-share"></i></button>
		</div>
		<div class="btn-group">
			<button class="btn btn-sm btn-default btnFind"><i class="fa fa-search"></i></button>
			<button class="btn btn-sm btn-default btnReplace"><i class="fa fa-random"></i></button>
		</div>
		<div class="btn-group">
			<button class="btn btn-sm btn-default btnFontDn"><i class="fa fa-search-minus"></i></button>
			<button class="btn btn-sm btn-default btnFontUp"><i class="fa fa-search-plus"></i></button>
		</div>
		<div class="btn-group">
			<button class="btn btn-sm btn-default btnLight"><i class="fa fa-sticky-note-o"></i></button>
			<button class="btn btn-sm btn-default btnDark"><i class="fa fa-sticky-note"></i></button>
		</div>
			<!--button class="btn btn-sm btn-default btnFullScr"><i class="fa fa-arrows-alt "></i></button>
			<button class="btn btn-sm btn-default btnSave"><i class="fa fa-save "></i></button-->
	</div>
	<textarea></textarea>
	<input type="hidden">
</div>

<script>
	wb_include("/engine/js/ace/ace.js");
	wb_include("/engine/js/ace/theme-chrome.js");
	wb_include("/engine/js/ace/mode-php.js");
	var module_editarea = function(id) {
		var taid="#txt"+id;
		var $edit=$(taid);
		var height = $edit.attr("height");
		var value = html_entity_decode($edit.html());
		var fldname = $edit.attr("name");
		var theme = "chrome";
		var fsize = $(document).data("modEditAreaFsize");
		if (fsize == undefined || fsize == "") {
			fsize = 12;
			$(document).data("modEditAreaFsize",fsize);
		}
		var editor = ace.edit("txt"+id);
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
		editor.setValue(value);
		editor.gotoLine(0, 0);
		$("#inp"+id).val(value);

		editor.getSession().on("change", function() {
			update();
		});

		$(editor.container).on("mouseleave",function(){
			update();
		});

		function update() {
			var value = editor.getSession().getValue();
			$("#inp"+id).val(value);
		}

		function toolbar() {
			$toolbar = $("#"+id+" .mod_editarea_toolbar");
			$("#"+id+" .mod_editarea_toolbar button").on("click",function(){

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
      var div = $(this).parents(toolbar).parent();
      div.parents(".modal").toggleClass("fullscr");
      if (div.parents(".modal").hasClass("fullscr")) {
        var offset = div.find("pre.ace_editor").offset();
        div.find("pre.ace_editor").height($(window).height() - offset.top - 15);
      } else {
        div.find("pre.ace_editor").height(400);
      }
      editor.resize();

      document.getElementById(id).requestFullScreen();

      window.dispatchEvent(new Event('resize'));
    }
    if ($(this).hasClass("btnSave")) {
      var fo = $(this).parents(".modal").find("[data-wb-formsave]").trigger("click");
      //wb_formsave(fo);
    }


				return false;
			});
		}
		toolbar();
	}
	module_editarea(id);
</script>
