<div class="modal fade" id="tree_{{form}}_{{name}}" data-keyboard="false" data-backdrop="true" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
	<div class="modal-content">
      <div class="modal-body">

<form class="form-horizontal" role="form">
	<div class="form-group row">
	  <label class="col-sm-2 form-control-label">Идентификатор</label>
	   <div class="col-sm-10"><input type="text" class="form-control" name="id" placeholder="Идентификатор" required ></div>
	</div>

	<div class="form-group row">
	  <label class="col-sm-2 form-control-label">Наименование</label>
	   <div class="col-sm-10"><input type="text" class="form-control" name="name" placeholder="Наименование" required ></div>
	</div>
	
<div class="nav-active-primary">
<ul class="nav nav-tabs" role="tablist">
	<li class="nav-item"><a class="nav-link active" href="#treeData" data-toggle="tab">Данные</a></li>
	<li class="nav-item"><a class="nav-link" href="#treeDict" data-toggle="tab" >Словарь</a></li>
</ul>
</div>
<div class="tab-content  p-a m-b-md">
<br />
<div id="treeData" class="tab-pane fade show active" role="tabpanel">

	<div class="form-group row">
	  <label class="col-sm-2 form-control-label">Заголовок</label>
	   <div class="col-sm-10"><input type="text" class="form-control" name="header" placeholder="Заголовок"></div>
	</div>
</div>

<div id="treeDict" class="tab-pane fade" data-wb-role="include" src="/engine/forms/common/common_tree_dict.php" role="tabpanel"></div>

</div>
</form>


    </div>
		  <div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Закрыть</button>
			<button type="button" class="btn btn-primary" data-wb-formsave="#treesEditForm"><span class="glyphicon glyphicon-ok"></span> Сохранить изменения</button>
		  </div>

		</div>
</div>
</div>
