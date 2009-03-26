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

class ShopMail
{
  // DEPRECIATED!
  function easyshop_sendemail_old($send_to, $subject, $message, $headers2){
  	global $pref;
  	$headers .= $headers2;
  	if ($pref['smtp_enable']) {
      // Send by SMTP
  		require_once(e_PLUGIN."easyshop/easyshop_smtp.php");
  		if (smtpmail($send_to, $subject, $message, $headers)) {
  			return TRUE;
  		} else {
  			return FALSE;
  		}
  	} else {
      // Send by PHP mail
  		$headers .= "Return-Path: <".$pref['siteadminemail'].">\n";
  		if(@mail($send_to, $subject, $message, $headers)){
  			return TRUE;
  		}else{
  			return FALSE;
  		}
  	}
  }
  // END OF DEPRECIATED FUNCTION
  
  function easyshop_sendemail($send_to, $subject, $message, $headers2, $attachment_name) {
    $domain_name = General::parseUrl(e_SELF); // Parse the current url
    $domain_name = $domain_name[host]; // Retrieve the host name from the parsed array
    require_once(e_HANDLER.'mail.php');
    // $bcc_mail = "yourmailaccount@yourdomain.tld";
    // if (!sendemail($send_to, $subject, $message, $to_name, $send_from="no-reply@".$domain_name, $from_name="EasyShop", $attachments="$attachment_name", $Cc="", $Bcc="$bcc_mail")) {
    if (!sendemail($send_to, $subject, $message, $to_name, "no-reply@".$domain_name, "EasyShop", $attachment_name, "", $bcc_mail)) {
  			return FALSE;
    }	else { // E-mail was send succesfully
  			return TRUE;
    }
  }

  function easyshop_senddownloads($array, $to_email)
  {
    $address = $to_email;
    // Loop throught the basket to detect dowloadable products
    foreach($array as $id => $item) {
      $sql = new db;
      $sql -> db_Select(DB_TABLE_SHOP_ITEMS, "*", "item_id=$item[db_id]");
      if ($row = $sql-> db_Fetch()){
        $download_product  = $row['download_product'];
        $download_filename = $row['download_filename'];
      }
      // Check if this is a downloadable product
      if ($download_product == 2 && strlen($download_filename) > 0) {
        $scrambled_name = intval($item[db_id]).$download_filename;
        $attachment_name_scrambled = "downloads/".MD5($scrambled_name);
        $attachment_name = "downloads/".$download_filename;
        // Create temporary original file to download with unscrambled name
        if (!copy($attachment_name_scrambled, $attachment_name)) {
            $message = EASYSHOP_CLASS_02." $attachment_name_scrambled...\n";
        }
        $subject = EASYSHOP_CLASS_03." ".$item[item_name]." ".date("Y-m-d");
        $message = EASYSHOP_CLASS_04.": ".$download_filename."\n";
        // $message .= "Download filename scrambled: ".$attachment_name_scrambled."\n"; // debug info
        // $message .= "Download filename: ".$attachment_name; // debug info
  			if(!ShopMail::easyshop_sendemail($address, $subject, $message, $header, $attachment_name)) {
  				$message = EASYSHOP_CLASS_05;  // Sending downloadable product failed
  			}
  			// Delete the temporary download file
  			unlink($attachment_name);
      }
    }
  }

}

class Security
{
  function get_session_id()
  {
    static $session_id;
    if ( $session_id == "" ) // 1.31 fix: setting static already sets the variable; thanks KVN
    {
      $session_id = session_id();
    }
    return $session_id;
  }
}

