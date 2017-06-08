<?php
//session_start();
//
//if (!isset($_SESSION["u"]) || empty($_POST['expense_id'])) {
//    echo json_encode(null);
//    exit ;
//}

exit;

require_once ('config.php');

$db = connect();

$sql = "DELETE FROM `expenses` WHERE expense_id=:expense_id";
$sth = $db->prepare($sql);
$sth->bindValue(':expense_id', $_POST['expense_id']);
	
$sth->execute();

echo $sth->errorCode(); 
?>