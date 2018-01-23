<?php

function _adminAfterItemRead($Item,$mode=NULL) {
  if ($_ENV["route"]["mode"]=="edit" AND $_ENV["route"]["item"]=="settings") {
    $backups=wbListFiles($_ENV["path_app"]."/backup",true);
    $Item["backups"]=array();
    foreach($backups as $key => $name) {
      $tmp=explode("-",$name);
      $date=explode(".",$tmp[2]); $date=$date[0];
      $date=substr($date,0,4)."-".substr($date,4,2)."-".substr($date,6,2)." ".substr($date,8,2).":".substr($date,10,2).":".substr($date,12,2);
      if ($tmp[1]=="a") {$Item["backups"][]=array("type"=>"app","created"=>$date,"name"=>$name,"date"=>date("d.m.Y H:i:s",strtotime($date)));}
      if ($tmp[1]=="e") {$Item["backups"][]=array("type"=>"engine","created"=>$date,"name"=>$name,"date"=>date("d.m.Y H:i:s",strtotime($date)));}
    }
  }
  return $Item;
}

function ajax__admin() {
   $_GET["form"]=$_ENV["route"]["form"]="admin";
   $_GET["mode"]=$_ENV["route"]["mode"]=$_ENV["route"]["params"][0];
   $_GET["action"]=$_ENV["route"]["action"]=$_ENV["route"]["params"][1];
   $_GET["item"]=$_ENV["route"]["item"]=$_ENV["route"]["params"][2];
   $out=wbCallFormFunc(ucfirst($_ENV["route"]["params"][0]),$_POST,"admin",$_ENV["route"]["params"][0]);
   return $out;
}

function _adminBackup($Item,$mode) {
  if ($_SESSION["user_role"]!=="admin") {return; die;}
  $out="";
  $name=$_ENV["route"]["item"];
  $action=$_ENV["route"]["action"];
  $header="";
  if ($_ENV["route"]["params"]["confirm"]=="true") {
      $out=wbGetForm("admin","edit");
      $type=explode("-",$name);
      if ($type[1]=="e") {$type="системы";}
      if ($type[1]=="a") {$type="приложения";}
      if ($action=="restore") {$action_name="Восстановить";}
      if ($action=="remove") {$action_name="Удалить";}
      if ($action=="backup") {
        $action_name="Резервная копия";
        $_ENV["route"]["params"]["date"]=date("Y-m-d H-i:s");
        $type="";
      }
      $Item=array(
        "action"=>$_ENV["route"]["action"],
        "action_name"=>$action_name,
        "type"=>$type,
        "name"=>$name,
        "created"=>$_ENV["route"]["params"]["date"],
        "date"=>date("d.m.Y H:i:s",strtotime($_ENV["route"]["params"]["date"]))
      );
      $out=$out->find("#backup_confirm",0);
      $out->wbSetData($Item);
      return $out;
  } else {
      $res="";
      if (!isset($_ENV["route"]["params"]["step"])) {$step=0;} else {$step=$_ENV["route"]["params"]["step"];}
      if ($action=="restore") {

      }
      if ($action=="backup") {
        $root=$_ENV["path_app"];
        switch($step) {
          default:
        		$res=array("next"=>"Создание резервной копии системы, ждите...","error"=>0,"count"=>2);
        		break;
          case 1:
            if (!is_dir($root."/backup")) {@mkdir($root."/backup");}
        		$name="backup-e-".date("YmdHis").".zip";
        		exec("cd {$root} && zip -r backup/{$name} engine/ -x '*.git*'");
        		$res=array("next"=>"Создание резервной копии приложения","error"=>0);
        		break;
          case 2:
        		if (!is_dir($root."/backup")) {@mkdir($root."/backup");}
        		$name="backup-a-".date("YmdHis").".zip";
        		exec("cd {$root} && zip -r backup/{$name} . -x '*engine*' -x '*backup*'");
        		$res=array("next"=>"Завершение","error"=>0);
        		break;
        }
      }
      if ($action=="remove") {
        switch($step) {
          default:
        		$res=array("next"=>"Удаление {$name}, ждите...","error"=>0,"count"=>1);
        		break;
          case 1:
            exec("cd {$_ENV['path_app']}/backup && rm {$name}");
            if (is_file("{$_ENV['path_app']}/backup/{$name}")) {
                $res=array("next"=>"Ошибка, не удалось удалить {$name}","error"=>1);
            } else {
                $res=array("next"=>"Файл {$name} успешно удалён","error"=>0);
            }
            break;
          case 2:
            $res=array("next"=>"Файл {$name} успешно удалён","error"=>0);
            break;
        }
      }
      echo json_encode($res);
      die;
    }
}
?>
