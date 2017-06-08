<?php
session_start();

if(!isset($_SESSION["u"])){
	header("Location: login.php");
	exit ;
}
else
if($_SESSION['level'] != 9){
	die("You are not authorized to view this!");
}

require_once ('template_top.php');

include ('config.php');
include ('config_general.php');

//only admim can see the page
if($_SESSION['level'] != 9)
die("You are not authorized to view this!");

$db                      = connect();

$expense_categories_rows = null;
///////////////////READ expense_categories
$find_sql                   = "SELECT * FROM `expense_categories` where parent_id = 0 order by expense_category_name";
$stmt                       = $db->prepare($find_sql);

$stmt->execute();
$expense_categories_rows    = $stmt->fetchAll();
///////////////////READ expense_categories

?>


<script>
	//var loading = $('<div class="modal-backdrop"></div><div class="progress progress-striped active loading"><div class="progress-bar" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%">');

	$(function ()
		{

			///////////////////////////////////////////////////////////// FILL expense_categories
			var jArray_expense_categories =   <?php echo json_encode($expense_categories_rows); ?>;

			var combo_expense_categories_rows = "<option value='0'></option>";
			for (var i = 0; i < jArray_expense_categories.length; i++)
			{
				combo_expense_categories_rows += "<option value='" + jArray_expense_categories[i]["expense_category_id"] + "'>" + jArray_expense_categories[i]["expense_category_name"] + "</option>";
			}

			$("[name=expense_category_id]").html(combo_expense_categories_rows);
			$("[name=expense_category_id]").change(); //select row 0 - no conflict on POST validation @ PHP
			///////////////////////////////////////////////////////////// FILL expense_categories

			$('[name=expense_daterec]').datetimepicker(
				{
					weekStart: 1,
					todayBtn:  1,
					autoclose: 1,
					todayHighlight: 1,
					startView: 2,
					minView: 2,
					forceParse: 1
				});

			//set default value
			var timeInMs = new Date();
			$('[name=expense_daterec]').val(timeInMs.getDate() + "-" + (timeInMs.getMonth()+1) + "-" + timeInMs.getFullYear());

			////////////////////////////////////////
			// MODAL FUNCTIONALITIES [START]
			//when modal closed, hide the warning messages + reset
			$('#modalEXPENSES').on('hidden.bs.modal', function()
				{
					//clear sub categories combo
					$("#expense_sub_category_id").html("");
					
					//when close - clear elements
					$('#formEXPENSES').trigger("reset");

					//clear validator error on form
					validatorEXPENSES.resetForm();
				});

		    //add new - subcategory modal - hide event - reset field
		    $('#modalEXPENSESsubcat').on('hidden.bs.modal', function() {
				//refresh the combo
		    	refresh_subcategory_by_categoryVAL();
		        
		        //clear elements
		        $('#formEXPENSESsubcat').trigger("reset");
		    });
		    
		    
			//functionality when the modal already shown and its long, when reloaded scroll to top
			$('#modalEXPENSES').on('shown.bs.modal', function()
				{
					$(this).animate(
						{
							scrollTop : 0
						}, 'slow');
				});
			// MODAL FUNCTIONALITIES [END]
			////////////////////////////////////////

			//when combo category change
			$('[name=expense_category_id]').on('change', function()
			{
				refresh_subcategory_by_categoryVAL();
			});
			
			//when combo sub_category change
			$('[name=expense_sub_category_id]').on('change', function()
			{
				//check if is first option **add new**
				if ($(this).val()=="-1")
				{
					//set modal parentID
					$("#subcat_parent_id").val($("#expense_category_id").val());
					
					//show modal to add new sub category
					$('#modalEXPENSESsubcat').modal('toggle');
				}

			});

			//http://wenzhixin.net.cn/p/bootstrap-table/docs/examples.html#via-javascript-table
			$('#expenses_tbl').bootstrapTable();

			//new record
			$('#btn_expenses_new').on('click', function(e)
				{
					$('#lblTitle_EXPENSES').html("New EXPENSES");

					$('#modalEXPENSES').modal('toggle');
				});

			//edit record
			$('#btn_expenses_edit').on('click', function(e)
				{
					var row = $('#expenses_tbl').bootstrapTable('getSelections');

					if (row.length>0)
					{
						query_EXPENSES_modal(row[0].expense_template_id);
					}
					else
					alert("Please select a row");
				});

			//delete record
			$('#btn_expenses_delete').on('click', function(e)
				{
					var row = $('#expenses_tbl').bootstrapTable('getSelections');

					if (row.length>0)
					{
						if (confirm("Would you like to delete " + row[0].expense_category_id + " ?"))
						delete_EXPENSES(row[0].expense_template_id);
					}
					else
					alert("Please select a row");
				});


			var validatorEXPENSES = $("#formEXPENSES").validate({
				rules : {
					expense_category_id : { greaterThanZero : true },
					expense_sub_category_id : { greaterThanZero : true }
				},
				messages : {
					expense_category_id : 'Required Field',
					expense_sub_category_id : 'Required Field'
				}
			});
			
			var formEXPENSESsubcat = $("#formEXPENSESsubcat").validate({
				rules : {
					subcategory_txt : { 
						required : true
					 },
				messages : {
					subcategory_txt : 'Required Field'
				}
			}
			});
			
			////////////////////////////////////////
			// MODAL SUBMIT aka save & update button
			$('#formEXPENSES').submit(function(e)
				{
					e.preventDefault();

					////////////////////////// validation
					var form = $(this);
					form.validate();

					if (!form.valid())
					return;
					////////////////////////// validation

					var postData = $(this).serializeArray();
					var formURL = $(this).attr("action");

					//close modal
					$('#modalEXPENSES').modal('toggle');

					$.ajax(
						{
							url : formURL,
							type: "POST",
							data : postData,
							success:function(data, textStatus, jqXHR)
							{
								if (data=="00000")
								//refresh
								$('#expenses_tbl').bootstrapTable('refresh');
								else
								alert("ERROR");
							},
							error: function(jqXHR, textStatus, errorThrown)
							{
								alert("ERROR - connection error");
							}
						});
				});

			////////////////////////////////////////
			// MODAL SUBMIT aka save & update button
			$('#formEXPENSESsubcat').submit(function(e)
				{
					e.preventDefault();

					////////////////////////// validation
					var form = $(this);
					form.validate();

					if (!form.valid())
					return;
					////////////////////////// validation

					var postData = $(this).serializeArray();
					var formURL = $(this).attr("action");

					loading.appendTo($('#formEXPENSESsubcat'));

					$.ajax(
						{
							url : formURL,
							type: "POST",
							data : postData,
							success:function(data, textStatus, jqXHR)
							{
								 loading.remove();
								if (data=="00000")
								{
									$('#modalEXPENSESsubcat').modal('toggle');
								}
								else
									alert("ERROR");
							},
							error: function(jqXHR, textStatus, errorThrown)
							{
								 loading.remove();
								alert("ERROR - connection error");
							}
						});
				});


		}); //jQuery

			
	//universal function to fill combos
	function setComboItems(ctl_name, jArray)
	{
		var combo_rows = "<option value='0'></option><option value='-1'>**Add new**</option>";
		for (var i = 0; i < jArray.length; i++)
		{
			combo_rows += "<option value='" + jArray[i]["id"] + "'>" + jArray[i]["description"] + "</option>";
		}

//		dontshowmodal=true;
		$("[name=" + ctl_name + "]").html(combo_rows);
		$("[name=" + ctl_name + "]").change();
		
//		dontshowmodal=false;
	}
			
	function refresh_subcategory_by_categoryVAL(sub_category)
	{
		$("#sub_category_indicator").show();
		//used when edit a record
		var sub_category_id;
		sub_category_id = sub_category;
		//used when edit a record

		$.ajax(
			{
				url : 'tab_dashboard_admin_big_brother_settings_get_by_category.php',
				dataType : 'json',
				type : 'POST',
				data :
				{
					"id" : $("#expense_category_id").val(),
				},
				success : function(data)
				{
					setComboItems("expense_sub_category_id",data.recs);

					if (sub_category_id)
					{	
						$('[name=expense_sub_category_id]').val(sub_category_id);
					
						if (sub_category_id!= 0 && $('[name=expense_sub_category_id]').val()==null)
							alert("Subcategory record cant be found!");
					}	
						
					$("#sub_category_indicator").hide();
				},
				error : function(e)
				{
					$("#sub_category_indicator").hide();
					alert("error");
				}
			});
	}
			
	//bootstrap-table
	function queryParamsEXPENSES(params)
	{
		var q =
		{
			"limit": params.limit,
			"offset": params.offset,
			"search": params.search,
			"name": params.sort,
			"order": params.order
		};

		return q;
	}

	//edit button - read record
	function query_EXPENSES_modal(rec_id)
	{
		loading.appendTo(document.body);

		$.ajax(
			{
				url : "tab_dashboard_admin_big_brother_settings_fetch.php",
				type: "POST",
				data :
				{
					expense_id : rec_id
				},
				success:function(data, textStatus, jqXHR)
				{
					loading.remove();

					if (data!='null')
					{
						$("[name=expensesFORM_updateID]").val(data.expense_template_id);
						$('[name=expense_category_id]').val(data.expense_category_id);
						refresh_subcategory_by_categoryVAL(data.expense_sub_category_id);
						$('[name=price]').val(data.price);

						$('#lblTitle_EXPENSES').html("Edit Expense");
						$('#modalEXPENSES').modal('toggle');
					}
					else
					alert("ERROR - Cant read the record.");
				},
				error: function(jqXHR, textStatus, errorThrown)
				{
					loading.remove();
					alert("ERROR");
				}
			});
	}

	//delete button - delete record
	function delete_EXPENSES(rec_id)
	{
		loading.appendTo(document.body);

		$.ajax(
			{
				url : "tab_dashboard_admin_big_brother_settings_delete.php",
				type: "POST",
				data :
				{
					expense_id : rec_id
				},
				success:function(data, textStatus, jqXHR)
				{
					loading.remove();

					if (data=='00000')
					{
						//refresh
						$('#expenses_tbl').bootstrapTable('refresh');
					}
					else
					alert("ERROR - Cant delete the record.");
				},
				error: function(jqXHR, textStatus, errorThrown)
				{
					loading.remove();
					alert("ERROR");
				}
			});
	}



