<?php


function comments__widget() {
	$out=wbGetForm();
	$out->wbSetData();
	return $out->outerHtml();
}


function comments__edit() {
	$out=wbGetForm("comments","edit");
    $Item=wbItemRead("comments",$_ENV["route"]["item"]); 
	$Item=_commentsBeforeItemShow($Item);
	$out->wbSetData($Item);
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
	switch($mode) {
		case "edit" :
			if (!isset($Item["date"]) OR $Item["date"]=="") {$Item["date"]=date("Y-m-d H:i:s"); } else {	$Item["date"]=date("Y-m-d H:i:s",strtotime($Item["date"])); }
			break;
		case "list"	:
			$Item["date"]=date("d.m.Y H:i",strtotime($Item["date"]));
			$Item["text"]=wbGetWords(strip_tags($Item["text"]),50);
			$Item["smalltext"]=wbGetWords(strip_tags($Item["text"]),10);
			if ($_SESSION["user_id"]!=="admin" && (!$Item["show"]==1 OR !$Item["show"]=="on") ) {$Item=NULL;}
			break;
		default		:
			$Item["date_short"]=date("d.m.y H:i",strtotime($Item["date"]));
			$Item["date"]=date("d.m.Y H:i",strtotime($Item["date"]));
			$Item["header"]="";
			break;
	}
	return $Item;
}

function _commentsBeforeItemSave($Item) {
    print_r($Item);
	if (!isset($Item["id"]) OR $Item["id"]=="_new") {$Item["id"]=wbNewId();}
	if (!isset($Item["date"]) OR $Item["date"]=="") {$Item["date"]=date("Y-m-d H:i:s");}
	return $Item;
}

function _commentsAfterItemRead($Item) {
	if (isset($Item["show"]) && !isset($Item["visible"])) $Item["visible"]=$Item["show"];
	if (isset($Item["visible"]) && !isset($Item["show"])) $Item["show"]=$Item["visible"];
	return $Item;
}

?>
