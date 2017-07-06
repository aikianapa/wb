<?php
function ajax__pagination() {
	$res=array();
	foreach($_POST as $key =>$val) {$$key=$val;}
	if (!isset($page)) $page=1;
	if (!isset($find)) $find="";
	$fe=wbFromString("<div>".$foreach."</div>");
	//$fe->find("[data-wb-tpl={$tplid}]")->attr("data-find",$find);
	$fe->find("[data-wb-tpl={$tplid}]")->attr("data-wb-cache",$cache);
	$fe->find("[data-wb-tpl={$tplid}]")->attr("data-wb-page",$page);
	$fe->find("[data-wb-tpl={$tplid}]")->removeClass("loaded");
	$fe->find("[data-wb-tpl={$tplid}]")->append($tpl);
	$fe->find("[data-wb-tpl={$tplid}]",0)->tagForeach();
	$form=$fe->find("[data-wb-tpl={$tplid}]")->attr("form");
	//$call=$form."BeforePaginationShow";
	//if (is_callable($call)) {$call($fe->find("[data-wb-tpl={$tplid}]",0) );}
	$res["data"]=$fe->find("[data-wb-tpl={$tplid}]")->html();
	$res["pagr"]=$fe->find("#ajax-{$tplid}")->outerHtml();
	$res["pages"]=$fe->find("[data-wb-tpl={$tplid}]")->attr("data-wb-pages");
	return json_encode($res);
}

function ajax__save($form=null) {
	if ($form==null) {$form=$_ENV["route"]["params"]["form"];}
	$eFunc="{$form}__formsave"; $aFunc="{$form}_formsave";
	if 		(is_callable($aFunc)) {$ret=$aFunc();} 
	elseif (is_callable($eFunc)) {$ret=$eFunc();}
	else {
		if (!isset($_POST["id"])) {$_POST["id"]="";}
		if (!isset($_GET["item"])) {$_POST["id"]="";}
		if ($_POST["id"]=="" && $_GET["item"]>"") {$_POST["id"]=$_GET["item"];}
		$table=wbTable($form);
		$res=wbItemSave($table,$_POST); $ret=array();
		wbTableFlush($table);
		if (isset($_GET["copy"])) {
			$old=str_replace("//","/",$_ENV["path_app"]."/uploads/{$form}/{$_GET["copy"]}/");
			$new=str_replace("//","/",$_ENV["path_app"]."/uploads/{$form}/{$_GET["item"]}/");
			recurse_copy($old,$new);
		}
		if ($res) {
			$ret["error"]=0;
		} else {$ret["error"]=1; $ret["text"]=$res;}
	}
	return json_encode($ret);
}

function ajax__setdata() {
	$form=$_ENV["route"]["params"]["form"];
	$item=$_ENV["route"]["params"]["item"];
	
	$table=wbTable($form);
	if (!isset($_REQUEST["data-mode"])) {$_REQUEST["mode"]="list";} else {$_REQUEST["mode"]=$_REQUEST["data-mode"];}
	$Item=wbItemRead($table,$item);
	$call="{$form}BeforeShowItem"; if (is_callable($call)) {$Item=$call($Item);} else {
	$call="_{$form}BeforeShowItem"; if (is_callable($call)) {$Item=$call($Item,"list");}}
	$tpl=wbFromString($_REQUEST["tpl"]);
	$tpl->find(":first")->attr("item","{{id}}");
	foreach($tpl->find("[data-wb-role]") as $dr) {$dr->removeClass("loaded"); }
	$tpl->wbSetData($Item);
	return $tpl;
}
?>