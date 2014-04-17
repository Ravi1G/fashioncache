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

	
	if (isset($_POST['action']) && $_POST['action'] == "editprofile")
	{
		$fname			= mysql_real_escape_string(ucfirst(strtolower(getPostParameter('fname'))));
		$lname			= mysql_real_escape_string(ucfirst(strtolower(getPostParameter('lname'))));
		$email			= mysql_real_escape_string(strtolower(getPostParameter('email')));
		$address		= mysql_real_escape_string(getPostParameter('address'));
		$address2		= mysql_real_escape_string(getPostParameter('address2'));
		$city			= mysql_real_escape_string(getPostParameter('city'));
		$state			= mysql_real_escape_string(getPostParameter('state'));
		$zip			= mysql_real_escape_string(getPostParameter('zip'));
		$country		= (int)getPostParameter('country');
		$phone			= mysql_real_escape_string(getPostParameter('phone'));
		$newsletter		= (int)getPostParameter('newsletter');
		
		unset($errs);
		$errs = array();

		if(!($fname && $lname && $email))
		{
			$errs[] = CBE1_MYPROFILE_ERR;
		}

		if(isset($email) && $email !="" && !preg_match("/^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/", $email))
		{
			$errs[] = CBE1_MYPROFILE_ERR1;
		}

		if (count($errs) == 0)
		{
			$up_query = "UPDATE cashbackengine_users SET email='$email', fname='$fname', lname='$lname', address='$address', address2='$address2', city='$city', state='$state', zip='$zip', country='$country', phone='$phone', newsletter='$newsletter' WHERE user_id='$userid' LIMIT 1";
		
			if (smart_mysql_query($up_query))
			{
				$_SESSION['FirstName'] = $fname;
				header("Location: myprofile.php?msg=1");
				exit();
			}
		}
	}


	if (isset($_POST['action']) && $_POST['action'] == "changepwd")
	{
		$pwd		= mysql_real_escape_string(getPostParameter('password'));
		$newpwd		= mysql_real_escape_string(getPostParameter('newpassword'));
		$newpwd2	= mysql_real_escape_string(getPostParameter('newpassword2'));

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
			$upp_query = "UPDATE cashbackengine_users SET password='".PasswordEncryption($newpwd)."' WHERE user_id='$userid' LIMIT 1";
		
			if (smart_mysql_query($upp_query))
			{
				header("Location: myprofile.php?msg=2");
				exit();
			}	
		}
	}

	///////////////  Page config  ///////////////
	$PAGE_TITLE = CBE1_MYPROFILE_TITLE;

	require_once ("inc/header.inc.php");

