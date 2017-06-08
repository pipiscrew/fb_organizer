<?php
session_start();
if ($_SESSION['level']!=9) {
	die("not allowed to enter to this section!");
	}
	
$active_tab="user_working_hours";

require_once ('template_top.php');

// include DB
require_once ('config.php');

$db       = connect();

$users_rows=null;
///////////////////READ users
	$find_sql = "SELECT * FROM `users` order by user_level_id";
	$stmt      = $db->prepare($find_sql);
	
	$stmt->execute();
	$users_rows = $stmt->fetchAll();
///////////////////READ users


?>

		
		<!--speedometer-->
    <script src="js/raphael.2.1.0.min.js"></script>
    <script src="js/justgage.1.0.1.min.js"></script>
		
<script>

var speedoH;
var speedoM;

			$(function ()
				{

				  speedoH = new JustGage({
					id: "speedometerH", 
					value: 0, 
					min: 0,
					max: 400,
					title: " ",
					label: "hours",    
					        
				  });
				  
				  speedoM = new JustGage({
					id: "speedometerM", 
					value: 0, 
					min: 0,
					max: 60,
					title: " ",
					label: "minutes",    
					        
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

//fill months
	var month = new Array();
	month.push("");
	month.push("January");
	month.push("February");
	month.push("March");
	month.push("April");
	month.push("May");
	month.push("June");
	month.push("July");
	month.push("August");
	month.push("September");
	month.push("October");
	month.push("November");
	month.push("December");

	var combo_month_rows = "";
	for(var no in month)
		combo_month_rows += "<option value='" + no + "'>" + month[no] + "</option>";
			
	$("#filter_month").html(combo_month_rows);
	$("#filter_month").change(); //select row 0 - no conflict on POST validation @ PHP
	
	$('#filter_month,#filter_userid').on('change', function() {
		$('#user_working_hours_tbl').bootstrapTable('refresh');
		
					    $.ajax(
					    {
					        url : "tab_user_working_hours_fetch_timediff.php",
					        type: "POST",
					        data : {
						        	user : $("#filter_userid").val(),
									month : $("#filter_month").val()
									},
					        success:function(dataO, textStatus, jqXHR)
					        {
					        	var data = JSON.parse(dataO);

					        	if (data==null || data=="null")
					        	{
					        		speedoH.refresh(0);
					        		speedoM.refresh(0);
					        			console.log("null");
					        	}
					        	else 
					        	{
									//hours
									speedoH.refresh(data.h);
									
									//minutes
									speedoM.refresh(data.m);
								}
					        },
					        error: function(jqXHR, textStatus, errorThrown)
					        {
					        	speedoH.refresh(0);
					        	speedoM.refresh(0);
					            alert("ERROR - connection error");
					        }
					    });
					    
	});
	
//

    $('[name=date_start]').datetimepicker({
        weekStart: 1,
        todayBtn:  1,
		autoclose: 1,
		todayHighlight: 1,
		startView: 2,
		forceParse: 1
    });
	

    $('[name=date_end]').datetimepicker({
        weekStart: 1,
        todayBtn:  1,
		autoclose: 1,
		todayHighlight: 1,
		startView: 2,
		forceParse: 1
    });
	


					//http://wenzhixin.net.cn/p/bootstrap-table/docs/examples.html#via-javascript-table
					$('#user_working_hours_tbl').bootstrapTable();

					//new record
					$('#btn_user_working_hours_new').on('click', function(e)
					{
						$('#lblTitle_USER_WORKING_HOURS').html("New User Working Hours");
						
						$('#modalUSER_WORKING_HOURS').modal('toggle');
					});
						
					//edit record
					$('#btn_user_working_hours_edit').on('click', function(e)
					{
						var row = $('#user_working_hours_tbl').bootstrapTable('getSelections');

						if (row.length>0)
							{
								query_USER_WORKING_HOURS_modal(row[0].user_working_hour_id);
								console.log(row[0].user_working_hour_id);
							}
						else 
							alert("Please select a row");
					});
					
					//delete record
					$('#btn_user_working_hours_delete').on('click', function(e)
					{
						var row = $('#user_working_hours_tbl').bootstrapTable('getSelections');

						if (row.length>0)
							{
								if (confirm("Would you like to delete " + row[0].date_start + " ?"))
									delete_USER_WORKING_HOURS(row[0].user_working_hour_id);
							}
						else 
							alert("Please select a row");
					});
					

				    ////////////////////////////////////////
				    // MODAL FUNCTIONALITIES [START]
				    //when modal closed, hide the warning messages + reset
				    $('#modalUSER_WORKING_HOURS').on('hidden.bs.modal', function() {
				        //when close - clear elements
				        $('#formUSER_WORKING_HOURS').trigger("reset");
				 
				        //clear validator error on form
				        validatorUSER_WORKING_HOURS.resetForm();
				    });
				 
				    //functionality when the modal already shown and its long, when reloaded scroll to top
				    $('#modalUSER_WORKING_HOURS').on('shown.bs.modal', function() {
				        $(this).animate({
				            scrollTop : 0
				        }, 'slow');
				    });
				    // MODAL FUNCTIONALITIES [END]
				    ////////////////////////////////////////
				    
				    //jquery.validate.min.js
				    var validatorUSER_WORKING_HOURS = $("#formUSER_WORKING_HOURS").validate({
				        rules : {
							user_id : { greaterThanZero : true }
				        },
				        messages : {
							user_id : 'Required Field',
				        }
				    });
				    
					////////////////////////////////////////
					// MODAL SUBMIT aka save & update button
					$('#formUSER_WORKING_HOURS').submit(function(e) {
					    e.preventDefault();
					 
					    ////////////////////////// validation
					    var form = $(this);
					    form.validate();
					 
					    if (!form.valid())
					        return;
					    ////////////////////////// validation
					 
					    var postData = $(this).serializeArray();
					    var formURL = $(this).attr("action");
					 
					    //close modal
					    $('#modalUSER_WORKING_HOURS').modal('toggle');
					 
					    $.ajax(
					    {
					        url : formURL,
					        type: "POST",
					        data : postData,
					        success:function(data, textStatus, jqXHR)
					        {
					            if (data=="00000")
									//refresh
									$('#user_working_hours_tbl').bootstrapTable('refresh');
					            else
					                alert("ERROR");
					        },
					        error: function(jqXHR, textStatus, errorThrown)
					        {
					            alert("ERROR - connection error");
					        }
					    });
					});

				});
				
				//bootstrap-table
				function queryParamsUSER_WORKING_HOURS(params)
				{
					
				    var q = {
				        "limit": params.limit,
				        "offset": params.offset,
				        "search": params.search,
						"user" : $("#filter_userid").val(),
						"month" : $("#filter_month").val(),
				        "name": params.sort,
				        "order": params.order
				    };
				 
				    return q;
//					var q = {
//						"limit": params.pageSize,
//						"offset": params.pageSize * (params.pageNumber - 1),
//						"search": params.searchText,
//						"user" : $("#filter_userid").val(),
//						"month" : $("#filter_month").val(),
//						"name": params.sortName,
//						"order": params.sortOrder
//					};
//					
//					return q;
				}
				
				//edit button - read record
				function query_USER_WORKING_HOURS_modal(rec_id){
					loading.appendTo(document.body);
					
				    $.ajax(
				    {
				        url : "tab_user_working_hours_fetch.php",
				        type: "POST",
				        data : { user_working_hour_id : rec_id },
				        success:function(data, textStatus, jqXHR)
				        {
							loading.remove();
							
				        	if (data!='null')
							{
							 	$("[name=user_working_hoursFORM_updateID]").val(data.user_working_hour_id);
		$('[name=user_id]').val(data.user_id);
		$('[name=date_start]').val(data.date_start);
		$('[name=date_end]').val(data.date_end);

							 	
							 	$('#lblTitle_USER_WORKING_HOURS').html("Edit User Working Hours");
								$('#modalUSER_WORKING_HOURS').modal('toggle');
							}
							else
								alert("ERROR - Cant read the record.");
				        },
				        error: function(jqXHR, textStatus, errorThrown)
				        {
				        	loading.remove();
				            alert("ERROR");
				        }
				    });
				}
				
				//delete button - delete record
				function delete_USER_WORKING_HOURS(rec_id){
					loading.appendTo(document.body);
					
				    $.ajax(
				    {
				        url : "tab_user_working_hours_delete.php",
				        type: "POST",
				        data : { user_working_hour_id : rec_id },
				        success:function(data, textStatus, jqXHR)
				        {
				        	loading.remove();
				        	
				        	if (data=='00000')
							{
								//refresh
								$('#user_working_hours_tbl').bootstrapTable('refresh');
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

		<div class="container">
			<button id="btn_user_working_hours_new" type="button" class="btn btn-success">
				New
			</button>
			<button id="btn_user_working_hours_edit" type="button" class="btn btn-primary">
				Edit
			</button>
			<button id="btn_user_working_hours_delete" type="button" class="btn btn-danger">
				Delete
			</button> 
		<br/>
		<br/>
		
		Filter by User <select id="filter_userid"></select><br>Filter by Month <select id="filter_month"></select>
		
			<table id="user_working_hours_tbl"
	           data-toggle="table"
	           data-striped=true
	           data-url="tab_user_working_hours_pagination.php"
			   data-search="false"
				data-show-columns="false"
	           data-show-refresh="false"
	           data-show-toggle="false"
	           data-pagination="true"
	           data-click-to-select="true" data-single-select="true"
	           data-page-size="50"
	           data-height="500"
	           data-side-pagination="server"
	           data-query-params="queryParamsUSER_WORKING_HOURS">

				<thead>
					<tr>
						<th data-field="state" data-checkbox="true" >
						</th>

						<th data-field="user_working_hour_id" data-visible="false">
							user_working_hour_id
						</th>
						
						<th data-field="user_id" data-sortable="true">
							User
						</th>
						
						<th data-field="date_start" data-sortable="true">
							DateTime Start
						</th>
						
						<th data-field="date_end" data-sortable="true">
							DateTime End
						</th>
						
						<th data-field="reason" data-sortable="true">
							Reason
						</th>
					</tr>
				</thead>
			</table	>
		</div>

<br/>

<div class="row">
	<div class="col-md-6">
		<span class="pull-right">
			<div id="speedometerH">
			</div>
		</span>
	</div>

	<div class="col-md-6">
		<span class="pull-left">
			<div id="speedometerM">
			</div>
		</span>
	</div>
</div>




<!-- NEW USER_WORKING_HOURS MODAL [START] -->
<div class="modal fade" id="modalUSER_WORKING_HOURS" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
					&times;
				</button>
				<h4 class="modal-title" id='lblTitle_USER_WORKING_HOURS'>New</h4>
			</div>
			<div class="modal-body">
				<form id="formUSER_WORKING_HOURS" role="form" method="post" action="tab_user_working_hours_save.php">

			
				<div class='form-group'>
					<label>user_id :</label>
					<select id="user_id" name='user_id' class='form-control'>
					</select>
				</div>


			
				<div class='form-group'>
					<label>date_start :</label><br>
					<input type="text" name="date_start" class="form-control" data-date-format="dd-mm-yyyy hh:ii" readonly class="form_datetime">
				</div>


			
				<div class='form-group'>
					<label>date_end :</label><br>
					<input type="text" name="date_end" class="form-control" data-date-format="dd-mm-yyyy hh:ii" readonly class="form_datetime">
				</div>



						<!-- <input name="user_working_hoursFORM_FKid" id="USER_WORKING_HOURS_FKid" class="form-control" style="display:none;"> -->
						<input name="user_working_hoursFORM_updateID" id="user_working_hoursFORM_updateID" class="form-control" style="display:none;">

						<div class="modal-footer">
							<button id="bntCancel_USER_WORKING_HOURS" type="button" class="btn btn-default" data-dismiss="modal">
								cancel
							</button>
							<button id="bntSave_USER_WORKING_HOURS" class="btn btn-primary" type="submit" name="submit">
								save
							</button>
						</div>
                </form>
            </div><!-- End of Modal body -->
        </div><!-- End of Modal content -->
    </div><!-- End of Modal dialog -->
</div><!-- End of Modal -->
<!-- NEW USER_WORKING_HOURS MODAL [END] -->



<?php
include ('template_bottom.php');
?>