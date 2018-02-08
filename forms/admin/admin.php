<?php

function _adminAfterItemRead($Item, $mode=null)
{
    if ($_ENV["route"]["mode"]=="edit" and $_ENV["route"]["item"]=="settings") {
        $backups=wbListFiles($_ENV["path_app"]."/backup", true);
        $Item["backups"]=array();
        foreach ($backups as $key => $name) {
            $size=sprintf("%u", filesize($_ENV["path_app"]."/backup/".$name) / 1024 / 1024 )."Мб";
            $tmp=explode("-", $name);
            $date=explode(".", $tmp[2]);
            $date=$date[0];
            $date=substr($date, 0, 4)."-".substr($date, 4, 2)."-".substr($date, 6, 2)." ".substr($date, 8, 2).":".substr($date, 10, 2).":".substr($date, 12, 2);
            if ($tmp[1]=="a") {
                $Item["backups"][]=array("type"=>"app+db","size"=>$size,"_created"=>$date,"name"=>$name,"date"=>date("d.m.Y H:i:s", strtotime($date)));
            }
            if ($tmp[1]=="e") {
                $Item["backups"][]=array("type"=>"engine","size"=>$size,"_created"=>$date,"name"=>$name,"date"=>date("d.m.Y H:i:s", strtotime($date)));
            }
            if ($tmp[1]=="u") {
                $Item["backups"][]=array("type"=>"uploads","size"=>$size,"_created"=>$date,"name"=>$name,"date"=>date("d.m.Y H:i:s", strtotime($date)));
            }
        }
    }
    return $Item;
}

function ajax__admin()
{
  if (wbRole("admin")) {
    $_GET["form"]=$_ENV["route"]["form"]="admin";
    $_GET["mode"]=$_ENV["route"]["mode"]=$_ENV["route"]["params"][0];
    $_GET["action"]=$_ENV["route"]["action"]=$_ENV["route"]["params"][1];
    $_GET["item"]=$_ENV["route"]["item"]=$_ENV["route"]["params"][2];
    $out=wbCallFormFunc(ucfirst($_ENV["route"]["params"][0]), $_POST, "admin", $_ENV["route"]["params"][0]);
    return $out;
  }
}

