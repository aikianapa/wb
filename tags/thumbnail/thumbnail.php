<?php
class tagThumbnail extends kiNode  {

    function __construct($that){
        $this->DOM = $that;
    }

    public function __destruct(){
        unset($this->DOM);
    }

    public function tagThumbnail($Item=array()) {
	include($_ENV["path_engine"]."/wbattributes.php");
        $bkg=false;
        $img="";
        $src=$this->DOM->attr("src");
        if ($src=="") {
            $src="0";
        }
        $noimg=$this->DOM->attr("data-wb-noimg");
        $form=$this->DOM->attr("data-wb-form");
        if ($form=="" && isset($Item["form"])) {
            $form=$Item["form"];
        }
        $from=$this->DOM->attr("data-wb-from");
        if ($from=="") {
            $from="images";
        }
        $item=$this->DOM->attr("data-wb-item");
        if ($item=="" && isset($Item["id"])) {
            $item=$Item["id"];
        }
        $show=$this->DOM->attr("data-wb-show");
        $class=$this->DOM->attr("class");
        $style=$this->DOM->attr("style");
        $width=$this->DOM->attr("width");
        if ($width=="") {
            $width=$_ENV['thumb_width']."px";
        }
        $height=$this->DOM->attr("height");
        if ($height=="") {
            $height=$_ENV['thumb_height']."px";
        }
        $offset=$this->DOM->attr("offset");
        $contain=$this->DOM->attr("contain");

        if ($form>"" && $item>"") {
            $Item=wbItemRead($form,$item);
        }
        $json=$this->DOM->attr('json');
        if ($json>"") {
            $images=json_decode($json,true);
        }
        else {
            if (isset($Item[$from])) {
                if (is_array($Item[$from])) {
                    $images=$Item[$from];
                }
                else {
                    $images=json_decode($Item[$from],true);
                }
            } else {
                $images="";
            }
        }
        if (!isset($idx) AND is_numeric($src)) {
            $idx=$src;
        }
        else {
            $idx=0;
        }
        if (is_array($images) AND is_numeric($src)) {
            if (isset($images[$idx])) {
                $img=trim($images[$idx]["img"]);
            }
            else {
                $img="";
            }
            if (substr($img,0,1)=="/") {
                $src=$img;
            }
            else {
                $src=wbGetItemImg($Item,$idx,$noimg,$from,true);
            }
            $src=str_replace($_ENV["path_app"],"",$src);
            $img=explode($src,"/");
            $img=$img[count($img)-1];
            $this->DOM->attr("src",$src);
        }
        $srcSrc=$src;
        $srcImg=explode("/",trim($src));
        $srcImg=$srcImg[count($srcImg)-1];
        $srcExt=explode(".",strtolower(trim($srcImg)));
        $srcExt=$srcExt[count($srcExt)-1];
        $exts=array("jpg","jpeg","gif","png","svg","pdf");

        if (!in_array($srcExt,$exts)) {
            $src="/engine/uploads/__system/filetypes/{$srcExt}.png";
            $img="{$srcExt}.png";
            $ext="png";
        }



        if (is_numeric($this->DOM->attr("src"))) {
            $idx=$this->DOM->attr("src");
            $this->DOM->removeAttr("src");
            $num=true;
        }
        else {
            $idx=$this->DOM->parents("[idx]")->attr("idx");
            if ($idx>"" && $src=="") {
                $num=true;
            }
            else {
                $idx=0;
            }
        }
        if ($this->DOM->attr("data-wb-size")>"") {
            $size=$this->DOM->attr("data-wb-size");
        }
        if (!isset($size) OR $size=="") {
            $size=$this->DOM->attr("size");
        }
        if ($size=="" AND isset($Item["intext_position"])) {
            if (isset($Item["intext_position"]["width"]) AND $Item["intext_position"]["width"]>"") {
                $width=$Item["intext_position"]["width"];
            }
            else {
                $width=$_ENV["thumb_width"];
            }
            if (isset($Item["intext_position"]["height"]) AND $Item["intext_position"]["height"]>"") {
                $height=$Item["intext_position"]["height"];
            }
            else {
                $height=$_ENV["thumb_height"];
            }
            $size="{$width}px;{$height}px;src";
        }
        if ($size>"") {
            $size=explode(";",$size);
            if (count($size)==1) {
                $size[1]=$size[0];
            }
            $width=$size[0];
            $height=$size[1];
            if (isset ($size[2]) && $size[2]=="src") {
                $bkg=false;
            }
            else {
                $bkg=true;
            }
        }
        if ($offset>"") {
            $offset=explode(";",$offset);
            if (count($offset)==1) {
                $offset[1]=$offset[0];
            }
            $top=$offset[1];
            $left=$offset[0];
        } else {
            $top="15%";
            $left="50%";
        }

        if (!is_file($_ENV["path_app"].$src) && !is_file($_SERVER["DOCUMENT_ROOT"].$src)) {
            if (isset($Item["img"]) && !isset($Item[$from]) && isset($Item["%{$from}"]) && !is_file($src)) {
                $tmpItem=array();
                $tmpItem[$from]=$Item["%{$from}"];
                $tmpItem["form"]=$Item["%form"];
                $tmpItem["id"]=$Item["%id"];
                $src=wbGetItemImg($tmpItem,$idx,$noimg);
            } else {
                if ($noimg>"") {
                    $src=$noimg;
                } else {
                    if ($bkg==true) {
                        $src="/engine/uploads/__system/image.svg";
                        $img="image.svg";
                        $ext="svg";
                    }
                    if ($bkg==false) {
                        $src="/engine/uploads/__system/image.jpg";
                        $img="image.jpg";
                        $ext="jpg";
                    }
                }
            }
        }

        $img=explode("/",trim($src));
        $img=$img[count($img)-1];
        $ext=explode(".",trim($src));
        $ext=$ext[count($ext)-1];
        $this->DOM->src=$src;

        if ($src==array()) {
            $src="";
        }
        if ($img=="" AND $bkg==true) {
            $src="/engine/uploads/__system/image.svg";
            $img="image.svg";
            $ext="svg";
        }
        if ($src=="" AND $bkg==true) {
            $src="/engine/uploads/__system/image.svg";
            $img="image.svg";
            $ext="svg";
        }
        else {
            if ($src=="" AND $bkg==false) {
                $src="/engine/uploads/__system/image.jpg";
                $img="image.jpg";
                $ext="jpg";
            }
            $img=explode("/",$src);
            $img=$img[count($img)-1];
            $this->DOM->attr("img",$img);
            $ext=substr($img,-3);
        }

        if ($ext!=="svg") {
            if ($contain=="true") {
                $thumb="thumbc";
            }
            else {
                $thumb="thumb";
            }
            $src=urldecode($src);
            list( $w, $h, $t ) = getimagesize($_SERVER["DOCUMENT_ROOT"].$src);
            if (substr($width,-2)=="px") {
                $width=substr($width,0,-2)*1;
            }
            if (substr($height,-2)=="px") {
                $height=substr($height,0,-2)*1;
            }
            if (substr($width,-1)=="%") {
                $w=ceil($w/100*(substr($width,0,-1)*1));
            } else {
                $w=$width;
            }

            if (substr($height,-1)=="%" ) {
                $h=ceil($h/100*(substr($height,0,-1)*1));
            } else {
                $h=$height;
            }

            $src="/{$thumb}/{$w}x{$h}/src{$src}";
        }

        if ($bkg==true) {
            if (!in_array($srcExt,$exts) OR $contain=="true") {
                $bSize="contain";
            }
            else {
                $bSize="cover";
            }
            if (is_numeric($width)) {
                $width.="px";
            }
            if (is_numeric($height)) {
                $height.="px";
            }
            //$style="width:{$width}; height: {$height}; background: url('{$src}') {$left} {$top} no-repeat; display:inline-block; background-size: {$bSize}; background-clip: content-box;".$style;
            $style="background: url('{$src}') {$left} {$top} no-repeat; display:inline-block; background-size: {$bSize}; background-clip: content-box;".$style;
            $this->DOM->attr("src","/engine/uploads/__system/transparent.png");
            $this->DOM->attr("width",$width);
            $this->DOM->attr("height",$height);
        } else {
            if ($ext!=="svg") {
//			$src.="&h={$height}&zc=1";
                $this->DOM->attr("src",$src);
            }
        }
        $this->DOM->removeAttr("data-wb-size");
        $this->DOM->removeAttr("size");
        $this->DOM->attr("data-src",$srcSrc);
        $this->DOM->attr("data-ext",$srcExt);
        $this->DOM->attr("class",$class);
        $this->DOM->attr("noimg",$noimg);
        $this->DOM->attr("style",$style);
        $this->DOM->removeAttr('json');
    }


}
