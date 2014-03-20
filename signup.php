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


	if (isset($_POST['action']) && $_POST['action'] == "signup")
	{
		unset($errs);
		$errs = array();

		$fname		= mysql_real_escape_string(ucfirst(strtolower(getPostParameter('fname'))));
		$lname		= mysql_real_escape_string(ucfirst(strtolower(getPostParameter('lname'))));
		$email		= mysql_real_escape_string(strtolower(getPostParameter('email')));
		$cemail		= mysql_real_escape_string(strtolower(getPostParameter('cemail')));
		$username	= mysql_real_escape_string(strtolower(getPostParameter('email')));
		$pwd		= mysql_real_escape_string(getPostParameter('password'));
		$pwd2		= mysql_real_escape_string(getPostParameter('password2'));
		$country	= (int)getPostParameter('country');
		$phone		= mysql_real_escape_string(getPostParameter('phone'));
		$captcha	= mysql_real_escape_string(getPostParameter('captcha'));
		$newsletter	= (int)getPostParameter('newsletter');
		$tos		= (int)getPostParameter('tos');
		$ref_id		= (int)getPostParameter('referer_id');
		$ip			= getenv("REMOTE_ADDR");

		if (!($fname && $lname && $email && $cemail && $pwd && $pwd2 && $country))
		{
			$errs[] = CBE1_SIGNUP_ERR;
		}

		if (SIGNUP_CAPTCHA == 1)
		{
			if (!$captcha)
			{
				$errs[] = CBE1_SIGNUP_ERR2;
			}
			else
			{
				if (empty($_SESSION['captcha']) || strcasecmp($_SESSION['captcha'], $captcha) != 0)
				{
					$errs[] = CBE1_SIGNUP_ERR3;
				}
			}
		}

		if (isset($email) && $email != "" && !preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $email))
		{
			$errs[] = CBE1_SIGNUP_ERR4;
		}

		if (isset($email) && $email != "" && isset($cemail) && $cemail != "")
		{
			if ($email !== $cemail)
			{
				$errs[] = CBE1_SIGNUP_ERR5;
			}
		}

		if (isset($pwd) && $pwd != "" && isset($pwd2) && $pwd2 != "")
		{
			if ($pwd !== $pwd2)
			{
				$errs[] = CBE1_SIGNUP_ERR6;
			}
			elseif ((strlen($pwd)) < 6 || (strlen($pwd2) < 6) || (strlen($pwd)) > 20 || (strlen($pwd2) > 20))
			{
				$errs[] = CBE1_SIGNUP_ERR7;
			}
			elseif (stristr($pwd, ' '))
			{
				$errs[] = CBE1_SIGNUP_ERR8;
			}
		}

		if (!(isset($tos) && $tos == 1))
		{
			$errs[] = CBE1_SIGNUP_ERR9;
		}

		if (count($errs) == 0)
		{
				$query = "SELECT username FROM cashbackengine_users WHERE username='$email' OR email='$email' LIMIT 1";
				$result = smart_mysql_query($query);

				if (mysql_num_rows($result) != 0)
				{
					header ("Location: signup.php?msg=exists");
					exit();
				}

				// check referral
				if ($ref_id > 0)
				{
					$check_referral_query = "SELECT email FROM cashbackengine_users WHERE user_id='$ref_id' LIMIT 1";
					$check_referral_result = smart_mysql_query($check_referral_query);

					if (mysql_num_rows($check_referral_result) != 0)
						$ref_id = $ref_id;
					else
						$ref_id = 0;
				}

				$unsubscribe_key = GenerateKey($username);

				if (ACCOUNT_ACTIVATION == 1)
				{
					$activation_key = GenerateKey($username);
					$insert_query = "INSERT INTO cashbackengine_users SET username='$username', password='".PasswordEncryption($pwd)."', email='$email', fname='$fname', lname='$lname', country='$country', phone='$phone', ref_id='$ref_id', newsletter='$newsletter', ip='$ip', status='inactive', activation_key='$activation_key', unsubscribe_key='$unsubscribe_key', created=NOW()";
				}
				else
				{
					$insert_query = "INSERT INTO cashbackengine_users SET username='$username', password='".PasswordEncryption($pwd)."', email='$email', fname='$fname', lname='$lname', country='$country', phone='$phone', ref_id='$ref_id', newsletter='$newsletter', ip='$ip', status='active', activation_key='', unsubscribe_key='$unsubscribe_key', last_login=NOW(), login_count='1', last_ip='$ip', created=NOW()";
				}

				smart_mysql_query($insert_query);
				$new_user_id = mysql_insert_id();

				if (SIGNUP_BONUS > 0)
				{
					// save SIGN UP BONUS transaction //
					$reference_id = GenerateReferenceID();
					smart_mysql_query("INSERT INTO cashbackengine_transactions SET reference_id='$reference_id', user_id='$new_user_id', payment_type='Sign Up Bonus', amount='".SIGNUP_BONUS."', status='confirmed', created=NOW(), process_date=NOW()");
					/////////////////////////////////////
				}

				// add bonus to referral, save transaction //
				if (REFER_FRIEND_BONUS > 0 && isset($ref_id) && $ref_id > 0)
				{
					$reference_id = GenerateReferenceID();
					$ref_res = smart_mysql_query("INSERT INTO cashbackengine_transactions SET reference_id='$reference_id', user_id='$ref_id', payment_type='Refer a Friend Bonus', amount='".REFER_FRIEND_BONUS."', status='pending', created=NOW()");
				}
				//////////////////////////////////////////////


				if (ACCOUNT_ACTIVATION == 1)
				{			
					////////////////////////////////  Send Message  //////////////////////////////
					$etemplate = GetEmailTemplate('activate');
					$esubject = $etemplate['email_subject'];
					$emessage = $etemplate['email_message'];

					$activate_link = SITE_URL."activate.php?key=".$activation_key;

					$emessage = str_replace("{first_name}", $fname, $emessage);
					$emessage = str_replace("{username}", $email, $emessage);
					$emessage = str_replace("{password}", $pwd, $emessage);
					$emessage = str_replace("{activate_link}", $activate_link, $emessage);

					$to_email = $fname.' '.$lname.' <'.$email.'>';
					$subject = $esubject;
					$message = $emessage;

					$headers = 'MIME-Version: 1.0' . "\r\n";
					$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
					$headers .= 'From: '.SITE_TITLE.' <'.NOREPLY_MAIL.'>' . "\r\n";
				
					@mail($to_email, $subject, $message, $headers);
					////////////////////////////////////////////////////////////////////////////////

					header("Location: activate.php?msg=1"); // show account activation message
					exit();
				}
				else
				{
					////////////////////////////////  Send welcome message  ////////////////////////
					$etemplate = GetEmailTemplate('signup');
					$esubject = $etemplate['email_subject'];
					$emessage = $etemplate['email_message'];

					$emessage = str_replace("{first_name}", $fname, $emessage);
					$emessage = str_replace("{username}", $email, $emessage);
					$emessage = str_replace("{password}", $pwd, $emessage);
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

					header("Location: myaccount.php?msg=welcome"); // forward new user to member dashboard
					exit();
				}
		}
		else
		{
			$allerrors = "";
			foreach ($errs as $errorname)
				$allerrors .= "&#155; ".$errorname."<br/>\n";
		}
	}

	///////////////  Page config  ///////////////
	$PAGE_TITLE = CBE1_SIGNUP_TITLE;
	
	require_once ("inc/header.inc.php");
	
