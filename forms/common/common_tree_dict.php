	<div class="col-12" data-wb-role="multiinput" name="fields">
			<div class="col-4">
				<div class="form-group mb-0">
					<div class="input-group">
						<span class="input-group-addon wb-tree-dict-prop-btn"><i class="fa fa-gear"></i></span>
						<input class="form-control" placeholder="{{_LANG[name]}}" type="text" name="name">
					</div>
				</div>
			</div>
			<div class="col-4">
				<div class="form-group mb-0">
					<div class="input-group">
						<span class="input-group-addon wb-tree-dict-lang-btn"><i class="fa fa-language"></i></span>
						<input class="form-control" placeholder="{{_LANG[label]}}" type="text" name="label">
					</div>
				</div>
			</div>
			<div class="col-4">
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
					<input type="hidden" name="prop" value="">
					<input type="hidden" name="lang" value="">
			</div>
	</div>

<div class="modal tree-edit-fld" id="{{_form}}EditDictFld" data-keyboard="true" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header pb-0 pt-1">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">×</span> </button>
				<h5 class="modal-title"></h5>
			</div>
			<div class="modal-body">
				<form class="form-horizontal" role="form"></form>
			</div>
		</div>
	</div>
</div>
<div class="modal tree-edit-fld" id="{{_form}}EditDictFldLang" data-keyboard="true" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header pb-0 pt-1">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">×</span> </button>
				<h5 class="modal-title"></h5>
			</div>
			<div class="modal-body">
				<form class="form-horizontal" role="form"></form>
			</div>
		</div>
	</div>
</div>


	<script type="text/template" class="wb-prop-lang">
		<form>
		<div class="form-group row" data-type-allow="multiinput">
			<label class="col-12 form-control-label">{{_LANG[locales]}}</label>
			<div class="col-12" data-wb-role="multiinput" name="labels">
				<div class="col-sm-3">
				<select class="form-control" placeholder="{{_LANG[lang]}}" name="id" data-wb-role="foreach" data-wb-from="_ENV[locales]">
					<option value="{{id}}">{{_locale}}</option>
				</select>
				</div>
				<div class="col-sm-9">
				<input class="form-control" placeholder="{{_LANG[name]}}" type="text" name="name">
				</div>
			</div>
		</div>
		</form>
	</script>

	<script type="text/template" class="wb-prop-fields">
		<form>
		<div class="form-group row" data-type-disallow="multiinput">
			<label class="col-sm-3 form-control-label">{{_LANG[default]}}</label>
			<div class="col-sm-9"><input class="form-control" placeholder="{{_LANG[default]}}" type="text" name="value"></div>
		</div>
		<div class="form-group row" data-type-allow="forms">
			<label class="col-sm-3 form-control-label">{{_LANG[form]}}</label>
			<div class="col-sm-9">
				<select class="form-control" name="form" placeholder="{{_LANG[form]}}" data-wb-role="foreach" data-wb-from="_ENV[forms]" data-wb-tpl="false">
					<option value="{{0}}">{{0}}</option>
				</select>
			</div>
		</div>

		<div class="form-group row" data-type-allow="multiinput">
			<label class="col-12 form-control-label">{{_LANG[fldset]}}</label>
			<div class="col-12" data-wb-role="multiinput" name="multiflds">
				<div class="col-sm-2">
				<input class="form-control" placeholder="{{_LANG[name]}}" type="text" name="name">
				</div>
				<div class="col-sm-3">
				<input class="form-control" placeholder="{{_LANG[label]}}" type="text" name="label">
				</div>
				<div class="col-sm-2">
				<select class="form-control" placeholder="{{_LANG[type]}}" type="text" name="type">
					<option value="string">string</option>
					<option value="text">text</option>
					<option value="number">number</option>
					<option value="checkbox">checkbox</option>
					<option disabled>--== {{_LANG[plugins]}} ==--</option>
					<option value="switch">switch</option>
					<option value="enum">enum</option>
					<option value="tree">tree</option>
					<option value="tags">tags</option>
					<option value="phone">phone</option>
					<option value="mask">mask</option>
					<option value="datepicker">datepicker</option>
					<option value="datetimepicker">datetimepicker</option>
					<!--
					<option value="forms">forms</option>
					<option value="editor">editor</option>
					<option value="source">source</option>
					<option value="gallery">gallery</option>
					<option value="image">image</option>
					<option value="multiinput">multiinput</option>
					<option value="snippet">snippet</option>
					<option value="module">module</option>
					-->

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
				<div class="col-sm-3">
				<input class="form-control" placeholder="{{_LANG[default]}}" type="text" name="value">
				</div>
				<div class="col-sm-2">
				<input class="form-control" placeholder="{{_LANG[class]}}" type="text" name="class">
				</div>
			</div>
		</div>

		<div class="form-group row" data-type-allow="enum">
			<label class="col-sm-3 form-control-label">{{_LANG[enum]}}</label>
			<div class="col-sm-9">
				<input class="form-control input-tags" placeholder="{{_LANG[enum]}}" type="text" name="enum">
			</div>
		</div>

		<div class="form-group row" data-type-allow="forms">
			<label class="col-sm-3 form-control-label">{{_LANG[mode]}}</label>
			<div class="col-sm-9">
				<input class="form-control" placeholder="{{_LANG[mode]}}" type="text" name="mode">
			</div>
		</div>
		<div class="form-group row" data-type-allow="forms">
			<label class="col-sm-3 form-control-label">{{_LANG[selector]}}</label>
			<div class="col-sm-9">
				<input class="form-control" placeholder="{{_LANG[selector]}}" type="text" name="selector">
			</div>
		</div>
		<div class="form-group row" data-type-disallow="forms">
			<label class="col-sm-3 form-control-label">{{_LANG[json]}}</label>
			<div class="col-sm-9"><input class="form-control" placeholder="{{_LANG[json]}}" type="text" name="json"></div>
		</div>
		<div class="form-group row">
			<label class="col-sm-3 form-control-label">{{_LANG[class]}}</label>
			<div class="col-sm-9"><input class="form-control" placeholder="{{_LANG[class]}}" type="text" name="class"></div>
		</div>
		<div class="form-group row">
			<label class="col-sm-3 form-control-label">{{_LANG[css]}}</label>
			<div class="col-sm-9"><input class="form-control" placeholder="{{_LANG[css]}}" type="text" name="style"></div>
		</div>
		</form>
<script type="text/locale">
	[eng]
	fldset  = "Multiinput Fields set"
	enum 	= "Enum"
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
	selector= "Selector"
	locales = "Locales"
	lang 	= "Lang"
	prop	= "Properties"
[rus]
	fldset  = "Набор полей для мультиввода"
	enum 	= "Перечисления"
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
	selector= "Селектор"
	locales = "Локализации"
	lang 	= "Язык"
	prop	= "Свойства"
</script>


	</script>

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
        selector= "Selector"
        locales = "Locales"
        prop	= "Properties"
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
        selector= "Селектор"
        locales = "Локализации"
        prop	= "Свойства"
</script>
