<div id="{{_GET[form]}}List" class="element-wrapper">
		<h3 class="element-header">
		 Список товаров
		 <button class="btn btn-sm btn-success pull-right" data-wb-ajax="/form/edit/{{_GET[form]}}/_new" data-wb-append="body">
		   <i class="fa fa-plus"></i> Добавить товар
		 </button>
		</h3>
	<div class="row">
		<div class="col-sm-3">
				<div class="bg-dark text-light">
                    <div class="catalog-header">
                    Категории
					<a href="#" data-wb-ajax="/form/edit/tree/products_category" data-wb-append="body" class="text-light pull-right"
					data-toggle="modal" data-target="#treeEdit" >
					<span class="fa fa-gear"></span></a>
                    </div>	
				<ul id="{{_GET[form]}}Catalog" data-wb-role="tree" data-wb-item="{{_GET[form]}}_category" data-wb-tpl="true" class="sidebar-nav list-unstyled">
					<li>
						<a data-wb-ajax="/form/list/products/{{id}}/" title="{{name}}" data-wb-html=".content-box">{{name}}</a>
					</li>
				</ul>
				</div>

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
				  <tbody  data-wb-role="foreach" data-wb-from="result" data-wb-add="true" data-wb-sort="name" data-wb-size="15">
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
	#{{_GET[form]}}List .bg-dark {padding:10px;}
	#{{_GET[form]}}List #{{_GET[form]}}Catalog {padding-left: 0px;}
	#{{_GET[form]}}List #{{_GET[form]}}Catalog a {cursor:pointer;}
    #{{_GET[form]}}List #{{_GET[form]}}Catalog ul {list-style:none; padding-left:30px;}
	#{{_GET[form]}}List #{{_GET[form]}}Catalog ul li {font-weight:normal;width: 100%;line-height: auto;}
    #{{_GET[form]}}List #{{_GET[form]}}Catalog ul li:before {content:'›'; position:absolute; margin-left:-10px;}
	#{{_GET[form]}}List #{{_GET[form]}}Catalog a {display: inline-block; width: 95%; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;}
	#{{_GET[form]}}List #{{_GET[form]}}Catalog > li > a {width:auto;}
    #{{_GET[form]}}List .catalog-header {font-weight:bold; margin-bottom:10px;}
    #{{_GET[form]}}List .catalog-header a {width: auto;}
		
</style>
<script>

	$(document).on("tree_after_formsave",function(event,name,item,form,res){
		template_set_data("#{{_GET[form]}}Catalog");
	});
</script>
