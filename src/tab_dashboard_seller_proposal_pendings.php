<?php
session_start();

require_once ('template_top.php');

include ('config.php');
include ('config_general.php');

//only seller can see the page
if ($_SESSION['level']!=1 && $_SESSION['level']!=2)
		die("You are not authorized to view this!");
		
$db=connect();

//get users
$chart_db_users = getSet($db,"select user_id,fullname from users where user_id =".$_SESSION['id'],null);

$chart_row_setup = array();

$chart_columns_setup= array();
$chart_columns_setup[]="Month";

//add user
$chart_columns_setup[]	= $chart_db_users[0]["fullname"];

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
//	for( $i= 0 ; $i <= sizeof($chart_db_users)-1 ; $i++ )
//	{
		//query with date between depends on $m variable
		$col_vals[] = (int) getScalar($db,"select count(offer_id) from offers where approval_user_date is null and offer_seller_id=".$_SESSION['id']." and offer_proposal_date between '".$start."' and '".$end."'",null);
//	}

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
            subtitle: 'Proposals'
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
				};

				return q;
			}
			
			// server side: return object with rows and total params
		    function responseHandler(res) {
		    	
		    	$("#total_amount").html("Total amount : " + res.total_amount);
		    	
		        return {
		            rows: res.rows,
		            total: res.total
		        }
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
		
		
	    function actionsFormatter(value, row) {
				return "<a onclick='proposal_delete(" + row.company_id + "," + row.offer_id + ",\"" + row.offer_proposal_date + "\");' class='btn btn-danger btn-xs'>delete</a>";
		}
		
		function companyFormatter(value, row) {
			var s ="";
			
			if (row.is_lead=="0")
			{
				s= "tab_clients_details.php?id=" + row.company_id;
			}
			else if (row.is_lead=="1"){
				s= "tab_leads_details.php?id=" + row.company_id;
			}
			else {
				s= "tab_inclients_details.php?id=" + row.company_id;
			} 
			
			return value + "&nbsp;&nbsp;<a style='float:right' href='" + s + "' target='_blank'>View Details</a>";
		}
		
		function proposal_delete (client_id, offer_id, offer_proposal_date)
		{
			if (!confirm("Would you like to delete the offer issued " + offer_proposal_date ))
				return;
				
			loading.appendTo(document.body);
			
		    $.ajax(
		    {
		        url : "tab_leads_details_proposal_delete.php",
		        type: "POST",
		        data : { client_id : client_id, offer_id : offer_id },
		        success:function(data, textStatus, jqXHR)
		        {
		        	loading.remove();
		        	
		        	if (data=='00000')
					{
						//refresh
						location.reload(true);
					}
					else
						alert("ERROR - Cant delete the record.");
		        },
		        error: function(jqXHR, textStatus, errorThrown)
		        {
		        	loading.remove();
		            alert("ERROR");
		        }
		    });
		}
    
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
	<h1>
		Proposal Pendings
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
					Start Date :
				</label><br>
				<input type="text" id="start_date" name="start_date" class="form-control" data-date-format="yyyy-mm-dd" value="<?= date("Y-m-01");?>" readonly class="form_datetime">
			</div>
		</div>
		<div class="col-md-1">
			<div class='form-group' >
				<label>
					End Date :
				</label><br>
				<input type="text" id="end_date" name="end_date" class="form-control" data-date-format="yyyy-mm-dd" value="<?= get_end_of_the_month(date("m"),date("Y"));?>"  readonly class="form_datetime">
			</div>
		</div>

		<div class="col-md-1">
			<button onclick="refresh_grid()" class="btn btn-primary" style="margin-top:25px">Refresh</button>
		</div>
	</div>
</div>

				<div class="row">					
					<table id="pay_pend_tbl"
						data-url="tab_dashboard_seller_proposal_pendings_pagination.php"
						data-pagination="true"
						data-page-size="50"
						data-side-pagination="server"
						data-query-params="queryParamsOFFERS"
						data-sort-name="offer_proposal_date"
						data-sort-order="desc"
			            data-striped=true"
			            data-response-handler="responseHandler">
						<thead>
							<tr>
								<th data-field="offer_id" data-visible="false">OFFERID</th> 
								<th data-field="company_id" data-visible="false">COMPID</th> 
								<th data-field="is_lead" data-visible="false">ISLEAD</th> 
								<th data-field="url" data-visible="false">url</th> 
								<th data-field="offer_proposal_date" data-sortable="true" data-visible="true">Issued</th> 
								<th data-field="offer_company_name" data-formatter="companyFormatter" data-sortable="true">Company Name</th>
								<th data-field="offer_company_manager_name" data-sortable="true">Manager Name</th>
								<th data-field="offer_telephone" data-sortable="true">Telephone</th>
								<th data-field="gen_total" data-formatter="proposalFormatter" data-sortable="true">Proposal Amount</th>
								<th data-field="seen" data-sortable="false">Seen</th>
								<th data-field="action_delete" data-formatter="actionsFormatter" data-sortable="false">Actions</th>
								
							</tr>
						</thead>

						<tbody id="pay_pend_rows"></tbody>
					</table>
					
					<center><h3><span id="total_amount" class='label label-primary label-lg'></span></h3></center>
				</div>



<?php
include ('template_bottom.php');
?>