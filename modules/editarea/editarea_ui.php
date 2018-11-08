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
			<!--button class="btn btn-sm btn-default btnFullScr"><i class="fa fa-arrows-alt "></i></button-->
			<button class="btn btn-sm btn-default btnSave"><i class="fa fa-save "></i></button>
	</div>
	<textarea></textarea>
	<input type="hidden">
</div>

<script>
	wbapp.scriptWait("/engine/js/ace/ace.js");
	wbapp.scriptWait("/engine/js/ace/theme-chrome.js");
	wbapp.scriptWait("/engine/js/ace/mode-php.js");
	wbapp.scriptWait("/engine/modules/editarea/editarea.js");
</script>
