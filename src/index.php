<?php
include ('template_top.php');

require_once ('config.php');
require_once ('config_general.php');

$db   = connect();

$now  = date("Y-m-d");

$m    = date("n.j");
$rows = getSet($db, "select * from namedays where day=?", array($m));
?>

<link href='http://fonts.googleapis.com/css?family=Anton|Skranji' rel='stylesheet' type='text/css'/>


<style type="text/css">
	table.gridtable {
		font-family: verdana,arial,sans-serif;
		font-size:11px;
		color:#333333;
		border-width: 1px;
		border-color: #c0c0c0;
		border-collapse: collapse;
		width:95%;
		margin: 0 auto;
	}
	table.gridtable th {
		border-width: 1px;
		padding: 8px;
		border-style: solid;
		border-color: #c0c0c0;
	}
	table.gridtable td {
		border-width: 1px;
		padding: 8px;
		border-style: solid;
		border-color: #c0c0c0;
	}
	table.scoretable {
		font-family: verdana,arial,sans-serif;
		font-size:11px;
		color:#333333;
		border-width: 1px;
		border-color: #c0c0c0;
		border-collapse: collapse;
		width:100px;
	}
	table.scoretable th {
		border-width: 1px;
		padding: 8px;
		border-style: solid;
		border-color: #c0c0c0;
		font-family: 'Anton', sans-serif;
		font-size: xx-large;
		text-align:center;
	}
	table.scoretable td {
		border-width: 1px;
		padding: 8px;
		border-style: solid;
		border-color: #c0c0c0;
		text-align:center;
		font-family: 'Skranji', cursive;
		font-size: xx-large;
	}
</style>

		<!--speedometer-->
    <script src="js/raphael.2.1.0.min.js"></script>
    <script src="js/justgage.1.0.1.min.js"></script>
    
    <script>

	function get_stats(){
 	   var selected = $('#cmb_user').find('option:selected');
       var user_level = selected.data('level'); 
       var is_department = selected.data('is_department'); 
       
//       console.log(user_level, is_department,$('#cmb_user').val());
//       
       if (is_department)
			is_department=1;
		else 
			is_department=0;
       		
		var stats_filename;
		
		//pickup the correct stats file
		switch (user_level){
			case 1:case 2:case 9: 
				stats_filename = "index_dashboard_sales.php";
				break;
			default :
				stats_filename = "";
		}
		
		if (!stats_filename)
		{
			alert ("Unkown status filename!");
			return ;
		}
			
		//change image - if doesnt exist fallback to .error
		$("#img_user").attr("src", "img_users/" + $('#cmb_user').val() + ".png");
			 
			 
			console.log("user_id=" + $('#cmb_user').val()+"&is_department="+is_department);
		//ajax - user_id used only when admin
	    $.ajax(
	    {
	        url : stats_filename,
	        type: "POST",
	        data : "user_id=" + $('#cmb_user').val()+"&is_department="+is_department,
	        success:function(data, textStatus, jqXHR)
	        {
	        	if (data=="empty")
	        		{
	        			alert("No people found in this department!");
	        			return;
	        		}
	        	//console.log(data);
	        	eval(data);
	        },
	        error: function(jqXHR, textStatus, errorThrown)
	        {
	            alert("Cant get dashboard stats\r\n\r\nERROR - connection error");
	        }
	    });
	}

	
			$(function ()
				{
				//callback onerror (if image doesnt exist)
				$("#img_user").error(function() {
				  this.src = 'img_users/doesntexist.png'; // replace with other image
				});


<?php if ($_SESSION['level']==9) {	?>
				$('#cmb_user').on('change', function() {
					get_stats();
				});
<?php } ?>

	
				//find user on combo
				$('#cmb_user').val(<?=$_SESSION['id'];?>);
				
				//get stats for user
				get_stats();
						
				})//jQuery ends
	</script>
				  
