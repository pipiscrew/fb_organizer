<?php

//session_start();
//
//if (!isset($_SESSION["u"])) {
//	header("Location: index.html");
//	exit ;
//}

$active_tab="leads";

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


$client_sector_subs_rows=null;
///////////////////READ client_sector_subs
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


//$tax_offices_rows=null;
///////////////////READ tax_offices
//	$find_sql = "SELECT * FROM `tax_offices` order by tax_office_name";
//	$stmt      = $db->prepare($find_sql);
//	
//	$stmt->execute();
//	$tax_offices_rows = $stmt->fetchAll();
///////////////////READ tax_offices


$rowREC=null;
///////////////////READ SPEFIC RECORD
if (isset($_GET["id"])) {
	$find_sql = "SELECT client_id
	,is_lead
	,profile_guid
	,client_code
	,client_name
	,client_sector_id
	,client_sector_sub_id
	,client_source_id
	,client_rating_id
	,profile_sent
	,country_id
	,manager_name
	,manager_name2
	,address
	,telephone
	,mobile
	,email
	,email2
	,facebook_page
	,website
	,comment
	,DATE_FORMAT(owned_date, '%d-%m-%Y %H:%i') AS owned_date
	,userA.fullname as owner
	,DATE_FORMAT(modified_date, '%d-%m-%Y %H:%i') AS modified_date
	,userB.fullname as modified_by
	,has_facebook_page_before
	,room_exists
	,city
	,area
	,clients.owner as ownerB
FROM `clients`
left join users as userA on userA.user_id = clients.owner
left join users as userB on userB.user_id = clients.modified_by where client_id = :id";

	$stmt      = $db->prepare($find_sql);
	$stmt->bindValue(':id', $_GET["id"]);
	
	$stmt->execute();
	$rowREC = $stmt->fetchAll();

if ($_SESSION["level"] != 9 && $rowREC[0]["ownerB"]!=$_SESSION["id"])
 {
 	if ($rowREC[0]==null)
 		die("record doesnt exist! ask administrator why!");
 	else 
 		die("you cant administrate this record! ask administrator why!");
 }
}
///////////////////READ SPEFIC RECORD

 if ($rowREC!=null && $rowREC[0]["is_lead"] != 1 )
 	die("Company is not lead");
 else if (isset($_GET["id"]) && $rowREC==null)
 	die("Company is NULL!");
?>


<!--jqTE files-->
<link href="css/jquery-te-green.css" rel="stylesheet"></link> 
<script type="text/javascript" src="js/jquery-te-1.4.0.min.js"></script>

