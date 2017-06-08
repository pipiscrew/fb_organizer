<?php
//session_start();
//
//if (!isset($_SESSION["u"])) {
//	header("Location: index.html");
//	exit ;
//}

$active_tab="clients";

require_once ('template_top.php');

// include DB
require_once ('config.php');

$db       = connect();


$client_sectors_rows=null;
///////////////////READ client_sectors
	$find_sql = "SELECT * FROM `client_sectors` order by client_sector_name";
	$stmt      = $db->prepare($find_sql);
	
	$stmt->execute();
	$client_sectors_rows = $stmt->fetchAll();
///////////////////READ client_sectors


//$client_sector_subs_rows=null;
/////////////////////READ client_sector_subs
//	$find_sql = "SELECT * FROM `client_sector_subs` order by client_sector_sub_name";
//	$stmt      = $db->prepare($find_sql);
//	
//	$stmt->execute();
//	$client_sector_subs_rows = $stmt->fetchAll();
///////////////////READ client_sector_subs


$client_sources_rows=null;
///////////////////READ client_sources
	$find_sql = "SELECT * FROM `client_sources` order by client_source_name";
	$stmt      = $db->prepare($find_sql);
	
	$stmt->execute();
	$client_sources_rows = $stmt->fetchAll();
///////////////////READ client_sources


$client_ratings_rows=null;
///////////////////READ client_ratings
	$find_sql = "SELECT * FROM `client_ratings` order by client_rating_name";
	$stmt      = $db->prepare($find_sql);
	
	$stmt->execute();
	$client_ratings_rows = $stmt->fetchAll();
///////////////////READ client_ratings


$countries_rows=null;
///////////////////READ countries
	$find_sql = "SELECT * FROM `countries` order by country_name";
	$stmt      = $db->prepare($find_sql);
	
	$stmt->execute();
	$countries_rows = $stmt->fetchAll();
///////////////////READ countries





$row=null;
///////////////////READ SPEFIC RECORD
if (isset($_GET["id"])) {
	$find_sql = "SELECT client_id
	,client_code
	,is_lead
	,client_name
	,client_sector_id
	,client_sector_sub_id
	,client_source_id
	,client_rating_id
	,profile_sent
	,country_id
	,manager_name
	,address
	,telephone
	,mobile
	,email
	,facebook_page
	,website
	,comment
	,DATE_FORMAT(owned_date, '%d-%m-%Y %H:%i') AS owned_date
	,userA.fullname as owner
	,DATE_FORMAT(modified_date, '%d-%m-%Y %H:%i') AS modified_date
	,userB.fullname as modified_by
	,has_facebook_page_before
	,room_exists
	,city,area
	,clients.owner as ownerB 
 FROM `clients`
 left join users as userA on userA.user_id = clients.owner
 left join users as userB on userB.user_id = clients.modified_by where client_id = :id";

	$stmt      = $db->prepare($find_sql);
	$stmt->bindValue(':id', $_GET["id"]);
	
	$stmt->execute();
	$row = $stmt->fetchAll();
	

if ($_SESSION["level"] != 9 && $row[0]["ownerB"]!=$_SESSION["id"])
 {
 	if ($row[0]==null)
 		die("record doesnt exist! ask administrator why!");
 	else 
 		die("you cant administrate this record! ask administrator why!");
	
 }
 
 if ($row!=null && $row[0]["is_lead"] != 0 )
 	die("Company is not client");
 else if (isset($_GET["id"]) && $row==null)
 	die("Company is NULL!");
}
///////////////////READ SPEFIC RECORD

?>


		
<script type="text/javascript">
		
			
			