class General
{
  function multiple_paging($total_pages,$items_per_page,$action,$action_id,$page_id,$page_devider)
  // Parameters: $total_pages = the total pages that must be paginated
  // $items_per_page = the number of items represented per page
  // $action = action from url, e.g. catpage or prodpage
  // $action_id = action_id from url
  // $page_devider is the page devide character
  {
    if ($page_id <> "" or $page_id > 0 or $page_id == null) {
     $f_action_id = $page_id; // For prodpage the $page_id is the page indicator
    } else {
     $f_action_id = $action_id; // For catpage the $action_id is the page indicator
    }
    $last_page = intval(($total_pages + $items_per_page - 1) / $items_per_page); // Rounded last page number
    if ($last_page > 1 ) { // Suppress page indication if there is only one page
      $page_count = 1;
      if ($f_action_id == "" or $f_action_id < 1 or $f_action_id == null) {
        $f_action_id = 1; // Set initial page if no page parameter or illegal parameter is given
      }
      while ($page_count <= $last_page) { // For each page counter display a page
        if ($page_count == $f_action_id) { // If it is the page itself, no link
          $page_text .= " ".EASYSHOP_SHOP_05." ".$page_count." ".$page_devider;
        } else { // This is a different page than the current one, provide a link
          //$offset = $items_per_page * ($page_count - 1);
          if ($action == "catpage" or $action == "allcat") {
          $page_text .= " <a href='".e_SELF."?catpage.".$page_count."'>".EASYSHOP_SHOP_05." ".$page_count."</a> ".$page_devider;
          }
          if ($action == "cat" or $action == "prodpage") {
          $page_text .= " <a href='".e_SELF."?prodpage.".$action_id.".".$page_count."'>".EASYSHOP_SHOP_05." ".$page_count."</a> ".$page_devider;
          }
          if ($action == "blanks") {
          $page_text .= " <a href='".e_SELF."?blanks.".$page_count."'>".EASYSHOP_SHOP_05." ".$page_count."</a> ".$page_devider;
          }
          if ($action == "" or $action == "mcatpage") {
          $page_text .= " <a href='".e_SELF."?mcatpage.".$page_count."'>".EASYSHOP_SHOP_05." ".$page_count."</a> ".$page_devider;
          }
        }
        // Some debug info
        //$page_text .= " lastpage: $last_page, items per page: $items_per_page, page_count: $page_count, total_pages: $total_pages <br/> ";
        //$page_text .= " f_action_id: $f_action_id page_id: $page_id <br/> ";
        $page_count++;
      }
      $page_text = substr($page_text, 0, -(strlen($page_devider))); // Remove length of last divider character from page string
    }
    return $page_text;
  }
  
  function determine_offset($f_action,$f_action_id,$f_items_per_page)
  // Parameters: $action = action from url, e.g. catpage or prodpage
  // $action_id = action_id from url
  // $items_per_page = the number of items represented per page
  {
    if ($f_action == null ) {
      $f_offset = 0;
    } else {
        if ($f_action_id == null or $f_action_id <= 0 or $f_action_id == "") {
          $f_offset = 0;
      } else {
          $f_offset = $f_items_per_page * ($f_action_id - 1);
      }
    }
    return $f_offset;
  }
  
  function validateDecimal($f_value) {
  // Parameter: $f_value = value to be checked on maximum of 2 decimals
    if (!ereg("^[+-]?[0-9]*\.?[0-9]{0,2}$", $f_value)) {
    // Not a decimal;
    return false;
    }
    return true;
  }

  function getCommentTotal($pluginid, $id) {
     // Get number of comments for an item.
     // This method returns the number of comments for the supplied plugin/item id.
     // @param   string   a unique ID for this plugin, maximum of 10 character
     // @param   int      id of the item comments are allowed for
     // @return  int      number of comments for the supplied parameters
    global $pref, $e107cache, $tp;
    $query = "where comment_item_id='$id' AND comment_type='$pluginid'";
    $mysql = new db();
    return $mysql->db_Count("comments", "(*)", $query);
  }
  
  function getCurrentVersion(){
  $current_version = strtolower(trim(file_get_contents('http://e107.webstartinternet.com/e107_files/downloads/easyshop_ver.php')));
  return $current_version;
  }
  
  function getEasyShopDownloadDir() {
  $download_dir = "http://e107.webstartinternet.com/download.php?list.5";
  return $download_dir;
  }
  
  function Array_Clean($str,&$array) {
    // Cleans a given string from an array
    // @param   string    the string that you want to delete from an array
    // @param   array     the name of the array you want to apply the clean function
      if (in_array($str,$array)==true) {
        foreach ($array as $key=>$value) {
          if ($value==$str) unset($array[$key]);
        }
      }
  }
  
