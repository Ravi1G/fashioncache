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


	if (isset($_POST['action']) && $_POST['action'] == "login")
	{
		$username	= mysql_real_escape_string(getPostParameter('username'));
		$pwd		= mysql_real_escape_string(getPostParameter('password'));
		$remember	= (int)getPostParameter('rememberme');
		$ip			= getenv("REMOTE_ADDR");

		if (!($username && $pwd))
		{
			$errormsg = CBE1_LOGIN_ERR;
		}
		else
		{
			$sql = "SELECT * FROM cashbackengine_users WHERE username='$username' AND password='".PasswordEncryption($pwd)."' LIMIT 1";
			$result = smart_mysql_query($sql);

			if (mysql_num_rows($result) != 0)
			{
					$row = mysql_fetch_array($result);

					if ($row['status'] == 'inactive')
					{
						header("Location: login.php?msg=2");
						exit();
					}

					if (LOGIN_ATTEMPTS_LIMIT == 1)
					{
						unset($_SESSION['attems_'.$username."_".$ip], $_SESSION['attems_left']);
					}

					if ($remember == 1)
					{
						$cookie_hash = md5(sha1($username.$ip));
						setcookie("usname", $cookie_hash, time()+3600*24*365, '/');
						$login_sql = "login_session = '$cookie_hash', ";
					}

					smart_mysql_query("UPDATE cashbackengine_users SET ".$login_sql." last_ip='$ip', login_count=login_count+1, last_login=NOW() WHERE user_id='".(int)$row['user_id']."' LIMIT 1");

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
						$redirect_url = "myaccount.php";
					}

					header("Location: ".$redirect_url);
					exit();
			}
			else
			{
				if (LOGIN_ATTEMPTS_LIMIT == 1)
				{
					$check_sql = "SELECT * FROM cashbackengine_users WHERE username='$username' AND status!='inactive' AND block_reason!='login attempts limit' LIMIT 1";
					$check_result = smart_mysql_query($check_sql);

					if (mysql_num_rows($check_result) != 0)
					{
						if (!session_id()) session_start();
						$_SESSION['attems_'.$username."_".$ip] += 1;
						$_SESSION['attems_left'] = LOGIN_ATTEMPTS - $_SESSION['attems_'.$username.'_'.$ip];

						if ($_SESSION['attems_left'] == 0)
						{ 
							// block user //
							smart_mysql_query("UPDATE cashbackengine_users SET status='inactive', block_reason='login attempts limit' WHERE username='$username' LIMIT 1"); 
							unset($_SESSION['attems_'.$username."_".$ip], $_SESSION['attems_left']);
					
							header("Location: login.php?msg=6");
							exit();
						}
						else
						{
							header("Location: login.php?msg=5");
							exit();
						}
					}
				}

				header("Location: login.php?msg=1");
				exit();
			}
		}
	}

	///////////////  Page config  ///////////////
	$PAGE_TITLE = CBE1_LOGIN_TITLE;

	require_once ("inc/header.inc.php");

?>

