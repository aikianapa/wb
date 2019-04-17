<div class="row" id="moduleUpdate">
	<h5 class="element-header">
		{{_LANG[header]}}
		<a type="button" class="btn btn-warning btn-sm pull-right" id="module_update_btn">
			<i class="fa fa-cloud-upload"></i> &nbsp; {{_LANG[btn_update]}}
		</a>
	</h5>
	<div class="col-sm-12">
	    <div class="alert alert-danger">
		<h3>{{_LANG[warning]}}!</h3>
		<p>{{_LANG[message]}}</p>
	    </div>
	</div>

	<div class="widget update-process col-12 d-none">
		<div class="widget-content themed-background-dark text-light-op">{{_LANG[header]}}</div>
		<div class="widget-content themed-background-muted text-center">
			<div class="progress progress-striped active">
				<div class="progress-bar progress-bar-info" role="progressbar" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
					{{_LANG[init]}}
				</div>
			</div>
			<br>
			<a href="/admin/" class="btn btn-success">{{_LANG[complete]}}</a>
		</div>
		<div data-wb-role="foreach" data-wb-count="8" data-wb-tpl="false" data-wb-hide="*">
			<meta class="step{{_idx}}" value="{{_LANG[step{{_idx}}]}}" data-wb-hide="false">
		</div>
	</div>
</div>
<script src="update.js"></script>
<script type="text/locale">
[eng]
header		= "WebBasic Update"
warning		= "Warning"
message		= "The update may affect the health of your system.<br/>System and application backups will be stored in the /backup directory."
btn_update	= "Update"
init		= "Initialization"
complete	= "Update complete"
step0		= "Downloading last version WebBasic"
step1		= "Create backup for system"
step2		= "Create backup for application"
step3		= "Create backup for uploads"
step4		= "Unzip archive"
step5		= "Remove previous version"
step6		= "Update system"
step7		= "Update complete"
[rus]
header		= "Обновление WebBasic"
warning		= "Внимание"
message		= "Обновление может повлиять на работоспособность вашей системы.<br/>Резервные копии системы и приложения будут сохранены в дирректории /backup."
btn_update	= "Обновить"
init		= "Инициализация"
complete	= "Обновление завершено"
step0		= "Получение актуальной версии WebBasic"
step1		= "Создание резервной копии системы"
step2		= "Создание резервной копии приложения"
step3		= "Создание резервной копии загрузок"
step4		= "Распаковка архива"
step5		= "Удаление предыдущей версии"
step6		= "Обновление системы"
step7		= "Обновление выполнено"
</script>
