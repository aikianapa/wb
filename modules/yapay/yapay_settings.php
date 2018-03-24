<div data-wb-role="include" src="modal" data-wb-id="{{_ENV[route][name]}}Settings" data-wb-hide="*"></div>

<span data-wb-html="#{{_ENV[route][name]}}Settings .modal-title">Настройки Yandex.Money</span>
<button class="btn btn-secondary" data-dismiss="modal" data-wb-html="#{{_ENV[route][name]}}Settings .modal-footer">Готово</button>
<div data-wb-html="#{{_ENV[route][name]}}Settings .modal-body">
    <div class="row form-group">
        <label class="col-sm-4 control-label">ID Кошелька</label>
        <div class="col-sm-8"><input type="text" name="yapay[id]" class="form-control" placeholder="ID Яндекс кошелька"></div>
    </div>
</div>