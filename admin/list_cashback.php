<?php
/*******************************************************************\
 * CashbackEngine v2.1
 * http://www.CashbackEngine.net
 *
 * Copyright (c) 2010-2014 CashbackEngine Software. All rights reserved.
 * ------------ CashbackEngine IS NOT FREE SOFTWARE --------------
\*******************************************************************/

	set_time_limit(120);
	session_start();
	require_once("../inc/adm_auth.inc.php");
	require_once("../inc/config.inc.php");
	require_once("./inc/admin_funcs.inc.php");
	
	
	if(isset($_POST['action']) && ($_POST['action']=='show'))
	{
		//Validations
		$errs = array();
		if(!$_POST['start_date'] || !$_POST['end_date'])
		{
			$errs[] = "Please Select Starting-Date & Ending-Date";
		}
		
		$select_option = mysql_real_escape_string(getPostParameter('select_option'));
		
		if(count($errs)==0)
		{
			/*$no_days = mysql_real_escape_string(getPostParameter('no_of_days'));
			define('SECONDS_PER_DAY', 86400);
			
			$back_date = date('Y-m-d', time() - $no_days * SECONDS_PER_DAY);*/
			
			$start_date = mysql_real_escape_string(getPostParameter('start_date'));
			$end_date = mysql_real_escape_string(getPostParameter('end_date'));
			if($select_option == 'showall')
			{
				$where = "";
			}
			elseif ($select_option == 'paid')
			{
				$where = "and t.status = 'confirmed' ";
			}
			elseif($select_option == 'unpaid')
			{
				$where = "and t.status = 'pending' ";
			}
		//SELECT * FROM logs WHERE date 
//			STR_TO_DATE(date, '%Y-%m-%d') 
//between STR_TO_DATE(from_date, '%Y-%m-%d') and STR_TO_DATE(to_date, '%m/%d/%Y')
	
			$query = "SELECT 
						u.user_id,
						u.username,
						u.fname,
						t.retailer,
						t.transaction_date,
						sum(t.amount) AS total_amount,
						c.cashback_method,
						c.paypal_email,
						c.address,
						c.city,
						c.state,
						c.country,
						c.zip,
						c.venmo_username,
						country.name AS country_name
						FROM cashbackengine_transactions AS t 
						INNER JOIN 
						cashbackengine_users AS u 
						ON u.user_id = t.user_id
						LEFT JOIN
						cashbackengine_cashback_method AS c
						ON u.user_id = c.user_id
						LEFT JOIN
						cashbackengine_countries AS country
						ON c.country = country.country_id
						WHERE t.payment_type='cashback' and 
						t.transaction_date 
						between '$start_date' and '$end_date'  
						$where
						GROUP BY u.username ";

			$result_retailers = smart_mysql_query($query);
		}
	}
	
	$title = "User Transactions";
	require_once ("inc/header.inc.php");
	
?>
<link href="css/jquery.qtip.css" rel="stylesheet" type="text/css"/>
<!-- Show the errors pending -->
<?php 
	if(count($errs)>0)
	{
		foreach( $errs as $error)
		{
			?>
			<div class="error_box">
				<?php echo $error;?>		
			</div>
			<?php 
		}
	}
	
