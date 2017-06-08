<?php
session_start();
if ($_SESSION['level']!=9)
	die("You dont have permissions to access this area! Ask administrator for more!");
	

if (!isset($_SESSION["u"]) || empty($_POST['client_id']) || empty($_POST['offer_id'])) {
    echo json_encode(null);
    exit ;
}

require_once ('config.php');

$db = connect();

//validate that is the real owner
$owner_id = getScalar($db, "SELECT owner FROM clients WHERE client_id=?", array($_POST['client_id']));
if ($_SESSION['level']!=9 && $owner_id!=$_SESSION["id"] )
{
	die("you cant administrate this record! ask administrator why!");
}


$sql = "DELETE FROM `offers` WHERE offer_id=:offer_id";
$sth = $db->prepare($sql);
$sth->bindValue(':offer_id', $_POST['offer_id']);
	
$sth->execute();


//delete the proposal.docx + _approval.docx
if ($sth->errorCode()=="00000")
{
//		$company_id = $_POST['client_id'];
//		$filepath1="./proposals/$company_id/".$_POST['offer_id'].".docx";
//		$filepath2="./proposals/$company_id/".$_POST['offer_id']."_approval.docx";
//	
//		unlink($filepath1);
//		unlink($filepath2);
		
		echo "00000";
}
//echo $sth->errorCode(); 
?>