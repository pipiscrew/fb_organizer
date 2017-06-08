<?php

$content_users = getSet($db,"select * from users",null);

?>

<script>

	$(function() {
	
		$("[name='create_room']").bootstrapSwitch();
		$("[name='room_type']").bootstrapSwitch();
		$("[name='graphics']").bootstrapSwitch();
	
	
		///////////////////////////////////////////////////////////// FILL users
		var jArray_users =   <?php echo json_encode($content_users); ?>;

		var combo_users_rows = "<option></option>";
		for (var i = 0; i < jArray_users.length; i++)
		{
			combo_users_rows += "<option value='" + jArray_users[i]["user_id"] + "'>" + jArray_users[i]["fullname"] + "</option>";
		}

		$("[name=account_executive_id]").html(combo_users_rows);
		$("[name=account_executive_id],[name=account_manager]").change();
		
		///////////////////////////////////////////////////////////// FILL users
		
				    ////////////////////////////////////////
				    // MODAL FUNCTIONALITIES [START]
				    //when modal closed, hide the warning messages + reset
				    $('#modalOFFER_ROOM_DETAILS').on('hidden.bs.modal', function() {
				        //when close - clear elements
				        $('#formOFFER_ROOM_DETAILS').trigger("reset");
				 
				        //clear validator error on form
				        validatorOFFER_ROOM_DETAILS.resetForm();
				    });
				 
				    //functionality when the modal already shown and its long, when reloaded scroll to top
				    $('#modalOFFER_ROOM_DETAILS').on('shown.bs.modal', function() {
				        $(this).animate({
				            scrollTop : 0
				        }, 'slow');
				    });
				    // MODAL FUNCTIONALITIES [END]
				    ////////////////////////////////////////
				    
				    //jquery.validate.min.js
				    var validatorOFFER_ROOM_DETAILS = $("#formOFFER_ROOM_DETAILS").validate({
				        rules : {
//				             offer_id : { required : true },
				             room_name : { required : true },
				             account_manager : { required : true },
				             account_executive_id : { required : true },
//				             posts_per_week : { required : true },
//				             post_language : { required : true },
//				             privacy : { required : true },
//				             post_rules : { required : true },
				             email1 : { required : true, email: true },
				             email2 : { email: true },
				             email3 : { email: true },
				             email4 : { email: true }
				        },
				        messages : {
//				            offer_id : 'Required Field',
				            room_name : 'Required Field',
				            account_manager : 'Required Field',
				            account_executive_id : 'Required Field',
//				            posts_per_week : 'Required Field',
//				            post_language : 'Required Field',
//				            privacy : 'Required Field',
//				            post_rules : 'Required Field',
				            email1 : 'Valid mail required',
				            email2 : 'Valid mail required',
				            email3 : 'Valid mail required',
				            email4 : 'Valid mail required'

				        }
				    });
				    
					////////////////////////////////////////
					// MODAL SUBMIT aka save & update button
					$('#formOFFER_ROOM_DETAILS').submit(function(e) {
					    e.preventDefault();
					 
					 	if (!$('[name=create_room]').bootstrapSwitch('state'))
					 	{
							alert("Please, turn on the 'Create Room' option!");
							return;
						}
					    ////////////////////////// validation
					    var form = $(this);
					    form.validate();
					 
					    if (!form.valid())
					        return;
					    ////////////////////////// validation
					 
					    var postData = $(this).serializeArray();
					    var formURL = $(this).attr("action");
					 
					    //close modal
//					    $('#modalOFFER_ROOM_DETAILS').modal('toggle');
					 	loading.appendTo($('#formOFFER_ROOM_DETAILS'));
					 	
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
					            else
					               { loading.remove(); alert("ERROR\r\n" + data);}
					        },
					        error: function(jqXHR, textStatus, errorThrown)
					        {
					        	loading.remove();
					            alert("ERROR - connection error");
					        }
					    });
					});
					
				//delete button by Content MODAL
				$('#bntDelete_OFFER_ROOM_DETAILS').on('click', function(e) {
					e.preventDefault();
					
					if (confirm("The action will delete the 'Content Details' for this offer, are you sure ?"))
					{
						loading.appendTo($('#formOFFER_ROOM_DETAILS'));
						
					    $.ajax(
					    {
					        url : "tab_clients_details_contracts_content_modal_delete.php",
					        type: "POST",
					        data : { offer_room_detail_id : $("#offer_room_detailsFORM_updateID").val() },
					        success:function(data, textStatus, jqXHR)
					        {
					        	if (data=='00000')
								{
									//refresh
									location.reload(true);
								}
								else{
									 loading.remove();
									 alert("ERROR - Cant delete the record.");
								}
									
					        },
					        error: function(jqXHR, textStatus, errorThrown)
					        {
					        	loading.remove();
					            alert("ERROR");
					        }
					    });
					}

				});
				
					
	});
			
 function edit_content(offer_id)
 {
	//store to modal hidden input, the offerID selected by primary grid!
	$("#content_offerID").val(offer_id);
	
					loading.appendTo(document.body);
					
				    $.ajax(
				    {
				        url : "tab_clients_details_contracts_content_modal_fetch.php",
				        type: "POST",
				        data : { offer_id : offer_id },
				        success:function(data, textStatus, jqXHR)
				        {
				        	loading.remove();
				        	
				        	if (!data.offer_room_detail_id)
			        		{
			        			if(data==0)
			        			{
								 	$('#lblTitle_OFFER_ROOM_DETAILS').html("New Content");
									$("#bntDelete_OFFER_ROOM_DETAILS").hide();
									
									$('[name=room_name]').val("pipiscrew & ");
									
									$('#modalOFFER_ROOM_DETAILS').modal('toggle');
									return;
								}
								else {
									alert(data);
									return;									
								}

							}
				        	
				        	if (data!='null')
							{
								$("#bntDelete_OFFER_ROOM_DETAILS").show();
								
							 	$("[name=offer_room_detailsFORM_updateID]").val(data.offer_room_detail_id);
								$('[name=create_room]').bootstrapSwitch('state',parseInt(data.create_room));
								$('[name=room_type]').bootstrapSwitch('state',parseInt(data.room_type));
								$('[name=room_name]').val(data.room_name);
								$('[name=account_manager]').val(data.account_manager);
								$('[name=account_executive_id]').val(data.account_executive_id);
								$('[name=posts_per_week]').val(data.posts_per_week);
								$('[name=graphics]').bootstrapSwitch('state',parseInt(data.graphics));
								$('[name=post_language]').val(data.post_language);
								$('[name=content_priv_radio]').val(data.privacy);

								$('[name=email1]').val(data.email1);
								$('[name=email2]').val(data.email2);
								$('[name=email3]').val(data.email3);
								$('[name=email4]').val(data.email4);
								$('[name=content_comment]').val(data.comment);

							 	
							 	$('#lblTitle_OFFER_ROOM_DETAILS').html("Edit Content");
								$('#modalOFFER_ROOM_DETAILS').modal('toggle');
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

<!-- NEW OFFER_ROOM_DETAILS MODAL [START] -->
<div class="modal fade" id="modalOFFER_ROOM_DETAILS" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
					&times;
				</button>
				<h4 class="modal-title" id='lblTitle_OFFER_ROOM_DETAILS'>New Content</h4>
			</div>
			<div class="modal-body">
				<form id="formOFFER_ROOM_DETAILS" role="form" method="post" action="tab_clients_details_contracts_content_modal_save.php">

<div class='row'>
<div class="col-md-6" style="text-align:right">
				<div class='form-group'>
					<label>Create Room&nbsp;<span class="glyphicon glyphicon-unchecked"></span></label><br>
					<input type="checkbox" data-on-text="Yes" data-off-text="No" name='create_room'>
				</div>
</div>
<div class="col-md-6" style="text-align:left">
				<div class='form-group'>
					<label>Room&nbsp;<span class="glyphicon glyphicon-new-window"></span></label><br>
					<input type="checkbox" data-on-text="Tech" data-off-text="Posting" name='room_type'>
		 		</div>
</div>
</div>

				<div class='form-group'>
					<label>Room Name :</label>
					<input name='room_name' class='form-control' placeholder='pipiscrew & ClientRoomName'>
				</div>
			
<div class='row'>
			<div class="col-md-6">
				<div class='form-group'>
					<label>Account Manager :</label>
					<select name='account_manager' class='form-control'>
						<option></option>
						<option>Nikos Cookies</option>
						<option>Eirini Tsesmetzi</option>
					</select>
				</div>
			</div>

			<div class="col-md-6">			
					<div class='form-group'>
						<label>Account Executive :</label>
						<select name='account_executive_id' class='form-control'>
						</select>
					</div>
			</div>
</div>
	
<div class='row'>
			<div class="col-md-6">
				<div class='form-group'>
					<label>Posts per Week :</label>
						<select id="v" name="posts_per_week" class='form-control'>
							<option>
								0
							</option>
							<option>
								2
							</option>
							<option>
								3
							</option>
							<option>
								4
							</option>
							<option>
								7
							</option>
							<option>
								14
							</option>
						</select>
						
				</div>
			</div>
			
			<div class="col-md-6">
				<div class='form-group'>
					<label>Graphics :</label><br>
					<input type="checkbox" data-on-text="Yes" data-off-text="No" name='graphics'>
				</div>
			</div>
</div>

<div class='row'>
			<div class="col-md-6">
				<div class='form-group'>
					<label>Post Language :</label>
						<select id="post_language" name="post_language" class='form-control'>
							<option>
								Greek
							</option>
							<option>
								English
							</option>
							<option>
								Spanish
							</option>
							<option>
								Russian
							</option>
							<option>
								Arabic
							</option>
							<option>
								French
							</option>
							<option>
								Indonesian
							</option>
							<option>
								Portuguese
							</option>
							<option>
								Romanian
							</option>
							<option>
								Albanian
							</option>
						</select>
						
				</div>
			</div>
			
			<div class="col-md-6">
				<div class='form-group'>
					<label>Privacy :</label>
					<div>
						<input id="isPublic" name="content_priv_radio" value="1" type="radio">
						<label for="isPublic"><img src="img/room_type_public.png"> Public </label>

						<input id="isClosed" name="content_priv_radio" value="2" type="radio">
						<label for="isClosed"><img src="img/room_type_closed.png"> Closed</label>	

						<input id="isSecret" name="content_priv_radio" value="3" type="radio" checked="checked">
						<label for="isSecret"><img src="img/room_type_secret.png"> Secret</label>
					</div>
				</div>
			</div>
</div>

				<div class='form-group'>
					<label>Emails to add :</label>
					
					<div class='row'>
						<div class="col-md-6">
							<div class='form-group'>
								<input name='email1' class='form-control' style="display: inline-block" placeholder='email1'>
							</div>
						</div>

						<div class="col-md-6">
							<div class='form-group'>
								<input name='email2' class='form-control' style="display: inline-block" placeholder='email2'>
							</div>
						</div>
					</div>
					
					<div class='row'>
						<div class="col-md-6">
							<div class='form-group'>
								<input name='email3' class='form-control' style="display: inline-block" placeholder='email3'>
							</div>
						</div>

						<div class="col-md-6">
							<div class='form-group'>
								<input name='email4' class='form-control' style="display: inline-block" placeholder='email4'>
							</div>
						</div>
					</div>

				</div>
						
				<div class='form-group'>
					<label>Comment :</label>
					<textarea rows="3" name='content_comment' style="resize:none" class='form-control' placeholder='comment'></textarea>
				</div>
			
				<center>
					<a href="http://pipiscrew.com/fb_com/!pipiscrewAPI_content_rules/" target="_blank"><span class="glyphicon glyphicon-user"></span>&nbsp;Post Rules Template&nbsp;<span class="glyphicon glyphicon-user"></span></a>
				</center>
				
						<!--holds the offer ID-->
						<input name="content_offerID" id="content_offerID" class="form-control" style="display:none;">
						
						<!-- <input name="offer_room_detailsFORM_FKid" id="OFFER_ROOM_DETAILS_FKid" class="form-control" style="display:none;"> -->
						<input name="offer_room_detailsFORM_updateID" id="offer_room_detailsFORM_updateID" class="form-control" style="display:none;">

						<div class="modal-footer">
							<button id="bntDelete_OFFER_ROOM_DETAILS" style="float:left" type="button" class="btn btn-danger">
								delete room
							</button>
							
							<button id="bntCancel_OFFER_ROOM_DETAILS" type="button" class="btn btn-default" data-dismiss="modal">
								cancel
							</button>
							<button id="bntSave_OFFER_ROOM_DETAILS" class="btn btn-primary" type="submit" name="submit">
								save
							</button>
						</div>
                </form>
            </div><!-- End of Modal body -->
        </div><!-- End of Modal content -->
    </div><!-- End of Modal dialog -->
</div><!-- End of Modal -->
<!-- NEW OFFER_ROOM_DETAILS MODAL [END] -->