</script>

<div class="container">
	<button id="btn_expenses_new" type="button" class="btn btn-success">
		New
	</button>
	<button id="btn_expenses_edit" type="button" class="btn btn-primary">
		Edit
	</button>
	<button id="btn_expenses_delete" type="button" class="btn btn-danger">
		Delete
	</button>

	<table id="expenses_tbl"
	           data-toggle="table"
	           data-striped=true
	           data-url="tab_dashboard_admin_big_brother_settings_pagination.php"
	           data-show-columns="true"
	           data-search="true"
	           data-show-refresh="true"
	           data-show-toggle="true"
	           data-pagination="true"
	           data-click-to-select="true" data-single-select="true"
	           data-page-size="50"
	           data-height="500"
	           data-side-pagination="server"
	           data-query-params="queryParamsEXPENSES">

		<thead>
			<tr>
				<th data-field="state" data-checkbox="true" >
				</th>

				<th data-field="expense_template_id" data-visible="false">
					expense_id
				</th>

				<th data-field="expense_category_id" data-sortable="true">
					Category
				</th>
				
				<th data-field="expense_sub_category_id" data-sortable="true">
					Subcategory
				</th>

				<th data-field="price" data-sortable="true">
					Price
				</th>
			</tr>
		</thead>
	</table	>
