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
	
	$query	= "SELECT * FROM cashbackengine_users WHERE user_id='$userid' AND status='active' LIMIT 1";
	$result = smart_mysql_query($query);

	if (mysql_num_rows($result) > 0)
	{
		$row = mysql_fetch_array($result);
	}
	else
	{
		header ("Location: logout.php");
		exit();
	}
?>

<?php 
if (isset($_POST['action']) && $_POST['action'] == "changepwd")
	{
		$pwd		= mysql_real_escape_string(getPostParameter('password'));
		$newpwd		= mysql_real_escape_string(getPostParameter('newpassword'));
		$newpwd2	= mysql_real_escape_string(getPostParameter('newpassword2'));
		$newsletter = mysql_real_escape_string(getPostParameter('newsletter'));

		$errs2 = array();

		if (!($pwd && $newpwd && $newpwd2))
		{
			$errs2[] = CBE1_MYPROFILE_ERR0;
		}
		else
		{
			if (PasswordEncryption($pwd) !== $row['password'])
			{
				$errs2[] = CBE1_MYPROFILE_ERR2;
			}

			if ($newpwd !== $newpwd2)
			{
				$errs2[] = CBE1_MYPROFILE_ERR3;
			}
			elseif ((strlen($newpwd)) < 6 || (strlen($newpwd2) < 6) || (strlen($newpwd)) > 20 || (strlen($newpwd2) > 20))
			{
				$errs2[] = CBE1_MYPROFILE_ERR4;
			}
			elseif (stristr($newpwd, ' '))
			{
				$errs2[] = CBE1_MYPROFILE_ERR5;
			}
		}

		if (count($errs2) == 0)
		{
			$upp_query = "UPDATE cashbackengine_users SET newsletter='$newsletter', password='".PasswordEncryption($newpwd)."' WHERE user_id='$userid' LIMIT 1";
		
			if (smart_mysql_query($upp_query))
			{
				header("Location: change_pwd.php?msg=2");
				exit();
			}	
		}
	}
	
	$PAGE_TITLE = "Change Password";
	require_once ("inc/header.inc.php");
?>
<?php 
	if(isset($_POST['action_newsletter']) && $_POST['action_newsletter']=="newsletter")
	{
		$newsletter = $_POST['newsletter'];
		if($newsletter =="1")
		{
			$value = 1;
		}
		else 
		{
			$value = 0;
		}

		$up_query = "UPDATE cashbackengine_users SET newsletter='$value' WHERE user_id = '$userid'";
		
		if (smart_mysql_query($up_query))
			{
				header("Location: change_pwd.php?msg=3");
				exit();
			}	
	}
?>

