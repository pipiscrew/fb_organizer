<?php
session_start();

if (!isset($_SESSION["u"]) || !isset($_SESSION["mail"])) {
	header("Location: login.php");
	exit ;
}


require_once ('config.php');
require_once ('config_general.php');

$db = connect();

if (!isset($_POST["mail_recipient"]) || !isset($_POST["mail_subject"]) || !isset($_POST["mail_body"]) || !isset($_POST["mail_offer_rec_id"]))
	die("required field(s) missing");

$res =  send_mail_to_user_proposal($_SESSION['reply_mail'], $_POST["mail_recipient"],$_POST["mail_subject"],$_POST["mail_body"]);

if ($res == "ok"){
	executeSQL($db, "update offers set offer_sent_by_mail=? where offer_id=?",array(date("Y-m-d H:i:s"),$_POST["mail_offer_rec_id"]));
	
	$company_id = getScalar($db, "select company_id from offers where offer_id=?",array($_POST["mail_offer_rec_id"]));
	write_log($db, 5, "Proposal - Resend Mail " . $_POST["mail_recipient"] . " - Offer ID : " . $_POST["mail_offer_rec_id"],$company_id,$_SESSION['id']);
}

echo $res;
