<?php
/*******************************************************************\
 * CashbackEngine v2.1
 * http://www.CashbackEngine.net
 *
 * Copyright (c) 2010-2014 CashbackEngine Software. All rights reserved.
 * ------------ CashbackEngine IS NOT FREE SOFTWARE --------------
\*******************************************************************/


/**
 * Run mysql query
 * @param	$sql		mysql query to run
 * @return	boolean		false if failed run mysql query
*/

function smart_mysql_query($sql)
{
	/*$link = mysql_connect('localhost','username','password');

	if($link == false){
		//try to reconnect
	}
*/
	$res = mysql_query($sql) or die("<p align='center'><span style='font-size:11px; font-family: tahoma, verdana, arial, helvetica, sans-serif; color: #000;'>query failed: ".mysql_error()."</span></p>");
	if(!$res){
		return false;
	}
	return $res;
}


/**
 * Retrieves parameter from POST array
 * @param	$name	parameter name
*/


function getPostParameter($name)
{
	$data = isset($_POST[$name]) ? $_POST[$name] : null;
	if(!is_null($data) && get_magic_quotes_gpc() && is_string($data))
	{
		$data = stripslashes($data);
	}
	$data = trim($data);
	$data = htmlentities($data, ENT_QUOTES, 'UTF-8');
	return $data;
}


/**
 * Retrieves parameter from GET array
 * @param	$name	parameter name
*/


function getGetParameter($name)
{
	return isset($_GET[$name]) ? $_GET[$name] : false;
}


/**
 * Returns random password
 * @param	$length		length of string
 * @return	string		random password
*/

if (!function_exists('generatePassword')) {
	function generatePassword($length = 8)
	{
		$password = "";
		$possible = "0123456789abcdefghijkmnpqrstvwxyzABCDEFGHJKLMNPQRTVWXYZ!(@)";
		$i = 0; 

		while ($i < $length)
		{ 
			$char = substr($possible, mt_rand(0, strlen($possible)-1), 1);

			if (!strstr($password, $char))
			{ 
				$password .= $char;
				$i++;
			}
		}
		return $password;
	}
}


/**
 * Returns random key
 * @param	$text		string
 * @return	string		random key for user verification
*/

if (!function_exists('GenerateKey')) {
	function GenerateKey($text)
	{
		$text = preg_replace("/[^0-9a-zA-Z]/", " ", $text);
		$text = substr(trim($text), 0, 50);
		$key = md5(time().$text.mt_rand(1000,9999));
		return $key;
	}
}


/**
 * Calculate percentage
 * @param	$amount				Amount
 * @param	$percent			Percent value
 * @return	string				returns formated money value
*/

if (!function_exists('CalculatePercentage')) {
	function CalculatePercentage($amount, $percent)
	{
		return number_format(($amount/100)*$percent,2,'.','');
	}
}


/**
 * Returns formated money value
 * @param	$amount				Amount
 * @param	$hide_currency		Hide or Show currency sign
 * @param	$hide_zeros			Show as $5.00 or $5
 * @return	string				returns formated money value
*/

if (!function_exists('DisplayMoney')) {
	function DisplayMoney($amount, $hide_currency = 0, $hide_zeros = 0)
	{
		$newamount = number_format($amount, 2, '.', '');

		if ($hide_zeros == 1)
		{
			$cents = substr($newamount, -2);
			if ($cents == "00") $newamount = substr($newamount, 0, -3);
		}

		if ($hide_currency != 1)
		{
			switch (SITE_CURRENCY_FORMAT)
			{
				case "1": $newamount = SITE_CURRENCY.$newamount; break;
				case "2": $newamount = SITE_CURRENCY." ".$newamount; break;
				case "3": $newamount = SITE_CURRENCY.number_format($amount, 2, ',', ''); break;
				case "4": $newamount = $newamount." ".SITE_CURRENCY; break;
				case "5": $newamount = $newamount.SITE_CURRENCY; break;
				default: $newamount = SITE_CURRENCY.$newamount; break;
			}	
		}

		return $newamount;
	}
}


/**
 * Returns formated cashback value
 * @param	$value		Cashback value
 * @return	string		returns formated cashback value
*/

if (!function_exists('DisplayCashback')) {
	function DisplayCashback($value)
	{
		if (empty($value) || $value == "") 
		{
			return "";
		}
		if (strstr($value,'%')) 
		{
			$cashback = $value;
		}
		elseif (strstr($value,'points')) 
		{
			$cashback = str_replace("points"," ".CBE1_POINTS,$value);
		}
		else
		{
			switch (SITE_CURRENCY_FORMAT)
			{
				case "1": $cashback = SITE_CURRENCY.$value; break;
				case "2": $cashback = SITE_CURRENCY." ".$value; break;
				case "3": $cashback = SITE_CURRENCY.number_format($value, 2, ',', ''); break;
				case "4": $cashback = $value." ".SITE_CURRENCY; break;
				case "5": $cashback = $value.SITE_CURRENCY; break;
				default: $cashback = SITE_CURRENCY.$value; break;
			}
		}

		return $cashback;
	}
}


