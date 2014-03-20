<?php
/*******************************************************************\
 * CashbackEngine v2.1
 * http://www.CashbackEngine.net
 *
 * Copyright (c) 2010-2014 CashbackEngine Software. All rights reserved.
 * ------------ CashbackEngine IS NOT FREE SOFTWARE --------------
\*******************************************************************/

	session_start();
	require_once("inc/auth.inc.php");
	require_once("inc/config.inc.php");

	$cc = 0;

	function GetLastVisit($retailer_id)
	{
		global $userid;
		$query = "SELECT DATE_FORMAT(cashbackengine_clickhistory.added, '%e %b %Y %H:%i:%S') AS last_visit FROM cashbackengine_clickhistory WHERE user_id='$userid' AND retailer_id='".(int)$retailer_id."' ORDER BY added DESC LIMIT 1";
		$result = smart_mysql_query($query);
		if (mysql_num_rows($result) > 0)
		{
			$row = mysql_fetch_array($result);
			return $row['last_visit'];
		}
	}

	$query = "SELECT cashbackengine_clickhistory.*, COUNT(*) AS total_visits, DATE_FORMAT(cashbackengine_clickhistory.added, '%e %b %Y %H:%i:%S') AS click_datetime, cashbackengine_retailers.* FROM cashbackengine_clickhistory cashbackengine_clickhistory, cashbackengine_retailers cashbackengine_retailers WHERE cashbackengine_clickhistory.user_id='$userid' AND cashbackengine_clickhistory.retailer_id=cashbackengine_retailers.retailer_id GROUP BY cashbackengine_clickhistory.retailer_id ORDER BY cashbackengine_clickhistory.added DESC LIMIT 50";
	$result = smart_mysql_query($query);
	$total = mysql_num_rows($result);


	///////////////  Page config  ///////////////
	$PAGE_TITLE = CBE1_CLICK_TITLE;

	require_once ("inc/header.inc.php");

?>

	<h1><?php echo CBE1_CLICK_TITLE; ?></h1>


	<?php if ($total > 0) { ?>

		<p align="center"><?php echo CBE1_CLICK_TEXT; ?></p>

            <table align="center" width="95%" border="0" class="btb" cellspacing="0" cellpadding="3">
              <tr>
				<th width="50%"><?php echo CBE1_CLICK_STORE; ?></th>
				<th width="10%"><?php echo CBE1_CLICK_VISITS; ?></th>
                <th width="25%"><?php echo CBE1_CLICK_LAST_VISIT; ?></th>
				<th width="15%"><?php echo CBE1_CLICK_GOSTORE; ?></th>
              </tr>
			<?php while ($row = mysql_fetch_array($result)) { $cc++; ?>
              <tr class="<?php if (($cc%2) == 0) echo "row_even"; else echo "row_odd"; ?>">
                <td valign="middle" align="left"><a class="click" href="<?php echo GetRetailerLink($row['retailer_id'], $row['title']); ?>"><?php echo $row['title']; ?></a></td>
				<td nowrap="nowrap" valign="middle" align="center"><?php echo $row['total_visits']; ?></td>
                <td nowrap="nowrap" valign="middle" align="center"><?php echo GetLastVisit($row['retailer_id']); ?></td>
				<td nowrap="nowrap" valign="middle" align="center"><a class="go2store" href="<?php echo SITE_URL; ?>go2store.php?id=<?php echo $row['retailer_id']; ?>" target="_blank"><?php echo CBE1_GO_TO_STORE; ?></a></td>
              </tr>
			<?php } ?>
           </table>

    <?php }else{ ?>
			<p align="center">
				<?php echo CBE1_CLICK_NO; ?><br/><br/>
				<a class="goback" href="#" onclick="history.go(-1);return false;"><?php echo CBE1_GO_BACK; ?></a>
			</p>
     <?php } ?>

<?php require_once ("inc/footer.inc.php"); ?>