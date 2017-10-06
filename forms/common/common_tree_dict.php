	<div class="col-sm-12" data-wb-role="multiinput" name="fields">
		<div class="row">
			<div class="col-sm-3 col-xs-12">
				<input class="form-control" placeholder="Имя поля" type="text" name="name">
			</div>

			<div class="col-sm-4 col-xs-12">
				<input class="form-control" placeholder="Метка" type="text" name="label">
			</div>
			<div class="col-sm-3 col-xs-12">
					<select class="form-control" name="type" placeholder="Тип поля">
					<option value="string">string</option>
					<option value="text">text</option>
					<option value="number">number</option>
					<option value="checkbox">checkbox</option>
					<option disabled>--== Плагины ==--</option>
					<option value="editor">editor</option>
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
					<option disabled>--== Другие ==--</option>
					<option value="date">date</option>
					<option value="week">week</option>
					<option value="month">month</option>
					<option value="year">year</option>
					<option value="time">time</option>
					<option value="color">color</option>
					</select>
			</div>
			<div class="col-sm-2 col-xs-12">
				<input class="form-control" placeholder="Значение по-умолчанию" type="text" name="value">
			</div>
			<div class="col-sm-3 hidden-xs">&nbsp;</div>
			<div class="col-sm-4 col-xs-12">
				<input class="form-control" placeholder="Свойства JSON" type="text" name="prop">
			</div>
			<div class="col-sm-5 col-xs-12">
				<input class="form-control" placeholder="Стиль CSS" type="text" name="style">
			</div>
		</div>
	</div>
