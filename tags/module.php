<?php
    function tagModule(&$dom,$Item=array()) {
        if (!$dom->params->src) {
            $src = $dom->attr("src");    
        } else {
            $src = $dom->params->src;
        }
        $e=$_ENV["path_engine"]."/modules/{$src}/{$src}.php";
        $a=$_ENV["path_app"]."/modules/{$src}/{$src}.php";
        if (is_file($e)) require_once($e);
        if (is_file($a)) require_once($a);
        $attributes=array();
        //$exclude=array("role","data-wb-role","class","src");
        $dom->removeClass("wb-done");
        $exclude=array("data-wb-role","data-wb","src");
        $call=$src."_init"; if (is_callable($call)) {$out=@$call($dom,$Item);}
        $call=$src."__init"; if (is_callable($call)) {$out=@$call($dom,$Item);}
        if (!is_object($out)) {
            $out=wbFromFile($_ENV["route"]["hostp"]."/module/{$src}/");
        }
        $func=$src."_afterRead";
        $_func=$src."__afterRead";
        // функция вызывается после получения шаблона модуля
        if (is_callable($func)) {
            $func($out,$Item,$dom);
        } else if (is_callable($_func)) {
            $_func($out,$Item,$dom);
        }
        $out->wbSetData($Item);
        $ats=$dom->attributes();
        foreach($ats as $an => $av) {
            if (!in_array($an,$exclude)) {
                if ($an=="class") {
                    $out->find(":first:not(style,script,br)")->addClass($av->value);
                } else {
                    $out->find(":first:not(style,script,br)")->attr($an,$av->value);
                }
            }
        }
        $out->find(":first:not(style,script,br)")->append($dom->html());
        $func=$src."_beforeShow";
        $_func=$src."__beforeShow";
        // функция вызывается перед выдачей модуля во внешний шаблон
        if (is_callable($func)) {
            $func($out,$Item);
        } else if (is_callable($_func)) {
            $_func($out,$Item);
        }
        $out->wbSetData($Item);
        $dom->replaceWith($out->outerHtml());
        $dom->addClass("wb-done");
        return $dom;
    }
?>
