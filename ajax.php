<?php
if (isset($_GET["getsysmsg"])) {
        require_once __DIR__."/functions.php";
        wbInitEnviroment();
        echo ajax__getsysmsg();
}
if (is_file($_SERVER["DOCUMENT_ROOT"]."/functions.php")) require_once $_SERVER["DOCUMENT_ROOT"]."/functions.php";

function ajax__pagination() {
    if (isset($_REQUEST["route"])) {
        $_ENV["route"]=json_decode($_REQUEST["route"],true);
    }
    $res=array();
    foreach($_POST as $key =>$val) {
        $$key=$val;
    }
    $vars=wbItemToArray(json_decode($vars,true));
    $tmp_a=$_ENV["dba"];
    $tmp_e=$_ENV["dbe"];
    if (is_array($vars)) $_ENV=array_merge($_ENV,$vars);
    $_ENV["dba"]=$tmp_a;
    $_ENV["dbe"]=$tmp_e;
    unset($vars,$tmp_a,$tmp_e);
    if (!isset($page)) $page=1;
    if (!isset($find)) $find="";
    $fe=wbFromString("<div>".$foreach."</div>");
    $fe->wbClearClass();
    $fe->find("[data-wb-tpl={$tplid}]")->attr("data-wb-find",$find);
    //$fe->find("[data-wb-tpl={$tplid}]")->attr("data-wb-cache",$cache);
    $fe->find("[data-wb-tpl={$tplid}]")->attr("data-wb-page",$page);
    $fe->find("[data-wb-tpl={$tplid}]")->removeClass("wb-done");
    $fe->find("[data-wb-tpl={$tplid}]")->append($tpl);
    $fe->find("[data-wb-tpl={$tplid}]",0)->tagForeach();
    //$form=$fe->find("[data-wb-tpl={$tplid}]")->attr("form");
    //$call=$form."BeforePaginationShow";
    //if (is_callable($call)) {$call($fe->find("[data-wb-tpl={$tplid}]",0) );}
    $res["data"]=$fe->find("[data-wb-tpl={$tplid}]")->html();
    $res["pagr"]=$fe->find("#ajax-{$tplid}")->outerHtml();
    $res["pages"]=$fe->find("[data-wb-tpl={$tplid}]")->attr("data-wb-pages");
    //$res["pages"]=$fe->find("#ajax-{$tplid}")->attr("data-wb-pages");
    return json_encode($res);
}

function ajax__pagination_vars() {
    // $tplid=$_ENV["route"]["params"][0];
    // $res=$_SESSION["temp"][$tplid];
    // unset($_SESSION["temp"][$tplid]);
    // return wbJsonEncode($res);
}

function ajax__onlineusers() {
	clearstatcache();
	if ( $directory_handle = opendir( session_save_path() ) ) {
		$count = 0;
		while ( false !== ( $file = readdir( $directory_handle ) ) ) {
			if($file != '.' && $file != '..') {
				if ( time()- filemtime(session_save_path() . '' . $file) < 3 * 60) {
					$count++;
				}
			}
		}
		closedir($directory_handle);
	}

	header('Content-Type: application/json');
	return json_encode(["count"=>$count]);
}

function ajax__getuser() {
	$res=false;
	if (isset($_SESSION["user"])) $res=$_SESSION["user"];
	return base64_encode(json_encode($res));
}

function ajax__callfunc() {
	$func=$_ENV["route"]["params"][0];
	$res = false;
	if (is_callable($func)) {
		$i=0;
		$params=array();
		foreach($_POST as $key => $val) {
			$p[$i] = $val;
			$params[]='$p['.$i.']';
			$i++;
		}
		$params=implode(",",$params);
		$res = eval('return $func('.$params.');');
	}
    header('Content-Type: application/json; charset=utf-8');
	if (is_array($res)) {
		echo json_encode($res);
	} else {
		$arr=["__data" => "{$res}"];
        echo json_encode($arr);
	}
	die;
}

function ajax__settings() {
    $sett=$_ENV["settings"];
    $mrch=wbMerchantList();
    foreach($mrch as $k => $m) {
        unset($sett[$m["name"]]);
    }
    return base64_encode(json_encode($sett));
}

function ajax__getsysmsg() {
        return base64_encode(json_encode(wbGetSysMsg()));
}

