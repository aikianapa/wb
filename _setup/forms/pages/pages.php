<?php
function pages_edit() {
	$out=wbGetForm("pages","edit");
	$id=$_ENV["route"]["item"];
	$Item=wbItemRead("pages",$id);
	if ($id=="_new") {
		$Item["id"]=wbNewId();
		$Item["template"]=$_ENV["settings"]["template"];
	}
	$Item["tpllist"]=wbListTpl();
	$Item=wbCallFormFunc("BeforeItemEdit",$Item);
	$out->wbSetData($Item);
	$options=$out->find("select[name=template] option");
	foreach($options as $opt) {
		if (strpos($opt->attr("value"),".inc.")) $opt->remove();
	}
	return $out;
}

function pagesAfterItemRead($Item=null) {
    if ($Item!==null) {
        if ($_ENV["route"]["mode"]=="show") {
            if (!isset($Item["title"]) OR $Item["title"]=="") {$Item["title"]=$Item["header"];}
            if ($Item["title"]=="") {$Item["title"]=$_ENV["settings"]["header"];}
        }
        if ($_ENV["route"]["mode"]=="list") {
		$Item=wbItemToArray($Item);
		$Item["header"]=$Item["lang"][$_SESSION["lang"]]["name"];
        }
    }
	return $Item;
}




?>
