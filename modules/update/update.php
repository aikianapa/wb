<?php
function update__init()
{
    if (isset($_ENV["route"]["params"][0])) {
        $mode=$_ENV["route"]["params"][0];
        $call="update__{$mode}";
        if (is_callable($call)) {
            $out=@$call();
       }
    } else {
	$out=update__ui();
    }
    return $out;
}

function update__ui() {
	$out=wbFromFile(__DIR__ ."/update_ui.php",true);
        $updates=wbListFiles($_ENV["path_app"]."/update", true);
        $dir=str_replace($_ENV["path_system"],"/engine",__DIR__);
        $dir=str_replace($_ENV["path_app"],"",$dir);
	$js=$out->find('script[src*="update.js"]');
	if (is_object($js)) {$js->attr("src",$dir."/update.js");}
	$out->wbSetData($Item);
	return $out;
}

function update__step() {
	if (!isset($_ENV["route"]["params"][1])) return false;
	$step=$_ENV["route"]["params"][1];
	set_time_limit(600000);
	$root=$_SERVER['DOCUMENT_ROOT'];
	$engine="{$root}/engine";
	if (!is_dir($root."/backup")) {@mkdir($root."/backup",0766);}
	switch ($step) {
		default:
			$res=array("next"=>".step0","error"=>0,"count"=>7);
			break;
		case 1:
			exec("cd {$root} && wget https://codeload.github.com/aikianapa/wb/zip/master && mv master master.zip && chmod 0777 master.zip");

			$res=array("next"=>".step".$step,"error"=>0);
			if (is_file("{$root}/master.zip")) {$res["error"]=false;} else {$res["error"]=true;}
			break;
		case 2:
			$name="backup-e-".date("YmdHis").".zip";
			exec("cd {$root} && zip -r backup/{$name} engine/ -x '*.git*'");

			$res=array("next"=>".step".$step,"error"=>0);
			break;
		case 3:
			$name="backup-a-".date("YmdHis").".zip";
			exec("cd {$root} && zip -r backup/{$name} . -x '*engine*' -x '*backup*' -x '*uploads*'");

			$res=array("next"=>".step".$step,"error"=>0);
			break;
		case 4:
			$name="backup-u-".date("YmdHis").".zip";
			exec("cd {$root} && zip -r backup/{$name} uploads/ ");

			$res=array("next"=>".step".$step,"error"=>0);
			break;
		case 5:
			exec("cd {$root} && unzip -q master.zip &&	rm master.zip");

			$res=array("next"=>".step".$step,"error"=>0);
			if (is_dir("{$root}/wb-master")) {$res["error"]=false;} else {$res["error"]=true;}
			break;
		case 6:
			$res=array("next"=>".step".$step,"error"=>0);

			exec("cd {$root} && rm -rf {$engine}/!(update.php) && rm -rf {$engine}/.*");
			break;
		case 7:

			wbRecurseCopy($root."/wb-master",$engine);
			exec("cd {$root} && rm -R wb-master && rm master.zip");
			$res=array("next"=>".step".$step,"error"=>0);
			break;
	}
	return json_encode($res);
}
