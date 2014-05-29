 <?php
 require_once("../inc/config.inc.php");
 
 $today = "";
 $fetched_date = "";
 $is_completed = "";
 
 //Read the record from history table
 
 	$query_read_history = "SELECT 
 								id,	network_id,	fetched_date, field_name, field_value,is_completed 
 								FROM cashbackengine_network_fetch_history 
 								WHERE network_id = 9";
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
 								WHERE network_id = 9";
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
	 		//exit;
	 	}	
		
 	set_time_limit(0);   
  	$cDevKey = '00b72e6d005831676a338b6a10e13f54b37f9c67f03307442f392fc7814efc17b90ff5a13efc010149709a7f721fd379cd59e792d091073fcf32a0ceb35e4c4127/72c921bea48518c380d72cb8b51d054b917c85d90b70d33630449364a104f12b90d650e703a8b84df0563f6b47466934fa03fedeb22cb728ec5c632ea5542a49';
 	$cURL = "http://couponfeed.linksynergy.com/coupon?token=1498767497b950eb0019efb9c34bf949d1c8988a738ce5a8422ca39ba1dd79f0&resultsperpage=100&pagenumber=$page_no&U1={USERID}";
 	//&network=1, mid can be passed to the cURL for selected records
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
	    break;
	} 
	else {
	    $cXML = simplexml_load_string($cHTML);
	    //Read id of linkshare from the database starts
			$query_nw_id = "SELECT network_id FROM cashbackengine_affnetworks WHERE network_name = 'Linkshare'";
			$rs	= smart_mysql_query($query_nw_id);
			$total = mysql_num_rows($rs);
			if($total > 0)
			{
				$row_nw = mysql_fetch_assoc($rs);
				$network_id = $row_nw['network_id'];
			}//Read id of linkshare from the database ends
			
			$total_pages = $cXML->TotalPages;
			$page_no_requested = $cXML->PageNumberRequested;
	    foreach($cXML AS $c)
	    {
	    	if(count($c)>0)
	    	{
				
	    	$category = mysql_real_escape_string($c->categories->{'category'});
	    	$promotiontype= mysql_real_escape_string($c->promotiontypes->promotiontype);
	    	$coupon_code = mysql_real_escape_string($c->couponcode);
	    	$offer_description = mysql_real_escape_string($c->offerdescription); // description
	    	$offerstartdate= mysql_real_escape_string($c->offerstartdate);// end_date
	    	$offerenddate= mysql_real_escape_string($c->offerenddate); //end_date
	    	$clickurl = mysql_real_escape_string($c->clickurl);//link
	    	$impressionpixel= mysql_real_escape_string($c->impressionpixel);
	    	$advertiserid= mysql_real_escape_string($c->advertiserid); // Retailer id
	    	$advertisername = mysql_real_escape_string($c->advertisername); // Retailer name
	    	$network= mysql_real_escape_string($c->network);
	    	
	    	$pieces = explode("&", $clickurl);
			$link_type = explode("=",$pieces[2]);
			$link_type = $link_type[1];
			
			$link_type_id = explode(".",$pieces[1]);
			$link_type_id = $link_type_id[1];
	    	
			$reference_id = $advertiserid.'_'.link_type.'_'.$link_type_id;
			
	    	$query_chk_ref = "SELECT 
	    							coupon_id, retailer_id, user_id, title, code, link, 
	    							start_date, end_date, description, status 
	    						FROM  
	    							cashbackengine_coupons WHERE coupon_ref_id='$reference_id'";
		
	    	$rs	= smart_mysql_query($query_chk_ref);
			$total = mysql_num_rows($rs);
			
				if($total>0)
				{
					//Do not insert the new record
				}
				else 
				{
					//Get the maximum sort order from the coupons to give the next sort order
					$query_get_sortorder = "SELECT MAX(sort_order) AS max_order_id FROM cashbackengine_coupons";
    				$sort_order	= smart_mysql_query($query_get_sortorder);
					 if(mysql_num_rows($sort_order)>0)
					 {
					 	$record = mysql_fetch_assoc($sort_order); 
					 	$sort_order = $record['max_order_id']+1;
					 }
					
					//Insert the record - Also check whether the retailer exists or not within the database,
					// if not exist then create a new retailer with advertiserid and advertisername
					 
					$query_chk_retailer = "SELECT 
											retailer_id, title 
										FROM 
											cashbackengine_retailers 
											WHERE 
											network_id = 9 AND
											program_id = '$advertiserid'";
					
    				$rs_retailer	= smart_mysql_query($query_chk_retailer);
					$total_retailer = mysql_num_rows($rs_retailer);
					
					//Check if retailer doesnot exist then store the status as expired 
					//or fire an email to create retailer with the id and title
					if($total_retailer == 0)
					{
						//Create a record for retailer 
						$slug = str_replace(" ","-",$advertisername);
						$query_insert_retailer = "INSERT INTO cashbackengine_retailers SET
													program_id = '$advertiserid',
													title = '$advertisername',
													network_id ='9',
													retailer_slug = '$slug',
													added = NOW(),
													is_profile_completed = 1
													";
						$result_retailer = smart_mysql_query($query_insert_retailer);
						$retailer_id = mysql_insert_id();
						$clickurl	=	$clickurl."&U1={USERID}";
						 
						$query_insert = "INSERT INTO cashbackengine_coupons 
											SET  
												retailer_id = '$retailer_id', 
												title = '$promotiontype', 
												link = '$clickurl', 
												start_date = '$offerstartdate', 
												end_date = '$offerenddate', 
												description = '$offer_description', 
												status ='active', 
												added = NOW(), 
												coupon_ref_id = '$reference_id',
												sort_order = '$sort_order',
												code='$coupon_code'
						";
						$send_email = 1;
					}
					else 
					{
					
						//Fetch Retailer id from the database using title, program_id and network_id
						$query_chk_retailer = "SELECT 
												retailer_id, title 
												FROM
												cashbackengine_retailers 
												WHERE 
												network_id = 9 AND title = '$advertisername' AND
												program_id = $advertiserid";
					
    					$rs_retailer	= smart_mysql_query($query_chk_retailer);
						$total_retailer = mysql_num_rows($rs_retailer);
						$row_retailer = mysql_fetch_assoc($rs_retailer);
						$retailer_id = $row_retailer['retailer_id'];
					
						$clickurl	=	$clickurl."&U1={USERID}";
						
						//Insert the records and set flag to 0 results don't fire the email
						$query_insert = "INSERT INTO cashbackengine_coupons 
											SET  
												retailer_id = '$retailer_id', 
												title = '$promotiontype', 
												link = '$clickurl', 
												start_date = '$offerstartdate', 
												end_date = '$offerenddate', 
												description = '$offer_description', 
												status ='active', 
												added = NOW(), 
												coupon_ref_id = '$reference_id',
												sort_order = '$sort_order',
												code='$coupon_code'
						";
						$send_email = 0;
					}
					
					$result = smart_mysql_query($query_insert);
					$new_coupon_id = mysql_insert_id();
					
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
						mail($to, $subject, $message, $headers);
					}
						
				}
	    	}
	    }
	}

		if(($today == $fetched_date) && ($page_no == $total_pages))
		{
			//Set the completed flag to 1,
			$query_update_history = "UPDATE cashbackengine_network_fetch_history 
										SET  
 											fetched_date=NOW(),
 											field_value=$page_no,
 											is_completed=1 
 											WHERE network_id = 9";	
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
 											WHERE network_id = 9";
	 		$result = smart_mysql_query($query_update_history);
	 		$is_completed = 0;
	 	}
	 	
	}
} 
?>