<?php
session_start();

require_once ('template_top.php');

include ('config.php');
include ('config_general.php');

//only admim can see the page
if ($_SESSION['level']!=9)
		die("You are not authorized to view this!");
		
$db=connect();

if (isset($_GET["month"]) && $_GET["month"]==1)
{
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
	for($m= 1 ; $m < 13 ; $m++)
	{
		$col_vals= array();
		
		$col_vals[]=monthName($m);
		
		$total_tmp="";
		
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
			$total_tmp = (int) getScalar($db,"select count(client_call_id) from client_calls left join clients on clients.client_id = client_calls.client_id where owner= " . $chart_db_users[$i]['user_id'] . "  and (client_call_datetime BETWEEN '".$start." 00:00' AND '".$end." 23:59')",null);
			$col_vals[] =  $total_tmp;
		}
		
		$chart_row_setup[]=$col_vals;
	}	
	

}
else 
{
	$users_rows=null;
	///////////////////READ users
	$users_rows = getSet($db, "SELECT * FROM `users` order by user_level_id",null);
	///////////////////READ users

	//get users
	$chart_db_users = getSet($db,"select user_id,fullname from users where user_level_id in (1,2,9)",null);

	$chart_row_setup = array();

	$chart_columns_setup= array();
	$chart_columns_setup[]="Day";
	for( $i= 0 ; $i <= sizeof($chart_db_users)-1 ; $i++ )
	{
		$chart_columns_setup[]	= $chart_db_users[$i]["fullname"];
	}

	//always on 0 array position is the bars names
	$chart_row_setup[0] = $chart_columns_setup;



	//below merge the bars values
	$col_vals= array();

	//for each month
	for($m= 0 ; $m < 7 ; $m++)
	{
		$col_vals= array();
		
		
		
		//$_GET["dstart"];
		$time = strtotime($_GET["dstart"].' '.$m . " days");
		
		$col_vals[]= date('D d M', $time);
		
		$day_str = date("Y-m-d", $time);
		
		$total_tmp="";
		
		//for each user
		for( $i= 0 ; $i <= sizeof($chart_db_users)-1 ; $i++ )
		{
			//query with date between depends on $m variable
			$total_tmp = (int) getScalar($db,"select count(client_call_id) from client_calls left join clients on clients.client_id = client_calls.client_id where owner= " . $chart_db_users[$i]['user_id'] . "  and (client_call_datetime BETWEEN '".$day_str." 00:00' AND '".$day_str." 23:59')",null);
						
			$col_vals[] = $total_tmp;
		}
		
//var_dump($col_vals);
//exit;


		$chart_row_setup[]=$col_vals;
	}	
}

//	exit;	

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
            subtitle: 'Calls',
          }
        };

        var chart = new google.charts.Bar(document.getElementById('columnchart_material'));

        chart.draw(data, options);
      }
    </script>
    
<script>
	var loading = $('<div class="modal-backdrop"></div><div class="progress progress-striped active loading"><div class="progress-bar" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%">');
			
    $(function() {
    	
				$('[name=start_date],[name=end_date]').datetimepicker({
			        weekStart: 1,
			        todayBtn:  1,
					autoclose: 1,
					todayHighlight: 1,

					startView: 2,
					minView: 2,
					
					forceParse: 1
			    });
			    
			    	 $('#filter_userid').on('change', function() {
//			       		$('#pay_pend_tbl').bootstrapTable('refresh');
refresh_grid();
			    	 });
    
					///////////////////////////////////////////////////////////// FILL users
					var jArray_users =   <?php echo json_encode($users_rows); ?>;

					var combo_users_rows = "<option value='0'></option>";
					for (var i = 0; i < jArray_users.length; i++)
					{
						combo_users_rows += "<option value='" + jArray_users[i]["user_id"] + "'>" + jArray_users[i]["fullname"] + "</option>";
					}

					$("[name=user_id]").html(combo_users_rows);
					$("[name=user_id]").change(); //select row 0 - no conflict on POST validation @ PHP
					
					$("#filter_userid").html(combo_users_rows);
					
					<?php if ($_GET["u"]==9999) { ?>
						$("#filter_userid").change(); //select row 0 - no conflict on POST validation @ PHP	
					<?php } else { ?>
						$("#filter_userid").val(<?= $_GET["u"];?>);	
					<?php } ?>
					
					//
					///////////////////////////////////////////////////////////// FILL users
	

					 //convert2magic!
					 $("#pay_pend_tbl").bootstrapTable();
					
				    
				    
					
		}); //jQuery ends 
	
	function refresh_grid()
	{
		$('#pay_pend_tbl').bootstrapTable('refresh');
	}
			//bootstrap-table
			function queryParamsOFFERS(params)
			{
				var s,e;
					if ($("#start_date").val().trim().length > 0 && $("#end_date").val().trim().length == 0)
					{
						alert("Please fill 'end date'");
						return false;
					}
					else if ($("#end_date").val().trim().length > 0 && $("#start_date").val().trim().length == 0)
					{
						alert("Please fill 'start date'");
						return false;
					}
					else if ($("#start_date").val().trim().length > 0 && $("#end_date").val().trim().length > 0)
					{
						s=$("#start_date").val() + " 00:00";
						e=$("#end_date").val() + " 23:59";
					}
					
				var q = {
					"limit": params.limit,
					"offset": params.offset,
					"search": params.search,
					"name": params.sort,
					"order": params.order,
                    //
                    "start_date" : s,
                    "end_date" : e,
                    "user": $("#filter_userid").val(),
				};

				return q;
			}
			

    
    
    	function contract_active(value, row) {
	    	var img_fl= "status_inactive";
	    	var caption = "Inactive";
	    	
	    	if (row.is_ended==1 && row.is_start==0)
	    	 {
	    	 		img_fl = "status_active";
	    	 		caption = "Active";
	    	 }

			var x = "<img style='margin-right:5px;' src='img/{img_filename}.png'>";
			x = x.replace("{img_filename}",img_fl);

			return x + caption;
	    	
//			if (value && value!="null")
//			{
//				var s = value + "<a style='float:right' href='http://localhost:8080/proposal/index.php?" + row.url + "' target='_blank'>View</a></center>";
//				return s;
//			}
//			else 
//				return "";
		}
		
	    function proposalFormatter(value, row) {
	    	
			if (value && value!="null")
			{
				var s = value + "<a style='float:right' href='http://localhost:8080/proposal/index.php?" + row.url + "' target='_blank'>View</a></center>";
				return s;
			}
			else 
				return "";
		}
		
		
	    function statFormatter(value, row) {
	    	
	    	var icon_stat = '<span class="glyphicon glyphicon-remove">';
			
			if (value != null && value == 1)
			{
				 icon_stat = '<span class="glyphicon glyphicon-ok">';
			}
			
			return icon_stat;
		}
		
		
		function companyFormatter(value, row) {
			var s ="";
			
			if (row.is_lead=="0")
			{
				s= "tab_clients_details.php?id=" + row.client_id;
			}
			else if (row.is_lead=="1"){
				s= "tab_leads_details.php?id=" + row.client_id;
			}
			else {
				s= "tab_inclients_details.php?id=" + row.client_id;
			} 
			
			var contact_status="<img style='margin-right:5px;' src='img/status_client_inactive.png'>";
			if ( row.is_lead != null && row.is_lead == 0)
				contact_status="<img style='margin-right:5px;' src='img/status_client_active.png'>";
			
			return contact_status + value + "&nbsp;&nbsp;<a style='float:right' href='" + s + "' target='_blank'>View Details</a>";
		}
    
