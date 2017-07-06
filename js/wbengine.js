wb_include("/engine/js/underscore-min.js");
wb_include("/engine/js/backbone-min.js");
wb_include("/engine/js/php.js");
	
$(document).ready(function(){
	wb_delegates();
});

function wb_delegates() {
	wb_ajax();
	wb_pagination();
	wb_formsave();
}

function wb_include(url){
	if (!$(document).find("script[src='"+url+"']").length) {
		document.write('<script src="'+ url + '" type="text/javascript" ></script>\n');
	}
}


function wb_formsave() {
// <button data-formsave="#formId" data-src="/path/ajax.php"></button>
// data-formsave	-	JQ идентификатор сохраняемой формы
// data-form		-	переопределяет имя формы, по-умолчанию берётся аттрибут name тэга form
// data-src			-	путь к кастомному ajax обработчику (необязательно)

	$(document).undelegate(".modal-dialog:visible input, .modal-dialog:visible select, .modal-dialog:visible textarea","change");
	$(document).delegate(".modal-dialog:visible input, .modal-dialog:visible select, .modal-dialog:visible textarea","change",function(){
		$(".modal-dialog:visible").find("[data-formsave] span.glyphicon").removeClass("glyphicon-ok").addClass("glyphicon-save");
	});


	$(document).undelegate("[data-wb-formsave]:not([data-wb-role=include])","click");
	$(document).delegate("[data-wb-formsave]:not([data-wb-role=include])","click",function(){
		var formObj=$($(this).attr("data-wb-formsave"));
		if ($(this).attr("data-wb-add")=="false") {$(formObj).attr("data-wb-add","false");}
		$(this).find("span.glyphicon").addClass("loader");
		var save=wb_formsave_obj(formObj);
		$(this).find("span.glyphicon").removeClass("loader glyphicon-save").addClass("glyphicon-ok");
		if (save) {
			return save;
		} else {
			return {error:1};
		};
		return false;
	});
};

