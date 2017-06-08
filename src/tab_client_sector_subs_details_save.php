<?php
session_start();

if (!isset($_SESSION["u"])) {
	header("Location: login.php");
	exit ;
}

$active_tab = "client_sector_subs";

if(!isset($_POST['client_sector_sub_name']) || !isset($_POST['client_sector_id'])){
	echo "error010101010";
	return;
}

// include DB
require_once ('config.php');

$db = connect();



$ret_val="";
if(isset($_POST['client_sector_subsFORM_updateID']) && !empty($_POST['client_sector_subsFORM_updateID']))
{
	$sql = "UPDATE `client_sector_subs` set client_sector_sub_name=:client_sector_sub_name, client_sector_id=:client_sector_id WHERE client_sector_sub_id=:client_sector_sub_id";
	$stmt = $db->prepare($sql);
	$stmt->bindValue(':client_sector_sub_id', $_POST['client_sector_subsFORM_updateID']);
	$ret_val = "isupdate";
}
else
{
	$sql = "INSERT INTO `client_sector_subs` (client_sector_sub_name, client_sector_id) VALUES (:client_sector_sub_name, :client_sector_id)";
	$stmt = $db->prepare($sql);
	$ret_val = "isnew";
}

$stmt->bindValue(':client_sector_sub_name' , $_POST['client_sector_sub_name']);
$stmt->bindValue(':client_sector_id' , $_POST['client_sector_id']);

$stmt->execute();

$res = $stmt->rowCount();


if($res == 1)
	header("Location: tab_client_sector_subs.php?$ret_val=1");
else
	header("Location: tab_client_sector_subs.php?iserror=1");

?>