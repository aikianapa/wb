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
	wbTrigger("func",__FUNCTION__,"after",func_get_args());
}

function wbInitEnviroment() {
	wbTrigger("func",__FUNCTION__,"before");
	$_ENV["path_engine"]=__DIR__;
	$_ENV["path_app"]=$_SERVER["DOCUMENT_ROOT"];
	$_ENV["dbe"]=__DIR__."/database"; 			// Engine data
	$_ENV["dba"]=$_ENV["path_app"]."/database";	// App data
	$_ENV["dbec"]=__DIR__."/database/_cache"; 			// Engine data
	$_ENV["dbac"]=$_ENV["path_app"]."/database/_cache";	// App data
	$_ENV["error"]=array();
	wbTrigger("func",__FUNCTION__,"after",func_get_args());
}

function wbInitFunctions() {
	wbTrigger("func",__FUNCTION__,"before");
	if (is_file($_ENV["path_app"]."/functions.php")) {require_once($_ENV["path_app"]."/functions.php");}
	$forms=wbListForms();
	foreach($forms as $form) {
			$inc=array(
				"{$_ENV["path_engine"]}/forms/{$form}.php", "{$_ENV["path_engine"]}/forms/{$form}/{$form}.php",
				"{$_ENV["path_app"]}/forms/{$form}.php", "{$_ENV["path_app"]}/forms/{$form}/{$form}.php"
			); $res=FALSE;
			foreach($inc as $k => $file) {
				if (is_file("{$file}") && $res==FALSE ) {include_once("{$file}"); if ($k>1) {$res=TRUE;}; }
			}
	}
	wbTrigger("func",__FUNCTION__,"after",func_get_args());
}

function wbInitDatabase() {
	wbTrigger("func",__FUNCTION__,"before");
	if (!is_dir($_ENV["dbe"])) {mkdir($_ENV["dbe"],0777);}
	if (!is_dir($_ENV["dba"])) {mkdir($_ENV["dba"],0777);}
	if (!is_dir($_ENV["dbec"])) {mkdir($_ENV["dbec"],0777);}
	if (!is_dir($_ENV["dbac"])) {mkdir($_ENV["dbac"],0777);}
	wbTrigger("func",__FUNCTION__,"after",func_get_args());
}

function wbFlushDatabase() {
	wbTrigger("func",__FUNCTION__,"before");
	$etables=wbTableList(true);
	$atables=wbTableList();
	foreach($etables as $key) {wbTableFlush($_ENV["dbe"]."/".$key);}
	foreach($atables as $key) {wbTableFlush($_ENV["dba"]."/".$key);}
	
	wbTrigger("func",__FUNCTION__,"after",func_get_args());
}

