<?php
final class wbRouter {

    /*
    $routes = array(
      // 'url' => 'контроллер/действие/параметр1/параметр2/параметр3'
      '/' => 'MainController/index', // главная страница
      '/(p1:str)(p2:num)unit(p3:any).htm' => '/show/page/$1/$2/$3', // главная страница
      '/contacts' => 'MainController/contacts', // страница контактов
      '/blog' => 'BlogController/index', // список постов блога
      '/blog/(:num)' => 'BlogController/viewPost/$1', // просмотр отдельного поста, например, /blog/123
      '/blog/(:any)/(:num)' => 'BlogController/$1/$2', // действия над постом, например, /blog/edit/123 или /blog/dеlete/123
      '/(:any)' => 'MainController/anyAction' // все остальные запросы обрабатываются здесь
    );

    // добавляем все маршруты за раз
    wbRouter::addRoute($routes);

    // а можно добавлять по одному
    wbRouter::addRoute('/about', 'MainController/about');
    echo "<br><br>";
    // непосредственно запуск обработки
    print_r(wbRouter::getRoute());
    */

    public static $routes = array();
    private static $params = array();
    private static $names = array();
    public static $requestedUrl = '';


    // Добавить маршрут
    public static function addRoute($route, $destination=null) {
        if ($destination != null && !is_array($route)) {
            $route = array($route => $destination);
        }
        self::$routes = array_merge(self::$routes, $route);
    }

    // Разделить переданный URL на компоненты
    public static function splitUrl($url) {
        return preg_split('/\//', $url, -1, PREG_SPLIT_NO_EMPTY);
    }

    // Текущий обработанный URL
    public static function getCurrentUrl() {
        return (self::$requestedUrl?:'/');
    }

    // Обработка переданного URL
    public static function getRoute($requestedUrl = null) {
        // Если URL не передан, берем его из REQUEST_URI
        if ($requestedUrl === null) {
            $request=explode('?', $_SERVER["REQUEST_URI"]);
            $uri = reset($request);
            $requestedUrl = urldecode(rtrim($uri, '/'));
        }
        self::$requestedUrl = $requestedUrl;
        // если URL и маршрут полностью совпадают
        if (isset(self::$routes[$requestedUrl])) {
            self::$params = self::splitUrl(self::$routes[$requestedUrl]);
            self::$names[] = "";
            return self::returnRoute();
        }
        if (is_array(self::$routes)) {
        foreach (self::$routes as $route => $uri) {
            // Заменяем wildcards на рег. выражения
            $name=null;
            self::$names=array();
            $route=str_replace(" ","",$route);
            if (strpos($route, ':') !== false) {
                // Именование параметров
                preg_match_all("'\((\w+):(\w+)\)'",$route,$matches);
                if (isset($matches[1])) {
                    foreach($matches[1] as $name) {
                        $route=str_replace("(".$name.":","(:",$route);
                        self::$names[] = $name;
                    }
                }
                $route = str_replace('(:any)', '(.+)', str_replace('(:num)', '([0-9]+)', str_replace('(:str)', '(.[a-zA-Z]+)', $route)));
            }
            if (preg_match('#^'.$route.'$#', $requestedUrl)) {
                if (strpos($uri, '$') !== false && strpos($route, '(') !== false) {
                    $uri = preg_replace('#^'.$route.'$#', $uri, $requestedUrl);
                }
                self::$params = self::splitUrl($uri);
                break; // URL обработан!
            }
        }
	}
        return self::returnRoute();
    }

    // Сборка ответа
    public static function returnRoute() {
        $_GET=array();
        $_ENV["route"]=array();
        $_ENV["route"]["uri"]=$_SERVER["REQUEST_URI"];
        $controller="form";
        $action="mode";

        $form = isset(self::$params[0]) ? self::$params[0]: 'default_form';
        $mode = isset(self::$params[1]) ? self::$params[1]: 'default_mode';
        foreach(self::$params as $i => $param) {
            if (strpos($param, ':')) {
                $tmp=explode(":",$param);
                $_ENV["route"][$tmp[0]]=$tmp[1];
            } else {
                if ($i==0) {
                    $_ENV["route"]["controller"]=$param;
                }
                if ($i==1) {
                    $_ENV["route"]["mode"]=$param;
                }
                if ($i>1) {
                    $_ENV["route"]["params"][]=$param;
                }
                if (isset(self::$names[$i])) {
                    $_ENV["route"]["params"][self::$names[$i]]=$param;
                }
            }


        }
        $tmp=explode("?",$_SERVER["REQUEST_URI"]);
        if (isset($tmp[1])) {
            parse_str($tmp[1],$get);
            if (!isset($_ENV["route"]["params"])) {
                $_ENV["route"]["params"]=array();
            }
            $_ENV["route"]["params"]=(array)$_ENV["route"]["params"]+(array)$get;
        }
        $_GET=array_merge($_GET,$_ENV["route"]);
        if (isset($_GET["engine"]) && $_GET["engine"]=="true") {
            $_SERVER["SCRIPT_NAME"]="/engine".$_SERVER["SCRIPT_NAME"];
        }
        if (isset($_SERVER["REQUEST_SCHEME"]) && $_SERVER["REQUEST_SCHEME"]>"") {
            $scheme=$_SERVER["REQUEST_SCHEME"];
        }
        elseif (isset($_SERVER["SCHEME"]) && $_SERVER["SCHEME"]>"") {
            $scheme=$_SERVER["SCHEME"];
        }
        else {
            $scheme="http";
        }
        $_ENV["route"]["scheme"]=$scheme;
        $_ENV["route"]["host"]=$_SERVER["HTTP_HOST"];
        $_ENV["route"]["port"]=$_SERVER["SERVER_PORT"];
        $tmp=explode(".",$_ENV["route"]["host"]);
        $count=count($tmp);
        if ($count==1) {
            $_ENV["route"]["domain"]=$tmp[count($tmp)-1];
            $_ENV["route"]["zone"]="";
            $_ENV["route"]["subdomain"]="";
        } else {
            $_ENV["route"]["domain"]=$tmp[count($tmp)-2].".".$tmp[count($tmp)-1];
            $_ENV["route"]["zone"]=$tmp[count($tmp)-1];
            if ($tmp>2) {
                unset($tmp[$count-1],$tmp[$count-2]);
                $_ENV["route"]["subdomain"]=implode(".",$tmp);
            }
        }
        $_ENV["route"]["hostp"]=$_ENV["route"]["scheme"]."://".$_ENV["route"]["host"];
        if ($_ENV["route"]["port"]!=="80" AND $_ENV["route"]["port"]!=="443") {
            $_ENV["route"]["hostp"].=":".$_ENV["route"]["port"];
        }
        if ($form=='default_form' && $mode='default_mode' && $_SERVER["QUERY_STRING"]>"") {
            parse_str($_SERVER["QUERY_STRING"],$_GET);
            $_ENV["route"]=array("scheme"=>$scheme,"host"=>$_SERVER["HTTP_HOST"],"controller"=>$controller,$controller=>$_GET["form"], $action=>$_GET["mode"], "params"=>$_GET);
        }
        $_ENV["server"]=$_ENV["route"]["hostp"];
        return $_ENV["route"];
    }

}
?>
