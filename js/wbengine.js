wb_include("/engine/js/php.js");

$(document).ready(function(){
	wb_delegates();
});

function wb_delegates() {
	wb_ajax();
	wb_pagination();
	wb_formsave();
	wb_plugins();
	wb_base_fix();
	wb_multiinput();
}

function wb_include(url){
	if (!$(document).find("script[src='"+url+"']").length) {
		document.write('<script src="'+ url + '" type="text/javascript" ></script>\n');
	}
}

function wb_multiinput() {
	$.get("/ajax/getform/common/multiinput_menu/",function(data){$(document).data("wb-multiinput-menu",data);});
	$.get("/ajax/getform/common/multiinput_row/",function(data){$(document).data("wb-multiinput-row",data);});
	$(document).undelegate(".wb-multiinput","mouseenter");
	$(document).delegate(".wb-multiinput","mouseenter",function(){
		$(this).append("<div class='wb-multiinput-menu'>"+$(document).data("wb-multiinput-menu")+"</div>");
	});
	$(document).undelegate(".wb-multiinput","mouseleave");
	$(document).delegate(".wb-multiinput","mouseleave",function(){
		$(this).find(".wb-multiinput-menu").remove();
	});
	$(document).undelegate(".wb-multiinput","contextmenu");
	$(document).delegate(".wb-multiinput","contextmenu",function(e){
		var offset = $(this).offset();
		var relativeX = (e.pageX - offset.left -25);
		var relativeY = (e.pageY - offset.top);
		$(this).find(".wb-multiinput-menu").css("margin-left",relativeX+"px").css("margin-top",relativeY+"px");
		$(this).find(".wb-multiinput-menu [data-toggle=dropdown]").trigger("click");
		return false;
	});
	$(document).undelegate(".wb-multiinput-menu .dropdown-item","click");
	$(document).delegate(".wb-multiinput-menu .dropdown-item","click",function(e){
		var multi=$(this).parents("[data-wb-role=multiinput]");
		var tpl=$($(multi).attr("data-tpl")).html();
		var row=$(document).data("wb-multiinput-row");
		var name=$(multi).attr("name");
		
		row=str_replace("{{template}}",tpl,row);
		if ($(this).attr("href")=="#after") {$(this).parents(".wb-multiinput").after(row);}
		if ($(this).attr("href")=="#before") {$(this).parents(".wb-multiinput").before(row);}
		if ($(this).attr("href")=="#remove") {$(this).parents(".wb-multiinput").remove();}
		if (!$(multi).find(".wb-multiinput").length) {
			$(multi).append(row);
		};
		wb_multiinput_sort(multi);
		e.preventDefault();
	});
}

function wb_multiinput_sort(mi) {
	var name=$(mi).attr("name");
	$(mi).find(".wb-multiinput").each(function(i){
		$(this).find("input,select,textarea").each(function(){
			if ($(this).attr("data-wb-field")>"") {var field=$(this).attr("data-wb-field");} else {
				var field=$(this).attr("name");
			}
			if (field!==undefined && field>"") {
				$(this).attr("name",name+"["+i+"]["+field+"]");
				$(this).attr("data-wb-field",field);
			}
		});
	});
}

function wb_base_fix() {
	if ($("base").length) {
		var base=$("base").attr("href");
		$(document).undelegate("a","click");
		$(document).delegate("a","click",function(e){
			var hash=$(this).attr("href");
			if (hash!==undefined && substr(hash,0,1)=="#") {
				var loc=explode("#",window.location.href);
				var loc=str_replace(base,"",loc[0]);
				document.location=loc+hash;
				e.preventDefault();
			}
		});
	}
}

