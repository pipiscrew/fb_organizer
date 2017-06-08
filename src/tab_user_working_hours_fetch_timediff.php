<?php

if (!isset($_POST["user"]) || !isset($_POST["month"]))
{
	echo json_encode("0101");
	exit;
}


$month=$_POST["month"];
$user=$_POST["user"];

if (!is_numeric($user) || !is_numeric($month))
{
	echo json_encode("0202");
	exit;
}

include ('config.php');

$db = connect();

$where="";

if (!empty($month))
{
	$year = date('Y'); //this year
	
	$month_calc = $month+1; //increase by 1
	$start_date = date("$year-$month_calc-01"); //convert to date
	$mod_date = strtotime($start_date."- 1 day"); //subtract -1!
	$m = date("Y-m-d",$mod_date); //format back to mysql style!

	//construct the query string!
	$where = " date_start BETWEEN '$year-$month-01' AND '$m'";	
	//$where = " date_start BETWEEN '2014-$month-01' AND '2014-$month-31'";
}
	
if (!empty($user))
{
	if (!empty($where))
		$where .= " and ";
	
		$where .= " user_working_hours.user_id=".$user;
}
	
if (!empty($where))
	$where = " where ".$where;
	
	
$sql = "SELECT SUM(t) 
FROM (
  SELECT TIME_TO_SEC(TIMEDIFF(date_end,date_start)) as t
  FROM user_working_hours ".$where. " 
) hours;";


$r= getScalar($db, $sql, array(null));

$hours = floor($r / 3600);
$mins = floor(($r - ($hours*3600)) / 60);
	
echo json_encode(array("h" => $hours, "m" => $mins));

?>