function ajax__alive() {
	$ret=false;
	if (isset($_POST["data"]) && $_POST["data"]=="wb_get_user_role") {
		if (isset($_SESSION["user_role"]) && $_SESSION["user_role"]>"" && isset($_POST["user_role"]) && $_POST["user_role"]==$_SESSION["user_role"]) $ret=true;
		if (!isset($_POST["user_role"])) $ret=true;
	}


    if ($ret==true) {
	$ret=[
		"user_role"=>$_SESSION["user_role"],
		"mode"=>"wb_set_user_role"
	];
	if (isset($_POST["modal"]) && $_POST["modal"]=="true") {
            $out=wbGetForm("common","modal");
            $out->find(".modal")->attr("id","wb_session_die");
            $out->find(".modal")->addClass("in show");
            $out->find(".modal-header")->html("<h6 class='text-danger'><i class='fa fa-exclamation-triangle'></i> {$_ENV["sysmsg"]["session_expired"]}</h6>");
            $out->find(".modal-body")->html("<i class='fa fa-clock-o fa-3x tx-danger absolute pull-left'></i><p class='text-center'>{$_ENV["sysmsg"]["session_expired_msg"]}</p>");
            $out->find(".modal-footer")->html("<a class='btn btn-primary' href='/login'><i class='fa fa-sign-in'></i> {$_ENV["sysmsg"]["session_expired_login"]}</a>");
            $ret["modal"]=$out->outerHtml();
	}

    } else {
            $ret=["mode"=>"wb_session_die"];
    }
    // Clear cache

    $path=$_ENV["dba"]."/_cache";
    $files=wbListFiles($path);
    foreach($files as $file) {
        $user=md5($_SESSION["user_id"]);
        if (substr($file,0,strlen($user))==$user) {
            if (isset($_POST["data"]) && is_array($_POST["data"]) && !in_array($file,$_POST["data"])) {
                // если файл не в списке присутствующих на экране списков, то удаляем
                unlink($path."/".$file);
            }
        } else {
            if (filemtime($path."/".$file)+86400 < time()) {
                // если файл не принадлежит юзеру, но ему больше суток, то удаляем
                unlink($path."/".$file);
            }
        }
    }
    return base64_encode(json_encode($ret));
}

function ajax__gettree() {
    foreach($_POST as $k => $v) {
        $$k=$v;
    }
    $tree=wbTreeRead($tree);
    $tree=wbTreeFindBranch($tree["tree"],$branch,$parent,$childrens);
     return base64_encode(json_encode(wbItemToArray($tree)));
}

function ajax__gettreedict() {
    foreach($_POST as $k => $v) {
        $$k=$v;
    }
    $tree=wbTreeRead($tree);
    $tree=$tree["dict"];
     return base64_encode(json_encode(wbItemToArray($tree)));
}


function ajax__save($form=null) {
    if ($form==null) {
        $form=$_ENV["route"]["form"];
    }
    $eFunc="{$form}__formsave";
    $aFunc="{$form}_formsave";
    if 		(is_callable($aFunc)) {
        $ret=$aFunc();
    }
    elseif (is_callable($eFunc)) {
        $ret=$eFunc();
    }
    else {
        if (!isset($_POST["id"])) {
            $_POST["id"]="";
        }
        if (!isset($_GET["item"])) {
            $_POST["id"]="";
        }
        if ($_POST["id"]=="" && $_GET["item"]>"") {
            $_POST["id"]=$_GET["item"];
        }
        $res=wbItemSave($form,$_POST);
        $ret=array();
        if (isset($_GET["copy"])) {
            $old=str_replace("//","/",$_ENV["path_app"]."/uploads/{$form}/{$_GET["copy"]}/");
            $new=str_replace("//","/",$_ENV["path_app"]."/uploads/{$form}/{$_GET["item"]}/");
            recurse_copy($old,$new);
        }
        if ($res) {
            $ret["error"]=0;
        } else {
            $ret["error"]=1;
            $ret["text"]=$res;
        }
    }
    return json_encode($ret);
}

function ajax__rmitem($form=null) {
    if ($form==null) {
        $form=$_ENV["route"]["form"];
    }
    $aFunc="{$form}_rmitem";
    if	(is_callable($aFunc)) {
        $ret=$aFunc();
    } else {
	if (isset($_SESSION["user_id"])) {
		$_ENV["DOM"]=wbGetForm("common","remove_confirm");
		if (isset($_REQUEST["confirm"]) AND $_REQUEST["confirm"]=="true") {
				$_ENV["DOM"]->find("script[data-wb-tag=success]")->remove();
			} else {
				wbItemRemove($_ENV["route"]["form"],$_ENV["route"]["item"]);
				$_ENV["DOM"]->find(".modal")->remove();
			}
			return $_ENV["DOM"];
		}
	}
}

