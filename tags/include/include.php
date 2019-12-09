<?php
class tagInclude extends kiNode  {

    function __construct($that){
        $this->DOM = $that;
    }

    public function __destruct(){
        unset($this->DOM);
    }

    public function tagInclude($Item = array()) {
		include($_ENV["path_engine"]."/wbattributes.php");
        if (!isset($src)) {
            $src=$ssrc=$this->DOM->attr("src");
        }
        else {
            $ssrc=$src;
        }

	if (isset($src) AND strpos($src,"/")) $src=wbNormalizePath($src);

        $res=0;
        if (isset($id)) {
            $did=$id;
        }
        else {
            $did="";
            unset($id);
        }
        if (isset($add)) {
            $dad=$add;
        }
        else {
            $dad="";
            unset($add);
        }
        if (isset($header)) {
            $Item["header"]=$header;
        }
        else {
            $header="";
        }
        if (isset($footer)) {
            $Item["footer"]=$footer;
        }
        else {
            $footer="";
        }
        if (isset($vars)) {
            $Item=wbAttrAddData($vars,$Item);
        }
        else {
            $vars="";
        }
        if (isset($json)) {
            $Item=json_decode($json,true);
        }
        else {
            $json="";
        }
        if (isset($formsave)) {
            $dfs=$formsave;
            unset($formsave);
        }
        else {
            $dfs="";
        }
        if (!isset($class)) {
            $class="";
        }
        if (!isset($name)) {
            $name=$this->DOM->attr("name");
        }
        switch($src) {
        case "modal":
            $this_content=wbGetForm("common",$src);
            break;
        case "imgviewer":
            // устарело
            // вызов через <script data-wb-src="imgviewer"></script>
            $src="/engine/js/imgviewer.php";
            break;
        case "uploader":
            if ($mode > "") {
                $this_content=wbGetForm("common","upl_".$mode);
            } else {
                $this_content=wbGetForm("common",$src);
            }
            break;
        case "editor":
            if ($name=="") {
                $name="text";
            }
            $this_content=wbGetForm("common",$src);
            break;
        case "source":
            if ($name=="") {
                $name="text";
            }
            $this_content=wbGetForm("common",$src);
            break;
        case "gallery":
            $this_content=wbGetForm("common",$src);
            $attributes=$this->DOM->attributes();
            if (is_object($attributes)) {
                foreach($attributes  as $at) {
                    $atname=$at->name;
                    $atval=$this->DOM->attr($atname);
                    if ($atname!=="src" AND $atname!=="data-wb-role") {
                        $this_content->find("div",0)->attr($atname,$atval);
                        $this_content->find("div",0)->removeClass("wb-done");
                    }
                }
            }
            break;
        case "seo":
            $this_content=wbGetForm("common",$src);
            break;
        case "form":
            $form="";
            $mode="";
            $arr=explode("_",$name);
            if (isset($arr[0])) $form=$arr[0];
            if (isset($arr[1])) {
                unset($arr[0]);
                $mode=implode("_",$arr);
            }
            $engine=explode(":",$form);
            if (isset($engine[1]) AND ($engine[0]=="engine" OR $engine[0]=="e")) {
                $form=$engine[1];
                $engine=true;
            } else {
                $engine=false;
            }
            $this_content=wbGetForm($form,$mode,$engine);
            break;
        case "snippet":
            if (!isset($mode) AND isset($name)) {
                $mode=$name;
            }
            $this_content=wbGetForm("snippets",$mode);
            $this_content->wbSetValues($_ENV["attributes"]);
            $this_content->wbClearClass();
            break;
        case "template":
            $this_content=wbGetTpl($name);
            break;
        case "module":
            $_SESSION["module"]=array($name=>$this->DOM->attributes());
            $module=$_ENV["route"]["hostp"]."/module/{$name}/";
            unset($_SESSION["module"]);
            $this_content=wbFromFile($module);
            $this_content->wbSetFormLocale();
            break;
        case "shortcode":
                $tree=wbItemRead("admin","shortcodes");
                if ($_ENV["last_error"]=="1006") {
                        $tree=wbItemRead("admin:engine","shortcodes");
                        wbItemSave("admin",$tree);
                }
                $tree=wbItemToArray($tree["shortcode"]);
                $tree=wbTreeFindBranch($tree,$name);
                $this_content=wbFromString($tree[0]["data"]["code"]);
                break;
        }

        $vars=$this->DOM->attr("data-wb-vars");
        if ($vars>"") {
            $Item=wbAttrAddData($vars,$Item);
        }
        if (!isset($this_content)) {
            if ($src=="") {
                $src=$this->DOM->html();
                $this_content=wbfromString($src);
            }
            else {
                $tplpath=explode("/",$src);
                $tplpath=array_slice($tplpath,0,-1);
                $tplpath=implode("/",$tplpath)."/";
                $src=wbSetValuesStr($src,$Item);
                $file=$_ENV["path_app"].$src;
                if (is_file($file)) {
                    $src=$file;
                } else if (is_file($_SERVER["DOCUMENT_ROOT"].$file)) {
                    $src=$_SERVER["DOCUMENT_ROOT"].$file;
                } else {
					$tmp = explode("://",$src);
                    if ($tmp[0] !== $_ENV["route"]["scheme"] ) {
                        if (substr($src,0,1) !== "/") {
                            $src="/".$src;
                        } 
                        $src=$_ENV["route"]["hostp"].$src;
                    }
                }
                $this_content=wbFromFile($src);
            }
        }
        $this_content->wbSetFormLocale();
        if ($did>"") {
            $this_content->find(":first")->attr("id",$did);
        }
        if ($dad=="false") {
            $this_content->find("[data-wb-formsave]")->attr("data-wb-add",$dad);
        }
        if ($dfs>"") {
            $this_content->find("[data-wb-formsave]")->attr("data-wb-formsave",$dfs);
        }
        if ($dfs=="false") {
            $this_content->find("[data-wb-formsave]")->remove();
        }
        if ($class>"" AND ( $this->DOM->attr("data-wb-hide")=="*" OR $this->DOM->is("meta") OR $this->DOM->is("link") )) {
            $this_content->find(":first")->addClass($class);
        }

        if (($ssrc=="editor" OR $ssrc=="source") && !$this_content->find("textarea.{$ssrc}.wb-done")->length) {
            if ($did>"") {
                $this_content->find(":first")->removeAttr("id");
                $this_content->find("textarea.{$ssrc}")->attr("id",$did);
            }
            foreach($this_content->find("textarea.{$ssrc}:not(.wb-done)") as $editor) {
                if ($editor->attr("id")=="") {
                    $sid=wbNewId();
                    $editor->attr("id","{$ssrc}-{$sid}");
                    $editor->parent("div")->find(".source-toolbar")->attr("id","{$ssrc}-toolbar-{$sid}");;
                }
                if ($editor->attr("name")=="") {
                    $editor->attr("name",$name);
                }
            }
            $this_content->find("textarea.{$ssrc}")->attr("name",$name);
            if ($ssrc=="source" AND !$this_content->parents("form")->find("[name={$name}]")->length) {
                $this_content->append("<textarea class='hidden wb-done' name='$name'></textarea>");
            }

        }

        $this_content->tagInclude_inc($Item);
        $this_content->wbSetData($Item);



        $this->DOM->append($this_content);

        if ($this->DOM->find("include")->length) {
            $this->DOM->tagInclude_inc($Item);
        }

        if ($this->DOM->attr("data-wb-hide")=="*" OR $this->DOM->is("meta") OR $this->DOM->is("link")) {
            $this->DOM->replaceWith($this->DOM->html());
        }
    }

