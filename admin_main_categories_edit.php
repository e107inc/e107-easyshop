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
require_once("includes/config.php");

if(!getperms("P")){ header("location:".e_BASE."index.php"); }

// Set the active menu option for admin_menu.php
$pageid = 'admin_menu_02';

function tokenizeArray($array) {
    unset($GLOBALS['tokens']);
    $delims = "~";
    $word = strtok( $array, $delims );
    while ( is_string( $word ) ) {
        if ( $word ) {
            global $tokens;
            $tokens[] = $word;
        }
        $word = strtok ( $delims );
    }
}

if ($_POST['create_main_category'] == '1') {
// Create new Product Category
    if (isset($_POST['main_category_active_status']))
    {
        $main_category_active_status = 2;
    } else
    {
        $main_category_active_status = 1;
    }
    $sql -> db_Insert(DB_TABLE_SHOP_MAIN_CATEGORIES,
        "0,
		'".$tp->toDB($_POST['main_category_name'])."',
		'".$tp->toDB($_POST['main_category_description'])."',
		'".$tp->toDB($_POST['main_category_image'])."',
		'".$tp->toDB($main_category_active_status)."',
		1") or die(mysql_error());
    header("Location: admin_main_categories.php");
    exit;

} else if ($_POST['main_category_dimensions'] == '1') {
    $sql->db_Update(DB_TABLE_SHOP_PREFERENCES,
    "categories_per_page='".$tp->toDB($_POST['categories_per_page'])."',
	num_category_columns='".$tp->toDB($_POST['num_category_columns'])."'
	WHERE
	store_id=1");
    header("Location: admin_main_categories.php");
    exit;

} else if ($_POST['change_main_order'] == '1') {
    // Change category order
    for ($x = 0; $x < count($_POST['main_category_order']); $x++) {
        tokenizeArray($_POST['main_category_order'][$x]);
        $newCategoryOrderArray[$x] = $tokens;
    }

    for ($x = 0; $x < count($newCategoryOrderArray); $x++) {
        $sql -> db_Update(DB_TABLE_SHOP_MAIN_CATEGORIES,
            "main_category_order=".$tp->toDB($newCategoryOrderArray[$x][1])."
            WHERE main_category_id=".$tp->toDB($newCategoryOrderArray[$x][0]));
    }

    // Change category active status
    $sql -> db_Update(DB_TABLE_SHOP_MAIN_CATEGORIES, "main_category_active_status=1");
    
    foreach ($_POST['main_category_active_status'] as $value) {
    	$sql -> db_Update(DB_TABLE_SHOP_MAIN_CATEGORIES, "main_category_active_status=2 WHERE main_category_id=".$tp->toDB($value));
    }

    header("Location: admin_main_categories.php");
    exit;

} else if ($_POST['edit_main_category'] == '2') {
    // Edit Product Category
    if (isset($_POST['main_category_active_status']))
    {
        $main_category_active_status = 2;
    } else
    {
        $main_category_active_status = 1;
    }
    $sql -> db_Update(DB_TABLE_SHOP_MAIN_CATEGORIES,
        "main_category_name='".$tp->toDB($_POST['main_category_name'])."',
		main_category_description='".$tp->toDB($_POST['main_category_description'])."',
		main_category_image='".$tp->toDB($_POST['main_category_image'])."',
		main_category_active_status='".$tp->toDB($main_category_active_status)."'
		WHERE main_category_id=".$tp->toDB($_POST['main_category_id']));
    header("Location: admin_main_categories.php");
    exit;

} else if ($_GET['delete_main_category'] == '1') {
  	// Verify deletion before actual delete
    $text = "
    <br /><br />
    <center>
        ".EASYSHOP_MCATEDIT_02."
        <br /><br />
        <table width='100'>
            <tr>
                <td>
                    <a href='admin_main_categories_edit.php?delete_main_category=2&main_category_id=".$_GET['main_category_id']."'>".EASYSHOP_MCATEDIT_03."</a>
                </td>
                <td>
                    <a href='admin_main_categories.php'>".EASYSHOP_MCATEDIT_04."</a>
                </td>
            </tr>
        </table>
    </center>";

    // Render the value of $text in a table.
    $title = "<b>".EASYSHOP_MCATEDIT_01."</b>";
    $ns -> tablerender($title, $text);

} else if ($_GET['delete_main_category'] == '2') {
	// Variable delete_main_category = 2 if answer equals Yes
	$MainCategoryId = $tp->toDB($_GET['main_category_id']);

    // Delete category from tables
    $sql -> db_Delete(DB_TABLE_SHOP_MAIN_CATEGORIES, "main_category_id=$MainCategoryId");
    header("Location: admin_main_categories.php");
    exit;
}

require_once(e_ADMIN."footer.php");
?>