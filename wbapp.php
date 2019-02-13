<?php
use Nahid\JsonQ\Jsonq;
class wbApp {
    public $settings;
    public $route;
    public $data;
    public $dom;
    public $template;

    public function __construct() {
        include_once (__DIR__."/functions.php");
        wbInit();
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
            die("Функция {$wbfunc} не существует");
        } else {
            return call_user_func_array($func,$params);
        }
    }

	public function json($data) {
		$json = new Jsonq();
		if (is_string($data)) {$data=wbItemList($data);}
		if (!is_array($data)) {$data=array($data);}
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
                $this->dom=$this->template->clone();
                return $this->dom;
        }

        public function fromString($string) {
                $this->template=wbFromString($string);
                $this->dom=$this->template->clone();
                return $this->dom;
        }

        public function form($form="pages",$mode="show",$engine=false) {
                $this->template=wbGetForm($form,$mode,$engine);
                $this->dom=$this->template->clone();
                return $this->dom;
        }

        public function dom($data="__wbVarNotAssigned__") {
		if ($data!=="__wbVarNotAssigned__") {
			$this->data($data);
		}
                $this->dom->wbSetData($this->data);
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
