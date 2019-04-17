<?php
class tagCart extends kiNode  {

    function __construct($that){
        $this->DOM = $that;
    }

    public function tagCart($Item) {
	    include($_ENV["path_engine"]."/wbattributes.php");
	    if ($this->DOM->text()=="" AND !isset($mode)) {
		    $mode="list";
		    $tpl=wbGetTpl("/engine/tags/cart/cart_list.php",true);
		    $l=$tpl->wbSetFormLocale();
		    $this->DOM->html($tpl);
	    } else if ($this->DOM->text()=="" AND isset($mode)) {
		    $this->DOM->html(wbFromFile(__DIR__."/cart_{$mode}.php"));
	    } else if ($this->DOM->text()!=="" AND isset($mode)) {
		    $this->DOM->append(wbFromFile(__DIR__."/cart_{$mode}.php"));
	    }
		$Item=wbCartGetData();
	    if ($mode=="list") {
		    $tplid=uniqId();
		    $this->DOM->attr("data-template",$tplid);
		    $this->DOM->addTemplate($this->innerHtml());

		    if ($_SESSION["user_id"]>"") {
			if ($Item["fullname"]=="") $Item["fullname"] = $_SESSION["user"]["first_name"]." ".$_SESSION["user"]["last_name"];
			if ($Item["email"]=="") $Item["email"] = $_SESSION["user"]["email"];
			if ($Item["phone"]=="") $Item["phone"] = $_SESSION["user"]["phone"];
		    }


		    if (!$this->DOM->children("[data-wb-role=multiinput][name=items]")->length) $this->DOM->wbSetData($Item);
		    $this->DOM->find("input[name=fullname]",0)->wbSetValues($Item);
		    $items=$this->DOM->find(".cart-item");
		    $idx=0;
		    foreach($items as $i) {
			    if ($i->attr("idx")=="") {
				$i->attr("idx",$idx);
				$idx++;
			    }
		    }
	    }
	    $this->DOM->find(".cart-lines")->html($Item["lines"]);
	    $this->DOM->find(".cart-count")->html($Item["count"]);
	    $this->DOM->find(".cart-total")->html($Item["total"]);
	    $this->DOM->append('<script data-wb-append="body">$(document).on("wbapp",function(){wb_include("/engine/tags/cart/cart.js")});</script>');
    }
}

function ajax__cart() {
    return wbCartAction();
}


function wbCartAction()
{
    if (!isset($_SESSION['order_id']) or '' == $_SESSION['order_id']) {
        $_SESSION['order_id'] = wbNewId();
        $new = true;
    } else {
        $new = false;
    }
    $param = wbCartParam();
	$order=wbCartGetData();

    switch ($param['action']) {
        case 'add-to-cart':       wbCartItemAdd($order); break;
        case 'cart-update':   	  wbCartUpdate($order); break;
        //case "cart-item-recalc":  wbCartItemRecalc($order); break;
        case 'cart-item-remove':  wbCartItemRemove($order); break;
        case 'cart-clear':        wbCartClear($order); break;
        case 'getdata':           echo json_encode($order); die; break;
    }

    return $_SESSION['order_id'];
}

function wbCartGetData($order=null)
{
	if ($order == null ) {$order_id = $_SESSION['order_id'];}
	$order = wbItemRead('orders', $order_id);
	if (!isset($order['id']) or $new = true) {
		$order['id'] = $_SESSION['order_id'];
		$order['user_id'] = $_SESSION['user_id'];
		$order['date'] = date('Y-m-d H:i:s');
	}
	$order=wbCartCalcTotals($order);
	return $order;
}

function wbCartUpdate($order)
{
    $order['items'] = $_POST;
    $order = wbCartCalcTotals($order);
    wbItemSave('orders', $order);
}

function wbCartParam()
{
    $param = $_ENV['route']['params'];
    $param['mode'] = $_ENV['route']['mode'];
    $param = array_merge($param, $_REQUEST);
    $param['action'] = $param[0];

    return $param;
}

function wbCartClear($order)
{
    $order['items'] = array();
    $order['total'] = 0;
    $order['lines'] = 0;
    $order['count'] = 0;
    wbItemSave('orders', $order);
}

function wbCartItemAdd($order)
{
    $param = wbCartParam();
    if ($param['id'] > '' and $param['quant'] > '') {
        $pos = wbCartItemPos($order);
        $line = $param;
        unset($line['mode'],$line['action']);
        $order['items'][$pos] = $line;
        $order = wbCartCalcTotals($order);
        wbItemSave('orders', $order);
    }
}

function wbCartCalcTotals($order)
{
    $order['lines'] = 0;
    $order['count'] = 0;
    $order['total'] = 0;
    if (!isset($order['items'])) $order['items']=array();
    foreach ($order['items'] as $item) {
	$order['lines'] += 1;
	$order['count'] += intval($item['quant']);
        $order['total'] += intval($item['quant']) * intval($item['price']);
    }
    return $order;
}

function wbCartItemPos($order)
{
    $param = wbCartParam();
    if (!isset($order['items'])) $order['items']=array();
    $pos = 0;
    foreach ($order['items'] as $key => $Item) {
        if ($Item['form'] == $param['form'] and $Item['id'] == $param['id']) {
            return $pos;
        }
        ++$pos;
    }

    return $pos;
}

function wbCartItemPosCheck($Item)
{
    $res = true;
    $param = wbCartParam();
    foreach ($param as $k => $fld) {
        if ($Item[$fld] !== $_GET[$fld]) {
            $res = false;
        }
    }

    return $res;
}


?>
