	<div class="form-group row">
	  <label class="col-sm-2 control-label">Галерея</label>
	   <div class="col-sm-3">
			<select name="images_position[pos]" class="form-control">
			<option value="">Нет</option>
			<option value="top">Сверху</option>
			<option value="bottom">Снизу</option>
			</select>
	   </div>
		<label class="col-sm-2 control-label">Размер (Ш/В)</label>
		<div class="col-sm-2"><input type="number" class="form-control" placeholder="200" name="images_position[width]"></div>
		<div class="col-sm-2"><input type="number" class="form-control" placeholder="160" name="images_position[height]"></div>
	</div>

	<div class="form-group row">
		<label class="col-sm-2 control-label">В текст</label>
		<div class="col-sm-3">
			<select name="intext_position[pos]" class="form-control">
			<option value="">Нет</option>
			<option value="left">Слева</option>
			<option value="right">Справа</option>
			</select>
		</div>
		<label class="col-sm-2 control-label">Размер (Ш/В)</label>
		<div class="col-sm-2"><input type="number" class="form-control" placeholder="200" name="intext_position[width]"></div>
		<div class="col-sm-2"><input type="number" class="form-control" placeholder="160" name="intext_position[height]"></div>
	</div>

<div id="comImagesUpl" data-role="tabpanel">
	<input type="hidden" name="images" data-wb-role="uploader" multiple>
</div>
