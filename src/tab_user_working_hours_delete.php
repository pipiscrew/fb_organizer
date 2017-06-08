<?php
session_start();
if ($_SESSION['level']!=9)
	die("You dont have permissions to access this area! Ask administrator for more!");
	

if (!isset($_SESSION["u"]) || empty($_POST['user_working_hour_id'])) {
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

$sql = "DELETE FROM `user_working_hours` WHERE user_working_hour_id=:user_working_hour_id";
$sth = $db->prepare($sql);
$sth->bindValue(':user_working_hour_id', $_POST['user_working_hour_id']);
	
$sth->execute();

echo $sth->errorCode(); 
?>