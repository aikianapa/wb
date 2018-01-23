<?php
function _newsBeforeShowItem($Item) {
    $Item["descr"]=strip_tags($Item["descr"]);
    if ($Item["descr"]=="") {$Item["descr"]=wbGetWords(strip_tags($Item["text"]),40);}
    $Item["day"]=date("d",strtotime($Item["date"]));
    $Item["month"]=date("M",strtotime($Item["date"]));
    $Item["datetime"]=date("d.m.Y H:i",strtotime($Item["date"]));
	if ($_GET["mode"]=="list") {
		$Item["images_count"]=count($Item["images"]);
		unset($Item["text"]);
	}
    return $Item;
}

function _newsAfterItemRead($Item=null) {
    if ($Item!==null) {
        if ($_ENV["route"]["mode"]=="show") {
            if (!isset($Item["title"]) OR $Item["title"]=="") {$Item["title"]=$Item["header"];}
            if ($Item["title"]=="") {$Item["title"]=$_ENV["settings"]["header"];}
        }
    }
	return $Item;
}
?>