<script type="text/javascript">

			//user by tab_client_appointments.php
			var is_lead = 1;

		function getParameterByName(name) {
		    name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
		    var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
		        results = regex.exec(location.search);
		    return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
		}

        $(function() {

		//setup jQTE			
		$("#mail3_body").jqte({css:"jqte_green"});

		$('[name=telephone]').on('input',function(e){
			if ($(this).val().length>9)
			{
			 	$("#tel_indicator").show();
			 	
					$.ajax({
						url : 'tab_leads_details_ask_4_double.php',
						dataType : 'json',
						type : 'POST',
						data : {
							"tel" : $(this).val()
						},
			            success : function(data) {
			            	$("#tel_indicator").hide();
			            	
			            	if (data=="error")
			            		alert("Error - when checking for double records via telephone");
			            	else if (data.rec_count>0)
			            	{
								$("[name=telephone]").css({ 'background': 'rgba(255, 0, 0, 0.3)' });
							}
							else 
								$("[name=telephone]").css({ 'background': 'white' });
						}
					});
			}
			else 
				$("[name=telephone]").css({ 'background': 'white' });
		});
		
//GET NEW CLIENT CODE IF IS ON 'CREATE NEW'
 <?php if (!isset($_GET["id"])){ ?>
 	
 				$.ajax({
					url : 'tab_leads_details_save_new_getcode.php',
					dataType : 'json',
					type : 'POST',
					data : {
						is_lead :"1", //no needed
					    client_code: "1000"
					},
		            success : function(data) {
							$("#client_code").val(data);
					}
				});
<?php } elseif (isset($_GET["showcalls"])) {?>	
			$('a[href=#calls]').tab('show');
<?php } elseif (isset($_GET["showproposals"])) {?>	
			$('a[href=#proposals]').tab('show');	
<?php } 

if (isset($_GET["addfacebook"])) { ?>

//when parameter specificed andalso doesnt have facebook page before
if ($('#fb_pages option').length==0)
	show_facebook_pages_modal();
	
<?php }
?>		

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


	///////////////////////////////////////////////////////////// FILL client_sector_subs
//	var jArray_client_sector_subs =   <?php echo json_encode($client_sector_subs_rows); ?>;
//
//	var combo_client_sector_subs_rows = "<option value='0'></option>";
//	for (var i = 0; i < jArray_client_sector_subs.length; i++)
//	{
//		combo_client_sector_subs_rows += "<option value='" + jArray_client_sector_subs[i]["client_sector_sub_id"] + "'>" + jArray_client_sector_subs[i]["client_sector_sub_name"] + "</option>";
//	}
//
//	$("[name=client_sector_sub_id]").html(combo_client_sector_subs_rows);
//	$("[name=client_sector_sub_id]").change(); //select row 0 - no conflict on POST validation @ PHP
	///////////////////////////////////////////////////////////// FILL client_sector_subs


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

	$("[name=country_id]").html(combo_countries_rows);
	$("[name=country_id]").change(); //select row 0 - no conflict on POST validation @ PHP
	///////////////////////////////////////////////////////////// FILL countries


	$("[name='is_lead']").bootstrapSwitch();
	$("[name='profile_sent']").bootstrapSwitch();
	$("[name='has_facebook_page_before']").bootstrapSwitch();
	$("[name='room_exists']").bootstrapSwitch();
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

//	var d1 = new Date();
//	d1.setDate(d1.getDate());
		
	//set default value
//	$('[name=meeting_datetime]').val(new Date().toISOString().slice(0, 10));

	//init datepicker
//	$('[name=next_call]').datepicker().on('changeDate', function(ev)
//	{
//		$('[name=next_call]').datepicker('hide'); //close when selected
//	});


//	$('[name=next_call]').val(new Date().toISOString().slice(0, 10));
    
//	var d1 = new Date();
//	d1.setDate(d1.getDate());
//		
//	//set default value
//	$('[name=next_call]').datepicker('setValue', d1)


//	var d1 = new Date();
//	d1.setDate(d1.getDate());
//		
//	//set default value
//	$('[name=owned_date]').datepicker('setValue', d1)
//
//	//init datepicker
//	$('[name=modified_date]').datepicker().on('changeDate', function(ev)
//	{
//		$('[name=modified_date]').datepicker('hide'); //close when selected
//	});
//
//	var d1 = new Date();
//	d1.setDate(d1.getDate());
//		
//	//set default value
//	$('[name=modified_date]').datepicker('setValue', d1)

//	//init datepicker
//	$('[name=next_renewal]').datepicker().on('changeDate', function(ev)
//	{
//		$('[name=next_renewal]').datepicker('hide'); //close when selected
//	});
//
//	var d1 = new Date();
//	d1.setDate(d1.getDate());
//		
//	//set default value
//	$('[name=next_renewal]').datepicker('setValue', d1)
//
//	//init datepicker
//	$('[name=marketingplan_datetime]').datepicker().on('changeDate', function(ev)
//	{
//		$('[name=marketingplan_datetime]').datepicker('hide'); //close when selected
//	});
//
//	var d1 = new Date();
//	d1.setDate(d1.getDate());
//		
//	//set default value
//	$('[name=marketingplan_datetime]').datepicker('setValue', d1)


	///////////////////////////////////////////////////////////// EDIT RECORD
	var jArray = <?php echo json_encode($rowREC); ?>;
	
//	console.log(jArray);
	if (jArray) {
		//WARNING THE FIELD NAMES IS CASE SENSITIVE ON ARRAY
		// console.log(jArray);
		//if checkbox - $('[name=visible_cat]').prop('checked', jArray["visible"]);
		$('[name=leadsFORM_updateID]').val(jArray[0]["client_id"]);
//		$('[name=is_lead]').bootstrapSwitch('state',parseInt(jArray[0]["is_lead"]));
		$('[name=client_name]').val(jArray[0]["client_name"]);
		$('[name=city_lead]').val(jArray[0]["city"]);
		$('[name=area_lead]').val(jArray[0]["area"]);		
		$('[name=client_code]').val(jArray[0]["client_code"]);
		$('[name=client_sector_id]').val(jArray[0]["client_sector_id"]);
	//	$('[name=client_sector_sub_id]').val(jArray[0]["client_sector_sub_id"]);
		$('[name=client_source_id]').val(jArray[0]["client_source_id"]);
		$('[name=client_rating_id]').val(jArray[0]["client_rating_id"]);
	//	$('[name=next_call]').val(jArray[0]["next_call"]);
		$('[name=profile_sent]').bootstrapSwitch('state',parseInt(jArray[0]["profile_sent"]));
		$('[name=country_id]').val(jArray[0]["country_id"]);
		$('[name=manager_name]').val(jArray[0]["manager_name"]);
		$('[name=manager_name2]').val(jArray[0]["manager_name2"]);
		$('[name=address]').val(jArray[0]["address"]);
//		$('[name=vat_no]').val(jArray[0]["vat_no"]);
//		$('[name=tax_office_id]').val(jArray[0]["tax_office_id"]);
		$('[name=telephone]').val(jArray[0]["telephone"]);
		$('[name=mobile]').val(jArray[0]["mobile"]);
		$('[name=email]').val(jArray[0]["email"]);
		$('[name=email2]').val(jArray[0]["email2"]);
		$('[name=facebook_page]').val(jArray[0]["facebook_page"]);
		$('[name=website]').val(jArray[0]["website"]);
//		$('[name=service_starts]').val(jArray[0]["service_starts"]);
//		$('[name=service_ends]').val(jArray[0]["service_ends"]);
		$('[name=comment]').val(jArray[0]["comment"]);
		$('[name=owned_date]').val(jArray[0]["owned_date"]);
		$('[name=owner]').val(jArray[0]["owner"]);
		$('[name=modified_date]').val(jArray[0]["modified_date"]);
		$('[name=modified_by]').val(jArray[0]["modified_by"]);
		$("#profile_guid").val(jArray[0]["profile_guid"]);
//		$('[name=has_facebook_page_before]').bootstrapSwitch('state',parseInt(jArray[0]["has_facebook_page_before"]));
//		$('[name=facebook_likes]').val(jArray[0]["facebook_likes"]);
//		$('[name=next_renewal]').val(jArray[0]["next_renewal"]);
//		$('[name=marketingplan_datetime]').val(jArray[0]["marketingplan_datetime"]);
//		$('[name=marketingplan_location]').val(jArray[0]["marketingplan_location"]);
//		$('[name=marketingplan_google]').val(jArray[0]["marketingplan_google"]);
//		$('[name=marketingplan_attachment]').val(jArray[0]["marketingplan_attachment"]);
//		$('[name=room_exists]').bootstrapSwitch('state',parseInt(jArray[0]["room_exists"]));

		//combo
		refresh_SubSector_by_SectorVAL(jArray[0]["client_sector_sub_id"]);
		
		//fill list for CALLS
		loadCALLSrecs();
		
		//fill list for APPOINTMENTS
		loadAPPOINTMENTSrecs();
	}
	///////////////////////////////////////////////////////////// EDIT RECORD\
	
			var validatorLEAD = $("#leads_FORM").validate({
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
					email : {
						email: true	
					},
					email2 : {
						email: true	
					}
					
					
				},
				messages : {
					telephone : 'Only digits',
					client_name : 'Required Field',
					client_sector_id : 'Required Field',
					client_sector_sub_id : 'Required Field',
					client_source_id : 'Required Field',
					client_rating_id : 'Required Field',
					manager_name : 'Required Field',
					client_code : 'Required Field, value must be > 1000',
					email : 'Please enter a valid email',
					email2 : 'Please enter a valid email',
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
			            else
			                alert("ERROR");
			                
	    			    //show profile mail when addnew...
	    			    var g = getParameterByName("sendprofile");
	    			    console.log(g);
	    			    if (g==1)
	    			    {
							sent_profile();
						}
	    			    //show profile mail when addnew...
    			    
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
		
		
		////////////////////////////////////////
		// MODAL SUBMIT aka SEND MAIL
		$('#formomailthree').submit(function(e) {
			e.preventDefault();

			loading.appendTo($('#formomailthree'));

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
		            {	
		            	alert("Email successfully delivered to " + $("#mail3_recipient").val());
		            	//location.reload(true);
		            	var x = document.URL;
		            	x = x.replace("addfacebook=1","");
		            	window.location=x;
		            	return;

		            }
		            else
		             	alert("Error, please verify mail and retry!");
		             	
    			    $('#modalomailthree').modal('toggle');
   			    
		        },
		        error: function(jqXHR, textStatus, errorThrown) 
		        {
		            alert("Server Error");      
		        }
		    });
		});
		
				
	});
	//jquery ends
	
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
	


    	
		
