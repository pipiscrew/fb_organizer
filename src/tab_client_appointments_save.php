<?php
session_start();

if (!isset($_SESSION["id"])) {
    echo json_encode(null);
    exit ;
}
 
if (!isset($_POST['client_appointmentsFORM_client_id']) || !isset($_POST['client_appointment_datetime']) || !isset($_POST['client_appointment_location']) || !isset($_POST['client_appointment_google']) || !isset($_POST['client_appointment_comment'])){
	echo "error010101010";
	return;
}
// 
// if (!isset($_POST['client_appointment_is_lead']))
// {echo "no set";
// exit;}
// echo "dd".$_POST['client_appointment_is_lead'];
// exit;
//DB
require_once ('config.php');
require_once ('config_general.php');


$db = connect();
 
 if(isset($_POST['client_appointmentsFORM_updateID']) && !empty($_POST['client_appointmentsFORM_updateID'])) 
 {
	$client_id = getScalar($db, "select client_appointment_client_id from client_appointments where client_appointment_id=?", array($_POST['client_appointmentsFORM_updateID']));
	$client_txt = getScalar($db, "select client_name from clients where client_id=?", array($client_id));
 }
 else {
	$client_id = $_POST['client_appointmentsFORM_client_id'];
	$client_txt = getScalar($db, "select client_name from clients where client_id=?", array($client_id)); 	
 }


$appointment_dt = $_POST['client_appointment_datetime'];
$appointment_location = $_POST['client_appointment_location'];
$appointment_comment = $_POST['client_appointment_comment'];
 
 $client_appointment_is_lead =$_POST['client_appointment_is_lead'];
// if ( $_POST['client_appointment_is_lead']==1)
//	$client_appointment_is_lead = "1";
//else 
//	$client_appointment_is_lead = "0";

	
//$client_appointment_is_lead = "0";
//
//if (isset($_POST['client_appointment_is_lead'])) {
//	if ($_POST['client_appointment_is_lead'] == "on")
//		$client_appointment_is_lead = 1;
//	else
//		$client_appointment_is_lead = "0";
//}



$client_appointment_datetime=null;
if (!empty($_POST['client_appointment_datetime']))
{
	$dt = DateTime::createFromFormat('d-m-Y H:i', $_POST['client_appointment_datetime']);
	
	$client_appointment_datetime =	$dt->format('Y-m-d H:i:s');
}

$app_id=null;
if(isset($_POST['client_appointmentsFORM_updateID']) && !empty($_POST['client_appointmentsFORM_updateID']))
{
	$sql = "UPDATE client_appointments set client_appointment_datetime=:client_appointment_datetime, client_appointment_location=:client_appointment_location, client_appointment_google=:client_appointment_google, client_appointment_comment=:client_appointment_comment where client_appointment_id=:client_appointment_id";
	$stmt = $db->prepare($sql);
	$stmt->bindValue(':client_appointment_id' , $_POST['client_appointmentsFORM_updateID']);
	
	$app_id = $_POST['client_appointmentsFORM_updateID']; 	
}
else
{
	$sql = "INSERT INTO client_appointments (client_appointment_client_id, client_appointment_is_lead, client_appointment_datetime, client_appointment_location, client_appointment_google, client_appointment_comment, client_appointment_owner_id) VALUES (:client_appointment_client_id, :client_appointment_is_lead, :client_appointment_datetime, :client_appointment_location, :client_appointment_google, :client_appointment_comment, :client_appointment_owner_id)";
	$stmt = $db->prepare($sql);
	$stmt->bindValue(':client_appointment_client_id' , $_POST['client_appointmentsFORM_client_id']);
	$stmt->bindValue(':client_appointment_owner_id' , $_SESSION["id"]);
	$stmt->bindValue(':client_appointment_is_lead' , $client_appointment_is_lead, PDO::PARAM_INT);
}


$stmt->bindValue(':client_appointment_datetime' , $client_appointment_datetime);
$stmt->bindValue(':client_appointment_location' , $_POST['client_appointment_location']);
$stmt->bindValue(':client_appointment_google' , $_POST['client_appointment_google']);
$stmt->bindValue(':client_appointment_comment' , $_POST['client_appointment_comment']);

$stmt->execute();
 
 //when insert
if ($app_id==null)
	$app_id = $db->lastInsertId(); 	
 
//echo $stmt->errorCode(); 

if ($stmt->errorCode() == "00000")
{
	
	
	$arr = json_decode($_POST['participants'],true);

	//delete if any (aka UPDATE)
	$sql = "delete from `client_appointment_participants` where client_appointment_id=:client_appointment_id";
	$stmt = $db->prepare($sql);
	$stmt->bindValue(':client_appointment_id' , $app_id);
	$stmt->execute();
	if($stmt->errorCode() != "00000"){
		echo $stmt->errorCode();
		exit;
	}
		
	//insert
	$sql = "INSERT INTO `client_appointment_participants` (client_appointment_id, user_id) VALUES (:client_appointment_id, :user_id)";
	if ($stmt = $db->prepare($sql)){

		$coordinator = $_SESSION['u'];

//grab participants
		$participants ="0";
		foreach ($arr as $userID) {
			$participants.= ",{$userID}";
		}
		
		$partSET = getSet($db,"select fullname from users where user_id in ({$participants})",null);
		
		$participants="";
		foreach($partSET as $row) {
			$participants .= $row['fullname'] . "<br>";
		}

//grab participants		
		
		foreach ($arr as $userID) {
			$stmt->bindValue(':client_appointment_id' , $app_id);
			$stmt->bindValue(':user_id' , $userID);
			
			$stmt->execute();	
			
			if($stmt->errorCode() != "00000"){
				echo $stmt->errorCode();
				exit;
			}
			else {
				$user_mail = getScalar($db, "select mail from users where user_id=?", array($userID));
				
				//email users
				send_mail_to_user($user_mail, "Appointment Participation for {$client_txt}", "Coordinator : {$coordinator}<br>Company : {$client_txt}<br>Arranged at $appointment_dt<br>Location : ".$appointment_location."<br>Participants : {$participants}<br><br>Comments : ".$appointment_comment);
				
				//notify users
				write_log($db, 1, "Appointment Participation for client $client_txt at $appointment_dt<br>Location : ".$appointment_location."<br>Comments : ".$appointment_comment, $client_id, $userID);
			}
		}
	}

	echo $stmt->errorCode(); 
}
else
	echo $stmt->errorCode(); 
?>