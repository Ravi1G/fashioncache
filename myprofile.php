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
		if(isset($zip) && $zip!="" && !is_numeric($zip))
		{
			$errs[] = "Zip code must be numeric";
		}
		if(isset($phone) && $phone!="" && !is_numeric($phone))
		{
			$errs[] = "Phone number must be numeric";
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

	/*
		PREVIOUS CHANGE PASSWORD 
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
	}*/

	///////////////  Page config  ///////////////
	$PAGE_TITLE = CBE1_MYPROFILE_TITLE;

	require_once ("inc/header.inc.php");

?>

        <h1><?php echo CBE1_MYPROFILE_TITLE; ?></h1>
        
        <!-- Show the leftside bar if the user is logged in -->
		<?php 
			if(isLoggedIn())	
				require_once("inc/left_sidebar.php");
		?>
			
		<?php if (isset($_GET['msg']) && is_numeric($_GET['msg']) && !$_POST['action']) { ?>
			<div class="success_msg">
				<?php

					switch ($_GET['msg'])
					{
						case "1": echo CBE1_MYPROFILE_MSG1; break;
					}

				?>
			</div>
		<?php } ?>


		<?php
				if (count($errs) > 0)
				{
					foreach ($errs as $errorname) { $allerrors .= "&#155; ".$errorname."<br/>\n"; }
					echo "<div class='error_msg'>".$allerrors."</div>";
				}
		?>

		<p align="right"><span class="req">* <?php echo CBE1_LABEL_REQUIRED; ?></span></p>

		<form action="" method="post">
          <table width="70%" align="center" cellpadding="3" cellspacing="0" border="0">
            <tr>
              <td width="150" align="right" valign="middle"><?php echo CBE1_LABEL_USERNAME; ?>:</td>
              <td align="left" valign="top"><span class="username"><?php echo $row['username']; ?></span></td>
            </tr>
            <tr>
              <td align="right" valign="middle"><span class="req">* </span><?php echo CBE1_LABEL_FNAME; ?>:</td>
              <td align="left" valign="top"><input type="text" class="textbox" name="fname" id="fname" value="<?php if(isset($fname) && ($fname!="")){echo $fname;} elseif(isset($row['fname'])&& ($row['fname']!="")) {echo $row['fname'];} ?>" size="25" /></td>
            </tr>
            <tr>
              <td align="right" valign="middle"><span class="req">* </span><?php echo CBE1_LABEL_LNAME; ?>:</td>
              <td align="left" valign="top"><input type="text" class="textbox" name="lname" id="lname" value="<?php  if(isset($lname) && ($lname!="")){echo $lname;}elseif(isset($row['lname']) && ($row['lname']!="")){echo $row['lname'];} ?>" size="25" /></td>
            </tr>
            <tr>
              <td align="right" valign="middle"><span class="req">* </span><?php echo CBE1_LABEL_EMAIL; ?>:</td>
              <td align="left" valign="top"><input type="text" class="textbox" name="email" id="email" value="<?php if(isset($email)&& ($email!="")){echo $email;} elseif(isset($row['email'])&& ($row['email']!="")) {echo $row['email'];} ?>" size="25" /></td>
            </tr>
            <tr>
              <td align="right" valign="middle"><?php echo CBE1_LABEL_ADDRESS1; ?>:</td>
              <td align="left" valign="top"><input type="text" class="textbox" name="address" id="address" value="<?php if(isset($address) && ($address!="")){echo $address;} elseif(isset($row['address'])&& ($row['address']!="")){echo $row['address'];} ?>" size="25" /></td>
            </tr>
            <tr>
              <td align="right" valign="middle"><?php echo CBE1_LABEL_ADDRESS2; ?>:</td>
              <td align="left" valign="top"><input type="text" class="textbox" name="address2" id="address2" value="<?php if(isset($address2)&& ($address2!="")){echo $address2;} elseif(isset($row['address2'])&& ($row['address2']!=""))echo $row['address2']; ?>" size="25" /></td>
            </tr>
            <tr>
              <td align="right" valign="middle"><?php echo CBE1_LABEL_CITY; ?>:</td>
              <td align="left" valign="top"><input type="text" class="textbox" name="city" id="city" value="<?php if(isset($city) && ($city!="")){echo $city;} elseif(isset($row['city']) && ($row['city']!="")){echo $row['city'];} ?>" size="25" /></td>
            </tr>
            <tr>
              <td align="right" valign="middle"><?php echo CBE1_LABEL_STATE; ?>:</td>
              <td align="left" valign="top"><input type="text" class="textbox" name="state" id="state" value="<?php if(isset($state) && ($state!="")){echo $state;} elseif(isset($row['state']) && ($row['state']!="")){echo $row['state'];} ?>" size="25" /></td>
            </tr>
            <tr>
              <td align="right" valign="middle"><?php echo CBE1_LABEL_ZIP; ?>:</td>
              <td align="left" valign="top"><input type="text" class="textbox" name="zip" id="zip" value="<?php if(isset($zip) && ($zip!="")){echo $zip;} elseif(isset($row['zip']) && ($row['zip']!="")) {echo $row['zip'];} ?>" size="25" /></td>
            </tr>
            <tr>
              <td align="right" valign="middle"><?php echo CBE1_LABEL_COUNTRY; ?>:</td>
              <td align="left" valign="top">
				<select name="country" class="textbox2" id="country" style="width: 170px;">
				<?php

					$sql_country = "SELECT * FROM cashbackengine_countries WHERE signup='1' AND status='active' ORDER BY name ASC";
					$rs_country = smart_mysql_query($sql_country);
					$total_country = mysql_num_rows($rs_country);

					if ($total_country > 0)
					{
						while ($row_country = mysql_fetch_array($rs_country))
						{
							if(isset($country) && ($country!="") && ($country==$row_country['country_id']))
								{
									echo "<option value='".$row_country['country_id']."' selected>".$row_country['name']."</option>\n";
								}
							elseif ($row['country'] == $row_country['country_id'])
								{
									echo "<option value='".$row_country['country_id']."' selected>".$row_country['name']."</option>\n";
								}
							else
							{
								echo "<option value='".$row_country['country_id']."'>".$row_country['name']."</option>\n";
							}
						}
					}

				?>
				</select>
			  </td>
            </tr>
            <tr>
              <td align="right" valign="middle"><?php echo CBE1_LABEL_PHONE; ?>:</td>
              <td align="left" valign="top"><input type="text" class="textbox" name="phone" id="phone" value="<?php if(isset($phone) && ($phone!="")){echo $phone;} elseif(isset($row['phone']) && ($row['phone']!="")) {echo $row['phone'];} ?>" size="25" /></td>
            </tr>
			<tr>
				<td align="right" valign="top">&nbsp;</td>
				<td align="left" valign="top"><input type="checkbox" name="newsletter" class="checkboxx" value="1" <?php if(isset($newsletter) && ($newsletter!="")){ echo "checked";}elseif(isset($newsletter) && ($newsletter=="")){echo "";} elseif(isset($row['newsletter']) && ($row['newsletter']!="")){echo "checked";}else{ echo "";} ?>/> <?php echo CBE1_MYPROFILE_NEWSLETTER; ?>
				</td>
			</tr>
           <tr>
            <td colspan="2" align="center" valign="bottom">
				<input type="hidden" name="action" value="editprofile" />
				<input type="submit" class="submit" name="Update" id="Update" value="<?php echo CBE1_MYPROFILE_UPBUTTON; ?>" />
				<!-- <input type="button" class="cancel" name="cancel" value="<?php echo CBE1_CANCEL_BUTTON; ?>" onClick="javascript:document.location.href='myaccount.php'" />-->
			</td>
          </tr>
          </table>
        </form>
		<br/>


		<!-- <center><h3><?php echo CBE1_MYPROFILE_PASSWORD; ?></h3></center>-->

		<?php
				if (count($errs2) > 0)
				{
					foreach ($errs2 as $errorname) { $allerrors .= "&#155; ".$errorname."<br/>\n"; }
					echo "<div class='error_msg' style='width: 60%'>".$allerrors."</div>";
				}
		?>
		<!-- OLD CHANGE PASSWORD FUNCTIONALITY
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
 -->
<?php require_once ("inc/footer.inc.php"); ?>