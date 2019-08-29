<?php
use Nahid\JsonQ\Jsonq;
use Rct567\DomQuery\DomQuery;

class wbDom extends DomQuery {
    
    /* ======================================================= 
                        WEB BASIC EXTENSIONS
    ======================================================= */
    
    public function fetch($Item=null) {
        if ($Item == null AND isset($this->data)) {
            $Item = $this->data;
        } else if ($Item !== null) {
            $this->data = $Item;    
        }
        if (!$this->isDocument) {
            $this->excludeTags();
            $this->fetchParams();
        }
        $wbtags = $this->children(":not(.wb-done)");
        if (count($wbtags)) {
            foreach($wbtags as &$wb) {
                $wb->app = $this->app;
                $wb->data = $this->data;
                $role = $wb->fetchParams()->hasRole();
                $where = $wb->wbWhere();
                if ($where) {
                    if (in_array($role,array_keys($_ENV["tags"]))) {
                        $func="tag".ucfirst($wb->params->role);
                        $func($wb);
                    }
                    $wb->fetch();
                } else {
                    $wb->remove();
                }
            }
        }
        
        
//        echo "<pre>".htmlspecialchars($this->outerHtml())."</pre>";
        if (!$this->wbdone) {
            if (!$this->isDocument) {
                $this->includeTags();
                $this->setValues();
                
            }
        }
        return $this;
    }
    
	public function outerHtml($clear = false) {
        if ($clear) {
            $this->find(".wb-done")->removeClass("wb-done");
        }
		return $this->prop('outerHTML');
	}
    
    public function fetchParams() {
        if (isset($this->params)) return $this;
        $this->setAttributes();
        $wbd = $this->attr("data-wb");
        if (substr($wbd,0,1) == "{" AND substr($wbd,-1,1) == "}") {
            $params=json_decode($wbd,true);
        } else {
            parse_str($wbd,$params);
        }
        $attributes = $this->attributes;
        if ($attributes->length) {
            foreach($attributes as $attr) {
                $tmp=$attr->name;
                if (strpos($tmp,"ata-wb-")) {
                    $tmp=str_replace("data-wb-","",$tmp);
                    $params[$tmp]=$attr->value; 
                }
            }
        }
        $this->params=(object)$params;
        if (isset($this->params->role)) {
            $this->role = $this->params->role;
        } else {
            $this->role = false;
        }
        return $this;
    }
    
    function excludeTags() {
        $list=$this->find("textarea,[type=text/template],.wb-done,.wb-value,pre,.nowb,[data-role=module],[data-wb-role=module],select.select2[data-wb-ajax] option");
        foreach ($list as $ta) {
            $id=wbNewId();
            $ta->attr("wb-exclude-id",$id);
            $ta->replaceWith(strtr($ta->outerHtml(),array("{{"=>"#~#~","}}"=>"~#~#")));
        };
        return $this;
    }
    
    function includeTags() {
        $list=$this->find("[wb-exclude-id]");
        foreach ($list as $ta) {
            $ta->removeAttr("wb-exclude-id");
            $ta->replaceWith(strtr($ta->outerHtml(),array("#~#~"=>"{{","~#~#"=>"}}")));
        };
        return $this;
    }
    
    public function clear() {
        $this->html("");
        return $this;
    }
    
    public function tag() {
        return $this->tagName;
    }
    
    public function hasRole($role=null) {
        if ($role == null && isset($this->params->role)) {
            return $this->params->role;
        } else if ($role !== null AND $role == $this->params->role) {
            return true;
        } else {
            return false;
        }
    }
    
    public function setValues($Item=null) {
        if ($Item == null) $Item = $this->data;
        if (!((array)$Item === $Item)) {$Item=[];}
        $this->setAttributes($Item);
        $this->html(wbSetValuesStr($this->html(),$Item));
        return $this;
    }
    
    public function setAttributes($Item=null) {
        if ($Item == null) $Item = $this->data;
            $attributes=$this->attributes;
            if ($attributes->length) {
                foreach($attributes as $at) {
                    $atname=$at->name;
                    if ($atname !== "data-wb-if") {
                        $atval=html_entity_decode($this->attr($atname));
                        if (strpos($atname,"}}")) $atname=wbSetValuesStr($atname,$Item);
                        if ($atval>"" && strpos($atval,"}}")) {
                            $fld=str_replace(array("{{","}}"),array("",""),$atval);
                            if (isset($Item[$fld]) AND $this->is(":input")) {
                                $atval=$Item[$fld];
                                if ((array)$atval === $atval) $atval=wbJsonEncode($atval);
                            } else {
                                $atval=wbSetValuesStr($atval,$Item);
                            }
                            $this->attr($atname,$atval);
                        };
                    }
                };
            }

        return $this;
    }
    
    public function wbWhere($Item=null) {
        $res = true;
        $where=$this->params->where;
        if (!$where) return $res;
        if ($Item == null) $Item=$this->data;
        $res = wbWhereItem($Item,$where);
        //if ($this->params->hide !== "false") $this->removeAttr("data-wb-where");
        return $res;
    }
    
}
    
    

class wbApp {
    public $settings;
    public $route;
    public $data;
    public $dom;
    public $template;

    public function __construct() {
        include_once (__DIR__."/functions.php");
        if (!isset($_ENV['settings'])) wbInit();
        require_once $_ENV["path_engine"]."/tags/pagination.php";
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

        public function fromString($string="", $isDocument = true) {
            $dom=new wbDom($string);
            $dom->app = $this;
            $dom->isDocument = $isDocument;
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
