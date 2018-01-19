<?php
function yamap__init() {
	if (isset($_ENV["route"]["params"][0])) {
		$mode=$_ENV["route"]["params"][0];
		$call="yamap__{$mode}";
		if (is_callable($call)) {$out=@$call();}
		die;
	} else {
		$out=file_get_contents(__DIR__ ."/yamap_canvas.php");
		return $out;
	}
}
?>
