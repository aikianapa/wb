$(document).ready(function () {
"use strict"
    $("#module_update_btn").off("click");
    $("#module_update_btn").on("click", function () {
        wb_update_process();
    });

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
        //var panel = '<div class="widget update-process ">' + '	<div class="widget-content themed-background-dark text-light-op">Обновление системы</div>' + '	<div class="widget-content themed-background-muted text-center">' + '		<div class="progress progress-striped active">' + '			<div class="progress-bar progress-bar-info" role="progressbar" aria-valuemin="0" aria-valuemax="100" style="width: 0%">' + msg + '			</div>' + '		</div>' + '<br><a href="/admin/" class="btn btn-success">Обновление завершено</a>' + '	</div>' + '</div>';
        var panel=$("#moduleUpdate .widget.update-process");
        $("#moduleUpdate").html(panel);
        $(panel).removeClass("d-none");
        $("#moduleUpdate .update-process .btn-success").hide();
        $("#moduleUpdate .update-process .progress-bar-info").css("width", start + "%");
    }

        $.ajax({
            async: true
            , type: 'POST'
            , url: "/module/update/step/" + step
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
                $("#moduleUpdate .update-process .text-light-op").html( $("#moduleUpdate meta"+data.next).attr("value") );
                $("#moduleUpdate .update-process .progress-bar-info").css("width", percent + "%");
                $("#moduleUpdate .update-process .progress-bar-info").html(percent + "%");
                if (step < count) {
                    step++;
                    wb_update_process(step, count, data.next );
                }
                else {
                    $("#moduleUpdate .update-process .progress").removeClass("progress-striped active");
                    $("#moduleUpdate .update-process .btn-success").show("slow");
                    setTimeout(function(){
                        window.location.reload(true);
                    },2500);
                }
            }
        });

}

});
