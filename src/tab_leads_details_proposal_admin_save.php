<?php
session_start();

if (!isset($_SESSION["u"]) || !isset($_POST["pay_date"])) {
    echo json_encode(null);
    exit ;
}
 
if (!isset($_POST['offersFORM_updateID'])){
	echo "error010101010";
	return;
}
 
//DB
require_once ('config.php');
 require_once ('config_general.php');
 
$db = connect();
 
$is_lead = 1;
$is_paid = "0";

if (isset($_POST['is_paid'])) {
	if ($_POST['is_paid'] == "on")
	{	
		$is_paid = 1;
		$is_lead="0";
	}
	else
		{
			$is_paid = "0";
			$is_lead=1;
		}
	
}

$pay_date=null;
if (!empty($_POST['pay_date']))
{
    //convert html control 24h date - to PHP 24h format date
    $dt = DateTime::createFromFormat('d-m-Y', $_POST['pay_date']);
    //set to variable a string date formatted as mySQL likes!
    $pay_date = $dt->format('Y-m-d');
}


	//GET CLIENT ID BY OFFER
	$cust_id = getScalar($db,"select company_id from offers where offer_id=?",array($_POST['offersFORM_updateID']));

	//UPDATE OFFER AS PAID
	$sql = "UPDATE offers set is_paid=:is_paid, is_paid_when=:is_paid_when where offer_id=:offer_id";

	$stmt= $db->prepare($sql);
	$stmt->bindValue(':offer_id' , $_POST['offersFORM_updateID']);
	$stmt->bindValue(':is_paid' , $is_paid, PDO::PARAM_INT);
	$stmt->bindValue(':is_paid_when' , $pay_date);

	$stmt->execute();

	$res = $stmt->errorCode();

	if($res != "00000"){
		echo "There was an error! Cant set offer is paid, errorcode : ".$res;
		exit;
	}

	///////////////////// UPDATE CLIENT AFTER ^ SUCCESS UPDATE TO PAID ^
	$sql = "UPDATE clients set is_lead=:is_lead where client_id=:client_id";

	$stmt= $db->prepare($sql);
	$stmt->bindValue(':is_lead' , $is_lead, PDO::PARAM_INT);
	$stmt->bindValue(':client_id' , $cust_id);
	$stmt->execute();

	$res = $stmt->errorCode();

	if($res == "00000")
	{
//		$u_mail = getScalar($db,"select users.mail from offers left join users on users.user_id = offers.offer_seller_id where offer_id=?",array($_POST['offersFORM_updateID']));
		$offer_row = getRow($db,"select users.mail as mail, company_id, offer_company_name, offer_seller_id from offers left join users on users.user_id = offers.offer_seller_id where offer_id=?",array($_POST['offersFORM_updateID']));
		$u_mail = $offer_row["mail"];
		$u_id = $offer_row["offer_seller_id"];
		$company_id = $offer_row["company_id"];
		$company_txt = $offer_row["offer_company_name"];

		send_mail_to_user($u_mail,"Proposal is now paid for client : ".$company_txt, "You can now update client details and services dates<br><br>http://localhost:8080/api/tab_clients_details.php?showcontracts=1&id=$company_id");
		write_log($db,1,"Proposal PAID for client:".$company_txt,$company_id,$u_id);
		write_log($db,4,"Proposal PAID for client:".$company_txt,$company_id,$u_id);
		
		if (isset($_POST["dont_redirect"]) && $_POST["dont_redirect"]==1) //when coming by tab_dashboard_seller_payments_pending.php
			echo "1";
		else {
			if ($is_lead==1)
				header("Location: tab_leads_details.php?id=".$cust_id);
			else 
				header("Location: tab_clients_details.php?showcontracts=1&id=".$cust_id);
		}
	}
	else
		echo "There was an error! Cant set offer is paid, errorcode : ".$res;
	
	//header("Location: tab_leads.php?$ret_val=1");
?>