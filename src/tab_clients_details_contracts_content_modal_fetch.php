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
	$f = getScalar($db,"select count(offer_room_detail_id) from offer_room_details where offer_id=?", array($_POST['offer_id']));
	
	if ($f>1)
		die("-error-For this offer found double record!\r\n\r\nOperation Aborted!");
	elseif ($f==0)
		die($f);
	
//validation 2
	$f = getScalar($db,"select count(offer_room_detail_id) from offer_room_details where is_deleted=1 and offer_id=?", array($_POST['offer_id']));
	
	if ($f>0)
		die("-error-For this offer found a 'DELETED Content' record!\r\nPlease, inform the administrator!\r\n\r\nOperation Aborted!");
		
	$r= getRow($db, "SELECT offer_room_detail_id, create_room, room_type, room_name, account_manager, account_executive_id, posts_per_week, graphics, post_language, privacy, email1, email2, email3, email4, comment, DATE_FORMAT(daterec,'%d-%m-%Y %H:%i') as daterec FROM offer_room_details where is_deleted=0 and offer_id=? limit 1", array($_POST['offer_id']));

    //unicode
    header("Content-Type: application/json", true);
	echo json_encode($r);

	
} catch (exception $e) {
    echo "-error-exception:".$e->getMessage();
}
?>