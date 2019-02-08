<?php
function phpmailer__init()
{
    if (isset($_ENV["route"]["params"][0])) {
        $mode=$_ENV["route"]["params"][0];
        $call="phpmailer__{$mode}";
        if (is_callable($call)) {
            echo @$call();
        }
        die;
    } else {
        return phpmailer__checkout();
    }
}

function phpmailer__settings() {
    if (wbRole("admin")) {
        $form=wbFromFile(__DIR__."/phpmailer_settings.php");
        $form->wbSetData($_ENV["settings"]);  // проставляем значения
        return $form->outerHtml();
    }
}

?>
