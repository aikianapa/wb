<?php
include(__DIR__."/kiDom.php");
function wbInit() {
	wbErrorList();
	wbTrigger("func",__FUNCTION__,"before");
	wbInitEnviroment();
	wbInitDatabase();
	wbInitFunctions();
	wbRouterAdd();
	wbRouterGet();
}

function wbInitEnviroment() {
  if (!isset($_SESSION["user"])) {$_SESSION["user"]="User";}
  if (!isset($_SESSION["user_role"])) {$_SESSION["user_role"]="user";}
	wbTrigger("func",__FUNCTION__,"before");
	$_ENV["path_engine"]=__DIR__;
	$_ENV["path_app"]=$_SERVER["DOCUMENT_ROOT"];
	$_ENV["dbe"]=__DIR__."/database"; 			// Engine data
	$_ENV["dba"]=$_ENV["path_app"]."/database";	// App data
	$_ENV["dbec"]=__DIR__."/database/_cache"; 			// Engine data
	$_ENV["dbac"]=$_ENV["path_app"]."/database/_cache";	// App data
	$_ENV["error"]=array();
	$_ENV["env_id"]=$_ENV["new_id"]=wbNewId();
  $_ENV["datetime"]=date("Y-m-d H:i:s");
	$_ENV["forms"]=wbListForms(false);
    wbCheckWorkspace();
	$variables=array();
	$settings=wbItemRead("admin","settings");
	if (!$settings) {$settings=array();} else {
		foreach((array)$settings["variables"] as $v) {
			$variables[$v["var"]]=$v["value"];
		}
	}
	$_ENV["variables"]=$variables;
	$settings=array_merge($settings,$variables);
	$_ENV["settings"]=$settings;
}

function wbCheckWorkspace() {
     if (!is_readable($_ENV["path_app"]) OR !is_writable($_ENV["path_app"])) {
        $out=wbGetTpl("setup.htm");
        $error="<p><h4>ВНИМАНИЕ!</h4> Установка невозможна, так как дирректория установки
        <i>{$_ENV["path_app"]}</i> не имеет необходимых прав доступа.
        Пожалуйста, установите права чтения/записи/создания файлов для указанной дирректории
        и попробуйте снова.</p>";
        $out->find("#error .alert-warning")->html($error);
        $out->find(".step-content.active")->removeClass("active");
        $out->find("#error.step-content")->addClass("active");
        $out->wbSetData();
        echo $out;
        die;
    }
}

function wbFormUploadPath() {
	$path="/uploads";
	if ($_ENV["route"]["controller"]=="form") {
		if (isset($_ENV["route"]["form"]) AND $_ENV["route"]["form"]>"") {$path.="/".$_ENV["route"]["form"];} else {$path.="/undefined";}
		if (isset($_ENV["route"]["item"]) AND $_ENV["route"]["item"]>"") {$path.="/".$_ENV["route"]["item"];} else {$path.="/undefined";}
	}elseif ($_ENV["route"]["controller"]=="ajax" AND $_ENV["route"]["mode"]=="buildfields" AND isset($_POST["data"])) {
		if (isset($_POST["data"]["_form"]) AND $_POST["data"]["_form"]>"") {$path.="/".$_POST["data"]["_form"];} else {$path.="/undefined";}
		if (isset($_POST["data"]["_id"]) AND $_POST["data"]["_id"]>"") {$path.="/".$_POST["data"]["_id"];} else {$path.="/undefined";}
    } else {$path.="/undefined";}
	return $path;
}

function wbInitFunctions() {
	wbTrigger("func",__FUNCTION__,"before");
	if (is_file($_ENV["path_app"]."/functions.php")) {require_once($_ENV["path_app"]."/functions.php");}
	foreach($_ENV["forms"] as $form) {
			$inc=array(
				"{$_ENV["path_engine"]}/forms/{$form}.php", "{$_ENV["path_engine"]}/forms/{$form}/{$form}.php",
				"{$_ENV["path_app"]}/forms/{$form}.php", "{$_ENV["path_app"]}/forms/{$form}/{$form}.php"
			); $res=FALSE;
			foreach($inc as $k => $file) {
				//if (is_file("{$file}") && $res==FALSE ) {include_once("{$file}"); if ($k>1) {$res=TRUE;}; }
                if (is_file("{$file}") && $res==FALSE ) {include_once("{$file}");}
			}
	}

}

function wbItemToArray($Item=array()) {
    if (is_array($Item)) {
        foreach($Item as $i => $item) {
            if (!is_array($item) AND substr($item,0,2)=="[{") {$item=json_decode($item,true);}
            $item=wbItemToArray($item);
            $Item[$i]=$item;
            if (isset($item["id"])) {unset($Item[$i]); $Item[$item["id"]]=$item;}

        }
    }
    return $Item;
}

function wbGetDataWbFrom($Item,$str) {
    $str=trim($str);
    $str=wbSetValuesStr($str,$Item);
    $Item=wbItemToArray($Item);
    $pos=strpos($str,"[");
    if ($pos) {
        $fld="[".substr($str,0,$pos)."]";
        $suf=substr($str,$pos);
        $fld.=$suf;
        $fld=str_replace('[','["',$fld);
        $fld=str_replace(']','"]',$fld);
        $fld=str_replace('""','"',$fld);
        eval('$res=$Item'.$fld.';');
        return $res;
    } else {
        return $Item[$str];
    }
}

function wbFieldBuild($param,$data=array()) {
	$set=wbGetForm("common","tree_fldset");
	$tpl=wbGetForm("snippets",$param["type"]);
	$opt=json_decode($param["prop"],true);
	$options="";
	if (isset($opt["required"]) AND $opt["required"]==true) {$options.=" required ";}
	if (isset($opt["readonly"]) AND $opt["readonly"]==true) {$options.=" readonly ";}
	if (isset($opt["disabled"]) AND $opt["disabled"]==true) {$options.=" disabled ";}
	$param["options"]=trim($options);
	switch($param["type"]) {
		case "number":
			if (isset($opt["min"])) {$tpl->find("input")->attr("min",$opt["min"]);}
			if (isset($opt["max"])) {$tpl->find("input")->attr("max",$opt["max"]);}
			if (isset($opt["step"])) {$tpl->find("input")->attr("step",$opt["step"]);}
			if (isset($opt["datalist"])) {
				$param["listid"]=wbNewId();
				$tpl->find("input")->attr("list",$param["listid"]);
				$tpl->find("datalist")->attr("data-wb-from",json_encode($opt["datalist"],JSON_UNESCAPED_UNICODE));
				$tpl->find("datalist")->attr("data-wb-role","foreach");
			} else {$tpl->find("datalist")->remove();}
			break;
        case "enum":
            if (substr($param["value"],0,2)=="[{") {
                $arr=json_encode($param["value"],true);
            } else {
                $arr=explode(";",$param["value"]);
                foreach($arr as $i => $line) {
                    unset($arr[$i]); $arr[$line]=array("id"=>$line,"name"=>$line);
                }
            }
            $param["enum"]=$arr; unset($param["value"]);
            break;
        case "image":
            $tpl->wbSetValues($param);
            $tpl->wbSetData($data);
            break;
				case "gallery":
            $tpl->wbSetValues($param);
            $tpl->wbSetData($data);
						$tpl->find(".wb-uploader")->attr("data-wb-path","/uploads/{$data['_form']}/{$data['_item']}/");
            break;
	}
	$set->find(".form-group > label")->html($param["label"]);
	$set->find(".form-group > div")->html($tpl->outerHtml());
	$set->wbSetData($param);
    $set->wbSetValues($data);
    return $set->outerHtml();
}