function sent_profile()
{
	var id = '<?=$_GET["id"];?>' ;

	if (!id)
	{
		alert("Please save the record and retry!");
		return;
	} else {
		if ($("[name=manager_name]").val().trim().length == 0)
		{
			alert ("Send Profile\r\n\r\nPlease enter 'Manager Name' and save the record");
			return;
		}

		if ($("[name=client_name]").val().trim().length == 0)
		{
			alert ("Send Profile\r\n\r\nPlease enter 'Company Name' and save the record");
			return;
		}

		if ($("[name=email]").val().trim().length == 0)
		{
			alert ("Send Profile\r\n\r\nPlease enter 'Email' and save the record");
			return;
		}
		
		if ($("#profile_guid").val().trim().length == 0)
		{
			alert ("Send Profile\r\n\r\nInform the administrator to enter 'GUID'");
			return;
		}
	}


	var mailbody = "Αξιότιμε κύριε " + $("[name=manager_name]").val() + ",<br><br>Σας επισυνάπτω το Link του εταιρικού μας προφίλ, σχετικά με τα Facebook Business Services που παρέχουμε.<br><br>";

	mailbody+= "<a href='http://localhost:8080/profile/?z=" + $("#profile_guid").val() +  "' target='_blank'>Profile</a><br><br>";
	mailbody+="Είμαι στην διάθεσή σας για οποιαδήποτε διευκρίνηση χρειαστείτε.<br><br><br>";
	mailbody+= "<span style='color:rgb(102,102,102)'>	With regards,<br><?=$_SESSION['u'];?><br><?=$_SESSION['u_sign'];?></span><br><font size='1' color='#666666'>	<img src='https://lh4.googleusercontent.com/-qhw8okUHR1U/UzwX6pABRaI/AAAAAAAAAe0/K1JvbXpwufs/w415-h61-no/Signature.png' width='200' height='29' class='CToWUd'><br></font><span style='font-family:verdana,geneva;font-size:x-small'>	16 Beaufort Court,<br>Canary Wharf<br>E14 9XL London, UK</span><br><b style='font-size:x-small'>	<a href='mailto:pipiscrew@pipiscrew.com' target='_blank'>		Email us	</a></b><br><font size='1' color='#666666'>	<b>		<a href='http://ww.facebook.com/pipiscrew' target='_blank'>			Go To Facebook Page		</a>	</b></font><br><font size='1' color='#666666'>	<b>		<a href='http://www.pipiscrew.com/' target='_blank'>			Go To Website		</a>	</b></font><br><font color='#666666'>	<b style='font-size:x-small'>		UK:&nbsp;		<a value='+442032390395'>			+44 20 32 39 0395		</a>	</b></font><br><font size='1' color='#666666'>	<b>		Greece:&nbsp;		<a value='+302155309484'>			+30 215 530 9484		</a>	</b></font><br><font color='#808080' face='Verdana, sans-serif'>	<span style='font-size:11px'>		- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -&nbsp;	</span></font><br><span style='color:gray;font-family:Verdana,sans-serif;font-size:8pt'>	This e-mail and any attached files are confidential and may also be legally privileged.</span><br><span style='color:gray;font-family:Verdana,sans-serif;font-size:8pt'>	They are intended solely for the intended addressee.</span><font color='#222222' face='Calibri, sans-serif'>	&nbsp;</font><span style='color:gray;font-family:Verdana,sans-serif;font-size:8pt'>	If you are not the addressee,</span><br><span style='color:gray;font-family:Verdana,sans-serif;font-size:8pt'>	please e-mail it back to the sender and then immediately, permanently delete it.</span><br><span style='color:gray;font-family:Verdana,sans-serif;font-size:8pt'>	Do not read, print, re-transmit, store or act in reliance on it.</span><br><span style='font-size:8pt;font-family:Webdings;color:green'>	P</span>&nbsp;<span style='font-size:8pt;font-family:Verdana,sans-serif;color:gray'>	Please consider the environment before printing this e-mail.</span>";
	
	$("#mail3_subject").val("pipiscrew Facebook Agency - " + $("[name=client_name]").val() + " - Profile");
	
	var rec=$("[name=email]").val();
	
	if ($("[name=email2]").val().trim().length>0)
		rec+=";" + $("[name=email2]").val();
	
	$("#mail3_recipient").val(rec);
	
	$("#mail3_body").jqteVal(mailbody);
	$("#mail3_offer_rec_id").val(id);

	$('#modalomailthree').modal('toggle');	

	
}

