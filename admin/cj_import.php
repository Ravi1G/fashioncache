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
	set_time_limit(0);   
    $cDevKey = '00b72e6d005831676a338b6a10e13f54b37f9c67f03307442f392fc7814efc17b90ff5a13efc010149709a7f721fd379cd59e792d091073fcf32a0ceb35e4c4127/72c921bea48518c380d72cb8b51d054b917c85d90b70d33630449364a104f12b90d650e703a8b84df0563f6b47466934fa03fedeb22cb728ec5c632ea5542a49'; 
    //Read id of linkshare from the database starts
	$query_nw_id = "SELECT network_id FROM cashbackengine_affnetworks WHERE network_name = 'Commission Junction'";
	$rs	= smart_mysql_query($query_nw_id);
	$total = mysql_num_rows($rs);
	if($total > 0)
	{
		$row_nw = mysql_fetch_assoc($rs);
		$network_id = $row_nw['network_id'];
	}//Read id of commision junction from the database ends
	
	if((isset($_GET['start_date'])) && ($_GET['start_date']!="")&& (isset($_GET['end_date'])) && ($_GET['end_date']!=""))
	{
		$start_date = $_GET['start_date'];
		$end_date	= $_GET['end_date'];
	}
	else {
	//Current date and time
	$current_date = date('Ymd'); // SETTING THESE VARIABLES pending
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
	//Computing start and end date for the cURL
 	$end_date = date("Y-m-d", strtotime($current_date));
 	$start_date = date("Y-m-d", strtotime($start_date));
	}
	
    $cURL = "https://commission-detail.api.cj.com/v3/commissions?date-type=event&start-date=$start_date&end-date=$end_date";
    $ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $cURL);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
	              'Authorization: ' . $cDevKey,
	              'User-Agent: "Mozilla/5.0 (Windows; U; Windows NT 6.0; en-US; rv:1.9.0.15) Gecko/2009101601 Firefox/3.0.15 GTB6 (.NET CLR 3.5.30729)"'
	            ));
	 
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
	curl_setopt($ch, CURLOPT_TIMEOUT, 10);
	 
	$cHTML = curl_exec($ch);
	if (curl_error($ch)) {
	    echo "Curl error: " . curl_error($ch);
	} 
	else {
	    $cXML = simplexml_load_string($cHTML);
	    for ($i = 0; $i < count($cXML->commissions->commission); $i++) {
	    	$insert_flg = 1;
	    	$single = $cXML->commissions->commission[$i];
	        $program_id = $single->cid;
	        $transaction_id = $single->{'order-id'};
	        $user_id =$single->sid;
	        $transaction_amount = $single->{'sale-amount'};
	        $commission = $single->{'commission-amount'};
	        $status = $single->{'action-status'};
	        $original_action_id = $single->{'original-action-id'};
	        $retailer = $single->{'advertiser-name'};
			$transaction_date = $single->{'event-date'};
			$transaction_date= date('Y-m-d h:i:s',strtotime($transaction_date));
			//$transaction_date = explode('T',$transaction_date);
			//$transaction_date = $transaction_date[0].' 00:00:00';
	//Query to check whether the transaction already exists in database			
			$query_chk_ref = "SELECT program_id,transaction_id,user_id,amount,transaction_amount,transaction_commision,transaction_date,original_action_id FROM cashbackengine_transactions WHERE original_action_id = '$original_action_id'";
			
			$rs	= smart_mysql_query($query_chk_ref);
			$total = mysql_num_rows($rs);
			if($total > 0)
			{
				while($rows = mysql_fetch_array($rs))
				{	
				
					if(// If any match then nothing will happen otherwise - new record will be inserted
						($program_id == $rows['program_id']) &&
						($user_id == $rows['user_id']) &&
						($transaction_amount == $rows['transaction_amount']) &&
						($commission == $rows['transaction_commision']) &&
						($original_action_id == $rows['original_action_id'])
					)	
					{
						$insert_flg = 0;
					}
				}
				
			}
			
	        if($insert_flg == 1)
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
			//status of the transaction by default : pending
			
				$status = 'pending';
			
			
	        $query = "INSERT INTO cashbackengine_transactions SET 
	        				network_id = '$network_id',
							program_id ='$program_id',
							reference_id='$transaction_id',
							user_id ='$user_id',
							transaction_amount = '$transaction_amount',
							transaction_commision = '$commission',
							status = '$status',
							created = NOW(),
							amount = '$member_money',
							original_action_id = '$original_action_id',
							payment_type ='cashback',
							transaction_date = '$transaction_date',
							retailer = '$retailer'
							";
			
	        $result = smart_mysql_query($query);
	        	
				//If commission is less than the amount then fire an email to the
				if($commission < $member_money && $result==1)
				{
					$insert_id = mysql_insert_id();
					$to      = SITE_MAIL;
					$subject = 'Cashback amount is more than commision';
					$message = 'Please check the transaction with id : '.$insert_id.'\n 
					The commision of this particular transaction is :'.$commission.'\n
					Money for the member is :'.$member_money.' ,Which is more than the commision, Which seems like
					a conflict, please resolve this issue';
					
					$headers = 'From: '.SITE_TITLE.' <'.NOREPLY_MAIL.'>' . "\r\n";
					
					mail($to, $subject, $message, $headers);
					$insert_id = 0;
				} 
	        }
	    } // ends for ($i = 0; $i < count($cXML->commissions->commission); $i++)
	} // ends else from if (curl_error($ch))
    ?>