function wbInitDatabase() {
	wbTrigger("func",__FUNCTION__,"before");
	if (!is_dir($_ENV["dbe"])) {mkdir($_ENV["dbe"],0777);}
	if (!is_dir($_ENV["dba"])) {mkdir($_ENV["dba"],0777);}
	if (!is_dir($_ENV["dbec"])) {mkdir($_ENV["dbec"],0777);}
	if (!is_dir($_ENV["dbac"])) {mkdir($_ENV["dbac"],0777);}

}

function wbFlushDatabase() {
	wbTrigger("func",__FUNCTION__,"before");
	$etables=wbTableList(true);
	$atables=wbTableList();
	foreach($etables as $key) {wbTableFlush($_ENV["dbe"]."/".$key);}
	foreach($atables as $key) {wbTableFlush($_ENV["dba"]."/".$key);}
}

function wbTable($table="data",$engine=false) {
	wbTrigger("func",__FUNCTION__,"before");
	if ($engine==false) {$db=$_ENV["dba"];} else {$_ENV["dbe"];}
	$tname=$table;
	$table=wbTablePath($table,$engine);
	$_ENV[$table]["name"]=$tname;
    if (!is_file($table)) {
        if ($tname>"" AND in_array($tname,$_ENV["forms"])) {
            wbTableCreate($tname);
        }
    }
    if (!is_file($table)) {
        wbError("func",__FUNCTION__,1001,func_get_args());
        $table=null;
	} else {
		$_ENV["cache"][$table]=json_decode(file_get_contents($table),true);
	}
	wbTrigger("func",__FUNCTION__,"after",func_get_args(), $table);
	return $table;
}

function wbTableCreate($table="data",$engine=false) {
	wbTrigger("func",__FUNCTION__,"before");
	if ($engine==false) {$db=$_ENV["dba"];} else {$db=$_ENV["dbe"];}
	$table=wbTablePath($table,$engine);
	if (!is_file($table) AND is_dir($db)) {
		$json=json_encode(array("dict"=>array(),"data"=>array(),"view"=>array()), JSON_HEX_QUOT | JSON_HEX_APOS);
		$res=file_put_contents($table,$json, LOCK_EX);
		if ($res) {chmod($table,0777);} else {$table=null;}
	} else {
		wbError("func",__FUNCTION__,1002,func_get_args());
	}
	wbTrigger("func",__FUNCTION__,"after",func_get_args(), $table);
	return $table;
}


function wbTableRemove($table=null,$engine=false) {

	$cache=wbTableCachePath($table,$engine);
	$table=wbTablePath($table,$engine);
	wbRrecurseDelete($cache);
	if (is_file($table)) {
		wbRrecurseDelete($cache);
		unlink($table);
		if (is_file($table)) { // не удалилось
			wbError("func",__FUNCTION__,1003,func_get_args());
		}
	} else { // не существует
			wbError("func",__FUNCTION__,1001,func_get_args());
	}

	return $table;
}

function wbTablePath($table="data",$engine=false) {

	if ($engine==false) {$db=$_ENV["dba"];} else {$_ENV["dbe"];}
	$table=$db."/".$table;
	wbTrigger("func",__FUNCTION__,"after",func_get_args(),$table);
	return $table;
}

function wbTableCachePath($table="data",$engine=false) {
	if ($engine==false) {$db=$_ENV["dbac"];} else {$_ENV["dbec"];}
	$table=$db."/".$table;
	wbTrigger("func",__FUNCTION__,"after",func_get_args(),$table);
	return $table;
}

function wbTableList($engine=false) {
		if ($engine==false) {$db=$_ENV["dba"];} else {$db=$_ENV["dbe"];}
	$list=wbListFiles($db);
	wbTrigger("func",__FUNCTION__,"after",func_get_args(),$list);
	return $list;
}

function wbListItems($table="data",$where=null) {return wbItemList($table,$where);}
function wbItemList($table="data",$where="") {
	if (count(explode($_ENV["path_app"],$table))==1) {$table=wbTable($table);}
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
		array_walk($list,function(&$item,$key,$args){
			$item=wbTrigger("form",__FUNCTION__,"AfterItemRead",$args,$item);
		},func_get_args());

		$object = new ArrayObject($list);
		foreach($object as $key => $item) {
        if (substr($item["id"],0,1)=="_" AND $_SESSION["user_role"]!=="admin") {unset($list[$key]);} elseif (!wbWhereItem($item,$where)) {unset($list[$key]);}
		}


	}
	$list=wbTrigger("form",__FUNCTION__,"AfterItemList",func_get_args(),$list);
	$list=wbTrigger("func",__FUNCTION__,"after",func_get_args(),$list);
	return $list;
}

function wbTreeRead($name) {
	wbTrigger("form",__FUNCTION__,"BeforeTreeRead",func_get_args(),array());
    $tree=wbItemRead("tree",$name);
	if (!isset($tree["tree"])) {$tree["tree"]=array();}  else {
		$tree["tree"]=json_decode($tree["tree"],true);
	}
	if (!isset($tree["_tree__dict_"])) {$tree["dict"]=array();}  else {
		$tree["dict"]=json_decode($tree["_tree__dict_"],true);
	}
    $tree=wbTrigger("form",__FUNCTION__,"AfterTreeRead",func_get_args(),$tree);
	return $tree;
}

function wbTreeFindBranchById($Item,$id) {
	$res=false;
		foreach($Item as $item) {
			if ($item["id"]==$id) {
				return $item;
			} else {
				if (is_array($item["children"])) {
					$res=wbTreeFindBranchById($item["children"],$id);
					if ($res) return $res;
				}
			}
		}
		return $res;
}


function wbTreeWhere($tree,$id,$field,$inc=true) {
	if (!is_array($tree)) {$tree=wbTreeRead($tree); $tree_id=$tree["id"]; $tree=$tree["tree"];} else {$tree_id=$tree["id"];}
	$tree=wbTreeFindBranchById($tree,$id);
	$cache_id=md5($tree_id.$id.$field.$inc);
	if (isset($_ENV["cache"][__FUNCTION__][$cache_id])) {
		return $_ENV["cache"][__FUNCTION__][$cache_id];
	} else {
		$list=wbTreeIdList($tree);
		$where="";
		foreach($list as $key => $val) {
			if ($key==0) {$where.='"'.$val.'"';} else {$where.=',"'.$val.'"';}
		}
		$where="in_array({$field},array({$where}))";
		$_ENV["cache"][__FUNCTION__][$cache_id]=$where;
		return $_ENV["cache"][__FUNCTION__][$cache_id];
	}
}

function wbTreeIdList($tree,$list=array()) {
		if (isset($tree["id"])) {$list[]=$tree["id"];}
		if (isset($tree["children"])) {
			foreach($tree["children"] as $key =>  $child) {
				$list=wbTreeIdList($child,$list);
			}
		}
	return $list;
}

function wbWhereLike($ref,$val) {
	if (is_array($ref)) {
		$ref=implode("|",$ref);
	} else {
		$val=trim($val);
		$val=str_replace(" ","|",$val);
	}
	$res=preg_match("/{$val}/ui",$ref);
	return $res;
}


function wbWhereNotLike($ref,$val) {
	if (is_array($ref)) {
		$ref=implode("|",$ref);
	} else {
		$val=trim($val);
		$val=str_replace(" ","|",$val);
	}
	$res=preg_match("/{$val}/ui",$ref);
	if ($res==1) {$res=0;} else {$res=1;}
	return $res;
}