</script>


<!-- Content Header (Page header) -->
<section class="content-header">
	<h1>
		Calls
	</h1>

</section>

<!-- Main content -->
<section class="content">

<div id="columnchart_material" style="height: 300px;"></div>

				 <br><br>
	
<div id="custom-toolbar">
	<div class="row" >
		<div class="col-md-1">
			<div class='form-group'>
				<label>
					Filter by User :
				</label><br>
				<select name="filter_userid" id="filter_userid"></select>
			</div>
		</div>
		<div class="col-md-1">
			<div class='form-group'>
				<label>
					Start Date :
				</label><br>
				<input type="text" id="start_date" name="start_date" class="form-control" data-date-format="yyyy-mm-dd" value="<?= $_GET["dstart"]; ?>" readonly class="form_datetime">
			</div>
		</div>
		<div class="col-md-1">
			<div class='form-group' >
				<label>
					End Date :
				</label><br>
				<input type="text" id="end_date" name="end_date" class="form-control" data-date-format="yyyy-mm-dd" value="<?= $_GET["dend"]; ?>"  readonly class="form_datetime">
			</div>
		</div>

		<div class="col-md-1">
			<button onclick="refresh_grid()" class="btn btn-primary" style="margin-top:25px">Refresh</button>
		</div>
	</div>
</div>

				<div class="row">					
					<table id="pay_pend_tbl"
						data-url="index_dashboard_sales_04_calls_pagination.php"
						data-pagination="true"
						data-page-size="50"
						data-side-pagination="server"
						data-query-params="queryParamsOFFERS"
						data-sort-name="client_call_datetime"
						data-sort-order="desc"
			            data-striped=true"
			            data-response-handler="responseHandler">
						<thead>
							<tr>
								<th data-field="client_id" data-visible="false">ID</th> 
								<th data-field="is_lead" data-visible="false">ISLEAD</th> 
								<th data-field="client_code" data-visible="true">Code</th> 
								<th data-field="client_name" data-formatter="companyFormatter" data-visible="true">Company</th> 
								<th data-field="client_call_datetime" data-visible="true">Datetime</th> 
								<th data-field="chk_answered" data-formatter="statFormatter" data-visible="true">Answered</th> 
								<th data-field="chk_company_presented" data-formatter="statFormatter" data-visible="true">Company Presented</th> 
								<th data-field="chk_company_profile" data-formatter="statFormatter" data-visible="true">Company Profile</th> 
								<th data-field="chk_client_proposal" data-formatter="statFormatter"  data-sortable="true" data-visible="true">Client Proposal</th> 
								<th data-field="chk_appointment_booked" data-formatter="statFormatter" data-sortable="true" data-visible="true">Appointment Booked</th> 
								<th data-field="client_call_next_call" data-sortable="true">Next Call</th>
								<th data-field="owner" data-sortable="true">Owner</th>
								
							</tr>
						</thead>

						<tbody id="pay_pend_rows"></tbody>
					</table>
					

				</div>



<?php
include ('template_bottom.php');
?>