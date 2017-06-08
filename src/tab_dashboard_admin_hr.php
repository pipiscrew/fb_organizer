<?php
session_start();

require_once ('template_top.php');

include ('config.php');
include ('config_general.php');

//only admim can see the page
if ($_SESSION['level']!=9)
		die("You are not authorized to view this!");
		
$db=connect();
?>

<script>
				//bootstrap-table
				function queryParamsHR_RECORDS(params)
				{
					var q = {
						"limit": params.limit,
						"offset": params.offset,
						"search": params.search,
						"name": params.sort,
						"order": params.order
					};
 
					return q;
				}
			
			
function cv_formatter(value, row) {
    if (value==null)
        return "";
 
	var x = "<a href='http://pipiscrew.com/fb_com/pipiscrew_cv/" + value + "' target='_blank'>Download CV</a>";
	
	
    return x;
}

</script>
		<div class="container">
		
			<table id="hr_records_tbl"
	           data-toggle="table"
	           data-striped=true
	           data-url="tab_dashboard_admin_hr_pagination.php"
	           data-search="true"
	           data-pagination="true"
	           data-page-size="50"
	           data-height="500"
	           data-side-pagination="server"
	           data-query-params="queryParamsHR_RECORDS">

				<thead>
					<tr>
						<th data-field="hr_record_id" data-visible="false">
							hr_record_id
						</th>
						
						<th data-field="full_name" data-sortable="true">
							Full Name
						</th>
						
						<th data-field="email" data-sortable="true">
							Email
						</th>
						
						<th data-field="gender" data-sortable="true">
							Gender
						</th>
						
						<th data-field="fb_id" data-sortable="true">
							FB_ID
						</th>
						
						<th data-field="tel" data-sortable="true">
							Telephone
						</th>
						
						<th data-field="city" data-sortable="true">
							City
						</th>
						
						<th data-field="portofolio" data-sortable="true">
							Portofolio
						</th>
						
						<th data-field="skills" data-sortable="true">
							Skills
						</th>
						
						<th data-field="dob" data-sortable="true">
							DOB
						</th>
						
						<th data-field="filename"  data-formatter="cv_formatter" data-sortable="true">
							CV Filename
						</th>
						
						<th data-field="daterec" data-sortable="true">
							DateREC
						</th>
						
					</tr>
				</thead>
			</table	>
		</div>
		


<?php
include ('template_bottom.php');
?>