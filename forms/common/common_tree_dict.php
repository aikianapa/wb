	<div class="col-sm-12" data-wb-role="multiinput" name="fields">

			<div class="col-sm-3 col-xs-12">
				<input class="form-control" placeholder="{{_LANG[name]}}" type="text" name="name">
			</div>

			<div class="col-sm-4 col-xs-12">
				<input class="form-control" placeholder="{{_LANG[label]}}" type="text" name="label">
			</div>
			<div class="col-sm-3 col-xs-12">
					<select class="form-control" name="type" placeholder="{{_LANG[type]}}">
					<option value="string">string</option>
					<option value="text">text</option>
					<option value="number">number</option>
					<option value="checkbox">checkbox</option>
					<option disabled>--== {{_LANG[plugins]}} ==--</option>
					<option value="editor">editor</option>
					<option value="source">source</option>
					<option value="gallery">gallery</option>
					<option value="image">image</option>
					<option value="multiinput">multiinput</option>
					<option value="switch">switch</option>
					<option value="call">call</option>
					<option value="enum">enum</option>
					<option value="tree">tree</option>
					<option value="snippet">snippet</option>
					<option value="tags">tags</option>
					<option value="phone">phone</option>
					<option value="mask">mask</option>
					<option value="datepicker">datepicker</option>
					<option value="datetimepicker">datetimepicker</option>
					<option disabled>--== {{_LANG[other]}} ==--</option>
					<option value="date">date</option>
					<option value="week">week</option>
					<option value="month">month</option>
					<option value="year">year</option>
					<option value="time">time</option>
					<option value="color">color</option>
					</select>
			</div>
			<div class="col-sm-2 col-xs-12">
				<input class="form-control" placeholder="{{_LANG[default]}}" type="text" name="value">
			</div>
			<div class="col-sm-3 hidden-xs">&nbsp;</div>
			<div class="col-sm-4 col-xs-12">
				<input class="form-control" placeholder="{{_LANG[json]}}" type="text" name="prop">
			</div>
			<div class="col-sm-5 col-xs-12">
				<input class="form-control" placeholder="{{_LANG[css]}}" type="text" name="style">
			</div>

	</div>

<script type="text/locale">
[eng]
        label	= "Label"
        name    = "Field name"
        type	= "Field type"
        default = "Default value"
        json    = "JSON data"
        css	= "CSS style"
        other	= "Other"
        plugins = "Plugins"
[rus]
        label	= "Метка"
        name    = "Имя поля"
        type	= "Тип поля"
        default = "Значение по-умолчанию"
        json    = "Данные JSON"
        css	= "Стиль CSS"
        other	= "Другие"
        plugins = "Плагины"
</script>
