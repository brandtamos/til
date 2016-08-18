<?php
$username = $_POST["username"];
$password = $_POST["password"];

$hashedpassword = md5($password);

$m = new MongoClient();
$db = $m->selectDB("til");
$users = $db->selectCollection('users');

//check to see if username exists
$user = $users->findOne(array('username' => $username));
if($user != NULL){
	echo json_encode(array('response' => 'failure'));
}
else{
	$newUser = array("username" => $username, "password" => $hashedpassword);
	$users->insert($newUser);
	session_start();
	$_SESSION["user_id"] = $newUser['_id'];
	echo json_encode(array('response' => 'success'));
}
?>