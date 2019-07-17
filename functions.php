<?php
require_once __DIR__.'/lib/vendor/autoload.php';
require_once __DIR__.'/kiDom.php';
require_once __DIR__.'/wbapp.php';

use Nahid\JsonQ\Jsonq;
use soundintheory\PHPSQL;
use Rct567\DomQuery\DomQuery;

function wbInit()
{
    error_reporting(error_reporting() & ~E_NOTICE);
    wbErrorList();
    wbTrigger('func', __FUNCTION__, 'before');
    wbInitEnviroment();
    wbInitDatabase();
    wbInitFunctions();
//    wbTableList();
    wbRouterAdd();
    wbRouterGet();
}

function wbInitEnviroment()
{
    if (!isset($_SESSION['user'])) {
        $_SESSION['user'] = '';
    }

    if (!isset($_SESSION['user_role'])) {
        $_SESSION['user_role'] = '';
    }
    if (!isset($_SESSION['trigger'])) {
        $_SESSION['trigger'] = array();
    }
    if (!isset($_SESSION['order_id']) or '' == $_SESSION['order_id']) {
        $_SESSION['order_id'] = wbNewId();
        $new = true;
    } else {
        $new = false;
    }
    wbTrigger('func', __FUNCTION__, 'before');
    $dir=explode("/",__DIR__);
    array_pop($dir);
    $dir=implode("/",$dir);
    $_ENV['path_app'] = ($_SERVER['DOCUMENT_ROOT']>"") ? $_SERVER['DOCUMENT_ROOT'] : $dir ;
    $_ENV['path_engine'] = $_ENV['path_app'].'/engine';
    $_ENV['path_system'] = __DIR__;
    $_ENV['base'] = "/tpl/";
    $_ENV['path_tpl'] = $_ENV['path_app'].$_ENV['base'];
    $_ENV['dbe'] = $_ENV['path_engine'].'/database'; 			// Engine data
    $_ENV['dba'] = $_ENV['path_app'].'/database';	// App data
    $_ENV['dbec'] = $_ENV['path_engine'].'/database/_cache'; 			// Engine data
    $_ENV['dbac'] = $_ENV['path_app'].'/database/_cache';	// App data
    $_ENV['error'] = array();
    $_ENV['last_error'] = null;
    $_ENV['env_id'] = $_ENV['new_id'] = wbNewId();
    $_ENV['datetime'] = date('Y-m-d H:i:s');
    $_ENV['forms'] = wbListForms(false);
    $_ENV['modules'] = wbListModules();
    $_ENV['tables'] = wbTableList();
    $_ENV['thumb_width'] = 200;
    $_ENV['thumb_height'] = 160;
    $_ENV['intext_width'] = 320;
    $_ENV['intext_height'] = 240;
    $_ENV['page_size'] = 12;
    $_ENV['data'] = new stdClass(); // for store some data
    wbCheckWorkspace();
    $variables = array();
    $settings = wbItemRead('admin', 'settings');
    if (!$settings) {
        $settings = array();
    } else {
        foreach ((array) $settings['variables'] as $v) {
            $variables[$v['var']] = $v['value'];
        }
    }
    $_ENV['variables'] = $variables;
    $settings = array_merge($settings, $variables);
    $_ENV['settings'] = $settings;

    if ($_SERVER["REQUEST_URI"]=="/engine/") {
        unset($_SESSION["lang"]);
    }
    else {
        if (isset($_SESSION["user_lang"]) AND $_SESSION["user_lang"]>"") {
            //
        } else {
            if ((!isset($_SESSION['lang']) OR $_SESSION['lang']=="") AND (!isset($_ENV['lang']) OR $_ENV['lang']=="")) {
                if (isset($_ENV['settings']["lang"])) {
                    $_SESSION['lang'] = $_ENV["lang"] = $_ENV['settings']["lang"];
                } else {
                    $_SESSION['lang'] = $_ENV["lang"] = 'eng';
                }
            } else {
                if (isset($_SESSION['lang'])) {
                    $_ENV['lang']=$_SESSION["lang"];
                } else if (isset($_ENV['lang'])) {
                    $_SESSION["lang"]=$_ENV['lang'];
                } else {
                    $_SESSION['lang'] = $_ENV["lang"] = 'eng';
                }
            }
        }
    }
    $_ENV["locales"]=wbListLocales();
    $_ENV["settings"]["js_locale"]=substr($_SESSION["lang"],0,2);


    if (isset($_ENV['settings']['path_tpl']) and $_ENV['settings']['path_tpl'] > '') {
		$_ENV['base']=$_ENV['settings']['path_tpl'];
        $_ENV['path_tpl'] = $_ENV['path_app'].$_ENV['base'];
    }
    if (isset($_ENV['settings']['thumb_width']) and $_ENV['settings']['thumb_width'] > '0') {
        $_ENV['thumb_width'] = $_ENV['settings']['thumb_width'];
    }
    if (isset($_ENV['settings']['thumb_height']) and $_ENV['settings']['thumb_height'] > '0') {
        $_ENV['thumb_height'] = $_ENV['settings']['thumb_height'];
    }
    if (isset($_ENV['settings']['intext_width']) and $_ENV['settings']['intext_width'] > '0') {
        $_ENV['intext_width'] = $_ENV['settings']['intext_width'];
    }
    if (isset($_ENV['settings']['intext_height']) and $_ENV['settings']['intext_height'] > '0') {
        $_ENV['intext_height'] = $_ENV['settings']['intext_height'];
    }
    if (isset($_ENV['settings']['page_size']) and is_numeric($_ENV['settings']['page_size'])) {
        $_ENV['page_size'] = $_ENV['settings']['page_size'];
    }
    $_ENV['sysmsg'] = wbGetSysMsg();

    // Load tags
    $_ENV['tags'] = wbListTags();
    foreach(array_keys($_ENV['tags']) as $name) {
        require_once $_ENV['tags'][$name];
    }
}

function wbGetSysMsg() {
    $locale=array();
    if (is_file($_ENV["path_app"]."/forms/common/system_messages.ini")) {
        $locale=parse_ini_file($_ENV["path_app"]."/forms/common/system_messages.ini",true);
    } else if (is_file($_ENV["path_engine"]."/forms/common/system_messages.ini")) {
        $locale=parse_ini_file($_ENV["path_engine"]."/forms/common/system_messages.ini",true);
    }
    if (isset($locale[$_SESSION["lang"]])) {
        $locale=$locale[$_SESSION["lang"]];
    }
    return $locale;
}


function wbMailer(
    $from = null, $sent = null, $subject = null, $message = null, $attach = null
) {
    return wbMail($from, $sent, $subject, $message, $attach);
}



function wbMail(
    $from = null, $sent = null, $subject = null, $message = null, $attach = null
) {
    if ($from == null) {
        $from=$_ENV["settings"]["email"].";".$_ENV["settings"]["header"];
    } else if (!is_array($from)) {
        if (strpos($from,";")) {
            $from=explode(";",$from);
        }
        else {
            $from=array($from,strip_tags($_ENV['settings']['header']));
        }
    }
    if (!is_array($sent) AND is_string($sent) AND strpos($sent,";")) {
        $sent=array(explode(";",$sent));
    }
    else if (!is_array($sent)) {
        $sent=array(array($sent,$sent));
    } else if (is_array($sent) AND !is_array($sent[0]) AND strpos($sent[0],";")) {
        foreach($sent as $k => $s) {
            if (!is_array($s)) {$sent[$k]=explode(";",$s);}
        }
    } else if (is_array($sent) AND !is_array($sent[0]) AND !strpos($sent[0],";")) {
        $sent=array($sent);
    }

    if ($_ENV["settings"]["phpmailer"]=="on") {
        require_once __DIR__.'/modules/phpmailer/phpmailer/PHPMailerAutoload.php';
        $sett=$_ENV["settings"]["phmail"];
        $mail = ($sett["func"]=="sendmail") ? new PHPMailer(true) : new PHPMailer();
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
        if ($sett["smtp"]=="on") {
		$mail->isSMTP();
		$mail->Host = $sett["host"];
		$sett["smtp"]=="on" ? $mail->SMTPAuth = true : $mail->SMTPAuth = false;
		$mail->Username = $sett["username"];
		$mail->Password = $sett["password"];
		$mail->SMTPSecure = $sett["secure"];
		intval($sett["port"]) > 0 ? $mail->Port = intval($sett["port"]) : $mail->Port = 587;
	}
        $mail->setFrom($from[0], $from[1]);
        $mail->addReplyTo($from[0], $from[1]);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        foreach($sent as $s) $mail->addAddress($s[0], $s[1]);
        //Read an HTML message body from an external file, convert referenced images to embedded,
        //convert HTML into a basic plain-text alternative body
        $mail->msgHTML($message, dirname(__FILE__));
        $mail->CharSet = 'utf-8';
        //Replace the plain text body with one created manually
        $mail->AltBody = strip_tags($message);
        //Attach an image file

        if (!is_array($attach) AND is_string($attach)) $attach=array($attach);
        if (is_array($attach)) foreach($attach as $a) $mail->addAttachment($a);
        //send the message, check for errors
        $mail->send();
        $error=$_ENV["error"][__FUNCTION__]=$mail->ErrorInfo;
    } else {

// Set content-type header for sending HTML email
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

// Additional headers
        $headers .= "From: {$from[0]}\r\n"."X-Mailer: php";
        foreach($sent as $s) {
            $error=mail($s[0],$subject,$message,$headers);
            if ($error==true) {$error=false;} else {$error=true;}
            $_ENV["error"][__FUNCTION__]=$error;

        }
    }
        if ($error>"") {
            return false;
        }
        else {
            return true;
        }
}

function wbCheckWorkspace()
{
    if (!is_readable($_ENV['path_app']) or !is_writable($_ENV['path_app'])) {
        @chmod($_ENV['path_app'], 0766);
        if (!is_readable($_ENV['path_app']) or !is_writable($_ENV['path_app'])) {
            $out = wbGetTpl('setup.htm');
            $error = $out->find('#errors #rights');
            $out->find('#error.alert-warning')->html($error);
            $out->find('#wizard')->remove();
            $out->wbSetData();
            echo $out;
            die;
        }
    }
}

function wbFormUploadPath()
{
    $path = '/uploads';
    if ('form' == $_ENV['route']['controller']) {
        if (isset($_ENV['route']['form']) and $_ENV['route']['form'] > '') {
            $path .= '/'.$_ENV['route']['form'];
        } else {
            $path .= '/undefined';
        }
        if (isset($_ENV['route']['item']) and $_ENV['route']['item'] > '') {
            $path .= '/'.$_ENV['route']['item'];
        } else {
            $path .= '/undefined';
        }
    }
    elseif ('ajax' == $_ENV['route']['controller'] and 'buildfields' == $_ENV['route']['mode'] and isset($_POST['data'])) {
        if (isset($_POST['data']['_form']) and $_POST['data']['_form'] > '') {
            $path .= '/'.$_POST['data']['_form'];
        } else {
            $path .= '/undefined';
        }
        if (isset($_POST['data']['_id']) and $_POST['data']['_id'] > '') {
            $path .= '/'.$_POST['data']['_id'];
        } else {
            $path .= '/undefined';
        }
    } else {
        $path .= '/undefined';
    }

    return $path;
}

function wbInitFunctions()
{
    wbTrigger('func', __FUNCTION__, 'before');
    if (is_file($_ENV['path_app'].'/functions.php')) {
        require_once $_ENV['path_app'].'/functions.php';
    }
    foreach ($_ENV['forms'] as $form) {
        $inc = array(
                   "{$_ENV['path_engine']}/forms/{$form}.php", "{$_ENV['path_engine']}/forms/{$form}/{$form}.php",
                   "{$_ENV['path_app']}/forms/{$form}.php", "{$_ENV['path_app']}/forms/{$form}/{$form}.php",
               );
        foreach ($inc as $k => $file) {
            if (is_file("{$file}")) {
                include_once "{$file}";
            }
        }
    }
    foreach ($_ENV['modules'] as $module) {
        $inc = array(
                   "{$_ENV['path_engine']}/modules/{$module}.php", "{$_ENV['path_engine']}/modules/{$module}/{$module}.php",
                   "{$_ENV['path_app']}/modules/{$module}.php", "{$_ENV['path_app']}/modules/{$module}/{$module}.php",
               );
        foreach ($inc as $k => $file) {
            if (!is_callable($module.'__init') && !is_callable($module.'_init') && is_file($file)) {
                include_once $file;
            }
        }
    }
}

