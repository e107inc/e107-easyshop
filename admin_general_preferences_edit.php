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

require_once("../../class2.php");
require_once(e_HANDLER."userclass_class.php");
require_once(e_ADMIN."auth.php");

if(!getperms("P")){ header("location:".e_BASE."index.php"); }

// Set the active menu option for admin_menu.php
$pageid = 'admin_menu_03';

if ($_POST['edit_preferences'] == '1') {
    // Add a trailing slash to the path in case there is none
    if (substr($_POST['store_image_path'],-1) != "/") {
      $_POST['store_image_path'] = $_POST['store_image_path']."/";
    }
    
    // Ensure that print_special_instr is 'Off' when email_order is 'Off'
    if ($_POST['email_order'] <> '1') {
      $_POST['print_special_instr'] = '0';
    }
    
    // Count of preference record with store_id 1
    $pref_records = $sql->db_Count(DB_TABLE_SHOP_PREFERENCES, "(*)", "WHERE store_id=1");

    // Update if record 1 is available
    if ($pref_records == 1) {
      // Change Shop Preferences
      $sql->db_Update(DB_TABLE_SHOP_PREFERENCES,
      "store_name='".$tp->toDB($_POST['store_name'])."',
    	support_email='".$tp->toDB($_POST['support_email'])."',
    	store_address_1='".$tp->toDB($_POST['store_address_1'])."',
    	store_address_2='".$tp->toDB($_POST['store_address_2'])."',
    	store_city='".$tp->toDB($_POST['store_city'])."',
    	store_state='".$tp->toDB($_POST['store_state'])."',
    	store_zip='".$tp->toDB($_POST['store_zip'])."',
    	store_country='".$tp->toDB($_POST['store_country'])."',
    	store_welcome_message='".$tp->toDB($_POST['store_welcome_message'])."',
    	store_info='".$tp->toDB($_POST['store_info'])."',
    	store_image_path='".$tp->toDB($_POST['store_image_path'])."',
    	paypal_email='".$tp->toDB($_POST['paypal_email'])."',
    	popup_window_height='".$tp->toDB($_POST['popup_window_height'])."',
    	popup_window_width='".$tp->toDB($_POST['popup_window_width'])."',
    	cart_background_color='".$tp->toDB($_POST['cart_background_color'])."',
    	thank_you_page_title='".$tp->toDB($_POST['thank_you_page_title'])."',
    	thank_you_page_text='".$tp->toDB($_POST['thank_you_page_text'])."',
    	thank_you_page_email='".$tp->toDB($_POST['thank_you_page_email'])."',
    	payment_page_style='".$tp->toDB($_POST['payment_page_style'])."',
    	payment_page_image='".$tp->toDB($_POST['payment_page_image'])."',
    	sandbox=1,
      set_currency_behind='".$tp->toDB($_POST['set_currency_behind'])."',
      minimum_amount='".intval($tp->toDB($_POST['minimum_amount']))."',
      always_show_checkout='".$tp->toDB($_POST['always_show_checkout'])."',
      email_order='".$tp->toDB($_POST['email_order'])."',
      product_sorting='".$tp->toDB($_POST['product_sorting'])."',
      page_devide_char='".$tp->toDB($_POST['page_devide_char'])."',
      icon_width='".intval($tp->toDB($_POST['icon_width']))."',
    	cancel_page_title='".$tp->toDB($_POST['cancel_page_title'])."',
    	cancel_page_text='".$tp->toDB($_POST['cancel_page_text'])."',
    	enable_comments='".$tp->toDB($_POST['enable_comments'])."',
    	show_shopping_bag='".$tp->toDB($_POST['show_shopping_bag'])."',
      print_shop_address = '".$tp->toDB($_POST['print_shop_address'])."',
      print_shop_top_bottom = '".$tp->toDB($_POST['print_shop_top_bottom'])."',
      print_discount_icons = '".$tp->toDB($_POST['print_discount_icons'])."',
      shopping_bag_color = '".$tp->toDB(intval($_POST['shopping_bag_color']))."',
      enable_ipn = '".$tp->toDB(intval($_POST['enable_ipn']))."',
      enable_number_input = '".$tp->toDB(intval($_POST['enable_number_input']))."',
      print_special_instr = '".$tp->toDB(intval($_POST['print_special_instr']))."'
    	WHERE
    	store_id=1");
      if (isset($_POST['sandbox'])) {
  	    if ($_POST['sandbox'] == '2') {
  		    $sql->db_Update(DB_TABLE_SHOP_PREFERENCES, "sandbox='2' WHERE store_id=1");
  	    }
      }
      $sql->db_Update(DB_TABLE_SHOP_CURRENCY, "currency_active='1'");
      $sql->db_Update(DB_TABLE_SHOP_CURRENCY, "currency_active='2' WHERE currency_id=".$tp->toDB($_POST['currency_id']));
    } else {
      // Insert record 1; for some 1.21 users the predefined record in easyshop_preferences was not created on install
      $arg= "ALTER TABLE #easyshop_preferences AUTO_INCREMENT = 1";
      // Autoincrement will make this record number 1... //bugfix of 1.3 where I tried to fill in value '1' hardcoded, which MySQL doesn't like
      $sql->db_Select_gen($arg,false);
      $sql -> db_Insert(DB_TABLE_SHOP_PREFERENCES,
      "",
      $tp->toDB($_POST['store_name']),
    	$tp->toDB($_POST['support_email']),
    	$tp->toDB($_POST['store_address_1']),
    	$tp->toDB($_POST['store_address_2']),
    	$tp->toDB($_POST['store_city']),
    	$tp->toDB($_POST['store_state']),
    	$tp->toDB($_POST['store_zip']),
    	$tp->toDB($_POST['store_country']),
    	$tp->toDB($_POST['store_welcome_message']),
    	$tp->toDB($_POST['store_info']),
    	$tp->toDB($_POST['store_image_path']),
    	3,
    	25,
    	3,
    	25,
    	$tp->toDB($_POST['paypal_email']),
    	$tp->toDB($_POST['popup_window_height']),
    	$tp->toDB($_POST['popup_window_width']),
    	$tp->toDB($_POST['cart_background_color']),
    	$tp->toDB($_POST['thank_you_page_title']),
    	$tp->toDB($_POST['thank_you_page_text']),
    	$tp->toDB($_POST['thank_you_page_email']),
    	$tp->toDB($_POST['payment_page_style']),
    	$tp->toDB($_POST['payment_page_image']),
    	"",
    	"",
    	1,
      $tp->toDB($_POST['set_currency_behind']),
      $tp->toDB(intval($_POST['minimum_amount'])),
      $tp->toDB($_POST['always_show_checkout']),
      $tp->toDB($_POST['email_order']),
      $tp->toDB($_POST['product_sorting']),
      $tp->toDB($_POST['page_devide_char']),
      $tp->toDB(intval($_POST['icon_width'])),
    	$tp->toDB($_POST['cancel_page_title']),
    	$tp->toDB($_POST['cancel_page_text']),
    	$tp->toDB($_POST['enable_comments']),
    	$tp->toDB($_POST['show_shopping_bag']),
    	$tp->toDB($_POST['print_shop_address']),
    	$tp->toDB($_POST['print_shop_top_bottom']),
    	$tp->toDB($_POST['print_discount_icons']),
    	$tp->toDB(intval($_POST['shopping_bag_color'])),
    	$tp->toDB(intval($_POST['enable_ipn'])),
    	$tp->toDB(intval($_POST['enable_number_input'])),
    	$tp->toDB(intval($_POST['print_special_instr']))
      );
    }
    header("Location: admin_general_preferences.php");
    exit;
}
require_once(e_ADMIN."footer.php");
?>