<?php
session_start();
require __DIR__."/functions.php";
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
$_ENV["DOM"]->wbSetData($_ENV["ITEM"]);
$hide=$_ENV["DOM"]->find("data-wb-hide");
foreach($hide as $h) {$h->tagHideAttrs();}
//echo $_ENV["DOM"]->beautyHtml();
echo $_ENV["DOM"]->outerHtml();
wbFlushDatabase();
?>
