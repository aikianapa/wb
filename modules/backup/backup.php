<?php
function backup__init()
{
    if (isset($_ENV["route"]["params"][0])) {
        $mode=$_ENV["route"]["params"][0];
        $call="backup__{$mode}";
        if (is_callable($call)) {
            $out=@$call();
       }
    } else {
	$out=backup__ui();
    }
    return $out;
}

function backup__ui() {
	$out=wbFromFile(__DIR__ ."/backup_ui.php",true);
        $backups=wbListFiles($_ENV["path_app"]."/backup", true);
        $dir=str_replace($_ENV["path_system"],"/engine",__DIR__);
        $dir=str_replace($_ENV["path_app"],"",$dir);
	$js=$out->find('script[src*="backup.js"]');
	if (is_object($js)) {$js->attr("src",$dir."/backup.js");}
        foreach ($backups as $key => $name) {
            $size=sprintf("%u", filesize($_ENV["path_app"]."/backup/".$name) / 1024 / 1024 )."Мб";
            $tmp=explode("-", $name);
            $date=explode(".", $tmp[2]);
            $date=$date[0];
            $date=substr($date, 0, 4)."-".substr($date, 4, 2)."-".substr($date, 6, 2)." ".substr($date, 8, 2).":".substr($date, 10, 2).":".substr($date, 12, 2);
            if ($tmp[1]=="a") {
                $Item["backups"][]=array("type"=>"application","size"=>$size,"_created"=>$date,"name"=>$name,"date"=>date("d.m.Y H:i:s", strtotime($date)));
            }
            if ($tmp[1]=="d") {
                $Item["backups"][]=array("type"=>"database","size"=>$size,"_created"=>$date,"name"=>$name,"date"=>date("d.m.Y H:i:s", strtotime($date)));
            }
            if ($tmp[1]=="e") {
                $Item["backups"][]=array("type"=>"engine","size"=>$size,"_created"=>$date,"name"=>$name,"date"=>date("d.m.Y H:i:s", strtotime($date)));
            }
            if ($tmp[1]=="u") {
                $Item["backups"][]=array("type"=>"uploads","size"=>$size,"_created"=>$date,"name"=>$name,"date"=>date("d.m.Y H:i:s", strtotime($date)));
            }
        }
	$out->wbSetData($Item);
	return $out;
}

function backup__list() {
        $list=array();
        $backups=wbListFiles($_ENV["path_app"]."/backup", true);
        foreach ($backups as $key => $name) {
            $size=sprintf("%u", filesize($_ENV["path_app"]."/backup/".$name) / 1024 / 1024 )."Мб";
            $tmp=explode("-", $name);
            $date=explode(".", $tmp[2]);
            $date=$date[0];
            $date=substr($date, 0, 4)."-".substr($date, 4, 2)."-".substr($date, 6, 2)." ".substr($date, 8, 2).":".substr($date, 10, 2).":".substr($date, 12, 2);
            if ($tmp[1]=="a") {
                $list[]=array("type"=>"application","size"=>$size,"_created"=>$date,"name"=>$name,"date"=>date("d.m.Y H:i:s", strtotime($date)));
            }
            if ($tmp[1]=="d") {
                $list[]=array("type"=>"database","size"=>$size,"_created"=>$date,"name"=>$name,"date"=>date("d.m.Y H:i:s", strtotime($date)));
            }
            if ($tmp[1]=="e") {
                $list[]=array("type"=>"engine","size"=>$size,"_created"=>$date,"name"=>$name,"date"=>date("d.m.Y H:i:s", strtotime($date)));
            }
            if ($tmp[1]=="u") {
                $list[]=array("type"=>"uploads","size"=>$size,"_created"=>$date,"name"=>$name,"date"=>date("d.m.Y H:i:s", strtotime($date)));
            }
        }
        return $list;
}


function ajax__backup()
{
  if (wbRole("admin")) {
    $_GET["form"]=$_ENV["route"]["form"]="admin";
    $_GET["mode"]=$_ENV["route"]["mode"]=$_ENV["route"]["params"][0];
    $_GET["action"]=$_ENV["route"]["action"]=$_ENV["route"]["params"][1];
    $_GET["item"]=$_ENV["route"]["item"]=$_ENV["route"]["params"][2];
    $out=wbCallFormFunc(ucfirst($_ENV["route"]["params"][0]), $_POST, "backup", $_ENV["route"]["params"][0]);
    return $out;
  }
}

