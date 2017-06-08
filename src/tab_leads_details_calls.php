<?php
if(!isset($_SESSION))
{
	session_start();

	if(!isset($_SESSION["u"]))
	{
		header("Location: login.php");
		exit ;
	}
}
?>

<script type="text/javascript">
	///////////////// JQuery when document is ready
	$(function()
		{
			$('#btn_newCLIENT_CALLS').on('click', function(e)
				{
					e.preventDefault();

					$("#client_id").val("<?= $_GET["id"]; ?>");

					$('#modalCLIENT_CALLS').modal('toggle');

				});

			////////////////////////////////////////
			// MODAL FUNCTIONALITIES [START]
			//when modal closed, hide the warning messages + reset
			$('#modalCLIENT_CALLS').on('hidden.bs.modal', function()
				{
					//when close - clear elements
					$('#formCLIENT_CALLS').trigger("reset");

					//clear validator error on form
					validatorCLIENT_CALLS.resetForm();

					$('#bntSave_CLIENT_CALLS').text('save');
					$('#lblTitle_CLIENT_CALLS').text('New Call');

				});

			//functionality when the modal already shown and its long, when reloaded scroll to top
			$('#modalCLIENT_CALLS').on('shown.bs.modal', function()
				{
					$(this).animate(
						{
							scrollTop : 0
						}, 'slow');
				});
			// MODAL FUNCTIONALITIES [END]

			//delete button
			$('#btn_DelCLIENT_CALLS').on('click', function(e)
				{
					e.preventDefault();

					//get selected row - ID column
					var anSelected = getSelected('record_calls_TBL');
					if (anSelected == null)
					{
						alert("Please select a row!");
						return;
					}


					var rowData = anSelected[0];
					if (!confirm('Would you like to delete :\r\n' + rowData))
					return;
					
					loading.appendTo(document.body);


					$.ajax(
						{
							type : "POST",
							url : "tab_leads_details_calls_delete.php",
							data : "id=" + anSelected[0],
							datatype : "json",
							success : function(data)
							{
								loading.remove();
								
								if (data == "00000")
								{
									loadCALLSrecs();
								} else
								alert("Catastrophic Error!\r\nRecord not found!");
							},
							error : function()
							{
								loading.remove();
								
								alert("Connection Error!");
							}
						});

				});


			//edit button
			$('#btn_EditCLIENT_CALLS').on('click', function(e)
				{
					e.preventDefault();
					
					//get selected row - ID column
					var anSelected = getSelected('record_calls_TBL');
					if (anSelected == null)
					{
						alert("Please select a row!");
						return;
					}

					loading.appendTo(document.body);
					
					var rowData = anSelected[0];

					$.ajax(
						{
							type : "POST",
							url : "tab_leads_details_calls_fetch_record.php",
							data : "CLIENT_CALLSid=" + anSelected[0],
							datatype : "json",
							success : function(data)
							{
								loading.remove();
								
								if (data != null)
								{
									//set the db values to textboxes
									$('[name=client_call_id]').val(data.client_call_id);
									$('[name=client_call_datetime]').val(data.client_call_datetime);
									$('[name=client_call_discussion]').val(data.client_call_discussion);
									$('[name=client_call_next_call]').val(data.client_call_next_call);

									$('[name=chk_answered]').bootstrapSwitch('state',parseInt(data.chk_answered));
									$('[name=chk_company_presented]').bootstrapSwitch('state',parseInt(data.chk_company_presented));
									$('[name=chk_company_profile]').bootstrapSwitch('state',parseInt(data.chk_company_profile));
									$('[name=chk_client_proposal]').bootstrapSwitch('state',parseInt(data.chk_client_proposal));
									$('[name=chk_appointment_booked]').bootstrapSwitch('state',parseInt(data.chk_appointment_booked));

									//							$('[name=chk_answered]').val(data.chk_answered);
									//							$('[name=chk_company_presented]').val(data.chk_company_presented);
									//							$('[name=chk_company_profile]').val(data.chk_company_profile);
									//							$('[name=chk_client_proposal]').val(data.chk_client_proposal);
									//							$('[name=chk_appointment_booked]').val(data.chk_appointment_booked);
									$('[name=client_id]').val(data.client_id);
									$('[name=comment_call]').val(data.comment);


									//set form texts for update
									$('#bntSave_CLIENT_CALLS').text('Update Call');
									$('#lblTitle_CLIENT_CALLS').text('Edit Call');

									//set recordID
									$('[name=client_callsFORM_updateID]').val(anSelected[0]);

									//show modal
									$('#modalCLIENT_CALLS').modal('toggle');
								} else
								alert("Catastrophic Error!\r\nRecord not found!");
							},
							error : function()
							{
								loading.remove();
								
								alert("Connection Error!");
							}
						});

				});



			//set selected row on table
			$('#record_calls_TBL').on('click', 'tbody tr', function(event)
				{
					$(this).addClass('highlight').siblings().removeClass('highlight');
				});

			var validatorCLIENT_CALLS = $("#formCLIENT_CALLS").validate(
				{
					rules :
					{
						client_call_datetime :
						{
							required : true
						},
						client_call_discussion :
						{
							required : true
						}
					},
					messages :
					{
						client_call_datetime : 'Required field',
						client_call_discussion : 'Required field'
					}
				});

			//used for insert+update
			$('#formCLIENT_CALLS').submit(function(e)
				{
					e.preventDefault();
					var form = $(this);

					////////////////////////// validation
					form.validate();

					if (!form.valid())
					return;
					////////////////////////// validation

					$.ajax(
						{
							type : form.attr('method'),
							url : form.attr('action'),
							data : form.serialize(),
							success : function(data, textStatus, jqXHR)
							{
								console.log(data);
								if (data == "00000")
								{
									//close modal
									$('#modalCLIENT_CALLS').modal('toggle');
									loadCALLSrecs();
								} else
								{
									//warn user
									alert("Catastrophic Error!");
								}

							},
							error : function(jqXHR, textStatus, errorThrown)
							{
								alert("Catastrophic Error!\r\nRecord couldnt saved");
							}
							// ,
							// done: function($msg){
							// alert("!" + $msg);
							// }
						});
				});


		});
