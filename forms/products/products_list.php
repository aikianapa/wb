<div id="{{_GET[form]}}List" class="element-wrapper">
		<h3 class="element-header">
		 Список товаров
		 <button class="btn btn-sm btn-success pull-right" data-wb-ajax="/form/edit/{{_GET[form]}}/_new" data-wb-append="body">
		   <i class="os-icon os-icon-ui-22"></i> Добавить товар
		 </button>
		</h3>
	<div class="row">
		<div class="col-sm-3">
				<div class="themed-background-dark text-light">
					<b class="">Категории
					<a href="#" data-ajax="mode=edit&amp;form=tree&amp;id={{_GET[form]}}_category" class="text-light pull-right"
					data-toggle="modal" data-target="#treeEdit" data-html="#treeEdit .modal-body">
					<span class="fa fa-gear"></span></a>
					</b>
					
				<ul id="{{_GET[form]}}Catalog" data-wb-role="tree" data-wb-item="{{_GET[form]}}_category" data-add="true" class="sidebar-nav list-unstyled">
					<li>
						<a data-wb-ajax="/form/list/products/{{id}}/" title="{{name}}" data-wb-html="#{{_GET[form]}}List .list">{{name}}</a>
					</li>
				</ul>
				</div>
				<div data-role="include" src="modal" data-wb-id="treeEdit" data-wb-formsave="#treeEditForm" data-add="false" data-header="Категории"></div>
		</div>
		<div class="col-sm-9 list">
	
			  <div class="table-responsive">
				<table class="table table-striped formlist">
				  <thead>
					<tr>
					  <th data-sort="name">Наименование</th>
					  <th data-sort="price">Цена</th>
					  <th>Действие</th>
					</tr>
				  </thead>
				  <tbody  data-wb-role="foreach" data-wb-from="result" data-wb-sort="name" data-wb-size="15">
					<tr item="{{id}}">
					  <td>{{name}}</td>
					  <td align="right">{{price}}</td>
                      <td class="text-right" data-wb-role="include" src="/engine/forms/common/item_actions.php">
					</tr>
				  </tbody>
				</table>
			  </div>
		</div>
	</div>
</div>

<style>
	#{{_GET[form]}}List .themed-background-dark {padding:10px;}
	#{{_GET[form]}}List #{{_GET[form]}}Catalog {padding-left: 0px;}
	#{{_GET[form]}}List #{{_GET[form]}}Catalog a {cursor:pointer;}
	#{{_GET[form]}}List #{{_GET[form]}}Catalog ul li {font-weight:normal;width: 100%;line-height: auto;}
	#{{_GET[form]}}List #{{_GET[form]}}Catalog a {display: inline-block; width: 95%; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;}
	#{{_GET[form]}}List #{{_GET[form]}}Catalog > li > a {width:100%;}
		
</style>
<script>

	$(document).on("tree_after_formsave",function(event,name,item,form,res){
		template_set_data("#{{_GET[form]}}Catalog");
	});
</script>
