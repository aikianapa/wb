<?php
function filemanager__init()
{
    if (isset($_ENV["route"]["params"][0])) {
        $mode=$_ENV["route"]["params"][0];
        $call="filemanager__{$mode}";
        if (is_callable($call)) {
            $out=@$call();
        }
        die;
    } else {
        $out=file_get_contents(__DIR__ ."/filemanager_ui.php");
        return $out;
    }
}

function filemanager__getdir($result=false)
{
    if (filemanager__allow()) {
    $list=array();
    $dir=$_ENV["path_app"];
    if ($_ENV["route"]["params"]["dir"]>"") {
        $dir.=$_ENV["route"]["params"]["dir"];
        $dir=str_replace("..", "", $dir);
        $list[] = array("type"=>"back","path"=>$back,"link"=>false,"ext"=>$ext,"name"=>"..");
    }
    if ($dircont = scandir($dir)) {
        $i=0;
        $idx=0;
        while (isset($dircont[$i])) {
            if ($dircont[$i] !== '.' && $dircont[$i] !== '..') {
                $current_file = "{$dir}/{$dircont[$i]}";
                $ext=pathinfo($current_file, PATHINFO_EXTENSION);
                $link=is_link($current_file);
                $href=$_ENV["route"]["params"]["dir"]."/".$dircont[$i];
                if (is_file($current_file)) {
                    $list[] = array("type"=>"file","path"=>$dir,"href"=>$href,"link"=>$link,"ext"=>$ext,"name"=>$dircont[$i]);
                }
                if (is_dir($current_file)) {
                    $list[] = array("type"=>"dir","path"=>$dir,"href"=>$href,"link"=>$link,"ext"=>$ext,"name"=>$dircont[$i]);
                }
            }
            $i++;
        }
    }
    $path=explode("/", $_ENV["route"]["params"]["dir"]);
    unset($path[0]);
    $arr=array(
            "result"=>wbArraySortMulti($list, "type,name"),
            "path"=>$path
        );
		if ($result==true) {
				return $arr;
		} else {
	    $out=wbGetTpl("/engine/modules/filemanager/filemanager_ui.php", true);
	    $out=$out->find("#panel", 0);
	    $out->wbSetData($arr);
	    echo $out;
		}
    }
}

function filemanager__getfile()
{
    if (filemanager__allow() && isset($_POST["file"])) {
        echo file_get_contents($_ENV["path_app"].$_POST["file"]);
    }
}

function filemanager__putfile()
{
    $res=false;
    if (wbRole("admin") && isset($_POST["file"]) && isset($_POST["text"])) {
        $res=file_put_contents($_ENV["path_app"].$_POST["file"], $_POST["text"]);
    }
    echo json_encode($res);
}

function filemanager__dialog()
{
    if (filemanager__allow()) {
        $out=wbGetTpl("/engine/modules/filemanager/filemanager_ui.php", true);
        $out=$out->find("#filemanagerModalDialog", 0);
        $action=$_ENV["route"]["params"][1];
        $title=$out->find("meta[name=$action]")->attr("title");
        $content=$out->find("meta[name=$action]")->attr("content");
        $visible=$out->find("meta[name=$action]")->attr("visible");
        $invisible=$out->find("meta[name=$action]")->attr("invisible");
        $fields=array();
        foreach ($out->find("[name]:not(meta)") as $inp) {
            $fld=$inp->attr("name");
            $inp->wbSetAttributes();
            if ($inp->is(":input") and isset($visible) and in_array($fld, wbAttrToArray($visible))) {
                $inp->attr("type", "text");
            }
            if ($inp->is(":input") and isset($invisible) and in_array($fld, wbAttrToArray($invisible))) {
                $inp->attr("type", "hidden");
            }
            $fields[$fld]=$inp->outerHtml();
        }
        $out->find(".modal-title")->html($title);
        $out->find(".modal-body form")->html($content);
        $out->children("meta")->remove();
        $out->children("input")->remove();
        $out->find(".modal-footer .btn-primary")->attr("data-action", $action);
        $out->wbSetValues($fields);
        if ($action=="paste") {
            filemanager__action_paste($out);
        } else {
            echo $out;
        }
    }
}

function filemanager__action_paste($out=null)
{
		$flag=true;
		if ($out!==null) {
					$_ENV["route"]["params"]["dir"]=$_POST["path"];
					$cur=filemanager__getdir(true);
					$arr=array();
					foreach($cur["result"] as $item) {
							if ($item["type"]!=="back") $arr[]=$item["name"]."::".$item["type"];
					}
					foreach($_POST["list"] as $i => $item) {
							$check=$item["name"]."::".$item["type"];
							if (in_array($check,$arr)) {$flag=false; break;}
					}
					if (!$flag) {echo json_encode(array("res"=>"dialog","action"=>$out->outerHtml(),"post"=>$_POST)); die;}
		}
		if ($flag==true) {
				foreach($_POST["list"] as $i => $item) {
						$dst=$_ENV["path_app"].$_POST["path"]."/".$item["name"];
						$src=$_ENV["path_app"].$item["path"]."/".$item["name"];
						wbRecurseCopy($src,$dst);
						if 				($item["type"]=="dir") {

						} elseif 	($item["type"]=="file") {

						}
				}
				echo json_encode(array("res"=>$_POST["method"],"action"=>"reload_list"));
		}
		die;
}

