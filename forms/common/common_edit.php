<div class="modal fade" id="{{_form}}_{{_mode}}" data-keyboard="false" data-backdrop="true" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
	<div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h5 class="modal-title">{{_LANG[title]}}</h5>
      </div>
      <div class="modal-body">

<form id="{{_form}}EditForm" data-wb-form="{{_form}}" data-wb-item="{{_item}}"  class="form-horizontal" role="form">
	<div class="form-group row">
	  <label class="col-sm-2 form-control-label">{{_LANG[name]}}</label>
	   <div class="col-sm-10"><input type="text" class="form-control" name="id" placeholder="{{_LANG[name]}}" required ></div>
	</div>

<div class="nav-active-primary">
<ul class="nav nav-tabs" role="tablist">
	<li class="nav-item"><a class="nav-link active" href="#{{_form}}Descr" data-toggle="tab">{{_LANG[prop]}}</a></li>
	<li class="nav-item"><a class="nav-link" href="#{{_form}}Text" data-toggle="tab" >{{_LANG[content]}}</a></li>
	<li class="nav-item"><a class="nav-link" href="#{{_form}}Images" data-toggle="tab">{{_LANG[images]}}</a></li>
</ul>
</div>
<div class="tab-content  p-a m-b-md">
<br />
<div id="{{_form}}Descr" class="tab-pane fade show active" role="tabpanel">

	<div class="form-group row">
	  <label class="col-sm-2 form-control-label">{{_LANG[header]}}</label>
	   <div class="col-sm-10"><input type="text" class="form-control" name="header" placeholder="{{_LANG[header]}}"></div>
	</div>

	<div class="form-group row">
	  <label class="col-sm-2 form-control-label">{{_LANG[descr]}}</label>
	   <div class="col-sm-10"><input type="text" class="form-control" name="meta_description" placeholder="{{_LANG[descr]}}"></div>
	</div>

	<div class="form-group row">
		<label class="col-sm-2 form-control-label">{{_LANG[visible]}}</label>
		<div class="col-sm-2"><label class="switch switch-success"><input type="checkbox" name="active"><span></span></label></div>
	</div>


	<div class="form-group row">
	  <label class="col-sm-2 form-control-label">{{_LANG[keywords]}}</label>
	   <div class="col-sm-10"><input type="text" class="form-control input-tags" name="meta_keywords" placeholder="{{_LANG[keywords]}}"></div>
	</div>

</div>

<div id="{{_form}}Text" class="tab-pane fade" data-wb-role="include" src="editor" role="tabpanel"></div>
<div id="{{_form}}Images" class="tab-pane fade" data-wb-role="include" src="uploader" role="tabpanel"></div>
</div>
</form>


    </div>
		  <div class="modal-footer" data-wb-role="include" src="form" data-wb-name="common_close_save" data-wb-hide="wb">
		  </div>

		</div>
</div>
</div>

<script type="text/locale">
[eng]
        title		= "Edit item"
	name            = "Item name"
	header		= "Header"
	descr		= "Description"
	visible		= "Visible"
	keywords	= "Keywords"
	prop		= "Properties"
	content		= "Content"
	images		= "Images"
[rus]
        title		= "Редактирование записи"
	name            = "Имя записи"
	header		= "Заголовок"
	descr		= "Описание"
	visible		= "Отображать"
	keywords	= "Ключевые слова"
	prop		= "Характеристики"
	content		= "Контент"
	images		= "Изображения"
</script>
