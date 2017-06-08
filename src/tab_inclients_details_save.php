<?php

//include('../../Debug.php');
//
////Catch
//Debug::register();


session_start();

if (!isset($_SESSION["id"])) {
	header("Location: login.php");
	exit ;
}

$active_tab = "clients";

if(!isset($_POST['client_code']) || !isset($_POST['client_name']) || !isset($_POST['client_sector_id']) || !isset($_POST['client_sector_sub_id']) || !isset($_POST['client_source_id']) || !isset($_POST['client_rating_id']) || !isset($_POST['country_id']) || !isset($_POST['manager_name']) || !isset($_POST['address']) || !isset($_POST['telephone']) || !isset($_POST['mobile']) || !isset($_POST['email']) || !isset($_POST['website']) || !isset($_POST['comment']) || !isset($_POST['owned_date']) || !isset($_POST['owner']) || !isset($_POST['modified_date']) || !isset($_POST['modified_by']) || !isset($_POST['city_client']) || !isset($_POST['area_client'])){
	echo "error010101010";
	return;
}

// include DB
require_once ('config.php');
require_once ('config_general.php');

$db = connect();


/////////////////////
$is_lead = "2";
/////////////////////

$has_facebook_page_before = "0";

if (isset($_POST['has_facebook_page_before'])) {
	if ($_POST['has_facebook_page_before'] == "on")
		$has_facebook_page_before = 1;
	else
		$has_facebook_page_before = "0";
}

$room_exists = "0";

if (isset($_POST['room_exists'])) {
	if ($_POST['room_exists'] == "on")
		$room_exists = 1;
	else
		$room_exists = "0";
}


$ret_val="";
if(isset($_POST['clientsFORM_updateID']) && !empty($_POST['clientsFORM_updateID']))
{
	$sql = "UPDATE `clients` set is_lead=:is_lead, client_name=:client_name, client_sector_id=:client_sector_id, client_sector_sub_id=:client_sector_sub_id, client_source_id=:client_source_id, client_rating_id=:client_rating_id, country_id=:country_id, city=:city, area=:area, manager_name=:manager_name, address=:address, telephone=:telephone, mobile=:mobile, email=:email, website=:website, comment=:comment, modified_date=:modified_date, modified_by=:modified_by, has_facebook_page_before=:has_facebook_page_before, room_exists=:room_exists WHERE client_id=:client_id";
	$stmt = $db->prepare($sql);
	$stmt->bindValue(':client_id', $_POST['clientsFORM_updateID']);
	
	$stmt->bindValue(':modified_date' , date("Y-m-d H:i:s"));
	$stmt->bindValue(':modified_by' , $_SESSION["id"]);
	
	$ret_val = "isupdate";
}
else
{
	$sql = "INSERT INTO `clients` (client_code, is_lead, client_name, client_sector_id, client_sector_sub_id, client_source_id, client_rating_id, country_id, city, area, manager_name, address, telephone, mobile, email, website, comment, owned_date, owner, has_facebook_page_before, room_exists) VALUES (:client_code, :is_lead, :client_name, :client_sector_id, :client_sector_sub_id, :client_source_id, :client_rating_id, :country_id, :city, :area, :manager_name, :address, :telephone, :mobile, :email, :website, :comment, :owned_date, :owner, :has_facebook_page_before, :room_exists)";
	$stmt = $db->prepare($sql);
	
	$stmt->bindValue(':client_code' , $_POST['client_code']);
	$stmt->bindValue(':owned_date' , date("Y-m-d H:i:s"));
	$stmt->bindValue(':owner' , $_SESSION["id"]);
	
	$ret_val = "isnew";
}

$stmt->bindValue(':is_lead' , $is_lead, PDO::PARAM_INT);
$stmt->bindValue(':client_name' , $_POST['client_name']);
$stmt->bindValue(':client_sector_id' , $_POST['client_sector_id']);
$stmt->bindValue(':client_sector_sub_id' , $_POST['client_sector_sub_id']);
$stmt->bindValue(':client_source_id' , $_POST['client_source_id']);
$stmt->bindValue(':client_rating_id' , $_POST['client_rating_id']);
$stmt->bindValue(':country_id' , $_POST['country_id']);
$stmt->bindValue(':city' , $_POST['city_client']);
$stmt->bindValue(':area' , $_POST['area_client']);
$stmt->bindValue(':manager_name' , $_POST['manager_name']);
$stmt->bindValue(':address' , $_POST['address']);
$stmt->bindValue(':telephone' , $_POST['telephone']);
$stmt->bindValue(':mobile' , $_POST['mobile']);
$stmt->bindValue(':email' , $_POST['email']);
//$stmt->bindValue(':facebook_page' , $_POST['facebook_page']);
$stmt->bindValue(':website' , $_POST['website']);
$stmt->bindValue(':comment' , $_POST['comment']);
$stmt->bindValue(':has_facebook_page_before' , $has_facebook_page_before, PDO::PARAM_INT);
$stmt->bindValue(':room_exists' , $room_exists, PDO::PARAM_INT);

$stmt->execute();

$res = $stmt->rowCount();

if($res == 1)
{
//log

	if ($ret_val == "isnew")
	{
		$customer_id = $db->lastInsertId();
		
		write_log($db, 4, "Client added named : ".$_POST['client_name']." by seller ".$_SESSION['u'], $db->lastInsertId(), $_SESSION['id']);
		
		header("Location: tab_inclients_details.php?showcalls=1&id=".$customer_id);
	}
	else 
	{
		write_log($db, 4, "Client ".$_POST['client_name']." updated by seller ".$_SESSION['u'], $_POST['clientsFORM_updateID'], $_SESSION['id']);
		
		header("Location: tab_inclients.php?$ret_val=1");
	}
}
else
	header("Location: tab_inclients.php?iserror=1");

?>