function filemanager__action()
{
    if (!filemanager__allow()) {
        return;
    }
    $call="filemanager__action_".$_ENV["route"]["params"][1];
    if (is_callable($call)) {
        echo $call();
    }
}

function filemanager__action_newdir()
{
    $res=false;
    if (!filemanager__allow()) {
        return json_encode($res);
    }
    $action=$_ENV["route"]["params"][1];
    $dir=$_ENV["path_app"].$_POST["path"];
    $newname=$_POST["newname"];
    $path=$dir."/".$newname;
    if (!is_dir($path) and $newname>"") {
        $res=mkdir($path);
    }
    if (!$res) {
        return $res;
    }
    return json_encode(array(
        "res"=>$res,
        "name"=>$newname,
        "type"=>"dir",
        "action"=>"reload_list"
    ));
}

function filemanager__action_newfile()
{
    $res=false;
    if (!filemanager__allow()) {
        return json_encode($res);
    }
    $action=$_ENV["route"]["params"][1];
    $dir=$_ENV["path_app"].$_POST["path"];
    $newname=$_POST["newname"];
    $path=$dir."/".$newname;
    if (!is_file($path) and $newname>"") {
        $res=file_put_contents($path, "");
    }
    if (is_file($path)) {
		    return json_encode(array(
		        "res"=>$res,
		        "name"=>$newname,
		        "type"=>"file",
		        "ext"=> pathinfo($path, PATHINFO_EXTENSION),
		        "action"=>"reload_list"
		    ));
		}
}

function filemanager__action_rmdir()
{
    $res=false;
    if (!filemanager__allow()) {
        return json_encode($res);
    }
    $action=$_ENV["route"]["params"][1];
    $dir=$_ENV["path_app"].$_POST["path"]."/".$_POST["dirname"];
    if (is_dir($dir) and !is_link($dir)) {
        $res=wbRecurseDelete($dir);
    }
    if (is_dir($dir) && $res!==false) {
        return json_encode($res);
    } else {
        return json_encode(array(
            "res"=>$res,
            "name"=>$_POST["dirname"],
            "type"=>"dir",
            "action"=>"reload_list"
        ));
    }
}

function filemanager__action_rmfile()
{
    $res=false;
    if (!filemanager__allow()) {
        return json_encode($res);
    }
    $action=$_ENV["route"]["params"][1];
    $file=$_ENV["path_app"].$_POST["path"]."/".$_POST["filename"];
    if (is_file($file) and !is_link($file)) {
        $res=wbFileRemove($file);
    }
    if (is_file($file) and $res!==false) {
        return json_encode($res);
    } else {
        return json_encode(array(
            "res"=>$res,
            "name"=>$_POST["filename"],
            "type"=>"dir",
            "action"=>"reload_list"
        ));
    }
}

function filemanager__action_renfile()
{
    $res=false;
    if (!filemanager__allow()) {
        return json_encode($res);
    }
    $action=$_ENV["route"]["params"][1];
    $oldfile=$_ENV["path_app"].$_POST["path"]."/".$_POST["oldname"];
    $newfile=$_ENV["path_app"].$_POST["path"]."/".$_POST["filename"];
    if (is_file($oldfile) and !is_file($newfile) and $_POST["filename"]>"") {
        $res=rename($oldfile, $newfile);
    }
    if (!is_file($newfile) or $res==false) {
        return json_encode($res);
    } else {
        return json_encode(array(
            "res"=>$res,
            "name"=>$_POST["filename"],
            "type"=>"file",
            "ext"=> pathinfo($newfile, PATHINFO_EXTENSION),
            "action"=>"change_name"
        ));
    }
}

function filemanager__action_rendir()
{
    $res=false;
    if (!filemanager__allow()) {
        return json_encode($res);
    }
    $action=$_ENV["route"]["params"][1];
    $olddir=$_ENV["path_app"].$_POST["path"]."/".$_POST["oldname"];
    $newdir=$_ENV["path_app"].$_POST["path"]."/".$_POST["dirname"];
    if (is_dir($olddir) and !is_dir($newdir) and $_POST["dirname"]>"") {
        $res=rename($olddir, $newdir);
    }
    if (!is_dir($newdir) or $res==false) {
        return json_encode($res);
    } else {
        return json_encode(array(
            "res"=>$res,
            "name"=>$_POST["dirname"],
            "type"=>"dir",
            "action"=>"change_name"
        ));
    }
}

function filemanager__allow()
{
    return wbRole("admin");
}
