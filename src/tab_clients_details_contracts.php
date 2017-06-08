<?php
session_start();

if (!isset($_SESSION["u"]) || !isset($_SESSION["mail"])) {
	header("Location: login.php");
	exit ;
}

// include DB
require_once ('config.php');

$db             = connect();


$contracts = null;
///////////////////READ Contracts
$contracts = getSet($db,"select offer_id, rec_guid, DATE_FORMAT(is_paid_when,'%d-%m-%Y') as is_paid_when,rec_guid, DATE_FORMAT(offer_date_rec,'%d-%m-%Y %H:%i') as date_created,ifNull(DATE_FORMAT(invoice_sent_when,'%d-%m-%Y'),'') as invoice_sent_when_two,rec_guid_invoice_last_viewed_when, users.fullname as user,FORMAT(offer_total_amount,2) as gen_total,DATE_FORMAT(invoice_detail_when,'%d-%m-%Y %H:%i') as invoice_detail_when,usB.fullname as invoice_sent_user,invoice_sent_when,invoice_detail_id,
       CASE offer_type
           WHEN 1 THEN 'New'
           WHEN 2 THEN 'Update'
           WHEN 3 THEN 'Renewal'
           ELSE 'unknown'
       END AS offer_type,ifNull(DATE_FORMAT(service_starts,'%d-%m-%Y'),'no setted') as Starts, ifNull(DATE_FORMAT(service_ends,'%d-%m-%Y'),'no setted') as Ends,
       (select count(offer_room_detail_id) from offer_room_details where is_deleted=0 and offer_room_details.offer_id=offers.offer_id) as content_exists, 
       (select is_deleted from offer_room_details where offer_room_details.offer_id=offers.offer_id) as content_deleted, 
	   (select domain from offer_page_details where offer_page_details.offer_id=offers.offer_id) as page_domain  
       from offers
left join users on users.user_id = offers.offer_seller_id
left join users as usB on usB.user_id = offers.invoice_sent_user
where company_id=? and is_paid=1 order by offer_date_rec DESC", array($_GET['id']));
///////////////////READ Contracts



?>

<!--upload files
<link href="css/jquery.uploadfile.min.css" rel="stylesheet"></link> 
<script src="js/jquery.uploadfile.min.js"></script>
upload files-->

<script>

var uploadObjMarkPlan;

	//grid formatter
    function invoice_seen(value, row) {
    	console.log(value);
    	if (value == "null")
    		return "";
    	else 
	        return '<center><i title="' + value + '" class="glyphicon glyphicon-star"></i></center>';
    }
    
    function marketplan_mail(){
		window.open("tab_clients_details_contracts_marketplan_mail.php?id=" + $("#contractsFORM_updateID").val());
	}
	
    $(function() {
    	
		//setup jQTE			
		$("#mail2_body").jqte({css:"jqte_green"});
   
    			
		////////////////////////////////////////
		// MODAL SUBMIT aka SEND MAIL
		$('#formomailtwo').submit(function(e) {
			e.preventDefault();

			loading.appendTo($('#formomailtwo'));

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
		            {	alert("Email successfully delivered to " + $("#mail2_recipient").val());
		            	location.reload(true);}
		            else
		             	alert("Error, please verify mail and retry!");
		             	
    			    $('#modalomailtwo').modal('toggle');
		        },
		        error: function(jqXHR, textStatus, errorThrown) 
		        {
		            alert("Server Error");      
		        }
		    });
		});
		
    				$("[name='marketing_plan_completed_contract'],[name='request_access']").bootstrapSwitch();
    	
				    $('[name=next_renewal_contract], [name=service_starts_contract], [name=service_ends_contract]').datetimepicker({
				        weekStart: 1,
				        todayBtn:  1,
						autoclose: 1,
						todayHighlight: 1,
						startView: 2,
						minView: 2,
						forceParse: 1
				    });
    
    				$('[name=marketing_plan_when_contract]').datetimepicker({
				        weekStart: 1,
				        todayBtn:  1,
						autoclose: 1,
						todayHighlight: 1,
						startView: 2,
						forceParse: 1
				    });
				    
					//when user only - UPLOAD PROPOSAL APPROVAL
					uploadObjMarkPlan= $("#markplan_upload").uploadFile({
						url:"tab_clients_details_contracts_upload.php",
						showProgress : true,
						fileName:"myfile",
						autoSubmit:true,
						maxFileCount:1,
						maxFileSize:31457280, //30mb
						dynamicFormData: function()
						{
						    //var data ="XYZ=1&ABCD=2";
						    var data ={ client_id : <?= $_GET['id'] ?>, offer_id: $("#contractsFORM_updateID").val() };
						    return data;        
						},
						onSubmit:function(files) 
						{//callback to be invoked before the file upload.
						
							console.log("refresh")
	           				uploadObjMarkPlan.fileCounter = 0;
	                        uploadObjMarkPlan.selectedFiles = 0;
	                        uploadObjMarkPlan.fCounter = 0; //failed uploads
	                        uploadObjMarkPlan.sCounter = 0; //success uploads
	                        uploadObjMarkPlan.tCounter = 0; //total uploads
					
							if (!files)
								return;
								
							var g = files[0].toLowerCase();
							var ext = (g.substring(g.length-4));
							
							if (ext!=".ppt" && ext!="pptx")
								{
									return false;
								}
								
						    //files : List of files to be uploaded
						    //return flase;   to stop upload

						},
						onSuccess:function(files,data,xhr,pd)
						{
	  						//custom error handling
	                        var info = JSON.parse(data);
	 
	                        if (info["jquery-upload-file-error"]!=null)
	                        {
	                           //show the error thrown by upload PHP
	                            alert(info["jquery-upload-file-error"]);

	                            //remove any status groupboxes from jQ-uploader via class!
	                            $(".ajax-file-upload-statusbar").remove();

	                            //reset jQ-uploader counters!
	                            console.log("refresh")
	                            uploadObjMarkPlan.fileCounter = 0;
	                            uploadObjMarkPlan.selectedFiles = 0;
	                            uploadObjMarkPlan.fCounter = 0; //failed uploads
	                            uploadObjMarkPlan.sCounter = 0; //success uploads
	                            uploadObjMarkPlan.tCounter = 0; //total uploads
	                        }
                        }
						
					});
					
					//marketing plan download
					$('#btn_dn_contract_markplan').on('click', function(e) {
						e.preventDefault();
 						window.location= "tab_clients_details_contracts_marketplan_download.php?client_updateID=<?= $_GET['id'] ?>&offerID=" + $("#contractsFORM_updateID").val();
					});
					
				
					
					//edit record
