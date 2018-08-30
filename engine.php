<?php
if (!isset($_SESSION)) {session_start();}
require_once __DIR__."/functions.php";
wbInit();
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
	$hide=$_ENV["DOM"]->find("data-wb-hide");
	foreach($hide as $h) {$h->tagHideAttrs();}
}
$_ENV["DOM"]->wbTargeter();
$_ENV["DOM"]->wbClearClass();

session_write_close();
echo $_ENV["DOM"]->beautyHtml(); die;

echo $_ENV["DOM"]->outerHtml();
?>
