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
	$_SERVER["QUERY_STRING"]="/engine/phpThumb/phpThumb.php?src={$_GET["src"]}&w={$_GET["w"]}&h={$_GET["h"]}&zc={$_GET["zc"]}";
	header("Location: ".$_SERVER["QUERY_STRING"]);
	die;
}
?>
