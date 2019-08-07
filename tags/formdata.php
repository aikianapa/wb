<?php
function tagFormdata(&$dom,$Item=null) {
    $mode="show";
    if ($Item==null && isset($dom->data)) $Item=$dom->data;
    if ($Item==null && !isset($dom->data)) $Item=[];
    if (isset($dom->params->form)) $table = $dom->params->form;
    if (isset($dom->params->table)) $table = $dom->params->table;
    if (isset($dom->params->mode)) $mode = $dom->params->mode;
    $srcItem=$Item;
// get items from table
        if (isset($dom->params->form) AND in_array($dom->params->form,$_ENV["forms"])) {
            $Item=wbItemRead($dom->params->form,$dom->params->item);
        } else if (isset($dom->params->form) AND !in_array($dom->params->form,$_ENV["forms"])) {
            $Item=[];
        }
        if (isset($dom->params->table) AND in_array($dom->params->table,$_ENV["forms"])) {
            $Item=wbItemRead($dom->params->table,$dom->params->item);
        } else if (isset($dom->params->table) AND !in_array($dom->params->table,$_ENV["forms"])) {
            $Item=[];
        }
// get items from array
        if (isset($dom->params->from) AND isset($Item[$dom->params->from])) {
            $Item=$Item[$dom->params->from];
        } else if (isset($dom->params->from) AND !isset($Item[$dom->params->from])) {
            $Item=[];
        }

        if (isset($vars) AND $vars>"") $Item=wbAttrAddData($vars,$Item);
        if (isset($json) AND $json>"") $Item=json_decode($json,true);
        

        if (isset($field) AND $field>"") $Item=wbGetDataWbFrom($Item,$field);
        if (isset($vars) AND $vars>"") $Item=wbAttrAddData($vars,$Item);
        if (isset($call) AND is_callable($call)) $Item=$call($Item);

        if (isset($table)) {
				$Item=wbCallFormFunc("BeforeShowItem",$Item,$table,$mode);
				$Item=wbCallFormFunc("BeforeItemShow",$Item,$table,$mode);
		}
        if (isset($clear) AND $clear=="true") {$clear=true;} else {$clear=false;}
        $Item["_parent"] = $srcItem;
        $dom->data = $Item;
        $dom->fetch();
        $dom->done=true;
        return $dom;
}
?>