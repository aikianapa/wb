<?php
function lang_init() {
	$els=wbTreeRead("languages");
	if ($els!==null) {
		$els=$els["tree"];
		$langs=array();
	} else {
		$els = wbListLocales();
	}
	$lang=null;
	foreach($els as $i => $l) {
		$langs[]=$l["id"];
		if ($lang==null) {$lang=$l["id"];}
	}
	if (isset($_ENV["route"]["params"][0])) $p=$_ENV["route"]["params"][0];
	if (in_array($p,$langs)) {$lang=$p;}
	$_SESSION["lang"]=$lang;
	$check=explode("/",$_SERVER["REQUEST_URI"]);
	session_write_close();
	Header("HTTP/1.0 200 OK");
	if (count($check)>2 AND $check[1]=="module" AND $check[2]=="lang") {
		header("Refresh:0; url=/");
	} else {
		header("Refresh:0; url=".$_SERVER["HTTP_REFERER"]);
	}
	exit;
}
?>
