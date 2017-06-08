<?php
session_start();

if (!isset($_SESSION["u"])) {
	header("Location: login.php");
	exit ;
}

if ($_SESSION['level']!=9)
	die("You dont have permissions to access this area! Ask administrator for more!");
	
$active_tab = "countries";

if(!isset($_POST['country_name']) || !isset($_POST['country_min']) || !isset($_POST['country_max'])){
	echo "error010101010";
	return;
}

// include DB
require_once ('config.php');

$db = connect();

$ret_val="";
if(isset($_POST['countriesFORM_updateID']) && !empty($_POST['countriesFORM_updateID']))
{
	$sql = "UPDATE `countries` set country_name=:country_name, country_min=:country_min, country_max=:country_max WHERE country_id=:country_id";
	$stmt = $db->prepare($sql);
	$stmt->bindValue(':country_id', $_POST['countriesFORM_updateID']);
	$ret_val = "isupdate";
}
else
{
	$sql = "INSERT INTO `countries` (country_name, country_min, country_max) VALUES (:country_name, :country_min, :country_max)";
	$stmt = $db->prepare($sql);
	$ret_val = "isnew";
}

$stmt->bindValue(':country_name' , $_POST['country_name']);
$stmt->bindValue(':country_min' , $_POST['country_min']);
$stmt->bindValue(':country_max' , $_POST['country_max']);

$stmt->execute();

$res = $stmt->rowCount();


if($res == 1)
	header("Location: tab_countries.php?$ret_val=1");
else
	header("Location: tab_countries.php?iserror=1");

?>