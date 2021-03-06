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


	if (isset($_GET['id']) && is_numeric($_GET['id']))
	{
		$id = (int)$_GET['id'];

		$query = "SELECT t.*, DATE_FORMAT(t.created, '%e %b %Y %h:%i %p') AS payment_date, DATE_FORMAT(t.updated, '%e %b %Y %h:%i %p') AS updated_date, DATE_FORMAT(process_date, '%e %b %Y %h:%i %p') AS processed_date, u.username, u.email, u.fname, u.lname FROM cashbackengine_transactions t, cashbackengine_users u WHERE t.transaction_id='$id' AND t.user_id=u.user_id";
		$result = smart_mysql_query($query);
		$total = mysql_num_rows($result);
	}


	$title = "Payment Details";
	require_once ("inc/header.inc.php");

?>
    
    
     <h2>Payment Details</h2>

		<?php if ($total > 0) { 

				$row = mysql_fetch_array($result);
		 ?>
            <table width="450" bgcolor="#F9F9F9" style="border-radius: 7px;" cellpadding="3" cellspacing="5" border="0" align="center">
              <tr>
                <td width="200" valign="middle" align="right" class="tb1">Payment ID:</td>
                <td valign="top"><?php echo $row['transaction_id']; ?></td>
              </tr>
              <tr>
                <td nowrap="nowrap" valign="middle" align="right" class="tb1">Reference ID:</td>
                <td valign="top"><?php echo $row['reference_id']; ?></td>
              </tr>
              <tr>
                <td valign="middle" align="right" class="tb1">Username:</td>
                <td valign="top"><?php echo $row['username']; ?></td>
              </tr>
              <tr>
                <td valign="middle" align="right" class="tb1">Member:</td>
                <td valign="top"><a href="user_details.php?id=<?php echo $row['user_id']; ?>" class="user"><?php echo $row['fname']." ".$row['lname']; ?></a></td>
              </tr>
              <tr>
                <td valign="middle" align="right" class="tb1">Email:</td>
                <td valign="top"><a href="mailto:<?php echo $row['email']; ?>"><?php echo $row['email']; ?></a></td>
              </tr>
              <tr>
                <td valign="middle" align="right" class="tb1">Payment type:</td>
                <td valign="top"><?php echo $row['payment_type']; ?></td>
              </tr>
			  <?php if ($row['payment_type'] == "Withdrawal" && $row['payment_method'] != "") { ?>
              <tr>
                <td valign="middle" align="right" class="tb1">Payment method:</td>
                <td valign="top">
					<?php if ($row['payment_method'] == "paypal") { ?><img src="images/paypal.gif" align="absmiddle" />&nbsp;<?php } ?>
					<?php echo GetPaymentMethodByID($row['payment_method']); ?>
                </td>
              </tr>
			  <?php } ?>
			  <?php if ($row['payment_details'] != "") { ?>
              <tr>
                <td valign="middle" align="right" class="tb1">Payment Details:</td>
                <td valign="top"><?php echo $row['payment_details']; ?></td>
              </tr>
			  <?php } ?>
              <tr>
                <td valign="middle" align="right" class="tb1">Amount:</td>
                <td valign="top"><span class="amount"><?php echo DisplayMoney($row['amount']); ?></span></td>
              </tr>
              <tr>
                <td valign="middle" align="right" class="tb1">Status:</td>
                <td valign="top">
					<?php
						switch ($row['status'])
						{
							case "confirmed": echo "<span class='confirmed_status'>confirmed</span>"; break;
							case "pending": echo "<span class='pending_status'>pending</span>"; break;
							case "declined": echo "<span class='declined_status'>declined</span>"; break;
							case "failed": echo "<span class='failed_status'>failed</span>"; break;
							case "request": echo "<span class='request_status'>awaiting approval</span>"; break;
							case "paid": echo "<span class='paid_status'>paid</span>"; break;
							default: echo "<span class='payment_status'>".$row['status']."</span>"; break;
						}
					?>
				</td>
              </tr>
			  <?php if ($row['status'] == "declined" && $row['reason'] != "") { ?>
              <tr>
                <td valign="middle" align="right" class="tb1">Reason:</td>
                <td style="color: #EB0000; background: #FFEBEB; border-left: 2px #FF0000 solid" valign="top"><?php echo $row['reason']; ?></td>
              </tr>
			  <?php } ?>
              <tr>
                <td valign="middle" align="right" class="tb1">Date Added:</td>
                <td valign="top"><?php echo $row['payment_date']; ?></td>
              </tr>
			  <?php if ($row['updated_date'] != "" && ($row['updated_date'] != $row['payment_date'])) { ?>
              <tr>
                <td valign="middle" align="right" class="tb1">Date Updated:</td>
                <td valign="top"><?php echo $row['updated_date']; ?></td>
              </tr>
			  <?php } ?>
			  <?php if ($row['payment_type'] == "Withdraw" && ($row['status'] == "declined" || $row['status'] == "confirmed") && $row['process_date'] != "0000-00-00 00:00:00") { ?>
              <tr>
                <td valign="middle" align="right" class="tb1">Process Date:</td>
                <td valign="top"><?php echo $row['processed_date']; ?></td>
              </tr>
			  <?php } ?>
            <tr>
              <td align="center" colspan="2" valign="bottom">
				<?php if ($row['payment_type'] == "Withdrawal" && $row['status'] == "request") { ?>
					<input type="button" class="submit" name="proceed" value="Proceed Payment" onClick="javascript:document.location.href='payment_process.php?id=<?php echo $row['transaction_id']; ?>'" />
				<?php } ?>
				<input type="button" class="cancel" name="cancel" value="Go Back" onclick="history.go(-1);return false;" />
			  </td>
            </tr>
          </table>
      <?php }else{ ?>
			<p align="center">Sorry, no payment found.<br/><br/><a class="goback" href="#" onclick="history.go(-1);return false;">Go Back</a></p>
      <?php } ?>

<?php require_once ("inc/footer.inc.php"); ?>