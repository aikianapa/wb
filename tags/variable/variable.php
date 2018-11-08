<?php
class tagVariable extends kiNode  {

    function __construct($that){
        $this->DOM = $that;
    }

    public function __destruct(){
        unset($this->DOM);
    }

    public function tagVariable($Item) {
	if ($this->DOM->is("[data-wb-role=variable]") OR $this->DOM->is("[role=variable]")) {
		$this->DOM->tagVariable_set($Item);
	} else if ($this->DOM->find("[data-wb-role=variable],[role=variable]")->length) {
		$vars=$this->DOM->find("[data-wb-role=variable],[role=variable]");
		foreach($vars as $v) {
			$v->tagVariable_set($Item);
		}
	}
    }

    public function tagVariable_set($Item) {
	include($_ENV["path_engine"]."/wbattributes.php");
        if ($var>"") {
            if (strtoupper(substr($var,0,5))=="_SESS") {
                $var=str_replace(array('[',']','_SESS'),array('["','"]','_SESSION'),$var);
                eval('$'.$var.' = "'.$this->DOM->attr("value").'";');
            } else {
                $_ENV["variables"][$var]="";
                if (!isset($where)) $where=$if;
            if ($where=="") {
                    $_ENV["variables"][$var]=wbSetValuesStr($this->DOM->attr("value"),$Item);
                } else {
                    if (wbWhereItem($Item,$where)) {
                        $_ENV["variables"][$var]=wbSetValuesStr($this->DOM->attr("value"),$Item);
                    } else {
                        $_ENV["variables"][$var]=wbSetValuesStr($this->DOM->attr("else"),$Item);
                    }
                }
                $tmp=substr($_ENV["variables"][$var],0,1);
                if ($tmp=="{" OR $tmp=="]" AND is_array(json_decode($_ENV["variables"][$var],true))) {
                    $array=json_decode($_ENV["variables"][$var],true);
                    if (is_array($array)) {
                        $_ENV["variables"][$var]=$array;
                    }
                }
                unset($array,$tmp);

                if (isset($oconv) AND $oconv>"") {
                    $_ENV["variables"][$var]=wbOconv($_ENV["variables"][$var],$oconv);
                }
            }
        }
        if ($this->DOM->attr("data-wb-hide")!=="false") {
            $this->DOM->remove();
        }
    }

}
