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
require_once(HEADERF);

// Get language file (assume that the English language file is always present)
$lan_file = e_PLUGIN."easyshop/languages/".e_LANGUAGE.".php";
include_lan($lan_file);

// read the post from PayPal system and add 'cmd'
$req = 'cmd=_notify-validate';

include_once("includes/ipn_functions.php");

foreach ($_POST as $key => $value) {
$value = urlencode(stripslashes($value));
$req .= "&$key=$value";

}
$log = fopen("ipn.log", "a");
fwrite($log, "\n\nipn - " . gmstrftime ("%b %d %Y %H:%M:%S", time()));

      
// post back to PayPal system to validate
$header = "POST /cgi-bin/webscr HTTP/1.0\r\n";
$header .= "Host: www.sandbox.paypal.com\r\n";
$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
$header .= "Content-Length: " . strlen($req) . "\r\n\r\n";

$fp = fsockopen ('ssl://www.sandbox.paypal.com', "443", $errno, $errstr, 30);

if (!$fp) {
// HTTP ERROR
 fwrite($log, "\nFailed to open HTTP connection!\n errno:". $errno .", error string:". $errstr);
} else {
fputs ($fp, $header . $req);
fwrite($log, "\n Written POST to paypal");
while (!feof($fp)) {
    
    $res = fgets ($fp, 1024);
if (strcmp ($res, "VERIFIED") == 0) {
    
fwrite($log, "\n Paypal response VERIFIED");
    
//loop through the $_POST array and store all vars to arrays $fielddata and $itemdata.
        $sql = new db;
        $sql2 = new db;
        $fielddata = array();
        $itemdata = array();
        foreach($_POST as $key => $value){   // Arrange fields and items into seperate arrays
            $value = $tp -> toDB($value);
            if (ereg( "[0-9]{1,3}$",$key)) {        // Any item with one or more digits is an item 
                $itemdata[$key] = $value;           // not sure how handling2 will be received !!                                      
            }
            else {                           // else it's a generic field for the transaction
                $fielddata[$key] = $value;
            }
                
        }

        // check the payment_status is Completed

        if ($fielddata['payment_status'] == "Completed"){
        // check that txn_id has not been previously processed
           $needle = $fielddata['txn_id']; // assign needle to $needle for pre PHP 4.2 
           $stored_trans = transaction("all", $itemdata, $fielddata, "ES_processing"); // get all transactions (limit to 3 day window in future?) 
           
           if (!in_array($needle,$stored_trans )){
            // check that receiver_email is your Primary PayPal email
                $this_trans = transaction($fielddata['custom'],null,null, "ES_processing"); // get the specific transaction
                 
                if ($fielddata['receiver_email'] == $this_trans['receiver_email']){
                        
                    
                     // check that totals and currency used are as expected
                    if(($this_trans['mc_gross'] == $fielddata['mc_gross']) && ($this_trans['mc_currency'] == $fielddata['mc_currency'])){
                           transaction("update", $itemdata, $fielddata, "ES_processing");
                           $stock_updated = update_stock($fielddata['txn_id'], $fielddata['custom']);
                           !$stock_updated? fwrite($log, "\n stock update failed with \n session id:".$fielddata['custom']."\n \n")
                           : fwrite($log, "\n stock updated successfully \n \n");
                           // totals or currency doesn't match - user intervention required - update monitor - send admin email?
                           
                    }else{
                           
                           $fielddata['payment_status'] = "EScheck_totals_".$fielddata['payment_status'];
                           transaction("FORCE_NEW", $itemdata, $fielddata);
                           fwrite($log, "\n mc_gross doesn't match \n rxd mc_gross:".$fielddata['mc_gross']."\n \n");
                           // totals or currency doesn't match - user intervention required - update monitor - send admin email?
                    }
                               
                }
                else
                {
                    //receiver email doesn't match - could be fraudulent - update monitor - send admin email?
                    $fielddata['payment_status'] = "EScheck_rxemail_".$fielddata['payment_status'];
                    transaction("FORCE_NEW", $itemdata, $fielddata);
                    if ( $this_trans['receiver_email'] == ""){
                    fwrite($log, "\n Local Entry has already been Completed or doesn't exist \n 
                    This could be a fraudalent entry or more likely 'a double hit' on the confirm order button!!!\n
                    Customer may need a refund/Credit Card Chargeback!! \n \n"); 
                    }else{
                    fwrite($log, "\n Receiver Email mismatched \n rxd email:".$this_trans['receiver_email']."\n \n");
                    }
                }
                
                
           }
           else
           {
               // this is a duplicate txn_id - possibly fraudulent - update monitor - send admin email?
               $fielddata['payment_status'] = "EScheck_dupltxn_".$fielddata['payment_status'];
               transaction("FORCE_NEW", $itemdata, $fielddata );
               fwrite($log, "\n duplicate txn_id\n \n");
           }
            
        }
        else
        {
          // store transaction and update store monitor of incomplete transaction - send admin an email also?  
          $fielddata['payment_status'] = "EScheck_".$fielddata['payment_status'];
          if(transaction("update", $itemdata, $fielddata, "ES_processing")){
            fwrite($log, "\n payment status not 'Completed' \n status:".$fielddata['payment_status']."\n \n");
          }else{
            transaction("FORCE_NEW", $itemdata, $fielddata);
            fwrite($log, "\n payment status not 'Completed'  \n status:".$fielddata['payment_status']."\n
            LOCAL ENTRY NOT PRESENT! \n \n");
          }
          
        }
        
         // if logfile is enabled....user must make sure it's secure a future option perhaps
         //fwrite($log, "\n".(print_r($fielddata, true))."\n".(print_r($itemdata, true)));
         
}else if (strcmp ($res, "INVALID") == 0) {
// log for manual investigation
      fwrite($log, "\n Paypal response 'INVALID'\n \n"); 


  }

}
fclose ($fp);
}

fclose($log);

require_once(FOOTERF);

?>