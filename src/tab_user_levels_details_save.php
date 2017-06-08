<?php
session_start();

if (!isset($_SESSION["u"])) {
	header("Location: login.php");
	exit ;
}

if ($_SESSION['level']!=9)
	die("You dont have permissions to access this area! Ask administrator for more!");

$active_tab = "user_levels";

if(!isset($_POST['user_level_name'])){
	echo "error010101010";
	return;
}

// include DB
require_once ('config.php');

$db = connect();



$ret_val="";
if(isset($_POST['user_levelsFORM_updateID']) && !empty($_POST['user_levelsFORM_updateID']))
{
	$sql = "UPDATE `user_levels` set user_level_name=:user_level_name WHERE user_level_id=:user_level_id";
	$stmt = $db->prepare($sql);
	$stmt->bindValue(':user_level_id', $_POST['user_levelsFORM_updateID']);
	$ret_val = "isupdate";
}
else
{
	$sql = "INSERT INTO `user_levels` (user_level_name) VALUES (:user_level_name)";
	$stmt = $db->prepare($sql);
	$ret_val = "isnew";
}

$stmt->bindValue(':user_level_name' , $_POST['user_level_name']);

$stmt->execute();

$res = $stmt->rowCount();


if($res == 1)
	header("Location: tab_user_levels.php?$ret_val=1");
else
	header("Location: tab_user_levels.php?iserror=1");

?>