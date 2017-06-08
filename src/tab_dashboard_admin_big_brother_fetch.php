<?php
session_start();

if (!isset($_SESSION["u"]) || empty($_POST['expense_id'])) {
    echo json_encode(null);
    exit ;
} else if($_SESSION['level'] != 9){
	die("You are not authorized to view this!");
}


try {
	include ('config.php');

	$db = connect();

	$r= getRow($db, "SELECT expense_id, expense_template_id, cost, pay_method, DATE_FORMAT(daterec,'%d-%m-%Y') as daterec, misc_title, DATE_FORMAT(misc_daterec,'%d-%m-%Y') as misc_daterec, misc_is_paid, comments, owner_id FROM expenses where expense_id=?", array($_POST['expense_id']));

    //unicode
    header("Content-Type: application/json", true);
	echo json_encode($r);

	
} catch (exception $e) {
    echo json_encode(null);
}
?>