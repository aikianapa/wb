<?php
function form__controller() {
	wbTrigger("func",__FUNCTION__,"before");
	if (!in_array($_ENV["route"]["mode"],array("setup_engine","edit","list")) AND !in_array($_ENV["route"]["form"],$_ENV["forms"])) {
		echo form__controller__error_404();
		die;
	}
	$call=__FUNCTION__ ."__".$_ENV["route"]["mode"];
	if (is_callable($call)) {
		$_ENV["DOM"]=$call();
	} else {
		$aCall=$_ENV["route"]["form"]."_".$_ENV["route"]["mode"];
		$eCall=$_ENV["route"]["form"]."__".$_ENV["route"]["mode"];
		if (is_callable($aCall)) {
		    $out=$aCall();
		} elseif (is_callable($eCall)) {
		    $out=$eCall();
		} elseif (form__controller__common__controller()==true) {
		    $out=$_ENV["DOM"];
		} else {
		      echo __FUNCTION__ .": {$_ENV['sysmsg']['err_func_lost']} ".$call."()";
		      die;
		}
		if (!is_object($out)) {$_ENV["DOM"]=wbFromString($out);}
		$_ENV["DOM"]=$out;
	}
	unset($out);
	wbTrigger("func",__FUNCTION__,"after");
	return $_ENV["DOM"];
}

function form__controller__common__controller() {
	$mode=$_ENV["route"]["mode"];
	$form=$_ENV["route"]["form"];
	$item=$_ENV["route"]["item"];
	$aCall=$form."_".$mode; $eCall=$form."__".$mode;
	$out=false;
	if (is_callable($aCall)) {$out=$aCall($item);} elseif (is_callable($eCall)) {$out=$eCall($item);}
	if ($out==false) {
        if ($item>"") {$mode.="_".$item;}
        $out=wbGetForm($form,$mode);
        if ($out=="") return false;
        $out=wbFromString($out);
        $_ENV["DOM"]=$out;
    }
    if (!is_object($out)) {$out=wbFromString($out);}
    $_ENV["DOM"]=$out;
    unset($out);
    return true;
}

function form__controller__show() {
	$form=$_ENV["route"]["form"];
	$item=$_ENV["route"]["item"];
	$mode="show";
    $Item=$_ENV["ITEM"]=wbItemRead($form,$item);
    if (!$Item) {
        $fid=wbFurlGet($form,$item);
        if ($fid) {$item=$fid;} else {return form__controller__error_404();}
        $Item=$_ENV["ITEM"]=wbItemRead($form,$item);
    }
    $Item=wbItemToArray($Item);
    if (is_callable("wbBeforeShowItem")) {$Item=$_ENV["ITEM"]=wbBeforeShowItem($Item);}
    $Item=$_ENV["ITEM"]=wbCallFormFunc("BeforeShowItem",$Item,$form,$mode);

    if (is_callable("wbBeforeItemShow")) {$Item=$_ENV["ITEM"]=wbBeforeItemShow($Item);}
    $Item=$_ENV["ITEM"]=wbCallFormFunc("BeforeItemShow",$Item,$form,$mode);
    $aCall=$form."_".$mode; $eCall=$form."__".$mode;
	if (is_callable($aCall)) {$out=$aCall($Item);} elseif (is_callable($eCall)) {$out=$eCall($Item);}
    if (!in_array($form,$_ENV["forms"]) OR $Item==false OR (isset($Item["active"]) AND $Item["active"]!=="on")) {
			echo form__controller__error_404();
			die;
	} else {
        if (isset($out)) {
            if (is_string($out)) {$_ENV["DOM"]=wbFromString($out);} elseif (is_object($out)) {$_ENV["DOM"]=$out;}
        } else {
            $out=wbGetForm($form,$mode);
            if ($_ENV["error"]["wbGetForm"]!=="noform") { $_ENV["DOM"]=$out; } else {
                if (!isset($Item["template"]) OR $Item["template"]=="") {
			//$Item["template"]="default.php";
			$_ENV["DOM"]=wbFromString("<html>{{text}}</html>");
		} else {
			$_ENV["DOM"]=wbGetTpl($Item["template"]);
		}
                if (!is_object($_ENV["DOM"])) {$_ENV["DOM"]=wbFromString($_ENV["DOM"]);}
            }
        }
		$_ENV["DOM"]->wbSetData($Item);
	}
	return $_ENV["DOM"];
}

function form__controller__remove() {
	if (isset($_SESSION["user_id"])) {
		$_ENV["DOM"]=wbGetForm("common","remove_confirm");
        if (isset($_REQUEST["confirm"]) AND $_REQUEST["confirm"]=="true") {
			$_ENV["DOM"]->find("script[data-wb-tag=success]")->remove();
		} else {
			wbItemRemove($_ENV["route"]["form"],$_ENV["route"]["item"]);
			$_ENV["DOM"]->find(".modal")->remove();
		}
		return $_ENV["DOM"];
	}
}

