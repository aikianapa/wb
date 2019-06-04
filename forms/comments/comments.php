<?php

function comments__widget() {
    if (isset($_GET["item"])) {$save=$_GET["item"];}
    $_GET["item"]="_new";
    $out=wbGetForm("comments","widget");
	$out->wbSetData();
    if (isset($save)) {$_GET["item"]=$save;}
	return $out->outerHtml();
}


function comments__edit() {
	$out=wbGetForm("comments","edit");
	$Item=wbItemRead("comments",$_ENV["route"]["item"]);
	$Item=_commentsBeforeItemShow($Item);
	$out->wbSetData($Item,true);
	return $out->outerHtml();
}

function comments__getajax() {
	switch($_GET["view"]) {
		case "modal" :
			$out=wbGetForm($_GET["form"],"show");
			$Item=_commentsBeforeItemShow(wbItemRead("comments",$_GET["item"]));
			$out->wbSetData($Item);
			return $out->outerHtml();
			break;
		case "new" :
			$out=wbGetForm($_GET["form"],"edit");
			$Item["id"]=newIdRnd();
			if ($_SESSION["User"]!="Admin") {
				$out->find("textarea[name=reply]")->parents(".form-group")->remove();
				$out->find("input[type=checkbox]")->parents(".form-group")->remove();
				} else {
				$out->find(".modal-body button[formsave]")->parents(".form-group")->remove();
			}
			$out->wbSetData($Item);
			return $out->outerHtml();
			break;
	}
}

function _commentsBeforeItemShow($Item,$mode=NULL) {
    if ($mode==NULL) {$mode=$_ENV["route"]["mode"];}
    $time=strtotime($Item["date"]);
    switch($mode) {
		case "edit" :
			if (!isset($Item["date"]) OR $Item["date"]=="") {$Item["date"]=date("Y-m-d H:i:s"); } else {	$Item["date"]=date("Y-m-d H:i:s",$time); }
			break;
		case "list"	:
			$Item["dateshow"]=date("d.m.Y H:i",$time);
			if ($_SESSION["user"]["role"]!=="admin" && (!$Item["show"]==1 OR !$Item["show"]=="on") ) {$Item=NULL;}
			break;
		default		:
			    $Item["date"]=date("d.m.Y H:i",$time);
			    $Item["date_d"]=date("d",$time);
			    $Item["date_m"]=date("m",$time);
			    $Item["date_y"]=date("y",$time);
			    $Item["date_Y"]=date("Y",$time);
			    $Item["date_h"]=date("h",$time);
			    $Item["date_H"]=date("H",$time);
			    $Item["date_i"]=date("i",$time);
			    $Item["date_s"]=date("s",$time);
			    $Item["text"]=strip_tags($Item["text"]);
			break;
	}
	return $Item;
}

function _commentsBeforeItemSave($Item) {
	if (!isset($Item["id"]) OR $Item["id"]=="_new") {$Item["id"]=wbNewId();}
	if (!isset($Item["ip"])) {$Item["ip"]=$_SERVER["REMOTE_ADDR"];}
	if (!isset($Item["active"])) {$Item["active"]="on";}
	if (!isset($Item["reply"])) {$Item["reply"]="";}
	if (!isset($Item["date"]) OR $Item["date"]=="") {$Item["date"]=date("Y-m-d H:i:s");}
	$Item["text"]=htmlentities($Item["text"]);
	return $Item;
}

function _commentsAfterItemRead($Item) {
	if (isset($Item["show"]) && !isset($Item["visible"])) $Item["visible"]=$Item["show"];
	if (isset($Item["visible"]) && !isset($Item["show"])) $Item["show"]=$Item["visible"];
	return $Item;
}

?>
