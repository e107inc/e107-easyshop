<?php
/*
+------------------------------------------------------------------------------+
| EasyShop - an easy e107 web shop  | adapted by nlstart
| formerly known as
|	jbShop - by Jesse Burns aka jburns131 aka Jakle
|	Plugin Support Site: e107.webstartinternet.com
|
|	For the e107 website system visit http://e107.org
|
|	Released under the terms and conditions of the
|	GNU General Public License (http://gnu.org).
+------------------------------------------------------------------------------+
*/
// Ensure this program is loaded in admin theme before calling class2
$eplug_admin = true;

// class2.php is the heart of e107, always include it first to give access to e107 constants and variables
require_once("../../class2.php");

// Include auth.php rather than header.php ensures an admin user is logged in
require_once(e_ADMIN."auth.php");

// Check to see if the current user has admin permissions for this plugin
if (!getperms("P")) {
	// No permissions set, redirect to site front page
	header("location:".e_BASE."index.php");
	exit;
}

// Get language file (assume that the English language file is always present)
$lan_file = e_PLUGIN."easyshop/languages/".e_LANGUAGE.".php";
include_lan($lan_file);

require_once("includes/config.php");
// IPN addition
include_once("includes/ipn_functions.php");

// Set the active menu option for admin_menu.php
$pageid = 'admin_menu_06';

$sql = new db;
// Wrap the shop monitor page in a table
$text ="<table border='0' width='95%' cellpadding='3'>";
$text .= "<tr>
				<td valign='top' align='left' width='45%'>
					<center>
					<table class='fborder' width='90%'>
						<tr>
							<td class='fcaption' colspan='2' width='100%'>
								".EASYSHOP_MONITOR_01."
							</td>
						</tr>";

// Display active Product Main Categories header
$text .= "<tr>
				<td class='forumheader'>
					".EASYSHOP_MONITOR_13."
				</td>
				<td class='forumheader2'>";
// Count active Product Main Categories
$text .= $sql->db_Count(DB_TABLE_SHOP_MAIN_CATEGORIES, "(*)", "WHERE main_category_active_status = '2'");
$text .="</td>
			</tr>";

// Display inactive Product Main Categories header
$text .= "<tr>
				<td class='forumheader'>
					".EASYSHOP_MONITOR_14."
				</td>
				<td class='forumheader2'>";
// Count inactive Product Categories
$text .= $sql->db_Count(DB_TABLE_SHOP_MAIN_CATEGORIES, "(*)", "WHERE main_category_active_status = '1'");
$text .="</td>
			</tr>";

// Display active Product Categories header
$text .= "<tr>
				<td class='forumheader'>
					".EASYSHOP_MONITOR_05."
				</td>
				<td class='forumheader2'>";
// Count active Product Categories
$text .= $sql->db_Count(DB_TABLE_SHOP_ITEM_CATEGORIES, "(*)", "WHERE category_active_status = '2'");
$text .="</td>
			</tr>";

// Display active Product Categories without Main Category header
$text .= "<tr>
				<td class='forumheader'>
					".EASYSHOP_MONITOR_16."
				</td>
				<td class='forumheader2'>";
// Count active Product Categories without Main Category
$text .= $sql -> db_Count(DB_TABLE_SHOP_ITEM_CATEGORIES, "(*)", "WHERE category_active_status = 2 AND category_main_id= ''");
$text .="</td>
			</tr>";

// Display inactive Product Categories header
$text .= "<tr>
				<td class='forumheader'>
					".EASYSHOP_MONITOR_06."
				</td>
				<td class='forumheader2'>";
// Count inactive Product Categories
$text .= $sql->db_Count(DB_TABLE_SHOP_ITEM_CATEGORIES, "(*)", "WHERE category_active_status = '1'");
$text .="</td>
			</tr>";

// Display active products header
$text .= "<tr>
				<td class='forumheader'>
					".EASYSHOP_MONITOR_02."
				</td>
				<td class='forumheader2'>";
