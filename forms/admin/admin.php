<?php

function _adminAfterItemRead($Item, $mode=null)
{
    if (isset($_ENV["route"]) AND $_ENV["route"]["mode"]=="edit" AND $_ENV["route"]["item"]=="settings") {
        if (!isset($Item["path_tpl"])) {$Item["path_tpl"]="/tpl";}
        // ******************** MERCHANTS ******************** //
        if (!isset($Item["merchant"])) {$Item["merchant"]="yapay";}
        $Item["merchants"]=wbMerchantList();
    }
    return $Item;
}



function _adminBeforeItemSave($Item)
{
    if ($Item["id"]=="settings") {
        unset($Item["backups"]);
    }
    if (!is_dir($_ENV["path_app"].$Item["path_tpl"])) {$Item["path_tpl"]="/tpl";}
        if (isset($Item["lang"])) {
                $_SESSION['lang'] = $_ENV["lang"] = $Item["lang"];
        }
    return $Item;
}

function ajax__admin() {
        if (is_callable(__FUNCTION__."_".$_ENV["route"]["params"][0]) AND wbRole("admin")) {
                $call=__FUNCTION__."_".$_ENV["route"]["params"][0];
                return json_encode(@$call());
        }
}


function ajax__admin_clearcache() {
        $dir=null;
        if ($_ENV["route"]["params"][1]=="images") {$dir=$_ENV['path_app']."/uploads/_cache";}
        if ($_ENV["route"]["params"][1]=="texts") {$dir=$_ENV['dbac'];}

        @exec("cd {$dir} && rm -rf *");

        if ($dir!==null) {
                foreach(glob($dir . '/*') as $file) {
                        echo $file."\n";
                    if(is_dir($file))
                        rmdir($file);
                    else
                        unlink($file);
                }
                return true;
        } else {
                return false;
        }
}
