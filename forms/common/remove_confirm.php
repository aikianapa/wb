<div class="modal fade" id="{{_form}}_{{_item}}" data-keyboard="false" data-backdrop="true" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
	<div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h5 class="modal-title">{{_LANG[title]}}</h5>
      </div>
      <div class="modal-body">
		<div class="row">
			<div class="col-3">
				<i class="fa fa-warning fa-4x text-danger"></i>
			</div>
			<div class="col-9">
				{{_LANG[confirm]}}
			</div>
		</div>
          <div class="alert alert-warning text-center hidden" style="margin-top:20px;">{{_LANG[error]}}</div>
      </div>

		  <div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal"><span class="fa fa-close"></span> {{_LANG[cancel]}}</button>
			<button type="button" class="btn btn-danger wb-remove-confirm" data-dismiss="modal"
					 data-wb-ajax="/ajax/rmitem/{{_form}}/{{_item}}/"
					 data-wb-append="body">
					 <span class="fa fa-trash"></span> {{_LANG[remove]}}
			</button>
		  </div>
		</div>
</div>
</div>
<script data-wb-tag="success" language="javascript">
	if ($("[data-wb-table='{{_form}}']").length) {
		$("[data-wb-table='{{_form}}'] [idx='{{_item}}']").remove();
	} else {
		$(document).find("[idx='{{_item}}']").remove();
	}
</script>
<script type="text/locale">
[eng]
	title		= "Remove item"
	error 		= "WARNING! Remove item error."
	remove		= "Remove"
	cancel		= "Cancel"
	confirm		= "Confirm item remove with ID <b>{{_item}}</b> in table <b>{{_form}}</b>.<br>This action is irreversible."
[rus]
	title		= "Удаление записи"
	error 		= "ВНИМАНИЕ! Ошибка удаления записи"
	remove		= "Удалить"
	cancel		= "Отмена"
	confirm		= "Пожалуйста, подтвердите удаление записи с идентификатором <b>{{_item}}</b> из таблицы <b>{{_form}}</b>.<br>Данное действие необратимо."
</script>