//user by tab_client_appointments.php
var is_lead = 0;

        $(function() {

//GET NEW CLIENT CODE IF IS ON 'CREATE NEW'
 <?php if (!isset($_GET["id"])){ ?>
 	//is the same for clients, the numbering is unique
// 				$.ajax({
//					url : 'tab_leads_details_save_new_codevalidation.php',
//					dataType : 'json',
//					type : 'POST',
//					data : {
//						is_lead :"1",
//					    client_code: "1000"
//					},
//		            success : function(data) {
//							$("#client_code").val(data);
//					}
//				});
<?php } elseif (isset($_GET["showcalls"])) {?>	
			$('a[href=#calls]').tab('show');
<?php } elseif (isset($_GET["showcontracts"])) {?>	
			$('a[href=#contracts]').tab('show');	
<?php } elseif (isset($_GET["showproposals"])) {?>	
			$('a[href=#proposals]').tab('show');	
<?php } ?>		



//facebook pages
<?php if (isset($_GET["id"])){ ?>
	fill_fb_pages();
<?php } else { ?>
	$("#facebook_foreign").hide();
<?php } ?>

	///////////////////////////////////////////////////////////// FILL client_sectors
	var jArray_client_sectors =   <?php echo json_encode($client_sectors_rows); ?>;

	var combo_client_sectors_rows = "<option value='0'></option>";
	for (var i = 0; i < jArray_client_sectors.length; i++)
	{
		combo_client_sectors_rows += "<option value='" + jArray_client_sectors[i]["client_sector_id"] + "'>" + jArray_client_sectors[i]["client_sector_name"] + "</option>";
	}

	$("[name=client_sector_id],#oSECTOR_description").html(combo_client_sectors_rows);
	$("[name=client_sector_id]").change(); //select row 0 - no conflict on POST validation @ PHP
	///////////////////////////////////////////////////////////// FILL client_sectors


	///////////////////////////////////////////////////////////// FILL client_sources
	var jArray_client_sources =   <?php echo json_encode($client_sources_rows); ?>;

	var combo_client_sources_rows = "<option value='0'></option>";
	for (var i = 0; i < jArray_client_sources.length; i++)
	{
		combo_client_sources_rows += "<option value='" + jArray_client_sources[i]["client_source_id"] + "'>" + jArray_client_sources[i]["client_source_name"] + "</option>";
	}

	$("[name=client_source_id]").html(combo_client_sources_rows);
	$("[name=client_source_id]").change(); //select row 0 - no conflict on POST validation @ PHP
	///////////////////////////////////////////////////////////// FILL client_sources


	///////////////////////////////////////////////////////////// FILL client_ratings
	var jArray_client_ratings =   <?php echo json_encode($client_ratings_rows); ?>;

	var combo_client_ratings_rows = "<option value='0'></option>";
	for (var i = 0; i < jArray_client_ratings.length; i++)
	{
		combo_client_ratings_rows += "<option value='" + jArray_client_ratings[i]["client_rating_id"] + "'>" + jArray_client_ratings[i]["client_rating_name"] + "</option>";
	}

	$("[name=client_rating_id]").html(combo_client_ratings_rows);
	$("[name=client_rating_id]").change(); //select row 0 - no conflict on POST validation @ PHP
	///////////////////////////////////////////////////////////// FILL client_ratings


	///////////////////////////////////////////////////////////// FILL countries
	var jArray_countries =   <?php echo json_encode($countries_rows); ?>;

	var combo_countries_rows = "<option value='0'></option>";
	for (var i = 0; i < jArray_countries.length; i++)
	{
		combo_countries_rows += "<option value='" + jArray_countries[i]["country_id"] + "'>" + jArray_countries[i]["country_name"] + "</option>";
	}

	//fill also #invoice details# modal!
	$("[name=country_id],[name=country_id_INVOICE]").html(combo_countries_rows);
	$("[name=country_id],[name=country_id_INVOICE]").change(); //select row 0 - no conflict on POST validation @ PHP
	///////////////////////////////////////////////////////////// FILL countries



//	$("[name='is_lead']").bootstrapSwitch();
//	$("[name='profile_sent']").bootstrapSwitch();
	$("[name='has_facebook_page_before']").bootstrapSwitch();
//	$("[name='room_exists']").bootstrapSwitch();
	$("[name=chk_answered],[name=chk_company_presented],[name=chk_company_profile],[name=chk_client_proposal],[name=chk_appointment_booked]").bootstrapSwitch();



//client_call_datetime + client_call_next_call ==> is on CALLS modal
    $('[name=client_call_datetime],[name=client_call_next_call]').datetimepicker({
        weekStart: 1,
        todayBtn:  1,
		autoclose: 1,
		todayHighlight: 1,
		startView: 2,
		forceParse: 1
    });
    

	///////////////////////////////////////////////////////////// EDIT RECORD
	var jArray = <?php echo json_encode($row); ?>;
	
	$("#room_exists").val("0");
	
	if (jArray) {
		//WARNING THE FIELD NAMES IS CASE SENSITIVE ON ARRAY
		$('[name=clientsFORM_updateID]').val(jArray[0]["client_id"]);
		$('[name=client_code]').val(jArray[0]["client_code"]);
		$('[name=client_name]').val(jArray[0]["client_name"]);
		$('[name=city_client]').val(jArray[0]["city"]);
		$('[name=area_client]').val(jArray[0]["area"]);	
		$('[name=client_sector_id]').val(jArray[0]["client_sector_id"]);
		$('[name=client_source_id]').val(jArray[0]["client_source_id"]);
		$('[name=client_rating_id]').val(jArray[0]["client_rating_id"]);
		$('[name=country_id]').val(jArray[0]["country_id"]);
		$('[name=manager_name]').val(jArray[0]["manager_name"]);
		$('[name=address]').val(jArray[0]["address"]);
		$('[name=telephone]').val(jArray[0]["telephone"]);
		$('[name=mobile]').val(jArray[0]["mobile"]);
		$('[name=email]').val(jArray[0]["email"]);
		$('[name=facebook_page]').val(jArray[0]["facebook_page"]);
		$('[name=website]').val(jArray[0]["website"]);
		$('[name=comment]').val(jArray[0]["comment"]);
		$('[name=owned_date]').val(jArray[0]["owned_date"]);
		$('[name=owner]').val(jArray[0]["owner"]);
		$('[name=modified_date]').val(jArray[0]["modified_date"]);
		$('[name=modified_by]').val(jArray[0]["modified_by"]);
		$('[name=has_facebook_page_before]').bootstrapSwitch('state',parseInt(jArray[0]["has_facebook_page_before"]));
		$('[name=room_exists]').val(jArray[0]["room_exists"]);

		//combo
		refresh_SubSector_by_SectorVAL(jArray[0]["client_sector_sub_id"]);
		
		//fill list for CALLS
		loadCALLSrecs();
		
		//fill list for APPOINTMENTS
		loadAPPOINTMENTSrecs();
	}
	///////////////////////////////////////////////////////////// EDIT RECORD\
	

			var validatorCLIENT = $("#clients_FORM").validate({
				rules : {
					client_name : { 
						required : true,
						minlength : 2,
						maxlength : 200
					 },
					client_code : { 
						required : true,
						number: true,
						min: 1000,
						max: 9999
					 },
					 manager_name : { 
						required : true,
						minlength : 2,
						maxlength : 200
					 },
					 telephone : { digits: true },
					client_sector_id : { greaterThanZero : true },
					client_sector_sub_id : { greaterThanZero : true },
					client_source_id : { greaterThanZero : true },
					client_rating_id : { greaterThanZero : true },
					country_id : { greaterThanZero : true },
					
				},
				messages : {
					telephone : 'Only digits',
					client_name : 'Required Field',
					client_sector_id : 'Required Field',
					client_sector_sub_id : 'Required Field',
					client_source_id : 'Required Field',
					client_rating_id : 'Required Field',
					country_id : 'Required Field',
					manager_name : 'Required Field',
					client_code : 'Required Field, value must be > 1000',
				}
			});
			
			//when combo sector change
			$('[name=client_sector_id]').on('change', function()
				{
					refresh_SubSector_by_SectorVAL();
				});
				
			//when combo subsector change
			$('[name=client_sector_sub_id]').on('change', function()
			{
//				console.log(dontshowmodal);
				if ($(this).val()==0 && dontshowmodal==false)
				{
					$("#oSECTOR_description").val($("#client_sector_id").val());
					//show modal
					$('#modaloSUBSECTOR').modal('toggle');
				}

			});
			
			
		    ////////////////////////////////////////
		    // MODAL FUNCTIONALITIES [START]
		    //when modal closed, hide the warning messages + reset
		    $('#modalCLIENTS_PAGES').on('hidden.bs.modal', function() {
		        //when close - clear elements
		        $('#formCLIENTS_PAGES').trigger("reset");
		    });
		 
		    //functionality when the modal already shown and its long, when reloaded scroll to top
		    $('#modalCLIENTS_PAGES').on('shown.bs.modal', function() {
		        $(this).animate({
		            scrollTop : 0
		        }, 'slow');
		    });
		    // MODAL FUNCTIONALITIES [END]
		    ////////////////////////////////////////

			////////////////////////////////////////
			// MODAL SUBMIT aka save & update button
			$('#formCLIENTS_PAGES').submit(function(e) {
			    e.preventDefault();
			 
			 	if ($("[name=client_page]").val().trim().length == 0)
			 	{
					alert("Please enter facebook page");
					return;
				}
			 
			  loading.appendTo(document.body);
			  
			    var postData = $(this).serializeArray();
			    var formURL = $(this).attr("action");
			 
			    //close modal
			    $('#modalCLIENTS_PAGES').modal('toggle');
			 
			    $.ajax(
			    {
			        url : formURL,
			        type: "POST",
			        data : postData,
			        success:function(data, textStatus, jqXHR)
			        {
			            if (data=="00000"){
			            	
			            	fill_fb_pages();	
			            	
			            		loading.remove();					
						}

							//refresh
							//$('#clients_pages_tbl').bootstrapTable('refresh');
			            else
			                alert("ERROR");
			        },
			        error: function(jqXHR, textStatus, errorThrown)
			        {
			            alert("ERROR - connection error");
			        }
			    });
			});
					
				    
				    
			////////////////////////////////////////
			// MODAL FUNCTIONALITIES [START]
			//when modal closed, hide the warning messages + reset
			$('#modaloSUBSECTOR').on('hidden.bs.modal', function() {
				//when close - clear elements
				$('#formoSUBSECTOR').trigger("reset");

				//clear validator error on form
				validatorSUBSECTOR.resetForm();
			});
			
			//functionality when the modal already shown and its long, when reloaded scroll to top
			$('#modaloSUBSECTOR').on('shown.bs.modal', function() {
				$(this).animate({
					scrollTop : 0
				}, 'slow');
			});
			// MODAL FUNCTIONALITIES [END]
			////////////////////////////////////////
			

			var validatorSUBSECTOR = $("#formoSUBSECTOR").validate({
				rules : {
					 oSUBSECTOR_name : { 
						required : true,
						minlength : 2,
						maxlength : 100
					 },
					oSECTOR_description : { greaterThanZero : true }
					
				},
				messages : {
					oSECTOR_description : 'Required Field',
					oSUBSECTOR_name : 'Required Field'
				}
			});
			
		////////////////////////////////////////
		// MODAL SUBMIT aka save button
		$('#formoSUBSECTOR').submit(function(e) {
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
			$('#modaloSUBSECTOR').modal('toggle');

		    $.ajax(
		    {
		        url : formURL,
		        type: "POST",
		        data : postData,
		        success:function(data, textStatus, jqXHR) 
		        {
		            if (data=="ok")
		            	refresh_SubSector_by_SectorVAL();
		            else
		             	alert("ERROR");
		        },
		        error: function(jqXHR, textStatus, errorThrown) 
		        {
		            alert("ERROR");      
		        }
		    });
		});
		
	
	}); //jQuery ends
	

	function loadCALLSrecs(){
			$.ajax({
				type : 'POST',
				url : "tab_leads_details_calls_fetch_all.php",
				data : {
					CLIENT_id : <?php if (isset($_GET["id"])) echo $_GET["id"]; else echo 0;?>
				},
				success : function(msg) {
					$("#record_calls_rows").html(msg.tableRows);
				}
			});
		}
		
	function loadAPPOINTMENTSrecs(){
			$.ajax({
				type : 'POST',
				url : "tab_client_appointments_fetch_all.php",
				data : {
					CLIENT_id : <?php if (isset($_GET["id"])) echo $_GET["id"]; else echo 0;?>
				},
				success : function(msg) {
					$("#client_appointments_rows").html(msg.tableRows);
				}
			});
		}
		
	function refresh_SubSector_by_SectorVAL(sub_sector)
	{
		var sub_sector_id;
		sub_sector_id = sub_sector;
		
		
		$.ajax(
			{
				url : 'tab_leads_details_get_by_city.php',
				dataType : 'json',
				type : 'POST',
				data :
				{
					"valid" : $("#client_sector_id").val(),
				},
				success : function(data)
				{
					setComboItems("client_sector_sub_id",data.recs);

					if (sub_sector_id)
					{	$('[name=client_sector_sub_id]').val(sub_sector_id);
					
						if (sub_sector_id!= 0 && $('[name=client_sector_sub_id]').val()==null)
							alert("Sub Sector record cant be found!");
						
					}	
						

				},
				error : function(e)
				{
					alert("error");
				}
			});
	}
	
	//for subSector modal
	var dontshowmodal=true;
	function setComboItems(ctl_name, jArray)
	{
		var combo_rows = "<option value='0'>**Add new**</option>";
		for (var i = 0; i < jArray.length; i++)
		{
			combo_rows += "<option value='" + jArray[i]["ID"] + "'>" + jArray[i]["DESCR"] + "</option>";
		}

		dontshowmodal=true;
		$("[name=" + ctl_name + "]").html(combo_rows);
		$("[name=" + ctl_name + "]").change();
		
		dontshowmodal=false;
	}
	
