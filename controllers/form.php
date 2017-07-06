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
	$_ENV["DOM"]=wbGetTpl("login.htm");
	return $_ENV["DOM"];
}

function form__controller__list() {
	$form=$_ENV["route"]["params"]["form"];
	$mode=$_ENV["route"]["mode"];
	$_ENV["DOM"]=wbGetForm($form,$mode);
	return $_ENV["DOM"];
}

function form__controller__edit() {
	$form=$_ENV["route"]["params"]["form"];
	$item=$_ENV["route"]["params"]["item"];
	$mode=$_ENV["route"]["mode"];
	$_ENV["DOM"]=wbGetForm($form,$mode);
	if (!is_object($_ENV["DOM"])) {$_ENV["DOM"]=wbFromString($_ENV["DOM"]);}
	$data=wbItemRead(wbTable($form),$item);
	if (!$data) {$data=array("id"=>wbNewId());}
	$_ENV["DOM"]->wbSetData($data);
	return $_ENV["DOM"];
}

?>
