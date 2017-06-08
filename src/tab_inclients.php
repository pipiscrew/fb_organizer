<?php
//session_start();
//
//if (!isset($_SESSION["u"])) {
//	header("Location: index.html");
//	exit ;
//}

$active_tab="inclients";

require_once ('template_top.php');

// include DB
require_once ('config.php');

$table_columns_caps = array(
	'client_id',
	'Code',
//	'is_lead',
	'Name',
//	'client_sector_id',
//	'client_sector_sub_id',
//	'client_source_id',
//	'client_rating_id',
//	'meeting_datetime',
//	'meeting_location',
//	'meeting_google',
//	'profile_sent',
//	'country_id',
//	'manager_name',
//	'address',
//	'vat_no',
//	'tax_office_id',
	'Telephone',
	'Mobile',
	'Email',
//	'facebook_page',
//	'website',
//	'Service Starts',
//	'Service Ends',
//	'comment',
	'Owned Date',
	'Owner',
//	'modified_date',
//	'modified_by',
//	'has_facebook_page_before',
//	'facebook_likes',
//	'next_renewal',
//	'marketingplan_datetime',
//	'marketingplan_location',
//	'marketingplan_google',
//	'marketingplan_attachment',
//	'room_exists',

);

$table_columns = array(
	'client_id',
	'client_code',
//	'is_lead',
	'client_name',
//	'client_sector_id',
//	'client_sector_sub_id',
//	'client_source_id',
//	'client_rating_id',
//	'meeting_datetime',
//	'meeting_location',
//	'meeting_google',
//	'profile_sent',
//	'country_id',
//	'manager_name',
//	'address',
//	'vat_no',
//	'tax_office_id',
	'telephone',
	'mobile',
	'email',
//	'facebook_page',
//	'website',
//	'service_starts',
//	'service_ends',
//	'comment',
	'owned_date',
	'owner',
//	'modified_date',
//	'modified_by',
//	'has_facebook_page_before',
//	'facebook_likes',
//	'next_renewal',
//	'marketingplan_datetime',
//	'marketingplan_location',
//	'marketingplan_google',
//	'marketingplan_attachment',
//	'room_exists',

);

$db       = connect();

$wh=" where ";
if ($_SESSION["level"] != 9)
	$wh = " where owner=".$_SESSION["id"]." and ";

$find_sql = "SELECT client_id, client_code, is_lead, client_name, client_sectors.client_sector_name as client_sector_id, client_sector_subs.client_sector_sub_name as client_sector_sub_id, client_sources.client_source_name as client_source_id, client_ratings.client_rating_name as client_rating_id, profile_sent, country_id, manager_name, address, telephone, mobile, email, facebook_page, website, comment, DATE_FORMAT(owned_date,'%d-%m-%Y %H:%i') as owned_date,userA.fullname as owner, DATE_FORMAT(modified_date,'%d-%m-%Y %H:%i') as modified_date, modified_by, has_facebook_page_before, room_exists FROM `clients`
 LEFT JOIN client_sectors ON client_sectors.client_sector_id = clients.client_sector_id
 left join users as userA on userA.user_id = clients.owner
 LEFT JOIN client_sector_subs ON client_sector_subs.client_sector_sub_id = clients.client_sector_sub_id
 LEFT JOIN client_sources ON client_sources.client_source_id = clients.client_source_id
 LEFT JOIN client_ratings ON client_ratings.client_rating_id = clients.client_rating_id $wh is_lead=2 order by client_id";

$stmt      = $db->prepare($find_sql);
$stmt->execute();
$rows_sql = $stmt->fetchAll();
$rows= array();

foreach($rows_sql as $row_key => $row_sql){
	for($i = 0; $i < count($table_columns); $i++){
		{
			$rows[$row_key][$table_columns[$i]] = $row_sql[$table_columns[$i]];
		}
	}
}
?>

    <script type="text/javascript">
        $(function() {
            $("#clients_list").dataTable({
                "aaSorting": [], //disable initial sort
                "iDisplayLength": 50, //pagination per 50recs
                "aoColumnDefs": [{ "bVisible": false, "aTargets": [0] }] //hide 1st col 
            	});
        });
    </script>
    
<!-- Content Header (Page header) -->
<section class="content-header">
<?php if (!isset($_GET["isnew"]) && !isset($_GET["isupdate"]) && !isset($_GET["isdelete"]) && !isset($_GET["iserror"])) { ?>
	<h1>
		Inactive Clients
	</h1>
<?php } else { ?>
<br>
<div class="alert alert-<?php echo isset($_GET["iserror"])? "danger":"success"; ?> alert-dismissable">
                <i class="glyphicon glyphicon-<?php echo isset($_GET["iserror"])? "remove":"ok"; ?>"></i>
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>
                <?php 
                
if (isset($_GET["isnew"]))
               echo "Inactive Client Inserted!";
elseif (isset($_GET["isupdate"]))
               echo "Inactive Client Edited!";
elseif (isset($_GET["isdelete"]))
               echo "Inactive Client Deleted!";
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

<div class="row">
	<div class="col-xs-12">

		<div class="box">
			<div class="box-header">

			</div>

			<div class="box-body table-responsive">
				<br /><br />
				<table id="clients_list" class="table table-bordered table-striped">
					<thead>
						<tr>
							<?php
							foreach($table_columns_caps as $table_column)
							{
								?>
								<th>
									<?php echo $table_column; ?>
								</th>
								<?php
							} ?>
							<th>
								Actions
							</th>
						</tr>
					</thead>
					<tbody>
						<?php
						foreach($rows as $row)
						{
							?>
							<tr>
								<?php
								foreach($table_columns as $table_column)
								{
									?>
									<td>
										<?php echo $row[$table_column]; ?>
									</td>
									<?php
								} ?>
								<td>
									<a href="tab_inclients_details.php?id=<?php echo $row['client_id'] ?>" class="btn btn-primary btn-xs">
										Edit
									</a>
									
								<?php if ($_SESSION['level']==9) { ?>
									<a href="tab_clients_delete.php?id=<?php echo $row['client_id'] ?>" onclick="return confirm('Delete row, are you sure?')" class="btn btn-danger btn-xs">
										Delete
									</a>
								<?php } ?>
								
								</td>
							</tr>
							<?php
						} ?>

					</tbody>
					<tfoot>
						<tr>
							<?php
							foreach($table_columns_caps as $table_column)
							{
								?>
								<th>
									<?php echo $table_column; ?>
								</th>
								<?php
							} ?>
							<th>
								Actions
							</th>
						</tr>
					</tfoot>
				</table>
			</div><!-- /.box-body -->
		</div><!-- /.box -->
	</div>
</div>



<?php
include ('template_bottom.php');
?>