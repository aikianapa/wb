<?php

function news_show() {
	$out=wbGetTpl("news_show.php");
	$out->wbSetData($_ENV["ITEM"]);
	return $out;
}


function newsAfterItemRead($Item) {
	$Item=wbItemToArray($Item);
	$Item["header"]=$Item["lang"][$_SESSION["lang"]]["name"];
    	return $Item;
}
?>
