<?php
function %form%__edit() {
	$out=wbGetForm("%form%","edit");
	$id=$_ENV["route"]["item"];
	$Item=wbItemRead("%form%",$id);
	if ($id=="_new") $Item["id"]=wbNewId();
	$Item=wbCallFormFunc("BeforeItemEdit",$Item);
	$out->wbSetData($Item);
	return $out;
}

function %form%__list() {
	$out=wbGetForm("%form%","list");
	$out->wbSetData();
	return $out;
}

function _%form%BeforeItemEdit($Item=null) {
	return $Item;
}

function _%form%BeforeItemShow($Item=null) {
	return $Item;
}

function _%form%AfterItemRead($Item=null) {
	return $Item;
}

function _%form%AfterItemList($Item=null) {
	return $Item;
}
?>
