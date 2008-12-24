#==============================================================================+
# EasyShop v1.4 - an easy e107 shop plugin with Paypal checkout
# originally distributed as jbShop - by Jesse Burns aka Jakle
#
# Plugin Support Website: [link=http://e107.webstartinternet.com]http://e107.webstartinternet.com[/link]
#
# A plugin for the e107 Website System; visit [link=http://e107.org]http://e107.org[/link]
# For more plugins visit: [link=http://www.e107coders.org]http://www.e107coders.org[/link]
#
# Adapted by nlstart
#==============================================================================+

If you like this plugin, send me something from [link=https://www.amazon.com/gp/registry/wishlist/KA5YB4XJZYCW/]my Amazon wishlist[/link] to keep me motivated!

Get all out of the EasyShop plugin: buy the [link=http://shop.webstartinternet.com/e107_plugins/easyshop/easyshop.php?prod.1]EasyShop 1.3 Manual[/link].

Purpose of the EasyShop plugin
==============================
GOAL: Create an easy to set up web shop within e107 that integrates with PayPal checkout.
Currently only HTML PayPal Website Payments Default is supported.

Features:
- use PayPal or e-mail the order to website administrator
- predefined all 16 PayPal supported currencies
- create unlimited main categories
- create unlimited categories
- set class access per category
- create unlimited categories per main category
- create unlimited products per product category
- Category overview layout: set the number of product category columns shown
- Category overview layout: set the number of categories per page
- Product overview layout: set the number of product columns
- Product overview layout: set the number of products per page
- create unlimited product properties like sizes, colors etc
- create unlimited product discount codes with percentage/price with optional validation on class, dates and promotional codes
- price delta per product property
- optional display of currency sign before or after price
- optional display of a product (main) category with an image
- optional display of an image per product
- optional display of shop text below or under shop main display
- optional display of shopping basket image when user is shopping
- price per product
- handling cost per first product
- separate handling cost other same product
- sending costs per product
- separate sending costs other same product
- attach up to 5 properties per product (size, color etc.)
- attach 1 product discount code per product
- displays random active products in a menu as 'Featured product'
- displays a list of active categories and active products in a menu as 'Product Categories'
- caches selected products during session until user clicks checkout
- customers can maintain their basket before checkout
- checkout directly from the 'Featured product' menu, the basket or category main page
- integrated e107 search functionality
- optional integrated e107 comments functionality for logged in members
- upload of pictures through admin menu
- XHTML 1.1 compliant
- build-in security checks for safe shopping basket

The EasyShop plugin does NOT:
- bookstock calculation
- VAT handling
- contain hidden codes to promote PayPal

Prerequisites:
==============
Before actually using PayPal Shopping Cart functionality on your website, you will need the following:

REQUIRED
 * e107 core v0.7.7 (or newer) installed.
 * A PayPal Premier or Business account
 * The PayPal verified email address at which you will receive payments
 * At least one active product group defined in EasyShop
 * At least one active product with a price defined in EasyShop

OPTIONAL
 * The URL for the web page users see after a successful transaction
 * The URL for the web page users see after canceling a transaction
 * The URL of your 50 x 150 pixel logo
 * Optional product details (including product id, shipping and sales tax rates)


Installation:
=============

Important Release Candidate information
=======================================
Release Candidates (recognisable on the abbreviation RC in the download name) are meant to be prelimary distributes before the actual release. Release Candidates of plugins - use at your own risk.
All release candidates are tested on e107 v0.7.8 and v.0.7.11. Possible new e107 features might be used and therefore this module might not function correctly on earlier versions, however for e107 0.7.7 this module is expected to run okay. It is strongly advised to test this module before implementing it on a live website.

1. Fresh install:
=================
a. Unzip the files.
b. Upload the EasyShop plugin files into your 'e107_plugins' folder. Although 'Upload plugin' from the Admin section might work, uploading your plugin files by using an FTP client program is recommended.
c. When working on Linux or Unix based server set the CHMOD settings of directories to 755 and set CHMOD of all .php files to 644.
d. Login as an administrator into e107, go to Plugin Manager, install EasyShop and you can start defining the settings.

2. Upgrading
============
2a. from jbShop v1.1:
First, download a copy of EasyShop 1.2 first, perform the upgrade following the readme.txt instructions from there. After a succesful conversion, overwrite the EasyShop 1.2 files with the EasyShop 1.3 files, go to Admin Area > Plugin Manager > perform the upgrade for EasyShop.

2b. from EasyShop v1.2x:
Overwrite the EasyShop 1.2x files with the EasyShop 1.3 files, go to Admin Area > Plugin Manager > perform the upgrade for EasyShop.

2c. from EasyShop v1.3
Overwrite the EasyShop 1.3 files with the EasyShop 1.31 files, go to Admin Area > Plugin Manager > perform the upgrade for EasyShop. NOTE: this means that EasyShop 1.2x installations have to install and upgrade to 1.3 first before installing 1.31.

3. Language Support:
====================
English, Dutch, French, German, Italian, Norwegian, Persian, Polish, Portuguese (Brazilian), Russian
Note: English is included in the default installation. Additional language files can be downloaded from [link=http://e107.webstartinternet.com]http://e107.webstartinternet.com[/link].

You are encouraged to translate EasyShop into your own native language. Contact me through my [link=http://e107.webstartinternet.com/contact.php]contact page[/link] if you want to send a finished and tested translation.


Styling your EasyShop
=====================
The following style classes have been introduced to style the Main Category Name, Category Name or Product Name to your own personal preference:
1. .easyshop_main_cat_name: style the description of the main category  (introduced in 1.4)
2. .easyshop_cat_name: style the description of the category
3. .easyshop_prod_name: style the description of the product
4. .easyshop_prod_box: style the description of the left box at product details page
5. .easyshop_prod_img: style the image within the left box at product details page
6. .easyshop_nr_of_prod: style the number of products element at the category/product details page (introducted in 1.4)

If you do not specify the styles the size, color, background etc. will be as your regular style settings.
Example to add to your style.css of your theme (which will set the font size to twelve pixels for all of the above mentioned descriptions):
.easyshop_prod_name, .easyshop_cat_name, .easyshop_main_cat_name {
  font-size: 12px;
}
Example to change the border style color to white:
fieldset {
  border-color: #000;
}
Example to center your product image on the product detail page:
.easyshop_prod_img {
  margin-left: auto;
  margin-right: auto;
}


Known Bugs
==========
- If one of the three menus (easyshop, easyshop_list or easyshop_specials) is in a menu on the left they all don't work and the easyshop.php itself refers back to the Frontpage of your e107 website.
Work around: put the menus in the right menus of your theme. Cause: unknown.
- Search of comments (search_comments.php) doesn't work.


Changelog:
==========
Version 1.4 (EasyShop, XXXXXX XX, 2008)
 * Sub-goals for release 1.4:
   - code efficiency
   - add new functionality: PayPal Instant Payment Notification (IPN)
   - add new functionality: automatic product bookstock calculation (with IPN)
   - special thanks for this release go to KVN, jburns131 and Igor
 * New/Added Features:
   - admin_config.php: added button to upload images directly
   - admin_config.php: added button to upload download products directly
   - admin_general_preferences.php: Settings: new option to enable user input of number of products
   - admin_general_preferences.php: PayPal info: new option to enable PayPal IPN
   - admin_monitor.php: new lists to view IPN orders; thanks KVN
   - admin_overview.php: new program to view downloadable products
 * Altered Features:
   - easyshop.php: added style #easyshop_main_cat_name to Main Category Name
   - easyshop_ver.php: security related: outsiders can't determine anymore which EasyShop version you are running
 * Bugs Fixed:
   - admin_categories.php: link to product maintenance fixed
   - admin_main_categories.php: removed non-existing link for main categories
   - admin_general_preferences.php: removed hard coded English texts; thanks Igor
   - admin_monitor.php: removed hard coded English texts; thanks Igor
   - easyshop_class.php: removed hard coded text "Mail to admin"
   - help.php: removed hard coded English text; thanks Igor
   - English.php: new language terms
 * Minor Changes:
   - plugin.php: fixed for correct upgrade to 1.4

Version 1.32 (EasyShop, October 31, 2008)
 * Bugs Fixed:
   - easyshop.php: added security checks
   - easyshop_basket.php: added security check
 * Minor Changes:
   - plugin.php: fixed for correct upgrade to 1.32  (upgrade directly is possible for 1.2x and 1.3x users as well)
 * Notes:
   - Security release; highly recommended to install this release to protect from SQL injection exploits
   - No language terms have been changed or added; language packs of EasyShop v1.31 can still be used.

Version 1.31 (EasyShop, August, 27, 2008)
 * New/Added Features:
   - easyshop.php: added style #easyshop_main_cat_name to Main Category Name
   - easyshop.php: added style #easyshop_cat_name to Category Name
   - easyshop.php: added style #easyshop_prod_name to Product Name
   - easyshop.php: added style #easyshop_prod_box to style the Product Detail left box
   - easyshop.php: added style #easyshop_prod_img to style the Product Image within Product Detail left box
   - easyshop.php: added extra line breaks between category image and description
   - easyshop.php: added style='padding:0 10px; margin-left:10px;' to legend form element; thanks mcpeace
   - easyshop.php: added display of sku number at product details page
   - admin_general_preferences_edit.php: added check on valid number format of minimum amount
 * Altered Features:
   - none
 * Bugs Fixed:
   - admin_general_preferences.php: proper formatting of minimum order amount
   - admin_general_preferences_edit.php: insert of record #1 should be fixed properly
   - easyshop_class.php: security: fixed check on variable because static already sets the variable; thanks KVN
   - easyshop.php: deleted obsolete `-sign from line 746; thanks mcpeace
   - easyshop.php: various HTML style improvements; thanks mcpeace
   - easyshop.php: proper formatting of minimum order amount
   - easyshop.php: added missing end div tag to allcat view mode for proper rendering
   - easyshop_sql.php: table easyshop_preferences; changed field minimum_amount from INT into FLOAT
   - easyshop_basket.php: handling of properties with spaces; thanks KVN
 * Minor Changes:
   - easyshop_class.php: typo in getCurrentVersion adjusted; thanks KVN
   - admin_check_update.php: typo in getCurrentVersion adjusted
   - plugin.php: fixed for correct upgrade to 1.31

Version 1.3 (EasyShop, July, 26 2008):
 * Sub-goals for release 1.3:
   - code efficiency
   - add more flexibility: more options in preferences
   - add more flexibility: main categories
   - add more flexibility: product properties (e.g. color, size etc.)
   - add more flexibility: product price discounts (with or without voucher code)
   - security
   - XHTML 1.1 compliant
   - integrated with core e107 comments functionality
   - integrated with core e107 class functionality
   - integrated with core e107 front page
 * New/Added Features:
   - easyshop_sql: new database structure
   - plugin.php: update to new database structure
   - admin_config.php: in product overview added a link to view product in shop front page
   - admin_categories.php: enable class per category so shop owner can have class related categories
   - admin_general_preferences.php: admin setting to set currency behind amount
   - admin_general_preferences.php: admin setting to set minimum amount
   - admin_general_preferences.php: admin setting to display checkout button always
   - admin_general_preferences.php: admin setting for alternative product sorting (not active yet)
   - admin_general_preferences.php: admin setting for page dividing character
   - admin_general_preferences.php: admin setting to set size of icon width as presented in admin (main) categories and products
   - admin_general_preferences.php: PayPal info for Cancel page title and Cancel page text
   - admin_general_preferences.php: admin setting to enable the e107 comments function so visitors can comment products
   - admin_general_preferences.php: admin setting to enable background shopping bag image in easyshop_menu
   - admin_general_preferences_edit.php: trailing slash for image path added in case it is missing
   - admin_general_preferences_edit.php: for some v1.2x users the automatic creation of the default record did not work ;
     the application will create record #1 in those cases.
   - admin_properties.php: new admin program to maintain properties
   - admin_discounts.php: new admin program to maintain discounts
   - admin_main_categories.php: new admin program to maintain main categories
   - admin_main_categories_edit.php: new admin program to maintain main categories
   - admin_monitor.php: added row with out-of-stock products
   - admin_monitor.php: added row with categories without main category
   - admin_monitor.php: added row with active products with discount
   - admin_monitor.php: added row with active products with one or more properties
   - cancelled.php: new program for (future IPN) cancelled orders
   - easyshop.php: security: users current session ID is checked before displaying checkout button to prevent XSS vulnerabilities
   - easyshop.php: security: shop support e-mail address is hidden in inline javascript to protect it from e-mail harvasting
   - easyshop.php: security: checks if user belongs to the allowed class when viewing specific category or product
   - easyshop.php: when user is logged in the user id will be passed towards PayPal; the administrator will receive this in the order confirmation e-mail
   - easyshop.php: when user is logged in as admin an edit icon is presented to go directly to maintenance product
   - easyshop_menu.php: security: implemented Singleton Pattern to prevent injections
   - easyshop_specials_menu.php: new menu that shows randomly all products with a discount
   - easyshop_basket.php: security: implemented Singleton Pattern to prevent injections
   - easyshop_class.php: efficiency: new program to call some generic functions
   - easyshop_smtp.php: new program to send e-mail confirmation to site admin
   - admin_upload.php: new admin program to maintain image folder
   - search_comments.php: new program to search for EasyShop comments NOTE: not functioning yet!
   - e_frontpage.php: new program that makes EasyShop also selectable in the e107 FrontPage program
 * Altered Features:
   - admin_categories.php: when active main categories are present; select main category from list in edit/create mode
   - easyshop.php: multipaging and calculations for checkout are done from the easyshop_class
   - easyshop.php: shorter url handling
   - easyshop.php: when showing the basket the front page isn't shown any more
   - easyshop_menu.php: calculations for checkout are done from the easyshop_class
   - easyshop_menu.php: adjusted according shorter url handling
   - easyshop_list_menu.php: adjusted according shorter url handling
   - admin_check_update.php: moved some functionalities to easyshop_class.php
   - admin_categories.php: efficient way to determine drop down list of selected number of categories
 * Bugs Fixed:
   - easyshop.php: better handling minimum amount
   - admin_config.php: adding new and editing existing products can only select active categories
   - thank_you.php: reset the shopping basket after succesful paypal transaction
   - admin_monitor.php: proper active menu indication
   - easyshop_menu.php: fixed text 'price' now included from language file
   - easyshop_menu.php: removed html td tags on price line for better display of menu
 * Minor Changes:
   - HTML output of programs adjusted to XHTML 1.1 compliant (not for admin modules)
   - all admin_ programs: ensured that programs are loaded in admin theme with setting $eplug_admin = true; before calling class2

Version 1.21 (EasyShop, 12 March 2008)
 * New/Added Features:
   - None
 * Altered Features:
   - None
 * Bugs Fixed:
   - includes/config.php: changed table names to lower case
   - plugin.php: added conversion script to convert case sensitive database names to lower case database names
 * Minor Changes:
   - None

Version 1.2 (EasyShop, April 2007):
RC1: 24 April 2007, RC2: 31 May 2007, RC3: 05 June 2007, RC4: 16 October 2007, RC5: 25 October 2007, RC6: 03 January 2008, RC7: 10 March 2008 final version 1.2
Since jbShop ended at version 1.11, I wanted to continue the sequel. That's why the very first version of EasyShop starts with version number 1.2.
 * Sub-goals for release 1.2:
   - make plugin more e107 compliant
   - make plugin language independent
   - no database conversion from jbShop 1.11
 * New/Added Features:
   - EasyShop forum [link=http://e107.webstartinternet.com]http://e107.webstartinternet.com[/link]
   - EasyShop bugtracker [link=http://e107.webstartinternet.com]http://e107.webstartinternet.com[/link]
   - rewritten all code for independent language use
   - added help function for administrative menu
   - added currency code for Yen
   - added currency codes to support all PayPal currencies: AUD, CHF, CZK, DDK, HKD, HUF, NOK, NZD, PLN, SEK, SGD (RC4)
   - the 16 supported currencies are: AUD Australian Dollar, CAD Canadian Dollar, CHF Swiss Franc, CZK Czech Koruna,
     DKK Danish Krone, EUR Euro, GBP Pound Sterling, HKD Hong Kong Dollar, HUF Hungarian Forint, JPY Japanese Yen,
     NOK Norwegian Krone, NZD New Zealand Dollar, PLN Polish Zloty, SEK Swedish Krona, SGD Singapore Dollar, USD U.S. Dollar (since RC4)
   - admin_readme.php displays readme.txt properly from menu in e107 style
   - added check to display message if there are no active product categories
   - added check to display message if there are no active products within a product category
   - added easyshop_menu.php that randomly displays active products in a menu
   - easyshop_menu.php: shows link to view the shopping basket before checkout (RC4)
   - easyshop_menu.php: added checkout button from the menu (RC6)
   - easyshop_menu.php: build in variable set_currency_behind = 0/1 for displaying currency before or after amount for future admin setting (RC6)
   - easyshop_list_menu.php: new menu that shows product links sorted by category (RC7)
   - added checks on products maintenance to have prices with 2 decimals
   - added admin_monitor.php to display shop summary overview
   - added jbshop/plugin.php to provide automatic conversion/rename of database tables from jbShop to EasyShop
   - admin_categories: added display of number of products in the category overview
   - admin_categories: added use of BB code in description field (RC2)
   - admin_config: added display of total and inactive number of products in the shop inventory
   - admin_check_update.php: new program that checks for updates on the NLSTART server (RC7)
   - added functionality to directly access products from category overview (if any available)
   - jbShop performed on each click on 'add to cart' the interface was sending an add form message to PayPal
     this resulted in a new PayPal browser window/tab on each 'add to cart' click.
     EasyShop buffers and saves all 'add to cart' clicks of each session and only interfaces to PayPal by hitting the 'view cart' button.
   - easyshop.php: displays additional costs in product detail overview in case they are above zero
   - easyshop.php: display of categories in HTML format (RC2)
   - easyshop.php: display of shopping basket with e_QUERY ?edit (called from easyshop_menu.php) (RC4)
   - easyshop.php: build in variable set_currency_behind = 0/1 for displaying currency before or after amount for future admin setting (RC4)
   - easyshop.php: build in variable minimum_amount so that checkout button is only shown if total amount is above this minimum (RC4)
   - easyshop.php: build in variable always_show_checkout = 0/1, in case it is 1 the checkout button is always shown, otherwise checkout button will be shown if there is at least one product ordered (RC4)
   - easyshop.php: show checkout button directly from the shopping basket (RC6)
   - easyshop.php: show checkout button directly from category main page (RC6)
   - easyshop.php: removed redundant &amp;url= from category to product overview links (RC6)
   - easyshop_basket.php: shopping cart contains possibilty to delete a product row, reset complete cart or continue shopping (RC4)
   - easyshop_basket.php: build in possibility to add or minus the quantity of ordered products (RC4)
   - easyshop_menu.php: show checkout button directly from the featured product menu (RC6)
   - easyshop_ver.php: new file that contains the current version of easyshop (RC7)
   - admin_check_update.php: new program that checks if an update of EasyShop is available (RC7)
   - e_search.php: added integration with e107 search functionality (RC7)
   - search/search_parser.php: added integration with e107 search functionality (RC7)
   - added Dutch language support (RC4)
   - added Portuguese language support (RC4); thanks to catarina
   - added Norwegian language support (RC4); thanks to Fivestar
   - added French language support (RC5); thanks to Lolo
   - added Russian language support (RC6); thanks to Igor&amp;
   - added Spanish language support (RC7); thanks to DelTree
   - added Italian language support (RC7); thanks to DuMaZone
 * Altered Features:
   - jbShop renamed to EasyShop
   - admin_menu: use of show_admin_menu function to make it more e107 style
   - admin_menu: added admin_check_update.php to menu (RC7)
   - admin_categories: edit/delete product categories in e107 style
   - admin_categories: add/edit category is capable of setting active flag directly (RC5)
   - admin_categories_edit: add/edit category is capable of setting active flag directly (RC5)
   - admin_config: display product price in overview per category
   - admin_config: add/edit/delete products in e107 style
   - admin_config: add/edit product is capable of setting active flag directly (RC5)
   - admin_config_edit: add/edit product is capable of setting active flag directly (RC5)
   - deleted hardcoded resize functionality of categories and products
     (this might be a possible problem for converted jbShop users with e.g. different sized or large images; these users will have to change their images)
   - select category and product image by clicking on miniaturized icon
   - jbShop used 'Add to cart' and 'View cart' buttons from PayPal website which caused delay in performance of the website.
     Besides that this also was blocking full language independency.
     EasyShop renders buttons from the theme css class style 'button'
   - display of prices is made consistent in English style with 2 decimals and no thousand separator
   - easyshop.php: added name='submit' to input class='button' (RC5); thanks secretr
   - easyshop.php: changed link for 'Continue shopping' to return to previous page (instead to start of easyshop.php) (RC6)
   - easyshop.php: category paging was hardcoded and limited to 10 pages. Proper paging function created for unlimited pages (RC6)
   - easyshop.php: product paging was hardcoded and limited to 10 pages. Proper paging function created for unlimited pages (RC6)
   - plugin.php: reads list of database table from easyshop_sql.php (RC7)
 * Bugs Fixed:
   - easyshop.php: e107_plugin directory was hardcoded
   - thank_you.php: e107_plugin directory was hardcoded
   - thank_you.php: customer shopping basket wasn't cleared (RC7)
   - plugin.php: create link in link menu e107_plugin directory was hardcoded
   - plugin.php: removed update $upgrade_alter_tables for flawless upgrade (old remainder of jbShop) (RC6)
   - admin_categories, admin_config, admin_general_preferences, admin_menu, admin_monitor, admin_readme, easyshop, easyshop_basket,
     easyshop_menu, help: fixed fatal errors on language include (RC5); thanks to secretr
   - admin_config: removed several &url tags of hyperlinks as they are not needed (RC4)
   - admin_menu.php: class2.php and auth.php included
   - admin_menu.php: added check for current user admin permissions
   - admin_monitor.php: fixed short php code into proper php code (RC7)
   - easyshop_menu.php: changed & sign in URL's into &amp; for XHTML compliancy (RC6)
   - easyshop_menu.php: Prevent a PHP warning: Division by zero (RC7)
   - easyshop.php, admin_config.php: non XHTML compatible >> text string replaced by &raquo; (right angle quote)
   - easyshop.php: disabled session_cache_limiter('public') (RC5); thanks secretr
   - easyshop.php: enabled session_cache_limiter('nocache') (RC6); thanks mygoggie/secretr
   - easyshop.php: changed & sign in URL's into &amp; for XHTML compliancy (RC6)
   - easyshop.php: login from product details page was referring to paypal checkout due to missing form tag (RC6)
   - easyshop.php: another missing form tag situation solved in the checkout function (RC7)
   - header redirect admin_config_edit.php when editing products didn't jump back to category properly after updating the record
   - replaced mysql_real_escape_string to $tp->toDB when saving variables. This respects various e107 (rights) settings and also avoids XSS and other injection vulnerabilities.
   - show delete function conditionally (only when there are no products in the category) to avoid orphanized products in database
   - admin_readme.php: made readable on Unix platforms by reading lowercase file name (RC2)
   - easyshop.php: fixed wrong return message of no categories if no products were available (RC2)
   - easyshop.php: when clicking on categories while showing details the link is incorrect. Corrected previous line 661. (RC4)
   - easyshop.php: removed borders around product images (RC4)
   - easyshop.php: force a space in the cell of SKU number for proper border display when field is empty (RC6)
   - easyshop.php: fixed presentation of currency sign before and after with variable $set_currency_behind (RC6)
   - easyshop.php: on multiple category pages the actual page will was presented as link too (RC6)
   - easyshop.php: on multiple product pages the second page had always hardcoded category id #1 (RC6)
   - easyshop.php: general settings only read once instead of three times (RC6)
   - easyshop.php: applied correct currency settings while showing the basket (RC7)
   - easyshop.php: return to original page of category or product details after adding product to basket (RC7)
   - easyshop_basket.php: fixed wrong calculations with shipping and handling costs for basket plus and minus (RC7)
   - easyshop_basket.php: fixed wrong calculations with handling costs when ordering more than one product (RC7)
   - easyshop_menu.php: will function correctly when other plugins are active (RC6)
   - admin_general_preferences, admin_config and admin_categories: include handler ren_help.php (for showing help at BBcode buttons) (RC3)
 * Minor Changes:
   - restyled and updated readme.txt
   - various small bugfixes
   - used different language terminology; e.g. products instead of items and shop instead of store (some small fixes on that in RC2)
   - changed and convert to smaller logos (16/32 pixels)
   - code optimization for use of checkout button from function show_checkout (RC6)
   - code optimization for use of pagination of categories and products (RC6)
   - code optimization for use of shop address header (RC6)
   - Dutch.php: replaced special characters with correct HTML tags in Dutch language file (RC6)
   - easyshop.php: preparations for flexible page devider character from future settings (RC7)

Version 1.11 (jbShop, 01 May 2006):
 * New/Added Features:
   - None
 * Altered Features:
   - None
 * Bugs Fixed:
   - Fixed the broken 'Add to Cart' and 'View Cart' links on the 'Item Details' page
 * Minor Changes:
   - None

Version 1.1 (jbShop, 29 Apr 2006):
 * New/Added Features:
   - Added Multiple Currencies: Canadian Dollars, Euros and Pound Stirling
   - Added testing features which allow you to test transactions using the 'Paypal Sandbox'
 * Altered Features:
   - None
 * Bugs Fixed:
   - Fixed mySQL v5 compatibility issues that prevented users from entering/saving data
 * Minor Changes:
   - Fixed Display Issue: Incorrect image size for categories and items
   - Fixed other minor display issues
   - Did some minor code cleanup

Version 1.0 (jbShop, 24 April 2006):
   - Initial Release


Future roadmap
==============
* monitor the buglist on [link=http://e107.webstartinternet.com]http://e107.webstartinternet.com[/link]
* monitor what features end users want
* publish more languages support files that are handed over by the community
* Paypal IPN (Instant Payment Notification)
* product bookstock keeping (possible with IPN)


License
=======
EasyShop is distributed as free open source code released under the terms and conditions of the [link=external=http://www.gnu.org/licenses/gpl.txt]GNU General Public License[/link].