    public function tagInclude_inc($Item) {
        if ($this->DOM->find("include")->length) {
            $this_content=$this->DOM->html();
            $this->DOM->append("<div id='___include___' style='display:none;'>{$this_content}</div>");
            //$Item=wbItemToArray($Item);
            foreach($this->DOM->find("include") as $inc) {
                $inc->wbSetAttributes($Item);
                if (!isset($data)) $data=$inc->attr("data");
                $from=$inc->attr("data-wb-from");
                if ($data>"") {
                    $this->DOM->find("#___include___")->html($data);
                } else {
                    if ($from=="") {
                        $from="text";
                    }
                    $this->DOM->find("#___include___")->html(wbGetDataWbFrom($Item,$from));
                }


                $attr=array("html","outer","htmlOuter","outerHtml","innerHtml","htmlInner","text","value");
                foreach ($attr as $key => $attribute) {
                    $find=$inc->attr($attribute);
                    if ($attribute=="outer" OR $attribute=="htmlOuter") {
                        $attribute="outerHtml";
                    }
                    if ($attribute=="inner" OR $attribute=="html" OR $attribute=="innerHtml" OR $attribute=="htmlInner") {
                        $attribute="html";
                    }
                    if ($find>"" ) {
                        foreach($this->DOM->find("#___include___")->find($find) as $text) {
                            $inc->after($text->$attribute());
                        }
                    }
                };
                unset($attribute);
                $inc->remove();
            };
            unset($inc);
            $this->DOM->find("#___include___")->remove();
        }
    }



}