//					$('#btn_contract_edit').on('click', function(e)
//					{
//						var row = $('#contracts_tbl').bootstrapTable('getSelections');
//
//						if (row.length>0)
//							{
//								query_CONTRACT_modal(row[0].id);
//								console.log(row[0].id);
//							}
//						else 
//							alert("Please select a row");
//					});
					
					 ///////////////////////////////////////////////////////////// FILL Contracts grid
					 var jArray_contracts =   <?php echo json_encode($contracts); ?>;

					 var combo_contracts_rows = "";
					 var which_contest_icon = "";
					 var which_page_icon = "";
					 for (var i = 0; i < jArray_contracts.length; i++)
					 {
					 	///////////////////////////////content column
					 	 which_contest_icon = "";
					 	 
						 if (jArray_contracts[i]["content_deleted"])
						 {
						 	if (jArray_contracts[i]["content_deleted"]==1)
						 		which_contest_icon = "minus-sign";
						 } 
						 
						 if (which_contest_icon.length==0){
							 if (jArray_contracts[i]["content_exists"]==0) {
							 	which_contest_icon = "remove";
							 } else {
							 	which_contest_icon = "ok";
							 }						 	
						 }
						 ///////////////////////////////content column

						///////////////////////////////page column
						 if (jArray_contracts[i]["page_domain"])
						 	which_page_icon = "ok";
						 else 
						 	which_page_icon = "remove";
						 ///////////////////////////////page column
					 	
//					 	if (jArray_contracts[i]["coddntent_deleted"]==0)
//					 		which_contest_icon
					 		
					 	combo_contracts_rows += "<tr><td>" + jArray_contracts[i]["offer_id"] + "</td><td>" + jArray_contracts[i]["date_created"] + "</td>" + 
					 	"<td>" + jArray_contracts[i]["user"] + "</td><td>" + jArray_contracts[i]["is_paid_when"] + "</td><td>" + jArray_contracts[i]["gen_total"] + "</td><td>" + jArray_contracts[i]["offer_type"] + "</td>" +
					 	"<td>" + jArray_contracts[i]["Starts"] + "</td><td>" + jArray_contracts[i]["Ends"] + "</td><td><center><span  class='glyphicon glyphicon-" + which_contest_icon + "'></span></center></td><td><center><span  class='glyphicon glyphicon-" + which_page_icon + "'></span></center></td><td>" + jArray_contracts[i]["invoice_sent_when_two"] + "</td><td>" + jArray_contracts[i]["rec_guid_invoice_last_viewed_when"] + "</td>";

						
						//03/12/2014
						//invoice_detail_when + invoice_detail_user + invoice_detail_id - 
						//gets updated by #tab_clients_details_contract_invoice.php# aka click 'Invoice Details'
					 	if ( jArray_contracts[i]["invoice_detail_when"]==null || jArray_contracts[i]["Starts"]=="no setted" || jArray_contracts[i]["Ends"]=="no setted" || jArray_contracts[i]["invoice_detail_id"]==null){
//							if (jArray_contracts[i]["Starts"]=="no setted")
//							{
//								//serv start date is null 
//								//show only edit button
//								combo_contracts_rows +=	"<td><a onclick='query_CONTRACT_modal(" + jArray_contracts[i]["offer_id"] + ");' class='btn btn-primary btn-xs'>Edit</a>";
//							}
//							else {
//								//show download +
//								//show edit button
					 			combo_contracts_rows +=	"<td><a onclick='invoice_details_choose(" + jArray_contracts[i]["offer_id"] + ");' class='btn btn-success btn-xs'>Invoice Details</a>&nbsp;<a onclick='query_CONTRACT_modal(" + jArray_contracts[i]["offer_id"] + ");' class='btn btn-primary btn-xs'>1.Marketing</a>";
//							}

					 	}
					 	//combo_contracts_rows +=	"<td><a id='invoice_download' target='_blank' href='tab_clients_details_contract_invoice.php?id=" + jArray_contracts[i]["offer_id"] + "' class='btn btn-danger btn-xs'>Download</a>";
					 	else //here has also 
					 	{//reactivate
					 		combo_contracts_rows +=	"<td>";
							
							combo_contracts_rows += "<a onclick='query_CONTRACT_modal(" + jArray_contracts[i]["offer_id"] + ");' class='btn btn-primary btn-xs'>1.Marketing</a>&nbsp;";
							
							if ( jArray_contracts[i]["invoice_sent_when"]==null){
								//when never sent, show the [sent+view] button
								combo_contracts_rows += "<a onclick=\"invoice_view('" + jArray_contracts[i]["rec_guid"] + "','" + jArray_contracts[i]["invoice_detail_id"] + "');\" class='btn btn-primary btn-xs'>View</a>&nbsp;<a onclick='invoice_send(" + jArray_contracts[i]["offer_id"]  + "," + jArray_contracts[i]["invoice_detail_id"] + ");' class='btn btn-danger btn-xs'>send</a>&nbsp;";
								combo_contracts_rows += "<a href='http://localhost:8080/proposal/index.php?rec_guid=" + jArray_contracts[i]["rec_guid"] + "' target='_blank' class='btn btn-primary btn-xs'>View Proposal</a>&nbsp;";
							}
							else {
								//only view
								combo_contracts_rows += "<a onclick=\"invoice_view('" + jArray_contracts[i]["rec_guid"] + "','" + jArray_contracts[i]["invoice_detail_id"] + "');\" class='btn btn-primary btn-xs'>View</a>&nbsp;";
								combo_contracts_rows += "<a href='http://localhost:8080/proposal/index.php?rec_guid=" + jArray_contracts[i]["rec_guid"] + "' target='_blank' class='btn btn-primary btn-xs'>View Proposal</a>&nbsp;";
								
						 		<?php if ($_SESSION['level']==9)
						 		{
							 		?>
						 			combo_contracts_rows += "<a href='tab_clients_details_contract_invoice_reactivate.php?id=" + jArray_contracts[i]["offer_id"] + "' class='btn btn-success btn-xs'>Resend</a>&nbsp;&nbsp;";
						 			<?php
						 		} ?>
						 		
					 			combo_contracts_rows +=	"Sent " + jArray_contracts[i]["invoice_sent_when"] + " by " + jArray_contracts[i]["invoice_sent_user"];
							}
					 	}
//beta
combo_contracts_rows +=	"&nbsp;<a onclick='edit_content(" + jArray_contracts[i]["offer_id"] + ");' class='btn btn-primary btn-xs'>2.Content</a>";
combo_contracts_rows +=	"&nbsp;<a onclick='edit_page(" + jArray_contracts[i]["offer_id"] + ");' class='btn btn-primary btn-xs'>3.Page</a>";
combo_contracts_rows +=	"&nbsp;<a onclick='edit_advertise(" + jArray_contracts[i]["offer_id"] + ");' class='btn btn-primary btn-xs'>4.Advertise</a>&nbsp;";
					 	combo_contracts_rows +="</td></tr>";
					 }

					 $("#contracts_rows").html(combo_contracts_rows);
					 ///////////////////////////////////////////////////////////// FILL Contracts grid
					
					 //convert2magic!
					 $("#contracts_tbl").bootstrapTable();

					//>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
					//>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> INVOICE DETAILS <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<
					//>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>

				    ////////////////////////////////////////
				    // MODAL FUNCTIONALITIES [START]-invoice details
				    //when modal closed, hide the warning messages + reset
				    $('#modalCHOOSEINVOICE').on('hidden.bs.modal', function() {
				        //when close - clear elements
				        $('#formCHOOSEINVOICE').trigger("reset");

				        //destroy bootstrap-table
				        $("#CHOOSEINVOICE_tbl").bootstrapTable('destroy');
				    });
				 
				    //functionality when the modal already shown and its long, when reloaded scroll to top
				    $('#modalCHOOSEINVOICE').on('shown.bs.modal', function() {
				        $(this).animate({
				            scrollTop : 0
				        }, 'slow');
				    });
				    // MODAL FUNCTIONALITIES [END]-invoice details
				    ////////////////////////////////////////
				    
					//when row clicked by bootstrap-table (card view)-there is no other way(?)
					$('#CHOOSEINVOICE_tbl').on('click-row.bs.table', function (e, row, $element)
					{
//						console.log(row.client_invoice_detail_id);
//						if (!confirm("Please confirm the invoice details will be :\r\n"+ row.company_name + "\r\n" + row.vat_no + "\r\n" + row.tax_office_id))
//							return;
						if (!row){
							alert("Please choose valid row!");
							return;
						}
							
							
						loading.appendTo(document.body);

	 					//close modal
	 					$('#modalCHOOSEINVOICE').modal('toggle');

						//set selected to form input element
						$("#CHOOSEINVOICE_invoicedetailID").val(row.client_invoice_detail_id);
		
						//////////////////////////////////////////////////////
						// POST TO PHP - SERIALIZE FORM
						//////////////////////////////////////////////////////
						var frm = $("#formCHOOSEINVOICE");
					    var postData = frm.serializeArray();
					    var formURL = frm.attr("action");
						
					    $.ajax(
					    {
					        url : formURL,
					        type: "POST",
					        data : postData,
					        success:function(data, textStatus, jqXHR)
					        {

					        	
					            if (data=="00000")
								{
									//refresh
									setTimeout(function(){
										window.location="tab_clients_details.php?showcontracts=1&id=<?= $_GET['id'] ?>";
									}, 5000);	
								}
					            else{
						        	loading.remove();
						        	alert("ERROR - Not updated");
								}
					                
					        },
					        error: function(jqXHR, textStatus, errorThrown)
					        {
					        	loading.remove();
					            alert("ERROR - connection error");
					        }
					    });
					    
//when docx
//						//submit native the form!
//						document.formCHOOSEINVOICE.submit();
//						
//						//go back after 5sec
//						setTimeout(function(){
//							window.location="tab_clients_details.php?showcontracts=1&id=<?= $_GET['id'] ?>";
//						}, 5000);						

					});
					

