<?php
function payeer__init()
{
    if (isset($_ENV["route"]["params"][0])) {
        $mode=$_ENV["route"]["params"][0];
        $call="payeer__{$mode}";
        if (is_callable($call)) {
            echo @$call();
        }
        die;
    } else {
        return payeer__checkout();
    }
}
// ФУНКЦИЯ НЕ ТЕСТИРОВАНА ДО КОНЦА
function payeer__checkout() {
        $order=wbItemRead("orders",$_SESSION["order_id"]);
        $order["user_id"]=$_SESSION["user_id"];
        $order["name"]=$_SESSION["user"];
        wbItemSave("orders",$order);
        $out=wbFromFile(__DIR__ ."/payeer_ui.php");
        $amount=number_format($order["total"], 2, '.', '');
        $desc="Order №{$order["id"]}, total amount = {$amount} {$_ENV["settings"]["payeer"]["currency"]}";
        $out->find(".modal-title")->html($desc);
        $arHash=array(
		$_ENV["settings"]["payeer"]["id"], // "m_shop"
		$order["id"], // "m_orderid"
		$amount, // "m_amount"
		$_ENV["settings"]["payeer"]["currency"], // "m_curr"
		base64_encode($desc), //"m_desc"
		$_ENV["settings"]["payeer"]["key1"] // "m_key"
	);

        $form=array(
		"m_shop"=>$_ENV["settings"]["payeer"]["id"],
		"m_orderid"=>$order["id"],
		"m_amount"=>$amount,
		"m_curr"=>$_ENV["settings"]["payeer"]["currency"],
		"m_desc"=>base64_encode($desc),
		"m_sign"=>payeer__sign($arHash),
	);
        foreach($form as $key => $val) {
            $out->find("[name='{$key}']")->attr("value",$val);
        }
        $out->wbSetData($order);
        return $out->outerHtml();
}

function payeer__success() {
	$order_id=$_ENV["route"]["params"][1];
	$apicheck=$_ENV["route"]["params"][2];
	$myOrder=$order=wbItemRead("orders",$_SESSION["order_id"]);
	$apikey=payeer__sign($order);
    if (isset($_ENV["settings"]["payeer"]["success"]) AND $_ENV["settings"]["payeer"]["success"]>"") {
        $success=$_ENV["settings"]["payeer"]["success"];
    } else {$success="/success";}
    if (isset($_ENV["settings"]["payeer"]["fail"]) AND $_ENV["settings"]["payeer"]["fail"]>"") {
        $fail=$_ENV["settings"]["payeer"]["fail"];
    } else {$fail="/fail";}

	if ($apikey==$apicheck) {
		$order["active"]="on";
		$order["payed"]="on";
		unset($order["apikey"]);
		wbItemSave("orders",$order);
		$_SESSION["order_id"]=wbNewId();
		header('Location: '.$success);
		die;
	} else {
		header('Location: '.$fail);
		die;
	}
}

function payeer__settings() {
    if (wbRole("admin")) {
        $form=wbFromFile(__DIR__."/payeer_settings.php");
        $_ENV["settings"]=wbItemToArray($_ENV["settings"]);
        $form->wbSetData($_ENV["settings"]);  // проставляем значения
        return $form->outerHtml();
    }
}

function payeer__sign($arHash) {
	//$arParams = array();
	//if (isset($_ENV["settings"]["payeer"]["success"]) AND $_ENV["settings"]["payeer"]["success"]>"" ) $arParams['success_url']=$_ENV["settings"]["payeer"]["success"];
	//if (isset($_ENV["settings"]["payeer"]["fail"]) AND $_ENV["settings"]["payeer"]["fail"]>"" ) $arParams['fail_url']=$_ENV["settings"]["payeer"]["fail"];
	//if (isset($_ENV["settings"]["payeer"]["status"]) AND $_ENV["settings"]["payeer"]["status"]>"" ) $arParams['status_url']=$_ENV["settings"]["payeer"]["status"];
	//$arParams["reference"]=$order;
	//$key=md5($_ENV["settings"]["payeer"]["key2"].$arHash["m_orderid"]);
	//$arHash[] = urlencode(base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, json_encode($arParams), MCRYPT_MODE_ECB)));
return strtoupper(hash('sha256', implode(":", $arHash)));
}
?>