  function CreateRandomDiscountCode() {
   // Letter O (uppercase o) is not included; can be confused with zero (0)
   // The letter l (lowercase L), and the number 1 have been removed, as they can be easily mixed up
    $chars = "AaBbCcDdEeFfGgHhIiJjKkLMmNnoPpQqRrSsTtUuVvWwXxYyZz023456789,.;:#$%*=+[]";
    srand((double)microtime()*1000000);
    $i = 0;
    $random_discount_code = '' ;
    while ($i <= 5) { // Create a 6 character random code
        $num = rand() % 69;
        $tmp = substr($chars, $num, 1);
        $random_discount_code = $random_discount_code . $tmp;
        $i++;
    }
    return htmlspecialchars($random_discount_code);
  }
  
  function parseUrl($url) {
    $r  = "^(?:(?P<scheme>\w+)://)?";
    $r .= "(?:(?P<login>\w+):(?P<pass>\w+)@)?";
    $r .= "(?P<host>(?:(?P<subdomain>[-\w\.]+)\.)?" . "(?P<domain>[-\w]+\.(?P<extension>\w+)))";
    $r .= "(?::(?P<port>\d+))?";
    $r .= "(?P<path>[\w/]*/(?P<file>\w+(?:\.\w+)?)?)?";
    $r .= "(?:\?(?P<arg>[\w=&]+))?";
    $r .= "(?:#(?P<anchor>\w+))?";
    $r = "!$r!";  // Delimiters
    preg_match ( $r, $url, $out );
    return $out;
  }
}

class Shop
{
  function switch_columns($p_num_item_columns) {
  // Returns column width percentage variable $column_width
  // @param   integer    the number of item columns from the settings
		switch ($p_num_item_columns) {
			case 1:
				$column_width = '100%';
				break;
			case 2:
				$column_width = '50%';
				break;
			case 3:
				$column_width = '33%';
				break;
			case 4:
				$column_width = '25%';
				break;
			case 5:
				$column_width = '20%';
				break;
		}
		return $column_width;
  }

