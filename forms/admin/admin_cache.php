<div class="col-12">
	<div class="row" style="padding-left: 30px; padding-right:60px;">
		<div class="col-sm-3">
			<label>{{_LANG[controller]}}</label>
		</div>
		<div class="col-sm-2">
			<label>{{_LANG[form]}}</label>
		</div>
		<div class="col-sm-2">
			<label>{{_LANG[mode]}}</label>
		</div>
		<div class="col-sm-2">
			<label>{{_LANG[lifetime]}}</label>
		</div>
		<div class="col-sm-2">
			<label>{{_LANG[active]}}</label>
		</div>
	</div>
	<div data-wb-role="multiinput" name="cache">
		<div class="col-sm-3">
			<input class="form-control" name="controller" placeholder="{{_LANG[controller]}}">
		</div>
		<div class="col-sm-2">
			<input class="form-control" name="form" placeholder="{{_LANG[form]}}">
		</div>
		<div class="col-sm-2">
			<input class="form-control" name="mode" placeholder="{{_LANG[mode]}}">
		</div>
		<div class="col-sm-2">
			<input class="form-control" min="1" type="number" name="lifetime" placeholder="{{_LANG[lifetime]}}">
		</div>
		<div class="col-sm-2">
			<label class="switch switch-sm switch-success">
				<input type="checkbox" name="active" value="">
				<span></span>
			</label>
		</div>
	</div>
</div>
<script type="text/locale">
[rus]
        controller	= Контроллер
	form		= Форма
        mode		= Режим
        lifetime        = Время жизни сек.
        active          = Активто
[eng]
        controller	= Controller
	form		= Form name
        mode		= Mode
        lifetime        = Lifetime sec.
        active          = Active
</script>
