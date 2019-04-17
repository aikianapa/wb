<?php
class tagEditable extends kiNode  {
	function __construct($that) {
		$this->DOM = $that;
	}

	public function __destruct() {
		unset($this->DOM);
	}

	public function tagEditable($Item=array()) {
		if (
			$this->DOM->attr("data-allow")!=="*" AND (
				$this->DOM->attr("data-allow")=="" OR
				$this->DOM->attr("data-disallow")=="*" OR
				($this->DOM->attr("data-allow")>"" AND !wbRole($this->DOM->attr("data-allow")) ) OR
				($this->DOM->attr("data-disallow")>"" AND wbRole($this->DOM->attr("data-disallow")) )
			)
		) {
			$this->DOM->removeAttr("data-wb-editable");
			$this->DOM->removeAttr("data-allow");
			$this->DOM->removeAttr("data-disallow");
			return;
		}
		$param=$this->DOM->attr("data-wb-editable");
		//$this->DOM->attr("data-wb-editable-inner",base64_encode($this->DOM->html()));
		//$this->DOM->attr("data-wb-editable-route",base64_encode(json_encode($_ENV["route"])));
		//$this->DOM->attr("data-wb-editable-item",base64_encode(json_encode($Item)));
		$this->DOM->append('<script data-wb-append="body">wb_include("/engine/tags/editable/editable.js");</script>');
	}
}

	function ajax__wb_tag_editable() {
		$_ENV["route"]=json_decode(base64_decode($_POST["route"]),true);
		$inner=base64_decode($_POST["inner"]);
		$param=$_POST["param"];
		$text = str_replace('"',"&quot;",$_POST["text"]);
		if ($param[0]=="/") {$param=substr($param,1);}
		$param=explode("/",$param);
		$form 	= $param[0];
		$item 	= $param[1];
		$field = $param[2];
		$Item=wbItemRead($form,$item);
		$idx='$Item';
		for($i=2;$i<count($param);$i++) {
			$idx.='["'.$param[$i].'"]';
		}
		eval($idx." = ".' "'.$text.'";');
		foreach($Item as $fld => $val) {
			if ($fld == "id" OR $fld == $field OR $fld[0]=="_") {} else {unset($Item[$fld]);}
		}
		wbItemSave($form,$Item,true);
		print_r($Item);
	}
