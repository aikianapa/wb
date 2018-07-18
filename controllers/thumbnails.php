<?php

function thumbnails__controller() {
    $params=$_ENV["route"]["params"];
	if (!isset($_GET)) {
        $_GET=array();
        $_GET["w"]=$params["w"]; unset($params["w"]);
        $_GET["h"]=$params["h"]; unset($params["h"]);
    }
	if (isset($params["src"])) {
        $_GET["src"]=$params["src"]; unset($params["src"]);
    } else {
        $_GET["src"].="/".implode("/",$params);
    }
	$_GET["zc"]=$_ENV["route"]["zc"];
    thumbnail__view();
	die;
}

function thumbnail__view() {
    $file=$_ENV["path_app"]."/".$_GET["src"];
    if (is_file($file)) {
        $size = getimagesize ($file);
        $ext = pathinfo($file, PATHINFO_EXTENSION);
        $width=$size[0];
        $height=$size[1];
        $mime=$size["mime"];
        $cachefile=md5($file."_".filemtime($file)."_".$_GET["w"]."_".$_GET["h"]).".".$ext;
        $cachedir=$_ENV["path_app"]."/uploads/_cache/".substr($cachefile,0,2);
        if (!is_dir($cachedir)) {mkdir ( $cachedir, 0755 , true );}
        if (!is_file($cachedir."/".$cachefile)) {
            $image = new \Imagick(realpath($file));
            if ($_GET["zc"]==1) {
                $image->cropThumbnailImage($_GET["w"], $_GET["h"], true);
            } else {
                $image->thumbnailImage($_GET["w"], $_GET["h"], true);
            }
            file_put_contents($cachedir."/".$cachefile, $image);
        } else {
            $image=file_get_contents($cachedir."/".$cachefile);
        }
        header("Content-Type: ".$mime);
        echo $image;
    }
}

?>