/**
 * Returns time left
 * @return	string	time left
*/

if (!function_exists('GetTimeLeft')) {
	function GetTimeLeft($time_left)
	{
		$days		= floor($time_left / (60 * 60 * 24));
		$remainder	= $time_left % (60 * 60 * 24);
		$hours		= floor($remainder / (60 * 60));
		$remainder	= $remainder % (60 * 60);
		$minutes	= floor($remainder / 60);
		$seconds	= $remainder % 60;

		$days == 1 ? $dw = "day" : $dw = "days";
		$hours == 1 ? $hw = "hr" : $hw = "hrs";
		$minutes == 1 ? $mw = "min" : $mw = "mins";
		$seconds == 1 ? $sw = "second" : $sw = "seconds";

		if ($time_left > 0)
		{
			//$new_time_left = $days." $dw ".$hours." $hw ".$minutes." $mw";
			$new_time_left = $days." $dw ".$hours." $hw";
			return $new_time_left;
		}
		else
		{
			return "<span class='expired'>expired</span>";
		}
	}
}


/**
 * Returns member's referrals total
 * @param	$userid		User's ID
 * @return	string		member's referrals total
*/

if (!function_exists('GetReferralsTotal')) {
	function GetReferralsTotal($userid)
	{
		$query = "SELECT COUNT(*) AS total FROM cashbackengine_users WHERE ref_id='".(int)$userid."'";
		$result = smart_mysql_query($query);

		if (mysql_num_rows($result) > 0)
		{
			$row = mysql_fetch_array($result);
			return $row['total'];
		}
	}
}


/**
 * Returns  member's current balance
 * @param	$userid					User's ID
 * @param	$hide_currency_option	Hide or show currency sign
 * @return	string					member's current balance
*/

if (!function_exists('GetUserBalance')) {
	function GetUserBalance($userid, $hide_currency_option = 0)
	{
		$query = "SELECT SUM(amount) AS total FROM cashbackengine_transactions WHERE user_id='".(int)$userid."' AND status='confirmed'";
		$result = smart_mysql_query($query);

		if (mysql_num_rows($result) != 0)
		{
			$row_confirmed = mysql_fetch_array($result);

			if ($row_confirmed['total'] > 0)
			{
				$row_paid = mysql_fetch_array(smart_mysql_query("SELECT SUM(amount) AS total FROM cashbackengine_transactions WHERE user_id='".(int)$userid."' AND ((status='paid' OR status='request') OR (payment_type='Withdrawal' AND status='declined'))"));

				$balance = $row_confirmed['total'] - $row_paid['total'];

				return DisplayMoney($balance, $hide_currency_option);
			}
			else
			{
				return DisplayMoney(0, $hide_currency_option);
			}

		}
		else
		{
			return DisplayMoney("0.00", $hide_currecy_option);
		}
	}
}


/**
 * Returns date of last transaction
 * @param	$userid		User's ID
 * @return	mixed		date of last transaction or false
*/

if (!function_exists('GetBalanceUpdateDate')) {
	function GetBalanceUpdateDate($userid)
	{
		$result = smart_mysql_query("SELECT DATE_FORMAT(updated, '%e %b %Y %h:%i %p') AS last_process_date FROM cashbackengine_transactions WHERE user_id='".(int)$userid."' ORDER BY updated DESC LIMIT 1");
		if (mysql_num_rows($result) != 0)
		{
			$row = mysql_fetch_array($result);
			return $row['last_process_date'];
		}
		else
		{
			return false;
		}

	}
}


/**
 * Add/Deduct money from member's balance
 * @param	$userid		User's ID
 * @param	$amount		Amount
 * @param	$action		Action
*/

if (!function_exists('UpdateUserBalance')) {
	function UpdateUserBalance($userid, $amount, $action)
	{
		$userid = (int)$userid;

		if ($action == "add")
		{
			smart_mysql_query("INSERT INTO cashbackengine_transactions SET user_id='$userid', amount='$amount', status='confirmed'");
		}
		elseif ($action == "deduct")
		{
			smart_mysql_query("INSERT INTO cashbackengine_transactions SET user_id='$userid', amount='$amount', status='deducted'");
		}
	}
}


/**
 * Returns total of member's requested money
 * @return	string	total
*/

if (!function_exists('GetRequestsTotal')) {
	function GetRequestsTotal()
	{
		$result = smart_mysql_query("SELECT COUNT(*) AS total FROM cashbackengine_transactions WHERE status='request'");
		$row = mysql_fetch_array($result);
		return $row['total'];
	}
}


/**
 * Returns total of members friends invitations
 * @return	string	total
*/

if (!function_exists('GetInvitationsTotal')) {
	function GetInvitationsTotal()
	{
		$result = smart_mysql_query("SELECT COUNT(*) AS total FROM cashbackengine_invitations");
		$row = mysql_fetch_array($result);
		return $row['total'];
	}
}


