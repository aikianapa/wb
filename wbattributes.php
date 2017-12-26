<?php
	$attrs=new IteratorIterator($this->attributes());
	foreach ($attrs as $attr) {
		$tmp=$attr->name;
		if (strpos($tmp,"ata-wb-")) {$tmp=str_replace("data-wb-","",$tmp); $$tmp=$attr."";}
	}; unset($attrs);
?>
