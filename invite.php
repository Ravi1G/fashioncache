<?php
/*******************************************************************\
 * CashbackEngine v2.1
 * http://www.CashbackEngine.net
 *
 * Copyright (c) 2010-2014 CashbackEngine Software. All rights reserved.
 * ------------ CashbackEngine IS NOT FREE SOFTWARE --------------
\*******************************************************************/

	session_start();
	require_once("inc/auth.inc.php");
	require_once("inc/config.inc.php");
	require_once("inc/pagination.inc.php");

	define('FRIENDS_INVITATIONS_LIMIT', 5);
	
	$ReferralLink = SITE_URL."?ref=".$userid;


	if (isset($_POST['action']) && $_POST['action'] == "friend")
	{
		unset($errs);
		$errs = array();

		$uname		= $_SESSION['FirstName'];
		$fname		= array();
		$fname		= $_POST['fname'];
		$femail		= array();
		$femail		= $_POST['femail'];
		$umessage	= mysql_real_escape_string(nl2br(getPostParameter('umessage')));

		if(!($fname[1] && $femail[1]))
		{
			$errs[] = CBE1_INVITE_ERR;
		}
		else
		{
			foreach ($fname as $k=>$v)
			{
				if ($femail[$k] != "" && !preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $femail[$k]))
				{
					$errs[] = CBE1_INVITE_ERR2; break;
				}
			}
		}

		if (count($errs) == 0)
		{
			////////////////////////////////  Send Message  //////////////////////////////
				$etemplate = GetEmailTemplate('invite_friend');
				
				$recipients = "";

				foreach ($fname as $k=>$v)
				{
					if (isset($v) && $v != "" && isset($femail[$k]) && $femail[$k] != "")
					{
						$friend_name = $v;
						$friend_email = $femail[$k];
						
						$esubject = $etemplate['email_subject'];

						if ($umessage != "")
						{
							$emessage = $umessage;
							$emessage = str_replace("{friend_name}", $friend_name, $emessage);
							$emessage = str_replace("{first_name}", $uname, $emessage);
							$emessage = str_replace("{referral_link}", $ReferralLink, $emessage);
							$emessage .= "Sign up here: <a href=\'$ReferralLink\'>".$ReferralLink."</a>";
						}
						else
						{
							$emessage = $etemplate['email_message'];
							$emessage = str_replace("{friend_name}", $friend_name, $emessage);
							$emessage = str_replace("{first_name}", $uname, $emessage);
							$emessage = str_replace("{referral_link}", $ReferralLink, $emessage);
						}

						$to_email = $friend_name.' <'.$friend_email.'>';
						$subject = $esubject;
						$message = $emessage;
		
						$headers  = 'MIME-Version: 1.0' . "\r\n";
						$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
						$headers .= 'From: '.SITE_TITLE.' <'.NOREPLY_MAIL.'>' . "\r\n";

						$recipients .= $friend_name."|".$friend_email."||";

						@mail($to_email, $subject, $message, $headers);
					}
				}
			////////////////////////////////////////////////////////////////////////////////

			// save invitations in history //
			smart_mysql_query("INSERT INTO cashbackengine_invitations SET user_id='".(int)$userid."', recipients='$recipients', message='$umessage', sent_date=NOW()");

			header("Location: invite.php?msg=1");
			exit();
		}
		else
		{
			$allerrors = "";
			foreach ($errs as $errorname)
				$allerrors .= "&#155; ".$errorname."<br/>\n";
		}
	}

	///////////////  Page config  ///////////////
	$PAGE_TITLE = CBE1_INVITE_TITLE;

	require_once ("inc/header.inc.php");

