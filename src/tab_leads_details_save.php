<?php
session_start();

if (!isset($_SESSION["id"])) {
	header("Location: login.php");
	exit ;
}

$active_tab = "leads";

if(!isset($_POST['client_code']) || !isset($_POST['client_name']) || !isset($_POST['client_sector_id']) || !isset($_POST['client_sector_sub_id']) || !isset($_POST['client_source_id']) || !isset($_POST['client_rating_id']) || !isset($_POST['country_id']) || !isset($_POST['manager_name']) || !isset($_POST['address']) || !isset($_POST['telephone']) || !isset($_POST['mobile']) || !isset($_POST['email']) || !isset($_POST['website']) || !isset($_POST['comment']) || !isset($_POST['city_lead']) || !isset($_POST['area_lead'])){
	echo "error010101010";
	return;
}

// include DB
require_once ('config.php');
require_once ('config_general.php');

$db = connect();

/////////////////////
$is_lead = "1";
/////////////////////

$profile_sent = "0";

if (isset($_POST['profile_sent'])) {
	if ($_POST['profile_sent'] == "on")
		$profile_sent = 1;
	else
		$profile_sent = "0";
}

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
if(isset($_POST['leadsFORM_updateID']) && !empty($_POST['leadsFORM_updateID']))
{
	$sql = "UPDATE `clients` set is_lead=:is_lead, manager_name2=:manager_name2, email2=:email2, client_name=:client_name, client_sector_id=:client_sector_id, client_sector_sub_id=:client_sector_sub_id, client_source_id=:client_source_id, client_rating_id=:client_rating_id, profile_sent=:profile_sent, country_id=:country_id, city=:city, area=:area, manager_name=:manager_name, address=:address, telephone=:telephone, mobile=:mobile, email=:email, website=:website, comment=:comment, modified_date=:modified_date, modified_by=:modified_by, profile_sent_when=:profile_sent_when WHERE client_id=:client_id";
	$stmt = $db->prepare($sql);
	$stmt->bindValue(':client_id', $_POST['leadsFORM_updateID']);
	
	$stmt->bindValue(':modified_date' , date("Y-m-d H:i:s"));
	$stmt->bindValue(':modified_by' , $_SESSION["id"]);
	
	$ret_val = "isupdate";
}
else
{
	$guid = guid();
	
	$sql = "INSERT INTO `clients` (is_lead, manager_name2, email2, profile_guid, client_code, client_name, client_sector_id, client_sector_sub_id, client_source_id, client_rating_id, profile_sent, country_id, city, area, manager_name, address, telephone, mobile, email, website, comment, owned_date, owner, profile_sent_when) VALUES (:is_lead, :manager_name2, :email2, :profile_guid, :client_code, :client_name, :client_sector_id, :client_sector_sub_id, :client_source_id, :client_rating_id, :profile_sent, :country_id, :city, :area, :manager_name, :address, :telephone, :mobile, :email, :website, :comment, :owned_date, :owner, :profile_sent_when)";
	$stmt = $db->prepare($sql);
	
	$stmt->bindValue(':client_code' , $_POST['client_code']);
	$stmt->bindValue(':owned_date' , date("Y-m-d H:i:s"));
	$stmt->bindValue(':profile_guid' , $guid);
	$stmt->bindValue(':owner' , $_SESSION["id"]);
	
	$ret_val = "isnew";
}

$stmt->bindValue(':is_lead' , $is_lead, PDO::PARAM_INT);
$stmt->bindValue(':client_name' , $_POST['client_name']);
$stmt->bindValue(':client_sector_id' , $_POST['client_sector_id']);
$stmt->bindValue(':client_sector_sub_id' , $_POST['client_sector_sub_id']);
$stmt->bindValue(':client_source_id' , $_POST['client_source_id']);
$stmt->bindValue(':client_rating_id' , $_POST['client_rating_id']);
//$stmt->bindValue(':next_call' , $next_call); //$_POST['next_call']);
$stmt->bindValue(':profile_sent' , $profile_sent, PDO::PARAM_INT);

//when once sent, freeze there
if ($profile_sent==1)
	$stmt->bindValue(':profile_sent_when' , date("Y-m-d"));
else 
	$stmt->bindValue(':profile_sent_when' , null);

$stmt->bindValue(':country_id' , $_POST['country_id']);
$stmt->bindValue(':city' , $_POST['city_lead']);
$stmt->bindValue(':area' , $_POST['area_lead']);
$stmt->bindValue(':manager_name' , $_POST['manager_name']);
$stmt->bindValue(':address' , $_POST['address']);
$stmt->bindValue(':telephone' , $_POST['telephone']);
$stmt->bindValue(':mobile' , $_POST['mobile']);
$stmt->bindValue(':email' , $_POST['email']);
//$stmt->bindValue(':facebook_page' , $_POST['facebook_page']);
$stmt->bindValue(':website' , $_POST['website']);
$stmt->bindValue(':comment' , $_POST['comment']);
//$stmt->bindValue(':owned_date' , $_POST['owned_date']);
//$stmt->bindValue(':owner' , $_POST['owner']);
//$stmt->bindValue(':modified_date' , $_POST['modified_date']);
//$stmt->bindValue(':modified_by' , $_POST['modified_by']);
$stmt->bindValue(':manager_name2' , $_POST['manager_name2']);
$stmt->bindValue(':email2' , $_POST['email2']);

$stmt->execute();

//$arr = $stmt->errorInfo();
//var_dump($arr);
//exit;

$res = $stmt->rowCount();


if($res == 1)
{
	if ($ret_val == "isnew"){
		
		$customer_id = $db->lastInsertId();

		write_log($db, 4, "Lead added named : ".$_POST['client_name']." by seller ".$_SESSION['u'], $db->lastInsertId(), $_SESSION['id']);
		
		$send_profile="";
		if ($profile_sent == 1)
			$send_profile="&sendprofile=1";
		
		header("Location: tab_leads_details.php?showcalls=1&addfacebook=1{$send_profile}&id=".$customer_id);
		}
	else {
		
		write_log($db, 4, "Lead ".$_POST['client_name']." updated by seller ".$_SESSION['u'], $_POST['leadsFORM_updateID'], $_SESSION['id']);
		
		header("Location: tab_leads_details.php?id=".$_POST['leadsFORM_updateID']);
		//header("Location: tab_leads.php?$ret_val=1");		
	}

}
else
	header("Location: tab_leads.php?iserror=1");

?>