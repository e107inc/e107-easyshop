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

if ($_POST['create_category'] == '1') {
// Create new Product Category
    if (isset($_POST['category_active_status']))
    {
        $category_active_status = 2;
    } else
    {
        $category_active_status = 1;
    }
    $sql -> db_Insert(DB_TABLE_SHOP_ITEM_CATEGORIES,
        "0,
		'".$tp->toDB($_POST['category_name'])."',
		'".$tp->toDB($_POST['category_description'])."',
		'".$tp->toDB($_POST['category_image'])."',
		'".intval($tp->toDB($category_active_status))."',
		1,
    '".intval($tp->toDB($_POST['category_main_id']))."',
    '".intval($tp->toDB($_POST['category_class']))."'
    ") or die(mysql_error());
    header("Location: admin_categories.php");
    exit;

} else if ($_POST['category_dimensions'] == '1') {
    $sql->db_Update(DB_TABLE_SHOP_PREFERENCES,
    "categories_per_page='".$tp->toDB($_POST['categories_per_page'])."',
	num_category_columns='".$tp->toDB($_POST['num_category_columns'])."'
	WHERE
	store_id=1");
    header("Location: admin_categories.php");
    exit;

} else if ($_POST['change_order'] == '1') {
    // Change category order
    for ($x = 0; $x < count($_POST['category_order']); $x++) {
        tokenizeArray($_POST['category_order'][$x]);
        $newCategoryOrderArray[$x] = $tokens;
    }

    for ($x = 0; $x < count($newCategoryOrderArray); $x++) {
        $sql -> db_Update(DB_TABLE_SHOP_ITEM_CATEGORIES,
            "category_order=".$tp->toDB($newCategoryOrderArray[$x][1])."
            WHERE category_id=".$tp->toDB($newCategoryOrderArray[$x][0]));
    }

    // Change category active status
    $sql -> db_Update(DB_TABLE_SHOP_ITEM_CATEGORIES, "category_active_status=1");
    
    foreach ($_POST['category_active_status'] as $value) {
    	$sql -> db_Update(DB_TABLE_SHOP_ITEM_CATEGORIES, "category_active_status=2 WHERE category_id=".$tp->toDB($value));
    }

    header("Location: admin_categories.php");
    exit;

} else if ($_POST['edit_category'] == '2') {
    // Edit Product Category
    if (isset($_POST['category_active_status']))
    {
        $category_active_status = 2;
    } else
    {
        $category_active_status = 1;
    }

    $sql -> db_Update(DB_TABLE_SHOP_ITEM_CATEGORIES,
    "category_name='".$tp->toDB($_POST['category_name'])."',
		category_description='".$tp->toDB($_POST['category_description'])."',
		category_image='".$tp->toDB($_POST['category_image'])."',
		category_active_status='".intval($tp->toDB($category_active_status))."',
		category_main_id='".intval($tp->toDB($_POST['category_main_id']))."',
    category_class='".intval($tp->toDB($_POST['category_class']))."'
		WHERE category_id='".intval($tp->toDB($_POST['category_id']))."'");
    header("Location: admin_categories.php");
    exit;

} else if ($_GET['delete_category'] == '1') {
  	// Verify deletion before actual delete
    $text = "
    <br /><br />
    <center>
        ".EASYSHOP_CATEDIT_02."
        <br /><br />
        <table width='100'>
            <tr>
                <td>
                    <a href='admin_categories_edit.php?delete_category=2&category_id=".$_GET['category_id']."'>".EASYSHOP_CATEDIT_03."</a>
                </td>
                <td>
                    <a href='admin_categories.php'>".EASYSHOP_CATEDIT_04."</a>
                </td>
            </tr>
        </table>
    </center>";

    // Render the value of $text in a table.
    $title = "<b>".EASYSHOP_CATEDIT_01."</b>";
    $ns -> tablerender($title, $text);

} else if ($_GET['delete_category'] == '2') {
	// Variable delete_category = 2 if answer equals Yes
	$categoryId = $tp->toDB($_GET['category_id']);

    // Delete category from tables
    $sql -> db_Delete(DB_TABLE_SHOP_ITEM_CATEGORIES, "category_id=$categoryId");
    header("Location: admin_categories.php");
    exit;
}

require_once(e_ADMIN."footer.php");
?>