function wb_formsave_obj(formObj) {
	if (wb_check_required(formObj)) {
		var ptpl=formObj.attr("parent-template");
		var padd=formObj.attr("data-wb-add");
		// обработка switch из appUI (и checkbox вообще кроме bs-switch)
		var ui_switch="";
		formObj.find("input[type=checkbox]:not(.bs-switch)").each(function(){
			var swname=$(this).attr("name");
			if ($(this).prop("checked")==true) {ui_switch+="&"+swname+"=on";} else {ui_switch+="&"+swname+"=";}
		});

		// обработка bootstrap switch
		var bs_switch="";
		formObj.find(".bs-switch").each(function(){
			var bsname=$(this).attr("name");
			if (bsname!=undefined && bsname>"") {
				if ($(this).bootstrapSwitch("state")==true) {bs_switch+="&"+bsname+"=on";} else {bs_switch+="&"+bsname+"=";}
			}
		});
		if (formObj.find("input[name=id]").length) {
			var item_id=formObj.find("input[name=id]").val();
		} else {item_id=formObj.attr("data-wb-item");}
		var ic_date="";
		formObj.find("[name][type^=date]").each(function(){
			var dtname=$(this).attr("name");
			var type=$(this).attr("type");
			var mask="";
			if ($(this).attr("type")=="datepicker") {mask="Y-m-d";}
			if ($(this).attr("type")=="date") { mask="Y-m-d";}
			if ($(this).attr("type")=="datetimepicker") { mask="Y-m-d H:i";}
			if ($(this).attr("type")=="datetime") { mask="Y-m-d H:i";}
			if ($(this).attr("date-iconv")>"") {mask=$(this).attr("date-iconv");}
			ic_date+="&"+dtname+"="+date(mask,strtotime($(this).val()));
		});


		// прячем данные корзины перед сериализацией - нужно для orders_edit.php
		var cart=formObj.find("[data-wb-role=cart]");
		if (cart.length) {
			cart.find("input,select,textarea").each(function(){
				if ($(this).attr("disabled")!=undefined) {$(this).addClass("tmpDisabled");} else {$(this).prop("disabled");}
			});
			var form=formObj.serialize();
			cart.find("input,select,textarea").each(function(){
				if (!$(this).hasClass("tmpDisabled")) {$(this).removeAttr("disabled");}
			});

		} else {
			var form=formObj.serialize();
		}
		form+=ui_switch+bs_switch+ic_date;
		
				console.log(form);
		var name=formObj.attr("data-wb-form");
		var item=formObj.attr("data-wb-item");
		var oldi=formObj.attr("data-wb-item-old");

		
		if ($(this).attr("data-wb-form")!==undefined) {name=$(this).attr("data-wb-form");}
		if ($(this).attr("data-wb-src")!==undefined) {
			var src=$(this).attr("data-wb-src");
		} else {
			var src="/ajax/save/"+name+"/"+item;
		}
		if (oldi!==undefined) {src+="&copy="+oldi;}
		
		if (ptpl==undefined) {
			var ptpl=$(document).find("[data-wb-add=true][data-wb-tpl]").attr("data-wb-tpl");
		}
		if ($(this).parents("#engine__setup").length) {var setup=true;} else {setup=false;}
		if (name!==undefined) {
		var data = {mode: "save", form: name } ;
		$.ajax({
			type:		'POST',
			url: 		src,
			data:		form,
			success:	function(data){
				if ($.bootstrapGrowl) {
					$.bootstrapGrowl("Сохранено!", {
						ele: 'body',
						type: 'success',
						offset: {from: 'top', amount: 20},
						align: 'right',
						width: "auto",
						delay: 4000,
						allow_dismiss: true,
						stackup_spacing: 10 
					});
				}

				if (ptpl!==undefined && padd!=="false") {
					var tpl=$(document).find("script#"+ptpl).html();
					var list=$(document).find("[data-wb-tpl="+ptpl+"]");
					var post={
						tpl: tpl
					};
					
					var ret=false;
					if (list.attr("data-add")+""!=="false") {
					$.post("/ajax/setdata/"+name+"/"+item_id,post,function(ret){
						if (list.find("[item="+item_id+"]").length) {
							list.find("[item="+item_id+"]").after(ret);
							list.find("[item="+item_id+"]:first").remove();
						} else {
							list.prepend(ret);
						}
						list.find("[item="+item+"]").each(function(){
							if ($(this).attr("idx")==undefined) {$(this).attr("idx",$(this).attr("item"));}
						});
					});
					}

				}
				if (setup==true) {document.location.href="/login.htm";}
				$(document).trigger(name+"_after_formsave",[name,item,form,true]);
				return data;
			},
			error:		function(data){
				$(document).trigger(name+"_after_formsave",[name,item,form,false]);
				if ($.bootstrapGrowl) {
					$.bootstrapGrowl("Ошибка сохранения!", {
						ele: 'body',
						type: 'danger',
						offset: {from: 'top', amount: 20},
						align: 'right',
						width: "auto",
						delay: 4000,
						allow_dismiss: true,
						stackup_spacing: 10 
					});
				}
				return {error:1};
			}
		});
		}
	} else {
				$.bootstrapGrowl("Ошибка сохранения!", {
					ele: 'body',
					type: 'danger',
					offset: {from: 'top', amount: 20},
					align: 'right',
					width: "auto",
					delay: 4000,
					allow_dismiss: true,
					stackup_spacing: 10 
				});
	}
}

function wb_check_email(email) {
		if (email.match(/^([a-z0-9_-]+\.)*[a-z0-9_-]+@[a-z0-9_-]+(\.[a-z0-9_-]+)*\.[a-z]{2,6}$/i)) {
			return true; } else {return false;}
}

function wb_check_required(form) {
	var res=true;
	var idx=0;
	$(form).find("input,select,textarea").each(function(i){
	if ($(this).is("[required]:not([disabled],[type=checkbox]):visible")) {
			if ($(this).val()=="") {res=false; idx++; $(this).data("idx",idx); $(document).trigger("wb_required_false",[this]);}
			else {
				if ($(this).attr("type")=="email" && !wb_check_email($(this).val())) {
					res=false; idx++; 
					$(this).data("idx",idx); 
					$(this).data("error","Введите корректный email");
					$(document).trigger("wb_required_false",[this]);
				} else {$(document).trigger("wb_required_true",[this]);}
			}
		}
	});
	if (res==true) {$(document).trigger("wb_required_success",[form]);}
	if (res==false) {$(document).trigger("wb_required_danger",[form]);}
	return res;
}



