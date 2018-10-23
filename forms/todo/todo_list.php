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
	.todo-list ul {padding:0;}
	.todo-list li.list-item {display: flex;}
	#content .nav .dropdown-menu {margin-left: -140px;}
	#switcher {display:none;}
</style>


<div id="list">

<div class="row">
<div class="col-12">
<h3 class="element-header">
    {{_LANG[checklist]}}
<ul class="nav navbar-nav pull-right" id="todo-status-menu">
<li class="nav-item dropdown">
<a class="nav-link text-muted" href="#" data-toggle="dropdown">
<i class="fa fa-ellipsis-v">
</i>
</a>
		<div class="dropdown-menu  dropdown-menu-right status">
			<a class="dropdown-item" href="javascript:void(0);" data-status="success">
			<i class="fa fa-dot-circle-o text-success"></i> {{_LANG[active]}}</a>

			<a class="dropdown-item" href="javascript:void(0);" data-status="danger">
			<i class="fa fa-dot-circle-o text-danger"></i> {{_LANG[danger]}}</a>

			<a class="dropdown-item" href="javascript:void(0);" data-status="muted">
			<i class="fa fa-circle-o text-muted"></i> {{_LANG[archive]}}</a>

		</div>

</li>
</ul>


    </h3>
</div>
</div>
<div class="row">
	<div class="col-12">
<ul class="list-group todo-list"
      data-wb-role="foreach" data-wb-table="todo" data-wb-sort="time"
			data-wb-size="false" data-wb-where='user = "{{_SESS[user_id]}}"'>
	<meta data-wb-role="variable" var="class" value="todo-done" where='done<>""' data-wb-hide="*">
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
	<a class="col-1 todo-close text-right" href="javascript:void(0);"><i class="fa  fa-trash-o fa-2x"></i></a>
	</li>
</ul>
    <div data-wb-append=".todo-list">
      <ul data-block="danger"></ul>
      <ul data-block="success"></ul>
      <ul data-block="warn"></ul>
      <ul data-block="muted" class="hide"></ul>
    </div>
</div>
</div>
<div class="row">
		<form id="add-todo-form" class="col-12 col-sm-6">
			<div class="input-group">
				<input type="text" id="add-todo" name="add-todo" class="form-control" placeholder="{{_LANG[addtask]}}..." >
				<span class="input-group-addon"><span class="text-sm text-muted bottom_counter"><strong></strong> {{_LANG[from]}} <strong></strong></span></span>
				<button class="input-group-addon ">{{_LANG[add]}}</button>

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
<script type="text/locale">
[eng]
	checklist	= "Check List"
	add 		= "Add"
	addtask		= "Add task"
	from		= "from"
	active		= "Active"
	danger		= "Danger"
	archive		= "Archive"
[rus]
	checklist	= "Список дел"
	add 		= "Добавить"
	addtask		= "Добавить задачу"
	from		= "из"
	active		= "Активные"
	danger		= "Важные"
	archive		= "Архивные"
</script>
