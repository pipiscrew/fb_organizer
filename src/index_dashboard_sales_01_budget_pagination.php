<?php
	session_start();

	if (!isset($_SESSION["u"])) {
		header("Location: login.php");
		exit ;
	}
	else if ($_SESSION['level']!=9) {
		die("You are not authorized to view this!");
	}

// include your code to connect to DB.
include ('config.php');

$table_columns = array(
'offer_id',
'is_ended',
'is_start',
'offer_seller_name',
'is_lead',
'url',
'company_id',
'offer_company_name',
'offer_company_manager_name',
'is_paid_when',
'invoice_sent_when',
'offer_telephone',
'gen_total',
'service',
'seen',
'mrkt_campaigner'
);

$conn     = connect();

if (!is_numeric($_GET["limit"]) || !is_numeric($_GET["offset"]))
{
	echo "error";
	exit;
}

$limit = $_GET["limit"];
$offset= $_GET["offset"];

///////////////////////////////////extra-dates
$start_dt = $_GET["start_date"];
$end_dt = $_GET["end_date"];

$where ="";
if (!empty($start_dt) && !empty($end_dt))
{
	$where = " and (is_paid_when BETWEEN '$start_dt' AND '$end_dt')";

}

///////////////////////////extra-combo user
$user= $_GET["user"];

if (!empty($user))
{
        $where .= " and offer_seller_id=".$user;
}
///////////////////////////combo user

$sql="select service_ends >= CURDATE() as is_ended, service_starts > CURDATE() as is_start,CONCAT(DATE_FORMAT(service_starts,'%d-%m-%Y'),' - ',DATE_FORMAT(service_ends,'%d-%m-%Y')) as service,offer_company_name,offer_seller_name,clients.is_lead as is_lead,CONCAT('rec_guid=',rec_guid) as url, offer_company_manager_name,offer_telephone,company_id,offer_id,FORMAT(offer_total_amount,2, 'de_DE') AS gen_total,DATE_FORMAT(invoice_sent_when,'%d-%m-%Y') as invoice_sent_when,DATE_FORMAT(is_paid_when,'%d-%m-%Y') as is_paid_when, '' as mrkt_campaigner
 from offers 
left join clients on clients.client_id = offers.company_id
where is_paid=1 {$where}";
$count_query_sql = "select count(*) from offers where  is_paid=1 {$where}";

$total_query_sql = "select FORMAT(sum(offer_total_amount),2, 'de_DE') as gen_total from offers where is_paid=1 {$where}";

//////////////////////////////////////WHEN SEARCH TEXT SPECIFIED
if (isset($_GET["search"]) && !empty($_GET["search"]))
{
	$sdafsd= $_GET["search"];

	$like_str = " or #field# like :searchTerm";
	$where = " 0=1 ";

	foreach($table_columns as $col)
	{
		$where.= str_replace("#field#",$col, $like_str);
	}

	$sql.= " where ". $where;
	$count_query_sql.= " where ". $where;
}

//////////////////////////////////////WHEN SORT COLUMN NAME SPECIFIED
if (isset($_GET["name"]) && isset($_GET["order"]))
{
	$name= $_GET["name"];
	$order= $_GET["order"];

	$sql.= " order by {$_GET["name"]} {$_GET["order"]}";
}


//////////////////////////////////////PREPARE
$stmt = $conn->prepare($sql." limit :offset,:limit");

//////////////////////////////////////WHEN SEARCH TEXT SPECIFIED *BIND*
if (isset($_GET["search"]) && !empty($_GET["search"]))
	$stmt->bindValue(':searchTerm', '%'.$_GET["search"].'%');

//////////////////////////////////////WHEN COLSORT
//if (isset($_GET["name"]) && isset($_GET["order"]))
//{
//	$stmt->bindValue(':col_name', $name);
//	$stmt->bindValue(':col_order', $order);
//}

//////////////////////////////////////PAGINATION SETTINGS
$stmt->bindValue(':offset' , intval($offset), PDO::PARAM_INT);
$stmt->bindValue(':limit' , intval($limit), PDO::PARAM_INT);

//////////////////////////////////////FETCH ROWS
$stmt->execute();

$rows_sql = $stmt->fetchAll();

$rows     = array();
$x=-1;
foreach($rows_sql as $row_key){
	$x+=1;
	
	for($i = 0; $i < count($table_columns); $i++)
	{
		$rows[$x][$table_columns[$i]] = $row_key[$table_columns[$i]];
	}
	
	
	$rows[$x]['seen'] = "<span class='label label-primary'>{$rows[$x]['seen']}</span>"; 

}

//////////////////////////////////////COUNT TOTAL 
if (isset($_GET["search"]) && !empty($_GET["search"]))
	$count_recs = getScalar($conn, $count_query_sql, array(':searchTerm' => '%'.$_GET["search"].'%'));
else
	$count_recs = getScalar($conn, $count_query_sql, null);


$total_amount = getScalar($conn,$total_query_sql,null);

//////////////////////////////////////JSON ENCODE
$arr = array('total'=> $count_recs,'rows' => $rows,'total_amount' => $total_amount);

header("Content-Type: application/json", true);

echo json_encode($arr);

?>