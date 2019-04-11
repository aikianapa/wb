<h5 class="element-header">
        {{_LANG[header]}}
        <a type="button" class="btn btn-warning pull-right" data-wb-ajax="/ajax/backup/backup/backup/?confirm=true" data-wb-append="body" id="module_btn_backup">
		<i class="fa fa-cloud-upload"></i> &nbsp; {{_LANG[btn_backup]}}
	</a>
</h5>
            <div class="row" id="moduleBackup">
                <div class="col-sm-12">
                    <table class="table table-striped">
                      <thead>
                        <!--td><label class="ckbox mg-b-0"><input type="checkbox"><span></span></label></td-->
                        <th data-wb-sort="_created:d">{{_LANG[datetime]}}</th>
                        <th data-wb-sort="type:a">{{_LANG[type]}}</th>
                        <th class="d-none d-sm-table-cell">{{_LANG[filename]}}</th>
                        <th data-wb-sort="size:d">{{_LANG[filesize]}}</th>
                        <th class="text-right">{{_LANG[action]}}</th>
                      </thead>
                      <tbody data-wb-role="foreach" id="moduleBackupsList" data-wb-call="backup__list" data-wb-sort="_created:d" data-wb-size="{{_ENV[page_size]}}">
                        <tr data-name="{{name}}">
                          <td>{{date}}</td>
                          <td>{{type}}</td>
                          <td class="d-none d-sm-table-cell">{{name}}</td>
                          <td>{{size}}</td>
                          <td class="text-right">

                            <div class="btn-group">
                              <a class="btn btn-sm btn-white" type="button" href="/backup/{{name}}">
                                <i class="fa fa-download"></i>
                              </a>
                              <a aria-expanded="false" aria-haspopup="true" class="btn btn-sm btn-white dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" type="button">
                                <span class="sr-only">Toggle Dropdown</span>
                              </a>
                              <div class="dropdown-menu dropdown-menu-right">
                                <a class="dropdown-item" href="/backup/{{name}}">
                                  <i class="fa fa-download"></i> {{_LANG[upload]}}
                                </a>
                                <a class="dropdown-item" href="javascript:void(0);"
                                  data-wb-ajax="/ajax/backup/backup/restore/{{name}}/?confirm=true" data-wb-append="body">
                                  <i class="fa fa-upload"></i> {{_LANG[restore]}}
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="javascript:void(0);"
                                  data-wb-ajax="/ajax/backup/backup/remove/{{name}}/?confirm=true" data-wb-append="body">
                                  <i class="fa fa-trash"></i> {{_LANG[remove]}}
                                </a>
                              </div>
                            </div>

                          </td>
                        </tr>
                      </tbody>
                    </table>
                </div>
            </div>

