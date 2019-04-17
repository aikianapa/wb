<div data-wb-role="include" src="modal" data-wb-id="{{_ENV[route][name]}}Settings" data-wb-hide="*"></div>

<span data-wb-html="#{{_ENV[route][name]}}Settings .modal-title">{{_LANG[payeer_settings]}}</span>
<button class="btn btn-secondary" data-dismiss="modal" data-wb-formsave="#admin_settings" data-wb-html="#{{_ENV[route][name]}}Settings .modal-footer">Готово</button>
<div data-wb-html="#{{_ENV[route][name]}}Settings .modal-body">
    <div class="row form-group">
        <label class="col-sm-4 control-label">{{_LANG[payeer_id]}}</label>
        <div class="col-sm-8"><input type="text" name="payeer[id]" class="form-control" placeholder="{{_LANG[payeer_id]}}"></div>
    </div>

    <div class="row form-group">
        <label class="col-sm-4 control-label">{{_LANG[payeer_key1]}}</label>
        <div class="col-sm-8"><input type="text" name="payeer[key1]" class="form-control" placeholder="{{_LANG[payeer_key1]}}"></div>
    </div>

    <div class="row form-group">
        <label class="col-sm-4 control-label">{{_LANG[payeer_key2]}}</label>
        <div class="col-sm-8"><input type="text" name="payeer[key2]" class="form-control" placeholder="{{_LANG[payeer_key2]}}"></div>
    </div>
<hr>
    <div class="row form-group">
        <label class="col-sm-4 control-label">{{_LANG[payeer_success]}}</label>
        <div class="col-sm-8"><input type="text" name="payeer[success]" class="form-control" placeholder="{{_LANG[payeer_success]}}"></div>
    </div>

    <div class="row form-group">
        <label class="col-sm-4 control-label">{{_LANG[payeer_fail]}}</label>
        <div class="col-sm-8"><input type="text" name="payeer[fail]" class="form-control" placeholder="{{_LANG[payeer_fail]}}"></div>
    </div>
    <div class="row form-group">
        <label class="col-sm-4 control-label">{{_LANG[payeer_status]}}</label>
        <div class="col-sm-8"><input type="text" name="payeer[status]" class="form-control" placeholder="{{_LANG[payeer_status]}}"></div>
    </div>
<hr>
    <div class="row form-group">
	<label class="col-sm-4 control-label">{{_LANG[payeer_currency]}}</label>
	<div class="col-sm-8">
	<select class="form-control" name="payeer[currency]">
		<option value="USD">USD</option>
		<option value="EUR">EUR</option>
		<option value="RUB">RUB</option>
	</select>
	</div>
    </div>
</div>

<script type="text/locale">
[eng]
payeer_settings		= "Settings"
payeer_id		= "Merchant ID"
payeer_key1		= "Secret Key #1"
payeer_key2		= "Secret Key #2"
payeer_success		= "URL success"
payeer_fail		= "URL fail"
payeer_status		= "URL status"
payeer_currency		= "Currency"

[rus]
payeer_settings			= "Настройки"
payeer_id		= "ID Мерчанта"
payeer_key1		= "Секретный ключ #1"
payeer_key2		= "Секретный ключ #2"
payeer_success		= "URL success"
payeer_fail		= "URL fail"
payeer_status		= "URL status"
payeer_currency		= "Валюта"
</script>
