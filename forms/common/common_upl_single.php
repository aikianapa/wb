        <ul class="list-inline" role="foreach" data-wb-from="{{name}}" data-wb-limit="1">
            <li>{{img}}</li>
        </ul>
<div class="wb-uploader single wbImagesAll">
	<input type="hidden" name="{{name}}">
	<div id="filelist" class="list-group">Your browser doesn't have Flash, Silverlight or HTML5 support.</div>
	<div class="uploader">
		<ul class="gallery list-inline" role="foreach" data-wb-from="{{name}}" data-wb-limit="1">
			<li class="thumbnail" data-name="{{img}}" title="{{title}}" alt="{{alt}}" >
				<img data-wb-role='thumbnail' size='320px;240px;bkg' style='height:auto;' class='img-fluid' src="{{%path}}/{{img}}"/>
				<a href="#" class="btn btn-outline-primary delete" data-toggle="dropdown"><span class="fa fa-trash"></span></a>
				<a href="#" class="btn btn-outline-primary info"><span class="fa fa-info"></span></a>
			</li>			
		</ul>
		<a id="pickfiles" class="btn btn-default hidden" href="javascript:;">Выбрать</a>
		<a id="uploadfiles" class="btn btn-default pull-left" href="javascript:;" style="z-index:100;">Загрузить</a>
		<script type="text/template" style="display:none;" class="wbImagesDropdownTpl">
			<div class="dropdown-menu">
				<a href="javascript:void(0);" class="dropdown-item delete-confirm"><i class="fa fa-trash"></i> Удалить</a>
				<a href="javascript:void(0);" class="dropdown-item"><i class="glyphicon glyphicon-ban-circle"></i> Отмена</a>
			</div>
		</script>

	</div>
	<pre id="console"></pre>
</div>