/**
 * Returns member's pending cashback
 * @return	string	member's pending cashback
*/

if (!function_exists('GetPendingBalance')) {
	function GetPendingBalance()
	{
		global $userid;
		$result = smart_mysql_query("SELECT SUM(amount) AS total FROM cashbackengine_transactions WHERE user_id='".(int)$userid."' AND status='pending'");
		$row = mysql_fetch_array($result);
		$total = DisplayMoney($row['total']);
		return $total;
	}
}


/**
 * Returns member's declined cashback
 * @return	string	member's declined cashback
*/

if (!function_exists('GetDeclinedBalance')) {
	function GetDeclinedBalance()
	{
		global $userid;
		$result = smart_mysql_query("SELECT SUM(amount) AS total FROM cashbackengine_transactions WHERE user_id='".(int)$userid."' AND status='declined'");
		$row = mysql_fetch_array($result);
		$total = DisplayMoney($row['total']);
		return $total;
	}
}


/**
 * Returns member's lifetime cashback
 * @return	string	member's lifetime cashback
*/

if (!function_exists('GetLifetimeCashback')) {
	function GetLifetimeCashback()
	{
		global $userid;
		// all confirmed payments
		$row = mysql_fetch_array(smart_mysql_query("SELECT SUM(amount) AS total FROM cashbackengine_transactions WHERE user_id='".(int)$userid."' AND status='confirmed'"));
		// "paid" payments
		$row2 = mysql_fetch_array(smart_mysql_query("SELECT SUM(amount) AS total FROM cashbackengine_transactions WHERE user_id='".(int)$userid."' AND status='paid'"));
		$total = $row['total'] - $row['total2'];
		$total = DisplayMoney($total);
		return $total;
	}
}


/**
 * Returns cash out requested for member
 * @return	string	requested cash value
*/

if (!function_exists('GetCashOutRequested')) {
	function GetCashOutRequested()
	{
		global $userid;
		$result = smart_mysql_query("SELECT SUM(amount) AS total FROM cashbackengine_transactions WHERE user_id='".(int)$userid."' AND status='request'");
		$row = mysql_fetch_array($result);
		$total = DisplayMoney($row['total']);
		return $total;
	}
}


/**
 * Returns cash out processed for member
 * @return	string	cash out processed value
*/

if (!function_exists('GetCashOutProcessed')) {
	function GetCashOutProcessed()
	{
		global $userid;
		$result = smart_mysql_query("SELECT SUM(amount) AS total FROM cashbackengine_transactions WHERE user_id='".(int)$userid."' AND status='paid'");
		$row = mysql_fetch_array($result);
		$total = DisplayMoney($row['total']);
		return $total;
	}
}


/**
 * Returns total of new member's messages from administrator
 * @return	integer		total of new messages for member from administrator
*/

if (!function_exists('GetMemberMessagesTotal')) {
	function GetMemberMessagesTotal()
	{
		$userid	= $_SESSION['userid'];
		$result = smart_mysql_query("SELECT COUNT(*) AS total FROM cashbackengine_messages_answers WHERE user_id='".(int)$userid."' AND is_admin='1' AND viewed='0'");
		$row = mysql_fetch_array($result);

		if ($row['total'] == 0)
		{
			$result = smart_mysql_query("SELECT COUNT(*) AS total FROM cashbackengine_messages WHERE user_id='".(int)$userid."' AND is_admin='1' AND viewed='0'");
			$row = mysql_fetch_array($result);
		}
		return (int)$row['total'];
	}
}


/**
 * Returns total of new messages for admin
 * @return	integer		total of new messages for admin from members
*/

if (!function_exists('GetMessagesTotal')) {
	function GetMessagesTotal()
	{
		$result = smart_mysql_query("SELECT COUNT(*) AS total FROM cashbackengine_messages WHERE is_admin='0' AND viewed='0'");
		$row = mysql_fetch_array($result);
		return (int)$row['total'];
	}
}


/**
 * Returns total of users which added retialer to their favorites list
 * @return	integer		total of new messages for admin from members
*/

if (!function_exists('GetFavoritesTotal')) {
	function GetFavoritesTotal($retailer_id)
	{
		$result = smart_mysql_query("SELECT COUNT(*) AS total FROM cashbackengine_favorites WHERE retailer_id='".(int)$retailer_id."'");
		$row = mysql_fetch_array($result);
		return (int)$row['total'];
	}
}


/**
 * Returns payment method name by payment method ID
 * @return	string	payment method name
*/

if (!function_exists('GetPaymentMethodByID')) {
	function GetPaymentMethodByID($pmethod_id)
	{
		$result = smart_mysql_query("SELECT pmethod_title FROM cashbackengine_pmethods WHERE pmethod_id='".(int)$pmethod_id."' LIMIT 1");
		$total = mysql_num_rows($result);

		if ($total > 0)
		{
			$row = mysql_fetch_array($result);
			return $row['pmethod_title'];
		}
		else
		{
			return "Unknown";
		}
	}
}


