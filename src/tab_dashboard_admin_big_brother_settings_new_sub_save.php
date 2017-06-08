<?php
session_start();

if (!isset($_SESSION["u"])) {
	header("Location: login.php");
	exit ;
}
else if ($_SESSION['level']!=9) {
	die("You are not authorized to view this!");
}
 
if (!isset($_POST['subcat_parent_id']) || !isset($_POST['subcategory_txt'])){
	echo "error010101010";
	return;
}
 
//DB
require_once ('config.php');
 
$db = connect();
 

$sql = "INSERT INTO expense_categories (parent_id, expense_category_name) VALUES (:parent_id, :expense_category_name)";
$stmt = $db->prepare($sql);


$stmt->bindValue(':parent_id' , $_POST['subcat_parent_id']);
$stmt->bindValue(':expense_category_name' , $_POST['subcategory_txt']);

$stmt->execute();


echo $stmt->errorCode();
	
?>