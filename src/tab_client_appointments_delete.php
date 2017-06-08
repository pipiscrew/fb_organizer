<?php
session_start();

if ($_SESSION['level']!=9)
	die("You dont have permissions to access this area! Ask administrator for more!");

require_once ('config.php');

$db = connect();

$sql = "DELETE FROM `client_appointments` WHERE client_appointment_id=:client_appointment_id";
$sth = $db->prepare($sql);
$sth->bindValue(':client_appointment_id', $_POST['client_appointment_id']);
	
$sth->execute();

echo $sth->errorCode(); 
?>