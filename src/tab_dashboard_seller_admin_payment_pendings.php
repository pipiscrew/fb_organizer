<?php
session_start();

require_once ('template_top.php');

include ('config.php');

//only admim can see the page
if ($_SESSION['level']!=9)
		die("You are not authorized to view this!");
		
$db=connect();

$users_rows=null;
///////////////////READ users
$users_rows = getSet($db, "SELECT * FROM `users` order by user_level_id",null);

///////////////////READ users

?>
<script>
	var loading = $('<div class="modal-backdrop"></div><div class="progress progress-striped active loading"><div class="progress-bar" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%">');
			
    $(function() {
    	
			    	 $('#filter_userid').on('change', function() {
			       		$('#pay_pend_tbl').bootstrapTable('refresh');
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
					$("#filter_userid").change(); //select row 0 - no conflict on POST validation @ PHP
					///////////////////////////////////////////////////////////// FILL users
	

					 //convert2magic!
					 $("#pay_pend_tbl").bootstrapTable();
					
				    ////////////////////////////////////////
				    // MODAL FUNCTIONALITIES [START]
				    //when modal closed, hide the warning messages + reset
				    $('#modalOFFERS').on('hidden.bs.modal', function() {
				        //when close - clear elements
				        $('#formOFFERS').trigger("reset");
				    });
				 
				    //functionality when the modal already shown and its long, when reloaded scroll to top
				    $('#modalOFFERS').on('shown.bs.modal', function() {
				        $(this).animate({
				            scrollTop : 0
				        }, 'slow');
				    });
				    // MODAL FUNCTIONALITIES [END]
				    ////////////////////////////////////////

					$('[name=is_paid]').bootstrapSwitch('state',false);
					
					$('[name=pay_date]').datetimepicker({
				        weekStart: 1,
				        todayBtn:  1,
						autoclose: 1,
						todayHighlight: 1,

						startView: 2,
						minView: 2,
						
						forceParse: 1
				    });
				    
				    
					////////////////////////////////////////
					// MODAL SUBMIT aka save & update button
					$('#formOFFERS').submit(function(e) {
					    e.preventDefault();

						if ($('[name=pay_date]').val().trim().length == 0)
						{
							alert("Please enter date");
							return;
						}
						else {
							
							if ($('[name=is_paid]').bootstrapSwitch('state')==false)
							{
								alert("Proposal is not tagged as paid.\r\n\r\nOperation Aborted!\r\n\r\nThe other time use 'cancel'");
								return;
							}
							
						}
			
			loading.appendTo($('#formOFFERS'));
			
					    var postData = $(this).serializeArray();
					    var formURL = $(this).attr("action");
						
					    $.ajax(
					    {
					        url : formURL,
					        type: "POST",
					        data : postData,
					        success:function(data, textStatus, jqXHR)
					        {
					        						  
					    loading.remove();
					            if (data=="1")
									{
										$("#"+mark_as_paid_button).hide();
										$('#modalOFFERS').modal('toggle');
									}
					            else
					                alert("ERROR, please refresh the page");
					        },
					        error: function(jqXHR, textStatus, errorThrown)
					        {
					        	loading.remove();
					            alert("ERROR - connection error");
					        }
					    });
					});
					
		}); //jQuery ends 
	
			//bootstrap-table
			function queryParamsOFFERS(params)
			{
				var q = {
					"limit": params.limit,
					"offset": params.offset,
					"search": params.search,
					"name": params.sort,
					"order": params.order,
					"user": $("#filter_userid").val(),
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
    
		var mark_as_paid_button;
		function pay(offer_id,eventt){
			mark_as_paid_button = eventt.srcElement.id;
			
			$("#offersFORM_updateID").val(offer_id);
			$('#modalOFFERS').modal('toggle');
		}
	
		function del(offer_id,eventt){
			
			if (confirm("Delete offer ?"))
			{
				$.ajax({
					url : 'tab_dashboard_seller_admin_payment_pendings_delete.php',
					type : 'POST',
					data : {
						"offer_id" : offer_id
					},
		            success : function(data) {
		            	
		            	if (data == "00000")
		            	{
							$('#pay_pend_tbl').bootstrapTable('refresh');	
						}
						else 
						{
							alert("Couldnt delete the offer");
						}
						
					},
					error : function(e)
					{
						alert("error");
					}
				});
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

				Filter by User <select name="filter_userid" id="filter_userid"></select><br><br>
	
				<div class="row">					
					<table id="pay_pend_tbl"
						data-url="tab_dashboard_seller_admin_payment_pendings_pagination.php"
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
								<th data-field="offer_seller_name" data-sortable="true">Seller</th>
								<th data-field="actions" data-sortable="false">Actions</th>
								
							</tr>
						</thead>

						<tbody id="pay_pend_rows"></tbody>
					</table>
					
					<center><h3><span id="total_amount" class='label label-primary label-lg'></span></h3></center>
				</div>


<!-- NEW OFFER MODAL [START] -->
<div class="modal fade" id="modalOFFERS" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
					&times;
				</button>
				<h4 class="modal-title" id='lblTitle_OFFERS'>
					Edit Payment
				</h4>
			</div>
			<div class="modal-body">
				<form id="formOFFERS" role="form" method="post" action="tab_leads_details_proposal_admin_save.php">

					<div class='form-group' style="width:300px">
						<label>Date :</label><br>
						<input type="text" name="pay_date" class="form-control" data-date-format="dd-mm-yyyy" readonly class="form_datetime">
					</div>
					

					<div class='form-group'>
						<label>
							Paid :
						</label><br>
						<input type="checkbox" name='is_paid'>
					</div>

					<input name="offersFORM_updateID" id="offersFORM_updateID" class="form-control" style="display:none;">
					<input name="dont_redirect" id="dont_redirect" class="form-control" value="1" style="display:none;">

					<div class="modal-footer">
						<button id="bntCancel_OFFERS" type="button" class="btn btn-default" data-dismiss="modal">
							cancel
						</button>
						<button id="bntSave_OFFERS" class="btn btn-primary" type="submit" name="submit">
							save
						</button>
					</div>
				</form>
			</div><!-- End of Modal body -->
		</div><!-- End of Modal content -->
	</div><!-- End of Modal dialog -->
</div><!-- End of Modal -->
<!-- NEW OFFER MODAL [END] -->

<?php
include ('template_bottom.php');
?>