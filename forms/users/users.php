<?php
function _usersAfterItemRead($Item) {
	if (wbLoopCheck(__FUNCTION__,func_get_args())) {return $Item;} else {wbLoopProtect(__FUNCTION__,func_get_args());}
    if (!isset($Item["roleprop"]) OR $Item["roleprop"]=="" OR $Item["roleprop"]=="[]") {
	if ($Item["id"]=="admin") {
		// read old config
		$prop=wbTreeRead("_config");
		if (isset($prop["tree"]) AND count($prop["tree"])) {
			$Item["roleprop"]=$prop["tree"];
		} else {
			$prop=wbItemRead("users:engine","admin");
			$Item["roleprop"]=$prop["roleprop"];
		}
	} else {
		// read admin menu/dashboard config as default
		$prop=wbItemRead("users","admin");
		$Item["roleprop"]=$prop["roleprop"];
		$Item["_roleprop__dict_"]=$prop["_roleprop__dict_"];
	}
    }
    return $Item;
}

function _usersBeforeItemSave($Item) {
	if (wbLoopCheck(__FUNCTION__,func_get_args())) {return $Item;} else {wbLoopProtect(__FUNCTION__,func_get_args());}
	// clear menu/dashboard config
	if (!isset($Item["isgroup"]) OR !$Item["isgroup"]=="on") {unset($Item["_roleprop__dict_"],$Item["roleprop"]);}
	return $Item;
}
?>
