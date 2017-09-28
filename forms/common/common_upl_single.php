<div class="wb-uploader single wbImagesAll">
	<li class="imagesAttr col-md-12">
		<div class="header">
			<button type="button" class="close" aria-hidden="true">&times;</button>
			<h4 class="title">Атрибуты изображения</h4>
		</div>

		<div class="form-group"><label class="col-sm-3 control-label">Ссылка</label>
		<div class="col-sm-9"><input type="text" class="form-control attr-link" readonly></div>
		</div>

		<div class="form-group"><label class="col-sm-3 control-label">Заголовок</label>
		<div class="col-sm-9"><input type="text" class="form-control attr-title" placeholder="Заголовок"></div>
		</div>

		<div class="form-group"><label class="col-sm-3 control-label">Описание</label>
		<div class="col-sm-9"><textarea type="text" class="form-control attr-alt" placeholder="Описание"></textarea></div>
		</div>

	</li>
	<input type="hidden" name="image">
	<div id="filelist" class="list-group">Your browser doesn't have Flash, Silverlight or HTML5 support.</div>

	<ul class="uploader gallery list-inline" data-wb-role="foreach" data-wb-from="image">
		<a id="pickfiles" class="btn btn-default hidden" href="javascript:;">Выбрать</a>
		<a id="uploadfiles" class="btn btn-default pull-left" href="javascript:;" style="z-index:100;">Загрузить</a>
		<li class="thumbnail" data-name="{{img}}" title="{{title}}" alt="{{alt}}" >
			<img data-wb-role='thumbnail' size='320px;240px;bkg' style='height:auto;' class='img-fluid' src="{{%path}}/{{img}}"/>
			<a href="#" class="btn btn-outline-primary delete" data-toggle="dropdown"><span class="fa fa-trash"></span></a>
			<a href="#" class="btn btn-outline-primary info"><span class="fa fa-info"></span></a>
		</li>
	</ul>
	<script type="text/template" style="display:none;" class="wbImagesDropdownTpl">
		<div class="dropdown-menu">
			<a href="javascript:void(0);" class="dropdown-item delete-confirm"><i class="fa fa-trash"></i> Удалить</a>
			<a href="javascript:void(0);" class="dropdown-item"><i class="glyphicon glyphicon-ban-circle"></i> Отмена</a>
		</div>
	</script>

	<pre id="console"></pre>
</div>

