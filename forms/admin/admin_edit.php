<h5 class="element-header">
        {{_LANG[settings]}}
        <a type="button" href="#" class="btn btn-primary pull-right" data-wb-formsave="#admin_settings"><i class="fa fa-save"></i> &nbsp; {{_LANG[btn_save]}}</a>
</h5>
<form method="post" id="admin_settings" data-wb-form="admin" data-wb-item="settings" data-wb-allow="admin">
    <div class="nav-active-primary">
        <ul class="nav nav-pills flex-column flex-md-row" role="tablist">
                <li class="nav-item"><a class="nav-link active" href="#adminMain" data-toggle="tab">{{_LANG[tab_main]}}</a></li>
                <li class="nav-item"><a class="nav-link" href="#adminCache" data-toggle="tab">{{_LANG[tab_cache]}}</a></li>
                <li class="nav-item"><a class="nav-link" href="#adminAdd" data-toggle="tab">{{_LANG[tab_includes]}}</a></li>
                <li class="nav-item"><a class="nav-link" href="#adminTree" data-toggle="tab">{{_LANG[tab_catalog]}}</a></li>
                <li class="nav-item"><a class="nav-link" href="#adminUpdate" data-toggle="tab">{{_LANG[tab_update]}}</a></li>
                <li class="nav-item"><a class="nav-link" href="#adminBackups" data-toggle="tab">{{_LANG[tab_backup]}}</a></li>
                <li class="nav-item"><a class="nav-link" href="#" data-wb-ajax="/module/sitemap" data-wb-html=".content-box">{{_LANG[tab_sitemap]}}</a></li>
                <li class="nav-item"><a class="nav-link" href="#" data-wb-ajax="/form/list/source" data-wb-html=".content-box">{{_LANG[tab_explorer]}}</a></li>
        </ul>
    </div>
    <div class="tab-content pd-y-20">
        <div id="adminMain" class="tab-pane active" role="tabpanel">
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group">
                        <label for="">Заголовок сайта</label>
                        <input class="form-control" placeholder="Заголовок сайта" type="text" name="header" required> </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="">Электронная почта</label>
                        <input class="form-control" placeholder="Электронная почта" type="text" name="email" required> </div>
                </div>
                <div class="col-sm-12">
                    <div class="row form-group">
                        <div class="col-3">
                        <label for="">Платёжная система</label>
                            <select class="form-control" name="merchant" data-wb-role="foreach" data-wb-from="merchants" value="{{merchant}}" data-wb-hide="wb">
                                <option value="{{name}}">{{name}} [{{type}}]</option>
                            </select>
                        </div>
                        <div class="col-2">
                            <label>&nbsp;</label>
                            <a href="#" data-wb-ajax="/module/{{merchant}}/settings" data-wb-html="#adminMain .merchant-settings" class="btn btn-secondary form-control"><i class="fa fa-gear"></i> Настройки</a>
                            <div class="merchant-settings"></div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12">
                        <div class="form-group">
                                <label for="">{{_LANG[beautifyHtml]}}</label>
                                <label class="switch switch-sm switch-success">
				<input type="checkbox" name="beautifyHtml" value="">
				<span></span>
			</label>
                        </div>
                </div>
                <div class="col-sm-12" data-wb-role="multiinput" name="variables">
                    <div class="col-sm-3 col-xs-12">
                        <input class="form-control" placeholder="Переменная" type="text" name="var"> </div>
                    <div class="col-sm-4 col-xs-12">
                        <input class="form-control" placeholder="Значение" type="text" name="value"> </div>
                    <div class="col-sm-5 col-xs-12">
                        <input class="form-control" placeholder="Описание" type="text" name="header"> </div>
                </div>
            </div>
        </div>
        <div id="adminCache" class="tab-pane" role="tabpanel" data-wb-role="include" src="form" data-wb-name="admin_cache">

        </div>
        <div id="adminAdd" class="tab-pane" role="tabpanel">
            <div class="row">
                <div class="col-xl-6">
                    <div class="row">
                        <div class="col-6"><h5>Вставка в HEAD</h5></div>
                        <div class="col-6"><label class="switch switch-success"><input type="checkbox" name="head_add_active"><span></span></label></div>
                    </div>
                    <div data-wb-role="include" data-wb-name="head_add" src="source" role="tabpanel"></div>
                </div>
                <div class="col-xl-6">
                    <div class="row">
                        <div class="col-6"><h5>Вставка в BODY</h5></div>
                        <div class="col-6"><label class="switch switch-success"><input type="checkbox" name="body_add_active"><span></span></label></div>
                    </div>
                    <div data-wb-role="include" data-wb-name="body_add" src="source" role="tabpanel"></div>
                </div>
            </div>
        </div>
        <div id="adminTree" class="tab-pane fade" role="tabpanel">
            <div class="row">
                <div class="col-sm-12">
                    <div data-wb-role="tree" name="tree"></div>
                </div>
            </div>
        </div>
        <div id="adminUpdate" class="tab-pane fade" role="tabpanel">
            <div class="row">
                <div class="col-sm-12">
                    <div class="alert alert-danger">
                        <h3>Внимание!</h3>
                        <p>Обновление может повлиять на работоспособность вашей системы.<br/>
                        Резервные копии системы и приложения будут сохранены в дирректории backup.</p>
                    </div>
                </div>
                <div class="col-sm-12">
                    <button type="button" class="btn btn-warning pull-right" id="admin_btn_update">Обновить</button>
                </div>
            </div>
        </div>
        <div id="adminBackups" class="tab-pane fade" role="tabpanel">
            <div class="row">
                <div class="col-sm-12">
                    <table class="table table-striped">
                      <thead>
                        <!--td><label class="ckbox mg-b-0"><input type="checkbox"><span></span></label></td-->
                        <td>Дата</td>
                        <td>Тип</td>
                        <td class="d-none d-sm-table-cell">Имя файла</td>
                        <td>Зазмер</td>
                        <td>Действие</td>
                      </thead>
                      <tbody data-wb-role="foreach" id="adminBackupsList" data-wb-from="backups" data-wb-sort="_created:d" data-wb-limit="10">
                        <tr data-name="{{name}}">
                          <td>{{date}}</td>
                          <td>{{type}}</td>
                          <td class="d-none d-sm-table-cell">{{name}}</td>
                          <td>{{size}}</td>
                          <td>

                            <div class="btn-group">
                              <a class="btn btn-sm btn-white" type="button" href="/backup/{{name}}">
                                <i class="fa fa-download"></i>
                              </a>
                              <a aria-expanded="false" aria-haspopup="true" class="btn btn-sm btn-white dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" type="button">
                                <span class="sr-only">Toggle Dropdown</span>
                              </a>
                              <div class="dropdown-menu dropdown-menu-right">
                                <a class="dropdown-item" href="/backup/{{name}}">
                                  <i class="fa fa-download"></i> Скачать
                                </a>
                                <a class="dropdown-item" href="javascript:void(0);"
                                  data-wb-ajax="/ajax/admin/backup/restore/{{name}}/?confirm=true" data-wb-append="body">
                                  <i class="fa fa-upload"></i> Восстановить
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="javascript:void(0);"
                                  data-wb-ajax="/ajax/admin/backup/remove/{{name}}/?confirm=true" data-wb-append="body">
                                  <i class="fa fa-trash"></i> Удалить
                                </a>
                              </div>
                            </div>

                          </td>
                        </tr>
                      </tbody>
                    </table>
                </div>
                <div class="col-sm-12">
                    <button type="button" class="btn btn-warning pull-right" data-wb-ajax="/ajax/admin/backup/backup/?confirm=true" data-wb-append="body" id="admin_btn_backup">
                      <i class="fa fa-cloud-upload"></i>
                      Создать резервную копию
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

