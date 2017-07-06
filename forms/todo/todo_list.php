<div id="content" class="app-content box-shadow-z2 pjax-container" role="main">
<style>
    #add-todo-form {height: 3.5rem; vertical-align: middle; display: table-cell;}
	#add-todo-form .form-control {height:1.8rem;}

	.todo-list .item-title {display:inline-block; width: 100%;}
	.todo-list [type=datetimepicker] {border:0;background:transparent;}
	.todo-list .todo-done .item-title {text-decoration:line-through;}
	#content .nav .dropdown-menu {margin-left: -140px;}
	#switcher {display:none;}
</style>

<div class="app-header hidden-lg-up black lt b-b">
<div class="navbar" data-pjax>
<a data-toggle="modal" data-target="#aside" class="navbar-item pull-left hidden-lg-up p-r m-a-0">
<i class="ion-navicon">
</i>
</a>
<div class="navbar-item pull-left h5" id="pageTitle">Project</div>
<ul class="nav navbar-nav pull-right">
<li class="nav-item dropdown pos-stc-xs">
<a class="nav-link" data-toggle="dropdown">
<i class="ion-android-search w-24">
</i>
</a>
<div class="dropdown-menu text-color w-md animated fadeInUp pull-right">
<form class="navbar-form form-inline navbar-item m-a-0 p-x v-m" role="search">
<div class="form-group l-h m-a-0">
<div class="input-group">
<input type="text" class="form-control" placeholder="Search projects..."> <span class="input-group-btn">
<button type="submit" class="btn white b-a no-shadow">
<i class="fa fa-search">
</i>
</button>
</span>
</div>
</div>
</form>
</div>
</li>
<li class="nav-item dropdown pos-stc-xs">
<a class="nav-link clear" data-toggle="dropdown">
<i class="ion-android-notifications-none w-24">
</i> <span class="label up p-a-0 danger">
</span>
</a>
<div class="dropdown-menu pull-right w-xl animated fadeIn no-bg no-border no-shadow">
<div class="scrollable" style="max-height: 220px">
<ul class="list-group list-group-gap m-a-0">
<li class="list-group-item dark-white box-shadow-z0 b">
<span class="pull-left m-r">
<img src="images/a0.jpg" alt="..." class="w-40 img-circle">
</span> <span class="clear block">Use awesome <a href="#" class="text-primary">animate.css</a>
<br>
<small class="text-muted">10 minutes ago</small>
</span>
</li>
<li class="list-group-item dark-white box-shadow-z0 b">
<span class="pull-left m-r">
<img src="images/a1.jpg" alt="..." class="w-40 img-circle">
</span> <span class="clear block">
<a href="#" class="text-primary">Joe</a> Added you as friend<br>
<small class="text-muted">2 hours ago</small>
</span>
</li>
<li class="list-group-item dark-white text-color box-shadow-z0 b">
<span class="pull-left m-r">
<img src="images/a2.jpg" alt="..." class="w-40 img-circle">
</span> <span class="clear block">
<a href="#" class="text-primary">Danie</a> sent you a message<br>
<small class="text-muted">1 day ago</small>
</span>
</li>
</ul>
</div>
</div>
</li>
<li class="nav-item dropdown">
<a class="nav-link clear" data-toggle="dropdown">
<span class="avatar w-32">
<img src="images/a3.jpg" class="w-full rounded" alt="...">
</span>
</a>
<div class="dropdown-menu w dropdown-menu-scale pull-right">
<a class="dropdown-item" href="profile.html">
<span>Profile</span>
</a> <a class="dropdown-item" href="setting.html">
<span>Settings</span>
</a> <a class="dropdown-item" href="app.inbox.html">
<span>Inbox</span>
</a> <a class="dropdown-item" href="app.message.html">
<span>Message</span>
</a>
<div class="dropdown-divider">
</div>
<a class="dropdown-item" href="docs.html">Need help?</a> <a class="dropdown-item" href="signin.html">Sign out</a>
</div>
</li>
</ul>
</div>
</div>
<div class="app-body">
<div class="app-body-inner">
<div class="row-col">
<div class="col-xs-3 w-xl modal fade aside aside-lg" id="subnav">
</div>
<div class="col-xs-4 modal fade aside aside-xs" id="list">
<div class="row-col b-r light lt">
<div class="b-b">
<div class="navbar no-radius">
<ul class="nav navbar-nav pull-right m-l">
<li class="nav-item dropdown">
<a class="nav-link text-muted" href="#" data-toggle="dropdown">
<i class="fa fa-ellipsis-h">
</i>
</a>

		<div class="dropdown-menu status">
			<a class="dropdown-item" href="javascript:void(0);" data-status="success">
			<i class="fa fa-dot-circle-o text-success"></i>Активные</a>

			<a class="dropdown-item" href="javascript:void(0);" data-status="danger">
			<i class="fa fa-dot-circle-o text-danger"></i>Важные</a>

			<a class="dropdown-item" href="javascript:void(0);" data-status="muted">
			<i class="fa fa-circle-o text-muted"></i>Архивные</a>

		</div>

</li>
</ul>
<ul class="nav navbar-nav">
<li class="nav-item">
<span class="navbar-item m-r-0 text-md">Чек-лист</span>
</li>

<li class="nav-item">
<span class="navbar-item m-r-0 text-md">
		<form id="add-todo-form">
			<input type="text" id="add-todo" name="add-todo" class="form-control rounded" placeholder="Добавить задачу..">
		</form>
</span>
</li>

<li class="nav-item">
<a class="nav-link">
<span class="label rounded counter">0</span>
</a>
</li>
</ul>
</div>
</div>
<div class="row-row">
<div class="row-body scrollable hover">
<div class="row-inner">
<div class="col-sm-offset-4 col-sm-8 list todo-list" data-ui-list="b-r b-2x b-theme"
      data-role="foreach" form="todo" data-sort="time" data-loader="loaderTodo"
			data-size="false" where='user = "{{_SESS[user_id]}}"'>
	<meta data-role="variable" var="class" value="todo-done" where='done<>""' data-hide="*">
	<div class="list-item row-col {{class}} hide"  item="{{id}}" data-id="{{id}}" data-status="{{status}}">
		<a class="todo-close pull-right" href="javascript:void(0);"><i class="fa  fa-trash-o text-muted"></i></a>
	<div class="col-xs">
	<label class="md-check p-r-xs">
	<input type="checkbox" name="done">
	<i></i>
	</label>
	</div>
	<div class="list-body col-xs">
		<span class="item-title _500">{{task}}</span>
		<div class="text-{{status}}">
			<i class="fa fa-clock-o"></i>
			<input type="datetimepicker" class="text-{{status}} text-xs" name="time">
		</div>
	</div>
	</div>
</div>
    <div append=".todo-list">
      <div data-block="danger"></div>
      <div data-block="success"></div>
      <div data-block="warn"></div>
      <div data-block="muted" class="hide"></div>
    </div>
</div>
</div>
</div>
<div class="p-a b-t clearfix">
<!--div class="btn-group pull-right">
  <a href="#" class="btn btn-xs white circle"><i class="fa fa-fw fa-angle-left"></i></a>
  <a href="#" class="btn btn-xs white circle"><i class="fa fa-fw fa-angle-right"></i></a>
</div-->
<span class="text-sm text-muted bottom_counter">Показано <strong></strong> записей из <strong></strong>
</span>
</div>
</div>
</div>
</div>
</div>
</div>


					<script src="/engine/forms/todo/js/todo.js?{{_SESS[_new]}}"></script>
					<script>
						$(document).ready(function(){
							CompTodo.init();

						});
					</script>


</div>
