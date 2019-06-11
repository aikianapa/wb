<?php

include_once(__DIR__."/weprocessor/weprocessor.php");

function wbSetValuesStr($tag = "",$Item = array(), $limit = 2, $vars = null)
{
	if (is_object($tag)) $tag = $tag->outerHtml();
	if (!strpos($tag,"}}")) return $tag;
	$Item=wbItemToArray($Item,false);
    $processor = new WEProcessor($Item);
    $tag=$processor->substitute($tag);
    return $tag;
}
?>
