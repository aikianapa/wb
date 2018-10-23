<div class="modal fade" id="{{_GET[form]}}_{{_GET[item]}}" data-keyboard="false" data-backdrop="true" role="dialog" aria-hidden="true">
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
			<button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> {{_LANG[cancel]}}</button>
			<button type="button" class="btn btn-danger wb-remove-confirm" data-dismiss="modal"
					 data-wb-ajax="/form/remove/{{_GET[form]}}/{{_GET[item]}}/"
					 data-wb-append="body">
					 <span class="glyphicon glyphicon-ok"></span> {{_LANG[remove]}}
			</button>
		  </div>
		</div>
</div>
</div>
<script data-wb-tag="success" language="javascript">
	if ($("[data-wb-table='{{_GET[form]}}']").length) {
		$("[data-wb-table='{{_GET[form]}}'] [idx='{{_GET[item]}}']").remove();
	} else {
		$(document).find("[idx='{{_GET[item]}}']").remove();
	}
</script>
<script type="text/locale">
[eng]
	title		= "Remove item"
	error 		= "WARNING! Remove item error."
	remove		= "Remove"
	cancel		= "Cancel"
	confirm		= "Confirm item remove with ID <b>{{_GET[item]}}</b> in table <b>{{_GET[form]}}</b>.<br>This action is irreversible."
[rus]
	title		= "Удаление записи"
	error 		= "ВНИМАНИЕ! Ошибка удаления записи"
	remove		= "Удалить"
	cancel		= "Отмена"
	confirm		= "Пожалуйста, подтвердите удаление записи с идентификатором <b>{{_GET[item]}}</b> из таблицы <b>{{_GET[form]}}</b>.<br>Данное действие необратимо."
</script>