  function show_checkout($p_session_id, $p_special_instr_text) {
    // Parameter $p_session_id is used to check the users' current session ID to prevent XSS vulnarabilities
    // Parameter $p_special_instr_text is used to pass e-mail special instructions for seller
    if ($p_session_id != session_id()) { // Get out of here: incoming session id is not equal than current session id
     header("Location: ".e_BASE); // Redirect to the home page
     exit;
    }

    // Check query
    if(e_QUERY){
    	$tmp = explode(".", e_QUERY);
    	$action = $tmp[0];
    	unset($tmp);
    }

  	$sql2 = new db;
  	$sql2 -> db_Select(DB_TABLE_SHOP_PREFERENCES, "*", "store_id=1");
  	if ($row2 = $sql2-> db_Fetch()){
  		$sandbox               = $row2['sandbox'];
    	$paypal_email          = $row2['paypal_email'];
    	$payment_page_style    = $row2['payment_page_style'];
    	$paypal_currency_code  = $row2['paypal_currency_code'];
    	$set_currency_behind   = $row2['set_currency_behind'];
      $minimum_amount        = intval($row2['minimum_amount']);
      $always_show_checkout  = $row2['always_show_checkout'];
      $email_order           = $row2['email_order'];
      $show_shopping_bag     = $row2['show_shopping_bag'];
      $print_special_instr   = $row2['print_special_instr'];
    // IPN addition
    $enable_ipn = $row2['enable_ipn'];      
  	}

    $sql3 = new db;
  	$sql3 -> db_Select(DB_TABLE_SHOP_CURRENCY, "*", "currency_active=2");
  	if ($row3 = $sql3-> db_Fetch()){
  		$unicode_character = $row3['unicode_character'];
  		$paypal_currency_code = $row3['paypal_currency_code'];
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
    if ($email_order == 1) {
   		$actionDomain = e_SELF;
    } else {
    	if ($sandbox == 2) {
    		$actionDomain = "https://www.sandbox.paypal.com/cgi-bin/webscr";
    	} else {
    		$actionDomain = "https://www.paypal.com/cgi-bin/webscr";
    	}
    }
    // Display check out button
    
    $f_text = ""; // initialise
  if(($enable_ipn == 2) && ($_SESSION['sc_total']['items'] > 0) && $email_order <> 1){   // IPN addition if IPN_enabled - use AJAX
           $f_text .= "
            <form action='track_checkout.php' method='POST'>
            <!-- <span id='checkoutbutton'> -->
            <div>
            <input type='hidden' name='phpsessionid' value='".session_id()."'>
            <input type='hidden' name='source_url' value='".urlencode(e_SELF.(e_QUERY ? "?".e_QUERY : ""))."'>
            <input class='button' type='submit' value='".EASYSHOP_CLASS_01."'/>
            <!-- </span> -->
            </div>
            </form>";
            
  } else {
     $f_text = ""; // initialize variable
    // <form target='_blank' action='$paypalDomain/cgi-bin/webscr' method='post'> leads to XHTML incompatible caused by target
  	$f_text .= "
  	<form action='$actionDomain' method='post'>
  	<div>
  		<input type='hidden' name='cmd' value='_cart'/>
      <input type='hidden' name='upload' value='1'/>
  		<input type='hidden' name='business' value='$paypal_email'/>
      <input type='hidden' name='page_style' value='$payment_page_style'/>
      ";

    // Fill the Cart with products from the basket
    $count_items = count($_SESSION['shopping_cart']); // Count number of different products in basket
    $array = $_SESSION['shopping_cart'];
    // PayPal requires to pass multiple products in a sequence starting at 1
    $cart_count = 1;
    // Set thanks page to correct value
    $thanks_page = str_replace('easyshop.php', 'thank_you.php', e_SELF);

    // For each product in the shopping cart array write PayPal details
    foreach($array as $id => $item) {
        $f_text .= "
        <input type='hidden' name='item_name_".$cart_count."' value='".$item['item_name']."'/>
        <input type='hidden' name='item_number_".$cart_count."' value='".$item['sku_number']."'/>
        <input type='hidden' name='amount_".$cart_count."' value='".$item['item_price']."'/>
        <input type='hidden' name='quantity_".$cart_count."' value='".$item['quantity']."'/>
        <input type='hidden' name='shipping_".$cart_count."' value='".$item['shipping']."'/>
        <input type='hidden' name='shipping2_".$cart_count."' value='".$item['shipping2']."'/>
        <input type='hidden' name='handling_".$cart_count."' value='".$item['handling']."'/>
        ";
        $cart_count++;
    }

    $f_text .= "
        <input type='hidden' name='currency_code' value='$paypal_currency_code'/>
        <input type='hidden' name='no_note' value='1'/>
        <input type='hidden' name='lc' value='US'/>
        <input type='hidden' name='bn' value='PP-ShopCartBF'/>
        <input type='hidden' name='rm' value='1'/>
        <input type='hidden' name='return' value='".$thanks_page."'/>
    ";
  }

  if((!$enable_ipn == 2 || $email_order == 1) && ($_SESSION['sc_total']['items'] > 0)){ // nlstart fix: here too! :)  ### IPN addition if IPN_enabled - use AJAX
      // in case setting always show checkout button is off
      if ($always_show_checkout == 0) {
      // When there are items in the shopping cart, display 'Go to checkout' button
  			if ($_SESSION['sc_total']['items'] > 0) {
  			  // Only show 'Go to checkout' if total amount is above minimum amount
          if ($_SESSION['sc_total']['sum'] > $minimum_amount) {
            if ($email_order == 1) {
              // Only show enter special instructions if setting is 'On'
              if ($print_special_instr == 1) {
              $f_text .= "<table border='0' class='tborder' cellspacing='5'>
                      		<tr>
                      			<td class='tborder' style='width: 200px' valign='top'>
                      				<span class='smalltext' style='font-weight: bold'>
                            ".EASYSHOP_SHOP_82."
                      				</span>
                      				<br />
                            ".EASYSHOP_SHOP_83."
                      			</td>
                      			<td class='tborder' style='width: 200px'>
                      				<textarea class='tbox' cols='50' rows='2' name='special_instr_text'>$p_special_instr_text</textarea>
                      			</td>
                      		</tr>
                      		<tr>
                         </table>";
              }
              $f_text .= "<input type='hidden' name='email_order' value='1'/>";
              $f_text .= "<input class='button' type='submit' value='".EASYSHOP_SHOP_09."'>";
            }
            if (!($enable_ipn == 2)) { // Suppress standard checkout button when IPN enabled
    					$f_text .= "<input class='button' type='submit' value='".EASYSHOP_SHOP_09."'/>";
            }
            $f_text .= "
            </div>
  					</form>
  					<br />";
  				} else { // Minimum amount has not been reached; inform the shopper
  				  $f_text .= EASYSHOP_SHOP_36." : ".$unicode_character_before.number_format($minimum_amount, 2, '.', '').$unicode_character_after." <br />
            ".EASYSHOP_SHOP_37." : ".$unicode_character_before.number_format(($minimum_amount - $_SESSION['sc_total']['sum']), 2, '.', '').$unicode_character_after." </div></form><br />";
  				}
        } else { // v1.2 RC1 Fix for proper closing the form tag
          $f_text .= "</div></form><br />";
        }
      } // else { // RC6 Fix for proper closing the form tag
        // $f_text .= "</div></form><br />";
      // }
    // in case setting always display checkout button is on
    if ($always_show_checkout == 1) {
  			$f_text .= "
          <input class='button' type='submit' value='".EASYSHOP_SHOP_09."'>
        </div>
        </form>
  			<br />";
    }
  }

    // Show 'Manage your basket link'
   	if ($_SESSION['sc_total']['items'] > 0 AND $action != "edit") {
    	$f_text .= "
      <div style='text-align:center;'>
        <a href='easyshop.php?edit'>".EASYSHOP_SHOP_35."</a>";
      // Conditionally show cart icon (dependent on show shopping bag flag)
      if ($show_shopping_bag == '1') {
        $f_text .= "&nbsp;<a href='easyshop.php?edit'><img style='border:0;' src='".e_PLUGIN_ABS."easyshop/images/cart.gif' alt='".EASYSHOP_SHOP_35."'/></a>";
      }
       
    	$f_text .= "
      </div>";
    }

    /* // Some debug info
    print_r($_SESSION['shopping_cart']);
    print ("<br/>");
    print_r($_SESSION['sc_total']);
    print ("<br/>");
    print_r($_SESSION['sc_total']['shipping']);
    print ("<br/>");
    print_r($_SESSION['sc_total']['shipping2']);
    print ("<br/>");
    print_r($_SESSION['sc_total']['handling']);
    print ("<br/>");
    */
    return $f_text;
  }
  
  function show_ipn_checkout($p_session_id) {
    // Parameter $p_session_id is used to check the users' current session ID to prevent XSS vulnarabilities
    if ($p_session_id != session_id()) { // Get out of here: incoming session id is not equal than current session id
     header("Location: ".e_BASE); // Redirect to the home page
     exit;
    }

    // Check query
    if(e_QUERY){
    	$tmp = explode(".", e_QUERY);
    	$action = $tmp[0];
    	unset($tmp);
    }

  	$sql2 = new db;
  	$sql2 -> db_Select(DB_TABLE_SHOP_PREFERENCES, "*", "store_id=1");
  	while($row2 = $sql2-> db_Fetch()){
  		$sandbox = $row2['sandbox'];
    	$paypal_email = $row2['paypal_email'];
    	$payment_page_style = $row2['payment_page_style'];
    	$paypal_currency_code = $row2['paypal_currency_code'];
    	$set_currency_behind = $row2['set_currency_behind'];
      $minimum_amount = intval($row2['minimum_amount']);
      $always_show_checkout = $row2['always_show_checkout'];
      $email_order = $row2['email_order'];
  	}

    $sql3 = new db;
  	$sql3 -> db_Select(DB_TABLE_SHOP_CURRENCY, "*", "currency_active=2");
  	while($row3 = $sql3-> db_Fetch()){
  		$unicode_character = $row3['unicode_character'];
  		$paypal_currency_code = $row3['paypal_currency_code'];
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
  	if ($sandbox == 2) {
  		$paypalDomain = "https://www.sandbox.paypal.com";
  	} else {
  		$paypalDomain = "https://www.paypal.com";
  	}

    // Display check out button
    // <form target='_blank' action='$paypalDomain/cgi-bin/webscr' method='post'> leads to XHTML incompatible caused by target
  	$f_text .= "
  	<form action='$paypalDomain/cgi-bin/webscr' method='post'>
  	<div>
  		<input type='hidden' name='cmd' value='_xclick' />
      <input type='hidden' name='upload' value='1' />
  		<input type='hidden' name='business' value='$paypal_email' />
      <input type='hidden' name='page_style' value='$payment_page_style' />
      ";

    // Fill the Cart with products from the basket
    $count_items = count($_SESSION['shopping_cart']); // Count number of different products in basket
    $array = $_SESSION['shopping_cart'];
    // PayPal requires to pass multiple products in a sequence starting at 1
    $cart_count = 1;
    // Set thanks page to correct value
    $thanks_page = str_replace('easyshop.php', 'thank_you.php', e_SELF);
    $cancel_page = str_replace('easyshop.php', 'cancelled.php', e_SELF);
    $ipn_notify_page = str_replace('easyshop.php', 'ipn_notify.php', e_SELF);

    // For each product in the shopping cart array write PayPal details
    foreach($array as $id => $item) {
        $f_text .= "
        <input type='hidden' name='item_name_".$cart_count."' value='".$item['item_name']."' />
        <input type='hidden' name='item_number_".$cart_count."' value='".$item['sku_number']."' />
        <input type='hidden' name='amount_".$cart_count."' value='".$item['item_price']."' />
        <input type='hidden' name='quantity_".$cart_count."' value='".$item['quantity']."' />
        <input type='hidden' name='shipping_".$cart_count."' value='".$item['shipping']."' />
        <input type='hidden' name='shipping2_".$cart_count."' value='".$item['shipping2']."' />
        <input type='hidden' name='handling_".$cart_count."' value='".$item['handling']."' />
        ";
        $cart_count++;
    }

    $f_text .= "
        <input type='hidden' name='currency_code' value='$paypal_currency_code' />
        <input type='hidden' name='no_note' value='1' />
        <input type='hidden' name='lc' value='US' />
        <input type='hidden' name='bn' value='PP-ShopCartBF' />
        <input type='hidden' name='rm' value='1' />
        <input type='hidden' name='notify_url' value='$ipn_notify_page' />
        <input type='hidden' name='return' value='".$thanks_page."' />
        <input type='hidden' name='cancel_return' value='".$cancel_page."' />
    ";

    if (USER) { // If user is logged in we also include the user id
      $f_text .="<input type='hidden' name='custom' value='".USERID."' />";
    }

    if ($email_order == 0) {
      // in case setting always show checkout button is off
      if ($always_show_checkout == 0) {
      // When there are items in the shopping cart, display 'Go to checkout' button
  			if ($_SESSION['sc_total']['items'] > 0) {
  			  // Only show 'Go to checkout' if total amount is above minimum amount
          if ($_SESSION['sc_total']['sum'] > $minimum_amount) {
  					$f_text .= "
              <input class='button' type='submit' value='".EASYSHOP_SHOP_09."'/>
            </div>
  					</form>
  					<br />";
  				} else { // Minimum amount has not been reached; inform the shopper
  				  $f_text .= EASYSHOP_SHOP_36." : ".$unicode_character_before.number_format($minimum_amount, 2, '.', '').$unicode_character_after." <br />
            ".EASYSHOP_SHOP_37." : ".$unicode_character_before.number_format(($minimum_amount - $_SESSION['sc_total']['sum']), 2, '.', '').$unicode_character_after." <br />";
  				}
        }
      } else { // RC6 Fix for proper closing the form tag
        $f_text .= "</div></form><br />";
      }
    } else { // e-mail the order to admin
      $f_text .= "<a class='button' href='function MailOrder($array)'>".EASYSHOP_SHOP_79."</a></form><br />";
    }
    // in case setting always display checkout button is on
    //else
    if ($always_show_checkout == 1) {
  			$f_text .= "
          <input class='button' type='submit' value='".EASYSHOP_SHOP_09."'>
  			</form>
  			<br />";
    }
    // Show 'Manage your basket link'
   	if ($_SESSION['sc_total']['items'] > 0 AND $action != "edit") {
    	$f_text .= "
      <div style='text-align:center;'><a href='easyshop.php?edit'>".EASYSHOP_SHOP_35."</a></div>
    	";
    }

    /* // Some debug info
    print_r($_SESSION['shopping_cart']);
    print ("<br/>");
    print_r($_SESSION['sc_total']);
    print ("<br/>");
    print_r($_SESSION['sc_total']['shipping']);
    print ("<br/>");
    print_r($_SESSION['sc_total']['shipping2']);
    print ("<br/>");
    print_r($_SESSION['sc_total']['handling']);
    print ("<br/>");
    */
    return $f_text;
  }
}
?>