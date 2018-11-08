<?php
function editarea__init()
{
    if (isset($_ENV["route"]["params"][0])) {
        $mode=$_ENV["route"]["params"][0];
        $call="editarea__{$mode}";
        if (is_callable($call)) {
            $out=@$call();
        }
        die;
    } else {
        $out=file_get_contents(__DIR__ ."/editarea_ui.php");
        return $out;
    }
}

function editarea__afterRead($out,$Item,$obj) {
        $_attributes=$obj->attributes();
        if ($_attributes) {
                $attrs=new IteratorIterator($_attributes);
                foreach ($attrs as $attr) {
                        $tmp=$attr->name;
                        if (strpos($tmp,"ata-wb-")) {$tmp=str_replace("data-wb-","",$tmp); $$tmp=$attr->value()."";} else {
                                $$tmp=$attr->value();
                        }
                }; unset($attrs);
        }

        $cr=$out->find(".mod_editarea",0);
        $sc=$out->find("script",0);
        $ta=$cr->find("textarea");
        $in=$cr->find("input",0);

        if (!isset($taid))              {$taid=wbNewId();}
        if (!isset($height))            {$height=200;}
        if (!isset($name))              {$name="text";}
        if (!isset($value))             {$value="";}
        if (isset($Item["value"]))      {$value=$Item["value"];}
        if (isset($toolbar) AND $toolbar=="false") {
                $out->find(".mod_editarea_toolbar")->remove();
        }

        $cr->attr("id",   "ed_".$taid);
        $ta->attr("id",         $taid);
        $ta->attr("height",   $height);
        $ta->attr("name",       $name);

        $in->attr("id",   "inp".$taid);
        $in->attr("name",       $name);



        if (isset($class)) {$class=str_replace("wb-attrs","",$class); $ta->attr("class",$class);}
        if (isset($style)) {$ta->attr("style",$style);}
        if (isset($value)) {$ta->html($value);}

        $sc->append("$(document).ready(function(){module_editarea('{$taid}');});");
        return $out;
}
?>