</div>



<!-- NEW EXPENSES MODAL [START] -->
<div class="modal fade" id="modalEXPENSES" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
					&times;
				</button>
				<h4 class="modal-title" id='lblTitle_EXPENSES'>
					New Expense
				</h4>
			</div>
			<div class="modal-body">
				<form id="formEXPENSES" role="form" method="post" action="tab_dashboard_admin_big_brother_settings_save.php">


					<div class='form-group'>
						<label>
							Category :
						</label>
						<select id="expense_category_id" name='expense_category_id' class='form-control'>
						</select>

					</div>



					<div class='form-group'>
						<label>
							Subcategory :
						</label>&nbsp;<img id="sub_category_indicator" src="img/mini_indicator.gif" style="display: none;">
						<select id="expense_sub_category_id" name='expense_sub_category_id' class='form-control'>
						</select>

					</div>


					<div class='form-group'>
						<label>
							Price :
						</label>
						<input name='price' class='form-control' placeholder='price'>
					</div>



					<!-- <input name="expensesFORM_FKid" id="EXPENSES_FKid" class="form-control" style="display:none;"> -->
					<input name="expensesFORM_updateID" id="expensesFORM_updateID" class="form-control" style="display:none;">

					<div class="modal-footer">
						<button id="bntCancel_EXPENSES" type="button" class="btn btn-default" data-dismiss="modal">
							cancel
						</button>
						<button id="bntSave_EXPENSES" class="btn btn-primary" type="submit" name="submit">
							save
						</button>
					</div>
				</form>
			</div><!-- End of Modal body -->
		</div><!-- End of Modal content -->
	</div><!-- End of Modal dialog -->
</div><!-- End of Modal -->
<!-- NEW EXPENSES MODAL [END] -->

<!-- NEW SUBCATEGORY MODAL [START] -->
<div class="modal fade" id="modalEXPENSESsubcat" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
					&times;
				</button>
				<h4 class="modal-title" id='lblTitle_EXPENSESsubcat'>
					New Subcategory
				</h4>
			</div>
			<div class="modal-body">
				<form id="formEXPENSESsubcat" role="form" method="post" action="tab_dashboard_admin_big_brother_settings_new_sub_save.php">


					<div class='form-group'>
						<label>
							Subcategory :
						</label>
						<input id="subcategory_txt" name='subcategory_txt' class='form-control'>
					</div>


					<input name="subcat_parent_id" id="subcat_parent_id" class="form-control" style="display:none;">

					<div class="modal-footer">
						<button id="bntCancel_EXPENSESsubcat" type="button" class="btn btn-default" data-dismiss="modal">
							cancel
						</button>
						<button id="bntSave_EXPENSESsubcat" class="btn btn-primary" type="submit" name="submit">
							save
						</button>
					</div>
				</form>
			</div><!-- End of Modal body -->
		</div><!-- End of Modal content -->
	</div><!-- End of Modal dialog -->
</div><!-- End of Modal -->
<!-- NEW SUBCATEGORY MODAL [END] -->


<?php
include ('template_bottom.php');
?>