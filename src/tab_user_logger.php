<?php
include ('template_top.php');
?>

<script>
			$(function ()
				{
					//http://wenzhixin.net.cn/p/bootstrap-table/docs/examples.html#via-javascript-table
					$('#logger_tbl').bootstrapTable();
					
		$('[name=start_date],[name=end_date]').datetimepicker({
	        weekStart: 1,
	        todayBtn:  1,
			autoclose: 1,
			todayHighlight: 1,

			startView: 2,
			minView: 2,
			
			forceParse: 1
	    });
	    
				}) //jQuery ends
					
				//bootstrap-table
				function queryParamsLOGGER(params)
				{
					if ($("#start_date").val().trim().length > 0 && $("#end_date").val().trim().length == 0)
					{
						alert("Please fill 'end date'");
						return false;
					}
					else if ($("#end_date").val().trim().length > 0 && $("#start_date").val().trim().length == 0)
					{
						alert("Please fill 'start date'");
						return false;
					}
					
					var q = {
						"limit": params.limit,
						"offset": params.offset,
						"search": params.search,
                        //
                        "start_date" : $("#start_date").val(),
                        "end_date" : $("#end_date").val(),
                        //
                        
						"name": params.sort,
						"order": params.order
					};
 
					return q;
				}
				
				function date_formatter(value, row) {
					if (value==null)
						return "";

					var date = new Date(value);
					var options = {
					    year: "numeric", month: "2-digit",
					    day: "2-digit", hour: "2-digit", minute: "2-digit", hour12: false
					};
					
					return(date.toLocaleString("en-GB", options));
						
// 					var t = value.split(/[- :]/);
//    				var dt = new Date(t[0], t[1]-1, t[2], t[3]||0, t[4]||0, t[5]||0);
//    				return dt.getDate() + "/" + (dt.getMonth() + 1) + "/" + dt.getFullYear();
				}
					
			    function logtype_formatter(value, row) {
			    	
			    	var icon="";
			    	switch (value){
						case "4" : icon="notify.png"; break;
						case "5" : icon="info.png"; break;
						case "6" : icon="danger.png"; break;
						case "1" : icon="user_notify.png"; break;
						case "2" : icon="user_info.png"; break;
						case "3" : icon="user_danger.png"; break;
						
						default: icon="unknown.png"; break;
					}
					
			        return '<center><img src="img/logger/' + icon + '"></center>';
			        
					
//			    	var icon="";
//			    	switch (value){
//						case "1" : icon="glyphicon-warning-sign"; break;
//						case "2" : icon="glyphicon-info-sign"; break;
//						case "3" : icon="glyphicon-minus-sign"; break;
//						default: icon="glyphicon-asterisk"; break;
//					}

//			        return '<i class="glyphicon ' + icon + '"></i> ';
			    }

				
</script>

<div id="custom-toolbar">
<div class="row" style="width:600px">
	<div class="col-md-6">
		<div class='form-group'>
			<label>
				Start Date :
			</label><br>
			<input type="text" id="start_date" name="start_date" class="form-control" data-date-format="yyyy-mm-dd 00:00:00" readonly class="form_datetime">
		</div>
	</div>
	<div class="col-md-6">
		<div class='form-group'>
			<label>
				End Date :
			</label><br>
			<input type="text" id="end_date" name="end_date" class="form-control" data-date-format="yyyy-mm-dd 23:59:59" readonly class="form_datetime">
		</div>
	</div>

</div>
</div>
					
			<table id="logger_tbl"
	           data-toggle="table"
	           data-striped=true
	           data-url="tab_user_logger_pagination.php"
	           data-search="true"
	           data-show-refresh="true"
	           data-show-toggle="true"
	           data-pagination="true"
	           data-click-to-select="true" data-single-select="true"
	           data-page-size="50"
			   data-sort-name="log_UTC_when" data-sort-order="desc"
	           data-side-pagination="server"
	           data-toolbar="#custom-toolbar" 
	           data-query-params="queryParamsLOGGER">

				<thead>
					<tr>
						<th data-field="state" data-checkbox="true" >
						</th>

						<th data-field="log_id" data-visible="false">
							log_id
						</th>
						
						<th data-field="log_UTC_when" data-formatter="date_formatter" data-sortable="true">
							Date UTC
						</th>
						
						<th data-field="log_type"  data-formatter="logtype_formatter" data-sortable="true">
							Type
						</th>
						
						<th data-field="log_text" data-sortable="true">
							Description
						</th>
						
						<th data-field="user_id" data-sortable="true">
							User
						</th>
						
						<th data-field="client_id" data-sortable="true">
							Client
						</th>
						
					</tr>
				</thead>
			</table	>
		
		

<?php
include ('template_bottom.php');
?>