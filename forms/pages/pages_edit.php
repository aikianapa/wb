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

<form id="pagesEditForm" data-wb-form="pages" data-wb-item="{{id}}"  class="form-horizontal" role="form">
	<div class="form-group row">
	  <label class="col-sm-2 form-control-label">Имя записи</label>
	   <div class="col-sm-10"><input type="text" class="form-control" name="id" placeholder="Имя записи" required ></div>
	</div>

<div class="nav-active-primary">
<ul class="nav nav-tabs" role="tablist">
	<li class="nav-item"><a class="nav-link active" href="#pageDescr" data-toggle="tab">Характеристики</a></li>
	<li class="nav-item"><a class="nav-link" href="#pageText" data-toggle="tab" >Контент</a></li>
	<li class="nav-item"><a class="nav-link" href="#pageSource" data-toggle="tab" >Исходный код</a></li>
	<li class="nav-item"><a class="nav-link" href="#pageImages" data-toggle="tab">Изображения</a></li>
</ul>
</div>
<div class="tab-content  p-a m-b-md">
<br />
<div id="pageDescr" class="tab-pane fade show active" role="tabpanel">

	<div class="form-group row">
	  <label class="col-sm-2 form-control-label">Заголовок</label>
	   <div class="col-sm-10"><input type="text" class="form-control" name="header" placeholder="Заголовок"></div>
	</div>

	<div class="form-group row">
	  <label class="col-sm-2 form-control-label">Подвал</label>
	   <div class="col-sm-10"><input type="text" class="form-control" name="footer" placeholder="Подвал"></div>
	</div>

	<div class="form-group row">
	  <label class="col-sm-2 form-control-label">Шаблон</label>
	   <div class="col-sm-10">
		   <select class="form-control" name="template" placeholder="Шаблон" data-wb-role="foreach" data-wb-from="tpllist">
				<option value="{{0}}">{{0}}</option>
		   </select>
		</div>
	</div>

	<div class="form-group row">
	  <label class="col-sm-2 form-control-label">Описание</label>
	   <div class="col-sm-10"><input type="text" class="form-control" name="meta_description" placeholder="Описание"></div>
	</div>
	<div class="form-group row">
	  <label class="col-sm-2 form-control-label">Ключевые слова</label>
	   <div class="col-sm-10"><input type="text" class="form-control input-tags" name="meta_keywords" placeholder="Ключевые слова"></div>
	</div>

</div>

<div id="pageText" class="tab-pane fade" data-wb-role="include" src="editor" role="tabpanel"></div>
<div id="pageSource" class="tab-pane fade" data-wb-role="include" src="source" role="tabpanel">222</div>
<div id="pageImages" class="tab-pane fade"  src="" data-wb-ext="jpg png gif zip pdf doc" role="tabpanel">333</div>
</div>
</form>




		  <div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Закрыть</button>
			<button type="button" class="btn btn-primary" data-wb-formsave="#pagesEditForm"><span class="glyphicon glyphicon-ok"></span> Сохранить изменения</button>
		  </div>
		</div>
		</div>
</div>
</div>
