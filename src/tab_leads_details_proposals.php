<?php
// include DB
require_once ('config.php');

$db             = connect();


$proposals = null;
///////////////////READ Proposals
$proposals = getSet($db,"select offer_id, DATE_FORMAT(offer_proposal_date,'%d-%m-%Y') as offer_proposal_date,rec_guid,rec_guid_answer,offer_email,IF(rec_guid_last_viewed_when IS NULL, '',DATE_FORMAT(rec_guid_last_viewed_when,'%d-%m-%Y')) as rec_guid_last_viewed_when,approval_user_date,offer_company_name,offer_company_manager_name, users.fullname as user,FORMAT(offer_total_amount,2) as gen_total,
 IF(offer_sent_by_mail IS NULL, '', DATE_FORMAT(offer_sent_by_mail,'%d-%m-%Y')) as offer_sent_by_mail,
       CASE offer_type
           WHEN 1 THEN 'New'
           WHEN 2 THEN 'Update'
           WHEN 3 THEN 'Renewal'
           ELSE 'unknown'
       END AS offer_type
       from offers
left join users on users.user_id = offers.offer_seller_id
where is_deleted=0 and company_id=? and (is_paid IS NULL or is_paid=0) order by offer_proposal_date DESC", array($_GET['id']));
///////////////////READ Proposals

?>

<!--jqTE files-->
<link href="css/jquery-te-green.css" rel="stylesheet"></link> 
<script type="text/javascript" src="js/jquery-te-1.4.0.min.js"></script>

<!--upload files-->
<link href="css/jquery.uploadfile.min.css" rel="stylesheet"></link> 
<script src="js/jquery.uploadfile.min.js"></script>
<!--upload files-->

<script>

	//grid formatter
    function approve_format(value, row) {
    	if (value == "null")
    		return "";
    	else 
	        return '<center><i title="' + value + '" class="glyphicon glyphicon-star"></i></center>';
    }
    
var uploadObj;
var upload_form;

    $(function() {
			//setup jQTE			
			$("#mail_body").jqte({css:"jqte_green"});
			
		////////////////////////////////////////
		// MODAL SUBMIT aka SEND MAIL
		$('#formoMAIL').submit(function(e) {
			e.preventDefault();

			loading.appendTo($('#formoMAIL'));

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

		            if (data=="ok")
		            	alert("Email successfully delivered to " + $("#mail_recipient").val());
		            else
		             	alert("Error, please verify mail and retry!");
		             	
    			    $('#modaloMAIL').modal('toggle');
		        },
		        error: function(jqXHR, textStatus, errorThrown) 
		        {
		            alert("Server Error");      
		        }
		    });
		});
		
   		
					 ///////////////////////////////////////////////////////////// FILL proposals grid
					 var jArray_proposals =   <?php echo json_encode($proposals); ?>;

					 var combo_proposals_rows = "";
					 for (var i = 0; i < jArray_proposals.length; i++)
					 {
					 	combo_proposals_rows += "<tr><td></td><td>" + jArray_proposals[i]["offer_id"] + "</td><td>" + jArray_proposals[i]["offer_proposal_date"] + "</td>" +
					 	"<td>" + jArray_proposals[i]["user"] + "</td><td>" + jArray_proposals[i]["gen_total"] + "</td><td>" +
					 	 jArray_proposals[i]["offer_type"] + "</td><td>" + jArray_proposals[i]["offer_sent_by_mail"] + "</td>" + 
					 	 "<td>" + jArray_proposals[i]["rec_guid_last_viewed_when"] + "</td><td>" + jArray_proposals[i]["approval_user_date"] + "</td>" +
					 	 "<td><a href='http://localhost:8080/proposal/index.php?rec_guid=" + jArray_proposals[i]["rec_guid"] + "' target='_blank' class='btn btn-primary btn-xs' style='margin:5px'>View</a>" + 
						 "<a href='javascript:showmailmodal(" + jArray_proposals[i]["offer_id"] + ")' class='btn btn-danger btn-xs' style='margin:5px'>Resend</a></td><td>" + jArray_proposals[i]["rec_guid"] + "</td><td>" + jArray_proposals[i]["rec_guid_answer"] + "</td><td>" + jArray_proposals[i]["offer_company_manager_name"] + "</td><td>" + jArray_proposals[i]["offer_email"] + "</td><td>" + jArray_proposals[i]["offer_company_name"] + "</td></tr>";
					 }

					 $("#lead_proposals_rows").html(combo_proposals_rows);
					 ///////////////////////////////////////////////////////////// FILL proposals grid

					 $("#lead_proposals_tbl").bootstrapTable();

					//edit record ADMIN
					 $('#btn_lead_proposals_payment').on('click', function(e)
					 	{
					 		e.preventDefault();

					 		var row = $('#lead_proposals_tbl').bootstrapTable('getSelections');

					 		if (row.length>0)
					 		{
					 			query_OFFERS_payment_modal(row[0].id, row[0].approved);
					 		}
					 		else
					 			alert("Please select a row");
					 	})					
					 	

					//delete record
					$('#btn_lead_proposals_delete').on('click', function(e)
					{
						var row = $('#lead_proposals_tbl').bootstrapTable('getSelections');

						if (row.length>0)
							{
								if (confirm("Would you like to delete " + row[0].col_descr + " ?"))
									delete_proposal(row[0].id);
							}
						else 
							alert("Please select a row");
					});

					


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
				    
				    
					$('[name=pay_date]').datetimepicker({
				        weekStart: 1,
				        todayBtn:  1,
						autoclose: 1,
						todayHighlight: 1,

						startView: 2,
						minView: 2,
						
						forceParse: 1
				    });
				    
	
	})//jQuery ends here
	
				//delete button - delete record
				function delete_proposal(rec_id){
					loading.appendTo(document.body);
					
				    $.ajax(
				    {
				        url : "tab_leads_details_proposal_admin_delete.php",
				        type: "POST",
				        data : { client_id : <?= $_GET['id'] ?>, offer_id : rec_id },
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
				
				//edit button - WHEN ADMIN - read record
				function query_OFFERS_payment_modal(rec_id, approved){
					
		 			if (approved=="null")
		 			{
						alert("Sorry, for the moment the company is not approved the proposal, you cant proceed futher!");
						return;
		 			}
					 			
					loading.appendTo(document.body);
					
				    $.ajax(
				    {
				        url : "tab_leads_details_proposal_admin_fetch.php",
				        type: "POST",
				        data : { offer_id : rec_id, client_id: <?= $_GET['id'] ?> },
				        success:function(data, textStatus, jqXHR)
				        {
							loading.remove();
							
				        	if (data!='null')
							{
<?php 
if ($active_tab=="leads") {
	
?>
								if (data.offer_type!=1)
								{
									alert("Record doesnt have status '1-New', The value is "+data.offer_type+"\r\n\r\nOperation Aborted!");
									return;	
									
								}

<?php } ?>								
							 	$("[name=offersFORM_updateID]").val(data.offer_id);
								$('[name=is_paid]').bootstrapSwitch('state',parseInt(data.is_paid));

							 	
							 	$('#lblTitle_OFFERS').html("Edit Proposal");
								$('#modalOFFERS').modal('toggle');
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
				
				function validateOFFERform()
				{
					if ($('[name=pay_date]').val().trim().length == 0)
					{
						alert("Please enter date");
						return false;
					}
				}
				
				function showmailmodal(rec_id)
				{
			 		var row = $('#lead_proposals_tbl').bootstrapTable('getSelections');

			 		if (row.length>0)
			 		{
						var mailbody = "Αξιότιμε κύριε " + row[0].company_manager_name + ",<br><br>Σας επισυνάπτω το Link της προσφορά για τις υπηρεσίες μας στο Facebook.<br><br>";
						mailbody+= "<a href='http://localhost:8080/proposal/?j=" + row[0].guid + "' target='_blank'>Proposal</a><br><br>";
						mailbody+= "Ο κωδικός για να δείτε την προσφορά είναι: <b>" + row[0].guid_solution + "</b><br><br>"
						mailbody+="Η προσφορά θα είναι διαθέσιμη online για 10 ημέρες.<br><br>Είμαι στην διάθεσή σας για οποιαδήποτε διευκρίνηση χρειαστείτε.<br><br><br>";
						mailbody+= "<span style='color:rgb(102,102,102)'>	With regards,<br><?=$_SESSION['u'];?><br><?=$_SESSION['u_sign'];?></span><br><font size='1' color='#666666'>	<img src='https://lh4.googleusercontent.com/-qhw8okUHR1U/UzwX6pABRaI/AAAAAAAAAe0/K1JvbXpwufs/w415-h61-no/Signature.png' width='200' height='29' class='CToWUd'><br></font><span style='font-family:verdana,geneva;font-size:x-small'>	16 Beaufort Court,<br>Canary Wharf<br>E14 9XL London, UK</span><br><b style='font-size:x-small'>	<a href='mailto:n.cookies@pipiscrew.com' target='_blank'>		Email us	</a></b><br><font size='1' color='#666666'>	<b>		<a href='http://ww.facebook.com/pipiscrew' target='_blank'>			Go To Facebook Page		</a>	</b></font><br><font size='1' color='#666666'>	<b>		<a href='http://www.pipiscrew.com/' target='_blank'>			Go To Website		</a>	</b></font><br><font color='#666666'>	<b style='font-size:x-small'>		UK:&nbsp;		<a value='+442032390395'>			+44 20 32 39 0395		</a>	</b></font><br><font size='1' color='#666666'>	<b>		Greece:&nbsp;		<a value='+302155309484'>			+30 215 530 9484		</a>	</b></font><br><font color='#808080' face='Verdana, sans-serif'>	<span style='font-size:11px'>		- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -&nbsp;	</span></font><br><span style='color:gray;font-family:Verdana,sans-serif;font-size:8pt'>	This e-mail and any attached files are confidential and may also be legally privileged.</span><br><span style='color:gray;font-family:Verdana,sans-serif;font-size:8pt'>	They are intended solely for the intended addressee.</span><font color='#222222' face='Calibri, sans-serif'>	&nbsp;</font><span style='color:gray;font-family:Verdana,sans-serif;font-size:8pt'>	If you are not the addressee,</span><br><span style='color:gray;font-family:Verdana,sans-serif;font-size:8pt'>	please e-mail it back to the sender and then immediately, permanently delete it.</span><br><span style='color:gray;font-family:Verdana,sans-serif;font-size:8pt'>	Do not read, print, re-transmit, store or act in reliance on it.</span><br><span style='font-size:8pt;font-family:Webdings;color:green'>	P</span>&nbsp;<span style='font-size:8pt;font-family:Verdana,sans-serif;color:gray'>	Please consider the environment before printing this e-mail.</span>";
						
						$("#mail_subject").val("pipiscrew Facebook Agency - " + row[0].offer_company_name + " - Proposal");
						$("#mail_recipient").val(row[0].offer_email);
						$("#mail_body").jqteVal(mailbody);
						$("#mail_offer_rec_id").val(rec_id);
						
						$('#modaloMAIL').modal('toggle');
						console.log(row[0].offer_email);
			 		}
			 		else
			 			alert("Please select a row");
				}
</script>
		
<br/>
		<div class="container">
			<form id='new_prop' action="tab_proposal.php" method="post">
				<button id="btn_lead_proposals_new" class="btn btn-success" type="submit" name="submit">
					New
				</button>

<!--used when form submitted (aka going for new proposal)-->
				<input id='client_id' name='client_id' type='hidden' value="<?php echo $_GET['id']; ?>">

<?php 
if ($_SESSION['level']==9){
	 ?>
				<button id="btn_lead_proposals_payment" type="button" class="btn btn-primary">
					Payment
				</button>
<?php } ?>

				<?php if ($_SESSION['level']==9) { ?>
				<button id="btn_lead_proposals_delete" type="button" class="btn btn-danger">
					Delete
				</button>
				<?php } ?>
			</form>

<br>
		
			<table id="lead_proposals_tbl"
	           data-striped=true data-click-to-select="true" data-single-select="true">
				<thead>
					<tr>
						<th data-field="state" data-checkbox="true" ></th>
						<th data-field="id" >ID</th> 
						<!--data-visible="false"-->
						<th data-field="col_descr" data-sortable="true">Offer Date Start</th>
						<th data-sortable="true">Seller</th>
						<th data-sortable="true">Total Cost &euro;</th>
						<th data-sortable="true">Type</th>
						<th data-sortable="true">Email Sent</th>
						<th data-sortable="true">Seen</th>
						<th data-field="approved" data-sortable="true" data-formatter="approve_format">Approved</th>
						<th data-sortable="false">Actions</th>
						<th data-field="guid" data-visible="false">guid</th>
						<th data-field="guid_solution" data-visible="false">guidsol</th>
						<th data-field="company_manager_name" data-visible="false">company_manager_name</th>
						<th data-field="offer_email" data-visible="false">offer_email</th>
						<th data-field="offer_company_name" data-visible="false">offer_company_name</th>
					</tr>
				</thead>

				<tbody id="lead_proposals_rows"></tbody>
			</table>
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
					New
				</h4>
			</div>
			<div class="modal-body">
				<form id="formOFFERS" role="form" method="post" action="tab_leads_details_proposal_admin_save.php" onsubmit="return validateOFFERform()">

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



<!-- MAIL MODAL [START] -->
<div class="modal fade bs-modal-lg" id="modaloMAIL" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
					&times;
				</button>
				<h4 class="modal-title" id='lblTitle_oMAIL'>Resend Proposal</h4>
			</div>
			<div class="modal-body">
				<p style="background-color: #428bca;color:#fff;padding: 5px;" align=center>email will be send by proposal@watetron.com with <b>reply</b> property to <b><?=$_SESSION['reply_mail'];?></b></p>				
				
				<form id="formoMAIL" role="form" method="post" action="tab_leads_details_proposals_resend_mail.php">

						<div class='form-group'>
							<label>Recipient (multiple addresses separated by semicolon) :</label>
							<input id='mail_recipient' name='mail_recipient' class='form-control' placeholder='Recipient' required autofocus>
						</div>
						
						<div class='form-group'>
							<label>Subject :</label>
							<input id='mail_subject' name='mail_subject' class='form-control' placeholder='Subject' required autofocus>
						</div>

							<input id='mail_body' name='mail_body' data-role="none" class='editor'>
							
							<input id='mail_offer_rec_id' name='mail_offer_rec_id' style="display: none">
													
						<div class="modal-footer">
							<button id="bntCancel_MAIL" type="button" class="btn btn-default" data-dismiss="modal">
								cancel
							</button>
							<button id="bntSend_MAIL" class="btn btn-primary" type="submit" name="submit">
								send
							</button>
						</div>
						
				</form>
			</div><!-- End of Modal body -->
		</div><!-- End of Modal content -->
	</div><!-- End of Modal dialog -->
</div><!-- End of Modal -->
<!-- MAIL MODAL [END] -->