<div class="modal fade {{action}}" id="backup_confirm" data-keyboard="false" data-backdrop="true" role="dialog" aria-hidden="true" data-wb-allow="admin" data-wb-where='"{{_ENV[route][mode]}}"="backup"'>
  <meta name="file" value="{{name}}">
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
          <div class="msg pd-b-20">
          Резервная копия <u>{{typetext}}</u> от {{date}}<br>
          {{action_name}} {{name}}?
          </div>
          <div class="checks" data-wb-where='"{{_ENV[route][params][1]}}"="restore"'>
          <div class="form-control-sm row">
        		<label class="col text-right form-control-sm">Восстановить базу данных</label>
        		<div class="col-sm-2"><label class="switch switch-success"><input type="checkbox" value="on" name="db"><span></span></label></div>
        	</div>
          <div class="form-control-sm row">
        		<label class="col text-right form-control-sm">Восстановить файлы приложения</label>
        		<div class="col-sm-2"><label class="switch switch-success"><input type="checkbox" value="on" name="app"><span></span></label></div>
        	</div>
        </div>
        </div>
		  <div class="modal-footer">
        <div class="sk-three-bounce hidden" style="margin:5px 20px;">
          <div class="sk-child sk-bounce1 bg-gray-800"></div>
          <div class="sk-child sk-bounce2 bg-gray-800"></div>
          <div class="sk-child sk-bounce3 bg-gray-800"></div>
        </div>
			<button type="button" class="btn btn-default" data-dismiss="modal"><span class="fa fa-close"></span> Назад</button>
			<button type="button" class="btn btn-danger" id="admin_backup_confirm"><span class="fa fa-upload"></span> {{action_name}}</button>
		  </div>
		</div>
  </div>
</div>

<script src="/engine/forms/admin/admin.js"></script>
<script type="text/locale">
[rus]
        settings	= Настройки
        slogan 		= Используйте мастер для подготовки установки.
        step1 		= Основное
        step2 		= Безопасность
        step3 		= Шаблон
        step4 		= Установка
        header		= Заголовок
        email		= Эл.почта
        login		= Логин админа
        password	= Пароль админа
        chkpass		= Повтор пароля
        beautifyHtml    = Форматировать HTML
        msg_ready	= Всё готово к установке. Нажмите кнопку Установить для начала процесса.
        btn_prev	= Назад
        btn_next	= Далее
        btn_install	= Установить
        btn_save	= Сохранить
        tab_cache	= Кэширование
        tab_main	= Основные
        tab_includes	= Вставки
        tab_catalog	= Каталог
        tab_update	= Обновление
        tab_backup	= Бэкап
        tab_sitemap	= Карта сайта
        tab_explorer	= Проводник

[eng]
        settings	= Settings
        slogan 		= Use installation wizard to setup engine.
        step1 		= Main
        step2 		= Security
        step3 		= Template
        step4 		= Installation
        header		= Header
        email		= Email
        login		= Admin Login
        password	= Admin Password
        chkpass		= Check password
        beautifyHtml    = Beautify HTML
        msg_ready	= Ready to installation. Click Install button to Start.
        btn_prev	= Prev
        btn_next	= Next
        btn_install	= Install
        btn_save	= Save
        tab_cache	= Cache
        tab_main	= Main
        tab_includes	= Includes
        tab_catalog	= Catalog
        tab_update	= Update
        tab_backup	= Backup
        tab_sitemap	= Sitemap
        tab_explorer	= Explorer
</script>
