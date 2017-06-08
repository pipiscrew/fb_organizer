<?php
session_start();

if (!isset($_SESSION["u"]) || !isset($_SESSION["mail"])) {
	header("Location: login.php");
	exit ;
}

if (!isset($_POST['page_offerID']) || !isset($_POST['domain']) || !isset($_POST['call_action']) || !isset($_POST['website']) || !isset($_POST['short_description'])){
	echo "error010101010";
	return;
}
 
//DB
require_once ('config.php');
 
$db = connect();
 

$is_creation = "0";

if (isset($_POST['is_creation'])) {
	if ($_POST['is_creation'] == "on")
		$is_creation = 1;
	else
		$is_creation = "0";
}

$reviews = "0";

if (isset($_POST['reviews'])) {
	if ($_POST['reviews'] == "on")
		$reviews = 1;
	else
		$reviews = "0";
}

$cover_photo_change = "0";

if (isset($_POST['cover_photo_change'])) {
	if ($_POST['cover_photo_change'] == "on")
		$cover_photo_change = 1;
	else
		$cover_photo_change = "0";
}

$profile_photo_change = "0";

if (isset($_POST['profile_photo_change'])) {
	if ($_POST['profile_photo_change'] == "on")
		$profile_photo_change = 1;
	else
		$profile_photo_change = "0";
}


if(isset($_POST['offer_page_detailsFORM_updateID']) && !empty($_POST['offer_page_detailsFORM_updateID']))
{
	$sql = "UPDATE offer_page_details set offer_id=:offer_id, is_creation=:is_creation, domain=:domain, reviews=:reviews, call_action=:call_action, website=:website, cover_photo_change=:cover_photo_change, profile_photo_change=:profile_photo_change, short_description=:short_description, daterec=:daterec where offer_page_detail_id=:offer_page_detail_id";
	$stmt = $db->prepare($sql);
	$stmt->bindValue(':offer_page_detail_id' , $_POST['offer_page_detailsFORM_updateID']);
}
else
{
	//validation - when insert, check if any record exists for this offer!!
	$f = getScalar($db,"select count(offer_page_detail_id) from offer_page_details where offer_id=?", array($_POST['page_offerID']));
	
	if ($f>0)
		die("-error-For this offer found double record!\r\nPlease, inform the administrator!\r\n\r\nOperation Aborted!");
	//validation - when insert, check if any record exists for this offer!!
	
	$sql = "INSERT INTO offer_page_details (offer_id, is_creation, domain, reviews, call_action, website, cover_photo_change, profile_photo_change, short_description, daterec) VALUES (:offer_id, :is_creation, :domain, :reviews, :call_action, :website, :cover_photo_change, :profile_photo_change, :short_description, :daterec)";
	$stmt = $db->prepare($sql);
}

$stmt->bindValue(':offer_id' , $_POST['page_offerID']);
$stmt->bindValue(':is_creation' , $is_creation, PDO::PARAM_INT);
$stmt->bindValue(':domain' , $_POST['domain']);
$stmt->bindValue(':reviews' , $reviews, PDO::PARAM_INT);
$stmt->bindValue(':call_action' , $_POST['call_action']);
$stmt->bindValue(':website' , $_POST['website']);
$stmt->bindValue(':cover_photo_change' , $cover_photo_change, PDO::PARAM_INT);
$stmt->bindValue(':profile_photo_change' , $profile_photo_change, PDO::PARAM_INT);
$stmt->bindValue(':short_description' , $_POST['short_description']);
$stmt->bindValue(':daterec' , date("Y-m-d H:i:s"));

$stmt->execute();
 
echo $stmt->errorCode(); 
?>