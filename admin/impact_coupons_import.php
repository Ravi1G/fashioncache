 <?php
 require_once("../inc/config.inc.php");
  
 	$today = "";
	$fetched_date = "";
	$is_completed = "";
 
	 //Read the record from history table
 	$query_read_history = "SELECT 
 								id,	network_id,	fetched_date, field_name, field_value,is_completed 
 								FROM cashbackengine_network_fetch_history 
 								WHERE network_id = 10 AND field_name='page_no'";
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
 								WHERE network_id = 10 AND field_name='page_no'";
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
 	$cURL ="https://IRPrFpKBUxQT94351LPSTigVAqTUY52NG1:8CX2ifugv7HkmRPo5LTf8ANvZN2kFMTZ@api.impactradius.com/2010-09-01/Mediapartners/IRPrFpKBUxQT94351LPSTigVAqTUY52NG1/PromoAds?page=$page_no&pagesize=100";
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
	    break;
	} 
	else {
	    //Read id of linkshare from the database starts
		$cXML = simplexml_load_string($cHTML);
		$current_page = $cXML->PromotionalAds->attributes()->page;
		$total_pages = $cXML->PromotionalAds->attributes()->numpages;

		foreach($cXML->PromotionalAds->PromotionalAd AS $c)
		{
			$title			=	mysql_real_escape_string($c->Name);
			$link			=	mysql_real_escape_string($c->TrackingLink); 
			$start_date		=	mysql_real_escape_string($c->StartDate); 
			$end_date		=	mysql_real_escape_string($c->EndDate);
			//$description	=	mysql_real_escape_string($c->Description); 
			$description	=	mysql_real_escape_string($c->LinkText);
			$campaign_id	=	mysql_real_escape_string($c->CampaignId);
			$campaign_name	=	mysql_real_escape_string($c->CampaignName);
			$promo_code		=	mysql_real_escape_string($c->PromoCode);
			$coupon_ref_id	=	'impact_'.$c->Id;
			$query_check	=	"SELECT coupon_id FROM cashbackengine_coupons WHERE coupon_ref_id = '$coupon_ref_id'";
			$result			=	smart_mysql_query($query_check);
			$total			=	mysql_num_rows($result);
			if($total > 0)
			{
			}
			else
			{
			//Get the maximum sort order from the coupons to give the next sort order
				$query_get_sortorder	=	"SELECT MAX(sort_order) AS max_order_id FROM cashbackengine_coupons";
    			$sort_order				=	smart_mysql_query($query_get_sortorder);
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
										network_id = 10 AND
										program_id = '$campaign_id'";

				$rs_retailer	= smart_mysql_query($query_chk_retailer);
				$total_retailer = mysql_num_rows($rs_retailer);
				
			//Check if retailer doesnot exist then store the status as expired 
							//or fire an email to create retailer with the id and title
				if($total_retailer == 0)
				{
					$slug = str_replace(" ","-",$campaign_name);
					$slug = str_replace("'","",$slug);
					//Create a record for retailer 
					$query_insert_retailer = "INSERT INTO cashbackengine_retailers SET
												program_id = '$campaign_id',
												title = '$campaign_name',
												network_id ='10',
												retailer_slug = '$slug',
												added = NOW(),
												is_profile_completed = 1
												";
					$result_retailer = smart_mysql_query($query_insert_retailer);
					$retailer_id = mysql_insert_id();
					$query_insert = "INSERT INTO cashbackengine_coupons 
										SET  
											retailer_id = '$retailer_id', 
											title = '$title', 
											link = '$link', 
											start_date = '$start_date', 
											end_date = '$end_date', 
											description = '$description', 
											status ='active', 
											added = NOW(), 
											coupon_ref_id = '$coupon_ref_id',
											sort_order = '$sort_order'
					";
					$send_email = 1;
				}
				else
				{
					$rs_retailer	= smart_mysql_query($query_chk_retailer);
					$total_retailer = mysql_num_rows($rs_retailer);
					$row_retailer = mysql_fetch_assoc($rs_retailer);
					$retailer_id = $row_retailer['retailer_id'];
					//Insert the records and set flag to 0 results don't fire the email
					$query_insert = "INSERT INTO cashbackengine_coupons 
										SET  
											retailer_id = '$retailer_id', 
											title = '$title', 
											link = '$link', 
											start_date = '$start_date', 
											end_date = '$end_date', 
											description = '$description', 
											status ='active', 
											added = NOW(), 
											coupon_ref_id = '$coupon_ref_id',
											sort_order = '$sort_order'
					";
					$send_email = 0;
				}
				$result_coupon = smart_mysql_query($query_insert);
				$new_coupon_id = mysql_insert_id();
				
				if($send_email == 1)
				{//Fire an email
					$insert_id = $new_coupon_id;
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
		}//End foreach loop
	}//else part end
			// ends else from if (curl_error($ch))
		if(($today == $fetched_date) && ($page_no == $total_pages))
		{
			//Set the completed flag to 1,
			$query_update_history = "UPDATE cashbackengine_network_fetch_history 
										SET  
 											fetched_date=NOW(),
 											field_value=$page_no,
 											is_completed=1 
 											WHERE network_id = 10 AND field_name='page_no'";	
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
 											WHERE network_id = 10 AND field_name='page_no'";
	 		$result = smart_mysql_query($query_update_history);
	 		$is_completed = 0;
	 	}
	}
}//End while loop
?>