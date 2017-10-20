<?php
function ulogin__init() {
	if (isset($_ENV["route"]["params"][0])) {
		$mode=$_ENV["route"]["params"][0];
		$call="ulogin__{$mode}";
		if (is_callable($call)) {$out=@$call();}
		die;
	} else {
		$out=file_get_contents(__DIR__ ."/ulogin_form.php");
		if (isset($_POST["token"])) {
			$s=file_get_contents('http://ulogin.ru/token.php?token=' . $_POST['token'] . '&host=' . $_SERVER['HTTP_HOST']);
			$user = json_decode($s, true);
			$check = ulogin__check($user);
			if ($check!==false) {
				$check["token"]=$_POST['token'];
				wbItemSave("users",$check);
				$out="<script language='javascript'>document.location.href='/module/ulogin/login/{$check["id"]}/{$_POST['token']}';</script>";

			} else {
				$out.="<div>Авторизация {$user["network"]} не удалась!</div>";
			}
		}
		return $out;
	}
}

function ulogin__login() {
	$user_id=$_ENV["route"]["params"][1];
	$token=$_ENV["route"]["params"][2];
	$user=wbItemRead("users",$user_id);
	if ($user["token"]==$token) {
        if (!is_callable("engine__controller_login_success")) {include_once($_ENV["path_engine"]."/controllers/engine.php");}
        engine__controller_login_success($user); 
	} else {
		header('Location: /login/');
	}
	die;
}

function ulogin__check($user) {
	$res=false;
	$check=wbItemList("users",'email="'.$user["email"].'"');
	foreach($check as $u) {
		if ($u["active"]=="on") {
            $user["id"]=$u["id"];
            $u["avatar"]=ulogin__avatar($user);
            wbItemSave("users",$u);
            $res=$u;
            return $res;
        }
	}
	if ($user["verified_email"]==1) {
		$newuser=array(
			"id"			=> wbNewId(),
			"first_name"	=> $user["first_name"],
			"last_name"		=> $user["last_name"],
			"email"			=> $user["email"],
			"active"		=> "on",
			"role"			=> "user",
			"point"			=> "/"
		);
        $user["id"]=$newuser["id"];
        $newuser["avatar"]=ulogin__avatar($user);
		wbItemSave("users",$newuser);
		return $newuser;
	}
	return $res;
}

function ulogin__avatar($user) {
    $avatar="";
    if (isset($user["photo_big"]) AND $user["photo_big"]>"") {$avatar=$user["photo_big"];} 
    elseif  (isset($user["photo"]) AND $user["photo"]>"") {$avatar=$user["photo"];} 
    else {$avatar="";}
    if ($avatar>"") {
        $name=explode("/",$avatar); $name=strtolower($name[count($name)-1]);
        $path= wbNormalizePath("{$_ENV["path_app"]}/uploads/users/{$user["id"]}/{$name}");
        if (wbPutContents($path,file_get_contents($avatar))) {
            $avatar="[".json_encode(array("img"=>$name,"title"=>"","visible"=>0))."]";
        }
    }
    return $avatar;
}

?>
