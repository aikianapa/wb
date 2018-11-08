<?php
	if (is_object($this->DOM)) {
		$_attributes=$this->DOM->attributes();
	} else {
		$_attributes=$this->attributes();
	}

    if ($_attributes) {
	$attrs=new IteratorIterator($_attributes);
	foreach ($attrs as $attr) {
		$tmp=$attr->name;
		if (strpos($tmp,"ata-wb-")) {$tmp=str_replace("data-wb-","",$tmp); $$tmp=$attr->value()."";} else {
			$$tmp=$attr->value();
		}
	}; unset($attrs);
    }
?>
