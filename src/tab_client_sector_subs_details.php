<?php
$active_tab="client_sector_subs";

require_once ('template_top.php');

if ($_SESSION['level']!=9)
	die("You dont have permissions to access this area! Ask administrator for more!");
	
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



$row=null;
///////////////////READ SPEFIC RECORD
if (isset($_GET["id"])) {
	$find_sql = "SELECT * FROM `client_sector_subs` where client_sector_sub_id = :id";

	$stmt      = $db->prepare($find_sql);
	$stmt->bindValue(':id', $_GET["id"]);
	
	$stmt->execute();
	$row = $stmt->fetchAll();
}
///////////////////READ SPEFIC RECORD

?>

<script type="text/javascript">
        $(function() {


	///////////////////////////////////////////////////////////// FILL client_sectors
	var jArray_client_sectors =   <?php echo json_encode($client_sectors_rows); ?>;

	var combo_client_sectors_rows = "<option value='0'></option>";
	for (var i = 0; i < jArray_client_sectors.length; i++)
	{
		combo_client_sectors_rows += "<option value='" + jArray_client_sectors[i]["client_sector_id"] + "'>" + jArray_client_sectors[i]["client_sector_name"] + "</option>";
	}

	$("[name=client_sector_id]").html(combo_client_sectors_rows);
	$("[name=client_sector_id]").change(); //select row 0 - no conflict on POST validation @ PHP
	///////////////////////////////////////////////////////////// FILL client_sectors




	///////////////////////////////////////////////////////////// EDIT RECORD
	var jArray = <?php echo json_encode($row); ?>;
	
	
	if (jArray) {
		//WARNING THE FIELD NAMES IS CASE SENSITIVE ON ARRAY
		// console.log(jArray);
		//if checkbox - $('[name=visible_cat]').prop('checked', jArray["visible"]);
		$('[name=client_sector_subsFORM_updateID]').val(jArray[0]["client_sector_sub_id"]);
		$('[name=client_sector_sub_name]').val(jArray[0]["client_sector_sub_name"]);
		$('[name=client_sector_id]').val(jArray[0]["client_sector_id"]);


	}
	///////////////////////////////////////////////////////////// EDIT RECORD\
	});
	
</script>

<!-- Content Header (Page header) -->
<section class="content-header">
	<h1>
	<?php 
		if (isset($_GET["id"]))
			echo "Update client_sector_subs";
		else 
			echo "Create new client_sector_subs";
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
			
<form id="client_sector_subs_FORM" role="form" method="post" action="tab_client_sector_subs_details_save.php">
	<button id="btn_client_sector_subs_details_save"  class="btn btn-primary" type="submit" name="submit">
		<span class="glyphicon glyphicon-floppy-disk"></span> save
	</button>
	<br>
	<br>

	<form role="form">
		<div class="row">

			<div class="col-xs-6 col-md-4">
				<div class='form-group'>
					<label>client_sector_sub_name :</label>
					<input name='client_sector_sub_name' class='form-control' placeholder='client_sector_sub_name'>
				</div>
			</div>


			<div class="col-xs-6 col-md-4">
				<div class='form-group'>
					<label>client_sector_id :</label>
					<select id="client_sector_id" name='client_sector_id' class='form-control'>
					</select>
				</div>
			</div>

		</div>


		<input name="client_sector_subsFORM_updateID" class="form-control" style="display:none;">

	</form>
</form>

			</div><!-- /.box-body -->
		</div><!-- /.box -->
	</div>
</div>

<?php
include ('template_bottom.php');
?>