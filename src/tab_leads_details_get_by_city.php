<?php

session_start();



if (!isset($_SESSION["u"])) {

	header("Location: login.php");

	exit ;

}





//include ('../../Debug.php');

//

////Catch

//Debug::register();



// include DB

require_once ('config.php');



if(!isset($_POST["valid"]))

{

	echo json_encode(null);

	return;

}





$colWhereVAL         = $_POST["valid"];





$conn                = connect();



$recs               = getSet($conn,"select client_sector_sub_id as ID, client_sector_sub_name as DESCR from client_sector_subs where client_sector_id=?",array($colWhereVAL));



$json                = array('recs'=> $recs);



header("Content-Type: application/json", true);



echo json_encode($json);