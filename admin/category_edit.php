<?php
/*******************************************************************\
 * CashbackEngine v2.1
 * http://www.CashbackEngine.net
 *
 * Copyright (c) 2010-2014 CashbackEngine Software. All rights reserved.
 * ------------ CashbackEngine IS NOT FREE SOFTWARE --------------
\*******************************************************************/

	session_start();
	require_once("../inc/adm_auth.inc.php");
	require_once("../inc/config.inc.php");
	require_once("./inc/admin_funcs.inc.php");


	if (isset($_GET['id']) && is_numeric($_GET['id']))
	{
		$id = (int)$_GET['id'];

		$query = "SELECT * FROM cashbackengine_categories WHERE category_id='$id'";
		$rs = smart_mysql_query($query);
		$total = mysql_num_rows($rs);
	}


	if (isset($_POST["action"]) && $_POST["action"] == "edit")
	{
		unset($errors);
		$errors = array();
 
		$catid					= (int)getPostParameter('catid');
		$catname				= mysql_real_escape_string(getPostParameter('catname'));
		$slug_value				= mysql_real_escape_string(getPostParameter('slug'));
		
		$category_description	= mysql_real_escape_string(nl2br(getPostParameter('description')));
		$parent_category		= (int)getPostParameter('parent_id');
		$meta_description		= mysql_real_escape_string(nl2br(getPostParameter('meta_description')));
		$meta_keywords			= mysql_real_escape_string(getPostParameter('meta_keywords'));
		$sort_order				= (int)getPostParameter('sort_order');
		$show_in_menu			= (int)getPostParameter('show_in_menu');

		//Replace space character with '-'
		$slug = str_replace(" ","-",$slug_value);
		
		
		if (!($catname && $catid && $slug_value))
		{
			$errors[] = "Please enter required fields";
		}

		if (count($errors) == 0)
		{
			$query_check_slug = smart_mysql_query("SELECT * FROM cashbackengine_categories WHERE slug = '$slug'");
			$row_category = mysql_fetch_assoc($query_check_slug);

			if($row_category['category_id'] == $catid)
			{
				$slug = $slug_value;
			}
			else{
				$pos     = "";
				$pos_val = "";
				//Check for the exisint slug if any matches then put number at the end of slug
				$query_check_exisiting_slug = smart_mysql_query("SELECT * FROM cashbackengine_categories WHERE slug = '$slug'");
				$row_category = mysql_fetch_assoc($query_check_exisiting_slug);
				
				while(mysql_num_rows($query_check_exisiting_slug)!==0)
					{
						$pos = strpos($row_category['slug'], "-");
						if($pos)
						{
							$pieces    = explode("-", $row_category['slug']);
							$count = count($pieces);
							for($i=0;$i<$count;$i++)
							{
								$val = $val.$pieces[$i].'-';
							}
							
							$pos_value = $pieces[$count-1];
							if(!$pos_value)
							{
								$pos_value = 1;
							}
							else
							{
								$pos_value = intval($pos_value) + 1;
							}
							
							$slug = str_replace(" ","-",$slug_value).'-'.$pos_value;
						}
						else
						{
							$slug = $slug_value.'-1'; 
						}
						$query_check_exisiting_slug = smart_mysql_query("SELECT * FROM cashbackengine_categories WHERE slug = '$slug'");
						$row_category = mysql_fetch_assoc($query_check_exisiting_slug);
					}
			}
			

			
			smart_mysql_query("UPDATE cashbackengine_categories SET 
															parent_id='$parent_category', 
															name='$catname', 
															description='$category_description', 
															category_url='', 
															meta_description='$meta_description', 
															meta_keywords='$meta_keywords', 
															sort_order='$sort_order',
															show_in_menu='$show_in_menu', 
															slug = '$slug'
															WHERE category_id='$catid'");

			header("Location: categories.php?msg=updated");
			exit();
		}
		else
		{
			$errormsg = "";
			foreach ($errors as $errorname)
				$errormsg .= "&#155; ".$errorname."<br/>";
		}
	}


	$title = "Edit Category";
	require_once ("inc/header.inc.php");

?>


    <h2>Edit Category</h2>

	<?php if ($total > 0) {
	
		$row = mysql_fetch_array($rs);

	?>

		<?php if (isset($errormsg) && $errormsg != "") { ?>
			<div style="width:75%;" class="error_box"><?php echo $errormsg; ?></div>
		<?php } ?>

      <form action="" method="post">
        <table width="75%" cellpadding="2" cellspacing="5" border="0" align="center">
          <tr>
            <td colspan="2" align="right" valign="top"><font color="red">* denotes required field</font></td>
          </tr>
          <tr>
            <td width="150" nowrap="nowrap" valign="middle" align="right" class="tb1"><span class="req">* </span>Category Name:</td>
            <td valign="top"><input type="text" name="catname" id="catname" value="<?php echo $row['name']; ?>" size="40" class="textbox" /></td>
          </tr>
          <tr>
            <td width="150" nowrap="nowrap" valign="middle" align="right" class="tb1"><span class="req">* </span>Slug:</td>
			<td align="left">
				<input type="text" name="slug" id="slug" value="<?php echo $row['slug']; ?>" size="40" class="textbox" />
			</td>
          </tr>
          <tr>
            <td nowrap="nowrap" valign="middle" align="right" class="tb1">Parent Category:</td>
			<td align="left">
				<select name="parent_id">
					<option value=""> ---------- None ---------- </option>
					<?php CategoriesDropDown (0,"",$row['category_id'],$row['parent_id']); ?>
				</select>
			</td>
          </tr>
          <tr>
            <td nowrap="nowrap" valign="middle" align="right" class="tb1">Description:</td>
			<td align="left" valign="top"><textarea name="description" cols="75" rows="5" class="textbox2"><?php echo strip_tags($row['description']); ?></textarea></select>
			</td>
          </tr>
			<tr>
				<td valign="middle" align="right" class="tb1">Meta Description:</td>
				<td valign="top"><textarea name="meta_description" cols="75" rows="2" class="textbox2"><?php echo strip_tags($row['meta_description']); ?></textarea></td>
            </tr>
			<tr>
				<td valign="middle" align="right" class="tb1">Meta Keywords:</td>
				<td valign="top"><input type="text" name="meta_keywords" id="meta_keywords" value="<?php echo $row['meta_keywords']; ?>" size="78" class="textbox" /></td>
            </tr>
            <tr>
				<td valign="middle" align="right" class="tb1">Sort Order:</td>
				<td valign="middle"><input type="text" class="textbox" name="sort_order" value="<?php echo $row['sort_order']; ?>" size="5" /></td>
            </tr>
             <tr>
				<td valign="middle" align="right" class="tb1">Show in menu?</td>
				<td valign="middle"><input type="checkbox" class="checkbox" name="show_in_menu" value="1" <?php if ($row['show_in_menu'] == 1) echo "checked=\"checked\""; ?> />&nbsp;Yes!</td>
            </tr>
            <tr>
              <td align="center" colspan="2" valign="bottom">
			  <input type="hidden" name="catid" id="catid" value="<?php echo (int)$row['category_id']; ?>" />
			  <input type="hidden" name="action" id="action" value="edit">
			  <input type="submit" class="submit" name="update" id="update" value="Update" />
              <input type="button" class="cancel" name="cancel" value="Cancel" onClick="javascript:document.location.href='categories.php'" /></td>
            </tr>
          </table>
      </form>
      
	  <?php }else{ ?>
			<p align="center">Sorry, no record found.<br/><br/><a class="goback" href="#" onclick="history.go(-1);return false;">Go Back</a></p>
      <?php } ?>

<?php require_once ("inc/footer.inc.php"); ?>