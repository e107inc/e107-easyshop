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

// class2.php is the heart of e107, always include it first to give access to e107 constants and variables
require_once("../../class2.php");

// Get language file (assume that the English language file is always present)
$lan_file = e_PLUGIN."easyshop/languages/".e_LANGUAGE.".php";
include_lan($lan_file);

// use HEADERF for USER PAGES and e_ADMIN."auth.php" for admin pages
require_once(HEADERF);

require_once("includes/config.php");

require_once(e_HANDLER . "comment_class.php"); // Necessary for comments
$cobj = new comment;

// Check query
if(e_QUERY){
	$tmp = explode(".", e_QUERY);
	$action = $tmp[0];
	$action_id = intval($tmp[1]); // Intval to protect from SQL Injection
  $page_id = intval($tmp[2]); // Used for page id of prod
	unset($tmp);
}
// Extra check
if (strlen($action) > 0 and !in_array($action, array("edit", "cat", "prodpage", "mcat", "prod", "allcat", "catpage", "blanks", "mcatpage")) and $action != "") {
  // Get out of here: incoming action is not an expected one
  header("Location: ".e_BASE); // Redirect to the home page; in next version a specific error message
  //$ns -> tablerender ('Error encountered', 'Sorry, unexpected action '.$action.' specified.'); // require('FOOTERF');
  exit;
}
// Another extra check on action id
if (strlen($action_id) > 0 and $action_id < 1 and $action_id != "") {
  header("Location: ".e_BASE); // Redirect to the home page; in next version a specific error message
  //$ns -> tablerender ('Error encountered', 'Sorry, unexpected action id '.$action_id.' specified.'); // require('FOOTERF');
  exit;
}
// Another extra check on page id
if (strlen($page_id) > 0 and $page_id < 1 and $page_id != "") {
  header("Location: ".e_BASE); // Redirect to the home page; in next version a specific error message
  //$ns -> tablerender ('Error encountered', 'Sorry, unexpected page id '.$page_id.' specified.'); // require('FOOTERF');
  exit;
}

//-----------------------------------------------------------------------------+
//---------------------- Get and Set Defaults ---------------------------------+
//-----------------------------------------------------------------------------+

// Keep sessions alive when user uses back button of browser
// session_cache_limiter('public');
// Stop caching for all browsers
session_cache_limiter('nocache');
// Start a session to catch the basket
session_start();

// global $session_id;
// $session_id = session_id();
require_once("easyshop_class.php");
$session_id = Security::get_session_id();

// Debug info
//print_r ($_SESSION['shopping_cart']);
//print_r ($_SESSION['sc_total']);

// Set the totals to zero if there is no session variable
if(!isset($_SESSION['sc_total'])) {
  $_SESSION['sc_total']['items'] = 0;
  $_SESSION['sc_total']['sum']   = 0;
}

// Retrieve shop preferences just once
$sql = new db;
$sql -> db_Select(DB_TABLE_SHOP_PREFERENCES, "*", "store_id=1");
if ($row = $sql-> db_Fetch()){
  $store_name = $row['store_name'];
  $store_address_1 = $row['store_address_1'];
  $store_address_2 = $row['store_address_2'];
  $store_city = $row['store_city'];
  $store_state = $row['store_state'];
  $store_zip = $row['store_zip'];
  $store_country = $row['store_country'];
  $paypal_email = $row['paypal_email'];
  $paypal_currency_code = $row['paypal_currency_code'];
  $support_email = $row['support_email'];
  $store_image_path = $row['store_image_path'];
  $store_welcome_message = $row['store_welcome_message'];
  $store_info = $row['store_info'];
  $payment_page_style = $row['payment_page_style'];
  $payment_page_image = $row['payment_page_image'];
  $add_to_cart_button = $row['add_to_cart_button'];
  $view_cart_button = $row['view_cart_button'];
  $popup_window_height = $row['popup_window_height'];
  $popup_window_width = $row['popup_window_width'];
  $cart_background_color = $row['cart_background_color'];
  $thank_you_page_title = $row['thank_you_page_title'];
  $thank_you_page_text = $row['thank_you_page_text'];
  $num_category_columns = $row['num_category_columns'];
  $categories_per_page = $row['categories_per_page'];
  $num_item_columns = $row['num_item_columns'];
  $items_per_page = $row['items_per_page'];
  $sandbox = $row['sandbox'];
  $set_currency_behind = $row['set_currency_behind'];
  $minimum_amount = number_format($row['minimum_amount'], 2, '.', '');
  $always_show_checkout = $row['always_show_checkout'];
  $email_order = $row['email_order'];
  $product_sorting = $row['product_sorting'];
  $page_devide_char = $row['page_devide_char'];
  $enable_comments = $row['enable_comments'];
  $show_shopping_bag = $row['show_shopping_bag'];
  $print_shop_address = $row['print_shop_address'];
  $print_shop_top_bottom = $row['print_shop_top_bottom'];
  $print_discount_icons = $row['print_discount_icons'];
  $enable_ipn = $row['enable_ipn']; // IPN addition 
}

// Check admin setting to set currency behind amount
// 0 = currency before amount (default), 1 = currency behind amount
if ($set_currency_behind == '') {($set_currency_behind = 0);}

// Check admin setting to set minimum amount
// Checkout button is only shown if total amount is above this minimum
if ($minimum_amount == '') {($minimum_amount = 0);}

// Check admin setting to display checkout button always
// 0 = no, only show when at least 1 product is ordered, 1 = yes, always show checkout button
if ($always_show_checkout == '') {($always_show_checkout = 0);}

// Check admin setting to display page devide character
if ($page_devide_char == '') {($page_devide_char = "&raquo;");}

// Check admin setting to e-mail order to admin
// E-mail to admin overrules the checkout to PayPal!
// 0 = no e-mail to admin, 1 = e-mail order to admin
if ($email_order == '') {($email_order = 0);} // Introduced in 1.2 RC6, functioning since 1.3!

// Format the shop welcome message once
$store_welcome_message = $tp->toHTML($store_welcome_message, true);

// Define actual currency and position of currency character once
$sql -> db_Select(DB_TABLE_SHOP_CURRENCY, "*", "currency_active=2");
if ($row = $sql-> db_Fetch()){
	$unicode_character = $row['unicode_character'];
	$paypal_currency_code = $row['paypal_currency_code'];
}

// Determine currency before or after amount
if ($set_currency_behind == 1) {
  // Print currency after amount
  $unicode_character_before = "";
  $unicode_character_after = "&nbsp;".$unicode_character;
}
else {
  $unicode_character_before = "&nbsp;".$unicode_character."&nbsp;";
  $unicode_character_after = "";
	// Print currency before amount in all other cases
}

// Set values for variables $existing_tems and active_items
if ($sql -> db_Count(DB_TABLE_SHOP_ITEMS, "(*)", "WHERE category_id=".$action_id) > 0) {
	$existing_items = 1;
}
if ($sql -> db_Count(DB_TABLE_SHOP_ITEMS, "(*)", "WHERE category_id=".$action_id." AND item_active_status=2") > 0) {
	$active_items = 1;
}

// Determine the variable $column_width
$column_width = Shop::switch_columns($num_item_columns);

//-----------------------------------------------------------------------------+
//--------------- Get visitors name and e-mail address ------------------------+
//-----------------------------------------------------------------------------+
// Check incoming e-mail address
if ($_POST['email_order'] == 1 AND isset($_POST['to_email'])) {
  // Check the provided e-mail address
  if(check_email($_POST['to_email'])){
    // E-mail is valid
    $_SESSION['sc_total']['to_email'] = $_POST['to_email'];
  } else {
    // Not a valid e-mail address
    unset($_SESSION['sc_total']['to_email']);
  }
}
// Check incoming name (must be larger than 3 characters)
if ($_POST['email_order'] == 1 AND isset($_POST['to_name'])) {
  // Check the provided name
  if(strlen($_POST['to_name']) > 3){
    // Name is valid
    $_SESSION['sc_total']['to_name'] = $_POST['to_name'];
  } else {
    // Not a valid name
    unset($_SESSION['sc_total']['to_name']);
  }
}
// Determine if form to get visitors name and e-mail must be shown
if ($_POST['email_order'] == 1 AND !USER AND (!isset($_SESSION['sc_total']['to_email']) OR !isset($_SESSION['sc_total']['to_name']))) {
  // Perform an extra security check
  if ($session_id != session_id()) { // Get out of here: incoming session id is not equal than current session id
    header("Location: ".e_BASE); // Redirect to the home page
    exit;
  }
  // User has clicked on checkout but is not logged in and has not provided a name and e-mail yet
  $get_address_text .= "
 	<div style='text-align:center;'>
		<div style='width:100%'>
				<center>
					<table border='0' cellspacing='15' width='100%'>
						<tr>
							<td>
								<div style='text-align:center;'>".EASYSHOP_SHOP_65."</div>
								<br/>
								".EASYSHOP_SHOP_66."<br />
                <br />
                <br />
                ".EASYSHOP_SHOP_67."<br />
                <br />
                ".EASYSHOP_SHOP_68."<br />
                <br />
                <ul>
                  <li>".EASYSHOP_SHOP_69." <a href='".e_BASE."login.php'>".EASYSHOP_SHOP_70."</a></li><br />
  								<li>".EASYSHOP_SHOP_71." <a href='".e_BASE."signup.php'>".EASYSHOP_SHOP_72."</a></li><br />
								</ul>
								<br />
                ".EASYSHOP_SHOP_73."<br />
                <div>
  								<form method='post' action='".e_SELF."'>
  								<fieldset>
                    ".EASYSHOP_SHOP_74.":
                    <input class='tbox' size='25' type='text' name='to_name' value='".$_SESSION['sc_total']['to_name']."' /> ".EASYSHOP_SHOP_75."<br />
                    <br />
                    ".EASYSHOP_SHOP_76.":
                    <input class='tbox' size='25' type='text' name='to_email' value='".$_SESSION['sc_total']['to_email']."' /><br />
    								<input type='hidden' name='email_order' value='1'/>
                    <div style='text-align:center;'><input class='button' name='submit' type='submit' value='".EASYSHOP_SHOP_77."'/></div>
                  </fieldset>
                  </form>
                </div>
                <br />
              </td>
            </tr>
          </table>
        </center>
    </div>
  </div>";
	// Render the value of $get_address_text in a table.
	$title = EASYSHOP_SHOP_78;
	$ns -> tablerender($title, $get_address_text);
}

