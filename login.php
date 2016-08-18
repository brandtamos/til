<?php
$username = $_POST["username"];
$password = $_POST["password"];

$hashedpassword = md5($password);

$m = new MongoClient();
$db = $m->selectDB("til");
$users = $db->selectCollection('users');


$user = $users->findOne(array('username' => $username, 'password' => $hashedpassword));
if($user == NULL){
	echo json_encode(array('response' => 'failure'));
}
else{
	$userId = $user['_id']->{'$id'};
	$userName = $user['username'];
	session_start();
	$_SESSION["user_id"] = $userId;
	$_SESSION["user_name"] = $userName;
	echo json_encode(array('response' => 'success'));
}
?>