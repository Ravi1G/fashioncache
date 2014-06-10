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
						
						$recipients = $friend_name."|".$friend_email;
						
						// save invitations in history - n records for n invitations by a single sender
						 
						smart_mysql_query("INSERT INTO cashbackengine_invitations 
												SET user_id='".(int)$userid."', 
												recipients='$recipients', 
												message='$umessage',
												status='pending' , 
												sent_date=NOW()");
						$invite_id =  mysql_insert_id();
						$ReferralLink.='&in_id='.$invite_id;
						
						$esubject = $etemplate['email_subject'];

						if ($umessage != "")
						{
							//$emessage = $umessage;
							$emessage = $etemplate['email_message'];
							$emessage = str_replace("{friend_name}", $friend_name, $emessage);
							$emessage = str_replace("{first_name}", $uname, $emessage);
							$emessage = str_replace("{referral_link}", $ReferralLink, $emessage);
							$emessage = str_replace("{friend_message}", $umessage, $emessage);
							//$emessage .= ": Sign up here: <a href=\'$ReferralLink\'>".$ReferralLink."</a>";
						}
						else
						{
							$emessage = $etemplate['email_message'];
							$emessage = str_replace("{friend_name}", $friend_name, $emessage);
							$emessage = str_replace("{first_name}", $uname, $emessage);
							$emessage = str_replace("{referral_link}", $ReferralLink, $emessage);
							$emessage = str_replace("{friend_message}", "", $emessage);
						}

						$to_email = $friend_name.' <'.$friend_email.'>';
						$subject = $esubject;
						$message = $emessage;
		
						$headers  = 'MIME-Version: 1.0' . "\r\n";
						$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
						$headers .= 'From: '.SITE_TITLE.' <'.NOREPLY_MAIL.'>' . "\r\n";
						@mail($to_email, $subject, $message, $headers);
					}
				}
			////////////////////////////////////////////////////////////////////////////////

			// save invitations in history - old functionality
			//smart_mysql_query("INSERT INTO cashbackengine_invitations SET user_id='".(int)$userid."', recipients='$recipients', message='$umessage',status='pending' , sent_date=NOW()");

			header("Location: invite.php?msg=1");
			exit();
		}
		else
		{
			$allerrors = "";
			foreach ($errs as $errorname)
				$allerrors .= $errorname."<br/>\n";
		}
	}

	///////////////  Page config  ///////////////
	$PAGE_TITLE = CBE1_INVITE_TITLE;

	require_once ("inc/header.inc.php");

?>

