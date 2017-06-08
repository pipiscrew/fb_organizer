<?php
session_start();
if ($_SESSION['level']!=9)
	die("You dont have permissions to access this area! Ask administrator for more!");
	
if (!isset($_SESSION["u"]) || empty($_POST['id'])) {
    echo json_encode(null);
    exit ;
}

require_once ('config.php');

$db = connect();

$sql = "DELETE FROM `client_calls` WHERE client_call_id=:client_call_id";
$sth = $db->prepare($sql);
$sth->bindValue(':client_call_id', $_POST['id']);
	
$sth->execute();

echo $sth->errorCode(); 
?>