<?php
function ajax__pagination() {
	$res=array();
	foreach($_POST as $key =>$val) {$$key=$val;}
    $vars=wbItemToArray(json_decode($vars,true));
    $tmp_a=$_ENV["DBA"];
    $tmp_e=$_ENV["DBE"];
    $_ENV=array_merge($_ENV,$vars);
    $_ENV["DBA"]=$tmp_a;
    $_ENV["DBE"]=$tmp_e;
    unset($vars,$tmp_a,$tmp_e);
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

function ajax__pagination_vars() {
    $tplid=$_ENV["route"]["params"][0];
    $res=$_SESSION["temp"][$tplid];
    unset($_SESSION["temp"][$tplid]);
    return wbJsonEncode($res);
}

function ajax__cart() {
    return wbCartAction();
}

function ajax_alive() {
	$ret=false;
	if (isset($_SESSION["user_role"]) && $_SESSION["user_role"]>"") {
		if ($_POST["data"]=="wb_get_user_role") {
			$ret["user_role"]=$_SESSION["user_role"];
			$ret["mode"]="wb_set_user_role";
			$out=wbGetForm("common","modal");
			$out->find(".modal")->attr("id","wb_session_die");
			$out->find(".modal-header")->html("<h6 class='text-danger'><i class='fa fa-exclamation-triangle'></i> Сессия завершена</h6>");
			$out->find(".modal-body")->html("<center>Истёк период ожидания сессиии!<br>Пожалуйста, выполните <a href='/login'>вход в систему</a> заново.</center>");
			$out->find(".modal-footer")->html("<a class='btn btn-primary' href='/login'><i class='fa fa-sign-in'></i> Войти в систему</a>");
			$ret["msg"]=$out->outerHtml();
		} elseif ($_POST["data"]==$_SESSION["user_role"]) {
			$ret=true;
		} elseif ($_POST["data"]=="wb_session_die") {
			$ret["mode"]=$_POST["data"];
		}
	}
	return json_encode($ret);
}


function ajax__save($form=null) {
	if ($form==null) {$form=$_ENV["route"]["form"];}
	$eFunc="{$form}__formsave"; $aFunc="{$form}_formsave";
	if 		(is_callable($aFunc)) {$ret=$aFunc();}
	elseif (is_callable($eFunc)) {$ret=$eFunc();}
	else {
		if (!isset($_POST["id"])) {$_POST["id"]="";}
		if (!isset($_GET["item"])) {$_POST["id"]="";}
		if ($_POST["id"]=="" && $_GET["item"]>"") {$_POST["id"]=$_GET["item"];}
		$table=wbTable($form);
		$res=wbItemSave($table,$_POST); $ret=array();
//		wbTableFlush($table);
		if (isset($_GET["copy"])) {
			$old=str_replace("//","/",$_ENV["path_app"]."/uploads/{$form}/{$_GET["copy"]}/");
			$new=str_replace("//","/",$_ENV["path_app"]."/uploads/{$form}/{$_GET["item"]}/");
			recurse_copy($old,$new);
		}
		if ($res) {
			$ret["error"]=0;
		} else {$ret["error"]=1; $ret["text"]=$res;
			header("HTTP/1.0 404 Not Found");
		}
	}
	return json_encode($ret);
}

function ajax__setdata() {
	$form=$_ENV["route"]["form"];
	$item=$_ENV["route"]["item"];
	$table=wbTable($form);
	if (!isset($_REQUEST["data-wb-mode"])) {$_REQUEST["mode"]="list";} else {$_REQUEST["mode"]=$_REQUEST["data-wb-mode"];}
	$Item=wbItemRead($table,$item);
    if (!is_array($Item)) {$Item=array($Item);}
    if (!is_array($_REQUEST["data"])) {$_REQUEST["data"]=array($_REQUEST["data"]);}
	$Item=wbItemToArray(array_merge($Item,$_REQUEST["data"]));
    if (isset($Item["_form"])) {$_ENV["route"]["form"]=$_GET["form"]=$Item["_form"]; $_ENV["route"]["controller"]="form";}
    if (isset($Item["_item"])) {$_ENV["route"]["item"]=$_GET["item"]=$Item["_item"];}
    $Item=wbCallFormFunc("BeforeShowItem",$Item,$form,$_REQUEST["mode"]);
    $Item=wbCallFormFunc("BeforeItemShow",$Item,$form,$_REQUEST["mode"]);
	$tpl=wbFromString($_REQUEST["tpl"]);
	$tpl->find(":first")->attr("item","{{id}}");
	foreach($tpl->find("[data-wb-role]") as $dr) {$dr->removeClass("wb-done"); }
	$tpl->wbSetData($Item);
	return $tpl;
}

function ajax__listfiles() {
	$result=array();
	$_GET["path"]="/".implode("/",$_ENV["route"]["params"]);
	if ($_GET["path"]=="") {$path=$_ENV["path_app"]."/uploads";} else { $path=$_ENV["path_app"].$_GET["path"];}
	$files=wbListFiles($path);
	if (is_array($files)) {
	foreach($files as $key => $file) {
		if (is_file($path."/".$file)) {$result[]=$file;}
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

function ajax__getform() {
	echo wbGetForm($_ENV["route"]["params"][0],$_ENV["route"]["params"][1]);
	die;
}

function ajax__buildfields() {
	$res=array();
    if (isset($_POST["data"])) {$data=$_POST["data"];} else {$data=array();}
	foreach($_POST["dict"] as $dict) {
		$dict["value"]=htmlspecialchars($dict["value"]);
		$res=wbFieldBuild($dict,$data);
		echo $res;
	}
	die;
}

function ajax__mailer() {
	ajax__mail();
}

function ajax__mail() {
if (!isset($_POST["subject"])) {$_POST["subject"]="Письмо с сайта";}
if (isset($_POST["_tpl"])) {$out=wbGetTpl($_POST["_tpl"]);}
		elseif (isset($_POST["_form"])) {$out=wbGetTpl($_POST["_form"]);}
		else {$out=wbGetTpl("mail.php");}
if (!isset($_POST["email"])) {$_POST["email"]=$_ENV["route"]["mode"]."@".$_ENV["route"]["host"];}
if (!isset($_POST["name"])) {$_POST["name"]="Site Mailer";}

$out->wbSetData($_POST);
$out=$out->outerHtml();

if ($_ENV["route"]["mode"]=="mailer") {
	require $_ENV["path_engine"].'/lib/phpmailer/PHPMailerAutoload.php';
	//Create a new PHPMailer instance
	$mail = new PHPMailer(true);

/*
    $mail->SMTPDebug = 2;                                 // Enable verbose debug output
    $mail->isSMTP();                                      // Set mailer to use SMTP
    $mail->Host = 'smtp1.example.com;smtp2.example.com';  // Specify main and backup SMTP servers
    $mail->SMTPAuth = true;                               // Enable SMTP authentication
    $mail->Username = 'user@example.com';                 // SMTP username
    $mail->Password = 'secret';                           // SMTP password
    $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
    $mail->Port = 587;                                    // TCP port to connect to
*/

	//Set who the message is to be sent from
	$mail->setFrom($_POST["email"], 'Site Form');
	//Set an alternative reply-to address
	$mail->addReplyTo($_POST["email"], $_POST["name"]);
	//Set who the message is to be sent to
	$mail->addAddress("oleg_frolov@mail.ru", $_ENV["settings"]["header"]);
	//Set the subject line
	$mail->Subject = $_POST["subject"];
	//Read an HTML message body from an external file, convert referenced images to embedded,
	//convert HTML into a basic plain-text alternative body
	$mail->msgHTML($out, dirname(__FILE__));
	$mail->CharSet = 'utf-8';
	//Replace the plain text body with one created manually
	//$mail->AltBody = 'This is a plain-text message body';
	//Attach an image file
	//$mail->addAttachment('images/phpmailer_mini.png');
	//send the message, check for errors
	$res=$mail->send();
	$error=$mail->ErrorInfo;
} else {
	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
	$res=mail($_ENV["settings"]["email"], $_POST["subject"], $out,$headers);
	$error="unknown";
}
if (!$res) {
    echo "Ошибка отправки: " . $error;
} else {
    echo "Сообщение отрпавлено!";
}
if (isset($_POST["callback"]) AND is_callable($_POST["_callback"])) {
	@$_POST["_callback"]($res);
}
}
?>