function ajax__getlocale($type=null,$name=null) {
	if (isset($_POST["type"])) $type=$_POST["type"];
	if (isset($_POST["name"])) $name=$_POST["name"];
	if ($type==null) $type="tpl";
	if ($name==null) $name="default.php";
        if ($type=="tpl") {
                $out=wbGetTpl($name);
        } else if ($type=="form") {
                $arr=explode("_",$name);
                $form=$arr[0];
                unset($arr[0]);
                if ($arr[1]=="_") {unset($arr[1]);}
                $name=implode("_",$arr);
                $out=wbGetForm($form,$name);
        } else if ($type=="file") {
                $out=wbFromFile($_ENV["path_app"].$name);
        } else if ($type=="url") {
                $out=wbFromString(file_get_contents($name));
        }
	$locale=$out->wbGetFormLocale();
	$locale=$locale[$_SESSION["lang"]];
        return base64_encode(json_encode($locale));
}


function ajax__setdata() {
    $form=$_ENV["route"]["form"];
    $item=$_ENV["route"]["item"];
    $Item=array();
    $_REQUEST=json_decode(base64_decode($_REQUEST["data"]),true);
    if (isset($_REQUEST["_base"])) $_ENV['path_tpl'] = $_ENV['path_app'].$_REQUEST["_base"];
    if (isset($_REQUEST["data-wb-mode"])) $_REQUEST["mode"]=$_REQUEST["data-wb-mode"];
    if (!isset($_REQUEST["data-wb-mode"])) $_REQUEST["mode"]="list";
    if ($form!=="undefined" && $item!=="undefined") $Item=wbItemRead($form,$item);
    if (!is_array($_REQUEST["data"])) parse_str($_REQUEST["data"],$_REQUEST["data"]);
    $_POST = $_REQUEST["data"];
    $Item=wbItemToArray($Item);
    if (!is_array($Item)) $Item=array();
    $Item=array_merge($Item,$_REQUEST["data"]);
    if (isset($Item["_form"])) {
        $_ENV["route"]["form"]=$_GET["form"]=$Item["_form"];
        $_ENV["route"]["controller"]="form";
    }
    if (isset($Item["_item"])) $_ENV["route"]["item"]=$_GET["item"]=$Item["_item"];
    if (isset($Item["_mode"])) $_ENV["route"]["mode"]=$_GET["mode"]=$Item["_mode"];
    $Item=wbCallFormFunc("BeforeShowItem",$Item,$form,$_REQUEST["mode"]);
    $Item=wbCallFormFunc("BeforeItemShow",$Item,$form,$_REQUEST["mode"]);
    $tpl=wbFromString($_REQUEST["tpl"]);
    $tpl->find(":first")->attr("item","{{id}}");
    foreach($tpl->find("[data-wb-role]") as $dr) {
        $dr->removeClass("wb-done");
    }
    $tpl->wbSetData($Item,true);
    $tpl->find("empty")->remove();
    $tpl->tagHideAttrs();
    return $tpl;
}

function ajax__listfiles() {
    if (!isset($_SESSION["user_id"])) return;
    $result=array();
    $_GET["path"]="/".implode("/",$_ENV["route"]["params"]);
    if ($_GET["path"]=="") {
        $path=$_ENV["path_app"]."/uploads";
    }
    else {
        $path=$_ENV["path_app"].$_GET["path"];
    }
    $files=wbListFiles($path);
    if (is_array($files)) {
        foreach($files as $key => $file) {
            if (is_file($path."/".$file)) {
                $result[]=$file;
            }
        }
    }
    return json_encode($result);
}

function ajax__newid() {
    return json_encode(wbNewId());
}

function ajax__gettpl() {
    echo wbGetTpl($_ENV["route"]["params"][0]);
    die;
}

function ajax__remove() {
    if ($_ENV["route"]["params"][0]=="uploads") {
        $file=$_ENV["path_app"]."/".implode("/",$_ENV["route"]["params"]);
        return json_encode(wbFileRemove($file));
    }
    die;
}

