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

$db                      = connect();

class vrecord {
	public $exp_id;
	public $category;
	public $is_head;

	public $jan;
	public $jan_id;
	public $jan_suggested;
	public $jan_paid;
	public $jan_unpaid;
	public $feb;
	public $feb_id;
	public $feb_suggested;
	public $feb_paid;
	public $feb_unpaid;
	public $march;
	public $march_id;
	public $march_suggested;
	public $march_paid;
	public $march_unpaid;
	public $april;
	public $april_id;
	public $april_suggested;
	public $april_paid;
	public $april_unpaid;
	public $may;
	public $may_id;
	public $may_suggested;
	public $may_paid;
	public $may_unpaid;
	public $june;
	public $june_id;
	public $june_suggested;
	public $june_paid;
	public $june_unpaid;
	public $july;
	public $july_id;
	public $july_suggested;
	public $july_paid;
	public $july_unpaid;
	public $aug;
	public $aug_id;
	public $aug_suggested;
	public $aug_paid;
	public $aug_unpaid;
	public $sept;
	public $sept_id;
	public $sept_suggested;
	public $sept_paid;
	public $sept_unpaid;
    public $oct;
    public $oct_id;
    public $oct_suggested;
	public $oct_paid;
	public $oct_unpaid;
	public $nov;
	public $nov_id;
	public $nov_suggested;
	public $nov_paid;
	public $nov_unpaid;
	public $dec;	
	public $dec_id;
	public $dec_suggested;
	public $dec_paid;
	public $dec_unpaid;
}

class balancerec {
	public $income;
	public $expense;
	public $net;
	
	public $paid;
	public $unpaid;
}

$ListOfBalanceRECS = array(balancerec);
$rec_balance=null;

//create 12 entries
for ($i=1;$i<13;$i++){
	$ListOfBalanceRECS[]= new balancerec();

	$ListOfBalanceRECS[$i]->income = (float) get_income4month($db,(string)$i);
	$ListOfBalanceRECS[$i]->paid = (float) 0;
	$ListOfBalanceRECS[$i]->unpaid = (float) 0;
}


$ListOfRECS = array(vrecord);
$rec=null;

