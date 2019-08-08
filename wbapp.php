<?php
use Nahid\JsonQ\Jsonq;
use Rct567\DomQuery\DomQuery;
class wbApp {
    public $settings;
    public $route;
    public $data;
    public $dom;
    public $template;

    public function __construct() {
        include_once (__DIR__."/functions.php");
        if (!isset($_ENV['settings'])) wbInit();
require_once $_ENV["path_engine"]."/tags/foreach.php";
require_once $_ENV["path_engine"]."/tags/formdata.php";
    }

    function __call($func, $params){
        $wbfunc="wb".$func;
        if (is_callable($wbfunc)) {
                $res = call_user_func_array($wbfunc,$params);
                if ($func=="Init") {
                        $this->settings();
                        $this->getRoute();
                }
                return $res;
        } else if (!is_callable($func)) {
            die("Function {$wbfunc} not defined");
        } else {
            return call_user_func_array($func,$params);
        }
    }

        public function json($data) {
            $json = new Jsonq();
            if (is_string($data)) {
                $data=wbItemList($data);
            } else if (!is_array($data)) {
                $data=(array)$data;
            }
            return $json->collect($data);
        }

        public function settings() {
        $this->settings=$_ENV["settings"];
        return $this->settings;
        }

        public function getRoute() {
                $this->route=$_ENV["route"];
                return $this->route;
        }

        public function variable($name,$value="__wbVarNotAssigned__") {
        if ($value=="__wbVarNotAssigned__") {
                return $this->data[$name];
        }
        $this->data[$name]=$value;
        return $this->data;
        }

        public function data($data="__wbVarNotAssigned__") {
        if ($data=="__wbVarNotAssigned__") {
                return $this->data;
        }
        $this->data=$data;
        return $this->data;
        }

        public function template($name="default.php") {
                $this->template=wbGetTpl($name);
                $this->dom = clone $this->template;
                return $this->dom;
        }

        public function fromString($string="") {
            $dom=new DomQuery($string);
            $dom->app = $this;
            return $dom;
        }
    
        public function fromFile($file="") {
        $res = "";
        if ($file=="") {
            return $this->fromString("");
        } else {
            if (!session_id()) {
                session_start();
            }
            $context = stream_context_create(array(
                                                 'http'=>array(
                                                         'method'=>"POST",
                                                         'header'=>	'Accept-language:' . " en\r\n" .
                                                         'Content-Type:' . " application/x-www-form-urlencoded\r\n" .
                                                         'Cookie: ' . session_name()."=".session_id()."\r\n" .
                                                         'Connection: ' . " Close\r\n\r\n",
                                                         'content' => http_build_query($_POST)
                                                 )
                                             ));
            session_write_close();
            $url=parse_url($file);
            if (is_file($file)) {
                $fp = fopen ($file,"r");
                flock ($fp, LOCK_SH);
            }
            if (isset($url["scheme"])) {
                $res=@file_get_contents($file,false,$context);
            } else if (is_file($file)) {
                $res=file_get_contents($file);
            }
            if (is_file($file)) {
                $res=file_get_contents($file,false,$context);
                flock ($fp, LOCK_UN);
                fclose ($fp);
            }
            session_start(); // reopen session after session_write_close()
            return $this->fromString($res);
        }
	}

        public function form($form="pages",$mode="show",$engine=false) {
                $this->template=wbGetForm($form,$mode,$engine);
                $this->dom=clone $this->template;
                return $this->dom;
        }

        public function fromTpl($tpl) {
			$tpl=wbGetTpl($tpl);
			$tpl->wbSetData($this->data,false);
			$this->fromString($tpl->outerHtml());
			return $this->dom;
		}


        public function dom($data="__wbVarNotAssigned__",$clear=true) {
			if ($data!=="__wbVarNotAssigned__") {
				$this->data($data);
			}
			$tpl=wbFromString($this->dom);
            $tpl->wbSetData($this->data,$clear);
            $this->dom=$tpl->outerHtml();
            return $this->dom;
        }

        public function html() {
                return $this->dom()->outerHtml();
        }

        public function outerhtml() {
                return $this->dom()->outerHtml();
        }

        public function innerhtml() {
                return $this->dom()->innerHtml();
        }
}
?>