function wb_ajax() {
	$(document).undelegate("[data-wb-ajax]","click");
	$(document).delegate("[data-wb-ajax]","click",function(){
		var link=this;
		var src=$(this).attr("data-wb-ajax");
		var ajax={};
		if ($(link).attr("data-wb-tpl")!==undefined) {ajax.tpl=$($(link).attr("data-wb-tpl")).html();}
		$.post(src,ajax,function(data){
			var html=$("<div>"+data+"</div>");
			var mid="";
			$(html).find("[id]").each(function(i){
				if (i==0) {mid=$(this).attr("id");}
				$("#"+$(this).attr("id")).remove();
			});
			$("script.sc-"+mid).remove(); $(html).find("script").addClass("sc-"+mid);
			$("style.st-"+mid).remove(); $(html).find("style").addClass("st-"+mid);
			data=$(html).html();
			if ($(link).attr("data-wb-remove")!==undefined) {$($(link).attr("data-wb-remove")).remove();}
			if ($(link).attr("data-wb-after")!==undefined) {$($(link).attr("data-wb-after")).after(data);}
			if ($(link).attr("data-wb-before")!==undefined) {$($(link).attr("data-wb-before")).before(data);}
			if ($(link).attr("data-wb-html")!==undefined) {$($(link).attr("data-wb-html")).html(data);}
			if ($(link).attr("data-wb-replace")!==undefined) {$($(link).attr("data-wb-replace")).replaceWith(data);}
			if ($(link).attr("data-wb-append")!==undefined) {$($(link).attr("data-wb-append")).append(data);}
			if ($(link).attr("data-wb-prepend")!==undefined) {$($(link).attr("data-wb-prepend")).prepend(data);}
			wb_delegates();
			$("<div>"+data+"</div>").find(".modal[id]").each(function(i){
				if (i==0) {$("#"+$(this).attr("id")).modal();}
			});
			
		});
	});
	$("[data-wb-ajax][data-wb-autoload=true]").each(function(){$(this).trigger("click");$(this).removeAttr("data-wb-autoload")});
}

$(document).unbind("wb_required_false");
$(document).on("wb_required_false",function(event,that,text) {
	var delay=(4000+$(that).data("idx")*250)*1;
	var text=$(that).data("error");
	if (!text>"") {
		text="Заполните поле: "+$(that).attr("name");
		if ($(that).parents(".form-group").find("label").text()>"") {
			text="Заполните поле: "+$(that).parents(".form-group").find("label").text();
		}
		if ($(that).attr("placeholder")>"") {text="Заполните поле: "+$(that).attr("placeholder");}
	}

	$.bootstrapGrowl(text, {
		ele: 'body',
		type: 'warning',
		offset: {from: 'top', amount: 20},
		align: 'right',
		width: "auto",
		delay: delay,
		allow_dismiss: true,
		stackup_spacing: 10
	});
	
});