function wbGetUserUi($details=false) {
    $prop=wbGetUserUiConfig();
    if ($prop==null) {
        $conf=wbItemRead("users",$_SESSION["user_role"]);
        if (!$_ENV["last_error"] AND isset($conf["roleprop"]) AND $conf["roleprop"] !== "") {
            if ($details) {
                $prop=array();
                $prop["roleprop"]=wbItemToArray($conf["roleprop"]);
                $prop["_roleprop__dict_"]=wbItemToArray($conf["_roleprop__dict_"]);
            } else {
                $prop=wbItemToArray($conf["roleprop"]);
            }

        }
    }
    if ($prop==null) {
        $conf=wbItemRead("users:engine","admin");
        if (!$_ENV["last_error"]) {
            if ($details) {
                $prop=array();
                $prop["roleprop"]=wbItemToArray($conf["roleprop"]);
                $prop["_roleprop__dict_"]=wbItemToArray($conf["_roleprop__dict_"]);
            } else {
                $prop=wbItemToArray($conf["roleprop"]);
            }
        } else {
            if ($details) {
                $prop=array();
                $prop["roleprop"]=array();
                $prop["_roleprop__dict_"]=array();
            } else {
                $prop=array();
            }
        }
    }
    return $prop;
}

function wbGetUserUiConfig($prop=null) {
    if ($prop==null) {
        $prop=wbTreeRead("_config");
        $prop=wbItemToArray($prop["tree"]);
    }
    if (is_array($prop)) {
        foreach($prop as $key => $item) {
            $item=wbItemToArray($item);
            if (is_array($item)) {
                $item=wbGetUserUiConfig($item);
                if (!isset($item["data"])) {
                    $item["data"]=array();
                }
                $item["data"]["visible"]="on";
                $prop[$key]=$item;
            }
        };
    }
    return $prop;
}

