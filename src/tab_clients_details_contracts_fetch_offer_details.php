<?php
session_start();

if (!isset($_SESSION["u"]) || !isset($_POST["offer_id"])) {
	header("Location: login.php");
	exit ;
}
else {
	include ('config.php');

	$db = connect();

	$r= getRow($db, "SELECT * FROM offers where offer_id=?", array($_POST['offer_id']));

    //unicode
    header("Content-Type: application/json", true);
	echo json_encode($r);
}

?>