<div class="modal fade" id="{{_form}}_{{_mode}}" data-keyboard="false" data-backdrop="true" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
	<div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h5 class="modal-title">{{header}}</h5>
      </div>
      <div class="modal-body">

<form id="{{_form}}EditForm" data-wb-form="{{_form}}" data-wb-item="{{_item}}"  class="form-horizontal" role="form">

<input type="hidden" class="form-control" name="id">


<div class="nav-active-primary">
<ul class="nav nav-tabs" role="tablist">
	<li class="nav-item"><a class="nav-link active" href="#{{_form}}Descr" data-toggle="tab">{{_LANG[descr]}}</a></li>
	<li class="nav-item"><a class="nav-link" href="#{{_form}}Text" data-toggle="tab" >{{_LANG[text]}}</a></li>
	<li class="nav-item"><a class="nav-link" href="#{{_form}}Property" data-toggle="tab" >{{_LANG[prop]}}</a></li>
	<li class="nav-item"><a class="nav-link" href="#{{_form}}Images" data-toggle="tab">{{_LANG[images]}}</a></li>
</ul>
</div>
<div class="tab-content  p-a m-b-md">
<br />
<div id="{{_form}}Descr" class="tab-pane fade show active" role="tabpanel">

	<div class="form-group row">
	  <label class="col-sm-3 form-control-label">{{_LANG[name]}}</label>
	   <div class="col-sm-9"><input type="text" class="form-control" name="name" placeholder="{{_LANG[name]}}" required></div>
	</div>

	<div class="form-group row">
	  <label class="col-sm-3 form-control-label">{{_LANG[articul]}}</label>
	   <div class="col-sm-3"><input type="text" class="form-control" name="articul" placeholder="{{_LANG[articul]}}"></div>
		<label class="col-sm-3 form-control-label">{{_LANG[visible]}}</label>
		<div class="col-sm-3"><label class="switch switch-success"><input type="checkbox" name="active"><span></span></label></div>
	</div>

	<div class="form-group row">
	  <label class="col-sm-3 form-control-label">{{_LANG[category]}}</label>
	   <div class="col-sm-9">
		   <select class="form-control" name="category" placeholder="{{_LANG[category]}}" data-wb-role="tree" data-wb-item="products_category">
				<option value="{{id}}">{{name}}</option>
		   </select>
		</div>
	</div>

	<div class="form-group row">
	  <label class="col-sm-3 form-control-label">{{_LANG[price]}}</label>
	   <div class="col-sm-3"><input type="number" class="form-control" name="price" min="1" placeholder="{{_LANG[price]}}"></div>
	  <label class="col-sm-3 form-control-label">{{_LANG[price_sale]}}</label>
	   <div class="col-sm-3"><input type="number" class="form-control" name="sale" min="1" placeholder="{{_LANG[price_sale]}}"></div>

	</div>

	<div class="form-group row">
	  <label class="col-sm-3 form-control-label">{{_LANG[descr]}}</label>
	   <div class="col-sm-9"><input type="text" class="form-control" name="meta_description" placeholder="{{_LANG[descr]}}"></div>
	</div>

	<div class="form-group row">
	  <label class="col-sm-3 form-control-label">{{_LANG[keywords]}}</label>
	   <div class="col-sm-9"><input type="text" class="form-control input-tags" name="meta_keywords" placeholder="{{_LANG[keywords]}}"></div>
	</div>


</div>

<div id="{{_form}}Text" class="tab-pane fade" data-wb-role="include" src="editor" role="tabpanel"></div>
<div id="{{_form}}Property" class="tab-pane fade" role="tabpanel">
	<div data-wb-role="multiinput" name="property">
			<div class="col-sm-5"><input type="text" class="form-control" name="prop" placeholder="{{_LANG[property]}}"></div>
			<div class="col-sm-7"><input type="text" class="form-control" name="value" placeholder="{{_LANG[value]}}"></div>
	</div>

</div>
<div id="{{_form}}Images" class="tab-pane fade" data-wb-role="include" src="uploader" role="tabpanel"></div>
</div>
</form>


    </div>
		  <div class="modal-footer" data-wb-role="include" src="form" data-wb-name="common_close_save" data-wb-hide="wb">

		</div>
</div>
</div>
<script type="text/locale" data-wb-role="include" src="products_common"></script>
<script>
        if ($("#{{_form}}EditForm [name=category]").val() == "")  {
                var url=$(".content-box").data("wb_ajax");
                url=explode("/",url);
                $("#{{_form}}EditForm [name=category]").val(url[4]);
        }
</script>
