<div class="modal fade" id="{{_GET[form]}}_{{_GET[mode]}}" data-keyboard="false" data-backdrop="true" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
	<div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h5 class="modal-title">{{header}}</h5>
      </div>
      <div class="modal-body">

<form id="{{_GET[form]}}EditForm" data-wb-form="{{_GET[form]}}" data-wb-item="{{_GET[item]}}"  class="form-horizontal" role="form">

<input type="hidden" class="form-control" name="id" placeholder="Имя записи">


<div class="nav-active-primary">
<ul class="nav nav-tabs" role="tablist">
	<li class="nav-item"><a class="nav-link active" href="#{{_GET[form]}}Descr" data-toggle="tab">Описание</a></li>
	<li class="nav-item"><a class="nav-link" href="#{{_GET[form]}}Text" data-toggle="tab" >Текст</a></li>
	<li class="nav-item"><a class="nav-link" href="#{{_GET[form]}}Property" data-toggle="tab" >Характеристики</a></li>
	<li class="nav-item"><a class="nav-link" href="#{{_GET[form]}}Images" data-toggle="tab">Изображения</a></li>
</ul>
</div>
<div class="tab-content  p-a m-b-md">
<br />
<div id="{{_GET[form]}}Descr" class="tab-pane fade show active" role="tabpanel">

	<div class="form-group row">
	  <label class="col-sm-3 form-control-label">Наименование</label>
	   <div class="col-sm-9"><input type="text" class="form-control" name="name" placeholder="Наименование" required></div>
	</div>
	
	<div class="form-group row">
	  <label class="col-sm-3 form-control-label">Артикул</label>
	   <div class="col-sm-3"><input type="text" class="form-control" name="descr" placeholder="Артикул"></div>
		<label class="col-sm-3 form-control-label">Отображать</label>
		<div class="col-sm-3"><label class="switch switch-success"><input type="checkbox" name="active"><span></span></label></div>
	</div>

	<div class="form-group row">
	  <label class="col-sm-3 form-control-label">Категория</label>
	   <div class="col-sm-9">
		   <select class="form-control" name="category" placeholder="Категория" data-wb-role="tree" data-wb-item="products_category">
				<option value="{{id}}">{{name}}</option>
		   </select>
		</div>
	</div>
	
	<div class="form-group row">
	  <label class="col-sm-3 form-control-label">Цена</label>
	   <div class="col-sm-3"><input type="number" class="form-control" name="price" min="1" placeholder="Цена розничная"></div>
	  <label class="col-sm-3 form-control-label">Цена распродажи</label>
	   <div class="col-sm-3"><input type="number" class="form-control" name="price" min="1" placeholder="Цена распродажи"></div>

	</div>

	<div class="form-group row">
	  <label class="col-sm-3 form-control-label">Описание</label>
	   <div class="col-sm-9"><input type="text" class="form-control" name="meta_description" placeholder="Описание"></div>
	</div>

	<div class="form-group row">
	  <label class="col-sm-3 form-control-label">Ключевые слова</label>
	   <div class="col-sm-9"><input type="text" class="form-control input-tags" name="meta_keywords" placeholder="Ключевые слова"></div>
	</div>


</div>

<div id="{{_GET[form]}}Text" class="tab-pane fade" data-wb-role="include" src="editor" role="tabpanel"></div>
<div id="{{_GET[form]}}Property" class="tab-pane fade" role="tabpanel">
	<div data-wb-role="multiinput" name="property">
			<div class="col-sm-5"><input type="text" class="form-control" name="prop" placeholder="Характеристика"></div>
			<div class="col-sm-7"><input type="text" class="form-control" name="value" placeholder="Значение"></div>
	</div>

</div>
<div id="{{_GET[form]}}Images" class="tab-pane fade" data-wb-role="include" src="uploader" role="tabpanel"></div>
</div>
</form>


    </div>
		  <div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Закрыть</button>
			<button type="button" class="btn btn-primary" data-wb-formsave="#{{_GET[form]}}EditForm"><span class="glyphicon glyphicon-ok"></span> Сохранить изменения</button>
		  </div>

		</div>
</div>
</div>
