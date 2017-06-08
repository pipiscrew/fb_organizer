<?php
session_start();

require_once ('template_top.php');

include ('config.php');
include ('config_general.php');

//only admim can see the page
if ($_SESSION['level']!=9)
		die("You are not authorized to view this!");
		
$db=connect();

$users_rows=null;
///////////////////READ users
$users_rows = getSet($db, "SELECT * FROM `users` order by user_level_id",null);
///////////////////READ users

//get users
$chart_db_users = getSet($db,"select user_id,fullname from users where user_level_id in (1,2,9)",null);

$chart_row_setup = array();

$chart_columns_setup= array();
$chart_columns_setup[]="Month";
for( $i= 0 ; $i <= sizeof($chart_db_users)-1 ; $i++ )
{
	$chart_columns_setup[]	= $chart_db_users[$i]["fullname"];
}

//always on 0 array position is the bars names
$chart_row_setup[0] = $chart_columns_setup;



//below merge the bars values
$col_vals= array();

//for each month
for( $m= 1 ; $m <= 12 ; $m++ )
{
	$col_vals= array();
	
	$col_vals[]=monthName($m);
	
	//construct valid date for mySQL
	if ($m<10)
	{
		$start = date("Y-0{$m}-01");

		$end =  get_end_of_the_month($m,date('Y'));
	}
	else 
	{	$start = date("Y-{$m}-01");
		$end =  get_end_of_the_month($m,date('Y'));
	}
	
	//for each user
	for( $i= 0 ; $i <= sizeof($chart_db_users)-1 ; $i++ )
	{
		//query with date between depends on $m variable
		$col_vals[] = (float) getScalar($db,"select ifNull(score, 0) from user_scores where user_id =".$chart_db_users[$i]['user_id']." and score_when between '".$start."' and '".$end."'",null);
	}

	$chart_row_setup[]=$col_vals;
}
	

function monthName($month_int) {
	$month_int = (int)$month_int;
	$timestamp = mktime(0, 0, 0, $month_int);
	return date("F", $timestamp);
}

?>

    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript">
   
      google.load("visualization", "1.1", {packages:["bar"]});
      google.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = google.visualization.arrayToDataTable(          <?= json_encode($chart_row_setup);?>        );

        var options = {
          chart: {
            title: 'Company Performance',
            subtitle: 'Scores',
          }
        };

        var chart = new google.charts.Bar(document.getElementById('columnchart_material'));

        chart.draw(data, options);
      }
    </script>
    
    
<!-- Main content -->
<section class="content">

<div id="columnchart_material" style="height: 300px;"></div>