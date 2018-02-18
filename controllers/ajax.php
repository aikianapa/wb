<?php
function ajax__controller() {
    include_once(__DIR__."/../ajax.php");
    if (is_file($_ENV["path_app"]."/ajax.php")) {include_once($_ENV["path_app"]."/ajax.php");}
    wbInitFunctions();
	wbTrigger("func",__FUNCTION__,"before");
	$ecall=__FUNCTION__ ."__".$_ENV["route"]["mode"];
    $acall=__FUNCTION__ ."_".$_ENV["route"]["mode"];
	if (is_callable($ecall)) {
        $_ENV["DOM"]=$ecall();
    } elseif (is_callable($acall)) {
        $_ENV["DOM"]=$aecall();
    } else {
		$call=__FUNCTION__ ."__common";
		echo @$call();
	}
	wbTrigger("func",__FUNCTION__,"after");
return $_ENV["DOM"];
}

function ajax__controller__cart() {
    $res=false;
	if (is_callable("ajax_cart")) {$res=ajax_cart();} elseif (is_callable("ajax__cart")) {$res=ajax__cart();}
	return $res;
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
	$aCall="ajax_".$_ENV["route"]["mode"];
    $eCall="ajax__".$_ENV["route"]["mode"];
	if (is_callable($aCall)) {return $aCall();} elseif (is_callable($eCall)) {return $eCall();} else {
		echo __FUNCTION__ .": отсутствует функция ".$aCall."()";
		die;
	}
}

?>