function _adminBackup($Item, $mode)
{
    set_time_limit(600);
    if (!wbRole("admin")) {die;}
    $out="";
    $name=$_ENV["route"]["item"];
    $action=$_ENV["route"]["action"];
    $header="";
    $type=explode("-", $name);
    if ($_ENV["route"]["params"]["confirm"]=="true") {
        $out=wbGetForm("admin", "edit");
        if (isset($type[2])) {
            $date=$type[2];
            $date=substr($date, 0, 4)."-".substr($date, 4, 2)."-".substr($date, 6, 2)." ".substr($date, 8, 2).":".substr($date, 10, 2).":".substr($date, 12, 2);
        }
        $text="";
        $type=$type[1];
        if ($type=="e") {
            $typetext="системы";
        }
        if ($type=="a") {
            $typetext="приложения";
        }
        if ($type=="u") {
            $typetext="загрузок";
        }
        if ($action=="restore") {
            $action_name="Восстановить";
        }
        if ($action=="remove") {
            $action_name="Удалить";
        }
        if ($action=="backup") {
            $action_name="Создать резервную копию";
            $date=date("Y-m-d H:i:s");
            $type="";
        }
        $Item=array(
        "action"=>$_ENV["route"]["action"],
        "action_name"=>$action_name,
        "text"=>$text,
        "typetext"=>$typetext,
        "type"=>$type,
        "name"=>$name,
        "created"=>$date,
        "date"=>date("d.m.Y H:i:s", strtotime($date))
      );
        $out=$out->find("#backup_confirm", 0);
        if ($type=="e" OR $type=="u") {
            $out->find(".checks")->remove();
        }
        $out->wbSetData($Item);
        return $out;
    } else {
        $res="";
        $root=$_ENV["path_app"];
        $engine=$root."/engine";
        if (!isset($_ENV["route"]["params"]["step"])) {
            $step=0;
        } else {
            $step=$_ENV["route"]["params"]["step"];
        }
        if ($action=="restore") {
            if ($type[1]=="e") {
                switch ($step) {
                    case 0:
                      if (is_link($engine)) {
                          $res=array("next"=>"Восстановление резервной копии {$name} невозможно, так как /engine является символьной ссылкой.<br>Обратитесь к администратору.","error"=>1,"count"=>0);
                      } else {
                          $res=array("next"=>"Распаковка резервной копии...<br>{$name}","error"=>0,"count"=>1);
                      }
                          break;
                      case 1:
                      unlink($engine);
                          exec("rm -rf {$engine} && cd {$root}/backup && unzip {$name} -d {$root}");
                          $res=array("next"=>"Восстановление выполнено","error"=>0);
                          break;
                }
            } elseif ($type[1]=="a") {
                switch ($step) {
                    case 0:
                        $res=array("next"=>"Удаление текущей версии...<br>{$name}","error"=>0,"count"=>2);
                        break;
                    case 1:
                        if ($_POST["db"]=="on") {
                            //exec("cd {$root} && rm -rf {$root}/uploads");
                        }
                        if ($_POST["app"]=="on") {
                            // удаляем всё, кроме .htaccess|index.php|engine|database|uploads
                            exec("cd {$root} && find -maxdepth 1 -type f -name '.*' -not -name '.htaccess' -not -name  'engine' -not -name 'index.php' -exec rm -f {} \;");
                            exec("cd {$root} && find -maxdepth 1 -type d -not -name 'backup' -not -name 'engine' -not -name 'database' -not -name 'uploads' -exec rm -rf {} \;");
                        }
                        $res=array("next"=>"Восстановление из резервной копии...<br>{$name}","error"=>0,"count"=>2);
                    break;
                    case 2:
                        if ($_POST["db"]=="on") {
                            // database удаляем именно тут, иначе она создаётся перед восстановлением и мешает распаковке
                            exec("cd {$root} && rm -rf {$root}/database && unzip -q -o ./backup/{$name} database/* uploads/*");
                        }
                        if ($_POST["app"]=="on") {
                            exec("cd {$root} && unzip -q -o backup/{$name} -x 'database/*' 'uploads/*';");
                        }
                        $res=array("next"=>"Восстановление выполнено","error"=>0);
                        break;
                }
            } elseif ($type[1]=="u") {
                switch ($step) {
                    case 0:
                        $res=array("next"=>"Удаление текущей версии...<br>{$name}","error"=>0,"count"=>2);
                        break;
                    case 1:
                        exec("cd {$root} && rm -rf {$root}/uploads");
                        $res=array("next"=>"Восстановление из резервной копии...<br>{$name}","error"=>0,"count"=>2);
                    break;
                    case 2:
                        exec("cd {$root} && unzip -q -o backup/{$name}");
                        $res=array("next"=>"Восстановление выполнено","error"=>0);
                        break;
                }
            }
        } elseif ($action=="backup") {
          if (!is_dir($root."/backup")) {
              @mkdir($root."/backup",0766);
          }
            switch ($step) {
          case 0:
                $res=array("next"=>"Создание резервной копии системы...","error"=>0,"count"=>3);
                break;
          case 1:
                $name="backup-e-".date("YmdHis").".zip";
                exec("cd {$root} && zip -r backup/{$name} engine/ -x '*.git*' -x '*_cache*'");
                $res=array("next"=>"Создание резервной копии приложения...","error"=>0);
                break;
          case 2:
                $name="backup-a-".date("YmdHis").".zip";
                exec("cd {$root} && zip -r backup/{$name} . -x '*engine*' -x '*backup*' -x '*_cache*' -x '*uploads*'");
                $res=array("next"=>"Создание резервной копии загрузок","error"=>0);
                break;
          case 3:
                $name="backup-u-".date("YmdHis").".zip";
                exec("cd {$root} && zip -r backup/{$name} uploads/");
                $res=array("next"=>"Создание резервной копии завершено","error"=>0);
                break;
        }
        } elseif ($action=="remove") {
            switch ($step) {
          case 0:
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
        }
        }
        echo json_encode($res);
        die;
    }
}
