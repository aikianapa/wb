<?php
class tagCart extends kiNode  {

    function __construct($that){
        $this->DOM = $that;
    }

	public function tagCart() {
		include($_ENV["path_engine"]."/wbattributes.php");
		if ($this->DOM->text()=="" AND !isset($mode)) {
			$mode="list";
			$tpl=wbGetTpl("/engine/tags/cart/cart_list.php",true);
			$l=$tpl->wbSetFormLocale();
			print_r($l);
			$this->DOM->html($tpl);
		} else if ($this->DOM->text()=="" AND isset($mode)) {
			$this->DOM->html(wbFromFile(__DIR__."/cart_{$mode}.php"));
		} else if ($this->DOM->text()!=="" AND isset($mode)) {
			$this->DOM->append(wbFromFile(__DIR__."/cart_{$mode}.php"));
		}
		if ($mode=="list") {
			$Item=wbItemRead("orders",$_SESSION["order_id"]);
			$tplid=uniqId();
			$this->DOM->attr("data-template",$tplid);
			$this->DOM->addTemplate($this->innerHtml());
			if (!$this->DOM->children("[data-wb-role=multiinput][name=items]")->length) $this->DOM->wbSetData($Item);
			$items=$this->DOM->find(".cart-item");
			$idx=0;
			foreach($items as $i) {
				if ($i->attr("idx")=="") {
				    $i->attr("idx",$idx);
				    $idx++;
				}
			}
		}
		$this->DOM->append('<script data-wb-append="body">$(document).ready(function(){wb_include("/engine/tags/cart/cart.js")});</script>');
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
    $order = wbItemRead('orders', $_SESSION['order_id']);
    if (!isset($order['id']) or $new = true) {
        $order['id'] = $_SESSION['order_id'];
        $order['user_id'] = $_SESSION['user_id'];
        $order['date'] = date('Y-m-d H:i:s');
    }

    switch ($param['action']) {
        case 'add-to-cart':       wbCartItemAdd($order); break;
        case 'cart-update':   	  wbCartUpdate($order); break;
        //case "cart-item-recalc":  wbCartItemRecalc($order); break;
        case 'cart-item-remove':  wbCartItemRemove($order); break;
        case 'cart-clear':        wbCartClear($order); break;
        case 'getdata':            wbCartGetData($order); break;
    }

    return $_SESSION['order_id'];
}

function wbCartGetData($order)
{
    echo json_encode($order);
    die;
}

function wbCartUpdate($order)
{
    $order['items'] = $_POST;
    $order['total'] = wbCartCalcTotal($order);
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
        $order['total'] = wbCartCalcTotal($order);
        wbItemSave('orders', $order);
    }
}

function wbCartCalcTotal($order)
{
    $order['total'] = 0;
    foreach ($order['items'] as $item) {
        $order['total'] += $item['count'] * $item['price'];
    }
    unset($item);

    return $order['total'];
}

function wbCartItemPos($order)
{
    $param = wbCartParam();
    if (!isset($order['items'])) {
        $order['items'] = array();
    }
    $pos = 0;
    foreach ($order['items'] as $key => $Item) {
        if ($Item['form'] == $param['form'] and $Item['item'] == $param['id']) {
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
