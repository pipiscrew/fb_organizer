<?php
session_start();

if (!isset($_SESSION["id"])) {
	header("Location: login.php");
	exit ;
}
 
if (!isset($_POST['client_page_client_id']) || !isset($_POST['client_page'])){
	echo "error010101010";
	return;
}
 
//DB
require_once ('config.php');
 
$db = connect();
 
$sql = "INSERT INTO clients_pages (client_id, client_page) VALUES (:client_id, :client_page)";
$stmt = $db->prepare($sql);

$stmt->bindValue(':client_id' , $_POST['client_page_client_id']);
$stmt->bindValue(':client_page' , $_POST['client_page']);

$stmt->execute();
 
echo $stmt->errorCode(); 
?>