//-----------------------------------------------------------------------------+
//----------------------- E-mail the order  -----------------------------------+
//-----------------------------------------------------------------------------+
if ($_POST['email_order'] == 1 AND (USER OR (isset($_SESSION['sc_total']['to_name']) AND isset($_SESSION['sc_total']['to_email']) ))) {
  // Perform an extra security check
  if ($session_id != session_id()) { // Get out of here: incoming session id is not equal than current session id
    header("Location: ".e_BASE); // Redirect to the home page
    exit;
  }
  // Receive the setting email_order=1 from the checkout form (or the get visitors name form)
  // User has clicked on checkout and is logged in or has provided a name and e-mail
  $sender_name  = ((isset($pref['replyto_name']))?$pref['replyto_name']:$pref['siteadmin']);        // Keep 0.7.8 compatible
  $sender_email = ((isset($pref['replyto_email']))?$pref['replyto_email']:$pref['siteadminemail']); // Keep 0.7.8 compatible
  if (USER) {
    $sql = new db;
    $arg="SELECT *
         FROM #user
         WHERE user_id = '".intval(USERID)."'"; // Security fix
    $sql->db_Select_gen($arg,false);
    if($row = $sql-> db_Fetch()){
     $to_name   = $row['user_name'];
     $to_email  = $row['user_email'];
    }
  } else {
     $to_name   = $_SESSION['sc_total']['to_name'];  // This value is checked
     $to_email  = $_SESSION['sc_total']['to_email']; // This value is checked
  }
  $pref_sitename = $pref['sitename'];
  $temp_message = MailOrder($unicode_character_before, $unicode_character_after, $pref_sitename, $sender_name, $sender_email, $to_name, $to_email);
  // function returns an array; [0] is the message and [1] is $mail_result at success set to 1
  $mail_message = $temp_message[0];
  $mail_result  = $temp_message[1];
  unset($temp_message);
  if ($mail_result == 1) { // Succesfull e-mail has been send
    // Manipulate location to thank you page (where shop basket will be emptied)
    $target=('thank_you.php');
    header("Location: ".$target);
    exit;
  }
  $mail_text .= "
 	<div style='text-align:center;'>
		<div style='width:100%'>
				<center>
					<table border='0' cellspacing='15' width='100%'>
						<tr>
							<td>
								<center>".$mail_message."</center>
								<br/>".$mail_header."
              </td>
            </tr>
          </table>
        </center>
    </div>
  </div>";

	// Render the value of $mail_text in a table.
	$title = EASYSHOP_SHOP_61;
	$ns -> tablerender($title, $mail_text);
}

//-----------------------------------------------------------------------------+
//---------------------- Edit Shopping Basket ---------------------------------+
//-----------------------------------------------------------------------------+
// Show Shopping Cart if easyshop.php?edit is called
if ($action == 'edit') {
  // Perform an extra security check
  if ($session_id != session_id()) { // Get out of here: incoming session id is not equal than current session id
    header("Location: ".e_BASE); // Redirect to the home page
    exit;
  }
	$count_items = count($_SESSION['shopping_cart']);     // Count number of different products in basket
  $sum_quantity = $_SESSION['sc_total']['items'];       // Display cached sum of total quantity of items in basket
  $sum_shipping = $_SESSION['sc_total']['shipping'];    // Display cached sum of shipping costs for 1st item
  $sum_shipping2 = $_SESSION['sc_total']['shipping2'];  // Display cached sum of shipping costs for additional items (>1)
  $sum_handling = $_SESSION['sc_total']['handling'];    // Display cached sum of handling costs
  $sum_shipping_handling = number_format(($sum_shipping + $sum_shipping2 + $sum_handling), 2, '.', ''); // Calculate total handling and shipping price
  $sum_price = number_format(($_SESSION['sc_total']['sum'] + $sum_shipping_handling), 2, '.', ''); // Display cached sum of total price of items in basket + shipping + handling costs
  $average_price = number_format(($sum_price / $sum_quantity), 2, '.', ''); // Calculate the average price per product

  // When total quantity is zero hide the basket
  if ($sum_quantity == 0) {
    // Manipulate return target location back to edit basket mode
    $target=('easyshop.php');
    header("Location: ".$target);
    exit;
  }
  $text2 = "";
  $text2 .= "
 	<div>
  <br />".EASYSHOP_PUBLICMENU_02."
  </div>
  ";

  // Fill the Cart with products from the basket
  $count_items = count($_SESSION['shopping_cart']); // Count number of different products in basket
  $array = $_SESSION['shopping_cart'];
  // Show products in a sequence starting at 1
  $cart_count = 1;
  // Set the header
  $text2 .= "
	<div style='text-align:center;'>
	<table border='0' cellspacing='1'>
  <tr>
  <td class='tbox'>".EASYSHOP_SHOP_21."</td>
  <td class='tbox'>".EASYSHOP_SHOP_22."</td>
  <td class='tbox'>".EASYSHOP_SHOP_23."</td>
  <td class='tbox'>".EASYSHOP_SHOP_24."</td>
  <td class='tbox'>".EASYSHOP_SHOP_25."</td>
  <td class='tbox'>".EASYSHOP_SHOP_26."</td>
  <td class='tbox'>".EASYSHOP_SHOP_27."</td>
  <td class='tbox'>".EASYSHOP_SHOP_28."</td>
  </tr>
  ";

  // For each product in the shopping cart array write PayPal details
    foreach($array as $id => $item) {
    // Debug info
    // echo "{$id}, {$item['item_name']}, {$item['quantity']}, {$item['item_price']}, {$item['sku_number']}, {$item['shipping']}, {$item['shipping2']}, {$item['handling']}";
    $display_sku_number = $item['sku_number'];
    if ($item['sku_number'] == "") {
      $display_sku_number = "&nbsp;"; // Force a space in the cell for proper border display
    }
    $text2 .= "
    <br />
    <tr>
    <td class='tbox'>".$display_sku_number."</td>
    <td class='tbox'>".$tp->toHTML($item['item_name'], true)."</td>
    <td class='tbox'>".$unicode_character_before.number_format($item['item_price'], 2, '.', '').$unicode_character_after."</td>
    <td class='tbox'>".$item['quantity']."</td>
    <td class='tbox'>".$unicode_character_before.number_format($item['shipping'], 2, '.', '').$unicode_character_after."</td>
    <td class='tbox'>".$unicode_character_before.number_format($item['shipping2'], 2, '.', '').$unicode_character_after."</td>
    <td class='tbox'>".$unicode_character_before.number_format($item['handling'], 2, '.', '').$unicode_character_after."</td>
    <td class='tbox'>
    <a href='easyshop_basket.php?delete.".$id."'><img src='".e_IMAGE."admin_images/delete_16.png' style='border-style:none;' alt='".EASYSHOP_SHOP_29."' title='".EASYSHOP_SHOP_29."'/></a>&nbsp;";
    
    // IPN addition - If Quantity is still less than available stock show add option
    if ((!isset($item['item_track_stock'])) || ($item['quantity'] < $item['item_instock'])) {
    $text2 .= "
    <a href='easyshop_basket.php?add.".$id."'><img src='".e_IMAGE."admin_images/up.png' border='noborder' alt='".EASYSHOP_SHOP_33."' title='".EASYSHOP_SHOP_33."'/></a>&nbsp;
     ";
    } 

    // If quantity equals 1 don't show minus option
    if ($item['quantity'] > 1) {
    $text2 .= "
    <a href='easyshop_basket.php?minus.".$id."'><img src='".e_IMAGE."admin_images/down.png' style='border-style:none;' alt='".EASYSHOP_SHOP_34."' title='".EASYSHOP_SHOP_34."'/></a>
    ";
    }

    $text2 .= "
    </td>
    </tr>
    ";
    $cart_count++;
  }

  $text2 .= "
  </table>
  <br />".EASYSHOP_SHOP_16." ".$sum_quantity."
  <br />".EASYSHOP_SHOP_17." ".$count_items."
  <br />".EASYSHOP_SHOP_18." ".$unicode_character_before.$sum_price.$unicode_character_after."
  <br />".EASYSHOP_SHOP_19." ".$unicode_character_before.$average_price.$unicode_character_after."
  ";
  if ($sum_shipping_handling > 0) {
  $text2 .= "
    <br />".EASYSHOP_SHOP_20." ".$unicode_character_before.$sum_shipping_handling.$unicode_character_after;
  }

  // Add the checkout button produced by function show_checkout
  $text2 .= "
  <div style='text-align:center;'>
  <a href=easyshop_basket.php?reset>".EASYSHOP_SHOP_30."</a> |
  <a href='javascript:history.go(-1);'>".EASYSHOP_SHOP_31."</a><br />";
  $text2 .= Shop::show_checkout($session_id);
  $text2 .= "
  </div>
  </div>
  ";

	// Render the value of $text in a table.
	$title = EASYSHOP_SHOP_32;
	$ns -> tablerender($title, $text2);
}

