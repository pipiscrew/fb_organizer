<?php
	session_start();

	if (!isset($_SESSION["u"])) {
		header("Location: login.php");
		exit ;
	}
	else {
		date_default_timezone_set("UTC");
		
		if ($_SESSION["login_expiration"] != date("Y-m-d"))
		{	header("Location: login.php");
			exit ;
		}
	}
?>
<!DOCTYPE html>
<html>
	<head>
	<!--http://glyphicons.bootstrapcheatsheets.com/-->
		<meta charset="UTF-8">
		<title>PipisCrew</title>
		<meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
		<link rel="shortcut icon" href="main.ico" type="image/png"/>
		<link rel="apple-touch-icon" href="main.ico" type="image/png"/>

		<!-- bootstrap 3.0.2 -->
		<link href="css/bootstrap.min.css" rel="stylesheet" type="text/css" />
		<!-- Theme style -->
		<link href="css/AdminLTE.css" rel="stylesheet" type="text/css" />
		<!-- Datatables style -->
		<link href="css/datatables/dataTables.bootstrap.css" rel="stylesheet" type="text/css" />
		<!-- Switch style -->
		<link href="css/bootstrap-switch.min.css" rel="stylesheet" type="text/css" />
		<!-- Date style -->
		<link href="css/bootstrap-datepicker.css" rel="stylesheet" type="text/css" />
		<!-- Datetime style -->
    	<link href="css/bootstrap-datetimepicker.min.css" rel="stylesheet" media="screen">
    	<!--Bootstrap Table-->
    	<link href="css/bootstrap-table.min.css" rel="stylesheet" type="text/css" />
		
		<!-- jQuery 2.0.2 -->
		<script src="js/jquery.min.js" type="text/javascript"></script>
		<!-- Bootstrap -->
		<script src="js/bootstrap.min.js" type="text/javascript"></script>

		
    <!-- Switch Script -->
    <script src="js/bootstrap-switch.min.js" type="text/javascript"></script>

    <!-- Date Script -->
    <script src="js/bootstrap-datepicker.js" type="text/javascript"></script>

    <!-- Datetime Script -->
    <script src="js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
    
    <!-- Datatables Script -->
    <script src="js/plugins/datatables/jquery.dataTables.js" type="text/javascript"></script>
    <script src="js/plugins/datatables/dataTables.bootstrap.js" type="text/javascript"></script>

    <!-- Validator Script -->
    <script src="js/jquery.validate.min.js" type="text/javascript"></script>
    
    <!--Bootstrap Table-->
    <script src="js/bootstrap-table.min.js" type="text/javascript"></script>
    
    <script src="js/bootstrap-chooser.js" type="text/javascript"></script>
        