function form__controller__error_404($id=null) {
	header("HTTP/1.0 404 Not Found");
	$_ENV["route"]["error"]="404";
	$_ENV["DOM"]=wbGetTpl("404.php");
	if (is_object($_ENV["DOM"])) $_ENV["DOM"]->wbSetData();
	wbLog("func",__FUNCTION__,404,$_ENV["route"]);
	return $_ENV["DOM"];
}

function form__controller__error_301() {
    header("HTTP/1.1 301 Moved Permanently");
    header("Location: /");
    die;
}

function form__controller__list() {
	$form=$_ENV["route"]["form"];
	$mode=$_ENV["route"]["mode"];
	$aCall=$form."_".$mode; $eCall=$form."__".$mode;
	if (is_callable($aCall)) {$out=$aCall();} elseif (is_callable($eCall)) {$out=$eCall(); }
    if (!isset($out)) {
		$_ENV["DOM"]=wbGetForm($form,$mode);
		$_ENV["DOM"]->wbSetData();
	} else {
		if (is_string($out)) {$out=wbFromString($out);}
		$_ENV["DOM"]=$out;
	}
	return $_ENV["DOM"];
}

function form__controller__edit() {
	$form=$_ENV["route"]["form"];
	$item=$_ENV["route"]["item"];
	$mode=$_ENV["route"]["mode"];
    if ($item=="" OR $item=="_new") {$item=$_GET["item"]=$_ENV["route"]["item"]=wbNewId();}
	$aCall=$form."_".$mode; $eCall=$form."__".$mode;
	if (is_callable($aCall)) {$out=$aCall();} elseif (is_callable($eCall)) {$out=$eCall();}
	if (!isset($out)) {
		$_ENV["DOM"]=wbGetForm($form,$mode);
		$data=wbItemRead(wbTable($form),$item);
		if (!$data) {$data=array("id"=>$_ENV["route"]["item"]);}
	} else {
		if (is_string($out)) {$out=wbFromString($out);}
		$_ENV["DOM"]=$out;
	}
	$_ENV["DOM"]->wbSetData($data);
	return $_ENV["DOM"];
}

function form__controller__default_mode() {
	if (!is_dir($_ENV["path_app"]."/forms")) {form__controller__setup_engine();} else {die("Установка выполнена");}
}

function form__controller__select2() {
    $form=""; $where=""; $tpl=""; $val="";
    $form=$_ENV["route"]["form"];
    $custom="form_controller_select2_{$form}";
    if (is_callable($custom)) {return $custom();} else {
        $result=array();
        $single=false;
        if (isset($_POST["where"]) AND $_POST["where"]>"") {$where=$_POST["where"];}
        if (isset($_POST["tpl"]) AND $_POST["tpl"]>"") {$tpl=$_POST["tpl"];}
        if (isset($_POST["value"]) AND $_POST["value"]>"") {$val=$_POST["value"];}
        if (isset($_POST["id"]) AND $_POST["id"]>"") {$where='id="'.$_POST["id"].'"'; $single=true;}
        $list=wbItemList($form,$where);
        $iterator=new ArrayIterator($list);
        foreach($iterator as $key => $r) {
            $data=wbSetValuesStr(strip_tags($tpl),$r);
            $res=preg_match("/{$val}/ui",$data);
            if ($res) {$result[]=array("id"=>$r["id"],"text"=>$data,"item"=>$r);}
        }
        if ($single AND count($result)>0) {$result=$result[0];}
        header('Content-Type: application/json');
        echo wbJsonEncode($result);
        die;
    }
}

function form__controller__setup_engine() {
    $out=wbGetTpl("/engine/tpl/setup.htm",true);
    if (isset($_GET["params"]["lang"])) {$_SESSION["lang"]=$_ENV["lang"]=$_GET["params"]["lang"];} else {unset($_SESSION["lang"],$_ENV["lang"]);}
    if (is_dir($_ENV["path_tpl"])) {
	$out->wbSetFormLocale();
        $out->find("#setup")->remove();
        return $out;
     } elseif (isset($_POST["setup"]) AND $_POST["setup"]=="done" AND !is_dir($_ENV["path_app"]."/form")) {
		unset($_ENV["DOM"],$_ENV["errors"]);
		wbRecurseCopy($_ENV["path_engine"]."/_setup/",$_ENV["path_app"]);
		wbTableCreate("pages");
		wbTableCreate("users");
		wbTableCreate("todo");
		wbTableCreate("news");
		wbTableCreate("orders");
		$user=array("id"=>$_POST["login"],"password"=>md5($_POST["password"]),"email"=>$_POST["email"],"role"=>"admin","login_url"=>"/admin/","logout_url"=>"/login/","isgroup"=>"on","active"=>"on","super"=>"on","lang"=>$_SESSION["lang"]);
		$settings=array("id"=>"settings","header"=>$_POST["header"],"email"=>$_POST["email"],"lang"=>$_SESSION["lang"]);
		wbItemSave("users",$user);
		wbItemSave("admin",$settings);
		header('Location: '.'/');
		die;
	}
    $out->find("#error")->remove();
    $out->wbSetData();
    return $out;
    die;
}

?>
