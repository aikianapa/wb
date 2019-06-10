<?php

include_once(__DIR__."/weprocessor/weprocessor.php");

function wbSetValuesStr($tag = "",$Item = array(), $limit = 2, $vars = null)
{
	if (is_object($tag)) $tag = $tag->outerHtml();
    $processor = new WEProcessor($Item);
    return $processor->substitute($tag);
}
?>
