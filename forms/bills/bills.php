<?php
function bills__print() {
    $out=wbGetForm("bills","print");
    $out->wbSetData($_POST);
    return $out;
}
?>