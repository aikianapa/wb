<?php

include_once 'engine/weprocessor/weprocessor.php';

function wbSetValuesStr($tag = "",$Item = array(), $limit = 2, $vars = null)
{
    $processor = new WEProcessor($Item);
    return $processor->substitute($tag);
}

?>
