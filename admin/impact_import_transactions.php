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
	//Dummy Dates for the records
	//$start_date="2014-05-27T00:00:00-08:00";
	//$end_date="2014-06-10T00:00:00-08:00";
	
    $cURL = "https://IRPrFpKBUxQT94351LPSTigVAqTUY52NG1:8CX2ifugv7HkmRPo5LTf8ANvZN2kFMTZ@api.impactradius.com/2010-09-01/Mediapartners/IRPrFpKBUxQT94351LPSTigVAqTUY52NG1/Actions?ActionDateStart=$start_date&ActionDateEnd=$end_date";
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
	    foreach ($cXML->Actions->Action AS $action)
	    {
	    	//$id				=	mysql_real_escape_string($action->Id);// Can use referenceId
	    	$insert_flg 	=	1;
	    	$eventDate		=	mysql_real_escape_string(date('Y-m-d h:i:s',strtotime($action->EventDate)));//transaction_date
	    	$SaleAmount 	=	mysql_real_escape_string($action->Amount);//transaction_amout
	    	$referenceId	=	mysql_real_escape_string($action->Oid);// reference_id
	    	$status			=	mysql_real_escape_string($action-> State);//Status of transaction
	    	$commisionAmount=	mysql_real_escape_string($action->Payout);//CommisionAmount
	    	$programId 		=	mysql_real_escape_string($action->CampaignId);//ProgramId and networkId
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
						($UserId == $rows['user_id']) &&
						($SaleAmount == $rows['transaction_amount']) &&
						($commisionAmount == $rows['transaction_commision']) &&
						($UserId == $rows['user_id']) 
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
				 
				 /*
				  * 
				  *ASK ABOUT THE FUNCTIONALITY IS REQUIRED OR NOT, payment_type
				  * if($commission < $member_money && $result==1)
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
				}*/
				 
			}
	    	
	    }//end of foreach
	} // ends else from if (curl_error($ch))
    ?>