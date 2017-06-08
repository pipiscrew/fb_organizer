<link rel="stylesheet" href="css/bootstrap-tagsinput.css"></link>
<script src="js/bootstrap-tagsinput.min.js"></script>
<script src="js/bootstrap3-typeahead.min.js"></script>

<style>
@media (min-width: 992px) {
  .modal-lg {
    width: 900px;
  }
}

.mycheckbox {
	width: 26px;
	height: 26px;
	background: transparent url(img/checkbox.png) }
.mycheckboxon {
	background: transparent url(img/checkbox_on.png)}

.bootstrap-tagsinput { width: 100%; }
</style>

<script>
	
	$(function() {
		
		$("[name=ad_placement_mobile],[name=ad_placement_desktop],[name=ad_placement_desktop_right],[name=ad_placement_audience_network]").click(function() {
                $(this).toggleClass('mycheckboxon');
        });
                    
			//typeahead		
			$('[name=aud_countries]').tagsinput({
			  typeahead: {
			    source: ['Greece','United Kingdom','Italy','Czech Republic','Poland','Monaco','Egypt','Jordan','Taiwan','Romania','Cyprus','Turkey','Mexico','Mauritius','Lithuania','Serbia','Bulgaria']
			  },
			  confirmKeys: [13],
			  trimValue: true,
			  freeInput: true
			});

			$('[name=aud_languages]').tagsinput({
			  typeahead: {
			    source: ['Greek','English','Spanish','Russian','Arabic','French','Indonesian','Portuguese','Romanian','Albanian']
			  },
			  trimValue: true
			});
			
			$('[name=ad_keywords],[name=ad_client_goals],[name=aud_interests],[name=aud_behaviors],[name=ad_connections]').tagsinput({
			  trimValue: true
			});
			
		    ////////////////////////////////////////
		    // MODAL FUNCTIONALITIES [START]
		    //when modal closed, hide the warning messages + reset
		    $('#modalOFFER_ADVERTISE_DETAILS').on('hidden.bs.modal', function() {
		        //when close - clear elements
		        $('#formOFFER_ADVERTISE_DETAILS').trigger("reset");
		 
		        //clear validator error on form
		        validatorOFFER_ADVERTISE_DETAILS.resetForm();

				
				$("[name=ad_keywords],[name=ad_client_goals],[name=aud_countries],[name=aud_languages],[name=aud_interests],[name=aud_behaviors],[name=ad_connections]").tagsinput('removeAll');
				$("[name=ad_placement_mobile],[name=ad_placement_desktop],[name=ad_placement_desktop_right],[name=ad_placement_audience_network]").removeClass("mycheckboxon");
//				$("[name=ad_placement_mobile],[name=ad_placement_desktop],[name=ad_placement_desktop_right],[name=ad_placement_audience_network]").addClass("mycheckbox");
		    });
		 
		    //functionality when the modal already shown and its long, when reloaded scroll to top
		    $('#modalOFFER_ADVERTISE_DETAILS').on('shown.bs.modal', function() {
		        $(this).animate({
		            scrollTop : 0
		        }, 'slow');
		    });
		    // MODAL FUNCTIONALITIES [END]
		    ////////////////////////////////////////
				    
				    //jquery.validate.min.js
				    var validatorOFFER_ADVERTISE_DETAILS = $("#formOFFER_ADVERTISE_DETAILS").validate({
				        rules : {
//				             offer_id : { required : true },
				             user4_send_ppl_website : { required : true, greaterThanZero : true },
				             user4_increase_conversions : { required : true, greaterThanZero : true },
				             user4_boost_posts : { required : true, greaterThanZero : true },
				             user4_promote_page_likes : { required : true, greaterThanZero : true },
				             user4_get_installs_app : { required : true, greaterThanZero : true },
				             user4_increase_engag : { required : true, greaterThanZero : true },
				             user4_raise_attendance : { required : true, greaterThanZero : true },
				             user4_claim_offer : { required : true, greaterThanZero : true },
				             user4_video_views : { required : true, greaterThanZero : true },
				             aud_countries : { required : true }
//				             ad_keywords : { required : true },
//				             ad_client_goals : { required : true },
//				             ad_fb1 : { required : true, url:true },
//				             ad_fb2 : { url:true },
//				             ad_fb3 : { url:true },
//				             ad_fb4 : { url:true },
				             
//				             aud_age_min : { required : true },
//				             aud_age_max : { required : true },
//				             aud_languages : { required : true },
//				             aud_interests : { required : true },
//				             aud_behaviors : { required : true },
//				             ad_connections : { required : true },
//				             ad_placement_mobile : { required : true },
//				             ad_placement_desktop : { required : true },
//				             ad_placement_desktop_right : { required : true },
//				             ad_placement_audience_network : { required : true },

				        },
				        messages : {
//				            offer_id : 'Required Field',
//				            user4_send_ppl_website : 'Required Field',
//				            user4_increase_conversions : 'Required Field',
//				            user4_boost_posts : 'Required Field',
//				            user4_promote_page_likes : 'Required Field',
//				            user4_get_installs_app : 'Required Field',
//				            user4_increase_engag : 'Required Field',
//				            user4_raise_attendance : 'Required Field',
//				            user4_claim_offer : 'Required Field',
//				            user4_video_views : 'Required Field',
//				            ad_keywords : 'Required Field',
//				            ad_client_goals : 'Required Field',
				          //  ad_fb1 : 'Required Field',
//				            ad_fb2 : 'Required Field',
//				            ad_fb3 : 'Required Field',
//				            ad_fb4 : 'Required Field',
				            aud_countries : 'Required Field'
//				            aud_age_min : 'Required Field',
//				            aud_age_max : 'Required Field',
//				            aud_gender : 'Required Field',
//				            aud_languages : 'Required Field',
//				            aud_interests : 'Required Field',
//				            aud_behaviors : 'Required Field',
//				            ad_connections : 'Required Field',
//				            ad_placement_mobile : 'Required Field',
//				            ad_placement_desktop : 'Required Field',
//				            ad_placement_desktop_right : 'Required Field',
//				            ad_placement_audience_network : 'Required Field',

				        }
				    });
				    
			////////////////////////////////////////
			// MODAL SUBMIT aka save & update button
			$('#formOFFER_ADVERTISE_DETAILS').submit(function(e) {
			    e.preventDefault();
			 
			    ////////////////////////// validation
			    var form = $(this);
			    form.validate();
			 
			    if (!form.valid())
			        return;
			    ////////////////////////// validation
			 
			 	if ($('[name=aud_countries]').val().length < 5)
		 		{
					alert("Please fill 'Countries' field.");
					return;
				}
				
			    var postData = $(this).serializeArray();
			    var formURL = $(this).attr("action");
			 
			 
				//add checkbox items!
				var ad_placement_audience_network = $('[name=ad_placement_audience_network]').css('background-image').indexOf('_on.png')>0 ? 1 : 0;
				var ad_placement_desktop_right = $('[name=ad_placement_desktop_right]').css('background-image').indexOf('_on.png')>0 ? 1 : 0;
				var ad_placement_desktop = $('[name=ad_placement_desktop]').css('background-image').indexOf('_on.png')>0 ? 1 : 0;
				var ad_placement_mobile = $('[name=ad_placement_mobile]').css('background-image').indexOf('_on.png')>0 ? 1 : 0;
				
				postData.push({name: "ad_placement_audience_network", value : JSON.stringify(ad_placement_audience_network)});
				postData.push({name: "ad_placement_desktop_right", value : JSON.stringify(ad_placement_desktop_right)});
				postData.push({name: "ad_placement_desktop", value : JSON.stringify(ad_placement_desktop)});
				postData.push({name: "ad_placement_mobile", value : JSON.stringify(ad_placement_mobile)});
						
			    //close modal
			    loading.appendTo($('#formOFFER_ADVERTISE_DETAILS'));
			 
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
		
 function edit_advertise(offer_id)
 {
	//store to modal hidden input, the offerID selected by primary grid!
	$("#advertise_offerID").val(offer_id);
	
		loading.appendTo(document.body);
		
	    $.ajax(
	    {
	        url : "tab_clients_details_contracts_advertise_modal_fetch.php",
	        type: "POST",
	        data : { offer_id : offer_id },
	        success:function(data, textStatus, jqXHR)
	        {
	        	loading.remove();
	        	
				if (data.ad_obj)
	        		$("#ad_obj").html(data.ad_obj);
	        	else 
					alert("Cant fetch ad objs, please inform administrator");
        	
	        	
	        	if (!data.rec.offer_advertise_detail_id)
        		{
        			if(data.rec==0)
        			{
					 	$('#lblTitle_OFFER_ADVERTISE_DETAILS').html("New Advertise");
						$('#modalOFFER_ADVERTISE_DETAILS').modal('toggle');
						return;
					}
					else {
						alert(data.rec);
						return;									
					}

				}
	        	
	        	
	        	
	        	if (data!='null')
				{
					
				 	$("[name=offer_advertise_detailsFORM_updateID]").val(data.rec.offer_advertise_detail_id);
				 	
				 	if ($('[name=user4_send_ppl_website]'))
						$('[name=user4_send_ppl_website]').val(data.rec.user4_send_ppl_website);
					
					if ($('[name=user4_increase_conversions]'))	
						$('[name=user4_increase_conversions]').val(data.rec.user4_increase_conversions);
					
					if ($('[name=user4_boost_posts]'))
						$('[name=user4_boost_posts]').val(data.rec.user4_boost_posts);
					
					if ($('[name=user4_promote_page_likes]'))
						$('[name=user4_promote_page_likes]').val(data.rec.user4_promote_page_likes);
					
					if ($('[name=user4_get_installs_app]'))
						$('[name=user4_get_installs_app]').val(data.rec.user4_get_installs_app);
					
					if ($('[name=user4_increase_engag]'))
						$('[name=user4_increase_engag]').val(data.rec.user4_increase_engag);
					
					if ($('[name=user4_raise_attendance]'))
						$('[name=user4_raise_attendance]').val(data.rec.user4_raise_attendance);
					
					if ($('[name=user4_claim_offer]'))
						$('[name=user4_claim_offer]').val(data.rec.user4_claim_offer);
					
					if ($('[name=user4_video_views]'))
						$('[name=user4_video_views]').val(data.rec.user4_video_views);
					
					$('[name=ad_keywords]').tagsinput('add', data.rec.ad_keywords);
					$('[name=ad_client_goals]').tagsinput('add', data.rec.ad_client_goals);
					$('[name=ad_fb1]').val(data.rec.ad_fb1);
					$('[name=ad_fb2]').val(data.rec.ad_fb2);
					$('[name=ad_fb3]').val(data.rec.ad_fb3);
					$('[name=ad_fb4]').val(data.rec.ad_fb4);
					$('[name=aud_countries]').tagsinput('add', data.rec.aud_countries);
					$('[name=aud_age_min]').val(data.rec.aud_age_min);
					$('[name=aud_age_max]').val(data.rec.aud_age_max);
					
					
					if (data.rec.aud_gender=="All")
						$("#all").prop('checked',true);
					else if (data.rec.aud_gender=="Men")
						$("#men").prop('checked',true);	
					else if (data.rec.aud_gender=="Women")
						$("#women").prop('checked',true);	
						
					//$('[name=aud_gender]').val(data.rec.aud_gender);
					$('[name=aud_languages]').tagsinput('add', data.rec.aud_languages);
					$('[name=aud_interests]').tagsinput('add', data.rec.aud_interests);
					$('[name=aud_behaviors]').tagsinput('add', data.rec.aud_behaviors);
					$('[name=ad_connections]').tagsinput('add', data.rec.ad_connections);
			
					if (data.rec.ad_placement_mobile==1)
					{
						$("[name=ad_placement_mobile]").addClass('mycheckboxon')
					}

					if (data.rec.ad_placement_desktop==1)
					{
						$("[name=ad_placement_desktop]").addClass('mycheckboxon')
					}

					if (data.rec.ad_placement_desktop_right==1)
					{
						$("[name=ad_placement_desktop_right]").addClass('mycheckboxon')
					}

					if (data.rec.ad_placement_audience_network==1)
					{
						$("[name=ad_placement_audience_network]").addClass('mycheckboxon')
					}
					 	
					 						 						 						 	
//					$('[name=ad_placement_mobile]').val(data.rec.ad_placement_mobile);
//					$('[name=ad_placement_desktop]').val(data.rec.ad_placement_desktop);
//					$('[name=ad_placement_desktop_right]').val(data.rec.ad_placement_desktop_right);
//					$('[name=ad_placement_audience_network]').val(data.rec.ad_placement_audience_network);

				 	
				 	$('#lblTitle_OFFER_ADVERTISE_DETAILS').html("Edit Advertise");
					$('#modalOFFER_ADVERTISE_DETAILS').modal('toggle');
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
<!-- NEW OFFER_ADVERTISE_DETAILS MODAL [START] -->
<div class="modal fade" id="modalOFFER_ADVERTISE_DETAILS" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<!--<div class="modal-dialog">-->
<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
					&times;
				</button>
				<h4 class="modal-title" id='lblTitle_OFFER_ADVERTISE_DETAILS'>New</h4>
			</div>
			<div class="modal-body">
				<form id="formOFFER_ADVERTISE_DETAILS" role="form" method="post" action="tab_clients_details_contracts_advertise_modal_save.php">
			
			<div id="ad_obj" style=" display: table;margin: 0 auto;">
				
			</div>
			
			 <p style="background-color: #428bca;color:#fff;padding: 5px;" align=center>AD Specification</p>
			 
					<div class='form-group'>
						<label>Keywords (hashtags) :</label><br>
						<input name='ad_keywords' class='form-control'>
					</div>
					
    		<div class="row">
       			<div class="col-md-6">
					<div class='form-group'>
						<label>Competitor1 :</label>
						<input name='ad_fb1' class='form-control' placeholder='http://facebook.com/pipiscrew'>
					</div>
				</div>
				
        		<div class="col-md-6">
					<div class='form-group'>
						<label>Competitor2 :</label>
						<input name='ad_fb2' class='form-control' placeholder='http://facebook.com/pipiscrew'>
					</div>
				</div>
				

			</div>
				
    		<div class="row">
       			<div class="col-md-6">
					<div class='form-group'>
						<label>Competitor3 :</label>
						<input name='ad_fb3' class='form-control' placeholder='http://facebook.com/pipiscrew'>
					</div>
				</div>
				
        		<div class="col-md-6">
					<div class='form-group'>
						<label>Competitor4 :</label>
						<input name='ad_fb4' class='form-control' placeholder='http://facebook.com/pipiscrew'>
					</div>
				</div>
			</div>
	

					<div class='form-group'>
						<label>Client Goals :</label><br>
						<input name='ad_client_goals' class='form-control'>
					</div>
					
			 <p style="background-color: #428bca;color:#fff;padding: 5px;" align=center>Target Audience</p>
								
					<div class='form-group'>
						<label>Countries :</label>
						<input type="text" name='aud_countries' class='form-control'>
					</div>
					
				
    		<div class="row">
        		<div class="col-md-4">
					<div class='form-group'>
						<label>Age Min :</label>
						<input name='aud_age_min' type="number" value=13 min="13" max="65" step="1" style="width:70px" class='form-control'>
					</div>
				</div>
				
       			<div class="col-md-4">
					<div class='form-group'>
						<label>Age Max :</label>
						<input name='aud_age_max' type="number" value=0 min="13" max="65" step="1" style="width:70px" class='form-control'>
					</div>
				</div>
				
       			<div class="col-md-4">
					<div class='form-group'>
						<label>Gender :</label>
						
						<div>
							<input id="all" name="aud_gender" value="All" type="radio" checked="checked">
							<label for="all">&nbsp;All&nbsp;&nbsp;&nbsp;&nbsp;</label>

							<input id="men" name="aud_gender" value="Men" type="radio">
							<label for="men">&nbsp;Men&nbsp;&nbsp;&nbsp;&nbsp;</label>	

							<input id="women" name="aud_gender" value="Women" type="radio">
							<label for="women">&nbsp;Women&nbsp;&nbsp;</label>
						</div>
					</div>
				</div>
			</div>
			

				
					<div class='form-group'>
						<label>Languages :</label>
						<input name='aud_languages' class='form-control'>
					</div>
				
					<div class='form-group'>
						<label>Interests :</label>
						<input name='aud_interests' class='form-control'>
					</div>
				
					<div class='form-group'>
						<label>Behaviors :</label>
						<input name='aud_behaviors' class='form-control'>
					</div>
				
					<div class='form-group'>
						<label>Connections :</label>
						<input name='ad_connections' class='form-control'>
					</div>
				
<div class='row'>
				<div class='col-md-3'>
					<div class='form-group'>
						<label>Mobile News Feed :</label>
						<div name="ad_placement_mobile" class="mycheckbox"></div>
					</div>
				</div>
				
				<div class='col-md-3'>
					<div class='form-group'>
						<label>Desktop News Feed :</label>
						<div name='ad_placement_desktop' class="mycheckbox"></div>
					</div>
				</div>

				<div class='col-md-3'>								
					<div class='form-group'>
						<label>Desktop Right Column :</label>
						<div name="ad_placement_desktop_right" class="mycheckbox"></div>
					</div>
				</div>

				<div class='col-md-3'>	
					<div class='form-group'>
						<label>Audience Network :</label>
						<div name="ad_placement_audience_network" class="mycheckbox"></div>
					</div>
				</div>
</div>

						<!--holds the offer ID-->
						<input name="advertise_offerID" id="advertise_offerID" class="form-control" style="display:none;">
						
						<!-- <input name="offer_advertise_detailsFORM_FKid" id="OFFER_ADVERTISE_DETAILS_FKid" class="form-control" style="display:none;"> -->
						<input name="offer_advertise_detailsFORM_updateID" id="offer_advertise_detailsFORM_updateID" class="form-control" style="display:none;">

						<div class="modal-footer">
							<button id="bntCancel_OFFER_ADVERTISE_DETAILS" type="button" class="btn btn-default" data-dismiss="modal">
								cancel
							</button>
							<button id="bntSave_OFFER_ADVERTISE_DETAILS" class="btn btn-primary" type="submit" name="submit">
								save
							</button>
						</div>
                </form>
            </div><!-- End of Modal body -->
        </div><!-- End of Modal content -->
    </div><!-- End of Modal dialog -->
</div><!-- End of Modal -->
<!-- NEW OFFER_ADVERTISE_DETAILS MODAL [END] -->