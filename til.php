<?php
header('Content-type: application/json');
session_start();

$userId = '';
if(isset($_SESSION['user_id'])){
	$userId = $_SESSION['user_id'];
}
else{
	header("Location: /til/login.html");
}

$m = new MongoClient();
$db = $m->selectDB("til");
$entries = $db->selectCollection('entries');

$request_body = file_get_contents('php://input');
$data = json_decode($request_body);


if(isset($data->operation)){
	$op = $data->operation;
	switch ($op){
		case "get_recent_entries":
			//get 10 most recent entries
			$result = get_recent_entries();
			echo json_encode(array('response' => json_encode(iterator_to_array($result))));
			break;
		case "add_entry":
			$text = $data->payload;
			add_entry($text);
			echo json_encode(array('response' => 'success'));
			break;
		default:
			break;
	}
}

function getwords($key){
	global $m, $db, $chains;
	$query = array('key' => $key);
	$result = $chains->findOne($query);
	
	//if no key matches, return a random entry
	if($result == null){
		return null;
	}
	
	//pick a random word from the word list to return
	$wordcount = count($result['words']);
	$randomnum = rand(0, $wordcount-1);
	return $result['words'][$randomnum];
}
function get_recent_entries(){
	global $entries, $userId;
	$result = $entries->find(array('userid' => $userId),array('text' => true, 'date' => true, '_id' => false))->sort(array("date" => -1));
	//$result = $entries->find(array(),array('text' => true, 'date' => true));
	return $result;
}
function add_entry($text){
	global $entries, $userId;
	$entries->insert(array("userid" => $userId, "text" => $text, "date" => new MongoDate()));
}



?>