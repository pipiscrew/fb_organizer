<?php
session_start();

if (!isset($_SESSION["u"])) {
    echo json_encode(null);
    exit ;
}
else if($_SESSION['level'] != 9){
	die("You are not authorized to view this!");
}
 
if (!isset($_POST['template_id']) || !isset($_POST['cost']) || !isset($_POST['daterec']) || !isset($_POST['pay_method']) ){
	echo "error010101010";
	return;
}
 
//DB
require_once ('config.php');
 
$db = connect();
 



$daterec=null;

if (!empty($_POST['daterec']))
{
	$dt = DateTime::createFromFormat('d-m-Y', $_POST['daterec']);
	
	$daterec =	$dt->format('Y-m-d');
}


if(isset($_POST['expensesFORM_updateID']) && !empty($_POST['expensesFORM_updateID']))
{
	$sql = "UPDATE expenses set expense_template_id=:expense_template_id, cost=:cost, daterec=:daterec, comments=:comments, owner_id=:owner_id, pay_method=:pay_method where expense_id=:expense_id";
	$stmt = $db->prepare($sql);
	$stmt->bindValue(':expense_id' , $_POST['expensesFORM_updateID']);
}
else
{
	
	$sql = "INSERT INTO expenses (expense_template_id, cost, daterec, comments, owner_id, pay_method) VALUES (:expense_template_id, :cost, :daterec, :comments, :owner_id, :pay_method)";
	$stmt = $db->prepare($sql);
}

$stmt->bindValue(':expense_template_id' , $_POST['template_id']);
$stmt->bindValue(':cost' , $_POST['cost']);
$stmt->bindValue(':daterec' , $daterec);
$stmt->bindValue(':comments' , $_POST['comments']);
$stmt->bindValue(':owner_id' , $_SESSION['id']);
$stmt->bindValue(':pay_method' ,  $_POST['pay_method']);

$stmt->execute();
 
echo $stmt->errorCode(); 
?>