<?php
/*******************************************************************\
 * CashbackEngine v2.1
 * http://www.CashbackEngine.net
 *
 * Copyright (c) 2010-2014 CashbackEngine Software. All rights reserved.
 * ------------ CashbackEngine IS NOT FREE SOFTWARE --------------
\*******************************************************************/

	session_start();
	//require_once("inc/auth.inc.php");
	require_once("inc/config.inc.php");

	if (SUBMIT_COUPONS != 1)
	{
		header ("Location: index.php");
		exit();
	}


	if (isset($_POST['action']) && $_POST['action'] == "add")
	{
		unset($errs);
		$errs = array();

		$coupon_title	= mysql_real_escape_string(getPostParameter('coupon_title'));
		$retailer_id	= (int)getPostParameter('store');
		$code			= mysql_real_escape_string(getPostParameter('code'));
		$date_mm		= mysql_real_escape_string(getPostParameter('date_mm'));
		$date_dd		= mysql_real_escape_string(getPostParameter('date_dd'));
		$date_yy		= mysql_real_escape_string(getPostParameter('date_yy'));
		$description	= mysql_real_escape_string(nl2br(getPostParameter('description')));
		$captcha		= mysql_real_escape_string(getPostParameter('captcha'));
		$ip				= getenv("REMOTE_ADDR");

		if (isLoggedIn()) $author_id = (int)$userid; else $author_id = "11111111";

		if (!($coupon_title && $retailer_id && $code && $captcha))
		{
			$errs[] = CBE1_SCOUPON_ERR1;
		}
		else
		{
			if ($date_mm && $date_dd && $date_yy)
			{
				$end_date = $date_yy."-".$date_mm."-".$date_dd;
	
				if (strtotime($end_date) < strtotime("now"))
				{
					$errs[] = CBE1_SCOUPON_ERR3;
				}
				else
				{
					$end_date .= " 00:00:00";
				}
			}

			if (empty($_SESSION['captcha']) || strcasecmp($_SESSION['captcha'], $captcha) != 0)
			{
				$errs[] = CBE1_SIGNUP_ERR3;
			}

			$check_query = smart_mysql_query("SELECT * FROM cashbackengine_coupons WHERE retailer_id='$retailer_id' AND code='$code' LIMIT 1");
			if (mysql_num_rows($check_query) != 0)
			{
				$errs[] = CBE1_SCOUPON_ERR2;
			}
		}

		if (count($errs) == 0)
		{
			$query = "INSERT INTO cashbackengine_coupons SET title='$coupon_title', retailer_id='$retailer_id', user_id='$author_id', code='$code', start_date='', end_date='$end_date', description='$description', viewed='0', status='inactive', added=NOW()";
			$result = smart_mysql_query($query);

			// send email notification //
			if (NEW_COUPON_ALERT == 1)
			{
				SendEmail(SITE_ALERTS_MAIL, CBE1_EMAIL_ALERT1, CBE1_EMAIL_ALERT1_MSG);
			}
			/////////////////////////////
		
			header("Location: submit_coupon.php?msg=1");
			exit();
		}
		else
		{
			$allerrors = "";
			foreach ($errs as $errorname)
				$allerrors .= "&#155; ".$errorname."<br/>\n";
		}
	}


	if (isset($_REQUEST['id']) && is_numeric($_REQUEST['id']))
	{
		$retailer_id = (int)$_REQUEST['id'];

		$query = "SELECT * FROM cashbackengine_retailers WHERE retailer_id='$retailer_id' AND (end_date='0000-00-00 00:00:00' OR end_date > NOW()) AND status='active' LIMIT 1"; 
		$result = smart_mysql_query($query);
		if (mysql_num_rows($result) > 0)
		{
			$row = mysql_fetch_array($result);
		}
	}

	///////////////  Page config  ///////////////
	$PAGE_TITLE = CBE1_SCOUPON_TITLE;

	require_once ("inc/header.inc.php");

