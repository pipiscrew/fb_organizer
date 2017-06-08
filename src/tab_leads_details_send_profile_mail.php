<?php
session_start();

if (!isset($_SESSION["u"]) || !isset($_SESSION["mail"])) {
	header("Location: login.php");
	exit ;
}

require_once ('config.php');
require_once ('config_general.php');

$db = connect();

if (!isset($_POST["mail3_recipient"]) || !isset($_POST["mail3_subject"]) || !isset($_POST["mail3_body"]) || !isset($_POST["mail3_offer_rec_id"]))
	die("required field(s) missing");

$res =  send_mail_to_user_proposal($_SESSION['reply_mail'], $_POST["mail3_recipient"],$_POST["mail3_subject"],$_POST["mail3_body"]);

if ($res == "ok"){
	executeSQL($db, "update clients set profile_sent_when=?,profile_sent=1 where client_id=?",array(date("Y-m-d H:i:s"),$_POST["mail3_offer_rec_id"]));
	write_log($db, 5, "Profile sent to " . $_POST["mail3_recipient"],$_POST["mail3_offer_rec_id"],$_SESSION['id']);
}

echo $res;


