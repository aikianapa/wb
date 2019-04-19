<?php
class tagMultiInput extends kiNode  {

    function __construct($that){
        $this->DOM = $that;
    }

    public function __destruct(){
        unset($this->DOM);
    }

    public function tagMultiInput($Item) {
	if ($this->DOM->hasClass("wb-done")) return;
        include($_ENV["path_engine"]."/wbattributes.php");
        if ($this->DOM->attr("name") AND !isset($name)) {
            $name=$this->DOM->attr("name");
        }
        else {
            $this->DOM->attr("name");
        }
        $tpl=wbFromString($this->DOM->html());
        $tpl->wbSetData();
        $template=$tpl->outerHtml();
        $tplId=wbNewId();
        $this->DOM->after("<script type='text/template' id='{$tplId}'>".htmlspecialchars($template)."</script>");
        $this->DOM->attr("data-wb-tpl","#".$tplId);
        if (isset($Item[$name]) AND is_array($Item[$name])) {
            $this->tagMultiInputSetData($Item[$name]);
        }
        elseif (isset($Item["%".$name]) AND is_array($Item["%".$name])) {
            // dirty fix where multiinput in loop
            $this->tagMultiInputSetData($Item["%".$name]);
        } else {
            $this->tagMultiInputSetData();
        }
	$this->DOM->append('<script data-wb-append="body">wb_include("/engine/tags/multiinput/multiinput.js");</script>');
    }

    public function tagMultiInputSetData($Data=array(0=>array())) {
        $tpl=$this->DOM->html();
        $mtplid=$this->DOM->attr("data-wb-tpl");
        $this->DOM->html("");
        $name=$this->DOM->attr("name");
        foreach($Data as $i => $item) {
            $line=wbFromString($tpl);
            $line->wbSetData($item);
            $inp=$line->find("[data-wb-role=multiinput],input,select,textarea");
            foreach($inp as $tag) {
                $tname=$tag->attr("name");
                $tplid=$tag->attr("data-wb-tpl");
                $newid=str_replace("#","",$mtplid."_".$tname);
                if ($tplid>"" AND $mtplid>"") {
                    if (!$this->DOM->find("script[id={$newid}]")->length) {
                        $line->find("script[id={$tplid}]")->attr("id",$newid);
                        $this->DOM->append($line->find("script[id={$newid}]"));
                    } else {
                        $line->find("script[id={$tplid}]")->remove();
                    }
                    $tag->attr("data-wb-tpl",$newid);
                }
                if ($tname>"") {
			if ($tag->parents("[data-wb-role=multiinput][data-wb-field]")->length) {
				$pos=strpos($tname,"[");
				$tag->attr("name",$name."[{$i}][".substr($tname,0,$pos)."]".substr($tname,$pos));
			} else {
				$tag->attr("name",$name."[{$i}][".$tname."]");
				$tag->attr("data-wb-field",$tname);
			}
                }
            }
            $row=wbGetForm("common","multiinput_row")->outerHtml();
            $this->DOM->append(str_replace("{{template}}",$line->outerHtml(),$row));
            unset($line);
        }
    }



}
