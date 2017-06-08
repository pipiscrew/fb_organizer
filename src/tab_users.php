<?php
//session_start();
//
//if (!isset($_SESSION["u"])) {
//	header("Location: index.html");
//	exit ;
//}

$active_tab="users";

require_once ('template_top.php');

if ($_SESSION['level']!=9)
	die("You dont have permissions to access this area! Ask administrator for more!");
	
// include DB
require_once ('config.php');

$table_columns_caps = array(
	'ID',
	'Level',
	'Mail',
//	'Password',
	'Fullname',
	'Last Logon',

);

$table_columns = array(
	'user_id',
	'user_level_id',
	'mail',
//	'password',
	'fullname',
	'last_logon',

);

$db       = connect();

$find_sql = "SELECT user_id, user_levels.user_level_name as user_level_id, mail, password, fullname, last_logon FROM `users`
 LEFT JOIN user_levels ON user_levels.user_level_id = users.user_level_id";

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
            $("#users_list").dataTable();
        });
    </script>
    
<!-- Content Header (Page header) -->
<section class="content-header">
<?php if (!isset($_GET["isnew"]) && !isset($_GET["isupdate"]) && !isset($_GET["isdelete"]) && !isset($_GET["iserror"])) { ?>
	<h1>
		Users
	</h1>
<?php } else { ?>
<br>
<div class="alert alert-<?php echo isset($_GET["iserror"])? "danger":"success"; ?> alert-dismissable">
                <i class="glyphicon glyphicon-<?php echo isset($_GET["iserror"])? "remove":"ok"; ?>"></i>
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>
                <?php 
                
if (isset($_GET["isnew"]))
               echo "users inserted!";
elseif (isset($_GET["isupdate"]))
               echo "users edited!";
elseif (isset($_GET["isdelete"]))
               echo "users deleted!";
elseif (isset($_GET["iserror"]))
               echo "Error occurred!"; 
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
				<a class="btn btn-primary" href="tab_users_details.php">
					Create new user
				</a><br /><br />
				<table id="users_list" class="table table-bordered table-striped">
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
									<a href="tab_users_details.php?id=<?php echo $row['user_id'] ?>" class="btn btn-primary btn-xs">
										Edit
									</a>
									<a href="tab_users_delete.php?id=<?php echo $row['user_id'] ?>" onclick="return confirm('Delete row, are you sure?')" class="btn btn-danger btn-xs">
										Delete
									</a>
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