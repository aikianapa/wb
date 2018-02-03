<?php
function filemanager__init() {
	if (isset($_ENV["route"]["params"][0])) {
		$mode=$_ENV["route"]["params"][0];
		$call="filemanager__{$mode}";
		if (is_callable($call)) {$out=@$call();}
		die;
	} else {
		$out=file_get_contents(__DIR__ ."/filemanager_ui.php");
		return $out;
	}
}

function filemanager__getdir() {
	$list=array();
	$dir=$_ENV["path_app"];
	if ($_ENV["route"]["params"]["dir"]>"") {
		$dir.=$_ENV["route"]["params"]["dir"];
		$dir=str_replace("..","",$dir);
		$list[] = array("type"=>"back","path"=>$back,"link"=>false,"ext"=>$ext,"name"=>"..");
	}
	if ($dircont = scandir($dir)) {
           $i=0; $idx=0;
           while (isset($dircont[$i])) {
               if ($dircont[$i] !== '.' && $dircont[$i] !== '..') {
                   $current_file = "{$dir}/{$dircont[$i]}";
									 $ext=pathinfo($current_file, PATHINFO_EXTENSION);
									 $link=is_link($current_file);
									 $href=$_ENV["route"]["params"]["dir"]."/".$dircont[$i];
                   if (is_file($current_file)) {$list[] = array("type"=>"file","path"=>$dir,"href"=>$href,"link"=>$link,"ext"=>$ext,"name"=>$dircont[$i]); }
									 if (is_dir($current_file)) {$list[] = array("type"=>"dir","path"=>$dir,"href"=>$href,"link"=>$link,"ext"=>$ext,"name"=>$dircont[$i]); }
               }
               $i++;
           }
    }
		$path=explode("/",$_ENV["route"]["params"]["dir"]);
		unset($path[0]);
		$arr=array(
			"result"=>wbArraySortMulti($list,"type,name"),
			"path"=>$path
		);
		$out=wbGetTpl("/engine/modules/filemanager/filemanager_ui.php",true);
		$out=$out->find("#panel",0);
		$out->wbSetData($arr);
		echo $out;
}

function filemanager__getfile() {
		if (filemanager__allow() && isset($_POST["file"])) {
				echo file_get_contents($_ENV["path_app"].$_POST["file"]);
		}
}

function filemanager__putfile() {
		$res=false;
		if (wbRole("admin") && isset($_POST["file"]) && isset($_POST["text"])) {
				$res=file_put_contents($_ENV["path_app"].$_POST["file"],$_POST["text"]);
		}
		echo json_encode($res);
}

function filemanager__dialog() {
	if (filemanager__allow()) {
		$out=wbGetTpl("/engine/modules/filemanager/filemanager_ui.php",true);
		$out=$out->find("#filemanagerModalDialog",0);
		$action=$_ENV["route"]["params"][1];
		$title=$out->find("meta[name=$action]")->attr("title");
		$content=$out->find("meta[name=$action]")->attr("content");
		$visible=$out->find("meta[name=$action]")->attr("visible");
		$invisible=$out->find("meta[name=$action]")->attr("invisible");
		$fields=array();
		foreach($out->find("[name]:not(meta)") as $inp) {
			$fld=$inp->attr("name");
			$inp->wbSetAttributes();
			if ($inp->is(":input") AND isset($visible) AND in_array($fld,wbAttrToArray($visible))) {$inp->attr("type","text");}
			if ($inp->is(":input") AND isset($invisible) AND in_array($fld,wbAttrToArray($invisible))) {$inp->attr("type","hidden");}
			$fields[$fld]=$inp->outerHtml();
		}
		$out->find(".modal-title")->html($title);
		$out->find(".modal-body form")->html($content);
		$out->children("meta")->remove();
		$out->children("input")->remove();
		$out->find(".modal-footer .btn-primary")->attr("data-action",$action);
		$out->wbSetValues($fields);
		echo $out;
	}
}

function filemanager__action() {
	if (!filemanager__allow()) return;
	$call="filemanager__action_".$_ENV["route"]["params"][1];
	if (is_callable($call)) {echo $call();}
}