$categories = getSet($db, "select expense_template_id,cat.expense_category_name as cat,subcat.expense_category_name as sub,price from expense_templates
 left join expense_categories as cat on cat.expense_category_id=expense_templates.expense_category_id
 left join expense_categories as subcat on subcat.expense_category_id=expense_templates.expense_sub_category_id
 order by cat,sub",null);

$prev_cat = "";

foreach($categories as $row) {
	$rec = new vrecord();
	
	$rec->exp_id = $row['expense_template_id'];
	
	if ($prev_cat != $row['cat'])
		{//$rec->category = $row['cat']." > ".$row['sub'];
			
			//add dummy main category row!!
			$rec->exp_id =0;
			$rec->category = $row['cat'];
			$rec->is_head = 1;
			$rec->jan  = $rec->feb  = $rec->march = $rec->april = $rec->may  = $rec->june  = $rec->july  = $rec->aug  = $rec->sept  = $rec->oct  = $rec->nov = $rec->dec = "";
			$ListOfRECS[] = $rec;
			//add dummy main category row!!
				
			//add the real record!!
			$rec = new vrecord();
			$rec->category = $row['sub'];
			$rec->exp_id = $row['expense_template_id'];
			//add the real record!!
		}
	else 
	{	$rec->category = $row['sub'];
	}

		//q months 
		$y = date('Y');
		$m = date('m');
		
		$jan = getRow($db,"select expense_id,sum(cost) as cost from expenses where misc_daterec is null and  expense_template_id = {$row['expense_template_id']} and daterec between '{$y}-01-01' and '".get_end_of_the_month("01", $y)."' limit 1",null);
		$rec->jan = add_thousand($jan['cost'],2 );
		$rec->jan_id = $jan['expense_id'];
		$rec->jan_suggested = $row['price'];
		
		if ($jan['cost']>0)
			{$ListOfBalanceRECS[1]->paid +=(float) $jan['cost']; //$rec->jan;
			$ListOfBalanceRECS[1]->expense += (float) $jan['cost'];
			}
		else 
		{
			$ListOfBalanceRECS[1]->expense += $rec->jan_suggested;
			
			if ($row['price']>0)
				$ListOfBalanceRECS[1]->unpaid +=$rec->jan_suggested;
		}
		
		$feb = getRow($db,"select expense_id,sum(cost) as cost from expenses where misc_daterec is null and  expense_template_id = {$row['expense_template_id']} and daterec between '{$y}-02-01' and '".get_end_of_the_month("02", $y)."' limit 1",null);
		$rec->feb = add_thousand($feb['cost'],2 );
		$rec->feb_id = $feb['expense_id'];
		$rec->feb_suggested = $row['price'];
		
		if ($feb['cost']>0)
		{$ListOfBalanceRECS[2]->expense += (float) $feb['cost'];
				$ListOfBalanceRECS[2]->paid+=(float) $feb['cost']; //$rec->feb;
		}
		else
		{$ListOfBalanceRECS[2]->expense +=$rec->feb_suggested;
			if ($row['price']>0)
			$ListOfBalanceRECS[2]->unpaid +=$rec->feb_suggested;
		}

		$march = getRow($db,"select expense_id,sum(cost) as cost from expenses where misc_daterec is null and  expense_template_id = {$row['expense_template_id']} and daterec between '{$y}-03-01' and '".get_end_of_the_month("03", $y)."' limit 1",null);
		$rec->march = add_thousand($march['cost'],2 );
		$rec->march_id = $march['expense_id'];
		$rec->march_suggested = $row['price'];
		
		if ($march['cost']>0)
		{
		$ListOfBalanceRECS[3]->expense += (float) $march['cost'];	
		$ListOfBalanceRECS[3]->paid+=(float) $march['cost']; //$rec->march;
		}
		else 
		{
			$ListOfBalanceRECS[3]->expense += $rec->march_suggested;
			
			if ($row['price']>0)
			$ListOfBalanceRECS[3]->unpaid+=$rec->march_suggested;
		}
			
			
		$april = getRow($db,"select expense_id,sum(cost) as cost from expenses where misc_daterec is null and  expense_template_id = {$row['expense_template_id']} and daterec between '{$y}-04-01' and '".get_end_of_the_month("04", $y)."' limit 1",null);
		$rec->april = add_thousand($april['cost'],2 );
		$rec->april_id = $april['expense_id'];
		$rec->april_suggested = $row['price'];
		
		if ($april['cost']>0)
		{$ListOfBalanceRECS[4]->expense += (float) $april['cost'];
			$ListOfBalanceRECS[4]->paid+=(float) $april['cost'];
			}
		else 
		{$ListOfBalanceRECS[4]->expense +=$rec->april_suggested;
			if ($row['price']>0)
			$ListOfBalanceRECS[4]->unpaid+=$rec->april_suggested;
		}
		

			
		$may = getRow($db,"select expense_id,sum(cost) as cost from expenses where misc_daterec is null and  expense_template_id = {$row['expense_template_id']} and daterec between '{$y}-05-01' and '".get_end_of_the_month("05", $y)."' limit 1",null);
		$rec->may = add_thousand($may['cost'],2 );
		$rec->may_id = $may['expense_id'];
		$rec->may_suggested = $row['price'];
		
		if ($may['cost']>0)
		{
			$ListOfBalanceRECS[5]->expense += (float) $may['cost'];
			$ListOfBalanceRECS[5]->paid+=(float) $may['cost']; //$rec->may;
		}
			
		else
		{$ListOfBalanceRECS[5]->expense +=$rec->may_suggested;
			if ($row['price']>0)
			$ListOfBalanceRECS[5]->unpaid+=$rec->may_suggested;
		}
		
			
		$june = getRow($db,"select expense_id,sum(cost) as cost from expenses where misc_daterec is null and  expense_template_id = {$row['expense_template_id']} and daterec between '{$y}-06-01' and '".get_end_of_the_month("06", $y)."' limit 1",null);
		$rec->june = add_thousand($june['cost'],2 );
		$rec->june_id = $june['expense_id'];
		$rec->june_suggested = $row['price'];
		
		if ($june['cost']>0)
		{$ListOfBalanceRECS[6]->expense += (float) $june['cost'];
			$ListOfBalanceRECS[6]->paid+= (float) $june['cost']; //$rec->june;
			}
		else {
			$ListOfBalanceRECS[6]->expense +=$rec->june_suggested;
			if ($row['price']>0)
			$ListOfBalanceRECS[6]->unpaid+=$rec->june_suggested;
		}
			
			
		$july = getRow($db,"select expense_id,sum(cost) as cost from expenses where misc_daterec is null and  expense_template_id = {$row['expense_template_id']} and daterec between '{$y}-07-01' and '".get_end_of_the_month("07", $y)."' limit 1",null);
		$rec->july = add_thousand($july['cost'],2 );
		$rec->july_id = $july['expense_id'];
		$rec->july_suggested = $row['price'];
		
		if ($july['cost']>0)
		{$ListOfBalanceRECS[7]->expense += (float) $july['cost'];
				$ListOfBalanceRECS[7]->paid+=(float) $july['cost']; //$rec->july;
		}
		else
		{
			$ListOfBalanceRECS[7]->expense += $rec->july_suggested;
			if ($row['price']>0)
			$ListOfBalanceRECS[7]->unpaid+=$rec->july_suggested;
		}
			
			
		$aug = getRow($db,"select expense_id,sum(cost) as cost from expenses where misc_daterec is null and  expense_template_id = {$row['expense_template_id']} and daterec between '{$y}-08-01' and '".get_end_of_the_month("08", $y)."' limit 1",null);
		$rec->aug = add_thousand($aug['cost'],2 );
		$rec->aug_id = $aug['expense_id'];
		$rec->aug_suggested = $row['price'];
		
		if ($aug['cost']>0)
		{$ListOfBalanceRECS[8]->expense += (float) $aug['cost'];
				$ListOfBalanceRECS[8]->paid+=(float) $aug['cost'];//$rec->aug;
		}
		else
		{
			$ListOfBalanceRECS[8]->expense +=$rec->aug_suggested;
			if ($row['price']>0)
			$ListOfBalanceRECS[8]->unpaid+=$rec->aug_suggested;
		}			
			
		$sept = getRow($db,"select expense_id,sum(cost) as cost from expenses where misc_daterec is null and  expense_template_id = {$row['expense_template_id']} and daterec between '{$y}-09-01' and '".get_end_of_the_month("09", $y)."' limit 1",null);
		$rec->sept = add_thousand($sept['cost'],2 );
		$rec->sept_id = $sept['expense_id'];
		$rec->sept_suggested = $row['price'];
		
		if ($sept['cost']>0)
		{$ListOfBalanceRECS[9]->expense += (float) $sept['cost'];
			$ListOfBalanceRECS[9]->paid+=(float) $sept['cost'];//$rec->sept;
		}
		else 
		{$ListOfBalanceRECS[9]->expense += $rec->sept_suggested;
			if ($row['price']>0)
			$ListOfBalanceRECS[9]->unpaid+=$rec->sept_suggested;
		}		
			
			
		$oct = getRow($db,"select expense_id,sum(cost) as cost from expenses where misc_daterec is null and  expense_template_id = {$row['expense_template_id']} and daterec between '{$y}-10-01' and '".get_end_of_the_month("10", $y)."' limit 1",null);
		$rec->oct = add_thousand($oct['cost'],2 );
		$rec->oct_id = $oct['expense_id'];
		$rec->oct_suggested = $row['price'];
		
		if ($oct['cost']>0)
		{$ListOfBalanceRECS[10]->expense += (float) $oct['cost'];
			$ListOfBalanceRECS[10]->paid+=(float) $oct['cost'];//$rec->oct;
		}
		else
		{$ListOfBalanceRECS[10]->expense += $rec->oct_suggested;
			if ($row['price']>0)
			$ListOfBalanceRECS[10]->unpaid+=$rec->oct_suggested;
		}	
			
			
		$nov = getRow($db,"select expense_id,sum(cost) as cost from expenses where misc_daterec is null and  expense_template_id = {$row['expense_template_id']} and daterec between '{$y}-11-01' and '".get_end_of_the_month("11", $y)."' limit 1",null);
		$rec->nov = add_thousand($nov['cost'],2 );
		$rec->nov_id = $nov['expense_id'];
		$rec->nov_suggested = $row['price'];
		
		if ($nov['cost']>0)
		{$ListOfBalanceRECS[11]->expense += (float) $nov['cost'];
			$ListOfBalanceRECS[11]->paid+=(float) $nov['cost'];//$rec->nov;
		}
		else
		{$ListOfBalanceRECS[11]->expense +=$rec->nov_suggested;
			if ($row['price']>0)
			$ListOfBalanceRECS[11]->unpaid+=$rec->nov_suggested;
		}	

			
		$dec = getRow($db,"select expense_id,sum(cost) as cost from expenses where misc_daterec is null and  expense_template_id = {$row['expense_template_id']} and daterec between '{$y}-12-01' and '".get_end_of_the_month("12", $y)."' limit 1",null);
		$rec->dec = add_thousand($dec['cost'],2 );
		$rec->dec_id = $dec['expense_id'];
		$rec->dec_suggested = $row['price'];
		
		if ($dec['cost']>0)
		{$ListOfBalanceRECS[12]->expense += (float) $dec['cost'];	
		$ListOfBalanceRECS[12]->paid+=(float) $dec['cost'];//$rec->dec;
		}
		else
		{$ListOfBalanceRECS[12]->expense +=$rec->dec_suggested;
			if ($row['price']>0)
			$ListOfBalanceRECS[12]->unpaid+=$rec->dec_suggested;
		}	
		
			
		//q months 
	
	
//when all -1 ++ without price dont add it!!
if ($rec->jan_suggested == -1 && $rec->jan == 0 &&
	$rec->feb_suggested == -1 && $rec->feb == 0 &&
	$rec->march_suggested == -1 && $rec->march == 0 &&
	$rec->april_suggested == -1 && $rec->april == 0 &&
	$rec->may_suggested == -1 && $rec->may == 0 &&
	$rec->june_suggested == -1 && $rec->june == 0 &&
	$rec->july_suggested == -1 && $rec->july == 0 &&
	$rec->aug_suggested == -1 && $rec->aug == 0 &&
	$rec->sept_suggested == -1 && $rec->sept == 0 &&
	$rec->oct_suggested == -1 && $rec->oct == 0 &&
	$rec->nov_suggested == -1 && $rec->nov == 0 &&
	$rec->dec_suggested == -1 && $dec->nov == 0)
continue;

		$ListOfRECS[] = $rec;
					
		$prev_cat = $row['cat'];
}


		
//////////////////////////////// MISC
//$ListOfRECS_MISC = array(vrecord);

//$tmp_addnew_button = "<a style='width:100%' onclick='misc_add_new({month})' class='btn btn-primary btn-xs'>Add new</a>";
//$tmp_btns_record = "<a style='width:100%' onclick='misc_edit({rec_id},{month})' class='btn btn-primary btn-xs'>{title}</a>";
//$tmp="";

//////////////////////////// MISC
//using div -> so dont squeeze columns width
$tmp_addnew_button = "<div style='width:100%'><a style='width:100%' onclick='misc_add_new({month})' class='btn btn-primary btn-xs'>Add new</a></div>";
$tmp_btns_record = "<div style='width:100%'><a style='width:100%' onclick='misc_edit({rec_id},{month})' class='btn btn-{is_paid} btn-xs'>{title}</a></div>";
$tmp="";


$row_tmp="";
$y = date('Y');
$misc = array();
$xmonth ="";

for ($i=1;$i<13;$i++){
		if ($i<10)
			$xmonth="0".$i;
		else 
			$xmonth=$i;
			
	//init		
	$tmp = $row_tmp = "";
		
	$rows = getSet($db,"select * from expenses where misc_daterec between '{$y}-{$xmonth}-01' and '".get_end_of_the_month($xmonth, $y)."'",null);

	$row_tmp = str_replace("{month}",$xmonth,$tmp_addnew_button);

	foreach($rows as $row) {
		//balance
		if ($row['misc_is_paid']==1) //only when paid!
			$ListOfBalanceRECS[$i]->paid +=(float) $row['cost'];
			else 
			$ListOfBalanceRECS[$i]->unpaid +=(float) $row['cost'];
			
		//hotfix 21/1
//		if ($row['misc_is_paid']!=1) //only when paid!
{			$ListOfBalanceRECS[$i]->expense += (float) $row['cost'];

		
		}
		
		$tmp = str_replace("{month}",$xmonth,$tmp_btns_record);
		$tmp = str_replace("{is_paid}",$row['misc_is_paid']==1?"success":"danger",$tmp);
		$tmp = str_replace("{rec_id}",$row['expense_id'],$tmp);
//		$tmp = str_replace("{title}",add_thousand($row['cost'],2 ) . " " . $row['misc_title'],$tmp);
		$tmp = str_replace("{title}",add_thousand($row['cost'],2 ) . " " . $row['misc_title'],$tmp);
		
		$row_tmp .= $tmp;
	}
	
	
	$misc[] = $row_tmp;
	

}

//generate NET + format with euro!
for ($i=1;$i<13;$i++){
		$ListOfBalanceRECS[$i]->net =  add_thousand($ListOfBalanceRECS[$i]->income - $ListOfBalanceRECS[$i]->expense,2);
	$ListOfBalanceRECS[$i]->income = add_thousand($ListOfBalanceRECS[$i]->income,2) ;
	$ListOfBalanceRECS[$i]->expense = add_thousand($ListOfBalanceRECS[$i]->expense,2) ;
	
//	$ListOfBalanceRECS[$i]->net =  add_thousand($ListOfBalanceRECS[$i]->income - $ListOfBalanceRECS[$i]->expense,2) . " &euro;";
//	$ListOfBalanceRECS[$i]->income = add_thousand($ListOfBalanceRECS[$i]->income,2) . " &euro;";
//	$ListOfBalanceRECS[$i]->expense = add_thousand($ListOfBalanceRECS[$i]->expense,2) . " &euro;";
}

//		var_dump($ListOfBalanceRECS[1]->expense);
//		exit;

$pay_methods = getSet($db, "select * from transaction_methods",null);

function add_thousand($val, $decimal)
{
	if ($val==null)
		return 0;
	else 
		return number_format( $val , $decimal , ',' , '.' );
}

function get_income4month($db,$month_no){
	
	if (strlen($month_no)==1)
		$month="0".$month_no;
		
	$income = getScalar($db,"select sum(offer_total_amount) from offers where is_paid=1 and is_paid_when between '".date("Y")."-{$month}-01' and '".get_end_of_the_month($month_no, date("Y"))."'");
	
	return $income;
	
	$income = getSet($db,"select gen_subtotal,gen_fee_discount_money,country_id from offers where is_paid=1 and is_paid_when between '".date("Y")."-{$month}-01' and '".get_end_of_the_month($month_no, date("Y"))."'");
 

//echo "select gen_subtotal,offer_discount,country_id from offers where is_paid=1 and is_paid_when between '".date("Y")."-{$month_no}-01' and '".get_end_of_the_month($month_no, date("Y"))."'";
//exit;	
	foreach($income as $prop) {
		$subtotal = $prop["gen_subtotal"];
		$discount = $prop["gen_fee_discount_money"];
		
		$disc_calc = ($subtotal - $discount);
		
		//tax
		$tax_val                     = 0;
		if($prop["country_id"] == 5){
			$tax_val = $disc_calc * 0.2;
		}

		//total
		$total    = $tax_val + $disc_calc;

//		//subtotal
//		$subototal_pure4db           = $prop["gen_subtotal"];
//
//		//ds
//		$discount_perc               = $prop["offer_discount"];
//
//		//dss
//		$discount                    = $subototal_pure4db * ($discount_perc / 100);
//		$subtotal                    = $subototal_pure4db;
//
//		$disc_calc = ($subtotal - $discount);
//		//tax
//		$tax_val                     = 0;
//		if($prop["country_id"] == 5){
//			$tax_val = $disc_calc * 0.2;
//		}
//
//		//total
//		$total    += $tax_val + $disc_calc;
	}	

	
	return $total;
}




$chart_row_setup = array();

$chart_columns_setup= array();
$chart_columns_setup[]="Type";
$chart_columns_setup[]="Income";
$chart_columns_setup[]="Expenses";
$chart_columns_setup[]="NET";

//chart legend
$chart_row_setup[0] = $chart_columns_setup;

for ($i=1;$i<13;$i++){
	
	if ($i>$m)
	{
		$col_vals= array();
		$col_vals[]=monthName($i);

		$ListOfBalanceRECS[$i]->income= 0;
		$ListOfBalanceRECS[$i]->expense=0;
		$ListOfBalanceRECS[$i]->net=0;

		$col_vals[]=(float)$ListOfBalanceRECS[$i]->income;
		$col_vals[]=(float)$ListOfBalanceRECS[$i]->expense;
		$col_vals[]=(float)$ListOfBalanceRECS[$i]->net;

		$chart_row_setup[] = $col_vals;
	}
	else {
		$col_vals= array();
		$col_vals[]=monthName($i);

		$ListOfBalanceRECS[$i]->income= str_replace(".","",$ListOfBalanceRECS[$i]->income);
		$ListOfBalanceRECS[$i]->income= str_replace(",",".",$ListOfBalanceRECS[$i]->income);
		$ListOfBalanceRECS[$i]->expense=str_replace(".","",$ListOfBalanceRECS[$i]->expense);
		$ListOfBalanceRECS[$i]->expense=str_replace(",",".",$ListOfBalanceRECS[$i]->expense);
		$ListOfBalanceRECS[$i]->net=str_replace(".","",$ListOfBalanceRECS[$i]->net);
		$ListOfBalanceRECS[$i]->net=str_replace(",",".",$ListOfBalanceRECS[$i]->net);
			
		
		$col_vals[]=(float)$ListOfBalanceRECS[$i]->income;
		$col_vals[]=(float)$ListOfBalanceRECS[$i]->expense;
		$col_vals[]=(float)$ListOfBalanceRECS[$i]->net;

		$chart_row_setup[] = $col_vals;
	}

}


function monthName($month_int) {
	$month_int = (int)$month_int;
	$timestamp = mktime(0, 0, 0, $month_int);
	return date("F", $timestamp);
}

?>



    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript">
 
      google.load("visualization", "1.1", {packages:["bar"]});
      google.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = google.visualization.arrayToDataTable(<?= json_encode($chart_row_setup);?>);
 
        var options = {
          chart: {
            title: 'Company Performance',
            subtitle: 'Sales, Expenses and Profit',
          }
        };
 
        var chart = new google.charts.Bar(document.getElementById('columnchart_material'));
 
         chart.draw(data, options);
        }
      </script>  


