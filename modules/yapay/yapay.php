<?php
function yapay__init()
{
    if (isset($_ENV["route"]["params"][0])) {
        $mode=$_ENV["route"]["params"][0];
        $call="yapay__{$mode}";
        if (is_callable($call)) {
            echo @$call();
        }
        die;
    } else {
        return yapay__checkout();
    }
}

function yapay__checkout() {
        $order=wbItemRead("orders",$_SESSION["order_id"]);
        $order["apikey"]=yapay__apikey($order);
        $order["user_id"]=$_SESSION["user_id"];
        $order["name"]=$_SESSION["user"];
        if (count($order["items"]) AND $order["total"]>0) wbItemSave("orders",$order);
        $order["receiver"]=$_ENV["settings"]["yapay"]["id"];
        $out=wbFromFile(__DIR__ ."/yapay_ui.php");
        foreach($order as $key => $val) {
            $out->find("[name='{$key}']")->attr("value",$val);
        }
        $out->wbSetData($order);
        return $out->outerHtml(); 
}

function yapay__success() {
	$order_id=$_ENV["route"]["params"][1];
	$apicheck=$_ENV["route"]["params"][2];
	$order=wbItemToArray(wbItemRead("orders",$_SESSION["order_id"]));
	$apikey=yapay__apikey($order);
	if ($apikey==$apicheck) {
		$order["active"]="on";
		$order["payed"]="on";
		unset($order["apikey"]);
		wbItemSave("orders",$order);
		$_SESSION["order_id"]=wbNewId();
		header('Location: /cabinet');
		die;
	} else {
		header('Location: /fail');
		die;
	}
}

function yapay__settings() {
    if (wbRole("admin")) {
        $form=wbFromFile(__DIR__."/yapay_settings.php");
        $form->wbSetData($_ENV["settings"]);  // проставляем значения
        return $form->outerHtml();
    }
}

function yapay__apikey($order) {
	return md5($order["id"]."|".implode("|",$order["items"]));
}
?>
