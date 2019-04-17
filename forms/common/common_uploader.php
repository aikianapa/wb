	<div class="form-group row">
	  <label class="col-sm-2 control-label">{{_LANG[gallery]}}</label>
	   <div class="col-sm-3">
			<select name="images_position[pos]" class="form-control">
			<option value="">{{_LANG[not]}}</option>
			<option value="top">{{_LANG[above]}}</option>
			<option value="bottom">{{_LANG[below]}}</option>
			</select>
	   </div>
		<label class="col-sm-2 control-label">{{_LANG[size_wh]}}</label>
		<div class="col-sm-2"><input type="number" class="form-control" placeholder="{{_ENV[thumb_width]}}" name="images_position[width]"></div>
		<div class="col-sm-2"><input type="number" class="form-control" placeholder="{{_ENV[thumb_height]}}" name="images_position[height]"></div>
	</div>

	<div class="form-group row">
		<label class="col-sm-2 control-label">{{_LANG[text]}}</label>
		<div class="col-sm-3">
			<select name="intext_position[pos]" class="form-control">
			<option value="">{{_LANG[not]}}</option>
			<option value="left">{{_LANG[left]}}</option>
			<option value="right">{{_LANG[right]}}</option>
			</select>
		</div>
		<label class="col-sm-2 control-label">{{_LANG[size_wh]}}</label>
		<div class="col-sm-2"><input type="number" class="form-control" placeholder="{{_ENV[intext_width]}}" name="intext_position[width]"></div>
		<div class="col-sm-2"><input type="number" class="form-control" placeholder="{{_ENV[intext_height]}}" name="intext_position[height]"></div>
	</div>

<div id="comImagesUpl" data-role="tabpanel">
	<input type="hidden" name="images" data-wb-role="uploader" multiple>
</div>
<script type="text/locale">
[rus]
gallery		= "Галлерея"
text		= "В текст"
not		= "Нет"
above		= "Сверху"
below		= "Снизу"
left		= "Слева"
right		= "Справа"
size_wh		= "Размер (Ш/В)"
[eng]
gallery		= "Gallery"
text		= "To text"
not		= "Not"
above		= "Above"
below		= "Below"
left		= "Left"
right		= "Right"
size_wh		= "Size (W/H)"
</script>
