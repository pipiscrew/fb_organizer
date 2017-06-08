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

if ($_SESSION['level']==9)
	$find_sql = "SELECT * FROM `users` order by user_level_id";
else 
	$find_sql = "SELECT * FROM `users` where user_id=".$_SESSION['id'];
	
$users_rows = getSet($db,$find_sql,null);

?>

    <script type="text/javascript">
        $(function() {
        
<?php if ($_SESSION['level']==9) { ?>
			$('#user_id').on('change', function() {
			  $('#leads_tbl').bootstrapTable('refresh');
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
			
        });
        
				//bootstrap-table
				function queryParamsLEADS(params)
				{
					var q = {
						"limit": params.limit,
						"offset": params.offset,
						"search": params.search,
						"name": params.sort,
						"user_id" : $("#user_id").val(),
						"order": params.order
					};
 
					return q;
				}
				
				function actionsFormatter(value, row) {
					var s ="";
					
					var edit = "<a href='tab_leads_details.php?id=" + row.client_id + "' class='btn btn-primary btn-xs'>Edit</a>";
					
					<?php if ($_SESSION["level"] == 9) { ?>
						var del = "<a href='tab_leads_delete.php?id=" + row.client_id + "' onclick='return confirm('Delete row, are you sure?')' style='margin-left:7px' class='btn btn-danger btn-xs'>Delete</a>";	
					<?php } else { ?>
						var del ="";
					<?php } ?>


					return edit + del;			
					
				}
				
    </script>
    
<!-- Content Header (Page header) -->
<section class="content-header">
<?php if (!isset($_GET["isnew"]) && !isset($_GET["isupdate"]) && !isset($_GET["isdelete"]) && !isset($_GET["iserror"])) { ?>
	<h1>
		Leads
	</h1>
<?php } else { ?>
<br>
<div class="alert alert-<?php echo isset($_GET["iserror"])? "danger":"success"; ?> alert-dismissable">
                <i class="glyphicon glyphicon-<?php echo isset($_GET["iserror"])? "remove":"ok"; ?>"></i>
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>
                <?php 
                
if (isset($_GET["isnew"]))
               echo "Lead Inserted!";
elseif (isset($_GET["isupdate"]))
               echo "Lead Edited!";
elseif (isset($_GET["isdelete"]))
               echo "Lead Deleted!";
elseif (isset($_GET["iserror"]))
               echo "Error Occurred!"; 
               ?>
               
               </div>
               <?php
               }
               ?>

</section>

<!-- Main content -->
<section class="content">
<?php if ($_SESSION['level']==9) { ?>
			<select id="user_id" name='user_id' class='form-control'>
			</select>
<?php } ?>
					
			<table id="leads_tbl"
	           data-toggle="table"
	           data-striped=true
	           data-url="tab_leads_pagination.php"
	           data-search="true"
	           data-show-refresh="true"
	           data-pagination="true"
	           data-page-size="50"
	           data-sort-name="next_call"
	           data-sort-order="desc"
	           data-side-pagination="server"
	           data-query-params="queryParamsLEADS">

				<thead>
					<tr>

						<th data-field="client_id" data-visible="false">
							id
						</th>
						
						<th data-field="client_code" data-sortable="true">
							Code
						</th>
						
						<th data-field="next_call" data-sortable="true">
							Next Call
						</th>
						
						<th data-field="client_name" data-sortable="true">
							Name
						</th>
						<th data-field="client_sector_id" data-sortable="true">
							Sector
						</th>
						<th data-field="client_sector_sub_id" data-sortable="true">
							Sub Sector
						</th>
						
						<th data-field="client_rating_id" data-sortable="true">
							Rating
						</th>
						
						<th data-field="manager_name" data-sortable="true">
							Manager
						</th>
						
						<th data-field="telephone" data-sortable="true">
							Telephone
						</th>
						
						<th data-field="mobile" data-sortable="true">
							Mobile
						</th>
						<!--
						<th data-field="email" data-sortable="true">
							Email
						</th>
						-->
						<th data-field="owner" data-sortable="true">
							Owner
						</th>
						
						<th data-field="profile_guid_last_viewed_when" data-sortable="true">
							Profile Seen
						</th>
												
						<th data-field="actions" data-formatter="actionsFormatter" data-sortable="false">
							Actions
						</th>
					</tr>
				</thead>
			</table	>

<?php
include ('template_bottom.php');
?>