//this function triggered by *tab_leads_details_detail.php*
	function submitform()
	{
	    ////////////////////////// validation
	    var form = $("#leads_FORM");
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

				$.ajax({
					url : 'tab_leads_details_save_new_codevalidation.php',
					dataType : 'json',
					type : 'POST',
					data : {
						is_lead :"1",
					    client_code: $("#client_code").val(),
					    telephone : $("[name=telephone]").val(),
					    mobile : $("[name=mobile]").val(),
					    client_name : $("[name=client_name]").val(),
					    website : $("[name=website]").val()
					},
		            success : function(data) {
//		            	console.log(data);
		            	var doublecheck;
		            	var code;
		            	doublecheck = data.doublecheck;
		            	code = data.code;
		            	
		            	if (doublecheck.length>0)
		            	{
							var dcheck_txt="";

							
							for(var no in doublecheck)
								dcheck_txt += doublecheck[no]+"\r\n";
								
							alert ("Double record! Occurred at \r\n\r\n"+dcheck_txt);
							return;
						}


		            	if(code==0)
		            		{
		            			loading.appendTo(document.body);
								 form.submit();
							}
						else {
							
							var dcheck_txt="";

							if (doublecheck.length>0){
								for(var no in doublecheck)
									dcheck_txt += doublecheck[no]+"\r\n";
							}

								
							alert ("Code already exists! A new code generated!\r\n\r\nPlease try now!");
							$("#client_code").val(code);
						}
							
					}
				});
				

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
		if (isset($_GET["id"])){
				echo "Update Lead  (".$rowREC[0]["client_code"]." - ".$rowREC[0]["client_name"].")";
				echo "<script>document.title = 'pipiscrew - ".$rowREC[0]['client_name']."';</script>";
		}			
		else 
			echo "Create New Lead";
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
			<?php } ?>
		</ul>
		
		<!-- TABS Content [START] -->
		<div id="tabsContent" class="tab-content">

			<div class="tab-pane fade in active" id="details">
				<?php
				include ('tab_leads_details_detail.php');
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

