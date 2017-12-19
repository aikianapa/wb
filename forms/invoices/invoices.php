<?php
function invoices__print() {
    $out=wbGetForm("invoices","print");
    $out->wbSetData($_POST);
    return $out;
}

function invoices__edit() {
	$Item=wbItemRead("invoices",$_ENV["route"]["item"]);
    if 			($_ENV["route"]["item"]=="_in") {
		$out=wbGetForm("invoices","in");
		$Item["type"]="in";
	} elseif 	($_ENV["route"]["item"]=="_out") {
		$out=wbGetForm("invoices","out");
		$Item["type"]="out";
	} else {
		
	}
	$out->wbSetData($Item);
    return $out;
}
?>
