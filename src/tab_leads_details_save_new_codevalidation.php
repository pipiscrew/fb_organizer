<?php
session_start();

if (!isset($_SESSION["u"]) || !isset($_POST['is_lead']) || !isset($_POST['client_code'] )) {
	header("Location: login.php");
	exit ;
}

require_once ('config.php');

$db = connect();

$double_arr = array();
	
if (isset($_POST['telephone']) && isset($_POST['mobile']) && isset($_POST['client_name']) && isset($_POST['website']))
{
	
	$res = getRow($db, "select telephone,mobile,client_name,website from clients
						   where (length(telephone) > 0 and telephone=?) or (length(mobile) > 0 and mobile=?) or client_name=? or (length(website) > 0 and website=?)", 
						   array($_POST['telephone'], $_POST['mobile'], $_POST['client_name'], $_POST['website']));
	
	if ($res!=null) {
		
		if (strlen($_POST['telephone'])>0 && $res['telephone'] == $_POST['telephone'])
			$double_arr[] = "telephone";
			
		if (strlen($_POST['mobile'])>0 && $res['mobile'] == $_POST['mobile'])
			$double_arr[] = "mobile";
					
		if ($res['client_name'] == $_POST['client_name'])
			$double_arr[] = "client name";
			
		if (strlen($_POST['website'])>0 &&$res['website'] == $_POST['website'])
			$double_arr[] = "website";
		
		
	//	echo json_encode(array("doublecheck" => $double_arr, "code" => 0));
		
//		exit;
	}

}

$res = getScalar($db, "select count(client_id) from clients where is_lead=? and client_code=?", array($_POST['is_lead'], $_POST['client_code']));

$result="";

if ($res>0)
{
	//7/1/2015-here new code exists @ lead form, suggest new one!
	
	$result = getScalar($db, "select max(client_code)+1 from clients", null);
}
else 
{
	$result = 0;
}


echo json_encode(array("doublecheck" => $double_arr, "code" => $result));

?>