function wb_plugins(){
	$(document).ready(function(){
		if ($("[data-wb-src=datepicker]").length) {
			$("[type=datetimepicker]").datetimepicker({
				format: "dd.mm.yyyy hh:ii",
				autoclose: true,
				todayBtn: true
			});
		}
	});
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
	$(form).find("input[required],select[required],textarea[required],[type=password]").each(function(i){
	if ($(this).is(":not([disabled],[type=checkbox]):visible")) {
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
		if ($(this).is("[type=password]")) {
			var pcheck=$(this).attr("name")+"_check";
			if ($("input[type=password][name="+pcheck+"]").length) {
					if ($(this).val()!==$("input[type=password][name="+pcheck+"]").val()) {
						res=false;
						$(this).data("error","Пароли должны совпадать");
						$(document).trigger("wb_required_false",[this]);
					}
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
			$("<div>"+data+"</div>").find(".modal[id]").each(function(i){
				if (i==0) {$("#"+$(this).attr("id")).modal();}
			});
			$(document).trigger("wb_ajax_done",[link,src,data]);
			wb_plugins();
			wb_delegates();
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

function wb_setdata(selector,data,ret) {
	if (selector==undefined) {var selector="body";}
	if (data	==undefined) {var data={};}
	if ($(selector).length) {
		var tpl_id=$(selector).attr("data-wb-tpl");
		if (tpl_id!==undefined) {var html= urldecode($("#"+tpl_id).html());} else {
			if ($(selector).is("script")) {
				var html=$(selector).html();
			} else {
				var html=$(selector).outerHTML();
			}
		}
	} else {
		var html=selector;
	}
	var form="";
		var param={tpl:html,data:data};
		var url="/ajax/setdata/"+data.form+"/"+data.id;
		var res=null;
		$.when($.ajax({
			type:		'POST',
			async: 		false,
			data:		param,
			url: 		url})
		).done(function(data){
				if (ret==undefined || ret==false) {
					$(selector).after(data).remove();
				} else {
					res=data;
				}
		});
		return res;
}

function wb_pagination(pid) {
	if (pid==undefined) {var slr=".pagination";} else {var slr=".pagination[id="+pid+"]";}
	$.each($(document).find(slr),function(idx){
		var id=$(this).attr("id");
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

function wb_call_source(id	) {
	var eid="#"+id;
	if (!$(eid).parents(".formDesignerEditor").length) {
		$(document).data("sourceFile",null);
		var form = $(eid).parents("form");
		var theme=getcookie("sourceEditorTheme");
		var fsize=getcookie("sourceEditorFsize")*1;
		var source="&nbsp;";
		var fldname=$(eid).attr("name");
		if ($("[name="+fldname+"]").length) {source=$("[name="+fldname+"]").val();}

		if (theme==undefined || theme=="") {var theme="ace/theme/chrome"; 	setcookie("sourceEditorTheme",theme);}
		if (fsize==undefined || fsize=="") {var fsize=12; 					setcookie("sourceEditorFsize",fsize);}
		if ($(document).data("sourceClipboard")==undefined) {$(document).data("sourceClipboard","");}
		$(form).data(eid,ace.edit(id));
		$(form).data(eid).setTheme("ace/theme/chrome");
		$(form).data(eid).setOptions({
				enableBasicAutocompletion: true,
				enableSnippets: true
		});
		$(form).data(eid).getSession().setUseWrapMode(true);
		$(form).data(eid).getSession().setUseSoftTabs(true);
		$(form).data(eid).setDisplayIndentGuides(true);
		$(form).data(eid).setHighlightActiveLine(false);
		$(form).data(eid).setAutoScrollEditorIntoView(true);
		$(form).data(eid).commands.addCommand({
			name: 'save',
			bindKey: {win: 'Ctrl-S',  mac: 'Command-S'},
			exec: function() {
				console.log(form);
				//wb_formsave(form);
			},
			readOnly: false
		});
		$(form).data(eid).gotoLine(0,0);
		$(form).data(eid).resize(true);
		if ($("#cke_text .cke_contents").length) {var ace_height=$("#cke_text .cke_contents").height();} else {var ace_height=400;}
		$(".ace_editor").css("height",ace_height);
		$(form).data(eid).setTheme(theme);
		$(form).data(eid).setFontSize(fsize);
		$(form).data(eid).setValue(source);
		$(form).data(eid).gotoLine(0,0);
		$(form).data(eid).getSession().setMode("ace/mode/php");
		wb_call_source_events(eid,fldname);
		return $(form).data(eid);
	}
}


function wb_call_source_events(eid,fldname) {

	var tmp=explode("-",eid);
	var toolbar=tmp[0]+"-toolbar-"+tmp[1]+" ";
	$(toolbar).data("editor",false);
	$(toolbar).next(".ace_editor").attr("name",fldname).attr("id",substr(eid,1));
	var form = $(eid).parents("form");
	$(form).data(eid).getSession().on('change', function(e) {
		if ($(toolbar).data("editor")==undefined) {$(toolbar).data("editor",false);}
		if ($(toolbar).data("editor")==false && $(document).data("editor")!==true) {
			$(toolbar).data("editor",true);
			setTimeout(function(){
				$(document).trigger("sourceChange",{
								"value" : $(form).data(eid).getSession().getValue(),
								"field"	: fldname,
								"form"	: $(toolbar).parents("form")
				});
				$(toolbar).data("editor",false);
			},500);
		}
	});

	$('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
		if ($(e.target.hash).find(eid).length) {
			var form = $(eid).parents("form");
			var val=$(form).data(eid).getSession().getValue();
			$(form).data(eid).getSession().setValue(val);
		}
	});


	$(document).on("editorChange",function(e,data){
		$(document).data("editor",true);
		var eid="#"+$(".ace_editor[name="+data.field+"]").attr("id");
		var form = $(eid).parents("form");
		$(form).data(eid).getSession().setValue(data.value);
		setTimeout(function(){$(document).data("editor",false);},30);
	});




	$(document).undelegate(toolbar+" button","click");
	$(document).delegate(toolbar+" button","click",function(e){
		var theme=getcookie("sourceEditorTheme");
		var fsize=getcookie("sourceEditorFsize");
		if (theme==undefined || theme=="") {var theme="ace/theme/chrome";	setcookie("sourceEditorTheme",theme);}
		if (fsize==undefined || fsize=="") {var fsize=12; 					setcookie("sourceEditorFsize",fsize);}

		//if ($(this).hasClass("btnCopy")) 		{$(document).data("sourceFile",$(form).data(eid).getCopyText());}
		//if ($(this).hasClass("btnPaste")) 		{$(form).data(eid).insert($(document).data("sourceFile"));}
		if ($(this).hasClass("btnCopy")) 		{$(document).data("sourceClipboard",$(form).data(eid).getCopyText());}
		if ($(this).hasClass("btnPaste")) 		{$(form).data(eid).insert($(document).data("sourceClipboard"));}
		if ($(this).hasClass("btnUndo")) 		{$(form).data(eid).execCommand("undo");}
		if ($(this).hasClass("btnRedo")) 		{$(form).data(eid).execCommand("redo");}
		if ($(this).hasClass("btnFind")) 		{$(form).data(eid).execCommand("find");}
		if ($(this).hasClass("btnReplace")) 	{$(form).data(eid).execCommand("replace");}
		if ($(this).hasClass("btnLight")) 		{$(form).data(eid).setTheme("ace/theme/chrome"); setcookie("sourceEditorTheme","ace/theme/chrome");}
		if ($(this).hasClass("btnDark")) 		{$(form).data(eid).setTheme("ace/theme/monokai");  setcookie("sourceEditorTheme","ace/theme/monokai");}
		if ($(this).hasClass("btnClose")) 	{
			$(form).data(eid).setValue("");
			$(document).data("sourceFile",null);
			$("#sourceEditorToolbar .btnSave").removeClass("btn-danger");
		}
		if ($(this).hasClass("btnFontDn")) 	{
			if (fsize>8) {fsize=fsize*1-1;}
			$(form).data(eid).setFontSize(fsize); setcookie("sourceEditorFsize",fsize);
		}
		if ($(this).hasClass("btnFontUp")) 	{
			if (fsize<20) {fsize=fsize*1+1;}
			$(form).data(eid).setFontSize(fsize); setcookie("sourceEditorFsize",fsize);
		}
		if ($(this).hasClass("btnFullScr")) 	{
			var div=$(this).parents(toolbar).parent();
			var offset=div.offset();
			if (!div.hasClass("fullscr")) {
				div.parents(".modal").addClass("fullscr");
				div.addClass("fullscr");
				$(this).parents(".modal").css("overflow-y","hidden");
				$("pre.ace_editor").css("height",$(window).height()-$(toolbar).height()-$(toolbar).next(".nav").height()-15);
			} else {
				div.removeAttr("style");
				$("pre.ace_editor").css("height","500px");
				div.removeClass("fullscr");
				div.parents(".modal").removeClass("fullscr");
				$(this).parents(".modal").css("overflow-y","auto");
			}
			window.dispatchEvent(new Event('resize'));
		}
		if ($(this).hasClass("btnSave")) 	{
			var fo=$(this).parents(".modal").find("[data-wb-formsave]").trigger("click");

			//wb_formsave(fo);
		}
		e.preventDefault();
		return false;
	});
}




function setcookie ( name, value, exp_y, exp_m, exp_d, path, domain, secure ) {
  var cookie_string = name + "=" + escape ( value );
  if ( exp_y )  {
    var expires = new Date ( exp_y, exp_m, exp_d );
    cookie_string += "; expires=" + expires.toGMTString();
  }
  if ( path ) cookie_string += "; path=" + escape ( path );
  if ( domain ) cookie_string += "; domain=" + escape ( domain );
  if ( secure ) cookie_string += "; secure";
   document.cookie = cookie_string;
}

function delete_cookie ( cookie_name ) {
  var cookie_date = new Date ( );  // current date & time
  cookie_date.setTime ( cookie_date.getTime() - 1 );
  document.cookie = cookie_name += "=; expires=" + cookie_date.toGMTString();
}

function getcookie ( cookie_name ) {
  var results = document.cookie.match ( '(^|;) ?' + cookie_name + '=([^;]*)(;|$)' );
  if ( results ) {  return ( unescape ( results[2] ) ); }  else { return null; }
}

jQuery.fn.outerHTML = function(s) {
    return s
        ? this.before(s).remove()
        : jQuery("<p>").append(this.eq(0).clone()).html();
};
