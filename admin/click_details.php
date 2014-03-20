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
		$pn			= (int)$_GET['pn'];
		$click_id	= (int)$_GET['id'];

		$query = "SELECT *, DATE_FORMAT(added, '%e %b %Y %h:%i:%s %p') AS click_date FROM cashbackengine_clickhistory WHERE click_id='$click_id' LIMIT 1";
		$result = smart_mysql_query($query);
		$total = mysql_num_rows($result);
	}


	$title = "Click Details";
	require_once ("inc/header.inc.php");

?>
    
     <h2>Click Details</h2>

	 <?php if ($total > 0) { $row = mysql_fetch_array($result); ?>

          <table width="70%" align="center" bgcolor="#F7F7F7" cellpadding="2" cellspacing="5" border="0">
			<tr>
				<td width="230" valign="middle" align="right" class="tb1">Click ID:</td>
				<td valign="top"><?php echo $row['click_id']; ?></td>
			</tr>
			<tr>
				<td width="100" valign="middle" align="right" class="tb1">Store:</td>
				<td valign="top"><a href="retailer_details.php?id=<?php echo $row['retailer_id']; ?>"><?php echo GetStoreName($row['retailer_id']); ?></a></td>
			</tr>
			<tr>
				<td valign="middle" align="right" class="tb1">User:</td>
				<td valign="top"><a href="user_details.php?id=<?php echo $row['user_id']; ?>" class="user"><?php echo GetUsername($row['user_id']); ?></a></td>
			</tr>
			<tr>
				<td valign="top" align="right" class="tb1">Date/Time:</td>
				<td valign="top"><?php echo $row['click_date']; ?></td>
            </tr>
            <tr>
              <td align="center" colspan="2" valign="bottom">
				<input type="button" class="cancel" name="cancel" value="Go Back" onClick="history.go(-1);return false;" />
			  </td>
            </tr>
          </table>
      
	  <?php }else{ ?>
			<p align="center">Sorry, no click found.<br/><br/><a class="goback" href="#" onclick="history.go(-1);return false;">Go Back</a></p>
      <?php } ?>

<?php require_once ("inc/footer.inc.php"); ?>