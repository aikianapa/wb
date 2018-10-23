$(document).ready(function () {
"use strict"
    $(document).undelegate("#module_backup_confirm","click");
    $(document).delegate("#module_backup_confirm","click", function () {
        backup_process();
    });

    $(document).undelegate("#backup_confirm [type=checkbox]","change");
    $(document).delegate("#backup_confirm [type=checkbox]","change", function () {
        $("#module_backup_confirm").prop("disabled",true);
        $("#backup_confirm .checks [type=checkbox]").each(function(){
            if ($(this).prop("checked")) {$("#module_backup_confirm").prop("disabled",false);}
            // если хоть один чек включен, то разрешаем кнопку
        });
    });

	function backup_process(step,count) {
	  $("#module_backup_confirm").hide();
	  $("#backup_confirm .sk-three-bounce").removeClass("hidden");
	  $("#backup_confirm .checks").hide();
	  var action=$("#backup_confirm meta[name=action]").attr("value");
	  var file=$("#backup_confirm meta[name=file]").attr("value");
	  var url="/ajax/backup/backup/"+action+"/"+file+"/";

	  console.log(action);

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
		      backup_process(step,count);
		    } else {
		      $("#backup_confirm .modal-body .msg").html(data.next);
		      $("#backup_confirm .sk-three-bounce").addClass("hidden");
		      if (action=="remove" && data.error==0) {$("#moduleBackupsList tr[data-name='"+file+"']").remove();}
		      if (action=="backup" && data.error==0) {backup_refresh();}
		      if (data.error==0) {setTimeout(function(){$("#backup_confirm").modal("hide");},2000);}
		    }
		}
	    });
	}

	function backup_refresh() {
		$("a[data-wb-ajax='/module/backup/']").trigger("click");

	}


});