function wbTable($table="data",$engine=false) {
	wbTrigger("func",__FUNCTION__,"before");
	if ($engine==false) {$db=$_ENV["dba"];} else {$_ENV["dbe"];}
	$tname=$table;
	$table=wbTablePath($table,$engine);
	$_ENV[$table]["name"]=$tname;
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
	if ($engine==false) {$db=$_ENV["dba"];} else {$_ENV["dbe"];}
	$table=wbTablePath($table,$engine);
	if (!is_file($table)) {
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
	wbTrigger("func",__FUNCTION__,"before",func_get_args());
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
	wbTrigger("func",__FUNCTION__,"after",func_get_args());
	return $table;
}

function wbTablePath($table="data",$engine=false) {
	wbTrigger("func",__FUNCTION__,"before",func_get_args());
	if ($engine==false) {$db=$_ENV["dba"];} else {$_ENV["dbe"];}
	$table=$db."/".$table;	
	wbTrigger("func",__FUNCTION__,"after",func_get_args(),$table);
	return $table;
}

function wbTableCachePath($table="data",$engine=false) {
	wbTrigger("func",__FUNCTION__,"before",func_get_args());
	if ($engine==false) {$db=$_ENV["dbac"];} else {$_ENV["dbec"];}
	$table=$db."/".$table;	
	wbTrigger("func",__FUNCTION__,"after",func_get_args(),$table);
	return $table;
}

function wbTableList($engine=false) {
	wbTrigger("func",__FUNCTION__,"before",func_get_args());
	if ($engine==false) {$db=$_ENV["dba"];} else {$db=$_ENV["dbe"];}
	$list=wbListFiles($db);
	wbTrigger("func",__FUNCTION__,"after",func_get_args(),$list);
	return $list;
}

function wbListItems($table="data",$where=null) {return wbItemList($table);}
function wbItemList($table="data") {
	wbTrigger("func",__FUNCTION__,"before",func_get_args());
	$list=wbTrigger("form",__FUNCTION__,"BeforeItemList",func_get_args(),array());
	if (!is_file($table)) {
		wbError("func",__FUNCTION__,1001,func_get_args());
		return $list;
	} else {
		if (!isset($_ENV["cache"][$table])) {
			$_ENV["cache"][$table]=json_decode(file_get_contents($table),true);
		}
		if (isset($_ENV["cache"][$table]["data"])) {$list=$_ENV["cache"][$table]["data"];}
		array_walk($list,function(&$item,$key,$args){
			$item=wbTrigger("form",__FUNCTION__,"AfterItemRead",$args,$item);
		},func_get_args());
	}
	$list=wbTrigger("form",__FUNCTION__,"AfterItemList",func_get_args(),$list);
	wbTrigger("func",__FUNCTION__,"after",func_get_args(),$list);
	return $list;
}

function wbItemRead($table,$id) {
	wbTrigger("func",__FUNCTION__,"before",func_get_args());
	$cache=wbCacheName($table,$id);
	if (is_file($cache)) {
		$item=json_decode(file_get_contents($cache),true);
	} else {
		$list=wbItemList($table);
		if (isset($list[$id])) {
			$item=$list[$id];
			file_put_contents($cache,json_encode($item, JSON_HEX_QUOT | JSON_HEX_APOS | JSON_UNESCAPED_UNICODE), LOCK_EX);
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
	wbTrigger("func",__FUNCTION__,"after",func_get_args(),$item);
	return $item;
}

function wbCacheName($table,$id=null) {
	$tmp=explode($_ENV["dbe"],$table);
	if (count($tmp)==2) {$dbc=$_ENV["dbec"];$db=$_ENV["dbe"];} else {$dbc=$_ENV["dbac"];$db=$_ENV["dba"];}
	$tname=str_replace($db."/","",$table);
	if (!is_dir($dbc."/".$tname)) {mkdir($dbc."/".$tname,0777);}
	if ($id==null) {$cache=$cache=$dbc."/".$tname;} else {$cache=$dbc."/".$tname."/".$id;}
	return $cache;	
}

function wbItemRemove($table=null,$id=null) {
	wbTrigger("func",__FUNCTION__,"before",func_get_args());
	if (!is_file($table)) {
		wbError("func",__FUNCTION__,1001,func_get_args());
		return null;
	} else {
		$cache=wbCacheName($table,$id);
		if ($id!==null) {
			$item=wbItemRead($table,$id);
			if (is_array($item)) {$item["__wbFlag__"]="remove";}
			$item=json_encode($item, JSON_HEX_QUOT | JSON_HEX_APOS | JSON_UNESCAPED_UNICODE);
		}
		if (!is_dir($cache) AND $item!==null) {$res=file_put_contents($cache,$item, LOCK_EX);}
		if (!$res) {wbError("func",__FUNCTION__,1007,func_get_args());}
	}
	wbTrigger("func",__FUNCTION__,"after",func_get_args());
}

function wbItemSave($table,$item=null) {
	wbTrigger("func",__FUNCTION__,"before",func_get_args());
	$res=null;
	if (!is_file($table)) {
		wbError("func",__FUNCTION__,1001,func_get_args());
		return null;
	} else {
		if (!isset($item["id"])) {$item["id"]=wbNewId();}
		$item=wbItemSetTable($table,$item);
		$cache=wbCacheName($table,$item["id"]);
		$item=json_encode($item, JSON_HEX_QUOT | JSON_HEX_APOS | JSON_UNESCAPED_UNICODE);
		$call=$item["_table"]."BeforeSaveItem";
		if (is_callable($call)) {$item=$call($item);} else {$call="_".$call; if (is_callable($call)) {$item=$call($item);}}
		if (!is_dir($cache)) {$res=file_put_contents($cache,$item, LOCK_EX);}
		if (!$res) {wbError("func",__FUNCTION__,1007,func_get_args());}
	}
	wbTrigger("func",__FUNCTION__,"after",func_get_args());
	return $res;
}

function wbItemSetTable($table,$item=null) {
	$tablename=explode("/",$table);
	$item["_table"]=$tablename[count($tablename)-1];
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
			$call=$_ENV[$args[0]]["name"].$trigger;
			if (in_array($trigger,array(
				"BeforeItemRead","AfterItemRead",
				"BeforeItemList","AfterItemList",
				"BeforeItemSave","AfterItemSave",
				"BeforeItemRemove","AfterItemRemove",
			))) {
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
			$_ENV["error"][$type][$name]=array("errno"=>$error,"error"=>$_ENV["errors"][$error]);
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


function wbGetForm($form=NULL,$mode=NULL,$engine=null) {
	$_ENV["error"][__FUNCTION__]="";
	if ($form==NULL) {$form=$_GET["form"];}
	if ($mode==NULL) {$mode=$_GET["mode"];}
	$aCall=$form."_".$mode; $eCall=$form."__".$mode;
	if (is_callable($aCall)) {$out=$aCall();} elseif (is_callable($eCall) AND $engine!==false) {$out=$eCall();}
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

function wbListFiles($dir) {
	wbTrigger("func",__FUNCTION__,"before",func_get_args());
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
	wbTrigger("func",__FUNCTION__,"after",func_get_args());
	return $list;
}

function wbRrecurseDelete($src) {
    $dir = opendir($src);
	if (is_resource($dir)) {
		while(false !== ( $file = readdir($dir)) ) {
			if (( $file !== '.' ) && ( $file !== '..' )) {
				if ( is_dir($src . '/' . $file) ) {
					recurse_delete($src . '/' . $file);
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

function wbQuery($sql) {
	require_once($_ENV["path_engine"]."/lib/sql/PHPSQLParser.php");
	wbTrigger("func",__FUNCTION__,"before",func_get_args());
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
	wbTrigger("func",__FUNCTION__,"after",func_get_args());
	return $table["data"];
}

function wbt_where($table,$where) {
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

function wbFromString($str="") {
	return ki::fromString($str);
}

function wbFromFile($str="") {
	return ki::fromFile($str);
}

function wbAttrToArray($attr) {
	$attr=str_replace(","," ",$attr);
	$attr=str_replace(";"," ",$attr);
	return explode(" ",trim($attr));
}

function wbSetValuesStr($tag="",$Item=array(), $limit=2)
{
	if (is_object($tag)) {$tag=$tag->outerHtml();}
	if (!is_array($Item)) {$Item=array($Item);}
	if (is_string($tag)) {
	//$tag=strtr($tag,array("%7B%7B"=>"{{","%7D%7D"=>"}}"));
	$tag=str_replace("%7B%7B","{{",$tag);
	$tag=str_replace("%7D%7D","}}",$tag);
	if (strpos($tag, '}}') !== false ) {
		// функция подставляющая значения
		$tag = wbChangeQuot($tag);			// заменяем &quot на "
		$spec = array('_form', '_mode', '_item', '_id');
		$exit = false;
		$err = false;
		$nIter = 0;
		$mask = '`(\{\{){1,1}(%*[\w\d]+|_form|_mode|_item|((_SETT|_SETTINGS|_SESS|_SESSION|_SRV|_COOK|_COOKIE|$_ENV|_REQ|_GET|_POST|%*[\w\d]+)?([\[]{1,1}(%*[\w\d]+|"%*[\w\d]+")[\]]{1,1})*))(\}\}){1,1}`u';
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
								$sub = '$_SESSION["settings"]';
								break;
							case '_SETTINGS':
								$sub = '$_SESSION["settings"]';
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
		if (in_array($role,$_ENV["user"]["role"])) {$res=true;}
	} else {
		$users=wbTable("users");
		$user=wbReadItem($users,$userId);
		if (in_array($role,$user["role"])) {$res=true;}
	}
	
}

	function contentControls($set="") {
		$res="*";
		$controls="[data-wb-role]";
		$allow="[data-allow],[data-disallow],[data-disabled],[data-enabled],[data-readonly],[data-writable]";
		$target="[prepend],[append],[before],[after],[html]";
		if ($set!="") {$res=$$set;} else {$res="{$controls},{$allow},{$target}";}
		unset($controls,$allow,$target);
		return $res;
	}


function wbListForms() {
	$exclude=array("common","admin","source");
	$list=array();
	$eList=wbListFilesRecursive($_ENV["path_engine"] ."/forms");
	$aList=wbListFilesRecursive($_ENV["path_app"] ."/forms");
	$arr=$eList; 
	foreach($aList as $a) {$arr[]=$a;}
	unset($eList,$aList);
	foreach($arr as $i => $name) {
			$inc=strpos($name,".inc");
			$ext=explode(".",$name); $ext=$ext[count($ext)-1];
			$name=substr($name,0,-(strlen($ext)+1));
			$name=explode("_",$name); $name=$name[0];
			if ($ext=="php" && !$inc && !in_array($name,$exclude) && $name>"") {
				$exclude[]=$list[]=$name;
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

function wbListFilesRecursive($dir,$path=false) {
   $list = array();
   $stack[] = $dir;
   while ($stack) {
       $thisdir = array_pop($stack);
       if ($dircont = scandir($thisdir)) {
           $i=0; $idx=0;
           while (isset($dircont[$i])) {
               if ($dircont[$i] !== '.' && $dircont[$i] !== '..') {
                   $current_file = "{$thisdir}/{$dircont[$i]}";
                   if (is_file($current_file)) {
					   if ($path==true) {
							$list[$idx]["file"] = "{$dircont[$i]}"; 
							$list[$idx]["path"] = "{$thisdir}"; 
						} else { $list[] = "{$dircont[$i]}"; }
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



function wbClearValues($out) {
	$out=preg_replace("|\{\{([^\}]+?)\}\}|","",$out);
	return $out;
}


?>
