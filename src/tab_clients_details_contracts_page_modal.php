<style>
	.bootstrap-switch-large{
    width: 200px;
}
</style>

<script>


			$(function (){
				$("[name='is_creation']").bootstrapSwitch();
				$("[name='reviews']").bootstrapSwitch();
				$("[name='cover_photo_change']").bootstrapSwitch();
				$("[name='profile_photo_change']").bootstrapSwitch();
				
				$("#call_action").change(); //no conflict @ POST
				
				$('[name=is_creation]').on('switchChange.bootstrapSwitch', function (event, state) {
					if (state) //when is_creation
						$("#go2fb").show();
					else 
						$("#go2fb").hide();
				});
				
				    ////////////////////////////////////////
				    // MODAL FUNCTIONALITIES [START]
				    //when modal closed, hide the warning messages + reset
				    $('#modalOFFER_PAGE_DETAILS').on('hidden.bs.modal', function() {
				        //when close - clear elements
				        $('#formOFFER_PAGE_DETAILS').trigger("reset");
				 
				        //clear validator error on form
				        validatorOFFER_PAGE_DETAILS.resetForm();
				        
				        $("#go2fb").hide();
				    });
				 
				    //functionality when the modal already shown and its long, when reloaded scroll to top
				    $('#modalOFFER_PAGE_DETAILS').on('shown.bs.modal', function() {
				        $(this).animate({
				            scrollTop : 0
				        }, 'slow');
				        
				    });
				    // MODAL FUNCTIONALITIES [END]
				    ////////////////////////////////////////
				    
				    //jquery.validate.min.js
				    var validatorOFFER_PAGE_DETAILS = $("#formOFFER_PAGE_DETAILS").validate({
				        rules : {
				             domain : { url: true },
				             website : { url: true },
				        },
				        messages : {
				            domain : 'A valid website or an empty field',
				            website : 'A valid website or an empty field',
				        }
				    });
				    
					////////////////////////////////////////
					// MODAL SUBMIT aka save & update button
					$('#formOFFER_PAGE_DETAILS').submit(function(e) {
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
					    loading.appendTo($('#formOFFER_PAGE_DETAILS'));
					 
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
			})
			
			 function edit_page(offer_id)
			 {
				//store to modal hidden input, the offerID selected by primary grid!
				$("#page_offerID").val(offer_id);
			
				loading.appendTo(document.body);
				
			    $.ajax(
			    {
			        url : "tab_clients_details_contracts_page_modal_fetch.php",
			        type: "POST",
			        data : { offer_id : offer_id },
			        success:function(data, textStatus, jqXHR)
			        {
			        	loading.remove();
			        	
			        	if (!data.offer_page_detail_id)
		        		{
		        			if(data==0)	{
							 	$('#lblTitle_OFFER_PAGE_DETAILS').html("New Page");
								$('#modalOFFER_PAGE_DETAILS').modal('toggle');
								return;
							} else {
								alert(data);
								return;									
							}

						}
			        	
			        	if (data!='null')
						{
						 	$("[name=offer_page_detailsFORM_updateID]").val(data.offer_page_detail_id);
							$('[name=is_creation]').bootstrapSwitch('state',parseInt(data.is_creation));
							$('[name=domain]').val(data.domain);
							$('[name=reviews]').bootstrapSwitch('state',parseInt(data.reviews));
							$('[name=call_action]').val(data.call_action);
							$('[name=website]').val(data.website);
							$('[name=cover_photo_change]').bootstrapSwitch('state',parseInt(data.cover_photo_change));
							$('[name=profile_photo_change]').bootstrapSwitch('state',parseInt(data.profile_photo_change));
							$('[name=short_description]').val(data.short_description);
						 	
						 	$('#lblTitle_OFFER_PAGE_DETAILS').html("Edit Page");
							$('#modalOFFER_PAGE_DETAILS').modal('toggle');
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

<!-- NEW OFFER_PAGE_DETAILS MODAL [START] -->
<div class="modal fade" id="modalOFFER_PAGE_DETAILS" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
					&times;
				</button>
				<h4 class="modal-title" id='lblTitle_OFFER_PAGE_DETAILS'>New</h4>
			</div>
			<div class="modal-body">
				<form id="formOFFER_PAGE_DETAILS" role="form" method="post" action="tab_clients_details_contracts_page_modal_save.php">
				
					<div class='form-group'>
						<label>Page : </label><br>
						<input type="checkbox" data-size="large" data-on-text="Creation" data-off-text="Optimization" name='is_creation'>
						&nbsp;&nbsp;<a id="go2fb" style="display:none;" onclick="window.open('http://www.facebook.com/pages/create/');" class="btn btn-success btn-xs">Goto Facebook</a>
					</div>
				
					<div class='form-group'>
						<label>Domain URL :</label>
						<input name='domain' class='form-control' maxlength="150" placeholder='http://facebook.com/pipiscrew'>
					</div>
				
					<div class='form-group'>
						<label>Reviews :</label><br>
						<input type="checkbox" name='reviews'>
					</div>
				
					<div class='form-group'>
						<label>Call-Action-Button :</label>
						<select id="call_action" name="call_action" class='form-control'>
							<option></option><option>
								Shop Now
							</option>
							<option>
								Book Now
							</option>
							<option>
								Contact Us
							</option>
							<option>
								Use App
							</option>
							<option>
								Play Game
							</option>
							<option>
								Shop Now
							</option>
							<option>
								Sign Up
							</option>
							<option>
								Watch Video
							</option>
						</select>
					</div>
				
					<div class='form-group'>
						<label>Website :</label>
						<input name='website' class='form-control' maxlength="150" placeholder='http://localhost:8080'>
					</div>
				
					<div class='form-group'>
						<label>Cover Photo Needs Change :</label><br>
						<input type="checkbox" data-on-text="Yes" data-off-text="No" name='cover_photo_change'>
					</div>
				
					<div class='form-group'>
						<label>Profile Photo Needs Change :</label><br>
						<input type="checkbox" data-on-text="Yes" data-off-text="No" name='profile_photo_change'>
					</div>
				
					<div class='form-group'>
						<label>Short Description :</label>
						<input name='short_description' class='form-control' maxlength="150" placeholder='short_description'>
					</div>


					<!--holds the offer ID-->
					<input name="page_offerID" id="page_offerID" class="form-control" style="display:none;">
						
					<!-- <input name="offer_page_detailsFORM_FKid" id="OFFER_PAGE_DETAILS_FKid" class="form-control" style="display:none;"> -->
					<input name="offer_page_detailsFORM_updateID" id="offer_page_detailsFORM_updateID" class="form-control" style="display:none;">

					<div class="modal-footer">
						<button id="bntCancel_OFFER_PAGE_DETAILS" type="button" class="btn btn-default" data-dismiss="modal">
							cancel
						</button>
						<button id="bntSave_OFFER_PAGE_DETAILS" class="btn btn-primary" type="submit" name="submit">
							save
						</button>
					</div>
                </form>
            </div><!-- End of Modal body -->
        </div><!-- End of Modal content -->
    </div><!-- End of Modal dialog -->
</div><!-- End of Modal -->
<!-- NEW OFFER_PAGE_DETAILS MODAL [END] -->