//this function triggered by *tab_clients_details_detail.php*
	function submitform()
	{
	    ////////////////////////// validation
	    var form = $("#clients_FORM");
	    form.validate();

	    if (!form.valid())
	        return;
	    ////////////////////////// validation
	    
//	    	    console.log("submit");
//return;

<?php
	if (isset($_GET["id"])) {
?>
	  loading.appendTo(document.body);
	  form.submit();
	  
<?php	} else { ?>
//same for leads + customers  aka is unique
//				$.ajax({
//					url : 'tab_leads_details_save_new_codevalidation.php',
//					dataType : 'json',
//					type : 'POST',
//					data : {
//						is_lead :"1",
//					    client_code: $("#client_code").val()
//					},
//		            success : function(data) {
//		            	if(data==0)
//		            		{
//		            			loading.appendTo(document.body);
//								form.submit();
//							}
//						else {
//							alert ("Code already exists! A new code generated!\r\n\r\nPlease try now!");
//							$("#client_code").val(data);
//						}
//							
//					}
//				});
				

<?php	}  ?>	 
}


//////////facebook page
function show_facebook_pages_modal(){
	$('#modalCLIENTS_PAGES').modal('toggle');
}

function del_selected_facebook(){

var t = $("#fb_pages :selected").text(); 

if (t.length > 0 )	
{
	if (confirm("Delete "+t + " ?"))
	{
			  loading.appendTo(document.body);
			  
		$.post('tab_clients_details_detail_clients_pages_delete.php',	{id:$("#fb_pages").val()},function(e)
			{
				fill_fb_pages();
				loading.remove();
			});
		
	}
}

}

