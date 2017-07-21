<?php
function form__controller() {
	wbTrigger("func",__FUNCTION__,"before");
	$call=__FUNCTION__ ."__".$_ENV["route"]["mode"];
	if (is_callable($call)) {
		$_ENV["DOM"]=$call();
	} else {
		echo __FUNCTION__ .": отсутствует функция ".$call."()";
		die;
	}
	wbTrigger("func",__FUNCTION__,"after");
return $_ENV["DOM"];
}

function form__controller__show() {
	$Item=wbItemRead(wbTable("pages"),$_ENV["route"]["item"]);
	if ($Item==false OR $Item["active"]!=="on") {
		echo form__controller__error_404();
		die;
	} else {
		if (!isset($Item["template"]) OR $Item["template"]=="") {$Item["template"]="default.php";}
		$_ENV["DOM"]=wbGetTpl($Item["template"]);
		$_ENV["DOM"]->wbSetData($Item);
	}
	return $_ENV["DOM"];
}

function form__controller__remove() {
	if (isset($_SESSION["user_id"])) { 
		$_ENV["DOM"]=wbGetForm("common","remove_confirm");
		if (isset($_GET["params"]["confirm"])) {
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
	return wbGetTpl("404.htm");
}

function form__controller__list() {
	$form=$_ENV["route"]["form"];
	$mode=$_ENV["route"]["mode"];
	$_ENV["DOM"]=wbGetForm($form,$mode);
	$_ENV["DOM"]->wbSetData();
	return $_ENV["DOM"];
}

function form__controller__edit() {
	$form=$_ENV["route"]["form"];
	$item=$_ENV["route"]["item"];
	$mode=$_ENV["route"]["mode"];
	$aCall=$form."_".$mode; $eCall=$form."__".$mode;
	if (is_callable($aCall)) {$out=$aCall();} elseif (is_callable($eCall) AND $engine!==false) {$out=$eCall();}
	if (!isset($out)) {$_ENV["DOM"]=wbGetForm($form,$mode);} else {
		if (is_string($out)) {$out=wbFromString($out);}
		$_ENV["DOM"]=$out;
	}
	$data=wbItemRead(wbTable($form),$item);
	if (!$data) {$data=array("id"=>wbNewId());}
	$_ENV["DOM"]->wbSetData($data);
	return $_ENV["DOM"];
}

function form__controller__default_mode() {
	if (!is_dir($_ENV["path_app"]."/forms")) {form__controller__setup_engine();} else {die("Установка выполнена");}
}

function form__controller__setup_engine() {
	if (isset($_POST["setup"]) AND $_POST["setup"]=="done" AND !is_dir($_ENV["path_app"]."/form")) {
		unset($_ENV["DOM"],$_ENV["errors"]);
		wbRecurseCopy($_ENV["path_engine"]."/_setup/",$_ENV["path_app"]);
		wbTableCreate("pages");
		wbTableCreate("users");
		wbTableCreate("todo");
		wbTableCreate("news");
		wbTableCreate("orders");
		$user=array("id"=>$_POST["login"],"password"=>md5($_POST["password"]),"role"=>"admin","point"=>"/admin/","active"=>"on","super"=>"on");
		wbItemSave("users",$user);

		header('Location: '.'/');
		die;
	}
	//if (is_dir($_ENV["dba"])) {die("Установка уже выполнена");}

		echo wbGetTpl("setup.htm");


	die;
}

?>
