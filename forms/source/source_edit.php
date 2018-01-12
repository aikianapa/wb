	<div class="panel panel-default">
		<div id="sourceEditorToolbar" class="panel-heading">
			<div class="btn-group">
				<button class="btn btn-sm btn-default btnCopy"><i class="fa fa-files-o"></i></button>
				<button class="btn btn-sm btn-default btnPaste"><i class="fa fa-clipboard"></i></button>
			</div>
			<div class="btn-group">
				<button class="btn btn-sm btn-default btnUndo"><i class="gi gi-undo"></i></button>
				<button class="btn btn-sm btn-default btnRedo"><i class="gi gi-redo"></i></button>
			</div>
			<div class="btn-group">
				<button class="btn btn-sm btn-default btnFind"><i class="fa fa-search"></i></button>
				<button class="btn btn-sm btn-default btnReplace"><i class="gi gi-translate"></i></button>
			</div>
			<div class="btn-group">
				<button class="btn btn-sm btn-default btnFontDn"><i class="gi gi-text_smaller"></i></button>
				<button class="btn btn-sm btn-default btnFontUp"><i class="gi gi-text_bigger"></i></button>
			</div>
			<div class="btn-group">
				<button class="btn btn-sm btn-default btnLight"><i class="fa fa-sticky-note-o"></i></button>
				<button class="btn btn-sm btn-default btnDark"><i class="fa fa-sticky-note"></i></button>
			</div>
			<button class="btn btn-sm btn-default btnFullScr"><i class="fa fa-arrows-alt "></i></button>
			<button class="btn btn-sm btn-default btnSave"><i class="fa fa-save "></i></button>
		</div>
		<meta id="{{_GET[form]}}SourceEditorMeta" />
		<textarea class="sourceEditor" id="{{_GET[form]}}SourceEditor"></textarea>
	</div>

<style type="text/css" media="screen">
    .sourceModal.fullscr, .sourceModal.fullscr .modal-dialog {width:100% !important; padding:0 !important; margin:0 !important;}
    .sourceModal.fullscr .modal-body {padding:0;}
    .sourceModal.fullscr .modal-header, .sourceModal.fullscr .modal-footer {display:none;}
    .sourceModal.fullscr .nav {display:none;}
    .sourceEditor {position: relative;}
</style>
<script language="javascript" src="/engine/js/ace/ace.js"></script>
<script language="javascript">
	$("#{{_GET[form]}}Edit").addClass("sourceModal");
	$(document).data("sourceFile",null);
	var theme=getcookie("sourceEditorTheme");
	var fsize=getcookie("sourceEditorFsize")*1;
	var source="&nbsp;";
	var fldname="";
	var form="{{_GET[form]}}";
	if ($("#text").length) {source=$("#text").val();}

	if (theme==undefined || theme=="") {var theme="ace/theme/chrome"; 	setcookie("sourceEditorTheme",theme);}
	if (fsize==undefined || fsize=="") {var fsize=12; 					setcookie("sourceEditorFsize",fsize);}
	if ($(document).data("sourceClipboard")==undefined) {$(document).data("sourceClipboard","");}
    editor=aikiCallSourceEditor(form);
	editor.setTheme(theme);
	editor.setFontSize(fsize);
	editor.setValue(source);
	editor.gotoLine(0,0);

	$("#{{_GET[form]}}EditForm [data-toggle=tab],#{{_GET[form]}}Edit [data-formsave]").click(function(){
		var text="" ; var txt="text";
		if ($("#{{_GET[form]}}SourceEditorMeta").parents("[id][data-name]").length) {
			var txt=$("#{{_GET[form]}}SourceEditorMeta").parents("[id][data-name]").attr("data-name");
		}
		if ($("#{{_GET[form]}}Edit [name="+txt+"]").length) {
			var text=$("#{{_GET[form]}}Edit [name="+txt+"]").val();
		}
		if ($("#{{_GET[form]}}EditForm .call-source").parent("li").hasClass("active")) {$("#{{_GET[form]}}Edit [name="+txt+"]").val(editor.getValue());} else {
				if ($("#cke_text .cke_contents")) {var ace_height=$("#cke_text .cke_contents").height();} else {var ace_height=500;}
				if (ace_height<500) {var ace_height=500;}
				$(".ace_editor").css("height",ace_height);
				editor.getSession().setMode("ace/mode/php");
				editor.setValue(text);
				editor.gotoLine(0,0);
				editor.resize(true);
		}

	});



</script>
