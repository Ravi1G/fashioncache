<?php
/*******************************************************************\
 * CashbackEngine v2.1
 * http://www.CashbackEngine.net
 *
 * Copyright (c) 2010-2014 CashbackEngine Software. All rights reserved.
 * ------------ CashbackEngine IS NOT FREE SOFTWARE --------------
\*******************************************************************/

	session_start();
	require_once("inc/config.inc.php");


	if (isset($_POST['action']) && $_POST['action'] == "forgot")
	{
		$email = strtolower(mysql_real_escape_string(getPostParameter('email')));

		if (!($email) || $email == "")
		{
			header("Location: forgot.php?msg=1");
			exit();
		}
		else
		{
			if (!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $email))
			{
				header("Location: forgot.php?msg=2");
				exit();
			}
		}
		
		$query = "SELECT * FROM cashbackengine_users WHERE email='$email' AND status='active' LIMIT 1";
		$result = smart_mysql_query($query);

		if (mysql_num_rows($result) > 0)
		{
			$row = mysql_fetch_array($result);
			
			$newPassword = generatePassword(11);
			$update_query = "UPDATE cashbackengine_users SET password='".PasswordEncryption($newPassword)."' WHERE user_id='".(int)$row['user_id']."' LIMIT 1";
			
			if (smart_mysql_query($update_query))
			{
				////////////////////////////////  Send Message  //////////////////////////////
				$etemplate = GetEmailTemplate('forgot_password');
				$esubject = $etemplate['email_subject'];
				$emessage = $etemplate['email_message'];

				$emessage = str_replace("{first_name}", $row['fname'], $emessage);
				$emessage = str_replace("{username}", $row['username'], $emessage);
				$emessage = str_replace("{password}", $newPassword, $emessage);
				$emessage = str_replace("{login_url}", SITE_URL."login.php", $emessage);	
				
				$to_email = $row['fname'].' '.$row['lname'].' <'.$email.'>';
				$subject = $esubject;
				$message = $emessage;			
			
				$headers  = 'MIME-Version: 1.0' . "\r\n";
				$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
				$headers .= 'From: '.SITE_TITLE.' <'.NOREPLY_MAIL.'>' . "\r\n";

				if (@mail($to_email, $subject, $message, $headers))
				{
					header("Location: forgot.php?msg=4");
					exit();
				}
				////////////////////////////////////////////////////////////////////////////////
			}
		}
		else
		{
			header("Location: forgot.php?msg=3");
			exit();
		}

	}

	///////////////  Page config  ///////////////
	$PAGE_TITLE = CBE1_FORGOT_TITLE;

	require_once "inc/header.inc.php";
	
?>

	<h1><?php echo CBE1_FORGOT_TITLE; ?></h1>

	<?php if (isset($_GET['msg']) && is_numeric($_GET['msg']) && $_GET['msg'] != 4) { ?>
		<div class="error_msg">
			<?php if ($_GET['msg'] == 1) { echo CBE1_FORGOT_MSG1; } ?>
			<?php if ($_GET['msg'] == 2) { echo CBE1_FORGOT_MSG2; } ?>
			<?php if ($_GET['msg'] == 3) { echo CBE1_FORGOT_MSG3; } ?>
		</div>
	<?php }elseif($_GET['msg'] == 4){ ?>
		<div class="success_msg"><?php echo CBE1_FORGOT_MSG4; ?></div>
		<p align="center"><a class="goback" href="<?php echo SITE_URL; ?>login.php"><?php echo CBE1_FORGOT_GOBACK; ?></a></p>
	<?php }else{ ?> 
		<p align="center"><?php echo CBE1_FORGOT_TEXT; ?></p>
	<?php } ?>

	<?php if (!(isset($_GET['msg']) && $_GET['msg'] == 4)) { ?>
      <form action="" method="post">
        <table bgcolor="#F7F7F7" style="border: 1px solid #EEEEEE" width="100%" align="center" cellpadding="3" cellspacing="0" border="0">
          <tr height="50">
            <td width="40%" align="right" valign="middle" nowrap="nowrap"><b><?php echo CBE1_FORGOT_EMAIL; ?>:</b></td>
            <td width="20%" align="left" valign="middle" nowrap="nowrap"><input type="text" class="textbox" name="email" size="30" required="required" value="" /></td>
			<td width="40%" align="left" valign="middle" nowrap="nowrap">
		  		<input type="hidden" name="action" value="forgot" />
				<input type="submit" class="submit" name="send" id="send" value="<?php echo CBE1_FORGOT_BUTTON; ?>" />
			</td>
          </tr>
        </table>
      </form>
	<?php } ?>

<?php require_once ("inc/footer.inc.php"); ?>