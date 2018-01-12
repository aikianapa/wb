<?php
include_once($_SERVER["DOCUMENT_ROOT"]."/engine/functions.php");
wbInit();

if ($_GET["mode"]=="ajax") {echo source__ajax();}


function source__list() {
$out=wbGetForm();
$Item=array();
//$dirlist = getDirectoryTree($_SESSION["app_path"],'',array("engine","contents"));
$Item["dirTree"]="<div  class='sourceTree' role='menu'></div>\n";
$out->wbSetData($Item);
return $out->outerHtml();
}

function ajax__source() {
		$result=false;
	if ($_SESSION["user_role"]=="admin") {
		switch($_ENV["route"]["params"][0]) {
			case "getfile":
				$result=file_get_contents($_ENV["route"]["params"]["file"]);
				break;
			case "setfile":
				$result=file_put_contents($_ENV["path_app"].$_POST["file"],$_POST["value"]);
				break;
			case "getdir":
				if (!isset($_ENV["route"]["params"]["dir"])) {$dir="";} else {$dir=$_ENV["route"]["params"]["dir"];}
				$result = json_encode(getDirectoryJson($_ENV["path_app"].$dir,'',array("engine")));
				break;
			default:
				break;
		}
	}
	return $result;
}


function array2html($dirlist,$path="/") {
	$html="<ul>";
	if ($path=="/") {$html.="<li class='isFolder {$path}'><a href='{$path}' target=''>.</a></li>";}
	foreach($dirlist as $key => $item) {
		if (is_string($item)) $html.="<li class='isFile {$path}'><a href='{$path}{$key}' target='{$path}'>{$key}</a></li>";
		if (is_array($item)) {
			$cpath="{$path}{$key}/";
			$html.="<li class='isFolder {$path}'><a href='{$cpath}' target='{$path}'>{$key}</a>".array2html($item,$cpath)."</li>";
		}
	}
	$html.="</ul>";
	return $html;
}

function getDirectoryJson( $outerDir , $x, $exclude=array()){
    $dirs = array_diff( scandir( $outerDir ), Array( ".", ".." ) );
    $dir_array = Array();
    $i=0;
    foreach( $dirs as $d ){
        if (!in_array($d,$exclude)) {
			$dir=substr($outerDir,strlen($_SESSION["app_path"]));
			if ($dir=="") $dir="/";
			if( is_dir($outerDir."/".$d)  ){
				$dir_array[ $i ]["id"]=null;
				$dir_array[ $i ]["isFolder"]=true;
				$dir_array[ $i ]["isActive"]=false;
				$dir_array[ $i ]["isExpanded"]=false;
				$dir_array[ $i ]["isLazy"]=true;
				$dir_array[ $i ]["text"]=$d;
				$dir_array[ $i ]["href"]=$dir.$d."/";
				$dir_array[ $i ]["uiIcon"]="fa fa-folder-o text-primary";
				$dir_array[ $i ]["hrefTarget"]=$dir;
				//$dir_array[ $i ]["children"] = getDirectoryJson( $outerDir."/".$d , $x);
			} else {
				preg_match("/{$x}/",$d,$tmp);
				if (isset($tmp[0])) {
					$ext=explode(".",$d); $ext=$ext[count($ext)-1];

					switch($ext) {
						case "js"; case "php";
							$ico="fa fa-file-code-o";
							break;
						case "pdf";
							$ico="fa fa-file-pdf-o";
							break;
						case "jpeg"; case "jpg"; case "png"; case "gif"; case "tif"; case "tiff";  case "bmp";  case "svg";
							$ico="fa fa-file-image-o";
							break;
						case "zip"; case "arj"; case "tar"; case "gz"; case "rar";
							$ico="fa fa-file-archive-o";
							break;
						case "mov"; case "avi"; case "wmv"; case "mpeg"; case "mp4";
							$ico="fa fa-file-movie-o";
							break;
						case "txt"; case "log"; case "css";
							$ico="fa fa-file-text-o";
							break;
						default;
							$ico="fa fa-file-o";
							break;

					}

					$dir_array[ $i ]["id"]=null;
					$dir_array[ $i ]["isActive"]=false;
					$dir_array[ $i ]["isFolder"]=false;
					$dir_array[ $i ]["href"]=$dir."/".$d;
					$dir_array[ $i ]["hrefTarget"]=$dir;
					$dir_array[ $i ]["uiIcon"]=$ico."  text-primary" ;
					$dir_array[ $i ]["text"]=$d;
				}
			}
			$i++;
        }
    }
    return $dir_array;
}

function getDirectoryTree( $outerDir , $x, $exclude=array()){
    $dirs = array_diff( scandir( $outerDir ), Array( ".", ".." ) );
    $dir_array = Array();
    foreach( $dirs as $d ){
        if (!in_array($d,$exclude)) {
			if( is_dir($outerDir."/".$d)  ){
				$dir_array[ $d ] = getDirectoryTree( $outerDir."/".$d , $x);
			} else {
				preg_match("/{$x}/",$d,$tmp);
				if (isset($tmp[0])) $dir_array[ $d ] = $d;
			}
        }
    }
    return $dir_array;
}
?>
