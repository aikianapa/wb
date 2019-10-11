<?php
class tagFormdata extends kiNode  {

    function __construct($that){
        $this->DOM = $that;
    }

    public function __destruct(){
        unset($this->DOM);
    }

    public function tagFormData($Item=array()) {
		$clear;
        include($_ENV["path_engine"]."/wbattributes.php");

        $srcItem=$Item;

        if (isset($form) AND !isset($table)) $table=$form;
        if (isset($vars) AND $vars>"") $Item=wbAttrAddData($vars,$Item);
        if (isset($from) AND $from>"") $Item=wbGetDataWbFrom($Item,$from);
        if (isset($json) AND $json>"") $Item=json_decode($json,true);
        if (!isset($mode) OR $mode=="") $mode="show";
        if (isset($table) AND $table>"") {
            if (isset($item) AND $item>"") {
                $Item=wbItemRead(wbTable($table),$item);
                $Item=wbItemToArray($Item);
            } else {
                $Item=[];
            }
        }
        if (isset($field) AND $field>"") $Item=wbGetDataWbFrom($Item,$field);
        if (isset($vars) AND $vars>"") $Item=wbAttrAddData($vars,$Item);
        if (isset($call) AND is_callable($call)) $Item=$call($Item);
        if ((array)$srcItem === $srcItem) {
            foreach($srcItem as $k => $v) {
                $Item["%{$k}"]=$v;
            };
        }
        if (isset($table)) {
				$Item=wbCallFormFunc("BeforeShowItem",$Item,$table,$mode);
				$Item=wbCallFormFunc("BeforeItemShow",$Item,$table,$mode);
		}
        if (isset($clear) AND $clear=="true") {$clear=true;} else {$clear=false;}
		$this->DOM->wbSetData($Item,$clear);
    }


}
