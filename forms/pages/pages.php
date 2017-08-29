<?php
function pages__edit() {
	$out=wbGetForm("pages","edit");
	$id=$_ENV["route"]["item"];
	$Item=wbItemRead("pages",$id);
	if ($id=="_new") {
		$Item["id"]=wbNewId();
		$Item["template"]=$_ENV["settings"]["template"];
	}
	$Item["tpllist"]=wbListTpl();
	$out->wbSetData($Item);
	$options=$out->find("select[name=template] option");
	foreach($options as $opt) {
		if (strpos($opt->attr("value"),".inc.")) $opt->remove();
	}
	return $out;
}



/*

function page__list() {
	$out=aikiGetForm($_GET["form"],$_GET["mode"]);
	$Item=aikiListItems("page");
	$Item["result"]=array_sort($Item["result"],"id");
	$out->contentSetData($Item);
	$out->find("div.modal")->attr("id","pageEdit");
	$out->find("div.modal")->attr("data-backdrop","static");
	$out->find("[data-formsave]")->attr("data-formsave","#pageEditForm");
	$out->find(".modal-title")->html("Редактирование страницы");
	return $out->outerHtml();
}

function page__show($out=null,$Item=null) {
	if ($Item==null) $Item=$_SESSION["Item"];
	if ($out==null) {
		if (isset($Item["template"]) && $Item["template"]>"") {$out=aikiGetTpl($Item["template"]);} else {$out=aikiGetForm();}
	}
	return common__show($Item);
}

function page__getajax() {
	$Item=aikiReadItem($_GET["form"],$_GET["item"]);
	$out=aikiGetForm($_GET["form"],$_GET["mode"]);
	if (!is_object($out)) {
		$out=aikiGetForm($_GET["form"],"show");
		$out->contentSetData($Item);
	} else {
		$out=aikiFromString(page__show($out,$Item));
	}
	if (is_callable("pageChangeHtml")) {pageChangeHtml($out,$Item);}
	return $out->outerHtml();
}

function _pageBeforeShowItem($Item) {
	if (isset($Item["tags"])) $Item["tags"]=explode(",",$Item["tags"]);
	if ($_GET["mode"]=="show") {$Item=aikiAddItemGal($Item);}
	return $Item;
}

function _pageAfterReadItem($Item) {
	if ($_GET["mode"]=="list") {unset($Item["text"]);}
	return $Item;
}

*/


?>