<table width="100%" align="center" cellpadding="2" cellspacing="0" border="0">
<tr>
<td width="50%" valign="top" align="left">

        <h1><?php echo CBE1_LOGIN_TITLE; ?></h1>

		<?php if (isset($errormsg) || isset($_GET['msg'])) { ?>
			<div style="width: 80%;" class="error_msg">
				<?php if (isset($errormsg) && $errormsg != "") { ?>
					<?php echo $errormsg; ?>
				<?php }else{ ?>
					<?php if ($_GET['msg'] == 1) { echo CBE1_LOGIN_ERR1; } ?>
					<?php if ($_GET['msg'] == 2) { echo CBE1_LOGIN_ERR2; } ?>
					<?php if ($_GET['msg'] == 3) { echo CBE1_LOGIN_ERR3; } ?>
					<?php if ($_GET['msg'] == 4) { echo CBE1_LOGIN_ERR4; } ?>
					<?php if ($_GET['msg'] == 5) { echo CBE1_LOGIN_ERR1." ".$_SESSION['attems_left']." ".CBE1_LOGIN_ATTEMPTS; } ?>
					<?php if ($_GET['msg'] == 6) { echo CBE1_LOGIN_ERR6; } ?>
				<?php } ?>
			</div>
		<?php } ?>

		<div class="login_box">
		<form action="" method="post">
        <table bgcolor="#F9F9F9" width="95%" align="center" cellpadding="3" cellspacing="0" border="0">
          <tr>
            <td align="right" valign="middle"><?php echo CBE1_LOGIN_EMAIL2; ?>:</td>
            <td valign="top"><input type="text" class="textbox" name="username" value="<?php echo getPostParameter('username'); ?>" size="25" /></td>
          </tr>
          <tr>
            <td align="right" valign="middle"><?php echo CBE1_LOGIN_PASSWORD; ?>:</td>
            <td valign="top"><input type="password" class="textbox" name="password" value="" size="25" /></td>
          </tr>
          <tr>
            <td align="right" valign="middle">&nbsp;</td>
            <td valign="top"><input type="checkbox" class="checkboxx" name="rememberme" id="rememberme" value="1" <?php echo (@$rememberme == 1) ? "checked" : "" ?>/> <?php echo CBE1_LOGIN_REMEMBER; ?></td>
          </tr>
          <tr>
            <td valign="top" align="middle">&nbsp;</td>
			<td align="left" valign="bottom">
		  		<input type="hidden" name="action" value="login" />
				<input type="submit" class="submit" name="login" id="login" value="<?php echo CBE1_LOGIN_BUTTON; ?>" />
			</td>
          </tr>
          <tr>
		   <td valign="top" align="middle">&nbsp;</td>
            <td align="left" valign="bottom">
				<a href="<?php echo SITE_URL; ?>forgot.php"><?php echo CBE1_LOGIN_FORGOT; ?></a>
				<?php if (ACCOUNT_ACTIVATION == 1) { ?>
					<p><a href="<?php echo SITE_URL; ?>activation_email.php"><?php echo CBE1_LOGIN_AEMAIL; ?></a></p>
				<?php } ?>
			</td>
          </tr>
        </table>
      </form>
	  </div>

	<?php if (FACEBOOK_CONNECT == 1 && FACEBOOK_APPID != "" && FACEBOOK_SECRET != "") { ?>
		<div style="border-bottom: 1px dotted #ddd; margin-bottom: 20px;">
			<div style="font-size: 11px; font-weight: bold; background-color: #fff; color: #DDD; margin: 0 auto; top: 8px; text-align: center; width: 100px; position: relative;">or</div>
		</div>
		<p align="center"><a href="javascript: void(0);" onclick="facebook_login();" class="connect-f"><img src="<?php echo SITE_URL; ?>images/facebook_connect.png" /></a></p>
	<?php } ?>

</td>
<td width="2%" valign="top" align="left">&nbsp;</td>
<td width="48%" valign="top" align="left">
	
	<h1><?php echo CBE1_LOGIN_NMEMBER; ?></h1>
	<p><?php echo CBE1_LOGIN_TEXT2; ?></p>

		<p><b><?php echo str_replace("%site_title%",SITE_TITLE,CBE1_LOGIN_TXT1); ?></b></p>
		<ul id="benefits">
			<li><?php echo CBE1_LOGIN_LI1; ?></li>
			<?php if (SIGNUP_BONUS > 0) { ?><li><?php echo str_replace("%amount%",DisplayMoney(SIGNUP_BONUS),CBE1_LOGIN_LI2); ?></li><?php } ?>
			<?php if (REFER_FRIEND_BONUS > 0) { ?><li><?php echo str_replace("%amount%",DisplayMoney(REFER_FRIEND_BONUS),CBE1_LOGIN_LI3); ?></li><?php } ?>
			<li><?php echo CBE1_LOGIN_LI4; ?></li>
			<li><?php echo CBE1_LOGIN_LI5; ?></li>
			<li><?php echo CBE1_LOGIN_LI6; ?></li>
		</ul>

	<p align="center"><a class="button" href="<?php echo SITE_URL; ?>signup.php"><?php echo CBE_SIGNUP; ?></a></p>

