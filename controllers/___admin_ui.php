<?php
switch($_ENV["route"]["params"][0]) {
	case "list_forms":
		if (isset($_POST["tpl"])) {$tpl=wbFromString($_POST["tpl"]);}
		$forms=array();
		$forms[]=array("name"=>"pages","view"=>"Страницы");
		$forms[]=array("name"=>"orders","view"=>"Заказы");
		$forms[]=array("name"=>"users","view"=>"Пользователи");
		$forms=array("forms"=>$forms);
		$tpl->wbSetData($forms);
		echo $tpl;
	break;

}
?>
