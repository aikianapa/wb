<div class="row">
<div class="col-sm-12">
    <div class="alert alert-danger">
	<h3>{{_LANG[warning]}}!</h3>
	<p>{{_LANG[message]}}</p>
    </div>
</div>
<div class="col-sm-12">
    <button type="button" class="btn btn-warning pull-right" id="admin_btn_update"><i class="fa fa-refresh"></i> {{_LANG[btn_update]}}</button>
</div>
</div>

<script type="text/locale">
[eng]
warning		= "Warning"
message		= "The update may affect the health of your system.<br/>System and application backups will be stored in the /backup directory."
btn_update	= "Update"
[rus]
warning		= "Внимание"
message		= "Обновление может повлиять на работоспособность вашей системы.<br/>Резервные копии системы и приложения будут сохранены в дирректории /backup."
btn_update	= "Обновить"
</script>