// Count active products
$prod_count = $sql->db_Count(DB_TABLE_SHOP_ITEMS, "(*)", "WHERE item_active_status = '2'");
// If active product count is zero than display error message
if(!$prod_count){$text .= "<a href='admin_config.php'>".EASYSHOP_MONITOR_03."</a>";}
else { $text .= $prod_count;}
$text .="</td>
			</tr>";
			
// Display active products with discount header
$text .= "<tr>
				<td class='forumheader'>
          - ".EASYSHOP_MONITOR_17."
				</td>
				<td class='forumheader2'>";
// Count active products with discounts
$prod_discount_count = $sql->db_Count(DB_TABLE_SHOP_ITEMS, "(*)", "WHERE item_active_status = '2' AND prod_discount_id > '0'");
// If active product count with discounts is zero than display NONE
if(!$prod_discount_count){$text .= EASYSHOP_MONITOR_18;}
else { $text .= $prod_discount_count;}
$text .="</td>
			</tr>";

// Display active products with propertie header
$text .= "<tr>
				<td class='forumheader'>
          - ".EASYSHOP_MONITOR_19."
				</td>
				<td class='forumheader2'>";
// Count active products with properties
$prod_property_count = $sql->db_Count(DB_TABLE_SHOP_ITEMS, "(*)", "WHERE item_active_status = '2' AND (prod_prop_1_id > '0' OR prod_prop_2_id > '0' OR prod_prop_3_id > '0' OR prod_prop_4_id > '0' OR prod_prop_5_id > '0')");
// If active product count with property is zero than display NONE
if(!$prod_property_count){$text .= EASYSHOP_MONITOR_18;}
else { $text .= $prod_property_count;}
$text .="</td>
			</tr>";

// Display inactive products header
$text .= "<tr>
				<td class='forumheader'>
					".EASYSHOP_MONITOR_04."
				</td>
				<td class='forumheader2'>";
// Count inactive products
$text .= $sql->db_Count(DB_TABLE_SHOP_ITEMS, "(*)", "WHERE item_active_status = '1'");
$text .="</td>
			</tr>";

// Display out-of-stock products header
$text .= "<tr>
				<td class='forumheader'>
        ".EASYSHOP_MONITOR_15."
				</td>
				<td class='forumheader2'>";
// Count out-of-stock products
$text .= $sql->db_Count(DB_TABLE_SHOP_ITEMS, "(*)", "WHERE item_out_of_stock = '2'");
$text .="</td>
			</tr>";

// Display number of images header
// Build array with all images to choose from
$sql->db_Select(DB_TABLE_SHOP_PREFERENCES);
while($row = $sql-> db_Fetch()){
    $store_image_path = $row['store_image_path'];
}
require_once(e_HANDLER."file_class.php");
$fl = new e_file;
if($image_array = $fl->get_files(e_PLUGIN."easyshop/".$store_image_path, ".gif|.jpg|.png|.GIF|.JPG|.PNG","standard",2)){
	sort($image_array);
}
$image_count = count($image_array);

$text .= "<tr>
				<td class='forumheader'>
				".EASYSHOP_MONITOR_20."	".$store_image_path."
				</td>
				<td class='forumheader2'>";
// Display count number of images
$text .= $image_count;
$text .="</td>
			</tr>";

$text .="</td></tr></table>";