?>

        <table width="100%" style="border-bottom: 2px solid #EFF0FB;" align="center" cellpadding="2" cellspacing="0" border="0">
        <tr>
			<td align="left" valign="middle"><h1 style="margin-bottom:0;border:none;"><?php echo CBE1_SIGNUP_TITLE; ?></h1></td>
			<td align="right" valign="bottom"><?php echo CBE1_SIGNUP_MEMBER; ?> <a href="<?php echo SITE_URL; ?>login.php"><?php echo CBE1_LOGIN_TITLE; ?></a></td>
        </tr>
        </table>

		<?php if (FACEBOOK_CONNECT == 1 && FACEBOOK_APPID != "" && FACEBOOK_SECRET != "") { ?>
			<p align="center"><a href="javascript: void(0);" onclick="facebook_login();" class="connect-f"><img src="<?php echo SITE_URL; ?>images/facebook_connect.png" /></a></p>
			<div style="border-bottom: 1px dotted #ddd; width: 400px; margin: 0 auto;">
				<div style="font-size: 11px; font-weight: bold; background-color: #FFF; color: #DDD; margin: 0 auto; top: 8px; text-align: center; width: 100px; position: relative;">or</div>
			</div><br/>
		<?php } ?>

		<?php if (isset($allerrors) || isset($_GET['msg'])) { ?>
			<div class="error_msg">
				<?php if (isset($_GET['msg']) && $_GET['msg'] == "exists") { ?>
					<?php echo CBE1_SIGNUP_ERR10; ?> <a href="<?php echo SITE_URL; ?>forgot.php"><?php echo CBE1_LOGIN_FORGOT; ?></a></font><br/>
				<?php }elseif (isset($allerrors)) { ?>
					<?php echo $allerrors; ?>
				<?php }	?>
			</div>
		<?php } ?>

		<form action="" method="post">
        <table width="100%" align="center" cellpadding="3" cellspacing="0" border="0">
          <tr>
            <td colspan="2" align="right" valign="top"><span class="req">* <?php echo CBE1_LABEL_REQUIRED; ?></span></td>
          </tr>
          <tr>
            <td width="210" align="right" valign="middle"><span class="req">* </span><?php echo CBE1_LABEL_FNAME; ?>:</td>
            <td align="left" valign="middle"><input type="text" id="fname" class="textbox" name="fname" value="<?php echo getPostParameter('fname'); ?>" size="25" /></td>
          </tr>
          <tr>
            <td align="right" valign="middle"><span class="req">* </span><?php echo CBE1_LABEL_LNAME; ?>:</td>
            <td align="left" valign="middle"><input type="text" id="lname" class="textbox" name="lname" value="<?php echo getPostParameter('lname'); ?>" size="25" /></td>
          </tr>
          <tr>
            <td align="right" valign="middle"><span class="req">* </span><?php echo CBE1_LABEL_EMAIL2; ?>:</td>
            <td align="left" valign="middle"><input type="text" id="email" class="textbox" name="email" value="<?php echo getPostParameter('email'); ?>" size="25" /></td>
          </tr>
          <tr>
            <td align="right" valign="middle"><span class="req">* </span><?php echo CBE1_LABEL_CEMAIL2; ?>:</td>
            <td align="left" valign="middle"><input type="text" id="cemail" class="textbox" name="cemail" value="<?php echo getPostParameter('cemail'); ?>" size="25" /></td>
          </tr>
          <tr>
            <td align="right" valign="middle"><span class="req">* </span><?php echo CBE1_LABEL_PWD; ?>:</td>
            <td nowrap="nowrap" align="left" valign="middle"><input type="password" id="password" class="textbox" name="password" value="" size="25" /> <span class="note"><?php echo CBE1_SIGNUP_PTEXT; ?></span></td>
          </tr>
          <tr>
            <td align="right" valign="middle"><span class="req">* </span><?php echo CBE1_LABEL_CPWD; ?>:</td>
            <td nowrap="nowrap" align="left" valign="middle"><input type="password" id="password2" class="textbox" name="password2" value="" size="25" /> <span class="note"><?php echo CBE1_SIGNUP_PTEXT; ?></span></td>
          </tr>
          <tr>
            <td align="right" valign="middle"><span class="req">* </span><?php echo CBE1_LABEL_COUNTRY; ?>:</td>
            <td align="left" valign="middle">
				<select name="country" class="textbox2" id="country" style="width: 170px;">
				<option value=""><?php echo CBE1_LABEL_COUNTRY_SELECT; ?></option>
				<?php

					$sql_country = "SELECT * FROM cashbackengine_countries WHERE signup='1' AND status='active' ORDER BY name ASC";
					$rs_country = smart_mysql_query($sql_country);
					$total_country = mysql_num_rows($rs_country);

					if ($total_country > 0)
					{
						while ($row_country = mysql_fetch_array($rs_country))
						{
							if ($country == $row_country['country_id'])
								echo "<option value='".$row_country['country_id']."' selected>".$row_country['name']."</option>\n";
							else
								echo "<option value='".$row_country['country_id']."'>".$row_country['name']."</option>\n";
						}
					}

				?>
				</select>
			</td>
          </tr>
          <tr>
            <td align="right" valign="middle"><?php echo CBE1_LABEL_PHONE; ?>:</td>
            <td align="left" valign="middle"><input type="text" id="phone" class="textbox" name="phone" value="<?php echo getPostParameter('phone'); ?>" size="25" /></td>
          </tr>
		  <?php if (SIGNUP_CAPTCHA == 1) { ?>
          <tr>
            <td align="right" valign="middle"><span class="req">* </span><?php echo CBE1_SIGNUP_SCODE; ?>:</td>
            <td align="left" valign="middle">
				<input type="text" id="captcha" class="textbox" name="captcha" value="" size="8" />
				<img src="<?php echo SITE_URL; ?>captcha.php?rand=<?php echo rand(); ?>" id="captchaimg" align="absmiddle" /> <small><a href="javascript: refreshCaptcha();" style="color: #777" title="<?php echo CBE1_SIGNUP_RIMG; ?>"><img src="<?php echo SITE_URL; ?>images/icon_refresh.png" align="absmiddle" alt="<?php echo CBE1_SIGNUP_RIMG; ?>" /></a></small>
			</td>
          </tr>
			<script language="javascript" type="text/javascript">
				function refreshCaptcha()
				{
					var img = document.images['captchaimg'];
					img.src = img.src.substring(0,img.src.lastIndexOf("?"))+"?rand="+Math.random()*1000;
				}
			</script>
		  <?php } ?>
          <tr>
            <td align="right" valign="middle">&nbsp;</td>
            <td align="left" valign="middle"><input type="checkbox" name="newsletter" class="checkboxx" value="1" <?php echo (@$newsletter == 1) ? "checked" : "" ?>/> <?php echo CBE1_SIGNUP_NEWSLETTER; ?></td>
          </tr>
          <tr>
            <td align="right" valign="top">&nbsp;</td>
            <td align="left" valign="middle"><input type="checkbox" name="tos" class="checkboxx" value="1" <?php echo (@$tos == 1) ? "checked" : "" ?>/> <?php echo CBE1_SIGNUP_AGREE; ?> <a href="<?php echo SITE_URL; ?>terms.php" target="_blank"><?php echo CBE1_SIGNUP_TERMS; ?></a></td>
          </tr>
        </tr>
          <tr>
            <td align="right" valign="middle">&nbsp;</td>
			<td align="left" valign="middle">
			<?php if (isset($_COOKIE['referer_id']) && is_numeric($_COOKIE['referer_id'])) { ?>
				<input type="hidden" name="referer_id" id="referer_id" value="<?php echo (int)$_COOKIE['referer_id']; ?>" />
			<?php } ?>
				<input type="hidden" name="action" id="action" value="signup" />
				<input type="submit" class="submit" name="Signup" id="Signup" value="<?php echo CBE1_SIGNUP_BUTTON; ?>" />
		  </td>
          </tr>
        </table>
      </form>

<?php require_once ("inc/footer.inc.php"); ?>