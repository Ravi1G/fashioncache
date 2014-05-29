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


	if (isset($_POST['action']) && $_POST['action'] == "add")
	{
		
		$category_name			= mysql_real_escape_string(getPostParameter('catname'));
		$slug_value				= mysql_real_escape_string(getPostParameter('slug'));
		
		$category_description	= mysql_real_escape_string(nl2br(getPostParameter('description')));
		$parent_category		= (int)getPostParameter('parent_id');
		$meta_description		= mysql_real_escape_string(nl2br(getPostParameter('meta_description')));
		$meta_keywords			= mysql_real_escape_string(getPostParameter('meta_keywords'));
		$sort_order				= (int)getPostParameter('sort_order');
 		$show_in_menu			= (int)getPostParameter('show_in_menu');
 		
 		//Replace space character with '-'
		$slug = str_replace(" ","-",$slug_value);
		
 		if($category_name=="" || $slug_value=="")
 		{
 			$errors[] =  'Enter all the required fields';
 		}
 		if(count($errors)==0)
 		{
 		if (isset($category_name) && $category_name != "")
		{
			$check_query = smart_mysql_query("SELECT * FROM cashbackengine_categories WHERE parent_id='$parent_category' AND name='$category_name' AND category_url='$category_url'");
			
			if (mysql_num_rows($check_query) == 0)
			{
				$pos     = "";
				$pos_val = "";
				//Check for the exisint slug if any matches then put number at the end of slug
				$query_check_exisiting_slug = smart_mysql_query("SELECT * FROM cashbackengine_categories WHERE slug = '$slug'");
				$row_category = mysql_fetch_assoc($query_check_exisiting_slug);
				
				//$slug = $slug_value;
				
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

				$sql = "INSERT INTO 
								cashbackengine_categories 
								SET parent_id='$parent_category', 
								name='$category_name', 
								description='$category_description', 
								category_url='', 
								meta_description='$meta_description', 
								meta_keywords='$meta_keywords', 
								sort_order='$sort_order', 
								show_in_menu='$show_in_menu',
								slug ='$slug'
								";

				if (smart_mysql_query($sql))
				{
					header("Location: categories.php?msg=added");
					exit();
				}
			}
			else
			{
				header("Location: categories.php?msg=exists");
				exit();
			}
		}
 	}
 	else {
 		foreach ($errors as $errorname)
				$errormsg .= "&#155; ".$errorname."<br/>";
 	}
}

	$title = "Add Category";
	require_once ("inc/header.inc.php");

?>

		  <h2>Add Category</h2>
<?php if (isset($errormsg) && $errormsg != "") { ?>
			<div style="width:75%;" class="error_box"><?php echo $errormsg; ?></div>
		<?php } ?>
		  <form action="" method="post">
		  <table align="center" width="75%" border="0" cellpadding="3" cellspacing="0">
          <tr>
            <td colspan="2" align="right" valign="top"><font color="red">* denotes required field</font></td>
          </tr>
          <tr>
            <td width="150" nowrap="nowrap" valign="middle" align="right" class="tb1"><span class="req">* </span>Category Name:</td>
			<td align="left">
				<input type="text" name="catname" id="catname" value="<?php echo getPostParameter('catname'); ?>" size="40" class="textbox" />
			</td>
          </tr>
          <tr>
            <td width="150" nowrap="nowrap" valign="middle" align="right" class="tb1"><span class="req">* </span>Slug:</td>
			<td align="left">
				<input type="text" name="slug" id="slug" value="<?php echo getPostParameter('slug'); ?>" size="40" class="textbox" />
			</td>
          </tr>
          <tr>
            <td nowrap="nowrap" valign="middle" align="right" class="tb1">Parent Category:</td>
			<td align="left">
				<select name="parent_id">
					<option value=""> ---------- None ---------- </option>
					<?php CategoriesDropDown (0); ?>
				</select>
			</td>
          </tr>
          <tr>
            <td nowrap="nowrap" valign="middle" align="right" class="tb1">Description:</td>
			<td align="left" valign="top"><textarea name="description" cols="75" rows="5" class="textbox2"><?php echo getPostParameter('description'); ?></textarea></select>
			</td>
          </tr>
			<tr>
				<td valign="middle" align="right" class="tb1">Meta Description:</td>
				<td valign="top"><textarea name="meta_description" cols="75" rows="2" class="textbox2"><?php echo getPostParameter('meta_description'); ?></textarea></td>
            </tr>
			<tr>
				<td valign="middle" align="right" class="tb1">Meta Keywords:</td>
				<td valign="top"><input type="text" name="meta_keywords" id="meta_keywords" value="<?php echo getPostParameter('meta_keywords'); ?>" size="78" class="textbox" /></td>
            </tr>
            <tr>
				<td valign="middle" align="right" class="tb1">Sort Order:</td>
				<td valign="middle"><input type="text" class="textbox" name="sort_order" value="<?php echo getPostParameter('sort_order'); ?>" size="5" /></td>
            </tr>
             <tr>
				<td valign="middle" align="right" class="tb1">Show in menu?</td>
				<td valign="middle"><input type="checkbox" class="checkbox" name="show_in_menu" value="1" <?php if ($row['show_in_menu'] == 1) echo "checked=\"checked\""; ?> />&nbsp;Yes!</td>
            </tr>
          <tr>
			<td>&nbsp;</td>
			<td valign="middle" align="left">
				<input type="hidden" name="action" id="action" value="add" />
				<input type="submit" name="add" id="add" class="submit" value="Add Category" />
		    </td>
          </tr>
		  </table>
		  </form>


<?php require_once ("inc/footer.inc.php"); ?>