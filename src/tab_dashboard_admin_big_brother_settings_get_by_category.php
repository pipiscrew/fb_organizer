<?php
session_start();

if(!isset($_SESSION["u"]))
{
	header("Location: login.php");
	exit ;
}

// include DB
require_once ('config.php');

if(!isset($_POST["id"])){
	echo json_encode(null);
	return;
}

$id   = $_POST["id"];

$db   = connect();

$recs = getSet($db,"select expense_category_id as id,expense_category_name as description from expense_categories where parent_id = ?",array($id));

$json = array('recs'=> $recs);

header("Content-Type: application/json", true);

echo json_encode($json);