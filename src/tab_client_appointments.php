<?php

$rows_appointmentUSERS=null;
///////////////////READ USERS
if (isset($_GET["id"])) {
	$find_sql = "select * from users";
	$stmt      = $db->prepare($find_sql);
	$stmt->execute();
	$rows_appointmentUSERS = $stmt->fetchAll();
}
///////////////////READ USERS
?>
		<script>
			var map;
			
			$(function ()
				{

					$('#client_appointments_users').chooser();
					
					var jArray_appointmentUSERS = <?php echo json_encode($rows_appointmentUSERS); ?>;
					
					if (jArray_appointmentUSERS)
						$("#client_appointments_users").fillList(jArray_appointmentUSERS,"Participants", "user_id", "fullname");
						
					//set selected row on table
					$('#client_appointments_tbl').on('click', 'tbody tr', function(event)
					{
						$(this).addClass('highlight').siblings().removeClass('highlight');
					});
				
				    $('[name=client_appointment_datetime]').datetimepicker({
				        weekStart: 1,
				        todayBtn:  1,
						autoclose: 1,
						todayHighlight: 1,
						startView: 2,
						forceParse: 1
				    });

					//new record
					$('#btn_client_appointments_new').on('click', function(e)
					{
						$('#lblTitle_CLIENT_APPOINTMENTS').html("New Client Appointment");
						
						//set client ID
						$("#client_appointmentsFORM_client_id").val("<?= $_GET["id"]; ?>");
											
						$('#modalCLIENT_APPOINTMENTS').modal('toggle');
					});
						
					//edit record
					$('#btn_client_appointments_edit').on('click', function(e)
					{
						//get selected row - ID column
						var anSelected = getSelected('client_appointments_tbl');
						if (anSelected == null)
						{
							alert("Please select a row!");
							return;
						}
						
						query_CLIENT_APPOINTMENTS_modal(anSelected[0]);
					});
					
					//delete record
					$('#btn_client_appointments_delete').on('click', function(e)
					{
						
						//get selected row - ID column
						var anSelected = getSelected('client_appointments_tbl');
						if (anSelected == null)
						{
							alert("Please select a row!");
							return;
						}
else 
							{
								if (confirm("Would you like to delete " + anSelected[0] + " ?"))
									delete_CLIENT_APPOINTMENTS(anSelected[0]);
							}

					});
					

				    ////////////////////////////////////////
				    // MODAL FUNCTIONALITIES [START]
				    //when modal closed, hide the warning messages + reset
				    $('#modalCLIENT_APPOINTMENTS').on('hidden.bs.modal', function() {
				        //when close - clear elements
				        $('#formCLIENT_APPOINTMENTS').trigger("reset");
				 
				 		//reset users list
				 		$("#client_appointments_users").clearList();
				 		
				        //clear validator error on form
				        validatorCLIENT_APPOINTMENTS.resetForm();
				    });
				 
				    //functionality when the modal already shown and its long, when reloaded scroll to top
				    $('#modalCLIENT_APPOINTMENTS').on('shown.bs.modal', function() {
				        $(this).animate({
				            scrollTop : 0
				        }, 'slow');
				    });
				    // MODAL FUNCTIONALITIES [END]
				    ////////////////////////////////////////
				    
				    //jquery.validate.min.js
				    var validatorCLIENT_APPOINTMENTS = $("#formCLIENT_APPOINTMENTS").validate({
				        rules : {
				             client_appointment_location : { required : true },
				             client_appointment_datetime : { required : true },
				             client_appointment_comment : { required : true },

				        },
				        messages : {
				            client_appointment_datetime : 'Required Field',
				            client_appointment_location : 'Required Field',
				            client_appointment_comment : 'Required Field',

				        }
				    });
				    
					////////////////////////////////////////
					// MODAL SUBMIT aka save & update button
					$('#formCLIENT_APPOINTMENTS').submit(function(e) {
					    e.preventDefault();
					 
					    ////////////////////////// validation
					    var form = $(this);
					    form.validate();
					 
					    if (!form.valid())
					        return;
					    ////////////////////////// validation
					 
//					 var selected_participantsOBJ = {}; 
					 
					 var get_selected_participants = $("#client_appointments_users").getSelected();
					 
					 if (get_selected_participants.length==0)
					 {
					 	alert("Please choose participants!");
					 	return;
					 }

loading.appendTo(document.body);

					 	//set is lead or not 
					 	//when loaded from *tab_leads_details.php*
					 	//the is_lead = 1 otherwise is_lead=0
						 $("#client_appointment_is_lead").val(is_lead);
					 
					    var postData = $(this).serializeArray();
					    var formURL = $(this).attr("action");
						
						postData.push({name: "participants", value : JSON.stringify(get_selected_participants)});
					 
  //close modal
					    				$('#modalCLIENT_APPOINTMENTS').modal('toggle');
					    				
					    $.ajax(
					    {
					        url : formURL,
					        type: "POST",
					        data : postData,
					        success:function(data, textStatus, jqXHR)
					        {
					        					loading.remove();	  
					    
					            if (data=="00000")
									//refresh
									loadAPPOINTMENTSrecs();
					            else
					                alert("ERROR");
					        },
					        error: function(jqXHR, textStatus, errorThrown)
					        {
					        	loading.remove();
					            alert("ERROR - connection error");
					        }
					    });
					});

				});


				//edit button - read record
				function query_CLIENT_APPOINTMENTS_modal(rec_id){
					loading.appendTo(document.body);
					
				    $.ajax(
				    {
				        url : "tab_client_appointments_fetch.php",
				        type: "POST",
				        data : { client_appointment_id : rec_id },
				        success:function(dataO, textStatus, jqXHR)
				        {
							loading.remove();
							
				        	if (dataO!='null')
							{
								var data = dataO.appointment;
								var dataP = dataO.participants;
								
								$("#client_appointments_users").setSelected(dataP,"user_id")

							 	$("[name=client_appointmentsFORM_updateID]").val(data.client_appointment_id);
//								$('[name=client_appointmentsFORM_client_id]').val(data.client_appointment_client_id);
//								$('[name=client_appointment_is_lead]').bootstrapSwitch('state',parseInt(data.client_appointment_is_lead));
								$('[name=client_appointment_datetime]').val(data.client_appointment_datetime);
								$('[name=client_appointment_location]').val(data.client_appointment_location);
								$('[name=client_appointment_google]').val(data.client_appointment_google);
								$('[name=client_appointment_comment]').val(data.client_appointment_comment);

							 	$('#lblTitle_CLIENT_APPOINTMENTS').html("Edit Client Appointment");
							 	
								$('#modalCLIENT_APPOINTMENTS').modal('toggle');
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
				
//				function setSelected(jsonArray, idColName){
//					for (var i = 0; i < jsonArray.length; i++)
//						$('#client_appointments_users').children('a').each( function(index, element){
//							console.log( $( this ).text() );
//							
//							if ($(this).attr('data-name')==jsonArray[i][idColName])
//							{	
//								$(this).addClass('list-group-item active');
//								return false; //exit for each
//							}
//						});
//				}
				
				//delete button - delete record
				function delete_CLIENT_APPOINTMENTS(rec_id){
					loading.appendTo(document.body);
					
				    $.ajax(
				    {
				        url : "tab_client_appointments_delete.php",
				        type: "POST",
				        data : { client_appointment_id : rec_id },
				        success:function(data, textStatus, jqXHR)
				        {
				        	loading.remove();
				        	
				        	if (data=='00000')
							{
								//refresh
								loadAPPOINTMENTSrecs();
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
<br/>
		<div class="container">
			<button id="btn_client_appointments_new" type="button" class="btn btn-success">
				New
			</button>
			<button id="btn_client_appointments_edit" type="button" class="btn btn-primary">
				Edit
			</button>
			<?php if ($_SESSION['level']==9) { ?>
			<button id="btn_client_appointments_delete" type="button" class="btn btn-danger">
				Delete
			</button> 
			<?php } ?>
		
			<table id='client_appointments_tbl' class="table table-striped" >
				<thead>
					<tr>
						<th tabindex="0" rowspan="1" colspan="1">ID</th>
						<th tabindex="0" rowspan="1" colspan="1">Is Lead?</th>
						<th tabindex="0" rowspan="1" colspan="1">Datetime</th>
						<th tabindex="0" rowspan="1" colspan="1">Location</th>
						<th tabindex="0" rowspan="1" colspan="1">Participants</th>
					</tr>
				</thead>

				<tbody id="client_appointments_rows"></tbody>
			</table>
		</div>



<!-- NEW CLIENT_APPOINTMENTS MODAL [START] -->
<div class="modal fade" id="modalCLIENT_APPOINTMENTS" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
					&times;
				</button>
				<h4 class="modal-title" id='lblTitle_CLIENT_APPOINTMENTS'>New</h4>
			</div>
			<div class="modal-body">
				<form id="formCLIENT_APPOINTMENTS" role="form" method="post" action="tab_client_appointments_save.php">
			
					<div class='form-group'>
					<label>Datetime :</label><br>
					<input type="text" name="client_appointment_datetime" class="form-control" data-date-format="dd-mm-yyyy hh:ii" readonly class="form_datetime">
					</div>
					
					<div class='form-group'>
						<label>Location :</label>
						<input name='client_appointment_location' class='form-control' placeholder='client_appointment_location'>
					</div>

					<div class='form-group'>
						<label>Google :</label>
						<input name='client_appointment_google' class='form-control' placeholder='client_appointment_google'>
					</div>
        
					<div class='form-group'>
						<label>Comment :</label>
						<input name='client_appointment_comment' class='form-control' placeholder='client_appointment_comment'>
					</div>

					<div id="client_appointments_users" class="list-group centre"></div>
					
					<input name="client_appointment_is_lead" id="client_appointment_is_lead" class="form-control" style="display:none;"> 

					<input name="client_appointmentsFORM_client_id" id="client_appointmentsFORM_client_id" class="form-control" style="display:none;">
					<input name="client_appointmentsFORM_updateID" id="client_appointmentsFORM_updateID" class="form-control" style="display:none;">

					<div class="modal-footer">
						<button id="bntCancel_CLIENT_APPOINTMENTS" type="button" class="btn btn-default" data-dismiss="modal">
							cancel
						</button>
						<button id="bntSave_CLIENT_APPOINTMENTS" class="btn btn-primary" type="submit" name="submit">
							save
						</button>
					</div>
                </form>
            </div><!-- End of Modal body -->
        </div><!-- End of Modal content -->
    </div><!-- End of Modal dialog -->
</div><!-- End of Modal -->
<!-- NEW CLIENT_APPOINTMENTS MODAL [END] -->
