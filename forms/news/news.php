<?php
function _newsBeforeShowItem($Item) {
    $Item["textshort"]=wbGetWords(strip_tags($Item["text"]),40);
    $Item["day"]=date("d",strtotime($Item["date"]));
    $Item["month"]=date("M",strtotime($Item["date"]));
    $Item["datetime"]=date("d.m.Y H:i",strtotime($Item["date"]));
    $Item["date"]=date("d.m.Y",strtotime($Item["date"]));
	if ($_GET["mode"]=="list") {
		$Item["images_count"]=count($Item["images"]);
		unset($Item["text"]);
	}
    return $Item;
}

?>