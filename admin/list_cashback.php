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
		if(!$_POST['no_of_days'])
		{
			$errs[] = "Please enter the number of days";
		}
		elseif(!is_numeric($_POST['no_of_days']))
		{
			$errs[] = "Number of days should be numeric";
		}
		// Uncomment the following to accept the back dates before
		
		/*elseif($_POST['no_of_days'] < 30)
		{
			$errs[] = "Number of days should be more than 30";
		}*/
		
		$select_option = mysql_real_escape_string(getPostParameter('select_option'));
		
		if(count($errs)==0)
		{
			$no_days = mysql_real_escape_string(getPostParameter('no_of_days'));
			define('SECONDS_PER_DAY', 86400);
			
			$back_date = date('Y-m-d', time() - $no_days * SECONDS_PER_DAY);
			
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
			
			$query = "SELECT 
						u.user_id,
						u.username,
						u.fname,
						t.retailer,
						t.transaction_date,
						sum(t.amount) AS total_amount 
						FROM cashbackengine_transactions AS t 
						INNER JOIN 
						cashbackengine_users AS u 
						ON u.user_id = t.user_id 
						WHERE t.payment_type='cashback' and t.transaction_date < '$back_date'  
						$where
						GROUP BY u.username ";
			
			$result_retailers = smart_mysql_query($query);
			
		}
		
	}
	
	$title = "List Retailers Cashback";
	require_once ("inc/header.inc.php");
	
?>

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
	<table>
		<form method='post'>
			<tr class="showResultsForm">
				<td>Show Transaction happened atleast X days back:</td>
				<td><input class="noOfDays" type='text' name='no_of_days' placeholder="Enter no. of days, e.g. 3" value='<?php echo $no_days;?>'></td>
				<td>
				<?php $select_option = mysql_real_escape_string(getPostParameter('select_option'));?>				
					<select id='select_option' name ='select_option'>
						<option value="showall" <?php if($select_option=='showall'){echo "selected";}?>>Show All</option>
						<option value="paid" <?php if($select_option=='paid'){echo "selected";}?>>Paid</option>
						<option value="unpaid" <?php if($select_option=='unpaid'){echo "selected";}?>>Unpaid</option>
					</select>			
					<input type='hidden' name='action' value='show'>
					<input type='submit' value='Show' class="showResults">
				</td>
			</tr>
		</form>
	</table>
	
	<?php if((isset($result_retailers) && (mysql_num_rows($result_retailers) > 0))){?>
	<table class="retailersCashbackTable">
	<tr>
		<th width="30%" class="alignLeft">User</th>
		<th width="20%" class="alignright">Total Amount</th>
		<th width="50%" class="alignCenter">Click for detail</th>
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
		var no_of_days = "<?php echo mysql_real_escape_string(getPostParameter('no_of_days'))?>";
		var select_option = "<?php echo mysql_real_escape_string(getPostParameter('select_option'))?>";
		var currentParentTr = $(this).parents('tr').eq(0);
		var hasTransactions = currentParentTr.next('tr.transactions').length;
		var currentElem = $(this);
		currentElem.hide();
		currentParentTr.find('.transactionLoadingImg').show();
		
		if(hasTransactions){			
			currentParentTr.next().find('div.innerData').hide().slideDown(AnimationSpeed);			
			currentParentTr.find('.transactionLoadingImg').hide();
			currentParentTr.find('.hide_transactions').show();
		}else{
			var user_id = $(this).attr("u_id");
			var t_date = $(this).attr("t_date");
			
			$.ajax({
				type: "POST",
				url: "<?php echo SITE_URL.'admin/list_cashback_detail.php';?>",
				data: { user_id: user_id , t_date: t_date,no_of_days : no_of_days, select_option: select_option},
				success: function(response) { 										
					currentParentTr.after('<tr class="transactions"><td colspan="4"><div class="innerData">'+response+'</div></td></tr>');
					currentParentTr.next().find('div.innerData').hide().slideDown(AnimationSpeed);					
					if(currentParentTr.hasClass("even"))
						{
							currentParentTr.next().addClass("even");
						}					
					currentParentTr.find('.transactionLoadingImg').hide();
					currentParentTr.find('.hide_transactions').show();
		        }
			});
		}
	})

	$(".hide_transactions").click(function(e){
		var currentParentTr = $(this).parents('tr').eq(0);
		var currentElem = $(this);
		currentElem.hide();
		currentParentTr.find('.show_transactions').show();
		currentParentTr.next().find('div.innerData').show().slideUp(AnimationSpeed);		
	});
})
</script>

<?php require_once ("inc/footer.inc.php"); ?>