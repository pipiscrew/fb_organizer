<?php
session_start();

if (!isset($_SESSION["u"]) || empty($_POST['offer_id']) || empty($_POST['client_id'])) {
    echo json_encode(null);
    exit ;
}

try {
	include ('config.php');

	$db = connect();

	$r= getRow($db, "SELECT offer_id, is_paid, offer_type FROM offers where offer_id=?", array($_POST['offer_id']));

    //unicode
    header("Content-Type: application/json", true);
	echo json_encode($r);

	
} catch (exception $e) {
    echo json_encode(null);
}
?>