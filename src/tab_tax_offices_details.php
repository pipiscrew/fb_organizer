<?php
$active_tab="tax_offices";

require_once ('template_top.php');

if ($_SESSION['level']!=9)
	die("You dont have permissions to access this area! Ask administrator for more!");
	
// include DB
require_once ('config.php');

$db       = connect();


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
	$find_sql = "SELECT * FROM `tax_offices` where tax_office_id = :id";

	$stmt      = $db->prepare($find_sql);
	$stmt->bindValue(':id', $_GET["id"]);
	
	$stmt->execute();
	$row = $stmt->fetchAll();
}
///////////////////READ SPEFIC RECORD

?>

<script type="text/javascript">
        $(function() {


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



	///////////////////////////////////////////////////////////// EDIT RECORD
	var jArray = <?php echo json_encode($row); ?>;
	
	
	if (jArray) {
		//WARNING THE FIELD NAMES IS CASE SENSITIVE ON ARRAY
		// console.log(jArray);
		//if checkbox - $('[name=visible_cat]').prop('checked', jArray["visible"]);
		$('[name=tax_officesFORM_updateID]').val(jArray[0]["tax_office_id"]);
		$('[name=tax_office_name]').val(jArray[0]["tax_office_name"]);
		$('[name=country_id]').val(jArray[0]["country_id"]);
		$('[name=tax_office_code]').val(jArray[0]["tax_office_code"]);
		$('[name=tax_office_prefecture]').val(jArray[0]["tax_office_prefecture"]);


	}
	///////////////////////////////////////////////////////////// EDIT RECORD\
	});
	
</script>

<!-- Content Header (Page header) -->
<section class="content-header">
	<h1>
	<?php 
		if (isset($_GET["id"]))
			echo "Update tax office";
		else 
			echo "Create new tax office";
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
			
<form id="tax_offices_FORM" role="form" method="post" action="tab_tax_offices_details_save.php">
	<button id="btn_tax_offices_details_save"  class="btn btn-primary" type="submit" name="submit">
		<span class="glyphicon glyphicon-floppy-disk"></span> save
	</button>
	<br>
	<br>

	<form role="form">
		<div class="row">

			<div class="col-xs-6 col-md-4">
				<div class='form-group'>
					<label>Name :</label>
					<input name='tax_office_name' class='form-control' placeholder='tax_office_name'>
				</div>
			</div>


			<div class="col-xs-6 col-md-4">
				<div class='form-group'>
					<label>Country :</label>
					<select id="country_id" name='country_id' class='form-control'>
					</select>
				</div>
			</div>


			<div class="col-xs-6 col-md-4">
				<div class='form-group'>
					<label>Office Code :</label>
					<input name='tax_office_code' class='form-control' placeholder='tax_office_code'>
				</div>
			</div>

		</div>

		<div class="row">

			<div class="col-xs-6 col-md-4">
				<div class='form-group'>
					<label>Prefecture :</label>
					<input name='tax_office_prefecture' class='form-control' placeholder='tax_office_prefecture'>
				</div>
			</div>


		<input name="tax_officesFORM_updateID" class="form-control" style="display:none;">

	</form>
</form>

			</div><!-- /.box-body -->
		</div><!-- /.box -->
	</div>
</div>

<?php
include ('template_bottom.php');
?>