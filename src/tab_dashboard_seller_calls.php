<?php
require_once ('template_top.php');

// include DB
require_once ('config.php');

$db       = connect();

$user_id = $_SESSION['id'];

if ($_SESSION['level']==9 && isset($_POST["user_id"]))
	$user_id=$_POST["user_id"];

	
$rows=null;

$rows = getSet($db, "select client_calls.client_id,client_call_id, DATE_FORMAT(client_call_datetime,'%d-%m-%Y %H:%i') as client_call_datetime, DATE_FORMAT(client_call_next_call,'%d-%m-%Y %H:%i') as client_call_next_call,is_lead , client_name as company_name, client_calls.client_id,manager_name,telephone, mobile, 
(select offer_id from offers where company_id=client_calls.client_id order by offer_date_rec DESC limit 1) as proposal, 
client_call_discussion,  client_calls.comment,
(select CONCAT('rec_guid=',rec_guid) from offers where offer_id = proposal LIMIT 1) as url
from client_calls 
left join clients on clients.client_id=client_calls.client_id
left join users on users.user_id=clients.owner

where users.user_id=? and client_call_next_call between '".date("Y-m-d")." 00:00' and '".date("Y-m-d")." 23:59' 
order by client_call_next_call DESC", array($user_id));

$users_rows = getSet($db,"SELECT * FROM `users` order by user_level_id",null);

?>

<script>
    $(function() {
    	
		<?php if ($_SESSION['level']==9) { ?>
					$('#user_id').on('change', function() {
						if (this.value > 0 )
					  		document.forms["frmQ"].submit();
					});
		<?php } ?>
			
					///////////////////////////////////////////////////////////// FILL users
					var jArray_users =   <?php echo json_encode($users_rows); ?>;

					var combo_users_rows = "<option value='0'></option>";
					for (var i = 0; i < jArray_users.length; i++)
					{
						combo_users_rows += "<option value='" + jArray_users[i]["user_id"] + "'>" + jArray_users[i]["fullname"] + "</option>";
					}

					$("[name=user_id]").html(combo_users_rows);
					$("[name=user_id]").change(); //select row 0 - no conflict on POST validation @ PHP
					///////////////////////////////////////////////////////////// FILL users
					
					
					
					 ///////////////////////////////////////////////////////////// FILL Contracts grid
					 var jArray_rows =   <?php echo json_encode($rows); ?>;

					 var rows = "";
					 for (var i = 0; i < jArray_rows.length; i++)
					 {
					 	rows += "<tr><td></td><td>" + jArray_rows[i]["client_id"] + "</td><td>" + jArray_rows[i]["client_call_datetime"] + "</td>" +
					 	"<td>" + jArray_rows[i]["is_lead"] + "</td><td>" + jArray_rows[i]["company_name"] + "</td>" +
					 	"<td>" + jArray_rows[i]["manager_name"] + "</td><td>" + jArray_rows[i]["telephone"] + "</td><td>" + jArray_rows[i]["mobile"] + "</td><td>" + jArray_rows[i]["url"] + "</td>" +
					 	"<td>" + jArray_rows[i]["client_call_discussion"] + "</td><td>" + jArray_rows[i]["comment"] + "</td></tr>";
					 }
					 
					 $("#calls_rows").html(rows);
					
					 //convert2magic!
					 $("#calls_tbl").bootstrapTable();
					 

		}); //jQuery ends 
	
	    function leadFormatter(value, row) {
	        var icon = value == 0 ? 'glyphicon-star' : 'glyphicon-star-empty'

			var g = value == 0 ? "Client" : value == 1 ? "Lead" : "Inactive Client";
			
	        return '<i class="glyphicon ' + icon + '"></i> ' + g;
    	}	
    
	    function proposalFormatter(value, row) {
	    	
			if (value && value!="null")
			{
				var s = "<center><a href='http://localhost:8080/proposal/index.php?" + value + "' target='_blank'>View</a></center>";
				return s;
			}
			else 
				return "";
		}
		
		function companyFormatter(value, row) {
			var s ="";
			
			if (row.col_lead=="0")
			{
				s= "tab_clients_details.php?id=" + row.id;
			}
			else if (row.col_lead=="1"){
				s= "tab_leads_details.php?id=" + row.id;
			}
			else {
				s= "tab_inclients_details.php?id=" + row.company_id;
			} 
			
			return value + "&nbsp;&nbsp;<a style='float:right' href='" + s + "' target='_blank'>View Details</a>";
		}
    
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
	<h1>
		Calls To Do (<?= date("d-m-Y"); ?>)
	</h1>

</section>

<!-- Main content -->
<section class="content">
<?php if ($_SESSION['level']==9){ ?>
<form name="frmQ" method="post" action="" >
	Filter by User <select name="user_id" id="user_id"></select>
</form>
<?php } ?>
<br>

				<div class="row">					
					<table id="calls_tbl"
			           data-striped=true data-click-to-select="true" data-single-select="true">
						<thead>
							<tr>
								<th data-field="state" data-checkbox="true" ></th>
								<th data-field="id" data-visible="false">ID</th> 
								<th data-field="col_lastcall" data-sortable="true">Last Call</th>
								<th data-field="col_lead" data-formatter="leadFormatter" data-sortable="true">Type</th>
								<th data-field="col_name" data-formatter="companyFormatter" data-sortable="true">Company Name</th>
								<th data-field="col_mname" data-sortable="true">Manager Name</th>
								<th data-field="col_tel" data-sortable="true">Telephone</th>
								<th data-field="col_mob" data-sortable="true">Mobile</th>
								<th data-field="col_proposal" data-formatter="proposalFormatter" data-sortable="true">Proposal</th>
								<th data-field="col_discuss" data-sortable="true">Last Discussion</th>
								<th data-field="col_comment" data-sortable="true">Comment</th>
							</tr>
						</thead>

						<tbody id="calls_rows"></tbody>
					</table>
				</div>


<?php
include ('template_bottom.php');
?>