<!-- mail3 MODAL [START] -->
<div class="modal fade" id="modalomailthree" tabindex="-1" role="dialog" aria-labelledby="myModalLabeld" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
					&times;
				</button>
				<h4 class="modal-title" id='lblTitle_mailthree'>Send Profile</h4>
			</div>
			<div class="modal-body">
				<p style="background-color: #428bca;color:#fff;padding: 5px;" align=center>email will be send by proposal@watetron.com with <b>reply</b> property to <b><?=$_SESSION['reply_mail'];?></b></p>				
				
				<form id="formomailthree" role="form" method="post" action="tab_leads_details_send_profile_mail.php">

						<div class='form-group'>
							<label>Recipient (multiple addresses separated by semicolon) :</label>
							<input id='mail3_recipient' name='mail3_recipient' class='form-control' placeholder='Recipient' required autofocus>
						</div>
						
						<div class='form-group'>
							<label>Subject :</label>
							<input id='mail3_subject' name='mail3_subject' class='form-control' placeholder='Subject' required autofocus>
						</div>

							<input id='mail3_body' name='mail3_body' data-role="none" class='editor'>
							
							<input id='mail3_offer_rec_id' name='mail3_offer_rec_id' style="display: none">
													
						<div class="modal-footer">
							<button id="bntCancel_mailthree" type="button" class="btn btn-default" data-dismiss="modal">
								cancel
							</button>
							<button id="bntSend_mailthree" class="btn btn-primary" type="submit" name="submit">
								send
							</button>
						</div>
					</form>
            </div><!-- End of Modal body -->
        </div><!-- End of Modal content -->
    </div><!-- End of Modal dialog -->
</div><!-- End of Modal -->
<?php
include ('template_bottom.php');
?>