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


	///////////////  Page config  ///////////////
	$PAGE_TITLE = CBE1_BALANCE_TITLE;

	require_once ("inc/header.inc.php");

?>

		<h1><?php echo CBE1_BALANCE_TITLE; ?></h1>
<?php 
	$paid = GetUserBalance($userid);
	$pending = GetPendingBalance();
	
	$type = GetCashbackType($paid);
	$paid_without_type = RemoveCashbackType($paid);
	
	$type = GetCashbackType($pending);
	$pending_without_type = RemoveCashbackType($pending);
	
	
	$total = $paid_without_type + $pending_without_type;
	$total = '$'.$total;
?>

		<table align="center" class="btb" width="300" border="0" cellspacing="0" cellpadding="7">
		<tr class="available_balance">
			<td width="200" valign="middle" align="left"><?php echo CBE1_BALANCE_ABALANCE; ?></td>
			<td valign="middle" align="right"><?php echo $paid;?></td>
		</tr>
		<tr class="pending_cashback">
			<td valign="middle" align="left"><?php echo CBE1_BALANCE_PCASHBACK; ?></td>
			<td valign="middle" align="right"><?php echo $pending; ?></td>
		</tr>
		<tr>
			<td valign="middle" align="left">Total</td>
			<td valign="middle" align="right"><?php echo $total; ?></td>
		</tr>
		<!-- <tr class="declined_cashback">
			<td valign="middle" align="left"><?php echo CBE1_BALANCE_DCASHBACK; ?></td>
			<td valign="middle" align="right"><?php echo GetDeclinedBalance(); ?></td>
		</tr>
		<tr class="cashout_requested">
			<td valign="middle" align="left"><?php echo CBE1_BALANCE_CREQUESTED; ?></td>
			<td valign="middle" align="right"><?php echo GetCashOutRequested(); ?></td>
		</tr>
		<tr class="cashout_processed">
			<td valign="middle" align="left"><?php echo CBE1_BALANCE_CPROCESSED; ?></td>
			<td valign="middle" align="right"><?php echo GetCashOutProcessed(); ?></td>
		</tr>
		<tr class="lifetime_cashback">
			<td valign="middle" align="left"><?php echo CBE1_BALANCE_LCASHBACK; ?></td>
			<td valign="middle" align="right"><?php echo GetLifetimeCashback(); ?></td>
		</tr>-->
		</table>

		<p align="center"><?php echo CBE1_BALANCE_TEXT; ?> <a href="<?php echo SITE_URL; ?>mypayments.php"><?php echo CBE1_PAYMENTS_TITLE; ?></a></p>

		<?php if (GetBalanceUpdateDate($userid)) { ?>
			<p align="center"><?php echo CBE1_BALANCE_TEXT2; ?> <?php echo GetBalanceUpdateDate($userid); ?></p>
		<?php } ?>


     <?php

		$cc = 0;

		$query = "SELECT *, DATE_FORMAT(created, '%e %b %Y') AS date_created, DATE_FORMAT(updated, '%e %b %Y') AS updated_date FROM cashbackengine_transactions WHERE user_id='$userid' AND program_id!='0' AND status!='unknown' ORDER BY created DESC";
		$result = smart_mysql_query($query);
		$total = mysql_num_rows($result);

		if ($total > 0) {
 
     ?>
		    <h3><?php echo CBE1_BALANCE_TITLE2; ?></h3>

            <table align="center" class="btb" width="100%" border="0" cellspacing="0" cellpadding="3">
              <tr>
				<th width="15%"><?php echo CBE1_BALANCE_DATE; ?></th>
				<th width="50%"><?php echo CBE1_BALANCE_STORE; ?></th>
				<th>Order #</th>
				<th>Amount</th>
                <th width="15%"><?php echo CBE1_BALANCE_CASHBACK; ?></th>
                <th width="20%"><?php echo CBE1_BALANCE_STATUS; ?></th>
              </tr>
			<?php while ($row = mysql_fetch_array($result)) { $cc++; ?>
                <tr class="<?php if (($cc%2) == 0) echo "row_even"; else echo "row_odd"; ?>">
                  <td valign="middle" align="center"><?php echo $row['date_created']; ?></td>
                  <td valign="middle" align="center"><?php echo ($row['retailer'] != "") ? $row['retailer'] : "-----"; ?></td>
                  <td valign="middle" align="center"><?php echo $row['transaction_id']; ?></td>
                  <td valign="middle" align="center"><?php echo $row['transaction_amount']; ?></td>
                  
                  <td valign="middle" align="center"><?php echo DisplayMoney($row['amount']); ?></td>
                  <td valign="middle" align="center">
					<?php
							switch ($row['status'])
							{
								case "confirmed":	echo "<span class='confirmed_status'>".STATUS_CONFIRMED."</span>"; break;
								case "pending":		echo "<span class='pending_status'>".STATUS_PENDING."</span>"; break;
								case "declined":	echo "<span class='declined_status'>".STATUS_DECLINED."</span>"; break;
								case "failed":		echo "<span class='failed_status'>".STATUS_FAILED."</span>"; break;
								case "request":		echo "<span class='request_status'>".STATUS_REQUEST."</span>"; break;
								case "paid":		echo "<span class='paid_status'>".STATUS_PAID."</span>"; break;
								default: echo "<span class='payment_status'>".$row['status']."</span>"; break;
							}

							if ($row['status'] == "declined" && $row['reason'] != "")
							{
								echo " <div class='cashbackengine_tooltip'><img src='".SITE_URL."images/info.png' align='absmiddle' /><span class='tooltip'>".$row['reason']."</span></div>";
							}
					?>
				  </td>
                </tr>
			<?php } ?>
           </table>

     <?php } ?>
<?php require_once ("inc/footer.inc.php"); ?>