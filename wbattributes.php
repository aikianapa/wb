<?php
	if (is_object($this->DOM)) {
		$_attributes=$this->DOM->attributes();
	} else {
		$_attributes=$this->attributes();
	}
    if ($_attributes) {
	foreach ($_attributes as $attr) {
		$tmp=$attr->name;
		if (strpos($tmp,"ata-wb-")) {$tmp=str_replace("data-wb-","",$tmp); $$tmp=$attr->value()."";} else {
			$$tmp=$attr->value();
		}
		if ($tmp == "data-wb") {
			$json=json_decode($this->DOM->attr("data-wb"),true);
			foreach($json as $key => $val) {
				$$key = $val;
			}
		}
	}; unset($attrs);
    }
?>
