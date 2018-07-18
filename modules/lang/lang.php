<?php
function lang_init() {
    $els=wbTreeRead("languages");
	$els=$els["tree"];
	$langs=array();
	$lang=null;
	foreach($els as $i => $l) {
		$langs[]=$l["id"];
		if ($lang==null) {$lang=$l["id"];}
	}
	if (isset($_ENV["route"]["params"][0])) $p=$_ENV["route"]["params"][0];
	if (in_array($p,$langs)) {$lang=$p;}
	$_SESSION["lang"]=$lang;
	header("Location: ".$_SERVER["HTTP_REFERER"]);
	die;
}
?>
