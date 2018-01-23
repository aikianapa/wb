<h5 class="element-header">Настройки</h5>
<form method="post" id="admin_settings" data-wb-form="admin" data-wb-item="settings" data-wb-allow="admin">
    <div class="nav-active-primary">
        <ul class="nav nav-tabs" role="tablist">
            <li class="nav-item"><a class="nav-link active" href="#adminMain" data-toggle="tab">Основные</a></li>
            <li class="nav-item"><a class="nav-link" href="#adminAdd" data-toggle="tab">Вставки</a></li>
            <li class="nav-item"><a class="nav-link" href="#adminTree" data-toggle="tab">Каталог</a></li>
            <li class="nav-item"><a class="nav-link" href="#adminUpdate" data-toggle="tab">Обновление</a></li>
            <li class="nav-item"><a class="nav-link" href="#adminBackups" data-toggle="tab">Бэкапы</a></li>
        </ul>
    </div>
    <div class="tab-content pd-y-20">
        <div id="adminMain" class="tab-pane fade show active" role="tabpanel">
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
        <div id="adminAdd" class="tab-pane fade" role="tabpanel">
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
                        <td>Имя файла</td>
                        <td>Действие</td>
                      </thead>
                      <tbody data-wb-role="foreach" id="adminBackupsList" data-wb-from="backups" data-wb-limit="10">
                        <tr data-name="{{name}}">
                          <td>{{date}}</td>
                          <td>{{type}}</td>
                          <td>{{name}}</td>
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
                                  data-wb-ajax="/ajax/admin/backup/restore/{{name}}/?confirm=true&date={{created}}" data-wb-append="body">
                                  <i class="fa fa-upload"></i> Восстановить
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="javascript:void(0);"
                                  data-wb-ajax="/ajax/admin/backup/remove/{{name}}/?confirm=true&date={{created}}" data-wb-append="body">
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
                    <button type="button" class="btn btn-warning pull-right" id="admin_btn_backup"><i class="fa fa-cloud-upload"></i> Создать резервную копию</button>
                </div>
            </div>
        </div>
    </div>
</form>


<div class="modal fade" id="backup_confirm" data-keyboard="false" data-backdrop="true" role="dialog" aria-hidden="true" data-wb-allow="admin" data-wb-where='"{{_ENV[route][mode]}}"="backup"'>
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
          Резервная копия <u>{{type}}</u> от {{date}}<br>
          {{action_name}} {{name}}?
        </div>
		  <div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal"><span class="fa fa-close"></span> Назад</button>
			<button type="button" class="btn btn-danger" id="admin_backup_confirm"><span class="fa fa-upload"></span> {{action_name}}</button>
		  </div>
		</div>
  </div>
</div>

<script>
    $(document).ready(function () {
        $("#admin_btn_update").on("click", function () {
            wb_update_process();
        });

        $(document).delegate("#admin_backup_confirm","click", function () {
            wb_backup_process();
        });
    });

    function wb_backup_process(step,count,msg) {
      $("#admin_backup_confirm").hide();
      var action=$("#backup_confirm meta[name=action]").attr("value");
      var file=$("#backup_confirm meta[name=file]").attr("value");
      var url="/ajax/admin/backup/"+action+"/"+file+"/";
      if (step==undefined) {step=0;} else {url=url+"?step="+step;}
      if (count==undefined) {var count = 0;}
        $.ajax({
            async: true
            , type: 'POST'
            , url: url
            , success: function (data) {
                var data = JSON.parse(data);
                if (data.count !== undefined) {count=data.count;}
                if (msg!==undefined) $("#backup_confirm .modal-body").html(msg);
                if (step !== count) {
                  step++;
                  wb_backup_process(step,count,data.next);
                } else {
                  $("#backup_confirm .modal-body").html(data.next);
                  if (data.error==0) {$("#adminBackupsList tr[data-name='"+file+"']").remove();}
                }
            }
        });
      //$("#backup_confirm").modal("hide");
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
            var panel = '<div class="widget update-process ">' + '	<div class="widget-content themed-background-dark text-light-op">Обновление системы</div>' + '	<div class="widget-content themed-background-muted text-center">' + '		<div class="progress progress-striped active">' + '			<div class="progress-bar progress-bar-info" role="progressbar" aria-valuemin="0" aria-valuemax="100" style="width: 0%">' + msg + '			</div>' + '		</div>' + '		<a href="/admin/" class="btn btn-success">Завершить обновление</a>' + '	</div>' + '</div>';
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
                    }
                }
            });

    }
</script>