//					 $("#lead_proposals_tbl").bootstrapTable();


					

				    ////////////////////////////////////////
				    // MODAL FUNCTIONALITIES [START]
				    //when modal closed, hide the warning messages + reset
				    $('#modalcontracts').on('hidden.bs.modal', function() {
				        //when close - clear elements
				        $('#formcontracts').trigger("reset");
				        
				        
						$(".ajax-upload-dragdrop").show();

						
						//marketing plan
						$("#btn_dn_contract_markplan").hide();
						

						//remove any status groupboxes from jQ-uploader via class!
						$(".ajax-file-upload-statusbar").remove();
				    });
				 
				    //functionality when the modal already shown and its long, when reloaded scroll to top
				    $('#modalcontracts').on('shown.bs.modal', function() {
				    	upload_form = "modalcontracts";
				        $(this).animate({
				            scrollTop : 0
				        }, 'slow');
				    });
				    // MODAL FUNCTIONALITIES [END]
				    ////////////////////////////////////////
				    
				    
					////////////////////////////////////////
					// MODAL SUBMIT aka save & update button
					$('#formcontracts').submit(function(e) {
					    e.preventDefault();
					 
					    ////////////////////////// validation
//					    var form = $(this);
//					    form.validate();
//					 
//					    if (!form.valid())
//					        return;
					    ////////////////////////// validation
					 
					    var postData = $(this).serializeArray();
					    var formURL = $(this).attr("action");
					 
					    //close modal
					    $('#modalcontracts').modal('toggle');
					 
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
									
									//refresh
//									$('#offers_tbl').bootstrapTable('refresh');
					            else
					                alert("ERROR");
					        },
					        error: function(jqXHR, textStatus, errorThrown)
					        {
					            alert("ERROR - connection error");
					        }
					    });
					});
					
	
	})//jQuery ends here
	


					 //when download button clicked from grid - refresh destinations (invoice details) on grid is in modal!
					 //dynamic handler for grid buttons
					 function invoice_details_choose(offer_id)
					 {
						//store to modal hidden input, the offerID selected by primary grid!
						$("#CHOOSEINVOICE_offerID").val(offer_id);
						
					 		$.ajax(
					 			{
					 				url : 'tab_clients_details_contract_get_invoices.php',
					 				dataType : 'json',
					 				type : 'POST',
					 				data :	{ "client_id" : <?= $_GET['id'] ?>	}, //read client invoice details (aka branches)
					 				success : function(data)
					 				{
					 					//when response
					 					var r = data.recs;
					 					var tbl ="";
					 					
					 					if (r==undefined)
										{
											alert("error : no record");
											return;
										}					 						
					 					
					 					//construct table rows
					 					for (var i = 0; i < r.length; i++)
					 					{
					 						tbl +=  "<tr><td>" + r[i]["client_invoice_detail_id"] + "</td><td>" + r[i]["company_name"] + "</td>" +
					 						"<td>" + r[i]["occupation"] + "</td><td>" + r[i]["city"] + "</td><td>" + r[i]["country_id"] + "</td><td>" + r[i]["vat_no"] + "</td><td>" + r[i]["tax_office_id"] + "</td></tr>";
					 					}

					 					//set rows to table 
					 					$("#CHOOSEINVOICE_rows").html(tbl);
					 					
					 					//convert2magic!
					 					$("#CHOOSEINVOICE_tbl").bootstrapTable();
					 					
					 					//show modal
					 					$('#modalCHOOSEINVOICE').modal('toggle');
					 				},
					 				error : function(e)
					 				{
					 					alert("error");
					 				}
					 			});

					 		//location.reload(true);
					 	}

				function invoice_send (offer_id,detail_id) {
						loading.appendTo(document.body);
						
						var formURL = $("#formomailtwo").attr("action");
					    $.ajax(
					    {
					        url : "tab_clients_details_contracts_fetch_offer_details.php",
					        type: "POST",
					        dataType : 'json', 
					        data : {offer_id : offer_id},
					        success:function(data, textStatus, jqXHR)
					        {
					        	loading.remove();
					        	
					            if (data)
								{
									var newDay = new Date();
									var t = new Date(newDay.toUTCString());
									time4scan = Math.round(t / 1000);
					
									//13/01 - fix
									//before data.offer_company_name
									var mailbody = "Αξιότιμε κ. " + data.offer_company_manager_name + ",<br><br>Σας επισυνάπτω το Link για το τιμολόγιο των υπηρεσιών μας στο Facebook.<br><br>";

									mailbody+= "<a href='http://localhost:8080/proposal/?j=" + data.rec_guid + "&invoice=" + time4scan.toString() + "' target='_blank'>Invoice</a><br><br>";
									mailbody+= "Ο κωδικός για να δείτε το τιμολόγιο είναι: <b>" + data.rec_guid_answer_invoice + "</b><br><br>"
									mailbody+="Είμαι στην διάθεσή σας για οποιαδήποτε διευκρίνηση χρειαστείτε.<br><br><br>";
									mailbody+= "<span style='color:rgb(102,102,102)'>	With regards,<br><?=$_SESSION['u'];?><br><?=$_SESSION['u_sign'];?></span><br><font size='1' color='#666666'>	<img src='https://lh4.googleusercontent.com/-qhw8okUHR1U/UzwX6pABRaI/AAAAAAAAAe0/K1JvbXpwufs/w415-h61-no/Signature.png' width='200' height='29' class='CToWUd'><br></font><span style='font-family:verdana,geneva;font-size:x-small'>	16 Beaufort Court,<br>Canary Wharf<br>E14 9XL London, UK</span><br><b style='font-size:x-small'>	<a href='mailto:pipiscrew@pipiscrew.com' target='_blank'>		Email us	</a></b><br><font size='1' color='#666666'>	<b>		<a href='http://ww.facebook.com/pipiscrew' target='_blank'>			Go To Facebook Page		</a>	</b></font><br><font size='1' color='#666666'>	<b>		<a href='http://www.pipiscrew.com/' target='_blank'>			Go To Website		</a>	</b></font><br><font color='#666666'>	<b style='font-size:x-small'>		UK:&nbsp;		<a value='+442032390395'>			+44 20 32 39 0395		</a>	</b></font><br><font size='1' color='#666666'>	<b>		Greece:&nbsp;		<a value='+302155309484'>			+30 215 530 9484		</a>	</b></font><br><font color='#808080' face='Verdana, sans-serif'>	<span style='font-size:11px'>		- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -&nbsp;	</span></font><br><span style='color:gray;font-family:Verdana,sans-serif;font-size:8pt'>	This e-mail and any attached files are confidential and may also be legally privileged.</span><br><span style='color:gray;font-family:Verdana,sans-serif;font-size:8pt'>	They are intended solely for the intended addressee.</span><font color='#222222' face='Calibri, sans-serif'>	&nbsp;</font><span style='color:gray;font-family:Verdana,sans-serif;font-size:8pt'>	If you are not the addressee,</span><br><span style='color:gray;font-family:Verdana,sans-serif;font-size:8pt'>	please e-mail it back to the sender and then immediately, permanently delete it.</span><br><span style='color:gray;font-family:Verdana,sans-serif;font-size:8pt'>	Do not read, print, re-transmit, store or act in reliance on it.</span><br><span style='font-size:8pt;font-family:Webdings;color:green'>	P</span>&nbsp;<span style='font-size:8pt;font-family:Verdana,sans-serif;color:gray'>	Please consider the environment before printing this e-mail.</span>";
									
									$("#mail2_subject").val("pipiscrew Facebook Agency - " + data.offer_company_name + " - Invoice");
									$("#mail2_recipient").val(data.offer_email);
									$("#mail2_body").jqteVal(mailbody);
									$("#mail2_offer_rec_id").val(data.offer_id);
						
									$("#mail2_body").jqteVal(mailbody);
									$('#modalomailtwo').modal('toggle');	
								}
					            else{
						        	loading.remove();
						        	alert("ERROR - Not updated");
								}
					                
					        },
					        error: function(jqXHR, textStatus, errorThrown)
					        {
					        	loading.remove();
					            alert("ERROR - connection error");
					        }
					    });
					    
					
				}
				
				function invoice_view(offer_guid,detail_id)
				{
					var x = "http://localhost:8080/proposal/index.php?rec_guid=" + offer_guid + "&invoice_detail_id="+detail_id;
					window.open(x);
				}

				//edit button - read record
				function query_CONTRACT_modal(rec_id){
					loading.appendTo(document.body);
					
				    $.ajax(
				    {
				        url : "tab_clients_details_contracts_fetch.php",
				        type: "POST",
				        data : {  offer_id : rec_id , client_id: <?= $_GET['id'] ?> },
				        success:function(dataO, textStatus, jqXHR)
				        {
							loading.remove();
							
//							console.log(dataO);
//							return;
				        	if (dataO!='null')
							{
								if (dataO.plan==null)
								{
									$("#btn_dn_contract_markplan").hide();
									$(".ajax-upload-dragdrop").show();
								}
								else
								{
									$("#btn_dn_contract_markplan").show();
									$(".ajax-upload-dragdrop").hide();
								}

								if (dataO.approval==null)
								{
									//validation left by when proposalapproval was DOCX
									alert("WARNING Company didnt accept the proposal!")
								}
							
								
								
									
								data = dataO.record;
							 	$("[name=contractsFORM_updateID]").val(data.offer_id);
								$('[name=next_renewal_contract]').val(data.next_renewal);
								$('[name=service_starts_contract]').val(data.service_starts);
								$('[name=service_ends_contract]').val(data.service_ends);
								$('[name=marketing_plan_when_contract]').val(data.marketing_plan_when);
								$('[name=marketing_plan_location_contract]').val(data.marketing_plan_location);
								$('[name=marketing_plan_completed_contract]').bootstrapSwitch('state',parseInt(data.marketing_plan_completed));
								$('[name=request_access]').bootstrapSwitch('state',parseInt(data.request_access));
								$('[name=marketing_plan_comment_PKou1HBe]').val(data.marketing_plan_comment);
							 	
							 	$('#lblTitle_contracts').html("Edit Contract");
								$('#modalcontracts').modal('toggle');
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

	

<br/>
	<!--<div class="container">-->
	
<!--		<button id="btn_contract_edit" class="btn btn-primary" type="button" name="btn_contract_edit">
			Edit
		</button>	
-->		
<br/><br/>
			<table id="contracts_tbl"
	           data-striped=true > <!--data-click-to-select="true" data-single-select="true"-->
				<thead>
					<tr>
						<!--<th data-field="state" data-checkbox="true" ></th>-->
						<th data-field="id" >ID</th> 
						<!--data-visible="false"-->
						<th data-field="col_descr" data-sortable="true">Created</th>
						<th data-sortable="true">Seller</th>
						<th data-sortable="true">Paid Date</th>
						<th data-sortable="true">Total Cost &euro;</th>
						<th data-sortable="true">Type</th>
						<th data-sortable="true">Service Starts</th>
						<th data-sortable="true">Service Ends</th>
						<th data-sortable="true">Content</th>
						<th data-sortable="true">Page</th>
						<th data-sortable="true">Email Sent</th>
						<th data-sortable="true" data-formatter="invoice_seen">Seen</th>
						<th data-sortable="false">Invoice</th>
						
					</tr>
				</thead>

				<tbody id="contracts_rows"></tbody>
			</table>
	<!--</div>-->
			
<!-- NEW CLIENT_CALLS MODAL [START] -->
<div class="modal fade" id="modalcontracts" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
					&times;
				</button>
				<h4 class="modal-title" id='lblTitle_contracts'>New</h4>
			</div>
			<div class="modal-body">
				<form id="formcontracts" role="form" method="post" action="tab_clients_details_contracts_save.php">

           


				
<div class="row">

<div class='col-md-6'>
			<div class='form-group'>
					<label>Next Renewal :</label><br>
					<input type="text" name="next_renewal_contract" class="form-control" data-date-format="dd-mm-yyyy" readonly class="form_datetime">
			</div>
			</div>
			</div>

<div class="row">
			<div class='col-md-6'>
			<div class='form-group'>
					<label>Service Starts :</label><br>
					<input type="text" name="service_starts_contract" class="form-control" data-date-format="dd-mm-yyyy" readonly class="form_datetime">
			</div></div>
			


			<div class='col-md-6'>
			<div class='form-group'>
					<label>Service Ends :</label><br>
					<input type="text" name="service_ends_contract" class="form-control" data-date-format="dd-mm-yyyy" readonly class="form_datetime">
			</div>
			</div>
</div>

<div class="row">
			<div class='col-md-6'>
			<div class='form-group'>
					<label style="display: inline-block;padding-right: 10px" >Marketing Plan Date :</label><a onclick='marketplan_mail();' class='btn btn-warning btn-xs'>mail client</a> <br>
					<input type="text" name="marketing_plan_when_contract" class="form-control" data-date-format="dd-mm-yyyy hh:ii" readonly class="form_datetime">
			</div>
			</div>

			
				<div class='col-md-6'>
				<div class='form-group'>
					<label>Marketing Plan Location :</label>
					<input name='marketing_plan_location_contract' class='form-control' placeholder='marketing_plan_location'>
				</div>
				</div>

</div>

			<div class='form-group'>
				<label>Marketing Plan Attachment :</label>
				<button id="btn_dn_contract_markplan" type="submit" class="btn btn-primary btn-sm">
					Download
				</button>
				<div id="markplan_upload">Upload</div>
			</div>
			
			
				<div class='form-group'>
					<label>Marketing Plan Meeting Completed  <span class="glyphicon glyphicon-signal"></span></label><br>
					<input type="checkbox" data-on-text="Yes" data-off-text="No" name='marketing_plan_completed_contract'>
				</div>

				<div class='form-group'>
					<label>Request Access :</label><br>
					<input type="checkbox" data-on-text="Yes" data-off-text="No" name='request_access'>
				</div>


				<div class='form-group'>
					<label>Comment :</label>
					<textarea  style="resize: none;" rows="3" name='marketing_plan_comment_PKou1HBe' class='form-control'></textarea>
				</div>
				
						<!-- <input name="contractsFORM_FKid" id="contracts_FKid" class="form-control" style="display:none;"> -->
						<input name="contractsFORM_updateID" id="contractsFORM_updateID" class="form-control" style="display:none;">

						<div class="modal-footer">
							<button id="bntCancel_contracts" type="button" class="btn btn-default" data-dismiss="modal">
								cancel
							</button>
							<button id="bntSave_contracts" class="btn btn-primary" type="submit" name="submit">
								save
							</button>
						</div>
                </form>
            </div><!-- End of Modal body -->
        </div><!-- End of Modal content -->
    </div><!-- End of Modal dialog -->
</div><!-- End of Modal -->
<!-- NEW CLIENT_CALLS MODAL [END] -->



<!-- NEW CHOOSE INVOICE MODAL [START] -->
<div class="modal fade" id="modalCHOOSEINVOICE" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
					&times;
				</button>
				<h4 class="modal-title" id='lblTitle_contracts'>Please Choose Client Invoice Details</h4>
			</div>
			<div class="modal-body">
			           <!--data-striped=true-->				
					<table id="CHOOSEINVOICE_tbl"
			           data-card-view="true" 
			           data-height="500"
			           >

							<thead>
								<tr>
									<th data-field="client_invoice_detail_id" data-visible="false">
										id
									</th>
									<th data-field="company_name" data-sortable="true">
										Company Name
									</th>
									
									<th data-field="occupation" data-sortable="true">
										Occupation
									</th>
									
									<th data-field="city" data-sortable="true">
										City
									</th>
									
									<th data-field="country_id" data-sortable="true">
										Country
									</th>
									
									<th data-field="vat_no" data-sortable="true">
										VAT
									</th>
									
									<th data-field="tax_office_id" data-sortable="true">
										Tax Office
									</th>
									
								</tr>
							</thead>
							 <tbody id="CHOOSEINVOICE_rows"></tbody>
						</table	>
           
				<form id="formCHOOSEINVOICE" name="formCHOOSEINVOICE" role="form" method="post" action="tab_clients_details_contract_invoice_details_save.php">
						<!-- <input name="contractsFORM_FKid" id="contracts_FKid" class="form-control" style="display:none;"> -->
						<input name="CHOOSEINVOICE_offerID" id="CHOOSEINVOICE_offerID" class="form-control" style="display:none;">
						<input name="CHOOSEINVOICE_invoicedetailID" id="CHOOSEINVOICE_invoicedetailID" class="form-control" style="display:none;">

						<div class="modal-footer">
							<button id="bntCancel_CHOOSEINVOICE" type="button" class="btn btn-primary" data-dismiss="modal">
								cancel
							</button>
						</div>
                </form>
            </div><!-- End of Modal body -->
        </div><!-- End of Modal content -->
    </div><!-- End of Modal dialog -->
</div><!-- End of Modal -->
<!-- NEW CHOOSE INVOICE MODAL [START] -->

<!-- mail2 MODAL [START] -->
<div class="modal fade" id="modalomailtwo" tabindex="-1" role="dialog" aria-labelledby="myModalLabeld" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
					&times;
				</button>
				<h4 class="modal-title" id='lblTitle_mailtwo'>Send Invoice</h4>
			</div>
			<div class="modal-body">
				<p style="background-color: #428bca;color:#fff;padding: 5px;" align=center>email will be send by proposal@watetron.com with <b>reply</b> property to <b><?=$_SESSION['reply_mail'];?></b></p>				
				
				<form id="formomailtwo" role="form" method="post" action="tab_clients_details_contracts_send_invoice_mail.php">

						<div class='form-group'>
							<label>Recipient (multiple addresses separated by semicolon) :</label>
							<input id='mail2_recipient' name='mail2_recipient' class='form-control' placeholder='Recipient' required autofocus>
						</div>
						
						<div class='form-group'>
							<label>Subject :</label>
							<input id='mail2_subject' name='mail2_subject' class='form-control' placeholder='Subject' required autofocus>
						</div>

							<input id='mail2_body' name='mail2_body' data-role="none" class='editor'>
							
							<input id='mail2_offer_rec_id' name='mail2_offer_rec_id' style="display: none">
													
						<div class="modal-footer">
							<button id="bntCancel_mailtwo" type="button" class="btn btn-default" data-dismiss="modal">
								cancel
							</button>
							<button id="bntSend_mailtwo" class="btn btn-primary" type="submit" name="submit">
								send
							</button>
						</div>
					</form>
            </div><!-- End of Modal body -->
        </div><!-- End of Modal content -->
    </div><!-- End of Modal dialog -->
</div><!-- End of Modal -->

<?php include("tab_clients_details_contracts_content_modal.php") ?>
<?php include("tab_clients_details_contracts_page_modal.php") ?>
<?php include("tab_clients_details_contracts_advertise_modal.php") ?>