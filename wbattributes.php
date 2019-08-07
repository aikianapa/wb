<?php
	if (is_object($this->DOM)) {
		$_attributes=$this->DOM->attributes();
        $that=$this->DOM;
	} else {
		$_attributes=$this->attributes();
        $that=$this;
	}
    if ($_attributes) {

		if ($that->attr("data-wb") > "" ) {
            $wbd = $this->attr("data-wb");
            if (substr($wbd,0,1) == "{" AND substr($wbd,-1,1) == "}") {
                $params=json_decode($wbd,true);
            } else {
                parse_str($wbd,$params);
            }
            
			foreach($params as $key => $val) {
				$$key = $val;
                if ($key=="hide") $that->attr("data-wb-hide",$val);
                if ($key=="from") $that->attr("data-wb-from",$val);
			}
		}    
        
    foreach ($_attributes as $attr) {
		$tmp=$attr->name;
		if (strpos($tmp,"ata-wb-")) {$tmp=str_replace("data-wb-","",$tmp); $$tmp=$attr->value()."";} else {
			$$tmp=$attr->value();
		}
	}; unset($attrs);
    }
?>
