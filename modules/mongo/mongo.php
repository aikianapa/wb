<?php
require 'vendor/autoload.php';
use MongoDB\Client;

function mongoItemRead($connect,$table,$id) {
    $client = new Client($connect);
    $collection = $client->dot->cities;
    $query = ['id' => $id];
    $options = [];
    $cursor = $collection->find($query, $options)->toArray();
    $res=null;
    foreach ($cursor as $document) {
        $res=(array)$document;
        break;
    }
    return $res;
}

function mongoItemSave($connect,$table,$item) {
	$res = false;
	if (!isset($item["_id"]) AND isset($item["id"])) $item["_id"]=$item["id"];
	if (!isset($item["_id"]) OR $item["_id"]=="") return $res;
	if (is_string($connect)) $connect = new MongoDB\Driver\Manager($connect);
	$prev = mongoItemRead($connect,$table,$item["_id"]);
	if (!$prev) {$prev=["_id"=>$item["_id"]];}
	$bulk = new MongoDB\Driver\BulkWrite;
	if (!isset($item["_created"])) $item["_created"]=date("Y-m-d H:i:s");
	$item["_updated"]=date("Y-m-d H:i:s");
	$item=array_merge($prev,$item);
	ksort($item);
	$option = array("upsert" => true);
	$bulk->update(["_id"=>$item["_id"]],$item,$option);
	$res = $connect->executeBulkWrite($table, $bulk);
	return $res;
}

function mongoItemRemove($connect,$table,$id) {
	$res = false;
	if (is_string($connect)) $connect = new MongoDB\Driver\Manager($connect);
	$bulk = new MongoDB\Driver\BulkWrite;
	if (is_string($id)) {
		$bulk->delete(['_id' => $id], ['limit' => 1]);
	} else if (is_array($id)) {
		$bulk->delete($id);
	}
	$res = $connect->executeBulkWrite($table, $bulk);
	return $res;
}

function mongoItemList($connect,$table,$query=[],$options=[]) {
    $client = new Client($connect);
    $collection = $client->dot->cities;
    $cursor = $collection->find($query, $options)->toArray();
    $res=[];
    foreach ($cursor as $document) {
        $res[]=(array)$document;
    }
    return $res;
}
?>
