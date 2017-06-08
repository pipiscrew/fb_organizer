<?php
session_start();
if ($_SESSION['level']!=9)
	die("You dont have permissions to access this area! Ask administrator for more!");

try {
	include ('config.php');

	$db = connect();

	$r= getRow($db, "SELECT user_working_hour_id, user_id, DATE_FORMAT(date_start,'%d-%m-%Y %H:%i') as date_start, DATE_FORMAT(date_end,'%d-%m-%Y %H:%i') as date_end FROM user_working_hours where user_working_hour_id=?", array($_POST['user_working_hour_id']));

    //unicode
    header("Content-Type: application/json", true);
	echo json_encode($r);

	
} catch (exception $e) {
    echo json_encode(null);
}
?>