function fill_fb_pages(){
	$.ajax(
		{
			url : 'tab_clients_details_detail_clients_pages_fill.php',
			dataType : 'json',
			type : 'POST',
			data :
			{
				"id" : "<?= $_GET['id'] ?>",
			},
			success : function(data)
			{
				var jArray = data.recs;
				
				var combo_rows = ""; //<option value='0'></option>";
				for (var i = 0; i < jArray.length; i++)
				{
					combo_rows += "<option value='" + jArray[i]["client_page_id"] + "'>" + jArray[i]["client_page"] + "</option>";
				}
//refresh
				$("[name=fb_pages]").html(combo_rows);
				$("[name=fb_pages]").change();
			},
			error : function(e)
			{
				alert("error on fb_pages fill combo");
			}
		});	
}

</script>

<!-- Content Header (Page header) -->
<section class="content-header">
	<h1>
	<?php 
		if (isset($_GET["id"]))
		{
				echo "Update Client  (".$row[0]["client_code"]." - ".$row[0]["client_name"].")";
				echo "<script>document.title = 'pipiscrew - ".$row[0]['client_name']."';</script>";
		}
		else 
			echo "Create New Client";
	?>
		
	</h1>
</section>

<!-- Main content -->
<section class="content">
<div class="row">
	<div class="col-xs-12">

		<div class="box">
			<div class="box-header">

			</div>

			<div class="box-body table-responsive">
			
		<ul class='nav nav-pills' id='tabContainer'>
			<li class="active">
				<a href="#details" data-toggle='tab'>Details</a>
			</li>
				<?php
					if (isset($_GET['id'])) { ?>
							<li>
								<a href="#calls" data-toggle='tab'>Calls</a>
							</li>
							<li>
								<a href="#appointments" data-toggle='tab'>Appointments</a>
							</li>
							<li>
								<a href="#proposals" data-toggle='tab'>Proposals</a>
							</li>
							<li>
								<a href="#contracts" data-toggle='tab'>Contracts (Proposals PAID)</a>
							</li>
							<li>
								<a href="#invoicedetails" data-toggle='tab'>Add Invoice Details</a>
							</li>
							
			<?php } ?>
		</ul>
		
		<!-- TABS Content [START] -->
		<div id="tabsContent" class="tab-content">

			<div class="tab-pane fade in active" id="details">
				<?php
				include ('tab_clients_details_detail.php');
				?>
			</div>

			<div class="tab-pane" id="calls">
				<?php
					if (isset($_GET['id'])) {
						include ('tab_leads_details_calls.php');
					}
						
				?>
			</div>
			
			<div class="tab-pane" id="appointments">
				<?php
					if (isset($_GET['id'])) {
						include ('tab_client_appointments.php');
					}
						
				?>
			</div>
			
			<div class="tab-pane" id="proposals">
				<?php
					if (isset($_GET['id'])) {
						include ('tab_leads_details_proposals.php');
					}
						
				?>
			</div>	

			<div class="tab-pane" id="contracts">
				<?php
					if (isset($_GET['id'])) {
						include ('tab_clients_details_contracts.php');
					}
						
				?>
			</div>
			
			<div class="tab-pane" id="invoicedetails">
				<?php
					if (isset($_GET['id'])) {
						include ('tab_client_invoice_details.php');
					}
						
				?>
			</div>
		</div>
		<!-- TABS Content [END] -->

			</div><!-- /.box-body -->
		</div><!-- /.box -->
	</div>
