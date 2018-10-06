<?php
function ajax__pagination() {
    if (isset($_REQUEST["route"])) {
        $_ENV["route"]=json_decode($_REQUEST["route"],true);
    }
    $res=array();
    foreach($_POST as $key =>$val) {
        $$key=$val;
    }
    $vars=wbItemToArray(json_decode($vars,true));
    $tmp_a=$_ENV["dba"];
    $tmp_e=$_ENV["dbe"];
    if (is_array($vars)) $_ENV=array_merge($_ENV,$vars);
    $_ENV["dba"]=$tmp_a;
    $_ENV["dbe"]=$tmp_e;
    unset($vars,$tmp_a,$tmp_e);
    if (!isset($page)) $page=1;
    if (!isset($find)) $find="";
    $fe=wbFromString("<div>".$foreach."</div>");
    $fe->wbClearClass();
    $fe->find("[data-wb-tpl={$tplid}]")->attr("data-wb-find",$find);
    $fe->find("[data-wb-tpl={$tplid}]")->attr("data-wb-cache",$cache);
    $fe->find("[data-wb-tpl={$tplid}]")->attr("data-wb-page",$page);
    $fe->find("[data-wb-tpl={$tplid}]")->removeClass("wb-done");
    $fe->find("[data-wb-tpl={$tplid}]")->append($tpl);
    $fe->find("[data-wb-tpl={$tplid}]",0)->tagForeach();
    //$form=$fe->find("[data-wb-tpl={$tplid}]")->attr("form");
    //$call=$form."BeforePaginationShow";
    //if (is_callable($call)) {$call($fe->find("[data-wb-tpl={$tplid}]",0) );}
    $res["data"]=$fe->find("[data-wb-tpl={$tplid}]")->html();
    $res["pagr"]=$fe->find("#ajax-{$tplid}")->outerHtml();
    $res["pages"]=$fe->find("[data-wb-tpl={$tplid}]")->attr("data-wb-pages");
    return json_encode($res);
}

function ajax__pagination_vars() {
    // $tplid=$_ENV["route"]["params"][0];
    // $res=$_SESSION["temp"][$tplid];
    // unset($_SESSION["temp"][$tplid]);
    // return wbJsonEncode($res);
}

function ajax__settings() {
    $sett=$_ENV["settings"];
    $mrch=wbMerchantList();
    foreach($mrch as $k => $m) {
        unset($sett[$m["name"]]);
    }
    return base64_encode(json_encode($sett));
}

function ajax__cart() {
    return wbCartAction();
}

function ajax__alive() {
    $ret=false;
    if (isset($_SESSION["user_role"]) && $_SESSION["user_role"]>"") {
        if ($_POST["data"]=="wb_get_user_role") {
            $ret["user_role"]=$_SESSION["user_role"];
            $ret["mode"]="wb_set_user_role";
            $out=wbGetForm("common","modal");
            $out->find(".modal")->attr("id","wb_session_die");
            $out->find(".modal-header")->html("<h6 class='text-danger'><i class='fa fa-exclamation-triangle'></i> Сессия завершена</h6>");
            $out->find(".modal-body")->html("<center>Истёк период ожидания сессиии!<br>Пожалуйста, выполните <a href='/login'>вход в систему</a> заново.</center>");
            $out->find(".modal-footer")->html("<a class='btn btn-primary' href='/login'><i class='fa fa-sign-in'></i> Войти в систему</a>");
            $ret["msg"]=$out->outerHtml();
        }
        elseif ($_POST["data"]==$_SESSION["user_role"]) {
            $ret=true;
        }
        elseif ($_POST["data"]=="wb_session_die") {
            $ret["mode"]=$_POST["data"];
        }
    }
    // Clear cache

    $path=$_ENV["dba"]."/_cache";
    $files=wbListFiles($path);
    foreach($files as $file) {
        $user=md5($_SESSION["user_id"]);
        if (substr($file,0,strlen($user))==$user) {
            if (isset($_POST["data"]) && is_array($_POST["data"]) && !in_array($file,$_POST["data"])) {
                // если файл не в списке присутствующих на экране списков, то удаляем
                unlink($path."/".$file);
            }
        } else {
            if (filemtime($path."/".$file)+86400 < time()) {
                // если файл не принадлежит юзеру, но ему больше суток, то удаляем
                unlink($path."/".$file);
            }
        }
    }


    return json_encode($ret);
}