?>
		

		

		<!--<p align="right"><span class="req">* <?php echo CBE1_LABEL_REQUIRED; ?></span></p>-->
		<div class="container standardContainer innerRegularPages">
			<div class="leftAligned sidebar2">
				<div class="selfSection">
					<h2>My Fashion Cache</h2>
					<div class="secondaryNavigation">
						<div>Account Info</div>
						<div class="current">My Profile</div>
						<div>Purchase History</div>
						<div>Cash Back Method</div>
						<div>Invite Friends &#x0026; Earn $</div>
					</div>
				</div>
				<div class="selfSection">
					<h2>Cash Back Summary</h2>
					<div class="secondaryNavigation">
						<div>Pending Cash Back</div>
						<div>Recently Added</div>
						<div>Big Fat Payments</div>
						<div>Total Cash Back</div>
					</div>
				</div>
			</div>
			<div class="rightAligned flowContent1">
				<h1><?php echo CBE1_MYPROFILE_TITLE; ?></h1>				
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
													case "1": echo CBE1_MYPROFILE_MSG1; break;
													case "2": echo CBE1_MYPROFILE_MSG2; break;
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
						if (count($errs) > 0)
						{
							foreach ($errs as $errorname) { $allerrors .= '<li><div class="errorMessage">' . $errorname . '</div></li>'; }
							?>							
							 <div class="errorMessageContainer">
								<div class="leftContainer errorIcon"></div>
								<div class="leftContainer">	
									<!-- For Single line error, add singleError class -->
								   <ul class="standardList errorList <?php if (count($errs) < 2) { ?>singleError<?php } ?>"> 	             			
									  <?php echo $allerrors;?>															
								   </ul>
								</div>              
								<div class="cb"></div>			
							</div>					
							<?php 
						}
				?>
				<form action="" method="post">
					<div class="customTable">
						<div class="row leftAligned namePlates FnamePlate">
							<div class="label"><?php echo CBE1_LABEL_FNAME; ?><sup class="manadatoryField">*</sup></div>
							<div class="data"><input type="text" name="fname" id="fname" value="<?php echo $row['fname'];?>" size="25" /></div>
						</div>
						<div class="row leftAligned namePlates">
							<div class="label"><?php echo CBE1_LABEL_LNAME; ?><sup class="manadatoryField">*</sup></div>
							<div class="data"><input type="text" name="lname" id="lname" value="<?php echo $row['lname']; ?>" size="25" /></div>
						</div>
						<div class="cb"></div>
						<div class="row">
							<div class="label"><?php echo CBE1_LABEL_EMAIL; ?><sup class="manadatoryField">*</sup></div>
							<div class="data emailPlate">
								<input type="text" name="email" id="email" value="<?php echo $row['email']; ?>" size="25" />
							</div>				
						</div>
						<div class="row">
							<div class="label"><?php echo CBE1_LABEL_ADDRESS1; ?></div>
							<div class="data emailPlate">
								<input type="text" name="address" id="address" value="<?php echo $row['address']; ?>" size="25" />
							</div>				
						</div>
						<div class="row">
							<div class="label"><?php echo CBE1_LABEL_ADDRESS2; ?></div>
							<div class="data emailPlate">
								<input type="text" name="address2" id="address2" value="<?php echo $row['address2']; ?>" size="25" />
							</div>				
						</div>					
						<div class="row leftAligned locationPlate">
							<div class="label"><?php echo CBE1_LABEL_CITY; ?></div>
							<div class="data">
								<input type="text" name="city" id="city" value="<?php echo $row['city']; ?>" size="25" />
							</div>				
						</div>
						<div class="row leftAligned locationPlate">
							<div class="label"><?php echo CBE1_LABEL_STATE; ?></div>
							<div class="data ">
								<input type="text" name="state" id="state" value="<?php echo $row['state']; ?>" size="25" />
							</div>				
						</div>
						<div class="row leftAligned locationPlate">
							<div class="label"><?php echo CBE1_LABEL_ZIP; ?></div>
							<div class="data ">
								<input type="text" name="zip" id="zip" value="<?php echo $row['zip']; ?>" size="25" />
							</div>				
						</div>
						<div class="row leftAligned locationPlate">
							<div class="label"><?php echo CBE1_LABEL_COUNTRY; ?></div>
							<div class="data">
								<select name="country" id="country">
								<?php
				
									$sql_country = "SELECT * FROM cashbackengine_countries WHERE signup='1' AND status='active' ORDER BY name ASC";
									$rs_country = smart_mysql_query($sql_country);
									$total_country = mysql_num_rows($rs_country);
				
									if ($total_country > 0)
									{
										while ($row_country = mysql_fetch_array($rs_country))
										{
											if ($row['country'] == $row_country['country_id'])
												echo "<option value='".$row_country['country_id']."' selected>".$row_country['name']."</option>\n";
											else
												echo "<option value='".$row_country['country_id']."'>".$row_country['name']."</option>\n";
										}
									}
				
								?>
								</select>
							</div>				
						</div>					
						<div class="row phonePlates leftAligned locationPlate">
							<div class="label"><?php echo CBE1_LABEL_PHONE; ?></div>
							<div class="data">
								<input type="text" class="phoneInput" name="phone" id="phone" value="<?php echo $row['phone']; ?>" size="25" />
							</div>								
						</div>
						<div class="cb"></div>
						 <div class="newsLetters"><input type="checkbox" name="newsletter" class="yesNewsletter" value="1" <?php echo (@$row['newsletter'] == 1) ? "checked" : "" ?>/> <?php echo CBE1_MYPROFILE_NEWSLETTER; ?></div>
						<input type="hidden" name="action" value="editprofile" />
						<input type="submit" class="hidden" name="Update" id="Update" value="<?php echo CBE1_MYPROFILE_UPBUTTON; ?>" />
						<input type="button" class="hidden" name="cancel" id="CancelIt" value="<?php echo CBE1_CANCEL_BUTTON; ?>" onClick="javascript:document.location.href='myaccount.php'" />
						<div class="allStores forSignUp">
							<span id="updateForm">UPDATE</span>&#x00A0;&#x00A0;&#x00A0;<span id="cancelForm">CANCEL</span>
						</div>			
					</div>		
				</form>
			</div>
			<div class="cb"></div>
		</div>






		<!--<form action="" method="post">
          <table width="70%" align="center" cellpadding="3" cellspacing="0" border="0">
            <tr>
              <td width="150" align="right" valign="middle"><?php echo CBE1_LABEL_USERNAME; ?>:</td>
              <td align="left" valign="top"><span class="username"><?php echo $row['username']; ?></span></td>
            </tr>
            <tr>
              <td align="right" valign="middle"><span class="req">* </span>:</td>
              <td align="left" valign="top"></td>
            </tr>
            <tr>
              <td align="right" valign="middle"><span class="req">* </span>:</td>
              <td align="left" valign="top"></td>
            </tr>
            <tr>
              <td align="right" valign="middle"><span class="req">* </span>:</td>
              <td align="left" valign="top"></td>
            </tr>
            <tr>
              <td align="right" valign="middle">:</td>
              <td align="left" valign="top"></td>
            </tr>
            <tr>
              <td align="right" valign="middle">:</td>
              <td align="left" valign="top"></td>
            </tr>
            <tr>
              <td align="right" valign="middle">:</td>
              <td align="left" valign="top"></td>
            </tr>
            <tr>
              <td align="right" valign="middle">:</td>
              <td align="left" valign="top"></td>
            </tr>
            <tr>
              <td align="right" valign="middle">:</td>
              <td align="left" valign="top"></td>
            </tr>
            <tr>
              <td align="right" valign="middle">:</td>
              <td align="left" valign="top">
				
			  </td>
            </tr>
            <tr>
              <td align="right" valign="middle">:</td>
              <td align="left" valign="top"></td>
            </tr>
			<tr>
				<td align="right" valign="top">&nbsp;</td>
				<td align="left" valign="top">
				</td>
			</tr>
           <tr>
            <td colspan="2" align="center" valign="bottom">
				
			</td>
          </tr>
          </table>
        </form>-->
		


		<center><h3><?php echo CBE1_MYPROFILE_PASSWORD; ?></h3></center>

		<?php
				if (count($errs2) > 0)
				{
					foreach ($errs2 as $errorname) { $allerrors .= "&#155; ".$errorname."<br/>\n"; }
					echo "<div class='error_msg' style='width: 60%'>".$allerrors."</div>";
				}
		?>
		  <form action="" method="post">
          <table width="70%" align="center" cellpadding="3" cellspacing="0" border="0">
            <tr>
              <td width="150" nowrap="nowrap" align="right" valign="middle"><?php echo CBE1_MYPROFILE_OPASSWORD; ?>:</td>
              <td align="left" valign="top"><input type="password" class="textbox" name="password" id="password" value="" size="25" /></td>
            </tr>
            <tr>
              <td nowrap="nowrap" align="right" valign="middle"><?php echo CBE1_MYPROFILE_NPASSWORD; ?>:</td>
              <td align="left" valign="top"><input type="password" class="textbox" name="newpassword" id="newpassword" value="" size="25" /></td>
            </tr>
            <tr>
              <td nowrap="nowrap" align="right" valign="middle"><?php echo CBE1_MYPROFILE_CNPASSWORD; ?>:</td>
              <td align="left" valign="top"><input type="password" class="textbox" name="newpassword2" id="newpassword2" value="" size="25" /></td>
            </tr>
          <tr>
            <td colspan="2" align="center" valign="bottom">
				<input type="hidden" name="action" value="changepwd" />
				<input type="submit" class="submit" name="Change" id="Change" value="<?php echo CBE1_MYPROFILE_PWD_BUTTON; ?>" />
				<input type="button" class="cancel" name="cancel" value="<?php echo CBE1_CANCEL_BUTTON; ?>" onClick="javascript:document.location.href='myaccount.php'" />
			</td>
          </tr>
          </table>
        </form>

<?php require_once ("inc/footer.inc.php"); ?>