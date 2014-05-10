<?php
/*******************************************************************\
 * CashbackEngine v2.1
 * http://www.CashbackEngine.net
 *
 * Copyright (c) 2010-2014 CashbackEngine Software. All rights reserved.
 * ------------ CashbackEngine IS NOT FREE SOFTWARE --------------
\*******************************************************************/

	session_start();
	require_once("inc/iflogged.inc.php");
	require_once("inc/config.inc.php");

	if (!(FACEBOOK_CONNECT == 1 && FACEBOOK_APPID != "" && FACEBOOK_SECRET != ""))
	{
		//header ("Location: index.php");
		echo 'index.php';
		exit();
	}
	
	if(isset($_POST['action']) && $_POST['action']=='signupFB' && isset($_POST['id']) && $_POST!="")
		$user=$_POST;
	
	if ($user != "")
	{
	  try {
		$fuserid		= mysql_real_escape_string($user["id"]);  // ID FROM THE FACEBOOK API
		$fusername		= mysql_real_escape_string($user["username"]); // FULL NAME FROM THE FB API
		$newtoken		= base64_encode($fuserid."::".$fusername);
		$ip				= getenv("REMOTE_ADDR");//echo $fuserid;exit;
		
		$check_query = smart_mysql_query("SELECT * FROM cashbackengine_users WHERE auth_provider='facebook' AND auth_uid='".$fuserid."' LIMIT 1");

		if (mysql_num_rows($check_query) > 0)
		{	
			$row = mysql_fetch_assoc($check_query);
		
			if ($row['status'] == 'inactive')
			{	
				//header("Location: login.php?msg=2");
				echo 'login.php?msg=2';
				exit();
			}
			
			smart_mysql_query("UPDATE cashbackengine_users SET last_ip='$ip', login_count=login_count+1, last_login=NOW() WHERE user_id='".(int)$row['user_id']."' LIMIT 1");

			if (!session_id()) session_start();
			$_SESSION['userid'] = $row['user_id'];
			$_SESSION['FirstName'] = $row['fname'];

			if ($_SESSION['goRetailerID'])
			{
				$goRetailerID = (int)$_SESSION['goRetailerID'];
				$redirect_url = GetRetailerLink($goRetailerID, GetStoreName($goRetailerID));
				unset($_SESSION['goRetailerID']);
			}
			else
			{
				//$redirect_url = "myaccount.php";
				echo 'myaccount.php';
			}
			//header("Location: ".$redirect_url);
			exit();
		}
		else
		{
				 
			$email				= mysql_real_escape_string($user['email']);
			$passcode			= mysql_real_escape_string($user['id']);
			$username			= mysql_real_escape_string($user['username']);
			$password			= PasswordEncryption(generatePassword(10));
			$name				= explode('.',$username);
			$fname				= mysql_real_escape_string($user['first_name']);
			$lname				= mysql_real_escape_string($user['last_name']);
			$country			= "227";
			$unsubscribe_key	= GenerateKey($username);
			$ip					= getenv("REMOTE_ADDR");
			if (isset($_COOKIE['referer_id']) && is_numeric($_COOKIE['referer_id'])) $ref_id = (int)$_COOKIE['referer_id']; else $ref_id = 0;
			
			$insert_query = "INSERT INTO cashbackengine_users SET username='$email', password='$password', email='$email', fname='$fname', lname='$lname', country='$country', phone='', ref_id='$ref_id', newsletter='1', ip='$ip', status='active', auth_provider='facebook', auth_uid='$passcode', activation_key='', unsubscribe_key='$unsubscribe_key', last_login=NOW(), login_count='1', last_ip='$ip', created=NOW()";

			smart_mysql_query($insert_query);
			$new_user_id = mysql_insert_id();

			
			////////////////////////////////  Send welcome message  /////////////////////////
			$etemplate = GetEmailTemplate('signup');
			$esubject = $etemplate['email_subject'];
			$emessage = $etemplate['email_message'];

			$emessage = str_replace("{first_name}", $fname, $emessage);
			$emessage = str_replace("{username}", $email, $emessage);
			$emessage = str_replace("{password}", $password, $emessage);
			$emessage = str_replace("{login_url}", SITE_URL."login.php", $emessage);

			$to_email = $fname.' '.$lname.' <'.$email.'>';
			$subject = $esubject;
			$message = $emessage;

			$headers = 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
			$headers .= 'From: '.SITE_TITLE.' <'.NOREPLY_MAIL.'>' . "\r\n";
				
			@mail($to_email, $subject, $message, $headers);
			////////////////////////////////////////////////////////////////////////////////

			if (!session_id()) session_start();
			$_SESSION['userid']		= $new_user_id;
			$_SESSION['FirstName']	= $fname;
				echo 'myaccount.php?msg=welcome';
			//header("Location: myaccount.php?msg=welcome"); // forward new user to member dashboard
			exit();
		}

	  } catch (FacebookApiException $e) {
		$user = null;
	  }
	}
	else
	{
		$params = array(
			'canvas' => 1,
			'scope'  => 'email,offline_access,publish_stream,user_birthday,user_location',
			'fbconnect' => 1,
			'redirect_uri' => 'https://apps.facebook.com/'.FACEBOOK_APPID,
		);
		$fb_login_url = $facebook->getLoginUrl($params);
		header("Location: ".$fb_login_url);
		exit();
	}

?>