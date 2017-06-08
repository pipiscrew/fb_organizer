<?php
session_start();

if (!isset($_SESSION["id"]) || !isset($_POST["id"])) {
	header("Location: login.php");
	exit ;
}
require_once ('config.php');

$db = connect();

$find_sql = "SELECT * FROM `clients_pages` where client_id = :id";

$stmt      = $db->prepare($find_sql);
$stmt->bindValue(':id', $_POST["id"]);

$stmt->execute();
$rows = $stmt->fetchAll();

echo json_encode(array("recs" => $rows));

