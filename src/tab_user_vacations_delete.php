<?php
session_start();

if (!isset($_SESSION["u"]) || empty($_POST['user_vacation_id'])) {
    echo json_encode(null);
    exit ;
}
else {

	//only admins/CEO can delete the records
	if ($_SESSION['level']!=9 && $_SESSION['level']!=10)
	{	
		echo "you cant administrate this record, ask administrator for more.";
		exit;
	}	
	
}

require_once ('config.php');

$db = connect();

$sql = "DELETE FROM `user_vacations` WHERE user_vacation_id=:user_vacation_id";
$sth = $db->prepare($sql);
$sth->bindValue(':user_vacation_id', $_POST['user_vacation_id']);
	
$sth->execute();

echo $sth->errorCode(); 
?>