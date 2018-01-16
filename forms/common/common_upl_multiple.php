<div class="wb-uploader">
	<input type="hidden" name="images">
	<div id="filelist" class="list-group">Your browser doesn't have Flash, Silverlight or HTML5 support.</div>
	<br />
	<div class="uploader">
		<a id="pickfiles" class="btn btn-default hidden" href="javascript:;">Выбрать</a>
		<a id="uploadfiles" class="btn btn-default pull-left" href="javascript:;" style="z-index:100;">Загрузить</a>
		<p>Перетащите мышкой файлы в этот прямоугольник<br>
		или кликните по нему, чтобы найти файлы</p>
	</div>
	<br />
	<pre id="console"></pre>
	<div class="wbImagesAll">
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
	<ul class="gallery list-inline" data-wb-role="foreach" data-wb-from="images">
        <meta role="variable" var="class" value="" data-wb-hide="*">
        <meta role="variable" var="class" value="selected" where='visible="1"' data-wb-hide="*">
		<li class="thumbnail col-3 {{_VAR[class]}}" data-name="{{img}}" title="{{title}}" alt="{{alt}}" data-wb-where='"{{img}}">""' data-wb-hide="wb">
			<img data-wb-role='thumbnail' size='320px;240px;bkg' style='height:auto;' class='img-fluid' src="{{%path}}/{{img}}"/>
			<a href="#" class="btn btn-outline-primary delete" data-toggle="dropdown"><span class="fa fa-trash"></span></a>
			<a href="#" class="btn btn-outline-primary info"><span class="fa fa-info"></span></a>
		</li>
	</ul>
	<script type="text/template" style="display:none;" class="wbImagesDropdownTpl">
		<div class="dropdown-menu">
			<a href="javascript:void(0);" class="dropdown-item delete-confirm"><i class="fa fa-trash"></i> Удалить</a>
			<a href="javascript:void(0);" class="dropdown-item"><i class="fa fa-close"></i> Отмена</a>
		</div>
	</script>
	</div>
</div>
