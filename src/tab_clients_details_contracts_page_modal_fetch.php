<?php
session_start();

if (!isset($_SESSION["u"]) || !isset($_SESSION["mail"])) {
	header("Location: login.php");
	exit ;
}

try {
	include ('config.php');

	$db = connect();

//validation 1
	$f = getScalar($db,"select count(offer_page_detail_id) from offer_page_details where offer_id=?", array($_POST['offer_id']));
	
	if ($f>1)
		die("-error-For this offer found double record!\r\n\r\nOperation Aborted!");
	elseif ($f==0)
		die($f);

	$r= getRow($db, "SELECT offer_page_detail_id, offer_id, is_creation, domain, reviews, call_action, website, cover_photo_change, profile_photo_change, short_description, DATE_FORMAT(daterec,'%d-%m-%Y %H:%i') as daterec FROM offer_page_details where offer_id=? limit 1", array($_POST['offer_id']));

    //unicode
    header("Content-Type: application/json", true);
	echo json_encode($r);

	
} catch (exception $e) {
    echo "-error-exception:".$e->getMessage();
}
?>