function wbProfData(&$xhprof_data) {
	$tmp=$xhprof_data;
	$last=array_pop($tmp);
	$sec=$last["wt"];
	foreach($xhprof_data as $key=> &$item) {
		$item["func"]=explode("==>",$key);
		$item["func"]=$item["func"][1];
	}
	$xhprof_data = wbArraySort($xhprof_data,"wt:d");
	$tpl=wbFromString('
	<script data-wb-src="jquery"></script>
	<script data-wb-src="bootstrap3"></script>
	<table class="table table-striped">
	<thead><tr>
	<th>Name</th><th>Calls</th><th>Time</th><th>MemUse</th><th>PeakMemUse</th><th>CPU</th>
	</tr></thead>
	<tbody data-wb-role="foreach" data-wb-from="data">
	<tr>
	<td class="text-right">{{func}}</td>
	<td class="text-center">{{ct}}</td>
	<td class="text-right">{{wt->round(@ / 1000000,4)}}</td>
	<td class="text-right">{{mu->number_format()}}</td>
	<td class="text-right">{{pmu->number_format()}}</td>
	<td class="text-center">{{cpu}}</td>
	</tr>
	</tbody>
	</table>');
	$tpl->wbSetData(array("data"=>$xhprof_data));
	return $tpl->outerHtml();
}

function wbItemToArray(&$Item = array(),$convid = true)
{
	if (isset($Item["_table"]) && $Item["_table"]=="admin" && $Item["id"]=="settings") {$convid=false;}
    if ((array)$Item === $Item) {
        $tmpItem=array();
        foreach ($Item as $i => $item) {
			if (substr($i,0,1) !== "%" AND $i !== "_parent") {
				if (!((array)$item === $item))  {
					$tmp = json_decode($item, true);
					if ((array)$tmp === $tmp) {
						$item = wbItemToArray($tmp,$convid);
					}
				}
				$item = wbItemToArray($item,$convid);
			}
			if ( $convid == true AND (array)$item === $item AND isset($item['id'])) {
				$tmpItem[$item['id']] = $item;
			} else {
				$tmpItem[$i] = $item;
			}

        }
        $Item=$tmpItem;
    } else if ( !(array($Item) === $Item)) {
        $tmp = json_decode($Item, true);
        if ((array)$tmp === $tmp) {
            $Item = wbItemToArray($tmp,$convid);
        }
    }
    return $Item;
}

function wbGetDataWbFrom($Item, $str)
{
    $str = trim($str);
    $str_1=json_decode(wbSetValuesStr("{{".$str."}}",$Item),true);
    if (is_array($str_1)) return $str_1;

    if (substr($str,0,1)=="_" AND $str !== $str_1) {
        // если в атрибуте data-wb-from указанна общая переменная (типа _ENV, _SESS)
        $tmp=json_encode($str_1,true);
        if (is_array($tmp)) {
            return $tmp;
        }
        else {
            return $str_1;
        }
    }
    if (strpos($str,"}}")) $str = wbSetValuesStr($str, $Item);

    $pos = strpos($str, '[');
    if ($pos) {
        $fld = '['.substr($str, 0, $pos).']';
        $suf = substr($str, $pos);
        $fld .= $suf;
        $fld = str_replace('[', '["', $fld);
        $fld = str_replace(']', '"]', $fld);
        $fld = str_replace('""', '"', $fld);
        if (eval('return isset($Item'.$fld.');')) {
            eval('$res=$Item'.$fld.';');
        } else {
            $res="";
        }

        return $res;
    }
    if (isset($Item[$str])) {
        return $Item[$str];
    } else {
        return null;
    }
}

function wbMerchantList($type = 'both')
{
    $res = array();
    if ('both' == $type) {
        $res_e = wbMerchantList('engine');
        $res_a = wbMerchantList('app');

        return array_merge($res_e, $res_a);
    }
    $dir = $_ENV["path_{$type}"].'/modules';
    if (is_dir($dir)) {
        exec("find {$dir} -maxdepth 2 -name '*.php'", $list);
        foreach ($list as $val) {
            $file = $val;
            if (is_file($file) AND !strpos($file,"_")) {
                $php = strtolower(trim(file_get_contents($file)));
                $form = array_pop(explode('/', $file));
                $form = explode('.php', $form);
                $form = $form[0];
                if ((strpos($php, "function {$form}_checkout") and strpos($php, "function {$form}_success"))
                        or (strpos($php, "function {$form}__checkout") and strpos($php, "function {$form}__success"))) {
                    $arr = array();
                    $arr['id'] = $form;
                    $arr['name'] = $form;
                    $arr['dir'] = $dir;
                    $arr['type'] = $type;
                    $res[] = $arr;
                }
            }
        }
    }
    unset($dir,$list,$val,$form,$php,$file,$arr);
    return $res;
}

function wbFieldBuild($param, $data = array(),$locale=array())
{
	$param=wbItemToArray($param);
    $set = wbGetForm('common', 'tree_fldset');
    $tpl = wbGetForm('snippets', $param['type']);
    $opt=$param["prop"];
    $lang=$param["lang"];
    $options = '';
    if (isset($opt['required']) and true == $opt['required']) {
        $options .= ' required ';
    }
    if (isset($opt['readonly']) and true == $opt['readonly']) {
        $options .= ' readonly ';
    }
    if (isset($opt['disabled']) and true == $opt['disabled']) {
        $options .= ' disabled ';
    }
    $param['options'] = trim($options);
    $label=$param['label'];
    if (isset($lang["labels"]) AND isset($lang["labels"][$_SESSION["lang"]]) AND $lang["labels"][$_SESSION["lang"]]["name"]>"") $label=$param['label']=$lang["labels"][$_SESSION["lang"]]["name"];

	if ($param["name"]=="" AND $paran["label"]=="") return ;
    switch ($param['type']) {
    case 'number':
        if (isset($opt['min'])) {
            $tpl->find('input')->attr('min', $opt['min']);
        }
        if (isset($opt['max'])) {
            $tpl->find('input')->attr('max', $opt['max']);
        }
        if (isset($opt['step'])) {
            $tpl->find('input')->attr('step', $opt['step']);
        }
        if (isset($opt['datalist'])) {
            $param['listid'] = wbNewId();
            $tpl->find('input')->attr('list', $param['listid']);
            $tpl->find('datalist')->attr('data-wb-from', wbJsonEncode($opt['datalist']));
            $tpl->find('datalist')->attr('data-wb-role', 'foreach');
        } else {
            $tpl->find('datalist')->remove();
        }
        break;
    case 'enum':
	$arr=array();
	if ($param['value'] > '' AND strpos(";",$param['value'])) {
            $param['enum'] = array();
            $arr = explode(';', $param['value']);
        }
	$param=wbItemToArray($param);
	if (isset($param["prop"]["enum"])) $arr=explode(",",$param["prop"]["enum"]);
        foreach ($arr as $i => $line) {
		$line=ltrim(rtrim($line));
		$param['enum'][$line] = array('id' => $line, 'name' => $line);
        }
        $tpl->wbSetData($param);
	if (isset($data["type"])) {
		// если имя поля и одна из пропертей совпадает, то нужно фиксить
		$tpl->find("[name=type]")->attr("value",$data["type"]);
	}
        break;
    case 'image':
        if (isset($_POST['data-id']) AND $_POST['_form']=="tree") {
            $data["path"]="/uploads/{$_POST['_form']}/{$_POST['_item']}/{$_POST['data-id']}/";
        } else {
            $data["path"]="/uploads/{$data['_form']}/{$data['_item']}/";
        }
        $tpl->find('[data-wb-role=uploader]')->attr('data-wb-path',$data["path"]);
        $tpl->wbSetValues($param);
        $tpl->wbSetData($data);
        break;
    case 'gallery':
        if (isset($_POST['data-id']) AND $_POST['_form']=="tree") {
            $data["path"]="/uploads/{$_POST['_form']}/{$_POST['_item']}/{$_POST['data-id']}/";
        } else {
            $data["path"]="/uploads/{$data['_form']}/{$data['_item']}/";
        }
        $tpl->find('[data-wb-role=uploader]')->attr('data-wb-path',$data["path"]);
        $tpl->wbSetValues($param);
        $tpl->wbSetData($data);
        break;
    case 'forms':
        $env=$_ENV;
        $get=$_GET;
	$par=$param;
        $param=wbItemToArray($param);
        $form=$param["prop"]["form"];
        $mode=$param["prop"]["mode"];
        $_ENV["route"]["form"]=$param["_form"]=$_GET["form"]=$form;
        $_ENV["route"]["mode"]=$param["_mode"]=$_GET["mode"]=$mode;
        $tpl=wbGetForm($form,$mode);
        if ($param["prop"]["selector"]>"") {$tpl=$tpl->find($param["prop"]["selector"],0)->clone();}
        $tpl->wbSetValues($param);
        $tpl->wbSetData($data);
        $tpl->find(".nav-tabs .nav-item:first-child")->addClass("active");
        $_ENV=$env;
        $_GET=$get;
        $param=$par;
        unset($env,$get,$par);
        break;
    case 'module':
        if (!is_array($opt)) break;
        foreach($opt as $key => $val) {
            $tpl->find("[data-wb-role]:first")->attr($key,$val);
        }
        $tpl->wbSetValues($param);
        $tpl->wbSetData($data);
        break;
    case 'multiinput':
        $tpl->wbSetValues($param);
	$wrp=wbGetForm('common','multiinput_wrapper');
        $field=$param["name"];
        $flds = wbFromString('');
        if (isset($param["prop"]["multiflds"])) {
	$arr=$param["prop"]["multiflds"];
        foreach ($arr as $i => $multi) {
		if (!isset($multi["style"])) $multi["style"]="";
		if (!isset($multi["class"])) $multi["class"]="";
		$name = 'data'.$i;
		if ($multi["name"] > "") $name=$multi["name"];
		$line = $wrp->clone();
		$snip = wbGetForm('snippets', $multi['type']);
		$line->find(":first")->attr("class",$multi["class"]);
		$line->find(":first")->attr("style",$multi["style"]);
		$line->find(":first")->append($snip);
		$line->find('[name]')->attr('name', $name);
		$line->wbSetValues($multi);
		$line->find(".wb-value")->removeAttr("value");
		$line->find(".wb-value")->removeClass("wb-value");
		$line->find(".wb-attrs")->removeClass("wb-attrs");
		$flds->append($line);
        }
	}
        $tpl->find('[data-wb-role=multiinput]')->html($flds);
	$tpl->wbSetData($data);
        unset($flds);
        break;
    }
    if (isset($param["style"]) AND $param["style"]>"") {
	    // old
        $style=$tpl->attr("style");
        $tpl->find(":first")->attr("style",$style.$param["style"]);
    }

	if (isset($param["prop"]["class"])) $tpl->find(":first")->addClass($param["prop"]["class"]);
	if (isset($param["prop"]["style"])) $tpl->find(":first")->attr("style",$param["prop"]["style"]);

    $set->find('.form-group > label')->html($label);
    $set->find('.form-group > div')->html($tpl->outerHtml());
    $set->wbSetData($param);
    $set->wbSetValues($data);

    return $set->outerHtml();
}

function wbInitDatabase()
{
    wbTrigger('func', __FUNCTION__, 'before');
    if (!is_dir($_ENV['dbe'])) @mkdir($_ENV['dbe'], 0766);
    if (!is_dir($_ENV['dba'])) @mkdir($_ENV['dba'], 0766);
    if (!is_dir($_ENV['dbec'])) @mkdir($_ENV['dbec'], 0766);
    if (!is_dir($_ENV['dbac'])) @mkdir($_ENV['dbac'], 0766);
}

function wbFlushDatabase()
{
    wbTrigger('func', __FUNCTION__, 'before');
    $etables = wbTableList(true);
    $atables = wbTableList();
    foreach ($etables as $key) {
        wbTableFlush($_ENV['dbe'].'/'.$key);
    }
    foreach ($atables as $key) {
        wbTableFlush($_ENV['dba'].'/'.$key);
    }
}

function wbTable($table = 'data', $engine = false)
{
    wbTrigger('func', __FUNCTION__, 'before');
    $create = false;
    if (strpos($table,":")) {
        $table=explode(":",$table);
        if ($table[1]=="engine" OR $table[1]=="e") {
            $engine=true;
        } else if ($table[1]=="create" OR $table[1]=="c") {
		$create = true;
	}
        //$table=$table[0];
        $table=$table[1];
    }

    if (substr($table,0,strlen($_ENV['dbe'])) == $_ENV['dbe']) {
        $engine = true;
    }
    if (false == $engine) {
        $db = $_ENV['dba'];
    } else {
        $db = $_ENV['dbe'];
    }
    $tname = wbTableName($table);
    $table = wbTablePath($tname, $engine);
    if (!is_file($table)) {
        if ( $tname > '' and ( in_array($tname, $_ENV['forms'], true) or $create == true )) {
            wbTableCreate($tname);
        }
    }
    if (!is_file($table)) {
        wbError('func', __FUNCTION__, 1001, func_get_args());
        $table = null;
    } else {
        $_ENV[$table]['name'] = $tname;
    }
    wbTrigger('func', __FUNCTION__, 'after', func_get_args(), $table);

    return $table;
}

function wbTableName($table)
{
    $table = explode('/', $table);
    $table = array_pop($table);
    $table = str_replace('.json', '', $table);
    if (strpos($table,":")) {
        $table=explode(":",$table);
        $table=$table[0];
    }
    return $table;
}

function wbTableCreate($table = 'data', $engine = false)
{
    wbTrigger('func', __FUNCTION__, 'before');
    if (false == $engine) {
        $db = $_ENV['dba'];
    } else {
        $db = $_ENV['dbe'];
    }
    $table = wbTablePath($table, $engine);
    if (!is_file($table) and is_dir($db)) {
        $json = wbJsonEncode(null);
        $res = file_put_contents($table, $json, LOCK_EX);
        if ($res) {
            @chmod($table, 0766);
        } else {
            $table = null;
        }
    } else {
        wbError('func', __FUNCTION__, 1002, func_get_args());
    }
    wbTrigger('func', __FUNCTION__, 'after', func_get_args(), $table);

    return $table;
}

function wbTableRemove($table = null, $engine = false)
{
    $res = false;
    if (wbRole('admin')) {
        $cache = wbTableCachePath($table, $engine);
        $table = wbTablePath($table, $engine);
        wbRrecurseDelete($cache);
        if (is_file($table)) {
            wbRrecurseDelete($cache);
            unlink($table);
            if (is_file($table)) { // не удалилось
                wbError('func', __FUNCTION__, 1003, func_get_args());
            }
            $res = $table;
        } else { // не существует
            wbError('func', __FUNCTION__, 1001, func_get_args());
        }
    }

    return $res;
}

function wbTableExist($table)
{
    if (is_file($_ENV['dba'].'/'.$table.'.json')) {
        return true;
    }

    return false;
}

function wbTablePath($table = 'data', $engine = false)
{
    if (false == $engine) {
        $db = $_ENV['dba'];
    } else {
        $db = $_ENV['dbe'];
    }
    $table = $db.'/'.$table.'.json';
    wbTrigger('func', __FUNCTION__, 'after', func_get_args(), $table);
    return $table;
}

function wbTableCachePath($table = 'data', $engine = false)
{
    if (false == $engine) {
        $db = $_ENV['dbac'];
    } else {
        $_ENV['dbec'];
    }
    $table = $db.'/'.$table;
    wbTrigger('func', __FUNCTION__, 'after', func_get_args(), $table);

    return $table;
}

function wbTableList($engine = false)
{
    if (false == $engine) {
        $db = $_ENV['dba'];
    } else {
        $db = $_ENV['dbe'];
    }
    $list = wbListFiles($db);
    foreach ($list as $i => $table) {
        $tmp = explode('.', $table);
        if ('json' !== array_pop($tmp)) {
            unset($list[$i]);
        } else {
            $list[$i] = substr($table, 0, -5);
        }
    }
    wbTrigger('func', __FUNCTION__, 'after', func_get_args(), $list);

    return $list;
}


function wbListItems($table = 'pages', $where = '', $sort = null)
{
    return wbItemList($table = 'pages', $where = '', $sort = null);
}
function wbItemList($table = 'pages', $where = '', $sort = null)
{
    ini_set('max_execution_time', 900);
    ini_set('memory_limit', '1024M');
    wbTrigger('form', __FUNCTION__, 'BeforeItemList', func_get_args(), array());
    $call="wb".ucfirst(wbTableName($table))."ItemList";
    if (is_callable($call) AND $call !== __FUNCTION__) $list=$call($table,$where,$sort);
    if (!isset($list)) {
	    $drv=wbItemDriver(__FUNCTION__,func_get_args());
	    if ($drv!==false) {
		    $list = $drv["result"];
	    } else {
		    if (is_string($where)) {
				$where = $cWhere = wbSetValuesStr($where);
			} else {
				$cWhere=null;
			}
		    $list = array();
		    $table = wbTable($table);
		    $tname = wbTableName($table);
		    if (!is_file($table)) {
			wbError('func', __FUNCTION__, 1001, func_get_args());

			return array();
		    }
		    if ($cWhere !== null AND isset($_ENV['cache'][md5($table.$where.$sort.$_ENV["lang"].$_SESSION["lang"])])) {
			$list = $_ENV['cache'][md5($table.$where.$sort.$_ENV["lang"].$_SESSION["lang"])];
		    } else {
			$list = wb_file_get_contents($table);
			if (substr($list,0,1)=="{") {
			    $list = json_decode($list,true);
			}
			else {
			    $list=unserialize($list);
			}

			if ((array)$list === $list) {
			    foreach ($list as $key => $item) {
				$item['_table'] = $tname;
				if ($tname!=="tree") $item=wbItemToArray($item);
				$item = wbTrigger('form', __FUNCTION__, 'AfterItemRead', func_get_args(), $item);
				if (
				    ('_' == substr($item['id'], 0, 1) and 'admin' !== $_SESSION['user_role'])
				    or
				    (null == $item)

				    or (isset($item['_removed']) and true == $item['_removed'])
				) {
				    unset($list[$key]);
				}
				elseif (is_string($where) AND $where > '' AND !wbWhereItem($item, $where)) {
				    unset($list[$key]);
				} else if (!is_string($where) ) {
					// не реализовано
					//call_user_func_array($where,$list[$key]);
					//unset($list[$key]);
				} else {
				    $list[$key] = $item;
				}
			    }
			}
		    }
		    if (!is_array($list)) {
			$list = array();
		    }

		    if (null !== $sort) {
			$list = wbArraySortMulti($list, $sort);
		    }
		    if ($cWhere !== null) $_ENV['cache'][md5($table.$where.$sort.$_ENV["lang"].$_SESSION["lang"])] = $list;

	    }
    }
    $list = wbTrigger('form', __FUNCTION__, 'AfterItemList', func_get_args(), $list);
    $list = wbTrigger('func', __FUNCTION__, 'after', func_get_args(), $list);

    return $list;
}

function wbTreeRead($name)
{
    wbTrigger('form', __FUNCTION__, 'BeforeTreeRead', func_get_args(), array());
    $tree = wbItemRead('tree', $name);
    if ($_ENV["last_error"]==1006) return null;
    if (!isset($tree['tree'])) {
        $tree['tree'] = array();
    } else {
        $tree['tree'] = json_decode($tree['tree'], true);
    }
    if (!isset($tree['_tree__dict_'])) {
        $tree['dict'] = array();
    } else {
        $tree['dict'] = json_decode($tree['_tree__dict_'], true);
    }
    $tmp=wbTreeToArray($tree['tree']);
    $tree["assoc"] = wbItemToArray($tmp);
    $tree = wbTrigger('form', __FUNCTION__, 'AfterTreeRead', func_get_args(), $tree);

    return $tree;
}

function wbTreeToArray($tree) {
    $assoc=array();
    if (!is_array($tree)) return $assoc;
    foreach($tree as $i => $item) {
        if (isset($item["children"])  AND is_array($item["children"]) AND count($item["children"]) ) {
            $item["children"]=wbTreeToArray($item["children"]);
        }
        if (isset($item["id"])) {
            $key=$item["id"];
        } else {
            $key=$i;
        }
        if (!is_array($item["children"]) OR !count($item["children"])) {
            $item["children"]="";
        }
        if (!is_array($item["data"]) OR !count($item["data"])) {
            $item["data"]="";
        }
        $assoc[$key]=$item;

    }
    return $assoc;
}

function wbTreeFindBranchById($Item, $id)
{
    //$Item=wbItemToArray($Item);
    $res = false;
    if (is_array($Item)) {
        foreach ($Item as $item) {
            if ($item['id'] === $id) return $item;
            if (is_array($item['children'])) {
                $res = wbTreeFindBranchById($item['children'], $id);
                if ($res) return $res;
            }
        }
    }
    return $res;
}

function wbTreeFindBranch($tree, $branch = '', $parent = 'true', $childrens = 'true')
{
    //$tree=wbItemToArray($tree);
    if ($branch > '') {
        $branch = html_entity_decode($branch);
        $br = explode('->', $branch);
        foreach ($br as $b) {
            $tree = array(wbTreeFindBranchById($tree, rtrim(ltrim($b))));
        }
        if ('false' == $childrens) unset($tree['children']);
        if ('false' == $parent) $tree = $tree[0]['children'];
    }
    return $tree;
}

function wbTreeWhere($tree, $id, $field, $inc = true)
{
    if (!is_array($tree)) {
        $tree = wbTreeRead($tree);
        $tree_id = $tree['id'];
        $tree = $tree['tree'];
    } else {
        $tree_id = $tree['id'];
    }
    if (strpos($id, '->')) {
        $tree = wbTreeFindBranch($tree, $id);
        $tree = $tree[0];
    } else {
        $tree = wbTreeFindBranchById($tree, $id);
    }
    $cache_id = md5($tree_id.$id.$field.$inc.$_ENV["lang"].$_SESSION["lang"]);
    if (isset($_ENV['cache'][__FUNCTION__][$cache_id])) {
        return $_ENV['cache'][__FUNCTION__][$cache_id];
    }
    $list = wbTreeIdList($tree);
    $where = '';
    foreach ($list as $key => $val) {
        if (0 == $key) {
            $where .= '"'.$val.'"';
        } else {
            $where .= ',"'.$val.'"';
        }
    }
    $where = "in_array({$field},array({$where}))";
    $_ENV['cache'][__FUNCTION__][$cache_id] = $where;

    return $_ENV['cache'][__FUNCTION__][$cache_id];
}

function wbTreeIdList($tree, $list = array())
{
    if (isset($tree['id'])) {
        $list[] = $tree['id'];
    }
    //$tree = wbItemToArray($tree);
    if (isset($tree['children']) and is_array($tree['children'])) {
        foreach ($tree['children'] as $key => $child) {
            $list = wbTreeIdList($child, $list);
        }
    }

    return $list;
}

function wbWhereLike($ref, $val)
{
    if (is_array($ref)) {
        $ref = implode('|', $ref);
    } else {
        $val = trim($val);
        $val = str_replace(' ', '|', $val);
    }
    $res = preg_match("/{$val}/ui", $ref);

    return $res;
}

function wbWhereNotLike($ref, $val)
{
    if (is_array($ref)) {
        $ref = implode('|', $ref);
    } else {
        $val = trim($val);
        $val = str_replace(' ', '|', $val);
    }
    $res = preg_match("/{$val}/ui", $ref);
    if (1 == $res) {
        $res = 0;
    } else {
        $res = 1;
    }

    return $res;
}

function wbJsonEncode($Item = array())
{
	if (version_compare(phpversion(),"5.6")<0) {
		return stripcslashes(wbJsonEncodeAlt($Item));
	} else {
		return json_encode($Item, JSON_UNESCAPED_UNICODE | JSON_HEX_AMP | JSON_HEX_TAG | JSON_HEX_APOS | JSON_PARTIAL_OUTPUT_ON_ERROR | JSON_HEX_QUOT);
	}
}

function wbJsonEncodeAlt($a=false)
{
  if (is_null($a)) return 'null';
  if ($a === false) return 'false';
  if ($a === true) return 'true';
  if (is_scalar($a))
  {
    if (is_float($a))
    {
      // Always use "." for floats.
      $a = str_replace(",", ".", strval($a));
    }

    // All scalars are converted to strings to avoid indeterminism.
    // PHP's "1" and 1 are equal for all PHP operators, but
    // JS's "1" and 1 are not. So if we pass "1" or 1 from the PHP backend,
    // we should get the same result in the JS frontend (string).
    // Character replacements for JSON.
    static $jsonReplaces = array(array("\\", "/", "\n", "\t", "\r", "\b", "\f", '"'),
    array('\\\\', '\\/', '\\n', '\\t', '\\r', '\\b', '\\f', '\"'));
    return '"' . str_replace($jsonReplaces[0], $jsonReplaces[1], $a) . '"';
  }
  $isList = true;
  for ($i = 0, reset($a); $i < count($a); $i++, next($a))
  {
    if (key($a) !== $i)
    {
      $isList = false;
      break;
    }
  }
  $result = array();
  if ($isList)
  {
    foreach ($a as $v) $result[] = wbJsonEncodeAlt($v);
    return '[ ' . join(', ', $result) . ' ]';
  }
  else
  {
    foreach ($a as $k => $v) $result[] = wbJsonEncodeAlt($k).': '.wbJsonEncodeAlt($v);
    return '{ ' . join(', ', $result) . ' }';
  }
}

function wbItemDriver($func,$args=array()) {
	if (isset($_ENV["settings"]["dbdrv"]) AND $_ENV["settings"]["dbdrv"]) {
		$func=str_replace("wb",$_ENV["settings"]["dbdrv"],$func);
		if (!is_callable($func)) return false;
		$result=null; // нужно придумать вы
		return [
			"func"=>$func,
			"result"=>$result
		];
	} else {return false;}
}

function wbItemRead($table = null, $id = null)
{
    if (null == $table) $table = $_ENV['route']['form'];
    if (null == $id) $id = $_ENV['route']['item'];
    if ($table=="" OR $table==null) return;
    wbTrigger('form', __FUNCTION__, 'BeforeItemRead', func_get_args(), array());
    $call="wb".ucfirst(wbTableName($table))."ItemRead";
    if (is_callable($call)) $item=$call($table,$id);
    if (!isset($item)) {
	    $drv=wbItemDriver(__FUNCTION__,func_get_args());
	    if ($drv!==false) {
		    $item=$drv["result"];
	    } else {
		    $table = wbTable($table);
		    if (isset($_ENV['cache'][md5($table.$_ENV["lang"].$_SESSION["lang"])][$id])) {
			$item = $_ENV['cache'][md5($table.$_ENV["lang"].$_SESSION["lang"])][$id];
		    } else {
			$list = wbItemList($table);
			if (isset($list[$id])) {
			    $item = $list[$id];
			} else {
			    wbError('func', __FUNCTION__, 1006, func_get_args());
			    $item = null;
			}
		    }
	    }
    }
    if (null !== $item) {
        if (isset($item['images']) && $_ENV["route"]["mode"]!=="edit") $item = wbImagesToText($item);
        if (isset($item['_removed']) && 'remove' == $item['_removed']) $item = null; // если стоит флаг удаления, то возвращаем null
        $item = wbTrigger('form', __FUNCTION__, 'AfterItemRead', func_get_args(), $item);
    } else {
        $item = wbTrigger('form', __FUNCTION__, 'EmptyItemRead', func_get_args(), $item);
    }
    return $item;
}

function wbCacheCheck() {
    $cache = array("check"=>false,"id"=>false,"path"=>false,"data"=>false);
    if (isset($_ENV["route"]["params"]) AND $_ENV["route"]["params"]["wbcache"] AND in_array($_ENV["route"]["params"]["wbcache"],["update","false"])) {
        unset($_ENV["route"]["params"]["wbcache"]);
        if (!count($_ENV["route"]["params"])) unset($_ENV["route"]["params"]);
        return $cache;
    }
    if (isset($_ENV["settings"]["cache"]) AND is_array($_ENV["settings"]["cache"])) {
        foreach($_ENV["settings"]["cache"] as $line) {
            $c=wbAttrToArray($line["controller"]);
            $f=wbAttrToArray($line["form"]);
            $m=wbAttrToArray($line["mode"]);
            if (
                (in_array($_ENV["route"]["controller"],$c) OR $c==array("*") )
                AND     (in_array($_ENV["route"]["form"],$f) OR $f==array("*") OR ($f==array() AND !in_array("form",$c)))
                AND     (in_array($_ENV["route"]["mode"],$m) OR $m==array("*") OR ($m==array() AND !in_array("form",$c)))
                AND     $line["active"] == "on"
            )
            {
                $cacheId = md5(json_encode($_ENV["route"]).$_ENV["lang"].$_SESSION["lang"]);
                $cacheFile = $_ENV["dbac"]."/".$cacheId.".htm";
                if (!is_file($cacheFile)) {
                    $cache = array("check"=>null,"id"=>$cacheId,"path"=>$cacheFile,"data"=>false);
                } else {
                    $lastmod = filemtime($cacheFile);
                    $expired = $lastmod + $line["lifetime"]*1;
                    if (time() > $expired) {
                        $cache = array("check"=>null,"id"=>$cacheId,"path"=>$cacheFile,"data"=>false);
                    } else {
                        $data = file_get_contents($cacheFile);
                        $cache = array("check"=>true,"id"=>$cacheId,"path"=>$cacheFile,"data"=>$data);
                    }

                }
            }
        }
    }
    return $cache;
}


function wbCacheName($table, $id = null)
{
    $tmp = explode($_ENV['dbe'], $table);
    if (2 == count($tmp)) {
        $dbc = $_ENV['dbec'];
        $db = $_ENV['dbe'];
    } else {
        $dbc = $_ENV['dbac'];
        $db = $_ENV['dba'];
    }
    $tname = str_replace($db.'/', '', $table);
    if (!is_dir($db)) {
        mkdir($db, 0766);
    }
    if (!is_dir($dbc)) {
        mkdir($dbc, 0766);
    }
    if (!is_dir($dbc.'/'.$tname)) {
        mkdir($dbc.'/'.$tname, 0766);
    }
    if (null == $id) {
        $cache = $cache = $dbc.'/'.$tname;
    } else {
        $cache = $dbc.'/'.$tname.'/'.$id;
    }

    return $cache;
}

function wbItemRemove($table = null, $id = null, $flush = true)
{
    $res = false;
    $drv=wbItemDriver(__FUNCTION__,func_get_args());
    if ($drv!==false) {
	    $res = $drv["result"];
    } else {
	    $table = wbTable($table);
	    if (!is_file($table)) {
		wbError('func', __FUNCTION__, 1001, func_get_args());
		return null;
	    }
	    if (is_array($id)) {
		foreach($id as $iid) {
		    wbItemRemove($table, $iid, false);
		}
		if ($flush==true) {
		    wbTableFlush($table);
		}
	    } else if (is_string($id) OR is_numeric($id)) {
		if (strpos($id," ") OR strpos($id,'"') OR strpos($id,'=') OR strpos($id,'>') OR strpos($id,'<')) {
		    $list=wbItemList($table,$id);
		    foreach($list as $item) {
			wbItemRemove($table, $item["id"], false);
		    }
		    if ($flush==true) {
			wbTableFlush($table);
		    }
		} else {
		    $item = wbItemRead($table, $id);
		    if ($item == null)  return;
		    if (is_array($item)) {
			$item['_removed'] = true;
			$item=wbTrigger('form', __FUNCTION__, 'BeforeItemRemove', func_get_args(), $item);
			$_ENV['cache'][md5($table.$_ENV["lang"].$_SESSION["lang"])][$id] = $item;
		    }
		    $res = wbItemSave($table, $item, $flush);
		}
	    }
    }
    //if (!$res) {wbError('func', __FUNCTION__, 1007, func_get_args());}
    wbTrigger('form', __FUNCTION__, 'AfterItemRemove', func_get_args(), $item);
    return $res;
}

function wbSetChmod($ext = '.json')
{
    foreach ($_ENV['tables'] as $table) {
        if (is_file($_ENV['dba'].'/'.$table.$ext)) {
            @chmod($_ENV['dba'].'/'.$table.$ext, 0766);
        }
    }
}

function wbItemSave($table, $item = null, $flush = true)
{
	$table = wbTable($table);
	$item = wbItemSetTable($table, $item);
    $item = wbTrigger('form', __FUNCTION__, 'BeforeItemSave', func_get_args(), $item);
    $drv=wbItemDriver(__FUNCTION__,func_get_args());
    if ($drv!==false) {
	    $item = $drv["result"];
    } else {
	    $res = null;
	    if (!is_file($table)) {
		wbError('func', __FUNCTION__, 1001, func_get_args());
		return null;
	    }

	    if (!isset($_ENV['cache'][md5($table.$_ENV["lang"].$_SESSION["lang"])])) {
		$_ENV['cache'][md5($table.$_ENV["lang"].$_SESSION["lang"])] = array();
	    }
	    if (!isset($item['id']) or '_new' == $item['id']) {
		$item['id'] = wbNewId();
	    }

	    $_ENV['cache'][md5($table.$_ENV["lang"].$_SESSION["lang"])][$item['id']] = $item;
	    wbTrigger('form', __FUNCTION__, 'AfterItemSave', func_get_args(), $item);
	    $res = true;
	    if ($flush == true) {
		$res = wbTableFlush($table);
	    }
    }
    return $item["id"];
}

function wbTableFlush($table)
{
    // Сброс кэша в общий файл
    $res = false;
    $table = wbTable($table);
    $tname = wbTableName($table);
    $cache = $_ENV['cache'][md5($table.$_ENV["lang"].$_SESSION["lang"])];
    if (is_file($table) and isset($_ENV['cache'][md5($table.$_ENV["lang"].$_SESSION["lang"])])) {
        $fp = fopen($table, 'rb');
        flock($fp, LOCK_SH);
        $data = file_get_contents($table);
        if (substr($data,0,1)=="{") {
            $data = json_decode($data,true);
        } else {
            $data=unserialize($data);
        }
        $flag = false;
        foreach ($cache as $key => $item) {

            $item['_table'] = $tname;
            if (isset($data[$key])) {
                $data[$key]=array_merge($data[$key],$item);
            } else {
                $data[$key]=$item;
            }
            $flag = true;
            if (isset($item['_removed']) and true == $item['_removed']) {
                if (wbRole('admin')) {
                    unset($data[$key]);
                }
            }
        }
        if (isset($_ENV["settings"]["format"]) AND $_ENV["settings"]["format"]=="serialize") {
            $data = serialize($data);
        } else {
            $data = wbJsonEncode($data);
        }

        flock($fp, LOCK_UN);
        fclose($fp);
        if ($flag) {
            $res = file_put_contents($table, $data, LOCK_EX);
            wbLog('func', __FUNCTION__, 1009, func_get_args());
        } else {
            $res = null;
        }
        unset($_ENV['cache'][md5($table.$_ENV["lang"].$_SESSION["lang"])]);
    }

    return $res;
}

function wbItemSetTable($table, $item = null)
{
    $item['_table'] = wbTableName($table);
    $tmp=wbItemRead($item["_table"],$item["id"]);
    if (!$tmp or !isset($tmp['_created']) or '' == $tmp['_created']) {
        $item['_created'] = date('Y-m-d H:i:s');
    }
    if (!$tmp or !isset($tmp['_creator']) or '' == $tmp['_creator']) {
        $item['_creator'] = $_SESSION['user_id'];
    }
    $item['_lastdate'] = date('Y-m-d H:i:s');
    $item['_lastuser'] = $_SESSION['user_id'];

    $item["_id"] = $item["id"];
    return $item;
}

function wb_file_get_contents($file)
{
    $fp = fopen($file, 'rb');
    flock($fp, LOCK_SH);
    $contents = file_get_contents($file);
    flock($fp, LOCK_UN);
    fclose($fp);

    return $contents;
}

function wbTrigger($type, $name, $trigger, $args = null, $data = null)
{

	$env_error = $_ENV['error'];
	if (!isset($env_error) OR (array)$env_error !== $env_error) $_ENV['error'] = array();
	if (!isset($env_error[$type])) $_ENV['error'][$type] = array();
    switch ($type) {
    case 'form':
	$_ENV["trigger"][$trigger]=$args;
	$arg0 = $args[0];
        if ((string)$arg0 === $arg0) {
            $call = "wb".$trigger;
            if (is_callable($call)) $data = $call($data,$args);

            $call = wbTableName($arg0).$trigger;
            if (is_callable($call)) {
                $data = $call($data,$args);
            } else {
                $call = '_'.$call;
                if (is_callable($call)) {
                    $data = $call($data,$args);
                }
            }
        }
        if (isset($_SESSION['trigger'][$trigger])) {
            foreach ($_SESSION['trigger'][$trigger] as $module => $param) {
                $ecall = $module.'__'.$trigger;
                $acall = $module.'_'.$trigger;
                if (is_callable($acall)) {
                    $data['_furl'] = $acall($args, $data);
                }
                elseif (is_callable($ecall)) {
                    $data['_furl'] = $ecall($args, $data);
                }
            }
        }

        return $data;
        break;
    case 'func':
        $call = $name.'_'.$trigger;
        if (is_callable($call)) {
            $data = $call($data,$args);
        } else {
            wbError($type, $name, null);
        }

        return $data;
        break;
    default:
        break;
    }

    return $data;
}

function wbFurlPut($item, $string, $flag = 'update')
{
    $res = false;
    $table = $item['_table'];
    $id = $item['id'];
    $furl = wbFurlGenerate($string);
    if (!in_array('furl_index', $_ENV['tables'], true)) {
        wbTableCreate('furl_index');
    }
    $item = wbItemRead('furl_index', $table);
    if (!isset($item)) {
        $item = array('id' => $table, 'furl' => array());
    }
    switch ($flag) {
    case 'update':
        foreach ($item['furl'] as $f => $fid) {
            if ($id == $fid) {
                unset($item['furl'][$f]);
            }
        }
        $item['furl'][$furl] = $id;
        $res = $furl;
        break;
    case 'remove':
        foreach ($item['furl'] as $f => $fid) {
            if ($id == $fid) {
                unset($item['furl'][$f]);
            }
        }
        break;
    }
    $res = wbItemSave('furl_index', $item);
    if ($res) {
        $res = $furl;
    }
    unset($item,$table,$id,$furl);

    return $res;
}

function wbFurlGet($table, $furl)
{
    $res = false;
    if (!in_array('furl_index', $_ENV['tables'], true)) {
        wbTableCreate('furl_index');
    }
    $item = wbItemRead('furl_index', $table);
    if (isset($item['furl'][$furl])) {
        return $item['furl'][$furl];
    }

    return $res;
}

function wbFurlGenerate($str)
{
    $str = mb_strtolower(wbTranslit($str));
    $str = mb_ereg_replace('[^A-Za-z0-9 ]', ' ', $str);
    $str = str_replace(' ', '-', trim($str));
    $str = str_replace('--', '', trim($str));

    return $str;
}

function wbError($type, $name, $error = '__return__error__', $args = null)
{
    if (null == $error) {
        if (isset($_ENV['error'][$type][$name])) {
            unset($_ENV['error'][$type][$name]);
        }
        $_ENV["last_error"]=null;
    } else {

        if (is_array($args) AND isset( $_ENV['errors'][$error])) {
            foreach ($args as $key => $arg) {
                if (is_array($arg)) {
                    $arg = implode(',', $arg);
                }
                $_ENV['errors'][$error] = str_replace('{{'.$key.'}}', $arg, $_ENV['errors'][$error]);
            }
        }

        if ('__return__error__' == $error) {
            $error = $_ENV['error'][$type][$name];
        } else {
            if (isset($_ENV['errors'])) {
                $_ENV['error'][$type][$name] = array('errno' => $error, 'error' => $_ENV['errors'][$error]);
            } else {
                $_ENV['error'][$type][$name] = array('errno' => $error, 'error' => 'unknown error');
            }
        }
        $_ENV["last_error"]=$error;
    }
    return $error;
}

function wbErrorOut($error,$ret = false)
{
    if ($ret == false ) {
        echo $_ENV['errors'][$error];
    }
    else {
        return $_ENV['errors'][$error];
    }
}

function wbGetTpl($tpl = null, $path = false)
{
    $out = null;
    $cur = null;
    $locale = null;
    if (true == $path) {
        if (!$cur and is_file($_ENV['path_app']."/{$tpl}")) {
            $cur = wbNormalizePath($_ENV['path_app']."/{$tpl}");
        }
    } else {
        if (!$cur and is_file($_ENV['path_tpl']."/{$tpl}")) {
            $cur = wbNormalizePath($_ENV['path_tpl']."/{$tpl}");
        }
        if (!$cur and is_file($_ENV['path_engine']."/tpl/{$tpl}")) {
            $cur = wbNormalizePath($_ENV['path_engine']."/tpl/{$tpl}");
        }
    }
    if ($cur !== null ) {
        $out = wbFromFile($cur);
    }
    if (!$out) {
        if ($path !== false) {
            $cur = wbNormalizePath($path."/{$tpl}");
        } else {
            $cur = wbNormalizePath($_ENV['path_tpl']."/{$tpl}");
        }
        $cur=str_replace($_ENV["path_app"],"",$cur);
        wbErrorOut(wbError('func', __FUNCTION__, 1011, array($cur)));
    }

    $ini = substr($cur,0,-4).".ini";
    if (is_object($out) AND !is_file($ini) AND $out->find("[type='text/locale'][src]")->length) {
        $ini=$out->find("[type='text/locale'][src]")->attr("src");
        if (is_file($_ENV['path_app']."/{$ini}")) {
            $ini=$_ENV['path_app']."/{$ini}";
        } else {
            $path=implode("/",array_slice(explode("/",$cur),0,-1));
            $ini=$path."/".$out->find("[type='text/locale'][src]")->attr("src");
        }
    }
    if (is_object($out) AND is_file($ini)) {
        $locale=$out->wbSetFormLocale($ini);
    }
    if ($locale!==null) wbEnvData("tpl->{$tpl}->locale",$locale);
    return $out;
}

function wbLoopProtect($func,$args=array())
{
    if (!isset($_ENV['wbGetFormStack'])) {
        $_ENV['wbGetFormStack'] = array();
    }
    $_ENV['wbGetFormStack'][] = $func."_".md5(json_encode($args).$_ENV["lang"].$_SESSION["lang"]);
}

function wbLoopCheck($func,$args) {
    if (!isset($_ENV['wbGetFormStack'])) {
        $_ENV['wbGetFormStack'] = array();
    }
    if (in_array($func."_".md5(json_encode($args).$_ENV["lang"].$_SESSION["lang"]),$_ENV['wbGetFormStack'])) {
        return true;
    }
    else {
        return false;
    }
}

function wbOconv($value, $oconv)
{
    $oconv = htmlspecialchars_decode($oconv, ENT_QUOTES);
    $value = htmlspecialchars_decode($value, ENT_QUOTES);
    if (is_callable($oconv)) {
	$oconv = '$result = '.$oconv.'("'.$value.'");';
	eval($oconv);
	return $result;
    }
}

function wbGetForm($form = null, $mode = null, $engine = null)
{
    $_ENV['error'][__FUNCTION__] = '';
    if (null == $form) $form = $_ENV['route']['form'];
    if (null == $mode) $mode = $_ENV['route']['mode'];

    $aCall = $form.'_'.$mode;
    $eCall = $form.'__'.$mode;

    $loop=false;
    foreach(debug_backtrace() as $func) {
        if ($aCall==$func["function"]) {
            $loop=true;
        }
        if ($eCall==$func["function"]) {
            $loop=true;
        }
    }

    if (is_callable($aCall) and $loop == false) {
        $out = $aCall();
    }
    elseif (is_callable($eCall) and false !== $engine and $loop == false) {
        $out = $eCall();
    }

    if (!isset($out)) {
        $current = '';
        $ini = null;
        $flag = false;
        $path = array("/forms/{$form}_{$mode}.php", "/forms/{$form}/{$form}_{$mode}.php", "/forms/{$form}/{$mode}.php");
        foreach ($path as $form) {
            if (false == $flag) {
                if (is_file($_ENV['path_engine'].$form)) {
                    $current = $_ENV['path_engine'].$form;
                    $flag = $engine;
                }
                if (is_file($_ENV['path_app'].$form) && false == $flag) {
                    $current = $_ENV['path_app'].$form;
                    $flag = true;
                }
            }
        }
        unset($form);
        if ('' == $current) {
			$out=null;
            $current = "{$_ENV['path_engine']}/forms/common/common_{$mode}.php";
            if (is_file($current)) {
                $out = wbFromFile($current);
                $ini = substr($current,0,-4).".ini";
            }
            $current = "{$_ENV['path_app']}/forms/common/common_{$mode}.php";
            if (is_file($current)) {
                $out = wbFromFile($current);
                $ini = substr($current,0,-4).".ini";
            }
            if ($out==null) {
                $cur = wbNormalizePath("/forms/{$_ENV["route"]["form"]}_{$_ENV["route"]["mode"]}.php");
                $out = wbErrorOut(wbError('func', __FUNCTION__, 1012, array($cur)), true);
                $_ENV['error'][__FUNCTION__] = 'noform';
            }

        } else {
            $out = wbFromFile($current);
            $ini = substr($current,0,-4).".ini";
        }
    }
    if (is_string($out)) {
        $out = wbFromString($out);
    }
    if ($ini!==null AND !is_file($ini) AND $out->find("[type='text/locale'][src]")->length) {
        $src=$out->find("[type='text/locale'][src]")->attr("src");
        if (is_file($src)) {
            $ini=$src;
        }
        else {
            $ini=explode("/",$ini);
            $ini[count($ini)-1]=$src;
            if (is_file(implode("/",$ini))) {
                $ini=implode("/",$ini);
            }
            else {
                $ini[count($ini)-1]=$src.".ini";
                if (is_file(implode("/",$ini))) {
                    $ini=implode("/",$ini);
                }
                else {
                    $ini=null;
                }
            }
        }
    }
    $locale=$out->wbSetFormLocale($ini);
    if ($locale!==null) wbEnvData("form->{$aCall}->locale",$locale);
    return $out;
}

function wbEnvData($index,$value="__wb__null__data__") {
    $loop=explode("->",$index);
    $index='$_ENV["data"]';
    $count=count($loop);
    $i=0;
    $res = false;
    foreach($loop as $key) {
        $i++;
        if ($key=="") {
            $key="undefined";
        }
        else {
            $key=preg_replace('/[^ a-zа-яё\d]/ui', '_',$key );
        }
        $index.="->".$key;
        if (!eval('return isset( '.$index.' );')) {
            eval($index.' = new stdClass();');
        }
        if ($i==$count) {
            if ($value=="__wb__null__data__") {
                $res = eval('return '.$index.';');
            } else {

                $res = eval('return '.$index.' = $value;');
            }
        }
    }
    return $res;
}


function wbErrorList()
{
    $_ENV['errors'] = array(
                          100 => 'Login succeessful {{l}}',
                          101 => 'Login incorrect {{l}}',
                          404 => 'Page not found',
                          1001 => 'Table {{0}} not exists',
                          1002 => 'Table {{0}} already exixts',
                          1003 => 'Do not remove {{0}}',
                          1004 => 'Failed to remove file {{0}}',
                          1005 => 'Failed to remove table {{0}}',
                          1006 => 'Item {{1}} in table {{0}} not exists',
                          1007 => 'Failed to save record to table {{0}}',
                          1008 => 'Delete item {{1}} in table {{0}}',
                          1009 => 'Flush data from cache table {{0}}',
                          1010 => 'Failed to create table {{0}}',
                          1010 => 'Create a table {{0}}',
                          1011 => 'Template not found {{0}}',
                          1012 => 'Form not found {{0}}',
                          1013 => 'PHP code not valid'
                      );
}

function wbLog($type, $name, $error, $args)
{
    if (isset($_ENV['errors'][$error])) {
        $error = array('errno' => $error, 'error' => $_ENV['errors'][$error]);
    } else {
        $error = wbError($type, $name);
    }
    if (is_array($args)) {
        foreach ($args as $key => $arg) {
            if (is_array($arg)) {
                $arg = implode(',', $arg);
            }
            $error['error'] = str_replace('{{'.$key.'}}', $arg, $error['error']);
        }
    }
    if (isset($_ENV["settings"]["log"]) AND $_ENV["settings"]["log"]=="on")  {
        error_log("{$type} {$name} [{$error['errno']}]: {$error['error']} [{$_SERVER['REQUEST_URI']}]");
    }
}

function wbNewId($separator = '', $prefix = '')
{
    $mt = explode(' ', microtime());
    $md = substr(str_repeat('0', 2).dechex(ceil($mt[0] * 10000)), -4);
    $id = dechex(time() + rand(100, 999));
    if ($prefix > '') {
        $id = $prefix.$separator.$id.$md;
    } else {
        $id = $id.$separator.$md;
    }
    $_ENV['newIdLast']=$_SESSION['newIdLast'] = $id;

    return $id;
}

function wbGetItemImg($Item = null, $idx = 0, $noimg = '', $imgfld = 'images', $visible = true)
{
    $res = false;
    $count = 0;
    if (null == $Item) {
        $Item = $_ENV['ITEM'];
    }
    if (!is_file("{$_ENV['path_app']}/{$noimg}")) {
        if (is_file("{$_ENV['path_engine']}/uploads/__system/{$noimg}")) {
            $noimg = "/engine/uploads/__system/{$noimg}";
        } else {
            $noimg = '/engine/uploads/__system/image.jpg';
        }
    }
    $image = $noimg;

    if (isset($Item[$imgfld])) {
        if (!is_array($Item[$imgfld])) {
            $Item[$imgfld] = json_decode($Item[$imgfld], true);
        }
        if (!is_array($Item[$imgfld])) {
            $Item[$imgfld] = array();
        }
        foreach ($Item[$imgfld] as $key => $img) {
            if (!isset($img['visible'])) {
                $img['visible'] = 1;
            }

            if (false == $res and ((true == $visible and 1 == $img['visible']) or false == $visible) and is_file("{$_ENV['path_app']}/uploads/{$Item['_table']}/{$Item['id']}/{$img['img']}")) {
                if ($idx == $count) {
                    $image = "{$_ENV['path_app']}/uploads/{$Item['_table']}/{$Item['id']}/{$img['img']}";
                    $res = true;
                }
                ++$count;
            }
        }
        unset($img);
    }

    return urldecode($image);
}

function wbImagesToText($Item, $fld = 'text', $imgs = 'images')
{
    if (isset($Item[$imgs])) {
        $image = wbGetItemImg($Item,0,0,$imgs);
        $image = substr($image, strlen($_ENV['path_app']));
        $Item['_image'] = $image;
        if (!isset($Item[$fld])) {
            $Item[$fld]="";
        }
        if (isset($Item['intext_position']) and $Item['intext_position']['pos'] > '') {
            if ('' == $Item['intext_position']['width']) {
                $width = $_ENV['intext_width'];
            } else {
                $width = $Item['intext_position']['width'];
            }
            if ('' == $Item['intext_position']['height']) {
                $height = $_ENV['intext_height'];
            } else {
                $height = $Item['intext_position']['height'];
            }
            $img = "
                   <a href='{$image}' data-fancybox='gallery' class='wb-intext'>
                                                        <img data-wb-role='thumbnail' data-wb-size='{$width};{$height};src' src='{$image}' style='float:{$Item['intext_position']['pos']};' data-wb-hide='wb'>
                                                                </a>
                                                                ";
                                                                $Item[$fld] = $img.$Item[$fld];
        }
        if (isset($Item['images_position']) and isset($Item['images_position']['pos']) and $Item['images_position']['pos'] > '') {
            $gal = wbGetForm('common', 'gallery');
            $gal->wbSetData($Item);
            if ($image > '' and $Item['intext_position']['pos'] > '') {
                if ($gal->find("a[href='{$image}'][idx]")->length) {
                    $gal->find("a[href='{$image}']")->remove();
                } else {
                    $gal->find("a[href='{$image}']")->parents('[idx]')->remove();
                }
            }
            if (!$gal->find('a')->length) {
                $gal->find('.wb-gallery')->remove();
            }
            if ('top' == $Item['images_position']['pos']) {
                $Item[$fld] = $gal->outerHtml().$Item[$fld];
            }
            elseif ('bottom' == $Item['images_position']['pos']) {
                $Item[$fld] = $Item[$fld].$gal->outerHtml();
            }
            unset($gal);
        }

    }

    return $Item;
}



function wbPagination($c, $m)
{
    $current = $c;
    $last = $m;
    $delta = 4;
    $left = $current - $delta;
    $right = $current + $delta + 1;
    $range = array();
    $rangeWithDots = array();
    $l = -1;

    for ($i = 1; $i <= $last; $i++)
    {
        if ($i == 1 || $i == $last || $i >= $left && $i < $right)
        {
            array_push($range, $i);
        }
    }

    for($i = 0; $i<count($range); $i++)
    {
        if ($l != -1)
        {
            if ($range[$i] - $l === 2)
            {
                array_push($rangeWithDots, $l + 1);
            }
            else if ($range[$i] - $l !== 1)
            {
                array_push($rangeWithDots, '...');
            }
        }

        array_push($rangeWithDots, $range[$i]);
        $l = $range[$i];
    }

    return $rangeWithDots;
}

function wbListFiles($dir)
{
    $list = array();
    if (is_dir($dir) and $dircont = scandir($dir)) {
        $i = 0;
        $idx = 0;
        while (isset($dircont[$i])) {
            if ('.' !== $dircont[$i] && '..' !== $dircont[$i]) {
                $current_file = "{$dir}/{$dircont[$i]}";
                if (is_file($current_file)) {
                    $list[] = "{$dircont[$i]}";
                }
            }
            ++$i;
        }
    }

    return $list;
}

function wbFileRemove($file)
{
    $res = false;
    if (is_file($file) and wbRole('admin')) {
        unlink($file);
        if (is_file($file)) {
            $res = false;
        } else {
            $res = true;
        }
    }

    return $res;
}

function wbPutContents($dir, $contents)
{
    $parts = explode('/', $dir);
    $file = array_pop($parts);
    $dir = '';
    $u=umask();
    foreach ($parts as $part) {
        if (!is_dir($dir .= "/$part")) {
            mkdir($dir);
        }
    }
    umask($u);

    return file_put_contents("$dir/$file", $contents);
}

function wbRecurseDelete($src)
{
    $dir = opendir($src);
    if (is_resource($dir)) {
        while (false !== ($file = readdir($dir))) {
            if (('.' !== $file) && ('..' !== $file)) {
                if (is_dir($src.'/'.$file)) {
                    wbRecurseDelete($src.'/'.$file);
                } else {
                    unlink($src.'/'.$file);
                }
            }
        }
        closedir($dir);
        if (is_dir($src)) {
            rmdir($src);
        }
    }
}

function wbRecurseCopy($src, $dst)
{
	$mask=umask();
    if (is_file($src)) {
        copy($src, $dst);
    } else {
        $dir = opendir($src);
        if (is_resource($dir)) {
            if (!is_dir($dst)) mkdir($dst);
            while (false !== ($file = readdir($dir))) {
                if (('.' !== $file) && ('..' !== $file)) {
                    if (is_dir($src.'/'.$file)) {
                        wbRecurseCopy($src.'/'.$file, $dst.'/'.$file);
                        @chmod($dst.'/'.$file, 0777);
                    } else {
                        copy($src.'/'.$file, $dst.'/'.$file);
                        @chmod($dst.'/'.$file, 0766);
                    }
                }
            }
            closedir($dir);
        }
    }
    umask($mask);
}

function wbQuery($sql)
{
    require_once $_ENV['path_engine'].'/lib/sql/PHPSQLParser.php';

    $parser = new PHPSQLParser();
    $p = $parser->parse($sql);
    $sid = md5($sql.$_ENV["lang"].$_SESSION["lang"]);

    foreach ($p as $r => $a) {
        foreach ($a as $e) {
            wbt_route($sid, $e, $r);
        }
    }

    $table = array();
    $join = array();
    foreach ($_ENV['sql'][$sid]['FROM'] as $key => $t) {
        if (!isset($t['join'])) {
            // join пока не работает
            $table['name'] = $key;
            $table['table'] = $t['name'];
            $table['data'] = wbItemList(wbTable($t['name']));
        } else {
            $join[$key]['name'] = $key;
            $join[$key]['data'] = wbItemList(wbTable($t['name']));
            $join[$key]['join'] = $t['join'];
        }
    }
    if (isset($_ENV['sql'][$sid]['WHERE'])) {
        $where = wbt_where($table['name'], $_ENV['sql'][$sid]['WHERE']);
    }

    $object = new ArrayObject($table['data']);
    foreach ($object as $key => $item) {
        $call = $table['table'].'AfterReadItem';
        if (is_callable($call)) {
            $item = $call($item);
        }
        $object[$key] = $item;
    }
    $iterator = new wbt_filter($object->getIterator(), 'where', array('where' => $where, 'table' => $table['name'], 'join' => $join));
    $table['data'] = iterator_to_array($iterator);
    $iterator->rewind();

    unset($_ENV['sql'][$sid]);

    return $table['data'];
}

function wbWhereItem($item, $where = null)
{
	if (null == $where) return true;
    $where = htmlspecialchars_decode($where);
    $res = true;
    if (strpos($where,"}}")) $where = wbSetValuesStr($where, $item);

        if ('%' == $where[0]) {
		$phpif = substr($where, 1);
        } else {
		$phpif = wbWherePhp($where, $item);

        }
        if ($phpif > '') {
		//echo $where."<br>";
		//echo $phpif."<br>";
            eval('return $res = ( '.$phpif.' );');
        }

    return $res;
}

function wbWherePhp($str = '', $item = array())
{
	if (strpos($str,"}}")) {
		$str = wbSetValuesStr($str, $item);
		//$str=preg_replace("~\{\{([^(}})]*)\}\}~","",$str);
		$str=preg_replace("~\{\{(.*)\}\}~","",$str);
	}
	$cache=md5($str);
	if (!isset($_ENV["cache"][__FUNCTION__])) $_ENV["cache"][__FUNCTION__]=array();
	if (isset($_ENV["cache"][__FUNCTION__][$cache])) {
		return $_ENV["cache"][__FUNCTION__][$cache];
	}
	$exclude = array(
		'AND'	=> 0,
		'OR'	=> 0,
		'ARRAY'	=> 0,
		'LIKE'		=>array("func2"=>"wbWhereLike"),
		'IN_ARRAY'	=>array("func1"=>"in_array"),
		'IN'		=>array("func"=>"in_array"),
		'NOT_LIKE'	=>array("func2"=>"!wbWhereNotLike"),
		'NOT_IN_ARRAY'	=>array("arr"=>"!in_array"),
		'NOT_IN'	=>array("arr"=>"!in_array"),
	);
	$cond=array('<','>','=','==','>=','<=','!=','!==','#',"(",")","<>",",");
	$re = '/"(?:[^"\\\\]|\\\\.)*"|\'(?:[^"\\\\]|\\\\.)*\'|\{\{([^(}})]*)\}\}|\w+(?!\")\b|\[(.*?)\]|[=!#<>\,]+|[\(\)]/ium';
	preg_match_all($re, $str, $arr, PREG_SET_ORDER);
	$str=""; $len=0; $flag=0;
	foreach($arr as $index => $fld) {
		$fld=$fld[0];
		$sup=strtoupper($fld);
		$exc=isset($exclude[$sup]);
		$con=in_array($fld,$cond);
		if ($flag==1) $flag=2;
		if ( (isset($item[$fld]) AND ( (array)$item[$fld] === $item[$fld] )) OR isset($tmpfld)) {
			if (isset($arr[$index+1]) AND substr($arr[$index+1][0],0,1) == "[") {
				if (!isset($tmpfld)) {
					$tmpfld = str_replace("{$fld}", ' $item["'.$fld.'"]', $fld);
					$flag=3;
				} else {
					$tmpfld.= $fld;
				}
			}
		}

		if ($flag!==3) {
				if ((substr($fld,0,1)!=='"' OR substr($fld,0,1)!=="'") AND !$exc AND !$con) {
			    if (isset($item[$fld])) {
				if (is_array($item[$fld])) {
				    $fld="'".wbJsonEncode($item[$fld])."'";
				    //$fld='wbJsonEncode($item["'.$fld.'"])';
				    if ($fld=="null") {
					$fld=' "[]" ';
				    } else {
					$fld=htmlentities($fld);
				    }
				} else {
				    $fld = str_replace("{$fld}", ' $item["'.$fld.'"] ', $fld);
				}
			    } else if ((substr($fld,0,1)=='"' OR substr($fld,0,1)=="'") OR $fld=="''" OR $fld=='""') {
				// строки в кавычках
			    } else if ($fld > "" AND $fld!=="''" AND $fld!=='""'){
				if (!is_numeric($fld) AND is_string($fld)) {
					$fld = str_replace("{$fld}", ' $item["'.$fld.'"] ', $fld);
				}
			    }
			} else if ($exc AND $flag==0) {
				$prev=substr($str,-$len);
				if (isset($exclude[$sup]) AND isset($exclude[$sup]["func1"])) {
					$str=substr($str,0,-$len);
					if ($str>"") $str.="(";
					$str.=$exclude[$sup]["func1"];
					$fld="";
				} else if (isset($exclude[$sup]) AND isset($exclude[$sup]["func2"])) {
					$str=substr($str,0,-$len);
					$str.=$exclude[$sup]["func2"]."(".$prev;
					$flag = 1;
					$fld="";
				} else if (isset($exclude[$sup]) AND isset($exclude[$sup]["arr"])) {
					$str=substr($str,0,-$len);
					$str.=$exclude[$sup]["arr"]."(".$prev.", array";
					$flag = 4;
					$fld="";

				} else if (isset($exclude[$sup]) AND isset($exclude[$sup]["func1"])) {
					$fld=$exclude[$sup]["func1"];
				}
			} else if ($exc AND $flag==4) {
				$str.=")";
				$flag=0;
			} else if ($con) {
				$fld = strtr($fld, array(
					'>' => '>',
					'<' => ' < ',
					'>=' => ' >= ',
					'<=' => ' <= ',
					'<>' => ' !== ',
					'!=' => ' !== ',
					'!==' => ' !== ',
					'#' => ' !== ',
					'==' => ' == ',
					'=' => ' == ',
				));
				if ($str=="") $str='""';
			}
		}
		if ($flag==3 AND (!isset($arr[$index+1]) OR substr($arr[$index+1][0],0,1) !== "[")) {
			$fld=wbSetQuotes($tmpfld.$fld);
			eval('$arr=is_array('.$fld.');');
			if ($arr) eval('$fld=wbJsonEncode('.$fld.');');
			$str.=" ".$fld;
			$flag=0;
		} else if ($flag==2) {
			$str.=", ".$fld." ) ";
			$flag=0;
		} else if ($flag!==2 AND $flag!==3)  {
			$len=strlen($fld);
			$str.=" ".$fld;
		}
	}
	if ($flag==4) {$str.=")";}
    //$str=preg_replace("~\{\{([^(}})]*)\}\}~","",$str);
    $_ENV["cache"][__FUNCTION__][$cache]=$str;
    return $str;
}

function wbt_where($table, $where)
{
    $where = htmlspecialchars_decode($where);
    foreach ($where as $key => $val) {
        if ('$' == mb_substr($val, 0, 1)) {
            $tmp = explode('.', $val);
            if (2 == count($tmp)) {
                $val = '$'.mb_substr($tmp[0], 1).'["'.$tmp[1].'"]';
            } else {
                $val = '$'.$table.'["'.mb_substr($tmp[0], 1).'"]';
            }
            $where[$key] = $val;
        }
    }
    $res = implode(' ', $where);

    return $res;
}


function wbt_route($sid, $e, $r = 'TABLE')
{
    if (!isset($_ENV['sql'][$sid][$r])) {
        $_ENV['sql'][$sid][$r] = array();
    }
    $t = &$_ENV['sql'][$sid][$r];
    if (isset($_ENV['sql'][$sid]['JOIN'])) {
        $t = &$_ENV['sql'][$sid]['FROM'][$_ENV['sql'][$sid]['JOIN']]['join'];
    }
    if (isset($_ENV['sql'][$sid]['EXPR'])) {
        $t = &$_ENV['sql'][$sid]['EXPR'];
    }
    switch ($e['expr_type']) {
    case 'table':
        if (is_array($e['alias'])) {
            $key = $e['alias']['no_quotes'];
        } else {
            $key = $e['no_quotes'];
        }
        $t[$key]['name'] = $e['no_quotes'];
        if (isset($e['join_type']) and isset($e['ref_clause']) and is_array($e['ref_clause']) and 'JOIN' == $e['join_type']) {
            $_ENV['sql'][$sid]['JOIN'] = $key;
            foreach ($e['ref_clause'] as $s) {
                wbt_route($sid, $s, 'WHERE');
            }
            unset($_ENV['sql'][$sid]['JOIN']);
        }
        break;
    case 'expression':
        $_ENV['sql'][$sid]['EXPR'] = array();
        if (isset($e['sub_tree'])) {
            foreach ($e['sub_tree'] as $s) {
                wbt_route($sid, $s, 'WHERE');
            }
        }
        if (isset($e['alias']) and $e['alias']['no_quotes'] > '') {
            $t[$e['alias']['no_quotes']] = implode(' ', $_ENV['sql'][$sid]['EXPR']);
        } else {
            $t[] = implode(' ', $_ENV['sql'][$sid]['EXPR']);
        }
        unset($_ENV['sql'][$sid]['EXPR']);
        break;
    case 'colref':
        if (in_array($r, array('WHERE', 'TABLE', 'SELECT'), true)) {
            $t[] = '$'.$e['no_quotes'];
        }
        if (is_array($e['sub_tree'])) {
            foreach ($e['sub_tree'] as $s) {
                wbt_route($sid, $s, $r);
            }
        }
        break;
    case 'operator':
        $op = strtr(mb_strtoupper($e['base_expr']), array(
                        '<=' => '<=',
                        '>=' => '>=',
                        '=' => '==',
                        '<>' => '!==',
                        'NOT' => '!==',
                    ));
        $t[] = $op;
        break;
    case 'const':
        if (in_array($r, array('WHERE', 'TABLE', 'SELECT'), true)) {
            $t[] = $e['base_expr'];
        }
        break;
    case 'bracket_expression':
        if (in_array($r, array('WHERE', 'TABLE', 'SELECT'), true)) {
            $t[] = '(';
            if (isset($e['sub_tree'])) {
                foreach ($e['sub_tree'] as $s) {
                    wbt_route($sid, $s, $r);
                }
            }
            $t[] = ')';
        }
        break;
    }
}

class wbt_filter extends FilterIterator
{
    private $userFilter;
    private $variable;
    private $join;
    private $table;
    private $type;
    private $data;

    public function __construct(Iterator $iterator, $type, $data)
    {
        parent::__construct($iterator);
        //$tname, $filter, $join=null
        $this->type = $type;
        switch ($type) {
        case 'where':
            $this->userFilter = $data['where'];
            $this->table = $data['table'];
            $this->join = $data['join'];
            break;
        }
    }

    public function accept()
    {
        $item = $this->getInnerIterator()->current();
        switch ($this->type) {
        case 'where':
            eval('$'.$this->table.' = $item;');
            break;
        }
        eval('if ( '.$this->userFilter.' ) { $res=1; } else { $res=0; } ;');
        if (0 == $res) {
            return false;
        }

        return true;
    }
}



function wbRouterAdd($route = null, $destination = null)
{
    if (null == $route) { // Роутинг по-умолчанию
        $route = wbRouterRead();
    }
    wbRouter::addRoute($route, $destination);
}

function wbRouterRead($file = null)
{
    if (null == $file) {
        $file = $_ENV['path_engine'].'/router.ini';
        $route = wbRouterRead($file);
        if (is_file($_ENV['path_app'].'/router.ini')) {
            $route = array_merge(wbRouterRead($_ENV['path_app'].'/router.ini'), $route);
        }
    } else {
        if (is_file($file)) {
            $route = array();
            $router = new ArrayIterator(file($file));
            foreach ($router as $key => $r) {
                $r = explode('=>', $r);
                if (2 == count($r)) {
                    $route[trim($r[0])] = trim($r[1]);
                }
            }
        }
    }

    return $route;
}


function wbAuthGetContents($url,$get=null,$username=null,$password=null) {
	if (func_num_args()==3) {
		$password=$username;
		$username=$get;
		$get=array();
	}
	if (!is_array($get)) $get=(array)$get;
    $cred = sprintf('Authorization: Basic %s', base64_encode("$username:$password") );
    $opts = array(
                'http'=>array(
                    'method'=>'GET',
                    'header'=>$cred,
                    'content'=>$get
                )
            );
    $context = stream_context_create($opts);
    $handle = @fopen($url, 'r', false, $context);

    if (!$handle) {
        print_r ($http_response_header[0]);
        return false;
    }

    return stream_get_contents($handle);
}

function wbAuthPostContents($url, $post=null, $username=null,$password=null) {
	if (func_num_args()==3) {
		$password=$username;
		$username=$get;
		$post=array();
	}
	if (!is_array($post)) $post=(array)$post;

    $cred = sprintf('Authorization: Basic %s', base64_encode("$username:$password") );
    $post=http_build_query($post);
    $opts = array(
                'http'=>array(
                    'method'=>'POST',
                    'header'=>$cred,
                    'content'=>$post
                )
            );
    $context = stream_context_create($opts);
    $result = file_get_contents($url, false, $context);
    return $result;
}

function wbCheckPassword($str,$pass) {
	$res=false;
	if (wbMakePassword($str) == $pass) $res=true;
	return $res;
}

function wbMakePassword($str) {
	return md5($str);
}

function wbRouterGet($requestedUrl = null)
{
    return wbRouter::getRoute($requestedUrl);
}

function wbLoadController()
{
    if (isset($_ENV['route']['controller'])) {
        $path = '/controllers/'.$_ENV['route']['controller'].'.php';
        if (is_file($_ENV['path_engine'].$path)) {
            include_once $_ENV['path_engine'].$path;
        }
        if (is_file($_ENV['path_app'].$path)) {
            include_once $_ENV['path_app'].$path;
        }
        $ecall = $_ENV['route']['controller'].'__controller';
        $acall = $_ENV['route']['controller'].'_controller';
        if (is_callable($acall)) {
            return $acall(array($_ENV['DOM'], $_ENV['ITEM']));
        }
        if (is_callable($ecall)) {
            return $ecall(array($_ENV['DOM'], $_ENV['ITEM']));
        }
        echo "{$_ENV['err_ctrl_load']}: {$_ENV['route']['controller']}";
        die;
    }
}

function wbFromString($str = '')
{
    return ki::fromString($str);
}

function wbFromFile($str = '')
{
    return ki::fromFile($str);
}

function wbAttrToArray($attr)
{
    return wbArrayAttr($attr);
}

function wbAttrAddData($data, $Item, $mode = false)
{
    $data = stripcslashes(html_entity_decode($data));
    $data = json_decode($data, true);
    if (!is_array($Item)) {
        $Item = array($Item);
    }
    if (false == $mode) {
        $Item = array_merge($data, $Item);
    }
    if (true == $mode) {
        $Item = array_merge($Item, $data);
    }

    return $Item;
}

function wbGetWords($str, $w = 100)
{
    $res = '';
    $arr = explode(' ', trim($str));
    for ($i = 0; $i <= $w; ++$i) {
        if (isset($arr[$i])) {
            $res = $res.' '.$arr[$i];
        }
    }
    if (count($arr) > $w) {
        $res = $res.'...';
    }
    $res = trim($res);

    return $res;
}

include("wb_set_values_str.php");

function wbsvSetValue($Item,$sub) {
    $text="";
    if (eval('return is_array('.$sub.');')) {
        $text .= eval('return json_encode('.$sub.');');
    } else {
        $temp = '';
        eval('$temp .= '.$sub.';');
        $temp = strtr($temp, array('{{' => '#~#~', '}}' => '~#~#'));
        $text .= $temp;
    }
    return $text;
}


function wbsvRestoreValue($Item,$sub) {
    $arr=explode("~#~#|~#~#",str_replace("][","]~#~#|~#~#[",$sub));
    $index="";
    $result=true;
    foreach($arr as $a) {
        $index.=$a;
        if ($result==true AND eval('return isset('.$index.');')) {
            if (eval('return is_array('.$index.');')) {

            } else {

                $value=eval('return wbItemToArray('.$index.');');
                eval($index.' = $value ;');
            }
        }
    }
    return $Item;
}


// добавление кавычек к нечисловым индексам
function wbSetQuotes($In)
{
    $err = false;
    $mask = '`\[(%*[\w\d]+)\]`u';
    $nBrackets = preg_match_all($mask, $In, $res, PREG_OFFSET_CAPTURE);				// найти индексы без кавычек
    if ($nBrackets === false)
    {
        echo 'Ошибка в шаблоне индексов. Обратитесь к разработчику.' . '<br>';
        $err = true;
    } else
    {
        if ($nBrackets == 0)
        {
            if (substr($In, 0, 2) != '["')
            {
                if (!is_numeric($In)) $In = '"' . $In . '"';
                $In = '[' . $In . ']';
            }
        } else
        {
            for ($i = 0; $i < $nBrackets; $i++)
            {
                if (!is_numeric($res[1][$i][0])) $In = str_replace('['.$res[1][$i][0].']', '["'.$res[1][$i][0].'"]', $In);
            }
        }
    }
    return $In;
}

// заменяем &quot на "
function wbChangeQuot($Tag)
{
    $mask = '`&quot[^;]`u';
    $nQuot = preg_match_all($mask, $Tag, $res, PREG_OFFSET_CAPTURE);				// найти &quot без последеующего ;
    if ($nQuot === false)
    {
        echo 'Ошибка в шаблоне &quot. Обратитесь к разработчику.' . '<br>';
        $err = true;
        $In = $tag;
    } else
    {
        if ($nQuot == 0)
        {
            $In = $Tag;
        } else
        {
            $In = '';
            $startIn = 0;		// начальная позиция текста за предыдущей заменой
            for ($i = 0; $i < $nQuot; $i++)
            {
                $beforSize = $res[0][$i][1] - $startIn;
                $In .= substr($Tag, $startIn, $beforSize) . '"';		// исходный текст между предыдущей и текущей &quot
                $startIn += $beforSize + 5;
                if ($i+1 == $nQuot)		// это была последняя &quot
                {
                    $In .= substr($Tag,  $startIn, strlen($Tag) - $startIn);
                }
            }
        }
    }
    return $In;
}

function wbRole($role, $userId = null)
{
    $res = false;
    if (!is_array($role)) {
        $role = wbAttrToArray($role);
    }
    if (null == $userId) {
        $res = in_array($_SESSION['user_role'], $role, true);
    } else {
        $user = wbReadItem('users', $userId);
        $res = in_array($user['role'], $role, true);
    }

    return $res;
}

function wbControls($set = '')
{
    $res = '*';
    $controls = '[data-wb-role]';
    $allow = '[data-wb-allow], [data-wb-disallow], [data-wb-disabled], [data-wb-enabled], [data-wb-readonly], [data-wb-writable]';
    $target = '[data-wb-prepend], [data-wb-append], [data-wb-remove], [data-wb-before], [data-wb-after], [data-wb-html], [data-wb-replace], [data-wb-selector], [data-wb-addclass], [data-wb-removeclass], [data-wb-prependto], [data-wb-appendto], [data-wb-htmlto], [data-wb-removeattr], [data-wb-attr], [data-wb-src], [data-wb-clear]';
    $tags = array('dict', 'tree', 'gallery', 'imageloader', 'thumbnail', 'uploader','multiinput', 'where');
    foreach(array_keys($_ENV["tags"]) as $tag) {
        if (!in_array($tag,$tags)) $tags[]=$tag;
    }

    if ('' !== $set) {
        $res = $$set;
    } else {
        $res = "{$controls},{$allow},{$target}";
    }
    unset($controls,$allow,$target);

    return $res;
}

function wbListForms($exclude = true)
{
    if (true == $exclude) {
        $exclude = array('forms/common', 'forms/admin', 'forms/source', 'forms/snippets');
    }
    elseif (!is_array($exclude)) {
        $exclude = array('forms/snippets');
    }
    $list = array();
    $eList = wbListFilesRecursive($_ENV['path_engine'].'/forms', true);
    $aList = wbListFilesRecursive($_ENV['path_app'].'/forms', true);
    $arr = $eList;
    foreach ($aList as $a) {
        $arr[] = $a;
    }
    unset($eList,$aList);
    foreach ($arr as $i => $data) {
        $name = $data['file'];
        $path = $data['path'];
        $path = str_replace(array($_ENV['path_engine'], $_ENV['path_app']), array('.', '.'), $path);
        $inc = strpos($name, '.inc');
        $ext = explode('.', $name);
        $ext = $ext[count($ext) - 1];
        $name = substr($name, 0, -(strlen($ext) + 1));
        $name = explode('_', $name);
        $name = $name[0];
        foreach ($exclude as $exc) {
            if (!strpos($path, $exc)) {
                $flag = true;
            } else {
                $flag = false;
            }
        }
        if (('php' == $ext or 'htm' == $ext) && !$inc && true == $flag && $name > '' && !in_array($name, $list, true)) {
            $list[] = $name;
        }
    }
    unset($arr);
    //$merchE=wbCheckoutForms(true);
    //$merchA=wbCheckoutForms();
    //foreach($merchE as $m) {if (in_array($m["name"],$list)) {unset($list[array_search($m["name"],$list)]);}}
    //foreach($merchA as $m) {if (in_array($m["name"],$list)) {unset($list[array_search($m["name"],$list)]);}}
    if (in_array('form', $list, true)) {
        unset($list[array_search('form', $list, true)]);
    }

    return $list;
}

function wbListModules()
{
    $arr = [];
    $p=[$_ENV['path_engine'].'/modules',$_ENV['path_app'].'/modules'];
    foreach($p as $d) {
        if (is_dir($d)) $list = scandir($d);
        foreach ($list as $e) {
            if (!in_array($e,[".",".."]) AND !in_array($e,$arr) AND is_dir($_ENV['path_engine'].'/modules/'.$e) AND is_file($_ENV['path_engine'].'/modules/'.$e.'/'.$e.".php")) $arr[] = $e;
        }
    }
    return $arr;
}

function wbListTags()
{
    $list = array();
    $eList = wbListFilesRecursive($_ENV['path_engine'].'/tags', true);
    $aList = wbListFilesRecursive($_ENV['path_app'].'/tags', true);
    $arr = $eList;
    foreach ($aList as $a) {
        $arr[] = $a;
    }
    unset($eList,$aList);
    foreach ($arr as $i => $data) {
        $name = $data['file'];
        $path = $filepath = $data['path'];
        $path = str_replace(array($_ENV['path_engine'], $_ENV['path_app']), array('.', '.'), $path);
        $inc = strpos($name, '.inc');
        $ext = explode('.', $name);
        $ext = $ext[count($ext) - 1];
        $name = substr($name, 0, -(strlen($ext) + 1));
        $name = explode('_', $name);
        $name = $name[0];
        if (('php' == $ext) && !$inc && $name > '' && !in_array($name, $list, true)) {
            $list[strtolower($name)] = $filepath."/{$name}.{$ext}";
        }
    }
    unset($arr);
    return $list;
}

function wbListLocales() {
    $out=wbGetForm("admin","edit");
    $locales=wbEnvData("form->admin_edit->locale");
    foreach($locales as $key => $loc) {
        if (!isset($loc["_locale"])) {
            $loc["_locale"]=$key;
        }
        if (!isset($loc["_flag"])) {
            $loc["_flag"]="";
        }
        $locales[$key]=array("id"=>$key,"_locale"=>$loc["_locale"],"_flag"=>$loc["_flag"]);
    }
    return $locales;
}

function wbListFormsFull()
{
    $list = array();
    $types = array('engine', 'app');
    foreach ($types as $type) {
        $list[$type] = array();
        $fList = wbListFilesRecursive($_ENV['path_'.$type].'/forms');
        foreach ($fList as $fname) {
            $inc = strpos($fname, '.inc');
            $ext = explode('.', $fname);
            $ext = $ext[count($ext) - 1];
            $name = substr($fname, 0, -(strlen($ext) + 1));
            $tmp = explode('_', $name);
            $form = $tmp[0];
            unset($tmp[0]);
            $mode = implode('_', $tmp);
            //$uri_path=str_replace($_SESSION["root_path"],"",$_ENV["path_".$type]);
            $uri_path = '';
            $data = array(
                        'type' => $type,
                        'path' => $_ENV['path_'.$type]."/forms/{$form}/".$name.".{$ext}",
                        'dir' => "/forms/{$form}",
                        'uri' => $uri_path."/forms/{$form}/".$fname,
                        'form' => $form,
                        'file' => $fname,
                        'ext' => $ext,
                        'name' => $name,
                        'mode' => $mode,
                    );
            $list[$type][] = $data;
        }
    }

    return $list;
}

function wbArraySortMulti($array = array(), $args = array('votes' => 'd'))
{
    return wbArraySort($array, $args);
}
function wbArraySort($array = array(), $args = array('votes' => 'd'))
{
    // если передан атрибут, то предварительно готовим массив параметров
    if (is_string($args) && $args > '') {
        $args = wbArrayAttr($args);
        $param = array();
        foreach ($args as $ds) {
            $tmp = explode(':', $ds);
            if (!isset($tmp[1])) {
                $tmp[1] = 'a';
            }
            $param[$tmp[0]] = $tmp[1];
        }
        $args = $param;
        unset($param,$tmp,$ds);
    }
    // сортировка массива по нескольким полям
    uasort($array, function ($a, $b) use ($args) {
        $res = 0;
        $a = (object) $a;
        $b = (object) $b;
        foreach ($args as $k => $v) {
            if (isset($a->$k)) $a->$k=mb_strtolower($a->$k);
            if (isset($b->$k)) $b->$k=mb_strtolower($b->$k);
            if (isset($a->$k) && isset($b->$k)) {
                if ($a->$k == $b->$k) {
                    continue;
                }
                $res = ($a->$k < $b->$k) ? -1 : 1;
                if ('d' == $v) {
                    $res = -$res;
                }
                break;
            }
        }

        return $res;
    });

    return $array;
}

function wbArrayAttr($attr)
{
    $attr = trim($attr);
    $attr = str_replace(',', ' ', $attr);
    $attr = str_replace(';', ' ', $attr);

    return explode(' ', trim($attr));
}

function wbNormalizePath($path)
{
	return realpath($path);
}

function wbClearValues($out,$rep='')
{
    $out = preg_replace('/\{\{([^\}]+?)\}\}+|<script.*text\/template.*?>.*?<\/script>(*SKIP)(*F)/isumx', $rep, $out); return $out; } function wbListTpl() { $dir=$_ENV['path_tpl']; $list=array(); $result=array(); if (is_dir($dir)) { $list=wbListFilesRecursive($dir, true); foreach ($list as $l=> $val) {
    if (('.php' == substr($val['file'], -4) or '.htm' == substr($val['file'], -4) or '.tpl' == substr($val['file'], -4)) and !strpos('.inc.', $val['file'])) {
    $path = str_replace($dir, '', $val['path']);
    $res = substr($path.'/'.$val['file'], 1);
    $result[] = $res;
    }
    }
    }
    sort($result);

    return $result;
    }

    function wbListFilesRecursive($dir, $path = false)
    {
    $list = array();
    if (is_dir($dir)) {
    $stack[] = $dir;
    }
    else {
    $stack=array();
    }
    while ($stack) {
    $thisdir = array_pop($stack);
    if (is_dir($thisdir) and $dircont = scandir($thisdir)) {
    $i = 0;
    $idx = 0;
    while (isset($dircont[$i])) {
    if ('.' !== $dircont[$i] && '..' !== $dircont[$i]) {
    $current_file = "{$thisdir}/{$dircont[$i]}";
    if (is_file($current_file)) {
    if (true == $path) {
    $list[] = array(
    'file' => "{$dircont[$i]}",
    'path' => "{$thisdir}",
    );
    } else {
    $list[] = "{$dircont[$i]}";
    }
    ++$idx;
    }
    elseif (is_dir($current_file)) {
    $stack[] = $current_file;
    }
    }
    ++$i;
    }
    }
    }

    return $list;
    }

    function wbArrayWhere($arr, $where)
    {
    $res = array();
    $where=wbSetValuesStr($where);
    foreach ($arr as $key => $val) {
    if (wbWhereItem($val, $where)) {
    $res[]=$arr[$key];
    }
    unset($arr[$key]);
    }
    unset($arr);
    return $res;
    }

    function wbCallFormFunc($name, $Item, $form = null, $mode = null)
    {
    if (!isset($_GET['mode'])) $_GET['mode'] = '';
    if (!isset($_GET['form'])) $_GET['form'] = '';
    if (null == $mode) $mode = $_GET['mode'];
    if ('' == $mode) $mode = 'list';
    if (null == $form) {
    if (isset($Item['form']) && $Item['form'] > '') {
    $form = $Item['form'];
    } else {
    $form = $_GET['form'];
    }
    }
    $sf = $_GET['form'];
    $_GET['form'] = $form;
    // formCurrentInclude($form);
    $func = $form.$name;
    $_func = '_'.$func;
    //$Item=wbItemToArray($Item);
    if (is_callable($func)) {
    $Item = $func($Item, $mode);
    } else {
    if (is_callable($_func)) {
    $Item = $_func($Item, $mode);
    }
    }
    $_GET['form'] = $sf;

    return $Item;
    }

    function wbTranslit($textcyr = null, $textlat = null)
    {
    $cyr = array(
    'ё', 'ж', 'ч', 'щ', 'ш', 'ю', 'а', 'б', 'в', 'г', 'д', 'е', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ъ', 'ы', 'ь', 'э', 'я',
    'Ё', 'Ж', 'Ч', 'Щ', 'Ш', 'Ю', 'А', 'Б', 'В', 'Г', 'Д', 'Е', 'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ц', 'Ъ', 'Ы', 'Ь', 'Э', 'Я', );
    $lat = array(
    'e', 'j', 'ch', 'sch', 'sh', 'u', 'a', 'b', 'v', 'g', 'd', 'e', 'z', 'i', 'i', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'h', 'c', '`', 'y', '', 'e', 'ya',
    'E', 'j', 'Ch', 'Sch', 'Sh', 'U', 'A', 'B', 'V', 'G', 'D', 'E', 'Z', 'I', 'I', 'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'F', 'H', 'c', '`', 'Y', '', 'E', 'ya', );
    if ($textcyr) {
    return str_replace($cyr, $lat, $textcyr);
    }
    elseif ($textlat) {
    return str_replace($lat, $cyr, $textlat);
    } else {
    return null;
    }
    }

    function is_email($email) {
    $res=true;
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $res=false;
    return $res;
    }

    function wbBr2nl($str)
    {
    $str = preg_replace('/(rn|n|r)/', '', $str);
    return preg_replace('=<br */?>=i', 'n' , $str); } function wbCheckPhpCode($code) { $file=$_ENV["path_app"]."/uploads/".wbNewId().".php"; $umask=umask(0); file_put_contents($file,$code); umask($umask); exec("php -l ".$file, $error, $code);
	wbFileRemove($file);
	// ошибок нет
	if ($code == 0) return true;
	// ошибки есть
	return false;
}

?>
