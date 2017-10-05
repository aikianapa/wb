<div class="modal fade tree-edit" id="tree_{{form}}_{{name}}" data-keyboard="false" data-backdrop="true" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
	<div class="modal-content">
      <div class="modal-body">

<form class="form-horizontal" role="form">

	<div class="form-group row">
	  <label class="col-sm-3 form-control-label">Идентификатор</label>
	   <div class="col-sm-9"><input type="text" class="form-control" name="data-id" placeholder="Идентификатор" required ></div>
	</div>

	<div class="form-group row">
	  <label class="col-sm-3 form-control-label">Наименование</label>
	   <div class="col-sm-9"><input type="text" class="form-control" name="data-name" placeholder="Наименование" required ></div>
	</div>
</form>
	
<div class="tab-content  p-a m-b-md">
<div id="treeData" class="tab-pane fade show active" role="tabpanel">
	<form class="form-horizontal" role="form">
	</form>
</div>

<div id="treeDict" class="tab-pane fade" data-wb-role="include" src="/engine/forms/common/common_tree_dict.php" role="tabpanel"></div>
</div>




    </div>
		  <div class="modal-footer">
			<ul class="nav nav-tabs" role="tablist">
				<li class="nav-item"><a class="nav-link active" href="#treeData" data-toggle="tab">Данные</a></li>
				<li class="nav-item"><a class="nav-link" href="#treeDict" data-toggle="tab" >Словарь</a></li>
			</ul>
			<button type="button" class="btn btn-decondary" data-dismiss="modal" ><span class="fa fa-close"></span> Отмена</button>
			<button type="button" class="btn btn-primary tree-close" ><span class="fa fa-arrow-circle-left"></span> Изменить</button>
		  </div>

		</div>
</div>
</div>
