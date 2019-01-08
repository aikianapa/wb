<?php
session_start();
require_once __DIR__."/functions.php";
wbInit();
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
		$_ENV["DOM"]->wbSetData($_ENV["ITEM"]);
		$hide=$_ENV["DOM"]->find("[data-wb-hide]");
		foreach($hide as $h) {$h->tagHideAttrs();}
	}
	$_ENV["DOM"]->wbTargeter();
	$_ENV["DOM"]->wbClearClass();
	if ( isset($_ENV["settings"]["beautifyHtml"]) AND $_ENV["settings"]["beautifyHtml"]=="on" ) {
		$html = $_ENV["DOM"]->beautyHtml();
	} else {
		$html = $_ENV["DOM"]->outerHtml();
	}
}
if ($_ENV["route"]["mode"]=="show") $html=wbClearValues($html);
if ($cache["check"]===null) {
	file_put_contents($cache["path"],$html);
} else if ($cache["check"]===true) {
	$html=$cache["data"];
}
session_write_close();
echo $html;
?>
