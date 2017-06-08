<?php
if(!isset($_POST['client_id'])){
	echo "customer identification is not supplied!";
	return;
}

$active_tab     = "proposals";

require_once ('template_top.php');

// include DB
require_once ('config.php');

$db             = connect();


$countries_rows = null;
///////////////////READ countries
$find_sql       = "select country_id,country_name from countries order by country_name";
$stmt           = $db->prepare($find_sql);

$stmt->execute();
$countries_rows = $stmt->fetchAll();
///////////////////READ countries

$categories_rows= null;
///////////////////READ categories
$find_sql        = "select category_id,category_name from categories order by category_name";
$stmt            = $db->prepare($find_sql);

$stmt->execute();
$categories_rows = $stmt->fetchAll();
///////////////////READ categories

$sellers= null;
///////////////////READ sellers
$find_sql        = "select user_id, fullname from users where user_level_id in (1,2,9,10) order by fullname";
$stmt            = $db->prepare($find_sql);

$stmt->execute();
$sellers = $stmt->fetchAll();
///////////////////READ sellers

$customer_row  = null;
///////////////////READ customer
$customer_row = getRow($db, "select * from clients where client_id=?", array($_POST['client_id']));
///////////////////READ customer

?>

<!--jqTE files-->
<link href="css/jquery-te-green.css" rel="stylesheet"></link> 
<script type="text/javascript" src="js/jquery-te-1.4.0.min.js"></script>
<script type='text/javascript' src='js/jquery.validate.min.js'>
</script>

<style>
	/*jquery.validate.min red caption*/
	label.error
	{
		color: #FF0000;
		font-size: 11px;
		display: block;
		width: 100%;
		white-space: nowrap;
		float: none;
		margin: 8px 0 -8px 0;
		padding: 0!important;
	}

</style>

<style type="text/css">
	table.gridtable {
		font-family: verdana,arial,sans-serif;
		font-size:11px;
		color:#333333;
		border-width: 1px;
		border-color: #c0c0c0;
		border-collapse: collapse;
 		width:95%; //per container width
		margin: 0 auto; //center
	}
	table.gridtable th {
		border-width: 1px;
		padding: 8px;
		border-style: solid;
		border-color: #c0c0c0;
	}
	table.gridtable td {
		border-width: 1px;
		padding: 8px;
		border-style: solid;
		border-color: #c0c0c0;
	}
</style>

