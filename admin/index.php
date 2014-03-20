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
	require_once("./inc/ce.inc.php");


	$today = date("Y-m-d");
	$yesterday = date("Y-m-d", mktime(0, 0, 0, date("m") , date("d") - 1, date("Y")));

	$users_today = mysql_fetch_array(smart_mysql_query("SELECT COUNT(*) AS total FROM cashbackengine_users WHERE date(created)='$today'"));
	$users_today = $users_today['total'];
	if ($users_today > 0) $users_today = "+" . $users_today;

	$clicks_today = mysql_fetch_array(smart_mysql_query("SELECT COUNT(*) AS total FROM cashbackengine_clickhistory WHERE date(added)='$today'"));
	$clicks_today = $clicks_today['total'];
	if ($clicks_today > 0) $clicks_today = "+" . $clicks_today;

	$users_yesterday = mysql_fetch_array(smart_mysql_query("SELECT COUNT(*) AS total FROM cashbackengine_users WHERE date(created)='$yesterday'"));
	$users_yesterday = $users_yesterday['total'];

	$clicks_yesterday = mysql_fetch_array(smart_mysql_query("SELECT COUNT(*) AS total FROM cashbackengine_clickhistory WHERE date(added)='$yesterday'"));
	$clicks_yesterday = $clicks_yesterday['total'];

	$users_7days = mysql_fetch_array(smart_mysql_query("SELECT COUNT(*) AS total FROM cashbackengine_users WHERE date_sub(curdate(), interval 7 day) <= created"));
	$users_7days = $users_7days['total'];

	$users_30days = mysql_fetch_array(smart_mysql_query("SELECT COUNT(*) AS total FROM cashbackengine_users WHERE date_sub(curdate(), interval 30 day) <= created"));
	$users_30days = $users_30days['total'];

	$clicks_7days = mysql_fetch_array(smart_mysql_query("SELECT COUNT(*) AS total FROM cashbackengine_clickhistory WHERE date_sub(curdate(), interval 7 day) <= added"));
	$clicks_7days = $clicks_7days['total'];

	$all_users = mysql_fetch_array(smart_mysql_query("SELECT COUNT(*) AS total FROM cashbackengine_users"));
	$all_users = $all_users['total'];

	$all_retailers = mysql_fetch_array(smart_mysql_query("SELECT COUNT(*) AS total FROM cashbackengine_retailers"));
	$all_retailers = $all_retailers['total'];

	$all_coupons = mysql_fetch_array(smart_mysql_query("SELECT COUNT(*) AS total FROM cashbackengine_coupons"));
	$all_coupons = $all_coupons['total'];

	$all_reviews = mysql_fetch_array(smart_mysql_query("SELECT COUNT(*) AS total FROM cashbackengine_reviews"));
	$all_reviews = $all_reviews['total'];

	$clicks_30days = mysql_fetch_array(smart_mysql_query("SELECT COUNT(*) AS total FROM cashbackengine_clickhistory WHERE date_sub(curdate(), interval 30 day) <= added"));
	$clicks_30days = $clicks_30days['total'];


	$title = "Admin Home";
	require_once ("inc/header.inc.php");

?>

	<h2>Admin Home</h2>

	<?php if (file_exists("../install.php")) { ?>
		<div class="error_box">You must now delete "install.php" from your server. Failing to delete these files is a serious security risk!</div>
	<?php } ?>

 <table align="center" width="100%" border="0" cellpadding="2" cellspacing="2">
 <tr>
	<td width="40%" align="left" valign="top">

		<table align="center" width="95%" border="0" cellpadding="6" cellspacing="2">
		<tr>
			<td nowrap="nowrap" align="left" valign="middle" class="tb2"><font color="#84C315"><b>Cashback</b></font><font color="#5392D5"><b>Engine</b></font> version:</td>
			<td align="right" valign="middle"><?php echo $cashbackengine_version; ?></td>
		</tr>
		<tr>
			<td align="left" valign="middle" class="tb2">License Key:</td>
			<td nowrap="nowrap" align="right" valign="middle"><?php echo GetSetting('license'); ?></td>
		</tr>
		<tr>
			<td colspan="2"><div class="sline"></div></td>
		</tr>
		</table>

	</td>
	<td width="30%" align="left" valign="top">

		<table align="center" width="100%" border="0" cellpadding="3" cellspacing="2">
		<tr>
			<td align="left" valign="middle" class="tb2">Clicks Today:</td>
			<td align="right" valign="middle" class="stat_s"><a href="clicks.php"><font color="#2F97EB"><?php echo $clicks_today; ?></font></a></td>
		</tr>
		<tr>
			<td align="left" valign="middle" class="tb2">Clicks Yesterday:</td>
			<td align="right" valign="middle" class="stat_s"><?php echo $clicks_yesterday; ?></td>
		</tr>
		<tr>
			<td align="left" valign="middle" class="tb2">Last 7 Days Clicks:</td>
			<td align="right" valign="middle" class="stat_s"><?php echo $clicks_7days; ?></td>
		</tr>
		<tr>
			<td align="left" valign="middle" class="tb2">Last 30 Days Clicks:</td>
			<td align="right" valign="middle" class="stat_s"><?php echo $clicks_30days; ?></td>
		</tr>
		<tr>
			<td colspan="2"><div class="sline"></div></td>
		</tr>
		</table>

	</td>
	<td width="30%" align="left" valign="top">

		<table align="center" width="100%" border="0" cellpadding="3" cellspacing="2">
		<tr>
			<td align="left" valign="middle" class="tb2">Users Today:</td>
			<td align="right" valign="middle" class="stat_s"><a href="users.php"><font color="#2F97EB"><?php echo $users_today; ?></font></a></td>
		</tr>
		<tr>
			<td align="left" valign="middle" class="tb2">Users Yesterday:</td>
			<td align="right" valign="middle" class="stat_s"><?php echo $users_yesterday; ?></td>
		</tr>
		<tr>
			<td align="left" valign="middle" class="tb2">Last 7 Days Users:</td>
			<td align="right" valign="middle" class="stat_s"><?php echo $users_7days; ?></td>
		</tr>
		<tr>
			<td align="left" valign="middle" class="tb2">Last 30 Days Users:</td>
			<td align="right" valign="middle" class="stat_s"><?php echo $users_30days; ?></td>
		</tr>
		<tr>
			<td colspan="2"><div class="sline"></div></td>
		</tr>
		<tr>
			<td align="left" valign="middle" class="tb2">Total Users:</td>
			<td align="right" valign="middle" class="stat_s"><?php echo $all_users; ?></td>
		</tr>
		<tr>
			<td align="left" valign="middle" class="tb2">Total Retailers:</td>
			<td align="right" valign="middle" class="stat_s"><?php echo $all_retailers; ?></td>
		</tr>
		<tr>
			<td align="left" valign="middle" class="tb2">Total Reviews:</td>
			<td align="right" valign="middle" class="stat_s"><?php echo $all_reviews; ?></td>
		</tr>
		<tr>
			<td align="left" valign="middle" class="tb2">Total Coupons:</td>
			<td align="right" valign="middle" class="stat_s"><?php echo $all_coupons; ?></td>
		</tr>
		<tr>
			<td nowrap="nowrap" align="left" valign="middle" class="tb2">Total Cashback:</td>
			<td align="right" valign="middle" class="stat_s_green"><?php echo GetCashbackTotal(); ?></td>
		</tr>
		</table>

	</td>
 </tr>
 </table>



<?php require_once ("inc/footer.inc.php"); ?>