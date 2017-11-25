<?php
function products__list() {
	$out=wbGetForm($_ENV["route"]["form"],$_ENV["route"]["mode"]);
	$flag=""; $where=""; $Item=array();
	if (isset($_ENV["route"]["item"]) && $_ENV["route"]["item"]>"") {$where=wbTreeWhere($_ENV["route"]["form"]."_category",$_ENV["route"]["item"],"category"); $flag="category";}
    $Item["result"]=wbItemList($_ENV["route"]["form"],$where);
	$Item["result"]=wbArraySort($Item["result"],"id");
	$Item["_table"]=$_ENV["route"]["form"];
	$out->wbSetData($Item);
	//if ($flag=="category") {$out->replaceWith($out->find("#{$_ENV["route"]["form"]}List .list")->html());}
    return $out;
}
?>
