<?php
session_start();

if (!isset($_SESSION["u"])) {
	header("Location: login.php");
	exit ;
}

if ($_SESSION['level']!=9)
	if ($_POST['user_id']!= $_SESSION['id'])
		die("You dont have permissions to access this area! Ask administrator for more!");
	
//if ($_SESSION['level']!=9)
//	die("You dont have permissions to access this area! Ask administrator for more!");
	
 
if (!isset($_POST['date_start']) || !isset($_POST['date_end']) || !isset($_POST['comment'])){
	echo "error010101010";
	return;
}
 
//DB
require_once ('config.php');
 
$db = connect();
 

$authorized = "0";

if (isset($_POST['authorized'])) {
	if ($_POST['authorized'] == "on")
		$authorized = 1;
	else
		$authorized = "0";
}



$date_start=null;
if (!empty($_POST['date_start']))
{
	$dt = DateTime::createFromFormat('d-m-Y', $_POST['date_start']);
	
	$date_start =	$dt->format('Y-m-d');
}

$date_end=null;
if (!empty($_POST['date_end']))
{
	$dt = DateTime::createFromFormat('d-m-Y', $_POST['date_end']);
	
	$date_end =	$dt->format('Y-m-d');
}

$v = getScalar($db,"
select count(*) from offers where marketing_plan_when is not null
and marketing_plan_when between ? and ?",array($date_start,$date_end));

if ($v>0)
{
	echo "marketplan";
	exit;
}

if(isset($_POST['user_vacationsFORM_updateID']) && !empty($_POST['user_vacationsFORM_updateID']))
{
	$sql = "UPDATE user_vacations set date_start=:date_start, date_end=:date_end, authorized=:authorized, comment=:comment where user_vacation_id=:user_vacation_id";
	$stmt = $db->prepare($sql);
	$stmt->bindValue(':user_vacation_id' , $_POST['user_vacationsFORM_updateID']);
}
else
{
	$sql = "INSERT INTO user_vacations (user_id, date_start, date_end, authorized, comment) VALUES (:user_id, :date_start, :date_end, :authorized, :comment)";
	$stmt = $db->prepare($sql);
	$stmt->bindValue(':user_id' , $_POST['user_id']);
}


$stmt->bindValue(':date_start' , $date_start);
$stmt->bindValue(':date_end' , $date_end);
$stmt->bindValue(':authorized' , $authorized, PDO::PARAM_INT);
$stmt->bindValue(':comment' , $_POST['comment']);

$stmt->execute();
 
echo $stmt->errorCode(); 
?>