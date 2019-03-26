<?php
function formbuilder__init()
{
	if (!wbRole("admin")) return;
    if (isset($_ENV["route"]["params"][0])) {
        $mode=$_ENV["route"]["params"][0];
        $call="formbuilder__{$mode}";
        if (is_callable($call)) {
            $out=@$call();
            echo $out;
        }
	die;
    } else {
        $out=wbFromString(file_get_contents(__DIR__ ."/formbuilder_ui.php"));
        $out->wbSetData(array(
		"snippets"=>formbuilder__getsnippets()
	));
        return $out;
    }
}

function formbuilder__locale() {
        $out=wbFromString(file_get_contents(__DIR__ ."/formbuilder_ui.php"));
        $locale=$out->wbGetFormLocale();
        return base64_encode(json_encode($locale[$_SESSION["lang"]]));
}

function formbuilder__create() {
	$formName=strtolower($_ENV["route"]["params"][1]);
	$formPath=$_ENV["path_app"]."/forms/".$formName;
	$res=false;
	if (!is_dir($formPath)) { mkdir($formPath);
		$modes=array("","edit","list");
		foreach($modes as $mode) {
			if ($mode>"") {
				$form=file_get_contents($_ENV["path_engine"]."/forms/common/common_{$mode}.php");
				file_put_contents("{$formPath}/{$formName}_{$mode}.php",$form);
			} else {
				$prog=file_get_contents($_ENV["path_engine"]."/forms/common/common_prog.php");
				$prog=str_replace("%form%",$formName,$prog);
				file_put_contents("{$formPath}/{$formName}.php",$prog);
			}
		}
		$res=true;
	}
	return json_encode($res);
}


function formbuilder__getmodelist() {
	$res=array();
	$formName=strtolower($_ENV["route"]["params"][1]);
	$list=glob("{$_ENV['path_engine']}/forms/{$formName}/*.php");
	foreach($list as $name) {
		$name=array_pop(explode("/",$name));
		if (!in_array($name,$res)) $res[$name]=array("formname"=>$formName,"filename"=>$name,"filepath"=>"/forms/{$formName}/{$name}");
	}
	$list=glob("{$_ENV['path_app']}/forms/{$formName}/*.php");
	foreach($list as $name) {
		$name=array_pop(explode("/",$name));
		if (!in_array($name,$res)) $res[$name]=array("formname"=>$formName,"filename"=>$name,"filepath"=>"/forms/{$formName}/{$name}");
	}
	$list=glob("{$_ENV['path_engine']}/forms/{$formName}/*.ini");
	foreach($list as $name) {
		$name=array_pop(explode("/",$name));
		if (!in_array($name,$res)) $res[$name]=array("formname"=>$formName,"filename"=>$name,"filepath"=>"/forms/{$formName}/{$name}");
	}
	$list=glob("{$_ENV['path_app']}/forms/{$formName}/*.ini");
	foreach($list as $name) {
		$name=array_pop(explode("/",$name));
		if (!in_array($name,$res)) $res[$name]=array("formname"=>$formName,"filename"=>$name,"filepath"=>"/forms/{$formName}/{$name}");
	}
	return json_encode($res);
}

function formbuilder__getform() {
	$formName=strtolower($_ENV["route"]["params"][1]);
	$formFile=$_POST["formFile"];
	$form=glob("{$_ENV['path_app']}/forms/{$formName}/{$formFile}");
	if (!count($form)) {
		$form=glob("{$_ENV['path_engine']}/forms/{$formName}/{$formFile}");
	}
	if (isset($form[0]) AND is_file($form[0])) {
		$form=file_get_contents($form[0]);
	} else {
		$form = false;
	}
	return base64_encode(json_encode($form));
}

function formbuilder__getsnippets() {
	$snippets = wbTreeRead("formbuilder_snippets");
	return $snippets["tree"];
}