<div class="container standardContainer innerRegularPages">
			
			<?php 
			/* Left SideBar Content */
			if(isLoggedIn())
			{
				require_once("inc/left_sidebar.php");				
			}
			?>
			
			<div class="rightAligned flowContent1">
				<h1>Password and Settings</h1>					
					<?php if (isset($_GET['msg']) && is_numeric($_GET['msg']) && !$_POST['action']) { ?>
			
								<div class="errorMessageContainer successMessageContainer">
									<div class="leftContainer errorIcon"></div>
									<div class="leftContainer">	
										<!-- For Single line error, add singleError class -->
									   <ul class="standardList errorList singleError"> 	             			
										  <li>
											<div class="errorMessage">
												<?php			
													switch ($_GET['msg'])
													{
														case "2": echo CBE1_MYPROFILE_MSG2; break;
														case "3": echo "News letter status has been saved successfully"; break;
													}							
												?>
											 </div>
										  </li>															
									   </ul>
									</div>              
									<div class="cb"></div>			
								</div>		
					<?php } ?>				
					<?php
							if (count($errs2) > 0)
							{
								foreach ($errs2 as $errorname) { $allerrors .= '<li><div class="errorMessage">' . $errorname . '</div></li>'; }
								?>							
								 <div class="errorMessageContainer">
									<div class="leftContainer errorIcon"></div>
									<div class="leftContainer">	
										<!-- For Single line error, add singleError class -->
									   <ul class="standardList errorList <?php if (count($errs2) < 2) { ?>singleError<?php } ?>"> 	             			
										  <?php echo $allerrors;?>															
									   </ul>
									</div>              
									<div class="cb"></div>			
								</div>					
								<?php 
							}
					?>	
					<div class="customTable changePassword">
					<h2><?php echo CBE1_MYPROFILE_PASSWORD; ?></h2>			
					 <form action="" method="post">						
							<div class="passwordContent">
								<div class="row locationPlate">
									<div class="label"><?php echo CBE1_MYPROFILE_OPASSWORD; ?></div>
									<div class="data"><input type="password" name="password" id="password" value="" size="25" /></div>
								</div>
								<div class="row locationPlate">
									<div class="label"><?php echo CBE1_MYPROFILE_NPASSWORD; ?></div>
									<div class="data"><input type="password" name="newpassword" id="newpassword" value="" size="25" /></div>
								</div>
								<div class="row locationPlate">
									<div class="label"><?php echo CBE1_MYPROFILE_CNPASSWORD; ?></div>
									<div class="data"><input type="password" name="newpassword2" id="newpassword2" value="" size="25" /></div>
								</div>
								<!-- <div class="row locationPlate">
									<div class="label">News Letter</div>
									<div class="data"><input type="checkbox" name="newsletter" id="newsletter" value="1" size="25" <?php if($row['newsletter']){echo 'checked';}?>/></div>
								</div>-->
															
								
								<input type="hidden" name="action" value="changepwd" />
								<input type="submit" class="hidden" name="Change" id="Update" value="<?php echo CBE1_MYPROFILE_PWD_BUTTON; ?>" />
								<!-- <input type="button" class="cancel" name="cancel" value="<?php echo CBE1_CANCEL_BUTTON; ?>" onClick="javascript:document.location.href='myaccount.php'" />-->
								<div class="allStores forSignUp">
									<span id="updateForm">CHANGE PASSWORD</span><?php /* &#x00A0;&#x00A0;&#x00A0;<span id="cancelForm">CANCEL</span> */ ?>
								</div>
							</div>												
					</form>
					<br/>
					<div class="hr"></div>					
					<h2>Receive Newsletters</h2>
					 <div class="para">For the latest news and CashBack offers, subscribe to our Newsletter Service.</div>
					 <div class="para">To get more from Fashion Cache, including subscription deals and exclusive news from our Blogs and Offers, simply check the checkbox below and hit subscribe.</div>
					 <div class="subscriptionForm">
						
						<form method="post" name="form_newsletter" id="form_newsletter">
							<input type="hidden" name="action_newsletter" value="newsletter">				 
							<input type="checkbox" name="newsletter" value="1" <?php if($row['newsletter']){echo 'checked';}?>/> <b>I'd like to receive the newsletter from Fashion Cache.</b>
							<div class="allStores forSignUp">
							  <span id="updatenewsletter">SAVE</span>
							</div>
						</form>
						
					 </div>
					
				</div>
			 
			 
			 
			 
					 <?php /* <form action="" method="post">
					  <table width="70%" align="center" cellpadding="3" cellspacing="0" border="0">
						<tr>
						  <td width="150" nowrap="nowrap" align="right" valign="middle">:</td>
						  <td align="left" valign="top"></td>
						</tr>
						<tr>
						  <td nowrap="nowrap" align="right" valign="middle">:</td>
						  <td align="left" valign="top"><input type="password" class="textbox" name="newpassword" id="newpassword" value="" size="25" /></td>
						</tr>
						<tr>
						  <td nowrap="nowrap" align="right" valign="middle">:</td>
						  <td align="left" valign="top"></td>
						</tr>
					  <tr>
						<td colspan="2" align="center" valign="bottom">
							<input type="hidden" name="action" value="changepwd" />
							<input type="submit" class="submit" name="Change" id="Change" value="<?php echo CBE1_MYPROFILE_PWD_BUTTON; ?>" />
							<!-- <input type="button" class="cancel" name="cancel" value="<?php echo CBE1_CANCEL_BUTTON; ?>" onClick="javascript:document.location.href='myaccount.php'" />-->
						</td>
					  </tr>
					  </table>
					</form> */ ?>
				</div>
				<div class="cb"></div>
	</div>
	<script>
		$("#updatenewsletter").click(function(){
			$("#form_newsletter").submit();
		});
	</script>
<?php require_once ("inc/footer.inc.php"); ?>