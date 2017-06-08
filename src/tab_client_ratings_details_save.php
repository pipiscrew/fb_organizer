<?php
session_start();

if (!isset($_SESSION["u"])) {
	header("Location: login.php");
	exit ;
}

if ($_SESSION['level']!=9)
	die("You dont have permissions to access this area! Ask administrator for more!");
	
$active_tab = "client_ratings";

if(!isset($_POST['client_rating_name'])){
	echo "error010101010";
	return;
}

// include DB
require_once ('config.php');

$db = connect();



$ret_val="";
if(isset($_POST['client_ratingsFORM_updateID']) && !empty($_POST['client_ratingsFORM_updateID']))
{
	$sql = "UPDATE `client_ratings` set client_rating_name=:client_rating_name WHERE client_rating_id=:client_rating_id";
	$stmt = $db->prepare($sql);
	$stmt->bindValue(':client_rating_id', $_POST['client_ratingsFORM_updateID']);
	$ret_val = "isupdate";
}
else
{
	$sql = "INSERT INTO `client_ratings` (client_rating_name) VALUES (:client_rating_name)";
	$stmt = $db->prepare($sql);
	$ret_val = "isnew";
}

$stmt->bindValue(':client_rating_name' , $_POST['client_rating_name']);

$stmt->execute();

$res = $stmt->rowCount();


if($res == 1)
	header("Location: tab_client_ratings.php?$ret_val=1");
else
	header("Location: tab_client_ratings.php?iserror=1");

?>