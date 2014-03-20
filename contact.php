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

	$content = GetContent('contact');


	if (isset($_POST['action']) && $_POST['action'] == 'contact')
	{
		unset($errs);
		$errs = array();

		$fname			= getPostParameter('fname');
		$email			= getPostParameter('email');
		$email_subject	= trim(getPostParameter('email_subject'));
		$umessage		= nl2br(getPostParameter('umessage'));

		if (!($fname && $email && $email_subject && $umessage))
		{
			$errs[] = CBE1_CONTACT_ERR1;
		}
		else
		{
			if (isset($email) && $email !="" && !preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $email))
			{
				$errs[] = CBE1_CONTACT_ERR2;
			}
		}


		if (count($errs) == 0)
		{
				////////////////////////////////  Send Message  //////////////////////////////
				$message = "<html>
							<head>
								<title>".SITE_TITLE."</title>
							</head>
							<body>
							<table width='80%' border='0' cellpadding='10'>
							<tr>
								<td>";
				$message .= "<p style='font-family: Verdana, Arial, Helvetica, sans-serif; font-size:11px;'>";
				$message .= $umessage;
				$message .= "</p>
								</td>
							</tr>
							</table>
							</body>
						</html>";

				$to_email = SITE_MAIL;
			
				$headers  = 'MIME-Version: 1.0' . "\r\n";
				$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
				$headers .= 'From: '.$fname.' <'.$email.'>' . "\r\n";

				if (@mail($to_email, $email_subject, $message, $headers))
				{
					header ("Location: contact.php?msg=1");
					exit();
				}
				////////////////////////////////////////////////////////////////////////////////
		}
		else
		{
			$allerrors = "";
			foreach ($errs as $errorname)
				$allerrors .= $errorname."<br/>\n";
		}
	}

	///////////////  Page config  ///////////////
	$PAGE_TITLE = $content['title'];

	require_once ("inc/header.inc.php");
	
?>

	<h1><?php echo $content['title']; ?></h1>
	<p><?php echo $content['text']; ?></p>


	<?php if (isset($_GET['msg']) && $_GET['msg'] == 1) { ?>
		<div class="success_msg"><?php echo CBE1_CONTACT_SENT; ?></div>
	<?php }?>

	<?php if (isset($allerrors) && $allerrors != "") { ?>
		<div class="error_msg"><?php echo $allerrors; ?></div>
	<?php } ?>


	<h3><?php echo CBE1_CONTACT_TITLE; ?></h3>

	  <form action="" method="post">
	  <table border="0" cellspacing="0" cellpadding="3">
		<tr>
		  <td nowrap="nowrap" valign="middle" align="right"><?php echo CBE1_CONTACT_NAME; ?>:</td>
		  <td align="left" valign="top"><input name="fname" class="textbox" type="text" value="<?php echo getPostParameter('fname'); ?>" required="required"  size="30" /></td>
		</tr>
		<tr>
		  <td valign="middle" align="right"><?php echo CBE1_CONTACT_EMAIL; ?>:</td>
		  <td align="left" valign="top"><input name="email" class="textbox" type="text" value="<?php echo getPostParameter('email'); ?>" required="required"  size="30" /></td>
		</tr>
			<tr>
		  <td valign="middle" align="right"><?php echo CBE1_CONTACT_SUBJECT; ?>:</td>
		  <td align="left" valign="top"><input name="email_subject" class="textbox" type="text" value="<?php echo getPostParameter('email_subject'); ?>" required="required"  size="30" /></td>
		  </td>
		</tr>
		<tr>
		  <td valign="top" align="right"><?php echo CBE1_CONTACT_MESSAGE; ?>:</td>
		  <td align="left" valign="top"><textarea cols="50" rows="6" class="textbox2" required="required" name="umessage"><?php echo getPostParameter('umessage'); ?></textarea></td>
		</tr>
		<tr>
		  <td valign="top">&nbsp;</td>
		  <td align="left" valign="middle">
			<input type="hidden" name="action" id="action" value="contact" />
			<input type="submit" class="submit" name="Submit" value="<?php echo CBE1_CONTACT_BUTTON; ?>" />
		  </td>
		</tr>
	  </table>
	  </form>
	
<?php require_once ("inc/footer.inc.php"); ?>