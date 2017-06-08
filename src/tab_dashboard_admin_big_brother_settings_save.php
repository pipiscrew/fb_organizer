<?php
session_start();

if (!isset($_SESSION["u"])) {
	header("Location: login.php");
	exit ;
}
else if ($_SESSION['level']!=9) {
	die("You are not authorized to view this!");
}
 
if (!isset($_POST['expense_category_id']) || !isset($_POST['expense_sub_category_id']) || !isset($_POST['price'])){
	echo "error010101010";
	return;
}
 
//DB
require_once ('config.php');
 
$db = connect();
 



$expense_daterec=null;
if (!empty($_POST['expense_daterec']))
{
	$dt = DateTime::createFromFormat('d-m-Y', $_POST['expense_daterec']);
	
	$expense_daterec =	$dt->format('Y-m-d');
}

$created_date=date('Y-m-d H:i:s');



if(isset($_POST['expensesFORM_updateID']) && !empty($_POST['expensesFORM_updateID']))
{
	$sql = "UPDATE expense_templates set expense_category_id=:expense_category_id, expense_sub_category_id=:expense_sub_category_id, price=:price where expense_template_id=:expense_id";
	$stmt = $db->prepare($sql);
	$stmt->bindValue(':expense_id' , $_POST['expensesFORM_updateID']);
}
else
{
	$sql = "INSERT INTO expense_templates (expense_category_id, expense_sub_category_id, price, created_owner_id, created_date) VALUES (:expense_category_id, :expense_sub_category_id, :price,:created_owner_id, :created_date)";
	$stmt = $db->prepare($sql);
	$stmt->bindValue(':created_owner_id' , $_SESSION["id"]);
$stmt->bindValue(':created_date' , $created_date);
}

$stmt->bindValue(':expense_category_id' , $_POST['expense_category_id']);
$stmt->bindValue(':expense_sub_category_id' , $_POST['expense_sub_category_id']);
$stmt->bindValue(':price' , $_POST['price']);


$stmt->execute();
 
echo $stmt->errorCode(); 
?>