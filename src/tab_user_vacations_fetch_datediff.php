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
	
		$where .= " user_vacations.user_id=".$user;
}
	
if (!empty($where))
	$where = " and ".$where." and authorized=1";
	
	
//$sql = "select DATE_FORMAT(date_start,'%Y-%m-%d') as date_start,DATE_FORMAT(date_end,'%Y-%m-%d') as date_end,DAYOFWEEK(`date_start`) AS `startday`, TIMESTAMPDIFF(DAY, `date_start`, `date_end`) AS `interval` from user_vacations where date_start is not null and date_end is not null".$where;
$sql = "select DATE_FORMAT(date_start,'%Y-%m-%d') as date_start,DATE_FORMAT(date_end,'%Y-%m-%d') as date_end from user_vacations where date_start is not null and date_end is not null".$where;


$rows= getSet($db, $sql, array(null));

$count="";
foreach($rows as $row) {
	//echo $row['date_start'] . ", ".$row['date_end'].'#'.getWorkingDays($row['date_start'],$row['date_end'])."<br>";
	$count+=getWorkingDays($row['date_start'],$row['date_end']);
}

echo json_encode(array("d"=>$count));




function getWorkingDays($startDate, $endDate)	{
	$begin   = strtotime($startDate);
	$end     = strtotime($endDate);

	$no_days = 0;

	while($begin < $end) {
		$what_day = date("N",$begin);

		if($what_day < 6) // 6 and 7 are weekend days
			$no_days++;

		$begin += 86400; // +1 day
	};

	return $no_days;
}

?>