<div class="container standardContainer innerRegularPages">			
			<?php 
			/* Left SideBar Content */
			if(isLoggedIn())
			{
				require_once("inc/left_sidebar.php");				
			}
			?>
			
			<div class="rightAligned flowContent1 responsiveContainer">				
					<div class="RetailerContainer referFriendSectionContainers">
					
						<!--  Invite Friends Advertisement Starts -->
						<div class="inviteAd">
							<div class="fl">
								<div class="line1">Invite Your Friends</div>
								<div class="line2">GET REWARDED</div>
								<div class="line1">for spreading the word</div>
								<div class="line3">Earn a <b>$10 Gift Card</b> for each friend that signs up &#x0026; makes a qualified purchase.</div>
							</div>
							<div class="fr">
								<img src="<?php echo SITE_URL;?>img/ndStrom.jpg" alt="Nordstrom"/>
							</div>
							<div class="cb"></div>
						</div>
						<!--  Invite Friends Advertisement Ends -->
						
						<div class="categoryHeading categoryHeadingWithMargins referenceSectionHeadings">
							<h1><?php echo CBE1_INVITE_TITLE; ?></h1>
						</div>					
						<?php if (REFER_FRIEND_BONUS > 0) { ?>						
						<div class="referAFriend">
							<?php echo str_replace("%amount%",DisplayMoney(REFER_FRIEND_BONUS),CBE1_INVITE_TEXT); ?>
						</div>
						<?php } ?>
						<div class="yourReferralLink">
							<div class="leftAligned yourReferralLinkContainer">
								<div>
									<b><?php echo CBE1_INVITE_LINK; ?>:</b><input type="text" class="reflink_textbox" size="60" readonly="readonly" onfocus="this.select();" onclick="this.focus();this.select();" value="<?php echo $ReferralLink; ?>" />
									<span class="isResponsive">
										<a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode($ReferralLink); ?>&t=<?php echo SITE_TITLE; ?>" target="_blank" title="<?php echo CBE1_SHARE_FACEBOOK; ?>"><img src="<?php echo SITE_URL;?>img/fbIcon.jpg" align="absmiddle" alt="<?php echo CBE1_SHARE_FACEBOOK; ?>" /></a>
										<a href="http://twitter.com/intent/tweet?source=sharethiscom&text=<?php echo SITE_TITLE; ?>&url=<?php echo urlencode($ReferralLink); ?>" target="_blank" title="<?php echo CBE1_SHARE_TWITTER; ?>"><img src="<?php echo SITE_URL; ?>img/twtIcon.jpg" align="absmiddle" alt="<?php echo CBE1_SHARE_TWITTER; ?>" /></a>
										<a href="https://plus.google.com/share?url=<?php echo urlencode($ReferralLink); ?>" target="_blank" title="<?php echo CBE1_SHARE_GOOGLE; ?>"><img src="<?php echo SITE_URL; ?>img/gpIcon.jpg" align="absmiddle" alt="<?php echo CBE1_SHARE_GOOGLE; ?>" /></a>
									</span>
								</div>
							</div>
							<div class="rightAligned notResponsive">
								<a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode($ReferralLink); ?>&t=<?php echo SITE_TITLE; ?>" target="_blank" title="<?php echo CBE1_SHARE_FACEBOOK; ?>"><img src="<?php echo SITE_URL;?>img/fbIcon.jpg" align="absmiddle" alt="<?php echo CBE1_SHARE_FACEBOOK; ?>" /></a>
								<a href="http://twitter.com/intent/tweet?source=sharethiscom&text=<?php echo SITE_TITLE; ?>&url=<?php echo urlencode($ReferralLink); ?>" target="_blank" title="<?php echo CBE1_SHARE_TWITTER; ?>"><img src="<?php echo SITE_URL; ?>img/twtIcon.jpg" align="absmiddle" alt="<?php echo CBE1_SHARE_TWITTER; ?>" /></a>
								<a href="https://plus.google.com/share?url=<?php echo urlencode($ReferralLink); ?>" target="_blank" title="<?php echo CBE1_SHARE_GOOGLE; ?>"><img src="<?php echo SITE_URL; ?>img/gpIcon.jpg" align="absmiddle" alt="<?php echo CBE1_SHARE_GOOGLE; ?>" /></a>
							</div>
							<div class="cb"></div>
						</div>
					</div>
					<div class="RetailerContainer referFriendSectionContainers">
					<div class="categoryHeading categoryHeadingWithMargins referenceSectionHeadings">
						<h1><?php echo CBE1_INVITE_TITLE2; ?></h1>
					</div>					
					<?php if (REFER_FRIEND_BONUS > 0) { ?>
					<div class="referAFriend">
						<?php echo str_replace("%amount%",DisplayMoney(REFER_FRIEND_BONUS),CBE1_INVITE_TEXT2); ?>
					</div>
					<?php } ?>
					
					<?php if (isset($_GET['msg']) and $_GET['msg'] == 1) { ?>
						<div class="errorMessageContainer successMessageContainer">
							<div class="leftContainer errorIcon"></div>
							<div class="leftContainer">							
							   <ul class="standardList errorList singleError"> 	             			
									<li><div class="errorMessage"><?php echo CBE1_INVITE_SENT; ?></div></li>															
								</ul>
							</div>              
							<div class="cb"></div>			
						</div>
						<div class="shopNowBotton siteButton inviteMoreButton">
							<a href="<?php echo SITE_URL; ?>invite.php">
								<span><?php echo CBE1_INVITE_SEND_MORE;?> &#x003E;</span>
							</a>
						</div>
					<?php }else{ ?>
					<?php if (isset($allerrors) and $allerrors != "") { ?>
						<div class="errorMessageContainer">
							<div class="leftContainer errorIcon"></div>
							<div class="leftContainer">	
								<!-- For Single line error, add singleError class -->
							   <ul class="standardList errorList singleError"> 	             			
									<li><div class="errorMessage"><?php echo $allerrors; ?></div></li>															
								</ul>
							</div>              
							<div class="cb"></div>			
						</div>
					<?php } ?>
					<div class="referalForm">
						<form action="" method="post">										
								<table class="referalsResponsiveTable">
									<tr>
										<td width="6%">&#x00A0;</td>
										<td width="47%"><b><?php echo CBE1_INVITE_FNAME; ?></b><sup class="manadatoryField">*</sup></td>
										<td width="47%"><b><?php echo CBE1_INVITE_EMAIL; ?></b><sup class="manadatoryField">*</sup></td>
									</tr>
									<?php for ($i=1; $i<=FRIENDS_INVITATIONS_LIMIT; $i++) { ?>
									<tr>
										<td class="listCount">
											<?php echo $i; ?>.
										</td>
										<td>
											<input type="text" name="fname[<?php echo $i; ?>]" class="textbox" value="<?php echo $fname[$i]; ?>" size="25" />
										</td>
										<td>
											<input type="text" name="femail[<?php echo $i; ?>]" class="textbox" value="<?php echo $femail[$i]; ?>" size="25" />
										</td>
									</tr>
									<?php } ?>
									<tr>
										<td>&#x00A0;</td>
										<td colspan="2">
											<div class="messageCaption"><b><?php echo CBE1_INVITE_MESSAGE; ?>:</b></div>
											<textarea name="umessage" id="umessage" class="textbox2" cols="53" rows="5"><?php echo getPostParameter('umessage'); ?></textarea>
										</td>
									</tr>
								</table>
							
								<input type="hidden" name="action" id="action" value="friend" />
								
								<div class="shopNowBotton siteButton sendInvitationButton">
									<a>
										<span><?php echo CBE1_INVITE_BUTTON;?></span>
									</a>
								</div>
								<input type="submit" class="submit hide" name="Send" id="Send"/>							
						</form>
					</div>
					<?php } ?>
				</div>
					<div class="RetailerContainer referFriendSectionContainers">	
					<div class="categoryHeading categoryHeadingWithMargins referenceSectionHeadings" id="referrals">
						<h1><?php echo CBE1_INVITE_REFERRALS; ?></h1>
					</div>				
					<!--<a name="referrals"></a>-->
				
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
				
						/*$refs_query = "SELECT *, DATE_FORMAT(created, '%e %b %Y %h:%i %p') AS signup_date FROM cashbackengine_users WHERE ref_id='$userid' ORDER BY $rrorder $rorder LIMIT $from, $results_per_page";
						$total_refs_result = smart_mysql_query("SELECT * FROM cashbackengine_users WHERE ref_id='$userid'");
						$total_refs = mysql_num_rows($total_refs_result);
				
						$refs_result = smart_mysql_query($refs_query);
						$total_refs_on_page = mysql_num_rows($refs_result);*/
				
						//New query
							$refs_query = smart_mysql_query("SELECT * FROM cashbackengine_invitations WHERE user_id = '$userid'");
							$total_refs = mysql_num_rows($refs_query);
							
						
						if ($total_refs > 0)
						{
						?>
							<div class="sortBarOnTop">
							<!--
								<div class="sortby leftAligned">
									<form action="#referrals" id="form1" name="form1" method="get">
										<span><?php echo CBE1_SORT_BY; ?>:</span>
										<select name="column" id="column" onChange="document.form1.submit()" class="standardDropDown">
											<option value="created" <?php if ($_GET['column'] == "added") echo "created"; ?>><?php echo CBE1_INVITE_SDATE; ?></option>
											<option value="fname" <?php if ($_GET['column'] == "fname") echo "selected"; ?>><?php echo CBE1_INVITE_SNAME; ?></option>
											<option value="country" <?php if ($_GET['column'] == "country") echo "selected"; ?>><?php echo CBE1_INVITE_SCOUNTRY; ?></option>
										</select>
										<select name="order" id="order" onChange="document.form1.submit()" class="standardDropDown">
											<option value="desc" <?php if ($_GET['order'] == "desc") echo "selected"; ?>><?php echo CBE1_SORT_DESC; ?></option>
											<option value="asc" <?php if ($_GET['order'] == "asc") echo "selected"; ?>><?php echo CBE1_SORT_ASC; ?></option>
										</select>
										<input type="hidden" name="page" value="<?php echo $page; ?>" />
									</form>
								</div>
								-->
								<div class="results rightAligned">
									<?php echo CBE1_RESULTS_SHOWING; ?> <?php echo ($from + 1); ?> - <?php echo min($from + $total_refs_on_page, $total_refs); ?> <?php echo CBE1_RESULTS_OF; ?> <?php echo $total_refs; ?>
								</div>
								<div class="cb"></div>
							</div>
							<table class="categoryTable retailerTable referralsTable">
							<thead>
								<tr class="categoryTableHeading">							
									<!-- <td style="width:50%;" class="storeName alignCenter topLeft"><span><?php echo CBE1_INVITE_EMAIL; ?></span></td>							
									<td style="width:50%;" class="storeSite topRight"><?php echo CBE1_INVITE_SDATE; ?></td>-->
									<!-- New functionality -->
									<td style="width:33%;" class="storeName alignCenter topLeft"><span><?php echo CBE1_INVITE_EMAIL; ?></span></td>							
									<td style="width:33%;" class="storeSite storeName"><span class="notResponsive"><?php echo "Invitation Date"; ?></span><span class="isResponsive">Sent On</span></td>
									<td style="width:33%;" class="storeSite topRight"><span class="notResponsive"><?php echo "Invitation Status"; ?></span><span class="isResponsive">Status</span></td>
								</tr>
							</thead>
							<tbody>
								<!-- New functionality -->
								<?php while ($refs_row = mysql_fetch_array($refs_query)) { $cc++; ?>
								<tr>
								<td>
									<span class="notResponsive"><img src="<?php echo SITE_URL; ?>images/referral_icon.png" align="absmiddle" /> &nbsp; </span>
									<?php 
									$email = explode('|',$refs_row['recipients']);
									echo $email = $email[1];
									?>
								</td>						
								<td class="alignCenter"><?php echo $refs_row['sent_date']; ?></td>
								<td class="alignCenter"><?php echo $refs_row['status']?></td>
								</tr>
								<?php } ?>
								
								<!--<?php while ($refs_row = mysql_fetch_array($refs_result)) { $cc++; ?>
								<tr>
								<td><img src="<?php echo SITE_URL; ?>images/referral_icon.png" align="absmiddle" /> &nbsp; <?php echo $refs_row['email']." ".$refs_row['lname']; ?></td>						
								<td class="alignCenter"><?php echo $refs_row['signup_date']; ?></td>
							</tr>
							<?php } ?>-->
							</tbody>
							</table>
							
							<?php /* ?>
							
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
				
							<?php */ ?>	
							
							<?php echo ShowPagination("users",$results_per_page,"invite.php?column=$rrorder&order=$rorder&", "WHERE ref_id='".(int)$userid."'"); ?>
						
						<?php }else{ ?>
							<p><?php echo CBE1_INVITE_NOREFS; ?></p>
						<?php } ?>			
				</div>
			</div>
			<div class="cb"></div>
		</div>
	
<?php require_once ("inc/footer.inc.php"); ?>