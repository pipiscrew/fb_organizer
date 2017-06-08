<?php
session_start();
if ($_SESSION['level']!=9)
	die("You dont have permissions to access this area! Ask administrator for more!");
 
if (!isset($_POST['user_id']) || !isset($_POST['date_start']) || !isset($_POST['date_end'])){
	echo "error010101010";
	return;
}
 
//DB
require_once ('config.php');
 
$db = connect();
 



$date_start=null;
if (!empty($_POST['date_start']))
{
	$dt = DateTime::createFromFormat('d-m-Y H:i', $_POST['date_start']);
	
	$date_start =	$dt->format('Y-m-d H:i:s');
}

$date_end=null;
if (!empty($_POST['date_end']))
{
	$dt = DateTime::createFromFormat('d-m-Y H:i', $_POST['date_end']);
	
	$date_end =	$dt->format('Y-m-d H:i:s');
}


if(isset($_POST['user_working_hoursFORM_updateID']) && !empty($_POST['user_working_hoursFORM_updateID']))
{
	$sql = "UPDATE user_working_hours set user_id=:user_id, date_start=:date_start, date_end=:date_end where user_working_hour_id=:user_working_hour_id";
	$stmt = $db->prepare($sql);
	$stmt->bindValue(':user_working_hour_id' , $_POST['user_working_hoursFORM_updateID']);
}
else
{
	$sql = "INSERT INTO user_working_hours (user_id, date_start, date_end) VALUES (:user_id, :date_start, :date_end)";
	$stmt = $db->prepare($sql);
}

$stmt->bindValue(':user_id' , $_POST['user_id']);
$stmt->bindValue(':date_start' , $date_start);
$stmt->bindValue(':date_end' , $date_end);

$stmt->execute();
 
echo $stmt->errorCode(); 
?>