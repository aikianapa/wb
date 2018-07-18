<?php
function engine__controller()
{
    wbTrigger("func", __FUNCTION__, "before");
    $call=__FUNCTION__ ."_".$_ENV["route"]["mode"];
    if (is_callable($call)) {
        $_ENV["DOM"]=$call();
    } else {
        echo __FUNCTION__ .": отсутствует функция ".$call."()";
        die;
    }
    wbTrigger("func", __FUNCTION__, "after");
    return $_ENV["DOM"];
}


function engine__controller_login()
{
    //$user=array("id"=>"admin","password"=>md5("admin"),"role"=>"admin","point"=>"/admin/","active"=>"on");
    //wbItemSave("users",$user);
    if (isset($_POST["l"]) AND $_POST["l"]>"") {
        if (strpos($_POST["l"],"@")) {
            $users=wbItemList("users",'email="'.$_POST["l"].'"');
            foreach($users as $key => $item) { $_POST["l"]=$item["id"]; break;}
        }
        
        if ($user=wbItemRead("users", $_POST["l"])) {
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

function engine__controller_login_success($user) {
        $_SESSION["user_id"]=$user["id"];
        $_SESSION["user_role"]=$user["role"];
        if (isset($user["point"]) AND $user["point"]>"") {
            $point=$user["point"];
        } else {
            if ($user["role"]=="admin") {
                $point="/admin";
            } else {
                $point="/";
            }
        }
        if (!isset($user["name"])) {
            $user["name"]=$user["id"];
        }

        if (isset($user["avatar"])) {
            if (!is_array($user["avatar"])) {
                $user["avatar"]=json_decode($user["avatar"], true);
                $user["avatar"]=$user["avatar"][0];
            }
            $user["avatar"]="/uploads/users/{$user["id"]}/{$user["avatar"]["img"]}";
        } else {
            $user["avatar"]="/engine/uploads/__system/person.svg";
        }
        $_SESSION["user_name"]=$user["name"];
        $_SESSION["user_avatar"]=$user["avatar"];
        //$_SESSION["user"]=$user;
        header('Location: '.$point);
        die;
}

function engine__controller_signup()
{   
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
        }
        
    } elseif ($exist==true) {
            $out->find(".signpanel-wrapper")->html($out->find(".signbox.exist"));
            $out->find(".signbox.exist")->removeClass("d-none");
    
    } elseif (isset($_POST["password"]) AND isset($_POST["conpassword"])) {
        if ($_POST["password"]==$_POST["conpassword"] ) {
            $nick=$_POST["firstname"]." ".$_POST["lastname"];
            $user=array(
                 "id"               => wbNewId()
                ,"active"           => ""
                ,"role"             => "user"
                ,"first_name"       => $_POST["firstname"]
                ,"last_name"        => $_POST["lastname"]
                ,"name"             => $nick
                ,"password"         => md5($_POST["password"])
                ,"email"            => $_POST["email"]
            );
            wbItemSave("users",$user);
            
            
            $msg=$out->find(".signbox.mail",0);
            $msg->wbSetData($user);
            $subj=$msg->find(".subject",0);
            wbMail(array($_ENV["settings"]["email"],$_ENV["settings"]["header"]),array($_POST["email"],$nick),$subj->html(),$msg->html());
            unset($msg,$subj);
            $out->find(".signpanel-wrapper")->html($out->find(".signbox.check"));
            $out->find(".signbox.check")->removeClass("d-none");
        } else {
            $out->find("[name=password]",0)->prev("label",0)->addClass("text-danger");
            $out->find("[name=conpassword]",0)->prev("label",0)->addClass("text-danger");
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
    $_ENV["DOM"]=wbGetTpl("admin.htm");
    if (isset($_ENV["route"]["action"])) {
        $call=__FUNCTION__ ."_".$_ENV["route"]["action"];
        if (is_callable($call)) {
            $res=$call();
        } else {
            $res=__FUNCTION__ .": отсутствует функция ".$call."()";
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
    unset($_SESSION["user_id"],$_SESSION["user_role"],$_SESSION["user"]);
    setcookie("user_id", "", time()-3600, "/");
    unset($_COOKIE["user_id"]);
    header("Refresh: 0; URL=/");
    echo "Выход из системы, ждите...";
    die;
}

function engine__controller_include()
{
    include_once($_ENV["path_app"].$_SERVER["SCRIPT_NAME"]);
    die;
}

