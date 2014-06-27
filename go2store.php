<?php
/*******************************************************************\
 * CashbackEngine v2.1
 * http://www.CashbackEngine.net
 *
 * Copyright (c) 2010-2014 CashbackEngine Software. All rights reserved.
 * ------------ CashbackEngine IS NOT FREE SOFTWARE --------------
\*******************************************************************/

	session_start();
	require_once("inc/config.inc.php");
	
	// The Mobile Detection Class
	require_once("inc/mobile_detect.php");
	$detect = new Mobile_Detect;
	
	$userid = (int)$_SESSION['userid'];


	if (isset($_GET['id']) && is_numeric($_GET['id']))
	{
		$retailer_id = (int)$_GET['id'];
		$retailer_url = $_GET['rURL'];

		$query = "SELECT * FROM cashbackengine_retailers WHERE retailer_id='$retailer_id' LIMIT 1";
		$result = smart_mysql_query($query);

		if (mysql_num_rows($result) > 0)
		{
			if (isset($_GET['c']) && is_numeric($_GET['c']) && $_GET['c'] > 0)
			{
				$coupon_id = (int)$_GET['c'];

				$coupon_result = smart_mysql_query("SELECT * FROM cashbackengine_coupons WHERE coupon_id='$coupon_id' LIMIT 1");
				if (mysql_num_rows($coupon_result) > 0)
				{
					$coupon_row = mysql_fetch_array($coupon_result);
					$coupon_link = $coupon_row['link'];
				}
			}
			
			//user clicked banner
			$a = isset($_GET['a']) && is_numeric($_GET['a']) && $_GET['a']>0 ? $_GET['a'] : 0;
			$b = isset($_GET['b']) && is_numeric($_GET['b']) && $_GET['b'] > 0 ? $_GET['b'] : 0;
			$s = isset($_GET['s']) && is_numeric($_GET['s']) && $_GET['s'] > 0 ? $_GET['s'] : 0;
			if ($a || $b || $s)
			{
				$table_name = '';
				if($a)
				{
					$table_name = 'cashbackengine_advertisements';
					$other_pk = 'advertisement_id';
					$other_field = 'a';	
					$other_id = $a;
				}
				elseif($b)
				{	
					$table_name = 'cashbackengine_banners';
					$other_pk = 'banner_id';
					$other_field = 'b';
					$other_id = $b;
				}
				elseif($s)
				{
					$table_name = 'cashbackengine_sale_alert';
					$other_pk = 'sale_alert_id';
					$other_field = 's';
					$other_id = $s;	
				}
					
				if($table_name)
				{
					$other_result = smart_mysql_query("SELECT * FROM $table_name WHERE $other_pk=$other_id");
					if (mysql_num_rows($other_result) > 0)
					{
						$other_row = mysql_fetch_array($other_result);
						$other_link = $other_row['link'];
					}
				}
			}
			
			if (!isLoggedIn())
			{
				$_SESSION['goRetailerID']	= $retailer_id;
				$_SESSION['goCouponID']		= $coupon_id;
				$_SESSION['retailer_url']	= $retailer_url;
				//print_r($_SESSION);exit;
				//header("Location: signup_or_login.php?msg=4");
				
				if ($detect->isMobile() || $detect->isTablet()) {
					header("Location: signup_or_login.php");//Redirection to the Login Page
				} else {
					header("Location: retailers.php?show=111111&p=1");//p = 1 to show the popup in retailer if the user is logged in
				}
					exit();
			}

			// update retailer visits //
			smart_mysql_query("UPDATE cashbackengine_retailers SET visits=visits+1 WHERE retailer_id='$retailer_id'");

			// update coupon visits //
			if (isset($coupon_id) && is_numeric($coupon_id))
			{
				smart_mysql_query("UPDATE cashbackengine_coupons SET visits=visits+1 WHERE coupon_id='$coupon_id'");
			}

			// save member's click in history //
			smart_mysql_query("INSERT INTO cashbackengine_clickhistory SET user_id='".(int)$userid."', retailer_id='".(int)$retailer_id."', added=NOW()");

			$row = mysql_fetch_array($result);

			if ($row['url'] != "")
			{
				if ($coupon_link != "")
					$retailer_website = str_replace("{USERID}", $userid, $coupon_link);
				elseif($other_link!='')
					$retailer_website = str_replace("{USERID}", $userid, $other_link);
				else
					$retailer_website = str_replace("{USERID}", $userid, $row['url']);

				if (SHOW_LANDING_PAGE == 1)
				{
					// show landing page
					if ($coupon_id)
						$go_url = "redirect.php?id=".$retailer_id."&c=".$coupon_id;
					elseif ($other_id)
					{
						$go_url = "redirect.php?id=".$retailer_id."&$other_field=".$other_id;
					}
					else
						$go_url = "redirect.php?id=".$retailer_id;

					header("Location: $go_url");
					exit();
				}
				else
				{
					// directly open retailer's website
					header("Location: ".$retailer_website);
					exit();
				}
			}
		}
		else
		{
			///////////////  Page config  ///////////////
        	$PAGE_TITLE = CBE1_STORE_NOT_FOUND;

			require_once ("inc/header.inc.php");
			echo "<p align='center'>".CBE1_STORE_NOT_FOUND2."<br/><br/><a class='goback' href='#' onclick='history.go(-1);return false;'>".CBE1_GO_BACK."</a></p>";
			require_once ("inc/footer.inc.php");
		}
	}
	else
	{	
		header("Location: index.php");
		exit();
	}

?>