</div>

<!-- NEW SUBCATEGORY MODAL [START] -->
<div class="modal fade bs-modal-lg" id="modaloSUBSECTOR" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
					&times;
				</button>
				<h4 class="modal-title" id='lblTitle_oSUBSECTOR'>New Sub Sector</h4>
			</div>
			<div class="modal-body">
				<form id="formoSUBSECTOR" role="form" method="post" action="tab_client_sector_subs_details_save2.php">

						<div class='form-group'>
							<label>Parent Sector :</label>
							<select id="oSECTOR_description" name='oSECTOR_description' class='form-control' readonly>
							</select>
						</div>
						
						<div class='form-group'>
							<label>Sub Sector Name :</label>
							<input id='oSUBSECTOR_name' name='oSUBSECTOR_name' class='form-control' placeholder='Sub Sector Name'>
						</div>
						
						<div class="modal-footer">
							<button id="bntCancel_oSUBSECTOR" type="button" class="btn btn-default" data-dismiss="modal">
								cancel
							</button>
							<button id="bntSave_oSUBSECTOR" class="btn btn-primary" type="submit" name="submit">
								save
							</button>
						</div>
						
				</form>
			</div><!-- End of Modal body -->
		</div><!-- End of Modal content -->
	</div><!-- End of Modal dialog -->
