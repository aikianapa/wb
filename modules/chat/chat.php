<?php
function chat__init()
{
    if (isset($_ENV["route"]["params"][0])) {
        $mode=$_ENV["route"]["params"][0];
        $call="chat__{$mode}";
        if (is_callable($call)) {
            $out=@$call();
       }
    } else {
	$out=chat__ui();
    }
    return $out;
}

function chat__ui() {
	$out=wbFromFile(__DIR__ ."/chat_ui.php",true);
        $chats=wbListFiles($_ENV["path_app"]."/chat", true);
        $dir=str_replace($_ENV["path_system"],"/engine",__DIR__);
        $dir=str_replace($_ENV["path_app"],"",$dir);
	$js=$out->find('script[src*="chat.js"]');
	if (is_object($js)) {$js->attr("src",$dir."/chat.js");}
        $Item=array("chats"=>array());
	$out->wbSetData(array("nickname"=> $_SESSION["user"]["nickname"],"room"=>"common"));
	return $out;
}

function ajax__chat()
{
	$chat = new wbApp();
	if (!isset($_SESSION["user_id"]) OR $_SESSION["user_id"]=="") return;
	$chat->wbTable("chat:c");
	$chat->wbTable("chatcache:c");
	if (isset($_POST['function'])) {$function = $_POST['function'];} else {$function="getState";}
	if (isset($_POST['room'])) {$room = $_POST['room'];} else {$room="common";}
	$log = array();
	$where = 'room = "'.$room.'"';
    switch($function) {
    	case('getState'):
		$rooms=$chat->json("chatcache")->groupBy("room")->get();
		foreach($rooms as $key => $r) {$rooms[$key]=count($r);}
		$messages=wbItemList("chatcache",$where);
		$log['state'] = count($messages);
		if ($log['state']==0) $messages=wbItemList("chat",$where);
		$log['state'] = $rooms[$room];
		$log['room'] = $room;
		$log['state_rooms'] = $rooms;
        	break;
    	case('update'):
		$rooms=$chat->json("chatcache")->groupBy("room")->get();
		$lines=$rooms[$room];
		foreach($rooms as $key => $r) {
			$rooms[$key]=array_pop($r);
			$rooms[$key]=$rooms[$key]["id"];
		}
		$tpl = wbFromString(base64_decode($_POST["tpl"]));
        	$state = $_POST['state'];
        	$count = count($lines);
		$log['state_rooms'] = $rooms;
        	if($state == $count){
        		 $log['state'] = $state;
        		 $log['text'] = false;
		} else {
        		$text= array();
        		$log['state'] = $state + count($lines) - $state;
        		$line_num=0;
			foreach ($lines as $key => $line) {
				if($line_num >= $state){
					$t = $tpl->clone();
					$t->wbSetData($line);
					$t = $t->outerHtml();
					$text[] =  $t;
				}
				$line_num++;
                        }
        		$log['text'] = $text;
		}
		break;

    	 case('send'):
		  $nickname = htmlentities(strip_tags(base64_decode($_POST['nickname'])));
		  if ($nickname>"") {
			$reg_exUrl = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";
			$message = htmlentities(strip_tags(base64_decode($_POST['message'])));
			if(($message) != "\n"){
				if (preg_match($reg_exUrl, $message, $url)) {
					$message = preg_replace($reg_exUrl, '<a href="'.$url[0].'" target="_blank">'.$url[0].'</a>', $message);
				}
			}
			wbItemSave("chatcache",array(
				"room"		=> $room,
				"uid"		=> $_SESSION["user_id"],
				"role"		=> $_SESSION["user_role"],
				"nick"		=> $nickname,
				"msg"		=> $message
			));
		 }
        	 break;

    }

    echo json_encode($log);


}


?>
