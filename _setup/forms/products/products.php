<?php

function products_vitrina() {
	$tree=wbTreeRead("products_category");
	$tree=wbTreeFindBranchById($tree["tree"],$_ENV["route"]["item"],"category");
	$lang=wbArrayWhere($tree["data"]["lang"],'lang = "'.$_SESSION["lang"].'"');
	$header=$lang[0]["value"];
	$where=wbTreeWhere("products_category",$_ENV["route"]["item"],"category");
	$where='active = "on" AND ('.$where.')';
	$out=wbGetTpl("shop-grid.php");
	$out->wbSetData(array("_where"=>$where,"header"=>$header));
	return $out;
}

function productsAfterItemRead($Item) {
	if ($_ENV["route"]["mode"] == "show") {
		$Item=wbItemToArray($Item);
		$Item["header"]=$Item["name"];
	}

	return $Item;
}
?>
