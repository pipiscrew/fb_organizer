<?php

	if(!isset($_SESSION)){
		session_start();


		if(!isset($_SESSION["u"]))
		{
			header("Location: login.php");
			exit ;
		}
	}
?>

<style>
/*http://css-tricks.com/almanac/properties/d/display/*/

	.bw {
		display: inline-block;
	}

/*http://designshack.net/articles/css/joshuajohnson-2/*/	
	.bw:hover {
	  -webkit-filter: grayscale(100%);
	  cursor: pointer;
	}	
</style>

<br>

<form id="leads_FORM" role="form" method="post" action="tab_leads_details_save.php">



	
<!--	<button id="btn_leads_details_save" href="javascript: submitform()" class="btn btn-primary" type="submit" name="submit">
		<span class="glyphicon glyphicon-floppy-disk"></span> save
	</button>-->
	<br>
	<br>

	<form role="form">
		<div class="row">

			<div class="col-xs-6 col-md-4">
				<div class='form-group'>
					<label>Code :</label>
					<input maxlength="4" id="client_code" name='client_code' class='form-control'  
					<?php if (isset($_GET["id"])) echo "readonly"; ?>
					>
				</div>
			</div>
			
			<div class="col-xs-6 col-md-4">
				<div class='form-group'>
					<label>Company Name :</label>
					<input name='client_name' class='form-control' placeholder='Company Name'>
				</div>
			</div>


			<div class="col-xs-6 col-md-4">
				<div class='form-group'>
					<label>Sector :</label>
					<select id="client_sector_id" name='client_sector_id' class='form-control'>
					</select>
				</div>
			</div>

		</div>

		<div class="row">

			<div class="col-xs-6 col-md-4">
				<div class='form-group'>
					<label>Sub Sector :</label>
					<select id="client_sector_sub_id" name='client_sector_sub_id' class='form-control'>
					</select>
				</div>
			</div>


			<div class="col-xs-6 col-md-4">
				<div class='form-group'>
					<label>Source :</label>
					<select id="client_source_id" name='client_source_id' class='form-control'>
					</select>
				</div>
			</div>


			<div class="col-xs-6 col-md-4">
				<div class='form-group'>
					<label>Rating :</label>
					<select id="client_rating_id" name='client_rating_id' class='form-control'>
					</select>
				</div>
			</div>

		</div>


		<div class="row">

<!--			<div class="col-xs-6 col-md-4">
				<div class='form-group'>
					<label>Next Call :</label><br>
					<input type="text" name="next_call" class="form-control" data-date-format="dd-mm-yyyy hh:ii" readonly class="form_datetime">
				</div>
			</div>-->


			<div class="col-xs-6 col-md-4">
				<div class='form-group'  style="display: inline-block;padding-right: 20px">
					<label>Profile Sent :</label><br>
					<input type="checkbox" name='profile_sent'>
				</div>
				
				<?php if (isset($_GET["id"])) { ?>
				<a onclick='sent_profile()' class='btn btn-success btn-xs'>Send Profile</a>
					
				<?php } ?>
				
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
					<label>City :</label>
					<input name='city_lead' class='form-control' placeholder='City'>
				</div>
			</div>

		</div>

		<div class="row">

			<div class="col-xs-6 col-md-4">
				<div class='form-group'>
					<label>Manager Name :</label>
					<input name='manager_name' class='form-control' placeholder='Manager Name'>
				</div>
			</div>


			<div class="col-xs-6 col-md-4">
				<div class='form-group'>
					<label>Address :</label>
					<input name='address' class='form-control' placeholder='Address'>
				</div>
			</div>


			<div class="col-xs-6 col-md-4">
				<div class='form-group'>
					<label>Email :</label>
					<input name='email' class='form-control' placeholder='Email'>
				</div>
			</div>

		</div>



		<div class="row">

			<div class="col-xs-6 col-md-4">
				<div class='form-group'>
					<label>Manager Name2 :</label>
					<input name='manager_name2' class='form-control' placeholder='Manager Name'>
				</div>
			</div>


			<div class="col-xs-6 col-md-4">
				<div class='form-group'>
					<label>Area :</label>
					<input name='area_lead' class='form-control' placeholder='Area'>
				</div>
			</div>


			<div class="col-xs-6 col-md-4">
				<div class='form-group'>
					<label>Email2 :</label>
					<input name='email2' class='form-control' placeholder='Email'>
				</div>
			</div>

		</div>

		<div class="row">

			<div class="col-xs-6 col-md-4">
				<div class='form-group'>
					<label>Telephone :</label>&nbsp;&nbsp;<img id="tel_indicator" src="img/mini_indicator.gif" style="display: none;">
					<input name='telephone' class='form-control' placeholder='Telephone'>
				</div>
			</div>


			<div class="col-xs-6 col-md-4">
				<div class='form-group'>
					<label>Mobile :</label>
					<input name='mobile' class='form-control' placeholder='Mobile'>
				</div>
			</div>

			<div class="col-xs-6 col-md-4">
				<div class='form-group'>
					<label>Website :</label>
				    <div class="input-group">
				      <div class="input-group-addon">www.</div>
				      <input name='website' class='form-control' placeholder='pipiscrew.com'>
				    </div>
				</div>
			</div>
		</div>

		<div class="row">

			<div id="facebook_foreign" class="col-xs-6 col-md-4">
				<div class='form-group'>
					<label>Facebook Page :</label>
				    <div class="input-group">
				      <div class="input-group-addon">facebook.com/</div>
				      <select id="fb_pages" name='fb_pages' class='form-control'></select>
				      <!--<input name'facebook_page' class='form-control' placeholder='pipiscrew'>-->
				    </div>
				    
						<div class="bw">
							<img src="img/crud_add24.png" onclick="show_facebook_pages_modal()">
						</div>

						<div class="bw">
							<img src="img/crud_delete24.png" onclick="del_selected_facebook()">
						</div>

				    

				</div>
			</div>




			<div class="col-xs-6 col-md-4">
				<div class='form-group'>
					<label>Comment :</label>
					<input name='comment' class='form-control' placeholder='Comment'>
				</div>
			</div>
			
			<div class="col-xs-6 col-md-4">
				<div class='form-group'>
					<label>Owned Date :</label><br>
					<input type="text" class="form-control" name="owned_date" readonly>
				</div>
			</div>
		</div>



		<div class="row">

			<div class="col-xs-6 col-md-4">
				<div class='form-group'>
					<label>Owner :</label>
					<input name='owner' class='form-control' readonly>
				</div>
			</div>


			<div class="col-xs-6 col-md-4">
				<div class='form-group'>
					<label>Modified Date :</label><br>
					<input type="text" class="form-control" name="modified_date" readonly>
				</div>
			</div>


			<div class="col-xs-6 col-md-4">
				<div class='form-group'>
					<label>Modified by :</label>
					<input name='modified_by' class='form-control'  readonly>
				</div>
			</div>

<input type='hidden' id='profile_guid'>

		</div>


		<input name="leadsFORM_updateID" class="form-control" style="display:none;">

<div align="right">
	<a href="javascript: submitform()" class="btn btn-primary">
		<span class="glyphicon glyphicon-floppy-disk"></span> save
	</a>
</div>

	</form>
</form>