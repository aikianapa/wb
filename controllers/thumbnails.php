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
        list($width, $height, $type) = $size = getimagesize ($file);
        $ext = pathinfo($file, PATHINFO_EXTENSION);
        $mime=$size["mime"];
        $cachefile=md5($file."_".filemtime($file)."_".$_GET["w"]."_".$_GET["h"]).".".$ext;
        $cachedir=$_ENV["path_app"]."/uploads/_cache/".substr($cachefile,0,2);
        if (!is_dir($cachedir)) {mkdir ( $cachedir, 0755 , true );}
        if (!is_file($cachedir."/".$cachefile)) {
		if (class_exists("Imagick") ) {
		    $image = new \Imagick(realpath($file));
		    if ($_GET["zc"]==1) {
			$image->cropThumbnailImage($_GET["w"], $_GET["h"], true);
		    } else {
			$image->thumbnailImage($_GET["w"], $_GET["h"], true);
		    }
		    file_put_contents($cachedir."/".$cachefile, $image);
		} else {
			$image=thumbnail__view__gd(realpath($file),$_GET["w"], $_GET["h"]);
			header('Content-type: image/jpeg');
			if ($type==3) imagepng($image);
			if ($type==2) imagejpeg($image);
			if ($type==1) imagegif($image);
			file_put_contents($cachedir."/".$cachefile, $image);
			die;
		}
	} else {
            $image=file_get_contents($cachedir."/".$cachefile);
        }
        header("Content-Type: ".$mime);
        echo $image;
    }
}

function thumbnail__view__gd($imgSrc,$thumbnail_width,$thumbnail_height) { //$imgSrc is a FILE - Returns an image resource.
    //getting the image dimensions
    list($width_orig, $height_orig, $type) = getimagesize($imgSrc);
	// Image is PNG:
	if ($type == IMAGETYPE_PNG){
	    $myImage = imagecreatefrompng(realpath($imgSrc));
	}

	// Image is JPEG:
	else if ($type == IMAGETYPE_JPEG){
	    $myImage = imagecreatefromjpeg(realpath($imgSrc));
	}
	// Image is GIF:
	else if ($type == IMAGETYPE_GIF){
	    $myImage = imagecreatefromgif(realpath($imgSrc));
	}

    $ratio_orig = $width_orig/$height_orig;

    if ($thumbnail_width/$thumbnail_height > $ratio_orig) {
       $new_height = $thumbnail_width/$ratio_orig;
       $new_width = $thumbnail_width;
    } else {
       $new_width = $thumbnail_height*$ratio_orig;
       $new_height = $thumbnail_height;
    }

    $x_mid = $new_width/2;  //horizontal middle
    $y_mid = $new_height/2; //vertical middle

    $process = imagecreatetruecolor(round($new_width), round($new_height));

    imagecopyresampled($process, $myImage, 0, 0, 0, 0, $new_width, $new_height, $width_orig, $height_orig);
    $thumb = imagecreatetruecolor($thumbnail_width, $thumbnail_height);
    imagecopyresampled($thumb, $process, 0, 0, ($x_mid-($thumbnail_width/2)), ($y_mid-($thumbnail_height/2)), $thumbnail_width, $thumbnail_height, $thumbnail_width, $thumbnail_height);

    imagedestroy($process);
    imagedestroy($myImage);
    return $thumb;
}

?>
