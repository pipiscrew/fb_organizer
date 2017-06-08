<?php
session_start();

require_once ('template_top.php');

// include DB
require_once ('config.php');

$db       = connect();

$rows=null;
$rows_r=null;


if ($_SESSION['level']!=9) {
	$rows_r = getSet($db, "select company_id,gen_n2likes,gen_n1likes,gen_n1tat,gen_n2tat,gen_n1views,gen_n2views,offer_company_name,offer_company_manager_name,offer_telephone,mobile, CONCAT('rec_guid=',rec_guid,'&invoice_detail_id=',invoice_detail_id) as invoice,CONCAT(service_starts,' -- Likes : ',service_start_likes,'&nbsp;&nbsp;TAT : ',service_start_tat) as stats,'' as likes,'' as tat,'' as viewss from offers
	left join clients on clients.client_id = offers.company_id
	 where offer_seller_id = ? and 
	service_ends between CURDATE() and DATE_ADD(CURDATE() ,INTERVAL 7 DAY)", array($_SESSION['id']) );
}
else 
{
	$rows_r = getSet($db, "select company_id,gen_n2likes,gen_n1likes,gen_n1tat,gen_n2tat,gen_n1views,gen_n2views,offer_company_name,offer_company_manager_name,offer_telephone,mobile, CONCAT('rec_guid=',rec_guid,'&invoice_detail_id=',invoice_detail_id) as invoice,CONCAT(service_starts,' -- Likes : ',service_start_likes,'&nbsp;&nbsp;TAT : ',service_start_tat) as stats,'' as likes,'' as tat,'' as viewss from offers
	left join clients on clients.client_id = offers.company_id
	 where service_ends between CURDATE() and DATE_ADD(CURDATE() ,INTERVAL 7 DAY)", null );
}


foreach($rows_r as $row) {
$new_like_min                = $row["gen_n2likes"];
$new_like_max                = $row["gen_n1likes"];
$new_tat_min                 = $row["gen_n1tat"];
$new_tat_max                 = $row["gen_n2tat"];
$db_impression_min           = $row["gen_n1views"];
$db_impression_max           = $row["gen_n2views"];

	$row['likes'] = add_thousand(sum_and_div($new_like_min,$new_like_max),0);
	$row['tat'] = add_thousand(sum_and_div($new_tat_max,$new_tat_min),0);
	$row['views'] = add_thousand(sum_and_div($db_impression_max,$db_impression_min),0);

$rows[]=$row;
}


function sum_and_div($no1,$no2)
{
	try
	{
		$m = $no1 + $no2;
		return (int)$m / 2;
	}
	catch(Exception $e){
		return 0;
	}
}

function add_thousand($val, $decimal)
{
	return number_format( $val , $decimal , ',' , '.' );
}
?>

<script>
    $(function() {
    	
					 ///////////////////////////////////////////////////////////// FILL Contracts grid
					 var jArray_rows =   <?php echo json_encode($rows); ?>;
if (jArray_rows){
					 var rows = "";
					 for (var i = 0; i < jArray_rows.length; i++)
					 {


					 	rows += "<tr><td></td><td>" + jArray_rows[i]["company_id"] + "</td><td>" + jArray_rows[i]["offer_company_name"] + "</td>" +
					 	"<td>" + jArray_rows[i]["offer_company_manager_name"] + "</td><td>" + jArray_rows[i]["offer_telephone"] + "</td>" +
					 	"<td>" + jArray_rows[i]["mobile"] + "</td><td>" + jArray_rows[i]["invoice"] + "</td><td>Likes : "+ jArray_rows[i]["likes"] + "<br>TAT : " + jArray_rows[i]["tat"] + "<br>Views : " + jArray_rows[i]["views"] +"</td><td><span id='sp" + i + "'>" + jArray_rows[i]["stats"] +
					 	"</span><a href='javascript:choosefb_page(" + jArray_rows[i]["company_id"] + "," + i + ")' class='btn btn-primary btn-xs' style='margin-left:10px'>Get Now</a>" + "</td></tr>";
					 }
					 
					 $("#renew_rows").html(rows);
}

					
					 //convert2magic!
					 $("#renew_tbl").bootstrapTable();
					 
				    //when modal closed, hide the warning messages + reset
				    $('#modalFBpages').on('hidden.bs.modal', function() {
				        //destroy bootstrap-table
				        $("#fbpages_tbl").bootstrapTable('destroy');
				    });

					$('#fbpages_tbl').on('click-row.bs.table', function (e, row, $element)
					{
						if (!row){
							alert("Please choose valid row!");
							return;
						}
						
//						console.log(e						);
//						console.log(row);
						
						get_stats(row.col_faceboook_page);
						
	 					//close modal
	 					$('#modalFBpages').modal('toggle');
	 					
							
					});
					
		}); //jQuery ends 
		

							var span_id=0;
		function choosefb_page(company_id,d_spanid)
		{
			console.log(company_id);
			//$("#sp"+spanid).append("costas");
			spanid=d_spanid;
			
			$.ajax(
				{
					url : 'tab_clients_details_detail_clients_pages_fill.php',
					dataType : 'json',
					type : 'POST',
					data :
					{
						"id" : company_id,
					},
					success : function(data)
					{
						var jArray = data.recs;
						
						if (jArray.length==0)
						{
							alert("Client has not any Facebook page");
							return;
						}
						else if(jArray.length==1)
						{
							get_stats(jArray[0]["client_page"]);
						}
						else if(jArray.length>1) {
							var grid_rows = "";
							for (var i = 0; i < jArray.length; i++)
							{
								grid_rows += "<tr><td></td><td>" + jArray[i]["client_page_id"] + "</td><td>" + jArray[i]["client_page"] + "</td></tr>";
							}
							
							//refresh
							$("#fbpages_rows").html(grid_rows);
							
		 					//convert2magic!
		 					$("#fbpages_tbl").bootstrapTable();
						 					
							$("#modalFBpages").modal('toggle');
						} else {
							alert("Error, result doesnt contain any facebook page!!");
							return; 
						}
						
					},
					error : function(e)
					{
						alert("error on fb_pages fill combo");
					}
				});	
			    
		}
		
		function get_stats(page_handle){
			$.ajax(
				{
					url : 'get_fb_stats.php',
					dataType : 'json',
					type : 'POST',
					data :
					{
						"pg" : page_handle,
					},
					success : function(data)
					{
//						var jArray = data.recs;
						console.log("likes-" + data.l);
						console.log("tat-" + data.t);
						$("#sp"+spanid).append("<br>"+page_handle+" Likes : "+ data.l+" TAT : " + data.t);
					},
					error : function(e)
					{
						alert("error on fb_pages fill combo");
					}
				});	
		}
		
	    function invoiceFormatter(value, row) {
			if (value && value!="null")
			{
				var s = "<center><a href='http://localhost:8080/proposal/index.php?" + value + "' target='_blank'>View</a></center>";
				return s;
			}
			else 
				return "";
		}
		
		function companyFormatter(value, row) {
			var s= "tab_clients_details.php?id=" + row.id;
			
			return value + "&nbsp;&nbsp;<a style='float:right' href='" + s + "' target='_blank'>View Details</a>";
		}
		
</script>	

<!-- Content Header (Page header) -->
<section class="content-header">
	<h1>
		Renewals (<?php
		$now_plus_seven = strtotime(date("Y-m-d")."+ 7 days");
		
	 echo date("d-m-Y") . " - " .  date("d-m-Y",$now_plus_seven);  ?>)
	</h1>

</section>

<!-- Main content -->
<section class="content">

				<div class="row">					
					<table id="renew_tbl"
			           data-striped=true data-click-to-select="true" data-single-select="true">
						<thead>
							<tr>
								<th data-field="state" data-checkbox="true" ></th>
								<th data-field="id" data-visible="false">ID</th>
								<th data-field="col_name" data-formatter="companyFormatter" data-sortable="true">Company Name</th>
								<th data-field="col_mname" data-sortable="true">Manager Name</th>
								<th data-field="col_tel" data-sortable="true">Telephone</th>
								<th data-field="col_mob" data-sortable="true">Mobile</th>
								<th data-formatter="invoiceFormatter" data-sortable="true">Invoice</th>
								<th data-sortable="true">Estimations</th>
								<th data-sortable="true">Statistics</th>
							</tr>
						</thead>

						<tbody id="renew_rows"></tbody>
					</table>
				</div>

 <div class="modal fade" id="modalFBpages" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
      <div class="modal-content">

        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
          <h4 class="modal-title" id="mySmallModalLabel">Please choose Facebook page</h4>
        </div>
        <div class="modal-body">
        
				<div class="row">					
					<table id="fbpages_tbl"
			           data-click-to-select="true" data-single-select="true" data-show-header="false">
						<thead>
							<tr>
								<th data-field="state" data-checkbox="true"></th>
								<th data-field="id" data-visible="false">ID</th>
								<th data-field="col_faceboook_page">Page</th>
							</tr>
						</thead>

						<tbody id="fbpages_rows"></tbody>
					</table>
				</div>
				
        </div>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->
  
<?php
include ('template_bottom.php');
?>