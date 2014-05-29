<?php
/*******************************************************************\
 * CashbackEngine v2.1
 * http://www.CashbackEngine.net
 *
 * Copyright (c) 2010-2014 CashbackEngine Software. All rights reserved.
 * ------------ CashbackEngine IS NOT FREE SOFTWARE --------------
\*******************************************************************/

	session_start();
	require_once("../inc/config.inc.php");
	require_once("./inc/parsecsv.inc.php");
	set_time_limit(0);
	
	//Read id of linkshare from the database starts
	$query_nw_id = "SELECT network_id FROM cashbackengine_affnetworks WHERE network_name = 'Linkshare'";
	$rs	= smart_mysql_query($query_nw_id);
	$total = mysql_num_rows($rs);
	if($total > 0)
	{
		$row_nw = mysql_fetch_assoc($rs);
		$network_id = $row_nw['network_id'];
	}//Read id of linkshare from the database ends
	
	if((isset($_GET['start_date'])) && ($_GET['start_date']!="")&& (isset($_GET['end_date'])) && ($_GET['end_date']!=""))
	{
		$start_date = str_replace('-','',$_GET['start_date']);
		$end_date	= str_replace('-','',$_GET['end_date']);
	}
	else {
		//Current date and time
		$current_date = date('Ymd');
		$current_time = date('His');
	
		//Conditions to check and set start and end date for the query
		if(($current_time>000000)&&($current_time<010000))
			{
				$start_date = $current_date - 1;
			}
		else
			{
				$start_date = $current_date;
			}
	
	 	$end_date = $current_date;
	}
	$file_content = file_get_contents('https://reportws.linksynergy.com/downloadreport.php?bdate='.$start_date.'&edate='.$end_date.'&token=51f99871ce6bea90b4ddd82a396bef20af3768c29ff0848d7150c01f0e92c5e2&tokenid&reportid=12&');	
	$csv = new parseCSV();
	$csv->delimiter = ",";
	$csv->parse($file_content);
	if (count($csv->data) > 0)
	{
		foreach($csv->data as $value)
		{	
			$insert_flg = 1;
			$program_id = "";
			$reference_id = "";
			$retailer = "";
			$user_id = "";
			$commision = "";
			$transaction_amount = "";
			$transaction_date = "";
			
			$program_id = $value['Merchant ID'];
			$reference_id = $value['Order ID'];
			$retailer = $value['Merchant Name'];
			$payment_type = 'cashback';
			$user_id = $value['Member ID'];
			$commision = $value['Commissions($)'];
			$transaction_amount = $value['Sales($)'];
			$transaction_date = $value['Transaction Date'];
			$date = new DateTime($transaction_date);
			$transaction_date = $date->format('Y-m-d');
			
			//Query to check whether the reference already exists in database
			
			$query_chk_ref = "SELECT program_id,reference_id,payment_type,user_id,transaction_amount,amount,transaction_commision,transaction_date,network_id FROM cashbackengine_transactions WHERE reference_id = '$reference_id'";
			$rs	= smart_mysql_query($query_chk_ref);
			$total = mysql_num_rows($rs);
			if($total > 0)
			{
				while($rows = mysql_fetch_array($rs))
				{	
				
					if(// If any match then nothing will happen otherwise - new record will be inserted
						($program_id == $rows['program_id']) &&
						($reference_id == $rows['reference_id']) &&
						($payment_type == $rows['payment_type']) &&
						($commision == $rows['transaction_commision']) &&
						($transaction_amount == $rows['transaction_amount']) 
					)	
					{
						$insert_flg = 0;
					}
				}
			}
			
			if($insert_flg==1)
			{
			
				//Calculating the amount to be cashback from the cashback % given using network_id and program_id
				$query = "SELECT cashback FROM cashbackengine_retailers WHERE network_id='$network_id' AND program_id='$program_id' LIMIT 1";
				
				$cashback_result = smart_mysql_query($query);
				$cashback_row = mysql_fetch_array($cashback_result);

				$cashback_store = mysql_real_escape_string($cashback_row['title']);
				$cashback		= $cashback_row['cashback'];
				
			if ($cashback != "")
				{
					if (strstr($cashback, '%'))
					{
						$cashback_percent = str_replace('%','',$cashback);
						$member_money = CalculatePercentage($transaction_amount, $cashback_percent);
					}
					else
					{
						if ($commission < $cashback)
						{
							$member_money = $cashback;
							$cashbackengine_status = "incomplete";
							$reason = "too high cashback value";
						}
						else
						{
							$member_money = $cashback;
						}
					}
				}


				$query = "INSERT INTO cashbackengine_transactions SET
					program_id = '$program_id',
					reference_id = '$reference_id',
					retailer = '$retailer',
					payment_type = '$payment_type',
					user_id = '$user_id',
					transaction_amount = '$transaction_amount',
					transaction_commision = '$commision',
					transaction_date = '$transaction_date' ,
					network_id = '$network_id',
					created = NOW(),
					amount = '$member_money',
					status = 'pending'
				";
				$result = smart_mysql_query($query);	
				//If commission is less than the amount then fire an email to the
				if($commision < $member_money && $result==1)
				{
					$insert_id = mysql_insert_id();
					$to      = SITE_MAIL;
					$subject = 'Cashback amount is more than commision';
					$message = 'Please check the transaction with id : '.$insert_id.'\n 
					The commision of this particular transaction is :'.$commision.'\n
					Money for the member is :'.$member_money.' ,Which is more than the commision, Which seems like
					a conflict, please resolve this issue';
					
					$headers = 'From: '.SITE_TITLE.' <'.NOREPLY_MAIL.'>' . "\r\n";
					
					mail($to, $subject, $message, $headers);
					$insert_id = 0;
				} 
			}
			
		}
	}
?>