<?php
$sessdir = $_SERVER["DOCUMENT_ROOT"]."/uploads/_cache/sess/";
if (!is_dir($sessdir)) mkdir($sessdir,0766,true);
ini_set('session.save_path', $sessdir);
ini_set('session.gc_probability', 5);
ini_set('session.gc_divisor', 100);
ini_set('session.gc_maxlifetime', 100);
ini_set('session.cookie_lifetime', 100);
ini_set('display_errors', 1);
session_start();
require_once __DIR__."/functions.php";
wbInit();
if (is_callable("wbAfterInit")) {wbAfterInit();}
$cache=wbCacheCheck();
if ($cache["check"] === false OR $cache["check"] === null) {
	$_ENV["ITEM"]=array();
	if (!isset($_ENV["route"]["form"]) OR $_ENV["route"]["form"]!=="default_form") {
		$exclude=in_array($_ENV["route"]["controller"],array("module","ajax","thumbnails"));
		$_ENV["DOM"]=wbFromString(""); $_ENV["ITEM"]=array();
		if (is_callable("wbBeforeEngine") AND !$exclude) {$_ENV["ITEM"] = wbBeforeEngine();}
		if (is_callable("wbCustomEngine") AND !$exclude) {$_ENV["DOM"]  = wbCustomEngine();} else {wbLoadController();}
		if (is_callable("wbAfterEngine") AND !$exclude)  {$_ENV["ITEM"] = wbAfterEngine();}
	} else {
		if (!is_dir($_ENV["path_app"]."/form")) {wbLoadController();} else {
			$_ENV["DOM"]=wbFromString(wbErrorOut(404));
		}
	}
	if (!is_object($_ENV["DOM"])) {$_ENV["DOM"]=wbFromString($_ENV["DOM"]);}
	if ($_ENV["route"]["controller"]!=="module") {
		// чтобы вставки модуля не пометились .wb-done
		$_ENV["DOM"]->wbSetData($_ENV["ITEM"],true);
		$hide=$_ENV["DOM"]->find("[data-wb-hide]");
		foreach($hide as $h) {$h->tagHideAttrs();}
	}
	$_ENV["DOM"]->wbTargeter();
	$_ENV["DOM"]->wbClearClass();
	if ( isset($_ENV["settings"]["beautifyHtml"]) AND $_ENV["settings"]["beautifyHtml"]=="on" ) {
		$scripts=$_ENV["DOM"]->find("script");
		foreach($scripts as $sc) $sc->html(base64_encode($sc->html()));
		$_ENV["DOM"] = wbFromString($_ENV["DOM"]->beautyHtml());
		$scripts=$_ENV["DOM"]->find("script");
		foreach($scripts as $sc) $sc->html(base64_decode($sc->html()));
	}
	$html = $_ENV["DOM"]->outerHtml();
}

if ($cache["check"]===null) {
	file_put_contents($cache["path"],$html);
} else if ($cache["check"]===true) {
	$html=$cache["data"];
}
session_write_close();
echo $html;
// it reduce performance twice
// gc_collect_cycles();
?>
