<?php
function engine__controller()
{
    wbTrigger("func", __FUNCTION__, "before");
    $call=__FUNCTION__ ."_".$_ENV["route"]["mode"];
    if (is_callable($call)) {
        $_ENV["DOM"]=$call();
    } else {
        echo __FUNCTION__ .": {$_ENV['sysmsg']['err_func_lost']} ".$call."()";
        die;
    }
    wbTrigger("func", __FUNCTION__, "after");
    return $_ENV["DOM"];
}

function engine__controller_login()
{
	if (is_callable("engine_controller_login")) return engine_controller_login();
    //$user=array("id"=>"admin","password"=>md5("admin"),"role"=>"admin","point"=>"/admin/","active"=>"on");
    //wbItemSave("users",$user);
    if (is_callable("wbUserLogin")) {
		return wbUserLogin();
    } else {
	if (isset($_POST["recovery"]) AND $_POST["recovery"]=="password") {__engineRecoveryPassword();}
	if (isset($_POST["l"]) AND $_POST["l"]>"") {
		if (strpos($_POST["l"],"@")) {
		    $users=wbItemList("users",'email="'.$_POST["l"].'"');
		    foreach($users as $key => $item) { $_POST["l"]=$item["id"]; break;}
		}
		if ($user=wbItemRead("users", $_POST["l"])) {
			if (isset($user["lang"]) AND $user["lang"]>"") {
				$_SESSION["lang"]=$_ENV["lang"]=$user["lang"];
			}

		    if ($user["password"]==md5($_POST["p"]) AND $user["active"]=="on") {
			wbLog("func",__FUNCTION__,100,$_POST);
			engine__controller_login_success($user);
		    } else {
			wbTrigger("func",__FUNCTION__,"false",func_get_args(),$user);
			wbLog("func",__FUNCTION__,101,$_POST);
		    }
		}
	}
	$_ENV["DOM"]=wbGetTpl("login.htm");
	return $_ENV["DOM"];
    }
}

function engine__controller_login_success($user) {
		if (is_callable("engine_controller_success")) return engine_controller_success($user);
        $_SESSION["user_id"]=$user["id"];
        $_SESSION["user_role"]=$user["role"];
        if (isset($user["isgroup"]) AND $user["isgroup"]=="on") {
                $user["group_".$user["role"]]=$user;
        } else {
                $group=wbItemRead("users",$user["role"]);
                $user["group_".$user["role"]]=$group;
        }
        if (isset($user["point"]) AND $user["point"]>"") {
            $point=$user["point"];
        } else {
            if ($user["role"]=="admin") {
                $point="/admin";
            } else {
                $point="/";
            }
        }
        if (isset($user["group_".$user["role"]]["login_url"]) AND $user["group_".$user["role"]]["login_url"]>"") {
                $point=$user["group_".$user["role"]]["login_url"];
        }
        if (!isset($user["name"])) $user["name"]=$user["id"];

        if (isset($user["avatar"])) {
            if (!is_array($user["avatar"])) $user["avatar"]=json_decode($user["avatar"], true);
            $user["avatar"]=$user["avatar"][0];
            $user["avatar"]="/uploads/users/{$user["id"]}/{$user["avatar"]["img"]}";
        } else {
            $user["avatar"]="/engine/tpl/img/person.svg";
        }

        if (isset($user["group_".$user["role"]]["logout_url"]) AND $user["group_".$user["role"]]["logout_url"]>"") {
                $logout=$user["group_".$user["role"]]["logout_url"];
        } else {
                $logout="/";
        }
        $_SESSION["user_name"]=$user["name"];
        $_SESSION["user_avatar"]=$user["avatar"];
        $_SESSION["user_role"]=$user["role"];
        $_SESSION["user_logout_url"]=$logout;
        $_SESSION["user_login_url"]=$point;
        $_SESSION["user_lang"]=$user["lang"];
        $_SESSION["user"]=$user;
        wbTrigger("func","wbLogin","success");
        header('Location: '.$point);
        die;
}

