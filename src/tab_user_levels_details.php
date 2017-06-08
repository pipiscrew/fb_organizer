<?php
$active_tab="user_levels";

require_once ('template_top.php');

if ($_SESSION['level']!=9)
	die("You dont have permissions to access this area! Ask administrator for more!");
	
// include DB
require_once ('config.php');

$db       = connect();



$row=null;
///////////////////READ SPEFIC RECORD
if (isset($_GET["id"])) {
	$find_sql = "SELECT * FROM `user_levels` where user_level_id = :id";

	$stmt      = $db->prepare($find_sql);
	$stmt->bindValue(':id', $_GET["id"]);
	
	$stmt->execute();
	$row = $stmt->fetchAll();
}
///////////////////READ SPEFIC RECORD

?>

<script type="text/javascript">
        $(function() {




	///////////////////////////////////////////////////////////// EDIT RECORD
	var jArray = <?php echo json_encode($row); ?>;
	
	
	if (jArray) {
		//WARNING THE FIELD NAMES IS CASE SENSITIVE ON ARRAY
		// console.log(jArray);
		//if checkbox - $('[name=visible_cat]').prop('checked', jArray["visible"]);
		$('[name=user_levelsFORM_updateID]').val(jArray[0]["user_level_id"]);
		$('[name=user_level_name]').val(jArray[0]["user_level_name"]);


	}
	///////////////////////////////////////////////////////////// EDIT RECORD\
	});
	
</script>

<!-- Content Header (Page header) -->
<section class="content-header">
	<h1>
	<?php 
		if (isset($_GET["id"]))
			echo "Update user level";
		else 
			echo "Create new user level";
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
			
<form id="user_levels_FORM" role="form" method="post" action="tab_user_levels_details_save.php">
	<button id="btn_user_levels_details_save"  class="btn btn-primary" type="submit" name="submit">
		<span class="glyphicon glyphicon-floppy-disk"></span> save
	</button>
	<br>
	<br>

	<form role="form">
		<div class="row">

			<div class="col-xs-6 col-md-4">
				<div class='form-group'>
					<label>Level Name :</label>
					<input name='user_level_name' class='form-control' placeholder='user_level_name'>
				</div>
			</div>

		</div>


		<input name="user_levelsFORM_updateID" class="form-control" style="display:none;">

	</form>
</form>

			</div><!-- /.box-body -->
		</div><!-- /.box -->
	</div>
</div>

<?php
include ('template_bottom.php');
?>