<!-- label for validator -->
		<style>
			/*bootstrap striped table*/
			.table-striped tbody tr.highlight td {
				background-color: #B0BED9;
			}
			
			/*bootstrap-table selected row*/
			.fixed-table-container tbody tr.selected td	{ background-color: #B0BED9; }
			
			label.error { color: #FF0000; font-size: 11px; display: block; width: 100%; white-space: nowrap; float: none; margin: 8px 0 -8px 0; padding: 0!important; }
			
		
			/*progress*/
			.modal-backdrop { opacity: 0.7;	filter: alpha(opacity=70);	background: #fff; z-index: 2;}
			div.loading { position: fixed; margin: auto; top: 0; right: 0; bottom: 0; left: 0; width: 200px; height: 30px; z-index: 3; }
		
		</style>
		
				<script type="text/javascript">
					var loading = $('<div class="modal-backdrop"></div><div class="progress progress-striped active loading"><div class="progress-bar" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%">');
					var dont_close = $('<div class="modal-backdrop"></div><div class="progress progress-striped active loading"><div class="progress-bar" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%">');
//				window.onbeforeunload = function(){
//				  return 'Are you sure you want to leave?';
//				};


			

			//JQUERY START HERE////////////////////////////
			$(function() {
				//jQ custom validation
				$.validator.addMethod("greaterThanZero", function(value, element) {
				    return (value!=null && parseFloat(value) > 0);
				}, "* Amount must be greater than zero");			
			

				
			}); //jQuery ends
			
	
			function getSelected(selector) {
				var lines=null;
				
				$('#' + selector + ' > tbody  > tr').each(function() {
					if ($(this).hasClass('highlight')) {

						lines = $('td', $(this)).map(function(index, td) {
							return $(td).text();
						});

						return false;
						//return lines;
						//alert(lines[0] + ' ' + lines[1]);

						//alert($(this).html());
					}
				});

				return lines;
			}
			
			
				</script>
	</head>
	<!--<body class="skin-blue"> -->
	<body class="skin-black">
				

		<header class="header">
		
			<a href="" class="logo" style="font-size:3em">PipisCrew</a>
			<!-- Header Navbar: style can be found in header.less -->
			<nav class="navbar navbar-static-top" role="navigation">

				<!-- Sidebar toggle button-->
				<a href="#" class="navbar-btn sidebar-toggle" data-toggle="offcanvas" role="button"> <span class="sr-only">Toggle navigation</span> <span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span> </a>
				
<ul class="nav nav-pills pull-right" role="tablist">
<!--  <li  class="active"><a href="#">Home <span class="badge">42</span></a></li>
  <li ><a href="#">Profile</a></li>-->
  <li ><a href="#">Messages <span id="messages" class="badge"></span></a></li>
  <!--<li ><a href="#" id="logout">Logout</a></li>-->
</ul>

<!--				<div class="navbar-right">
					<a href="#" id="logout">Logout</a>
                </div>-->
			</nav>
		</header>
		<div class="wrapper row-offcanvas row-offcanvas-left">

			<aside class="left-side sidebar-offcanvas">
				<!-- sidebar: style can be found in sidebar.less -->
				<section class="sidebar">
					<!-- Sidebar user panel -->
					<div class="user-panel">
						<div class="pull-left info">

						</div>
					</div>

					<!-- sidebar menu: : style can be found in sidebar.less -->
					<ul class="sidebar-menu">

						<li class="active">
							<a href="index.php"> <i class="glyphicon glyphicon-random"></i> <span>Dashboard</span> </a>
						</li>
						
<?php if ($_SESSION['level']==9) { ?>

						<li class="treeview <?php if (isset($active_tab) && $active_tab=="user_levels") echo "active"?> ">
							<a href="#"> <i class="glyphicon glyphicon-th-large"></i> <span>User Levels</span> <i class="glyphicon pull-right glyphicon-chevron-right"></i> </a>
							<ul class="treeview-menu" style="display: none;">
								<li >
									<a href="tab_user_levels.php" style="margin-left: 10px;"><i class="glyphicon glyphicon-list"></i> List</a>
								</li>
								<li >
									<a href="tab_user_levels_details.php" style="margin-left: 10px;"><i class="glyphicon glyphicon-plus"></i> Create</a>
								</li>
							</ul>
						</li>

						<li class="treeview <?php if (isset($active_tab) && $active_tab=="users") echo "active"?> ">
							<a href="#"> <i class="glyphicon glyphicon-th-large"></i> <span>Users</span> <i class="glyphicon pull-right glyphicon-chevron-right"></i> </a>
							<ul class="treeview-menu" style="display: none;">
								<li >
									<a href="tab_users.php" style="margin-left: 10px;"><i class="glyphicon glyphicon-list"></i> List</a>
								</li>
								<li >
									<a href="tab_users_details.php" style="margin-left: 10px;"><i class="glyphicon glyphicon-plus"></i> Create</a>
								</li>
							</ul>
						</li>
						

						<li class="treeview <?php if (isset($active_tab) && $active_tab=="client_ratings") echo "active"?> ">
							<a href="#"> <i class="glyphicon glyphicon-th-large"></i> <span>Client Ratings</span> <i class="glyphicon pull-right glyphicon-chevron-right"></i> </a>
							<ul class="treeview-menu" style="display: none;">
								<li >
									<a href="tab_client_ratings.php" style="margin-left: 10px;"><i class="glyphicon glyphicon-list"></i> List</a>
								</li>
								<li >
									<a href="tab_client_ratings_details.php" style="margin-left: 10px;"><i class="glyphicon glyphicon-plus"></i> Create</a>
								</li>
							</ul>
						</li>

						<li class="treeview <?php if (isset($active_tab) && $active_tab=="client_sectors") echo "active"?> ">
							<a href="#"> <i class="glyphicon glyphicon-th-large"></i> <span>Client Sectors</span> <i class="glyphicon pull-right glyphicon-chevron-right"></i> </a>
							<ul class="treeview-menu" style="display: none;">
								<li >
									<a href="tab_client_sectors.php" style="margin-left: 10px;"><i class="glyphicon glyphicon-list"></i> List</a>
								</li>
								<li >
									<a href="tab_client_sectors_details.php" style="margin-left: 10px;"><i class="glyphicon glyphicon-plus"></i> Create</a>
								</li>
							</ul>
						</li>
						
						<li class="treeview <?php if (isset($active_tab) && $active_tab=="client_sector_subs") echo "active"?> ">
							<a href="#"> <i class="glyphicon glyphicon-th-large"></i> <span>Client Sub Sectors</span> <i class="glyphicon pull-right glyphicon-chevron-right"></i> </a>
							<ul class="treeview-menu" style="display: none;">
								<li >
									<a href="tab_client_sector_subs.php" style="margin-left: 10px;"><i class="glyphicon glyphicon-list"></i> List</a>
								</li>
								<li >
									<a href="tab_client_sector_subs_details.php" style="margin-left: 10px;"><i class="glyphicon glyphicon-plus"></i> Create</a>
								</li>
							</ul>
						</li>

						<li class="treeview <?php if (isset($active_tab) && $active_tab=="client_sources") echo "active"?> ">
							<a href="#"> <i class="glyphicon glyphicon-th-large"></i> <span>Client Sources</span> <i class="glyphicon pull-right glyphicon-chevron-right"></i> </a>
							<ul class="treeview-menu" style="display: none;">
								<li >
									<a href="tab_client_sources.php" style="margin-left: 10px;"><i class="glyphicon glyphicon-list"></i> List</a>
								</li>
								<li >
									<a href="tab_client_sources_details.php" style="margin-left: 10px;"><i class="glyphicon glyphicon-plus"></i> Create</a>
								</li>
							</ul>
						</li>
						
						<li class="treeview <?php if (isset($active_tab) && $active_tab=="countries") echo "active"?> ">
							<a href="#"> <i class="glyphicon glyphicon-th-large"></i> <span>Countries</span> <i class="glyphicon pull-right glyphicon-chevron-right"></i> </a>
							<ul class="treeview-menu" style="display: none;">
								<li >
									<a href="tab_countries.php" style="margin-left: 10px;"><i class="glyphicon glyphicon-list"></i> List</a>
								</li>
								<li >
									<a href="tab_countries_details.php" style="margin-left: 10px;"><i class="glyphicon glyphicon-plus"></i> Create</a>
								</li>
							</ul>
						</li>

						<li class="treeview <?php if (isset($active_tab) && $active_tab=="tax_offices") echo "active"?> ">
							<a href="#"> <i class="glyphicon glyphicon-th-large"></i> <span>Tax Offices</span> <i class="glyphicon pull-right glyphicon-chevron-right"></i> </a>
							<ul class="treeview-menu" style="display: none;">
								<li >
									<a href="tab_tax_offices.php" style="margin-left: 10px;"><i class="glyphicon glyphicon-list"></i> List</a>
								</li>
								<li >
									<a href="tab_tax_offices_details.php" style="margin-left: 10px;"><i class="glyphicon glyphicon-plus"></i> Create</a>
								</li>
							</ul>
						</li>


						<li class="treeview <?php if (isset($active_tab) && $active_tab=="user_working_hours") echo "active"?> ">
							<a href="#"> <i class="glyphicon glyphicon-th-large"></i> <span>User Woking Hours</span> <i class="glyphicon pull-right glyphicon-chevron-right"></i> </a>
							<ul class="treeview-menu" style="display: none;">
								<li >
									<a href="tab_user_working_hours.php" style="margin-left: 10px;"><i class="glyphicon glyphicon-list"></i> List</a>
								</li>
							</ul>
						</li>
						
						<li class="treeview <?php if (isset($active_tab) && $active_tab=="logger") echo "active"?> ">
							<a href="#"> <i class="glyphicon glyphicon-th-large"></i> <span>Logger</span> <i class="glyphicon pull-right glyphicon-chevron-right"></i> </a>
							<ul class="treeview-menu" style="display: none;">
								<li >
									<a href="tab_user_logger.php" style="margin-left: 10px;"><i class="glyphicon glyphicon-list"></i> List</a>
								</li>
							</ul>
						</li>
	
<?php } ?>

						<li class="treeview <?php if (isset($active_tab) && $active_tab=="leads") echo "active"?> ">
							<a href="#"> <i class="glyphicon glyphicon-th-large"></i> <span>Leads</span> <i class="glyphicon pull-right glyphicon-chevron-right"></i> </a>
							<ul class="treeview-menu" style="display: none;">
								<li >
									<a href="tab_leads.php" style="margin-left: 10px;"><i class="glyphicon glyphicon-list"></i> List</a>
								</li>
								<li >
									<a href="tab_leads_details.php" style="margin-left: 10px;"><i class="glyphicon glyphicon-plus"></i> Create</a>
								</li>
							</ul>
						</li>
						
						<li class="treeview <?php if (isset($active_tab) && $active_tab=="clients") echo "active"?> ">
							<a href="#"> <i class="glyphicon glyphicon-th-large"></i> <span>Clients</span> <i class="glyphicon pull-right glyphicon-chevron-right"></i> </a>
							<ul class="treeview-menu" style="display: none;">
								<li >
									<a href="tab_clients.php" style="margin-left: 10px;"><i class="glyphicon glyphicon-list"></i> List</a>
								</li>
<!--								<li >
									<a href="tab_clients_details.php" style="margin-left: 10px;"><i class="glyphicon glyphicon-plus"></i> Create</a>
								</li>-->
							</ul>
						</li>
						

						<li class="treeview <?php if (isset($active_tab) && $active_tab=="inclients") echo "active"?> ">
							<a href="#"> <i class="glyphicon glyphicon-th-large"></i> <span>Inactive Clients</span> <i class="glyphicon pull-right glyphicon-chevron-right"></i> </a>
							<ul class="treeview-menu" style="display: none;">
								<li >
									<a href="tab_inclients.php" style="margin-left: 10px;"><i class="glyphicon glyphicon-list"></i> List</a>
								</li>
							</ul>
						</li>

						<li class="treeview <?php if (isset($active_tab) && $active_tab=="users_vacations") echo "active"?> ">
							<a href="#"> <i class="glyphicon glyphicon-th-large"></i> <span>User Vacations</span> <i class="glyphicon pull-right glyphicon-chevron-right"></i> </a>
							<ul class="treeview-menu" style="display: none;">
								<li >
									<a href="tab_user_vacations.php" style="margin-left: 10px;"><i class="glyphicon glyphicon-list"></i> List</a>
								</li>
							</ul>
						</li>
					</ul>
				</section>
				<!-- /.sidebar -->
			</aside>
			<!-- Right side column. Contains the navbar and content of the page -->
			<aside class="right-side">

