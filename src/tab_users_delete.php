<?php
session_start();

if (!isset($_SESSION["u"])) {
	header("Location: login.php");
	exit ;
}

if ($_SESSION['level']!=9)
	die("You dont have permissions to access this area! Ask administrator for more!");

if (!isset($_GET['id'])) {
	echo "error010101010";
	return;
}

require_once ('config.php');

$db = connect();

$sql = "DELETE FROM `users` WHERE user_id=:user_id";
$sth = $db->prepare($sql);
$sth->bindValue(':user_id', $_GET['id']);
	
$sth->execute();

$g = $sth->rowCount();

if($g == 1)
	header("Location: tab_users.php?isdelete=1");
else
	header("Location: tab_users.php?iserror=1");
?>