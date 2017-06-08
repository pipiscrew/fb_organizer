<?php

session_start();

if (!isset($_SESSION["u"])) {
	header("Location: login.php");
	exit ;
}


if(!isset($_POST['client_id']) || !isset($_POST['client_call_datetime']) || !isset($_POST['client_call_discussion']) || !isset($_POST['client_call_next_call']) || !isset($_POST['comment_call'])){
	echo "error010101010";
	return;
}

// include DB
require_once ('config.php');
require_once ('config_general.php');

$db = connect();

$chk_answered = "0";

if (isset($_POST['chk_answered'])) {
	if ($_POST['chk_answered'] == "on")
		$chk_answered = 1;
	else
		$chk_answered = "0";
}

$chk_company_presented = "0";

if (isset($_POST['chk_company_presented'])) {
	if ($_POST['chk_company_presented'] == "on")
		$chk_company_presented = 1;
	else
		$chk_company_presented = "0";
}

$chk_company_profile = "0";

if (isset($_POST['chk_company_profile'])) {
	if ($_POST['chk_company_profile'] == "on")
		$chk_company_profile = 1;
	else
		$chk_company_profile = "0";
}

$chk_client_proposal = "0";

if (isset($_POST['chk_client_proposal'])) {
	if ($_POST['chk_client_proposal'] == "on")
		$chk_client_proposal = 1;
	else
		$chk_client_proposal = "0";
}

$chk_appointment_booked = "0";

if (isset($_POST['chk_appointment_booked'])) {
	if ($_POST['chk_appointment_booked'] == "on")
		$chk_appointment_booked = 1;
	else
		$chk_appointment_booked = "0";
}

$client_call_datetime=null;
if (!empty($_POST['client_call_datetime']))
{
    //convert html control 24h date - to PHP 24h format date
    $dt = DateTime::createFromFormat('d-m-Y H:i', $_POST['client_call_datetime']);
    //set to variable a string date formatted as mySQL likes!
    $client_call_datetime = $dt->format('Y-m-d H:i:s');
}

$client_call_next_call=null;
if (!empty($_POST['client_call_next_call']))
{
    //convert html control 24h date - to PHP 24h format date
    $dt = DateTime::createFromFormat('d-m-Y H:i', $_POST['client_call_next_call']);
    //set to variable a string date formatted as mySQL likes!
    $client_call_next_call = $dt->format('Y-m-d H:i:s');
}

$ret_val="";
if(isset($_POST['client_callsFORM_updateID']) && !empty($_POST['client_callsFORM_updateID']))
{
	$sql = "UPDATE `client_calls` set client_call_datetime=:client_call_datetime, client_call_discussion=:client_call_discussion, client_call_next_call=:client_call_next_call, chk_answered=:chk_answered, chk_company_presented=:chk_company_presented, chk_company_profile=:chk_company_profile, chk_client_proposal=:chk_client_proposal, chk_appointment_booked=:chk_appointment_booked, comment=:comment WHERE client_call_id=:client_call_id";
	$stmt = $db->prepare($sql);
	$stmt->bindValue(':client_call_id', $_POST['client_callsFORM_updateID']);
	$ret_val = "isupdate";
}
else
{
	$sql = "INSERT INTO `client_calls` (client_id, client_call_datetime, client_call_discussion, client_call_next_call, chk_answered, chk_company_presented, chk_company_profile, chk_client_proposal, chk_appointment_booked, comment) VALUES (:client_id, :client_call_datetime, :client_call_discussion, :client_call_next_call, :chk_answered, :chk_company_presented, :chk_company_profile, :chk_client_proposal, :chk_appointment_booked, :comment)";
	$stmt = $db->prepare($sql);
	$stmt->bindValue(':client_id' , $_POST['client_id']);
	$ret_val = "isnew";
}

$stmt->bindValue(':client_call_datetime' , $client_call_datetime); // $_POST['client_call_datetime']);
$stmt->bindValue(':client_call_discussion' , $_POST['client_call_discussion']);
$stmt->bindValue(':client_call_next_call' , $client_call_next_call); //$_POST['client_call_next_call']);
$stmt->bindValue(':chk_answered' , $chk_answered, PDO::PARAM_INT);
$stmt->bindValue(':chk_company_presented' , $chk_company_presented, PDO::PARAM_INT);
$stmt->bindValue(':chk_company_profile' , $chk_company_profile, PDO::PARAM_INT);
$stmt->bindValue(':chk_client_proposal' , $chk_client_proposal, PDO::PARAM_INT);
$stmt->bindValue(':chk_appointment_booked' , $chk_appointment_booked, PDO::PARAM_INT);
$stmt->bindValue(':comment' , $_POST['comment_call']);

$stmt->execute();

$status = $stmt->errorCode();

if ($status="00000")
{
	if ($ret_val == "isnew")
			write_log($db, 4, "Call added for ".getScalar($db,"select client_name from clients where client_id=?",array($_POST['client_id']))." by seller ".$_SESSION['u'], $_POST['client_id'], $_SESSION['id']);
	else 
			write_log($db, 4, "Call updated for ".getScalar($db,"select client_name from clients where client_id=?",array($_POST['client_id']))." by seller ".$_SESSION['u'], $_POST['client_id'], $_SESSION['id']);
}		

echo $status;