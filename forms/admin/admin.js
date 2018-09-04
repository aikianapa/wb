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

    $(document).undelegate("#adminMain select[name=merchant]","change");
    $(document).delegate("#adminMain select[name=merchant]","click", function () {
        $(this).parents(".form-group").find("a[data-wb-ajax]").attr("data-wb-ajax","/module/"+$(this).val()+"/settings");
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
