<div data-wb-role="include" src="modal" data-wb-id="{{_ENV[route][name]}}Settings" data-wb-hide="*"></div>
<span data-wb-html="#{{_ENV[route][name]}}Settings .modal-title">Chat {{_LANG.settings}}</span>
<button class="btn btn-primary" data-wb-formsave="#ChatBox_settings" data-wb-html="#{{_ENV[route][name]}}Settings .modal-footer">{{_LANG.ready}}</button>
<div data-wb-html="#{{_ENV[route][name]}}Settings .modal-body">
	<form method="post" id="ChatBox_settings" data-wb-form="admin" data-wb-item="settings" data-wb-add="false" data-wb-allow="admin">
    <div class="row form-group">
        <label class="col-sm-5 control-label" title="{{_LANG[startview]}}">{{_LANG[startview]}}</label>
        <div class="col-sm-7">
		<label class="switch switch-sm switch-success">
		<input type="checkbox" name="chatmod[startview]" value="{{_LANG[startview]}}">
		<span></span>
        </div>
    </div>

    <div class="row form-group">
        <label class="col-sm-5 control-label">{{_LANG[chatname]}}</label>
        <div class="col-sm-7"><input type="text" name="chatmod[chatname]" class="form-control" placeholder="{{_LANG[chatname]}}" required></div>
    </div>

    <div class="row form-group">
        <label class="col-sm-5 control-label" title="{{_LANG[last]}}">{{_LANG[last]}}</label>
        <div class="col-sm-7"><input type="number" name="chatmod[last]" class="form-control" placeholder="{{_LANG[last]}}" min="0" max="1000" required></div>
    </div>

    <div class="row form-group">
        <label class="col-sm-5 control-label" title="{{_LANG[timerH]}}">{{_LANG[timerH]}}</label>
        <div class="col-sm-7"><input type="number" name="chatmod[timerh]" class="form-control" placeholder="{{_LANG[timerH]}}" min="1" max="60" required></div>
    </div>

    <div class="row form-group">
        <label class="col-sm-5 control-label" title="{{_LANG[timerV]}}">{{_LANG[timerV]}}</label>
        <div class="col-sm-7"><input type="number" name="chatmod[timerv]" class="form-control" placeholder="{{_LANG[timerV]}}" min="1" max="60" required></div>
    </div>

<hr>
    <div class="row form-group">
        <label class="col-sm-5 control-label" title="{{_LANG[notify]}}">{{_LANG[notify]}}</label>
        <div class="col-sm-2">
            <label class="switch switch-success"><input type="checkbox" name="chatmod[notify]"><span></span></label>
        </div>
    </div>

    <div class="row form-group">
        <label class="col-sm-5 control-label">{{_LANG[delay]}}</label>
        <div class="col-sm-7"><input type="text" name="chatmod[delay]" class="form-control" placeholder="{{_LANG[delay]}}"  min="1" max="60" required></div>
    </div>

    <div class="row form-group">
        <label class="col-sm-5 control-label">{{_LANG[color]}}</label>
        <div class="col-sm-7">
		<select class="form-control" name="chatmod[color]" value="{{chatmod[color]}}" required>
			<option value="primary">Primary</option>
			<option value="warning">Warning</option>
			<option value="danger">Danger</option>
			<option value="info">Info</option>
			<option value="default">Default</option>
		</select>
	</div>
    </div>
	</form>
</div>
<script>
	$("#ChatBox_settings").on("wb_form_saved",function(){
		$("#{{_ENV[route][name]}}Settings").modal("hide");
	});
</script>
<script type="text/locale">
[eng]
settings	= "Settings"
ready	        = "Ready"
last		= "On start messages count"
timerH		= "Hidden chat window timer (sec)"
timerV		= "Visible chat window timer (sec)"
chatname	= "Default chat name"
startview	= "Show chat window on start"
notify		= "Show notify baloons"
delay		= "Hide notify delay (sec)"
color		= "Notify baloons color"
[rus]
settings	= "Настройки"
ready	        = "Готово"
last		= "Количество сообщений при запуске"
timerH		= "Таймер скрытого окна (сек)"
timerV		= "Таймер открытого окна (сек)"
chatname	= "Имя чата по-умолчанию"
startview	= "Показывать окно чата при запуске"
notify		= "Показывать всплывающие подсказки"
delay		= "Задержка скрытия всплывающих подсказок (сек)"
color		= "Цвет всплывающих подсказок"
</script>
