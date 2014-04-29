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

	//$id = (int)trim($_GET['id']);
	$id = 1;
	$isEdit = $id==0 ? 0 : 1;
	$isPost = count($_POST)>0 ? 1 : 0;
	$retailer = array();
	
	if ($isPost)
	{
		$title			= mysql_real_escape_string(getPostParameter('title'));
		$retailer_id	= (int)getPostParameter('retailer_id');
		$link			= mysql_real_escape_string(getPostParameter('link'));
 
		//validation
		unset($errors);
		$errors = array();
		
		if($title==''){
			$errors[] = 'Title is mandatory.';			
		}
		
		if(empty($retailer_id)){
			$errors[] = 'Retailer is mandatory.';			
		}
		
		//validate link
		if (isset($link) && $link!="" && !preg_match("/\b(?:(?:https?):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i", $link))
		{
			$errors[] = "Enter correct link url. (e.g. 'http://abc.com' or 'https://abc.com' or 'http://www.abc.com')";
		}

		if (count($errors) == 0)
		{
			if($id)
			{
				$msg = 'updated';			
				$sql = "update cashbackengine_sale_alert set link='$link', title='$title', retailer_id=$retailer_id where sale_alert_id=$id";
			}
			else 
			{
				$msg = 'added';
				$sql = "INSERT INTO cashbackengine_sale_alert(link, title, retailer_id) values('$link','$title', $retailer_id)";
			}
			
			smart_mysql_query($sql);
			header("Location: sale_alert_add_edit.php?msg=$msg");
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
		$retailer = getSaleAlert($id);
		if(empty($retailer)){
			//header("Location: sale_alerts.php?msg=invalid_sale_alert&msg_type=error");
			//exit();
		}
		
		$retailer_id = $retailer['retailer_id'];
		$_POST = $retailer;
	}
	
	if($isEdit)
	{
		$title = "Edit Sale Alert";
	}
	else 
	{
		$title = "Add Sale Alert";
	}

	
	require_once ("inc/header.inc.php");
	$retailers = getAllActiveRetailer()
?>

		<h2><?php echo $title;?></h2>
		<?php 
		if (!isset($errormsg) && $errormsg == "" && isset($_GET['msg']) && $_GET['msg']!="" && $_GET['msg']=="updated") { 
			?>
			<div style="width:60%;" class="success_box">
				<?php
				switch ($_GET['msg'])
				{
					case "updated": echo "Sale Alert has been successfully edited"; break;				
				}
				?>
			</div>
			<?php } ?>
			  
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
			<td width="150" nowrap="nowrap" valign="middle" align="right" class="tb1">Link :</td>
			<td align="left">
				<input type="text" name="link" id="link" value="<?php echo getPostParameter('link'); ?>" size="40" class="textbox" />
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