?>

	<h1><?php echo CBE1_INVITE_TITLE; ?></h1>

	<table align="center" width="100%" border="0" cellpadding="3" cellspacing="0">
	<?php if (REFER_FRIEND_BONUS > 0) { ?>
	<tr>
		<td align="left" valign="top">
			<?php echo str_replace("%amount%",DisplayMoney(REFER_FRIEND_BONUS),CBE1_INVITE_TEXT); ?>
		</td>
	</tr>
	<?php } ?>
	<tr>
		<td align="left" valign="middle">
			<div class="referral_link_share">
				<a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode($ReferralLink); ?>&t=<?php echo SITE_TITLE; ?>" target="_blank" title="<?php echo CBE1_SHARE_FACEBOOK; ?>"><img src="<?php echo SITE_URL; ?>images/facebook_share.png" align="absmiddle" alt="<?php echo CBE1_SHARE_FACEBOOK; ?>" /></a>
				<a href="http://twitter.com/intent/tweet?source=sharethiscom&text=<?php echo SITE_TITLE; ?>&url=<?php echo urlencode($ReferralLink); ?>" target="_blank" title="<?php echo CBE1_SHARE_TWITTER; ?>"><img src="<?php echo SITE_URL; ?>images/twitter_share.png" align="absmiddle" alt="<?php echo CBE1_SHARE_TWITTER; ?>" /></a>
				<a href="https://plus.google.com/share?url=<?php echo urlencode($ReferralLink); ?>" target="_blank" title="<?php echo CBE1_SHARE_GOOGLE; ?>"><img src="<?php echo SITE_URL; ?>images/google_share.png" align="absmiddle" alt="<?php echo CBE1_SHARE_GOOGLE; ?>" /></a>
			</div>
			<div class="referral_link">
				<b><?php echo CBE1_INVITE_LINK; ?>:</b>
				<input type="text" class="reflink_textbox" size="60" readonly="readonly" onfocus="this.select();" onclick="this.focus();this.select();" value="<?php echo $ReferralLink; ?>" />
			</div>
		</td>
	</tr>
	</table>
	<br />


	<h1><?php echo CBE1_INVITE_TITLE2; ?></h1>

	<?php if (REFER_FRIEND_BONUS > 0) { ?>
	<table align="center" width="100%" border="0" cellpadding="3" cellspacing="0">
	<tr>
		<td align="center" valign="top">
			<?php echo str_replace("%amount%",DisplayMoney(REFER_FRIEND_BONUS),CBE1_INVITE_TEXT2); ?><br/><br/>
		</td>
	</tr>
	</table>
	<?php } ?>


	<?php if (isset($_GET['msg']) and $_GET['msg'] == 1) { ?>
		<div class="success_msg"><?php echo CBE1_INVITE_SENT; ?></div>
		<p align="center"><a class="button" href="<?php echo SITE_URL; ?>invite.php"><?php echo CBE1_INVITE_SEND_MORE; ?> &raquo;</a></p>
	<?php }else{ ?>
          
		<?php if (isset($allerrors) and $allerrors != "") { ?>
			<div class="error_msg" ><?php echo $allerrors; ?></div>
		<?php } ?>

		<form action="" method="post">
		<table bgcolor="#F7F7F7" align="center" width="100%" border="0" cellpadding="3" cellspacing="0">
		<tr>
			<td align="left" valign="top">
				<br/>
				<table align="center" width="70%" cellpadding="3" cellspacing="1" border="0">
                <tr>
					<td width="6%">&nbsp;</td>
					<td width="47%" align="left" valign="top"><?php echo CBE1_INVITE_FNAME; ?> <span class="req">* </span></td>
					<td width="47%" align="left" valign="top"><?php echo CBE1_INVITE_EMAIL; ?> <span class="req">* </span><br/>
				</tr>
				<?php for ($i=1; $i<=FRIENDS_INVITATIONS_LIMIT; $i++) { ?>
                <tr>
					<td align="center" valign="middle"><span style="color: #777"><?php echo $i; ?>.</span></td>
					<td align="left" valign="top"><input type="text" name="fname[<?php echo $i; ?>]" class="textbox" value="<?php echo $fname[$i]; ?>" size="25" /></td>
					<td align="left" valign="top"><input type="text" name="femail[<?php echo $i; ?>]" class="textbox" value="<?php echo $femail[$i]; ?>" size="25" /></td>
				</tr>
				<?php } ?>
                <tr>
					<td>&nbsp;</td>
					<td colspan="2" align="left" valign="top">
						<?php echo CBE1_INVITE_MESSAGE; ?>:<br>
						<textarea name="umessage" id="umessage" class="textbox2" cols="53" rows="5"><?php echo getPostParameter('umessage'); ?></textarea>
					</td>
				</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td height="40" align="center" valign="top">
				<input type="hidden" name="action" id="action" value="friend" />
				<input type="submit" class="submit" name="Send" id="Send" value="<?php echo CBE1_INVITE_BUTTON; ?>" />
			</td>
		</tr>
		</table>
		</form>

	<?php } ?>


	<h1><?php echo CBE1_INVITE_REFERRALS; ?></h1>
	<a name="referrals"></a>

	<?php

		$results_per_page = 10;
		$cc = 0;

		////////////////// filter  //////////////////////
		if (isset($_GET['column']) && $_GET['column'] != "")
		{
			switch ($_GET['column'])
			{
				case "fname": $rrorder = "fname"; break;
				case "country": $rrorder = "country"; break;
				case "created": $rrorder = "created"; break;
				default: $rrorder = "created"; break;
			}
		}
		else
		{
			$rrorder = "created";
		}

		if (isset($_GET['order']) && $_GET['order'] != "")
		{
			switch ($_GET['order'])
			{
				case "asc": $rorder = "asc"; break;
				case "desc": $rorder = "desc"; break;
				default: $rorder = "desc"; break;
			}
		}
		else
		{
			$rorder = "desc";
		}
		//////////////////////////////////////////////////

		if (isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0) { $page = (int)$_GET['page']; } else { $page = 1; }
		$from = ($page-1)*$results_per_page;

		$refs_query = "SELECT *, DATE_FORMAT(created, '%e %b %Y %h:%i %p') AS signup_date FROM cashbackengine_users WHERE ref_id='$userid' ORDER BY $rrorder $rorder LIMIT $from, $results_per_page";
		$total_refs_result = smart_mysql_query("SELECT * FROM cashbackengine_users WHERE ref_id='$userid'");
		$total_refs = mysql_num_rows($total_refs_result);

		$refs_result = smart_mysql_query($refs_query);
		$total_refs_on_page = mysql_num_rows($refs_result);

		if ($total_refs > 0)
		{
	?>
			<div class="browse_top">
			<div class="sortby">
				<form action="#referrals" id="form1" name="form1" method="get">
					<span><?php echo CBE1_SORT_BY; ?>:</span>
					<select name="column" id="column" onChange="document.form1.submit()">
						<option value="created" <?php if ($_GET['column'] == "added") echo "created"; ?>><?php echo CBE1_INVITE_SDATE; ?></option>
						<option value="fname" <?php if ($_GET['column'] == "fname") echo "selected"; ?>><?php echo CBE1_INVITE_SNAME; ?></option>
						<option value="country" <?php if ($_GET['column'] == "country") echo "selected"; ?>><?php echo CBE1_INVITE_SCOUNTRY; ?></option>
					</select>
					<select name="order" id="order" onChange="document.form1.submit()">
						<option value="desc" <?php if ($_GET['order'] == "desc") echo "selected"; ?>><?php echo CBE1_SORT_DESC; ?></option>
						<option value="asc" <?php if ($_GET['order'] == "asc") echo "selected"; ?>><?php echo CBE1_SORT_ASC; ?></option>
					</select>
					<input type="hidden" name="page" value="<?php echo $page; ?>" />
				</form>
			</div>
			<div class="results">
				<?php echo CBE1_RESULTS_SHOWING; ?> <?php echo ($from + 1); ?> - <?php echo min($from + $total_refs_on_page, $total_refs); ?> <?php echo CBE1_RESULTS_OF; ?> <?php echo $total_refs; ?>
			</div>
			</div>

			<table align="center" class="btb" width="100%" border="0" cellspacing="0" cellpadding="5">
			<tr>
				<th width="50%"><?php echo CBE1_INVITE_SNAME; ?></th>
				<th width="25%"><?php echo CBE1_INVITE_SCOUNTRY; ?></th>
				<th width="25%"><?php echo CBE1_INVITE_SDATE; ?></th>
			</tr>
			<?php while ($refs_row = mysql_fetch_array($refs_result)) { $cc++; ?>
			<tr class="<?php if (($cc%2) == 0) echo "row_even"; else echo "row_odd"; ?>">
				<td align="left" valign="middle"><img src="<?php echo SITE_URL; ?>images/referral_icon.png" align="absmiddle" /> &nbsp; <b><?php echo $refs_row['fname']." ".$refs_row['lname']; ?></b></td>
				<td align="center" valign="middle"><?php echo GetCountry($refs_row['country']); ?></td>
				<td align="center" valign="middle"><?php echo $refs_row['signup_date']; ?></td>
			</tr>
			<?php } ?>
			</table>

			<?php echo ShowPagination("users",$results_per_page,"invite.php?column=$rrorder&order=$rorder&", "WHERE ref_id='".(int)$userid."'"); ?>
		
		<?php }else{ ?>
			<p><?php echo CBE1_INVITE_NOREFS; ?></p>
		<?php } ?>


<?php require_once ("inc/footer.inc.php"); ?>