<?php
$active_tab="users_vacations";

require_once ('template_top.php');

//if ($_SESSION['level']!=9)
//	die("You dont have permissions to access this area! Ask administrator for more!");
	
// include DB
require_once ('config.php');

$db       = connect();


$users_rows=null;
///////////////////READ users
if ($_SESSION['level']==9)
	$find_sql = "SELECT * FROM `users` order by user_level_id";
else 
	$find_sql = "SELECT * FROM `users` where user_id=".$_SESSION['id'];
	
	$stmt      = $db->prepare($find_sql);
	
	$stmt->execute();
	$users_rows = $stmt->fetchAll();
///////////////////READ users

$rows = null;
///////////////////READ Rows
if ($_SESSION['level']==9){
	$wh = construct_where();
	
	$rows = getSet($db,"select user_vacation_id, users.fullname as user_id, DATE_FORMAT(date_start,'%d-%m-%Y') as date_start, DATE_FORMAT(date_end,'%d-%m-%Y') as date_end, authorized, comment from user_vacations 
	 LEFT JOIN users ON users.user_id = user_vacations.user_id ".$wh, array(null));		
}
else {
	$rows = getSet($db,"select user_vacation_id, users.fullname as user_id, DATE_FORMAT(date_start,'%d-%m-%Y') as date_start, DATE_FORMAT(date_end,'%d-%m-%Y') as date_end, authorized, comment from user_vacations 
	 LEFT JOIN users ON users.user_id = user_vacations.user_id where user_vacations.user_id=?", array($_SESSION['id']));	
}
///////////////////READ Rows

//js tranform
$sel_month=0;
$sel_user=0;

if (isset($_POST["filter_month"]))
	$sel_month=$_POST["filter_month"];
	
if (isset($_POST["filter_userid"]))
	$sel_user=$_POST["filter_userid"];
//js tranform
	
function construct_where()
{
	$month=$_POST["filter_month"];
	$user=$_POST["filter_userid"];

	$where="";

	if (!empty($month))
	{
		$year = date('Y'); //this year
		
		$month_calc = $month+1; //increase by 1
		$start_date = date("$year-$month_calc-01"); //convert to date
		$mod_date = strtotime($start_date."- 1 day"); //subtract -1!
		$m = date("Y-m-d",$mod_date); //format back to mysql style!

		//construct the query string!
		$where = " date_start BETWEEN '$year-$month-01' AND '$m'";	
		//$where = " date_start BETWEEN '2014-$month-01' AND '2014-$month-31'";
	}	
	
	if (!empty($user))
	{
		if (!empty($where))
			$where .= " and ";
		
			$where .= " user_vacations.user_id=".$user." and authorized=1";
	}
		
	if (!empty($where))
		$where = " where ".$where;

	return $where;
}


?>


		
<?php if ($_SESSION['level']==9 && (isset($_POST["filter_month"]) || isset($_POST["filter_userid"]))){ ?>
		<!--speedometer-->
    <script src="js/raphael.2.1.0.min.js"></script>
    <script src="js/justgage.1.0.1.min.js"></script>
<?php } ?>

<script>

			
<?php if ($_SESSION['level']==9 && (isset($_POST["filter_month"]) || isset($_POST["filter_userid"]))){ ?>
var speedoD;

<?php } ?>

			$(function ()
				{
					
<?php if ($_SESSION['level']==9 && (isset($_POST["filter_month"]) || isset($_POST["filter_userid"]))){ ?>

				  speedoD = new JustGage({
					id: "speedometerD", 
					value: 0, 
					min: 0,
					max: 50,
					title: " ",
					label: "days",    
					        
				  });

<?php } ?>

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
			document.forms["frmQ"].submit();
		})
	
	console.log(<?= $sel_user; ?>);
	 $("#filter_userid").val(<?= $sel_user; ?>);
	 $("#filter_month").val(<?= $sel_month; ?>);
     ///////////////////////////////////////////////////////////// FILL rows grid
     var jArray_rows =   <?php echo json_encode($rows); ?>;

     var the_rows = "";
     for (var i = 0; i < jArray_rows.length; i++)
     {
        the_rows += "<tr><td></td><td>" + jArray_rows[i]["user_vacation_id"] + "</td><td>" + jArray_rows[i]["user_id"] + "</td>" +
        "<td>" + jArray_rows[i]["date_start"] + "</td><td>" + jArray_rows[i]["date_end"] + "</td><td>" + jArray_rows[i]["comment"] + "</td><td>";
        
        if (jArray_rows[i]["authorized"]==1)
        {
			the_rows +="<span class=\"glyphicon glyphicon-ok\">";
		}
		else 
		{
			the_rows +="<span class=\"glyphicon glyphicon-remove\">";
		}
		
        the_rows += "</td></tr>";
     }

     //set the table rows
     $("#user_vacations_tbl_rows").html(the_rows);
     ///////////////////////////////////////////////////////////// FILL rows grid

     //transform html to magic!
     $("#user_vacations_tbl").bootstrapTable();
                     
<?php
if (isset($_POST["filter_month"]) || isset($_POST["filter_userid"])) { ?>
					    $.ajax(
					    {
					        url : "tab_user_vacations_fetch_datediff.php",
					        type: "POST",
					        data : {
						        	user : <?= $sel_user; ?>,
									month : <?= $sel_month; ?>
									},
					        success:function(dataO, textStatus, jqXHR)
					        {
					        	var data = JSON.parse(dataO);

					        	if (data==null || data=="null")
					        	{
					        		speedoD.refresh(0);
					        			console.log("null");
					        	}
					        	else 
					        	{
									//hours
									speedoD.refresh(data.d);
								}
					        },
					        error: function(jqXHR, textStatus, errorThrown)
					        {
					        	speedoD.refresh(0);
					            alert("ERROR - connection error");
					        }
					    });
<?php } ?>


<?php
if ($_SESSION['level']==9) {
?>
	$("[name='authorized']").bootstrapSwitch();
<?php } ?>

    $('[name=date_start]').datetimepicker({
        weekStart: 1,
        todayBtn:  1,
		autoclose: 1,
		todayHighlight: 1,
		startView: 2,
		minView: 2, 
		forceParse: 1
    });
	
//	//set default value
//	$('[name=date_start]').val(new Date().toISOString().slice(0, 10) + " 00:00");

    $('[name=date_end]').datetimepicker({
        weekStart: 1,
        todayBtn:  1,
		autoclose: 1,
		todayHighlight: 1,
		startView: 2,
		minView: 2, 
		forceParse: 1
    });
	
//	//set default value
//	$('[name=date_end]').val(new Date().toISOString().slice(0, 10) + " 00:00");



					//new record
					$('#btn_user_vacations_new').on('click', function(e)
					{
						$('#lblTitle_USER_VACATIONS').html("New User Vacation");
						
						$('#modalUSER_VACATIONS').modal('toggle');
					});
						
					//edit record
					$('#btn_user_vacations_edit').on('click', function(e)
					{
						var row = $('#user_vacations_tbl').bootstrapTable('getSelections');

						if (row.length>0)
							{
								query_USER_VACATIONS_modal(row[0].id);
							}
						else 
							alert("Please select a row");
					});
					
					//delete record
					$('#btn_user_vacations_delete').on('click', function(e)
					{
						var row = $('#user_vacations_tbl').bootstrapTable('getSelections');

						if (row.length>0)
							{
								if (confirm("Would you like to delete " + row[0].dtp_start + " ?"))
									delete_USER_VACATIONS(row[0].id);
							}
						else 
							alert("Please select a row");
					});
					

				    ////////////////////////////////////////
				    // MODAL FUNCTIONALITIES [START]
				    //when modal closed, hide the warning messages + reset
				    $('#modalUSER_VACATIONS').on('hidden.bs.modal', function() {
				        //when close - clear elements
				        $('#formUSER_VACATIONS').trigger("reset");
				 
				        //clear validator error on form
				        validatorUSER_VACATIONS.resetForm();
				    });
				 
				    //functionality when the modal already shown and its long, when reloaded scroll to top
				    $('#modalUSER_VACATIONS').on('shown.bs.modal', function() {
				        $(this).animate({
				            scrollTop : 0
				        }, 'slow');
				    });
				    // MODAL FUNCTIONALITIES [END]
				    ////////////////////////////////////////
				    
				    //jquery.validate.min.js
				    var validatorUSER_VACATIONS = $("#formUSER_VACATIONS").validate({
				        rules : {
				        	 user_id : { greaterThanZero : true },
				             date_start : { required : true },
				             date_end : { required : true },
				             comment : { required : true },

				        },
				        messages : {
				        	user_id : 'Required Field',
				        	date_start : 'Required Field',
				        	date_end : 'Required Field',
				            comment : 'Required Field',

				        }
				    });
				    
					////////////////////////////////////////
					// MODAL SUBMIT aka save & update button
					$('#formUSER_VACATIONS').submit(function(e) {
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
					    $('#modalUSER_VACATIONS').modal('toggle');
					 
					    $.ajax(
					    {
					        url : formURL,
					        type: "POST",
					        data : postData,
					        success:function(data, textStatus, jqXHR)
					        {
					            if (data=="00000")
									//refresh
									location.reload(true);
								else if  (data=="marketplan")
								{
									alert("Sorry, marketing plan exists in this period!\r\n\r\nTry again using different dates!")
									return;
								}
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
				
				
				//edit button - read record
				function query_USER_VACATIONS_modal(rec_id){
					loading.appendTo(document.body);
				
					
				    $.ajax(
				    {
				        url : "tab_user_vacations_fetch.php",
				        type: "POST",
				        data : { user_vacation_id : rec_id },
				        success:function(data, textStatus, jqXHR)
				        {
							loading.remove();
							
				        	if (data!='null')
							{
<?php
if ($_SESSION['level']==9) {
?>
								$('[name=authorized]').bootstrapSwitch('state',parseInt(data.authorized));
<?php } else { ?>								
								if (parseInt(data.authorized)==1)
								{
							        //clear validator error on form
									$("[name=date_end]").val("");
									$("[name=date_start]").val("");
									$("[name=comment]").val("");
									$("[name=user_vacationsFORM_updateID]").val("");
									$("#user_id").val(0);
									
							        //warn 
							        alert("You cant edit an appoved record!");
							        console.log("GFh");
									return;
								}
<?php } ?>	

							 	$("[name=user_vacationsFORM_updateID]").val(data.user_vacation_id);
								$('[name=user_id]').val(data.user_id);
								$('[name=date_start]').val(data.date_start);
								$('[name=date_end]').val(data.date_end);

								$('[name=comment]').val(data.comment);

							 	
							 	$('#lblTitle_USER_VACATIONS').html("Edit USER_VACATIONS");
								$('#modalUSER_VACATIONS').modal('toggle');
							}
							else
								alert("You cant edit an appoved record!");
				        },
				        error: function(jqXHR, textStatus, errorThrown)
				        {
				        	loading.remove();
				            alert("ERROR");
				        }
				    });
				}
				
				//delete button - delete record
				function delete_USER_VACATIONS(rec_id){
					loading.appendTo(document.body);
					
				    $.ajax(
				    {
				        url : "tab_user_vacations_delete.php",
				        type: "POST",
				        data : { user_vacation_id : rec_id },
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


		<div class="container">
			<button id="btn_user_vacations_new" type="button" class="btn btn-success">
				New
			</button>
<?php if ($_SESSION['level']==9){ ?>
			<button id="btn_user_vacations_edit" type="button" class="btn btn-primary">
				Edit
			</button>

			<button id="btn_user_vacations_delete" type="button" class="btn btn-danger">
				Delete
			</button> 
<?php } ?>

		<br/>
		<br/>
		
<?php if ($_SESSION['level']==9){ ?>
<form name="frmQ" method="post" action="" >
	Filter by User <select name="filter_userid" id="filter_userid"></select><br>Filter by Month <select name="filter_month" id="filter_month"></select>
</form>
<?php } ?>

		    <table id="user_vacations_tbl"
		       data-striped=true
		       data-click-to-select="true"
		       data-single-select="true">

				<thead>
					<tr>
						<th data-field="state" data-checkbox="true" >
						</th>

						<th data-field="id" data-visible="false">
							UserID
						</th>
						
						<th data-field="user_id" data-sortable="true">
							User
						</th>
						
						<th data-field="dtp_start" data-field="date_start" data-sortable="true">
							Date Start
						</th>
						
						<th data-field="date_end" data-sortable="true">
							Date End
						</th>
						
						<th data-field="comment" data-sortable="false">
							Comment
						</th>
						
						<th data-field="authorized" data-sortable="true">
							Authorized
						</th>

						
					</tr>
				</thead>
				<tbody id="user_vacations_tbl_rows"></tbody>
			</table	>
		</div>


<br/>

<?php if ($_SESSION['level']==9 && (isset($_POST["filter_month"]) || isset($_POST["filter_userid"]))){ ?>

				<div id="speedometerD">
				</div>
<?php } ?>

<!-- NEW CLIENT_CALLS MODAL [START] -->
<div class="modal fade" id="modalUSER_VACATIONS" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
					&times;
				</button>
				<h4 class="modal-title" id='lblTitle_USER_VACATIONS'>New</h4>
			</div>
			<div class="modal-body">
				<form id="formUSER_VACATIONS" role="form" method="post" action="tab_user_vacations_save.php">

			
				<div class='form-group'>
					<label>User :</label>
					<select id="user_id" name='user_id' class='form-control'>
					</select>
				</div>


			<div class='form-group'>
					<label>Date Start :</label><br>
					<input type="text" name="date_start" class="form-control" data-date-format="dd-mm-yyyy" readonly class="form_datetime">
			</div>


			<div class='form-group'>
					<label>Date End :</label><br>
					<input type="text" name="date_end" class="form-control" data-date-format="dd-mm-yyyy" readonly class="form_datetime">
			</div>


<?php
if ($_SESSION['level']==9) {
?>
				<div class='form-group'>
					<label>Authorized :</label><br>
					<input type="checkbox" name='authorized'>
				</div>
<?php } ?>	

			
				<div class='form-group'>
					<label>Comment :</label>
					<input name='comment' class='form-control' placeholder='comment'>
				</div>



						<!-- <input name="user_vacationsFORM_FKid" id="USER_VACATIONS_FKid" class="form-control" style="display:none;"> -->
						<input name="user_vacationsFORM_updateID" id="user_vacationsFORM_updateID" class="form-control" style="display:none;">

						<div class="modal-footer">
							<button id="bntCancel_USER_VACATIONS" type="button" class="btn btn-default" data-dismiss="modal">
								cancel
							</button>
							<button id="bntSave_USER_VACATIONS" class="btn btn-primary" type="submit" name="submit">
								save
							</button>
						</div>
                </form>
            </div><!-- End of Modal body -->
        </div><!-- End of Modal content -->
    </div><!-- End of Modal dialog -->
</div><!-- End of Modal -->
<!-- NEW CLIENT_CALLS MODAL [END] -->




<?php
include ('template_bottom.php');
?>