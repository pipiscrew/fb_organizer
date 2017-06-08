<?php

////enable global exception handler
//include ('../../Debug.php');
//
////Catch
//Debug::register();


session_start();

if (isset($_POST['submit']))//If the form has been submitted
{
	include ('config.php');

	$db = connect();

	//try to find @ users table (aka admin/3rd party users)
	//$r = getScalar($db, "SELECT COUNT(ID) FROM users WHERE mail=? AND password=?", array($_POST['email'], $_POST['pass']));
	$r = getRow($db, "SELECT * FROM users WHERE mail=? AND password=?", array($_POST['email'], $_POST['pass']));

	if ($r > 0) {
		date_default_timezone_set("UTC");
		//Login success - set session cookie
		$_SESSION['mail'] = $_POST['email'];
		$_SESSION['reply_mail'] = $r["reply_mail"];
		$_SESSION['u'] = $r['fullname'];
		$_SESSION['id'] = $r['user_id'];
		$_SESSION['level'] = $r['user_level_id'];
		$_SESSION['login_expiration'] = date("Y-m-d");
		$_SESSION['u_sign'] = $r['signature'];
		
		$user_id= $r['user_id'];
		$dt = date("Y-m-d H:i:s");
		
		executeSQL($db,"update users set last_logon=? where user_id=?", array($dt, $user_id));
		
		executeSQL($db,"INSERT INTO `user_working_hours` (user_id, date_start, date_end) VALUES (?,?,?)", array($user_id,$dt,null));
		
//
//		$r = getScalar($db, "SELECT is_sadmin FROM admin_users WHERE mail=? AND password=?", array($_POST['email'], $_POST['pass']));
//		
//		$_SESSION['sadmin'] = $r;
		
		//Redirect the user to a logged in page
		header("Location: index.php");

		//Do not display any more script for this page
		exit ;
	} else
			//Redirect the user to a log in page
			header("Location: admin.html");

} else
	echo "no submit";
?>