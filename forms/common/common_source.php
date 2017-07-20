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
  <meta id="source123SourceEditorMeta" />
  <textarea class="sourceEditor" id="source123SourceEditor" name="text"></textarea>
</div>

<style type="text/css" media="screen">
  .sourceModal.fullscr, .sourceModal.fullscr .modal-dialog {width:100% !important; padding:0 !important; margin:0 !important;}
  .sourceModal.fullscr .modal-body {padding:0;}
  .sourceModal.fullscr .modal-header, .sourceModal.fullscr .modal-footer {display:none;}
  .sourceModal.fullscr .nav {display:none;}
  .sourceEditor {position: relative;}
</style>

<script language="javascript">
$("#source123Edit").addClass("sourceModal");
$(document).data("sourceFile",null);
var theme=getcookie("sourceEditorTheme");
var fsize=getcookie("sourceEditorFsize")*1;
var source="&nbsp;";
var fldname="";
var form="source123";
if ($("#text").length) {source=$("#text").val();}

if (theme==undefined || theme=="") {var theme="ace/theme/chrome"; 	setcookie("sourceEditorTheme",theme);}
if (fsize==undefined || fsize=="") {var fsize=12; 					setcookie("sourceEditorFsize",fsize);}
if ($(document).data("sourceClipboard")==undefined) {$(document).data("sourceClipboard","");}
  editor=aikiCallSourceEditor(form);
editor.setTheme(theme);
editor.setFontSize(fsize);
editor.setValue(source);
editor.gotoLine(0,0);

$("#source123EditForm [data-toggle=tab],#source123Edit [data-formsave]").click(function(){
  var text="" ; var txt="text";
  if ($("#source123SourceEditorMeta").parents("[id][data-name]").length) {
    var txt=$("#source123SourceEditorMeta").parents("[id][data-name]").attr("data-name");
  }
  if ($("#source123Edit [name="+txt+"]").length) {
    var text=$("#source123Edit [name="+txt+"]").val();
  }
  if ($("#source123EditForm").parent("li").hasClass("active")) {$("#source123Edit [name="+txt+"]").val(editor.getValue());} else {
      if ($("#cke_text .cke_contents")) {var ace_height=$("#cke_text .cke_contents").height();} else {var ace_height=500;}
      if (ace_height<500) {var ace_height=500;}
      $(".ace_editor").css("height",ace_height);
      editor.getSession().setMode("ace/mode/php");
      editor.setValue(text);
      editor.gotoLine(0,0);
      editor.resize(true);
  }

});

function aikiCallSourceEditor(form) {
	if (!$(form).parents(".formDesignerEditor").length) {
		console.log("callSourceEditor");
		var editorName="SourceEditor";
		if (form!==undefined) {editorName=form+editorName;}
		var editor = ace.edit(editorName);
		editor.setTheme("ace/theme/chrome");
		editor.setOptions({
				enableBasicAutocompletion: true,
				enableSnippets: true
		});
		editor.getSession().setUseWrapMode(true);
		editor.getSession().setUseSoftTabs(true);
		editor.setDisplayIndentGuides(true);
		editor.setHighlightActiveLine(false);
		editor.setAutoScrollEditorIntoView(true);
		editor.commands.addCommand({
			name: 'save',
			bindKey: {win: 'Ctrl-S',  mac: 'Command-S'},
			exec: function(editor) {
				$("#sourceEditorToolbar .btnSave").trigger("click");
			},
			readOnly: false
		});
		editor.gotoLine(0,0);
		editor.resize(true);
    active_source_buttons();
		return editor;
	}
}


function active_source_buttons() {
	$(document).undelegate("#sourceEditorToolbar button","click");
	$(document).delegate("#sourceEditorToolbar button","click",function(e){
		var theme=getcookie("sourceEditorTheme");
		var fsize=getcookie("sourceEditorFsize");
		if (theme==undefined || theme=="") {var theme="ace/theme/chrome";	setcookie("sourceEditorTheme",theme);}
		if (fsize==undefined || fsize=="") {var fsize=12; 					setcookie("sourceEditorFsize",fsize);}

		//if ($(this).hasClass("btnCopy")) 		{$(document).data("sourceFile",editor.getCopyText());}
		//if ($(this).hasClass("btnPaste")) 		{editor.insert($(document).data("sourceFile"));}
		if ($(this).hasClass("btnCopy")) 		{$(document).data("sourceClipboard",editor.getCopyText());}
		if ($(this).hasClass("btnPaste")) 		{editor.insert($(document).data("sourceClipboard"));}
		if ($(this).hasClass("btnUndo")) 		{editor.execCommand("undo");}
		if ($(this).hasClass("btnRedo")) 		{editor.execCommand("redo");}
		if ($(this).hasClass("btnFind")) 		{editor.execCommand("find");}
		if ($(this).hasClass("btnReplace")) 	{editor.execCommand("replace");}
		if ($(this).hasClass("btnLight")) 		{editor.setTheme("ace/theme/chrome"); setcookie("sourceEditorTheme","ace/theme/chrome");}
		if ($(this).hasClass("btnDark")) 		{editor.setTheme("ace/theme/monokai");  setcookie("sourceEditorTheme","ace/theme/monokai");}
		if ($(this).hasClass("btnClose")) 	{
			editor.setValue("");
			$(document).data("sourceFile",null);
			$("#sourceEditorToolbar .btnSave").removeClass("btn-danger");
		}
		if ($(this).hasClass("btnFontDn")) 	{
			if (fsize>8) {fsize=fsize*1-1;}
			editor.setFontSize(fsize); setcookie("sourceEditorFsize",fsize);
		}
		if ($(this).hasClass("btnFontUp")) 	{
			if (fsize<20) {fsize=fsize*1+1;}
			editor.setFontSize(fsize); setcookie("sourceEditorFsize",fsize);
		}
		if ($(this).hasClass("btnFullScr")) 	{
			var div=$(this).parents("#sourceEditorToolbar").parent();
			var offset=div.offset();
			if (!div.hasClass("fullscr")) {
				div.parents(".modal").addClass("fullscr");
				div.addClass("fullscr");
				$(this).parents(".modal").css("overflow-y","hidden");
				div.find("pre.ace_editor").css("height",$(window).height()-$("#sourceEditorToolbar").height()-$("#sourceEditorToolbar").next(".nav").height()-15);
			} else {
				div.removeAttr("style");
				div.find("pre.ace_editor").css("height","500px");
				div.removeClass("fullscr");
				div.parents(".modal").removeClass("fullscr");
				$(this).parents(".modal").css("overflow-y","auto");
			}
			window.dispatchEvent(new Event('resize'));
		}
		if ($(this).hasClass("btnSave")) 	{
			var fo=$(this).parents("#sourceEditorToolbar").parents("form");
			aiki_formsave($(fo));
		}
		e.preventDefault();
	});
}


</script>
