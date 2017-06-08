<?php
session_start();

if (!isset($_SESSION["u"])) {
    header("Location: index.html");
    exit ;
}


// include your code to connect to DB.
include ('config.php');

$table_columns = array(
'client_invoice_detail_id',
'client_id',
'company_name',
'occupation',
'address',
'pobox',
'city',
'country_id',
'vat_no',
'tax_office_id',

);

$conn     = connect();

if (!is_numeric($_GET["limit"]) || !is_numeric($_GET["offset"]))
{
	echo "error";
	exit;
}

$limit = $_GET["limit"];
$offset= $_GET["offset"];


$sql="select client_invoice_detail_id, client_id, company_name, occupation, address, pobox, city, countries.country_name as country_id, vat_no, tax_offices.tax_office_name as tax_office_id from client_invoice_details 
 LEFT JOIN countries ON countries.country_id = client_invoice_details.country_id
 LEFT JOIN tax_offices ON tax_offices.tax_office_id = client_invoice_details.tax_office_id where client_id=".$_GET["clientid"];
$count_query_sql = "select count(*) from client_invoice_details where client_id=".$_GET["clientid"];

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

	$sql.= " and ( ". $where .")";
	$count_query_sql.= " and ( ". $where.")";
}

//////////////////////////////////////WHEN SORT COLUMN NAME SPECIFIED
if (isset($_GET["name"]) && isset($_GET["order"]))
{
	$name= $_GET["name"];
	$order= $_GET["order"];

	$sql.= " order by :col_name :col_order";
}


//////////////////////////////////////PREPARE
$stmt = $conn->prepare($sql." limit :offset,:limit");

//////////////////////////////////////WHEN SEARCH TEXT SPECIFIED *BIND*
if (isset($_GET["search"]) && !empty($_GET["search"]))
	$stmt->bindValue(':searchTerm', '%'.$_GET["search"].'%');

//////////////////////////////////////WHEN COLSORT
if (isset($_GET["name"]) && isset($_GET["order"]))
{
	$stmt->bindValue(':col_name', $name);
	$stmt->bindValue(':col_order', $order);
}

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
	
}

//////////////////////////////////////COUNT TOTAL 
if (isset($_GET["search"]))
	$count_recs = getScalar($conn, $count_query_sql, array(':searchTerm' => '%'.$_GET["search"].'%'));
else
	$count_recs = getScalar($conn, $count_query_sql, null);

//////////////////////////////////////JSON ENCODE
$arr = array('total'=> $count_recs,'rows' => $rows);

header("Content-Type: application/json", true);

echo json_encode($arr);

?>