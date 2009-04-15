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

// if e107 is not running we won't run this plugin program
if (!defined('e107_INIT')) { exit; }

// determine the plugin directory as a global variable
global $PLUGINS_DIRECTORY;

// read the database names array of this plugin from the includes/config file
@include_once("includes/config.php"); // Sometimes require_once blanked out Plugin Manager

$eplug_folder = "easyshop";
// Get language file (assume that the English language file is always present)
$lan_file = e_PLUGIN."easyshop/languages/".e_LANGUAGE.".php";
include_lan($lan_file);

$eplug_name = "EasyShop";
$eplug_version = "1.4";
$eplug_author = "nlstart";
$eplug_url = EASYSHOP_URL;
$eplug_email = "nlstart@users.sourceforge.net";
$eplug_description = EASYSHOP_DESC;
$eplug_compatible = "e107v0.7+";
$eplug_compliant = TRUE;
$eplug_readme = "readme.txt";

//$eplug_folder = "easyshop";
$eplug_menu_name = "easyshop";
$eplug_conffile = "admin_config.php";
$eplug_icon = $eplug_folder."/images/logo_32.png";
$eplug_icon_small = $eplug_folder."/images/logo_16.png";
$eplug_caption = EASYSHOP_CAPTION;

// List of preferences
// this stores a default value(s) in the preferences. 0 = Off , 1= On
// Preferences are saved with plugin folder name as prefix to make preferences unique and recognisable
$eplug_prefs = array(
		"easyshop_1" => 0
);

// List of table names -----------------------------------------------------------------------------------------------
$eplug_sql = file_get_contents(e_PLUGIN."{$eplug_folder}/{$eplug_folder}_sql.php");
preg_match_all("/CREATE TABLE (.*?)\(/i", $eplug_sql, $matches);
$eplug_table_names   = $matches[1];

// List of sql requests to create tables -----------------------------------------------------------------------------
// Apply create instructions for every table you defined in locator_sql.php --------------------------------------
// MPREFIX must be used because database prefix can be customized instead of default e107_
$eplug_tables = explode(";", str_replace("CREATE TABLE ", "CREATE TABLE ".MPREFIX, $eplug_sql));
for ($i=0; $i<count($eplug_tables); $i++) {
   $eplug_tables[$i] .= ";";
}
array_pop($eplug_tables); // Get rid of last (empty) entry

// Add pre-defined Shop Preferences into the plugin table array
array_push($eplug_tables,
"INSERT INTO ".MPREFIX."easyshop_preferences (`store_id`, `store_name`, `support_email`, `store_address_1`, `store_address_2`, `store_city`, `store_state`, `store_zip`, `store_country`, `store_welcome_message`, `store_info`, `store_image_path`, `num_category_columns`, `categories_per_page`, `num_item_columns`, `items_per_page`, `paypal_email`, `popup_window_height`, `popup_window_width`, `cart_background_color`, `thank_you_page_title`, `thank_you_page_text`, `thank_you_page_email`, `payment_page_style`, `payment_page_image`, `add_to_cart_button`, `view_cart_button`, `sandbox`, `set_currency_behind`, `minimum_amount`, `always_show_checkout`, `email_order`, `product_sorting`, `page_devide_char`, `icon_width`, `cancel_page_title`, `cancel_page_text`, `enable_comments`, `show_shopping_bag`, `print_shop_address`, `print_shop_top_bottom`, `print_discount_icons`, `shopping_bag_color`, `enable_ipn`) VALUES
(1, 'My EasyShop', 'support@yourdomain.com', '1 Some St.', 'Unit 3', 'Some Town', 'OR', '01234', 'USA', 'Thank you for visiting our shop online. We have many products on sale at the moment, make sure you check them out.<br /><br />If you have any questions about our products, please feel free to e-mail us.', '', 'images/', 3, 25, 3, 25, 'someone@somewhere.com', '', '', '', 'Thank you for shopping with us', 'Your transaction has been completed, and a receipt for your purchase has been e-mailed to you.', '', 'custom_payment_page', '', '', '', 1, '0', 0, '0', '0', '', '', 0, 'Sorry', 'Your transaction failed or was canceled. Please inform the webmaster of this website in case you tried to purchase products from our shop.', '0', '0', '0', '0', '0', '0', 0, 0);"
);

// Create a link in main menu (yes=TRUE, no=FALSE)
$eplug_link = TRUE;
$eplug_link_name = EASYSHOP_LINKNAME;
$eplug_link_url = $PLUGINS_DIRECTORY.$eplug_folder."/easyshop.php";
$eplug_done = EASYSHOP_DONE1." ".$eplug_name." v".$eplug_version." ".EASYSHOP_DONE2;

