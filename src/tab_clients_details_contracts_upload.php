<?php
session_start();

if (!isset($_SESSION["u"]) || empty($_POST['offer_id']) || empty($_POST['client_id'])) {
	header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
    exit ;
}
else {
	
//	$r = strtolower($_FILES["myfile"]["name"]);
//	if (!endsWith($r,".docx"))
//	{
//		$ret= array();
//		$ret['jquery-upload-file-error']='Accepts only .docx';
//		echo json_encode($ret);
//		exit;
//	}
}



// include DB
require_once ('config.php');
require_once ('config_general.php');


//$r = getScalar($db, "select proposal_approval_attachment from offers where offer_id=?", array($_POST['offer_id']));
//
//if ($r!="null")
//{
//
//		$ret= array();
//		$ret['jquery-upload-file-error']=$e->errorMessage();
//		echo json_encode($ret);
//exit;
//}

//
//if ( empty($_POST['offer_id']))
//{
//    echo json_encode("e1");
//    exit ;
//}
//
//if ( empty($_POST['client_id']))
//{
//    echo json_encode("e2");
//    exit ;
//}

$company_id = $_POST['client_id'];
$output_dir="./proposals/$company_id/";
$fileName = $_POST['offer_id']."_plan.pptx";

if (file_exists($output_dir.$fileName))
{
		$ret= array();
		$ret['jquery-upload-file-error']='MarketPlan file already exists!';
		echo json_encode($ret);
		exit;
}

//if dir NOT exists
if (!file_exists($output_dir)){
		$ret= array();
		$ret['jquery-upload-file-error']='Folder doesnt exist at '.$output_dir." (aka where is the proposal?)";
		echo json_encode($ret);
		exit;
		
	  //  die('Folder doesnt exist at '.$output_dir);
}


if(isset($_FILES["myfile"]))
{
	$ret = array();
	
//	This is for custom errors;	
/*	$custom_error= array();
	$custom_error['jquery-upload-file-error']="File already exists";
	echo json_encode($custom_error);
	die();
*/
try {
	$error =$_FILES["myfile"]["error"];
	//You need to handle  both cases
	//If Any browser does not support serializing of multiple files using FormData() 
	if(!is_array($_FILES["myfile"]["name"])) //single file
	{
// 	 	$fileName = $_FILES["myfile"]["name"];
 		move_uploaded_file($_FILES["myfile"]["tmp_name"],$output_dir.$fileName);
 		
		$db = connect();
		executeSQL($db,"update offers set marketing_plan_attachment=1 where offer_id=?", array($_POST['offer_id']));

    	$ret[]= $fileName;
    	
    	$company_name = getScalar($db, "select client_name from clients where client_id=?", array($company_id));
    	
    	write_log($db, 4, "Marketing Plan uploaded for client : $company_name by user : ".$_SESSION['u'], $company_id, $_SESSION['id']);
	}
	
	}
	catch (Exception $e) {
		$ret= array();
		$ret['jquery-upload-file-error']=$e->errorMessage();
	}
    echo json_encode($ret);
 }
 
// function endsWith($haystack, $needle)
//{
//    $length = strlen($needle);
//    if ($length == 0) {
//        return true;
//    }
//
//    return (substr($haystack, -$length) === $needle);
//}

 ?>