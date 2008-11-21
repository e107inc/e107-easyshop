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
// Include ren_help for display_help (while showing BBcodes)
require_once(e_HANDLER."ren_help.php");

// Check to see if the current user has admin permissions for this plugin
if (!getperms("P")) {
	// No permissions set, redirect to site front page
	header("location:".e_BASE."index.php");
	exit;
}

// Get language file (assume that the English language file is always present)
$lan_file = e_PLUGIN."easyshop/languages/".e_LANGUAGE.".php";
include_lan($lan_file);

// Set the active menu option for admin_menu.php
$pageid = 'admin_menu_07';

// Creation of currencies can be skipped if there are 16 currencies
$sql = new db;
if ($sql->db_Count(DB_TABLE_SHOP_CURRENCY) < 16) {
  // Check for each PayPal currency if it is missing and add it to the currency file
  /*
  Supported PayPal currencies:
  01. AUD Australian Dollar
  02. CAD Canadian Dollar
  03. CHF Swiss Franc
  04. CZK Czech Koruna
  05. DKK Danish Krone
  06. EUR Euro
  07. GBP Pound Sterling
  08. HKD Hong Kong Dollar
  09. HUF Hungarian Forint
  10. JPY Japanese Yen
  11. NOK Norwegian Krone
  12. NZD New Zealand Dollar
  13. PLN Polish Zloty
  14. SEK Swedish Krona
  15. SGD Singapore Dollar
  16. USD U.S. Dollar
  */
	if ($sql->db_Count(DB_TABLE_SHOP_CURRENCY, "(*)", "WHERE paypal_currency_code = 'AUD'") != 1) {
	    $sql->db_Insert(DB_TABLE_SHOP_CURRENCY,
	        "0,
			'".EASYSHOP_GENPREF_30." (&#36;AU)',
			'AUD',
			'&#36;AU',
			'1',
			'2',
			'1'");
	}
	if ($sql->db_Count(DB_TABLE_SHOP_CURRENCY, "(*)", "WHERE paypal_currency_code = 'CAD'") != 1) {
	    $sql->db_Insert(DB_TABLE_SHOP_CURRENCY,
	        "0,
			'".EASYSHOP_GENPREF_18." (C&#36;)',
			'CAD',
			'C&#36;',
			'1',
			'2',
			'2'");
	}
	if ($sql->db_Count(DB_TABLE_SHOP_CURRENCY, "(*)", "WHERE paypal_currency_code = 'CHF'") != 1) {
	    $sql->db_Insert(DB_TABLE_SHOP_CURRENCY,
	        "0,
			'".EASYSHOP_GENPREF_31." (SFr.)',
			'CHF',
			'SFr.',
			'1',
			'2',
			'3'");
	}
	if ($sql->db_Count(DB_TABLE_SHOP_CURRENCY, "(*)", "WHERE paypal_currency_code = 'CZK'") != 1) {
	    $sql->db_Insert(DB_TABLE_SHOP_CURRENCY,
	        "0,
			'".EASYSHOP_GENPREF_32." (K&#10d;)',
			'CZK',
			'K&#10d;',
			'1',
			'2',
			'4'");
	}
	if ($sql->db_Count(DB_TABLE_SHOP_CURRENCY, "(*)", "WHERE paypal_currency_code = 'DKK'") != 1) {
	    $sql->db_Insert(DB_TABLE_SHOP_CURRENCY,
	        "0,
			'".EASYSHOP_GENPREF_33." (kr.)',
			'DKK',
			'Dkr.',
			'1',
			'2',
			'5'");
	}
	if ($sql->db_Count(DB_TABLE_SHOP_CURRENCY, "(*)", "WHERE paypal_currency_code = 'EUR'") != 1) {
	    $sql->db_Insert(DB_TABLE_SHOP_CURRENCY,
	        "0,
			'".EASYSHOP_GENPREF_19." (&#8364;)',
			'EUR',
			'&#8364;',
			'1',
			'2',
			'6'");
	}
	if ($sql->db_Count(DB_TABLE_SHOP_CURRENCY, "(*)", "WHERE paypal_currency_code = 'GBP'") != 1) {
	    $sql->db_Insert(DB_TABLE_SHOP_CURRENCY,
	        "0,
			'".EASYSHOP_GENPREF_20." (&#163;)',
			'GBP',
			'&#163;',
			'1',
			'2',
			'7'");
	}
	if ($sql->db_Count(DB_TABLE_SHOP_CURRENCY, "(*)", "WHERE paypal_currency_code = 'HKD'") != 1) {
	    $sql->db_Insert(DB_TABLE_SHOP_CURRENCY,
	        "0,
			'".EASYSHOP_GENPREF_34." (HK&#36;)',
			'HKD',
			'HK&#36;',
			'1',
			'2',
			'8'");
	}
	if ($sql->db_Count(DB_TABLE_SHOP_CURRENCY, "(*)", "WHERE paypal_currency_code = 'HUF'") != 1) {
	    $sql->db_Insert(DB_TABLE_SHOP_CURRENCY,
	        "0,
			'".EASYSHOP_GENPREF_35." (Ft)',
			'HUF',
			'Ft',
			'1',
			'2',
			'9'");
	}
	if ($sql->db_Count(DB_TABLE_SHOP_CURRENCY, "(*)", "WHERE paypal_currency_code = 'JPY'") != 1) {
	    $sql->db_Insert(DB_TABLE_SHOP_CURRENCY,
	        "0,
			'".EASYSHOP_GENPREF_29." (&#165;)',
			'JPY',
			'&#165;',
			'1',
			'2',
			'10'");
	}
	if ($sql->db_Count(DB_TABLE_SHOP_CURRENCY, "(*)", "WHERE paypal_currency_code = 'NOK'") != 1) {
	    $sql->db_Insert(DB_TABLE_SHOP_CURRENCY,
	        "0,
			'".EASYSHOP_GENPREF_36." (Nkr.)',
			'NOK',
			'Nkr.',
			'1',
			'2',
			'11'");
	}
	if ($sql->db_Count(DB_TABLE_SHOP_CURRENCY, "(*)", "WHERE paypal_currency_code = 'NZD'") != 1) {
	    $sql->db_Insert(DB_TABLE_SHOP_CURRENCY,
	        "0,
			'".EASYSHOP_GENPREF_37." (NZ&#36;)',
			'NZD',
			'NZ&#36;',
			'1',
			'2',
			'12'");
	}
	if ($sql->db_Count(DB_TABLE_SHOP_CURRENCY, "(*)", "WHERE paypal_currency_code = 'PLN'") != 1) {
	    $sql->db_Insert(DB_TABLE_SHOP_CURRENCY,
	        "0,
			'".EASYSHOP_GENPREF_38." (P&#142;)',
			'PLN',
			'P&#142;',
			'1',
			'2',
			'13'");
	}
	if ($sql->db_Count(DB_TABLE_SHOP_CURRENCY, "(*)", "WHERE paypal_currency_code = 'SEK'") != 1) {
	    $sql->db_Insert(DB_TABLE_SHOP_CURRENCY,
	        "0,
			'".EASYSHOP_GENPREF_39." (Skr.)',
			'SEK',
			'Skr.',
			'1',
			'2',
			'14'");
	}
	if ($sql->db_Count(DB_TABLE_SHOP_CURRENCY, "(*)", "WHERE paypal_currency_code = 'SGD'") != 1) {
	    $sql->db_Insert(DB_TABLE_SHOP_CURRENCY,
	        "0,
			'".EASYSHOP_GENPREF_40." (S&#36;)',
			'SGD',
			'S&#36;',
			'1',
			'2',
			'15'");
	}
	if ($sql->db_Count(DB_TABLE_SHOP_CURRENCY, "(*)", "WHERE paypal_currency_code = 'USD'") != 1) {
	    $sql->db_Insert(DB_TABLE_SHOP_CURRENCY,
	        "0,
			'".EASYSHOP_GENPREF_17." (&#36;)',
			'USD',
			'&#36;',
			'2',
			'2',
			'16'");
	}
}

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
    $thank_you_page_email = $row['thank_you_page_email'];
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
    $icon_width = $row['icon_width'];
    $cancel_page_title = $row['cancel_page_title'];
    $cancel_page_text = $row['cancel_page_text'];
    $enable_comments = $row['enable_comments'];
    $show_shopping_bag = $row['show_shopping_bag'];
    $print_shop_address = $row['print_shop_address'];
    $print_shop_top_bottom = $row['print_shop_top_bottom'];
    $print_discount_icons = $row['print_discount_icons'];
    $shopping_bag_color = $row['shopping_bag_color'];
    $enable_ipn = $row['enable_ipn']; // IPN addition
}

// Preferences consists of three parts: Shop info, Settings, PayPal info
// 1. Shop Contact Info
$text .= "
<form name='good' method='POST' action='admin_general_preferences_edit.php'>
<fieldset>
	<legend>
		".EASYSHOP_GENPREF_01."
	</legend>
	<table border='0' class='tborder' cellspacing='15'>
		<tr>
			<td class='tborder' style='width: 200px'>
				<span class='smalltext' style='font-weight: bold'>
					".EASYSHOP_GENPREF_02."
				</span>
			</td>
			<td class='tborder' style='width: 200px'>
				<input class='tbox' size='25' type='text' name='store_name' value='$store_name' />
			</td>
		</tr>
		<tr>
			<td class='tborder' style='width: 200px'>
				<span class='smalltext' style='font-weight: bold'>
					".EASYSHOP_GENPREF_03."
				</span>
			</td>
			<td class='tborder' style='width: 200px'>
				<input class='tbox' size='35'  type='text' name='store_address_1' value='$store_address_1' />
			</td>
		</tr>
		<tr>
			<td class='tborder' style='width: 200px'>
				<span class='smalltext' style='font-weight: bold'>
					".EASYSHOP_GENPREF_04."
				</span>
			</td>
			<td class='tborder' style='width: 200px'>
				<input class='tbox' size='35'  type='text' name='store_address_2' value='$store_address_2' />
			</td>
		</tr>
		<tr>
			<td class='tborder' style='width: 200px'>
				<span class='smalltext' style='font-weight: bold'>
					".EASYSHOP_GENPREF_05."
				</span>
			</td>
			<td class='tborder' style='width: 200px'>
				<input class='tbox' size='25'  type='text' name='store_city' value='$store_city' />
			</td>
		</tr>
		<tr>
			<td class='tborder' style='width: 200px'>
				<span class='smalltext' style='font-weight: bold'>
					".EASYSHOP_GENPREF_06."
				</span>
			</td>
			<td class='tborder' style='width: 200px'>
				<input class='tbox' size='2' maxlength='2' type='text' name='store_state' value='$store_state' />
			</td>
		</tr>
		<tr>
			<td class='tborder' style='width: 200px'>
				<span class='smalltext' style='font-weight: bold'>
					".EASYSHOP_GENPREF_07."
				</span>
			</td>
			<td class='tborder' style='width: 200px'>
				<input class='tbox' size='12' maxlength='10'  type='text' name='store_zip' value='$store_zip' />
			</td>
		</tr>
		<tr>
			<td class='tborder' style='width: 200px'>
				<span class='smalltext' style='font-weight: bold'>
					".EASYSHOP_GENPREF_08."
				</span>
			</td>
			<td class='tborder' style='width: 200px'>
				<input class='tbox' size='3' maxlength='3'  type='text' name='store_country' value='$store_country' />
			</td>
		</tr>
		<tr>
			<td class='tborder' style='width: 200px'>
				<span class='smalltext' style='font-weight: bold'>
					".EASYSHOP_GENPREF_09."
				</span>
			</td>
			<td class='tborder' style='width: 200px'>
				<input class='tbox' size='25'  type='text' name='support_email' value='$support_email' />
			</td>
		</tr>
		<tr>
			<td class='tborder' style='width: 200px' valign='top'>
				<span class='smalltext' style='font-weight: bold'>
					".EASYSHOP_GENPREF_10."
				</span>
				<br />
				".EASYSHOP_GENPREF_11."
				
			</td>
			<td class='tborder' style='width: 200px'>
				<textarea class='tbox' cols='50' rows='7' name='store_welcome_message' onselect='storeCaret(this);' onclick='storeCaret(this);' onkeyup='storeCaret(this);'>$store_welcome_message</textarea><br/>".display_help('helpa')."
			</td>
		</tr>
		<tr>
			<td class='tborder' style='width: 200px'>
				<span class='smalltext' style='font-weight: bold'>
					".EASYSHOP_GENPREF_12."
				</span>
				<br />
				".EASYSHOP_GENPREF_13."
			</td>
			<td class='tborder' style='width: 200px' valign='top'>
				<input class='tbox' size='35' type='text' name='store_image_path' value='$store_image_path' />
			</td>
		</tr>
	</table>
</fieldset>
<br />";
	
// 2. Settings
$text .= "
<fieldset>
    <legend>
      ".EASYSHOP_GENPREF_44."
    </legend>
	<table border='0' class='tborder' cellspacing='15'>
		<tr>
			<td class='tborder' style='width: 200px'>
				<span class='smalltext' style='font-weight: bold'>
					".EASYSHOP_GENPREF_45."
				</span>
				<br />
				".EASYSHOP_GENPREF_46."<br />
				".EASYSHOP_GENPREF_47."
			</td>
			<td class='tborder' style='width: 200px'>
				<select class='tbox' name='set_currency_behind'>
				<option value='0' ";
				if ($set_currency_behind == '0' or $set_currency_behind == '') {
					$text .= "selected='selected'";
				}
				$text .= ">".EASYSHOP_GENPREF_48."</option>
				<option value='1' ";
				if ($set_currency_behind == '1') {
					$text .= "selected='selected'";
				}
				$text .=
				  ">".EASYSHOP_GENPREF_49."</option>
			</td>
		</tr>
		<tr>
			<td class='tborder' style='width: 200px'>
				<span class='smalltext' style='font-weight: bold'>
					".EASYSHOP_GENPREF_50."
				</span>
				<br />
				".EASYSHOP_GENPREF_51."<br />
				".EASYSHOP_GENPREF_52."
			</td>
			<td class='tborder' style='width: 200px'>
				<input class='tbox' size='25'  type='text' name='minimum_amount' value='$minimum_amount' />
			</td>
		</tr>
		<tr>
			<td class='tborder' style='width: 200px'>
				<span class='smalltext' style='font-weight: bold'>
					".EASYSHOP_GENPREF_53."
				</span>
				<br />
				".EASYSHOP_GENPREF_54."
			</td>
			<td class='tborder' style='width: 200px'>
				<select class='tbox' name='always_show_checkout'>
				<option value='0' ";
				if ($always_show_checkout == '0' or $always_show_checkout == '') {
					$text .= "selected='selected'";
				}
				$text .=
					">".EASYSHOP_GENPREF_48."</option>
					<option value='1' ";
				if ($always_show_checkout == '1') {
					$text .= "selected='selected'";
				}
				$text .=
				">".EASYSHOP_GENPREF_49."</option>
			</td>
		</tr>
		<tr>
			<td class='tborder' style='width: 200px'>
				<span class='smalltext' style='font-weight: bold'>
					".EASYSHOP_GENPREF_55."
				</span>
				<br />
				".EASYSHOP_GENPREF_56."
			</td>
			<td class='tborder' style='width: 200px'>
				<input class='tbox' size='6'  type='text' name='page_devide_char' value='$page_devide_char' />
			</td>
		</tr>
		<tr>
			<td class='tborder' style='width: 200px'>
				<span class='smalltext' style='font-weight: bold'>
					".EASYSHOP_GENPREF_57."
				</span>
				<br />
				".EASYSHOP_GENPREF_58."<br />
				".EASYSHOP_GENPREF_59."
			</td>
			<td class='tborder' style='width: 200px'>
				<input class='tbox' size='3'  type='text' name='icon_width' value='$icon_width' />
			</td>
		</tr>
		<tr>
			<td class='tborder' style='width: 200px'>
				<span class='smalltext' style='font-weight: bold'>
					".EASYSHOP_GENPREF_60."
				</span>
			</td>
			<td class='tborder' style='width: 200px'>
				<select class='tbox' name='enable_comments'>
				<option value='0' ";
				if ($enable_comments == '0' or $enable_comments == '') {
					$text .= "selected='selected'";
				}
				$text .=
				">".EASYSHOP_GENPREF_48."</option>
				<option value='1' ";
				if ($enable_comments == '1') {
					$text .= "selected='selected'";
				}
				$text .=
				">".EASYSHOP_GENPREF_49."</option>
			</td>
		</tr>

		<tr>
			<td class='tborder' style='width: 200px'>
				<span class='smalltext' style='font-weight: bold'>
					".EASYSHOP_GENPREF_61."
				</span>
			</td>
			<td class='tborder' style='width: 200px'>
				<select class='tbox' name='show_shopping_bag'>
				<option value='0' ";
				if ($show_shopping_bag == '0' or $show_shopping_bag == '') {
					$text .= "selected='selected'";
				}
				$text .=
				">".EASYSHOP_GENPREF_48."</option>
				<option value='1' ";
				if ($show_shopping_bag == '1') {
					$text .= "selected='selected'";
				}
				$text .=
				">".EASYSHOP_GENPREF_49."</option>
			</td>
		</tr>";
		
		if ($show_shopping_bag == '1') { // Ask for bag color only if it switched on
			$text .= "
    		<tr>
    			<td class='tborder' style='width: 200px'>
    				<span class='smalltext' style='font-weight: bold'>
             ".EASYSHOP_GENPREF_66."
    				</span>
    			</td>
    			<td class='tborder' style='width: 200px'>
    				<select class='tbox' name='shopping_bag_color'>
    				<option value='0' ";
    				if ($shopping_bag_color == '0' or $shopping_bag_color == '') {
    					$text .= "selected='selected'";
    				}
    				$text .=
    				">".EASYSHOP_GENPREF_67."</option>
    				<option value='1' ";
    				if ($shopping_bag_color == '1') {
    					$text .= "selected='selected'";
    				}
    				$text .=
    				">".EASYSHOP_GENPREF_68."</option>
    			</td>
    		</tr>";
		} // End of if show graphical basket equals true

		$text .= "
		<tr>
			<td class='tborder' style='width: 200px'>
				<span class='smalltext' style='font-weight: bold'>
					".EASYSHOP_GENPREF_63."
				</span>
			</td>
			<td class='tborder' style='width: 200px'>
				<select class='tbox' name='print_shop_top_bottom'>
				<option value='0' ";
				if ($print_shop_top_bottom == '0' or $print_shop_top_bottom == '') {
					$text .= "selected='selected'";
				}
				$text .=
				">".EASYSHOP_GENPREF_64."</option>
				<option value='1' ";
				if ($print_shop_top_bottom == '1') {
					$text .= "selected='selected'";
				}
				$text .=
				">".EASYSHOP_GENPREF_65."</option>
			</td>
		</tr>

		<tr>
			<td class='tborder' style='width: 200px'>
				<span class='smalltext' style='font-weight: bold'>
					".EASYSHOP_GENPREF_62."
				</span>
			</td>
			<td class='tborder' style='width: 200px'>
				<select class='tbox' name='print_shop_address'>
				<option value='0' ";
				if ($print_shop_address == '0' or $print_shop_address == '') {
					$text .= "selected='selected'";
				}
				$text .=
				">".EASYSHOP_GENPREF_48."</option>
				<option value='1' ";
				if ($print_shop_address == '1') {
					$text .= "selected='selected'";
				}
				$text .=
				">".EASYSHOP_GENPREF_49."</option>
			</td>
		</tr>

		<tr>
			<td class='tborder' style='width: 200px'>
				<span class='smalltext' style='font-weight: bold'>
         ".EASYSHOP_GENPREF_70." <br/>(".EASYSHOP_GENPREF_71.")
				</span>
			</td>
			<td class='tborder' style='width: 200px'>
				<select class='tbox' name='print_discount_icons'>
				<option value='0' "; if($print_discount_icons == '0' or $print_discount_icons == '') {$text .= "selected='selected'";} $text .= ">".EASYSHOP_GENPREF_48."</option>
				<option value='1' "; if($print_discount_icons == '1') {$text .= "selected='selected'";} $text .= ">".EASYSHOP_GENPREF_49."</option>
			</td>
		</tr>

	</table>
</fieldset>
<br />";
  
// 3. PayPal info
$text .= "
<fieldset>
	<legend>
		".EASYSHOP_GENPREF_14."
	</legend>
	<table border='0' class='tborder' cellspacing='15'>

		<tr>
			<td class='tborder' style='width: 200px'>
				<span class='smalltext' style='font-weight: bold'>
					".EASYSHOP_GENPREF_69."
				</span>
			</td>
			<td class='tborder' style='width: 200px'>
				<select class='tbox' name='email_order'>
				<option value='0' ";
				if ($email_order == '0' or $email_order == '') {
					$text .= "selected='selected'";
				}
				$text .=
				">".EASYSHOP_GENPREF_48."</option>
				<option value='1' ";
				if ($email_order == '1') {
					$text .= "selected='selected'";
				}
				$text .=
				">".EASYSHOP_GENPREF_49."</option>
			</td>
		</tr>

		<tr>
			<td class='tborder' style='width: 200px'>
				<span class='smalltext' style='font-weight: bold'>
					".EASYSHOP_GENPREF_15."
				</span>
			</td>
			<td class='tborder' style='width: 200px'>
				<input class='tbox' size='25'  type='text' name='paypal_email' value='$paypal_email' />
			</td>
		</tr>
		<tr>
			<td class='tborder' style='width: 200px'>
				<span class='smalltext' style='font-weight: bold'>
					".EASYSHOP_GENPREF_16."
				</span>
			</td>
			<td class='tborder' style='width: 200px'>
				<select class='tbox' name='currency_id'>";
						
				$sql2 = new db;
				$sql2 -> db_Select(DB_TABLE_SHOP_CURRENCY, "*", "ORDER BY currency_order", "no-where");
				while ($row2 = $sql2->db_Fetch()) {
					if($row2['currency_active'] == '2') {
						$text .= "
						<option value='".$row2['currency_id']."' selected='selected'>".$row2['display_name']."</option>";
					} else {
						$text .= "
						<option value='".$row2['currency_id']."'>".$row2['display_name']."</option>";
					}
				}
				$text .= "
				</select>
			</td>
		</tr>
		<tr>
			<td class='tborder' style='width: 200px'>
				<span class='smalltext' style='font-weight: bold'>
					".EASYSHOP_GENPREF_21."
				</span>
			</td>
			<td class='tborder' style='width: 200px'>
				<input class='tbox' size='40'  type='text' name='thank_you_page_title' value='$thank_you_page_title' />
			</td>
		</tr>
		<tr>
			<td class='tborder' style='width: 200px' valign='top'>
				<span class='smalltext' style='font-weight: bold'>
					".EASYSHOP_GENPREF_22."
				</span>
				<br />
				".EASYSHOP_GENPREF_23."
			</td>
			<td class='tborder' style='width: 200px'>
				<textarea class='tbox' cols='50' rows='7' name='thank_you_page_text'>$thank_you_page_text</textarea>
			</td>
		</tr>
		<tr>
			<td class='tborder' style='width: 200px'>
				<span class='smalltext' style='font-weight: bold'>
					".EASYSHOP_GENPREF_41."
				</span>
			</td>
			<td class='tborder' style='width: 200px'>
				<input class='tbox' size='40'  type='text' name='cancel_page_title' value='$cancel_page_title' />
			</td>
		</tr>
		<tr>
			<td class='tborder' style='width: 200px' valign='top'>
				<span class='smalltext' style='font-weight: bold'>
					".EASYSHOP_GENPREF_42."
				</span>
				<br />
				".EASYSHOP_GENPREF_43."
			</td>
			<td class='tborder' style='width: 200px'>
				<textarea class='tbox' cols='50' rows='7' name='cancel_page_text'>$cancel_page_text</textarea>
			</td>
		</tr>
		<tr>
			<td class='tborder' style='width: 200px'>
				<span class='smalltext' style='font-weight: bold'>
					".EASYSHOP_GENPREF_24."
				</span>
				<br />
				".EASYSHOP_GENPREF_25."
			</td>
			<td class='tborder' style='width: 200px' valign='top'>
				<input class='tbox' size='25'  type='text' name='payment_page_style' value='$payment_page_style' />
			</td>
		</tr>
		<tr>
			<td class='tborder' style='width: 200px'>
				<span class='smalltext' style='font-weight: bold'>
					".EASYSHOP_GENPREF_26."
				</span>
				<br />
				".EASYSHOP_GENPREF_27."
			</td>
			<td class='tborder' style='width: 200px' valign='top'>";
				if ($sandbox == '2') {
					$text .= "
					<input class='tbox' size='25'  type='checkbox' name='sandbox' value='2' checked='checked' />";
				} else {
					$text .= "
					<input class='tbox' size='25'  type='checkbox' name='sandbox' value='2' />";
				}
			$text .= "
			</td>
		</tr>";

if ($enable_ipn == '2'){
    $optiontext = " <input class='tbox' size='25' type='checkbox' name='enable_ipn' value='2' checked='checked'></option>";
}else{
    $optiontext = " <input class='tbox' size='25' type='checkbox' name='enable_ipn' value='2' ></option>";
}

$text .= "
      <tr>
        <td class='tborder' style='width: 200px'>
        <span class='smalltext' style='font-weight: bold'>
        ".EASYSHOP_GENPREF_72."<br />
        ".EASYSHOP_GENPREF_73."<br/>
        ".EASYSHOP_GENPREF_74."<br />
        ".EASYSHOP_GENPREF_75."<br />
        ".EASYSHOP_GENPREF_76."</br />
        <br/>".EASYSHOP_GENPREF_77."
         </span></td>
        <td class='tborder' style='width: 200px' valign='top'>".$optiontext."</td>
      </tr>
";
		
	$text .= "
	</table>
</fieldset>
<br />
<center>
	<input type='hidden' name='edit_preferences' value='1'>
	<input class='button' type='submit' value='".EASYSHOP_GENPREF_28."'>
</center>
<br />
</form>";

// Render the value of $text in a table.
$title = EASYSHOP_GENPREF_00;
$ns -> tablerender($title, $text);

require_once(e_ADMIN."footer.php");
?>