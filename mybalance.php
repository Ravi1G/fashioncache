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

<script src="<?php echo SITE_URL;?>js/footable.js" type="text/javascript"></script>
<script type="text/javascript">
    $(function() {
      	$('.footable').footable();
    });
</script>

<?php 
	//$paid = GetUserBalance($userid);
	//$pending = GetPendingBalance();
	
	$query_paid ="SELECT SUM(amount) AS total FROM cashbackengine_transactions WHERE user_id='".(int)$userid."' AND payment_type='cashback' AND status='confirmed'";
	$result_paid = smart_mysql_query($query_paid);
	$total_paid = mysql_fetch_assoc($result_paid);
	$total_paid = round($total_paid['total'],2);
	
	$query_pending = "SELECT SUM(amount) AS total FROM cashbackengine_transactions WHERE user_id='".(int)$userid."' AND payment_type='cashback' AND status='pending'";
	$result_pending = smart_mysql_query($query_pending);
	$total_pending = mysql_fetch_assoc($result_pending);
	$total_pending = round($total_pending['total'],2);
	
	$total = number_format($total_paid + $total_pending,2);
	$total_pending = number_format($total_pending,2);
	$total_paid = number_format($total_paid,2);
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
			<div class="responsiveBalanceHistory">
			<div class="balanceContainer greenColored">
				<div class="heading">Paid Amount</div>
				<div class="balanceSection">					
					<div class="balanceSectionHolder">
						<div class="fl dollarIcon">$</div>
						<?php $paid_amount = explode( '.', $total_paid );?>						
						<div class="fl amountIs">
							<?php 
								echo $paid_amount[0];
							?>
						</div>
						<div class="fl smallAmountIs">
							<?php
							 if($paid_amount[1]!="") 
									echo '.'.$paid_amount[1];?>
						</div>
						<div class="cb"></div>
					</div>
				</div>
			</div>
			
			<div class="balanceContainer  orangeColored">
				<div class="heading">Pending Amount</div>
				<div class="balanceSection">					
					<div class="balanceSectionHolder">
						<div class="fl dollarIcon">$</div>
						<?php $pending_amount = explode('.',$total_pending);?>
						<div class="fl amountIs"><?php echo $pending_amount[0];?></div>
						<div class="fl smallAmountIs">
							<?php  
							 if($pending_amount[1]!="") 
									echo '.'.$pending_amount[1];?>
						</div>
						<div class="cb"></div>
					</div>
				</div>
			</div>
			
			<div class="balanceContainer  blackColored">
				<div class="heading">Total Amount</div>
				<div class="balanceSection">					
					<div class="balanceSectionHolder">
						<?php $total_amount = explode('.',$total);?>
						<div class="fl dollarIcon">$</div>
						<div class="fl amountIs"><?php echo $total_amount[0]; ?></div>
						<div class="fl smallAmountIs">
							<?php
								if($total_amount[1]!="") 
									echo '.'.$total_amount[1];
							?>
						</div>
						<div class="cb"></div>
					</div>
				</div>
			</div>
			<div class="cb"></div>
		</div>
		
		 <?php
			$cc = 0;
	
			$query = "SELECT *, DATE_FORMAT(created, '%e %b %Y') AS date_created, DATE_FORMAT(updated, '%e %b %Y') AS updated_date FROM cashbackengine_transactions WHERE user_id='$userid' AND payment_type='cashback' AND program_id!='0' AND status!='unknown' ORDER BY created DESC";
			$result = smart_mysql_query($query);
			$total = mysql_num_rows($result);
	
			if ($total > 0) { 
     	 ?>		
			<br/>
			<h1><?php echo CBE1_BALANCE_TITLE2; ?></h1>			
			
			
			<table class="standardTable footable">
				<thead>
					<tr>
						<th data-hide="phone,tablet" width="12.66%" class="firstCell"><span class="onResponsiveCell">Order No.</span></th>
						<th data-class="expand" class="onResponsiveCell3" width="21.66%"><?php echo CBE1_BALANCE_STORE;?></th>
						<th data-hide="phone,tablet" width="15.66%"><span class="onResponsiveCell"><?php echo CBE1_BALANCE_DATE;?></span></th>		
						<th width="16.66%" class="onResponsiveCell2">Amount</th>
						<th data-hide="phone,tablet" width="16.66%"><span class="onResponsiveCell"><?php echo CBE1_BALANCE_CASHBACK;?></span></th>
						<th data-hide="phone" width="16.66%" class="lastCell last"><span class="onResponsiveCell"><?php echo CBE1_BALANCE_STATUS;?></span></th>
					</tr>
				</thead>
				<tbody>
					<?php while ($row = mysql_fetch_array($result)) { $cc++; ?>
						<tr>
						  <td valign="middle" align="center"><?php echo $row['reference_id']; ?></td>
						  <td valign="middle" align="center" class="onResponsiveCell1"><?php echo ($row['retailer'] != "") ? $row['retailer'] : "--"; ?></td>
						  <td valign="middle" align="center"><?php echo $row['date_created']; ?></td>
						  <td valign="middle" align="right" class="numeric"><span class="amountAlignment"><?php echo DisplayMoney($row['transaction_amount']); ?></span></td>
						  <td valign="middle" align="right" class="numeric"><span class="amountAlignment"><?php echo DisplayMoney($row['amount']); ?></span></td>
						  <td valign="middle" align="center">

							<?php
									switch ($row['status'])
									{
										case "confirmed":	echo "<span class='confirmed_status'>paid</span>"; break;
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
				</tbody>
			</table>
			  <?php } ?>
			 
			
		</div>
		<div class="cb"></div>
	</div>	



		

<?php require_once ("inc/footer.inc.php"); ?>