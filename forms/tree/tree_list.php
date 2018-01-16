
<div class="element-wrapper">
	<h6 class="element-header">
	 Список каталогов
	 <button class="btn btn-sm btn-success pull-right" data-wb-ajax="/form/edit/{{_GET[form]}}/_new" data-wb-append="body">
	   <i class="fa fa-plus"></i> Добавить каталог
	 </button>
	</h6>
	<div class="element-box">
	  <div class="table-responsive">
		<table class="table table-lightborder">
		  <thead>
			<tr>
			  <th>Идентификатор</th>
			  <th>Заголовок</th>
			  <th class="text-right">Действие</th>
			</tr>
		  </thead>
		  <tbody data-wb-role="foreach" data-wb-table="tree" data-wb-add="true" data-wb-size="20" data-wb-sort="id">
			<tr>
			  <td class="nowrap">{{id}}</td>
			  <td>{{header}}<br><small>{{techdescr}}</small></td>
			  <td class="text-right" data-wb-role="include" src="/engine/forms/common/item_actions.php">
			  </td>
			</tr>
		  </tbody>
		</table>
	  </div>
	</div>
</div>