//-----------------------------------------------------------------------------+
//---------------------- Display a Category -----------------------------------+
//-----------------------------------------------------------------------------+
  if ($action == "cat" or $action == "prodpage") {
	if ($sql -> db_Select(DB_TABLE_SHOP_ITEM_CATEGORIES, "*", "category_id='".$action_id."' AND (category_class IN (".USERCLASS_LIST.")) ")){
  	if($row = $sql-> db_Fetch()){
  		$category_name = $row['category_name'];
  		$category_main_id  = $row['category_main_id'];
  	}
  } else {
    // No access to this category
	  define("e_PAGETITLE", PAGE_NAME);
	  require_once(HEADERF);
	  $ns->tablerender(EASYSHOP_SHOP_48,"<div style='text-align:center'>".EASYSHOP_SHOP_49."</div>");
	  require_once(FOOTERF);
	  exit;
  }

  if ($category_main_id <> "") {
  	$sql -> db_Select(DB_TABLE_SHOP_MAIN_CATEGORIES, "*", "main_category_id=".$category_main_id);
  	while($row = $sql-> db_Fetch()){
  	    $main_category_name = $row['main_category_name'];
  	}
  }

  // Determine the offset to display
  $item_offset = General::determine_offset($action,$page_id,$items_per_page);

  // Print the shop at the 'top' if the setting is not set to 'bottom' (value 1)
  if ($print_shop_top_bottom != '1') {
    $text .= print_store_header($store_name,$store_address_1,$store_address_2,$store_city,$store_state,$store_zip,$store_country,$support_email,$store_welcome_message,$print_shop_address);
  }
  
	$text .= "
	<br />
			<div style='width:100%; text-align:center;'>
				<fieldset>
					<legend style='padding:0 10px; margin-left:10px;'>
						<a href='easyshop.php'>".EASYSHOP_SHOP_03."</a> &raquo;";

   if (isset($main_category_name)) {
    $text .= " <a href='".$_GET['url']."?mcat.".$category_main_id."'><b>$main_category_name</b></a> &raquo; ";
   }

	$text .= "
             <b>$category_name</b>
					</legend>
					<br />";

					if ($existing_items == null) {
						$text .= "
						<br />
						<div style='text-align:center;'>
							<span class='smalltext'>
                ".EASYSHOP_SHOP_06."
							</span>
						</div>
						<br />";
					} else {
						$text .= "
							<table style='border:0; cellspacing:15; width:100%; text-align:center;'>";

								$text .= "
								<tr>";

    						// Total of active product items
    						$sql3 = new db;
    						$total_items = $sql3 -> db_Count(DB_TABLE_SHOP_ITEMS, "(*)", "WHERE item_active_status=2 AND category_id=".$action_id);

								$count_rows = 0;
								$sql -> db_Select(DB_TABLE_SHOP_ITEMS, "*", "item_active_status=2 AND category_id=".$action_id." ORDER BY item_order LIMIT $item_offset, $items_per_page");
								while($row = $sql-> db_Fetch()){
								  $item_id = $row['item_id'];
                  $category_id = $row['category_id'];
            	    $item_image = $row['item_image'];
            	    $item_name = $row['item_name'];
            	    $item_description = $row['item_description'];
                  $item_price = number_format($row['item_price'], 2, '.', '');
                  $sku_number = $row['sku_number'];
                  $shipping_first_item = $row['shipping_first_item'];
                  $shipping_additional_item = $row['shipping_additional_item'];
                  $handling_override = $row['handling_override'];
                  $item_out_of_stock = $row['item_out_of_stock'];
                  $item_out_of_stock_explanation = $row['item_out_of_stock_explanation'];
                  $prod_prop_1_id = $row['prod_prop_1_id'];
                  $prod_prop_2_id = $row['prod_prop_2_id'];
                  $prod_prop_3_id = $row['prod_prop_3_id'];
                  $prod_prop_4_id = $row['prod_prop_4_id'];
                  $prod_prop_5_id = $row['prod_prop_5_id'];
                  $prod_discount_id = $row['prod_discount_id'];

                  for ($n = 1; $n < 6; $n++){
                    // Clear properties (for next products in same category)
                    ${"prop".$n."_name"} = "";
                    ${"prop".$n."_list"} = "";
                    ${"prop".$n."_prices"} = "";
                    ${"prop".$n."_array"} = "";
                    ${"price".$n."_array"} = "";
                    $sql2 = new db;
                  	$sql2 -> db_Select(DB_TABLE_SHOP_PROPERTIES, "*", "property_id=".${"prod_prop_".$n."_id"});
                  	while($row2 = $sql2-> db_Fetch()){
                      if ($row2['prop_display_name'] <> "" or $row2['prop_display_name'] <> 0){
                  	    ${"prop".$n."_name"} = $row2['prop_display_name'];
                  	    ${"prop".$n."_list"} = $row2['prop_list'];
                  	    ${"prop".$n."_prices"} = $row2['prop_prices'];
                      }
                  	}
                  }

                  if ($prod_discount_id <> "") {
                    $sql3 = new db;
                  	$sql3 -> db_Select(DB_TABLE_SHOP_DISCOUNT, "*", "discount_id=".$prod_discount_id);
                  	if ($row3 = $sql3-> db_Fetch()){
                  	    $discount_id = $row3['discount_id'];
                  	    $discount_name = $row3['discount_name'];
                  	    $discount_class = $row3['discount_class'];
                  	    $discount_flag = $row3['discount_flag'];
                  	    $discount_price = $row3['discount_price'];
                  	    $discount_percentage = $row3['discount_percentage'];
                  	    $discount_valid_from = $row3['discount_valid_from'];
                  	    $discount_valid_till = $row3['discount_valid_till'];
                  	    $discount_code = $row3['discount_code'];
                  	}
                  }
                  if ($discount_valid_till == 0) {
                    $discount_valid_till = 9999999999; // set end date far away
                  }

									$text .= "
										<td style='width:$column_width; text-align:center;'>
											<br />";

												if ($item_image == '') {
													$text .= "
													&nbsp;";
												} else {
													$text .= "
													<a href='".e_SELF."?prod.".$item_id."'>
                            <img src='$store_image_path".$item_image."' style='border-style:none;' alt='' />
                          </a>
													";
												}

												$text .= "
												<br /><br />

												<div class='easyshop_prod_name'><a href='".e_SELF."?prod.".$item_id."'>".$item_name."</a></div>
												<br /><br />

												<b>".EASYSHOP_SHOP_10.": $unicode_character_before".number_format($item_price, 2, '.', '')." $unicode_character_after</b>
												<br /><br />

												<a href='".e_SELF."?prod.".$item_id."'>".EASYSHOP_SHOP_11."</a>
												<br /><br />";


												if ($item_out_of_stock == 2) {
                                                    $text .= "
                                                    <div style='color: red'>
                                                        <b>".EASYSHOP_SHOP_07."</b>
                                                    </div>
                                                    <b>".$item_out_of_stock_explanation."<b>";
                                                } else {

                          // Add to Cart at Category page
                          $text .= "
													<form method='post' action='easyshop_basket.php'>
              						<div>";

                          // Include selected properties in the category form
                          // Function include_prop returns array! [0] is for $text and [1] is for $property_prices!
                          $temp_array = include_prop($prop1_list, $prop1_array, $prop1_prices,$prop1_name,
                                                     $prop2_list, $prop2_array, $prop2_prices,$prop2_name,
                                                     $prop3_list, $prop3_array, $prop3_prices,$prop3_name,
                                                     $prop4_list, $prop4_array, $prop4_prices,$prop4_name,
                                                     $prop5_list, $prop5_array, $prop5_prices,$prop5_name,
                                                     $prop6_list, $prop6_array, $prop6_prices,$prop6_name,
                                                     $unicode_character_before, $unicode_character_after, $item_price);
                          $text .= $temp_array[0];
                          $property_prices = $temp_array[1];

                          // Include selected discount in the category form
                          // Function include_disc returns an array! [0] is for $text and [1] is for $item_price!
                          $temp_array = include_disc($discount_id, $discount_class, $discount_valid_from, $discount_valid_till,
                                                     $discount_code, $item_price, $discount_flag, $discount_percentage, $discount_price,
                                                     $property_prices, $unicode_character_before, $unicode_character_after, $print_discount_icons);
                          $text .= $temp_array[0];
                          $item_price = $temp_array[1];
                          unset($temp_array);

              						$text .= "
														<input type='hidden' name='item_id' value='".$item_id."'/>
														<input type='hidden' name='item_name' value='".$item_name."'/>
                            <input type='hidden' name='sku_number' value='".$sku_number."'/>
														<input type='hidden' name='item_price' value='".number_format($item_price, 2, '.', '')."'/>

														<input type='hidden' name='shipping' value='".number_format($shipping_first_item, 2, '.', '')."'/>
														<input type='hidden' name='shipping2' value='".number_format($shipping_additional_item, 2, '.', '')."'/>
														<input type='hidden' name='handling' value='".number_format($handling_override, 2, '.', '')."'/>

														<input type='hidden' name='category_id' value='".$action_id."'/>
                            <input type='hidden' name='fill_basket' value='C'/>";
                            
                            // Include properties lists hidden in the form
                            for ($n = 1; $n < 6; $n++){
                            $propname = "prop".$n."_name";
                            $proplist = "prop".$n."_list";
                            $propprices = "prop".$n."_prices";
                						$text .= "
                              <input type='hidden' name='$propname' value='".${"prop".$n."_name"}."'/>
                              <input type='hidden' name='$proplist' value='".${"prop".$n."_list"}."'/>
                              <input type='hidden' name='$propprices' value='".${"prop".$n."_prices"}."'/>";
                            }

                            // Include user id if user is logged in
                            if(USER){
                              $text .="<input type='hidden' name='custom' value='".USERID."'/>";
                            }

                            // IPN addition to include stock tracking option
                            if ($row['item_track_stock']== 2 && $enable_ipn == 2){   
                            $text .="   <input type='hidden' name='item_instock' value='".$row['item_instock']."'>
                                        <input type='hidden' name='item_track_stock' value='".$row['item_track_stock']."'>";                            
                            }
                            // IPN addition to include Item's database ID into session variable
                            $text .= " <input type='hidden' name='db_id' value='".$row['item_id']."'>";                                                             
                			$text .= "
                            <input type='hidden' name='return_url' value='".e_SELF.(e_QUERY ? '?'.e_QUERY : '')."'/>
                            <input class='button' name='submit' type='submit' value='".EASYSHOP_SHOP_08."'/>
                          </div>
													</form>";

												}

                        if (ADMIN && getperms("P")) { // Show admin icon when administrator
                          $text .= "
                          <div style='text-align:center;'>
                            <a href='admin_config.php?edit_item=1&amp;item_id=".$item_id."&amp;category_id=".$category_id."'><img style='border:0' src='".e_PLUGIN."easyshop/images/edit_16.png' alt='".EASYSHOP_CONF_ITM_22."' title='".EASYSHOP_CONF_ITM_22."'/></a>
                          </div>";
                        }

												$text .= "
										</td>";
										$count_rows++;

									if ($count_rows == $num_item_columns) {
										$text .= "
										</tr>
										<tr>";
										$count_rows = 0;
									}
									
									// To avoid confusion for the next to be fetched product; unset most important variables
									unset($item_id, $category_id, $item_image, $item_name, $item_description, $item_price, $sku_number,
                        $shipping_first_item, $shipping_additional_item, $handling_override, $item_out_of_stock, $item_out_of_stock_explanation,
                        $prod_prop_1_id, $prod_prop_2_id, $prod_prop_3_id, $prod_prop_4_id, $prod_prop_5_id,
                        $prod_discount_id, $discount_id);
								} // End of while fetch

							$text .= "
                  <td></td>
                </tr>
							</table>
						<br />";

						if ($active_items == null) {
							$text .= "
							<div style='text-align:center;'>
								<span class='smalltext'>
                  ".EASYSHOP_SHOP_06."
								</span>
							</div>";
						} else {
              $text .= Shop::show_checkout($session_id); // Code optimisation: make use of function show_checkout
            } // End of Else for show Categorie with active products

						$text .= General::multiple_paging($total_items,$items_per_page,$action,$action_id,$page_id,$page_devide_char);

						$text .= "
						<br />";
					}
				$text .= "
				</fieldset>
			</div>

	<br />";

  // Print the shop at the 'bottom' if the setting is set to 'bottom' (value 1)
  if ($print_shop_top_bottom == '1') {
    $text .= print_store_header($store_name,$store_address_1,$store_address_2,$store_city,$store_state,$store_zip,$store_country,$support_email,$store_welcome_message,$print_shop_address);
  }

	// Render the value of $text in a table.
	$title = EASYSHOP_SHOP_00;
	$ns -> tablerender($title, $text);
}

