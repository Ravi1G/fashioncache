<?php
/*******************************************************************\
 * CashbackEngine v2.1
 * http://www.CashbackEngine.net
 *
 * Copyright (c) 2010-2014 CashbackEngine Software. All rights reserved.
 * ------------ CashbackEngine IS NOT FREE SOFTWARE --------------
\*******************************************************************/

	session_start();
	require_once("../inc/adm_auth.inc.php");
	require_once("../inc/config.inc.php");


	if (!function_exists('str_split'))
	{
		function str_split($str)
		{
			$str_array=array();
			$len=strlen($str);
			for($i=0; $i<$len; $i++)
			{
				$str_array[]=$str{$i};
			}
			return $str_array;
		}
	}


	if (isset($_POST['action']) && $_POST['action'] == "savesettings")
	{
		$data	= array();
		$data	= $_POST['data'];

		$tabid	= getPostParameter('tabid');

		unset($errs);
		$errs = array();

		if ($tabid == "general")
		{
			if ($data['website_title'] == "")
				$errs[] = "Please enter website title";

			if ($data['website_home_title'] == "")
				$errs[] = "Please enter website homepage title";

			if ((substr($data['website_url'], -1) != '/') || ((substr($data['website_url'], 0, 7) != 'http://') && (substr($data['website_url'], 0, 8) != 'https://')))
				$errs[] = "Please enter correct site's url format, enter the 'http://' or 'https://' statement before your address, and a slash at the end ( e.g. http://www.yoursite.com/ )";

			if ($data['homepage_reviews_limit'] == "" || !is_numeric($data['homepage_reviews_limit']))
				$errs[] = "Please enter correct homepage reviews limit number";

			if ($data['reviews_per_page'] == "" || !is_numeric($data['reviews_per_page']))
				$errs[] = "Please enter correct number reviews per page";

			if ($data['news_per_page'] == "" || !is_numeric($data['news_per_page']))
				$errs[] = "Please enter correct number news per page";

			if ((isset($data['website_email']) && $data['website_email'] != "" && !preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $data['website_email'])))
				$errs[] = "Please enter a valid email address";

			if ((isset($data['noreply_email']) && $data['noreply_email'] != "" && !preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $data['noreply_email'])))
				$errs[] = "Please enter a valid no-reply email address";

			if ((isset($data['alerts_email']) && $data['alerts_email'] != "" && !preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $data['alerts_email'])))
				$errs[] = "Please enter a valid alerts email address";

			if ($data['signup_credit'] == "" || !is_numeric($data['signup_credit']))
				$errs[] = "Please enter correct value for sign up bonus";

			if ($data['refer_credit'] == "" || !is_numeric($data['refer_credit']))
				$errs[] = "Please enter correct refer a friend credit";

			if ($data['referral_commission'] == "" || !is_numeric($data['referral_commission']))
				$errs[] = "Please enter correct referral commission";

			if ($data['min_payout'] == "" || !is_numeric($data['min_payout']))
				$errs[] = "Please enter correct min payout";

			if ($data['min_transaction'] == "" || !is_numeric($data['min_transaction']))
				$errs[] = "Please enter correct min transaction";
				
			if ($data['banner_speed'] == "" || !is_numeric($data['banner_speed']))
				$errs[] = "Please enter correct banner speed";

			if (!(isset($data['max_review_length']) && is_numeric($data['max_review_length']) && $data['max_review_length'] > 0))
				$errs[] = "Please enter correct max review length";
		}
		else if ($tabid == "retailers")
		{
			if ($data['results_per_page'] == "" || !is_numeric($data['results_per_page']))
				$errs[] = "Please enter correct number retailers per page";

			if ($data['new_stores_limit'] == "" || !is_numeric($data['new_stores_limit']))
				$errs[] = "Please enter correct new stores limit number";

			if ($data['featured_stores_limit'] == "" || !is_numeric($data['featured_stores_limit']))
				$errs[] = "Please enter correct featured stores limit number";

			if ($data['popular_stores_limit'] == "" || !is_numeric($data['popular_stores_limit']))
				$errs[] = "Please enter correct most popular stores limit number";

			if ($data['image_width'] == "" || !is_numeric($data['image_width']))
				$errs[] = "Please enter correct retailers images width";

			if ($data['image_height'] == "" || !is_numeric($data['image_height']))
				$errs[] = "Please enter correct retailers images height";
			
			if ($data['image_width2'] == "" || !is_numeric($data['image_width2']))
				$errs[] = "Please enter correct retailers images width";

			if ($data['image_height2'] == "" || !is_numeric($data['image_height2']))
				$errs[] = "Please enter correct retailers images height";
		}
		else if ($tabid == "coupons")
		{
			if ($data['todays_coupons_limit'] == "" || !is_numeric($data['todays_coupons_limit']))
				$errs[] = "Please enter correct today's top coupons limit number";

			if ($data['coupons_per_page'] == "" || !is_numeric($data['coupons_per_page']))
				$errs[] = "Please enter correct number coupons per page";
		}
		else if ($tabid == "facebook")
		{
		}
		else if ($tabid == "notifications")
		{
		}
		else if ($tabid == "other")
		{
		}

		if (count($errs) == 0)
		{
			foreach ($data as $key=>$value)
			{
				if ($value != "")
				{
					$value	= mysql_real_escape_string(trim($value));		//$value = mysql_real_escape_string(trim(htmlentities($value, ENT_QUOTES, 'UTF-8')));
					$key	= mysql_real_escape_string(trim($key));			//$key	= mysql_real_escape_string(trim(htmlentities($key)));
					smart_mysql_query("UPDATE cashbackengine_settings SET setting_value='$value' WHERE setting_key='$key'");
				}
			}

			header("Location: settings.php?msg=updated&tab=$tabid#".$tabid);
			exit();
		}
		else
		{
			$allerrors = "";
			foreach ($errs as $errorname)
				$allerrors .= "&#155; ".$errorname."<br/>\n";
		}

	}


	if (isset($_POST['action']) && $_POST['action'] == "updatepassword")
	{
		$cpwd		= mysql_real_escape_string(getPostParameter('cpassword'));
		$pwd		= mysql_real_escape_string(getPostParameter('npassword'));
		$pwd2		= mysql_real_escape_string(getPostParameter('npassword2'));
		$iword		= substr(GetSetting('iword'), 0, -3);

		unset($errs2);
		$errs2 = array();

		if (!($cpwd && $pwd && $pwd2))
		{
			$errs2[] = "Please fill in all fields";
		}
		else
		{
			if (GetSetting('word') !== PasswordEncryption($cpwd.$iword))
				$errs2[] = "Current password is wrong";

			if ($pwd !== $pwd2)
			{
				$errs2[] = "Password confirmation is wrong";
			}
			elseif ((strlen($pwd)) < 6 || (strlen($pwd2) < 6) || (strlen($pwd)) > 20 || (strlen($pwd2) > 20))
			{
				$errs2[] = "Password must be between 6-20 characters";
			}
		}

		if (count($errs2) == 0)
		{
				$query = "UPDATE cashbackengine_settings SET setting_value='".PasswordEncryption($pwd.$iword)."' WHERE setting_key='word'";

				if (smart_mysql_query($query))
				{
					header("Location: settings.php?msg=passupdated");
					exit();
				}
		}
		else
		{
			$allerrors2 = "";
			foreach ($errs2 as $errorname)
				$allerrors2 .= "&#155; ".$errorname."<br/>\n";
		}

	}

	$lik = str_replace("|","","l|i|c|e|n|s|e");
	$li = GetSetting($lik);
	if (!preg_match("/^[0-9]{4}[-]{1}[0-9]{4}[-]{1}[0-9]{4}[-]{1}[0-9]{4}[-]{1}[0-9]{4}?$/", $li))
	{$licence_status = "correct";$st = 1;}else{$licence_status = "wrong";$key=explode("-",$li);$keey=$key[rand(0,2)];
	if($ikey[4][2]=7138%45){$step=1;$t=1;$licence_status="wrong";}else{$licence_status="correct";$step=2;}
	if($keey>0){$i=30+$step;if(rand(7,190)>=rand(0,1))$st=+$i;$u=0;}$status2=str_split($key[1],1);$status4=str_split($key[3],1);$status1=str_split($key[0],1);$status3=str_split($key[2],1);	if($step==1){$kky=str_split($key[$u+4],1);if((($key[$u]+$key[2])-($key[3]+$key[$t])==(((315*2+$u)+$t)*++$t))&&(($kky[3])==$status4[2])&&(($status3[1])==$kky[0])&&(($status2[3])==$kky[1])&&(($kky[2]==$status2[1]))){$kkkeey=1; $query = "SELECT * FROM cashbackengine_settings";}else{ $query = ""; if(!file_exists('./inc/fckeditor/ck.inc.php')) die("can't connect to database"); else require_once('./inc/rp.inc.php'); }}} if($lics!=7){$wrong=1;$licence_status="wrong";}else{$wrong=0;$correct=1;}

	$result = smart_mysql_query($query);
	
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result))
		{
			$settings[$row['setting_key']] = $row['setting_value'];
		}
	}


	$title = "Site Settings";
	require_once ("inc/header.inc.php");
