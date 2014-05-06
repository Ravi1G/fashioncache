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

<?php 
	$paid = GetUserBalance($userid);
	$pending = GetPendingBalance();
	
	$type = GetCashbackType($paid);
	$paid_without_type = RemoveCashbackType($paid);
	
	$type = GetCashbackType($pending);
	$pending_without_type = RemoveCashbackType($pending);
	
	
	$total = number_format($paid_without_type + $pending_without_type,2);
	$total_without_type = $total;
	$total = '$'.$total;
?>

	<div class="container standardContainer innerRegularPages">		
		<?php 
		/* Left SideBar Content */
		if(isLoggedIn())
		{
			require_once("inc/left_sidebar.php");				
		}
		?>
		<div class="rightAligned flowContent1">		 
			<h1><?php echo CBE1_BALANCE_TITLE; ?></h1>
			
			<div class="balanceContainer  greenColored">
				<div class="heading">Paid Amount</div>
				<div class="balanceSection">					
					<div class="balanceSectionHolder">
						<div class="fl dollarIcon">$</div>
						<?php $paid_amount = explode( '.', $paid_without_type );?>						
						<div class="fl amountIs">
							<?php 
								echo $paid_amount[0];
							?>
						</div>
						<div class="fl smallAmountIs">.<?php echo $paid_amount[1];?></div>
						<div class="cb"></div>
					</div>
				</div>
			</div>
			
			<div class="balanceContainer  orangeColored">
				<div class="heading">Pending Amount</div>
				<div class="balanceSection">					
					<div class="balanceSectionHolder">
						<div class="fl dollarIcon">$</div>
						<?php $pending_amount = explode('.',$pending_without_type);?>
						<div class="fl amountIs"><?php echo $pending_amount[0];?></div>
						<div class="fl smallAmountIs">.<?php echo $pending_amount[1];?></div>
						<div class="cb"></div>
					</div>
				</div>
			</div>
			
			<div class="balanceContainer  blackColored">
				<div class="heading">Total Amount</div>
				<div class="balanceSection">					
					<div class="balanceSectionHolder">
						<?php $total_amount = explode('.',$total_without_type);?>
						<div class="fl dollarIcon">$</div>
						<div class="fl amountIs"><?php echo $total_amount[0]; ?></div>
						<div class="fl smallAmountIs">.<?php echo $total_amount[1];?></div>
						<div class="cb"></div>
					</div>
				</div>
			</div>
			<div class="cb"></div>
		
		
		 <?php
			$cc = 0;
	
			$query = "SELECT *, DATE_FORMAT(created, '%e %b %Y') AS date_created, DATE_FORMAT(updated, '%e %b %Y') AS updated_date FROM cashbackengine_transactions WHERE user_id='$userid' AND program_id!='0' AND status!='unknown' ORDER BY created DESC";
			$result = smart_mysql_query($query);
			$total = mysql_num_rows($result);
	
			if ($total > 0) { 
     	 ?>		
			<br/>
			<h1><?php echo CBE1_BALANCE_TITLE2; ?></h1>			
			<table class="standardTable">
				<thead>
					<tr>
						<th width="12.66%" class="firstCell">Order No.</th>
						<th width="21.66%"><?php echo CBE1_BALANCE_STORE;?></th>
						<th width="15.66%"><?php echo CBE1_BALANCE_DATE;?></th>		
						<th width="16.66%">Amount</th>
						<th width="16.66%"><?php echo CBE1_BALANCE_CASHBACK;?></th>
						<th width="16.66%" class="lastCell last"><?php echo CBE1_BALANCE_STATUS;?></th>
					</tr>
				</thead>
				<tbody>
					<?php while ($row = mysql_fetch_array($result)) { $cc++; ?>
						<tr>
						  <td valign="middle" align="center"><?php echo $row['reference_id']; ?></td>
						  <td valign="middle" align="center"><?php echo ($row['retailer'] != "") ? $row['retailer'] : "--"; ?></td>
						  <td valign="middle" align="center"><?php echo $row['date_created']; ?></td>
						  <td valign="middle" align="center"><?php echo $row['transaction_amount']; ?></td>
						  <td valign="middle" align="center"><?php echo DisplayMoney($row['amount']); ?></td>
						  <td valign="middle" align="center">
						  <div class="statusCenter">
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
							</div>
						  </td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
			  <?php } ?>
			 
			
		</div>
		<div class="cb"></div>
	</div>	

		

<?php require_once ("inc/footer.inc.php"); ?>