<script type="text/javascript">
			var go_back_url;
			var go_back_url_timer;
			
	function myTimer() {
		clearInterval(go_back_url_timer);
		
		window.location=go_back_url;
	}
	

	var change_after_check=false;
	var check_if_change_after_check=false;
 
	$(function()
		{
			fill_fb_pages();

			//setup jQTE			
			$("#mail_body").jqte({css:"jqte_green"});

			///////////////////////////////////////////////////////////// FILL sellers
			var jArray_sellers =   <?php echo json_encode($sellers); ?>;

			var combo_sellers_rows = "<option value='0'></option>";
			for (var i = 0; i < jArray_sellers.length; i++)
			{
				combo_sellers_rows += "<option value='" + jArray_sellers[i]["user_id"] + "'>" + jArray_sellers[i]["fullname"] + "</option>";
			}

/////////////////////////////

//on value change
$('[name=send_ppl_website],[name=increase_conversions],[name=boost_posts],[name=promote_page_likes],[name=get_installs_app],[name=increase_engag],[name=raise_attendance],[name=claim_offer],[name=video_views]').on('input',function(e){

	calculate_percentage(this.name);
});


	
	$('#is_new_offer,#category,#contract,#post_manage,#apps,#country').on('change', function() {
		if (check_if_change_after_check)
	  		change_after_check=true;
	});

	$('[name=budget],[name=discount],[name=budget],[name=extra_budget],[name=app_ad_budget]').change(function() { 
		if (check_if_change_after_check)
			change_after_check=true;
	});



/////////////////////////////

			$("#seller_id").html(combo_sellers_rows);
			$("#seller_id").change(); //select row 0 - no conflict on POST validation @ PHP
			$("#seller_id").val(<?=$_SESSION['id'];?>);
			
				//a item clicked from list
				$('#the_specs_list').on('click', 'a', function(event) {
					event.preventDefault();
					
					if ($(this).attr('data-name')==null)
						return;

					var row_text_name = $(this).data('name');
					
					if ($(this).hasClass('list-group-item active')) {
						//clear textboxes
						$("[name="+row_text_name + "]").val("");
						$("[name="+row_text_name + "_cost]").val("");
						$("[name="+row_text_name + "_d]").val("");
						$("[name="+row_text_name + "_w]").val("");
						$("[name="+row_text_name + "_m]").val("");
						
						//hide row
						$("#"+row_text_name + "_row").css("visibility", 'hidden');
						
						$(this).removeClass('list-group-item active');
						$(this).addClass('list-group-item');
					} else {
						$("#"+row_text_name + "_row") .css("visibility", 'visible');
						$(this).addClass('list-group-item active');
					}
					
				});
				

<?php
if ($_SESSION['level']!=9) {
	?>
		$('#seller_id').prop("disabled", true);
	<?php
} ?>

//$("[name=seller]").val($("#seller_id :selected").text()); 

			///////////////////////////////////////////////////////////// FILL sellers
			
			///////////////////////////////////////////////////////////// FILL countries
			var jArray_countries =   <?php echo json_encode($countries_rows); ?>;

			var combo_countries_rows = "<option value='0'></option>";
			for (var i = 0; i < jArray_countries.length; i++)
			{
				combo_countries_rows += "<option value='" + jArray_countries[i]["country_id"] + "'>" + jArray_countries[i]["country_name"] + "</option>";
			}

			$("#country").html(combo_countries_rows);
			$("#country").val(1);
			//$("#country").change(); //select row 0 - no conflict on POST validation @ PHP
			///////////////////////////////////////////////////////////// FILL countries

			///////////////////////////////////////////////////////////// FILL countries
			var jArray_cats =   <?php echo json_encode($categories_rows); ?>;

			var combo_cats_rows = "<option value='0'></option>";
			for (var i = 0; i < jArray_cats.length; i++)
			{
				combo_cats_rows += "<option value='" + jArray_cats[i]["category_id"] + "'>" + jArray_cats[i]["category_name"] + "</option>";
			}

			$("#category").html(combo_cats_rows);
			$("#category").change(); //select row 0 - no conflict on POST validation @ PHP
			///////////////////////////////////////////////////////////// FILL countries

			///////////////////////////////////////////////////////////// set company combo + textbox details
			
			var jArray_cust =   <?php echo json_encode($customer_row); ?>;
			
if (jArray_cust){
	

			//hidden input
			$("#cust_id").val(jArray_cust["client_id"]);
			$("#cust_code").val(jArray_cust["client_code"]);
			//hidden input
						
			//textboxes
			$("[name=company_name]").val(jArray_cust["client_name"]);
			$("[name=company_manager_name]").val(jArray_cust["manager_name"]);
			
			var rec = jArray_cust["email"];

			if (jArray_cust["email2"] != null && jArray_cust["email2"].length>0)
					rec += ";" + jArray_cust["email2"];
				
			$("[name=email]").val(rec);
			
			$("[name=telephone]").val(jArray_cust["telephone"]);
			$("[name=city]").val(jArray_cust["city"]);
			$("[name=page_url]").val(jArray_cust["facebook_page"]);
		
			var offer_type="<option value='0'></option>";
			if (jArray_cust["is_lead"]==0) //active_client
			{
				go_back_url = "tab_clients_details.php?showproposals=1&id="+jArray_cust["client_id"];
				offer_type+= "<option value='1'>New</option><option value='2'>Update</option><option value='3'>Renewal</option>";
			}
			else if(jArray_cust["is_lead"]==1) //lead
			{
				go_back_url = "tab_leads_details.php?showproposals=1&id="+jArray_cust["client_id"];
				offer_type+= "<option value='1'>New</option>";
			}
			else if(jArray_cust["is_lead"]==2) //inactive_client
			{
				go_back_url = "tab_inclients_details.php?showproposals=1&id="+jArray_cust["client_id"];
				offer_type+= "<option value='1'>New</option><option value='3'>Renewal</option>";
			}

			$("#offer_type").html(offer_type);
			
			if (jArray_cust["is_lead"]==1)
				$("#offer_type").val(1);
			else 
				$("#offer_type").change(); //select row 0

}
else {
	alert("ERROR! Couldnt find customer record!");
}
			///////////////////////////////////////////////////////////// set company combo + textbox details
			
//			$('#seller_id').on('change', function()
//				{
//					$("[name=seller]").val($("#seller_id :selected").text()); 
//				});
				
		// MODAL FUNCTIONALITIES [START]
		//when modal closed, hide the warning messages + reset
		$('#modaloMAIL').on('hidden.bs.modal', function() {
			window.location=go_back_url;
		});
		
		////////////////////////////////////////
		// MODAL SUBMIT aka save button
		$('#formoMAIL').submit(function(e) {
			e.preventDefault();

			loading.appendTo($('#formoMAIL'));
//			loading.appendTo(document.body);

		    var postData = $(this).serializeArray();
		    var formURL = $(this).attr("action");

			//close modal
//			$('#modaloSUBSECTOR').modal('toggle');

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
		
			$('#is_new_offer').on('change', function()
				{
					$("#is_new_offer_hide").slideToggle();

					if (this.value==0)
					$("[name=page_url]").val("");
				});



			$("[name=doc_english],[name='purp_apps'],[name='purp_sales'] ,[name='page_merge'],[name='name_change'],[name='graphics_sw']").bootstrapSwitch();


			var d1 = new Date();
			d1.setDate(d1.getDate());

			//default value for datepicker + 9days
			var d2 = new Date();
			d2.setDate(d2.getDate()+9);
			//default value for datepicker + 9days

			//init datepicker
			$('#proposaldate, #proposaldatetill').datepicker().on('changeDate', function(ev)
				{
					$('#proposaldate, #proposaldatetill').datepicker('hide'); //to close when selected
				});

			//set default value
			$('#proposaldate').datepicker('setValue', d1)

			//set default value
			$('#proposaldatetill').datepicker('setValue', d2)

			///////////////////////////////////// add custom validations
			//validate currency
			$.validator.addMethod('currency', function(value, element, regexp)
				{
					var re = /^\d{1,9}(\.\d{1,2})?$/;
					return this.optional(element) || re.test(value);
				}, '');

			//for combos
			$.validator.addMethod("valueNotEquals", function(value, element, arg)
				{
					return arg != value;
				}, "");
			///////////////////////////////////// add custom validations


			$.validator.addMethod("regx", function(value, element, regexpr)
				{
					return regexpr.test(value);
				}, '');



			$('#page_merge').on('switchChange.bootstrapSwitch', function (event, state)
				{
					

					$("#page_mergeDIV").slideToggle();
				});


			$('#name_change').on('switchChange.bootstrapSwitch', function (event, state)
				{
					

					$("#name_changeDIV").slideToggle();
				});

			var validatorFRM = $("#offer_FORM").validate(
				{
					rules :
					{
						seller_id :
						{
							required : true,
							valueNotEquals : '0'
						},
						page_url :
						{
							required : true
						},
						extra_budget :
						{
							currency : true
						},
						app_ad_budget :
						{
							currency : true
						},
						proposaldate :
						{
							required : true
						},
						proposaldatetill :
						{
							required : true
						},
						city :
						{
							required : true
						},
						company_manager_name :
						{
							required : true
						},
						company_name :
						{
							required : true
						},
						email :
						{
							required : true,
							email: true
						},
						telephone :
						{
							required : true,
							minlength : 7,
							digits : true
						},
						country :
						{
							required : true,
							valueNotEquals : '0'
						},
						offer_type :
						{
							required : true,
							valueNotEquals : '0'
						},
						category :
						{
							required : true,
							valueNotEquals : '0'
						},
						budget:
						{
							required : true,
							currency : true,
							min : 150
						}


					},
					messages :
					{
						seller_id : 'Required Field',
						page_url : 'Required Field',
						offer_type : 'Required Field',
						email : 'Required Field',
						telephone : 'Required Field, at least 7 digit',
						country : 'Required Field',
						category : 'Required Field',
						budget : 'Required Field, acceptable value 150'
					}
				});

				$('[name=fb_pages]').on('change', function() {
					var t = $("#fb_pages :selected").text(); 

					if (t.length > 0 )	
						  $("[name=page_url]").val(t);
				});
	
	
			$('#contract').on('change', function() {
			  calculate_percentage_all();
			});
	
			$('[name=budget]').on('input',function(e){
				calculate_percentage_all();
			});
			
//$('[name=send_ppl_website]').on('input',function(e){
//	calculate_percentage(this.name);
//});



	
		});//jQuery ENDS


	function fill_fb_pages(){
		$.ajax(
			{
				url : 'tab_clients_details_detail_clients_pages_fill.php',
				dataType : 'json',
				type : 'POST',
				data :
				{
					"id" : "<?= $_POST['client_id'] ?>",
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

	function humanizeNumber(n) {
	  n = n.toString()
	  while (true) {
	    var n2 = n.replace(/(\d)(\d{3})($|,|\.)/g, '$1,$2$3')
	    if (n == n2) break
	    n = n2
	  }
	  return n
	}

	function validate_one_month(){
		if ($("#offer_type").val() == 1 && $("#contract").val() == 1 && $("#budget").val() < 300)
		{
			alert("When using period '1 month' the budget can not be less 300 euro!")	
			return false;
		}
		else 
			return true;
	}
	
	function ctl_name2name(ctl_name){
	
		switch(ctl_name)
		{
			case "send_ppl_website":
				return "Send people to your website";
				break;
			case "increase_conversions":
				return "Increase conversions on your website";
				break;
			case "boost_posts":
				return "Boost your posts";
				break;
			case "promote_page_likes":
				return "Promote your Page";
				break;
			case "get_installs_app":
				return "Get installs of your app";
				break;
			case "increase_engag":
				return "Increase engagement in your app";
				break;
			case "raise_attendance":
				return "Raise attendance at your event";
				break;
			case "claim_offer":
				return "Get people to claim your offer";
				break;
			case "video_views":
				return "Get video views";
				break;
			default :
				return ctl_name;
				break;
		}
		
		
	}
	
	function validate_rows(){
		//returns true when all is ok!

		var ctl_name = "";
		var ctl_value = "";
		var ctl_value_length = 0;
		var has_active_item = false;
		var sum_all=0;
		
		$('#the_specs_list').children('a').each(function() {
			if ($(this).hasClass('list-group-item active'))
				if ($(this).attr('data-name'))
					{
						has_active_item=true;
						
						ctl_name = $(this).attr('data-name');
						ctl_value = $("[name=" + ctl_name +"]").val();
						ctl_value_length = $("[name=" + ctl_name +"]").val().length;
						ctl_warning_is_visible = $("#" + ctl_name + "_warning").is(":visible"); 
						
						if (ctl_value_length==0 || ctl_value==0 || ctl_value>100 || ctl_warning_is_visible)
						{
							alert("Please fill \r\n\r\n" + ctl_name2name(ctl_name));
							return false;
						}
						else 
							sum_all+= parseInt(ctl_value);
					}
					

		});
					
		if (has_active_item && (ctl_value_length==0 || ctl_value==0 || ctl_value>100 || ctl_warning_is_visible))
			return false;
		else
		{
			if (sum_all<100 )			
			{
				alert("The percentage is under 100%");
				return false;
			}
			else if (sum_all>100)			
			{
				alert("The percentage is over 100%");
				return false;
			}
			else 
				return true;
		}
		
	}
	
	
	function calculate_percentage_all(){
		$('#the_specs_list').children('a').each(function() {
			if ($(this).hasClass('list-group-item active'))
				if ($(this).attr('data-name'))
					{
						ctl_name = $(this).attr('data-name');
						ctl_value = $("[name=" + ctl_name +"]").val();
					
						if (ctl_value>0)
						{
							calculate_percentage(ctl_name);
						}
					}
					

		});
	}
	
	function calculate_percentage(ctl_name)
	{
		var months = $("#contract").val();
		var budget = $("#budget").val();
		var total_budget = months * budget;
		var percentage = $("[name=" + ctl_name + "]").val();
		var percentage2 = percentage / 100;
		var r = 0;
		
		r = total_budget * percentage2;
		
		////////////////////////////////////////////////////////////////
		var month_days = months * 30;
		var month_weeks =months * 4;
		var month_month =r/months;
		
		var daily = r/month_days;
		
		var weekly = r/month_weeks;
		
		if (daily>0){
			daily = parseFloat(parseFloat(daily).toFixed(2));
			weekly = parseFloat(parseFloat(weekly).toFixed(2));
			month_month = parseFloat(parseFloat(month_month).toFixed(2));
			r = parseFloat(parseFloat(r).toFixed(2));
			
			$("[name=" + ctl_name + "_cost]").val(r + "€");
			
			$("[name=" + ctl_name + "_d]").val(daily + "€");
			$("[name=" + ctl_name + "_w]").val(weekly + "€");
			$("[name=" + ctl_name + "_m]").val(month_month + "€");
			
			//error label when daily < 1
			var t = ctl_name + "_warning";
			
			
			if (daily<1)
				$("#" + t).show();
			else 
				$("#" + t).hide();
			//error label when daily < 1
				
				
			//$("[name=" + ctl_name + "_eta]").val("D : " + daily + "€ W : " + weekly + "€ M : " + month_month + "€");
			//$("[name=" + ctl_name + "_cost]").val(r + "€");
		}
		else 
		{
			$("#" + ctl_name + "_warning").hide();
			$("[name=" + ctl_name + "_cost]").val("");
			$("[name=" + ctl_name + "_d]").val("");
			$("[name=" + ctl_name + "_w]").val("");
			$("[name=" + ctl_name + "_m]").val("");
		}
	}
	
//	function get_money()
//	{
//		var ctl_name = "";
//		var ctl_value = "";
//		var ctl_value_length = 0;
//		var has_active_item = false;
//		
//		$('#the_specs_list').children('a').each(function() {
//			if ($(this).hasClass('list-group-item active'))
//				if ($(this).attr('data-name'))
//					{
//						has_active_item=true;
//						
//						ctl_name = $(this).attr('data-name');
//						ctl_value = $("[name=" + ctl_name +"]").val();
//						ctl_value_length = $("[name=" + ctl_name +"]").val().length;
//						
//						if (ctl_value_length==0 || ctl_value==0 || ctl_value>100)
//						{
//							alert("Please fill " + ctl_name);
//							return false;
//						}
//					}
//					
//
//		});
//					
//		if (has_active_item && (ctl_value_length==0 || ctl_value==0 || ctl_value>100))
//			return false;
//		else
//			return true;
//	}
	
	
	function submitform(saverecord)
	{
		if ($("#budget").val()=="151.1")
		{
			//going for postmanagement
				
		}
		else {
			if (!validate_rows())
			{
				return;
			}
		}

		
		if ($("#apps").val()>0)
		{
			if ($("#extra_budget").val().length==0 && $("#app_ad_budget").val().length==0)
			{
				alert ("Please fill 'Creation Budget' or 'App Ad Budget'")
				return ;
			}
			
		}
		
// 
//			console.log("a");
//		else 
//			console.log("b");
//		return;
//


		////////////////////////// validation
		var form = $("#offer_FORM");
		form.validate();

		if (!form.valid())
		{
			alert("Please fill the required fields!");
			return;
		}
		
		if ($("#budget").val()=="151.1")
		{
			//going for postmanagement
				
		}
		else 
		{
			if (!validate_one_month())
				return;
		}
		////////////////////////// validation
		
		
		
		//restore the element so exist in POST
		$('#seller_id').prop("disabled", false);

		
		if(saverecord)
		{//save rec + email here
				
			//validation for user modification
			if (change_after_check){
				alert("Please click 'Check' again!")
				return;
			}
			
			$("#save_rec").val("2");
			
			loading.appendTo(document.body);

			var postData = form.serializeArray();
			var formURL = form.attr("action");
			
			$.ajax(
				{
					url : formURL,
					type : "POST",
					data : postData,
					success : function(data, textStatus, jqXHR)
					{
						//lock back GUI
						$('#seller_id').prop("disabled", true);
						
						//loading.remove();

						var info = JSON.parse(data);
						
						
						var mailbody = "Αξιότιμε κ. " + $("[name=company_manager_name]").val() + ",<br><br>Σας επισυνάπτω το Link της προσφορά για τις υπηρεσίες μας στο Facebook.<br><br>";
						mailbody+= "<a href='http://localhost:8080/proposal/?j=" + info.guid + "' target='_blank'>Proposal</a><br><br>";
						mailbody+= "Ο κωδικός για να δείτε την προσφορά είναι: <b>" + info.guid_solution + "</b><br><br>"
						mailbody+="Η προσφορά θα είναι διαθέσιμη online για 10 ημέρες.<br><br>Είμαι στην διάθεσή σας για οποιαδήποτε διευκρίνηση χρειαστείτε.<br><br><br>";
						//mailbody+="<div dir='ltr'><div><span style='color:rgb(102,102,102)'><br></span></div><div><span style='color:rgb(102,102,102)'>With regards,</span></div><div><div><font color='#666666'>PipisCrew</font></div><div><i><font size='1' color='#666666'>CEO &amp; Founder</font></i></div></div><div><i><font size='1' color='#666666'><img src='https://lh4.googleusercontent.com/-qhw8okUHR1U/UzwX6pABRaI/AAAAAAAAAe0/K1JvbXpwufs/w415-h61-no/Signature.png' width='200' height='29' class='CToWUd'><br></font></i></div><div><div></div><div><span style='font-family:verdana,geneva;font-size:x-small'>16 Beaufort Court,&nbsp;</span><span style='font-family:verdana,geneva;font-size:x-small'>Canary Wharf</span></div><div><span style='font-family:verdana,geneva;font-size:x-small'>E14 9XL London, UK</span><br></div><div><font color='#666666'><b style='font-size:x-small'><a href='mailto:a.frontzos@pipiscrew.com' target='_blank'>Email us</a></b><br></font></div><div><div><font size='1' color='#666666'><b><a href='http://ww.facebook.com/pipiscrew' target='_blank'>Go To Facebook Page</a></b></font></div><div><font size='1' color='#666666'><b><a href='http://www.pipiscrew.com/' target='_blank'>Go To Website</a></b></font></div><div><font color='#666666'><b style='font-size:x-small'>UK:&nbsp;<a value='+442032390395'>+44 20 32 39 0395</a></b><font size='1'><b><br></b></font></font></div><div><font size='1' color='#666666'><b>Greece:&nbsp;<a value='+302155309484'>+30 215 530 9484</a></b></font></div></div></div><div><div><font color='#808080' face='Verdana, sans-serif'><span style='font-size:11px'>- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -&nbsp;</span></font></div><div><span style='color:gray;font-family:Verdana,sans-serif;font-size:8pt'>This e-mail and any attached files are confidential and may also be legally privileged.</span></div><div><span style='color:gray;font-family:Verdana,sans-serif;font-size:8pt'>They are intended solely for the intended addressee.</span><font color='#222222' face='Calibri, sans-serif'>&nbsp;</font><span style='color:gray;font-family:Verdana,sans-serif;font-size:8pt'>If you are not the addressee,</span></div><div><span style='color:gray;font-family:Verdana,sans-serif;font-size:8pt'>please e-mail it back to the sender and then immediately, permanently delete it.</span></div><div><span style='color:gray;font-family:Verdana,sans-serif;font-size:8pt'>Do not read, print, re-transmit, store or act in reliance on it.</span></div><div><p><span style='font-family:Calibri,sans-serif'></span></p><p><b><span style='font-size:8pt;font-family:Webdings;color:green'>P</span></b><span style='font-size:8pt;font-family:Webdings;color:green'>&nbsp;</span><span style='font-size:8pt;font-family:Verdana,sans-serif;color:gray'>Please consider the environment before printing this e-mail.</span></p></div></div></div>";
						mailbody+= "<span style='color:rgb(102,102,102)'>	With regards,<br><?=$_SESSION['u'];?><br><?=$_SESSION['u_sign'];?></span><br><font size='1' color='#666666'>	<img src='https://lh4.googleusercontent.com/-qhw8okUHR1U/UzwX6pABRaI/AAAAAAAAAe0/K1JvbXpwufs/w415-h61-no/Signature.png' width='200' height='29' class='CToWUd'><br></font><span style='font-family:verdana,geneva;font-size:x-small'>	16 Beaufort Court,<br>Canary Wharf<br>E14 9XL London, UK</span><br><b style='font-size:x-small'>	<a href='mailto:n.cookies@pipiscrew.com' target='_blank'>		Email us	</a></b><br><font size='1' color='#666666'>	<b>		<a href='http://ww.facebook.com/pipiscrew' target='_blank'>			Go To Facebook Page		</a>	</b></font><br><font size='1' color='#666666'>	<b>		<a href='http://www.pipiscrew.com/' target='_blank'>			Go To Website		</a>	</b></font><br><font color='#666666'>	<b style='font-size:x-small'>		UK:&nbsp;		<a value='+442032390395'>			+44 20 32 39 0395		</a>	</b></font><br><font size='1' color='#666666'>	<b>		Greece:&nbsp;		<a value='+302155309484'>			+30 215 530 9484		</a>	</b></font><br><font color='#808080' face='Verdana, sans-serif'>	<span style='font-size:11px'>		- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -&nbsp;	</span></font><br><span style='color:gray;font-family:Verdana,sans-serif;font-size:8pt'>	This e-mail and any attached files are confidential and may also be legally privileged.</span><br><span style='color:gray;font-family:Verdana,sans-serif;font-size:8pt'>	They are intended solely for the intended addressee.</span><font color='#222222' face='Calibri, sans-serif'>	&nbsp;</font><span style='color:gray;font-family:Verdana,sans-serif;font-size:8pt'>	If you are not the addressee,</span><br><span style='color:gray;font-family:Verdana,sans-serif;font-size:8pt'>	please e-mail it back to the sender and then immediately, permanently delete it.</span><br><span style='color:gray;font-family:Verdana,sans-serif;font-size:8pt'>	Do not read, print, re-transmit, store or act in reliance on it.</span><br><span style='font-size:8pt;font-family:Webdings;color:green'>	P</span>&nbsp;<span style='font-size:8pt;font-family:Verdana,sans-serif;color:gray'>	Please consider the environment before printing this e-mail.</span>";
						
						$("#mail_subject").val("pipiscrew Facebook Agency - " + $("[name=company_name]").val() + " - Proposal");
						$("#mail_recipient").val($("[name=email]").val());
						$("#mail_body").jqteVal(mailbody);
						$("#mail_offer_rec_id").val(info.last_id);
						
						$('#modaloMAIL').modal('toggle');
						
						window.open("http://localhost:8080/proposal/index.php?rec_guid=" + info.guid);

					},
					error : function(jqXHR, textStatus, errorThrown)
					{
						//lock back GUI
						$('#seller_id').prop("disabled", true);
						
						
						loading.remove();
						alert("ERROR");
					}
				});
				
		
//				loading.appendTo(document.body);
//
//				$("#save_rec").val("2");
//
//				console.log("step2");
//				document.offer_FORM.submit();
//
//				//lock back GUI
//				$('#seller_id').prop("disabled", true);
//
//				//3 sec
//				go_back_url_timer = setInterval(function(){myTimer()}, 5000);
	
		}
		else
		{
			$("#save_rec").val("1");
			
			loading.appendTo(document.body);

			var postData = form.serializeArray();
			var formURL = form.attr("action");
			$.ajax(
				{
					url : formURL,
					type : "POST",
					data : postData,
					success : function(data, textStatus, jqXHR)
					{
						//lock back GUI
						$('#seller_id').prop("disabled", true);
						
						loading.remove();

						var info = JSON.parse(data);
						
						if (info.error)
						{
							alert(info.error);
							return;
						}

						$("#infoRES").html(				"<font color=green><b>Your Estimation results based on your Facebook Page :</b></font>");

						var tbl = "<center><table style='width:400px' class='gridtable'>" + 
						"<tr><td>Total Likes</td><td>"+humanizeNumber(info.LIKES[0])+"</td><td>"+humanizeNumber(info.LIKES[1])+"</td></tr>" +
						"<tr><td>Reach</td><td>"+humanizeNumber(info.IMPRESSIONS[0])+"</td><td>"+humanizeNumber(info.IMPRESSIONS[1])+"</td></tr>" +
						"<tr><td>TAT</td><td>"+humanizeNumber(info.TAT[0])+"</td><td>"+humanizeNumber(info.TAT[1])+"</td></tr>" +
						"<tr><td>Website Clicks</td><td>"+humanizeNumber(info.WEBCLICKS[0])+"</td><td>"+humanizeNumber(info.WEBCLICKS[1])+"</td></tr>" +
						"<tr><td>App Impressions</td><td>"+humanizeNumber(info.APPIMPRESSIONS[0])+"</td><td>"+humanizeNumber(info.APPIMPRESSIONS[1])+"</td></tr>" +
						"<tr><td>Total Fees</td><td colspan='2'>"+humanizeNumber(info.T_FEES)+"</td></tr>" +
						"<tr><td>Total Ad Budget</td><td colspan='2'>"+humanizeNumber(info.T_AD_BUDGET)+"</td></tr>" +
						"<tr><td>Total Posting</td><td colspan='2'>"+humanizeNumber(info.T_POST)+"</td></tr>" +
						"<tr><td>App/Names/Rename</td><td colspan='2'>"+humanizeNumber(info.T_APPS)+"</td></tr>" +
						"<tr><td>Total Proposal</td><td colspan='2'>"+humanizeNumber(info.T_PROP)+"</td></tr>" +
						"</table></center>";
						
						$("#estimationhtml").html(tbl);
						
						//validation for user modification
						check_if_change_after_check=true;
						change_after_check=false;

					},
					error : function(jqXHR, textStatus, errorThrown)
					{
						//lock back GUI
						$('#seller_id').prop("disabled", true);
						
						
						loading.remove();
						alert("ERROR");
					}
				});

			
		}

	}

</script>
<!-- Main content -->
<section class="content">

<div class="row">
	<div class="col-xs-12">

		<div class="box">

<p style="background-color: #428bca;color:#fff;padding: 5px;margin-top: 10px" align=center>COMPANY DETAILS</p>

<form id="offer_FORM" name="offer_FORM" role="form" method="post" action="get_offer.php">

			<div class="row">
				<div class="col-md-4">
					<div class='form-group'>
						<label>
							Seller :
						</label>
						<select id="seller_id" name="seller_id" class='form-control'>
						</select>
					</div>
				</div>

				<div class="col-md-4">
					<div class="row">
						<div class="col-md-4">
							<div class='form-group'>

								<label>
									Type :
								</label>
								<select id="offer_type" name="offer_type" class='form-control'>
								</select>
							</div>
						</div>

						<div class="col-md-4">
							<div class='form-group'>
								<label>
									Country :
								</label>
								<select id="country" name="country" class='form-control'>
								</select>

							</div>
						</div>

						<div class="col-md-3">
							<div class='form-group'>

								<label>
									English :
								</label><br>
								<input type="checkbox" class='form-control' id="doc_english" name="doc_english">
							</div>
						</div>
					</div>

				</div>

				<div class="col-md-2">
					<div class='form-group'>

						<label>
							Proposal Date :
						</label>
						<input type="text" class="form-control" id="proposaldate" name="proposaldate" data-date-format="dd-mm-yyyy" style="width:100px" >
					</div>
				</div>

				<div class="col-md-2">
					<div class='form-group'>

						<label>
							Proposal Valid Till :
						</label>
						<input type="text" class="form-control" id="proposaldatetill" name="proposaldatetill" data-date-format="dd-mm-yyyy" style="width:100px" >
					</div>
				</div>

			</div>





			<div class="row">

				<div class="col-md-4">
					<div class='form-group'>
						<label>
							Company Name :
						</label>
						<input maxlength="200" name='company_name' class='form-control' placeholder='Company Name'>
					</div>
				</div>

				<div class="col-md-4">
					<div class='form-group'>
						<label>
							Company Manager Name :
						</label>
						<input maxlength="100" name='company_manager_name' class='form-control' placeholder='Manager Name'>
					</div>
				</div>
				
				<div class="col-md-4">
					<div class='form-group'>
						<label>
							Likes :
						</label>
						<input maxlength="100" name='unknown_likes' class='form-control' placeholder='Likes'>
					
						<label>
							TAT :
						</label>
						<input maxlength="100" name='unknown_tat' class='form-control' placeholder='TAT'>
					</div>
				</div>

				
			</div>




			<div class="row">
				<div class="col-md-4">

					<div class='form-group'>
						<label>
							City :
						</label>

						<input id="city" name="city" type="text" class='form-control' placeholder='City'>
					</div>
				</div>

				<div class="col-md-4">
					<div class='form-group'>
						<label>
							Email :
						</label>
						<input name='email' type="email" class='form-control' placeholder='Email'>
					</div>
				</div>

				<div class="col-md-4">
					<div class='form-group'>
						<label>
							Telephone :
						</label>
						<input name='telephone' class='form-control' placeholder='Telephone'>
					</div>
				</div>
			</div>


<p style="background-color: #428bca;color:#fff;padding: 5px;" align=center>APPS</p>




			<div class="form-inline">
				<div class="bg-primary" style="padding: 10px;">

					<div class='form-group'>
						<label>
							Apps :
						</label>
						<select id="apps" name="apps" class='form-control' style="width:120px;margin-right: 40px">
							<option>
								0
							</option>
							<option>
								1
							</option>
							<option>
								2
							</option>
							<option>
								3
							</option>
						</select>

						<label>
							Creation Budget :
						</label>
						<input id="extra_budget" name="extra_budget" class="form-control" style="width:120px;margin-right: 40px" placeholder="Creation Budget">

						<label>
							App Ad Budget :
						</label>
						<input id="app_ad_budget" name="app_ad_budget" class="form-control" style="width:120px;margin-right: 40px" placeholder="App Ad Budget">
						
					</div>
				</div>
			</div>
			
			<div id="app_resDIV" class="row" style="display:none;">
				<div class="col-md-3">
					<span id="app_impressions" style="display:block;font-size:15px;height:25px" class="label label-primary">
					</span>
				</div>
			</div>
			<br><br>
			
<p style="background-color: #428bca;color:#fff;padding: 5px;" align=center>OPTIMIZATION</p>

			<div class="row">
				<div class="col-md-2">
					<div class='form-group'>
						<label>
							Facebook Page Type :
						</label>

						<select class='form-control' id="is_new_offer">
							<option value="0">
								New Page
							</option>
							<option value="1">
								Existing Page
							</option>
						</select>
					</div>
				</div>



				<div class="col-md-2">
					<div class='form-group'>
						<label>
							Category :
						</label>
						<select id="category" name="category" class='form-control'>
						</select>
					</div>
				</div>

				<div class="col-md-2">
					<div class='form-group'>
						<label>
							Contract Period :
						</label>
						<select id="contract" name="contract" class='form-control'>
							<option value="1">
								1 Month
							</option>
							<option value="2">
								2 Month
							</option>
							<option value="3">
								3 Month
							</option>
							<option value="6">
								6 Month
							</option>
							<option value="9">
								9 Month
							</option>
							<option value="12">
								12 Month
							</option>
						</select>
					</div>
				</div>

				<div class="col-md-2">
					<div class='form-group'>
						<label>
							Posting Management (per week) :
						</label>
						<select id="post_manage" name="post_manage" class='form-control'>
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
				
				<div class="col-md-2">
					<div class='form-group'>
						<label>
							Language :
						</label>
						<select id="language" name="language" class='form-control'>
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
				
				<div class="col-md-2">
					<div class='form-group'>
						<label>
							Graphics :
						</label>
						<input type="checkbox" id="graphics_sw" name="graphics_sw">
					</div>
				</div>
			</div>

			<div id="is_new_offer_hide" class='form-group' style="display: none;">
				<label>
					Facebook Page Handle :
				</label>
				<div class="row">
					<div class="col-md-4">
							<div class="input-group">
								<div class="input-group-addon">
									facebook.com/
								</div>
								<input maxlength="100" name='page_url' class='form-control' placeholder='pipiscrew'>
							</div>
					</div>
					<div class="col-md-4">
						<select id="fb_pages" name='fb_pages' class='form-control'></select>
					</div>
				</div>
			</div>

			<div class="form-inline">
				<!--<label style="color: blue">-->
				<label style="background-color: #428bca;color:#fff;padding: 5px;font-weight: normal;" >
					Manage :
				</label>
				<div class="bg-primary" style="padding: 10px;">

					<div class='form-group'>

						<label >
							Page Merge :
						</label>
						<input type="checkbox" id="page_merge" name="page_merge">

						<label  style="margin-left: 40px">
							Name Change :
						</label>
						<input type="checkbox" id="name_change" name="name_change">

					</div>
				</div>
			</div>

			<br>
			<div id="page_mergeDIV" class="form-inline" style="display: none;margin-bottom: 10px">
				<div class="row">
					<div class="col-md-4">
						<div class='form-group'>
							<label style="width:120px;">
								Page 1 :
							</label>
							<input id="page_one" name="page_one" type="text" class='form-control' placeholder='page one' style="width:320px;">
						</div>
					</div>

					<div class="col-md-4">
						<div class='form-group'>
							<label style="width:80px;">
								Page 2 :
							</label>
							<input id="page_two" name="page_two" type="text" class='form-control' placeholder='page two' style="width:300px;">
						</div>
					</div>
				</div>
			</div>

			<div id="name_changeDIV" class="form-inline" style="display: none;">
				<div class="row">
					<div class="col-md-4">

						<div class='form-group'>
							<label style="width:120px;">
								Old Page Name :
							</label>
							<input id="old_page_name" name="old_page_name" type="text" class='form-control' placeholder='old_page_name' style="width:320px;">
						</div>
					</div>

					<div class="col-md-4">

						<div class='form-group'>
							<label style="width:80px;">
								Old URL :
							</label>


							<input id="old_url" name="old_url" type="text" class='form-control' placeholder='old_url' style="width:300px;">
						</div>
					</div>
				</div>

				<br>

				<div class="row">
					<div class="col-md-4">
						<div class='form-group'>
							<label style="width:120px;">
								New Page Name :
							</label>
							<input id="new_page_name" name="new_page_name" type="text" class='form-control' placeholder='new_page_name' style="width:320px;">

						</div>
					</div>

					<div class="col-md-4">
						<div class='form-group'>
							<label style="width:80px;">
								New URL :
							</label>
							<input id="new_url" name="new_url" type="text" class='form-control' placeholder='new_url' style="width:300px;">
						</div>
					</div> 
				</div>


			</div>



			<div class='form-group' style="width:200px;padding-top: 5px">
				<label>
					Budget (per month, use 151.1) :
				</label>
				<input  id="budget" name="budget" class='form-control' placeholder='Budget'>
			</div>
			
			<div class='form-group' style="width:200px;padding-top: 5px">
				<label>
					Discount :
				</label>
				<input  id="discount" maxlength="3"  name="discount" class='form-control' placeholder='%'>
			</div>
			

			<br>


	<div class="row">
		<div class="col-md-4">
			<div class='form-group'>
				<label>
					Adverts Platform :
				</label>
				<select id="ad_type" name="ad_type" class='form-control'>
					<option value="1">
						All Facebook MarketPlace Ads (NewsFeed / Right Column / Mobile)
					</option>
					<option value="2">
						NewsFeed Ads
					</option>
					<option value="3">
						Right Column Ads
					</option>
					<option value="4">
						Mobile Ads
					</option>
					<option value="5">
						Atlas-Website Banners
					</option>
				</select>
			</div>
		</div>


		<div class="col-md-4">
			<div class='form-group'>
				<label>
					Comment (* This comment appears on proposal) :
				</label>
				<textarea rows="2" name="comment" class='form-control' placeholder="Comment" style="resize: none;"></textarea>
			</div>
				</div>

<div class="col-md-4" style="margin-top: 25px">
			<div class='form-group'>
				<a href="javascript: submitform()" class="btn btn-primary btn-lg">
					Check
				</a>
				<!--				<button class="btn btn-primary btn-lg" type="submit" name="submit">
				Check
				</button>-->



			</div>
		</div>
			</div>
	

    <div class="row">
        <div class="col-md-4">
        
			<div  id="the_specs_list" class="list-group centre">
				<a class='list-group-item active2'>Campaign objectives : </a>
				<a href='#' class='list-group-item' data-name='send_ppl_website'><img src="img/row1.png">&nbsp;&nbsp;Send people to your website</a>
				<a href='#' class='list-group-item' data-name='increase_conversions'><img src="img/row2.png">&nbsp;&nbsp;Increase conversions on your website</a>
				<a href='#' class='list-group-item' data-name='boost_posts'><img src="img/row3.png">&nbsp;&nbsp;Boost your posts</a>
				<a href='#' class='list-group-item' data-name='promote_page_likes'><img src="img/row4.png">&nbsp;&nbsp;Promote your Page</a>
				<a href='#' class='list-group-item' data-name='get_installs_app'><img src="img/row5.png">&nbsp;&nbsp;Get installs of your app</a>
				<a href='#' class='list-group-item' data-name='increase_engag'><img src="img/row6.png">&nbsp;&nbsp;Increase engagement in your app</a>
				<a href='#' class='list-group-item' data-name='raise_attendance'><img src="img/row7.png">&nbsp;&nbsp;Raise attendance at your event</a>
				<a href='#' class='list-group-item' data-name='claim_offer'><img src="img/row8.png">&nbsp;&nbsp;Get people to claim your offer</a>
				<a href='#' class='list-group-item' data-name='video_views'><img src="img/row9.png">&nbsp;&nbsp;Get video views</a>
			</div>
		</div>
		

			<div id="the_specs_list2" class="col-md-4 list-group" >
				<a class='list-group-item active2'>Percentage :&emsp;Cost&emsp;&emsp;&emsp;&emsp;&emsp;Daily&emsp;&emsp;&emsp;&emsp;&emsp;Weekly&emsp;&emsp;&emsp;&emsp;&emsp;Monthly</a>
			    <div class="row" id="send_ppl_website_row" style="height:50px;visibility: hidden;">
					<input style="display: inline-block;margin-left:15px;margin-right:15px;" class='list-group-item'   name="send_ppl_website" type="number" min="0" max="100" step="10"  placeholder="100%">
					<input style="display: inline-block;width:100px;" class='list-group-item'  name="send_ppl_website_cost" readonly>
					<input style="display: inline-block;width:100px;" class='list-group-item'  name="send_ppl_website_d"  readonly>
					<input style="display: inline-block;width:100px;" class='list-group-item'  name="send_ppl_website_w"  readonly>
					<input style="display: inline-block;width:100px;" class='list-group-item'  name="send_ppl_website_m"  readonly>
					<span id="send_ppl_website_warning" title="Daily rate can not be lower than 1" class="glyphicon glyphicon-exclamation-sign" style="margin-left:10px;color:red;display:none;"></span>
				</div>

<style>
	 a.list-group-item.active2,a.list-group-item.active2:hover {
	 	background-color: #5EBB5E;
	 	color: #fff;
	 }
</style>
			    <div class="row" id="increase_conversions_row" style="height:50px;visibility: hidden;">
					<input style="display: inline-block;margin-left:15px;margin-right:15px;" class='list-group-item'   name="increase_conversions" type="number" min="0" max="100" step="10"  placeholder="100%">
					<input style="display: inline-block;width:100px;" class='list-group-item'  name="increase_conversions_cost" readonly>
					<input style="display: inline-block;width:100px;" class='list-group-item'  name="increase_conversions_d"  readonly>
					<input style="display: inline-block;width:100px;" class='list-group-item'  name="increase_conversions_w"  readonly>
					<input style="display: inline-block;width:100px;" class='list-group-item'  name="increase_conversions_m"  readonly>
					<span id="increase_conversions_warning" title="Daily rate can not be lower than 1" class="glyphicon glyphicon-exclamation-sign" style="margin-left:10px;color:red;display:none;"></span>
				</div>
				

			    <div class="row" id="boost_posts_row" style="height:50px;visibility: hidden;">
					<input style="display: inline-block;margin-left:15px;margin-right:15px;" class='list-group-item'   name="boost_posts" type="number" min="0" max="100" step="10"  placeholder="100%">
					<input style="display: inline-block;width:100px;" class='list-group-item'  name="boost_posts_cost" readonly>
					<input style="display: inline-block;width:100px;" class='list-group-item'  name="boost_posts_d"  readonly>
					<input style="display: inline-block;width:100px;" class='list-group-item'  name="boost_posts_w"  readonly>
					<input style="display: inline-block;width:100px;" class='list-group-item'  name="boost_posts_m"  readonly>
					<span id="boost_posts_warning" title="Daily rate can not be lower than 1" class="glyphicon glyphicon-exclamation-sign" style="margin-left:10px;color:red;display:none;"></span>
				</div>
				
			    <div class="row" id="promote_page_likes_row" style="height:50px;visibility: hidden;">
					<input style="display: inline-block;margin-left:15px;margin-right:15px;" class='list-group-item'   name="promote_page_likes" type="number" min="0" max="100" step="10"  placeholder="100%">
					<input style="display: inline-block;width:100px;" class='list-group-item'  name="promote_page_likes_cost" readonly>
					<input style="display: inline-block;width:100px;" class='list-group-item'  name="promote_page_likes_d"  readonly>
					<input style="display: inline-block;width:100px;" class='list-group-item'  name="promote_page_likes_w"  readonly>
					<input style="display: inline-block;width:100px;" class='list-group-item'  name="promote_page_likes_m"  readonly>
					<span id="promote_page_likes_warning" title="Daily rate can not be lower than 1" class="glyphicon glyphicon-exclamation-sign" style="margin-left:10px;color:red;display:none;"></span>
				</div>
				
			    <div class="row" id="get_installs_app_row" style="height:50px;visibility: hidden;">
					<input style="display: inline-block;margin-left:15px;margin-right:15px;" class='list-group-item'   name="get_installs_app" type="number" min="0" max="100" step="10"  placeholder="100%">
					<input style="display: inline-block;width:100px;" class='list-group-item'  name="get_installs_app_cost" readonly>
					<input style="display: inline-block;width:100px;" class='list-group-item'  name="get_installs_app_d"  readonly>
					<input style="display: inline-block;width:100px;" class='list-group-item'  name="get_installs_app_w"  readonly>
					<input style="display: inline-block;width:100px;" class='list-group-item'  name="get_installs_app_m"  readonly>
					<span id="get_installs_app_warning" title="Daily rate can not be lower than 1" class="glyphicon glyphicon-exclamation-sign" style="margin-left:10px;color:red;display:none;"></span>
				</div>
				
			    <div class="row" id="increase_engag_row" style="height:50px;visibility: hidden;">
					<input style="display: inline-block;margin-left:15px;margin-right:15px;" class='list-group-item'   name="increase_engag" type="number" min="0" max="100" step="10"  placeholder="100%">
					<input style="display: inline-block;width:100px;" class='list-group-item'  name="increase_engag_cost" readonly>
					<input style="display: inline-block;width:100px;" class='list-group-item'  name="increase_engag_d"  readonly>
					<input style="display: inline-block;width:100px;" class='list-group-item'  name="increase_engag_w"  readonly>
					<input style="display: inline-block;width:100px;" class='list-group-item'  name="increase_engag_m"  readonly>
					<span id="increase_engag_warning" title="Daily rate can not be lower than 1" class="glyphicon glyphicon-exclamation-sign" style="margin-left:10px;color:red;display:none;"></span>
				</div>
				
			    <div class="row" id="raise_attendance_row" style="height:50px;visibility: hidden;">
					<input style="display: inline-block;margin-left:15px;margin-right:15px;" class='list-group-item'   name="raise_attendance" type="number" min="0" max="100" step="10"  placeholder="100%">
					<input style="display: inline-block;width:100px;" class='list-group-item'  name="raise_attendance_cost" readonly>
					<input style="display: inline-block;width:100px;" class='list-group-item'  name="raise_attendance_d"  readonly>
					<input style="display: inline-block;width:100px;" class='list-group-item'  name="raise_attendance_w"  readonly>
					<input style="display: inline-block;width:100px;" class='list-group-item'  name="raise_attendance_m"  readonly>
					<span id="raise_attendance_warning" title="Daily rate can not be lower than 1" class="glyphicon glyphicon-exclamation-sign" style="margin-left:10px;color:red;display:none;"></span>
				</div>
				
			    <div class="row" id="claim_offer_row" style="height:50px;visibility: hidden;">
					<input style="display: inline-block;margin-left:15px;margin-right:15px;" class='list-group-item'   name="claim_offer" type="number" min="0" max="100" step="10"  placeholder="100%">
					<input style="display: inline-block;width:100px;" class='list-group-item'  name="claim_offer_cost" readonly>
					<input style="display: inline-block;width:100px;" class='list-group-item'  name="claim_offer_d"  readonly>
					<input style="display: inline-block;width:100px;" class='list-group-item'  name="claim_offer_w"  readonly>
					<input style="display: inline-block;width:100px;" class='list-group-item'  name="claim_offer_m"  readonly>
					<span id="claim_offer_warning" title="Daily rate can not be lower than 1" class="glyphicon glyphicon-exclamation-sign" style="margin-left:10px;color:red;display:none;"></span>
				</div>
				
			    <div class="row" id="video_views_row" style="height:50px;visibility: hidden;">
					<input style="display: inline-block;margin-left:15px;margin-right:15px;" class='list-group-item'   name="video_views" type="number" min="0" max="100" step="10"  placeholder="100%">
					<input style="display: inline-block;width:100px;" class='list-group-item'  name="video_views_cost" readonly>
					<input style="display: inline-block;width:100px;" class='list-group-item'  name="video_views_d"  readonly>
					<input style="display: inline-block;width:100px;" class='list-group-item'  name="video_views_w"  readonly>
					<input style="display: inline-block;width:100px;" class='list-group-item'  name="video_views_m"  readonly>
					<span id="video_views_warning" title="Daily rate can not be lower than 1" class="glyphicon glyphicon-exclamation-sign" style="margin-left:10px;color:red;display:none;"></span>
				</div>
			</div>
</div>

<input name="cust_id" id="cust_id" class="form-control" style="display:none;">
<input name="cust_code" id="cust_code" class="form-control" style="display:none;">

<!--
1 = View only
2 = DOCx
-->
		<input id="save_rec" name="save_rec" class="form-control" style="display:none;">

			</form>
<br><br>
<p style="background-color: #428bca;color:#fff;padding: 5px;" align=center>ESTIMATION RESULT</p>

						<div id="estimationhtml">
						</div>
						
			<!--background-color:#ff0000; -->
<!--			<div id="resDIV" style="display:none; width: intrinsic;" >-->

				<!--<h4>-->
			<!--<div id="resDIV" class="row" style="display:none;">

				<div id="infoRES">
				</div>

				<div class="col-md-3">
					<span id="TAT" style="display:block;font-size:15px;height:25px" class="label label-primary">
					</span>
				</div>

				<div class="col-md-3">
					<span id="impressions" style="display:block;font-size:15px;height:25px" class="label label-primary">
					</span>
				</div>

				<div class="col-md-3">
					<span id="likes" style="display:block;font-size:15px;height:25px" class="label label-primary">
					</span>
				</div>

				<div class="col-md-3">
					<span id="total_cost" style="display:block;font-size:15px;height:25px"  class="label label-primary">
					</span>
				</div>
&nbsp;
&nbsp;
				<center>
				<a href="javascript: submitform(true)" class="btn btn-success btn-lg">
					Save Information
				</a>
				</center>
				
			</div>-->
			
&nbsp;
&nbsp;
				<center>
				<a href="javascript: submitform(true)" class="btn btn-success btn-lg">
					Save Information
				</a>
				</center>
			&nbsp;
		</div> <!-- box -->
	</div> <!--col-xs-12-->
</div> <!-- row -->

<!-- MAIL MODAL [START] -->
<div class="modal fade bs-modal-lg" id="modaloMAIL" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
					&times;
				</button>
				<h4 class="modal-title" id='lblTitle_oMAIL'>Send Proposal</h4>
			</div>
			<div class="modal-body">
				<p style="background-color: #428bca;color:#fff;padding: 5px;" align=center>email will be send by proposal@watetron.com with <b>reply</b> property to <b><?=$_SESSION['reply_mail'];?></b></p>				
				
				<form id="formoMAIL" role="form" method="post" action="tab_proposal_send_mail.php">

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



<?php
include ('template_bottom.php');
?>