//-----------------------------------------------------------------------------+
//-------------------- Display a MAIN Category --------------------------------+
//-----------------------------------------------------------------------------+
  if ($action == "mcat" ) {
  // Count the number of categories with the given mcat id
	$total_categories = $sql->db_Count(DB_TABLE_SHOP_ITEM_CATEGORIES, "(*)", "WHERE category_active_status = '2' AND category_main_id='".$action_id."' AND (category_class IN (".USERCLASS_LIST.")) ");

  if ($total_categories > 0) {

  	$sql -> db_Select(DB_TABLE_SHOP_MAIN_CATEGORIES, "*", "main_category_id=".$action_id);
  	while($row = $sql-> db_Fetch()){
        $main_category_id = $row['main_category_id'];
  	    $main_category_name = $row['main_category_name'];
  	    $main_category_description = $row['main_category_description'];
  	    $main_category_image = $row['main_category_image'];
  	    $main_category_active_status = $row['main_category_active_status'];
  	}
  }

  // Determine the offset to display
  $item_offset = General::determine_offset($action,$page_id,$items_per_page);

  // Print the shop at the 'top' if the setting is not set to 'bottom' (value 1)
  if ($print_shop_top_bottom != '1') {
    $text .= print_store_header($store_name,$store_address_1,$store_address_2,$store_city,$store_state,$store_zip,$store_country,$support_email,$store_welcome_message,$print_shop_address);
  }

	$text .= "
	<br />
			<div style='width:100%; text-align:center;'>
				<fieldset>
					<legend style='padding:0 10px; margin-left:10px;'>
						<a href='easyshop.php'>".EASYSHOP_SHOP_03."</a> &raquo;";

   if (isset($main_category_id)) {
    $text .= " <a href='".$_GET['url']."?mcat.".$main_category_id."'><b>$main_category_name</b></a>";
   }
   $text .= "
					</legend>                                  `
					<br />";

					if (!isset($main_category_id) AND ($total_categories > 0)) {
						$text .= "
						<br />
						<div style='text-align:center;'>
							<span class='smalltext'>
                ".EASYSHOP_SHOP_42."
							</span>
						</div>
						<br />";
					} else {
						$text .= "
							<table style='border:0; cellspacing:15; width:100%; text-align:center;'>";

								$text .= "
								<tr>";

								$count_rows = 0;
								$sql -> db_Select(DB_TABLE_SHOP_ITEM_CATEGORIES, "*", "category_active_status=2 AND category_main_id='".$action_id."' AND (category_class IN (".USERCLASS_LIST.")) ORDER BY category_order LIMIT $item_offset, $items_per_page");
								while($row = $sql-> db_Fetch()){
									$text .= "
										<td style='width:$column_width; text-align:center;'>
											<br />";

												if ($row['category_image'] == '') {
													$text .= "
													&nbsp;";
												} else {
													$text .= "
													<a href='".e_SELF."?cat.".$row['category_id']."'><img src='$store_image_path".$row['category_image']."' style='border-style:none;' alt='' /></a><br /><br />
													<div class='easyshop_cat_name'><a href='".e_SELF."?cat.".$row['category_id']."'>".$row['category_name']."</a></div><br />
                            ".$tp->toHTML($row['category_description'], true)."<br />
													";
												}

                        // Count the total of products per category
  										  $sql2 = new db;
												$total_products_category = $sql2->db_Count(DB_TABLE_SHOP_ITEMS, "(*)", "WHERE item_active_status = '2' AND category_id=".$row['category_id']);

                        ($total_products_category <> 1)? $prod_text = EASYSHOP_SHOP_43 : $prod_text = EASYSHOP_SHOP_44;
   											$text .= $total_products_category ." ".$prod_text." ".EASYSHOP_SHOP_45."
										</td>";
										$count_rows++;

									if ($count_rows == $num_category_columns) {
										$text .= "
										</tr>
										<tr>";
										$count_rows = 0;
									}
								}

							$text .= "
							</tr>
							</table>
						<br />";

						if ($total_categories == null or $total_categories == 0) {
							$text .= "
							<div style='text-align:center;'>
								<span class='smalltext'>
                  ".EASYSHOP_SHOP_04."
								</span>
							</div>";
						} else {
              $text .= Shop::show_checkout($session_id); // Code optimisation: make use of function show_checkout
            } // End of Else for show Categorie with active products

						$text .= General::multiple_paging($total_categories,$items_per_page,$action,$action_id,$page_id,$page_devide_char);

						$text .= "
						<br />";
					}
				$text .= "
				</fieldset>
			</div>
	<br />";

  // Print the shop at the 'bottom' if the setting is set to 'bottom' (value 1)
  if ($print_shop_top_bottom == '1') {
    $text .= print_store_header($store_name,$store_address_1,$store_address_2,$store_city,$store_state,$store_zip,$store_country,$support_email,$store_welcome_message,$print_shop_address);
  }

	// Render the value of $text in a table.
	$title = EASYSHOP_SHOP_00;
	$ns -> tablerender($title, $text);
}

//-----------------------------------------------------------------------------+
//----------------------- Display a Product -----------------------------------+
//-----------------------------------------------------------------------------+
if ($action == "prod") {

	if($sql -> db_Count(DB_TABLE_SHOP_ITEM_CATEGORIES, "(*)", "WHERE category_active_status = 2  AND (category_class IN (".USERCLASS_LIST.")) ") > 0) {
		$no_categories = 1;
	}

	// Fetch details per product
	$sql -> db_Select(DB_TABLE_SHOP_ITEMS, "*", "item_id=".$action_id);
  if ($row = $sql-> db_Fetch()){
      $item_id = $row['item_id'];
      $category_id = $row['category_id'];
	    $item_image = $row['item_image'];
	    $item_name = $row['item_name'];
	    $item_description = $row['item_description'];
      $item_price = number_format($row['item_price'], 2, '.', '');
      $sku_number = $row['sku_number'];
      $shipping_first_item = $row['shipping_first_item'];
      $shipping_additional_item = $row['shipping_additional_item'];
      $handling_override = $row['handling_override'];
      $item_out_of_stock = $row['item_out_of_stock'];
      $item_out_of_stock_explanation = $row['item_out_of_stock_explanation'];
      $prod_prop_1_id = $row['prod_prop_1_id'];
      $prod_prop_2_id = $row['prod_prop_2_id'];
      $prod_prop_3_id = $row['prod_prop_3_id'];
      $prod_prop_4_id = $row['prod_prop_4_id'];
      $prod_prop_5_id = $row['prod_prop_5_id'];
      $prod_discount_id = $row['prod_discount_id'];
      // IPN addition adding item_instock, track stock and database ID to checkout data
      $item_instock = $row['item_instock'];
      $item_track_stock = $row['item_track_stock'];
      $db_id = $row['item_id'];      
	}

	if ($sql -> db_Select(DB_TABLE_SHOP_ITEM_CATEGORIES, "*", "category_id='".$category_id."' AND (category_class IN (".USERCLASS_LIST.")) ")){
  	if ($row = $sql-> db_Fetch()){
  		$category_name = $row['category_name'];
  		$category_main_id  = $row['category_main_id'];
  	}
  } else {
    // No access to this category
	  define("e_PAGETITLE", PAGE_NAME);
	  require_once(HEADERF);
	  $ns->tablerender(EASYSHOP_SHOP_48,"<div style='text-align:center'>".EASYSHOP_SHOP_49."</div>");
	  require_once(FOOTERF);
	  exit;
  }

  if ($category_main_id <> "") {
  	$sql -> db_Select(DB_TABLE_SHOP_MAIN_CATEGORIES, "*", "main_category_id=".$category_main_id);
  	if ($row = $sql-> db_Fetch()){
  	    $main_category_name = $row['main_category_name'];
  	}
  }

  for ($n = 1; $n < 6; $n++){
  	$sql -> db_Select(DB_TABLE_SHOP_PROPERTIES, "*", "property_id=".${"prod_prop_".$n."_id"});
  	if ($row = $sql-> db_Fetch()){
  	    ${"prop".$n."_name"} = $row['prop_display_name'];
  	    ${"prop".$n."_list"} = $row['prop_list'];
  	    ${"prop".$n."_prices"} = $row['prop_prices'];
  	}
  }
  
  if ($prod_discount_id <> "") {
  	$sql -> db_Select(DB_TABLE_SHOP_DISCOUNT, "*", "discount_id=".$prod_discount_id);
  	if ($row = $sql-> db_Fetch()){
  	    $discount_id = $row['discount_id'];
  	    $discount_name = $row['discount_name'];
  	    $discount_class = $row['discount_class'];
  	    $discount_flag = $row['discount_flag'];
  	    $discount_price = $row['discount_price'];
  	    $discount_percentage = $row['discount_percentage'];
  	    $discount_valid_from = $row['discount_valid_from'];
  	    $discount_valid_till = $row['discount_valid_till'];
  	    $discount_code = $row['discount_code'];
  	}
  }
  
  if ($discount_valid_till == 0) {
    $discount_valid_till = 9999999999; // set end date far away
  }

  // Print the shop at the 'top' if the setting is not set to 'bottom' (value 1)
  if ($print_shop_top_bottom != '1') {
    $text .= print_store_header($store_name,$store_address_1,$store_address_2,$store_city,$store_state,$store_zip,$store_country,$support_email,$store_welcome_message,$print_shop_address);
  }

	$text .= "
	<br />
	<div style='width:100%'>
		<fieldset>
			<legend style='padding:0 10px; margin-left:10px;'>
				<a href='easyshop.php'>".EASYSHOP_SHOP_03."</a> &raquo;";
   if ($category_main_id <> "0") {
    $text .= " <a href='".$_GET['url']."?mcat.".$category_main_id."'><b>$main_category_name</b></a> &raquo; ";
   }
   $text .= "
        <a href='".$_GET['url']."?cat.".$category_id."'><b>$category_name</b></a> &raquo;
        <b>$item_name</b>
			</legend>
			<br />
			<table style='border:0; cellspacing:15; width=95%; text-align:center;'>
				<tr>
					<td style='width:50%; valign:top;'>
						<div class='easyshop_prod_box'>

							<div class='easyshop_prod_img'><img src='".$store_image_path.$item_image."' style='border-style:none;' alt='' /></div><br />
							<br /><div class='easyshop_prod_name'>".$item_name."</div><br/>";
							
              // Display the SKU number if it is filled in
              if ($item['sku_number'] <> "") {
                $text .= "<br />".EASYSHOP_SHOP_21.":&nbsp;".$sku_number;
              }

    $text .= "
						</div>
						<br />
					</td>
					<td style='width:50%; valign:center;'>
					".$tp->toHTML($item_description, true)."
						<br /><br />";

         	$text .= "<b>".EASYSHOP_SHOP_10.":</b> $unicode_character_before".number_format($item_price, 2, '.', '')."$unicode_character_after<br />";

          // Conditionally print additional costs if they are more than zero
          if ($shipping_first_item > 0 ){
          	$text .= "
        						<b>".EASYSHOP_SHOP_12.":</b> $unicode_character_before".number_format($shipping_first_item, 2, '.', '')."$unicode_character_after<br />
        						";
          }

          if ($shipping_additional_item > 0 ){
          	$text .= "
        						<b>".EASYSHOP_SHOP_13.":</b> $unicode_character_before".number_format($shipping_additional_item, 2, '.', '')."$unicode_character_after<br />
        						";
          }

          if ($handling_override > 0 ){
          	$text .= "
        						<b>".EASYSHOP_SHOP_14.":</b> $unicode_character_before".number_format($handling_override, 2, '.', '')."$unicode_character_after<br />
        						";
          }
          
              if ($item_out_of_stock == 2) {
                  $text .= "
                  <div style='color: red'>
                      <b>".EASYSHOP_SHOP_07."</b>
                  </div>
                  <b>$item_out_of_stock_explanation<b>";
              } else {
                $prop1_count = $sql->db_Count(DB_TABLE_SHOP_ITEM_CATEGORIES, "(*)", "WHERE item_id='".$action_id."' AND (category_class IN (".USERCLASS_LIST.")) ");
                if ($prop1_count = 0) {
                    // Error that should not happen! Indicate that item_id does not exists.
                    $text .= EASYSHOP_SHOP_15;
                }

                // Add to Cart at Product Details page
                $text .= "
                            <br />
    						<form method='post' action='easyshop_basket.php'>
    						<div>";

                // Include selected properties in the product form
                // Function include_prop returns an array! [0] is for $text and [1] is for $property_prices!
                $temp_array = include_prop($prop1_list, $prop1_array, $prop1_prices,$prop1_name,
                                           $prop2_list, $prop2_array, $prop2_prices,$prop2_name,
                                           $prop3_list, $prop3_array, $prop3_prices,$prop3_name,
                                           $prop4_list, $prop4_array, $prop4_prices,$prop4_name,
                                           $prop5_list, $prop5_array, $prop5_prices,$prop5_name,
                                           $prop6_list, $prop6_array, $prop6_prices,$prop6_name,
                                           $unicode_character_before, $unicode_character_after, $item_price);
                $text .= $temp_array[0];
                $property_prices = $temp_array[1];
                unset($temp_array);

                // Include selected discount in the product form
                // Function include_disc returns an array! [0] is for $text and [1] is for $item_price!
                $temp_array = include_disc($discount_id, $discount_class, $discount_valid_from, $discount_valid_till,
                                           $discount_code, $item_price, $discount_flag, $discount_percentage, $discount_price,
                                           $property_prices, $unicode_character_before, $unicode_character_after, $print_discount_icons);
                $text .= $temp_array[0];
                $item_price = $temp_array[1];
                unset($temp_array);

                // Include also currency sign to send it to the basket
                // Send the product data to the basket
    						$text .= "
    							<input type='hidden' name='unicode_character_before' value='".$unicode_character_before."'/>
    							<input type='hidden' name='unicode_character_after' value='".$unicode_character_after."'/>

    							<input type='hidden' name='item_id' value='".$item_id."'/>
    							<input type='hidden' name='item_name' value='".$item_name."'/>
                  <input type='hidden' name='sku_number' value='".$sku_number."'/>
    							<input type='hidden' name='item_price' value='".number_format($item_price, 2, '.', '')."'/>

    							<input type='hidden' name='shipping' value='".number_format($shipping_first_item, 2, '.', '')."'/>
    							<input type='hidden' name='shipping2' value='".number_format($shipping_additional_item, 2, '.', '')."'/>
    							<input type='hidden' name='handling' value='".number_format($handling_override, 2, '.', '')."'/>

    							<input type='hidden' name='category_id' value='".$category_id."'/>";
                                
                            // IPN addition to include stock tracking option
                            if ($item_track_stock== 2 && $enable_ipn == 2){   
                              $text .=" <input type='hidden' name='item_instock' value='".$item_instock."'>
                                        <input type='hidden' name='item_track_stock' value='".$item_track_stock."'>";                                      }
                            
                            // IPN addition to include Item's database ID into session variable
                            $text .= " <input type='hidden' name='db_id' value='".$db_id."'>";                                                                           
                  $text .="              
                  <input type='hidden' name='fill_basket' value='P'/>";

                // Include properties lists hidden in the form
                for ($n = 1; $n < 6; $n++){
                $propname = "prop".$n."_name";
                $proplist = "prop".$n."_list";
                $propprices = "prop".$n."_prices";
    						$text .= "
                  <input type='hidden' name='$propname' value='".${"prop".$n."_name"}."'/>
                  <input type='hidden' name='$proplist' value='".${"prop".$n."_list"}."'/>
                  <input type='hidden' name='$propprices' value='".${"prop".$n."_prices"}."'/>";
                }

                // Include user id if user is logged in
                if(USER){
                  $text .="<input type='hidden' name='custom' value='".USERID."'/>";
                }

    						$text .= "
                  <input type='hidden' name='return_url' value='".e_SELF.(e_QUERY ? '?'.e_QUERY : '')."'/>
                  <input class='button' type='submit' value='".EASYSHOP_SHOP_08."'/>
                </div>
    						</form>";
              } // End of the Else for an active product in the Details view

              // View Cart at Product Details page
              $text .= Shop::show_checkout($session_id); // Code optimisation: make use of function show_checkout

						$text .= "
						<br />
					</td>
				</tr>
			</table>
		</fieldset>
	</div>
  ";

  if (ADMIN && getperms("P")) { // Show admin icon when administrator
    $text .= "
    <div style='text-align:right;'>
      <a href='admin_config.php?edit_item=1&amp;item_id=".$item_id."&amp;category_id=".$category_id."'><img style='border:0' src='".e_PLUGIN."easyshop/images/edit_16.png' alt='".EASYSHOP_CONF_ITM_22."' title='".EASYSHOP_CONF_ITM_22."'/></a>
    </div>";
  }

  // Print the shop at the 'bottom' if the setting is set to 'bottom' (value 1)
  if ($print_shop_top_bottom == '1') {
    $text .= print_store_header($store_name,$store_address_1,$store_address_2,$store_city,$store_state,$store_zip,$store_country,$support_email,$store_welcome_message,$print_shop_address);
  }

  if ($enable_comments == 1) { // Show comment totals or 'Be the first to comment etc' when total is zero when setting is enabled
    if (General::getCommentTotal(easyshop, $item_id) == 0) {
      $text .= "<br/>".EASYSHOP_SHOP_38;
    } else {
      $text .= "<br/>".EASYSHOP_SHOP_39.": ".General::getCommentTotal(easyshop, $item_id);
    }
  }

	// Render the value of $text in a table.
	$title = EASYSHOP_SHOP_00;
	$ns -> tablerender($title, $text);

  if ($enable_comments == 1) { // Show comments and input comments form when setting is enabled
    // Show comments input section
    $comment_to = $item_id;
    $comment_sub = "Re: " . $tp->toFORM($item_name, false);
    $cobj->compose_comment("easyshop", "comment", $comment_to, $width, $comment_sub, $showrate = false);
    if (isset($_POST['commentsubmit']))
    {
       $cobj->enter_comment($_POST['author_name'], $_POST['comment'], "easyshop", $comment_to, $pid, $_POST['subject']);
       $target=('easyshop.php?prod.'.$item_id);
       header("Location: ".$target);
    }
  }
}

//-----------------------------------------------------------------------------+
//----------------------- Show All Categories ---------------------------------+
//-----------------------------------------------------------------------------+
if($action == "allcat" or $action == "catpage" or $action == "blanks") {

  if ($action == "blanks") {
   $add_where = " AND category_main_id= '' ";
  }
  $categories_count = $sql -> db_Count(DB_TABLE_SHOP_ITEM_CATEGORIES, "(*)", "WHERE category_active_status = 2 ".$add_where." AND (category_class IN (".USERCLASS_LIST."))");
	if($categories_count > 0) {
		$no_categories = 1;
	}
  // Print the shop at the 'top' if the setting is not set to 'bottom' (value 1)
  if ($print_shop_top_bottom != '1') {
    $text .= print_store_header($store_name,$store_address_1,$store_address_2,$store_city,$store_state,$store_zip,$store_country,$support_email,$store_welcome_message,$print_shop_address);
  }

  // Determine the offset to display
  $category_offset = General::determine_offset($action,$action_id,$categories_per_page);

	$text .= "
	<br />
	<form method='post' action='admin_categories_edit.php'>
		<div style='text-align:center;'>
			<div style='width:100%'>
				<fieldset>
					<legend style='padding:0 10px; margin-left:10px;'>";
					if ($action == "blanks") {
            $text .= "<a href='easyshop.php'>".EASYSHOP_SHOP_40."</a> &raquo;";
					}
					$text .= "
						<b>";
            if ($action == "blanks") {
              $text .= EASYSHOP_SHOP_46;
            } else {
              $text .= EASYSHOP_SHOP_03;
            }
           $text .= "
           </b>
					</legend>
					<br />";
					if (!isset($no_categories)) {
						$text .= "
						<br />
            <div style='text-align:center;'>
							<span class='smalltext'>
								".EASYSHOP_SHOP_04."
							</span>
						</div>
						<br />";
					} else {
						$text .= "
              <div style='text-align:center;'>
							<table border='0' cellspacing='15' width='100%'>";

								$text .= "
								<tr>";

								$count_rows = 0;
								$sql -> db_Select(DB_TABLE_SHOP_ITEM_CATEGORIES, "*", "category_active_status=2 $add_where AND (category_class IN (".USERCLASS_LIST.")) ORDER BY category_order LIMIT $category_offset, $categories_per_page");
								while($row = $sql-> db_Fetch()){
									$text .= "
										<td style='width:$column_width;'>
											<br />
                      <div style='text-align:center;'>
												<div class='easyshop_cat_name'><a href='".e_SELF."?cat.".$row['category_id']."'>".$row['category_name']."</a></div>
												<br />";

												if ($row['category_image'] == '') {
													$text .= "
													&nbsp;";
												} else {
													$text .= "
													<br /><a href='".e_SELF."?cat.".$row['category_id']."'><img src='$store_image_path".$row['category_image']."' style='border-style:none;' alt='' /></a><br />
													";
												}

												$text .= "
												<br />
												".$tp->toHTML($row['category_description'], true)."
												<br />";

                        // Count the total of products per category
  										  $sql2 = new db;
												$total_products_category = $sql2->db_Count(DB_TABLE_SHOP_ITEMS, "(*)", "WHERE item_active_status = '2' AND category_id='".$row['category_id']."'");
                        // Display 'product' or 'products'
                        ($total_products_category <> 1)? $prod_text = EASYSHOP_SHOP_43 : $prod_text = EASYSHOP_SHOP_44;
   											$text .= $total_products_category ." ".$prod_text." ".EASYSHOP_SHOP_45;

                        // Display if category if class specific
                        if ($row['category_class'] > 0 ) {
                          $text .= "<br/><i>".EASYSHOP_SHOP_54."</i>";
                        }

										$text .= "
											</div>
										</td>";
										$count_rows++;

									if ($count_rows == $num_category_columns) {
										$text .= "
										</tr>
										<tr>";
										$count_rows = 0;
									}
								}

							$text .= "
							</tr>
							</table>
						</div>
						<br />";

						$total_categories = $sql -> db_Count(DB_TABLE_SHOP_ITEM_CATEGORIES, "(*)", "WHERE category_active_status=2".$add_where." AND (category_class IN (".USERCLASS_LIST."))");
						$text .= General::multiple_paging($total_categories,$categories_per_page,$action,$action_id,$page_id,$page_devide_char);

						$text .= "
						<br />";
					}
				$text .= "
				</fieldset>
			</div>
		</div>
	</form>
	<br />";
	
	if ($_SESSION['sc_total']['items'] < 1) { // Solve the layout misfit problem
    $text .="</div>";
	}
  $text .= "<div style='text-align:center;'>".Shop::show_checkout($session_id)."</div>"; // Code optimisation: make use of function show_checkout

  // Print the shop at the 'bottom' if the setting is set to 'bottom' (value 1)
  if ($print_shop_top_bottom == '1') {
    $text .= print_store_header($store_name,$store_address_1,$store_address_2,$store_city,$store_state,$store_zip,$store_country,$support_email,$store_welcome_message,$print_shop_address);
  }

	// Render the value of $text in a table.
	$title = EASYSHOP_SHOP_00;
	$ns -> tablerender($title, $text);
}

//-----------------------------------------------------------------------------+
//-------------------- Show All MAIN Categories -------------------------------+
//-----------------------------------------------------------------------------+
if($action == "" or $action == "mcatpage") {

	$main_categories = ($sql -> db_Count(DB_TABLE_SHOP_MAIN_CATEGORIES, "(*)", "WHERE main_category_active_status = 2") > 0);
  // Print the shop at the 'top' if the setting is not set to 'bottom' (value 1)
  if ($print_shop_top_bottom != '1') {
    $text .= print_store_header($store_name,$store_address_1,$store_address_2,$store_city,$store_state,$store_zip,$store_country,$support_email,$store_welcome_message,$print_shop_address);
  }

  // Determine the offset to display
  $main_category_offset = General::determine_offset($action,$action_id,$categories_per_page);

	$text .= "
	<br />
	<form method='post' action='admin_categories_edit.php'>
		<div style='text-align:center;'>
			<div style='width:100%'>
				<fieldset>
					<legend style='padding:0 10px; margin-left:10px;'>
						<b>".EASYSHOP_SHOP_40."</b>
					</legend>
					<br />";
					if ($main_categories < 1) {
            // Redirect to easyshop.php?allcat if there are no main categories (backwards compatability for 1.2 functionality)
            header("Location: "."easyshop.php?allcat");
					} else {
						$text .= "
              <div style='text-align:center;'>
							<table border='0' cellspacing='15' width='100%'>";

								$text .= "
								<tr>";

								$count_rows = 0;
								//$sql -> db_Select(DB_TABLE_SHOP_MAIN_CATEGORIES, "*", "main_category_active_status=2 ORDER BY main_category_order LIMIT $main_category_offset, $categories_per_page");
								
               $sql5 = new db;
          			// Only display main category records in use
         			 $arg5= "SELECT DISTINCT category_main_id, main_category_id, main_category_name, main_category_image, main_category_description
                       FROM #easyshop_item_categories, #easyshop_main_categories
                       WHERE category_main_id = main_category_id AND main_category_active_status = '2'
                       ORDER BY main_category_name
                       LIMIT $main_category_offset, $categories_per_page";
                $sql5->db_Select_gen($arg5,false);
          			while($row5 = $sql5-> db_Fetch()){

								//while($row = $sql-> db_Fetch()){
									$text .= "
										<td style='width:$column_width;'>
											<br />
                      <div style='text-align:center;'>
												<div class='easyshop_main_cat_name'><a href='".e_SELF."?mcat.".$row5['main_category_id']."'>".$row5['main_category_name']."</a></div>
												<br />";

												if ($row5['main_category_image'] == '') {
													$text .= "
													&nbsp;";
												} else {
													$text .= "
													<a href='".e_SELF."?mcat.".$row5['main_category_id']."'><img src='$store_image_path".$row5['main_category_image']."' style='border-style:none;' alt='' /></a>
													";
												}

                        // Count active Product Categories with the current fetched Main Category and show them additionally below description
                        $sql8 = new db;
                        $cat_with_this_main = $sql8 -> db_Count(DB_TABLE_SHOP_ITEM_CATEGORIES, "(*)", "WHERE category_active_status = 2 AND category_main_id= '".$row5['main_category_id']."' AND (category_class IN (".USERCLASS_LIST.")) ");

												$text .= "
												<br />
												".$tp->toHTML($row5['main_category_description'], true)."
												<br />($cat_with_this_main)
											</div>
										</td>";
										$count_rows++;

									if ($count_rows == $num_category_columns) {
										$text .= "
										</tr>
										<tr>";
										$count_rows = 0;
									}
								} // End of while of fetching all main categories in use
								
                // Count active Product Categories without Main Category and show them additionally on last page
                $sql7 = new db;
                $cat_without_main = $sql7 -> db_Count(DB_TABLE_SHOP_ITEM_CATEGORIES, "(*)", "WHERE category_active_status = 2 AND category_main_id= '' AND (category_class IN (".USERCLASS_LIST.")) ");
                if ($cat_without_main > 0) {
  									$text .= "
  										<td style='width:$column_width;'>
  											<br />
                        <div style='text-align:center;'>
  												<a href='".e_SELF."?blanks'><b>".EASYSHOP_SHOP_46."</b></a>
  												<br />
                            ($cat_without_main)
  												<br />
  											</div>
  										</td>
                      </tr>";
  										$count_rows++;
                } // End of if $cat_without_main

							$text .= "
							</tr>
							</table>
						</div>
						<br />";

               $sql6 = new db;
          			// Only display main category records in use
         			 $arg6 ="SELECT DISTINCT category_main_id, main_category_id, main_category_name, main_category_image, main_category_description
                       FROM #easyshop_item_categories, #easyshop_main_categories
                       WHERE category_main_id = main_category_id AND main_category_active_status = '2'
                       ";
                $sql6->db_Select_gen($arg6,false);
          			while($row6 = $sql6-> db_Fetch()){
                  $count_total_categories++;
                }

            $total_categories = $count_total_categories;
						$text .= General::multiple_paging($total_categories,$categories_per_page,$action,$action_id,$page_id,$page_devide_char);

						$text .= "
						<br />";
					}
				$text .= "
				</fieldset>
			</div>
		</div>
	</form>
	<br />";
  $text .= "<div style='text-align:center;'>".Shop::show_checkout($session_id)."</div>"; // Code optimisation: make use of function show_checkout

  // Print the shop at the 'bottom' if the setting is set to 'bottom' (value 1)
  if ($print_shop_top_bottom == '1') {
    $text .= print_store_header($store_name,$store_address_1,$store_address_2,$store_city,$store_state,$store_zip,$store_country,$support_email,$store_welcome_message,$print_shop_address);
  }

	// Render the value of $text in a table.
	$title = EASYSHOP_SHOP_00;
	$ns -> tablerender($title, $text);
}

function print_store_header($p_name,$p_address_1,$p_address_2,$p_city,$p_state,$p_zip,$p_country,$p_email,$p_welcome_message,$p_print_shop_address){
	if ((($p_address_1 == '') && ($p_address_2 == '') && ($p_city == '') && ($p_state == '') && ($p_zip == '') && ($p_country == '')) or $p_print_shop_address != '1') {
		$display_message = null;
	} else {
		$display_message = 1;
	}
	$sh_text .= "
	<br />
	<div style='width:100%'>
      <div style='text-align:center;'>
			<table border='0' cellspacing='15' width='100%'>
				<tr>
					<td style='width:50%; valign:top;'>
						<div>
							<span class='smalltext'>";
								if ($display_message == null) {
									// Don't display address
								} else {
									$sh_text .= "
									<b>".EASYSHOP_SHOP_01."</b>
									<br />
									$p_name";
									if ($p_address_1 != null){
										$sh_text .= "
										<br />
										$p_address_1";
									}
									if ($p_address_2 !=null){
										$sh_text .= "
										<br />
										$p_address_2";
									}
									if ($p_city != null){
										$sh_text .= "
										<br />
										$p_city,";
									}
									if (($p_address_1 == null) && ($p_address_2 == null) && ($p_city == null)) {
										$sh_text .= "
										<br />";
									}
									if ($p_state != null){
										$sh_text .= "
										$p_state";
									}
									if ($p_zip != null){
										$sh_text .= "
										$p_zip";
									}
									if (($p_address_1 == null) && ($p_address_2 == null) && ($p_city == null) && ($p_state == null) && ($p_zip == null)) {
										// Don't add a line break
									} else {
										$sh_text .= "
										<br />";
									}
									if ($p_country != null){
										$sh_text .= "
										$p_country";
									}
									$sh_text .= "
									<br />";
  								if ($p_email != '') {
  									$sh_text .= "
  									<b>".EASYSHOP_SHOP_02."</b>
  									<br />
                    ";
                    // Security: protect shop e-mail from e-mail harvasting
                    // Method: split the contact e-mail and present it in inline javascript
                    $email = split("@", $p_email); //split e-mail address at the @-sign
                      $p_email_name = $email[0]; // everything before the @-sign
                    $tld = split(".", $email[1]); //split the part after the @-sign on dot-sign
                    //Now use an if->else to find out if it's a subdomain or not
                    if(count($tld) == 2) {
                      //Normal simple address as someone@blah.com
                      $p_email_domain = $email[0]; // domain = blah
                      $p_email_tld = $email[1]; // tld = .com
                    } else { // Subdomains like someone@blah.org.uk
                      // Determine the last tld expression
                      $last_dot = strrchr(".",$email[1]);
                      $p_email_domain = substr($email[1], 0, $last_dot); // domain = blah.org
                      $p_email_tld = substr($email[1], $last_dot); // tld = .uk
                    }
                    // Display the splitted e-mail in an inline javascript where we join them to one e-mail address
                    $sh_text .= "
                    <a href=\"#\" onclick=\"JavaScript:window.location='mailto:'+'".$p_email_name."'+'@'+'".$p_email_domain."'+'".$p_email_tld."'\">".EASYSHOP_SHOP_47."</a>
                    ";
  								} // End of showing e-mail when filled in
								} // End of else of displaying address
							$sh_text .= "
							</span>
						</div>
						<br />
					</td>
					<td style='width:50%; text-align:top;'>
						".$p_welcome_message."
					</td>
				</tr>
			</table>
			</div>
	</div>";
	return $sh_text;
}

function include_prop($prop1_list, $prop1_array, $prop1_prices,$prop1_name,
                      $prop2_list, $prop2_array, $prop2_prices,$prop2_name,
                      $prop3_list, $prop3_array, $prop3_prices,$prop3_name,
                      $prop4_list, $prop4_array, $prop4_prices,$prop4_name,
                      $prop5_list, $prop5_array, $prop5_prices,$prop5_name,
                      $prop6_list, $prop6_array, $prop6_prices,$prop6_name,
                      $unicode_character_before, $unicode_character_after, $item_price ) {
  // Function provides the property select boxes for category and product details and signals if property prices have been used
  for ($n = 1; $n < 6; $n++){
   if (${"prop".$n."_list"} <> "") {
      ${"prop".$n."_array"} = explode(",", ${"prop".$n."_list"});
      if (${"prop".$n."_prices"} <> "") {
        ${"price".$n."_array"} = explode(",", ${"prop".$n."_prices"});
      }
      $text .= "<b>".${"prop".$n."_name"}.":</b> "; // Name of property list
      $text .= "<select class='tbox' name='prod_prop_$n'>";
      // Add an empty value for the property list; check in easyshop_basket if value is selected
      $text .= "<option value=' ' selected='selected'>&nbsp;</option>";
      $arrayLength = count(${"prop".$n."_array"});
      for ($i = 0; $i < $arrayLength; $i++){
          $text .= "<option value='".${"prop".$n."_array"}[$i]."'>".${"prop".$n."_array"}[$i];
          // Display different price if corresponding price delta in properties is found
          if (${"price".$n."_array"}[$i] <> 0) {
            $text .= "&nbsp;".$unicode_character_before.number_format(($item_price+${"price".$n."_array"}[$i]), 2, '.', '')."&nbsp;".$unicode_character_after;
            $property_prices = 1; // There is at least one or more property price detected; use in discounts
          }
          $text .= "</option>";
      }
      $text .= "</select><br/>";
   }
 }
 return array($text, $property_prices);
}

function include_disc ($discount_id, $discount_class, $discount_valid_from, $discount_valid_till,
                       $discount_code, $item_price, $discount_flag, $discount_percentage, $discount_price,
                       $property_prices, $unicode_character_before, $unicode_character_after, $print_discount_icons){
  // Function provides the discount handling for category and product details and returns discount price (when no discount code is applied)
  // Include selected discount in the product form
  if (isset($discount_id)) { // Include the product discount if it is filled in
    $text .=  "<input type='hidden' name='discount_id' value='".$discount_id."'/>";
    // Determine if user class if applicable for this discount
    if (check_class($discount_class)) {
      // Determine if discount date is valid
      $today = time(); // Record the current date/time stamp
      if ($today > $discount_valid_from and $today < $discount_valid_till) { // This moment is between start and end date of discount
        if ($discount_code <> "") { // Ask the discount code to activate discount
          $text .= "<b>".EASYSHOP_SHOP_50.":</b><br/> <input class='tbox' size='25' type='text' name='discount_code' /><br/>"; // Discount code
        } else { // Apply the discount straight away; no discount code needed
          // Adjust item price
          $old_item_price = number_format($item_price, 2, '.', '');
          if ($discount_flag == 1) { // Discount percentage
            $item_price = number_format($item_price -  ( ( $discount_percentage / 100) * $item_price ), 2, '.', '');
          }
          else { // Discount amount
            $item_price = $item_price - $discount_price;
          }
          // Protection against a discount that makes the price negative
          if ($item_price < 0) {
            $item_price = 0;
          }
          if ($property_prices != 1) { // Without variable property prices we can indicate the new price
            // Display From For text
            $text .= EASYSHOP_SHOP_51." ".$unicode_character_before.$old_item_price.$unicode_character_after." ".EASYSHOP_SHOP_52." ".$unicode_character_before.number_format($item_price.$unicode_character_after, 2, '.', '')."<br/>";
          } else { // Only able to tell there will be a discount due to unknown property selection with price delta
            $text .= EASYSHOP_SHOP_53." ";
            if ($discount_flag == 1) { // Discount percentage
              $text .= $discount_percentage."%<br/>";
            }
            else { // Discount amount
              $text .= $unicode_character_before.number_format($discount_price, 2, '.', '').$unicode_character_after."<br/>";
            }
          } // End else/if of property price indications
        } // End else/if of applying the discount immediately

        // Do something special for 'special' percentages when print_discount_icons flag is set
        if ($print_discount_icons == 1){
          $display_text = EASYSHOP_SHOP_53." ".(($discount_percentage>0)?$discount_percentage."%":$unicode_character_before.number_format($discount_price, 2, '.', '').$unicode_character_after);
            if ($discount_flag == 1 AND strstr("_5_10_20_50_", "_".$discount_percentage."_")) {
            $text .= "&nbsp;<img src='".e_PLUGIN_ABS."easyshop/images/offer_".$discount_percentage.".gif' style='height:22px' alt='$display_text' title='$display_text' />";
          } else {
            $text .= "&nbsp;<img src='".e_PLUGIN_ABS."easyshop/images/special_offer.gif' style='height:22px' alt='$display_text' title='$display_text' />";
          }
        } // End if print_discount_icons

      } // End if date is valid: don't calculate the discount if the date is invalid
    } // End if user class is valid: don't calculate the discount if the user class is invalid
  } // End if when discount_id is found
return array($text,$item_price);
} // End of function include_disc

function MailOrder($unicode_character_before, $unicode_character_after, $pref_sitename, $sender_name, $sender_email, $to_name, $to_email) {
  //if(isset($_POST['email'])){
    $check= TRUE;
  	if ($check) {
    /*
  		if($_POST['from_email']){
  			if(!preg_match('/^[-!#$%&\'*+\\.\/0-9=?A-Z^_`{|}~]+@([-0-9A-Z]+\.)+([0-9A-Z]){2,4}$/i', $_POST['from_email'])){
  				$error = ORDER_88."<br />";
  				$id = $_POST['id'];
  			}
  		}
    */
  		if ($error) {
  			$message .= "<div style='text-align:center'><b>".EASYSHOP_SHOP_60." ".$error."</b></div>";
  		} else {
        $time_stamp = date('r', time());
  			$header  = "Return-Path: ".$sender_name." <".$sender_email.">\n";
  			$header .= "From: ".$sender_name." <".$sender_email.">\n";
  			$header .= "Reply-To: ".$sender_name." <".$sender_email.">\n";
  			$header .= "X-Sender: ".$sender_name." <".$sender_email.">\n";
  			$header .= "X-Mailer: PHP\n";
  			$header .= "X-MimeOLE: Produced by e107 EasyShop plugin on ".$pref_sitename."\n";
  			$header .= "X-Priority: 3\n";
  			$header .= "MIME-Version: 1.0\n";
  			$header .= "Content-Type: text/html; charset=iso-8859-1\n";
  			$header .= "Content-transfer-encoding: 8bit\nDate: ".$time_stamp."\n";

  			$address = $to_name." <".$to_email.">"; // Provide multiple To: addresses separated with comma
  			$pre_subject = ((isset($pref_sitename))?"[":"");
  			$post_subject = ((isset($pref_sitename))?"]":"");
  			$subject = $pre_subject.$pref_sitename.$post_subject." ".EASYSHOP_SHOP_62;

  			$message = EASYSHOP_SHOP_58."&nbsp;".$time_stamp."&nbsp;".EASYSHOP_SHOP_59."<br />
            				<div style='text-align:center;'>
                  	<table border='1' cellspacing='1'>
                    <tr>
                    <td class='tbox'>".EASYSHOP_SHOP_21."</td>
                    <td class='tbox'>".EASYSHOP_SHOP_22."</td>
                    <td class='tbox'>".EASYSHOP_SHOP_23."</td>
                    <td class='tbox'>".EASYSHOP_SHOP_24."</td>
                    <td class='tbox'>".EASYSHOP_SHOP_25."</td>
                    <td class='tbox'>".EASYSHOP_SHOP_26."</td>
                    <td class='tbox'>".EASYSHOP_SHOP_27."</td>
                    </tr>";

        // Fill the message with products from the basket
        $count_items = count($_SESSION['shopping_cart']); // Count number of different products in basket
        $sum_quantity = $_SESSION['sc_total']['items'];       // Display cached sum of total quantity of items in basket
        $sum_shipping = $_SESSION['sc_total']['shipping'];    // Display cached sum of shipping costs for 1st item
        $sum_shipping2 = $_SESSION['sc_total']['shipping2'];  // Display cached sum of shipping costs for additional items (>1)
        $sum_handling = $_SESSION['sc_total']['handling'];    // Display cached sum of handling costs
        $sum_shipping_handling = number_format(($sum_shipping + $sum_shipping2 + $sum_handling), 2, '.', ''); // Calculate total handling and shipping price
        $sum_price = number_format(($_SESSION['sc_total']['sum'] + $sum_shipping_handling), 2, '.', ''); // Display cached sum of total price of items in basket + shipping + handling costs

        $array = $_SESSION['shopping_cart'];
        // PayPal requires to pass multiple products in a sequence starting at 1; we do as well in the mail
        $cart_count = 1;
        // For each product in the shopping cart array write PayPal details
        foreach($array as $id => $item) {
          $display_sku_number = $item['sku_number'];
          if ($item['sku_number'] == "") {
            $display_sku_number = "&nbsp;"; // Force a space in the cell for proper border display
          }
          $message .= "
                      <tr>
                      <td class='tbox'>".$display_sku_number."</td>
                      <td class='tbox'>".$item['item_name']."</td>
                      <td class='tbox'>".$unicode_character_before.$item['item_price'].$unicode_character_after."</td>
                      <td class='tbox'>".$item['quantity']."</td>
                      <td class='tbox'>".$unicode_character_before.$item['shipping'].$unicode_character_after."</td>
                      <td class='tbox'>".$unicode_character_before.$item['shipping2'].$unicode_character_after."</td>
                      <td class='tbox'>".$unicode_character_before.$item['handling'].$unicode_character_after."</td>
                      </tr>";
          $cart_count++;
        }
        $message .= "
                    </table>
                    </div>
                    <div style='text-align:left;'>
                    <br />".EASYSHOP_SHOP_16." ".$sum_quantity."
                    <br />".EASYSHOP_SHOP_18." ".$unicode_character_before.$sum_price.$unicode_character_after."
                    ";
                    if ($sum_shipping_handling > 0) {
                      $message .= "<br />".EASYSHOP_SHOP_20." ".$unicode_character_before.$sum_shipping_handling.$unicode_character_after;
                    }

        $message .= "</div><br /><br /><div style='text-align:center;'>&copy; <a href='http://e107.webstartinternet.com/'>EasyShop</a></div>";

  			if(!ShopMail::easyshop_sendemail($address, $subject, $message, $header)) {
  				$message = EASYSHOP_SHOP_55;  // Order e-mail failed
  			} else {
          // Send also a copy to the shop owner
          $address = $sender_name." <".$sender_email.">";
          $message = EASYSHOP_SHOP_64." ".$to_name." (<a href'".$to_email."'>".$to_email."</a>)<br /><br />".$message; // Extra in admin mail: "Following mail has been send to"
    			if(!ShopMail::easyshop_sendemail($address, $subject, $message, $header)) {
    				$message = EASYSHOP_SHOP_63;  // Order e-mail to admin failed
    			} else {
    				$message = EASYSHOP_SHOP_56; // Order e-mail succeeded
    				$mail_result = 1;
          }
  			}
  		}
  	} else {
  		$message = EASYSHOP_SHOP_57; // Please fill in all fields correctly
  	}
  //}
  return array($message, $mail_result);
}

// === End of BODY ===
// use FOOTERF for USER PAGES and e_ADMIN.'footer.php' for admin pages
require_once(FOOTERF);
?>