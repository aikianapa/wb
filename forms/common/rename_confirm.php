<div class="modal fade" id="{{_form}}_{{_item}}" data-keyboard="false" data-backdrop="true" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form class="form-horizontal" role="form">
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
                    <div class="form-group row">
                        <label class="col-sm-3 form-control-label">{{_LANG[new_id]}}</label>
                        <div class="col-sm-9"><input type="text" class="form-control" name="id" placeholder="{{_LANG[new_id]}}"></div>
                    </div>
                    <div class="alert alert-warning text-center hidden" style="margin-top:20px;">{{_LANG[error]}}</div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><span class="fa fa-close"></span> {{_LANG[cancel]}}</button>
                    <button type="button" class="btn btn-danger wb-rename-confirm" data-dismiss="modal" data-wb-ajax="/ajax/rename/{{_form}}/{{_item}}/" data-wb-append="body">
                        <span class="fa fa-trash"></span> {{_LANG[rename]}}
                    </button>
                </div>
            </div>
        </form>
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
	title		= "Rename item"
	error 		= "WARNING! rename item error."
	rename		= "Rename"
	cancel		= "Cancel"
    old_id      = "Previous ID"
	new_id      = "New ID"
    confirm		= "Rename item with ID <b>{{_item}}</b> in table <b>{{_form}}</b>."
[rus]
	title		= "Переименование записи"
	error 		= "ВНИМАНИЕ! Ошибка удаления записи"
	rename		= "Переименовать"
    old_id      = "Предыдущий ID"
    new_id      = "Новый ID"
	cancel		= "Отмена"
	confirm		= "Переименование записи с идентификатором <b>{{_item}}</b> в таблице <b>{{_form}}</b>."
</script>
