<?php
session_start();

if (!isset($_SESSION["u"]) || !isset($_SESSION["mail"])) {
	header("Location: login.php");
	exit ;
}

require_once ('config.php');

$db = connect();

//$sql = "DELETE FROM `offer_room_details` WHERE offer_room_detail_id=:offer_room_detail_id";
$sql = "update `offer_room_details` set is_deleted=1 WHERE offer_room_detail_id=:offer_room_detail_id";

$sth = $db->prepare($sql);
$sth->bindValue(':offer_room_detail_id', $_POST['offer_room_detail_id']);
	
$sth->execute();

echo $sth->errorCode(); 
?>