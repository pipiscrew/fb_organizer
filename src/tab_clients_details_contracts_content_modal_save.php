<?php
session_start();

if (!isset($_SESSION["u"]) || !isset($_SESSION["mail"])) {
	header("Location: login.php");
	exit ;
}
 
if (!isset($_POST['content_offerID']) || !isset($_POST['room_name']) || !isset($_POST['account_manager']) || !isset($_POST['account_executive_id']) || !isset($_POST['posts_per_week']) || !isset($_POST['post_language']) || !isset($_POST['content_priv_radio']) || !isset($_POST['email1']) ){
	echo "error010101010";
	return;
}
 
//DB
require_once ('config.php');
 
$db = connect();
 

$create_room = "0";

if (isset($_POST['create_room'])) {
	if ($_POST['create_room'] == "on")
		$create_room = 1;
	else
		$create_room = "0";
}

$room_type = "0";

if (isset($_POST['room_type'])) {
	if ($_POST['room_type'] == "on")
		$room_type = 1;
	else
		$room_type = "0";
}

$graphics = "0";

if (isset($_POST['graphics'])) {
	if ($_POST['graphics'] == "on")
		$graphics = 1;
	else
		$graphics = "0";
}




if(isset($_POST['offer_room_detailsFORM_updateID']) && !empty($_POST['offer_room_detailsFORM_updateID']))
{
	$sql = "UPDATE offer_room_details set offer_id=:offer_id, create_room=:create_room, room_type=:room_type, room_name=:room_name, account_manager=:account_manager, account_executive_id=:account_executive_id, posts_per_week=:posts_per_week, graphics=:graphics, post_language=:post_language, privacy=:privacy, email1=:email1, email2=:email2, email3=:email3, email4=:email4, comment=:comment, daterec=:daterec where offer_room_detail_id=:offer_room_detail_id";
	$stmt = $db->prepare($sql);
	$stmt->bindValue(':offer_room_detail_id' , $_POST['offer_room_detailsFORM_updateID']);
}
else
{
	//validation - when insert, check if any record exists for this offer!!
	$f = getScalar($db,"select count(offer_room_detail_id) from offer_room_details where offer_id=?", array($_POST['content_offerID']));
	
	if ($f>0)
		die("-error-For this offer found double record!\r\nPlease, inform the administrator!\r\n\r\nOperation Aborted!");
	//validation - when insert, check if any record exists for this offer!!
		
	$sql = "INSERT INTO offer_room_details (offer_id, create_room, room_type, room_name, account_manager, account_executive_id, posts_per_week, graphics, post_language, privacy, email1, email2, email3, email4, comment, daterec) VALUES (:offer_id, :create_room, :room_type, :room_name, :account_manager, :account_executive_id, :posts_per_week, :graphics, :post_language, :privacy, :email1, :email2, :email3, :email4, :comment, :daterec)";
	$stmt = $db->prepare($sql);
}

$stmt->bindValue(':offer_id' , $_POST['content_offerID']);
$stmt->bindValue(':create_room' , $create_room, PDO::PARAM_INT);
$stmt->bindValue(':room_type' , $room_type, PDO::PARAM_INT);
$stmt->bindValue(':room_name' , $_POST['room_name']);
$stmt->bindValue(':account_manager' , $_POST['account_manager']);
$stmt->bindValue(':account_executive_id' , $_POST['account_executive_id']);
$stmt->bindValue(':posts_per_week' , $_POST['posts_per_week']);
$stmt->bindValue(':graphics' , $graphics, PDO::PARAM_INT);
$stmt->bindValue(':post_language' , $_POST['post_language']);
$stmt->bindValue(':privacy' , $_POST['content_priv_radio']);
//$stmt->bindValue(':post_rules' , $_POST['post_rules']);
$stmt->bindValue(':email1' , $_POST['email1']);
$stmt->bindValue(':email2' , $_POST['email2']);
$stmt->bindValue(':email3' , $_POST['email3']);
$stmt->bindValue(':email4' , $_POST['email4']);
$stmt->bindValue(':comment' , $_POST['content_comment']);
$stmt->bindValue(':daterec' , date("Y-m-d H:i:s"));

$stmt->execute();
 
echo $stmt->errorCode(); 
?>