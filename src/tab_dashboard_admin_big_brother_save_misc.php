<?php
session_start();

if (!isset($_SESSION["u"])) {
    echo json_encode(null);
    exit ;
}
else if($_SESSION['level'] != 9){
	die("You are not authorized to view this!");
}
 
if (!isset($_POST['cost_two']) || !isset($_POST['pay_method_two']) || !isset($_POST['misc_title_two']) || !isset($_POST['misc_daterec_two']) || !isset($_POST['comments_two'])){
	echo "error010101010";
	return;
}
 
//DB
require_once ('config.php');
 
$db = connect();
 

$misc_is_paid = "0";

if (isset($_POST['misc_is_paid_two'])) {
	if ($_POST['misc_is_paid_two'] == "on")
		$misc_is_paid = 1;
	else
		$misc_is_paid = "0";
}




$misc_daterec=null;
if (!empty($_POST['misc_daterec_two']))
{
	$dt = DateTime::createFromFormat('d-m-Y', $_POST['misc_daterec_two']);
	
	$misc_daterec =	$dt->format('Y-m-d');
}


if(isset($_POST['expensesFORM_updateID_two']) && !empty($_POST['expensesFORM_updateID_two']))
{
	$sql = "UPDATE expenses set cost=:cost, pay_method=:pay_method, misc_title=:misc_title, misc_daterec=:misc_daterec, misc_is_paid=:misc_is_paid, comments=:comments, owner_id=:owner_id where expense_id=:expense_id";
	$stmt = $db->prepare($sql);
	$stmt->bindValue(':expense_id' , $_POST['expensesFORM_updateID_two']);
}
else
{
	$sql = "INSERT INTO expenses (cost, pay_method, misc_title, misc_daterec, misc_is_paid, comments, owner_id) VALUES (:cost, :pay_method, :misc_title, :misc_daterec, :misc_is_paid, :comments, :owner_id)";
	$stmt = $db->prepare($sql);
}

$stmt->bindValue(':cost' , $_POST['cost_two']);
$stmt->bindValue(':pay_method' , $_POST['pay_method_two']);
$stmt->bindValue(':misc_title' , $_POST['misc_title_two']);
$stmt->bindValue(':misc_daterec' , $misc_daterec);
$stmt->bindValue(':misc_is_paid' , $misc_is_paid, PDO::PARAM_INT);
$stmt->bindValue(':comments' , $_POST['comments_two']);
$stmt->bindValue(':owner_id' , $_SESSION['id']);

$stmt->execute();
 
echo $stmt->errorCode(); 
?>