function ajax__gettree() {
    foreach($_POST as $k => $v) {
        $$k=$v;
    }
    $tree=wbTreeRead($tree);
    $tree=wbTreeFindBranch($tree["tree"],$branch,$parent,$childrens);
    return base64_encode(json_encode($tree));
}

function ajax__gettreedict() {
    foreach($_POST as $k => $v) {
        $$k=$v;
    }
    $tree=wbTreeRead($tree);
    $tree=$tree["dict"];
    return base64_encode(json_encode($tree));
}


function ajax__save($form=null) {
    if ($form==null) {
        $form=$_ENV["route"]["form"];
    }
    $eFunc="{$form}__formsave";
    $aFunc="{$form}_formsave";
    if 		(is_callable($aFunc)) {
        $ret=$aFunc();
    }
    elseif (is_callable($eFunc)) {
        $ret=$eFunc();
    }
    else {
        if (!isset($_POST["id"])) {
            $_POST["id"]="";
        }
        if (!isset($_GET["item"])) {
            $_POST["id"]="";
        }
        if ($_POST["id"]=="" && $_GET["item"]>"") {
            $_POST["id"]=$_GET["item"];
        }
        $res=wbItemSave($form,$_POST);
        $ret=array();
        if (isset($_GET["copy"])) {
            $old=str_replace("//","/",$_ENV["path_app"]."/uploads/{$form}/{$_GET["copy"]}/");
            $new=str_replace("//","/",$_ENV["path_app"]."/uploads/{$form}/{$_GET["item"]}/");
            recurse_copy($old,$new);
        }
        if ($res) {
            $ret["error"]=0;
        } else {
            $ret["error"]=1;
            $ret["text"]=$res;
            header("HTTP/1.0 404 Not Found");
        }
    }
    return json_encode($ret);
}

function ajax__setdata() {
    $form=$_ENV["route"]["form"];
    $item=$_ENV["route"]["item"];
    $table=wbTable($form);
    $_REQUEST=json_decode(base64_decode($_REQUEST["data"]),true);
    if (isset($_REQUEST["_base"])) {
        $_ENV['path_tpl'] = $_ENV['path_app'].$_REQUEST["_base"];
    }
    if (!isset($_REQUEST["data-wb-mode"])) {
        $_REQUEST["mode"]="list";
    }
    else {
        $_REQUEST["mode"]=$_REQUEST["data-wb-mode"];
    }
    $Item=wbItemRead($table,$item);
    if (!is_array($Item)) {
        $Item=array($Item);
    }
    if (!is_array($_REQUEST["data"])) {
        $_REQUEST["data"]=array($_REQUEST["data"]);
    }
    $Item=array_merge(wbItemToArray($Item),$_REQUEST["data"]);
    if (isset($Item["_form"])) {
        $_ENV["route"]["form"]=$_GET["form"]=$Item["_form"];
        $_ENV["route"]["controller"]="form";
    }
    if (isset($Item["_item"])) {
        $_ENV["route"]["item"]=$_GET["item"]=$Item["_item"];
    }
    if (isset($Item["_mode"])) {
        $_ENV["route"]["mode"]=$_GET["mode"]=$Item["_mode"];
    }
    $Item=wbCallFormFunc("BeforeShowItem",$Item,$form,$_REQUEST["mode"]);
    $Item=wbCallFormFunc("BeforeItemShow",$Item,$form,$_REQUEST["mode"]);
    $tpl=wbFromString($_REQUEST["tpl"]);
    $tpl->find(":first")->attr("item","{{id}}");
    foreach($tpl->find("[data-wb-role]") as $dr) {
        $dr->removeClass("wb-done");
    }
    $tpl->wbSetData($Item);
    $tpl->tagHideAttrs();
    return $tpl;
}

