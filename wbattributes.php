<?php
    $_attributes=$this->attributes();
    $_ENV["attributes"]=array();
    if ($_attributes) {
	$attrs=new IteratorIterator($_attributes);
	foreach ($attrs as $attr) {
		$tmp=$attr->name;
		if (strpos($tmp,"ata-wb-")) {$tmp=str_replace("data-wb-","",$tmp); $_ENV["attributes"][$tmp]=$$tmp=$attr->value()."";} else {
			$_ENV["attributes"][$attr->name]=$attr->value();
		}
	}; unset($attrs);
    }
?>
