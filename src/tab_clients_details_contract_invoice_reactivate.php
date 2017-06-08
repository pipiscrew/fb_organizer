<?php
session_start();

if (!isset($_SESSION["id"]) || !isset($_GET['id'])) {
	header("Location: login.php");
	exit ;
}

//only admins
if ($_SESSION['level']!=9)
{
	header("Location: login.php");
	exit ;
}
//
////validate that is the real owner
//$owner_id = getScalar($db, "SELECT offer_seller_id FROM offers WHERE offer_id=?", array($_GET['id']));
//if ($owner_id!=$_SESSION["id"] )
//{
//	die("you cant administrate this record! ask administrator why!");
//}


require_once ('config.php');

$db = connect();

$cust_id = getScalar($db, "SELECT company_id FROM offers WHERE offer_id=?", array($_GET['id']));

$sql = "UPDATE offers set invoice_sent_when=:invoice_sent_when, invoice_sent_user=:invoice_sent_user,invoice_detail_id=:invoice_detail_id where offer_id=:offer_id";
$stmt = $db->prepare($sql);
$stmt->bindValue(':offer_id' , $_GET['id']);

$stmt->bindValue(':invoice_sent_when' , null, PDO::PARAM_INT);
$stmt->bindValue(':invoice_sent_user' , null, PDO::PARAM_INT);
$stmt->bindValue(':invoice_detail_id' , null, PDO::PARAM_INT);

$stmt->execute();
 

if ($stmt->errorCode()=="00000")
	header("Location: tab_clients_details.php?showcontracts=1&id=".$cust_id);
else 
	die("Update record process, end up with error code : ".$stmt->errorCode())
?>