// Upgrading
$upgrade_add_prefs = "";
$upgrade_remove_prefs = "";
$upgrade_alter_tables = array(
"ALTER TABLE ".MPREFIX."easyshop_preferences CHANGE minimum_amount minimum_amount int(11) NOT NULL default '0';",
"ALTER TABLE ".MPREFIX."easyshop_item_categories CHANGE category_class category_class int(11) NOT NULL;",
"ALTER TABLE ".MPREFIX."easyshop_discount CHANGE discount_class discount_class int(11) NOT NULL;",
"ALTER TABLE ".MPREFIX."easyshop_discount CHANGE discount_flag discount_flag int(11) NOT NULL;",
"ALTER TABLE ".MPREFIX."easyshop_discount CHANGE discount_price discount_price FLOAT NOT NULL;",
"ALTER TABLE ".MPREFIX."easyshop_discount CHANGE discount_percentage discount_percentage FLOAT NOT NULL;",
"ALTER TABLE ".MPREFIX."easyshop_discount CHANGE discount_valid_from discount_valid_from int(11) NOT NULL;",
"ALTER TABLE ".MPREFIX."easyshop_discount CHANGE discount_valid_till discount_valid_till int(11) NOT NULL;",
"ALTER TABLE ".MPREFIX."easyshop_items CHANGE category_id category_id int(11) NOT NULL;",
"ALTER TABLE ".MPREFIX."easyshop_items CHANGE item_price item_price FLOAT NOT NULL;",
"ALTER TABLE ".MPREFIX."easyshop_items CHANGE shipping_first_item shipping_first_item FLOAT NOT NULL;",
"ALTER TABLE ".MPREFIX."easyshop_items CHANGE shipping_additional_item shipping_additional_item FLOAT NOT NULL;",
"ALTER TABLE ".MPREFIX."easyshop_items CHANGE handling_override handling_override FLOAT NOT NULL;",
"ALTER TABLE ".MPREFIX."easyshop_items CHANGE prod_prop_1_id prod_prop_1_id int(11) NOT NULL;",
"ALTER TABLE ".MPREFIX."easyshop_items CHANGE prod_prop_2_id prod_prop_2_id int(11) NOT NULL;",
"ALTER TABLE ".MPREFIX."easyshop_items CHANGE prod_prop_3_id prod_prop_3_id int(11) NOT NULL;",
"ALTER TABLE ".MPREFIX."easyshop_items CHANGE prod_prop_4_id prod_prop_4_id int(11) NOT NULL;",
"ALTER TABLE ".MPREFIX."easyshop_items CHANGE prod_prop_5_id prod_prop_5_id int(11) NOT NULL;",
"ALTER TABLE ".MPREFIX."easyshop_items CHANGE prod_discount_id prod_discount_id int(11) NOT NULL;",
"ALTER TABLE ".MPREFIX."easyshop_preferences ADD enable_ipn int(11) NOT NULL default '1' AFTER shopping_bag_color;",
"ALTER TABLE ".MPREFIX."easyshop_preferences ADD enable_number_input varchar(1) NOT NULL default '' AFTER enable_ipn;",
"ALTER TABLE ".MPREFIX."easyshop_preferences ADD print_special_instr varchar(1) NOT NULL default '' AFTER enable_number_input;",
"ALTER TABLE ".MPREFIX."easyshop_preferences ADD email_info_level varchar(1) NOT NULL default '' AFTER print_special_instr;",
"ALTER TABLE ".MPREFIX."easyshop_items ADD item_instock int(11) NOT NULL default '0' AFTER prod_discount_id;",
"ALTER TABLE ".MPREFIX."easyshop_items ADD item_track_stock int(11) NOT NULL default '0' AFTER item_instock;",
"ALTER TABLE ".MPREFIX."easyshop_items ADD download_product int(11) NOT NULL default '0' AFTER item_track_stock;",
"ALTER TABLE ".MPREFIX."easyshop_items ADD download_filename varchar(200) NOT NULL default '' AFTER download_product;",
"CREATE TABLE ".MPREFIX."easyshop_ipn_orders (
 ppfield_id int(127) NOT NULL auto_increment,
 payment_type varchar(20) default NULL,
 payment_date varchar(30) default NULL,
 payment_status varchar(50) default NULL,
 pending_reason varchar(20) default NULL,
 address_status varchar(20) default NULL,
 payer_status varchar(20) default NULL,
 first_name varchar(64) default NULL,
 last_name varchar(64) default NULL,
 payer_email varchar(127) default NULL,
 payer_id varchar(13) default NULL,
 address_name varchar(128) default NULL,
 address_country varchar(64) default NULL,
 address_country_code varchar(3) default NULL,
 address_zip varchar(20) default NULL,
 address_state varchar(40) default NULL,
 address_city varchar(40) default NULL,
 address_street varchar(200) default NULL,
 business varchar(127) default NULL,
 receiver_email varchar(127) default NULL,
 receiver_id varchar(13) default NULL,
 residence_country varchar(2) default NULL,
 shipping varchar(10) default NULL,
 tax varchar(10) default NULL,
 mc_currency varchar(10) default NULL,
 mc_fee varchar(10) default NULL,
 mc_gross varchar(10) default NULL,
 txn_type varchar(10) default NULL,
 txn_id varchar(18) default NULL,
 parent_txn_id varchar(18) default NULL,
 notify_version varchar(10) default NULL,
 auction_buyer_id varchar(64) default NULL,
 auction_closing_date varchar(30) default NULL,
 for_auction varchar(2) default NULL,
 reason_code varchar(4) default NULL,
 custom varchar(255) default NULL,
 invoice varchar(127) default NULL,
 verify_sign varchar(255) default NULL,
 num_cart_items varchar(10) default NULL,
 charset varchar(10) default NULL,
 mc_shipping varchar(10) default NULL,
 mc_handling varchar(10) default NULL,
 test_ipn varchar(2) default NULL,
 payment_gross varchar(10) default NULL,
 phpsessionid varchar(127) default NULL,
 phptimestamp varchar(40) default NULL,
 all_items TEXT,
 PRIMARY KEY (ppfield_id),
 UNIQUE KEY invoice (invoice)
 )TYPE=MyISAM;"
);
// Remove redundant program easyshop_smtp.php
unlink("easyshop_smtp.php");


$eplug_upgrade_done = EASYSHOP_DONE3." ".$eplug_name." v".$eplug_version.".";
?>