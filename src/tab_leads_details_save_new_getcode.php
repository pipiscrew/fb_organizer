<?php
session_start();

if (!isset($_SESSION["u"]) || !isset($_POST['is_lead']) || !isset($_POST['client_code'] )) {
	header("Location: login.php");
	exit ;
}

require_once ('config.php');

$db = connect();
//
//$res = getScalar($db, "select count(client_id) from clients where is_lead=? and client_code=?", array($_POST['is_lead'], $_POST['client_code']));
//
//if ($res>0)
//{
//	$res2 = getScalar($db, "select max(client_code)+1 from clients where is_lead=?", array($_POST['is_lead']));
	$res2 = getScalar($db, "select max(client_code)+1 from clients", null);
	echo json_encode($res2);	
//}
//else 
//	echo json_encode($res);	

?>