/**
 * Returns random string
 * @param	$len	string length
 * @param	$chars	chars in the string
 * @return	string	random string
*/

if (!function_exists('GenerateRandString')) {
	function GenerateRandString($len, $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789')
	{
		$string = '';
		for ($i = 0; $i < $len; $i++)
		{
			$pos = rand(0, strlen($chars)-1);
			$string .= $chars{$pos};
		}
		return $string;
	}
}


/**
 * Returns random payment's reference ID
 * @return	string	Reference ID
*/

if (!function_exists('GenerateReferenceID')) {
	function GenerateReferenceID()
	{
		unset($num);

		$num = GenerateRandString(11,"0123456789");
    
		$check = smart_mysql_query("SELECT * FROM cashbackengine_transactions WHERE reference_id='$num'");
    
		if (mysql_num_rows($check) == 0)
		{
			return $num;
		}
		else
		{
			return GenerateOrderID();
		}
	}
}


/**
 * Returns Encrypted password
 * @param	$password	User's ID
 * @return	string		encrypted password
*/

if (!function_exists('PasswordEncryption')) {
	function PasswordEncryption($password)
	{
		return md5(sha1($password));
	}
}


/**
 * Check user login
 * @return	boolen			false or true
*/

function CheckCookieLogin()
{
    $uname = mysql_real_escape_string($_COOKIE['usname']);

	if (!empty($uname))
	{
        $check_query = "SELECT * FROM cashbackengine_users WHERE login_session='$uname' LIMIT 1";
		$check_result = mysql_query($check_query);
		
		if (mysql_num_rows($check_result) > 0)
		{
			$row = mysql_fetch_array($check_result);
			
			$_SESSION['userid'] = $row['user_id'];
			$_SESSION['FirstName'] = $row['fname'];

			setcookie("usname", $uname, time()+3600*24*365, '/');

			return true;
		}
		else
		{
			return false;
		}
    }
	else
	{
		return false;
	}
}


/**
 * Returns most popular retailer's ID of the week
 * @return	integer		retailer's ID
*/

if (!function_exists('GetStoreofWeek')) {
	function GetStoreofWeek()
	{
		$result = smart_mysql_query("SELECT COUNT(*) AS total, retailer_id FROM cashbackengine_clickhistory WHERE date_sub(curdate(), interval 7 day) <= added GROUP BY retailer_id ORDER BY total DESC LIMIT 1");
	
		if (mysql_num_rows($result) == 0)
		{
			$result = smart_mysql_query("SELECT retailer_id FROM cashbackengine_retailers WHERE (end_date='0000-00-00 00:00:00' OR end_date > NOW()) AND status='active' ORDER BY RAND() LIMIT 1");
		}
		else
		{
			$row = mysql_fetch_array($result);
			return (int)$row['retailer_id'];
		}
	}
}


/**
 * Saves referral's ID in cookies
 * @param	$ref_id		Referrals's ID
*/

if (!function_exists('setReferal')) {
	function setReferal($ref_id)
	{
		//set up cookie for one month period
		setcookie("referer_id", $ref_id, time()+(60*60*24*30));
	}
}


/**
 * Check if user logged in
 * @return	boolen		false or true
*/

if (!function_exists('isLoggedIn')) {
	function isLoggedIn()
	{
		if (!(isset($_SESSION['userid']) && is_numeric($_SESSION['userid'])))
			return false;
		else
			return true;
	}
}


/**
 * Returns user's information
 * @param	$user_id	User ID
 * @return	string		user name, or "User not found"
*/

if (!function_exists('GetUsername')) {
	function GetUsername($user_id)
	{
		$result = smart_mysql_query("SELECT * FROM cashbackengine_users WHERE user_id='".(int)$user_id."' LIMIT 1");
		
		if (mysql_num_rows($result) != 0)
		{
			$row = mysql_fetch_array($result);
			return $row['fname']." ".$row['lname'];
		}
		else
		{
			return "User not found";
		}
	}
}


/**
 * Returns setting value by setting's key
 * @param	$setting_key	Setting's Key
 * @return	string	setting's value
*/

if (!function_exists('GetSetting')) {
	function GetSetting($setting_key)
	{
		$setting_result = smart_mysql_query("SELECT setting_value FROM cashbackengine_settings WHERE setting_key='".$setting_key."'");
		$setting_total = mysql_num_rows($setting_result);

		if ($setting_total > 0)
		{
			$setting_row = mysql_fetch_array($setting_result);
			$setting_value = $setting_row['setting_value'];
		}
		else
		{
			die ("config settings not found");
		}

		return $setting_value;
	}
}


/**
 * Returns top menu pages links
 * @return	string	top menu pages links
*/

if (!function_exists('ShowTopPages')) {
	function ShowTopPages()
	{
		$language = mysql_real_escape_string(USER_LANGUAGE);
		$result = smart_mysql_query("SELECT * FROM cashbackengine_content WHERE (language='' OR language='$language') AND (page_location='top' OR page_location='topfooter') AND status='active'");
		if (mysql_num_rows($result) > 0)
		{
			while ($row = mysql_fetch_array($result))
			{
				echo "<a href=\"".SITE_URL."content.php?id=".$row['content_id']."\">".$row['link_title']."</a> ";
			}
		}
	}
}


/**
 * Returns footer menu pages links
 * @return	string	footer menu pages links
*/

if (!function_exists('ShowFooterPages')) {
	function ShowFooterPages()
	{
		$language = mysql_real_escape_string(USER_LANGUAGE);
		$result = smart_mysql_query("SELECT * FROM cashbackengine_content WHERE (language='' OR language='$language') AND (page_location='footer' OR page_location='topfooter') AND status='active'");
		if (mysql_num_rows($result) > 0)
		{
			while ($row = mysql_fetch_array($result))
			{
				echo "<a href=\"".SITE_URL."content.php?id=".$row['content_id']."\">".$row['link_title']."</a> &middot; ";
			}
		}
	}
}


/**
 * Returns content for static pages
 * @param	$content_name	Content's Name or Content ID
 * @return	array	(1) - Page Title, (2) - Page Text
*/

if (!function_exists('GetContent')) {
	function GetContent($content_name)
	{
		$language = mysql_real_escape_string(USER_LANGUAGE);

		if (is_numeric($content_name))
		{
			$content_id = (int)$content_name;
			$content_result = smart_mysql_query("SELECT * FROM cashbackengine_content WHERE (language='' OR language='$language') AND content_id='".$content_id."' LIMIT 1");
		}
		else
		{
			$content_result = smart_mysql_query("SELECT * FROM cashbackengine_content WHERE (language='' OR language='$language') AND name='".$content_name."' LIMIT 1");
		}

		$content_total = mysql_num_rows($content_result);

		if ($content_total > 0)
		{
			$content_row			= mysql_fetch_array($content_result);
			$contents['link_title'] = stripslashes($content_row['link_title']);
			$contents['title']		= stripslashes($content_row['title']);
			$contents['text']		= stripslashes($content_row['description']);
		}
		else
		{
			$contents['title']	= CBE1_CONTENT_NO;
			$contents['text']	= "<p align='center'>".CBE1_CONTENT_NO_TEXT."<br/><br/><a class='goback' href='".SITE_URL."'>".CBE1_CONTENT_GOBACK."</a></p>";
		}

		return $contents;
	}
}


/**
 * Returns content for email template
 * @param	$email_name	Email Template Name
 * @return	array	(1) - Email Subject, (2) - Email Message
*/

if (!function_exists('GetEmailTemplate')) {
	function GetEmailTemplate($email_name)
	{
		$language = mysql_real_escape_string(USER_LANGUAGE);
		
		$etemplate_result = smart_mysql_query("SELECT * FROM cashbackengine_email_templates WHERE language='".$language."' AND email_name='".$email_name."' LIMIT 1");
		$etemplate_total = mysql_num_rows($etemplate_result);

		if ($etemplate_total > 0)
		{
			$etemplate_row = mysql_fetch_array($etemplate_result);
			$etemplate['email_subject'] = stripslashes($etemplate_row['email_subject']);
			$etemplate['email_message'] = stripslashes($etemplate_row['email_message']);

			$etemplate['email_message'] = "<html>
								<head>
									<title>".$etemplate['email_subject']."</title>
								</head>
								<body>
								<table width='80%' border='0' cellpadding='10'>
								<tr>
									<td align='left' valign='top'>".$etemplate['email_message']."</td>
								</tr>
								</table>
								</body>
							</html>";
		}
		else
		{
			$etemplate['email_subject'] = CBE1_EMAIL_NO_SUBJECT;
			$etemplate['email_message'] = CBE1_EMAIL_NO_MESSAGE;
		}

		return $etemplate;
	}
}


/**
 * Sends email
 * @param	$recipient	Email Recipient
 * @param	$subject	Email Subject
 * @param	$message	Email Message
*/

if (!function_exists('SendEmail')) {
	function SendEmail($recipient, $subject, $message)
	{
		$emessage = $message;
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
		$headers .= 'From: '.SITE_TITLE.' <'.SITE_MAIL.'>' . "\r\n";
		@mail($recipient, $subject, $emessage, $headers);
	}
}


/**
 * Returns list of categories
 * @param	$cat_id Category ID
 * @param	$level	Level
 * @return	string	categories list
*/

if (!function_exists('ShowCategories')) {
	function ShowCategories($cat_id, $level=0)
	{
		$result = smart_mysql_query("SELECT category_id, parent_id, name FROM cashbackengine_categories WHERE parent_id='$cat_id' ORDER BY sort_order, name");
		
		if (mysql_num_rows($result) >= 1)
		{
			while($row = mysql_fetch_array($result))
			{
				$pxs = $level*10;
				if ($_GET['cat'] === $row['category_id']) $actives = " class=\"active\""; else $actives = "";
				echo "<ul style='padding-left:".$pxs."px;margin:0;'><li".$actives."><a href=\"".SITE_URL."retailers.php?cat=".$row['category_id']."\">".$row['name']."</a></li></ul>";
				ShowCategories($row['category_id'], $level+1);
			}
		}
	}
}


/**
 * Returns category name
 * @param	$category_id	Category ID
 * @param	$description	show/hide descritpion
 * @return	string			category name
*/

if (!function_exists('getCategory')) {
	function getCategory($category_id, $description = 0)
	{
		if (isset($category_id) && is_numeric($category_id) && $category_id != 0)
		{
			$query = "SELECT * FROM cashbackengine_categories WHERE category_id='".(int)$category_id."' LIMIT 1";
			$result = smart_mysql_query($query);
			if (mysql_num_rows($result) > 0)
			{
				$row = mysql_fetch_array($result);
				
				if ($description == 1)
				{
					if ($row['description'] != "") return "<p class='category_description'>".$row['description']."</p>";
				}
				else
				{
					return $row['name'];
				}
			}
			else
			{
				if ($description != 1) return CBE1_STORES_CNO;
			}
		}
		else
		{
			if ($description != 1) return CBE1_STORES_STORES;
		}
	}
}


/**
 * Returns retailer name
 * @param	$retailer_id	Retailer ID
 * @return	string			retailer name
*/

if (!function_exists('GetStoreName')) {
	function GetStoreName($retailer_id)
	{
		$result = smart_mysql_query("SELECT * FROM cashbackengine_retailers WHERE retailer_id='".(int)$retailer_id."' LIMIT 1");
		$row = mysql_fetch_array($result);
		return $row['title'];
	}
}


/**
 * Returns retailer's rating
 * @param	$retailer_id	Retailer ID
 * @return	string			rating
*/

if (!function_exists('GetStoreRating')) {
	function GetStoreRating($retailer_id, $show_stars = 0)
	{
		$result = smart_mysql_query("SELECT AVG(rating) as store_rating FROM cashbackengine_reviews WHERE retailer_id='".(int)$retailer_id."'");
		if (mysql_num_rows($result) > 0)
		{
			$row = mysql_fetch_array($result);
			$rating = $row['store_rating'];
			$rating = number_format($rating, 2, '.', '');
		}
		else
		{
			return "----";
		}

		if ($show_stars == 1)
		{
			$rating_stars = $rating*20;
			$store_rating = "<div class='rating'><div class='cover'></div><div class='progress' style='width: ".$rating_stars."%;'></div></div>";
			return $store_rating;
		}
		else
		{
			return $rating;
		}		
	}
}



/**
 * Returns retailer's countries
 * @param	$retailer_id		Retailer ID
 * @param	$show_only_images	show/hide country name
 * @return	string				retailer's countries
*/

if (!function_exists('GetStoreCountries')) {
	function GetStoreCountries($retailer_id, $show_only_images = 1)
	{
		$sql_store_countires = smart_mysql_query("SELECT rc.country_id, c.* FROM cashbackengine_retailer_to_country rc, cashbackengine_countries c WHERE rc.country_id=c.country_id AND rc.retailer_id='".(int)$retailer_id."' ORDER BY c.name");

		if (mysql_num_rows($sql_store_countires) > 0)
		{
			$store_countires = "";
			while ($row_store_countires = mysql_fetch_array($sql_store_countires))
			{
				if ($show_only_images == 1)
					$store_countires .= "<img src='".SITE_URL."images/flags/".strtolower($row_store_countires['code']).".png' alt='".$row_store_countires['name']."' title='".$row_store_countires['name']."' align='absmiddle' /> ";
				else
					$store_countires .= "<span class='country_list'><img src='".SITE_URL."images/flags/".strtolower($row_store_countires['code']).".png' alt='".$row_store_countires['name']."' title='".$row_store_countires['name']."' /> ".$row_store_countires['name']."</span>";
			}

			return $store_countires;
		}
		else
		{
			//return "<img src='".SITE_URL."images/flags/worldwide.png' alt='".CBE1_STORES_WORLDWIDE."' title='".CBE1_STORES_WORLDWIDE."' align='absmiddle' /> ";
		}
	}
}


/**
 * Returns country name
 * @param	$country_id			Country ID
 * @param	$show_only_icon		Show/Hide country name
 * @return	string				country name
*/

if (!function_exists('GetCountry')) {
	function GetCountry($country_id, $show_only_icon = 0)
	{
		$result = smart_mysql_query("SELECT * FROM cashbackengine_countries WHERE country_id='".(int)$country_id."' LIMIT 1");

		if (mysql_num_rows($result) > 0)
		{
			$row = mysql_fetch_array($result);
			
			if ($show_only_icon == 1)
				$country_name = "<img src='".SITE_URL."images/flags/".strtolower($row['code']).".png' alt='".$row['name']."' title='".$row['name']."' align='absmiddle'/>";
			else
				$country_name = "<img src='".SITE_URL."images/flags/".strtolower($row['code']).".png' alt='".$row['name']."' title='".$row['name']."' align='absmiddle' /> ".$row['name'];
		
			return $country_name;
		}
	}
}


/**
 * Returns store's coupons total
 * @param	$retailer_id	Retailer ID
 * @return	integer			store's coupons total
*/

if (!function_exists('GetStoreCouponsTotal')) {
	function GetStoreCouponsTotal($retailer_id)
	{
		$result = smart_mysql_query("SELECT COUNT(*) AS total FROM cashbackengine_coupons WHERE retailer_id='".(int)$retailer_id."' AND status='active'");
		$row = mysql_fetch_array($result);
		return (int)$row['total'];
	}
}


/**
 * Returns total of new submitted coupons
 * @return	integer		total of new submitted coupons
*/

if (!function_exists('GetNewCouponsTotal')) {
	function GetNewCouponsTotal()
	{
		$result = smart_mysql_query("SELECT COUNT(*) AS total FROM cashbackengine_coupons WHERE viewed='0'");
		$row = mysql_fetch_array($result);
		return (int)$row['total'];
	}
}


/**
 * Returns exclusive coupons total
 * @return	integer		exclusive coupons total
*/

if (!function_exists('GetExclusiveCouponsTotal')) {
	function GetExclusiveCouponsTotal()
	{
		$result = smart_mysql_query("SELECT COUNT(*) AS total FROM cashbackengine_coupons WHERE exclusive='1' AND status='active'");
		$row = mysql_fetch_array($result);
		return (int)$row['total'];
	}
}


/**
 * Returns expiring soon coupons total
 * @return	integer		expiring soon coupons total
*/

if (!function_exists('GetExpiringCouponsTotal')) {
	function GetExpiringCouponsTotal()
	{
		$result = smart_mysql_query("SELECT COUNT(*) AS total FROM cashbackengine_coupons WHERE end_date!='0000-00-00 00:00:00' AND (end_date BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 3 DAY)) AND status='active'");
		$row = mysql_fetch_array($result);
		return (int)$row['total'];
	}
}



/**
 * Returns user's clicks total
 * @param	$user_id	User ID
 * @return	integer		user's clicks total
*/

if (!function_exists('GetUserClicksTotal')) {
	function GetUserClicksTotal($user_id)
	{
		$result = smart_mysql_query("SELECT COUNT(*) AS total FROM cashbackengine_clickhistory WHERE user_id='".(int)$user_id."'");
		$row = mysql_fetch_array($result);
		return number_format($row['total']);
	}
}


/**
 * Returns store's reviews total
 * @param	$retailer_id	Retailer ID
 * @param	$all			calculates all review
 * @param	$word			show/hide word
 * @return	integer			store's reviews total
*/

if (!function_exists('GetStoreReviewsTotal')) {
	function GetStoreReviewsTotal($retailer_id, $all = 0, $word = 1)
	{
		if ($all == 1)
			$result = smart_mysql_query("SELECT COUNT(*) AS total FROM cashbackengine_reviews WHERE retailer_id='".(int)$retailer_id."'");
		else
			$result = smart_mysql_query("SELECT COUNT(*) AS total FROM cashbackengine_reviews WHERE retailer_id='".(int)$retailer_id."' AND status='active'");
		
		$row = mysql_fetch_array($result);
		$total_reviews = (int)$row['total'];

		if ($word == 1)
		{
			if ($total_reviews == 0)
				$total_reviews = "No reviews";
			else if ($total_reviews == 1)
				$total_reviews .= " review";
			else
				$total_reviews .= " reviews";
		}

		return $total_reviews;
	}	
}


/**
 * Returns user's reviews total
 * @param	$user_id	User ID
 * @return	integer		user's reviews total
*/

if (!function_exists('GetUserReviewsTotal')) {
	function GetUserReviewsTotal($user_id)
	{
		$result = smart_mysql_query("SELECT COUNT(*) AS total FROM cashbackengine_reviews WHERE user_id='".(int)$user_id."' AND status='active'");
		$row = mysql_fetch_array($result);
		return (int)$row['total'];
	}
}


/**
 * Returns store reports total number
 * @return	integer		store reports total
*/

if (!function_exists('GetRetailerReportsTotal')) {
	function GetRetailerReportsTotal()
	{
		$result = smart_mysql_query("SELECT COUNT(*) AS total FROM cashbackengine_reports WHERE retailer_id<>'0'"); // AND viewed='0'
		$row = mysql_fetch_array($result);
		return (int)$row['total'];
	}
}


/**
 * Returns stores total
 * @return	integer		stores total
*/

if (!function_exists('GetStoresTotal')) {
	function GetStoresTotal()
	{
		$result = smart_mysql_query("SELECT COUNT(*) AS total FROM cashbackengine_retailers WHERE (end_date='0000-00-00 00:00:00' OR end_date > NOW()) AND status='active'");
		$row = mysql_fetch_array($result);
		return (int)$row['total'];
	}
}


/**
 * Returns coupons total
 * @return	integer		coupons total
*/

if (!function_exists('GetCouponsTotal')) {
	function GetCouponsTotal()
	{
		$result = smart_mysql_query("SELECT COUNT(*) AS total FROM cashbackengine_coupons WHERE status='active'");
		$row = mysql_fetch_array($result);
		return (int)$row['total'];
	}
}


/**
 * Returns paid cashback total
 * @return	string		paid cashback total
*/

if (!function_exists('GetCashbackTotal')) {
	function GetCashbackTotal()
	{
		$result = smart_mysql_query("SELECT SUM(amount) AS total FROM cashbackengine_transactions WHERE status='confirmed'");
		$row = mysql_fetch_array($result);
		$total_cashback = DisplayMoney($row['total']);
		return $total_cashback;
	}
}


/**
 * Returns users total
 * @return	integer		users total
*/

if (!function_exists('GetUsersTotal')) {
	function GetUsersTotal()
	{
		$result = smart_mysql_query("SELECT COUNT(*) AS total FROM cashbackengine_users WHERE status='active'");
		$row = mysql_fetch_array($result);
		return (int)$row['total'];
	}
}


/**
 * Returns formatted sctring
 * @param	$str		string
 * @return	string		formatted sctring
*/

if (!function_exists('well_formed')) {
	function well_formed($str) {
		$str = strip_tags($str);
		$str = preg_replace("/[^a-zA-Z0-9_ (\n|\r\n)]+/", "", $str);
		$str = str_replace("&nbsp;", "", $str);
		$str = str_replace("&", "&amp;", $str);
		return $str;
	}
}


/**
 * Returns retailer's link
 * @param	$retailer_id		Retailer ID
 * @param	$retailer_title		Retailer Title
 * @return	string				Returns retailer's link
*/

if (!function_exists('GetRetailerLink')) {
	function GetRetailerLink($retailer_id, $retailer_title = "") {
		$retailer_id = (int)$retailer_id;
		$retailer_link = SITE_URL."view_retailer.php?id=".$retailer_id;
		return $retailer_link;
	}
}

/**
 * Print array with formatting
*/

if (!function_exists('pr')) {
	function pr($arr, $exit = 0) {
		echo '<pre>';
		print_r($arr);
		echo '</pre>';
		if($exit)
			exit;
	}
}

if (!function_exists('GetCashbackType')) {
	function GetCashbackType($cashback){
		$cashback_type = 'currency';
		if(strpos($cashback,'%')!== false)
			$cashback_type = '%';
		elseif(strpos($cashback,'points')!== false) 
			$cashback_type = 'points';
			
		return $cashback_type;
	}
}

if (!function_exists('RemoveCashbackType')) {
	function RemoveCashbackType($cashback){
		return str_replace(array('%', 'points'), array('', ''), $cashback);
	}
}

if (!function_exists('GetTrendingSaleCoupons')) {
	function GetTrendingSaleCoupons(){
		$coupons = array();
		$r = smart_mysql_query("SELECT r.cashback,r.retailer_id, r.image as retailer_image,r.image_I,r.image_II,r.image_III, c.title, c.coupon_id, c.description FROM cashbackengine_coupons c 
		LEFT JOIN cashbackengine_retailers r ON c.retailer_id=r.retailer_id
		WHERE 
		c.status='active' and 
		c.trending_sale='1' and
		(c.start_date<=NOW() AND (c.end_date='0000-00-00 00:00:00' OR c.end_date > NOW()))
		order by c.sort_order asc
		");
		
		if(mysql_num_rows($r)){
			while($row = mysql_fetch_array($r, MYSQL_ASSOC)){
				$coupons[] = $row;					
			}	
		}
		
		return $coupons;
	}
}

if (!function_exists('GetAdvertisements')) {
	function GetAdvertisements($ids = array()){
		$advertisements = array();
		$where = '';
		if($ids){
			$where = ' where advertisement_id in ('.implode(',', $ids).')';			
		}
		$query = 'select * from cashbackengine_advertisements'.$where;
		$r = smart_mysql_query($query);
		
		if(mysql_num_rows($r)){
			while($row = mysql_fetch_array($r, MYSQL_ASSOC)){
				$advertisements[$row['advertisement_id']] = $row;					
			}	
		}
		return $advertisements;
	}
}

if (!function_exists('GetMenuCategories')) {
function GetMenuCategories ()
{
	$result = smart_mysql_query("SELECT name, category_id FROM cashbackengine_categories WHERE show_in_menu=1 ORDER BY sort_order, name");
	$total = mysql_num_rows($result);

	if ($total > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$category_id = $row['category_id'];
			$allcategories[$category_id] = $row;
		}
	}
	return $allcategories;
}
}



?>