$(document).on("chat_start",function() {
	var vTimer = 2000; 	if (wbapp.settings.chatmod.timerv !== undefined ) vTimer=wbapp.settings.chatmod.timerv*1000;
	var hTimer = 10000;	if (wbapp.settings.chatmod.timerh !== undefined ) hTimer=wbapp.settings.chatmod.timerh*1000;
	var dRoom = "common";	if (wbapp.settings.chatmod.chatname !== undefined ) dRoom=wbapp.settings.chatmod.chatname;
	var baloons = true;	if (wbapp.settings.chatmod.notify !== undefined && wbapp.settings.chatmod.notify == "") baloons=false;
	var bColor = "warning"; if (wbapp.settings.chatmod.color !== undefined) bColor=wbapp.settings.chatmod.color;
	var bDelay = 10000; 	if (wbapp.settings.chatmod.delay !== undefined) bDelay=wbapp.settings.chatmod.delay*1000;
	var show = false; 	if (wbapp.settings.chatmod.startview !== undefined && wbapp.settings.chatmod.startview == "on") show=true;
	var state = 0;
	var state_rooms = [];
	var mes;
	var name = $("#ChatBox").attr("data-nickname");
	var uid = $("#ChatBox").attr("data-uid");
	var notify ;


	$("#ChatBox #ChatMsgTpl").remove();

    // strip tags
    // display name on page
    $("#ChatBox #nickname").html(name);

    // kick off chat
	var chat =  new Chat();
	chat.getState();
	var timer = chat.setTimer(hTimer);

	$('#ChatBox').on('show.bs.modal', function (e) {
		timer = chat.setTimer(0);
		var newroom=$(e.relatedTarget).attr("data-room");
		if (newroom==undefined || newroom=="") newroom=dRoom;
		if (newroom!==chat.room) {
			chat.room=newroom;
			$('#ChatBox').attr("data-room",chat.room);
			$("#ChatBox #chat-area").html("");
			chat.showText(chat.texts[chat.room]);
		}
		$("[data-target='#ChatBox'][data-room='"+chat.room+"'] .notify").addClass("hidden");
		timer = chat.setTimer(vTimer);
		chat.notifies[chat.room] = "no";
		chat.toBottom();
	});

	$('#ChatBox').on('hidden.bs.modal', function (e) {
		timer = chat.setTimer(0);
		timer = chat.setTimer(hTimer);
		chat.room = null;
	});

	$(document).on("wb_ajax_done",function(){
		chat.notify();
	});

    // watch textarea for key presses
    $("#ChatBox #sendie").keydown(function(event) {
        var key = event.which;

        //all keys including return.
        if (key >= 33) {

            var maxLength = $(this).attr("maxlength");
            var length = this.value.length;

            // don't allow new content if length is maxed out
            if (length >= maxLength) {
                event.preventDefault();
            }
        }
    });
    // watch textarea for release of key press
    $('#ChatBox #sendie').keyup(function(e) {
        if (e.keyCode == 13) send();
    });

    $('#ChatBox .chat-send').click(function(){
	send();
    });

    function send() {
            var text = $('#ChatBox #sendie').val();
            var maxLength = $('#ChatBox #sendie').attr("maxlength");
            var len = text.length;
		if (len <= 1) {
			$('#ChatBox #sendie').val("");
			return;
		}
            // send
            if (len <= maxLength + 1) {
                chat.send(text, name);
                $('#ChatBox #sendie').val("");

            } else {
                $('#ChatBox #sendie').val(text.substring(0, maxLength));
            }
    }


function Chat () {
	this.update = updateChat;
	this.send = sendChat;
	this.getState = getStateOfChat;
	this.setTimer = setTimer;
	this.notify = chatNotify;
	this.playSound = playSound;
	this.stopSound = stopSound;
	this.addText = addText;
	this.showText = showText;
	this.toBottom = toBottom;
	this.room = null; // current room
	this.lasts = lasts; // функция получает/обновляет список последних id сообщений чатов
	this.texts = {}; // все сообщения чатов
	this.rooms = {}; // список последних id сообщений чатов
	this.notifies = {};
}

function addText(text,chatRoom) {
	var last = $("<div>"+text+"</div>").find("p[data-id]:last-child").attr("data-id");
	if (chat.texts[chatRoom] == undefined) chat.texts[chatRoom] = "";
	chat.rooms[chatRoom]=last;
	chat.texts[chatRoom]+=text;
	if (chat.room == chatRoom) {
		chat.showText(text);
		setcookie("#ChatBox_"+chatRoom,last);
	}

	if (getcookie("#ChatBox_"+chatRoom) !== last) {
		if (chat.notifies[chatRoom] !== "wait") chat.notifies[chatRoom] = "yes";
	} else {
		if (chat.notifies[chatRoom] !== "wait") chat.notifies[chatRoom] = "no";
	}
	chat.notify();
}

function showText(text) {
	$('#ChatBox #chat-area').append(text);
	chat.toBottom();
}

function toBottom() {
	setTimeout(function(){
		$('#ChatBox .modal-body').scrollTop(0);
	setTimeout(function(){
		$('#ChatBox .modal-body').scrollTop($('#ChatBox #chat-area').height());
	},100);
	},100);

}


function setTimer(timeout) {
	if (timeout==undefined) var timeout=vTimer;
	if (timeout==0) {
		console.log("Stop chat timer");
		clearInterval(timer);
	} else {
		console.log("Set chat timer to: "+timeout);
		timer = setInterval(function() {chat.update();},timeout);
	}
	return timer;
}

//gets the state of the chat
function getStateOfChat() {
        $.ajax({
		type: "POST",
		url: "/ajax/chat/",
		data: {'function': 'getState'},
		dataType: "json",
		success: function(data) {
				data = $.parseJSON(base64_decode(data));
				state = data.state;
				var last;
				$.each(state,function(ro,item){
					chat.addText(item,ro);
				});
				if (show == true) {$('#ChatBox').modal("show");} else {$('#ChatBox').modal("hide");}
			}
        });
}

function lasts() {
	var res = {};
	$.each(chat.rooms,function(ro,item){
		res[ro]=item;
	});
	return res;
}

//Updates the chat
function updateChat() {
	$.ajax({
		type: "POST",
		url: "/ajax/chat/",
		data: {
		'function': 'update',
		'state': chat.lasts(),
			    },
		dataType: "json",
		success: function(data) {
				$.each(data.state,function(ro,item){
					if (item.text !== null) {
						chat.addText(item.text,ro);
					}
				});
			    },
	});
}

function chatNotify() {
	var notify = false;
	var i = 0;
	$.each(chat.notifies,function(ro,no){
		if (no == "yes") {
			notify = true;
			$("#ChatBox").trigger("chat-notify",ro);
			if (chat.room !== ro) {
				chat.notifies[ro] = "wait";
				if ($.bootstrapGrowl) {
					var baloon = $("#ChatBox #ChatBaloon").html();
					baloon=str_replace("{room}",ro,baloon);
					$.bootstrapGrowl(baloon, {
						ele: 'body',
						type: bColor,
						offset: {
						from: 'bottom',
						amount: 20
					},
					align: 'left',
					width: "auto",
					delay: bDelay + i*200,
					allow_dismiss: true,
					stackup_spacing: 10
					});
					i++;
				}

			}
		}
		if (no !== "no" && chat.room !== ro) {
			$("[data-target='#ChatBox'][data-room='"+ro+"'] .notify").removeClass("hidden");
		}
	});
	if (notify == true) chat.playSound();
}

function playSound(file) {
	if (file == undefined) {var file="/engine/modules/chat/chat.mp3";}
	chat.stopSound();
	$('<audio class="chat-notify" autoplay="autoplay" style="display:none;">'
	+ '<source src="' + file + '" />'
	+ '<embed src="' + file + '" hidden="true" autostart="true" loop="false"/>'
	+ '</audio>'
	).appendTo('#ChatBox');
}

function stopSound() {
            $("#ChatBox .chat-notify").remove();
}

//send the message
function sendChat(message, nickname)
{
	if (message==undefined || message=="") return;
	updateChat();
    $.ajax({
	type: "POST",
	url: "/ajax/chat/",
	data: {
	'function': 'send',
	'message': base64_encode(message),
	'nickname': base64_encode(nickname),
	'room': chat.room
		},
	dataType: "json",
	success: function(data) {
			data = $.parseJSON(base64_decode(data));
			chat.addText(data.text,chat.room);
		},
    });
}


});

