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

/////validation
$g = getScalar($db,"select count(client_id) from clients where client_rating_id=?",array($_GET['id']));

if ($g>0)
{	
	header("Location: tab_client_ratings.php?isused=1");
	exit;
}
/////validation

$sql = "DELETE FROM `client_ratings` WHERE client_rating_id=:client_rating_id";
$sth = $db->prepare($sql);
$sth->bindValue(':client_rating_id', $_GET['id']);
	
$sth->execute();

$g = $sth->rowCount();

if($g == 1)
	header("Location: tab_client_ratings.php?isdelete=1");
else
	header("Location: tab_client_ratings.php?iserror=1");
?>