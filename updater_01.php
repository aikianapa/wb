<?php
include_once (__DIR__."/functions.php");
wbInit();

$upd=wbItemRead("admin","updates");
if (!isset($upd["ready"])) {
        $_SESSION["user_role"]="admin";
        $list=wbListFiles($_ENV["dba"]);
        foreach ($list as $table) {
            $ext=pathinfo($_ENV["dba"]."/".$table, PATHINFO_EXTENSION);
            if ($ext=="" AND !is_file($_ENV["dba"]."/".$table.".json")) {
                $_ENV["DBA"]->createTable($table);
                $tlist=wbItemListOld($table);
                foreach($tlist as $item) {
                    $_ENV["DBA"]->insert($table,$item,true);
                }
            }
        }

        $upd=wbItemRead("admin","updates");
        if (!isset($upd["ready"])) {$upd["ready"]=array();}
        if (!in_array(__FILE__,$upd["ready"])) {$upd["ready"][]=__FILE__;}
        wbRecurseDelete($_ENV["dba"]."/_cache");
}
//unlink(__FILE__);

function wbItemReadOld($table=null,$id=null) {
	if ($table==null) {$table=$_ENV["route"]["form"];}
	if ($id==null) {$id=$_ENV["route"]["item"];}
    wbTrigger("form",__FUNCTION__,"BeforeItemRead",func_get_args(),array());
	if (count(explode($_ENV["path_app"],$table))==1) {$table=wbTable($table);}
	//$cache=wbCacheNameOld($table,$id);
	//if (is_file($cache)) {
	//	$item=json_decode(file_get_contents($cache),true);
	//} else {
		$list=wbItemListOld($table);
		if (isset($list[$id])) {
			$item=$list[$id];
		} else {
			wbError("func",__FUNCTION__,1006,func_get_args());
			$item=null;
		}
	//}
	if (is_array($item) AND isset($item["__wbFlag__"])) { // если стоит флаг удаления, то возвращаем null
		if ($item["__wbFlag__"]=="remove") {$item=null;}
	} else {
		$item=wbTrigger("form",__FUNCTION__,"AfterItemRead",func_get_args(),$item);
	}
	return $item;
}

function wbItemListOld($table="pages",$where="",$sort="id") {
	if (count(explode($_ENV["path_app"],$table))==1) {$table=wbTablePathOld($table,false);}
	if (!is_file($table)) {
		wbError("func",__FUNCTION__,1001,func_get_args());
		return array();
	} else {
	   wbTrigger("form",__FUNCTION__,"BeforeItemList",func_get_args(),array());
        if (!isset($_ENV["cache"][$table])) {
			$_ENV["cache"][$table]=json_decode(file_get_contents($table),true);
		}
		if (isset($_ENV["cache"][$table]["data"])) {$list=$_ENV["cache"][$table]["data"];} else {$list=array();}
		if (!is_array($list)) {$list=array($list);}
		
		$object = new ArrayObject($list);
		foreach($object as $key => $item) {
            $item=wbTrigger("form",__FUNCTION__,"AfterItemRead",$args,$item);
            if 	(
								(substr($item["id"],0,1)=="_" AND $_SESSION["user_role"]!=="admin")
						OR
								($item==null)
            
                        OR      (isset($item["_remove"]) AND $item["_remove"]==true)

						) {unset($list[$key]);} elseif (!wbWhereItem($item,$where)) {unset($list[$key]);}
		  }
	}
	$list=wbTrigger("form",__FUNCTION__,"AfterItemList",func_get_args(),$list);
	$list=wbTrigger("func",__FUNCTION__,"after",func_get_args(),$list);
	return $list;
}

function wbTablePathOld($table="data",$engine=false) {
	if ($engine==false) {$db=$_ENV["dba"];} else {$_ENV["dbe"];}
	$table=$db."/".$table;
	wbTrigger("func",__FUNCTION__,"after",func_get_args(),$table);
	return $table;
}

?>