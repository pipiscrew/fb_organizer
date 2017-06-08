<?php
session_start();

require_once ('template_top.php');

include ('config.php');

//only seller can see the page
if ($_SESSION['level']!=1 && $_SESSION['level']!=2)
		die("You are not authorized to view this!");
		
$db=connect();


?>
<script>
	var loading = $('<div class="modal-backdrop"></div><div class="progress progress-striped active loading"><div class="progress-bar" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%">');
			
    $(function() {

					 //convert2magic!
					 $("#pay_pend_tbl").bootstrapTable();
				
					
		}); //jQuery ends 
	
			//bootstrap-table
			function queryParamsOFFERS(params)
			{
				var q = {
					"limit": params.limit,
					"offset": params.offset,
					"search": params.search,
					"name": params.sort,
					"order": params.order
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
 
	

					
	
	    function leadFormatter(value, row) {
	        var icon = value == 0 ? 'glyphicon-star' : 'glyphicon-star-empty'

			var g = value == 0 ? "Client" : value == 1 ? "Lead" : "Inactive Client";
			
	        return '<i class="glyphicon ' + icon + '"></i> ' + g;
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
    
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
	<h1>
		Payment Pendings
	</h1>

</section>

<!-- Main content -->
<section class="content">

	
				<div class="row">					
					<table id="pay_pend_tbl"
						data-url="tab_dashboard_seller_payment_pendings_pagination.php"
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
								<th data-field="offer_company_name" data-formatter="companyFormatter" data-sortable="true">Company Name</th>
								<th data-field="offer_company_manager_name" data-sortable="true">Manager Name</th>
								<th data-field="offer_telephone" data-sortable="true">Telephone</th>
								<th data-field="gen_total" data-formatter="proposalFormatter" data-sortable="true">Proposal Amount</th>
								
							</tr>
						</thead>

						<tbody id="pay_pend_rows"></tbody>
					</table>
					
					<center><h3><span id="total_amount" class='label label-primary label-lg'></span></h3></center>
				</div>



<?php
include ('template_bottom.php');
?>