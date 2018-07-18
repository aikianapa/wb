<?php
$aiki_projects=false;
include(__DIR__."/engine/engine.php");
$__page->contentSetData();
$__page->contentAppends();
if (is_callable("aikiBeforeShowHtml")) {aikiBeforeShowHtml($__page);}
echo $__page->outerHtml();
aikiClearMemory($__page);
?>
