<textarea name="text" class="editor">
</textarea>
<script>
$(document).ready(function(){
  if ($("textarea.editor:not(.wb-done)").length) {
		$("textarea.editor:not(.wb-done)").each(function(){
      console.log($(this).attr("name"));
			if ($(this).attr("id")==undefined || $(this).attr("id")=="") {$(this).attr("id",JSON.parse(ajax_getid()));}

			var editor = $(this).ckeditor();
			$(this).addClass("wb-done");
			CKEDITOR.config.extraPlugins = 'youtube';
			CKEDITOR.config.toolbarGroups = [
				{ name: 'document',    groups: [ 'document', 'doctools' ] },
			//    { name: 'editing',     groups: [ 'find', 'selection', 'spellchecker' ] },
				{ name: 'mode' },
				{ name: 'clipboard',   groups: [ 'clipboard', 'undo' ] },
				{ name: 'links' },
				{ name: 'insert' },
				{ name: 'others' },
				'/',
				{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
				{ name: 'paragraph',   groups: [ 'list', 'indent', 'blocks', 'align' ] },
				{ name: 'colors' },
				{ name: 'tools' }
			];
			CKEDITOR.config.skin = 'bootstrapck';
			CKEDITOR.config.allowedContent = true;
			CKEDITOR.config.forceEnterMode = true;
			CKEDITOR.plugins.registered['save']=
			{
			   init : function( editor )
			   {
				  var command = editor.addCommand( 'save',
					 {
						modes : { wysiwyg:1, source:1 },
						exec : function( editor ) {
						   var fo=editor.element.$.form;
						   editor.updateElement();
						   wb_formsave($(fo));
						}
					 }
				  );
				  editor.ui.addButton( 'Save',{label : 'Сохранить',command : 'save'});
			   }
			}
		});
			CKEDITOR.on('instanceReady', function(){
			   $.each( CKEDITOR.instances, function(instance) {
				CKEDITOR.instances[instance].on("change", function(e) {
					for ( instance in CKEDITOR.instances )
					$("textarea#"+instance).html(CKEDITOR.instances[instance].getData());
					$("textarea#"+instance).trigger("change");
				});
			   });
			});
	}
});
</script>
