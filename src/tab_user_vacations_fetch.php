<?php
session_start();

if (!isset($_SESSION["u"]) || empty($_POST['user_vacation_id'])) {
    echo json_encode(null);
    exit ;
}
else {

//if ($_SESSION['level']!=9)
//	if ($_POST['user_id']!= $_SESSION['id'])
//		die("You dont have permissions to access this area! Ask administrator for more!");
		
	if ($_SESSION['level']!=9 && $_SESSION['level']!=10)
	{	
		echo "you cant administrate this record, ask administrator for more.";
		exit;
	}	
	
}

try {
	include ('config.php');

	$db = connect();

	$r= getRow($db, "SELECT user_vacation_id, user_id, DATE_FORMAT(date_start,'%d-%m-%Y') as date_start, DATE_FORMAT(date_end,'%d-%m-%Y') as date_end, authorized, comment FROM user_vacations where user_vacation_id=?", array($_POST['user_vacation_id']));

	//only admins/CEO can delete the records
	if ($_SESSION['level']!=9 && $_SESSION['level']!=10)
	{	
	 	if ($r['authorized']==1)
	 		{
				echo json_encode(null);
				exit;
			}
	}
	
    //unicode
    header("Content-Type: application/json", true);
	echo json_encode($r);

	
} catch (exception $e) {
    echo json_encode(null);
}
?>