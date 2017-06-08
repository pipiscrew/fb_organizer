<?php
session_start();

if (!isset($_SESSION["u"])) {
	header("Location: login.php");
	exit ;
}

if ($_SESSION['level']!=9)
	die("You dont have permissions to access this area! Ask administrator for more!");

$active_tab = "users";

if(!isset($_POST['user_level_id']) || !isset($_POST['mail']) || !isset($_POST['password']) || !isset($_POST['fullname']) || !isset($_POST['last_logon'])){
	echo "error010101010";
	return;
}

// include DB
require_once ('config.php');

$db = connect();



$ret_val="";
if(isset($_POST['usersFORM_updateID']) && !empty($_POST['usersFORM_updateID']))
{
	$sql = "UPDATE `users` set user_level_id=:user_level_id, mail=:mail, password=:password, fullname=:fullname, last_logon=:last_logon WHERE user_id=:user_id";
	$stmt = $db->prepare($sql);
	$stmt->bindValue(':user_id', $_POST['usersFORM_updateID']);
	$ret_val = "isupdate";
}
else
{
	$sql = "INSERT INTO `users` (user_level_id, mail, password, fullname, last_logon) VALUES (:user_level_id, :mail, :password, :fullname, :last_logon)";
	$stmt = $db->prepare($sql);
	$ret_val = "isnew";
}

$stmt->bindValue(':user_level_id' , $_POST['user_level_id']);
$stmt->bindValue(':mail' , $_POST['mail']);
$stmt->bindValue(':password' , $_POST['password']);
$stmt->bindValue(':fullname' , $_POST['fullname']);
$stmt->bindValue(':last_logon' , $_POST['last_logon']);

$stmt->execute();

$res = $stmt->rowCount();


if($res == 1)
	header("Location: tab_users.php?$ret_val=1");
else
	header("Location: tab_users.php?iserror=1");

?>