function _backupBackup($Item, $mode)
{
	set_time_limit(999999);
	$out=wbFromFile(__DIR__ ."/backup_ui.php",true);
	$_LANG=$out->wbGetFormLocale();
	$_LANG=$_LANG[$_SESSION["lang"]];
	if (!wbRole("admin")) {die;}
    $name=$_ENV["route"]["item"];
    $action=$_ENV["route"]["action"];
    $header="";
    $type=explode("-", $name);
    if ($_ENV["route"]["params"]["confirm"]=="true") {
        if (isset($type[2])) {
            $date=$type[2];
            $date=substr($date, 0, 4)."-".substr($date, 4, 2)."-".substr($date, 6, 2)." ".substr($date, 8, 2).":".substr($date, 10, 2).":".substr($date, 12, 2);
        }
        $text="";
        $type=$type[1];
        $typetext=$_LANG["type_".$type];
        $action_name = $_LANG["btn_".$action];
        if ($action=="backup") {
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
                          $res=array("next"=>"{$_LANG['restore_from']} {$name} {$_LANG['restore_error']}","error"=>1,"count"=>0);
                      } else {
                          $res=array("next"=>"{$_LANG['backup_unzip']}, {$_LANG['wait']}...<br>{$name}","error"=>0,"count"=>1);
                      }
                          break;
                      case 1:
                      unlink($engine);
                          exec("rm -rf {$engine} && cd {$root}/backup && unzip {$name} -d {$root}  && chmod -R 0777 {$engine}");
                          $res=array("next"=>"{$_LANG['restore_complete']}","error"=>0);
                          break;
                }
            } elseif ($type[1]=="d") {
                switch ($step) {
                    case 0:
                        $res=array("next"=>"{$_LANG['remove_current']}, {$_LANG['wait']}...<br>{$name}","error"=>0,"count"=>2);
                        break;
                    case 1:
                        exec("cd {$root} && rm -rf {$root}/database");
                        $res=array("next"=>"{$_LANG['restore_from']}, {$_LANG['wait']}...<br>{$name}","error"=>0,"count"=>2);
			break;
                    case 2:
                        exec("cd {$root} && unzip -q -o backup/{$name} && chmod -R 0777 ./database");
                        $res=array("next"=>"{$_LANG['restore_complete']}","error"=>0);
                        break;
                }

            } elseif ($type[1]=="a") {
                switch ($step) {
                    case 0:
                        $res=array("next"=>"{$_LANG['remove_current']}, {$_LANG['wait']}...<br>{$name}","error"=>0,"count"=>2);
                        break;
                    case 1:
                        // remove all, except .htaccess|index.php|engine|database|uploads
                        exec("cd {$root} && find -maxdepth 1 -type f -name '.*' -not -name '.htaccess' -not -name  'engine' -not -name 'index.php' -exec rm -f {} \;");
                        exec("cd {$root} && find -maxdepth 1 -type d -not -name 'backup' -not -name 'engine' -not -name 'database' -not -name 'uploads' -exec rm -rf {} \;");
                        $res=array("next"=>"{$_LANG['restore_from']}, {$_LANG['wait']}...<br>{$name}","error"=>0,"count"=>2);
			break;
                    case 2:
                        exec("cd {$root} && unzip -q -o backup/{$name} -x 'database/*' 'uploads/*';  && chmod -R 0777 ./*");
                        $res=array("next"=>"{$_LANG['restore_complete']}","error"=>0);
                        break;
                }
            } elseif ($type[1]=="u") {
                switch ($step) {
                    case 0:
                        $res=array("next"=>"{$_LANG['remove_current']}, {$_LANG['wait']}...<br>{$name}","error"=>0,"count"=>2);
                        break;
                    case 1:
                        exec("cd {$root} && rm -rf {$root}/uploads");
                        $res=array("next"=>"{$_LANG['restore_from']}, {$_LANG['wait']}...<br>{$name}","error"=>0,"count"=>2);
			break;
                    case 2:
                        exec("cd {$root} && unzip -q -o backup/{$name}  && chmod -R 0777 {$root}/uploads");
                        $res=array("next"=>"{$_LANG['restore_complete']}","error"=>0);
                        break;
                }
            }
        } elseif ($action=="backup") {
          if (!is_dir($root."/backup")) {
		$u=umask();
		@mkdir($root."/backup",0766);
		umask($u);
	  }
          $options=$_POST["options"];
            switch ($step) {
          case 0:
                $res=array("next"=>"{$_LANG["backup"]} {$_LANG["type_e"]}, {$_LANG['wait']}...","error"=>0,"count"=>4,"options"=>$options);
                break;
          case 1:
                $name="backup-e-".date("YmdHis").".zip";
                if ($options["engine"]=="on") {
			exec("cd {$root} && zip -r backup/{$name} engine/ -x '*.git*' -x '*_cache*'");
		}
                $res=array("next"=>"{$_LANG["backup"]} {$_LANG["type_a"]}, {$_LANG['wait']}...","error"=>0,"options"=>$options);
                break;
          case 2:
                $name="backup-a-".date("YmdHis").".zip";
                if ($options["app"]=="on") {
			exec("cd {$root} && zip -r backup/{$name} . -x '*engine*' -x '*backup*' -x '*_cache*' -x '*database*' -x '*uploads*'");
		}
                $res=array("next"=>"{$_LANG["backup"]} {$_LANG["type_d"]}, {$_LANG['wait']}","error"=>0,"options"=>$options);
                break;
          case 3:
		$name="backup-d-".date("YmdHis").".zip";
                if ($options["db"]=="on") {
			exec("cd {$root} && zip -r backup/{$name} database/");
		}
		$res=array("next"=>"{$_LANG["backup"]} {$_LANG["type_u"]}, {$_LANG['wait']}","error"=>0,"options"=>$options);
		break;
          case 4:
                $name="backup-u-".date("YmdHis").".zip";
                if ($options["upl"]=="on") {
			exec("cd {$root} && zip -r backup/{$name} uploads/");
		}
                $res=array("next"=>"{$_LANG["backup_complete"]}","error"=>0,"options"=>$options);
                break;
        }
        exec("cd {$root} && chmod -R 0777 ./backup");
        } elseif ($action=="remove") {
            switch ($step) {
          case 0:
                $res=array("next"=>"{$_LANG['removing']} {$name}, {$_LANG['wait']}...","error"=>0,"count"=>1);
                break;
          case 1:
            exec("cd {$_ENV['path_app']}/backup && rm {$name}");
            if (is_file("{$_ENV['path_app']}/backup/{$name}")) {
                $res=array("next"=>"{$_LANG['remove_error']} {$name}","error"=>1);
            } else {
                $res=array("next"=>"{$_LANG['remove_complete']}: {$name}","error"=>0);
            }
            break;
        }
        }
        echo json_encode($res);
        die;
    }
}


?>
