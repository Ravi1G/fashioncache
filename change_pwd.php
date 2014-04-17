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
				header("Location: change_pwd.php?msg=2");
				exit();
			}	
		}
	}
	
	$PAGE_TITLE = "Change Password";
	require_once ("inc/header.inc.php");
?>


<center><h3><?php echo CBE1_MYPROFILE_PASSWORD; ?></h3></center>
	<?php 
			if(isLoggedIn())	
				require_once("inc/left_sidebar.php");
	?>
	<?php if (isset($_GET['msg']) && is_numeric($_GET['msg']) && !$_POST['action']) { ?>
			<div class="success_msg">
				<?php

					switch ($_GET['msg'])
					{
						case "2": echo CBE1_MYPROFILE_MSG2; break;
					}

				?>
			</div>
		<?php } ?>
		
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
				<!-- <input type="button" class="cancel" name="cancel" value="<?php echo CBE1_CANCEL_BUTTON; ?>" onClick="javascript:document.location.href='myaccount.php'" />-->
			</td>
          </tr>
          </table>
        </form>
 