function filemanager__action_newdir() {
	$res=false;
	if (!filemanager__allow()) return json_encode($res);
	$action=$_ENV["route"]["params"][1];
	$dir=$_ENV["path_app"].$_POST["path"];
	$newname=$_POST["newname"];
	$path=$dir."/".$newname;
	if (!is_dir($path) AND $newname>"") {$res=mkdir($path);}
	if (!$res) return $res;
	return json_encode(array(
		"res"=>$res,
		"name"=>$newname,
		"type"=>"dir",
		"action"=>"reload_list"
	));
}

function filemanager__action_newfile() {
	$res=false;
	if (!filemanager__allow()) return json_encode($res);
	$action=$_ENV["route"]["params"][1];
	$dir=$_ENV["path_app"].$_POST["path"];
	$newname=$_POST["newname"];
	$path=$dir."/".$newname;
	if (!is_file($path) AND $newname>"") {$res=file_put_contents($path,"");}
	if (!$res) return $res;
	return json_encode(array(
		"res"=>$res,
		"name"=>$newname,
		"type"=>"file",
		"ext"=> pathinfo($path, PATHINFO_EXTENSION),
		"action"=>"reload_list"
	));
}

function filemanager__action_rmdir() {
	$res=false;
	if (!filemanager__allow()) return json_encode($res);
	$action=$_ENV["route"]["params"][1];
	$dir=$_ENV["path_app"].$_POST["path"]."/".$_POST["dirname"];
	if (is_dir($dir) AND !is_link($dir)) {
			$res=wbRecurseDelete($dir);
	}
	if (is_dir($dir) AND $res!==false) {return json_encode($res);} else {
		return json_encode(array(
			"res"=>$res,
			"name"=>$_POST["dirname"],
			"type"=>"dir",
			"action"=>"reload_list"
		));
	}
}

function filemanager__action_rmfile() {
	$res=false;
	if (!filemanager__allow()) return json_encode($res);
	$action=$_ENV["route"]["params"][1];
	$file=$_ENV["path_app"].$_POST["path"]."/".$_POST["filename"];
	if (is_file($file) AND !is_link($file)) {
			$res=wbFileRemove($file);
	}
	if (is_file($file) AND $res!==false) {return json_encode($res);} else {
		return json_encode(array(
			"res"=>$res,
			"name"=>$_POST["filename"],
			"type"=>"dir",
			"action"=>"reload_list"
		));
	}
}

function filemanager__action_renfile() {
	$res=false;
	if (!filemanager__allow()) return json_encode($res);
	$action=$_ENV["route"]["params"][1];
	$oldfile=$_ENV["path_app"].$_POST["path"]."/".$_POST["oldname"];
	$newfile=$_ENV["path_app"].$_POST["path"]."/".$_POST["filename"];
	if (is_file($oldfile) AND !is_file($newfile) AND $_POST["filename"]>"") {
			$res=rename($oldfile,$newfile);
	}
	if (!is_file($newfile) OR $res==false) {return json_encode($res);} else {
		return json_encode(array(
			"res"=>$res,
			"name"=>$_POST["filename"],
			"type"=>"file",
			"ext"=> pathinfo($newfile, PATHINFO_EXTENSION),
			"action"=>"change_name"
		));
	}
}

function filemanager__action_rendir() {
	$res=false;
	if (!filemanager__allow()) return json_encode($res);
	$action=$_ENV["route"]["params"][1];
	$olddir=$_ENV["path_app"].$_POST["path"]."/".$_POST["oldname"];
	$newdir=$_ENV["path_app"].$_POST["path"]."/".$_POST["dirname"];
	if (is_dir($olddir) AND !is_dir($newdir) AND $_POST["dirname"]>"") {
			$res=rename($olddir,$newdir);
	}
	if (!is_dir($newdir) OR $res==false) {return json_encode($res);} else {
		return json_encode(array(
			"res"=>$res,
			"name"=>$_POST["dirname"],
			"type"=>"dir",
			"action"=>"change_name"
		));
	}
}

function filemanager__allow() {
		return wbRole("admin");
}

?>
