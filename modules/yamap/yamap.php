<?php
function yamap__init() {
	if (isset($_ENV["route"]["params"][0])) {
		$mode=$_ENV["route"]["params"][0];
		$call="yamap__{$mode}";
		if (is_callable($call)) {$out=@$call();}
		die;
	} else {
		$out=wbFromFile(__DIR__ ."/yamap_canvas.php");
		return $out;
	}
}

function yamap__afterRead(&$out,$Item,$dom) {
	$field=$dom->attr("name");
	if ($field > "") {

		$out->find("div.yamap .yamap_data")->attr("name",$field);
		$out->find("div.yamap .yamap_editor")->attr("name",$field);
	}
}

function yamap__beforeShow($out,$Item) {
	if (!$out->find("[editable]")->length) {
		$out->find(".yamap_editor")->remove();
	}
}
?>