function ajax__rename() {
    if (isset($_SESSION["user_id"]) AND in_array($_SESSION["user_role"],["admin","moder"])) {
        return json_encode(wbItemRename($_ENV["route"]["form"],$_ENV["route"]["item"],$_POST["id"]));
    }
    die;
}


function ajax__getform() {
        $out = wbGetForm($_ENV["route"]["params"][0],$_ENV["route"]["params"][1]);
        header('Content-Type: text/html; charset=utf-8');
        echo $out;
        die;
}

function ajax__buildfields() {
    $res=array();
    if (isset($_POST["data"])) {
        $data=$_POST["data"];
    }
    else {
        $data=array();
    }
    foreach($_POST["dict"] as $dict) {
        $dict["value"]=htmlspecialchars($dict["value"]);
        $res=wbFieldBuild($dict,$data);
        return $res;
    }
    die;
}


function ajax__treeedit() {
    $out=wbGetForm("common","tree_edit");
    $out->wbSetData($_POST);
    $arr=array();
    if (is_array($_POST["fields"])) {
            foreach($_POST["fields"] as $dict) {
                if (isset($dict["name"]) AND !in_array($dict["name"],$arr)) {
                    $dict["value"]=htmlspecialchars($dict["value"]);
                    $res=wbFieldBuild($dict,$_POST["data"]);
                    $out->find(".treeData form")->append($res);
                    $arr[]=$dict["name"];
                    $prop=$out->find(".treeDict [data-wb-field=name][value={$dict["name"]}]")->parents(".wb-multiinput")->find(".wb-prop-fields");
			foreach($prop->find("[data-type-allow],[data-type-disallow]") as $element) {
				if ($element->is("[data-type-allow]")) {
					$attr=wbArrayAttr($element->attr("data-type-allow"));
					if (!in_array($dict["type"],$attr)) {$element->remove();}
				}
				if ($element->is("[data-type-disallow]")) {
					$attr=wbArrayAttr($element->attr("data-type-disallow"));
					if (in_array($dict["type"],$attr)) {$element->remove();}
				}
			}
                }
            }
    }
    return $out->outerHtml();
}


function ajax__mailer() {
    return ajax__mail();
}

function ajax__mail() {
    $attachments=[];
    if (!isset($_POST["_subject"])) $_POST["_subject"]=$_ENV['sysmsg']["mail_from_site"];
    if (!isset($_POST["subject"])) $_POST["subject"]=$_POST["_subject"];
    $_POST["subject"] = mb_encode_mimeheader($_POST["subject"],"UTF-8");

    if (isset($_POST["_tpl"])) {
        $out=wbGetTpl($_POST["_tpl"]);
    }
    elseif (isset($_POST["_form"])) {
        $out=wbGetTpl($_POST["_form"]);
    }
    elseif (isset($_POST["_message"])) {
        $out=wbFromString($_POST["_message"]);
        $b64img = $out->find("img[src^='data:']");
        foreach($b64img as $b64) {
            $attachments[] = $b64->attr("src");
            $b64->remove();
        }
    }
    else {
        $out=wbGetTpl("mail.php");
    }
    if (!isset($_POST["email"])) $_POST["email"]=$_ENV["route"]["mode"]."@".$_ENV["route"]["host"];
    if (!isset($_POST["name"]))  $_POST["name"]="Site Mailer";
    if (isset($_POST["_mailto"])) {
        $mailto=$_POST["_mailto"];
    }
    else {
        $mailto = $_ENV["settings"]["email"];
    }
    $receivers = [];
    $sendlist = explode(",",$mailto);
    foreach($sendlist as $tmp) {
        $tmp = trim( $tmp );
        $receivers[] = "{$tmp};{$_ENV["settings"]["header"]}";
    }

    $Item=$_POST;
    $out->wbSetData($Item);
    $out=$out->outerHtml();
    $res=wbMail("{$_POST["email"]};{$_POST["name"]}",$receivers, $_POST["subject"], $out, $attachments);
    if (!$res) {
        $result=json_encode(array("error"=>true,"msg"=>$_ENV['sysmsg']["mail_sent_error"].": ".$_ENV["error"]['wbMail']));
    } else {
        $result=json_encode(array("error"=>false,"msg"=>$_ENV['sysmsg']["mail_sent_success"]."!"));
    }
    if (isset($_POST["_callback"]) AND is_callable($_POST["_callback"])) {
        return @$_POST["_callback"]($result);
    }
    return $result;
}
?>
