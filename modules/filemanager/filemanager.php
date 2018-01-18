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
                   if (is_file($current_file)) {$list[] = array("type"=>"file","path"=>$dir,"link"=>$link,"ext"=>$ext,"name"=>$dircont[$i]); }
									 if (is_dir($current_file)) {$list[] = array("type"=>"dir","path"=>$dir,"link"=>$link,"ext"=>$ext,"name"=>$dircont[$i]); }
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

?>