?>	
  <h2><?php echo $title;?></h2>
	<table>
		<form method='post'>
			<tr class="showResultsForm">
				<!-- <td>Show Transaction happened atleast X days back:</td>-->
				<!-- <td><input class="noOfDays" type='text' name='no_of_days' placeholder="Enter no. of days, e.g. 3" value='<?php echo $no_days;?>'></td>-->
				
				<td valign="middle"><input type="text" name="start_date" id="start_date" value="<?php echo getPostParameter('start_date'); ?>" size="12"  maxlength="12" class="textbox" placeholder="Select Start Date"/></td>
				<td valign="middle"><input type="text" name="end_date" id="end_date" value="<?php echo getPostParameter('end_date'); ?>" size="12"  maxlength="12" class="textbox" placeholder="Select End Date"/></td>
				<td>
				<?php $select_option = mysql_real_escape_string(getPostParameter('select_option'));?>				
					<select id='select_option' name ='select_option'>
						<option value="showall" <?php if($select_option=='showall'){echo "selected";}?>>Show All</option>
						<option value="paid" <?php if($select_option=='paid'){echo "selected";}?>>Paid</option>
						<option value="unpaid" <?php if($select_option=='unpaid'){echo "selected";}?>>Unpaid</option>
					</select>			
					<input type='hidden' name='action' value='show'>
					<input type='submit' value=' Show ' class="showResults submit">
				</td>
			</tr>
		</form>
	</table>
	
	
	<?php if((isset($result_retailers) && (mysql_num_rows($result_retailers) > 0))){?>
	<table class="retailersCashbackTable">
	<tr>
		<th width="30%" class="alignLeft">User</th>
		<th width="20%" class="alignright">Total Amount</th>
		<th width="11%" class="alignCenter">Payment Method</th>
		<th width="39%" class="alignCenter">Click for detail</th>
	</tr>
		<?php 
		$i = 1;
		while($rows = mysql_fetch_assoc($result_retailers))
			{
				?>
					<tr <?php if($i%2){ ?>class="even" <?php } else {?> class="odd" <?php } ?>>
						<td>
							<?php echo $rows['fname'];?>
						</td>
						<td class="alignRight">
							<?php echo $rows['total_amount'];?>
						</td>
						<td align="center" valign="middle">
						
						<a href="#" class="payment_method paymentMethodIns" user_id="<?php echo $rows['user_id']?>">
							<?php echo $rows['cashback_method'];?>
						</a>
						<!--  For Venmo  -->
						<?php if($rows['cashback_method']=='venmo'){?>
						<div class="paymentMethodDataContainer">
							<div class="blockTitle">Venmo User Detail</div>
							<div class="blockDescription">
								<div><b>Username:</b> <?php echo $rows['venmo_username'];?></div>
							</div>
						</div>
						<?php }?>
						
						<!--  For Paypal  -->
						<?php if($rows['cashback_method']=='paypal'){?>
						<div class="paymentMethodDataContainer">
							<div class="blockTitle">PayPal User Detail</div>
							<div class="blockDescription">
								<div><b>Email:</b> <?php echo $rows['paypal_email'];?></div>
							</div>
						</div>
						<?php }?>
						
						<!--  For Check  -->
						<?php if($rows['cashback_method']=='check'){?>
						<div class="paymentMethodDataContainer">
							<div class="blockTitle">Check Details</div>
							<div class="blockDescription">
								<table>
									<tr>
										<td><b>Address:</b></td>
										<td><?php echo $rows['address'];?></td>
									</tr>
									<tr>
										<td><b>City:</b></td>
										<td><?php echo $rows['city'];?></td>
									</tr>
									<tr>
										<td><b>State:</b></td>
										<td><?php echo $rows['state'];?></td>
									</tr>
									<tr>
										<td><b>Country:</b></td>
										<td><?php echo $rows['country_name'];?></td>
									</tr>
									<tr>
										<td><b>Zip Code:</b></td>
										<td><?php echo $rows['zip'];?></td>
									</tr>
								</table>								
							</div>
						</div>
						<?php }?>
						</td>
						<td align="center" class="actionCenter">
							<a href="#" class="show_transactions" u_id="<?php echo $rows['user_id'];?>" t_date="<?php echo $rows['transaction_date'];?>">Show Transactions</a>
							<a href="#" style="display:none;" class="hide_transactions">Hide Transactions</a>
							<img class="transactionLoadingImg" height="20" width="20" style="display:none;" src="<?php echo SITE_URL.'admin/images/loading.gif'?>" />
						</td>
					</tr>
			<?php $i++; }
		?>
	</table>
	<?php }?>
<script>
$(function(){
	// Slide Up-Down's Speed
	var AnimationSpeed = 300;
	
	$(".show_transactions").click(function(e){		
		//var no_of_days = "<?php echo mysql_real_escape_string(getPostParameter('no_of_days'))?>";
		var start_date = "<?php echo mysql_real_escape_string(getPostParameter('start_date'))?>";
		var end_date = "<?php echo mysql_real_escape_string(getPostParameter('end_date'))?>";
		var select_option = "<?php echo mysql_real_escape_string(getPostParameter('select_option'))?>";
		var currentParentTr = $(this).parents('tr').eq(0);
		var hasTransactions = currentParentTr.next('tr.transactions').length;
		var currentElem = $(this);
		currentElem.hide();
		currentParentTr.find('.transactionLoadingImg').show();
		
		if(hasTransactions){			
			currentParentTr.next().show();			
			currentParentTr.find('.transactionLoadingImg').hide();
			currentParentTr.find('.hide_transactions').show();
		}else{
			var user_id = $(this).attr("u_id");
			var t_date = $(this).attr("t_date");
			
			$.ajax({
				type: "POST",
				url: "<?php echo SITE_URL.'admin/list_cashback_detail.php';?>",
				data: { user_id: user_id , t_date: t_date,start_date: start_date,end_date: end_date, select_option: select_option},
				success: function(response) { 										
					currentParentTr.after('<tr class="transactions"><td colspan="4"><div class="innerData">'+response+'</div></td></tr>');
					//currentParentTr.next().find('div.innerData');					
					if(currentParentTr.hasClass("even")){
							currentParentTr.next().addClass("even");
					}					
					currentParentTr.find('.transactionLoadingImg').hide();
					currentParentTr.find('.hide_transactions').show();
		        }
			});
		}
	});

	$(".hide_transactions").click(function(e){
		var currentParentTr = $(this).parents('tr').eq(0);
		var currentElem = $(this);
		currentElem.hide();
		currentParentTr.find('.show_transactions').show();
		currentParentTr.next().hide();
	});

	$(".payment_method").click(function(){
		var u_id = $(this).attr("user_id");
		$.colorbox({
		    iframe: true,
		    width: 593,
		    height: 360,
		    opacity: 0.8,
		    scrolling: false,
		    closeButton: true,
		    fixed: true,
		    transition: "none",
		    href : "<?php echo SITE_URL;?>admin/payment_method_popup.php?user_id="+u_id
		});
	});
	$('#start_date').calendricalDate();
    $('#end_date').calendricalDate();
});
</script>
<script src="js/jquery.qtip.min.js" type="text/javascript"></script>
<script type="text/javascript">
	$(function(){
		$('.paymentMethodIns').each(function() { 
			$(this).qtip({ 
				position: {
						my: 'top left',
						at: 'right bottom'
					},
				style: { 
						classes: 'qtip-tipped customQtip'
					},
				content: {		
						text: $(this).next('div')
					}
			});
		});
	});
</script>



<?php require_once ("inc/footer.inc.php"); ?>