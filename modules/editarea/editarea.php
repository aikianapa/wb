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
$attrlist=$obj->attrlist();
$sc2=$out->find("script:not([src])");
$ta=$out->find("textarea");
$ta->attr("id");
//$sc2->prepend("var id='{$attrlist["taid"]}';");
$data=array(
	 "_id"=>$attrlist["taid"]
	,"_value"=>$Item["value"]
);
$out->wbSetData($data);
return $out;


}
?>
