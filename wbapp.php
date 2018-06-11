<?php

class wbApp {
    public static $settings = array();
    public static $route = array();
    
    public function __construct() {
        include_once (__DIR__."/functions.php");
        
    }
    
    function __call($func, $params){
        $func="wb".$func;
        if (is_callable($func)) {
            return call_user_func_array($func,$params);
        } else {
            die("Функция wb{$func} не существует");
        }
    }

    public function settings() {
        self::$settings=$_ENV["settings"];
        return self::$settings;
    }
    
    public function getRoute() {
        self::$route=$_ENV["route"];
        return self::$route;
    }
}


?>