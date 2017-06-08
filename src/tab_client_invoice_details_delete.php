<?php
session_start();
if ($_SESSION['level']!=9)
	die("You dont have permissions to access this area! Ask administrator for more!");

require_once ('config.php');

$db = connect();

$sql = "DELETE FROM `client_invoice_details` WHERE client_invoice_detail_id=:client_invoice_detail_id";
$sth = $db->prepare($sql);
$sth->bindValue(':client_invoice_detail_id', $_POST['client_invoice_detail_id']);
	
$sth->execute();

echo $sth->errorCode(); 
?>