<?php
require_once ('config.php');

$db = connect();

$rows_appointmentUSERS=null;
///////////////////READ USERS

	$find_sql = "select * from users";
	$stmt      = $db->prepare($find_sql);
	$stmt->execute();
	$rows_appointmentUSERS = $stmt->fetchAll();

///////////////////READ USERS


	require_once ('template_top.php');
	
	
?>


<link href='js/fullcalendar.min.css' rel='stylesheet' />
<link href='js/fullcalendar.print.css' rel='stylesheet' media='print' />
<script src='js/moment.min.js'></script>
<script src='js/fullcalendar.min.js'></script>
<script>

var jArray_appointmentUSERS = <?php echo json_encode($rows_appointmentUSERS); ?>;

	$(document).ready(function() {
	
					$('#client_appointments_users').chooser();
					
					
					
					if (jArray_appointmentUSERS)
						$("#client_appointments_users").fillList(jArray_appointmentUSERS,"Participants", "user_id", "fullname");
						
						
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
				    
	
	var legend_bar_tmp = "<p style='background-color: {bgcolor};color:#fff;padding: 5px;margin-top: 10px' align=center>{caption}</p>";
	
		$('#calendar').fullCalendar({
			header: {
				left: 'prev,next today',
				center: 'title',
				right: 'month,agendaWeek,agendaDay'
			},
			defaultDate: '<?=date("Y")?>-<?=date("m")?>-01',
			editable: false,
			eventLimit: true, // allow "more" link when too many events
			events: {
				url: 'tab_dashboard_seller_appointments_query.php',
				success :  function(e) {

var color_bars="";
var tmp = "";
var prev_owner="";
				for(var no in e)
				{
					if (prev_owner==e[no].owner)
						continue;
						

					tmp ="";
					tmp = legend_bar_tmp.replace('{bgcolor}',e[no].color);
					tmp = tmp.replace('{caption}',e[no].owner);
					color_bars+=tmp;
					
					prev_owner=e[no].owner;
				}

				$("#legends").html(color_bars);
					
				},
				error: function() {
					alert("error");
				}
			},
			loading: function(bool) {
				$('#loading').toggle(bool);
			},
		    eventClick: function(calEvent, jsEvent, view) {
		    	query_CLIENT_APPOINTMENTS_modal(calEvent.id);
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
					            	location.reload(true);
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
					
	}); //jQuery end

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

if (data.client_appointment_owner_id!=<?= $_SESSION["id"]?>)
{
								$("#appointment_detail").show();
	
								$('#proposal_date').val(data.client_appointment_datetime);
								$('#location').val(data.client_appointment_location);
								$('#google').val(data.client_appointment_google);
								$('#comment').val(data.client_appointment_comment);
		
		var parts="";
for (no in dataP){
	
	for (no2 in jArray_appointmentUSERS){
		if (jArray_appointmentUSERS[no2]['user_id']==dataP[no]['user_id'])	
			//console.log(jArray_appointmentUSERS[no2]['fullname']);
			parts+="<li>" + jArray_appointmentUSERS[no2]['fullname'] + "</li>";
	}
	
}

$("#parts").html(parts);

//console.log(dataP);

}								
else {
								$("#appointment_detail").hide();
								
								$("#client_appointments_users").setSelected(dataP,"user_id")

							 	$("[name=client_appointmentsFORM_updateID]").val(data.client_appointment_id);
								$('[name=client_appointment_datetime]').val(data.client_appointment_datetime);
								$('[name=client_appointment_location]').val(data.client_appointment_location);
								$('[name=client_appointment_google]').val(data.client_appointment_google);
								$('[name=client_appointment_comment]').val(data.client_appointment_comment);

							 	$('#lblTitle_CLIENT_APPOINTMENTS').html(data.client_name);
							 	
								$('#modalCLIENT_APPOINTMENTS').modal('toggle');
}

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
</script>
<style>

	body {
		margin: 0;
		padding: 0;
		font-family: "Lucida Grande",Helvetica,Arial,Verdana,sans-serif;
		font-size: 14px;
	}

	.fc-event{
	    cursor: pointer;
	}

	#loading {
		display: none;
		position: absolute;
		top: 10px;
		right: 10px;
	}

	#calendar {
		max-width: 900px;
		margin: 40px auto;
		padding: 0 10px;
	}

</style>

	<div id='loading'>loading...</div>

	<div id='legends' style="margin-top:100px;width:150px;position:absolute;right:0;top:0;">
		
	</div>
	
	<div id='appointment_detail' style="float:left;margin-left:20px;display:none;">
	  <div>
	    <label>Datetime :</label><br>
	    <input id="proposal_date" readonly>
	  </div>
	  
	  <div>
	    <label>Location :</label><br>
	    <input style="width:300px" id="location" readonly>
	  </div>
	  
	  <div>
	    <label>Google :</label><br>
	    <input style="width:300px" id="google" readonly>
	  </div>
	  
	  <div>
	    <label>Comment :</label><br>
	    <input style="width:300px" id="comment" readonly>
	  </div>
	  
	  <div>
	    <label>Participants :</label><br>
	    <ul id="parts">
	    	
	    </ul>
	  </div>
	  
	</div>


	<div id='calendar' style="padding:50px"></div>


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

<?php
include ('template_bottom.php');
?>
