<?php

session_start();



if (!isset($_SESSION["u"])) {

	header("Location: login.php");

	exit ;

}





if(!isset($_POST['oSECTOR_description']) || !isset($_POST['oSUBSECTOR_name'])){

	echo "error010101010";

	return;

}



// include DB

require_once ('config.php');



$db = connect();







$ret_val="";



$sql = "INSERT INTO `client_sector_subs` (client_sector_sub_name, client_sector_id) VALUES (:client_sector_sub_name, :client_sector_id)";

$stmt = $db->prepare($sql);

$ret_val = "isnew";





$stmt->bindValue(':client_sector_sub_name' , $_POST['oSUBSECTOR_name']);

$stmt->bindValue(':client_sector_id' , $_POST['oSECTOR_description']);



$stmt->execute();



$res = $stmt->rowCount();





if($res == 1)

	echo "ok";

else

	echo "error";



?>