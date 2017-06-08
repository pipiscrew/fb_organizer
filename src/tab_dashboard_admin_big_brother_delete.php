<?php
session_start();

if (!isset($_SESSION["u"])) {
    echo json_encode(null);
    exit ;
}
else if($_SESSION['level'] != 9){
	die("You are not authorized to view this!");
}

require_once ('config.php');

if (!isset($_POST['expense_id'])){
	echo "error010101010";
	return;
}

$db = connect();

$sql = "DELETE FROM `expenses` WHERE expense_id=:expense_id";
$sth = $db->prepare($sql);
$sth->bindValue(':expense_id', $_POST['expense_id']);
	
$sth->execute();

echo $sth->errorCode(); 
?>