function engine__controller_signup()
{
	if (is_callable("engine_controller_signup")) return engine_controller_signup();
    $res=false;
    $out=wbGetTpl("signup.htm");
    $exist=false;
    if (isset($_POST["email"])) {
        $users=wbItemList("users",'email="'.$_POST["email"].'"');
        foreach($users as $key => $item) { $exist=true; break; }
    }


    if (isset($_ENV["route"]["active"]) AND $_ENV["route"]["active"]>"") {
        $user=wbItemRead("users",$_ENV["route"]["active"]);
        if (isset($user["email"]) AND isset($user["password"])) {
            $user["active"]="on";
            wbItemSave("users",$user);
            $out->find(".signpanel-wrapper")->html($out->find(".signbox.success"));
            $out->find(".signbox.success")->removeClass("d-none");
            wbTrigger("func","wbSignup","success",null,$user);
        }

    } elseif ($exist==true) {
            $out->find(".signpanel-wrapper")->html($out->find(".signbox.exist"));
            $out->find(".signbox.exist")->removeClass("d-none");

    } elseif (isset($_POST["password"]) AND isset($_POST["password_check"])) {
        if ($_POST["password"]==$_POST["password_check"] ) {
		unset($_POST["password_check"]);
            $nick=$_POST["first_name"]." ".$_POST["last_name"];
            $user=array(
                 "id"               => wbNewId()
                ,"active"           => ""
                ,"first_name"       => $_POST["first_name"]
                ,"last_name"        => $_POST["last_name"]
                ,"nickname"	    => $nick
                ,"name"		    => $nick
                ,"password"         => md5($_POST["password"])
                ,"email"            => $_POST["email"]
            );
            $ukeys=array_keys($user);
			foreach($_POST as $key => $val) {
				if (substr($val,1)!=="_" AND !in_array($key,$ukeys)) {
					$user[$key]=$val;
				}
			}

            $unsave=array_keys($user);
            foreach($_POST as $key => $value) {
                if (!in_array($key,$unsave)) {
                    $value=htmlspecialchars($value,ENT_QUOTES);
                    $user[$key]=$value;
                }
            }
            if (!isset($user["role"])) $user["role"]="user";
            $out->wbSetData($user);
            $msg=$out->find(".signbox.mail",0);
            $subj=$msg->find(".subject",0);
            $message=$msg->find(".message")->outerHtml();
            $result=wbMail(array($_ENV["settings"]["email"],$_ENV["settings"]["header"]),array($_POST["email"],$nick),$subj->text(),$message);
            unset($msg,$subj);
            if ($result) {
                $out->find(".signpanel-wrapper")->html($out->find(".signbox.check"));
                $out->find(".signbox.check")->removeClass("d-none");
                wbItemSave("users",$user);
                wbTrigger("func","wbSignup","done",null,$user);
            } else {
                $out->find(".signpanel-wrapper")->html($out->find(".signbox.error"));
                $out->find(".signbox.error")->removeClass("d-none");
                $out->find(".signbox.error p.errmsg")->html($_ENV["error"]["wbMail"]);
                wbTrigger("func","wbSignup","error",null,user);
            }

        } else {
            $out->find("[name=password]",0)->prev("label",0)->addClass("text-danger");
            $out->find("[name=password_check]",0)->prev("label",0)->addClass("text-danger");
        }
    }
    $out->find(".signbox.d-none")->remove();
    $out->wbSetData($_POST);
    $_ENV["DOM"]=$out;
    unset($out);
    return $_ENV["DOM"];
}

function engine__controller_admin()
{
	if (is_callable("engine_controller_admin")) return engine_controller_admin();
    $_ENV["DOM"]=wbGetTpl("admin.htm");
    if (isset($_ENV["route"]["action"])) {
        $call=__FUNCTION__ ."_".$_ENV["route"]["action"];
        if (is_callable($call)) {
            $res=$call();
        } else {
            $res=__FUNCTION__ .": {$_ENV['sysmsg']['err_func_lost']} ".$call."()";
        }
        echo $res;
        die;
    } else {
        wbSetChmod();
    }
    return $_ENV["DOM"];
}

function engine__controller_admin_ui()
{
    include_once(__DIR__."/admin_ui.php");
}

