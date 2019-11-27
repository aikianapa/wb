<?php

function thumbnails_controller()
{
    $params=$_ENV["route"]["params"];
    if (!isset($_GET)) {
        $_GET=array();
        $_GET["w"]=$params["w"];
        unset($params["w"]);
        $_GET["h"]=$params["h"];
        unset($params["h"]);
    }
    if (isset($params["src"])) {
        $_GET["src"]=$params["src"];
        unset($params["src"]);
    } else {
        $_GET["src"].="/".implode("/", $params);
    }
    $_GET["zc"]=$_ENV["route"]["zc"];
    thumbnail_view();
    die;
}

function thumbnail_view()
{
    $remote = false;
    $cache = true;
    if (isset($_ENV["route"]["http"])) {
        $remote=true;
        $p="http";
    }
    if (isset($_ENV["route"]["https"])) {
        $remote=true;
        $p="https";
    }
    if (isset($_ENV["route"]["params"]) and in_array("nocache", $_ENV["route"]["params"])) {
        $cache=false;
    }

    if (isset($_ENV["route"]["params"]) and isset($_ENV["route"]["params"][0])) {
        $tmp=base64_decode($_ENV["route"]["params"][0]);
        if (strpos($tmp, "ttp://") or strpos($tmp, "ttps://")) {
            $remote = true;
            $url = $tmp;
        }
    }

    if ($remote) {
        if (!isset($url)) {
            $url=$p.substr($_ENV["route"]["uri"], strpos($_ENV["route"]["uri"], "://"));
        }
        $ext = pathinfo($url, PATHINFO_EXTENSION);
        $file=$_ENV["path_app"]."/uploads/_remote/".md5($url).".".$ext;
        if (!is_file($file) or !$cache) {
            $image=file_get_contents($url);
            wbPutContents($file, $image);
        }
    } else {
        $file=$_ENV["path_app"]."/".$_GET["src"];
    }

    if (is_file($file)) {
        list($width, $height, $type) = $size = getimagesize($file);
        $ext = pathinfo($file, PATHINFO_EXTENSION);
        $mime=$size["mime"];
        $cachefile=md5($file."_".$_GET["w"]."_".$_GET["h"]."_".$_GET["ox"]."_".$_GET["oy"]).".".$ext;
        $cachedir=$_ENV["path_app"]."/uploads/_cache/".substr($cachefile, 0, 2);
        $tw = $_GET["w"];
        $th = $_GET["h"];
        if (!is_dir($cachedir)) {
            $u=umask();
            mkdir($cachedir, 0766, true);
            umask($u);
        }
        if (!is_file($cachedir."/".$cachefile) and $cache) {
            if (class_exists("Imagick")) {
                $image = new \Imagick(realpath($file));
                if ($remote) {
                    unlink($file);
                }
                $scale = 1;
//                if ($tw > $width OR $th > $height) {
                $scale_w = $tw / $width;
                $scale_h = $th / $height;
                if ($scale_h > $scale_w) {
                    $scale = $scale_h;
                } else {
                    $scale = $scale_w;
                }

                $width = $width * $scale;
                $height = $height * $scale;
                $image->resizeImage($width, $height, false, 1, false);
                // resise source
//                }
                //echo "width: $width<br>height: $height<br>$scale";die;

                // $image->thumbnailImage($_GET["w"], $_GET["h"] * (1+$scale), true);




                //echo "swidth: $width<br>sheight: $height<br>width: $new_width<br>height: $new_height<br>$scale";die;


                if ($_GET["zc"]!==1) {
                    $ox = ($tw / 100 * $_GET["ox"]) / 2;
                    if ($th>=$height) {
                        $oy=0;
                    } else {
                        $oy = (($height - $th) / 100 * $_GET["oy"]);
                    }
                    if ($tw>=$width) {
                        $ox=0;
                    } else {
                        $ox = (($width - $tw) / 100 * $_GET["ox"]);
                    }

                    //echo "swidth: $width<br>sheight: $height<br>x: $ox<br>y: $oy<br>$scale";die;

                    $image->cropImage($tw, $th, $ox, $oy);
                } else {
                    $image->resizeImage($tw, $th, false, 1, false);
                }
                file_put_contents($cachedir."/".$cachefile, $image);
            } else {
                $image=thumbnail_view_gd(realpath($file), $_GET["w"], $_GET["h"]);
                header("Content-Type: ".$mime);
                if ($type==3) {
                    imagepng($image, $cachedir."/".$cachefile);
                }
                if ($type==2) {
                    imagejpeg($image, $cachedir."/".$cachefile);
                }
                if ($type==1) {
                    imagegif($image, $cachedir."/".$cachefile);
                }
                $image=file_get_contents($cachedir."/".$cachefile);
            }
        } else {
            $image=file_get_contents($cachedir."/".$cachefile);
        }
        header("Content-Type: ".$mime);
        echo $image;
    }
}

function thumbnail_view_gd($imgSrc, $thumbnail_width, $thumbnail_height)
{ //$imgSrc is a FILE - Returns an image resource.
    //getting the image dimensions
    list($width_orig, $height_orig, $type) = getimagesize($imgSrc);
    // Image is PNG:
    if ($type == IMAGETYPE_PNG) {
        $myImage = imagecreatefrompng(realpath($imgSrc));
    }

    // Image is JPEG:
    elseif ($type == IMAGETYPE_JPEG) {
        $myImage = imagecreatefromjpeg(realpath($imgSrc));
    }
    // Image is GIF:
    elseif ($type == IMAGETYPE_GIF) {
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
