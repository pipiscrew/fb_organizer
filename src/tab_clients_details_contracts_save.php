<?php
session_start();

if (!isset($_SESSION["u"])) {
    echo json_encode(null);
    exit ;
}
 
if (!isset($_POST['next_renewal_contract']) || !isset($_POST['service_starts_contract']) || !isset($_POST['service_ends_contract']) || !isset($_POST['marketing_plan_when_contract']) || !isset($_POST['marketing_plan_location_contract'])){
	echo "error010101010";
	return;
}
 
//DB
require_once ('config.php');
require_once ('config_general.php');
 
$db = connect();
 

$marketing_plan_completed = "0";

if (isset($_POST['marketing_plan_completed_contract'])) {
	if ($_POST['marketing_plan_completed_contract'] == "on")
		$marketing_plan_completed = 1;
	else
		$marketing_plan_completed = "0";
}

$request_access = "0";

if (isset($_POST['request_access'])) {
	if ($_POST['request_access'] == "on")
		$request_access = 1;
	else
		$request_access = "0";
}




$next_renewal=null;
if (!empty($_POST['next_renewal_contract']))
{
	$dt = DateTime::createFromFormat('d-m-Y', $_POST['next_renewal_contract']);
	
	$next_renewal =	$dt->format('Y-m-d');
}

$service_starts=null;
if (!empty($_POST['service_starts_contract']))
{
	$dt = DateTime::createFromFormat('d-m-Y', $_POST['service_starts_contract']);
	
	$service_starts =	$dt->format('Y-m-d');
}

$service_ends=null;
if (!empty($_POST['service_ends_contract']))
{
	$dt = DateTime::createFromFormat('d-m-Y', $_POST['service_ends_contract']);
	
	$service_ends =	$dt->format('Y-m-d');
}

$marketing_plan_when=null;
if (!empty($_POST['marketing_plan_when_contract']))
{
	$dt = DateTime::createFromFormat('d-m-Y H:i', $_POST['marketing_plan_when_contract']);
	
	$marketing_plan_when =	$dt->format('Y-m-d H:i:s');
}


$request_proceed=false;

if(isset($_POST['contractsFORM_updateID']) && !empty($_POST['contractsFORM_updateID']))
{
	$rec_id = 0;
	//notify
	 $rec_id = $_POST['contractsFORM_updateID'];
	 $offer_id_request_before = getScalar($db,"select request_access from offers where offer_id={$rec_id}",null);
	 
	 if ($offer_id_request_before!=$request_access)
		$request_proceed=true;
 	//notify		
 			
	$sql = "UPDATE offers set marketing_plan_comment=:marketing_plan_comment, request_access=:request_access, next_renewal=:next_renewal, service_starts=:service_starts, service_ends=:service_ends, marketing_plan_when=:marketing_plan_when, marketing_plan_location=:marketing_plan_location, marketing_plan_completed=:marketing_plan_completed where offer_id=:offer_id";
	$stmt = $db->prepare($sql);
	$stmt->bindValue(':offer_id' , $_POST['contractsFORM_updateID']);
}
else
{
	$sql = "INSERT INTO offers (marketing_plan_comment,request_access, next_renewal, service_starts, service_ends, marketing_plan_when, marketing_plan_location, marketing_plan_completed) VALUES (:marketing_plan_comment, :request_access, :next_renewal, :service_starts, :service_ends, :marketing_plan_when, :marketing_plan_location, :marketing_plan_completed)";
	$stmt = $db->prepare($sql);
}

$stmt->bindValue(':next_renewal' , $next_renewal);
$stmt->bindValue(':service_starts' , $service_starts);
$stmt->bindValue(':service_ends' , $service_ends);
$stmt->bindValue(':marketing_plan_when' , $marketing_plan_when);
$stmt->bindValue(':marketing_plan_location' , $_POST['marketing_plan_location_contract']);
$stmt->bindValue(':marketing_plan_completed' , $marketing_plan_completed, PDO::PARAM_INT);
$stmt->bindValue(':request_access' , $request_access, PDO::PARAM_INT);
$stmt->bindValue(':marketing_plan_comment' ,  $_POST['marketing_plan_comment_PKou1HBe']);

$stmt->execute();


if(isset($_POST['contractsFORM_updateID']) && !empty($_POST['contractsFORM_updateID']))
 {//update

 }
 else {//insertnew
 	if ($request_access==1)
 		$request_proceed=true;
 }
 
 
$status = $stmt->errorCode();

if ($status=="00000")
{
	$row_log = getRow($db,"select * from offers where offer_id=?",array($_POST['contractsFORM_updateID']));
	write_log($db, 4, "Contract for company ". $row_log['offer_company_name']." edited by seller ".$_SESSION['u'], $row_log['company_id'], $_SESSION['id']);	
	

	if ($marketing_plan_completed==1)
			write_log($db, 4, "Contract for company ". $row_log['offer_company_name']." edited by seller ".$_SESSION['u']." 'Marketing Plan' completed", $row_log['company_id'], $_SESSION['id']);	
			
	if ($request_proceed){
	
		if ($request_access==1)
		{
			   $body_text = "Link Page:  <a href='http://facebook.com/".$row_log['offer_page_url']."' target='_blank'>http://facebook.com/".$row_log['offer_page_url']."</a>";
			//send_mail("Request Access for ".$row_log['offer_company_name'],  $body_text);
				send_mail_to_cookies("Request Access for ".$row_log['offer_company_name'],  $body_text);
		}
		else {
			    $body_text = "Link Page:  <a href='http://facebook.com/".$row_log['offer_page_url']."' target='_blank'>http://facebook.com/".$row_log['offer_page_url']."</a>";
			//send_mail("Remove Access for  ".$row_log['offer_company_name'],  $body_text);	
				send_mail_to_cookies("Remove Access for  ".$row_log['offer_company_name'],  $body_text);		
		}
	}
}

echo $status; 
?>