<div id="content" class="app-content box-shadow-z2 pjax-container" role="main">
<style>
    #add-todo-form {padding-top:1em;}
  .todo-list .hide {display:none;}
  .todo-list .list-item * {color:#666;}
	.todo-list .list-item input[type=checkbox] {width:1.5em;height:1.5em;}
	.todo-list .item-title {display:inline-block; width: 100%;}
	.todo-list [type=datetimepicker] {border:0;background:transparent;}
	.todo-list .todo-done .item-title {text-decoration:line-through;}
	.todo-list .danger {background-color: rgba(244, 67, 54, 0.3)!important;}
	.todo-list .success {background-color: rgba(76, 175, 80, 0.3)!important;}
	.todo-list .muted {background-color: rgba(158, 158, 158, 0.4);}
	#content .nav .dropdown-menu {margin-left: -140px;}
	#switcher {display:none;}
</style>


<div id="list">

<div class="row">
<div class="col-12">
<ul class="nav navbar-nav pull-right">
<li class="nav-item dropdown">
<a class="nav-link text-muted" href="#" data-toggle="dropdown">
<i class="fa fa-ellipsis-v">
</i>
</a>

		<div class="dropdown-menu status">
			<a class="dropdown-item" href="javascript:void(0);" data-status="success">
			<i class="fa fa-dot-circle-o text-success"></i> Активные</a>

			<a class="dropdown-item" href="javascript:void(0);" data-status="danger">
			<i class="fa fa-dot-circle-o text-danger"></i> Важные</a>

			<a class="dropdown-item" href="javascript:void(0);" data-status="muted">
			<i class="fa fa-circle-o text-muted"></i> Архивные</a>

		</div>

</li>
</ul>



<h3>Чек-лист</h3>
</div>
</div>
<div class="row">
	<div class="col-12">
<ul class="list-group todo-list"
      data-wb-role="foreach" data-wb-table="todo" data-sort="time"
			data-size="false" where='user = "{{_SESS[user_id]}}"'>
	<meta data-wb-role="variable" var="class" value="todo-done" where='done<>""' data-hide="*">
	<li class="list-group-item list-item {{class}} {{status}} hide" item="{{id}}" data-id="{{id}}" data-status="{{status}}">

	<div class="col-1">
	<label class="md-check p-r-xs">
	<input type="checkbox" name="done">
	<i></i>
	</label>
	</div>
	<div class="list-body col-10">
		<span class="item-title _500">{{task}}</span>
		<div>
			<i class="fa fa-clock-o"></i>
			<input type="datetimepicker" class="text-xs" name="time">
		</div>
	</div>
	<a class="col-1 todo-close pull-right" href="javascript:void(0);"><i class="fa  fa-trash-o"></i></a>
	</li>
</ul>
    <div data-append=".todo-list">
      <div data-block="danger"></div>
      <div data-block="success"></div>
      <div data-block="warn"></div>
      <div data-block="muted" class="hide"></div>
    </div>
</div>
</div>
<div class="row">
		<form id="add-todo-form" class="col-12 col-sm-6">
			<div class="input-group">
				<input type="text" id="add-todo" name="add-todo" class="form-control" placeholder="Добавить задачу.." >
				<span class="input-group-addon"><span class="text-sm text-muted bottom_counter"><strong></strong> из <strong></strong></span></span>
				<span class="input-group-addon">Добавить</span>

			</div>
		</form>

</div>

</div>

					<script src="/engine/forms/todo/js/todo.js?{{_SESS[_new]}}"></script>
					<script>
						$(document).ready(function(){
							CompTodo.init();

						});
					</script>

</div>
