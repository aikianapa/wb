<?php
function ajax__todo() {
	$res=false;
	if (isset($_ENV["route"]["params"][0])) {
		$call = __FUNCTION__ ."_".$_ENV["route"]["params"][0];
		if (is_callable($call)) $res=$call();
		wbTableFlush(wbTable("todo"));
	}
	return json_encode($res);
}

function ajax__todo_add() {
	$Item=array(
		"id"		=> wbNewId(),
		"form"		=> "todo",
		"user"		=> $_SESSION["user_id"],
		"task"		=> $_POST["task"],
		"status"	=> $_POST["status"],
		"done"		=> $_POST["done"],
		"time"		=> "",
		"created"	=> date("Y-m-d H:i:s")
	);
	$todo=wbTable("todo");
	$res=wbItemSave($todo,$Item);
	if ($res) {$res=array("id"=>$Item["id"]);}
	return $res;
}


function ajax__todo_upd() {
	$res=false;
	$todo=wbTable("todo");
	$Item=wbItemRead($todo,$_POST["id"]);
	if ($Item["user"]==$_SESSION["user_id"]) {
		foreach($_POST as $key => $val) {	$Item[$key]=$_POST[$key];	}
		$res=wbItemSave($todo,$Item);
		if ($res) {$res=true;}
	}
	return $res;
}

function ajax__todo_counter() {
	$where='user = "'.$_SESSION["user_id"].'"';
	if (isset($_GET["status"])) {$where.=' AND status = "'+$_GET["status"]+'"';}
	$list=wbListItems(wbTable("todo"),$where);
	$res=count($list["result"]);
	unset($list,$item);
	return $res;
}


function ajax__todo_getitem() {
	$res=false;
	$Item=wbItemRead(wbTable("todo"),$_POST["id"]);
	if ($Item["user"]==$_SESSION["user_id"]) {$res=$Item;}
	return $res;
}

function ajax__todo_getitemhtml() {
	$res=false;
	$todo=wbTable("todo");
	$Item=wbItemRead($todo,$_POST["id"]);
	if (is_callable("todoBeforeShowItem")) {$Item=todoBeforeShowItem($Item);}
	if ($Item["user"]==$_SESSION["user_id"]) {
		$tpl=wbGetForm("todo","list");
		$tpl=$tpl->find(".todo-list",0)->html();
		$out=wbFromString("<div></div>");
		$out->find("div")->append($tpl);
		$out->wbSetData($Item);
		echo $out->find("div")->html();
		unset($tpl,$out);
		die;
	}
	return $res;
}

function ajax__todo_getlist() {
	$where='user = "'.$_SESSION["user_id"].'" AND category="'.$_GET["category"].'"';
	$tpl=aikiGetForm("todo","list");
	$tpl=$tpl->find(".task-list",0)->clone();
	$tpl->removeClass("loaded");
	$tpl->attr("where",$where);

	$out=aikiFromString("<div></div>");
	$out->find("div")->append($tpl);
	$out->contentSetData($list);
	echo $out->find("div")->html();
	unset($tpl,$out);
	die;
}



function ajax__todo_generate() {
	$res=false;



	for ($i=1; $i<5000; $i++) {

	$Item=array(
		"id"		=> newIdRnd(),
		"form"		=> "todo",
		"user"		=> $_SESSION["user_id"],
		"task"		=> "Абра швабра кадабра ".$i,
		"status"	=> "default",
		"done"		=> "",
		"time"		=> date("Y-m-d H:i:s")
	);
	aikiSaveItem("todo",$Item);
	}



	return $res;
}


function ajax__todo_del() {
	$todo=wbTable("todo");
	$res=wbItemRemove($todo,$_POST["id"]);
	wbTableFlush($todo);
	return $res;
}

function todoBeforeShowItem($Item) {
	if ($Item["time"]>"") {$Item["timeview"]=date("d.m.y H:i",strtotime($Item["time"]));}
	return $Item;
}

function todoAfterItemRead($Item) {
	if ($Item["time"]=="") {$time=date("Y-m-d H:i",strtotime($Item["created"]));} else {$time=$Item["time"];}
	$time=date("Y-m-d H:i:s",strtotime($time));
	$now=date("Y-m-d H:i:s");
	$now_mi_3=date("Y-m-d H:i:s",strtotime('-3 hour'));
	$now_pl_3=date("Y-m-d H:i:s",strtotime('+3 hour'));
	$status="warn";
	if ($now<=$time) {$status="success";}
	if ($time<=$now_pl_3 && $Item["time"]!=="") {$status="danger";}
	if ($Item["done"]=="1") {$status="muted";}
	$Item["status"]=$status;
	if ($Item["time"]=="") {$Item["time"]=$time;}
	$Item["time"]=$time=date("Y-m-d H:i:s",strtotime($time));
	if (isset($_POST["data"]["status"])) $Item["status"]=$_POST["data"]["status"];
	return $Item;
}

?>
