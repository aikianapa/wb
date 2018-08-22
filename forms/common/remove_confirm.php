<div class="modal fade" id="{{_GET[form]}}_{{_GET[item]}}" data-keyboard="false" data-backdrop="true" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
	<div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h5 class="modal-title">Удаление записи</h5>
      </div>
      <div class="modal-body">
          Пожалуйста, подтвердите удаление записи с идентификатором <b>{{_GET[item]}}</b> из таблицы <b>{{_GET[form]}}</b>. Данное действие необратимо.
          <div class="alert alert-warning text-center hidden" style="margin-top:20px;">ВНИМАНИЕ!!! Ошибка удаления записи</div>
      </div>

		  <div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Отмена</button>
			<button type="button" class="btn btn-danger wb-remove-confirm" data-dismiss="modal" 
					 data-wb-ajax="/form/remove/{{_GET[form]}}/{{_GET[item]}}/"
					 data-wb-append="body">
					 <span class="glyphicon glyphicon-ok"></span> Удалить
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
