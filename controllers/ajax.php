<?php
include_once(__DIR__."/../ajax.php");
function ajax__controller() {
	wbTrigger("func",__FUNCTION__,"before");
	$call=__FUNCTION__ ."__".$_ENV["route"]["mode"];
	if (is_callable($call)) {$_ENV["DOM"]=$call();} else {
		$call=__FUNCTION__ ."__common";
		echo @$call();
	}
	wbTrigger("func",__FUNCTION__,"after");
return $_ENV["DOM"];
}

function ajax__controller__forms() {
	$route=$_ENV["route"];
	echo 1111111111111111111111;
	die;
	return false;
}

function ajax__controller__pagination() {
	if (is_callable("ajax__pagination")) {echo ajax__pagination();} else {echo json_encode(false);}
	die;
}

function ajax__controller__save() {
	if (is_callable("ajax__save")) {echo ajax__save();} else {echo json_encode(false);}
	die;
}

function ajax__controller__common() {
	$call="ajax__".$_ENV["route"]["mode"];
	if (is_callable($call)) {return @$call();} else {
		echo __FUNCTION__ .": отсутствует функция ".$call."()";
		die;		
	}
}

?>
