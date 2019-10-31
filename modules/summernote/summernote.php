<?php
function summernote__init(&$dom) {
	if (isset($_ENV["route"]["params"][0])) {
		$mode=$_ENV["route"]["params"][0];
		$call="summernote__{$mode}";
		if (is_callable($call)) {$out=@$call();}
		die;
	} else {
		$out = wbFromFile(__DIR__ ."/summernote-ui.php");
        $ats = $dom->attributes();
        foreach( $ats as $atn => $atv ) {
            if (!strpos(" ".$atn,"data-wb")) {
                $out->find(".summernote")->attr($atn,$atv);
            }
        }
        $out->wbSetData();
        return $out;
	}
}
?>