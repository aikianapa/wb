<?php
function module__controller() {
	wbTrigger("func",__FUNCTION__,"before");
	$call=__FUNCTION__ ."__".$_ENV["route"]["mode"];
	if (is_callable($call)) {
		$_ENV["DOM"]=$call();
	} else {
		module__controller__init($_ENV["route"]["mode"]);
	}
	wbTrigger("func",__FUNCTION__,"after");
	return $_ENV["DOM"];
}

function module__controller__init($module=null) {
	if ($module==null) $module=$_ENV["route"]["name"];
	$aModule=$_ENV["path_app"]."/modules/{$module}/{$module}.php";
	$eModule=$_ENV["path_engine"]."/modules/{$module}/{$module}.php";
	if (is_file($aModule)) {include_once($aModule);} elseif (is_file($eModule)) {include_once($eModule);}
	$out=null;
	$call=$module."_init"; if (is_callable($call)) {$out=@$call($_ENV["DOM"]);}
	$call=$module."__init"; if (is_callable($call)) {$out=@$call($_ENV["DOM"]);}
	if ($out!==null) {$_ENV["DOM"]=$out;} else {
		$_ENV["DOM"]=wbFromString("{$_ENV['sysmsg']['err_mod_lost']}: [{$module}]");
	}
	if (!is_object($_ENV["DOM"])) {$_ENV["DOM"]=wbFromString($_ENV["DOM"]);}
	return $_ENV["DOM"];
}


?>
