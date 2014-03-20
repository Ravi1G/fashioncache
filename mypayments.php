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

	$query = "SELECT *, DATE_FORMAT(created, '%e %b %Y') AS date_created, DATE_FORMAT(process_date, '%e %b %Y') AS process_date FROM cashbackengine_transactions WHERE user_id='$userid' AND program_id='' AND status!='unknown' ORDER BY created DESC";
	$result = smart_mysql_query($query);
	$total = mysql_num_rows($result);

	
	///////////////  Page config  ///////////////
	$PAGE_TITLE = CBE1_PAYMENTS_TITLE;

	require_once ("inc/header.inc.php");

?>

	<h1><?php echo CBE1_PAYMENTS_TITLE; ?></h1>

	<?php if ($total > 0) { ?>

            <table align="center" class="btb" width="100%" border="0" cellspacing="0" cellpadding="3">
              <tr>
                <th width="15%"><?php echo CBE1_PAYMENTS_DATE; ?></th>
				<th width="17%"><?php echo CBE1_PAYMENTS_ID; ?></th>
                <th width="25%"><?php echo CBE1_PAYMENTS_TYPE; ?></th>
                <th width="15%"><?php echo CBE1_PAYMENTS_STATUS; ?></th>
                <th width="17%"><?php echo CBE1_PAYMENTS_PDATE; ?></th>
				<th width="17%"><?php echo CBE1_PAYMENTS_AMOUNT; ?></th>
				<th width="7%"></th>
              </tr>
			<?php while ($row = mysql_fetch_array($result)) { $cc++; ?>
                <tr class="<?php if (($cc%2) == 0) echo "row_even"; else echo "row_odd"; ?>">
                  <td valign="middle" align="center"><?php echo $row['date_created']; ?></td>
                  <td valign="middle" align="center"><a href="<?php echo SITE_URL; ?>mypayments.php?id=<?php echo $row['transaction_id']; ?>"><?php echo $row['reference_id']; ?></a></td>
                  <td valign="middle" align="center">
					<?php
							switch ($row['payment_type'])
							{
								case "Cashback":				echo PAYMENT_TYPE_CASHBACK; break;
								case "Withdrawal":				echo PAYMENT_TYPE_WITHDRAWAL; break;
								case "Refer a Friend Bonus":	echo PAYMENT_TYPE_FBONUS; break;
								case "Sign Up Bonus":			echo PAYMENT_TYPE_SBONUS; break;
								default:						echo $row['payment_type']; break;
							}
					?>
				  </td>
                  <td nowrap="nowrap" valign="middle" align="center">
					<?php
							switch ($row['status'])
							{
								case "confirmed":	echo "<span class='confirmed_status'>".STATUS_CONFIRMED."</span>"; break;
								case "pending":		echo "<span class='pending_status'>".STATUS_PENDING."</span>"; break;
								case "declined":	echo "<span class='declined_status'>".STATUS_DECLINED."</span>"; break;
								case "failed":		echo "<span class='failed_status'>".STATUS_FAILED."</span>"; break;
								case "request":		echo "<span class='request_status'>".STATUS_REQUEST."</span>"; break;
								case "paid":		echo "<span class='paid_status'>".STATUS_PAID."</span>"; break;
								default:			echo "<span class='payment_status'>".$row['status']."</span>"; break;
							}

							if ($row['status'] == "declined" && $row['reason'] != "")
							{
								echo " <div class='cashbackengine_tooltip'><img src='".SITE_URL."images/info.png' align='absmiddle' /><span class='tooltip'>".$row['reason']."</span></div>";
							}
					?>
				  </td>
				  <td valign="middle" align="center"><?php echo $row['process_date']; ?></td>
                  <td valign="middle" align="center"><?php echo DisplayMoney($row['amount']); ?></td>
				  <td valign="middle" align="center"><a href="<?php echo SITE_URL; ?>mypayments.php?id=<?php echo $row['transaction_id']; ?>"><img src="<?php echo SITE_URL; ?>images/icon_view.png" /></a></td>
                </tr>
			<?php } ?>
           </table>


		<?php
	
		// payment details //
		if (isset($_GET['id']) && is_numeric($_GET['id']))
		{
			$transaction_id = (int)$_GET['id'];
			$payment_result = smart_mysql_query("SELECT *, DATE_FORMAT(created, '%e %b %Y %h:%i %p') AS date_created, DATE_FORMAT(process_date, '%e %b %Y %h:%i %p') AS process_date FROM cashbackengine_transactions WHERE transaction_id='$transaction_id' AND user_id='$userid' AND program_id='' AND status<>'unknown' LIMIT 1");
			
			if (mysql_num_rows($payment_result) > 0)
			{
				$payment_row = mysql_fetch_array($payment_result);
		?>

		 <h3><?php echo CBE1_PAYMENTS_DETAILS; ?></h3>
		 
		 <div class="payment_details">
		 <table width="500" cellpadding="5" cellspacing="3" border="0">
            <tr>
              <td width="110" nowrap="nowrap" valign="middle" align="left" class="tb1"><?php echo CBE1_PAYMENTS_ID; ?>:</td>
              <td align="left" valign="middle"><?php echo $payment_row['reference_id']; ?></td>
            </tr>
           <tr>
            <td nowrap="nowrap" valign="middle" align="left" class="tb1"><?php echo CBE1_PAYMENTS_TYPE; ?>:</td>
            <td align="left" valign="middle">
				<?php
						switch ($payment_row['payment_type'])
						{
							case "Cashback":				echo PAYMENT_TYPE_CASHBACK; break;
							case "Withdrawal":				echo PAYMENT_TYPE_WITHDRAWAL; break;
							case "Refer a Friend Bonus":	echo PAYMENT_TYPE_FBONUS; break;
							case "Sign Up Bonus":			echo PAYMENT_TYPE_SBONUS; break;
							default:						echo $payment_row['payment_type']; break;
						}
				?>
			</td>
          </tr>
		<?php if ($payment_row['payment_type'] == "Withdrawal" && $payment_row['payment_method'] != "") { ?>
          <tr>
            <td nowrap="nowrap" valign="middle" align="left" class="tb1"><?php echo CBE1_PAYMENTS_METHOD; ?>:</td>
            <td align="left" valign="middle">
					<?php if ($payment_row['payment_method'] == "paypal") { ?><img src="<?php echo SITE_URL; ?>images/icon_paypal.png" align="absmiddle" />&nbsp;<?php } ?>
					<?php echo GetPaymentMethodByID($payment_row['payment_method']); ?>
			</td>
          </tr>
		<?php } ?>
		<?php if ($payment_row['payment_details'] != "") { ?>
           <tr>
            <td nowrap="nowrap" valign="middle" align="left" class="tb1"><?php echo CBE1_PAYMENTS_DETAILS; ?>:</td>
            <td align="left" valign="middle"><?php echo $payment_row['payment_details']; ?></td>
          </tr>
		 <?php } ?>
          <tr>
            <td nowrap="nowrap" valign="middle" align="left" class="tb1"><?php echo CBE1_PAYMENTS_AMOUNT; ?>:</td>
            <td align="left" valign="middle"><?php echo DisplayMoney($payment_row['amount']); ?></td>
          </tr>
          <tr>
            <td nowrap="nowrap" valign="middle" align="left" class="tb1"><?php echo CBE1_PAYMENTS_DATE; ?>:</td>
            <td align="left" valign="middle"><?php echo $payment_row['date_created']; ?></td>
          </tr>
		  <?php if ($payment_row['process_date'] != "") { ?>
          <tr>
            <td nowrap="nowrap" valign="middle" align="left" class="tb1"><?php echo CBE1_PAYMENTS_PDATE; ?>:</td>
            <td align="left" valign="middle"><?php echo $payment_row['process_date']; ?></td>
          </tr>
		  <?php } ?>
          <tr>
            <td nowrap="nowrap" valign="middle" align="left" class="tb1"><?php echo CBE1_PAYMENTS_STATUS; ?>:</td>
            <td align="left" valign="middle">
					<?php
							switch ($payment_row['status'])
							{
								case "confirmed":	echo "<span class='confirmed_status'>".STATUS_CONFIRMED."</span>"; break;
								case "pending":		echo "<span class='pending_status'>".STATUS_PENDING."</span>"; break;
								case "declined":	echo "<span class='declined_status'>".STATUS_DECLINED."</span>"; break;
								case "failed":		echo "<span class='failed_status'>".STATUS_FAILED."</span>"; break;
								case "request":		echo "<span class='request_status'>".STATUS_REQUEST."</span>"; break;
								case "paid":		echo "<span class='paid_status'>".STATUS_PAID."</span>"; break;
								default:			echo "<span class='payment_status'>".$payment_row['status']."</span>"; break;
							}

							if ($payment_row['status'] == "declined" && $payment_row['reason'] != "")
							{
								echo " <div class='cashbackengine_tooltip'><img src='".SITE_URL."images/info.png' align='absmiddle' /><span class='tooltip'>".$payment_row['reason']."</span></div>";
							}
					?>				
			</td>
          </tr>
          </table>
		  </div>
		<?php
			}
		} // end payment details
		?>

		<p align="center"><a class="goback" href="<?php echo SITE_URL; ?>mybalance.php"><?php echo CBE1_GO_BACK; ?></a></p>

	<?php }else{ ?>
		<p align="center">
			<?php echo CBE1_PAYMENTS_NO; ?><br/><br/>
			<a class="goback" href="#" onclick="history.go(-1);return false;"><?php echo CBE1_GO_BACK; ?></a>
		</p>
	<?php } ?>

<?php require_once ("inc/footer.inc.php"); ?>