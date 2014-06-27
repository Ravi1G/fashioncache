<?php
/*******************************************************************\
 * CashbackEngine v2.1
 * http://www.CashbackEngine.net
 *
 * Copyright (c) 2010-2014 CashbackEngine Software. All rights reserved.
 * ------------ CashbackEngine IS NOT FREE SOFTWARE --------------
\*******************************************************************/

	require_once("../inc/config.inc.php");
	
	$today = "";
	$fetched_date = "";
	$is_completed = "";
 
	 //Read the record from history table
 	$query_read_history = "SELECT 
 								id,	network_id,	fetched_date, field_name, field_value,is_completed 
 								FROM cashbackengine_network_fetch_history 
 								WHERE network_id = 10 AND field_name='page_no_transaction'";
	$rs_history	= smart_mysql_query($query_read_history);
	$total_rs = mysql_num_rows($rs_history);
	
	if($total_rs > 0)
	{
		$row_history = mysql_fetch_assoc($rs_history);
		$page_no = $row_history['field_value'];
		$fetched_date = $row_history['fetched_date'];
		$is_completed = $row_history['is_completed'];
		//Fetch today's date and compare with the date fetched from database, 
		// if not today then - reset the page number
		$today = date("Y-m-d");
		if($today > $fetched_date)
		{// Set the variables
			$page_no = 1;
			$is_completed = 0;
		}
	 	else if($is_completed==0)
	 	{
	 		$page_no = $page_no + 1;
	 	}
	 	else if(($is_completed == 1) && ($today==$fetched_date))
	 	{// if completed flag is 1 and fetched date is today then don't proceed further exit from the script
	 		echo 'The records are completed';
	 		exit;
	 	}
	}
	
	 while($is_completed==0)
	{
		$query_read_history = "SELECT 
 								id,	network_id,	fetched_date, field_name, field_value,is_completed 
 								FROM cashbackengine_network_fetch_history 
 								WHERE network_id = 10 AND field_name='page_no_transaction'";
		$rs_history	= smart_mysql_query($query_read_history);
		$total_rs = mysql_num_rows($rs_history);
	
		if($total_rs > 0)
			{
			$row_history = mysql_fetch_assoc($rs_history);
			$page_no = $row_history['field_value'];
			$fetched_date = $row_history['fetched_date'];
			$is_completed = $row_history['is_completed'];

			//Fetch today's date and compare with the date fetched from database, 
			// if not today then - reset the page number
			$today = date("Y-m-d");
			if($today > $fetched_date)
			{
				$page_no = 1;
			}
		 	else if($is_completed==0)
		 	{
		 		$page_no = $page_no + 1;
		 	}
		 	else if(($is_completed == 1) && ($today==$fetched_date))
		 	{// if completed flag is 1 and fetched date is today then don't proceed further exit from the script
		 		echo 'The records are completed';
		 		break;
		 	}
			set_time_limit(0);   
		    //$cDevKey = '00b72e6d005831676a338b6a10e13f54b37f9c67f03307442f392fc7814efc17b90ff5a13efc010149709a7f721fd379cd59e792d091073fcf32a0ceb35e4c4127/72c921bea48518c380d72cb8b51d054b917c85d90b70d33630449364a104f12b90d650e703a8b84df0563f6b47466934fa03fedeb22cb728ec5c632ea5542a49'; 
		    //Read id of linkshare from the database starts
			$query_nw_id = "SELECT network_id FROM cashbackengine_affnetworks WHERE network_name = 'Impact Radius'";
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
			//Conditions to check and set start and end date for the query
			$current_date = date('Ymd'); // SETTING THESE VARIABLES pending
			$current_time = date('His');
			if(($current_time>000000)&&($current_time<010000))
				{
					$start_date = $current_date - 1;
				}
			else
				{
					$start_date = $current_date;
				}
			//Computing start and end date for the cURL
		 	$end_date = date("Y-m-d", strtotime($current_date)).'T00:00:00-08:00';
		 	$start_date = date("Y-m-d", strtotime($start_date)).'T00:00:00-08:00';
			}
			//Dummy Dates for the records////////////////// 
			//$start_date="2014-05-27T00:00:00-08:00";
			//$end_date="2014-06-10T00:00:00-08:00";
			
		    $cURL = "https://IRPrFpKBUxQT94351LPSTigVAqTUY52NG1:8CX2ifugv7HkmRPo5LTf8ANvZN2kFMTZ@api.impactradius.com/2010-09-01/Mediapartners/IRPrFpKBUxQT94351LPSTigVAqTUY52NG1/Actions?ActionDateStart=$start_date&ActionDateEnd=$end_date&page=$page_no";
		    $ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $cURL);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			              'User-Agent: "Mozilla/5.0 (Windows; U; Windows NT 6.0; en-US; rv:1.9.0.15) Gecko/2009101601 Firefox/3.0.15 GTB6 (.NET CLR 3.5.30729)"'
			            ));
			 
			curl_setopt($ch, CURLOPT_HEADER, false);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
			curl_setopt($ch, CURLOPT_TIMEOUT, 0);
			 
			$cHTML = curl_exec($ch);
			if (curl_error($ch)) {
			    echo "Curl error: " . curl_error($ch);
			} 
			else {
			    $cXML = simplexml_load_string($cHTML);
			    $total_pages = $cXML->Actions->attributes()->numpages;
			    foreach ($cXML->Actions->Action AS $action)
			    {
			    	//$id				=	mysql_real_escape_string($action->Id);// Can use referenceId
			    	$insert_flg 	=	1;
			    	$eventDate		=	mysql_real_escape_string(date('Y-m-d h:i:s',strtotime($action->EventDate)));//transaction_date
			    	$SaleAmount 	=	mysql_real_escape_string($action->Amount);//transaction_amout
			    	$referenceId	=	mysql_real_escape_string($action->Oid);// reference_id
			    	$status			=	mysql_real_escape_string($action-> State);//Status of transaction
			    	$commisionAmount=	mysql_real_escape_string($action->Payout);//CommisionAmount
			    	$programId 		=	mysql_real_escape_string($action->CampaignId);//ProgramId 
			    	$UserId			=	mysql_real_escape_string($action->SharedId);//UserId
		
			    	$query_chk_ref ="SELECT reference_id,
			    							program_id,
			    							transaction_id,
			    							user_id,
			    							amount,
			    							transaction_amount,
			    							transaction_commision,
			    							transaction_date,
			    							user_id FROM cashbackengine_transactions WHERE reference_id = '$referenceId' && network_id=10";
			    	
			    	$rs	= smart_mysql_query($query_chk_ref);
					$total = mysql_num_rows($rs);
					if($total > 0)
					{//Donot insert the record
					while($rows = mysql_fetch_array($rs))
						{	
							if(// If any match then nothing will happen otherwise - new record will be inserted
								($programId == $rows['program_id']) &&
								($SaleAmount == $rows['transaction_amount']) &&
								($commisionAmount == $rows['transaction_commision'])&&
								($eventDate == $rows['transaction_date'])
							)	
							{
								$insert_flg = 0;
							}
						}
					}
					
					if($insert_flg == 1)
					{
					//Calculating the amount to be cashback from the cashback % given using network_id and program_id
						$query = "SELECT cashback FROM cashbackengine_retailers WHERE network_id=10 AND program_id='$programId' LIMIT 1";
						
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
						//Insert the record
						//Retailer can also be inserted using $retailer
						$query_insert_record = "INSERT INTO 
												cashbackengine_transactions	SET 
												reference_id		= '$referenceId',
												transaction_date	= '$eventDate',
												transaction_amount	= '$SaleAmount',
												status				= '$status',
												transaction_commision ='$commisionAmount',
												program_id			= '$programId',
												user_id				= '$UserId',
												created				=	NOW(),
												network_id			=	10,
												payment_type		=	'cashback'
												"
						;
						
						$result = smart_mysql_query($query_insert_record);
						 
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
			    	
			    }//end of foreach
			} // ends else from if (curl_error($ch))
			
			
			if(($page_no == $total_pages))
		{
			//Set the completed flag to 1,
			$query_update_history = "UPDATE cashbackengine_network_fetch_history 
										SET  
 											fetched_date=NOW(),
 											field_value=$page_no,
 											is_completed=1 
 											WHERE network_id = 10 AND field_name='page_no_transaction'";	
			$result = smart_mysql_query($query_update_history);
			$is_completed = 1;
			break;
			exit;
		}
	 	else
	 	{
	 		$query_update_history = "UPDATE	cashbackengine_network_fetch_history 
	 									SET  
 											fetched_date=NOW(),
 											field_value=$page_no,
 											is_completed=0 
 											WHERE network_id = 10 AND field_name='page_no_transaction'";
	 		$result = smart_mysql_query($query_update_history);
	 		$is_completed = 0;
	 	}
		}
	}//End of while
    ?>