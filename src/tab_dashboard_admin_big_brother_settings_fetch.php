<?php
//session_start();
//
//if (!isset($_SESSION["u"]) || empty($_POST['expense_id'])) {
//    echo json_encode(null);
//    exit ;
//}

try {
	include ('config.php');

	$db = connect();

	$r= getRow($db, "SELECT expense_template_id, expense_category_id, expense_sub_category_id, price FROM expense_templates where expense_template_id=?", array($_POST['expense_id']));

    //unicode
    header("Content-Type: application/json", true);
	echo json_encode($r);

	
} catch (exception $e) {
    echo json_encode(null);
}
?>