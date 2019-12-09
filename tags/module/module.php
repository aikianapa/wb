<?php
class tagModule extends kiNode  {

    function __construct($that){
        $this->DOM = $that;
    }

    public function __destruct(){
        unset($this->DOM);
    }

    public function tagModule($Item=array()) {
        $src=$this->DOM->attr("src");
        $e=$_ENV["path_engine"]."/modules/{$src}/{$src}.php";
        $a=$_ENV["path_app"]."/modules/{$src}/{$src}.php";
        if (is_file($e)) {
            require_once($e);
        }
        if (is_file($a)) {
            require_once($a);
        }
        $attributes=array();
        //$exclude=array("role","data-wb-role","class","src");
        $this->DOM->removeClass("wb-done");
        $exclude=array("data-wb-role","src");
	$aCall=$src."_init"; 
	$eCall=$src."__init";
	if (is_callable($aCall)) {$out=@$aCall($this->DOM,$Item);} else if (is_callable($eCall)) {$out=@$eCall($this->DOM,$Item);}
	if (!is_object($out) AND $out>"") {
		$out = wbFromString($out);
	} else {
		$out=wbFromFile($_ENV["route"]["hostp"]."/module/{$src}/");
	}
        $func=$src."_afterRead";
        $_func=$src."__afterRead";
        // функция вызывается после получения шаблона модуля
        if (is_callable($func)) {
            $func($out,$Item,$this->DOM);
        }
        else {
            if (is_callable($_func)) {
                $_func($out,$Item,$this->DOM);
            }
        }
        $out->wbSetData($Item);
        $ats=$this->DOM->attributes();
        foreach($ats as $an => $av) {
            if (!in_array($an,$exclude)) {
                if ($an=="class") {
                    $out->find(":first:not(style,script,br)")->addClass($av);
                } else {
                    $out->find(":first:not(style,script,br)")->attr($an,$av);
                }
            }
        }
        $out->find(":first:not(style,script,br)")->append($this->DOM->html());
        $func=$src."_beforeShow";
        $_func=$src."__beforeShow";
        // функция вызывается перед выдачей модуля во внешний шаблон
        if (is_callable($func)) {
            $func($out,$Item);
        }
        else {
            if (is_callable($_func)) {
                $_func($out,$Item);
            }
        }
        $out->wbSetData($Item);
        $this->DOM->replaceWith($out);
        unset($_SESSION["{$src}_data"]);
    }



}
