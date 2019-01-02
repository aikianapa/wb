<?php

function robokassa__init()
{
    if (isset($_ENV["route"]["params"][0])) {
        $mode=$_ENV["route"]["params"][0];
        $call="robokassa__{$mode}";
        if (is_callable($call)) {
            echo @$call();
        }
        die;
    } else {
        return robokassa__checkout();
    }
}


function robokassa__checkout() {
    $order=wbItemRead("orders",$_SESSION["order_id"]);
    $order["apikey"]=yapay__apikey($order);
    $order["user_id"]=$_SESSION["user_id"];
    $order["name"]=$_SESSION["user"];
    if (count($order["items"]) AND $order["total"]>0) wbItemSave("orders",$order);
    //$SETT=$_SESSION["settings"]["robokassa"];
    $SETT=robokassa__getsett();
	$test_mode = $SETT['test'];
	$success_url = "http://{$_SERVER['HTTP_HOST']}/{$_GET["form"]}/success/{$order['id']}.htm";
	$fail_url = "http://{$_SERVER['HTTP_HOST']}/{$_GET["form"]}/fail/{$order['id']}.htm";
	$result_url = $_ENV["route"]["hostp"]."/module/robokassa/result/{$order['id']}";
	$test_mode = $SETT['test'];
	$mrh=array();
	$mrh["login"] = $SETT['id'];
	$mrh["url"]=$SETT["url"]; // url мерчанта
	$mrh["inv_id"] = 0; // номер заказа системный (не принимает шестнатиричные)
	$mrh["Shp_orderId"] = $order["id"];
    $mrh["ResultURL"]=$result_url;
	$mrh["inv_desc"] = "Order № {$order['id']}"; // описание заказа
	$mrh["summ"] = $order["total"]; // сумма заказа
	$mrh["currency"] = ""; // предлагаемая валюта платежа
	$mrh["culture"] = "ru"; // язык
	if ($test_mode=="on") {
		$mrh["pass"] = $SETT['test1'];
		$mrh["test"] = 1;
	} else {$mrh["pass"] = $SETT['key1'];}
	$crc="{$mrh['login']}:{$mrh['summ']}:{$mrh['inv_id']}:{$mrh['pass']}:Shp_orderId={$mrh['Shp_orderId']}";
	$mrh["crc"]  = md5($crc); // формирование подписи
    $out=wbFromFile(__DIR__ ."/robokassa_ui.php");
	$out->wbSetData($mrh);
return $out->outerHtml();
}

function robokassa__success() {
	$order=wbItemRead("orders",$_REQUEST["Shp_orderId"]);
	$SETT=$_SESSION["settings"]["robokassa"];
    $SETT=robokassa__getsett();
	$test_mode = $SETT['test'];
	// HTTP parameters:
	$mrh=array();
	$mrh["login"] = $SETT['id'];
	$mrh["summ"] = $order["total"];
	$mrh["inv_id"] = $_REQUEST["InvId"];
	$mrh["Shp_orderId"]=$order["id"];
	if ($test_mode=="on") {$mrh["pass"] = $SETT['test1'];} else {$mrh["pass"] = $SETT['key1'];}

	// build own CRC
	$my_crc="{$mrh['summ']}:{$mrh['inv_id']}:{$mrh['pass']}:Shp_orderId={$mrh['Shp_orderId']}";
	$my_crc = strtoupper(md5($my_crc));

	$crc = $_REQUEST["SignatureValue"];
	$crc = strtoupper($crc);  // force uppercase

    if (isset($_ENV["settings"]["robokassa"]["success"]) AND $_ENV["settings"]["robokassa"]["success"]>"") {
        $success=$_ENV["settings"]["robokassa"]["success"];
    } else {$success="/success";}
    if (isset($_ENV["settings"]["robokassa"]["fail"]) AND $_ENV["settings"]["robokassa"]["fail"]>"") {
        $fail=$_ENV["settings"]["robokassa"]["fail"];
    } else {$fail="/fail";}

	if (strtoupper($my_crc) == strtoupper($crc)) {
		$order["active"]="on";
		$order["payed"]="on";
		unset($order["apikey"]);
		wbItemSave("orders",$order);
		$_SESSION["order_id"]=wbNewId();
        setcookie("order_id","",time()-3600,"/"); unset($_COOKIE["order_id"]);
		header('Location: '.$success);
		die;
	} else {
		header('Location: '.$fail);
		die;
	}
}

function robokassa__fail() {
    if (isset($_ENV["settings"]["robokassa"]["fail"]) AND $_ENV["settings"]["robokassa"]["fail"]>"") {
        $fail=$_ENV["settings"]["robokassa"]["fail"];
    } else {$fail="/fail";}
    header('Location: '.$fail);
	die;
}

function robokassa__result() {
	$order=wbItemRead("orders",$_REQUEST["Shp_orderId"]);
	$SETT=robokassa__getsett();
	$test_mode = $SETT['test'];
	// HTTP parameters:
	$mrh=array();
	$mrh["login"] = $SETT['id'];
	$mrh["summ"] = $order["total"];
	$mrh["inv_id"] = $_REQUEST["InvId"];
	$mrh["Shp_orderId"]=$order["id"];
	if ($test_mode=="on") {$mrh["pass"] = $SETT['test1'];} else {$mrh["pass"] = $SETT['key1'];}

	// build own CRC
	$my_crc="{$mrh['summ']}:{$mrh['inv_id']}:{$mrh['pass']}:Shp_orderId={$mrh['Shp_orderId']}";
	$my_crc = strtoupper(md5($my_crc));

	$crc = $_REQUEST["SignatureValue"];
	$crc = strtoupper($crc);  // force uppercase


	if (strtoupper($my_crc) !== strtoupper($crc)) {    echo "bad sign\n"; exit();	} else {
		$order["active"]="on";
		$order["payed"]="on";
		unset($order["apikey"]);
		wbItemSave("orders",$order);
		$_SESSION["order_id"]=wbNewId();
        setcookie("order_id","",time()-3600,"/"); unset($_COOKIE["order_id"]);
    }

	echo "OK{$mrh['inv_id']}\n";
}

function robokassa__settings() {
    if (wbRole("admin")) {
        $form=wbFromFile(__DIR__."/robokassa_settings.php");
        $form->wbSetData($_ENV["settings"]);  // проставляем значения
        return $form->outerHtml();
    }
}

function robokassa__getsett() {
    $SETT=array();
    $SETT["id"]=$_ENV["settings"]["robokassa"]["id"];
    $SETT["url"]=$_ENV["settings"]["robokassa"]["url"];
    $SETT['key1']=$_ENV["settings"]["robokassa"]["key1"];
    $SETT['key2']=$_ENV["settings"]["robokassa"]["key2"];
    $SETT['test']=$_ENV["settings"]["robokassa"]["test"];
    $SETT['test1']=$_ENV["settings"]["robokassa"]["test1"];
    $SETT['test2']=$_ENV["settings"]["robokassa"]["test2"];
    return $SETT;
}

?>
