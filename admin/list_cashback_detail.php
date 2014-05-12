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
	$start_date = mysql_real_escape_string(getPostParameter('start_date'));
	$end_date = mysql_real_escape_string(getPostParameter('end_date'));
	
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
				t.amount,
				n.network_name 
					FROM cashbackengine_transactions AS t 
						INNER JOIN cashbackengine_users AS u ON u.user_id = t.user_id
						INNER JOIN cashbackengine_affnetworks AS n ON t.network_id = n.network_id 
				WHERE u.user_id='$user_id' and t.payment_type='cashback' and 
						t.transaction_date 
						between '$start_date' and '$end_date' $where";
	
	$result_detail = smart_mysql_query($query);

	if((isset($result_detail) && (mysql_num_rows($result_detail) > 0)))
	{	?>
		<table class="retailersCashbackTable1">
			<tr>
				<th width="35%">Retailer</th>
				<th width="10%" class="alignRight">Amount</th>
				<th width="32%" class="alignCenter">Date of transaction</th>
				<th>Platform</th>
				<th width="10%" class="alignCenter">Mark as paid</th>
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
				
				$loading_img = SITE_URL.'admin/images/loading.gif';
				echo '<tr>
						<td>'.$row['retailer'].'</td>
						<td class="alignRight">'.$row['amount'].'</td>
						<td class="alignCenter">'.$row['transaction_date'].'</td>
						<td>'.$row['network_name']."</td>
						<td class='alignCenter'>
							<a href='#' style='$style_for_update_link' class='update_link' r_id=".$row['transaction_id'].">Update</a>
							<span class='show_paid' style='$style_for_paid_link'>PAID</span>
							<img class='updateLoadingImg' height='20' width='20' style='display:none;' src= '$loading_img'/>
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
		url: "<?php echo SITE_URL.'admin/list_cashback_detail.php';?>",
		data: { update_transaction_id: transaction_id },
		success: function(response) { 
			currentParentT.find(".updateLoadingImg").hide();
			currentParentT.find(".show_paid").show();
        }
	});
	
});	

</script>