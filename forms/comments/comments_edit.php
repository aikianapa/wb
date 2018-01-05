<div class="modal fade" id="{{_GET[form]}}_{{_GET[mode]}}" data-keyboard="false" data-backdrop="true" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
	<div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h5 class="modal-title">{{header}}</h5>
      </div>
        <div class="modal-body" data-wb-role="include" src="/engine/forms/comments/comments_form.php">
        </div>
		  <div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal"><span class="fa fa-close"></span> Закрыть</button>
			<button type="button" class="btn btn-primary" data-wb-formsave="#{{_GET[form]}}EditForm"><span class="fa fa-check"></span> Сохранить изменения</button>
		  </div>

		</div>
</div>
</div>











<style>
.comments-rating .rating-symbol {font-size:25px; color:#FFA500;}
</style>
