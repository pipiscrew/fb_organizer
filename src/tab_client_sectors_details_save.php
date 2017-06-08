<?php
session_start();

if (!isset($_SESSION["u"])) {
	header("Location: login.php");
	exit ;
}

if ($_SESSION['level']!=9)
	die("You dont have permissions to access this area! Ask administrator for more!");
	
$active_tab = "client_sectors";

if(!isset($_POST['client_sector_name'])){
	echo "error010101010";
	return;
}

// include DB
require_once ('config.php');

$db = connect();



$ret_val="";
if(isset($_POST['client_sectorsFORM_updateID']) && !empty($_POST['client_sectorsFORM_updateID']))
{
	$sql = "UPDATE `client_sectors` set client_sector_name=:client_sector_name WHERE client_sector_id=:client_sector_id";
	$stmt = $db->prepare($sql);
	$stmt->bindValue(':client_sector_id', $_POST['client_sectorsFORM_updateID']);
	$ret_val = "isupdate";
}
else
{
	$sql = "INSERT INTO `client_sectors` (client_sector_name) VALUES (:client_sector_name)";
	$stmt = $db->prepare($sql);
	$ret_val = "isnew";
}

$stmt->bindValue(':client_sector_name' , $_POST['client_sector_name']);

$stmt->execute();

$res = $stmt->rowCount();


if($res == 1)
	header("Location: tab_client_sectors.php?$ret_val=1");
else
	header("Location: tab_client_sectors.php?iserror=1");

?>