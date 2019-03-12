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
        $dRoom="common"; if (isset($_ENV["settings"]["chatmod"]["chatname"])) {$lasts=$_ENV["settings"]["chatmod"]["chatname"];}
	$out->wbSetData(array("nickname"=> $_SESSION["user"]["nickname"],"room"=>$dRoom));
	return $out;
}

function ajax__chat()
{
	$chat = new wbApp();
	if (!isset($_SESSION["user_id"]) OR $_SESSION["user_id"]=="") return;
	$user = $_SESSION["user_id"];
	$chat->wbTable("chat:c");
	$chat->wbTable("chatcache:c");
	if (isset($_POST['function'])) {$function = $_POST['function'];} else {$function="getState";}
	$dRoom="common"; if (isset($_ENV["settings"]["chatmod"]["chatname"])) {$lasts=$_ENV["settings"]["chatmod"]["chatname"];}
	if (isset($_POST['room'])) {$room = $_POST['room'];} else {$room=$dRoom;}
	$log = array();
	$where = 'room = "'.$room.'"';
	$from=date("Y-m-d H:i:s",strtotime("now - 1 month"));
	$tpl = wbFromFile(__DIR__ . "/chat_ui.php");
	$tpl = wbFromString($tpl->find("#ChatBox #ChatMsgTpl",0)->html());
    switch($function) {
    	case('getState'):
		$lasts = 15; if (isset($_ENV["settings"]["chatmod"]["last"])) {$lasts=$_ENV["settings"]["chatmod"]["last"];}
		$rooms1=$chat->json("chatcache")->get();
		$rooms2=$chat->json("chat")->get();
		$messages=array_merge($rooms1,$rooms2);
		$rooms=$chat->json($messages)
			->where("_created",">",$from)
			->groupBy("room")
			->get();
		foreach($rooms as $key => $r) {
			$data=wbArraySort(array_slice($r,-$lasts),"_created:a");
			$rooms[$key]="";
			foreach($data as $item) {
				$t = $tpl->clone();
				$t->wbSetData($item);
				$rooms[$key].=$t->outerHtml();
			}
		}
		$log['state'] = $rooms;
		$log=base64_encode(json_encode($log));
        	break;
    	case('update'):
		$state = $_POST['state'];
		$rooms=array_keys($state);
		$messages=$chat->json("chatcache")->where("room","in",$rooms)->groupBy("room")->get();
		$log = array("state"=>array());
		foreach($messages as $room => $msgs) {
			$last=$chat->json($msgs)->where("id","=",$state[$room])->get();
			$last=array_pop($last);
			$news=$chat->json($msgs)
			->where("_created",'>=',$last["_created"])
			->where("id",'!==',$last["id"])
			->get();
			$last=$news;
			$last=array_pop($last);
			$text= "";
			if (count($news)) {
				foreach ($news as $key => $line) {
					$t = $tpl->clone();
					$t->wbSetData($line);
					$text .= $t->outerHtml();
				}
				$log["state"][$room]["text"]=$text;
				$log["state"][$room]["last_id"]=$last["id"];
			} else {
				unset($log["state"][$room]);
			}
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
			$id = explode(" ",microtime());
			$id = dechex($id[1].substr($id[0],2));
			$id = wbItemSave("chatcache",array(
				"id"		=> $id,
				"room"		=> $room,
				"uid"		=> $_SESSION["user_id"],
				"role"		=> $_SESSION["user_role"],
				"nick"		=> $nickname,
				"msg"		=> $message
			));
			$line=wbItemRead("chatcache",$id);
			$t = $tpl->clone();
			$t->wbSetData($line);
			$log["text"]=$t->outerHtml();
			$log=base64_encode(json_encode($log));
		 }
        	 break;

    }

    echo json_encode($log);
}

function chat__settings() {
    if (wbRole("admin")) {
        $form=wbFromFile(__DIR__."/chat_settings.php");
        $form->wbSetData($_ENV["settings"]);  // проставляем значения
        return $form->outerHtml();
    }
}

function chat__arj() {

}


?>
