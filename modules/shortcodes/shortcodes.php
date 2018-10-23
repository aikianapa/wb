<?php
function shortcodes__init()
{
    if (isset($_ENV["route"]["params"][0])) {
        $mode=$_ENV["route"]["params"][0];
        $call="shortcodes__{$mode}";
        if (is_callable($call)) {
            $out=@$call();
       }
    } else {
	$out=shortcodes__ui();
    }
    return $out;
}

function shortcodes__ui() {
	$out=wbFromFile(__DIR__ ."/shortcodes_ui.php",true);
	$Item=wbItemRead("admin","shortcodes");
	if ($_ENV["last_error"]=="1006") {
		$Item=wbItemRead("admin:engine","shortcodes");
	}
	$out->wbSetData($Item);
	return $out;
}
?>
