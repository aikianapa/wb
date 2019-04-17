<div id="{{_form}}List" class="element-wrapper">
		<h3 class="element-header">
		 {{_LANG[list]}}
		 <button class="btn btn-sm btn-success pull-right" data-wb-ajax="/form/edit/{{_form}}/_new" data-wb-append="body">
		   <i class="fa fa-plus"></i> {{_LANG[add]}}
		 </button>
		</h3>
	<div class="row">
		<div class="col-sm-3">
			<div class="row">
				<div class="content-left">
          <label class="content-left-label">
          {{_LANG[categories]}}
						<a href="#" data-wb-ajax="/form/edit/tree/products_category" data-wb-append="body" class="text-light pull-right"
						data-toggle="modal" data-target="#treeEdit" >
						<span class="fa fa-gear"></span></a>
          </label>
					<ul id="{{_form}}Catalog" data-wb-role="tree" data-wb-item="{{_form}}_category" data-wb-add="true"  class="nav mg-t-1-force">
						<li class="nav-item">
							<a class="nav-link" data-wb-ajax="/form/list/products/{{id}}/" title="{{name}}" data-wb-html=".content-box">{{name}}</a>
						</li>
					</ul>
				</div>
			</div>
		</div>
		<div class="col-12 col-sm-9 list">

			  <div class="table-responsive">
				<table class="table table-striped formlist">
				  <thead>
					<tr>
					  <th data-sort="name">{{_LANG[name]}}</th>
					  <th class="text-right" data-sort="price">{{_LANG[price]}}</th>
					  <th class="text-right">{{_LANG[action]}}</th>
					</tr>
				  </thead>
				  <tbody  data-wb-role="foreach" data-wb-from="result" data-wb-add="true" data-wb-sort="name" data-wb-size="{{_ENV[page_size]}}">
					<tr item="{{id}}">
					  <td>{{name}}<br><small>{{articul}}</small></td>
					  <td align="right">{{price}}</td>
					<td class="text-right" data-wb-role="include" src="form" data-wb-name="common_item_actions"></td>
					</tr>
				  </tbody>
				</table>
			  </div>
		</div>
	</div>
</div>

<style>
	#{{_form}}List #{{_form}}Catalog a {cursor:pointer;}
	#{{_form}}List #{{_form}}Catalog a {overflow: hidden; text-overflow: ellipsis; white-space: nowrap;}
	#{{_form}}List .content-left {width: 100%;}
</style>
<script type="text/locale" data-wb-role="include" src="products_common"></script>
