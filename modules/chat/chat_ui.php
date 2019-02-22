<div class="modal chat-box" id="ChatBox" tabindex="-1" role="dialog" data-nickname="{{nickname}}" data-uid="{{_SESS[user_id]}}">
	<div class="modal-dialog" role="document">
	<div class="modal-content">
		<div class="modal-header">
			<h6 class="modal-title"><i class="fa fa-user"></i> <span id="nickname"></span></h6>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<div class="modal-body">
			<div id="chat-wrap"><div id="chat-area"></div></div>
		</div>
		<div class="modal-footer">
			<textarea id="sendie" rows="2" maxlength = '2048' placeholder="{{_LANG[type]}}"></textarea>
			<button type="button" class="btn btn-primary chat-send"> {{_LANG[send]}}</button>
		</div>
	</div>
	</div>
	<script type="text/template" id="ChatMsgTpl">
		<p data-id="{{id}}" data-uid="{{uid}}"><span><i class="fa fa-user"></i> {{nick}}</span>
		<small class="pull-right">
			<i class="fa fa-clock-o"></i>
			{{date('d.m.y H:i',strtotime("{{_created}}"))}}
		</small>
		<br>{{msg}}</p>
	</script>
</div>
<div data-wb-prepend=".kt-headpanel-right" data-wb-hide="*">
	<a class="nav-link pd-x-7 pos-relative" data-toggle="modal" data-target="#ChatBox" data-room="common">
	<i class="icon ion-chatboxes tx-24"></i>
	<span class="notify square-8 bg-danger pos-absolute t-15 r-0 rounded-circle hidden"></span>
	</a>
</div>


<script data-wb-append="body">
$(document).on("wbapp",function() {
    wb_include("/engine/modules/chat/chat.js");
    wb_include("/engine/modules/chat/chat.css");

});
$(document).on("wb_include",function(event,data) {
    if (data.url=="/engine/modules/chat/chat.js") {
        //$("#ChatBox").modal("show");
        $(document).trigger("chat_start");
    }
});
</script>

<script type="text/locale">
[eng]
exit            = "Exit"
chat	        = "Chat"
send		= "Send"
type		= "Type your message"
[rus]
exit            = "Выйти"
chat 	        = "Чат"
send		= "Отправить"
type		= "Напишите ваше сообщение"
</script>
