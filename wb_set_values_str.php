<?php

include_once(__DIR__."/weprocessor/weprocessor.php");

function wbSetValuesStr($tag = "",$Item = array(), $limit = 2, $vars = null)
{
	$Item=wbItemToArray($Item);
	if (is_object($tag)) $tag = $tag->outerHtml();
	if (!strpos($tag,"}}")) return $tag;

    if (true) {
        $processor = new WEProcessor($Item);
        return $processor->substitute($tag);
    } else {
        // $vars - не используется
        if (is_object($tag)) $tag = $tag->outerHtml();
        if (!is_array($Item)) $Item = array($Item);
        html_entity_decode($tag);
        $tag = wbChangeQuot($tag);            // заменяем & quot на "
        if (!strpos($tag,"}}")) return $tag;

    // Обработка для доступа к полям с JSON имеющим id в содержании, в частности к tree
    /*
        $flag = false;
        foreach ($Item as $key => $item) {
            if (!is_array($item) and ('[' == substr(trim($item), 0, 1) or '{' == substr(trim($item), 0, 1))) {
                $item = json_decode($item, true);
                if (is_array($item)) {
                    foreach ($item as $k => $a) {
                        if (isset($a['id']) and $a['id'] > '' and $key !== $a['id']) {
                            $flag = true;
                            $Item[$key][$a['id']] = $a;
                        }
                    }
                }
            } else {
                $Item[$key] = $item;
            }
        }
    */
        // функция подставляющая значения
        $spec= array('_form','_mode','_item','_id');
        $exit = false;
        $err = false;
        $nIter = 0;
        $mask = '`(\{\{){1,1}([^_{]%*[\w\d]+|_form|_mode|_item|((_SETT|_SETTINGS|_SESS|_SESSION|_VAR|_SRV|_COOK|_COOKIE|_FUNC|_LANG|_ENV|_REQ|_GET|_POST|@(.*)|%*[\w\d]+)?' .
        '((([\[]{1,1}(%*[\w\d]+|"%*[\w\d]+")[\]]{1,1})|([\.]{1,1}(%*[\w\d]+|"%*[\w\d]+")))*)?' .
        '(->)?([\w\d]+\(([\w\d\040,@\"\'-_\.]*)\))?))(\}\}){1,1}`u';
        $maskI = '`[\[\.]{1,1}(%*[\w\d]+|"%*[\w\d]+")[\]]?`u';
        while (!$exit) {
            $nUndef = 0;
            $nSub   = preg_match_all($mask, $tag, $res, PREG_OFFSET_CAPTURE);                // найти все вставки, не содержащие в себе других вставок
            if ($nSub === false) {
                echo 'Ошибка в шаблоне подстановок. Обратитесь к разработчику.' . '<br>';
                $err = true;
            } else {
                if ($nSub == 0) {
                    $exit = true;
                } else {
                    $text    = '';
                    $startIn = 0;        // начальная позиция текста за предыдущей заменой
                    for ($i = 0; $i < $nSub; $i++)        // замена в исходном тексте найденных подстановок
                    {
                        $In = $res[4][$i][0] . $res[6][$i][0];                        // текст вставки без скобок {{ и }} и без функции
                        if (($In == '') and ($res[12][$i][0] == '') and ($res[13][$i][0] == ''))  // нет стандартного префикса и индексов и функции
                        {
                            $In = $res[2][$i][0];                        // текст вставки без скобок {{ и }}
                        }
                        $beforSize = $res[2][$i][1] - 2 - $startIn;
                        $text .= substr($tag, $startIn, $beforSize);        // исходный текст между предыдущей и текущей вставками
                        $default = false;
                        $special = 0;
                        switch (strtoupper($res[4][$i][0]))                    // префикс вставки
                        {
                            case '_SETT':
                            $sub = '$_ENV["settings"]';
                            break;
                            case '_SETTINGS':
                            $sub = '$_ENV["settings"]';
                            break;
                case '_LANG':
                if (isset($_SESSION["lang"])) {
                    $lang=$_SESSION["lang"];
                } else if (isset($_ENV["lang"])) {
                    $lang=$_ENV["lang"];
                }
                if (!isset($lang)) {$lang="eng";}
                if ($vars!==null AND is_array($Item) AND isset($Item["_global"]) AND $Item["_global"]==false ) {
                    $sub = '$Item[$lang]';
                } else {
                    $sub = '$_ENV["locale"][$lang]';
                }
                break;
                            case '_VAR':
                            $sub = '$_ENV["variables"]';
                            break;
                            case '_SESS':
                            $sub = '$_SESSION';
                            break;
                            case '_SESSION':
                            $sub = '$_SESSION';
                            break;
                            case '_COOK':
                            $sub = '$_COOKIE';
                            break;
                            case '_COOKIE':
                            $sub = '$_COOKIE';
                            break;
                            case '_REQ':
                            $sub = '$_REQUEST';
                            break;
                            case '_GET':
                            $sub = '$_GET';
                            break;
                            case '_ENV':
                            $sub = '$_ENV';
                            break;
                            case '_FUNC':
                            // нужно придумать вызов функций
                            break;
                            case '_SRV':
                            $sub = '$_SERVER';
                            break;
                            case '_POST':
                            $sub = '$_POST';
                            break;
                            case '':
                            if (in_array($In, $spec)) {
                                $sub = '$_GET';
                                $In  = substr($In, 1, strlen($In) - 1);        // убираем символ _ в начале
                                if (!isset($_GET["item"]) and ($In == 'item')) $In = 'id';
                                if (!isset($_GET["id"]) and ($In == 'id')) $In = 'item';
                            } else {
                                $sub = '$Item';
                            }
                            break;
                            default:                                    // 1ый индекс без скобок [] - префикса нет
                            $sub     = '$Item';
                            $default = true;
                            $n = strlen($res[4][$i][0]);
                            $In= '[' . substr($In, 0, $n) . ']' . substr($In, $n, strlen($In) - $n);
                            break;
                        }
                        if ($default) {
                            $pos = 0;
                        } else {
                            $pos = strlen($res[4][$i][0]);        // смещение индексной части от начала вставки
                        }
                        if ($res[6][$i][1]>-1)        // индексы есть
                        {
                            $nInd = preg_match_all($maskI, $res[6][$i][0], $resI, PREG_OFFSET_CAPTURE);                // найти все индексы
                            $indB = '';
                            for ($i1 = 0; $i1 < $nInd; $i1++) {
                                $indB .= '[' . $resI[1][$i1][0] . ']';
                            }
                            $In = str_replace($res[6][$i][0], $indB, $In);     // замена . на []
                        }
                        if (($sub == '$Item') or ($sub == '$_GET') or (($sub != '$Item') and ($res[6][$i][0] > '')))        // кавычки проставляем для $Item или остальных префиксов с непустыми индексами
                        {
                            $sub .= wbSetQuotes(substr($In, $pos, strlen($In) - $pos));        // индексная часть текущей вставки с добавленными кавычками у текстовых индексов
                        }
                        if (eval('return isset('.$sub.');') AND !eval('return is_array('.$sub.');')) {
                            $Item = wbsvRestoreValue($Item,$sub);
                        }

                        if (eval('return isset(' . $sub . ');')) {
                            if (eval('return is_array(' . $sub . ');')) {
                                $bDone = false;
                                if ($res[12][$i][1]>-1 and $res[13][$i][1]>-1)        // есть -> и функция
                                {
                                    $func      = $res[13][$i][0];
                                    $func_name = substr($func, 0, strpos($func, '('));
                                    if (function_exists($func_name)) {

                                        $newParm = array();
                                        if ($res[14][$i][1] == - 1)        // параметров нет?
                                        {
                                            $newParm[] = $sub;        // если параметров нет, подставляем префикс + индексы
                                        } else {
                                            $a     = $res[14][$i][0];
                                            $Parms = explode(',', $a);
                                            foreach ($Parms as $parm) {
                                                $newParm[] = (trim($parm) == '@') ? $sub : trim($parm);
                                            }
                                        }
                                        if (is_array($newParms) AND count($newParms)) {
                                            $ParmsNew = implode(',', $newParm);
                                            $func     = str_replace($res[14][$i][0], $ParmsNew, $func);
                                        } else {
                                            $func = str_replace("()", "({$sub})", $func);
                                        }
                                        $result = eval('$text .= @' . $func . ';');
                                        if ($result !== false) {
                                            $bDone = true;
                                        }
                                    }
                                }
                                if (!$bDone) {
                                    $text .= eval('return json_encode(' . $sub . ');');
                                    if ($res[12][$i][1]>-1 or $res[13][$i][1]>-1)        // есть -> или функция
                                    {
                                        $text .= $res[12][$i][0] . $res[13][$i][0];
                                    }
                                }
                            } else {
                                $bDone = false;
                                if ($res[12][$i][1]>-1 and $res[13][$i][1]>-1)        // есть -> и функция
                                {
                                    $newParm = array();
                                    $subE = eval('return ' . $sub . ';');
                                    if (is_string($subE)) $subE = "'{$subE}'";
                                    if ($res[14][$i][0] == "")        // параметров нет?
                                    {
                                        $newParm[] = $subE;        // если параметров нет, подставляем префикс + индексы
                                        $func     = str_replace('()', '('.$subE.')', $res[13][$i][0]);
                                    } else {
                                        $a     = $res[14][$i][0];
                                        $Parms = explode(',', $a);
                                        foreach ($Parms as $parm) {
                                            $newParm[] = (trim($parm) == '@') ? $subE : trim($parm);
                                        }
                        $ParmsNew = implode(',', $newParm);
                        $func     = str_replace($res[14][$i][0], $ParmsNew, $res[13][$i][0]);
                                    }
                                    $func_name= substr($func, 0, strpos($func, '('));
                                    if (function_exists($func_name)) {
                                        $result = eval('$text .= @' . $func . ';');
                                        if ($result !== false) {
                                            $bDone = true;
                                        }
                                    }
                                }
                                if (!$bDone) {
                                    eval('$text .= ' . $sub . ';');
                                    if ($res[12][$i][1]>-1 or $res[13][$i][1]>-1)        // есть -> или функция
                                    {
                                        $text .= $res[12][$i][0] . $res[13][$i][0];
                                    }
                                }
                            }
                        } else {
                            if (strlen($res[6][$i][0]) == 0 and $res[12][$i][1]==-1 and $res[13][$i][1]>-1)        // нет индексов и  нет -> и есть функция
                            {
                                $newParm = array();
                                $Parms = explode(',', $res[14][$i][0]);
                                foreach ($Parms as $parm) {
                                    $newParm[] = (trim($parm) == '@') ? '""' : trim($parm);
                                }
                                $ParmsNew = implode(',', $newParm);
                                $func     = str_replace($res[14][$i][0], $ParmsNew, $res[13][$i][0]);
                                if ($res[4][$i][1] > 0) $func      = $res[4][$i][0] . $func;
                                $func_name = substr($func, 0, strpos($func, '('));
                                if (function_exists($func_name)) {
                                    $result = eval('$text .= @' . $func . ';');
                                    if ($result === false) {
                                        $text .= $func;
                                    }
                                } else {
                                    $text .= $func;
                                }
                            } else {
                                //if (eval('return isset($Item["'.$res[4][$i][0].'"]);')) {
                                if (eval('return isset('.$sub.');')) {
                                    $temp = '';
                                    $temp = strtr($temp, array('{{'=> '#~#~','}}'=> '~#~#'));
                                    $text .= $temp;
                                } else {
                        if ($res[12][$i][1]>-1 and $res[13][$i][1]>-1)        // есть -> и функция
                        {
                        $func      = $res[13][$i][0];
                        $func_name = substr($func, 0, strpos($func, '('));
                        if (function_exists($func_name) AND eval('return isset('.$sub.');')) {
                            $newParm = array();
                            if ($res[14][$i][1] == - 1)        // параметров нет?
                            {
                            $newParm[] = $sub;        // если параметров нет, подставляем префикс + индексы
                            } else {
                            $a     = $res[14][$i][0];
                            $Parms = explode(',', $a);
                            foreach ($Parms as $parm) {
                                $newParm[] = (trim($parm) == '@') ? $sub : trim($parm);
                            }
                            }
                            if (count($newParms)) {
                            $ParmsNew = implode(',', $newParm);
                            $func     = str_replace($res[14][$i][0], $ParmsNew, $func);
                            } else {
                            $func = str_replace("()", "({$sub})", $func);
                            }
                            $result = eval('$text .= @' . $func . ';');
                        } else {
                            $text .= '{{' . $res[2][$i][0] . '}}';
                        }
                        } else {
                            $e=trim(wbSetQuotes($sub));
                            $e=str_replace(array('["$','"]"]'),array('$','"]'),$e);
                            if (substr($e,0,1)=='$') {
                                if (eval('return isset( '.$e.' );')) {
                                    eval('$text .= '.$e.' ;');
                                } else {
                                    $text .= '{{' . $res[2][$i][0] . '}}';
                                }
                            } else {
                                $text .= '{{' . $res[2][$i][0] . '}}';
                            }
                        }
                                }

                                $nUndef++;
                            }
                        }
                        $startIn += $beforSize + strlen($res[2][$i][0]) + 4;
                        if ($i + 1 == $nSub)        // это была последняя вставка
                        {
                            $text .= substr($tag,  $startIn, strlen($tag) - $startIn);
                        }
                    }
                    $tag = $text;
                }
            }
            $nIter++;
            if ($limit > 0 and $nIter == $limit) $exit = true;
            if ($nUndef == $nSub) $exit = true;
        }
        $tag = strtr($tag, array('#~#~'=> '{{','~#~#'=> '}}'));
        return $tag;
    }
}

?>
