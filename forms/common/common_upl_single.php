<div class="wb-uploader single wbImagesAll d-none hidden">
    <input type="hidden" name="{{name}}">
	<div id="filelist" class="list-group">{{_LANG[error]}}</div>
	<div class="uploader">
		<ul class="gallery list-inline row" data-wb-role="foreach" data-wb-from="{{name}}" data-wb-limit="1" data-wb-hide="data-wb-role, data-wb-limit">
			<li class="col thumbnail" data-name="{{img}}" title="{{title}}" alt="{{alt}}" >
                <img data-wb-role='thumbnail' size='{{_ENV[thumb_width]}};{{_ENV[thumb_height]}}' width="600" height="600" style='height:auto;' class='col img-fluid' src="{{%path}}/{{img}}" />
			</li>
			<empty>
			<li class="col thumbnail" data-name="null" title="{{_LANG[addimg]}}" alt="{{_LANG[addimg]}}" idx="0">
				<img class="col img-fluid" style="background: url('/engine/uploads/__system/image.svg') 50% 15% no-repeat; display:inline-block; background-size: cover; background-clip: content-box;width:100%;"
				src="/engine/uploads/__system/transparent.png" >
			</li>
			</empty>
		</ul>
		<a id="pickfiles" class="btn btn-default hidden" href="javascript:;">Find</a>
		<a id="uploadfiles" class="btn btn-default pull-left" href="javascript:;" style="z-index:100;">Upload</a>
	</div>
	<pre id="console"></pre>
</div>

<script type="text/locale">
[rus]
	error 		= "Ваш браузер не поддерживает Flash, Silverlight или HTML5."
	addimg		= "Залить изображение"
[eng]
	error 		= "Your browser doesn't have Flash, Silverlight or HTML5 support."
	addimg		= "Upload image"
</script>