<!-- Content Header (Page header) -->
<section class="content-header">
	<?php
	if(isset($_SESSION['open_session_auto']) && $_SESSION['open_session_auto'] == 1)
	{
		$_SESSION['open_session_auto'] = null;
		?>

		<div class="alert alert-success" id="alertBOX" >
			Automatically a record added to log, that you returned back from the appointment!
		</div>
		<?php
	} ?>

</section>

<div id="js_dynamic"></div>

<!-- Main content -->
<section class="content">

<div class="row">

	<div class="col-md-6">
		<div class="box box-solid">
			<!--<div class="box-header">
				<i class="fa fa-text-width">
				</i>
				<h3 class="box-title">
					User Information
				</h3>
			</div> /.box-header -->
			<div class="box-body">
			<div class="row">
				<div class="col-md-3">
					<table class="scoretable">
						<thead>
							<tr>
								<th>
									Score
								</th>
							</tr>
						</thead>

						<tbody>
							<tr>
								<td id="score_txt">
									
								</td>
							</tr>
						</tbody>
					</table>
				</div>
				<div class="col-md-3">
					<img id="img_user" src="img_users/<?php if (!file_exists('img_users'.DIRECTORY_SEPARATOR.$_SESSION['id'].'.png')) echo 'doesntexist'; else echo $_SESSION['id']; ?>.png" width="180px" height="180px">
				</div>
				<div class="col-md-6">
						<ul>
							<li>
								Your Name : <?= $_SESSION['u']; ?>
							</li>
							<li>
								Your Level : <?= getScalar($db,"select user_level_name from user_levels where user_level_id=?",array($_SESSION['level'])); ?>
							</li>
							<li>
								Your Email : <?= $_SESSION['mail']; ?>
							</li>
                            <li>Your Monthly Hours out of <?php 

										$x = new SimpleWorkingDays();
										$days = $x->get_month_working_days(date('n'), date('Y')); 
										echo $days * 8;
										$seconds = getScalar($db,"SELECT SUM(t)
										FROM (
										  SELECT TIME_TO_SEC(TIMEDIFF(date_end,date_start)) as t
										  FROM user_working_hours where user_id=?
										) hours;",array($_SESSION['id']));

										$hours = floor($seconds / 3600);
										$mins = floor(($seconds - ($hours*3600)) / 60);
										$secs = floor($seconds % 60);
										echo " : ".$hours . "h " . $mins . "m"

                             ?>
							</li>
                            <li>Holidays so far for <?= date('Y', strtotime('+1 years')); ?> : 
                            <?php
                            $d_start= date("Y-01-01");
                            $d_end= date("Y-12-31");
                            $row_set=getSet($db, "select date_start, date_end from user_vacations where user_id=".$_SESSION['id']." and authorized=1 and date_end between '{$d_start}' and '{$d_end}'",null);
                            $count_working_days = 0;
                            
							foreach($row_set as $row) {
								$count_working_days.=$x->get_month_working_days_between_range($row["date_start"],$row["date_end"]);
							}
							
							echo $count_working_days. " days";?></li>
                                        							
						</ul>
			</div><!-- /row inside>col2 -->
</div><!-- /row inside -->
			</div><!-- /.box-body -->
		</div><!-- /.box -->
	</div>
	
	<!-- right box -->
	
	<div class="col-md-6">
		<div class="box box-solid">
			<div class="box-header">
				<i class="fa fa-text-width">
				</i>
				<h3 class="box-title">
					Namedays
				</h3>
			</div>
			<div class="box-body">
			<div class="row">
				<div class="col-md-6" style="padding-left: 50px">
					<?php
					$names_row = "";
					echo "<h4>Today ".date("j/n")."</h4>";
					echo "<p style='padding-left: 30px;'>";
					foreach($rows as $row)
					{
						$names_row .= $row['names'] . ", ";
					}

					if(strlen($names_row) > 0)
					$names_row = substr($names_row,0, strlen($names_row) - 2);
					else
					$names_row = "No fiesta.";

					echo $names_row."</p>";
					?>
				</div>
				<div class="col-md-6">
					<?php
					$mod_date = strtotime($now."+ 1 days");
					$names_row= "";
					$m        = date("n.j",$mod_date);
					echo "<h4>Tomorrow ".date("j/n",$mod_date)."</h4>";
					echo "<p style='padding-left: 30px;'>";
					$rows = getSet($db, "select * from namedays where day=?", array($m));
					foreach($rows as $row)
					{
						$names_row .= $row['names'] . ", ";
					}

					if(strlen($names_row) > 0)
					$names_row = substr($names_row,0, strlen($names_row) - 2);
					else
					$names_row = "No fiesta.";

					echo $names_row."</p>";
					?>
			</div><!-- /row inside>col2 -->
</div><!-- /row inside -->
			</div><!-- /.box-body -->
		</div><!-- /.box -->
	</div>
</div>


<div class="row">

	<div class="col-md-6">
		<div class="box box-solid">
			<div class="box-header">
				<i class="fa fa-text-width">
				</i>
				<h3 class="box-title">
					Sales
				</h3>
			</div>
			<div class="box-body">
			
				<select id="cmb_user" <?php if ($_SESSION['level']!=9) echo "style='display:none;'"?> >
				<option value='-1'></option>
				
<!--
				level - used to indentify which php will use (needed for switch) aka contains the first value of ex "1,2"
				value - which user will select on PHP file having user_level the value
-->
				<option value='1,2' data-level='1' data-is_department="1">Sales Department</option>
				<option value='4,5' data-level='4' data-is_department="1">Marketing Department</option>
<?php

$users_set = getSet($db,"select user_id, user_level_id, fullname from users order by fullname",null);

foreach($users_set as $row) {
	echo "<option value='".$row["user_id"]."' data-level='".$row["user_level_id"]."'>".$row["fullname"]."</option>";
}

?>
				</select>
				
				<div class="row">					
					<table  id="sales_tbl" class="gridtable">
						<thead>
							<tr>
								<th>Type</th>
								<th>YDay</th>
								<th>Day</th>
								<th>Week</th>
								<th>Month</th>
								<th>3months</th>
								<th>6months</th>
								<th>9months</th>
								<th>12months</th>
								<th>Graph</th>
							</tr>
						</thead>

						<tbody id="sales_rows"></tbody>
					</table>
				</div>
				
			</div><!-- /.box-body -->
		</div><!-- /.box -->
	</div>
	
	<div class="col-md-6">
		<div class="box box-solid">
			<div class="box-header">
				<i class="fa fa-text-width">
				</i>
				<h3 class="box-title">
					Quick Actions 
				</h3>
				<a href="http://localhost:8080/api/tab_user_vacations.php" style="margin:10px;float: right" type="button" class="btn btn-default btn-sm">Take a holiday OFF</a>

<?php if ($_SESSION["level"]==9) { ?>

				<a href="http://localhost:8080/tinytodo/" target="_blank" style="margin:10px;float: right" type="button" class="btn btn-success btn-sm">TODO</a>
				<!--<a href="http://localhost:8080/api/aa.php" target="_blank" style="margin:10px;float: right" type="button" class="btn btn-success btn-sm">TODO</a>-->
<?php } ?>
				
			</div>
			<div class="box-body">
				<div class="row" style="margin-left: 10px;margin-bottom: 10px">		

					<div class="col-md-3">
					<a href="http://localhost:8080/api/tab_leads_details.php" style="width:175px" type="button" class="btn btn-primary btn-lg">New Lead</a>
					</div>
					<div class="col-md-3">
					<a href="http://localhost:8080/api/tab_leads.php" style="width:175px" type="button" class="btn btn-success btn-lg">Leads List</a>
					</div>
					<div class="col-md-3">
					<a href="http://localhost:8080/api/tab_clients.php" style="width:175px" type="button" class="btn btn-info btn-lg">Client List</a>
					</div>
					<div class="col-md-3">
					<a href="javascript:alert('no set');" style="width:175px" type="button" class="btn btn-warning btn-lg">Client Marketing</a>
					</div>
				
				</div>
				
				<div class="row"  style="margin-left: 10px;margin-bottom: 10px">		
					<div class="col-md-3" >
					<a href="tab_dashboard_seller_calls.php" style="width:175px" type="button" class="btn btn-danger btn-lg">Calls To Do</a>
					</div>
					<div class="col-md-3">
					<a href="tab_dashboard_seller_renewals.php" style="width:175px" type="button" class="btn btn-warning btn-lg">Renewals Weekly</a>
					</div>
					<div class="col-md-3">
					<button style="width:175px" type="button" class="btn btn-primary btn-lg">Todo</button>
					</div>
					<div class="col-md-3">
					<button style="width:175px" type="button" class="btn btn-success btn-lg">Education Level</button>
					</div>
				</div>
				
				<div class="row"  style="margin-left: 10px;margin-bottom: 10px">		
					<div class="col-md-3" >
						<a href="tab_dashboard_seller_calls_today.php" style="width:175px" type="button" class="btn btn-danger btn-lg">Calls Done</a>
					</div>
					<div class="col-md-3" >
						<a href="tab_dashboard_seller_appointments.php" style="width:175px" type="button" class="btn btn-danger btn-lg">Appointments</a>
					</div>
					<div class="col-md-3" >
						<a href="tab_dashboard_seller_fb_requests.php" style="width:175px" type="button" class="btn btn-success btn-lg">Facebook Requests</a>
					</div>
				</div>
				
				
<?php if ($_SESSION['level']==9) { ?>
				<div class="row"  style="margin-left: 10px;margin-bottom: 10px">
					<div class="col-md-3">
						<a href="tab_dashboard_seller_admin_payment_pendings.php" style="width:175px" type="button" class="btn btn-primary btn-lg">Payment Pendings</a>
					</div>
					<div class="col-md-3" >
						<a href="tab_dashboard_seller_admin_proposal_pendings.php" style="width:175px" type="button" class="btn btn-success btn-lg">Proposal Pendings</a>
					</div>
					<div class="col-md-3">
						<a href="tab_dashboard_seller_admin_paid_list.php" style="width:175px" type="button" class="btn btn-warning btn-lg">Paid List</a>
					</div>
					<div class="col-md-3">
						<a href="tab_dashboard_admin_big_brother.php" style="width:175px" type="button" class="btn btn-primary btn-lg">Big Brother</a>
					</div>
				</div>
				
				<div class="row"  style="margin-left: 10px">
					<div class="col-md-3">
						<a href="tab_dashboard_admin_contracts_live.php" style="width:175px" type="button" class="btn btn-primary btn-lg">Contracts Beta!</a>
					</div>
					<div class="col-md-3" >
						<a href="tab_dashboard_admin_scores.php" style="width:175px" type="button" class="btn btn-success btn-lg">Scores</a>
					</div>
					<div class="col-md-3">
						<a href="tab_dashboard_admin_hr.php" style="width:175px" type="button" class="btn btn-warning btn-lg">HR</a>
					</div>
				</div>
<?php } else if ($_SESSION['level']==1||$_SESSION['level']==2) {?>
				<div class="row"  style="margin-left: 10px">
					<div class="col-md-3">
						<a href="tab_dashboard_seller_payment_pendings.php" style="width:175px" type="button" class="btn btn-primary btn-lg">Payment Pendings</a>
					</div>
					<div class="col-md-3" >
						<a href="tab_dashboard_seller_proposal_pendings.php" style="width:175px" type="button" class="btn btn-success btn-lg">Proposal Pendings</a>
					</div>
					<div class="col-md-3">
						<a href="tab_dashboard_seller_paid_list.php" style="width:175px" type="button" class="btn btn-warning btn-lg">Paid List</a>
					</div>
				</div>
				

				
<?php } ?>
				
			</div><!-- /.box-body -->
		</div><!-- /.box -->
	</div><!-- col-md-6-->
</div><!-- /.row -->




<?php
include ('template_bottom.php');
?>