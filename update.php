<?php
set_time_limit(60000);
$root=$_SERVER['DOCUMENT_ROOT'];
$step=$_REQUEST["step"];
$engine="{$root}/engine";
if (!is_dir($root."/backup")) {@mkdir($root."/backup",0766);}
switch ($step) {
	default:
		$res=array("next"=>"Получение актуальной версии Web Basic Engine","error"=>0,"count"=>7);
		break;
	case 1:
		exec("cd {$root} && wget https://codeload.github.com/aikianapa/wb/zip/master && mv master master.zip && chmod 0777 master.zip");
		$res=array("next"=>"Создание резервной копии системы");
		if (is_file("{$root}/master.zip")) {$res["error"]=false;} else {$res["error"]=true;}
		break;
	case 2:
		$name="backup-e-".date("YmdHis").".zip";
		exec("cd {$root} && zip -r backup/{$name} engine/ -x '*.git*'");
		$res=array("next"=>"Создание резервной копии приложения","error"=>0);
		break;
	case 3:
		$name="backup-a-".date("YmdHis").".zip";
		exec("cd {$root} && zip -r backup/{$name} . -x '*engine*' -x '*backup*' -x '*uploads*'");
		$res=array("next"=>"Создание резервной копии загрузок","error"=>0);
		break;
	case 4:
		$name="backup-u-".date("YmdHis").".zip";
		exec("cd {$root} && zip -r backup/{$name} uploads/ ");
		$res=array("next"=>"Распаковка архива","error"=>0);
		break;
	case 5:
		exec("cd {$root} && unzip -q master.zip &&	rm master.zip");
		$res=array("next"=>"Удаление предыдущей версии");
		if (is_dir("{$root}/wb-master")) {$res["error"]=false;} else {$res["error"]=true;}
		break;
	case 6:
		$res=array("next"=>"Обновление системы","error"=>0);
		exec("cd {$root} && rm -rf {$engine}/!(update.php) && rm -rf {$engine}/.*");
		break;
	case 7:
		recurse_copy($root."/wb-master",$engine);
		exec("cd {$root} && rm -R wb-master && rm master.zip");
        $res=array("next"=>"Обновление выполнено","error"=>0);
		break;
}
echo json_encode($res);

function recurse_copy($src,$dst) {
    $dir = opendir($src);
    @mkdir($dst); @chmod($dst,0777);
    while(false !== ( $file = readdir($dir)) ) {
        if (( $file != '.' ) && ( $file != '..' )) {
            if ( is_dir($src . '/' . $file) ) {
                recurse_copy($src.'/'. $file , $dst.'/'.$file);
            } else {
				copy($src.'/'.$file , $dst.'/'.$file);
				@chmod($dst.'/'.$file,0766);
			}
        }
    }
    closedir($dir);
}
?>
