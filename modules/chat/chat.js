/*
Created by: Kenrick Beckett
Modified by: Oleg Frolov
Name: Chat Engine
*/



$(document).on("chat_start",function() {

	var instanse = false;
	var state = 0;
	var state_rooms = [];
	var mes;
	var room = "common";
	var name = $("#ChatBox").attr("data-nickname");
	var uid = $("#ChatBox").attr("data-uid");
	console.log(uid);
	var msgt = base64_encode($("#ChatBox #ChatMsgTpl").html());


	$("#ChatBox #ChatMsgTpl").remove();

    // strip tags
    // display name on page
    $("#ChatBox #nickname").html(name);

    // kick off chat
	var chat =  new Chat();
	//chat.getState();
	chat.update();
	var timer = chat.setTimer(1000);

	$('#ChatBox').on('show.bs.modal', function (e) {
		timer = chat.setTimer(0);
		var newroom=$(e.relatedTarget).attr("data-room");
		if (newroom==undefined || newroom=="") newroom="common";
		if (newroom!==room) {
			room=newroom;
			state = 0;
			$("#ChatBox #chat-area").html("");
			chat.update();
			//chat.getState();
		}
		$("[data-toggle=modal][data-target='#ChatBox'][data-room='"+room+"'] .notify").addClass("hidden");
		timer = chat.setTimer(1000);
	});

	$('#ChatBox').on('hidden.bs.modal', function (e) {
		timer = chat.setTimer(0);
		timer = chat.setTimer(30000);
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
}

function setTimer(timeout) {
	if (timeout==undefined) timeout=1000;
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
    if(!instanse) {
        instanse = true;
        $.ajax({
type: "POST",
url: "/ajax/chat/",
data: {
'function': 'getState',
'room': room
            },
dataType: "json",

success: function(data) {
                state = data.state;
                instanse = false;
            },
        });
    }
}

//Updates the chat
function updateChat() {
    if(!instanse) {
        instanse = true;
        $.ajax({
type: "POST",
url: "/ajax/chat/",
data: {
'function': 'update',
'state': state,
'room': room,
'tpl': msgt
            },
dataType: "json",
success: function(data) {
                if(data.text) {
                    for (var i = 0; i < data.text.length; i++) {
                        $('#ChatBox #chat-area').append($( data.text[i] ));
                    }
                    $('#ChatBox .modal-body').scrollTop( $("#ChatBox #chat-wrap").height() );
                }
                document.getElementById('chat-area').scrollTop = document.getElementById('chat-area').scrollHeight;
                instanse = false;
                state = data.state;
                chat.notify(data);
            },
        });
    }
    else {
        setTimeout(updateChat, 1500);
    }
}

function chatNotify(data) {
	var notify = false;
	$.each(data.state_rooms,function(r,c){
		if (state_rooms[r] == undefined) { state_rooms[r] = getcookie("#ChatBox_"+r);}
		if (state_rooms[r] == undefined || state_rooms[r] == "") {state_rooms[r]=c; setcookie("#ChatBox_"+r,c);}
		if (state_rooms[r] !== c) {
			notify = true;
			state_rooms[r]=c;
			if (!$("#ChatBox").is(":visible") || r !== room) {
				$("[data-toggle=modal][data-target='#ChatBox'][data-room='"+r+"'] .notify").removeClass("hidden");
				$("#ChatBox").trigger("chat-notify",r);
			} else {
				setcookie("#ChatBox_"+r,c);
			}
		}
	});
	if (notify == true && $("#ChatBox #chat-area p:last").attr("data-uid") !== uid) chat.playSound();
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
'room': room
        },
dataType: "json",
success: function(data) {
            updateChat();
        },
    });
}


});

