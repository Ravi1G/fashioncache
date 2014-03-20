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


if (isset($_POST['action']) && $_POST['action'] == "editpmethod")
{
	unset($errs);
	$errs = array();

	$pmethod_id			= (int)getPostParameter('pmethodid');
	$pmethod_title		= mysql_real_escape_string(getPostParameter('pmethod_title'));
	$pmethod_details	= mysql_real_escape_string(nl2br(getPostParameter('pmethod_details')));
	$status				= mysql_real_escape_string(getPostParameter('status'));


	if(!($pmethod_title && $pmethod_details && $status))
	{
		$errs[] = "Please fill in all required fields";
	}

	if (count($errs) == 0)
	{
		$sql = "UPDATE cashbackengine_pmethods SET pmethod_title='$pmethod_title', pmethod_details='$pmethod_details', status='$status' WHERE pmethod_id='$pmethod_id' LIMIT 1";

		if (smart_mysql_query($sql))
		{
			header("Location: pmethods.php?msg=updated");
			exit();
		}
	}
	else
	{
		$allerrors = "";
		foreach ($errs as $errorname)
			$allerrors .= "&#155; ".$errorname."<br/>\n";
	}

}


	if (isset($_GET['id']) && is_numeric($_GET['id']))
	{
		$pmid = (int)$_GET['id'];

		$query = "SELECT * FROM cashbackengine_pmethods WHERE pmethod_id='$pmid' LIMIT 1";
		$result = smart_mysql_query($query);
		$total = mysql_num_rows($result);
	}


	$title = "Edit Payment Method";
	require_once ("inc/header.inc.php");

?>
 
      <?php if ($total > 0) {

		  $row = mysql_fetch_array($result);
		  
      ?>

        <h2><?php echo $row['pmethod_title']; ?></h2>


		<?php if (isset($allerrors) && $allerrors != "") { ?>
			<div class="error_box"><?php echo $allerrors; ?></div>
		<?php } ?>

        <form action="" method="post">
          <table width="70%" bgcolor="#F7F7F7" align="center" cellpadding="2" cellspacing="5" border="0">
          <tr>
            <td width="100" nowrap="nowrap" valign="middle" align="right" class="tb1"><span class="req">* </span>Name:</td>
            <td valign="top"><input type="text" name="pmethod_title" id="pmethod_title" value="<?php echo $row['pmethod_title']; ?>" size="32" class="textbox" /></td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">
				<span class="req">* </span>Payment Details:
				<br/><span class="help">user will need to provide<br/> this information to complete<br/> the money transfer</span>
			</td>
            <td valign="top"><textarea name="pmethod_details" cols="45" rows="5" class="textbox2"><?php echo strip_tags($row['pmethod_details']); ?></textarea></td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1"><span class="req">* </span>Status:</td>
            <td valign="top">
				<select name="status">
					<option value="active" <?php if ($row['status'] == "active") echo "selected"; ?>>active</option>
					<option value="inactive" <?php if ($row['status'] == "inactive") echo "selected"; ?>>inactive</option>
				</select>
			</td>
          </tr>
          <tr>
            <td colspan="2" align="center" valign="bottom">
			<input type="hidden" name="pmethodid" id="pmethodid" value="<?php echo (int)$row['pmethod_id']; ?>" />
			<input type="hidden" name="action" id="action" value="editpmethod" />
			<input type="submit" name="save" id="save" class="submit" value="Update" />
			<input type="button" class="cancel" name="cancel" value="Cancel" onClick="javascript:document.location.href='pmethods.php'" />
		  </td>
          </tr>
        </table>
      </form>

      <?php }else{ ?>
				<p align="center">Sorry, no payment method found.<br/><br/><a class="goback" href="#" onclick="history.go(-1);return false;">Go Back</a></p>
      <?php } ?>


<?php require_once ("inc/footer.inc.php"); ?>