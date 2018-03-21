<?php
if (!isset($_SESSION)) {session_start();}
require_once __DIR__."/functions.php";
wbInit();
$_ENV["ITEM"]=array();
if (!isset($_ENV["route"]["form"]) OR $_ENV["route"]["form"]!=="default_form") {
	$_ENV["DOM"]=wbFromString(""); $_ENV["ITEM"]=array();
	if (is_callable("wbBeforeEngine")) {$_ENV["ITEM"] = wbBeforeEngine();}
	if (is_callable("wbCustomEngine")) {$_ENV["DOM"]  = wbCustomEngine();} else {wbLoadController();}
	if (is_callable("wbAfterEngine"))  {$_ENV["ITEM"] = wbAfterEngine();}
} else {
	if (!is_dir($_ENV["path_app"]."/form")) {wbLoadController();} else {
		$_ENV["DOM"]=wbFromString(wbErrorOut(404));
	}
}
if (!is_object($_ENV["DOM"])) {$_ENV["DOM"]=wbFromString($_ENV["DOM"]);}
if ($_ENV["route"]["controller"]!=="module") {
	// чтобы вставки модуля не пометились .wb-done
	$_ENV["DOM"]->wbSetData($_ENV["ITEM"]);
	$hide=$_ENV["DOM"]->find("data-wb-hide");
	foreach($hide as $h) {$h->tagHideAttrs();}
}
$_ENV["DOM"]->wbTargeter();
//echo $_ENV["DOM"]->beautyHtml();
session_write_close();
echo $_ENV["DOM"]->outerHtml();
?>