function wb_pagination(pid) {
	if (pid==undefined) {var slr=".pagination";} else {var slr=".pagination[id="+pid+"]";}
	$.each($(document).find(slr),function(idx){
		var id=$(this).attr("id");
		console.log(id);
		if ($(this).is(":not([data-idx])")) {$(this).attr("data-idx",idx);}
/*
		$("thead[data='"+id+"']").attr("data-idx",idx);
		$("thead[data='"+id+"'] th[data-sort]").each(function(){
			var desc=$(this).data("desc");
			if (desc==undefined || desc=="") {$(this).prepend("<i class='aiki-sort fa fa-arrows-v pull-left'></i>");}
			if (desc==undefined || desc=="" || desc=="false") {$(this).attr("data-desc","false");} else {$(this).attr("data-desc","true");}
			$(this).data("desc",$(this).attr("data-desc"));
		});
*/		
		$("[data-page^="+id+"]").hide().removeClass("hidden");
		$("[data-page="+id+"-1]").show();
		$(document).undelegate(".pagination[id="+id+"] li a, thead[data="+id+"] th[data-sort]","click");
		$(document).delegate(".pagination[id="+id+"] li a, thead[data="+id+"] th[data-sort]","click",function(event){
			if (!$(this).is("a") || !$(this).parent().hasClass("active")) { // отсекает дубль вызова ajax, но не работает trigger в поиске
			console.log("active_pagination(): Click");
			var that=$(this);
/*
			if ($(this).is("th[data-sort]")) {
				var $source=$(this).parents("thead");
				var page=$source.attr("data")+"-"+$(".pagination[id="+id+"] .active").attr("data-page");
				var sort=$(this).attr("data-sort");
				var desc=$(this).attr("data-desc");
				var sort=explode(" ",trim(sort));
				$(sort).each(function(i){
					var s=explode(":",sort[i]);
					if (s[1]==undefined) {
						if (desc==undefined) {s[1]="a";}
						if (desc!==undefined && desc=="false") {s[1]="a"; desc="false";}
						if (desc!==undefined && desc=="true") {s[1]="d"; desc="true";}
					}
					if (s[1]=="a") {s[1]="d";} else {s[1]="a";}
					$(that).attr("data-sort",implode(":",s));
				});
				var sort=$(this).attr("data-sort");
				
				$(this).parents("thead").find("th[data-sort]").each(function(){
					$(this).find(".aiki-sort").remove();
					$(this).data("desc","");
					$(this).removeAttr("data-desc");
				});
				if (desc=="true") {
					$(this).prepend("<i class='aiki-sort fa fa-long-arrow-up pull-left'></i>");
					$(this).data("desc","false");
				} else {
					$(this).prepend("<i class='aiki-sort fa fa-long-arrow-down pull-left'></i>");
					$(this).data("desc","true");
				}
			} else {
*/
				var $source=$(this).parents(".pagination");
				var page=$(this).attr("data");
				var sort=null;	
				var desc=null;
//			}
			if (substr(page,0,4)=="page") {
				// js пагинация
				$("[data-page^="+id+"]").hide();
				$("[data-page="+page+"]").show();
			} else {
				var cache=$source.attr("data-cache");
				var size=$source.attr("data-size");
				var idx=$source.attr("data-idx");
				var arr=explode("-",page);
				var tpl=$("#"+arr[1]).html();
				var foreach=$('<div>').append($("[data-wb-tpl="+arr[1]+"]").clone());
				$(foreach).find("[data-wb-tpl="+arr[1]+"]").html("");
				$(foreach).find("[data-wb-tpl="+arr[1]+"]").attr("data-sort",sort);
				$(foreach).find("[data-wb-tpl="+arr[1]+"]").removeAttr("data-desc");
				var loader=$(foreach).find("[data-wb-tpl="+arr[1]+"]").attr("data-loader");
				var offset=$(foreach).find("[data-wb-tpl="+arr[1]+"]").attr("data-offset"); if (offset==undefined) {offset=130;}
				var foreach=$(foreach).html();
				var param={tpl:tpl,tplid:arr[1],idx:idx,page:arr[2],size:size,cache:cache,foreach:foreach};
				var url="/ajax/pagination/";
				if ($("#"+id).data("find")!==undefined) {var find=$("#"+id).data("find");} else {
					var find=$source.attr("data-find");		
				}
				if (find>"") {find=urldecode(find);}
				param.find=find;
				param.sort=sort;
/*
				if (loader=="" || loader==undefined ) {
					$("[data-wb-tpl="+arr[1]+"]").html(ajax_loader());
				} else {
					var funcCall = loader + "(true);";
					eval ( funcCall );
				}
*/
				$("body").addClass("cursor-wait");
				$.ajax({
					async: 		true,
					type:		'POST',
					data:		param,
					url: 		url,
					success: 	function(data){
									var data=JSON.parse(data);
									$("[data-wb-tpl="+arr[1]+"]").html(data.data);
									if (data.pages>"1") {
										$(".pagination[id=ajax-"+pid+"]").show();
										var pid=$(data.pagr).attr("id");
										$(document).undelegate(".pagination[id="+pid+"] li a","click");
										$("#"+pid).after(data.pagr);
										$("#"+pid+":first").remove();
									} else {
										$(".pagination[id=ajax-"+arr[1]+"]").hide();
									}
									window.location.hash="page-"+idx+"-"+arr[2];
									wb_delegates();
									console.log("active_pagination(): trigger:after-pagination-done");
									$(document).trigger("after-pagination-done",[id,page,arr[2]]);
									$("body").removeClass("cursor-wait");
									if (loader=="" || loader==undefined ) {} else {
										var funcCall = loader + "(false);";
										eval ( funcCall );
									}
									
								},
					error:		function(data){
						$("body").removeClass("cursor-wait");
						if (loader=="" || loader==undefined ) {} else {
							var funcCall = loader + "(false);";
							eval ( funcCall );
						}
						(document).trigger("after-pagination-error",[id,page,arr[2]]);
						
					}
				});
			}
				$(this).parents("ul").find("li").removeClass("active");
				$(this).parent("li").addClass("active");

				var scrollTop=$("[data-wb-tpl="+arr[1]+"]").offset().top-offset;
				if (scrollTop<0) {scrollTop=0;}
				$('body,html').animate({scrollTop: scrollTop}, 1000);
				
				//$(document).trigger("after_pagination_click",[id,page,arr[2]]);
		}
		event.preventDefault();
		return false;
		});
	});
}
