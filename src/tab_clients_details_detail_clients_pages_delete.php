<?php

session_start();

if (!isset($_SESSION["u"])) {
	header("Location: login.php");
	exit ;
}

if (!isset($_POST['id'])) {
	echo "error010101010";
	return;
}

require_once ('config.php');

$db = connect();

$sql = "DELETE FROM `clients_pages` WHERE client_page_id=:client_page_id";
$sth = $db->prepare($sql);
$sth->bindValue(':client_page_id', $_POST['id']);
	
$sth->execute();

$g = $sth->rowCount();

echo $g;
?>