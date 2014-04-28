<?php
require_once("../inc/config.inc.php");
require_once("./inc/admin_funcs.inc.php");

if(isset($_POST['update_transaction_id']) && $_POST['update_transaction_id']!="")
{
	$t_id = $_POST['update_transaction_id'];
	$query = smart_mysql_query("UPDATE cashbackengine_transactions SET status='confirmed', updated=NOW() WHERE transaction_id=$t_id");
}

if(isset($_POST['user_id']) && ($_POST['user_id']!=""))
{
	$user_id = $_POST['user_id'];
	$date = $_POST['t_date']; 
	$back_date = $_POST['back_date'];
	$no_of_days = $_POST['no_of_days'];
	$select_option = $_POST['select_option'];
	
	$no_days = $no_of_days;
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
				t.transaction_id,
				t.transaction_date,
				t.retailer,
				t.status,
				t.amount 
					FROM cashbackengine_transactions 
					AS t INNER JOIN cashbackengine_users AS u ON u.user_id = t.user_id 
				WHERE u.user_id='$user_id' and t.payment_type='cashback' and t.transaction_date < '$back_date' $where";
	
	$result_detail = smart_mysql_query($query);

	if((isset($result_detail) && (mysql_num_rows($result_detail) > 0)))
	{	?>
		<table class="retailersCashbackTable1">
			<tr>
				<th width="35%">Retailer</th>
				<th width="16%" class="alignRight">Amount</th>
				<th width="32%" class="alignCenter">Date of transaction</th>
				<th width="17%" class="alignCenter">Mark as paid</th>
			</tr>
			<?php 
			while($row = mysql_fetch_assoc($result_detail))
			{
				$status = $row['status'];
				if($status == 'confirmed')
				{
					$style_for_update_link = 'display:none;';
					$style_for_paid_link = '';
				}
				elseif($status != 'confirmed')
				{
					$style_for_update_link = '';
					$style_for_paid_link = 'display:none;';
				}
				echo '<tr>
						<td>'.$row['retailer'].'</td>
						<td class="alignRight">'.$row['amount'].'</td>
						<td class="alignCenter">'.$row['transaction_date']."</td>
						<td class='alignCenter'>
							<a href='#' style='$style_for_update_link' class='update_link' r_id=".$row['transaction_id'].">Update</a>
							<span class='show_paid' style='$style_for_paid_link'>PAID</span>
							<img class='updateLoadingImg' height='20' width='20' style='display:none;' src='https://www.theratchetshop.com/skin/frontend/default/default/images/ajaxcart/loading.gif' />
						</td>
					</tr>";
			}
			?>
		</table>
	<?php 
	}
}?>

<script>
$(".update_link").click(function(e){
	var transaction_id = $(this).attr('r_id');
	var current_element = $(this);
	var currentParentT = $(this).parents('td').eq(0);

	current_element.hide();
	currentParentT.find(".updateLoadingImg").show();
	
	$.ajax({
		type: "POST",
		url: "http://localhost:86/admin/list_cashback_detail.php",
		data: { update_transaction_id: transaction_id },
		success: function(response) { 
			currentParentT.find(".updateLoadingImg").hide();
			currentParentT.find(".show_paid").show();
        }
	});
	
});	

</script>