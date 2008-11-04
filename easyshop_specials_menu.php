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
if (!defined('e107_INIT')) { exit; }

// Get language file (assume that the English language file is always present)
$lan_file = e_PLUGIN."easyshop/languages/".e_LANGUAGE.".php";
include_lan($lan_file);

// include define tables info
require_once(e_PLUGIN."easyshop/includes/config.php"); // It's important to point to the correct plugin folder!

require_once("easyshop_class.php");
$session_id = Security::get_session_id(); // Get the session id by using Singleton pattern

// Randomly pick an active product from an active product category (only pick categories that user is entitled to see)
// with an active discount that the user is entitled to see
$today = time();
$sql = new db;
$arg="SELECT *
      FROM #easyshop_items
      LEFT JOIN #easyshop_item_categories
      ON #easyshop_items.category_id = #easyshop_item_categories.category_id
      LEFT JOIN #easyshop_discount
      ON #easyshop_items.prod_discount_id = #easyshop_discount.discount_id
      WHERE category_active_status = '2' AND item_active_status = '2' AND (category_class IN (".USERCLASS_LIST.")) AND prod_discount_id > 0
            AND discount_valid_from < '".$today."'
            AND (discount_valid_till > '".$today."' OR discount_valid_till = '0')
            AND (discount_class IN (".USERCLASS_LIST."))
      ORDER BY RAND()";

$sql->db_Select_gen($arg,false);
if ($row = $sql-> db_Fetch() and ($row["item_id"] > 0)){
    $category_id = $row["category_id"];
		$item_id = $row["item_id"];
		$item_name = $row["item_name"];
		$item_description = $row["item_description"];
		$item_image = $row["item_image"];
		$item_active_status = $row["item_active_status"];
    $item_price = $row["item_price"];

    // Retrieve shop settings
    $sql -> db_Select(DB_TABLE_SHOP_PREFERENCES, "*", "store_id=1");
    if ($row = $sql-> db_Fetch()){
        $store_image_path = $row['store_image_path'];
        $set_currency_behind = $row['set_currency_behind'];
    }

    // Check admin setting to set currency behind amount
    // 0 = currency before amount (default), 1 = currency behind amount
    if ($set_currency_behind == '') {($set_currency_behind = 0);}

    // Define position of currency character
    $sql -> db_Select(DB_TABLE_SHOP_CURRENCY, "*", "currency_active=2");
    if($row = $sql-> db_Fetch()){
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

    // NOTE: image directories are always supposed to be a folder under the easyshop directory (!)
    $prodlink = e_PLUGIN."easyshop/".$store_image_path."".$item_image;
    $urllink  = e_PLUGIN."easyshop/easyshop.php?prod.$item_id"; // got rid of long urls";

    $text = "
    <table style='text-align:center;'>
      <tr>
        <td class='forumheader3' style='colspan:2; text-align:center;'>$item_name</td>
      </tr>
      <tr>
        <td class='forumheader3' style='colspan:2; text-align:center;'><a href='$urllink' title='$item_description'><img style='border-style:none;' src='$prodlink' alt='$item_description' title='$item_description'/></a></td>
      </tr>
      <tr>
        <td class='forumheader3' style='colspan:2; text-align:center;'>".EASYSHOP_PUBLICMENU_09."&nbsp;".$unicode_character_before."&nbsp;".number_format($item_price, 2, '.', '')."&nbsp;".$unicode_character_after."</td>
      </tr>
    </table>
    ";
} else {  // End of if check on fetched category_id
  // Inform about no access to any category
  $text = "
      <table style='text-align:center;'>
      <tr><td>
      ".EASYSHOP_PUBLICMENU3_10."
      </td></tr>
      </table>
      ";
}

$caption = EASYSHOP_PUBLICMENU3_01;
$ns -> tablerender($caption, $text);
?>