</td>
</tr>
</table>

	<?php

		if (isset($_SESSION['goRetailerID']) && $_SESSION['goRetailerID'] != "" && isset($_GET['msg']) && $_GET['msg'] == 4)
		{
			$retailer_id = (int)$_SESSION['goRetailerID'];
			$result = smart_mysql_query("SELECT * FROM cashbackengine_retailers WHERE retailer_id='$retailer_id' LIMIT 1");
			if (mysql_num_rows($result) > 0)
			{
				$row = mysql_fetch_array($result);
				$retailer_website = str_replace("{USERID}", "0", $row['url']);
			}

			if (isset($_SESSION['goCouponID']) && is_numeric($_SESSION['goCouponID']) && $_SESSION['goCouponID'] > 0)
			{
				$coupon_id = (int)$_SESSION['goCouponID'];
				$coupon_result = smart_mysql_query("SELECT * FROM cashbackengine_coupons WHERE coupon_id='$coupon_id' LIMIT 1");
				if (mysql_num_rows($coupon_result) > 0)
				{
					$coupon_row = mysql_fetch_array($coupon_result);
					if ($coupon_row['link'] != "")
					{
						$retailer_website = str_replace("{USERID}", "0", $coupon_row['link']);
					}
				}
			}
	?>
			<br/>
			<table bgcolor="#F9F9F9" style="border-top: 2px dotted #EEEEEE; border-bottom: 2px dotted #EEEEEE" align="center" width="100%" border="0" cellspacing="0" cellpadding="5">
			<?php if (isset($_GET['msg']) && $_GET['msg'] == 4) { ?>
				<tr><td bgcolor="#B7B7B7" colspan="2" align="center"><span style="color: #FFF"><?php echo CBE1_LOGIN_ERR4; ?></span></td></tr>
			<?php } ?>
			<tr>
				<td width="125" align="center" valign="middle">
					<div class="imagebox"><img src="<?php if (!stristr($row['image'], 'http')) echo SITE_URL."img/"; echo $row['image']; ?>" width="<?php echo IMAGE_WIDTH; ?>" height="<?php echo IMAGE_HEIGHT; ?>" alt="<?php echo $row['title']; ?>" title="<?php echo $row['title']; ?>" border="0" /></div>
				</td>
				<td align="left" valign="bottom">
					<table width="100%" border="0" cellspacing="0" cellpadding="3">
						<tr>
							<td width="80%" align="left" valign="middle"><h2 class="stitle"><?php echo $row['title']; ?></h1></td>
							<td nowrap="nowrap" width="20%" align="center" valign="middle">
							<?php if ($row['cashback'] != "") { ?>
								<?php if ($row['old_cashback'] != "") { ?><span class="old_cashback"><?php echo DisplayCashback($row['old_cashback']); ?></span><?php } ?>
								<span class="cashback"><span class="value"><?php echo DisplayCashback($row['cashback']); ?></span> <?php echo CBE1_CASHBACK; ?></span>
							<?php } ?>
							</td>
						</tr>
						<tr>
							<td colspan="2" valign="top" align="left">
								<p class="retailer_description"><?php echo $row['description']; ?></p>
								<?php echo GetStoreCountries($row['retailer_id']); ?>
								<?php if ($row['conditions'] != "") { ?><p><b><?php echo CBE1_CONDITIONS; ?></b>: <?php echo $row['conditions']; ?></p><?php } ?>
							</td>
						</tr>							
					</table>
				</td>
			</tr>
			<tr>
				<td colspan="2" valign="middle" align="center">
					<div class="sline"></div>
					<h3><?php echo CBE1_LOGIN_THX; ?></h3>
					<p><a class="go2store_large" href="<?php echo $retailer_website; ?>" target="_blank"><?php echo CBE1_LOGIN_CONTINUE; ?></a></p>
				</td>
			</tr>
			</table>
	<?php } ?>


<?php require_once ("inc/footer.inc.php"); ?>