<script>
	    
	$(function (){
		
			///////////////////////////////////////////////////////////// FILL pay_methods
			var jArray_pay_methods =   <?php echo json_encode($pay_methods); ?>;

			var combo_pay_methods = "<option value='0'></option>";
			for (var i = 0; i < jArray_pay_methods.length; i++)
			{
				combo_pay_methods += "<option value='" + jArray_pay_methods[i]["transaction_method_id"] + "'>" + jArray_pay_methods[i]["transaction_method_name"] + "</option>";
			}

			$("[name=pay_method],[name=pay_method_two]").html(combo_pay_methods);
			$("[name=pay_method],[name=pay_method_two]").change(); //select row 0 - no conflict on POST validation @ PHP
			///////////////////////////////////////////////////////////// FILL pay_methods
	
		    $('[name=daterec],[name=misc_daterec_two]').datetimepicker({
		        weekStart: 1,
		        todayBtn:  1,
				autoclose: 1,
				todayHighlight: 1,
				startView: 2,
				minView: 2,
				forceParse: 1
		    });
		    
		    
			$("[name='misc_is_paid_two']").bootstrapSwitch();

			var jArray_balance =   <?php echo json_encode($ListOfBalanceRECS); ?>;
			if (jArray_balance)
			{
				
				var in_row ="<tr><td>Income : </td>";
				var out_row ="<tr><td>Expenses : </td>";
				var net_row ="<tr><td>NET : </td>";
				
				for(var no in jArray_balance)	
				{
					//if is header!
					if (typeof(jArray_balance[no]) == "string")
						continue;
						
					in_row+= "<td>"+jArray_balance[no]["income"]+" &euro;</td>";
					out_row+= "<td>"+jArray_balance[no]["expense"]+" &euro;</td>";
					net_row+= "<td>"+jArray_balance[no]["net"]+" &euro;</td>";
				}
				
				in_row +="</tr>";
				out_row +="</tr>";
				net_row +="</tr>";
			
				$("#balance_rows").html(in_row+out_row+net_row);	
			}
			
			//http://wenzhixin.net.cn/p/bootstrap-table/docs/examples.html#via-javascript-table
			$('#balance_tbl').bootstrapTable();
			
			
			
			


			var jArray = <?php echo json_encode($ListOfRECS); ?>;
			var jArray_MISC = <?php echo json_encode($misc); ?>;
		
			if (jArray)
			{
				var r ="";
				for(var no in jArray)	
				{
					//the first dummy record
					if (jArray[no]=="vrecord")
						continue;
					
					//template_id for table #expense_templates#
					r+= "<tr><td>"+jArray[no]["exp_id"]+"</td>";
					//each button has the ID for table #EXPENSES#

					//CATEGORY
					if ( jArray[no]["is_head"] == 1)
						{r+="<td><strong>"+jArray[no]["category"]+"</strong></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>";
						continue;
						}
					else 
					{
						var count_cost_overall = convert_to_valid_decimal(jArray[no]["jan"]) + convert_to_valid_decimal(jArray[no]["feb"]) + convert_to_valid_decimal(jArray[no]["march"]) +
						convert_to_valid_decimal(jArray[no]["april"]) + convert_to_valid_decimal(jArray[no]["may"]) + convert_to_valid_decimal(jArray[no]["june"]) + convert_to_valid_decimal(jArray[no]["july"]) +
						convert_to_valid_decimal(jArray[no]["aug"]) + convert_to_valid_decimal(jArray[no]["sept"]) + convert_to_valid_decimal(jArray[no]["oct"]) +  convert_to_valid_decimal(jArray[no]["nov"]) + convert_to_valid_decimal(jArray[no]["dec"]);
						
						if (count_cost_overall>0)
							r+="<td><div style='margin-left:20px'>"+jArray[no]["category"]+" <span class='badge alert-info'>" + format_decimal(count_cost_overall) + "</span></div></td>";
						else 
							r+="<td><div style='margin-left:20px'>"+jArray[no]["category"]+"</div></td>";
							

					}
					
					
			
					////////////////////////////////////////////
					r+= create_button_for_month(jArray[no]["jan_suggested"],jArray[no]["jan"],jArray[no]["jan_id"],jArray[no]["exp_id"],"01");
					r+= create_button_for_month(jArray[no]["feb_suggested"],jArray[no]["feb"],jArray[no]["feb_id"],jArray[no]["exp_id"],"02");
					r+= create_button_for_month(jArray[no]["march_suggested"],jArray[no]["march"],jArray[no]["march_id"],jArray[no]["exp_id"],"03");
					r+= create_button_for_month(jArray[no]["april_suggested"],jArray[no]["april"],jArray[no]["april_id"],jArray[no]["exp_id"],"04");
					r+= create_button_for_month(jArray[no]["may_suggested"],jArray[no]["may"],jArray[no]["may_id"],jArray[no]["exp_id"],"05");
					r+= create_button_for_month(jArray[no]["june_suggested"],jArray[no]["june"],jArray[no]["june_id"],jArray[no]["exp_id"],"06");
					r+= create_button_for_month(jArray[no]["july_suggested"],jArray[no]["july"],jArray[no]["july_id"],jArray[no]["exp_id"],"07");
					r+= create_button_for_month(jArray[no]["aug_suggested"],jArray[no]["aug"],jArray[no]["aug_id"],jArray[no]["exp_id"],"08");
					r+= create_button_for_month(jArray[no]["sept_suggested"],jArray[no]["sept"],jArray[no]["sept_id"],jArray[no]["exp_id"],"09");
					r+= create_button_for_month(jArray[no]["oct_suggested"],jArray[no]["oct"],jArray[no]["oct_id"],jArray[no]["exp_id"],"10");
					r+= create_button_for_month(jArray[no]["nov_suggested"],jArray[no]["nov"],jArray[no]["nov_id"],jArray[no]["exp_id"],"11");
					r+= create_button_for_month(jArray[no]["dec_suggested"],jArray[no]["dec"],jArray[no]["dec_id"],jArray[no]["exp_id"],"12");
					r+="</tr>";

				}
				
				//////////////////////////// MISC
				r+="<tr><td></td><td><strong>Miscellaneous</strong></td>";
				for(var no in jArray_MISC)	
				{
					r+="<td>" + jArray_MISC[no] + "</td>";
				}
				
				r+="</tr>"
				//////////////////////////// MISC
				
						console.log(jArray_balance);
				//////////////////////////// PAID + UNPAID (footer rows)
				r+="<tr><td></td><td><strong>Unpaid</strong></td>";
				for(var no in jArray_balance)
				{
					//if is header!
					if (typeof(jArray_balance[no]) == "string")
						continue;
						
					r+="<td>" + jArray_balance[no]["unpaid"] + "</td>";
				}
				r+="</tr>"
				
				r+="<tr><td></td><td><strong>Paid</strong></td>";
				for(var no in jArray_balance)
				{
					//if is header!
					if (typeof(jArray_balance[no]) == "string")
						continue;
						
					r+="<td>" + jArray_balance[no]["paid"] + "</td>";
				}
				
				r+="</tr>"
				//////////////////////////// PAID + UNPAID (footer rows)		

				
				$("#exp_rows").html(r);
			}
			
			/////////////////////////////////////////////////////used only to sum up the category item
			function format_decimal(n, sep, decimals) {
			    sep = sep || "."; // Default to period as decimal separator
			    decimals = decimals || 2; // Default to 2 decimals

			    return n.toLocaleString().split(sep)[0]
			        + sep
			        + n.toFixed(decimals).split(sep)[1];
			}

			
			function convert_to_valid_decimal(price){
				if (!price)
					return 0.00;
				else 
					{
						var x = price.replace('.',''); //remove thousand inserted by PHP
						x  = x.replace(',','.'); //replace , to . (is for decimal) (js flavor)
						
						return parseFloat(x);
					}
							
//				console.log(price);
//				var o = price.split('.');
//				console.log(o.length);
//				
//				if (o.length == 3)
//					console.log(price.replace('.',''));
			}
			/////////////////////////////////////////////////////used only to sum up the category item
			
			
			//http://wenzhixin.net.cn/p/bootstrap-table/docs/examples.html#via-javascript-table
			$('#expenses_tbl').bootstrapTable();
		
		
		    ////////////////////////////////////////
		    // MODAL FUNCTIONALITIES [START]
		    //when modal closed, hide the warning messages + reset
		    $('#modalEXPENSES').on('hidden.bs.modal', function() {
		        //when close - clear elements
		        $('#formEXPENSES').trigger("reset");
		 
		        //clear validator error on form
		        validatorEXPENSES.resetForm();
		    });
		 
		    //functionality when the modal already shown and its long, when reloaded scroll to top
		    $('#modalEXPENSES').on('shown.bs.modal', function() {
		        $(this).animate({
		            scrollTop : 0
		        }, 'slow');
		    });
		    // MODAL FUNCTIONALITIES [END]
		    ////////////////////////////////////////
				    
			///////////////////////////////////// add custom validation
			//validate currency
			$.validator.addMethod('currency', function(value, element, regexp)
				{
					var re = /^\d{1,9}(\.\d{1,2})?$/;
					return this.optional(element) || re.test(value);
				}, '');

			
		    var validatorEXPENSES = $("#formEXPENSES").validate({
		        rules : {
		             
		             cost : {required : true,
			                currency: true },
			          daterec : {required : true},
			          pay_method : {required : true,
			                greaterThanZero : true  }


		        },
		        messages : {
		            daterec : 'Required Field',
		            pay_method : 'Required Field',
		            cost : 'Required Field ex. 34.08'
		        }
		    });
		    
			////////////////////////////////////////
			// MODAL SUBMIT aka save & update button
			$('#formEXPENSES').submit(function(e) {
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
							location.reload(true);
			            else
			                alert("ERROR");
			        },
			        error: function(jqXHR, textStatus, errorThrown)
			        {
			            alert("ERROR - connection error");
			        }
			    });
			});
			

				$('#bntDelete_EXPENSES').on('click', function(e) {
					e.preventDefault();
					
					if (!confirm("Unpaid current record?"))
						return;
						
					var x = $("#expensesFORM_updateID").val();
					
					if (x)
						delete_EXPENSE(x);
				});
				
				//delete button_two - delete record
				function delete_EXPENSE(rec_id){
					loading.appendTo(formEXPENSES);
					
				    $.ajax(
				    {
				        url : "tab_dashboard_admin_big_brother_delete.php",
				        type: "POST",
				        data : { expense_id : rec_id },
				        success:function(data, textStatus, jqXHR)
				        {
				        	if (data=='00000')
							{
								location.reload(true);
							}
							else
							{
								loading.remove();
								alert("ERROR - Cant delete the record.");
							}
				        },
				        error: function(jqXHR, textStatus, errorThrown)
				        {
				        	loading.remove();
				            alert("ERROR");
				        }
				    });
				}
				
	/////////////////////////////////////////////////////////////////////////////////////////////////////
	/////////////////////////////// MISC MODAL
	/////////////////////////////////////////////////////////////////////////////////////////////////////
	
			    ////////////////////////////////////////
			    // MODAL FUNCTIONALITIES [START]
			    //when modal closed, hide the warning messages + reset
			    $('#modalEXPENSES_two').on('hidden.bs.modal', function() {
			        //when close - clear elements
			        $('#formEXPENSES_two').trigger("reset");
			 
			        //clear validator error on form
			        validatorEXPENSES_two.resetForm();
			    });
			 
			    //functionality when the modal already shown and its long, when reloaded scroll to top
			    $('#modalEXPENSES_two').on('shown.bs.modal', function() {
			        $(this).animate({
			            scrollTop : 0
			        }, 'slow');
			    });
			    // MODAL FUNCTIONALITIES [END]
			    ////////////////////////////////////////
    
				    
			    //jquery.validate.min.js
				var validatorEXPENSES_two = $("#formEXPENSES_two").validate(
					{
						rules :
						{
							misc_title_two :
							{
								required : true
							},
							cost_two :
							{
								required : true,
								currency: true
							},
							misc_daterec_two :
							{
								required : true
							},
							pay_method_two :
							{
								required : true,
								greaterThanZero : true
							}
						},
						messages :
						{
							misc_daterec_two : 'Required Field',
							pay_method_two : 'Required Field',
							misc_title_two : 'Required Field',
							cost_two : 'Required Field ex. 34.08'
						}
					});
								    
					////////////////////////////////////////
					// MODAL SUBMIT aka save & update button
					$('#formEXPENSES_two').submit(function(e) {
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
					    $('#modalEXPENSES_two').modal('toggle');
					 
					    $.ajax(
					    {
					        url : formURL,
					        type: "POST",
					        data : postData,
					        success:function(data, textStatus, jqXHR)
					        {
					        	console.log(data);
					            if (data=="00000")
									location.reload(true);
					            else
					                alert("ERROR");
					        },
					        error: function(jqXHR, textStatus, errorThrown)
					        {
					            alert("ERROR - connection error");
					        }
					    });
					});
					

				$('#bntDelete_EXPENSES_two').on('click', function(e) {
					e.preventDefault();
					
					if (!confirm("Delete current record?"))
						return;
						
					var x = $("#expensesFORM_updateID_two").val();
					
					if (x)
						delete_EXPENSE_two(x);
				});
				
				//delete button_two - delete record
				function delete_EXPENSE_two(rec_id){
					loading.appendTo(formEXPENSES_two);
					
				    $.ajax(
				    {
				        url : "tab_dashboard_admin_big_brother_delete.php",
				        type: "POST",
				        data : { expense_id : rec_id },
				        success:function(data, textStatus, jqXHR)
				        {
				        	if (data=='00000')
							{
								location.reload(true);
							}
							else
							{
								loading.remove();
								alert("ERROR - Cant delete the record.");
							}
				        },
				        error: function(jqXHR, textStatus, errorThrown)
				        {
				        	loading.remove();
				            alert("ERROR");
				        }
				    });
				}
				
					
	}) //jQuery ends
	
	function create_button_for_month(month_suggested,month,month_id,template_id,month_no){
			var template_btn = "<a style='width:100%' onclick='view_cost({suggested},\"{price}\",{exp_id},{edit},{template_id},{month_no});' class='btn btn-{paid} btn-xs'>{pay_price}</a>";
	
			var price_month = 0 ;
			
			if(month)
				 price_month = month; //.replace(',',".");
	
			var month_cell = template_btn.replace('{suggested}',month_suggested);
			month_cell = month_cell.replace('{price}',price_month);
			month_cell = month_cell.replace('{exp_id}',month_id);
			month_cell = month_cell.replace('{template_id}',template_id);
			month_cell = month_cell.replace('{month_no}',month_no);
			
			if (month==0){
				month_cell = month_cell.replace('{pay_price}',month_suggested);
				month_cell = month_cell.replace('{paid}',"danger");
				month_cell = month_cell.replace('{edit}',0);
			}
				
			else{
				month_cell = month_cell.replace('{pay_price}',price_month);
				month_cell = month_cell.replace('{paid}',"success");
				month_cell = month_cell.replace('{edit}',1);
			} 
				
			var curr_month = <?= $m+1?>;
			
			if (month_no > curr_month)
			return "<td></td>";
			else 
			return "<td>"+month_cell+"</td>";
	}
	
	function view_cost(suggested,price,rec_id,is_edit,template_id,month_no)
	{
	
		$('[name=daterec]').datetimepicker('setStartDate', '<?=date("Y")?>-'+month_no+'-01');
		$('[name=daterec]').datetimepicker('setEndDate', getLastDateOfMonth(<?=date("Y")?>,month_no-1));
		    
		$("#template_id").val(template_id);
		
		if (is_edit==1)
		{
			query_EXPENSES_modal(rec_id);
			
//			$('#lblTitle_EXPENSES').html("Edit Expense");
//			$("[name=cost]").val(price);
//			$("#expensesFORM_updateID").val(rec_id);
		}
		else
		{
			$('#lblTitle_EXPENSES').html("Add Expense");
			$("[name=cost]").val(suggested);
			$("[name=daterec]").val('01-'+twoDigits(month_no)+'-<?=date("Y")?>');//date_now4mysql());
			
			$("#bntDelete_EXPENSES").hide(); //hide delete
			$('#modalEXPENSES').modal('toggle');
		}


		
	}
	
	//edit button - read record
	function query_EXPENSES_modal(rec_id){
		loading.appendTo(document.body);
		
	    $.ajax(
	    {
	        url : "tab_dashboard_admin_big_brother_fetch.php",
	        type: "POST",
	        data : { expense_id : rec_id },
	        success:function(data, textStatus, jqXHR)
	        {
				loading.remove();
				
	        	if (data!='null')
				{
				 	$("[name=expensesFORM_updateID]").val(data.expense_id);
					$('[name=template_id]').val(data.expense_template_id);
					$('[name=cost]').val(data.cost);
					$('[name=daterec]').val(data.daterec);
					$('[name=comments]').val(data.comments);
					$('[name=pay_method]').val(data.pay_method);

				 	$("#bntDelete_EXPENSES").show(); //show delete
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
	
	function twoDigits(d) {
	    if(0 <= d && d < 10) return "0" + d.toString();
	    if(-10 < d && d < 0) return "-0" + (-1*d).toString();
	    return d.toString();
	}

	function date_now4mysql()
	{
		var d = new Date();
		var str_date = twoDigits(d.getDate()) + "-" + twoDigits(d.getMonth() + 1) + "-" + d.getFullYear();
		return str_date;
	}
	
	function getLastDateOfMonth(Year,Month){
 		var d = new Date((new Date(Year, Month+1,1))-1);
 		
		var str_date = twoDigits(d.getDate()) + "-" + twoDigits(d.getMonth() + 1) + "-" + d.getFullYear();
		return str_date; 		
	}
	
	/////////////////////////////////////////////////////////////////////////////////////////////////////
	/////////////////////////////// MISC MODAL
	/////////////////////////////////////////////////////////////////////////////////////////////////////
	

			
				
	function misc_add_new(month_no){
		$("#lblTitle_EXPENSES_two").val("New Miscellaneous");
		$("#bntDelete_EXPENSES_two").hide(); //hide delete
		
		$('[name=misc_daterec_two]').datetimepicker('setStartDate', '<?=date("Y")?>-'+month_no+'-01');
		$('[name=misc_daterec_two]').datetimepicker('setEndDate', getLastDateOfMonth(<?=date("Y")?>,month_no-1));
		$('[name=misc_daterec_two]').val('01-'+twoDigits(month_no)+'-<?=date("Y")?>');
		
		$("#modalEXPENSES_two").modal('toggle');
	}
	
	function misc_edit(rec_id,month_no){
	 	//restrict datepicker
		$('[name=misc_daterec_two]').datetimepicker('setStartDate', '<?=date("Y")?>-'+month_no+'-01');
		$('[name=misc_daterec_two]').datetimepicker('setEndDate', getLastDateOfMonth(<?=date("Y")?>,month_no-1));

		query_EXPENSES_modal_two(rec_id);
	}
	
				function query_EXPENSES_modal_two(rec_id){
					loading.appendTo(document.body);
					
				    $.ajax(
				    {
				        url : "tab_dashboard_admin_big_brother_fetch.php",
				        type: "POST",
				        data : { expense_id : rec_id },
				        success:function(data, textStatus, jqXHR)
				        {
							loading.remove();
							
				        	if (data!='null')
							{
							 	$("[name=expensesFORM_updateID_two]").val(data.expense_id);
								$('[name=cost_two]').val(data.cost);
								$('[name=pay_method_two]').val(data.pay_method);
								$('[name=misc_title_two]').val(data.misc_title);
								$('[name=misc_daterec_two]').val(data.misc_daterec);
								$('[name=misc_is_paid_two]').bootstrapSwitch('state',parseInt(data.misc_is_paid));
								$('[name=comments_two]').val(data.comments);
							 	
		
							 	$('#lblTitle_EXPENSES_two').html("Edit Miscellaneous");
							 	$("#bntDelete_EXPENSES_two").show(); //show delete
								$('#modalEXPENSES_two').modal('toggle');
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
				
</script>

<div id="columnchart_material" style="height: 300px;"></div>

<br>	<a href="tab_dashboard_admin_big_brother_settings.php" style = "float:right" type="button" class="btn btn-primary btn-lg">settings</a>
<br><br>
	<table id="balance_tbl"
	           data-striped=true>

		<thead>
			<tr>
				<th data-width=180 data-field="category" data-sortable="true">
					
				</th>
				
				<th data-width=130 data-field="jan" data-sortable="false">
					January
				</th>

				<th data-width=130 data-field="feb" data-sortable="false">
					February
				</th>
				
				<th data-width=130 data-field="march" data-sortable="false">
					March
				</th>
				
				<th data-width=130 data-field="april" data-sortable="false">
					April
				</th>
				
				<th data-width=130 data-field="may" data-sortable="false">
					May
				</th>
				
				<th data-width=130 data-field="june" data-sortable="false">
					June
				</th>
				
				<th data-width=130 data-field="july" data-sortable="false">
					July
				</th>
				
				<th data-width=130 data-field="aug" data-sortable="false">
					August
				</th>
				
				<th data-width=130 data-field="sept" data-sortable="false">
					September
				</th>
				
				<th data-width=130 data-field="oct" data-sortable="false">
					October
				</th>

				<th data-width=130 data-field="nov" data-sortable="false">
					November
				</th>
				
				<th data-width=130 data-field="dec" data-sortable="false">
					December
				</th>
				
			</tr>
		</thead>
		
		<tbody id="balance_rows"></tbody>
	</table	>
	
	<br><br>
	<table id="expenses_tbl"
	           data-striped=true>

		<thead>
			<tr>
				<th data-field="expense_template_id" data-visible="false">
					template_id
				</th>

				<th data-width=180 data-field="category" data-sortable="false">
					Category
				</th>
				
				<th data-width=130 data-field="jan" data-sortable="false">
					January
				</th>

				<th data-width=130 data-field="feb" data-sortable="false">
					February
				</th>
				
				<th data-width=130 data-field="march" data-sortable="false">
					March
				</th>
				
				<th data-width=130 data-field="april" data-sortable="false">
					April
				</th>
				
				<th data-width=130 data-field="may" data-sortable="false">
					May
				</th>
				
				<th data-width=130 data-field="june" data-sortable="false">
					June
				</th>
				
				<th data-width=130 data-field="july" data-sortable="false">
					July
				</th>
				
				<th data-width=130 data-field="aug" data-sortable="false">
					August
				</th>
				
				<th data-width=130 data-field="sept" data-sortable="false">
					September
				</th>
				
				<th data-width=130 data-field="oct" data-sortable="false">
					October
				</th>

				<th data-width=130 data-field="nov" data-sortable="false">
					November
				</th>
				
				<th data-width=130 data-field="dec" data-sortable="false">
					December
				</th>
				
			</tr>
		</thead>
		
		<tbody id="exp_rows"></tbody>
	</table	>
	

<!-- NEW EXPENSES MODAL [START] -->
<div class="modal fade" id="modalEXPENSES" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
					&times;
				</button>
				<h4 class="modal-title" id='lblTitle_EXPENSES'>New</h4>
			</div>
			<div class="modal-body">
				<form id="formEXPENSES" role="form" method="post" action="tab_dashboard_admin_big_brother_save.php">

				<div class='form-group'>
					<label>Cost :</label>
					<input name='cost' class='form-control' placeholder='cost'>
				</div>

				<div class='form-group'>
					<label>Method :</label>
					<select name='pay_method' class='form-control'>
					</select>
				</div>
				

			
				<div class='form-group'>
					<label>Date :</label><br>
					<input type="text" name="daterec" class="form-control" data-date-format="dd-mm-yyyy" readonly class="form_datetime">
				</div>


			
				<div class='form-group'>
					<label>Comments :</label>
					<input name='comments' class='form-control' maxlength="150" placeholder='comments'>
				</div>



				<input name="template_id" id="template_id" class="form-control" style="display: none;">
				<input name="expensesFORM_updateID" id="expensesFORM_updateID" class="form-control" style="display: none;" >

				<div class="modal-footer">
					<button id="bntDelete_EXPENSES" type="button" class="btn btn-danger" style="float: left;">
						unpaid
					</button>
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

<!-- NEW MISC EXPENSES MODAL [START] -->
<div class="modal fade" id="modalEXPENSES_two" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
					&times;
				</button>
				<h4 class="modal-title" id='lblTitle_EXPENSES_two'>New Miscellaneous</h4>
			</div>
			<div class="modal-body">
				<form id="formEXPENSES_two" role="form" method="post" action="tab_dashboard_admin_big_brother_save_misc.php">

				<div class='form-group'>
					<label>Title :</label>
					<input name='misc_title_two' maxlength="30" class='form-control' placeholder='title'>
				</div>

		
				<div class='form-group'>
					<label>Cost :</label>
					<input name='cost_two' class='form-control' placeholder='cost'>
				</div>


			
				<div class='form-group'>
					<label>Method :</label>
					<select name='pay_method_two' class='form-control'>
					</select>
				</div>

			
				<div class='form-group'>
					<label>Date :</label><br>
					<input type="text" name="misc_daterec_two" class="form-control" data-date-format="dd-mm-yyyy" readonly class="form_datetime">
				</div>


			
				<div class='form-group'>
					<label>Paid :</label><br>
					<input type="checkbox" name='misc_is_paid_two'>
				</div>


			
				<div class='form-group'>
					<label>Comments :</label>
					<input name='comments_two' class='form-control' placeholder='comments'>
				</div>


					<input name="expensesFORM_updateID_two" id="expensesFORM_updateID_two" class="form-control" style="display:none;">

					<div class="modal-footer">
						<button id="bntDelete_EXPENSES_two" type="button" class="btn btn-danger" style="float: left;">
							delete
						</button>
						<button id="bntCancel_EXPENSES_two" type="button" class="btn btn-default" data-dismiss="modal">
							cancel
						</button>
						<button id="bntSave_EXPENSES_two" class="btn btn-primary" type="submit" name="submit">
							save
						</button>
					</div>
                </form>
            </div><!-- End of Modal body -->
        </div><!-- End of Modal content -->
    </div><!-- End of Modal dialog -->
</div><!-- End of Modal -->
<!-- NEW MISC EXPENSES MODAL [END] -->



<?php
include ('template_bottom.php');
?>