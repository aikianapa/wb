<div data-wb-role="include" src="modal" data-wb-id="{{_ENV[route][name]}}Settings" data-wb-hide="*">
</div>

<span data-wb-html="#{{_ENV[route][name]}}Settings .modal-title">Настройки ROBOKASSA</span>
<button class="btn btn-secondary" data-dismiss="modal" data-wb-formsave="#admin_settings" data-wb-html="#{{_ENV[route][name]}}Settings .modal-footer">Готово</button>
<div data-wb-html="#{{_ENV[route][name]}}Settings .modal-body">
    <div class="row form-group">
        <label class="col-sm-4 control-label">Мерчант ID</label>
        <div class="col-sm-8"><input type="text" name="robokassa[id]" class="form-control" placeholder="Мерчант ID"></div>
    </div>
    <div class="row form-group">
        <label class="col-sm-4 control-label">Мерчант URL</label>
        <div class="col-sm-8"><input type="text" name="robokassa[url]" class="form-control" placeholder="Мерчант URL"></div>
    </div>
    <div class="row form-group">
        <label class="col-sm-4 control-label">Секретный ключ #1</label>
        <div class="col-sm-8"><input type="text" name="robokassa[key1]" class="form-control" placeholder="Секретный ключ #1"></div>
    </div>

    <div class="row form-group">
        <label class="col-sm-4 control-label">Секретный ключ #2</label>
        <div class="col-sm-8"><input type="text" name="robokassa[key2]" class="form-control" placeholder="Секретный ключ #2"></div>
    </div>

    <div class="row form-group">
        <label class="col-sm-4 control-label">URL success</label>
        <div class="col-sm-8"><input type="text" name="robokassa[success]" class="form-control" placeholder="URL для перехода при успешной оплате"></div>
    </div>

    <div class="row form-group">
        <label class="col-sm-4 control-label">URL fail</label>
        <div class="col-sm-8"><input type="text" name="robokassa[fail]" class="form-control" placeholder="URL для перехода при неудачной оплате"></div>
    </div>

    <div class="row form-group">
        <label class="col-sm-4 control-label">Тестовый режим</label>
        <div class="col-sm-2">
            <label class="switch switch-success"><input type="checkbox" name="robokassa[test]"><span></span></label>
        </div>
    </div>

    <div class="row form-group">
        <label class="col-sm-4 control-label">Тестовый ключ #1</label>
        <div class="col-sm-8"><input type="text" name="robokassa[test1]" class="form-control" placeholder="Тестовый ключ #1"></div>
    </div>

    <div class="row form-group">
        <label class="col-sm-4 control-label">Тестовый ключ #2</label>
        <div class="col-sm-8"><input type="text" name="robokassa[test2]" class="form-control" placeholder="Тестовый ключ #2"></div>
    </div>
</div>
