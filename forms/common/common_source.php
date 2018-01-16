<div class="card card-default clearfix">
  <div id="sourceEditorToolbar" class="source-toolbar card-header col-12">
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
    <button class="btn btn-sm btn-default btnFullScr"><i class="fa fa-arrows-alt "></i></button>
    <button class="btn btn-sm btn-default btnSave"><i class="fa fa-save "></i></button>
  </div>
  <textarea class="source col-12"></textarea>
</div>

<style type="text/css" media="screen">
  .sourceModal.fullscr, .sourceModal.fullscr .modal-dialog {width:100% !important; padding:0 !important; margin:0 !important;}
  .sourceModal.fullscr .modal-body {padding:0;}
  .sourceModal.fullscr .modal-header, .sourceModal.fullscr .modal-footer {display:none;}
  .sourceModal.fullscr .nav {display:none;}
  .sourceEditor {position: relative;}
  .source .card-header {padding:0.2em;}
</style>

<script language="javascript">

if ($("textarea.source:not(.wb-done)").length) {
		$("textarea.source:not(.wb-done)").each(function(){
			wb_call_source($(this).attr("id"));
		});
}

</script>