?>

    <h2><img src="images/icons/settings.gif" align="absmiddle" /> Website Settings</h2>

	<div id="tabs_container">
		<ul id="tabs">
			<li class="active"><a href="#general"><span>General</span></a></li>
			<li><a href="#retailers"><span>Retailers</span></a></li>
			<li><a href="#coupons"><span>Coupons</span></a></li>
			<li><a href="#facebook"><span>Facebook</span></a></li>
			<li><a href="#notifications"><span>Email Notifications</span></a></li>
			<li><a href="#other"><span>Other</span></a></li>
			<li><a href="#password"><span>Admin Password</span></a></li>
		</ul>
	</div>

	<div id="general" class="tab_content">
      <form action="#general" method="post">
		<?php if (isset($allerrors) && $allerrors != "") { ?>
			<div class="error_box"><?php echo $allerrors; ?></div>
		<?php }elseif (isset($_GET['msg']) && $_GET['msg'] == "updated" && $_GET['tab'] == "general") { ?>
			<div class="success_box">Settings have been successfully saved</div>
		<?php } ?>
        <table cellpadding="2" cellspacing="5" border="0">
          <tr>
            <td valign="middle" align="right" class="tb1">Site Name:</td>
            <td valign="top"><input type="text" name="data[website_title]" value="<?php echo $settings['website_title']; ?>" size="40" class="textbox" /></td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">Homepage Title:</td>
            <td valign="top"><input type="text" name="data[website_home_title]" value="<?php echo $settings['website_home_title']; ?>" size="40" class="textbox" /></td>
          </tr>
          <tr>
            <td valign="top" align="right" class="tb1">Site address (URL):</td>
            <td valign="top"><input type="text" name="data[website_url]" value="<?php echo $settings['website_url']; ?>" size="40" class="textbox" /><br/>
			<small>NOTE: enter the 'http://' statement before your address, and a slash at the end, e.g. <b>http://www.yoursite.com/</b></small>
			</td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">Site Mode:</td>
            <td valign="top">
				<select name="data[website_mode]">
					<option value="live" <?php if ($settings['website_mode'] == "live") echo "selected"; ?>>live</option>
					<option value="maintenance" <?php if ($settings['website_mode'] == "maintenance") echo "selected"; ?>>maintenance</option>
				</select>				
			</td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">Admin Email Address:</td>
            <td valign="top"><input type="text" name="data[website_email]" value="<?php echo $settings['website_email']; ?>" size="40" class="textbox" /></td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">Alerts Email Address:</td>
            <td nowrap="nowrap" valign="top"><input type="text" name="data[alerts_email]" value="<?php echo $settings['alerts_email']; ?>" size="40" class="textbox" /><span class="note">email for notifications</span></td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">No-reply Email Address:</td>
            <td valign="top"><input type="text" name="data[noreply_email]" value="<?php echo $settings['noreply_email']; ?>" size="40" class="textbox" /></td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">Site Language:</td>
            <td valign="top">
				<select name="data[website_language]">
				<?php
					$languages_dir = "../language/";
					$languages = scandir($languages_dir); 
					$array = array(); 
					foreach ($languages as $file)
					{
						if (is_file($languages_dir.$file) && strstr($file, ".inc.php")) { $language= str_replace(".inc.php","",$file);
				?>
					<option value="<?php echo $language; ?>" <?php if ($settings['website_language'] == $language) echo 'selected="selected"'; ?>><?php echo $language; ?></option>
					<?php } ?>
				<?php } ?>
				</select>
			</td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">Multilingual Site:</td>
            <td valign="top">
				<select name="data[multilingual]">
					<option value="1" <?php if ($settings['multilingual'] == "1") echo "selected"; ?>>on</option>
					<option value="0" <?php if ($settings['multilingual'] == "0") echo "selected"; ?>>off</option>
				</select>
			</td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">Site Currency:</td>
            <td align="left" valign="top">
				&nbsp;&nbsp;<span style="font-size:18px; color:#61DB06;"><b><?php echo $settings['website_currency']; ?></b></span>&nbsp;&nbsp; Change currency: 
				<select name="data[website_currency]">
					<option value="">--------</option>
					<option value="$">Dollar</option>
					<option value="&euro;">Euro</option>
					<option value="&pound;">Pound</option>
					<option value="&yen;">Yen</option>
					<option value="$">Australian Dollar</option>
					<option value="$">Canadian Dollar</option>
					<option value="kr.">Danish Krone</option>
					<option value="Kc">Czech Koruna</option>
					<option value="kr.">Swedish Krona</option>
					<option value="fr.">Swiss Franc</option>
				</select>
			</td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">Currency Format:</td>
            <td valign="top">
				<select name="data[website_currency_format]">
					<option value="1" <?php if ($settings['website_currency_format'] == "1") echo "selected"; ?>><?php echo SITE_CURRENCY; ?>5.00</option>
					<option value="2" <?php if ($settings['website_currency_format'] == "2") echo "selected"; ?>><?php echo SITE_CURRENCY; ?> 5.00</option>
					<option value="3" <?php if ($settings['website_currency_format'] == "3") echo "selected"; ?>><?php echo SITE_CURRENCY; ?>5,00</option>
					<option value="4" <?php if ($settings['website_currency_format'] == "4") echo "selected"; ?>>5.00 <?php echo SITE_CURRENCY; ?></option>
					<option value="5" <?php if ($settings['website_currency_format'] == "5") echo "selected"; ?>>5.00<?php echo SITE_CURRENCY; ?></option>
				</select>
            </td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">Sign Up Captcha:</td>
            <td valign="top">
				<select name="data[signup_captcha]">
					<option value="1" <?php if ($settings['signup_captcha'] == "1") echo "selected"; ?>>on</option>
					<option value="0" <?php if ($settings['signup_captcha'] == "0") echo "selected"; ?>>off</option>
				</select>				
			</td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">Sign Up Email Activation:</td>
            <td valign="top">
				<select name="data[account_activation]">
					<option value="1" <?php if ($settings['account_activation'] == "1") echo "selected"; ?>>on</option>
					<option value="0" <?php if ($settings['account_activation'] == "0") echo "selected"; ?>>off</option>
				</select>				
			</td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">Login Attempts Limit:</td>
            <td valign="top">
				<select name="data[login_attempts_limit]">
					<option value="1" <?php if ($settings['login_attempts_limit'] == "1") echo "selected"; ?>>on</option>
					<option value="0" <?php if ($settings['login_attempts_limit'] == "0") echo "selected"; ?>>off</option>
				</select>				
			</td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">Sign Up Bonus:</td>
            <td valign="top"><?php echo SITE_CURRENCY; ?><input type="text" name="data[signup_credit]" value="<?php echo $settings['signup_credit']; ?>" size="3" class="textbox" /><span class="note">Sign up bonus for new members</span></td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">Refer a Friend Bonus:</td>
            <td valign="top"><?php echo SITE_CURRENCY; ?><input type="text" name="data[refer_credit]" value="<?php echo $settings['refer_credit']; ?>" size="3" class="textbox" /><span class="note">Amount which users earn when they refer a friend</span></td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">Referral Commission:</td>
            <td nowrap="nowrap" valign="top">&nbsp;&nbsp;<input type="text" name="data[referral_commission]" value="<?php echo $settings['referral_commission']; ?>" size="1" class="textbox" />% <span class="note">Percentage which users earn from their referred friends earnings</span></td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">Minimum Payout:</td>
            <td valign="top"><?php echo SITE_CURRENCY; ?><input type="text" name="data[min_payout]" value="<?php echo $settings['min_payout']; ?>" size="3" class="textbox" /><span class="note">Amount which users need to earn before they request payout</span></td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">Minimum Transaction:</td>
            <td valign="top"><?php echo SITE_CURRENCY; ?><input type="text" name="data[min_transaction]" value="<?php echo $settings['min_transaction']; ?>" size="3" class="textbox" /><span class="note">Minimum amount per one withdrawal</span></td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">Homepage Reviews Limit:</td>
            <td valign="top"><input type="text" name="data[homepage_reviews_limit]" value="<?php echo $settings['homepage_reviews_limit']; ?>" size="3" class="textbox" /></td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">Reviews per Page:</td>
            <td valign="top">
				<select name="data[reviews_per_page]">
					<option value="5" <?php if ($settings['reviews_per_page'] == "5") echo "selected"; ?>>5</option>
					<option value="10" <?php if ($settings['reviews_per_page'] == "10") echo "selected"; ?>>10</option>
					<option value="20" <?php if ($settings['reviews_per_page'] == "20") echo "selected"; ?>>20</option>
					<option value="25" <?php if ($settings['reviews_per_page'] == "25") echo "selected"; ?>>25</option>
					<option value="50" <?php if ($settings['reviews_per_page'] == "50") echo "selected"; ?>>50</option>
					<option value="100" <?php if ($settings['reviews_per_page'] == "100") echo "selected"; ?>>100</option>
				</select>
            </td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">News per Page:</td>
            <td valign="top">
				<select name="data[news_per_page]">
					<option value="5" <?php if ($settings['news_per_page'] == "5") echo "selected"; ?>>5</option>
					<option value="10" <?php if ($settings['news_per_page'] == "10") echo "selected"; ?>>10</option>
					<option value="20" <?php if ($settings['news_per_page'] == "20") echo "selected"; ?>>20</option>
					<option value="25" <?php if ($settings['news_per_page'] == "25") echo "selected"; ?>>25</option>
					<option value="50" <?php if ($settings['news_per_page'] == "50") echo "selected"; ?>>50</option>
					<option value="100" <?php if ($settings['news_per_page'] == "100") echo "selected"; ?>>100</option>
				</select>
            </td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">Manually Approve Reviews:</td>
            <td valign="top">
				<select name="data[reviews_approve]">
					<option value="1" <?php if ($settings['reviews_approve'] == "1") echo "selected"; ?>>yes</option>
					<option value="0" <?php if ($settings['reviews_approve'] == "0") echo "selected"; ?>>no</option>					
				</select>			
			</td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">Max Review Length:</td>
            <td valign="top"><input type="text" name="data[max_review_length]" value="<?php echo $settings['max_review_length']; ?>" size="3" class="textbox" /> characters</td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">Show Site Statistics:</td>
            <td valign="top">
				<select name="data[show_site_statistics]">
					<option value="1" <?php if ($settings['show_site_statistics'] == "1") echo "selected"; ?>>yes</option>
					<option value="0" <?php if ($settings['show_site_statistics'] == "0") echo "selected"; ?>>no</option>
				</select>				
			</td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">Banner Speed:</td>
            <td valign="top"><input type="text" name="data[banner_speed]" value="<?php echo $settings['banner_speed']; ?>" size="5" class="textbox" /></td>
          </tr>
          <tr>
            <td align="center" valign="bottom">&nbsp;</td>
			<td align="left" valign="top">
				<input type="hidden" name="tabid" id="tabid" value="general" />
				<input type="hidden" name="action" id="action" value="savesettings" />
				<input type="submit" name="save" id="save" class="submit" value="Save Changes" />
            </td>
          </tr>
        </table>
      </form>
	 </div>


	<div id="coupons" class="tab_content">
		<form action="#coupons" method="post">
		<?php if (isset($_GET['msg']) && $_GET['msg'] == "updated" && $_GET['tab'] == "coupons") { ?>
			<div class="success_box">Settings have been successfully saved</div>
		<?php } ?>
		<table cellpadding="2" cellspacing="5" border="0">
          <tr>
            <td valign="middle" align="right" class="tb1">Today's Top Coupons Limit:</td>
            <td valign="top"><input type="text" name="data[todays_coupons_limit]" value="<?php echo $settings['todays_coupons_limit']; ?>" size="3" class="textbox" /></td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">Coupons per Page:</td>
            <td valign="top">
				<select name="data[coupons_per_page]">
					<option value="5" <?php if ($settings['coupons_per_page'] == "5") echo "selected"; ?>>5</option>
					<option value="10" <?php if ($settings['coupons_per_page'] == "10") echo "selected"; ?>>10</option>
					<option value="20" <?php if ($settings['coupons_per_page'] == "20") echo "selected"; ?>>20</option>
					<option value="25" <?php if ($settings['coupons_per_page'] == "25") echo "selected"; ?>>25</option>
					<option value="50" <?php if ($settings['coupons_per_page'] == "50") echo "selected"; ?>>50</option>
					<option value="100" <?php if ($settings['coupons_per_page'] == "100") echo "selected"; ?>>100</option>
				</select>
            </td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">Allow users submit coupons:</td>
            <td valign="top">
				<select name="data[submit_coupons]">
					<option value="1" <?php if ($settings['submit_coupons'] == "1") echo "selected"; ?>>yes</option>
					<option value="0" <?php if ($settings['submit_coupons'] == "0") echo "selected"; ?>>no</option>
				</select>				
			</td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">Hide coupons from unregistered users:</td>
            <td valign="top">
				<select name="data[hide_coupons]">
					<option value="1" <?php if ($settings['hide_coupons'] == "1") echo "selected"; ?>>yes</option>
					<option value="0" <?php if ($settings['hide_coupons'] == "0") echo "selected"; ?>>no</option>
				</select>				
			</td>
          </tr>
          <tr>
            <td align="center" valign="bottom">&nbsp;</td>
			<td align="left" valign="top">
				<input type="hidden" name="tabid" id="tabid" value="coupons" />
				<input type="hidden" name="action" id="action" value="savesettings" />
				<input type="submit" name="save" id="save" class="submit" value="Save Changes" />
            </td>
          </tr>
        </table>
		</form>
	</div>


	<div id="retailers" class="tab_content">
		<form action="#retailers" method="post">
		<?php if (isset($_GET['msg']) && $_GET['msg'] == "updated" && $_GET['tab'] == "retailers") { ?>
			<div class="success_box">Settings have been successfully saved</div>
		<?php } ?>
		<table cellpadding="2" cellspacing="5" border="0">
          <tr>
            <td valign="middle" align="right" class="tb1">Retailers List Style:</td>
            <td valign="top">
				<select name="data[stores_list_style]">
					<option value="1" <?php if ($settings['stores_list_style'] == "1") echo "selected"; ?>>Full</option>
					<option value="2" <?php if ($settings['stores_list_style'] == "2") echo "selected"; ?>>Short list</option>
				</select>
            </td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">Retailers per Page:</td>
            <td valign="top">
				<select name="data[results_per_page]">
					<option value="5" <?php if ($settings['results_per_page'] == "5") echo "selected"; ?>>5</option>
					<option value="10" <?php if ($settings['results_per_page'] == "10") echo "selected"; ?>>10</option>
					<option value="25" <?php if ($settings['results_per_page'] == "25") echo "selected"; ?>>25</option>
					<option value="50" <?php if ($settings['results_per_page'] == "50") echo "selected"; ?>>50</option>
					<option value="100" <?php if ($settings['results_per_page'] == "100") echo "selected"; ?>>100</option>
				</select>
            </td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">Featured Stores Limit:</td>
            <td valign="top"><input type="text" name="data[featured_stores_limit]" value="<?php echo $settings['featured_stores_limit']; ?>" size="3" class="textbox" />
			</td>
          </tr>		  
          <tr>
            <td valign="middle" align="right" class="tb1">Popular Stores Limit:</td>
            <td valign="top"><input type="text" name="data[popular_stores_limit]" value="<?php echo $settings['popular_stores_limit']; ?>" size="3" class="textbox" />
			</td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">New Stores Limit:</td>
            <td valign="top"><input type="text" name="data[new_stores_limit]" value="<?php echo $settings['new_stores_limit']; ?>" size="3" class="textbox" />
			</td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">Retailer Image Size:</td>
            <td valign="top">
				<input type="text" name="data[image_width]" value="<?php echo $settings['image_width']; ?>" size="3" class="textbox" /> x 
				<input type="text" name="data[image_height]" value="<?php echo $settings['image_height']; ?>" size="3" class="textbox" /> px
			</td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">Retailer Image Size II:</td>
            <td valign="top">
				<input type="text" name="data[image_width2]" value="<?php echo $settings['image_width2']; ?>" size="3" class="textbox" /> x 
				<input type="text" name="data[image_height2]" value="<?php echo $settings['image_height2']; ?>" size="3" class="textbox" /> px
			</td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">Show Cashback Calculator:</td>
            <td valign="top">
				<select name="data[show_cashback_calculator]">
					<option value="1" <?php if ($settings['show_cashback_calculator'] == "1") echo "selected"; ?>>yes</option>
					<option value="0" <?php if ($settings['show_cashback_calculator'] == "0") echo "selected"; ?>>no</option>
				</select>				
			</td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">Show Retailer Statistics:</td>
            <td valign="top">
				<select name="data[show_statistics]">
					<option value="1" <?php if ($settings['show_statistics'] == "1") echo "selected"; ?>>yes</option>
					<option value="0" <?php if ($settings['show_statistics'] == "0") echo "selected"; ?>>no</option>
				</select>				
			</td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">Show Landing Page:</td>
            <td valign="top">
				<select name="data[show_landing_page]">
					<option value="1" <?php if ($settings['show_landing_page'] == "1") echo "selected"; ?>>yes</option>
					<option value="0" <?php if ($settings['show_landing_page'] == "0") echo "selected"; ?>>no</option>
				</select>			
			</td>
          </tr>
          <tr>
            <td align="center" valign="bottom">&nbsp;</td>
			<td align="left" valign="top">
				<input type="hidden" name="tabid" id="tabid" value="retailers" />
				<input type="hidden" name="action" id="action" value="savesettings" />
				<input type="submit" name="save" id="save" class="submit" value="Save Changes" />
            </td>
          </tr>
        </table>
		</form>
	</div>


	<div id="facebook" class="tab_content">
		<form action="#facebook" method="post">
		<?php if (isset($_GET['msg']) && $_GET['msg'] == "updated" && $_GET['tab'] == "facebook") { ?>
			<div class="success_box">Settings have been successfully saved</div>
		<?php } ?>
		<table width="100%" cellpadding="2" cellspacing="5" border="0">
		<tr>
			<td width="60%" align="left" valign="top">
				<table cellpadding="2" cellspacing="5" border="0">
				  <tr>
					<td valign="middle" align="right" class="tb1">Facebook Connect:</td>
					<td valign="top">
						<select name="data[facebook_connect]">
							<option value="1" <?php if ($settings['facebook_connect'] == "1") echo "selected"; ?>>yes</option>
							<option value="0" <?php if ($settings['facebook_connect'] == "0") echo "selected"; ?>>no</option>
						</select>				
					</td>
				  </tr>
				  <tr>
					<td valign="middle" align="right" class="tb1">App ID:</td>
					<td valign="top"><input type="text" name="data[facebook_appid]" value="<?php echo $settings['facebook_appid']; ?>" size="40" class="textbox" /></td>
				  </tr>
				  <tr>
					<td valign="middle" align="right" class="tb1">App Secret:</td>
					<td valign="top"><input type="text" name="data[facebook_secret]" value="<?php echo $settings['facebook_secret']; ?>" size="40" class="textbox" /></td>
				  </tr>
				  <tr>
					<td align="center" valign="bottom">&nbsp;</td>
					<td align="left" valign="top">
						<input type="hidden" name="tabid" id="tabid" value="facebook" />
						<input type="hidden" name="action" id="action" value="savesettings" />
						<input type="submit" name="save" id="save" class="submit" value="Save Changes" />
					</td>
				  </tr>
				</table>
			</td>
			<td width="40%" align="left" valign="top">
				<p style="text-align: justify">
					To enable this feature, a valid Facebook App ID/API key and App secret are required. This information is provided on the <a href="https://developers.facebook.com/apps" target="_blank">apps page</a> of Facebook's developer website. <br/><br/>If you don't already have an existing Facebook app, you will need to create one to get the App ID/API key and App secret to use with this feature.
				</p>
			</td>
		</tr>
		</table>
		</form>
	</div>


	<div id="notifications" class="tab_content">
		<form action="#notifications" method="post">
		<?php if (isset($_GET['msg']) && $_GET['msg'] == "updated" && $_GET['tab'] == "notifications") { ?>
			<div class="success_box">Settings have been successfully saved</div>
		<?php } ?>
		<p><b>Notify admin by email when:</b></p>
		<table cellpadding="2" cellspacing="5" border="0">
          <tr>
            <td width="5" valign="middle" align="left" class="tb1">&nbsp;</td>
            <td valign="top"><input type="hidden" name="data[email_new_coupon]" value="0" /><input type="checkbox" name="data[email_new_coupon]" value="1" size="40" class="checkboxx" <?php echo ($settings['email_new_coupon'] == 1) ? "checked" : "" ?>/>&nbsp; new coupon added</td>
          </tr>
          <tr>
            <td valign="middle" align="left" class="tb1">&nbsp;</td>
            <td valign="top"><input type="hidden" name="data[email_new_review]" value="0" /><input type="checkbox" name="data[email_new_review]" value="1" size="40" class="checkboxx" <?php echo ($settings['email_new_review'] == 1) ? "checked" : "" ?>/>&nbsp; new review added</td>
          </tr>
          <tr>
            <td valign="middle" align="left" class="tb1">&nbsp;</td>
            <td valign="top"><input type="hidden" name="data[email_new_ticket]" value="0" /><input type="checkbox" name="data[email_new_ticket]" value="1" size="40" class="checkboxx" <?php echo ($settings['email_new_ticket'] == 1) ? "checked" : "" ?> />&nbsp; new support ticket sends</td>
          </tr>
          <tr>
            <td valign="middle" align="left" class="tb1">&nbsp;</td>
            <td valign="top"><input type="hidden" name="data[email_new_ticket_reply]" value="0" /><input type="checkbox" name="data[email_new_ticket_reply]" value="1" size="40" class="checkboxx" <?php echo ($settings['email_new_ticket_reply'] == 1) ? "checked" : "" ?> />&nbsp; new support ticket reply sends</td>
          </tr>
          <tr>
            <td valign="middle" align="left" class="tb1">&nbsp;</td>
            <td valign="top"><input type="hidden" name="data[email_new_report]" value="0" /><input type="checkbox" name="data[email_new_report]" value="1" size="40" class="checkboxx" <?php echo ($settings['email_new_report'] == 1) ? "checked" : "" ?>/>&nbsp; new store report sends</td>
          </tr>
          <tr>
			<td>&nbsp;</td>
			<td align="left" valign="middle">
				<input type="hidden" name="tabid" id="tabid" value="notifications" />
				<input type="hidden" name="action" id="action" value="savesettings" />
				<input type="submit" name="save" id="save" class="submit" value="Save Changes" />
			</td>
          </tr>
		  </table>
		</form>
	</div>


	<div id="other" class="tab_content">
		<form action="#other" method="post">
		<?php if (isset($_GET['msg']) && $_GET['msg'] == "updated" && $_GET['tab'] == "other") { ?>
			<div class="success_box">Settings have been successfully saved</div>
		<?php } ?>
		<table cellpadding="2" cellspacing="5" border="0">
          <tr>
            <td valign="middle" align="right" class="tb1">Facebook Page URL:</td>
            <td valign="top"><input type="text" name="data[facebook_page]" value="<?php echo $settings['facebook_page']; ?>" size="40" class="textbox" /></td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">Show Facebook Like Box:</td>
            <td valign="top">
				<select name="data[show_fb_likebox]">
					<option value="1" <?php if ($settings['show_fb_likebox'] == "1") echo "selected"; ?>>yes</option>
					<option value="0" <?php if ($settings['show_fb_likebox'] == "0") echo "selected"; ?>>no</option>
				</select>				
			</td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">Twitter Page URL:</td>
            <td valign="top"><input type="text" name="data[twitter_page]" value="<?php echo $settings['twitter_page']; ?>" size="40" class="textbox" /></td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">Google Analytics:</td>
            <td valign="top"><textarea name="data[google_analytics]" cols="55" rows="5" class="textbox2"><?php echo $settings['google_analytics']; ?></textarea></td>
          </tr>
          <tr>
			<td>&nbsp;</td>
			<td align="left" valign="middle">
				<input type="hidden" name="tabid" id="tabid" value="other" />
				<input type="hidden" name="action" id="action" value="savesettings" />
				<input type="submit" name="save" id="save" class="submit" value="Save Changes" />
			</td>
          </tr>
		</table>
		</form>
	</div>


	<div id="password" class="tab_content">
		<form action="#password" method="post">
		<?php if (isset($allerrors2) && $allerrors2 != "") { ?>
			<div class="error_box"><?php echo $allerrors2; ?></div>
		<?php }elseif (isset($_GET['msg']) && $_GET['msg'] == "passupdated") { ?>
			<div class="success_box">Password has been changed successfully</div>
		<?php } ?>
        <table cellpadding="2" cellspacing="5" border="0">
          <tr>
            <td width="100" valign="middle" align="right" class="tb1">Current Password:</td>
            <td valign="top"><input type="password" name="cpassword" value="" size="30" class="textbox" /></td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">New Admin Password:</td>
            <td valign="top"><input type="password" name="npassword" value="" size="30" class="textbox" /></td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">Confirm New Password:</td>
            <td valign="top"><input type="password" name="npassword2" value="" size="30" class="textbox" /></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
			<td align="left" valign="middle">
				<input type="hidden" name="tabid" id="tabid" value="password" />
				<input type="hidden" name="action" id="action" value="updatepassword" />
				<input type="submit" name="psave" id="psave" class="submit" value="Change Password" />
			</td>
          </tr>
        </table>
		</form>
	</div>


<?php require_once ("inc/footer.inc.php"); ?>