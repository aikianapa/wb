<div data-wb-role="include" src="modal" data-wb-id="{{_ENV[route][name]}}Settings" data-wb-hide="*"></div>

<span data-wb-html="#{{_ENV[route][name]}}Settings .modal-title">Настройки Yandex.Money</span>
<button class="btn btn-secondary" data-dismiss="modal" data-wb-formsave="#admin_settings" data-wb-html="#{{_ENV[route][name]}}Settings .modal-footer">Готово</button>
<div data-wb-html="#{{_ENV[route][name]}}Settings .modal-body">
    <div class="row form-group">
        <label class="col-sm-4 control-label">ID Кошелька</label>
        <div class="col-sm-8"><input type="text" name="yapay[id]" class="form-control" placeholder="ID Яндекс кошелька"></div>
    </div>

    <div class="row form-group">
        <label class="col-sm-4 control-label">URL success</label>
        <div class="col-sm-8"><input type="text" name="yapay[success]" class="form-control" placeholder="URL для перехода при успешной оплате"></div>
    </div>

    <div class="row form-group">
        <label class="col-sm-4 control-label">URL fail</label>
        <div class="col-sm-8"><input type="text" name="yapay[fail]" class="form-control" placeholder="URL для перехода при неудачной оплате"></div>
    </div>
<hr>
    <div class="row form-group">
        <label class="col-sm-4 control-label">Тестовый режим</label>
        <div class="col-sm-2">
            <label class="switch switch-success"><input type="checkbox" name="yapay[test]"><span></span></label>
        </div>
    </div>

    <div class="row form-group">
        <label class="col-sm-4 control-label">ID Тестового кошелька</label>
        <div class="col-sm-8"><input type="text" name="yapay[test_id]" class="form-control" placeholder="ID тестового Яндекс кошелька"></div>
    </div>
</div>
