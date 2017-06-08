<?php
session_start();

if (!isset($_SESSION["u"])) {
	header("Location: login.php");
	exit ;
}

if ($_SESSION['level']!=9)
	die("You dont have permissions to access this area! Ask administrator for more!");
	
$active_tab = "tax_offices";

if(!isset($_POST['tax_office_name']) || !isset($_POST['country_id']) || !isset($_POST['tax_office_code']) || !isset($_POST['tax_office_prefecture'])){
	echo "error010101010";
	return;
}

// include DB
require_once ('config.php');

$db = connect();

$ret_val="";
if(isset($_POST['tax_officesFORM_updateID']) && !empty($_POST['tax_officesFORM_updateID']))
{
	$sql = "UPDATE `tax_offices` set tax_office_name=:tax_office_name, country_id=:country_id, tax_office_code=:tax_office_code, tax_office_prefecture=:tax_office_prefecture WHERE tax_office_id=:tax_office_id";
	$stmt = $db->prepare($sql);
	$stmt->bindValue(':tax_office_id', $_POST['tax_officesFORM_updateID']);
	$ret_val = "isupdate";
}
else
{
	$sql = "INSERT INTO `tax_offices` (tax_office_name, country_id, tax_office_code, tax_office_prefecture) VALUES (:tax_office_name, :country_id, :tax_office_code, :tax_office_prefecture)";
	$stmt = $db->prepare($sql);
	$ret_val = "isnew";
}

$stmt->bindValue(':tax_office_name' , $_POST['tax_office_name']);
$stmt->bindValue(':country_id' , $_POST['country_id']);
$stmt->bindValue(':tax_office_code' , $_POST['tax_office_code']);
$stmt->bindValue(':tax_office_prefecture' , $_POST['tax_office_prefecture']);

$stmt->execute();

$res = $stmt->rowCount();


if($res == 1)
	header("Location: tab_tax_offices.php?$ret_val=1");
else
	header("Location: tab_tax_offices.php?iserror=1");

?>