function wbJsonEncode($Item=array()) {
    return json_encode($Item, JSON_HEX_QUOT | JSON_HEX_APOS | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
}

function wbItemRead($table=null,$id=null) {
	if ($table==null) {$table=$_ENV["route"]["form"];}
	if ($id==null) {$id=$_ENV["route"]["item"];}
  wbTrigger("form",__FUNCTION__,"BeforeItemRead",func_get_args(),array());
	if (count(explode($_ENV["path_app"],$table))==1) {$table=wbTable($table);}
	$cache=wbCacheName($table,$id);
	if (is_file($cache)) {
		$item=json_decode(file_get_contents($cache),true);
	} else {
		$list=wbItemList($table);
		if (isset($list[$id])) {
			$item=$list[$id];
			file_put_contents($cache,wbJsonEncode($item), LOCK_EX);
		} else {
			wbError("func",__FUNCTION__,1006,func_get_args());
			$item=null;
		}
	}
	if (is_array($item) AND isset($item["__wbFlag__"])) { // если стоит флаг удаления, то возвращаем null
		if ($item["__wbFlag__"]=="remove") {$item=null;}
	} else {
		$item=wbTrigger("form",__FUNCTION__,"AfterItemRead",func_get_args(),$item);
	}
	return $item;
}

function wbCacheName($table,$id=null) {
	$tmp=explode($_ENV["dbe"],$table);
	if (count($tmp)==2) {$dbc=$_ENV["dbec"];$db=$_ENV["dbe"];} else {$dbc=$_ENV["dbac"];$db=$_ENV["dba"];}
	$tname=str_replace($db."/","",$table);
    if (!is_dir($db)) {mkdir($db,0777);}
    if (!is_dir($dbc)) {mkdir($dbc,0777);}
    if (!is_dir($dbc."/".$tname)) {mkdir($dbc."/".$tname,0777);}
	if ($id==null) {$cache=$cache=$dbc."/".$tname;} else {$cache=$dbc."/".$tname."/".$id;}
	return $cache;
}

function wbItemRemove($table=null,$id=null,$flush=true) {
    wbTrigger("form",__FUNCTION__,"BeforeItemRemove",func_get_args(),array());

	if (count(explode($_ENV["path_app"],$table))==1) {$table=wbTable($table);}
	if (!is_file($table)) {
		wbError("func",__FUNCTION__,1001,func_get_args());
		return null;
	} else {
		$cache=wbCacheName($table,$id);
		if ($id!==null) {
			$item=wbItemRead($table,$id);
			if (is_array($item) && $table!=="users" && !isset($item["super"]) && $item["super"]!=="on") {$item["__wbFlag__"]="remove";}
			$item=json_encode($item, JSON_HEX_QUOT | JSON_HEX_APOS | JSON_UNESCAPED_UNICODE);
		}
		if (!is_dir($cache) AND $item!==null) {$res=file_put_contents($cache,$item, LOCK_EX);}
		if (!$res) {wbError("func",__FUNCTION__,1007,func_get_args());}
	}
	if ($flush==true) {wbTableFlush($table);}

    wbTrigger("form",__FUNCTION__,"AfterItemRemove",func_get_args(),array());
}

function wbItemSave($table,$item=null,$flush=true) {
	if (count(explode($_ENV["path_app"],$table))==1) {$table=wbTable($table);}
	$res=null;
	if (!is_file($table)) {
		wbError("func",__FUNCTION__,1001,func_get_args());
		return null;
	} else {
		if (!isset($item["id"]) OR $item["id"]=="_new") {$item["id"]=wbNewId(); $prev=null;} else {$prev=wbItemRead($table,$item["id"]);}
		$item=wbItemSetTable($table,$item);
		$cache=wbCacheName($table,$item["id"]);
		if ($prev!==null) {$item=array_merge($prev,$item);}
        $item=wbTrigger("form",__FUNCTION__,"BeforeItemSave",func_get_args(),$item);
		if (!is_dir($cache)) {$res=file_put_contents($cache,json_encode($item, JSON_HEX_QUOT | JSON_HEX_APOS | JSON_UNESCAPED_UNICODE), LOCK_EX);}
		if (!$res) {wbError("func",__FUNCTION__,1007,func_get_args());} else {
            wbTrigger("form",__FUNCTION__,"AfterItemSave",func_get_args(),$item);
        }
	}
	if ($flush==true) {wbTableFlush($table);}
	return $res;
}

function wbItemSetTable($table,$item=null) {
	$tablename=explode("/",$table);
	$item["_table"]=$tablename[count($tablename)-1];
    if (!isset($item["_created"]) OR $item["_created"]=="") $item["_created"]=date("Y-m-d H:i:s");
    if (!isset($item["_creator"]) OR $item["_creator"]=="") $item["_creator"]=$_SESSION["user_id"];
    $item["_lastdate"]=date("Y-m-d H:i:s");
    $item["_lastuser"]=$_SESSION["user_id"];
	return $item;
}

function wbTableFlush($table) {
	// Сброс кэша в общий файл
	$cache=wbCacheName($table);
	$clist=wbListFiles($cache);
	$data=json_decode(file_get_contents($table),true);
	if (!isset($data["data"])) {$data["data"]=array();}
	foreach($clist as $key) {
		$data["data"][$key]=$item=json_decode(file_get_contents($cache."/".$key),true);
		if (isset($item["__wbFlag__"]) AND $item["__wbFlag__"]=="remove") {
			unset($data["data"][$key]);
		}
		unlink($cache."/".$key);
	}
	$_ENV["cache"][$table]=$data;
	$data=json_encode($data, JSON_HEX_QUOT | JSON_HEX_APOS | JSON_UNESCAPED_UNICODE);
	$res=file_put_contents($table,$data, LOCK_EX);
	return $res;
}

function wbTrigger($type,$name,$trigger,$args=null,$data=null) {
	if (!isset($_ENV["error"][$type])) {$_ENV["error"][$type]=array();}
	switch ($type) {
		case "form":
			if (isset($_ENV[$args[0]]["name"])) {
				$call=$_ENV[$args[0]]["name"].$trigger;
				if (is_callable($call)) {$data=$call($data);} else {$call="_".$call; if (is_callable($call)) {$data=$call($data);}}
			}
			return $data;
			break;
		case "func":
			if ($trigger=="before") wbError($type,$name,null);
			break;
		default:
			break;
	}
	return $data;
}

function wbError($type,$name,$error="__return__error__",$args=null) {
	if ($error==null) {
		if (isset($_ENV["error"][$type][$name])) {unset($_ENV["error"][$type][$name]);}
	} else {
		if ($error=="__return__error__") {$error=$_ENV["error"][$type][$name];} else {
            if (isset($_ENV["errors"])) {
			     $_ENV["error"][$type][$name]=array("errno"=>$error,"error"=>$_ENV["errors"][$error]);
            } else {
                $_ENV["error"][$type][$name]=array("errno"=>$error,"error"=>"unknown error");
            }
			wbLog($type,$name,$error,$args);
		}
	}
	return $error;
}

function wbErrorOut($error) {
	echo $_ENV["errors"][$error];
}

function wbGetTpl($tpl=null,$path=false) {
	$out=null;
	if ($path==true) {
		if (!$out AND is_file($_ENV["path_app"]."/{$tpl}")) {$out=wbFromFile($_ENV["path_app"]."/{$tpl}");}
	} else {
		if (!$out AND is_file($_ENV["path_app"]."/tpl/{$tpl}")) {$out=wbFromFile($_ENV["path_app"]."/tpl/{$tpl}");}
		if (!$out AND is_file($_ENV["path_engine"]."/tpl/{$tpl}")) {$out=wbFromFile($_ENV["path_engine"]."/tpl/{$tpl}");}
	}
	if ($out==null) wbErrorOut(wbError("func",__FUNCTION__,404,func_get_args()));
	return $out;
}

function wbLoopProtect($func) {
    if (!isset($_ENV["wbGetFormStack"])) {$_ENV["wbGetFormStack"]=array();}
    $_ENV["wbGetFormStack"][]=$func;
}

function wbGetForm($form=NULL,$mode=NULL,$engine=null) {
	$_ENV["error"][__FUNCTION__]="";
	if ($form==NULL) {$form=$_GET["form"];}
	if ($mode==NULL) {$mode=$_GET["mode"];}
    $aCall=$form."_".$mode; $eCall=$form."__".$mode;
    if (!isset($_ENV["wbGetFormStack"])) {$_ENV["wbGetFormStack"]=array();}
    if (is_callable($aCall) AND !in_array($aCall,$_ENV["wbGetFormStack"])) {
        $_ENV["wbGetFormStack"][]=$aCall;
        $out=$aCall();
    } elseif (is_callable($eCall) AND $engine!==false AND !in_array($eCall,$_ENV["wbGetFormStack"])) {
        $_ENV["wbGetFormStack"][]=$eCall;
        $out=$eCall();
    }

	if (!isset($out)) {
		$current=""; $flag=false;
		$path=array("/forms/{$form}_{$mode}.php","/forms/{$form}/{$form}_{$mode}.php","/forms/{$form}/{$mode}.php");
		foreach($path as $form) {
			if ($flag==false) {
				if (is_file($_ENV["path_engine"].$form)) {$current=$_ENV["path_engine"].$form; $flag=$engine;}
				if (is_file($_ENV["path_app"].$form) && $flag==false) {$current=$_ENV["path_app"].$form; $flag=true;}
			}
		}; unset($form);
		if ($current=="") {
			$common="{$_ENV["path_engine"]}/forms/common/common_{$mode}.php";
			if (is_file($common)) {
				$out=wbfromFile($common);
			} else {
				$out=wbFromString("<p class='alert alert-warning'>Error! Form not found.</p>");
				$_ENV["error"][__FUNCTION__]="noform";
			}
		} else {$out=wbfromFile($current);}
	}
	if (is_string($out)) {$out=wbFromString($out);}
	return $out;
}

function wbErrorList() {
	$_ENV["errors"]=array(
		404=>"Страница не существует",
		1001=>"Таблица {{0}} не существует",
		1002=>"Таблица {{0}} уже существует",
		1003=>"Не удалось удалить {{0}}",
		1004=>"He удалось заблокировать файл {{0}}",
		1005=>"Не удалось записать таблицу {{0}}",
		1006=>"Запись {{1}} в таблице {{0}} не существует",
		1007=>"Не удалось сохранить запись в таблицу {{0}}",
	);
}

function wbLog($type,$name,$error,$args) {
	$log = fopen($_ENV["path_app"]."/wblog.txt", "a");
	$error=wbError($type,$name);
	if (is_array($args)) {
		foreach($args as $key => $arg) {
			$error["error"]=str_replace("{{".$key."}}",$arg,$error["error"]);
		}
	}
	fwrite($log, date("d-m-Y H:i:s")." {$type} {$name} [{$error["errno"]}]: {$error["error"]}\n");


}

function wbNewId($separator="") {
	$mt=explode(" ",microtime());
	$md=substr(str_repeat("0",2).dechex(ceil($mt[0]*10000)),-4);
	$id=dechex(time()+rand(100,999)).$separator.$md;
	$_SESSION["newIdLast"]=$id;
	return $id;
}

function wbGetItemImg($Item=null,$idx=0,$noimg="",$imgfld="images",$visible=true) {
	$res=false; $count=0;
	if ($Item==null) {$Item=$_SESSION["Item"];}
	if (!is_file("{$_ENV["path_app"]}/{$noimg}")) {
		if (is_file("{$_ENV["path_engine"]}/uploads/__system/{$noimg}")) {
			$noimg="/engine/uploads/__system/{$noimg}";
		} else {
			$noimg="/engine/uploads/__system/image.jpg";
		}
	}
	$image=$noimg;

	if (isset($Item[$imgfld])) {
		if (!is_array($Item[$imgfld])) {$Item[$imgfld]=json_decode($Item[$imgfld],true);}
		if (!is_array($Item[$imgfld])) {$Item[$imgfld]=array();}
		foreach($Item[$imgfld] as $key => $img) {
			if (!isset($img["visible"])) {$img["visible"]=1;}

			if ($res==false AND (($visible==true AND $img["visible"]==1) OR $visible==false) AND is_file("{$_ENV["path_app"]}/uploads/{$Item["_table"]}/{$Item["id"]}/{$img["img"]}")) {
				if ($idx==$count) {
					$image="{$_ENV["path_app"]}/uploads/{$Item["_table"]}/{$Item["id"]}/{$img["img"]}"; $res=true;
				}
				$count++;
			}
		}; unset($img);
	}
	return urldecode($image);
}

function wbListFiles($dir) {
	$list=array();
	if ($dircont = scandir($dir)) {
           $i=0; $idx=0;
           while (isset($dircont[$i])) {
               if ($dircont[$i] !== '.' && $dircont[$i] !== '..') {
                   $current_file = "{$dir}/{$dircont[$i]}";
                   if (is_file($current_file)) {$list[] = "{$dircont[$i]}"; }
               }
               $i++;
           }
    }

	return $list;
}

function wbFileRemove($file) {
	$res=false;
	if (is_file($file)) {
		unlink($file);
		if (is_file($file)) {$res=false;} else {$res=true;}
	}
	return $res;
}

function wbPutContents($dir, $contents){
    $parts = explode('/', $dir);
    $file = array_pop($parts);
    $dir = '';
    foreach($parts as $part)
        if(!is_dir($dir .= "/$part")) mkdir($dir);
    return file_put_contents("$dir/$file", $contents);
}

function wbRecurseDelete($src) {
    $dir = opendir($src);
	if (is_resource($dir)) {
		while(false !== ( $file = readdir($dir)) ) {
			if (( $file !== '.' ) && ( $file !== '..' )) {
				if ( is_dir($src . '/' . $file) ) {
					wbRecurseDelete($src . '/' . $file);
				}
				else {
					unlink($src . '/' . $file);
				}
			}
		}
		closedir($dir);
		if (is_dir($src)) {rmdir($src);}
	}
}

function wbRecurseCopy($src,$dst) {
  $dir = opendir($src);
	if (is_resource($dir)) {
mkdir($dst);
		while(false !== ( $file = readdir($dir)) ) {
			if (( $file != '.' ) && ( $file != '..' )) {
				if ( is_dir($src . '/' . $file) ) {
					wbRecurseCopy($src . '/' . $file,$dst . '/' . $file);
					chmod($dst.'/'.$file,0777);
				} else {
					copy($src . '/' . $file,$dst . '/' . $file);
					chmod($dst.'/'.$file,0766);
				}
			}
		}
		closedir($dir);
	}
}

function wbQuery($sql) {
	require_once($_ENV["path_engine"]."/lib/sql/PHPSQLParser.php");

	$parser = new PHPSQLParser();
	$p = $parser->parse($sql);
	$sid=md5($sql);

	foreach($p as $r => $a) {
		foreach($a as $e) {wbt_route($sid,$e,$r);}
	}

	$table=array(); $join=array();
	foreach($_ENV["sql"][$sid]["FROM"] as $key => $t) {
		if (!isset($t["join"])) {
			// join пока не работает
			$table["name"]=$key;
			$table["table"]=$t["name"];
			$table["data"]=wbItemList(wbTable($t["name"]));
		} else {
			$join[$key]["name"]=$key;
			$join[$key]["data"]=wbItemList(wbTable($t["name"]));
			$join[$key]["join"]=$t["join"];
		}
	}
	if (isset($_ENV["sql"][$sid]["WHERE"])) {$where=wbt_where($table["name"],$_ENV["sql"][$sid]["WHERE"]);}

	$object = new ArrayObject($table["data"]);
	foreach($object as $key =>$item) {
		$call=$table["table"]."AfterReadItem"; if (is_callable($call)) {$item=$call($item);}
		$object[$key] = $item;
	}
	$iterator = new wbt_filter($object->getIterator(), "where", array("where"=>$where,"table"=>$table["name"],"join"=>$join));
	$table["data"]=iterator_to_array($iterator);
	$iterator->rewind();

	unset($_ENV["sql"][$sid]);

	return $table["data"];
}

function wbWhereItem($item,$where=NULL) {
	$where=htmlspecialchars_decode($where);
	$res=true;
	if (!$where==NULL) {
		if (substr($where,0,1)=="%") {$phpif=substr($where,1);} else {$phpif=wbWherePhp($where,$item);}
        if ($phpif>"") @eval('if ( '.$phpif.' ) { $res=1; } else { $res=0; } ;');
	};
	return $res;
}


function wbWherePhp($str="",$item=array()) {
	$str=" ".trim(strtr($str,array(
					"("=>" ( ",
					")"=>" ) ",
					"="=>" == ",
					">"=>" > ",
					"<"=>" < ",
					">="=>" >= ",
					"<="=>" <= ",
					"<>"=>" !== ",
                    "!="=>" !== ",
                    "!=="=>" !== ",
                    "#"=>" !== ",
                    "=="=>" == ",
	)))." ";
	$exclude=array("AND","OR","LIKE","NOT_LIKE","IN_ARRAY");
	  $str=wbSetValuesStr($str,$item);
    preg_match_all('/\w+(?!\")\b/iu',$str,$arr); // возникают ошибки если в тексте пробел
		$flag=true;
    foreach($arr[0] as $a => $fld) {
        if (!in_array(strtoupper($fld),$exclude)) {
            if (isset($item[$fld]) AND $flag==true) {
                $str=str_replace(" {$fld} ",' $item["'.$fld.'"] ',$str);
								$flag=false;
            }
        } else {
					$flag=true;
				}
    }

	preg_match_all('/in_array\s\(\s(.*),array \(/',$str,$arr);
	foreach($arr[1] as $a => $fld) {
		$str=str_replace("in_array ( {$fld},array (", 'in_array ($item["'.$fld.'"],array(',$str);
	}

	if (strpos(strtolower($str)," like ")) {
		preg_match_all('/\S*\slike\s\S*/iu',$str,$arr);
		foreach($arr[0] as $a => $cls) {
			$tmp=explode(" like ",$cls);
			if (count($tmp)==2) {
				$str=str_replace($cls,'wbWhereLike('.$tmp[0].','.$tmp[1].')',$str);
			}
		}
	}

	if (strpos(strtolower($str)," not_like ")) {
		preg_match_all('/\S*\snot_like\s\S*/iu',$str,$arr);
		foreach($arr[0] as $a => $cls) {
			$tmp=explode(" not_like ",$cls);
			if (count($tmp)==2) {
				$str=str_replace($cls,'wbWhereNotLike('.$tmp[0].','.$tmp[1].')',$str);
			}
		}
	}
	return $str;
}


function wbt_where($table,$where) {
		$where=htmlspecialchars_decode($where);
		foreach($where as $key => $val) {
			if (mb_substr($val,0,1)=="$") {
				$tmp=explode(".",$val);
				if (count($tmp)==2) {
					$val="$".mb_substr($tmp[0],1).'["'.$tmp[1].'"]';
				} else {
					$val="$".$table.'["'.mb_substr($tmp[0],1).'"]';
				}
				$where[$key]=$val;
			}
		}
		$res=implode(" ",$where);
	return $res;
}


function wbt_route($sid,$e,$r="TABLE") {
	if (!isset($_ENV["sql"][$sid][$r])) {$_ENV["sql"][$sid][$r]=array();}
	$t=&$_ENV["sql"][$sid][$r];
	if (isset($_ENV["sql"][$sid]["JOIN"])) {$t=&$_ENV["sql"][$sid]["FROM"][$_ENV["sql"][$sid]["JOIN"]]["join"];}
	if (isset($_ENV["sql"][$sid]["EXPR"])) {$t=&$_ENV["sql"][$sid]["EXPR"];}
	switch($e["expr_type"]) {
		case "table":
			if (is_array($e["alias"])) {$key=$e["alias"]["no_quotes"];} else {$key=$e["no_quotes"];}
			$t[$key]["name"]=$e["no_quotes"];
			if (isset($e["join_type"]) AND isset($e["ref_clause"]) AND is_array($e["ref_clause"]) AND $e["join_type"]=="JOIN") {
				$_ENV["sql"][$sid]["JOIN"]=$key;
				foreach($e["ref_clause"] as $s) {wbt_route($sid,$s,"WHERE");}
				unset($_ENV["sql"][$sid]["JOIN"]);
			}
			break;
		case "expression":
			$_ENV["sql"][$sid]["EXPR"]=array();
			if (isset($e["sub_tree"])) {foreach($e["sub_tree"] as $s) {wbt_route($sid,$s,"WHERE");}}
			if (isset($e["alias"]) AND $e["alias"]["no_quotes"]>"") {
				$t[$e["alias"]["no_quotes"]]=implode(" ",$_ENV["sql"][$sid]["EXPR"]);
			} else {
				$t[]=implode(" ",$_ENV["sql"][$sid]["EXPR"]);
			}
			unset($_ENV["sql"][$sid]["EXPR"]);
			break;
		case "colref":
			if (in_array($r,array("WHERE","TABLE","SELECT"))) {$t[]="$".$e["no_quotes"];}
			if (is_array($e["sub_tree"])) {foreach($e["sub_tree"] as $s) {wbt_route($sid,$s,$r);}}
			break;
		case "operator":
			$op=strtr(mb_strtoupper($e["base_expr"]),array(
					"<="=>"<=",
					">="=>">=",
					"="=>"==",
					"<>"=>"!==",
					"NOT"=>"!=="
			));
			$t[]=$op;
			break;
		case "const":
			if (in_array($r,array("WHERE","TABLE","SELECT"))) {$t[]=$e["base_expr"];}
			break;
		case "bracket_expression":
			if (in_array($r,array("WHERE","TABLE","SELECT"))) {
				$t[]="(";
				if (isset($e["sub_tree"])) {foreach($e["sub_tree"] as $s) {wbt_route($sid,$s,$r);}}
				$t[]=")";
			}
			break;

	}
}

class wbt_filter extends FilterIterator {
    private $userFilter;
    private $variable;
    private $join;
    private $table;
    private $type;
    private $data;
    public function __construct(Iterator $iterator, $type, $data) {
        parent::__construct($iterator);
        //$tname, $filter, $join=null
        $this->type = $type;
		switch($type) {
			case "where":
				$this->userFilter = $data["where"];
				$this->table = $data["table"];
				$this->join = $data["join"];
				break;
		}
    }

    public function accept() {
		$item=$this->getInnerIterator()->current();
		switch($this->type) {
			case "where":
				eval('$'.$this->table.' = $item;');
				break;
		}
        eval('if ( '.$this->userFilter.' ) { $res=1; } else { $res=0; } ;');
        if( $res == 0) {return false;}
        return true;
    }
}

function wbRouterAdd($route=null, $destination=null) {
	if ($route==null) { // Роутинг по-умолчанию
			$route=wbRouterRead();
	}
	wbRouter::addRoute($route,$destination);
}

function wbRouterRead($file=null) {
	if ($file==null) {
		$file=$_ENV["path_engine"]."/router.ini";
		$route=wbRouterRead($file);
		if (is_file($_ENV["path_app"]."/router.ini")) {$route=array_merge(wbRouterRead($_ENV["path_app"]."/router.ini"),$route);}
	} else {
		if (is_file($file)) {
			$route=array();
			$router=new ArrayIterator(file($file));
			foreach($router as $key => $r) {
				$r=explode("=>",$r);
				if (count($r)==2) $route[trim($r[0])]=trim($r[1]);
			}
		}
	}
	return $route;
}

function wbRouterGet($requestedUrl = null) {
	return wbRouter::getRoute($requestedUrl);
}

function wbLoadController() {
	if (isset($_ENV["route"]["controller"])) {
		$path="/controllers/".$_ENV["route"]["controller"].".php";
		if (is_file($_ENV["path_app"] . $path)) {
			include_once($_ENV["path_app"] . $path);
			$call=$_ENV["route"]["controller"]."_controller";
			return @$call(array($__page,$Item));
		} else {
			if (is_file(__DIR__ . $path)) {
				include_once(__DIR__ . $path);
				$call=$_ENV["route"]["controller"]."__controller";
				return @$call();
			} else {
				echo "Ошибка загрузки контроллера: {$_ENV["route"]["controller"]}";
				die;
			}
		}
	}
}

function wbFromString($str="") {
	return ki::fromString($str);
}

function wbFromFile($str="") {
	return ki::fromFile($str);
}

function wbAttrToArray($attr) {
	return wbArrayAttr($attr);
}

function wbAttrAddData($data,$Item,$mode=FALSE) {
	$data=stripcslashes(html_entity_decode($data));
	$data=json_decode($data,true);
	if (!is_array($Item)) {$Item=array($Item);}
	if ($mode==FALSE) $Item=array_merge($data,$Item);
	if ($mode==TRUE) $Item=array_merge($Item,$data);
	return $Item;
}

function wbGetWords($str,$w) {
	$res="";
	$arr=explode(" ",trim($str));
	for ($i=0; $i<=$w; $i++) {
		if (isset($arr[$i])) $res=$res." ".$arr[$i];
	}
	if (count($arr)>$w) {$res=$res."...";}
	$res=trim($res);
	return $res;
}

function wbSetValuesStr($tag="",$Item=array(), $limit=2)
{
	if (is_object($tag)) {$tag=$tag->outerHtml();}
	if (!is_array($Item)) {$Item=array($Item);}
    $Item=wbItemToArray($Item);
    // Обработка для доступа к полям с JSON имеющим id в содержании, в частности к tree
    $arr=new ArrayIterator($Item);
    $Item=array();
    $flag=false;
    foreach($arr as $key => $item) {
        if (!is_array($item) AND (substr(trim($item),0,1)=="[" OR substr(trim($item),0,1)=="{")) {
            $item=json_decode($item,true);
                foreach($item as $k => $a) {
                    if (isset($a["id"]) AND $a["id"]>"" AND $key!==$a["id"]) {
                        $flag=true;
                        $Item[$key][$a["id"]]=$a;
                    }
                }
        } else {$Item[$key]=$item;}
    }
    //if ($flag) print_r($Item);
    // ================ Конец обработки ======================
	if (is_string($tag)) {
	//$tag=strtr($tag,array("%7B%7B"=>"{{","%7D%7D"=>"}}"));
	$tag=str_replace(array("%7B%7B","%7D%7D"),array("{{","}}"),$tag);
	if (strpos($tag, '}}') !== false ) {
		// функция подставляющая значения
		$tag = wbChangeQuot($tag);			// заменяем &quot на "
		$spec = array('_form', '_mode', '_item', '_id');
		$exit = false;
		$err = false;
		$nIter = 0;
		$_FUNC="";
		$mask = '`(\{\{){1,1}(%*[\w\d]+|_form|_mode|_item|((_SETT|_SETTINGS|_SESS|_SESSION|_VAR|_SRV|_COOK|_COOKIE|_FUNC|_ENV|_REQ|_GET|_POST|%*[\w\d]+)?([\[]{1,1}(%*[\w\d]+|"%*[\w\d]+")[\]]{1,1})*))(\}\}){1,1}`u';
		while(!$exit) {
			$nUndef = 0;
			$nSub = preg_match_all($mask, $tag, $res, PREG_OFFSET_CAPTURE);				// найти все вставки, не содержащие в себе других вставок
			if ($nSub !== false) {
				if ($nSub == 0) {$exit = true;} else {
					$text = '';
					$startIn = 0;		// начальная позиция текста за предыдущей заменой
					for ($i = 0; $i < $nSub; $i++)		// замена в исходном тексте найденных подстановок
					{
						$In = $res[2][$i][0];						// текст вставки без скобок {{ и }}
						$beforSize = $res[2][$i][1] - 2 - $startIn;
						$text .= substr($tag, $startIn, $beforSize);		// исходный текст между предыдущей и текущей вставками
						$default = false;
						$special = 0;
						switch($res[4][$i][0])					// префикс вставки
						{
							case '_SETT':
								$sub = '$_ENV["settings"]';
								break;
							case '_SETTINGS':
								$sub = '$_ENV["settings"]';
								break;
							case '_VAR':
								$sub = '$_ENV["variables"]';
								break;
							case '_SESS':
								$sub = '$_SESSION';
								break;
							case '_SESSION':
								$sub = '$_SESSION';
								break;
							case '_COOK':
								$sub = '$_COOKIE';
								break;
							case '_COOKIE':
								$sub = '$_COOKIE';
								break;
							case '_REQ':
								$sub = '$_REQUEST';
								break;
							case '_GET':
								$sub = '$_GET';
								break;
							case '_ENV':
								$sub = '$_ENV';
								break;
							case '_FUNC':
								// нужно придумать вызов функций
								break;
							case '_SRV':
								$sub = '$_SERVER';
								break;
							case '_POST':
								$sub = '$_POST';
								break;
							case '':
								if(in_array($In, $spec))
								{
									$sub = '$_GET';
									$In = substr($In, 1, strlen($In) - 1);		// убираем символ _ в начале
									if (!isset($_GET["item"]) and ($In == 'item')) $In = 'id';
									if (!isset($_GET["id"]) and ($In == 'id')) $In = 'item';
								} else
								{
									$sub = '$Item';
								}
								break;
							default:									// 1ый индекс без скобок [] - префикса нет
								$sub = '$Item';
								$default = true;
								$n = strlen($res[4][$i][0]);
								$In = '[' . substr($In, 0, $n) . ']' . substr($In, $n, strlen($In) - $n);
								break;
						}
						if ($default)
						{
							$pos = 0;
						} else
						{
							$pos = strlen($res[4][$i][0]);
						}
						$sub .= wbSetQuotes(substr($In, $pos, strlen($In) - $pos));		// индексная часть текущей вставки с добавленными кавычками у текстовых индексов
						if (eval('return isset(' . $sub . ');'))
						{
							if (eval('return is_array(' . $sub . ');'))
							{
								$text .= eval('return json_encode(' . $sub . ');');
							} else
							{
								$temp="";
								eval('$temp .= ' . $sub . ';');
								$temp=strtr($temp,array("{{"=>"#~#~","}}"=>"~#~#"));
								$text.=$temp;
							}
						} else {
							/*
							$skip=array("_GET","_POST","_COOK","_COOKIE","_SESS","_SESSION","_SETT","_SETTINGS");
							$tmp=explode("[",$res[2][$i][0]); $tmp=$tmp[0];
							if (in_array($tmp,$skip)) {
								$text.="";
							} else {
								$text .= '{{' . $res[2][$i][0] . '}}';;
							}
							*/
							$text.="";
							$nUndef++;
						}
						$startIn += $beforSize + strlen($res[2][$i][0]) + 4;
						if ($i+1 == $nSub)		// это была последняя вставка
						{
							$text .= substr($tag,  $startIn, strlen($tag) - $startIn);
						}
					}
					$tag = $text;
				}
			}
			$nIter++;
			if ($limit > 0 and $nIter == $limit) $exit = true;
			if ($nUndef == $nSub) $exit = true;
		}
			if (isset($_GET["mode"]) && $_GET["mode"]=="edit") {
				$tag=ki::fromString($tag);
				foreach($tag->find("pre") as $pre) {
					$pre->html(htmlspecialchars($pre->html()));
				}; unset($pre);
				$tag=$tag->htmlOuter();
			}
	}
	$tag=strtr($tag,array("#~#~"=>"{{","~#~#"=>"}}"));
	return $tag;
	}
}

// добавление кавычек к нечисловым индексам
function wbSetQuotes($In) {
	$err = false;
	$mask = '`\[(%*[\w\d]+)\]`u';
	$nBrackets = preg_match_all($mask, $In, $res, PREG_OFFSET_CAPTURE);				// найти индексы без кавычек
	if ($nBrackets !== false) {
		if ($nBrackets == 0) {
			if (substr($In, 0, 2) != '["') {
				if (!is_numeric($In)) $In = '"' . $In . '"';
				$In = '[' . $In . ']';
			}
		} else {
			for ($i = 0; $i < $nBrackets; $i++) {
				if (!is_numeric($res[1][$i][0])) $In = str_replace('['.$res[1][$i][0].']', '["'.$res[1][$i][0].'"]', $In);
			}
		}
	}
	return $In;
}

// заменяем &quot на "
function wbChangeQuot($Tag) {
	$mask = '`&quot[^;]`u';

	if (is_string($Tag)) {
		$nQuot = preg_match_all($mask, $Tag, $res, PREG_OFFSET_CAPTURE);				// найти &quot без последеующего ;
		if ($nQuot !== false) {
			if ($nQuot == 0) {$In = $Tag;} else {
				$In = '';
				$startIn = 0;		// начальная позиция текста за предыдущей заменой
				for ($i = 0; $i < $nQuot; $i++) {
					$beforSize = $res[0][$i][1] - $startIn;
					$In .= substr($Tag, $startIn, $beforSize) . '"';		// исходный текст между предыдущей и текущей &quot
					$startIn += $beforSize + 5;
					if ($i+1 == $nQuot)		// это была последняя &quot
					{
						$In .= substr($Tag,  $startIn, strlen($Tag) - $startIn);
					}
				}
			}
		}
	}
	return $In;
}

function wbRole($role,$userId=null) {
	$res=false;
	if ($userId==null) {
		if (in_array($role,$_SESSION["user_role"])) {$res=true;}
	} else {
		$user=wbReadItem("users",$userId);
		if (in_array($role,$user["role"])) {$res=true;}
	}

}

	function wbControls($set="") {
		$res="*";
		$controls="[data-wb-role]";
		$allow="[data-wb-allow],[data-wb-disallow],[data-wb-disabled],[data-wb-enabled],[data-wb-readonly],[data-wb-writable]";
		$target="[data-wb-prepend],[data-wb-append],[data-wb-before],[data-wb-after],[data-wb-html],[data-wb-replace]";
		$tags=array("module","formdata","foreach", "dict", "tree","gallery",
					"include","imageloader","thumbnail","uploader",
					"multiinput","where","cart","variable"
		);
		if ($set!="") {$res=$$set;} else {$res="{$controls},{$allow},{$target}";}
		unset($controls,$allow,$target);
		return $res;
	}

function wbCartAction() {
    if (!isset($_SESSION["order_id"]) OR $_SESSION["order_id"]=="") {$_SESSION["order_id"]=wbNewId();$new=true;} else {$new=false;}
    $param=wbCartParam();
    $order=wbItemRead("orders",$_SESSION["order_id"]);
    if (!isset($order["id"]) OR $new=true) {
        $order["id"]=$_SESSION["order_id"];
        $order["user_id"]=$_SESSION["user_id"];
        $order["date"]=date("Y-m-d H:i:s");
    }


	switch($param["action"]) {
		case "add-to-cart":       wbCartItemAdd($order); break;
        case "cart-update":       wbCartUpdate($order); break;
		case "cart-item-recalc":  wbCartItemRecalc($order); break;
		case "cart-item-remove":  wbCartItemRemove($order); break;
		case "cart-clear":        wbCartClear($order); break;
	}
    return $_SESSION["order_id"];
}

function wbCartUpdate($order) {
    $order["items"]=$_POST;
    $order["total"]=wbCartCalcTotal($order);
    wbItemSave("orders",$order);
}

function wbCartParam() {
    $param=array(
    "mode"  =>$_ENV["route"]["mode"],
    "action"=>$_ENV["route"]["params"][0]
    );
    $param=array_merge($param,$_POST);
    return $param;
}

function wbCartClear($order) {
	$order["items"]=array();
	$order["total"]=0;
    $order["lines"]=0;
	wbItemSave("orders",$order);
}

function wbCartItemAdd($order) {
    $param=wbCartParam();
    if ($param["item"]>"" AND $param["count"]>"") {
		$pos=wbCartItemPos($order);
        $line=$param;
        unset($line["mode"],$line["action"]);
        $order["items"][$pos]=$line;
		$order["total"]=wbCartCalcTotal($order);
		wbItemSave("orders",$order);
	}
}

function wbCartCalcTotal($order) {
	$order["total"]=0;
	foreach($order["items"] as $item) {
		$order["total"]+=$item["count"]*$item["price"];
	}; unset($item);
	return $order["total"];
}

function wbCartItemPos($order) {
    $param=wbCartParam();
	if (!isset($order["items"])) {$order["items"]=array();}
    $pos=0;
	foreach($order["items"] as $key => $Item) {
		if ($Item["form"]==$param["form"] AND $Item["item"]==$param["item"]) { return $pos;} else {$pos++;}
	}
	return $pos;
}


function wbCartItemPosCheck($Item) {
	$res=true;
    $param=wbCartParam();
	foreach($param as $k => $fld) {
		if ( $Item[$fld]!=$_GET[$fld] ) {$res=false;}
	}
	return $res;
}

function wbListForms($exclude=true) {
	if ($exclude==true) {$exclude=array("forms/common","forms/admin","forms/source","forms/snippets");}
	elseif (!is_array($exclude)) {$exclude=array("forms/snippets");}
	$list=array();
	$eList=wbListFilesRecursive($_ENV["path_engine"] ."/forms",true);
	$aList=wbListFilesRecursive($_ENV["path_app"] ."/forms",true);
	$arr=$eList;
	foreach($aList as $a) {$arr[]=$a;}
	unset($eList,$aList);
	foreach($arr as $i => $data) {
			$name=$data["file"];
			$path=$data["path"];
			$path=str_replace(array($_ENV["path_engine"],$_ENV["path_app"]),array(".","."),$path);
			$inc=strpos($name,".inc");
			$ext=explode(".",$name); $ext=$ext[count($ext)-1];
			$name=substr($name,0,-(strlen($ext)+1));
			$name=explode("_",$name); $name=$name[0];
			foreach($exclude as $exc) {
			if (!strpos($path,$exc)) {$flag=true;} else {$flag=false;} }
			if (($ext=="php" OR $ext=="htm") && !$inc && $flag==true && $name>"" && !in_array($name,$list)) {
				$list[]=$name;
			}
	}
	unset($arr);
	//$merchE=wbCheckoutForms(true);
	//$merchA=wbCheckoutForms();
	//foreach($merchE as $m) {if (in_array($m["name"],$list)) {unset($list[array_search($m["name"],$list)]);}}
	//foreach($merchA as $m) {if (in_array($m["name"],$list)) {unset($list[array_search($m["name"],$list)]);}}
	if (in_array("form",$list)) {unset($list[array_search("form",$list)]);}
	return $list;
}

function wbListFormsFull() {
	$list=array();
	$types=array("engine","app");
	foreach($types as $type) {
		$list[$type]=array();
		$fList=wbListFilesRecursive($_ENV["path_".$type] ."/forms");
		foreach($fList as $fname) {
			$inc=strpos($fname,".inc");
			$ext=explode(".",$fname); $ext=$ext[count($ext)-1];
			$name=substr($fname,0,-(strlen($ext)+1));
			$tmp=explode("_",$name);
			$form=$tmp[0]; unset($tmp[0]);
			$mode=implode("_",$tmp);
			//$uri_path=str_replace($_SESSION["root_path"],"",$_ENV["path_".$type]);
			$uri_path="";
			$data=array(
				"type"=>$type,
				"path"=>$_ENV["path_".$type] ."/forms/{$form}/".$name.".{$ext}",
				"dir"=>	"/forms/{$form}",
				"uri"=>$uri_path ."/forms/{$form}/".$fname,
				"form"=>$form,
				"file"=>$fname,
				"ext"=>$ext,
				"name"=>$name,
				"mode"=>$mode
			);
			$list[$type][]=$data;
		}
	}
	return $list;
}

function wbArraySort( $array=array(), $args = array('votes' => 'd') ){
	// если передан атрибут, то предварительно готовим массив параметров
	if (is_string($args) && $args>"") {
			$args=wbArrayAttr($args);
			$param=array();
			foreach($args as $ds) {
				$tmp=explode(":",$ds);
				if (!isset($tmp[1])) {$tmp[1]="a";}
				$param[$tmp[0]]=$tmp[1];
			}
			$args=$param;
			unset($param,$tmp,$ds);
	}
	// сортировка массива по нескольким полям
	usort( $array, function( $a, $b ) use ( $args ){
		$res = 0;
		$a = (object) $a;
		$b = (object) $b;
		foreach( $args as $k => $v ){
			if (isset($a->$k) && isset($b->$k)) {
				if( $a->$k == $b->$k ) continue;

				$res = ( $a->$k < $b->$k ) ? -1 : 1;
				if( $v=='d' ) $res= -$res;
				break;
			}
		}
		return $res;
	} );
	return $array;
}

function wbArrayAttr($attr) {
	$attr=str_replace(","," ",$attr);
	$attr=str_replace(";"," ",$attr);
	return explode(" ",trim($attr));
}

function wbNormalizePath( $path ) {
    $patterns = array('~/{2,}~', '~/(\./)+~', '~([^/\.]+/(?R)*\.{2,}/)~', '~\.\./~');
    $replacements = array('/', '/', '', '');
    return preg_replace($patterns, $replacements, $path);
}

function wbClearValues($out) {
	$out=preg_replace("|\{\{([^\}]+?)\}\}|","",$out);
	return $out;
}

function wbListTpl() {
	$dir=$_ENV["path_app"]."/tpl";
	$list=array(); $result=array();
	if (is_dir($dir)) {
		$list=wbListFilesRecursive($dir);
		foreach($list as $l => $val) {
			if (substr($val,-4)==".php") {
				$list[$l]=str_replace($dir,"",$val);
				if (substr_count($list[$l],"/")==1) {$list[$l]=substr($list[$l],1);}
				$result[]=$list[$l];
			}
		}
	}
	$list=wbArraySort($result);
	return $list;
}

function wbListFilesRecursive($dir,$path=false) {
   $list = array();
   $stack[] = $dir;
   while ($stack) {
       $thisdir = array_pop($stack);
       if (is_dir($thisdir) AND $dircont = scandir($thisdir)) {
           $i=0; $idx=0;
           while (isset($dircont[$i])) {
               if ($dircont[$i] !== '.' && $dircont[$i] !== '..') {
                   $current_file = "{$thisdir}/{$dircont[$i]}";
                   if (is_file($current_file)) {
					   if ($path==true) {
							$list[]=array(
								"file" => "{$dircont[$i]}",
								"path" => "{$thisdir}"
							);
						} else {
							 $list[] = "{$dircont[$i]}";
						 }
                       $idx++;
                   } elseif (is_dir($current_file)) {
						$stack[] = $current_file;
                   }
               }
               $i++;
           }
       }
   }
   return $list;
}

function wbArraySortMulti( $array=array(), $args = array('votes' => 'd') ){
	// если передан атрибут, то предварительно готовим массив параметров
	if (is_string($args) && $args>"") {
			$args=wbAttrToArray($args);
			$param=array();
			foreach($args as $ds) {
				$tmp=explode(":",$ds);
				if (!isset($tmp[1])) {$tmp[1]="a";}
				$param[$tmp[0]]=$tmp[1];
			}
			$args=$param;
			unset($param,$tmp,$ds);
	}
	// сортировка массива по нескольким полям
	usort( $array, function( $a, $b ) use ( $args ){
		$res = 0;
		$a = (object) $a;
		$b = (object) $b;
		foreach( $args as $k => $v ){
			if (isset($a->$k) && isset($b->$k)) {
				if( $a->$k == $b->$k ) continue;

				$res = ( $a->$k < $b->$k ) ? -1 : 1;
				if( $v=='d' ) $res= -$res;
				break;
			}
		}
		return $res;
	} );
	return $array;
}

function wbCallFormFunc($name,$Item,$form=null,$mode=null) {
	if (!isset($_GET["mode"])) {$_GET["mode"]="";}
	if (!isset($_GET["form"])) {$_GET["form"]="";}
	if ($mode==null) {$mode=$_GET["mode"];}
	if ($mode=="") {$mode="list";}
	if ($form==null) {
		if (isset($Item["form"]) && $Item["form"]>"") {$form=$Item["form"];} else {$form=$_GET["form"];}
	}
	$sf=$_GET["form"]; $_GET["form"]=$form;
//	formCurrentInclude($form);
	$func=$form.$name; $_func="_".$func;
	if (is_callable($func)) {$Item=$func($Item,$mode);} else {
		if (is_callable($_func)) {$Item=$_func($Item,$mode);}
	}
	$_GET["form"]=$sf;
	return $Item;
}

function wbTranslit($textcyr = null, $textlat = null) {
    $cyr = array(
    'ё', 'ж',  'ч',  'щ',   'ш',  'ю',  'а', 'б', 'в', 'г', 'д', 'е', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ъ', 'ы', 'ь', 'э', 'я',
    'Ё', 'Ж',  'Ч',  'Щ',   'Ш',  'Ю',  'А', 'Б', 'В', 'Г', 'Д', 'Е', 'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ц', 'Ъ', 'Ы', 'Ь', 'Э', 'Я');
    $lat = array(
    'e', 'j', 'ch', 'sch', 'sh', 'u', 'a', 'b', 'v', 'g', 'd', 'e', 'z', 'i', 'i', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'h', 'c', '`', 'y', '', 'e', 'ya',
    'E', 'j', 'Ch', 'Sch', 'Sh', 'U', 'A', 'B', 'V', 'G', 'D', 'E', 'Z', 'I', 'I', 'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'F', 'H', 'c', '`', 'Y', '', 'E', 'ya');
    if($textcyr) return str_replace($cyr, $lat, $textcyr);
    else if($textlat) return str_replace($lat, $cyr, $textlat);
    else return null;
}

function wbBr2nl($str) {
$str = preg_replace("/(rn|n|r)/", "", $str);
return preg_replace("=<br */?>=i", "n", $str);
}
?>
