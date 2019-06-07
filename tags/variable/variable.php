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
		$this->wbSetAttributes($Item);
		include($_ENV["path_engine"]."/wbattributes.php");
        if ($var>"") {
			//if (isset($value)) {$value=preg_replace("~\{\{(.*)\}\}~","",wbSetValuesStr($value,$Item));}
			//if (isset($else)) {$else=preg_replace("~\{\{(.*)\}\}~","",wbSetValuesStr($else,$Item));}
			//if (isset($if)) {$if=preg_replace("~\{\{(.*)\}\}~","",wbSetValuesStr($if,$Item));}


		if (isset($_ENV["variables"][$var])) {unset($_ENV["variables"][$var]);}
		if (!isset($where) OR (isset($where) AND wbWhereItem($Item,$where)) OR isset($if)) {
			if (!isset($if) OR (isset($if) AND wbWhereItem($Item,$if))) {
				$_ENV["variables"][$var]=$value;
			} else {
				$_ENV["variables"][$var]=$else;
			}

			if (isset($_ENV["variables"][$var])) {
				$tmp=substr($_ENV["variables"][$var],0,1);
				if ($tmp=="{" OR $tmp=="]" AND is_array(json_decode($_ENV["variables"][$var],true))) {
					$array=json_decode($_ENV["variables"][$var],true);
					if ((array)$array === $array) $_ENV["variables"][$var]=$array;
				}
			}

			if (isset($oconv) AND $oconv>"") {
				$_ENV["variables"][$var]=wbOconv($_ENV["variables"][$var],$oconv);
			}
		} else {
			$this->DOM->remove();
		}
		if ($hide!=="false") $this->DOM->remove();
        }
    }
}
