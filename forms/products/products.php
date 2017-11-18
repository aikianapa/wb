<?php
function products__list() {
	$out=wbGetForm($_GET["form"],$_GET["mode"]);
	$flag=""; $where=""; $Item=array();
	if (isset($_ENV["route"]["item"]) && $_ENV["route"]["item"]>"") {$where=wbTreeWhere($_GET["form"]."_category",$_ENV["route"]["item"],"category"); $flag="category";}
	$Item["result"]=wbItemList($_GET["form"],$where);
	$Item["result"]=wbArraySort($Item["result"],"id");
	$Item["_table"]=$_GET["form"];
	$out->wbSetData($Item);
/*
	$modal=$out->find("div.modal");
	foreach($modal as $m) {
		if ($m->attr("id")=="") {
			$m->attr("id","{$_GET["form"]}Edit");
		}
		$m->attr("data-backdrop","static");
		if ($m->find("[data-formsave]")->length && $m->find("[data-formsave]")->attr("data-formsave")=="") {
			$m->find("[data-formsave]")->attr("data-formsave","#{$_GET["form"]}EditForm");
		}
		if ($m->find(".modal-title")->html()=="") $m->find(".modal-title")->html("Редактирование");
	}
	*/
	if ($flag=="category") {$out=$out->find("#{$_GET["form"]}List .list")->html(); return $out;}
	if ($flag=="") {return $out->outerHtml();}
}
?>
