<?php
function orders__edit() {
	$out=wbGetForm();
	$Item=wbItemRead("orders",$_ENV["route"]["item"]);
	$out->wbSetData($Item);
	return $out;
}
?>
