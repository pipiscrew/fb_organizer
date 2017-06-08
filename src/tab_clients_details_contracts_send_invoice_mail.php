<?php
session_start();

if (!isset($_SESSION["u"]) || !isset($_SESSION["mail"])) {
	header("Location: login.php");
	exit ;
}

require_once ('config.php');
require_once ('config_general.php');

$db = connect();

if (!isset($_POST["mail2_recipient"]) || !isset($_POST["mail2_subject"]) || !isset($_POST["mail2_body"]) || !isset($_POST["mail2_offer_rec_id"]))
	die("required field(s) missing");

$res =  send_mail_to_user_proposal($_SESSION['reply_mail'], $_POST["mail2_recipient"],$_POST["mail2_subject"],$_POST["mail2_body"]);

if ($res == "ok"){
//	$invoice_solution= null;
//	if (getScalar($db, "select rec_guid_answer_invoice from offers where offer_id=?", array($_POST["mail2_offer_rec_id"]))==null)
//		$invoice_solution = guid_solution();
//		
//	executeSQL($db, "update offers set invoice_sent_when=?,rec_guid_answer_invoice=?,invoice_sent_user=? where offer_id=?",array(date("Y-m-d H:i:s"),$invoice_solution, $_SESSION['id'], $_POST["mail2_offer_rec_id"]));
executeSQL($db, "update offers set invoice_sent_when=?,invoice_sent_user=? where offer_id=?",array(date("Y-m-d H:i:s"), $_SESSION['id'], $_POST["mail2_offer_rec_id"]));
}

echo $res;


