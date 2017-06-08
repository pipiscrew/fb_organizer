<?php
$active_tab="users";

require_once ('template_top.php');

if ($_SESSION['level']!=9)
	die("You dont have permissions to access this area! Ask administrator for more!");
	
// include DB
require_once ('config.php');

$db       = connect();


$user_levels_rows=null;
///////////////////READ user_levels
	$find_sql = "SELECT * FROM `user_levels` order by user_level_name";
	$stmt      = $db->prepare($find_sql);
	
	$stmt->execute();
	$user_levels_rows = $stmt->fetchAll();
///////////////////READ user_levels



$row=null;
///////////////////READ SPEFIC RECORD
if (isset($_GET["id"])) {
	$find_sql = "SELECT * FROM `users` where user_id = :id";

	$stmt      = $db->prepare($find_sql);
	$stmt->bindValue(':id', $_GET["id"]);
	
	$stmt->execute();
	$row = $stmt->fetchAll();
}
///////////////////READ SPEFIC RECORD

?>

<script type="text/javascript">
        $(function() {


	///////////////////////////////////////////////////////////// FILL user_levels
	var jArray_user_levels =   <?php echo json_encode($user_levels_rows); ?>;

	var combo_user_levels_rows = "<option value='0'></option>";
	for (var i = 0; i < jArray_user_levels.length; i++)
	{
		combo_user_levels_rows += "<option value='" + jArray_user_levels[i]["user_level_id"] + "'>" + jArray_user_levels[i]["user_level_name"] + "</option>";
	}

	$("[name=user_level_id]").html(combo_user_levels_rows);
	$("[name=user_level_id]").change(); //select row 0 - no conflict on POST validation @ PHP
	///////////////////////////////////////////////////////////// FILL user_levels



	//init datepicker
	$('[name=last_logon]').datepicker().on('changeDate', function(ev)
	{
		$('[name=last_logon]').datepicker('hide'); //close when selected
	});

	var d1 = new Date();
	d1.setDate(d1.getDate());
		
	//set default value
	$('[name=last_logon]').datepicker('setValue', d1)


	///////////////////////////////////////////////////////////// EDIT RECORD
	var jArray = <?php echo json_encode($row); ?>;
	
	
	if (jArray) {
		//WARNING THE FIELD NAMES IS CASE SENSITIVE ON ARRAY
		// console.log(jArray);
		//if checkbox - $('[name=visible_cat]').prop('checked', jArray["visible"]);
		$('[name=usersFORM_updateID]').val(jArray[0]["user_id"]);
		$('[name=user_level_id]').val(jArray[0]["user_level_id"]);
		$('[name=mail]').val(jArray[0]["mail"]);
		$('[name=password]').val(jArray[0]["password"]);
		$('[name=fullname]').val(jArray[0]["fullname"]);
		$('[name=last_logon]').val(jArray[0]["last_logon"]);


	}
	///////////////////////////////////////////////////////////// EDIT RECORD\
	});
	
</script>

<!-- Content Header (Page header) -->
<section class="content-header">
	<h1>
	<?php 
		if (isset($_GET["id"]))
			echo "Update user";
		else 
			echo "Create new user";
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
			
<form id="users_FORM" role="form" method="post" action="tab_users_details_save.php">
	<button id="btn_users_details_save"  class="btn btn-primary" type="submit" name="submit">
		<span class="glyphicon glyphicon-floppy-disk"></span> save
	</button>
	<br>
	<br>

	<form role="form">
		<div class="row">

			<div class="col-xs-6 col-md-4">
				<div class='form-group'>
					<label>Level :</label>
					<select id="user_level_id" name='user_level_id' class='form-control'>
					</select>
				</div>
			</div>


			<div class="col-xs-6 col-md-4">
				<div class='form-group'>
					<label>eMail :</label>
					<input name='mail' class='form-control' placeholder='mail'>
				</div>
			</div>


			<div class="col-xs-6 col-md-4">
				<div class='form-group'>
					<label>Password :</label>
					<input name='password' class='form-control' placeholder='password' type="password">
				</div>
			</div>

		</div>

		<div class="row">

			<div class="col-xs-6 col-md-4">
				<div class='form-group'>
					<label>Fullname :</label>
					<input name='fullname' class='form-control' placeholder='fullname'>
				</div>
			</div>


			<div class="col-xs-6 col-md-4">
				<div class='form-group'>
					<label>Last Logon :</label><br>
					<input type="text" class="form-control" name="last_logon" data-date-format="yyyy-mm-dd" style="width:100px" >
				</div>
			</div>

		</div>


		<input name="usersFORM_updateID" class="form-control" style="display:none;">

	</form>
</form>

			</div><!-- /.box-body -->
		</div><!-- /.box -->
	</div>
</div>

<?php
include ('template_bottom.php');
?>