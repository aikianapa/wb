<?php
    $_attributes=$this->attributes();
    if ($_attributes) {
	$attrs=new IteratorIterator($_attributes);
	foreach ($attrs as $attr) {
		$tmp=$attr->name;
		if (strpos($tmp,"ata-wb-")) {$tmp=str_replace("data-wb-","",$tmp); $$tmp=$attr->value()."";}
	}; unset($attrs);
    }
?>