</div><!-- End of Modal -->
<!-- NEW SUBCATEGORY MODAL [END] -->

<!-- NEW CLIENTS_PAGES MODAL [START] -->
<div class="modal fade" id="modalCLIENTS_PAGES" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
					&times;
				</button>
				<h4 class="modal-title" id='lblTitle_CLIENTS_PAGES'>New Facebook Page</h4>
			</div>
			<div class="modal-body">
				<form id="formCLIENTS_PAGES" role="form" method="post" action="tab_clients_details_detail_clients_pages_save.php">



				    
					<div class='form-group'>
						<label>Facebook Page :</label>
					    <div class="input-group">
					      <div class="input-group-addon">facebook.com/</div>
							<input name='client_page' class='form-control' placeholder='pipiscrew'>	
					    </div>
					</div>

					<input name="client_page_client_id" id="client_page_client_id" class="form-control" value="<?= $_GET['id'] ?>" style="display:none;">

					<div class="modal-footer">
						<button id="bntCancel_CLIENTS_PAGES" type="button" class="btn btn-default" data-dismiss="modal">
							cancel
						</button>
						<button id="bntSave_CLIENTS_PAGES" class="btn btn-primary" type="submit" name="submit">
							save
						</button>
					</div>
                </form>
            </div><!-- End of Modal body -->
        </div><!-- End of Modal content -->
    </div><!-- End of Modal dialog -->
</div><!-- End of Modal -->
<!-- NEW CLIENTS_PAGES MODAL [END] -->
<?php
include ('template_bottom.php');
?>