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
				$out="<script language='javascript'>document.location.href='/module/ulogin/login/{$check["id"]}/{$_POST['token']}';</script>";
				$check["token"]=$_POST['token'];
				wbItemSave("users",$check);
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
		$_SESSION["user_id"]=$user["id"];
		$_SESSION["user_role"]=$user["role"];
		if (!isset($user["name"])) {$user["name"]=$user["id"];}
		$_SESSION["user"]=$user;
		header('Location: '.$user["point"]);
	} else {
		header('Location: /login/');
	}
	die;
}

function ulogin__check($user) {
	$res=false;
	$check=wbItemList("users",'email="'.$user["email"].'"');
	foreach($check as $u) {
		if ($u["active"]=="on") {$res=$u; return $res;}
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
		wbItemSave("users",$newuser);
		return $newuser;
	}
	return $res;
}
?>
