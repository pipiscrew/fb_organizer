<?php

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


$tax_offices_rows=null;
///////////////////READ tax_offices
	$find_sql = "SELECT * FROM `tax_offices` order by tax_office_name";
	$stmt      = $db->prepare($find_sql);
	
	$stmt->execute();
	$tax_offices_rows = $stmt->fetchAll();
///////////////////READ tax_offices


?>

<script>
			$(function ()
				{

	///////////////////////////////////////////////////////////// FILL countries
//	var jArray_countries =   <?php echo json_encode($countries_rows); ?>;
//
//	var combo_countries_rows = "<option value='0'></option>";
//	for (var i = 0; i < jArray_countries.length; i++)
//	{
//		combo_countries_rows += "<option value='" + jArray_countries[i]["country_id"] + "'>" + jArray_countries[i]["country_name"] + "</option>";
//	}
//
//	$("[name=country_id_INVOICE]").html(combo_countries_rows);
//	$("[name=country_id_INVOICE]").change(); //select row 0 - no conflict on POST validation @ PHP
	///////////////////////////////////////////////////////////// FILL countries


	///////////////////////////////////////////////////////////// FILL tax_offices
	var jArray_tax_offices =   <?php echo json_encode($tax_offices_rows); ?>;

	var combo_tax_offices_rows = "<option value='0'></option>";
	for (var i = 0; i < jArray_tax_offices.length; i++)
	{
		combo_tax_offices_rows += "<option value='" + jArray_tax_offices[i]["tax_office_id"] + "'>" + jArray_tax_offices[i]["tax_office_name"] + "</option>";
	}

	$("[name=tax_office_id_INVOICE]").html(combo_tax_offices_rows);
	$("[name=tax_office_id_INVOICE]").change(); //select row 0 - no conflict on POST validation @ PHP
	///////////////////////////////////////////////////////////// FILL tax_offices




					//http://wenzhixin.net.cn/p/bootstrap-table/docs/examples.html#via-javascript-table
					$('#client_invoice_details_tbl').bootstrapTable();

					//new record
					$('#btn_client_invoice_details_new').on('click', function(e)
					{
						$('#lblTitle_CLIENT_INVOICE_DETAILS').html("New CLIENT_INVOICE_DETAILS");
						
						$('#modalCLIENT_INVOICE_DETAILS').modal('toggle');
					});
						
					//edit record
					$('#btn_client_invoice_details_edit').on('click', function(e)
					{
						var row = $('#client_invoice_details_tbl').bootstrapTable('getSelections');

						if (row.length>0)
							{
								query_CLIENT_INVOICE_DETAILS_modal(row[0].client_invoice_detail_id);
								console.log(row[0].client_invoice_detail_id);
							}
						else 
							alert("Please select a row");
					});
					
					//delete record
					$('#btn_client_invoice_details_delete').on('click', function(e)
					{
						var row = $('#client_invoice_details_tbl').bootstrapTable('getSelections');

						if (row.length>0)
							{
								if (confirm("Would you like to delete :\r\n" + row[0].company_name + "\r\n" + row[0].address + " ?"))
									delete_CLIENT_INVOICE_DETAILS(row[0].client_invoice_detail_id);
							}
						else 
							alert("Please select a row");
					});
					

				    ////////////////////////////////////////
				    // MODAL FUNCTIONALITIES [START]
				    //when modal closed, hide the warning messages + reset
				    $('#modalCLIENT_INVOICE_DETAILS').on('hidden.bs.modal', function() {
				        //when close - clear elements
				        $('#formCLIENT_INVOICE_DETAILS').trigger("reset");
				 
				        //clear validator error on form
				        validatorCLIENT_INVOICE_DETAILS.resetForm();
				    });
				 
				    //functionality when the modal already shown and its long, when reloaded scroll to top
				    $('#modalCLIENT_INVOICE_DETAILS').on('shown.bs.modal', function() {
				        $(this).animate({
				            scrollTop : 0
				        }, 'slow');
				        
//				        $("#client_id_INVOICE").val( <?= $_GET["id"]; ?>);
				    });
				    // MODAL FUNCTIONALITIES [END]
				    ////////////////////////////////////////
				    
				    //jquery.validate.min.js
				    var validatorCLIENT_INVOICE_DETAILS = $("#formCLIENT_INVOICE_DETAILS").validate({
				        rules : {
				             client_id_INVOICE : { required : true },
				             company_name : { required : true },
				             occupation : { required : true },
				             address_INVOICE : { required : true },
				             pobox : { required : true },
				             city : { required : true },
				             vat_no_INVOICE : { required : true },
				             country_id_INVOICE : { greaterThanZero : true },
				             tax_office_id_INVOICE : { greaterThanZero : true },

				        },
				        messages : {
				            client_id_INVOICE : 'Required Field',
				            company_name : 'Required Field',
				            occupation : 'Required Field',
				            address_INVOICE : 'Required Field',
				            pobox : 'Required Field',
				            city : 'Required Field',
				            vat_no_INVOICE : 'Required Field',
				            country_id_INVOICE : 'Required Field',
				            tax_office_id_INVOICE : 'Required Field',

				        }
				    });
				    
					////////////////////////////////////////
					// MODAL SUBMIT aka save & update button
					$('#formCLIENT_INVOICE_DETAILS').submit(function(e) {
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
					    $('#modalCLIENT_INVOICE_DETAILS').modal('toggle');
					 
					    $.ajax(
					    {
					        url : formURL,
					        type: "POST",
					        data : postData,
					        success:function(data, textStatus, jqXHR)
					        {
					            if (data=="00000")
									//refresh
									$('#client_invoice_details_tbl').bootstrapTable('refresh');
					            else
					                alert("ERROR");
					        },
					        error: function(jqXHR, textStatus, errorThrown)
					        {
					            alert("ERROR - connection error");
					        }
					    });
					});

				});
				
				//bootstrap-table
				function queryParamsCLIENT_INVOICE_DETAILS(params)
				{
					
				    var q = {
				        "limit": params.limit,
				        "offset": params.offset,
				        "search": params.search,
				        "name": params.sort,
				        "order": params.order,
						"clientid" : <?= $_GET["id"]; ?>
				    };
				 
				    return q;    

				}
				
				//edit button - read record
				function query_CLIENT_INVOICE_DETAILS_modal(rec_id){
					loading.appendTo(document.body);
					
				    $.ajax(
				    {
				        url : "tab_client_invoice_details_fetch.php",
				        type: "POST",
				        data : { client_invoice_detail_id : rec_id },
				        success:function(data, textStatus, jqXHR)
				        {
							loading.remove();
							
				        	if (data!='null')
							{
							 	$("[name=client_invoice_detailsFORM_updateID]").val(data.client_invoice_detail_id);
//								$('[name=client_id_INVOICE]').val(data.client_id);
								$('[name=company_name]').val(data.company_name);
								$('[name=occupation]').val(data.occupation);
								$('[name=address_INVOICE]').val(data.address);
								$('[name=pobox]').val(data.pobox);
								$('[name=city]').val(data.city);
								$('[name=country_id_INVOICE]').val(data.country_id);
								$('[name=vat_no_INVOICE]').val(data.vat_no);
								$('[name=tax_office_id_INVOICE]').val(data.tax_office_id);

							 	
							 	$('#lblTitle_CLIENT_INVOICE_DETAILS').html("Edit CLIENT_INVOICE_DETAILS");
								$('#modalCLIENT_INVOICE_DETAILS').modal('toggle');
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
				function delete_CLIENT_INVOICE_DETAILS(rec_id){
					loading.appendTo(document.body);
					
				    $.ajax(
				    {
				        url : "tab_client_invoice_details_delete.php",
				        type: "POST",
				        data : { client_invoice_detail_id : rec_id },
				        success:function(data, textStatus, jqXHR)
				        {
				        	loading.remove();
				        	
				        	if (data=='00000')
							{
								//refresh
								$('#client_invoice_details_tbl').bootstrapTable('refresh');
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
			<button id="btn_client_invoice_details_new" type="button" class="btn btn-success">
				New
			</button>
			<button id="btn_client_invoice_details_edit" type="button" class="btn btn-primary">
				Edit
			</button>
			<?php if ($_SESSION['level']==9) { ?>
			<button id="btn_client_invoice_details_delete" type="button" class="btn btn-danger">
				Delete
			</button> 
			<?php } ?>
			<br/><br/>
<!--	       
	           data-page-size="50"
	           data-show-columns="true"
	           data-search="true"
	           data-show-refresh="true"
	           data-show-toggle="true"
	           data-pagination="true"
	           -->
		
			<table id="client_invoice_details_tbl"
	           data-toggle="table"
	           data-striped=true
	           data-url="tab_client_invoice_details_pagination.php"
	           data-click-to-select="true" data-single-select="true"

	           data-height="500"
	           data-side-pagination="server"
	           data-query-params="queryParamsCLIENT_INVOICE_DETAILS">

				<thead>
					<tr>
						<th data-field="state" data-checkbox="true" >
						</th>

						<th data-field="client_invoice_detail_id" data-visible="false">
							id
						</th>
						
						<th data-field="client_id_INVOICE" data-sortable="true" data-visible="false">
							client_id_INVOICE
						</th>
						
						<th data-field="company_name" data-sortable="true">
							Company Name
						</th>
						
						<th data-field="occupation" data-sortable="true">
							Occupation
						</th>
						
						<th data-field="address" data-sortable="true">
							Address
						</th>
						
						<th data-field="pobox" data-sortable="true" data-visible="false">
							pobox
						</th>
						
						<th data-field="city" data-sortable="true">
							City
						</th>
						
						<th data-field="country_id" data-sortable="true">
							Country
						</th>
						
						<th data-field="vat_no" data-sortable="true">
							VAT
						</th>
						
						<th data-field="tax_office_id" data-sortable="true">
							Tax Office
						</th>
						
					</tr>
				</thead>
			</table	>
		</div>



<!-- NEW CLIENT_INVOICE_DETAILS MODAL [START] -->
<div class="modal fade" id="modalCLIENT_INVOICE_DETAILS" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
					&times;
				</button>
				<h4 class="modal-title" id='lblTitle_CLIENT_INVOICE_DETAILS'>New</h4>
			</div>
			<div class="modal-body">
				<form id="formCLIENT_INVOICE_DETAILS" role="form" method="post" action="tab_client_invoice_details_save.php">

				<div class='form-group'>
					<label>Company Name :</label>
					<input name='company_name' class='form-control' placeholder='Company Name'>
				</div>


			
				<div class='form-group'>
					<label>Activity :</label>
					<input name='occupation' class='form-control' placeholder='Activity'>
				</div>


			
				<div class='form-group'>
					<label>Address :</label>
					<input name='address_INVOICE' class='form-control' placeholder='Address'>
				</div>


			
				<div class='form-group'>
					<label>Postal Code :</label>
					<input name='pobox' class='form-control' placeholder='Postal Code'>
				</div>


			
				<div class='form-group'>
					<label>City :</label>
					<input name='city' class='form-control' placeholder='City'>
				</div>


			
				<div class='form-group'>
					<label>Country :</label>
					<select id="country_id_INVOICE" name='country_id_INVOICE' class='form-control'>
					</select>
				</div>


			
				<div class='form-group'>
					<label>VAT :</label>
					<input name='vat_no_INVOICE' class='form-control' placeholder='VAT'>
				</div>


			
				<div class='form-group'>
					<label>Tax Office :</label>
					<select id="tax_office_id_INVOICE" name='tax_office_id_INVOICE' class='form-control'>
					</select>
				</div>



						<input name="client_id_INVOICE" id="client_id_INVOICE" value="<?= $_GET["id"]; ?>" style="display:none;">
						<input name="client_invoice_detailsFORM_updateID" id="client_invoice_detailsFORM_updateID" style="display:none;">

						<div class="modal-footer">
							<button id="bntCancel_CLIENT_INVOICE_DETAILS" type="button" class="btn btn-default" data-dismiss="modal">
								cancel
							</button>
							<button id="bntSave_CLIENT_INVOICE_DETAILS" class="btn btn-primary" type="submit" name="submit">
								save
							</button>
						</div>
                </form>
            </div><!-- End of Modal body -->
        </div><!-- End of Modal content -->
    </div><!-- End of Modal dialog -->
</div><!-- End of Modal -->
<!-- NEW CLIENT_INVOICE_DETAILS MODAL [END] -->