/*
// Another table for future references e.g. order information
$text .= "<td valign='top' align='left' width='10%'>&nbsp;</td>
				<td valign='top' align='left' width='45%'>
					<!-- Table within the cell for orders display -->
					<center>
					<table class='fborder' width='90%'>
						<tr>
							<td class='fcaption' colspan='2' width='100%'>
								".EASYSHOP_MONITOR_07."
							</td>
						</tr>";

// Order info, Show orders waiting for approval
$text .= "<tr>
				<td class='forumheader'>
					<a href='admin_orders.php'?view=1>".EASYSHOP_MONITOR_08."</a>
				</td>
				<td class='forumheader2'>";
				$sql2 = new db;
				$i = 0;
				$sql2->db_Select(DB_TABLE_SHOP_ORDERS, "*", "order_status<>'C' GROUP BY order_number");
				while($row2=$sql2->db_Fetch())
				{
					$i++;
				}
$text .= $i;
$text .="</td>
			</tr>";

// Order info, show Total completed orders
$text .= "<tr>
				<td class='forumheader'>
					<a href='admin_orders.php'?view=2>".EASYSHOP_MONITOR_09."</a>
				</td>
				<td class='forumheader2'>";
				$sql2 = new db;
				$i = 0;
				$sql2->db_Select(DB_TABLE_SHOP_ORDERS, "*", "order_status='C' GROUP BY order_number");
				while($row2=$sql2->db_Fetch())
				{
					$i++;
				}
$text .=$i."</td>
			</tr>";

// Order info, show Total offline orders
$text .= "<tr>
				<td class='forumheader'>
					<a href='admin_manage_orders.php?view=3'>".EASYSHOP_MONITOR_10."</a>
				</td>
				<td class='forumheader2'>";
				$sql2 = new db;
				$i = 0;
				$sql2->db_Select(DB_TABLE_SHOP_ORDERS, "*", "channel='offline' GROUP BY order_number");
				while($row2=$sql2->db_Fetch())
				{
					$i++;
				}
$text .=$i."</td>
			</tr>";

// Order info, show Total orders
$text .= "<tr>
				<td class='forumheader'>
					".EASYSHOP_MONITOR_11."
				</td>
				<td class='forumheader2'>";
				$sql2 = new db;
				$i = 0;
				$sql2->db_Select(DB_TABLE_SHOP_ORDERS, "*", "order_id > 0 GROUP BY order_number");
				while($row2=$sql2->db_Fetch())
				{
					$i++;
				}

$text .=$i."</td>
			</tr>";
$text .="</table>";
*/

// Close the HTML wrap table
$text .="</td></tr></table>";

// IPN addition - introduce basic reporting
$result_text ="";
if(isset($_GET['report'])){

$one_day = 24 * 60 * 60; // hrs* mins * secs    
    // should we clean EScheck entries? i.e. they have been checked by admin and any fraudulent activities sorted
    if( $_GET['report'] == "clean_check"){ 
        $current_time = time();
        $cutoff_time = $current_time - ($one_day * $_GET['days']);
        if($_GET['check']<>0){
            $check_del = transaction("delete", NULL, NULL, "EScheck_", $cutoff_time, $current_time);
            $check_del ? $result_text .= " EScheck entries deleted successfully <br />" :
                         $result_text .= " There was a problem deleting EScheck entries <br />" ;
        }else{
          $result_text .= " There are no EScheck entries to delete <br />" ;
        }  
            
    }
// should we clean ES_shopping/processing entries? -is older than 3 days too little ?!??!?!?
    if($_GET['report'] == "clean_shop"){
        $current_time = time();
        $cutoff_time = $current_time - ($one_day * $_GET['days']);
        
        
        if($_GET['shop']<>0){
            $check_del = transaction("delete", NULL, NULL, "ES_shopping", $cutoff_time, $current_time);
            
            $check_del ? $result_text .= " ES_shopping entries deleted successfully <br />" :
                         $result_text .= " No ES_shopping entries older than ".$_GET['days']." days to delete <br />" ;
        }else{
            $result_text .= " There are no ES_shopping entries to delete <br />" ;
        }

        if($_GET['proc']<>0){
            $check_del = transaction("delete", NULL, NULL, "ES_processing", $cutoff_time, $current_time);
            
            $check_del ? $result_text .= " ES_processing entries deleted successfully <br />" :
                         $result_text .= " No ES_processing entries older than ".$_GET['days']." days to delete <br />" ;
        }else{
            $result_text .= " There are no ES_processing entries to delete <br />" ;   
        }
    }
}

$report = report();
//$reporttext ="<table border='0' width='95%' cellpadding='3'><tr><td>";
$reporttext ="<table class='fborder' width='90%'><tr><td>";
if (isset($report['Completed']['report_count'])){
        $Completed = "<br /><div onclick='expandit(\"Completed\");'><span class='button'> 'Completed Transactions' Report </span></div><br /><span id='Completed' style='display:none;'>";
        for($i=1;$i<=$report['Completed']['report_count'];$i++){
            $Completed .= $report['Completed'][$i]['report_table'];
        }
        $Completed .="</span>";
} else {  $Completed=""; }