function engine__controller_logout()
{
		if (is_callable("engine_controller_logout")) return engine_controller_logout();
        if (isset($_SESSION["user_logout_url"]) AND $_SESSION["user_logout_url"]>"") {
                $point=$_SESSION["user_logout_url"];
        } else {$point="/";}
        setcookie("user_id", "", time()-3600, "/");
        unset($_COOKIE["user_id"]);
        wbTrigger("func","wbLogout","done");
        session_destroy();
        header("Refresh: 0; URL=".$point);
        echo "{$_ENV['sysmsg']['logout_wait']}...";
        die;
}

function engine__controller_recovery() {
	if (is_callable("engine_controller_recovery")) return engine_controller_recovery();
	$token=base64_decode($_ENV["route"]["token"]);
	$param=explode(";",$token);
	$out=wbGetTpl("login.htm");
	if (count($param)!==3) {
		header('Location: /login');
		die;
	}
	if (strpos($param[1],"@")) {
		$users=wbItemList("users",'email="'.$param[1].'"');
		foreach($users as $key => $item) {
			$user_id=$item["id"];
			break;
		}
	}
	if ($user=wbItemRead("users", $user_id)) {
		if ($user["pwdtoken"]==$param[2] AND $user["email"]==$param[1] AND $user["password"]==$param[0]) {
			if (isset($_POST["_token"]) AND $user["pwdtoken"]==$_POST["_token"] AND $_POST["_pwd1"] == $_POST["_pwd2"]) {
				$user["password"]=md5($_POST["_pwd1"]);
				$user["pwdtoken"]=null;
				$res=wbItemSave("users",$user,true);
				$out->find(".recovery-success")->removeClass("d-none");
				$out->find('.login-block')->addClass('d-none');
				$out->find('.recovery-password')->addClass('d-none');
				$out->find('.recovery-block')->addClass('d-none');
				$out->wbSetData();
				echo $out;
				die;
			} else if (!isset($_POST["_token"])) {
				$out->find('.login-block')->addClass('d-none');
				$out->find('.recovery-block')->removeClass('d-none');
				$out->wbSetData(["login"=>$param[1],"_token"=>$param[2],"_email"=>$param[1]]);
				echo $out;
			}
		} else {
			header("HTTP/1.0 404 Not Found");
			die;
		}
	} else {
		header('Location: /login');
		die;
	}
			header("HTTP/1.0 404 Not Found");
			die;
}

function __engineRecoveryPassword() {
	$out=wbGetTpl("login.htm");
	$out->wbSetData();
	if (isset($_POST["l"]) AND $_POST["l"]>"") {
		if (strpos($_POST["l"],"@")) {
		    $users=wbItemList("users",'email="'.$_POST["l"].'"');
		    foreach($users as $key => $item) { $_POST["l"]=$item["id"]; break;}
		}
		if ($user=wbItemRead("users", $_POST["l"])) {
			if (isset($user["lang"]) AND $user["lang"]>"") {
				$_SESSION["lang"]=$_ENV["lang"]=$user["lang"];
			}
			$user["pwdtoken"]=wbNewId();
			wbItemSave("users",$user,true);
			$letter=$out->find(".recovery-letter",0);
			$link=$_ENV["route"]["hostp"]."/login/recovery/".base64_encode($user["password"].";".$user["email"].";".$user["pwdtoken"]);
			$letter->wbSetData(["link"=>$link]);
			$subject=$out->find(".signbox-header .recovery-block")->text();
			$res=wbMail($_ENV["settings"]["email"].";".$_ENV["settings"]["header"],"oleg_frolov@mail.ru",$subject,$letter->outerHtml());
			$out->find(".recovery-block")->removeClass("d-none");
			$out->find('.main-block')->addClass('d-none');
			$out->find('.recovery-password')->addClass('d-none');
			$out->find('.login-block')->addClass('d-none');
			$out->wbSetData(["email"=>$user["email"],"site"=>$_ENV["settings"]["header"]]);
			if ($res) {
				$out->find(".recovery-info")->removeClass("d-none");
				echo $out;
			} else {
				$out->find(".recovery-wrong")->removeClass("d-none");
				echo $out;

			}
		}
	}
	die;
}


function engine__controller_include()
{
    include_once($_ENV["path_app"].$_SERVER["SCRIPT_NAME"]);
    die;
}