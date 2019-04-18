<div class="modal tree-edit" id="tree_{{form}}_{{name}}" data-keyboard="false" data-backdrop="false" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
	<div class="modal-content">
    <div class="modal-header pb-0 pt-1" data-wb-role="include" src="/engine/forms/common/common_tree_edit_nav.php">
    </div>

      <div class="modal-body">

<form class="form-horizontal" role="form">
	<div class="form-group row">
	  <label class="col-sm-3 form-control-label">{{_LANG[id]}}</label>
	   <div class="col-sm-9"><input type="text" class="form-control" name="data-id" placeholder="{{_LANG[id]}}" required ></div>
	</div>

	<div class="form-group row">
	  <label class="col-sm-3 form-control-label">{{_LANG[name]}}</label>
	   <div class="col-sm-9"><input type="text" class="form-control" name="data-name" placeholder="{{_LANG[name]}}"></div>
	</div>
</form>

<div class="tab-content  p-a m-b-md">
<div id="treeData_{{form}}_{{name}}" class="treeData tab-pane show active" role="tabpanel">
	<form class="form-horizontal" role="form">
	</form>
</div>

<div id="treeDict_{{form}}_{{name}}" class="treeDict tab-pane" data-wb-role="include" src="/engine/forms/common/common_tree_dict.php" role="tabpanel"></div>
</div>

    </div>

		</div>
</div>
</div>

<script type="text/locale">
[eng]
        id    = "ID"
        name    = "Name"
[rus]
        id    = "Идентификатор"
        name    = "Наименование"
</script>
