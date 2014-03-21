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

	$id = (int)trim($_GET['id']);
	$isEdit = $id==0 ? 0 : 1;
	$isPost = count($_POST)>0 ? 1 : 0;
	$retailer = array();
	
	if ($isPost)
	{
		$title			= mysql_real_escape_string(getPostParameter('title'));
		$description	= mysql_real_escape_string(nl2br(getPostParameter('description')));
		$retailer_id	= (int)getPostParameter('retailer_id');
 
		//validation
		unset($errors);
		$errors = array();
		
		if($title==''){
			$errors[] = 'Title is mandatory.';			
		}
		
		if($description==''){
			$errors[] = 'Description is mandatory.';			
		}
		
		if(empty($retailer_id)){
			$errors[] = 'Retailer is mandatory.';			
		}

		if (count($errors) == 0)
		{
			if($id)
			{
				$msg = 'updated';			
				$sql = "update cashbackengine_trending_sales set title='$title', description='$description', retailer_id=$retailer_id where trending_sale_id=$id";
			}
			else 
			{
				$msg = 'added';
				$sql = "INSERT INTO cashbackengine_trending_sales(title, description, retailer_id) values('$title', '$description', $retailer_id)";
			}
			
			smart_mysql_query($sql);
			header("Location: trending_sales.php?msg=$msg");
			exit();
		}
		else
		{
			$errormsg = "";
			foreach ($errors as $errorname)
				$errormsg .= "&#155; ".$errorname."<br/>";
		}
	}
	
	//run in case of edit when it's not post(First load)
	//fetch sale data
	if($isEdit && !$isPost)
	{
		$retailer = getTrendingSale($id);
		
		if(empty($retailer)){
			header("Location: trending_sales.php?msg=invalid_sale&msg_type=error");
			exit();
		}
		
		$retailer_id = $retailer['retailer_id'];
		$_POST = $retailer;
	}
	
	if($isEdit)
	{
		$title = "Edit Trending Sale";
	}
	else 
	{
		$title = "Add Trending Sale";
	}

	
	require_once ("inc/header.inc.php");
	
	$retailers = getAllActiveRetailer()
?>

		<h2><?php echo $title;?></h2>
		  
		<?php if (isset($errormsg) && $errormsg != "") { ?>
			<div class="error_box"><?php echo $errormsg; ?></div>
		<?php }?>

		  <form action="" method="post">
		  <table align="center" width="75%" border="0" cellpadding="3" cellspacing="0">
          <tr>
            <td colspan="2" align="right" valign="top"><font color="red">* denotes required field</font></td>
          </tr>
          <tr>
            <td nowrap="nowrap" valign="middle" align="right" class="tb1"><span class="req">* </span>Retailer:</td>
			<td align="left">
				<select name="retailer_id">
					<option value=""> ---------- None ---------- </option>
					<?php 
					foreach($retailers as $single){
					?>
						<option value="<?php echo $single['retailer_id']?>" <?php if($retailer_id==$single['retailer_id']){?>selected="selected"<?php } ?>><?php echo $single['title']?></option>
					<?php 	
					}
					?>
				</select>
			</td>
          </tr>
          <tr>
            <td width="150" nowrap="nowrap" valign="middle" align="right" class="tb1"><span class="req">* </span>Title:</td>
			<td align="left">
				<input type="text" name="title" id="title" value="<?php echo getPostParameter('title'); ?>" size="40" class="textbox" />
			</td>
          </tr>
          <tr>
            <td nowrap="nowrap" valign="middle" align="right" class="tb1"><span class="req">* </span>Description:</td>
			<td align="left" valign="top"><textarea name="description" cols="75" rows="5" class="textbox2"><?php echo getPostParameter('description'); ?></textarea></select>
			</td>
          </tr>
          <tr>
			<td>&nbsp;</td>
			<td valign="middle" align="left">
				<input type="hidden" name="action" id="action" value="add" />
				<input type="submit" name="add" id="add" class="submit" value="<?php echo $title;?>" />
		    </td>
          </tr>
		  </table>
		  </form>


<?php require_once ("inc/footer.inc.php"); ?>