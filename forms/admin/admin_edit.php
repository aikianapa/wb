<h5 class="element-header">Настройки</h5>
<form method="post" id="admin_settings" data-wb-form="admin" data-wb-item="settings" data-wb-allow="admin">
    <div class="nav-active-primary">
        <ul class="nav nav-pills flex-column flex-md-row" role="tablist">
            <li class="nav-item"><a class="nav-link active" href="#adminMain" data-toggle="tab">Основные</a></li>
            <li class="nav-item"><a class="nav-link" href="#adminAdd" data-toggle="tab">Вставки</a></li>
            <li class="nav-item"><a class="nav-link" href="#adminTree" data-toggle="tab">Каталог</a></li>
            <li class="nav-item"><a class="nav-link" href="#adminUpdate" data-toggle="tab">Обновление</a></li>
            <li class="nav-item"><a class="nav-link" href="#adminBackups" data-toggle="tab">Бэкапы</a></li>
            <li class="nav-item"><a class="nav-link" href="#" data-wb-ajax="/module/sitemap" data-wb-html=".content-box">Карта сайта</a></li>
            <li class="nav-item"><a class="nav-link" href="#" data-wb-ajax="/form/list/users" data-wb-html=".content-box">Пользователи</a></li>
            <li class="nav-item"><a class="nav-link" href="#" data-wb-ajax="/form/list/source" data-wb-html=".content-box">Проводник</a></li>
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
                <div class="col-sm-12" data-wb-role="multiinput" name="variables">
                    <div class="col-sm-3 col-xs-12">
                        <input class="form-control" placeholder="Переменная" type="text" name="var"> </div>
                    <div class="col-sm-4 col-xs-12">
                        <input class="form-control" placeholder="Значение" type="text" name="value"> </div>
                    <div class="col-sm-5 col-xs-12">
                        <input class="form-control" placeholder="Описание" type="text" name="header"> </div>
                </div>
                <div class="col-sm-12">
                    <button type="button" class="btn btn-primary mg-t-20" data-wb-formsave="#admin_settings">Сохранить</button>
                </div>
            </div>
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

                <div class="col-sm-12">
                    <button type="button" class="btn btn-primary mg-t-20" data-wb-formsave="#admin_settings">Сохранить</button>
                </div>
            </div>
        </div>
        <div id="adminTree" class="tab-pane fade" role="tabpanel">
            <div class="row">
                <div class="col-sm-12">
                    <div data-wb-role="tree" name="tree"></div>
                </div>
                <div class="col-sm-12">
                    <button type="button" class="btn btn-primary mg-t-20" data-wb-formsave="#admin_settings">Сохранить</button>
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

<script>
    $(document).ready(function () {
        $("#admin_btn_update").off("click");
        $("#admin_btn_update").on("click", function () {
            wb_update_process();
        });

        $(document).undelegate("#admin_backup_confirm","click");
        $(document).delegate("#admin_backup_confirm","click", function () {
            wb_backup_process();
        });

        $(document).undelegate("#backup_confirm [type=checkbox]","change");
        $(document).delegate("#backup_confirm [type=checkbox]","change", function () {
            $("#admin_backup_confirm").prop("disabled",true);
            $("#backup_confirm .checks [type=checkbox]").each(function(){
                if ($(this).prop("checked")) {$("#admin_backup_confirm").prop("disabled",false);}
                // если хоть один чек включен, то разрешаем кнопку
            });
        });
    });

    function wb_backup_process(step,count) {
      $("#admin_backup_confirm").hide();
      $("#backup_confirm .sk-three-bounce").removeClass("hidden");
      $("#backup_confirm .checks").hide();
      var action=$("#backup_confirm meta[name=action]").attr("value");
      var file=$("#backup_confirm meta[name=file]").attr("value");
      var url="/ajax/admin/backup/"+action+"/"+file+"/";
      if (step==undefined) {step=0;} else {url=url+"?step="+step;}
      if (count==undefined) {var count = 0;}
      var data={};
      $("#backup_confirm.restore input").each(function(){
        if ($(this).prop("checked")) {data[$(this).attr("name")]="on";} else {data[$(this).attr("name")]="";}
      });
        $.ajax({
            async: true
            , type: 'POST'
            , url: url
            , data: data
            , success: function (data) {
                var data = JSON.parse(data);
                if (data.count !== undefined) {count=data.count;}
                if (data.next !== undefined) {var msg=data.next;} else {var msg="";}
                $("#backup_confirm .modal-body .msg").html(msg);
                if (step !== count) {
                  step++;
                  wb_backup_process(step,count);
                } else {
                  $("#backup_confirm .modal-body .msg").html(data.next);
                  $("#backup_confirm .sk-three-bounce").addClass("hidden");
                  if (action=="remove" && data.error==0) {$("#adminBackupsList tr[data-name='"+file+"']").remove();}
                  if (data.error==0) {setTimeout(function(){$("#backup_confirm").modal("hide");},2000);}
                }
            }
        });
    }


    function wb_update_process(step, count, msg) {
        var param = {};
        var start = 30;
        if (step == undefined) {
            var step = 0;
        }
        if (count == undefined) {
            var count = 0;
        }
        if (msg == undefined) {
            var msg = "Инициализация";
            var panel = '<div class="widget update-process ">' + '	<div class="widget-content themed-background-dark text-light-op">Обновление системы</div>' + '	<div class="widget-content themed-background-muted text-center">' + '		<div class="progress progress-striped active">' + '			<div class="progress-bar progress-bar-info" role="progressbar" aria-valuemin="0" aria-valuemax="100" style="width: 0%">' + msg + '			</div>' + '		</div>' + '<br><a href="/admin/" class="btn btn-success">Обновление завершено</a>' + '	</div>' + '</div>';
            $(".content-box").html(panel);
            $(".content-box .update-process .btn-success").hide();
            $(".content-box .update-process .progress-bar-info").css("width", start + "%");
        }

            $.ajax({
                async: true
                , type: 'POST'
                , url: "/engine/update.php?step=" + step
                , success: function (data) {
                    var data = JSON.parse(data);
                    if (count > 0) {
                        var percent = ceil(start + ((100 - start) / count * step));
                    }
                    else {
                        var percent = start;
                    }
                    if (data.count !== undefined) {
                        count = data.count;
                        $(".content-box").data("count", count);
                    }
                    $(".content-box .update-process .text-light-op").html(data.next);
                    $(".content-box .update-process .progress-bar-info").css("width", percent + "%");
                    $(".content-box .update-process .progress-bar-info").html(percent + "%");
                    if (step < count) {
                        step++;
                        wb_update_process(step, count, data.next );
                    }
                    else {
                        $(".content-box .update-process .progress").removeClass("progress-striped active");
                        $(".content-box .update-process .btn-success").show("slow");
                        setTimeout(function(){
                            window.location.reload(true);
                        },1500);
                    }
                }
            });

    }
</script>
