<?php

function users__list() {
	$out=wbGetForm($_ENV["route"]["form"],$_ENV["route"]["mode"]);
	$flag=""; $where=""; $Item=array();
	if (isset($_ENV["route"]["item"])) $where='role="'.$_ENV["route"]["item"].'"';
	$Item["result"]=wbItemList($_ENV["route"]["form"],$where);
	$Item["result"]=wbArraySort($Item["result"],"id");
	$Item["_table"]=$_ENV["route"]["form"];
	$out->wbSetData($Item);
	//if ($flag=="category") {$out->replaceWith($out->find("#{$_ENV["route"]["form"]}List .list")->html());}
    return $out;
}

function _usersAfterItemRead($Item) {
	if (wbLoopCheck(__FUNCTION__,func_get_args())) {return $Item;} else {wbLoopProtect(__FUNCTION__,func_get_args());}
	if ($_ENV["route"]["mode"]=="edit") {
	    if (!isset($Item["roleprop"]) OR $Item["roleprop"]=="" OR $Item["roleprop"]=="[]") {
		if ($Item["super"]=="on") {
			$Item=array_merge($Item,wbGetUserUi(true));
			$Item["isgroup"]="on";
			$Item["role"]="admin";
		} else {
			// read admin menu/dashboard config as default
			$prop=wbItemRead("users",$_SESSION["user_role"]);
			$Item["roleprop"]=$prop["roleprop"];
			$Item["_roleprop__dict_"]=$prop["_roleprop__dict_"];
		}
	    }
	}
    return $Item;
}

function _usersBeforeItemSave($Item) {
	if (wbLoopCheck(__FUNCTION__,func_get_args())) {return $Item;} else {wbLoopProtect(__FUNCTION__,func_get_args());}
	// clear menu/dashboard config
	if (!isset($Item["isgroup"]) OR !$Item["isgroup"]=="on") {unset($Item["_roleprop__dict_"],$Item["roleprop"]);}
	if (isset($_SESSION["user_id"]) AND $Item["id"]==$_SESSION["user_id"] AND $_SESSION["user_lang"]!==$Item["lang"]) {
		$_SESSION["lang"]=$_SESSION["user_lang"]=$Item["lang"];
	}
	return $Item;
}

?>
