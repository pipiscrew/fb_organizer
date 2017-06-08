<?php
	session_start(); //to ensure you are using same session

	if (!isset($_SESSION["u"])) {
		header("Location: login.php");
		exit ;
	}
	
	date_default_timezone_set("UTC");

	// include DB
	require_once ('config.php');

	$db = connect();

	//on logout write it to dbase
	$u_id=$_SESSION['id'];	
	
	//query the last user record from table (aka where date_end is null)
	$id = getScalar($db, "select user_working_hour_id from user_working_hours where date_end is null and user_id=? order by user_working_hour_id DESC limit 1",array($u_id));
	
	if(isset($_GET["reason"]))
		$reason = $_GET["reason"];
	else
		$reason = null;
	

	
	$logout_type = $_GET["type"];
	

	
	if(empty($logout_type)){
		die("no logout type describe, operation aborted!\r\n\r\nPlease logout again!");
	}
	
	//1-LOGOUT
	//2-APPOINTMENT
	//3-EMERGENCY

	//update the date_end for the found record^
	$affected = executeSQL($db, "update user_working_hours set date_end=?,logout_type=?,reason=? where user_working_hour_id=?", array(date("Y-m-d H:i:s"),$logout_type,$reason, $id));
	
	if ($logout_type==2) //when the logout is appointment
	{
				executeSQL($db,"INSERT INTO `user_working_hours` (user_id, date_start, date_end,logout_type,reason) VALUES (?,?,?,?,?)", array($_SESSION['id'],date("Y-m-d H:i:s"),null,$logout_type,$reason));	
	}
	
	//DISCUSS WITH NICK?
	//when there is no update, insert new... (aka will not contain datestart)
	if($affected == 0){
			$affected = executeSQL($db, "INSERT INTO `user_working_hours` (user_id, logout_type, reason, date_end) VALUES (?,?,?,?)", array($u_id,$logout_type,$reason,date("Y-m-d H:i:s")));
	}

	//LOGOUT
	session_destroy(); //destroy the session
	header("location: login.php"); //to redirect back to login
	exit();
?>