?>

	<h1><?php echo CBE1_SCOUPON_TITLE; ?></h1>

	<?php if (isset($_GET['msg']) && $_GET['msg'] == 1) { ?>
		<div class="success_msg"><?php echo CBE1_SCOUPON_SENT; ?></div>
	<?php } ?>

	<?php if (!(isset($_GET['msg']) && $_GET['msg'] == 1)) { ?>		

		<p><?php echo CBE1_SCOUPON_TEXT; ?></p>

		<?php if (isset($allerrors) && $allerrors != "") { ?>
			<div class="error_msg"><?php echo $allerrors; ?></div>
		<?php } ?>

		<img src="<?php echo SITE_URL; ?>images/coupon.png" style="float: right" />

		<form action="" method="post">
		<table bgcolor="#F9F9F9" width="100%" align="center" cellpadding="3" cellspacing="0" border="0">
		<tr>
		   <td align="right" valign="middle"><span class="req">* </span><?php echo CBE1_SCOUPON_STORE; ?>:</td>
		   <td valign="top">
				<select name="store" id="store" style="width: 190px;">
				<option value="">-- select --</option>
				<?php
					$select_allstores = smart_mysql_query("SELECT * FROM cashbackengine_retailers WHERE (end_date='0000-00-00 00:00:00' OR end_date > NOW()) AND status='active' ORDER BY title ASC");
					while ($srow_allstores = mysql_fetch_array($select_allstores))
					{
						if ($retailer_id == $srow_allstores['retailer_id']) $dsel = "selected='selected'"; else $dsel = "";
						echo "<option value=\"".$srow_allstores['retailer_id']."\" $dsel>".$srow_allstores['title']."</option>";
					}
				?>
				</select>
		   </td>
		</tr>
		<tr>
		   <td align="right" valign="middle"><span class="req">* </span><?php echo CBE1_SCOUPON_NAME; ?>:</td>
		   <td valign="top"><input type="text" name="coupon_title" id="coupon_title" autocomplete="off" value="<?php echo getPostParameter('coupon_title'); ?>" class="textbox" size="29" /></td>
		</tr>
		<tr>
		   <td align="right" valign="middle"><span class="req">* </span><?php echo CBE1_SCOUPON_CODE; ?>:</td>
		   <td valign="top"><input type="text" name="code" id="code" autocomplete="off" value="<?php echo getPostParameter('code'); ?>" class="textbox" size="29" /></td>
		</tr>
		<tr>
		   <td align="right" valign="middle"><?php echo CBE1_SCOUPON_EXPIRY; ?>:</td>
		   <td valign="top">
				<input type="text" name="date_mm" id="date_mm" autocomplete="off" placeholder="<?php echo CBE1_SCOUPON_EXPIRY_MM; ?>" class="textbox" value="<?php echo getPostParameter('date_mm'); ?>" maxlength="2" size="2" />
				<input type="text" name="date_dd" id="date_dd" autocomplete="off" placeholder="<?php echo CBE1_SCOUPON_EXPIRY_DD; ?>" class="textbox" value="<?php echo getPostParameter('date_dd'); ?>" maxlength="2" size="2" />
				<input type="text" name="date_yy" id="date_yy" autocomplete="off" placeholder="<?php echo CBE1_SCOUPON_EXPIRY_YYYY; ?>" class="textbox" value="<?php echo getPostParameter('date_yy'); ?>" maxlength="4" size="4" />
			</td>
		</tr>
		<tr>
			<td align="right" valign="middle">&nbsp;</td>
			<td align="left" valign="middle">
				<textarea name="description" cols="55" rows="5" class="textbox2" placeholder="<?php echo CBE1_SCOUPON_DESCRIPTION; ?>"><?php echo getPostParameter('description'); ?></textarea>
			</td>
		</tr>
		<tr>
			<td align="right" valign="middle"><span class="req">* </span><?php echo CBE1_SIGNUP_SCODE; ?>:</td>
			<td align="left" valign="middle">
				<input type="text" id="captcha" class="textbox" name="captcha" value="" size="8" />
				<img src="<?php echo SITE_URL; ?>captcha.php?rand=<?php echo rand(); ?>" id="captchaimg" align="absmiddle" /> <small><a href="javascript: refreshCaptcha();" style="color: #777" title="<?php echo CBE1_SIGNUP_RIMG; ?>"><img src="<?php echo SITE_URL; ?>images/icon_refresh.png" align="absmiddle" alt="<?php echo CBE1_SIGNUP_RIMG; ?>" /></a></small>
			</td>
		 </tr>
		 <tr>
			<td align="left" valign="top">&nbsp;</td>
			<td align="left" valign="top">
				<?php if ($row['retailer_id'] > 0) { ?><input type="hidden" name="id" value="<?php echo (int)$row['retailer_id']; ?>" /><?php } ?>
				<input type="hidden" name="action" value="add" />
				<input type="submit" class="submit" value="<?php echo CBE1_SUBMIT_BUTTON; ?>" />
				<input type="button" class="cancel" name="cancel" value="<?php echo CBE1_CANCEL_BUTTON; ?>" onclick="history.go(-1);return false;" />
			</td>
		 </tr>
		 </table>
		 </form>

			<script language="javascript" type="text/javascript">
				function refreshCaptcha()
				{
					var img = document.images['captchaimg'];
					img.src = img.src.substring(0,img.src.lastIndexOf("?"))+"?rand="+Math.random()*1000;
				}
			</script>

	<?php } ?>

<?php require_once ("inc/footer.inc.php"); ?>