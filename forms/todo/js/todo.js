/*
 *  Document   : compTodo.js
 *  Author     : pixelcave
 *  Description: Custom javascript code used in To do list page
 */

var CompTodo = function() {
    return {
        init: function() {
            var todoInput       = $('#add-todo');
            var todoOptions     = $('#todo-options');
            var todoInputVal    = '';
            var todoForm		= $('#todo-form');
            var todoEdit		= $('.item-title');
            var todoNav			= $('#list .navbar-nav');

            $('.todo-done input:checkbox').prop('checked', true);
            $('.todo-list .list-item').each(function(){
                var status=$(this).attr("data-status");
                $('.todo-list [data-block='+status+']').append($(this));
            });
            $('.todo-list .list-item').removeClass("hide");
            $('.todo-list [data-block=muted]').removeClass("hide").slideUp(0);

			countTodo();
			eventsTodo();

			function eventsTodo() {
				var todoInput       = $('#add-todo');
				var todoOptions     = $('#todo-options');
				var todoInputVal    = '';
				var todoForm		= $('#todo-form');
				var todoEdit		= $('.item-title');
				var todoNav			= $('#list .navbar-nav');


			todoNav.off('click','a[data-status]');
			todoNav.on('click','a[data-status]',function(){
          if ($(this).find(".fa-circle-o").length) {
            $(this).find(".fa-circle-o").removeClass("fa-circle-o").addClass("fa-dot-circle-o");
            $('.todo-list [data-block='+$(this).attr('data-status')+']').slideDown(200);
          } else {
            $(this).find(".fa-dot-circle-o").removeClass("fa-dot-circle-o").addClass("fa-circle-o");
            $('.todo-list [data-block='+$(this).attr('data-status')+']').slideUp(200);
          }
          countTodo(true);
				});

				/* Toggle todo state */
				$('.todo-list').off('click', 'input:checkbox');
				$('.todo-list').on('click', 'input:checkbox', function(){
						$(this).parents('[data-id]').toggleClass('todo-done');
						var data={};
						data.done="";
						data.id=$(this).parents('[data-id]').attr("data-id");
						var status=$(".nav .dropdown-menu.status a:last").attr("data-status");
						if ($(this).parents('[data-id]').hasClass('todo-done')) {
							data.done=1;
							data.status=status;
							$(this).parents('[data-id]').find('.list-body div, input').addClass("text-"+status);
						} else {
							$(this).parents('[data-id]').find('.list-body div, input').removeClass("text-"+status);
						}
						updTodo(data);
				});

				$('.todo-list').off('change', 'input[name=time]');
				$('.todo-list').on('change', 'input[name=time]', function(){
						var data={};
						data.id=$(this).parents('[data-id]').attr("data-id");
						data.time=$(this).val();
						updTodo(data);
				});


				todoEdit.off('click');
				todoEdit.on('click',function(){
					$(this).attr("contenteditable",true);
					$(this).focus();
				});


				$('.todo-list').off('click', '.todo-close');
				$('.todo-list').on('click', '.todo-close', function(){
						// Удаление записи
            var that=$(this);
						that.parents('[data-id]').slideUp(200);
						var data={};
						data.id=that.parents("[data-id]").attr("data-id");
            setTimeout(function(){that.parents("[data-id]").remove();},200)
						delTodo(data);
				});


				$('.todo-list').off('click', '[contenteditable]');
				$('.todo-list').on('click', '[contenteditable]', function(){
					return false;
				});

				$('.todo-list').off('keydown', '[contenteditable]');
				$('.todo-list').on('keydown', '[contenteditable]', function(e){
					var code = e.which;
					if(code==13) {
						$(this).parents('label').trigger('focusout');
						return false;
					}
				});

				$('.todo-list').off('focusout', '.item-title');
				$('.todo-list').on('focusout', '.item-title', function(){
						$(this).find("span[contenteditable=true]").removeAttr("contenteditable");
						var data={};
						data.id=$(this).parents("[data-id]").attr("data-id");
						data.task=$(this).text(); if (data.task=="") {data.task="&nbsp;";}
						updTodo(data);
						return false;
				});


				/* Add a new todo to the list */
				$('#add-todo-form').off('submit');
				$('#add-todo-form').on('submit', function(e){
					todoInputVal = todoInput.prop('value');
					if ( todoInputVal ) {
						var data={};
						data.task=todoInputVal;
						data.category=$('#todoCatalog li.active').attr("data-id");
						data.done="";
						data.status="warn";
						var id=addTodo(data);
						if (id!==false) {
							var data=getTodo(id,true);
							$('.todo-list [data-block=warn]').append(data);
							$('.todo-list .list-item[data-id='+id+']').slideDown(200).removeClass("hide");
							todoInput.prop('value', '').focus();
							eventsTodo();
							wb_plugins();
						}

					}
					e.preventDefault();
					return false;
				});
			}

            function refreshTodo(id) {
    					moment.locale("ru");
    					var html=getTodo(id,true);
    					var flag=false;
    					var itime=0;
    					var that=$('.todo-list [data-id='+id+']');
    					var cur=that.index();
              var curstat=that.attr("data-status");
    					that.replaceWith(html);
              var that=$('.todo-list [data-id='+id+']');
              var status=that.attr("data-status");
    					var time=moment(date("Y-m-d H:i:s",strtotime(that.find("input[name=time]").val()))).format("YYYY-MM-DD HH:mm:ss");
              that.removeClass("hide");

              if (curstat!==status) {cur="";}
              if (!$('.todo-list [data-block='+status+']').find(".list-item").length) {
                  $('.todo-list [data-id='+id+']').slideUp(200);
                  setTimeout(function(){
                    $('.todo-list [data-block='+status+']').append(that);
                    $('.todo-list [data-id='+id+']').slideDown(200);
                    wb_plugins();
                  },200);
              } else {
      					$('.todo-list [data-block='+status+']').find(".list-item").each(function(i){
      						if (!flag) {
      							itime=date("Y-m-d H:i:s",strtotime($(this).find("input[name=time]").val()));
      							itime=moment(itime).format("YYYY-MM-DD HH:mm:ss");
      							var item=$(this);
      							if (itime>=time) {
      								flag=true;
      								if (cur!==i) {
      									$('.todo-list [data-id='+id+']').slideUp(200);
      									setTimeout(function(){
      										item.before($('.todo-list [data-id='+id+']'));
      										$('.todo-list [data-id='+id+']').slideDown(200);
      										wb_plugins();
      									},200);
      								}
      							}
							if (itime<time) {
      								if ($(this).is(":last-child") && cur!==i-1) {
      									$('.todo-list [data-id='+id+']').slideUp(200);
      									setTimeout(function(){
      										item.after($('.todo-list [data-id='+id+']'));
      										$('.todo-list [data-id='+id+']').slideDown(200);
      										wb_plugins();
      									},200);
      								}
      							}
      						}
      					});
              }

    					eventsTodo();
			}


			function addTodo(data) {
				var res=false;
				$.ajax({
					url: "/ajax/todo/add",
					async:false, method: "post", data: data,
					success: function(data){
						data=JSON.parse(data);
						if (data.id!==undefined) {res=data.id;}
					}
				});
				return res;
			}


			function delTodo(data) {
				var err=false
				if (data.category==undefined || data.category=="") {data.category="unsorted";}
				$.ajax({
					url: "/ajax/todo/del",
					async: false, method: "post", data: data,
					success: function(data){
								data=JSON.parse(data);
								err=data;
								countTodo(true);
					}
				});
				return err;
			}

			function getTodo(id,html) {
				if (html==undefined) {var action="getitem";} else {var action="getitemhtml";}
				var res=false;
				var ajax= "/ajax/todo/"+action;
				$.ajax({
					url:ajax,
					async: false, method: "post", data: {id:id},
					success: function(data){
								if (action!=="getitemhtml") {data=JSON.parse(data);}
								res=data;
					}
				});
				return res;
			}

			function updTodo(data) {
				var id=data.id;
				$.ajax({
					url: "/ajax/todo/upd",
					async: false, method: "post", data: data,
					success: function(data){
								data=JSON.parse(data);
								err=data;
								refreshTodo(id);
								countTodo(true);
					}
				});
			}

			function countTodo(fast) {
          if (fast==undefined && fast!==true) {
    				$.ajax({
    					url: "/ajax/todo/counter",
    					async: false, method: "post",
    					success: function(data){
    								data=JSON.parse(data);
    								$("#content").find(".nav-item .counter").html(data);
    					}
    				});
    			}

          setTimeout(function(){
            var all=$(".todo-list .list-item").length;
            var vis=$(".todo-list .list-item:visible").length;
            $("#content").find(".nav-item .counter").html(all);
            $("#content .bottom_counter strong:eq(1)").html(all);
            $("#content .bottom_counter strong:eq(0)").html(vis);
          },300);
      }
        }
    };
}();
