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
 								WHERE network_id = 1";
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
 								WHERE network_id = 1";
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
		    $cDevKey = '00b72e6d005831676a338b6a10e13f54b37f9c67f03307442f392fc7814efc17b90ff5a13efc010149709a7f721fd379cd59e792d091073fcf32a0ceb35e4c4127/72c921bea48518c380d72cb8b51d054b917c85d90b70d33630449364a104f12b90d650e703a8b84df0563f6b47466934fa03fedeb22cb728ec5c632ea5542a49'; 
		   
			$cURL = "https://linksearch.api.cj.com/v2/link-search?website-id=7383079&advertiser-ids=joined&records-per-page=100&page-number=$page_no";
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
				 $total_matched = $cXML->links->attributes()->{'total-matched'};
				 
				 $total_pages = ceil($total_matched/100);
				 foreach($cXML->links->link as $c)
				 {
				 	
				 	$advertiser_id			= mysql_real_escape_string($c -> {'advertiser-id'}); // program - id, use the local id using following $query_chk_retailer
				 	$advertiser_name		= mysql_real_escape_string($c -> {'advertiser-name'}); // Retailer - name
				 	$link_id				= mysql_real_escape_string($c -> {'link-id'}); // Unique identifier
				 	$link_code				= mysql_real_escape_string($c -> {'link-code-html'}); // can Extract coupon code from here ---- doubt
				 	$link_name				= mysql_real_escape_string($c -> {'link-name'});//Title
				 	$description			= mysql_real_escape_string($c -> {'description'});// Coupon descriptin
				 	$promotion_start_date	= mysql_real_escape_string($c -> {'promotion-start-date'}); // start date
				 	$promotion_end_date		= mysql_real_escape_string($c -> {'promotion-end-date'}); // end date
				 	$link					= mysql_real_escape_string($c -> {'destination'});// link
				 	
				 	//$link_code_extract 		= strtoupper ($link_name); 
				 	//$pieces = explode("USE CODE", $link_code_extract);
				 	//print_r($pieces);
				 	//echo $link_code;
				 	//print_r($c->{'link-name'});
				 	//echo '-HERE<br><br>';
		
				 	
				 	$query_check = "SELECT coupon_id FROM cashbackengine_coupons WHERE coupon_ref_id = '$link_id'";
				 	
				 	$result = smart_mysql_query($query_check);
				 	$total = mysql_num_rows($result);
					if($total > 0)
					{
					}
					else{
					//Get the maximum sort order from the coupons to give the next sort order
							$query_get_sortorder = "SELECT MAX(sort_order) AS max_order_id FROM cashbackengine_coupons";
		    				$sort_order	= smart_mysql_query($query_get_sortorder);
							 if(mysql_num_rows($sort_order)>0)
							 {
							 	$record = mysql_fetch_assoc($sort_order); 
							 	$sort_order = $record['max_order_id']+1;
							 }
							 
							 //Check whether the retailer exists or not within the database,
							// if not exist then create a new retailer with advertiserid and advertisername
							 
							$query_chk_retailer = "SELECT 
													retailer_id, title 
												FROM 
													cashbackengine_retailers 
													WHERE 
													network_id = 1 AND
													program_id = '$advertiser_id'";
							
		    				$rs_retailer	= smart_mysql_query($query_chk_retailer);
							$total_retailer = mysql_num_rows($rs_retailer);
							//Check if retailer doesnot exist then store the status as expired 
							//or fire an email to create retailer with the id and title
							if($total_retailer == 0)
							{
								$slug = str_replace(" ","-",$advertiser_name);
								
								//Create a record for retailer 
								$query_insert_retailer = "INSERT INTO cashbackengine_retailers SET
															program_id = '$advertiser_id',
															title = '$advertiser_name',
															network_id ='1',
															retailer_slug = '$slug',
															added = NOW(),
															is_profile_completed = 1
															";
								$result_retailer = smart_mysql_query($query_insert_retailer);
								$retailer_id = mysql_insert_id();
								$clickurl	=	$link."&sid={USERID}";
								 
								$query_insert = "INSERT INTO cashbackengine_coupons 
													SET  
														retailer_id = '$retailer_id', 
														title = '$link_name', 
														link = '$clickurl', 
														start_date = '$promotion_start_date', 
														end_date = '$promotion_end_date', 
														description = '$description', 
														status ='active', 
														added = NOW(), 
														coupon_ref_id = '$link_id',
														sort_order = '$sort_order'
								";
								$send_email = 1;
							}
							else{
								$rs_retailer	= smart_mysql_query($query_chk_retailer);
								$total_retailer = mysql_num_rows($rs_retailer);
								$row_retailer = mysql_fetch_assoc($rs_retailer);
								$retailer_id = $row_retailer['retailer_id'];
							
								$clickurl	=	$link."&sid={USERID}";
								
								//Insert the records and set flag to 0 results don't fire the email
								$query_insert = "INSERT INTO cashbackengine_coupons 
													SET  
														retailer_id = '$retailer_id', 
														title = '$link_name', 
														link = '$clickurl', 
														start_date = '$promotion_start_date', 
														end_date = '$promotion_end_date', 
														description = '$description', 
														status ='active', 
														added = NOW(), 
														coupon_ref_id = '$link_id',
														sort_order = '$sort_order'
								";
								$send_email = 0;
							}
							$result_coupon = smart_mysql_query($query_insert);
							$new_coupon_id = mysql_insert_id();
						}
							
					if($send_email == 1)
					{//Fire an email
						$insert_id = $new_coupon_id;
						//////******** Change the email - id to SITE_MAIL
						$to      = SITE_MAIL;
						$subject = 'Coupon with new retailer';
						$message = 'Please complete the information of the retailer.
						Click on the link to fill the information : <a href="'.SITE_URL.'admin/retailer_edit.php?id='.$retailer_id.'">Edit Retailer<a>
						\r\n';
						
						$headers = 'From: '.SITE_TITLE.' <'.NOREPLY_MAIL.'>' . "\r\n";
						//////********* Uncomment the following to send email
						//echo $message.'<br>';
						mail($to, $subject, $message, $headers);
					}
			}
		} // ends else from if (curl_error($ch))
			if(($today == $fetched_date) && ($page_no == $total_pages))
		{
			//Set the completed flag to 1,
			$query_update_history = "UPDATE cashbackengine_network_fetch_history 
										SET  
 											fetched_date=NOW(),
 											field_value=$page_no,
 											is_completed=1 
 											WHERE network_id = 1";	
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
 											WHERE network_id = 1";
	 		$result = smart_mysql_query($query_update_history);
	 		$is_completed = 0;
	 	}
	}
}
    ?>