<div class="modal fade {{_ENV[route][mode]}}" id="backup_confirm" data-keyboard="false" data-backdrop="true" role="dialog" aria-hidden="true" data-wb-allow="admin" data-wb-where='"{{_ENV[route][mode]}}"="backup"'>
  <meta name="file" value="{{name}}" data-wb-where='action <> "backup"'>
  <meta name="action" value="{{action}}">
  <div class="modal-dialog modal" role="document">
	<div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          <h5 class="modal-title">{{action_name}}</h5>
        </div>
        <div class="modal-body">
		<div class="checks msg" data-wb-where='"{{_ENV[route][params][1]}}"="backup"'>
			{{_LANG.create}}
			<div class="row">
				<label class="col-6 text-right form-control-sm">{{_LANG[backup]}} {{_LANG[type_a]}}</label>
				<div class="col-2"><label class="switch switch-success"><input type="checkbox" value="on" name="options[app]"><span></span></label></div>
			</div>
			<div class="row">
				<label class="col-6 text-right form-control-sm">{{_LANG[backup]}} {{_LANG[type_d]}}</label>
				<div class="col-2"><label class="switch switch-success"><input type="checkbox" value="on" name="options[db]"><span></span></label></div>
			</div>
			<div class="row">
				<label class="col-6 text-right form-control-sm">{{_LANG[backup]}} {{_LANG[type_u]}}</label>
				<div class="col-2"><label class="switch switch-success"><input type="checkbox" value="on" name="options[upl]"><span></span></label></div>
			</div>
			<div class="row">
				<label class="col-6 text-right form-control-sm">{{_LANG[backup]}} {{_LANG[type_e]}}</label>
				<div class="col-2"><label class="switch switch-success"><input type="checkbox" value="on" name="options[engine]"><span></span></label></div>
			</div>
		</div>
          <div class="checks" data-wb-where='"{{_ENV[route][params][1]}}"="restore"'>

          <div class="msg pd-b-20">
          {{_LANG[backup]}} <u>{{typetext}}</u> {{_LANG[from]}} {{date}}<br>
          {{action_name}} {{name}}?
          </div>
        </div>
        </div>
		  <div class="modal-footer">
        <div class="sk-three-bounce hidden" style="margin:5px 20px;">
          <div class="sk-child sk-bounce1 bg-gray-800"></div>
          <div class="sk-child sk-bounce2 bg-gray-800"></div>
          <div class="sk-child sk-bounce3 bg-gray-800"></div>
        </div>
			<button type="button" class="btn btn-default" data-dismiss="modal"><span class="fa fa-close"></span> {{_LANG[btn_back]}}</button>
			<button type="button" class="btn btn-danger" id="module_backup_confirm"><span class="fa fa-upload"></span> {{action_name}}</button>
		  </div>
		</div>
  </div>
</div>

<script src="backup.js"></script>

<script type="text/locale">
[eng]
	header		= "Backup & Restore"
	backup		= "Backup"
	restore		= "Restore"
	upload		= "Upload"
	remove		= "Remove"
	removing	= "Removing"
	wait		= "wait"
	btn_backup	= "Create backup"
	btn_restore	= "Restore"
	btn_remove	= "Remove"
	btn_back	= "Back"
	datetime	= "Date/Time"
	type		= "Type"
	type_e		= "engine"
	type_a		= "app"
	type_u		= "uploads"
	type_d		= "database"
	filesize	= "File size"
	filename	= "File name"
	action		= "Action"
	from		= "from"
	backup_unzip	= "Unzip backup file"
	backup_complete	= "Backup complete"
	restore_complete= "Restore complete"
	restore_from	= "Restore from backup"
	restore_error 	= "is not possible because /engine is a symbolic link.<br / >please Contact the server administrator."
	restore_db	= "Restore database"
	restore_app	= "Restore application files"
	remove_complete = "Remove file complete"
	remove_error	= "Error, unable to remove file"
	remove_current	= "Remove current version"
	create		= "Confirm to create Backup"
[rus]
	header		= "Резервные копии и восстановление"
	backup		= "Резервная копия"
	restore		= "Восстановление"
	upload		= "Скачать"
	remove		= "Удалить"
	removing	= "Удаление"
	wait		= "ждите"
	btn_backup	= "Создать резервную копию"
	btn_restore	= "Восстановить"
	btn_remove	= "Удалить"
	btn_back	= "Назад"
	datetime	= "Дата/Время"
	type		= "Тип"
	type_e		= "системы"
	type_a		= "приложения"
	type_d		= "базы данных"
	type_u		= "загрузок"
	filesize	= "Размер"
	filename	= "Имя файла"
	action		= "Действие"
	from		= "от"
	backup_unzip	= "Распаковка резервной копии"
	backup_complete	= "Создание резервной копии завершено"
	restore_complete= "Восстановление выполнено"
	restore_from	= "Восстановление из резервной копии"
	restore_error	= "невозможно, так как /engine является символьной ссылкой.<br>Обратитесь к администратору сервера."
	restore_db	= "Восстановить базу данных"
	restore_app	= "Восстановить файлы приложения"
	remove_complete = "Удаление файла выполнено"
	remove_error	= "Ошибка, не удалось удалить файл"
	remove_current	= "Удаление текущей версии"
	create		= "Подтвердите создание резервной копии"
</script>
