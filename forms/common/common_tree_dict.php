	<div class="col-12" data-wb-role="multiinput" name="fields">
			<div class="col-6">
				<div class="form-group mb-0">
					<div class="input-group">
						<span class="input-group-addon wb-tree-dict-prop-btn"><i class="fa fa-gear"></i></span>
						<input class="form-control" placeholder="{{_LANG[name]}}" type="text" name="name">
					</div>
				</div>
			</div>
			<div class="col-6">
					<select class="form-control" name="type" placeholder="{{_LANG[type]}}">
					<option value="string">string</option>
					<option value="text">text</option>
					<option value="number">number</option>
					<option value="checkbox">checkbox</option>
					<option disabled>--== {{_LANG[plugins]}} ==--</option>
					<option value="forms">forms</option>
					<option value="editor">editor</option>
					<option value="source">source</option>
					<option value="gallery">gallery</option>
					<option value="image">image</option>
					<option value="multiinput">multiinput</option>
					<option value="switch">switch</option>
					<option value="enum">enum</option>
					<option value="tree">tree</option>
					<option value="snippet">snippet</option>
					<option value="tags">tags</option>
					<option value="phone">phone</option>
					<option value="mask">mask</option>
					<option value="module">module</option>
					<option value="datepicker">datepicker</option>
					<option value="datetimepicker">datetimepicker</option>
					<option disabled>--== {{_LANG[other]}} ==--</option>
					<option value="tel">tel</option>
					<option value="date">date</option>
					<option value="week">week</option>
					<option value="month">month</option>
					<option value="year">year</option>
					<option value="time">time</option>
					<option value="color">color</option>
					</select>
			</div>

		<div class="hidden wb-prop-fields">
			<div class="form-group row" data-type-allow="">
				<label class="col-sm-3 form-control-label">{{_LANG[label]}}</label>
				<div class="col-sm-9"><input class="form-control" placeholder="{{_LANG[label]}}" type="text" name="label"></div>
			</div>
			<div class="form-group row">
				<label class="col-sm-3 form-control-label">{{_LANG[default]}}</label>
				<div class="col-sm-9"><input class="form-control" placeholder="{{_LANG[default]}}" type="text" name="value"></div>
			</div>
			<div class="form-group row">
				<label class="col-sm-3 form-control-label">{{_LANG[form]}}</label>
				<div class="col-sm-9">
					<select class="form-control" data-wb-field="form" placeholder="{{_LANG[form]}}" data-wb-role="foreach" data-wb-from="_ENV[forms]" data-wb-tpl="false" data-wb-hide="wb">
						<option>{{0}}</option>
					</select>
				</div>
			</div>
			<div class="form-group row">
				<label class="col-sm-3 form-control-label">{{_LANG[mode]}}</label>
				<div class="col-sm-9">
					<input class="form-control" placeholder="{{_LANG[mode]}}" type="text" name="mode">
				</div>
			</div>
			<div class="form-group row">
				<label class="col-sm-3 form-control-label">{{_LANG[json]}}</label>
				<div class="col-sm-9"><input class="form-control" placeholder="{{_LANG[json]}}" type="text" name="prop"></div>
			</div>
			<div class="form-group row">
				<label class="col-sm-3 form-control-label">{{_LANG[class]}}</label>
				<div class="col-sm-9"><input class="form-control" placeholder="{{_LANG[class]}}" type="text" name="class"></div>
			</div>
			<div class="form-group row">
				<label class="col-sm-3 form-control-label">{{_LANG[css]}}</label>
				<div class="col-sm-9"><input class="form-control" placeholder="{{_LANG[css]}}" type="text" name="style"></div>
			</div>

		</div>
	</div>

<script type="text/locale">
[eng]
        label	= "Label"
        name    = "Field name"
        type	= "Field type"
        default = "Default value"
        json    = "JSON data"
        css	= "Style CSS"
        other	= "Other"
        plugins = "Plugins"
        form 	= "Form"
        mode	= "Mode"
        class 	= "Style class"
[rus]
        label	= "Метка"
        name    = "Имя поля"
        type	= "Тип поля"
        default = "Значение по-умолчанию"
        json    = "Данные JSON"
        css	= "Стиль CSS"
        other	= "Другие"
        plugins = "Плагины"
        form 	= "Форма"
        mode	= "Режим"
        class 	= "Класс стиля"
</script>
