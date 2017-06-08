<?php
session_start();

if (!isset($_SESSION["u"])) {
	header("Location: login.php");
	exit ;
}

if ($_SESSION['level']!=9)
	die("You dont have permissions to access this area! Ask administrator for more!");
	
$active_tab = "client_sources";

if(!isset($_POST['client_source_name'])){
	echo "error010101010";
	return;
}

// include DB
require_once ('config.php');

$db = connect();



$ret_val="";
if(isset($_POST['client_sourcesFORM_updateID']) && !empty($_POST['client_sourcesFORM_updateID']))
{
	$sql = "UPDATE `client_sources` set client_source_name=:client_source_name WHERE client_source_id=:client_source_id";
	$stmt = $db->prepare($sql);
	$stmt->bindValue(':client_source_id', $_POST['client_sourcesFORM_updateID']);
	$ret_val = "isupdate";
}
else
{
	$sql = "INSERT INTO `client_sources` (client_source_name) VALUES (:client_source_name)";
	$stmt = $db->prepare($sql);
	$ret_val = "isnew";
}

$stmt->bindValue(':client_source_name' , $_POST['client_source_name']);

$stmt->execute();

$res = $stmt->rowCount();


if($res == 1)
	header("Location: tab_client_sources.php?$ret_val=1");
else
	header("Location: tab_client_sources.php?iserror=1");

?>