function ajax__listfiles() {
    $result=array();
    $_GET["path"]="/".implode("/",$_ENV["route"]["params"]);
    if ($_GET["path"]=="") {
        $path=$_ENV["path_app"]."/uploads";
    }
    else {
        $path=$_ENV["path_app"].$_GET["path"];
    }
    $files=wbListFiles($path);
    if (is_array($files)) {
        foreach($files as $key => $file) {
            if (is_file($path."/".$file)) {
                $result[]=$file;
            }
        }
    }
    return json_encode($result);
}

function ajax__newid() {
    return json_encode(wbNewId());
}

function ajax__gettpl() {
    echo wbGetTpl($_ENV["route"]["params"][0]);
    die;
}

function ajax__remove() {
    if ($_ENV["route"]["params"][0]=="uploads") {
        $file=$_ENV["path_app"]."/".implode("/",$_ENV["route"]["params"]);
        return json_encode(wbFileRemove($file));
    }
    die;
}

function ajax__getform() {
    echo wbGetForm($_ENV["route"]["params"][0],$_ENV["route"]["params"][1]);
    die;
}

function ajax__buildfields() {
    $res=array();
    if (isset($_POST["data"])) {
        $data=$_POST["data"];
    }
    else {
        $data=array();
    }
    foreach($_POST["dict"] as $dict) {
        $dict["value"]=htmlspecialchars($dict["value"]);
        $res=wbFieldBuild($dict,$data);
        return $res;
    }
    die;
}


function ajax__treeedit() {
    $out=wbGetForm("common","tree_edit");
    $out->wbSetData($_POST);
    $arr=array();
    foreach($_POST["fields"] as $dict) {
        if (isset($dict["name"]) AND !in_array($dict["name"],$arr)) {
            $dict["value"]=htmlspecialchars($dict["value"]);
            $res=wbFieldBuild($dict,$_POST["data"]);
            $out->find(".treeData form")->append($res);
            $arr[]=$dict["name"];
        }
    }
    return $out->outerHtml();
}


function ajax__mailer() {
    return ajax__mail();
}

function ajax__mail() {
    if (!isset($_POST["_subject"])) {
        $_POST["_subject"]="Письмо с сайта";
    }
    if (isset($_POST["_tpl"])) {
        $out=wbGetTpl($_POST["_tpl"]);
    }
    elseif (isset($_POST["_form"])) {
        $out=wbGetTpl($_POST["_form"]);
    }
    elseif (isset($_POST["_message"])) {
        $out=wbFromString($_POST["_message"]);
    }
    else {
        $out=wbGetTpl("mail.php");
    }
    if (!isset($_POST["email"])) {
        $_POST["email"]=$_ENV["route"]["mode"]."@".$_ENV["route"]["host"];
    }
    if (!isset($_POST["name"])) {
        $_POST["name"]="Site Mailer";
    }

    if (isset($_POST["_mailto"])) {
        $mailto=$_POST["_mailto"];
    }
    else {
        $mailto = $_ENV["settings"]["email"];
    }

    $out->wbSetData($_POST);
    $out=$out->outerHtml();

    if ($_ENV["route"]["mode"]=="mailer") {
        $res=wbMailer("{$_POST["email"]};{$_POST["name"]}","{$mailto};{$_ENV["settings"]["header"]}",$_POST["_subject"],$out);
    } else {
        $res=wbMail("{$_POST["email"]};{$_POST["name"]}","{$mailto};{$_ENV["settings"]["header"]}", $_POST["subject"], $out);
    }
    if (!$res) {
        $result=json_encode(array("error"=>true,"msg"=>"Ошибка отправки сообщения: ".$_ENV["error"]));
    } else {
        $result=json_encode(array("error"=>false,"msg"=>"Сообщение отправлено!"));
    }
    if (isset($_POST["callback"]) AND is_callable($_POST["_callback"])) {
        @$_POST["_callback"]($result);
    }
    return $result;
}
?>
