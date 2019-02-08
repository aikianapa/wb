<div data-wb-role="include" src="modal" data-wb-id="{{_ENV[route][name]}}Settings" data-wb-hide="*"></div>

<span data-wb-html="#{{_ENV[route][name]}}Settings .modal-title">{{_LANG.settings}} PhpMailer</span>
<button class="btn btn-secondary" data-dismiss="modal" data-wb-formsave="#admin_settings" data-wb-html="#{{_ENV[route][name]}}Settings .modal-footer">{{_LANG.ready}}</button>
<div data-wb-html="#{{_ENV[route][name]}}Settings .modal-body">
    <div class="row form-group">
        <label class="col-sm-4 control-label" title="Set system mail function">Mail function</label>
        <div class="col-sm-8">
		<select class="col-sm-8 form-control" name="phmail[func]" value="{{phmail[func]}}">
			<option value="mail">mail</option>
			<option value="sendmail">sendmail</option>
		</select>
	</div>
    </div>

    <div class="row form-group">
        <label class="col-sm-4 control-label" title="Set mailer to use SMTP">Use SMTP</label>
        <div class="col-sm-8">
		<label class="switch switch-sm switch-success">
		<input type="checkbox" name="phmail[smtp]" value="{{phmail[smtp]}}">
		<span></span>
        </div>
    </div>

    <div class="row form-group">
        <label class="col-sm-4 control-label" title="Specify SMTP server">SMTP host</label>
        <div class="col-sm-8"><input type="text" name="phmail[host]" class="form-control" placeholder="SMTP host"></div>
    </div>

    <div class="row form-group">
        <label class="col-sm-4 control-label" title="Specify SMTP server">SMTP port</label>
        <div class="col-sm-8"><input type="number" name="phmail[port]" class="form-control" placeholder="SMTP port"></div>
    </div>

    <div class="row form-group">
        <label class="col-sm-4 control-label" title="Set system mail function">SMTP secure</label>
        <div class="col-sm-8">
		<select class="col-sm-8 form-control" name="phmail[secure]" value="{{phmail[secure]}}">
			<option value="tls">TLS</option>
			<option value="ssl">SSL</option>
		</select>
	</div>
    </div>

<hr>
    <div class="row form-group">
        <label class="col-sm-4 control-label" title="Enable SMTP authentication">SMTP Auth</label>
        <div class="col-sm-2">
            <label class="switch switch-success"><input type="checkbox" name="phmail[auth]"><span></span></label>
        </div>
    </div>

    <div class="row form-group">
        <label class="col-sm-4 control-label">SMTP username</label>
        <div class="col-sm-8"><input type="text" name="phmail[username]" class="form-control" placeholder="SMTP username"></div>
    </div>

    <div class="row form-group">
        <label class="col-sm-4 control-label">SMTP password</label>
        <div class="col-sm-8"><input type="password" name="phmail[password]" class="form-control" placeholder="SMTP password"></div>
    </div>

</div>
<script type="text/locale">
[eng]
settings	= "Settings"
ready	        = "Ready"
[rus]
settings	= "Настройки"
ready	        = "Готово"
</script>
