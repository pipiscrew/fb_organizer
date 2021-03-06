<?php

session_start();

if (!isset($_SESSION["u"])) {
	header("Location: login.php");
	exit ;
}

if (!isset($_GET['id'])) {
	echo "error010101010";
	return;
}

require_once ('config.php');

$db = connect();

//validate that is the real owner
$owner_id = getScalar($db, "SELECT owner FROM clients WHERE client_id=?", array($_GET['id']));
if ($owner_id!=$_SESSION["id"] )
{
	die("you cant administrate this record! ask administrator why!");
}

//DELETE CALLS
$sql = "DELETE FROM `client_calls` WHERE client_id=:client_id";
$sth = $db->prepare($sql);
$sth->bindValue(':client_id', $_GET['id']);
$sth->execute();

//DELETE APPOINTMENTS
$sql = "DELETE FROM `client_appointments` WHERE client_appointment_client_id=:client_id";
$sth = $db->prepare($sql);
$sth->bindValue(':client_id', $_GET['id']);
$sth->execute();

//DELETE APPOINTMENTS PARTICIPANTS (SHOULD LOOP^ FOR APPOINT_ID)


//DELETE ITSELF
$sql = "DELETE FROM `clients` WHERE client_id=:client_id";
$sth = $db->prepare($sql);
$sth->bindValue(':client_id', $_GET['id']);
	
$sth->execute();

$g = $sth->rowCount();

if($g == 1)
	header("Location: tab_leads.php?isdelete=1");
else
	header("Location: tab_leads.php?iserror=1");
?>