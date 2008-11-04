<?php
/*
+------------------------------------------------------------------------------+
| EasyShop - an easy e107 web shop  | adapted by nlstart
| formerly known as
|    jbShop - by Jesse Burns aka jburns131 aka Jakle
|    Plugin Support Site: e107.webstartinternet.com
|
|    For the e107 website system visit http://e107.org
|
|    Released under the terms and conditions of the
|    GNU General Public License (http://gnu.org).
|    Code addition by KVN to support nlstart
|    Aug 2008 :- IPN API and basic Stock Tracking functions
+------------------------------------------------------------------------------+
*/

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

// Include define tables info
require_once("includes/config.php");
require_once("includes/ipn_functions.php");  

// Set the active menu option for admin_menu.php
$pageid = 'admin_menu_90';

// Put shop preferences into an array
$shoppref = shop_pref();


if ($_POST['submit'] == 'Update'){
    isset($_POST['enable_ipn']) ? $shoppref['enable_ipn'] = '2' : $shoppref['enable_ipn'] = '1';
    shop_pref($shoppref); // updates shop preferences with given array
}

if ($shoppref['enable_ipn'] == '2'){
    $optiontext = " <input type='checkbox' name='enable_ipn' value='2' checked='checked'></option>";
}else{
    $optiontext = " <input type='checkbox' name='enable_ipn' value='2' ></option>";
}



$text = "
          <br />
          <center>
          <table style='".ADMIN_WIDTH."' class='fborder'>
          <form name='ipn_form' action='".e_SELF."' method='post'>
                  <tr>
                  <td class='forumheader'>Enable Paypal IPN<br />
                  <span class='smalltext'>
                  To setup Paypal: <br/>
                  1. Login and goto My account - Profile - Instant Payment Notification Preferences <br />
                  2. ensure 'IPN' is on and place your website URL in the 'IPN' URL box <br />
                  3. Click on Save.</br />
                  <br/><b>Note:</b> Paypal IPN will only work on a public Server - it will not work on a 'Localhost'
                   </span></td>
                  <td class='forumheader'>".$optiontext."</td>
                  </tr>
                  <tr>
                  <td class='forumheader'>User Defined Return URL <br /> 
                  <span class='smalltext'>
                  This will be a future security option where the user can rename the validation file to anything they want and then provide the Return URL directly to PayPal's 'Return URL' text box</span>
                  </td>
                  <td class='forumheader'></td>
                  </tr>
                  
                  <tr>
                  
                  <td colspan =  '2'><center><input class='button' type='submit' name='submit' value='Update'></center></td>
                  
                  </tr>
         </form>
         </table>
         </center>
          
              ";   
    // Render the value of $text in a table.
    $title = "Paypal IPN Config";
    $ns -> tablerender($title, $text);
   
   require_once(e_ADMIN."footer.php");    

?>