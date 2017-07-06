<?php
switch($_ENV["route"]["params"][1]) {
	case "list_forms":
		if (isset($_POST["tpl"])) {$tpl=wbFromString($_POST["tpl"]);}
		$forms=array();
		$forms[]=array("name"=>"pages");
		$forms[]=array("name"=>"orders");
		$forms=array("forms"=>$forms);
		$tpl->wbSetData($forms);
		echo $tpl;
	break;
	
}
?>