if (isset($report['ES_processing']['report_count'])){
        $ES_processing = "<br /><div onclick='expandit(\"ES_processing\");'><span class='button'> 'Transactions being processed' Report </span></div><br /><span id='ES_processing' style='display:none;'>";
        for($i=1;$i<=$report['ES_processing']['report_count'];$i++){
            $ES_processing .= $report['ES_processing'][$i]['report_table'];
        }
        $ES_processing .="</span>";
} else {  $ES_processing=""; }

if (isset($report['ES_shopping']['report_count'])){
        $ES_shopping = "<br /><div onclick='expandit(\"ES_shopping\");'><span class='button'> 'Current Shoppers' Report </span></div><br /><span id='ES_shopping' style='display:none;'>";
        for($i=1;$i<=$report['ES_shopping']['report_count'];$i++){
            $ES_shopping .= $report['ES_shopping'][$i]['report_table'];
        }
        $ES_shopping .="</span>";
} else {  $ES_shopping=""; }

if (isset($report['EScheck']['report_count'])){
        $EScheck = "<br /><div onclick='expandit(\"EScheck\");'><span class='button'> 'Accounts requiring attention' </span></div><br /><span id='EScheck' style='display:none;'>";
        for($i=1;$i<=$report['EScheck']['report_count'];$i++){
            $EScheck .= $report['EScheck'][$i]['report_table'];
        }
        $EScheck .="</span>";
} else {  $EScheck=""; }

if (isset($report['totals']['report_count'])){
        $totals = "<br /><div onclick='expandit(\"totals\");'> <span class='button'> 'Transactions failing the Totals check' - probably Fraudulent </span></div><br /><span id='totals' style='display:none;'>";
        for($i=1;$i<=$report['totals']['report_count'];$i++){
            $totals .= $report['totals'][$i]['report_table'];
        }
        $totals .="</span>";
} else {  $totals=""; }

if (isset($report['rxemail']['report_count'])){
        $rxemail = "<br /><div onclick='expandit(\"rxemail\");'> <span class='button'> 'Transactions failing the Easyshop Email check' - could be a double entry error or possibly Fraudulent </span></div><br /><span id='rxemail' style='display:none;'>";
        for($i=1;$i<=$report['rxemail']['report_count'];$i++){
            $rxemail .= $report['rxemail'][$i]['report_table'];
        }
        $rxemail .="</span>";
} else {  $rxemail=""; }

if (isset($report['dupltxn']['report_count'])){
        $dupltxn = "<br /><div onclick='expandit(\"dupltxn\");'><span class='button'> 'Transactions failing the Paypal check' - probably Fraudulent </span></div><br /><span id='dupltxn' style='display:none;'>";
        for($i=1;$i<=$report['dupltxn']['report_count'];$i++){
            $dupltxn .= $report['dupltxn'][$i]['report_table'];
            
        }
        $dupltxn .="</span>";
} else {  $dupltxn=""; }
        
$reporttext .= $Completed . $ES_processing . $ES_shopping . $EScheck . $totals . $rxemail . $dupltxn;

$clean_shop_days = 3;
$clean_check_days = 7;

$reporttext .= "
<div style='text-align:center;'>
<br/><span class='button'><b>
<a href='admin_monitor.php?report=clean_shop&days=".$clean_shop_days."&shop=".$report['ES_shopping']['report_count']."&proc=".$report['ES_processing']['report_count']."'>&nbsp;&nbsp; Delete Shopping/Processing transactions > 3 days old&nbsp;&nbsp;</a>
</b></span>&nbsp;&nbsp;&nbsp;&nbsp;
<span class='button'><b>
<a href='admin_monitor.php?report=clean_check&days=".$clean_check_days."&check=".$report['EScheck']['report_count']."'>&nbsp;&nbsp; Delete all ES check transactions greater than 7 days &nbsp;&nbsp;</a>
</b></span>
</div>
</td></tr></table>
<div style='text-align:center;'>".$result_text."</div>";

$text .= $reporttext;

// Render the value of $text in a table.
$ns->tablerender(EASYSHOP_MONITOR_00, $text);
require_once(e_ADMIN."footer.php");
?>