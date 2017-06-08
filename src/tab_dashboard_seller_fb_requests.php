<?php
require_once ('template_top.php');

// include DB
require_once ('config.php');

$db       = connect();

$user_id = $_SESSION['id'];

if ($_SESSION['level']==9 && isset($_POST["user_id"]))
	$user_id=$_POST["user_id"];

	
$rows=null;

$rows = getSet($db, "select 
fb_request_proposal_id, company_name, manager_name, email, telephone,
town, facebook, ad_budget, likes_switch, post_engag_switch, conv_eshop_switch,
website_clicks_switch, app_switch, content_manage_switch, users.fullname as user_id, ifnull(comments,'') as comments, DATE_FORMAT(daterec,'%d-%m-%Y') as daterec 
from fb_request_proposals 
left join users on users.user_id=fb_request_proposals.user_id 
where fb_request_proposals.user_id=?  
order by daterec DESC", array($user_id));

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
					 	rows += "<tr><td></td><td>" + jArray_rows[i]["fb_request_proposal_id"] + "</td><td>" + jArray_rows[i]["company_name"] + "</td>" +
					 	"<td>" + jArray_rows[i]["manager_name"] + "</td><td>" + jArray_rows[i]["email"] + "</td>" +
					 	"<td>" + jArray_rows[i]["telephone"] + "</td><td>" + jArray_rows[i]["town"] + "</td><td>" + jArray_rows[i]["facebook"] + "</td><td>" + jArray_rows[i]["ad_budget"] + "</td>" +
					 	"<td>" + jArray_rows[i]["likes_switch"] + "</td><td>" + jArray_rows[i]["post_engag_switch"] + "</td>" +
					 	"<td>" + jArray_rows[i]["conv_eshop_switch"] + "</td><td>" + jArray_rows[i]["website_clicks_switch"] + "</td>" +
					 	"<td>" + jArray_rows[i]["app_switch"] + "</td><td>" + jArray_rows[i]["content_manage_switch"] + "</td>" +
					 	"<td>" + jArray_rows[i]["user_id"] + "</td><td>" + jArray_rows[i]["comments"] + "</td><td>" + jArray_rows[i]["daterec"] + "</td></tr>";
					 }
					 
					 $("#fb_request_proposals_rows").html(rows);
					
					 //convert2magic!
					 $("#fb_request_proposals_tbl").bootstrapTable();
					 

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
		
	    function statFormatter(value, row) {
	    	
	    	var icon_stat = '<span class="glyphicon glyphicon-remove">';
			
			if (value != null && value == 1)
			{
				 icon_stat = '<span class="glyphicon glyphicon-ok">';
			}
			
			return icon_stat;
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
		Facebook Proposal Requests (<?= date("d-m-Y"); ?>)
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

			<table id="fb_request_proposals_tbl"
	           data-striped=true
	           data-click-to-select="true" data-single-select="true"
	           data-page-size="50"
	           data-side-pagination="server">

				<thead>
					<tr>
						<th data-field="state" data-checkbox="true" >
						</th>

						<th data-field="fb_request_proposal_id" data-visible="false">
							fb_request_proposal_id
						</th>
						
						<th data-field="company_name" data-sortable="true">
							company_name
						</th>
						
						<th data-field="manager_name" data-sortable="true">
							manager_name
						</th>
						
						<th data-field="email" data-sortable="true">
							email
						</th>
						
						<th data-field="telephone" data-sortable="true">
							telephone
						</th>
						
						<th data-field="town" data-sortable="true">
							town
						</th>
						
						<th data-field="facebook" data-sortable="true">
							facebook
						</th>
						
						<th data-field="ad_budget" data-sortable="true">
							ad budget
						</th>
						
						<th data-field="likes_switch" data-formatter="statFormatter" data-sortable="true">
							likes
						</th>
						
						<th data-field="post_engag_switch" data-formatter="statFormatter" data-sortable="true">
							post engagement
						</th>
						
						<th data-field="conv_eshop_switch" data-formatter="statFormatter" data-sortable="true">
							conversion eshop
						</th>
						
						<th data-field="website_clicks_switch" data-formatter="statFormatter" data-sortable="true">
							website clicks
						</th>
						
						<th data-field="app_switch" data-formatter="statFormatter" data-sortable="true">
							app
						</th>
						
						<th data-field="content_manage_switch" data-formatter="statFormatter" data-sortable="true">
							content manage
						</th>
						
						<th data-field="user_id" data-sortable="true">
							user
						</th>
						
						<th data-field="comments" data-sortable="true">
							comments
						</th>
						
						<th data-field="daterec" data-sortable="true">
							daterec
						</th>
						
					</tr>
				</thead>
				<tbody id="fb_request_proposals_rows"></tbody>
			</table	>
				</div>


<?php
include ('template_bottom.php');
?>