</script>
<br/>
<button id="btn_newCLIENT_CALLS" type="button" class="btn btn-success">
	New
</button>
<button id="btn_EditCLIENT_CALLS" type="button" class="btn btn-primary">
	Edit
</button>

<?php if ($_SESSION['level']==9) { ?>
<button id="btn_DelCLIENT_CALLS" type="button" class="btn btn-danger">
	Delete
</button>
<?php } ?>

<table id='record_calls_TBL' class="table table-striped" >

	<thead>
		<tr>
			<th tabindex="0" rowspan="1" colspan="1">
				ID
			</th>
			<th tabindex="0" rowspan="1" colspan="1">
				Datetime
			</th>
			<th tabindex="0" rowspan="1" colspan="1">
				Answered
			</th>
			<th tabindex="0" rowspan="1" colspan="1">
				Company Presented
			</th>
			<th tabindex="0" rowspan="1" colspan="1">
				Company Profile
			</th>
			<th tabindex="0" rowspan="1" colspan="1">
				Client Proposal
			</th>
			<th tabindex="0" rowspan="1" colspan="1">
				Appointment Booked
			</th>
			<th tabindex="0" rowspan="1" colspan="1">
				Next Call
			</th>
		</tr>
	</thead>

	<tbody id="record_calls_rows">
	</tbody>
</table>


<!-- TAB CLIENT_CALLS [START] -->
<div class="tab-pane fade in active" id="client_callsTAB">
<!-- NEW CLIENT_CALLS MODAL [START] -->
<div class="modal fade" id="modalCLIENT_CALLS" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
					&times;
				</button>
				<h4 class="modal-title" id='lblTitle_CLIENT_CALLS'>
					New Call
				</h4>
			</div>
			<div class="modal-body">
				<form id="formCLIENT_CALLS" role="form" method="post" action="tab_leads_details_calls_save.php">

				<div class='form-group'>
					<label style="width: 140px">
						When :
					</label>
					<input class="form_datetime" type="text" name="client_call_datetime" readonly="readonly" data-date-format="dd-mm-yyyy hh:ii" />
				</div>

				<div class='form-group'>
					<label>
						Call Discussion :
					</label>
					<textarea  style="resize: none;" rows="3" name='client_call_discussion' class='form-control' placeholder='Call Discussion'></textarea>
				</div>

				<div class='form-group'>
					<label style="width: 140px">
						Next Call :
					</label>
					<input class="form_datetime" type="text" name="client_call_next_call" readonly="readonly" data-date-format="dd-mm-yyyy hh:ii" />
				</div>

				<div class='form-group'>
					<label style="width: 140px">
						Answered :
					</label>
					<input type="checkbox" name='chk_answered'>
				</div>

				<div class='form-group'>
					<label style="width: 140px">
						Company Presented :
					</label>
					<input type="checkbox" name='chk_company_presented'>
				</div>

				<div class='form-group'>
					<label style="width: 140px">
						Company Profile :
					</label>
					<input type="checkbox" name='chk_company_profile'>
				</div>

				<div class='form-group'>
					<label style="width: 140px">
						Client Proposal :
					</label>
					<input type="checkbox" name='chk_client_proposal'>
				</div>

				<div class='form-group'>
					<label style="width: 140px">
						Appointment Booked :
					</label>
					<input type="checkbox" name='chk_appointment_booked'>
				</div>

				<div class='form-group'>
					<label style="width: 140px">
						Comment :
					</label>
					<textarea style="resize: none;" rows="3" name='comment_call' class='form-control' placeholder=Comment></textarea>
				</div>


				<input name="client_id" id="client_id" class="form-control" style="display:none;">
				<input name="client_callsFORM_updateID" id="client_callsFORM_updateID" class="form-control" style="display:none;">

			
				<div class="modal-footer">
					<button id="bntCancel_CLIENT_CALLS" type="button" class="btn btn-default" data-dismiss="modal">
						cancel
					</button>
					<button id="bntSave_CLIENT_CALLS" class="btn btn-primary" type="submit" name="submit">
						save
					</button>
				</div>
			</form>
			</div>
		</div>
		
	</div>
</div>
<!-- NEW CLIENT_CALLS MODAL [END] -->
</div>